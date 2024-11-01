=== Notch Pay for WooCommerce ===
Contributors: notchpay,chapdel
Tags: notch pay, notchpay, mobile money, orange money, woocommerce, payment gateway, cameroon, cameroun, xaf, fcfa, mastercard, visa, paypal
Requires at least: 5.8
Tested up to: 6.4
Stable tag: 2.1.5
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Notch Pay provides merchants with the tools and services to accept online payments from local and international customers using Mobile Money and cards.

== Description ==

Notch Pay makes it easy for businesses in Cameroon, Nigeria, Ghana, Kenya and South Africa to accept secure payments from multiple local and global payment channels. Integrate Notch Pay with your store today, and let your customers pay you with their choice of methods.

With Notch Pay for WooCommerce, you can accept payments via:

* Credit/Debit Cards — Visa, Mastercard
* PayPal
* Mobile money (Cameroon)
* Many more coming soon

= Why Notch Pay? =

* Start receiving payments instantly—go from sign-up to your first real transaction in as little as 15 minutes
* Simple, transparent pricing—no hidden charges or fees
* Setup fees
* Modern, seamless payment experience with Notch Pay
* Understand your customers better through a simple and elegant dashboard
* Access to attentive, empathetic customer support 24/7
* Clearly documented APIs to build your custom payment experiences


== Installation ==

*   Go to __WordPress Admin__ > __Plugins__ > __Add New__ from the left-hand menu
*   In the search box type __Notch Pay for WooCommerce__
*   Click on Install now when you see __Notch Pay for WooCommerce__ to install the plugin
*   After installation, __activate__ the plugin.


= Notch Pay Setup and Configuration =
*   Go to __WooCommerce > Settings__ and click on the __Payments__ tab
*   You'll see Notch Pay listed along with your other payment methods. Click __Set Up__
*   On the next screen, configure the plugin. There is a selection of options on the screen. Read what each one does below.

1. __Enable/Disable__ - Check this checkbox to Enable Notch Pay on your store's checkout
2. __Title__ - This will represent Notch Pay on your list of Payment options during checkout. It guides users to know which option to select to pay with Notch Pay. __Title__ is set to "Debit/Credit Cards" by default, but you can change it to suit your needs.
3. __Description__ - This controls the message that appears under the payment fields on the checkout page. Use this space to give more details to customers about what Notch Pay is and what payment methods they can use with it.
4. __Test Mode__ - Check this to enable test mode. When selected, the fields in step six will say "Test" instead of "Live." Test mode enables you to test payments before going live. The orders process with test payment methods, no money is involved so there is no risk. You can uncheck this when your store is ready to accept real payments.
5. __Payment Option__ - Select how Notch Pay Checkout displays to your customers. A popup displays Notch Pay Checkout on the same page, while Redirect will redirect your customer to make payment.
6. __API Keys__ - The next two text boxes are for your Notch Pay API keys, which you can get from your Notch Pay Dashboard. If you enabled Test Mode in step four, then you'll need to use your test API keys here. Otherwise, you can enter your live keys.
7. __Additional Settings__ - While not necessary for the plugin to function, there are some extra configuration options you have here. You can do things like add custom metadata to your transactions (the data will show up on your Notch Pay dashboard) or use Notch Pay's [Split Payment feature](https://notchpay.com/docs/payments/split-payments). The tooltips next to the options provide more information on what they do.
8. Click on __Save Changes__ to update the settings.

To account for poor network connections, which can sometimes affect order status updates after a transaction, we __strongly__ recommend that you set a Webhook URL on your Notch Pay dashboard. This way, whenever a transaction is complete on your store, we'll send a notification to the Webhook URL, which will update the order and mark it as paid. You can set this up by using the URL in red at the top of the Settings page. Just copy the URL and save it as your webhook URL on your Notch Pay dashboard under __Settings > API Keys & Webhooks__ tab.

If you do not find Notch Pay on the Payment method options, please go through the settings again and ensure that:

*   You've checked the __"Enable/Disable"__ checkbox
*   You've entered your __API Keys__ in the appropriate field
*   You've clicked on __Save Changes__ during setup

== Frequently Asked Questions ==

= What Do I Need To Use The Plugin =

*   A Notch Pay Business account—use an existing account or [create an account here](https://business.notchpay.com/signup)
*   An active [WooCommerce installation](https://docs.woocommerce.com/document/installing-uninstalling-woocommerce/)

== Changelog ==
= 2.1.1 - May 17, 2024 =
*   New: Add locked currency
= 2.1.0 - February 11, 2024 =
*   New: Add support for WooCommerce checkout block
*   New: Minimum WooCommerce supported version: 7.0
*   Improve: Notch Pay test mode notice will now be displayed in the WooCommerce Admin Notes Inbox
*   Tweak: Declare compatibility for High Performance Order Storage (HPOS)
*   Tweak: Minimum PHP version: 7.4
*   Tweak: WooCommerce 8.3 compatibility
*   Improve: Improvement to webhook notifications and order processing
= 2.0.4 - January 2023
*   Fixed cancel callback
*   Added New logo
= 1.1.0 -  October 22, 2022 =
*   Changed Plugin Slug
= 1.0.0 -  October 13, 2022 =
*   First release