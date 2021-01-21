<?php

namespace DCoders\Bkash\API;

use WP_REST_Controller;

/**
 * Class BkashRestController
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
class BkashRestController extends WP_REST_Controller {
	/**
	 * @var string
	 */
	public $namespace = 'dc-bkash';

	/**
	 * @var string
	 */
	public $version = 'v1';

	/**
	 * Permission check
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Error|bool
	 */
	public function admin_permissions_check( $request ) {
		return current_user_can( 'manage_options' );
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
