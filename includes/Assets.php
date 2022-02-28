<?php
/**
 * Class Assets
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

use DCoders\Bkash\Gateway\Processor;

/**
 * Scripts and Styles Class
 */
class Assets {
	/**
	 * Assets constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_all_scripts' ] );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ], 5 );
		} else {
			add_action( 'wp_enqueue_scripts', [ $this, 'register_all_scripts' ], 5 );
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'dc-app-vendor' );
		wp_localize_script( 'dc-app-vendor', 'dc_bkash_admin', $this->get_admin_localized_scripts() );
	}

	/**
	 * Enqueue front scripts
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_front_scripts() {

	}

	/**
	 * Register our app scripts and styles
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_all_scripts() {
		$this->register_scripts( $this->get_scripts() );
		$this->register_styles( $this->get_styles() );
	}

	/**
	 * Register scripts
	 *
	 * @param array $scripts Scripts data.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function register_scripts( $scripts ) {
		foreach ( $scripts as $handle => $script ) {
			$deps      = isset( $script['deps'] ) ? $script['deps'] : false;
			$in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
			$version   = isset( $script['version'] ) ? $script['version'] : BKASH_VERSION;

			wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
		}
	}

	/**
	 * Register styles
	 *
	 * @param array $styles Styles array data.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_styles( $styles ) {
		foreach ( $styles as $handle => $style ) {
			$deps = isset( $style['deps'] ) ? $style['deps'] : false;

			wp_register_style( $handle, $style['src'], $deps, BKASH_VERSION );
		}
	}

	/**
	 * Get all registered scripts
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_scripts() {
		$plugin_js_assets_path = BKASH_ASSETS . '/js/';

		$dependencies = [
			'wp-api-fetch',
		];

		// for local development
		// when webpack "hot module replacement" is enabled, this
		// constant needs to be turned "true" on "wp-config.php".
		if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
			$plugin_js_assets_path = 'http://localhost:8080/';
		}

		$scripts = [
			'dc-bkash'          => [
				'src'       => $plugin_js_assets_path . 'dc-bkash.js',
				'version'   => filemtime( BKASH_PATH . '/assets/js/dc-bkash.js' ),
				'deps'      => [ 'jquery' ],
				'in_footer' => true,
			],
			'sweetalert'        => [
				'src'       => '//cdn.jsdelivr.net/npm/sweetalert2@10',
				'deps'      => [ 'jquery' ],
				'in_footer' => true,
			],
			'dc-app-runtime'    => [
				'src'       => $plugin_js_assets_path . 'runtime.js',
				'version'   => filemtime( BKASH_PATH . '/assets/js/runtime.js' ),
				'deps'      => $dependencies,
				'in_footer' => true,
			],
			'dc-app-vendor'     => [
				'src'       => $plugin_js_assets_path . 'vendors.js',
				'version'   => filemtime( BKASH_PATH . '/assets/js/vendors.js' ),
				'deps'      => [ 'dc-app-runtime' ],
				'in_footer' => true,
			],
			'dc-app-script'     => [
				'src'       => $plugin_js_assets_path . 'app.js',
				'version'   => filemtime( BKASH_PATH . '/assets/js/app.js' ),
				'deps'      => [ 'dc-app-vendor' ],
				'in_footer' => true,
			],
			'dc-upgrade-script' => [
				'src'       => $plugin_js_assets_path . 'upgrade.js',
				'version'   => filemtime( BKASH_PATH . '/assets/js/upgrade.js' ),
				'deps'      => [ 'dc-app-vendor' ],
				'in_footer' => true,
			],
		];

		return $scripts;
	}

	/**
	 * Get registered styles
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_styles() {
		$plugin_css_assets_path = BKASH_ASSETS . '/css/';

		// for local development
		// when webpack "hot module replacement" is enabled, this
		// constant needs to be turned "true" on "wp-config.php".
		if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
			$plugin_css_assets_path = 'http://localhost:8080/';
		}

		$styles = [
			'dc-bkash'       => [
				'src'     => $plugin_css_assets_path . 'dc-bkash.css',
				'deps'    => [],
				'version' => filemtime( BKASH_PATH . '/assets/css/dc-bkash.css' ),
			],
			'dc-app-css'     => [
				'src'     => $plugin_css_assets_path . 'app.css',
				'deps'    => [ 'wp-components' ],
				'version' => filemtime( BKASH_PATH . '/assets/css/app.css' ),
			],
			'dc-upgrade-css' => [
				'src'     => $plugin_css_assets_path . 'upgrade.css',
				'deps'    => [ 'wp-components' ],
				'version' => filemtime( BKASH_PATH . '/assets/css/upgrade.css' ),
			],
		];

		return $styles;
	}

	/**
	 * Admin localized scripts
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_admin_localized_scripts() {
		$bkash_script_url = dc_bkash()->gateway->processor()->get_script();

		$localize_data = [
			'ajaxurl'                => admin_url( 'admin-ajax.php' ),
			'nonce'                  => wp_create_nonce( 'dc_bkash_admin' ),
			'rest'                   => [
				'root'    => esc_url_raw( get_rest_url() ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'version' => 'dc-bkash/v1',
			],
			'api'                    => null,
			'libs'                   => [],
			'current_time'           => current_time( 'mysql' ),
			'text_domain'            => 'dc-bkash',
			'asset_url'              => BKASH_ASSETS,
			'script_url'             => $bkash_script_url,
			'test_mode'              => dc_bkash()->gateway->processor()->check_test_mode(),
			'test_mode_with_key'     => dc_bkash()->gateway->processor()->get_test_mode_type( 'with_key' ),
			'all_credentials_filled' => dc_bkash_check_all_api_keys_filled(),
		];

		return apply_filters( 'dc_bkash_admin_localize_script', $localize_data );
	}
}
