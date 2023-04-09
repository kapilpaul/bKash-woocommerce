<?php
/**
 * Class V_3_0_0
 *
 * @since 3.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade\Upgrades
 */

namespace DCoders\Bkash\Upgrade\Upgrades;

use DCoders\Bkash\Abstracts\DcBkashUpgrader;
use DCoders\Bkash\Admin\Settings as AdminSettings;

/**
 * Class V_3_0_0
 */
class V_3_0_0 extends DcBkashUpgrader {

	/**
	 * Update bKash transactions table and add `refund_status` & `refund_amount` column
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function update_bkash_transactions_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'bkash_transactions';

		// @codingStandardsIgnoreStart
		$wpdb->query(
			"ALTER TABLE `{$table_name}`
            ADD COLUMN `refund_charge` varchar(255) DEFAULT NULL AFTER `refund_amount`,
            ADD COLUMN `refund_id` varchar(255) DEFAULT NULL AFTER `refund_charge`"
		);
		// @codingStandardsIgnoreEnd
	}
}
