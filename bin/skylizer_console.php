<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));
require __DIR__ . '/../vendor/autoload.php';

use Application\Console\AllyCorpUpdateCommand;
use Application\Console\PriceUpdateCommand;
use Symfony\Component\Console\Application;
use Zend\Mvc\Application as ZendApplication;
use Zend\Stdlib\ArrayUtils;

$appConfig = require __DIR__ . '/../config/application.config.php';
//$appConfig = require __DIR__ . '/../config/autoload/console.global.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge(
        $appConfig,
        require __DIR__ . '/../config/development.config.php'
    );
}
$zendApplication = ZendApplication::init($appConfig);
$serviceManager = $zendApplication->getServiceManager();

$application = new Application('skylizer', '1.0.0');
$application->add(new PriceUpdateCommand($serviceManager));
$application->add(new AllyCorpUpdateCommand($serviceManager));

//$application->setDefaultCommand($command->getName(), true);
$application->run();
