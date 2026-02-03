# Themes Directory - Comprehensive Documentation

> **Path:** `/wpl/club/core/themes/`
> **Platform:** TAO.ai / #Hires - Community & Career Platform
> **Generated:** 2026-01-30

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [index.php](#indexphp)
3. [images/index.php](#imagesindexphp)
4. [head.php](#headphp)
5. [head_landing.php](#head_landingphp)
6. [head_lp.php](#head_lpphp)
7. [header.php](#headerphp)
8. [header_mobile.php](#header_mobilephp)
9. [header_new.php](#header_newphp)
10. [header-iframe.php](#header-iframephp)
11. [header_landing.php](#header_landingphp)
12. [header_lp.php](#header_lpphp)
13. [header_events_landing.php](#header_events_landingphp)
14. [chatbot.php](#chatbotphp)
15. [footer.php](#footerphp)
16. [footer_backup.php](#footer_backupphp)
17. [footer_landing.php](#footer_landingphp)
18. [footer_lp.php](#footer_lpphp)
19. [footer_events_landing.php](#footer_events_landingphp)
20. [Page Assembly Matrix](#page-assembly-matrix)
21. [Constants Reference](#constants-reference)

---

## Architecture Overview

The themes directory implements a **multi-layout templating system** for the TAO.ai/Hires platform. Pages are assembled by combining a `head` + `header` + (page content) + `footer` file. There are **five distinct layout pipelines**:

| Layout | Head | Header | Footer | Used For |
|---|---|---|---|---|
| **Main App** | `head.php` | `header.php` | `footer.php` | Club, Events, Jobs, Asks, Settings |
| **Mobile App** | `head.php` | `header_mobile.php` | `footer_backup.php` | Mobile-specific views |
| **Landing Page (reads)** | `head_landing.php` | `header_landing.php` | `footer_landing.php` | Blog/reads landing pages |
| **LP (content)** | `head_lp.php` | `header_lp.php` | `footer_lp.php` | Content/article listing pages |
| **Events Landing** | `head.php` | `header_events_landing.php` | `footer_events_landing.php` | Event-specific landing pages |
| **New/Simple** | `head.php` | `header_new.php` | `footer_backup.php` | Simplified header layout |
| **Iframe** | `head.php` | `header-iframe.php` | (varies) | Embedded iframe views |

**Include Chain:** Headers include their respective `head.php` via `include_once('head.php')` or `include_once('head_landing.php')` etc. The footer closes `</body></html>`.

---

## index.php

**Full Path:** `/wpl/club/core/themes/index.php`
**Size:** 30 bytes | **Lines:** 3

### Purpose/Role
Security guard file. Prevents directory listing/browsing.

### Content
```php
<?php
    // silence is golden
```

### Integration Points
- None. Purely defensive.

---

## images/index.php

**Full Path:** `/wpl/club/core/themes/images/index.php`
**Size:** 30 bytes | **Lines:** 3

### Purpose/Role
Same security guard for the `images/` subdirectory.

---

## head.php

**Full Path:** `/wpl/club/core/themes/head.php`
**Size:** 32,416 bytes | **Lines:** ~767

### Purpose/Role
**Primary HTML `<head>` section** for the main application. Opens `<!DOCTYPE html>`, `<html>`, `<head>`, and `<body>` tags. Sets up all meta tags, CSS, JS, and global JavaScript variables. Also renders the loader overlay and toast notification container.

### Template Structure
```
<!DOCTYPE html>
<html lang="en">
<head>
    [Meta tags - charset, viewport, OG, Twitter, SEO]
    [Timezone cookie script]
    [Favicon links]
    [taoh_user_record_history() call]
    [<title> tag]
    [Google Fonts]
    [CSS includes]
    [Global JS variables block]
    [JS includes]
    [Summernote editor CSS/JS]
    [Emoji area CSS/JS]
    [IndexedDB variable declarations]
    [IndexedDB cache deletion logic]
    [TAOH_CUSTOM_HEAD output]
</head>
[Inline <style> block - navbar scroll, modals, off-canvas menus, toast styles]
<body>
    [#loader div with TAOH_LOADER_GIF]
    [#toast_container with toast markup]
```

### Key PHP Variables Set
- `$taoh_site_favicon` - from `TAOH_PAGE_FAVICON` or `TAOH_SITE_FAVICON`
- `$h_session_user_info` - user session array (when logged in + profile complete)
- `$session_country` - extracted from `full_location`
- `$user_timezone` - from `getValidTimezone()`
- `$lastParam` - last URL segment of `TAOH_SITE_URL_ROOT`

### Global JS Variables Declared (lines 141-231)
| Variable | Source |
|---|---|
| `_taoh_site_url_root` | `TAOH_SITE_URL_ROOT` |
| `_taoh_site_root_hash` | `TAOH_SITE_ROOT_HASH` |
| `_taoh_root_path_hash` | `TAOH_ROOT_PATH_HASH` |
| `_taoh_cdn_prefix` | `TAOH_CDN_PREFIX` |
| `_taoh_ops_prefix` | `TAOH_OPS_PREFIX` |
| `_taoh_ops_code` | `TAOH_OPS_CODE` |
| `_action_url` | `TAOH_ACTION_URL` |
| `_intao_db_version` | `TAOH_INDEXEDDB_VERSION` |
| `_taoh_site_ajax_url` | `taoh_site_ajax_url(1)` |
| `_taoh_dash_site_ajax_url` | `taoh_site_ajax_url(2)` |
| `_taoh_ajax_token` | `taoh_get_api_token(1)` |
| `_taoh_ajax_secret` | `TAOH_AJAX_SECRET` |
| `_taoh_cache_chat_proc_url` | `TAOH_CACHE_CHAT_PROC_URL` |
| `_taoh_cache_chat_url` | `TAOH_CACHE_CHAT_URL` |
| `_taoh_site_logo` | `TAOH_SITE_LOGO` |
| `_taoh_site_sq_logo` | `TAOH_SITE_FAVICON` |
| `_taoh_site_name` | `TAOH_SERVER_NAME` |
| `_taoh_plugin_name` | Last segment of URL |
| `_is_logged_in` | `taoh_user_is_logged_in()` |
| `_is_profile_complete` | Session check |
| `_taoh_get_skill` | User skill or `TAOH_DEFAULT_SKILL` |
| `_taoh_get_company` | User company or `TAOH_DEFAULT_COMPANY` |
| `_taoh_get_title` | User title or `TAOH_DEFAULT_TITLE` |
| `_taoh_get_country` | User country or `TAOH_DEFAULT_COUNTRY` |
| `_taoh_user_timezone` | User TZ or `TAOH_DEFAULT_TIMEZONE` |
| `d_user_logged` | Login state |
| `d_user_profile_completed` | Profile completion state |
| `d_user_profile_type` | `'professional'` or user type |
| `CURRENT_PAGE` | `TAOH_SITE_CURRENT_APP_SLUG` |
| `CURRENT_INNER_PAGE` | `TAO_CURRENT_APP_INNER_PAGE` |
| `SETTINGSLINK`, `EVENTREGISTERLINK`, etc. | CTA link HTML |

### CSS Includes (in order, lines 100-120)
1. Bootstrap 4.6.1 (`bootstrap.min.css`)
2. Line Awesome 1.3.0 (`line-awesome.min.css`, `all.min.css`)
3. Font Awesome 6.4.2 (`all.min.css`)
4. Tom Select 2.2.2 (`tom-select.css`)
5. Select2 4.1.0 (`select2.min.css`)
6. Owl Carousel 2.3.4 (`owl.carousel.min.css`)
7. intl-tel-input 17.0.8 (`intlTelInput.css`)
8. Fancybox 3.5.7 (`jquery.fancybox.css`)
9. IconSelect (`iconselect.css`)
10. TAO CDN play3 style (`style.css`)
11. Intro.js (`introjs.min.css`)
12. `jquery-confirm.min.css` (CDN)
13. `styles_config.php` (dynamic, versioned)
14. `mobile_style.css` (versioned)
15. `slick.css` (versioned)
16. `taoh.css` (versioned)
17. `styles_internal.css` (versioned)
18. Chat icons (`icons.min.css`)
19. `chatbot.css` (CDN)
20. Summernote BS4 (`summernote-bs4.css`)
21. `emojionearea.min.css` (CDN)

### JS Includes (in order, lines 234-263)
1. jQuery 3.6.0
2. Bootstrap 4.6.1 bundle
3. Tom Select 2.2.2
4. `form_validation.js` (CDN)
5. `shares.js` (CDN, versioned)
6. `luxon.min.js` (CDN)
7. `taoh.js` (CDN, versioned)
8. `hires.js` (CDN, versioned)
9. `pagination.js` (CDN)
10. `intao.js` (CDN, versioned by IndexedDB version)
11. Select2 4.1.0-beta.1
12. Fancybox 3.5.7
13. Owl Carousel 2.3.4
14. jQuery Lazy 1.7.11
15. jQuery Validate 1.13.1 + additional-methods
16. intl-tel-input 17.0.8
17. `jquery-confirm.min.js` (CDN)
18. `jquery-mod.repeatable.js` (CDN)
19. `chatbot.js` (CDN)
20. Summernote BS4
21. `text_editor.js` (CDN)
22. `emojionearea.min.js` (CDN)

### Inline CSS Sections (after `</head>`, lines 357-742)
- **Navbar scroll colors** (`.navbar-scroll`, `.navbar-scrolled`)
- **Modal side-panel** (`.modal.left .modal-dialog` - right-side slide-in, 320px)
- **Off-canvas menu** (`.off-canvas-menu` - left slide, 320px, translateX animation)
- **Off-canvas menu list styles**
- **Toast notifications** (`.toast`, `.toast_active`, `.info_class`, `.success_class`, `.toasterror_class`)
- **Responsive navbar** (`@media max-width: 992px`, `@media max-width: 1199px`)
- **Misc UI** (`.main-box`, `.borders`, `.mod-title`, `.btn-donate-info`)

### Integration Points
- **Included by:** `header.php` (line 59), `header_mobile.php` (line 29), `header-iframe.php` (line 12), `header_new.php` (line 1), `header_events_landing.php` (line 57)
- **Functions called:** `taoh_user_is_logged_in()`, `taoh_session_get()`, `taoh_site_ajax_url()`, `taoh_get_api_token()`, `taoh_user_record_history()`, `getValidTimezone()`, `taoh_delete_local_cache()`
- **`TAOH_CUSTOM_HEAD`** constant is echoed if defined (line 352-354), allowing per-site custom head injection.

### Surgical Edit Notes
- To add a new CSS file: insert after line 118 (before `styles_internal.css`)
- To add a new JS file: insert after line 252 (before Summernote block)
- To add a new global JS variable: insert inside the `<script>` block at lines 140-232
- The `OpenRegsiterDropdown()` function (line 224) is defined inline here
- IndexedDB deletion logic at lines 291-349 handles `?intao_delete`, `?action_jobs`, `?action_asks`, `?action_events` URL params

---

## head_landing.php

**Full Path:** `/wpl/club/core/themes/head_landing.php`
**Size:** 8,998 bytes | **Lines:** ~110

### Purpose/Role
Lightweight `<head>` for **landing/reads pages**. Similar structure to `head.php` but with fewer JS/CSS dependencies. Includes `Access-Control-Allow-Origin` meta for `TAOH_DASH_PREFIX`.

### Key Differences from head.php
- No Select2, Fancybox, intl-tel-input, jquery-confirm, Summernote, emoji
- Uses `landing.css` instead of `styles_config.php`
- Sets `_taoh_site_url_root` and `_taoh_plugin_name` to `TAOH_TEMP_SITE_URL`
- No session/user info extraction
- No IndexedDB deletion logic
- No inline CSS block after `</head>`
- Opens `<body>` tag at line 109

### CSS Includes
1. Bootstrap 4.6.1
2. Line Awesome 1.3.0
3. Font Awesome 6.4.2
4. TAO CDN play3 style
5. `landing.css` (CDN, date-versioned)
6. `mobile_style.css` (CDN)
7. `slick.css` (CDN)
8. `taoh.css` (CDN)
9. Tom Select 2.0.2
10. Owl Carousel 2.3.4

### JS Includes
1. jQuery 3.6.0
2. Bootstrap 4.6.1 bundle
3. Tom Select 2.0.2
4. `taoh.js`, `hires.js`, `intao.js` (CDN)
5. Owl Carousel 2.3.4
6. jQuery Lazy 1.7.11

### JS Variables
- `_intao_db_version`, `_taoh_site_ajax_url`, `_taoh_dash_site_ajax_url`, `_taoh_site_url_root`, `_taoh_plugin_name`

### Integration Points
- **Included by:** `header_landing.php` (line 2)
- Outputs `TAOH_CUSTOM_HEAD` if defined

---

## head_lp.php

**Full Path:** `/wpl/club/core/themes/head_lp.php`
**Size:** 10,454 bytes | **Lines:** ~184

### Purpose/Role
`<head>` for the **LP (content listing) pages**. Very similar to `head_landing.php` but uses `reads_lp_css.css` and `jet_pack.css` instead of `landing.css`. Includes additional inline `<style>` block after `</head>` with content-specific styles.

### Key Differences from head_landing.php
- Includes `reads_lp_css.css` and `jet_pack.css`
- Includes `pagination.js`
- Has inline `<style>` block (lines 103-183) with:
  - `.css-font` (1.17em)
  - `.post-box-title` overflow/ellipsis (3 lines)
  - `.column2 li.other-news` flex display
  - `#tabbed-widget` tab active state (#F88C00)
  - `.cat-tabs-header` styling
  - Responsive `#wrapper.boxed` padding

### Integration Points
- **Included by:** `header_lp.php` (line 2)

---

## header.php

**Full Path:** `/wpl/club/core/themes/header.php`
**Size:** 166,378 bytes | **Lines:** ~3,800+

### Purpose/Role
**Primary application header** -- the largest file in the directory. Renders the full top navigation bar with all nav items (Club, Events, Jobs, Asks, Messages), notification dropdown, user menu dropdown, off-canvas mobile menu, profile modal, super-admin banner, learning hub modal, login popup modal, share modal, and inline SVG icons for every nav item.

### PHP Setup (lines 1-85)
- Sets `$current_app`, `$taoh_home_url`, `$h_directory_flags_to_show`
- Fetches `$user_data` via `taoh_user_all_info()`
- Extracts `$user_ptoken`, `$profile_complete`, `$h_my_profile_tag_category`
- Fetches CAPTCHA code via `taoh_url_get_content(TAOH_SITE_CAPTCHA)`
- Parses `$conttokenvar` for detail pages, builds `$detail_name`, `$cont`, `$utoken`
- Fetches user API data via `taoh_get_user_info()` into `$data_api`
- **Includes `head.php`** at line 59
- Sets `$ptoken`, `$my_status`, `$session_data`

### Super Admin Banner (lines 87-109)
- Condition: logged in + `is_super_admin == 1` + `site_status == 'init'`
- Renders red banner div with "Finish Setup" button linking to `TAOH_DASH_PREFIX/tao/superadmin/super_admin_form.php?data=<base64_token>`
- Token contains: API token, email, domain

### Main Header Structure (lines 111-500+)
```html
<div class="wrapper">
  <!-- Conditional on NETWORKING_VERSION -->
  <header class="page-header" id="myHeader">
    <nav class="header-nav navbar" id="mainNavbar">
      <div class="container">
        <!-- Logo box with site favicon + TAOH_SITE_NAME_SLUG text -->
        <!-- Optional secondary logo (TAOH_SITE_LOGO_2) -->
        <!-- Mobile menu toggle button -->

        <!-- Navbar items (conditional on login state + feature flags) -->
        <ul class="navbar-nav">
          <!-- Club (if TAOH_CLUBS_ENABLE) - inline SVG icon -->
          <!-- Events (if TAOH_EVENTS_ENABLE) - inline SVG icon -->
          <!-- Jobs (if TAOH_JOBS_ENABLE) - dropdown with Scout sub-items -->
          <!-- Asks (if TAOH_ASKS_ENABLE) - inline SVG icon -->
          <!-- Messages (if logged in + TAOH_MESSAGE_ENABLE) - hidden by default -->
          <!-- Notifications dropdown (if logged in + TAOH_NOTIFICATION_ENABLED == 1) -->
          <!-- User dropdown (logged in: profile, settings, directory, manage, messages, referral, logout) -->
          <!-- User dropdown (not logged in: Login/Signup button or popup trigger) -->
        </ul>
      </div>
    </nav>
  </header>
```

### Navigation Items - Active State Detection
| Nav Item | Active Condition |
|---|---|
| Club | `taoh_parse_url(0)` is `stlo`, empty, `club`, or `directory` (not `profile`), and not `/live` |
| Events | URI contains `TAOH_PLUGIN_PATH_NAME.'/events'` |
| Jobs | URI contains `TAOH_PLUGIN_PATH_NAME.'/jobs'` |
| Asks | URI contains `TAOH_PLUGIN_PATH_NAME.'/asks'` |

### User Menu Dropdown Items (when logged in)
1. Profile link (`/profile/<ptoken>`) -- only if `$profile_complete`
2. Settings link (`TAOH_SETTINGS_URL`)
3. Directory sub-menu (collapsible, with tag-based directory flags from `TAOH_DIRECTORY_FLAGS_TO_SHOW`)
4. Manage Site Settings (super admin only, links to external dashboard)
5. Messages (`/dm/networking`) -- if `TAOH_MESSAGE_ENABLE`
6. Referral (`/referral`)
7. Log out (`/logout`, clears `localStorage.isCodeSent`)

### Off-Canvas Mobile Menu
- Same nav structure but in sidebar format
- Includes Learning Hub with categorized sub-items (Reads, Cards, Tips, Flashcards, etc.)
- Growth Tools sub-menu from `taoh_available_apps()`

### Modals Defined
1. **Learning Hub Modal** (`#learn-modal`) - Left slide modal with categorized learning resources
2. **Login Popup Modal** (`#loginPopupModal`) - For non-logged-in users, with login form
3. **Share Modal** - Social sharing (LinkedIn, Twitter/X, Facebook, WhatsApp, Email, Copy Link)
4. **Profile Card Modal** (`#profileCardModal`) - User profile popup

### Key JS Functions (inline)
- `openLoginPopup()` / `closeLoginPopup()` - Login modal management
- `loginFormSubmit()` - AJAX login via `taoh_site_ajax_url()`
- `openNav()` / `closeNav()` - Sidebar toggle
- `OpenRegsiterDropdown(e)` - Event registration dropdown

### Integration Points
- **Includes:** `head.php` (line 59)
- **Included by:** Main app pages via their routing
- **Feature flags used:** `TAOH_CLUBS_ENABLE`, `TAOH_EVENTS_ENABLE`, `TAOH_JOBS_ENABLE`, `TAOH_ASKS_ENABLE`, `TAOH_MESSAGE_ENABLE`, `TAOH_NOTIFICATION_ENABLED`, `TAOH_READS_ENABLE`, `NETWORKING_VERSION`
- **Opens:** `<div class="wrapper">`, `<main class="page-body">` -- must be closed by footer

### Surgical Edit Notes
- To add a new nav item: insert a new `<li class="nav-item">` inside the `<ul class="navbar-nav">` block (around line 162-307)
- To modify the user dropdown: edit the `dropdown-item-list` div (around lines 400-470)
- To change logo display: edit the `.logo-box` div (around lines 118-132)
- Active state for nav items is determined by `stristr()` / `taoh_parse_url()` checks at the start of each `<li>`
- The `NETWORKING_VERSION == 5` check at line 113 controls whether the header renders at all (for room views in older versions)

---

## header_mobile.php

**Full Path:** `/wpl/club/core/themes/header_mobile.php`
**Size:** 111,404 bytes | **Lines:** ~2,500+

### Purpose/Role
**Mobile-specific header** with a simplified navigation bar. Structurally similar to `header.php` but adds `mobile-app-header` CSS class and a `mobile-user-menu` div. Navigation uses the same SVG icons but renders for mobile layout.

### Key Differences from header.php
- CSS class: `mobile-app-header` on the `<header>` element
- Has a separate `mobile-user-menu` div (hidden by default, `display: none`)
- Handles three user states: logged in, page-specific avatar (TAOH_PAGE_AVATAR), and not logged in
- Not-logged-in state uses `openLoginPopup()` instead of direct login link
- Includes `head.php` at line 29
- Does not include `NETWORKING_VERSION` conditional wrapping
- Off-canvas menu includes same Growth Tools and Learning Hub structure

### Integration Points
- **Includes:** `head.php` (line 29)
- Opens `<div class="wrapper">`, `<header>`, `<main class="page-body">`

---

## header_new.php

**Full Path:** `/wpl/club/core/themes/header_new.php`
**Size:** 10,446 bytes | **Lines:** ~166

### Purpose/Role
**Simplified header** with basic navigation, Growth Tools dropdown, About dropdown, Resources dropdown. Used for pages that need a cleaner layout without the full app nav bar.

### Template Structure
```
[include head.php]
<header class="header-area bg-white border-bottom header1">
  <div class="container">
    <div class="row">
      [Logo]
      [Menu: Hires | Growth Tools dropdown | About dropdown | Resources dropdown]
      [Login/Signup button OR User dropdown with notifications]
    </div>
  </div>
  [Off-canvas mobile menu]
</header>
```

### Navigation Items
- **Hires** - links to `TAOH_SITE_URL_ROOT`
- **Growth Tools** - dropdown with `taoh_available_apps()` + JusASK + Tips & Tricks
- **About** - dropdown with About + TAO.ai
- **Resources** - dropdown with Blogs (`TAOH_READS_URL`), Flashcards (`TAOH_FLASHCARD_URL`), Obvious Baba (`TAOH_OBVIOUS_URL`)

### User Menu (logged in)
- Notification dropdown with badge count
- User dropdown: Referral, Settings, Log out

### Off-Canvas Menu
- User profile + name
- Hires, Growth Tools sub-menu, About sub-menu, Account sub-menu

### Inline CSS/JS
- Notifier badge animation CSS (pulse keyframes)
- `openNav()` / `closeNav()` sidebar JS functions

### Integration Points
- **Includes:** `head.php` (line 1)
- **Functions:** `taoh_user_is_logged_in()`, `taoh_user_full_name()`, `taoh_get_profile_image()`, `taoh_available_apps()`
- Does NOT open `<main>` tag -- caller must do this

---

## header-iframe.php

**Full Path:** `/wpl/club/core/themes/header-iframe.php`
**Size:** 1,796 bytes | **Lines:** ~104

### Purpose/Role
**Minimal header for iframe-embedded pages.** Includes `head.php` and renders a simple navbar with logo, sidebar toggle JS, and notifier badge CSS.

### Template Structure
```
[include head.php]
<style> .notifier, .bell, .badges CSS + @keyframes pulse </style>
<script> openNav(), closeNav() </script>
```

### Key Details
- Sets `$current_app`, `$taoh_home_url`, `$about_url`
- Fetches `$data = taoh_user_all_info()` and `$ptoken`
- Only outputs CSS and JS -- no visible HTML beyond what `head.php` provides
- The notifier CSS is identical to `header_new.php`

### Integration Points
- **Includes:** `head.php` (line 12)
- Designed for pages loaded inside iframes where a full header is not needed

---

## header_landing.php

**Full Path:** `/wpl/club/core/themes/header_landing.php`
**Size:** 6,155 bytes | **Lines:** ~144

### Purpose/Role
**Blog/reads landing page header.** Fixed-position header with top nav menu, search bar, social icons, logo, ad banner, and category navigation. Uses a magazine/newspaper-style layout.

### PHP Setup (lines 1-27)
- **Includes `head_landing.php`** (line 2)
- Fetches tags list via API: `core.content.get` with `type=tags_list`, `tags=latest`
- Slices response to first 7 tags for navigation

### Template Structure
```html
<div class="boxed" id="wrapper">
  <div class="header-res-height"> <!-- responsive height spacer -->
    <header style="position: fixed; top: 0; z-index: 100001;">
      <div id="theme-header" class="container theme-header">
        <!-- Top nav (TAOH_MENU_HEADER_1 JSON menu) -->
        <!-- Search bar (action: TAOH_READS_LP_URL/search) -->
        <!-- Social icons (Facebook, Twitter) -->
        <!-- Logo + Ad banner (TAOH_ADS_BANNERS[0]) -->
        <nav id="main-nav">
          <!-- Category nav: Home + dynamic tag categories + Blogs -->
        </nav>
      </div>
    </header>
  </div>
```

### Responsive Height CSS (`.header-res-height`)
| Breakpoint | Height |
|---|---|
| Default | 140px |
| 375px | 145px |
| 425px | 155px |
| 500px | 160px |
| 768px | 178px |
| 1024px | 218px |

### Data Sources
- `TAOH_MENU_HEADER_1` - JSON-encoded top menu items
- `TAOH_ADS_BANNERS` - JSON-encoded banner ads array (uses index 0)
- Tags API response - dynamic category navigation

### Integration Points
- **Includes:** `head_landing.php` (line 2)
- **API call:** `core.content.get` for tags
- **Constants:** `TAOH_READS_LP_URL`, `TAOH_LP_HOME_URL`, `TAOH_SITE_DOC_ROOT_FILE`, `TAOH_ADS_BANNERS`, `TAOH_MENU_HEADER_1`

---

## header_lp.php

**Full Path:** `/wpl/club/core/themes/header_lp.php`
**Size:** 10,287 bytes | **Lines:** ~200

### Purpose/Role
**Content listing page (LP) header.** Similar to `header_landing.php` but adds a mobile slide-out sidebar, active category highlighting, and taller responsive heights.

### Key Differences from header_landing.php
- **Includes `head_lp.php`** instead of `head_landing.php`
- Parses `$category` from URL (`taoh_parse_url_lp(1) == 'category'`)
- Opens `<body id="top">` tag
- Has slide-out mobile sidebar (`#slide-out`) with search, social icons, and mobile menu
- Active category highlighting in nav (compares `$header2_val` to `$category`)
- Wraps in `<div class="wrapper-outer">` > `<div id="wrapper" class="boxed">` > `<div class="inner-wrapper">`
- Uses `taoh_lp_category_link()` for category URLs

### Responsive Height CSS (`.header-res-height`)
| Breakpoint | Height |
|---|---|
| Default | 170px |
| 375px | 185px |
| 425px | 190px |
| 500px | 200px |
| 768px | 228px |
| 900px | 280px |
| 1024px | 228px |

### Integration Points
- **Includes:** `head_lp.php` (line 2)
- **Functions:** `taoh_parse_url_lp()`, `taoh_lp_category_link()`
- Opens multiple wrapper divs that must be closed by `footer_lp.php`

---

## header_events_landing.php

**Full Path:** `/wpl/club/core/themes/header_events_landing.php`
**Size:** 66,493 bytes | **Lines:** ~1,500+

### Purpose/Role
**Events-specific landing page header.** Full application header similar to `header.php` but tailored for event landing pages. Includes super admin banner, full nav bar, and event-specific integrations.

### PHP Setup
- Sets same variables as `header.php`: `$current_app`, `$taoh_home_url`, `$user_data`, `$user_ptoken`, CAPTCHA, content tokens
- Fetches user API data for profile
- **Includes `head.php`** at line 57
- Includes super admin init banner

### Template Structure
Nearly identical to `header.php` with:
- Same nav items (Club, Events, Jobs, Asks)
- Same notification dropdown
- Same user menu dropdown
- Same off-canvas menu
- Same Learning Hub modal

### Integration Points
- **Includes:** `head.php` (line 57)
- **Paired with:** `footer_events_landing.php`
- Opens `<div class="wrapper">`, `<header>`, `<main class="page-body">`

---

## chatbot.php

**Full Path:** `/wpl/club/core/themes/chatbot.php`
**Size:** 18,958 bytes | **Lines:** ~365

### Purpose/Role
**AI chatbot widget** that provides three bots in an accordion interface: Support Dojo, Sidekick, and Obvious Baba. Included conditionally in footer files.

### PHP Setup (lines 1-13)
- Builds `$share_link` from `$_SERVER`
- Sets `$taoh_home_url`, `$app_url`, `$app_action`
- Determines `$current_page` for bot context

### HTML Structure
```html
<style> .supportChatbot, .sideChatbot, .obviousChatbot max-height/scroll </style>

<!-- Chatbot accordion (hidden by default, id="dojo_bot_show") -->
<div class="chatbot-acc" id="dojo_bot_show" style="display: none;">
  <div class="accordion" id="accordionExample">
    <div class="card">
      <!-- Header: bot logo, title, dropdown to switch bots, close button -->
      <!-- #dojo_support_Form - Support Dojo chat panel -->
      <!-- #sidekick_Form - Sidekick chat panel -->
      <!-- #obvious_baba_Form - Obvious Baba chat panel -->
    </div>
  </div>
</div>

<!-- Floating menu button (desktop only, d-none d-sm-block) -->
<div class="animated-menu menu-container">
  <label onclick="side_openForm(event)">
    <img src="[Group 194.svg]" />
  </label>
</div>
```

### Visibility Condition (line 149)
```php
if((taoh_user_is_logged_in() && (TAOH_ENABLE_OBVIOUSBABA || TAOH_ENABLE_SIDEKICK || TAOH_ENABLE_JUSASK)))
```

### Key JS Functions

| Function | Purpose |
|---|---|
| `side_openForm(event)` | Shows chatbot panel, opens Support Dojo tab |
| `side_closeForm()` | Hides chatbot panel |
| `taoh_jusask_chat_init(chat_ask, bot_name, bot_desc, ask_bot, chatarea, chaticon, contact_support, bot_btn)` | Sends AJAX POST to `taoh_site_ajax_url()` with action `taoh_all_chatbot_get` |
| `render_jusask_chat_template(data, slot, bot_name, chaticon)` | Renders bot/user messages into the chat area, handles ticket display |

### Bot Configuration Constants
| Bot | Name Const | Title Const | Description Const | Message Const | Image Const | Ask Const |
|---|---|---|---|---|---|---|
| Support | `TAOH_JUSASK_SUPPORT_BOT_ASK` | `TAOH_JUSASK_SUPPORT_BOT_TITLE` | `TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION` | `TAOH_JUSASK_SUPPORT_BOT_MSG1` | `TAOH_JUSASK_SUPPORT_BOT_IMG` | `TAOH_JUSASK_SUPPORT_BOT_ASK` |
| Sidekick | `TAOH_JUSASK_BOT_1_NAME` | `TAOH_JUSASK_BOT_1_TITLE` | `TAOH_JUSASK_BOT_1_DESCRIPTION` | `TAOH_JUSASK_BOT_1_MSG1` | `TAOH_JUSASK_BOT_1_IMG` | `TAOH_JUSASK_BOT_1_ASK` |
| Obvious Baba | `TAOH_JUSASK_BOT_2_NAME` | `TAOH_JUSASK_BOT_2_TITLE` | `TAOH_JUSASK_BOT_2_DESCRIPTION` | `TAOH_JUSASK_BOT_2_MSG1` | `TAOH_JUSASK_BOT_2_IMG` | `TAOH_JUSASK_BOT_2_ASK` |

### Bot Logo Map
| Bot | SVG File |
|---|---|
| Support Dojo | `Group 194.svg` |
| Sidekick | `side-kick-svg.svg` |
| Obvious Baba | `Obviousbaba.svg` |

### AJAX Endpoint
- **URL:** `taoh_site_ajax_url()`
- **Action:** `taoh_all_chatbot_get`
- **Params:** `ask`, `ops`, `bot`, `bot_desc`, `send_to_support`, `current_page`

### Integration Points
- **Included by:** `footer.php` (line 6), `footer_backup.php` (line 5), `footer_events_landing.php` (line 8)
- **Feature flags:** `TAOH_JUSASK_ENABLE`, `TAOH_ENABLE_OBVIOUSBABA`, `TAOH_ENABLE_SIDEKICK`, `TAOH_ENABLE_JUSASK`
- **Input classes:** `.support_ask`, `.side_ask`, `.obvious_ask` (text inputs)
- **Button classes:** `.support_btn`, `.side_btn`, `.obvious_btn` (submit buttons)

---

## footer.php

**Full Path:** `/wpl/club/core/themes/footer.php`
**Size:** 113,007 bytes | **Lines:** ~2,800+

### Purpose/Role
**Primary application footer** -- the second-largest file. Handles chatbot inclusion, networking footer, event-specific includes, login prompt, footer banner ads, footer menu, copyright, modals (Dojo popup, bug report, IndexedDB warning, job post), and all closing scripts (GA, notifications, metrics, referral checks, sitemap, TTL checks).

### PHP Setup (lines 1-9)
- Gets `$curr_page` and `$opt` from URL
- Conditionally includes `chatbot.php` (if `TAOH_ENABLE_OBVIOUSBABA || TAOH_ENABLE_SIDEKICK || TAOH_ENABLE_JUSASK`)
- Conditionally includes networking footer based on `NETWORKING_VERSION` (1, 4, or 5)
- For events pages: includes `events_rsvp_directory.php`, `events_exhibitors.php`, `events_speakers.php`, `events_agenda.php`, `events_rooms.php`, `events_tables.php`, `event_upgrade_modal.php`, `events_footer.php`

### Utility Functions Defined (lines 48-183)
| Function | Purpose |
|---|---|
| `getMaxUploadSize()` | Returns max upload size considering PHP + Nginx limits |
| `convertToBytes($size)` | Converts size strings (e.g., "2M") to bytes |
| `getMaxUploadSizeBytes()` | Wrapper returning just bytes |
| `get_max_upload_size()` | Returns min of upload_max_filesize and post_max_size |
| `convert_to_bytes($val)` | Another size converter |

### Footer HTML Structure (lines 229-365)
```html
<!-- Conditional on NETWORKING_VERSION -->
<footer class="page-footer">
  <section class="footer-area" style="background: #1E1C1C;">
    <!-- Tracking pixel (logged-in users) -->
    <!-- Footer menu from TAOH_FOOTER_MENU_ARRAY or default links -->
    <!-- Footer menu items: Home, Terms dropdown, By Tao.ai, Feedback, Donate -->
    <!-- Copyright line with TAOH_SITE_NAME_SLUG, TheWORKCompany -->
    <!-- Report an issue link (logged-in only) -->
  </section>
</footer>
```

### Modals Defined
1. **Basic Settings Modal** - `require_once TAOH_SITE_PATH_ROOT . '/core/basic-settings-modal.php'`
2. **Dojo Suggestion Popup** (`#dojo-popup`) - Toast-like popup
3. **Dojo V1 Modal** (`#dojoV1Modal`) - Full modal with Dojo mascot SVG
4. **Chatbot Accordion** (`.chatbot-acc d-none`) - Static demo chatbot
5. **Bug Report Modal** (`#reportBugModal`) - Form with description, email, human verification
6. **IndexedDB Warning Modal** (`#indexedDBWarningModal`)
7. **Job Post Modal** (`#jobPostModal`)

### Closing Scripts (after footer HTML)
- `highlight-green` animation CSS
- `TAOH_CUSTOM_FOOT` output (with `var_dump(get_defined_vars())` -- likely debug)
- Google Analytics (gtag.js) if `TAOH_SITE_GA_ENABLE`
- Client timezone cookie fallback
- **Key JS Functions:**

| Function | Purpose |
|---|---|
| `checkReferralStatus()` | AJAX check, shows toast if session expired, runs every 60s |
| `taoh_notification_init(call_from)` | Fetches notifications via AJAX, renders list, updates badge |
| `render_notification_list_template(data, call_from)` | Builds notification HTML |
| `taoh_counter_init(call_at)` | Updates notification badge count |
| `updateStatus(process)` | AJAX to update user status |
| `checksitemap()` | Triggers sitemap generation if daily file missing |
| `checkProfileCompletion()` | Shows modal if profile incomplete (anonymous user), every 60s |
| `checksuperadminInit()` | Shows toast if super admin hasn't completed setup, every 60s |
| `mertricsLoad()` | Pushes view metrics for `.dash_metrics` elements |
| `checkTTL(index_name, store_name)` | IndexedDB TTL expiration check |
| `triggerNextRequest(callback, ttl)` | Delayed callback helper |
| `showIndexedDBWarning()` | Shows warning modal |
| `taoh_metrix_ajax(app, arr_cont)` | Bulk metrics push |
| `save_metrics(app, metrics_type, conttoken)` | Saves metrics to IndexedDB |
| `moveMetricstoRedis()` | Transfers metrics from IndexedDB to server |

### Interval Timers
- `checkReferralStatus()` - every 60s
- `taoh_notification_init(0)` - every `TAOH_NOTIFICATION_LOOP_TIME_INTERVAL` ms
- `checkProfileCompletion()` - every 60s (not on settings page)
- `checksuperadminInit()` - every 60s
- `savetaodata()` + `checkTTL()` - every 10s

### Post-HTML PHP (lines ~590-620)
- Handles `?secret_delete` and `?secret_delete_force` params to delete cache files
- Timezone cookie redirect for events pages
- `taoh_cacheops('logpush')` and `taoh_exit()`

### Integration Points
- **Includes:** `chatbot.php`, networking footers (`networking_footer1.php`, `networking_footer4.php`, `networking_footer5.php`), event components, `basic-settings-modal.php`
- **Closes:** `</main>`, `</div>` (wrapper), `</body>`, `</html>`
- **Feature flags:** `TAOH_JUSASK_ENABLE`, `TAOH_ENABLE_OBVIOUSBABA`, `TAOH_ENABLE_SIDEKICK`, `NETWORKING_VERSION`, `TAOH_NOTIFICATION_ENABLED`, `TAOH_NOTIFICATION_STATUS`, `TAOH_MESSAGE_ENABLE`, `TAOH_SITE_GA_ENABLE`, `TAOH_SITE_DONATE_ENABLE`, `TAOH_FOOTER_BANNER_AD`, `TAOH_FOOTER_MENU_ARRAY`

### Surgical Edit Notes
- To add footer links: edit the `TAOH_FOOTER_MENU_ARRAY` logic (around line 250) or the fallback block (line 264)
- To add a new modal: insert before `</div><!-- end class="wrapper" -->` (around line 553)
- To modify notification behavior: edit `taoh_notification_init()` (JS block)
- The `var_dump(get_defined_vars())` at line 113 in the `TAOH_CUSTOM_FOOT` block is likely a debug artifact

---

## footer_backup.php

**Full Path:** `/wpl/club/core/themes/footer_backup.php`
**Size:** 23,821 bytes | **Lines:** ~622

### Purpose/Role
**Older/simpler footer** used with `header_new.php` and `header_mobile.php`. Simpler than `footer.php` -- no event includes, no networking footer versions, fewer modals.

### Key Differences from footer.php
- Includes `chatbot.php` only if `TAOH_JUSASK_ENABLE` (not the triple-flag check)
- Includes `networking_footer.php` (generic, not versioned)
- Login prompt for non-logged-in users
- Uses `TAOH_CUSTOM_FOOTER` for custom footer link overrides
- Has simpler footer menu (TAO.ai, TheWorkTimes, NoWorkerLeftBehind, Employers, Partners, Professionals)
- Includes Support SVG icon link and Feedback link
- Same Google Analytics, timezone, TTL, and metrics logic as `footer.php`
- Same `secret_delete` / `secret_delete_force` cache cleanup

### Integration Points
- **Includes:** `chatbot.php`, `networking_footer.php`
- **Closes:** `</main>`, `</div>`, `</body>`, `</html>`
- **Post-HTML:** same cache cleanup + timezone redirect + `taoh_exit()`

---

## footer_landing.php

**Full Path:** `/wpl/club/core/themes/footer_landing.php`
**Size:** 6,402 bytes | **Lines:** ~129

### Purpose/Role
**Landing/reads page footer.** Simple footer with copyright, links (TAO.ai, TheWorkTimes, NoWorkerLeftBehind, Employers, Partners, Professionals), Support, Feedback, Privacy/Terms/Conduct links.

### Key Features
- `TAOH_CUSTOM_FOOT` output with `var_dump(get_defined_vars())`
- Google Analytics integration
- Timezone cookie fallback
- `checkTTL()` function (IndexedDB)
- Closes `</body></html>`

### Integration Points
- **Paired with:** `header_landing.php`

---

## footer_lp.php

**Full Path:** `/wpl/club/core/themes/footer_lp.php`
**Size:** 11,977 bytes | **Lines:** ~174

### Purpose/Role
**LP content listing footer.** Includes a rich footer widget area with **three columns of related posts** pulled from API, ad banner, and multi-line footer menus from constants.

### PHP Setup
- Fetches tag list via `content.get.taglist` API
- Fetches footer ad banner from `TAOH_ADS_BANNERS[1]`

### Template Structure
```html
<!-- Bottom ad banner -->
<footer id="theme-footer">
  <div class="container">
    <!-- 3-column footer widget area -->
    <!-- Column 1: Posts from tag[0] via blog_lp_related_post() -->
    <!-- Column 2: Posts from tag[1] -->
    <!-- Column 3: Posts from tag[2] -->
  </div>
</footer>
<!-- Footer bottom -->
<div class="footer-bottom">
  <!-- TAOH_MENU_FOOTER_LINE_1 links -->
  <!-- TAOH_MENU_FOOTER_LINE_2 links (prefixed with TAOH_TEMP_SITE_URL) -->
  <!-- Copyright -->
  <!-- TAOH_MENU_FOOTER_LINE_3 links (Privacy, Terms, etc.) -->
</div>
<!-- Scroll to top, fb-root, reading-position-indicator -->
</body></html>
```

### Data Sources
- `content.get.taglist` API for footer tag list
- `blog_lp_related_post($tag, 3)` for each column (3 posts per tag)
- `TAOH_ADS_BANNERS` JSON for banner ads
- `TAOH_MENU_FOOTER_LINE_1`, `_LINE_2`, `_LINE_3` for menu links

### Integration Points
- **Paired with:** `header_lp.php`
- **Functions:** `blog_lp_related_post()`, `taoh_lp_blog_link()`, `slugify2()`
- Closes all wrapper divs opened by `header_lp.php`

---

## footer_events_landing.php

**Full Path:** `/wpl/club/core/themes/footer_events_landing.php`
**Size:** 28,493 bytes | **Lines:** ~500

### Purpose/Role
**Events landing page footer.** Similar to `footer.php` but with event-specific adjustments. Includes chatbot, footer banner ads, footer menu, modals, and closing scripts.

### Key Differences from footer.php
- Includes `chatbot.php` with same triple-flag check
- No networking footer includes
- No event-specific component includes
- Has same footer menu structure (from `TAOH_FOOTER_MENU_ARRAY` or defaults)
- IndexedDB warning modal with "retry" logic (retries 3 times before showing warning)
- Has `termsLink`/`termsList` toggle JS (unlike footer.php which uses Bootstrap dropdown)
- Includes `save_metrics()` and `moveMetricstoRedis()` functions
- Same GA, timezone, TTL, sitemap, super admin check logic

### Integration Points
- **Paired with:** `header_events_landing.php`
- **Closes:** `</main>`, `</div>`, `</body>`, `</html>`
- **Post-HTML:** timezone redirect + `taoh_cacheops('logpush')` + `taoh_exit()`

---

## Page Assembly Matrix

| Page Type | Head | Header | Footer | Chatbot |
|---|---|---|---|---|
| Main app (Club, Events, etc.) | `head.php` (via header) | `header.php` | `footer.php` | Via footer |
| Mobile view | `head.php` (via header) | `header_mobile.php` | `footer_backup.php` | Via footer |
| Simple/New layout | `head.php` (via header) | `header_new.php` | `footer_backup.php` | Via footer |
| Iframe embed | `head.php` (via header) | `header-iframe.php` | (varies) | N/A |
| Blog landing | `head_landing.php` (via header) | `header_landing.php` | `footer_landing.php` | No |
| Content listing (LP) | `head_lp.php` (via header) | `header_lp.php` | `footer_lp.php` | No |
| Events landing | `head.php` (via header) | `header_events_landing.php` | `footer_events_landing.php` | Via footer |

---

## Constants Reference

### Site Configuration
| Constant | Used In | Purpose |
|---|---|---|
| `TAOH_SITE_URL_ROOT` | All files | Base URL for the site |
| `TAOH_SITE_TITLE` | head files | Default page title |
| `TAOH_SITE_LOGO` | headers | Site logo image URL |
| `TAOH_SITE_FAVICON` | head files | Favicon URL |
| `TAOH_SITE_NAME_SLUG` | header, footer | Display name (e.g., "#Hires") |
| `TAOH_PLUGIN_PATH_NAME` | headers | URL path segment for the plugin |
| `TAOH_CDN_PREFIX` | head.php | CDN base URL |
| `TAOH_CDN_CSS_PREFIX` | head files | CDN CSS path |
| `TAOH_CDN_JS_PREFIX` | head files | CDN JS path |
| `TAOH_CSS_JS_VERSION` | head.php | Cache-busting version string |
| `TAOH_LOADER_GIF` | head.php | Loading spinner image |

### Feature Flags
| Constant | Controls |
|---|---|
| `TAOH_CLUBS_ENABLE` | Club nav item visibility |
| `TAOH_EVENTS_ENABLE` | Events nav item visibility |
| `TAOH_JOBS_ENABLE` | Jobs nav item visibility |
| `TAOH_ASKS_ENABLE` | Asks nav item visibility |
| `TAOH_MESSAGE_ENABLE` | Messages nav + connections |
| `TAOH_READS_ENABLE` | Reads/Learning Hub items |
| `TAOH_NOTIFICATION_ENABLED` | Notification bell (0=off, 1=bell only, 2=auto-poll) |
| `TAOH_NOTIFICATION_STATUS` | 0=manual, 2=auto-poll |
| `TAOH_JUSASK_ENABLE` | Chatbot inclusion |
| `TAOH_ENABLE_SIDEKICK` | Sidekick bot |
| `TAOH_ENABLE_OBVIOUSBABA` | Obvious Baba bot |
| `TAOH_ENABLE_JUSASK` | Support Dojo bot |
| `TAOH_SITE_GA_ENABLE` | Google Analytics |
| `TAOH_SITE_DONATE_ENABLE` | Donate link in footer |
| `NETWORKING_VERSION` | Networking footer version (1, 4, 5) |

### Custom Injection Points
| Constant | Where | Purpose |
|---|---|---|
| `TAOH_CUSTOM_HEAD` | `head.php`, `head_landing.php`, `head_lp.php` | Inject custom HTML into `<head>` |
| `TAOH_CUSTOM_FOOT` | `footer.php`, `footer_backup.php`, `footer_landing.php`, `footer_events_landing.php` | Inject custom HTML before closing scripts |
| `TAOH_CUSTOM_FOOTER` | `footer_backup.php` | Replace default footer links |
| `TAOH_FOOTER_MENU_ARRAY` | `footer.php`, `footer_events_landing.php` | JSON array of footer menu items |
| `TAOH_GA_CODE` | footer files | Custom GA code block (overrides default gtag) |
| `TAOH_PAGE_URL` | headers | Override home URL |
| `TAOH_PAGE_LOGO` | headers | Override logo per page |
| `TAOH_PAGE_FAVICON` | head files | Override favicon per page |

### Page-Level SEO Constants
| Constant | Meta Tag |
|---|---|
| `TAO_PAGE_TITLE` | `<title>`, og:title, twitter:title |
| `TAO_PAGE_DESCRIPTION` | description, og:description, twitter:description |
| `TAO_PAGE_IMAGE` | og:image, twitter:image |
| `TAO_PAGE_AUTHOR` | author, sailthru.author |
| `TAO_PAGE_KEYWORDS` | keywords |
| `TAO_PAGE_ROBOT` | robots |
| `TAO_PAGE_CANONICAL` | canonical link |
| `TAO_PAGE_TYPE` | page-type |
| `TAO_PAGE_CATEGORY` | page-category-name |
| `TAO_PAGE_TWITTER_SITE` | twitter:site, twitter:creator |
| `TAO_PAGE_GA` / `TAOH_PAGE_GA` | GA tracking ID override |
