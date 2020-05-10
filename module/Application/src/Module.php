<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\MvcEvent;
use Zend\Session\SessionManager;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
    
    /**
     * This method is called once the MVC bootstrapping is complete.
     */
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one to avoid passing the
        // session manager as a dependency to other models.
        $serviceManager->get(SessionManager::class);

        // for Eve Online set TZ to UTC, same as EVE-ESI uses on his own
        \date_default_timezone_set('UTC');
    }
}
