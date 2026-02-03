# Jobs Module — Comprehensive Codebase Reference

> **Purpose**: Instant-reference doc for surgical code edits. Every file, function, variable, API call, DOM element, and data flow documented.

---

## 1. DIRECTORY STRUCTURE

```
jobs/
├── main.php                  # Router — all URL dispatch logic
├── functions.php             # 2 helper functions (API wrappers)
├── apply_form_fields.php     # 6 form field generator functions
├── actions/
│   └── main.php              # POST handlers: add_comments(), apply_job()
├── jobs.php                  # Main listing page (logged-in + guest, 1236 lines)
├── jobs_detail.php           # Single job detail page (434 lines)
├── jobs_mobile.php           # Mobile job listing + detail (766 lines)
├── visitor.php               # Landing page for non-logged users (586 lines)
├── scout.php                 # Scout program landing page (462 lines)
├── club.php                  # Job networking — creates club room + redirects (308 lines)
├── chat.php                  # Chat redirect — gets clubkey + redirects (49 lines)
├── chat_new.php              # Newer chat interface
├── search.php                # Search/filter form partial (79 lines)
├── common_job_details.php    # Reusable job detail display component (259 lines)
├── job_tabs.php              # Navigation tabs partial (43 lines)
├── jobs_common_js.php        # Shared JS: save, apply, share, modal handlers (483 lines)
├── jobs_new.php              # Legacy — similar to jobs.php
├── ajax.php                  # AJAX dispatcher (light wrapper)
└── index.php                 # "Silence is golden"
```

---

## 2. ROUTER — main.php (lines 1-158)

**Entry guard**: Redirects to site root if `TAOH_JOBS_ENABLE` is false (line 4).

**Constants defined**:
```php
TAOH_APP_SLUG        = 'jobs'           // line 9 (if not already defined)
TAOH_CURR_APP_SLUG   = 'jobs'           // line 11
TAOH_CURR_APP_URL    = TAOH_SITE_URL_ROOT.'/jobs'  // line 12
TAOH_CURR_APP_IMAGE  = CDN + '/app/jobs/images/jobs.png'  // line 13
TAOH_CURR_APP_IMAGE_SQUARE = CDN + '/app/jobs/images/jobs_sq.png'  // line 14
TAOH_JOBS_URL        = TAOH_SITE_URL_ROOT.'/jobs'  // line 18
```

**URL parsing** (lines 22-36):
```php
$current_app = taoh_parse_url(0);   // 'jobs'
$action      = taoh_parse_url(1);   // route segment
$goto        = taoh_parse_url(2);   // sub-segment (combined with [3] if exists)
$id          = '/'.taoh_parse_url(4); // optional deeper ID
// Special: if $goto == 'stlo', set $goto = ''
```

**Includes** (lines 38-40): `functions.php`, `apply_form_fields.php`

**Switch routes** (line 43 onward):

| `$action` | Target File | Auth | Redirect Pattern |
|---|---|---|---|
| `d` | `jobs_detail.php` OR `jobs.php` (if `$_GET['q']=='main'`) | No | Direct include |
| `mobile` | `jobs_mobile.php` | No | Direct include |
| `scout` | `scout.php` | No | Direct include |
| `club` | `club.php` | No | Direct include |
| `about` | `visitor.php` | No | Direct include |
| `dash` | — | No | `/fwd/ss/{HASH}/log/1/u/loc/jobs` |
| `post` | — | No | `/fwd/ss/{HASH}/log/1/u/loc/jobs/post` |
| `professional-dashboard` | — | No | `/fwd/ss/{HASH}/log/1/u/loc/jobs/professional-dashboard/{goto}{id}` |
| `scouts` | — | No | `/fwd/ss/{HASH}/log/1/u/loc/dashboard` |
| `scout-dashboard` | — | Yes/No | Logged: `.../scout-dashboard/{goto}{id}`, Not: `.../scouts-signup` |
| `employer-dashboard` | — | Yes/No | Logged: `.../employer-dashboard/{goto}{id}`, Not: `.../scouts-signup-employer` |
| `scouts-signup-employer` | — | Yes/No | `/fwd/ss/{HASH}/log/{0|1}/u/loc/jobs/scouts-signup-employer` |
| `scouts-signup-professional` | — | Yes/No | Same pattern with `-professional` |
| `scouts-signup` | — | Yes/No | Same pattern |
| `new_design` | `_design.php` | No | Direct include |
| **default** `chat` | `chat.php` | **Yes** (redirects to login if not) | Direct include |
| **default** `chat1` | `chat_new.php` | **Yes** | Direct include |
| **default** other | `jobs.php` | No (always shows, `|| 1` on line 148) | Direct include |

**Note**: Line 148 has `taoh_user_is_logged_in() || 1` — visitor.php is effectively dead code for default route.

---

## 3. FUNCTIONS — functions.php (lines 1-56)

### `taoh_get_info($conttoken, $type='content')`
- **Validates**: `ctype_alnum($conttoken)` — redirects if invalid
- **API call**: `jobs.job.get` (GET)
- **Parameters**:
  ```php
  'mod' => 'jobs',
  'token' => taoh_get_dummy_token(1),
  'conttoken' => $conttoken  // when $type == 'content'
  // OR
  'title' => $conttoken, 'type' => $type2  // when $type != 'content'
  ```
- **Type mapping**: `rolechat` → `title`, `skillchat` → `skill`, `orgchat` → `company`
- **Cache key**: `taoh_p2us('jobs.job').'_'.$conttoken.'_'.$type`
- **Returns**: `json_decode($return, true)` — associative array

### `taoh_chat_clubkey_post($taoh_id, $current_app, $conttype, $request_type)`
- **API call**: `chat.club.post` (POST)
- **Recipe format**: `"type:$conttype::q:$taoh_id::user:n"`
- **Parameters**: `mod=jobs`, `token`, `app=$current_app`, `q=$recipe`
- **Returns**: JSON decoded array

---

## 4. FORM FIELDS — apply_form_fields.php (lines 1-60)

All return HTML strings. Used in apply modals across `jobs.php`, `jobs_detail.php`, `jobs_mobile.php`.

| Function | HTML Output | Key Attributes |
|---|---|---|
| `field_location($coords, $loc, $geohash, $js, $required)` | `<select id="locationSelect">` + hidden inputs for `full_location`, `geohash` | Calls `locationSelect()` JS |
| `field_job_location(...)` | `<select id="joblocationSelect">` + same hiddens | Calls `joblocationSelect()` JS |
| `field_company($options)` | `<select id="companySelect" name="company:company[]">` | Calls `companySelect()` JS, uses `explode(':>', $value)` for options |
| `field_fname($option)` | `<input name="fname" class="form-control form--control">` | — |
| `field_lname($option)` | `<input name="lname" class="form-control form--control">` | — |
| `field_email($option)` | `<input name="email" required class="form-control form--control">` | — |

---

## 5. ACTIONS — actions/main.php (lines 1-68)

**Dispatch** (lines 5-9):
```php
if($_POST['action'] == "addcomments") → add_comments()
elseif($_POST['ops'] == "apply")      → apply_job()
```

### `add_comments()`
- **Auth**: Must be logged in
- **API**: `jobs.job.post` (POST)
- **Params**: `mod=asks`, `token`, `toenter=$_POST`, `ops=answer`
- **Response**: Sets success message, redirects to `/jobs/d/{$_POST['slug']}`

### `apply_job()`
- **Auth**: Must be logged in (checked at line 55)
- **Default API**: `jobs.put.apply` (POST)
- **Scout override** (lines 47-52): If `$_POST['enable_scout_apply'] == 1`:
  - API changes to `jobs.scout.job.post`
  - Adds `type=applicant`, `conttoken=$_POST['conttoken']`
- **Params**: `mod=jobs`, `token`, `toenter=$_POST`, `ops=$_POST['ops']`
- **Response**: Returns raw JSON via `echo $results; die;`

---

## 6. JOBS.PHP — Main Listing Page (lines 1-1236)

### PHP Setup (lines 1-111)

**SEO** (lines 4-7):
```php
TAO_PAGE_TITLE       = "Comprehensive Open Jobs List at {SITE}..."
TAO_PAGE_DESCRIPTION = "Browse our comprehensive jobs list..."
TAO_PAGE_KEYWORDS    = "Job openings at {SITE}..."
TAO_PAGE_ROBOT       = 'noindex, nofollow'
```

**User data** (lines 13-29):
```php
$current_app    = taoh_parse_url(0)
$app_data       = taoh_app_info($current_app)
$taoh_user_vars = $data = taoh_user_all_info()
$profile_complete = $data->profile_complete ?? null
$ptoken         = $data->ptoken ?? null
$share_link     = full current URL
$log_nolog_token = logged_in ? $ptoken : TAOH_API_TOKEN_DUMMY
```

**Metrics fetch** (lines 31-46):
```php
// API: system.users.metrics (GET)
// Params: mod=system, token, slug=TAO_PAGE_TYPE, cache_name, ttl=3600
$liked_arr = json_encode($get_liked['conttoken_liked'])  // or ''
```

**Session init** (lines 49-103) — Always resets these session arrays:
```php
$_SESSION[HASH.'_eligible_scouted_jobs'] = []  // from jobs.scout.list
$_SESSION[HASH.'_scouted_jobs']          = []  // from jobs.applied.job → output.scout
$_SESSION[HASH.'_applied_jobs']          = []  // from jobs.applied.job → output.jobs
```

If logged in:
1. **API `jobs.scout.list`** (GET): `mod=jobs, ptoken, secret=TAOH_API_SECRET, cache_required=0`
   - Stores `$data_res['output']` → `_eligible_scouted_jobs` (array of conttokens)
2. **API `jobs.applied.job`** (GET): `mod=jobs, ptoken, token, secret, cache_required=0`
   - Maps `output.scout` → `{conttoken: apply_id}` → `_scouted_jobs`
   - Maps `output.jobs` → `{conttoken: apply_id}` → `_applied_jobs`

**Currency** (line 107): `$currencies = json_encode(taoh_get_currency_symbol('',true))`

### HTML Structure (lines 112-581)

**Layout overview**:
```
.mobile-app
├── Hero section (only if NOT logged in, lines 167-258)
│   ├── Left: PROFESSIONAL CTA → "Sign up today" (modal login)
│   ├── Right: EMPLOYER CTA → "Find your next Hire" (modal login)
│   └── Stats bar: 5M+ visitors, 2M+ events, 1M+ connections, 10K+ recruiters, 150+ communities
│
├── <header> sticky-top (lines 261-328)
│   ├── Tab nav (#myTab):
│   │   ├── "All Jobs" (active, onclick: get_job_type())
│   │   ├── "Applied Jobs" (logged-in only, onclick: get_job_type('applied'))
│   │   ├── "Saved Jobs" (logged-in only, onclick: get_job_type('saved'))
│   │   ├── job_tabs.php includes (Scout tabs)
│   │   ├── "My Jobs" link → /jobs/dash (logged-in, blue)
│   │   └── "+Post Job" link → /jobs/post (logged-in, blue)
│   │       OR "Login/Signup" button (not logged-in)
│   └── search.php include (search form)
│
├── <section> main content (lines 330-380)
│   └── .container .sticky-container
│       └── .tab-content
│           └── .tab-pane #home
│               ├── .col-lg-3: #joblistArea + #pagination
│               ├── .col-lg-6: .detail_tab.sticky-detail (job preview panel)
│               ├── .no_result_div (hidden by default)
│               └── .col-lg-3: sidebar widgets
│                   ├── taoh_get_recent_jobs('new')
│                   ├── taoh_jusask_widget('new') (if TAOH_ENABLE_JUSASK)
│                   └── taoh_readables_widget('new') (if TAOH_LEARNING_WIDGET_ENABLE)
│
├── Apply Modal #myModal (lines 381-564)
│   └── .modal-xl → form#fileUploadForm
│       ├── Hidden fields: ops=apply, slug, to_email, recruiter_fname, opscode, ptoken, company_name, position_title, conttoken
│       ├── Personal Details: fname, lname
│       ├── Contact & Experience: email, location (joblocationSelect), company
│       ├── Resume Upload: file input → uploads to CDN_PREFIX/cache/upload/now
│       ├── Cover Letter: .summernote WYSIWYG
│       ├── Links: linkedin_url, github_url, port_url, web_url
│       ├── Additional Info: addntl_info textarea
│       ├── Right column: sticky card showing company_name, position_title, placeType, description (truncated)
│       └── Submit button + enable_scout_apply hidden field
│
└── Share Modal #exampleModal1 (lines 565-581)
    └── .modal-sm → #share_icon (populated by JS)
```

### JavaScript (lines 584-1233)

**Global variables** (lines 586-633):
```javascript
isLoggedIn        // from PHP
loaderArea        = $('#listloaderArea')
joblistArea       = $('#joblistArea')
locationSelectInput = $('#locationSelect')
geohashInput      = $('#geohash')
currentMod        // app_data->slug
geohash = "", search = ""
postDate, from_date, to_date  // from form inputs
currentPage = 1, totalItems = 0, itemsPerPage = 10
like_min, comment_min, share_min  // threshold constants
liked_arr         // JSON string from PHP
det_slot          = $('.detail_tab')
job_type = ''     // '', 'applied', or 'saved'
applied_jobs      // JSON object {conttoken: apply_id}
scouted_jobs      // JSON object {conttoken: apply_id}
eligible_scouted_jobs  // JSON array of conttokens
profile_complete  // from PHP
currencies        // full currency array from PHP
store_name = JOBStore  // IndexedDB store name
```

**Key JS functions**:

| Function | Lines | Purpose |
|---|---|---|
| `get_job_type(type)` | 680-709 | Switches tabs: '', 'applied', 'saved'. Resets search/page/filters. |
| `searchFilter()` | 756-776 | Reads form, sets search/geohash, calls getjoblistdata or taoh_jobs_init |
| `getjoblistdata(queryString, job_type)` | 778-832 | IndexedDB-first: checks cache by CRC32 hash key, renders from cache or falls back to API |
| `taoh_jobs_init(queryString, job_type)` | 887-940 | AJAX POST to `taoh_site_ajax_url()` with `taoh_action=jobs_get` |
| `render_jobs_template(data, slot, job_type)` | 942-1128 | Renders job cards into #joblistArea. Handles no-results, apply button logic, scout indicators |
| `job_detail_execute(conttoken)` | 1184-1207 | AJAX POST `taoh_action=jobs_get_detail` → renders into `.detail_tab` |
| `show_pagination(holder)` | 834-861 | Pagination.js init with 10 items/page |
| `clearBtn(type)` | 863-885 | Clears search or location filter |
| `updateCanonical(url, job_url)` | 1132-1170 | Updates `<link rel="canonical">` and pushes URL state |
| `indx_jobs_list(data)` | 1212-1223 | Stores job list in IndexedDB with 30-min TTL |
| `getCurrencySymbol(index)` | 747-753 | Looks up currency symbol from PHP-provided array |

**IndexedDB caching flow** (when `TAOH_INTAODB_ENABLE`):
```
1. getjoblistdata() called
2. Hash key = crc32(search + geohash + queryString + page + limit + dates + job_type)
3. Check JOBStore for 'jobs_' + hash
4. HIT → render from cache, skip API
5. MISS → call taoh_jobs_init() → API → indx_jobs_list() stores result
6. TTL check every 30 seconds via setInterval → checkTTL()
```

**Apply button rendering logic** (inside `render_jobs_template`, lines 1010-1065):

```
IF logged in:
  IF profile_complete == 0:
    → "Apply Now" with class .profile_incomplete (triggers settings modal)
  ELSE IF job owner != current user:
    IF enable_scout_job == 'on':
      IF conttoken in scouted_jobs:
        → "Applied! View Application Status" (click_action: view_application)
      ELSE IF conttoken in eligible_scouted_jobs:
        → "Apply through Scout (referred)" (orange, click_action: apply_through_scout_link)
      ELSE:
        → "Apply through Scout" (orange, click_action: request_through_scout_link)
    ELSE (non-scout):
      IF conttoken in applied_jobs:
        → "Applied!" (success class)
      ELSE IF apply_link exists:
        → "Apply Now" → opens external link in new tab
      ELSE IF email exists AND enable_apply:
        → "Apply Now" with class .open_modal (opens apply form modal)
      ELSE:
        → "Apply Now" → mailto:email
ELSE (not logged in):
  → "Apply" with class .create_referral
```

**Job listing click** (line 1172-1181):
- Adds `.active` class to clicked row
- Calls `job_detail_execute(conttoken)`
- Calls `save_metrics('jobs','click',conttoken)`
- Updates canonical URL via `updateCanonical()`

**Job list AJAX request params** (taoh_jobs_init, lines 910-923):
```javascript
{
  taoh_action: 'jobs_get',
  ops: 'list',
  search: search,       // text query
  geohash: geohash,     // location hash
  offset: currentPage - 1,
  limit: itemsPerPage,  // 10
  postDate: postDate,   // today|yesterday|last_week|last_month|date_range
  from_date, to_date,   // date range values
  filter_type: job_type, // ''|'applied'|'saved'
  ptoken: ptoken,
  filters: queryString   // serialized form
}
```

---

## 7. JOBS_DETAIL.PHP — Single Job Page (lines 1-434)

### PHP Setup (lines 1-111)

**URL parsing** (lines 6-10):
```php
$taoh_url_vars = taoh_parse_url(2);  // slug-conttoken
$conttoken = array_pop(explode('-', $taoh_url_vars));
// Validates: length 10-20, alphanumeric → redirects to /404 if invalid
```

**API call** (lines 16-38): `jobs.job.get` (GET)
- Params: `token(1), ops=info, mod=jobs, cache_name='job_details_'+conttoken, cache_time=7200, conttoken`
- Uses `TAOH_API_PREFIX` with raw response flag

**SEO** (lines 49-78):
```php
TAO_PAGE_DESCRIPTION = strip_tags(job description)
TAO_PAGE_IMAGE       = $response['image']
TAO_PAGE_TITLE       = ucfirst(decoded title)
TAO_PAGE_ROBOT       = 'index, follow'  // NOTE: indexed! Unlike jobs.php
TAO_PAGE_CANONICAL   = canonical from user_site_info.source if different from current site
```

**Metrics** (lines 93-105): Same `system.users.metrics` call as jobs.php.

**Click/view detection** (line 13):
```php
$click_view = isset($_SERVER['HTTP_REFERER']) ? 'click' : 'view';
```

### HTML Structure (lines 112-431)

```
<section> jobs-area
├── .col-lg-9: common_job_details.php (with $from = 'detail')
│   └── Comments section (#scroll_show, hidden by default)
│       └── taoh_comments_widget(conttoken, conttype=jobs, label=Comment)
├── .col-lg-3: sidebar
│   ├── jobs_networking_widget()
│   └── taoh_invite_friends_widget()
├── Share Modal #exampleModal1 → taoh_share_widget()
└── Apply Modal #myModal (identical structure to jobs.php modal)
```

### JavaScript (lines 361-431)

```javascript
conttoken          // from PHP
enable_scout_apply // from PHP (0 for detail page)
liked_arr          // from PHP

$(document).ready:
  - save_metrics('jobs', click_view, conttoken) // if logged in
  - get_liked_check(conttoken) → renders bookmark icon

// Comment scroll handler
$('.comment_go').click → shows #scroll_show, scrolls to #scroll_id
// Auto-scroll if $_GET['comments'] or $_GET['apply_by_form']
```

---

## 8. COMMON_JOB_DETAILS.PHP — Reusable Detail Component (lines 1-259)

**Context variable**: `$from` — set by caller (`'detail'` or `'listing'`)

### PHP Logic (lines 1-199)

**API call** (lines 11-29): `jobs.job.get` (GET) — same as jobs_detail.php
- Uses `cfcc1d=1` param (CF cache flag)

**Job data extraction** (lines 31-94):
```php
$job_title, $job_description, $job_description_modal
$job_company (array), $job_company_name (first company title)
$job_location, $job_created, $job_placeType, $job_roltype
$job_scouted = $job['meta']['enable_scout_job']
$apply_link  = $job['meta']['apply_link']
$apply_email = $job['meta']['email']
$enable_apply = $job['meta']['enable_apply']
$owner_ptoken = $job['ptoken']
$share_link  = full URL to job detail page
```

**Payment term mapping** (lines 60-87):
```
monthly  → ' per month'  / ' paid on per Month basis'
hourly   → ' per hour'   / ' paid on per Hour basis'
annualy  → ' per year'   / ' paid on per Year basis'
weekly   → ' per week'   / ' paid on per Week basis'
daily    → ' per Daily'  / ' paid on per Day basis'
project  → ' per project' / ' paid on per Project basis'
```

**Session-based scout/apply check** (lines 95-177):
- If not in session, fetches `jobs.scout.list` and `jobs.applied.job` APIs
- Apply button logic: same branching as jobs.php JS but server-side PHP

**Apply button states** (PHP-rendered, lines 145-187):
```
Logged in + profile incomplete → .profile_incomplete class
Logged in + scout ON + already scouted → "Applied! View Application Status"
Logged in + scout ON + eligible → "Apply through Scout(Referred)" (orange)
Logged in + scout ON + not eligible → "Apply through Scout" (orange)
Logged in + non-scout + already applied → "Applied!"
Logged in + non-scout + apply_link → external link
Logged in + non-scout + email + enable_apply → .open_modal
Logged in + non-scout + email only → mailto:
Not logged in → .create_referral → "Signup Here to Apply"
```

### HTML Output (lines 200-258)

```html
.light-dark-card.right-detail-tab.from_{detail|listing}.desktop-job-list
├── .sticky
│   ├── Back button (only when $from == 'detail')
│   ├── Apply button (only when $from == 'detail', float right)
│   ├── Job title + bookmark icon (.like_render) + share icon (.share_box) + scout logo
│   ├── Posted date (taohFullyearConvert)
│   ├── Company | Location | Role Type | Job Type + Payment info
│   └── Apply button (only when $from == 'listing')
├── Skills section: newgenerateSkillHTML($job['meta']['skill'])
├── Job Description section: decoded HTML
├── Payment Details + Application Deadline (row)
├── Apply button again (when $from == 'listing')
└── Hidden: #hideconttoken = conttoken
```

---

## 9. JOB_TABS.PHP — Nav Tabs Partial (lines 1-43)

**Input**: `$page_sel` variable (set by caller: `'jobs'` or `'scout'`)

**Logic**:
```
IF $page_sel != "jobs":
  Show "All Jobs" tab → /jobs/
  IF TAOH_SCOUT_ENABLE: Show "Scout" tab (blue bold) → /jobs/scout

IF $page_sel == "jobs" AND TAOH_SCOUT_ENABLE:
  IF logged in:
    IF status_scout_employer >= 1: "#EmployerScouts" → /jobs/scouts-signup-employer
    IF status_scout_professional == 2: "ScoutMembers" → /jobs/scouts-signup-professional
    IF status_scout == 2: "Scouts" → /jobs/scouts-signup
  Always: "About Scout" (blue bold) → /jobs/scout/
```

---

## 10. SEARCH.PHP — Filter Form Partial (lines 1-79)

**Profile incomplete check** (lines 6-14): If `profile_complete == 0`, calls `showBasicSettingsModal()` JS.

**Form**: `#searchFilter`, `onsubmit="searchFilter();return false"`

| Field | ID/Name | Type | Notes |
|---|---|---|---|
| Search query | `#query` name=`query` | text | Placeholder: "Job Title, Company Name" |
| Location | `#locationSelect` | select | Uses `field_location()`, hidden on mobile |
| Post date | `#postdate` name=`post_date` | select | Options: today, yesterday, last_week, last_month, date_range |
| From date | `#from_date` | date | Shown only when "Date Range" selected |
| To date | `#to_date` | date | Shown only when "Date Range" selected |
| Search button | — | submit | Rounded, "Search" text + icon |

**Clear badges**: `#searchClear`, `#locationClear` — hidden by default, shown when filter active.

---

## 11. JOBS_COMMON_JS.PHP — Shared JavaScript (lines 1-483)

**PHP preamble** (lines 1-16): Gets user data, defines `$ptoken`, `$log_nolog_token`.

**Global JS vars set**:
```javascript
_taoh_site_url_root  // from TAOH_SITE_URL_ROOT
app_slug             // from TAO_PAGE_TYPE
```

### Functions and Event Handlers

#### `delete_jobs_into()` (lines 21-41)
- Opens IndexedDB `taoh_intaodb`
- Iterates `JOBStore`, deletes any key containing `'job'`

#### `.create_referral` click (lines 43-82)
- For non-logged users clicking apply
- Posts to AJAX: `taoh_action=taoh_apply_job_referral`, `from_link`, `detail_link`, `job_title`
- Sets cookie `{HASH}_referral_back_url` = current URL (1 day)
- Clears localStorage email
- Shows `#config-modal` (login modal)

#### `.click_action` click (lines 85-128)
- Reads `data-action` attribute from button
- Calls `save_metrics('jobs', metrics, conttoken)`
- Action routing:
  - `apply_click` → opens `apply_link` in new tab
  - `apply_through_scout_link` → navigates to `/jobs/professional-dashboard/{url}/apply/`
  - `view_application` → navigates to `/jobs/professional-dashboard/{url}/view_application/{apply_id}`
  - `request_through_scout_link` → navigates to `/jobs/professional-dashboard/{url}/request_to_refer/`
  - `scout_dashboard` → `/jobs/scout-dashboard/`
  - `employer_dashboard` → `/jobs/employer-dashboard/`

#### `get_liked_check(conttoken)` (lines 130-155)
- Checks `liked_arr` (from PHP) AND `localStorage` key `{app_slug}_{conttoken}_liked`
- Returns SVG bookmark icon (filled if liked, outline if not)
- Filled: class `.already-saved`
- Unfilled: class `.job_save` with `data-cont` attribute

#### `.job_save` click (lines 157-182)
- Swaps icon to filled, removes `.job_save` class → `.already-saved`
- Sets `localStorage` key
- Calls `delete_jobs_into()` (bust cache)
- Calls `save_metrics('jobs','like',savetoken)`
- AJAX POST: `taoh_action=job_like_put`, `conttoken`, `ptoken`

#### `truncateHTML(html, maxLength)` (lines 185-226)
- DOM-based HTML truncation preserving tags
- Adds "..." at cutoff

#### `decodeHTMLEntities(str)` (lines 228-231)
- Uses DOMParser for entity decoding

#### `.open_modal` click (lines 234-279)
- Reads data attributes from button: conttoken, position, company, toemail, fname, description
- Populates modal form hidden fields and display areas
- Decodes and truncates description to 200 chars
- Shows `.apply_modal` modal

#### `.toggle-link` click (lines 282-294)
- Toggles between full/short description in modal

#### `#fileToUpload` change (lines 297-349)
- Validates: max 5MB, allowed types: pdf, doc, docx
- Uploads via `fetch()` to `TAOH_CDN_PREFIX/cache/upload/now`
- On success: sets `.resume_link` hidden input to returned URL
- Shows success/error in `#responseMessage`

#### `#fileUploadForm` submit (lines 351-392)
- Prevents default, validates form via jQuery Validate
- Disables submit, shows spinner
- Gets Summernote cover letter content
- Calls `save_metrics('jobs','applyform',conttoken)`
- Calls `delete_jobs_into()`
- AJAX POST to `TAOH_ACTION_URL/jobs?uslo=2` with serialized form + cover_letter
- On success: hides modal, shows success message, reloads page

#### jQuery Validate rules (lines 394-428):
```javascript
fname: required
lname: required
email: required + email format
coordinates: required
fileToUpload: required + extension(pdf,doc,docx) + filesize(5MB)
```

#### `.share_box` click (lines 434-472)
- Reads title, ptoken, share link, conttoken from data attrs
- Builds social share URLs: Facebook, Twitter, LinkedIn, Email
- Populates `#share_icon` with social buttons + copy URL
- Shows `#exampleModal1`

#### `.profile_incomplete` click (lines 474-482)
- Calls `showBasicSettingsModal()` if exists

---

## 12. VISITOR.PHP — Non-Logged Landing (lines 1-586)

**SEO**: Same as jobs.php. Uses `taoh_get_header()`.

**Page sections**:
```
Hero: Professional CTA (left) + Employer CTA (right) — modal login buttons
Stats: 5M+ / 2M+ / 1M+ / 10K+ / 150+
About: "Your Career Breakthrough Awaits" + "How Does Jobs app Work?" (3 steps)
Features: Valued Connections | Exclusive Insights | Informed Decisions (blue bg)
Job Listings: .jobs-list populated by AJAX, with pagination
Apply/Post CTAs: "Apply With Us" (dark) + "Post a Job" (light)
Testimonials: Owl Carousel — Fedex, Amazon, Walmart, Staples, Apple, UPS
Blog Articles: AJAX fetches articles_get (category=jobs)
Join Section: Employers, Professionals, Partners (hidden)
```

**JS functions**:
- `taoh_jobs_init()` — simplified version, no IndexedDB, no job_type filter
- `taoh_articles_init()` — fetches `taoh_action=articles_get, ops=front, type=reads, mod=core, category=jobs`
- `render_jobs_template(data, slot)` — simple job cards (no apply buttons, just links)
- `render_articles_grid_template(data, slot)` — blog article cards

---

## 13. SCOUT.PHP — Scout Landing (lines 1-462)

**Uses**: `taoh_get_header()`, includes `job_tabs.php` with `$page_sel = "scout"`.

**Company logos**: Randomizes 10 from logos 1-30 (top row) and 10 from logos 31-60 (bottom row) using CSS animation classes `.itemLeft`, `.itemRight`.

**Page sections** (all static content, no AJAX):
```
Header tabs (sticky)
Hero (non-logged only): "Get the Best Talent Through Expert Professionals"
Stats bar (non-logged only)
For Employers box (blue bg #D8E0F9): "Hire 6X Faster" → /jobs/scouts-signup-employer
For Professionals (yellow #F7EE9E) + For Scouts (green #A8EAC9CC) side by side
Company logos carousel
"How does this work?" tabbed section:
  - Employers: Signup → Scout Effect → Find your hire
  - Professionals: Signup → Scout Effect → Apply and get hired
  - Scouts: Signup → Refer with ease → Earn rewards
Impact stats: 88%, 55%, 30-50%
Why Choose Us: Empowerment, Community, Mission-Driven
```

---

## 14. CLUB.PHP — Job Networking Room (lines 1-308)

**Auth required**: Redirects non-logged users to job detail page.

**Flow**:
1. Parse conttoken from URL slug
2. Check for `$_GET['key']` — if exists, use as keyslug
3. Otherwise: fetch job via `jobs.job.get` API, derive keyslug from `hash('crc32', contslug + country)`
4. `get_room_info($keyslug, ptoken, ['app' => 'job'])` — check existing room
5. If room exists: use it. Otherwise create full room structure with:
   - Club info: title, description, image, links, profile_types (employer/professional/provider)
   - Skills, company, roles from job meta
   - Sponsors: NoWorkerLeftBehind + TAO.ai
   - 13 FAQ entries about networking page
   - Breadcrumbs: Home → Jobs → Job Title
   - geo_enable=false, owner_enable=true
6. Create cell info for current user
7. `create_room_info()` + `taoh_networking_postcell()`
8. Redirect logic:
   - If current user IS room owner → `/club/room/{slug}-{keyslug}`
   - If current user is NOT owner → `/club/room/{slug}-{keyslug}/chatwith/{owner_ptoken}?from=owner&with={chat_name}`
   - Fallback → back to job detail page

---

## 15. CHAT.PHP — Chat Redirect (lines 1-49)

**URL parsing**:
```php
$request_type = taoh_parse_url(2)  // e.g., 'skillchat'
$current_app  = taoh_parse_url(3)  // type parameter
$taoh_id      = taoh_parse_url(4)  // key
$slug         = taoh_parse_url(5)
$title        = taoh_parse_url(6)  // or slug if no title
```

**API**: `jobs.chat.get` (GET) — params: `mod=jobs, token, key=$taoh_id, type=$current_app`

**Retry loop**: While no clubkey, tries up to `$tried >= 1` (max 2 attempts). Checks `status == 'redirect'`.

**Redirect**: `/fwd/club/{clubkey}` or fallback to `/jobs/d/{taoh_id}`

---

## 16. JOBS_MOBILE.PHP — Mobile View (lines 1-766)

**Header**: Uses `taoh_get_header_mobile()` instead of regular header.

**Unique features vs jobs.php**:
- Fetches single job detail server-side (from conttoken in URL)
- Hardcoded job detail example in HTML (looks like dev/test content)
- Has `taoh_taoh_room_get_member_active_chat_init()` for active chat list
- Uses heart icons for likes (not bookmarks)
- Location change triggers geohash lookup via AJAX `taoh_get_geohash`
- Uses `format_object(data).items` (not `.output.list`)
- Has legacy metrics display (likes count, share count visible)
- Calls `taoh_cacheops('metricspush', ...)` server-side for view tracking

---

## 17. ALL API ENDPOINTS REFERENCE

| API Call | Method | Used In | Purpose |
|---|---|---|---|
| `jobs.get` | POST (AJAX) | jobs.php, visitor.php, jobs_mobile.php | Fetch job listings |
| `jobs.job.get` | GET | functions.php, common_job_details.php, jobs_detail.php, club.php, jobs_mobile.php | Single job details |
| `jobs.scout.list` | GET | jobs.php, common_job_details.php | Get eligible scouted jobs for user |
| `jobs.applied.job` | GET | jobs.php, common_job_details.php | Get user's applied + scouted jobs |
| `jobs.job.post` | POST | actions/main.php | Post comments (mod=asks, ops=answer) |
| `jobs.put.apply` | POST | actions/main.php | Submit job application |
| `jobs.scout.job.post` | POST | actions/main.php | Scout-based application (type=applicant) |
| `jobs.chat.get` | GET | chat.php | Get chat room clubkey |
| `chat.club.post` | POST | functions.php | Create chat club room |
| `system.users.metrics` | GET | jobs.php, jobs_detail.php | Get liked conttokens |
| `content.save` | POST (AJAX) | jobs_common_js.php | Save/like job (taoh_action=job_like_put) |
| `core.refer.put` | POST (AJAX) | jobs_common_js.php | Create referral (taoh_action=taoh_apply_job_referral) |
| `articles_get` | POST (AJAX) | visitor.php | Fetch blog articles |
| `get_member_active_chat` | POST (AJAX) | jobs_mobile.php | Get active chat rooms |
| `taoh_get_geohash` | POST (AJAX) | jobs_mobile.php | Convert lat/lon to geohash |

---

## 18. AJAX ACTION NAMES (taoh_action values)

| taoh_action | File | Purpose |
|---|---|---|
| `jobs_get` | jobs.php, visitor.php, jobs_mobile.php | Fetch job list |
| `jobs_get_detail` | jobs.php | Fetch single job detail HTML |
| `job_like_put` | jobs_common_js.php | Save/like a job |
| `taoh_apply_job_referral` | jobs_common_js.php | Create referral for non-logged users |
| `articles_get` | visitor.php | Fetch blog articles |
| `get_member_active_chat` | jobs_mobile.php | Active chat rooms |
| `taoh_get_geohash` | jobs_mobile.php | Geohash lookup |

---

## 19. DOM ELEMENT QUICK REFERENCE

### Key IDs
| ID | File | Purpose |
|---|---|---|
| `#joblistArea` | jobs.php | Job listing container |
| `#pagination` | jobs.php, visitor.php | Pagination widget |
| `#listloaderArea` | jobs.php | Loader spinner area |
| `#myModal` / `.apply_modal` | jobs.php, jobs_detail.php | Apply form modal |
| `#exampleModal1` | jobs.php, jobs_detail.php | Share modal |
| `#fileUploadForm` | jobs_common_js.php | Apply form |
| `#fileToUpload` | Modal | Resume file input |
| `#responseMessage` | Modal | Upload status message |
| `#searchFilter` | search.php | Search form |
| `#query` | search.php | Search text input |
| `#locationSelect` | search.php | Location dropdown |
| `#geohash` | search.php | Hidden geohash input |
| `#postdate` | search.php | Date filter dropdown |
| `#dateRangeInputs` | search.php | Date range container (hidden by default) |
| `#share_icon` | jobs_common_js.php | Share icons container |
| `#hideconttoken` | common_job_details.php | Hidden conttoken value |
| `#config-modal` | External | Login/signup modal |
| `#scroll_show` | jobs_detail.php | Comments section (hidden) |
| `#enable_scout_apply` | Modal | Hidden scout apply flag |

### Key CSS Classes
| Class | Purpose |
|---|---|
| `.job-listing-block-row` | Job card in listing (clickable) |
| `.detail_tab` / `.sticky-detail` | Job detail preview panel |
| `.open_modal` | Triggers apply modal |
| `.click_action` | Triggers action routing (apply, scout, view) |
| `.create_referral` | Non-logged apply button |
| `.job_save` | Unsaved bookmark icon |
| `.already-saved` | Saved bookmark icon |
| `.share_box` | Share button |
| `.profile_incomplete` | Incomplete profile apply button |
| `.light-dark-card` | Card component (supports dark mode) |
| `.no_result_div` | No results container |
| `.result_div` | Results container |
| `.mobile-app` | Main wrapper |
| `.desktop-job-list` | Desktop-only elements |
| `.mobile-job-list` | Mobile-only elements |
| `.mod_conntoken` | Hidden input for conttoken in modal |
| `.resume_link` | Hidden input for uploaded resume URL |
| `.summernote` | Cover letter WYSIWYG editor |
| `.submit` | Form submit button |

---

## 20. SESSION & STORAGE MAP

### PHP Session Keys
```php
$_SESSION[TAOH_ROOT_PATH_HASH.'_eligible_scouted_jobs']  // array of conttokens
$_SESSION[TAOH_ROOT_PATH_HASH.'_scouted_jobs']           // {conttoken: apply_id}
$_SESSION[TAOH_ROOT_PATH_HASH.'_applied_jobs']           // {conttoken: apply_id}
```

### JavaScript localStorage Keys
```javascript
`${app_slug}_${conttoken}_liked`     // '1' if job saved
`email`                              // cleared on referral creation
`Status_${conttoken}`                // temporary apply success flag
`isCodeSent`                         // login flow state
```

### IndexedDB
```
Database: taoh_intaodb
├── JOBStore: { taoh_data: 'jobs_{crc32hash}', values: {API response} }
└── taoh_ttl:  { taoh_ttl: 'jobs_{crc32hash}', time: timestamp+30min }
```

### Cookies
```
{HASH}_referral_back_url = current URL (1 day expiry, set on referral creation)
```

---

## 21. KEY CONSTANTS REFERENCE

| Constant | Where Defined | Purpose |
|---|---|---|
| `TAOH_JOBS_ENABLE` | External config | Feature flag for entire module |
| `TAOH_SCOUT_ENABLE` | External config | Scout program feature flag |
| `TAOH_INTAODB_ENABLE` | External config | IndexedDB caching flag |
| `TAOH_SITE_URL_ROOT` | External config | Base site URL |
| `TAOH_CDN_PREFIX` | External config | CDN URL for assets/uploads |
| `TAOH_API_PREFIX` | External config | API endpoint base URL |
| `TAOH_API_SECRET` | External config | API auth secret |
| `TAOH_API_TOKEN_DUMMY` | External config | Anonymous token |
| `TAOH_OPS_CODE` | External config | Operations validation code |
| `TAOH_ROOT_PATH_HASH` | External config | Session key prefix |
| `TAOH_SITE_ROOT_HASH` | External config | Used in /fwd/ redirect URLs |
| `TAOH_LOGIN_URL` | External config | Login page URL |
| `TAOH_REDIRECT_URL` | External config | Post-login redirect |
| `TAOH_SITE_NAME_SLUG` | External config | Site display name |
| `TAOH_ACTION_URL` | External config | Form POST endpoint base |
| `TAOH_PLUGIN_PATH` | External config | Server filesystem path to plugin |
| `TAOH_SOCIAL_LIKES_THRESHOLD` | External config | Min likes to show count |
| `TAOH_SOCIAL_COMMENTS_THRESHOLD` | External config | Min comments to show count |
| `TAOH_SOCIAL_SHARES_THRESHOLD` | External config | Min shares to show count |
| `TAOH_METRICS_COUNT_SHOW` | External config | Show metrics counts flag |
| `TAOH_ENABLE_JUSASK` | External config | Q&A widget flag |
| `TAOH_LEARNING_WIDGET_ENABLE` | External config | Learning widget flag |

---

## 22. EXTERNAL DEPENDENCIES

| Library | Usage |
|---|---|
| jQuery | DOM, AJAX, events |
| Bootstrap 4+ | Grid, modals, tabs, tooltips, form-control |
| Pagination.js | `$(holder).pagination({...})` |
| Summernote | Cover letter WYSIWYG (`$('.summernote')`) |
| jQuery Validate | Form validation (`$.validator`, `$('#form').validate()`) |
| Owl Carousel | Testimonial carousel (visitor.php) |
| TomSelect | Location/company autocomplete (locationSelect, companySelect JS) |
| Line Awesome | `la la-*` icons |
| Material Icons | Secondary icons |

---

## 23. FRAMEWORK FUNCTIONS USED

| Function | Purpose |
|---|---|
| `taoh_get_header()` / `taoh_get_header_mobile()` | Page header/head |
| `taoh_get_footer()` | Page footer |
| `taoh_user_is_logged_in()` | Auth check (boolean) |
| `taoh_user_all_info()` | Returns user object |
| `taoh_user_full_name()` | Display name string |
| `taoh_parse_url(index)` | URL segment at index |
| `taoh_app_info(slug)` | App metadata object |
| `taoh_redirect(url)` | HTTP redirect |
| `taoh_exit()` | Die/exit wrapper |
| `taoh_site_ajax_url()` | AJAX endpoint URL |
| `taoh_apicall_get(call, vals, prefix, raw)` | GET API call |
| `taoh_apicall_post(call, vals)` | POST API call |
| `taoh_session_get(hash)` | Get session data array |
| `taoh_get_dummy_token(flag)` | Get auth token |
| `taoh_get_currency_symbol(index, all)` | Currency symbol(s) |
| `taoh_set_success_message(msg)` | Flash success message |
| `taoh_set_error_message(msg)` | Flash error message |
| `taoh_title_desc_decode(text)` | Decode encoded text |
| `taoh_p2us(string)` | String to underscore format |
| `taoh_scope_key_encode(data, scope)` | Cache key encoding |
| `taoh_either_or(val, fallback)` | Coalesce helper |
| `taoh_sanitizeInput(input)` | Input sanitization |
| `taoh_cacheops(op, data)` | Cache operations |
| `slugify2(text)` | URL slugification |
| `taohFullyearConvert(timestamp)` | Date formatting |
| `formatDate(date)` | Date formatting |
| `get_room_info(key, ptoken, opts)` | Club room lookup |
| `create_room_info(data, ptoken)` | Club room creation |
| `taoh_networking_getcell(key, ptoken)` | Get networking cell |
| `taoh_networking_postcell(data, ptoken)` | Create networking cell |
| `taoh_get_user_info(ptoken, scope)` | Get other user's info |
| `taoh_comments_widget(opts)` | Comments component |
| `taoh_share_widget(opts)` | Share component |
| `taoh_get_recent_jobs(type)` | Recent jobs sidebar widget |
| `taoh_jusask_widget(type)` | Q&A sidebar widget |
| `taoh_readables_widget(type)` | Learning content widget |
| `taoh_invite_friends_widget(title, type)` | Invite widget |
| `jobs_networking_widget()` | Job networking sidebar widget |
| `taoh_metrix_ajax(type, arr)` | Bulk metrics tracking |

### Client-side Helper Functions (defined externally, used in jobs JS)
```javascript
format_object(data)              // Normalize API response structure
convertToSlug(text)              // URL slugification
displayTaohFormatted(text)       // Decode HTML entities
ucfirst(text)                    // Capitalize first letter
taoh_title_desc_decode(text)     // Decode job text
loader(show, element)            // Show/hide loader spinner
save_metrics(type, action, ct)   // Track user actions via AJAX
renderJobType(type)              // Format job type for display
renderRoleType(type)             // Format role type for display
newgenerateSkillHTML(skills)     // Render skill badges (new style)
newgenerateLocationHTML(loc)     // Render location (new style)
newgenerateCompanyHTML(company)  // Render company (new style)
generateSkillHTML(skills)        // Skill badges (legacy — visitor.php)
generateCompanyHTML(company)     // Company display (legacy)
generateLocationHTML(loc)        // Location display (legacy)
generateRoleHTML(roles)          // Role display (legacy)
taoh_set_success_message(msg)    // JS flash message
taoh_set_error_message(msg)      // JS flash error
getIntaoDb(dbName)               // Open IndexedDB connection
IntaoDB.setItem(store, data)     // IndexedDB write
checkTTL(key, store)             // Check/invalidate TTL
crc32(str)                       // CRC32 hash
timetotimestamp(time)            // Time conversion
showBasicSettingsModal()         // Profile settings modal
```
