<?php

/**
 * Insert transaction in table
 *
 * @param $data
 *
 * @return false|int
 */
function insert_bkash_transaction( $data ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$insert = $wpdb->insert( $table_name, [
		"order_number"       => sanitize_text_field( $data['order_number'] ),
		"payment_id"         => sanitize_text_field( $data['payment_id'] ),
		"trx_id"             => sanitize_text_field( $data['trx_id'] ),
		"transaction_status" => sanitize_text_field( $data['transaction_status'] ),
		"invoice_number"     => sanitize_text_field( $data['invoice_number'] ),
		"amount"             => sanitize_text_field( $data['amount'] ),
	] );

	return $insert;
}

/**
 * Get payment form bkash table
 *
 * @param $order_number
 *
 * @return array|object|null
 */
function get_bkash_payment( $order_number ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$query = "SELECT * FROM $table_name WHERE order_number='%d'";

	$item = $wpdb->get_row(
		$wpdb->prepare( $query, $order_number )
	);

	return $item;
}

/**
 * Get all payment list form bkash table
 *
 * @param array $args
 *
 * @return array|object|null
 */
function get_bkash_payments_list( $args = [] ) {
	global $wpdb;

	$defaults = [
		'number'  => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'ASC',
	];

	$args = wp_parse_args( $args, $defaults );

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$query = "SELECT * FROM $table_name";

	if ( isset( $args['search'] ) ) {
		$query .= " WHERE order_number LIKE '%{$args['search']}%' OR WHERE invoice_number LIKE '%{$args['search']}%'";
	}

	$query .= " ORDER BY {$args['orderby']} {$args['order']} LIMIT %d, %d";

	$items = $wpdb->get_results(
		$wpdb->prepare( $query, $args['offset'], $args['number'] )
	);

	return $items;
}

/**
 * Get Count of total payments in DB
 * @return string|null
 */
function get_payments_count() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	return (int) $wpdb->get_var( "SELECT COUNT(id) from $table_name" );
}

/**
 * Delete a payment
 *
 * @param int $id
 *
 * @return int|boolean
 */
function delete_bkash_payment( $id ) {
	global $wpdb;

	return $wpdb->delete(
		$wpdb->prefix . 'bkash_transactions',
		[ 'id' => $id ],
		[ '%d' ]
	);
}

/**
 * delete multiple data from table
 *
 * @param array $ids
 *
 * @return bool|int
 */
function delete_multiple_bkash_payments( array $ids ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bkash_transactions';

	$ids = implode( ',', $ids );
	return $wpdb->query( "DELETE FROM {$table_name} WHERE ID IN($ids)" );
}
