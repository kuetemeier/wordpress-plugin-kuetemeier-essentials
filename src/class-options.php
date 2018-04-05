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

require_once plugin_dir_path( __FILE__ ) . '/class-wp-plugin.php';

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

		$this->add_admin_subpage(
			$this->wp_plugin->get_admin_page_slug(),
			'Kuetemeier > Essentials',
			'Essentials',
			array(
				'general' => __( 'General', 'kuetemeier-essentials' ),
				'modules' => __( 'Modules', 'kuetemeier-essentials' ),
				'test'    => 'Test',
			),
			0
		);

		// register callback to actually create the admin page and subpages
		add_action( self::ACTION_PREFIX . 'create_admin_menu', array( &$this, 'callback__create_admin_menu' ) );

		$this->add_option_section(
			new Option_Section(
				// id
				'test',
				// title
				'Test',
				// page
				$this->wp_plugin->get_admin_page_slug(),
				// (optional) tab
				'test',
				// (optional) content
				'Dies ist ein Test'
				// (optional) display_function
			)
		);

		// --------------------------------
		// add OPTION SETTINGS
		// --------------------------------

		$this->add_option_setting(
			new Option_Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				// module
				'default',
				// id
				'test_option_1',
				// default value
				false,
				// label
				'Test mit dieser Option 1',
				// page
				$this->wp_plugin->get_admin_page_slug(),
				// tab
				'test',
				// section
				'test',
				// description
				'A Dies sollte er validieren und speichern'
			)
		);

		$this->add_option_setting(
			new Option_Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'core',
				'test_option_2',
				true,
				'Test mit dieser Option 2',
				$this->wp_plugin->get_admin_page_slug(),
				'test',
				'test',
				'B Dies sollte er validieren und speichern'
			)
		);

		$this->add_option_setting(
			new Option_Setting_Checkbox(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'default',
				'test_option_3',
				true,
				'Test mit dieser Option 3',
				$this->wp_plugin->get_admin_page_slug(),
				'test',
				'test',
				'C Dies sollte er validieren und speichern'
			)
		);

		$this->add_option_setting(
			new Option_Setting_Text(
				// WP_Plugin instance
				$this->get_wp_plugin(),
				'default',
				'test_text',
				'Ein Text',
				'Ein Textfeld',
				$this->wp_plugin->get_admin_page_slug(),
				'test',
				'test',
				'Dies ist ein Textfeld'
			)
		);

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

}


// We want all Option classes in one file for speed reasons.
// phpcs:disable Generic.Files.OneClassPerFile


/**
 * Encapsulate a WordPress option section (see WordPress Settings API).
 */
class Option_Section {


	/** @var string Unique key. */
	protected $id = '';


	/** @var string Admin Page slug this section belongs to. */
	protected $page = '';


	/** @var string Tab this section belongs to. */
	protected $tab = '';


	/** @var string Title of this sectino. */
	protected $title = '';


	/** @var string Optional content (text) of the section. */
	protected $content = '';


	/** @var callable Function called to display the secion. */
	protected $display_function;


	/**
	 * Create a Option Section.
	 *
	 * @param string   $id Unique key.
	 * @param string   $title Title of this sectino.
	 * @param string   $page Admin Page slug this section belongs to.
	 * @param string   $tab (optional) Tab this section belongs to.
	 * @param string   $content (optional) content (text) of the section.
	 * @param callable $display_function Function called to display the secion.
	 *
	 * @since 0.1.0
	 */
	public function __construct( $id, $title, $page, $tab = '', $content = '', $display_function = null ) {
		$this->id = $id;
		$this->title = $title;
		$this->page = $page;
		$this->tab = $tab;
		$this->content = $content;
		$this->set_display_function( $display_function );
	}

	/**
	 * Get unique ID of this section.
	 *
	 * @return string The ID.
	 *
	 * @since 0.1.0
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Get the page slug this section sits on.
	 *
	 * @return strng Page slug.
	 *
	 * @since 0.1.0
	 */
	public function get_page() {
		return $this->page;
	}


	/**
	 * Get the key for the tab this section sits on.
	 *
	 * @return string Tab key.
	 *
	 * @since 0.1.0
	 */
	public function get_tab() {
		return $this->tab;
	}


	/**
	 * Get the title of this section.
	 *
	 * @return string The title.
	 *
	 * @since 0.1.0
	 */
	public function get_title() {
		return $this->title;
	}


	/**
	 * Get the content of this section.
	 *
	 * @return string The content.
	 *
	 * @since 0.1.0
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Default display function.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress default args for display functions.
	 *
	 * @return void
	 *
	 * @see Option_Setting::display_function()
	 * @since 0.1.0
	 */
	public function callback__display_function( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo esc_html( $this->get_content() ); ?>
		</div>
		<?php
	}

	/**
	 * Set a custom display function for this section.
	 *
	 * @param callable $display_function Funktion to be called when the section is displayed.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function set_display_function( $display_function ) {
		if ( empty( $display_function ) ) {
			$this->display_function = array( &$this, 'callback__display_function' );
		} else {
			$this->display_function = $display_function;
		}
	}

	/**
	 * Get the current display function for this section.
	 *
	 * @return callable The display function.
	 *
	 * @since 0.1.0
	 */
	public function get_display_function() {
		return $this->display_function;
	}

	/**
	 * Add this setting to the settings page (register it with WordPress ).
	 *
	 * Built-in pages include 'general', 'reading', 'writing', 'discussion', 'media', etc.
	 * Create your own using add_options_page();
	 *
	 * @since 1.0.0
	 *
	 * @param string $page The slug-name of the settings page on which to show the section.
	 * @param string $tab  Optional. The slug-name of the current tab (if any is present on the settings page).
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_settings_section WordPress Settings API - Sections
	 */
	public function do_add_settings_section( $page = '', $tab = '' ) {
		// Do we have to filter for page slug?
		if ( ! empty( $page ) ) {

			// Yes:
			if ( ! ( $page === $this->get_page() ) ) {
				// Do nothing if page slugs do not match
				return;
			}
		}

		// Do we have to filter for tag slug?
		if ( ! empty( $tab ) ) {

			// Yes:
			if ( ! ( $tab === $this->get_tab() ) ) {
				// Do nothing if tab slugs do not match
				return;
			}
		}

		// Add this section to WordPress sections
		// https://codex.wordpress.org/Function_Reference/add_settings_section
		add_settings_section(
			// String for use in the 'id' attribute of tags.
			$this->get_id(),
			// Title of the section.
			$this->get_title(),
			// Function that fills the section with the desired content. The function should echo its output.
			$this->get_display_function(),
			// The menu page on which to display this section.
			$this->get_page()
		);

	}
}

/**
 * A single setting for an option managed by Options.
 *
 * This is an abstract class and has to be extended for specific use cases.
 */
abstract class Option_Setting {

	/** @var WP_Plugin A valid instance of an object, that is a subclass of WP_Plugin, normally the main plugin object. */
	protected $wp_plugin = null;


	/**
	 * Cache for the db_option_table_base_key.
	 *
	 * @internal
	 * @var string
	 */
	private $cache__db_option_table_base_key = '';


	/** @var string Key of the module this option setting belongs to. */
	protected $module = '';


	/** @var string Unique key. */
	protected $id = '';


	/** @var mixed Default value. */
	protected $default_value = null;


	/** @var string Common name. */
	protected $name = '';


	/** @var string Label for the settings page. */
	protected $label = '';


	/** @var string Key of the admin page this option settings belongs to. */
	protected $page = '';


	/** @var string Key of the tab on the admin page this option settings belongs to. */
	protected $tab = '';


	/** @var string Key of the section on the admin page this option settings belongs to. */
	protected $section = 'default';


	/** @var int Value for display order in the section. */
	protected $display_order = 0;


	/** @var mixed Value for an 'empty' setting. */
	protected $empty_value = '';


	/** @var string Description shown in the admin page. */
	protected $description = '';


	/**
	 * Initialize an OptionSetting.
	 *
	 * @param WP_Plugin $wp_plugin      A valid instance of WP_Plugin.
	 * @param string    $module         Key of the module this option belongs to.
	 * @param string    $id             Unique ID.
	 * @param string    $default_value  Default value.
	 * @param string    $label          Label for the admin page.
	 * @param string    $page           (optional) Key (slug) of the admin page, this setting should be displayed on.
	 * @param string    $tab            (optional) Key (slug) for the tab on the admin page, this setting should be displayed on.
	 * @param string    $section        (optional) Key for the section in the admin page, this option belongs to.
	 * @param string    $description    (optional) Description to be shown next to the setting on the admin page.
	 * @param mixed     $empty_value    (optional) The 'empty' value of this option.
	 * @param int       $display_order  (optional) Display order in the section.
	 */
	public function __construct( $wp_plugin, $module, $id, $default_value, $label, $page = '', $tab = '', $section = '', $description = '', $empty_value = '', $display_order = 0 ) {
		$this->wp_plugin = $wp_plugin;
		$this->module = $module;
		$this->id = $id;
		$this->default_value = $default_value;
		$this->label = $label;
		$this->page = $page;
		$this->tab = $tab;
		$this->set_section( $section );
		$this->set_description( $description );
		$this->empty_value = $empty_value;
		$this->display_order = $display_order;

		// add a cached value for quicker 'get' operations
		$this->cache__db_option_table_base_key = $wp_plugin->get_db_option_table_base_key();
	}


	/**
	 * Returns a valid instance of an object, that implementes WP_Plugin.
	 *
	 * @return WP_Plugin
	 *
	 * @since 0.1.0
	 */
	public function get_wp_plugin() {
		return $this->wp_plugin;
	}

	/**
	 * Key of the module this option setting belongs to.
	 *
	 * @return string The module key.
	 *
	 * @since 0.1.0
	 */
	public function get_module() {
		return $this->module;
	}

	/**
	 * Unique ID
	 *
	 * @return string The ID.
	 *
	 * @since 0.1.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Default value of this option
	 *
	 * @return string The default value.
	 *
	 * @since 0.1.0
	 */
	public function get_default_value() {
		return $this->default_value;
	}

	/**
	 * The 'empty' value of this option.
	 *
	 * For some types it may be `null`, for some types it may be `0` or `''`. Totally your choise.
	 *
	 * @return string The empy value.
	 *
	 * @since 0.1.0
	 */
	public function get_empty_value() {
		return $this->empty_value;
	}

	/**
	 * Admin page label.
	 *
	 * @return string The admin page label.
	 *
	 * @since 0.1.0
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Key for the admin page this option belongs to.
	 *
	 * @return string The page key / slug.
	 *
	 * @since 0.1.0
	 */
	public function get_page() {
		return $this->page;
	}

	/**
	 * Key for the tab this option belongs to.
	 *
	 * @return string The tab key / slug..
	 *
	 * @since 0.1.0
	 */
	public function get_tab() {
		return $this->tab;
	}

	/**
	 * Key for section this option belongs to.
	 *
	 * @return string The section key.
	 *
	 * @since 0.1.0
	 */
	public function get_section() {
		return $this->section;
	}

	/**
	 * Set a new section key.
	 *
	 * @param sting $section New section key for this option.
	 *
	 * @since 0.1.0
	 */
	public function set_section( $section ) {
		$_section = $section;
		if ( empty( $_section ) ) {
			$_section = 'default';
		}
		$this->section = $_section;
	}

	/**
	 * Display order in the section.
	 *
	 * @return int The display order.
	 *
	 * @since 0.1.0
	 */
	public function get_display_order() {
		return $this->display_order;
	}

	/**
	 * Description on the admin page.
	 *
	 * @return string The description.
	 *
	 * @since 0.1.0
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set a new description for this option.
	 *
	 * @param sting $description The new description (html).
	 *
	 * @since 0.1.0
	 */
	public function set_description( $description ) {
		$_description = $description;

		// convert empty string to null
		if ( empty( $_description ) ) {
			$_description = '';
		}

		$this->description = $_description;
	}


	/**
	 * Function to be used to display the setting on the admin page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress display funciton args.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	abstract public function callback__display_setting( $args );


	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * Every subclass must declare a function that returns a sanitzie version of the given value.
	 * E.g. use sanitize_text_field for a string.
	 *
	 * @param  mixed $input An input value to be sanitzied by this function.
	 *
	 * @return mixed Sanitized version of $value or null if we cannot sanitzie the input.
	 */
	abstract public function sanitize( $input );


	/**
	 * Tests, if the option matches the page and the tab. If so, it validates its value
	 * in the `$input` array and retuns it in the `$valid_input` array. Just as a the
	 * normal WordPress Settings API value callback would do it.
	 *
	 * If `$page` or `$tab` does not match, it returns the untouched `$valid_input`array.
	 *
	 * Note: This method is normally not called directly. It's called in the input
	 * validation process from the Options class it is registered to.
	 *
	 * @param string $page Key / slug of the page the value should be validated for.
	 * @param string $tab Key / slug of the tab the value should be validated for.
	 * @param array  $valid_input WordPress valid input array.
	 * @param array  $input WordPress array of form input values.
	 *
	 * @return array `$valid_input` with a validated version, if page and tab matches and value is valid, untouched `$valid_input` otherwise.
	 *
	 * @since 0.1.0
	 */
	public function validate( $page, $tab, $valid_input, $input ) {

		$error = false;
		$error_msg = 'Everything fine.';

		if ( ! empty( $page ) ) {
			if ( $page !== $this->get_page() ) {
				return $valid_input;
			}
		}

		if ( ! empty( $tab ) ) {
			if ( $tab !== $this->get_tab() ) {
				return $valid_input;
			}
		}

		$module = $this->get_module();
		$id = $this->get_id();

		$input_value = $this->sanitize( $this->get_from_array( $input, null ) );
		if ( isset( $input_value ) ) {
			$valid_input = $this->set_in_array( $valid_input, $input_value );

		} else {
			$valid_input = $this->set_in_array( $valid_input, $this->get_empty_value() );
		}

		if ( $error ) {
			// https://codex.wordpress.org/Function_Reference/add_settings_error
			add_settings_error(
				// Slug title of the setting to which this error applies.
				$this->get_id(),
				// Slug-name to identify the error.
				'error',
				// message
				$error_msg
				// optional type, may be: 'error' or 'updated', default: 'error'
			);
		}

		return $valid_input;
	}


	/**
	 * Register option setting field with WordPress API if `$page` and `$tab` matches for this option.
	 *
	 * Note: This method is normally not called directly. It's called in the register
	 * process from the Options class it is registered to.
	 *
	 * @param string $page Key / slug of the page for which the option should be registered.
	 * @param string $tab Key / slug of the tab for which the option should be registered.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function do_add_settings_field( $page, $tab ) {

		// Do we have to filter for page slug?
		if ( ! empty( $page ) ) {

			// Yes:
			if ( ! ( $page === $this->get_page() ) ) {
				// Do nothing if page slugs do not match
				return;
			}
		}
/*
if ( $this->get_id() === 'testdp' ) {
	var_dump( $this );
	wp_die ( "Test" );
}
*/
		// Do we have to filter for tag slug?
		if ( ! empty( $tab ) ) {

			// Yes:
			if ( ! ( $tab === $this->get_tab() ) ) {
				// Do nothing if tab slugs do not match
				return;
			}
		}

		add_settings_field(
			// ID used to identify the field throughout the theme
			$this->get_id(),
			// The label to the left of the option interface element
			$this->get_label(),
			// The name of the function responsible for rendering the option interface
			array( &$this, 'callback__display_setting' ),
			// The page on which this option will be displayed
			$this->get_page(),
			// The name of the section to which this field belongs
			$this->get_section(),
			// The array of arguments to pass to the callback. In this case, just a description.
			array(
				$this->get_description(),
			)
		);
	}


	/**
	 * Get the current value for this option (from the WordPress Option API)
	 *
	 * @param mixed $default (optional) Default value, if this option cannot be found in the Options API, default: `null`.
	 * @param bool  $force   (optional) Force this method to not use the internal cache (likely not used and left untouched).
	 *
	 * @return mixed Value or `$default`.
	 *
	 * @since 0.1.0
	 * @since 0.2.1 Reworked, cache option added, default value changed.
	 */
	public function get( $default = null, $force = false ) {

		if ( ! isset( $default ) ) {
			$default = $this->get_default_value();
		}

		if ( $force ) {
			$option_values = get_option( $this->get_wp_plugin()->get_db_option_table_base_key() );

		} else {
			$option_values = get_option( $this->cache__db_option_table_base_key );
		}

		if ( ! isset( $option_values ) ) {
			return $default;
		}

		// Find our value and return it (or $default, if not found).
		return $this->get_from_array( $option_values, $default );
	}


	/**
	 * Helper function. Get a value from an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to get the value of.
	 * @param mixed $default (optional) Default value, if value is not in `$array`, default: `null`.
	 *
	 * @return mixed The vaule in the array - if found, otherwise `$default`.
	 *
	 * @since 0.1.0
	 * @since 0.2.1 Default value changed.
	 */
	protected function get_from_array( $array, $default = null ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			return $array[ $module ][ $id ];
		}
		return $default;
	}

	/**
	 * Helper function. Sets a value in an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to set the value in.
	 * @param mixed $value The new value.
	 *
	 * @return array The array including the new value.
	 *
	 * @since 0.1.0
	 */
	protected function set_in_array( $array, $value ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( ! isset( $array[ $module ] ) || ! is_array( $array[ $module ] ) ) {
			$array[ $module ] = array();
		}

		$array[ $module ][ $id ] = $value;

		return $array;
	}

	/**
	 * Helper function. Unsets a value in an array, based on the option ID and module ID.
	 *
	 * @param array $array Array to unset the value in.
	 *
	 * @return array The array excluding the new value.
	 *
	 * @since 0.1.0
	 */
	protected function unset_in_array( $array ) {
		$module = $this->get_module();
		$id = $this->get_id();

		if ( isset( $array[ $module ] ) && ( isset( $array[ $module ][ $id ] ) ) ) {
			unset( $array[ $module ][ $id ] );
		}

		return $array;
	}
}


/**
 * A Checkbox implementation of Option_Setting.
 */
class Option_Setting_Checkbox extends Option_Setting {

	// phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod
	// Default and empty values are different.

	/**
	 * Initialize a Checkbox option
	 *
	 * @param WP_Plugin $wp_plugin A valid instance of WP_Plugin.
	 * @param string    $module Key of the module this option belongs to.
	 * @param string    $id Unique ID.
	 * @param string    $default_value Default value.
	 * @param string    $label Label for the admin page.
	 * @param string    $page (optional) Key (slug) of the admin page, this setting should be displayed on.
	 * @param string    $tab (optional) Key (slug) for the tab on the admin page, this setting should be displayed on.
	 * @param string    $section (optional) Key for the section in the admin page, this option belongs to.
	 * @param string    $description (optional) Description to be shown next to the setting on the admin page.
	 * @param mixed     $empty_value (optional) The 'empty' value of this option.
	 * @param int       $display_order (optional) Display order in the section.
	 */
	public function __construct( $wp_plugin, $module, $id, $default_value, $label, $page = '', $tab = '', $section = '', $description = '', $empty_value = 0, $display_order = 0 ) {
		parent::__construct( $wp_plugin, $module, $id, $default_value, $label, $page, $tab, $section, $description, $empty_value, $display_order );

	}
	// phpcs:enable Generic.CodeAnalysis.UselessOverridingMethod


	/**
	 * Sanitize the input value for a Checkbox value.
	 *
	 * Valid values for Checkboxes are 0 and 1
	 *
	 * @param string $input An input vlalue.
	 *
	 * @return int A clean and sanitized version or the 'empty' value, if it cannot be sanitized.
	 *
	 * @since 0.1.12 Does real sanitization.
	 */
	public function sanitize( $input ) {

		switch ( $input ) {
			case 0:
				return 0;
			case 1:
				return 1;
			case '0':
				return 0;
			case '1':
				return 1;
			case true:
				return 1;
			case false:
				return 0;
			default:
				return $this->empty_value();
		}
	}

	/**
	 * Displays this option on an admin page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress display function args.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__display_setting( $args ) {
		$options = \Kuetemeier_Essentials\Kuetemeier_Essentials::instance()->options();

		$value = $this->get();
		$complete_id = $this->get_module() . '_' . $this->get_id();

		// Next, we update the name attribute to access this element's ID in the context of the display options array
		// We also access the show_header element of the options collection in the call to the checked() helper function
		$esc_html = '<input type="checkbox" id="' . esc_attr( $complete_id ) . '" name="' . $this->get_wp_plugin()->get_db_option_table_base_key();
		$esc_html .= '[' . esc_attr( $this->get_module() ) . '][' . esc_attr( $this->get_id() ) . ']" value="1" ' . checked( 1, $value, false ) . '/>';

		// Here, we'll take the first argument of the array and add it to a label next to the checkbox
		$esc_html .= '<label for="' . esc_attr( $complete_id ) . '"> ' . esc_html( $args[0] ) . '</label>';

		// phpcs:disable WordPress.XSS.EscapeOutput
		// $esc_html contains only escaped content.
		echo $esc_html;
		// phpcs:enable WordPress.XSS.EscapeOutput

	}
}

/**
 * A Textbox implementation of Option_Setting.
 *
 * This is a good demonstration how easy it is to implement new option types.
 */
class Option_Setting_Text extends Option_Setting {

	/**
	 * Returns a sanitized version of $input, based on the Option_Settings type.
	 *
	 * @param string $input An input value to be sanitzied by this function.
	 *
	 * @return string   sanitized version of $value or null if we cannot sanitzie the input
	 */
	public function sanitize( $input ) {
		if ( ! isset( $input ) ) {
			return null;
		}

		return sanitize_text_field( $input );
	}


	/**
	 * Displays this option on an admin page.
	 *
	 * WARNING: This is a callback. Never call it directly!
	 * This method has to be public, so WordPress can see and call it.
	 *
	 * @param array $args WordPress display function args.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */
	public function callback__display_setting( $args ) {
		// Get current value.
		$value = $this->get();

		// Assemble a compound and escaped id string.
		$esc_id = esc_attr( $this->get_module() . '_' . $this->get_id() );
		// Assemble an escaped name string. The name attribute is importan, it defines the keys for the $input array in validation.
		$esc_name = esc_attr( $this->get_wp_plugin()->get_db_option_table_base_key() . '[' . $this->get_module() . '][' . $this->get_id() . ']' );

		// Compose output.
		$esc_html = '<input type="text" id="' . $esc_id . '" name="' . $esc_name . '" value="' . esc_attr( $value ) . '" class="regular-text ltr" />';
		$esc_html .= '<p class="description" id="' . $esc_id . '-description">' . esc_html( $args[0] ) . '</p>';

		// phpcs:disable WordPress.XSS.EscapeOutput
		// $esc_html contains only escaped content.
		echo $esc_html;
		// phpcs:enable WordPress.XSS.EscapeOutput
	}
}

// phpcs:enable
