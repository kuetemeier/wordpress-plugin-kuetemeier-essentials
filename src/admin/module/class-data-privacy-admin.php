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

namespace Kuetemeier_Essentials\Admin\Module;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once( dirname(__FILE__) . '/class-admin-module.php' );
require_once( plugin_dir_path( __FILE__ ) . '/../../config.php' );


/**
 * Class Kuetemeier_Essentials
 */
class Data_Privacy_Admin extends Admin_Module {

	const OPTION_KEY = 'kuetemeier_essentials_data_privacy';

    function __construct() {
        parent::__construct();
    }

    public function _callback_settings_privacy_check_for_comments( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'wporg' ); ?></p>
    	<input name="MY_options[cb_option]" type="checkbox" id="MY_options[cb_option]" value="1" <?php checked(1, $O['cb_opion']); ?> />
		<?php
    }

    public function _callback_admin_menu() {
		add_submenu_page(
			'kuetemeier_essentials',
			'Data Privacy',
			'Data Privacy',
			\Kuetemeier_Essentials\ADMIN_PAGE_CAPABILITY,
			'kuetemeier_essentials_data_privacy',
			array( &$this, '_callback_option_page_data_privacy' )
		);
    }

	function _callback_option_page_data_privacy() {
		$this->_display_option_page( 'kuetemeier_essentials_data_privacy', \Kuetemeier_Essentials\ADMIN_PAGE_CAPABILITY );
	}

	public function _callback_admin_init() {
		register_setting( self::OPTION_KEY, 'kuetemeier_essentials' );

		add_settings_section(
			'kuetemeier_essentials_dashboard', // id
			__( 'Privacy check for comments', 'kuetemeier_essentials' ), // title
			array( &$this, '_callback_settings_privacy_check_for_comments' ), // callback
			self::OPTION_KEY // page
	 	);
	}
}
