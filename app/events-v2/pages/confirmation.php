<?php
/**
 * Events V2 - RSVP Confirmation Page
 *
 * Step 3: Success page with add-to-calendar options
 */

// Check login
if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_LOGIN_URL);
}

// Get event token from URL
$eventtoken = $goto ?? '';

if (empty($eventtoken)) {
    taoh_redirect(TAOH_EVENTS_V2_URL);
}

// Fetch event details
$event = events_v2_get_detail($eventtoken);

if (!$event) {
    include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/error.php');
    die();
}

// Event details
$event_image = events_v2_get_image($event);
$event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
$event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);
$event_end_time = events_v2_format_time($event['utc_end_at'], $event['locality'] ?? 0);
$event_url = events_v2_event_url($eventtoken, $event['url_slug'] ?? '');
$type_badge = events_v2_get_type_badge($event['event_type'] ?? 'virtual');

// Get user info
$user_info = taoh_user_all_info();

// Page meta
$page_title = 'Registration Confirmed - ' . ($event['title'] ?? 'Event');
$page_css = 'rsvp';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/head.php'); ?>
    <title><?php echo htmlspecialchars($page_title); ?> | <?php echo TAOH_SITE_TITLE; ?></title>
</head>
<body class="ev2-body ev2-rsvp-page">
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/header.php'); ?>

    <main class="ev2-rsvp-main">
        <div class="container">
            <!-- Progress Steps -->
            <div class="ev2-progress-steps">
                <div class="ev2-progress-step completed">
                    <div class="ev2-progress-step-number"><i class="bi bi-check"></i></div>
                    <span class="ev2-progress-step-label">Select Ticket</span>
                </div>
                <div class="ev2-progress-step completed">
                    <div class="ev2-progress-step-number"><i class="bi bi-check"></i></div>
                    <span class="ev2-progress-step-label">Your Details</span>
                </div>
                <div class="ev2-progress-step active">
                    <div class="ev2-progress-step-number"><i class="bi bi-check"></i></div>
                    <span class="ev2-progress-step-label">Confirmation</span>
                </div>
            </div>

            <div class="ev2-rsvp-container">
                <div class="ev2-rsvp-card ev2-confirmation">
                    <!-- Success Icon -->
                    <div class="ev2-confirmation-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>

                    <h1>You're Registered!</h1>
                    <p>A confirmation email has been sent to <strong><?php echo htmlspecialchars($user_info->email ?? ''); ?></strong></p>

                    <!-- Ticket Summary -->
                    <div class="ev2-ticket-summary">
                        <div class="ev2-ticket-summary-header">
                            <img src="<?php echo htmlspecialchars($event_image); ?>" alt="Event" class="ev2-ticket-summary-image">
                            <div class="ev2-ticket-summary-title">
                                <h3><?php echo htmlspecialchars($event['title'] ?? 'Event'); ?></h3>
                                <p>
                                    <span class="badge <?php echo $type_badge['class']; ?>">
                                        <i class="<?php echo $type_badge['icon']; ?> me-1"></i><?php echo $type_badge['label']; ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="ev2-ticket-summary-details">
                            <div class="ev2-ticket-summary-row">
                                <label><i class="bi bi-calendar3 me-2"></i>Date</label>
                                <span><?php echo $event_date; ?></span>
                            </div>
                            <div class="ev2-ticket-summary-row">
                                <label><i class="bi bi-clock me-2"></i>Time</label>
                                <span><?php echo $event_time; ?> - <?php echo $event_end_time; ?></span>
                            </div>
                            <div class="ev2-ticket-summary-row">
                                <label><i class="bi bi-globe me-2"></i>Timezone</label>
                                <span><?php echo taoh_user_timezone(); ?></span>
                            </div>
                            <?php if (!empty($event['location'])): ?>
                                <div class="ev2-ticket-summary-row">
                                    <label><i class="bi bi-geo-alt me-2"></i>Location</label>
                                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Add to Calendar -->
                    <h3 class="mb-3">Add to Your Calendar</h3>
                    <div class="ev2-add-calendar">
                        <a href="#" class="ev2-add-calendar-btn" data-calendar="google">
                            <i class="bi bi-google"></i>
                            <span>Google Calendar</span>
                        </a>
                        <a href="#" class="ev2-add-calendar-btn" data-calendar="outlook">
                            <i class="bi bi-microsoft"></i>
                            <span>Outlook</span>
                        </a>
                        <a href="#" class="ev2-add-calendar-btn" data-calendar="ical">
                            <i class="bi bi-calendar-plus"></i>
                            <span>iCal / Apple</span>
                        </a>
                    </div>

                    <!-- Actions -->
                    <div class="ev2-confirmation-actions">
                        <a href="<?php echo $event_url; ?>" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Event
                        </a>
                        <a href="<?php echo TAOH_EVENTS_V2_URL; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-grid me-2"></i>Browse More Events
                        </a>
                    </div>

                    <!-- Share Section -->
                    <div class="ev2-confirmation-share">
                        <h4>Share with Friends</h4>
                        <p>Invite others to join you at this event</p>
                        <div class="ev2-share-buttons">
                            <a href="#" class="ev2-share-btn ev2-share-twitter" data-share="twitter" title="Share on Twitter">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="#" class="ev2-share-btn ev2-share-linkedin" data-share="linkedin" title="Share on LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="#" class="ev2-share-btn ev2-share-facebook" data-share="facebook" title="Share on Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="ev2-share-btn ev2-share-email" data-share="email" title="Share via Email">
                                <i class="bi bi-envelope"></i>
                            </a>
                            <button class="ev2-share-btn ev2-share-copy" data-share="copy" title="Copy Link">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/footer.php'); ?>
    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-v2/rsvp.js"></script>

    <script>
        window.EVENTS_V2_URL = '<?php echo TAOH_EVENTS_V2_URL; ?>';
        window.EV2_EVENT_DATA = {
            eventtoken: '<?php echo htmlspecialchars($eventtoken); ?>',
            title: '<?php echo addslashes($event['title'] ?? 'Event'); ?>',
            description: '<?php echo addslashes(events_v2_truncate($event['description'] ?? '', 200)); ?>',
            start_date: '<?php echo $event['utc_start_at'] ?? ''; ?>',
            end_date: '<?php echo $event['utc_end_at'] ?? ''; ?>',
            location: '<?php echo addslashes($event['location'] ?? ''); ?>',
            url: '<?php echo $event_url; ?>'
        };

        // Initialize confirmation page
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EV2Rsvp !== 'undefined') {
                EV2Rsvp.initConfirmation();
            }
        });
    </script>
</body>
</html>
