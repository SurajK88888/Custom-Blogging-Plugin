<?php
namespace CBP\widgets;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Ad Widget Class
 * 
 * Allows users to place CBP ads in theme sidebars.
 */
class AdWidget extends \WP_Widget {

    /**
     * Initialize widget.
     */
    public static function init() {
        add_action( 'widgets_init', function() {
            register_widget( __CLASS__ );
        });
    }

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            'cbp_ad_widget',
            esc_html__( 'CBP Ad Widget', 'custom-blog-pro' ),
            [ 'description' => esc_html__( 'Displays the global sidebar ad for Custom Blog Pro.', 'custom-blog-pro' ) ]
        );
    }

    /**
     * Frontend display of widget.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // Check if ads are globally enabled
        $ads_enabled = get_option( 'cbp_ads_enabled', true );
        if ( ! $ads_enabled ) {
            return;
        }

        $sidebar_ad = get_option( 'cbp_ad_sidebar', '' );
        if ( empty( $sidebar_ad ) ) {
            return;
        }

        echo $args['before_widget'];
        
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $post_id = is_singular() ? get_the_ID() : 0;

        printf(
            '<div class="cbp-ad-wrapper" data-ad-id="sidebar" data-post-id="%d" style="text-align: center;">%s</div>',
            esc_attr( $post_id ),
            $sidebar_ad
        );

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance
     */
    public function form( $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Advertisement', 'custom-blog-pro' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'custom-blog-pro' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <em><?php esc_html_e( 'The ad content is managed globally from the CBP Settings dashboard.', 'custom-blog-pro' ); ?></em>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        return $instance;
    }
}
