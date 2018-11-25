<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Service\EveSSOManager;



/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController
{
    /**
     * Entity manager.
     *
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager;
    
    private $eveSsoManager;


    private $logger;
    
    /**
     * Constructor.
     */
    public function __construct($entityManager, $eveSsoManager, $logger)
    {
        $this->entityManager = $entityManager;
        $this->eveSsoManager = $eveSsoManager;
        $this->logger = $logger;
    }
    
    /**
     * Authenticates user against EVE SSO
     */
    public function indexAction()
    {
        $this->logger->debug('Auth indexAction');
        
        $res = $this->eveSsoManager->eveSsoLogin($this->params());
    
        switch ($res) {
        case 'viewuser':
            return $this->redirect()->toRoute('index');
                break;
        case 'auth_fail':
            //return $this->redirect()->toRoute('auth', array('action'=>'notauthorized'));
            $msg = $this->eveSsoManager->getMessage();
            if(!$msg) {
                $msg = 'SSO error: unknown problem';
            }
            break;
        case 'invalid_state':
                $msg = 'SSO error: invalid state';
            break;
        case 'usr_error':
                $msg = 'SSO error: login successful, but not able to fetch user details';
            break;
        case 'unknown':
                $msg = "SSO error: unknown error. (sorry, don't know what failed but if it's a repeatable error I'm happy to look after him";
            break;
        }
        
        
        // if we reach this point, no auth is possible or allowed. Therefore properly set back the session
        $this->eveSsoManager->clearIdentity();
                
        return new ViewModel(
            array(
            'msg' => $msg
            )
        );
    }

    /**
     * EVE SSO Logout Action
     * 
     * @return ViewModel
     */
    public function logoutAction()
    {
        
        $this->eveSsoManager->clearIdentity();
        return $this->redirect()->toRoute('home', array('action'=>'index'));
    }
    
    public function notAuthorizedAction()
    {
        $this->logger->debug('NOT AUTH action');
        $this->getResponse()->setStatusCode(403);
        return new ViewModel(array());
    }
}
