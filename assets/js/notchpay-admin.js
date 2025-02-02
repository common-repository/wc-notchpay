jQuery( function( $ ) {
	'use strict';

	/**
	 * Object to handle Notch Pay admin functions.
	 */
	var wc_notchpay_admin = {
		/**
		 * Initialize.
		 */
		init: function() {

			// Toggle api key settings.
			$( document.body ).on( 'change', '#woocommerce_notchpay_testmode', function() {
				var test_secret_key = $( '#woocommerce_notchpay_test_secret_key' ).parents( 'tr' ).eq( 0 ),
					test_public_key = $( '#woocommerce_notchpay_test_public_key' ).parents( 'tr' ).eq( 0 ),
					live_secret_key = $( '#woocommerce_notchpay_live_secret_key' ).parents( 'tr' ).eq( 0 ),
					live_public_key = $( '#woocommerce_notchpay_live_public_key' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					test_secret_key.show();
					test_public_key.show();
					live_secret_key.hide();
					live_public_key.hide();
				} else {
					test_secret_key.hide();
					test_public_key.hide();
					live_secret_key.show();
					live_public_key.show();
				}
			} );

			$( '#woocommerce_notchpay_testmode' ).change();

			$( document.body ).on( 'change', '.woocommerce_notchpay_split_payment', function() {
				var subaccount_code = $( '.woocommerce_notchpay_subaccount_code' ).parents( 'tr' ).eq( 0 ),
					subaccount_charge = $( '.woocommerce_notchpay_split_payment_charge_account' ).parents( 'tr' ).eq( 0 ),
					transaction_charge = $( '.woocommerce_notchpay_split_payment_transaction_charge' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					subaccount_code.show();
					subaccount_charge.show();
					transaction_charge.show();
				} else {
					subaccount_code.hide();
					subaccount_charge.hide();
					transaction_charge.hide();
				}
			} );

			$( '#woocommerce_notchpay_split_payment' ).change();

			// Toggle Custom Metadata settings.
			$( '.wc-notchpay-metadata' ).change( function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.wc-notchpay-meta-order-id, .wc-notchpay-meta-name, .wc-notchpay-meta-email, .wc-notchpay-meta-phone, .wc-notchpay-meta-billing-address, .wc-notchpay-meta-shipping-address, .wc-notchpay-meta-products' ).closest( 'tr' ).show();
				} else {
					$( '.wc-notchpay-meta-order-id, .wc-notchpay-meta-name, .wc-notchpay-meta-email, .wc-notchpay-meta-phone, .wc-notchpay-meta-billing-address, .wc-notchpay-meta-shipping-address, .wc-notchpay-meta-products' ).closest( 'tr' ).hide();
				}
			} ).change();

			// Toggle Bank filters settings.
			$( '.wc-notchpay-payment-channels' ).on( 'change', function() {

				var channels = $( ".wc-notchpay-payment-channels" ).val();

				if ( $.inArray( 'card', channels ) != '-1' ) {
					$( '.wc-notchpay-cards-allowed' ).closest( 'tr' ).show();
					$( '.wc-notchpay-banks-allowed' ).closest( 'tr' ).show();
				}
				else {
					$( '.wc-notchpay-cards-allowed' ).closest( 'tr' ).hide();
					$( '.wc-notchpay-banks-allowed' ).closest( 'tr' ).hide();
				}

			} ).change();

			$( ".wc-notchpay-payment-icons" ).select2( {
				templateResult: formatNotchPayPaymentIcons,
				templateSelection: formatNotchPayPaymentIconDisplay
			} );

			$( '#woocommerce_notchpay_test_secret_key, #woocommerce_notchpay_live_secret_key' ).after(
				'<button class="wc-notchpay-toggle-secret" style="height: 30px; margin-left: 2px; cursor: pointer"><span class="dashicons dashicons-visibility"></span></button>'
			);

			$( '.wc-notchpay-toggle-secret' ).on( 'click', function( event ) {
				event.preventDefault();

				let $dashicon = $( this ).closest( 'button' ).find( '.dashicons' );
				let $input = $( this ).closest( 'tr' ).find( '.input-text' );
				let inputType = $input.attr( 'type' );

				if ( 'text' == inputType ) {
					$input.attr( 'type', 'password' );
					$dashicon.removeClass( 'dashicons-hidden' );
					$dashicon.addClass( 'dashicons-visibility' );
				} else {
					$input.attr( 'type', 'text' );
					$dashicon.removeClass( 'dashicons-visibility' );
					$dashicon.addClass( 'dashicons-hidden' );
				}
			} );
		}
	};

	function formatNotchPayPaymentIcons( payment_method ) {
		if ( !payment_method.id ) {
			return payment_method.text;
		}

		var $payment_method = $(
			'<span><img src=" ' + wc_notchpay_admin_params.plugin_url + '/assets/images/' + payment_method.element.value.toLowerCase() + '.png" class="img-flag" style="height: 15px; weight:18px;" /> ' + payment_method.text + '</span>'
		);

		return $payment_method;
	};

	function formatNotchPayPaymentIconDisplay( payment_method ) {
		return payment_method.text;
	};

	wc_notchpay_admin.init();

} );
