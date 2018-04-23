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
 * Module for testing and development. TODO: Only activated in Alpha mode.
 */
class Develop_Frontend extends \Kuetemeier_Essentials\Plugin_Modules\Frontend_Module {

	/**
	 * Create Development Module.
	 *
	 * @param WP_Plugin $wp_plugin A vallid instance of WP_Plugin (should be the main WordPress Plugin object).
	 *
	 * @since 0.1.12
	 */
	public function __construct( $wp_plugin ) {
		parent::__construct(
			// id
			'develop',
			// name
			__( 'Development', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$options = $this->get_wp_plugin()->get_options();

/*
		$options->add_option_section(
			new \Kuetemeier_Essentials\Options\Section(
				// id
				'test',
				// title
				'Test',
				// page
				$this->get_admin_page_slug(),
				// (optional) tab
				'test',
				// (optional) content
				'Dies ist ein Test'
				// (optional) display_function
			)
		);

		// --------------------------------
		// add OPTION SETTINGS
		// --------------------------------

		$options->add_option_setting(
			new \Kuetemeier_Essentials\Options\Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				// module
				'default',
				// id
				'test_option_1',
				// default value
				false,
				// label
				'Test mit dieser Option 1',
				// page
				$this->get_admin_page_slug(),
				// tab
				'test',
				// section
				'test',
				// description
				'A Dies sollte er validieren und speichern'
			)
		);

		$options->add_option_setting(
			new \Kuetemeier_Essentials\Options\Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'core',
				'test_option_2',
				true,
				'Test mit dieser Option 2',
				$this->get_admin_page_slug(),
				'test',
				'test',
				'B Dies sollte er validieren und speichern'
			)
		);

		$options->add_option_setting(
			new \Kuetemeier_Essentials\Options\Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'default',
				'test_option_3',
				true,
				'Test mit dieser Option 3',
				$this->get_admin_page_slug(),
				'test',
				'test',
				'C Dies sollte er validieren und speichern'
			)
		);

		$options->add_option_setting(
			new \Kuetemeier_Essentials\Options\Setting_Text(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'default',
				'test_text',
				'Ein Text',
				'Ein Textfeld',
				$this->get_admin_page_slug(),
				'test',
				'test',
				'Dies ist ein Textfeld'
			)
		);
*/
	}

}
