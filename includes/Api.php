<?php

namespace DCoders\Bkash;

use DCoders\Bkash\API\Payment;
use DCoders\Bkash\API\Settings;
use DCoders\Bkash\API\Upgrade;

/**
 * API Class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
class API {
	/**
	 * @var array
	 */
	private $classes;

	/**
	 * Initialize the class
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	function __construct() {
		$this->classes = [
			Settings::class,
			Upgrade::class,
			Payment::class,
		];

		add_action( 'rest_api_init', [ $this, 'register_api' ] );
	}

	/**
	 * Register the API
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_api() {
		foreach ( $this->classes as $class ) {
			$object = new $class();
			$object->register_routes();
		}
	}
}
