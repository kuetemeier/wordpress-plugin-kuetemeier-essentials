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

namespace KuetemeierEssentials\Module;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Core Module of the Kuetemeier-Essentials Plugin.
 */
class Dataprivacy extends \Kuetemeier\WordPress\PluginModule {

	public static function manifest()
	{
		return array(
			'id'         => 'data-privacy',
			'short'		 => __('Data Privacy', 'kuetemeier-essentials'),
			'desciption' => __('Kuetemeier Essentials Data Privacy Module.', 'kuetemeier-essentials'),

			'config'     => array(
				'data-privacy-field-in-comments' => 0,
			)
		);
	}

	public function getAdminOptionSettings()
	{

		return array(
			'subpages' => array(
				array(
					'id'         => 'data-privacy',
					'parent'     => 'kuetemeier',
					'title'      => 'Data Privacy',
					'menuTitle'  => 'Data Privacy',
					'priority'   => 0,
				)
			),
			'tabs' => array(
				array(
					'id'         => 'data-privacy-general',
					'page'       => 'data-privacy',
					'title'      => __('General Data Privacy', 'kuetemeier-essentials')
				),
			),
			'sections' => array(
				array(
					'id'         => 'data-privacy-introduction',
					'tab'        => 'data-privacy-general',
					'title'      => __('Introduction', 'kuetemeier-essentials'),
					'content'	 => 'An introduction to data privacy.'
				),
			),
			'options' => array(
			)
		);
	}

	public function contentVersion($section)
	{
		$plugin = $section->getPlugin();
		$stable = $plugin->is_stable_version() ? __('Stable Version', 'kuetemeier-essentials') : __('Development Version', 'kuetemeier-essentials');

		echo '<p><b>Kuetemeier Essentials Plugin Version:</b> ' . $plugin->getVersion();
		echo '</p><p><b>License:</b> Alpha Test Version - limitied license</p>'.'<p>'.$stable.'</p>';

	}
}
