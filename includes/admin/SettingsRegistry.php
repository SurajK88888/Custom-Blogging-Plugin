<?php
namespace CBP\admin;

use CBP\helper\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Settings Registry Class
 * 
 * Registers the WP options, sections, and fields.
 */
class SettingsRegistry {

    const GROUP = 'cbp_settings';

    /**
     * Initialize settings hooks.
     */
    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    /**
     * Register all settings fields.
     */
    public static function register_settings() {
        
        // 1. Appearance Settings
        add_settings_section( 'cbp_appearance_section', null, null, self::GROUP );
        self::register_field( 'cbp_primary_color', '#2563eb', 'sanitize_hex_color' );
        self::register_field( 'cbp_secondary_color', '#1e40af', 'sanitize_hex_color' );
        self::register_field( 'cbp_border_radius', '12px', 'sanitize_text_field' );
        self::register_field( 'cbp_font_family', 'Inter', 'sanitize_text_field' );
        self::register_field( 'cbp_dark_mode', '0', 'absint' );
        self::register_field( 'cbp_card_shadow', '0 4px 6px -1px rgba(0, 0, 0, 0.1)', 'sanitize_text_field' );
        self::register_field( 'cbp_layout_sidebar', 'right', 'sanitize_text_field' );
        
        // 2. SMTP Settings
        add_settings_section( 'cbp_smtp_section', null, null, self::GROUP );
        self::register_field( 'cbp_smtp_enabled', '0', 'absint' );
        self::register_field( 'cbp_smtp_host', '', 'sanitize_text_field' );
        self::register_field( 'cbp_smtp_port', '587', 'absint' );
        self::register_field( 'cbp_smtp_username', '', 'sanitize_text_field' );
        self::register_field( 'cbp_smtp_password', '', 'sanitize_text_field' );
        self::register_field( 'cbp_smtp_encryption', 'tls', 'sanitize_text_field' );
        self::register_field( 'cbp_smtp_from_email', '', 'sanitize_email' );
        self::register_field( 'cbp_smtp_from_name', '', 'sanitize_text_field' );

        // 3. Ad Settings
        add_settings_section( 'cbp_ads_section', null, null, self::GROUP );
        self::register_field( 'cbp_ads_enabled', '1', 'absint' );
        // We use wp_kses_post to allow HTML/JS for ad codes, but securely.
        self::register_field( 'cbp_ad_top', '', [ __CLASS__, 'sanitize_ad_code' ] );
        self::register_field( 'cbp_ad_middle', '', [ __CLASS__, 'sanitize_ad_code' ] );
        self::register_field( 'cbp_ad_bottom', '', [ __CLASS__, 'sanitize_ad_code' ] );
        self::register_field( 'cbp_ad_sidebar', '', [ __CLASS__, 'sanitize_ad_code' ] );
    }

    /**
     * Helper to register a setting and add its field to a section.
     */
    private static function register_field( $option_name, $default, $sanitize_callback ) {
        register_setting(
            self::GROUP,
            $option_name,
            [
                'type'              => 'string',
                'sanitize_callback' => $sanitize_callback,
                'default'           => $default,
            ]
        );
    }

    /**
     * Ad Code Sanitizer
     * Allows script tags for ads (e.g. Google AdSense) but requires admin capability.
     */
    public static function sanitize_ad_code( $input ) {
        if ( current_user_can( 'unfiltered_html' ) ) {
            return wp_unslash( $input ); // Trust admins with unfiltered_html
        } else {
            return wp_kses_post( wp_unslash( $input ) );
        }
    }
}
