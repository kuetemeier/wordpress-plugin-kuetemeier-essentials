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
 * Aan admin options page.
 */
abstract class Container {


	/** @var string Unique key. */
	private $id = '';


	/** @var string Title of this sectino. */
	private $title = '';


	/** @var string Optional content (text) of the section. */
	private $content = '';


	/** @var callable Function called to display the secion. */
	private $display_function;


	/** @var array A Hash Collection of Elements. */
	private $elements = array();


	/**
	 * Create a Tab.
	 *
	 * @param string   $id               Unique key (only lowercase, `a-z` , `0-9` and single `_`).
	 * @param string   $title            Title of this sectino.
	 * @param array    $elements         (optional) Initial collection of Elements.
	 * @param string   $content          (optional) content (text) of the section.
	 * @param callable $display_function (optional) Function called to display the secion.
	 *                                   Defaults to Collection::callback__default_display_function().
	 *
	 * @since 0.2.2
	 */
	public function __construct( $id, $title, $elements = array(), $content = '', $display_function = null ) {

		if ( empty( $id ) ) {
			wp_die( 'FATAL ERROR: $id of a collection must not be empty!' );
		}

		if ( empty( $title ) ) {
			wp_die( 'FATAL ERROR: $title of a collection must not be empty!' );
		}

		if ( ( ! isset( $elements ) || ( ! is_array( $elements ) ) ) ) {
			wp_die( 'FATAL ERROR: $element of a collection has to be an array.' );
		}

		$this->id = trim( $id );
		$this->title = trim( $title );
		$this->elements = $elements;
		$this->content = $content;
		$this->set_display_function( $display_function );
	}


	/**
	 * Returns the composed unique ID of this Collection.
	 *
	 * @return string Composed, unique ID of this tab.
	 *
	 * @since 0.2.2
	 */
	public function get_uid() {
		return $this->get_id();
	}

	/**
	 * Get unique ID of this section.
	 *
	 * @return string The ID.
	 *
	 * @since 0.2.2
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Get the title of this section.
	 *
	 * @return string The title.
	 *
	 * @since 0.2.2
	 */
	public function get_title() {
		return $this->title;
	}


	/**
	 * Get the content of this section.
	 *
	 * @return string The content.
	 *
	 * @since 0.2.2
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
	 * @since 0.2.2
	 */
	public function callback__default_display_function( $args ) {
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
	 * @since 0.2.2
	 */
	public function set_display_function( $display_function ) {
		if ( empty( $display_function ) ) {
			$this->display_function = array( &$this, 'callback__default_display_function' );
		} else {
			$this->display_function = $display_function;
		}
	}

	/**
	 * Get the current display function for this section.
	 *
	 * @return callable The display function.
	 *
	 * @since 0.2.2
	 */
	public function get_display_function() {
		return $this->display_function;
	}


	/**
	 * Tests if the collection is empty.
	 *
	 * @return bool True if the collection has no elements, false otherwise..
	 *
	 * @since 0.2.2
	 */
	public function is_empty() {
		return empty( $this->elements );
	}


	/**
	 * Returns count of elements in this collection.
	 *
	 * @return int Elements count.
	 *
	 * @since 0.2.2
	 */
	public function count() {
		return count( $this->elements );
	}


	/**
	 * Deletes all elements in this collection.
	 *
	 * @return void
	 *
	 * @since 0.2.0
	 */
	public function clear() {
		$this->elements = array();
	}


	/**
	 * Add an element to the collection.
	 *
	 * @param string $uid       Unique ID (in this collection).
	 * @param mixed  $element   The element to be added.
	 * @param bool   $overwrite (optional) If an element with the same key is present, update (overwrite) it.
	 *
	 * @return bool True: Success element added, false otherwise.
	 *
	 * @since 0.2.2
	 */
	public function add( $uid, $element, $overwrite = false ) {

		if ( ! isset( $uid ) ) {
			return false;
		}

		$uid = trim( $uid );

		if ( isset( $this->elements[ $uid ] ) && ( ! $overwrite ) ) {
			return false;
		}

		$this->elements[ $uid ] = $element;

		return true;
	}


	/**
	 * Returns an element by uid.
	 *
	 * @param string $uid     Unique identifier (key) to search element for.
	 * @param mixed  $default Default value to return, if `$uid` is not found in this collection.
	 *
	 * @since 0.2.2
	 */
	public function get( $uid, $default = null ) {

		if ( ! isset( $uid ) ) {
			return $default;
		}

		if ( isset( $this->elements[ $uid ] ) ) {
			return $this->elements[ $uid ];
		}

		return $default;
	}


	/**
	 * Run method `$method` with `$args` for each element of the collection.
	 *
	 * Note: You have to ensure, that there is a method with this name for each element.
	 *
	 * @param string $method Name of the method to be called.
	 * @param array  $args   (optional) Args to be passed to method call.
	 *
	 * @since 0.2.2
	 */
	public function foreach_call_method( $method, $args = array() ) {
		foreach ( $this->elements as $element ) {
			if ( is_object( $element ) && method_exists( $element, $method ) ) {
				$element->$method( $args );
			}
		}
	}


	/**
	 *
	 *
	 */
}
