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

	public function oenology_get_settings_page_tabs() {
		$tabs = array(
			'general' => 'General',
			'varietals' => 'Varietals'
			);
		return $tabs;

	}

	protected function _options_page_tabs( $current = 'general' ) {

		if ( isset ( $_GET['tab'] ) ) :
			$current = $_GET['tab'];
		else:
			$current = 'general';
		endif;


		$tabs = $this->oenology_get_settings_page_tabs();
		$links = array();
		foreach( $tabs as $tab => $name ) {
			if ( $tab == $current ) {
				$links[] = '<a class="nav-tab nav-tab-active" href="?page=oenology-settings&tab=$tab">' . $name . '</a>';
			} else {
				$links[] = '<a class="nav-tab" href="?page=kuetemeier_essentials&tab=' . $tab . '">' . $name .'</a>';
			}
		}

		echo '<div id="icon-themes" class="icon32"><br /></div>';
		echo '<h2 class="nav-tab-wrapper">';


		foreach ( $links as $link ) {
		  echo $link;
		}
		echo '</h2>';

	}

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


/*
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
*/


/*
		?>


		<div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php $this->_options_page_tabs(); ?>

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
*/

		?>
     <div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

          <?php $this->_options_page_tabs(); ?>
          <?php /*if ( isset( $_GET['settings-updated'] ) ) {
               echo "<div class='updated'><p>Settings updated successfully.</p></div>";
          } */?>
			<?php settings_errors(); ?>
     <form action="options.php" method="post">
     <?php
     settings_fields( $page_slug );
     do_settings_sections( $page_slug );
     ?>
     <?php $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'general' ); ?>
     <input name="kuetemeier_essentials[submit-<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'oenology'); ?>" />
     <input name="kuetemeier_essentials[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'oenology'); ?>" />
     </form>
     </div>
<?php





  	}

}

