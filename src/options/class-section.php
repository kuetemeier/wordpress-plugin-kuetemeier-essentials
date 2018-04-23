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

namespace Kuetemeier_Essentials\Options;

/*********************************
 * KEEP THIS for security reasons
 * blocking direct access to our plugin PHP files by checking for the ABSPATH constant
 */
defined( 'ABSPATH' ) || die( 'No direct call!' );


/**
 * Encapsulate a WordPress option section (see WordPress Settings API).
 */
class Section {


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
	 * Display function that do NOT escape the content.
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
	public function callback__display_function_no_esc( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php echo $this->get_content(); ?>
		</div>
		<?php
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
			if ( $display_function === 'NOESC!') {
				$this->display_function = array( &$this, 'callback__display_function_no_esc' );
			} else {
				$this->display_function = $display_function;
			}
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
