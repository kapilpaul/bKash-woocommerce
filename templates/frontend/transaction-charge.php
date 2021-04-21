<?php
/**
 * Transaction Charge.
 *
 * @package DC-Bkash
 */

?>

<tr class="cart-discount">
	<th><?php esc_html_e( 'bKash Charge ', 'dc-bkash' ); ?></th>
	<td data-title="<?php esc_html_e( 'bKash Charge', 'dc-bkash' ); ?>"><?php echo wp_kses_post( $charge_amount ); ?></td>
</tr>
