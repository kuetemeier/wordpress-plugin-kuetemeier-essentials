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

namespace Kuetemeier_Essentials;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );

require_once dirname( __FILE__ ) . '/class-wp-plugin.php';
require_once dirname( __FILE__ ) . '/options/class-section.php';
require_once dirname( __FILE__ ) . '/options/class-setting.php';
require_once dirname( __FILE__ ) . '/options/class-setting-checkbox.php';
require_once dirname( __FILE__ ) . '/options/class-setting-text.php';


/**
 * Manages all plugin options and interacts with the WordPress Options and Settings API.
 */
final class Options {

	/**
	 * Default prefix for custom actions in this class.
	 */
	const ACTION_PREFIX = 'kuetemeier_essentials_options_';


	/**
	 * Default WordPress capability / role for admin pages to be viewed.
	 */
	const OPTIONS_PAGE_CAPABILITY = 'manage_options';


	/**
	 * Hash of registered admin subpages (option pages).
	 *
	 * @var array
	 * @since 0.1.0
	 * @see Options::add_admin_subpage()
	 */
	private $admin_subpages = array();


	/**
	 * List of all registered option settings.
	 *
	 * @var Option_Setting[]
	 * @since 0.1.0
	 */
	private $option_settings = array();


	/**
	 * List of all registered option sections.
	 *
	 * @var Options_Section[]
	 * @since 0.1.0
	 */
	private $option_sections = array();


	/**
	 * Holds a valid instance of this class.
	 *
	 * @var Options
	 * @since 0.1.0
	 */
	public static $instance = null;


	/**
	 * A valid instance of the Plugin class that has instanciated this class.
	 *
	 * We use it for referencing to the modules of the plugin.
	 *
	 * @var WP_Plugin
	 * @since 0.1.11
	 */
	private $plugin;


	/**
	 * Initialize and create basline for Options.
	 *
	 * @return void
	 * @since 0.1.0
	 *
	 * @param WP_Plugin $wp_plugin Caller of this constructor.
	 */
	public function __construct( $wp_plugin ) {

		if ( ! is_null( self::$instance ) ) {
			wp_die( 'You tried to create a second instance of \Kuetemeier_Essentials\Options' );
		}

		$this->wp_plugin = $wp_plugin;

		// register callback to actually create the admin page and subpages
		add_action( self::ACTION_PREFIX . 'create_admin_menu', array( &$this, 'callback__create_admin_menu' ) );
	}

	// TODO: set default values if there is no database entry

	/**
	 * Register an option setting.
	 *
	 * Hint: Calling this function is cheap, so you can (and it's recommmended) to
	 * use it in Frontend Modules.
	 *
	 * @param Option_Setting $option_setting A valid instance of an Option_Setting object.
	 *
	 * @return bool True if added successfull, false otherwise.
	 *
	 * @since 0.1.0
	 */
	public function add_option_setting( $option_setting ) {
		if ( empty( $option_setting ) ) {
			return false;
		}

		array_push( $this->option_settings, $option_setting );
		return true;
	}


	/**
	 * Register an option section.
	 *
	 * @param Option_Section $option_section A valid instance of an Option_Section object.
	 *
	 * @return bool True if added successfull, false otherwise.
	 *
	 * @since 0.1.0
	 */
	public function add_option_section( $option_section ) {
		if ( empty( $option_section ) ) {
			return false;
		}

		array_push( $this->option_sections, $option_section );
		return true;
	}


	/**
	 * Return the WordPress entry of the option table.
	 *
	 * If no entry exists, this function returns 'false'
	 */
	public function get_db_options() {
		return get_option( $this->get_wp_plugin()->get_db_option_table_base_key() );
	}

	/**
	 * Return the options for a specific module under the OPTIONS_SETTINGS_KEY
	 *
	 * @param string $module_key Key of the module to get the options for.
	 *
	 * @see get_db_options
	 *
	 * @return Array of options or false, if module key did not exists.
	 *
	 * @since 0.1.0
	 * @since 0.1.12 Reworked validation for module key.
	 */
	public function get_db_options_for_module( $module_key ) {
		// get complete options for our key
		$options = $this->get_db_options();

		// We cannot find anything if we have no $module_key
		if ( empty( $module_key ) ) {
			return false;
		}

		if ( ! $this->get_wp_plugin()->modules()->is_valid_module_key( $module_key ) ) {
			return false;
		}

		// No options found for our general db key?
		if ( empty( $options ) ) {
			return false;
		}

		// Does the $module_key exists in our db options?
		if ( array_key_exists( $module_key, $options ) ) {
			return $options[ $module_key ];
		}

		// something went wrong / no data found
		return false;
	}

	/**
	 * Get an option from the WordPress option table.
	 *
	 * @param string $module_key Key of the desired module to retrieve option for.
	 * @param string $option_key Key of the option to be retrieved (in context of `$module_key`).
	 * @param bool   $default (optional) Default value if module or option could not be found, defaults to `false`.
	 *
	 * @return mixed The option value if found, else `$default`.
	 *
	 * @since 0.1.0
	 */
	public function get_option( $module_key, $option_key, $default = false ) {

		$module_options = $this->get_db_options_for_module( $module_key );

		if ( ! $module_options ) {
			return $default;
		}

		if ( array_key_exists( $option_key, $module_options ) ) {
			return $module_options[ $option_key ];
		}

		// If we cannot retriev a value, return default.
		return $default;
	}


	/**
	 * Prepare all admin hooks.
	 *
	 * Normaly should not be called directly. Is called by the WP_Plugin init process.
	 * IMPORTANT: THis must be called AFTER all admin classes of the modules are loaded.
	 * So it's best to leave it to WP_Plugin.
	 *
	 * @see WP_Plugin
	 *
	 * @since 0.1.0
	 * @since 0.1.12 Reworked to WP_Plugin init process
	 */
	public function init_admin_hooks() {
		add_action( 'admin_init', array( &$this, 'callback__admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'callback__admin_menu' ) );
	}


	/**
	 * Register an admin options subpage.
	 *
	 * Tabs are optional.
	 *
	 * Example for tabs:
	 * ```
	 * array(
	 *     'general' => __( 'General', 'kuetemeier-essentials' ),
	 *     'modules' => __( 'Modules', 'kuetemeier-essentials' ),
	 *     'test'    => 'Test',
	 * )
	 * ```
	 *
	 * @param string   $slug                           Slug of admin subpage.
	 * @param string   $title                          Title of the admin subpage.
	 * @param string   $menu_title                     WP Admin menu title.
	 * @param array    $tabs                           (optional) Array of tabs to be created for this subpage.
	 * @param int      $order                          (optional) Menu order index for subpage.
	 * @param string   $parent_slug                    (optional) Slug of the parrent page, defaults to the plugin admin page slug.
	 * @param string   $capability                     (optional) WordPress capability role to view this subpage.
	 * @param callable $callback__options_page_display (optional) Callback for displaying this subpage.
	 * @param callable $callback__validate_options     (optional) Callback for validation options on form submit. Default: Options::callback__validate_options().
	 *
	 * @return void
	 *
	 * @see Options::callback__options_page_() Default validation callback.
	 * @see Options::callback__validate_options() Default validation callback.
	 *
	 * @since 0.1.0
	 * @since 0.2.1 Reworked.
	 */
	public function add_admin_subpage(
		$slug,
		$title,
		$menu_title,
		$tabs = array(),
		$order = 100,
		$parent_slug = null,
		$capability = self::OPTIONS_PAGE_CAPABILITY,
		$callback__options_page_display = null,
		$callback__validate_options = null
	) {

		if ( ! isset( $parent_slug ) ) {
			$parent_slug = $this->get_wp_plugin()->get_admin_page_slug();
		}

		if ( ! isset( $callback__options_page_display ) ) {
			$callback__options_page_display = array( &$this, 'callback__default_options_page_display' );
		}

		if ( ! isset( $callback__validate_options ) ) {
			$callback__validate_options = array( &$this, 'callback__default_validate_options' );
		}

		$this->admin_subpages[ $slug ] = array(
			'slug'                           => $slug,
			'title'                          => $title,
			'menu_title'                     => $menu_title,
			'tabs'                           => $tabs,
			'order'                          => $order,
			'callback__options_page_display' => $callback__options_page_display,
			'capability'                     => $capability,
			'parent_slug'                    => $parent_slug,
			'callback__validate_options'     => $callback__validate_options,
		);
	}


	/**
	 * Callback for WP admin_init. Registeres WP settings for the WP Settings API.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__admin_init() {

		foreach ( $this->admin_subpages as $subpage ) {
			register_setting( $subpage['slug'], $this->get_wp_plugin()->get_db_option_table_base_key(), $subpage['callback__validate_options'] );
		}

	}


	/**
	 * Callback for WP admin_menu. Registeres the admin menu with the WP Settings API.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__admin_menu() {

		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
		do_action( self::ACTION_PREFIX . 'before_create_admin_menu' );

		do_action( self::ACTION_PREFIX . 'create_admin_menu' );

		do_action( self::ACTION_PREFIX . 'after_create_admin_menu' );
		// phpcs::enable

	}


	/**
	 * Callback for WP Settings API. Validates submitted options.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $input Input values form the submitted form.
	 *
	 * @return array Returns an array with valid options. See WP Settings API.
	 *
	 * @since 0.1.0
	 */
	public function callback__default_validate_options( $input ) {
		$valid_input = get_option( $this->get_wp_plugin()->get_db_option_table_base_key() );

		if ( ! $valid_input ) {
			$valid_input = array();
		}

		$submit = '';
		$page = '';
		$tab = '';

		foreach ( array_keys( $input ) as $key ) {
			if ( substr( $key, 0, 7 ) === 'submit|' ) {
				$parts = explode( '|', $key );

				$count = count( $parts );

				if ( $count > 0 ) {
					$submit = $parts[0];
					if ( $count > 1 ) {
						$page = $parts[1];
					}
					if ( $count > 2 ) {
						$tab = $parts[2];
					}
					break;
				}
			}
		}

		if ( ! empty( $submit ) ) {

			foreach ( $this->option_settings as $setting ) {
				$valid_input = $setting->validate( $page, $tab, $valid_input, $input );
			}
		}

		return $valid_input;
	}


	/**
	 * Default callback for an Option_Section that's display function is empty.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress arguments for sections.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__add_settings_section__display_empty_section( $args ) {
		// intentionally empty
	}


	/**
	 * Register all needed settings to the WordPress Settings API for the current page and active tab (if any).
	 *
	 * @param sting  $page_slug Key of the page to add fields for.
	 * @param string $current_tab Key of the current tab in the page, that is active.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	private function register_settings( $page_slug, $current_tab ) {

		register_setting( $this->get_wp_plugin()->get_admin_page_slug(), $this->get_wp_plugin()->get_db_option_table_base_key(), array( &$this, 'callback__validate_options' ) );
		register_setting( 'kuetemeier_essentials_data_privacy', $this->get_wp_plugin()->get_db_option_table_base_key(), array( &$this, 'callback__validate_options' ) );
		register_setting( $page_slug, $this->get_wp_plugin()->get_db_option_table_base_key(), array( &$this, 'callback__validate_options' ) );

		foreach ( $this->option_sections as $option_section ) {
			$option_section->do_add_settings_section( $page_slug, $current_tab );
		}

		add_settings_section(
			'default',
			'',
			array( &$this, 'callback__add_settings_section__display_empty_section' ),
			$page_slug
		);

		foreach ( $this->option_settings as $option_setting ) {
			$option_setting->do_add_settings_field( $page_slug, $current_tab );
		}

	}



	/**
	 * Returns an orderd list of admin subpages.
	 *
	 * Order $this->admin_subpages by `order` property (higher value means lower position in menu).
	 *
	 * @return array Ordered list of admin subpages.
	 *
	 * @since 0.2.1
	 */
	private function get_ordered_list_of_admin_subpages() {

		$cmp = function ( $a, $b ) {
			if ( $a['order'] === $b['order'] ) {
				return 0;
			}

			if ( $a['order'] > $b['order'] ) {
				return 1;
			}

			return -1;
		};

		$sorted = array_values( $this->admin_subpages );

		usort( $sorted, $cmp );

		return $sorted;
	}


	/**
	 * Create an admin menu of all registered options, pages and subpages.
	 *
	 * For more informations see the WordPress Settings API.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__create_admin_menu() {

		// add top level menu page
		add_menu_page(
			'Kuetemeier', // page title
			'Kuetemeier', // menu title
			self::OPTIONS_PAGE_CAPABILITY, // capability
			$this->get_wp_plugin()->get_admin_page_slug(), // menu slug
			array( &$this, 'callback__default_options_page_display' ) // function
		);

		// Use this hook to add your own subpages via add_admin_subpage
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals
		do_action( self::ACTION_PREFIX . 'configure_admin_menu' );
		// phpcs::enable

		// add all configured subpages to WP
		foreach ( $this->get_ordered_list_of_admin_subpages() as $subpage ) {

			// add dashboard (same as top-level)
			add_submenu_page(
				// parent_slug - The slug name for the parent menu (or the file name of a standard WordPress admin page).
				$subpage['parent_slug'],
				// page_title - The text to be displayed in the title tags of the page when the menu is selected.
				$subpage['title'],
				// menu_title - The text to be used for the menu.
				$subpage['menu_title'],
				// capability - The capability required for this menu to be displayed to the user.
				$subpage['capability'],
				// menu_slug - The slug name to refer to this menu by. Should be unique for this menu and only include lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
				$subpage['slug'],
				// display function
				$subpage['callback__options_page_display']
			);
		}
	}


	/**
	 * A litte helper, gets the tabs for a given subpage.
	 *
	 * @param string $subpage (optional) Slug of a subpage, defaults to the default option page of this plugin.
	 *
	 * @return array Tabs for the given subpage.
	 */
	private function get_tabs_for_options_subpage( $subpage = self::OPTIONS_PAGE_SLUG ) {

		if ( array_key_exists( $subpage, $this->admin_subpages ) ) {
			return $this->admin_subpages[ $subpage ]['tabs'];
		}

		return array();
	}


	/**
	 * Print out the tabs for the option page defined by `$page_slug`.
	 *
	 * Marks the tab with the same slug as found in `$_GET['tab']`as "current".
	 *
	 * @param string $page_slug Key (slug) of the page to print the tabs for.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	private function options_page_tabs( $page_slug = self::OPTIONS_PAGE_SLUG ) {

		$tabs = $this->get_tabs_for_options_subpage( $page_slug );

		// phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification
		$current_tab = '';
		if ( isset( $_GET['tab'] ) ) {
			$key = sanitize_key( $_GET['tab'] );

			if ( array_key_exists( $key, $tabs ) ) {
				// Set current tab, if we can find the URL parameter in our tabs list.
				$current_tab = $key;
			}
		} else {
			// Default to first tab in list, if there is a list.
			if ( count( $tabs ) > 0 ) {
				$current_tab = key( $tabs );
			}
		}
		// phpcs:enable WordPress.CSRF.NonceVerification.NoNonceVerification

		$this->register_settings( $page_slug, $current_tab );

		if ( count( $tabs ) > 0 ) {

			echo '<br /></div>';
			echo '<h2 class="nav-tab-wrapper">';

			foreach ( $tabs as $tab => $name ) {
				if ( $tab === $current_tab ) {
					echo '<a class="nav-tab nav-tab-active" href="?page=' . esc_attr( $page_slug ) . '&tab=' . esc_attr( $tab ) . '">' . esc_html( $name ) . '</a>';
				} else {
					echo '<a class="nav-tab" href="?page=' . esc_attr( $page_slug ) . '&tab=' . esc_attr( $tab ) . '">' . esc_html( $name ) . '</a>';
				}
			}

			echo '</h2>';
		}

	}

	/**
	 * Displays the admin option page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__default_options_page_display() {
		// phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification

		// Set default to a known slug
		$page_slug = $this->get_wp_plugin()->get_admin_page_slug();

		// Get active page from URL.
		if ( isset( $_GET['page'] ) ) {
			$_page_slug = sanitize_key( $_GET['page'] );

			// Test if it's a "real" page in our subpage list.
			if ( array_key_exists( $_page_slug, $this->admin_subpages ) ) {
				$page_slug = $_page_slug;
			}
		}

		$tab = ( isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : '' );

		?>
		<div class="wrap">

			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

			<?php $this->options_page_tabs( $page_slug ); ?>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( $page_slug );
				do_settings_sections( $page_slug );
				?>

				<p class="submit">
					<input name="kuetemeier-essentials[submit|<?php echo esc_attr( $page_slug ); ?>|<?php echo esc_attr( $tab ); ?>]" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'kuetemeier-essentials' ); ?>" />
					<input name="kuetemeier-essentials[reset-<?php echo esc_attr( $tab ); ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e( 'Reset Defaults', 'kuetemeier-essentials' ); ?>" />
				</p>
			</form>
		</div>
		<?php

		// phpcs:enable WordPress.CSRF.NonceVerification.NoNonceVerification
	}


	/**
	 * Returns a valid instance of the Main Plugin object. A subclass of WP_Plugin.
	 *
	 * @return WP_Plugin A valid instance of the Main Plugin object.
	 *
	 * @since 0.2.1
	 */
	public function get_wp_plugin() {
		return $this->wp_plugin;
	}


	/**
	 * Get registered section by id.
	 *
	 * @param string $id Section ID.
	 *
	 * @return Opiton_Section A valid section object or `null`.
	 */
	public function get_section( $id ) {
		if ( isset( option_sections[ $id ] ) ) {
			return $this->option_sections[ $id ];
		} else {
			return null;
		}
	}
}
