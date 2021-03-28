## Payment Gateway bKash for WC

Contributors: [kapilpaul](https://kapilpaul.me)\
Donate link: https://kapilpaul.me/\
Tags: bkash, mobile-banking, bkash-payment, bkash payment, bkashPayment, bkash payment method, woocommerce bkash, bkash Bangladesh, bkash wordpress, bkash woocommerce\
Requires at least: 4.0\
Tested up to: 5.3.2\
Stable tag: trunk\
Requires PHP: 5.6\
License: GPLv2 or later\
License URI: https://www.gnu.org/licenses/gpl-2.0.html\
You can easily pay via bKash.

### Description

##### bKash PAYMENT METHOD FOR WOOCOMMERCE

Woocommerce payment method for bKash for easy checkout.


##### Installation Guide

1. After activate the plugin you need to go to Woocommerce settings for payments. Here you can see bKash as a payment method. Enable this and open settings for this payment method.
2. Collect your <b><i>USERNAME, PASSWORD, APP_KEY, APP_SECRET</i></b> from bKash. Place this in here and you are able to collect your payment.


##### On Test Mode

You can use the information below for a test checkout. Default values that will pass with each request.
`Wallet : 01770618575`
`OTP : 123456`
`PIN : 12121`

##### Demo Video
[youtube](https://www.youtube.com/watch?v=U83RE3Kfy1A)

##### Frequently Asked Questions

1. Do I need bKash credentials?

    -- Yes. You need bKash USERNAME, PASSWORD, APP_KEY and APP_SECRET.

2. How do I get credentials?

    -- You may contact with bKash support 16247.

3. Is this a plug and play plugin?

    -- Yes. Follow the installation process and you are good to go.
    
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
│   │   └── bkash_logo.png
│   ├── js
│   │   ├── app.css
│   │   ├── app.js
│   │   ├── dc-bkash.js
│   │   ├── runtime.js
│   │   ├── upgrade.css
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
│       │   │   └── settings.js
│       │   ├── components
│       │   │   ├── Header.js
│       │   │   ├── Posts.js
│       │   │   ├── bKash
│       │   │   │   └── api-response.js
│       │   │   └── fields.js
│       │   ├── images
│       │   │   └── duplicate.png
│       │   ├── index.js
│       │   ├── router
│       │   │   └── index.js
│       │   ├── styles
│       │   │   ├── react-toastify.css
│       │   │   └── style.scss
│       │   └── utils
│       │       ├── admin-menu-fix.js
│       │       ├── bkash.js
│       │       └── helper.js
│       └── upgrade
│           ├── App.js
│           ├── Pages
│           │   └── Upgrades.js
│           ├── index.js
│           └── styles
│               └── style.scss
├── composer.json
├── composer.lock
├── includes
│   ├── API
│   │   ├── BkashBaseRestController.php
│   │   ├── Payment.php
│   │   ├── Settings.php
│   │   └── Upgrade.php
│   ├── Abstracts
│   │   └── DcBkashUpgrader.php
│   ├── Admin
│   │   ├── Menu.php
│   │   └── Settings.php
│   ├── Admin.php
│   ├── Ajax.php
│   ├── Api.php
│   ├── Assets.php
│   ├── Frontend
│   │   └── Shortcode.php
│   ├── Frontend.php
│   ├── Gateway
│   │   ├── Bkash.php
│   │   ├── Manager.php
│   │   └── Processor.php
│   ├── Installer.php
│   ├── Traits
│   │   └── Form_Error.php
│   ├── Upgrade
│   │   ├── AdminNotice.php
│   │   ├── Manager.php
│   │   ├── Upgrades
│   │   │   └── V_2_0_0.php
│   │   └── Upgrades.php
│   └── functions.php
├── index.php
├── package-lock.json
├── package.json
├── payment-gateway-bkash-for-wc.php
├── postcss.config.js
├── readme.txt
├── templates
│   ├── admin
│   │   └── upgrade-notice.php
│   └── frontend
│       └── transaction-charge.php
└── webpack.config.js
```


## Privacy Policy
Payment Gateway bKash for WC uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.
    
Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.
    
Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**
    
Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

#### Changelog
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
= 1.3.0 =\
**Important Update** This update added the bKash transaction charge.

= 1.2.0 =\
**Important Update** This update added the appsero tracker to get the analytics and performance of the plugin.

= 1.1.0 =\
**Important Update** This update changes code structures for improving performance. Make sure to take backup and read the changlogs first before upgrade process.

