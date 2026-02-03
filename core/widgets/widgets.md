# Widgets Module -- Comprehensive Developer Documentation

> **Path:** `/wpl/club/core/widgets/`
> **Platform:** TAOH (Tao.ai / Hires) -- PHP server-rendered widget system
> **Generated:** 2026-01-30
> **Total Files:** 54 PHP files across 12 directories

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Root-Level Files](#2-root-level-files)
3. [ads/ -- Advertisement Widgets](#3-ads----advertisement-widgets)
4. [apps/ -- Application Widgets](#4-apps----application-widgets)
5. [comments/ -- Comment System](#5-comments----comment-system)
6. [events/ -- Event Widgets](#6-events----event-widgets)
7. [feeds/ -- Feed Comment System](#7-feeds----feed-comment-system)
8. [follow/ -- Follow System](#8-follow----follow-system)
9. [general/ -- General-Purpose Widgets](#9-general----general-purpose-widgets)
10. [invite/ -- Invitation System](#10-invite----invitation-system)
11. [likes/ -- Likes System](#11-likes----likes-system)
12. [reads/ -- Reading/Blog Content Widgets](#12-reads----readingblog-content-widgets)
13. [share/ -- Social Sharing Widgets](#13-share----social-sharing-widgets)
14. [API Endpoint Summary](#14-api-endpoint-summary)
15. [AJAX Action Summary](#15-ajax-action-summary)
16. [Constants and Global Variables](#16-constants-and-global-variables)
17. [Security Considerations and Known Bugs](#17-security-considerations-and-known-bugs)
18. [Integration Points](#18-integration-points)
19. [Key Edit Points Quick Reference](#19-key-edit-points-quick-reference)

---

## 1. Architecture Overview

### Pattern

The widget system follows an **include-based dispatch pattern**:

1. **`widget_functions.php`** is the central registry. It defines ~35 `taoh_*` wrapper functions.
2. Each wrapper function calls `include_once()` to pull in the actual widget template from a subdirectory.
3. Widget templates are **self-contained PHP/HTML views** that call the TAOH API layer (`taoh_apicall_get()` / `taoh_apicall_post()`) and render HTML directly.
4. AJAX endpoints exist in `follow/ajax.php`, `likes/ajax.php`, and `feeds/action.php` for client-side interactions.

### Data Flow

```
Page Controller
  -> taoh_*_widget($data)          [widget_functions.php]
    -> include_once('subdir/file.php')
      -> taoh_apicall_get/post()   [TAOH API layer]
      -> Render HTML + inline JS
```

### Key Conventions

- **`$data`** array is passed from caller via scope inheritance (PHP `include` shares calling scope).
- **`$taoh_call`** = API endpoint name (e.g., `'core.content.get'`, `'jobs.get'`).
- **`$taoh_vals`** = associative array of API parameters.
- **`taoh_get_dummy_token()`** / **`taoh_get_dummy_token(1)`** = auth token helpers.
- **`TAOH_SITE_URL_ROOT`**, **`TAOH_CDN_PREFIX`**, **`TAOH_API_SECRET`** = global constants for URL construction.
- **`taoh_apicall_get($call, $vals)`** / **`taoh_apicall_post($call, $vals)`** = unified API caller.
- **`taoh_site_ajax_url()`** = returns the AJAX handler URL for jQuery.post() calls.

### Index Files

All `index.php` files across every subdirectory contain only `// silence is golden` -- they are directory traversal guards with no logic.

**Files:** `index.php`, `ads/index.php`, `apps/index.php`, `comments/index.php`, `feeds/index.php`, `follow/index.php` (empty), `general/index.php`, `likes/index.php`, `reads/index.php`, `share/index.php`

---

## 2. Root-Level Files

### `widget_functions.php`

**Purpose:** Central widget registry and dispatcher. Every widget in the system is invoked through a function defined here.

**Key Functions Defined (all are thin wrappers that `include_once` the actual widget):**

| Function | Includes | Parameter | Notes |
|---|---|---|---|
| `taoh_sponsor_slider_widget($eventtoken)` | `events/sponsor.php` | event token | |
| `taoh_follow_widget($data)` | `follow/follow.php` | token + type | |
| `taoh_likes_widget($data)` | `likes/likes.php` | conttoken + type | |
| `taoh_comments_widget($data)` | `comments/comments_details.php` + `comments/comments_post.php` | conttoken + conttype + label | Full display + form |
| `taoh_feeds_comments_widget($data)` | `feeds/comments_details.php` + `feeds/comments_post.php` | same shape | Feed-specific variant |
| `taoh_comments_widget_get($data)` | `comments/comments_details.php` | same | Display only |
| `taoh_comments_widget_post($data)` | `comments/comments_post.php` | same | Post form only |
| `taoh_share_widget($data)` | `share/share.php` | share_data + conttype + ptoken + conttoken + widget_id | Full social share |
| `taoh_social_share_widget($data)` | `share/social_share.php` | same shape | Simpler variant |
| `taoh_readables_widget($widget_type='')` | `reads/readables.php` | 'new' or empty | |
| `taoh_obviousbaba_widget()` | `reads/obviousbaba.php` | none | |
| `taoh_jusask_widget($widget_type='')` | `reads/jusask.php` | 'new' or empty | |
| `taoh_leftmenu_widget()` | `general/leftmenu.php` | none | |
| `taoh_video_widget($data)` | `general/video.php` | YouTube URL | |
| `taoh_copynshare_widget()` | `general/copynshare.php` | none | |
| `taoh_calendar_widget($data)` | `general/calendar.php` | event data with dates | |
| `taoh_reads_category_widget()` | `reads/categories.php` | none | |
| `taoh_reads_search_widget()` | `reads/search.php` | none | |
| `taoh_newsletter_search_widget()` | `reads/newsletter_search.php` | none | |
| `taoh_ads_widget($ads=0)` | `general/ads.php` -> calls `taoh_ads_general($ads)` | ads index | |
| `taoh_stats_widget()` | `general/stats.php` | none | |
| `taoh_tao_widget()` | **DISABLED** (commented out) | -- | Was `general/tao.php` |
| `taoh_wemet_video_widget()` | `general/wemet.php` | none | |
| `taoh_jobs_widget()` | `apps/jobs.php` | none | |
| `taoh_free_monthly_jobs_widget()` | `apps/free_monthly_jobs.php` | none | |
| `taoh_free_promotional_jobs_widget()` | `apps/free_promotional_jobs.php` | none | |
| `taoh_asks_widget()` | `apps/asks.php` | none | |
| `taoh_wordSlide_widget()` | `general/wordSlider.php` | none | |
| `taoh_invite_widget()` | **DISABLED** (commented out) | -- | Old invite system |
| `taoh_invite_friends_widget($title,$app,$data='')` | `invite/invite_friends.php` | title, app, optional data | Gated: `taoh_user_is_logged_in()` |
| `taoh_get_recent_jobs($widget_type='')` | `general/recent_jobs.php` | 'new' or empty | |
| `taoh_jobs_networking_widget()` | `general/jobs_networking.php` | none | |
| `taoh_new_ads_widget()` | `general/new_ads.php` | none | |
| `taoh_new_common_ads_widget($app,$type='square',$qty=1)` | `ads/common_ads.php` | app, type, qty | |
| `taoh_user_profile_short($ptoken)` | `general/short_profile.php` | user ptoken | |
| `taoh_type_widget($data='')` | `general/type_widget.php` | 'employer' or empty | |
| `taoh_recent_event_widget()` | `events/recent_events.php` | none | |
| `taoh_recent_multiple_event_widget($exclude='')` | `events/recent_multiple_events.php` | eventtoken to exclude | |
| `taoh_recent_events_full_display($exclude='')` | `events/recent_events_full_display.php` | eventtoken to exclude | |

**Integration Points:**
- Called by page controllers / template files throughout the application.
- All includes are relative paths, so this file must be included from within the widgets directory or with the correct working directory.

**Edit Points:**
- **Line 87-89:** `taoh_tao_widget()` is commented out (disabled TAO tips).
- **Line 116:** `taoh_invite_widget()` is commented out (disabled old invite).
- To add a new widget: define a new `taoh_*` function here and create the corresponding template file.

---

## 3. ads/ -- Advertisement Widgets

### `ads/common_ads.php`

**Purpose:** Renders ad cards fetched from the internal ads API. Used for context-aware ads (per-app, per-type).

**API Endpoint Called:**
- `ads.get` via `taoh_apicall_get()`

**Parameters Sent:**
- `token` = `taoh_get_dummy_token()`
- `type` = `$type` (square, banner, etc.) -- passed from `taoh_new_common_ads_widget($app, $type, $qty)`
- `app` = `$app` -- the application context
- `qty` = `$qty` -- number of ads requested
- `ptype` = `taoh_user_type()` -- user type for targeting

**Rendered Output:** Card with title, image (256px width), subtitle, link. Iterates over `$return['output']` array.

**Key Edit Points:**
- **Line 8:** `$taoh_vals['ptype']` controls user-type targeting.
- **Line 25:** Image width hardcoded to 256.
- Output uses `TAOH_SITE_URL_ROOT.$return['link']` for ad destination links.

---

## 4. apps/ -- Application Widgets

### `apps/asks.php`

**Purpose:** Sidebar widget showing 4 recent "Asks" (Q&A) items.

**API Endpoint Called:**
- `api/anony.asks.asks.search.get` via `taoh_apicall_get()`

**Parameters:** `mod=asks`, `offset=0`, `limit=4`

**Rendered Output:** Card with title "Asks" listing up to 4 questions with links to `/asks/d/{slug}-{conttoken}`.

**Key Edit Points:**
- **Line 10:** `limit=4` controls number of asks shown.
- **Line 29:** URL pattern: `TAOH_SITE_URL_ROOT."/asks/d/".slugify2($value->title)."-".$value->conttoken`

### `apps/jobs.php`

**Purpose:** Sidebar widget showing 4 recent job listings.

**API Endpoint Called:**
- `jobs.get` via `taoh_apicall_get()`

**Parameters:** `mod=jobs`, `geohash`, `token`, `ops` (default 'hires'), `local=TAOH_JOBS_GET_LOCAL`, `search`, `limit=4`, `offset`, `filters`, `cache_time=30`

**Rendered Output:** Card titled "Jobs" listing jobs with title + company links. Company extracted from `$value->company` array using `:>` delimiter.

**Key Edit Points:**
- **Line 18:** `ops => 'hires'` is the default operation type.
- **Line 21:** `limit=4` controls job count.
- **Line 35:** Bug: `json_decode($data, true)` returns array but code uses `$fetch_arr->result` with object syntax.
- **Line 44:** Company parsing: `list($pre, $company) = explode(':>', $value1);`
- **Line 49:** Job URL: `TAOH_SITE_URL_ROOT."/jobs/d/".slugify2($value->title)."-".$value->conttoken`
- **Line 52:** Company chat URL: `TAOH_SITE_URL_ROOT."/jobs/chat/orgchat/".$company_key."/".$company`

### `apps/free_monthly_jobs.php`

**Purpose:** Identical to `apps/jobs.php`. Displays free monthly job listings. Same API call, same code, no differences.

### `apps/free_promotional_jobs.php`

**Purpose:** Same as `apps/jobs.php` but adds `TAOH_JOBS_ENABLE` gate.

**Key Difference:** **Line 35:** `if (isset($fetch_arr->result) && TAOH_JOBS_ENABLE)` -- only renders when jobs feature is enabled globally.

---

## 5. comments/ -- Comment System

### `comments/action.php`

**Purpose:** Server-side POST handler for submitting comments. Processes form submissions, validates, and posts to API.

**API Endpoint Called:**
- `core.content.post` via `taoh_apicall_post()`

**Parameters Sent:**
- `token`, `toenter` (full `$_POST`), `ops=add`, `type=comment`, `mod=core`
- `conttoken`, `parentid`, `conttype`
- `redis_action=comments_post`, `redis_store=taoh_intaodb_asks`
- `cache` => `array('remove' => [$conttype."_comments_".$conttoken])`

**Key Functions:**
- `add_comments()` -- validates login, encodes comment via `taoh_title_desc_encode()`, posts to API, redirects with success/error flash message.

**Flow:**
1. Checks `$_POST['action'] == 'save'`
2. Validates comment not empty
3. Calls `add_comments()` which POSTs to `core.content.post`
4. Redirects to `$_POST['redirect']` with flash message

**Key Edit Points:**
- **Line 36-37:** Cache invalidation key: `$conttype."_comments_".$conttoken`
- **Line 46-47:** Redis integration params for real-time updates.
- **Line 51:** API endpoint string: `'core.content.post'`.
- **Line 34:** Comment encoding: `taoh_title_desc_encode($_POST['comment'])`

### `comments/comments_details.php`

**Purpose:** Renders the comment display section -- fetches and displays threaded comments (parent + replies) for a content item.

**API Endpoint Called:**
- `core.content.get` via `taoh_apicall_get()`

**Parameters:** `mod=core`, `token`, `conttoken`, `type=comment`, `conttype=$data['conttype']`, `ops=get`, `cache_name`

**Key Functions:**
- `isJsonString($input)` -- checks if a comment is JSON-formatted (for structured ask responses with key-value pairs).

**Rendered Output:** Accordion-style collapsible comment list with avatars, names, dates, reply threading. Shows total count in header.

**Key Edit Points:**
- **Line 18-20:** Cache key construction: `$data['conttype']."_comments_".$data['conttoken']`
- **Lines 42-47:** Threading logic: `parentid == 0` or empty = root comment, else nested under parent by commentid.
- **Lines 80-97:** JSON comment handling -- supports structured comments from ask forms (iterates key-value pairs, handles nested arrays).
- **Line 65:** Avatar fallback chain: `avatar_image` field > `TAOH_OPS_PREFIX.'/avatar/PNG/128/'.$comment['avatar'].'.png'` > `'default'`.
- **Line 103:** Reply button: sets `data-commentid` attribute, calls `scrollToSection()`.
- **Line 113:** Reply avatar URL pattern: `https://opslogy.com/avatar/PNG/128/{avatar}.png` (hardcoded domain).

**Client-Side JS (embedded):**
- `showLoading(event)` -- disables submit button, shows spinner, submits form after 1000ms delay.
- `scrollToSection()` -- reads `data-commentid` from event target, sets `.parentid` hidden input value, smooth-scrolls to comment form.

### `comments/comments_post.php`

**Purpose:** Renders the comment submission form (visible only to logged-in users).

**Form Action:** `TAOH_SITE_URL_ROOT.'/actions/comments'` (POST method)

**Hidden Fields:**
- `conttoken` = `$data['conttoken']`
- `action` = `save`
- `redirect` = `$_SERVER['REQUEST_URI']` or `$data['redirect']`
- `conttype` = `$data['conttype']`
- `parentid` = empty (set by JS for replies)

**Rendered Output:** Card with textarea for comment, submit button with `data-metrics="comment_click"` for analytics.

**Key Edit Points:**
- **Line 9:** Form action URL: `TAOH_SITE_URL_ROOT.'/actions/comments'`
- **Line 17:** Label is dynamic from `$data['label']` (e.g., "Comment", "Review", "Response").
- **Line 26:** Submit button has `data-metrics="comment_click"` and class `click_action` for analytics.

---

## 6. events/ -- Event Widgets

### `events/recent_events.php`

**Purpose:** Shows a single most recent event as a compact card widget with image, date, title, type, and poster info.

**API Endpoint Called:**
- `events.get` via `taoh_apicall_get()`

**Parameters:** `mod=events`, `key` (TAOH_API_SECRET or TAOH_API_DUMMY_SECRET based on TAOH_EVENTS_GET_LOCAL), `token=taoh_get_dummy_token(1)`, `local=TAOH_EVENTS_GET_LOCAL`, `ops=list`, `limit=1`, `offset=0`, `cache_time=120`

**Rendered Output:** Linked card (`<a>` wrapper) with event image (248x122px), date badge (SVG calendar icon + formatted time), title, event type display (virtual/in-person/hybrid with SVG icons), poster name + avatar.

**Key Edit Points:**
- **Line 3:** `$limit = 1` -- single event.
- **Line 9:** Key selection: local vs remote based on `TAOH_EVENTS_GET_LOCAL`.
- **Lines 33-35:** Default image fallback: `TAOH_SITE_URL_ROOT."/assets/images/event.jpg"`
- **Line 36:** Share link pattern: `TAOH_SITE_URL_ROOT.'/events/d/'.slugify2($event_title).'-'.$eventtoken`
- **Line 37:** Event type defaults to `'virtual'` via null coalescing: `$events_data['event_type'] ?? 'virtual'`
- **Line 51:** Uses `event_time_display($event_utc_start, $event_locality, $event_timezone)` helper.
- **Lines 60-72:** Event type SVG icon rendering (in-person = walking person, hybrid = house+laptop, virtual = video camera).
- **Lines 82-87:** Avatar cascade: `avatar_image` > `avatar` from site assets > default avatar.

### `events/recent_multiple_events.php`

**Purpose:** Shows up to 3 recent events in a compact list format with date boxes (day + month).

**API Endpoint Called:**
- `events.get` via `taoh_apicall_get()`

**Parameters:** Same as `recent_events.php` but `limit=3`. Supports `exclude_eventtoken` parameter.

**Key Edit Points:**
- **Line 3:** `$limit = 3`
- **Lines 18-20:** Exclusion logic: `if($exclude_eventtoken != '') $taoh_vals['exclude_eventtoken'] = $exclude_eventtoken;`
- **Lines 47-52:** Date box uses `event_time_display()` with specific format parameters: `"date", "j"` for day number, `"date", "M"` for month abbreviation.
- **Line 57-58:** Info line shows event type + time: `event_time_display(..., "date", "h:i A")`.

### `events/recent_events_full_display.php`

**Purpose:** Shows up to 4 events in full card format with images, live/not-live/expired badge logic, lobby links, and analytics tracking.

**API Endpoint Called:**
- `events.get` via `taoh_apicall_get()`

**Parameters:** Same pattern, `limit=4`, `cfcc5h=1` (Cloudflare cache flag). Supports `exclude_eventtoken`.

**Rendered Output:** Full event cards with background image overlay effect (glass overlay), date badge, title, type icon, and dynamic live-state badges determined client-side.

**Key Edit Points:**
- **Line 4:** `$limit = 4`
- **Line 15:** `cfcc5h => 1` enables Cloudflare edge caching.
- **Line 42:** Lobby link construction: `TAOH_SITE_URL_ROOT.'/events/chat/id/events/'.$eventtoken`
- **Line 47-49:** Analytics attributes: `data-metrics="view"`, `data-type="events"`, `data-conttoken`.
- **Lines 111-147:** Client-side JS block per event card:
  - `taoh_user_timezone()` for user-local time.
  - `eventLiveState(utc_start, utc_end, locality, timezone)` determines state.
  - Three states: `'live'` (green "Join Now" with play icon), `'before'` (yellow "Event Not Live" with hourglass), else (gray "Event Expired" with ticket icon).
  - Badge HTML injected via `$('#event_badge_{eventtoken}').html(event_badge)`.

### `events/sponsor.php`

**Purpose:** Renders a sponsor/exhibitor carousel widget for events. Uses IndexedDB (`IntaoDB`) client-side to fetch cached event metadata -- no server-side API call.

**PHP Output:** Hidden container div with `id="sponsor_slider"`, injects `$eventtoken` into JS.

**Client-Side JS:**
- Reads from `IntaoDB.getItem(objStores.event_store.name, eventSponsorInfoKey)` where key = `event_MetaInfo_${s_eventtoken}`.
- `buildSponsorCarousel(s_eventtoken, response)` -- builds Bootstrap carousel HTML.
- Sponsor template: image with glass overlay background effect, title (linked if `sponsor.link` exists), description.
- Exhibitor template: `exh_logo`, `exh_subtitle`, `exh_description`, `exh_hero_button_url`.
- Carousel auto-rotates at 2000ms interval.

**Key Edit Points:**
- **Line 18:** PHP `$eventtoken` injected into JS variable.
- **Line 19:** IndexedDB key pattern: `` `event_MetaInfo_${s_eventtoken}` ``
- **Lines 44-59:** Sponsor HTML template (JS template literals).
- **Lines 61-77:** Exhibitor HTML template.
- **Line 83-85:** Carousel interval: `interval: 2000`.

---

## 7. feeds/ -- Feed Comment System

### `feeds/action.php`

**Purpose:** POST handler for feed comments. Nearly identical to `comments/action.php` but outputs raw JSON result instead of redirecting (for AJAX consumption).

**API Endpoint Called:**
- `core.content.post` via `taoh_apicall_post()`

**Key Differences from `comments/action.php`:**
- **Line 49:** `echo $result; die;` -- returns raw API response instead of redirecting.
- No `redis_action` or `redis_store` parameters in the API call.
- Cache invalidation: `'cache' => array('remove' => array($_POST['conttype']."_comments_".$_POST['conttoken']))`

### `feeds/comments_details.php`

**Purpose:** Feed-specific comment display. Simpler than `comments/comments_details.php` -- shows flat comments in a scrollable div without accordion collapse or JSON comment parsing.

**API Endpoint Called:**
- `core.content.get` via `taoh_apicall_get()`

**Parameters:** `mod=core`, `token`, `conttoken`, `type=comment`, `conttype=$conttoken` (note: conttype reuses conttoken value), `ops=get`

**Key Differences from `comments/comments_details.php`:**
- **Line 12:** `'conttype' => $conttoken` -- uses conttoken as conttype (intentional for feed context or possible bug).
- Requires `taoh_user_is_logged_in()` to display anything.
- Simpler avatar handling (no `avatar_image` field check, uses `avatar` with default fallback).
- No JSON comment parsing -- always calls `taoh_title_desc_decode()`.
- Wrapped in scrollable div with id `comments{$conttoken}`.
- Comment count shown but hidden (`style="display:none;"`).

### `feeds/comments_post.php`

**Purpose:** Feed-specific comment input form. Single-line input (not textarea) designed for AJAX submission.

**Key Differences from `comments/comments_post.php`:**
- Falls back to `$_POST` if `$data` not set: `if(!isset($data) && isset($_POST)) $data = $_POST;`
- Uses `<input type="text">` instead of `<textarea>`.
- Submit button has class `save_post` with `data-id="$conttoken"` for external JS AJAX handler.
- No `<form action>` URL -- designed for AJAX submission handled by external JS.
- Shows user avatar image: `$data['avatar']`.
- Enter key is prevented from submitting (jQuery `keypress` handler on line 41-44).
- `commentresponseMessage` span includes conttoken for uniqueness: `id="commentresponseMessage{$conttoken}"`.

---

## 8. follow/ -- Follow System

### `follow/functions.php`

**Purpose:** Core follow/unfollow business logic. Defines three functions.

**Key Functions:**

| Function | API Call | Params | Auth |
|---|---|---|---|
| `get_follow($conttoken, $type)` | `core.content.get` | `ops=get`, `type=follow`, `mod=core` | `taoh_get_dummy_token(1)` |
| `do_follow()` | `core.content.get` | `ops=add`, `type=follow`, `mod=core` | `taoh_get_dummy_token()` |
| `un_follow()` | `core.content.get` | `ops=remove`, `type=follow`, `mod=core` | `taoh_get_dummy_token()` |

**Note:** All three use `core.content.get` (GET method), even for add/remove operations -- these are side-effecting GET calls.

**AJAX Handler Functions:**
- `do_follow()` -- reads `$_POST['conttoken']` and `$_POST['conttype']`, echoes raw API JSON response.
- `un_follow()` -- reads `$_POST['conttoken']` and `$_POST['conttype']`, echoes raw API JSON response.

### `follow/follow.php`

**Purpose:** Renders the Follow/Unfollow button with follower count.

**Includes:** `functions.php` at top.

**Expected `$data` keys:** `token` (the content token to follow), `type` (the content type).

**Rendered Output:** `<span>` button showing `(count) Follow` or `(count) Un Follow` based on `$followed` boolean state.

**Client-Side JS:**
- `doFollow()` -- `jQuery.post` to `taoh_site_ajax_url()` with `action: 'do_follow'`, `conttoken`, `conttype`. Updates `#followCount` with `$follow_count + 1` (client-side optimistic).
- `unFollow()` -- `jQuery.post` with `action: 'un_follow'`. Updates `#followCount` with `$follow_count - 1`.

**Key Edit Points:**
- **Lines 3-4:** Data extraction: `$contoken = $data['token']`, `$type = $data['type']`.
- **Lines 6-8:** Token selection: `TAOH_API_SECRET` for anon, `TAOH_API_TOKEN` for logged-in.
- **Lines 38-42:** Follow AJAX -- count update is client-side only (hardcoded value), no server-side re-fetch.

### `follow/ajax.php`

**Purpose:** AJAX entry point. Sets `Content-Type: application/json` header and includes `functions.php`.

**Note:** This file just bootstraps. The actual AJAX action routing (calling `do_follow()` or `un_follow()`) must happen upstream via the TAOH AJAX dispatcher that matches `$_POST['action']` to function names.

---

## 9. general/ -- General-Purpose Widgets

### `general/ads.php`

**Purpose:** Fetches ad JSON from CDN and renders a random or indexed ad.

**Key Function:** `taoh_ads_general($ads_count = 0)`

**Data Source:**
- `taoh_url_get_content(TAOH_CDN_PREFIX."/app/ads.php")` -- fetches raw ad JSON from CDN.

**Logic:** Replaces `[TAOH_CDN_PREFIX]`, `[TAOH_SITE_URL_ROOT]`, `[TAOH_HOME_URL]` placeholders in returned JSON string. Decodes to array. If `$ads_count` matches an array index, uses that ad; otherwise shuffles and picks first.

**Rendered Output:** Card with title, image (256px width), short description, CTA button with custom `color` and `background` from ad JSON.

**Key Edit Points:**
- **Line 9:** CDN URL: `TAOH_CDN_PREFIX."/app/ads.php"`
- **Lines 10-12:** Three placeholder replacements for dynamic URLs.
- **Lines 14-24:** Selection logic: specific index vs. random shuffle.
- **Line 45:** Button uses `$return['color']` and `$return['background']` from ad JSON.
- **Lines 54-96:** Large commented-out switch block for legacy ad rendering (dead code).

### `general/calendar.php`

**Purpose:** "Add to Calendar" dropdown widget for events. Generates Google Calendar and Outlook live calendar links.

**API Endpoints Called:** None (uses `$data` passed from caller).

**Expected `$data` keys:** `conttoken['title']`, `local_start_at`, `local_end_at`, `eventtoken`, `conttoken['full_location']`

**Rendered Output:** CSS-animated dropdown button ("Add to Calender" [sic]) with Google Calendar and Outlook Calendar link items.

**Key Edit Points:**
- **Line 79:** Google Calendar date format: `Ymd'T'Hi'00/'` + end date.
- **Lines 81-83:** Outlook date format: `Y-m-d'T'H:i:s`.
- **Line 89:** Event details text includes referral link: `TAOH_SITE_URL_ROOT.'/events/d/'.taoh_slugify($title).'-'.$response['eventtoken']`
- **Line 94:** Google Calendar URL with `action=TEMPLATE`.
- **Line 96:** Outlook URL via `outlook.live.com/owa` path.
- **Lines 104-109:** jQuery toggle with 5-second auto-close timeout.

### `general/copynshare.php`

**Purpose:** Copy-to-clipboard URL widget and social media sharing card. Renders share links for Facebook, Twitter, LinkedIn, Email.

**API Endpoints Called:** None (constructs URLs from PHP constants and server variables).

**Uses Constants:** `TAO_PAGE_IMAGE`, `TAO_PAGE_TITLE`, `TAO_PAGE_DESCRIPTION`

**Rendered Output:** Card with "Copy & Share Link" section (input field + Copy button) and "Share on Social Media" section with SVG icons for Facebook, Twitter, LinkedIn, Email.

**Client-Side JS:** `copyText()` -- uses `navigator.clipboard.writeText()`, shows/hides tooltip with jQuery class toggle.

**Key Edit Points:**
- **Lines 3-18:** Share URL construction for each platform from `$_SERVER['REQUEST_SCHEME']`, `HTTP_HOST`, `REQUEST_URI`.
- **Line 41:** Input field displays the current page URL.
- **Lines 52-63:** Social media icon links (SVG-based).

### `general/jobs_networking.php`

**Purpose:** Displays "Networking Rooms" links from a JSON config constant.

**Data Source:** `TAOH_WIDGET_ROOMS` constant (JSON string, decoded to associative array).

**Rendered Output:** Card listing room links with titles. Each link appends `?open_network=true` query parameter.

**Key Edit Points:**
- **Line 2:** `TAOH_WIDGET_ROOMS` must be a valid JSON constant defining rooms with `room_title`, `room_keyword`, `room_url`.
- **Line 21:** Country extraction: `array_pop(explode(', ', taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->full_location))`
- **Line 22:** Room key hash: `hash('crc32', $keyword.$country)`
- **Line 27:** Link pattern: `TAOH_SITE_URL_ROOT.$wid_room_url.$wid_room_keyword.'?open_network=true'`

### `general/leftmenu.php`

**Purpose:** Renders the left sidebar navigation menu with app links, flashcard modal, and learning section links.

**API Endpoints Called:** None.

**Uses:** `taoh_available_apps()` for dynamic app listing, `TAOH_SITE_CURRENT_APP_SLUG` and `TAOH_WERTUAL_SLUG` for active state, `TAOH_REDIRECT_URL` for learning page active states.

**Rendered Output:** Sticky sidebar (`position-sticky top-0`) with:
- Home link
- Dynamic app links from `taoh_available_apps()` loop
- Growth Reads link
- Flashcards link (opens Bootstrap modal)
- Obvious Baba link
- JusASKTheCoach link

**Flashcard Modal:** Contains 3 hardcoded flashcard categories with large SVG icons:
- Jobs (networking) -> `/learning/flashcard/networking/`
- Work (career-development) -> `/learning/flashcard/career-development/`
- Wellness (mindfulness) -> `/learning/flashcard/mindfulness/`

**Key Edit Points:**
- **Line 3:** Home active state: `TAOH_SITE_CURRENT_APP_SLUG == TAOH_WERTUAL_SLUG`
- **Lines 5-7:** Dynamic app loop: `foreach (taoh_available_apps() as $app)`
- **Line 35:** `TAOH_PAGE_URL` constant for home URL override.
- **Lines 40-116:** Flashcard modal content with SVG illustrations.

### `general/new_ads.php`

**Purpose:** Fetches user-type-targeted ads from CDN and renders a card.

**Data Source:** `taoh_url_get_content(TAOH_CDN_PREFIX."/app/ads.php?type=".$user_type)`

**Rendered Output:** Card with title, image, subtitle, CTA button. Only renders if `$return['title']` is non-empty.

**Key Edit Points:**
- **Line 2:** User type from `taoh_user_type()`.
- **Line 3:** CDN URL with `?type=` parameter for targeting.
- Output fields: `title`, `image`, `subtitle`, `link_url`, `button_text`, `color`, `background`.

### `general/recent_jobs.php`

**Purpose:** Displays up to 3 recent random jobs. Has "new" widget variant with modernized design.

**API Endpoint Called:**
- `content.get.randomjobs` via `taoh_apicall_get()`

**Parameters:** `mod=jobs`, `secret=TAOH_API_SECRET`, `local=TAOH_JOBS_GET_LOCAL`, `cache_name='jobs_recent_'.TAOH_API_SECRET.'_'.taoh_get_dummy_token()`, `cache_time=30`

**Rendered Output:** Gated by `$widget_type == 'new' && TAOH_JOBS_ENABLE`. Shows:
- Header "Recent Jobs" with optional "View All Jobs" button
- Up to 3 job items with title, location, optional Scout badge
- "Post a Job" button for employer users

**Key Edit Points:**
- **Line 16:** `array_slice($content['list'], 0, 3)` -- max 3 jobs.
- **Line 20:** Double gate: `$widget_type == 'new'` AND `TAOH_JOBS_ENABLE`.
- **Line 27:** "View All Jobs" hidden on jobs page: `!stristr($_SERVER['REQUEST_URI'], TAOH_PLUGIN_PATH_NAME.'/jobs')`
- **Lines 48-55:** Scout badge: `$value['enable_scout_job'] == 'on'` shows scout icon with tooltip.
- **Line 67:** Employer check: `$_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']->type == 'employer'`
- **Line 40:** Job detail URL: `TAOH_SITE_URL_ROOT.'/jobs/d/'.slugify2($wid_job_title)."-".$wid_job_conttoken`

### `general/recent_jobs_old.php`

**Purpose:** Older version of `recent_jobs.php` with different card layout (bordered cards with `12px border-radius`). Same API call and logic.

### `general/short_profile.php`

**Purpose:** Compact user profile card widget showing avatar, name, location, and "View full profile" link.

**Data Source:** `taoh_get_user_info($ptoken, 'info')` -- fetches user data from API.

**Rendered Output:** Card with avatar image (40px), user's first name, location with map icon, link to `/profile/{ptoken}`.

**Key Edit Points:**
- **Line 2:** `$ptoken` from caller scope (set by `taoh_user_profile_short($ptoken)`).
- **Line 3:** API call: `taoh_get_user_info($ptoken, 'info')`
- **Lines 8-16:** Avatar resolution chain: `avatar_image` field > `TAOH_OPS_PREFIX.'/avatar/PNG/128/'.$avatar.'.png'` > `TAOH_OPS_PREFIX.'/avatar/PNG/128/avatar_def.png'`.
- **Line 18:** Profile URL: `TAOH_SITE_URL_ROOT.'/profile/'.$ptoken`

### `general/stats.php`

**Purpose:** Shows site statistics ("Hires In Numbers") for non-logged-in users only.

**Data Source:** `json_decode(taoh_url_get_content(TAOH_SITE_STATS))` -- fetches stats JSON.

**Rendered Output:** Grid of stat numbers with labels, colored with rotating `text-color-N` classes. Hidden on mobile (`mob-hide` class).

**Key Edit Points:**
- **Line 2:** Gate: `!taoh_user_is_logged_in()` -- hidden for authenticated users.
- **Line 3:** `TAOH_SITE_STATS` constant for stats endpoint.
- **Line 4:** Random color offset: `$counter = rand(1,5)`

### `general/tao.php`

**Purpose:** "TAO Tips & Tricks" widget showing career development tips. Currently **DISABLED** in `widget_functions.php` (lines 87-89 commented out).

**API Endpoint Called:**
- Logged in: `tao.get` with `mod=tao`, `token=taoh_get_dummy_token()`
- Anonymous: `app/tao/tao.php` (CDN endpoint, no params)

**Rendered Output:** Colored card (random background from `bg-gray`, `bg-light`, `bg-3`) with tip links showing title, URL, and app attribution.

### `general/type_widget.php`

**Purpose:** Instructional accordion widget showing how-to steps for employers vs. job seekers.

**Logic:** If `$data == 'employer'`, shows "Post jobs and find candidates" (3-step accordion). Otherwise shows "Search for jobs and apply" (3-step accordion).

**Key Edit Points:**
- **Line 3:** Branch condition: `$data == 'employer'`
- Employer steps: Post open roles -> Engage with candidates -> Select best candidates
- Seeker steps: Search for jobs -> Chat with recruiters -> Apply for jobs
- Seeker version has `mob-hide` class (hidden on mobile).

### `general/video.php`

**Purpose:** YouTube video embed widget with Fancybox 3.5.7 lightbox player.

**External Dependencies:** Fancybox 3.5.7 CSS + JS from cdnjs.cloudflare.com.

**Key Function Used:** `taoh_get_youtubeId($data)` -- extracts YouTube ID from URL.

**Rendered Output:** Video thumbnail image (from `img.youtube.com/vi/{id}/maxresdefault.jpg`) with SVG play button overlay. Clicking opens Fancybox lightbox with embedded YouTube player at `https://www.youtube.com/embed/{id}?rel=0`.

**Key Edit Points:**
- **Line 4:** `taoh_get_youtubeId($data)` helper extracts ID.
- **Line 13:** Thumbnail URL: `https://img.youtube.com/vi/{id}/maxresdefault.jpg`
- **Line 16:** Embed URL: `https://www.youtube.com/embed/{id}?rel=0`

### `general/wemet.php`

**Purpose:** "Your Video Chat Room" widget with Google Meet, Zoom, and Microsoft Teams options.

**API Endpoints Called:** None.

**Rendered Output:** Card with dropdown menu to switch between three video platforms. Each platform has step-by-step numbered instructions and a "Chat Now" button:
- Google Meet: `https://meet.google.com/new`
- Zoom: `https://zoom.us/join`
- Teams: `https://www.microsoft.com/en-in/microsoft-teams/join-a-meeting`

**Client-Side JS:**
- `loadChatType(type, param)` -- hides all `.chat_section` divs, shows selected one, updates active state.
- `CopyToClipboard(id)` -- clipboard utility using `document.execCommand('copy')` (not currently used in template).

**Key Edit Points:**
- **Line 9:** Hardcoded `$roomkey = 'b91267c5'` (`TAOH_CLUBKEY` commented out).
- **Line 4:** `if(1)` -- always renders (login gate commented out).

### `general/wordSlider.php`

**Purpose:** Pure CSS word animation slider. No PHP logic. CSS `@keyframes animate` with `content:` property cycling through "Recruiters", "Seekers", "Professionals", "Communities" over 10 seconds.

---

## 10. invite/ -- Invitation System

### `invite/invite_friends.php`

**Purpose:** Full invite-a-friend widget with social sharing icons, referral link copy-to-clipboard, and email invitation modal form.

**Auth Gate:** `taoh_user_is_logged_in()` in `widget_functions.php` wrapper.

**AJAX Action:** `taoh_invite_friend` via jQuery.post to `taoh_site_ajax_url()`

**AJAX Parameters:**
- `taoh_action` = `'taoh_invite_friend'`
- `first_name`, `last_name`, `email`, `comment`
- `from_link` = `window.location.href`
- `network_title` = `$title` (from caller)
- `app_name` = `$app` (from caller)
- `referral_link` = `TAOH_INVITE_REFERRAL_URL`

**Rendered Output:**
- Card with invite image (100px)
- Social sharing icons: email (modal), Facebook, Twitter/X, LinkedIn
- Referral URL copy input with Copy button
- Bootstrap modal with invitation form (first name, last name, email, message)

**Key Edit Points:**
- **Line 13:** Defines `TAOH_INVITE_REFERRAL_URL` constant from `$data` or `$_SERVER`.
- **Line 14:** `$referral_code['key']` for referral code tracking.
- **Line 45:** Displayed referral URL: `TAOH_INVITE_REFERRAL_URL."/".$referral_code`
- **Lines 123-140:** `chk_validations(id)` -- jQuery validation that adds `error_class` to empty fields.
- **Lines 142-145:** Email validation regex: `/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/`
- **Lines 151-200:** `invitationSubmit()` -- validates, disables button, posts via AJAX, shows success/error in `#error_msg`.
- **Lines 202-216:** `copyText()` -- `navigator.clipboard.writeText()` with success animation.

---

## 11. likes/ -- Likes System

### `likes/ajax.php`

**Purpose:** Defines the `taoh_like()` function for server-side AJAX like handling.

**API Endpoint Called:**
- `core.content.get` with `ops=like`, `type=likes`

**Parameters:** `mod=core`, `token=taoh_get_dummy_token(1)`, `ops=like`, `type=likes`, `conntoken=$_POST['conttoken']`, `conttype=$_POST['type']`

**Known Bugs:**
- **Line 11:** Parameter key `conntoken` -- likely typo, should be `conttoken`.
- **Line 21:** `print_r($result)` references undefined `$result` -- the API response is stored in `$req`. This function will produce a PHP notice and output nothing useful.

### `likes/likes.php`

**Purpose:** Renders the like button with view count and like count. Shows eye icon + views, heart icon (red if liked, gray if not) + likes count.

**API Endpoint Called:**
- `core.content.get` with `ops=get`, `type=stats`

**Parameters:** `mod=core`, `token=taoh_get_dummy_token(1)`, `ops=get`, `type=stats`, `conntoken=$conttoken` (same typo), `conttype=$type`

**Expected `$data` keys:** `conttoken`, `type`

**Rendered Output:** Inline icons -- eye icon + view count, heart icon + like count. Heart is clickable (gray) if not yet liked, solid red if liked.

**AJAX (client-side):**
- `doLike()` -- `jQuery.post` to `taoh_site_ajax_url()` with `taoh_action: 'taoh_like'`, `conttoken`, `type`. Updates `#likeCount` to `$likes + 1` and toggles heart color (client-side optimistic, no rollback).

**Key Edit Points:**
- **Line 16:** Same `conntoken` typo as ajax.php.
- **Line 50:** Like count update is hardcoded client-side: `$likes + 1`.

---

## 12. reads/ -- Reading/Blog Content Widgets

### `reads/readables.php`

**Purpose:** Displays recent blog/reads articles from a random category, with "new" and legacy layout variants.

**API Endpoint Called:**
- `reads.get.reads` via `taoh_apicall_get()`

**Parameters:** `q=''`, `mod=reads`, `ctype` (secret or token based on login), `code`, `type=category`, `limit=4`, `local=TAOH_READS_GET_LOCAL`

**Rendered Output:** Card listing up to 4 articles with titles, author names, and links. Category name shown in header.

**Key Edit Points:**
- **Line 24:** `limit=4` article count.
- **Line 37:** Category/slug extracted via `###` delimiter: `explode('###', $readables->value ?? '', 2)`
- **Line 39:** Widget type branching: `$widget_type == 'new'` for flat layout vs. card layout.
- **Line 54:** New layout article URL: `TAOH_READS_URL."/blog/".slugify2($value1->title)."-".$value1->conttoken`
- **Line 83:** Legacy layout uses `$value1->link` directly.

### `reads/categories.php`

**Purpose:** Sidebar widget showing top 5 blog categories.

**API Endpoint Called:**
- `reads.get.reads` with `type=catlist`, `qty=5`

**Parameters:** `mod=reads`, `ctype` (secret/token), `code`, `type=catlist`, `qty=5`

**Rendered Output:** Card listing category names as links to `TAOH_READS_URL."/search?q=".urlencode($value)."&type=category"`.

### `reads/search.php`

**Purpose:** Blog search form widget with optional "+ Post" button for content creators.

**Rendered Output:** Search form posting to `TAOH_READS_URL."/search"` via GET method. Shows "+ Post" button when `$_GET['creator']` is set, linking to `TAOH_READS_URL."/post/?post=".date('Ymd')`.

**Key Edit Points:**
- **Lines 19-23:** Creator gate: `$_GET['creator']` parameter presence enables Post button.
- **Line 28:** Form action: `TAOH_READS_URL."/search"`

### `reads/newsletter_search.php`

**Purpose:** Newsletter-specific search form widget. Simpler than `reads/search.php`.

**Form Action:** `TAOH_NEWSLETTER_URL."/search"` via GET method.

**Rendered Output:** Card with "SEARCH NEWSLETTER" title and search input.

### `reads/obviousbaba.php`

**Purpose:** "Obvious Baba [#funlessons]" widget showing a random work/life quote.

**API Endpoint Called:**
- `/api/worklessons` via `taoh_apicall_get()` with `$prefix=TAOH_OBVIOUS_PREFIX`

**Parameters:** Empty array (no params).

**Rendered Output:** Card with Obvious Baba character image (250px from `TAOH_OBVIOUS_PREFIX."/images/obviousbaba.png"`), quote text, attribution link.

### `reads/jusask.php`

**Purpose:** "#JusASK, The Career Coach" widget promoting the AI career coaching feature.

**API Endpoints Called:** None (static promotional widget).

**Rendered Output:** Card with JusASK image and promotional text. Has "new" (flat, no card wrapper) and legacy (card wrapper) layout variants based on `$widget_type`.

**Key Edit Points:**
- **Line 3:** URL: `TAOH_SITE_URL_ROOT."/learning/jusask"`
- **Line 17:** Image: `TAOH_CDN_PREFIX."/app/jusask/images/jusask_sq_256.png"`

---

## 13. share/ -- Social Sharing Widgets

### `share/share.php`

**Purpose:** Full social share widget with Facebook SDK integration, share-for-discount flow, copy-to-clipboard, and multi-platform sharing (Facebook, Twitter/X, LinkedIn, Email, Copy URL).

**Expected `$data` keys:** `share_data` (URL), `conttype`, `ptoken`, `conttoken`, `widget_id` (optional, defaults to `'widget_id'`)

**Facebook SDK:** App ID `1271794846576386`, SDK version `v18.0`.

**Client-Side JS Functions:**
- `sharOnFacebook(url)` -- Uses `FB.ui()` share dialog with `method: 'share'`, `quote` for description.
- `taoh_events_enable_share_discount(share_url, platform)` -- Opens share popup window (600x600), polls `popup.closed` at 500ms interval, sets `localStorage.setItem('event_'+event_id+'_shared', true)` when closed, shows discount alert.
- Share click handler (`.share_counts`) -- calls `save_metrics()` for analytics, determines platform from `data-click` attribute, checks `currentShareLink` JS var and `#social_from` input for dynamic URL override.
- Copy handler (`.copys-btns`) -- uses `document.execCommand("copy")` fallback, shows success animation.

**Key Edit Points:**
- **Line 6:** `$widget_id` default: `$data['widget_id'] ?? 'widget_id'`
- **Line 30:** Container: `data-widget-id` attribute for targeting.
- **Line 61:** Facebook App ID: `'1271794846576386'`
- **Lines 102-120:** Copy handler uses delegated `$(document).on('click','.copys-btns')` and checks `$('#social_from').val() == '1'` for feed-context dynamic URLs.
- **Lines 138-221:** `taoh_events_enable_share_discount()` -- platform switch for FB/LI/TW URL construction, popup polling for discount unlock.

### `share/social_share.php`

**Purpose:** Simpler social share variant without copy button. Same Facebook SDK integration and discount flow.

**Expected `$data` keys:** `share_data`, `conttype`, `ptoken`, `conttoken` (no `widget_id`).

**Key Differences from `share/share.php`:**
- No `widget_id` or `data-widget-id` attribute.
- No copy button in the social icon row.
- `$share_text_js` is `json_encode($desc)` (double-encoded string) vs. raw `$desc` in `share.php`.
- Direct `.click()` binding instead of delegated `$(document).on('click')` for copy handler.
- No `#social_from` dynamic URL override.

**Shared Elements:** Both files load Facebook SDK, define `sharOnFacebook()`, `taoh_events_enable_share_discount()`, and use `save_metrics()` for analytics.

---

## 14. API Endpoint Summary

| Endpoint | Method | Used In | Purpose |
|---|---|---|---|
| `ads.get` | GET | `ads/common_ads.php` | Fetch targeted ads by app/type |
| `api/anony.asks.asks.search.get` | GET | `apps/asks.php` | Search asks/Q&A items |
| `jobs.get` | GET | `apps/jobs.php`, `free_monthly_jobs.php`, `free_promotional_jobs.php` | Search job listings |
| `core.content.get` | GET | `comments/comments_details.php`, `feeds/comments_details.php`, `follow/functions.php` (3 functions), `likes/likes.php`, `likes/ajax.php` | Comments, follow, likes, stats |
| `core.content.post` | POST | `comments/action.php`, `feeds/action.php` | Submit new comments |
| `events.get` | GET | `events/recent_events.php`, `recent_multiple_events.php`, `recent_events_full_display.php` | List events |
| `content.get.randomjobs` | GET | `general/recent_jobs.php`, `recent_jobs_old.php` | Random recent jobs |
| `reads.get.reads` | GET | `reads/readables.php`, `reads/categories.php` | Blog articles and categories |
| `tao.get` | GET | `general/tao.php` | TAO tips (currently disabled) |
| `/api/worklessons` | GET | `reads/obviousbaba.php` | Random work quote |
| CDN: `/app/ads.php` | GET | `general/ads.php`, `general/new_ads.php` | Ad JSON from CDN |
| CDN: `TAOH_SITE_STATS` | GET | `general/stats.php` | Site statistics |
| `taoh_get_user_info()` | internal | `general/short_profile.php` | User profile data |

---

## 15. AJAX Action Summary

| JS Action Key | Server Function | Defined In | Called From |
|---|---|---|---|
| `action: 'do_follow'` | `do_follow()` | `follow/functions.php` | `follow/follow.php` JS |
| `action: 'un_follow'` | `un_follow()` | `follow/functions.php` | `follow/follow.php` JS |
| `taoh_action: 'taoh_like'` | `taoh_like()` | `likes/ajax.php` | `likes/likes.php` JS |
| `taoh_action: 'taoh_invite_friend'` | (external handler) | N/A | `invite/invite_friends.php` JS |

**Note:** Follow uses `action` key; Likes and Invite use `taoh_action` key. This inconsistency indicates they are dispatched by different AJAX routers.

---

## 16. Constants and Global Variables

### PHP Constants Required

| Constant | Purpose | Used By |
|---|---|---|
| `TAOH_SITE_URL_ROOT` | Base site URL | Nearly all files |
| `TAOH_CDN_PREFIX` | CDN base URL | ads, new_ads, jusask, obviousbaba |
| `TAOH_API_SECRET` | Anonymous API key | follow, likes, events, jobs, reads, stats |
| `TAOH_API_TOKEN` | Authenticated user API token | follow, likes |
| `TAOH_API_DUMMY_SECRET` | Fallback secret for events | events widgets |
| `TAOH_API_PREFIX` | API base URL override | events widgets |
| `TAOH_OPS_PREFIX` | Avatar/ops CDN prefix | comments, feeds, short_profile |
| `TAOH_JOBS_ENABLE` | Jobs feature toggle | recent_jobs, free_promotional_jobs |
| `TAOH_JOBS_GET_LOCAL` | Jobs local/remote flag | All job widgets |
| `TAOH_EVENTS_GET_LOCAL` | Events local/remote flag | All event widgets |
| `TAOH_READS_GET_LOCAL` | Reads local/remote flag | readables.php |
| `TAOH_READS_URL` | Reads section base URL | reads widgets, leftmenu |
| `TAOH_NEWSLETTER_URL` | Newsletter base URL | newsletter_search.php |
| `TAOH_OBVIOUS_PREFIX` | ObviousBaba API base | obviousbaba.php |
| `TAOH_OBVIOUS_URL` | ObviousBaba site URL | obviousbaba.php |
| `TAOH_SITE_STATS` | Stats API URL | stats.php |
| `TAOH_WIDGET_ROOMS` | JSON rooms config string | jobs_networking.php |
| `TAOH_ROOT_PATH_HASH` | Session namespace key | Multiple files |
| `TAOH_SITE_CURRENT_APP_SLUG` | Current app identifier | leftmenu.php |
| `TAOH_WERTUAL_SLUG` | Home/default app slug | leftmenu.php |
| `TAOH_REDIRECT_URL` | Current page URL | leftmenu.php |
| `TAOH_PLUGIN_PATH_NAME` | Plugin path segment | recent_jobs.php |
| `TAOH_HOME_URL` | Home URL | ads.php placeholder |
| `TAO_PAGE_TITLE` | Open Graph title | copynshare, share widgets |
| `TAO_PAGE_DESCRIPTION` | Open Graph description | copynshare, share widgets |
| `TAO_PAGE_IMAGE` | Open Graph image | copynshare, share widgets |

### Key Helper Functions Used

| Function | Purpose |
|---|---|
| `taoh_apicall_get($call, $vals, $prefix?, $flag?)` | HTTP GET to internal API |
| `taoh_apicall_post($call, $vals)` | HTTP POST to internal API |
| `taoh_url_get_content($url)` | Raw HTTP GET to any URL |
| `taoh_get_dummy_token($flag?)` | Get API auth token |
| `taoh_user_is_logged_in()` | Boolean auth check |
| `taoh_user_type()` | Returns user type string |
| `taoh_user_timezone()` | Returns user timezone |
| `taoh_get_user_info($ptoken, $type)` | Fetch user profile data |
| `taoh_site_ajax_url()` | Get AJAX endpoint URL |
| `taoh_set_error_message($msg)` | Set flash error message |
| `taoh_set_success_message($msg)` | Set flash success message |
| `taoh_title_desc_encode($str)` | Encode text for API storage |
| `taoh_title_desc_decode($str)` | Decode text for display |
| `taoh_exit()` | Safe script termination |
| `taoh_available_apps()` | Get list of available apps |
| `slugify2($str)` | Generate URL-safe slug |
| `taoh_slugify($str)` | Alternative slug function |
| `taoh_get_youtubeId($url)` | Extract YouTube video ID |
| `event_time_display($utc, $locality, $tz, ...)` | Timezone-aware event time formatting |
| `taoh_fullyear_convert($date, $convert)` | Date formatting for comments |
| `taoh_session_get($key)` | Session data access |

### Key Client-Side JS Globals

| Variable/Function | Purpose | Set By |
|---|---|---|
| `currentShareLink` | Override share URL dynamically | External JS |
| `event_id` | Share discount localStorage key | External JS |
| `IntaoDB` | IndexedDB wrapper object | External JS library |
| `objStores` | IndexedDB store definitions | External JS library |
| `eventLiveState(start, end, locality, tz)` | Determine event live/before/after | External JS |
| `isValidTimezone(tz)` | Timezone validation | External JS |
| `save_metrics(type, action, token)` | Analytics tracking | External JS |

---

## 17. Security Considerations and Known Bugs

### XSS Risks
- `comments/comments_details.php`: Uses `htmlspecialchars()` on names (good), but `taoh_title_desc_decode()` on comment body may output raw HTML.
- `feeds/comments_details.php`: Unescaped decode output -- XSS risk on comment content.
- `invite/invite_friends.php`: `$title` variable injected into JS string without escaping (line 167).
- `general/copynshare.php`: `$share_link` from `$_SERVER` in input `value` attribute -- reflected XSS risk.

### CSRF
- No CSRF tokens on comment POST forms (`comments/comments_post.php`, `feeds/comments_post.php`).
- No CSRF tokens on follow/like/invite AJAX calls.

### Open Redirect
- `comments/action.php` line 6, 63, 65: `header("Location: ".$_POST['redirect'])` -- unvalidated redirect URL from user input.

### Known Bugs
1. **`likes/ajax.php` line 21:** `print_r($result)` uses undefined variable -- should be `$req`.
2. **`likes/ajax.php` line 11 and `likes/likes.php` line 16:** `conntoken` typo -- should be `conttoken`.
3. **`apps/jobs.php`, `free_monthly_jobs.php`:** `json_decode($data, true)` returns associative array but code accesses `$fetch_arr->result` with object syntax.
4. **`feeds/comments_details.php` line 12:** Uses `$conttoken` value as both `conttoken` and `conttype` parameters.
5. **Client-side counts:** Like and follow count updates are optimistic (hardcoded `+1`/`-1`) with no server re-validation or rollback on failure.
6. **Duplicate SDK:** Both `share/share.php` and `share/social_share.php` load Facebook SDK -- if both render on the same page, SDK loads twice.

### Disabled Features
- `taoh_tao_widget()` -- TAO Tips (commented out in `widget_functions.php` line 87-89).
- `taoh_invite_widget()` -- Old invite system (commented out in `widget_functions.php` line 116).

---

## 18. Integration Points

| Integration | Direction | Mechanism |
|---|---|---|
| Page templates | Pages -> Widgets | `taoh_*_widget()` function calls |
| AJAX router | Widgets -> Core | `taoh_site_ajax_url()` POST endpoint |
| Comment route | Form POST -> `/actions/comments` | `comments/action.php` handler |
| API layer | Widgets -> Backend | `taoh_apicall_get()` / `taoh_apicall_post()` |
| CDN | Widgets -> Static | Avatars (`TAOH_OPS_PREFIX`), ads, images |
| IndexedDB | Client JS -> Browser | Sponsor data, share state |
| PHP Session | Widgets -> Session | `$_SESSION[TAOH_ROOT_PATH_HASH]` for user info |
| External: Facebook | Widgets -> FB SDK | Share dialog, App ID `1271794846576386` |
| External: Google | Widgets -> Google | Calendar API, Meet links |
| External: Microsoft | Widgets -> MS | Outlook Calendar, Teams links |
| External: Zoom | Widgets -> Zoom | Join meeting links |
| Analytics | Widgets -> Tracking | `data-metrics` attributes, `save_metrics()` JS, `click_action`/`click_metrics` classes |
| localStorage | Widgets -> Browser | Share discount tracking per event |

---

## 19. Key Edit Points Quick Reference

| Task | File(s) | Line(s) |
|---|---|---|
| Add a new widget | Create partial file + `widget_functions.php` | End of file |
| Change comment form action URL | `comments/comments_post.php` | 9 |
| Change comment API params | `comments/action.php` | 37-49 |
| Fix `conntoken` typo in likes | `likes/ajax.php` line 11, `likes/likes.php` line 16 | 11, 16 |
| Fix undefined `$result` in likes | `likes/ajax.php` | 21 (change to `$req`) |
| Change recent jobs count | `general/recent_jobs.php` | 16 |
| Change event widget count | `events/recent_events.php` L3, `recent_multiple_events.php` L3, `recent_events_full_display.php` L4 | 3-4 |
| Change event cache TTL | `events/*.php` | `cache_time` param |
| Modify share platforms | `share/share.php` or `share/social_share.php` | Icon links section |
| Change Facebook App ID | `share/share.php` L61, `share/social_share.php` L50 | 50-61 |
| Toggle jobs feature | Set `TAOH_JOBS_ENABLE` constant | Config |
| Re-enable TAO Tips widget | Uncomment in `widget_functions.php` | 87-88 |
| Change ad selection logic | `general/ads.php` -> `taoh_ads_general()` | 8-24 |
| Modify invite form fields | `invite/invite_friends.php` | 71-99 (form), 151-200 (JS submit) |
| Change flashcard categories | `general/leftmenu.php` | 38-116 (modal section) |
| Change video chat platforms | `general/wemet.php` | 29-64 (platform sections) |
| Add new event type icon | `events/recent_events.php`, `recent_multiple_events.php`, `recent_events_full_display.php` | Type icon switch blocks |
| Change avatar fallback chain | `comments/comments_details.php` L65, `feeds/comments_details.php` L52-59, `general/short_profile.php` L8-16 | Various |
