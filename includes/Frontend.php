<?php

namespace DCoders\Bkash;

/**
 * Frontend handler class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
class Frontend {
    /**
     * Frontend constructor.
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function __construct() {
        new Frontend\Shortcode();
    }
}
