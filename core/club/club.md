# Club Module — Comprehensive Developer Documentation

> **Path:** `/wpl/club/core/club/`
> **Platform:** TAOH (TAO Hub)
> **Stack:** PHP 7+, jQuery, Bootstrap 4, Summernote WYSIWYG
> **Last analyzed:** 2026-01-30
> **Files:** 39 PHP files across root, `includes/`, and `old/` subfolders

---

## 1. Purpose

The **Club** module is the primary community/networking hub of the TAOH platform. It provides:

- **Virtual networking rooms** (chat-based, multi-version UI) with geo-partitioned, time-rotating slugs
- **Member directory** with follow/unfollow, skill tagging, keyword-room navigation
- **Announcements & News Feed** with full CRUD, likes, comments, file uploads
- **Groups** (custom rooms + keyword-generated profile rooms)
- **Lobby** (room landing with multi-layer access control)
- **Classifieds, Employer Branding, Flipper, Alumni** (static/marketing pages)
- **Connections** (following list + direct messaging)
- **User profiles**
- **Live Now** integration
- **Admin tools** (room creation form, announcement management)

All networking chat is powered by an **external microservice** at `TAOH_CHAT_NET_URL`.

---

## 2. Architecture & Data Flow

```
Browser
  |
  +-> main.php  -------------- Central Router (switch on URL slug)
  |       |
  |       +-> club.php              Landing page (RSVP via events.user.rsvp API)
  |       +-> lobby.php             Room entry + access control chain
  |       +-> networking*.php       Chat room UI (versions 1, 3.0, 4, 5, 5_passive, 5_kal)
  |       +-> groups.php            Room listings
  |       +-> directory.php         Member directory
  |       +-> announcements.php     CRUD announcements
  |       +-> news_feed.php         CRUD news feed
  |       +-> connections.php       Following list + DM
  |       +-> profile.php           User profile
  |       +-> classifieds.php       Static placeholder
  |       +-> flipper.php           Static marketing
  |       +-> alum_landing.php      Alumni landing
  |       +-> the_start_club.php    Startup landing
  |       +-> employer_branding.php Employer branding display
  |       +-> employer_branding_form.php  Employer branding form
  |       +-> adapter.php          Auto-create room -> 302 redirect
  |
  +-> ajax.php  -------------- AJAX endpoint (POST taoh_action dispatch, 75+ functions)
  |       |
  |       +- Directory & user ops    (taoh_dir_users_list, taoh_user_info, ...)
  |       +- Room/message ops        (taoh_room_send_message, taoh_rooms_get, ...)
  |       +- Channel ops (v3.0)      (taoh_ntw_create_channel, taoh_ntw_send_message, ...)
  |       +- Speed networking        (taoh_speed_networking_*, taoh_ntw_speed_networking_*)
  |       +- Feed/announcement ops   (taoh_announcement_save, taoh_get_feed_list, ...)
  |       +- Activity tracking       (taoh_track_activities, taoh_get_activities)
  |
  +-> includes/
  |       +- club_header.php        Navigation bar (desktop + mobile)
  |       +- club_room_data.php     Room data helper functions (4 functions)
  |       +- ads_data.php           Static ad data arrays
  |       +- error.php             Error overlay (codes 1002-1010)
  |
  +-> networking_footer*.php ---  Footer scripts (DM keys, profile stage)
  |
  +-> NtwAdapterClub.php -------  Room/channel creation business logic
          |
          +-> TAOH_CHAT_NET_URL    External chat microservice
```

### Request Flow for a Networking Room

1. User clicks room link -> `main.php` routes to `lobby.php`
2. `lobby.php` runs access control chain (publish, country, company, title, skill, lock code, date/time)
3. If allowed -> redirect to `networking{version}.php` (version from room data)
4. Networking page loads chat iframe from `TAOH_CHAT_NET_URL` with room slug
5. Footer file (`networking_footer{version}.php`) sets up DM keys, profile stage JS

### Networking Version Dispatch (main.php lines 75-89)

```php
if (NETWORKING_3_0) {
    include_once('networking_3_0.php');        // Channel-based chat UI
} elseif (NETWORKING_VERSION == 1) {
    include_once('networking1.php');           // V1 legacy
} elseif (NETWORKING_VERSION == 4) {
    include_once('networking4.php');           // V4
} elseif (NETWORKING_VERSION == 5) {
    include_once('networking5.php');           // V5 current main
} elseif (NETWORKING_VERSION == 'mini') {
    include_once('networking5_kal.php');       // KAL/mini variant
} elseif (NETWORKING_VERSION == 'passive') {
    include_once('networking5_passive.php');   // Passive/viewer variant
} else {
    include_once('networking1.php');           // Fallback to V1
}
```

### Room Slug Generation (CRC32)

```php
// NtwAdapterClub::generateRoomSlug()
$week = date('W');  // ISO week number
$slug = hash('crc32', TAOH_SITE_ROOT_HASH . $week . $country_code);
// Deterministic, weekly rotation, geo-partitioned by TAOH_OPS_CODE
```

---

## 3. File-by-File Reference

### 3.1 `main.php` (~101 lines) — Central Router

**Role:** Switch-case router mapping URL path segments to page includes.

**Routes:**

| Slug | File | Notes |
|------|------|-------|
| `rooms` | groups.php (or networking_form.php if `?admin=createroom`) | Room listings / admin form |
| `groups` | groups.php | Alias for rooms |
| `classifieds` | classifieds.php | Static classifieds |
| `zerodayemployer` | zerodayemployer.php | Zero-day employer page |
| `lobby` | lobby.php | Room access gatekeeper |
| `announcements` | announcements.php | Sets `$footer_tracking_link` |
| `news_feed` | news_feed.php | Sets `$footer_tracking_link` |
| `d` | news_feed_detail.php | Detail view for a feed item |
| `alum` | alum_landing.php | Alumni landing |
| `the_start_club` | the_start_club.php | Startup landing |
| `flipper` | flipper.php | Marketing page |
| `employer_branding` | employer_branding.php | Employer branding display |
| `employer_branding_form` | employer_branding_form.php | Employer branding form |
| `profile` | profile.php | User profile |
| `live_url` | loadLiveNowData.php | Live Now data proxy |
| `networking` | adapter.php | Auto-create room + redirect |
| `room` / `custom-room` / `forum` / `livenow` | Version-dependent networking page | See dispatch logic above |
| *(default)* | club.php (if `TAOH_CLUBS_ENABLE`) | Falls through to events/jobs/asks |

---

### 3.2 `NtwAdapterClub.php` (~530 lines) — Room/Channel Business Logic

**Role:** Final class encapsulating room and channel creation.

**Methods:**

| Method | Parameters | Purpose |
|--------|-----------|---------|
| `constructDefaultChannelInfo()` | `$roomInfo` | Build DEFAULT (type=1) channel payload |
| `constructDefaultEventChannelInfo()` | `$roomInfo, $eventData` | Build event channel payload |
| `constructEventSessionChannelInfo()` | `$roomInfo, $sessionData` | Build SESSION (type=7) channel payload |
| `constructEventExhibitorChannelInfo()` | `$roomInfo, $exhibitorData` | Build EXHIBITOR (type=2) channel payload |
| `createBulkRoomInfoChannels()` | `$roomSlug, $channels[]` | POST to `TAOH_CHAT_NET_URL` to batch-create channels |
| `generateRoomSlug()` | `$baseHash, $week, $countryCode` | CRC32-based deterministic slug generation |
| `constructAndCreateRoomInfo()` | `$profileTypes[]` | Full room creation orchestration with 6 profile types |

**Profile types:** attendee, speaker, sponsor, recruiter, moderator, organizer

**Channel types:** DEFAULT(1), EXHIBITOR(2), SESSION(7), SPONSOR

---

### 3.3 `adapter.php` (~63 lines) — Room Auto-Creator + Redirect

**Role:** Creates a networking room via `NtwAdapterClub` if it doesn't exist, then 302-redirects to `/club/room/{slug}-{roomslug}`.

**Requires:** Login (redirects to login if no session).

**Flow:** Instantiate `NtwAdapterClub` -> `generateRoomSlug()` -> `constructAndCreateRoomInfo()` -> `createBulkRoomInfoChannels()` -> `header('Location: ...')`.

---

### 3.4 `ajax.php` (~2748 lines) — Server-Side AJAX Handler

**Role:** POST endpoint dispatched by `taoh_action` parameter. Contains 75+ functions.

#### Directory & User Functions

| Function | Line | API/Endpoint | Purpose |
|----------|------|-------------|---------|
| `taoh_dir_users_list()` | 3 | `users.directory.list` | Paginated directory listing |
| `taoh_user_info()` | 202 | user info API | Fetch single user info |
| `taoh_get_user_live_status()` | 227 | live status API | Check if user is live |
| `taoh_create_google_meet_link()` | 240 | Google Meet API | Create Meet link |

#### Room & Message Functions (Legacy)

| Function | Line | Purpose |
|----------|------|---------|
| `get_keywords_room()` | 135 | Keyword room lookup/creation |
| `taoh_load_network_entries()` | 257 | Load network entries for a room |
| `taoh_room_send_message()` | 330 | Send message to a room |
| `taoh_forum_send_message()` | 356 | Send message to a forum |
| `taoh_hash()` | 384 | Hash utility |
| `taoh_add_video_chat()` | 404 | Add video chat to room |
| `taoh_network_update_online()` | 465 | Update user online status |
| `taoh_rooms_get()` | 641 | Get all rooms |
| `taoh_room_delete()` | 681 | Delete a room |
| `taoh_get_all_my_rooms()` | 698 | Get rooms for current user |
| `taoh_add_user_to_room()` | 763 | Add user to a room |
| `taoh_room_get_message_list()` | 794 | Get messages for a room |
| `taoh_last_job_posted_date()` | 664 | Get last job posted date |

#### Admin & Form Functions

| Function | Line | Purpose |
|----------|------|---------|
| `toah_network_form_post()` | 498 | Admin room creation/edit form handler |

#### Feed & Announcement Functions

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_announcement_save()` | 851 | Save announcement (create/update) |
| `taoh_get_feed_list()` | 901 | Get paginated feed list |
| `taoh_feed_delete()` | 939 | Delete a feed item |
| `taoh_get_feed_detail()` | 953 | Get single feed item detail |
| `feed_like_put()` | 974 | Like/unlike a feed item |

#### Channel Functions (Legacy Chat)

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_create_channel()` | 1135 | Create a chat channel |
| `taoh_channel_send_message()` | 1186 | Send message to channel |
| `taoh_direct_send_message()` | 1231 | Send direct message |
| `taoh_channel_like_message()` | 1269 | Like a channel message |
| `taoh_channel_pin_message()` | 1309 | Pin a channel message |
| `taoh_create_channel_from_ticket()` | 1503 | Create channel from event ticket |
| `taoh_create_channel_with_organizer()` | 1724 | Create channel with organizer |
| `taoh_create_channel_for_1_1()` | 1777 | Create 1:1 DM channel |

#### Speed Networking Functions (Legacy)

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_speed_networking_get_data()` | 1354 | Get speed networking session data |
| `taoh_speed_networking_add_user()` | 1372 | Add user to speed networking |
| `taoh_speed_networking_block_user()` | 1397 | Block user in speed networking |
| `taoh_speed_networking_connect_user()` | 1423 | Connect with user |
| `taoh_speed_networking_connect_user_update()` | 1449 | Update connection status |
| `taoh_speed_networking_get_user()` | 1470 | Get speed networking user |
| `taoh_speed_networking_connect_user_get()` | 1488 | Get connection data |

#### Activity Tracking

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_track_activities()` | 1830 | Track user activity |
| `taoh_get_activities()` | 1843 | Get activity log |

#### Networking 3.0 Functions (Channel-based via `TAOH_CHAT_NET_URL`)

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_ntw_get_room_stamp()` | 1882 | Get room timestamp/version |
| `taoh_ntw_get_room_channel_stamp()` | 1902 | Get channel timestamp |
| `taoh_ntw_create_channel()` | 1926 | Create channel (v3.0) |
| `taoh_ntw_get_channels()` | 1975 | List channels in room |
| `taoh_ntw_get_user_channels()` | 2017 | List user's channels |
| `taoh_ntw_join_channel()` | 2053 | Join a channel |
| `taoh_ntw_add_channel_members()` | 2074 | Add members to channel |
| `taoh_ntw_remove_channel_members()` | 2099 | Remove members from channel |
| `taoh_ntw_delete_channel()` | 2124 | Delete a channel |
| `taoh_ntw_add_broadcast_message()` | 2149 | Add broadcast message |
| `taoh_ntw_get_broadcast_message()` | 2182 | Get broadcast messages |
| `taoh_ntw_star_channel()` | 2216 | Star/favorite a channel |
| `checkChannelPasscode()` | 2243 | Validate channel passcode |
| `taoh_ntw_send_message()` | 2252 | Send message (v3.0) |
| `taoh_ntw_get_channel_type()` | 2296 | Get channel type |
| `taoh_ntw_get_channel_info()` | 2315 | Get channel info |
| `taoh_ntw_add_reply_message()` | 2336 | Reply to message |
| `taoh_pin_message()` | 2367 | Pin message (v3.0) |
| `taoh_ntw_get_messages()` | 2389 | Get messages (v3.0) |
| `taoh_ntw_delete_message()` | 2412 | Delete message |
| `taoh_ntw_react_message()` | 2670 | React to message (emoji) |
| `taoh_forward_channel_transcript()` | 2727 | Forward channel transcript |

#### Networking 3.0 Speed Networking

| Function | Line | Purpose |
|----------|------|---------|
| `taoh_ntw_speed_networking_get_data()` | 2443 | Get speed networking data (v3.0) |
| `taoh_ntw_speed_networking_add_user_old()` | 2465 | Add user (old method) |
| `taoh_ntw_speed_networking_add_user()` | 2486 | Add user (current) |
| `taoh_ntw_add_activity_channel()` | 2505 | Add activity channel |
| `taoh_ntw_get_activity_channel()` | 2525 | Get activity channel |
| `taoh_ntw_speed_networking_block_user()` | 2544 | Block user (v3.0) |
| `taoh_ntw_speed_networking_connect_user()` | 2571 | Connect with user (v3.0) |
| `taoh_ntw_speed_networking_connect_user_update()` | 2593 | Update connection (v3.0) |
| `taoh_ntw_speed_networking_connect_user_revoke()` | 2615 | Revoke connection |
| `taoh_ntw_speed_networking_get_user()` | 2637 | Get user data (v3.0) |
| `taoh_ntw_speed_networking_connect_user_get()` | 2655 | Get connection (v3.0) |
| `taoh_ntw_init_speed_networking()` | 2695 | Initialize speed networking session |
| `taoh_ntw_get_connections()` | 2712 | Get all connections |

---

### 3.5 `club.php` (~2222 lines) — Main Landing Page

**Role:** Club homepage. Fetches RSVP data via `events.user.rsvp` API. Displays event cards, room listings, admin controls.

**API:** `events.user.rsvp`

**Admin check:** `TAOH_ADMIN_TOKENS` (CSV) + `$_SESSION['is_super_admin']`

---

### 3.6 `lobby.php` (~594 lines) — Room Entry + Access Control

**Role:** Multi-layered room access gatekeeper.

**Access Control Chain (in order):**

| Step | Check | Slug Mutation |
|------|-------|--------------|
| 1 | **Publish status** — room must be published | None |
| 2 | **Secret token** — optional `secret_token` parameter | None |
| 3 | **Country filter** — user country must match allowed list | `hash('crc32', slug + country)` |
| 4 | **Company filter** — user company must match | `hash('crc32', slug + company)` |
| 5 | **Title filter** — user title must match | `hash('crc32', slug + title)` |
| 6 | **Skill filter** — user skills must match | `hash('crc32', slug + skill)` |
| 7 | **Lock code** — numeric code entry | `hash('crc32', slug + code)` |
| 8 | **Date/time lock** — room accessible only during scheduled window | None |

**Outputs:** Redirects to networking page on success, shows error overlay on failure.

**Security note:** Lock code validation is client-side only -- can be bypassed.

---

### 3.7 `announcements.php` (~1055 lines) — Announcements CRUD

**Role:** Full create/read/update/delete for admin announcements.

**Features:**
- Summernote WYSIWYG editor
- File uploads (max 10 files) to `TAOH_CDN_PREFIX/cache/upload/now`
- Image uploads to CDN
- Like system (tracked in localStorage -- no server-side dedup)
- Comment system via AJAX
- Admin-only create/edit/delete (checked via `TAOH_ADMIN_TOKENS` CSV + `is_super_admin`)

**Type identifier:** `type=announcement`

---

### 3.8 `news_feed.php` (~1064 lines) / `news_feed_detail.php` (~175 lines) — News Feed

**Role:** Identical structure to announcements but with `type=news_feed`. Detail page shows single item with full content and comments.

---

### 3.9 `directory.php` (~676 lines) — Member Directory

**Role:** Searchable, paginated member list.

**Features:**
- AJAX load via `taoh_dir_users_list` action
- Follow/unfollow via `core.followup.get.list` API
- Pagination: `jquery.twbsPagination`, 20 per page
- Skill display with keyword room navigation (clicking skill -> room)
- Search by name
- Profile card with photo, name, title, company, country

---

### 3.10 `profile_directory.php` (~816 lines) — Flag/Tag Directory

**Role:** Filtered directory using `TAOH_TAG_CATEGORY` for flag/tag-based filtering. Similar to directory.php but with tag-based filters.

---

### 3.11 `groups.php` (~620 lines) — Room Listings

**Role:** Lists custom rooms + keyword-generated profile rooms.

**Key Functions:**
- `getCombinations()` -- generates keyword combinations for profile rooms
- `generatePoster()` -- creates room poster images
- Room visibility checks (published, date range, access level)

**Constants defined:** `TAOH_ROOM_KEY`, `TAOH_ROOM_LIVE`

---

### 3.12 `connections.php` (~730 lines) — Following List + DM

**Role:** Shows users the current user follows. Provides DM capability.

**DM Key generation:**
```php
$dm_key = hash('crc32', 'dm-direct-message');
```

**AJAX action for DM:** `taoh_create_channel_for_1_1`

---

### 3.13 `profile.php` (~887 lines) — User Profile

**Role:** Displays user profile via `taoh_get_user_info($ptoken, 'full')`. Shows profile photo, bio, skills, connections.

---

### 3.14 `networking1.php` (~7241 lines) — Networking V1

**Role:** Chat room UI version 1 (legacy). Includes `check_live_now_token()` for live session validation. Requires auth. Validates profile completion.

---

### 3.15 `networking_3_0.php` (~1128 lines) — Networking V3.0

**Role:** V3.0 channel-based chat UI with sidebar, message list, reply panel.

**Warning:** Has `error_reporting(E_ALL)` enabled -- exposes errors in production.

**Architecture:** Channel sidebar (left) + message area (center) + reply panel (right). All communication via `TAOH_CHAT_NET_URL` with ops/action pattern.

---

### 3.16 `networking4.php` (~11078 lines) — Networking V4

**Role:** V4 networking UI. Includes `check_live_now_token()`.

---

### 3.17 `networking5.php` (~13114 lines) — Networking V5

**Role:** V5 networking UI (current main version). Most feature-complete.

**Key function:** `convertEmbedSrc()` -- converts YouTube/Vimeo/Twitch URLs to embed format.

---

### 3.18 `networking5_passive.php` (~13756 lines) — Networking V5 Passive

**Role:** Passive/viewer variant of V5 networking. Reduced interaction capabilities.

---

### 3.19 `networking5_kal.php` (~15382 lines) — Networking V5 KAL/Mini Variant

**Role:** KAL-specific variant of V5 networking. Selected when `NETWORKING_VERSION == 'mini'`.

---

### 3.20 `networking-visitor.php` (~6961 lines) — Visitor Networking

**Role:** Public/visitor-facing networking page. No login required. Accessed via `networking` slug.

---

### 3.21 `networking_form.php` (~604 lines) — Admin Room Creation Form

**Role:** Admin form for creating/editing networking rooms. Accessed via `rooms?admin=createroom`. Requires `form_fields.php`.

---

### 3.22 `networking_footer1.php` (~830 lines) — Footer V1

**Role:** DM key setup, private chat resolution, profile stage JS for V1 networking.

**DM key:** `hash('crc32', 'dm-direct-message')` -- identical across all footer files.

---

### 3.23 `networking_footer4.php` (~838 lines) — Footer V4

**Role:** Footer scripts for V4 networking. Same DM key pattern.

---

### 3.24 `networking_footer5.php` (~842 lines) — Footer V5

**Role:** Footer scripts for V5 networking. Same DM key pattern.

---

### 3.25 `includes/club_header.php` (~122 lines) — Navigation Bar

**Role:** Top navigation with conditional tabs.

**Conditional flags:**
- `TAOH_ANNOUNCEMENT_ENABLE` -- shows/hides Announcements tab
- `TAOH_NEWS_ENABLE` -- shows/hides News tab
- `$pagename` variable -- highlights active tab

**Responsive:** Separate desktop and mobile nav markup.

---

### 3.26 `includes/club_room_data.php` (~429 lines) — Room Data Helpers

**Functions:**

| Function | Purpose |
|----------|---------|
| `get_networking_keyword_room_data()` | Fetch keyword-based room data |
| `get_networking_directory_room_data()` | Fetch directory room data |
| `get_networking_local_room_data()` | Fetch local/geo room data |
| `get_forum_room_data()` | Fetch forum room data |

---

### 3.27 `includes/ads_data.php` (~52 lines) — Static Ad Data

**Role:** Returns hardcoded arrays for Jobs and Community Networking Hour ad placements.

---

### 3.28 `includes/error.php` (~126 lines) — Error Overlay

**Role:** Displays error overlay with auto-refresh (60 seconds).

**Error codes:** 1002-1010 (room not found, access denied, expired, capacity, etc.)

---

### 3.29 `loadLiveNowData.php` (~5 lines) — Live Now Proxy

```php
echo file_get_contents(TAOH_LIVE_NOW_URL);
```

**Warning:** No error handling, potential SSRF if URL misconfigured.

---

### 3.30 `classifieds.php` (~1494 lines) — Classifieds

**Role:** Fully hardcoded placeholder cards. No dynamic data. Static HTML.

---

### 3.31 `flipper.php` (~94 lines) — Flipper Marketing Page

**Role:** Static marketing/landing page.

---

### 3.32 `the_start_club.php` (~238 lines) — Startup Landing

**Role:** Static startup community landing page.

---

### 3.33 `alum_landing.php` (~38 lines) — Alumni Landing

**Role:** Alumni community landing page.

---

### 3.34 `employer_branding.php` (~884 lines) — Employer Branding Display

**Role:** Employer branding showcase with heavy inline CSS.

---

### 3.35 `employer_branding_form.php` (~432 lines) — Employer Branding Form

**Role:** Form for submitting employer branding content.

---

### 3.36 `old/clubnew.php` — Legacy Club Page

**Role:** Deprecated. Legacy version of club landing.

---

### 3.37 `old/networking.php` — Legacy Networking

**Role:** Deprecated. Legacy networking UI.

---

### 3.38 `old/networking_footer.php` — Legacy Footer

**Role:** Deprecated. Legacy networking footer.

---

## 4. Dependencies & Includes

### PHP Includes (common across files)

| Include | Purpose |
|---------|---------|
| `includes/club_header.php` | Top navigation bar |
| `includes/club_room_data.php` | Room data helper functions |
| `includes/ads_data.php` | Ad placement data |
| `includes/error.php` | Error overlay |
| `NtwAdapterClub.php` | Room creation adapter |
| `core/form_fields.php` | Form field helpers (for networking_form.php) |

### JavaScript Libraries

| Library | Used In |
|---------|---------|
| jQuery | All pages |
| Bootstrap 4 | All pages |
| Summernote WYSIWYG | announcements.php, news_feed.php |
| Fancybox (lightbox) | announcements.php |
| Owl Carousel | club.php |
| jquery.twbsPagination | directory.php |

### External Services

| Service | Constant | Purpose |
|---------|----------|---------|
| Chat microservice | `TAOH_CHAT_NET_URL` | All networking room chat, channel CRUD, messages |
| CDN | `TAOH_CDN_PREFIX` | File/image uploads |
| Live Now | `TAOH_LIVE_NOW_URL` | Live session data |
| Google Meet | (via ajax.php) | Meet link creation |

---

## 5. Key Constants & Variables

### Platform Constants

| Constant | Purpose |
|----------|---------|
| `TAOH_CHAT_NET_URL` | Chat microservice base URL |
| `TAOH_OPS_CODE` | Operations/tenant code |
| `TAOH_CDN_PREFIX` | CDN base URL for uploads |
| `TAOH_LIVE_NOW_URL` | Live Now data endpoint |
| `TAOH_ADMIN_TOKENS` | CSV of admin token hashes |
| `TAOH_SITE_ROOT_HASH` | Base hash for slug generation |
| `TAOH_SITE_URL_ROOT` | Site root URL |
| `TAOH_PLUGIN_PATH` | Plugin directory path |
| `TAOH_CORE_PATH` | Core directory path |
| `TAOH_MY_NOW_CODE` | Current user's now code |

### Feature Toggles

| Constant | Purpose |
|----------|---------|
| `TAOH_CLUBS_ENABLE` | Enable/disable club module |
| `TAOH_EVENTS_ENABLE` | Enable/disable events |
| `TAOH_JOBS_ENABLE` | Enable/disable jobs |
| `TAOH_ASKS_ENABLE` | Enable/disable asks |
| `TAOH_ANNOUNCEMENT_ENABLE` | Toggle announcements tab |
| `TAOH_NEWS_ENABLE` | Toggle news tab |
| `TAOH_ENABLE_DM` | Enable/disable direct messaging |
| `TAOH_ENABLE_FOLLOW` | Enable/disable follow system |
| `TAOH_ENABLE_GROUPS` | Enable/disable groups |
| `TAOH_ENABLE_DIRECTORY` | Enable/disable member directory |
| `TAOH_ENABLE_CLASSIFIEDS` | Enable/disable classifieds |
| `TAOH_ENABLE_EMPLOYER_BRANDING` | Enable/disable employer branding |

### Display Constants

| Constant | Purpose |
|----------|---------|
| `TAOH_THEME_COLOR` | Primary theme color |
| `TAOH_CLUB_NAME` | Club display name |
| `TAOH_CLUB_LOGO` | Club logo URL |
| `TAOH_CLUB_BANNER` | Club banner URL |
| `TAOH_COMPANY_NAME` | Company name |
| `TAOH_COUNTRY_CODE` | Country code for geo filtering |
| `TAOH_TAG_CATEGORY` | Tag category for profile directory |
| `TAOH_DEFAULT_NETWORKING_VERSION` | Default networking UI version |

### Networking Constants

| Constant/Variable | Purpose |
|-------------------|---------|
| `NETWORKING_3_0` | Boolean: use v3.0 channel-based UI |
| `NETWORKING_VERSION` | Version selector: 1, 4, 5, 'mini', 'passive' |
| `TAOH_ROOM_KEY` | Current room key (defined in groups.php) |
| `TAOH_ROOM_LIVE` | Room live status (defined in groups.php) |
| `TAOH_CURR_APP_SLUG` | Always `'club'` in this module |

### Session Variables

| Variable | Purpose |
|----------|---------|
| `$_SESSION['ptoken']` | User authentication token |
| `$_SESSION['is_super_admin']` | Super admin flag |
| `$_SESSION['user_info']` | Cached user info object |
| `$_SESSION['country']` | User's country |
| `$_SESSION['company']` | User's company |
| `$_SESSION['title']` | User's job title |
| `$_SESSION['skills']` | User's skills array |

---

## 6. API Endpoints Used

| API Endpoint | Called From | Purpose |
|-------------|------------|---------|
| `users.directory.list` | ajax.php (`taoh_dir_users_list`) | Directory user listing |
| `events.user.rsvp` | club.php | Event RSVP data |
| `core.followup.get.list` | directory.php, connections.php | Follow/following list |
| `taoh_get_user_info` | profile.php, ajax.php | User profile data |
| `TAOH_CHAT_NET_URL/ops/rooms` | NtwAdapterClub.php | Room CRUD |
| `TAOH_CHAT_NET_URL/ops/channels` | NtwAdapterClub.php, ajax.php | Channel CRUD |
| `TAOH_CHAT_NET_URL/ops/messages` | ajax.php (ntw_* functions) | Message send/get/delete |
| `TAOH_CHAT_NET_URL/ops/speed-networking` | ajax.php (ntw_speed_*) | Speed networking ops |
| `TAOH_LIVE_NOW_URL` | loadLiveNowData.php | Live session data |
| Google Meet API | ajax.php (`taoh_create_google_meet_link`) | Meet link generation |

---

## 7. Integration Points

| Module | Integration |
|--------|-------------|
| **Authentication** | `$_SESSION['ptoken']` for all auth; login redirect when missing |
| **Direct Message** | DM key via `hash('crc32', 'dm-direct-message')`; connections.php, networking_footer*.php |
| **Events** | RSVP API in club.php; session channels in NtwAdapterClub |
| **Live Now** | `loadLiveNowData.php` proxy; `check_live_now_token()` in networking1/4 |
| **CDN/Upload** | File uploads to `TAOH_CDN_PREFIX/cache/upload/now` |
| **Chat Microservice** | All room/channel ops via `TAOH_CHAT_NET_URL` |
| **Follow System** | `core.followup.get.list` in directory + connections |
| **Admin System** | `TAOH_ADMIN_TOKENS` CSV + `is_super_admin` session flag |
| **Blog/Learning** | Cross-links from club navigation |
| **Community** | Shared header, shared user session |

---

## 8. Security Considerations

### Authentication
- All pages check `$_SESSION['ptoken']`; redirect to login if missing
- Admin actions gated by `TAOH_ADMIN_TOKENS` (CSV of hashes) + `is_super_admin`

### Known Issues
- **Lock code validation is client-side only** in `lobby.php` -- can be bypassed
- **`loadLiveNowData.php`** uses raw `file_get_contents()` -- no error handling, potential SSRF
- **`networking_3_0.php`** has `error_reporting(E_ALL)` -- exposes errors in production
- **localStorage-based like tracking** in announcements -- easily manipulated, no server-side dedup
- **No CSRF tokens** visible in AJAX calls
- **CDN uploads** lack visible file-type validation in the PHP layer
- **No rate limiting** on AJAX endpoints or file uploads

---

## 9. Quick-Reference Edit Guide

| Task | File(s) to Edit |
|------|-----------------|
| Add a new route/page | `main.php` (add case to switch) |
| Change nav tabs | `includes/club_header.php` |
| Add new AJAX action | `ajax.php` (add function, dispatch matches function name) |
| Modify room access rules | `lobby.php` (access control chain, steps 1-8) |
| Change room creation logic | `NtwAdapterClub.php` |
| Edit room data fetching | `includes/club_room_data.php` |
| Change announcement behavior | `announcements.php` |
| Update error messages | `includes/error.php` (codes 1002-1010) |
| Modify networking UI (current) | `networking5.php` + `networking_footer5.php` |
| Change directory pagination/search | `directory.php` |
| Update DM key logic | `connections.php`, `networking_footer*.php` |
| Add new channel type | `NtwAdapterClub.php` (add construct method) |
| Change ads | `includes/ads_data.php` |
| Add speed networking feature | `ajax.php` (taoh_ntw_speed_networking_* functions) |
| Modify v3.0 channel UI | `networking_3_0.php` |
| Change profile display | `profile.php` |
| Edit employer branding | `employer_branding.php` + `employer_branding_form.php` |

---

*Generated by deep analysis of all 39 PHP files in the club module.*
