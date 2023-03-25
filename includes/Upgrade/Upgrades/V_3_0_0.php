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

	public static function update_bkash_settings_field() {
		$settings_format = dc_bkash()->settings->get_settings();

		error_log( print_r( $settings_format, true ) );

//		update_option( AdminSettings::OPTION_KEY, [ 'gateway' => $gateway_settings ], false );
	}
}
