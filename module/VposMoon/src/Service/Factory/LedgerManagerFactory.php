<?php

namespace VposMoon\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use VposMoon\Service\LedgerManager;

/**
 * Description of StructureManagerFactory
 *
 * @author chr
 */
class LedgerManagerFactory
{

    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LedgerManager(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get('MyLogger')
        );
    }

}
