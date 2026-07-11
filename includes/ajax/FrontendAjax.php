<?php
namespace CBP\ajax;

use CBP\post\CPT;
use CBP\helper\Security;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Frontend AJAX Handlers
 */
class FrontendAjax {

    /**
     * Initialize AJAX hooks
     */
    public static function init() {
        add_action( 'wp_ajax_cbp_load_blogs', [ __CLASS__, 'load_blogs' ] );
        add_action( 'wp_ajax_nopriv_cbp_load_blogs', [ __CLASS__, 'load_blogs' ] );
    }

    /**
     * Load Blogs via AJAX (Pagination & Filtering)
     */
    public static function load_blogs() {
        // Verify nonce if we implemented one for the frontend (optional for public read data, but good practice).
        // if ( ! Security::verify_nonce('cbp_frontend_ajax', 'security') ) { ... }

        $page     = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
        $category = isset( $_POST['category'] ) ? Security::sanitize_text( wp_unslash( $_POST['category'] ) ) : '';
        $tag      = isset( $_POST['tag'] ) ? Security::sanitize_text( wp_unslash( $_POST['tag'] ) ) : '';

        $args = [
            'post_type'      => CPT::POST_TYPE,
            'post_status'    => 'publish',
            'paged'          => $page,
            'posts_per_page' => 9,
        ];

        if ( ! empty( $category ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'cbp_category',
                'field'    => 'slug',
                'terms'    => $category,
            ];
        }

        if ( ! empty( $tag ) ) {
            $args['tax_query'][] = [
                'taxonomy' => 'cbp_tag',
                'field'    => 'slug',
                'terms'    => $tag,
            ];
        }

        $query = new \WP_Query( $args );

        if ( $query->have_posts() ) {
            ob_start();
            while ( $query->have_posts() ) {
                $query->the_post();
                
                $reading_time = get_post_meta( get_the_ID(), '_cbp_reading_time', true );
                $is_featured  = get_post_meta( get_the_ID(), '_cbp_is_featured', true );
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'cbp-card' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="cbp-card-image" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'large' ); ?>
                            <?php if ( $is_featured ) : ?>
                                <span class="cbp-badge featured-badge"><?php esc_html_e( 'Featured', 'custom-blog-pro' ); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    
                    <div class="cbp-card-content">
                        <header class="cbp-card-header">
                            <?php the_title( '<h2 class="cbp-card-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' ); ?>
                            <div class="cbp-meta">
                                <span class="cbp-date"><?php echo get_the_date(); ?></span>
                                <?php if ( $reading_time ) : ?>
                                    <span class="cbp-reading-time">&bull; <?php echo esc_html( $reading_time ); ?> <?php esc_html_e( 'min read', 'custom-blog-pro' ); ?></span>
                                <?php endif; ?>
                            </div>
                        </header>
                        <div class="cbp-card-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                </article>
                <?php
            }
            $html = ob_get_clean();

            wp_send_json_success( [
                'html'       => $html,
                'max_pages'  => $query->max_num_pages,
            ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'No more blogs found.', 'custom-blog-pro' ) ] );
        }

        wp_die();
    }
}
