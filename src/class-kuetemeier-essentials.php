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

namespace Kuetemeier_Essentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once dirname( __FILE__ ) . '/class-modules.php';
require_once dirname( __FILE__ ) . '/class-options.php';
require_once dirname( __FILE__ ) . '/config.php';

/**
 * The main plugin class.
 *
 * @since 0.1.0
 */
final class Kuetemeier_Essentials extends WP_Plugin {


	/**
	 * Constructor of Kuetemeier_Essentials.
	 *
	 * It initializes all Options and modules.
	 *
	 * @since 0.1.0
	 * @since 0.1.12 Reworked for WP_Plugin init process.
	 */
	public function __construct() {

		$options = new Options( $this );
		$modules = new Modules( $this, Config\AVAILABLE_MODULES );

		parent::__construct(
			Config\PLUGIN_VERSION,
			Config\PLUGIN_VERSION_STABLE,
			$options,
			$modules
		);

		add_action( 'wp_enqueue_scripts', array( &$this, 'callback__add_public_scripts' ) );

		if (is_admin()) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'callback__add_admin_scripts' ) );
		}
	}

	public function callback__add_public_scripts() {
		wp_register_script('kuetemeier_essentials_public_js', plugins_url(
			'assets/scripts/kuetemeier-essentials-public.min.js',
			str_replace('src', '', __FILE__ ) ),
			array('jquery'), Config\PLUGIN_VERSION, true);

		wp_enqueue_script('kuetemeier_essentials_public_js');

		wp_register_style('kuetemeier_essentials_public_css', plugins_url(
			'assets/styles/kuetemeier-essentials.min.css',
			str_replace('src', '', __FILE__ ) ),
			array(), Config\PLUGIN_VERSION, 'all');

		wp_enqueue_style('kuetemeier_essentials_public_css');
	}

	public function callback__add_admin_scripts() {
		wp_register_script('kuetemeier_essentials_admin_js', plugins_url(
			'assets/scripts/kuetemeier-essentials-admin.min.js',
			str_replace('src', '', __FILE__ ) ),
			array('jquery'), Config\PLUGIN_VERSION, true);

		wp_enqueue_script('kuetemeier_essentials_admin_js');

		wp_register_style('kuetemeier_essentials_admin_css', plugins_url(
			'assets/styles/kuetemeier-essentials-admin.min.css',
			str_replace('src', '', __FILE__ ) ),
			array(), Config\PLUGIN_VERSION, 'all');

		wp_enqueue_style('kuetemeier_essentials_admin_css');
	}



	/**
	 * Returns a valid instance of Kuetemeier_Essentials.
	 *
	 * @param sting $self Needed for internal purpose. DO NOT USE IT, LEVE IT BLANK.
	 *
	 * @return Kuetemeier_Essentials
	 *
	 * @see WP_Plugin::instance()
	 * @since 0.1.11
	 */
	public static function instance( $self = '' ) {
		if ( ! empty( $self ) ) {
			die( 'Do not use the parameter \$self!' );
		}
		return parent::instance( __CLASS__ );
	}

	/**
	 * Send a debug message to the browser console.
	 *
	 * @param Object $data Data to be outputted to console.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function debug_to_console( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			echo( '<script>console.log( "' .
				esc_html( KUETEMEIER_ESSENTIALS_NAME ) . ': "' .
				wp_json_encode( $data ) . '" );</script>' );
		} else {
			echo( '<script>console.log( "' .
				esc_html( KUETEMEIER_ESSENTIALS_NAME ) .
				': ' . esc_html( $data ) . '" );</script>' );
		}
	}

}
