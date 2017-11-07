Annotare - Eve Notepad for Scanners
==================================================

This is tbd

## Installation

You need to have Apache 2.4 HTTP server, PHP v.7 or later with `gd` and `intl` extensions, and MySQL 5.6 or later.

Download skylizer to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies, in particular Zend Framework 3 and Doctrine 2.

Enable development mode:

```
php composer.phar development-enable
```

If required adjust permissions for all files and directories:

```
sudo chown -R www-data:www-data data
sudo find ./ -type f -exec chmod 664 {} \;
sudo find ./ -type d -exec chmod 775 {} \;
```

Create `config/autoload/local.php` config file by copying its distrib version:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

Edit `config/autoload/local.php` and set various parameters. You may look out for '// @' inside the file in order to identify where settings have to made.

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

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/skylizer/public
    
    <Directory /path/to/skylizer/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the skylizer website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug please get in touch via 
 - Eve ingame mail to 'Rtg Quack'
 - via Eve online Forum thread about syklizer
