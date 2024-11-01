jQuery( function( $ ) {

	let notchpay_submit = false;

	$( '#wc-notchpay-form' ).hide();

	wcNotchPayFormHandler();

	jQuery( '#notchpay-payment-button' ).click( function() {
		return wcNotchPayFormHandler();
	} );

	jQuery( '#notchpay_form form#order_review' ).submit( function() {
		return wcNotchPayFormHandler();
	} );

	function wcNotchPayCustomFields() {

		let custom_fields = [
			{
				"display_name": "Plugin",
				"variable_name": "plugin",
				"value": "woo-notchpay"
			}
		];

		if ( wc_notchpay_params.meta_order_id ) {

			custom_fields.push( {
				display_name: "Order ID",
				variable_name: "order_id",
				value: wc_notchpay_params.meta_order_id
			} );

		}

		if ( wc_notchpay_params.meta_name ) {

			custom_fields.push( {
				display_name: "Customer Name",
				variable_name: "customer_name",
				value: wc_notchpay_params.meta_name
			} );
		}

		if ( wc_notchpay_params.meta_email ) {

			custom_fields.push( {
				display_name: "Customer Email",
				variable_name: "customer_email",
				value: wc_notchpay_params.meta_email
			} );
		}

		if ( wc_notchpay_params.meta_phone ) {

			custom_fields.push( {
				display_name: "Customer Phone",
				variable_name: "customer_phone",
				value: wc_notchpay_params.meta_phone
			} );
		}

		if ( wc_notchpay_params.meta_billing_address ) {

			custom_fields.push( {
				display_name: "Billing Address",
				variable_name: "billing_address",
				value: wc_notchpay_params.meta_billing_address
			} );
		}

		if ( wc_notchpay_params.meta_shipping_address ) {

			custom_fields.push( {
				display_name: "Shipping Address",
				variable_name: "shipping_address",
				value: wc_notchpay_params.meta_shipping_address
			} );
		}

		if ( wc_notchpay_params.meta_products ) {

			custom_fields.push( {
				display_name: "Products",
				variable_name: "products",
				value: wc_notchpay_params.meta_products
			} );
		}

		return custom_fields;
	}

	function wcNotchPayCustomFilters() {

		let custom_filters = {};

		if ( wc_notchpay_params.card_channel ) {

			if ( wc_notchpay_params.banks_allowed ) {

				custom_filters[ 'banks' ] = wc_notchpay_params.banks_allowed;

			}

			if ( wc_notchpay_params.cards_allowed ) {

				custom_filters[ 'card_brands' ] = wc_notchpay_params.cards_allowed;
			}

		}

		return custom_filters;
	}

	function wcPaymentChannels() {

		let payment_channels = [];

		if ( wc_notchpay_params.bank_channel ) {
			payment_channels.push( 'bank' );
		}

		if ( wc_notchpay_params.card_channel ) {
			payment_channels.push( 'card' );
		}

		if ( wc_notchpay_params.ussd_channel ) {
			payment_channels.push( 'ussd' );
		}

		if ( wc_notchpay_params.qr_channel ) {
			payment_channels.push( 'qr' );
		}

		if ( wc_notchpay_params.bank_transfer_channel ) {
			payment_channels.push( 'bank_transfer' );
		}

		return payment_channels;
	}

	function wcNotchPayFormHandler() {

		$( '#wc-notchpay-form' ).hide();

		if ( notchpay_submit ) {
			notchpay_submit = false;
			return true;
		}

		let $form = $( 'form#payment-form, form#order_review' ),
			notchpay_txnref = $form.find( 'input.notchpay_txnref' ),
			subaccount_code = '',
			charges_account = '',
			transaction_charges = '';

		notchpay_txnref.val( '' );

		if ( wc_notchpay_params.subaccount_code ) {
			subaccount_code = wc_notchpay_params.subaccount_code;
		}

		if ( wc_notchpay_params.charges_account ) {
			charges_account = wc_notchpay_params.charges_account;
		}

		if ( wc_notchpay_params.transaction_charges ) {
			transaction_charges = Number( wc_notchpay_params.transaction_charges );
		}

		let amount = Number( wc_notchpay_params.amount );

		let notchpay_callback = function( transaction ) {
			$form.append( '<input type="hidden" class="notchpay_txnref" name="notchpay_txnref" value="' + transaction.reference + '"/>' );
			notchpay_submit = true;

			$form.submit();

			$( 'body' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				},
				css: {
					cursor: "wait"
				}
			} );
		};

		let paymentData = {
			key: wc_notchpay_params.key,
			email: wc_notchpay_params.email,
			amount: amount,
			ref: wc_notchpay_params.txnref,
			currency: wc_notchpay_params.currency,
			subaccount: subaccount_code,
			bearer: charges_account,
			transaction_charge: transaction_charges,
			metadata: {
				custom_fields: wcNotchPayCustomFields(),
			},
			onSuccess: notchpay_callback,
			onCancel: () => {
				$( '#wc-notchpay-form' ).show();
				$( this.el ).unblock();
			}
		};

		if ( Array.isArray( wcPaymentChannels() ) && wcPaymentChannels().length ) {
			paymentData[ 'channels' ] = wcPaymentChannels();
			if ( !$.isEmptyObject( wcNotchPayCustomFilters() ) ) {
				paymentData[ 'metadata' ][ 'custom_filters' ] = wcNotchPayCustomFilters();
			}
		}

		const notchpay = new NotchNotPop();
		notchpay.newTransaction( paymentData );
	}

} );