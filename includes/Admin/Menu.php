<?php
/**
 * Admin Pages Handler
 *
 * Class Menu
 *
 * @since 2.0.0
 *
 * @package DCoders\Bkash\Admin
 *
 * @author Kapil Paul
 */

namespace DCoders\Bkash\Admin;

/**
 * Class Menu.
 *
 * @package DCoders\Bkash\Admin
 */
class Menu {
	/**
	 * Menu constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
	}

	/**
	 * Register our menu page
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		global $submenu;

		$parent_slug = 'dc-bkash';
		$capability  = 'manage_options';

		$hook = add_menu_page( __( 'bKash', 'dc-bkash' ), __( 'bKash', 'dc-bkash' ), $capability, $parent_slug, [ $this, 'plugin_page' ], BKASH_ASSETS . '/images/bkash.png' );

		if ( current_user_can( $capability ) ) {
			$submenu[ $parent_slug ][] = [ __( 'Transactions', 'dc-bkash' ), $capability, $this->get_submenu_url() ]; // phpcs:ignore

			$submenu[ $parent_slug ][] = [ __( 'Search Transaction', 'dc-bkash' ), $capability, $this->get_submenu_url( 'search-transaction' ) ]; // phpcs:ignore

			$submenu[ $parent_slug ][] = [ __( 'Refund', 'dc-bkash' ), $capability, $this->get_submenu_url( 'refund' ) ]; // phpcs:ignore

			$submenu[ $parent_slug ][] = [ __( 'Settings', 'dc-bkash' ), $capability, $this->get_submenu_url( 'settings' ) ]; // phpcs:ignore

			$submenu[ $parent_slug ][] = [ __( 'Generate Doc', 'dc-bkash' ), $capability, $this->get_submenu_url( 'generate-doc' ) ]; // phpcs:ignore
		}

		add_action( 'load-' . $hook, [ $this, 'init_hooks' ] );
	}

	/**
	 * Initialize our hooks for the admin page
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init_hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Load scripts and styles for the app
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'dc-app-css' );
		wp_enqueue_script( 'dc-app-script' );
	}

	/**
	 * Handles the main page
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function plugin_page() {
		echo '<div id="dc-bkash-app"></div>';
	}

	/**
	 * Make submenu admin url from slug
	 *
	 * @param string $slug Slug for menu.
	 * @param string $parent_slug Parent slug.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	private function get_submenu_url( $slug = '', $parent_slug = 'dc-bkash' ) {
		return 'admin.php?page=' . $parent_slug . '#/' . $slug;
	}
}
