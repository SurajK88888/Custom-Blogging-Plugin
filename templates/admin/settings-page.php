<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Verify user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

// Show settings errors
settings_errors( 'cbp_settings' );
?>

<div class="cbp-admin-wrap">
    
    <div class="cbp-admin-header">
        <h1><?php esc_html_e( 'Custom Blog Pro Settings', 'custom-blog-pro' ); ?></h1>
    </div>

    <div class="cbp-admin-layout">
        
        <!-- Sidebar Navigation -->
        <nav class="cbp-tabs-nav">
            <a href="#tab-appearance" class="active"><?php esc_html_e( 'Appearance', 'custom-blog-pro' ); ?></a>
            <a href="#tab-smtp"><?php esc_html_e( 'SMTP Config', 'custom-blog-pro' ); ?></a>
            <a href="#tab-ads"><?php esc_html_e( 'Advertisements', 'custom-blog-pro' ); ?></a>
            <a href="#tab-tools"><?php esc_html_e( 'Tools', 'custom-blog-pro' ); ?></a>
        </nav>

        <!-- Main Form Content -->
        <form action="options.php" method="post" class="cbp-tabs-content" enctype="multipart/form-data">
            <?php 
            // Outputs nonce, action, and option_page fields for a settings page.
            settings_fields( \CBP\admin\SettingsRegistry::GROUP ); 
            ?>

            <!-- Appearance Tab -->
            <div id="tab-appearance" class="cbp-tab-pane active">
                <h2><?php esc_html_e( 'Appearance Settings', 'custom-blog-pro' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cbp_primary_color"><?php esc_html_e( 'Primary Color', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="color" id="cbp_primary_color" name="cbp_primary_color" value="<?php echo esc_attr( get_option('cbp_primary_color', '#2563eb') ); ?>" style="width:50px; height: 35px; padding:0; border:none; border-radius:4px;" />
                            <p class="description"><?php esc_html_e( 'Used for buttons, progress bars, and badges.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_secondary_color"><?php esc_html_e( 'Secondary Color', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="color" id="cbp_secondary_color" name="cbp_secondary_color" value="<?php echo esc_attr( get_option('cbp_secondary_color', '#1e40af') ); ?>" style="width:50px; height: 35px; padding:0; border:none; border-radius:4px;" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_header_bg_color"><?php esc_html_e( 'Blog Header Background Color', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="color" id="cbp_header_bg_color" name="cbp_header_bg_color" value="<?php echo esc_attr( get_option('cbp_header_bg_color', '#6c63ff') ); ?>" style="width:50px; height: 35px; padding:0; border:none; border-radius:4px;" />
                            <p class="description"><?php esc_html_e( 'Background color of the blog post title header banner on single post pages.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_border_radius"><?php esc_html_e( 'Border Radius', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_border_radius" name="cbp_border_radius" value="<?php echo esc_attr( get_option('cbp_border_radius', '12px') ); ?>" />
                            <p class="description"><?php esc_html_e( 'Example: 8px or 1rem', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_font_family"><?php esc_html_e( 'Typography (Google Font)', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_font_family" name="cbp_font_family">
                                <?php
                                $fonts = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Outfit'];
                                $current_font = get_option('cbp_font_family', 'Inter');
                                foreach ( $fonts as $font ) {
                                    echo '<option value="' . esc_attr( $font ) . '" ' . selected( $current_font, $font, false ) . '>' . esc_html( $font ) . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_card_shadow"><?php esc_html_e( 'Card Shadow', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_card_shadow" name="cbp_card_shadow" value="<?php echo esc_attr( get_option('cbp_card_shadow', '0 4px 6px -1px rgba(0, 0, 0, 0.1)') ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_layout_sidebar"><?php esc_html_e( 'Sidebar Layout', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_layout_sidebar" name="cbp_layout_sidebar">
                                <option value="right" <?php selected( get_option('cbp_layout_sidebar', 'right'), 'right' ); ?>><?php esc_html_e( 'Right Sidebar', 'custom-blog-pro' ); ?></option>
                                <option value="left" <?php selected( get_option('cbp_layout_sidebar', 'right'), 'left' ); ?>><?php esc_html_e( 'Left Sidebar', 'custom-blog-pro' ); ?></option>
                                <option value="none" <?php selected( get_option('cbp_layout_sidebar', 'right'), 'none' ); ?>><?php esc_html_e( 'No Sidebar (Full Width)', 'custom-blog-pro' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_dark_mode"><?php esc_html_e( 'Enable Dark Mode', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_dark_mode" name="cbp_dark_mode">
                                <option value="0" <?php selected( get_option('cbp_dark_mode', '0'), '0' ); ?>><?php esc_html_e( 'Light Mode (Default)', 'custom-blog-pro' ); ?></option>
                                <option value="1" <?php selected( get_option('cbp_dark_mode', '0'), '1' ); ?>><?php esc_html_e( 'Dark Mode', 'custom-blog-pro' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Automatically applies a dark theme wrapper to the frontend components.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- SMTP Tab -->
            <div id="tab-smtp" class="cbp-tab-pane">
                <h2><?php esc_html_e( 'SMTP Configuration', 'custom-blog-pro' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cbp_smtp_enabled"><?php esc_html_e( 'Enable Custom SMTP', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_smtp_enabled" name="cbp_smtp_enabled">
                                <option value="1" <?php selected( get_option('cbp_smtp_enabled', '0'), '1' ); ?>><?php esc_html_e( 'Enabled', 'custom-blog-pro' ); ?></option>
                                <option value="0" <?php selected( get_option('cbp_smtp_enabled', '0'), '0' ); ?>><?php esc_html_e( 'Disabled', 'custom-blog-pro' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_host"><?php esc_html_e( 'SMTP Host', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_smtp_host" name="cbp_smtp_host" value="<?php echo esc_attr( get_option('cbp_smtp_host') ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_port"><?php esc_html_e( 'SMTP Port', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_smtp_port" name="cbp_smtp_port" value="<?php echo esc_attr( get_option('cbp_smtp_port', '587') ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_username"><?php esc_html_e( 'SMTP Username', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_smtp_username" name="cbp_smtp_username" value="<?php echo esc_attr( get_option('cbp_smtp_username') ); ?>" autocomplete="off" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_password"><?php esc_html_e( 'SMTP Password', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="password" id="cbp_smtp_password" name="cbp_smtp_password" value="<?php echo esc_attr( get_option('cbp_smtp_password') ); ?>" autocomplete="new-password" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_encryption"><?php esc_html_e( 'Encryption', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_smtp_encryption" name="cbp_smtp_encryption">
                                <option value="tls" <?php selected( get_option('cbp_smtp_encryption', 'tls'), 'tls' ); ?>>TLS</option>
                                <option value="ssl" <?php selected( get_option('cbp_smtp_encryption', 'tls'), 'ssl' ); ?>>SSL</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_from_email"><?php esc_html_e( 'From Email', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_smtp_from_email" name="cbp_smtp_from_email" value="<?php echo esc_attr( get_option('cbp_smtp_from_email') ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_smtp_from_name"><?php esc_html_e( 'From Name', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <input type="text" id="cbp_smtp_from_name" name="cbp_smtp_from_name" value="<?php echo esc_attr( get_option('cbp_smtp_from_name') ); ?>" />
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Advertisements Tab -->
            <div id="tab-ads" class="cbp-tab-pane">
                <h2><?php esc_html_e( 'Advertisement Config', 'custom-blog-pro' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cbp_ads_enabled"><?php esc_html_e( 'Enable Ads', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <select id="cbp_ads_enabled" name="cbp_ads_enabled">
                                <option value="1" <?php selected( get_option('cbp_ads_enabled', '1'), '1' ); ?>><?php esc_html_e( 'Enabled', 'custom-blog-pro' ); ?></option>
                                <option value="0" <?php selected( get_option('cbp_ads_enabled', '1'), '0' ); ?>><?php esc_html_e( 'Disabled', 'custom-blog-pro' ); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_ad_top"><?php esc_html_e( 'Top Banner Ad', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <textarea id="cbp_ad_top" name="cbp_ad_top"><?php echo esc_textarea( get_option('cbp_ad_top') ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'HTML or AdSense code. Appears above the single blog content.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_ad_middle"><?php esc_html_e( 'Middle Ad', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <textarea id="cbp_ad_middle" name="cbp_ad_middle"><?php echo esc_textarea( get_option('cbp_ad_middle') ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'HTML or AdSense code. Injected halfway through the article.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_ad_bottom"><?php esc_html_e( 'Bottom Banner Ad', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <textarea id="cbp_ad_bottom" name="cbp_ad_bottom"><?php echo esc_textarea( get_option('cbp_ad_bottom') ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'HTML or AdSense code. Appears below the single blog content.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cbp_ad_sidebar"><?php esc_html_e( 'Sidebar Ad (Widget)', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <textarea id="cbp_ad_sidebar" name="cbp_ad_sidebar"><?php echo esc_textarea( get_option('cbp_ad_sidebar') ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Add the CBP Ad Widget to your sidebar to display this code.', 'custom-blog-pro' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Tools Tab -->
            <div id="tab-tools" class="cbp-tab-pane">
                <h2><?php esc_html_e( 'Import / Export Tools', 'custom-blog-pro' ); ?></h2>
                <p><?php esc_html_e( 'Migrate your CBP settings to another installation easily.', 'custom-blog-pro' ); ?></p>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Export Settings', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <a href="<?php echo esc_url( wp_nonce_url( admin_url('admin-post.php?action=cbp_export_settings'), 'cbp_export_nonce' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Download JSON Export', 'custom-blog-pro' ); ?></a>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Note: Import requires a separate multipart/form-data form -->
            <div id="tab-tools-import" class="cbp-tab-pane" style="display:none;" data-linked-tab="#tab-tools">
                <hr>
                <h3><?php esc_html_e( 'Import Settings', 'custom-blog-pro' ); ?></h3>
                <!-- The import form must be handled carefully outside the main settings form, but we can visually place it here and handle the submit button via JS, or just put the markup here and let the backend redirect back. Actually, we'll put the form right inside the tab, but since we are already inside a <form>, we can't nest forms. 
                For MVP, we will use a file input in the main form and intercept the save in our Tools class. -->
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cbp_import_file"><?php esc_html_e( 'Import JSON File', 'custom-blog-pro' ); ?></label></th>
                        <td>
                            <!-- To support file upload on options.php, form must have enctype="multipart/form-data" -->
                            <input type="file" id="cbp_import_file" name="cbp_import_file" accept=".json" />
                            <p class="description"><?php esc_html_e( 'Warning: Importing will overwrite your current settings.', 'custom-blog-pro' ); ?></p>
                            <?php submit_button( __( 'Import Now', 'custom-blog-pro' ), 'secondary', 'cbp_do_import', false ); ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Submit Button -->
            <div class="cbp-submit-wrap">
                <button type="submit" class="cbp-btn"><?php esc_html_e( 'Save All Settings', 'custom-blog-pro' ); ?></button>
            </div>

        </form>

    </div>
</div>
