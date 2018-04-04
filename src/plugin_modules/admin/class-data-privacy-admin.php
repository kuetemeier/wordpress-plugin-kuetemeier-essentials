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
class Data_Privacy_Admin extends \Kuetemeier_Essentials\Plugin_Modules\Admin_Module {

	/**
	 * Create Data Privacy Module.
	 *
	 * @param WP_Plugin $wp_plugin A vallid instance of WP_Plugin (should be the main WordPress Plugin object).
	 *
	 * @since 0.1.12
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

		// --------------------------------------------------------
		// Admin option page

		// add admin menu page
		$this->wp_plugin()->options()->add_admin_options_subpage(
			$this->admin_page_slug(),
			'Kuetemeier > ' . __( 'Data Privacy', 'kuetemeier-essentials' ),
			__( 'Data Privacy', 'kuetemeier-essentials' )
		);

		// --------------------------------------------------------
		// Sections

		$options = $this->wp_plugin()->options();

		$options->add_option_section(
			new \Kuetemeier_Essentials\Option_Section(
				// ID
				'ke_dp_wp_comments',
				// title
				__( 'WordPress Comments', 'kuetemeier-essentials' ),
				// page
				$this->admin_page_slug(),
				// tab
				'',
				// content
				__( 'Users of the comment system should agree to our privacy policy', 'kuetemeier-essentials' )
			)
		);

		// --------------------------------------------------------
		// Settings

	}

}
