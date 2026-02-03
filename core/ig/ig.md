# IG (Image Generator) Module (`core/ig/`)

## Purpose
Dynamic image generator that creates blog post thumbnail/social-share images on-the-fly using PHP GD library. Generates colored PNG images with the post title text overlaid, category-specific background colors, and "Reads by TAO" branding.

---

## File-by-File Breakdown

### `index.php` (Primary — standalone mode)
- **Role**: Generates images when accessed via direct URL path like `/ig/{title}/{category}/{width_height}/...`
- **URL Pattern**: `images/ig/{title}/{podname}/{width_height}/default.png`
- **URL Parsing**: Splits `$_SERVER['REQUEST_URI']` on `images/ig/`, then extracts:
  - `$vals[0]` = title (URL-encoded)
  - `$vals[1]` = podname (used as category)
  - `$vals[2]` = dimensions (e.g., `600_400`)

#### Functions:
| Function | Purpose | Lines |
|----------|---------|-------|
| `taoh_string_split($string, $length)` | Splits string into word-boundary-respecting chunks of max `$length` characters | 2-19 |
| `taoh_get_category_color($cat)` | Returns hex color for blog category. 18 predefined categories (interview→#3E54AC, networking→#3795BD, etc.). `general` returns random color. | 20-51 |
| `hex2rgb($hex)` | Converts hex color to RGB string. Handles 1-6 char hex codes. | 159-184 |

#### Image Generation:
1. Default size: 600×400, overridable via URL segment
2. Font: `../../fonts/VERDANA0.TTF` (relative path)
3. Title truncated at 140 chars, auto-wrapped by word boundaries
4. Font size: `width / 25`
5. Category label rendered at bottom: `[ Category Name ]`
6. Branding: "Reads by TAO" at very bottom
7. Output: `Content-Type: image/png` + `imagepng()`

---

### `index_vk.php` (Framework-integrated mode)
- **Role**: Same image generation logic but uses `taoh_parse_url()` for URL parsing and `TAOH_PLUGIN_PATH` for font path.
- **URL Parsing**: `taoh_parse_url(2)` = title, `taoh_parse_url(3)` = category, `taoh_parse_url(4)` = dimensions
- **Font path**: `TAOH_PLUGIN_PATH/assets/fonts/VERDANA0.TTF`
- **Identical generation logic** to `index.php` — only input sourcing differs.

---

### Static Assets
- `leaf.png`, `sun.jpg` — Static images (likely used as fallbacks or decorative elements)
- `images/` — Subdirectory for additional image assets

---

## Category Color Map
| Category | Color |
|----------|-------|
| interview | #3E54AC |
| job-search | #4E6E81 |
| networking | #3795BD |
| resume | #AD7BE9 |
| jobs-of-future | #C92C6D |
| branding | #609EA2 |
| mentor-coach | #865DFF |
| conflict-management | #537FE7 |
| growth-mindset | #183A1D |
| handling-change | #F0A04B |
| leadership | #5D9C59 |
| career-development | #2B3467 |
| learning | #3A98B9 |
| mindfulness | #E96479 |
| future-of-work | #4D455D |
| organization | #060047 |
| general-work-strategy | #443C68 |
| productivity | #40513B |
| upskilling | #8D7B68 |

## Dependencies
- PHP GD library (`imagecreate`, `imagettftext`, `imagepng`)
- Font file: `VERDANA0.TTF`

## Key Edit Points
- **Add new category color**: Edit `$cat_arr` in `taoh_get_category_color()` in `index.php`
- **Change image dimensions**: Modify default `$img['width']`/`$img['height']` values
- **Change font**: Edit `$font` variable path
- **Change branding text**: Edit `$text_foot = 'Reads by TAO'` line
- **Change title truncation**: Edit the `140` char limit in `if ($text_len > 140)`
