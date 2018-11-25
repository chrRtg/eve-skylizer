<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtCosmicDetail;
use VposMoon\Entity\AtStructure;

/**
 * Description of CosmicManager
 *
 * "cosmic" is something some may scan with a scan or dscan and any kind of anomaly
 *
 * @author chr
 */
class CosmicManager
{
    /*
     * EVE constants
     */

    const EVE_CATEGORY_STRUCTURE = 23;
    const EVE_CATEGORY_SHIP = 6;
    // fixed position elements we may use as a reference point
    const EVE_GROUP_SUN = 6;
    const EVE_GROUP_PLANET = 7;
    const EVE_GROUP_MOON = 8;
    const EVE_GROUP_ASTEROIDBELT = 9;
    const EVE_GROUP_STARGATE = 10;
    const EVE_GROUP_STATION = 15;
    // structures we may store individual
    const EVE_GROUP_CONTROLTOWER = 365;
    const EVE_GROUP_CITADEL = 1657;
    const EVE_GROUP_ENGINEERING_COMPLEX = 1404;
    const EVE_GROUP_COSMICANOMALY = 885;
    const EVE_GROUP_COSMICSIGNATURE = 502;
    const EVE_GROUP_FORCEFIELD = 411;
    const EVE_GROUP_WORMHOLE = 988;
    const EVE_TYPE_UWORMHOLE = 26272;

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
    // pattern to analyze and break various inputs
    private $cosmic_scan_regexp = '/^([A-Z]{3}-[0-9]{3})\t(.*)\t(.*)\t(.*)\t([0-9\,]+.?\%)\t(.*)/';
    private $cosmic_dscan_regexp = '/^(\S*)\t([\S ]*)\t([\S ]*)\t(-|[0-9\.\,]+ [AEUkm]+)/';

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $eveSSOManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveSSOManager = $eveSSOManager;
        $this->eveDataManager = $eveDataManager;
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
        // analyze the scan, improve and enhance the data
        $this->parseScan();

        $structure_plusminus = $this->preparePlusMinusList();

        //$this->logger->debug('### DATA Collector: '.print_r($this->data_collector, true));
        //$this->logger->debug('### Structure Collector: '.print_r($this->structure_collector, true));
        //$this->logger->debug('### Structure plusminus: '.print_r($structure_plusminus, true));
        //$this->logger->debug('### Elems Collector: '.print_r($this->structure_elem_collector, true));
        //$this->logger->debug('### Celestials Collector: '.print_r($this->celestial_collector, true));

        // clean anomalies no longer in the solarsystem
        $this->cleanSolarsystemAnomaliesByPlusminus($structure_plusminus);

        // persist scan
        if ($this->structure_collector && count($this->structure_collector)) {
            foreach ($this->structure_collector as $key => $value) {
                // @todo at the moment we support only SCAN cause we can detect a previous scan easyly by his signature
                if ($value['scantype'] == 'SCAN') {
                    $res = $this->writeStructure($value);
                }
            }
        }

        return ($structure_plusminus);
    }

    /**
     * Compare all Scans by their signature against the signatures in the user
     * current system.
     *
     * Create a list to identify new signatures, signatures already existing and
     * signatures which exist but not in the user input.
     *
     * @return array result array
     */
    private function preparePlusMinusList()
    {
        if (!$this->structure_collector || count($this->structure_collector) == 0) {
            return false; // nothing to do
        }

        $current_solarsystem = $this->eveSSOManager->getUserLocationAsSystemID();
        if (!$current_solarsystem) {
            $this->logger->info('User without location, can not create cleanup structure without a proper location');
            return false;
        }

        $res = array();

        $siglist = $this->getlocalSignatures($current_solarsystem);
        //$this->logger->debug('### structureCleanup -  list of signatures: '.print_r($siglist, true));
        // iterate through the new scan.
        foreach ($this->structure_collector as $key => $value) {
            $sigmatch = array_search($value['signature'], array_column($siglist, 'signature'));
            if ($sigmatch !== false) {
                // match in Siglist, mark entry as "remain"
                $siglist[$sigmatch]['remain'] = true;
            } else {
                $res['new'][$value['signature']] = 1;
            }
        }
        $res['list'] = $siglist;

        return ($res);
    }

    /**
     * Remove anomalies no longer in the solarsystem.
     *
     * Datasource is the list created by @see preparePlusMinusList()
     *
     * @param array $plusminus_list
     */
    private function cleanSolarsystemAnomaliesByPlusminus($plusminus_list)
    {
        if (!$this->countScantypes('SCAN')) {
            return false; // if zero, no signatures in the scan
        }

        if (empty($plusminus_list)) {
            return false; // input not an array
        }

        if (!array_key_exists('list', $plusminus_list)) {
            return false; // nothing to do
        }

        foreach ($plusminus_list['list'] as $key => $value) {
            if (!isset($value['remain']) && $this->isAnomaly($value['groupId'])) {
                //$this->logger->debug('delete ID: ' . print_r($value,true));
                $this->removeStructure($value['id']);
            }
        }
    }

    /**
     *
     * @todo improve documentation
     * Distribute the scanned data into
     */
    public function parseScan()
    {
        // @todo split method, it's to complex

        $current_solarsystem = $this->eveSSOManager->getUserLocationAsSystemID();
        if (!$current_solarsystem) {
            $this->logger->info('User without location, can not create at_structure entries without location');
            return false;
        }

        foreach ($this->data_collector as $key => $value) {
            //$this->logger->debug('CosmiccManager->parseScan() :: Prepare Scan, entry data: '.print_r($value, true));

            $this->data_collector[$key]['solarsystem_id'] = $current_solarsystem;

            // if signature, check if signature in this solar system is already existing.
            // in this case determine if existing or new scan has the better quality.
            if ($value['signature']) {
                $atstructure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneBy(array('solarSystemId' => $current_solarsystem, 'signature' => $value['signature']));
                if ($atstructure_entity) {
                    // if quality of existing scan is higher than the current scan, than skip the scan.
                    // if value is identical take the current scan because he may contain some new data
                    if ($atstructure_entity->getScanQuality()
                        && (int) $value['quality']
                        && (int) $value['quality'] < $atstructure_entity->getScanQuality()
                    ) {
                        return false;
                    }

                    $this->data_collector[$key]['atstructure_id'] = $atstructure_entity->getId();
                }
            }

            // check typename it it's a cosmic_detail (scannable anomaly)
            if ($value['eve_typename']) {
                $cosmicdetail = $this->getCosmicDetailByName($value['eve_typename']);
                if ($cosmicdetail) {
                    $this->data_collector[$key]['at_cosmic_detail_id'] = $cosmicdetail->getCosmicDetailId();
                    $this->data_collector[$key]['eve_groupid'] = $cosmicdetail->getCosmicMain()->getGroupid()->getGroupid();
                } else {
                    // no cosmic, then look for an eve itemname
                    $invtype_entity = $this->entityManager->getRepository(\Application\Entity\Invtypes::class)->findOneByTypename($value['eve_typename']);
                    if ($invtype_entity) {
                        $this->data_collector[$key]['eve_typeid'] = $invtype_entity->getTypeid();
                        $this->data_collector[$key]['eve_groupid'] = $invtype_entity->getGroupid()->getGroupid();
                    }
                }
            }

            // if we have not got a groupID so far. let's see if the eve_categoryname resolves to some
            // @todo
            if (!$this->data_collector[$key]['eve_groupid'] && $this->data_collector[$key]['eve_categoryname']) {
                if ($group = $this->eveDataManager->getGroupByLocalizedName($this->data_collector[$key]['eve_categoryname'])) {
                    $this->data_collector[$key]['eve_groupid'] = $group->getGroupid();
                    $this->data_collector[$key]['structure_name'] = $this->data_collector[$key]['eve_categoryname'];
                }
            }

            // give the "structure_name" the best human readable name possible
            if (!$value['structure_name']) {
                if ($value['eve_typename']) {
                    $this->data_collector[$key]['structure_name'] = $value['eve_typename'];
                } elseif ($value['eve_groupname']) {
                    $this->data_collector[$key]['structure_name'] = $value['eve_groupname'];
                }
            }

            // distribute the items in the proper data collectors
            if ($this->data_collector[$key]['at_cosmic_detail_id']) {
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif ($this->isAnomaly($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif ($this->isStructure($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->structure_collector[] = $this->data_collector[$key];
            } elseif ($this->isCelestial($this->data_collector[$key]['eve_groupid'], $this->data_collector[$key]['eve_typeid'])) {
                $this->celestial_collector[] = $this->data_collector[$key];
            } else {
                // let's inspect the item a little bit deeper:
                $item = $this->eveDataManager->getItemByLocalizedName($this->data_collector[$key]['structure_name']);
                if ($item && $item['categoryid'] == self::EVE_CATEGORY_STRUCTURE) {
                    $this->structure_elem_collector[] = $this->data_collector[$key];
                } else {
                    // anything else we have to check individually if categoryID == 23 for structure modules
                    $this->logger->info('parse SCAN/DSCAN :: item not supported: >>' . $this->data_collector[$key]['structure_name'] . '<<');
                }
            }
        }
    }

    /**
     * Insert or Update data a AtStructure table entry.
     *
     * @param  array Structure-data array
     * @return type
     */
    public function writeStructure($structure_data)
    {
        //$this->logger->debug('### writeStructure :: ' . print_r($structure_data, true));

        $structure_entity = null;
        if ($structure_data['atstructure_id']) {
            $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_data['atstructure_id']);
        }

        // insert (or update)
        if ($structure_entity === null) {
            $structure_entity = new AtStructure();
            $structure_entity->setCreateDate(new \DateTime("now"));
            $structure_entity->setCreatedBy($this->eveSSOManager->getIdentityID());
        }

        if ($structure_data['scantype']) {
            $structure_entity->setScanType($structure_data['scantype']);
        }

        if ($structure_data['eve_typeid']) {
            $structure_entity->setTypeId($structure_data['eve_typeid']); // invTypes.typeID (the structure)
        }

        if ($structure_data['eve_groupid']) {
            $structure_entity->setGroupId($structure_data['eve_groupid']); // invTypes.typeID (the structure)
        }

        if ($structure_data['corporation_id']) {
            $structure_entity->setCorporationId((int) $structure_data['corporation_id']); // owner: EveCorporation.corporationId
        }

        if ($structure_data['celestial_id']) {
            $structure_entity->setCelestialId($structure_data['celestial_id']);
            // celestial id but no solarsystem? fill the gap
            if (!$structure_data['solarsystem_id']) {
                $mapdeormalize_entity = $this->entityManager->getRepository(\Application\Entity\Mapdenormalize::class)->findOneByItemid($structure_data['celestial_id']);
                if ($mapdeormalize_entity) {
                    $structure_data['solarsystem_id'] = $mapdeormalize_entity->getSolarSystemid();
                }
            }
        }

        if ($structure_data['celestial_distance']) {
            $structure_entity->setCelestialDistance($structure_data['celestial_distance']);
        }

        if ($structure_data['structure_name']) {
            $structure_entity->setStructureName($structure_data['structure_name']);
        }

        if ($structure_data['signature']) {
            $structure_entity->setSignature($structure_data['signature']);
        }

        if ($structure_data['quality']) {
            $structure_entity->setScanQuality($structure_data['quality']);
        }

        if ($structure_data['distance']) {
            //$this->logger->debug('### Distance TEST __' . $structure_data['distance'] . '__   calc__' . \VposMoon\Service\ScanManager::getEveDistanceKM($structure_data['distance']) . '__');
            $structure_entity->setCelestialDistance(\VposMoon\Service\ScanManager::getEveDistanceKM($structure_data['distance']));
        }

        if ($structure_data['at_cosmic_detail_id']) {
            $structure_entity->setAtCosmicDetailId($structure_data['at_cosmic_detail_id']);
        }

        if ($structure_data['solarsystem_id']) {
            $structure_entity->setSolarSystemId($structure_data['solarsystem_id']);
        }

        $structure_entity->setLastseenDate(new \DateTime("now"));
        $structure_entity->setLastseenBy($this->eveSSOManager->getIdentityID());

        $this->entityManager->persist($structure_entity);
        $this->entityManager->flush();

        // fetch result and hydrate to array
        $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_entity->getId());
        $hydrator = new \DoctrineModule\Stdlib\Hydrator\DoctrineObject($this->entityManager);
        $structure_array = $hydrator->extract($structure_entity);

        return ($structure_array);
    }

    /**
     * Removes a structure from the database
     *
     * @param int $structure_id
     */
    public function removeStructure($structure_id)
    {
        $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_id);

        if ($structure_entity) {
            $this->entityManager->remove($structure_entity);
            $this->entityManager->flush();
        }
    }

    /**
     * Write a target-system into a existing struture
     *
     * @param int $structure_id
     * @param int $target_id <null>
     * @return int structure id or false on error
     */
    public function writeTargetToStructure($structure_id, $target_id = null)
    {
        if (empty($structure_id)) {
            return false;
        }

        //$this->logger->debug('### writeTargetToStructure :: ' . $structure_id);

        $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_id);
        //$this->logger->debug('### writeTargetToStructure :: ' . print_r($structure_entity, true));

        if (!$structure_entity) {
            return false;
        }

        $structure_entity->setTargetSystemId($target_id);

        $this->entityManager->persist($structure_entity);
        $this->entityManager->flush();

        return $structure_id;
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

        $queryBuilder->select('at.id, at.signature, at.scanType, at.groupId')
            ->from(\VposMoon\Entity\AtStructure::class, 'at')
            ->andWhere('at.solarSystemId = :solarid AND at.scanType = :scantype');

        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
        return ($res);
    }

    /**
     *
     * @return type
     */
    public function getStructureArray()
    {
        return ([
            'scantype' => null,
            'atstructure_id' => null,
            'eve_typeid' => null,
            'eve_typename' => null, // not written to db, to be resolved to his ID
            'eve_groupid' => null,
            'eve_groupname' => null, // not written to db, to be resolved to his ID
            'eve_categoryname' => null,
            'celestial_id' => null,
            'celestial_distance' => null,
            'structure_name' => null,
            'signature' => null,
            'quality' => null,
            'distance' => null,
            'corporation_id' => null,
            'solarsystem_id' => null,
            'at_cosmic_detail_id' => null,
        ]);
    }

    /**
     * Check if scan is a SCAN
     *
     * If match the scan will be added as strucutre data into $this->data_collector
     *
     * @param  string $line
     * @return boolean true if match
     */
    public function isScan($line)
    {
        if (preg_match($this->cosmic_scan_regexp, $line, $match)) {
            $structure_data = $this->getStructureArray();

            $structure_data['scantype'] = 'SCAN';
            $structure_data['signature'] = $match[1];
            $structure_data['eve_categoryname'] = $match[2];
            $structure_data['eve_groupname'] = $match[3];
            $structure_data['eve_typename'] = $match[4];
            $structure_data['quality'] = $match[5];
            $structure_data['distance'] = $match[6];

            $this->data_collector[] = $structure_data;

            return true;
        }

        return (false);
    }

    /**
     * Check if scan is a DSCAN
     *
     * @param  string $line
     * @return boolean
     */
    public function isDscan($line)
    {
        if (preg_match($this->cosmic_dscan_regexp, $line, $match)) {
            $structure_data = $this->getStructureArray();

            $structure_data['scantype'] = 'DSCAN';
            $structure_data['eve_typeid'] = $match[1];
            $structure_data['eve_itemname'] = $match[2];
            $structure_data['eve_typename'] = $match[3];
            $structure_data['distance'] = $match[4];

            $this->data_collector[] = $structure_data;
            return true;
        }

        return (false);
    }

    /**
     *
     * @param  type $eve_groupID
     * @param  type $eve_typeID
     * @return type
     */
    public function isAnomaly($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_COSMICANOMALY
            || $eve_groupID == self::EVE_GROUP_COSMICSIGNATURE
            || $eve_groupID == self::EVE_GROUP_WORMHOLE
            || $eve_typeID == self::EVE_TYPE_UWORMHOLE
        ) {
            return (true);
        }
        return (false);
    }

    public function isStructure($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_CONTROLTOWER
            || $eve_groupID == self::EVE_GROUP_CITADEL
            || $eve_groupID == self::EVE_GROUP_ENGINEERING_COMPLEX
        ) {
            return (true);
        }
        return (false);
    }

    public function isCelestial($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_SUN
            || $eve_groupID == self::EVE_GROUP_PLANET
            || $eve_groupID == self::EVE_GROUP_MOON
            || $eve_groupID == self::EVE_GROUP_ASTEROIDBELT
            || $eve_groupID == self::EVE_GROUP_STARGATE
            || $eve_groupID == self::EVE_GROUP_STATION
        ) {
            return (true);
        }
        return (false);
    }

    public function ping($param = '')
    {
        return ('CosmicManager-ping ' . $param);
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
            foreach ($this->structure_collector as $key => $value) {
                if ($value['scantype'] == 'SCAN') {
                    $cnt++;
                }
            }
        }
        return $cnt;
    }
}
