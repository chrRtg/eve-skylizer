<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use User\Controller\AuthController;
use User\Service\EveSSOManager;

/**
 * This is the factory for EveSSOController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        return new AuthController(
				$container->get('doctrine.entitymanager.orm_default'), 
				$container->get(EveSSOManager::class),
				$container->get('MyLogger')
			);
    }
}
