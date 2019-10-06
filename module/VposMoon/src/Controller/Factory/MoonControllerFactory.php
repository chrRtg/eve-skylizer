<?php

namespace VposMoon\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use VposMoon\Controller\MoonController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class MoonControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new MoonController(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\VposMoon\Service\MoonManager::class),
            $container->get(\VposMoon\Service\StructureManager::class),
            $container->get(\VposMoon\Service\ScanManager::class),
            $container->get(\Application\Service\EveDataManager::class),
            $container->get('MyLogger')
        );
    }

}
