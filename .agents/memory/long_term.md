# Purpose

Stores permanent, historical truths (architectural decisions, resolved nasty bugs, user preferences, API patterns, discovered codebase quirks).

# Long-Term Project Memory Index

This file serves as the permanent source of truth for repository architecture, design patterns, and systemic bug resolutions.

## Project Ground Rules
- **Framework**: WordPress 6.x+, PHP 8.1+
- **Primary Database**: MySQL/MariaDB (via `$wpdb` with prepared statements)
- **Architecture**: Modular, decoupled folder structure (`core`, `admin`, `frontend`, etc.). PSR-4 autoloading.
- **Frontend**: Vanilla JS (modular ES6), CSS variables for theming, responsive.

## Historical Log & Knowledge Base
*(AI Agent will append entries below chronologically)*
- **2026-07-11**: Initialized Custom Blog Pro (CBP) project state and long-term architectural guidelines.
- **2026-07-11**: Phase 1 Completed (Modules 1-7). 
  - **Context/Problem**: Heavy analytics operations (COUNT, SUM) degrading WP Admin performance.
  - **Resolution**: Implemented Transient Caching (15 min) in `AnalyticsEngine.php`.
- **2026-07-11**: Phase 2 Completed (Granular spec features).
  - **Context/Problem**: Needed to store manual Bulk Email Campaigns without altering the custom `wp_cbp_email_queue` database schema (which strictly requires a `post_id`).
  - **Resolution**: Registered a new, hidden Custom Post Type (`cbp_campaign`). Custom emails are inserted as private posts, generating a valid `post_id` that the existing Queue Manager and WP Cron jobs can parse natively. This avoided breaking structural changes to the DB schema while maintaining perfect integration with the email architecture.
  - **Context/Problem**: Settings migration across environments.
  - **Resolution**: Built `Tools.php` to export/import JSON representations of all `cbp_*` options via the `admin-post.php` hook.
- **2026-07-11**: Frontend Blog Submission Form Feature Added.
  - **Context/Problem**: Normal users (non-admins) had no way to submit blog posts without WP admin access.
  - **Resolution**: Built a `[cbp_submit_form]` shortcode system. `Shortcodes.php` renders the form; `FrontendSubmission.php` handles AJAX submission via `wp_ajax_cbp_submit_blog`. Posts are inserted with `post_status = 'pending'` so admin must approve before publishing. File uploads handled via `media_handle_upload()` with MIME type validation.
  - **Role Requirement**: User must have `edit_posts` capability. The `cbp_blogger` role (registered in `Roles.php`) satisfies this. Default Subscribers do NOT and see a friendly error.
  - **Admin Notification**: A "Pending Review" KPI card with a direct link was added to the CBP Dashboard so admins always know when user submissions are waiting.

- **2026-07-12**: Admin Menu Consolidation.
  - **Context/Problem**: "Blogs (CBP)" appeared as a separate top-level WordPress admin menu item, disconnected from "Custom Blog Pro".
  - **Resolution**: Changed `show_in_menu` from `true` to `'cbp-dashboard'` in `CPT.php`. WordPress automatically nests CPT sub-menus (All Blogs, Add New, Categories, Tags) under the parent slug. All `add_submenu_page()` calls are now centralized in `Dashboard::add_admin_menu()`. Removed duplicate registrations from `Settings.php` and `EmailCampaigns.php`.
  - **Rule**: All admin menu registration for CBP must go through `Dashboard::add_admin_menu()` only. Individual modules must NOT call `add_submenu_page()` themselves.

- **2026-07-12**: Single Blog UI Upgrades (Visual — no logic changes).
  - **Colorful Header Banner**: `.cbp-single-header` now uses `background: var(--cbp-header-bg)` with `border-radius: 20px`. Decorative pseudo-element circles add depth. White title text + `text-shadow`.
  - **Time in Meta**: `get_the_time()` added alongside `get_the_date()` in meta row. Format: "July 12, 2026 at 10:30 pm".
  - **Header BG Color Control**: New `cbp_header_bg_color` setting registered in `SettingsRegistry.php`. Exposed as a color picker in Settings → Appearance. Injected as `--cbp-header-bg` CSS variable via `DynamicCSS.php`.
  - **Meta Chips**: Author, date, and reading time now styled as frosted-glass pill badges (white semi-transparent background, blur) inside the colorful header.

- **2026-07-12**: Layout Spacing & Share Bar Fix.
  - **Context/Problem**: No gap between colorful header and content below; Share bar had no padding and was misaligned.
  - **Resolution**: Added `margin-bottom: 2rem` to `.cbp-single-header`. Moved all `.cbp-social-share` styles out of the inline `<style>` block in `Components.php` and into `frontend.css`. Share bar now has `padding: 1.5rem 2rem`, `background: #f9fafb`, pill-shaped buttons with hover lift.
  - **Rule**: Never put `<style>` tags inside PHP component render methods. All styles belong in `frontend.css`.

- **2026-07-12**: Comment Section Card UI.
  - **Context/Problem**: WordPress native `comments_template()` output had no card wrapper — it rendered as flat unstyled HTML below the article.
  - **Resolution**: Wrapped `comments_template()` in `<div class="cbp-comments-card">` in `single-cbp_blog.php`. Added comprehensive CSS scoped under `.cbp-comments-card` in `frontend.css`: card shell (bg, shadow, border-radius), section headings with border-bottom, individual comment bubble cards (`#f9fafb` bg, `border-radius: 12px`), pill Reply button, styled form inputs with focus glow ring, pill Submit button.
  - **Rule**: All selectors are scoped under `.cbp-comments-card` to avoid conflicting with the active theme's global comment styles.

- **2026-07-12**: Blog Grid Shortcode `[cbp_blog_grid]` Added.
  - **Context/Problem**: No way for visitors to browse all blog posts on a frontend page in a card layout.
  - **Resolution**: Added `render_blog_grid()` to `Shortcodes.php`. Created `templates/frontend/blog-grid.php` as the card grid template. Card layout: Featured image → Title (linked) → Excerpt (22 words) → Author/Date/Time/Reading-time meta → "Read Post →" button. Added CSS for card footer meta, read-more button, no-image placeholder, pagination, and column modifiers.
  - **Shortcode Attributes**: `posts_per_page` (default 9), `columns` (2 or 3), `category` (slug filter), `orderby` (date/title/rand).
  - **Setup**: Create a WP Page, add `[cbp_blog_grid]`, publish. Visitors see the card grid with pagination.
  - **Reusable**: The same `.cbp-card` CSS classes are shared between the shortcode grid and all other CBP card components. Consistent UI guaranteed.

- **2026-07-12**: Admin Status Column with AJAX Inline Update Added.
  - **Context/Problem**: Admins had to open each post individually to change its status (publish/pending/draft).
  - **Resolution**: Created `includes/admin/BlogListColumns.php`. Hooks: `manage_cbp_blog_posts_columns` (adds Status column), `manage_cbp_blog_posts_custom_column` (renders status dropdown + color dot), `wp_ajax_cbp_update_post_status` (AJAX handler). Inline JS (attached via `wp_add_inline_script`) listens to dropdown `change` events, POSTs to `admin-ajax.php`, updates the color dot on success, reverts on failure.
  - **Security**: Each dropdown carries a per-post nonce (`cbp_update_status_{post_id}`). Handler verifies nonce + `edit_post` capability + whitelist-validates the new status value before calling `wp_update_post()`.
  - **Column is sortable**: `manage_edit-cbp_blog_sortable_columns` maps `cbp_status → post_status`.
  - **Assets scope**: CSS and JS are only enqueued on `edit.php?post_type=cbp_blog` to avoid polluting other admin screens.