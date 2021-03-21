<?php

namespace VposMoon\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use VposMoon\Controller\VposController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class VposControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new VposController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\VposMoon\Service\VposManager::class),
            $container->get(\VposMoon\Service\StructureManager::class),
            $container->get(\VposMoon\Service\ScanManager::class),
            $container->get(\Application\Service\EveDataManager::class),
            $container->get('MyLogger')
        );
    }

}
