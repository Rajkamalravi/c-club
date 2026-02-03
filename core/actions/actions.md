# Actions Module (`core/actions/`)

## Purpose
Central action router that handles all POST-based form submissions and server-side operations across the platform. Acts as the **command dispatcher** — receives an action slug from the URL and delegates to the appropriate handler file.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: Entry-point router for all actions. Parses URL segment 1 via `taoh_parse_url(1)` to determine which action handler to load.
- **Routing Table**:
  | URL Slug | Handler File |
  |----------|-------------|
  | `settings` | `settings_actions.php` (local) |
  | `contact` | `contact_actions.php` (local) |
  | `bulk` | `bulk_actions.php` (local) |
  | `flashcard` | `core/learning/flashcards/actions.php` |
  | `tips` | `core/learning/tips/actions.php` |
  | `blog` | `core/learning/blog/actions.php` |
  | `newsletter` | `core/learning/newsletter/actions.php` |
  | `candy` | `core/candy/action.php` |
  | `comments` | `core/widgets/comments/action.php` |
  | `feed_comments` | `core/widgets/feeds/action.php` |
  | `support` | `support_actions.php` (local) |
  | `feedback` | `feedback_actions.php` (local) |
  | `referral` | `referral_actions.php` (local) |
- **Fallback** (line 55-61): If the action matches an app in `taoh_available_apps()`, it loads `/app/{action}/actions/main.php`.
- **Key Function**: `taoh_parse_url(1)` — extracts URL segment.

---

### `settings_actions.php` (Profile/Settings Updates)
- **Role**: Handles ALL profile and settings form submissions. The largest and most complex action handler (~800 lines).
- **Auth**: Requires login (`taoh_user_is_logged_in()`), redirects to `TAOH_LOGIN_URL` if not.

#### Key Functions Defined:
| Function | Purpose | Lines |
|----------|---------|-------|
| `process_keywords_post()` | Extracts keyword fields from `$_POST` based on `TAOH_USER_KEYWORDS` constant, restructures into `$_POST['keywords']` | 6-19 |
| `set_profile_completion_flag($exist_data)` | Calculates `profile_stage` (0-3) based on completeness of fname/lname/coordinates (stage 1), company/skill/role (stage 2), tags_data (stage 3). Sets `$_POST['profile_stage']` and `$_POST['completed_profile_stages']`. | 21-59 |
| `update_profile_info($data)` | Calls API `users.tao.add` with cache invalidation pattern. Removes multiple cache keys: `profile_detail_`, `profile_short_`, `profile_info_`, `profile_cell_`, `profile_full_`, `profile_public_`, `users_*`. Uses `taoh_intaodb_common` store. | 62-94 |
| `profile_completion($post)` | Legacy function. Validates general tab fields (`fname`, `lname`, `type`, `email`, `title:title`, `company:company`, `full_location`, `local_timezone`) and public tab fields (`chat_name`, `skill:skill`, `aboutme`). Returns status codes 0/1/2. | 378-431 |

#### Action Handlers (dispatched by `$_POST['taoh_action']`):

1. **`basic_profile_update`** (lines 96-167):
   - Handles avatar upload via `taoh_remote_file_upload()` to `TAOH_CDN_PREFIX/cache/upload/now`
   - Validates profile completeness for fields: `fname`, `lname`, `email`, `type`, `country_code`, `coordinates`, `company:company`, `title:title`
   - Sets `profile_complete = 1` if valid
   - Auto-sets `chat_name` from `fname` if anonymous/empty
   - Returns JSON `{status: bool, data: object, error: string}`

2. **`general_profile_update` / `profile_tags_update` / `profile_update`** (lines 169-372):
   - `general_profile_update`: Processes keywords, sets chat_name, calls `set_profile_completion_flag()`
   - `profile_tags_update`: Merges new tags with existing, normalizes category keys to snake_case, JSON-encodes tag_data
   - Handles **employee** CRUD: Add/Edit via `emp_add`/`emp_edit` POST keys, Delete via `emp_delete`/`emp_btnDelete`. Fields: `title:title`, `current_role`, `emp_roletype`, `company:company`, `emp_coordinates`, `emp_full_location`, `emp_geohash`, `emp_placeType`, `skill:skill`, `emp_start_month`, `emp_year_start`, `emp_end_month`, `emp_year_end`, `emp_responsibilities`, `emp_profile_headline`, `emp_industry`
   - Handles **education** CRUD: Same pattern. Fields: `company:company`, `edu_degree`, `edu_specalize`, `edu_grade`, `edu_start_month`, `edu_start_year`, `edu_end_month`, `edu_complete_year`, `edu_activities`, `edu_description`, `skill:skill`
   - `profile_form_5`: Encodes `aboutme`, `funfact` via `taoh_title_desc_encode()`, JSON-encodes `hobbies`, trims `mylink`
   - `profile_form_6`: Converts `tao_unsubscribe_emails` to int, normalizes `unlist_me_dir` to yes/no

3. **`new_profile` / `old_profile`** (lines 433-595):
   - Legacy handler. Same employee/education CRUD logic (duplicated code).
   - Handles `about_btnSave`, `funfact_btnSave`, `hobbies_btnSave`, `mylink`, `tao_unsubscribe_emails`, `unlist_me_dir`
   - On profile creation: checks referral cookie `TAOH_ROOT_PATH_HASH.'_'.'refer_token'`, calls `core.refer.update` API
   - Redirects to `/apps?success=true` on create, `/settings` on update

4. **`taoh_session` handler** (lines 683-767):
   - `old` session: Full profile update with mandatory field check
   - Non-old session: AJAX-style, returns `1` or `0`
   - `first_update`: Calls `update_refer_for_profile_complete()`

5. **`step2` handler** (lines 769-800): Direct API call for step2 data

#### API Endpoints Called:
- `users.tao.add` — Primary profile update
- `core.refer.update` — Referral tracking on profile complete

#### Cache Invalidation Pattern:
Keys removed on profile update: `profile_detail_{ptoken}`, `profile_short_{token}`, `profile_info_{ptoken}`, `profile_cell_{ptoken}`, `profile_full_{ptoken}`, `profile_public_{ptoken}`, `*_networking_cell_{ptoken}`, `users_*`

---

### `contact_actions.php`
- **Role**: Handles "Contact Us" form submissions.
- **Auth**: If not logged in, redirects to `mailto:info@tao.ai` with subject from `$_GET['q']`.
- **API**: Calls `users.contact.reach` with POST data (`subject`, `message`).
- **POST fields**: `we_subject`, `we_category`, `we_message`, `we_locn`
- **On success**: Sets flash message "Email Sent!", redirects to `TAOH_ABOUT_URL`.

---

### `bulk_actions.php`
- **Role**: Bulk content import via file upload (CSV or PHP/JSON).
- **Upload dir**: `TAOH_PLUGIN_PATH/cache/bulk/`
- **Two import modes**:
  1. **PHP/JSON files** (`.php` extension): Parses JSON, iterates entries, POSTs each to `https://api.tao.ai/scripts/addblurb.php`
  2. **CSV files**: Parses CSV with `fgetcsv()`, row 1 = headers, subsequent rows = data. Same API call.
- **Fields per entry**: `title`, `category`, `pod`, `body`, `excerpt`, `visiblity`, `blog_type`, `subtitle`, `media_link`, `media_url`, `media_type`, `source_name`, `source_url`, `via_name`, `via_url`
- **POST params**: `type` (reads/flash), `global` (0/1)
- **SSL**: `verify_peer` and `verify_peer_name` both disabled.

---

### `feedback_actions.php`
- **Status**: **NOT IN USE** (comment on line 2: "this file not in use - we can delete it")
- Calls `core.content.post` API with `type: feedback`, `ops: add`, `mod: tao_tao`.

---

### `referral_actions.php`
- **Role**: Processes referral form submissions.
- **API**: `core.referral.put` with `mod: users`
- **POST fields**: Entire `$_POST` array sent as `toenter`
- **On success**: Flash "Referral request for {EMAIL} added!", redirect to `TAOH_REFERRAL_URL`.

---

### `support_actions.php`
- **Role**: Handles support ticket submissions.
- **Data flow**:
  1. Reads cache file at `TAOH_PLUGIN_PATH/cache/logs/{TAOH_API_SECRET}.cache`
  2. Extracts last 20 URLs from user's browsing history (filtered by `TAOH_API_TOKEN`)
  3. Stores `$_POST`, `$_GET`, `$_SESSION`, `$_COOKIE` to Redis via `TAOH_CACHE_CHAT_PROC_URL` with key `support_{ptoken}_{time}` and 48hr TTL
  4. Sends support email via `core.post` API with `type: support`, `ops: send`
- **On success**: Flash "Thank you for contacting us. We will respond within 48 hours."

---

### `index.php`
- **Role**: Security guard file — contains `// silence is golden`. Prevents directory listing.

---

## Architecture & Data Flow
```
URL: /actions/{slug}
      |
      v
  main.php
      |
      +-- taoh_parse_url(1) extracts slug
      |
      +-- Routes to handler file
              |
              +-- Handler processes $_POST
              +-- Calls TAOH API (taoh_apicall_post / taoh_apicall_get)
              +-- Invalidates cache keys
              +-- Sets flash message (taoh_set_success_message)
              +-- Redirects user (taoh_redirect)
```

## Dependencies
- `taoh_parse_url()`, `taoh_user_is_logged_in()`, `taoh_get_dummy_token()`, `taoh_get_api_token()`
- `taoh_apicall_post()`, `taoh_apicall_get()`, `taoh_apicall_post_debug()`
- `taoh_redirect()`, `taoh_exit()`, `taoh_set_success_message()`, `taoh_set_error_message()`
- `taoh_session_save()`, `taoh_session_get()`, `taoh_delete_local_cache()`
- `taoh_remote_file_upload()`, `taoh_title_desc_encode()`
- `taoh_user_all_info()`, `taoh_available_apps()`
- Constants: `TAOH_PLUGIN_PATH`, `TAOH_API_PREFIX`, `TAOH_CDN_PREFIX`, `TAOH_API_SECRET`, `TAOH_API_TOKEN`, `TAOH_ROOT_PATH_HASH`, `TAOH_LOGIN_URL`, `TAOH_ABOUT_URL`, `TAOH_REFERRAL_URL`, `TAOH_SITE_URL_ROOT`, `TAOH_CACHE_CHAT_PROC_URL`

## Key Edit Points
- **Add new action route**: Edit `main.php`, add `else if($action == "newslug")` block
- **Modify profile update fields**: Edit `settings_actions.php`, find the relevant `$_POST['taoh_action']` block
- **Change employee/education fields**: Edit field mapping arrays in `settings_actions.php` (~lines 228-245 for employee, ~lines 270-282 for education)
- **Modify cache invalidation**: Edit `$remove` arrays in `settings_actions.php` or `update_profile_info()`
