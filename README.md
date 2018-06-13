# skŸlizer - The Eve-Online scan analyzer

skŸlizer is a tool for Eve Online to handle any kind of scans. 

At the moment only survey scans of moon and storing mining structures is supported, more to come.

## Features

* [for the impatient - a screenshot](https://raw.githubusercontent.com/chrRtg/eve-skylizer/master/public/img/skylizer_screenshot_1.jpg)
* read Moon scan data from Eve Online. Press below the scan results "copy to clipboard", then navigate to skŸlizer and paste the scan to the area to the right and then the button "submit your scan" below the area.
* search by Systemname or Constellation with autosuggest, just start typing
* easily navigate to neighbor systems, constellation or show all scanned moons in the current constellation
* show the composition, amount and value of the goo for each moon
* show the composition, amount and value of the refined minerals ("Ore") for each moon
* filter by goo or refined minerals
* order by value (which moon is the R64 equivalent)
* polls current prices from Eve via ESI call
* Help Function
* for Moon-Managers (specific right in the tool) create CSV exports
* and much more to be come...

In the future skŸlizer will become a tool to share moon, scan and dscan results and to combine the scan results. It will e.g. show after a scan which kind of structure has been anchored on a moon or somewhere else.

## [PUBLIC Demo of skŸlizer](https://skylizer.eve-tools.info)

Data available in the public demo for: 3QE-9Q, Alparena, Daras, F-TE1T, Hakonen, HM-UVD, L-C3O7, Reschard, Taisy and ZOYW-O, see [PUBLIC Demo of skŸlizer](https://skylizer.eve-tools.info)

Feel free to add your scans (or some from pastebin) to the tool. 

## Install and Update

Please have a look into the wiki to understand how to install and maintain skŸlizer:

* [Changelog](/wiki/Changelog)
* [Update the application](/wiki/Updates)
* [How to install](/wiki/Install)

## Features of the framework (for developers)

skŸlizer is also a robust foundation to build any kind of EVE related tools on it. 

- EVE ESI (swagger) API interface
- Login is available only via EVE-SSO as a identity provider. 
- Allow login by EVE-Online player names, corporations or public access
- Deny access by EVE-Online player names 
- A granular role-rights-permission system to manage who is allowed to use what.
- User, Role and Permission administration interface
- set one player name as application administrator, also with the capability to add more administrators
- uses [Zend Framework 3](https://github.com/zendframework/zendframework)
- based on the fantastic [Role Demo Sample from olegkrivtsov](https://github.com/olegkrivtsov/using-zf3-book-samples/tree/master/roledemo)
- uses some more libraries, see below in the *thank you* section.

## Contributing

If you found a mistake or a bug please get in touch via 
* Eve ingame mail to 'Rtg Quack'
* via Eve online Forum thread about skŸlizer

## License

This code is provided under the [Apache License 2.0](https://choosealicense.com/licenses/apache-2.0/). 

## Requirements

- Webserver, e.g. Apache
- PHP 7.1 or later with `gd` and `intl` extensions
- MySQL 5.6 or later.

## Thank you (to be extended)

* to Oleg Krivtsov [Using Zend Framework 3](https://github.com/olegkrivtsov/using-zend-framework-3-book). Thanks Oleg & respect to your great effort!
* to OG for teaching me doctrine 2 and more
* [EveLabs](https://github.com/EvELabs/oauth2-eveonline) for the  Oauth Library
* [SeAT](https://github.com/eveseat/eseye) for the ESI interface
* and of course to [xell network seven](http://evemaps.dotlan.net/corp/xell_network_seven) and [V.e.G.A.](http://evemaps.dotlan.net/alliance/V.e.G.A.), flying with them since some years

