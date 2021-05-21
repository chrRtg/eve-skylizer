<?php
namespace VposMoon\Service;

/**
 * The StructuresManager writes and changes the data in AtStructure like anomalies, structures and such
 */
class LedgerManager
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
     * get List of all structures in Ledger
     *
     * @return array    resultSet
     */
    public function getLedgerStructures()
    {
        $sql = 'select
                    aml.structure_name,
                    aml.structure_id,
                    md.itemName as celestial,
                    round(sum(aml.goo_quantity),0) as gqf,
                    round(sum((aml.goo_quantity) * aml.refinedPrice),0) as cpf,
                    group_concat(DISTINCT(select round(sum((aml1.goo_quantity) * aml1.refinedPrice),0) from at_mining_ledger aml1 where aml1.structure_id = aml.structure_id and aml1.last_updated >= DATE_SUB(NOW(), INTERVAL 5 WEEK))) as cp5w,
                    group_concat(DISTINCT(select round(sum(aml4.goo_quantity),0) from at_mining_ledger aml4 where aml4.structure_id = aml.structure_id and aml4.last_updated >= DATE_SUB(NOW(), INTERVAL 5 WEEK))) as gq5w
                from
                    at_mining_ledger aml
                left join mapDenormalize md on aml.celestial_id = md.itemID
                group by
                    aml.structure_id, aml.structure_name, md.itemName';

        $statement = $this->entityManager->getConnection()->prepare($sql);
        $statement->execute();

        return ($statement->fetchAll());
    }

    /**
     * get oldest and newest Legder date.
     *
     * @return array resultSet
     */
    public function getLedgerMinMaxDate() {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('min(aml.lastUpdated) as min, max(aml.lastUpdated) as max')
            ->from(\VposMoon\Entity\AtMiningLedger::class, 'aml')
            ->setMaxResults(1);

        return ($queryBuilder->getQuery()->getOneOrNullResult());
    }

    public function getLedgerPerDay($structure_id = 0)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();


        $queryBuilder->select('aml.lastUpdated as last_updated, sum(aml.gooQuantity) as pieces')
            ->addSelect('round(sum(aml.gooQuantity * aml.baseprice),0) as orePrice')
            ->addSelect('round(sum((aml.gooQuantity * aml.refinedprice)),0) as compositionPrice')
            ->addSelect("group_concat(DISTINCT ats.structureName SEPARATOR ', ') as structures")
            ->from(\VposMoon\Entity\AtMiningLedger::class, 'aml')
            ->from(\VposMoon\Entity\AtStructure::class, 'ats')
            ->from(\Application\Entity\Invtypes::class, 'it')
            ->where('aml.eveInvtypesTypeid = it.typeid')
            ->andWhere('ats.structureId = aml.structureId')
            ->groupBy('aml.lastUpdated')
            ->orderBy('aml.lastUpdated');

        if (!empty($structure_id)) {
            $queryBuilder->andWhere('aml.structureId = :sid')->setParameters(array('sid' => (intval($structure_id))));
        }

        return ($queryBuilder->getQuery()->getResult());
    }

    public function ping()
    {
        return 'I am LedgerManager';
    }
}
