<?php
/**
 * Class Transaction
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\API
 */

namespace DCoders\Bkash\API;

use WP_REST_Server;

/**
 * Class Transaction
 */
class Transaction extends BkashBaseRestController {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'transactions';
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
			sprintf( '/%s/', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_transactions' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);

		register_rest_route(
			$this->get_namespace(),
			sprintf( '/%s/refund', $this->rest_base ),
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'refund_transaction' ],
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}

	/**
	 * Get all transactions.
	 *
	 * @param \WP_Rest_Request $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 */
	public function get_transactions( $request ) {
		$args   = [];
		$params = $this->get_collection_params();

		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		// change `per_page` to `number`.
		$args['number'] = $args['per_page'];
		$args['offset'] = $args['number'] * ( $args['page'] - 1 );

		// unset others.
		unset( $args['per_page'] );
		unset( $args['page'] );

		$data         = [];
		$transactions = dc_bkash_get_payments_list( $args );

		if ( ! empty( $transactions ) ) {
			foreach ( $transactions as $transaction ) {
				$response = $this->prepare_item_for_response( $transaction, $request );
				$data[]   = $this->prepare_response_for_collection( $response );
			}
		}

		$total     = isset( $args['search'] ) ? count( $transactions ) : dc_bkash_get_payments_count();
		$max_pages = ceil( $total / (int) $args['number'] );

		$response = rest_ensure_response( $data );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return $response;
	}

	/**
	 * Refund transaction.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.1.0
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function refund_transaction( $request ) {
		$order_number     = $request->get_param( 'order_number' );
		$amount           = $request->get_param( 'amount' );
		$reason           = $request->get_param( 'refund_reason' );
		$wc_create_refund = $request->get_param( 'wc_create_refund' );

		$refund = dc_bkash()->gateway->init_refund( $order_number, $amount, $reason, $wc_create_refund );

		$response = rest_ensure_response( $refund );

		return $response;
	}

	/**
	 * Prepare a transaction item for response.
	 *
	 * @param mixed            $transaction Transaction Object.
	 * @param \WP_REST_Request $request     Request data.
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function prepare_item_for_response( $transaction, $request ) {
		$data = (array) $transaction;

		$order = wc_get_order( $transaction->order_number );

		if ( $order ) {
			$data['order_url'] = $order->get_edit_order_url();
		}

		$data['created_at'] = gmdate( 'd-m-Y h:i:s A', strtotime( $transaction->created_at ) );

		$response = rest_ensure_response( $data );
		$response->add_links( $this->prepare_links( $transaction ) );

		return $response;
	}

	/**
	 * Prepares links for the request.
	 *
	 * @param mixed $transaction Transaction object.
	 *
	 * @return array Links for the given post.
	 */
	protected function prepare_links( $transaction ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = [
			'self'       => [
				'href' => rest_url( trailingslashit( $base ) . $transaction->id ),
			],
			'collection' => [
				'href' => rest_url( $base ),
			],
		];

		return $links;
	}
}
