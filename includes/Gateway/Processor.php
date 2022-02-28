<?php
/**
 * Bkash payment processor helper class
 *
 * Class Processor
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Gateway
 */

namespace DCoders\Bkash\Gateway;

use PHPMailer\PHPMailer\Exception;

/**
 * Class Processor
 */
class Processor {
	/**
	 * Holds the processor class
	 *
	 * @var Processor
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
	 * Holds payment search url
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $payment_search_url;

	/**
	 * Refund payment url
	 *
	 * @since 2.1.0
	 *
	 * @var string
	 */
	public $refund_payment_url;

	/**
	 * Holds the version.
	 *
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

		$this->grant_token_url = "https://checkout.$env.bka.sh/{$this->version}/checkout/token/grant";

		$payment_query_base       = "https://$server.$env.bka.sh/{$this->version}/checkout/payment/";
		$this->payment_query_url  = sprintf( '%squery/', $payment_query_base );
		$this->payment_search_url = sprintf( '%ssearch/', $payment_query_base );
		$this->refund_payment_url = sprintf( '%srefund/', $payment_query_base );
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
			self::$instance = new Processor();
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
	 * Payment execute Url.
	 *
	 * @param string $payment_id Payment ID.
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
	 * Sending remote request.
	 *
	 * @param string $url     Target URL request.
	 * @param array  $data    Data for sending request.
	 * @param array  $headers Headers data.
	 *
	 * @return mixed|string|\WP_Error
	 */
	public function make_request( $url, $data, $headers = [] ) {
		if ( isset( $headers['headers'] ) ) {
			$headers = $headers['headers'];
		}

		$args = [
			'body'        => wp_json_encode( $data ),
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
	 * Create payment request in bKash.
	 *
	 * @param float   $amount                 Amount.
	 * @param string  $invoice_id             Invoice ID.
	 * @param boolean $calculate_final_amount Final amount calculation.
	 *
	 * @return bool|mixed|string
	 */
	public function create_payment( $amount, $invoice_id, $calculate_final_amount = false ) {
		try {
			if ( ! $this->check_test_mode() && ! $this->get_token() ) {
				return false;
			}

			$amount = $calculate_final_amount ? $this->get_final_amount( $amount ) : $amount;

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
	 * Execute payment url.
	 *
	 * @param string $payment_id Payment ID.
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
	 * @since 2.0.0
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

	/**
	 * Get Authorization header for bkash
	 *
	 * @return array
	 */
	public function get_authorization_header() {
		try {
			$token = $this->get_token();

			if ( ! is_wp_error( $token ) && $token ) {
				$prefix = $this->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

				$headers = [
					'Authorization' => sprintf( 'Bearer %s', $token ),
					'X-App-Key'     => dc_bkash_get_option( $prefix . 'app_key' ),
					'Content-Type'  => 'application/json',
				];

				$args = [
					'headers' => $headers,
					'timeout' => apply_filters( 'dc_bkash_remote_timeout', 30 ),
				];

				return $args;
			}

			return [ 'headers' => [ 'Content-Type' => 'application/json' ] ];
		} catch ( \Exception $e ) {
			return [ 'headers' => [ 'Content-Type' => 'application/json' ] ];
		}
	}

	/**
	 * Get Token from bKash
	 *
	 * @param bool|string $token_data Token data.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|mixed
	 */
	public function get_token( $token_data = false ) {
		$token = get_transient( 'dc_bkash_token' );

		if ( $token ) {
			$token_response = get_transient( 'dc_bkash_token_data' );

			if ( $token_data && $token_response ) {
				return $token_response;
			}

			return $token;
		}

		$prefix = $this->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

		$user_name = dc_bkash_get_option( $prefix . 'username' );
		$password  = dc_bkash_get_option( $prefix . 'password' );

		$data = [
			'app_key'    => dc_bkash_get_option( $prefix . 'app_key' ),
			'app_secret' => dc_bkash_get_option( $prefix . 'app_secret' ),
		];

		$headers = [
			'username'     => $user_name,
			'password'     => $password,
			'Content-Type' => 'application/json',
		];

		$result = $this->make_request( $this->grant_token_url, $data, $headers );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( isset( $result['id_token'] ) && isset( $result['token_type'] ) ) {
			$token = $result['id_token'];
			set_transient( 'dc_bkash_token', $token, $result['expires_in'] );

			// Setting full response data in transient.
			set_transient( 'dc_bkash_token_data', $result, $result['expires_in'] );

			if ( $token_data ) {
				return $result;
			}

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
	 * Calculate final amount based on bKash charge option.
	 *
	 * @param float $amount Amount.
	 *
	 * @since 1.3.0
	 *
	 * @return float|int
	 */
	public function get_final_amount( $amount ) {
		$amount = apply_filters( 'dc_bkash_before_calculated_final_amount', $amount );

		$amount = $amount + $this->get_transaction_charge_amount( $amount );

		$amount = number_format( $amount, 2, '.', '' );

		return apply_filters( 'dc_bkash_after_calculated_final_amount', $amount );
	}

	/**
	 * Get transaction charge amount.
	 *
	 * @param float $total_amount Total Amount.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed|void
	 */
	public function get_transaction_charge_amount( $total_amount ) {
		$transaction_charge        = dc_bkash_get_option( 'transaction_charge' );
		$transaction_charge_amount = 0;

		if ( 'on' === $transaction_charge || 'yes' === $transaction_charge ) {
			$charge_type   = dc_bkash_get_option( 'charge_type' );
			$charge_amount = (float) dc_bkash_get_option( 'charge_amount' );

			if ( 'percentage' === $charge_type ) {
				$transaction_charge_amount = (float) $total_amount * ( $charge_amount / 100 );
			} else {
				$transaction_charge_amount = $charge_amount;
			}
		}

		$transaction_charge_amount = number_format( $transaction_charge_amount, 2, '.', '' );

		return apply_filters( 'dc_bkash_get_transaction_charge', $transaction_charge_amount );
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
		$suffix = $this->check_test_mode() ? '-sandbox' : '';

		$script = sprintf( 'https://scripts.%s.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout%s.js', $env, $suffix );

		return $script;
	}

	/**
	 * Get test mode type with key or not
	 *
	 * @param string $key Key for the option.
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

	/**
	 * Get Credential from settings.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_credentials() {
		$prefix = $this->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

		$user_name  = dc_bkash_get_option( $prefix . 'username' );
		$password   = dc_bkash_get_option( $prefix . 'password' );
		$app_key    = dc_bkash_get_option( $prefix . 'app_key' );
		$app_secret = dc_bkash_get_option( $prefix . 'app_secret' );

		return [
			'user_name'  => $user_name,
			'password'   => $password,
			'app_key'    => $app_key,
			'app_secret' => $app_secret,
		];
	}

	/**
	 * Refund payment.
	 *
	 * @param string $amount     Refund amount.
	 * @param string $payment_id Payment ID.
	 * @param string $trx_id     Transaction ID.
	 * @param string $reason     Refund Reason.
	 *
	 * @since 2.1.0
	 *
	 * @return bool|mixed|string|\WP_Error
	 */
	public function refund( $amount, $payment_id, $trx_id, $reason = '' ) {
		if ( $this->check_test_mode() && $this->get_test_mode_type( 'without_key' ) ) {
			return false;
		}

		$refund_data = [
			'amount'    => "$amount",
			'paymentID' => $payment_id,
			'trxID'     => $trx_id,
			'sku'       => 'hello test',
			'reason'    => empty( $reason ) ? __( 'Refund amount', 'dc-bkash' ) : $reason,
		];

		$response = $this->make_request( $this->refund_payment_url, $refund_data, $this->get_authorization_header() );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'dc_bkash_refund_payment_error', $response );
		}

		if ( isset( $response['transactionStatus'] ) && 'completed' === strtolower( $response['transactionStatus'] ) ) {
			return $response;
		}

		return new \WP_Error( 'dc_bkash_refund_payment_error', $response );
	}
}
