<?php

namespace VposMoon\Controller;

use VposMoon\Entity\AtMoon;
use VposMoon\Form\MoonForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * The MoonController is about Moons, Survey Scans and Moon Goo
 */
class MoonController extends AbstractActionController
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
    public function __construct($entityManager, $moonManager, $cosmicManager, $scanManager, $eveDataManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->moonManager = $moonManager;
        $this->cosmicManager = $cosmicManager;
        $this->scanManager = $scanManager;
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
                // store input to text file, one file per user per day
                file_put_contents('./data/storage/' . date('ymd') . '_' . $this->currentUser()->getEveUserid() . '.txt', escapeshellcmd ($data['scan']) . PHP_EOL, FILE_APPEND);

                // process the scan
                $pc_res = $this->scanManager->processScan($data['scan'], $this->currentUser()->getEveUserid());
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

        return new ViewModel(
            [
                'form' => $form,
                'moon_list' => $moon_list,
                'filters' => $filters,
                'filters_json' => \json_encode($filters),
                'message' => $message,
            ]
        );
    }

    /**
     * Initiate a Price Update
     *
     * @see \Application\Service\EveDataManager::updatePrices()
     *
     * @return ViewModel
     */
    public function priceUpdateAction()
    {
        $cnt = $this->eveDataManager->updatePrices();

        return new ViewModel(
            [
                'cnt' => $cnt,
            ]
        );
    }

    /**
     * Initiate a Price Update from Console Application
     *
     * @see \Application\Service\EveDataManager::updatePrices()
     *
     * @return ViewModel
     */
    public function priceUpdateConsole()
    {
        return $this->eveDataManager->updatePricesFromEveAPI();
    }
    
    /**
     * Initiate a Price Update via Evepraisal from Console Application
     *
     * @see \Application\Service\EveDataManager::updatePrices()
     *
     * @return ViewModel
     */
    public function priceEpUpdateConsole()
    {
        return $this->eveDataManager->updatePricesFromEvepraisal();
    }

    /**
     * Fetch Alliance and Corporation Information from ESI.
     *
     * To be called from the shell
     *
     * @param  bool $force
     * @param  char $mode  a for alliance, c for corporation or b for both
     * @return string    Information about the actions taken
     */
    public function allyCorpUpdateConsole($force = false, $mode = false)
    {
        return $this->eveDataManager->updateAllyCorp($force, $mode);
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
            'it.typename',
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
     * Create or edit a structure.
     *
     * A structure consist of the eve-type, a celestial (moon, planet, sun, gate, station) the structure is nearby,
     * the owning corporation and the player given name of the structure
     *
     * @todo support more structures than moons
     * @todo support distance to celestial
     *
     * @return JsonModel
     */
    public function editStructureJsonAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine("Content-Type", "application/json");
        // Checks if the request is valid
        if (!$request->isPost() || !$request->isXmlHttpRequest()) {
            $response->setContent(Json::encode(['error' => ['Very bad request']]));
            return $response;
        }

        $params = $this->params()->fromPost();

        // @ todo : add/edit spec for any kind of structure
        // $this->logger->debug('editStructureAction() ' . print_r($params, true));

        if (isset($params['structureid'])) {
            // @todo this logic belongs to the ScanManager

            $structure_data = $this->cosmicManager->getStructureArray();

            $structure_data['scantype'] = 'STRUCT';
            $structure_data['atstructure_id'] = (!empty($params['structureid']) ? $params['structureid'] : 0);
            $structure_data['eve_typeid'] = (!empty($params['struct_item_id']) ? $params['struct_item_id'] : 0);
            $structure_data['celestial_id'] = (!empty($params['moonid']) ? $params['moonid'] : 0);
            $structure_data['celestial_distance'] = 5000;
            $structure_data['structure_name'] = (trim($params['struct_name']) ? trim($params['struct_name']) : '');
            $structure_data['corporation_id'] = (!empty($params['owning_corp']) ? $params['owning_corp'] : 0);

            if ($structure_data['eve_typeid']) {
                $invtype_entity = $this->entityManager->getRepository(\Application\Entity\Invtypes::class)->findOneByTypeid($structure_data['eve_typeid']);
                $structure_data['eve_groupid'] = $invtype_entity->getGroupid()->getGroupid();
            }

            // Any required data available, now we can access the structure
            $res = $this->cosmicManager->writeStructure($structure_data);

            return new JsonModel(
                [
                    'status' => 'SUCCESS',
                    'items' => $res,
                    'moonid' => (int) $params['moonid'],
                ]
            );
        }

        return new JsonModel(
            [
                'error' => 'Moon not found or not given',
            ]
        );
    }

    /**
     * Fetch the data about a structure by ID
     *
     * Expects from GET or POST a variable 'q' with the structure ID.
     *
     * @return JsonModel
     */
    public function getStructureJsonAction()
    {
        $query = $this->params()->fromQuery('q');

        if (!empty($query)) {
            $res = $this->eveDataManager->getStructureById($query);

            if (!empty($res)) {
                return new JsonModel(
                    [
                        'status' => 'SUCCESS',
                        'items' => $res,
                    ]
                );
            }

            return new JsonModel(
                [
                    'status' => 'EMPTY',
                ]
            );
        }

        return new JsonModel(
            [
                'status' => 'FAIL',
            ]
        );
    }

    /**
     * Remove a structure
     *
     * Expects from GET or POST a variable 'id' with the structure ID.
     *
     * @return void
     */
    public function deleteStructureJsonAction()
    {
        $query = $this->params()->fromQuery('id');

        if (!empty($query)) {
            $res = $this->cosmicManager->removeStructure($query);

            return new JsonModel(
                [
                    'status' => 'SUCCESS',
                ]
            );
        }

        return new JsonModel(
            [
                'status' => 'FAIL',
            ]
        );
    }

    /**
     * Find a corporation by a part of her name or ticker
     *
     * Expects from GET or POST a variable 'q' with the string partitial to search for.
     *
     * @return JsonModel
     */
    public function getCorporationsJsonAction()
    {
        $limit = 25;
        $query = $this->params()->fromQuery('q');

        if (!empty($query)) {
            $res = $this->eveDataManager->getCorporationByPartial($query);

            if (!empty($res)) {
                return new JsonModel(
                    [
                        'status' => 'SUCCESS',
                        'items' => $res,
                    ]
                );
            }

            return new JsonModel(
                [
                    'status' => 'EMPTY',
                ]
            );
        }

        return new JsonModel(
            [
                'status' => 'FAIL',
            ]
        );
    }

    /**
     * Fetch a list of systems and constellations from map by a search term.
     *
     * Expects from GET or POST a variable 'q' with the string partitial to search for.
     *
     * @return JsonModel
     */
    public function getSystemsJsonAction()
    {
        $limit = 10;
        $query = $this->params()->fromQuery('q');

        // $this->logger->debug('fetch System (' . $query . ')');

        if (!empty($query)) {
            $res = $this->eveDataManager->getSystemByPartial($query);

            if (!empty($res)) {
                return new JsonModel(
                    [
                        'status' => 'SUCCESS',
                        'items' => $res,
                    ]
                );
            }

            return new JsonModel(
                [
                    'status' => 'EMPTY',
                ]
            );
        }

        return new JsonModel(
            [
                'status' => 'FAIL',
            ]
        );
    }

    /**
     * Delete a Moon and his Goo
     *
     * Expects from GET or POST a variable 'id' with the moon ID.
     *
     * @return redirect to moon index page
     */
    public function deleteAction()
    {
        $moonid = (int) $this->params()->fromRoute('id', 0);
        $this->logger->debug('someeone removed the goo from moon (' . $moonid . ')');

        $this->moonManager->deleteGoo($moonid);

        return $this->redirect()->toRoute('vposmoon', ['action' => 'index']);
    }
}
