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

require_once plugin_dir_path( __FILE__ ) . '/class-admin-module.php';
require_once plugin_dir_path( __FILE__ ) . '/../../config.php';
require_once plugin_dir_path( __FILE__ ) . '/../../class-kuetemeier-essentials.php';

/**
 * Class Kuetemeier_Essentials
 */
class Core_Admin extends Admin_Module {

	public function __construct( $options ) {
		parent::__construct( $options );
	}

	public function callback_admin_menu() {
		//$this->callback_admin_menu_page_create();
	}

	public function callback_admin_init() {
		//$this->callback_admin_init_settings();

	}

	public function callback_admin_menu_page_create() {
		/*
		// add top level menu page
		add_menu_page(
			'Kuetemeier', // page title
			'Kuetemeier', // menu title
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_CAPABILITY, // capability
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG, // menu slug
			array( &$this, '_callback_menu_page_display' ) // function
			// icon https://developer.wordpress.org/reference/functions/add_menu_page/
		);

		// add dashboard (same as top-level)
		add_submenu_page(
			// parent_slug - The slug name for the parent menu (or the file name of a standard WordPress admin page).
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG,
			// page_title - The text to be displayed in the title tags of the page when the menu is selected.
			'Kuetemeier - Essentials',
			// menu_title - The text to be used for the menu.
			'Essentials',
			// capability - The capability required for this menu to be displayed to the user.
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_CAPABILITY,
			// menu_slug - The slug name to refer to this menu by. Should be unique for this menu and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG,
			array( &$this, '_callback_menu_page_display' ) // function
		);

		do_action( 'kuetemeier_essentials_menu_page_create' );
		*/
	}

	public function callback_menu_page_display() {
		/*
		$this->display_option_page( \Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG );
		*/
	}

	/*
	 public function oenology_get_settings_page_tabs() {
		$tabs = array(
			'general' => 'General',
			'varietals' => 'Varietals'
			);
		return $tabs;
	}
	*/

	/*
	public function _callback_settings_dashboard_common( $args ) {
		$modules = \Kuetemeier_Essentials\Kuetemeier_Essentials::instance()->get_modules();

		$names = $modules->foreach_frontend( 'get_name' );
		$name_list = join( ', ', $names);
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Number of loaded modules', 'kuetemeier_essentials' );
			echo ': ' . esc_html( $modules->count() );
			?></p>
			<p><?php esc_html_e( 'Active Modules', 'kuetemeier_essentials' );
		echo ': ' . esc_html( $name_list );
		?></p>

	<p>Config: <?php
		$options = get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );
	print_r ($options);

	  ?></p>
	</div>
		<?php

	}

	*/


	public function options_validate( $input ) {
		$_options = get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );
		$valid_input = $_options;

		// Determine which form action was submitted
		$submit_general = ( ! empty( $input['submit-general'] ) ? true : false );
		$reset_general = ( ! empty( $input['reset-general'] ) ? true : false );
		$submit_varietals = ( ! empty( $input['submit-varietals'] ) ? true : false );
		$reset_varietals = ( ! empty( $input['reset-varietals'] ) ? true : false );

		if ( $submit_general ) { // if General Settings Submit
			/*
			$valid_input['header_nav_menu_position'] = ( 'below' == $input['header_nav_menu_position'] ? 'below' : 'above' );
			$valid_input['header_nav_menu_depth'] = ( ( 1 || 2 || 3 ) == $input['header_nav_menu_depth'] ? $input['header_nav_menu_depth'] : $valid_input['header_nav_menu_depth'] );
			*/
			$valid_input['module_load_data_privacy'] = ( '1' == $input['module_load_data_privacy'] ? 1 : 0 );
		} elseif ( $reset_general ) { // if General Settings Reset Defaults
			/*
			$oenology_default_options = oenology_get_default_options();
			$valid_input['header_nav_menu_position'] = $oenology_default_options['header_nav_menu_position'];
			$valid_input['header_nav_menu_depth'] = $oenology_default_options['header_nav_menu_depth'];
			$valid_input['display_footer_credit'] = $oenology_default_options['display_footer_credit'];
			*/
			$valid_input['module_load_data_privacy'] = 1;
		} elseif ( $submit_varietals ) {
			$valid_input['module_load_develop'] = ( '1' == $input['module_load_develop'] ? 1 : 0 );
		}
		//print_r ($valid_input);
		//exit (0);
		return $valid_input;
	}

	public function callback_admin_init_settings() {

		/*
		if( false == get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY ) ) {
			update_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY, array( 'core' => array( 'version' => '1.0' ) ) );
		} // end if


		 // First, we register a section. This is necessary since all future options must belong to a
		add_settings_section(
			// ID used to identify this section and with which to register options
			\Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY,
			// Title to be displayed on the administration page
			__( 'Common', 'kuetemeier_essentials' ),
			// Callback used to render the description of the section
			array( &$this, '_callback_settings_dashboard_common' ),
			// Page on which to add this section of options
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG
		);
		*/

	}
}
