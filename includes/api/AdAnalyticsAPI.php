<?php
namespace CBP\api;

use CBP\helper\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Ad Analytics REST API
 * 
 * Handles view and click tracking for ads asynchronously.
 */
class AdAnalyticsAPI {

    /**
     * Initialize REST API hooks.
     */
    public static function init() {
        add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
    }

    /**
     * Register REST routes.
     */
    public static function register_routes() {
        register_rest_route( 'cbp/v1', '/ads/track', [
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ __CLASS__, 'handle_tracking' ],
            'permission_callback' => '__return_true', // Public endpoint
            'args'                => [
                'ad_id'   => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
                'post_id' => [ 'required' => true, 'type' => 'integer', 'sanitize_callback' => 'absint' ],
                'action'  => [ 'required' => true, 'type' => 'string', 'enum' => ['view', 'click'] ],
            ]
        ] );
    }

    /**
     * Handle the tracking request.
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public static function handle_tracking( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'cbp_ad_views';

        $ad_id   = $request->get_param( 'ad_id' );
        $post_id = $request->get_param( 'post_id' );
        $action  = $request->get_param( 'action' );

        $ip      = Security::get_client_ip();
        $agent   = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : 'Unknown';
        
        // Basic deduction for device and browser based on User Agent
        $device  = ( preg_match( '/Mobile|Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i', $agent ) ) ? 'Mobile' : 'Desktop';
        $browser = self::get_browser_name( $agent );
        $clicked = ( 'click' === $action ) ? 1 : 0;

        if ( 'view' === $action ) {
            // Check if this IP already viewed this ad on this post recently to prevent spamming stats.
            $exists = $wpdb->get_var( $wpdb->prepare(
                "SELECT id FROM {$table} WHERE ad_id = %s AND post_id = %d AND user_ip = %s AND clicked = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                $ad_id, $post_id, $ip
            ) );

            if ( $exists ) {
                return rest_ensure_response( [ 'success' => true, 'message' => 'View already recorded.' ] );
            }
        }

        $wpdb->insert(
            $table,
            [
                'ad_id'      => $ad_id,
                'post_id'    => $post_id,
                'user_ip'    => $ip,
                'device'     => $device,
                'browser'    => $browser,
                'clicked'    => $clicked,
                'created_at' => current_time( 'mysql' ),
            ],
            [ '%s', '%d', '%s', '%s', '%s', '%d', '%s' ]
        );

        return rest_ensure_response( [ 'success' => true ] );
    }

    /**
     * Deduce browser name from User Agent.
     */
    private static function get_browser_name( $user_agent ) {
        if ( strpos( $user_agent, 'Opera' ) || strpos( $user_agent, 'OPR/' ) ) return 'Opera';
        elseif ( strpos( $user_agent, 'Edge' ) ) return 'Edge';
        elseif ( strpos( $user_agent, 'Chrome' ) ) return 'Chrome';
        elseif ( strpos( $user_agent, 'Safari' ) ) return 'Safari';
        elseif ( strpos( $user_agent, 'Firefox' ) ) return 'Firefox';
        elseif ( strpos( $user_agent, 'MSIE' ) || strpos( $user_agent, 'Trident/7' ) ) return 'Internet Explorer';
        return 'Other';
    }
}
