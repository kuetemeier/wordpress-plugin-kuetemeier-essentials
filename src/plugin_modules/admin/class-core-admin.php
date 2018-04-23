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

/**
 * Class Kuetemeier_Essentials
 */
class Core_Admin extends \Kuetemeier_Essentials\Plugin_Modules\Admin_Module {

	/**
	 * Create Core module.
	 *
	 * @param WP_Plugin $wp_plugin A vallid instance of WP_Plugin (should be the main WordPress Plugin object).
	 *
	 * @since 0.1.12
	 */
	public function __construct( $wp_plugin ) {
		parent::__construct(
			// id
			'core',
			// name
			__( 'Core', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$options = $this->get_wp_plugin()->get_options();

		$options->add_admin_subpage(
			$this->get_wp_plugin()->get_admin_page_slug(),
			'Kuetemeier > Essentials',
			'Essentials',
			array(
				'general' => __( 'General', 'kuetemeier-essentials' ),
				'modules' => __( 'Modules', 'kuetemeier-essentials' ),
			),
			0
		);

		$options->add_option_section(
			new \Kuetemeier_Essentials\Options\Section(
				// id
				'ke-section-general-version',
				// title
				'Version Information',
				// page
				$this->get_wp_plugin()->get_admin_page_slug(),
				// (optional) tab
				'general',
				// (optional) content
				'<p><b>Kuetemeier Essentials Plugin Version:</b> ' . \Kuetemeier_Essentials\Config\PLUGIN_VERSION .
				'</p><p><b>License:</b> Alpha Test Version - limitied license</p>',
				// (optional) display_function
				'NOESC!'
			)
		);


	}

}
