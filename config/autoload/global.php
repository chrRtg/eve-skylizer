<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Cache\Storage\Adapter\Filesystem;

return [
    // Session configuration.
    'session_config' => [
        'cookie_lifetime'     => 60*60*24*7, // Session cookie will expire in 7 days
        'gc_maxlifetime'      => 60*60*24*30, // How long to store session data on server (for 1 month).        
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    // Cache configuration.
    'caches' => [
        'FilesystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => 'data/cache',
                    // Store cached data for 1 hour.
                    'ttl' => 60*60*1 
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [                        
                    ],
                ],
            ],
        ],
    ],
    'doctrine' => [        
        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
        'configuration' => [
            'orm_default' => [
				'datetime_functions' => [
					'date' => 'DoctrineExtensions\Query\Mysql\Date',
					'date_format' => 'DoctrineExtensions\Query\Mysql\DateFormat',
					'dateadd' => 'DoctrineExtensions\Query\Mysql\DateAdd',
					'datediff' => 'DoctrineExtensions\Query\Mysql\DateDiff',
					'day' => 'DoctrineExtensions\Query\Mysql\Day',
					'dayname' => 'DoctrineExtensions\Query\Mysql\DayName',
					'last_day' => 'DoctrineExtensions\Query\Mysql\LastDay',
					'minute' => 'DoctrineExtensions\Query\Mysql\Minute',
					'second' => 'DoctrineExtensions\Query\Mysql\Second',
					'strtodate' => 'DoctrineExtensions\Query\Mysql\StrToDate',
					'time' => 'DoctrineExtensions\Query\Mysql\Time',
					'timestampadd' => 'DoctrineExtensions\Query\Mysql\TimestampAdd',
					'timestampdiff' => 'DoctrineExtensions\Query\Mysql\TimestampDiff',
					'week' => 'DoctrineExtensions\Query\Mysql\Week',
					'weekday' => 'DoctrineExtensions\Query\Mysql\WeekDay',
					'year' => 'DoctrineExtensions\Query\Mysql\Year',
				],
				'numeric_functions' => [
					'acos'  => 'DoctrineExtensions\Query\Mysql\Acos',
					'asin' => 'DoctrineExtensions\Query\Mysql\Asin',
					'atan2' => 'DoctrineExtensions\Query\Mysql\Atan2',
					'atan' => 'DoctrineExtensions\Query\Mysql\Atan',
					'cos' => 'DoctrineExtensions\Query\Mysql\Cos',
					'cot' => 'DoctrineExtensions\Query\Mysql\Cot',
					'hour' => 'DoctrineExtensions\Query\Mysql\Hour',
					'pi' => 'DoctrineExtensions\Query\Mysql\Pi',
					'power' => 'DoctrineExtensions\Query\Mysql\Power',
					'quarter' => 'DoctrineExtensions\Query\Mysql\Quarter',
					'rand' => 'DoctrineExtensions\Query\Mysql\Rand',
					'round' => 'DoctrineExtensions\Query\Mysql\Round',
					'sin' => 'DoctrineExtensions\Query\Mysql\Sin',
					'std' => 'DoctrineExtensions\Query\Mysql\Std',
					'tan' => 'DoctrineExtensions\Query\Mysql\Tan',
				],
				'string_functions' => [
					'binary' => 'DoctrineExtensions\Query\Mysql\Binary',
					'char_length' => 'DoctrineExtensions\Query\Mysql\CharLength',
					'concat_ws' => 'DoctrineExtensions\Query\Mysql\ConcatWs',
					'countif' => 'DoctrineExtensions\Query\Mysql\CountIf',
					'crc32' => ' DoctrineExtensions\Query\Mysql\Crc32',
					'degrees' => 'DoctrineExtensions\Query\Mysql\Degrees',
					'field' => 'DoctrineExtensions\Query\Mysql\Field',
					'find_in_set' => 'DoctrineExtensions\Query\Mysql\FindInSet',
					'group_concat' => 'DoctrineExtensions\Query\Mysql\GroupConcat',
					'ifelse' => 'DoctrineExtensions\Query\Mysql\IfElse',
					'ifnull' => 'DoctrineExtensions\Query\Mysql\IfNull',
					'match_against' => 'DoctrineExtensions\Query\Mysql\MatchAgainst',
					'md5' => 'DoctrineExtensions\Query\Mysql\Md5',
					'month' => 'DoctrineExtensions\Query\Mysql\Month',
					'monthname' => 'DoctrineExtensions\Query\Mysql\MonthName',
					'nullif' => 'DoctrineExtensions\Query\Mysql\NullIf',
					'radians' => 'DoctrineExtensions\Query\Mysql\Radians',
					'regexp' => 'DoctrineExtensions\Query\Mysql\Regexp',
					'replace' => 'DoctrineExtensions\Query\Mysql\Replace',
					'sha1' => 'DoctrineExtensions\Query\Mysql\Sha1',
					'sha2' => 'DoctrineExtensions\Query\Mysql\Sha2',
					'soundex' => 'DoctrineExtensions\Query\Mysql\Soundex',
					'uuid_short' => 'DoctrineExtensions\Query\Mysql\UuidShort',
				]
            ]
        ]			
    ],
];
