<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtStructure;

/**
 * The StructuresManager writes and changes the data in AtStructure like anomalies, structures and such
 */
class StructureManager
{

    /**
     * Doctrine entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * UserManager
     *
     * @var \User\Service\UserManager
     */
    private $userManager;

    /**
     *
     * @var \Application\Service\EveEsiManager
     */
    private $eveESIManager;

    /**
     *
     * @var \User\Service\EveSSOManager
     */
    private $eveSSOManager;

    /**
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $userManager, $eveESIManager, $eveSSOManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->eveESIManager = $eveESIManager;
        $this->eveSSOManager = $eveSSOManager;
        $this->logger = $logger;
    }

    /**
     * Insert or Update data a AtStructure table entry.
     *
     * @param  array Structure-data array
     * @return array (structure_id, updated)
     */
    public function writeStructure($structure_data)
    {
        //$this->logger->debug('### writeStructure :: write:' . print_r($structure_data, true));

        $mode_update = 'u';
        $structure_entity = null;
        if ($structure_data['atstructure_id']) {
            $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_data['atstructure_id']);
        }

        // insert (or update)
        if ($structure_entity === null) {
            $structure_entity = new AtStructure();
            $structure_entity->setCreateDate(new \DateTime("now"));
            $structure_entity->setCreatedBy($this->eveSSOManager->getIdentityID());
            $mode_update = 'n';
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

        if ($structure_data['solarsystem_id']) {
            $structure_entity->setSolarSystemId($structure_data['solarsystem_id']);
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

        if ($structure_data['at_cosmic_detail_id']) {
            $structure_entity->setAtCosmicDetailId($structure_data['at_cosmic_detail_id']);
        }

        if ($structure_data['target_system_id']) {
            $structure_entity->setTargetSystemId($structure_data['target_system_id']);
        }

        if ($structure_data['structure_id']) {
            $structure_entity->setStructureId($structure_data['structure_id']);
        }

        if ($structure_data['fuel_expires']) {
            $structure_entity->setFuelExpires($structure_data['fuel_expires']);
        }

        if ($structure_data['reinforce_hour']) {
            $structure_entity->setReinforceHour($structure_data['reinforce_hour']);
        }

        if ($structure_data['reinforce_weekday']) {
            $structure_entity->setReinforceWeekday($structure_data['reinforce_weekday']);
        }

        if ($structure_data['structure_state']) {
            $structure_entity->setStructureState($structure_data['structure_state']);
        }

        if ($structure_data['chunk_arrival_time']) {
            $structure_entity->setChunkArrivalTime($structure_data['chunk_arrival_time']);
        }

        if ($structure_data['extraction_start_time']) {
            $structure_entity->setExtractionStartTime($structure_data['extraction_start_time']);
        }

        if ($structure_data['natural_decay_time']) {
            $structure_entity->setNaturalDecayTime($structure_data['natural_decay_time']);
        }


        $structure_entity->setLastseenDate(new \DateTime("now"));
        $structure_entity->setLastseenBy($this->eveSSOManager->getIdentityID());
 
        $this->entityManager->persist($structure_entity);
        $this->entityManager->flush();

        return (array('id' => $structure_entity->getId(), 'mod' => $mode_update));
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
     * Usually used to add a WH connection to a WH entry
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

        $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_id);

        if (!$structure_entity) {
            return false;
        }

        $structure_entity->setTargetSystemId($target_id);

        $this->entityManager->persist($structure_entity);
        $this->entityManager->flush();

        return $structure_id;
    }

    /**
     * Delete all anomalies older than 3 days
     *
     * @return int  amount of entries deleted
     */
    public function removeOutdatedAnomalies()
    {
        $date = new \DateTime("now");
        $date->modify('-3 day');
        $removedate = $date->format('Y-m-d H:i:s');

        // $this->logger->debug('### removedate: '. $removedate);

        $parameter['cmpdate'] = $removedate;
        $parameter['anogrouplist'] = array('885', '502', '988', '26272');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(\VposMoon\Entity\AtStructure::class, 'at')
            ->where('at.lastseenDate <= :cmpdate')
            ->setParameters($parameter)
            ->andWhere($qb->expr()->in('at.groupId', ':anogrouplist'));

        return $qb->getQuery()->execute();
    }

    /**
     * Fetch all information about the Corporations structures
     *
     * Takes care to refresh SSO token in user_cli if required
     * Blocks unblocks user_cli entries
     *
     * Call requires some elaborated ESI scopes
     *  - esi-corporations.read_structures.v1
     *  - esi-industry.read_corporation_mining.v1
     *  - esi-universe.read_structures.v1
     *
     * @param boolean $climode if run from shell / cli set to true
     * @return void
     */
    public function esiFetchCoprporationStructures($climode = false)
    {
        // any cli user already in progress?
        $running = $this->userManager->checkCliUserInUse();
        if ($running != 0) {
            if ($climode) {
                echo "STOP :: at least one UserCli entry still in use." . PHP_EOL;
            }
            $this->logger->debug('STOP :: StructureManager->esiFetchCoprporationStructures() :: still a UserCli entry in use!');
            return 0;
        }

        // get next cliUser and set in_use = 1
        $cli_user = $this->userManager->getNextCliUser();
        if (!$cli_user) {
            return 0;
        }
 
        // if SSO token in user-cli entry has expired renew it
        if ($this->userManager->checkCliUserTokenExpiry($cli_user)) {
            $ac = unserialize($cli_user->getAuthContainer());
            $new_token = $this->eveSSOManager->getFreshAccessToken($ac['refresh_token']);
            $this->userManager->updateSsoCliUser($cli_user->getEveUserid(), $new_token);
        }

        // ... and set in_use = 1
        $this->userManager->setCliUserInUse($cli_user);

        // Get all corporation structures
        $struct_arr = $this->esiGetAllCorpStructures($cli_user->getEveCorpid(), unserialize($cli_user->getAuthContainer()));
        if ($climode) {
            echo "fetched " . count($struct_arr) . " structures for corporation ".$cli_user->getEveCorpid()." " . PHP_EOL;
        }

        // enrich them with name, type and solar system
        $struct_arr = $this->esiGetCorpStructuresByStructures($struct_arr, unserialize($cli_user->getAuthContainer()));
        if ($climode) {
            echo "enriched them with detail information like name and type" . PHP_EOL;
        }

        // enrich them with their extractions if drilling plattforms
        $struct_arr = $this->esiGetCorpMinningExtractions($struct_arr, $cli_user->getEveCorpid(), unserialize($cli_user->getAuthContainer()));
        if ($climode) {
            echo "enriched them with some moon mining extractions" . PHP_EOL;
        }

        //$this->logger->debug('### esiFetchCoprporationStructures :: after #1 :: ' . print_r($struct_arr, true));
     
        // Write the result data object to DB
        $this->esiWriteStructure($struct_arr, $climode);

        // reset CliUser for next usage
        $this->userManager->unsetCliUserInUse($cli_user);
    }

    /**
     * Updates or inserts the structures which were fetched from ESI
     *
     * The method tries to find aach structure in the database by his structure_id.
     * If this fails it tries to find the structure by solarsystem, type and name.
     * If found the existing database entry gets updated.
     * If not found a new entry gets inserted.
     *
     * @param array $struct_arr
     * @param boolean $climode
     * @return void
     */
    private function esiWriteStructure($struct_arr, $climode = false)
    {
        foreach ($struct_arr as $v) {
            $structure_data = $this->getStructureArray();

            $v['name'] = \VposMoon\Service\CosmicManager::cleanEveItemName($v['name']);

            // do we already have this structure in DB?
            // structure found by his eve-structureID?
            $atstructure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneBy(array(
                'structureId' => $v['structure_id']));

            if (!$atstructure_entity) {
                // if not, structure to be found identified by solarsystem, type and name?
                $atstructure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneBy(array(
                    'solarSystemId' => $v['solar_system_id'],
                    'typeId' => $v['type_id'],
                    'structureName' => $v['name']));
            }

            if ($atstructure_entity) {
                $structure_data['atstructure_id'] = $atstructure_entity->getId();
            }

            $structure_data['eve_typeid'] = $v['type_id'];
            $structure_data['structure_name'] = $v['name'];
            $structure_data['corporation_id'] = $v['corporation_id'];
            $structure_data['solarsystem_id'] = $v['solar_system_id'];
            $structure_data['structure_id'] = $v['structure_id'];
            $structure_data['state_timer_start'] = (!empty($v['state_timer_start']) ? self::eveDateToTimestamp($v['state_timer_start']) : null);
            $structure_data['state_timer_end'] = (!empty($v['state_timer_end']) ? self::eveDateToTimestamp($v['state_timer_end']) : null);
            $structure_data['fuel_expires'] = self::eveDateToTimestamp($v['fuel_expires']);
            $structure_data['reinforce_hour'] = $v['reinforce_hour'];
            $structure_data['reinforce_weekday'] = $v['reinforce_weekday'];
            $structure_data['structure_state'] = $v['state'];

            if (!empty($v['moon_id'])) {
                $structure_data['celestial_id'] = $v['moon_id'];
                $structure_data['celestial_distance'] = '5000';
                $structure_data['chunk_arrival_time'] = self::eveDateToTimestamp($v['chunk_arrival_time']);
                $structure_data['extraction_start_time'] = self::eveDateToTimestamp($v['extraction_start_time']);
                $structure_data['natural_decay_time'] = self::eveDateToTimestamp($v['natural_decay_time']);
            }

            // get evetype group
            $invtype_entity = $this->entityManager->getRepository(\Application\Entity\Invtypes::class)->findOneByTypeid($structure_data['eve_typeid']);
            $structure_data['eve_groupid'] = $invtype_entity->getGroupid()->getGroupid();

            if ($climode) {
                echo "\twrite structure : " . $structure_data['structure_name'] . PHP_EOL;
            }

            $this->writeStructure($structure_data);
        }
    }

    /**
     * Get array of all corporation structures
     *
     * @param string $corp_id
     * @param \Seat\Eseye\Containers\EsiAuthentication auth_container
     * @return array structures Array
     */
    private function esiGetAllCorpStructures($corp_id, $auth_container)
    {
        $extractions = array();
        $page = 1;
        $xpages = 0;

        do {
            $res = $this->eveESIManager->authedRequest('get', '/corporations/{corporation_id}/structures/', ['corporation_id' => $corp_id], $auth_container, $page);
            $extractions = array_merge($extractions, (array) $res);
            $xpages = $res->headers['X-Pages'];
        } while ($page++ < $xpages);

        // convert the result into a assoc array with the structure ID as a key
        $extractions_arr = [];
        foreach ($extractions as $v) {
            $extractions_arr[$v->structure_id] = (array) $v;
        }

        return $extractions_arr;
    }

    /**
     * Enrich structures array by calling ESI for each structure with
     *  - name
     *  - solar_system_id
     *  - type_id
     *
     * @param array struct_arr
     * @param \Seat\Eseye\Containers\EsiAuthentication auth_container
     * @return array updated struct_arr
     */
    private function esiGetCorpStructuresByStructures($struct_arr, $auth_container)
    {
        foreach ($struct_arr as $k => $v) {
            $res = $this->eveESIManager->authedRequest('get', '/universe/structures/{structure_id}/', ['structure_id' => $k], $auth_container);
            $struct_arr[$k]['name'] = $res->name;
            $struct_arr[$k]['solar_system_id'] = $res->solar_system_id;
            $struct_arr[$k]['type_id'] = $res->type_id;
        }

        return $struct_arr;
    }

    /**
     * Enrich structures array by calling ESI for each structure with
     *  - moon id
     *  - extraction details
     *
     * @param array struct_arr
     * @param string $corp_id
     * @param \Seat\Eseye\Containers\EsiAuthentication auth_container
     * @return array structures Array
     */
    private function esiGetCorpMinningExtractions($struct_arr, $corp_id, $auth_container)
    {
        $extractions = array();
        $page = 1;
        $xpages = 0;

        do {
            $res = $this->eveESIManager->authedRequest('get', '/corporation/{corporation_id}/mining/extractions/', ['corporation_id' => $corp_id], $auth_container, $page);
            $extractions = array_merge($extractions, (array) $res);
            $xpages = $res->headers['X-Pages'];
        } while ($page++ < $xpages);

        // enrich struct_arr with the data from the extractions
        foreach ($extractions as $v) {
            $struct_arr[$v->structure_id]['moon_id'] = $v->moon_id;
            $struct_arr[$v->structure_id]['chunk_arrival_time'] = $v->chunk_arrival_time;
            $struct_arr[$v->structure_id]['extraction_start_time'] = $v->extraction_start_time;
            $struct_arr[$v->structure_id]['natural_decay_time'] = $v->natural_decay_time;
        }

        return $struct_arr;
    }

    /**
     *
     * @return type
     */
    public static function getStructureArray()
    {
        return ([
            'scantype' => null,
            'atstructure_id' => null,
            'eve_typeid' => null,
            'eve_typename' => null, // not written to db, to be resolved to his ID
            'eve_groupid' => null,
            'eve_groupname' => null,
            'eve_categoryname' => null, // not written to db, to be resolved to his ID
            'celestial_id' => null,
            'celestial_distance' => null,
            'structure_name' => null,
            'signature' => null,
            'quality' => 0,
            'distance' => null,
            'corporation_id' => null,
            'solarsystem_id' => null,
            'at_cosmic_detail_id' => null,
            'target_system_id' => null,
            'structure_id' => null,
            'fuel_expires' => null,
            'reinforce_hour' => null,
            'reinforce_weekday' => null,
            'structure_state' => null,
            'state_timer_start' => null,
            'state_timer_end' => null,
            'chunk_arrival_time' => null,
            'extraction_start_time' => null,
            'natural_decay_time' => null
        ]);
    }

    /**
     * Convert EVE atom time format (2019-10-05T15:49:53Z) to dateTime
     *
     * @param string $evedate
     * @return DateTime converted date
     */
    private static function eveDateToTimestamp($evedate)
    {
        return new \DateTime($evedate);
    }
}
