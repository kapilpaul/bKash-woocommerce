<?php

namespace Inc\Admin;

/**
 * Class Payments
 * @package Inc\Admin
 */
class Payments {
	/**
	 * @return void
	 */
	public function plugin_page() {
		$template = __DIR__ . '/views/payment-list.php';

		if ( file_exists( $template ) ) {
			include $template;
		}
	}
}
