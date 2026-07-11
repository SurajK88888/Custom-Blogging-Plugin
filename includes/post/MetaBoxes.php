<?php
namespace CBP\post;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom Meta Boxes for CBP Blog
 */
class MetaBoxes {

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_seo_meta_box' ] );
        add_action( 'save_post', [ __CLASS__, 'save_seo_meta_box' ] );
    }

    public static function add_seo_meta_box() {
        add_meta_box(
            'cbp_seo_meta_box',
            __( 'Custom Blog Pro - SEO Settings', 'custom-blog-pro' ),
            [ __CLASS__, 'render_seo_meta_box' ],
            'cbp_blog',
            'normal',
            'high'
        );
    }

    public static function render_seo_meta_box( $post ) {
        wp_nonce_field( 'cbp_save_seo_data', 'cbp_seo_nonce' );

        $seo_title = get_post_meta( $post->ID, '_cbp_seo_title', true );
        $seo_desc  = get_post_meta( $post->ID, '_cbp_seo_description', true );

        ?>
        <p>
            <label for="cbp_seo_title"><strong><?php esc_html_e( 'SEO Title', 'custom-blog-pro' ); ?></strong></label><br>
            <input type="text" id="cbp_seo_title" name="cbp_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" class="large-text" />
            <br><small><?php esc_html_e( 'Overrides the default post title in search engines.', 'custom-blog-pro' ); ?></small>
        </p>
        <p>
            <label for="cbp_seo_description"><strong><?php esc_html_e( 'SEO Meta Description', 'custom-blog-pro' ); ?></strong></label><br>
            <textarea id="cbp_seo_description" name="cbp_seo_description" rows="3" class="large-text"><?php echo esc_textarea( $seo_desc ); ?></textarea>
            <br><small><?php esc_html_e( 'A short description of the post for search engine snippets.', 'custom-blog-pro' ); ?></small>
        </p>
        <?php
    }

    public static function save_seo_meta_box( $post_id ) {
        if ( ! isset( $_POST['cbp_seo_nonce'] ) || ! wp_verify_nonce( $_POST['cbp_seo_nonce'], 'cbp_save_seo_data' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['cbp_seo_title'] ) ) {
            update_post_meta( $post_id, '_cbp_seo_title', sanitize_text_field( $_POST['cbp_seo_title'] ) );
        }

        if ( isset( $_POST['cbp_seo_description'] ) ) {
            update_post_meta( $post_id, '_cbp_seo_description', sanitize_textarea_field( $_POST['cbp_seo_description'] ) );
        }
    }
}
