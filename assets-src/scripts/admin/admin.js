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
		$( '#kuetemeier-url' ).val( '' );
		$( '#kuetemeier-hidden' ).hide();
		$( '#kuetemeier-error' ).text( '' );
		$( '#kuetemeier-width' ).val( '' );
		$( '#kuetemeier-height' ).val( '' );
		$( '#kuetemeier-mime-type' ).val( '' );
	}

	$( 'body' ).on( 'click', '#kuetemeier-clear', function ( e ) {
		clear();
	});

	$( 'body' ).on( 'click', '#kuetemeier-show', function ( e ) {
		$( '#kuetemeier-media-new-panel' ).show();
		e.preventDefault();
	});

	$( 'body' ).on( 'click', '#kuetemeier-in-upload-ui #kuetemeier-add', function ( e ) {
		if ( isAdding ) {
			return;
		}
		isAdding = true;

		$('#kuetemeier-in-upload-ui #kuetemeier-add').prop('disabled', true);

		var postData = {
			'url': $( '#kuetemeier-url' ).val(),
			'width': $( '#kuetemeier-width' ).val(),
			'height': $( '#kuetemeier-height' ).val(),
			'mime-type': $( '#kuetemeier-mime-type' ).val()
		};
		wp.media.post('kuetemeier_add_external_media_without_import', postData )
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
				$( '#kuetemeier-hidden' ).hide();
				$( '#kuetemeier-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#kuetemeier-in-upload-ui #kuetemeier-add').prop('disabled', false);
				isAdding = false;
			}).fail(function (response ) {
				$( '#kuetemeier-error' ).text( response['error'] );
				$( '#kuetemeier-width' ).val( response['width'] );
				$( '#kuetemeier-height' ).val( response['height'] );
				$( '#kuetemeier-mime-type' ).val( response['mime-type'] );
				$( '#kuetemeier-hidden' ).show();
				$( '#kuetemeier-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#kuetemeier-in-upload-ui #kuetemeier-add' ).prop('disabled', false);
				isAdding = false;
			});
		e.preventDefault();
		$( '#kuetemeier-buttons-row .spinner' ).css( 'visibility', 'visible' );
	});

	$( 'body' ).on( 'click', '#kuetemeier-in-upload-ui #kuetemeier-cancel', function (e ) {
		clear();
		$( '#kuetemeier-media-new-panel' ).hide();
		$( '#kuetemeier-buttons-row .spinner' ).css( 'visibility', 'hidden' );
		$( '#kuetemeier-in-upload-ui #kuetemeier-add' ).prop('disabled', false);
		isAdding = false;
		e.preventDefault();
	});
});
