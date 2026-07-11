<div class="wrap">
    <h1><?php esc_html_e( 'Bulk Email Campaigns', 'custom-blog-pro' ); ?></h1>
    
    <?php settings_errors( 'cbp_campaign' ); ?>

    <div class="cbp-card" style="max-width: 800px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <p><?php esc_html_e( 'Compose a custom email and queue it to a list of recipients.', 'custom-blog-pro' ); ?></p>
        
        <form method="post" action="">
            <?php wp_nonce_field( 'cbp_send_campaign', 'cbp_email_campaign_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="cbp_campaign_subject"><?php esc_html_e( 'Email Subject', 'custom-blog-pro' ); ?></label></th>
                    <td>
                        <input type="text" id="cbp_campaign_subject" name="cbp_campaign_subject" class="regular-text" required />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cbp_campaign_body"><?php esc_html_e( 'Email Body', 'custom-blog-pro' ); ?></label></th>
                    <td>
                        <?php 
                            wp_editor( '', 'cbp_campaign_body', [
                                'textarea_name' => 'cbp_campaign_body',
                                'media_buttons' => true,
                                'textarea_rows' => 10,
                            ] );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cbp_campaign_emails"><?php esc_html_e( 'Recipients (Comma Separated)', 'custom-blog-pro' ); ?></label></th>
                    <td>
                        <textarea id="cbp_campaign_emails" name="cbp_campaign_emails" rows="4" class="large-text" placeholder="user1@example.com, user2@example.com" required></textarea>
                    </td>
                </tr>
            </table>
            
            <?php submit_button( __( 'Queue Campaign', 'custom-blog-pro' ) ); ?>
        </form>
    </div>
</div>
