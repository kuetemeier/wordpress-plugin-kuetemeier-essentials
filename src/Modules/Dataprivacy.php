<?php

/**
 * Kuetemeier-Essentials - Data Privacy Module
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
final class Dataprivacy extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'data-privacy',
            'short' => __('Data Privacy', 'kuetemeier-essentials'),
            'description' => __('Kuetemeier Essentials Data Privacy Module.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier-data-privacy',

            'config' => array(
                'wp-comments-add-data-privacy-field' => 1,
                'wp-comments-data-privacy-statement-url' => __('\/data-privacy\/', 'kuetemeier-essentials'),
            )
        );
    }


    public function getAdminOptionSettings()
    {

        return array(
            'subpages' => array(
                array(
                    'id' => 'kuetemeier-data-privacy',
                    'parent' => 'kuetemeier',
                    'title' => __('Data Privacy', 'kuetemeier-essentials'),
                    'menuTitle' => __('Data Privacy', 'kuetemeier-essentials'),
                    'content' => __('Helps you with some GDPR requirements.', 'kuetemeier-essentials'),
                    'priority' => 200,
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'data-privacy-general',
                    'page' => 'kuetemeier-data-privacy',
                    'title' => __('General Data Privacy', 'kuetemeier-essentials'),
                ),
            ),
            'sections' => array(
                array(
                    'id' => 'data-privacy-introduction',
                    'tab' => 'data-privacy-general',
                    'title' => __('Introduction', 'kuetemeier-essentials'),
                    'content' => __('An introduction and more help to data privacy. (Comming soon).', 'kuetemeier-essentials'),
                ),
                array(
                    'id' => 'data-privacy-comments',
                    'tab' => 'data-privacy-general',
                    'title' => __('WordPress Comments', 'kuetemeier-essentials'),
                    'content' => __('It is recommended to add a privacy checkbox to the commentary function.', 'kuetemeier-essentials'),
                ),
            ),
            'options' => array(
                array(
                    'id' => 'wp-comments-add-data-privacy-field',
                    'section' => 'data-privacy-comments',
                    'title' => __('Privacy for WordPress Comments', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Add Privacy Checkbox', 'kuetemeier-essentials'),
                    'description' => __('(recommended) Adds a privacy checkbox to the WordPress comment system.', 'kuetemeier-essentials'),
                ),
                array(
                    'id' => 'wp-comments-data-privacy-statement-url',
                    'section' => 'data-privacy-comments',
                    'title' => __('URL to data privacy statement', 'kuetemeier-essentials'),
                    'type' => 'Text',
                    'label' => __('', 'kuetemeier-essentials'),
                    'description' => __('A valid URL, e.g. /privacy/ or https://yourdomain.com/privacy/.', 'kuetemeier-essentials') . '\n' .
                        __('Point this to you privacy statement URL (a link to it is shown in the notice).', 'kuetemeier-essentials'),
                )
            )
        );
    }


    public function frontendInit()
    {
        parent::frontendInit();
        $this->wpCommentsAddDataPrivacyField();
    }


    /**
     * Add a privacy field to comments
     */
    private function wpCommentsAddDataPrivacyField()
    {
        if ($this->getOption('wp-comments-add-data-privacy-field')) {
            //add your checkbox after the comment field
            add_filter('comment_form_field_comment', array(&$this, 'callbackCommentFormField'));

            //javascript validation
            add_action('wp_footer', array(&$this, 'callbackValdatePrivacyCommentJavaScript'));

            //no js fallback validation
            add_filter('preprocess_comment', array(&$this, 'callback__verify_comment_privacy'));

            //save field as comment meta
            add_action('comment_post', array(&$this, 'callback__save_comment_privacy'));
        }
        return false;
    }


    public function callbackCommentFormField($commentField)
    {
        $url = $this->getOption('wp-comments-data-privacy-statement-url');
        $statement = '';
        if (empty($url)) {
            $statement = __('Data Privacy Statement', 'kuetemeier-essentials');
        } else {
            $statement = '<a target="blank" href="' . $url . '">' . __('Data Privacy Statement', 'kuetemeier_essentials') . '</a>';
        }

        $label = sprintf(__('I have read the %s and accept the it.', 'kuetemeier-essentials'), $statement);
        $commentField .= '<p class="pprivacy"><input type="checkbox" name="privacy" value="privacy-key" class="privacyBox" aria-req="true"> ' . $label . '</p>';

        return $commentField;
    }


    private function getMsgWPCommentDataPrivacy()
    {
        return __('You must agree to our privacy term by checking the box...', 'kuetemeier-essentials');
    }


    public function callbackValdatePrivacyCommentJavaScript()
    {
        $msg = $this->getMsgWPCommentDataPrivacy();

        if (is_single() && comments_open()) {
            wp_enqueue_script('jquery');
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function($){
                $("#submit").click( function (e) {
                    if (!$('.privacyBox').prop('checked')){
                        e.preventDefault();
                        alert('<?php esc_html_e($msg) ?>');
                        return false;
                    }
                });
            });
            </script>
            <?php
        }
    }


    public function callbackVerifyCommentMetaData($commentData)
    {
        $msg = $this->getMsgWPCommentDataPrivacy();

        if (!isset($_POST['privacy'])) {
            wp_die(__('Error: ' . esc_html($msg), 'kuetemeier-essentials'));
        }

        return $commentData;
    }


    public function callbackSaveCommentMetaData($comment_id)
    {
        add_comment_meta($comment_id, 'privacy', $_POST['privacy']);
    }
}
