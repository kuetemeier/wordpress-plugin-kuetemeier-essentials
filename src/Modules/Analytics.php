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

namespace KuetemeierEssentials\Modules;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Analytics Module of the Kuetemeier-Essentials Plugin.
 */
final class Analytics extends \Kuetemeier\WordPress\PluginModule {

	public static function manifest()
	{
		return array(
			'id'          => 'analytics',
			'short'		  => __('Analytics', 'kuetemeier-essentials'),
			'description' => __('Kuetemeier Essentials Analytics Module.', 'kuetemeier-essentials'),
			'page'        => 'kuetemeier-analytics',

			'config'      => array(
				'google-tag-manger-tag' => '',
				'header-code' => '',
				'body-code' => '',
				'footer-code' => '',
				'ga-tracking-id' => '',
				'ga-ip-anonymization' => 1,
			)
		);
	}

	public function getAdminOptionSettings()
	{
		return array(
			'subpages' => array(
				array(
					'id'           => 'kuetemeier-analytics',
					'parent'       => 'kuetemeier',
					'title'        => __('Analytics', 'kuetemeier-essentials'),
					'menuTitle'    => __('Analytics', 'kuetemeier-essentials'),
					'priority'     => 200,
				)
			),
			'tabs' => array(
				array(
					'id'           => 'analytics-google',
					'page'         => 'kuetemeier-analytics',
					'title'        => __('Google Analytics', 'kuetemeier-essentials'),
					'noButtons'    => 0
				),
				/*
				array(
					'id'           => 'analytics-active-campaign',
					'page'         => 'kuetemeier-analytics',
					'title'        => __('Active Campaign', 'kuetemeier-essentials')
				),*/
				array(
					'id'           => 'analytics-header-footer',
					'page'         => 'kuetemeier-analytics',
					'title'        => __('Header & Footer Code', 'kuetemeier-essentials')
				),
			),
			'sections' => array(
				/*array(
					'id'           => 'analytics-google-tag-manager',
					'tab'          => 'analytics-google',
					'title'        => __('Google Tag Manger', 'kuetemeier-essentials'),
					'content'	   => __('Google Analytics and Google Tag Manger Support - Comming soon.', 'kuetemeier-essentials'),
				),*/
				array(
					'id'           => 'analytics-google-simple',
					'tab'          => 'analytics-google',
					'title'        => __('Google Analytics (Simple Version)', 'kuetemeier-essentials'),
					'content'	   => __('This is the quick start for your Google Analytics Tracking, just add your GA_TRACKING_ID and done.', 'kuetemeier-essentials'),
				),
				array(
					'id'           => 'analytics-header-footer',
					'tab'          => 'analytics-header-footer',
					'title'        => __('Insert custom HTML Code into your Website', 'kuetemeier-essentials'),
					'content'      => array(&$this, 'contentAnalyticsHeaderFooter'),
				),
			),
			'options' => array(
				array(
					'id'           => 'header-code',
					'section'      => 'analytics-header-footer',
					'type'         => 'TextArea',
					'code'		   => 1,
					'allowScripts' => 1,
					'large'        => 1,
					'rows'         => 10,
					'title'	       => __('Code to insert into Header', 'kuetemeier-essentials'),
					'description'  => __('Valid HTML Code, that will be placed as close as possibe after the opening <head> tag (global, on every post and page). Shortcodes will be parsed and executed.', 'kuetemeier-essentials' ).'\n'.
					                  __('Note: The Theme has to support the `wp_head` code for this to work - most themes do.', 'kuetemeier-essentials'),
					'customDesign' => 1,
				),
				array(
					'id'           => 'footer-code',
					'section'      => 'analytics-header-footer',
					'type'         => 'TextArea',
					'code'		   => 1,
					'allowScripts' => 1,
					'large'        => 1,
					'rows'         => 10,
					'title'	       => __('Code to insert into Footer', 'kuetemeier-essentials'),
					'description'  => __('Valid HTML Code, that will be directly inserted just before the closing </body> tag (global, on every post and page). Shortcodes will be parsed and executed.', 'kuetemeier-essentials' ),
					'customDesign' => 1,
				),
				array(
					'id'           => 'ga-tracking-id',
					'section'      => 'analytics-google-simple',
					'type'         => 'Text',
					'code'		   => 1,
					'title'	       => __('Google Analytics Tracking ID', 'kuetemeier-essentials'),
					'label'        => __('(e.g. UA-12345678-90)', 'kuetemeier-essentials'),
					'description'  => __('Your Google Analytics Tracking-ID, found in the property settings.', 'kuetemeier-essentials' ),
				),
				array(
					'id'           => 'ga-ip-anonymization',
					'section'      => 'analytics-google-simple',
					'type'         => 'CheckBox',
					'code'		   => 1,
					'title'	       => __('Anonymize IP', 'kuetemeier-essentials'),
					'label'        => __('(highly recommended)', 'kuetemeier-essentials'),
					'description'  => __('Check to anonymize the IP of the visitor, before it is send to Google.', 'kuetemeier-essentials' ),
				),
			)
		);
	}


	public function frontendInit()
	{
		parent::frontendInit();

		if (!empty($this->getOption('header-code'))) {
			add_action('wp_head', array(&$this, 'callback__addHeaderCode'), 0);
		}

		if (!empty($this->getOption('footer-code'))) {
			add_action('wp_footer', array(&$this, 'callback__addFooterCode'), PHP_INT_MAX);
		}

		if (!empty($this->getOption('ga-tracking-id'))) {
			add_action('wp_head', array(&$this, 'callback__GoogleAnalyticsHeadCode'), 0);
		}
	}


	public function contentAnalyticsHeaderFooter()
	{
		echo "<p>";
		_e('Here you can place code, that will be inserted into the header and footer of your Website.', 'kuetemeier-essentials');
		echo "<br />";
		_e('Some themes will already support this feature. Netherless, this fields are theme independent and useful for the ones, that don\'t.', 'kuetemeier-essentials');
		echo "</p><p>";
		_e("If you don't need them, just leave them blank.", 'kuetemeier-essentials');
		echo "</p>";
	}


	public function callback__addHeaderCode()
	{
		echo do_shortcode($this->getOption('header-code'));
	}


	public function callback__addFooterCode()
	{
		echo do_shortcode($this->getOption('footer-code'));
	}

	/**
	 *
	 * @see https://developers.google.com/analytics/devguides/collection/gtagjs/ip-anonymization
	 */
	public function callback__GoogleAnalyticsHeadCode()
	{
		$trackingID = $this->getOption('ga-tracking-id');
		$anonymizeIP = $this->getOption('ga-ip-anonymization');
		$gaConfig = array();

		if ($anonymizeIP) {
			$gaConfig['anonymize_ip'] = 'true';
		}

		?>
		<link rel="dns-prefetch" href="//www.googletagmanager.com">
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-27563916-14"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo esc_attr($trackingID) ?>', <?php echo json_encode($gaConfig) ?>);
		</script>
		<?php
	}
}
