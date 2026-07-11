<?php
namespace CBP\core;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Roles and Capabilities Class
 */
class Roles {

    /**
     * Initialize Roles & Capabilities
     */
    public static function init() {
        // Typically, role initialization should happen on plugin activation
        // rather than on every page load. We can hook into a specific action
        // or call this from the Installer.
    }

    /**
     * Add custom roles and capabilities
     * Should be called during plugin activation.
     */
    public static function add_roles() {
        // Example: Add a 'cbp_manager' role or add caps to 'administrator'
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'manage_cbp_settings' );
            $admin->add_cap( 'view_cbp_analytics' );
            $admin->add_cap( 'manage_cbp_ads' );
            $admin->add_cap( 'manage_cbp_emails' );
        }

        // Add a specialized role for bloggers if needed
        add_role( 'cbp_blogger', __( 'CBP Blogger', 'custom-blog-pro' ), [
            'read' => true,
            'edit_posts' => true,
            'upload_files' => true,
            'view_cbp_analytics' => true,
        ] );
    }

    /**
     * Remove custom roles and capabilities
     * Should be called during plugin uninstall.
     */
    public static function remove_roles() {
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->remove_cap( 'manage_cbp_settings' );
            $admin->remove_cap( 'view_cbp_analytics' );
            $admin->remove_cap( 'manage_cbp_ads' );
            $admin->remove_cap( 'manage_cbp_emails' );
        }

        remove_role( 'cbp_blogger' );
    }
}
