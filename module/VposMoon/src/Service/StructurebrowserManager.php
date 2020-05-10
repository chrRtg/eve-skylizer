<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtStructure;
use VposMoon\Entity\AtStructureServices;
use Application\Service\EveDataManager;

/**
 * The StructuresManager writes and changes the data in AtStructure like anomalies, structures and such
 */
class StructurebrowserManager
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
     * create list of structuresaccording to filter settings
     *
     * @param  array $filters
     * @return array
     */
    public function structuresList($constellation, $region, $mode)
    {
        $limit = 10;

        $this->entityManager->getConnection()->exec('SET @@group_concat_max_len = 8000;');

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('at')
            ->addSelect('uchgd.eveUsername as uchgd_lastUsername')
            ->addSelect('own.corporationName own_corporationName, own.ticker as own_corpticker')
            ->addSelect('grp.groupname as grp_name, cat.categoryname as cat_categoryname, it.typename as it_typename')
            ->addSelect('map.itemname as map_name, celest.itemname as celest_name')
            ->addSelect('map.solarsystemid as mapn, map.constellationid as mapc')
            ->from(\VposMoon\Entity\AtStructure::class, 'at')
            ->leftJoin(\User\Entity\User::class, 'uchgd', 'WITH', 'at.lastseenBy = uchgd.eveUserid')
            ->leftJoin(\Application\Entity\EveCorporation::class, 'own', 'WITH', 'at.corporationId = own.corporationId')
            ->leftJoin(\Application\Entity\Invgroups::class, 'grp', 'WITH', 'at.groupId = grp.groupid')
            ->leftJoin(\Application\Entity\Invcategories::class, 'cat', 'WITH', 'grp.categoryid = cat.categoryid')
            ->leftJoin(\Application\Entity\Invtypes::class, 'it', 'WITH', 'at.typeId = it.typeid')
            ->leftJoin(\Application\Entity\Mapdenormalize::class, 'map', 'WITH', 'map.itemid = at.solarSystemId')
            ->leftJoin(\Application\Entity\Mapdenormalize::class, 'celest', 'WITH', 'at.celestialId = celest.itemid');

        /*
        * now add the filters to the query
        */

        $filter_gid = array();

        // filter by structures
        $filter_gid[] = 365;
        $filter_gid[] = 1406;
        $filter_gid[] = 1404;
        $filter_gid[] = 1657;
        $filter_gid[] = 1408;
        $filter_gid[] = 2016;
        $filter_gid[] = 2017;

        $parameter['filtergid'] = $filter_gid;
        $queryBuilder->andWhere('at.groupId IN (:filtergid)');

        switch ($mode) {
            case 'fuel':
                $queryBuilder->andWhere('at.fuelExpires IS NOT NULL');
                $queryBuilder->orderBy('at.fuelExpires', 'ASC');
                break;
            case 'moongoo':
                $queryBuilder->andWhere('at.chunkArrivalTime IS NOT NULL');
                $parameter['datenow'] =  new \DateTime('-3 days');
                $queryBuilder->andWhere('at.naturalDecayTime >= :datenow');
                $queryBuilder->orderBy('at.chunkArrivalTime', 'ASC');
                break;
            case 'timers':
                $queryBuilder->andWhere('at.structureState IS NOT NULL');
                $parameter['timerexclude'] = 'shield_vulnerable';
                $queryBuilder->andWhere('at.structureState != :timerexclude');
                $queryBuilder->orderBy('at.stateTimerEnd', 'ASC');
                // state_timer_end string: Date at which the structure will move to it's next state
                // state_timer_start string: Date at which the structure entered it's current state
                break;
            default:
                return(false);
                break;
        }

        $queryBuilder->setParameters($parameter);
        $queryBuilder->setMaxResults($limit);

        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
        return($res);
    }
}
