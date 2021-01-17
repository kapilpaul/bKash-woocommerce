<?php

namespace DCoders\Bkash\Admin;

/**
 * Class Settings
 * @since 2.0.0
 *
 * @package DCoders\Bkash\Admin
 *
 * @author Kapil Paul
 */
class Settings {
	/**
	 * Settings constructor
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_ajax_dc_bkash_get_setting', [ $this, 'get_settings_data' ] );
//		add_action( 'wp_ajax_dc_bkash_save_settings', [ $this, 'save_settings' ] );
	}

	/**
	 * Get settings field
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$fields = [
			'gateway'           => [
				'test_mode'          => [
					'title'   => __( 'Test Mode', BKASH_TEXT_DOMAIN ),
					'type'    => 'select',
					'options' => [ "on" => "ON", "off" => "OFF" ],
					'default' => __( 'off', BKASH_TEXT_DOMAIN ),
				],
				'title'              => [
					'title'   => __( 'Title', BKASH_TEXT_DOMAIN ),
					'type'    => 'text',
					'default' => __( 'bKash Payment', BKASH_TEXT_DOMAIN ),
				],
				'username'           => [
					'title' => __( 'User Name', BKASH_TEXT_DOMAIN ),
					'type'  => 'text',
				],
				'password'           => [
					'title' => __( 'Password', BKASH_TEXT_DOMAIN ),
					'type'  => 'password',
				],
				'app_key'            => [
					'title' => __( 'App Key', BKASH_TEXT_DOMAIN ),
					'type'  => 'text',
				],
				'app_secret'         => [
					'title' => __( 'App Secret', BKASH_TEXT_DOMAIN ),
					'type'  => 'text',
				],
				'transaction_charge' => [
					'title'   => __( 'Enable bKash Charge', BKASH_TEXT_DOMAIN ),
					'type'    => 'checkbox',
					'label'   => __( '&nbsp;', BKASH_TEXT_DOMAIN ),
					'default' => 'no',
				],
				'charge_type'        => [
					'title'       => __( 'Charge Type', BKASH_TEXT_DOMAIN ),
					'type'        => 'select',
					'options'     => [ "fixed" => "Fixed", "percentage" => "Percentage" ],
					'default'     => 'percentage',
					'description' => __( 'This option will only work when the bKash Charge is enabled', BKASH_TEXT_DOMAIN ),
				],
				'charge_amount'      => [
					'title'       => __( 'Charge Amount', BKASH_TEXT_DOMAIN ),
					'type'        => 'text',
					'default'     => 2,
					'description' => __( 'This option will only work when the bKash Charge is enabled', BKASH_TEXT_DOMAIN ),
				],
				'description'        => [
					'title'       => __( 'Description', BKASH_TEXT_DOMAIN ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', BKASH_TEXT_DOMAIN ),
					'default'     => __( 'Pay via bKash', BKASH_TEXT_DOMAIN ),
					'desc_tip'    => true,
				],
			],
			'dokan_integration' => [],
		];

		return $fields;
	}

	/**
	 * Get settings sections
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings_sections() {
		$sections = [
			'gateway'           => [
				'id'    => 'gateway',
				'title' => 'Payment Gateway',
			],
			'dokan_integration' => [
				'id'    => 'dokan_integration',
				'title' => 'Dokan Integration',
			],
		];

		return $sections;
	}

	/**
	 * Send settings data requesting from ajax
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function get_settings_data() {
		$this->check_permission();

		wp_send_json_success( $this->get_settings() );
	}

	/**
	 * Get all settings data
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = [
			'sections' => $this->get_settings_sections(),
			'fields'   => [],
		];

		$fields = $this->get_settings_fields();

		foreach ( $this->get_settings_sections() as $key => $section ) {
			$settings['fields'][ $section ] = isset( $fields[ $section ] ) ? $fields[ $section ] : [];
		}

		return $settings;
	}

	/**
	 * Validate nonce and permission check
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function check_permission() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( __( 'You have no permission to get settings value', BKASH_TEXT_DOMAIN ) );
		}

		$_post_data = wp_unslash( $_POST );

		if ( ! isset( $_post_data['nonce'] ) || ! wp_verify_nonce( $_post_data['nonce'], 'dc_bkash_admin' ) ) {
			wp_send_json_error( __( 'Invalid nonce', BKASH_TEXT_DOMAIN ) );
		}
	}
}
