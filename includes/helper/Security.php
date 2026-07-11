<?php
namespace CBP\helper;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Security Helper Class
 */
class Security {

    /**
     * Verify a nonce.
     *
     * @param string $nonce_name The name of the nonce.
     * @param string $action The nonce action.
     * @return bool True if valid, false otherwise.
     */
    public static function verify_nonce( $nonce_name, $action ) {
        if ( ! isset( $_REQUEST[ $nonce_name ] ) ) {
            return false;
        }

        return wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_name ] ) ), $action );
    }

    /**
     * Check if the current user has a specific capability.
     *
     * @param string $capability The capability to check.
     * @return bool
     */
    public static function current_user_can( $capability ) {
        return current_user_can( $capability );
    }

    /**
     * Sanitize a string input safely.
     *
     * @param string $input
     * @return string
     */
    public static function sanitize_text( $input ) {
        return sanitize_text_field( $input );
    }
}
