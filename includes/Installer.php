<?php
/**
 * Class Installer
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash;

/**
 * Class Installer
 */
class Installer {

	/**
	 * Run the installer
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_version();
		$this->create_tables();
	}

	/**
	 * Add time and version on DB
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function add_version() {
		$installed = get_option( 'dc_bkash_installed' );

		if ( ! $installed ) {
			update_option( 'dc_bkash_installed', time() );
		}

		update_option( dc_bkash()->get_db_version_key(), BKASH_VERSION );
	}

	/**
	 * Create necessary database tables
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function create_tables() {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'bkash_transactions';

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `payment_id` varchar(255) DEFAULT NULL,
                  `trx_id` varchar(255) DEFAULT NULL,
                  `transaction_status` varchar(255) DEFAULT NULL,
                  `invoice_number` varchar(255) DEFAULT NULL,
                  `order_number` varchar(15) DEFAULT NULL,
                  `amount` float NOT NULL DEFAULT '0',
                  `verification_status` int(1) NOT NULL DEFAULT 0,
                  `refund_status` int(1) NOT NULL DEFAULT 0,
                  `refund_amount` float NOT NULL DEFAULT '0',
                  `refund_reason` varchar(255) DEFAULT NULL,
                  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;";

		dbDelta( $sql );
	}
}
