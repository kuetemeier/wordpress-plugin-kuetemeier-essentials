<?php

/**
 * Kuetemeier-Essentials - Media Module
 *
 * @package   kuetemeier-essentials
 * @author    Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license   GNU General Public License 3
 * @link      https://kuetemeier.de
 * @copyright 018 Jörg Kütemeier
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
 * Media Module of the Kuetemeier-Essentials Plugin.
 */
final class Media extends \Kuetemeier\WordPress\PluginModule
{

    public static function manifest()
    {
        return array(
            'id' => 'media',
            'short' => __('Media', 'kuetemeier-essentials'),
            'description' => __('WordPress media enhancements.', 'kuetemeier-essentials'),
            'page' => 'kuetemeier-media',

            'config' => array(
                'external-media-enabled' => 0,
                'imgix-js-enabled' => 0,
            )
        );
    }


    public function getAdminOptionSettings()
    {

        return array(
            'subpages' => array(
                array(
                    'id' => 'kuetemeier-media',
                    'parentSlug' => 'kuetemeier',
                    'title' => __('Media', 'kuetemeier-essentials'),
                    'content' => __('WordPress Media Library enhancements.', 'kuetemeier-essentials'),
                )
            ),
            'tabs' => array(
                array(
                    'id' => 'media-common',
                    'page' => 'kuetemeier-media',
                    'title' => __('Common Media Library Options', 'kuetemeier-essentials'),
                    'content' => __('On this tab you find some enhancements for the WordPress Media Library.', 'kuetemeier-essentials')
                ),
                array(
                    'id' => 'media-imgix',
                    'page' => 'kuetemeier-media',
                    'title' => __('imgix Support', 'kuetemeier-essentials')
                ),
                array(
                    'id' => 'media-kimg',
                    'page' => 'kuetemeier-media',
                    'title' => __('kimg Shortcode', 'kuetemeier-essentials'),
                    'noButtons' => 1
                ),
            ),
            'sections' => array(
                array(
                    'id' => 'media-common-external',
                    'tab' => 'media-common',
                    'title' => __('Add external Media to the Library', 'kuetemeier-essentials'),
                    'content' => __('If activated, this feature will allow you to add external Media (by it\'s URL) to the Media Library.', 'kuetemeier-essentials') . "\n\n" .
                        __('The media is NOT imported, but you can use it like "normal" media and enhance it with custom fields (like captions or copyright informations).', 'kuetemeier-essentials') . "\n\n" .
                        __('This is usefull for [S3](https://aws.amazon.com/s3), CDN or [imgix](https://imgix.com) integration.', 'kuetemeier-essentials'),
                    'markdown' => 1,
                ),
                array(
                    'id' => 'media-imgix',
                    'tab' => 'media-imgix',
                    'title' => __('imgix Support', 'kuetemeier-essentials'),
                    'content' => __('[imgix](https://imgix.com) is a "Powerful image processing,  simple API - Optimize, deliver, and cache your entire image library for fast, stress-free websites and apps TRY IT FREE".', 'kuetemeier-essentials'),
                    'markdown' => 1
                ),
                /*array(
                    'id'         => 'media-imgix-source',
                    'tab'        => 'media-imgix',
                    'title'      => __( 'imgix Source Settings', 'kuetemeier-essentials' ),
                    'content'    => __( 'Default imgix source settings.', 'kuetemeier-essentials' )
                ),*/
                array(
                    'id' => 'media-kimg',
                    'tab' => 'media-kimg',
                    'title' => __('"kimg" - Kuetemeier Image Shortcode', 'kuetemeier-essentials'),
                    'content' => __('Usefull Shortcut to create image tags with imgix support and copyright informations.', 'kuetemeier-essentials') . "\n\n" .
                        __('Beta Test - More information will be available soon', 'kuetemeier -essentials') . "\n",
                    'markdown' => 1
                ),

            ),
            'options' => array(
                array(
                    'id' => 'external-media-enabled',
                    'section' => 'media-common-external',
                    'title' => __('Enable external Media', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Check to be able to reference external Media and add it to the Media Library.', 'kuetemeier-essentials')
                ),
                array(
                    'id' => 'imgix-js-enabled',
                    'section' => 'media-imgix',
                    'title' => __('Enable IMGIX JavaScript', 'kuetemeier-essentials'),
                    'type' => 'CheckBox',
                    'label' => __('Check to enable the IMGIX JavaScript', 'kuetemeier-essentials'),
                )
            )
        );
    }


    public function frontendInit()
    {
        parent::frontendInit();

        if ($this->getOption('imgix-js-enabled')) {
            add_action('wp_enqueue_scripts', array(&$this, 'addScripts'));
            add_action('wp_head', array(&$this, 'addImgixDNSPrefetchToHeader'), 0);
            add_shortcode('kimg', array(&$this, 'callbackAddShortCodeKIMG'));
        }
    }


    public function adminInit($options)
    {
        parent::adminInit($options);

        $this->enableExternalMedia();
    }


    public function addScripts()
    {
        wp_register_script(
            'kuetemeier_essentials_media_public',
            plugins_url('assets/scripts/imgix.min.js', str_replace('src/Modules', '', __FILE__))
        );

        wp_enqueue_script('kuetemeier_essentials_media_public');
    }


    public function addImgixDNSPrefetchToHeader()
    {
        echo '<link rel="dns-prefetch" href="//kuetemeier.imgix.com">';
    }


    /* --------------------------------------------------------------------------------------------------------------
     * BEGIN - ShortCut 'kimg'
     * -------------------------------------------------------------------------------------------------------------- */


    public function callbackAddShortCodeKIMG($atts)
    {
        $a = shortcode_atts(array(
            'id' => '',
            'src' => '',
            'c' => '1',
            'copyright' => '1'
        ), $atts);

        if ($a['c'] === 'false') {
            $a['c'] = 0;
        }
        if ($a['copyright'] === 'false') {
            $a['copyright'] = 0;
        }

        $copyright = ((!$a['c']) || (!$a['copyright'])) ? false : true;

        $ret = '';

        if ($copyright) {
            $ret .= '[caption';
            if (!empty($a['id'])) {
                $ret .= ' id="' . $a['id'] . '"';
            }
            $ret .= ']';
        }
        $ret .= '<img src="' . $a['src'] . '" alt="%caption%" title="%copyright%" />';
        if ($copyright) {
            $ret .= '[/caption]';
        }

        return do_shortcode($ret);
    }


    /* --------------------------------------------------------------------------------------------------------------
     * END - ShortCut 'kimg'
     * -------------------------------------------------------------------------------------------------------------- */


    /* --------------------------------------------------------------------------------------------------------------
     * BEGIN - add-external-media
     * -------------------------------------------------------------------------------------------------------------- */


    const PAGE_SLUG_ADD_MEDIA = 'kuetemeier-add-media-by-reference';


    public function enableExternalMedia()
    {
        if ($this->getOption('external-media-enabled')) {
            add_action(
                'admin_menu',
                array(&$this, 'callbackAdminMenuAddExternalMedia')
            );

            add_action(
                'post-plupload-upload-ui',
                array(&$this, 'postUploadUI')
            );

            add_action(
                'post-html-upload-ui',
                array(&$this, 'postUploadUI')
            );

            add_action(
                'wp_ajax_kuetemeier_add_external_media_without_import',
                array(&$this, 'wpAjaxAddExternalMediaWithoutImport')
            );

            add_action(
                'admin_post_kuetemeier_add_external_media_without_import',
                array(&$this, 'adminPostAddExternalMediaWithoutImport')
            );
        }
    }


    public function callbackAdminMenuAddExternalMedia()
    {
        if ($this->getOption('external-media-enabled')) {
            add_submenu_page(
                'upload.php', // parent_slug
                __('Reference by URL', 'kuetemeier-essentials'), // page_title
                __('Reference by URL', 'kuetemeier-essentials'), // menu_title
                'manage_options', // capability
                self::PAGE_SLUG_ADD_MEDIA, // menu_slug
                array(&$this, 'callbackSubmenuPageMediaByReference') // callable
            );
        }
    }


    public function printMediaNewPanel($is_in_upload_ui)
    {
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $width = isset($_GET['width']) ? $_GET['width'] : '';
        $height = isset($_GET['height']) ? $_GET['height'] : '';
        $mimeType = isset($_GET['mime-type']) ? $_GET['mime-type'] : '';
        $error = isset($_GET['error']) ? $_GET['error'] : '';

        ?>
        <div id="kuetemeier-media-new-panel" <?php
        if ($is_in_upload_ui) {
            echo 'style="display: none"';
        }
        ?>>
            <div class="url-row">
                <label><?php _e('URL to reference Media to', 'kuetemeier-essentials'); ?></label>
                <span id="kuetemeier-url-input-wrapper">
                    <input id="kuetemeier-url" name="url" type="url" required placeholder="<?php
                    _e('Image URL (https://my.domain/my-image.jpg)', 'kuetemeier-essentials'); ?>" value="<?php
                    echo esc_url($url);
                    ?>">
                </span>
            </div>
            <div id="kuetemeier-hidden" <?php
            if ($is_in_upload_ui || empty($error)) {
                echo 'style="display: none"';
            } ?>>
                <div>
                    <span id="kuetemeier-error"><?php echo esc_html($error); ?></span>
                    <?php
                    _e('Please fill in the following properties manually. If you leave the fields blank (or 0 for width/height), the plugin will try to resolve them automatically', 'kuetemeier-essentials');
                    ?>
                </div>
                <div id="kuetemeier-properties">
                    <label><?php _e('Width', 'kuetemeier-essentials'); ?></label>
                    <input id="kuetemeier-width" name="width" type="number" value="<?php echo esc_html($width); ?>">
                    <label><?php _e('Height', 'kuetemeier-essentials'); ?></label>
                    <input id="kuetemeier-height" name="height" type="number" value="<?php echo esc_html($height); ?>">
                    <label><?php _e('MIME Type', 'kuetemeier-essentials'); ?></label>
                    <input id="kuetemeier-mime-type" name="mime-type" type="text" value="<?php echo esc_html($mimeType); ?>">
                </div>
            </div>

            <div id="kuetemeier-buttons-row">
                <input type="hidden" name="action" value="kuetemeier_add_external_media_without_import">
                <?php /* <span class="spinner"></span> */ ?>

                <input type="submit" id="kuetemeier-add" class="button button-primary" value="<?php
                _e('Add', 'kuetemeier-essentials');
                ?>">
                <?php
                if ($is_in_upload_ui) {
                    echo '<input type="button" id="kuetemeier-cancel" class="button" value="'.
                        _e('Cancel', 'kuetemeier-essentials') . '">';
                } ?>
            </div>
        </div>
        <?php
    }


    public function callbackSubmenuPageMediaByReference()
    {
        ?>
        <div class="wrap">
            <h2><?php _e('Add Media Reference (by URL)', 'kuetemeier-essentials') ?></h2>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <?php $this->printMediaNewPanel(false); ?>
            </form>
        </div>
        <?php
    }


    public function postUploadUI()
    {
        $media_library_mode = get_user_option('media_library_mode', get_current_user_id());
        ?>
            <div id="kuetemeier-in-upload-ui">
            <div class="row1">
                <?php echo __('or'); ?>
            </div>
            <div class="row2">
                <?php if ('grid' === $media_library_mode) : ?>
                <button id="kuetemeier-show" class="button button-large">
                    <?php _e('Reference Media by URL', 'kuetemeier-essentials'); ?>
                </button>
                    <?php printMediaNewPanel(true); ?>
                <?php else : ?>
                <a class="button button-large" href="<?php
                echo esc_url(admin_url('/upload.php?page=' . self::PAGE_SLUG_ADD_MEDIA, __FILE__));
                ?>">
                    <?php _e('Add External Media without Import', 'kuetemeier-essentials'); ?>
                </a>
                <?php endif; ?>
            </div>
            </div>
        <?php
    }


    public function wpAjaxAddExternalMediaWithoutImport()
    {
        $info = addExternalMediaWithoutImport();
        if (isset($info['id'])) {
            if ($attachment = wp_prepare_attachment_for_js($info['id'])) {
                wp_send_json_success($attachment);
            } else {
                $info['error'] = _('Failed to prepare attachment for js');
                wp_send_json_error($info);
            }
        } else {
            wp_send_json_error($info);
        }
    }


    public function adminPostAddExternalMediaWithoutImport()
    {
        $info = $this->addExternalMediaWithoutImport();

        $redirect_url = 'upload.php';
        if (!isset($info['id'])) {
            $redirect_url = $redirect_url . '?page=' . urlencode(self::PAGE_SLUG_ADD_MEDIA) . '&url=' . urlencode($info['url']);
            $redirect_url = $redirect_url . '&error=' . urlencode($info['error']);
            $redirect_url = $redirect_url . '&width=' . urlencode($info['width']);
            $redirect_url = $redirect_url . '&height=' . urlencode($info['height']);
            $redirect_url = $redirect_url . '&mime-type=' . urlencode($info['mime-type']);
        }
        wp_redirect(admin_url($redirect_url));
        exit;
    }


    public function sanitizeAndValidateInput()
    {
        // Don't call sanitize_text_field on url because it removes '%20'.
        // Always use esc_url/esc_url_raw when sanitizing URLs. See:
        // https://codex.wordpress.org/Function_Reference/esc_url
        $input = array(
            'url' => esc_url_raw($_POST['url']),
            'width' => sanitize_text_field($_POST['width']),
            'height' => sanitize_text_field($_POST['height']),
            'mime-type' => sanitize_mime_type($_POST['mime-type'])
        );

        $width_str = $input['width'];
        $width_int = intval($width_str);
        if (!empty($width_str) && $width_int <= 0) {
            $input['error'] = __('Width and height must be non-negative integers.', 'kuetemeier-essentials');
            return $input;
        }

        $height_str = $input['height'];
        $height_int = intval($height_str);
        if (!empty($height_str) && $height_int <= 0) {
            $input['error'] = __('Width and height must be non-negative integers.', 'kuetemeier-essentials');
            return $input;
        }

        $input['width'] = $width_int;
        $input['height'] = $height_int;

        return $input;
    }


    public function addExternalMediaWithoutImport()
    {
        $input = $this->sanitizeAndValidateInput();

        if (isset($input['error'])) {
            return $input;
        }

        $url = $input['url'];
        $width = $input['width'];
        $height = $input['height'];
        $mime_type = $input['mime-type'];

        if (empty($width) || empty($height) || empty($mime_type)) {
            $image_size = @getimagesize($url);

            if (empty($image_size)) {
                if (empty($mime_type)) {
                    $response = wp_remote_head($url);
                    if (is_array($response) && isset($response['headers']['content-type'])) {
                        $input['mime-type'] = $response['headers']['content-type'];
                    }
                }
                $input['error'] = __('Unable to get the image size.', 'kuetemeier-essentials');
                return $input;
            }

            if (empty($width)) {
                $width = $image_size[0];
            }

            if (empty($height)) {
                $height = $image_size[1];
            }

            if (empty($mime_type)) {
                $mime_type = $image_size['mime'];
            }
        }

        $filename = wp_basename($url);
        $attachment = array(
            'guid' => $url,
            'post_mime_type' => $mime_type,
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
        );
        $attachmentMetaData = array('width' => $width, 'height' => $height, 'file' => $filename);
        $attachmentMetaData['sizes'] = array('full' => $attachmentMetaData);
        $attachment_id = wp_insert_attachment($attachment);
        wp_update_attachment_metadata($attachment_id, $attachmentMetaData);

        $input['id'] = $attachment_id;
        return $input;
    }


    /* --------------------------------------------------------------------------------------------------------------
    * END - add-external-media
    * --------------------------------------------------------------------------------------------------------------- */
}
