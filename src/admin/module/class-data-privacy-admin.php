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

namespace Kuetemeier_Essentials\Admin\Module;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once( plugin_dir_path(__FILE__) . '/class-admin-module.php' );
require_once( plugin_dir_path( __FILE__ ) . '/../../config.php' );
require_once( plugin_dir_path( __FILE__ ) . '/../../class-options.php' );

/**
 * Class Kuetemeier_Essentials
 */
class Data_Privacy_Admin extends Admin_Module {

    function __construct( $options ) {
        parent::__construct( $options );

        $this->_set_admin_page_slug_part( 'data_privacy' );

        // add admin menu page
        $this->_options->add_admin_options_subpage(
        	$this->get_admin_page_slug(),
        	'Kuetemeier > ' . __( 'Data Privacy', 'kuetemeier-essentials' ),
        	__( 'Data Privacy', 'kuetemeier-essentials' )
        );

        // --------------------------------------------------------
        // Sections

        $this->_options->add_option_section(
        	new \Kuetemeier_Essentials\Option_Section(
	        	// ID
	        	'ke_dp_wp_comments',
	        	// title
	        	__('WordPress Comments', 'kuetemeier-essentials' ),
	        	// page
	        	$this->get_admin_page_slug(),
	        	// tab
	        	'',
	        	// content
	        	__( 'Users of the comment system should agree to our privacy policy', 'kuetemeier-essentials' )
	        )
	    );

		$this->_options->add_option_setting(
			new \Kuetemeier_Essentials\Option_Setting_Checkbox(
				// module
				'data_privacy',
				// id
				'add_privacy_field_to_comments',
				// default
				false,
				// label
				__( 'Privacy Checkbox', 'kuetemeier-essentials' ),
				// page
				$this->get_admin_page_slug(),
				// tab
				'',
				// section
				'ke_dp_wp_comments',
				// description
				__( 'Add privacy checkbox to comment fields', 'kuetemeier-essentials' )
			)
		);

    }

    public function _callback_admin_init() {

    }

}
