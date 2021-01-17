<?php

namespace DCoders\Bkash\Traits;

/**
 * Error handler trait
 *
 * @since 2.0.0
 *
 * @author Kapil Paul
 */
trait Form_Error {
    /**
     * Holds the errors
     *
     * @since 2.0.0
     *
     * @var array
     */
    public $errors = [];

    /**
     * Check if the form has error
     *
     * @param  string  $key
     *
     * @since 2.0.0
     *
     * @return boolean
     */
    public function has_error( $key ) {
        return isset( $this->errors[ $key ] ) ? true : false;
    }

    /**
     * Get the error by key
     *
     * @param $key
     *
     * @since 2.0.0
     *
     * @return string | false
     */
    public function get_error( $key ) {
        if ( isset( $this->errors[ $key ] ) ) {
            return $this->errors[ $key ];
        }

        return false;
    }
}
