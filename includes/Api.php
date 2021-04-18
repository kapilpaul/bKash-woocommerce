<?php
/**
 * API Class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

use DCoders\Bkash\API\Payment;
use DCoders\Bkash\API\Settings;
use DCoders\Bkash\API\Transaction;
use DCoders\Bkash\API\Upgrade;

/**
 * Class API
 */
class API {

	/**
	 * Holds the api classes.
	 *
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
	public function __construct() {
		$this->classes = [
			Settings::class,
			Upgrade::class,
			Payment::class,
			Transaction::class,
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
