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

require_once( plugin_dir_path( __FILE__ ) . '/../../config.php' );

/**
 * Class Kuetemeier_Essentials
 */
abstract class Admin_Module {

	function __construct() {

	}

	abstract public function _callback_admin_menu();

	abstract public function _callback_admin_init();


 	protected function _display_option_page( $page_slug, $capabilitiy_level = \Kuetemeier_Essentials\CORE_OPTION_PAGE_CAPABILITY ) {
		// check user capabilities
		if ( ! current_user_can( $capabilitiy_level ) ) {
			return;
		}


/*		// add error/update messages

		// check if the user have submitted the settings
		// wordpress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
			add_settings_error( 'kuetemeier_essentials_messages', 'kuetemeier_essentials_message', __( 'Settings Saved', 'kuetemeier_essentials' ), 'updated' );
		}*/

		// show error/update messages
		// settings_errors( 'kuetemeier_essentials_messages' );
		?>

		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<!-- Add the icon to the page -->
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
			<?php settings_errors(); ?>

			<form action="options.php" method="post">
				<?php
				// output fields for the registered setting
				settings_fields( $page_slug );

				// output setting sections and their fields
				do_settings_sections( $page_slug );

				// output save settings button
				submit_button( esc_html__( 'Save Settings', \Kuetemeier_Essentials\TEXTDOMAIN ) );
				?>
			</form>
		</div>
		<?php
  	}

}

