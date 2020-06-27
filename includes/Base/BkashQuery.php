<?php
/**
 * Bkash-woocommerce
 * Kapil Paul
 */

namespace Inc\Base;

use Inc\WC_PGW_BKASH;

/**
 * Class BkashQuery
 * @package Inc\Base
 */
class BkashQuery extends WC_PGW_BKASH {
	/**
	 * class instance
	 */
	private static $selfClassInstance;

	/**
	 * @return BkashQuery
	 */
	public static function getSelfClass() {
		if ( ! self::$selfClassInstance ) {
			return self::$selfClassInstance = ( new self );
		}

		return self::$selfClassInstance;
	}

	/**
	 * Grant Token Url
	 *
	 * @return string
	 */
	public static function grantTokenUrl() {
		$env = self::checkTestMode() ? 'sandbox' : 'pay';

		return "https://checkout.$env.bka.sh/v1.2.0-beta/checkout/token/grant";
	}

	/**
	 * Payment Query Url
	 *
	 * @return string
	 */
	public static function paymentQueryUrl() {
		$env = self::checkTestMode() ? 'sandbox' : 'pay';

		return "https://direct.$env.bka.sh/v1.2.0-beta/checkout/payment/query/";
	}

	/**
	 * Payment Create Url
	 *
	 * @return string
	 */
	public static function paymentCreateUrl() {
		return self::getPaymentUrl( 'create' );
	}

	/**
	 * Payment execute Url
	 *
	 * @param $payment_id
	 *
	 * @return string
	 */
	public static function paymentExecuteUrl( $payment_id = '' ) {
		$url = self::getPaymentUrl( 'execute' );
		$url = self::checkTestMode() ? $url : $url . "/$payment_id";

		return $url;
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	public static function getPaymentUrl( $type ) {
		if ( self::checkTestMode() ) {
			return "https://merchantserver.sandbox.bka.sh/api/checkout/v1.2.0-beta/payment/$type";
		}

		return "https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/$type";
	}

	/**
	 * Get Token
	 *
	 * @return bool|mixed
	 */
	public static function getToken() {
		if ( $token = get_transient( 'bkash_token' ) ) {
			return $token;
		}

		$userName = self::get_pgw_option( 'username' );
		$password = self::get_pgw_option( 'password' );

		$data = [
			"app_key"    => self::get_pgw_option( 'app_key' ),
			"app_secret" => self::get_pgw_option( 'app_secret' ),
		];

		$headers = [
			"username"     => $userName,
			"password"     => $password,
			"Content-Type" => "application/json",
		];

		$result = self::makeRequest( self::grantTokenUrl(), $data, $headers );

		if ( isset( $result['id_token'] ) && isset( $result['token_type'] ) ) {
			$token = $result['id_token'];
			set_transient( 'bkash_token', $token, $result['expires_in'] );

			return $result['id_token'];
		}

		return false;
	}

	/**
	 * sending curl request
	 *
	 * @param $url
	 * @param $data
	 * @param array $headers
	 *
	 * @return mixed|string
	 */
	public static function makeRequest( $url, $data, $headers = [] ) {
		if ( isset( $headers['headers'] ) ) {
			$headers = $headers['headers'];
		}

		$args = array(
			'body'        => json_encode( $data ),
			'timeout'     => '30',
			'redirection' => '30',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers,
			'cookies'     => [],
		);

		$response = wp_remote_retrieve_body( wp_remote_post( esc_url_raw( $url ), $args ) );

		return json_decode( $response, true );
	}

	/**
	 * verify payment on bKash end
	 *
	 * @param $paymentID
	 * @param $orderTotal
	 *
	 * @return bool|mixed|string
	 */
	public static function verifyPayment( $paymentID, $orderTotal ) {
		if ( self::checkTestMode() ) {
			return [
				'amount'                => $orderTotal,
				'paymentID'             => $paymentID,
				'trxID'                 => $paymentID,
				'transactionStatus'     => 'completed',
				'merchantInvoiceNumber' => 'test-invoice-number',
			];
		}

		if ( $token = self::getToken() ) {
			$url      = self::paymentQueryUrl() . $paymentID;
			$response = wp_remote_get( $url, self::getAuthorizationHeader() );
			$result   = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( isset( $result['errorCode'] ) && isset( $result['errorMessage'] ) ) {
				return false;
			}

			return $result;
		}

		return false;
	}

	/**
	 * @param $invoice
	 * @param $amount
	 *
	 * @return bool|mixed|string
	 */
	public static function createPayment( $amount, $invoice ) {
		if ( ! self::checkTestMode() && ! self::getToken() ) {
			return false;
		}

		$amount = self::get_final_amount( $amount );

		$payment_data = [
			'amount'                => $amount,
			'currency'              => 'BDT',
			'intent'                => 'sale',
			'merchantInvoiceNumber' => $invoice,
		];

		$response = self::makeRequest( self::paymentCreateUrl(), $payment_data, self::getAuthorizationHeader() );

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
	public static function executePayment( $payment_id ) {
		if ( ! self::checkTestMode() && ! self::getToken() ) {
			return false;
		}

		$data = [];

		if ( self::checkTestMode() ) {
			$data = [ 'paymentID' => $payment_id ];
		}

		$response = self::makeRequest( self::paymentExecuteUrl( $payment_id ), $data, self::getAuthorizationHeader() );

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
	public static function getAuthorizationHeader() {
		if ( $token = self::getToken() ) {
			$headers = [
				"Authorization" => "Bearer {$token}",
				"X-App-Key"     => self::get_pgw_option( 'app_key' ),
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
	public static function checkTestMode() {
		try {
			if ( self::get_pgw_option( 'test_mode' ) == 'on' ) {
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
	 * @since 1.3.0
	 *
	 * @param $amount
	 *
	 * @return float|int
	 */
	public static function get_final_amount( $amount ) {
		if ( self::get_pgw_option( 'transaction_charge' ) == 'yes' ) {
			$charge_type   = self::get_pgw_option( 'charge_type' );
			$charge_amount = (float) self::get_pgw_option( 'charge_amount' );

			if ( $charge_type == 'percentage' ) {
				$amount = $amount + $amount * ( $charge_amount / 100 );
			} else {
				$amount = $amount + $charge_amount;
			}
		}

		$amount = number_format($amount, 2, '.', '');

		return $amount;
	}

	/**
	 * Get payment gateway settings option
	 *
	 * @since 1.3.0
	 *
	 * @param $key
	 *
	 * @return string
	 */
	public static function get_pgw_option( $key ) {
		$self_class = self::getSelfClass();

		return $self_class->get_option( $key );
	}
}
