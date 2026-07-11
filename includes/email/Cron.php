<?php
namespace CBP\email;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Cron Integration Class
 * 
 * Handles WP Cron scheduling and executing the email queue.
 */
class Cron {

    const HOOK = 'cbp_process_email_queue';

    /**
     * Initialize cron hooks.
     */
    public static function init() {
        // Register custom cron intervals
        add_filter( 'cron_schedules', [ __CLASS__, 'add_cron_interval' ] );
        
        // Hook the processing function to our custom action
        add_action( self::HOOK, [ __CLASS__, 'process_queue' ] );
    }

    /**
     * Add a custom 5-minute interval for processing emails.
     */
    public static function add_cron_interval( $schedules ) {
        if ( ! isset( $schedules['cbp_five_minutes'] ) ) {
            $schedules['cbp_five_minutes'] = [
                'interval' => 5 * MINUTE_IN_SECONDS,
                'display'  => esc_html__( 'Every 5 Minutes (CBP)', 'custom-blog-pro' ),
            ];
        }
        return $schedules;
    }

    /**
     * Schedule the event if it isn't already.
     * Called during plugin activation.
     */
    public static function schedule_event() {
        if ( ! wp_next_scheduled( self::HOOK ) ) {
            wp_schedule_event( time(), 'cbp_five_minutes', self::HOOK );
        }
    }

    /**
     * Clear the scheduled event.
     * Called during plugin deactivation.
     */
    public static function clear_schedule() {
        $timestamp = wp_next_scheduled( self::HOOK );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, self::HOOK );
        }
    }

    /**
     * Process the queue.
     * Runs on the scheduled cron interval.
     */
    public static function process_queue() {
        // Get a batch of 50 emails to prevent timeouts
        $batch = QueueManager::get_batch( 50 );

        if ( empty( $batch ) ) {
            return;
        }

        foreach ( $batch as $item ) {
            // Mark as processing
            QueueManager::update_status( $item->id, 'processing' );

            $post = get_post( $item->post_id );
            
            if ( ! $post || 'publish' !== $post->post_status ) {
                // Post no longer exists or isn't published. Fail silently.
                QueueManager::update_status( $item->id, 'failed' );
                continue;
            }

            // Prepare email content
            $subject = esc_html__( 'New Post: ', 'custom-blog-pro' ) . $post->post_title;
            $body    = Templates::get_email_body( $post );

            // Send email
            $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
            $sent = wp_mail( $item->recipient, $subject, $body, $headers );

            if ( $sent ) {
                QueueManager::log_and_remove( $item );
            } else {
                QueueManager::update_status( $item->id, 'failed' );
            }
        }
    }
}
