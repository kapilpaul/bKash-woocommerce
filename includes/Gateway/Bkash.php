<?php
/**
 * Class Bkash
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Gateway
 */

namespace DCoders\Bkash\Gateway;

/**
 * Class Bkash
 */
class Bkash extends \WC_Payment_Gateway {

	/**
	 * Initialize the gateway/
	 * Bkash constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->init();
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thank_you_page' ) );
		add_action( 'wp_enqueue_scripts', [ $this, 'payment_scripts' ] );
	}

	/**
	 * Init basic settings
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->id                 = 'bkash';
		$this->icon               = false;
		$this->has_fields         = true;
		$this->method_title       = __( 'bKash', 'dc-bkash' );
		$this->method_description = __( 'Pay via bKash payment', 'dc-bkash' );
		$title                    = dc_bkash_get_option( 'title', 'gateway' );
		$this->title              = empty( $title ) ? __( 'bKash', 'dc-bkash' ) : $title;
		$this->description        = dc_bkash_get_option( 'description', 'gateway' );

		// Auto refunds by gateway enabled when API keys available.
		if ( dc_bkash_check_all_api_keys_filled() ) {
			$this->supports[] = 'refunds';
		}
	}

	/**
	 * Process admin options
	 *
	 * @since 2.0.0
	 *
	 * @return bool|void
	 */
	public function admin_options() {
		parent::admin_options();

		$bkash_settings_url = admin_url( 'admin.php?page=dc-bkash#/settings' );

		printf(
			/* translators: %1$d: page number %2$d: max page number */
			esc_html__( '%1$sYou will get %2$s setting options in %3$s here %4$s.%5$s', 'dc-bkash' ),
			'<p>',
			esc_html( $this->method_title ),
			wp_kses_post( sprintf( '<a href="%s">', $bkash_settings_url ) ),
			'</a>',
			'</p>'
		);
	}

	/**
	 * Process the gateway integration
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Empty cart.
		WC()->cart->empty_cart();

		$create_payment_data = $this->create_payment_request( $order );

		if ( is_wp_error( $create_payment_data ) ) {
			$create_payment_data = false;
		}

		return [
			'result'              => 'success',
			'order_number'        => $order_id,
			'amount'              => (float) $order->get_total(),
			'checkout_order_pay'  => $order->get_checkout_payment_url(),
			'redirect'            => $this->get_return_url( $order ),
			'create_payment_data' => $create_payment_data,
		];
	}

	/**
	 * Include payment scripts
	 *
	 * @return void
	 */
	public function payment_scripts() {
		//phpcs:ignore
		if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
			return;
		}

		// if our payment gateway is disabled.
		if ( 'no' === $this->enabled ) {
			return;
		}

		wp_enqueue_style( 'dc-bkash' );

		// Loading this scripts only in checkout page.
		wp_enqueue_script( 'sweetalert' );
		wp_enqueue_script( 'dc-bkash' );

		$this->localize_scripts();
	}

	/**
	 * Localize scripts and passing data
	 *
	 * @return void
	 */
	public function localize_scripts() {
		global $woocommerce;

		$bkash_script_url = dc_bkash()->gateway->processor()->get_script();

		$data = [
			'amount'     => $woocommerce->cart->cart_contents_total,
			'nonce'      => wp_create_nonce( 'dc-bkash-nonce' ),
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'script_url' => $bkash_script_url,
		];

		wp_localize_script( 'dc-bkash', 'dc_bkash', $data );
	}

	/**
	 * Create bKash Payment request
	 *
	 * @param \WC_Order $order Order Object.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
	public function create_payment_request( \WC_Order $order ) {
		$processor = dc_bkash()->gateway->processor();
		$response  = $processor->create_payment( (float) $order->get_total(), $order->get_id() );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return $response;
	}

	/**
	 * Thank you page after order
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function thank_you_page( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			return;
		}

		if ( $this->id === $order->get_payment_method() ) {
			$payment_data = dc_bkash_get_payment( $order_id );

			$trx_id = $payment_data ? $payment_data->trx_id : '';
			$status = $order->needs_payment() ? 'NOT PAID' : 'Completed';

			dc_bkash_get_template(
				'frontend/payment-details',
				[
					'trx_id' => $trx_id,
					'status' => $status,
				]
			);
		}
	}

	/**
	 * Process refund.
	 *
	 * @param int        $order_id Order ID.
	 * @param float|null $amount   Refund amount.
	 * @param string     $reason   Refund reason.
	 *
	 * @since 2.1.0
	 *
	 * @see \DCoders\Bkash\Gateway\Manager::init_refund
	 *
	 * @return boolean|\WP_Error True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		return dc_bkash()->gateway->init_refund( $order_id, $amount, $reason );
	}
}
