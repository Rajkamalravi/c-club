# Tools Module (`core/tools/`)

## Purpose
Collection of wellness/productivity mini-tools. Currently includes a Pomodoro timer and a breathing exercise (Inhale-Exhale). Falls back to the blog module for unknown routes.

---

## File-by-File Breakdown

### `main.php` (Router)
- **Role**: Routes based on `taoh_parse_url(1)`.
- **Routing**:
  | Slug | Handler |
  |------|---------|
  | `inhale-exhale` | `inhale-exhale.php` |
  | `pomodoro` | `pomodoro.php` |
  | default | `../blog/main.php` (redirects to blog) |

---

### `pomodoro.php` (Pomodoro Timer)
- **Role**: Self-contained Pomodoro timer page with inline CSS/JS.
- **Layout**: Uses `taoh_get_header()` / `taoh_get_footer()`. Shows ObviousBaba mascot image from `TAOH_OPS_PREFIX/images/obviousbaba.png`.
- **Timer Modes**:
  - Work: 25 minutes
  - Short Break: 5 minutes
  - Long Break: 15 minutes
- **Implementation**: Pure JS `pomodoro` object with `setInterval` at 1-second ticks. Visual filler bar rises as timer progresses.
- **No server interaction** — entirely client-side.

---

### `inhale-exhale.php` (Breathing Exercise)
- **Role**: Guided breathing animation page.
- **Cycle**: Inhale (4s) → Hold (7s) → Exhale (8s) → repeat
- **Implementation**: CSS gradient animation + JS `setTimeout` cycle. Three CSS classes toggle background animation direction:
  - `.inex-container` (inhale — normal animation)
  - `.inex-container-hold` (hold — static dark)
  - `.inex-container-exhale` (exhale — reverse animation)
- **Uses**: jQuery for class toggling, `taoh_get_header()`/`taoh_get_footer()`

---

### `index.php`
- Security guard (`// silence is golden`).

---

## Key Edit Points
- **Add new tool**: Edit `main.php` switch, add new `case` + PHP file
- **Change Pomodoro durations**: Edit `startWork` (25), `startShortBreak` (5), `startLongBreak` (15) in `pomodoro.php`
- **Change breathing timing**: Edit `setTimeout` values in `inhale-exhale.php` (inhale: 4000, hold: 7000, exhale: 8000)
