# Dojo.js Implementation Guide

## Overview

Dojo.js is a smart messaging system that displays contextual, state-based messages to users at strategic time intervals (0th, 1st, 5th, and 10th minutes). It helps drive user engagement by showing relevant calls-to-action based on their current state and context.

## Quick Start

### 1. Include the Script

```html
<script src="dojo.js"></script>
```

### 2. Add a Message Container

Add this HTML element where you want messages to appear:

```html
<div id="dojo-message-container" style="display:none; padding:15px; background:#f0f0f0; margin:10px 0; border-radius:5px;"></div>
```

### 3. Configure State Variables

Set these global variables **before** including dojo.js or in a separate script tag:

```javascript
// User state
var d_user_logged = 1;                    // 0 = not logged in, 1 = logged in
var d_user_profile_completed = 1;         // 0 = incomplete, 1 = complete
var d_user_profile_type = 'professional'; // 'professional' or other types

// Event state
var d_is_sponsorship_available = 0;       // 0 = no, 1 = yes
var d_rsvp_done = 1;                      // 0 = not registered, 1 = registered
var d_is_session_added = 0;               // 0 = no session, 1 = session added
var d_is_exhibitor_added = 0;             // 0 = no exhibit, 1 = exhibit added
var d_is_event_live = 0;                  // 0 = not live, 1 = live

// Links for CTAs (use empty string or actual URLs)
var SETTINGSLINK = '<a href="/settings">here</a>';
var EVENTREGISTERLINK = '<a href="/register">here</a>';
var SESSIONREGISTERLINK = '<a href="/session">here</a>';
var EXHIBITREGISTERLINK = '<a href="/exhibit">here</a>';
var EVENTSLINK = '<a href="/events">here</a>';
var COMMENTLINK = '<a href="#comments">here</a>';

// Current page context
var current_page = 'events'; // 'events' or other page types
```

### 4. Auto-Start

If a `#dojo-message-container` element exists, messages will start automatically on page load.

## Manual Control

### Start Messages

```javascript
// Start with random messages
startDojoMessages();

// Start with sequential messages
startDojoMessages('dojo-message-container', false);

// Use custom container
startDojoMessages('my-custom-container-id', true);
```

### Stop Messages

```javascript
stopDojoMessages();
```

### Restart Messages

Useful when user state changes (e.g., after login):

```javascript
// Update state
d_user_logged = 1;
d_user_profile_completed = 1;

// Restart with new messages
restartDojoMessages();
```

## Message Logic Flow

The system selects messages based on this priority order:

### 1. Not Logged In + Events Page
- Shows event registration encouragement
- Example: "Don't just watch - join. Reserve your spot now"

### 2. Logged In + Profile Incomplete
- Prompts profile completion
- Example: "Complete your profile to unlock full access - takes less than a minute"

### 3. Logged In + Professional Profile + Events Page
- Encourages event registration for professionals
- Example: "Few seconds to secure your seat - register now"

### 4. Logged In + Non-Professional + Events Page (No Sponsorship)
- Shows ticket selection prompts
- Example: "Seats are filling fast - check your ticket options and register now"

### 5. Logged In + Non-Professional + Events Page (With Sponsorship)
- Highlights sponsorship opportunities
- Example: "Explore sponsorship options - your best seat might come with extra perks"

### 6. RSVP Done + No Session Added
- Prompts session completion
- Example: "You're registered - now complete your session to confirm participation"

### 7. RSVP Done + No Exhibitor Added
- Prompts exhibitor form completion
- Example: "You're registered - now complete your exhibitor form to confirm your booth"

### 8. All Complete + Event Not Live
- Encourages exploration and networking
- Example: "All set for this event — explore other upcoming ones while you wait"

## Message Timing

Messages appear at these intervals after page load:

| Minute | Delay | When it appears |
|--------|-------|-----------------|
| 0th | 0ms | Immediately on load |
| 1st | 60,000ms | 1 minute after load |
| 5th | 300,000ms | 5 minutes after load |
| 10th | 600,000ms | 10 minutes after load |

## Advanced Usage

### Display a Specific Message

```javascript
var message = getMessageByMinute(5); // Get message for 5th minute
displayMessage(message, 'dojo-message-container');
```

### Hide Messages

```javascript
hideMessage('dojo-message-container');
```

### Get Message Array

```javascript
var messages = getMessageArray(); // Returns current state's message array
console.log(messages);
```

### Sequential vs Random

```javascript
// Random message selection (default)
startDojoMessages('dojo-message-container', true);

// Sequential message selection (0th minute = 1st message, 1st minute = 2nd message, etc.)
startDojoMessages('dojo-message-container', false);
```

## Example Implementations

### Example 1: Basic Events Page

```html
<!DOCTYPE html>
<html>
<head>
    <title>Event Page</title>
</head>
<body>
    <h1>Upcoming Event</h1>

    <!-- Message container -->
    <div id="dojo-message-container" style="display:none; padding:15px; background:#fff3cd; margin:20px 0; border-left:4px solid #ffc107; border-radius:4px;"></div>

    <div class="event-details">
        <!-- Event content here -->
    </div>

    <script>
        // Configure state
        var d_user_logged = 0;
        var d_user_profile_completed = 0;
        var current_page = 'events';
        var EVENTREGISTERLINK = '<a href="/register" style="color:#007bff; font-weight:bold;">Register here</a>';
    </script>
    <script src="dojo.js"></script>
</body>
</html>
```

### Example 2: Post-Login State Update

```javascript
// After user logs in via AJAX
function onLoginSuccess() {
    // Update state
    d_user_logged = 1;
    d_user_profile_completed = 0; // Profile still incomplete

    // Restart messages to show profile completion prompts
    restartDojoMessages();
}
```

### Example 3: Custom Styling

```html
<style>
#dojo-message-container {
    display: none;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin: 20px 0;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    font-size: 16px;
    line-height: 1.6;
}

#dojo-message-container a {
    color: #ffd700;
    text-decoration: underline;
    font-weight: bold;
}

#dojo-message-container a:hover {
    color: #ffed4e;
}
</style>
```

### Example 4: Dynamic State Management

```javascript
// Check and update state periodically
function updateDojoState() {
    // Fetch current user state from API
    fetch('/api/user-state')
        .then(response => response.json())
        .then(data => {
            var stateChanged = false;

            if (d_user_profile_completed !== data.profile_completed) {
                d_user_profile_completed = data.profile_completed;
                stateChanged = true;
            }

            if (d_rsvp_done !== data.rsvp_done) {
                d_rsvp_done = data.rsvp_done;
                stateChanged = true;
            }

            if (d_is_session_added !== data.session_added) {
                d_is_session_added = data.session_added;
                stateChanged = true;
            }

            // Restart messages if state changed
            if (stateChanged) {
                restartDojoMessages();
            }
        });
}

// Check state every 30 seconds
setInterval(updateDojoState, 30000);
```

## API Reference

### Configuration Variables

| Variable | Type | Values | Description |
|----------|------|--------|-------------|
| `d_user_logged` | number | 0, 1 | User login status |
| `d_user_profile_completed` | number | 0, 1 | Profile completion status |
| `d_user_profile_type` | string | 'professional', etc | User profile type |
| `d_is_sponsorship_available` | number | 0, 1 | Sponsorship availability |
| `d_rsvp_done` | number | 0, 1 | Event registration status |
| `d_is_session_added` | number | 0, 1 | Session submission status |
| `d_is_exhibitor_added` | number | 0, 1 | Exhibitor registration status |
| `d_is_event_live` | number | 0, 1 | Event live status |
| `current_page` | string | 'events', etc | Current page context |
| `SETTINGSLINK` | string | HTML/URL | Profile settings link |
| `EVENTREGISTERLINK` | string | HTML/URL | Event registration link |
| `SESSIONREGISTERLINK` | string | HTML/URL | Session registration link |
| `EXHIBITREGISTERLINK` | string | HTML/URL | Exhibitor registration link |
| `EVENTSLINK` | string | HTML/URL | Events list link |
| `COMMENTLINK` | string | HTML/URL | Comments section link |

### Functions

#### `getMessageArray()`
Returns the appropriate message array based on current state.

**Returns:** `Array<string>` - Array of contextual messages

---

#### `getMessageByMinute(minute)`
Get a random message for a specific minute interval.

**Parameters:**
- `minute` (number): One of 0, 1, 5, or 10

**Returns:** `string` - Selected message or empty string

---

#### `getMessageByMinuteSequential(minute)`
Get a sequential message for a specific minute interval.

**Parameters:**
- `minute` (number): One of 0, 1, 5, or 10

**Returns:** `string` - Selected message or empty string

---

#### `displayMessage(message, containerId)`
Display a message in a container with fade-in animation.

**Parameters:**
- `message` (string): Message to display
- `containerId` (string, optional): Container element ID (default: 'dojo-message-container')

---

#### `hideMessage(containerId)`
Hide the message container with fade-out animation.

**Parameters:**
- `containerId` (string, optional): Container element ID (default: 'dojo-message-container')

---

#### `startDojoMessages(containerId, useRandom)`
Start automatic message display at scheduled intervals.

**Parameters:**
- `containerId` (string, optional): Container element ID (default: 'dojo-message-container')
- `useRandom` (boolean, optional): Use random messages (default: true)

---

#### `stopDojoMessages()`
Stop all scheduled messages.

---

#### `restartDojoMessages(containerId, useRandom)`
Restart the message sequence (useful when state changes).

**Parameters:**
- `containerId` (string, optional): Container element ID (default: 'dojo-message-container')
- `useRandom` (boolean, optional): Use random messages (default: true)

## Best Practices

1. **Set state variables before loading dojo.js** or immediately after
2. **Use meaningful CTAs** - Make links actionable with clear text
3. **Update state dynamically** - Call `restartDojoMessages()` when user state changes
4. **Style appropriately** - Make messages visible but not intrusive
5. **Test all states** - Verify messages appear correctly for each user scenario
6. **Mobile-friendly** - Ensure message container is responsive
7. **Accessibility** - Use proper contrast ratios and readable font sizes

## Troubleshooting

### Messages not appearing?
- Check if `#dojo-message-container` exists in DOM
- Verify state variables are set correctly
- Check browser console for errors
- Ensure dojo.js is loaded after DOM or state variables

### Wrong messages showing?
- Review state variable values
- Check `current_page` variable matches your context
- Use `getMessageArray()` to debug which array is being used

### Messages appearing at wrong times?
- Check if `restartDojoMessages()` is being called unintentionally
- Verify no other scripts are interfering with timers
- Use browser dev tools to inspect timeout IDs

### Want to test immediately?
```javascript
// Force show message for 5th minute right now
var msg = getMessageByMinute(5);
displayMessage(msg);
```

## Performance Notes

- Messages use `setTimeout`, not `setInterval` - more efficient
- Timers are automatically cleaned up with `stopDojoMessages()`
- Minimal DOM manipulation (only updates one container)
- No external dependencies required

## Browser Support

Works in all modern browsers that support:
- ES5 JavaScript
- `setTimeout` / `clearTimeout`
- Basic DOM manipulation
- CSS transitions

## License & Credits

Part of the club event management system.
