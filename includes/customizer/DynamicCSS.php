<?php
namespace CBP\customizer;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Dynamic CSS Engine
 * 
 * Outputs dynamic CSS variables based on the admin settings.
 */
class DynamicCSS {

    /**
     * Initialize hook.
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'output_css_variables' ], 20 );
    }

    /**
     * Output inline CSS variables to override defaults in frontend.css.
     */
    public static function output_css_variables() {
        $primary   = get_option( 'cbp_primary_color', '#2563eb' );
        $secondary = get_option( 'cbp_secondary_color', '#1e40af' );
        $radius    = get_option( 'cbp_border_radius', '12px' );
        $font      = get_option( 'cbp_font_family', 'Inter' );
        $shadow    = get_option( 'cbp_card_shadow', '0 4px 6px -1px rgba(0, 0, 0, 0.1)' );
        $dark_mode = get_option( 'cbp_dark_mode', '0' );

        // Enqueue Google Font if needed
        if ( in_array( $font, ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Outfit'] ) ) {
            $font_url = 'https://fonts.googleapis.com/css2?family=' . urlencode( $font ) . ':wght@400;500;600;700&display=swap';
            wp_enqueue_style( 'cbp-google-font', $font_url, [], null );
        }

        // Base variables
        $custom_css = "
            :root {
                --cbp-primary-color: " . esc_attr( $primary ) . ";
                --cbp-secondary-color: " . esc_attr( $secondary ) . ";
                --cbp-border-radius: " . esc_attr( $radius ) . ";
                --cbp-font-family: '" . esc_attr( $font ) . "', sans-serif;
                --cbp-shadow: " . esc_attr( $shadow ) . ";
                
                /* Default Light Mode Colors */
                --cbp-bg-color: #ffffff;
                --cbp-text-color: #334155;
                --cbp-border-color: #e2e8f0;
            }
        ";

        // Dark Mode Overrides
        if ( '1' === $dark_mode ) {
            $custom_css .= "
                body.single-cbp_blog, .cbp-blog-card {
                    --cbp-bg-color: #0f172a;
                    --cbp-text-color: #f8fafc;
                    --cbp-border-color: #334155;
                    background-color: var(--cbp-bg-color) !important;
                    color: var(--cbp-text-color) !important;
                }
                .cbp-blog-card {
                    border-color: var(--cbp-border-color) !important;
                }
            ";
        }

        // Attach this inline CSS to our main frontend stylesheet
        wp_add_inline_style( 'cbp-frontend-style', wp_strip_all_tags( $custom_css ) );
    }
}
