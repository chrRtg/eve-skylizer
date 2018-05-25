<?php
namespace VposMoon;

use Zend\Router\Http\Segment; 
use Zend\Router\Http\Literal;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\MoonController::class => InvokableFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'vposmoon' => [
                'type'    => Segment::class,
                'options' => [
                    // Change this to something specific to your module
                    'route'    => '/vposmoon[/:action[/:id]]',
                    'defaults' => [
                        'controller'    => Controller\MoonController::class,
                        'action'        => 'index',
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
        ],
    ],		
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            Controller\MoonController::class => [
                // Allow autorized users to visit "index"
                ['actions' => ['index', 'getSystemsJson', 'editStructure'], 'allow' => '@'],
                // Give access to "delete" "moon.manage" permission.
                ['actions' => ['delete', 'dlMoonsCsv', 'priceUpdate'], 'allow' => '+moon.manage']
            ],
        ]
    ],
    // This key stores configuration for RBAC manager.
    'service_manager' => [
        'factories' => [
            Service\NavManager::class => Service\Factory\NavManagerFactory::class,
			Service\MoonManager::class => Service\Factory\MoonManagerFactory::class,
			Service\CosmicManager::class => Service\Factory\CosmicManagerFactory::class,
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
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
