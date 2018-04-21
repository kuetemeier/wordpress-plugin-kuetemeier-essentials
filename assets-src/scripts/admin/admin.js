/* eslint-env browser */

/*
 * JavaScript file for admin functions of the
 * WordPress Plugin Kuetemeier-Essentials
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


function kuetemeier_essential_admin() {
	//console.log("Hello World Admin!");
}

jQuery(function ( $ ) {
	var isAdding = false;

	function clear() {
		$( '#emwi-url' ).val( '' );
		$( '#emwi-hidden' ).hide();
		$( '#emwi-error' ).text( '' );
		$( '#emwi-width' ).val( '' );
		$( '#emwi-height' ).val( '' );
		$( '#emwi-mime-type' ).val( '' );
	}

	$( 'body' ).on( 'click', '#emwi-clear', function ( e ) {
		clear();
	});

	$( 'body' ).on( 'click', '#emwi-show', function ( e ) {
		$( '#emwi-media-new-panel' ).show();
		e.preventDefault();
	});

	$( 'body' ).on( 'click', '#emwi-in-upload-ui #emwi-add', function ( e ) {
		if ( isAdding ) {
			return;
		}
		isAdding = true;

		$('#emwi-in-upload-ui #emwi-add').prop('disabled', true);

		var postData = {
			'url': $( '#emwi-url' ).val(),
			'width': $( '#emwi-width' ).val(),
			'height': $( '#emwi-height' ).val(),
			'mime-type': $( '#emwi-mime-type' ).val()
		};
		wp.media.post( 'add_external_media_without_import', postData )
			.done(function ( response ) {
				var attachment = wp.media.model.Attachment.create( response );
				attachment.fetch();

				// Update the attachment list in browser.
				var frame = wp.media.frame || wp.media.library;
				if ( frame ) {
					frame.content.mode( 'browse' );

					// The frame variable may be MediaFrame.Manage or MediaFrame.EditAttachments.
					// In the later case, library = frame.library.
					var library = frame.state().get( 'library' ) || frame.library;
					library.add( attachment ? [ attachment ] : [] );

					if ( wp.media.frame._state != 'library' ) {
						var selection = frame.state().get( 'selection' );
						if ( selection ) {
							selection.add( attachment );
						}
					}
				}

				// Reset the input.
				clear();
				$( '#emwi-hidden' ).hide();
				$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#emwi-in-upload-ui #emwi-add').prop('disabled', false);
				isAdding = false;
			}).fail(function (response ) {
				$( '#emwi-error' ).text( response['error'] );
				$( '#emwi-width' ).val( response['width'] );
				$( '#emwi-height' ).val( response['height'] );
				$( '#emwi-mime-type' ).val( response['mime-type'] );
				$( '#emwi-hidden' ).show();
				$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#emwi-in-upload-ui #emwi-add' ).prop('disabled', false);
				isAdding = false;
			});
		e.preventDefault();
		$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'visible' );
	});

	$( 'body' ).on( 'click', '#emwi-in-upload-ui #emwi-cancel', function (e ) {
		clear();
		$( '#emwi-media-new-panel' ).hide();
		$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
		$( '#emwi-in-upload-ui #emwi-add' ).prop('disabled', false);
		isAdding = false;
		e.preventDefault();
	});
});
