<?php
namespace VposMoon\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use VposMoon\View\Helper\VposMoonHelper;
use VposMoon\Service\MoonManager;


class VposMoonHelperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $eveDataManager = $container->get(\Application\Service\EveDataManager::class);
        $moonManager = $container->get(\VposMoon\Service\MoonManager::class);
        
        return new VposMoonHelper($eveDataManager, $moonManager, $container->get('Config'));
    }
}

