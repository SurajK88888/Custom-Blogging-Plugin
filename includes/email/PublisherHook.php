<?php
namespace CBP\email;

use CBP\post\CPT;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Publisher Hook Class
 * 
 * Intercepts the post transition to "publish" and populates the email queue.
 */
class PublisherHook {

    /**
     * Initialize hook.
     */
    public static function init() {
        add_action( 'transition_post_status', [ __CLASS__, 'on_post_publish' ], 10, 3 );
    }

    /**
     * Handle post status transition.
     * 
     * @param string   $new_status
     * @param string   $old_status
     * @param \WP_Post $post
     */
    public static function on_post_publish( $new_status, $old_status, $post ) {
        // Only target our custom post type
        if ( CPT::POST_TYPE !== $post->post_type ) {
            return;
        }

        // Only trigger when transitioning TO publish FROM something else
        if ( 'publish' === $new_status && 'publish' !== $old_status ) {
            
            // Fetch recipients.
            // In a full implementation, we'd fetch specific lists from the Settings module.
            // For now, we fetch all users who are subscribers.
            
            $users = get_users( [
                'role'   => 'subscriber',
                'fields' => [ 'user_email' ],
            ] );

            $emails = [];
            foreach ( $users as $user ) {
                if ( is_email( $user->user_email ) ) {
                    $emails[] = $user->user_email;
                }
            }

            // We could also have a custom table of external subscribers.
            // That would be fetched and merged here.

            if ( ! empty( $emails ) ) {
                QueueManager::add_to_queue( $post->ID, $emails );
            }
        }
    }
}
