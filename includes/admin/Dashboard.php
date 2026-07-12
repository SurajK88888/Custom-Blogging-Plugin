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
     * Add the top-level menu and all sub-menu items.
     * The cbp_blog CPT (All Blogs, Add New, Categories, Tags) is automatically
     * nested here because CPT::register_post_type() sets show_in_menu = 'cbp-dashboard'.
     */
    public static function add_admin_menu() {
        // Top-level parent menu
        add_menu_page(
            __( 'Custom Blog Pro Dashboard', 'custom-blog-pro' ),
            __( 'Custom Blog Pro', 'custom-blog-pro' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_dashboard_page' ],
            'dashicons-chart-area',
            58
        );

        // Dashboard sub-menu (mirrors top-level so label can differ)
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'Dashboard', 'custom-blog-pro' ),
            __( 'Dashboard', 'custom-blog-pro' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_dashboard_page' ]
        );

        // --- Blog Management Sub-Menus ---

        // All Blogs - links to the native WP post list for cbp_blog
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'All Blogs', 'custom-blog-pro' ),
            __( 'All Blogs', 'custom-blog-pro' ),
            'edit_posts',
            'edit.php?post_type=cbp_blog'
        );

        // Add New Blog - links to the native WP new post screen for cbp_blog
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'Add New Blog', 'custom-blog-pro' ),
            __( 'Add New Blog', 'custom-blog-pro' ),
            'edit_posts',
            'post-new.php?post_type=cbp_blog'
        );

        // Categories - links to the cbp_category taxonomy management screen
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'Blog Categories', 'custom-blog-pro' ),
            __( 'Categories', 'custom-blog-pro' ),
            'manage_categories',
            'edit-tags.php?taxonomy=cbp_category&post_type=cbp_blog'
        );

        // Tags - links to the cbp_tag taxonomy management screen
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'Blog Tags', 'custom-blog-pro' ),
            __( 'Tags', 'custom-blog-pro' ),
            'manage_categories',
            'edit-tags.php?taxonomy=cbp_tag&post_type=cbp_blog'
        );

        // --- Plugin Configuration Sub-Menus ---

        // Settings sub-menu
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'CBP Settings', 'custom-blog-pro' ),
            __( 'Settings', 'custom-blog-pro' ),
            'manage_options',
            'cbp-settings',
            [ '\CBP\admin\Settings', 'render_settings_page' ]
        );

        // Email Campaigns sub-menu
        add_submenu_page(
            self::PAGE_SLUG,
            __( 'Email Campaigns', 'custom-blog-pro' ),
            __( 'Email Campaigns', 'custom-blog-pro' ),
            'manage_options',
            'cbp-email-campaigns',
            [ '\CBP\admin\EmailCampaigns', 'render_page' ]
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
            // Count pending user submissions waiting for review
            $pending_count = wp_count_posts( \CBP\post\CPT::POST_TYPE )->pending ?? 0;
            include $template;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Template Missing', 'custom-blog-pro' ) . '</h1></div>';
        }
    }
}
