<?php
/**
 * Application Plugin Factory
 * To create Application Plugin by injecting config array
 */
namespace Application\Controller\Plugin\Factory;

use Application\Controller\Plugin\LoggerPlugin;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoggerPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return (new LoggerPlugin($container));
    }
}
