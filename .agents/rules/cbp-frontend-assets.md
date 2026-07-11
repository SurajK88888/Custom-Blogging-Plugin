---
description: Custom Blog Pro — frontend JS, CSS, and template conventions
globs: "{assets/**/*,templates/**/*,**/*.css,**/*.js}"
alwaysApply: false
---

# Frontend & Assets (CBP)

## CSS Theming

- Define design tokens as CSS variables; admin Appearance module writes overrides inline or to a generated stylesheet.
- Split assets: `admin.css`, `frontend.css`, `dashboard.css`, `cards.css`, `responsive.css`.
- Support light/dark mode via `[data-theme="dark"]` or `.cbp-dark` class toggled from settings.

## JavaScript

- Prefer vanilla JS; use ES6 modules only where bundling is already set up.
- Localize scripts with `wp_localize_script()` — pass REST URL, nonce, and i18n strings.
- Frontend AJAX pagination/filtering must degrade gracefully without JS.

## Templates

- Place PHP templates in `templates/`; load via helper, not direct theme includes.
- Escape all output; never trust post meta or ad HTML without `wp_kses_post()` or allowed HTML config.
- Archive/single templates: card layout, reading progress, related posts, ad injection hooks.

## Admin UI

- Card-based SaaS-style dashboard; match WordPress admin patterns (`wrap`, notices, list tables where appropriate).
- Use WP color picker, media uploader, and Settings API for options pages.
