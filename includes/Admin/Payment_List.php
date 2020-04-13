<?php


namespace Inc\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Payment_List
 * @package Inc\Admin
 */
class Payment_List extends \WP_List_Table {
	/**
	 * Payment_List constructor.
	 */
	public function __construct() {
		parent::__construct( [
			'singular' => 'payment',
			'plural'   => 'payments',
			'ajax'     => false,
		] );
	}

	/**
	 * Get column Names
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'                 => '<input type="checkbox" />',
			'order_number'       => __( 'Order Number', 'bkash_wc' ),
			'amount'             => __( 'Amount', 'bkash_wc' ),
			'payment_id'         => __( 'Payment ID', 'bkash_wc' ),
			'trx_id'             => __( 'Trx ID', 'bkash_wc' ),
			'invoice_number'     => __( 'Invoice Number', 'bkash_wc' ),
			'transaction_status' => __( 'Status', 'bkash_wc' ),
			'created_at'         => __( 'Payment Time', 'bkash_wc' ),
		];
	}

	/**
	 * Prepare Columns Names with pagination
	 *
	 * @param string $search
	 *
	 * @return void
	 */
	public function prepare_items( $search = '' ) {
		$column   = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = [ $column, $hidden, $sortable ];

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$args = [
			'number' => $per_page,
			'offset' => $offset,
		];

		if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {
			$args['orderby'] = $_GET['orderby'];
			$args['order']   = $_GET['order'];
		}

		if ( $search != '' ) {
			$args['search'] = $search;
		}

		$this->items = get_bkash_payments_list( $args );

		$this->set_pagination_args( [
			'total_items' => get_payments_count(),
			'per_page'    => $per_page,
		] );
	}

	/**
	 * Set default columns for table
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'value':
				break;
			default:
				return isset( $item->$column_name ) ? $item->$column_name : '';
		}
	}

	/**
	 * Customize checkbox column
	 *
	 * @param object $item
	 *
	 * @return string|void
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="payment_id[]" value="%d"/>', $item->id
		);
	}

	/**
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = [
			'order_number' => [ 'order_number', true ],
			'amount'       => [ 'amount', true ],
			'created_at'   => [ 'created_at', true ],
		];

		return $sortable_columns;
	}
}
