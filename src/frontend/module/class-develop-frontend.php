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

namespace Kuetemeier_Essentials\Frontend\Module;

require_once( dirname(__FILE__) . '/class-frontend-module.php' );

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Class Kuetemeier_Essentials
 */
class Develop_Frontend extends Frontend_Module {

	public function get_id() {
		return 'develop';
	}

	public function get_name() {
		return 'Develop';
	}


	/**
	 * custom option and settings:
	 * callback functions
	 */

	// developers section cb

	// section callbacks can accept an $args parameter, which is an array.
	// $args have the following keys defined: title, id, callback.
	// the values are defined at the add_settings_section() function.
	public function _settings_section_developers_cb( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'wporg' ); ?></p>
		<?php
	}

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

	public function settings_init() {


		// register a new section in the "wporg" page
		add_settings_section(
			'kuetemeier_essentials_section_developers',
			__( 'The Matrix has you.', 'kuetemeier_essentials' ),
			array( &$this, '_settings_section_developers_cb' ),
			'kuetemeier_essentials'
	 	);

		add_settings_section(
			'kuetemeier_essentials_section_test',
			__( 'The Matrix has you.', 'kuetemeier-essentials' ),
			array( &$this, '_settings_section_developers_cb' ),
			'kuetemeier_essentials'
	 	);


		// register a new field in the "wporg_section_developers" section, inside the "wporg" page
		add_settings_field(
			'kuetemeier_essentials_field_pill', // as of WP 4.6 this value is used only internally
			// use $args' label_for to populate the id inside the callback
			__( 'Pill', 'kuetemeier_essentials' ),
			array( &$this, '_settings_field_pill_cb'),
			'kuetemeier_essentials',
			'kuetemeier_essentials_section_developers',
			[
				'label_for' => 'kuetemeier_essentials_field_pill',
				'class' => 'kuetemeier_essentials_row',
				'kuetemeier_essentials_custom_data' => 'custom',
			]
		);


	}

}
