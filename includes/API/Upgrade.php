<?php
/**
 * Class Upgrade
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\API
 */

namespace DCoders\Bkash\API;

use Peast\Syntax\Exception;
use WP_Error;
use WP_Http;
use WP_REST_Server;

/**
 * Class Upgrade
 */
class Upgrade extends BkashBaseRestController {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'upgrade';
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
			'/' . $this->rest_base,
			[
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'apply_updates' ],
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}

	/**
	 * Apply updates.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return WP_Error|\WP_REST_Response
	 * @throws \Exception Error throwing.
	 */
	public function apply_updates( $request ) {
		try {
			if ( dc_bkash()->upgrades->has_ongoing_process() ) {
				throw new \Exception( __( 'There is an upgrading process going on.', 'dc-bkash' ) );
			}

			if ( ! dc_bkash()->upgrades->is_upgrade_required() ) {
				throw new \Exception( __( 'Update is not required', 'dc-bkash' ) );
			}

			// Doing the upgrading here.
			dc_bkash()->upgrades->do_upgrade();

			return rest_ensure_response( [ 'message' => 'success' ] );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'dc_bkash_upgradable_error',
				__( $e->getMessage(), 'dc-bkash' ), //phpcs:ignore
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}
	}

	/**
	 * Update permission check
	 *
	 * @since 2.0.0
	 *
	 * @return bool|\WP_Error
	 */
	public function admin_permission_check() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return new \WP_Error(
				'dc_bkash_upgradable_error',
				__( 'You have no permission to do that', 'dc-bkash' ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		return true;
	}
}
