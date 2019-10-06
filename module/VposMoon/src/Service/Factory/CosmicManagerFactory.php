<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use VposMoon\Service\CosmicManager;

/**
 * Description of MoonManagerFactory
 *
 * @author chr
 */
class CosmicManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $logger = $container->get('MyLogger');

        return new CosmicManager(
            $entityManager, 
            $container->get(\User\Service\EveSSOManager::class),
            $container->get(\Application\Service\EveDataManager::class),
            $container->get(\VposMoon\Service\StructureManager::class),
            $logger
        );
    }

}
