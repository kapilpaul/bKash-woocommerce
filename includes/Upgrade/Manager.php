<?php
/**
 * Class Manager
 *
 * @since 2.0.0
 *
 * @author Kapil
 *
 * @package DCoders\Bkash\Upgrade
 */

namespace DCoders\Bkash\Upgrade;

/**
 * Class Manager
 */
class Manager {

	/**
	 * Upgrading DB key.
	 *
	 * @var string
	 */
	private $is_upgrading_db_key = 'dc_bkash_is_upgrading';

	/**
	 * Manager constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_notices', [ AdminNotice::class, 'show_notice' ] );
	}

	/**
	 * Checks if update is required or not
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function is_upgrade_required() {
		return Upgrades::is_upgrade_required();
	}

	/**
	 * Checks for any ongoing process
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function has_ongoing_process() {
		return ! ! get_option( $this->is_upgrading_db_key, false );
	}

	/**
	 * Get upgradable upgrades
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_upgrades() {
		$upgrades = get_option( $this->is_upgrading_db_key, null );

		if ( ! empty( $upgrades ) ) {
			return $upgrades;
		}

		/**
		 * Filter upgrades
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		$upgrades = apply_filters( 'dc_bkash_upgrade_upgrades', Upgrades::get_upgrades() );

		uksort(
			$upgrades,
			function ( $a, $b ) {
				return version_compare( $b, $a, '<' );
			}
		);

		update_option( $this->is_upgrading_db_key, $upgrades, false );

		return $upgrades;
	}

	/**
	 * Run upgrades
	 *
	 * This will execute every method found in a upgrader class, execute `run` method defined
	 * in `DcBkashUpgrader` abstract class and then finally, `update_db_version` will update the db version
	 * reference in database.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function do_upgrade() {
		$upgrades = $this->get_upgrades();

		foreach ( $upgrades as $version => $upgraders ) {
			foreach ( $upgraders as $upgrader ) {
				$required_version = null;

				if ( is_array( $upgrader ) ) {
					$required_version = $upgrader['require'];
					$upgrader         = $upgrader['upgrader'];
				}

				call_user_func( [ $upgrader, 'run' ], $required_version );
				call_user_func( [ $upgrader, 'update_db_version' ] );
			}
		}

		delete_option( $this->is_upgrading_db_key );

		/**
		 * Fires after finish the upgrading
		 *
		 * At this point plugin should update the
		 * db version key to version constant like BKASH_VERSION
		 *
		 * @since 2.0.0
		 */
		do_action( 'dc_bkash_upgrade_finished' );
	}
}

