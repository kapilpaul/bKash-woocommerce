<?php

/**
 * bkash settings option
 *
 * @param $option
 * @param $section
 *
 * @since 2.0.0
 *
 * @return mixed
 */
function dc_bkash_get_option( $option, $section = 'gateway' ) {
	return dc_bkash()->settings->get_option( $option, $section );
}
