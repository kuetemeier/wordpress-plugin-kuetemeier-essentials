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
 * Test Module of the Kuetemeier-Essentials Plugin.
 * Only needed for development.
 */
final class Test extends \Kuetemeier\WordPress\PluginModule {

	public static function manifest()
	{
		return array(
			'id'          => 'test',
			'short'		  => __('Test', 'kuetemeier-essentials'),
			'description' => __('Test module for development - Not recommended!.', 'kuetemeier-essentials'),
			'page'        => 'kuetemeier-test',

			'config'      => array(
				'test-text' => '',
				'test-textarea' => 'Test Area',
				'test-number' => 10,
				'test-email' => '',
				'test-select' => 'b',
				'test-radio' => 'b'
			)
		);
	}

	public function getAdminOptionSettings()
	{

		return array(
			'subpages' => array(
				array(
					'id'         => 'kuetemeier-test',
					'parent'     => 'kuetemeier',
					'title'      => __('Test', 'kuetemeier-essentials'),
					'menuTitle'  => __('Test', 'kuetemeier-essentials'),
					'priority'   => 0,
					'content'	 => 'Some glorious content!'
				)
			),
			'tabs' => array(
				array(
					'id'         => 'test-general',
					'page'       => 'kuetemeier-test',
					'title'      => __('Test General', 'kuetemeier-essentials')
				),
				array(
					'id'         => 'test',
					'page'       => 'ke-test',
					'title'      => __('Test', 'kuetemeier-essentials'),
				)
			),
			'sections' => array(
				array(
					'id'         => 'test',
					'page'       => 'ke-test',
					'title'      => __('Test', 'kuetemeier-essentials'),
					'content'	 => 'Einfach ein Test'
				),
				array(
					'id'         => 'test2',
					'tab'        => 'test-general',
					'title'      => __('Test 2', 'kuetemeier-essentials'),
					'content'	 => 'Und noch ein Test'
				),
				array(
					'id'         => 'test3',
					'tab'        => 'test',
					'title'      => __('Test 3', 'kuetemeier-essentials'),
					'content'	 => 'Und noch ein Test'
				),
			),
			'options' => array(
				array(
					'id'          => 'test-text',
					'section'     => 'test2',
					'title'		  => __('Test', 'kuetemeier-essentials'),
					'type'        => 'Text',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
				),
				array(
					'id'          => 'test-textarea',
					'section'     => 'test2',
					'title'		  => __('Test', 'kuetemeier-essentials'),
					'type'        => 'TextArea',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
					'allowHTML'   => 1,
					'allowScripts' => 1,
					'code'		  => true,
					'large'		  => true,
					'cols'		  => 50
				),
				array(
					'id'          => 'test-number',
					'section'     => 'test2',
					'title'		  => __('Number', 'kuetemeier-essentials'),
					'type'        => 'Number',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
				),
				array(
					'id'          => 'test-email',
					'section'     => 'test2',
					'title'		  => __('Email', 'kuetemeier-essentials'),
					'type'        => 'Email',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
				),
				array(
					'id'          => 'test-select',
					'section'     => 'test2',
					'title'		  => __('Select', 'kuetemeier-essentials'),
					'type'        => 'Select',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
					'values'	  => array(
						'1' => 'First',
						'b' => 'Second',
						'c' => 'Third'
					)
				),
				array(
					'id'          => 'test-radio',
					'section'     => 'test2',
					'title'		  => __('Select', 'kuetemeier-essentials'),
					'type'        => 'Radio',
					'label'       => __( 'A Label', 'kuetemeier-essentials' ),
					'description' => __( 'A Description', 'kuetemeier-essentials' ),
					'values'	  => array(
						'1' => 'On',
						'0' => 'Off',
					),
				),
			)
		);
	}
}
