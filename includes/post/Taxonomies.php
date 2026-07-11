<?php
namespace CBP\post;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Custom Taxonomies Registration Class
 */
class Taxonomies {

    const TAX_CATEGORY = 'cbp_category';
    const TAX_TAG      = 'cbp_tag';

    /**
     * Initialize Taxonomies
     */
    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_taxonomies' ] );
    }

    /**
     * Register Custom Taxonomies
     */
    public static function register_taxonomies() {
        // Register Category
        $cat_labels = [
            'name'                       => _x( 'CBP Categories', 'Taxonomy General Name', 'custom-blog-pro' ),
            'singular_name'              => _x( 'CBP Category', 'Taxonomy Singular Name', 'custom-blog-pro' ),
            'menu_name'                  => __( 'Categories', 'custom-blog-pro' ),
            'all_items'                  => __( 'All Categories', 'custom-blog-pro' ),
            'parent_item'                => __( 'Parent Category', 'custom-blog-pro' ),
            'parent_item_colon'          => __( 'Parent Category:', 'custom-blog-pro' ),
            'new_item_name'              => __( 'New Category Name', 'custom-blog-pro' ),
            'add_new_item'               => __( 'Add New Category', 'custom-blog-pro' ),
            'edit_item'                  => __( 'Edit Category', 'custom-blog-pro' ),
            'update_item'                => __( 'Update Category', 'custom-blog-pro' ),
            'view_item'                  => __( 'View Category', 'custom-blog-pro' ),
            'separate_items_with_commas' => __( 'Separate categories with commas', 'custom-blog-pro' ),
            'add_or_remove_items'        => __( 'Add or remove categories', 'custom-blog-pro' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'custom-blog-pro' ),
            'popular_items'              => __( 'Popular Categories', 'custom-blog-pro' ),
            'search_items'               => __( 'Search Categories', 'custom-blog-pro' ),
            'not_found'                  => __( 'Not Found', 'custom-blog-pro' ),
            'no_terms'                   => __( 'No categories', 'custom-blog-pro' ),
            'items_list'                 => __( 'Categories list', 'custom-blog-pro' ),
            'items_list_navigation'      => __( 'Categories list navigation', 'custom-blog-pro' ),
        ];

        $cat_args = [
            'labels'                     => $cat_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        ];
        register_taxonomy( self::TAX_CATEGORY, [ CPT::POST_TYPE ], $cat_args );

        // Register Tag
        $tag_labels = [
            'name'                       => _x( 'CBP Tags', 'Taxonomy General Name', 'custom-blog-pro' ),
            'singular_name'              => _x( 'CBP Tag', 'Taxonomy Singular Name', 'custom-blog-pro' ),
            'menu_name'                  => __( 'Tags', 'custom-blog-pro' ),
            'all_items'                  => __( 'All Tags', 'custom-blog-pro' ),
            'new_item_name'              => __( 'New Tag Name', 'custom-blog-pro' ),
            'add_new_item'               => __( 'Add New Tag', 'custom-blog-pro' ),
            'edit_item'                  => __( 'Edit Tag', 'custom-blog-pro' ),
            'update_item'                => __( 'Update Tag', 'custom-blog-pro' ),
            'view_item'                  => __( 'View Tag', 'custom-blog-pro' ),
            'separate_items_with_commas' => __( 'Separate tags with commas', 'custom-blog-pro' ),
            'add_or_remove_items'        => __( 'Add or remove tags', 'custom-blog-pro' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'custom-blog-pro' ),
            'popular_items'              => __( 'Popular Tags', 'custom-blog-pro' ),
            'search_items'               => __( 'Search Tags', 'custom-blog-pro' ),
            'not_found'                  => __( 'Not Found', 'custom-blog-pro' ),
            'no_terms'                   => __( 'No tags', 'custom-blog-pro' ),
        ];

        $tag_args = [
            'labels'                     => $tag_labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
        ];
        register_taxonomy( self::TAX_TAG, [ CPT::POST_TYPE ], $tag_args );
    }
}
