/**
 * Dojo.js - Smart messaging system for user engagement
 * Displays contextual messages at 0th, 1st, 5th, and 10th minute intervals
 */

// Global configuration variables (can be overwritten)
var d_user_logged = 0;
var d_user_profile_completed = 0;
var d_user_profile_type = 'professional';
var d_is_sponsorship_available = 0;
var d_rsvp_done = 0;
var d_is_session_added = 0;
var d_is_exhibitor_added = 0;
var d_is_event_live = 0;
var SETTINGSLINK = '';
var EVENTREGISTERLINK = '';
var SESSIONREGISTERLINK = '';
var EXHIBITREGISTERLINK = '';
var EVENTSLINK = '';
var COMMENTLINK = '';
var current_page = 'events';

/**
 * Get appropriate message array based on current state
 * @returns {Array} Array of contextual messages
 */
function getMessageArray() {
    var message_array = [];

    if (!d_user_logged && current_page == 'events') {
        message_array = [
            "Don't just watch - join. Reserve your spot now",
            "2 clicks. Your seat, secured. Spots are filling fast.",
            "Claim yours before it's gone. You're already halfway there - register to confirm",
            "This event fits your profile. Lock your seat now",
            "Join the room, not just the page",
            "Future-you will thank you. Register in under a minute",
            "Your kind of event. One tap to join the guest list",
            "Get access. Get updates. Get in",
            "Ready when you are - tap 'Join' to save your spot"
        ];
    }
    else if (d_user_logged && !d_user_profile_completed) {
        message_array = [
            "Complete your profile to unlock full access - takes less than a minute " + SETTINGSLINK,
            "Your token's valid, but your profile isn't complete yet - finish it now " + SETTINGSLINK,
            "You're 60 seconds away from full access - update your profile here " + SETTINGSLINK,
            "Almost there! Complete your profile to activate all features " + SETTINGSLINK,
            "Finish your setup now to experience the full product " + SETTINGSLINK,
            "Your access is waiting - complete your profile in /settings " + SETTINGSLINK,
            "Don't stop halfway - finalize your profile to go live " + SETTINGSLINK,
            "Profile incomplete? Fix it in a minute to unlock everything " + SETTINGSLINK,
            "Quick win: complete your profile to enable all tools " + SETTINGSLINK,
            "You're verified - now complete your profile to make it official " + SETTINGSLINK
        ];
    }
    else if (d_user_profile_completed && d_user_profile_type == 'professional' && current_page == 'events') {
        message_array = [
            "Few seconds to secure your seat - register now " + EVENTREGISTERLINK,
            "Don't miss out - claim your spot before it's gone " + EVENTREGISTERLINK,
            "You're a pro - your seat deserves to be saved " + EVENTREGISTERLINK,
            "Act fast - registration closes soon " + EVENTREGISTERLINK,
            "One click to confirm your spot, don't wait " + EVENTREGISTERLINK,
            "Limited seats for professionals - grab yours now " + EVENTREGISTERLINK,
            "It takes seconds to join, and spots are vanishing " + EVENTREGISTERLINK,
            "Register now to stay ahead - seats are almost full " + EVENTREGISTERLINK,
            "You belong in this room - save your spot today " + EVENTREGISTERLINK,
            "Move quick - your perfect event seat is seconds away " + EVENTREGISTERLINK
        ];
    }
    else if (d_user_profile_completed && d_user_profile_type != 'professional' && current_page == 'events' && !d_is_sponsorship_available) {
        message_array = [
            "Seats are filling fast - check your ticket options and register now " + EVENTREGISTERLINK,
            "Claim the right ticket for your role before it's gone " + EVENTREGISTERLINK,
            "Pick your perfect ticket and secure your spot today " + EVENTREGISTERLINK,
            "A few seconds to find the best ticket - register while seats last " + EVENTREGISTERLINK,
            "Choose your ticket wisely - best spots are running out " + EVENTREGISTERLINK,
            "You're almost in - review ticket options and confirm now " + EVENTREGISTERLINK,
            "No sponsorships, no wait - grab the ticket that fits you best " + EVENTREGISTERLINK,
            "Quick check: select the right ticket to lock your seat " + EVENTREGISTERLINK,
            "Best seats go first - choose your ticket and claim yours " + EVENTREGISTERLINK,
            "One minute to go - pick your ticket and complete registration " + EVENTREGISTERLINK
        ];
    }
    else if (d_user_profile_completed && d_user_profile_type != 'professional' && current_page == 'events' && d_is_sponsorship_available) {
        message_array = [
            "Explore sponsorship options - your best seat might come with extra perks " + EVENTREGISTERLINK,
            "Check available sponsorship levels before you register - upgrades go fast " + EVENTREGISTERLINK,
            "Want visibility and VIP access? See sponsorship tiers first " + EVENTREGISTERLINK,
            "Sponsorships come with premium spots - explore before you claim " + EVENTREGISTERLINK,
            "You may qualify for sponsored benefits - check levels before booking " + EVENTREGISTERLINK,
            "Compare ticket and sponsorship options to get the best value " + EVENTREGISTERLINK,
            "Before you register, see if a sponsor tier fits you better " + EVENTREGISTERLINK,
            "Sponsors get priority seating - explore options while available " + EVENTREGISTERLINK,
            "Unlock more exposure and perks - check sponsorship levels now " + EVENTREGISTERLINK,
            "Your seat's waiting - but a sponsorship could make it shine " + EVENTREGISTERLINK
        ];
    }
    else if (d_rsvp_done && !d_is_session_added) {
        message_array = [
            "You're registered - now complete your session to confirm participation " + SESSIONREGISTERLINK,
            "Lock in your session details to finalize your spot " + SESSIONREGISTERLINK,
            "Finish your session setup - it only takes a minute " + SESSIONREGISTERLINK,
            "You're almost done - complete your session form to go live " + SESSIONREGISTERLINK,
            "Confirm your session to make sure you're on the schedule " + SESSIONREGISTERLINK,
            "Don't miss your slot - finalize your session now " + SESSIONREGISTERLINK,
            "Complete your session form to unlock full event access " + SESSIONREGISTERLINK,
            "Secure your presentation time - finish session setup today " + SESSIONREGISTERLINK,
            "One step left - complete your session details before it closes " + SESSIONREGISTERLINK,
            "You're in the event - now claim your session to make it official " + SESSIONREGISTERLINK
        ];
    }
    else if (d_rsvp_done && !d_is_exhibitor_added) {
        message_array = [
            "You're registered - now complete your exhibitor form to confirm your booth " + EXHIBITREGISTERLINK,
            "Finalize your exhibit setup - it only takes a minute " + EXHIBITREGISTERLINK,
            "Complete your exhibitor details to secure your showcase spot " + EXHIBITREGISTERLINK,
            "Don't miss visibility - finish your exhibitor form today " + EXHIBITREGISTERLINK,
            "You're almost there - complete your exhibit form to go live " + EXHIBITREGISTERLINK,
            "Confirm your booth and join the exhibitor lineup now " + EXHIBITREGISTERLINK,
            "One quick step - submit your exhibitor info to activate your space " + EXHIBITREGISTERLINK,
            "Lock your exhibit spot before the floor plan fills up " + EXHIBITREGISTERLINK,
            "Showcase your brand - complete your exhibitor form in minutes " + EXHIBITREGISTERLINK,
            "You're on the list - now complete your exhibit form to stand out " + EXHIBITREGISTERLINK
        ];
    }
    else if (d_rsvp_done && d_is_session_added && d_is_exhibitor_added && !d_is_event_live) {
        message_array = [
            "All set for this event — explore other upcoming ones while you wait " + EVENTSLINK,
            "You're ready! Discover more events that match your interests " + EVENTSLINK,
            "Event not live yet — connect early through the comments section " + COMMENTLINK,
            "Everything's done — start conversations and build collaborations now " + COMMENTLINK,
            "You're all prepped — explore similar events to expand your network " + EVENTSLINK,
            "While you wait, drop a comment to meet fellow attendees " + COMMENTLINK,
            "Great job completing setup — now check out what's next on the calendar " + EVENTSLINK,
            "Event countdown started — engage with others in comments today " + COMMENTLINK,
            "You're good to go — why not register for another event while you wait? " + EVENTSLINK,
            "Network starts now — comment, connect, and collaborate before the event begins " + COMMENTLINK
        ];
    }

    return message_array;
}

/**
 * Pick a message based on minute interval (0th, 1st, 5th, or 10th)
 * @param {number} minute - Current minute (0, 1, 5, or 10)
 * @returns {string} Selected message or empty string if invalid minute
 */
function getMessageByMinute(minute) {
    var message_array = getMessageArray();

    if (message_array.length === 0) {
        return '';
    }

    var validMinutes = [0, 1, 5, 10];
    if (validMinutes.indexOf(minute) === -1) {
        return '';
    }

    // Pick a random message from the array
    var randomIndex = Math.floor(Math.random() * message_array.length);
    return message_array[randomIndex];
}

/**
 * Alternative: Get message by index (for sequential display)
 * @param {number} minute - Current minute (0, 1, 5, or 10)
 * @returns {string} Selected message or empty string
 */
function getMessageByMinuteSequential(minute) {
    var message_array = getMessageArray();

    if (message_array.length === 0) {
        return '';
    }

    var minuteMap = {0: 0, 1: 1, 5: 2, 10: 3};
    var index = minuteMap[minute];

    if (index === undefined || index >= message_array.length) {
        index = 0; // Fallback to first message
    }

    return message_array[index];
}

/**
 * Display message in a container element
 * @param {string} message - Message to display
 * @param {string} containerId - ID of container element (default: 'dojo-message-container')
 */
function displayMessage(message, containerId) {
    containerId = containerId || 'dojo-message-container';
    var container = document.getElementById(containerId);

    if (container && message) {
        container.innerHTML = message;
        container.style.display = 'block';

        // Add fade-in animation
        container.style.opacity = '0';
        setTimeout(function() {
            container.style.transition = 'opacity 0.5s';
            container.style.opacity = '1';
        }, 10);
    }
}

/**
 * Hide message container
 * @param {string} containerId - ID of container element
 */
function hideMessage(containerId) {
    containerId = containerId || 'dojo-message-container';
    var container = document.getElementById(containerId);

    if (container) {
        container.style.opacity = '0';
        setTimeout(function() {
            container.style.display = 'none';
        }, 500);
    }
}

// Automatic message timing system
var dojoMessageTimers = [];
var dojoMessageStartTime = null;

/**
 * Start automatic message display at 0th, 1st, 5th, and 10th minute
 * @param {string} containerId - ID of container element to display messages
 * @param {boolean} useRandom - Use random messages (true) or sequential (false)
 */
function startDojoMessages(containerId, useRandom) {
    containerId = containerId || 'dojo-message-container';
    useRandom = useRandom !== undefined ? useRandom : true;

    // Clear any existing timers
    stopDojoMessages();

    // Record start time
    dojoMessageStartTime = new Date();

    // Define timing intervals in milliseconds
    var timings = [
        {minute: 0, delay: 0},           // Immediately (0th minute)
        {minute: 1, delay: 60000},       // 1 minute
        {minute: 5, delay: 300000},      // 5 minutes
        {minute: 10, delay: 600000}      // 10 minutes
    ];

    // Schedule messages
    timings.forEach(function(timing) {
        var timer = setTimeout(function() {
            var message = useRandom ?
                getMessageByMinute(timing.minute) :
                getMessageByMinuteSequential(timing.minute);

            if (message) {
                displayMessage(message, containerId);
            }
        }, timing.delay);

        dojoMessageTimers.push(timer);
    });
}

/**
 * Stop all scheduled messages
 */
function stopDojoMessages() {
    dojoMessageTimers.forEach(function(timer) {
        clearTimeout(timer);
    });
    dojoMessageTimers = [];
    dojoMessageStartTime = null;
}

/**
 * Restart message sequence (useful when conditions change)
 * @param {string} containerId - ID of container element
 * @param {boolean} useRandom - Use random messages
 */
function restartDojoMessages(containerId, useRandom) {
    stopDojoMessages();
    startDojoMessages(containerId, useRandom);
}

// Auto-start on page load (if container exists)
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dojo-message-container')) {
                startDojoMessages();
            }
        });
    } else {
        // DOM already loaded
        if (document.getElementById('dojo-message-container')) {
            startDojoMessages();
        }
    }
}
