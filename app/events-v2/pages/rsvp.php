<?php
/**
 * Events V2 - RSVP Ticket Selection Page
 *
 * Step 1: Select ticket type
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

// Check if already RSVP'd
if (events_v2_has_rsvp($event)) {
    taoh_redirect(TAOH_EVENTS_V2_URL . '/confirmation/' . $eventtoken);
}

// Event details
$status_badge = events_v2_get_status_badge($event);
$event_image = events_v2_get_image($event);
$event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
$event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);
$event_url = events_v2_event_url($eventtoken, $event['url_slug'] ?? '');

// Get ticket types (from event data or default)
$ticket_types = $event['ticket_types'] ?? [
    [
        'id' => 'free',
        'name' => 'Free Admission',
        'description' => 'General admission with access to all sessions',
        'price' => 0,
        'available' => true
    ]
];

// Page meta
$page_title = 'Register - ' . ($event['title'] ?? 'Event');
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
                <div class="ev2-progress-step active">
                    <div class="ev2-progress-step-number">1</div>
                    <span class="ev2-progress-step-label">Select Ticket</span>
                </div>
                <div class="ev2-progress-step">
                    <div class="ev2-progress-step-number">2</div>
                    <span class="ev2-progress-step-label">Your Details</span>
                </div>
                <div class="ev2-progress-step">
                    <div class="ev2-progress-step-number">3</div>
                    <span class="ev2-progress-step-label">Confirmation</span>
                </div>
            </div>

            <div class="ev2-rsvp-container">
                <div class="row">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <div class="ev2-rsvp-card">
                            <div class="ev2-rsvp-card-header">
                                <h1>Select Your Ticket</h1>
                                <p>Choose the ticket type that best suits your needs</p>
                            </div>

                            <!-- Ticket Selection -->
                            <div class="ev2-ticket-selection">
                                <?php foreach ($ticket_types as $index => $ticket): ?>
                                    <div class="ev2-ticket-option <?php echo !$ticket['available'] ? 'ev2-ticket-sold-out' : ''; ?>">
                                        <input
                                            type="radio"
                                            name="ticket_type"
                                            id="ticket-<?php echo $index; ?>"
                                            value="<?php echo htmlspecialchars($ticket['id']); ?>"
                                            <?php echo !$ticket['available'] ? 'disabled' : ''; ?>
                                            <?php echo $index === 0 && $ticket['available'] ? 'checked' : ''; ?>
                                        >
                                        <label for="ticket-<?php echo $index; ?>" class="ev2-ticket-card">
                                            <div class="ev2-ticket-card-content">
                                                <div class="ev2-ticket-info">
                                                    <h3><?php echo htmlspecialchars($ticket['name']); ?></h3>
                                                    <p><?php echo htmlspecialchars($ticket['description']); ?></p>
                                                    <?php if (!empty($ticket['features'])): ?>
                                                        <ul class="ev2-ticket-features">
                                                            <?php foreach ($ticket['features'] as $feature): ?>
                                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i><?php echo htmlspecialchars($feature); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ev2-ticket-price">
                                                    <?php if ($ticket['price'] == 0): ?>
                                                        <span class="ev2-price-free">Free</span>
                                                    <?php else: ?>
                                                        <span class="ev2-price-amount">$<?php echo number_format($ticket['price'], 2); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php if (!$ticket['available']): ?>
                                                <span class="ev2-ticket-sold-out-badge">Sold Out</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Actions -->
                            <div class="ev2-rsvp-actions">
                                <a href="<?php echo $event_url; ?>" class="btn btn-outline-secondary" id="ev2-back-btn">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Event
                                </a>
                                <button type="button" class="btn btn-primary" id="ev2-continue-btn">
                                    Continue<i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Event Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="ev2-rsvp-card ev2-event-summary">
                            <div class="ev2-summary-image">
                                <img src="<?php echo htmlspecialchars($event_image); ?>" alt="<?php echo htmlspecialchars($event['title'] ?? 'Event'); ?>">
                            </div>
                            <div class="ev2-summary-content">
                                <h3><?php echo htmlspecialchars($event['title'] ?? 'Event'); ?></h3>
                                <ul class="ev2-summary-details">
                                    <li>
                                        <i class="bi bi-calendar3"></i>
                                        <span><?php echo $event_date; ?></span>
                                    </li>
                                    <li>
                                        <i class="bi bi-clock"></i>
                                        <span><?php echo $event_time; ?></span>
                                    </li>
                                    <?php if (!empty($event['location'])): ?>
                                        <li>
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
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
        window.EV2_EVENT_TOKEN = '<?php echo htmlspecialchars($eventtoken); ?>';
    </script>
</body>
</html>
