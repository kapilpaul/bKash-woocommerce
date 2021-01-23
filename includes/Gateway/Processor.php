<?php

namespace DCoders\Bkash\Gateway;

/**
 * Bkash payment processor helper class
 *
 * Class Processor
 * @since 2.0.0
 *
 * @package DCoders\Bkash\Gateway
 *
 * @author Kapil Paul
 */
class Processor {
	/**
	 * Holds the processor class
	 *
	 * @since 2.0.0
	 */
	public static $instance;

	/**
	 * Holds grant token url
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $grant_token_url;

	/**
	 * Holds payment query url
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $payment_query_url;

	/**
	 * Processor constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$env = $this->check_test_mode() ? 'sandbox' : 'pay';

		$this->grant_token_url   = "https://checkout.$env.bka.sh/v1.2.0-beta/checkout/token/grant";
		$this->payment_query_url = "https://direct.$env.bka.sh/v1.2.0-beta/checkout/payment/query/";
	}

	/**
	 * Get self instance
	 *
	 * @since 2.0.0
	 *
	 * @return Processor
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			return self::$instance = new Processor();
		}

		return self::$instance;
	}

	/**
	 * Payment Create Url
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function payment_create_url() {
		return $this->get_payment_url( 'create' );
	}

	/**
	 * Payment execute Url
	 *
	 * @param $payment_id
	 *
	 * @return string
	 */
	public function payment_execute_url( $payment_id = '' ) {
		$url = $this->get_payment_url( 'execute' );
		$url = $this->check_test_mode() ? $url : $url . "/$payment_id";

		return $url;
	}

	/**
	 * Get Payment url based on type
	 *
	 * @param $type
	 *
	 * @return string
	 */
	public function get_payment_url( $type ) {
		if ( $this->check_test_mode() ) {
			return "https://merchantserver.sandbox.bka.sh/api/checkout/v1.2.0-beta/payment/$type";
		}

		return "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/$type";
	}

	/**
	 * Get Token
	 *
	 * @@since 2.0.0
	 *
	 * @return bool|mixed
	 */
	public function get_token() {
		if ( $token = get_transient( 'bkash_token' ) ) {
			return $token;
		}

		$prefix = 'with_key' === dc_bkash_get_option( 'test_mode_type' ) ? 'sandbox_' : '';

		$user_name = dc_bkash_get_option( $prefix . 'username' );
		$password  = dc_bkash_get_option( $prefix . 'password' );

		$data = [
			"app_key"    => dc_bkash_get_option( $prefix . 'app_key' ),
			"app_secret" => dc_bkash_get_option( $prefix . 'app_secret' ),
		];

		$headers = [
			"username"     => $user_name,
			"password"     => $password,
			"Content-Type" => "application/json",
		];

		$result = $this->make_request( $this->grant_token_url, $data, $headers );

		if ( isset( $result['id_token'] ) && isset( $result['token_type'] ) ) {
			$token = $result['id_token'];
			set_transient( 'bkash_token', $token, $result['expires_in'] );

			return $result['id_token'];
		}

		return false;
	}

	/**
	 * Sending remote request
	 *
	 * @param $url
	 * @param $data
	 * @param array $headers
	 *
	 * @return mixed|string|\WP_Error
	 */
	public function make_request( $url, $data, $headers = [] ) {
		if ( isset( $headers['headers'] ) ) {
			$headers = $headers['headers'];
		}

		$args = [
			'body'        => json_encode( $data ),
			'timeout'     => '30',
			'redirection' => '30',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'cookies'     => [],
		];

		$response = wp_remote_post( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

//		if (
//			200 !== wp_remote_retrieve_response_code( $response ) &&
//			201 !== wp_remote_retrieve_response_code( $response ) &&
//			204 !== wp_remote_retrieve_response_code( $response )
//		) {
//			return new \WP_Error( 'dc_bkash_request_error', $body );
//		}

		return json_decode( $response, true );
	}

	/**
	 * Verify payment on bKash end
	 *
	 * @param $payment_id
	 * @param $order_total
	 *
	 * @return bool|mixed|string
	 */
	public function verify_payment( $payment_id, $order_total ) {
		if ( $this->check_test_mode() ) {
			return [
				'amount'                => $order_total,
				'paymentID'             => $payment_id,
				'trxID'                 => $payment_id,
				'transactionStatus'     => 'completed',
				'merchantInvoiceNumber' => 'test-invoice-number',
			];
		}

		$token = $this->get_token();

		if ( ! $token ) {
			return false;
		}

		$url      = $this->payment_query_url . $payment_id;
		$response = wp_remote_get( $url, $this->get_authorization_header() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $result['errorCode'] ) && isset( $result['errorMessage'] ) ) {
			return false;
		}

		return $result;
	}

	/**
	 * @param $invoice
	 * @param $amount
	 *
	 * @return bool|mixed|string
	 */
	public function create_payment( $amount, $invoice ) {
		if ( ! $this->check_test_mode() && ! $this->get_token() ) {
			return false;
		}

		$amount = self::get_final_amount( $amount );

		$payment_data = [
			'amount'                => $amount,
			'currency'              => 'BDT',
			'intent'                => 'sale',
			'merchantInvoiceNumber' => $invoice,
		];

		$response = self::make_request( self::paymentCreateUrl(), $payment_data, self::get_authorization_header() );

		if ( isset( $response['paymentID'] ) && $response['paymentID'] ) {
			return $response;
		}

		return false;
	}

	/**
	 * @param $payment_id
	 *
	 * @return bool|mixed|string
	 */
	public function executePayment( $payment_id ) {
		if ( ! self::check_test_mode() && ! self::get_token() ) {
			return false;
		}

		$data = [];

		if ( self::check_test_mode() ) {
			$data = [ 'paymentID' => $payment_id ];
		}

		$response = self::make_request( self::paymentExecuteUrl( $payment_id ), $data, $this->get_authorization_header() );

		if ( isset( $response['transactionStatus'] ) && $response['transactionStatus'] == 'Completed' ) {
			return $response;
		}

		return false;
	}

	/**
	 * Get Authorization header for bkash
	 *
	 * @return array
	 */
	public function get_authorization_header() {
		if ( $token = $this->get_token() ) {
			$headers = [
				"Authorization" => "Bearer {$token}",
				"X-App-Key"     => $this->get_pgw_option( 'app_key' ),
				"Content-Type"  => 'application/json',
			];

			$args = [ 'headers' => $headers ];

			return $args;
		}

		return [ 'headers' => [ "Content-Type" => 'application/json' ] ];
	}

	/**
	 * Check if test mode is on or not
	 *
	 * @return bool
	 */
	public function check_test_mode() {
		try {
			if ( dc_bkash_get_option( 'test_mode', 'gateway' ) === 'on' ) {
				return true;
			}

			return false;
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}

	/**
	 * calculate final amount based on bKash charge option
	 *
	 * @param $amount
	 *
	 * @since 1.3.0
	 *
	 * @return float|int
	 */
	public function get_final_amount( $amount ) {
		if ( $this->get_pgw_option( 'transaction_charge' ) == 'yes' ) {
			$charge_type   = $this->get_pgw_option( 'charge_type' );
			$charge_amount = (float) $this->get_pgw_option( 'charge_amount' );

			if ( $charge_type == 'percentage' ) {
				$amount = $amount + $amount * ( $charge_amount / 100 );
			} else {
				$amount = $amount + $charge_amount;
			}
		}

		$amount = number_format( $amount, 2, '.', '' );

		return $amount;
	}

	/**
	 * Get payment gateway settings option
	 *
	 * @param $key
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_pgw_option( $key ) {
		$self_class = $this->getSelfClass();

		return $self_class->get_option( $key );
	}
}
