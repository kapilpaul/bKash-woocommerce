<?php
/**
 * Transaction Charge.
 *
 * @package DC-Bkash
 */

?>

<tr>
	<td class="label"><?php esc_html_e( 'bKash Charge:', 'dc-bkash' ); ?></td>
	<td width="1%"></td>
	<td class="total">
		<?php echo wp_kses_post( $charge_amount ); ?>
	</td>
</tr>
