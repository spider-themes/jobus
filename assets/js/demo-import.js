jQuery( document ).ready( function( $ ) {
	const $template = $( '#jbs-empty-state-template' );
	const $modalTemplate = $( '#jbs-modal-container' );

	if ( $template.length > 0 ) {
		// Clear and move template into the flex container for perfect centering.
		$( '.wrap > #posts-filter' ).html( $template.contents() );
		
		// Move modal into body to avoid overflow issues.
		$( 'body' ).append( $modalTemplate.contents() );
		
		const $overlay = $( '#jbs-modal-popup' );
		const $initial = $( '#jbs-modal-initial' );
		const $loading = $( '#jbs-modal-processing' );

		// Set dynamic job count from PHP.
		$( '#jbs-dynamic-count' ).text( jobus_demo_import.job_count_text );

		// Handle Main Button click -> Show Modal.
		$( document ).on( 'click', '#jbs-trigger-import', function( e ) {
			e.preventDefault();
			$overlay.css( 'display', 'flex' ).hide().fadeIn( 200 );
		} );

		// Cancel Button.
		$( document ).on( 'click', '#jbs-modal-no', function() {
			$overlay.fadeOut( 200 );
		} );

		// Modal "Yes" Button -> Trigger AJAX & Show Spinner.
		$( document ).on( 'click', '#jbs-modal-yes', function() {
			$initial.hide();
			$loading.addClass( 'active' );

			const $statusText = $( '#jbs-import-status' );
			const statusSteps = [
				'Preparing demo environment...',
				'Applying global plugin settings...',
				'Processing collections and taxonomies...',
				'Downloading and sideloading media assets...',
				'Finishing setup & flushing rewrite rules...',
				'Almost done! Finalizing data...',
			];
			
			let currentStep = 0;
			const stepInterval = setInterval( function() {
				if ( currentStep < statusSteps.length - 1 ) {
					currentStep++;
					$statusText.fadeOut( 200, function() {
						$( this ).text( statusSteps[ currentStep ] ).fadeIn( 200 );
					} );
				}
			}, 3000 );

			// Set a flag to prevent page navigation during import.
			window.onbeforeunload = function() {
				return 'Import is in progress. Please do not close the window.';
			};

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'jobus_import_demo_data',
					nonce: jobus_demo_import.nonce,
				},
				success: function( response ) {
					clearInterval( stepInterval );
					if ( response.success ) {
						// Show the modern success template instead of reloading
						$loading.removeClass( 'active' );
						$( '#jbs-modal-success' ).addClass( 'active' );
						window.onbeforeunload = null;
					} else {
						alert( response.data.message || 'Error importing data' );
						window.onbeforeunload = null;
						$loading.removeClass( 'active' );
						$initial.addClass( 'active' ).show();
					}
				},
				error: function() {
					clearInterval( stepInterval );
					alert( 'Server error occurred during import.' );
					window.onbeforeunload = null;
					$loading.removeClass( 'active' );
					$initial.show();
				},
			} );
		} );
	}
} );
