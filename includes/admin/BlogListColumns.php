<?php
namespace CBP\admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Blog List Columns — Admin Post List Enhancement
 *
 * Adds a "Status" column to the cbp_blog admin list table.
 * The status cell contains a dropdown that submits via AJAX,
 * allowing admins to change post status without leaving the list.
 *
 * Reusable pattern: Add new columns to the $columns array and
 * handle their output in render_column(). Each AJAX action
 * follows the same nonce-verify → cap-check → update → respond pattern.
 *
 * @package Custom_Blog_Pro
 */
class BlogListColumns {

    const POST_TYPE  = 'cbp_blog';
    const AJAX_ACTION = 'cbp_update_post_status';

    /**
     * Initialize hooks.
     */
    public static function init() {
        // Add custom columns
        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', [ __CLASS__, 'add_columns' ] );

        // Render column content
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', [ __CLASS__, 'render_column' ], 10, 2 );

        // Make Status column sortable
        add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', [ __CLASS__, 'sortable_columns' ] );

        // AJAX handler for inline status update (logged-in admins only)
        add_action( 'wp_ajax_' . self::AJAX_ACTION, [ __CLASS__, 'ajax_update_status' ] );

        // Enqueue the inline script only on the CBP post list screen
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_column_assets' ] );
    }

    /**
     * Add the Status column to the list table.
     *
     * @param array $columns Existing columns.
     * @return array
     */
    public static function add_columns( $columns ) {
        // Insert "Status" column after the "Title" column
        $new_columns = [];
        foreach ( $columns as $key => $label ) {
            $new_columns[ $key ] = $label;
            if ( 'title' === $key ) {
                $new_columns['cbp_status'] = __( 'Status', 'custom-blog-pro' );
            }
        }
        return $new_columns;
    }

    /**
     * Render the content of each custom column.
     *
     * @param string $column  Column key.
     * @param int    $post_id Post ID.
     */
    public static function render_column( $column, $post_id ) {
        if ( 'cbp_status' !== $column ) {
            return;
        }

        $current_status = get_post_status( $post_id );

        // Available statuses an admin can transition between
        $statuses = [
            'publish' => __( 'Published', 'custom-blog-pro' ),
            'pending' => __( 'Pending Review', 'custom-blog-pro' ),
            'draft'   => __( 'Draft', 'custom-blog-pro' ),
            'private' => __( 'Private', 'custom-blog-pro' ),
        ];

        $nonce = wp_create_nonce( 'cbp_update_status_' . $post_id );

        // Status badge + dropdown inside a form-like wrapper
        ?>
        <div class="cbp-status-cell" data-post-id="<?php echo esc_attr( $post_id ); ?>">

            <select
                class="cbp-status-select"
                data-post-id="<?php echo esc_attr( $post_id ); ?>"
                data-nonce="<?php echo esc_attr( $nonce ); ?>"
                data-original="<?php echo esc_attr( $current_status ); ?>"
                aria-label="<?php esc_attr_e( 'Change post status', 'custom-blog-pro' ); ?>"
            >
                <?php foreach ( $statuses as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <span class="cbp-status-indicator cbp-status-<?php echo esc_attr( $current_status ); ?>"></span>
            <span class="cbp-status-spinner" style="display:none;">&#8987;</span>

        </div>
        <?php
    }

    /**
     * Register the Status column as sortable.
     *
     * @param array $sortable Existing sortable columns.
     * @return array
     */
    public static function sortable_columns( $sortable ) {
        $sortable['cbp_status'] = 'post_status';
        return $sortable;
    }

    /**
     * AJAX handler: Update post status.
     * Verifies nonce, checks capability, then updates the post.
     */
    public static function ajax_update_status() {
        // 1. Nonce verification
        $post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
        if ( ! $post_id || ! check_ajax_referer( 'cbp_update_status_' . $post_id, 'nonce', false ) ) {
            wp_send_json_error( [ 'message' => __( 'Security check failed.', 'custom-blog-pro' ) ], 403 );
        }

        // 2. Capability check
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have permission to edit this post.', 'custom-blog-pro' ) ], 403 );
        }

        // 3. Validate new status
        $new_status      = isset( $_POST['new_status'] ) ? sanitize_key( $_POST['new_status'] ) : '';
        $allowed_statuses = [ 'publish', 'pending', 'draft', 'private' ];
        if ( ! in_array( $new_status, $allowed_statuses, true ) ) {
            wp_send_json_error( [ 'message' => __( 'Invalid status value.', 'custom-blog-pro' ) ], 400 );
        }

        // 4. Update the post status
        $result = wp_update_post(
            [
                'ID'          => $post_id,
                'post_status' => $new_status,
            ],
            true // return WP_Error on failure
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( [ 'message' => $result->get_error_message() ], 500 );
        }

        // Return the new status label for the UI to update
        $labels = [
            'publish' => __( 'Published', 'custom-blog-pro' ),
            'pending' => __( 'Pending Review', 'custom-blog-pro' ),
            'draft'   => __( 'Draft', 'custom-blog-pro' ),
            'private' => __( 'Private', 'custom-blog-pro' ),
        ];

        wp_send_json_success( [
            'new_status' => $new_status,
            'label'      => $labels[ $new_status ] ?? $new_status,
        ] );
    }

    /**
     * Enqueue the inline JS and CSS only on the CBP post list screen.
     *
     * @param string $hook Current admin page hook.
     */
    public static function enqueue_column_assets( $hook ) {
        if ( 'edit.php' !== $hook ) {
            return;
        }
        // phpcs:ignore WordPress.Security.NonceVerification
        if ( ! isset( $_GET['post_type'] ) || self::POST_TYPE !== $_GET['post_type'] ) {
            return;
        }

        // Inline CSS for the status column
        $css = '
            .cbp-status-cell { display: inline-flex; align-items: center; gap: 6px; }
            .cbp-status-select { padding: 4px 8px; border-radius: 6px; border: 1px solid #c3c4c7; font-size: 13px; cursor: pointer; }
            .cbp-status-indicator { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
            .cbp-status-publish  { background: #22c55e; }
            .cbp-status-pending  { background: #f59e0b; }
            .cbp-status-draft    { background: #94a3b8; }
            .cbp-status-private  { background: #a855f7; }
            .cbp-status-spinner  { font-size: 16px; animation: cbp-spin 1s linear infinite; }
            @keyframes cbp-spin { to { transform: rotate(360deg); } }
        ';
        wp_add_inline_style( 'list-tables', $css );

        // Inline JS — AJAX status update on select change
        $js = '
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".cbp-status-select").forEach(function (select) {
                select.addEventListener("change", function () {
                    var cell    = select.closest(".cbp-status-cell");
                    var spinner = cell.querySelector(".cbp-status-spinner");
                    var indicator = cell.querySelector(".cbp-status-indicator");
                    var postId  = select.dataset.postId;
                    var nonce   = select.dataset.nonce;
                    var newStatus = select.value;

                    // Show loading state
                    select.disabled = true;
                    spinner.style.display = "inline";

                    var data = new URLSearchParams();
                    data.append("action", "' . self::AJAX_ACTION . '");
                    data.append("post_id", postId);
                    data.append("nonce", nonce);
                    data.append("new_status", newStatus);

                    fetch(ajaxurl, { method: "POST", body: data })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            spinner.style.display = "none";
                            select.disabled = false;
                            if (res.success) {
                                // Update badge color
                                indicator.className = "cbp-status-indicator cbp-status-" + res.data.new_status;
                                select.dataset.original = res.data.new_status;
                            } else {
                                alert(res.data.message || "Update failed.");
                                select.value = select.dataset.original;
                            }
                        })
                        .catch(function () {
                            spinner.style.display = "none";
                            select.disabled = false;
                            alert("Network error. Please try again.");
                            select.value = select.dataset.original;
                        });
                });
            });
        });
        ';
        wp_add_inline_script( 'common', $js );
    }
}
