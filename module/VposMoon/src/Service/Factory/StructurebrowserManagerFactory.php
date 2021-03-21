<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use VposMoon\Service\StructurebrowserManager;

/**
 * Description of StructureManagerFactory
 *
 * @author chr
 */
class StructurebrowserManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StructurebrowserManager(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('MyLogger')
        );
    }

}
