<?php
/**
 * Template: Frontend Blog Submission Form
 *
 * Loaded by the [cbp_submit_form] shortcode.
 * Variables available: $categories (array of WP_Term objects)
 *
 * @package Custom_Blog_Pro
 */

// wp_ajax_url and nonce are localized via cbpFrontendData from Assets.php
?>

<div class="cbp-submit-wrapper" id="cbp-submit-form-wrapper">

    <!-- Page Header -->
    <div class="cbp-submit-header">
        <h2 class="cbp-submit-title"><?php esc_html_e( 'Write a Blog Post', 'custom-blog-pro' ); ?></h2>
        <p class="cbp-submit-subtitle"><?php esc_html_e( 'Share your story with our community. Your post will be reviewed before publishing.', 'custom-blog-pro' ); ?></p>
    </div>

    <!-- Success / Error Message Area -->
    <div class="cbp-submit-notice" id="cbp-submit-notice" style="display:none;" role="alert" aria-live="polite"></div>

    <!-- Submission Form -->
    <form id="cbp-blog-submit-form" class="cbp-submit-form" enctype="multipart/form-data" novalidate>

        <!-- Nonce for security -->
        <?php wp_nonce_field( 'cbp_submit_blog_action', 'cbp_submit_nonce' ); ?>

        <!-- Blog Title -->
        <div class="cbp-form-group">
            <label for="cbp-blog-title" class="cbp-form-label">
                <?php esc_html_e( 'Blog Title', 'custom-blog-pro' ); ?>
                <span class="cbp-required" aria-label="required">*</span>
            </label>
            <input
                type="text"
                id="cbp-blog-title"
                name="cbp_blog_title"
                class="cbp-form-control"
                placeholder="<?php esc_attr_e( 'Enter a compelling title...', 'custom-blog-pro' ); ?>"
                maxlength="200"
                required
            />
        </div>

        <!-- Category -->
        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
        <div class="cbp-form-group">
            <label for="cbp-blog-category" class="cbp-form-label">
                <?php esc_html_e( 'Category', 'custom-blog-pro' ); ?>
            </label>
            <select id="cbp-blog-category" name="cbp_blog_category" class="cbp-form-control">
                <option value=""><?php esc_html_e( '— Select a Category —', 'custom-blog-pro' ); ?></option>
                <?php foreach ( $categories as $cat ) : ?>
                    <option value="<?php echo esc_attr( $cat->term_id ); ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Short Excerpt -->
        <div class="cbp-form-group">
            <label for="cbp-blog-excerpt" class="cbp-form-label">
                <?php esc_html_e( 'Short Description (Excerpt)', 'custom-blog-pro' ); ?>
            </label>
            <textarea
                id="cbp-blog-excerpt"
                name="cbp_blog_excerpt"
                class="cbp-form-control"
                rows="2"
                placeholder="<?php esc_attr_e( 'A brief summary of your post (shown in the blog card)...', 'custom-blog-pro' ); ?>"
                maxlength="300"
            ></textarea>
        </div>

        <!-- Blog Content -->
        <div class="cbp-form-group">
            <label for="cbp-blog-content" class="cbp-form-label">
                <?php esc_html_e( 'Blog Content', 'custom-blog-pro' ); ?>
                <span class="cbp-required" aria-label="required">*</span>
            </label>
            <textarea
                id="cbp-blog-content"
                name="cbp_blog_content"
                class="cbp-form-control cbp-content-editor"
                rows="12"
                placeholder="<?php esc_attr_e( 'Write your full blog post here...', 'custom-blog-pro' ); ?>"
                required
            ></textarea>
        </div>

        <!-- Featured Image Upload -->
        <div class="cbp-form-group">
            <label for="cbp-blog-image" class="cbp-form-label">
                <?php esc_html_e( 'Featured Image', 'custom-blog-pro' ); ?>
            </label>
            <div class="cbp-upload-area" id="cbp-upload-area">
                <div class="cbp-upload-icon">📷</div>
                <p class="cbp-upload-text"><?php esc_html_e( 'Click to upload or drag and drop', 'custom-blog-pro' ); ?></p>
                <p class="cbp-upload-hint"><?php esc_html_e( 'PNG, JPG, WEBP up to 5MB', 'custom-blog-pro' ); ?></p>
                <input
                    type="file"
                    id="cbp-blog-image"
                    name="cbp_blog_image"
                    class="cbp-file-input"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                />
            </div>
            <div class="cbp-image-preview" id="cbp-image-preview" style="display:none;">
                <img id="cbp-preview-img" src="" alt="<?php esc_attr_e( 'Preview', 'custom-blog-pro' ); ?>" />
                <button type="button" class="cbp-remove-image" id="cbp-remove-image" aria-label="<?php esc_attr_e( 'Remove image', 'custom-blog-pro' ); ?>">✕</button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="cbp-form-actions">
            <button type="submit" id="cbp-submit-btn" class="cbp-btn cbp-btn-primary">
                <span class="cbp-btn-text"><?php esc_html_e( 'Submit for Review', 'custom-blog-pro' ); ?></span>
                <span class="cbp-btn-spinner" style="display:none;" aria-hidden="true"></span>
            </button>
            <p class="cbp-submit-disclaimer">
                <?php esc_html_e( '* Your post will be reviewed by our team before being published on the site.', 'custom-blog-pro' ); ?>
            </p>
        </div>

    </form>

</div><!-- .cbp-submit-wrapper -->

<style>
/* ============================================================
   CBP Frontend Submission Form Styles
   Scoped to .cbp-submit-wrapper to avoid theme conflicts.
   Reusable: Copy this block for any CBP frontend form.
   ============================================================ */
.cbp-submit-wrapper {
    max-width: 760px;
    margin: 2rem auto;
    font-family: var(--cbp-font-family, 'Inter', sans-serif);
}
.cbp-submit-header {
    text-align: center;
    margin-bottom: 2.5rem;
}
.cbp-submit-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--cbp-text-color, #1a1a2e);
    margin-bottom: 0.5rem;
}
.cbp-submit-subtitle {
    color: #6b7280;
    font-size: 1rem;
}
.cbp-submit-notice {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-weight: 500;
}
.cbp-submit-notice.cbp-notice-success {
    background: #d1fae5;
    border-left: 4px solid #10b981;
    color: #065f46;
}
.cbp-submit-notice.cbp-notice-error {
    background: #fee2e2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
}
.cbp-submit-login-notice {
    padding: 1.5rem;
    background: #eff6ff;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    text-align: center;
}
.cbp-submit-login-notice a {
    color: var(--cbp-primary-color, #6c63ff);
    font-weight: 600;
}
.cbp-submit-form {
    background: #fff;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}
.cbp-form-group {
    margin-bottom: 1.75rem;
}
.cbp-form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #374151;
    font-size: 0.95rem;
}
.cbp-required {
    color: #ef4444;
    margin-left: 3px;
}
.cbp-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    color: #111827;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    box-sizing: border-box;
    background: #fafafa;
}
.cbp-form-control:focus {
    outline: none;
    border-color: var(--cbp-primary-color, #6c63ff);
    box-shadow: 0 0 0 3px rgba(108,99,255,0.15);
    background: #fff;
}
.cbp-content-editor {
    resize: vertical;
    min-height: 220px;
    line-height: 1.7;
}
/* Upload Area */
.cbp-upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    position: relative;
    background: #fafafa;
}
.cbp-upload-area:hover {
    border-color: var(--cbp-primary-color, #6c63ff);
    background: #f5f3ff;
}
.cbp-upload-icon { font-size: 2.5rem; margin-bottom: 0.5rem; }
.cbp-upload-text { font-weight: 600; color: #374151; margin: 0 0 0.25rem; }
.cbp-upload-hint { font-size: 0.85rem; color: #9ca3af; margin: 0; }
.cbp-file-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.cbp-image-preview {
    margin-top: 1rem;
    position: relative;
    display: inline-block;
}
.cbp-image-preview img {
    max-height: 200px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}
.cbp-remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    cursor: pointer;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
/* Submit Button */
.cbp-form-actions { text-align: center; }
.cbp-btn-primary {
    background: var(--cbp-primary-color, #6c63ff);
    color: white;
    border: none;
    padding: 0.85rem 3rem;
    border-radius: 50px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.cbp-btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(108,99,255,0.35);
}
.cbp-btn-primary:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}
.cbp-btn-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255,255,255,0.4);
    border-top-color: white;
    border-radius: 50%;
    animation: cbp-spin 0.7s linear infinite;
}
@keyframes cbp-spin { to { transform: rotate(360deg); } }
.cbp-submit-disclaimer {
    margin-top: 1rem;
    color: #9ca3af;
    font-size: 0.85rem;
}
</style>
