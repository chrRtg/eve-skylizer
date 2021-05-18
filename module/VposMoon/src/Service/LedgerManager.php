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


        return($statement->fetchAll());
    }


    public function ping()
    {
        return 'I am LedgerManager';
    }
}