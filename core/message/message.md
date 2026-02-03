# Message Module (`core/message/`)

## Purpose
Full-featured 1-to-1 messaging system with real-time chat, video calling, offline messaging, user profile sidebar, IndexedDB caching, and read/unread status tracking. This is the central messaging hub of the platform.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: Entry-point router for `/message` paths.
- **Defines**: `TAOH_CURR_APP_SLUG = TAOH_MESSAGEPAGE_NAME`
- **URL Parsing**: `taoh_parse_url(1)` = slug, `taoh_parse_url(2)` = keyslug
- **Routing**:
  | Slug | Handler |
  |------|---------|
  | `ajax` / `message_ajax` | `message_ajax.php` |
  | `dm` (with keyslug) | `club/networking1.php` |
  | `dm` (no keyslug) | `club.php` |
  | default | `message.php` |

---

### `message.php` (Main Chat UI — ~1700 lines)
- **Role**: Renders the full messaging interface and contains all client-side chat logic.
- **Auth**: Requires `profile_complete` flag; redirects to `/settings` if incomplete.
- **Deep-link support**: URL `/message/chatwith/{room_hash}-{ptoken}` auto-opens a specific conversation.

#### PHP Setup (lines 1-25):
- Extracts `messaging_chat_arr` from URL for deep-linking
- Defines `THIS_PAGE_URL` and `THIS_PAGE_AJAX_NAME`
- Calls `taoh_get_header()`

#### HTML Layout (3-column):
1. **Left Panel** (`mm_left_section`): Participants list with search, read-all toggle
2. **Center Panel** (`mm_center_section`): Chat messages, input area, video call button, offline message fallback
3. **Right Panel** (`mm_right_section`): User profile info

#### Client-Side JavaScript Architecture:

**Key Variables:**
- `my_pToken` — Current user's ptoken
- `mm_room_key` — Current active room hash
- `chatwith` — Ptoken of user being chatted with
- `chatwith_liveStatus` — 0/1 whether other user is online
- `currentRoomMsgList` — All room message entries
- `mmuserInfoList` / `mmroomInfoList` — Cached user/room info

**Core Functions:**

| Function | Purpose |
|----------|---------|
| `getUserInfo(pToken, ops, serverFetch)` | Gets user info from: local var → global var → IndexedDB → server AJAX. Caches with 2-day TTL. |
| `getRoomInfo(room_hash, ptoken, data, serverFetch)` | Gets room info from: local var → global var → IndexedDB → server. 5-min TTL. Auto-creates room if not found. |
| `fetchMMRoomsData(requestData)` | Fetches room list from IndexedDB (`mm_rooms_{ptoken}`) |
| `processMMRoomsData(requestData, response)` | Merges new rooms into `currentRoomMsgList`, deduplicates |
| `renderMMRoomsData(roomMsgList)` | Renders left panel room list with avatars, last message, time ago, read status. Supports search filtering. |
| `fetchMMChatData(requestData)` | Fetches chat messages from IndexedDB (`cm_{room}_{from}_{to}`) |
| `processMMChatData(requestData, response)` | Paginates messages, handles init/scrollup/interval modes |
| `renderMMMessages(response)` | Renders chat bubbles, handles temp messages, scroll behavior, new message indicators |
| `sendMMChat(message, my_pToken, chatwith, mm_room_key, user_type)` | Sends message: renders optimistically, stores temp in IndexedDB, POSTs to server, handles offline |
| `updateMMChatSendArea(mm_room_key, chatwith)` | Toggles between live chat input and offline "Send Message" button |
| `updateChatWindowHeader(chatwith)` | Updates chat header with user name, avatar, online status |
| `updateUserProfileInfo(chatwith)` | Loads right panel profile via AJAX to `message_ajax.php` |
| `userLiveStatusUpdate(interval)` | Polls user live status; adapts interval: 1min (active), 3min (away), 5min (tab hidden) |
| `mm_makeAllRead()` | Marks all rooms as read in IndexedDB |

**AJAX Actions Used:**
- `taoh_add_video_chat` — Creates video chat link
- `taoh_room_send_message` — Sends chat message
- `taoh_post_message` — Sends offline message
- `taoh_user_info` — Fetches user info
- Room info: `ops: status`, `status: getroom`

**Polling Intervals:**
- Room list: every 5 seconds
- Chat messages: every 3 seconds (when active)
- User live status: 1 min (active) / 3 min (away) / 5 min (tab hidden)

**IndexedDB Keys:**
- `mm_rooms_{ptoken}` — Room entries list
- `cm_{room}_{from}_{to}` — Chat messages
- `cm_temp_{room}_{ptoken}` — Temp (unsent) messages
- `user_info_list` — Cached user profiles
- `room_info_list` — Cached room info

---

### `message_ajax.php` (AJAX Handler)
- **Role**: Handles AJAX calls specific to the message module.
- **Dispatch**: Checks `$_REQUEST['taoh_action']`, calls matching function if it exists.
- **WordPress compatibility**: Registers WP AJAX hooks if in WP context.
- **Defined Functions**:
  - `taoh_user_profile_details()` — Decodes `$_POST['user_data']` JSON, includes `profile_data.php` template

---

### `club.php` (DM Room Creator)
- **Role**: Creates/joins DM room for messaging, then redirects to the room URL.
- **Auth**: Requires login.
- **Flow**:
  1. Generates room slug: `hash('crc32', 'dm-direct-message')`
  2. Calls `create_dm_room($keyslug, $ptoken)`
  3. Gets/creates networking cell via `taoh_networking_getcell()` / `taoh_networking_postcell()`
  4. Redirects to room URL, appending `?chatwith={ptoken}` if provided
- **Note**: Code after `taoh_exit()` (line 75) is dead code (legacy room creation logic).

---

### `profile_data.php` (Profile Template)
- **Role**: Renders user profile card for the right sidebar panel (loaded via AJAX).
- **Input**: `$user_data` array (from JSON decode in `message_ajax.php`)
- **Displays**: Avatar, chat_name, type, location, About, Fun Fact, Skills, About Profile Type, Experience, Education
- **Privacy**: If not logged in, truncates about/funfact/about_type to 100 chars
- **Employee rendering**: Parses `title:title` and `company:company` fields using `:>` delimiter format, displays roletype, date range, location, responsibilities
- **Education rendering**: Same `:>` parsing, shows degree, specialization, grade, activities, description

---

## Architecture
```
URL: /message/{slug}
       |
       v
   main.php (router)
       |
       +-- message.php (full chat UI)
       |       |-- JS polls rooms every 5s
       |       |-- JS polls chat every 3s
       |       |-- IndexedDB for offline + caching
       |       |-- AJAX → message_ajax.php (profile)
       |       |-- AJAX → main ajax.php (send/receive)
       |
       +-- message_ajax.php → profile_data.php
       |
       +-- club.php (DM room setup → redirect)
```

## Dependencies
- `taoh_get_header()`, `taoh_get_footer()`, `taoh_user_all_info()`
- `taoh_networking_getcell()`, `taoh_networking_postcell()`, `create_dm_room()`
- `taoh_site_ajax_url()`, `taoh_site_message_ajax_url()`
- IndexedDB library: `IntaoDB`, `objStores.ntw_store`, `objStores.common_store`
- Constants: `TAOH_MESSAGEPAGE_NAME`, `TAOH_ROOT_PATH_HASH`, `TAOH_SITE_URL_ROOT`, `TAOH_OPS_PREFIX`

## Key Edit Points
- **Modify chat UI layout**: Edit HTML in `message.php` (lines 267-410)
- **Change polling intervals**: Edit `setInterval` calls in `message.php` JS (room: ~line 641, chat: ~line 1537)
- **Add new AJAX handler**: Add function in `message_ajax.php`
- **Modify profile sidebar**: Edit `profile_data.php`
- **Change message send logic**: Edit `sendMMChat()` JS function (~line 1099)
- **Change offline behavior**: Edit `updateMMChatSendArea()` and `messages_offline_send_message_btn` handler
