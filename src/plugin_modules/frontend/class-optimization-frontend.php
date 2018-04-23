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
require_once dirname( __FILE__ ) . '/../../class-options.php';


/**
 * Data privacy Module of the Kuetemeier-Essentials Plugin.
 */
final class Optimization_Frontend extends \Kuetemeier_Essentials\Plugin_Modules\Frontend_Module {

	/**
	 * Option: Enable reference Media from URLs
	 *
	 * @var \Kuetemeier_Essentials\Option_Setting_Checkbox
	 */
	private $option_disable_emojis;

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
			'optimization',
			// name
			__( 'Optimization', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$this->init_options();

		if ( $this->option_disable_emojis->get() ) {
			add_action( 'init', array( &$this, 'callback__disable_emojis' ) );
		}
	}


	/**
	 * Init all Media options.
	 *
	 * @return void
	 *
	 * @since  0.2.1
	 */
	private function init_options() {

		$options = $this->get_wp_plugin()->get_options();

		$this->option_disable_emojis = new \Kuetemeier_Essentials\Options\Setting_Checkbox(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// option id
			'disable_emoji',
			// default value
			false,
			// label
			__( 'Disable WordPress Emojis', 'kuetemeier-essentials' ),
			// page
			$this->get_admin_page_slug(),
			// tab
			'ke-tab-optimization-common',
			// section
			'ke-optimization-common',
			// description
			__( 'Check (recommended) to disable the Emojis support in WordPress.', 'kuetemeier-essentials' )
		);

 		$options->add_option_setting( $this->option_disable_emojis );
	}


	/**
	 * Disable the emoji's
	 *
	 * @see https://kinsta.com/knowledgebase/disable-emojis-wordpress
	 */
	public function callback__disable_emojis() {
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
	function callback__disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}
}
