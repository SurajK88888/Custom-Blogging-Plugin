<?php
namespace CBP\frontend;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Frontend Components UI Class
 */
class Components {

    /**
     * Render the Reading Progress Bar
     */
    public function render_reading_progress() {
        echo '<div class="cbp-progress-container"><div class="cbp-progress-bar" id="cbp-reading-progress"></div></div>';
    }

    /**
     * Render Social Sharing Buttons
     */
    public function render_social_sharing() {
        $url   = urlencode( get_permalink() );
        $title = urlencode( get_the_title() );

        ?>
        <div class="cbp-social-share">
            <span class="cbp-share-label"><?php esc_html_e( 'Share this article:', 'custom-blog-pro' ); ?></span>
            <a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" class="cbp-share-btn twitter">Twitter</a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $url ); ?>" target="_blank" rel="noopener noreferrer" class="cbp-share-btn facebook">Facebook</a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_attr( $url ); ?>&title=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" class="cbp-share-btn linkedin">LinkedIn</a>
        </div>
        <style>
            .cbp-social-share { margin: 2rem 0; padding-top: 2rem; border-top: 1px solid var(--cbp-border-color); display: flex; align-items: center; gap: 1rem; }
            .cbp-share-label { font-weight: 600; }
            .cbp-share-btn { padding: 0.5rem 1rem; border-radius: var(--cbp-border-radius); text-decoration: none; color: white; font-weight: 500; transition: opacity 0.2s; }
            .cbp-share-btn:hover { opacity: 0.8; color: white; text-decoration: none;}
            .cbp-share-btn.twitter { background: #1DA1F2; }
            .cbp-share-btn.facebook { background: #4267B2; }
            .cbp-share-btn.linkedin { background: #0077b5; }
        </style>
        <?php
    }

    /**
     * Render Related Posts Grid
     *
     * @param int $post_id
     */
    public function render_related_posts( $post_id ) {
        $categories = wp_get_post_terms( $post_id, 'cbp_category', [ 'fields' => 'ids' ] );
        
        if ( empty( $categories ) ) {
            return;
        }

        $args = [
            'post_type'      => CPT::POST_TYPE,
            'post__not_in'   => [ $post_id ],
            'posts_per_page' => 3,
            'tax_query'      => [
                [
                    'taxonomy' => 'cbp_category',
                    'field'    => 'term_id',
                    'terms'    => $categories,
                ],
            ],
        ];

        $related = new \WP_Query( $args );

        if ( $related->have_posts() ) {
            echo '<h3 class="cbp-related-title">' . esc_html__( 'Related Articles', 'custom-blog-pro' ) . '</h3>';
            echo '<div class="cbp-grid-container">';
            
            while ( $related->have_posts() ) {
                $related->the_post();
                ?>
                <article <?php post_class( 'cbp-card' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a class="cbp-card-image" href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail( 'medium' ); ?>
                        </a>
                    <?php endif; ?>
                    <div class="cbp-card-content">
                        <header class="cbp-card-header">
                            <?php the_title( '<h4 class="cbp-card-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h4>' ); ?>
                        </header>
                    </div>
                </article>
                <?php
            }
            wp_reset_postdata();
            
            echo '</div>';
        }
    }
}
