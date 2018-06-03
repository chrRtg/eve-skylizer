<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtCosmicMain;
use VposMoon\Entity\AtCosmicDetail;
use VposMoon\Entity\AtStructure;

/**
 * Description of MoonManager
 *
 * @author chr
 */
class CosmicManager {

	/**
	 * Doctrine entity manager.
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;
	private $logger;
	
	/**
	 *
	 * @var \User\Service\EveSSOManager
	 */
	private $eveSSOManager;
	
	// pattern to analyze and break various inputs
	private $cosmic_scan_regexp = '/([A-Z]{3}-[0-9]{3})\t(.*)\t(.*)\t(.*)\t([0-9\,]+\%)\t(.*)/';
	private $cosmic_dscan_regexp = '/(.+)\t(.+)\t(-|[0-9\.\,]+ [AUkm]+)/';
	
	/**
	 * Constructs the service.
	 */
	public function __construct($entityManager, $eveSSOManager, $logger)
	{
		$this->entityManager = $entityManager;
		$this->eveSSOManager = $eveSSOManager;
		$this->logger = $logger;
	}

	/**
	 * Check if scan is a SCAN
	 * 
	 * @param string $line
	 * @return boolean
	 */
	public function isScan($line)
	{
		if (preg_match($this->cosmic_scan_regexp, $line, $match)) {
			$this->logger->debug('it\'s a SCAN');
			return true;
		} 

		return(false);
	}	

	/**
	 * Check if scan is a DSCAN
	 * 
	 * @param string $line
	 * @return boolean
	 */	
	public function isDscan($line)
	{
		if (preg_match($this->cosmic_dscan_regexp, $line, $match)) {
			$this->logger->debug('it\'s a Dscan');
			return true;
		} 

		return(false);
	}	

	public function processScan() {
		$this->logger->debug('processs collected scan/dscan data');
	}


	/**
	 * Insert or Update data a AtStructure table entry.
	 * 
	 * @param int $structure_id	ID of the at-structure, 0 on insert
	 * @param int $inv_types_typeid	invTypes.typeID (the structure)
	 * @param int $map_denormalize_itemid	mapDenormalize.itemID to attach structure to a celestial
	 * @param int $corp_id		EveCorporation.corporationId of the owning corporation
	 * @param string $structure_name	The player given name of the structure
	 * @return type
	 */
	public function writeStructure($structure_id=0, $inv_types_typeid=0, $map_denormalize_itemid=0, $corp_id=0, $structure_name=null)
	{
		$structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_id);

		// insert (or update)
		if ($structure_entity === null) {
			$structure_entity = new AtStructure();
			$structure_entity->setCreateDate(new \DateTime("now"));
			$structure_entity->setCreatedBy($this->eveSSOManager->getIdentityID());
			$structure_entity->setItemId(0);
			$structure_entity->setTypeId(0);
			$structure_entity->setCorporationId(0);
		}

		if($map_denormalize_itemid) {
			$structure_entity->setItemId($map_denormalize_itemid); // mapDenormalize.itemID in case of celestials,
		}
		if($inv_types_typeid) {
			$structure_entity->setTypeId($inv_types_typeid); // invTypes.typeID (the structure)
		}
		if($corp_id) {
			$structure_entity->setCorporationId($corp_id); // owner: EveCorporation.corporationId
		}
		if($structure_name) {
			$structure_entity->setStructureName($structure_name);
		}

		$structure_entity->setLastseenDate(new \DateTime("now"));
		$structure_entity->setLastseenBy($this->eveSSOManager->getIdentityID());

		$this->entityManager->persist($structure_entity);
		$this->entityManager->flush();

		// fetch result and hydrate to array
		$structure_entity = $this->entityManager->getRepository(AtStructure::class)->findOneById($structure_entity->getId());
		$hydrator = new \DoctrineModule\Stdlib\Hydrator\DoctrineObject($this->entityManager);
		$structure_array = $hydrator->extract($structure_entity);

		return($structure_array);
	}
	
	
	public function ping($param = '')
	{
		return('CosmicManager-ping ' . $param);
	}

}
