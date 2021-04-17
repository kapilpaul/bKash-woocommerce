<?php
/**
 * Frontend handler class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

/**
 * Class Frontend
 */
class Frontend {

	/**
	 * Frontend constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		new Frontend\Shortcode();
	}
}
