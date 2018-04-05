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

namespace Kuetemeier_Essentials\Options;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );


/**
 * A single setting for an option managed by Options.
 *
 * This is an abstract class and has to be extended for specific use cases.
 */
abstract class Setting {

	/** @var WP_Plugin A valid instance of an object, that is a subclass of WP_Plugin, normally the main plugin object. */
	private $wp_plugin = null;


	/**
	 * Cache for the db_option_table_base_key.
	 *
	 * @internal
	 * @var string
	 */
	private $cache__db_option_table_base_key = '';


	/** @var string Key of the module this option setting belongs to. */
	private $module_id = '';


	/** @var string Unique key. */
	private $id = '';


	/** @var mixed Default value. */
	private $default_value = null;


	/** @var string Common name. */
	private $name = '';


	/** @var string Label for the settings page. */
	private $label = '';


	/** @var string Key of the admin page this option settings belongs to. */
	private $page_id = '';


	/** @var string Key of the tab on the admin page this option settings belongs to. */
	private $tab_id = '';


	/** @var string Key of the section on the admin page this option settings belongs to. */
	private $section_id = 'default';


	/** @var int Value for display order in the section. */
	private $display_order = 0;


	/** @var bool States if this setting should be enabled on an admin options page. */
	private $enabled = true;


	/** @var bool States if this setting a pro / premium setting. */
	private $pro_setting = false;


	/** @var mixed Value for an 'empty' setting. */
	private $empty_value = '';


	/** @var string Description shown aside in the admin page. */
	private $label_for = '';


	/** @var string Description shown in the admin page. */
	private $description = '';


	/**
	 * Initialize an OptionSetting.
	 *
	 * @param WP_Plugin $wp_plugin      A valid instance of WP_Plugin.
	 * @param string    $module_id         Key of the module this option belongs to.
	 * @param string    $id             Unique ID.
	 * @param string    $default_value  Default value.
	 * @param string    $label          Label for the admin page.
	 * @param string    $page_id        (optional) Key (slug) of the admin page, this setting should be displayed on.
	 * @param string    $tab_id         (optional) Key (slug) for the tab on the admin page, this setting should be displayed on.
	 * @param string    $section_id     (optional) Key for the section in the admin page, this option belongs to.
	 * @param string    $label_for      (optional) 'Label for' Description to be shown aside the setting on the admin page.
	 * @param string    $description    (optional) Description to be shown next to the setting on the admin page.
	 * @param bool      $enabled        (optional) Enable this setting in the admin options page? Default: true.
	 * @param bool      $pro_setting    (optional) Is this setting a Pro / Premium setting? Default: false.
	 * @param mixed     $empty_value    (optional) The 'empty' value of this option.
	 * @param int       $display_order  (optional) Display order in the section.
	 */
	public function __construct(
		$wp_plugin,
		$module_id,
		$id, $default_value,
		$label, $page_id = '',
		$tab_id = '',
		$section_id = '',
		$label_for = '',
		$description = '',
		$enabled = true,
		$pro_setting = false,
		$empty_value = '',
		$display_order = 0 ) {

		$this->wp_plugin = $wp_plugin;
		$this->module_id = $module_id;
		$this->id = $id;
		$this->default_value = $default_value;
		$this->label = $label;
		$this->page_id = $page_id;
		$this->tab_id = $tab_id;
		$this->set_section_id( $section_id );
		$this->label_for = $label_for;
		$this->set_description( $description );
		$this->enabled = $enabled;
		$this->pro_setting = $pro_setting;
		$this->empty_value = $empty_value;
		$this->display_order = $display_order;

		// add a cached value for quicker 'get' operations
		$this->cache__db_option_table_base_key = $wp_plugin->get_db_option_table_base_key();
	}


	/**
	 * Returns a valid instance of an object, that implementes WP_Plugin.
	 *
	 * @return WP_Plugin
	 *
	 * @since 0.1.0
	 */
	public function get_wp_plugin() {
		return $this->wp_plugin;
	}

	/**
	 * Key of the module this option setting belongs to.
	 *
	 * @return string The module key.
	 *
	 * @since 0.1.0
	 */
	public function get_module_id() {
		return $this->module_id;
	}

	/**
	 * Unique ID
	 *
	 * @return string The ID.
	 *
	 * @since 0.1.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Default value of this option
	 *
	 * @return string The default value.
	 *
	 * @since 0.1.0
	 */
	public function get_default_value() {
		return $this->default_value;
	}

	/**
	 * The 'empty' value of this option.
	 *
	 * For some types it may be `null`, for some types it may be `0` or `''`. Totally your choise.
	 *
	 * @return string The empy value.
	 *
	 * @since 0.1.0
	 */
	public function get_empty_value() {
		return $this->empty_value;
	}

	/**
	 * Admin page label.
	 *
	 * @return string The admin page label.
	 *
	 * @since 0.1.0
	 */
	public function get_label() {
		return $this->label;
	}


	/**
	 * Admin page label for (shown aside the input field).
	 *
	 * @return string The admin page label.
	 *
	 * @since 0.1.0
	 */
	public function get_label_for() {
		return $this->label_for;
	}


	/**
	 *
	 */
	public function get_section_object() {
		$this->get_wp_plugin()->get_options()->get_section_by_id( $this->get_section_id() );

	}

	/**
	 * Key for the admin page this option belongs to.
	 *
	 * @return string The page key / slug.
	 *
	 * @since 0.1.0
	 */
	public function get_page_id() {
		return $this->page_id;
		//return   )->get_page();
	}

	/**
	 * Key for the tab this option belongs to.
	 *
	 * @return string The tab key / slug..
	 *
	 * @since 0.1.0
	 */
	public function get_tab_id() {
		return $this->tab_id;
	}

	/**
	 * Key for section this option belongs to.
	 *
	 * @return string The section key.
	 *
	 * @since 0.1.0
	 */
	public function get_section_id() {
		return $this->section_id;
	}

	/**
	 * Set a new section key.
	 *
	 * @param sting $section New section key for this option.
	 *
	 * @since 0.1.0
	 */
	public function set_section_id( $section ) {
		$_section = $section;
		if ( empty( $_section ) ) {
			$_section = 'default';
		}

		$this->section = $_section;
	}

	/**
	 * Display order in the section.
	 *
	 * @return int The display order.
	 *
	 * @since 0.1.0
	 */
	public function get_display_order() {
		return $this->display_order;
	}

	/**
	 * Description on the admin page.
	 *
	 * @return string The description.
	 *
	 * @since 0.1.0
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set a new description for this option.
	 *
	 * @param sting $description The new description (html).
	 *
	 * @since 0.1.0
	 */
	public function set_description( $description ) {
		$_description = $description;

		// convert empty string to null
		if ( empty( $_description ) ) {
			$_description = '';
		}

		$this->description = $_description;
	}


	/**
	 * Function to be used to display the setting on the admin page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress display funciton args.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	abstract public function callback__display_setting( $args );


	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * Every subclass must declare a function that returns a sanitzie version of the given value.
	 * E.g. use sanitize_text_field for a string.
	 *
	 * @param  mixed $input An input value to be sanitzied by this function.
	 *
	 * @return mixed Sanitized version of $value or null if we cannot sanitzie the input.
	 */
	abstract public function sanitize( $input );


	/**
	 * Tests, if the option matches the page and the tab. If so, it validates its value
	 * in the `$input` array and retuns it in the `$valid_input` array. Just as a the
	 * normal WordPress Settings API value callback would do it.
	 *
	 * If `$page` or `$tab` does not match, it returns the untouched `$valid_input`array.
	 *
	 * Note: This method is normally not called directly. It's called in the input
	 * validation process from the Options class it is registered to.
	 *
	 * @param string $page Key / slug of the page the value should be validated for.
	 * @param string $tab Key / slug of the tab the value should be validated for.
	 * @param array  $valid_input WordPress valid input array.
	 * @param array  $input WordPress array of form input values.
	 *
	 * @return array `$valid_input` with a validated version, if page and tab matches and value is valid, untouched `$valid_input` otherwise.
	 *
	 * @since 0.1.0
	 */
	public function validate( $page, $tab, $valid_input, $input ) {

		$error = false;
		$error_msg = 'Everything fine.';

		if ( ! empty( $page ) ) {
			if ( $page !== $this->get_page_id() ) {
				return $valid_input;
			}
		}

		if ( ! empty( $tab ) ) {
			if ( $tab !== $this->get_tab_id() ) {
				return $valid_input;
			}
		}

		$module = $this->get_module_id();
		$id = $this->get_id();

		$input_value = $this->sanitize( $this->get_from_array( $input, null ) );
		if ( isset( $input_value ) ) {
			$valid_input = $this->set_in_array( $valid_input, $input_value );

		} else {
			$valid_input = $this->set_in_array( $valid_input, $this->get_empty_value() );
		}

		if ( $error ) {
			// https://codex.wordpress.org/Function_Reference/add_settings_error
			add_settings_error(
				// Slug title of the setting to which this error applies.
				$this->get_id(),
				// Slug-name to identify the error.
				'error',
				// message
				$error_msg
				// optional type, may be: 'error' or 'updated', default: 'error'
			);
		}

		return $valid_input;
	}


	/**
	 * Register option setting field with WordPress API if `$page` and `$tab` matches for this option.
	 *
	 * Note: This method is normally not called directly. It's called in the register
	 * process from the Options class it is registered to.
	 *
	 * @param string $page Key / slug of the page for which the option should be registered.
	 * @param string $tab Key / slug of the tab for which the option should be registered.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function do_add_settings_field( $page, $tab ) {

		// Do we have to filter for page slug?
		if ( ! empty( $page ) ) {

			// Yes:
			if ( ! ( $page === $this->get_page_id() ) ) {
				// Do nothing if page slugs do not match
				return;
			}
		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab ) ) {

			// Yes:
			if ( ! ( $tab === $this->get_tab_id() ) ) {
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
			array( &$this, 'callback__display_setting' ),
			// The page on which this option will be displayed
			$this->get_page_id(),
			// The name of the section to which this field belongs
			$this->get_section_id(),
			// The array of arguments to pass to the callback. In this case, just a description.
			array(
				$this->get_description(),
			)
		);
	}


	/**
	 * Get the current value for this option (from the WordPress Option API)
	 *
	 * @param mixed $default (optional) Default value, if this option cannot be found in the Options API, default: `null`.
	 * @param bool  $force   (optional) Force this method to not use the internal cache (likely not used and left untouched).
	 *
	 * @return mixed Value or `$default`.
	 *
	 * @since 0.1.0
	 * @since 0.2.1 Reworked, cache option added, default value changed.
	 */
	public function get( $default = null, $force = false ) {

		if ( ! isset( $default ) ) {
			$default = $this->get_default_value();
		}

		if ( $force ) {
			$option_values = get_option( $this->get_wp_plugin()->get_db_option_table_base_key() );

		} else {
			$option_values = get_option( $this->cache__db_option_table_base_key );
		}

		if ( ! isset( $option_values ) ) {
			return $default;
		}

		// Find our value and return it (or $default, if not found).
		return $this->get_from_array( $option_values, $default );
	}


	/**
	 * Helper function. Get a value from an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to get the value of.
	 * @param mixed $default (optional) Default value, if value is not in `$array`, default: `null`.
	 *
	 * @return mixed The vaule in the array - if found, otherwise `$default`.
	 *
	 * @since 0.1.0
	 * @since 0.2.1 Default value changed.
	 */
	protected function get_from_array( $array, $default = null ) {
		$module = $this->get_module_id();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			return $array[ $module ][ $id ];
		}
		return $default;
	}

	/**
	 * Helper function. Sets a value in an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to set the value in.
	 * @param mixed $value The new value.
	 *
	 * @return array The array including the new value.
	 *
	 * @since 0.1.0
	 */
	protected function set_in_array( $array, $value ) {
		$module = $this->get_module_id();
		$id = $this->get_id();

		if ( ! isset( $array[ $module ] ) || ! is_array( $array[ $module ] ) ) {
			$array[ $module ] = array();
		}

		$array[ $module ][ $id ] = $value;

		return $array;
	}

	/**
	 * Helper function. Unsets a value in an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to unset the value in.
	 *
	 * @return array The array excluding the new value.
	 *
	 * @since 0.1.0
	 */
	protected function unset_in_array( $array ) {
		$module = $this->get_module_id();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			unset( $array[ $module ][ $id ] );
		}

		return $array;
	}


	/**
	 * Helper function for callbackk__display_setting, returns html for the label.
	 *
	 * @param string $composed_id A composed id for the html id fields.
	 *
	 * @return string HTML or '', if label property is empty.
	 *
	 * @since 0.2.1
	 */
	protected function display_label_for_html( $composed_id ) {

		if ( empty( $this->get_label_for() ) ) {
			return '';
		}

		$esc_id = esc_attr( $composed_id );
		return '<label id="' . $esc_id . '-label" for="' . $esc_id . '"> ' . esc_html( $this->get_label_for() ) . '</label>';

	}


	/**
	 * Helper function for callbackk__display_setting, returns html for the description.
	 *
	 * @param string $composed_id A composed id for the html id fields.
	 *
	 * @return string HTML or '', if description property is empty.
	 *
	 * @since 0.2.1
	 */
	protected function display_description_html( $composed_id ) {

		if ( empty( $this->get_description() ) ) {
			return '';
		}

		$esc_id = esc_attr( $composed_id );

		return '<p class="description" id="' . $esc_id . '-description">' . esc_html( $this->get_description() ) . '</p>';
	}

	/**
	 * States if this setting will be enabled on an admin options page.
	 *
	 * @return bool State.
	 *
	 * @since 0.2.1
	 */
	public function is_enabled() {
		return $this->enabled;
	}


	/**
	 * States if this setting belongs to a pro / premium version of this plugin.
	 *
	 * @return bool State.
	 *
	 * @since 0.2.1
	 */
	public function is_pro_setting() {
		return $this->pro_setting;
	}

}
