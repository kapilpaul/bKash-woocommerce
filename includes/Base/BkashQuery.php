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

		$selfClass = self::getSelfClass();
		$userName  = $selfClass->get_option( 'username' );
		$password  = $selfClass->get_option( 'password' );

		$data = [
			"app_key"    => $selfClass->get_option( 'app_key' ),
			"app_secret" => $selfClass->get_option( 'app_secret' ),
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
		$selfClass = self::getSelfClass();

		if ( $token = self::getToken() ) {
			$headers = [
				"Authorization" => "Bearer {$token}",
				"X-App-Key"     => $selfClass->get_option( 'app_key' ),
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
			$selfClass = self::getSelfClass();

			if ( $selfClass->get_option( 'test_mode' ) == 'on' ) {
				return true;
			}

			return false;
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}
}
