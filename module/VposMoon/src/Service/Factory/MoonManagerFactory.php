<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Session\Container;
use VposMoon\Service\MoonManager;

/**
 * Description of MoonManagerFactory
 *
 * @author chr
 */
class MoonManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $sessionManager = $container->get(SessionManager::class);
        $sessionContainer = new Container('eve_user', $sessionManager);

    
        return new MoonManager(
            $sessionContainer,
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\User\Service\EveSSOManager::class),
            $container->get(\Application\Service\EveEsiManager::class),
            $container->get('MyLogger')
        );
    }

}
