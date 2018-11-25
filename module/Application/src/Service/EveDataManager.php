<?php

namespace Application\Service;

use Application\Entity\Trntranslations;
use Application\Entity\Invtypes;
use Application\Entity\Invgroups;
use Application\Entity\Invmarketgroups;
use Application\Entity\Invcategories;
use Application\Entity\EveAlly;
use Application\Entity\EveCorporation;
use VposMoon\Entity\AtStructure;
use User\Entity\User;
use Application\Entity\Mapdenormalize;
use Application\Entity\Maplocationwormholeclasses;
use Application\Entity\Mapsolarsystemjumps;

/**
 * Manager service class to fetch EVE data from your database or to fill your database with EVE related data
 * usually provided by ESI, the EVE swagger API
 */
class EveDataManager
{
    /**
     * Entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     *
     * @var \Application\Service\EveEsiManager
     */
    private $eveESIManager;

    /**
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $eveESIManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveESIManager = $eveESIManager;
        $this->logger = $logger;
    }

    /**
     * Fetch system or constellation by a the first letters of the name
     *
     * Common usage is typeahead select field to select a location.
     *
     * Values for classid:
     *    1 - 6 are for w-space, with 1 being easy / unrewarding and 6 being hard / lucrative.
     *    7 is highsec,
     *    8 is lowsec, and
     *    9 is nullsec.
     *
     * @param  string $partial
     * @param  int    $limit
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR
     */
    public function getSystemByPartial($partial, $limit = 10)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('md.itemid as id, md.itemid, md.itemname')
            ->addSelect('mdc.itemname as constellation, mdc.itemid as constellationid')
            ->addSelect('mdr.itemname as region, mdr.itemid as regionid')
            ->addSelect('mwc1.wormholeclassid as classidH')
            ->addSelect('mwc2.wormholeclassid as classidL')
            ->from(Mapdenormalize::class, 'md')
            ->leftJoin(Mapdenormalize::class, 'mdc', 'WITH', 'mdc.itemid = md.constellationid')
            ->leftJoin(Mapdenormalize::class, 'mdr', 'WITH', 'mdr.itemid = md.regionid')
            ->leftJoin(Maplocationwormholeclasses::class, 'mwc1', 'WITH', 'mwc1.locationid = md.itemid')
            ->leftJoin(Maplocationwormholeclasses::class, 'mwc2', 'WITH', 'mwc2.locationid = md.regionid')
            ->where('md.groupid IN (4,5)')
            ->andWhere('md.itemname like :q')
            ->setParameter('q', $partial . '%')
            ->orderBy('md.itemname', 'ASC')
            ->setMaxResults($limit);

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }

    /**
     * Get neighbour solar systems by ID of a solar system
     *
     * @param  int id
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getNeighboursByID($id)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ms.tosolarsystemid as mds_id, ms.toconstellationid as mdc_id, mds.itemname as mds_name, mdc.itemname as mdc_name')
            ->from(Mapsolarsystemjumps::class, 'ms')
            ->from(Mapdenormalize::class, 'mds')
            ->from(Mapdenormalize::class, 'mdc')
            ->where('mds.itemid = ms.tosolarsystemid')
            ->andWhere('mdc.itemid = ms.toconstellationid')
            ->andWhere('(ms.fromsolarsystemid = :q OR ms.fromconstellationid = :q)')
            ->setParameter('q', $id)
            ->groupBy('ms.tosolarsystemid, ms.toconstellationid');

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }

    /**
     * Fetch a solarsystem or a constellation by his ID
     *
     * @param  int systemid
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getSystemByID($systemid)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('md.itemid as id, md.itemid, md.itemname, mdc.itemname as constellation, mdc.itemid as constellationid, mdr.itemname as region, mdr.itemid as regionid')
            ->from(Mapdenormalize::class, 'md')
            ->leftJoin(Mapdenormalize::class, 'mdc', 'WITH', 'mdc.itemid = md.constellationid')
            ->leftJoin(Mapdenormalize::class, 'mdr', 'WITH', 'mdr.itemid = md.regionid')
            ->where('md.groupid IN (4,5)')
            ->andWhere('md.itemid = :q')
            ->setParameter('q', $systemid)
            ->orderBy('md.itemname', 'ASC')
            ->setMaxResults(1);

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }

    /**
     * Fetch a list of Eve Items by their groupID.
     *
     * @param  array $ids group ids
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getTypeByGroupIDs($ids)
    {
        if (!is_array($ids)) {
            throw new Exception('Parameter for getTypeByGroupIDs() must be an array!');
        }

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('it.typeid, it.typename')
            ->from(Invtypes::class, 'it')
            ->add('where', $queryBuilder->expr()->in('it.groupid', $ids))
            ->andWhere('it.published = 1')
            ->orderBy('it.typename', 'ASC');

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }


    /**
     * Find a EVE item by his name in all supported languages.
     * Delivers the english name and it IDs for item, group and category
     *
     * @param  string $name
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getItemByLocalizedName($name)
    {
        //$this->logger->debug('getItemByLocalizedName __' . $name . '__');
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('t.typename, t.typeid, g.groupid, c.categoryid')
            ->from(Invtypes::class, 't')
            ->leftJoin(Trntranslations::class, 'trn', 'WITH', 'trn.tcid = 8 AND trn.keyid = t.typeid')
            ->leftJoin(Invgroups::class, 'g', 'WITH', 'g.groupid = t.groupid')
            ->leftJoin(Invcategories::class, 'c', 'WITH', 'c.categoryid = g.categoryid')
            ->where('trn.text = :word')
            ->setParameter('word', $name)
            ->setMaxResults(1);

        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

        if (!count($res)) {
            return(false);
        }

        return($res[0]);
    }


    /**
     * Get a EveItemGroup by the Item name in any language
     *
     * @param  string $name
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getGroupByLocalizedName($name)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('g')
            ->from(Invgroups::class, 'g')
            ->leftJoin(Trntranslations::class, 'trn', 'WITH', 'trn.tcid = 7 AND trn.keyid = g.groupid')
            ->where('trn.text = :word')
            ->setParameter('word', $name)
            ->setMaxResults(1);

        return($queryBuilder->getQuery()->getOneOrNullResult());
    }

    /**
     * Fetch a list of corporations where corporation name or ticker begins with $partial
     *
     * @param  string $partial
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getCorporationByPartial($partial, $limit = 10)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('c.corporationId as id, c.corporationId, c.corporationName, c.ticker, a.allianceName')
            ->from(EveCorporation::class, 'c')
            ->leftJoin(EveAlly::class, 'a', 'WITH', 'c.allianceId = a.allianceId')
            ->where('c.corporationName LIKE :word')
            ->orWhere('c.ticker LIKE :word')
            ->setParameter('word', '%'.addcslashes($partial, '%_').'%')
            ->setMaxResults($limit);

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }

    /**
     * Fetch a structure with details by structure-id
     *
     * @param  int $structure_id
     * @return \Doctrine\ORM\Query::HYDRATE_SCALAR result
     */
    public function getStructureById($structure_id = 0)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ats.structureName, ats.corporationId, ec.corporationName, ec.ticker corporationTicker, ec.allianceId, ea.allianceName, ea.ticker allianceTicker')
            ->addSelect('it.typename structItemname, it.typeid, ig.groupname, crea.eveUsername as creaName, ats.createDate, ats.lastseenDate, chgn.eveUsername lastseenName')
            ->from(AtStructure::class, 'ats')
            ->leftJoin(EveCorporation::class, 'ec', 'WITH', 'ats.corporationId = ec.corporationId')
            ->leftJoin(EveAlly::class, 'ea', 'WITH', 'ec.allianceId = ea.allianceId')
            ->leftJoin(invTypes::class, 'it', 'WITH', 'ats.typeId = it.typeid')
            ->leftJoin(invGroups::class, 'ig', 'WITH', 'it.groupid = ig.groupid')
            ->leftJoin(User::class, 'crea', 'WITH', 'ats.createdBy = crea.eveUserid')
            ->leftJoin(User::class, 'chgn', 'WITH', 'ats.lastseenBy = chgn.eveUserid')
            ->where('ats.id = :q')
            ->setParameter('q', $structure_id)
            ->setMaxResults(1);

        return($queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR));
    }




    /**
     * Fetch inventory prices from EVE-ESI.
     *
     * Update the Invtypes.baseprice with the prices we got from EVE.
     *
     * @return int updated prices count
     */
    public function updatePrices()
    {
        $this->logger->debug('updatePrices');
        $price_arr = [];

        // get his corporation details from ESI
        $prices = $this->eveESIManager->publicRequest('get', '/markets/prices/', []);

        $this->logger->debug('updatePrices :: prices cnt: ' . count($prices));
        // better than get_object_vars() cause working in all cases;
        $prices_data = (array) $prices;
        // $this->logger->debug('prices_data cnt: ' . count($prices_data));
        // transform prices into a associative array with the typeID as key
        foreach ($prices_data as $price) {
            $price_arr[$price->type_id] = (!empty($price->average_price) ? $price->average_price : (!empty($price->adjusted_price) ? $price->adjusted_price : 0.0));
        }
        // $this->logger->debug('price_arr cnt: ' . count($price_arr));
        // no prices, no need to persist
        if (!count($price_arr)) {
            return 0;
        }

        // update field "Baseprice" in table "Invtypes"
        $batchSize = 20;
        $i = 0;
        $j = 0;
        $q = $this->entityManager->createQuery('select t from Application\Entity\Invtypes t');
        $iterableResult = $q->iterate();
        foreach ($iterableResult as $row) {
            $types = $row[0];
            $tid = $types->getTypeid();
            //$this->logger->debug('check tid:' . $tid);
            if (!empty($price_arr[$tid])) {
                //$this->logger->debug('write ' . $tid . '  with price: ' . $price_arr[$tid] . '  DB baseprice: ' . $types->getBaseprice());
                $types->setBaseprice($price_arr[$tid]);
                $this->entityManager->persist($types);
                $j++;
            }

            if (($i % $batchSize) === 0) {
                //$this->logger->debug('persist at: ' . $i);
                $this->entityManager->flush(); // Executes all updates.
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
            }
            ++$i;
        }
        $this->entityManager->flush();

        $this->logger->info($j . ' Prices were updated via ESI');

        return($j);
    }


    /**
     * Fetch Alliance and Corporation Information from ESI.
     *
     * To be called from the shell
     *
     * @param  bool $force
     * @param  char $mode  a for alliance, c for corporation or b for both
     * @return string    Information about the actions taken
     */
    public function updateAllyCorp($force = false, $mode = false)
    {
        $ally_cnt = 0;
        $corp_cnt = 0;

        if ($force) {
            echo PHP_EOL . 'Force mode enabled, fetch all' . PHP_EOL;
        }

        if ($mode && ($mode == 'a' || $mode == 'b')) {
            echo PHP_EOL . 'Fetch Alliances' . PHP_EOL;
            $ally_cnt = $this->updateAllianceESI($force);
        }

        if ($mode && ($mode == 'c' || $mode == 'b')) {
            echo PHP_EOL . 'Fetch corporations' . PHP_EOL;
            $corp_cnt = $this->updateCorporationESI($force);
        }

        return ($ally_cnt . ' alliances and ' . $corp_cnt . ' Corporations were updated');
    }

    /**
     * Fetch all alliances and their name and ticker from ESI.
     *
     * Name and ticker are writen if the allianceID is not found in the local database.
     *
     * Sequence of calls:
     *
     *  # List all active player alliances
     *  https://esi.evetech.net/latest/alliances/?datasource=tranquility
     *
     * # Public information about an alliance
     * https://esi.evetech.net/latest/alliances/1354830081/?datasource=tranquility
     *
     * @param  type $force_update Force an update regardless if the allianceID is present in the database
     * @return int
     */
    private function updateAllianceESI($force_update = false)
    {

        // get a list of all alliances from ESI
        $ally_list = $this->eveESIManager->publicRequest('get', '/alliances', []);

        $ally_list_arr = (array) $ally_list;
        if (!count($ally_list_arr)) {
            return 0;
        }

        //$this->logger->debug('ally_list: ' . print_r($ally_list,true));
        //$this->logger->debug('ally_list_arr: ' . print_r($ally_list_arr,true));

        $batch_size = 50;
        $i = 1;
        foreach ($ally_list_arr as $row) {
            $alliance_entity = $this->entityManager->getRepository(EveAlly::class)->findOneByAllianceId((int) $row);

            if (!$alliance_entity) {
                $alliance_entity = new EveAlly;
            } elseif ($alliance_entity->getAllianceName() && !$force_update) {
                // if alliance has a name and it's not Force-Mode skip this alliance (presuming it's already up to date)
                echo('S');
                continue;
            }


            // fetch alliance details from ESI
            $ally_detail = $this->eveESIManager->publicRequest('get', '/alliances/{alliance_id}/', ['alliance_id' => $row]);
            $this->logger->debug('ally_detail: ' . print_r($ally_detail, true));

            if (isset($ally_detail->name)) {
                $alliance_entity->setAllianceName($ally_detail->name);
            }
            if (isset($ally_detail->ticker)) {
                $alliance_entity->setTicker($ally_detail->ticker);
            }

            $alliance_entity->setAllianceId($row);
            $this->entityManager->persist($alliance_entity);

            if (($i % $batch_size) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
                echo '-W' . PHP_EOL;
            }

            if (($i % 5) === 0) {
                if (($i % 10) === 0) {
                    echo $i;
                } else {
                    echo ':';
                }
            } else {
                echo '.';
            }
            ++$i;
        }

        $this->entityManager->flush(); //Persist objects that did not make up an entire batch
        $this->entityManager->clear();
        echo '#W' . PHP_EOL;

        return($i);
    }

    /**
     * Fetch all Corporations and their name and ticker from ESI.
     *
     * Name and ticker are writen if the corporationID is not found in the local database or
     * if the alliance assignment has been changed.
     *
     * Sequence of calls:
     *
     * # List all current member corporations of an alliance
     * https://esi.evetech.net/latest/alliances/1354830081/corporations/?datasource=tranquility
     *
     * # Public information about a corporation
     * https://esi.evetech.net/latest/corporations/98002591/?datasource=tranquility
     *
     * @param  type $force_update Force an update regardless if the allianceID is present in the database
     * @return int
     */
    private function updateCorporationESI($force_update = false)
    {
        $batch_size = 50;
        $i = 1;

        if ($force_update) {
            $q = $this->entityManager->createQuery('SELECT a FROM Application\Entity\EveAlly a');
        } else {
            // fetch only empty alliances (without corporations)
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $q = $queryBuilder->select('a')
                ->from(EveAlly::class, 'a')
                ->leftJoin(EveCorporation::class, 'c', 'WITH', 'c.allianceId = a.allianceId')
                ->where('c.corporationId IS NULL')
            //->setMaxResults(8)
                ->getQuery();
        }

        $iterableAllyResult = $q->iterate();
        // iterate through all alliances in DB
        foreach ($iterableAllyResult as $row) {
            // do stuff with the data in the row, $row[0] is always the object
            $alliance_id = $row[0]->getAllianceId();

            // get a list of all alliances from ESI
            $ally_members_list = $this->eveESIManager->publicRequest('get', '/alliances/{alliance_id}/corporations/', ['alliance_id' => $alliance_id]);

            $ally_members_arr = (array) $ally_members_list;
            if (!count($ally_members_arr)) {
                continue;
            }

            echo $row[0]->getAllianceName() . ' ';

            $i = 1;
            // iterate through all corporation ESI give us for the current alliance
            foreach ($ally_members_arr as $corporation_id) {
                $this->updateCorporation($corporation_id);

                if (($i % $batch_size) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear(); // Detaches all objects from Doctrine!
                    echo '-W';
                }

                if (($i % 5) === 0) {
                    if (($i % 10) === 0) {
                         echo $i;
                    } else {
                         echo ':';
                    }
                } else {
                    echo '.';
                }
                ++$i;
            }
            $this->entityManager->flush(); //Persist objects that did not make up an entire batch
            $this->entityManager->clear();
            echo 'W#';

            // detach from Doctrine, so that it can be Garbage-Collected immediately
            $this->entityManager->detach($row[0]);

            echo ' (' . $i . ')' . PHP_EOL;
        }

        return($i);
    }

    /**
     * Fetch corporation details from ESI and insert or update them into the database.
     *
     * Attention: This method is build for batch processing. To finally store the data to
     * the database you have to coll the EntityManager with flush().
     *
     * @param  int  $corporation_id
     * @param  int  $alliance_id
     * @param  bool $force_update
     * @return bool
     */
    private function updateCorporation($corporation_id)
    {
        $corporation_detail = $this->eveESIManager->publicRequest('get', '/corporations/{corporation_id}/', ['corporation_id' => $corporation_id]);

        // fetch corporation
        $corporation_entity = $this->entityManager->getRepository(EveCorporation::class)->findOneByCorporationId((int) $corporation_id);

        if (!$corporation_entity) {
            $corporation_entity = new EveCorporation();
            $corporation_entity->setCorporationId($corporation_id);
        }

        if (isset($corporation_detail->name)) {
            $corporation_entity->setCorporationName($corporation_detail->name);
        }

        if (isset($corporation_detail->ticker)) {
            $corporation_entity->setTicker($corporation_detail->ticker);
        }

        if (isset($corporation_detail->alliance_id)) {
            $corporation_entity->setAllianceId($corporation_detail->alliance_id);
        }


        $this->entityManager->persist($corporation_entity);

        return(true);
    }

    public function ping($param = '')
    {
        return('EveDataManager-ping ' . $param);
    }
}
