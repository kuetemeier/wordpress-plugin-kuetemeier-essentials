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
class Data_Privacy_Admin extends Admin_Module {

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

		// add dashboard (same as top-level)
		add_submenu_page(
			// parent_slug - The slug name for the parent menu (or the file name of a standard WordPress admin page).
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG,
			// page_title - The text to be displayed in the title tags of the page when the menu is selected.
			'Data Privacy',
			// menu_title - The text to be used for the menu.
			'Data Privacy',
			// capability - The capability required for this menu to be displayed to the user.
			\Kuetemeier_Essentials\CORE_OPTION_PAGE_CAPABILITY,
			// menu_slug - The slug name to refer to this menu by. Should be unique for this menu and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
			\Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG,
			array( &$this, '_callback_menu_page_display' ) // function
		);

		//do_action( 'kuetemeier_essentials_menu_page_create' );

	}

	public function _callback_menu_page_display() {
		$this->_display_option_page( \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG );
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
		?></p>

<p>Config: <?php
	    $options = get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );
print_r ($options);

      ?></p>
	</div>
		<?php

	}

	public function _callback_setting_toggle_module_privacy( $args ) {

	    // First, we read the options collection
	    $options = get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );


/*	    if( false == get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY ) ) {
    		$options = array( 'core' => array( 'version' => '1.0' ) );
		} // end if
*/

	    $key = 'module_dp_1';
	    $value = 0;
	    if ( array_key_exists( 'core', $options ) ) {
		    $_value = $options['core'];
		    if ( ! empty( $_value ) ) {
			    if ( array_key_exists( $key, $_value ) ) {
			    	$_value = $_value[$key];
			    	if ( ! empty ($_value) )
			    		$value = $_value;
			    }
			}
		}

	    // Next, we update the name attribute to access this element's ID in the context of the display options array
	    // We also access the show_header element of the options collection in the call to the checked() helper function
	    $html = '<input type="checkbox" id="' .$key. '" name="'.\Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY.'[core][' .$key . ']" value="1" ' . checked(1, $value, false) . '/>';

	    // Here, we'll take the first argument of the array and add it to a label next to the checkbox
	    $html .= '<label for="' . $key . '"> '  . $args[0] . '</label>';

	    echo $html;
	} // end sandbox_toggle_header_callback


	public function _callback_setting_toggle_module_develop( $args ) {

	    // First, we read the options collection
	    $options = get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );

	    $key = 'module_dp_2';
	    $value = 0;
	    if ( array_key_exists( 'core', $options ) ) {
		    $_value = $options['core'];
		    if ( ! empty( $_value ) ) {
			    if ( array_key_exists( $key, $_value ) ) {
			    	$_value = $_value[$key];
			    	if ( ! empty ($_value) )
			    		$value = $_value;
			    }
			}
		}

	    // Next, we update the name attribute to access this element's ID in the context of the display options array
	    // We also access the show_header element of the options collection in the call to the checked() helper function
	    $html = '<input type="checkbox" id="' .$key. '" name="'.\Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY.'[core][' .$key . ']" value="1" ' . checked(1, $value, false) . '/>';

	    // Here, we'll take the first argument of the array and add it to a label next to the checkbox
	    $html .= '<label for="' . $key . '"> '  . $args[0] . '</label>';

	    echo $html;
	} // end sandbox_toggle_header_callback


 	public function _callback_admin_init_settings() {

	    if( false == get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY ) ) {
    		update_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY, array( 'core' => array( 'version' => '1.0' ) ) );
		} // end if


	 	// First, we register a section. This is necessary since all future options must belong to a
	    add_settings_section(
	    	// ID used to identify this section and with which to register options
	        \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_SETTINGS_KEY,
	        // Title to be displayed on the administration page
	        __( 'Common', 'kuetemeier_essentials' ),
	        // Callback used to render the description of the section
	        array( &$this, '_callback_settings_dashboard_common' ),
	        // Page on which to add this section of options
	        \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG
	    );


	    // Next, we'll introduce the fields for toggling the visibility of content elements.
	    add_settings_field(
	    	// ID used to identify the field throughout the theme
	        'module_load_data_privacy',
	        // The label to the left of the option interface element
	        'Data Privacy Module',
	        // The name of the function responsible for rendering the option interface
	        array( &$this, '_callback_setting_toggle_module_privacy'),
	        // The page on which this option will be displayed
	        \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG,
	        // The name of the section to which this field belongs
	        \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY,
	        // The array of arguments to pass to the callback. In this case, just a description.
	        array(
	            'Activate this setting to load the data privacy module.'
	        )
	    );


	    // Next, we'll introduce the fields for toggling the visibility of content elements.
	    add_settings_field(
	    	// ID used to identify the field throughout the theme
	        'module_load_develop',
	        // The label to the left of the option interface element
	        'Develop Module',
	        // The name of the function responsible for rendering the option interface
	        array( &$this, '_callback_setting_toggle_module_develop'),
	        // The page on which this option will be displayed
	        \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG,
	        // The name of the section to which this field belongs
	        \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY,
	        // The array of arguments to pass to the callback. In this case, just a description.
	        array(
	            'Activate this setting to load the develop module.'
	        )
	    );



		//register_setting( \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_PAGE_SLUG, \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY );
		register_setting( \Kuetemeier_Essentials\CORE_OPTION_PAGE_SLUG, \Kuetemeier_Essentials\DATA_PRIVACY_OPTION_SETTINGS_KEY );

 	}

}
