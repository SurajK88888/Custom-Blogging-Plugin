<?php
namespace CBP\logger;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Logger Class
 * 
 * Handles inserting logs into wp_cbp_activity_logs
 */
class Logger {

    /**
     * Log an activity.
     *
     * @param string $action A short action string (e.g., 'blog_published').
     * @param string $description Detailed description of the action.
     * @param int|null $user_id Optional. Defaults to current user.
     * @return int|false The number of rows inserted, or false on error.
     */
    public static function log( $action, $description = '', $user_id = null ) {
        global $wpdb;

        if ( null === $user_id ) {
            $user_id = get_current_user_id();
        }

        $table = $wpdb->prefix . 'cbp_activity_logs';

        $ip      = self::get_ip();
        $browser = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Unknown';

        return $wpdb->insert(
            $table,
            [
                'user_id'     => $user_id,
                'action'      => sanitize_text_field( $action ),
                'description' => wp_kses_post( $description ),
                'ip'          => sanitize_text_field( $ip ),
                'browser'     => $browser,
                'created_at'  => current_time( 'mysql' ),
            ],
            [
                '%d', // user_id
                '%s', // action
                '%s', // description
                '%s', // ip
                '%s', // browser
                '%s', // created_at
            ]
        );
    }

    /**
     * Retrieve the user IP address safely.
     *
     * @return string
     */
    private static function get_ip() {
        $ip = '127.0.0.1';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
