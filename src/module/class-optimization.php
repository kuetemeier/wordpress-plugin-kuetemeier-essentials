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

namespace Kuetemeier_Essentials\Module;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Data privacy Module of the Kuetemeier-Essentials Plugin.
 */
class Optimization extends {

	public static function manifest() {
		return array(
			'id'         => 'optimization',
			'short'		 => __( 'Optimization', 'kuetemeier-essentials' ),
			'desciption' => __( 'WordPress Optimization options', 'kuetemeier-essentials' ),

			'config'     => array(
				'disable_emoji' => 0,
				'disable_embeds' => 0,
			)
		);
	}

	public function frontend_init() {
		$this->frontend_init_disable_emojis();
		$this->frontend_init_disable_embeds();
	}

	public function admin_init() {

		$admin_page_options = array(
			'pages' => array(

			),
			'tabs' => array(

			),
			'sections' => array(

			),
			'options' => array(
				array(
					'id'          => 'disable_emoji',
					'pro'		  => false,
					'alpha'		  => false,
					'beta'		  => false,
					'type'        => 'Checkbox',
					'label'       => __( 'Disable WordPress Emojis', 'kuetemeier-essentials' ),
					'section'     => 'optimization/common/emojis',
					'label'       => __( 'Disable WordPress Emojis', 'kuetemeier-essentials' ),
					'description' => __( 'Check (recommended) to disable the Emojis support in WordPress.', 'kuetemeier-essentials' )
				),
				array(
					'id'          => 'disable_embeds',
					'pro'		  => false,
					'alpha'		  => false,
					'beta'		  => false,
					'type'        => 'Checkbox',
					'label'       => __( 'Disable WordPress Emojis', 'kuetemeier-essentials' ),
					'section'     => 'optimization/common/emojis',
					'label'       => __( 'Disable WordPress Emojis', 'kuetemeier-essentials' ),
					'description' => __( 'Check (recommended) to disable the Emojis support in WordPress.', 'kuetemeier-essentials' )
				)
			);
	}


	/* ------------------------------------------------------------------------------------------------------------------------
	 * BEGIN - disable emojis
	 * ------------------------------------------------------------------------------------------------------------------------ */

	private function frontend_init_disable_emojis() {
		if ( $this->config->get('optimization/disable_emoji') ) {
			add_action( 'init', array( &$this, 'callback__init__disable_emojis' ) );
		}
	}

	/**
	 * Disable the emoji's
	 *
	 * @see https://kinsta.com/knowledgebase/disable-emojis-wordpress
	 */
	public function callback__init__disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', array( &$this, 'callback__disable_emojis_tinymce' ) );
		add_filter( 'wp_resource_hints', array( &$this, 'callback__disable_emojis_remove_dns_prefetch' ), 10, 2 );
	}


	/**
	 * Filter function used to remove the tinymce emoji plugin.
	 *
	 * @param array $plugins
	 * @return array Difference betwen the two arrays
	 */
	public function callback__disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}


	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @param array $urls URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 * @return array Difference betwen the two arrays.
	 */
	public function callback__disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}


	/* ------------------------------------------------------------------------------------------------------------------------
	 * END - disable emojis
	 *
	 * BEGIN - disable embeds
	 * ------------------------------------------------------------------------------------------------------------------------ */


	public function frontend_init_disable_embeds() {
		if ( $this->config->get('optimization/disable_emoji') ) {
			add_action( 'init', array( &$this, 'callback__init__disable_embeds', 9999 ) );
		}
	}


	public function callback__init__disable_embeds() {
		// Remove the REST API endpoint.
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );

		// Turn off oEmbed auto discovery.
		add_filter( 'embed_oembed_discover', '__return_false' );

		// Don't filter oEmbed results.
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );

		// Remove oEmbed discovery links.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

		// Remove oEmbed-specific JavaScript from the front-end and back-end.
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		add_filter( 'tiny_mce_plugins', array( &$this, 'disable_embeds_tiny_mce_plugin' ) );

		// Remove all embeds rewrite rules.
		add_filter( 'rewrite_rules_array', array( &$this, 'disable_embeds_rewrites' ) );

		// Remove filter of the oEmbed result before any HTTP requests are made.
		remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );
	}


	public function callback__disable_embeds_tiny_mce_plugin( $plugins ) {
		return array_diff( $plugins, array( 'wpembed' ) );
	}


	public function callback__disable_embeds_rewrites( $rules ) {
		foreach( $rules as $rule => $rewrite ) {
			if( false !== strpos( $rewrite, 'embed=true' ) ) {
				unset( $rules[$rule] );
			}
		}
		return $rules;
	}


	/* ------------------------------------------------------------------------------------------------------------------------
	 * END - disable embeds
	 * ------------------------------------------------------------------------------------------------------------------------ */

}
