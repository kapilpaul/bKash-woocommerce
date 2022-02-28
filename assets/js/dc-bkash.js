( function ( $, window, document ) {
	var script_loaded = false;
	var loader;

	const dc_bkash = window.dc_bkash;

	const dc_bkash_payment = {
		checkout_form: $( 'form.checkout, form#order_review' ),
		is_bkash_selected: function () {
			return (
				'bkash' === $( '.woocommerce-checkout' )
					.find( 'input[name="payment_method"]:checked' )
					.val()
			);
		},
		set_loading_on: function () {
			dc_bkash_payment.checkout_form.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		},
		set_loading_done: function () {
			dc_bkash_payment.checkout_form.removeClass( 'processing' ).unblock();
		},
		submit_error: function ( errorMessage ) {
			dc_bkash_payment.set_loading_done();
			loader.style.display = 'none';

			$( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();

			dc_bkash_payment.checkout_form.prepend(
				'<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + errorMessage + '</div>'
			);

			dc_bkash_payment.checkout_form.removeClass( 'processing' );
			dc_bkash_payment.checkout_form
				.find( '.input-text, select, input:checkbox' )
				.trigger( 'validate' )
				.blur();

			dc_bkash_payment.scroll_to_notice();
			$( document.body ).trigger( 'checkout_error' );
		},
		scroll_to_notice: function () {
			$( 'html, body' ).animate(
				{
					scrollTop: $( 'form.checkout, form#order_review' ).offset().top - 100
				},
				1000
			);
		},
		set_order: function ( urlPath = wc_checkout_params.checkout_url ) {
			$.ajax( {
				type: 'POST',
				url: urlPath,
				data: dc_bkash_payment.checkout_form.serialize(),
				dataType: 'json'
			} )
				.done( function ( result ) {
					try {
						if ( 'success' === result.result || result.success ) {

							if ( 'object' === typeof result.data ) {
								result = result.data;
							}

							dc_bkash_payment.init_bkash(
								result.order_number,
								result.amount,
								result.create_payment_data
							);

							return;
						} else if ( 'failure' === result.result ) {
							throw new Error( 'Result failure' );
						} else {
							throw new Error( 'Invalid response' );
						}
					} catch ( err ) {

						// Reload page
						if ( true === result.reload ) {
							window.location.reload();
							return;
						}

						// Trigger update in case we need a fresh nonce
						if ( true === result.refresh ) {
							$( document.body ).trigger( 'update_checkout' );
						}

						// Add new errors
						if ( result.messages ) {
							dc_bkash_payment.submit_error( result.messages );
						} else {
							dc_bkash_payment.submit_error(
								'<div class="woocommerce-error">' + wc_checkout_params.i18n_checkout_error + '</div>'
							);
						}
					}
				} )
				.fail( function ( jqXHR, textStatus, errorThrown ) {
					dc_bkash_payment.submit_error(
						'<div class="woocommerce-error">' + errorThrown + '</div>'
					);
				} );
		},
		do_submit: function () {
			dc_bkash_payment.set_loading_on();

			let set_order;

			if ( $( document.body ).hasClass( 'woocommerce-order-pay' ) ) {
				dc_bkash_payment.set_order( dc_bkash.ajax_url );
			} else {
				dc_bkash_payment.set_order();
			}
		},
		load_bkash_script: function () {
			if ( ! script_loaded ) {

				//create loader for bKash
				dc_bkash_payment.create_bkash_loader();

				loader.style.display = 'block';
				window.$ = $.noConflict( true );

				//fetching script
				$.getScript( dc_bkash.script_url, function () {
					loader.style.display = 'none';
					dc_bkash_payment.create_bkash_button();
					script_loaded = true;
				} );
			}
		},
		create_bkash_loader: function () {
			var elem = document.createElement( 'div' );
			elem.className = 'bkash-loader';
			elem.id = 'bkash-loader';
			document.body.appendChild( elem );
			loader = document.getElementById( 'bkash-loader' );
		},
		create_bkash_button: function () {
			var bkashBtn = document.createElement( 'button' );
			bkashBtn.id = 'bKash_button';
			bkashBtn.className = 'btn btn-danger';
			bkashBtn.setAttribute( 'disabled', 'disabled' );
			bkashBtn.style.display = 'none';
			document.body.appendChild( bkashBtn );
		},
		execute_bkash_request: function ( order_number, payment_id ) {
			let execute_payment_data = {
				payment_id: payment_id,
				order_number: order_number,
				action: 'dc-bkash-execute-payment-request',
				_nonce: dc_bkash.nonce
			};

			$.ajax( {
				url: dc_bkash.ajax_url,
				method: 'POST',
				data: execute_payment_data,
				success: function ( response ) {
					if ( response.success && null != response.data.paymentID ) {
						let data = response.data;
						window.location.href = data.order_success_url;
					} else {
						bKash.execute().onError(); //run clean up code
						dc_bkash_payment.show_alert( 'Payment Failed!', response.data.errorMessage );
					}
				},
				error: function () {
					bKash.execute().onError(); // Run clean up code

					dc_bkash_payment.show_alert( 'Payment Failed!', 'Something went wrong!' );
				}
			} );
		},
		init_bkash: function ( order_number, amount, create_payment = false ) {
			loader.style.display = 'block';

			let payment_id;
			let payment_request = {
				amount: amount,
				intent: 'sale',
				currency: 'BDT',
				merchantInvoiceNumber: order_number
			};

			bKash.init( {
				paymentMode: 'checkout',
				paymentRequest: payment_request,
				createRequest: function () {
					if ( ! create_payment ) {
						bKash.create().onError();
						return;
					}

					payment_id = create_payment.paymentID;
					bKash.create().onSuccess( create_payment );
				},
				executeRequestOnAuthorization: function () {
					dc_bkash_payment.execute_bkash_request( order_number, payment_id );
				},
				onClose: function () {
					loader.style.display = 'none';

					dc_bkash_payment.show_alert( 'Opps...', 'Payment Cancelled!' );
				}
			} );

			bKash.reconfigure( {
				paymentRequest: payment_request
			} );

			$( '#bKash_button' ).removeAttr( 'disabled' );
			$( '#bKash_button' ).click();
		},
		show_alert: function( title, text ) {
			Swal.fire( {
				icon: 'error',
				title: title,
				text: text,
				confirmButtonText: 'OK'
			} ).then( ( result ) => {
				loader.style.display = 'none';
				$( dc_bkash_payment.checkout_form ).removeClass( 'processing' ).unblock();
			} );
		},
		init: function () {
			if ( dc_bkash_payment.is_bkash_selected() ) {
				dc_bkash_payment.load_bkash_script();
			}

			//on change load payment script
			dc_bkash_payment.checkout_form.on(
				'change',
				'input[name="payment_method"]',
				function ( e ) {
					$( 'body' ).trigger( 'update_checkout' );

					if ( dc_bkash_payment.is_bkash_selected() ) {
						dc_bkash_payment.load_bkash_script();
					}
				}
			);

			dc_bkash_payment.checkout_form.on( 'click', '#place_order', function ( e ) {
				if ( dc_bkash_payment.is_bkash_selected() ) {
					e.preventDefault();

					dc_bkash_payment.do_submit();
				}
			} );
		}
	};

	dc_bkash_payment.init();
} ( jQuery, window, document ) );
