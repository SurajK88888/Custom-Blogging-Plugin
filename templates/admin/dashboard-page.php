<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Variables $kpis and $popular_posts are provided by includes/admin/Dashboard.php
?>
<div class="cbp-dash-wrap">
    
    <div class="cbp-dash-header">
        <h1><?php esc_html_e( 'Dashboard Overview', 'custom-blog-pro' ); ?></h1>
    </div>

    <!-- KPI Grid -->
    <div class="cbp-kpi-grid">
        <div class="cbp-kpi-card">
            <span class="cbp-kpi-title"><?php esc_html_e( 'Total Blogs', 'custom-blog-pro' ); ?></span>
            <span class="cbp-kpi-value"><?php echo number_format_i18n( $kpis['total_blogs'] ?? 0 ); ?></span>
        </div>
        <div class="cbp-kpi-card">
            <span class="cbp-kpi-title"><?php esc_html_e( 'Total Blog Views', 'custom-blog-pro' ); ?></span>
            <span class="cbp-kpi-value"><?php echo number_format_i18n( $kpis['total_views'] ?? 0 ); ?></span>
        </div>
        <div class="cbp-kpi-card">
            <span class="cbp-kpi-title"><?php esc_html_e( 'Ad Clicks (CTR)', 'custom-blog-pro' ); ?></span>
            <span class="cbp-kpi-value">
                <?php echo number_format_i18n( $kpis['ad_clicks'] ?? 0 ); ?> 
                <small style="font-size: 14px; color: #10b981;">(<?php echo esc_html( $kpis['ad_ctr'] ?? 0 ); ?>%)</small>
            </span>
        </div>
        <div class="cbp-kpi-card">
            <span class="cbp-kpi-title"><?php esc_html_e( 'Emails Sent', 'custom-blog-pro' ); ?></span>
            <span class="cbp-kpi-value"><?php echo number_format_i18n( $kpis['total_emails'] ?? 0 ); ?></span>
        </div>
        <div class="cbp-kpi-card" style="border-left: 4px solid #f59e0b;">
            <span class="cbp-kpi-title"><?php esc_html_e( 'Pending Review', 'custom-blog-pro' ); ?></span>
            <span class="cbp-kpi-value" style="color: #f59e0b;">
                <?php echo number_format_i18n( $pending_count ?? 0 ); ?>
            </span>
            <?php if ( ! empty( $pending_count ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=cbp_blog&post_status=pending' ) ); ?>" style="font-size:12px; color:#f59e0b; text-decoration:underline; margin-top:4px; display:block;">
                <?php esc_html_e( 'Review Posts →', 'custom-blog-pro' ); ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Layout: Chart & Popular Posts -->
    <div class="cbp-dash-layout">
        
        <!-- Chart Panel -->
        <div class="cbp-dash-panel">
            <div class="cbp-card">
                <h3><?php esc_html_e( 'Engagement Overview', 'custom-blog-pro' ); ?></h3>
                <div class="cbp-chart-container" style="position: relative; height:300px;">
                    <canvas id="cbpOverviewChart"></canvas>
                </div>
            </div>

            <div class="cbp-card">
                <h3><?php esc_html_e( 'Device Breakdown', 'custom-blog-pro' ); ?></h3>
                <div class="cbp-chart-container" style="position: relative; height:300px;">
                    <canvas id="cbpDeviceChart"></canvas>
                </div>
            </div>

            <div class="cbp-card">
                <h3><?php esc_html_e( 'Browser Breakdown', 'custom-blog-pro' ); ?></h3>
                <div class="cbp-chart-container" style="position: relative; height:300px;">
                    <canvas id="cbpBrowserChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Popular Posts Panel -->
        <div class="cbp-dash-panel">
            <h2><?php esc_html_e( 'Popular Posts', 'custom-blog-pro' ); ?></h2>
            <?php if ( ! empty( $popular_posts ) ) : ?>
                <table class="cbp-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Title', 'custom-blog-pro' ); ?></th>
                            <th style="text-align: right;"><?php esc_html_e( 'Views', 'custom-blog-pro' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $popular_posts as $post ) : ?>
                            <tr>
                                <td>
                                    <strong><a href="<?php echo esc_url( get_edit_post_link( $post['ID'] ) ); ?>"><?php echo esc_html( $post['post_title'] ); ?></a></strong>
                                </td>
                                <td style="text-align: right;">
                                    <span class="cbp-badge" style="background:#eff6ff; color:#2563eb; padding:2px 8px; border-radius:12px; font-size:12px; font-weight:600;">
                                        <?php echo number_format_i18n( $post['views'] ); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="color: #64748b;"><?php esc_html_e( 'No view data available yet.', 'custom-blog-pro' ); ?></p>
            <?php endif; ?>
        </div>

    </div>

</div>
