<?php
/**
 * Class Settings
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Admin
 */

namespace DCoders\Bkash\API;

use WP_REST_Server;
use DCoders\Bkash\Admin\Settings as AdminSettings;

/**
 * Class Settings
 */
class Settings extends BkashBaseRestController {
	/**
	 * Initialize the class
	 */
	public function __construct() {
		$this->rest_base = 'settings';
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
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_settings_data' ],
					'permission_callback' => [ $this, 'admin_permission_check' ],
					'args'                => $this->get_collection_params(),
				],
				[
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => [ $this, 'update_items' ],
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
					'permission_callback' => [ $this, 'admin_permissions_check' ],
				],
				'schema' => [ $this, 'get_item_schema' ],
			]
		);
	}

	/**
	 * Admin permission check
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function admin_permission_check() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get settings data.
	 *
	 * @param object $request Request Object.
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function get_settings_data( $request ) {
		$settings = dc_bkash()->settings->get_settings();

		return rest_ensure_response( $settings );
	}

	/**
	 * Update items.
	 *
	 * @param object $request Request Object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function update_items( $request ) {
		$fields = $request->get_param( 'fields' );

		update_option( AdminSettings::OPTION_KEY, $fields, false );

		// Delete dependency transients.
		delete_transient( 'dc_bkash_token' );
		delete_transient( 'dc_bkash_token_data' );

		return $this->get_settings_data( $request );
	}

	/**
	 * Get item schema
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'settings',
			'type'       => 'object',
			'properties' => [],
		];

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
