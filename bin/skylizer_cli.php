<?php

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

//echo('CLI chdir to: __'.dirname(__DIR__).'__');

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\Application as ZendApplication;
use Zf3SymfonyConsole\Controller\Plugin\ConsoleParams;
use VposMoon\Controller\MoonController;

$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
	$appConfig = ArrayUtils::merge(
			$appConfig, require __DIR__ . '/../config/development.config.php'
	);
}
$zendApplication = ZendApplication::init($appConfig);
$serviceManager = $zendApplication->getServiceManager();

(new Application('skylizer price-update application'))
	->register('price')
	->setDescription("Fetch updated prices from EVE servers")
	->setCode(function(InputInterface $input, OutputInterface $output) use ($serviceManager) {

		$serviceManager->get('ControllerPluginManager')->setService('params', new ConsoleParams($input));

		$controller = $serviceManager->get('ControllerManager')->get(MoonController::class);

		
		echo ("Running skylizer price-update application" . PHP_EOL);
		echo ("please wait, may take a while ..." . PHP_EOL);
		$cnt = $controller->priceUpdateConsole();
		echo ("updated $cnt prices." . PHP_EOL);	})
	->getApplication()
//	->setDefaultCommand('echo', true)
	->run();
