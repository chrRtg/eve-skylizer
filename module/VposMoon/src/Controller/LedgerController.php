<?php

namespace VposMoon\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Uri\Uri;
use VposMoon\Form\MoonForm;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use VposMoon\Entity\AtMoon;

/**
 * The MoonController is about Moons, Survey Scans and Moon Goo
 */
class LedgerController extends AbstractActionController
{

    /**
     * Entity manager.
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Moon manager.
     *
     * @var VposMoon\Service\VposManager
     */
    private $vposManager;

    /**
     * Moon manager.
     *
     * @var VposMoon\Service\LedgerManager
     */
    private $ledgerManager;

    /**
     *
     * @var \Application\Service\EveDataManager
     */
    private $eveDataManager;


    /**
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($entityManager, $ledgerManager, $vposManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->ledgerManager = $ledgerManager;
        $this->vposManager = $vposManager;
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

        // create filters for listview out of get parameters and the user session parameters
        $filters = $this->vposManager->manageFilters($this->params()->fromQuery(), $this->eveDataManager, $goto_currentsystem);

        $ledger = $this->ledgerManager->getLedgerStructures();

        return new ViewModel(
            [
            'filters' => $filters,
            'ledger' => $ledger
            ]
        );
    }

    public function chartJsonAction()
    {
        $structid = $this->params()->fromQuery('s');

        $data = $this->ledgerManager->getLedgerPerDay($structid ? $structid : 0);
        $range = $this->ledgerManager->getLedgerMinMaxDate();

        $this->logger->debug('fetch struct:__'.$structid.'__  : ' . print_r($data,true));

        if (!empty($data)) {
            return new JsonModel(
                [
                    'status' => 'SUCCESS',
                    'data' => $data,
                    'range' => $range
                ]
            );
        }

        return new JsonModel(
            [
                'status' => 'EMPTY',
            ]
        );
        return false;
    }
}
