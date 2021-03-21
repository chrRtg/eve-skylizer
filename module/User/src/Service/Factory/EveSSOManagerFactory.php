<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Session\Container;
use User\Service\UserManager;
use User\Service\EveSSOManager;
use User\Service\RbacManager;

class EveSSOManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        // Instantiate dependencies.
        $sessionManager = $container->get(SessionManager::class);
        $sessionContainer = new Container('eve_sso', $sessionManager);
        
        return new EveSSOManager(
            $container->get(\Laminas\Authentication\AuthenticationService::class), 
            $container->get(RbacManager::class), 
            $sessionContainer, 
            $container->get('Config'), 
            $container->get(UserManager::class),
            $container->get(\Application\Service\EveEsiManager::class),
            $container->get('MyLogger')
        );
    }
}
