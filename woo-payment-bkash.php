<?php
/**
 * Plugin Name: Payment Gateway bKash for WC
 * Plugin URI: https://wordpress.org/plugins/woo-payment-bkash/
 * Description: An eCommerce payment method that helps you sell anything. Beautifully.
 * Version: 2.1.0
 * Author: Kapil Paul
 * Author URI: https://kapilpaul.me
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dc-bkash
 * Domain Path: /languages
 *
 * @package Dc-Bkash
 */

/**
 * Copyright (c) 2021 Kapil Paul (email: kapilpaul007@gmail.com). All rights reserved.
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

// don't call the file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * DCoders_Bkash class
 *
 * @class DCoders_Bkash The class that holds the entire DCoders_Bkash plugin
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
final class DCoders_Bkash {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '2.1.0';

	/**
	 * Holds various class instances.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	private $container = [];

	/**
	 * Constructor for the DCoders_Bkash class
	 *
	 * Sets up all the appropriate hooks and actions within our plugin.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function __construct() {
		$this->define_constants();

		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

		$this->init_appsero_tracker();

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
	}

	/**
	 * Initializes the DCoders_Bkash() class
	 *
	 * Checks for an existing DCoders_Bkash() instance and if it doesn't find one, creates it.
	 *
	 * @since 2.0.0
	 *
	 * @return DCoders_Bkash|bool
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new DCoders_Bkash();
		}

		return $instance;
	}

	/**
	 * Magic getter to bypass referencing plugin.
	 *
	 * @param mixed $prop Properties to find.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
			return $this->container[ $prop ];
		}

		return $this->{$prop};
	}

	/**
	 * Magic isset to bypass referencing plugin.
	 *
	 * @param mixed $prop Properties to find.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function __isset( $prop ) {
		return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
	}

	/**
	 * Define the constants
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'BKASH_VERSION', self::VERSION );
		define( 'BKASH_FILE', __FILE__ );
		define( 'BKASH_PATH', dirname( BKASH_FILE ) );
		define( 'BKASH_INCLUDES', BKASH_PATH . '/includes' );
		define( 'BKASH_TEMPLATE_PATH', BKASH_PATH . '/templates/' );
		define( 'BKASH_URL', plugins_url( '', BKASH_FILE ) );
		define( 'BKASH_ASSETS', BKASH_URL . '/assets' );
		define( 'BKASH_TEXT_DOMAIN', 'dc-bkash' );
	}

	/**
	 * Load the plugin after all plugins are loaded
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init_plugin() {
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Placeholder for activation function
	 *
	 * Nothing being called here yet.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function activate() {
		$installer = new DCoders\Bkash\Installer();
		$installer->run();
	}

	/**
	 * Placeholder for deactivation function
	 *
	 * Nothing being called here yet.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function deactivate() {

	}

	/**
	 * Include the required files
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function includes() {
		if ( $this->is_request( 'admin' ) ) {
			$this->container['admin'] = new DCoders\Bkash\Admin();
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->container['frontend'] = new DCoders\Bkash\Frontend();
		}
	}

	/**
	 * Initialize the hooks
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'init_classes' ] );

		// Localize our plugin.
		add_action( 'init', [ $this, 'localization_setup' ] );
	}

	/**
	 * Instantiate the required classes
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init_classes() {
		if ( $this->is_request( 'ajax' ) ) {
			$this->container['ajax'] = new DCoders\Bkash\Ajax();
		}

		$this->container['api']      = new DCoders\Bkash\API();
		$this->container['assets']   = new DCoders\Bkash\Assets();
		$this->container['settings'] = new DCoders\Bkash\Admin\Settings();
		$this->container['upgrades'] = new DCoders\Bkash\Upgrade\Manager();
		$this->container['gateway']  = new DCoders\Bkash\Gateway\Manager();

		$this->container = apply_filters( 'dc_bkash_get_class_container', $this->container );
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @uses load_plugin_textdomain()
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'dc-bkash', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();

			case 'ajax':
				return defined( 'DOING_AJAX' );

			case 'rest':
				return defined( 'REST_REQUEST' );

			case 'cron':
				return defined( 'DOING_CRON' );

			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Check woocommerce is exists or not
	 *
	 * @since 2.0.03
	 *
	 * @return bool
	 */
	public function has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Initialize Appsero Tracker
	 *
	 * @since 1.2.0
	 *
	 * @return  void
	 */
	public function init_appsero_tracker() {
		if ( ! class_exists( 'Appsero\Client' ) ) {
			require_once __DIR__ . '/appsero/src/Client.php';
		}

		$client = new Appsero\Client( 'f5998f6a-c466-4c4e-8627-0188f177e7f5', 'Payment Gateway bKash for WC', __FILE__ );

		// Active insights.
		$client->insights()->init();
	}

	/**
	 * Get DB version key
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_db_version_key() {
		// old version key.
		if ( get_option( 'wcwpbkash_version', null ) ) {
			return 'wcwpbkash_version';
		}

		return 'dc_bkash_version';
	}

} // DCoders_Bkash

/**
 * Initialize the main plugin
 *
 * @since 2.0.0
 *
 * @return \DCoders_Bkash|bool
 */
function dc_bkash() {
	return DCoders_Bkash::init();
}

/**
 * Kick-off the plugin.
 */
dc_bkash();
