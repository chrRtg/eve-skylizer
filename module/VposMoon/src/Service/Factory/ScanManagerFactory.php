<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use VposMoon\Service\ScanManager;

/**
 * Description of MoonManagerFactory
 *
 * @author chr
 */
class ScanManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ScanManager(
            $container->get(\VposMoon\Service\MoonManager::class),
            $container->get(\VposMoon\Service\CosmicManager::class),
            $container->get('MyLogger')
        );
    }

}
