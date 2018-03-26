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
require_once( dirname(__FILE__) . '/class-options.php' );

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

	protected $_options;

	/**
	 * Main Kueteemier_Essentials Instance
	 * Ensures only one instance of Kuetemeier_Essentials is loaded or can be loaded.
	 *
	 * @return Kuetemeier_Essentials Kuetemeier_Essentials instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			do_action( 'kuetemeier_essentials_loaded', self::$_instance );
		}
		return self::$_instance;
	}

	function __construct() {

		// order is important! Options BEFORE Modules!
		$this->_options = new Options();
		do_action( 'kuetemeier_essentials_options_loaded' );

		$this->_modules = new Modules( $this->_options );
		do_action( 'kuetemeier_essentials_modules_loaded' );

		$this->_options->init_admin_hooks();

		// add_action( 'admin_init', array( &$this, '_callback_admin_init' ) );
	}

/*
	public function _callback_admin_init() {

	}
*/

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

	public function get_modules() {
		return $this->_modules;
	}

}
