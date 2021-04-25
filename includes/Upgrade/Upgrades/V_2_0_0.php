<?php
/**
 * Class V_2_0_0
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade\Upgrades
 */

namespace DCoders\Bkash\Upgrade\Upgrades;

use DCoders\Bkash\Abstracts\DcBkashUpgrader;
use DCoders\Bkash\Admin\Settings as AdminSettings;

/**
 * Class V_2_0_0
 */
class V_2_0_0 extends DcBkashUpgrader {

	/**
	 * Update bKash transactions table and add `verification_status` column
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function update_bkash_transactions_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'bkash_transactions';

		// @codingStandardsIgnoreStart
		$wpdb->query(
			"ALTER TABLE `{$table_name}`
            ADD COLUMN `verification_status` INT(1) DEFAULT 0 AFTER `amount`"
		);
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Update installed and version values in option table.
	 * Updating the key here.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function update_option_keys() {
		$installed = get_option( 'wcwpbkash_installed' );

		if ( $installed ) {
			update_option( 'dc_bkash_installed', $installed );

			delete_option( 'wcwpbkash_installed' );
		}

		update_option( dc_bkash()->get_db_version_key(), get_option( 'wcwpbkash_version', BKASH_VERSION ) );

		delete_option( 'wcwpbkash_version' );
	}

	/**
	 * Migrate old format credentials to new format
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public static function migrate_pgw_credentials() {
		$old_data = get_option( 'woocommerce_bkash_settings', [] );

		// If there is no old data found for bKash then return.
		if ( ! $old_data ) {
			return;
		}

		$settings_format = dc_bkash()->settings->get_settings();

		if ( empty( $settings_format ) && isset( $settings_format['fields']['gateway'] ) ) {
			return;
		}

		$gateway_settings = $settings_format['fields']['gateway'];

		// adding data to sandbox key if it's on test mode.
		if ( isset( $old_data['test_mode'] ) && 'on' === $old_data['test_mode'] ) {
			$old_data['sandbox_username']   = $old_data['username'];
			$old_data['sandbox_password']   = $old_data['password'];
			$old_data['sandbox_app_key']    = $old_data['app_key'];
			$old_data['sandbox_app_secret'] = $old_data['app_secret'];

			//phpcs:ignore
			$old_data['username'] = $old_data['password'] = $old_data['app_key'] = $old_data['app_secret'] = '';
		}

		// convert transaction charge value from 'yes' to 'on'.
		if ( wc_string_to_bool( $old_data['transaction_charge'] ) ) {
			$old_data['transaction_charge'] = 'on';
		}

		foreach ( $gateway_settings as $key => $settings ) {
			if ( array_key_exists( $key, $old_data ) ) {
				$gateway_settings[ $key ]['default'] = $old_data[ $key ];
			}
		}

		update_option( AdminSettings::OPTION_KEY, [ 'gateway' => $gateway_settings ], false );

		// Delete dependency transients.
		delete_transient( 'dc_bkash_token' );
		delete_transient( 'dc_bkash_token_data' );
	}
}
