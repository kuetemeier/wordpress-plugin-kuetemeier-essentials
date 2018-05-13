<?php

/**
 * Kuetemeier-Essentials - Core Module
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

namespace KuetemeierEssentials\Modules;

// KEEP THIS for security reasons - blocking direct access to the PHP files by checking for the ABSPATH constant.
defined('ABSPATH') || die('No direct call!');

/**
 * Core Module of the Kuetemeier-Essentials Plugin.
 */
final class Core extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'core',
            'short' => __('Core', 'kuetemeier-essentials'),
            'description' => __('Kuetemeier Essentials Core Module.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier',

            'config' => array()
        );
    }


    public function getAdminOptionSettings()
    {

        return array(
            'pages' => array(
                array(
                    'id' => 'kuetemeier',
                    'title' => 'Kuetemeier',
                    'menu-title' => 'Kuetemeier',
                )
            ),
            'subpages' => array(
                array(
                    'id' => 'kuetemeier',
                    'parent' => 'kuetemeier',
                    'title' => 'Kuetemeier Essentials',
                    'menuTitle' => 'Essentials',
                    'priority' => 0,
                    'content' => __('Essential Features for WordPress!', 'kuetemeier-essentials')
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'core-general',
                    'page' => 'kuetemeier',
                    'title' => __('General', 'kuetemeier-essentials'),
                    'noButtons' => 1,
                ),
                array(
                    'id' => 'core-modules',
                    'page' => 'kuetemeier',
                    'title' => __('Modules', 'kuetemeier-essentials'),
                    'noButtons' => 1,
                ),
                array(
                    'id' => 'core-insights',
                    'page' => 'kuetemeier',
                    'title' => __('Insights', 'kuetemeier-essentials'),
                    'noButtons' => 1
                )
            ),
            'sections' => array(
                array(
                    'id' => 'core-version',
                    'tabs' => array('core-general'),
                    'title' => __('Version Information', 'kuetemeier-essentials'),
                    'content' => array(&$this, 'contentVersion')
                ),
                array(
                    'id' => 'core-what-is-new',
                    'tabs' => array('core-general'),
                    'title' => __('What is new?', 'kuetemeier-essentials'),
                    'content' => array(&$this, 'contentWhatIsNew')
                ),
                array(
                    'id' => 'core-installed-modules',
                    'tab' => 'core-modules',
                    'title' => __('Enabled Modules', 'kuetemeier-essentials'),
                    'content' => array(&$this, 'contentModules')
                ),
                array(
                    'id' => 'core-module-management',
                    'tab' => 'core-modules',
                    'title' => __('Module Management', 'kuetemeier-essentials'),
                    'content' => __('Coming soon.', 'kuetemeier-essentials')
                ),
                array(
                    'id' => 'core-insights',
                    'tab' => 'core-insights',
                    'title' => __('Usefull informations about your WordPress installation:', 'kuetemeier-essentials'),
                    'content' => array(&$this, 'contentInsights')
                ),
            ),
            'options' => array()
        );
    }


    public function contentVersion($section)
    {
        $plugin = $section->getPlugin();
        $stable = $plugin->isStableVersion() ? __('Stable Version', 'kuetemeier-essentials') : __('Development Version', 'kuetemeier-essentials');

        echo '<p><b>' . __('Kuetemeier Essentials Plugin Version', 'kuetemeier-essentials') . ':</b> ' .
            esc_html($plugin->getVersion()) . '</p>' .
            '<p><strong>' . __('Version Type', 'kuetemeier-essentials') . ':</strong> ' . esc_html($stable) . '</p>';

        if ($plugin->proVersionAvailable()) {
            echo '</p><p><b>' . __('License', 'kuetemeier-essentials') . ':</b> ' . __('Pro Version', 'kuetemeier-essentials') . '</p>';
        } else {
            echo '</p><p><b>' . __('License', 'kuetemeier-essentials') . ':</b> ' . __('GPL (Standard, free)', 'kuetemeier-essentials') . '</p>';
            echo '<p>' . __('Did you know? There is a Pro Version of this plugin available. You can get it here:', 'kuetemeier-essentials') .
                ' <a href="https://kuetemeier.de/kuetemeier-essentials">https://kuetemeier.de/kuetemeier-essentials</a>.</p>';
        }
    }


    public function contentWhatIsNew()
    {
        echo '<p>' . __('This is the initial public release. In the next weeks, I will launch some helpfull and documenting articles on my Blog', 'kuetemeier-essentials') .
            ' (<a href="https://kuetemeier.de/blog/">https://kuetemeier.de/blog</a>).</p>';
        echo '<p>' . __('With regard to the GDPR, there will be regular updates and additional functions in the next few days.', 'kuetemeier-essentials') . '</p>';
    }


    public function contentModules($section)
    {
        $modules = $section->getPluginModules();

        echo '<table class="ke-info-table"><tbody>';
        echo '<tr><td>' . __('Module', 'kuetemeier-essentials') . '</td><td>' . __('Description', 'kuetemeier-essentials') . '</td></tr>';
        foreach ($modules->keys() as $key) {
            $module = $modules->get($key);
            $manifest = $module->manifest();

            $link = (isset($manifest['page'])) ? '/wp-admin/admin.php?page=' . esc_attr($manifest['page']) : null;

            if (isset($link)) {
                echo '<tr><th>' .
                    esc_html($manifest['short']) .
                    '</th><td><a href="' .
                    esc_url($link) . '">' .
                    esc_html($manifest['description']) .
                    '</a></td></tr>';
            } else {
                echo '<tr><th>' .
                    esc_html($manifest['short']) .
                    '</th><td>' .
                    esc_html($manifest['description']) .
                    '</td></tr5';
            }
        }
        echo '</tbody><table>';
    }


    public function contentInsights()
    {
        global $wp_version;

        $isMultisite = (is_multisite()) ? __('yes', 'kuetemeier-essentials') : __('no', 'kuetemeier-essentials');

        $server_signature = (isset($_SERVER['SERVER_SIGNATURE'])) ? $_SERVER['SERVER_SIGNATURE'] : '';

        $maxFileUpload = $this->getMaximumFileUploadSize();

        $infos = array(
            array('WordPress Version', $wp_version),
            array('Multisite Installation', $isMultisite),
            array('PHP Version', PHP_VERSION),
            array('PHP_DEBUG', PHP_DEBUG),
            array('WP_DEBUG', WP_DEBUG),
            array('Operating System', php_uname()),
            array('PHP Memory Limit', ini_get('memory_limit')),
            array('Server Name', (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '')),
            array('Server Signature', $server_signature),
            array('Server IP', (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '')),
            array('Server Software', (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '')),
            array('Server Protocoll', (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '')),
            array('Document Root', (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '')),
            array('Post Max Size', ini_get('post_max_size')),
            array('Max Upload Filesize', ini_get('upload_max_filesize')),
            array('Effective Maximum File Upload Size', $maxFileUpload . ' Bytes'),
        );

        echo '<table class="ke-info-table"><tbody>';
        foreach ($infos as $info) {
            $value = (isset($info[1])) ? $info[1] : '';
            echo '<tr><th>' . esc_html($info[0]) . ':</th><td>' . esc_html($value) . '</td></tr>';
        }
        echo '</tbody><table>';
    }


    /**
     * This function returns the maximum files size that can be uploaded in PHP
     *
     * @returns int File size in bytes
     *
     * @see https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
     **/
    private function getMaximumFileUploadSize()
    {
        return min($this->convertPHPSizeToBytes(ini_get('post_max_size')), $this->convertPHPSizeToBytes(ini_get('upload_max_filesize')));
    }

    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     *
     * @param string $sSize
     *
     * @return integer The value in bytes
     *
     * @see https://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
     */
    private function convertPHPSizeToBytes($sSize)
    {
        $sSuffix = strtoupper(substr($sSize, -1));
        if (!in_array($sSuffix, array('P', 'T', 'G', 'M', 'K'))) {
            return (int)$sSize;
        }
        $iValue = substr($sSize, 0, -1);
        switch ($sSuffix) {
            case 'P':
                $iValue *= 1024;
                // Fallthrough intended
            case 'T':
                $iValue *= 1024;
                // Fallthrough intended
            case 'G':
                $iValue *= 1024;
                // Fallthrough intended
            case 'M':
                $iValue *= 1024;
                // Fallthrough intended
            case 'K':
                $iValue *= 1024;
                break;
        }
        return (int)$iValue;
    }
}
