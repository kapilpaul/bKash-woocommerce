<?php
/**
 * Class Manager.
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash
 */

namespace DCoders\Bkash\Gateway;

/**
 * Class Manager
 */
class Manager {
	/**
	 * Hold instance of bKash
	 *
	 * @var Object
	 *
	 * @since 2.0.0
	 */
	public $bkash_instance;

	/**
	 * Manager constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Setup Hooks
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	private function setup_hooks() {
		/**
		 * Actions
		 */
		add_action( 'dc_bkash_execute_payment_success', [ $this, 'after_execute_payment' ], 10, 2 );
		add_action( 'woocommerce_cart_totals_before_order_total', [ $this, 'dc_bkash_display_transaction_charge' ] );
		add_action( 'woocommerce_review_order_before_order_total', [ $this, 'dc_bkash_display_transaction_charge' ] );
		add_action( 'woocommerce_admin_order_totals_after_tax', [ $this, 'dc_bkash_display_transaction_charge_on_admin' ] );
		add_action( 'dc_bkash_after_query_payment', [ $this, 'maybe_update_transaction' ], 10, 3 );

		/**
		 * Filters
		 */
		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_gateway' ] );
		add_filter( 'woocommerce_calculated_total', [ $this, 'dc_bkash_calculate_total' ] );
		add_filter( 'woocommerce_get_order_item_totals', [ $this, 'dc_bkash_get_order_item_totals' ], 10, 3 );

	}

	/**
	 * Add payment class to the container
	 *
	 * @since 2.0.0
	 *
	 * @return Bkash
	 */
	public function bkash() {
		$this->bkash_instance = false;

		if ( ! $this->bkash_instance ) {
			$this->bkash_instance = new Bkash();
		}

		return $this->bkash_instance;
	}

	/**
	 * Register WooCommerce Payment Gateway
	 *
	 * @param array $gateways All Gateways.
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		$gateways[] = $this->bkash();

		return $gateways;
	}

	/**
	 * Get bKash processor class instance
	 *
	 * @since 2.0.0
	 *
	 * @return Processor
	 */
	public function processor() {
		return Processor::get_instance();
	}

	/**
	 * Store data in db after execute payment success.
	 * bKash do not verify the payment id every time.
	 *
	 * @param int|\WC_Order $order           Order ID or order Object.
	 * @param array         $execute_payment Execute Payment data.
	 *
	 * @return mixed
	 */
	public function after_execute_payment( $order, $execute_payment ) {
		if ( ! $order instanceof \WC_Order ) {
			$order = wc_get_order( $order );
		}

		try {
			$payment_id        = sanitize_text_field( $execute_payment['paymentID'] );
			$order_grand_total = (float) $order->get_total();
			$verified          = 0;

			if ( (float) $execute_payment['amount'] === $order_grand_total ) {
				$order->add_order_note(
					sprintf(
						/* translators: %1$s: Transaction ID, %2$s: Grand Total. */
						__( 'bKash payment completed. Transaction ID #%1$s! Amount: %2$s', 'dc-bkash' ),
						$execute_payment['trxID'],
						$order_grand_total
					)
				);

				$order->payment_complete();

			} else {
				$order->update_status(
					'on-hold',
					sprintf(
						/* translators: %1$s: Transaction ID, %2$s: Payment Amount. */
						__( 'Partial payment. Transaction ID #%1$s! Amount: %2$s', 'dc-bkash' ),
						$execute_payment['trxID'],
						$execute_payment['amount']
					)
				);
			}

			$processor    = dc_bkash()->gateway->processor();
			$payment_info = $processor->verify_payment( $payment_id, $order_grand_total );

			if ( ! $payment_info || is_wp_error( $payment_info ) ) {
				$payment_info = $execute_payment;
			} elseif ( isset( $payment_info['transactionStatus'] ) && isset( $payment_info['trxID'] ) ) {
				$verified = 1;
			} else {
				$payment_info = $execute_payment;
			}

			$insert_data = [
				'order_number'        => $order->get_id(),
				'payment_id'          => isset( $payment_info['paymentID'] ) ? $payment_info['paymentID'] : $payment_id,
				'trx_id'              => isset( $payment_info['trxID'] ) ? $payment_info['trxID'] : '',
				'transaction_status'  => isset( $payment_info['transactionStatus'] ) ? $payment_info['transactionStatus'] : '',
				'invoice_number'      => isset( $payment_info['merchantInvoiceNumber'] ) ? $payment_info['merchantInvoiceNumber'] : '',
				'amount'              => isset( $payment_info['amount'] ) ? floatval( $payment_info['amount'] ) : $order_grand_total,
				'verification_status' => $verified,
			];

			dc_bkash_insert_transaction( $insert_data );

			/**
			 * Fires after the execute payment insert
			 */
			do_action( 'dc_bkash_after_execute_payment', $order, $payment_info );

		} catch ( \Exception $e ) {
			error_log( 'dc_bkash after execute payment ' . print_r( $e->getMessage() ) ); //phpcs:ignore
		}
	}

	/**
	 * Bkash calculate total if there is any transaction charge.
	 *
	 * @param float $total Total amount.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function dc_bkash_calculate_total( $total ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $total;
		}

		$payment_method = 'bkash';

		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( $payment_method === $chosen_payment_method ) {
			$processor = dc_bkash()->gateway->processor();

			return $processor->get_final_amount( $total );
		}

		return $total;
	}

	/**
	 * Display the transaction charge.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function dc_bkash_display_transaction_charge() {
		$payment_method        = 'bkash';
		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );

		if ( $payment_method !== $chosen_payment_method ) {
			return;
		}

		$processor = dc_bkash()->gateway->processor();

		$charge_amount = wc_price( $processor->get_transaction_charge_amount( WC()->cart->get_subtotal() ) );

		dc_bkash_get_template(
			'frontend/transaction-charge',
			[
				'charge_amount' => $charge_amount,
			]
		);
	}

	/**
	 * Display the transaction charge on admin panel order details.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function dc_bkash_display_transaction_charge_on_admin( $order_id ) {
		$order = wc_get_order( $order_id );

		$payment_method        = 'bkash';
		$chosen_payment_method = $order->get_payment_method();

		if ( $payment_method !== $chosen_payment_method ) {
			return;
		}

		$processor = dc_bkash()->gateway->processor();

		$charge_amount = wc_price( $processor->get_transaction_charge_amount( $order->get_subtotal() ), [ 'currency' => $order->get_currency() ] );

		dc_bkash_get_template(
			'admin/transaction-charge',
			[
				'charge_amount' => $charge_amount,
			]
		);
	}

	/**
	 * Get order items total and add bkash charge to show in order item details.
	 *
	 * @param array     $total_rows  Total rows.
	 * @param \WC_Order $order       Order Instance.
	 * @param mixed     $tax_display Tax display.
	 *
	 * @return array
	 */
	public function dc_bkash_get_order_item_totals( $total_rows, $order, $tax_display ) {
		$payment_method        = 'bkash';
		$chosen_payment_method = $order->get_payment_method();

		if ( $payment_method !== $chosen_payment_method ) {
			return $total_rows;
		}

		$processor     = dc_bkash()->gateway->processor();
		$charge_amount = wc_price( $processor->get_transaction_charge_amount( $order->get_subtotal() ), [ 'currency' => $order->get_currency() ] );

		$bkash_charge['bkash_charge'] = [
			'label' => __( 'bKash Charge', 'dc-bkash' ),
			'value' => $charge_amount,
		];

		$total_rows = dc_bkash_add_array_after( $total_rows, 'payment_method', $bkash_charge );

		return apply_filters( 'dc_bkash_get_order_item_totals', $total_rows, $this, $tax_display );
	}

	/**
	 * Maybe update transaction data.
	 *
	 * @param string $payment_id     bKash Payment ID.
	 * @param array  $verify_payment Verification payment data.
	 * @param array  $response       response data.
	 *
	 * @return void
	 */
	public function maybe_update_transaction( $payment_id, $verify_payment, $response ) {
		if ( 'Completed' !== $verify_payment['transactionStatus'] ) {
			return;
		}

		$order_number = $verify_payment['merchantInvoiceNumber'];

		$payment = dc_bkash_get_payment( $order_number );

		if ( $payment->verification_status ) {
			return $payment;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'bkash_transactions';

		//phpcs:ignore
		$wpdb->update(
			$table_name,
			[ 'verification_status' => 1 ],
			[ 'order_number' => $order_number ],
			[ '%d' ],
			[ '%s' ]
		);
	}
}
