<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Custom_Blog_Pro
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Clean up database tables, settings, etc.
// 1. Clear scheduled cron jobs
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
    if ( class_exists( '\CBP\email\Cron' ) ) {
        \CBP\email\Cron::clear_schedule();
    }
}

// 2. Drop Plugin Custom Tables
global $wpdb;

$tables = [
    $wpdb->prefix . 'cbp_email_logs',
    $wpdb->prefix . 'cbp_email_queue',
    $wpdb->prefix . 'cbp_ad_views',
    $wpdb->prefix . 'cbp_blog_views',
    $wpdb->prefix . 'cbp_activity_logs',
    $wpdb->prefix . 'cbp_plugin_settings',
];

foreach ( $tables as $table ) {
    $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}

// Any options defined via standard WP options table
delete_option( 'cbp_version' );

// Remove custom roles and capabilities
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
    if ( class_exists( '\CBP\core\Roles' ) ) {
        \CBP\core\Roles::remove_roles();
    }
}
