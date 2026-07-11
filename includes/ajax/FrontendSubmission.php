<?php
namespace CBP\ajax;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Frontend Blog Submission AJAX Handler
 *
 * Handles secure blog post submission from the frontend shortcode form.
 * Posts are created with 'pending' status for admin review before publishing.
 *
 * Reusable pattern: This class demonstrates the standard CBP AJAX pattern:
 * 1. Nonce verification
 * 2. Login check
 * 3. Capability check
 * 4. Input sanitization
 * 5. Data processing
 * 6. JSON response
 */
class FrontendSubmission {

    /**
     * Initialize AJAX hooks
     */
    public static function init() {
        // Only logged-in users can submit (wp_ajax_ = logged in only)
        add_action( 'wp_ajax_cbp_submit_blog', [ __CLASS__, 'handle_submission' ] );
    }

    /**
     * Handle the blog submission form POST via AJAX
     */
    public static function handle_submission() {

        // 1. Security: Verify nonce
        if ( ! isset( $_POST['cbp_submit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cbp_submit_nonce'] ) ), 'cbp_submit_blog_action' ) ) {
            wp_send_json_error( [ 'message' => __( 'Security check failed. Please refresh the page and try again.', 'custom-blog-pro' ) ] );
        }

        // 2. Security: Check user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => __( 'You must be logged in to submit a blog post.', 'custom-blog-pro' ) ] );
        }

        // 3. Security: Check user has permission to publish/draft posts
        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have permission to submit blog posts. Please contact the administrator.', 'custom-blog-pro' ) ] );
        }

        // 4. Validate required fields
        $title   = isset( $_POST['cbp_blog_title'] ) ? sanitize_text_field( wp_unslash( $_POST['cbp_blog_title'] ) ) : '';
        $content = isset( $_POST['cbp_blog_content'] ) ? wp_kses_post( wp_unslash( $_POST['cbp_blog_content'] ) ) : '';

        if ( empty( $title ) ) {
            wp_send_json_error( [ 'message' => __( 'Blog title is required.', 'custom-blog-pro' ) ] );
        }
        if ( empty( $content ) ) {
            wp_send_json_error( [ 'message' => __( 'Blog content is required.', 'custom-blog-pro' ) ] );
        }

        // 5. Optional fields
        $excerpt  = isset( $_POST['cbp_blog_excerpt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cbp_blog_excerpt'] ) ) : '';
        $category = isset( $_POST['cbp_blog_category'] ) ? absint( $_POST['cbp_blog_category'] ) : 0;

        // 6. Insert the post as 'pending' so admin can review before publishing
        $post_data = [
            'post_title'   => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_status'  => 'pending',
            'post_type'    => CPT::POST_TYPE,
            'post_author'  => get_current_user_id(),
        ];

        $post_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $post_id ) ) {
            wp_send_json_error( [ 'message' => $post_id->get_error_message() ] );
        }

        // 7. Assign category if provided
        if ( $category > 0 ) {
            wp_set_object_terms( $post_id, $category, 'cbp_category' );
        }

        // 8. Handle featured image upload
        if ( ! empty( $_FILES['cbp_blog_image']['name'] ) ) {
            // Require media handling functions
            if ( ! function_exists( 'media_handle_upload' ) ) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
            }

            // Validate file type before uploading
            $allowed_mime_types = [ 'image/jpeg', 'image/png', 'image/gif', 'image/webp' ];
            $file_type = isset( $_FILES['cbp_blog_image']['type'] ) ? sanitize_mime_type( wp_unslash( $_FILES['cbp_blog_image']['type'] ) ) : '';

            if ( in_array( $file_type, $allowed_mime_types, true ) ) {
                $attachment_id = media_handle_upload( 'cbp_blog_image', $post_id );
                if ( ! is_wp_error( $attachment_id ) ) {
                    set_post_thumbnail( $post_id, $attachment_id );
                }
            }
        }

        // 9. Success — return a confirmation message
        wp_send_json_success( [
            'message' => __( 'Thank you! Your blog post has been submitted and is awaiting admin review. We\'ll notify you once it\'s published.', 'custom-blog-pro' ),
            'post_id' => $post_id,
        ] );
    }
}
