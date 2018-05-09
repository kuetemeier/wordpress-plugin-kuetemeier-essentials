# WordPress Plugin Kuetemeier Essentials#

Description: WordPress PlugIn with essential extensions for speed, data privacy and optimization.

## Version Information ##

#### This source code ####
Version: 0.6.4-beta

#### Latest stable ####
Latest stable version: not released yet

#### WordPress Version ####

Requires at least: 4.9
Tested up to: 4.9.5

#### PHP Version ####

Minimum PHP Version: 5.6
(tests are only written for 7.0 and later) 

## A LITTLE WARNING FOR DEVELOPERS ##

Version informations (and some others) are centralized in `package.json`. Gulp will replace it in many other files (e.g. in this file, readme.txt and kuetemeier-essentials.php).
See gulp.task('replace') in gulpfile.js for more informations. You have been warned ;-).

## Requirements for development: ##

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

Install the dependencies of the gulp:

```bash
$ npm install-dev
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
$ gulp
```

Watch the project:

```bash
$ gulp watch
```

Deploy with svn:

```bash
$ gulp deploy
```

## Changelog ##

##### 0.1.0 #####

* Initial version.

## License: ##

License: GNU General Public License 3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
