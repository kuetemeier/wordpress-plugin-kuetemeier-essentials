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

/**
 * Class Modules - mangae kuetemeier_essential module classes
 */
class Options {

	const ACTION_PREFIX = 'kuetemeier_essentials_options_';
	const OPTIONS_SETTINGS_KEY = 'kuetemeier_essentials';

	const OPTIONS_PAGE_CAPABILITY = 'manage_options';
	const OPTIONS_PAGE_SLUG = 'kuetemeier_essentials';

	protected $_admin_options_subpages = array();
	protected $_admin_options_subpages_order = array();

	function __construct() {

		$this->add_core_admin_options_subpage();
		add_action( self::ACTION_PREFIX.'create_admin_menu', array( &$this, '_callback_create_admin_menu' ) );
	}

	public function init_admin_hooks() {
		// IMPORTANT: THis must be called AFTER all admin classes of the modules are loaded
		add_action( 'admin_init', array( &$this, '_callback_admin_init' ) );
		add_action( 'admin_menu', array( &$this, '_callback_admin_menu' ) );
	}

	public function add_admin_options_subpage(
		$slug,
		$title,
		$menu_title,
		$tabs = array(),
		$display_func = null,
		$capability = self::OPTIONS_PAGE_CAPABILITY,
		$parent_slug = self::OPTIONS_PAGE_SLUG
	) {

		if ( $display_func == null )
			$display_func = array( &$this, '_callback_options_page_display' );

		array_push( $this->_admin_options_subpages_order, $slug );
		$this->_admin_options_subpages[$slug] = array(
			'slug' => $slug,
			'title' => $title,
			'menu_title' => $menu_title,
			'capability' => $capability,
			'parent_slug' => $parent_slug,
			'display_func' => $display_func,
			'tabs' => $tabs
		);
	}


	public function _callback_admin_init() {

	}

	public function _callback_admin_menu() {
		$this->_create_admin_menu();
	}

	public function add_core_admin_options_subpage() {
		$this->add_admin_options_subpage(
			self::OPTIONS_PAGE_SLUG,
			'Kuetemeier - Essentials',
			'Essentials',
			array(
				'general' => 'General',
				'module' => 'Module'
			)
		);
	}

	public function _callback_create_admin_menu() {

		// add top level menu page
		add_menu_page(
			'Kuetemeier', // page title
			'Kuetemeier', // menu title
			self::OPTIONS_PAGE_CAPABILITY, // capability
			self::OPTIONS_PAGE_SLUG, // menu slug
			array( &$this, '_callback_options_page_display' ) // function
		);

		// Use this hook to add your own subpages via add_admin_options_subpage
		do_action( self::ACTION_PREFIX.'configure_admin_menu' );

		// add all configured subpages to WP
		foreach( $this->_admin_options_subpages_order as $subpage_slug ) {
			$subpage = $this->_admin_options_subpages[ $subpage_slug ];

			// add dashboard (same as top-level)
			add_submenu_page(
				// parent_slug - The slug name for the parent menu (or the file name of a standard WordPress admin page).
				$subpage[ 'parent_slug' ],
				// page_title - The text to be displayed in the title tags of the page when the menu is selected.
				$subpage[ 'title' ],
				// menu_title - The text to be used for the menu.
				$subpage[ 'menu_title' ],
				// capability - The capability required for this menu to be displayed to the user.
				$subpage[ 'capability' ],
				// menu_slug - The slug name to refer to this menu by. Should be unique for this menu and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
				$subpage[ 'slug' ],
				// display function
				$subpage[ 'display_func' ]
			);
		}


	}

	public function _create_admin_menu() {
		do_action( self::ACTION_PREFIX.'before_create_admin_menu' );

		do_action( self::ACTION_PREFIX.'create_admin_menu' );

		do_action( self::ACTION_PREFIX.'after_create_admin_menu' );
	}


	public function _callback_options_page_display() {

	}
}
