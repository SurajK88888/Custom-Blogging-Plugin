<?php
/**
 * The template for displaying CBP Blog Archive
 *
 * @package Custom_Blog_Pro
 */

get_header(); ?>

<div id="primary" class="content-area cbp-archive-container">
    <main id="main" class="site-main">

        <header class="page-header">
            <?php
            if ( is_post_type_archive( \CBP\post\CPT::POST_TYPE ) ) {
                echo '<h1 class="page-title">' . esc_html__( 'Our Blog', 'custom-blog-pro' ) . '</h1>';
            } else {
                the_archive_title( '<h1 class="page-title">', '</h1>' );
                the_archive_description( '<div class="archive-description">', '</div>' );
            }
            ?>
        </header>

        <!-- Category/Tag Filters will go here via AJAX -->
        <div class="cbp-filters-wrapper">
            <!-- Filter UI (To be built via components) -->
        </div>

        <?php if ( have_posts() ) : ?>

            <div class="cbp-grid-container" id="cbp-ajax-container">
                <?php
                while ( have_posts() ) :
                    the_post();
                    
                    // Fetch Meta
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
                                <?php the_title( '<h2 class="cbp-card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
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
                    
                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            the_posts_pagination( [
                'prev_text' => __( 'Previous', 'custom-blog-pro' ),
                'next_text' => __( 'Next', 'custom-blog-pro' ),
            ] );
            ?>

        <?php else : ?>
            <p><?php esc_html_e( 'No blogs found.', 'custom-blog-pro' ); ?></p>
        <?php endif; ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
