<?php

namespace DCoders\Bkash\Frontend;

/**
 * Class Shortcode
 * @package DCoders\Bkash\Frontend
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
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
     * @param array $atts
     * @param string $content
     *
     * @since 2.0.0
     *
     * @return string
     */
    public function render_frontend( $atts, $content = '' ) {
        // wp_enqueue_style( 'frontend' );
        // wp_enqueue_script( 'frontend' );

        $content .= 'Hello World!';

        return $content;
    }
}
