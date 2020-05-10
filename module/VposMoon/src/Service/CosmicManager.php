<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtCosmicDetail;
use VposMoon\Entity\AtStructure;
use Application\Service\EveDataManager;

/**
 * Description of CosmicManager
 *
 * "cosmic" is something some may scan with a scan or dscan and any kind of anomaly
 *
 * @author chr
 */
class CosmicManager
{
    // up to which distance we create a relation between a structure and a celestial without comparing first and next possible celestial ?
    const MAX_SUPPORTED_DISTANCE = 9900000;
    // up to which distance we accept a relation between a refinery and a moon?
    const MAX_POSSIBLE_MOONDISTANCE = 10000;

    /**
     * Doctrine entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $logger;

    /**
     *
     * @var \Application\Service\EveDataManager
     */
    private $eveDataManager;

    /**
     *
     * @var \VposMoon\Service\StructureManager
     */
    private $structureManager;

    /**
     * To collect raw scan/dscan data
     *
     * @var array
     */
    private $data_collector;

    /**
     * To collect structure and anomalies
     *
     * @var array
     */
    private $celestial_collector;

    /**
     * To collect structure and anomalies
     *
     * @var array
     */
    private $structure_collector;

    /**
     * To collect elements which belong to a structure
     *
     * @var array
     */
    private $structure_elem_collector;

    /**
     *
     * @var \User\Service\EveSSOManager
     */
    private $eveSSOManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $eveSSOManager, $eveDataManager, $structureManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveSSOManager = $eveSSOManager;
        $this->eveDataManager = $eveDataManager;
        $this->structureManager = $structureManager;
        $this->logger = $logger;
    }

    /**
     * Prepare a scan of anomalies and structures then persist the entries.
     *
     * Distribute the scanned data into
     *
     * @return array list of new and added structures
     */
    public function processScan()
    {
        $res_arr = array(
            'del_anom' => 0,
            'scan_anom' => array()
        );
        // analyze the scan, improve and enhance the data
        $this->parseScan();

        // check current scan against all signatures in DB of type SCAN in current system
        $res_arr['del_anom'] = $this->evaluateAnomalyScan();

        $this->evaluateStructureScan();
        
        //$this->logger->debug('### DATA Collector: '.print_r($this->data_collector, true));
        //$this->logger->debug('### Structure Collector: '.print_r($this->structure_collector, true));
        //$this->logger->debug('### Elems Collector: '.print_r($this->structure_elem_collector, true));
        //$this->logger->debug('### Celestials Collector: '.print_r($this->celestial_collector, true));

        // persist scan
        if ($this->structure_collector && count($this->structure_collector)) {
            foreach ($this->structure_collector as $value) {
                if (!(isset($value['ignore']) && $value['ignore'] == 1 )) {
                    $writeres = $this->structureManager->writeStructure($value);
                    $res_arr['scan_anom'][$writeres['id']] = $writeres['mod'];
                }
            }
        }
        return ($res_arr);
    }


    /**
     * Evaluate the structues in the scan if the have to be inserted or updated
     *
     * @return void
     */
    private function evaluateStructureScan()
    {
        if (!$this->structure_collector || count($this->structure_collector) == 0) {
            return false; // nothing to do
        }

        foreach ($this->structure_collector as $key => $struct) {
            // skip non structure entries
            if ($struct['scantype'] != 'STRUCT') {
                continue;
            }

            //$this->logger->debug('### evaluateStructureScan: '.print_r($struct, true));

            $next_celestial = null;
            // get next celestial, moon for refineries, any for all others
            $next_celestial = $this->getNextCelestial($struct['distance']);

            // we use both later below
            $celestial_id = null;
            $celestial_distance = null;
            
            // add relation to next celestial as well their distance to each other
            if ($next_celestial['match']) {
                $celestial_id = (int) $next_celestial['match']['celestial_id'];
                if ($next_celestial['match']['eve_groupid'] == EveDataManager::EVE_GROUP_STARGATE) {
                    $this->structure_collector[$key]['target_system_id'] = $celestial_id;
                } else {
                    $this->structure_collector[$key]['celestial_id'] = $celestial_id;
                }

                $celestial_distance = (int) $next_celestial['dist'];

                // if structure distance is >= 0.1 AU, we assume a distance of 500.000
                // we don't use a distance of less than 1.000, i
                if ((int) $struct['distance'] > 14959787) {
                    $celestial_distance = 500000;
                } elseif (\VposMoon\Service\ScanManager::isRefinery($struct['eve_groupid']) && $celestial_distance < 5000) {
                    $celestial_distance = 5000;
                } elseif ($celestial_distance < 1000) {
                    $celestial_distance = 1000;
                }
                $this->structure_collector[$key]['celestial_distance'] = $celestial_distance;
            }

            // Ansiblex gate, link the destination like a WH
            if ($struct['eve_typeid'] == EveDataManager::EVE_TYPE_ANSIBLEX_JUMP_GATE) {
                $this->structure_collector[$key]['target_system_id'] = $this->getSystemIDFromEveItemName(explode(' » ', $struct['eve_itemname'])[1]);
            }

            // do we already have this structure in DB?
            $atstructure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneBy(array(
                'solarSystemId' => $struct['solarsystem_id'],
                'typeId' => $struct['eve_typeid'],
                'structureName' => $struct['structure_name']));

            if ($atstructure_entity) {
                // We have to determine if the new scan or the one in DB has better quality
                // we assume the less the distance between a structure and his celestial is, the better is the data
                $db_celestial_distance = $atstructure_entity->getCelestialDistance();
                if (isset($db_celestial_distance) && $db_celestial_distance < $celestial_distance) {
                    // we got a celestial distance from the structure in DB and her distance is less then the current scanned structure
                    $this->structure_collector[$key]['ignore'] = 1;
                } else {
                    $this->structure_collector[$key]['atstructure_id'] = $atstructure_entity->getId();
                }
            }
        }

        return false;
    }

    /**
     * Determine which anomaly goes to the database and which has to be deleted
     *
     * Compare all scanned anomalies by their signature against the signatures already in the database.
     * Remove from DB what's no longer in the scan. Remove from the scan what's in the DB with same or higher quality.
     *
     * @return int  ammount of entries removed from DB
     */
    private function evaluateAnomalyScan()
    {
        $del_cnt = 0;

        if (!$this->structure_collector || count($this->structure_collector) == 0) {
            return false; // nothing to do
        }

        $current_solarsystem = $this->eveSSOManager->getUserLocationAsSystemID();
        if (!$current_solarsystem) {
            $this->logger->info('User without location, can not create cleanup structure without a proper location');
            return false;
        }

        // do we have any SCAN in our results? If not skip anything below, in particular keep the scans in the current system
        if (!$this->countScantypes('SCAN')) {
            return ($del_cnt);
        }

        // get all stored signatures of type SCAN (anomalies) from the DB
        $siglist = $this->getlocalSignatures($current_solarsystem);

        // iterate through the signature in DB
        foreach ($siglist as $value) {
            // are all signatures in DB are also in the scan? If not remove the anomaly from DB
            $sigmatch = array_search($value['signature'], array_column($this->structure_collector, 'signature'));
            if ($sigmatch === false) {
                // structure in DB but not in scan
                $this->structureManager->removeStructure($value['id']);
                $del_cnt++;
            } else {
                $sigqual = (int) $this->structure_collector[$sigmatch]['quality'];
                if ($sigqual <= $value['scanQuality']) {
                    // remove entry from scan, the one in DB is of better quality
                    $this->structure_collector[$sigmatch]['ignore'] = 1;
                }
            }

            // set distance for all anomalies to NULL while the next-celestial detection is not active
            $this->structure_collector[$sigmatch]['celestial_distance'] = null;
        }

        return ($del_cnt);
    }


    /**
     * Parse a SCAN or DSCAN
     *
     * Split the scanned data into celestials, anomalies, structures, and
     * structure-elements (attachted to a tower).
     *
     * @return
     */
    public function parseScan()
    {
        // @consider split method, it's to complex

        $current_solarsystem = $this->eveSSOManager->getUserLocationAsSystemID();
        if (!$current_solarsystem) {
            $this->logger->info('User without location, can not create at_structure entries without location');
            return false;
        }

        foreach ($this->data_collector as $key => $scan_elem) {
            //$this->logger->debug('CosmiccManager->parseScan() :: Prepare Scan, entry data: '.print_r($scan_elem, true));
            // set current system as the default
            $this->data_collector[$key]['solarsystem_id'] = $current_solarsystem;
            if (isset($scan_elem['eve_itemname']) && $scan_elem['eve_itemname']) {
                // check if EveItemName is a Celestial to retrieve his solaryssystem from the name.
                $celestial = $this->eveDataManager->getCelestialByName($scan_elem['eve_itemname']);
                if ($celestial) {
                    // overwrite the default
                    $this->data_collector[$key]['solarsystem_id'] = $celestial->getSolarSystemid();
                    $this->data_collector[$key]['celestial_id'] = $celestial->getItemid();
                } else {
                    // fallback to support named structures
                    $sytem_id = $this->getSystemIDFromEveItemName($scan_elem['eve_itemname']);
                    if ($sytem_id) {
                        $this->data_collector[$key]['solarsystem_id'] = $sytem_id;
                    }
                }
            }


            // if signature, check if signature in this solar system is already existing.
            // in this case determine if existing or new scan has the better quality.
            if ($scan_elem['signature']) {
                $atstructure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneBy(array('solarSystemId' => $current_solarsystem, 'signature' => $scan_elem['signature']));
                if ($atstructure_entity) {
                    $this->data_collector[$key]['atstructure_id'] = $atstructure_entity->getId();
                }
            }


            // check typename it it's a cosmic_detail (scannable anomaly)
            if ($scan_elem['eve_typename']) {
                $cosmicdetail = $this->getCosmicDetailByName($scan_elem['eve_typename']);
                if ($cosmicdetail) {
                    $this->data_collector[$key]['at_cosmic_detail_id'] = $cosmicdetail->getCosmicDetailId();
                    $this->data_collector[$key]['eve_groupid'] = $cosmicdetail->getCosmicMain()->getGroupid()->getGroupid();
                } else {
                    // no cosmic, then look for an eve itemname
                    $invtype_entity = $this->entityManager->getRepository(\Application\Entity\Invtypes::class)->findOneByTypename($scan_elem['eve_typename']);
                    if ($invtype_entity) {
                        $this->data_collector[$key]['eve_typeid'] = $invtype_entity->getTypeid();
                        $this->data_collector[$key]['eve_groupid'] = $invtype_entity->getGroupid()->getGroupid();
                    }
                }
            }

            // if we have not got a groupID so far. let's see if the eve_categoryname resolves to some
            if (!$this->data_collector[$key]['eve_groupid'] && $this->data_collector[$key]['eve_categoryname']) {
                if ($group = $this->eveDataManager->getGroupByLocalizedName($this->data_collector[$key]['eve_categoryname'])) {
                    $this->data_collector[$key]['eve_groupid'] = $group->getGroupid();
                    $this->data_collector[$key]['structure_name'] = $this->data_collector[$key]['eve_categoryname'];
                }
            }

            // give the "structure_name" the best human readable name possible
            if (!$scan_elem['structure_name']) {
                if ($scan_elem['eve_typename']) {
                    $this->data_collector[$key]['structure_name'] = $scan_elem['eve_typename'];
                } elseif ($scan_elem['eve_groupname']) {
                    $this->data_collector[$key]['structure_name'] = $scan_elem['eve_groupname'];
                }
            }

            // distribute the items into the proper data collectors
            if ($this->data_collector[$key]['at_cosmic_detail_id']) {
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif (\VposMoon\Service\ScanManager::isAnomaly($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif (\VposMoon\Service\ScanManager::isStructure($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->data_collector[$key]['scantype'] = 'STRUCT';
                // for structures retrieve and add the user given names
                $this->data_collector[$key]['structure_name'] = $this->cleanEveItemName($scan_elem['eve_itemname']);
                // add scan element to structure collector
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif (\VposMoon\Service\ScanManager::isCelestial($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->celestial_collector[] = $this->data_collector[$key];
            } else {
                // let's inspect the item a little bit deeper:
                $item = $this->eveDataManager->getItemByLocalizedName($this->data_collector[$key]['structure_name']);
                if ($item && $item['categoryid'] == EveDataManager::EVE_CATEGORY_STRUCTURE) {
                    $this->structure_elem_collector[] = $this->data_collector[$key];
                } else {
                    // anything else we have to check individually if categoryID == 23 for structure modules
                    $this->logger->info('parse SCAN/DSCAN :: item not supported: >>' . $this->data_collector[$key]['structure_name'] . '<<');
                }
            }
        }
    }

    /**
     * Find a cosmic detail entry by his name
     * Method searches in all supported languages.
     *
     * @param  string $name
     * @return \Doctrine\ORM\Query::HYDRATE_OBJECT Response or false if not found
     */
    private function getCosmicDetailByName($name)
    {
        if (!$name) {
            return (false);
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('acd, acm')
            ->from(\VposMoon\Entity\AtCosmicDetail::class, 'acd')
        //->leftJoin(\VposMoon\Entity\AtCosmicMain::class, 'acm', 'WITH', 'acm.cosmicMainId = acd.cosmicMainId')
            ->join('acd.cosmicMain', 'acm')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('acd.typename', ':name'),
                    $queryBuilder->expr()->eq('acd.typenameDe', ':name')
                )
            )
            ->setParameter('name', $name);

        return ($queryBuilder->getQuery()->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_OBJECT));
    }

    /**
     * Get a list of all SCAN in a given solarsystem
     *
     * @param  type $solarsystem_id
     * @return object result set
     */
    private function getlocalSignatures($solarsystem_id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $parameter['solarid'] = $solarsystem_id;
        $parameter['scantype'] = 'SCAN';
        $queryBuilder->setParameters($parameter);

        $queryBuilder->select('at.id, at.signature, at.scanType, at.groupId, at.scanQuality')
            ->from(\VposMoon\Entity\AtStructure::class, 'at')
            ->andWhere('at.solarSystemId = :solarid AND at.scanType = :scantype');

        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
        return ($res);
    }

    /**
     * Returns the name without the system name
     *
     * A eve_itemname is written in the form of "<system name> - <item name>". This function
     * returns the <item name> if retrievabele, otherwise the function returns the input
     *
     * @param   string  item name
     *
     * @return string   clean name or item name as not stripable
     */
    public static function cleanEveItemName(string $itemname)
    {
        $re = '/.+? - ([\S -]+)/';

        if ($itemname) {
            preg_match($re, $itemname, $name_split_arr);
            if (!empty($name_split_arr[1])) {
                $itemname = $name_split_arr[1];
           }
        }

        return $itemname;
    }

    /**
     * Takes a EveItemName like "RLL-9R IX - Intaki Space Police Assembly Plant" and extracts the celestial name from it
     *
     * @param string name of celestial
     * @return string celestial ID or null
     */
    private function getSystemIDFromEveItemName(string $itemname)
    {
        if (empty($itemname)) {
            return null;
        }

        $systemname = null;

        // EveItemname of a celestial looks like "RLL-9R IX - Intaki Space Police Assembly Plant"
        // we split the input by " - ", the first part is the system name
        $re_regular = '/(.*?) (»|-) (.*)/';
        preg_match($re_regular, $itemname, $name_split_arr);
        if (!empty($name_split_arr[1])) {
            $systemname = $name_split_arr[1];
        }
        if (!$systemname) {
            return (null);
        }

        $celstial = $this->eveDataManager->getCelestialByName($systemname);

        if ($celstial) {
            return($celstial->getItemid());
        }
        return(null);
    }

    
    /**
     * Find the nearest celestial from the internal data collector to a given distance
     *
     * @param int distance
     * @return array 'dist' for relative distance and 'match' for the celestial itself
     */
    private function getNextCelestial($dist)
    {
        $res = array('match' => false, 'dist' => null);
        $celstial_dist_collector = array();

        if (!isset($this->celestial_collector) || !isset($dist) || $dist===null || $dist===false) {
            return ($res);
        }

        foreach ($this->celestial_collector as $ckey => $celestial) {
            $celstial_dist_collector[$ckey] = abs($celestial['distance'] - $dist);
        }

        // no match? go back home
        if (count($celstial_dist_collector) == 0) {
            return ($res);
        }

        asort($celstial_dist_collector);
        $celstial_dist_collector_keys = array_keys($celstial_dist_collector);
        $first = $celstial_dist_collector_keys[0];

        // only one match, then accept it
        if (count($celstial_dist_collector) == 1) {
            $res['match'] = $this->celestial_collector[$first];
            $res['dist'] = $celstial_dist_collector[$first];
            return($res);
        }

        $second = $celstial_dist_collector_keys[1];

        // first == second && dist > MAX_SUPPORTED_DISTANCE ? Then we have duplicate distances as AU values and can not determine a relation
        if ($celstial_dist_collector[$first] == $celstial_dist_collector[$second] && $dist > self::MAX_SUPPORTED_DISTANCE) {
            return ($res);
        }

        // nothing against to accept the match
        $res['match'] = $this->celestial_collector[$first];
        $res['dist'] = $celstial_dist_collector[$first];
        return($res);
    }


    /**
     * add a array to local data_collector
     *
     * @param array $entry
     */
    public function addToDataToCollector($entry)
    {
        $this->data_collector[] = $entry;
    }


    /**
     * Count the number of a give Scantype in $this->structure_collector
     *
     * @param string scan type [SCAN]
     *
     * @return int count
     */
    private function countScantypes($scantype = 'SCAN')
    {
        $cnt = 0;

        if ($this->structure_collector && count($this->structure_collector)) {
            foreach ($this->structure_collector as $value) {
                if ($value['scantype'] == $scantype) {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }
}
