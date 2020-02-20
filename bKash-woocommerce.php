<?php
/**
 * Plugin Name: Payment Gateway bKash for WC
 * Plugin URI: https://kapilpaul.me/
 * Description: An eCommerce payment method that helps you sell anything. Beautifully.
 * Version: 1.0.0
 * Author: Kapil Paul
 * Author URI: https://kapilpaul.me
 * Text Domain: bKash-wc
 * License: GPLv2 or later
 *
 * @package bKash-woocommerce
 */

/**
 * Copyright (c) 2020 Kapil Paul (email: kapilpaul007@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

use Inc\Base\BkashWoocommerceActivator;
use Inc\Base\BkashWoocommerceDeactivator;
use Inc\bKashWoocommerceGateway;

if (!defined('ABSPATH')) die;

defined('ABSPATH') || exit;

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

final class WC_WP_bKash
{
    /**
     * WC_WP_bKash constructor.
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, array($this, 'active'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'init'));
        add_filter('woocommerce_payment_gateways', array($this, 'register_gateway'));
    }

    /**
     * necessary activations when
     * activate plugin
     */
    public function active()
    {
        BkashWoocommerceActivator::do_install();
    }

    /**
     * deactivation on plugin deactivate
     */
    public function deactivate()
    {
        BkashWoocommerceDeactivator::deactivate();
    }

    /**
     * initialize woocommerce payment gateway
     */
    public function init()
    {
        if (!class_exists('WC_Payment_Gateway')) {
            return;
        }

        new \Inc\Bkash();
    }


    /**
     * Register WooCommerce Payment Gateway
     * @param array $gateways
     * @return array
     */
    public function register_gateway($gateways)
    {
        $gateways[] = new bKashWoocommerceGateway();
        return $gateways;
    }
}

new WC_WP_bKash();
