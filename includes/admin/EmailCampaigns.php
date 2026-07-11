<?php
namespace CBP\admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Email Campaigns UI
 */
class EmailCampaigns {

    const PAGE_SLUG = 'cbp-email-campaigns';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_submenu' ], 20 );
        add_action( 'admin_init', [ __CLASS__, 'handle_form_submission' ] );
    }

    public static function add_submenu() {
        add_submenu_page(
            \CBP\admin\Dashboard::PAGE_SLUG,
            __( 'Email Campaigns', 'custom-blog-pro' ),
            __( 'Email Campaigns', 'custom-blog-pro' ),
            'manage_options',
            self::PAGE_SLUG,
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function render_page() {
        $template = CBP_PLUGIN_DIR . 'templates/admin/email-campaigns.php';
        if ( file_exists( $template ) ) {
            include $template;
        }
    }

    public static function handle_form_submission() {
        if ( ! isset( $_POST['cbp_email_campaign_nonce'] ) || ! wp_verify_nonce( $_POST['cbp_email_campaign_nonce'], 'cbp_send_campaign' ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $subject = sanitize_text_field( $_POST['cbp_campaign_subject'] ?? '' );
        $body    = wp_kses_post( $_POST['cbp_campaign_body'] ?? '' );
        $emails  = sanitize_textarea_field( $_POST['cbp_campaign_emails'] ?? '' );

        if ( empty( $subject ) || empty( $body ) || empty( $emails ) ) {
            add_settings_error( 'cbp_campaign', 'empty_fields', __( 'All fields are required.', 'custom-blog-pro' ) );
            return;
        }

        // Create a hidden post to store the campaign data
        $post_id = wp_insert_post([
            'post_title'   => $subject,
            'post_content' => $body,
            'post_status'  => 'private',
            'post_type'    => 'cbp_campaign',
        ]);

        if ( ! is_wp_error( $post_id ) && $post_id ) {
            // Queue emails
            $email_list = array_map( 'trim', explode( ',', $emails ) );
            $email_list = array_filter( $email_list, 'is_email' );

            if ( ! empty( $email_list ) ) {
                global $wpdb;
                $table = $wpdb->prefix . 'cbp_email_queue';
                $now   = current_time( 'mysql' );

                foreach ( $email_list as $recipient ) {
                    $wpdb->insert( $table, [
                        'post_id'      => $post_id,
                        'recipient'    => $recipient,
                        'status'       => 'pending',
                        'attempt'      => 0,
                        'scheduled_at' => $now,
                    ] );
                }
                add_settings_error( 'cbp_campaign', 'campaign_queued', sprintf( __( 'Campaign queued for %d recipients.', 'custom-blog-pro' ), count( $email_list ) ), 'success' );
            } else {
                add_settings_error( 'cbp_campaign', 'invalid_emails', __( 'No valid emails provided.', 'custom-blog-pro' ) );
            }
        }
    }
}
