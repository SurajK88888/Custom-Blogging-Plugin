<?php
namespace CBP\admin;

use CBP\post\CPT;
use CBP\helper\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Custom Meta Boxes Class
 */
class MetaBoxes {

    /**
     * Initialize Meta Boxes
     */
    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_custom_meta_boxes' ] );
        add_action( 'save_post', [ __CLASS__, 'save_custom_meta_boxes' ] );
    }

    /**
     * Add Meta Boxes to CBP Blog
     */
    public static function add_custom_meta_boxes() {
        add_meta_box(
            'cbp_blog_settings',
            __( 'Blog Settings (SEO & Features)', 'custom-blog-pro' ),
            [ __CLASS__, 'render_meta_box_content' ],
            CPT::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Render the Meta Box Content
     *
     * @param \WP_Post $post
     */
    public static function render_meta_box_content( $post ) {
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'cbp_save_blog_data', 'cbp_blog_meta_nonce' );

        $seo_title       = get_post_meta( $post->ID, '_cbp_seo_title', true );
        $seo_description = get_post_meta( $post->ID, '_cbp_seo_description', true );
        $reading_time    = get_post_meta( $post->ID, '_cbp_reading_time', true );
        $is_featured     = get_post_meta( $post->ID, '_cbp_is_featured', true );

        ?>
        <table class="form-table">
            <tr>
                <th><label for="cbp_seo_title"><?php esc_html_e( 'SEO Title', 'custom-blog-pro' ); ?></label></th>
                <td>
                    <input type="text" id="cbp_seo_title" name="cbp_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="cbp_seo_description"><?php esc_html_e( 'SEO Description', 'custom-blog-pro' ); ?></label></th>
                <td>
                    <textarea id="cbp_seo_description" name="cbp_seo_description" rows="4" style="width:100%;"><?php echo esc_textarea( $seo_description ); ?></textarea>
                </td>
            </tr>
            <tr>
                <th><label for="cbp_reading_time"><?php esc_html_e( 'Reading Time (minutes)', 'custom-blog-pro' ); ?></label></th>
                <td>
                    <input type="number" id="cbp_reading_time" name="cbp_reading_time" value="<?php echo esc_attr( $reading_time ); ?>" class="small-text" min="1" />
                </td>
            </tr>
            <tr>
                <th><label for="cbp_is_featured"><?php esc_html_e( 'Featured Post', 'custom-blog-pro' ); ?></label></th>
                <td>
                    <input type="checkbox" id="cbp_is_featured" name="cbp_is_featured" value="1" <?php checked( $is_featured, '1' ); ?> />
                    <span class="description"><?php esc_html_e( 'Check to mark this post as featured.', 'custom-blog-pro' ); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save the Meta Box Data
     *
     * @param int $post_id
     */
    public static function save_custom_meta_boxes( $post_id ) {
        // Check if our nonce is set and verify it.
        if ( ! Security::verify_nonce( 'cbp_blog_meta_nonce', 'cbp_save_blog_data' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( ! Security::current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Make sure that it is set and sanitize it.
        if ( isset( $_POST['cbp_seo_title'] ) ) {
            update_post_meta( $post_id, '_cbp_seo_title', Security::sanitize_text( wp_unslash( $_POST['cbp_seo_title'] ) ) );
        }

        if ( isset( $_POST['cbp_seo_description'] ) ) {
            update_post_meta( $post_id, '_cbp_seo_description', sanitize_textarea_field( wp_unslash( $_POST['cbp_seo_description'] ) ) );
        }

        if ( isset( $_POST['cbp_reading_time'] ) ) {
            update_post_meta( $post_id, '_cbp_reading_time', absint( $_POST['cbp_reading_time'] ) );
        }

        if ( isset( $_POST['cbp_is_featured'] ) ) {
            update_post_meta( $post_id, '_cbp_is_featured', '1' );
        } else {
            update_post_meta( $post_id, '_cbp_is_featured', '0' );
        }
    }
}
