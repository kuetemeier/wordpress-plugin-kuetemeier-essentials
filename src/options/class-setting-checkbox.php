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


require_once dirname( __FILE__ ) . '/class-setting.php';

/**
 * A Checkbox implementation of Option_Setting.
 */
class Setting_Checkbox extends Setting {

	// phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	// Default and empty values are different.

	/**
	 * Initialize a Checkbox option
	 *
	 * @param WP_Plugin $wp_plugin A valid instance of WP_Plugin.
	 * @param string    $module Key of the module this option belongs to.
	 * @param string    $id Unique ID.
	 * @param string    $default_value Default value.
	 * @param string    $label Label for the admin page.
	 * @param string    $page (optional) Key (slug) of the admin page, this setting should be displayed on.
	 * @param string    $tab (optional) Key (slug) for the tab on the admin page, this setting should be displayed on.
	 * @param string    $section (optional) Key for the section in the admin page, this option belongs to.
	 * @param string    $description (optional) Description to be shown next to the setting on the admin page.
	 * @param mixed     $empty_value (optional) The 'empty' value of this option.
	 * @param int       $display_order (optional) Display order in the section.
	 */
	public function __construct( $wp_plugin, $module, $id, $default_value, $label, $page = '', $tab = '', $section = '', $description = '', $empty_value = 0, $display_order = 0 ) {
		parent::__construct( $wp_plugin, $module, $id, $default_value, $label, $page, $tab, $section, $description, $empty_value, $display_order );

	}
	// phpcs:enable Generic.CodeAnalysis.UselessOverridingMethod


	/**
	 * Sanitize the input value for a Checkbox value.
	 *
	 * Valid values for Checkboxes are 0 and 1
	 *
	 * @param string $input An input vlalue.
	 *
	 * @return int A clean and sanitized version or the 'empty' value, if it cannot be sanitized.
	 *
	 * @since 0.1.12 Does real sanitization.
	 */
	public function sanitize( $input ) {

		switch ( $input ) {
			case 0:
				return 0;
			case 1:
				return 1;
			case '0':
				return 0;
			case '1':
				return 1;
			case true:
				return 1;
			case false:
				return 0;
			default:
				return $this->empty_value();
		}
	}

	/**
	 * Displays this option on an admin page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress display function args.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__display_setting( $args ) {
		$options = $this->get_wp_plugin()->get_options();

		$value = $this->get();

		// Assemble a compound and escaped id string.
		$esc_id = esc_attr( $this->get_module_id() . '_' . $this->get_id() );

		// Next, we update the name attribute to access this element's ID in the context of the display options array
		// We also access the show_header element of the options collection in the call to the checked() helper function
		$esc_html = '<input type="checkbox" id="' . $esc_id . '" name="' . $this->get_wp_plugin()->get_db_option_table_base_key();
		$esc_html .= '[' . esc_attr( $this->get_module_id() ) . '][' . esc_attr( $this->get_id() ) . ']" value="1" ' . checked( 1, $value, false ) . '/>';

		$esc_html .= $this->display_label_for_html( $esc_id );
		$esc_html .= $this->display_description_html( $esc_id );

		// phpcs:disable WordPress.XSS.EscapeOutput
		// $esc_html contains only escaped content.
		echo $esc_html;
		// phpcs:enable WordPress.XSS.EscapeOutput

	}

}
