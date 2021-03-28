<?php

namespace DCoders\Bkash;

/**
 * Class Ajax
 * @since 2.0.0
 *
 * @package DCoders\Bkash
 *
 * @author Kapil Paul
 */
class Ajax {
	/**
	 * Ajax constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_dc-bkash-execute-payment-request', [ $this, 'execute_payment_request' ] );
	}

	/**
	 * Execute payment request on bKash
	 *
	 * @return void
	 */
	public function execute_payment_request() {
		try {
			if ( ! wp_verify_nonce( $_POST['_nonce'], 'dc-bkash-nonce' ) ) {
				$this->send_json_error( __( 'Something went wrong here!', 'dc-bkash' ) );
			}

			if ( ! $this->validate_fields( $_POST ) ) {
				$this->send_json_error( __( 'Empty value is not allowed', 'dc-bkash' ) );
			}

			$payment_id   = ( isset( $_POST['payment_id'] ) ) ? sanitize_text_field( $_POST['payment_id'] ) : '';
			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_text_field( $_POST['order_number'] ) : '';

			$order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( __( 'Wrong or invalid order ID', 'dc-bkash' ) );
			}

			$processor       = dc_bkash()->gateway->processor();
			$execute_payment = $processor->execute_payment( $payment_id );

			if ( is_wp_error( $execute_payment ) ) {
				$this->send_json_error( $execute_payment->get_error_message() );
			}

			if ( $execute_payment ) {
				do_action( 'dc_bkash_execute_payment_success', $order, $execute_payment );

				$execute_payment['order_success_url'] = $order->get_checkout_order_received_url();
				wp_send_json_success( $execute_payment );
			}

			$this->send_json_error( __( 'Something went wrong!', 'dc-bkash' ) );

		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Send json error
	 *
	 * @param $text
	 *
	 * @return void
	 */
	public function send_json_error( $text ) {
		wp_send_json_error( __( $text, 'dc-bkash' ) );
		wp_die();
	}

	/**
	 * Validate fields
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function validate_fields( $data ) {
		foreach ( $data as $key => $value ) {
			if ( empty( $value ) ) {
				return false;
			}
		}

		return true;
	}
}
