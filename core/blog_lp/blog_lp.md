# Blog Landing Page Module (`core/blog_lp/`)

## Purpose
Public-facing blog/reads landing page system. Renders a news-magazine style blog interface with categorized sections, search, detail view, pagination, and IndexedDB client-side caching. Used for the `/learning` or reads landing page (LP) routes.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: Entry-point router. Sets `TAOH_APP_SLUG = 'learning'`.
- **Requires**: `reads_lp_functions.php` first (no dependencies).
- **Routing** via `taoh_parse_url_lp(1)`:
  | Slug | File |
  |------|------|
  | `d` | `reads_lp_detail.php` (single post view) |
  | `search` / `category` | `reads_lp_search.php` |
  | default | `reads_lp.php` (home listing) |
- **Note**: The `case 'search' || 'category'` on line 11 is a PHP bug — `'search' || 'category'` evaluates to `true`, so this case matches everything truthy. In practice it still works because `d` matches first and default catches the rest.

---

### `reads_lp_functions.php` (Function Library)
- **Role**: Contains ALL helper functions for the blog LP. ~690 lines.
- **Key Functions**:

| Function | Purpose | API Call | Lines |
|----------|---------|----------|-------|
| `taoh_all_lp_reads_widget($val_arr, $design)` | Widget dispatcher — routes to display functions by design type (`center1`-`center4`, `right1`-`right3`, `right_ad1`-`right_ad3`) | None | 2-37 |
| `taoh_lp_get_widget()` | Fetches landing page widget data | `core.content.get` with `type: landing`, `mod: users` | 38-58 |
| `taoh_lp_blog_link($conttoken, $link)` | Generates blog post URL: `TAOH_READS_LP_URL/d/{conttoken}` | None | 59-64 |
| `taoh_lp_category_link()` | Returns `TAOH_READS_LP_URL/category` | None | 65-67 |
| `blog_lp_related_post($tags, $count, $debug)` | Fetches related posts by tag | `core.content.get` with `type: reads`, `ops: list`, `category: uncategorized` | 68-99 |
| `taoh_side_bar()` | Renders sidebar with Recent/Popular tabbed widget + social icons | Uses `blog_lp_related_post()` | 100-161 |
| `get_tags_list_lp()` | Fetches tags list | `core.content.get` with `type: tags_list`, `ops: list`, `tags: latest` | 649-672 |
| `get_footer_tags_list_lp()` | Fetches footer tags | `content.get.taglist` | 674-689 |

- **Display Widget Functions** (all render HTML templates with blog cards):
  - `taoh_all_lp_central_widget1($center1)` — First item large, rest small thumbnails
  - `taoh_all_lp_central_widget2($center_2)` — Slices to 3, first large
  - `taoh_all_lp_central_widget3($center3)` — Scroll items
  - `taoh_all_lp_central_widget4($center_4)` — Last 3 items
  - `taoh_lp_blog_satart($hero)` — Hero featured posts grid
  - `taoh_releated_widget1/2($related)` — Related posts
  - `taoh_int_releated`, `taoh_resume_releated`, `taoh_brand_releated` — Category-specific renderers
  - `taoh_job_releated`, `taoh_rand_releated` — Sidebar tab renderers
  - `taoh_learn_releated`, `taoh_mind_releated`, `taoh_prod_releated`, `taoh_net_releated`, `taoh_stress_releated` — Category tab renderers

- **Image Fallback Pattern** (used everywhere): If image missing or from `images.unsplash.com`, falls back to: `TAOH_CDN_PREFIX/images/igcache/{encoded_title}/900_600/blog.jpg`

---

### `reads_lp.php` (Blog Home Page)
- **Role**: Main blog listing page with categorized sections.
- **Layout Structure**:
  1. Hero featured posts (top)
  2. Category sections using `$response_tags[0..6]` — each fetched via `blog_lp_related_post(tag, count)`
  3. Tabbed categories section using `$response_tags[7..11]`
  4. "Recent Posts" — loaded via AJAX with pagination
  5. Sidebar (right column)
- **Client-Side JS**:
  - AJAX endpoint: `TAOH_TEMP_SITE_FILE_PARSE + TAOH_TEMP_SITE_URL + '/ajax'`
  - Action: `taoh_central_tag_get` with `ops: list`
  - Uses IndexedDB (`IntaoDB`) when `TAOH_INTAODB_ENABLE` is true
  - Store name: `READStore`, TTL store: `TTLStore`
  - Cache key: `tags_lp_{crc32('list'+page+perpage+category)}`
  - TTL: 2 hours
  - Pagination: `simplePagination` jQuery plugin, triggers at 11+ items

---

### `reads_lp_detail.php` (Single Post View)
- **Role**: Renders individual blog post.
- **Data Fetch**: `core.content.get` API with `ops: detail`, `type: reads`, `conttoken` from URL segment 2.
- **URL Parsing**: Extracts conttoken by splitting on `-` and taking last element.
- **Rendered Data**: `title`, `description` (HTML decoded + URL decoded), `image`/`video_link` (YouTube check), `date`, `author` info (`chat_name`, `avatar`, `ptoken`).
- **Related Posts**: Fetched in sidebar using post's `tags`.
- **Breaking News Bar**: Rotating titles from `blog_lp_related_post('', 5)`, 3-second interval.

---

### `reads_lp_search.php` (Search/Category Page)
- **Role**: Search results and category listing.
- **URL Parsing**: `taoh_parse_url_lp(1)` = action, `taoh_parse_url_lp(2)` = category. Also checks `$_GET['q']` for search.
- **AJAX**: Same pattern as `reads_lp.php` but sends `search` or `tags` param.
- **IndexedDB key**: `tags_lp_search_{crc32(page+perpage+searchText)}`

---

### `index.php`
- Security guard (`// silence is golden`).

---

## API Endpoints Used
| Endpoint | Purpose | Params |
|----------|---------|--------|
| `core.content.get` | Fetch post list/detail | `mod: core`, `type: reads`, `ops: list/detail` |
| `core.content.get` | Landing widget data | `mod: users`, `type: landing` |
| `core.content.get` | Tags list | `mod: users`, `type: tags_list`, `ops: list` |
| `content.get.taglist` | Footer tags | `secret`, `token` |

## Constants Used
`TAOH_API_TOKEN_DUMMY`, `TAOH_CDN_PREFIX`, `TAOH_READS_LP_URL`, `TAOH_TEMP_SITE_FILE_PARSE`, `TAOH_TEMP_SITE_URL`, `TAOH_TEMP_SITE_FILE_PATH_SECRET`, `TAOH_INTAODB_ENABLE`

## Key Edit Points
- **Add new blog category section**: Edit `reads_lp.php`, add new section block referencing `$response_tags[n]`
- **Change image fallback**: Search for `images/igcache/` in `reads_lp_functions.php`
- **Modify AJAX data fetch**: Edit `taoh_blogs_init()` JS function in `reads_lp.php` or `reads_lp_search.php`
- **Change IndexedDB TTL**: Edit `blog_setting_time.setHours(... + 2)` in the `indx_blogs_lp_list()` JS function
- **Add new display widget**: Add function in `reads_lp_functions.php`, register in `taoh_all_lp_reads_widget()` switch
