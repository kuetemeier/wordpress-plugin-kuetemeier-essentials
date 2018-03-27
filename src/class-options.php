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
 * Options - Helpers to interact with the WordPress Options and Settings API
 */
class Options {

	const ACTION_PREFIX = 'kuetemeier_essentials_options_';

	// Key for the WordPress database table 'options'
	const OPTIONS_SETTINGS_KEY = 'kuetemeier_essentials';

	const OPTIONS_PAGE_CAPABILITY = 'manage_options';
	const OPTIONS_PAGE_SLUG = 'kuetemeier_essentials';

	protected $_admin_options_subpages = array();
	protected $_admin_options_subpages_order = array();

	protected $_option_settings = array();
	protected $_option_sections = array();

	public static $_instance = null;

	function __construct() {

		if ( ! is_null( self::$_instance ) )
			die ('You tried to create to instances of \Kuetemeier_Essentials\Options');

		$this->add_core_admin_options_subpage();
		add_action( self::ACTION_PREFIX.'create_admin_menu', array( &$this, '_callback_create_admin_menu' ) );

		$this->add_option_section( new Option_Section( 'test', 'Test', 'kuetemeier_essentials', 'test', 'Dies ist ein Test' ) );
		$this->add_option_section( new Option_Section( 'test2', 'Test2', 'kuetemeier_essentials', 'test', 'Dies ist ein 2. Test' ) );

		$this->add_option_setting( new Option_Setting_Checkbox( 'core', 'test_option', false, 'Test', 'kuetemeier_essentials', 'test', 'test', 'This is the first option' ) );


		$this->add_option_setting( new Option_Setting( 'core', 'first',  'V:1', 'First',  'kuetemeier_essentials', 'general', 'default', 'This is the first option' ) );
		$this->add_option_setting( new Option_Setting( 'core', 'second', 'V:2', 'Second', 'kuetemeier_essentials', 'general', 'kuetemeier_essentials', 'This is the first option' ) );
		$this->add_option_setting( new Option_Setting( 'core', 'third',  'V:3', 'Third',  'kuetemeier_essentials_data_privacy', '', '', 'This is the first option' ) );
	}

// TODO: set default values if there is no database entry

/*

	    if( false == get_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY ) ) {
    		update_option( \Kuetemeier_Essentials\CORE_OPTION_SETTINGS_KEY, array( 'core' => array( 'version' => '1.0' ) ) );
		} // end if

*/



	/**
	 * Main Kueteemier_Essentials Instance
	 * Ensures only one instance of Kuetemeier_Essentials is loaded or can be loaded.
	 *
	 * @return Kuetemeier_Essentials Kuetemeier_Essentials instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function add_option_setting( $option_setting ) {
		if ( ! empty( $option_setting ) )
			array_push( $this->_option_settings, $option_setting );
	}

	public function add_option_section( $option_section ) {
		if ( ! empty( $option_section ) )
			array_push( $this->_option_sections, $option_section );
	}

	public function get_db_option_key() {
		return self::OPTIONS_SETTINGS_KEY;
	}

	public function test_module_key_valid( $module_key ) {
		$valid_keys = array( 'default' => 1, 'core' => 1, 'data-privacy' => 1 );

		return array_key_exists( $module_key, $valid_keys);
	}

	/**
	 * Return the WordPress entry of the option table.
	 *
	 * If no entry exists, this function returns 'false'
	 */
	public function get_db_options() {
		return get_option( self::OPTIONS_SETTINGS_KEY );
	}

	/**
	 * Return the options for a specific module under the OPTIONS_SETTINGS_KEY
	 *
	 * @see get_db_options
	 *
	 * @return Array of options or false, if module key did not exists
	 */
	public function get_db_options_for_module( $module_key ) {
		// get complete options for our key
		$options = $this->get_db_options();

		// We cannot find anything if we have no $module_key
		if ( empty( $module_key ) )
			return false;

		if ( ! $this->test_module_key_valid( $module_key ) )
			return false;

		// No options found for our general db key?
		if ( empty( $options ) )
			return false;

		// Does the $module_key exists in our db options?
		if ( array_key_exists( $module_key, $options) ) {
			return $options[ $module_key ];
		}

		// something went wrong / no data found
		return false;
	}

	public function get_option( $module_key, $option_key, $default = false ) {
		$module_options = $this->get_db_options_for_module( $module_key );

		if ( ! $module_options )
			return $default;

		if ( array_key_exists( $option_key, $module_options ) )
			return $module_options[ $option_key ];

		return $default;
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

	public function _callback_validate_options( $input ) {
		$valid_input = get_option( self::OPTIONS_SETTINGS_KEY );

		return $valid_input;
	}

	public function _callback_add_settings_section__display_empty_section( $args ) {
	}

	protected function _do_add_settings_fields( $page_slug, $current_tab ) {
		foreach( $this->_option_settings as $option_setting ) {
			$option_setting->do_add_settings_field( $page_slug, $current_tab );
		}
	}

	protected function _do_add_settings_sections( $page_slug, $current_tab ) {
		foreach( $this->_option_sections as $option_section ) {
			$option_section->do_add_settings_section( $page_slug, $current_tab );
		}
	}

	public function _register_settings( $page_slug, $current_tab ) {
		register_setting( self::OPTIONS_SETTINGS_KEY, self::OPTIONS_SETTINGS_KEY, array( &$this, '_callback_validate_options' ) );

		$this->_do_add_settings_sections( $page_slug, $current_tab );

		add_settings_section(
			'default',
			'',
			array( &$this, '_callback_add_settings_section__display_empty_section' ),
			$page_slug
		);

		$this->_do_add_settings_fields( $page_slug, $current_tab );

	}

	public function add_core_admin_options_subpage() {
		$this->add_admin_options_subpage(
			self::OPTIONS_PAGE_SLUG,
			'Kuetemeier > Essentials',
			'Essentials',
			array(
				'test' => 'Test',
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


	public function get_tabs_for_options_subpage( $subpage = self::OPTIONS_PAGE_SLUG ) {

		if ( array_key_exists( $subpage, $this->_admin_options_subpages ) ) {
			return $this->_admin_options_subpages[ $subpage ][ 'tabs' ];
		}

		return array();
	}

	public function _create_admin_menu() {
		do_action( self::ACTION_PREFIX.'before_create_admin_menu' );

		do_action( self::ACTION_PREFIX.'create_admin_menu' );

		do_action( self::ACTION_PREFIX.'after_create_admin_menu' );
	}

	protected function _options_page_tabs( $page_slug = self::OPTIONS_PAGE_SLUG ) {

		$tabs = $this->get_tabs_for_options_subpage( $page_slug );;

		$current_tab = '';
		if ( isset ( $_GET['tab'] ) ) {
			$_current_tab = $_GET['tab'];
			// Set current tab, if we can find the URL parameter in our tabs list.
			if ( array_key_exists( $_current_tab, $tabs) );
			$current_tab = $_current_tab;
		} else {
			// Default to first tab in list, if there is a list.
			if ( sizeof($tabs) > 0 ) {
				$current_tab = key($tabs);
			}
		}

		$this->_register_settings( $page_slug, $current_tab );

		if ( sizeof($tabs) > 0 ) {

			$links = array();
			foreach( $tabs as $tab => $name ) {
				if ( $tab == $current_tab ) {
					$links[] = '<a class="nav-tab nav-tab-active" href="?page=oenology-settings&tab=$tab">' . $name . '</a>';
				} else {
					$links[] = '<a class="nav-tab" href="?page=kuetemeier_essentials&tab=' . $tab . '">' . $name .'</a>';
				}
			}

			echo '<br /></div>';
			echo '<h2 class="nav-tab-wrapper">';


			foreach ( $links as $link ) {
				echo $link;
			}
			echo '</h2>';

			// TODO: remove debug
			//global $wp_settings_fields;
			//print_r($wp_settings_fields['kuetemeier_essentials']);
		}

	}


	public function _callback_options_page_display() {

		// Set default to a known slug
		$page_slug = self::OPTIONS_PAGE_SLUG;

		// Get active page from URL.
		$_page_slug = $_GET['page'];
		if ( isset($_page_slug) ) {

			// Test if it's a "real" page in our subpage list.
			if ( array_key_exists( $_page_slug, $this->_admin_options_subpages ) ) {
				$page_slug = $_page_slug;
			}
		}

		?>
	    <div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	        <?php $this->_options_page_tabs( $page_slug ); ?>
			<?php settings_errors(); ?>

		     <form action="options.php?page=<?php esc_attr_e( $page_slug ); ?>" method="post">
			     <?php
			     settings_fields( $page_slug );
			     do_settings_sections( $page_slug );
			     ?>
			     <?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' ); ?>
			     <p class="submit">
				     <input name="kuetemeier_essentials[submit-<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'oenology'); ?>" />
				     <input name="kuetemeier_essentials[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'oenology'); ?>" />
			     </p>
		     </form>
	     </div>
		<?php

	}
}


/*
	// pill field cb

	// field callbacks can accept an $args parameter, which is an array.
	// $args is defined at the add_settings_field() function.
	// wordpress has magic interaction with the following keys: label_for, class.
	// the "label_for" key value is used for the "for" attribute of the <label>.
	// the "class" key value is used for the "class" attribute of the <tr> containing the field.
	// you can add custom key value pairs to be used inside your callbacks.
	public function _settings_field_pill_cb( $args ) {
		// get the value of the setting we've registered with register_setting()
		$options = get_option( 'kuetemeier_essentials_options' );
		// output the field
		?>
		<select id="<?php echo esc_attr( $args['label_for'] ); ?>"
		data-custom="<?php echo esc_attr( $args['wporg_custom_data'] ); ?>"
		name="wporg_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
		>
		<option value="red" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'red', false ) ) : ( '' ); ?>>
		<?php esc_html_e( 'red pill', 'wporg' ); ?>
		</option>
		<option value="blue" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'blue', false ) ) : ( '' ); ?>>
		<?php esc_html_e( 'blue pill', 'wporg' ); ?>
		</option>
		</select>
		<p class="description">
		<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'wporg' ); ?>
		</p>
		<p class="description">
		<?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'wporg' ); ?>
		</p>
		<?php
	}

*/

class Option_Section {
	protected $_id = '';

	protected $_page = '';

	protected $_tab = '';

	protected $_title = '';

	protected $_content = '';

	protected $_display_function;

	function __construct( $id, $title, $page, $tab = '', $content = '', $display_function = null ) {
		$this->_id = $id;
		$this->_title = $title;
		$this->_page = $page;
		$this->_tab = $tab;
		$this->_content = $content;
		$this->set_display_function( $display_function );
	}


	public function get_id() {
		return $this->_id;
	}

	public function get_page() {
		return $this->_page;
	}

	public function get_tab() {
		return $this->_tab;
	}

	public function get_title() {
		return $this->_title;
	}

	public function get_content() {
		return $this->_content;
	}

	public function _callback__display_function( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo esc_html( $this->_content ); ?>
		</div>
		<?php
	}

	public function set_display_function( $display_function ) {
		if ( empty( $display_function) ) {
			$this->_display_function = array( &$this, '_callback__display_function' );
		} else {
			$this->_display_function = $display_function;
		}
	}

	public function get_display_function() {
		return $this->_display_function;
	}

	/**
	 * Add this setting to the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string 	$page    		The slug-name of the settings page on which to show the section. Built-in pages include
     *                           		'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
	 *                           		add_options_page();
	 * @param string 	$tab 			Optional. The slug-name of the current tab (if any is present on the settings page)
	 */
	public function do_add_settings_section( $page = '', $tab = '' ) {
		// Do we have to filter for page slug?
		if ( ! empty( $page) ) {

			// Yes:
			if ( ! ( $page == $this->get_page() ) )
				// Do nothing if page slugs do not match
				return;

		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab) ) {

			// Yes:
			if ( ! ( $tab == $this->get_tab() ) )
				// Do nothing if tab slugs do not match
				return;

		}

		// Add this section to WordPress sections
		// https://codex.wordpress.org/Function_Reference/add_settings_section
		add_settings_section(
			// String for use in the 'id' attribute of tags.
			$this->get_id(),
			// Title of the section.
			$this->get_title(),
			// Function that fills the section with the desired content. The function should echo its output.
			$this->get_display_function(),
			// The menu page on which to display this section.
			$this->get_page()
		);

	}
}

/**
 * A single setting for an option managed by Options.
 */
class Option_Setting {

	/**
	 * Id of the module this option setting belongs to
	 */
	protected $_module = '';

	/**
	 * Unique ID of the Instance of this Class and the key for the database entry.
	 */
	protected $_id = '';

	protected $_default = null;

	/**
	 * Common name
	 */
	protected $_name = '';

	/**
	 * Label, e.g. for the setting page
	 */
	protected $_label = '';

	protected $_page = '';

	protected $_tab = '';

	protected $_section = 'default';

	protected $_order = 0;

	/**
	 * Description shown in the settings page.
	 */
	protected $_description = '';

	function __construct( $module, $id, $default, $label, $page = '', $tab = '', $section = '', $description = '', $order = 0 ) {
		$this->_module = $module;
		$this->_id = $id;
		$this->_default = $default;
		$this->_label = $label;
		$this->_page = $page;
		$this->_tab = $tab;
		$this->set_section( $section );
		$this->set_description ( $description );
		$this->_order = $order;
	}

	public function get_module() {
		return $this->_module;
	}

	public function get_id() {
		return $this->_id;
	}

	public function get_default() {
		return $this->_default;
	}

	public function get_label() {
		return $this->_label;
	}

	public function get_page() {
		return $this->_page;
	}

	public function get_tab() {
		return $this->_tab;
	}

	public function get_section() {
		return $this->_section;
	}

	public function set_section( $section ) {
		$_section = $section;
		if ( empty($_section) )
			$_section = 'default';
		$this->_section = $_section;
	}

	public function get_order() {
		return $this->_tab;
	}

	public function get_description() {
		return $this->_tab;
	}

	public function set_description( $description ) {
		$_description = $description;

		// convert empty string to null
		if ( empty($_description) )
			$_description = null;

		$this->_description = $_description;
	}

	public function _callback_display_setting( $args ) {
		echo "Hallo Welt: ".$this->get_label();

	}

	public function do_add_settings_field( $page, $tab ) {

		// Do we have to filter for page slug?
		if ( ! empty( $page) ) {

			// Yes:
			if ( ! ( $page == $this->get_page() ) )
				// Do nothing if page slugs do not match
				return;

		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab) ) {

			// Yes:
			if ( ! ( $tab == $this->get_tab() ) )
				// Do nothing if tab slugs do not match
				return;

		}

		add_settings_field(
	    	// ID used to identify the field throughout the theme
	        $this->get_id(),
	        // The label to the left of the option interface element
	        $this->get_label(),
	        // The name of the function responsible for rendering the option interface
	        array( &$this, '_callback_display_setting'),
	        // The page on which this option will be displayed
	        $this->get_page(),
	        // The name of the section to which this field belongs
	        $this->get_section(),
	        // The array of arguments to pass to the callback. In this case, just a description.
	        array(
	            $this->get_description()
	        )
		);
	}

	public function sanitize( $input ) {

	}

	public function get() {
		$options = \Kuetemeier_Essentials\Options::instance();

		$module_option = $options->get_option( $this->get_module(), $this->get_id(), $this->get_default() );

		return $module_option;
	}

	/**
	 * Set this option to value $value
	 */
	public function set( $value ) {
	}
}

class Option_Setting_Checkbox extends Option_Setting {

	function __construct( $module, $id, $default, $label, $page = '', $tab = '', $section = '', $description = '', $order = 0 ) {
		parent::__construct( $module, $id, $default, $label, $page, $tab, $section, $description, $order );

	}

	public function _callback_display_setting( $args ) {
		$options = \Kuetemeier_Essentials\Options::instance();
		$value = $this->get();
		$complete_id = $this->get_module() . '_' . $this->get_id();

	    // Next, we update the name attribute to access this element's ID in the context of the display options array
	    // We also access the show_header element of the options collection in the call to the checked() helper function
	    $html = '<input type="checkbox" id="' . $complete_id . '" name="'. $options->get_db_option_key() . '[' . $this->get_module() .'][' . $this->get_id() . ']" value="1" ' . checked(1, $value, false) . '/>';

	    // Here, we'll take the first argument of the array and add it to a label next to the checkbox
	    $html .= '<label for="' . $complete_id . '"> '  . $args[0] . '</label>';

	    echo $html;
	}
}
