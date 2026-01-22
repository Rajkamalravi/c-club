# Events V2 Module Documentation

## Overview

The `events-v2` module is a modernized, user-friendly events system running parallel to the existing `events` module. Built with Bootstrap 5 and modern vanilla JavaScript, it provides a responsive, accessible experience for event discovery, viewing, and RSVP.

## Key Features

- **Modern UI/UX**: Clean, responsive design using Bootstrap 5.3
- **Mobile-First**: Optimized for all device sizes
- **No jQuery Dependency**: Pure vanilla JavaScript (ES6+)
- **Reuses Existing Backend**: Same APIs as the original events module
- **Parallel Operation**: Runs at `/events-v2/` without affecting `/events/`

## Directory Structure

```
app/events-v2/
├── main.php                    # Main router/controller
├── functions.php               # Helper functions (extends existing)
├── ajax.php                    # AJAX endpoints
├── events-v2.md               # This documentation
├── index.php                   # Security redirect
├── actions/
│   └── main.php               # RSVP form handlers
├── includes/
│   ├── head.php               # Bootstrap 5 CSS/JS includes
│   ├── header.php             # Navigation header
│   ├── footer.php             # Page footer with JS
│   ├── error.php              # Error page template
│   └── components/
│       ├── event-card.php     # Event card component
│       ├── ticket-card.php    # Ticket selection card
│       ├── filters.php        # Filter sidebar
│       └── pagination.php     # Pagination component
└── pages/
    ├── listing.php            # Event listing/discovery
    ├── detail.php             # Event detail page
    ├── rsvp.php               # Ticket type selection
    ├── rsvp-form.php          # Registration form
    └── confirmation.php       # RSVP success page

assets/css/events-v2/
├── variables.css              # CSS custom properties
├── main.css                   # Base styles
├── components.css             # Reusable components
├── listing.css                # Listing page styles
├── detail.css                 # Detail page styles
└── rsvp.css                   # RSVP flow styles

assets/js/events-v2/
├── main.js                    # Main entry point
├── listing.js                 # Listing page logic
├── detail.js                  # Detail page logic
├── rsvp.js                    # RSVP flow logic
└── utils/
    ├── api.js                 # API wrapper
    └── helpers.js             # DOM utilities
```

## URL Routes

| URL Pattern | Handler | Description |
|-------------|---------|-------------|
| `/events-v2/` | `pages/listing.php` | Event listing with filters |
| `/events-v2/d/{slug}` | `pages/detail.php` | Event detail page |
| `/events-v2/rsvp/{token}` | `pages/rsvp.php` | Ticket selection |
| `/events-v2/rsvp-form/{token}` | `pages/rsvp-form.php` | Registration form |
| `/events-v2/confirmation/{token}` | `pages/confirmation.php` | Success page |
| `/events-v2/ajax/{action}` | `ajax.php` | AJAX endpoints |

## Technology Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| Bootstrap | 5.3.3 | CSS Framework & Components |
| Font Awesome | 6.x | Icons |
| Bootstrap Icons | Latest | Additional icons |
| Inter Font | Variable | Typography |
| Vanilla JS | ES6+ | Interactions |
| Luxon.js | Existing | Date/time handling |

## CSS Architecture

### Custom Properties (variables.css)

```css
:root {
    /* Colors */
    --ev2-primary: #2557A7;
    --ev2-primary-hover: #1e4a8f;
    --ev2-primary-light: #e8f0fe;
    --ev2-success: #28a745;
    --ev2-warning: #ffc107;
    --ev2-danger: #dc3545;

    /* Typography */
    --ev2-font-family: 'Inter', system-ui, sans-serif;
    --ev2-font-size-base: 1rem;

    /* Spacing */
    --ev2-spacing-1: 0.25rem;
    --ev2-spacing-2: 0.5rem;
    /* ... up to --ev2-spacing-16 */

    /* Shadows */
    --ev2-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
    --ev2-shadow-md: 0 4px 6px rgba(0,0,0,0.1);

    /* Transitions */
    --ev2-transition-fast: 150ms ease;
    --ev2-transition-normal: 300ms ease;
}
```

### File Organization

1. **variables.css** - Design tokens only
2. **main.css** - Base styles, layout, utilities
3. **components.css** - Reusable component styles
4. **listing.css** - Page-specific: listing
5. **detail.css** - Page-specific: detail
6. **rsvp.css** - Page-specific: RSVP flow

## JavaScript Architecture

### Global Objects

| Object | File | Purpose |
|--------|------|---------|
| `EV2Helpers` | `utils/helpers.js` | DOM utilities, formatting |
| `EV2Api` | `utils/api.js` | API communication |
| `EV2App` | `main.js` | Core initialization |
| `EV2Listing` | `listing.js` | Listing page logic |
| `EV2Detail` | `detail.js` | Detail page logic |
| `EV2Rsvp` | `rsvp.js` | RSVP flow logic |

### Helper Functions (EV2Helpers)

```javascript
// DOM Selection
EV2Helpers.$('#id')           // querySelector
EV2Helpers.$$('.class')       // querySelectorAll

// Event Binding
EV2Helpers.on(el, 'click', fn)
EV2Helpers.onAll('.btn', 'click', fn)
EV2Helpers.delegate(parent, 'click', '.child', fn)

// Utilities
EV2Helpers.debounce(fn, 300)
EV2Helpers.throttle(fn, 100)
EV2Helpers.formatDate(date, format)
EV2Helpers.formatTime(date)
EV2Helpers.getRelativeTime(date)

// UI Feedback
EV2Helpers.showToast(message, type)
EV2Helpers.showLoading(container)
EV2Helpers.hideLoading(container)
```

### API Wrapper (EV2Api)

```javascript
// Fetch events
const events = await EV2Api.getEvents({ search, type, from_date });

// Get single event
const event = await EV2Api.getEvent(eventtoken);

// Get event metadata (speakers, exhibitors, sponsors)
const meta = await EV2Api.getEventMeta(eventtoken);

// Submit RSVP
const result = await EV2Api.submitRsvp(formData);
```

## PHP Helper Functions

### functions.php

Extends existing `/app/events/functions.php`:

```php
// Get events list with filters
events_v2_get_list($filters, $limit, $offset)

// Get single event details
events_v2_get_detail($eventtoken)

// Get event meta (speakers, exhibitors, sponsors)
events_v2_get_meta($eventtoken)

// Format date for display
events_v2_format_date($input_date, $locality, $format)

// Format time for display
events_v2_format_time($input_date, $locality)

// Get status badge info
events_v2_get_status_badge($event_arr)
// Returns: ['status' => 'live', 'class' => 'bg-success', 'label' => 'Live Now']

// Get event type badge
events_v2_get_type_badge($event_type)
// Returns: ['class' => 'bg-info', 'icon' => 'bi-camera-video', 'label' => 'Virtual']

// Truncate text
events_v2_truncate($text, $length)

// Get event image with fallback
events_v2_get_image($event_data)

// Check if user has RSVP'd
events_v2_has_rsvp($event_arr)

// Generate event URL
events_v2_event_url($eventtoken, $slug)

// Generate RSVP URL
events_v2_rsvp_url($eventtoken)
```

## Event States

Same as existing events module:

| State | Description |
|-------|-------------|
| `before` | Event has not started |
| `prelive` | Event day, session not started |
| `live` | Event currently in progress |
| `postlive` | Event day, session ended |
| `after` | Event completely ended |

## Event Types

| Type | Icon | Badge Color |
|------|------|-------------|
| Virtual | `bi-camera-video` | Info (blue) |
| In-Person | `bi-geo-alt` | Dark |
| Hybrid | `bi-broadcast` | Purple |

## RSVP Flow

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│  Event List  │ ──▶ │ Event Detail │ ──▶ │ Select Ticket│ ──▶ │  Fill Form   │
│  /events-v2  │     │ /events-v2/d │     │ /rsvp/{token}│     │ /rsvp-form   │
└──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘
                                                                       │
                                                                       ▼
                                                               ┌──────────────┐
                                                               │ Confirmation │
                                                               │/confirmation │
                                                               └──────────────┘
```

### Progress Steps UI

1. **Select Ticket** - Choose ticket type (Free, VIP, etc.)
2. **Your Details** - Fill registration form
3. **Confirmation** - Success with add-to-calendar

## API Endpoints Used

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `events.get` | GET | List events with filters |
| `events.event.get` | GET | Single event details |
| `events.content.get` | GET | Speakers, exhibitors, sponsors |
| `events.rsvp.get` | GET | RSVP information |
| `events.rsvp.post` | POST | Submit RSVP |

## Constants

```php
// Module
define('TAOH_CURR_APP_SLUG', 'events-v2');
define('TAOH_EVENTS_V2_URL', TAOH_SITE_URL_ROOT . '/events-v2');

// Event Status (reused from events module)
TAOH_EVENTS_EVENT_UNPUBLISHED  = 1
TAOH_EVENTS_EVENT_SUSPENDED    = 2
TAOH_EVENTS_EVENT_EXPIRED      = 3
TAOH_EVENTS_EVENT_PUBLISHED    = 4
TAOH_EVENTS_EVENT_ACTIVE       = 5
TAOH_EVENTS_EVENT_LIVEABLE     = 6
TAOH_EVENTS_EVENT_EARLY_START  = 7
TAOH_EVENTS_EVENT_START        = 8
TAOH_EVENTS_EVENT_STOP         = 9

// RSVP Status
TAOH_EVENTS_RSVP_SUSPENDED    = 1
TAOH_EVENTS_RSVP_NEW          = 2
TAOH_EVENTS_RSVP_NOTMATCHED   = 3
TAOH_EVENTS_RSVP_NOMATCH      = 4
TAOH_EVENTS_RSVP_MATCH        = 5
TAOH_EVENTS_RSVP_NOMATCH_LIVE = 6
TAOH_EVENTS_RSVP_MATCH_LIVE   = 7
```

## Authentication

- **Public Access**: Event listing, event details
- **Login Required**: RSVP pages, form submission, confirmation

Authentication redirects to `TAOH_LOGIN_URL` if not logged in.

## Responsive Breakpoints

Following Bootstrap 5 breakpoints:

| Breakpoint | Min Width | Grid Columns |
|------------|-----------|--------------|
| xs | 0 | 1 |
| sm | 576px | 1-2 |
| md | 768px | 2 |
| lg | 992px | 3 |
| xl | 1200px | 3-4 |
| xxl | 1400px | 4 |

## Accessibility Features

- Semantic HTML5 elements
- ARIA labels on interactive elements
- Keyboard navigation support
- Focus indicators
- 4.5:1 color contrast ratios
- Screen reader friendly

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Optimizations

- Lazy loading images
- Debounced search input
- Throttled scroll handlers
- CSS containment where applicable
- Minimal JavaScript dependencies

## Development Notes

### Adding a New Page

1. Create PHP file in `pages/`
2. Add route in `main.php` switch statement
3. Create page-specific CSS in `assets/css/events-v2/`
4. Create page-specific JS in `assets/js/events-v2/`
5. Include CSS in `includes/head.php`
6. Include JS in `includes/footer.php`

### Component Usage

```php
// Include event card component
include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/components/event-card.php');
ev2_render_event_card($event_data);
```

### Toast Notifications

```javascript
// Success
EV2Helpers.showToast('RSVP submitted successfully!', 'success');

// Warning
EV2Helpers.showToast('Please select a ticket', 'warning');

// Error
EV2Helpers.showToast('Something went wrong', 'danger');
```

## Comparison with Original Events Module

| Feature | events | events-v2 |
|---------|--------|-----------|
| CSS Framework | Custom + Bootstrap 4 | Bootstrap 5.3 |
| JavaScript | jQuery | Vanilla ES6+ |
| Responsive | Partial | Full mobile-first |
| Accessibility | Limited | WCAG 2.1 AA |
| Code Style | Legacy | Modern modules |
| Dependencies | Many | Minimal |
