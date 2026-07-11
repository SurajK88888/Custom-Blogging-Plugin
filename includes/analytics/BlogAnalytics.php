<?php
namespace CBP\analytics;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Blog Analytics Class
 */
class BlogAnalytics {

    /**
     * Initialize Blog Analytics
     */
    public static function init() {
        add_action( 'wp', [ __CLASS__, 'track_blog_view' ] );
    }

    /**
     * Track single blog view
     */
    public static function track_blog_view() {
        // Only track if we are on a single CBP blog post
        if ( is_singular( CPT::POST_TYPE ) ) {
            
            // Prevent tracking logged-in admins if needed (optional)
            if ( current_user_can( 'manage_options' ) ) {
                // Uncomment to disable tracking for admins
                // return;
            }

            self::insert_view_record( get_the_ID() );
        }
    }

    /**
     * Insert view record into database
     *
     * @param int $post_id
     */
    private static function insert_view_record( $post_id ) {
        global $wpdb;

        $table = $wpdb->prefix . 'cbp_blog_views';
        
        $ip      = self::get_ip();
        $browser = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Unknown';
        $country = 'Unknown'; // Can be mapped later via an IP geolocation service or GeoIP library.
        $device  = wp_is_mobile() ? 'Mobile' : 'Desktop';

        $wpdb->insert(
            $table,
            [
                'post_id'    => $post_id,
                'visitor_ip' => sanitize_text_field( $ip ),
                'country'    => sanitize_text_field( $country ),
                'device'     => sanitize_text_field( $device ),
                'browser'    => $browser,
                'created_at' => current_time( 'mysql' ),
            ],
            [
                '%d', // post_id
                '%s', // visitor_ip
                '%s', // country
                '%s', // device
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
