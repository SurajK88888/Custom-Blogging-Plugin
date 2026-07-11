# Phase 2 Execution Task List

## Component 1: Appearance & Customizer Expansion
- [x] P2.C1.1: Register new options (`cbp_font_family`, `cbp_dark_mode`) in `includes/admin/SettingsRegistry.php`.
- [x] P2.C1.2: Add UI controls to `templates/admin/settings-page.php`.
- [x] P2.C1.3: Update `includes/customizer/DynamicCSS.php` to output typography and dark mode CSS.

## Component 2: Analytics Dashboard Expansion
- [x] P2.C2.1: Add `get_device_stats()` and `get_browser_stats()` to `includes/analytics/AnalyticsEngine.php`.
- [x] P2.C2.2: Pass stats to JS in `includes/admin/Dashboard.php` & render Canvas in `templates/admin/dashboard-page.php`.
- [x] P2.C2.3: Build new charts in `assets/js/dashboard.js`.

## Component 3: Advanced Email Operations (Bulk Campaigns)
- [x] P2.C3.1: Create `includes/admin/EmailCampaigns.php` (Submenu & POST handler).
- [x] P2.C3.2: Build UI in `templates/admin/email-campaigns.php`.
- [x] P2.C3.3: Wire `EmailCampaigns` into `Plugin.php`.

## Component 4: Advanced Tools (Import/Export/Backup)
- [x] P2.C4.1: Add Tools Tab to `templates/admin/settings-page.php`.
- [x] P2.C4.2: Create `includes/admin/Tools.php` to handle JSON export/import.
- [x] P2.C4.3: Wire `Tools` into `Plugin.php`.

## Component 5: Blog Enhancements (SEO & Related Posts)
- [x] P2.C5.1: Add Custom SEO fields to MetaBoxes (Update `includes/post/CPT.php`).
- [x] P2.C5.2: Output SEO data to `<head>` and build Related Posts grid.
