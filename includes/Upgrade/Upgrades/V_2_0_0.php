<?php

namespace DCoders\Bkash\Upgrade\Upgrades;

use DCoders\Bkash\Abstracts\DcBkashUpgrader;

/**
 * Class V_2_0_0
 * @package DCoders\Bkash\Upgrade\Upgrades
 *
 * @author Kapil
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

		$wpdb->query(
			"ALTER TABLE `{$table_name}`
            ADD COLUMN `verification_status` INT(1) DEFAULT 0 AFTER `amount`"
		);
	}

	/**
	 * Update installed and version values in option table
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

		update_option( dc_bkash()->get_db_version_key(), BKASH_VERSION );

		delete_option( 'wcwpbkash_version' );
	}
}
