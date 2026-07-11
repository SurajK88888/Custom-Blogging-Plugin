<?php
namespace CBP\frontend;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Template Loader Class
 * 
 * Intercepts the WordPress template hierarchy to load custom templates
 * from the plugin's `templates/` directory for CBP blog posts.
 */
class TemplateLoader {

    /**
     * Initialize the template loader hooks
     */
    public static function init() {
        add_filter( 'template_include', [ __CLASS__, 'load_templates' ] );
        add_action( 'wp_head', [ __CLASS__, 'output_seo_metadata' ] );
    }

    /**
     * Output custom SEO metadata to the head for CBP Blogs
     */
    public static function output_seo_metadata() {
        if ( is_singular( CPT::POST_TYPE ) ) {
            global $post;
            $seo_title = get_post_meta( $post->ID, '_cbp_seo_title', true );
            $seo_desc  = get_post_meta( $post->ID, '_cbp_seo_description', true );

            if ( ! empty( $seo_title ) ) {
                echo '<title>' . esc_html( $seo_title ) . '</title>' . "\n";
            }
            if ( ! empty( $seo_desc ) ) {
                echo '<meta name="description" content="' . esc_attr( $seo_desc ) . '">' . "\n";
            }
        }
    }

    /**
     * Load custom templates for CBP Blog CPT
     *
     * @param string $template The path to the template being included.
     * @return string
     */
    public static function load_templates( $template ) {
        
        // 1. Single Blog Template
        if ( is_singular( CPT::POST_TYPE ) ) {
            $custom_template = self::locate_template( 'single-cbp_blog.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
        }
        
        // 2. Blog Archive / Taxonomy Template
        elseif ( is_post_type_archive( CPT::POST_TYPE ) || is_tax( 'cbp_category' ) || is_tax( 'cbp_tag' ) ) {
            $custom_template = self::locate_template( 'archive-cbp_blog.php' );
            if ( $custom_template ) {
                return $custom_template;
            }
        }

        // Return default template if no match is found
        return $template;
    }

    /**
     * Locate the template.
     * Searches in the theme first, then falls back to the plugin's templates directory.
     *
     * @param string $template_name
     * @return string|false
     */
    private static function locate_template( $template_name ) {
        // Look within passed path within the theme - this is priority.
        $template = locate_template( [
            'custom-blog-pro/' . $template_name,
            $template_name
        ] );

        // If not found in theme, use our plugin's default template.
        if ( ! $template ) {
            $plugin_template = CBP_PLUGIN_DIR . 'templates/' . $template_name;
            if ( file_exists( $plugin_template ) ) {
                $template = $plugin_template;
            }
        }

        return $template;
    }
}
