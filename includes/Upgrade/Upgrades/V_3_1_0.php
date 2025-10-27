<?php
/**
 * Class V_3_1_0
 *
 * @since 3.1.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade\Upgrades
 */

namespace DCoders\Bkash\Upgrade\Upgrades;

use DCoders\Bkash\Abstracts\DcBkashUpgrader;
use DCoders\Bkash\Admin\Settings as AdminSettings;

/**
 * Class V_3_1_0
 */
class V_3_1_0 extends DcBkashUpgrader {

	/**
	 * Update bKash transactions table name.
	 *
	 * @since 3.1.0
	 *
	 * @return void
	 */
	public static function update_bkash_transactions_table() {
		global $wpdb;

		$table_name     = $wpdb->prefix . 'bkash_transactions';
		$new_table_name = $wpdb->prefix . 'dc_bkash_transactions';

		// @codingStandardsIgnoreStart
		$wpdb->query(
			"ALTER TABLE `{$table_name}` RENAME TO `{$new_table_name}`"
		);
		// @codingStandardsIgnoreEnd
	}
}
