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
function dc_bkash_get_option( $option, $section ) {
	return dcoders_bkash()->settings->get_option( $option, $section );
}
