<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use VposMoon\Service\MiningManager;

/**
 * Description of MiningManagerFactory
 *
 * @author chr
 */
class MiningManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new MiningManager(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\Application\Service\EveEsiManager::class),
            $container->get('MyLogger')
        );
    }
}
