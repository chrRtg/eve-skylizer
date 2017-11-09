<?php

namespace VposMoon\Service;

use VposMoon\Entity\AtCosmicMain;
use VposMoon\Entity\AtCosmicDetail;

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
	
	// pattern to analyze and break various inputs
	private $cosmic_scan_regexp = '/([A-Z]{3}-[0-9]{3})\t(.*)\t(.*)\t(.*)\t([0-9\,]+\%)\t(.*)/';
	private $cosmic_dscan_regexp = '/(.+)\t(.+)\t(-|[0-9\.\,]+ [AUkm]+)/';
	
	/**
	 * Constructs the service.
	 */
	public function __construct($entityManager, $logger)
	{
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}

	public function isScan($line)
	{
		if (preg_match($this->cosmic_scan_regexp, $line, $match)) {
			$this->logger->debug('it\'s a SCAN');
			return true;
		} 

		return(false);
	}	
	
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

}
