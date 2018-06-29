<?php

/**
 * Kuetemeier-Essentials - Toolkit Module
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
 * Toolkit Module of the Kuetemeier-Essentials Plugin.
 */
final class Toolkit extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'toolkit',
            'short' => __('Toolkit', 'kuetemeier-essentials'),
            'description' => __('Little helper and tools for enriching your WordPress installation.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier-toolkit',

            'config' => array(
                'custom-excerpt-read-more' => 0,
                'custom-excerpt-read-more-content' => '%1$s <p class="read-more-button-container"><a class="button" href="%2$s">Read more</a></p>'
            )
        );
    }


    public function getAdminOptionSettings()
    {

        return array(
            'subpages' => array(
                array(
                    'id' => 'kuetemeier-toolkit',
                    'parentSlug' => 'kuetemeier',
                    'title' => __('Toolkit', 'kuetemeier-essentials'),
                    'content' => __('Little helper and tools for enriching your WordPress installation.', 'kuetemeier-essentials'),
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'toolkit-common',
                    'page' => 'kuetemeier-toolkit',
                    'title' => __('Common functions', 'kuetemeier-essentials'),
                    'content' => __('A collection of small helper functions.', 'kuetemeier-essentials'),
                ),
                array(
                    'id' => 'toolkit-shortcodes',
                    'page' => 'kuetemeier-toolkit',
                    'title' => __('Shortcodes', 'kuetemeier-essentials'),
                    'content' => __('The Toolkit section comes with some handy Shortcodes:', 'kuetemeier-essentials'),
                    'noButtons' => 1
                ),
            ),
            'sections' => array(
                array(
                    'id' => 'toolkit-common-more',
                    'tab' => 'toolkit-common',
                    'title' => __('Read more', 'kuetemeier-essentials'),
                    'content' => __('By default, WordPress skips all of the excerpt_length and excerpt_more filters when the custom excerpt is used. If you would like to use the read more links or read more buttons with custom excerpt, you can activate it here:', 'kuetemeier-essentials'),
                ),
                array(
                    'id' => 'toolkit-shortcodes-dates',
                    'tab' => 'toolkit-shortcodes',
                    'title' => __('Dates', 'kuetemeier-essentials'),
                    'content' => __('Use the Shortcode `[ke-current-year]` to display the current year. Very helpful in Copyright notices.', 'kuetemeier-essentials') . "\n\n" .
                        __('', 'kuetemeier-essentials') . "\n\n" .
                        __('', 'kuetemeier-essentials'),
                    'markdown' => 1,
                ),
            ),
            'options' => array(
                array(
                    'id' => 'custom-excerpt-read-more',
                    'section' => 'toolkit-common-more',
                    'type' => 'CheckBox',
                    'code' => 1,
                    'title' => __('Activate "read more" with custom excerpt', 'kuetemeier-essentials'),
                    'label' => __('(recommended)', 'kuetemeier-essentials'),
                    'description' => __('Check to place a "read more" notice after custom exceprts.', 'kuetemeier-essentials'),
                ),
                array(
                    'id' => 'custom-excerpt-read-more-content',
                    'section' => 'toolkit-common-more',
                    'type' => 'Text',
                    'code' => 1,
                    'title' => __('Content of the "read more" link.', 'kuetemeier-essentials'),
                    //'label' => __('(e.g. UA-12345678-90)', 'kuetemeier-essentials'),
                    'doNotFilter' => 1,
                    'description' => __('You can use %1$s for the excerpt and %2$s for the link to the article.', 'kuetemeier-essentials'),
                ),
            )
        );
    }


    public function frontendInit()
    {
        parent::frontendInit();

        add_shortcode('ke-current-year', array(&$this, 'callbackShortCodeCurrentYear'));

        if ($this->getOption('custom-excerpt-read-more')) {
            add_filter('wp_trim_excerpt', array(&$this, 'callbackCustomExcerptAddReadMore'));
        }
    }



    /* ------------------------------------------------------------------------------------------------------------------------
     * BEGIN - ShortCut 'kimg'
     * ------------------------------------------------------------------------------------------------------------------------ */


    public function callbackShortCodeCurrentYear()
    {
        return esc_html(date("Y"));
    }


    /* ------------------------------------------------------------------------------------------------------------------------
     * END - ShortCut 'kimg'
     * ------------------------------------------------------------------------------------------------------------------------ */


    public function callbackCustomExcerptAddReadMore($excerpt)
    {
        $output = $excerpt;

        $content = '%1$s <a href="%2$s">Read more</a>';
        $content = $this->getOption('custom-excerpt-read-more-content');
        //wp_die($content);
        if (has_excerpt()) {
            $output = sprintf(
                $content,
                $excerpt,
                get_permalink()
            );
        }
        return $output;
    }
}
