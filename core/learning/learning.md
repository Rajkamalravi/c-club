# Learning Module - Comprehensive Documentation

> Auto-generated deep analysis of every PHP file in `/wpl/club/core/learning/` and all subdirectories.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Root Files](#root-files)
3. [Blog Sub-module (`blog/`)](#blog-sub-module)
4. [Flashcards Sub-module (`flashcards/`)](#flashcards-sub-module)
5. [Newsletter Sub-module (`newsletter/`)](#newsletter-sub-module)
6. [Tips Sub-module (`tips/`)](#tips-sub-module)
7. [Key Constants & Variables Reference](#key-constants--variables-reference)
8. [API Endpoints Reference](#api-endpoints-reference)
9. [AJAX Actions Reference](#ajax-actions-reference)
10. [Surgical Edit Guide](#surgical-edit-guide)

---

## Architecture Overview

The Learning module is a multi-feature content platform built on the **TAOH framework**. It serves as the content/learning hub with the following sub-applications:

```
learning/
  main.php                 <-- PRIMARY ROUTER (sets TAOH_CURR_APP_SLUG = 'learning')
  index.php                <-- Directory protection ("silence is golden")
  obviousbaba.php          <-- ObviousBaba quote landing page
  askobviousbaba.php       <-- ObviousBaba chat (public, login-gated chat)
  askob.php / askob_old.php    <-- ObviousBaba chat (login-required variants)
  askcc.php / askcc_old.php    <-- AskTheCoach chat (login-required variants)
  jusask.php               <-- JusAsk career coach chat (public, login-gated)
  blog/                    <-- Blog/Reads sub-module (default route)
  flashcards/              <-- Flashcards sub-module
  newsletter/              <-- Newsletter sub-module
  tips/                    <-- Tips sub-module
```

### Routing Flow

`main.php` uses `taoh_parse_url(1)` to route:

| URL Segment | Target File | Notes |
|---|---|---|
| `obviousbaba` | `obviousbaba.php` | Quote landing |
| `jusask` | `jusask.php` | Career coach chat |
| `askobviousbaba` | `askobviousbaba.php` | OB chat |
| `flashcard` | `flashcards/main.php` | Flashcard sub-router |
| `newsletter` | `newsletter/main.php` | Newsletter sub-router |
| `tips` or `tip` | `tips/tips.php` | Tips page |
| *(default)* | `blog/main.php` | Blog/reads sub-router |
| ~~`askob`~~ | ~~`askob.php`~~ | **Commented out** |
| ~~`askcc`~~ | ~~`askcc.php`~~ | **Commented out** |

### Common Patterns

- **SEO Constants**: Every page-level file defines `TAO_PAGE_AUTHOR`, `TAO_PAGE_TITLE`, `TAO_PAGE_DESCRIPTION`, `TAO_PAGE_IMAGE`, `TAO_PAGE_ROBOT` before calling `taoh_get_header()`.
- **API Calls**: Server-side via `taoh_apicall_get($url, $params)` / `taoh_apicall_post($url, $params)`. Primary API: `core.content.get` and `core.content.post`.
- **AJAX Pattern**: Client POSTs to `taoh_site_ajax_url()` with `taoh_action` parameter.
- **Content Tokens**: Every piece of content is identified by a `conttoken` (10-20 char alphanumeric).
- **Health Check**: Some pages call `/health/appload.php` before rendering.
- **IndexedDB Caching**: Controlled by `TAOH_INTAODB_ENABLE` constant; uses `IntaoDB`, `READStore`, `TTLStore`.
- **Redis Caching**: Server-side via `taoh_cacheops()` and `taoh_delete_local_cache()`.

---

## Root Files

### `index.php`
- **Purpose**: Directory index protection.
- **Content**: `// silence is golden`

### `main.php`
- **Purpose**: Primary router for the entire learning module.
- **Key Logic**: Sets `TAOH_CURR_APP_SLUG` to `'learning'`, then `switch(taoh_parse_url(1))` to route to sub-modules.
- **Edit Point**: Add new routes by adding `case` statements before the `default` block (line 33).

### `obviousbaba.php`
- **Purpose**: ObviousBaba landing/quote page with OwlCarousel.
- **SEO**: Author=`ObviousBaba`, Image=`https://obviousbaba.com/images/obviousbaba.png`
- **AJAX Actions**:
  - `taoh_get_qoute` - Fetches a random quote (called on page load and carousel change)
- **Key JS Functions**:
  - `getQoute()` - Fetches quote via AJAX
  - `get_nxt_qoute()` - Adds quote to carousel
  - `render_chat_template(data, slot)` - Renders quote HTML
- **Dependencies**: OwlCarousel 2.3.4 (CDN)
- **Integration**: Links to `/learning/askobviousbaba` for chat.
- **Images**: `TAOH_OPS_PREFIX/images/cards/ob1.png`, `TAOH_OPS_PREFIX/images/obviousbaba_logo.png`, `TAOH_OPS_PREFIX/images/obviousbaba.png`

### `askobviousbaba.php`
- **Purpose**: Public-facing ObviousBaba chat interface. Shows login button for unauthenticated users; full chat for logged-in users.
- **SEO**: Author=`ObviousBaba`, Robot=`index, follow`
- **Health Check**: Calls `/health/appload.php?url=...` with `mod=askobviousbaba` via `taoh_apicall_get()`.
- **AJAX Actions**:
  - `chat_msg_get` - Sends user message, receives bot response
- **Key JS Functions**:
  - `taoh_chat_init()` - Sends message via AJAX
  - `render_chat_template(data, slot)` - Renders chat bubbles (bot left, user right)
- **Login Gate**: Uses `taoh_user_is_logged_in()`. Unauthenticated users see a login/signup button linking to `TAOH_LOGIN_URL?redirect_url=TAOH_REDIRECT_URL`.
- **Images**: `TAOH_CDN_PREFIX/app/askob/images/askob_sq_64.png`, `TAOH_CDN_PREFIX/app/askob/images/askob_sq_512.png`

### `askob.php`
- **Purpose**: ObviousBaba chat (requires login). Nearly identical to `askobviousbaba.php` but without public access gate.
- **AJAX Action**: `chat_msg_get`
- **Images**: Uses `TAOH_OBVIOUS_PREFIX` for image paths.
- **Note**: Route is **commented out** in `main.php`.

### `askob_old.php`
- **Purpose**: Older duplicate of `askob.php`. Same functionality, slightly different styling.
- **Note**: Legacy/backup file, not routed.

### `askcc.php`
- **Purpose**: AskTheCoach chat interface. Requires login.
- **AJAX Action**: `chat_coach_get`
- **Images**: `TAOH_CDN_PREFIX/app/jusask/images/jusask_sq_64.png`, `TAOH_CDN_PREFIX/app/jusask/images/jusask_sq_512.png`
- **Note**: Route is **commented out** in `main.php`.

### `askcc_old.php`
- **Purpose**: Older version of `askcc.php`. Different header background (black vs. default).
- **Note**: Legacy/backup file, not routed.

### `jusask.php`
- **Purpose**: JusAsk community-powered career coach chat. Public-facing with login gate for chat functionality.
- **SEO**: Author=`JusAsk`, Robot=`index, follow`
- **Health Check**: Calls `/health/appload.php` with `mod=jusask`.
- **AJAX Action**: `chat_coach_get`
- **Key JS Functions**:
  - `taoh_chat_init()` - Sends `ask` message, receives response array
  - `render_chat_template(data, slot)` - Renders multiple chat messages; bot messages in blue, user messages in white
- **Login Gate**: Logged-in users get chat input; others get login button.
- **Images**: `TAOH_CDN_PREFIX/app/jusask/images/jusask_sq_512.png` (background), `jusask_sq_64.png` (bot avatar)
- **Disclaimer**: Shows privacy/testing disclaimer at bottom.

---

## Blog Sub-module

### `blog/index.php`
- **Purpose**: Directory protection. `// silence is golden`

### `blog/main.php`
- **Purpose**: Blog sub-router. Parses `taoh_parse_url(1)` for blog-specific routes.
- **Prerequisites**: Checks `TAOH_READS_ENABLE` constant. If URL starts with `/reads/`, redirects to `/learning/`.
- **Includes**: `functions.php`, `widget_functions.php`
- **Routes**:

| Segment | Target | Conditions |
|---|---|---|
| `blog` | `blog_detail.php` | Blog detail view |
| `all` | `blog_all_post.php` | Login + `/post` required |
| `post` | `blog_post.php` | Login + date-gate (`$_GET['post'] == date('Ymd')`) |
| `edit` | `blog_post.php` | Login required |
| `job`/`jobs` | `reads_jobs.php` | Jobs-focused reads |
| `work` | `reads_work.php` | Work-focused reads |
| `wellness` | `reads_wellness.php` | Wellness reads |
| `search` | `search.php` | Search results |
| *(default)* | `reads.php` | Main reads landing |

### `blog/functions.php`
- **Purpose**: Core blog helper functions.
- **Functions**:
  - `taoh_get_blog_categories_blog()` - Fetches categories from `TAOH_CDN_PREFIX/assets/category.php`. Returns decoded JSON array.
  - `tags_widget()` - Renders category tag cloud linking to `TAOH_READS_URL/search?q={slug}&type=category`.
  - `taoh_blog_link($conttoken, $link="")` - Returns blog detail URL. If `$link` provided, returns external link. Default: `TAOH_READS_URL/blog/{conttoken}`.
  - `blog_related_post($category)` - **API**: `core.content.get` with `ops=related`, `type=reads`, `count=3`. Returns related posts.
  - `blog_related_widget($category)` - Renders "RELATED POSTS" sidebar widget.
  - `blog_related_side_widget($category)` - Renders "MOST POPULAR" sidebar with images.
  - `external_link_icon()` - Returns SVG icon for external links.
  - `blog_search_widget()` - Renders search form POSTing to `TAOH_READS_URL`.
  - `side_widget4()` - Renders "FEATURED" sidebar from `taoh_central_widget_get()`.
  - `taoh_central_widget_get()` - **API**: `core.content.get` with `ops=list`, `type=reads`, `sort=rand`, `perpage=10`. Returns shuffled content list.
  - `field_tags()` - (defined but not detailed in the file)

### `blog/actions.php`
- **Purpose**: Blog form action handler (POST processing).
- **Actions**:
  - `$_POST['action'] == "save"`:
    - Validates title and description required.
    - Encodes fields via `taoh_title_desc_encode()`.
    - **API**: `core.content.post` with `ops=add`, `type=reads`.
    - Cache: `taoh_delete_local_cache()` with patterns `reads_{conttoken}`, `reads_*`.
    - On success: redirects to `TAOH_READS_URL/blog/{conttoken}`.
    - Handles `$_POST['sub_secret_token']` and `TAOH_ROOT_PATH_HASH` for security.
  - `$_POST['action'] == "blog_delete"`:
    - **API**: `core.content.post` with `ops=delete`, `type=reads`.
    - Returns JSON response.
- **Constants Used**: `TAOH_API_TOKEN`, `TAOH_API_SECRET`, `TAOH_ROOT_PATH_HASH`, `BLOG_MAXMIUM_STATUS`.

### `blog/ajax.php`
- **Purpose**: AJAX handler for blog likes.
- **Functions**:
  - `blog_like_put()` - Pushes like metric via `taoh_cacheops('metricspush', $values)`. Values: `[conttoken, 'reads', ptoken, 'like', time(), TAOH_API_SECRET]`.

### `blog/blog_detail.php`
- **Purpose**: Main blog detail/article view page.
- **URL Parsing**: `taoh_parse_url(2)` for conttoken. Extracts from slug format (`title-slug-conttoken`) using `array_pop(explode('-', ...))`. Validates: 10-20 chars, alphanumeric.
- **API Calls**:
  - `core.content.get` with `ops=detail`, `type=reads` - Fetches blog content.
  - `system.users.metrics` - Checks if current user has liked the post.
- **SEO**: Full dynamic SEO from API response (author, description, image, title, robot, canonical).
- **Canonical Handling**: If `source` differs from `TAOH_SITE_URL`, adds `<link rel="canonical">` and `<meta name="original-source">`.
- **Key Variables**: `$conttoken`, `$title`, `$description`, `$categories`, `$video_link`, `$image`, `$author`, `$profile_picture`, `$ptoken`, `$share_link`.
- **Widgets**: Comments (`taoh_comments_widget()`), share (`taoh_share_widget()`), related posts, tags, copy-n-share (`taoh_copynshare_widget()`), recent jobs (`taoh_get_recent_jobs()`).
- **JS Functions**:
  - `blogDelete()` / `deleteConfirm()` - Delete blog via AJAX to `TAOH_ACTION_URL/blog`.
  - Like handling with `localStorage` persistence.
  - `save_metrics('reads', click_view, conttoken)` on page load.
- **Edit Point**: Owner sees "Edit Blog" and "Delete Blog" buttons when `$taoh_user_vars->ptoken == $ptoken`.

### `blog/blog_detail_2.php`
- **Purpose**: Older blog detail page using `TAOH_SITE_CONTENT_GET` direct URL instead of `taoh_apicall_get()`.
- **Note**: Legacy version, likely not actively used.

### `blog/blog_post.php`
- **Purpose**: Blog create/edit form (current version).
- **Form Action**: POSTs to `TAOH_ACTION_URL/blog`.
- **Fields**: `action` (hidden, "save"), `source`, `sub_secret_token`, `local`, `conttoken` (edit only), `title`, `subtitle`, `recipe_title`, `description` (Summernote editor), `excerpt`, `visiblity` (public/login/password), `category[]`, `tags`, `blog_type` (internal/external), `media_link`, `media_url`, `media_type` (image/youtube/soundcloud), `source_name`, `source_url`, `via_name`, `via_url`, `status` (publish/draft/review), `publish` (date).
- **Editor**: Summernote (WYSIWYG).
- **Category**: Uses `blogCategorySelect()` JS function with Choices.js library.
- **Constants**: `BLOG_MAXMIUM_STATUS` controls available status options. `TAOH_READS_POST_LOCAL` for local posting flag.
- **Edit Mode**: If `conttoken` exists via `taoh_parse_url(3)`, fetches existing content from `core.content.get` with `ops=detail`, `type=reads`.

### `blog/blog_post_new.php`
- **Purpose**: Newer blog post form variant with dynamic tag input.
- **Difference from blog_post.php**: Uses `addInput()` JS function for tags as array inputs instead of single text field.

### `blog/blog_post_bkp.php`
- **Purpose**: Backup of blog_post.php with `htmlspecialchars()` on title field.

### `blog/blog_all_post.php`
- **Purpose**: Admin blog post form with additional `user_token` field shown.
- **Access**: Requires login + specific URL pattern.

### `blog/flashcard_post.php`
- **Purpose**: Older blog/flashcard create form using jquery-te editor and FilePond image uploader.
- **Dependencies**: jquery-te 1.4.0, FilePond 4.30.4.

### `blog/reads.php`
- **Purpose**: Main reads landing page (default route for blog sub-module).
- **SEO**: Dynamic title/description/keywords using `TAOH_SITE_NAME_SLUG`.
- **Data Flow**:
  1. PHP: Calls `taoh_wellness_widget_get('work')` for widget data.
  2. PHP: Renders hero section with trending bar and featured posts.
  3. JS: AJAX `taoh_central_get` action for paginated article list.
- **IndexedDB**: If `TAOH_INTAODB_ENABLE`, caches via `IntaoDB.setItem()` with TTL.
- **Key JS Functions**:
  - `taoh_blogs_init()` - Fetches articles via AJAX
  - `getreadslistdata()` - IndexedDB-first data fetching
  - `render_blog_template(data, slot)` - Renders article cards
  - `indx_reads_list()` - Saves to IndexedDB
  - Trending bar text rotator (`setInterval(change, 3000)`)
- **Layout Widgets**: `center1`/`center2`/`center3` (center column), `right_ad1`/`right_ad2`/`right_ad3` (ads), `right1`/`right2`/`right3` (sidebar).
- **Pagination**: Uses simplePagination.js with `show_pagination()`.

### `blog/reads_css.php`
- **Purpose**: Pure CSS stylesheet for reads/blog pages.
- **Key Classes**: `.blog-listing`, `.gray-dark`, `.session_title`, `.blog-news`, `.parentContainer`, `.h3-title`, `.descrip`, `.lat-title`, `.top-title`, `.td-read-more`, `.cl-image`, `.image-box`.

### `blog/reads_jobs.php`
- **Purpose**: Jobs-focused reads page. Same pattern as `reads.php`.
- **Difference**: `$reads_type = 'jobs'`. Includes `taoh_metrix_widjet()` in hero area for job metrics.

### `blog/reads_work.php`
- **Purpose**: Work-focused reads page.
- **Difference**: `$reads_type = 'work'`.

### `blog/reads_wellness.php`
- **Purpose**: Wellness-focused reads page.
- **Difference**: `$reads_type = 'wellness'`.

### `blog/search.php`
- **Purpose**: Blog search results page.
- **Input**: `$_GET['q']` for search query, `$_GET['type']` for search type.
- **AJAX Action**: `taoh_central_get` with `search` parameter.
- **IndexedDB**: Supports caching with `TAOH_INTAODB_ENABLE`.

### `blog/widget_functions.php`
- **Purpose**: Widget rendering functions for blog layout.
- **Functions**:
  - `taoh_wellness_widget_get($reads_type)` - **API**: `core.content.get` with `mod=users`, `type=landing`, `ops=$reads_type`. Returns structured widget data with keys: `hero`, `trending_bar`, `center1-3`, `right1-3`, `right_ad1-3`.
  - `taoh_all_reads_widget($val_arr, $design)` - Dispatcher that calls the appropriate widget renderer based on `$design` string.
  - `taoh_all_central_widget1($center1)` - "WHAT'S NEW" widget: 1 large + 4 small articles.
  - `taoh_all_central_widget2($center2)` - "EDITOR'S PICK" widget: 2 large + 4 small.
  - `taoh_all_central_widget3($center3)` - "RANDOM READS" widget: 3 cards in a row.
  - `taoh_all_right_widget1($related1)` - "RELATED" sidebar widget with image.
  - `taoh_all_right_widget2($right2)` - "APPLY TO JOBS" sidebar listing.
  - `taoh_all_right_widget3($related3)` - "RELATED" sidebar widget (duplicate pattern).
  - `taoh_right_ad1/2/3($data)` - Advertisement sidebar widgets. Ad1 links to #JusASKTheCoach, Ad2 to Obvious Baba, Ad3 to #JusASKTheCoach.
- **Image Fallback**: All widgets use `TAOH_CDN_PREFIX/images/igcache/{encoded_title}/900_600/blog.jpg` when image is missing or from Unsplash.

### `blog/blog_bkup.php`
- **Purpose**: Older blog listing page with featured header, breaking news ticker. Legacy/backup.

### `blog/blog_local.php`
- **Purpose**: Older reads layout using `taoh_wellness_widget_get('work')`. Legacy.

### `blog/blogs_2.php`
- **Purpose**: Alternative blog listing using `TAOH_SITE_READS` API. Legacy.

---

## Flashcards Sub-module

### `flashcards/index.php`
- **Purpose**: Directory protection. `// silence is golden`

### `flashcards/main.php`
- **Purpose**: Flashcard sub-router. Uses `taoh_parse_url(2)`.
- **Includes**: `functions.php`
- **Routes**:

| Segment | Target | Conditions |
|---|---|---|
| `post` | `flashcard_post.php` | Login + date-gate |
| `edit` | `flashcard_post.php` | Login required |
| *(default)* | `flash_local.php` | Main flashcard display |

### `flashcards/functions.php`
- **Purpose**: Flashcard helper functions.
- **Functions**:
  - `taoh_get_blog_categories2($type)` - Fetches categories. For `flash` type: **API** `infofetch.get` with `ops=flashcat`. Returns decoded categories array.

### `flashcards/actions.php`
- **Purpose**: Flashcard form action handler.
- **Actions**:
  - `$_POST['action'] == "save"`:
    - Validates title and description.
    - **API**: `core.content.post` with `ops=add`, `type=flash`.
    - Cache invalidation: `flash_{conttoken}`, `flash_*`.
    - Redirects to `TAOH_FLASHCARD_URL/{conttoken}` on success.
  - `$_POST['action'] == "flash_delete"`:
    - **API**: `core.content.get` with `ops=delete`, `type=flash`.

### `flashcards/ajax.php`
- **Purpose**: AJAX handler for flashcard fetching.
- **Functions**:
  - `flashcard_get()` - **API**: `core.content.get` with dynamic `ops`, `type`, `category`, `conttoken` from `$_POST`. Returns JSON response.

### `flashcards/flashcards.php`
- **Purpose**: Flashcard display page (OwlCarousel-based). An alternative display to `flash_local.php`.
- **Features**: OwlCarousel card viewer, categories modal, URL updates via `pushState`.
- **AJAX Action**: `flashcard_get` with `ops=random`, `type=flash`.
- **Note**: Uses OwlCarousel for card navigation.

### `flashcards/flash_local.php`
- **Purpose**: **Main flashcard display page** (default route). CSS flip-card UI.
- **SEO**: Author=`Flashcard`, Robot=`index, follow`.
- **URL Parsing**: `$category = taoh_parse_url(2)`, `$conttoken = taoh_parse_url(3)`.
- **PHP Data Loading**:
  - `taoh_category_info($category, 'flash')` - Category metadata (color, text, bucket).
  - `taoh_get_categories('flash')` - All categories.
  - `taoh_category_bucket($all_categories, $bucket)` - Filtered categories.
  - `taoh_user_all_info()` - Current user info.
- **AJAX Action**: `flashcard_get` with `ops=random`, `type=flash`, `category`.
- **Key JS Functions**:
  - `taoh_flash_init()` - Fetches random flashcard.
  - `taoh_cont_init()` - Fetches specific flashcard by conttoken.
  - `render_title_template(data, slot)` - Renders card front title.
  - `render_quote_template(data, slot)` - Renders card back description.
  - `render_url(data)` - Updates browser URL via `pushState`.
  - `render_button(conttoken, flashptoken, slot)` - Shows Edit/Delete for card owner.
  - `handleFlip()` - CSS flip animation toggle.
  - `copyText()` - Copies card URL to clipboard.
  - `deleteConfirm()` - Deletes card via POST to `TAOH_ACTION_URL/flashcard`.
- **Images**: `TAOH_SITE_URL_ROOT/assets/images/flashcard/{category}.png` (card backgrounds), `/sq/{category}.png` (square), `/card/{category}.png`.
- **Metrics**: Calls `save_metrics('flashcard', 'view', conttoken)` per card view.
- **Categories Modal**: Lists all categories with counts, linking to `/learning/flashcard/{slug}/`.

### `flashcards/flashcard_2.php`
- **Purpose**: Older flashcard display with direct API call, sidebar with tips form and category dropdown. Legacy.

### `flashcards/flashcard_post.php`
- **Purpose**: Flashcard create/edit form.
- **Form Action**: POSTs to `TAOH_ACTION_URL/flashcard`.
- **Fields**: `action`, `source`, `sub_secret_token`, `conttoken` (edit), `title`, `description` (CKEditor), `visiblity`, `category[]`, `blog_type`, `media_link`, `media_url`, `media_type`, `source_name`, `source_url`, `status`, `publish`.
- **Editor**: CKEditor (via CDN).
- **Edit Mode**: If `taoh_parse_url(3)` has conttoken, fetches from `core.content.get` with `ops=detail`, `type=flash`.

---

## Newsletter Sub-module

### `newsletter/index.php`
- **Purpose**: Directory protection. `// silence is golden`

### `newsletter/main.php`
- **Purpose**: Newsletter sub-router. Uses `taoh_parse_url(2)`.
- **Includes**: `functions.php`, `widget_functions.php`
- **Routes**:

| Segment | Target | Conditions |
|---|---|---|
| `d` | `newsletter_detail.php` | Newsletter detail view |
| `post` | `newsletter_post.php` | Date-gate (`$_GET['post'] == date('Ymd')`) |
| `edit` | `newsletter_post.php` | Login required |
| `search` | `search.php` | Search results |
| *(default)* | `newsletter.php` | Newsletter landing |

- **Note**: Calls `die()` at end to prevent further execution.

### `newsletter/functions.php`
- **Purpose**: Newsletter helper functions. Mirrors `blog/functions.php` but for `newsletter` content type.
- **Functions**:
  - `taoh_get_blog_categories_blog()` - Same as blog version. Fetches from `TAOH_CDN_PREFIX/assets/category.php`.
  - `tags_widget()` - Categories widget linking to `TAOH_NEWSLETTER_URL/search?q={slug}&type=category`.
  - `taoh_newsletter_link($conttoken, $link="")` - Returns `TAOH_NEWSLETTER_URL/d/{conttoken}` or external link.
  - `newsletter_related_post($category)` - **API**: `core.content.get`, `ops=related`, `type=newsletter`, `count=3`.
  - `blog_related_widget($category)` - "RELATED POSTS" widget using newsletter data.
  - `blog_related_side_widget($category)` - "MOST POPULAR" with images.
  - `external_link_icon()` - SVG external link icon.
  - `blog_search_widget()` - Search form for `TAOH_NEWSLETTER_URL`.
  - `side_widget4()` - "FEATURED" sidebar from `taoh_central_widget_get()`.
  - `taoh_central_widget_get()` - **API**: `core.content.get`, `ops=list`, `type=newsletter`, `sort=rand`, `perpage=10`. Returns shuffled list.

### `newsletter/actions.php`
- **Purpose**: Newsletter form action handler.
- **Actions**:
  - `$_POST['action'] == "save"`:
    - Validates title and description.
    - **API**: `core.content.post`, `ops=add`, `type=newsletter`.
    - Cache: removes `newsletter_{conttoken}`, `newsletter_*`.
    - Redirects to `TAOH_READS_URL/newsletter/d/{conttoken}` on success.
- **Note**: No delete action defined (unlike blog/flashcard).

### `newsletter/ajax.php`
- **Purpose**: Newsletter like handler.
- **Functions**:
  - `newsletter_like_put()` - Pushes like via `taoh_cacheops('metricspush', $values)`. Values: `[conttoken, 'newsletter', ptoken, 'like', time(), TAOH_API_SECRET]`.

### `newsletter/newsletter.php`
- **Purpose**: Newsletter landing page. Same widget-based layout as blog reads pages.
- **SEO**: Dynamic title/description/keywords with `TAOH_SITE_NAME_SLUG`.
- **Data**: `taoh_wellness_widget_get('work')` with `$reads_type = 'work'`.
- **AJAX Action**: `taoh_central_newsletter_get` with `ops=list`, `offset`, `limit`.
- **Key JS Functions**:
  - `taoh_newsletter_init()` - Fetches newsletter list via AJAX.
  - `render_newsletter_template(data, slot)` - Renders newsletter article list.
  - Trending bar text rotator.
- **Layout**: Hero section with featured images, center widgets (center1-3), sidebar (tags, ads, right widgets, recent jobs).
- **Search**: Uses `taoh_newsletter_search_widget()`.
- **Pagination**: simplePagination.js, shows when `totalItems >= 11`.

### `newsletter/newsletter_detail.php`
- **Purpose**: Newsletter article detail page.
- **URL Parsing**: `taoh_parse_url(3)` -> extracts conttoken from slug format.
- **Validation**: 10-20 chars, alphanumeric.
- **API Calls**:
  - `core.content.get`, `ops=detail`, `type=newsletter` - Fetches content.
  - `system.users.metrics` - Checks user like status.
- **SEO**: Full dynamic SEO. Defines `TAO_PAGE_TYPE` as `'newsletter'`.
- **Canonical**: Cross-site canonical with `/hires/learning/newsletter/d/` prefix.
- **Features**: Video/image display, author section, breadcrumbs, comments widget, share widget, related posts, like/comment/share/view metrics.
- **Owner Controls**: Edit button for content owner. Delete button (commented out).
- **JS**: Like handling with localStorage, metrics tracking, comment scroll.
- **Sidebar**: Recent jobs, FEATURED widget (`side_widget4()`), copy-n-share widget.

### `newsletter/newsletter_post.php`
- **Purpose**: Newsletter create/edit form.
- **Form Action**: POSTs to `TAOH_ACTION_URL/newsletter`.
- **Fields**: `action`, `source`, `sub_secret_token`, `local`, `conttoken` (edit), `title`, `subtitle`, `description` (Summernote), `excerpt`, `visiblity`, `category[]`, `newsletter_type` (internal/external), `media_link`, `media_url`, `media_type`, `source_name`, `source_url`, `via_name`, `via_url`, `publish_date` (datetime-local), `nl_send_to` (all/professional/employer/provider).
- **Editor**: Summernote for description, jquery-te as backup.
- **Image Upload**: FilePond.
- **Edit Mode**: Fetches from `core.content.get` with `type=newsletter` when conttoken present.
- **Dependencies**: jquery-te, FilePond, Choices.js.

### `newsletter/reads_css.php`
- **Purpose**: Pure CSS stylesheet for newsletter pages. Identical to `blog/reads_css.php`.

### `newsletter/search.php`
- **Purpose**: Newsletter search results page.
- **Input**: `$_GET['q']` for search.
- **AJAX Action**: `taoh_central_newsletter_get` with `search` parameter.
- **IndexedDB**: Supports caching with TTL.
- **Search Widget**: Uses `taoh_newsletter_search_widget()`.

### `newsletter/widget_functions.php`
- **Purpose**: Widget rendering functions for newsletter layout. Mirrors `blog/widget_functions.php` but uses `type=newsletter` and `blog_type=newsletter`.
- **Functions**: Same pattern as blog version:
  - `taoh_wellness_widget_get($reads_type)` - API: `core.content.get`, `mod=users`, `type=landing`, `blog_type=newsletter`.
  - `taoh_all_reads_widget()` - Dispatcher.
  - `taoh_all_central_widget1/2/3()` - Center widgets (WHAT'S NEW, EDITOR'S PICK, RANDOM READS).
  - `taoh_all_right_widget1/2/3()` - Sidebar widgets (RELATED, APPLY TO JOBS).
  - `taoh_right_ad1/2/3()` - Ad widgets.
- **Link Function**: Uses `taoh_newsletter_link()` instead of `taoh_blog_link()`.

---

## Tips Sub-module

### `tips/index.php`
- **Purpose**: Directory protection. `// silence is golden`

### `tips/actions.php`
- **Purpose**: Tips form action handler.
- **Logic**: If user is logged in, sets `token = TAOH_API_TOKEN`, `mod = 'tips'`, then calls **API** `core.tips.post` with `$_POST` data. Shows success message and redirects to `TAOH_TIPS_URL`.

### `tips/tips.php`
- **Purpose**: Tips listing and submission page.
- **Layout**: 3-column: left menu (`taoh_leftmenu_widget()`), center (tips list + search), right sidebar.
- **Sidebar Widgets** (for logged-in users):
  - Submit Tips form (POSTs to `TAOH_ACTION_URL/tips`): fields `tip`, `url`, `cat` (dropdown).
  - `taoh_obviousbaba_widget()`, `taoh_readables_widget()`, `taoh_reads_category_widget()`, `taoh_ads_widget()`.
- **AJAX Actions**:
  - `get_tips` - Fetches tips list with `cat`, `ops`, `search`, `offset`, `limit`.
  - `delete_tips` - Deletes tip by conttoken.
  - `upvote_tips` - Upvotes tip by conttoken.
- **Categories**: Hardcoded array of 17 categories (General Search Strategy, Interview, Job Search, Networking, Resume, Jobs of Future, General Work Strategy, Branding, Career Development, Conflict Management, Growth Mindset, Handling Change, Leadership, Learning, Mindfulness, Upskilling, Future of Work).
- **Key JS Functions**:
  - `get_cat_list()` - Renders category tabs and dropdown.
  - `cat_change(e, data)` - Switches category filter.
  - `get_tips()` - Fetches tips via AJAX.
  - `tips_delete(id)` - Confirms and deletes tip.
  - `tips_upvote(id)` - Upvotes tip.
  - `render_tips_template(data, slot)` - Renders tip cards with vote count, title, author, category, delete (for "mytips" view).
  - `do_search(e)` - Live search on keyup.
- **Special View**: `ops='mytips'` shows user's own tips with DELETE option.

---

## Key Constants & Variables Reference

| Constant | Purpose |
|---|---|
| `TAOH_CURR_APP_SLUG` | Current app identifier (`'learning'`) |
| `TAOH_SITE_URL_ROOT` | Site root URL |
| `TAOH_SITE_URL` | Site URL (for canonical comparison) |
| `TAOH_CDN_PREFIX` | CDN base URL for static assets |
| `TAOH_OPS_PREFIX` | Operations/assets prefix |
| `TAOH_OBVIOUS_PREFIX` | ObviousBaba-specific asset prefix |
| `TAOH_API_PREFIX` | API base URL |
| `TAOH_API_TOKEN` | Authenticated user API token |
| `TAOH_API_SECRET` | API secret key |
| `TAOH_API_DUMMY_SECRET` | Dummy/public API secret |
| `TAOH_API_TOKEN_DUMMY` | Dummy token for unauthenticated |
| `TAOH_READS_URL` | Blog/reads base URL |
| `TAOH_NEWSLETTER_URL` | Newsletter base URL |
| `TAOH_FLASHCARD_URL` | Flashcard base URL |
| `TAOH_TIPS_URL` | Tips base URL |
| `TAOH_ACTION_URL` | Form action base URL |
| `TAOH_LOGIN_URL` | Login page URL |
| `TAOH_REDIRECT_URL` | Post-login redirect URL |
| `TAOH_ROOT_PATH_HASH` | Security hash for form submissions |
| `TAOH_READS_ENABLE` | Feature flag: enable reads |
| `TAOH_READS_GET_LOCAL` | Flag for local API calls |
| `TAOH_READS_POST_LOCAL` | Flag for local form posts |
| `TAOH_INTAODB_ENABLE` | Feature flag: IndexedDB caching |
| `TAOH_METRICS_COUNT_SHOW` | Show metric counts |
| `TAOH_METRICS_EYE_SHOW` | Show view count icon |
| `TAOH_SOCIAL_LIKES_THRESHOLD` | Minimum likes before showing count |
| `TAOH_SITE_NAME_SLUG` | Site name for SEO |
| `BLOG_MAXMIUM_STATUS` | Controls available blog statuses |
| `TAO_PAGE_AUTHOR` | SEO: page author |
| `TAO_PAGE_TITLE` | SEO: page title |
| `TAO_PAGE_DESCRIPTION` | SEO: meta description |
| `TAO_PAGE_IMAGE` | SEO: OG image |
| `TAO_PAGE_ROBOT` | SEO: robot directive |
| `TAO_PAGE_CANONICAL` | SEO: canonical link |
| `TAO_PAGE_TYPE` | Content type identifier |
| `TAO_PAGE_AUTHOR_IMAGE` | Author avatar for detail pages |

---

## API Endpoints Reference

| Endpoint | Method | Usage |
|---|---|---|
| `core.content.get` | GET | Fetch content (detail, list, related, delete, landing) |
| `core.content.post` | POST | Create/update content (reads, flash, newsletter) |
| `core.tips.post` | POST | Submit tips |
| `system.users.metrics` | GET | Check user like status for content |
| `infofetch.get` | GET | Fetch flashcard categories (`ops=flashcat`) |

### `core.content.get` Parameters

| Param | Values | Notes |
|---|---|---|
| `mod` | `core`, `users` | Module |
| `ops` | `detail`, `list`, `related`, `delete`, `random` | Operation |
| `type` | `reads`, `flash`, `newsletter`, `landing` | Content type |
| `token` | varies | Auth token |
| `conttoken` | alphanumeric | Content identifier |
| `category` | string | Category filter |
| `count` | int | Result count |
| `page` | int | Page number |
| `perpage` | int | Items per page |
| `sort` | `rand` | Sort order |
| `q` | string | Search query |
| `key` | `TAOH_API_SECRET` or `TAOH_API_DUMMY_SECRET` | API key |
| `local` | bool | Local fetch flag |
| `cache_time` | int (seconds) | Cache duration |
| `blog_type` | `newsletter` | Sub-type filter |

---

## AJAX Actions Reference

| Action | File(s) | Purpose |
|---|---|---|
| `taoh_get_qoute` | `obviousbaba.php` | Get random ObviousBaba quote |
| `chat_msg_get` | `askobviousbaba.php`, `askob.php` | Send/receive ObviousBaba chat |
| `chat_coach_get` | `jusask.php`, `askcc.php` | Send/receive career coach chat |
| `taoh_central_get` | `blog/reads.php`, `blog/search.php` | Fetch blog article list |
| `flashcard_get` | `flashcards/ajax.php` | Fetch flashcard(s) |
| `taoh_central_newsletter_get` | `newsletter/newsletter.php`, `newsletter/search.php` | Fetch newsletter list |
| `get_tips` | `tips/tips.php` | Fetch tips list |
| `delete_tips` | `tips/tips.php` | Delete a tip |
| `upvote_tips` | `tips/tips.php` | Upvote a tip |

---

## Surgical Edit Guide

### Add a New Learning Sub-module
1. Create directory: `learning/newmodule/`
2. Add `index.php` with `// silence is golden`
3. Create `main.php` as sub-router
4. Add route in `learning/main.php` before `default` case:
   ```php
   case 'newmodule':
       include_once('newmodule/main.php');
       break;
   ```

### Re-enable AskOB / AskCC Routes
In `learning/main.php`, uncomment lines 8-13:
```php
case 'askob':
    include_once('askob.php');
    break;
case 'askcc':
    include_once('askcc.php');
    break;
```

### Change Blog Content Type
In `blog/functions.php` and `blog/widget_functions.php`, search for `type=reads` or `"type"=>"reads"` and change to your new type.

### Add New Widget to Reads Sidebar
1. In `blog/widget_functions.php`, add new function (e.g., `taoh_all_right_widget4()`).
2. Add case in `taoh_all_reads_widget()` switch.
3. In `blog/reads.php`, add the widget call in the sidebar column.

### Change Flashcard Image Paths
In `flashcards/flash_local.php`, lines 193-196. Image pattern:
- Card bg: `TAOH_SITE_URL_ROOT/assets/images/flashcard/{category}.png`
- Square: `TAOH_SITE_URL_ROOT/assets/images/flashcard/sq/{category}.png`
- Card overlay: `TAOH_SITE_URL_ROOT/assets/images/flashcard/card/{category}.png`

### Add New Newsletter Field
1. Add HTML input in `newsletter/newsletter_post.php` inside the form.
2. Add PHP variable extraction in edit mode (around line 38-49).
3. Ensure `newsletter/actions.php` passes the field through `$_POST` to the API.

### Change Tips Categories
In `tips/tips.php`, modify the hardcoded `cat` array in `get_cat_list()` function (lines 112-130).

### Toggle IndexedDB Caching
Controlled by `TAOH_INTAODB_ENABLE` constant (set externally). Affects `blog/reads.php`, `blog/search.php`, `newsletter/search.php`.

### Modify SEO for Any Page
Each page-level file defines SEO constants before `taoh_get_header()`. Search for `define('TAO_PAGE_` in the target file and modify values.

### Content Image Fallback Pattern
Used across all modules. When image is missing or from Unsplash:
```php
$image = TAOH_CDN_PREFIX."/images/igcache/".urlencode($title)."/900_600/blog.jpg";
```
To change fallback image format, search for `/images/igcache/` across all files.
