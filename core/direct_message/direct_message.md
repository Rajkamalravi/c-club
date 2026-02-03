# Direct Message Module (`core/direct_message/`)

## Purpose
Handles the Direct Message (DM) feature by creating/joining a DM networking room and redirecting the user to it. Integrates with the networking room infrastructure via `NtwAdapterDM`.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: URL router for the `/dm` or `/direct_message` path.
- **URL Parsing**: `taoh_parse_url(1)` = slug, `taoh_parse_url(2)` = page.
- **Defines**: `TAOH_CURR_APP_SLUG = 'club'`
- **Adds**: `noca` URL parameter with `TAOH_MY_NOW_CODE` (unless in AJAX context).
- **Routing**:
  | Slug | Handler |
  |------|---------|
  | `networking` | `direct_message/adapter.php` |
  | `dm` / default | Delegates to club networking pages based on `NETWORKING_VERSION` constant |

- **Networking version dispatch** (default/dm case):
  - `NETWORKING_3_0` true → `club/networking_3_0.php`
  - `NETWORKING_VERSION == 1` → `club/networking1.php`
  - `NETWORKING_VERSION == 4` → `club/networking4.php`
  - `NETWORKING_VERSION == 5` → `club/networking5.php`
  - `NETWORKING_VERSION == 'mini'` → `club/networking5_kal.php`
  - `NETWORKING_VERSION == 'passive'` → `club/networking5_passive.php`
  - Fallback → `club/networking1.php`

---

### `adapter.php` (DM Room Creator & Redirector)
- **Role**: Creates a DM room and redirects user into it.
- **Auth**: Requires login, redirects to `TAOH_SITE_URL_ROOT` if not.
- **Requires**: `NtwAdapterDM.php` from `TAOH_CORE_PATH`.

#### Flow:
1. Creates DM room data: `title: 'Direct Message'`, `description: 'Direct Message'`
2. Generates room slug: `hash('crc32', 'dm-direct-message')`
3. Instantiates `NtwAdapterDM`
4. Calls `$dmNtwAdapter->constructAndCreateRoomInfo($user_info_obj, {roomslug, dm_data, country_locked: 0})`
5. On success: sets `room['keyword'] = "dm"`, extracts `keyslug`
6. Builds redirect URL: `TAOH_SITE_URL_ROOT/club/room/{slugified_title}-{roomslug}`
7. Appends `?chatwithchannelid={channel_id}` if `$_GET['channel_id']` present
8. Redirects to room URL (or home on failure)

#### Key Variables:
- `$dm_room_slug` — CRC32 hash of 'dm-direct-message'
- `$room_info` — API response containing room data
- `$roomslug` — Final room key slug for URL

---

## Dependencies
- `NtwAdapterDM` class (from `core/NtwAdapterDM.php`)
- `taoh_user_is_logged_in()`, `taoh_user_all_info()`, `taoh_slugify()`
- `taoh_parse_url()`, `taoh_redirect()`, `taoh_exit()`, `taoh_add_var_to_url()`
- Constants: `TAOH_CORE_PATH`, `TAOH_PLUGIN_PATH`, `TAOH_SITE_URL_ROOT`, `TAOH_CURR_APP_SLUG`, `TAOH_MY_NOW_CODE`, `NETWORKING_VERSION`, `NETWORKING_3_0`

## Integration Points
- Creates rooms via `NtwAdapterDM->constructAndCreateRoomInfo()`
- Redirects to `club/room/` pages (handled by the `club` module)
- Supports deep-linking to specific channels via `channel_id` GET param

## Key Edit Points
- **Change DM room slug**: Edit `adapter.php` line 16, modify the hash input string
- **Change room creation params**: Edit the `constructAndCreateRoomInfo()` call in `adapter.php`
- **Add new routing slug**: Edit `main.php` switch statement
