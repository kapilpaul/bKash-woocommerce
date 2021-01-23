<?php

namespace DCoders\Bkash\Gateway;

/**
 * Class Bkash
 * @since 2.0.0
 *
 * @package DCoders\Bkash\Gateway
 *
 * @author Kapil Paul
 */
class Bkash extends \WC_Payment_Gateway {
	/**
	 * Initialize the gateway
	 * Bkash constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
//		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thank_you_page' ) );
		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
	}

	/**
	 * Init basic settings
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->id                 = 'bkash';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = __( 'bKash', BKASH_TEXT_DOMAIN );
		$this->method_description = __( 'Pay via bKash payment', BKASH_TEXT_DOMAIN );
		$title                    = dc_bkash_get_option( 'title', 'gateway' );
		$this->title              = empty( $title ) ? __( 'bKash', BKASH_TEXT_DOMAIN ) : $title;
		$this->description        = dc_bkash_get_option( 'description', 'gateway' );
	}

	/**
	 * Process admin options
	 *
	 * @since 2.0.0
	 *
	 * @return bool|void
	 */
	public function admin_options() {
		parent::admin_options();

		$bkash_settings_url = admin_url( 'admin.php?page=dc-bkash#/settings' );

		echo "<p>You will get {$this->method_title} setting options in <a href='{$bkash_settings_url}'>here</a>.</p>";
	}

	/**
	 * Process the gateway integration
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		//empty cart
		WC()->cart->empty_cart();

		return [
			'result'             => 'success',
			'order_number'       => $order_id,
			'amount'             => (float) $order->get_total(),
			'checkout_order_pay' => $order->get_checkout_payment_url(),
			'redirect'           => $this->get_return_url( $order ),
		];
	}

	/**
	 * include payment scripts
	 *
	 * @return void
	 */
	public function payment_scripts() {
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
			return;
		}

		// if our payment gateway is disabled
		if ( 'no' === $this->enabled ) {
			return;
		}

		if ( $this->get_option( 'test_mode' ) == 'off' ) {
			$script = "https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js";
		} else {
			$script = "https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js";
		}

		//loading this scripts only in checkout page
		if ( is_checkout() || is_checkout_pay_page() ) {
			wp_dequeue_script( 'jquery' );
			wp_enqueue_script( 'bkash_jquery', 'https://code.jquery.com/jquery-3.3.1.min.js', [], '3.3.1', false );
			wp_enqueue_script( 'bkash_checkout', $script, [], '1.2.0', true );
		}

		wp_register_script( 'wcb-checkout', plugins_url( 'js/bkash.js', dirname( __FILE__ ) ), [
			'jquery',
			'woocommerce',
			'wc-country-select',
			'wc-address-i18n',
		], '3.9.1', true );
		wp_enqueue_script( 'wcb-checkout' );

		wp_enqueue_style( 'bkash_woocommerce_css', plugins_url( 'css/bkash-woocommerce.css', dirname( __FILE__ ) ), [], '1.0.0', false );

		$this->localizeScripts();
	}
}
