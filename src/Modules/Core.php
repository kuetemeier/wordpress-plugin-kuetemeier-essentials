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
 * Core Module of the Kuetemeier-Essentials Plugin.
 */
final class Core extends \Kuetemeier\WordPress\PluginModule {

	public static function manifest()
	{
		return array(
			'id'          => 'core',
			'short'		  => __('Core', 'kuetemeier-essentials'),
			'description' => __('Kuetemeier Essentials Core Module.', 'kuetemeier-essentials'),

			'config'      => array(
			)
		);
	}

	public function getAdminOptionSettings()
	{

		return array(
			'pages' => array(
				array(
					'id'         => 'kuetemeier',
					'title'      => 'Kuetemeier',
					'menu-title' => 'Kuetemeier',
				)
			),
			'subpages' => array(
				array(
					'id'         => 'kuetemeier',
					'parent'     => 'kuetemeier',
					'title'      => 'Kuetemeier Essentials',
					'menuTitle'  => 'Essentials',
					'priority'   => 0,
					'content'	 => __('Essential Features for WordPress!', 'kuetemeier-essentials')
				)
			),
			'tabs' => array(
				array(
					'id'         => 'general',
					'page'       => 'kuetemeier',
					'title'      => __('General', 'kuetemeier-essentials')
				),
				array(
					'id'         => 'modules',
					'page'       => 'kuetemeier',
					'title'      => __('Modules', 'kuetemeier-essentials'),
				),
			),
			'sections' => array(
				array(
					'id'         => 'core-version',
					'tabs'       => array('general'),
					'title'      => __('Version Information', 'kuetemeier-essentials'),
					'content'	 => array(&$this, 'contentVersion')
				),
				array(
					'id'         => 'core-installed-modules',
					'tab'        =>  'modules',
					'title'      => __('Installed Modules', 'kuetemeier-essentials'),
					'content'	 => array(&$this, 'contentModules')
				),
				array(
					'id'         => 'core-module-management',
					'tab'        =>  'modules',
					'title'      => __('Module Management', 'kuetemeier-essentials'),
					'content'	 => __('Coming soon.', 'kuetemeier-essentials')
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

		echo '<p><b>Kuetemeier Essentials Plugin Version:</b> '.esc_html($plugin->getVersion());
		echo '</p><p><b>License:</b> Alpha Test Version - limitied license</p>'.'<p>'.esc_html($stable).'</p>';
	}

	public function contentModules($section)
	{
		$modules = $section->getPluginModules();

		foreach($modules->keys() as $key) {
            $module = $modules->get($key);
			$manifest = $module->manifest();

			echo '<p><strong>'.esc_html($manifest['short']).'</strong> - '.esc_html($manifest['description']).'</p>';
        }
	}
}
