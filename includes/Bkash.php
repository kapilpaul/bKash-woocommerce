<?php
/**
 * Bkash-woocommerce
 * Kapil Paul
 */

namespace Inc;

use Inc\Base\BkashQuery;

class Bkash {
	/**
	 * @var string
	 */
	private $table = 'bkash_transactions';

	/**
	 * @var object
	 */
	private $order;

	/**
	 * Bkash constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wc-bkash-create-payment-request', array( $this, 'create_payment_request' ) );
		add_action( 'wp_ajax_wc-bkash-execute-payment-request', array( $this, 'execute_payment_request' ) );
		add_action( 'wp_ajax_wc-bkash-get-order-amount', array( $this, 'get_order_amount' ) );
	}

	/**
	 * Store the payment and insert
	 * validation on bKash end by payment id
	 *
	 * @param $payment_id
	 *
	 * @param $order_number
	 *
	 * @param bool $bkash_response_data
	 *
	 * @return bool
	 */
	public function payment_store( $payment_id, $order_number, $bkash_response_data = false ) {
		try {
			$payment_id        = sanitize_text_field( $payment_id );
			$this->order       = $order = wc_get_order( $order_number );
			$order_grand_total = (float) $this->order->get_total();
			$order_grand_total = BkashQuery::get_final_amount( $order_grand_total );

			if ( $bkash_response_data['amount'] == $order_grand_total ) {
				$this->order->add_order_note(
					sprintf( __( 'bKash payment completed. Transaction ID #%s! Amount: %s', 'bkash-wc' ),
						$bkash_response_data['trxID'],
						$order_grand_total
					)
				);

				$this->order->payment_complete();

			} else {
				$this->order->update_status(
					'on-hold',
					__( "Partial payment.Transaction ID #{$bkash_response_data['trxID']}! Amount: {$bkash_response_data['amount']}", 'bkash-wc' )
				);
			}

			$payment_info = BkashQuery::verifyPayment( $payment_id, $order_grand_total );

			if ( isset( $payment_info['transactionStatus'] ) && isset( $payment_info['trxID'] ) ) {
			} else {
				$payment_info = $bkash_response_data;
			}

			$insert_data = [
				"order_number"       => $this->order->get_id(),
				"payment_id"         => $payment_info['paymentID'],
				"trx_id"             => $payment_info['trxID'],
				"transaction_status" => $payment_info['transactionStatus'],
				"invoice_number"     => $payment_info['merchantInvoiceNumber'],
				"amount"             => $payment_info['amount'],
			];

			insert_bkash_transaction( $insert_data );

		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
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

	/**
	 * create payment request for bKash
	 *
	 * @return void
	 */
	public function create_payment_request() {
		try {
			if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'wc-bkash-nonce' ) ) {
				$this->send_json_error( 'Something went wrong here!' );
			}

			if ( ! $this->validate_fields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_key( $_POST['order_number'] ) : '';

			$order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( 'Wrong or invalid order ID' );
			}

			$response = BkashQuery::createPayment( (float) $order->get_total(), $order_number );

			if ( $response ) {
				wp_send_json_success( $response );
			}

			$this->send_json_error( 'Something went wrong!' );
		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Execute payment request for bKash
	 *
	 * @return void
	 */
	public function execute_payment_request() {
		try {
			if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'wc-bkash-nonce' ) ) {
				$this->send_json_error( 'Something went wrong here!' );
			}

			if ( ! $this->validate_fields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$payment_id   = ( isset( $_POST['payment_id'] ) ) ? sanitize_text_field( $_POST['payment_id'] ) : '';
			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_text_field( $_POST['order_number'] ) : '';

			$this->order = $order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( 'Wrong or invalid order ID' );
			}

			$response = BkashQuery::executePayment( $payment_id );

			if ( $response ) {
				$this->payment_store( $response['paymentID'], $order_number, $response );
				$response['order_success_url'] = $order->get_checkout_order_received_url();
				wp_send_json_success( $response );
			}

			$this->send_json_error( 'Something went wrong!' );

		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}

	/**
	 * get order amount
	 *
	 * @return void
	 */
	public function get_order_amount() {
		try {
			if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'wc-bkash-nonce' ) ) {
				$this->send_json_error( 'Something went wrong here!' );
			}

			if ( ! $this->validate_fields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_text_field( $_POST['order_number'] ) : '';

			$order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( 'Wrong or invalid order ID' );
			}

			$total = BkashQuery::get_final_amount( (float) $order->get_total() );

			$order_data = [
				'amount'            => (float) $total,
				'order_success_url' => $order->get_checkout_order_received_url(),
			];

			wp_send_json_success( $order_data );

		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}

	/**
	 * send json error
	 *
	 * @param $text
	 *
	 * @return void
	 */
	public function send_json_error( $text ) {
		wp_send_json_error( __( $text, 'bkash-wc' ) );
		wp_die();
	}
}
