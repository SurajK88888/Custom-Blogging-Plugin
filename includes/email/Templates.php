<?php
namespace CBP\email;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Templates Class
 * 
 * Compiles dynamic data into HTML email templates.
 */
class Templates {

    /**
     * Get compiled HTML email body for a new post.
     * 
     * @param \WP_Post $post
     * @return string HTML content.
     */
    public static function get_email_body( $post ) {
        // Prepare template variables
        $args = [
            'title'       => $post->post_title,
            'excerpt'     => wp_trim_words( $post->post_content, 30, '...' ),
            'permalink'   => get_permalink( $post->ID ),
            'thumbnail'   => has_post_thumbnail( $post->ID ) ? get_the_post_thumbnail_url( $post->ID, 'large' ) : '',
            'site_name'   => get_bloginfo( 'name' ),
            'author_name' => get_the_author_meta( 'display_name', $post->post_author ),
        ];

        return self::render_template( 'base.php', $args );
    }

    /**
     * Render the template. Allows theme override.
     * 
     * @param string $template_name
     * @param array  $args Variables to inject into the template.
     * @return string
     */
    private static function render_template( $template_name, $args ) {
        // Look within theme for overrides first.
        $template = locate_template( [
            'custom-blog-pro/email/' . $template_name,
            'email/' . $template_name
        ] );

        // Fallback to plugin template
        if ( ! $template ) {
            $plugin_template = CBP_PLUGIN_DIR . 'templates/email/' . $template_name;
            if ( file_exists( $plugin_template ) ) {
                $template = $plugin_template;
            }
        }

        if ( ! $template ) {
            return '';
        }

        // Extract args to make them available as variables inside the included template
        extract( $args );

        ob_start();
        include $template;
        return ob_get_clean();
    }
}
