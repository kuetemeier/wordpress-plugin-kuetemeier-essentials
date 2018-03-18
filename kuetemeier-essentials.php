<?php
/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * Plugin Name: Kuetemeier Essentials
 * Plugin URI: http://wordpress.org/extend/plugins/kuetemeier-essentials/
 * Description: Essential and fast addons for WordPress websites
 * Version: 0.1.0
 * Author: Jörg Kütemeier
 * Author URI: https://kuetemeier.de
 *
 * Text Domain: kuetemeier-essentials
 * Domain Path: /languages/
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 * License: Apache License, Version 2.0
 * License URI: http://www.apache.org/licenses/LICENSE-2.0
 *
 * @package    kuetemeier-essentials
 * @author     Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license    Apache License, Version 2.0
 * @link       https://kuetemeier.de
 * @copyright  2018 Jörg Kütemeier
 *
 * Copyright 2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/*
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/* define constants, use old style for php version check */
define( 'KUETEMEIER_ESSENTIALS_VERSION', '0.1.0' );
define( 'KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION', '5.0' );


/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 *
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function KuetemeierEssentials_noticePhpVersionWrong() {
		global $KuetemeierEssentials_minimalRequiredPhpVersion;
		echo '<div class="updated fade">' .
			__( 'Error: plugin "Kuetemeier Essentials" requires a newer version of PHP to be running.', 'kuetemeier-essentials' ) .
						'<br/>' . __( 'Minimal version of PHP required: ', 'kuetemeier-essentials' ) . '<strong>' . $KuetemeierEssentials_minimalRequiredPhpVersion . '</strong>' .
						'<br/>' . __( 'Your server\'s PHP version: ', 'kuetemeier-essentials' ) . '<strong>' . phpversion() . '</strong>' .
				 '</div>';
}


function KuetemeierEssentials_PhpVersionCheck() {
		global $KuetemeierEssentials_minimalRequiredPhpVersion;
	if ( version_compare( phpversion(), $KuetemeierEssentials_minimalRequiredPhpVersion ) < 0 ) {
			add_action( 'admin_notices', 'KuetemeierEssentials_noticePhpVersionWrong' );
			return false;
	}
		return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 *
 * @return void
 */
function KuetemeierEssentials_i18n_init() {
		$pluginDir = dirname( plugin_basename( __FILE__ ) );
		load_plugin_textdomain( 'kuetemeier-essentials', false, $pluginDir . '/languages/' );
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action( 'plugins_loadedi', 'KuetemeierEssentials_i18n_init' );

// check PHP Version requirements
if ( KuetemeierEssentials_CheckPHPVersion() ) {
		// Only load and run the init function if we know PHP version can parse it
		include_once 'kuetemeier-essentials_init.php';
		KuetemeierEssentials_init( __FILE__ );
}
