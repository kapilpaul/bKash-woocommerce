<?php
/**
 * Class Upgrades
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Upgrade
 */

namespace DCoders\Bkash\Upgrade;

/**
 * Class Upgrades
 */
class Upgrades {

	/**
	 * Bkash upgraders.
	 *
	 * @since 2.0.0
	 *
	 * @var array
	 */
	private static $upgrades
		= [
			'2.0.0' => Upgrades\V_2_0_0::class,
			'2.1.0' => Upgrades\V_2_1_0::class,
		];

	/**
	 * Get DB installed version number
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_db_installed_version() {
		return get_option( dc_bkash()->get_db_version_key(), null );
	}

	/**
	 * Checks if upgrade is required or not
	 *
	 * @param bool $is_required Is required.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_upgrade_required( $is_required = false ) {
		$installed_version = self::get_db_installed_version();
		$upgrade_versions  = array_keys( self::$upgrades );

		if ( $installed_version && version_compare( $installed_version, end( $upgrade_versions ), '<' ) ) {
			return true;
		}

		return $is_required;
	}

	/**
	 * Update bKash DB version
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function update_db_dc_bkash_version() {
		$installed_version = self::get_db_installed_version();

		if ( version_compare( $installed_version, BKASH_VERSION, '<' ) ) {
			update_option( dc_bkash()->get_db_version_key(), BKASH_VERSION );
		}
	}

	/**
	 * Get upgrades
	 *
	 * @param array $upgrades Upgrades array data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public static function get_upgrades( $upgrades = [] ) {
		if ( ! self::is_upgrade_required() ) {
			return $upgrades;
		}

		$installed_version = self::get_db_installed_version();

		foreach ( self::$upgrades as $version => $class_name ) {
			if ( version_compare( $installed_version, $version, '<' ) ) {
				$upgrades[ $version ][] = $class_name;
			}
		}

		return $upgrades;
	}
}
