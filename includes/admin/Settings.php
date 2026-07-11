<?php
namespace CBP\admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Settings UI Scaffold Class
 * 
 * Registers the admin menu and enqueues isolated admin assets.
 */
class Settings {

    const PAGE_SLUG = 'cbp-settings';

    /**
     * Initialize Admin Hooks
     */
    public static function init() {
        // Menu registration is handled centrally by Dashboard::add_admin_menu()
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    /**
     * Enqueue Admin Assets exclusively on the CBP Settings page.
     * 
     * @param string $hook
     */
    public static function enqueue_admin_assets( $hook ) {
        if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
            return;
        }

        wp_enqueue_style(
            'cbp-admin-css',
            CBP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            CBP_VERSION
        );

        wp_enqueue_script(
            'cbp-admin-js',
            CBP_PLUGIN_URL . 'assets/js/admin.js',
            [ 'jquery' ], // Core WP dependency
            CBP_VERSION,
            true
        );
    }

    /**
     * Render the actual settings page HTML
     */
    public static function render_settings_page() {
        if ( get_transient( 'cbp_settings_import_success' ) ) {
            delete_transient( 'cbp_settings_import_success' );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings successfully imported.', 'custom-blog-pro' ) . '</p></div>';
        }
        
        // We will include our template file here
        $template = CBP_PLUGIN_DIR . 'templates/admin/settings-page.php';
        
        if ( file_exists( $template ) ) {
            include $template;
        } else {
            echo '<div class="wrap"><h1>' . esc_html__( 'Template Missing', 'custom-blog-pro' ) . '</h1></div>';
        }
    }
}
