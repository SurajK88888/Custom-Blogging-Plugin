<?php
namespace CBP\email;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Queue Manager Class
 * 
 * Handles database operations for the email queue and logs.
 */
class QueueManager {

    /**
     * Get the queue table name.
     */
    private static function get_queue_table() {
        global $wpdb;
        return $wpdb->prefix . 'cbp_email_queue';
    }

    /**
     * Get the logs table name.
     */
    private static function get_log_table() {
        global $wpdb;
        return $wpdb->prefix . 'cbp_email_logs';
    }

    /**
     * Add multiple emails to the queue.
     * 
     * @param int   $post_id    The blog post ID.
     * @param array $recipients Array of email addresses.
     */
    public static function add_to_queue( $post_id, $recipients ) {
        global $wpdb;
        $table = self::get_queue_table();

        // Standardize time
        $scheduled_at = current_time( 'mysql' );

        // We use bulk insertion to optimize DB calls. 
        // WPDB doesn't have a native bulk insert helper, so we construct it securely.
        
        $placeholders = [];
        $values       = [];
        
        foreach ( $recipients as $email ) {
            $email = sanitize_email( $email );
            if ( is_email( $email ) ) {
                $placeholders[] = '(%d, %s, %s, %d, %s)';
                array_push( $values, $post_id, $email, 'pending', 0, $scheduled_at );
            }
        }

        if ( empty( $values ) ) {
            return false;
        }

        // Limit bulk inserts to chunks of 500 to avoid query size limits
        $chunks = array_chunk( $placeholders, 500 );
        $value_chunks = array_chunk( $values, 500 * 5 );

        foreach ( $chunks as $index => $chunk ) {
            $query = "INSERT INTO {$table} (post_id, recipient, status, attempt, scheduled_at) VALUES " . implode( ', ', $chunk );
            
            // Because values array contains exactly what placeholders need, we prepare dynamically.
            $wpdb->query( $wpdb->prepare( $query, $value_chunks[ $index ] ) );
        }

        return true;
    }

    /**
     * Get a batch of pending emails.
     * 
     * @param int $limit Number of emails to retrieve.
     * @return array Array of objects.
     */
    public static function get_batch( $limit = 50 ) {
        global $wpdb;
        $table = self::get_queue_table();

        // Grab emails that are pending, or failed but attempt < 3.
        $query = $wpdb->prepare(
            "SELECT * FROM {$table} 
             WHERE status = 'pending' OR (status = 'failed' AND attempt < 3) 
             ORDER BY scheduled_at ASC 
             LIMIT %d",
            $limit
        );

        return $wpdb->get_results( $query );
    }

    /**
     * Update an item's status in the queue.
     * 
     * @param int    $id     Queue row ID.
     * @param string $status New status ('processing', 'failed').
     */
    public static function update_status( $id, $status ) {
        global $wpdb;
        $table = self::get_queue_table();

        if ( 'failed' === $status ) {
            // Increment attempt if failed
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$table} SET status = %s, attempt = attempt + 1 WHERE id = %d",
                $status, $id
            ) );
        } else {
            $wpdb->update(
                $table,
                [ 'status' => $status ],
                [ 'id' => $id ],
                [ '%s' ],
                [ '%d' ]
            );
        }
    }

    /**
     * Remove an item from the queue (usually after success)
     * and log it.
     * 
     * @param object $item Queue row object.
     */
    public static function log_and_remove( $item ) {
        global $wpdb;
        $queue_table = self::get_queue_table();
        $log_table   = self::get_log_table();

        // 1. Insert into logs
        $wpdb->insert(
            $log_table,
            [
                'post_id'    => $item->post_id,
                'recipient'  => $item->recipient,
                'status'     => 'sent',
                'created_at' => current_time( 'mysql' ),
            ],
            [ '%d', '%s', '%s', '%s' ]
        );

        // 2. Delete from queue
        $wpdb->delete(
            $queue_table,
            [ 'id' => $item->id ],
            [ '%d' ]
        );
    }
}
