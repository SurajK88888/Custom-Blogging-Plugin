<?php
/**
 * Template: Blog Grid (Shortcode)
 *
 * Rendered by [cbp_blog_grid] shortcode via Shortcodes::render_blog_grid().
 * Variables available:
 *   $blog_query (WP_Query) — the posts query result
 *   $atts       (array)    — shortcode attributes (columns, posts_per_page, etc.)
 *
 * Reusable pattern: Uses .cbp-card classes already defined in frontend.css.
 * Adding new card fields only requires editing this template.
 *
 * @package Custom_Blog_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Column class maps attribute to CSS modifier
$col_class = ( intval( $atts['columns'] ) === 2 ) ? 'cbp-grid-cols-2' : 'cbp-grid-cols-3';
?>

<div class="cbp-blog-grid-wrap <?php echo esc_attr( $col_class ); ?>">

    <?php if ( $blog_query->have_posts() ) : ?>

        <div class="cbp-grid-container cbp-shortcode-grid">

            <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>

                <?php
                // Meta values
                $reading_time  = get_post_meta( get_the_ID(), '_cbp_reading_time', true );
                $post_url      = get_permalink();
                $has_thumbnail = has_post_thumbnail();
                ?>

                <article class="cbp-card cbp-blog-card" role="article">

                    <?php /* ── 1. Image ── */ ?>
                    <a href="<?php echo esc_url( $post_url ); ?>"
                       class="cbp-card-image cbp-card-image-link"
                       aria-label="<?php the_title_attribute(); ?>"
                       tabindex="-1">
                        <?php if ( $has_thumbnail ) : ?>
                            <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'alt' => get_the_title() ] ); ?>
                        <?php else : ?>
                            <div class="cbp-card-no-image">
                                <span class="dashicons dashicons-welcome-write-blog"></span>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Featured badge
                        if ( get_post_meta( get_the_ID(), '_cbp_is_featured', true ) ) : ?>
                            <span class="cbp-badge featured-badge"><?php esc_html_e( 'Featured', 'custom-blog-pro' ); ?></span>
                        <?php endif; ?>
                    </a>

                    <div class="cbp-card-content">

                        <?php /* ── 2. Title ── */ ?>
                        <h2 class="cbp-card-title">
                            <a href="<?php echo esc_url( $post_url ); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <?php /* ── 3. Description / Excerpt ── */ ?>
                        <p class="cbp-card-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt() ?: get_the_content(), 22, '…' ); ?>
                        </p>

                        <?php /* ── 4. Author · Date · Time · Reading time ── */ ?>
                        <div class="cbp-card-footer-meta">
                            <span class="cbp-meta-author">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <?php the_author(); ?>
                            </span>
                            <span class="cbp-meta-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?php echo get_the_date(); ?>
                                <?php esc_html_e( 'at', 'custom-blog-pro' ); ?>
                                <?php echo get_the_time(); ?>
                            </span>
                            <?php if ( $reading_time ) : ?>
                                <span class="cbp-meta-read">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <?php echo esc_html( $reading_time ); ?> <?php esc_html_e( 'min read', 'custom-blog-pro' ); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <a href="<?php echo esc_url( $post_url ); ?>" class="cbp-read-more-btn" aria-label="<?php echo esc_attr( sprintf( __( 'Read more: %s', 'custom-blog-pro' ), get_the_title() ) ); ?>">
                            <?php esc_html_e( 'Read Post', 'custom-blog-pro' ); ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>

                    </div><!-- .cbp-card-content -->

                </article><!-- .cbp-card -->

            <?php endwhile; ?>

        </div><!-- .cbp-grid-container -->

        <?php
        // Pagination for the grid
        $big = 999999999;
        $pagination = paginate_links( [
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $blog_query->max_num_pages,
            'prev_text' => '&larr; ' . __( 'Previous', 'custom-blog-pro' ),
            'next_text' => __( 'Next', 'custom-blog-pro' ) . ' &rarr;',
        ] );
        if ( $pagination ) : ?>
            <nav class="cbp-pagination" aria-label="<?php esc_attr_e( 'Blog navigation', 'custom-blog-pro' ); ?>">
                <?php echo $pagination; // phpcs:ignore WordPress.Security.EscapeOutput ?>
            </nav>
        <?php endif; ?>

    <?php else : ?>

        <p class="cbp-no-posts">
            <?php esc_html_e( 'No blog posts found.', 'custom-blog-pro' ); ?>
        </p>

    <?php endif; ?>

</div><!-- .cbp-blog-grid-wrap -->
