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

require_once( plugin_dir_path( __FILE__ ) . '/../../config.php' );

/**
 * Class Kuetemeier_Essentials
 */
abstract class Admin_Module {

	/**
	 * Holds an instance of the @see \Kuetemeier_Essentials\Options class
	 */
	protected $_options;

	/**
	 * end part of the admin page (option settings) slug
	 *
	 * @see get_admin_page_slug()
	 *
	 */
	protected $_admin_page_slug_part = '';

	function __construct( $options ) {
		$this->_options = $options;
	}

	protected function _set_admin_page_slug_part( $page_slug ) {
		$this->_admin_page_slug_part = $page_slug;
	}

	public function get_admin_page_slug() {
		// TODO: escape
		return 'kuetemeier_essentials_'.$this->_admin_page_slug_part;
	}

	/**
	 * This function can be used as callbacks for admin_menu.
	 * Use this function, because Admin modules will be loaded AFTER the normal callback is fired
	 */
	public function _callback_admin_menu() {
		// intentionally empty
	}

	/**
	 * This function can be used as callbacks for admin_init.
	 * Use this function, because Admin modules will be loaded AFTER the normal callback is fired
	 */
	public function _callback_admin_init() {
		// intentionally empty
	}

}

