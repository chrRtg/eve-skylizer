<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Laminas\Paginator',
    'Laminas\ZendFrameworkBridge',
    'Laminas\Serializer',
    'DoctrineModule',
    'DoctrineORMModule',
    'Laminas\Cache',
    'Laminas\I18n',
    'Laminas\InputFilter',
    'Laminas\Filter',
	'Laminas\Log',
    'Laminas\Hydrator',
    'Laminas\Session',
    'Laminas\Mvc\Plugin\Prg',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Mvc\Plugin\FlashMessenger',
    'Laminas\Mvc\Plugin\FilePrg',
    'Laminas\Form',
    'Laminas\Router',
    'Laminas\Validator',
	'CurrentRoute',
    'Application',
    'User',
	'VposMoon'
];
