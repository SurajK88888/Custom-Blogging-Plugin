<?php
namespace CBP\frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcodes Registration Class
 *
 * Registers all public-facing shortcodes for the CBP plugin.
 *
 * Reusable pattern: Add new shortcodes here as static methods and
 * register them in the init() method. Each shortcode should return
 * HTML (not echo directly) to respect WordPress shortcode standards.
 */
class Shortcodes {

    /**
     * Initialize all shortcodes
     */
    public static function init() {
        add_shortcode( 'cbp_submit_form', [ __CLASS__, 'render_submit_form' ] );
    }

    /**
     * Render the Frontend Blog Submission Form
     *
     * Usage: [cbp_submit_form]
     *
     * @return string HTML output
     */
    public static function render_submit_form() {
        // If user is not logged in, show a login prompt instead of the form
        if ( ! is_user_logged_in() ) {
            $login_url = wp_login_url( get_permalink() );
            return sprintf(
                '<div class="cbp-submit-login-notice"><p>%s <a href="%s">%s</a></p></div>',
                esc_html__( 'You need to be logged in to submit a blog post.', 'custom-blog-pro' ),
                esc_url( $login_url ),
                esc_html__( 'Login here', 'custom-blog-pro' )
            );
        }

        // If user lacks the capability, show an access denied notice
        if ( ! current_user_can( 'edit_posts' ) ) {
            return '<div class="cbp-submit-notice cbp-submit-error"><p>' . esc_html__( 'You do not have permission to submit blog posts. Please contact the administrator to upgrade your account role.', 'custom-blog-pro' ) . '</p></div>';
        }

        // Get categories for the dropdown
        $categories = get_terms( [
            'taxonomy'   => 'cbp_category',
            'hide_empty' => false,
        ] );

        // Load the form template and capture its output
        ob_start();
        $template = CBP_PLUGIN_DIR . 'templates/frontend/submit-form.php';
        if ( file_exists( $template ) ) {
            include $template;
        }
        return ob_get_clean();
    }
}
