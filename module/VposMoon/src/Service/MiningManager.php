<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtStructure;
use VposMoon\Entity\AtMiningObserver;
use VposMoon\Entity\AtMiningLedger;
use Application\Service\EveDataManager;

/**
 * The Miningmanager takes care of all Moon Mining related stuff
 */
class MiningManager
{

    /**
     * Doctrine entity manager.
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
     * Constructs the service.
     */
    public function __construct($entityManager, $eveESIManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveESIManager = $eveESIManager;
        $this->logger = $logger;
    }

    

    /**
     * Fetch from ESI all mining observers of a given corporation and their mining ledgers
     *
     * @param int $corp_id
     * @param array $auth_container
     * @param bool $climode
     * @return void
     */
    public function esiFetchCorporationMiningData($corp_id, $auth_container, $climode)
    {
        if ($climode) {
            echo "fetch MiningLedger" .  PHP_EOL;
        }

        $observers = $this->esiGetCorpStructuresObservers($corp_id, $auth_container);
        $this->esiGetCorpMiningLedger($observers, $corp_id, $auth_container, $climode);

        return true;
    }


    /**
     * Get corporation mining observers and persist them
     *
     * @param int $corp_id
     * @param array $auth_container
     * @return void
     */
    private function esiGetCorpStructuresObservers($corp_id, $auth_container)
    {
        $payload = array();
        $page = 1;
        $xpages = 0;

        do {
            $res = $this->eveESIManager->authedRequest('get', '/corporation/{corporation_id}/mining/observers/', ['corporation_id' => $corp_id], $auth_container, $page);
            if (!$res) {
                return false;
            }
            $payload = array_merge($payload, (array) $res);
            $xpages = $res->headers['X-Pages'];
        } while ($page++ < $xpages);

        foreach ($payload as $v) {
            $this->writeMiningObserver((array) $v);
        }

        return $payload;
    }


    /**
     * Fetch all corporation mining ledgers
     *
     * @param array Observers
     * @param int $corp_id
     * @param array $auth_container
     * @param bool $climode
     * @return void
     */
    private function esiGetCorpMiningLedger($observers, $corp_id, $auth_container, $climode)
    {
        foreach ($observers as $v) {
            if ($climode) {
                echo "\tO";
            }
   
            $this->esiGetSingleCorpMiningLedger($v->observer_id, $corp_id, $auth_container, $climode);
            if ($climode) {
                echo PHP_EOL;
            }
        }
    }


    /**
     * Fetch the corporation mining ledger of a give structure and persist them
     *
     * @param bigint $structure_id
     * @param int $corp_id
     * @param array $auth_container
     * @param bool $climode
     * @return void
     */
    private function esiGetSingleCorpMiningLedger($structure_id, $corp_id, $auth_container, $climode)
    {
        $payload = array();
        $page = 1;
        $xpages = 0;
        
        do {
            $res = $this->eveESIManager->authedRequest('get', '/corporation/{corporation_id}/mining/observers/{observer_id}/', ['corporation_id' => $corp_id, 'observer_id' => $structure_id], $auth_container, $page);
            if (!$res) {
                return false;
            }
            $payload = array_merge($payload, (array) $res);
            $xpages = $res->headers['X-Pages'];
        } while ($page++ < $xpages);

        foreach ($payload as $v) {
            if ($climode) {
                echo ".";
            }
            $this->writeMiningLedger((array) $v, $structure_id);
        }
    }


    /**
     * create and persist a new mining ledger entry but only if not already existing
     *
     * @param array Ledgers fetched from ESI
     * @param bigint Structure ID
     * @return bool true if a entry has been created
     */
    private function writeMiningLedger($ledgers, $structure_id)
    {
        if ($ledgers['last_updated']) {
            $last_updated = \VposMoon\Service\StructureManager::eveDateToTimestamp($ledgers['last_updated']);
        }

        $ledger_entity = null;
        if ($structure_id) {
            $ledger_entity = $this->entityManager->getRepository(AtMiningLedger::class)->findOneBy(array(
                'structureId' => $structure_id,
                'eveUserid' => $ledgers['character_id'],
                'eveInvtypesTypeid' => $ledgers['type_id'],
                'lastUpdated' => $last_updated));
        }

        // if already existing not need to write it again
        if ($ledger_entity !== null) {
            return false;
        }

        // new entity
        $ledger_entity = new AtMiningLedger();

        $ledger_entity->setLastUpdated($last_updated);

        if ($ledgers['character_id']) {
            $ledger_entity->setEveUserid($ledgers['character_id']);
        }

        if ($ledgers['recorded_corporation_id']) {
            $ledger_entity->setEveCorpid($ledgers['recorded_corporation_id']);
        }

        if ($structure_id) {
            $ledger_entity->setStructureId($structure_id);
        }

        if ($ledgers['quantity']) {
            $ledger_entity->setGooQuantity($ledgers['quantity']);
        }

        if ($ledgers['type_id']) {
            $ledger_entity->setEveInvtypesTypeid($ledgers['type_id']);
        }

        $this->entityManager->persist($ledger_entity);
        $this->entityManager->flush();
        return true;
    }


    /**
     * create and persist a new mining observer enty but only if not already existing
     *
     * @param array $observers
     * @return bool true if a entry has been created
     */
    public function writeMiningObserver($observers)
    {
        if ($observers['last_updated']) {
            $last_updated = \VposMoon\Service\StructureManager::eveDateToTimestamp($observers['last_updated']);
        }

        $structure_entity = null;
        if ($observers['observer_id']) {
            $structure_entity = $this->entityManager->getRepository(AtMiningObserver::class)->findOneBy(array('structureId' => $observers['observer_id'], 'lastUpdated' => $last_updated));
        }

        // if already existing not need to write it again
        if ($structure_entity !== null) {
            return false;
        }

        // new entity
        $structure_entity = new AtMiningObserver();

        $structure_entity->setLastUpdated($last_updated);

        if ($observers['observer_id']) {
            $structure_entity->setStructureId($observers['observer_id']);
        }

        if ($observers['observer_type']) {
            $structure_entity->setObserverType($observers['observer_type']);
        }

        $this->entityManager->persist($structure_entity);
        $this->entityManager->flush();
        return true;
    }
}
