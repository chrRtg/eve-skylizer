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
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $userManager, $eveESIManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->eveESIManager = $eveESIManager;
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
     * @return void
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

        $numDeleted = $qb->getQuery()->execute();
    }


    public function fetchCoprporationStructures($climode=false)
    {
        // get next cliUser and set in_use = 1
        $cli_users = $this->userManager->getNextCliUser();
        if(!$cli_users) {
            return 0;
        }
        // ... and set in_use = 1
        // $this->userManager->setCliUserInUse($cli_users);

        // Get all corporation structures
        $struct_arr = $this->getAllCorpStructures($cli_users->getEveCorpid(), unserialize($cli_users->getAuthContainer()));
        if ($climode) {
            echo "fetched " . count($struct_arr) . " structures for corporation ".$cli_users->getEveCorpid()." " . PHP_EOL;
        }

        // enrich them with name, type and solar system
        $struct_arr = $this->getCorpStructuresByStructures($struct_arr, unserialize($cli_users->getAuthContainer()));
        if ($climode) {
            echo "enriched them with detail information like name and type" . PHP_EOL;
        }

        // enrich them with their extractions if drilling plattforms
        $struct_arr = $this->getCorpMinningExtractions($struct_arr, $cli_users->getEveCorpid(), unserialize($cli_users->getAuthContainer()));
        if ($climode) {
            echo "enriched them with some moon mining extractions" . PHP_EOL;
        }

        $this->logger->debug('## run fetchCoprporationStructures: ' . print_r($struct_arr, true));

        // for each CliUser 

        // remove CliUser
    }

    /**
     * Get array of all corporation structures
     *
     * @param string $corp_id
     * @param \Seat\Eseye\Containers\EsiAuthentication auth_container
     * @return array structures Array
     */
    private function getAllCorpStructures($corp_id, $auth_container)
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
        foreach($extractions as $v) {
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
    private function getCorpStructuresByStructures($struct_arr, $auth_container)
    {
        foreach($struct_arr as $k => $v) {
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
    private function getCorpMinningExtractions($struct_arr, $corp_id, $auth_container)
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
            'eve_groupname' => null, // not written to db, to be resolved to his ID
            'eve_categoryname' => null,
            'celestial_id' => null,
            'celestial_distance' => null,
            'structure_name' => null,
            'signature' => null,
            'quality' => 0,
            'distance' => null,
            'corporation_id' => null,
            'solarsystem_id' => null,
            'at_cosmic_detail_id' => null,
            'target_system_id' => null
        ]);
    }
}
