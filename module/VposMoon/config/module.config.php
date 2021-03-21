<?php
namespace VposMoon;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\MoonController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'vposmoon' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/vposmoon[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\MoonController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            'vpos' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/vpos[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\VposController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
            'struct' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/struct[/:action[/:id]]',
                    'defaults' => [
                        'controller' => Controller\StructbrowseController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    // You can place additional routes that match under the
                    // route defined above here.
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\MoonController::class => Controller\Factory\MoonControllerFactory::class,
            Controller\VposController::class => Controller\Factory\VposControllerFactory::class,
            Controller\StructbrowseController::class => Controller\Factory\StructbrowseControllerFactory::class,
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            Controller\MoonController::class => [
                // Allow autorized users to visit "index"
                ['actions' => ['index', 'getSystemsJson', 'getCorporationsJson', 'editStructureJson', 'getStructureJson', 'deleteStructureJson'], 'allow' => '@'],
                // Give access to "delete" "moon.manage" permission.
                ['actions' => ['delete', 'dlMoonsCsv', 'priceUpdate'], 'allow' => '+moon.manage'],
            ],
            Controller\VposController::class => [
                // Allow autorized users to visit "index"
                ['actions' => ['index', 'delete', 'addSystemConnection', 'removeSystemConnection'], 'allow' => '@'],
                // Give access to "delete" "vpos.manage" permission.
                ['actions' => [], 'allow' => '+vpos.manage'],
            ],
            Controller\StructbrowseController::class => [
                // Allow autorized users to visit "index"
                ['actions' => ['index'], 'allow' => '@'],
            ],
        ],
    ],
    // This key stores configuration for RBAC manager.
    'service_manager' => [
        'factories' => [
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
            Service\MoonManager::class => Service\Factory\MoonManagerFactory::class,
            Service\VposManager::class => Service\Factory\VposManagerFactory::class,
            Service\ScanManager::class => Service\Factory\ScanManagerFactory::class,
            Service\CosmicManager::class => Service\Factory\CosmicManagerFactory::class,
            Service\MiningManager::class => Service\Factory\MiningManagerFactory::class,
            Service\StructureManager::class => Service\Factory\StructureManagerFactory::class,
            Service\StructurebrowserManager::class => Service\Factory\StructurebrowserManagerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'VposMoonModule' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\VposMoonHelper::class => View\Helper\Factory\VposMoonHelperFactory::class,
        ],
        'aliases' => [
            'vpViewTool' => View\Helper\VposMoonHelper::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver',
                ],
            ],
        ],
    ],
];
