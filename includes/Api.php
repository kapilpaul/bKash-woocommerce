<?php

namespace DCoders\Bkash;

/**
 * API Class
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
class API {
    /**
     * Initialize the class
     *
     * @since 2.0.0
     *
     * @return void
     */
    function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_api' ] );
    }

    /**
     * Register the API
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function register_api() {

    }
}
