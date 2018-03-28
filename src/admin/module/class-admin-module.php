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

