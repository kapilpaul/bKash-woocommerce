<?php

namespace DCoders\Bkash\Gateway;

/**
 * Class Manager
 * @since 2.0.0
 *
 * @package DCoders\Bkash
 *
 * @author Kapil Paul
 */
class Manager {
	/**
	 * Hold instance of bKash
	 *
	 * @since 2.0.0
	 */
	public $bkash_instance;

	/**
	 * Manager constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_gateway' ] );
	}

	/**
	 * Add payment class to the container
	 *
	 * @since 2.0.0
	 *
	 * @return Bkash
	 */
	public function bkash() {
		$this->bkash_instance = false;

		if ( ! $this->bkash_instance ) {
			$this->bkash_instance = new Bkash();
		}

		return $this->bkash_instance;
	}

	/**
	 * Register WooCommerce Payment Gateway
	 *
	 * @param array $gateways
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = $this->bkash();

		return $gateways;
	}

	/**
	 * Get bKash processor class instance
	 *
	 * @since 2.0.0
	 *
	 * @return Processor
	 */
	public function processor() {
		return Processor::get_instance();
	}
}
