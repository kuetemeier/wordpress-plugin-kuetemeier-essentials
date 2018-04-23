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

namespace Kuetemeier_Essentials\Plugin_Modules\Admin;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once plugin_dir_path( __FILE__ ) . '/../class-admin-module.php';
require_once plugin_dir_path( __FILE__ ) . '/../../config.php';
require_once plugin_dir_path( __FILE__ ) . '/../../class-options.php';

/**
 * Data Privacy Admin Module for WordPress Plugin Kuetemeier-Essentials.
 */
class Optimization_Admin extends \Kuetemeier_Essentials\Plugin_Modules\Admin_Module {

	/**
	 * Create Optimization Module.
	 *
	 * @param WP_Plugin $wp_plugin A vallid instance of WP_Plugin (should be the main WordPress Plugin object).
	 *
	 * @since 0.1.12
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

		// --------------------------------------------------------
		// Admin option page

		// add admin menu page
		$this->get_wp_plugin()->get_options()->add_admin_subpage(
			$this->get_admin_page_slug(),
			'Kuetemeier > ' . __( 'Optimization', 'kuetemeier-essentials' ),
			__( 'Optimization', 'kuetemeier-essentials' ),
			array(
				'tab-optimization-common' => __( 'Common Optimizations', 'kuetemeier-essentials' ),
			),
			200
		);

		// --------------------------------------------------------
		// Sections

		$options = $this->get_wp_plugin()->get_options();

		$sections = array(
			array(
				// ID
				'ke-optimization-common',
				// title
				__( 'Common Optimizations', 'kuetemeier-essentials' ),
				// page
				$this->get_admin_page_slug(),
				// tab
				'tab-optimization-common',
				// content
				__( 'On this tab you find some enhancements for the WordPress Media Library. Also look at the other tabs, you will find some fancy stuff there.', 'kuetemeier-essentials' )
			),
		);

		foreach ($sections as $section) {
			$options->add_option_section(new \Kuetemeier_Essentials\Options\Section( ...$section ));
		}

/*
		if ( $this->get_wp_plugin()->get_options()->get_option('media', 'external_media_enabled', false) ) {
			add_action( 'post-plupload-upload-ui', array( &$this, 'post_upload_ui' ) );
			add_action( 'post-html-upload-ui', array( &$this, 'post_upload_ui' ) );
			add_action( 'wp_ajax_add_external_media_without_import', array( &$this, 'wp_ajax_add_external_media_without_import' ) );
			add_action( 'admin_post_add_external_media_without_import', array( &$this, 'admin_post_add_external_media_without_import' ) );
		}
*/
	}


	public function callback__admin_init() {

	}

	public function callback__admin_menu() {
	}

}
