<?php
/**
 * Class Payment
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\API
 */

namespace DCoders\Bkash\API;

use DCoders\Bkash\Gateway\Processor;
use WP_Error;
use WP_Http;
use WP_REST_Server;

/**
 * Class Payment
 */
class Payment extends BkashBaseRestController {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'payment';
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/get-token', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_grant_token' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/create-payment', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'create_payment' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		$this->register_single_route( 'execute-payment/(?P<id>[a-zA-Z0-9-]+)', [ $this, 'execute_payment' ] );
		$this->register_single_route( 'query-payment/(?P<id>[a-zA-Z0-9-]+)', [ $this, 'query_payment' ] );
		$this->register_single_route( 'search-payment/(?P<id>[a-zA-Z0-9-]+)', [ $this, 'search_payment' ] );
		$this->register_single_route( 'search-transaction/(?P<id>[a-zA-Z0-9-]+)', [ $this, 'search_transaction' ] );
		$this->register_single_route( 'refund-payment/(?P<id>[a-zA-Z0-9-]+)', [ $this, 'refund_payment' ] );
	}

	/**
	 * Get token for bKash Payment gateway.
	 * returning with token data and request header.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_REST_Response|WP_Error
	 */
	public function get_grant_token( $request ) {
		$bkash_processor = Processor::get_instance();
		$token           = $bkash_processor->get_token( true );

		if ( is_wp_error( $token ) ) {
			return new WP_Error(
				'dc_bkash_rest_api_payment_get_token_error',
				__( $token->get_error_message(), 'dc-bkash' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$credentials = $bkash_processor->get_credentials();

		$request_params = [
			'headers'     => [
				'username' => $credentials['user_name'],
				'password' => $credentials['password'],
			],
			'body_params' => [
				'app_key'    => $credentials['app_key'],
				'app_secret' => $credentials['app_secret'],
			],
		];

		$response = [
			'title'          => __( 'Grant Token', 'dc-bkash' ),
			'data'           => $token,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->grant_token_url,
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Create Payment data.
	 * Returning with payment data and request header.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function create_payment( $request ) {
		$get_amount      = $request->get_param( 'amount' );
		$bkash_processor = Processor::get_instance();
		$amount          = $get_amount ? $get_amount : wp_rand( 10, 100 );
		$invoice_id      = sprintf( 'TBP%s', str_pad( wp_rand( 10, 999 ), 5, 0, STR_PAD_LEFT ) );
		$create_payment  = $bkash_processor->create_payment( (float) $amount, $invoice_id );

		if ( is_wp_error( $create_payment ) ) {
			return new WP_Error(
				'dc_bkash_rest_api_payment_create_payment_error',
				__( $create_payment->get_error_message(), 'dc-bkash' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers'     => $bkash_processor->get_authorization_header()['headers'],
			'body_params' => [
				'amount'                => $amount,
				'currency'              => 'BDT',
				'intent'                => 'sale',
				'merchantInvoiceNumber' => $invoice_id,
			],
		];

		$response = [
			'title'          => __( 'Create Payment', 'bkash-wc' ),
			'data'           => $create_payment,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_create_url(),
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Execute Payment data.
	 * Returning with execute payment data and request header.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function execute_payment( $request ) {
		$payment_id = $request->get_param( 'id' );

		$bkash_processor = Processor::get_instance();
		$execute_payment = $bkash_processor->execute_payment( $payment_id );

		if ( is_wp_error( $execute_payment ) ) {
			return new WP_Error(
				'dc_bkash_rest_api_payment_execute_payment_error',
				__( $execute_payment->get_error_message(), 'dc-bkash' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers' => $bkash_processor->get_authorization_header()['headers'],
		];

		$response = [
			'title'          => __( 'Execute Payment', 'dc-bkash' ),
			'data'           => $execute_payment,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_execute_url( $payment_id ),
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Verify Payment data
	 * returning with verify payment data and request header
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function query_payment( $request ) {
		$payment_id = $request->get_param( 'id' );

		$bkash_processor = Processor::get_instance();
		$verify_payment  = $bkash_processor->verify_payment( $payment_id );

		if ( is_wp_error( $verify_payment ) ) {
			return new WP_Error(
				'dc_bkash_rest_api_payment_verify_payment_error',
				__( $verify_payment->get_error_message(), 'dc-bkash' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers' => $bkash_processor->get_authorization_header()['headers'],
		];

		$response = [
			'title'          => __( 'Query Payment', 'dc-bkash' ),
			'data'           => $verify_payment,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_query_url . $payment_id,
		];

		do_action( 'dc_bkash_after_query_payment', $payment_id, $verify_payment, $response );

		return rest_ensure_response( $response );
	}

	/**
	 * Search Transaction details
	 * returning with search payment data and request header
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function search_payment( $request ) {
		$trx_id = $request->get_param( 'id' );

		$bkash_processor    = Processor::get_instance();
		$search_transaction = $bkash_processor->search_transaction( $trx_id );

		$request_params = [
			'headers' => $bkash_processor->get_authorization_header()['headers'],
		];

		$response = [
			'title'          => __( 'Search Transaction Details', 'dc-bkash' ),
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_search_url . $trx_id,
		];

		if ( is_wp_error( $search_transaction ) ) {
			$response['data'] = $search_transaction->get_error_message( 'dc_bkash_search_payment_error' );

			return rest_ensure_response( $response );
		}

		$response['data'] = $search_transaction;

		return rest_ensure_response( $response );
	}

	/**
	 * Search Transaction details
	 * returning with search payment data and request header
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function search_transaction( $request ) {
		$trx_id = $request->get_param( 'id' );

		$bkash_processor    = Processor::get_instance();
		$search_transaction = $bkash_processor->search_transaction( $trx_id );

		if ( is_wp_error( $search_transaction ) ) {
			return $search_transaction;
		}

		return rest_ensure_response( $search_transaction );
	}

	/**
	 * Refund Transaction details
	 * returning with refund payment data and request header
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function refund_payment( $request ) {
		$payment_id = $request->get_param( 'id' );
		$amount     = $request->get_param( 'amount' );
		$trx_id     = $request->get_param( 'trx_id' );
		$title      = $request->get_param( 'title' );

		$bkash_processor    = Processor::get_instance();
		$refund_transaction = $bkash_processor->refund( $amount, $payment_id, $trx_id, 'Product Fault' );

		$request_params = [
			'headers'     => $bkash_processor->get_authorization_header()['headers'],
			'body_params' => [
				'amount'    => $amount,
				'paymentID' => $payment_id,
				'trxID'     => $trx_id,
				'reason'    => 'Product Fault',
			],
		];

		$response = [
			'title'          => $title,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->refund_payment_url,
		];

		if ( is_wp_error( $refund_transaction ) ) {
			$response['data'] = $refund_transaction->get_error_message( 'dc_bkash_refund_payment_error' );

			return rest_ensure_response( $response );
		}

		$response['data'] = $refund_transaction;

		return rest_ensure_response( $response );
	}

	/**
	 * Registering single route which will have a id as a argument.
	 *
	 * @param string $path            Route Path.
	 * @param array  $callback_method Callback function to serve.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function register_single_route( $path, array $callback_method ) {
		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/%s', $this->rest_base, $path ),
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the payment.', 'dc-bkash' ),
						'type'        => 'string',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $callback_method,
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}
}
