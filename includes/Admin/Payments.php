<?php

namespace Inc\Admin;

/**
 * Class Payments
 * @package Inc\Admin
 */
class Payments {
	/**
	 * @var bool
	 */
	public static $instance = false;

	/**
	 * @return bool|Payments
	 */
	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new Payments();
		}

		return self::$instance;
	}

	/**
	 * @return void
	 */
	public function plugin_page() {
		$template = __DIR__ . '/views/payment-list.php';

		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	/**
	 * Get all payment list form bkash table
	 *
	 * @param array $args
	 *
	 * @return array|object|null
	 */
	public function get_bkash_payments_list( $args = [] ) {
		global $wpdb;

		$defaults = [
			'number'  => 20,
			'offset'  => 0,
			'orderby' => 'id',
			'order'   => 'ASC',
		];

		$args = wp_parse_args( $args, $defaults );

		$query = "SELECT * FROM {$wpdb->prefix}bkash_transactions";

		if ( isset( $args['search'] ) ) {
			$query .= " WHERE trx_id LIKE '%{$args['search']}%'";
		}

		$query .= " ORDER BY {$args['orderby']} {$args['order']}
					LIMIT %d, %d";

		$items = $wpdb->get_results(
			$wpdb->prepare( $query, $args['offset'], $args['number'] )
		);

		return $items;
	}

	/**
	 * Get payment form bkash table
	 *
	 * @param $order_number
	 *
	 * @return array|object|null
	 */
	public function get_bkash_payment( $order_number ) {
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}bkash_transactions WHERE order_number='%d'";

		$item = $wpdb->get_row(
			$wpdb->prepare( $query, $order_number )
		);

		return $item;
	}

	/**
	 * Get Count of total payments in DB
	 * @return string|null
	 */
	public function get_payments_count() {
		global $wpdb;

		return (int) $wpdb->get_var( "SELECT COUNT(id) from {$wpdb->prefix}bkash_transactions" );
	}
}

/**
 * get_bkash_payments_list
 *
 * @param array $args
 *
 * @return array|object|null
 */
function get_bkash_payments_list( $args = [] ) {
	$payments = Payments::init();

	return $payments->get_bkash_payments_list( $args );
}

/**
 * get_bkash_payment_by_id
 *
 * @param $order_number
 *
 * @return array|object|null
 */
function get_bkash_payment( $order_number ) {
	$payments = Payments::init();

	return $payments->get_bkash_payment( $order_number );
}

/**
 * get_payments_count
 * @return string|null
 */
function get_payments_count() {
	$payments = Payments::init();

	return $payments->get_payments_count();
}
