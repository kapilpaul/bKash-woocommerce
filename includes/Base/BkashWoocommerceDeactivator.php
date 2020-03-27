<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://kapilpaul.me
 * @since      1.0.0
 *
 * @package    Bkash_Woocommerce
 * @subpackage Bkash_Woocommerce/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Bkash_Woocommerce
 * @subpackage Bkash_Woocommerce/includes
 * @author     Kapil Paul <kapilpaul007@gmail.com>
 */

namespace Inc\Base;

class BkashWoocommerceDeactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}
