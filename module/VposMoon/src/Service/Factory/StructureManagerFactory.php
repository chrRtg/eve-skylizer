<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use VposMoon\Service\StructureManager;

/**
 * Description of StructureManagerFactory
 *
 * @author chr
 */
class StructureManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StructureManager(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\User\Service\UserManager::class),
            $container->get(\Application\Service\EveEsiManager::class),
            $container->get(\User\Service\EveSSOManager::class),
            $container->get(\VposMoon\Service\MiningManager::class),
            $container->get('MyLogger')
        );
    }
}
