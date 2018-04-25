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

namespace Kuetemeier_Essentials\Plugin_Modules;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );


/**
 * Abstract Plugin Module, to be extended by other classes to build Modules for this Plugin.
 *
 * @since 0.1.12
 */
abstract class Plugin_Module {

	/**
	 * Unique ID (frontend and admin modules may and should share the same ID) to identify this module.
	 *
	 * @var string Unique ID key.
	 *
	 * @since 0.1.12
	 */
	private $id = 'not defined';

	/**
	 * Spoken name of this module.
	 *
	 * @var string Spoken name of this module.
	 *
	 * @since 0.1.12
	 */
	private $name = 'not defined';


	/**
	 * A valid instance of the main WordPress Plugin object.
	 *
	 * @var WP_Plugin A valid instance.
	 *
	 * @since 0.1.12
	 */
	private $wp_plugin = null;


	/**
	 * Creates a Plugin Module.
	 *
	 * @param string    $id         Unique ID (frontend and admin modules may and should
	 *                              share the same ID) to identify this module.
	 * @param string    $name       Spoken name of this module.
	 * @param WP_Plugin $wp_plugin  A valid instance of the main WordPress Plugin object.
	 *
	 * @since 0.1.12
	 */
	public function __construct( $id, $name, $wp_plugin ) {

		$id = trim( $id );
		$name = trim( $name );

		if ( empty( $id ) ) {
			wp_die( 'FATAL ERROR: ID of a module cannot be empty.' );
		}

		if ( empty( $name ) ) {
			wp_die( 'FATAL ERROR: The name of a module cannot be empty.' );
		}

		if ( ! ( isset( $wp_plugin ) && is_subclass_of( $wp_plugin, 'Kuetemeier\WordPress\Plugin' ) ) ) {
			wp_die( 'FATAL ERROR: wp_plugin has to be a valid instance of a subclass of Kuetemeier\WordPress\Plugin' );
		}

		$this->id = $id;
		$this->name = $name;
		$this->wp_plugin = $wp_plugin;

	}

	/**
	 * Returns the unique ID of the module
	 *
	 * @return string The ID.
	 *
	 * @since 0.1.12
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Returns a "spoken" name or label for the module.
	 *
	 * @return string The name.
	 *
	 * @since 0.1.12
	 */
	public function get_name() {
		return $this->name;
	}


	/**
	 * Returns a valid instance of the main WordPress Plugin object.
	 *
	 * @return WP_Plugin A valid instace of a WP_Plugin object.
	 *
	 * @since 0.1.12
	 */
	public function get_wp_plugin() {
		return $this->wp_plugin;
	}


	/**
	 * Returns if this object is a frontend module.
	 *
	 * @return bool True if it is a frontend module.
	 *
	 * @since 0.1.12
	 */
	abstract public function is_frontend_module();


	/**
	 * Returns if this object is an admin module.
	 *
	 * @return bool True if it is an admin module.
	 *
	 * @since 0.1.12
	 */
	abstract public function is_admin_module();


	/**
	 * Get `admin_page_slug` for the this sub module.
	 *
	 * @return string Slug for the admin page of the module
	 * @since 0.1.0
	 */
	public function get_admin_page_slug() {
		return $this->wp_plugin->get_admin_page_slug( $this->get_id() );
	}


	/**
	 * This function can be used as callbacks for admin_menu.
	 * Use this function, because Admin modules will be loaded AFTER the normal callback is fired
	 *
	 * This is called after a successfull registration with the class `Modules`
	 *
	 * @return void
	 *
	 * @see \Kuetemeier_Essentials\Modules
	 * @since 0.1.0
	 */
	public function callback__admin_menu() {
		// intentionally empty
	}

	/**
	 * This function can be used as callbacks for admin_init.
	 * Use this function, because Admin modules will be loaded AFTER the normal callback is fired
	 *
	 * This is called after a successfull registration with the class `Modules`
	 *
	 * @return void
	 *
	 * @see \Kuetemeier_Essentials\Modules
	 * @since 0.1.0
	 */
	public function callback__admin_init() {
		// intentionally empty
	}

}
