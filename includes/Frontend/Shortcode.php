<?php
/**
 * Class Shortcode
 *
 * @author  Kapil Paul
 *
 * @since   2.0.0
 *
 * @package DCoders\Bkash\Frontend
 */

namespace DCoders\Bkash\Frontend;

/**
 * Class Shortcode
 */
class Shortcode {

	/**
	 * Shortcode constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'payment_gateway_bkash_for_wc', [ $this, 'render_frontend' ] );
	}

	/**
	 * Render frontend app
	 *
	 * @param array  $atts Attributes.
	 * @param string $content Content for shortcode.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function render_frontend( $atts, $content = '' ) {
		$content .= 'Hello World!';

		return $content;
	}
}
