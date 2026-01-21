# Events Module Documentation

## Overview

The `app/events` directory contains a comprehensive event management system for the TAO platform. This module handles virtual, in-person, and hybrid events including event creation, RSVP management, live chat/networking, exhibitor management, speaker management, sponsor handling, and more.

## Directory Structure

```
app/events/
├── actions/
│   └── main.php              # RSVP action handlers (add/delete RSVP)
├── includes/
│   └── error.php             # Custom error page template with retry functionality
├── main.php                  # Main router/controller - routes requests to appropriate handlers
├── functions.php             # Core utility functions for event status and display
├── adapter.php               # Networking adapter - handles room creation for events
├── NtwAdapterEvents.php      # Network adapter class for channel/room management
├── ajax.php                  # AJAX endpoints for event data retrieval
├── chat.php                  # Event lobby/chat interface (main event experience)
├── events.php                # Event listing page
├── events_detail.php         # Individual event detail page
├── events_agenda.php         # Event agenda/schedule display
├── events_exhibitors.php     # Exhibitor listing and management
├── events_speakers.php       # Speaker listing page
├── events_sponsor_detail.php # Sponsor detail view
├── all_events.php            # All events listing (alternative view)
├── ... (60+ PHP files)
└── index.php                 # Security redirect
```

## Core Files

### main.php (Router)
The main entry point that routes all `/events/*` requests. Key routes include:

| Route | Handler | Description |
|-------|---------|-------------|
| `/events` | `events.php` | Default event listing |
| `/events/d/{slug}` | `events_detail.php` | Event detail page |
| `/events/chat/id/events/{token}` | `chat.php` | Event lobby/networking |
| `/events/rsvp/{token}` | `rsvp.php` | RSVP form |
| `/events/exhibitors/{slug}` | `events_exhibitor_detail_new.php` | Exhibitor details |
| `/events/speakers/{slug}` | `events_speaker_detail.php` | Speaker details |
| `/events/sponsor/{slug}` | `events_sponsor_detail.php` | Sponsor details |
| `/events/hall/{slug}` | `events_hall_listing_page.php` | Hall/booth listing |
| `/events/allevents` | `all_events.php` | All events view |
| `/events/club` | `adapter.php` | Networking club adapter |
| `/events/session_slot` | `events_session_form.php` | Session scheduling |

### functions.php
Core utility functions:

- **`event_live_status($start_time, $end_time, $locality)`** - Returns event state: `before`, `prelive`, `live`, `postlive`, `after`
- **`event_live_state($start_time, $end_time, $event_status, $locality)`** - Determines live state with timezone support
- **`event_action_button($event_arr, $tokenkey)`** - Renders appropriate action button based on event/RSVP state
- **`event_time_display($input_date, $locality, $timezone_abbr, $input, $format)`** - Formats event times with timezone conversion
- **`event_state_widget($event_arr, $hide_rsvp)`** - Renders event details widget (sidebar)
- **`event_state_center_widget($event_arr, $hide_rsvp)`** - Renders centered event details widget
- **`changeDateTimezone($date, $to, $from, $targetFormat, $dstcheck)`** - Converts dates between timezones
- **`field_locations($coordinates, $location, $geohash, $js)`** - Location selector field

### NtwAdapterEvents.php
Network adapter class for managing event networking rooms and channels:

**Key Methods:**
- `generateChannelId($channel_slug_data)` - Creates unique channel identifiers
- `constructDefaultChannelInfo($room_slug, $channels_input_data)` - Builds default channel configuration
- `constructEventSessionChannelInfo($eventtoken, $channels_input_data)` - Creates session-based channels
- `constructEventExhibitorChannelInfo($eventtoken, $channels_input_data)` - Creates exhibitor channels
- `createBulkRoomInfoChannels($room_info, $my_ptoken)` - Batch creates networking channels
- `updateChannelInfo($roomslug, $keyword, $key, $channel_info)` - Updates existing channel
- `deleteChannel($roomslug, $keyword, $key, $channel_info)` - Removes a channel
- `generateRoomSlug($input_data)` - Creates room identifiers based on event/geo data
- `constructAndCreateRoomInfo($sess_user_info, $input_data)` - Full room creation with profiles
- `updateRoomInfo($sess_user_info, $input_data)` - Updates existing room configuration

### ajax.php
AJAX endpoints for data retrieval:

- `events_get_rooms()` - Fetches available chat rooms
- `events_get()` - Retrieves event listings with filters
- `events_get_tao()` - TAO-specific event retrieval
- `events_get_all_tao()` - All events for TAO platform

## Event States

Events progress through these lifecycle states:

| State | Description |
|-------|-------------|
| `before` | Event has not started yet |
| `prelive` | Event day, but session not started |
| `live` | Event currently in progress |
| `postlive` | Event day, but session ended |
| `after` | Event has completely ended |

## Event Types

The system supports three event types:

1. **Virtual** - Online-only events with lobby/streaming
2. **In-Person** - Physical venue events with location mapping
3. **Hybrid** - Combined virtual and in-person attendance

## Key Constants

```php
TAOH_EVENTS_EVENT_UNPUBLISHED  = 1
TAOH_EVENTS_EVENT_SUSPENDED    = 2
TAOH_EVENTS_EVENT_EXPIRED      = 3
TAOH_EVENTS_EVENT_PUBLISHED    = 4
TAOH_EVENTS_EVENT_ACTIVE       = 5
TAOH_EVENTS_EVENT_LIVEABLE     = 6
TAOH_EVENTS_EVENT_EARLY_START  = 7
TAOH_EVENTS_EVENT_START        = 8
TAOH_EVENTS_EVENT_STOP         = 9

TAOH_EVENTS_RSVP_SUSPENDED    = 1
TAOH_EVENTS_RSVP_NEW          = 2
TAOH_EVENTS_RSVP_NOTMATCHED   = 3
TAOH_EVENTS_RSVP_NOMATCH      = 4
TAOH_EVENTS_RSVP_MATCH        = 5
TAOH_EVENTS_RSVP_NOMATCH_LIVE = 6
TAOH_EVENTS_RSVP_MATCH_LIVE   = 7
```

## Channel Types

Networking channels are categorized as:

- `TAOH_CHANNEL_DEFAULT` (1) - General discussion channels
- `TAOH_CHANNEL_EXHIBITOR` (2) - Exhibitor booth channels
- `TAOH_CHANNEL_SESSION` (7) - Session/speaker channels

## User Profiles/Roles

The room system defines these user profiles:

| Profile | Description | Permissions |
|---------|-------------|-------------|
| `attendee` | Regular event participant | Basic access, DM, reactions |
| `speaker` | Event presenter | Same as attendee |
| `sponsor` | Event sponsor (gold/silver tiers) | Enhanced visibility |
| `exhibitor` | Booth operator | Dedicated channel |
| `recruiter` | Job poster | Can open job threads |
| `moderator` | Content moderator | Standard moderation |
| `organizer` | Event owner | Full access |

## Page Files

### Event Listing & Discovery
- `events.php` - Main event listing with filters
- `all_events.php` - Alternative all-events view
- `events_new.php` - New events layout
- `events_landing.php` - Landing page template
- `visitor.php` - Non-logged-in visitor view
- `search.php` / `search_new.php` - Event search functionality

### Event Details
- `events_detail.php` - Full event detail page
- `new_desc_page.php` - New description page layout
- `events_popup.php` - Event preview popup

### Event Lobby & Chat
- `chat.php` - Main event lobby interface (192KB - largest file)
- `chat-modal.php` - Chat modal component
- `chat_session.php` - Session-specific chat
- `chat_exhibitor.php` - Exhibitor chat interface
- `club.php` - Networking club features

### Agenda & Schedule
- `events_agenda.php` - Full agenda display
- `events_agenda_default_banner.php` - Agenda banner component
- `events_agenda_default_list.php` - Agenda list view

### Speakers
- `events_speakers.php` - Speaker listing
- `events_speaker_detail.php` - Speaker profile page
- `events_speaker_page.php` - Alternative speaker view
- `events_speakder_details.php` - Speaker details (typo in filename)
- `events_speaker_default_list.php` - Default speaker list

### Exhibitors
- `events_exhibitors.php` - Exhibitor listing
- `events_exhibitor_detail_new.php` - Exhibitor detail page
- `events_exhibitor_form_new.php` - Exhibitor registration form
- `events_exhibitor_default_list.php` - Default exhibitor list

### Sponsors
- `events_sponsor_detail.php` - Sponsor detail page
- `sponsor_details_modal.php` - Sponsor popup modal

### Halls & Venues
- `events_hall.php` - Virtual hall interface
- `events_hall_listing_page.php` - Hall directory
- `events_hall_exhibitors.php` - Exhibitors in hall
- `events_hall_rsvp.php` - Hall-specific RSVP
- `events_lobby_hall.php` - Lobby hall view

### RSVP & Registration
- `events_rsvp_default_list.php` - RSVP listing
- `events_rsvp_directory.php` - RSVP directory/lookup
- `events_session_form.php` - Session signup form

### Tables & Networking
- `events_tables.php` - Networking tables feature
- `tables.php` - Table management

### AI & Search
- `events_tao_ai.php` - TAO AI integration
- `events_tao_ai_new.php` - Updated AI features
- `events_tao_search.php` - AI-powered search

### Exports
- `export_rsvp.php` - Export RSVP data
- `export_raffle_entries.php` - Export raffle entries
- `export_raffle_feedback.php` - Export raffle feedback

### Utilities
- `event_health_check.php` - System health monitoring
- `event_status.php` - Event status checker
- `event_upgrade_modal.php` - Ticket upgrade modal
- `event_video_modal.php` - Video player modal
- `events_footer.php` - Footer component
- `events_rooms.php` - Room management
- `room_update.php` - Room update handler
- `next.php` - Navigation helper
- `swag_wall.php` - Swag/merch display
- `tao_connect_html.php` - TAO Connect integration

## API Endpoints

The module communicates with these API endpoints:

- `events.event.get` - Fetch event details
- `events.rsvp.get` - Fetch RSVP information
- `events.rsvp.post` - Submit RSVP
- `events.content.get` - Fetch event content (speakers, exhibitors, sponsors)
- `events.content.save.list` - Fetch saved/bookmarked items
- `events.get` - List events with filtering
- `events.rsvp.users.count` - Get attendee count

## Authentication

- Login required for: RSVP, chat/networking, exports, session forms
- Public access for: Event listing, event details, visitor views

## Caching

The module uses extensive caching:
- Event details: `event_detail_{eventtoken}`
- Event metadata: `event_MetaInfo_{eventtoken}_{type}_{search}`
- Saved items: `event_Saved_{eventtoken}`

## Error Handling

The `includes/error.php` provides a user-friendly error page with:
- Auto-refresh every 60 seconds
- Go back / Refresh buttons
- Contextual error messages based on error source

## Geo Features

- Country-locked events (`country_locked` flag)
- Timezone-aware scheduling
- Room slug generation based on geography for large events (500+ attendees)
