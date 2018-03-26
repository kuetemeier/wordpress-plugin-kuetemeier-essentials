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
	protected $_frontend_classes_loaded = false;
	protected $_admin_classes_loaded = false;

	const AVAILABLE_MODULES = array(
		'core' => 'Core',
		'data-privacy' => 'Data_Privacy',
		'develop' => 'Develop'
	);

	function __construct() {
		$this->_init_all_frontend_classes();

		add_action( 'admin_menu', array( &$this, '_callback_admin_menu__init_all_admin_modules_for_admin_menu' ) );

		add_action( 'admin_init', array( &$this, '_callback_admin_init__init_all_admin_modules_for_admin_init' ) );
	}

/*
	function __destruct() {

	}
*/

	public function _callback_admin_init__init_all_admin_modules_for_admin_init() {
		$this->_ensure_admin_classes_are_loaded();
		$this->foreach_admin( '_callback_admin_init' );
	}

	public function _callback_admin_menu__init_all_admin_modules_for_admin_menu() {
		$this->_ensure_admin_classes_are_loaded();
		$this->foreach_admin( '_callback_admin_menu' );
	}

	protected function _ensure_admin_classes_are_loaded() {
		if ( !$this->_admin_classes_loaded )
			$this->_init_all_admin_classes();
	}

	protected function _init_all_frontend_classes() {
		foreach( array_keys( self::AVAILABLE_MODULES ) as $module_id) {
			$this->_init_module_frontend_class( $module_id );
		}
		$this->_frontend_classes_loaded = true;
	}

	protected function _init_all_admin_classes() {
		foreach( array_keys( self::AVAILABLE_MODULES ) as $module_id) {
			$this->_init_module_admin_class( $module_id );
		}
		$this->_admin_classes_loaded = true;
	}

	protected function _init_module_frontend_class( $module_id ) {
		require_once( dirname(__FILE__) . '/frontend/module/class-'.$module_id.'-frontend.php' );
		$class_name = 'Kuetemeier_Essentials\\Frontend\Module\\'.self::AVAILABLE_MODULES[ $module_id ].'_Frontend';
		$this->set_frontend_class( $module_id, new $class_name() );
	}

	protected function _init_module_admin_class( $module_id ) {
		require_once( dirname(__FILE__) . '/admin/module/class-'.$module_id.'-admin.php' );
		$class_name = 'Kuetemeier_Essentials\\Admin\Module\\'.self::AVAILABLE_MODULES[ $module_id ].'_Admin';
		$this->set_admin_class( $module_id, new $class_name() );
	}

	protected function set_frontend_class( $module_id, $frontend_class ) {
		$this->_modules[$module_id]['frontend_class'] = $frontend_class;
	}

	protected function set_admin_class( $module_id, $frontend_class ) {
		$this->_modules[$module_id]['admin_class'] = $frontend_class;
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

