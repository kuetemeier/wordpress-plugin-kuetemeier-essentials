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

namespace Kuetemeier_Essentials\Admin\Module;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once plugin_dir_path( __FILE__ ) . '/../../config.php';

/**
 * Abstract Admin module, to be extended by other classes to build admin modules.
 */
abstract class Admin_Module {

	/**
	 * End part of the admin page (option settings) slug
	 *
	 * @var string
	 *
	 * @see get_admin_page_slug()
	 * @since 0.1.0
	 */
	protected $admin_page_slug_part = '';

	/**
	 * Initialize an Admin Module
	 *
	 * @param \Kuetemeier_Essentials\Options $options A valid instance of the `Options` class.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function __construct( $options ) {
		$this->options = $options;
	}


	/**
	 * Returns if this object is a frontend module.
	 *
	 * @return bool True if it is a frontend module.
	 *
	 * @since 0.1.12
	 */
	public function is_frontend_module() {
		return false;
	}


	/**
	 * Returns if this object is an admin module.
	 *
	 * @return bool True if it is an admin module.
	 *
	 * @since 0.1.12
	 */
	public function is_admin_module() {
		return true;
	}


	/**
	 * Set `admin_page_slug_part` property.
	 *
	 * @param string $page_slug New value.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	protected function set_admin_page_slug_part( $page_slug ) {
		$this->admin_page_slug_part = sanitize_text_field( $page_slug );
	}

	/**
	 * Get `admin_page_slug`
	 *
	 * @return string Slug for the admin page of the module
	 * @since 0.1.0
	 */
	public function admin_page_slug() {
		return sanitize_text_field( 'kuetemeier_essentials_' . $this->admin_page_slug_part );
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
	public function callback_admin_menu() {
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
	public function callback_admin_init() {
		// intentionally empty
	}

}

