<?php
/**
 * Vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4:
 *
 * @package    kuetemeier-essentials
 * @author     Jörg Kütemeier (https://kuetemeier.de/kontakt)
 * @license    Apache License, Version 2.0
 * @link       https://kuetemeier.de
 * @copyright  2018 Jörg Kütemeier (https://kuetemeier.de/kontakt)
 *
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Kuetemeier_Essentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

/**
 * Class Modules - mangae kuetemeier_essential module classes
 */
class Options {

	/*protected $_options = array();*/

	function __construct() {
/*		$_temp_options = get_option( self::OPTIONS_KEY, false);

		if ( isset ( $_temp_options ) ) {

		} else {
			$this->init_with_default_values();
		}*/




	}




/*
	public function init_with_default_values() {
		$_options = array(
			'test' => 'Juhu!'
		);
	}

	public function get( $key, $default = false ) {
		$key = trim( $key );
    	if ( empty( $key ) )
        	return $default;

        $value = $_options[$key];
        if ( empty( $value) )
        	return $default;

		return $value;
	}

	public function init() {

	}
*/
}
