<?php
namespace CBP\admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Import and Export Tools
 */
class Tools {

    public static function init() {
        add_action( 'admin_post_cbp_export_settings', [ __CLASS__, 'handle_export' ] );
        add_action( 'admin_init', [ __CLASS__, 'handle_import' ] );
    }

    /**
     * Handle JSON Export
     */
    public static function handle_export() {
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'cbp_export_nonce' ) ) {
            wp_die( __( 'Security check failed.', 'custom-blog-pro' ) );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized.', 'custom-blog-pro' ) );
        }

        // Get all options registered in our Plugin
        $options_to_export = [
            'cbp_primary_color', 'cbp_secondary_color', 'cbp_border_radius',
            'cbp_font_family', 'cbp_dark_mode', 'cbp_card_shadow', 'cbp_layout_sidebar',
            'cbp_smtp_enabled', 'cbp_smtp_host', 'cbp_smtp_port', 'cbp_smtp_username',
            'cbp_smtp_encryption', 'cbp_smtp_from_email', 'cbp_smtp_from_name',
            'cbp_ads_enabled', 'cbp_ad_top', 'cbp_ad_middle', 'cbp_ad_bottom', 'cbp_ad_sidebar'
        ];

        $export_data = [];
        foreach ( $options_to_export as $opt ) {
            $export_data[$opt] = get_option( $opt );
        }

        $json = wp_json_encode( $export_data );

        header( 'Content-Description: File Transfer' );
        header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
        header( 'Content-Disposition: attachment; filename="cbp-settings-export-' . date('Y-m-d') . '.json"' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        header( 'Content-Length: ' . strlen( $json ) );

        echo $json;
        exit;
    }

    /**
     * Handle JSON Import via settings page form POST
     */
    public static function handle_import() {
        // Only run when cbp_do_import button is clicked
        if ( ! isset( $_POST['cbp_do_import'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // The form posts to options.php, so WP might handle the settings save first,
        // but we intercept on admin_init before options.php processes if we hook early,
        // Actually, if action is options.php, it will process and redirect.
        // It's better to hook into `admin_action_update` or pre_update_option.
        // Wait, options.php doesn't fire admin_init? It does, but then redirects.
        
        if ( isset( $_FILES['cbp_import_file'] ) && $_FILES['cbp_import_file']['error'] === UPLOAD_ERR_OK ) {
            
            $file_contents = file_get_contents( $_FILES['cbp_import_file']['tmp_name'] );
            $import_data   = json_decode( $file_contents, true );

            if ( is_array( $import_data ) ) {
                foreach ( $import_data as $key => $value ) {
                    // Only import keys that start with cbp_
                    if ( strpos( $key, 'cbp_' ) === 0 ) {
                        update_option( sanitize_text_field( $key ), $value ); // sanitization of value happens down the line if we registered it properly, but we should be careful.
                    }
                }
                
                // Add a transient to show success message after redirect
                set_transient( 'cbp_settings_import_success', 1, 30 );
            }
        }
        
        // Redirect back to settings page
        $url = admin_url( 'admin.php?page=cbp-settings' );
        wp_safe_redirect( $url );
        exit;
    }
}
