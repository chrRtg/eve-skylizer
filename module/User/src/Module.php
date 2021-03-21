<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractActionController;
use User\Controller\AuthController;
use User\Service\EveSSOManager;

class Module
{
    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners. 
     */
    public function onBootstrap(MvcEvent $event)
    {
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method. 
        $sharedEventManager->attach(
            AbstractActionController::class, 
            MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100
        );
    }
    
    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user 
     * to the login page.
     */
    public function onDispatch(MvcEvent $event)
    {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);
        
        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        
        // Get the instance of EveSSOManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(EveSSOManager::class);
        
        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        if ($controllerName!=AuthController::class) {
            $result = $authManager->filterAccess($controllerName, $actionName);
            
            if ($result==EveSSOManager::AUTH_REQUIRED) {
                // Redirect the user to the "Login" page.
                return $controller->redirect()->toRoute('index');
            }
            else if ($result==EveSSOManager::ACCESS_DENIED) {
                // Redirect the user to the "Not Authorized" page.
                return $controller->redirect()->toRoute('auth', ['action' => 'notauthorized']);
            }
        }
    }
}
