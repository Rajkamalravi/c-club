<?php
/**
 * Events V2 Module - Main Router
 *
 * Modern events module with Bootstrap 5 and responsive design
 * Runs parallel to existing /events/ module at /events-v2/
 */

// Check if events module is enabled (use same flag as existing)
if (!TAOH_EVENTS_ENABLE) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
}

// Module constants
defined('TAOH_APP_SLUG') || define('TAOH_APP_SLUG', 'events-v2');
define('TAOH_CURR_APP_SLUG', 'events-v2');
define('TAOH_CURR_APP_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG);
define('TAOH_EVENTS_V2_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG);

// Reuse existing events API endpoints
define('EVENTS_V2_EVENT_GET', TAOH_API_PREFIX . "/events.event.get");
define('EVENTS_V2_RSVP_GET', TAOH_API_PREFIX . "/events.rsvp.get");

// Reuse existing status constants from events module
defined('TAOH_EVENTS_EVENT_UNPUBLISHED') || define('TAOH_EVENTS_EVENT_UNPUBLISHED', 1);
defined('TAOH_EVENTS_EVENT_SUSPENDED') || define('TAOH_EVENTS_EVENT_SUSPENDED', 2);
defined('TAOH_EVENTS_EVENT_EXPIRED') || define('TAOH_EVENTS_EVENT_EXPIRED', 3);
defined('TAOH_EVENTS_EVENT_PUBLISHED') || define('TAOH_EVENTS_EVENT_PUBLISHED', 4);
defined('TAOH_EVENTS_EVENT_ACTIVE') || define('TAOH_EVENTS_EVENT_ACTIVE', 5);
defined('TAOH_EVENTS_EVENT_LIVEABLE') || define('TAOH_EVENTS_EVENT_LIVEABLE', 6);
defined('TAOH_EVENTS_EVENT_EARLY_START') || define('TAOH_EVENTS_EVENT_EARLY_START', 7);
defined('TAOH_EVENTS_EVENT_START') || define('TAOH_EVENTS_EVENT_START', 8);
defined('TAOH_EVENTS_EVENT_STOP') || define('TAOH_EVENTS_EVENT_STOP', 9);

defined('TAOH_EVENTS_RSVP_SUSPENDED') || define('TAOH_EVENTS_RSVP_SUSPENDED', 1);
defined('TAOH_EVENTS_RSVP_NEW') || define('TAOH_EVENTS_RSVP_NEW', 2);
defined('TAOH_EVENTS_RSVP_NOTMATCHED') || define('TAOH_EVENTS_RSVP_NOTMATCHED', 3);
defined('TAOH_EVENTS_RSVP_NOMATCH') || define('TAOH_EVENTS_RSVP_NOMATCH', 4);
defined('TAOH_EVENTS_RSVP_MATCH') || define('TAOH_EVENTS_RSVP_MATCH', 5);
defined('TAOH_EVENTS_RSVP_NOMATCH_LIVE') || define('TAOH_EVENTS_RSVP_NOMATCH_LIVE', 6);
defined('TAOH_EVENTS_RSVP_MATCH_LIVE') || define('TAOH_EVENTS_RSVP_MATCH_LIVE', 7);

// Parse URL segments
$current_app = EVENT_DEMO_SITE ? 'events-v2' : taoh_parse_url(0);
$action = taoh_parse_url(1);
$goto = taoh_parse_url(2);

if (taoh_parse_url(2) && taoh_parse_url(3)) {
    $goto = taoh_parse_url(2) . '/' . taoh_parse_url(3);
}

$id = '';
if (taoh_parse_url(4)) {
    $id = '/' . taoh_parse_url(4);
}

$param = '';
if (taoh_parse_url(5)) {
    $param .= '/' . taoh_parse_url(5);
}
if (taoh_parse_url(6)) {
    $param .= '/' . taoh_parse_url(6);
}

// Include module functions
include "functions.php";
require_once(TAOH_PLUGIN_PATH . '/core/form_fields.php');

// Route handling
switch ($action) {
    // Event detail page
    case 'd':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/pages/detail.php");
        break;

    // RSVP ticket selection
    case 'rsvp':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/pages/rsvp.php");
        break;

    // RSVP form
    case 'rsvp-form':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/pages/rsvp-form.php");
        break;

    // RSVP confirmation
    case 'confirmation':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/pages/confirmation.php");
        break;

    // AJAX endpoints
    case 'ajax':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/ajax.php");
        break;

    // Default: Event listing page
    default:
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/pages/listing.php");
        break;
}

die();
