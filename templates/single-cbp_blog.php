<?php
/**
 * The template for displaying Single CBP Blog Post
 *
 * @package Custom_Blog_Pro
 */

get_header(); 

// Load our specific Components Class
$components = class_exists( '\CBP\frontend\Components' ) ? new \CBP\frontend\Components() : null;
?>

<div id="primary" class="content-area cbp-single-container">
    <main id="main" class="site-main">

        <?php if ( $components ) { $components->render_reading_progress(); } ?>

        <?php
        while ( have_posts() ) :
            the_post();

            $reading_time = get_post_meta( get_the_ID(), '_cbp_reading_time', true );
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class( 'cbp-single-article' ); ?>>
                
                <header class="cbp-single-header">
                    <?php the_title( '<h1 class="cbp-single-title">', '</h1>' ); ?>
                    
                    <div class="cbp-single-meta">
                        <span class="cbp-author"><?php esc_html_e( 'By', 'custom-blog-pro' ); ?> <?php the_author_posts_link(); ?></span>
                        <span class="cbp-date">&bull; <?php echo get_the_date(); ?> <?php esc_html_e( 'at', 'custom-blog-pro' ); ?> <?php echo get_the_time(); ?></span>
                        <?php if ( $reading_time ) : ?>
                            <span class="cbp-reading-time">&bull; <?php echo esc_html( $reading_time ); ?> <?php esc_html_e( 'min read', 'custom-blog-pro' ); ?></span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="cbp-single-thumbnail">
                        <?php the_post_thumbnail( 'full' ); ?>
                    </div>
                <?php endif; ?>

                <div class="cbp-single-content">
                    <?php
                    the_content();
                    
                    wp_link_pages( [
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'custom-blog-pro' ),
                        'after'  => '</div>',
                    ] );
                    ?>
                </div>

                <footer class="cbp-single-footer">
                    <?php if ( $components ) { $components->render_social_sharing(); } ?>
                </footer>

            </article>

            <?php
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
            ?>
            <div class="cbp-comments-card">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>

            <div class="cbp-related-posts-section">
                <?php if ( $components ) { $components->render_related_posts( get_the_ID() ); } ?>
            </div>

        <?php endwhile; // End of the loop. ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
