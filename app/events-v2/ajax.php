<?php
/**
 * Events V2 Module - AJAX Endpoints
 *
 * Handles AJAX requests for events data
 */

header('Content-Type: application/json');

// Get action from GET parameter
$ajax_action = $_GET['action'] ?? '';

// Response helper
function ev2_ajax_response($success, $data = null, $error = '') {
    $response = [
        'success' => $success,
        'output' => $data
    ];

    if (!$success && !empty($error)) {
        $response['error'] = $error;
    }

    echo json_encode($response);
    exit;
}

// Handle different AJAX actions
switch ($ajax_action) {
    /**
     * Get events list with filters
     * GET /events-v2/ajax?action=events
     */
    case 'events':
        // Handle multiple event types (type[] array or single type)
        $event_types = $_GET['type'] ?? [];
        if (is_string($event_types)) {
            $event_types = !empty($event_types) ? [$event_types] : [];
        }

        $filters = [
            'search' => $_GET['search'] ?? '',
            'event_type' => $event_types, // Now supports array
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'geohash' => $_GET['geohash'] ?? '',
        ];
        $limit = min(50, max(1, intval($_GET['limit'] ?? 12)));
        $offset = max(0, intval($_GET['offset'] ?? 0));

        $response = events_v2_get_list($filters, $limit, $offset);

        if ($response && isset($response['output'])) {
            ev2_ajax_response(true, $response['output']);
        } else {
            ev2_ajax_response(false, null, 'failed_to_fetch_events');
        }
        break;

    /**
     * Get single event details
     * GET /events-v2/ajax?action=event&eventtoken=XXX
     */
    case 'event':
        $eventtoken = $_GET['eventtoken'] ?? '';

        if (empty($eventtoken)) {
            ev2_ajax_response(false, null, 'event_token_required');
        }

        $event = events_v2_get_detail($eventtoken);

        if ($event) {
            ev2_ajax_response(true, $event);
        } else {
            ev2_ajax_response(false, null, 'event_not_found');
        }
        break;

    /**
     * Get event meta (speakers, exhibitors, sponsors)
     * GET /events-v2/ajax?action=meta&eventtoken=XXX
     */
    case 'meta':
        $eventtoken = $_GET['eventtoken'] ?? '';

        if (empty($eventtoken)) {
            ev2_ajax_response(false, null, 'event_token_required');
        }

        $meta = events_v2_get_meta($eventtoken);
        ev2_ajax_response(true, $meta);
        break;

    /**
     * Submit RSVP
     * POST /events-v2/ajax?action=rsvp
     */
    case 'rsvp':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ev2_ajax_response(false, null, 'post_method_required');
        }

        // Check login
        if (!taoh_user_is_logged_in()) {
            ev2_ajax_response(false, null, 'login_required');
        }

        // Get JSON body or form data
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $eventtoken = $input['eventtoken'] ?? '';
        if (empty($eventtoken)) {
            ev2_ajax_response(false, null, 'event_token_required');
        }

        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'email'];
        foreach ($required_fields as $field) {
            if (empty($input[$field])) {
                ev2_ajax_response(false, null, $field . '_required');
            }
        }

        // Validate email
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            ev2_ajax_response(false, null, 'invalid_email');
        }

        // Call RSVP API
        $taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_api_token(1, 1),
            'eventtoken' => $eventtoken,
            'ticket_type' => $input['ticket_type'] ?? 'free',
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? '',
            'job_title' => $input['job_title'] ?? '',
            'company' => $input['company'] ?? '',
        ];

        // Add custom fields
        foreach ($input as $key => $value) {
            if (strpos($key, 'custom_') === 0) {
                $taoh_vals[$key] = $value;
            }
        }

        $response = taoh_apicall_post('events.rsvp.post', $taoh_vals);
        $result = json_decode($response, true);

        if ($result && $result['success']) {
            ev2_ajax_response(true, $result['output'] ?? null);
        } else {
            ev2_ajax_response(false, null, 'rsvp_submission_failed');
        }
        break;

    /**
     * Check RSVP status
     * GET /events-v2/ajax?action=rsvp_status&eventtoken=XXX
     */
    case 'rsvp_status':
        $eventtoken = $_GET['eventtoken'] ?? '';

        if (empty($eventtoken)) {
            ev2_ajax_response(false, null, 'event_token_required');
        }

        if (!taoh_user_is_logged_in()) {
            ev2_ajax_response(true, ['has_rsvp' => false]);
        }

        $event = events_v2_get_detail($eventtoken);
        $has_rsvp = events_v2_has_rsvp($event ?? []);

        ev2_ajax_response(true, ['has_rsvp' => $has_rsvp]);
        break;

    /**
     * Search events
     * GET /events-v2/ajax?action=search&q=keyword
     */
    case 'search':
        $query = $_GET['q'] ?? '';
        $limit = min(20, max(1, intval($_GET['limit'] ?? 10)));

        if (strlen($query) < 2) {
            ev2_ajax_response(false, null, 'search_query_too_short');
        }

        $response = events_v2_get_list(['search' => $query], $limit, 0);

        if ($response && isset($response['output'])) {
            // Return simplified results for search dropdown
            $results = [];
            foreach (($response['output']['events'] ?? []) as $event) {
                $results[] = [
                    'eventtoken' => $event['eventtoken'],
                    'title' => $event['title'],
                    'date' => events_v2_format_date($event['utc_start_at'], $event['locality'] ?? 0),
                    'type' => $event['event_type'] ?? 'virtual',
                    'url' => events_v2_event_url($event['eventtoken'], $event['url_slug'] ?? ''),
                    'image' => events_v2_get_image($event),
                ];
            }
            ev2_ajax_response(true, $results);
        } else {
            ev2_ajax_response(false, null, 'search_failed');
        }
        break;

    default:
        ev2_ajax_response(false, null, 'invalid_action');
        break;
}
