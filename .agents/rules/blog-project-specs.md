---
trigger: always_on
---

---
description: Comprehensive Software Requirements Specification (SRS), architectural guardrails, data design, and workflows for the Custom Blog Pro (CBP) WordPress plugin project.
globs: "includes/**/*, assets/**/*, templates/**/*, custom-blog-pro.php, uninstall.php"
alwaysApply: true
---

# SYSTEM RULES & DEVELOPMENT PROTOCOLS

## Workflow Tracking Protocol
*   **Core Directive**: You must maintain active state tracking across multi-step development loops using `.agents/memory/short_term.md`. Never begin, modify, or conclude a task without referencing or updating this state.
*   **Operational Lifecycle**:
    1.  **Pre-Task Initialization**: Before writing code or proposing changes, read `.agents/memory/short_term.md`. Analyze the current phase, completed steps, and pending objectives.
    2.  **Execution Guardrails**: Only work on the current active item defined in the state file. Do not jump ahead to future tasks without explicit permission.
    3.  **Post-Task State Synchronization**: Immediately after executing a code modification, running a test, or finishing a sub-task, update `.agents/memory/short_term.md`.
    4.  **Context Handoff**: If a task requires multiple steps, rewrite the "Next Actionable Steps" section before asking the user for feedback. This ensures continuity in subsequent prompts.
*   **Update Formatting**: 
    *   Mark completed items clearly with `[x]`.
    *   Update the "Current Focus" block with timestamps or iteration numbers.
    *   Log any blockers or architectural pivots discovered during the step.

## Architectural Configuration Protocol
*   **Core Directive**: You are responsible for keeping `.agents/memory/long_term.md` accurate and up to date. This file serves as your source of truth for the codebase architecture, system design, and tech stack constraints.
*   **Trigger Events for Updates**: You must immediately update `.agents/memory/short_term.md` whenever you:
    1.  Create a new database table, schema change, or state management slice[cite: 1].
    2.  Introduce a new third-party library or package dependency[cite: 1].
    3.  Establish a new core folder structural pattern or routing convention[cite: 1].
    4.  Define a global architectural pattern that future features must follow[cite: 1].
*   **Update Guardrail**: Never delete existing architecture rules without asking the user, and document the *why* behind system design decisions[cite: 1].

## Development Prohibitions
*   Do not use implicit `any` types[cite: 1].
*   Never perform raw SQL queries without explicit justification in comments[cite: 1].

---

I recommend building it exactly as plugins like **Elementor, WooCommerce, Rank Math, and FluentCRM** are structured: modular, scalable, secure, and easy to maintain.

---

# PROJECT NAME

**Custom Blog Pro (CBP)**

> A Production-Level WordPress Blog Management Plugin with Email Marketing, Ad Management, Analytics, Role-Based Access Control, Modern UI, and Fully Customizable Design.

---

# PHASE 1 — Software Requirements Specification (SRS)

## Primary Goal

Develop a production-ready WordPress plugin that allows administrators and authorized bloggers to create, manage, publish, promote, and analyze blog posts while providing modern UI customization, email marketing capabilities, and advertisement management.

---

Design Goals

The plugin will follow a premium SaaS-style design:

Modern card-based admin dashboard
Responsive frontend blog cards
Fully customizable colors from the admin panel
Typography controls
Border radius and shadow controls
Button style customization
Light and dark mode
Live preview where practical
Clean animations and hover effects
WordPress-native UI with a polished experience


Technology Stack:

PHP 8.1+
WordPress 6.x+
Object-Oriented PHP
PSR-4 autoloading
WordPress Coding Standards
Vanilla JavaScript (with optional modular ES6 where appropriate)
CSS variables for theming
WP REST API
WP AJAX
WP Cron
$wpdb with prepared statements
Translation-ready (.pot)

# Core Modules

```
Core System

│
├── Plugin Loader
├── Installer
├── Uninstaller
├── Roles & Capabilities
├── Security
├── Assets Loader
├── Database
├── REST API
├── AJAX
├── Helper Functions
└── Logger
```

---

```
Blog Module

│
├── Blog CRUD
├── Categories
├── Tags
├── Featured Posts
├── Draft
├── Schedule
├── Revisions
├── Reading Time
├── Related Posts
├── SEO Fields
├── Featured Image
└── Blog Analytics
```

---

```
Frontend Module

│
├── Blog Archive
├── Single Blog
├── Search
├── Filters
├── Pagination
├── AJAX Loading
├── Responsive Cards
├── Related Posts
├── Breadcrumb
├── Social Sharing
└── Reading Progress
```

---

```
Email Module

│
├── Email Queue
├── HTML Templates
├── SMTP Support
├── Send to Registered Users
├── Send to Custom Emails
├── Bulk Email
├── Scheduled Email
├── Email Logs
├── Failed Queue
└── Retry System
```

---

```
Advertisement Module

│
├── Ad Slots
├── Top Banner
├── Middle Banner
├── Bottom Banner
├── Sidebar
├── Sticky Ads
├── Popup Ads
├── Google AdSense
├── Custom HTML
└── Analytics
```

---

```
Appearance Module

│
├── Color Picker
├── Typography
├── Card Design
├── Border Radius
├── Shadows
├── Buttons
├── Icons
├── Animation
├── Dark Mode
└── Layout
```

---

```
Analytics Module

│
├── Blog Views
├── Shares
├── Ad Views
├── Ad Clicks
├── CTR
├── Popular Posts
├── Devices
├── Browser
├── Country
└── Reports
```

---

```
Settings Module

│
├── General
├── Blog
├── Email
├── SMTP
├── Ads
├── Appearance
├── Import
├── Export
├── Backup
└── License
```

---

# PHASE 2 — Production Folder Structure

```text
custom-blog-pro/

│
├── custom-blog-pro.php
├── uninstall.php
├── readme.txt
├── changelog.txt
├── composer.json
├── LICENSE
│
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   ├── frontend.css
│   │   ├── dashboard.css
│   │   ├── forms.css
│   │   ├── cards.css
│   │   └── responsive.css
│   │
│   ├── js/
│   │   ├── admin.js
│   │   ├── dashboard.js
│   │   ├── frontend.js
│   │   ├── upload.js
│   │   ├── analytics.js
│   │   └── customizer.js
│   │
│   ├── icons/
│   └── images/
│
├── includes/
│
│   ├── core/
│   ├── admin/
│   ├── frontend/
│   ├── post/
│   ├── ads/
│   ├── analytics/
│   ├── email/
│   ├── api/
│   ├── ajax/
│   ├── database/
│   ├── helper/
│   ├── logger/
│   ├── customizer/
│   └── widgets/
│
├── templates/
│
├── languages/
│
├── vendor/
│
└── docs/
```

---

# PHASE 3 — Complete Database Design

Instead of two tables, we'll create multiple normalized tables.

```
wp_cbp_email_logs

id

post_id

recipient

status

message

opened

clicked

created_at
```

---

```
wp_cbp_ad_views

id

ad_id

post_id

user_ip

device

browser

clicked

created_at
```

---

```
wp_cbp_blog_views

id

post_id

visitor_ip

country

device

browser

created_at
```

---

```
wp_cbp_activity_logs

id

user_id

action

description

ip

browser

created_at
```

---

```
wp_cbp_email_queue

id

post_id

recipient

status

attempt

scheduled_at
```

---

```
wp_cbp_plugin_settings

id

setting_key

setting_value
```

---

# PHASE 4 — Workflow

```
Plugin Activated

↓

Create Database

↓

Register CPT

↓

Register Taxonomies

↓

Register Roles

↓

Register Capabilities

↓

Create Default Settings

↓

Load Assets

↓

Initialize Dashboard

↓

Ready
```

---

# Blog Upload Workflow

```
Admin Dashboard

↓

Click Add Blog

↓

Permission Check

↓

WordPress Editor

↓

Upload Images

↓

SEO

↓

Categories

↓

Tags

↓

Preview

↓

Publish

↓

Analytics Created

↓

Email Queue Generated

↓

Ads Injected

↓

Visible on Frontend
```

---

# Email Workflow

```
Publish Blog

↓

Queue Emails

↓

WP Cron

↓

SMTP

↓

Registered Users

↓

Custom Email List

↓

Send

↓

Log Result

↓

Retry Failed Emails
```

---

# Advertisement Workflow

```
Open Blog

↓

Determine Ad Position

↓

Inject Ad

↓

Track View

↓

Track Click

↓

Store Analytics

↓

Dashboard Reports
```

---

# Frontend Workflow

```
Visitor

↓

Archive Page

↓

Search

↓

Filter

↓

Open Blog

↓

Ads Display

↓

Share

↓

Comment

↓

Analytics

↓

Related Blogs
```

---

# Admin Dashboard Layout

A premium dashboard with cards similar to modern SaaS applications.

```
----------------------------------------------------

Dashboard

----------------------------------------------------

Total Blogs

Published

Draft

Scheduled

Pending

Views

Shares

Revenue

----------------------------------------------------

Recent Blogs

Recent Shares

Recent Emails

Recent Activities

----------------------------------------------------

Popular Categories

Popular Posts

Traffic Sources

----------------------------------------------------

Charts

Views

Emails

Ads

CTR

----------------------------------------------------
```

---

# Appearance Customizer

Every UI component should be configurable without editing code.

**Color Settings**

* Primary Color
* Secondary Color
* Accent Color
* Background Color
* Card Color
* Text Color
* Link Color
* Button Colors
* Hover Colors

**Typography**

* Google Fonts integration
* Font family
* Heading font
* Body font
* Font sizes
* Font weights
* Line height

**Cards**

* Border radius
* Shadow
* Border width
* Border color
* Hover animation
* Card spacing

**Buttons**

* Shape
* Size
* Padding
* Icon position
* Hover effect

**Layout**

* Container width
* Sidebar position
* Grid/List toggle
* Columns
* Gaps
* Mobile layout

**Dark Mode**

* Enable/Disable
* Custom dark palette

---

# Security Requirements

* WordPress Coding Standards (WPCS)
* Nonce verification on every form
* Capability checks (`current_user_can`)
* Strict input sanitization
* Output escaping (`esc_html`, `esc_attr`, `wp_kses_post`)
* Prepared SQL queries with `$wpdb->prepare`
* CSRF protection
* XSS prevention
* File upload validation
* MIME type validation
* Rate limiting for public actions
* Secure AJAX and REST endpoints

---

# Performance Requirements

* Lazy loading for images
* AJAX pagination
* Object caching support
* WP Cron for email queue
* Optimized database indexes
* Minimal database queries
* Deferred JavaScript loading
* Asset versioning
* Uninstall cleanup option

---

# Development Standards

* WordPress Plugin Handbook compliance
* PSR-4 autoloading (Composer)
* Object-Oriented PHP
* Namespaced classes
* Hook-based architecture
* Internationalization (i18n)
* Translation-ready (`.pot`)
* PHP 8.1+ compatible (while maintaining compatibility with supported WordPress versions)
* WordPress 6.x compatibility
* Multisite support where feasible

---

# AI Master Prompt Strategy

Instead of one giant prompt, we'll use a **single Master System Prompt** plus **50+ implementation prompts** (one for each module). This keeps the generated code consistent, modular, and production-ready while staying within model limits.

The implementation roadmap will cover modules such as:

1. Plugin bootstrap and loader
2. Activation, deactivation, uninstall
3. Database and installer
4. Roles and capabilities
5. Custom Post Type and taxonomies
6. Admin dashboard
7. Settings framework
8. Appearance customizer
9. Blog editor and frontend
10. Email queue and templates
11. Ad manager and analytics
12. Security, AJAX, REST API
13. Testing, optimization, packaging, and release


I recommend we build this plugin one production-ready module at a time, ensuring each phase is complete, tested, and does not break previously implemented functionality.