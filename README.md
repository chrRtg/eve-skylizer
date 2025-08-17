# skŸlizer - The Eve-Online scan analyzer and Structures Manager

---
**NOTE**

While I do not play Eve Online since 2023 I do not work any longer on this project. If you plan to take the project over and you have specific questions please feel free to reach out to me.

---

skŸlizer is a tool for Eve Online to handle any kind of scans and to gather as much as possible data from any Scan or Dscan.

skŸlizer add information about your corporations structures and visualize the mining ledgers.

For up to date information please have a look into the [Changelog](https://github.com/chrRtg/eve-skylizer/wiki/Changelog).

## Features

1. **Scan something in EVE & share your findings with your mates**
2. **copy'n paste your scan to skŸlizer**
3. **view & filter by value and ore - find the ISK** (or your next targets)
4. **view & filter by anomalies**
5. **Import and view Mining Ledgers - show them with efficacy grapsh in the new Ledger-View**
6. **Structures Dashboard - show timers, fuel due, chunk due and inactive drills**

* Identify the valuable moons (ISK!) in a system or the whole constellation
* How to use: copy moon scan data from Eve Online. Press below the scan results "copy to clipboard", then navigate to skŸlizer and paste the scan to the area to the right and then the button "submit your scan" below the area.
* **search** by system-name or constellation, just start typing - autosuggest fill the gaps
* easily **navigate** to neighbour systems, a constellation or show all scanned moons in the current constellation
* add structures, owning corporation and the structure name or some notes to any moon
* Help Functions

### Moon Scans

* show the **composition, amount and ISK for the goo** for each moon
* show the **composition, amount and ISK of the refined minerals ("Ore")** for each moon
* filter by goo or refined minerals
* order by value (which moon is the R64 equivalent?)
* polls **daily prices from Eve** via ESI call
* for Moon-Managers (specific right in the tool) create CSV exports

![skylizer-moon](https://raw.githubusercontent.com/wiki/chrRtg/eve-skylizer/img/skylizer_moon.png)

### Structures Scans (Directional Scanner)

* just cut'n paste your scan
* get a list with all citadels, engineering complex, refineries and towers
* Refineries are connected whith the moon scans immediately
* also get the postions next to a planet, moon, station or stargate with their distance
* player given names are detected and stored
* edit details like ownership and name
* automatic replacement of refineries if type or name has changed

### Anomaly Scans (Probe Scanner)

* just cut'n paste your scan
* get a overview by anomaly type
* automatic removal and improvement on each scan you enter
* links to description of anomaly in English and German
* add wormhole targets by systems name or flat (high, low, 0.0, WH)
* filter by type of anomaly (gas & ore, exploration, combat, faction, wormhole and structures)

![skylizer-scan](https://raw.githubusercontent.com/wiki/chrRtg/eve-skylizer/img/skylizer_scan.png)

In the future skŸlizer will become a tool to share moon, scan and dscan results and to combine the scan results. It will e.g. show after a scan which kind of structure has been anchored on a moon or somewhere else.

## [PUBLIC Demo of skŸlizer](https://skylizer.eve-tools.info)

Data available in the public demo for:

* 3QE-9Q
* Alparena
* Daras
* F-TE1T
* Hakonen
* and some more like HM-UVD,  L-C3O7, Reschard, Taisy and ZOYW-O

Feel free to add your scans (or some from pastebin) to the tool.

## Install and Update

Please have a look into the wiki to understand how to install and maintain skŸlizer:

* [Changelog](https://github.com/chrRtg/eve-skylizer/wiki/Changelog)
* [Update the data](https://github.com/chrRtg/eve-skylizer/wiki/Update-Data)
* [Update the application](https://github.com/chrRtg/eve-skylizer/wiki/Update-Application)
* [How to install](https://github.com/chrRtg/eve-skylizer/wiki/Install)

## Features of the framework (for developers)

skŸlizer is also a robust foundation to build any kind of EVE related tools on it.

* Code quality and security check: https://sonarcloud.io/dashboard?id=chrRtg_eve-skylizer
* EVE ESI (swagger) API interface
* Login is available only via EVE-SSO as a identity provider.
* Allow login by EVE-Online player names, corporations or public access
* Deny access by EVE-Online player names
* A granular role-rights-permission system to manage who is allowed to use what.
* User, Role and Permission administration interface
* set one player name as application administrator, also with the capability to add more administrators
* uses [Zend Framework 3](https://github.com/zendframework/zendframework)
* based on the fantastic [Role Demo Sample from olegkrivtsov](https://github.com/olegkrivtsov/using-zf3-book-samples/tree/master/roledemo)
* uses some more libraries, see below in the *thank you* section.

## Contributing

If you found a mistake or a bug please get in touch via

* Eve ingame mail to 'Rtg Quack'
* via Eve online Forum thread about skŸlizer

## License

This code is provided under the [Apache License 2.0](https://choosealicense.com/licenses/apache-2.0/).

## Requirements

* Webserver, e.g. Apache
* PHP 7.4 or better with `gd` and `intl` extensions
* MySQL 5.6 or better.

## Thank you (to be extended)

* the people who made [Laminas - open-source continuation of Zend Framework](https://getlaminas.org) 
* to Oleg Krivtsov [Using Zend Framework 3](https://github.com/olegkrivtsov/using-zend-framework-3-book). Thanks Oleg & respect to your great effort!
* to OG for teaching me doctrine 2 and more
* [EveLabs](https://github.com/EvELabs/oauth2-eveonline) for the  Oauth Library
* [SeAT](https://github.com/eveseat/eseye) for the ESI interface
* and of course to [xell network seven](http://evemaps.dotlan.net/corp/xell_network_seven) and [V.e.G.A.](http://evemaps.dotlan.net/alliance/V.e.G.A.), proud member and flying with them since some years
