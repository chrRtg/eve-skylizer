<?php
namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Application\Service\EveDataManager;
use Laminas\ServiceManager\Factory\FactoryInterface;

class EveDataManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        return new EveDataManager(
            $container->get('doctrine.entitymanager.orm_default'),
            $container->get(\Application\Service\EveEsiManager::class),
            $container->get('Config'),
            $container->get('MyLogger')
        );
    }
}
