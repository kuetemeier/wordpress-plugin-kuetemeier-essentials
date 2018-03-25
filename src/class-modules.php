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
class Modules {

	protected $_modules = array();

	const AVAILABLE_MODULES = array(
		'develop' => 'Develop',
		'data-privacy' => 'Data_Privacy'
	);

	function __construct() {

	}

	function __destruct() {

	}

	public function init_all_frontend_modules() {
		foreach( array_keys( self::AVAILABLE_MODULES) as $module_id) {
			$this->init_frontend($module_id);
		}
	}

	public function init_all_admin_modules() {
		foreach( array_keys( self::AVAILABLE_MODULES) as $module_id) {
			$this->init_frontend($module_id);
		}
	}

	public function init_frontend( $module_id ) {
		require_once( dirname(__FILE__) . '/frontend/module/class-'.$module_id.'-frontend.php' );
		$class_name = 'Kuetemeier_Essentials\\Frontend\Module\\'.self::AVAILABLE_MODULES[ $module_id ].'_Frontend';
		$this->set_frontend_class( $module_id, new $class_name() );
	}

	public function init_admin( $module_id ) {
		require_once( dirname(__FILE__) . '/admin/module/class-'.$module_id.'-admin.php' );
		$class_name = 'Kuetemeier_Essentials\\Admin\Module\\'.self::AVAILABLE_MODULES[ $module_id ].'_Admin';
		$this->set_admin_class( $module_id, new $class_name() );
	}

	public function add( $module_id, $frontend_class=null, $admin_class=null ) {
		$this->_modules[$module_id]['frontend_class'] = $frontend_class;
		$this->_modules[$module_id]['admin_class'] = $admin_class;
	}

	public function set_frontend_class( $module_id, $frontend_class ) {
		$this->_modules[$module_id]['frontend_class'] = $frontend_class;
	}

	public function set_admin_class( $module_id, $frontend_class ) {
		$this->_modules[$module_id]['frontend_class'] = $frontend_class;
	}

	public function foreach_frontend( $func, $args=null ) {
		$ret = array();
		foreach ($this->_modules as $module) {
			if ( isset( $module ) ) {
				array_push( $ret, $module['frontend_class']->{$func}( $args ) );
			} else {
				array_push( $ret, null );
			}
		}
		return $ret;
	}

	public function foreach_admin( $func, $args=null ) {
		foreach ($this->_modules as $module) {
			if ( isset ($module) ) {
				$module['admin_class']->{$func}($args);
			}
		}
	}

	public function count() {
		return count( $this->_modules );
	}
}

