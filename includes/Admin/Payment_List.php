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

		$this->process_bulk_action();
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

	/**
	 * Set the bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'trash' => __( 'Move to Trash', 'bkash_wc' ),
		);

		return $actions;
	}

	/**
	 * process bulk action
	 *
	 * @return void
	 */
	public function process_bulk_action() {
		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die( 'Are you cheating?' );
			}

		}

		$action = $this->current_action();

		switch ( $action ) {
			case 'trash':
				if ( delete_multiple_bkash_payments( $_POST['payment_id'] ) ) {
					$redirected_to = admin_url( 'admin.php?page=bkash-wc&payment-deleted=true' );
					wp_redirect( $redirected_to );
				}

				break;
			case 'delete':
				if ( delete_bkash_payment( sanitize_text_field( $_GET['id'] ) ) ) {
					$redirected_to = admin_url( 'admin.php?page=bkash-wc&payment-deleted=true' );
					wp_redirect( $redirected_to );
				}

				break;
			default:
				// do nothing or something else
				return;
				break;
		}

		return;
	}

	/**
	 * get column actions
	 *
	 * @param object $item
	 * @param $column_name
	 *
	 * @return string
	 */
	public function get_column_actions( $item, $column_name ) {
		$order = wc_get_order( $item->order_number );

		if ( ! is_object( $order ) ) {
			return sprintf('Invalid!');
		}

		$actions = [];

		$actions['delete'] = sprintf(
			'<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>',
			add_query_arg(
				[
					'id'     => absint( $item->id ),
					'action' => 'delete',
				]
			),
			$item->id,
			__( 'Delete this item', 'bkash_wc' ),
			__( 'Delete', 'bkash_wc' )
		);

		return sprintf(
			'<a href="%1$s"><strong>%2$s</strong></a> %3$s', esc_url_raw( $order->get_edit_order_url() ), $item->$column_name, $this->row_actions( $actions )
		);
	}

	/**
	 * Customize order_number column
	 *
	 * @param object $item
	 *
	 * @return string|void
	 */
	protected function column_order_number( $item ) {
		return $this->get_column_actions( $item, 'order_number' );
	}
}
