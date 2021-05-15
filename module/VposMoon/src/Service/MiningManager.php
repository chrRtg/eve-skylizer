<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtStructure;
use VposMoon\Entity\AtMiningObserver;
use VposMoon\Entity\AtMiningLedger;
use VposMoon\Entity\AtMiningPeriod;
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
     * @var \Application\Service\EveDataManager
     */
    private $eveDataManager;

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
    public function __construct($entityManager, $eveESIManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveESIManager = $eveESIManager;
        $this->eveDataManager = $eveDataManager;
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

        // get all mining observers
        $observers = $this->esiGetCorpStructuresObservers($corp_id, $auth_container);
        // get the coresponding Ledgers
        $this->esiGetCorpMiningLedger($observers, $corp_id, $auth_container, $climode);
        // prepare the results
        $this->calculateMiningPeriods();

        return true;
    }


    /**
     * Takes all mining ledgers, calculates the active days the moon has been mined and writes
     * the periods to AtMiningPeriods
     *
     * @return void
     */
    private function calculateMiningPeriods()
    {
        $periods = $this->getMiningPeriodsRaw();

        //$this->logger->debug('### calculateMiningPeriods :: periods got from DB :: ' . print_r(count($periods), true));

        if (!$periods) {
            return false;
        }
        
        // iterate through the results
        $last_struct = null;
        $last_start = null;
        $last_end = null;
        $force_write = false;

        $pdata = array();

        foreach ($periods as $val) {
            if (($last_struct &&  $last_struct != $val['structureId']) || $force_write) {
                // write
                $pdata['structure_id'] = $last_struct;
                $pdata['date_start'] = $last_start;
                $pdata['date_end'] = $last_end;
                $this->writeMiningPeriod($pdata);

                // reinit
                $last_struct = null;
                $last_start = null;
                $last_end = null;
                $force_write = null;
                $pdata = array();
            }

            $last_struct = $val['structureId'];

            if (!$last_start) {
                $last_start = $val['lastUpdated'];
            }

            // is next entry is about the next period ?
            $interval = date_diff($last_start, $val['lastUpdated']);
            $diff_days = (int) $interval->format('%a');
            if ($diff_days <= 3) {
                $last_end = $val['lastUpdated'];
            } else {
                // next period but current one has only one day
                if (!$last_end) {
                    $last_end = $val['lastUpdated'];
                }
                $force_write = true;
            }
        }

        // after the foreach write the last entry
        $pdata['structure_id'] = $last_struct;
        $pdata['date_start'] = $last_start;
        $pdata['date_end'] = $val['lastUpdated'];
        $this->writeMiningPeriod($pdata);
    }


    /**
     * Get all Mining Ledgers grouped by structure and date
     *
     * @return array    array of results or null
     */
    private function getMiningPeriodsRaw()
    {
        // select structure_id, last_updated from at_mining_ledger group by structure_id, last_updated order by structure_id, last_updated ;
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('amp.structureId, amp.lastUpdated')
            ->from(\VposMoon\Entity\AtMiningLedger::class, 'amp')
            ->groupBy('amp.structureId, amp.lastUpdated')
            ->orderBy('amp.structureId, amp.lastUpdated');
    
        $res = $queryBuilder->getQuery()->getResult();
        return ($res);
    }


    /**
     * create and persist a mining period, update an existing with same structure and start date
     *
     * @param array a single mining period
     * @return bool true if a entry has been created
     */
    public function writeMiningPeriod($period)
    {
        $period_entity = $this->entityManager->getRepository(AtMiningPeriod::class)->findOneBy(array('structureId' => $period['structure_id'], 'dateStart' => $period['date_start']));

        if (!$period_entity) {
            // new entity
            $period_entity = new AtMiningPeriod();
        }


        if ($period['structure_id']) {
            $period_entity->setStructureId($period['structure_id']);
        }

        if ($period['date_start']) {
            $period_entity->setDateStart($period['date_start']);
        }

        if ($period['date_end']) {
            $period_entity->setDateEnd($period['date_end']);
        }

        $this->entityManager->persist($period_entity);
        $this->entityManager->flush();
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
            if (isset($res->headers['X-Pages'])) {
                $xpages = $res->headers['X-Pages'];
            }
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

        // also persist structure name and celestialId to make the information also available in the future in case the
        // structure has been disbanded
        $structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneByStructureId($structure_id);

        if (isset($structure_entity)) {
            $ledger_entity->setCelestialId($structure_entity->getCelestialId());
            $ledger_entity->setStructureName($structure_entity->getStructureName());
        } else {
            $ledger_entity->setCelestialId(0);
            $ledger_entity->setStructureName('-');
        }

        //prices change but we need to know about the values at the given moment.
        $ore_price_entity = $this->eveDataManager->getOrePrice($ledgers['type_id']);
  
        $ledger_entity->setBaseprice(($ore_price_entity['refined'] ? $ore_price_entity['baseprice'] : 0));
        $ledger_entity->setRefinedprice(($ore_price_entity['refined'] ? $ore_price_entity['refined'] : 0));

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
