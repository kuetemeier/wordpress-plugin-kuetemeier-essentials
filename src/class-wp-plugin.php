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


require_once plugin_dir_path( __FILE__ ) . '/config.php';


/**
 * Builds the base of a plugin, gives references to Options and Modules
 *
 * @see Options Options of a plugin
 * @see Modules Modules of a plugin
 *
 * @since 0.1.11
 */
abstract class WP_Plugin {


	/**
	 * The plugin version number.
	 *
	 * @var string
	 *
	 * @since 0.1.11
	 */
	private $version = 'unknown';


	/**
	 * The last known stable version of the plugin.
	 *
	 * @var string
	 *
	 * @since 0.1.11
	 */
	private $version_stable = 'unknown';


	/**
	 * Holding a vaild instance.
	 *
	 * @var WP_Plugin
	 *
	 * @since 0.1.11
	 */
	private static $instance = null;

	/**
	 * Instance of the Modules Class.
	 *
	 * @var Modules
	 *
	 * @since 0.1.11
	 */
	private $modules;


	/**
	 * Instance of the Options Class.
	 *
	 * @var Options
	 *
	 * @since 0.1.11
	 */
	private $options;


	/**
	 * Initialize the plugin, load frontend modules and prepare backend modules.
	 *
	 * @param string  $version Actual version Number of this plugin.
	 * @param string  $version_stable The Number of the last known stable version (may equal `$version`).
	 * @param Options $options A fresh initilized Options object.
	 * @param Modules $modules A fresh initilized Modules object.
	 *
	 * @since 0.1.11
	 */
	public function __construct( $version, $version_stable, $options, $modules ) {
		$this->version = $version;
		$this->version_stable = $version_stable;

		$this->options = $options;
		$this->modules = $modules;

		$this->modules()->init_frontend_prepare_backend();

		$this->options()->init_admin_hooks();
	}


	/**
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @param string $self Class name to be instanciated.
	 *
	 * @return WP_Plugin A valid WP_Plugin instance
	 *
	 * @since 0.1.11
	 */
	public static function instance( $self = '' ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new $self();
			do_action( 'kuetemeier_wp_plugin_loaded', self::$instance );
		}
		return self::$instance;
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Don\'t clone me!', 'kuetemeier-essentials' ), esc_attr( $this->version() ) );
	}


	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'No wake up please!', 'kuetemeier-essentials' ), esc_attr( $this->version() ) );
	}


	/**
	 * Get the instance of the Modules class.
	 *
	 * @return Modules    A valid instance of the Module class.
	 *
	 * @since 0.1.11
	 */
	public function modules() {
		return $this->modules;
	}


	/**
	 * Get the instance of the Options class.
	 *
	 * @return Options    A valid instance of the Options class.
	 *
	 * @since 0.1.11
	 */
	public function options() {
		return $this->options;
	}


	/**
	 * Checks if this plugin is based on a known stable version.
	 *
	 * Hint: this may not be the 'last' stable verstion.
	 *
	 * @return  bool True if it is a stable version, false otherwise.
	 *
	 * @since 0.1.11
	 */
	public function is_stable() {
		return ( version_compare( $this->version, $this->version_stable ) === 0 );
	}


	/**
	 * Returns the slug of admin menu page for the given module (or the default admin slug).
	 *
	 * @param string $plugin_module_id (optional) A valid id of a Plugin_Module.
	 *
	 * @return string Default admin menu page slug.
	 *
	 * @since 0.2.1
	 */
	public function get_admin_page_slug( $plugin_module_id = '' ) {

		if ( ! isset( $plugin_module_id ) ) {
			wp_die( 'FATAL ERROR: Something is wrong, \$plugin_module_id is not set' );
		}

		trim( $plugin_module_id );

		if ( empty( $plugin_module_id ) ) {
			// const from config.php
			return Config\ADMIN_PAGE_SLUG;
		} else {
			return sanitize_text_field( Config\ADMIN_PAGE_SLUG . '-' . $plugin_module_id );
		}
	}


	/**
	 * Returns the base key for the WordPress option table.
	 *
	 * The key of the WordPress Option table is in the column `option_name`.
	 * The default value for the key should be a lowercase version of the Plugin name.
	 *
	 * @return string Database key for the WordPress options table.
	 *
	 * @since 0.2.1
	 */
	public function get_db_option_table_base_key() {
		// const from config.php
		return Config\DB_OPTION_TABLE_BASE_KEY;
	}


}
