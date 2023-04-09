<?php
/**
 * Bkash payment processor helper class
 *
 * Class Processor
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Gateway
 */

namespace DCoders\Bkash\Gateway;

use DCoders\Bkash\Gateway\IntegrationTypes\Checkout;
use DCoders\Bkash\Gateway\IntegrationTypes\CheckoutUrl;

/**
 * Class Processor
 */
class Processor {
	/**
	 * Holds the processor class
	 *
	 * @var Processor
	 *
	 * @since 2.0.0
	 */
	public static $instance;

	/**
	 * Get self instance
	 *
	 * @since 2.0.0
	 *
	 * @return Processor
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			$integration_type = dc_bkash_get_option( 'integration_type' );

			switch ( $integration_type ) {
				case 'checkout_url':
					self::$instance = new CheckoutUrl();
					break;
				case 'checkout':
				default:
					self::$instance = new Checkout();
					break;
			}
		}

		return self::$instance;
	}
}
