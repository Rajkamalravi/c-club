<?php
/**
 * Events V2 Module - Helper Functions
 *
 * Extends existing events functions with v2-specific helpers
 */

// Include original events functions to reuse
require_once(TAOH_APP_PATH . '/events/functions.php');

/**
 * Get events list with filters (v2 version)
 *
 * @param array $filters Filter options
 * @param int $limit Number of events to fetch
 * @param int $offset Pagination offset
 * @return array
 */
function events_v2_get_list($filters = [], $limit = 12, $offset = 0) {
    $taoh_call = "events.get";

    // Handle event_type - can be array or string
    $event_type = $filters['event_type'] ?? '';
    if (is_array($event_type)) {
        // Convert array to comma-separated string for API
        $event_type = implode(',', array_filter($event_type));
    }

    $taoh_vals = [
        'mod' => 'events',
        'geohash' => $filters['geohash'] ?? '',
        'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
        'token' => taoh_get_dummy_token(1),
        'local' => TAOH_EVENTS_GET_LOCAL ?? 0,
        'ops' => 'list',
        'search' => $filters['search'] ?? '',
        'limit' => $limit,
        'offset' => $offset,
        'postDate' => $filters['postDate'] ?? '',
        'from_date' => $filters['from_date'] ?? '',
        'to_date' => $filters['to_date'] ?? '',
        'filter_type' => $filters['filter_type'] ?? '',
        'event_type' => $event_type,
        'cache_time' => 120,
        'demo' => EVENT_DEMO_SITE ?? 0,
    ];

    $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
    return json_decode($data, true);
}

/**
 * Get single event details (v2 version)
 *
 * @param string $eventtoken Event token
 * @return array|null
 */
function events_v2_get_detail($eventtoken) {
    if (empty($eventtoken) || !ctype_alnum($eventtoken)) {
        return null;
    }

    $taoh_vals = [
        'token' => taoh_get_api_token(1, 1),
        'ops' => 'baseinfo',
        'mod' => 'events',
        'eventtoken' => $eventtoken,
        'cache_name' => 'event_detail_' . $eventtoken,
    ];

    $result = taoh_apicall_get('events.event.get', $taoh_vals);
    $response = taoh_get_array($result, true);

    if (!$response || !$response['success']) {
        return null;
    }

    return $response['output'];
}

/**
 * Get event meta info (speakers, exhibitors, sponsors)
 *
 * @param string $eventtoken Event token
 * @return array
 */
function events_v2_get_meta($eventtoken) {
    $cache_name = 'event_MetaInfo_' . $eventtoken . '__';
    $taoh_vals = [
        'mod' => 'events',
        'token' => taoh_get_api_token(1, 1),
        'eventtoken' => $eventtoken,
        'cfcc5h' => 1,
        'cache_name' => $cache_name,
    ];

    $response = taoh_apicall_get('events.content.get', $taoh_vals);
    $data = json_decode($response, true);

    if ($data['success'] && !empty($data['output'])) {
        return $data['output'];
    }

    return [
        'event_speaker' => [],
        'event_exhibitor' => [],
        'event_sponsor' => [],
    ];
}

/**
 * Format event date for display (v2 version - more user friendly)
 *
 * @param string $input_date Date in YmdHis format
 * @param int $locality Whether event is global (1) or local (0)
 * @param string $format Output format
 * @return string
 */
function events_v2_format_date($input_date, $locality = 0, $format = 'D, M j, Y') {
    $user_timezone = new DateTimeZone(taoh_user_timezone());

    if ($locality) {
        $datetime = DateTime::createFromFormat('YmdHis', $input_date, $user_timezone);
    } else {
        $datetime = DateTime::createFromFormat('YmdHis', $input_date, new DateTimeZone('UTC'));
        $datetime->setTimezone($user_timezone);
    }

    return $datetime ? $datetime->format($format) : '';
}

/**
 * Format event time for display
 *
 * @param string $input_date Date in YmdHis format
 * @param int $locality Whether event is global (1) or local (0)
 * @return string
 */
function events_v2_format_time($input_date, $locality = 0) {
    return events_v2_format_date($input_date, $locality, 'g:i A');
}

/**
 * Get user-friendly event status badge
 *
 * @param array $event_arr Event data array
 * @return array ['status' => string, 'class' => string, 'label' => string]
 */
function events_v2_get_status_badge($event_arr) {
    $state = event_live_state(
        $event_arr['utc_start_at'],
        $event_arr['utc_end_at'],
        $event_arr['status'],
        $event_arr['locality']
    );

    $badges = [
        'live' => [
            'status' => 'live',
            'class' => 'bg-success',
            'label' => 'Live Now'
        ],
        'before' => [
            'status' => 'upcoming',
            'class' => 'bg-primary',
            'label' => 'Upcoming'
        ],
        'prelive' => [
            'status' => 'starting_soon',
            'class' => 'bg-warning text-dark',
            'label' => 'Starting Soon'
        ],
        'postlive' => [
            'status' => 'ended',
            'class' => 'bg-secondary',
            'label' => 'Ended'
        ],
        'after' => [
            'status' => 'ended',
            'class' => 'bg-secondary',
            'label' => 'Ended'
        ],
    ];

    return $badges[$state] ?? $badges['before'];
}

/**
 * Get event type badge
 *
 * @param string $event_type virtual, in-person, or hybrid
 * @return array ['class' => string, 'icon' => string, 'label' => string]
 */
function events_v2_get_type_badge($event_type) {
    $types = [
        'virtual' => [
            'class' => 'bg-info',
            'icon' => 'bi-camera-video',
            'label' => 'Virtual'
        ],
        'in-person' => [
            'class' => 'bg-dark',
            'icon' => 'bi-geo-alt',
            'label' => 'In-Person'
        ],
        'hybrid' => [
            'class' => 'bg-purple',
            'icon' => 'bi-broadcast',
            'label' => 'Hybrid'
        ],
    ];

    $type = strtolower($event_type ?? 'virtual');
    return $types[$type] ?? $types['virtual'];
}

/**
 * Truncate text with ellipsis
 *
 * @param string $text Text to truncate
 * @param int $length Max length
 * @return string
 */
function events_v2_truncate($text, $length = 150) {
    $text = strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $text = preg_replace('/\s+/', ' ', trim($text));

    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . '...';
}

/**
 * Get event card image URL with fallback
 *
 * @param array $event_data Event data
 * @return string
 */
function events_v2_get_image($event_data) {
    $image = $event_data['conttoken']['event_image'] ?? '';

    if (empty($image)) {
        return TAOH_SITE_URL_ROOT . '/assets/images/event.jpg';
    }

    return $image;
}

/**
 * Check if user has RSVP'd to event
 *
 * @param array $event_arr Event data
 * @return bool
 */
function events_v2_has_rsvp($event_arr) {
    return isset($event_arr['mystatus']['rsvptoken']) && !empty($event_arr['mystatus']['rsvptoken']);
}

/**
 * Generate event URL
 *
 * @param string $eventtoken Event token
 * @param string $slug Event slug
 * @return string
 */
function events_v2_event_url($eventtoken, $slug = '') {
    $url_slug = $slug ? $slug . '-' . $eventtoken : $eventtoken;
    return TAOH_EVENTS_V2_URL . '/d/' . $url_slug;
}

/**
 * Generate RSVP URL
 *
 * @param string $eventtoken Event token
 * @return string
 */
function events_v2_rsvp_url($eventtoken) {
    return TAOH_EVENTS_V2_URL . '/rsvp/' . $eventtoken;
}
