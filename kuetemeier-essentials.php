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
define( 'KUETEMEIER_ESSENTIALS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


/***************************************
 * Helper functions for initialisation
 */

/**
 * Initialize internationalization (i18n) for this plugin.
 * WordPress Hook, not called directly.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 *
 * @return void
 *
 * @since 1.0.0
 */
function kuetemeier_essentials_hook_i18n_init() {
	load_plugin_textdomain( 'kuetemeier-essentials', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

/**
 * Display an error notice to the admin area
 * WordPress Hook, not called directly.
 *
 * @return void
 *
 * @since 1.0.0
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
 * If not met, displays an error to the admin area.
 *
 * @return boolean true if requirements are met, false otherwise
 *
 * @since 1.0.0
 */
function kuetemeier_essentials_is_php_version_requirements_fulfilled() {

	if ( version_compare( phpversion(), KUETEMEIER_ESSENTIALS_MINIMAL_PHP_VERSION ) < 0 ) {
		add_action( 'admin_notices', 'kuetemeier_essentials_hook_display_admin_notice' );
		return false;
	}
	return true;
}

/**
 * This code runs during plugin activation.
 * WordPress Hook, not called directly.
 *
 * @return void
 *
 * @since 1.0.0
 */
function kuetemeier_essentials_activation_hook() {
	include_once plugin_dir_path( __FILE__ ) . 'src/admin/class-activator.php';
	\Kuetemeier_Essentials\admin\Activator::activate();
}

/**
 * This code runs during plugin deactivation.
 * WordPress Hook, not called directly.
 *
 * @return void
 *
 * @since 1.0.0
 */
function kuetemeier_essentials_deactivation_hook() {
	include_once plugin_dir_path( __FILE__ ) . 'src/admin/class-deactivator.php';
	\Kuetemeier_Essentials\admin\Deactivator::deactivate();
}


function kuetemeier_essentials_callback_admin_init() {
	register_activation_hook( __FILE__, 'kuetemeier_essentials_activation_hook' );
	register_deactivation_hook( __FILE__, 'kuetemeier_essentials_deactivation_hook' );
}

/**
 * Plugin initialization
 *
 * Everything is registered via hooks and should not effect page life cycle.
 *
 * @since 1.0.0
 */
function kuetemeier_essentials_init() {

	// Initialize i18n.
	add_action( 'plugins_loaded', 'kuetemeier_essentials_hook_i18n_init' );

	// Check PHP version requirements.
	if ( kuetemeier_essentials_is_php_version_requirements_fulfilled() ) {

		// register activation and deactivation hooks
		add_action( 'admin_init', 'kuetemeier_essentials_callback_admin_init' );

		// Everything O.K., let's go! Include the main Kuetemeier_Essentials class.
		if ( ! class_exists( \Kuetemeier_Essentials\Kuetemeier_Essentials::class ) ) {
			include_once KUETEMEIER_ESSENTIALS_PLUGIN_DIR . '/src/class-kuetemeier-essentials.php';
		}

		// Initialize plugin.
		\Kuetemeier_Essentials\Kuetemeier_Essentials::instance();
	}
}

kuetemeier_essentials_init();
