<?php
/**
 * Class V_2_1_0
 *
 * @since 2.1.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade\Upgrades
 */

namespace DCoders\Bkash\Upgrade\Upgrades;

use DCoders\Bkash\Abstracts\DcBkashUpgrader;

/**
 * Class V_2_1_0
 */
class V_2_1_0 extends DcBkashUpgrader {

	/**
	 * Update bKash transactions table and add `refund_status` & `refund_amount` column
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
            ADD COLUMN `refund_status` INT(1) NOT NULL DEFAULT 0 AFTER `verification_status`,
            ADD COLUMN `refund_amount` FLOAT DEFAULT 0 AFTER `refund_status`,
            ADD COLUMN `refund_reason` varchar(255) DEFAULT NULL AFTER `refund_amount`"
		);
		// @codingStandardsIgnoreEnd
	}
}
