<?php
/**
 * Payment Details.
 *
 * @package DC-Bkash
 */

?>

<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
	<?php if ( isset( $payment_data ) ) : ?>
		<?php foreach ( $payment_data as $single_payment ) : ?>
		<li class="woocommerce-order-overview__payment-method method">
			<?php esc_html_e( 'bKash Transaction ID:', 'dc-bkash' ); ?>
			<strong><?php echo esc_html( $single_payment->trx_id ); ?></strong>

			<?php esc_html_e( 'Amount:', 'dc-bkash' ); ?>
			<strong><?php echo esc_html( $single_payment->amount ); ?></strong>
		</li>
		<?php endforeach; ?>
	<?php endif; ?>

	<li class="woocommerce-order-overview__payment-method method">
		<?php esc_html_e( 'Payment Status:', 'dc-bkash' ); ?>
		<strong><?php echo esc_html( strtoupper( $status ) ); ?></strong>
	</li>
</ul>
