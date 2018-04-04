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

require_once plugin_dir_path( __FILE__ ) . '/class-wp-plugin.php';

/**
 * Options - Helpers to interact with the WordPress Options and Settings API
 */
final class Options {

	const ACTION_PREFIX = 'kuetemeier_essentials_options_';

	// Key for the WordPress database table 'options'
	const OPTIONS_SETTINGS_KEY = 'kuetemeier_essentials';

	const OPTIONS_PAGE_CAPABILITY = 'manage_options';
	const OPTIONS_PAGE_SLUG = 'kuetemeier_essentials';

	/**
	 * Holds all registered admin option subpages.
	 *
	 * @var array
	 * @since 0.1.0
	 * @see Options::add_admin_options_subpage()
	 */
	private $admin_options_subpages = array();


	/**
	 * Holds the order list of the subpages
	 *
	 * @var array
	 * @since 0.1.0
	 * @see Options::add_admin_options_subpage()
	 */
	private $admin_options_subpages_order = array();


	/**
	 * List of all registered option settings.
	 *
	 * @var Option_Setting[]
	 * @since 0.1.0
	 */
	private $option_settings = array();


	/**
	 * List of all registered option sections.
	 *
	 * @var Options_Section[]
	 * @since 0.1.0
	 */
	private $option_sections = array();


	/**
	 * Holds a valid instance of this class.
	 *
	 * @var Options
	 * @since 0.1.0
	 */
	public static $instance = null;


	/**
	 * A valid instance of the Plugin class that has instanciated this class.
	 *
	 * We use it for referencing to the modules of the plugin.
	 *
	 * @var WP_Plugin
	 * @since 0.1.11
	 */
	private $plugin;


	/**
	 * Initialize and create basline for Options.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 * @param WP_Plugin $wp_plugin Caller of this constructor.
	 */
	public function __construct( $wp_plugin ) {

		if ( ! is_null( self::$instance ) ) {
			die( 'You tried to create a second instance of \Kuetemeier_Essentials\Options' );
		}

		$this->wp_plugin = $wp_plugin;

		$this->add_admin_options_subpage(
			self::OPTIONS_PAGE_SLUG,
			'Kuetemeier > Essentials',
			'Essentials',
			array(
				'general' => __( 'General', 'kuetemeier-essentials' ),
				'modules' => __( 'Modules', 'kuetemeier-essentials' ),
				'test'    => 'Test',
			)
		);

		// register callback to actually create the admin page and subpages
		add_action( self::ACTION_PREFIX . 'create_admin_menu', array( &$this, 'callback_create_admin_menu' ) );

		$this->add_option_section(
			new Option_Section(
				// id
				'test',
				// title
				'Test',
				// page
				'kuetemeier_essentials',
				// (optional) tab
				'test',
				// (optional) content
				'Dies ist ein Test'
				// (optional) display_function
			)
		);

		// --------------------------------
		// add OPTION SETTINGS
		// --------------------------------

		$this->add_option_setting(
			new Option_Setting_Checkbox(
				// module
				'default',
				// id
				'test_option_1',
				// default
				false,
				// label
				'Test mit dieser Option 1',
				// page
				'kuetemeier_essentials',
				// tab
				'test',
				// section
				'test',
				// description
				'A Dies sollte er validieren und speichern'
			)
		);

		$this->add_option_setting( new Option_Setting_Checkbox( 'core', 'test_option_2', true, 'Test mit dieser Option 2', 'kuetemeier_essentials', 'test', 'test', 'B Dies sollte er validieren und speichern' ) );
		$this->add_option_setting( new Option_Setting_Checkbox( 'default', 'test_option_3', true, 'Test mit dieser Option 3', 'kuetemeier_essentials', 'test', 'test', 'C Dies sollte er validieren und speichern' ) );

		$this->add_option_setting( new Option_Setting_Text( 'default', 'test_text', 'Ein Text', 'Ein Textfeld', 'kuetemeier_essentials', 'test', 'test', 'Dies ist ein Textfeld' ) );

	}

	// TODO: set default values if there is no database entry

	/**
	 * Register an option setting.
	 *
	 * @param Option_Setting $option_setting A valid instance of an Option_Setting object.
	 *
	 * @return bool True if added successfull, false otherwise.
	 *
	 * @since 0.1.0
	 */
	public function add_option_setting( $option_setting ) {
		if ( empty( $option_setting ) ) {
			return false;
		}

		$option_setting->set_db_option_key( $this->get_db_option_key() );
		array_push( $this->option_settings, $option_setting );
		return true;
	}


	/**
	 * Register an option section.
	 *
	 * @param Option_Section $option_section A valid instance of an Option_Section object.
	 *
	 * @return bool True if added successfull, false otherwise.
	 *
	 * @since 0.1.0
	 */
	public function add_option_section( $option_section ) {
		if ( empty( $option_section ) ) {
			return false;
		}

		array_push( $this->option_sections, $option_section );
		return true;
	}


	/**
	 * Returns the key for the WordPress option table, under wich this options are saved.
	 *
	 * @return string Key for the option table.
	 *
	 * @since 0.1.0
	 */
	public function get_db_option_key() {
		return self::OPTIONS_SETTINGS_KEY;
	}


	/**
	 * Test if the given key `$module_key`is a valid key for a module.
	 *
	 */
	public function is_module_key_valid( $module_key ) {
		$valid_keys = array(
			'default' => 1,
			'core' => 1,
			'data-privacy' => 1,
		);

		return array_key_exists( $module_key, $valid_keys );
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
		if ( empty( $module_key ) ) {
			return false;
		}

		if ( ! $this->is_module_key_valid( $module_key ) ) {
			return false;
		}

		// No options found for our general db key?
		if ( empty( $options ) ) {
			return false;
		}

		// Does the $module_key exists in our db options?
		if ( array_key_exists( $module_key, $options ) ) {
			return $options[ $module_key ];
		}

		// something went wrong / no data found
		return false;
	}

	public function get_option( $module_key, $option_key, $default = false ) {
		$module_options = $this->get_db_options_for_module( $module_key );

		if ( ! $module_options ) {
			return $default;
		}

		if ( array_key_exists( $option_key, $module_options ) ) {
			return $module_options[ $option_key ];
		}

		return $default;
	}

	public function init_admin_hooks() {
		// IMPORTANT: THis must be called AFTER all admin classes of the modules are loaded
		add_action( 'admin_init', array( &$this, 'callback_admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'callback_admin_menu' ) );
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

		if ( $display_func == null ) {
			$display_func = array( &$this, 'callback_options_page_display' );
		}

		array_push( $this->admin_options_subpages_order, $slug );
		$this->admin_options_subpages[ $slug ] = array(
			'slug'         => $slug,
			'title'        => $title,
			'menu_title'   => $menu_title,
			'capability'   => $capability,
			'parent_slug'  => $parent_slug,
			'display_func' => $display_func,
			'tabs'         => $tabs,
		);
	}

	public function callback_admin_init() {
		register_setting( self::OPTIONS_SETTINGS_KEY, self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );
		register_setting( 'kuetemeier_essentials_data_privacy', self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );
		//	register_setting( $page_slug, self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );
	}

	public function callback_admin_menu() {
		$this->create_admin_menu();
	}

	public function callback_validate_options( $input ) {
		$valid_input = get_option( self::OPTIONS_SETTINGS_KEY );

		$submit = '';
		$page = '';
		$tab = '';

		foreach ( array_keys( $input ) as $key ) {
			if ( substr( $key, 0, 7 ) === 'submit|' ) {
				$parts = explode( '|', $key );

				$count = count( $parts );

				if ( $count > 0 ) {
					$submit = $parts[0];
					if ( $count > 1 ) {
						$page = $parts[1];
					}
					if ( $count > 2 ) {
						$tab = $parts[2];
					}
					break;
				}
			}
		}

		if ( ! empty( $submit ) ) {

			foreach ( $this->option_settings as $setting ) {
				$valid_input = $setting->validate( $page, $tab, $valid_input, $input );
			}
		}

		//echo "Submit: $submit, Page: $page, Tab: $tab";

		//print_r( $input );
		//die('test');

		return $valid_input;
	}

	public function callback_add_settings_section__display_empty_section( $args ) {
	}

	protected function do_add_settings_fields( $page_slug, $current_tab ) {
		foreach ( $this->option_settings as $option_setting ) {
			$option_setting->do_add_settings_field( $page_slug, $current_tab );
		}
	}

	protected function do_add_settings_sections( $page_slug, $current_tab ) {
		foreach ( $this->option_sections as $option_section ) {
			$option_section->do_add_settings_section( $page_slug, $current_tab );
		}
	}

	public function register_settings( $page_slug, $current_tab ) {

		register_setting( self::OPTIONS_SETTINGS_KEY, self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );
		register_setting( 'kuetemeier_essentials_data_privacy', self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );
		register_setting( $page_slug, self::OPTIONS_SETTINGS_KEY, array( &$this, 'callback_validate_options' ) );

		$this->do_add_settings_sections( $page_slug, $current_tab );

		add_settings_section(
			'default',
			'',
			array( &$this, 'callback_add_settings_section__display_empty_section' ),
			$page_slug
		);

		$this->do_add_settings_fields( $page_slug, $current_tab );

	}

	public function callback_create_admin_menu() {

		// add top level menu page
		add_menu_page(
			'Kuetemeier', // page title
			'Kuetemeier', // menu title
			self::OPTIONS_PAGE_CAPABILITY, // capability
			self::OPTIONS_PAGE_SLUG, // menu slug
			array( &$this, 'callback_options_page_display' ) // function
		);

		// Use this hook to add your own subpages via add_admin_options_subpage
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
		do_action( self::ACTION_PREFIX . 'configure_admin_menu' );
		// phpcs::enable

		// add all configured subpages to WP
		foreach ( $this->admin_options_subpages_order as $subpage_slug ) {
			$subpage = $this->admin_options_subpages[ $subpage_slug ];

			// add dashboard (same as top-level)
			add_submenu_page(
				// parent_slug - The slug name for the parent menu (or the file name of a standard WordPress admin page).
				$subpage['parent_slug'],
				// page_title - The text to be displayed in the title tags of the page when the menu is selected.
				$subpage['title'],
				// menu_title - The text to be used for the menu.
				$subpage['menu_title'],
				// capability - The capability required for this menu to be displayed to the user.
				$subpage['capability'],
				// menu_slug - The slug name to refer to this menu by. Should be unique for this menu and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
				$subpage['slug'],
				// display function
				$subpage['display_func']
			);
		}
	}


	public function get_tabs_for_options_subpage( $subpage = self::OPTIONS_PAGE_SLUG ) {

		if ( array_key_exists( $subpage, $this->admin_options_subpages ) ) {
			return $this->admin_options_subpages[ $subpage ]['tabs'];
		}

		return array();
	}

	public function create_admin_menu() {
		do_action( self::ACTION_PREFIX . 'before_create_admin_menu' );

		do_action( self::ACTION_PREFIX . 'create_admin_menu' );

		do_action( self::ACTION_PREFIX . 'after_create_admin_menu' );
	}

	/**
	 * Print out the tabs for the option page defined by `$page_slug`.
	 *
	 * Marks the tab with the same slug as found in `$_GET['tab']`as "current".
	 *
	 * @param string $page_slug Key (slug) of the page to print the tabs for.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	private function options_page_tabs( $page_slug = self::OPTIONS_PAGE_SLUG ) {

		$tabs = $this->get_tabs_for_options_subpage( $page_slug );

		$current_tab = '';
		if ( isset( $_GET['tab'] ) ) {
			$key = sanitize_key( $_GET['tab'] );

			if ( array_key_exists( $key, $tabs ) ) {
				// Set current tab, if we can find the URL parameter in our tabs list.
				$current_tab = $key;
			}
		} else {
			// Default to first tab in list, if there is a list.
			if ( count( $tabs ) > 0 ) {
				$current_tab = key( $tabs );
			}
		}

		$this->register_settings( $page_slug, $current_tab );

		if ( count( $tabs ) > 0 ) {

			echo '<br /></div>';
			echo '<h2 class="nav-tab-wrapper">';

			foreach ( $tabs as $tab => $name ) {
				if ( $tab === $current_tab ) {
					echo '<a class="nav-tab nav-tab-active" href="?page=oenology-settings&tab=$tab">' . esc_html( $name ) . '</a>';
				} else {
					echo '<a class="nav-tab" href="?page=kuetemeier_essentials&tab=' . esc_attr( $tab ) . '">' . esc_html( $name ) . '</a>';
				}
			}

			echo '</h2>';
		}

	}


	public function callback_options_page_display() {

		// Set default to a known slug
		$page_slug = self::OPTIONS_PAGE_SLUG;

		// Get active page from URL.
		$_page_slug = sanitize_key( $_GET['page'] );
		if ( isset( $_page_slug ) ) {

			// Test if it's a "real" page in our subpage list.
			if ( array_key_exists( $_page_slug, $this->admin_options_subpages ) ) {
				$page_slug = $_page_slug;
			}
		}

		?>
		<div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php $this->options_page_tabs( $page_slug ); ?>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( $page_slug );
				do_settings_sections( $page_slug );
				?>
				<?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' ); ?>

				<p class="submit">
					<input name="kuetemeier_essentials[submit|<?php echo $page_slug; ?>|<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'kuetemeier-essentials' ); ?>" />
					<input name="kuetemeier_essentials[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e( 'Reset Defaults', 'kuetemeier-essentials' ); ?>" />
				</p>
			</form>
		</div>
		<?php

	}
}



class Option_Section {
	protected $id = '';

	protected $page = '';

	protected $tab = '';

	protected $title = '';

	protected $content = '';

	protected $display_function;

	function __construct( $id, $title, $page, $tab = '', $content = '', $display_function = null ) {
		$this->id = $id;
		$this->title = $title;
		$this->page = $page;
		$this->tab = $tab;
		$this->content = $content;
		$this->set_display_function( $display_function );
	}


	public function get_id() {
		return $this->id;
	}

	public function get_page() {
		return $this->page;
	}

	public function get_tab() {
		return $this->tab;
	}

	public function get_title() {
		return $this->title;
	}

	public function get_content() {
		return $this->content;
	}

	public function callback__display_function( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo esc_html( $this->content ); ?>
		</div>
		<?php
	}

	public function set_display_function( $display_function ) {
		if ( empty( $display_function ) ) {
			$this->display_function = array( &$this, 'callback__display_function' );
		} else {
			$this->display_function = $display_function;
		}
	}

	public function get_display_function() {
		return $this->display_function;
	}

	/**
	 * Add this setting to the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $page           The slug-name of the settings page on which to show the section. Built-in pages include
	 *                               'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using
	 *                               add_options_page();
	 * @param string $tab            Optional. The slug-name of the current tab (if any is present on the settings page)
	 */
	public function do_add_settings_section( $page = '', $tab = '' ) {
		// Do we have to filter for page slug?
		if ( ! empty( $page ) ) {

			// Yes:
			if ( ! ( $page == $this->get_page() ) ) {
				// Do nothing if page slugs do not match
				return;
			}
		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab ) ) {

			// Yes:
			if ( ! ( $tab == $this->get_tab() ) ) {
				// Do nothing if tab slugs do not match
				return;
			}
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

		//Options::instance()->sections_bug_workaround( $this->get_id(), $this->get_page() );

	}
}

/**
 * A single setting for an option managed by Options.
 */
abstract class Option_Setting {

	protected $db_option_key = 'kuetemeier';

	/**
	 * Id of the module this option setting belongs to
	 */
	protected $module = '';

	/**
	 * Unique ID of the Instance of this Class and the key for the database entry.
	 */
	protected $id = '';

	protected $default = null;

	/**
	 * Common name
	 */
	protected $name = '';

	/**
	 * Label, e.g. for the setting page
	 */
	protected $label = '';

	protected $page = '';

	protected $tab = '';

	protected $section = 'default';

	protected $order = 0;

	protected $empty_value = '';

	/**
	 * Description shown in the settings page.
	 */
	protected $description = '';

	public function __construct( $module, $id, $default, $label, $page = '', $tab = '', $section = '', $description = '', $empty_value = '', $order = 0 ) {
		$this->module = $module;
		$this->id = $id;
		$this->default = $default;
		$this->label = $label;
		$this->page = $page;
		$this->tab = $tab;
		$this->set_section( $section );
		$this->set_description( $description );
		$this->empty_value = $empty_value;
		$this->order = $order;
	}

	public function get_db_option_key() {
		return $this->db_option_key;
	}

	public function set_db_option_key( $value ) {
		$this->db_option_key = $value;
	}

	public function get_module() {
		return $this->module;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_default() {
		return $this->default;
	}

	public function get_empty_value() {
		return $this->empty_value;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_page() {
		return $this->page;
	}

	public function get_tab() {
		return $this->tab;
	}

	public function get_section() {
		return $this->section;
	}

	public function set_section( $section ) {
		$_section = $section;
		if ( empty( $_section ) ) {
			$_section = 'default';
		}
		$this->section = $_section;
	}

	public function get_order() {
		return $this->tab;
	}

	public function get_description() {
		return $this->description;
	}

	public function set_description( $description ) {
		$_description = $description;

		// convert empty string to null
		if ( empty( $_description ) ) {
			$_description = null;
		}

		$this->description = $_description;
	}

	abstract public function callback_display_setting( $args );

	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * Every subclass must declare a function that returns a sanitzie version of the given value.
	 * E.g. use sanitize_text_field for a string.
	 *
	 * @param  mixed $input      an input value to be sanitzied by this function
	 *
	 * @return mixed    sanitized version of $value or null if we cannot sanitzie the input
	 */
	abstract public function sanitize( $input );

	public function validate( $page, $tab, $valid_input, $input ) {

		if ( ! empty( $page ) ) {
			if ( $page != $this->get_page() ) {
				return $valid_input;
			}
		}

		if ( ! empty( $tab ) ) {
			if ( $tab != $this->get_tab() ) {
				return $valid_input;
			}
		}

		$module = $this->get_module();
		$id = $this->get_id();

		/*
		$error = "Test: Page: '$page' | Tab: '$tab' | Module: '$module' | ID: '$id' <br />\n";
		add_settings_error( 'test1', 'test2', $error, 'error' );
		add_settings_error( 'test1', 'test2', '$input: '.esc_html( wp_json_encode ( $input) ) );
		add_settings_error( 'test1', 'test2', '$valid_input (vorher): '.esc_html( wp_json_encode ( $valid_input ) ) );
		*/
		$input_value = $this->sanitize( $this->get_from_array( $input, null ) );
		if ( isset( $input_value ) ) {
			$valid_input = $this->set_in_array( $valid_input, $input_value );

		} else {
			$valid_input = $this->set_in_array( $valid_input, $this->get_empty_value() );
		}

		//      add_settings_error( 'test1', 'test2', '$valid_input (nachher): '.esc_html( wp_json_encode ( $valid_input ) ) );

		return $valid_input;
	}

	public function do_add_settings_field( $page, $tab ) {

		// Do we have to filter for page slug?
		if ( ! empty( $page ) ) {

			// Yes:
			if ( ! ( $page == $this->get_page() ) ) {
				// Do nothing if page slugs do not match
				return;
			}
		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab ) ) {

			// Yes:
			if ( ! ( $tab == $this->get_tab() ) ) {
				// Do nothing if tab slugs do not match
				return;
			}
		}

		add_settings_field(
			// ID used to identify the field throughout the theme
			$this->get_id(),
			// The label to the left of the option interface element
			$this->get_label(),
			// The name of the function responsible for rendering the option interface
			array( &$this, 'callback_display_setting' ),
			// The page on which this option will be displayed
			$this->get_page(),
			// The name of the section to which this field belongs
			$this->get_section(),
			// The array of arguments to pass to the callback. In this case, just a description.
			array(
				$this->get_description(),
			)
		);
	}

	public function get( $default = false ) {
		// get options from WordPress database 'options' table
		$options = get_option( $this->get_db_option_key() );

		// Find our value and return it (or $default, if not found).
		return $this->get_from_array( $options, $default );
	}

	/**
	 * Set this option to value $value
	 */
	public function set( $value ) {
	}

	protected function get_from_array( $array, $default = false ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			return $array[ $module ][ $id ];
		}
		return $default;
	}

	protected function set_in_array( $array, $value ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( ! isset( $array[ $module ] ) || ! is_array( $array[ $module ] ) ) {
			$array[ $module ] = array();
		}

		$array[ $module ][ $id ] = $value;

		return $array;
	}

	protected function unset_in_array( $array, $value ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			unset( $array[ $module ][ $id ] );
		}

		return $array;
	}
}

class Option_Setting_Checkbox extends Option_Setting {

	public function __construct( $module, $id, $default, $label, $page = '', $tab = '', $section = '', $description = '', $empty_value = 0, $order = 0 ) {
		parent::__construct( $module, $id, $default, $label, $page, $tab, $section, $description, $empty_value, $order );

	}

	public function sanitize( $input ) {
		// TODO: sanitize
		return $input;
	}

	public function callback_display_setting( $args ) {
		$options = \Kuetemeier_Essentials\Kuetemeier_Essentials::instance()->options();

		$value = $this->get();
		$complete_id = $this->get_module() . '_' . $this->get_id();

		//die ("checkbox");

		// Next, we update the name attribute to access this element's ID in the context of the display options array
		// We also access the show_header element of the options collection in the call to the checked() helper function
		$html = '<input type="checkbox" id="' . esc_attr( $complete_id ) . '" name="' . $options->get_db_option_key();
		$html .= '[' . esc_attr( $this->get_module() ) . '][' . esc_attr( $this->get_id() ) . ']" value="1" ' . checked( 1, $value, false ) . '/>';

		// Here, we'll take the first argument of the array and add it to a label next to the checkbox
		$html .= '<label for="' . esc_attr( $complete_id ) . '"> ' . esc_html( $args[0] ) . '</label>';

		echo $html;
	}
}

class Option_Setting_Text extends Option_Setting {

	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * Every subclass must declare a function that returns a sanitzie version of the given input value.
	 * E.g. use sanitize_text_field for a string.
	 *
	 * @param  string $input      an input value to be sanitzied by this function
	 *
	 * @return string   sanitized version of $value or null if we cannot sanitzie the input
	 */
	public function sanitize( $input ) {
		if ( ! isset( $input ) ) {
			return null;
		}

		return sanitize_text_field( $input );
	}

	public function callback_display_setting( $args ) {
		// Get current value.
		$value = $this->get();

		// Assemble a compound and escaped id string.
		$esc_id = esc_attr( $this->get_module() . '_' . $this->get_id() );
		// Assemble an escaped name string. The name attribute is importan, it defines the keys for the $input array in validation.
		$esc_name = esc_attr( $this->get_db_option_key() . '[' . $this->get_module() . '][' . $this->get_id() . ']' );

		// Compose output.
		$html = '<input type="text" id="' . $esc_id . '" name="' . $esc_name . '" value="' . esc_attr( $value ) . '" class="regular-text ltr" />';
		$html .= '<p class="description" id="' . $esc_id . '-description">' . esc_html( $args[0] ) . '</p>';

		// And show it to the world.
		echo $html;
	}
}
