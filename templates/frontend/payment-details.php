<?php
/**
 * Payment Details.
 *
 * @package DC-Bkash
 */

?>

<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
	<?php if ( isset( $trx_id ) ) : ?>
		<li class="woocommerce-order-overview__payment-method method">
			<?php esc_html_e( 'bKash Transaction ID:', 'dc-bkash' ); ?>
			<strong><?php echo esc_html( $trx_id ); ?></strong>
		</li>
	<?php endif; ?>

	<li class="woocommerce-order-overview__payment-method method">
		<?php esc_html_e( 'Payment Status:', 'dc-bkash' ); ?>
		<strong><?php echo esc_html( strtoupper( $status ) ); ?></strong>
	</li>
</ul>
