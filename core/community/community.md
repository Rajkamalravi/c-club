# Community Module (`core/community/`)

## Purpose
Simple redirect module — redirects users from the `/community` URL to the networking/club page.

---

## File-by-File Breakdown

### `main.php`
- **Role**: Single redirect. Immediately calls:
  ```php
  taoh_redirect(TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME . '/club');
  taoh_exit();
  ```
- **No logic, no auth check, no data processing.**
- **Depends on**: `TAOH_SITE_URL_ROOT`, `TAOH_NETWORKPAGE_NAME` constants.

### `index.php`
- Security guard (`// silence is golden`).

---

## Key Edit Points
- **Change redirect target**: Edit `main.php` line 2, modify the redirect URL path.
