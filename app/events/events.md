# Events App — Complete Architecture Reference

> **Generated**: 2026-01-30
> **Source**: `/wpl/club/app/events/`
> **Total files**: 70+ PHP files, 37,128 lines of PHP code

---

## Table of Contents

1. [Directory Structure & File Index](#1-directory-structure--file-index)
2. [Routing (main.php)](#2-routing-mainphp)
3. [Constants](#3-constants)
4. [Core Functions (functions.php)](#4-core-functions-functionsphp)
5. [AJAX Endpoints (ajax.php)](#5-ajax-endpoints-ajaxphp)
6. [Action Handlers (actions/main.php)](#6-action-handlers-actionsmainphp)
7. [Adapter Layer (NtwAdapterEvents)](#7-adapter-layer-ntwadapterevents)
8. [Networking Adapter (adapter.php)](#8-networking-adapter-adapterphp)
9. [API Endpoints Reference](#9-api-endpoints-reference)
10. [Authentication & Authorization](#10-authentication--authorization)
11. [Feature Modules — File-by-File](#11-feature-modules--file-by-file)
12. [Frontend Patterns](#12-frontend-patterns)
13. [Caching Strategy](#13-caching-strategy)
14. [External Integrations](#14-external-integrations)
15. [Data Flow Diagrams](#15-data-flow-diagrams)
16. [Key Data Structures](#16-key-data-structures)

---

## 1. Directory Structure & File Index

```
events/
├── main.php                          (323 lines)  — Router/dispatcher
├── functions.php                     (819 lines)  — Core utility functions
├── ajax.php                          (1446 lines) — AJAX endpoint handler
├── adapter.php                       (125 lines)  — Networking room adapter (standard)
├── adapterFirebase.php               (~125 lines) — Networking room adapter (Firebase)
├── NtwAdapterEvents.php              (1127 lines) — Networking adapter class
├── NtwAdapterEventsFirebase.php      (1160 lines) — Firebase networking adapter class
├── index.php                         (1 line)     — "Silence is golden"
│
├── # MAIN DISPLAY PAGES
├── events.php                        (1850 lines) — Main events listing (logged in)
├── events_new.php                    (1557 lines) — Newer events listing UI
├── visitor.php                       (1125 lines) — Events listing (not logged in)
├── events_detail.php                 (1960 lines) — Single event detail page
├── events_landing.php                (~600 lines) — Alt landing with tabs (upcoming/registered/saved)
├── all_events.php                    (975 lines)  — All events list view
│
├── # EVENT LOBBY
├── events_lobby_hall.php             (1386 lines) — Event lobby with tabbed sections
├── chat.php                          (3344 lines) — Main event chat/networking interface
├── chat-modal.php                    (~400 lines) — Chat modals (contact host, co-attendees, ticket)
├── chat_session.php                  (~300 lines) — Speaker session chat context
├── chat_exhibitor.php                (~300 lines) — Exhibitor booth chat context
│
├── # AGENDA
├── events_agenda.php                 (1236 lines) — Agenda/schedule display
├── events_agenda_default_banner.php  (20 lines)   — "No agenda" placeholder banner
├── events_agenda_default_list.php    (169 lines)  — Blurred placeholder agenda items
│
├── # SPEAKERS
├── events_speakers.php               (~400 lines) — Speaker listing within lobby
├── events_speaker_default_list.php   (~200 lines) — Blurred placeholder speaker items
├── events_speaker_detail.php         (~400 lines) — Speaker detail (AJAX partial)
├── events_speaker_page.php           (~500 lines) — Full-page speaker detail
├── events_speakder_details.php       (~400 lines) — Speaker detail page (typo in filename)
├── events_session_form.php           (~500 lines) — Speaker/session creation form
│
├── # EXHIBITORS
├── events_exhibitors.php             (1248 lines) — Exhibitor listing within lobby
├── events_exhibitor_default_list.php (~200 lines) — Blurred placeholder exhibitor items
├── events_exhibitor_detail_new.php   (1305 lines) — Exhibitor detail page
├── events_exhibitor_form_new.php     (~500 lines) — Exhibitor slot registration form
│
├── # SPONSORS
├── events_sponsor_detail.php         (821 lines)  — Sponsor detail page
├── sponsor_details_modal.php         (~200 lines) — Sponsor quick-view modal
│
├── # HALLS
├── events_hall.php                   (~300 lines) — Hall listing (speakers timeline)
├── events_hall_exhibitors.php        (~300 lines) — Hall listing (exhibitors)
├── events_hall_listing_page.php      (930 lines)  — Hall listing page
├── events_hall_rsvp.php              (~300 lines) — Hall RSVP view
│
├── # RSVP / ATTENDEES
├── events_rsvp_default_list.php      (~200 lines) — Blurred attendee cards
├── events_rsvp_directory.php         (877 lines)  — Attendee search/filter directory
│
├── # CONVERSATIONS
├── events_conversations.php          (1297 lines) — Multi-tab conversations (group/DM/speed networking)
│
├── # TABLES (Discussion)
├── tables.php                        (~200 lines) — Tables redirect/setup
├── events_tables.php                 (~300 lines) — Discussion tables within event
│
├── # ROOMS
├── events_rooms.php                  (~50 lines)  — Exhibitor room links
├── room_update.php                   (~42 lines)  — Delete/recreate room patterns
│
├── # TAO AI
├── events_tao_ai.php                 (~400 lines) — TAO AI event recommendations
├── events_tao_ai_new.php             (1024 lines) — TAO AI newer version
├── events_tao_search.php             (~200 lines) — TAO AI search
├── tao_connect_html.php              (~200 lines) — TAO connection HTML
│
├── # EXPORTS
├── export_rsvp.php                   (~300 lines) — Export RSVP to Excel
├── export_raffle_entries.php         (~200 lines) — Export raffle entries to Excel
├── export_raffle_feedback.php        (~200 lines) — Export raffle feedback to Excel
│
├── # OTHER
├── events_popup.php                  (~200 lines) — Continue Purchase / Share modals
├── events_footer.php                 (~300 lines) — Event footer with async data loading
├── events_mobile.php                 (1 line)     — Empty placeholder
├── event_health_check.php            (~200 lines) — CDN health check overlay
├── event_status.php                  (71 lines)   — "No Room Allocated" status page
├── event_upgrade_modal.php           (~300 lines) — Ticket upgrade modal
├── event_video_modal.php             (76 lines)   — Organizer video message modal
├── swag_wall.php                     (~300 lines) — Rewards/raffle/swag page
├── new_desc_page.php                 (~200 lines) — Event description landing page
├── search.php                        (~150 lines) — Search form
├── search_new.php                    (~150 lines) — Modern search UI
├── next.php                          (~50 lines)  — Redirect to next event
├── club.php                          (~400 lines) — Legacy networking (replaced by adapter.php)
│
├── actions/
│   └── main.php                      (88 lines)   — POST form handlers (RSVP create/delete)
│
├── includes/
│   └── error.php                     (128 lines)  — Custom error page with auto-refresh
│
└── old/                              — Legacy/backup files
    ├── adapter_1.php                 — Old adapter
    ├── chat-opt.php                  — Optimized chat (backup)
    ├── chat_old.php                  — Previous chat (175KB)
    ├── chat.php.bak                  — Chat backup (210KB)
    ├── events_common_js.php          — Shared JS (484 bytes)
    ├── events_detail_opt.php         — Optimized event detail (42KB)
    └── events_lobby_dashboard.php    — Lobby dashboard (25KB)
```

---

## 2. Routing (main.php)

**Entry guard**: Redirects to site root if `TAOH_EVENTS_ENABLE` is falsy.

**URL parsing**:
```
/events/{action}/{segment2}/{segment3}/{segment4}/{segment5}/{segment6}

$current_app = taoh_parse_url(0)   // 'events' (or forced if EVENT_DEMO_SITE)
$action      = taoh_parse_url(1)   // route key
$goto        = taoh_parse_url(2) . '/' . taoh_parse_url(3)  // combined slug
$id          = '/' . taoh_parse_url(4)
$param       = '/' . taoh_parse_url(5) . '/' . taoh_parse_url(6)
```

**Special**: If `$goto == 'stlo'`, it's cleared to empty string.

**Includes before routing**: `functions.php`, `core/form_fields.php`

### Route Table

| URL action | Auth Required | File Loaded | Notes |
|---|---|---|---|
| `rsvp` | Yes (login redirect) | `rsvp.php` | RSVP management |
| `add_rsvp` | Yes | Redirect to `/fwd/ss/{hash}/log/1/u/loc/events/rsvp/{eventtoken}/{title}/{returnUrl}` | |
| `edit_rsvp` | Yes | Same redirect as add_rsvp | |
| `upgrade_rsvp` | Yes | Same redirect but with `rsvp-upgrade` type | |
| `confirmation` | Yes | `rsvp_confirmation.php` | Post-RSVP confirmation |
| `beam` | Yes | `orgchat.php` | Organizer chat |
| `eventsticketview` | No | `events_ticket_view.php` | |
| `eventshtmlnewtemplate` | No | `events_html_new_template.php` | |
| `eventshall` | No | `events_hall.php` | |
| `eventshallexhibitors` | No | `events_hall_exhibitors.php` | |
| `eventshallrsvp` | No | `events_hall_rsvp.php` | |
| `newdescpage` | No | `new_desc_page.php` | |
| `exhibitors` | No | `events_exhibitor_detail_new.php` | |
| `sponsor` | No | `events_sponsor_detail.php` | |
| `speakers` | No | `events_speaker_detail.php` | |
| `speaker_detail` | No | `events_speakder_details.php` | Note: typo filename |
| `speaker` / `speaker_detail_page` | No | `events_speaker_page.php` | |
| `hall` | No | `events_hall_listing_page.php` | |
| `swag_wall` | No | `swag_wall.php` | |
| `events.tao.ai` | No | `events_tao_ai.php` | |
| `events.tao.ai.new` | No | `events_tao_ai_new.php` | |
| `allevents` | No | `all_events.php` | |
| `tao.connect.html` | No | `tao_connect_html.php` | |
| `d` | No | `events_detail.php` (or `events.php` if `$_GET['q']=='main'`) | Event detail route |
| `dd` | No | `events_detail_opt.php` (or `events.php` if `$_GET['q']=='main'`) | Optimized detail |
| `about` | No | `visitor.php` | |
| `mobile` | No | `events_mobile.php` (empty) | |
| `next` | No | `next.php` | |
| `status` | No | `event_status.php` | |
| `event_tables` | No | `events_tables.php` | |
| `tables` | No | `tables.php` (`$iframe=0`) | |
| `tables-iframe` | No | `tables.php` (`$iframe=1`) | |
| `dash` | No | Redirect to `/fwd/ss/{hash}/log/1/u/loc/events` | Dashboard login flow |
| `event_sponsor` | No | Redirect to `/fwd/ss/{hash}/log/1/u/loc/events/event_sponsor/{goto}{id}{param}` | |
| `master` | No | Redirect to `/fwd/ss/{hash}/log/1/u/loc/events/master-room/{goto}{id}` | |
| `post` | No | Redirect to `/fwd/ss/{hash}/log/1/u/loc/events/post` | Event creation |
| `club` | No | `adapter.php` (was `club.php`) | Networking entry |
| `chat` | No | `chat.php` (or `chat_session.php`/`chat_exhibitor.php` if segment2 matches) | |
| `chat-opt` | Yes | `chat-opt.php` or session/exhibitor variants | Optimized chat |
| `chat1` | Yes | `chat_new.php` | |
| `session_slot` | No | `events_session_form.php` | |
| `export_rsvp` | Yes | `export_rsvp.php` | |
| `export_raffle_entries` | Yes | `export_raffle_entries.php` | |
| `export_raffle_feedback` | Yes | `export_raffle_feedback.php` | |
| **default** | No | Demo: `events_tao_ai_new.php` (no action) or `all_events.php` (with action). Non-demo: `events.php` (always, `|| 1` overrides login check) | |

**Chat sub-routing** (`action == 'chat'`):
- `/events/chat/session/{eventtoken}` → `chat_session.php`
- `/events/chat/exhibitor/{eventtoken}` → `chat_exhibitor.php`
- `/events/chat/id/events/{eventtoken}` → `chat.php`

---

## 3. Constants

### Event Status Constants (main.php:22-30)

| Constant | Value | Meaning |
|---|---|---|
| `TAOH_EVENTS_EVENT_UNPUBLISHED` | 1 | Draft/unpublished |
| `TAOH_EVENTS_EVENT_SUSPENDED` | 2 | Suspended/paused |
| `TAOH_EVENTS_EVENT_EXPIRED` | 3 | Ended/expired |
| `TAOH_EVENTS_EVENT_PUBLISHED` | 4 | Published/listed |
| `TAOH_EVENTS_EVENT_ACTIVE` | 5 | Active |
| `TAOH_EVENTS_EVENT_LIVEABLE` | 6 | Can go live |
| `TAOH_EVENTS_EVENT_EARLY_START` | 7 | Starting early |
| `TAOH_EVENTS_EVENT_START` | 8 | Live now |
| `TAOH_EVENTS_EVENT_STOP` | 9 | Stopped |

### RSVP Status Constants (main.php:32-38)

| Constant | Value | Meaning |
|---|---|---|
| `TAOH_EVENTS_RSVP_SUSPENDED` | 1 | Not allowed |
| `TAOH_EVENTS_RSVP_NEW` | 2 | Initial/new |
| `TAOH_EVENTS_RSVP_NOTMATCHED` | 3 | Unmatched |
| `TAOH_EVENTS_RSVP_NOMATCH` | 4 | No match found |
| `TAOH_EVENTS_RSVP_MATCH` | 5 | Matched |
| `TAOH_EVENTS_RSVP_NOMATCH_LIVE` | 6 | Live, no match |
| `TAOH_EVENTS_RSVP_MATCH_LIVE` | 7 | Live, matched |

### URL & App Constants (main.php:11-19)

| Constant | Value |
|---|---|
| `TAOH_APP_SLUG` | `'events'` |
| `TAOH_CURR_APP_SLUG` | `'events'` |
| `TAOH_CURR_APP_URL` | `TAOH_SITE_URL_ROOT . '/events'` |
| `EVENTS_EVENT_GET` | `TAOH_API_PREFIX . "/events.event.get"` |
| `EVENTS_RSVP_GET` | `TAOH_API_PREFIX . "/events.rsvp.get"` |
| `TAOH_EVENTS_URL` | `TAOH_SITE_URL_ROOT . "/events"` |
| `TAOH_CURR_APP_IMAGE_SQUARE` | `TAOH_CDN_PREFIX . '/app/events/images/events_sq.png'` |
| `TAOH_CURR_APP_IMAGE` | `TAOH_CDN_PREFIX . '/app/events/images/events.png'` |

### Feature Flags (used across files)

| Flag | Purpose |
|---|---|
| `EVENT_DEMO_SITE` | Demo site mode — changes routing and API params |
| `TAOH_EVENTS_ENABLE` | Master enable/disable for events app |
| `TAOH_EVENTS_GET_LOCAL` | Use local (site-specific) events only |
| `TAOH_TABLES_DISCUSSION_SHOW` | Show discussion tables tab in lobby |
| `TAOH_COMMENTS_SHOW` | Show comments tab in lobby |
| `TAOH_ENABLE_CONVERSATION` | Show conversations tab in lobby |
| `TAO_CURRENT_APP_INNER_PAGE` | Tracks current inner page (e.g., `'events_lobby'`) |

### Channel Type Constants

| Constant | Default | Purpose |
|---|---|---|
| `TAOH_CHANNEL_EXHIBITOR` | 2 | Exhibitor booth channel type |
| `TAOH_CHANNEL_SESSION` | 7 | Speaker session channel type |

---

## 4. Core Functions (functions.php)

### `event_live_status($start_time, $end_time, $locality = 0)`
- **Purpose**: Determines event live status using custom timestamp format
- **Params**: `$start_time`/`$end_time` as numeric timestamps (e.g., `20241125092000`), `$locality` (0=UTC, 1=user timezone)
- **Returns**: `'before'` | `'prelive'` | `'live'` | `'postlive'` | `'after'`
- **Logic**: Uses `tao_timestamp()`. Pre-live = same day before start. Post-live = same day after end.
  - `$start_dday = floor($start_time / 100000) * 1000000` (start of day)
  - `$end_dday = floor($end_time / 1000000) * 1000000 + 999999` (end of day)

### `event_live_state($start_time, $end_time, $event_status, $locality = 0)`
- **Purpose**: Determines event state using UTC datetime strings (e.g., `"2024-11-25 09:20:00"`)
- **Params**: `$start_time`/`$end_time` as datetime strings, `$event_status` (int), `$locality`
- **Returns**: `'before'` | `'live'` | `'after'`
- **Logic**:
  - If `$locality`: uses user timezone via `taoh_user_timezone()`, compares with `TAOH_TIMEZONE_FORMAT`
  - If not: uses `gmdate("Y-m-d H:i:s")`, converts all to Unix timestamps via `strtotime()`
- **Used by**: All display pages, widget functions, button rendering

### `event_action_button($event_arr, $tokenkey = 0)`
- **Purpose**: Renders appropriate CTA button based on user state + event state
- **Params**: `$event_arr` (full event data array), `$tokenkey` (ticket type key)
- **Outputs HTML directly** (echo, not return)
- **State matrix**:

| User State | Event State | Button Output |
|---|---|---|
| Logged in + RSVP'd | Live | "You're going!" + "Event Live!" badge + calendar widget |
| Logged in + RSVP'd | Before | "You're going!" + "Event Status!" badge + calendar widget |
| Logged in + RSVP'd | After | "Check Event Lobby!" + "Event Ended" badge |
| Logged in + No RSVP | Live | "LIVE!" badge + "Select ticket" dropdown |
| Logged in + No RSVP | Before | "Select ticket" dropdown |
| Logged in + No RSVP | After | "Event Ended" badge |
| Not logged in | Any | "Login to Register" button → `TAOH_LOGIN_URL` |

- **Key elements**: `.rsvp_btn` class, `.sub-menu` for ticket type dropdown, `.click_metrics` for tracking

### `event_time_display($input_date, $locality = 0, $event_timezone_abbr = '', $input = 'date', $format = 'D, M d, Y h:i A')`
- **Purpose**: Formats event datetime for display with timezone awareness
- **Params**: `$input_date` (format `YmdHis`), `$locality` (0=local/UTC, 1=global), `$format` (PHP date format)
- **Returns**: Formatted date string
- **Logic**:
  - Global event (`$locality=1`): Creates DateTime from user timezone directly
  - Local event (`$locality=0`): Creates DateTime from UTC, converts to user timezone
- **Wrapped in** `function_exists` check to prevent redefinition

### `event_state_widget($event_arr, $hide_rsvp = 1)`
- **Purpose**: Renders right-sidebar "Event Details" card with status, dates, venue, RSVP button
- **Returns**: `1`
- **Outputs**: Full card HTML with:
  - Event status badge (Live/Not Live/Ended)
  - RSVP registration status with ticket selector
  - Start/end dates formatted via `event_time_display()`
  - Timezone display
  - Venue info (varies by event_type: virtual/hybrid/in-person)
  - Lobby link for virtual/hybrid: `/events/chat/id/events/{eventtoken}`
  - Map link for in-person (if `map_link` is valid URL)
  - Calendar widget via `taoh_calendar_widget($event_arr)`

### `event_state_center_widget($event_arr, $hide_rsvp = 1)`
- **Purpose**: Horizontal center-aligned event details bar (used in lobby pages)
- **Returns**: `1`
- **Similar to** `event_state_widget` but horizontal flex layout with SVG icons
- **CSS class**: `.event_details_center`
- **Contains**: Registration status, event status badge, start/end dates, event type with venue

### `event_state_center_widget2($event_arr, $hide_rsvp = 1)`
- **Purpose**: Alternative center widget with Bootstrap grid (row/col) layout
- **Default timezone**: Falls back to `'America/New_York'` if user timezone empty
- **Uses**: `col-12 col-sm-6 col-xl-3` grid for date/time/timezone/profile sections

### `changeDateTimezone($date, $to, $from = 'America/New_York', $targetFormat = "Y-m-d H:i:s", $dstcheck = true)`
- **Purpose**: Convert datetime between timezones with DST handling
- **Params**: `$date` (datetime string), `$to` (target TZ), `$from` (source TZ), `$targetFormat`, `$dstcheck`
- **Returns**: Formatted date string in target timezone
- **DST Logic**: Uses `DateTimeZone::getTransitions()` to detect DST, applies offset manually if DST is active

### `field_locations($coordinates="", $location="", $geohash="", $js="")`
- **Purpose**: Renders location selector dropdown with geohash support
- **Returns**: HTML string with `<select id="locationSelect">`, hidden inputs for `full_location` and `geohash`
- **Includes**: `<script>locationSelect();</script>` initialization
- **Wrapped in** `function_exists` check

---

## 5. AJAX Endpoints (ajax.php)

All endpoints are dispatched via `$_GET['taoh_action']` or `$_POST['taoh_action']`.

### `events_get_rooms()`
- **Method**: POST
- **Purpose**: Fetch room list from CDN
- **Params**: `$_POST['type']` ('rolechat'|'orgchat'|other), `$_POST['mod']`, `$_POST['misc']`, `$_POST['maxr']`, `$_POST['term']`
- **API call**: `GET /api/find.php` on `TAOH_CDN_PREFIX`
- **Auth**: `taoh_get_dummy_token(1)`
- **Cache**: SHA256 hash of call + params

### `events_get()`
- **Method**: GET
- **Purpose**: Main event listing with filtering/pagination
- **Params**: `$_GET['ops']` (default 'list'), `$_GET['search']`, `$_GET['geohash']`, `$_GET['offset']`, `$_GET['limit']` (default 12), `$_GET['from_date']`, `$_GET['to_date']`, `$_GET['filter_type']`, `$_GET['call_from']`, `$_GET['filters']`
- **API call**: `GET events.get` on `TAOH_API_PREFIX`
- **Auth**: `taoh_get_dummy_token(1)` (or full token for saved/rsvp_list)
- **Cache**: `cache_time: 120` seconds. Disabled for `filter_type` in `['saved', 'rsvp_list']`
- **Key params**: `demo: EVENT_DEMO_SITE`, `local: TAOH_EVENTS_GET_LOCAL`, `cfcc5h: 1`
- **Wrapped in** `function_exists` check

### `events_get_tao()`
- **Method**: GET
- **Purpose**: TAO demo site events
- **Same structure as** `events_get()` but with `demo: 1`, optional `event_type`, `list_all` flag
- **Auth**: `$_GET['token'] ?? taoh_get_api_token(1)`

### `events_get_all_tao()`
- **Method**: GET
- **Purpose**: All TAO events with `list_all: 1`
- **Same structure** but always sets `list_all: 1`, `cfcc5h: 1`

### `get_my_rsvp()`
- **Method**: POST
- **Purpose**: Fetch user's active RSVPs
- **Params**: `$_POST['limit']`, `$_POST['offset']`, `$_POST['mod']`
- **API call**: `GET events.get` with `ops: 'rsvpactive'`
- **Auth**: `taoh_get_api_token()` (authenticated)
- **Cache**: `taoh_p2us('events.get') . TAOH_API_SECRET . '_rsvpactive'`, TTL 3600

### `taoh_invite_rsvp_type()`
- **Method**: POST
- **Purpose**: Send RSVP invitation via referral system
- **Params**: `$_POST['event_title']` (base64 encoded), `$_POST['detail_link']`, `$_POST['from_link']`
- **API call**: `POST core.refer.put` with `ops: 'invite'`
- **Side effects**: Sets cookies `{TAOH_ROOT_PATH_HASH}_refer_token` and `{TAOH_ROOT_PATH_HASH}_referral_back_url` (1 day expiry)
- **Returns**: JSON `{success: 1, refer_token: "..."}`

### `event_like_put()`
- **Method**: POST
- **Purpose**: Save/bookmark an event
- **Params**: `$_POST['contttoken']`, `$_POST['eventtoken']`
- **API call**: `POST content.save`
- **Cache invalidation**: Removes `events_*` and `event_detail_{eventtoken}`
- **Redis store**: `taoh_intaodb_events`
- **Side effect**: Calls `taoh_delete_local_cache('events', $remove)`

### `event_rsvp_put()`
- **Method**: POST
- **Purpose**: Track RSVP click metric
- **Params**: `$_POST['conttoken']`, `$_POST['ptoken']`, `$_POST['met_click']`
- **API call**: `taoh_cacheops('metricspush', $values)` — pushes metric array

### `rsvp_status_check()`
- **Method**: POST
- **Purpose**: Check if current user has RSVP'd to event, return ticket info
- **Params**: `$_POST['event_token']`, `$_POST['ticket_types']` (JSON string)
- **API calls**:
  1. `GET events.rsvp.get` with `ops: 'status'`, `cache_required: 0`
  2. If RSVP'd: `GET events.rsvp.get` with `ops: 'info'`, `rsvptoken: {token}`
- **Returns**: JSON `{success: true, rsvp_status: {...}, rsvp_info: {...}, ticket_type: "..."}`
- **Auth**: `taoh_get_dummy_token()`

### `events_get_detail()`
- **Method**: POST
- **Purpose**: Fetch event detail content (returns HTML)
- **Params**: `$_POST['eventtoken']`, `$_POST['ptoken']`
- **Includes**: `event_detail_content.php`
- **Content-Type**: `application/html`

### `get_event_rsvp($eventtoken)`
- **Method**: GET (called directly with param)
- **Purpose**: Get RSVP data for event
- **API call**: `GET events.rsvp.get` with `ops: 'rsvp'`
- **Auth**: `TAOH_API_TOKEN`

### `get_event_baseinfo()`
- **Method**: POST
- **Purpose**: Fetch event base info
- **Params**: `$_POST['eventtoken']`
- **API call**: `GET events.event.get` with `ops: 'baseinfo'`
- **Cache**: `cache_name: 'event_detail_{eventtoken}'`, `cfcc5m: 1` (5 min CF cache)
- **Auth**: `taoh_get_api_token(1)`
- **Wrapped in** `function_exists` check

### `get_event_rsvp_status()`
- **Method**: POST
- **Purpose**: Get current user's RSVP status for event
- **Params**: `$_POST['eventtoken']`, optional `$_POST['token']`
- **API call**: `GET events.rsvp.get` with `ops: 'status'`, `cache_required: 0`
- **Wrapped in** `function_exists` check

### `get_event_rsvp_info()`
- **Method**: POST
- **Purpose**: Get detailed RSVP info by rsvptoken
- **Params**: `$_POST['rsvptoken']`, optional `$_POST['token']`
- **API call**: `GET events.rsvp.get` with `ops: 'info'`, `cache_required: 0`

### `get_event_sponsors()`
- **Method**: GET/POST (uses `$_REQUEST`)
- **Purpose**: Fetch event sponsors list
- **Params**: `$_REQUEST['eventtoken']`, optional `$_REQUEST['token']`
- **API call**: `GET events.sponsor.get` with `ops: 'list'`
- **Cache**: `cache_name: 'event_details_sponsor_{eventtoken}'`, `cache_time: 7200` (2 hours)

### `get_event_rsvped_users()`
- **Method**: GET/POST (uses `$_REQUEST`)
- **Purpose**: List attendees who RSVP'd
- **Params**: `$_REQUEST['eventtoken']`, `$_REQUEST['search']`, optional `$_REQUEST['token']`
- **API call**: `GET events.rsvp.users.list`
- **Cache**: `cache_name: 'event_rsvp_users_{eventtoken}'` (or `_{search}` suffix), `ttl: 7200`

### `event_email_send()`
- **Method**: POST
- **Purpose**: Send event-related emails
- **Params**: `$_POST['ptoken']`, `$_POST['email_type']`, `$_POST['event_token']`, `$_POST['ticket_type_slug']`, `$_POST['email_title']`, `$_POST['email_description']`
- **API call**: `POST events.rsvp.manageemail`
- **Text encoding**: Uses `taoh_title_desc_encode()` on title/description

### `get_event_MetaInfo()`
- **Method**: POST
- **Purpose**: Fetch event metadata (speakers, exhibitors, etc.)
- **Params**: `$_POST['eventtoken']`, optional `$_POST['type']`, `$_POST['search']`, `$_POST['search_speaker_name']`
- **API call**: `GET events.content.get`
- **Cache**: `cache_name: 'event_MetaInfo_{eventtoken}'` + optional `_{type}_{md5(search)}` suffix, `cfcc5h: 1`

### `save_exhibitor_slot()`
- **Method**: POST (multipart form)
- **Purpose**: Create or update exhibitor slot with file uploads
- **Params**: `$_POST['eventtoken']`, `$_POST['is_organizer']`, `$_POST['country_locked']`, `$_POST['exhibitor_id']`, `$_FILES['exh_logo_upload']`, `$_FILES['exh_banner_upload']`, plus form fields
- **File upload**: To `TAOH_CDN_PREFIX . '/cache/upload/now'` via `taoh_remote_file_upload()`
- **API call**: `POST events.content.post` with `meta_key: 'event_exhibitor'`
- **Post-save**: Creates/updates networking room channels via `NtwAdapterEvents`
- **Cache invalidation**: `event_MetaInfo_{eventtoken}*`
- **Text encoding**: `taoh_title_desc_encode()` on title, subtitle, description, raffle fields

### `event_save_speaker()`
- **Method**: POST (multipart form)
- **Purpose**: Create or update speaker/session
- **Similar to** `save_exhibitor_slot` but for speakers
- **File uploads**: `spk_logo_upload`, `spk_image_upload`, `spk_profileimg_upload[]` (array)
- **API call**: `POST events.content.post` with `meta_key: 'event_speaker'`
- **Post-save**: Creates/updates session channels via `NtwAdapterEvents::constructEventSessionChannelInfo()`
- **Array reindexing**: `array_values()` on `spk_name`, `spk_desig`, `spk_company`, `spk_bio`, `spk_linkedin`, `spk_profileimg`

### `speaker_get_detail()`
- **Method**: POST
- **Purpose**: Returns HTML partial for speaker detail modal
- **Includes**: `events_speaker_detail.php`

### `get_speaker_detail()`
- **Method**: POST
- **Purpose**: Returns JSON speaker data
- **API call**: `GET events.content.detail` with `meta_id: speaker_id`
- **Cache**: `cache_name: 'event_MetaInfo_{eventtoken}_speaker_{speaker_id}'`, `cfcc5h: 1`

### `update_event_exhibitor_raffle()`
- **Method**: POST
- **Purpose**: Submit raffle entry for exhibitor
- **API call**: `POST TAOH_CACHE_CHAT_PROC_URL` with `ops: 'event_exhibitor'`, `status: 'post'`

### `get_event_exhibitor_raffle()`
- **Method**: POST
- **Purpose**: Get raffle status for exhibitor
- **API call**: `POST TAOH_CACHE_CHAT_PROC_URL` with `ops: 'event_exhibitor'`, `status: 'get'`

### `update_exhibitor_rating()`
- **Method**: POST
- **Purpose**: Rate an exhibitor
- **API call**: `POST events.exhibitor.content.post` with `type: 'rating'`
- **Cache invalidation**: `event_MetaInfo_{eventtoken}_exhibitor_{exhibitor_id}`

### `add_exhibitor_comments()`
- **Method**: POST
- **Purpose**: Add comment on exhibitor
- **API call**: `POST events.exhibitor.content.post` with `type: 'comment'`

### `speaker_exhibitor_save_put()`
- **Method**: POST
- **Purpose**: Save/bookmark a speaker or exhibitor
- **API call**: `POST content.save` with slug, optional `speaker_id`/`exhibitor_id`
- **Cache invalidation**: `events_*`, `event_detail_{eventtoken}`, `event_Saved_*`
- **Redis store**: `taoh_intaodb_events`

### `get_event_saved_list()`
- **Method**: POST
- **Purpose**: Get user's saved/bookmarked items for event
- **API call**: `GET events.content.save.list`
- **Cache**: `cache_name: 'event_Saved_{eventtoken}'`

### `event_checkin()`
- **Method**: POST
- **Purpose**: Check in user to event (push to registration list)
- **Params**: `$_POST['eventtoken']`, `$_POST['ptoken']`, `$_POST['country_locked']`, `$_POST['country']`, `$_POST['ticket_details']` (JSON)
- **API call**: `POST TAOH_CONNECT_URL` with `ops: 'push_reg_list'`
- **Key**: `'taoh_events_{eventtoken}_{country}_reg_list'`
- **User details**: ptoken, chat_name, avatar, avatar_image, full_location, coordinates, geohash, local_timezone, profile_type, skill, title, company, site info, ticket_details

### `event_checkin_list()`
- **Method**: POST
- **Purpose**: Get checked-in attendees list with pagination
- **Params**: `$_POST['eventtoken']`, `$_POST['page']`, `$_POST['limit']` (default 20), `$_POST['q']` (search), `$_POST['country_locked']`, `$_POST['my_country']`
- **API call**: `POST TAOH_CONNECT_URL` with `ops: 'get_reg_list'`
- **Cache**: `cfcc15m: 1` (15 min CF cache)

### `rsvp_download()`
- **Method**: POST
- **Purpose**: Download RSVP list data
- **API call**: `POST events.rsvp.download`
- **Cache**: `cfcc15m: 1`

### `delete_event_meta()`
- **Method**: POST
- **Purpose**: Delete speaker/exhibitor/sponsor metadata
- **Params**: `$_POST['eventtoken']`, `$_POST['meta_id']`, `$_POST['meta_type']` (must be 'exhibitor'|'sponsor'|'speaker')
- **API call**: `POST events.content.delete`
- **Post-delete**: Deletes room patterns via `TAOH_CACHE_CHAT_PROC_URL`, then deletes associated channel from rooms
- **Channel deletion**: Uses `NtwAdapterEvents::deleteChannel()` with appropriate channel type

### `get_event_exhibitor()`
- **Method**: GET
- **Purpose**: Get single exhibitor detail
- **Params**: `$_GET['eventtoken']`, `$_GET['exhibitor_id']`
- **API call**: `GET events.content.detail`
- **Cache**: `cache_name: 'event_MetaInfo_{eventtoken}_exhibitor_{exhibitor_id}'`, `cfcc5h: 1`

### `get_event_tables()`
- **Method**: POST
- **Purpose**: Get discussion tables list
- **API call**: `POST TAOH_TABLE_REDIS_URL` with `ops: 'list'`, `app: '{TAOH_TABLE_VERSION}_{eventtoken}_tables'`

### `add_event_organizer_banner()`
- **Method**: POST (multipart)
- **Purpose**: Upload organizer banner
- **File upload**: `$_FILES['event_organizer_banner']`
- **API call**: `POST events.content.post` with `meta_key: 'event_organizer_banner'`

### `remove_event_organizer_banner()`
- **Method**: POST
- **Purpose**: Remove organizer banner
- **API call**: `POST events.content.delete`

---

## 6. Action Handlers (actions/main.php)

### `action == "delete_my_rsvp"` (POST)
- **Params**: `$_POST['rsvptoken']`
- **API call**: `GET events.rsvp.get` with `ops: 'delete'`, `status: 'confirm'`, `cache_required: 0`
- **Auth**: `taoh_get_dummy_token()`
- **Returns**: JSON response

### `action == "addrsvp"` (POST)
- **Params**: All `$_POST` fields passed as `toenter`
- **API call**: `POST events.rsvp.post` with `ops: 'addrsvp'`
- **Auth**: `taoh_get_dummy_token()`
- **Success**: Sets success message via `taoh_set_success_message()`, redirects to `/events/confirmation/{eventtoken}/{slug}`
- **Debug mode**: If `$_POST['debug']` set, displays endpoint, postdata, and result

---

## 7. Adapter Layer (NtwAdapterEvents)

**File**: `NtwAdapterEvents.php` (1127 lines)
**Class**: `NtwAdapterEvents`

### Key Methods

#### `generateChannelId(array $channel_slug_data): string`
- Sorts array, joins with `_`, generates 16-char secure slug via `generateSecureSlug()`

#### `constructDefaultChannelInfo(string $eventtoken, array $event_data): array`
- Creates default discussion channel for event
- Channel ID from `[$eventtoken, 'default']`
- Sets: title, description, type (0), image, visibility

#### `constructDefaultEventChannelInfo(string $eventtoken, string $roomslug, array $event_data): array`
- Event-specific default channel
- Includes event title, image, description from `$event_data['conttoken']`

#### `constructEventSessionChannelInfo(string $eventtoken, array $sessions): array`
- Creates channels for speaker sessions
- Channel ID from `[$eventtoken, 'session', $session['ID']]`
- Includes: streaming links, speaker name, timeslot, session description
- Channel type: `TAOH_CHANNEL_SESSION` (default 7)

#### `constructEventExhibitorChannelInfo(string $eventtoken, array $exhibitors): array`
- Creates channels for exhibitor booths
- Channel ID from `[$eventtoken, 'exhibitor', $exhibitor['ID']]`
- Includes: exhibitor title, logo, description, room keywords
- Channel type: `TAOH_CHANNEL_EXHIBITOR` (default 2)

#### `createBulkRoomInfoChannels(array $room_info, string $ptoken): string`
- Creates multiple channels at once
- Fetches event data via `events.event.get` and metadata via `events.content.get`
- Builds default + session + exhibitor channels
- **API call**: `POST TAOH_CHAT_NET_URL` with `ops: 'create_channels_bulk'`

#### `updateChannelInfo(string $roomslug, string $keyword, string $ptoken, array $channel_info): string`
- Updates single channel
- **API call**: `POST TAOH_CHAT_NET_URL` with `ops: 'update_channel'`

#### `deleteChannel(string $roomslug, string $keyword, string $ptoken, array $channel_info): string`
- Deletes single channel
- **API call**: `POST TAOH_CHAT_NET_URL` with `ops: 'delete_channel'`

#### `generateRoomSlug(array $params): array`
- **Params**: `country_code`, `country_name`, `local_timezone`, `eventtoken`, `country_locked`
- **Logic**:
  1. `country_locked=0`: Room slug = `{eventtoken}`
  2. `country_locked=1`: Room slug = `{eventtoken}-{country_code}`
  3. Large events (>500 attendees): Appends timezone abbreviation
- **Returns**: `['success' => true, 'roomslug' => '...']`

#### `constructAndCreateRoomInfo(object $user_info_obj, array $params): array`
- Creates full room via `club.room.info.put` API
- Room data includes: title, description, image, keywords, member limits, event flag
- **Returns**: Room info array with `room.keyslug`

**NtwAdapterEventsFirebase.php** (1160 lines): Parallel class `NtwAdapterEventsFirebase` with identical interface but Firebase backend.

---

## 8. Networking Adapter (adapter.php)

**Flow**:
1. Requires login (redirects to event detail page if not logged in)
2. Extracts `$eventtoken` from URL slug (last segment after `-`)
3. Fetches event base info via `events.event.get`
4. Creates `NtwAdapterEvents` instance
5. Generates room slug via `generateRoomSlug()` (considers country_locked)
6. Creates room via `constructAndCreateRoomInfo()`
7. Creates channels via `createBulkRoomInfoChannels()`
8. Redirects to `/club/room/{slug}-{roomslug}`

**Special redirects**:
- With `$_GET['exhbitor_id']` + `exhbitor_name`: Appends `?chatwithchannelid={id}&chatwithchanneltype=2`
- With `$_GET['session_id']` + `session_name`: Appends `?chatwithchannelid={id}&chatwithchanneltype=7`
- With `$_GET['chatwith']`: Appends `?chatwith={ptoken}`
- Otherwise: Plain redirect to room

---

## 9. API Endpoints Reference

### Event APIs

| Endpoint | Method | Operations (`ops`) | Key Params |
|---|---|---|---|
| `events.get` | GET | `list`, `active`, `rsvpactive`, `rsvplist` | geohash, search, limit, offset, from_date, to_date, filter_type, filters, demo, local, list_all |
| `events.event.get` | GET | `baseinfo`, `nextevent` | eventtoken, cache_name, cache_time |
| `events.content.get` | GET | (none) | eventtoken, type, search, search_speaker_name |
| `events.content.detail` | GET | (none) | eventtoken, meta_id |
| `events.content.save.list` | GET | (none) | eventtoken |
| `events.content.post` | POST | (none) | eventtoken, meta_key, meta_value, meta_id, is_organizer, rsvptoken |
| `events.content.delete` | POST | (none) | eventtoken, meta_id |

### RSVP APIs

| Endpoint | Method | Operations (`ops`) | Key Params |
|---|---|---|---|
| `events.rsvp.get` | GET | `status`, `info`, `rsvp`, `delete` | eventtoken, rsvptoken |
| `events.rsvp.post` | POST | `addrsvp` | toenter (form data) |
| `events.rsvp.users.list` | GET | (none) | eventtoken, search |
| `events.rsvp.users.count` | GET | (none) | eventtoken |
| `events.rsvp.download` | POST | (none) | eventtoken |
| `events.rsvp.list` | GET | (none) | eventtoken |
| `events.rsvp.manageemail` | POST | `email` | ptoken, email_type, event_token, ticket_type_slug |
| `events.user.rsvp` | GET | (none) | (user token) |

### Sponsor/Exhibitor APIs

| Endpoint | Method | Key Params |
|---|---|---|
| `events.sponsor.get` | GET | eventtoken, ops='list' |
| `events.exhibitor.content.post` | POST | type ('rating'/'comment'), exhibitor_id, rating/toenter |

### Content/Metrics APIs

| Endpoint | Method | Purpose |
|---|---|---|
| `content.save` | POST | Save/bookmark events, speakers, exhibitors |
| `system.users.metrics` | GET | User's liked/saved counts |
| `core.refer.put` | POST | Create referral/invite links |
| `core.followup.get.list` | GET | User's following list |

### Networking/Room APIs

| Endpoint | Method | Purpose |
|---|---|---|
| `club.room.info.put` | POST | Create/update room |
| `club.room.info.get` | GET | Get room info |
| `club.room.cell.put` | POST | Update user cell in room |
| `club.room.cell.get` | GET | Get user cell info |

### External Service URLs

| URL Constant | Purpose |
|---|---|
| `TAOH_API_PREFIX` | Main REST API base |
| `TAOH_CDN_PREFIX` | CDN for images, uploads, health checks |
| `TAOH_CDN_FIND` | CDN search/find API |
| `TAOH_CHAT_NET_URL` | Networking service (channels, rooms) |
| `TAOH_CACHE_CHAT_PROC_URL` | Redis-backed processing (room patterns, raffles) |
| `TAOH_CONNECT_URL` | Connection service (check-in, reg list) |
| `TAOH_TABLE_REDIS_URL` | Discussion tables Redis backend |

---

## 10. Authentication & Authorization

### Token Types

| Token | How to Get | Usage |
|---|---|---|
| `TAOH_API_TOKEN` | Defined constant (logged-in user) | Authenticated API calls |
| `taoh_get_api_token()` | Function | Returns user's API token |
| `taoh_get_api_token(1)` | Function with param | Returns token with fallback |
| `taoh_get_dummy_token()` | Function | Public/anonymous API calls |
| `taoh_get_dummy_token(1)` | Function with param | Dummy token with fallback |
| `TAOH_API_SECRET` | Defined constant | Secret key for authenticated ops |
| `TAOH_API_DUMMY_SECRET` | Defined constant | Secret key for public ops |
| `TAOH_OPS_CODE` | Defined constant | Operations code for cache/Redis calls |

### Auth Check Functions

| Function | Purpose |
|---|---|
| `taoh_user_is_logged_in()` | Returns boolean |
| `taoh_user_all_info()` | Returns user object (ptoken, country_code, country_name, local_timezone, email, chat_name, avatar, etc.) |
| `taoh_user_timezone()` | Returns user's timezone string |
| `taoh_session_get(TAOH_ROOT_PATH_HASH)` | Returns session data with `USER_INFO` |

### User Object Properties

```php
$user_info_obj->ptoken          // Personal user token
$user_info_obj->country_code    // e.g., "US"
$user_info_obj->country_name    // e.g., "United States"
$user_info_obj->local_timezone  // e.g., "America/New_York"
$user_info_obj->email
$user_info_obj->chat_name
$user_info_obj->avatar
$user_info_obj->avatar_image
$user_info_obj->full_location
$user_info_obj->coordinates
$user_info_obj->geohash
$user_info_obj->type            // Profile type
$user_info_obj->skill
$user_info_obj->title
$user_info_obj->company
```

### Authorization Levels

1. **Public**: Can view event listings, detail pages, visitor.php
2. **Logged in**: Can RSVP, access chat, save events, view attendees
3. **RSVP'd user**: Can access lobby, create exhibitor/speaker slots
4. **Organizer** (`$_POST['is_organizer'] == 1`): Can manage exhibitors, speakers, banners without RSVP
5. **Creator** (`$_GET['creator']`): Can access event management dashboard

---

## 11. Feature Modules — File-by-File

### events.php (1850 lines) — Main Events Listing

- **Auth**: Always loads (login check bypassed with `|| 1` on line 313)
- **Pre-fetches**:
  - User metrics (liked events) via `system.users.metrics`
  - User RSVPs via `events.user.rsvp`
- **Renders**: Hero section, event grid, pagination, share modal
- **JS Init**: `taoh_events_init()` — loads events via AJAX to `events_get`
- **Key JS functions**:
  - `render_events_grid_template(data)` — builds event card HTML
  - `searchFilter()` — handles search form
  - `date_read(timestamp, timezone)` — format dates
  - `convertToSlug(text)` — URL-safe slugs
- **Pagination**: `jquery.pagination.js`, 10 items per page
- **Share modal**: `#ShareModel` with Facebook, Twitter, LinkedIn, Email buttons
- **Social share URLs**:
  - Facebook: `https://www.facebook.com/sharer.php?u={url}`
  - Twitter: `https://twitter.com/intent/tweet?url={url}&text={title}`
  - LinkedIn: `https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}`

### events_detail.php (1960 lines) — Event Detail

- **URL pattern**: `/events/d/{slug}-{eventtoken}`
- **Extracts**: `$eventtoken` from last segment after `-`
- **API calls**:
  1. `events.event.get` with `ops: 'baseinfo'` → `$event_arr`
  2. `events.rsvp.get` with `ops: 'status'` → RSVP check
- **Renders**: Event title, image, dates, description, action buttons, sidebar widget
- **Uses**: `event_live_state()`, `event_action_button()`, `event_state_widget()`
- **Lobby link**: `/events/chat/id/events/{eventtoken}`

### events_lobby_hall.php (1386 lines) — Event Lobby

- **Purpose**: Tabbed event lobby after entering event
- **Tabs** (conditionally shown):
  1. Agenda — sessions schedule
  2. Description — event description
  3. Discussions/Tables — discussion tables (if `TAOH_TABLES_DISCUSSION_SHOW`)
  4. Comments (if `TAOH_COMMENTS_SHOW`)
  5. Attendees/RSVP Directory
  6. Exhibitors/Sponsors
  7. Speakers
  8. Conversations (if `TAOH_ENABLE_CONVERSATION`)
- **Sets**: `TAO_CURRENT_APP_INNER_PAGE = 'events_lobby'`
- **Includes**: Multiple sub-files for each tab content

### chat.php (3344 lines) — Event Chat/Networking

- **Largest file** in the events directory
- **Purpose**: Full networking interface within event
- **Flow**:
  1. Auth check (redirects to detail page if not logged in)
  2. Fetches event data, RSVP status, exhibitor/speaker data
  3. Geo-locking check (`country_locked`)
  4. Room creation via `NtwAdapterEvents`
  5. Renders lobby with tabs, chat interface, modals
- **Key modals**: Contact Host, Add Co-Attendees, RSVP Ticket, Complete Settings, Networking Match
- **Includes**: `events_lobby_hall.php`, `chat-modal.php`, `events_footer.php`

### events_agenda.php (1236 lines) — Agenda

- **Async function**: `getEventAgenda()` fetches and renders schedule items
- **API call**: `events.content.save.list` for user's saved agenda items
- **Features**: Time badges, speaker info, save/close buttons, hall color coding
- **Dojo notifications**: `notifyLiveDiscussionTables()` for live updates
- **Client-side DB**: IntaoDB for caching saved items

### events_conversations.php (1297 lines) — Conversations

- **Three sub-tabs**: Group Chats, Direct Messages, Speed Networking
- **Skeleton loading**: Shows loading placeholders during async fetch
- **Features**: Suggested action cards, conversation filtering, user profiles

### events_exhibitors.php (1248 lines) — Exhibitors

- **API call**: `events.content.save.list` for bookmarked exhibitors
- **Tracking**: Social token via SHA256 hash of ptoken
- **Modal**: `#sponsorDetailModal` for quick exhibitor view
- **Includes**: Sponsor form

### events_rsvp_directory.php (877 lines) — Attendee Directory

- **API call**: `core.followup.get.list` for user's following list (cached with CRC32 hash)
- **Features**: Follow/unfollow buttons with toggle state
- **CSS**: `.erd_follow_btn` with `data-follow_status` attribute
- **Pagination**: Built-in

### events_sponsor_detail.php (821 lines) — Sponsor Detail

- **Shows**: Sponsor logo, name, description, website, contact info
- **Error handling**: `showErrorPage()` for invalid sponsor
- **RSVP check**: Verifies user has RSVP'd before showing full content

### export_rsvp.php — Excel RSVP Export

- **Library**: PhpSpreadsheet
- **API call**: `POST events.rsvp.download`
- **Output**: Excel file grouped by ticket type
- **Data mapping**: User fields (name, email, ptoken, profile), ticket fields (title, type, cost), dynamic Q&A
- **Checkbox answers**: Unserialized and joined with commas

### export_raffle_entries.php — Excel Raffle Export

- **API call**: `GET events.raffle.users.list`
- **Output**: Excel with headers: name, email, ptoken, profile_type, answer

### event_health_check.php — Health Check

- **API call**: `GET TAOH_CDN_PREFIX/club/health.php`
- **States**: info, acknowledge, warning, critical, success
- **Features**: Dismissible modal, optional CTA button, URL `{{baseurl}}` placeholder replacement

### includes/error.php — Error Page

- **Features**: Error code display, "Go Back" button, "Refresh" button, auto-refresh every 60 seconds
- **Error contexts**: `event_exhibitor` (code 1001) = invalid request, default = server load
- **Variables**: `$error_code` (default 1001), `$error_from` (default 'server')

---

## 12. Frontend Patterns

### JavaScript Functions (commonly used across files)

| Function | Purpose |
|---|---|
| `taoh_events_init()` | Initialize event listing AJAX load |
| `taoh_rsvp_init()` | Initialize RSVP listing |
| `render_events_grid_template(data)` | Build event card HTML from JSON |
| `render_rsvp_grid_template(data)` | Build RSVP card HTML |
| `date_read(timestamp, timezone)` | Format timestamps for display |
| `convertToSlug(text)` | Create URL-safe slugs |
| `searchFilter()` | Handle search form submission |
| `getEventAgenda()` | Async: fetch and render agenda |
| `getEventSpeakers()` | Async: fetch and render speakers |
| `getEventTables()` | Async: fetch and render tables |
| `getEventRooms()` | Iterate exhibitor room keywords |
| `getEventBaseInfo()` | Fetch event details with IntaoDB caching |
| `getEventRSVPStatus()` | Fetch RSVP status with IntaoDB caching |
| `constructOrganizerVideoModalContent()` | Load organizer video message |
| `notifyLiveDiscussionTables()` | Dojo notification for live tables |

### Client-Side Storage (IntaoDB)

IntaoDB is an IndexedDB wrapper used for client-side caching:

| Key Pattern | Purpose |
|---|---|
| `eventBaseInfoKey` | Cached event base info |
| `eventRSVPStatusKey` | Cached RSVP status |
| `eventHallAccess` | User's hall access permissions |
| `sponsorGroup` | Organized sponsor data |
| `org_video_watched_key` | Track if organizer video was shown |

### CSS Framework & Theme

- **Bootstrap 4/5**: Grid system, modals, badges, buttons
- **Primary blue**: `#2557A7`
- **Gold/warning**: `#FCBC50`
- **Key CSS classes**:
  - `.event_details_center` — horizontal event details bar
  - `.event_live_suc` / `.event_live_war` — live status badges
  - `.edit-status-js` — RSVP status container
  - `.rsvp_btn` — RSVP button
  - `.click_metrics` — tracking-enabled elements
  - `.sub-menu` — ticket type dropdown
  - `.menu--main` — dropdown menu container
  - `.erd_follow_btn` — follow/unfollow button
  - `.events-border` — bordered section divider

### Modal IDs

| Modal ID | Purpose | Triggered by |
|---|---|---|
| `#ShareModel` | Social share options | Share button |
| `#sponsorDetailModal` | Sponsor/exhibitor quick view | Exhibitor click |
| `#speakerDetailModal` | Speaker quick view | Speaker click |
| `#upgradeModal` | Ticket upgrade | Upgrade button |
| `#continuePurchase` | Continue purchase after discount | Purchase flow |
| `#shareModal` | Share event with taoh widget | Share flow |

### AJAX Call Pattern

```javascript
// Standard GET
jQuery.get(taoh_ajax_url + '?taoh_action=events_get&ops=list&limit=12&offset=0', function(data) {
    var result = JSON.parse(data);
    // render result
});

// Standard POST
jQuery.post(taoh_ajax_url, {
    taoh_action: 'get_event_baseinfo',
    eventtoken: eventtoken
}, function(data) {
    var result = JSON.parse(data);
});
```

---

## 13. Caching Strategy

### Server-Side Cache (via API params)

| Cache Name Pattern | TTL | Used By |
|---|---|---|
| `event_detail_{eventtoken}` | 5h (`cfcc5h`) or `cfcc5m` (5 min) | `get_event_baseinfo`, adapter.php |
| `event_details_sponsor_{eventtoken}` | 7200s (2h) | `get_event_sponsors` |
| `event_rsvp_users_{eventtoken}` | 7200s (2h) | `get_event_rsvped_users` |
| `event_rsvp_users_{eventtoken}_{search}` | 7200s (2h) | `get_event_rsvped_users` (with search) |
| `event_MetaInfo_{eventtoken}` | 5h (`cfcc5h`) | `get_event_MetaInfo` |
| `event_MetaInfo_{eventtoken}_{type}_{md5}` | 5h | `get_event_MetaInfo` (filtered) |
| `event_MetaInfo_{eventtoken}_speaker_{id}` | 5h | `get_speaker_detail` |
| `event_MetaInfo_{eventtoken}_exhibitor_{id}` | 5h | `get_event_exhibitor` |
| `event_Saved_{eventtoken}` | varies | `get_event_saved_list` |
| `{taoh_p2us}_rsvpactive` | 3600s (1h) | `get_my_rsvp` |

### CF Cache Flags

| Flag | Meaning |
|---|---|
| `cfcc5m` | Cloudflare cache 5 minutes |
| `cfcc15m` | Cloudflare cache 15 minutes |
| `cfcc5h` | Cloudflare cache 5 hours |
| `cfcc2h` | Cloudflare cache 2 hours |

### Cache Invalidation Patterns

| On Action | Removes |
|---|---|
| Save/bookmark event | `events_*`, `event_detail_{eventtoken}` |
| Save speaker/exhibitor | `events_*`, `event_detail_{eventtoken}`, `event_Saved_*` |
| Save exhibitor slot | `event_MetaInfo_{eventtoken}*` |
| Save speaker | `event_MetaInfo_{eventtoken}*` |
| Delete meta | `event_detail_{eventtoken}`, `event_MetaInfo_{eventtoken}*`, `event_Saved_{eventtoken}` |
| Add/remove banner | `event_MetaInfo_{eventtoken}*` |

### Client-Side Cache (IntaoDB / LocalStorage)

- IntaoDB stores event data with timestamp tracking
- LocalStorage keys for organizer video watch status
- Redis store target: `taoh_intaodb_events`

---

## 14. External Integrations

### 1. Main REST API (`TAOH_API_PREFIX`)
- All event CRUD, RSVP, sponsors, speakers, content
- Called via `taoh_apicall_get()` and `taoh_apicall_post()`
- Returns JSON responses

### 2. CDN Service (`TAOH_CDN_PREFIX`)
- Static assets (images, event graphics)
- File uploads (`/cache/upload/now`)
- Health check (`/club/health.php`)
- Room finder API (`TAOH_CDN_FIND`)

### 3. Networking Service (`TAOH_CHAT_NET_URL`)
- Creates rooms and channels for events
- Operations: `create_channels_bulk`, `update_channel`, `delete_channel`
- Called via HTTP POST

### 4. Cache Processing (`TAOH_CACHE_CHAT_PROC_URL`)
- Redis-backed operations
- Room pattern management (`deleteroompattern`)
- Exhibitor raffle entries (`event_exhibitor` ops)
- Called via `taoh_post()`

### 5. Connect Service (`TAOH_CONNECT_URL`)
- Event check-in (`push_reg_list` / `get_reg_list`)
- Registration list management

### 6. Tables Service (`TAOH_TABLE_REDIS_URL`)
- Discussion tables CRUD
- App identifier: `{TAOH_TABLE_VERSION}_{eventtoken}_tables`

### 7. Firebase (via NtwAdapterEventsFirebase)
- Alternative networking backend
- Same interface as NtwAdapterEvents
- Selected when `version=1` in GET params

### 8. PhpSpreadsheet
- Excel export for RSVP, raffle entries, feedback

### 9. Social Platforms
- Facebook, Twitter/X, LinkedIn share URLs
- Email mailto links

---

## 15. Data Flow Diagrams

### Event Listing Flow

```
User visits /events
    → main.php: $action = '' (default)
    → loads events.php
    → PHP: fetches user metrics (system.users.metrics)
    → PHP: fetches user RSVPs (events.user.rsvp)
    → renders page skeleton
    → JS: taoh_events_init()
        → AJAX GET: ?taoh_action=events_get&ops=list&limit=12&offset=0
        → ajax.php: events_get()
            → API: events.get (GET)
            → returns JSON {count, list[]}
        → JS: render_events_grid_template(data)
        → pagination rendered if count > 12
```

### Event Detail Flow

```
User visits /events/d/{slug}-{eventtoken}
    → main.php: $action = 'd'
    → loads events_detail.php
    → PHP: API events.event.get (ops=baseinfo) → $event_arr
    → PHP: API events.rsvp.get (ops=status) → RSVP check
    → PHP: event_live_state() → $state (before/live/after)
    → PHP: renders title, image, description
    → PHP: event_action_button() → CTA button
    → PHP: event_state_widget() → sidebar details card
```

### RSVP Flow

```
User clicks "Select ticket"
    → ticket dropdown shown (populated from event.conttoken.ticket_types)
    → selects ticket type
    → redirect to /events/add_rsvp/{eventtoken}/{title}/{returnUrl}
    → main.php redirects to /fwd/ss/{hash}/log/1/u/loc/events/rsvp/{eventtoken}/{title}/{returnUrl}
    → (authentication/profile check happens in /fwd/ system)
    → shows RSVP form (rsvp.php)
    → form submit POST to actions/main.php (action=addrsvp)
    → API: events.rsvp.post
    → redirect to /events/confirmation/{eventtoken}/{slug}
```

### Networking/Chat Entry Flow

```
User clicks "Join Event" / lobby link
    → visits /events/club/{slug}-{eventtoken}
    → main.php: $action = 'club' → adapter.php
    → auth check (redirect if not logged in)
    → extract $eventtoken from slug
    → API: events.event.get → event data
    → NtwAdapterEvents::generateRoomSlug()
        → considers country_locked, country_code, timezone
    → NtwAdapterEvents::constructAndCreateRoomInfo()
        → API: club.room.info.put → creates room
    → NtwAdapterEvents::createBulkRoomInfoChannels()
        → fetches event metadata (speakers, exhibitors)
        → builds channel list (default + sessions + exhibitors)
        → API: TAOH_CHAT_NET_URL (create_channels_bulk)
    → redirect to /club/room/{room-title-slug}-{roomslug}
        → optional: ?chatwithchannelid={id} for specific channel
```

### Exhibitor/Speaker Save Flow

```
Organizer submits exhibitor form
    → AJAX POST: taoh_action=save_exhibitor_slot
    → ajax.php: save_exhibitor_slot()
    → checks is_organizer or has valid RSVP
    → uploads files to CDN (/cache/upload/now)
    → API: events.content.post (meta_key=event_exhibitor)
    → if existing (exhibitor_id set):
        → delete room patterns (TAOH_CACHE_CHAT_PROC_URL)
        → recreate room (constructAndCreateRoomInfo)
        → update channel (updateChannelInfo)
    → if new:
        → generateRoomSlug()
        → constructAndCreateRoomInfo()
        → createBulkRoomInfoChannels()
    → invalidate cache: event_MetaInfo_{eventtoken}*
```

---

## 16. Key Data Structures

### Event Array (`$event_arr`)

```php
$event_arr = [
    'eventtoken' => 'h72cb237mum5wiz',
    'status' => 8,                          // TAOH_EVENTS_EVENT_* constant
    'locality' => 0,                        // 0=local(UTC), 1=global
    'utc_start_at' => '2024-11-25 09:20:00',
    'utc_end_at' => '2024-11-25 17:00:00',
    'local_start_at' => '20241125092000',
    'local_end_at' => '20241125170000',
    'local_timezone' => 'America/New_York',
    'attendees' => 250,
    'conttoken' => [
        'title' => 'Event Title',
        'description' => '...',
        'image' => 'https://cdn.../image.png',
        'event_type' => 'virtual',          // 'virtual' | 'hybrid' | 'in-person'
        'venue' => 'Convention Center',
        'map_link' => 'https://maps.google.com/...',
        'country_locked' => 0,             // 0=open, 1=country-restricted
        'visibility' => 1,
        'link' => [
            ['label' => 'Website', 'value' => 'https://...']
        ],
        'ticket_types' => [
            ['title' => 'General', 'slug' => 'general', 'cost' => '0'],
            ['title' => 'VIP', 'slug' => 'vip', 'cost' => '50']
        ],
    ],
    'mystatus' => [
        'rsvptoken' => 'abc123',           // null if not RSVP'd
        'rsvp_slug' => 'general',
        'liveable' => 1,
    ],
];
```

### User Info Object (`taoh_user_all_info()`)

```php
$user_info_obj = (object)[
    'ptoken' => 'hN3PCXgw',
    'email' => 'user@example.com',
    'chat_name' => 'John Doe',
    'avatar' => 'jd',
    'avatar_image' => 'https://cdn.../avatar.png',
    'full_location' => 'New York, NY, USA',
    'coordinates' => '40.7128,-74.0060',
    'geohash' => 'dr5x1n7c',
    'local_timezone' => 'America/New_York',
    'country_code' => 'US',
    'country_name' => 'United States',
    'type' => 'professional',              // profile type
    'skill' => 'Engineering',
    'title' => 'Software Engineer',
    'company' => 'TechCorp',
];
```

### Channel Info Structure

```php
$channel_info = [
    'channel_id' => 'a1b2c3d4e5f6g7h8',  // 16-char secure slug
    'channel_type' => 7,                    // TAOH_CHANNEL_SESSION or TAOH_CHANNEL_EXHIBITOR
    'title' => 'Session: Keynote Speech',
    'description' => '...',
    'image' => 'https://cdn.../speaker.png',
    'global_slug' => 'h72cb237mum5wiz',    // eventtoken
    'visibility' => 1,
    'streaming_link' => 'https://...',      // sessions only
    'members' => [],
];
```

### Room Info Structure

```php
$room_info = [
    'room' => [
        'keyslug' => 'events-h72cb237mum5wiz',
        'title' => 'Event: Conference 2024',
        'keyword' => 'h72cb237mum5wiz',    // eventtoken
    ],
    'output' => [...],
];
```

---

## Notes

- **Filename typo**: `events_speakder_details.php` should be `events_speaker_details.php`
- **Dead code**: `events_mobile.php` is empty (1 line), `club.php` is replaced by `adapter.php`
- **Login bypass**: Default route in main.php has `|| 1` making login check always true (line 313)
- **Legacy files**: `old/` directory contains backups that are no longer loaded
- **Error handling**: Most API errors silently fail or return empty data; `includes/error.php` used for explicit error pages
- **Security**: Social token validation uses SHA256 hash of ptoken for referral link verification
