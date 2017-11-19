<?php

namespace Application\Service;

use Application\Entity\Trntranslations;
use Application\Entity\Invtypes;
use Application\Entity\Invgroups;
use Application\Entity\Invmarketgroups;
use Application\Entity\Invcategories;
use Application\Entity\EveAlly;
use Application\Entity\EveCorporation;
use Application\Entity\Mapdenormalize;
use Application\Entity\Maplocationwormholeclasses;
use Application\Entity\Mapsolarsystemjumps;

/**
 * Manager service class to fetch EVE data from your database or to fill your database with EVE related data
 * usually provided by ESI, the EVE swagger API
 */
class EveDataManager {

	/**
	 * Entity manager.
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
	 * Constructor.
	 */
	public function __construct($entityManager, $eveESIManager, $logger)
	{
		$this->entityManager = $entityManager;
		$this->eveESIManager = $eveESIManager;
		$this->logger = $logger;
	}

	/**
	 * Fetch system or constellation by a the first letters of the name
	 * 
	 * common usage is typeahead select field to select a location.
	 * 
	 * @param type $partial
	 * @param type $limit
	 * @return type
	 */
	public function getSystemByPartial($partial, $limit = 10)
	{
		$queryBuilder = $this->entityManager->createQueryBuilder();

		$queryBuilder->select('md.itemid as id, md.itemid, md.itemname, mdc.itemname as constellation, mdc.itemid as constellationid, mdr.itemname as region, mdr.itemid as regionid')
			->from(Mapdenormalize::class, 'md')
			->leftJoin(Mapdenormalize::class, 'mdc', 'WITH', 'mdc.itemid = md.constellationid')
			->leftJoin(Mapdenormalize::class, 'mdr', 'WITH', 'mdr.itemid = md.regionid')
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
	 * @param int id
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
	 * @param int systemid
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
	 * Fetch inventory prices from EVE-ESI. 
	 * 
	 * Update the Invtypes.baseprice with the prices we got from EVE.
	 * 
	 * @return int updated prices count
	 */
	public function updatePrices()
	{
		$this->logger->debug('updatePrices');
		$price_arr = [];

		// get his corporation details from ESI
		$prices = $this->eveESIManager->publicRequest('get', '/markets/prices/', []);

		$prices_data = get_object_vars($prices);

		// transform prices into a associative array with the typeID as key
		foreach ($prices_data as $price) {
			$price_arr[$price->type_id] = (!empty($price->average_price) ? $price->average_price : (!empty($price->adjusted_price) ? $price->adjusted_price : 0.0));
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
			if (!empty($price_arr[$tid])) {
				//var_dump($tid . '  has price: ' . $price_arr[$tid] . '  baseprice: '. $types->getBaseprice());
				$types->setBaseprice($price_arr[$tid]);
				$j++;
			}

			if (($i % $batchSize) === 0) {
				$this->entityManager->flush(); // Executes all updates.
				$this->entityManager->clear(); // Detaches all objects from Doctrine!
			}
			++$i;
		}
		$this->entityManager->flush();
		
		$this->logger->info($j . ' Prices were updated via ESI');
		
		return($j);
	}

}
