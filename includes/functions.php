<?php

/**
 * bkash settings option
 *
 * @param $option
 * @param $section
 *
 * @since 2.0.0
 *
 * @return mixed
 */
function dc_bkash_get_option( $option, $section = 'gateway' ) {
	return dc_bkash()->settings->get_option( $option, $section );
}

/**
 * Insert transaction in table
 *
 * @param $data
 *
 * @since 2.0.0
 *
 * @return false|int
 */
function dc_bkash_insert_transaction( $data ) {
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

	if ( is_wp_error( $insert ) ) {
		return $insert;
	}

	return $insert;
}

/**
 * Get payment form bkash table
 *
 * @param $order_number
 *
 * @since 2.0.0
 *
 * @return array|object|null
 */
function dc_bkash_get_payment( $order_number ) {
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
 * @since 2.0.0
 *
 * @return array|object|null
 */
function dc_bkash_get_payments_list( $args = [] ) {
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
 *
 * @since 2.0.0
 *
 * @return string|null
 */
function dc_bkash_get_payments_count() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	return (int) $wpdb->get_var( "SELECT COUNT(id) from $table_name" );
}

/**
 * Delete a payment
 *
 * @param int $id
 *
 * @since 2.0.0
 *
 * @return int|boolean
 */
function dc_bkash_delete_payment( $id ) {
	global $wpdb;

	return $wpdb->delete(
		$wpdb->prefix . 'bkash_transactions',
		[ 'id' => $id ],
		[ '%d' ]
	);
}

/**
 * Delete multiple data from table
 *
 * @param array $ids
 *
 * @since 2.0.0
 *
 * @return bool|int
 */
function dc_bkash_delete_multiple_payments( array $ids ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bkash_transactions';

	$ids = implode( ',', $ids );

	return $wpdb->query( "DELETE FROM {$table_name} WHERE ID IN($ids)" );
}

/**
 * Insert bKash transaction
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_insert_transaction() instead
 *
 * @param $data
 *
 * @return false|int
 */
function insert_bkash_transaction( $data ) {
	_deprecated_function( 'insert_bkash_transaction', '2.0.0', 'dc_bkash_insert_transaction' );

	return dc_bkash_insert_transaction( $data );
}

/**
 * Get payment form bkash table
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_get_payment() instead
 *
 * @param $order_number
 *
 * @return array|object|null
 */
function get_bkash_payment( $order_number ) {
	_deprecated_function( 'get_bkash_payment', '2.0.0', 'dc_bkash_get_payment' );

	return dc_bkash_get_payment( $order_number );
}

/**
 * Get all payment list form bkash table
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_get_payments_list() instead
 *
 * @param array $args
 *
 * @return array|object|null
 */
function get_bkash_payments_list( $args = [] ) {
	_deprecated_function( 'get_bkash_payments_list', '2.0.0', 'dc_bkash_get_payments_list' );

	return dc_bkash_get_payments_list( $args );
}

/**
 * Get Count of total payments in DB
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_get_payments_count() instead
 *
 * @return string|null
 */
function get_payments_count() {
	_deprecated_function( 'get_payments_count', '2.0.0', 'dc_bkash_get_payments_count' );

	return dc_bkash_get_payments_count();
}

/**
 * Delete a payment
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_delete_payment() instead
 *
 * @param int $id
 *
 * @return int|boolean
 */
function delete_bkash_payment( $id ) {
	_deprecated_function( 'delete_bkash_payment', '2.0.0', 'dc_bkash_delete_payment' );

	return dc_bkash_delete_payment( $id );
}

/**
 * Delete multiple data from table
 *
 * @deprecated from 2.0.0
 *
 * @use delete_multiple_bkash_payments() instead
 *
 * @param array $ids
 *
 * @return bool|int
 */
function delete_multiple_bkash_payments( array $ids ) {
	_deprecated_function( 'delete_multiple_bkash_payments', '2.0.0', 'dc_bkash_delete_multiple_payments' );

	return dc_bkash_delete_multiple_payments( $ids );
}
