<?php
namespace CBP\ads;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Ad Manager Class
 * 
 * Handles injecting ads into the content securely.
 */
class AdManager {

    /**
     * Initialize ad hooks.
     */
    public static function init() {
        // Inject ads into single blog posts
        add_filter( 'the_content', [ __CLASS__, 'inject_ads' ] );
    }

    /**
     * Inject ads into the content.
     * 
     * @param string $content
     * @return string
     */
    public static function inject_ads( $content ) {
        // Only run on single CBP blogs, inside the main loop
        if ( ! is_singular( CPT::POST_TYPE ) || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }

        // Check if ads are globally enabled (Stub from Settings module)
        $ads_enabled = get_option( 'cbp_ads_enabled', true );
        if ( ! $ads_enabled ) {
            return $content;
        }

        // Fetch ad codes
        $top_ad    = get_option( 'cbp_ad_top', '' );
        $middle_ad = get_option( 'cbp_ad_middle', '' );
        $bottom_ad = get_option( 'cbp_ad_bottom', '' );

        $new_content = $content;

        // Inject Bottom
        if ( ! empty( $bottom_ad ) ) {
            $new_content .= self::wrap_ad( $bottom_ad, 'bottom' );
        }

        // Inject Top
        if ( ! empty( $top_ad ) ) {
            $new_content = self::wrap_ad( $top_ad, 'top' ) . $new_content;
        }

        // Inject Middle (Splice after paragraph)
        if ( ! empty( $middle_ad ) ) {
            $new_content = self::inject_middle_ad( $new_content, self::wrap_ad( $middle_ad, 'middle' ) );
        }

        return $new_content;
    }

    /**
     * Wrap the ad code in a tracking div.
     * 
     * @param string $ad_code
     * @param string $position
     * @return string
     */
    private static function wrap_ad( $ad_code, $position ) {
        $post_id = get_the_ID();
        // The data-ad-id would normally map to a specific ad ID if we had multiple variations.
        // For now, we use the position as the ID.
        return sprintf(
            '<div class="cbp-ad-wrapper" data-ad-id="%s" data-post-id="%d" style="margin: 20px 0; text-align: center;">%s</div>',
            esc_attr( $position ),
            esc_attr( $post_id ),
            $ad_code
        );
    }

    /**
     * Inject ad after the halfway point of the paragraphs.
     * 
     * @param string $content
     * @param string $ad_html
     * @return string
     */
    private static function inject_middle_ad( $content, $ad_html ) {
        $closing_p = '</p>';
        $paragraphs = explode( $closing_p, $content );
        
        $count = count( $paragraphs );
        
        // If content is too short (less than 3 paragraphs), just append it.
        if ( $count < 3 ) {
            return $content . $ad_html;
        }

        $middle_index = floor( $count / 2 );

        $new_content = '';
        foreach ( $paragraphs as $index => $paragraph ) {
            if ( trim( $paragraph ) ) {
                $new_content .= $paragraph . $closing_p;
            }
            if ( $index == $middle_index ) {
                $new_content .= $ad_html;
            }
        }
        
        return $new_content;
    }
}
