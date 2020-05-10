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
    const EVE_GROUP_FORCEFIELD = 411;
    const EVE_GROUP_COSMICSIGNATURE = 502;
    const EVE_GROUP_COSMICANOMALY = 885;
    const EVE_GROUP_WORMHOLE = 988;
    const EVE_GROUP_ENGINEERING_COMPLEX = 1404;
    const EVE_GROUP_REFINERY = 1406;
    const EVE_GROUP_CITADEL = 1657;
    const EVE_TYPE_UWORMHOLE = 26272;
    // flex structures
    const EVE_GROUP_UPWELL_JUMP_GATE = 1408;
    const EVE_GROUP_UPWELL_CYNO_JAMMER = 2016;
    const EVE_GROUP_UPWELL_CYNO_BEACON = 2017;
    const EVE_TYPE_ANSIBLEX_JUMP_GATE = 35841;


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

    private $config;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $eveESIManager, $config, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveESIManager = $eveESIManager;
        $this->config = $config;
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
            ->setParameter('word', addcslashes($partial, '%_').'%')
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
     * get a celestial by his name
     *
     * @param string $eve_itemname
     * @param integer $solarsystem_id
     * @return object Mapdenormalize entity
     */
    public function getCelestialByName($eve_itemname, $solarsystem_id = 0)
    {
        if ($solarsystem_id) {
            $mapdenom_entity = $this->entityManager->getRepository(Mapdenormalize::class)->findOneBy(array('solarsystemid' => $solarsystem_id, 'itemname' => $eve_itemname));
        } else {
            $mapdenom_entity = $this->entityManager->getRepository(Mapdenormalize::class)->findOneBy(array('itemname' => $eve_itemname));
        }
        if ($mapdenom_entity) {
            return($mapdenom_entity);
        }
    }


    /**
     * Fetch inventory prices from EVE-ESI.
     *
     * Update the Invtypes.baseprice with the prices we got from EVE.
     *
     * @return int updated prices count
     */
    public function updatePricesFromEveAPI()
    {
        $this->logger->debug('updatePrices');
        $price_arr = [];

        // get his corporation details from ESI
        $prices = $this->eveESIManager->publicRequest('get', '/markets/prices/', []);

        $this->logger->debug('updatePrices :: prices cnt: ' . count($prices));
        // better than get_object_vars() cause working in all cases;
        $prices_data = (array) $prices;

        // transform prices into a associative array with the typeID as key
        foreach ($prices_data as $price) {
            $adjusted_price = (!empty($price->adjusted_price) ? $price->adjusted_price : 0.0);
            $price_arr[$price->type_id] = (!empty($price->average_price) ? $price->average_price : $adjusted_price);
        }

        $upd_cnt = $this->writeEveItemPrices($price_arr);

        $this->logger->info($upd_cnt . ' Prices were updated via ESI');

        return($upd_cnt);
    }

    /**
     * Fetch specific prices from market.fuzzwork.co.uk.
     *
     * Update the Invtypes.baseprice with the prices we got from market.fuzzwork.co.uk.
     *
     * @return int updated prices count
     */
    public function updatePricesFromFuzzwork()
    {
        // request string, contains all types we need for moon ore price calculation
        $request_string = 'aggregates/?region=10000002&types=34,35,36,37,38,39,40,11399,27029,48927,16633,16634,16635,16636,16637,16638,16639,16640,16641,16642,16643,16644,16646,16647,16648,16649,16650,16651,16652,16653,45490,45491,45492,45493,46280,46281,46282,46283,46284,46285,46286,46287,45494,45495,45496,45497,46288,46289,46290,46291,46292,46293,46294,46295,45498,45499,45500,45501,46296,46297,46298,46299,46300,46301,46302,46303,45502,45503,45504,45506,46304,46305,46306,46307,46308,46309,46310,46311,45510,45511,45512,45513,46312,46313,46314,46315,46316,46317,46318,46319';

        // Create a client with a base URI
        $client = new \GuzzleHttp\Client(['base_uri' => 'https://market.fuzzwork.co.uk/', 'verify' => false]);
        try {
            $response = $client->request('GET', $request_string);
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            $this->logger->debug('ERROR: GuzzleHTTP Exception on request: ' . print_r($e->getRequest(), true));
            if ($e->hasResponse()) {
                $this->logger->debug('ERROR: GuzzleHTTP Exception on request with response: ' . print_r($e->getResponse(), true));
            }
            return false;
        }

        $result = json_decode($response->getBody(), true);

        if (empty($result)) {
            return false;
        }

        $price_arr = [];
        foreach ($result as $k => $v) {
            if ($v['sell']['weightedAverage']) {
                $price_arr[$k] = floatval($v['sell']['weightedAverage']);
            } elseif ($v['buy']['weightedAverage']) {
                $price_arr[$k] = floatval($v['buy']['weightedAverage']);
            }
        }

        $upd_cnt = $this->writeEveItemPrices($price_arr);

        $this->logger->info($upd_cnt . ' Prices were updated via market.fuzzwork.co.uk');

        return($upd_cnt);
    }


    /**
     * Write prices into Invtypes table
     *
     * Takes a assocative array with the itemname as the key and the price as teh value.
     * Updates Invtypes.baseprice in a batch write
     *
     * @param array $price_arr
     * @return int  count of prices updated
     */
    private function writeEveItemPrices($price_arr)
    {
        // no prices, no need to persist
        if (!$price_arr || !is_array($price_arr) || !count($price_arr)) {
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
                echo's'; // skip entry
                continue;
            }

            // fetch alliance details from ESI
            $ally_detail = $this->eveESIManager->publicRequest('get', '/alliances/{alliance_id}/', ['alliance_id' => $row]);
            //$this->logger->debug('ally_detail: ' . print_r($ally_detail, true));

            if (isset($ally_detail->name)) {
                $alliance_entity->setAllianceName($ally_detail->name);
            }
            if (isset($ally_detail->ticker)) {
                $alliance_entity->setTicker($ally_detail->ticker);
            }

            $alliance_entity->setAllianceId($row);
            $this->entityManager->persist($alliance_entity);

            // we collected enough to batch write and afterwards to free ressources for the next batch
            if (($i % $batch_size) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine!
                echo '-W' . PHP_EOL;
            }

            // show progress
            $this->showProgress($i);
            ++$i;
        }

        $this->entityManager->flush(); //Persist objects that did not make up an entire batch
        $this->entityManager->clear();
        echo '#W' . PHP_EOL;

        return($i);
    }

    /**
     * Fetch all Corporations from ESI by the known Alliances
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
            // fetch only alliances without corporations yet
            $queryBuilder = $this->entityManager->createQueryBuilder();
            $q = $queryBuilder->select('a')
                ->from(EveAlly::class, 'a')
                ->leftJoin(EveCorporation::class, 'c', 'WITH', 'c.allianceId = a.allianceId')
                ->where('c.corporationId IS NULL')
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

                // show progress
                $this->showProgress($i);
                ++$i;
            }
            $this->entityManager->flush(); //Persist objects that did not make up an entire batch
            $this->entityManager->clear();
            echo 'W';

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
        $this->entityManager->flush();

        return(true);
    }

    /**
     * Search for corporation in ESI with a partial using the /search endpoint
     *
     * Any corp found will get added to the local database or updated if existing.accordion
     *
     * @param string search tearm
     */
    public function searchCorporationESI($query)
    {
        //$this->logger->debug('searchCorporationESI with term: ' . print_r($query, true));

        // search for all corporations who contain the term
        $corporation_list = $this->eveESIManager->search('corporation', $query);

        $corporation_list_arr = (array) $corporation_list;

        $num_res = count($corporation_list_arr['corporation']);
        if (!$num_res) {
            return 0;
        }

        // iterate through all corporation ESI gave us for this term
        foreach ($corporation_list_arr['corporation'] as $corporation) {
            $this->updateCorporation($corporation);
        }
        return($num_res);
    }


    /**
     * show progress in the shell
     *
     * @param int $i
     * @return void
     */
    private function showProgress($i)
    {
        if (($i % 5) === 0) {
            if (($i % 10) === 0) {
                 echo $i;
            } else {
                 echo ':';
            }
        } else {
            echo '.';
        }
    }

    public function ping($param = '')
    {
        return('EveDataManager-ping ' . $param);
    }
}
