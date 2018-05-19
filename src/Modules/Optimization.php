<?php

/**
 * Kuetemeier-Essentials - Optimiziation Module
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
 * Optimization Module of the Kuetemeier-Essentials Plugin.
 */
final class Optimization extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'optimization',
            'short' => __('Optimization', 'kuetemeier-essentials'),
            'description' => __('Optimize your WordPress installation.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier-optimization',

            'config' => array(
                'disable-emoji' => 0,
                'disable-embeds' => 0,
            )
        );
    }


    public function getAdminOptionSettings()
    {
        return array(
            'subpages' => array(
                array(
                    'id' => 'kuetemeier-optimization',
                    'parentSlug' => 'kuetemeier',
                    'title' => __('Optimization', 'kuetemeier-essentials'),
                    'content' => __('Optimize your WordPress installation.', 'kuetemeier-essentials')
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'optimization-general',
                    'page' => 'kuetemeier-optimization',
                    'title' => __('Speed', 'kuetemeier-essentials')
                ),
            ),
            'sections' => array(
                array(
                    'id' => 'ogeneral',
                    'tab' => 'optimization-general',
                    'title' => __('Basic WordPress Features', 'kuetemeier-essentials'),
                    'content' => __('These are basic WordPress features, that most likely are not needed and we recommend to disable them.', 'kuetemeier-essentials')
                ),
            ),
            'options' => array(
                array(
                    'id' => 'disable-emoji',
                    'section' => 'ogeneral',
                    'title' => __('WP Emojis Support', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Disable WordPress Emojis', 'kuetemeier-essentials'),
                    'description' => __('(recommended) Check to disable the Emojis support in WordPress.', 'kuetemeier-essentials')
                ),
                array(
                    'id' => 'disable-embeds',
                    'section' => 'ogeneral',
                    'title' => __('WP Embeds Support', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Disable WordPress Embeds', 'kuetemeier-essentials'),
                    'description' => __('(recommended) Check to disable the "Embed" functionality in WordPress.', 'kuetemeier-essentials')
                )
            )
        );
    }


    public function commonInit()
    {
        parent::commonInit();
        $this->frontendInitDisableEmojis();
    }


    public function frontendInit()
    {
        parent::frontendInit();
        $this->frontendInitDisableEmbeds();
    }


    /* --------------------------------------------------------------------------------------------------------------
     * BEGIN - disable emojis
     * -------------------------------------------------------------------------------------------------------------- */

    private function frontendInitDisableEmojis()
    {
        if ($this->getOption('disable-emoji')) {
            add_action('init', array(&$this, 'callbackDisableEmojis'));
        }
    }


    /**
     * Disable the emoji's
     *
     * @see https://kinsta.com/knowledgebase/disable-emojis-wordpress
     */
    public function callbackDisableEmojis()
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('tiny_mce_plugins', array(&$this, 'callbackDisableEmojisTinyMCE'));
        add_filter('wp_resource_hints', array(&$this, 'callbackDisableEmojisRemoveDNSPrefetch'), 10, 2);
    }


    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param array $plugins
     *
     * @return array Difference betwen the two arrays
     */
    public function callbackDisableEmojisTinyMCE($plugins)
    {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        } else {
            return array();
        }
    }


    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array $urls URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     *
     * @return array Difference betwen the two arrays.
     */
    public function callbackDisableEmojisRemoveDNSPrefetch($urls, $relation_type)
    {
        if ('dns-prefetch' == $relation_type) {
            // This filter is documented in wp-includes/formatting.php
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

            $urls = array_diff($urls, array($emoji_svg_url));
        }

        return $urls;
    }


    /* --------------------------------------------------------------------------------------------------------------
     * END - disable emojis
     *
     * BEGIN - disable embeds
     * -------------------------------------------------------------------------------------------------------------- */


    public function frontendInitDisableEmbeds()
    {
        if ($this->getOption('disable-embeds')) {
            add_action('init', array(&$this, 'callbackDisableEmbeds'), PHP_INT_MAX);
        }
    }


    public function callbackDisableEmbeds()
    {
        // Remove the REST API endpoint.
        remove_action('rest_api_init', 'wp_oembed_register_route');

        // Turn off oEmbed auto discovery.
        add_filter('embed_oembed_discover', '__return_false');

        // Don't filter oEmbed results.
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

        // Remove oEmbed discovery links.
        remove_action('wp_head', 'wp_oembed_add_discovery_links');

        // Remove oEmbed-specific JavaScript from the front-end and back-end.
        remove_action('wp_head', 'wp_oembed_add_host_js');
        add_filter('tiny_mce_plugins', array(&$this, 'disable_embeds_tiny_mce_plugin'));

        // Remove all embeds rewrite rules.
        add_filter('rewrite_rules_array', array(&$this, 'disable_embeds_rewrites'));

        // Remove filter of the oEmbed result before any HTTP requests are made.
        remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
    }


    public function callbackDisableEmbedsTinyMCE($plugins)
    {
        return array_diff($plugins, array('wpembed'));
    }


    public function callbackDisableEmbedsRewrites($rules)
    {
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }

    /* --------------------------------------------------------------------------------------------------------------
     * END - disable embeds
     * -------------------------------------------------------------------------------------------------------------- */
}
