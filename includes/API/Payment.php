<?php


namespace DCoders\Bkash\API;

use DCoders\Bkash\Gateway\Processor;
use WP_Error;
use WP_Http;
use WP_REST_Server;

/**
 * Class Payment
 * @author Kapil Paul
 * @since 2.0.0
 *
 * @package DCoders\Bkash\API
 *
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

		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/execute-payment/(?P<id>[a-zA-Z0-9-]+)', $this->rest_base ),
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the payment.', BKASH_TEXT_DOMAIN ),
						'type'        => 'string',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'execute_payment' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/verify-payment/(?P<id>[a-zA-Z0-9-]+)', $this->rest_base ),
			[
				'args'   => [
					'id' => [
						'description' => __( 'Unique identifier for the payment.', BKASH_TEXT_DOMAIN ),
						'type'        => 'string',
					],
				],
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'query_payment' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}

	/**
	 * Get token for bKash Payment gateway
	 * returning with token data and request header
	 *
	 * @param $request
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
				__( $token->get_error_message(), BKASH_TEXT_DOMAIN ),
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
			'title'          => __( 'Grant Token', BKASH_TEXT_DOMAIN ),
			'data'           => $token,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->grant_token_url,
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Create Payment data
	 * returning with payment data and request header
	 *
	 * @param $request
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function create_payment( $request ) {
		$bkash_processor = Processor::get_instance();
		$amount          = rand( 10, 100 );
		$invoice_id      = sprintf( 'TBP%s', str_pad( rand( 10, 999 ), 5, "0", STR_PAD_LEFT ) );
		$create_payment  = $bkash_processor->create_payment( (float) $amount, $invoice_id );

		if ( is_wp_error( $create_payment ) ) {
			return new WP_Error(
				'dc_bkash_rest_api_payment_create_payment_error',
				__( $create_payment->get_error_message(), BKASH_TEXT_DOMAIN ),
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
			'title'          => __( 'Create Payment', BKASH_TEXT_DOMAIN ),
			'data'           => $create_payment,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_create_url(),
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Execute Payment data
	 * returning with execute payment data and request header
	 *
	 * @param $request
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
				__( $execute_payment->get_error_message(), BKASH_TEXT_DOMAIN ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers' => $bkash_processor->get_authorization_header()['headers'],
		];

		$response = [
			'title'          => __( 'Execute Payment', BKASH_TEXT_DOMAIN ),
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
	 * @param $request
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
				__( $verify_payment->get_error_message(), BKASH_TEXT_DOMAIN ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		$request_params = [
			'headers' => $bkash_processor->get_authorization_header()['headers'],
		];

		$response = [
			'title'          => __( 'Query Payment', BKASH_TEXT_DOMAIN ),
			'data'           => $verify_payment,
			'request_params' => $request_params,
			'request_url'    => $bkash_processor->payment_query_url . $payment_id,
		];

		return rest_ensure_response( $response );
	}
}