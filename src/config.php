<?php
/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package    kuetemeier-essentials
 * @author     Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license    GNU General Public License 3
 * @link       https://kuetemeier.de
 * @copyright  2018 Jörg Kütemeier
 *
 *
 * Copyright 2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Kuetemeier_Essentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

// DON'T TOUCH THIS - AUTOREPLACED BY GULP - YOU HAVE BEEN WARNED.
const PLUGIN_VERSION = '0.1.12-alpha';
// DON'T TOUCH THIS - AUTOREPLACED BY GULP - YOU HAVE BEEN WARNED.
const PLUGIN_VERSION_STABLE = 'not released yet';

/**
 * List of available modules, that will be registered by Modules
 *
 * Hint: If you write an additional module, you have to register it here.
 *
 * @see Modules
 */
const AVAILABLE_MODULES = array(
	'core'         => 'Core',
	'data-privacy' => 'Data_Privacy',
	'develop'      => 'Develop',
);

const CORE_OPTION_PAGE_CAPABILITY = 'administrator';
const CORE_OPTION_PAGE_SLUG = 'kuetemeier_essentials';

const CORE_OPTION_SETTINGS_KEY = 'kuetemeier_essentials';

const DATA_PRIVACY_OPTION_PAGE_SLUG = 'kuetemeier_essentials_data_privacy';
const DATA_PRIVACY_OPTION_SETTINGS_KEY = 'kuetemeier_essentials_data_privacy';
