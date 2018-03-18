# WordPress Plugin Kuetemeier Essentials #

This is the WordPress Plugin "Kuetemeier Essentials". More info at https://kuetemeier.de

## Requirements: ##

* [Node.js](http://nodejs.org/)
* [Compass](http://compass-style.org/)
* [GIT](http://git-scm.com/)
* [Subversion](http://subversion.apache.org/)
* [Composer](https://getcomposer.org/)

## Installation: ##

Clone this repo:

```bash
$ git clone git@github.com:kuetemeiernet/kuetemeier-essentials.git
```

Install the dependencies of the grunt:

```bash
$ npm install
```

Install the dependencies with composer:

```bash
$ composer update
```

Install WordPress Coding Standards:

```bash
$ vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs
```

To verify that the new rules have been added, we can ask PHP CodeSniffer to report to us the sets of rules that it currently has available. In the Terminal, enter the following command:

```bash
$ vendor/bin/phpcs -i
```

You should see:

```bash
The installed coding standards are PEAR, Zend, PSR2, MySource, Squiz, PSR1, WordPress-VIP, WordPress, WordPress-Extra, WordPress-Docs and WordPress-Core
```


## Commands: ##

Lint, compile and compress the files:

```bash
$ grunt
```

Watch the project:

```bash
$ grunt watch
```

Deploy with svn:

```bash
$ grunt deploy
```

## Changelog ##

##### 0.1.0 #####

* Initial version.

## License: ##

Copyright 2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
