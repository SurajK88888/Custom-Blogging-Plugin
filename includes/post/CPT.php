<?php
namespace CBP\post;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Custom Post Type Registration Class
 */
class CPT {

    const POST_TYPE = 'cbp_blog';

    /**
     * Initialize the CPT
     */
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_post_type' ] );
    }

    /**
     * Register the Custom Post Type
     */
    public static function register_post_type() {
        $labels = [
            'name'                  => _x( 'CBP Blogs', 'Post Type General Name', 'custom-blog-pro' ),
            'singular_name'         => _x( 'CBP Blog', 'Post Type Singular Name', 'custom-blog-pro' ),
            'menu_name'             => __( 'Blogs (CBP)', 'custom-blog-pro' ),
            'name_admin_bar'        => __( 'CBP Blog', 'custom-blog-pro' ),
            'archives'              => __( 'Blog Archives', 'custom-blog-pro' ),
            'attributes'            => __( 'Blog Attributes', 'custom-blog-pro' ),
            'parent_item_colon'     => __( 'Parent Blog:', 'custom-blog-pro' ),
            'all_items'             => __( 'All Blogs', 'custom-blog-pro' ),
            'add_new_item'          => __( 'Add New Blog', 'custom-blog-pro' ),
            'add_new'               => __( 'Add New', 'custom-blog-pro' ),
            'new_item'              => __( 'New Blog', 'custom-blog-pro' ),
            'edit_item'             => __( 'Edit Blog', 'custom-blog-pro' ),
            'update_item'           => __( 'Update Blog', 'custom-blog-pro' ),
            'view_item'             => __( 'View Blog', 'custom-blog-pro' ),
            'view_items'            => __( 'View Blogs', 'custom-blog-pro' ),
            'search_items'          => __( 'Search Blog', 'custom-blog-pro' ),
            'not_found'             => __( 'Not found', 'custom-blog-pro' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'custom-blog-pro' ),
            'featured_image'        => __( 'Featured Image', 'custom-blog-pro' ),
            'set_featured_image'    => __( 'Set featured image', 'custom-blog-pro' ),
            'remove_featured_image' => __( 'Remove featured image', 'custom-blog-pro' ),
            'use_featured_image'    => __( 'Use as featured image', 'custom-blog-pro' ),
            'insert_into_item'      => __( 'Insert into blog', 'custom-blog-pro' ),
            'uploaded_to_this_item' => __( 'Uploaded to this blog', 'custom-blog-pro' ),
            'items_list'            => __( 'Blogs list', 'custom-blog-pro' ),
            'items_list_navigation' => __( 'Blogs list navigation', 'custom-blog-pro' ),
            'filter_items_list'     => __( 'Filter blogs list', 'custom-blog-pro' ),
        ];

        $args = [
            'label'                 => __( 'CBP Blog', 'custom-blog-pro' ),
            'description'           => __( 'Custom Blog Pro entries', 'custom-blog-pro' ),
            'labels'                => $labels,
            'supports'              => [ 'title', 'editor', 'thumbnail', 'revisions', 'author', 'excerpt', 'comments' ],
            'taxonomies'            => [ 'cbp_category', 'cbp_tag' ], // We'll register these next
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-welcome-write-blog',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true, // Enable Gutenberg editor
        ];

        register_post_type( 'cbp_blog', $args );

        // Register internal campaign post type for Bulk Emails
        register_post_type( 'cbp_campaign', [
            'label'               => __( 'Email Campaigns', 'custom-blog-pro' ),
            'public'              => false, // Internal use only
            'show_ui'             => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'supports'            => [ 'title', 'editor' ],
        ] );
    }
}
