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
final class Analytics_Frontend extends \Kuetemeier_Essentials\Plugin_Modules\Frontend_Module {

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
			'analytics',
			// name
			__( 'Analytics', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$this->init_options();

	}


	/**
	 * Init all Media options.
	 *
	 * @return void
	 *
	 * @since  0.2.1
	 */
	private function init_options() {
/*
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
*/	}

}
