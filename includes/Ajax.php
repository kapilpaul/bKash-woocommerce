<?php
/**
 * Class Ajax
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

/**
 * Class Ajax
 */
class Ajax {
	/**
	 * Ajax constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_dc-bkash-execute-payment-request', [ $this, 'execute_payment_request' ] );
		add_action( 'wp_ajax_dc-bkash-order-pay', [ $this, 'process_order_pay' ] );
	}

	/**
	 * Execute payment request on bKash
	 *
	 * @return void
	 */
	public function execute_payment_request() {
		try {
			$post_data = wp_unslash( $_POST );

			if ( ! wp_verify_nonce( $post_data['_nonce'], 'dc-bkash-nonce' ) ) {
				$this->send_json_error( __( 'Something went wrong here!', 'dc-bkash' ) );
			}

			if ( ! $this->validate_fields( $post_data ) ) {
				$this->send_json_error( __( 'Empty value is not allowed', 'dc-bkash' ) );
			}

			$payment_id   = ( isset( $post_data['payment_id'] ) ) ? sanitize_text_field( $post_data['payment_id'] ) : '';
			$order_number = ( isset( $post_data['order_number'] ) ) ? sanitize_text_field( $post_data['order_number'] ) : '';

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
	 * Send json error.
	 *
	 * @param string $text Text to send as message.
	 *
	 * @return void
	 */
	public function send_json_error( $text ) {
		wp_send_json_error( $text );
		wp_die();
	}

	/**
	 * Validate fields.
	 *
	 * @param array $data Data for validation.
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

	/**
	 * Process order pay.
	 *
	 * @since 2.1.0
	 *
	 * @return void
	 */
	public function process_order_pay() {
		try {
			$post_data = wp_unslash( $_POST );

			if ( ! wp_verify_nonce( $post_data['woocommerce-pay-nonce'], 'woocommerce-pay' ) ) {
				$this->send_json_error( __( 'Something went wrong here!', 'dc-bkash' ) );
			}

			if ( ! $this->validate_fields( $post_data ) ) {
				$this->send_json_error( __( 'Empty value is not allowed', 'dc-bkash' ) );
			}

			$order_id = ( isset( $post_data['order_id'] ) ) ? sanitize_text_field( $post_data['order_id'] ) : '';

			$order = wc_get_order( $order_id );

			if ( ! $order instanceof \WC_Order ) {
				$this->send_json_error( __( 'Wrong or invalid order ID', 'dc-bkash' ) );
			}

			$process_payment = dc_bkash()->gateway->bkash()->process_payment( $order_id );

			if ( $process_payment ) {
				wp_send_json_success( $process_payment );
			}

			$this->send_json_error( __( 'Something went wrong!', 'dc-bkash' ) );

		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}
}
