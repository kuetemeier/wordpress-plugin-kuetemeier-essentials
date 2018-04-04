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


/**
 * Manages frontend and admin Modules
 *
 * The modules are registered in `conifg.php` and autoloaded by this class.
 * If WordPress is called in the frontend ONLY frontend modueles will be loaded.
 * Admin modules are loaded in the `admin_init` and `admin_menu` callback.
 *
 * The class will automatically instantiated by `Kuetemeier_Essentials`, so
 * there should no need to instantiate it by yourself.
 *
 * @see Kuetemeier_Essentials              Managing Class
 * @see Frontend\Module\Frontend_Module    Fontend Modul
 * @see Admin\Module\Admin_Module          Admin Modul
 * @see AVAILABLE_MODULES                  List of available modules
 * @since 0.1.0
 */
final class Modules {

	/**
	 * Array of all registered modules.
	 *
	 * Holds to arrays:
	 *
	 * ```
	 * $modules[ admin_class ]     list of initilized admin module classes
	 * $modules[ frontend_class ]  list of initilized frontend module classes
	 * ```
	 *
	 * @see Frontend\Module\Frontend_Module
	 * @see Admin\Module\Admin_Module
	 *
	 * @var array [<description>]
	 * @since  0.1.0
	 */
	private $modules = array();


	/**
	 * Indicates if the frontend modules have been loaded
	 *
	 * @var boolean
	 * @since  0.1.0
	 */
	private $frontend_classes_loaded = false;


	/**
	 * Indicates if the admin modules have been loaded
	 *
	 * @var boolean
	 * @since  0.1.0
	 */
	private $admin_classes_loaded = false;


	/**
	 * List of available modules, that will be registered
	 *
	 * This is defined in 'config.php'
	 *
	 * @var  array
	 * @since  0.1.0
	 */
	private $available_modules = array();


	/**
	 * Initialize all frontend modules and register callbacks to initialize all admin modules.
	 *
	 * This class will be initialized once from the class `Kuetemeier_Essentials`
	 *
	 * @param WP_Plugin $wp_plugin WP_Plugin, that is instanciation this class.
	 * @param array     $available_modules A list of available modules. See `config.php`.
	 *
	 * @return   void
	 * @see Kuetemeier_Essentials
	 * @since  0.1.0
	 */
	public function __construct( $wp_plugin, $available_modules ) {
		$this->wp_plugin = $wp_plugin;
		$this->available_modules = $available_modules;
	}

	/**
	 * Init all registered frontend classes and set the hooks for admin inititialization.
	 *
	 * This method shloud be called once, from the WP_Plugin class, AFTER the Options
	 * constructor has been called and a valid instance is registered in WP_Plugin.
	 *
	 * @return void
	 * @since 0.1.11
	 *
	 * @see Modules::callback_admin_menu__init_all_admin_modules_for_admin_menu()
	 * @see Modules::callback_admin_menu__init_all_admin_modules_for_admin_init()
	 * @see WP_Plugin
	 * @see Options
	 */
	public function init_frontend_prepare_backend() {
		$this->init_all_frontend_classes();

		add_action( 'admin_menu', array( &$this, 'callback_admin_menu__init_all_admin_modules_for_admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'callback_admin_init__init_all_admin_modules_for_admin_init' ) );
	}

	/**
	 * Returns a list of available modules.
	 *
	 * @return array
	 *
	 * @since 0.1.11
	 */
	public function available_modules() {
		return $this->available_modules;
	}


	/**
	 * Callback to be run by 'admin_init', ensures registered admin classes are loaded.
	 *
	 * @internal Registered to WordPress within constructor.
	 * @return  void
	 * @since 0.1.0
	 */
	public function callback_admin_init__init_all_admin_modules_for_admin_init() {
		$this->ensure_admin_classes_are_loaded();
		$this->foreach_admin( 'callback_admin_init' );
	}


	/**
	 * Callback to be run by 'admin_menu', ensures registered admin classes are loaded.
	 *
	 * @internal Registered to WordPress withing constructor.
	 * @return  void
	 * @since 0.1.0
	 */
	public function callback_admin_menu__init_all_admin_modules_for_admin_menu() {
		$this->ensure_admin_classes_are_loaded();
		$this->foreach_admin( 'callback_admin_menu' );
	}


	/**
	 * Ensure all admin classes are loaded. If not, init them.
	 *
	 * @return  void
	 * @since  0.1.0
	 * @see Admin\Module\Admin_Module          Admin Modul
	 */
	private function ensure_admin_classes_are_loaded() {
		if ( ! $this->admin_classes_loaded ) {
			$this->init_all_admin_classes();
		}
	}


	/**
	 * Ensure all frontend classes are loaded. If not, init them.
	 *
	 * @return  void
	 * @since  0.1.0
	 * @see Frontend\Module\Frontend_Module    Fontend Modul
	 */
	private function init_all_frontend_classes() {
		foreach ( array_keys( $this->available_modules() ) as $module_id ) {
			$this->init_module_frontend_class( $module_id );
		}
		$this->frontend_classes_loaded = true;
	}


	/**
	 * Initialize all registered admin moduels (classes).
	 *
	 * @return  void
	 * @since  0.1.0
	 * @see Admin\Module\Admin_Module          Admin Modul
	 */
	private function init_all_admin_classes() {
		foreach ( array_keys( $this->available_modules() ) as $module_id ) {
			$this->init_module_admin_class( $module_id );
		}
		$this->admin_classes_loaded = true;
	}


	/**
	 * Initialize a single, registered frontend modul (the class of the module).
	 *
	 * Valid keys are defined in `AVAILABLE_MODULES`.
	 *
	 * @param string $module_id Key of the module to be registered.
	 *
	 * @return  void
	 * @since  0.1.0
	 * @see Admin\Module\Admin_Module    Admin Modul.
	 * @see AVAILABLE_MODULES            Quelle für gültige keys.
	 */
	private function init_module_frontend_class( $module_id ) {
		require_once dirname( __FILE__ ) . '/frontend/module/class-' . $module_id . '-frontend.php';
		$class_name = 'Kuetemeier_Essentials\\Frontend\Module\\' . $this->available_modules()[ $module_id ] . '_Frontend';
		$this->set_frontend_class( $module_id, new $class_name( $this->options() ) );
	}


	/**
	 * Initialize a single, registered admin modul (the class of the module).
	 *
	 * Valid keys are defined in `AVAILABLE_MODULES`.
	 *
	 * @param string $module_id Key of the module to be registered.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 * @see Admin\Module\Admin_Module    Admin Modul.
	 * @see AVAILABLE_MODULES            Quelle für gültige keys.
	 */
	private function init_module_admin_class( $module_id ) {
		require_once dirname( __FILE__ ) . '/admin/module/class-' . $module_id . '-admin.php';
		$class_name = 'Kuetemeier_Essentials\\Admin\Module\\' . $this->available_modules()[ $module_id ] . '_Admin';
		$this->set_admin_class( $module_id, new $class_name( $this->options() ) );
	}

	/**
	 * Register a frontend class.
	 *
	 * @param string                          $module_id      Valid id key of a module.
	 * @param Frontend\Module\Frontend_Module $frontend_class Valid instance of a frontend module class.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 * @see Frontend\Module\Frontend_Module
	 */
	private function set_frontend_class( $module_id, $frontend_class ) {
		$this->modules[ $module_id ]['frontend_class'] = $frontend_class;
	}

	/**
	 * Register an admin class.
	 *
	 * @param string                    $module_id      Valid id key of a module.
	 * @param Admin\Module\Admin_Module $frontend_class Valid instance of an admin module class.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 * @see Admin\Module\Admin_Module
	 */
	private function set_admin_class( $module_id, $frontend_class ) {
		$this->modules[ $module_id ]['admin_class'] = $frontend_class;
	}

	/**
	 * Calls the public method `$method` of the class Frontend_Module for all registered instances.
	 *
	 * @param string $method Name of the method.
	 * @param mixed  $args   (optional) Arguments to be passed to the method call.
	 *
	 * @return array Results of the method calls (their return values).
	 *
	 * @since 0.1.0
	 * @see Frontend\Module\Frontend_Module
	 */
	public function foreach_frontend( $method, $args = null ) {
		$ret = array();
		foreach ( $this->modules as $module ) {
			if ( isset( $module ) ) {
				array_push( $ret, $module['frontend_class']->{$method}( $args ) );
			} else {
				array_push( $ret, null );
			}
		}
		return $ret;
	}

	/**
	 * Calls the public method `$method` of the class Admin_Module for all registered instances.
	 *
	 * @param string $method Name of the method.
	 * @param mixed  $args   (optional) Arguments to be passed to the method call.
	 *
	 * @return array Results of the method calls (their return values).
	 *
	 * @since 0.1.0
	 * @see Admin\Module\Admin_Module
	 */
	public function foreach_admin( $method, $args = null ) {
		$ret = array();
		foreach ( $this->modules as $module ) {
			if ( isset( $module ) ) {
				array_push( $ret, $module['admin_class']->{$method}( $args ) );
			} else {
				array_push( $ret, null );
			}
		}
		return $ret;
	}

	/**
	 * Return how many modules are registered.
	 *
	 * @return int Count of registered modules.
	 */
	public function count() {
		return count( $this->modules );
	}


	/**
	 * A litte helper function, returns a valid Options instance.
	 *
	 * @return Options Configured Options instance.
	 *
	 * @since 0.1.11
	 */
	private function options() {
		return $this->wp_plugin->options();
	}


	/**
	 * Test if the given key is a valid key for a module.
	 *
	 * @param string $key Module key to test.
	 *
	 * @return bool True if key is valid, false otherwise.
	 *
	 * @since 0.1.12
	 */
	public function is_valid_module_key( $key ) {
		return isset( $this->available_modules()[ $key ] );
	}

}

