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
final class Media_Frontend extends \Kuetemeier_Essentials\Plugin_Modules\Frontend_Module {

	/**
	 * Option: Enable reference Media from URLs
	 *
	 * @var \Kuetemeier_Essentials\Option_Setting_Checkbox
	 */
	private $option_external_media_enabled;

	/**
	 * Option: Enable IMGIX JS support
	 *
	 * @var \Kuetemeier_Essentials\Option_Setting_Checkbox
	 */
	private $option_imgix_js_enabled;

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
			'media',
			// name
			__( 'Media', 'kuetemeier-essentials' ),
			// WP_Plugin instance
			$wp_plugin
		);

		$this->init_options();

		if ($this->option_imgix_js_enabled->get() ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'add_scripts' ) );
			add_action( 'wp_head', array( &$this, 'add_imgix_dns_prefetch_to_header'), 0 );
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

		$this->option_external_media_enabled = new \Kuetemeier_Essentials\Options\Setting_Checkbox(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// option id
			'external_media_enabled',
			// default value
			false,
			// label
			__( 'External Media in Library', 'kuetemeier-essentials' ),
			// page
			$this->get_admin_page_slug(),
			// tab
			'media-options',
			// section
			'ke-media-media-options',
			// description
			__( 'Check to be able to reference external Media in the Media Library.', 'kuetemeier-essentials' )
		);

		$options->add_option_setting( $this->option_external_media_enabled );

		$this->option_imgix_js_enabled = new \Kuetemeier_Essentials\Options\Setting_Checkbox(
			// WP_Plugin instance
			$this->get_wp_plugin(),
			// module
			$this->get_id(),
			// option id
			'imgix_js_enabled',
			// default value
			false,
			// label
			__( 'Enable IMGIX JavaScript', 'kuetemeier-essentials' ),
			// page
			$this->get_admin_page_slug(),
			// tab
			'media-options',
			// section
			'ke-media-media-options',
			// description
			__( 'Check to enable the IMGIX JavaScript functionality.', 'kuetemeier-essentials' )
		);

		$options->add_option_setting( $this->option_imgix_js_enabled );
	}

	public function add_scripts() {
		//wp_register_script('kuetemeier_essentials_media_public', plugins_url('assets/scripts/imgix.min.js', dirname(__FILE__).'/../..' ) );
		wp_register_script('kuetemeier_essentials_media_public', plugins_url('assets/scripts/imgix.min.js', str_replace('src/plugin_modules/frontend', '', __FILE__ ) ) );

		wp_enqueue_script('kuetemeier_essentials_media_public');
	}

	// TODO: build an option field for this!
	public function add_imgix_dns_prefetch_to_header() {
		?>
		<link rel="dns-prefetch" href="//kuetemeier.imgix.com">
		<?php
	}

}
