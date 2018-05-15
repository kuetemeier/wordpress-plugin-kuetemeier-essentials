# WordPress Plugin Kuetemeier Essentials#

Description: WordPress PlugIn with usefull extensions for speed, data privacy and optimization.

## Version Information ##

#### This source code ####
Version: 1.2.2

#### Latest stable ####
Latest stable version: 1.2.2

#### WordPress Version ####

Requires at least: 4.9
Tested up to: 4.9.5

#### PHP Version ####

Minimum PHP Version: 5.6
(tests are only written for 7.0 and later) 

## A LITTLE WARNING FOR DEVELOPERS ##

Version informations (and some others) are centralized in `package.json`. Gulp will replace it in many other files
(e.g. in this file, readme.txt and kuetemeier-essentials.php).
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

WARNING: Do NOT install the dev dependencies locally on this repo, as they would get included into the
plugin-distribution.zip and make it much much bigger - and worst: slower. Composer would try to autoload all dev
dependencies in production. Instead install them globally.
You would also save some disk space if you develeop more than one PHP application or plugin.

```bash
$ composer update --no-dev

$ composer global require squizlabs/php_codesniffer
$ composer global require wp-coding-standards/wpcs
$ composer global require phpdocumentor/phpdocumentor
$ composer global require phpunit/phpunit
$ composer global require wimg/php-compatibility
```

If you do not have it already, add `~/.composer/vendor/bin/` to your `PATH`, e.g. add

```bash
export PATH=~/.composer/vendor/bin/:$PATH
```

to your `.profile` file on Mac or Linux.

Register Rules to `phpcs`:

```bash
$ phpcs --config-set installed_paths ~/.composer/vendor/wimg/php-compatibility
$ phpcs -i
```

To verify that the new rules have been added, we can ask PHP CodeSniffer to report to us the sets of rules that it
currently has available. In the Terminal, enter the following command:

```bash
$ vendor/bin/phpcs -i
```

You should see:

```bash
The installed coding standards are PEAR, Zend, PSR2, MySource, Squiz, PSR1 and PHPCompatibility
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

## License: ##

License: GNU General Public License 3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
