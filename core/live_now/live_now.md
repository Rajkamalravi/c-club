# Live Now Module (`core/live_now/`)

## Purpose
Manages the "Live Now" feature — fetches live event data from an external URL, creates/joins a networking room for the live session, and redirects the user into it. Also provides a JSON data endpoint.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: URL router for `/live` or `/live_now` paths.
- **URL Parsing**: `taoh_parse_url(1)` = slug, `taoh_parse_url(2)` = page.
- **Defines**: `TAOH_CURR_APP_SLUG = 'club'`
- **Routing**:
  | Slug | Handler |
  |------|---------|
  | `live_now_data` | `live_now/live_now_data.php` (JSON API) |
  | `networking` | `live_now/adapter.php` |
  | `live` / default | Club networking pages (same version dispatch as `direct_message`) |

---

### `adapter.php` (Live Room Creator & Redirector)
- **Role**: Fetches live event data, creates networking room, redirects user.
- **Auth**: Requires login.
- **Requires**: `NtwAdapterLive.php` from `TAOH_CORE_PATH`.

#### Flow:
1. Fetches live data from `TAOH_LIVE_NOW_URL?y={YmdH}` (hourly cache-bust)
2. Parses JSON response, extracts `output` if `success: true`
3. Instantiates `NtwAdapterLive`
4. Calls `$liveNtwAdapter->generateRoomSlug({country_code, country_name, local_timezone, country_locked: 0, title})`
5. Calls `$liveNtwAdapter->constructAndCreateRoomInfo($user_info, {roomslug, live_now_data, country_locked: 0})`
6. On success: sets `room['keyword'] = "live"`, calls `createBulkRoomInfoChannels()`
7. Redirects to: `TAOH_SITE_URL_ROOT/club/room/{slugified_title}-{roomslug}`
8. Falls back to home on failure

#### Key Difference from DM:
- Room slug is **generated dynamically** based on user's country/timezone (not static hash)
- Calls `createBulkRoomInfoChannels()` to set up channels in the room
- Live data is fetched from external `TAOH_LIVE_NOW_URL`

---

### `adapter_bk.php` (Backup)
- **Role**: Identical to `adapter.php` but **without** `createBulkRoomInfoChannels()` call and without setting `room['keyword'] = "live"`. This is a backup/previous version.

---

### `live_now_data.php` (JSON API Endpoint)
- **Role**: Returns live event data as JSON. No auth required.
- **Flow**:
  1. Fetches `TAOH_LIVE_NOW_URL?y={YmdH}`
  2. Returns JSON:
     - Success: `{success: true, live_now_data: {...}, join_now_url: TAOH_SITE_URL_ROOT/live/networking}`
     - Failure: `{success: false, live_now_data: [], join_now_url: ''}`
- **Content-Type**: `application/json`
- **Used by**: Frontend JS to check if live session is active and get join URL.

---

## Dependencies
- `NtwAdapterLive` class (from `core/NtwAdapterLive.php`)
- `taoh_user_is_logged_in()`, `taoh_user_all_info()`, `taoh_slugify()`
- Constants: `TAOH_LIVE_NOW_URL`, `TAOH_SITE_URL_ROOT`, `TAOH_CORE_PATH`, `TAOH_PLUGIN_PATH`

## Integration Points
- Fetches live event data from `TAOH_LIVE_NOW_URL` (external service)
- Creates rooms via `NtwAdapterLive->constructAndCreateRoomInfo()`
- Redirects to `club/room/` pages
- `live_now_data.php` called by frontend JS widgets

## Key Edit Points
- **Change live data source**: Modify `TAOH_LIVE_NOW_URL` constant definition
- **Change room creation params**: Edit `adapter.php` `constructAndCreateRoomInfo()` call
- **Change JSON API response format**: Edit `live_now_data.php`
- **Modify room slug generation**: Edit `generateRoomSlug()` params in `adapter.php`
