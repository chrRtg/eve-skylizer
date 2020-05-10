<?php

namespace VposMoon\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Uri\Uri;
use VposMoon\Form\MoonForm;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use VposMoon\Entity\AtMoon;

/**
 * The MoonController is about Moons, Survey Scans and Moon Goo
 */
class VposController extends AbstractActionController
{

    /**
     * Entity manager.
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
     * Moon manager.
     *
     * @var VposMoon\Service\VposManager
     */
    private $vposManager;

    /**
     *
     * @var VposMoon\Service\StructureManager
     */
    private $structureManager;

    /**
     *
     * @var VposMoon\Service\ScanManager
     */
    private $scanManager;

    /**
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $vposManager, $structureManager, $scanManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->vposManager = $vposManager;
        $this->structureManager = $structureManager;
        $this->scanManager = $scanManager;
        $this->eveDataManager = $eveDataManager;
        $this->logger = $logger;
    }

    
    /**
     * Main index action, will create the Vpos List with filters and the input field.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $message = null;
        $goto_currentsystem = false;

        // Delete all anomalies older than 3 days
        $this->structureManager->removeOutdatedAnomalies();

        // Create scan input form
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

                // process the scan
                $pc_res = $this->scanManager->processScan($data['scan'], $this->currentUser()->getEveUserid());
                $message = $pc_res;
                $goto_currentsystem = true; // if something has been scanned, return to current system

                // one solution to reset a form
                $form = new MoonForm();
            } else {
                $message['message'] = array('error' => 'your input has not been recognized as valid input');
            }
        }

        // create filters for listview out of get parameters and the user session parameters
        $filters = $this->vposManager->manageFilters($this->params()->fromQuery(), $this->eveDataManager, $goto_currentsystem);

        // fetch list of moons
        $vpos_list = $this->vposManager->vposList($filters);

        return new ViewModel(
            [
            'form' => $form,
            'vpos_list' => $vpos_list,
            'filters' => $filters,
            'filters_json' => \json_encode($filters),
            'message' => $message
            ]
        );
    }


    /**
     * Delete a Vpos and his belongings
     *
     * @return redirect to moon index page
     */
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $this->logger->debug('delete a vpos (' . $id . ')');
        $this->structureManager->removeStructure($id);

        return $this->redirect()->toRoute('vpos', ['action' => 'index']);
    }


    public function addSystemConnectionAction()
    {
        $structid = (int) $this->params()->fromQuery('structid', 0);
        $targetid = (int) $this->params()->fromQuery('targetid', 0);

        if (!empty($structid) && !empty($targetid)) {
            $this->structureManager->writeTargetToStructure($structid, $targetid);
        }

        return $this->redirect()->toRoute('vpos', ['action' => 'index']);
    }


    public function removeSystemConnectionAction()
    {
        $structid = (int)$this->params()->fromQuery('structid', 0);

        if (!empty($structid)) {
            $this->logger->debug('remmove system connection for AtStructureID: ' . $structid);
            $this->structureManager->writeTargetToStructure($structid, null);
        }

        return $this->redirect()->toRoute('vpos', ['action' => 'index']);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function fetchCoprporationStructuresConsole()
    {
        return $this->structureManager->esiFetchCoprporationStructures(true);
    }    
}
