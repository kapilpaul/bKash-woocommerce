<?php
/**
 * Checkout class.
 *
 * Class Checkout
 *
 * @since 3.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Gateway\IntegrationTypes
 */

namespace DCoders\Bkash\Gateway\IntegrationTypes;

use DCoders\Bkash\Abstracts\BkashProcessor;

/**
 * Class Checkout
 *
 * @package DCoders\Bkash\Gateway\IntegrationTypes
 */
class Checkout extends BkashProcessor {

	/**
	 * Set url.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function set_urls() {
		$this->base_url = "https://checkout.$this->env.bka.sh/{$this->version}/checkout";

		$this->grant_token_url = $this->base_url . '/token/grant';

		$server                   = $this->check_test_mode() ? 'checkout' : 'direct';
		$payment_query_base       = "https://$server.$this->env.bka.sh/{$this->version}/checkout/payment/";
		$this->payment_query_url  = sprintf( '%squery/', $payment_query_base );
		$this->payment_search_url = sprintf( '%ssearch/', $payment_query_base );
		$this->refund_payment_url = sprintf( '%srefund/', $payment_query_base );
	}

	/**
	 * Create payment url.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function payment_create_url() {
		return $this->get_payment_url( 'create' );
	}

	/**
	 * Payment execute Url.
	 *
	 * @param string $payment_id Payment ID.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function payment_execute_url( $payment_id = '' ) {
		$url = $this->get_payment_url( 'execute' );
		$url = $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ? $url : $url . "/$payment_id";

		return $url;
	}

	/**
	 * Get Payment url based on type.
	 *
	 * @param string $type Type of payment.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_payment_url( $type ) {
		if ( $this->get_test_mode_type( 'without_key' ) ) {
			return "https://merchantserver.sandbox.bka.sh/api/checkout/{$this->version}/payment/$type";
		}

		return "{$this->base_url}/payment/$type";
	}

	/**
	 * Create payment request in bKash.
	 *
	 * @param float   $amount                 Amount.
	 * @param string  $invoice_id             Invoice ID.
	 * @param boolean $calculate_final_amount Final amount calculation.
	 * @param bool    $callback_url           Callback url.
	 * @param bool    $payer_reference        Payer reference.
	 *
	 * @return \WP_Error|mixed
	 */
	public function create_payment(
		$amount,
		$invoice_id,
		$calculate_final_amount = false,
		$callback_url = false,
		$payer_reference = false
	) {
		try {
			$amount = $calculate_final_amount ? $this->get_final_amount( $amount ) : $amount;

			$payment_data = [
				'amount'                => $amount,
				'currency'              => 'BDT',
				'intent'                => 'sale',
				'merchantInvoiceNumber' => $invoice_id,
			];

			return parent::create_payment_request( $payment_data );

		} catch ( \Exception $e ) {
			return new \WP_Error( 'dc_bkash_create_payment_error', $e );
		}
	}

	/**
	 * Execute payment url.
	 *
	 * @param string $payment_id Payment ID.
	 *
	 * @since 3.0.0
	 *
	 * @return bool|mixed|string
	 */
	public function execute_payment( $payment_id ) {
		if ( ! $this->check_test_mode() && ! $this->get_token() ) {
			return false;
		}

		$data = [];

		if ( $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ) {
			$data = [ 'paymentID' => $payment_id ];
		}

		return parent::execute_payment_request( $payment_id, $data );
	}

	/**
	 * Verify payment on bKash end.
	 *
	 * @param string $payment_id  Payment ID.
	 * @param float  $order_total Order Total.
	 *
	 * @return bool|mixed|string
	 */
	public function verify_payment( $payment_id, $order_total = null ) {
		if ( $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ) {
			return [
				'amount'                => $order_total,
				'paymentID'             => $payment_id,
				'trxID'                 => $payment_id,
				'transactionStatus'     => 'Completed',
				'merchantInvoiceNumber' => 'test-invoice-number',
			];
		}

		$token = $this->get_token();

		if ( ! $token || is_wp_error( $token ) ) {
			return false;
		}

		$url      = esc_url_raw( $this->payment_query_url . $payment_id );
		$response = wp_remote_get( $url, $this->get_authorization_header() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $result['errorCode'] ) && isset( $result['errorMessage'] ) ) {
			return new \WP_Error( 'dc_bkash_verify_payment_error', $result );
		}

		return $result;
	}

	/**
	 * Search payment on bKash end.
	 *
	 * @param string $trx_id Transaction ID.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed|\WP_Error
	 */
	public function search_transaction( $trx_id ) {
		if ( $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ) {
			return new \WP_Error( 'dc_bkash_search_payment_error', __( 'No API keys available', 'dc-bkash' ), [ 'status' => 500 ] );
		}

		$token = $this->get_token();

		if ( ! $token || is_wp_error( $token ) ) {
			return new \WP_Error( 'dc_bkash_search_payment_error', $token, [ 'status' => 500 ] );
		}

		$url      = esc_url_raw( $this->payment_search_url . $trx_id );
		$response = wp_remote_get( $url, $this->get_authorization_header() );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'dc_bkash_search_payment_error', $response, [ 'status' => 500 ] );
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $result['errorCode'] ) && isset( $result['errorMessage'] ) ) {
			return new \WP_Error( 'dc_bkash_search_payment_error', $result, [ 'status' => 500 ] );
		}

		return $result;
	}
}
