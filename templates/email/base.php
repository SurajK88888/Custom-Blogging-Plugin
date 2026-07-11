<?php
/**
 * Email Base Template
 * 
 * Variables available:
 * $title, $excerpt, $permalink, $thumbnail, $site_name, $author_name
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html( $title ); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; color: #1f2937; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background-color: #2563eb; color: #ffffff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .thumbnail { width: 100%; height: auto; display: block; }
        .content { padding: 30px; }
        .post-title { font-size: 22px; font-weight: 700; margin-top: 0; color: #111827; }
        .post-meta { font-size: 14px; color: #6b7280; margin-bottom: 20px; }
        .post-excerpt { font-size: 16px; color: #4b5563; margin-bottom: 30px; }
        .btn-read { display: inline-block; background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; text-align: center; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1><?php echo esc_html( $site_name ); ?></h1>
        </div>
        
        <?php if ( ! empty( $thumbnail ) ) : ?>
            <img src="<?php echo esc_url( $thumbnail ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="thumbnail" />
        <?php endif; ?>
        
        <div class="content">
            <h2 class="post-title"><?php echo esc_html( $title ); ?></h2>
            <div class="post-meta">Published by <?php echo esc_html( $author_name ); ?></div>
            
            <div class="post-excerpt">
                <?php echo esc_html( $excerpt ); ?>
            </div>
            
            <div style="text-align: center;">
                <a href="<?php echo esc_url( $permalink ); ?>" class="btn-read">Read Full Article</a>
            </div>
        </div>
        
        <div class="footer">
            &copy; <?php echo date('Y'); ?> <?php echo esc_html( $site_name ); ?>. All rights reserved.
        </div>
    </div>
</body>
</html>
