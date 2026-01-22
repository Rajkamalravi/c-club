<?php
/**
 * Events V2 - RSVP Registration Form Page
 *
 * Step 2: Fill in registration details
 */

// Check login
if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_LOGIN_URL);
}

// Get event token from URL
$eventtoken = $goto ?? '';
$selected_ticket = $_GET['ticket'] ?? '';

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

// Get user info to pre-fill form
$user_info = taoh_user_all_info();

// Event details
$event_image = events_v2_get_image($event);
$event_date = events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0);
$event_time = events_v2_format_time($event['utc_start_at'], $event['locality'] ?? 0);

// Get ticket info
$ticket_types = $event['ticket_types'] ?? [
    ['id' => 'free', 'name' => 'Free Admission', 'price' => 0]
];
$selected_ticket_info = null;
foreach ($ticket_types as $ticket) {
    if ($ticket['id'] === $selected_ticket) {
        $selected_ticket_info = $ticket;
        break;
    }
}
if (!$selected_ticket_info) {
    $selected_ticket_info = $ticket_types[0] ?? ['id' => 'free', 'name' => 'Free Admission', 'price' => 0];
}

// Page meta
$page_title = 'Complete Registration - ' . ($event['title'] ?? 'Event');
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
                <div class="ev2-progress-step active">
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
                                <h1>Complete Your Registration</h1>
                                <p>Please fill in your details to register for this event</p>
                            </div>

                            <!-- Registration Form -->
                            <form id="ev2-rsvp-form" class="ev2-rsvp-form" method="POST">
                                <input type="hidden" name="eventtoken" value="<?php echo htmlspecialchars($eventtoken); ?>">
                                <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($selected_ticket_info['id']); ?>">

                                <!-- Personal Information -->
                                <div class="ev2-form-section">
                                    <h3 class="ev2-form-section-title">Personal Information</h3>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="first_name"
                                                name="first_name"
                                                value="<?php echo htmlspecialchars($user_info->first_name ?? ''); ?>"
                                                required
                                            >
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="last_name"
                                                name="last_name"
                                                value="<?php echo htmlspecialchars($user_info->last_name ?? ''); ?>"
                                                required
                                            >
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            value="<?php echo htmlspecialchars($user_info->email ?? ''); ?>"
                                            required
                                        >
                                        <div class="form-text">We'll send your confirmation and event updates to this email</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input
                                            type="tel"
                                            class="form-control"
                                            id="phone"
                                            name="phone"
                                            value="<?php echo htmlspecialchars($user_info->phone ?? ''); ?>"
                                        >
                                    </div>
                                </div>

                                <!-- Professional Information -->
                                <div class="ev2-form-section">
                                    <h3 class="ev2-form-section-title">Professional Information</h3>

                                    <div class="mb-3">
                                        <label for="job_title" class="form-label">Job Title</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="job_title"
                                            name="job_title"
                                            value="<?php echo htmlspecialchars($user_info->job_title ?? ''); ?>"
                                        >
                                    </div>

                                    <div class="mb-3">
                                        <label for="company" class="form-label">Company / Organization</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="company"
                                            name="company"
                                            value="<?php echo htmlspecialchars($user_info->company ?? ''); ?>"
                                        >
                                    </div>
                                </div>

                                <!-- Custom Questions (if any) -->
                                <?php if (!empty($event['registration_questions'])): ?>
                                    <div class="ev2-form-section">
                                        <h3 class="ev2-form-section-title">Additional Information</h3>

                                        <?php foreach ($event['registration_questions'] as $question): ?>
                                            <div class="mb-3">
                                                <label for="custom_<?php echo $question['id']; ?>" class="form-label">
                                                    <?php echo htmlspecialchars($question['label']); ?>
                                                    <?php if ($question['required']): ?><span class="text-danger">*</span><?php endif; ?>
                                                </label>
                                                <?php if ($question['type'] === 'textarea'): ?>
                                                    <textarea
                                                        class="form-control"
                                                        id="custom_<?php echo $question['id']; ?>"
                                                        name="custom_<?php echo $question['id']; ?>"
                                                        rows="3"
                                                        <?php echo $question['required'] ? 'required' : ''; ?>
                                                    ></textarea>
                                                <?php elseif ($question['type'] === 'select'): ?>
                                                    <select
                                                        class="form-select"
                                                        id="custom_<?php echo $question['id']; ?>"
                                                        name="custom_<?php echo $question['id']; ?>"
                                                        <?php echo $question['required'] ? 'required' : ''; ?>
                                                    >
                                                        <option value="">Select an option</option>
                                                        <?php foreach ($question['options'] as $option): ?>
                                                            <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input
                                                        type="<?php echo $question['type']; ?>"
                                                        class="form-control"
                                                        id="custom_<?php echo $question['id']; ?>"
                                                        name="custom_<?php echo $question['id']; ?>"
                                                        <?php echo $question['required'] ? 'required' : ''; ?>
                                                    >
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Terms & Conditions -->
                                <div class="ev2-terms-checkbox">
                                    <input
                                        type="checkbox"
                                        id="ev2-terms-agree"
                                        name="agree_terms"
                                        required
                                    >
                                    <label for="ev2-terms-agree">
                                        I agree to the <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/terms" target="_blank">Terms of Service</a>
                                        and <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/privacy" target="_blank">Privacy Policy</a>.
                                        I consent to receive event-related communications.
                                    </label>
                                </div>

                                <!-- Actions -->
                                <div class="ev2-rsvp-actions">
                                    <a href="<?php echo TAOH_EVENTS_V2_URL; ?>/rsvp/<?php echo $eventtoken; ?>" class="btn btn-outline-secondary" id="ev2-back-btn">
                                        <i class="bi bi-arrow-left me-2"></i>Back
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="ev2-submit-btn" disabled>
                                        Complete Registration<i class="bi bi-check-lg ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary Sidebar -->
                    <div class="col-lg-4">
                        <div class="ev2-rsvp-card ev2-order-summary">
                            <h3>Order Summary</h3>

                            <!-- Event Info -->
                            <div class="ev2-summary-event">
                                <div class="ev2-summary-image-small">
                                    <img src="<?php echo htmlspecialchars($event_image); ?>" alt="Event">
                                </div>
                                <div class="ev2-summary-event-info">
                                    <h4><?php echo htmlspecialchars($event['title'] ?? 'Event'); ?></h4>
                                    <p><?php echo $event_date; ?> at <?php echo $event_time; ?></p>
                                </div>
                            </div>

                            <hr>

                            <!-- Ticket Info -->
                            <div class="ev2-summary-ticket">
                                <div class="d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($selected_ticket_info['name']); ?></span>
                                    <span>
                                        <?php if ($selected_ticket_info['price'] == 0): ?>
                                            Free
                                        <?php else: ?>
                                            $<?php echo number_format($selected_ticket_info['price'], 2); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <!-- Total -->
                            <div class="ev2-summary-total">
                                <div class="d-flex justify-content-between">
                                    <strong>Total</strong>
                                    <strong>
                                        <?php if ($selected_ticket_info['price'] == 0): ?>
                                            Free
                                        <?php else: ?>
                                            $<?php echo number_format($selected_ticket_info['price'], 2); ?>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Help Card -->
                        <div class="ev2-rsvp-card ev2-help-card">
                            <h4><i class="bi bi-question-circle me-2"></i>Need Help?</h4>
                            <p>If you have any questions about registration, please contact the event organizer.</p>
                            <a href="<?php echo TAOH_SITE_URL_ROOT; ?>/contact" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-envelope me-1"></i>Contact Support
                            </a>
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
