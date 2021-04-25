<?php
/**
 * Class AdminNotice
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade
 */

namespace DCoders\Bkash\Upgrade;

/**
 * Class AdminNotice
 */
class AdminNotice {

	/**
	 * Show admin notice to upgrade bKash
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function show_notice() {
		if ( ! current_user_can( 'update_plugins' ) || dc_bkash()->upgrades->has_ongoing_process() ) {
			return;
		}

		if ( ! dc_bkash()->upgrades->is_upgrade_required() ) {
			/**
			 * Fires when upgrade is not required
			 *
			 * @since 3.0.0
			 */
			do_action( 'dc_bkash_upgrade_is_not_required' );

			return;
		}

		wp_enqueue_style( 'dc-upgrade-css' );
		wp_enqueue_script( 'dc-upgrade-script' );

		dc_bkash_get_template( 'admin/upgrade-notice' );
	}
}
