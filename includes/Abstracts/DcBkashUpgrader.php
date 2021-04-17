<?php
/**
 * DC Bkash Upgrader abstract class.
 *
 * @package DCoders\Bkash\Abstracts
 */

namespace DCoders\Bkash\Abstracts;

use ReflectionClass;

/**
 * Class DcBkashUpgrader
 *
 * @package DCoders\Bkash\Abstracts
 *
 * @author Kapil Paul
 */
abstract class DcBkashUpgrader {
	/**
	 * Execute upgrader class methods
	 *
	 * This method will execute every method found in child upgrader class dynamically. Keep in mind that methods
	 * should be public static function.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public static function run() {
		$methods = get_class_methods( static::class );

		foreach ( $methods as $method ) {
			if ( 'run' !== $method && 'update_db_version' !== $method ) {
				call_user_func( [ static::class, $method ] );
			}
		}
	}

	/**
	 * Update the DB version
	 *
	 * Upgrader files should follow naming convention
	 * as V_XX_XX_XX.php where Xs are number following
	 * semvar convention. For example if you have a upgrader
	 * for version 1.23.40, the the filename should be
	 * V_1_23_40.php.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 * @throws \ReflectionException Throw Reflection Exception.
	 */
	public static function update_db_version() {
		$reflect    = new ReflectionClass( static::class );
		$base_class = $reflect->getShortName();
		$version    = str_replace( [ 'V_', '_' ], [ '', '.' ], $base_class );

		update_option( dc_bkash()->get_db_version_key(), $version );
	}
}
