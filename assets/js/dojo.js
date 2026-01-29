/**
 * Dojo.js - Smart messaging system for user engagement
 * Displays contextual messages at 0th, 1st, 5th, and 10th minute intervals
 */

/**
 * Get appropriate message array based on current state
 * @returns {Array} Array of contextual messages
 */
  var dojoRestartTimer = null;  // Add this new variable at the top
  let lastDojoRun = null;

function getMessageArray() {
    var message_array = [];

    /*console.log('---CURRENT_BLOCK_VISITING-------------'+CURRENT_BLOCK_VISITING)
    console.log('---PARTICIPANT_MEMBERS_COUNT-------------'+PARTICIPANT_MEMBERS_COUNT)
    console.log('---CHANNEL_MEMBERS_COUNT-------------'+CHANNEL_MEMBERS_COUNT)
    console.log('---PARTICIPANT_VISITING_COUNT-------------'+PARTICIPANT_VISITING_COUNT)
    console.log('---VIDEO_POSTED_RECENTLY-------------'+VIDEO_POSTED_RECENTLY)
    console.log('---NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN-------------'+NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN)*/

    if (!d_user_logged && CURRENT_INNER_PAGE =='events_details') {
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
    else if (d_user_profile_completed && d_user_profile_type == 'professional' && CURRENT_INNER_PAGE =='events_details') {
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
    else if (d_user_profile_completed && d_user_profile_type != 'professional' && CURRENT_INNER_PAGE =='events_details' && !d_is_sponsorship_available) {
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
	else if(0 && CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='CHANNEL' &&
        CHANNEL_MEMBERS_COUNT > 1 && NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN){
            //3 User in a any channel for 5min and did not post any message yet
        message_array = [
                "Hey there 👋<br>You've been here a bit — want to say hi or share what brought you in? A short hello can spark good chats",

                "Still getting the feel of the place? 🌿<br>Feel free to jump in anytime — maybe introduce yourself or share a thought to get things rolling",

                "Quiet thinker mode activated 🤔<br>When you're ready, drop a line — even a wave or a question can kick off great conversation",

                "Hanging out silently? Totally fine 👌<br>If something comes to mind, post it! Others often join in once someone breaks the ice",

                "You've been here for a bit ☕<br>Perfect time to share what you're working on or say hi — conversations start small",

                "Hey there 👋<br>Been here a bit — want to say hi or share what brought you in today? A quick hello goes a long way!",

                "Looks like you're getting the vibe 🌿<br>Feel free to drop a message — maybe introduce yourself or share what you're working on!",

                "Still here? Awesome 👋<br>You could post a hello or share a link — helps others know someone's around!",

                "You've been here a bit — want to start the convo? 💬<br>A short message or idea can get the chat rolling — others often jump in right after!"
        ] ;
    }
	else if(CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='CHANNEL'
         && CHANNEL_MEMBERS_COUNT > 1 && VIDEO_POSTED_RECENTLY){
        ////7. User is not 1st in a channel, and someone posted a video in the channel in less than 10min
        message_array = [
            "New group video just started 🎥<br>Someone started a group video a few minutes ago — want to check it out and join the convo?",

            "Looks like there's something👀<br>A fresh group video link been shared — might be worth a quick peek!",

            "Heads-up, new video started!⚡<br>There's a new group video — open it when you're ready and connect with others",

            "Group Video just started 📡<br>Someone just started a group video — feel free to join and learn",

            "Looks like the convo's going visual 👀<br>There's a new group video in the thread — jump in and see what everyone's reacting to",

            "Video alert 🎬<br>Someone started a group video not long ago — join and keep the energy going"
        ];
    }
    else if(0 && CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='CHANNEL' &&
        CHANNEL_MEMBERS_COUNT == 1 && PARTICIPANT_MEMBERS_COUNT == 1
    ){
        //5 User is 1st in any channel and 1st in room participant
        message_array = [
                "Welcome to the channel 👋 <br> It's a bit quiet right now, but you're in the right spot. Feel free to explore or start a conversation",
                "You've got the room to yourself 🧘 <br> A great moment to look around, share a thought, or drop the first post",
                "Early bird energy ⚡ <br> You're here ahead of the crowd — perfect time to get a feel for the space",
                "Quiet for now, but not for long 🌱<br> Take a moment to settle in — others will show up soon. You can spark the chat if you like",
                "Nice and peaceful in here ☕<br> You've got the place to yourself for a bit — want to share what brings you here?"
        ];
    }
    else if(0 && CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='CHANNEL' &&
        CHANNEL_MEMBERS_COUNT == 1 && PARTICIPANT_MEMBERS_COUNT > 1
    ){
        //6 User is 1st in any channel and not 1st in room participants
        message_array = [
            "Welcome to this space 👋<br>You're first to open this channel — others are around elsewhere. Feel free to start the chat or drop a question",
            "You've got a quiet corner 🪶<br>The main room's active — this one's open for new threads if you'd like to begin something",
            "Looks like this channel's all yours for now 🌿<br>Others are nearby — want to post the first message or topic here?",
            "You're early to this one ☀️<br>There's activity in the room — maybe start a note here so others can jump in when they notice",
            "Exploring new ground, I see 👀<br>No one's chatted here yet, but others are online. Perfect moment to set the tone or ask a question",
            "Welcome to this space 👋<br>You're first to open this channel — others are active elsewhere in the room. Want to drop the first note here?",
            "Psst… nice move 👀<br>You're first in this channel — others will see it soon. Say hello or share a quick thought to set the vibe",
            "You've got a head start 🚀<br>The room's filling up, but this channel's fresh — a perfect place to kick off a new convo",
            "Welcome, pathfinder 🌿<br>You're the first one here, but others are nearby — want to plant the first message so they can join in?"

        ];
    }
    else if(0 && CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='CHANNEL'
         && CHANNEL_MEMBERS_COUNT > 1){
        //8. I am in a channel, and 2nd or more participants joined,
        message_array = [
            "Looks like you've got company 👋<br>Others are here now — perfect moment to start a quick video or say hello",

            "The room's waking up ☀️<br>You're not solo anymore — maybe kick things off with a quick video chat by clicking the video icon?",

            "New energy in the channel ⚡<br>Others just came in — want to start a short video and break the ice?",

            "Feels livelier already 🎉<br>A few more people are here now — you could start a quick video by clicking the video icon to get the convo going",

            "Hey, the crew's growing 🙌<br>Looks like others joined — perfect time to start a quick video or share a hello",

            "Hey, the crew's growing 🎉<br>Why not start a quick video by clicking the video icon to get the conversation flowing?",

            "Not alone anymore 🙌<br>A few others just joined — hit that video button and make it a real chat!"

            ];
    }
    else if(0 && CURRENT_INNER_PAGE =='networking' && CURRENT_BLOCK_VISITING =='PARTICIPANT' &&
         PARTICIPANT_VISITING_COUNT == 1 && PARTICIPANT_MEMBERS_COUNT > 1)
    {
        //4 User clicked Participants tab for first time
        message_array = [
                "Plenty of folks here already 👋<br>Why not say hi to someone new? A short hello can spark a great chat",

                "You're in good company 🙌<br>There are " + PARTICIPANT_MEMBERS_COUNT + " people here — go ahead and drop a quick message or emoji wave!",

                "Room's alive ✨<br>Lots of energy here already. Pick someone who looks interesting and start a 1:1 hello",

                "See anyone you'd like to meet?<br>You can click on a name to chat directly — small intros lead to great connections",

                "Great mix of people in this room 🌎<br>Take a second to browse — maybe send a quick wave or invite someone to your Table"
            ];
    }
	else if(0 &&  CURRENT_INNER_PAGE =='networking' && PARTICIPANT_MEMBERS_COUNT == 1){
        ////2 When user is 1st in event, let them know
        message_array = [
            "Welcome to the room 👋<br>Glad you dropped by — it's quiet right now, but you can check out the product video while the room fills up",

            "You're early — great timing ⏰<br>Things haven't started buzzing yet. watch the product video to learn all the things that you can do today!",

            "Settling in early? Perfect 👍<br>Take a look around — you'll spot the product video clip while others join",

            "Good to see you here 🌟<br>The room's open and will get lively soon. While waiting, you can preview the product video on all that you can do today!",

            "Welcome back 👋 or maybe your first time — either way, great to see you.<br>Things are still warming up; check the event video or say hi once others arrive!",

            "Welcome to the room 👋<br>You're the first one here — perfect time to get comfortable. Watch the product video to get started!",

            "Hey trailblazer!<br>You've opened the room before anyone else — pioneers like you set the vibe. Check out the product video while others join",

            "Quiet before the buzz 🐝<br>You're the first in! Take a moment to explore the tabs or catch the product video — you'll be ready when others arrive",

            "Welcome, early explorer 🌍<br>You're in first — great chance to get a feel for the space. Watch the quick event overview while the room warms up"
        ];
    }
    return message_array;
}
//alert(d_is_session_allowed)
//http://localhost/hires-i/events/d/veterans-expo-2025-ukk139noc6n7ayw/stlo
/**
 * Pick a message based on minute interval (0th, 1st, 5th, or 10th)
 * @param {number} minute - Current minute (0, 1, 5, or 10)
 * @returns {string} Selected message or empty string if invalid minute
 */
function getMessageByMinute(minute) {
    var message_array = getMessageArray();

    console.log('----minute---',minute,'------------message_array------',message_array)

    if (message_array.length === 0) {
        return '';
    }

    var validMinutes = [0, 1, 2, 3];
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
    //var minuteMap = {0: 0, 0.5: 1, 1: 2, 2: 3};
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

    const now = Date.now();
    const FIVE_MIN = 5 * 60 * 1000; // ms

    // 1) Very first time -> run
    if (lastDojoRun === null) {
        taoh_set_success_message(message, 7000);
        lastDojoRun = now;
        // put any other "first-run" logic here
        //return true; // indicate message was shown
    }

    // 2) Subsequent calls -> only run if gap is >= 5 minutes
    if (now - lastDojoRun >= FIVE_MIN) {
        taoh_set_success_message(message, 7000);
        lastDojoRun = now;
        // put any other periodic logic here
        //return true; // indicate message was shown
    }

    // 3) If we're here, it's within the 5-minute cooldown -> do nothing
    //return false;


    //taoh_set_success_message(message, 7000);
    /*if (container && message) {
        container.innerHTML = message;
        container.style.display = 'block';

        // Add fade-in animation
        container.style.opacity = '0';
        setTimeout(function() {
            container.style.transition = 'opacity 0.5s';
            container.style.opacity = '1';
        }, 10);
    }*/
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
    //alert('-----------')
    containerId = containerId || 'dojo-message-container';
    useRandom = useRandom !== undefined ? useRandom : true;

    // Clear any existing timers FIRST
    stopDojoMessages();

    console.log('Starting Dojo messages - new timer sequence initiated');

    // Record start time
    dojoMessageStartTime = new Date();

    // Define timing intervals in milliseconds
    var timings = [
        {minute: 0, delay: 0},           // Immediately (0th minute)
        {minute: 1, delay: 60000},       // 1 minute
        {minute: 5, delay: 300000},      // 5 minutes
        {minute: 10, delay: 600000}      // 10 minutes
    ];

    /*var timings = [
        {minute: 0, delay: 0},           // Immediately (0th minute)
        {minute: 0.5, delay: 30000},       // 30 seconds
        {minute: 1, delay: 60000},      // 1 minute
        {minute: 2, delay: 120000}      // 2 minutes
    ];*/

    // Schedule messages
    timings.forEach(function(timing) {
        var timer = setTimeout(function() {
           // alert(timing.minute+'==========='+useRandom)
            var message = useRandom ?
                getMessageByMinute(timing.minute) :
                getMessageByMinuteSequential(timing.minute);

            if (message) {
                console.log('Displaying message at minute ' + timing.minute + ': ' + message.substring(0, 50) + '...');
                displayMessage(message, containerId);
            }
        }, timing.delay);

        dojoMessageTimers.push(timer);
    });

    console.log('Scheduled ' + dojoMessageTimers.length + ' message timers');
}

/**
 * Stop all scheduled messages
 */
function stopDojoMessages() {
    // Clear all timers individually
    for (var i = 0; i < dojoMessageTimers.length; i++) {
        clearTimeout(dojoMessageTimers[i]);
    }
    // Reset the array completely
    dojoMessageTimers.length = 0;
    dojoMessageStartTime = null;

    console.log('Dojo messages stopped, all timers cleared');
}

/**
 * Restart message sequence (useful when conditions change)
 * @param {string} containerId - ID of container element
 * @param {boolean} useRandom - Use random messages
 */


/*function restartDojoMessages(containerId, useRandom) {
    console.log('Restarting Dojo messages with new conditions');
    // Clear any pending restart first
      if (dojoRestartTimer) {
          clearTimeout(dojoRestartTimer);
          dojoRestartTimer = null;
      }

      stopDojoMessages();

      // Small delay to ensure all timers are cleared before starting new ones
      dojoRestartTimer = setTimeout(function() {
          dojoRestartTimer = null;
          startDojoMessages(containerId, useRandom);
      }, 100);

}*/

function restartDojoMessages(containerId, useRandom) {
      console.log('Restarting Dojo messages with new conditions');
      stopDojoMessages();
      startDojoMessages(containerId, useRandom);
}


// Auto-start on page load (if container exists)
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dojo-message-container')) {
                setTimeout(function() {
                    startDojoMessages();
                }, 2000); // Slight delay to ensure visibility

            }
        });
    } else {
        // DOM already loaded
        if (document.getElementById('dojo-message-container')) {
             setTimeout(function() {
                    startDojoMessages();
                }, 2000); // Slight delay to ensure visibility
        }
    }
}
/* else if (d_user_profile_completed && d_user_profile_type != 'professional' && CURRENT_INNER_PAGE =='events_details' && d_is_sponsorship_available) {
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
    else if (d_rsvp_done && !d_is_session_added && d_is_session_allowed) {
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
    else if (d_rsvp_done && !d_is_exhibitor_added && d_is_exhibitor_allowed) {
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
             "You're good to go — why not register for another event while you wait? " + EVENTSLINK,
            "You're all prepped — explore similar events to expand your network " + EVENTSLINK,
            "Great job completing setup — now check out what's next on the calendar " + EVENTSLINK,
           // "Event countdown started — engage with others in comments today " + COMMENTLINK,
           // "While you wait, drop a comment to meet fellow attendees " + COMMENTLINK,
           // "Everything's done — start conversations and build collaborations now " + COMMENTLINK,
           // "Network starts now — comment, connect, and collaborate before the event begins " + COMMENTLINK
        ];
    }*/
