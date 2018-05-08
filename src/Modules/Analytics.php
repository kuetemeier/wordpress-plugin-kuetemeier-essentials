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

			'config'      => array(
				'google-tag-manger-tag' => '',
				'header-code' => '',
				'footer-code' => '',
			)
		);
	}

	public function getAdminOptionSettings()
	{

		return array(
			'subpages' => array(
				array(
					'id'         => 'ke-analytics',
					'parent'     => 'kuetemeier',
					'title'      => __('Analytics', 'kuetemeier-essentials'),
					'menuTitle'  => __('Analytics', 'kuetemeier-essentials'),
					'priority'   => 200,
				)
			),
			'tabs' => array(
				array(
					'id'         => 'ke-analytics-google',
					'page'       => 'ke-analytics',
					'title'      => __('Google', 'kuetemeier-essentials')
				),
				array(
					'id'         => 'ke-analytics-active-campaign',
					'page'       => 'ke-analytics',
					'title'      => __('Active Campaign', 'kuetemeier-essentials')
				),
				array(
					'id'         => 'ke-analytics-header-footer',
					'page'       => 'ke-analytics',
					'title'      => __('Header/Footer', 'kuetemeier-essentials')
				),
			),
			'sections' => array(
				array(
					'id'         => 'ke-analytics-google-tag-manager',
					'tab'        => 'ke-analytics-google',
					'title'      => __('Google Tag Manger', 'kuetemeier-essentials'),
					'content'	 => __('If you use the Google Tag Manager, you can include it here.', 'kuetemeier-essentials'),
				),
			),
			'options' => array(
			)
		);
	}

}
