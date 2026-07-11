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


  