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

namespace KuetemeierEssentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once dirname( __FILE__ ) . '/config.php';

/**
 * The main plugin class.
 *
 * @since 0.1.0
 */
final class KuetemeierEssentialsPlugin extends \Kuetemeier\WordPress\Plugin {

	/**
	 * Holding a vaild instance.
	 *
	 * @var Plugin
	 *
	 * @since 0.1.0
	 */
	private static $instance = null;


	/**
	 * Instance of the Modules Class.
	 *
	 * @var Modules
	 *
	 * @since 0.1.11
	 */
	private $modules;


	/**
	 * Instance of the Options Class.
	 *
	 * @var Options
	 *
	 * @since 0.1.11
	 */
	private $options;


	/**
	 * Constructor of Kuetemeier_Essentials.
	 *
	 * It initializes all Options and modules.
	 *
	 * @since 0.1.0
	 * @since 0.1.12 Reworked for WP_Plugin init process.
	 */
	public function __construct() {

		$config = new \Kuetemeier\WordPress\Config(Config\PLUGIN_CONFIG);
		$config->set('plugin/dir', KUETEMEIER_ESSENTIALS_PLUGIN_DIR, true);
		$config->set('plugin/modules/namespace', 'KuetemeierEssentials\Module', true);
		parent::__construct($config);

/*		$this->options = new Options( $this );
		$this->modules = new Modules( $this, Config\AVAILABLE_MODULES );

		$this->modules->init_frontend_prepare_backend();

		$this->options->init_admin_hooks();*/

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
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @param string $self Class name to be instanciated.
	 *
	 * @return WP_Plugin A valid WP_Plugin instance
	 *
	 * @since 0.1.11
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			do_action( 'KuetemeierEssentials-loaded', self::$instance );
		}
		return self::$instance;
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


	/**
	 * Returns the slug of admin menu page for the given module (or the default admin slug).
	 *
	 * @param string $plugin_module_id (optional) A valid id of a Plugin_Module.
	 *
	 * @return string Default admin menu page slug.
	 *
	 * @since 0.2.1
	 */
	public function get_admin_page_slug( $plugin_module_id = '' ) {

		if ( ! isset( $plugin_module_id ) ) {
			wp_die( 'FATAL ERROR: Something is wrong, \$plugin_module_id is not set' );
		}

		trim( $plugin_module_id );

		if ( empty( $plugin_module_id ) ) {
			// const from config.php
			return Config\ADMIN_PAGE_SLUG;
		} else {
			return sanitize_text_field( Config\ADMIN_PAGE_SLUG . '-' . $plugin_module_id );
		}
	}

}
