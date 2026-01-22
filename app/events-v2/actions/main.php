<?php
/**
 * Events V2 Module - Action Handlers
 *
 * Handles form submissions for RSVP and other actions
 */

// Get action from URL
$action = taoh_parse_url(2) ?? '';

switch ($action) {
    /**
     * Handle RSVP form submission
     * POST /actions/events-v2/rsvp
     */
    case 'rsvp':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            taoh_set_error_message('Invalid request method');
            taoh_redirect(TAOH_EVENTS_V2_URL);
        }

        // Check login
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }

        // Get form data
        $eventtoken = $_POST['eventtoken'] ?? '';
        $ticket_type = $_POST['ticket_type'] ?? 'free';
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $job_title = trim($_POST['job_title'] ?? '');
        $company = trim($_POST['company'] ?? '');

        // Validate required fields
        if (empty($eventtoken)) {
            taoh_set_error_message('Invalid event');
            taoh_redirect(TAOH_EVENTS_V2_URL);
        }

        if (empty($first_name) || empty($last_name)) {
            taoh_set_error_message('Please enter your full name');
            taoh_redirect(TAOH_EVENTS_V2_URL . '/rsvp-form/' . $eventtoken . '?ticket=' . $ticket_type);
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            taoh_set_error_message('Please enter a valid email address');
            taoh_redirect(TAOH_EVENTS_V2_URL . '/rsvp-form/' . $eventtoken . '?ticket=' . $ticket_type);
        }

        // Prepare API call
        $taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_api_token(1, 1),
            'eventtoken' => $eventtoken,
            'ticket_type' => $ticket_type,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'job_title' => $job_title,
            'company' => $company,
        ];

        // Add custom fields
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'custom_') === 0) {
                $taoh_vals[$key] = trim($value);
            }
        }

        // Submit RSVP
        $response = taoh_apicall_post('events.rsvp.post', $taoh_vals);
        $result = json_decode($response, true);

        if ($result && $result['success']) {
            taoh_set_success_message('You have successfully registered for this event!');
            taoh_redirect(TAOH_EVENTS_V2_URL . '/confirmation/' . $eventtoken);
        } else {
            taoh_set_error_message($result['message'] ?? 'Failed to complete registration. Please try again.');
            taoh_redirect(TAOH_EVENTS_V2_URL . '/rsvp-form/' . $eventtoken . '?ticket=' . $ticket_type);
        }
        break;

    /**
     * Cancel RSVP
     * POST /actions/events-v2/cancel-rsvp
     */
    case 'cancel-rsvp':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            taoh_set_error_message('Invalid request method');
            taoh_redirect(TAOH_EVENTS_V2_URL);
        }

        // Check login
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }

        $eventtoken = $_POST['eventtoken'] ?? '';
        $rsvptoken = $_POST['rsvptoken'] ?? '';

        if (empty($eventtoken) || empty($rsvptoken)) {
            taoh_set_error_message('Invalid request');
            taoh_redirect(TAOH_EVENTS_V2_URL);
        }

        // Call cancel API
        $taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_api_token(1, 1),
            'eventtoken' => $eventtoken,
            'rsvptoken' => $rsvptoken,
            'action' => 'cancel',
        ];

        $response = taoh_apicall_post('events.rsvp.post', $taoh_vals);
        $result = json_decode($response, true);

        if ($result && $result['success']) {
            taoh_set_success_message('Your registration has been cancelled.');
        } else {
            taoh_set_error_message($result['message'] ?? 'Failed to cancel registration.');
        }

        taoh_redirect(TAOH_EVENTS_V2_URL . '/d/' . $eventtoken);
        break;

    default:
        taoh_redirect(TAOH_EVENTS_V2_URL);
        break;
}
