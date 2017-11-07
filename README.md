# skŸlizer - The Eve-Online scan analyzer

skŸlizer is a tool for Eve Online to handle any kind of scans. At the moment only survey scans of moon are supported which has been introduced to the game 25th of October 2017.

skŸlizer is also a robust foundation to build any kind of EVE related tools on it. 

## Features (of the framework)

* EVE ESI (swagger) API interface
* Login is available only via EVE-SSO as a identity provider. 
* Allow login by EVE-Online player names, corporations or public access
* Deny access by EVE-Online player names 
* A granular role-rights-permission system to manage who is allowed to use what.
* User, Role and Permission administration interface
* set one player name as application adminstrator, also with the capability to add more administators
* PHP 7.0 or better* uses [Zend Framework 3](https://github.com/zendframework/zendframework)
* MySQL 5.6 or better
* based on the fantastic [Role Demo Sample from olegkrivtsov](https://github.com/olegkrivtsov/using-zf3-book-samples/tree/master/roledemo)
* uses some more libraries, see below in the *thank you* section.

## Installation

You need to have Apache 2.4 HTTP server, PHP v.7 or later with `gd` and `intl` extensions, and MySQL 5.6 or later.

Download skylizer to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies, in particular Zend Framework 3 and Doctrine 2.

If required adjust permissions for all files and directories:

```
sudo chown -R www-data:www-data data
sudo find ./ -type f -exec chmod 664 {} \;
sudo find ./ -type d -exec chmod 775 {} \;
```

### Database

Login to MySQL client:

```
mysql -u root -p
```

Create database:

```
CREATE DATABASE skylizer;
GRANT ALL PRIVILEGES ON skylizer.* TO skylizer@localhost identified by '<your_password>';
quit
```

Run database migrations to intialize database schema:

```
./vendor/bin/doctrine-module migrations:migrate
```

### Apache virtual host

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
	ServerName <your web server name, e.g. skylizer.your.domain.com>
    DocumentRoot /path/to/skylizer/public
    
    <Directory /path/to/skylizer/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

### Application Configuration

Create `config/autoload/local.php` config file by copying its distrib version:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

Edit **`config/autoload/local.php`** and set various parameters. Hint: you may look out for '// @' inside the file in order to identify where settings have to made.

> **Please mind:** below you'll find some configuration examples. Text in **arrow brackets** ('<' and '>') has to be replaced by your input. The arrow brackets have to be **removed** too.

#### EVE Online SSO access

First go to [EVE Online developers portal](https://developers.eveonline.com/applications) to obtain some crendentials. 
What you need to have available is:
> your webserver URL. Please add `/auth/index` to your URl in order to generate the *redirect URL*
> as scope please add `publicData` and `esi-location.read_location.v1`

```php
'eve_sso' => array (
	// @ generate a new EVE application at https://developers.eveonline.com/applications
	'clientId'          => '<generate it at https://developers.eveonline.com/applications>',
	'clientSecret'      => '<generate it at https://developers.eveonline.com/applications>',
	'redirectUri'       => '<your application base URL>**/auth/index',
	'scope' => [
		// @ while generating your EVE application take care to add these scopes
		'publicData', 
		'esi-location.read_location.v1'
	],
```

#### User & Corporation access

Please add at least your EVE Online player name right to ´admin´ to enable access to yourself. 
Then you may add some friends into ´user_allow´ or add your corporation (full name, not ticker!) into ´corp_allow´.
If you would like to allow anybody to access your application and to override any setting made in ´user_allow´ and ´corp_allow´ you may set ´allow_all´ to ´YeS´. Then please mind the upper and lower case!

```php
'auth' => [
	// @ Change to reflect your needs
	'allow_all' => 'no', // set to 'YeS' (mind the upper and lower case!) **to allow to any Eve-User to get access as a regular user**
	'admin' => ["<your player name>"],
	'corp_allow' => ["<your corporation>", "<another corporation>"],
	'user_allow' => ["<some player not in the corportions above", "<another player name>"],
	'user_deny' => ["<some spy in your corporation", "<another spy in your corporation>", "<your CEO's name (joke...)>"],
],
```

#### Database 

Please enter your database connection details and credentials.

```php
'doctrine' => [
	'connection' => [
		'orm_default' => [
			'driverClass' => PDOMySqlDriver::class,
			'params' => [
			// @ Change database connection to your needs
				'host'     => '<127.0.0.1>',
				'user'     => '<skylizer>',
				'password' => '<db-password>',
				'dbname'   => '<skylizer>',
			]
		],
	], 
],
```

#### Logging

You may change the logging level. By default only errors or critical errors are logged.
In case you wan to get more or less information the supported logging levels are: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO and DEBUG. 
To change the log level set ´priority´ from ´Logger::ERR´ e.g. to ´Logger::DEBUG´ to switch to a log level of DEBUG - which is very noisy.
Logfiles are beeing stored in ´./data/log/´, one file each day. Please log-rotate them to avoid exceeding your hard drive capacity.

```php
	'log' => [
        'MyLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
					// @ Change loglevel to your needs, recommended Logger::ERR
					// supported levels: EMERG, ALERT, CRIT, ERR, WARN, NOTICE, INFO, DEBUG
                    'priority' => Logger::ERR,
                    'options' => [
                        'stream' => './data/log/atlog_'.date('ymd').'.log',
                    ],
                ],
            ],
        ],
	],
```

### GO!

Now you should be able to see the skylizer website by visiting the link "http://localhost/". 
 
## Contributing

If you found a mistake or a bug please get in touch via 
* Eve ingame mail to 'Rtg Quack'
* via Eve online Forum thread about syklizer


## Development

You're invited to add modules or enhance functionality to skŸlizer or even build your own application on it. 
I'm happy to support you or to speed up end enhance the developemnt as part of a team.
We also may discuss via EVE ingame mail, TS or even better discord.

To enable development mode:

```
php composer.phar development-enable
```

## License

This code is provided under the [Apache License 2.0](https://choosealicense.com/licenses/apache-2.0/). 

## Thank you (to be extended)
* to Oleg Krivtsov [Using Zend Framework 3](https://github.com/olegkrivtsov/using-zend-framework-3-book). Thanks Oleg & respect to your great effort!
* to OG for teaching me doctrine 2 and more
* [EveLabs](https://github.com/EvELabs/oauth2-eveonline) for the  Oauth Library
* [SeAT](https://github.com/eveseat/eseye) for the ESI interface
* to [xell network seven](http://evemaps.dotlan.net/corp/xell_network_seven) and [V.e.G.A.](http://evemaps.dotlan.net/alliance/V.e.G.A.)* flying with them since some years
