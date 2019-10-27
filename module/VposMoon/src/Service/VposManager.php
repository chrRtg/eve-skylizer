<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtStructure;
//use Seat\Eseye\Eseye;

/**
 * The VposManager manages all list and filter operations about Scans of anomalies, structures and such
 */
class VposManager
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
     *
     * @var \User\Service\EveSSOManager
     */
    private $eveSSOManager;

    /**
     *
     * @var \Zend\Session\Container
     */
    private $sessionContainer;


    /**
     *
     * @var int
     */
    private $eve_userid = 0;

    /**
     * Constructs the service.
     */
    public function __construct($sessionContainer, $entityManager, $eveSSOManager, $eveESIManager, $logger)
    {
        $this->sessionContainer = $sessionContainer;
        $this->entityManager = $entityManager;
        $this->eveSSOManager = $eveSSOManager;
        $this->eveESIManager = $eveESIManager;
        $this->logger = $logger;
    }
    
    /**
     * create list of structures and anomalies according to filter settings
     *
     * @param  array $filters
     * @return array
     */
    public function vposList($filters)
    {
        $query = $this->entityManager->getConnection()->exec('SET @@group_concat_max_len = 8000;');


        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('at')
            ->addSelect('acd.typenameDe as acd_typeNameDE, acd.typename as acd_typeName, acd.urlDe as acd_urlDe, acd.urlEn as acd_urlEn, acd.class as acd_class, acd.type as acd_type, acd.comment as acd_comment')
            ->addSelect('uchgd.eveUsername as uchgd_lastUsername')
            ->addSelect('own.corporationName own_corporationName, own.ticker as own_corpticker')
            ->addSelect('grp.groupname as grp_name, cat.categoryname as cat_categoryname, it.typename as it_typename')
            ->addSelect('map.itemname as map_name, celest.itemname as celest_name')
            ->addSelect('map.solarsystemid as mapn, map.constellationid as mapc')
            ->addSelect('mwc.itemname as target_name, mwc.security as target_security, mwc1.wormholeclassid as target_classidH, mwc2.wormholeclassid as target_classidL')
            ->from(\VposMoon\Entity\AtStructure::class, 'at')
            ->leftJoin(\VposMoon\Entity\AtCosmicDetail::class, 'acd', 'WITH', 'at.atCosmicDetailId = acd.cosmicDetailId')
            ->leftJoin(\User\Entity\User::class, 'uchgd', 'WITH', 'at.lastseenBy = uchgd.eveUserid')
            ->leftJoin(\Application\Entity\EveCorporation::class, 'own', 'WITH', 'at.corporationId = own.corporationId')
            ->leftJoin(\Application\Entity\Invgroups::class, 'grp', 'WITH', 'at.groupId = grp.groupid')
            ->leftJoin(\Application\Entity\Invcategories::class, 'cat', 'WITH', 'grp.categoryid = cat.categoryid')
            ->leftJoin(\Application\Entity\Invtypes::class, 'it', 'WITH', 'at.typeId = it.typeid')
            ->leftJoin(\Application\Entity\Mapdenormalize::class, 'map', 'WITH', 'map.itemid = at.solarSystemId')
            ->leftJoin(\Application\Entity\Mapdenormalize::class, 'celest', 'WITH', 'at.celestialId = celest.itemid')
            ->leftJoin(\Application\Entity\Mapdenormalize::class, 'mwc', 'WITH', 'mwc.itemid = at.targetSystemId')
            ->leftJoin(\Application\Entity\Maplocationwormholeclasses::class, 'mwc1', 'WITH', 'mwc1.locationid = at.targetSystemId')
            ->leftJoin(\Application\Entity\Maplocationwormholeclasses::class, 'mwc2', 'WITH', 'mwc2.locationid = mwc.regionid');

        /*
        * now add the filters to the query
        */

        // first run: collect parameters
        $parameter = null;
        if (!empty($filters['system'])) {
            $parameter['mditemid'] = $filters['system'];
        }

        // filter by type
        /*
        Structures     GroupID IN (365,1406,1404,1657)
        Wormhole     GroupID = 988
        Gas & Ore     acd_type IN (gas,ore)
        Exploration acd_type IN (data, relic, ghost)
        Combat      acd_type IN (anomaly, unrated, plex)
        Faction     acd_type = faction
        */
        $filter_gid = array();
        $filter_acdtype = array();
        
        if (!empty($filters['vpos_filter_structures']) && $filters['vpos_filter_structures'] == "1") {
            $filter_gid[] = 365;
            $filter_gid[] = 1406;
            $filter_gid[] = 1404;
            $filter_gid[] = 1657;
            $filter_gid[] = 1408;
            $filter_gid[] = 2016;
            $filter_gid[] = 2017;
        }
        if (!empty($filters['vpos_filter_unscanned']) && $filters['vpos_filter_unscanned'] == "1") {
            $filter_gid[] = 502;
        }
        if (!empty($filters['vpos_filter_wormhole']) && $filters['vpos_filter_wormhole'] == "1") {
            $filter_gid[] = 988;
        }
        if (!empty($filters['vpos_filter_gasore']) && $filters['vpos_filter_gasore'] == "1") {
            $filter_acdtype[] = 'gas';
            $filter_acdtype[] = 'ore';
        }
        if (!empty($filters['vpos_filter_exploration']) && $filters['vpos_filter_exploration'] == "1") {
            $filter_acdtype[] = 'data';
            $filter_acdtype[] = 'relic';
            $filter_acdtype[] = 'ghost';
        }
        if (!empty($filters['vpos_filter_combat']) && $filters['vpos_filter_combat'] == "1") {
            $filter_acdtype[] = 'anomaly';
            $filter_acdtype[] = 'unrated';
            $filter_acdtype[] = 'plex';
        }
        if (!empty($filters['vpos_filter_faction']) && $filters['vpos_filter_faction'] == "1") {
            $filter_acdtype[] = 'faction';
        }
        $parameter['filtergid'] = $filter_gid;
        $parameter['filteracdtype'] = $filter_acdtype;

        // if we have parameters, we need where statements
        if (!empty($parameter)) {
            $queryBuilder->setParameters($parameter);
            if (!empty($filters['system'])) {
                $queryBuilder->andWhere('map.itemid = :mditemid or map.constellationid = :mditemid');
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->in('at.groupId', ':filtergid'),
                    $queryBuilder->expr()->in('acd.type', ':filteracdtype')
                )
            );
        }


        $res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);
        return($res);
    }

    /**
     * Manage filters for @see vposList
     *
     * @param  array                               $get_parameters
     * @param  \Application\Service\EveDataManager $eveDataManager
     * @return array
     */
    public function manageFilters($get_parameters, $eveDataManager, $goto_localsystem = false)
    {
        if (!empty($get_parameters['forget'])) {
            unset($this->sessionContainer->filter);
        }
        
        //$this->logger->debug('filters get parameters: ' . print_r($get_parameters, true));

        // restore filter from user session
        if (empty($this->sessionContainer->filter)) {
            $filters = array();
        } else {
            $filters = $this->sessionContainer->filter;
        }

        // start with the view filters - create them with default if not set or existing
        if (!empty($get_parameters['vpos_filter_structures'])) {
            $filters['vpos_filter_structures'] = $get_parameters['vpos_filter_structures'];
        }
        if (!empty($get_parameters['vpos_filter_gasore'])) {
            $filters['vpos_filter_gasore'] = $get_parameters['vpos_filter_gasore'];
        }
        if (!empty($get_parameters['vpos_filter_exploration'])) {
            $filters['vpos_filter_exploration'] = $get_parameters['vpos_filter_exploration'];
        }
        if (!empty($get_parameters['vpos_filter_combat'])) {
            $filters['vpos_filter_combat'] = $get_parameters['vpos_filter_combat'];
        }
        if (!empty($get_parameters['vpos_filter_faction'])) {
            $filters['vpos_filter_faction'] = $get_parameters['vpos_filter_faction'];
        }
        if (!empty($get_parameters['vpos_filter_wormhole'])) {
            $filters['vpos_filter_wormhole'] = $get_parameters['vpos_filter_wormhole'];
        }
        if (!empty($get_parameters['vpos_filter_unscanned'])) {
            $filters['vpos_filter_unscanned'] = $get_parameters['vpos_filter_unscanned'];
        }


        // If filters not exist at this point, prefill them with their default
        // Also set all to active if "show all" has been selected
        if (empty($filters['vpos_filter_structures'])) {
            $filters['vpos_filter_structures'] = "1";
        }
        if (empty($filters['vpos_filter_gasore'])) {
            $filters['vpos_filter_gasore'] = "1";
        }
        if (empty($filters['vpos_filter_exploration'])) {
            $filters['vpos_filter_exploration'] = "1";
        }
        if (empty($filters['vpos_filter_combat'])) {
            $filters['vpos_filter_combat'] = "1";
        }
        if (empty($filters['vpos_filter_faction'])) {
            $filters['vpos_filter_faction'] = "1";
        }
        if (empty($filters['vpos_filter_wormhole'])) {
            $filters['vpos_filter_wormhole'] = "1";
        }
        if (empty($filters['vpos_filter_unscanned'])) {
            $filters['vpos_filter_unscanned'] = "1";
        }


        // system or constellation
        if (!empty($get_parameters['system'])) {
            $filters['system'] = $get_parameters['system'];
        } else {
            $my_location = $this->eveSSOManager->getUserLocationAsSystemID();
            // Current location if given, otherwise we'll take Jita
            $filters['system'] = ($my_location ? $my_location : '30000142');
        }

        //override selection, direct user to his current system if $goto_localsystem is true
        if ($goto_localsystem === true) {
            $filters['system'] = $this->eveSSOManager->getUserLocationAsSystemID();
        }

        $filters['info_system'] = $eveDataManager->getSystemByID($filters['system'])[0];

        // $this->logger->debug('filters persist: ' . print_r($filters, true));

        // persist filter into user session
        $this->sessionContainer->filter = $filters;
        return($filters);
    }
}
