<?php

namespace VposMoon\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use VposMoon\Form\MoonForm;
use Zend\Uri\Uri;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

/**
 * The MoonController is about Moons, Survey Scans and Moon Goo
 */
class MoonController extends AbstractActionController {

	/**
	 * Entity manager.
	 * @var Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	/**
	 *
	 * @var \Application\Service\EveDataManager
	 */
	private $eveDataManager;

	/**
	 * Moon manager.
	 * @var VposMoon\Service\MoonManager
	 */
	private $moonManager;

	/**
	 *
	 * @var VposMoon\Service\CosmicManager
	 */
	private $cosmicManager;

	/**
	 *
	 * @var \Application\Controller\Plugin\LoggerPlugin
	 */
	private $logger;

	/**
	 * Constructor. Its purpose is to inject dependencies into the controller.
	 */
	public function __construct($entityManager, $moonManager, $cosmicManager, $eveDataManager, $logger)
	{
		$this->entityManager = $entityManager;
		$this->moonManager = $moonManager;
		$this->cosmicManager = $cosmicManager;
		$this->eveDataManager = $eveDataManager;
		$this->logger = $logger;
	}

	
	/**
	 * Main index action, will create the Moon List with filters and the input field.
	 * 
	 * @return ViewModel
	 */
	public function indexAction()
	{
		$message = null;

		// Create login form
		$form = new MoonForm();

		// Check if user has submitted the form
		if ($this->getRequest()->isPost()) {

			// Fill in the form with POST data
			$data = $this->params()->fromPost();

			$form->setData($data);

			// Validate form
			if ($form->isValid()) {
				// Get filtered and validated data
				$data = $form->getData();
				// store input to text file, one file per user per day
				file_put_contents('./data/storage/' . date('ymd') . '_' . $this->currentUser()->getEveUserid() . '.txt', $data['scan'] . PHP_EOL, FILE_APPEND);

				// process the scan
				$pc_res = $this->processScan($data['scan']);
				$message = $pc_res['message'];

				// one solution to reset a form
				$form = new MoonForm();
			} else {
				$message[] = array('error' => 'your input has not been recognized as valid input');
			}
		}

		// create filters for moonList out of get parameters and the user session parameters
		$filters = $this->moonManager->manageFilters($this->params()->fromQuery(), $this->eveDataManager);

		// fetch list of moons
		$moon_list = $this->moonManager->moonList($filters);

		return new ViewModel([
			'form' => $form,
			'moon_list' => $moon_list,
			'filters' => $filters,
			'filters_json' => \json_encode($filters),
			'message' => $message
		]);
	}

	/**
	 * Initiate a Price Update
	 * @see \Application\Service\EveDataManager::updatePrices()
	 * 
	 * @return ViewModel
	 */
	public function priceUpdateAction()
	{
		$cnt = $this->eveDataManager->updatePrices();

		return new ViewModel([
			'cnt' => $cnt,
		]);
	}
	
	public function priceUpdateConsole()
	{
		$request = $this->getRequest();
		
		return $this->eveDataManager->updatePrices();
	}

	/**
	 * Export Moons and Moongoo as a CSV and offer a download link.
	 * 
	 * No further rendering will happen.
	 * 
	 * @return void
	 */
	public function dlMoonsCsvAction()
	{
		$columns = [
			'm.moonId',
			'm.namedStructure',
			'm.ownedBy',
			'm.lastseenBy',
			'm.lastseenDate',
			'mg.moongooId',
			'mg.gooAmount',
			'it.typeid',
			'it.typename'
		];

		$queryBuilder = $this->entityManager->createQueryBuilder();

		$queryBuilder->select($columns)
			->from(\VposMoon\Entity\AtMoon::class, 'm')
			->leftJoin(\VposMoon\Entity\AtMoongoo::class, 'mg', 'WITH', 'm.moonId = mg.moon')
			->leftJoin(\Application\Entity\Invtypes::class, 'it', 'WITH', 'it.typeid = mg.eveInvtypesTypeid');

		$res = $queryBuilder->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_SCALAR);

		if (!empty($res) && is_array($res)) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=' . 'skylizer_moons_' . $this->currentUser()->getEveUserid() . '_' . date('Ymd_His') . '.csv');
			$out = fopen('php://output', 'w');
			// Headline Columns
			fputcsv($out, $columns);

			// write csv line by line
			foreach ($res as $line) {
				fputcsv($out, $line);
			}
			fclose($out);
		}

		// Return Response to avoid default view rendering
		return $this->getResponse();
	}

	/**
	 * Fetch a list of systems and constellations from map by a search term.
	 * 
	 * The search term is given in get parameter "q", 
	 * example:  /vposmoon/getSystemsJson?q=ji
	 * 
	 * @return JsonModel
	 */
	public function getSystemsJsonAction()
	{
		$limit = 10;
		$query = $this->params()->fromQuery('q');

		if (!empty($query)) {
			$res = $this->eveDataManager->getSystemByPartial($query);

			if (!empty($res)) {
				return new JsonModel([
					'status' => 'SUCCESS',
					'items' => $res
				]);
			}

			return new JsonModel([
				'status' => 'EMPTY',
			]);
		}

		return new JsonModel([
			'status' => 'FAIL',
		]);
	}

	/**
	 * Delete a Moon and his Goo
	 * 
	 * @return redirect to moon index page
	 */
	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id', 0);


		$this->logger->debug('someeone tries to delete a moon (' . $id . ')');

		return $this->redirect()->toRoute('vposmoon', ['action' => 'index']);
	}

	/**
	 * Analyse scan results
	 * The scan result get idendified and routed to the appropriate manager
	 * After the line by line analysis is finished the managers store the (prepared) values.
	 * 
	 * @param string $data_scan
	 * @return array 'message', 'counter'
	 */
	private function processScan($data_scan)
	{
		$res_counter = array('goo' => 0, 'dscan' => 0, 'scan' => 0);

		// split input into lines. Interpret result line By line
		$lines = preg_split("/[\f\r\n]+/", $data_scan);
		if (!empty($lines)) {
			foreach ($lines as $line) {
				// if is moon Scan
				if ($this->moonManager->isMoonScan($line, $this->currentUser()->getEveUserid())) {
					$res_counter['goo'] ++;
				} else if ($this->cosmicManager->isDscan($line)) {
					$res_counter['dscan'] ++;
				} else if ($this->cosmicManager->isScan($line)) {
					$res_counter['scan'] ++;
				} else if (trim($line)) { // avoid emtpy lines
					$this->logger->notice('#Scan: no match for line:  __' . $line . '__');
				}
			}
		} else {
			$message[] = array('info' => 'Emtpy input field, nothing to do');
		}
		$message[] = array('info' => 'thanks, scanned a lot');
		$this->logger->debug($res_counter);

		// if results were idendified & collected, now the final step, preperatate & persist
		if ($res_counter['goo']) {
			$this->moonManager->processScan();
		}
		if ($res_counter['dscan'] || $res_counter['scan']) {
			$this->cosmicManager->processScan();
		}

		return(array('message' => $message, 'counter' => $res_counter));
	}

	/**
	 * No functional code but helps me to write better code then below
	 */
	private function codeCollection()
	{
		;
//		$this->moonManager->addMoonGoo();
//		$res = $this->cosmicManager->test(16);
//		var_dump( \Doctrine\Common\Util\Debug::export($res, 4));
//		$this->logger->notice($res->getCosmicMain()->getGroupNameDe());
		//$this->logger->notice(var_export($res, true));
//echo('<pre>');		
//		\Doctrine\Common\Util\Debug::dump($res);
//		\Doctrine\Common\Util\Debug::dump($res->getCosmicMain()->getType());
//		\Doctrine\Common\Util\Debug::dump($res->getCosmicMain()->getGroupid()->getGroupname());
//		\Doctrine\Common\Util\Debug::dump($res->getCosmicMain()->getGroupid());
//		\Doctrine\Common\Util\Debug::dump($res,5);
//		$res = $this->cosmicManager->test2(197);
//echo('<pre>');		
//		\Doctrine\Common\Util\Debug::dump($res);
//		$res = $this->cosmicManager->test3(2);
//
////\Doctrine\Common\Util\Debug::dump($moon_entity);
//$this->entityManager->getRepository(\Application\Entity\Invtypes::class)->findOneByTypename('Pyroxeres')
//		$res = $this->cosmicManager->test(16);
//		var_dump( \Doctrine\Common\Util\Debug::export($res, 4));
//		$this->logger->notice($res->getCosmicMain()->getGroupNameDe());
		//$this->logger->notice(var_export($res, true));
//echo('<pre>');		
//		\Doctrine\Common\Util\Debug::dump($res);
//		\Doctrine\Common\Util\Debug::dump($res->getCosmicMain()->getType());
//		\Doctrine\Common\Util\Debug::dump($res);
//		$this->logger->notice($res);
//		$this->logger->emerg('EMERG');
//		$this->logger->alert('ALERT');
//		$this->logger->crit('CRIT');
//		$this->logger->err('ERR');
//		$this->logger->warn('WARN');
//		$this->logger->notice('NOTICE');
//		$this->logger->info('INFO');
//		$this->logger->debug('DEBUG');
	}

}
