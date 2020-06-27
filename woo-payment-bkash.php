<?php
/**
 * Plugin Name: Payment Gateway bKash for WC
 * Plugin URI: https://kapilpaul.me/
 * Description: An eCommerce payment method that helps you sell anything. Beautifully.
 * Version: 1.3.0
 * Author: Kapil Paul
 * Author URI: https://kapilpaul.me
 * Text Domain: bkash-wc
 * License: GPLv2 or later
 *
 * @package bKash-woocommerce
 */

/**
 * Copyright (c) 2020 Kapil Paul (email: kapilpaul007@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

use Inc\Base\BkashWoocommerceActivator;
use Inc\Base\BkashWoocommerceDeactivator;
use Inc\WC_PGW_BKASH;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

defined( 'ABSPATH' ) || exit;

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

/**
 * Class WC_WP_bKash
 */
final class WC_WP_bKash {
	/**
	 * Plugin version
	 *
	 * @var string
	 */
	const version = '1.3.0';

	/**
	 * WC_WP_bKash constructor.
	 */
	public function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'active' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		$this->appsero_init_tracker_woo_payment_bkash();

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
	}

	/**
	 * Initializes the WC_WP_bKash() class
	 *
	 * Checks for an existing WC_WP_bKash() instance
	 * and if it doesn't find one, creates it.
	 *
	 * @return DCoders_Nagad|bool
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new WC_WP_bKash();
		}

		return $instance;
	}

	/**
	 * necessary activations when
	 * activate plugin
	 *
	 * @return void
	 */
	public function active() {
		BkashWoocommerceActivator::do_install();
	}

	/**
	 * deactivation on plugin deactivate
	 *
	 * @return void
	 */
	public function deactivate() {
		BkashWoocommerceDeactivator::deactivate();
	}

	/**
	 * initialize woocommerce payment gateway
	 *
	 * @return void
	 */
	public function init_plugin() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		$this->includes();
	}

	/**
	 * Include the required files
	 *
	 * @return void
	 */
	public function includes() {
		if ( is_admin() ) {
			new \Inc\Admin();
		}

		new \Inc\Bkash();
	}

	/**
	 * Define the constants
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'WC_WP_BKASH_VERSION', self::version );
		define( 'WC_WP_BKASH_FILE', __FILE__ );
		define( 'WC_WP_BKASH_PATH', dirname( WC_WP_BKASH_FILE ) );
		define( 'WC_WP_BKASH_INCLUDES', WC_WP_BKASH_PATH . '/includes' );
		define( 'WC_WP_BKASH_URL', plugins_url( '', WC_WP_BKASH_FILE ) );
	}

	/**
	 * Register WooCommerce Payment Gateway
	 *
	 * @param array $gateways
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = new WC_PGW_BKASH();

		return $gateways;
	}

	/**
	 * Initialize Appsero Tracker
	 *
	 * @return  void
	 */
	public function appsero_init_tracker_woo_payment_bkash() {
		if ( ! class_exists( 'Appsero\Client' ) ) {
			require_once __DIR__ . '/appsero/src/Client.php';
		}

		$client = new Appsero\Client( 'f5998f6a-c466-4c4e-8627-0188f177e7f5', 'Payment Gateway bKash for WC', __FILE__ );

		// Active insights
		$client->insights()->init();
	}
}

/**
 * initialize bkash class
 * @return \WC_WP_bKash|bool
 */
function init_wc_bkash() {
	return WC_WP_bKash::init();
}

//kick start the plugin
init_wc_bkash();
