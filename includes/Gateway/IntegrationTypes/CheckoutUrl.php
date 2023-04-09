<?php
/**
 * CheckoutUrl class.
 *
 * Class CheckoutUrl
 *
 * @since 3.0.0
 *
 * @author Kapil Paul
 *
 * @package DCoders\Bkash\Gateway\IntegrationTypes
 */

namespace DCoders\Bkash\Gateway\IntegrationTypes;

use DCoders\Bkash\Abstracts\BkashProcessor;

/**
 * Class CheckoutUrl
 *
 * @package DCoders\Bkash\Gateway\IntegrationTypes
 */
class CheckoutUrl extends BkashProcessor {

	/**
	 * Set url.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function set_urls() {
		$this->base_url = "https://tokenized.{$this->env}.bka.sh/{$this->version}/tokenized/checkout";

		$this->grant_token_url = $this->base_url . '/token/grant';

		$this->payment_query_url  = $this->base_url . '/payment/status';
		$this->payment_search_url = $this->base_url . '/general/searchTransaction';
		$this->refund_payment_url = $this->base_url . '/payment/refund';
	}

	/**
	 * Create payment url.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function payment_create_url() {
		return $this->get_payment_url( 'create' );
	}

	/**
	 * Payment execute Url.
	 *
	 * @param string $payment_id Payment ID.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function payment_execute_url( $payment_id = '' ) {
		return $this->get_payment_url( 'execute' );
	}

	/**
	 * Get Payment url based on type.
	 *
	 * @param string $type Type of payment.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_payment_url( $type ) {
		return "{$this->base_url}/$type";
	}

	/**
	 * Create payment request in bKash.
	 *
	 * @param float   $amount                 Amount.
	 * @param string  $invoice_id             Invoice ID.
	 * @param boolean $calculate_final_amount Final amount calculation.
	 * @param bool    $callback_url           Callback URL.
	 * @param bool    $payer_reference        Payer reference.
	 *
	 * @return \WP_Error|mixed
	 */
	public function create_payment(
		$amount,
		$invoice_id,
		$calculate_final_amount = false,
		$callback_url = false,
		$payer_reference = false
	) {
		try {
			$amount = $calculate_final_amount ? $this->get_final_amount( $amount ) : $amount;

			$payment_data = [
				'mode'                  => '0011',
				'amount'                => $amount,
				'currency'              => 'BDT',
				'intent'                => 'sale',
				'merchantInvoiceNumber' => $invoice_id,
				'payerReference'        => $payer_reference,
				'callbackURL'           => $callback_url,
			];

			return parent::create_payment_request( $payment_data );

		} catch ( \Exception $e ) {
			return new \WP_Error( 'dc_bkash_create_payment_error', $e );
		}
	}

	/**
	 * Execute payment url.
	 *
	 * @param string $payment_id Payment ID.
	 *
	 * @since 3.0.0
	 *
	 * @return bool|mixed|string
	 */
	public function execute_payment( $payment_id ) {
		if ( ! $this->check_test_mode() && ! $this->get_token() ) {
			return false;
		}

		$data = [ 'paymentID' => $payment_id ];

		return parent::execute_payment_request( $payment_id, $data );
	}
}
