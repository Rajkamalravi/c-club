# Asks App — Comprehensive Codebase Reference

## Directory Structure

```
asks/
├── index.php                      # Security guard (silence is golden)
├── main.php                       # Router/Entry point (~100 lines)
├── functions.php                  # Core API helpers (~51 lines)
├── asks.php                       # Main listing page (~965 lines)
├── asks_detail.php                # Ask detail page (~237 lines)
├── asks_detail_content.php        # Detail content template (~174 lines)
├── asks_mobile.php                # Mobile view (empty)
├── ask_common_js.php              # Shared JS functionality (~182 lines)
├── visitor.php                    # Public landing page (~729 lines)
├── chat.php                       # Skill/Role chat rooms (~168 lines)
├── club.php                       # Club/networking rooms (~297 lines)
├── about.php                      # About page (~50 lines)
├── search_new.php                 # Search/filter form UI
├── ajax.php                       # AJAX endpoints (~204 lines)
└── actions/
    └── main.php                   # Action handlers (disabled, ~30 lines)
```

---

## File-by-File Reference

### main.php — Router

**Constants defined:**
- `TAOH_APP_SLUG = 'asks'`
- `TAOH_CURR_APP_SLUG = 'asks'`
- `TAOH_CURR_APP_URL = TAOH_SITE_URL_ROOT . '/asks'`
- `TAOH_ASKS_ASK_GET = TAOH_API_PREFIX . "/asks.ask.get"`
- `TAOH_ASKS_URL = TAOH_SITE_URL_ROOT . "/asks"`
- `TAOH_CURR_APP_IMAGE`, `TAOH_CURR_APP_IMAGE_SQUARE` (CDN paths)

**Route switch on URL action:**
| Case | Target |
|------|--------|
| `d` | Ask detail page or main listing |
| `club` | Club/networking rooms |
| `chat` | Chat/skill rooms |
| `mobile` | Mobile view |
| `dash` | User dashboard redirect |
| `post` | Ask creation redirect |
| `about` | About page |
| `asks_new` | New design asks page |
| default | Main listing (logged in) or visitor page |

**Includes:** `functions.php`, core `form_fields.php`
**Guards:** Checks `TAOH_ASKS_ENABLE` feature flag

---

### functions.php — API Helpers

**`taoh_get_info($conttoken, $type = 'content')`**
- Fetches ask info via `asks.ask.get`
- Type mapping: `rolechat`→`title`, `skillchat`→`skill`, `orgchat`→`company`
- Cache key: `asks.ask_{conttoken}_{type}`

**`taoh_chat_clubkey_post($taoh_id, $current_app, $conttype, $request_type)`**
- Creates club room via `chat.club.post`
- Recipe: `type:{conttype}::q:{taoh_id}::user:n`

---

### ajax.php — AJAX Endpoints

| Function | API Call | Purpose |
|----------|----------|---------|
| `asks_get_rooms()` | `/api/find.php` | Get chat rooms by type (rolechat/skillchat/orgchat) |
| `asks_get()` | `asks.get` | Paginated ask listing with filters |
| `ask_like_put()` | `content.save` | Save/like an ask |
| `ask_comment_put()` | `taoh_cacheops('metricspush')` | Track comment click metrics |
| `taoh_apply_job_ask()` | `core.refer.put` | Create referral for non-logged user |
| `asks_get_detail()` | includes `asks_detail_content.php` | Get ask detail HTML via AJAX |

**`asks_get()` parameters:**
- `ops` (default 'list'), `geohash`, `search`, `limit` (default 10), `offset`, `postDate`, `from_date`, `to_date`, `filter_type`
- Uses `TAOH_API_SECRET` or `TAOH_API_DUMMY_SECRET`
- `filter_type='saved'` disables caching

**`ask_like_put()`:**
- Invalidates cache: `asks_*` and `ask_{conttoken}`
- Params: `slug='asks'`, `conttoken`, `redis_store='taoh_intaodb_asks'`

**`taoh_apply_job_ask()`:**
- Sets cookies: `refer_token`, `referral_back_url` (1 day TTL)
- Sends referral email with subject "Answer for the ask {title}"

---

### asks.php — Main Listing Page (Authenticated)

**Key flow:**
1. Fetches user liked items via `system.users.metrics` (slug='asks')
2. Displays tab navigation: Asks | Saved Asks | Dashboard | Create
3. Two-column layout: results + sidebar (recent jobs widget, just ask widget)
4. Dynamic content loading with `.loaderArea`, `.result_div`, pagination
5. Hero banner only for non-logged-in users

**Key variables:** `$current_app`, `$app_data`, `$taoh_user_vars`, `$ptoken`, `$log_nolog_token`

---

### asks_detail.php — Detail Page

**Key flow:**
1. Extracts `$conttoken` from URL slug
2. Fetches user metrics for liked status
3. Sets page meta: `TAO_PAGE_TITLE`, `TAO_PAGE_DESCRIPTION`, `TAO_PAGE_IMAGE`, `TAO_PAGE_ROBOT='index, follow'`
4. Two-column: main content (via `asks_detail_content.php`) + sidebar
5. Share modal with `taoh_share_widget()`

**Sidebar widgets:** User profile, invite friends, ask info (location/type), just ask CTA

**JS features:** Comment loading with scroll-to, like check, localStorage, IndexedDB cache clear

---

### asks_detail_content.php — Detail Content Template

**API call:** `asks.ask.get` with `ops='info'`, cache key `ask_details_{conttoken}`

**Rendered sections:**
1. Title + like/share icons + date + location/company tags
2. Skills via `newgenerateSkillHTML($ask['meta']['skill'])`
3. Full description (decoded HTML)
4. Comments/answers via `taoh_comments_widget(conttoken, 'ask', redirect, 'Answer')`

**Key variables:** `$conttoken`, `$ptoken`, `$from` ('detail'|'listing'), `$ask_title`, `$ask_description`, `$ask_company`, `$ask_location`, `$owner_ptoken`, `$share_link`, `$answer_btn`

**HTML helpers used:** `newgenerateCompanyHTML()`, `newgenerateLocationHTML()`, `newgenerateSkillHTML()`, `taoh_comments_widget()`, `taoh_share_widget()`

---

### ask_common_js.php — Shared JavaScript

**Global JS vars:** `profile_complete`, `isLoggedIn`, `conttoken`, `askResponse`, `app_slug`, `ptoken`, `liked_arr`

**Event handlers:**
| Selector | Action |
|----------|--------|
| `.click_action` | Track metrics (comment_click, network_click), route to section |
| `.create_referral` | AJAX `taoh_apply_job_ask`, set cookies, show signup modal |
| `.ask_save` | AJAX `ask_like_put`, update bookmark UI, set localStorage, clear IndexedDB |
| `.share_box` | Build social share URLs (FB, Twitter, LinkedIn, Email), show modal |
| `.post_answer` | Check login + profile completion, scroll to comments |

**Utility functions:**
- `get_liked_check(conttoken)` — returns filled/empty bookmark SVG, checks localStorage `{app_slug}_{conttoken}_liked`
- `delete_asks_into()` — clears IndexedDB ASKStore keys containing 'ask'

**Modals:** `#config-modal`, `#exampleModal1`, `showBasicSettingsModal()`

---

### visitor.php — Public Landing Page

**Meta:** Title, description, keywords (job-related SEO terms)

**Sections:**
1. Hero banner — "Welcome to Asks app, Where Questions Ignite Progress" + 2 CTAs
2. Stats — 5M+ visitors, 2M+ career events, 1M+ connections, 10K+ recruiters, 150+ communities
3. About — "A Conduit to Career Enlightenment" + 3-step how-it-works
4. Value props — Solve Career Conundrums, Cultivate Credibility, Collective Growth
5. Available Asks — dynamic list with pagination + signup CTA

---

### chat.php — Skill/Role Chat Room

**Function:** `asks_skill_room($app_temp, $contslug)`
- Requires login (redirects if not)
- Types: `skillchat`, `rolechat`, `orgchat`
- Fetches room metadata via `taoh_get_info()`
- Creates club via `taoh_chat_clubkey_post()`
- Stores user participation via `taoh_networking_postcell()`
- Captures: ptoken, chat_name, avatar, location, timezone, profile_type

---

### club.php — Club/Networking Room

- Requires login (redirects to detail if not)
- Fetches ask via `asks.ask.get` (ops='info', cache 7200s)
- Gets owner profile via `taoh_get_user_info(ptoken, 'public')`
- Builds club data structure with title, owner, description, members, guidelines
- Guidelines include rules about respect, recruiter communication, personal info

---

### search_new.php — Search Form

**Form ID:** `#searchFilter`, submit triggers `searchFilter()`

**Fields:**
- Search input `#query` — placeholder "Ask Title, Company Name"
- Location filter via `field_location()` — hidden on mobile
- Date range `#postdate` — Today, Yesterday, Last Week, Last Month, Date Range
- Conditional from/to date inputs when "Date Range" selected
- Clear buttons for search and location

---

### about.php — About Page

- Conditional CTA: "Checkout Settings" (logged in) or "Login/Sign Up" (not)
- Fetches FAQ from `TAOH_CDN_PREFIX . "/app/asks/faq.php"` (JSON)
- Displays: app name, short desc, full desc, thumbnail, video

---

### actions/main.php — Action Handlers (DISABLED)

**`add_answer()`** (dead code, dies at line 4):
- Would POST to `asks.ask.post` with `ops='answer'`
- Would redirect to ask detail on success

---

## API Endpoints

| Endpoint | Method | Purpose | Key Params |
|----------|--------|---------|------------|
| `asks.ask.get` | GET | Single ask details | token, conttoken, ops, mod |
| `asks.get` | GET | List with filters | token, geohash, search, limit, offset, postDate, from_date, to_date, filter_type |
| `asks.ask.post` | POST | Post answer | token, toenter, ops='answer' |
| `content.save` | POST | Like/save | token, conttoken, slug='asks' |
| `system.users.metrics` | GET | User's liked items | token, slug='asks', mod |
| `chat.club.post` | POST | Create club room | token, app, q (recipe) |
| `core.refer.put` | POST | Create referral | token, ops='invite', toenter |
| `/api/find.php` | GET | Search rooms | mod, type, term, misc, maxr |
| `taoh_cacheops('metricspush')` | POST | Track metrics | encoded JSON array |

---

## Caching Strategy

| Layer | Mechanism | Details |
|-------|-----------|---------|
| Server-side | TTL-based API cache | `cfcc5h=1` flag, `cache_required`, `cache_name` |
| Client localStorage | Quick state checks | `{app_slug}_{conttoken}_liked` |
| Client IndexedDB | Offline storage | ObjectStore: ASKStore |
| Cache invalidation | On mutations | `ask_like_put` clears `asks_*`, `ask_{conttoken}` |

---

## Data Flow

```
User Action → JS Handler (ask_common_js.php)
  → AJAX POST (ajax.php endpoint)
    → Backend API call (functions.php helpers)
      → JSON Response
        → JS DOM Update + Cache Update
```

**Ask lifecycle:** Create (via /asks/post redirect) → List (asks.get) → Detail (asks.ask.get ops=info) → Interact (like/comment/share/answer) → Network (chat/club rooms)

**User paths:**
- Visitor: `visitor.php` → detail → referral → signup modal
- Authenticated: `asks.php` → detail → like/save/answer/share/comment

---

## Key Global Dependencies

```php
// URLs & Paths
TAOH_SITE_URL_ROOT, TAOH_API_PREFIX, TAOH_CDN_PREFIX, TAOH_PCDN_PREFIX

// Auth & Security
TAOH_API_TOKEN, TAOH_API_TOKEN_DUMMY, TAOH_API_SECRET, TAOH_API_DUMMY_SECRET
TAOH_ROOT_PATH_HASH

// Feature Flags
TAOH_ASKS_ENABLE, TAOH_ASKS_GET_LOCAL, TAOH_SOCIAL_LIKES_THRESHOLD

// Global Functions
taoh_get_dummy_token(), taoh_apicall_get(), taoh_apicall_post()
taoh_user_is_logged_in(), taoh_session_get(), taoh_sanitizeInput()
taoh_parse_url(), taoh_p2us(), taoh_app_info()
taoh_get_user_info(), taoh_networking_getcell(), taoh_networking_postcell()
taoh_comments_widget(), taoh_share_widget(), taoh_jusask_widget()
taoh_get_recent_jobs(), taoh_cacheops(), taoh_site_ajax_url()
newgenerateSkillHTML(), newgenerateCompanyHTML(), newgenerateLocationHTML()
field_location(), showBasicSettingsModal()
```

---

## Architectural Patterns

1. **MVC-like:** Router (`main.php`) → Views (template files) → Models (API calls in `functions.php`/`ajax.php`)
2. **WordPress plugin conventions:** Global `taoh_*` helpers, plugin path structure
3. **AJAX-first UI:** Dynamic content loading, no full page reloads for interactions
4. **Token auth:** `ptoken` for users, dummy tokens for anonymous, API secrets for backend
5. **Widget system:** Reusable `taoh_*_widget()` components with conditional rendering
6. **Multi-layer cache:** Server TTL + localStorage + IndexedDB with invalidation on writes
