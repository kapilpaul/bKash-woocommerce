<?php
/**
 * Plugin Name: Payment Gateway bKash for WC
 * Plugin URI: https://kapilpaul.me/
 * Description: An eCommerce payment method that helps you sell anything. Beautifully.
 * Version: 1.1.3
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
	const version = '1.0.0';

	/**
	 * WC_WP_bKash constructor.
	 */
	public function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, array( $this, 'active' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
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
	public function init() {
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
}


/**
 * initialize bkash class
 * @return void
 */
function init_wc_bkash() {
	new WC_WP_bKash();
}

//kick start the plugin
init_wc_bkash();

