<?php
namespace CBP\admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Dashboard UI Scaffold Class
 * 
 * Registers the top-level admin menu and enqueues Dashboard assets.
 */
class Dashboard {

    const PAGE_SLUG = 'cbp-dashboard';

    /**
     * Initialize Admin Hooks
     */
    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ], 9 ); // Priority 9 to load before Settings (10)
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_dashboard_assets' ] );
    }

    /**
     * Add the top-level menu item.
     */
    public static function add_admin_menu() {
        add_menu_page(
            __( 'Custom Blog Pro Dashboard', 'custom-blog-pro' ),
            __( 'Custom Blog Pro', 'custom-blog-pro' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_dashboard_page' ],
            'dashicons-chart-area', // Better icon for the dashboard
            58
        );
    }

    /**
     * Enqueue Dashboard Assets exclusively.
     * 
     * @param string $hook
     */
    public static function enqueue_dashboard_assets( $hook ) {
        if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }

        // Chart.js via CDN
        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], '4.4.1', true );

        wp_enqueue_style(
            'cbp-dashboard-css',
            CBP_PLUGIN_URL . 'assets/css/dashboard.css',
            [],
            CBP_VERSION
        );

        wp_enqueue_script(
            'cbp-dashboard-js',
            CBP_PLUGIN_URL . 'assets/js/dashboard.js',
            [ 'jquery', 'chart-js' ],
            CBP_VERSION,
            true
        );

        // Fetch KPIs to pass to the JS for rendering
        $kpis = \CBP\analytics\AnalyticsEngine::get_kpis();
        $device_stats = \CBP\analytics\AnalyticsEngine::get_device_stats();
        $browser_stats = \CBP\analytics\AnalyticsEngine::get_browser_stats();
        
        wp_localize_script( 'cbp-dashboard-js', 'cbpDashboardData', [
            'views'   => $kpis['total_views'] ?? 0,
            'clicks'  => $kpis['ad_clicks'] ?? 0,
            'emails'  => $kpis['total_emails'] ?? 0,
            'devices' => $device_stats,
            'browsers'=> $browser_stats,
        ] );
    }

    /**
     * Render the actual dashboard HTML
     */
    public static function render_dashboard_page() {
        $template = CBP_PLUGIN_DIR . 'templates/admin/dashboard-page.php';
        
        if ( file_exists( $template ) ) {
            // Provide KPIs to the template
            $kpis = \CBP\analytics\AnalyticsEngine::get_kpis();
            $popular_posts = \CBP\analytics\AnalyticsEngine::get_popular_posts();
            include $template;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Template Missing', 'custom-blog-pro' ) . '</h1></div>';
        }
    }
}
