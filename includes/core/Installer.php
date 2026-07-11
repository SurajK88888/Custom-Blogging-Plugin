<?php
namespace CBP\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Installer Class
 * 
 * Handles plugin activation, database table creation.
 */
class Installer {

    /**
     * Run the installer
     */
    public static function run() {
        self::create_tables();
        self::add_default_settings();
        
        if ( class_exists( '\CBP\core\Roles' ) ) {
            Roles::add_roles();
        }

        // Safely flush rewrite rules for Custom Post Types
        if ( class_exists( '\CBP\post\CPT' ) ) {
            \CBP\post\CPT::register_post_type();
        }
        if ( class_exists( '\CBP\post\Taxonomies' ) ) {
            \CBP\post\Taxonomies::register_taxonomies();
        }
        
        // Flush rewrite rules after registering CPT
        flush_rewrite_rules();

        // Schedule Email Cron
        if ( class_exists( '\CBP\email\Cron' ) ) {
            \CBP\email\Cron::schedule_event();
        }
    }

    /**
     * Create necessary database tables using dbDelta.
     */
    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $tables = [
            self::get_email_logs_schema( $wpdb->prefix . 'cbp_email_logs', $charset_collate ),
            self::get_email_queue_schema( $wpdb->prefix . 'cbp_email_queue', $charset_collate ),
            self::get_ad_views_schema( $wpdb->prefix . 'cbp_ad_views', $charset_collate ),
            self::get_blog_views_schema( $wpdb->prefix . 'cbp_blog_views', $charset_collate ),
            self::get_activity_logs_schema( $wpdb->prefix . 'cbp_activity_logs', $charset_collate ),
            self::get_plugin_settings_schema( $wpdb->prefix . 'cbp_plugin_settings', $charset_collate ),
        ];

        foreach ( $tables as $sql ) {
            dbDelta( $sql );
        }
    }

    private static function get_email_logs_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            recipient varchar(100) NOT NULL,
            status varchar(20) NOT NULL,
            message longtext NOT NULL,
            opened tinyint(1) DEFAULT 0 NOT NULL,
            clicked tinyint(1) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) {$charset_collate};";
    }

    private static function get_email_queue_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            recipient varchar(100) NOT NULL,
            status varchar(20) NOT NULL,
            attempt tinyint(2) DEFAULT 0 NOT NULL,
            scheduled_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY status (status)
        ) {$charset_collate};";
    }

    private static function get_ad_views_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            ad_id bigint(20) unsigned NOT NULL,
            post_id bigint(20) unsigned NOT NULL,
            user_ip varchar(100) NOT NULL,
            device varchar(50) NOT NULL,
            browser varchar(50) NOT NULL,
            clicked tinyint(1) DEFAULT 0 NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY ad_id (ad_id),
            KEY post_id (post_id)
        ) {$charset_collate};";
    }

    private static function get_blog_views_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            visitor_ip varchar(100) NOT NULL,
            country varchar(50) NOT NULL,
            device varchar(50) NOT NULL,
            browser varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id)
        ) {$charset_collate};";
    }

    private static function get_activity_logs_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            action varchar(255) NOT NULL,
            description longtext NOT NULL,
            ip varchar(100) NOT NULL,
            browser varchar(100) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) {$charset_collate};";
    }

    private static function get_plugin_settings_schema( $table_name, $charset_collate ) {
        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            setting_key varchar(100) NOT NULL,
            setting_value longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY setting_key (setting_key)
        ) {$charset_collate};";
    }

    /**
     * Add default settings on activation.
     */
    private static function add_default_settings() {
        // We will seed default plugin settings in the settings table if needed.
    }
}
