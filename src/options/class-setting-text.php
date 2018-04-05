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
 * A Textbox implementation of Setting.
 *
 * This is a good demonstration how easy it is to implement new option types.
 */
class Setting_Text extends Setting {

	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * @param string $input An input value to be sanitzied by this function.
	 *
	 * @return string   sanitized version of $value or null if we cannot sanitzie the input
	 */
	public function sanitize( $input ) {
		if ( ! isset( $input ) ) {
			return null;
		}

		return sanitize_text_field( $input );
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
		// Get current value.
		$value = $this->get();

		// Assemble a compound and escaped id string.
		$esc_id = esc_attr( $this->get_module_id() . '_' . $this->get_id() );
		// Assemble an escaped name string. The name attribute is importan, it defines the keys for the $input array in validation.
		$esc_name = esc_attr( $this->get_wp_plugin()->get_db_option_table_base_key() . '[' . $this->get_module_id() . '][' . $this->get_id() . ']' );

		// Compose output.
		$esc_html = '<input type="text" id="' . $esc_id . '" name="' . $esc_name . '" value="' . esc_attr( $value ) . '" class="regular-text ltr" />';
		$esc_html .= $this->display_label_for_html( $esc_id );
		$esc_html .= $this->display_description_html( $esc_id );

		// phpcs:disable WordPress.XSS.EscapeOutput
		// $esc_html contains only escaped content.
		echo $esc_html;
		// phpcs:enable WordPress.XSS.EscapeOutput
	}
}
