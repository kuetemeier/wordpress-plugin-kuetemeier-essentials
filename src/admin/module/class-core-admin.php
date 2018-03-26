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

require_once( plugin_dir_path(__FILE__) . '/class-admin-module.php' );
require_once( plugin_dir_path( __FILE__ ) . '/../../config.php' );
require_once( plugin_dir_path( __FILE__ ) . '/../../class-kuetemeier-essentials.php' );

/**
 * Class Kuetemeier_Essentials
 */
class Core_Admin extends Admin_Module {

    function __construct() {
        parent::__construct();
    }

    public function _callback_admin_menu() {
    	$this->_callback_admin_menu_page_create();
    }

	public function _callback_admin_init() {
		$this->_callback_admin_init_settings();

	}

	public function _callback_admin_menu_page_create() {

		// add top level menu page
		add_menu_page(
			'Kuetemeier - Essentials', // page title
			'Kuetemeier - Essentials', // menu title
			\Kuetemeier_Essentials\ADMIN_PAGE_CAPABILITY, // capability
			\Kuetemeier_Essentials\ADMIN_MENU_SLUG, // menu slug
			array( &$this, '_callback_menu_page_display' ) // function
			// icon https://developer.wordpress.org/reference/functions/add_menu_page/
		);

		// add dashboard (same as top-level)
		add_submenu_page(
			\Kuetemeier_Essentials\ADMIN_MENU_SLUG, // parent slug
			'Kuetemeier-Essentials - Dashboard', // page title
			'Dashbaord', // menu title
			\Kuetemeier_Essentials\ADMIN_PAGE_CAPABILITY, // capability
			\Kuetemeier_Essentials\ADMIN_MENU_SLUG, // menu slug
			array( &$this, '_callback_menu_page_display' ) // function
		);

		do_action( 'kuetemeier_essentials_menu_page_create' );

	}

	public function _callback_menu_page_display() {
		$this->_display_option_page( \Kuetemeier_Essentials\CORE_ADMIN_PAGE_SLUG );
 	}


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
		?></p></div>
		<?php

	}

 	public function _callback_admin_init_settings() {

		// register a new setting for "wporg" page
		register_setting( 'kuetemeier_essentials', 'kuetemeier_essentials' );

	 	// First, we register a section. This is necessary since all future options must belong to a
	    add_settings_section(
	    	// ID used to identify this section and with which to register options
	        'kuetemeier_essentials',
	        // Title to be displayed on the administration page
	        __( 'Common', 'kuetemeier_essentials' ),
	        // Callback used to render the description of the section
	        array( &$this, '_callback_settings_dashboard_common' ),
	        // Page on which to add this section of options
	        \Kuetemeier_Essentials\CORE_ADMIN_PAGE_SLUG
	    );

	    // Next, we'll introduce the fields for toggling the visibility of content elements.
	    add_settings_field(
	    	// ID used to identify the field throughout the theme
	        'show_header',
	        // The label to the left of the option interface element
	        'Header',
	        // The name of the function responsible for rendering the option interface
	        'sandbox_toggle_header_callback',
	        // The page on which this option will be displayed
	        'general',
	        // The name of the section to which this field belongs
	        \Kuetemeier_Essentials\CORE_ADMIN_PAGE_SLUG,
	        // The array of arguments to pass to the callback. In this case, just a description.
	        array(
	            'Activate this setting to display the header.'
	        )
	    );

 	}

}
