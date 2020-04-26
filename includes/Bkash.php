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
	 * Bkash constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wc-bkash-process', array( $this, 'paymentStore' ) );
		add_action( 'wp_ajax_wc-bkash-create-payment-request', array( $this, 'create_payment_request' ) );
		add_action( 'wp_ajax_wc-bkash-execute-payment-request', array( $this, 'execute_payment_request' ) );
		add_action( 'wp_ajax_wc-bkash-get-order-amount', array( $this, 'get_order_amount' ) );
	}

	/**
	 * Store the payment and insert
	 * validation on bKash end by payment id
	 */
	public function paymentStore() {
		try {
			if ( ! wp_verify_nonce( $_POST['_ajax_nonce'], 'wc-bkash-nonce' ) ) {
				$this->send_json_error( 'Something went wrong!' );
			}

			$postParams        = [ 'order_number', 'payment_id' ];
			$containsAllValues = ! array_diff_key( array_flip( $postParams ), $_POST );

			if ( ! $containsAllValues ) {
				$this->send_json_error( 'Params are missing.' );
			}

			if ( ! $this->validateFields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$order_number = sanitize_key( $_POST['order_number'] );
			$order_number = isset( $order_number ) ? $order_number : 0;
			$payment_id   = sanitize_text_field( $_POST['payment_id'] );

			$order           = wc_get_order( $order_number );
			$orderGrandTotal = (float) $order->get_total();

			$paymentInfo = BkashQuery::verifyPayment( $payment_id, $orderGrandTotal );

			if ( $paymentInfo ) {
				$insertData = [
					"order_number"       => $order_number,
					"payment_id"         => $paymentInfo['paymentID'],
					"trx_id"             => $paymentInfo['trxID'],
					"transaction_status" => $paymentInfo['transactionStatus'],
					"invoice_number"     => $paymentInfo['merchantInvoiceNumber'],
					"amount"             => $paymentInfo['amount'],
				];

				$this->insertBkashPayment( $insertData );

				if ( $paymentInfo['amount'] == $orderGrandTotal ) {
					$order->add_order_note( sprintf( __( 'bKash payment completed.Transaction ID #%s! Amount: %s', 'bkash-wc' ), $trx_id, $orderGrandTotal ) );
					$order->payment_complete();

					wp_send_json_success( __( $order->get_view_order_url() ) );
				} else {
					$order->update_status(
						'on-hold',
						__( 'Partial payment.Transaction ID #%s! Amount: %s', 'bkash-wc' ),
						$trx_id,
						$paymentInfo['amount']
					);
				}
			}
			$this->send_json_error( "Failed" );
		} catch ( \Exception $e ) {
			$this->send_json_error( $e->getMessage() );
		}
	}

	/**
	 * insert Payment info
	 * to bKash transactions table
	 *
	 * @param $paymentInfo
	 *
	 * @return false|int
	 */
	public function insertBkashPayment( $paymentInfo ) {
		global $wpdb;

		$insert = $wpdb->insert( $wpdb->prefix . $this->table, array(
			"order_number"       => $paymentInfo['order_number'],
			"payment_id"         => $paymentInfo['payment_id'],
			"trx_id"             => $paymentInfo['trx_id'],
			"transaction_status" => $paymentInfo['transaction_status'],
			"invoice_number"     => $paymentInfo['invoice_number'],
			"amount"             => $paymentInfo['amount'],
		) );

		return $insert;
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	public function validateFields( $data ) {
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

			if ( ! $this->validateFields( $_POST ) ) {
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

			if ( ! $this->validateFields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$payment_id   = ( isset( $_POST['payment_id'] ) ) ? sanitize_text_field( $_POST['payment_id'] ) : '';
			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_text_field( $_POST['order_number'] ) : '';

			$order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( 'Wrong or invalid order ID' );
			}

			$response = BkashQuery::executePayment( $payment_id );

			if ( $response ) {
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

			if ( ! $this->validateFields( $_POST ) ) {
				$this->send_json_error( 'Empty value is not allowed' );
			}

			$order_number = ( isset( $_POST['order_number'] ) ) ? sanitize_text_field( $_POST['order_number'] ) : '';

			$order = wc_get_order( $order_number );

			if ( ! is_object( $order ) ) {
				$this->send_json_error( 'Wrong or invalid order ID' );
			}

			$order_data = [
				'amount'            => (float) $order->get_total(),
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
