<?php

/**
 * Kuetemeier-Essentials - The Main PlugIn Class
 *
 * @package   kuetemeier-essentials
 * @author    Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license   GNU General Public License 3
 * @link      https://kuetemeier.de
 * @copyright 2018 Jörg Kütemeier
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

// KEEP THIS for security reasons - blocking direct access to the PHP files by checking for the ABSPATH constant.
defined('ABSPATH') || die('No direct call!');

require_once dirname(__FILE__) . '/Config.php';

/**
 * The main plugin class.
 *
 * @since 0.1.0
 */
final class KuetemeierEssentialsPlugin extends \Kuetemeier\WordPress\Plugin
{

    /**
     * Holding a vaild instance.
     *
     * @var Plugin
     *
     * @since 0.1.0
     */
    private static $instance = null;


    /**
     * Constructor of Kuetemeier_Essentials.
     *
     * It initializes all Options and modules.
     *
     * @since 0.1.0
     * @since 0.1.12 Reworked for WP_Plugin init process.
     */
    public function __construct()
    {
        $config = new \Kuetemeier\WordPress\Config(Config\PLUGIN_CONFIG);
        $config->set('_plugin/dir', KUETEMEIER_ESSENTIALS_PLUGIN_DIR, true);
        $config->set('_plugin/modules/namespace', 'KuetemeierEssentials\Modules', true);
        parent::__construct($config);

        add_action('wp_enqueue_scripts', array(&$this, 'callbackAddPublicScripts'));

        if (is_admin()) {
            add_action('admin_enqueue_scripts', array(&$this, 'callbackAddAdminScripts'));
            add_action('admin_init', array(&$this, 'callbackAdminInit'));
        }
    }


    public function callbackAdminInit()
    {
        $this->config->set('_plugin/options/saveButtonText', __('Save', 'kuetemeier-essentials'), 1);
        $this->config->set('_plugin/options/resetButtonText', __('Reset to Defaults', 'kuetemeier-essentials'), 1);
    }


    public function callbackAddPublicScripts()
    {
        wp_register_script(
            'kuetemeier_essentials_public_js',
            plugins_url(
                'assets/scripts/kuetemeier-essentials-public.min.js',
                str_replace('src', '', __FILE__)
            ),
            array('jquery'),
            Config\PLUGIN_VERSION,
            true
        );

        wp_enqueue_script('kuetemeier_essentials_public_js');

        wp_register_style(
            'kuetemeier_essentials_public_css',
            plugins_url(
                'assets/styles/kuetemeier-essentials.min.css',
                str_replace('src', '', __FILE__)
            ),
            array(),
            Config\PLUGIN_VERSION,
            'all'
        );

        wp_enqueue_style('kuetemeier_essentials_public_css');
    }


    public function callbackAddAdminScripts()
    {
        wp_register_script(
            'kuetemeier_essentials_admin_js',
            plugins_url(
                'assets/scripts/kuetemeier-essentials-admin.min.js',
                str_replace('src', '', __FILE__)
            ),
            array('jquery'),
            Config\PLUGIN_VERSION,
            true
        );

        wp_enqueue_script('kuetemeier_essentials_admin_js');

        wp_register_style(
            'kuetemeier_essentials_admin_css',
            plugins_url(
                'assets/styles/kuetemeier-essentials-admin.min.css',
                str_replace('src', '', __FILE__)
            ),
            array(),
            Config\PLUGIN_VERSION,
            'all'
        );

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
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            do_action('KuetemeierEssentials-loaded', self::$instance);
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
    public function debugToConsole($data)
    {
        if (is_array($data) || is_object($data)) {
            echo ('<script>console.log( "' .
                esc_html(KUETEMEIER_ESSENTIALS_NAME) . ': "' .
                wp_json_encode($data) . '" );</script>');
        } else {
            echo ('<script>console.log( "' .
                esc_html(KUETEMEIER_ESSENTIALS_NAME) .
                ': ' . esc_html($data) . '" );</script>');
        }
    }
}
