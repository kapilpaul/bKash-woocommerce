<?php
/**
 * Class Settings
 *
 * @since 2.0.0
 *
 * @package DCoders\Bkash\Admin
 *
 * @author Kapil Paul
 */

namespace DCoders\Bkash\Admin;

/**
 * Class Settings
 */
class Settings {
	/**
	 * Option key to hold the settings in database
	 */
	const OPTION_KEY = 'dc_bkash_settings';

	/**
	 * Get settings field
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$fields = [
			'gateway' => [
				'title'              => [
					'title'   => __( 'Title', 'dc-bkash' ),
					'type'    => 'text',
					'default' => __( 'bKash Payment', 'dc-bkash' ),
				],
				'test_mode'          => [
					'title'   => __( 'Test Mode', 'dc-bkash' ),
					'type'    => 'select',
					'options' => [
						'on'  => __( 'ON', 'dc-bkash' ),
						'off' => __( 'OFF', 'dc-bkash' ),
					],
					'default' => __( 'off', 'dc-bkash' ),
				],
				'test_mode_type'     => [
					'title'   => __( 'Test Mode Type', 'dc-bkash' ),
					'type'    => 'select',
					'options' => [
						'without_key' => 'Without Key',
						'with_key'    => 'With Key',
					],
					'default' => __( 'with_key', 'dc-bkash' ),
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'on',
							'condition' => 'equal',
						],
					],
				],
				'username'           => [
					'title'   => __( 'User Name', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'off',
							'condition' => 'equal',
						],
					],
				],
				'password'           => [
					'title'   => __( 'Password', 'dc-bkash' ),
					'type'    => 'password',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'off',
							'condition' => 'equal',
						],
					],
				],
				'app_key'            => [
					'title'   => __( 'App Key', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'off',
							'condition' => 'equal',
						],
					],
				],
				'app_secret'         => [
					'title'   => __( 'App Secret', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'off',
							'condition' => 'equal',
						],
					],
				],
				'sandbox_username'   => [
					'title'   => __( 'Sandbox User Name', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'on',
							'condition' => 'equal',
						],
						[
							'key'       => 'test_mode_type',
							'value'     => 'with_key',
							'condition' => 'equal',
						],
					],
				],
				'sandbox_password'   => [
					'title'   => __( 'Sandbox Password', 'dc-bkash' ),
					'type'    => 'password',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'on',
							'condition' => 'equal',
						],
						[
							'key'       => 'test_mode_type',
							'value'     => 'with_key',
							'condition' => 'equal',
						],
					],
				],
				'sandbox_app_key'    => [
					'title'   => __( 'Sandbox App Key', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'on',
							'condition' => 'equal',
						],
						[
							'key'       => 'test_mode_type',
							'value'     => 'with_key',
							'condition' => 'equal',
						],
					],
				],
				'sandbox_app_secret' => [
					'title'   => __( 'Sandbox App Secret', 'dc-bkash' ),
					'type'    => 'text',
					'show_if' => [
						[
							'key'       => 'test_mode',
							'value'     => 'on',
							'condition' => 'equal',
						],
						[
							'key'       => 'test_mode_type',
							'value'     => 'with_key',
							'condition' => 'equal',
						],
					],
				],
				'transaction_charge' => [
					'title'   => __( 'Enable bKash Charge', 'dc-bkash' ),
					'type'    => 'select',
					'options' => [
						'on'  => 'ON',
						'off' => 'OFF',
					],
					'default' => 'off',
				],
				'charge_type'        => [
					'title'       => __( 'Charge Type', 'dc-bkash' ),
					'type'        => 'select',
					'options'     => [
						'fixed'      => 'Fixed',
						'percentage' => 'Percentage',
					],
					'default'     => 'percentage',
					'description' => __( 'This option will only work when the bKash Charge is enabled', 'dc-bkash' ),
					'show_if'     => [
						[
							'key'       => 'transaction_charge',
							'value'     => 'on',
							'condition' => 'equal',
						],
					],
				],
				'charge_amount'      => [
					'title'       => __( 'Charge Amount', 'dc-bkash' ),
					'type'        => 'text',
					'default'     => 2,
					'description' => __( 'This option will only work when the bKash Charge is enabled', 'dc-bkash' ),
					'show_if'     => [
						[
							'key'       => 'transaction_charge',
							'value'     => 'on',
							'condition' => 'equal',
						],
					],
				],
				'description'        => [
					'title'       => __( 'Description', 'dc-bkash' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'dc-bkash' ),
					'default'     => __( 'Pay via bKash', 'dc-bkash' ),
					'desc_tip'    => true,
				],
			],
		];

		return apply_filters( 'dc_bkash_settings_fields', $fields );
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
			'gateway' => [
				'id'    => 'gateway',
				'title' => 'Payment Gateway',
			],
		];

		return apply_filters( 'dc_bkash_settings_sections', $sections );
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

		$fields = wp_parse_args( get_option( self::OPTION_KEY ), $this->get_settings_fields() );

		foreach ( $this->get_settings_sections() as $key => $section ) {
			$settings['fields'][ $key ] = isset( $fields[ $key ] ) ? $fields[ $key ] : [];
		}

		return apply_filters( 'dc_bkash_get_settings', $settings );
	}

	/**
	 * Get option value
	 *
	 * @param string $option  Setting Option.
	 * @param string $section Setting Section.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|mixed|null
	 */
	public function get_option( $option, $section ) {

		$settings = $this->get_settings();

		if ( empty( $settings ) ) {
			return false;
		}

		$settings = $settings['fields'];
		$value    = null;

		if ( isset( $settings[ $section ] ) && isset( $settings[ $section ][ $option ] ) ) {
			$value = isset( $settings[ $section ][ $option ]['value'] ) ? $settings[ $section ][ $option ]['value'] : $settings[ $section ][ $option ]['default'];
		}

		return $value;
	}
}
