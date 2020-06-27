=== Payment Gateway bKash for WC ===
Contributors: kapilpaul
Donate link: https://kapilpaul.me/
Tags: bkash, woocommerce pgw, bdt, mobilebanking, bangladesh
Requires at least: 4.0
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

You can easily pay via bKash.

== Description ==

= bKash PAYMENT METHOD FOR WOOCOMMERCE =

Woocommerce payment method for bKash for easy checkout.

= Installation Guide =

1. After activate the plugin you need to go to Woocommerce settings for payments. Here you can see bKash as a payment method. Enable this and open settings for this payment method.
2. Collect your USERNAME, PASSWORD, APP_KEY, APP_SECRET from bKash. Place this in here and you are able to collect your payment.

** NB: Please note that this plugin requires jQuery 3.3.1 . Otherwise bKash script does not work with WordPress default jQuery. So called the jQuery 3.3.1 in checkout only. **

= Demo Video =

[youtube https://www.youtube.com/watch?v=U83RE3Kfy1A]

= Privacy Policy =
Payment Gateway bKash for WC uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

= Contributing and Reporting Bugs =
Payment Gateway bKash for WC is being developed on GitHub. If you’re interested in contributing to the plugin, please look at [Github page](https://github.com/kapilpaul/bKash-woocommerce).

== Installation ==

1. After activate the plugin you need to go to Woocommerce settings for payments. Here you can see bKash as a payment method. Enable this and open settings for this payment method.
2. Collect your USERNAME, PASSWORD, APP_KEY, APP_SECRET from bKash. Place this in here and you are able to collect your payment.

** NB: Please note that this plugin requires jQuery 3.3.1 . Otherwise bKash script does not work with WordPress default jQuery. So called the jQuery 3.3.1 in checkout only. **

== Frequently Asked Questions ==

= Do I need bKash credentials? =

Yes. You need bKash USERNAME, PASSWORD, APP_KEY and APP_SECRET.

= How do I get credentials? =

You may contact with bKash support 16247.

= Is this a plug and play plugin? =

Yes. Follow the installation process and you are good to go.


== Screenshots ==

1. Admin panel payment methods list
2. Admin panel bKash payment method settings
3. Order details data
4. bKash as a payment method
5. bkash main payment
6. bkash Payment List

== Changelog ==
= v1.3.0 (June 28, 2020) =
* Added: bKash transaction charge option.
* Updated: thank you page bug.

= v1.2.1 (June 27, 2020) =
* Fixed missing file bug.

= v1.2.0 (June 25, 2020) =
* Added: Bulk delete, single delete in payment list
* Added: appsero tracker for plugin analytics

= v1.1.1 (April 25, 2020) =
* Added: create payment through plugin
* Added: execute payment through plugin

= v1.1.0 (April 13, 2020) =
* Added: Payments list view
* Added: Search option in view
* Added: Some constants

= v1.0.0 (Feburuary 21, 2020) =
* bkash payment method for woocommerce
* Testing environment
* Payment for orders

== Upgrade Notice ==
= 1.3.0 =
**Important Update** This update added the bKash transaction charge.

= 1.2.0 =
**Important Update** This update added the appsero tracker to get the analytics and performance of the plugin.

= 1.1.0 =
**Important Update** This update changes code structures for improving performance. Make sure to take backup and read the changlogs first before upgrade process.
