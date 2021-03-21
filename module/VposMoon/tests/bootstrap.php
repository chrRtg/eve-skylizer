<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/VposMoonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

$additionalNamespaces = $additionalModulePaths = $moduleDependencies = null;

$rootPath = realpath(dirname(__DIR__));
$testsPath = "$rootPath/tests";

if (is_readable($testsPath . '/TestConfiguration.php')) {
    include_once $testsPath . '/TestConfiguration.php';
} else {
    include_once $testsPath . '/TestConfiguration.php.dist';
}

$path = array(
    ZF2_PATH,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

require_once  'Zend/Loader/AutoloaderFactory.php';
require_once  'Zend/Loader/StandardAutoloader.php';

use Laminas\Loader\AutoloaderFactory;
use Laminas\Loader\StandardAutoloader;

// setup autoloader
AutoloaderFactory::factory(
    array(
        'Laminas\Loader\StandardAutoloader' => array(
            StandardAutoloader::AUTOREGISTER_ZF => true,
            StandardAutoloader::ACT_AS_FALLBACK => false,
            StandardAutoloader::LOAD_NS => $additionalNamespaces,
        )
    )
);

// The module name is obtained using directory name or constant
$moduleName = pathinfo($rootPath, PATHINFO_BASENAME);
if (defined('MODULE_NAME')) {
    $moduleName = MODULE_NAME;
}

// A locator will be set to this class if available
$moduleTestCaseClassname = '\\'.$moduleName.'Test\\Framework\\TestCase';

// This module's path plus additionally defined paths are used $modulePaths
$modulePaths = array(dirname($rootPath));
if (isset($additionalModulePaths)) {
    $modulePaths = array_merge($modulePaths, $additionalModulePaths);
}

// Load this module and defined dependencies
$modules = array($moduleName);
if (isset($moduleDependencies)) {
    $modules = array_merge($modules, $moduleDependencies);
}


$listenerOptions = new Laminas\ModuleManager\Listener\ListenerOptions(array('module_paths' => $modulePaths));
$defaultListeners = new Laminas\ModuleManager\Listener\DefaultListenerAggregate($listenerOptions);
$sharedEvents = new Laminas\EventManager\SharedEventManager();
$moduleManager = new \Laminas\ModuleManager\ModuleManager($modules);
$moduleManager->getEventManager()->setSharedManager($sharedEvents);
$moduleManager->getEventManager()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

if (method_exists($moduleTestCaseClassname, 'setLocator')) {
    $config = $defaultListeners->getConfigListener()->getMergedConfig();

    $di = new \Laminas\Di\Di;
    $di->instanceManager()->addTypePreference('Laminas\Di\LocatorInterface', $di);

    if (isset($config['di'])) {
        $diConfig = new \Laminas\Di\Config($config['di']);
        $diConfig->configure($di);
    }

    $routerDiConfig = new \Laminas\Di\Config(
        array(
            'definition' => array(
                'class' => array(
                    'Laminas\Mvc\Router\RouteStackInterface' => array(
                        'instantiator' => array(
                            'Laminas\Mvc\Router\Http\TreeRouteStack',
                            'factory'
                        ),
                    ),
                ),
            ),
        )
    );
    $routerDiConfig->configure($di);

    call_user_func_array($moduleTestCaseClassname.'::setLocator', array($di));
}

// When this is in global scope, PHPUnit catches exception:
// Exception: Laminas\Stdlib\PriorityQueue::serialize() must return a string or NULL
unset($moduleManager, $sharedEvents);
