<?php

namespace Inc;

use Inc\Admin\Payments;
use Inc\Base\BkashQuery;
use WC_AJAX;
use WC_Payment_Gateway;

class WC_PGW_BKASH extends WC_Payment_Gateway {

	/**
	 * Initialize the gateway
	 * WC_PGW_BKASH constructor.
	 */
	public function __construct() {
		$this->id                 = 'bkash';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = __( 'bKash', 'bkash-wc' );
		$this->method_description = __( 'Pay via bKash payment', 'bkash-wc' );
		$title                    = $this->get_option( 'title' );
		$this->title              = empty( $title ) ? __( 'bKash', 'bkash-wc' ) : $title;
		$this->description        = $this->get_option( 'description' );

		$this->init_form_fields();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thank_you_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
	}

	/**
	 * Admin configuration parameters
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = [
			'enabled'            => [
				'title'   => __( 'Enable/Disable', 'bkash-wc' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable bKash', 'bkash-wc' ),
				'default' => 'yes',
			],
			'test_mode'          => [
				'title'   => __( 'Test Mode', 'bkash-wc' ),
				'type'    => 'select',
				'options' => [ "on" => "ON", "off" => "OFF" ],
				'default' => __( 'off', 'bkash-wc' ),
			],
			'title'              => [
				'title'   => __( 'Title', 'bkash-wc' ),
				'type'    => 'text',
				'default' => __( 'bKash Payment', 'bkash-wc' ),
			],
			'username'           => [
				'title' => __( 'User Name', 'bkash-wc' ),
				'type'  => 'text',
			],
			'password'           => [
				'title' => __( 'Password', 'bkash-wc' ),
				'type'  => 'password',
			],
			'app_key'            => [
				'title' => __( 'App Key', 'bkash-wc' ),
				'type'  => 'text',
			],
			'app_secret'         => [
				'title' => __( 'App Secret', 'bkash-wc' ),
				'type'  => 'text',
			],
			'transaction_charge' => [
				'title'   => __( 'Enable bKash Charge', 'bkash-wc' ),
				'type'    => 'checkbox',
				'label'   => __( '&nbsp;', 'bkash-wc' ),
				'default' => 'no',
			],
			'charge_type'        => [
				'title'   => __( 'Charge Type', 'bkash-wc' ),
				'type'    => 'select',
				'options' => [ "fixed" => "Fixed", "percentage" => "Percentage" ],
				'default' => 'percentage',
				'description' => __( 'This option will only work when the bKash Charge is enabled', 'bkash-wc' ),
			],
			'charge_amount'      => [
				'title'   => __( 'Charge Amount', 'bkash-wc' ),
				'type'    => 'text',
				'default' => 2,
				'description' => __( 'This option will only work when the bKash Charge is enabled', 'bkash-wc' ),
			],
			'description'        => [
				'title'       => __( 'Description', 'bkash-wc' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'bkash-wc' ),
				'default'     => __( 'Pay via bKash', 'bkash-wc' ),
				'desc_tip'    => true,
			],
		];
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
		// Remove cart
		WC()->cart->empty_cart();

		return array(
			'result'             => 'success',
			'order_number'       => $order_id,
			'amount'             => (float) $order->get_total(),
			'checkout_order_pay' => $order->get_checkout_payment_url(),
			'redirect'           => $this->get_return_url( $order ),
		);
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
		wp_dequeue_script( 'wc-checkout' );

		if ( $this->get_option( 'test_mode' ) == 'off' ) {
			$script = "https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js";
		} else {
			$script = "https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js";
		}

		//loading this scripts only in checkout page
		if ( is_checkout() || is_checkout_pay_page() ) {
			wp_dequeue_script( 'jquery' );
			wp_enqueue_script( 'bkash_jquery', 'https://code.jquery.com/jquery-3.3.1.min.js', array(), '3.3.1', false );
			wp_enqueue_script( 'bkash_checkout', $script, array(), '1.2.0', true );
		}

		wp_register_script( 'wcb-checkout', plugins_url( 'js/bkash.js', dirname( __FILE__ ) ), array(
			'jquery',
			'woocommerce',
			'wc-country-select',
			'wc-address-i18n',
		), '3.9.1', true );
		wp_enqueue_script( 'wcb-checkout' );

		wp_enqueue_style( 'bkash_woocommerce_css', plugins_url( 'css/bkash-woocommerce.css', dirname( __FILE__ ) ), array(), '1.0.0', false );

		$this->localizeScripts();
	}

	/**
	 * localize scripts and pass data
	 *
	 * @return void
	 */
	public function localizeScripts() {
		global $woocommerce;
		global $wp;

		$data = array(
			'amount' => $woocommerce->cart->cart_contents_total,
			'nonce'  => wp_create_nonce( 'wc-bkash-nonce' ),
		);

		$params = array(
			'ajax_url'                  => WC()->ajax_url(),
			'wc_ajax_url'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
			'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
			'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
			'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
			'checkout_url'              => WC_AJAX::get_endpoint( 'checkout' ),
			'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
			'debug_mode'                => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'i18n_checkout_error'       => esc_attr__( 'Error processing checkout. Please try again.', 'woocommerce' ),
		);

		wp_localize_script( 'wcb-checkout', 'wc_checkout_params', $params );
		wp_localize_script( 'bkash_checkout', 'bkash_params', $data );
	}

	/**
	 * Thank you page after order
	 *
	 * @param $order_id
	 *
	 * @return void
	 */
	public function thank_you_page( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( 'bkash' === $order->get_payment_method() ) {
			$payment_data = get_bkash_payment( $order_id );

			if ( $payment_data ) {
				$trx_id = $payment_data->trx_id;
				$status = $payment_data->transaction_status;
			}
			$status = $order->needs_payment() ? 'NOT PAID' : 'Completed';

			?>
            <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
				<?php if ( isset( $trx_id ) ) { ?>
                    <li class="woocommerce-order-overview__payment-method method">
                        bKash Transaction ID: <strong><?php echo $trx_id; ?></strong>
                    </li>
				<?php } ?>
                <li class="woocommerce-order-overview__payment-method method">
                    Payment Status:
                    <strong><?php echo strtoupper( $status ); ?></strong>
                </li>
            </ul>
			<?php
		}

	}
}
