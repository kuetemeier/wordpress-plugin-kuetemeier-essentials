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

namespace Kuetemeier_Essentials\Plugin_Modules\Frontend;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once dirname( __FILE__ ) . '/../class-frontend-module.php';

/**
 * Data privacy Module of the Kuetemeier-Essentials Plugin.
 */
final class Data_Privacy_Frontend extends \Kuetemeier_Essentials\Plugin_Modules\Frontend_Module {

	/**
	 * Option: Add privacy checkbox to comment fields
	 *
	 * @var \Kuetemeier_Essentials\Option_Setting_Checkbox
	 */
	private $option_add_privacy_field_to_comments;

	/**
	 * Option: Add privacy checkbox to comment fields
	 *
	 * @var \Kuetemeier_Essentials\Option_Setting_Checkbox
	 */
	private $option_add_privacy_field_to_comments_data_privacy_statement_url;

	/**
	 * Create Data Privacy Module.
	 *
	 * @param WP_Plugin $wp_plugin A vallid instance of WP_Plugin (should be the main WordPress Plugin object).
	 *
	 * @since 0.1.12
	 * @since 0.2.1 reworked
	 */
	public function __construct( $wp_plugin ) {
		parent::__construct(
			// id
			'data-privacy',
			// name
			__( 'Data Privacy', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$this->init_options();

		$this->partial_add_privacy_field_to_comments();
	}


	/**
	 * Init all Data Privacy options.
	 *
	 * @return void
	 *
	 * @since  0.2.1
	 */
	private function init_options() {

		$options = $this->get_wp_plugin()->options();

		$test = new \Kuetemeier_Essentials\Option_Setting_Checkbox(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// id
			'testdp',
			// default value
			false,
			// label
			__( 'Test', 'kuetemeier-essentials' ),
			// page
			$this->get_admin_page_slug(),
			// tab
			'',
			//'',
			// section
			'ke_dp_wp_comments',
			// description
			__( 'Add privacy checkbox to comment fields', 'kuetemeier-essentials' )
		);

		$options->add_option_setting( $test );

		$this->option_add_privacy_field_to_comments = new \Kuetemeier_Essentials\Option_Setting_Checkbox(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// id
			'add_privacy_field_to_comments',
			// default value
			false,
			// label
			__( 'Privacy Checkbox', 'kuetemeier-essentials' ),
			// page
			'kuetemeier_essentials',
			//$this->admin_page_slug(),
			// tab
			'test',
			//'',
			// section
			//'ke_dp_wp_comments',
			'',
			// description
			__( 'Add privacy checkbox to comment fields', 'kuetemeier-essentials' )
		);

		$options->add_option_setting( $this->option_add_privacy_field_to_comments );

		$this->option_add_privacy_field_to_comments_data_privacy_statement_url = new \Kuetemeier_Essentials\Option_Setting_Text(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// id
			'option_add_privacy_field_to_comments_data_privacy_statement_url',
			// default value
			'/datenschutz/',
			// label
			__( 'URL to data privacy statement', 'kuetemeier-essentials' ),
			// page
			'kuetemeier_essentials',
			//$this->admin_page_slug(),
			// tab
			'test',
			//'',
			// section
			//'ke_dp_wp_comments',
			'',
			// description
			__( 'Valid URL (e.g. /data-privacy/)', 'kuetemeier-essentials' )
		);

		$options->add_option_setting( $this->option_add_privacy_field_to_comments_data_privacy_statement_url );

	}


	public function callback__my_comment_form_field_comment( $comment_field ) {
		$url = $this->option_add_privacy_field_to_comments_data_privacy_statement_url->get();

		$comment_field .= '<p class="pprivacy"><input type="checkbox" name="privacy" value="privacy-key" class="privacyBox" aria-req="true"> Hiermit akzeptiere ich die ';

		if ( empty( $url ) ) {
			$comment_field .= 'Datenschutzbedingungen';
		} else {
			$comment_field .= '<a target="blank" href="' . $url . '">Datenschutzbedingungen</a>';

		}
		$comment_field .= '<p>';

		return $comment_field;
	}

	public function callback__valdate_privacy_comment_javascript() {
		if ( is_single() && comments_open() ) {
			wp_enqueue_script( 'jquery' );
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#submit").click( function (e) {
					if (!$('.privacyBox').prop('checked')){
						e.preventDefault();
						alert('You must agree to our privacy term by checking the box ....');
						return false;
					}
				});
			});
			</script>
			<?php
		}
	}


	public function callback__verify_comment_meta_data( $commentdata ) {
		if ( ! isset( $_POST['privacy'] ) )
			wp_die( __( 'Error: You must agree to our privacy term by checking the box ....' ) );

		return $commentdata;
	}


	public function callback__save_comment_meta_data( $comment_id ) {
		add_comment_meta( $comment_id, 'privacy', $_POST[ 'privacy' ] );
	}


	/**
	 * Add a privacy field to comments
	 */
	private function partial_add_privacy_field_to_comments() {
		if ( $this->option_add_privacy_field_to_comments->get() ) {

			//add your checkbox after the comment field
			add_filter( 'comment_form_field_comment', array( &$this, 'callback__my_comment_form_field_comment' ) );

			//javascript validation
			add_action( 'wp_footer', array( &$this, 'callback__valdate_privacy_comment_javascript' ) );

			//no js fallback validation
			add_filter( 'preprocess_comment', array( &$this, 'callback__verify_comment_privacy' ) );

			//save field as comment meta
			add_action( 'comment_post', array( &$this, 'callback__save_comment_privacy' ) );

		}
		return false;
	}

}
