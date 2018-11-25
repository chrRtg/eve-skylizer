<?php

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Application\Service\EveEsiManager;

/**
 * Description of MoonManagerFactory
 *
 * @author chr
 */
class EveEsiManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // use the same session container as User\Service\Factory\EveSSOManagerFactory to get access to 
        // the eve-sso related information
        $sessionManager = $container->get(SessionManager::class);
        $sessionContainer = new Container('eve_sso', $sessionManager);
        
        return new EveEsiManager(
            $sessionContainer,
            $container->get('MyLogger')
        );
    }

}
