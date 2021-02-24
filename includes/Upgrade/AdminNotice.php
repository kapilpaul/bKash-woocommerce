<?php

namespace DCoders\Bkash\Upgrade;

/**
 * Class AdminNotice
 * @package DCoders\Bkash\Upgrade
 *
 * @author Kapil
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

//        if ( ! dc_bkash()->upgrades->is_upgrade_required() ) {
//            /**
//             * Fires when upgrade is not required
//             *
//             * @since 3.0.0
//             */
//            do_action( 'dc_bkash_upgrade_is_not_required' );
//            return;
//        }

		dc_bkash_get_template( 'admin/upgrade-notice' );
		wp_enqueue_style( 'dc-upgrade-css' );
		wp_enqueue_script( 'dc-upgrade-script' );
	}

	/**
	 * Ajax handler method to initiate Dokan upgrade process
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public static function do_upgrade() {
//        check_ajax_referer( 'dokan_admin' );
//
//        try {
//            if ( ! current_user_can( 'update_plugins' ) ) {
//                throw new DokanException( 'dokan_ajax_upgrade_error', __( 'You are not authorize to perform this operation.', 'dokan-lite' ), 403 );
//            }
//
//            if ( dokan()->upgrades->has_ongoing_process() ) {
//                throw new DokanException( 'dokan_ajax_upgrade_error', __( 'There is an upgrading process going on.', 'dokan-lite' ), 400 );
//            }
//
//            if ( ! dokan()->upgrades->is_upgrade_required() ) {
//                throw new DokanException( 'dokan_ajax_upgrade_error', __( 'Update is not required.', 'dokan-lite' ), 400 );
//            }
//
//            dokan()->upgrades->do_upgrade();
//
//            wp_send_json_success( [ 'success' => true ], 201 );
//        } catch ( Exception $e ) {
//            self::send_response_error( $e );
//        }
	}
}
