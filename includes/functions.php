<?php
/**
 * DC bKash functions.
 *
 * @package DCoders\Bkash
 */

/**
 * Bkash settings option.
 *
 * @param string $option  Option from settings.
 * @param string $section Settings Section.
 *
 * @since 2.0.0
 *
 * @return mixed
 */
function dc_bkash_get_option( $option, $section = 'gateway' ) {
	$installed_version = get_option( dc_bkash()->get_db_version_key(), null );

	// doing this for backward compatibility.
	if ( $installed_version && version_compare( $installed_version, '1.3.0', '<=' ) ) {
		$payment_settings = get_option( 'woocommerce_bkash_settings', [] );

		// replace sandbox_ with blank string. we do not have any 'sandbox_' prefix in older version.
		$option = str_replace( 'sandbox_', '', $option );

		if ( array_key_exists( $option, $payment_settings ) ) {
			return $payment_settings[ $option ];
		}

		return null;
	}

	return dc_bkash()->settings->get_option( $option, $section );
}

/**
 * Insert transaction in table.
 *
 * @param array $data Data to insert.
 *
 * @since 2.0.0
 *
 * @return false|int
 */
function dc_bkash_insert_transaction( $data ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$data = apply_filters( 'dc_bkash_before_insert_transaction', $data );

	//phpcs:ignore
	$insert = $wpdb->insert(
		$table_name,
		[
			'order_number'        => sanitize_text_field( $data['order_number'] ),
			'payment_id'          => sanitize_text_field( $data['payment_id'] ),
			'trx_id'              => sanitize_text_field( $data['trx_id'] ),
			'transaction_status'  => sanitize_text_field( $data['transaction_status'] ),
			'invoice_number'      => sanitize_text_field( $data['invoice_number'] ),
			'amount'              => sanitize_text_field( $data['amount'] ),
			'verification_status' => sanitize_key( $data['verification_status'] ),
		]
	);

	if ( is_wp_error( $insert ) ) {
		return $insert;
	}

	return $insert;
}

/**
 * Get payment form bkash table.
 *
 * @param int $order_number Order Number.
 *
 * @since 2.0.0
 *
 * @return array|object|null
 */
function dc_bkash_get_payment( $order_number ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$query = sprintf( "SELECT * FROM %s WHERE order_number='%d'", $table_name, $order_number );

	//phpcs:ignore
	$item = $wpdb->get_row( $query );

	return $item;
}

/**
 * Get all payment list form bkash table
 *
 * @param array $args Arguments.
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
		'order'   => 'DESC',
	];

	$args = wp_parse_args( $args, $defaults );

	$table_name = $wpdb->prefix . 'bkash_transactions';

	$query = sprintf( 'SELECT * FROM %s', $table_name );

	if ( isset( $args['search'] ) ) {
		$wild = '%';
		$like = $wild . $wpdb->esc_like( $args['search'] ) . $wild;

		$query .= sprintf( ' WHERE order_number LIKE "%1$s" OR invoice_number LIKE "%2$s" OR trx_id LIKE "%3$s" OR payment_id LIKE "%4$s"', $like, $like, $like, $like );
	}

	$query .= sprintf( ' ORDER BY `%1$s` %2$s LIMIT %3$d, %4$d', $args['orderby'], $args['order'], $args['offset'], $args['number'] );

	//phpcs:ignore
	$items = $wpdb->get_results( $query );

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

	//phpcs:ignore
	return (int) $wpdb->get_var( "SELECT COUNT(id) from $table_name" );
}

/**
 * Update transaction.
 *
 * @param int   $order_number Order number.
 * @param array $data         Data to update (in column => value pairs).
 *                            Both $data columns and $data values should be "raw" (neither should be SQL escaped).
 *                            Sending a null value will cause the column to be set to NULL - the corresponding
 *                            format is ignored in this case.
 * @param array $format       Format of the inserted data.
 *
 * @since 2.1.0
 *
 * @return false|int
 */
function dc_bkash_update_transaction( $order_number, array $data, array $format ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'bkash_transactions';

	//phpcs:ignore
	return $wpdb->update(
		$table_name,
		$data,
		[ 'order_number' => $order_number ],
		$format,
		[ '%s' ]
	);
}

/**
 * Delete a payment
 *
 * @param int $id ID.
 *
 * @since 2.0.0
 *
 * @return int|boolean
 */
function dc_bkash_delete_payment( $id ) {
	global $wpdb;

	//phpcs:ignore
	return $wpdb->delete(
		$wpdb->prefix . 'bkash_transactions',
		[ 'id' => $id ],
		[ '%d' ]
	);
}

/**
 * Delete multiple data from table
 *
 * @param array $ids Multiple IDs.
 *
 * @since 2.0.0
 *
 * @return bool|int
 */
function dc_bkash_delete_multiple_payments( array $ids ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'bkash_transactions';

	$ids = implode( ',', $ids );

	//phpcs:ignore
	return $wpdb->query( "DELETE FROM {$table_name} WHERE ID IN($ids)" );
}

/**
 * Insert bKash transaction.
 *
 * @deprecated from 2.0.0
 *
 * @use dc_bkash_insert_transaction() instead
 *
 * @param array $data Data to insert.
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
 * @param int $order_number Order number.
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
 * @param array $args Arguments.
 *
 * @return array|object|null
 */
function get_bkash_payments_list( $args = [] ) {
	_deprecated_function( 'get_bkash_payments_list', '2.0.0', 'dc_bkash_get_payments_list' );

	return dc_bkash_get_payments_list( $args );
}

/**
 * Get Count of total payments in DB.
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
 * @param int $id ID.
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
 * @param array $ids IDs.
 *
 * @return bool|int
 */
function delete_multiple_bkash_payments( array $ids ) {
	_deprecated_function( 'delete_multiple_bkash_payments', '2.0.0', 'dc_bkash_delete_multiple_payments' );

	return dc_bkash_delete_multiple_payments( $ids );
}

/**
 * Get template part implementation.
 *
 * Looks at the theme directory first.
 *
 * @param string $slug Slug of template.
 * @param string $name Name of template.
 * @param array  $args Arguments to passed.
 *
 * @since 2.0.0
 *
 * @return void
 */
function dc_bkash_get_template_part( $slug, $name = '', $args = [] ) {
	$defaults = [ 'pro' => false ];

	$args = wp_parse_args( $args, $defaults );

	if ( $args && is_array( $args ) ) {
		extract( $args ); //phpcs:ignore
	}

	$template = '';

	// Look in yourtheme/bkash/slug-name.php and yourtheme/bkash/slug.php.
	$template = locate_template(
		[
			BKASH_TEMPLATE_PATH . "{$slug}-{$name}.php",
			BKASH_TEMPLATE_PATH . "{$slug}.php",
		]
	);

	/**
	 * Change template directory path filter.
	 *
	 * @since 2.0.0
	 */
	$template_path = apply_filters( 'dc_bkash_set_template_path', BKASH_TEMPLATE_PATH, $template, $args );

	// Get default slug-name.php.
	if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
		$template = $template_path . "/{$slug}-{$name}.php";
	}

	if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
		$template = $template_path . "/{$slug}.php";
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$template = apply_filters( 'dc_bkash_get_template_part', $template, $slug, $name );

	if ( $template ) {
		include $template;
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param mixed  $template_name Template Name.
 * @param array  $args          (default: array()) arguments.
 * @param string $template_path (default: '').
 * @param string $default_path  (default: '').
 *
 * @since 2.0.0
 *
 * @return void
 */
function dc_bkash_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args ); //phpcs:ignore
	}

	$extension = get_extension( $template_name ) ? '' : '.php';

	$located = dc_bkash_locate_template( $template_name . $extension, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), '2.1' );

		return;
	}

	do_action( 'dc_bkash_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'dc_bkash_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @param mixed  $template_name Template name.
 * @param string $template_path (default: '').
 * @param string $default_path  (default: '').
 *
 * @since 2.0.0
 *
 * @return string
 */
function dc_bkash_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = BKASH_TEMPLATE_PATH;
	}

	if ( ! $default_path ) {
		$default_path = BKASH_TEMPLATE_PATH;
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		[
			trailingslashit( $template_path ) . $template_name,
		]
	);

	// Get default template.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'dc_bkash_locate_template', $template, $template_name, $template_path );
}

/**
 * Get filename extension.
 *
 * @param string $file_name File name.
 *
 * @since 2.0.0
 *
 * @return false|string
 */
function get_extension( $file_name ) {
	$n = strrpos( $file_name, '.' );

	return ( false === $n ) ? '' : substr( $file_name, $n + 1 );
}

/**
 * Add item in specific position of an array
 *
 * @param array      $array Array.
 * @param int|string $position  <index position or name of the key after which you want to add the new array>.
 * @param array      $new_array New array to add.
 *
 * @since 2.0.0
 *
 * @return array
 */
function dc_bkash_add_array_after( $array, $position, $new_array ) {
	if ( is_int( $position ) ) {
		return array_merge(
			array_slice( $array, 0, $position ),
			$new_array,
			array_slice( $array, $position )
		);
	}

	$pos = array_search( $position, array_keys( $array ), true );

	return array_merge(
		array_slice( $array, 0, $pos + 1 ),
		$new_array,
		array_slice( $array, $pos )
	);
}

/**
 * Check all credentials are filled.
 *
 * @since 2.1.0
 *
 * @return bool
 */
function dc_bkash_check_all_api_keys_filled() {
	$processor = \DCoders\Bkash\Gateway\Processor::get_instance();
	$prefix    = $processor->get_test_mode_type( 'with_key' ) ? 'sandbox_' : '';

	$data = [
		'app_key'    => dc_bkash_get_option( $prefix . 'app_key' ),
		'app_secret' => dc_bkash_get_option( $prefix . 'app_secret' ),
		'user_name'  => dc_bkash_get_option( $prefix . 'username' ),
		'password'   => dc_bkash_get_option( $prefix . 'password' ),
	];

	foreach ( $data as $key ) {
		if ( empty( $key ) ) {
			return false;
		}
	}

	return true;
}
