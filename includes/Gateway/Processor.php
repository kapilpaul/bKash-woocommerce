<?php

namespace DCoders\Bkash\Gateway;

use PHPMailer\PHPMailer\Exception;

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
	 * @var string
	 */
	protected $version = 'v1.2.0-beta';

	/**
	 * Processor constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$env    = $this->check_test_mode() ? 'sandbox' : 'pay';
		$server = $this->check_test_mode() ? 'checkout' : 'direct';

		$this->grant_token_url   = "https://checkout.$env.bka.sh/{$this->version}/checkout/token/grant";
		$this->payment_query_url = "https://$server.$env.bka.sh/{$this->version}/checkout/payment/query/";
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
		$url = $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ? $url : $url . "/$payment_id";

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
		if ( $this->get_test_mode_type( 'without_key' ) ) {
			return "https://merchantserver.sandbox.bka.sh/api/checkout/{$this->version}/payment/$type";
		}

		$server = $this->check_test_mode() ? 'sandbox' : 'pay';

		return "https://checkout.{$server}.bka.sh/{$this->version}/checkout/payment/$type";
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

		return json_decode( $body, true );
	}

	/**
	 * Create payment request in bKash
	 *
	 * @param $amount
	 *
	 * @param $invoice_id
	 *
	 * @return bool|mixed|string
	 */
	public function create_payment( $amount, $invoice_id ) {
		try {
			if ( ! $this->check_test_mode() && ! $this->get_token() ) {
				return false;
			}

			$amount = $this->get_final_amount( $amount );

			$payment_data = [
				'amount'                => $amount,
				'currency'              => 'BDT',
				'intent'                => 'sale',
				'merchantInvoiceNumber' => $invoice_id,
			];

			$response = $this->make_request( $this->payment_create_url(), $payment_data, $this->get_authorization_header() );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			if ( isset( $response['paymentID'] ) && $response['paymentID'] ) {
				return $response;
			}

			return new \WP_Error( 'dc_bkash_create_payment_error', $response );
		} catch ( Exception $e ) {
			return new \WP_Error( 'dc_bkash_create_payment_error', $e );
		}
	}

	/**
	 * Execute payment url
	 *
	 * @param $payment_id
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

		$response = $this->make_request( $this->payment_execute_url( $payment_id ), $data, $this->get_authorization_header() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response['transactionStatus'] ) && 'Completed' === $response['transactionStatus'] ) {
			return $response;
		}

		return new \WP_Error( 'dc_bkash_execute_payment_error', $response );
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

		$url      = $this->payment_query_url . $payment_id;
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
	 * Get Authorization header for bkash
	 *
	 * @return array
	 */
	public function get_authorization_header() {
		if ( $token = $this->get_token() ) {
			$prefix = $this->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

			$headers = [
				"Authorization" => "Bearer {$token}",
				"X-App-Key"     => dc_bkash_get_option( $prefix . 'app_key' ),
				"Content-Type"  => 'application/json',
			];

			$args = [ 'headers' => $headers ];

			return $args;
		}

		return [ 'headers' => [ "Content-Type" => 'application/json' ] ];
	}

	/**
	 * Get Token from bKash
	 *
	 * @since 2.0.0
	 *
	 * @return bool|mixed
	 */
	public function get_token() {
		$token = get_transient( 'dc_bkash_token' );

		if ( $token ) {
			return $token;
		}

		$prefix = $this->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

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

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( isset( $result['id_token'] ) && isset( $result['token_type'] ) ) {
			$token = $result['id_token'];
			set_transient( 'dc_bkash_token', $token, $result['expires_in'] );

			return $result['id_token'];
		}

		return new \WP_Error( 'dc_bkash_create_token_error', $result );
	}

	/**
	 * Check if test mode is on or not
	 *
	 * @return bool
	 */
	public function check_test_mode() {
		if ( 'on' === dc_bkash_get_option( 'test_mode', 'gateway' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Calculate final amount based on bKash charge option
	 *
	 * @param $amount
	 *
	 * @since 1.3.0
	 *
	 * @return float|int
	 */
	public function get_final_amount( $amount ) {
		$amount = apply_filters( 'dc_bkash_before_calculated_final_amount', $amount );

		if ( 'yes' === dc_bkash_get_option( 'transaction_charge' ) ) {
			$charge_type   = dc_bkash_get_option( 'charge_type' );
			$charge_amount = (float) dc_bkash_get_option( 'charge_amount' );

			if ( 'percentage' === $charge_type ) {
				$amount = $amount + $amount * ( $charge_amount / 100 );
			} else {
				$amount = $amount + $charge_amount;
			}
		}

		$amount = number_format( $amount, 2, '.', '' );

		return apply_filters( 'dc_bkash_after_calculated_final_amount', $amount );
	}

	/**
	 * Get bkash script
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_script() {
		$env    = $this->check_test_mode() ? 'sandbox' : 'pay';
		$suffix = $this->check_test_mode() ? '-sandbox' : 'pay';

		$script = "https://scripts.{$env}.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout{$suffix}.js";

		return $script;
	}

	/**
	 * Get test mode type with key or not
	 *
	 * @param bool $key
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function get_test_mode_type( $key ) {
		if ( ! $this->check_test_mode() ) {
			return false;
		}

		$test_mode_type = dc_bkash_get_option( 'test_mode_type' );

		if ( $key === $test_mode_type ) {
			return true;
		}

		return false;
	}
}
