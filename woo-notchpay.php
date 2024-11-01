<?php
/**
 * Plugin Name: Notch Pay for WooCommerce
 * Plugin URI: https://notchpay.co
 * Description: Accept local and international payments on WooCommerce with Notch Pay.
 * Version: 2.1.5
 * Author: Notch Pay
 * Author URI: https://notchpay.co
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * WC requires at least: 7.0
 * WC tested up to: 8.3
 * Text Domain: woo-notchpay
 * Domain Path: /languages
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_NOTCHPAY_MAIN_FILE', __FILE__ );
define( 'WC_NOTCHPAY_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );

define( 'WC_NOTCHPAY_VERSION', '2.1.0' );

/**
 * Initialize Notch Pay WooCommerce payment gateway.
 */
function wc_notchpay_init() {

	load_plugin_textdomain( 'woo-notchpay', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		add_action( 'admin_notices', 'wc_notchpay_wc_missing_notice' );
		return;
	}

	add_action( 'admin_init', 'wc_notchpay_testmode_notice' );

	require_once __DIR__ . '/includes/class-wc-gateway-notchpay.php';

	add_filter( 'woocommerce_payment_gateways', 'wc_add_notchpay_gateway', 99 );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woo_notchpay_plugin_action_links' );

}
add_action( 'plugins_loaded', 'wc_notchpay_init', 99 );

/**
 * Add Settings link to the plugin entry in the plugins menu.
 *
 * @param array $links Plugin action links.
 *
 * @return array
 **/
function woo_notchpay_plugin_action_links( $links ) {

	$settings_link = array(
		'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=notchpay' ) . '" title="' . __( 'View Notch Pay WooCommerce Settings', 'woo-notchpay' ) . '">' . __( 'Settings', 'woo-notchpay' ) . '</a>',
	);

	return array_merge( $settings_link, $links );

}

/**
 * Add Notch Pay Gateway to WooCommerce.
 *
 * @param array $methods WooCommerce payment gateways methods.
 *
 * @return array
 */
function wc_add_notchpay_gateway( $methods ) {

	$methods[] = 'WC_Gateway_NotchPay';

	return $methods;
}

/**
 * Display a notice if WooCommerce is not installed
 */
function wc_notchpay_wc_missing_notice() {
	echo '<div class="error"><p><strong>' . sprintf( __( 'Notch Pay requires WooCommerce to be installed and active. Click %s to install WooCommerce.', 'woo-notchpay' ), '<a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=539' ) . '" class="thickbox open-plugin-details-modal">here</a>' ) . '</strong></p></div>';
}

/**
 * Display the test mode notice.
 **/
function wc_notchpay_testmode_notice() {

	if ( ! class_exists( Notes::class ) ) {
		return;
	}

	if ( ! class_exists( WC_Data_Store::class ) ) {
		return;
	}

	if ( ! method_exists( Notes::class, 'get_note_by_name' ) ) {
		return;
	}

	$test_mode_note = Notes::get_note_by_name( 'notchpay-test-mode' );

	if ( false !== $test_mode_note ) {
		return;
	}

	$notchpay_settings = get_option( 'woocommerce_notchpay_settings' );
	$test_mode         = $notchpay_settings['testmode'] ?? '';

	if ( 'yes' !== $test_mode ) {
		Notes::delete_notes_with_name( 'notchpay-test-mode' );

		return;
	}

	$note = new Note();
	$note->set_title( __( 'Notch Pay test mode enabled', 'woo-notchpay' ) );
	$note->set_content( __( 'Notch Pay test mode is currently enabled. Remember to disable it when you want to start accepting live payment on your site.', 'woo-notchpay' ) );
	$note->set_type( Note::E_WC_ADMIN_NOTE_INFORMATIONAL );
	$note->set_layout( 'plain' );
	$note->set_is_snoozable( false );
	$note->set_name( 'notchpay-test-mode' );
	$note->set_source( 'woo-notchpay' );
	$note->add_action( 'disable-notchpay-test-mode', __( 'Disable Notch Pay test mode', 'woo-notchpay' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=notchpay' ) );
	$note->save();
}

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Registers WooCommerce Blocks integration.
 */
function wc_gateway_notchpay_woocommerce_block_support() {
	if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		require_once __DIR__ . '/includes/class-wc-gateway-notchpay-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			static function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
				$payment_method_registry->register( new WC_Gateway_NotchPay_Blocks_Support() );
			}
		);
	}
}
add_action( 'woocommerce_blocks_loaded', 'wc_gateway_notchpay_woocommerce_block_support' );
