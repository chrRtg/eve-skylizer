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
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $logger)
    {
        $this->entityManager = $entityManager;
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

    public function ping() {
        $this->logger->debug('#### StructureManager PING()  says "Hello!" ');
    }
}
