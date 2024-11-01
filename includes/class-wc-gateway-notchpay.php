<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Gateway_NotchPay extends WC_Payment_Gateway_CC
{

	/**
	 * Is test mode active?
	 *
	 * @var bool
	 */
	public $testmode;

	/**
	 * Should orders be marked as complete after payment?
	 * 
	 * @var bool
	 */
	public $autocomplete_order;

	/**
	 * Notch Pay payment page type.
	 *
	 * @var string
	 */
	public $payment_page;

	/**
	 * Notch Pay test public key.
	 *
	 * @var string
	 */
	public $test_public_key;

	/**
	 * Notch Pay test secret key.
	 *
	 * @var string
	 */
	public $test_secret_key;

	/**
	 * Notch Pay live public key.
	 *
	 * @var string
	 */
	public $live_public_key;

	/**
	 * Notch Pay live secret key.
	 *
	 * @var string
	 */
	public $live_secret_key;

	/**
	 * Should we save customer cards?
	 *
	 * @var bool
	 */
	public $saved_cards;

	/**
	 * Should Notch Pay split payment be enabled.
	 *
	 * @var bool
	 */
	public $split_payment;

	/**
	 * Should the cancel & remove order button be removed on the pay for order page.
	 *
	 * @var bool
	 */
	public $remove_cancel_order_button;

	/**
	 * Notch Pay sub account code.
	 *
	 * @var string
	 */
	public $subaccount_code;

	/**
	 * Who bears Notch Pay charges?
	 *
	 * @var string
	 */
	public $charges_account;

	/**
	 * A flat fee to charge the sub account for each transaction.
	 *
	 * @var string
	 */
	public $transaction_charges;

	/**
	 * Should custom metadata be enabled?
	 *
	 * @var bool
	 */
	public $custom_metadata;

	/**
	 * Should the order id be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_order_id;

	/**
	 * Should the customer name be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_name;

	/**
	 * Should the billing email be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_email;

	/**
	 * Should the billing phone be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_phone;

	/**
	 * Should the billing address be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_billing_address;

	/**
	 * Should the shipping address be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_shipping_address;

	/**
	 * Should the order items be sent as a custom metadata to Notch Pay?
	 *
	 * @var bool
	 */
	public $meta_products;

	/**
	 * API public key
	 *
	 * @var string
	 */
	public $public_key;

	/**
	 * API secret key
	 *
	 * @var string
	 */
	public $secret_key;

	/**
	 * locked currency
	 */
	public $locked_currency;

	/**
	 * Gateway disabled message
	 *
	 * @var string
	 */
	public $msg;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->id                 = 'notchpay';
		$this->method_title       = __('Notch Pay', 'woo-notchpay');
		$this->method_description = sprintf(__('Notch Pay provides merchants with the tools and services to accept online payments from local and international customers using Mobile Money, Mastercard, Visa, PayPal and bank accounts. <a href="%1$s" target="_blank">Sign up</a> for a Notch Pay Business account, and <a href="%2$s" target="_blank">get your API keys</a>.', 'woo-notchpay'), 'https://business.notchpay.co', 'https://business.notchpay.co/settings/developer');
		$this->has_fields         = true;

		$this->payment_page = $this->get_option('payment_page');

		$this->supports = array(
			'products',
			'refunds'
		);

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Get setting values

		$this->title              = $this->get_option('title');
		$this->description        = 'Credit / Debit Cards, Mobile Money, PayPal or Digital Wallets';
		$this->enabled            = $this->get_option('enabled');
		$this->testmode           = $this->get_option('testmode') === 'yes' ? true : false;
		$this->autocomplete_order = $this->get_option('autocomplete_order') === 'yes' ? true : false;

		$this->test_public_key = $this->get_option('test_public_key');
		$this->test_secret_key = $this->get_option('test_secret_key');

		$this->live_public_key = $this->get_option('live_public_key');
		$this->live_secret_key = $this->get_option('live_secret_key');

		$this->locked_currency = $this->get_option('locked_currency');

		$this->saved_cards = $this->get_option('saved_cards') === 'yes' ? true : false;

		$this->split_payment              = $this->get_option('split_payment') === 'yes' ? true : false;
		$this->remove_cancel_order_button = $this->get_option('remove_cancel_order_button') === 'yes' ? true : false;
		$this->subaccount_code            = $this->get_option('subaccount_code');
		$this->charges_account            = $this->get_option('split_payment_charge_account');
		$this->transaction_charges        = $this->get_option('split_payment_transaction_charge');

		$this->custom_metadata = $this->get_option('custom_metadata') === 'yes' ? true : false;

		$this->meta_order_id         = $this->get_option('meta_order_id') === 'yes' ? true : false;
		$this->meta_name             = $this->get_option('meta_name') === 'yes' ? true : false;
		$this->meta_email            = $this->get_option('meta_email') === 'yes' ? true : false;
		$this->meta_phone            = $this->get_option('meta_phone') === 'yes' ? true : false;
		$this->meta_billing_address  = $this->get_option('meta_billing_address') === 'yes' ? true : false;
		$this->meta_shipping_address = $this->get_option('meta_shipping_address') === 'yes' ? true : false;
		$this->meta_products         = $this->get_option('meta_products') === 'yes' ? true : false;

		$this->public_key = $this->testmode ? $this->test_public_key : $this->live_public_key;
		$this->secret_key = $this->testmode ? $this->test_secret_key : $this->live_secret_key;

		// Hooks
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

		add_action('admin_notices', array($this, 'admin_notices'));
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

		// Payment listener/API hook.
		add_action('woocommerce_api_wc_gateway_notchpay', array($this, 'verify_notchpay_transaction'));

		// Webhook listener/API hook.
		add_action('woocommerce_api_wc_notchpay_webhook', array($this, 'process_webhooks'));

		// Check if the gateway can be used.
		if (!$this->is_valid_for_use()) {
			$this->enabled = false;
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 */
	public function is_valid_for_use()
	{
		return true;
	}

	/**
	 * Display notchpay payment icon.
	 */
	public function get_icon()
	{

		// $base_location = wc_get_base_location();

		$icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/woo-notchpay.png', WC_NOTCHPAY_MAIN_FILE)) . '" alt="Notch Pay Payment Options" />';

		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}

	/**
	 * Check if Notch Pay merchant details is filled.
	 */
	public function admin_notices()
	{

		if ($this->enabled == 'no') {
			return;
		}

		// Check required fields.
		if (!$this->public_key) {
			echo '<div class="error"><p>' . sprintf(__('Please enter your Notch Pay merchant details <a href="%s">here</a> to be able to use the Notch Pay WooCommerce plugin.', 'woo-notchpay'), admin_url('admin.php?page=wc-settings&tab=checkout&section=notchpay')) . '</p></div>';
			return;
		}
	}

	/**
	 * Check if Notch Pay gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available()
	{

		if ('yes' == $this->enabled) {

			if (!$this->public_key) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options()
	{

?>

		<h2><?php _e('Notch Pay', 'woo-notchpay'); ?>
			<?php
			if (function_exists('wc_back_link')) {
				wc_back_link(__('Return to payments', 'woo-notchpay'), admin_url('admin.php?page=wc-settings&tab=checkout'));
			}
			?>
		</h2>

		<h4>
			<strong><?php printf(__('Optional: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%1$s" target="_blank" rel="noopener noreferrer">here</a> to the URL below<span style="color: red"><pre><code>%2$s</code></pre></span>', 'woo-notchpay'), 'https://business.notchpay.co/settings/developer', WC()->api_request_url('WC_NotchPay_Webhook')); ?></strong>
		</h4>

		<?php

		if ($this->is_valid_for_use()) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';
		} else {
		?>
			<div class="inline error">
				<p><strong><?php _e('Notch Pay Payment Gateway Disabled', 'woo-notchpay'); ?></strong>: <?php echo $this->msg; ?></p>
			</div>

<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{

		$form_fields = array(
			'enabled'                          => array(
				'title'       => __('Enable/Disable', 'woo-notchpay'),
				'label'       => __('Enable Notch Pay', 'woo-notchpay'),
				'type'        => 'checkbox',
				'description' => __('Enable Notch Pay as a payment option on the checkout page.', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __('Title', 'woo-notchpay'),
				'type'        => 'text',
				'description' => __('This controls the payment method title which the user sees during checkout.', 'woo-notchpay'),
				'default'     => __('Notch Pay', 'woo-notchpay'),
				'desc_tip'    => true,
			),
			// 'description'                      => array(
			// 	'title'       => __('Description', 'woo-notchpay'),
			// 	'type'        => 'textarea',
			// 	'description' => __('This controls the payment method description which the user sees during checkout.', 'woo-notchpay'),
			// 	'default'     => __('Credit / Debit Cards, Mobile Money, PayPal or Digital Wallets', 'woo-notchpay'),
			// 	'desc_tip'    => true,
			// ),
			'testmode'                         => array(
				'title'       => __('Test mode', 'woo-notchpay'),
				'label'       => __('Enable Test Mode', 'woo-notchpay'),
				'type'        => 'checkbox',
				'description' => __('Test mode enables you to test payments before going live. <br />Once the LIVE MODE is enabled on your Notch Pay account uncheck this.', 'woo-notchpay'),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
		
			'test_public_key'                  => array(
				'title'       => __('Test Public Key', 'woo-notchpay'),
				'type'        => 'text',
				'description' => __('Enter your Test Public Key here.', 'woo-notchpay'),
				'default'     => '',
			),
			'test_secret_key'                  => array(
				'title'       => __('Test Secret / Hash Key', 'woo-notchpay'),
				'type'        => 'password',
				'description' => __('Enter your Test Secret / Hash Key here', 'woo-notchpay'),
				'default'     => '',
			),
			'live_public_key'                  => array(
				'title'       => __('Live Public Key', 'woo-notchpay'),
				'type'        => 'text',
				'description' => __('Enter your Live Public Key here.', 'woo-notchpay'),
				'default'     => '',
			),
			'live_secret_key'                  => array(
				'title'       => __('Live Secret / Hash Key', 'woo-notchpay'),
				'type'        => 'password',
				'description' => __('Enter your Live Secret / Hash Key here.', 'woo-notchpay'),
				'default'     => '',
			),
			'locked_currency'                     => array(
				'title'       => __('Locked currency', 'woo-notchpay'),
				'type'        => 'select',
				'description' => __('The currency your customers will make transactions', 'woo-notchpay'),
				'default'     => '',
				'desc_tip'    => false,
				'options'     => [
					''          => __('Select One', 'woo-notchpay'),
					"AED" => "AED",
					"AFN" => "AFN",
					"ALL" => "ALL",
					"AMD" => "AMD",
					"ANG" => "ANG",
					"AOA" => "AOA",
					"ARS" => "ARS",
					"AUD" => "AUD",
					"AWG" => "AWG",
					"AZN" => "AZN",
					"BAM" => "BAM",
					"BBD" => "BBD",
					"BDT" => "BDT",
					"BGN" => "BGN",
					"BHD" => "BHD",
					"BIF" => "BIF",
					"BMD" => "BMD",
					"BND" => "BND",
					"BOB" => "BOB",
					"BRL" => "BRL",
					"BSD" => "BSD",
					"BTN" => "BTN",
					"BWP" => "BWP",
					"BYN" => "BYN",
					"BZD" => "BZD",
					"CAD" => "CAD",
					"CDF" => "CDF",
					"CHF" => "CHF",
					"CLP" => "CLP",
					"CNY" => "CNY",
					"COP" => "COP",
					"CRC" => "CRC",
					"CUC" => "CUC",
					"CUP" => "CUP",
					"CVE" => "CVE",
					"CZK" => "CZK",
					"DJF" => "DJF",
					"DKK" => "DKK",
					"DOP" => "DOP",
					"DZD" => "DZD",
					"EGP" => "EGP",
					"ERN" => "ERN",
					"ETB" => "ETB",
					"EUR" => "EUR",
					"FJD" => "FJD",
					"FKP" => "FKP",
					"GBP" => "GBP",
					"GEL" => "GEL",
					"GHS" => "GHS",
					"GIP" => "GIP",
					"GMD" => "GMD",
					"GNF" => "GNF",
					"GTQ" => "GTQ",
					"GYD" => "GYD",
					"HKD" => "HKD",
					"HNL" => "HNL",
					"HRK" => "HRK",
					"HTG" => "HTG",
					"HUF" => "HUF",
					"IDR" => "IDR",
					"ILS" => "ILS",
					"INR" => "INR",
					"IQD" => "IQD",
					"IRR" => "IRR",
					"ISK" => "ISK",
					"JMD" => "JMD",
					"JOD" => "JOD",
					"JPY" => "JPY",
					"KES" => "KES",
					"KGS" => "KGS",
					"KHR" => "KHR",
					"KMF" => "KMF",
					"KPW" => "KPW",
					"KRW" => "KRW",
					"KWD" => "KWD",
					"KYD" => "KYD",
					"KZT" => "KZT",
					"LAK" => "LAK",
					"LBP" => "LBP",
					"LKR" => "LKR",
					"LRD" => "LRD",
					"LSL" => "LSL",
					"LYD" => "LYD",
					"MAD" => "MAD",
					"MDL" => "MDL",
					"MGA" => "MGA",
					"MKD" => "MKD",
					"MMK" => "MMK",
					"MNT" => "MNT",
					"MOP" => "MOP",
					"MRU" => "MRU",
					"MTL" => "MTL",
					"MUR" => "MUR",
					"MVR" => "MVR",
					"MWK" => "MWK",
					"MXN" => "MXN",
					"MYR" => "MYR",
					"MZN" => "MZN",
					"NAD" => "NAD",
					"NGN" => "NGN",
					"NIO" => "NIO",
					"NOK" => "NOK",
					"NPR" => "NPR",
					"NZD" => "NZD",
					"OMR" => "OMR",
					"PAB" => "PAB",
					"PEN" => "PEN",
					"PGK" => "PGK",
					"PHP" => "PHP",
					"PKR" => "PKR",
					"PLN" => "PLN",
					"PYG" => "PYG",
					"QAR" => "QAR",
					"RON" => "RON",
					"RSD" => "RSD",
					"RUB" => "RUB",
					"RWF" => "RWF",
					"SAR" => "SAR",
					"SBD" => "SBD",
					"SCR" => "SCR",
					"SDG" => "SDG",
					"SEK" => "SEK",
					"SGD" => "SGD",
					"SHP" => "SHP",
					"SLL" => "SLL",
					"SOS" => "SOS",
					"SRD" => "SRD",
					"SSP" => "SSP",
					"STN" => "STN",
					"SVC" => "SVC",
					"SYP" => "SYP",
					"SZL" => "SZL",
					"THB" => "THB",
					"TJS" => "TJS",
					"TMT" => "TMT",
					"TND" => "TND",
					"TOP" => "TOP",
					"TRY" => "TRY",
					"TTD" => "TTD",
					"TWD" => "TWD",
					"TZS" => "TZS",
					"UAH" => "UAH",
					"UGX" => "UGX",
					"USD" => "USD",
					"UYU" => "UYU",
					"UZS" => "UZS",
					"VES" => "VES",
					"VND" => "VND",
					"VUV" => "VUV",
					"WST" => "WST",
					"XAF" => "XAF",
					"XCD" => "XCD",
					"XOF" => "XOF",
					"XPF" => "XPF",
					"YER" => "YER",
					"ZAR" => "ZAR",
					"ZMW" => "ZMW",
					"ZWL" => "ZWL",
				  ],
			),
			'payment_page'                     => array(
				'title'       => __('Payment Option', 'woo-notchpay'),
				'type'        => 'select',
				'description' => __('Popup shows the payment popup on the page while Redirect will redirect the customer to Notch Pay to make payment.', 'woo-notchpay'),
				'default'     => 'redirect',
				'desc_tip'    => false,
				'options'     => array(
					''          => __('Select One', 'woo-notchpay'),
					// 'inline'    => __( 'Popup', 'woo-notchpay' ),
					'redirect'  => __('Redirect', 'woo-notchpay'),
				),
			),
		
			'autocomplete_order'               => array(
				'title'       => __('Autocomplete Order After Payment', 'woo-notchpay'),
				'label'       => __('Autocomplete Order', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-autocomplete-order',
				'description' => __('If enabled, the order will be marked as complete after successful payment', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			/* 'split_payment'                    => array(
				'title'       => __( 'Split Payment', 'woo-notchpay' ),
				'label'       => __( 'Enable Split Payment', 'woo-notchpay' ),
				'type'        => 'checkbox',
				'description' => '',
				'class'       => 'woocommerce_notchpay_split_payment',
				'default'     => 'no',
				'desc_tip'    => true,
			), */
			/* 'saved_cards'                      => array(
				'title'       => __('Saved Cards', 'woo-notchpay'),
				'label'       => __('Enable Payment via Saved Cards', 'woo-notchpay'),
				'type'        => 'checkbox',
				'description' => __('If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Notch Pay servers, not on your store.<br>Note that you need to have a valid SSL certificate installed.', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			), */
			'custom_metadata'                  => array(
				'title'       => __('Custom Metadata', 'woo-notchpay'),
				'label'       => __('Enable Custom Metadata', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-metadata',
				'description' => __('If enabled, you will be able to send more information about the order to Notch Pay.', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_name'                        => array(
				'title'       => __('Customer Name', 'woo-notchpay'),
				'label'       => __('Send Customer Name', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-meta-name',
				'description' => __('If checked, the customer full name will be sent to Notch Pay', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_email'                       => array(
				'title'       => __('Customer Email', 'woo-notchpay'),
				'label'       => __('Send Customer Email', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-meta-email',
				'description' => __('If checked, the customer email address will be sent to Notch Pay', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_phone'                       => array(
				'title'       => __('Customer Phone', 'woo-notchpay'),
				'label'       => __('Send Customer Phone', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-meta-phone',
				'description' => __('If checked, the customer phone will be sent to Notch Pay', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_billing_address'             => array(
				'title'       => __('Order Billing Address', 'woo-notchpay'),
				'label'       => __('Send Order Billing Address', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-meta-billing-address',
				'description' => __('If checked, the order billing address will be sent to Notch Pay', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_shipping_address'            => array(
				'title'       => __('Order Shipping Address', 'woo-notchpay'),
				'label'       => __('Send Order Shipping Address', 'woo-notchpay'),
				'type'        => 'checkbox',
				'class'       => 'wc-notchpay-meta-shipping-address',
				'description' => __('If checked, the order shipping address will be sent to Notch Pay', 'woo-notchpay'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
		);

		$this->form_fields = $form_fields;
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields()
	{

		if ($this->description) {
			echo wpautop(wptexturize($this->description));
		}

		if (!is_ssl()) {
			return;
		}

		/* if ($this->supports('tokenization') && is_checkout() && $this->saved_cards && is_user_logged_in()) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->save_payment_method_checkbox();
		} */
	}


	/**
	 * Load admin scripts.
	 */
	public function admin_scripts()
	{

		if ('woocommerce_page_wc-settings' !== get_current_screen()->id) {
			return;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		$notchpay_admin_params = array(
			'plugin_url' => WC_NOTCHPAY_URL,
		);

		wp_enqueue_script('wc_notchpay_admin', plugins_url('assets/js/notchpay-admin' . $suffix . '.js', WC_NOTCHPAY_MAIN_FILE), array(), WC_NOTCHPAY_VERSION, true);

		wp_localize_script('wc_notchpay_admin', 'wc_notchpay_admin_params', $notchpay_admin_params);
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id
	 *
	 * @return array|void
	 */
	public function process_payment($order_id)
	{



		if ('redirect' === $this->payment_page) {

			return $this->process_redirect_payment_option($order_id);
		} else {

			$order = wc_get_order($order_id);

			if (is_user_logged_in() && isset($_POST['wc-' . $this->id . '-new-payment-method']) && true === (bool) $_POST['wc-' . $this->id . '-new-payment-method'] && $this->saved_cards) {

				$order->update_meta_data('_wc_notchpay_save_card', true);

				$order->save();
			}

			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url(true),
			);
		}
	}

	/**
	 * Process a redirect payment option payment.
	 *
	 * @since 5.7
	 * @param int $order_id
	 * @return array|void
	 */
	public function process_redirect_payment_option($order_id)
	{

		$order        = wc_get_order($order_id);
		$amount       = $order->get_total();
		$txnref       = $order_id . '_' . time();

		$notchpay_params = array(
			'amount'       => absint($amount),
			'email'        => $order->get_billing_email(),
			'currency'     => $order->get_currency(),
			'reference'    => $txnref,
			'callback' => WC()->api_request_url('WC_Gateway_NotchPay'),
		);

		if($this->locked_currency != null) {
			$notchpay_params['locked_currency'] = $this->locked_currency;
		}

		if ($this->meta_name) {
			$notchpay_params['name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		}

		/* if ($this->meta_phone) {
			$notchpay_params['phone'] = $order->get_billing_phone();
		} */

		$order->update_meta_data('_notchpay_txn_ref', $txnref);
		$order->save();

		$notchpay_url = 'https://api.notchpay.co/payments/initialize/';

		$headers = array(
			'Authorization' =>  $this->public_key,
			'Content-Type'  => 'application/json',
			'Accept'  => 'application/json',
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
			"sslverify" => false,
			'body'    => json_encode($notchpay_params),
		);

	

		$request = wp_remote_post($notchpay_url, $args);

		if (!is_wp_error($request) && 201 === wp_remote_retrieve_response_code($request)) {

			$notchpay_response = json_decode(wp_remote_retrieve_body($request));

			return array(
				'result'   => 'success',
				'redirect' => $notchpay_response->authorization_url,
			);
		} else {
			wc_add_notice(__('Unable to process payment try again', 'woo-notchpay'), 'error');

			return;
		}
	}

	/**
	 * Process a token payment.
	 *
	 * @param $token
	 * @param $order_id
	 *
	 * @return bool
	 */
	public function process_token_payment($token, $order_id)
	{

		if ($token && $order_id) {

			$order = wc_get_order($order_id);

			$order_amount = $order->get_total() * 100;
			$txnref       = $order_id . '_' . time();

			$order->update_meta_data('_notchpay_txn_ref', $txnref);
			$order->save();

			$notchpay_url = 'https://api.notchpay.co/transaction/charge_authorization';

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->secret_key,
			);

			$metadata['custom_fields'] = $this->get_custom_fields($order_id);

			if (strpos($token, '###') !== false) {
				$payment_token  = explode('###', $token);
				$auth_code      = $payment_token[0];
				$customer_email = $payment_token[1];
			} else {
				$auth_code      = $token;
				$customer_email = $order->get_billing_email();
			}

			$body = array(
				'email'              => $customer_email,
				'amount'             => absint($order_amount),
				'metadata'           => $metadata,
				'authorization_code' => $auth_code,
				'reference'          => $txnref,
				'currency'           => $order->get_currency(),
			);

			$args = array(
				'body'    => json_encode($body),
				'headers' => $headers,
				'timeout' => 60,
			);

			$request = wp_remote_post($notchpay_url, $args);

			$response_code = wp_remote_retrieve_response_code($request);

			if (!is_wp_error($request) && in_array($response_code, array(200, 400), true)) {

				$notchpay_response = json_decode(wp_remote_retrieve_body($request));

				if ((200 === $response_code) && ('success' === strtolower($notchpay_response->data->status))) {

					$order = wc_get_order($order_id);

					if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {

						wp_redirect($this->get_return_url($order));

						exit;
					}

					$order_total      = $order->get_total();
					$order_currency   = $order->get_currency();
					$currency_symbol  = get_woocommerce_currency_symbol($order_currency);
					$amount_paid      = $notchpay_response->data->amount / 100;
					$notchpay_ref     = $notchpay_response->data->reference;
					$payment_currency = $notchpay_response->data->currency;
					$gateway_symbol   = get_woocommerce_currency_symbol($payment_currency);

					// check if the amount paid is equal to the order amount.
					if ($amount_paid < absint($order_total)) {

						$order->update_status('on-hold', '');

						$order->add_meta_data('_transaction_id', $notchpay_ref, true);

						$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
						$notice_type = 'notice';

						// Add Customer Order Note
						$order->add_order_note($notice, 1);

						// Add Admin Order Note
						$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $notchpay_ref);
						$order->add_order_note($admin_order_note);

						wc_add_notice($notice, $notice_type);
					} else {

						if ($payment_currency !== $order_currency) {

							$order->update_status('on-hold', '');

							$order->update_meta_data('_transaction_id', $notchpay_ref);

							$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note($notice, 1);

							// Add Admin Order Note
							$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $notchpay_ref);
							$order->add_order_note($admin_order_note);

							function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

							wc_add_notice($notice, $notice_type);
						} else {

							$order->payment_complete($notchpay_ref);

							$order->add_order_note(sprintf('Payment via Notch Pay successful (Transaction Reference: %s)', $notchpay_ref));

							if ($this->is_autocomplete_order_enabled($order)) {
								$order->update_status('completed');
							}
						}
					}

					$order->save();

					$this->save_subscription_payment_token($order_id, $notchpay_response);

					WC()->cart->empty_cart();

					return true;
				} else {

					$order_notice  = __('Payment was declined by Notch Pay.', 'woo-notchpay');
					$failed_notice = __('Payment failed using the saved card. Kindly use another payment option.', 'woo-notchpay');

					if (!empty($notchpay_response->message)) {

						$order_notice  = sprintf(__('Payment was declined by Notch Pay. Reason: %s.', 'woo-notchpay'), $notchpay_response->message);
						$failed_notice = sprintf(__('Payment failed using the saved card. Reason: %s. Kindly use another payment option.', 'woo-notchpay'), $notchpay_response->message);
					}

					$order->update_status('failed', $order_notice);

					wc_add_notice($failed_notice, 'error');

					do_action('wc_gateway_notchpay_process_payment_error', $failed_notice, $order);

					return false;
				}
			}
		} else {

			wc_add_notice(__('Payment Failed.', 'woo-notchpay'), 'error');
		}
	}

	/**
	 * Show new card can only be added when placing an order notice.
	 */
	public function add_payment_method()
	{

		wc_add_notice(__('You can only add a new card when placing an order.', 'woo-notchpay'), 'error');

		return;
	}

	/**
	 * Displays the payment page.
	 *
	 * @param $order_id
	 */
	public function receipt_page($order_id)
	{

		$order = wc_get_order($order_id);

		echo '<div id="wc-notchpay-form">';

		echo '<p>' . __('Thank you for your order, please click the button below to pay with Notch Pay.', 'woo-notchpay') . '</p>';

		echo '<div id="notchpay_form"><form id="order_review" method="post" action="' . WC()->api_request_url('WC_Gateway_NotchPay') . '"></form><button class="button" id="notchpay-payment-button">' . __('Pay Now', 'woo-notchpay') . '</button>';

		if (!$this->remove_cancel_order_button) {
			echo '  <a class="button cancel" id="notchpay-cancel-payment-button" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', 'woo-notchpay') . '</a></div>';
		}

		echo '</div>';
	}

	/**
	 * Verify Notch Pay payment.
	 */
	public function verify_notchpay_transaction()
	{

		if (isset($_REQUEST['notchpay_trxref'])) {
			$notchpay_txn_ref = sanitize_text_field($_REQUEST['notchpay_trxref']);
		} else {
			$notchpay_txn_ref = false;
		}
		

		@ob_clean();

		if ($notchpay_txn_ref) {

			$notchpay_response = $this->get_notchpay_transaction(sanitize_text_field($_REQUEST['reference']));

			if (false !== $notchpay_response) {

				if ('complete' == $notchpay_response->transaction->status) {

					

					$order_details = explode('_', $notchpay_response->transaction->merchant_reference);
					$order_id      = (int) $order_details[0];
					$order         = wc_get_order($order_id);


					if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {

						wp_redirect($this->get_return_url($order));

						exit;
					}

					$order_total      = $order->get_total();
					$order_currency   = $order->get_currency();
					$currency_symbol  = get_woocommerce_currency_symbol($order_currency);
					$amount_paid      = $notchpay_response->transaction->converted_amount;
					$notchpay_ref     = $notchpay_response->transaction->reference;
					$payment_currency = strtoupper($notchpay_response->transaction->currency);
					$gateway_symbol   = get_woocommerce_currency_symbol($payment_currency);

					// check if the amount paid is equal to the order amount.
					if ($amount_paid < absint($order_total)) {

						$order->update_status('on-hold', '');

						add_post_meta($order_id, '_transaction_id', $notchpay_ref, true);

						$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
						$notice_type = 'notice';

						// Add Customer Order Note
						$order->add_order_note($notice, 1);

						// Add Admin Order Note
						$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong> Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $notchpay_ref);
						$order->add_order_note($admin_order_note);

						function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

						wc_add_notice($notice, $notice_type);
					} else {

						if ($payment_currency !== $order_currency) {

							$order->update_status('on-hold', '');

							$order->update_meta_data('_transaction_id', $notchpay_ref);

							$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note($notice, 1);

							// Add Admin Order Note
							$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $notchpay_ref);
							$order->add_order_note($admin_order_note);

							function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

							wc_add_notice($notice, $notice_type);
						} else {

							$order->payment_complete($notchpay_ref);
							$order->add_order_note(sprintf(__('Payment via Notch Pay successful (Transaction Reference: %s)', 'woo-notchpay'), $notchpay_ref));

							if ($this->is_autocomplete_order_enabled($order)) {
								$order->update_status('completed');
							}
						}
					}

					$order->save();

					$this->save_card_details($notchpay_response, $order->get_user_id(), $order_id);

					WC()->cart->empty_cart();
				} else {

					$order_details = explode('_', $notchpay_txn_ref);

					$order_id = (int) $order_details[0];

					$order = wc_get_order($order_id);


					$order->update_status('failed', __('Payment was declined by Notch Pay.', 'woo-notchpay'));
				}
			}

			wp_redirect($this->get_return_url($order));

			exit;
		}

		wp_redirect(wc_get_page_permalink('cart'));

		exit;
	}

	/**
	 * Process Webhook.
	 */
	public function process_webhooks()
	{
		$json = file_get_contents('php://input');

		if (!array_key_exists('x-notch-signature', $_SERVER) || (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')) {
			exit;
		}

		$event = json_decode($json);

		if ('payment.complete' !== strtolower($event->event)) {
			return;
		}

		sleep(10);

		$notchpay_response = $this->get_notchpay_transaction($event->data->reference);

		if (false === $notchpay_response) {
			return;
		}

		$order_details = explode('_', $notchpay_response->transaction->merchant_reference);

		$order_id = (int) $order_details[0];

		$order = wc_get_order($order_id);

		if (!$order) {
			return;
		}

		$notchpay_txn_ref = $order->get_meta('_notchpay_txn_ref');

		if ($notchpay_response->transaction->merchant_reference != $notchpay_txn_ref) {
			exit;
		}

		http_response_code(200);

		if (in_array(strtolower($order->get_status()), array('processing', 'completed', 'on-hold'), true)) {
			exit;
		}

		$order_currency = $order->get_currency();

		$currency_symbol = get_woocommerce_currency_symbol($order_currency);

		$order_total = $order->get_total();

		$amount_paid = $notchpay_response->data->amount / 100;

		$notchpay_ref = $notchpay_response->data->reference;

		$payment_currency = strtoupper($notchpay_response->data->currency);

		$gateway_symbol = get_woocommerce_currency_symbol($payment_currency);

		// check if the amount paid is equal to the order amount.
		if ($amount_paid < absint($order_total)) {

			$order->update_status('on-hold', '');

			$order->add_meta_data('_transaction_id', $notchpay_ref, true);

			$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
			$notice_type = 'notice';

			// Add Customer Order Note.
			$order->add_order_note($notice, 1);

			// Add Admin Order Note.
			$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $notchpay_ref);
			$order->add_order_note($admin_order_note);

			function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

			wc_add_notice($notice, $notice_type);

			WC()->cart->empty_cart();
		} else {

			if ($payment_currency !== $order_currency) {

				$order->update_status('on-hold', '');

				$order->update_meta_data('_transaction_id', $notchpay_ref);

				$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-notchpay'), '<br />', '<br />', '<br />');
				$notice_type = 'notice';

				// Add Customer Order Note.
				$order->add_order_note($notice, 1);

				// Add Admin Order Note.
				$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Notch Pay Transaction Reference:</strong> %9$s', 'woo-notchpay'), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $notchpay_ref);
				$order->add_order_note($admin_order_note);

				function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

				wc_add_notice($notice, $notice_type);
			} else {

				$order->payment_complete($notchpay_ref);

				$order->add_order_note(sprintf(__('Payment via Notch Pay successful (Transaction Reference: %s)', 'woo-notchpay'), $notchpay_ref));

				WC()->cart->empty_cart();

				if ($this->is_autocomplete_order_enabled($order)) {
					$order->update_status('completed');
				}
			}
		}

		$order->save();

		$this->save_card_details($notchpay_response, $order->get_user_id(), $order_id);

		exit;
	}

	/**
	 * Save Customer Card Details.
	 *
	 * @param $notchpay_response
	 * @param $user_id
	 * @param $order_id
	 */
	public function save_card_details($notchpay_response, $user_id, $order_id)
	{

		$this->save_subscription_payment_token($order_id, $notchpay_response);

		$order = wc_get_order($order_id);

		$save_card = $order->get_meta('_wc_notchpay_save_card');

		if ($user_id && $this->saved_cards && $save_card && $notchpay_response->data->authorization->reusable && 'card' == $notchpay_response->data->authorization->channel) {

			$gateway_id = $order->get_payment_method();

			$last4          = $notchpay_response->data->authorization->last4;
			$exp_year       = $notchpay_response->data->authorization->exp_year;
			$brand          = $notchpay_response->data->authorization->card_type;
			$exp_month      = $notchpay_response->data->authorization->exp_month;
			$auth_code      = $notchpay_response->data->authorization->authorization_code;
			$customer_email = $notchpay_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			$token = new WC_Payment_Token_CC();
			$token->set_token($payment_token);
			$token->set_gateway_id($gateway_id);
			$token->set_card_type(strtolower($brand));
			$token->set_last4($last4);
			$token->set_expiry_month($exp_month);
			$token->set_expiry_year($exp_year);
			$token->set_user_id($user_id);
			$token->save();

			$order->delete_meta_data('_wc_notchpay_save_card');
			$order->save();
		}
	}

	/**
	 * Save payment token to the order for automatic renewal for further subscription payment.
	 *
	 * @param $order_id
	 * @param $notchpay_response
	 */
	public function save_subscription_payment_token($order_id, $notchpay_response)
	{

		if (!function_exists('wcs_order_contains_subscription')) {
			return;
		}

		if ($this->order_contains_subscription($order_id) && $notchpay_response->data->authorization->reusable && 'card' == $notchpay_response->data->authorization->channel) {

			$auth_code      = $notchpay_response->data->authorization->authorization_code;
			$customer_email = $notchpay_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			// Also store it on the subscriptions being purchased or paid for in the order
			if (function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order_id)) {

				$subscriptions = wcs_get_subscriptions_for_order($order_id);
			} elseif (function_exists('wcs_order_contains_renewal') && wcs_order_contains_renewal($order_id)) {

				$subscriptions = wcs_get_subscriptions_for_renewal_order($order_id);
			} else {

				$subscriptions = array();
			}

			if (empty($subscriptions)) {
				return;
			}

			foreach ($subscriptions as $subscription) {
				$subscription->update_meta_data('_notchpay_token', $payment_token);
				$subscription->save();
			}
		}
	}

	/**
	 * Get custom fields to pass to Notch Pay.
	 *
	 * @param int $order_id WC Order ID
	 *
	 * @return array
	 */
	public function get_custom_fields($order_id)
	{

		$order = wc_get_order($order_id);

		$custom_fields = array();

		$custom_fields[] = array(
			'display_name'  => 'Plugin',
			'variable_name' => 'plugin',
			'value'         => 'woo-notchpay',
		);

		if ($this->custom_metadata) {

			if ($this->meta_order_id) {

				$custom_fields[] = array(
					'display_name'  => 'Order ID',
					'variable_name' => 'order_id',
					'value'         => $order_id,
				);
			}

			if ($this->meta_name) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Name',
					'variable_name' => 'customer_name',
					'value'         => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				);
			}

			if ($this->meta_email) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Email',
					'variable_name' => 'customer_email',
					'value'         => $order->get_billing_email(),
				);
			}

			if ($this->meta_phone) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Phone',
					'variable_name' => 'customer_phone',
					'value'         => $order->get_billing_phone(),
				);
			}

			if ($this->meta_billing_address) {

				$billing_address = $order->get_formatted_billing_address();
				$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

				$notchpay_params['meta_billing_address'] = $billing_address;

				$custom_fields[] = array(
					'display_name'  => 'Billing Address',
					'variable_name' => 'billing_address',
					'value'         => $billing_address,
				);
			}

			if ($this->meta_shipping_address) {

				$shipping_address = $order->get_formatted_shipping_address();
				$shipping_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $shipping_address));

				if (empty($shipping_address)) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

					$shipping_address = $billing_address;
				}
				$custom_fields[] = array(
					'display_name'  => 'Shipping Address',
					'variable_name' => 'shipping_address',
					'value'         => $shipping_address,
				);
			}
		}

		return $custom_fields;
	}

	/**
	 * Process a refund request from the Order details screen.
	 *
	 * @param int $order_id WC Order ID.
	 * @param float|null $amount Refund Amount.
	 * @param string $reason Refund Reason
	 *
	 * @return bool|WP_Error
	 */
	public function process_refund($order_id, $amount = null, $reason = '')
	{
		return new WP_Error('error', __('Can&#39;t process refund at the moment. Try again later.', 'woo-notchpay'));
	}

	/**
	 * Checks if WC version is less than passed in version.
	 *
	 * @param string $version Version to check against.
	 *
	 * @return bool
	 */
	public function is_wc_lt($version)
	{
		return version_compare(WC_VERSION, $version, '<');
	}

	/**
	 * Checks if autocomplete order is enabled for the payment method.
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_autocomplete_order_enabled($order)
	{
		$autocomplete_order = false;

		$payment_method = $order->get_payment_method();

		$notchpay_settings = get_option('woocommerce_' . $payment_method . '_settings');

		if (isset($notchpay_settings['autocomplete_order']) && 'yes' === $notchpay_settings['autocomplete_order']) {
			$autocomplete_order = true;
		}

		return $autocomplete_order;
	}

	/**
	 * Retrieve the payment channels configured for the gateway
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected function get_gateway_payment_channels($order)
	{

		$payment_method = $order->get_payment_method();

		if ('notchpay' === $payment_method) {
			return array();
		}

		$payment_channels = $this->payment_channels;

		if (empty($payment_channels)) {
			$payment_channels = array('card');
		}

		return $payment_channels;
	}

	/**
	 * Retrieve a transaction from Notch pay.
	 *
	 * @since 5.7.5
	 * @param $notchpay_txn_ref
	 * @return false|mixed
	 */
	private function get_notchpay_transaction($notchpay_txn_ref)
	{

		$notchpay_url = 'https://api.notchpay.co/payments/' . $notchpay_txn_ref;

		$headers = array(
			'Authorization' => $this->public_key,
			'Content-Type'  => 'application/json',
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
		);

		$request = wp_remote_get($notchpay_url, $args);

		if (!is_wp_error($request) && 200 === wp_remote_retrieve_response_code($request)) {
			return json_decode(wp_remote_retrieve_body($request));
		}

		return false;
	}

	/**
	 * Get Notch Pay payment icon URL.
	 */
	public function get_logo_url()
	{

		$base_location = wc_get_base_location();

		$url = WC_HTTPS::force_https_url(plugins_url('assets/images/woo-notchpay.png', WC_NOTCHPAY_MAIN_FILE));

		return apply_filters('wc_notchpay_gateway_icon_url', $url, $this->id);
	}

	/**
	 * Check if an order contains a subscription.
	 *
	 * @param int $order_id WC Order ID.
	 *
	 * @return bool
	 */
	public function order_contains_subscription($order_id)
	{

		return function_exists('wcs_order_contains_subscription') && (wcs_order_contains_subscription($order_id) || wcs_order_contains_renewal($order_id));
	}
}
