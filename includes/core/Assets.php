<?php
namespace CBP\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Assets Loader Class
 */
class Assets {

    /**
     * Initialize the assets hooks
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_frontend_assets' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
    }

    /**
     * Enqueue Frontend Assets
     */
    public static function enqueue_frontend_assets() {
        // Enqueue frontend styles
        wp_enqueue_style(
            'cbp-frontend-style',
            CBP_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            CBP_VERSION
        );

        // Enqueue frontend scripts
        wp_enqueue_script(
            'cbp-frontend-js',
            CBP_PLUGIN_URL . 'assets/js/frontend.js',
            [],
            CBP_VERSION,
            true
        );

        wp_localize_script( 'cbp-frontend-js', 'cbpSettings', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'rest_url' => esc_url_raw( rest_url() ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
        ] );
    }

    /**
     * Enqueue Admin Assets
     */
    public static function enqueue_admin_assets() {
        // Enqueue admin styles
        wp_enqueue_style(
            'cbp-admin-style',
            CBP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            CBP_VERSION
        );

        // Enqueue admin scripts
        wp_enqueue_script(
            'cbp-admin-script',
            CBP_PLUGIN_URL . 'assets/js/admin.js',
            [],
            CBP_VERSION,
            true
        );
    }
}
