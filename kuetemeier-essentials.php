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

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );


/********************************************************
 * Define constants, use old style for php version check
 */
define( 'KUETEMEIER_ESSENTIALS_NAME', 'Kuetemeier Essentials' );
define( 'KUETEMEIER_ESSENTIALS_VERSION', '0.1.0' );
define( 'KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION', '5.6' );


/***************************************
 * Helper functions for initialisation
 */

/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 *
 * @return void
 */
function kuetemeier_essentials_i18n_init() {

	$_plugin_dir = dirname( plugin_basename( __FILE__ ) );
	load_plugin_textdomain( 'kuetemeier-essentials', false, $_plugin_dir . '/languages/' );

}

/**
 * Hook function, called by WordPress (not to be called directly)
 * Display an error notice to the admin area
 *
 * @return void
 */
function kuetemeier_essentials_hook_display_admin_notice() {

	printf( '<div class="error fade">' .
		/* translators: %1$s Plugin Version */
		esc_html__( 'Error: Plugin "%s" requires a newer version of PHP.', 'kuetemeier-essentials' ) . '<br/>' .
		esc_html__( 'Minimal PHP version required:', 'kuetemeier-essentials' ) .
		' <strong>' . esc_html( KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION ) . '</strong><br/>' .
		esc_html__( 'Current PHP version running on this server:', 'kuetemeier-essentials' ) .
		' <strong>' . esc_html( phpversion() ) . '</strong>' .
		'</div>',
		esc_html( KUETEMEIER_ESSENTIALS_NAME )
	);
}

/**
 * Checks the PHP version against the required version
 *
 * @return boolean true if requirements are met, false otherwise
 */
function kuetemeier_essentials_is_php_version_requirements_fulfilled() {

	if ( version_compare( phpversion(), KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION ) < 0 ) {
		add_action( 'admin_notices', 'kuetemeier_essentials_hook_display_admin_notice' );
		return false;
	}
	return true;
}


/************************
 * Plugin initialization
 */

// Initialize i18n.
add_action( 'plugins_loaded', 'kuetemeier_essentials_i18n_init' );

// Check PHP version requirements.
if ( kuetemeier_essentials_is_php_version_requirements_fulfilled() ) {
}
