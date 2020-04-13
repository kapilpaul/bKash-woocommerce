<?php

namespace Inc\Admin;

/**
 * Class Menu
 * @package Inc\Admin
 */
class Menu {
	/**
	 * Menu constructor.
	 * Initialize the class
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Set the admin menu
	 *
	 * @return void
	 */
	public function admin_menu() {
		$parent_slug = 'bkash-wc';
		$capability  = 'manage_options';

		add_menu_page( __( 'bKash Payments', 'bkash-wc' ), __( 'bKash', 'bkash-wc' ), $capability, $parent_slug, [ $this, 'bkash_page' ],  WC_WP_BKASH_URL . '/images/bkash.png');
	}

	/**
	 * Render the plugin page
	 *
	 * @return void
	 */
	public function bkash_page() {
		$payments = new Payments();
		$payments->plugin_page();
	}
}
