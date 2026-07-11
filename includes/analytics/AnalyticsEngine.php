<?php
namespace CBP\analytics;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Analytics Engine
 * 
 * Handles querying and caching data for the Admin Dashboard.
 */
class AnalyticsEngine {

    const CACHE_TIME = 15 * MINUTE_IN_SECONDS; // Cache for 15 minutes

    /**
     * Get aggregate KPIs.
     * 
     * @return array
     */
    public static function get_kpis() {
        $cache_key = 'cbp_dashboard_kpis';
        $kpis = get_transient( $cache_key );

        if ( false === $kpis ) {
            global $wpdb;

            // Totals
            $total_blogs = wp_count_posts( \CBP\post\CPT::POST_TYPE )->publish ?? 0;
            
            $views_table = $wpdb->prefix . 'cbp_blog_views';
            $total_views = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$views_table}" );

            $ads_table = $wpdb->prefix . 'cbp_ad_views';
            $ad_stats = $wpdb->get_row( "SELECT COUNT(id) as total_views, SUM(clicked) as total_clicks FROM {$ads_table}", ARRAY_A );
            $ad_views = (int) ($ad_stats['total_views'] ?? 0);
            $ad_clicks = (int) ($ad_stats['total_clicks'] ?? 0);
            $ad_ctr = ( $ad_views > 0 ) ? round( ($ad_clicks / $ad_views) * 100, 2 ) : 0;

            $email_table = $wpdb->prefix . 'cbp_email_logs';
            $total_emails = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$email_table} WHERE status = 'sent'" );

            $kpis = [
                'total_blogs'  => $total_blogs,
                'total_views'  => $total_views,
                'ad_views'     => $ad_views,
                'ad_clicks'    => $ad_clicks,
                'ad_ctr'       => $ad_ctr,
                'total_emails' => $total_emails,
            ];

            set_transient( $cache_key, $kpis, self::CACHE_TIME );
        }

        return $kpis;
    }

    /**
     * Get Popular Posts based on views.
     * 
     * @param int $limit
     * @return array
     */
    public static function get_popular_posts( $limit = 5 ) {
        $cache_key = 'cbp_popular_posts_' . $limit;
        $posts = get_transient( $cache_key );

        if ( false === $posts ) {
            global $wpdb;
            $views_table = $wpdb->prefix . 'cbp_blog_views';
            $posts_table = $wpdb->posts;

            // Join views with posts to get titles, ordering by view count
            $sql = $wpdb->prepare(
                "SELECT p.ID, p.post_title, COUNT(v.id) as views 
                 FROM {$posts_table} p 
                 LEFT JOIN {$views_table} v ON p.ID = v.post_id 
                 WHERE p.post_type = %s AND p.post_status = 'publish'
                 GROUP BY p.ID 
                 ORDER BY views DESC 
                 LIMIT %d",
                \CBP\post\CPT::POST_TYPE,
                $limit
            );

            $posts = $wpdb->get_results( $sql, ARRAY_A );
            set_transient( $cache_key, $posts, self::CACHE_TIME );
        }

        return $posts ? $posts : [];
    }

    /**
     * Get Blog Views by Device.
     * 
     * @return array
     */
    public static function get_device_stats() {
        $cache_key = 'cbp_device_stats';
        $stats = get_transient( $cache_key );

        if ( false === $stats ) {
            global $wpdb;
            $table = $wpdb->prefix . 'cbp_blog_views';
            $sql = "SELECT device, COUNT(id) as views FROM {$table} GROUP BY device ORDER BY views DESC";
            $stats = $wpdb->get_results( $sql, ARRAY_A );
            set_transient( $cache_key, $stats, self::CACHE_TIME );
        }

        return $stats ? $stats : [];
    }

    /**
     * Get Blog Views by Browser.
     * 
     * @return array
     */
    public static function get_browser_stats() {
        $cache_key = 'cbp_browser_stats';
        $stats = get_transient( $cache_key );

        if ( false === $stats ) {
            global $wpdb;
            $table = $wpdb->prefix . 'cbp_blog_views';
            $sql = "SELECT browser, COUNT(id) as views FROM {$table} GROUP BY browser ORDER BY views DESC";
            $stats = $wpdb->get_results( $sql, ARRAY_A );
            set_transient( $cache_key, $stats, self::CACHE_TIME );
        }

        return $stats ? $stats : [];
    }

    /**
     * Clear all analytics transients.
     * Call this when settings change or a forced refresh is needed.
     */
    public static function clear_cache() {
        delete_transient( 'cbp_dashboard_kpis' );
        delete_transient( 'cbp_popular_posts_5' );
        delete_transient( 'cbp_device_stats' );
        delete_transient( 'cbp_browser_stats' );
    }
}
