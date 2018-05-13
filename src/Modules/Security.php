<?php

/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package   kuetemeier-essentials
 * @author    Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license   GNU General Public License 3
 * @link      https://kuetemeier.de
 * @copyright 2018 Jörg Kütemeier
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

namespace KuetemeierEssentials\Modules;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined('ABSPATH') || die('No direct call!');

/**
 * Security Module of the Kuetemeier-Essentials Plugin.
 */
final class Security extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'security',
            'short' => __('Security', 'kuetemeier-essentials'),
            'description' => __('Increase the Security of your WordPress installation.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier-security',

            'config' => array(
                'remove-wp-version' => 0
            )
        );
    }


    public function getAdminOptionSettings()
    {
        return array(
            'subpages' => array(
                array(
                    'id' => 'kuetemeier-security',
                    'parentSlug' => 'kuetemeier',
                    'title' => __('Security', 'kuetemeier-essentials'),
                    'content' => __('Increase the Security of your WordPress installation.', 'kuetemeier-essentials'),
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'security-common',
                    'page' => 'kuetemeier-security',
                    'title' => __('Common', 'kuetemeier-essentials'),
                ),
            ),
            'sections' => array(
                array(
                    'id' => 'security-common',
                    'tab' => 'security-common',
                    'title' => __('IT-Security', 'kuetemeier-essentials'),
                    'content' => array(&$this, 'contentSecurityCommon')
                ),
                array(
                    'id' => 'security-remove-version',
                    'tab' => 'security-common',
                    'title' => __('Remove Version Informations', 'kuetemeier-essentials'),
                ),
            ),
            'options' => array(
                array(
                    'id' => 'remove-wp-version',
                    'section' => 'security-remove-version',
                    'title' => __('Remove WordPress Version', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Check to remove the WordPress Version from the HTML Code and RSS.', 'kuetemeier-essentials'),
                    'description' => __('(recommended)', 'kuetemeier-essentials'),
                ),
            )
        );
    }


    public function frontendInit()
    {
        parent::frontendInit();

        if ($this->getOption('remove-wp-version')) {
            add_filter('the_generator', array(&$this, 'callbackRemoveWPVersion'), PHP_INT_MAX);
        }
    }


    public function contentSecurityCommon()
    {
        echo '<p>' . __('IT security requires a complete concept that goes beyond the scope of this module.', 'kuetemeier-essentials') . '<br />';
        echo __('But a few useful features help you to better secure your WordPress installation (more features coming soon):', 'kuetemeier-essentials') . '</p>';
    }


    /* --------------------------------------------------------------------------------------------------------------
     * BEGIN - Remove WP Version
     * -------------------------------------------------------------------------------------------------------------- */

    public function callbackRemoveWPVersion()
    {
        return '';
    }

    /* --------------------------------------------------------------------------------------------------------------
     * END - Remove WP Version
     * -------------------------------------------------------------------------------------------------------------- */
}
