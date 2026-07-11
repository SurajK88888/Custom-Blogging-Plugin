---
description: Custom Blog Pro — WordPress PHP security, hooks, and coding patterns
globs: "**/*.php"
alwaysApply: false
---

# WordPress PHP Standards (CBP)

## Class & Hook Patterns

```php
// ✅ Namespaced class, hook registration in constructor or init method
namespace CBP\Core;

class Loader {
    public function register(): void {
        add_action( 'init', [ $this, 'on_init' ] );
    }
}
```

- One class per file; PSR-4 namespace maps to `includes/` subfolders.
- Use WordPress hooks — avoid direct calls across module boundaries when a filter/action fits.

## Security (required on every endpoint, form, and query)

| Concern | Pattern |
|---------|---------|
| Forms/AJAX | `wp_verify_nonce()`, `check_ajax_referer()` |
| Authorization | `current_user_can( 'cbp_manage_blogs' )` (use plugin caps) |
| Input | `sanitize_text_field()`, `absint()`, `wp_kses_post()` |
| Output | `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` |
| SQL | `$wpdb->prepare( 'SELECT ... WHERE id = %d', $id )` |
| REST | `permission_callback` on every route |
| Uploads | MIME validation, capability check, `wp_handle_upload()` |

## REST & AJAX

- REST routes under `cbp/v1`; AJAX actions prefixed `cbp_`.
- Always return `WP_Error` with appropriate HTTP status on failure.
- Rate-limit public-facing actions where applicable.

## Performance

- Lazy-load images; defer non-critical JS; version assets with `CBP_VERSION`.
- Use object cache where practical; optimize queries and add DB indexes.
- Email processing via WP Cron queue — never block publish on bulk send.

## i18n

Wrap user-facing strings: `__( 'Text', 'custom-blog-pro' )`, `_e()`, `_n()`.

## Uninstall

`uninstall.php` must offer optional full cleanup (tables, options, cron events) per settings.
