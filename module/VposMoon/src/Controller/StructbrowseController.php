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
class StructbrowseController extends AbstractActionController
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
     * @var VposMoon\Service\StructurebrowserManager
     */
    private $tructurebrowserManager;

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
    public function __construct($entityManager, $structurebrowserManager, $vposManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->structurebrowserManager = $structurebrowserManager;
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

        $request = $this->getRequest();
        $action = $this->params()->fromQuery('cardaction');

        // create filters for listview out of get parameters and the user session parameters
        $filters = $this->vposManager->manageFilters($this->params()->fromQuery(), $this->eveDataManager, $goto_currentsystem);

        if (!empty($action) && $request->isXmlHttpRequest()) {
            $template = null;

            switch ($action) {
                case 'moongoo':
                    $template = 'vpos-moon/structbrowse/partial/moongoo';
                    break;
                case 'fuel':
                    $template = 'vpos-moon/structbrowse/partial/fuel';
                    break;
                case 'timers':
                    $template = 'vpos-moon/structbrowse/partial/timers';
                    break;
                case 'nodrill':
                    $template = 'vpos-moon/structbrowse/partial/nodrill';
                    break;
                default:
                    return('action not know');
                    break;
            }

            // fetch list of structures according to the filter
            $struct_list = $this->structurebrowserManager->structuresList(null, null, $action);

            $view = new ViewModel(['struct_list' =>  $struct_list]);
            $view->setTemplate($template);
            $view->setTerminal(true);

            return $view;
        }

        return new ViewModel(
            [
            'filters' => $filters,
            'filters_json' => \json_encode($filters),
            'message' => $message
            ]
        );
    }
}
