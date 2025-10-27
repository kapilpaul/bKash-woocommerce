## Payment Gateway bKash for WC

Contributors: [kapilpaul](https://kapilpaul.me)\
Donate link: https://kapilpaul.me/ \
Tags: bkash, woocommerce payment, woocommerce gateway, woocommerce pgw, mobile banking, bangladesh, bdt, bkash payment, bkash checkout, bkash gateway, digital payment, ecommerce bangladesh, online payment, payment integration, woocommerc
\
Requires at least: 4.0\
Tested up to: 6.8.3\
Stable tag: trunk\
Requires PHP: 5.6\
License: GPLv2 or later\
License URI: https://www.gnu.org/licenses/gpl-2.0.html

### You can easily pay via bKash.

### Description

##### bKash PAYMENT METHOD FOR WOOCOMMERCE

bKash Payment Method for WooCommerce enables seamless online payments for Bangladeshi merchants using the most popular mobile financial service bKash.
This plugin allows your customers to pay securely and conveniently through bKash during checkout, providing a fast and trusted payment experience right from your WooCommerce store.

With an easy setup process and full integration into WooCommerce’s checkout flow, this plugin is designed to make transactions smooth for both merchants and customers.

##### ⭐ Key Features

🔐 Secure and reliable bKash payment integration\
⚙️ Easy setup and configuration in WooCommerce settings\
💸 Real-time transaction validation with the bKash API\
🧾 Support for Refunds and Transaction Lookup\
🎨 Fully compatible with the WooCommerce checkout page and themes\
📱 Optimized for both desktop and mobile users

##### Installation Guide

1. After activate the plugin you need to go to Woocommerce settings for payments. Here you can see bKash as a payment method. Enable this and open settings for this payment method.
2. Here you will see a link to go to the settings. (Or you may go to the bKash Settings from left menu).
3. Collect your <b><i>USERNAME, PASSWORD, APP_KEY, APP_SECRET</i></b> from bKash. Place this in here and you are able to collect your payment.


### Test Mode

In test mode, there are two options. One is with Test Credentials and another is without credentials.
You may play with this plugin without giving any credentials.
But when you need to generate document for bKash, you must need to fill up the necessary information.

You can use the below information for a test transaction.

<pre>
bKash Wallet : 01770618575
bKash OTP    : 123456
bKash PIN    : 12121
</pre>

### Live Credentials

Please provide necessary information here to receive live credentials: [bKash](https://pgw-integration.bkash.com/#/sign-up)

##### Demo Video
[youtube](https://www.youtube.com/watch?v=U83RE3Kfy1A)

##### Frequently Asked Questions

1. Do I need bKash credentials?

    -- Yes. You need bKash USERNAME, PASSWORD, APP_KEY and APP_SECRET.

2. How do I get credentials?

    -- You may contact with bKash support 16247.

3. How do I get bKash dov for submission =

   -- In this plugin, you can generate the doc.

4. Is this a plug and play plugin?

   -- Yes. Follow the installation process and you are good to go.

5. Can I generate API doc in this plugin? =

   -- Yes. You can generate and download the API doc inside the admin panel.

6. Can I refund? =

   -- Yes uou can refund from this plugin. Both automatic and manual payments are available.

## File Structure

```
├── README.md
├── assets
│   ├── css
│   │   ├── app.css
│   │   ├── dc-bkash.css
│   │   └── upgrade.css
│   ├── images
│   │   ├── bkash.png
│   │   ├── bkash_logo.png
│   │   ├── checked.png
│   │   └── wpspin.gif
│   ├── js
│   │   ├── app.js
│   │   ├── dc-bkash.js
│   │   ├── runtime.js
│   │   ├── upgrade.js
│   │   ├── vendors.js
│   │   └── vendors.js.LICENSE.txt
│   └── src
│       ├── admin
│       │   ├── App.js
│       │   ├── Pages
│       │   │   ├── Doc
│       │   │   │   └── doc-container.js
│       │   │   ├── generatedoc.js
│       │   │   ├── refund-container.js
│       │   │   ├── search-transaction.js
│       │   │   ├── settings.js
│       │   │   └── transactions.js
│       │   ├── components
│       │   │   ├── Header.js
│       │   │   ├── bKash
│       │   │   │   ├── api-response.js
│       │   │   │   └── refund.js
│       │   │   ├── fields.js
│       │   │   └── loader.js
│       │   ├── images
│       │   │   ├── duplicate.png
│       │   │   └── exceed-pin.png
│       │   ├── index.js
│       │   ├── router
│       │   │   └── index.js
│       │   ├── styles
│       │   │   ├── _colors.scss
│       │   │   ├── _common.scss
│       │   │   ├── _print.scss
│       │   │   ├── generate-doc.scss
│       │   │   ├── header.scss
│       │   │   ├── react-toastify.scss
│       │   │   ├── refund.scss
│       │   │   ├── settings.scss
│       │   │   ├── style.scss
│       │   │   └── transactions.scss
│       │   └── utils
│       │       ├── admin-menu-fix.js
│       │       ├── bkash.js
│       │       └── helper.js
│       ├── constants.js
│       └── upgrade
│           ├── App.js
│           ├── Pages
│           │   └── Upgrades.js
│           ├── index.js
│           └── styles
│               └── style.scss
├── build
│   └── index.js
├── composer.json
├── composer.lock
├── includes
│   ├── API
│   │   ├── BkashBaseRestController.php
│   │   ├── Payment.php
│   │   ├── Settings.php
│   │   ├── Transaction.php
│   │   └── Upgrade.php
│   ├── API.php
│   ├── Abstracts
│   │   └── DcBkashUpgrader.php
│   ├── Admin
│   │   ├── Menu.php
│   │   └── Settings.php
│   ├── Admin.php
│   ├── Ajax.php
│   ├── Assets.php
│   ├── Frontend
│   │   └── Shortcode.php
│   ├── Frontend.php
│   ├── Gateway
│   │   ├── Bkash.php
│   │   ├── Manager.php
│   │   └── Processor.php
│   ├── Installer.php
│   ├── Upgrade
│   │   ├── AdminNotice.php
│   │   ├── Manager.php
│   │   ├── Upgrades
│   │   │   ├── V_2_0_0.php
│   │   │   └── V_2_1_0.php
│   │   └── Upgrades.php
│   └── functions.php
├── index.php
├── languages
│   └── dc-bkash.pot
├── package-lock.json
├── package.json
├── phpcs.xml
├── postcss.config.js
├── readme.txt
├── templates
│   ├── admin
│   │   ├── transaction-charge.php
│   │   └── upgrade-notice.php
│   └── frontend
│       ├── payment-details.php
│       └── transaction-charge.php
├── vendor
├── webpack.config.js
└── woo-payment-bkash.php
```


## Privacy Policy
Payment Gateway bKash for WC uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

#### Changelog

= v3.0.0 (April 10, 2023) =
* Add: Checkout URL
* Refund ID in table
* Support parent order note
* other cosmetic changes

= v2.1.0 (February 28, 2022) =
* Add: Refund Transaction
* Add: Manual and automatic refund
* Add: Search Transaction
* Add: Refund API document generation
* fix: Order Pay Page bKash Payment
* fix: Document generation process

= v2.0.0 (April 26, 2021) =
* New plugin structure with React JS.
* New options panel with more options.
* Transactions list and pagination without loading.
* Verify transaction option.
* Most Important: API doc generation and download automatically.
* Actions and filters introduced.
* Rest api added.
* More functionalities added to simply the code.
* Display transaction charge in admin order page.
* Display transaction charge in checkout page.
* Display transaction charge in order review.
* jQuery removed from checkout page and added automatically from JS.
* Upgrader option for auto update date from old version to new version.

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

#### Upgrade Notice
= 2.1.0 =\
**Important Update** This update will alter some tables on database. Make sure to take backup and read the change logs first before upgrade process. Do not forget to upgrade the data to new version.

= 2.0.0 =\
**Important Update** This update changes all the code structures for improving performance. Make sure to take backup and read the change logs first before upgrade process. Do not forget to upgrade the data to new version.

= 1.3.0 =\
**Important Update** This update added the bKash transaction charge.

= 1.2.0 =\
**Important Update** This update added the appsero tracker to get the analytics and performance of the plugin.

= 1.1.0 =\
**Important Update** This update changes code structures for improving performance. Make sure to take backup and read the changlogs first before upgrade process.

