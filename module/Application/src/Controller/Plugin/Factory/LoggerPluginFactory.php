<?php
/** 
 * Application Plugin Factory 
 * To create Application Plugin by injecting config array
 */
namespace Application\Controller\Plugin\Factory;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Application\Controller\Plugin\LoggerPlugin;

class LoggerPluginFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $plugin = new LoggerPlugin($container);
    
        return $plugin;
    }    
}
?>