<?php
/**
 * API Class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

use DCoders\Bkash\API\Payment;
use DCoders\Bkash\API\Settings;
use DCoders\Bkash\API\Transaction;
use DCoders\Bkash\API\Upgrade;

/**
 * Class API
 */
class API {

	/**
	 * Holds the api classes.
	 *
	 * @var array
	 */
	private $classes;

	/**
	 * Initialize the class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->classes = [
			Settings::class,
			Upgrade::class,
			Payment::class,
			Transaction::class,
		];

		add_action( 'rest_api_init', [ $this, 'register_api' ] );
		add_action( 'woocommerce_api_verify-bkash-payment', [ $this, 'verify_bkash_payment' ] );
	}

	/**
	 * Verify bKash payment.
	 *
	 * @return void
	 */
	public function verify_bkash_payment() {

		$data = wp_unslash( $_GET );

		$order = wc_get_order( $data['order_id'] );

		// if it is not a valid order.
		if ( ! $order instanceof \WC_Order ) {
			wp_safe_redirect( site_url() );
			exit;
		}

		// if the nonce is not valid.
		if ( ! wp_verify_nonce( $data['nonce'], 'verify-bkash-payment' ) ) {
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		}

		// if the status is not success.
		if ( 'success' !== $data['status'] ) {
			wp_safe_redirect( $order->get_checkout_payment_url() );
			exit;
		}

		$processor       = dc_bkash()->gateway->processor();
		$execute_payment = $processor->execute_payment( $data['paymentID'] );

		if ( is_wp_error( $execute_payment ) ) {
			wp_safe_redirect( $order->get_checkout_payment_url() );
			exit;
		}

		if ( $execute_payment ) {
			do_action( 'dc_bkash_execute_payment_success', $order, $execute_payment );
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		}

		exit;
	}

	/**
	 * Register the API
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_api() {
		foreach ( $this->classes as $class ) {
			$object = new $class();
			$object->register_routes();
		}
	}
}
