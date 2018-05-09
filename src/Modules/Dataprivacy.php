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

namespace KuetemeierEssentials\Modules;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Core Module of the Kuetemeier-Essentials Plugin.
 */
final class Dataprivacy extends \Kuetemeier\WordPress\PluginModule {

	public static function manifest()
	{
		return array(
			'id'          => 'data-privacy',
			'short'		  => __('Data Privacy', 'kuetemeier-essentials'),
			'description' => __('Kuetemeier Essentials Data Privacy Module.', 'kuetemeier-essentials'),
			'page'        => 'kuetemeier-data-privacy',

			'config'      => array(
				'wp-comments-add-data-privacy-field' => 1,
				'wp-comments-data-privacy-statement-url' => __('\/data-privacy\/', 'kuetemeier-essentials'),
			)
		);
	}

	public function getAdminOptionSettings()
	{

		return array(
			'subpages' => array(
				array(
					'id'         => 'kuetemeier-data-privacy',
					'parent'     => 'kuetemeier',
					'title'      => 'Data Privacy',
					'menuTitle'  => 'Data Privacy',
					'content'    => 'Make your WordPress GDPR compliant.',
					'priority'   => 200,
				)
			),
			'tabs' => array(
				array(
					'id'         => 'data-privacy-general',
					'page'       => 'kuetemeier-data-privacy',
					'title'      => __('General Data Privacy', 'kuetemeier-essentials'),
				),
			),
			'sections' => array(
				array(
					'id'         => 'data-privacy-introduction',
					'tab'        => 'data-privacy-general',
					'title'      => __('Introduction', 'kuetemeier-essentials'),
					'content'	 => __('An introduction and more help to data privacy. (Comming soon).', 'kuetemeier-essentials'),
				),
				array(
					'id'         => 'data-privacy-comments',
					'tab'        => 'data-privacy-general',
					'title'      => __('WordPress Comments', 'kuetemeier-essentials'),
					'content'	 => __('It is recommended to add a privacy checkbox to the commentary function.', 'kuetemeier-essentials'),
				),
			),
			'options' => array(
				array(
					'id'          => 'wp-comments-add-data-privacy-field',
					'section'     => 'data-privacy-comments',
					'title'       => __('Privacy for WordPress Comments', 'kuetemeier-essentials'),
					'type'        => 'CheckBox',
					'label'       => __('Add Privacy Checkbox', 'kuetemeier-essentials' ),
					'description' => __('(recommended) Adds a privacy checkbox to the WordPress comment system.', 'kuetemeier-essentials'),
				),
				array(
					'id'          => 'wp-comments-data-privacy-statement-url',
					'section'     => 'data-privacy-comments',
					'title'       => __('URL to data privacy statement', 'kuetemeier-essentials'),
					'type'        => 'Text',
					'label'	      => __('', 'kuetemeier-essentials'),
					'description' => __( 'A valid URL, e.g. /privacy/ or https://yourdomain.com/privacy/.\nPoint this to you privacy statement URL (A link to it is shown in the notice).', 'kuetemeier-essentials'),
				)
			)
		);
	}


	public function frontendInit()
	{
		parent::frontendInit();
		$this->wpCommentsAddDataPrivacyField();
	}


	/**
	 * Add a privacy field to comments
	 */
	private function wpCommentsAddDataPrivacyField()
	{
		if ($this->getOption('wp-comments-add-data-privacy-field')) {

			//add your checkbox after the comment field
			add_filter( 'comment_form_field_comment', array( &$this, 'callback__comment_form_field_comment' ) );

			//javascript validation
			add_action( 'wp_footer', array( &$this, 'callback__valdate_privacy_comment_javascript' ) );

			//no js fallback validation
			add_filter( 'preprocess_comment', array( &$this, 'callback__verify_comment_privacy' ) );

			//save field as comment meta
			add_action( 'comment_post', array( &$this, 'callback__save_comment_privacy' ) );

		}
		return false;
	}


	public function callback__comment_form_field_comment($commentField)
	{
		$url = $this->getOption('wp-comments-data-privacy-statement-url');

		$commentField .= '<p class="pprivacy"><input type="checkbox" name="privacy" value="privacy-key" class="privacyBox" aria-req="true"> Hiermit akzeptiere ich die ';

		if ( empty( $url ) ) {
			$commentField .= 'Datenschutzbedingungen';
		} else {
			$commentField .= '<a target="blank" href="' . $url . '">Datenschutzbedingungen</a>';
		}

		$commentField .= '<p>';

		return $commentField;
	}

	private function getMsgWPCommentDataPrivacy()
	{
		return __('You must agree to our privacy term by checking the box...', 'kuetemeier-essentials');
	}

	public function callback__valdate_privacy_comment_javascript()
	{
		$msg = $this->getMsgWPCommentDataPrivacy();

		if ( is_single() && comments_open() ) {
			wp_enqueue_script( 'jquery' );
			?>
			<script type="text/javascript">
			jQuery(document).ready(function($){
				$("#submit").click( function (e) {
					if (!$('.privacyBox').prop('checked')){
						e.preventDefault();
						alert('<?php esc_html_e($msg) ?>');
						return false;
					}
				});
			});
			</script>
			<?php
		}
	}


	public function callback__verify_comment_meta_data($commentData)
	{
		$msg = $this->getMsgWPCommentDataPrivacy();

		if ( ! isset( $_POST['privacy'] ) )
			wp_die(__( 'Error: '.esc_html($msg), 'kuetemeier-essentials'));

		return $commentData;
	}


	public function callback__save_comment_meta_data( $comment_id )
	{
		add_comment_meta($comment_id, 'privacy', $_POST[ 'privacy' ]);
	}
}
