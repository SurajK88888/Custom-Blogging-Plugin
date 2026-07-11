---
trigger: always_on
description: Custom Blog Pro (CBP) — project workflow, architecture, and module build order
---

# Custom Blog Pro (CBP)

Production-level WordPress blog plugin: email marketing, ads, analytics, RBAC, appearance customizer, modern admin UI.

## State & Architecture Files

Before any task: read `.agents/memory/short_term.md`. After every code change, test, or sub-task: update it.

- Mark completed items `[x]`, keep "Current Focus" current, log blockers.
- Treat `project_config.md` as architecture source of truth (schema, endpoints, folder patterns).
- Update `project_config.md` when adding tables, dependencies, folder patterns, or global patterns. Document *why*, not just *what*. Never delete architecture rules without user approval.

## Build Approach

Build **one production-ready module at a time**. Each phase must be complete, tested, and must not break prior work. Follow patterns used by Elementor, WooCommerce, Rank Math, and FluentCRM: modular, scalable, secure, maintainable.

## Tech Stack

- PHP 8.1+, WordPress 6.x+, OOP with PSR-4 (Composer), WordPress Coding Standards
- Vanilla JS (modular ES6 where appropriate), CSS variables for theming
- WP REST API, WP AJAX, WP Cron, `$wpdb` with prepared statements
- Translation-ready (`languages/`, `.pot`)

## Folder Structure

```
custom-blog-pro/
├── custom-blog-pro.php, uninstall.php, composer.json
├── assets/{css,js,icons,images}/
├── includes/{core,admin,frontend,post,ads,analytics,email,api,ajax,database,helper,logger,customizer,widgets}/
├── templates/, languages/, vendor/, docs/
```

## Core Modules (implement in order)

1. Core — loader, installer, uninstaller, roles/caps, security, assets, DB, REST, AJAX, logger
2. Blog — CRUD, taxonomies, featured/draft/schedule, revisions, SEO, analytics
3. Frontend — archive, single, search, filters, pagination, AJAX, cards, sharing
4. Email — queue, templates, SMTP, bulk/scheduled send, logs, retry
5. Ads — slots (banner/sidebar/sticky/popup), AdSense, custom HTML, analytics
6. Appearance — colors, typography, cards, buttons, layout, dark mode
7. Analytics — views, shares, ad metrics, CTR, reports
8. Settings — general, blog, email, SMTP, ads, appearance, import/export/backup

## Database Tables (prefix `wp_cbp_`)

`email_logs`, `email_queue`, `ad_views`, `blog_views`, `activity_logs`, `plugin_settings` — use normalized schemas; prepared statements only.

## Activation Flow

Activate → create DB → register CPT/taxonomies → roles/caps → default settings → load assets → init dashboard.

## What to Avoid

- Jumping ahead in the module roadmap without explicit permission
- Raw SQL without `$wpdb->prepare` and justification
- Implicit `any` types (if TypeScript is added later)
- Editing code to customize appearance — use the Appearance module / admin settings