<?php
/**
 * Class BkashBaseRestController
 *
 * @since 2.0.0
 *
 * @package DCoders\Bkash\API
 *
 * @author Kapil Paul
 */

namespace DCoders\Bkash\API;

use WP_Http;
use WP_REST_Controller;

/**
 * Class BkashBaseRestController
 */
class BkashBaseRestController extends WP_REST_Controller {

	/**
	 * Namespace.
	 *
	 * @var string Namespace.
	 */
	public $namespace = 'dc-bkash';

	/**
	 * Version.
	 *
	 * @var string version.
	 */
	public $version = 'v1';

	/**
	 * Permission check
	 *
	 * @param \WP_REST_Request $request WP Rest Request.
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Error|bool
	 */
	public function admin_permissions_check( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'dc_bkash_permission_error',
				__( 'You have no permission to do that', 'dc-bkash' ),
				[ 'status' => WP_Http::BAD_REQUEST ]
			);
		}

		return true;
	}

	/**
	 * Get full namespace
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace . '/' . $this->version;
	}
}
