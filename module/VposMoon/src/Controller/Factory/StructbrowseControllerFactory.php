<?php

namespace VposMoon\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use VposMoon\Controller\StructbrowseController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class StructbrowseControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StructbrowseController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\VposMoon\Service\StructurebrowserManager::class),
            $container->get(\VposMoon\Service\VposManager::class),
            $container->get(\Application\Service\EveDataManager::class),
            $container->get('MyLogger')
        );
    }

}
