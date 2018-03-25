<?php
/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package    kuetemeier-essentials
 * @author     Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license    Apache License, Version 2.0
 * @link       https://kuetemeier.de
 * @copyright  2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)
 *
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

namespace Kuetemeier_Essentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once( dirname(__FILE__) . '/class-modules.php' );

/**
 * Class Kuetemeier_Essentials
 */
class Kuetemeier_Essentials {

	/**
	 * Kuetemeier_Essentials instance.
	 *
	 * @var Kuetemeier_Essentials
	 */
	protected static $_instance = null;

	/**
	 * The plugin version number.
	 *
	 * @var string
	 */
	public $_version = KUETEMEIER_ESSENTIALS_VERSION;

	protected $_modules;

	/**
	 * Main Kueteemier_Essentials Instance
	 * Ensures only one instance of Kuetemeier_Essentials is loaded or can be loaded.
	 *
	 * @return Kuetemeier_Essentials Kuetemeier_Essentials instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function __construct() {

		$this->_modules = new Modules();

		$this->_modules->init_all_frontend_modules();

		$this->_modules->init_all_admin_modules();

	}

	/**
	 * Send a debug message to the browser console.
	 *
	 * @param Object $data Data to be outputted to console.
	 */
	public function debug_to_console( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			echo( '<script>console.log( "' .
				esc_html( KUETEMEIER_ESSENTIALS_NAME ) . ': "' .
				wp_json_encode( $data ) . '" );</script>' );
		} else {
			echo( '<script>console.log( "' .
				esc_html( KUETEMEIER_ESSENTIALS_NAME ) .
				': ' . esc_html( $data ) . '" );</script>' );
		}
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Don\'t clone me!' ), esc_attr( $this->_version ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'No wake up please!' ), esc_attr( $this->_version ) );
	}

	public function _callback_settings_dashboard_common( $args ) {
		$names = $this->_modules->foreach_frontend( 'get_name' );
		$name_list = join( ', ', $names);
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Number of loaded modules', 'kuetemeier_essentials' );
			echo ': '.$this->_modules->count();
			?></p>
			<p><?php esc_html_e( 'Module names:', 'kuetemeier_essentials' );
		echo ': '.$name_list;
		?></p></div>
		<?php

	}

	/**
	 * custom option and settings
	 */
	public function settings_init() {


		// register a new setting for "wporg" page
		register_setting( 'kuetemeier_essentials', 'kuetemeier_essentials' );

		add_settings_section(
			'kuetemeier_essentials_dashboard',
			__( 'Allgemeines', 'kuetemeier_essentials' ),
			array( &$this, '_callback_settings_dashboard_common' ),
			'kuetemeier_essentials'
	 	);

		$this->_modules->foreach_frontend( 'settings_init' );
	}



	public function admin_init() {

		$this->settings_init();

	}



	/**
	 * top level menu
	 */
	public function options_page() {
		// add top level menu page
		add_menu_page(
			'Kuetemeier - Essentials', // page title
			'Kuetemeier - Essentials', // menu title
			'manage_options', // capability
			'kuetemeier_essentials', // menu slug
			array( &$this, 'options_page_html' ) // function
			// icon https://developer.wordpress.org/reference/functions/add_menu_page/
		);

		add_submenu_page(
			'kuetemeier_essentials', // parent slug
			'Kuetemeier-Essentials - Dashboard', // page title
			'Dashbaord', // menu title
			'administrator', // capability
			'kuetemeier_essentials', // menu slug
			array( &$this, 'options_page_html' ) // function
		);

		add_submenu_page(
			'kuetemeier_essentials',
			'Display Options',
			'Display Options',
			'administrator',
			'sandbox_theme_display_options',
			'sandbox_theme_display'
		);
	}


	/**
	 * top level menu:
	 * callback functions
	 */
	function options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// add error/update messages

		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
			add_settings_error( 'kuetemeier_essentials_messages', 'kuetemeier_essentials_message', __( 'Settings Saved', 'kuetemeier_essentials' ), 'updated' );
		}

		// show error/update messages
		settings_errors( 'kuetemeier_essentials_messages' );
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
		<?php
		// output security fields for the registered setting "wporg"
		settings_fields( 'kuetemeier_essentials' );
		// output setting sections and their fields
		// (sections are registered for "wporg", each field is registered to a specific section)
		do_settings_sections( 'kuetemeier_essentials' );
		// output save settings button
		submit_button( 'Save Settings' );
		?>
		</form>
		</div>
		<?php
	}

}
