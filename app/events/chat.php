<?php
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

/**
 * Remove a path segment from URL by index
 */
function removePathSegment($url, $index) {
    $parts = parse_url($url);
    $pathSegments = explode('/', trim($parts['path'], '/'));

    if (isset($pathSegments[$index])) {
        unset($pathSegments[$index]);
    }

    $newPath = '/' . implode('/', $pathSegments);
    $newUrl = $parts['scheme'] . '://' . $parts['host'];

    if (isset($parts['port'])) {
        $newUrl .= ':' . $parts['port'];
    }
    $newUrl .= $newPath;

    if (!empty($parts['query'])) {
        $newUrl .= '?' . $parts['query'];
    }

    return $newUrl;
}

/**
 * Add a path segment to URL before specified parameter
 */
function addPathSegment($url, $oldparam, $newParam) {
    $parsed = parse_url($url);
    $path = rtrim($parsed['path'], '/');
    $segments = explode('/', $path);
    $found = false;

    foreach ($segments as $i => $seg) {
        if ($seg === $oldparam) {
            array_splice($segments, $i, 0, $newParam);
            $found = true;
            break;
        }
    }

    if (!$found) {
        $segments[] = $newParam;
        $segments[] = 'stlo';
    }

    $newPath = implode('/', $segments);
    $newUrl = $parsed['scheme'] . "://" . $parsed['host'] . $newPath;

    if (isset($parsed['query'])) {
        $newUrl .= "?" . $parsed['query'];
    }

    return $newUrl;
}

$user_info_obj = taoh_user_all_info();
$valid_user = (bool) $user_info_obj?->profile_complete ?? false;
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$profile_type = ($taoh_user_is_logged_in && in_array($user_info_obj?->type ?? '', ['professional','employer','provider'], true))
    ? $user_info_obj->type : '';

$my_local_timezone = taoh_user_timezone();

$current_app = taoh_parse_url(3);
$eventtoken = taoh_parse_url(4);

$footer_tracking_link = 'events_lobby_'.$eventtoken;

if(taoh_parse_url(5)){
    $table_field = taoh_parse_url(5);
    $to_page = taoh_parse_url(6, 0);
    if(taoh_parse_url(5) == 'stlo'){
        $table_field = taoh_parse_url(6);
        $to_page = taoh_parse_url(7, 0);
    }

    if($table_field != '' && $table_field!= 'stlo' && $table_field == 'tables'){
        $uurl = TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG .'/tables?eventtoken='.$eventtoken.'&to_page='.$to_page;
        taoh_redirect($uurl);
        taoh_exit();
    }
}

$sharerlink  = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';

const TAO_PAGE_TYPE = 'events';
$app_data = taoh_app_info(TAO_PAGE_TYPE);

$user_timezone = taoh_user_timezone();
if (empty($user_timezone)) $user_timezone = 'America/New_York';

$taoh_vals = array(
    'token' => taoh_get_api_token(1, 1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken,
);

$result = taoh_apicall_get('events.event.get', $taoh_vals);
$response = taoh_get_array($result, true);

if (!$response || !$response['success']) {
   taoh_redirect(TAOH_EVENTS_URL);
   exit();
}

$event_arr = $response['output'];
$events_data = $event_arr['conttoken'] ?? [];
$ticket_types = $events_data['ticket_types'] ?? [];
$event_links = $events_data['link'] ?? [];
$event_faqs = $events_data['event_faq'] ?? [];
$source = $events_data['source'] ?? '';
$event_title = displayTaohFormatted($events_data['title']);
$is_exhibitor_enable = $events_data['enable_exhibitor_hall'] ?? 0 ;
$is_speaker_enable = $events_data['enable_speaker_hall'] ?? 0 ;
$is_hall_enable = $events_data['enable_hall'] ?? 0 ;
$is_sponsor_enable = isset($events_data['event_sponsor_levels']) ?  1 : 0;
$sponsor_levels = $events_data['event_sponsor_levels'] ?? [];
$country_locked = (int)($events_data['country_locked'] ?? 0);
$site_info = $event_arr['user_site_info'] ?? [];
// More robust HTML tag removal
$event_description_clean = strip_tags(html_entity_decode($event_arr['conttoken']['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
// Remove any remaining HTML entities and normalize whitespace
$event_description_clean = preg_replace('/\s+/', ' ', trim($event_description_clean));
$event_short = strlen($event_description_clean) > 157 ? substr($event_description_clean, 0, 157) . '...' : $event_description_clean;
if($event_arr['conttoken'][ 'event_image' ] != ''){
    $event_image = $event_arr['conttoken'][ 'event_image' ];
}else{
    $event_image = TAOH_SITE_URL_ROOT.'/assets/images/event.jpg';
}

/* Collecting organizer ptokens */
$raw_organizer_ptokens = $events_data['event_organizer_ptokens'] ?? '';
$event_organizer_ptokens = array_filter(
    array_map('trim', explode(',', $raw_organizer_ptokens)),
    fn($token) => $token !== ''
);

$event_owner = $event_arr['ptoken'];
if (!empty($event_owner)) {
    $event_organizer_ptokens[] = $event_owner;
}

$event_instance_owner = $events_data['ptoken'] ?? null;
if (!empty($event_instance_owner)) {
    $event_organizer_ptokens[] = $event_instance_owner;
}

if (defined('TAOH_SUPER_ORGANIZER_TOKEN') && !empty(TAOH_SUPER_ORGANIZER_TOKEN)) {
    $event_organizer_ptokens[] = TAOH_SUPER_ORGANIZER_TOKEN;
}
/* /Collecting organizer ptokens */

$show_rsvp_confirmation = isset($_GET['confirmation']) && $_GET['confirmation'] === 'rsvp';
$show_rsvp_ticket = isset($_GET['confirmation']) && $_GET['confirmation'] === 'rsvp_ticket';
$show_upgrade = isset($_GET['upgrade']) && $_GET['upgrade'] === 'from_email';
$rsvp_ticket_token = !empty($_GET['tickettoken']) ? $_GET['tickettoken'] : '';

$event_detail_url = TAOH_EVENTS_URL.'/d/'.slugify2($event_title).'-'.$eventtoken;
$event_detail_url_param = '';
if((isset($_GET['confirmation']) && ($_GET['confirmation'] === 'rsvp' || $_GET['confirmation'] === 'rsvp_ticket' )) && (isset($_GET['tickettoken']) &&  $_GET['tickettoken'] != '')){
    $event_detail_url_param = '?tickettoken=' . $rsvp_ticket_token . '&confirmation=rsvp_ticket';
}

if (!$taoh_user_is_logged_in) {
    $event_detail_url = $event_detail_url . $event_detail_url_param;
    taoh_redirect($event_detail_url);
    exit();
}

$event_organizer_banner = [];

$search = $type = ''; // wrongly handled type nd search in get_event_MetaInfo fn cache_name so here used
$cache_name = 'event_MetaInfo_' . $eventtoken . '_' . $type . '_' . $search;
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_api_token(1, 1),
    'eventtoken' => $eventtoken,
    'cfcc5h' => 1,
    'cache_name' => $cache_name,
);
$get_event_meta_info_response = taoh_apicall_get('events.content.get', $taoh_vals);
$get_event_meta_info_arr = json_decode($get_event_meta_info_response, true);
if (in_array($get_event_meta_info_arr['success'], [true, 'true']) && !empty($get_event_meta_info_arr['output'])) {
    $event_meta_info = $get_event_meta_info_arr['output'];

    $event_organizer_banner = $event_meta_info['event_organizer_banner'][0] ?? [];
}

define('TAO_PAGE_AUTHOR', 'Event Organizer');
define('TAO_PAGE_DESCRIPTION', $event_short);
define('TAO_PAGE_IMAGE', $event_image);
define('TAO_PAGE_TITLE', $event_title);
define('TAO_PAGE_ROBOT', 'index, follow');
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', TAOH_SITE_NAME_SLUG." Virtual job fair, ".TAOH_SITE_NAME_SLUG." Online career fair, ".TAOH_SITE_NAME_SLUG." Job fair event, ".TAOH_SITE_NAME_SLUG." Virtual networking opportunities, ".TAOH_SITE_NAME_SLUG." Remote job opportunities, ".TAOH_SITE_NAME_SLUG." Connecting talent and employers, ".TAOH_SITE_NAME_SLUG." Career advancement fair, ".TAOH_SITE_NAME_SLUG." Industry-specific job fair, ".TAOH_SITE_NAME_SLUG." Virtual recruitment event,".TAOH_SITE_NAME_SLUG." Professional networking event, ".TAOH_SITE_NAME_SLUG." Talent showcase platform, ".TAOH_SITE_NAME_SLUG." Online hiring event, ".TAOH_SITE_NAME_SLUG." Remote job fair, ".TAOH_SITE_NAME_SLUG." Job fair for job seekers, ".TAOH_SITE_NAME_SLUG." Virtual career fair, ".TAOH_SITE_NAME_SLUG." Job fair networking, ".TAOH_SITE_NAME_SLUG." Online job search event, ".TAOH_SITE_NAME_SLUG." Talent acquisition fair, ".TAOH_SITE_NAME_SLUG." Virtual job fair platform" ); }
$additive = '';
if(isset($site_info['source']) && $site_info['source'] !='' && TAOH_SITE_URL_ROOT != $site_info['source']){
    $canonical_url = $site_info['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken;
    $additive = '<link rel="canonical" href="'.$canonical_url.'"/> 
	<meta name="original-source" content="'.$canonical_url.'"/>';
}
define('TAO_PAGE_CANONICAL', $additive);

// Get RSVP status
$taoh_vals = array(
    'ops' => 'status',
    'mod' => 'events',
    'token' => TAOH_API_TOKEN,
    'eventtoken' => $eventtoken,
    'cache_required' => 0,
);
$rsvp_status_result = taoh_apicall_get('events.rsvp.get', $taoh_vals);
$rsvp_status_response = taoh_get_array($rsvp_status_result);

$is_user_rsvp_done = $rsvp_status_response['success'];
$event_detail_url = TAOH_EVENTS_URL.'/d/'.slugify2($event_title).'-'.$eventtoken;

// If the user is not rsved, redirect to the event detail page
if (!$is_user_rsvp_done) {
    taoh_redirect($event_detail_url);
    exit();
}
$rsvp_done_already = $rsvp_status_response['success'];
$rsvp_slug = $rsvp_status_response['output']['rsvp_slug'];
$rsvp_token = $rsvp_status_response['output']['rsvptoken'];

$ref_param =  taoh_parse_url(5);
$ref_slug = taoh_parse_url(6);
if($ref_param != '' && $ref_param != 'stlo'){
    $share_link = removePathSegment($sharerlink, 6);
}else{
    $share_link = $event_detail_url;
}

if($ref_slug != '' && $ref_slug != 'stlo'){
    $share_link = removePathSegment($share_link, 6);
}else{
    $share_link = $event_detail_url;
    $ref_slug = '';
}

$original_link = $share_link;
$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){
    $trackingtoken = hash('sha256', (string)$ptoken);
    $share_link = addPathSegment($share_link, 'stlo', $trackingtoken);
}

$social_token = '';
if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {
    $hashptoken = hash('sha256', (string)$ptoken);
    if ($ptoken !== '' && $hashptoken === (string)$ref_param) {
        $social_token = $ref_param;
    }
}

$success_discount_amt = '';
$success_sponsor_title = '';
$success_redirect = '';
$discount_amt = 0;

if(isset($ref_slug) && $ref_slug != '' && $ref_slug != 'stlo'){
    $ticketarr = array_column($ticket_types, 'social_sharing_discount', 'title');

    foreach($sponsor_levels as $value){
        if($value['slug'] == $ref_slug){
            if(array_key_exists($value['award_ticket_type'], $ticketarr)){
                $discount_amt = $ticketarr[$value['award_ticket_type']];
            }
            if($discount_amt > 0){
                $success_discount_amt = $discount_amt.'%';
                $success_sponsor_title = $value['title'];
                $success_redirect = TAOH_SITE_URL_ROOT.'/events/event_sponsor/'.$eventtoken.'/'.$ref_slug.'/socialshare/'.$trackingtoken;
            }
            break;
        }
    }
}
// Store discount info for template use
$discount_info = [
    'amount' => $success_discount_amt,
    'sponsor_title' => $success_sponsor_title,
    'redirect' => $success_redirect
];

// Get RSVP data
$taoh_vals = array(
    'ops' => 'info',
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'rsvptoken' => $rsvp_token,
);
$rsvp_data_result = taoh_apicall_get('events.rsvp.get', $taoh_vals);
$rsvp_data_response = taoh_get_array($rsvp_data_result);

$rsvp_data = $rsvp_data_response['output'];

$current_ticket_types = [];
foreach ($ticket_types as $item) {
    if ($item['slug'] === $rsvp_slug) {
        $current_ticket_types[] = $item;
    }
}

if (empty($current_ticket_types)) {
    taoh_redirect(TAOH_EVENTS_URL);
    exit();
}

$current_ticket_type = array_values($current_ticket_types)[0];
$is_user_paid = $rsvp_data['success'] && ($current_ticket_type['price'] === 'paid' && ($rsvp_data['payment_status'] ?? null) === '1');

if($event_arr['conttoken']['locality'] == ''){
    $event_arr['conttoken']['locality'] = 0;
}

$live_state = event_live_state($event_arr['utc_start_at'], $event_arr['utc_end_at'], $event_arr['status'], $event_arr['conttoken']['locality']);
$event_type = strtolower($events_data['event_type'] ?? 'virtual');

$live_btn_as_link = ($event_type === 'virtual' || $event_type === 'hybrid');

function find_title_slug($slug, $ticket_types, $field = 'slug')
{
    foreach ($ticket_types as $element) {
        if ($slug == $element['slug']) {
            return string_to_id($element[$field]);
        }
    }
}

function edit_prefill($tab, $field, $response, $ticket_types){
    $return = "";
    if ($response) {
        $return = $response->$field;
    }
    return $return;
}

function string_to_id($string)
{
    return strtolower(preg_replace('/\s+/', '', $string));
}


$enable_chat_room = 1;
if (isset($events_data['enable_chat_room'])) {
    $enable_chat_room = $events_data['enable_chat_room'];
}

$message_content = $events_data['message_content'] ?? '';
$chat_room_status = (int)$events_data['chat_room_status'] ?? 1;


$event_timestamp_start_data = [
    'utc_datetime'=> $event_arr['utc_start_at'],
    'local_datetime'=> $event_arr['local_start_at'],
    'timezone'=> $event_arr['local_timezone'],
    'locality'=>   $event_arr['conttoken']['locality']
];
$event_timestamp_end_data = [
    'utc_datetime'=> $event_arr['utc_end_at'],
    'local_datetime'=> $event_arr['local_end_at'],
    'timezone'=> $event_arr['local_timezone'],
    'locality'=>   $event_arr['conttoken']['locality']
];

$localized_event_start_data = get_localized_event_data($event_timestamp_start_data, taoh_user_timezone() ?? 'UTC');
$localized_event_ends_data = get_localized_event_data($event_timestamp_end_data, taoh_user_timezone() ?? 'UTC');

$event_start_at = (!empty($localized_event_start_data) ? beautifyTime($localized_event_start_data['datetime'], $localized_event_start_data['timezone'], '{week}, {month} {day}, {year}, {time} {abbr}') : '');


$event_locality = $event_arr['conttoken']['locality'] !='' ? $event_arr['conttoken']['locality'] : 0;
$event_timezone = $event_arr['local_timezone'];

$adopter_url = TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/club/' . taoh_slugify($events_data['title']) . '-' . $event_arr['eventtoken'];

if ($live_state == 'live') {
    $event_live_link = ($chat_room_status == 2 && filter_var($events_data['external_link'] ?? '', FILTER_VALIDATE_URL))
        ? $events_data['external_link']
        : $adopter_url;
}

$is_event_suspended = isset($event_arr['status']) && $event_arr['status'] == 2;
$is_event_freeze = isset($events_data['freeze_option']) && $events_data['freeze_option'] == 1;
define('TAO_CURRENT_APP_INNER_PAGE', 'events_lobby');
taoh_get_header();
$GLOBALS['show_events_css'] = true;

require_once TAOH_APP_PATH . '/events/event_health_check.php';
?>
<style>
    .org-msg-wrapper {
        padding-top: 3rem !important;
        border-top: 2px solid #d3d3d3;
    }

.exh-ts-control {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap; /* Allows tags to move to next line */
}

.exh-ts-control .tag {
    padding: 5px 10px;
    border: 1px solid #888e97;
    margin-right: 5px;
    margin-bottom: 5px;
    display: inline-block;
}

.exh-ts-control input {
    flex-grow: 1; /* Allow the input to grow and take up space */
    min-width: 100px; /* Minimum width to prevent shrinking too much */
}

.club-event-image {
  width: 100%;
  height: 100%;
  border-radius: 10px 10px 0 0;
  object-fit: contain;
  position: relative;
  z-index: 1;
  border-bottom: 1px solid #d3d3d3;
}
.club-events-bg {
  position: absolute;
  width: 100%;
  height: 100%;
  background-size: cover;
  border-radius: 10px 10px 0 0;
  z-index: 0;
}
.club-event-container {
  width: 100%;
  height: 150px;
  position: relative;
}
.club-glass-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  z-index: 1;
  border-radius: 10px 10px 0 0;
}
.aw-loader {
    height:1000px;
}

.joinus-btn {
    min-width: 110px;
    min-height: 42px;
    font-size: 18px;
    font-weight: 500;
}

    .detail-main-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        max-height: 320px;
        min-height: 136px;
        border: 1px solid #d3d3d3;
    }

    #setup_speaker_slot,
    #setup_exhibitor_slot, #share_event_slot, .edit-rsvp, .calendar-dropdown, #networking_link {
        flex: 1;
        max-width: 100%;
        white-space: normal !important;
    }

    #setup_speaker_slot:only-child, #networking_link:only-child,
    #setup_exhibitor_slot:only-child, #share_event_slot:only-child, .edit-rsvp:only-child, .calendar-dropdown:only-child {
        flex: 0 0 100%;
        white-space: normal !important;
        min-width: 0;
    }

    .sticky-top-fixed{
        top: -75px;
    }
    .modal.sponsorship-option#upgradeModal .modal-dialog .modal-body {
        margin: auto;
    }
    .new-exh-list .n-info-badge {
        color: #2557A7;
    }
    @media (max-width: 1399.98px) {
        .dropdown-menu.agenda_more.show {
            left: -130px !important;
        }
    }
    #upgradeModal #upgradeCards {
        justify-content: flex-start;
    }
    .main, .sticky-top.sticky-top-fixed {
        padding: 0 15px;
    }
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .events-btns-outer-block {
            max-width: 60%;
        }
    }
    @media (max-width: 991.98px) {
        .events-btns-outer-block {
            max-width: 320px;
        }
    }

    </style>

    <input type="hidden" id="share_link" value="<?= $share_link ?>">


<div class="event-new-flow pt-4 pb-5 aw aw-logo aw-loader light-dark">
    <div class="mx-auto" style="max-width: 1215px;">
        <div class="container">

            <!-- status-->

            <?php
            
            if( $show_rsvp_confirmation){
                if (strlen($rsvp_token) >= 5 || $current_ticket_type['price'] === 'free' || ($current_ticket_type['price'] === 'paid' && $is_user_paid)) { ?>
                    <div class="pb-3" style="border-bottom : 2px solid #D3D3D3; min-height: 71px; gap: 12px;">
                        <div class="event-status">
                            <div class="event-success px-3 py-4 px-lg-5" style="position: relative;">

                                <div class="d-flex align-items-center py-2 flex-column flex-md-row" style="gap: 2rem;">
                                    <svg width="62" height="62" viewBox="0 0 62 62" fill="none" xmlns="http://www.w3.org/2000/svg" style="min-width: fit-content; z-index: 1;">
                                        <path d="M31 62C39.2217 62 47.1067 58.7339 52.9203 52.9203C58.7339 47.1067 62 39.2217 62 31C62 22.7783 58.7339 14.8933 52.9203 9.07969C47.1067 3.26606 39.2217 0 31 0C22.7783 0 14.8933 3.26606 9.07969 9.07969C3.26606 14.8933 0 22.7783 0 31C0 39.2217 3.26606 47.1067 9.07969 52.9203C14.8933 58.7339 22.7783 62 31 62ZM44.6836 25.3086L29.1836 40.8086C28.0453 41.9469 26.2047 41.9469 25.0785 40.8086L17.3285 33.0586C16.1902 31.9203 16.1902 30.0797 17.3285 28.9535C18.4668 27.8273 20.3074 27.8152 21.4336 28.9535L27.125 34.6449L40.5664 21.1914C41.7047 20.0531 43.5453 20.0531 44.6715 21.1914C45.7977 22.3297 45.8098 24.1703 44.6715 25.2965L44.6836 25.3086Z" fill="#333333"/>
                                    </svg>

                                    <div style="z-index: 1;">
                                        <?php
                                        echo '<p class="text-md">Thank you. Your reservation is confirmed.';
                                        if ($event_type === 'in-person') {
                                            $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                                                '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer text-underline">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                                            echo ' See you in ' . $event_venue_loc . ' on ' . $event_start_at . '.';

                                        } elseif ($event_type === 'hybrid') {
                                            $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];
                                            $event_venue_loc = (!empty($events_data['map_link']) && filter_var($events_data['map_link'], FILTER_VALIDATE_URL)) ?
                                                '<a href="' . $events_data['map_link'] . '" target="_blank" class="cursor-pointer text-underline">' . $events_data['venue'] . '</a>' : $events_data['venue'];

                                            echo ' See you in ' . $event_venue_loc . '. or <a href="' . $lobby_link . '" title="'.$lobby_link.'" target="_blank" class="cursor-pointer text-underline">here</a> on ' . $event_start_at . '.';

                                        } elseif ($event_type === 'virtual') {
                                            $lobby_link = TAOH_CURR_APP_URL . '/chat/id/events/' . $event_arr['eventtoken'];

                                            echo ' See you <a href="' . $lobby_link . '" title="'.$lobby_link.'" class="cursor-pointer text-underline">here</a> on ' . $event_start_at . '.';
                                        }

                                        echo '</p>';
                                        ?>
                                    </div>
                                </div>

                                <!-- celebration svgs start -->
                                <div style="position: absolute; bottom: 20px;  right: 160px;">
                                    <div>
                                        <div>
                                            <svg width="18" height="25" viewBox="0 0 18 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.318 3.77589C16.365 5.23514 14.7135 6.47583 13.6295 7.61131C11.906 9.43762 10.2605 11.3433 8.68918 13.3165C6.90692 15.5672 5.23399 17.9133 3.67457 20.3273C2.9046 21.5442 1.89899 23.1643 2.04716 24.6725C1.56774 24.6054 1.12102 24.3969 0.7192 24.0039C-0.120703 22.8634 0.0796621 21.5432 0.633758 20.2654C1.4649 18.3487 2.84163 16.5823 4.04847 14.897C5.40672 12.9928 6.83148 11.1324 8.33424 9.3514C10.0716 7.28521 11.9027 5.3064 13.8117 3.40695C14.7683 2.44348 15.8447 1.28432 16.0959 -5.67698e-05L17.318 3.77589Z" fill="url(#paint0_linear_2972_101)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2972_101" x1="16.814" y1="4.97013" x2="0.179527" y2="20.0757" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#BC1558"/>
                                                        <stop offset="0.980392" stop-color="#BC1558"/>
                                                        <stop offset="1" stop-color="#BC1558"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>

                                        <div style="margin-top: -15px;">
                                            <svg width="28" height="23" viewBox="0 0 28 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.85009 10.17C0.811093 10.1303 0.783931 10.0868 0.7606 10.0551C0.7411 10.0352 0.737269 10.0234 0.71777 10.0036C1.11959 10.3966 1.56631 10.6051 2.04573 10.6722C3.11842 10.835 4.30381 10.2552 5.234 9.61408C7.20091 8.29744 8.98221 6.60935 10.813 5.11447C12.9727 3.34311 17.0873 -1.07483 20.0818 1.5913C20.6285 2.0682 21.1821 2.64746 21.6728 3.23404L23.0175 7.38876C22.877 8.16656 22.5791 8.94299 22.2188 9.64805C20.6988 12.507 17.2204 15.699 18.4655 19.1013C19.1713 19.3829 19.9421 19.4211 20.776 19.452C22.0032 19.4863 23.1939 19.6501 24.3711 20.0145C25.5207 20.3747 26.606 20.8995 27.6036 21.5574L28.0059 22.8003C27.1556 22.3955 26.2653 22.069 25.3584 21.8526C23.6071 21.4518 21.2853 21.9548 19.7599 20.9185C18.8173 20.269 17.9026 19.1397 17.218 18.2367C16.1406 16.8069 16.1341 14.8475 16.7776 13.24C18.0326 10.0876 22.272 6.7016 21.0011 3.09835C18.1545 1.97975 14.9261 5.37853 12.921 7.02139C11.1417 8.47344 9.42064 10.0244 7.57524 11.3932C6.47831 12.2059 4.91173 13.1838 3.49188 12.6756C2.44075 12.2966 1.53537 10.9943 0.85009 10.17Z" fill="url(#paint0_linear_2972_103)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2972_103" x1="21.3594" y1="7.7816" x2="6.23076" y2="21.5197" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#F599D0"/>
                                                        <stop offset="0.0117647" stop-color="#F599D0"/>
                                                        <stop offset="1" stop-color="#BC1558"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                                <div style="position: absolute; right: 120px; top: 40px;">
                                    <div>
                                        <div>
                                            <svg width="47" height="15" viewBox="0 0 47 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.05631 3.57771C-1.09793 3.16198 0.716167 3.6533 0.678374 3.6533C1.20749 3.6155 3.32394 3.69109 3.62629 3.72888C5.85612 3.84226 8.04816 4.40917 10.1268 5.16505C16.7785 7.62164 22.561 12.5726 29.6284 13.8954C31.9338 14.3111 34.0881 14.2733 36.2045 13.9332H36.2423C37.6785 13.7442 40.1729 13.0639 40.6264 12.9128C40.702 12.9128 40.702 12.9128 40.702 12.9128C42.8562 12.3081 46.4466 10.7963 46.4466 10.7963C45.8419 11.0987 44.1034 11.2498 43.1208 11.2876C43.1208 11.2876 43.1208 11.2876 43.1586 11.2876C42.7806 11.3254 42.4783 11.3254 42.4027 11.3254C39.9461 11.401 37.4895 11.0231 35.1463 10.2672C24.6774 6.86576 16.2872 -1.94018 4.53334 1.87699C4.11761 1.99037 3.70187 2.14154 3.28614 2.25493C2.98379 2.78404 1.62322 3.6533 1.05631 3.57771Z" fill="url(#paint0_linear_2922_67)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2922_67" x1="35.5238" y1="11.8553" x2="10.9292" y2="3.2678" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#1897C9"/>
                                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                                        <stop offset="0.490196" stop-color="#18B7C9"/>
                                                        <stop offset="0.909804" stop-color="#19D8C9"/>
                                                        <stop offset="1" stop-color="#19D8C9"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div style="margin-top: -67px; margin-left: 28.5px;">
                                            <svg width="23" height="61" viewBox="0 0 23 61" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10.931 60.1191C10.7042 60.1947 10.4775 60.3081 10.2507 60.3837C10.2507 60.3459 10.2885 60.3459 10.2885 60.3459C10.1373 60.3837 9.98614 60.4593 9.87276 60.4971C9.87276 60.3837 9.91055 60.2703 9.98614 60.1191C10.0617 59.968 10.4019 59.7412 10.742 59.5144C10.5908 59.5522 10.4397 59.5522 10.3263 59.59C10.3641 59.5522 10.4019 59.5144 10.4019 59.5144C15.0883 55.5839 13.9545 49.4991 12.216 44.3969C9.83497 37.4051 7.11381 30.5266 4.58163 23.5726C2.50298 17.9413 -0.747288 11.2518 1.86048 5.2426C2.9565 2.71041 4.84619 1.31204 7.00043 0.329407C8.47439 -0.0485306 9.94835 -0.0863266 11.3467 0.140438C6.88705 5.46936 10.5908 14.691 12.7829 20.7002C14.597 25.5756 16.3733 30.451 18.1874 35.3264C19.6991 39.4459 21.8534 43.8678 22.0423 48.3275C22.3069 54.7902 17.9228 57.6247 12.5183 59.5522C12.027 59.7412 11.5735 59.8924 11.0822 60.0813C11.0444 60.0813 10.9688 60.1191 10.931 60.1191ZM12.9341 59.2499L12.8585 59.2877C12.8963 59.2499 12.9341 59.2499 12.9341 59.2499Z" fill="url(#paint0_linear_2922_66)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2922_66" x1="0.890694" y1="30.2481" x2="22.056" y2="30.2481" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#1897C9"/>
                                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                                        <stop offset="0.329412" stop-color="#18B7C9"/>
                                                        <stop offset="0.819608" stop-color="#19D8C9"/>
                                                        <stop offset="1" stop-color="#19D8C9"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div style="position: absolute; right: 20px; bottom: 0;">
                                    <div>
                                        <div>
                                            <svg width="53" height="75" viewBox="0 0 53 75" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M52.7953 11.4704C49.9001 15.9032 44.8833 19.6722 41.5904 23.1215C36.3549 28.6693 31.3564 34.4582 26.5831 40.4523C21.1691 47.2894 16.0871 54.4162 11.35 61.7493C9.01106 65.4459 5.95628 70.3674 6.40637 74.9489C4.95002 74.7449 3.59302 74.1116 2.37239 72.9177C-0.179023 69.4533 0.429634 65.4428 2.11283 61.5612C4.63764 55.7387 8.81977 50.3729 12.4858 45.2536C16.6118 39.469 20.9399 33.8176 25.5049 28.4074C30.7826 22.1308 36.3448 16.1197 42.144 10.3497C45.0499 7.42293 48.3195 3.90172 49.0826 0.000102901L52.7953 11.4704Z" fill="url(#paint0_linear_2922_50)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2922_50" x1="51.2643" y1="15.0982" x2="0.732993" y2="60.9849" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#55BD02"/>
                                                        <stop offset="0.980392" stop-color="#CBC601"/>
                                                        <stop offset="1" stop-color="#CBC601"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                        <div style="margin-top: -34px;">
                                            <svg width="84" height="68" viewBox="0 0 84 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.775471 29.4231C0.656998 29.3026 0.574493 29.1704 0.503617 29.0742C0.444382 29.0139 0.432743 28.9779 0.373514 28.9177C1.59415 30.1115 2.95114 30.7449 4.4075 30.9488C7.66605 31.4433 11.267 29.6819 14.0926 27.7345C20.0676 23.7349 25.4787 18.6069 31.0401 14.0659C37.6008 8.68496 50.0999 -4.73561 59.1964 3.36341C60.857 4.81212 62.5389 6.57176 64.0293 8.35363L68.1144 20.9746C67.6874 23.3373 66.7824 25.6959 65.6881 27.8377C61.0706 36.5223 50.5043 46.2188 54.2866 56.5543C56.4305 57.4096 58.772 57.5257 61.305 57.6195C65.0331 57.7237 68.6502 58.2213 72.2261 59.3282C75.7185 60.4224 79.0152 62.0168 82.0455 64.0153L83.2676 67.7908C80.6847 66.5611 77.9803 65.5694 75.2251 64.912C69.9052 63.6943 62.8522 65.2224 58.2184 62.0745C55.3552 60.1014 52.5765 56.6709 50.4968 53.9277C47.224 49.5846 47.2042 43.6323 49.1591 38.7491C52.9716 29.173 65.8498 18.8872 61.9891 7.94143C53.3417 4.54341 43.5348 14.868 37.4436 19.8586C32.0387 24.2695 26.8105 28.9808 21.2047 33.139C17.8725 35.6079 13.1137 38.5783 8.80054 37.0348C5.60747 35.8834 2.85717 31.9273 0.775471 29.4231Z" fill="url(#paint0_linear_2922_51)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_2922_51" x1="63.0775" y1="22.1679" x2="17.1205" y2="63.9006" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#CBC601"/>
                                                        <stop offset="0.0117647" stop-color="#CBC601"/>
                                                        <stop offset="1" stop-color="#55BD02"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <!-- celebration svgs ends -->

                            </div>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class="pb-3" style="border-bottom : 2px solid #D3D3D3; min-height: 71px; gap: 12px;">
                        <div class="event-status">
                            <div class="event-success px-3 py-5 px-lg-5" style="position: relative;">
                                <div class="d-flex align-items-center py-2 flex-column flex-md-row" style="gap: 2rem;">
                                    <svg width="62" height="62" viewBox="0 0 62 62" fill="none" xmlns="http://www.w3.org/2000/svg" style="min-width: fit-content; z-index: 1;">
                                        <path d="M31 62C39.2217 62 47.1067 58.7339 52.9203 52.9203C58.7339 47.1067 62 39.2217 62 31C62 22.7783 58.7339 14.8933 52.9203 9.07969C47.1067 3.26606 39.2217 0 31 0C22.7783 0 14.8933 3.26606 9.07969 9.07969C3.26606 14.8933 0 22.7783 0 31C0 39.2217 3.26606 47.1067 9.07969 52.9203C14.8933 58.7339 22.7783 62 31 62ZM44.6836 25.3086L29.1836 40.8086C28.0453 41.9469 26.2047 41.9469 25.0785 40.8086L17.3285 33.0586C16.1902 31.9203 16.1902 30.0797 17.3285 28.9535C18.4668 27.8273 20.3074 27.8152 21.4336 28.9535L27.125 34.6449L40.5664 21.1914C41.7047 20.0531 43.5453 20.0531 44.6715 21.1914C45.7977 22.3297 45.8098 24.1703 44.6715 25.2965L44.6836 25.3086Z" fill="#333333"/>
                                    </svg>

                                    <div style="z-index: 1;">
                                        <p class="text-md">Sorry, your order has failed. Please try again!</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php }
            ?>

                <?php if( $live_state != 'live') { ?>
                <!-- upcoming events -->

                    <section class="club-section" style="width:100%;">
                        <div class="container">
                            <div id="events_blk" class="p-1 row">
                                <div class="col-lg-12">
                                    <div class="mt-3 row d-flex align-items-center flex-wrap-reverse">
                                        <div class="col-lg-6 mb-3 mb-lg-0"><h4>Upcoming Events</h4></div>

                                    </div>
                                    <div id="event_loaderArea"></div>
                                    <div id="events_list" class="d-flex flex-wrap justify-content-center mt-4 upcoming-events dasdasd p-0" style="margin: auto; gap: 12px;" >
                                        <?php echo taoh_recent_events_full_display($eventtoken);?>
                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?php echo TAOH_SITE_URL_ROOT.'/events';?>">View all Events <i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php } ?>
            <?php } ?>

            <div class="sticky-top sticky-top-fixed light-dark" style="z-index: 10;">
                <div class="max-w container pl-0 pr-0 pt-3 pb-2 border-bottom" id="event_info_container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3 mb-3" role="tablist" 
                        style="background:none;line-height: 1.143;">
                        <li class="nav-item" >
                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/';?>">Home</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <li class="nav-item" >
                            <a href="<?php echo TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG;?>">Events</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <li class="nav-item event_title">

                        </li>
                    </ul>
                    <div class="d-flex align-items-lg-center flex-column flex-lg-row pb-3 pt-3" style="gap: 9px;">
                        <div class="d-none d-lg-block" id="event_top_banner" style="position: relative;">
                            <div class="d-none" id="event_banner_container" style="display: none;">

                            </div>

                        </div>


                        <div class="d-flex flex-wrap flex-xl-nowrap align-items-center" style="gap: 8px; flex: 1;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1 event-title mr-2 line-clamp-2 event_title"></h5>
                                <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                                    <svg style="min-width: fit-content;" width="22" height="22" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"/>
                                    </svg>
                                    <p class="e-v2-info"><span id="event_start_end_datetime" class="event-day"></span></p>


                                </div>
                                <div class="d-flex align-items-center mb-1 mobile-none" style="gap: 8px;">
                                    <svg style="min-width: fit-content;" width="22" height="22" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.5 25C15.8152 25 18.9946 23.683 21.3388 21.3388C23.683 18.9946 25 15.8152 25 12.5C25 9.18479 23.683 6.00537 21.3388 3.66117C18.9946 1.31696 15.8152 0 12.5 0C9.18479 0 6.00537 1.31696 3.66117 3.66117C1.31696 6.00537 0 9.18479 0 12.5C0 15.8152 1.31696 18.9946 3.66117 21.3388C6.00537 23.683 9.18479 25 12.5 25ZM10.1855 17.4463L8.28125 16.7871C7.96387 16.6797 7.61719 16.6748 7.2998 16.7725L6.55273 17.0117C5.64941 17.2998 4.67285 16.8945 4.2334 16.0596L4.07227 15.7568C3.55469 14.7754 3.95996 13.5596 4.96094 13.0859L6.68457 12.2656C6.79687 12.2119 6.89941 12.1289 6.97266 12.0312L7.23145 11.6895C7.58301 11.2207 8.13965 10.9424 8.72559 10.9424C9.31152 10.9424 9.86816 11.2207 10.2197 11.6895L10.4443 11.9873C10.542 12.1143 10.6836 12.207 10.8398 12.2363C11.2207 12.3145 11.6064 12.1631 11.8359 11.8506L12.3438 11.1572C12.4414 11.0205 12.6025 10.9424 12.7686 10.9424C12.9834 10.9424 13.1787 11.0742 13.2568 11.2744L13.75 12.5391C13.8867 12.8906 14.0771 13.2227 14.3115 13.5254L15.1855 14.6387C15.4688 15 15.625 15.4492 15.625 15.9082C15.625 16.3672 15.4688 16.8164 15.1855 17.1777L14.5996 17.9297C14.1943 18.4473 13.5742 18.75 12.9199 18.75C12.5098 18.75 12.1094 18.6328 11.7627 18.4082L10.5225 17.6074C10.415 17.5391 10.3027 17.4854 10.1855 17.4414V17.4463ZM13.3691 6.95801L14.4531 8.04199C14.9463 8.53516 14.5947 9.375 13.9014 9.375H12.4414C12.168 9.375 11.8994 9.31641 11.6504 9.20898L9.56055 8.28125C8.8623 7.97363 8.97949 6.94824 9.72656 6.80176L11.6064 6.42578C12.2461 6.29883 12.9102 6.49902 13.3691 6.95801ZM12.1094 21.0938C12.1094 20.6641 12.4609 20.3125 12.8906 20.3125H13.6719C14.1016 20.3125 14.4531 20.6641 14.4531 21.0938C14.4531 21.5234 14.1016 21.875 13.6719 21.875H12.8906C12.4609 21.875 12.1094 21.5234 12.1094 21.0938ZM21.0547 14.5947L21.4453 15.7666C21.582 16.1768 21.3623 16.6162 20.9521 16.7529C20.542 16.8896 20.1025 16.6699 19.9658 16.2598L19.5752 15.0879C19.4385 14.6777 19.6582 14.2383 20.0684 14.1016C20.4785 13.9648 20.918 14.1846 21.0547 14.5947ZM20.083 18.5205L18.5205 20.083C18.2178 20.3857 17.7197 20.3857 17.417 20.083C17.1143 19.7803 17.1143 19.2822 17.417 18.9795L18.9795 17.417C19.2822 17.1143 19.7803 17.1143 20.083 17.417C20.3857 17.7197 20.3857 18.2178 20.083 18.5205Z" fill="#2557A7"/>
                                    </svg>
                                    <p class="e-v2-info" id="event_venue_info"></p>
                                </div>
                            </div>
                            <?php 
                            $class = '';
                            if($is_event_freeze || $is_event_suspended) $class ='event-live-end';
                            else if($live_state == 'live')   $class ='event-live-on';
                            else if($live_state == 'after')   $class ='event-live-end';
                            else $class ='event-live-before';
                                
                                ?>
                            <div class="flex-shrink-lg-0 ticket-card-div d-flex flex-row flex-wrap flex-xl-column events-btns-outer-block <?php echo $class;?>" style="gap: 6px;">
                                
                            <?php if($live_state != 'live') { ?>
                                <a href="<?php echo $adopter_url;?>" id="networking_link" style="display:none;"  class="btn btn-success">Go to Networking Room</a>
                            <?php } ?>
                                
                                <div class="event_status_button d-flex flex-wrap flex-xl-column" style="gap: 6px;">
                                    <input type="hidden" name="chat_room_status" id="chat_room_status" value="<?php echo $chat_room_status;?>"/>
                                    <?php
                                    if ($is_event_freeze || $is_event_suspended) {
                                        echo '<span class="btn event-end d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">
                                            <svg width="24" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.85714 0C7.80536 0 8.57143 0.754102 8.57143 1.6875V3.375H15.4286V1.6875C15.4286 0.754102 16.1946 0 17.1429 0C18.0911 0 18.8571 0.754102 18.8571 1.6875V3.375H21.4286C22.8482 3.375 24 4.50879 24 5.90625V8.4375H0V5.90625C0 4.50879 1.15179 3.375 2.57143 3.375H5.14286V1.6875C5.14286 0.754102 5.90893 0 6.85714 0ZM0 10.125H24V24.4688C24 25.8662 22.8482 27 21.4286 27H2.57143C1.15179 27 0 25.8662 0 24.4688V10.125ZM16.3393 16.084C16.8429 15.5883 16.8429 14.7867 16.3393 14.2963C15.8357 13.8059 15.0214 13.8006 14.5232 14.2963L12.0054 16.7748L9.4875 14.2963C8.98393 13.8006 8.16964 13.8006 7.67143 14.2963C7.17321 14.792 7.16786 15.5936 7.67143 16.084L10.1893 18.5625L7.67143 21.041C7.16786 21.5367 7.16786 22.3383 7.67143 22.8287C8.175 23.3191 8.98929 23.3244 9.4875 22.8287L12.0054 20.3502L14.5232 22.8287C15.0268 23.3244 15.8411 23.3244 16.3393 22.8287C16.8375 22.333 16.8429 21.5314 16.3393 21.041L13.8214 18.5625L16.3393 16.084Z" fill="#444444"/>
                                            </svg>
                                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>
                                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>        
                            
                                            <span>Event Suspended</span>
                                        </span>';
                                    } else {
                                        if ($live_state == 'live') {
                                            echo $event_live_link && $chat_room_status ? '<a href="' . $event_live_link . '" class="btn live d-flex align-items-center cursor-pointer px-3 metrics_action" style="gap: 12px;" data-metrics="events_join">' : '<span class="btn live d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">';
                                            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px">
                                                    <!-- Play circle -->
                                                    <circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                    <!-- Play triangle -->
                                                    <polygon points="34,28 34,52 54,40" fill="#28a745"></polygon>

                                                    <!-- Sound wave 1 -->
                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                    <!-- Sound wave 2 -->
                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                </svg>
                                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>                               
                                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live"  value="1"/>';
                                            if ($chat_room_status) {
                                                echo '<span>Event Live, ' . ((!$valid_user) ? 'Complete settings to' : 'Click to') . ' Join</span>';
                                            } else {
                                                echo '<span>Event Live</span>';
                                            }
                                            echo $event_live_link && $chat_room_status ? '</a>' : '</span>';
                                        } elseif ($live_state == 'after') {
                                            echo '<span class="btn event-end d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">
                                            <svg width="24" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.85714 0C7.80536 0 8.57143 0.754102 8.57143 1.6875V3.375H15.4286V1.6875C15.4286 0.754102 16.1946 0 17.1429 0C18.0911 0 18.8571 0.754102 18.8571 1.6875V3.375H21.4286C22.8482 3.375 24 4.50879 24 5.90625V8.4375H0V5.90625C0 4.50879 1.15179 3.375 2.57143 3.375H5.14286V1.6875C5.14286 0.754102 5.90893 0 6.85714 0ZM0 10.125H24V24.4688C24 25.8662 22.8482 27 21.4286 27H2.57143C1.15179 27 0 25.8662 0 24.4688V10.125ZM16.3393 16.084C16.8429 15.5883 16.8429 14.7867 16.3393 14.2963C15.8357 13.8059 15.0214 13.8006 14.5232 14.2963L12.0054 16.7748L9.4875 14.2963C8.98393 13.8006 8.16964 13.8006 7.67143 14.2963C7.17321 14.792 7.16786 15.5936 7.67143 16.084L10.1893 18.5625L7.67143 21.041C7.16786 21.5367 7.16786 22.3383 7.67143 22.8287C8.175 23.3191 8.98929 23.3244 9.4875 22.8287L12.0054 20.3502L14.5232 22.8287C15.0268 23.3244 15.8411 23.3244 16.3393 22.8287C16.8375 22.333 16.8429 21.5314 16.3393 21.041L13.8214 18.5625L16.3393 16.084Z" fill="#444444"/>
                                            </svg>
                                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>        
                            
                                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>
                                            <span>Ended</span>
                                        </span>';
                                        } else {
                                            echo '<span class="btn not-live d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">
                                            <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                            </svg>
                                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>        
                            
                                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="not_live" value="2"/>
                                            <span>Event Not Live!</span>
                                        </span>';
                                        }
                                    }

                                    ?>

                                    <button type="button" class="event_sponsor_right_header btn sponsor-btn" style="display:none;border:1px solid rgba(255, 193, 7, 0.8);border-radius: 6px;" data-toggle="modal" data-target="#sponsorInfo">
                                        <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 113.8" style="width: 29px;margin-right: 5px;fill: black;">
                                            <defs><style>.cls-1{fill-rule:evenodd;}</style></defs>
                                            <title>loudspeake</title>
                                            <path class="cls-1" d="M0,67.6c-.06-5.83,2.16-11,7.37-12.69l.13-.22a2.91,2.91,0,0,1,.6-.8,3.1,3.1,0,0,1,1.05-.5l.29-.11C34.7,44,46.09,30.07,52.64,20.61c0,9.29,2.33,21.4,6.43,33.12,4.21,12,10.29,23.71,17.71,31.76h0c-7.73-2.2-17.43-5-33.21-2.85a15.17,15.17,0,0,0-1.12,5.06,3.9,3.9,0,0,0,1,3l.24.22c1,.91,1.49,1.36,1.71,2.17a9.17,9.17,0,0,1-.13,3.22l-.09.67c-.37,2.93,1.05,3.53,2.46,4.13s2.51,1.08,3.08,2.69a.68.68,0,0,1,0,.4c-.58,3-2.5,4.64-4.43,6.29-.47.41-.95.82-1.36,1.21l-.06,0c-3.92,3.14-6.57,2.3-8.58-.22-1.58-2-2.68-5-3.7-7.81l-.44-1.22a122.88,122.88,0,0,1-4-15.09l-.34-1.5c-.87.24-1.78.51-2.73.79h0c-1.2.35-2.4.73-3.59,1.11l-.38.14a1.21,1.21,0,0,1-1.33,0C13.47,90,8.2,87,4.68,82a25,25,0,0,1-3.41-6.9A25.74,25.74,0,0,1,0,67.6ZM87.81,16.16a3,3,0,0,1-4.23-.6l-.07-.09a3.05,3.05,0,0,1,.63-4.19C89,7.69,93.84,4.07,98.7.56A3.09,3.09,0,0,1,100.92,0a3.06,3.06,0,0,1,2,1.21l0,.05a3,3,0,0,1,.52,2.23,3,3,0,0,1-1.21,2L87.81,16.16Zm15.79,57.7h0a3,3,0,0,1-2.11-.93,3,3,0,0,1-.85-2.14v-.07a3,3,0,0,1,3.07-3c5.37,0,10.83.2,16.2.3a3.07,3.07,0,0,1,3,3.1,3.09,3.09,0,0,1-.93,2.15,3,3,0,0,1-2.16.86l-16.19-.31Zm.47-15.71a3,3,0,0,1-3.25-2.78V55.3a3.07,3.07,0,0,1,2.77-3.23c5-.4,10.09-.83,15.1-1.15a3.06,3.06,0,0,1,3.24,2.81h0a3,3,0,0,1-.73,2.21A3.08,3.08,0,0,1,119.12,57c-4.85.48-10.18.88-15.05,1.14Zm-2.16-14.59a3.06,3.06,0,0,1-3.78-2.11v0a3,3,0,0,1,.27-2.3,3.1,3.1,0,0,1,1.83-1.46c5.21-1.37,10.4-3.05,15.61-4.45a3,3,0,0,1,2.3.27,3.08,3.08,0,0,1,1.45,1.8v0a3.07,3.07,0,0,1-2.1,3.78l-15.59,4.45Zm-6.3-14.32a3.05,3.05,0,0,1-4.09-1.37,3,3,0,0,1-.17-2.33,3.09,3.09,0,0,1,1.53-1.77l15-7.49a3,3,0,0,1,4.09,1.37,3,3,0,0,1,.16,2.33,3.07,3.07,0,0,1-1.53,1.76l-15,7.5ZM58.75,12c.13-.15.27-.29.4-.42a4.46,4.46,0,0,1,1.48-1.11h0a3.58,3.58,0,0,1,2-.08c2.25.36,4.66,2,7.11,4.51,6,6.19,12.32,18,16.82,30.41s7.2,25.36,5.85,33.77c-.51,3.13-1.57,5.66-3.31,7.33l-.09.07a5.64,5.64,0,0,1-2.47,1,2.43,2.43,0,0,0-.25-.2C80.3,83,75,75.9,70.54,67.55c1.14.22,2.6-.63,4-2.08,7-7.38,3.56-20.28-5.17-23.27C65.81,41,62.27,41,60.87,41.69l-.15.08C57.52,29.42,56.61,18,58.75,12Z"/>
                                        </svg>
                                        <svg id="Layer_2" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 113.8" style="width: 29px;margin-right: 5px;fill: white;">
                                            <defs><style>.cls-1{fill-rule:evenodd;}</style></defs>
                                            <title>loudspeake</title>
                                            <path class="cls-1" d="M0,67.6c-.06-5.83,2.16-11,7.37-12.69l.13-.22a2.91,2.91,0,0,1,.6-.8,3.1,3.1,0,0,1,1.05-.5l.29-.11C34.7,44,46.09,30.07,52.64,20.61c0,9.29,2.33,21.4,6.43,33.12,4.21,12,10.29,23.71,17.71,31.76h0c-7.73-2.2-17.43-5-33.21-2.85a15.17,15.17,0,0,0-1.12,5.06,3.9,3.9,0,0,0,1,3l.24.22c1,.91,1.49,1.36,1.71,2.17a9.17,9.17,0,0,1-.13,3.22l-.09.67c-.37,2.93,1.05,3.53,2.46,4.13s2.51,1.08,3.08,2.69a.68.68,0,0,1,0,.4c-.58,3-2.5,4.64-4.43,6.29-.47.41-.95.82-1.36,1.21l-.06,0c-3.92,3.14-6.57,2.3-8.58-.22-1.58-2-2.68-5-3.7-7.81l-.44-1.22a122.88,122.88,0,0,1-4-15.09l-.34-1.5c-.87.24-1.78.51-2.73.79h0c-1.2.35-2.4.73-3.59,1.11l-.38.14a1.21,1.21,0,0,1-1.33,0C13.47,90,8.2,87,4.68,82a25,25,0,0,1-3.41-6.9A25.74,25.74,0,0,1,0,67.6ZM87.81,16.16a3,3,0,0,1-4.23-.6l-.07-.09a3.05,3.05,0,0,1,.63-4.19C89,7.69,93.84,4.07,98.7.56A3.09,3.09,0,0,1,100.92,0a3.06,3.06,0,0,1,2,1.21l0,.05a3,3,0,0,1,.52,2.23,3,3,0,0,1-1.21,2L87.81,16.16Zm15.79,57.7h0a3,3,0,0,1-2.11-.93,3,3,0,0,1-.85-2.14v-.07a3,3,0,0,1,3.07-3c5.37,0,10.83.2,16.2.3a3.07,3.07,0,0,1,3,3.1,3.09,3.09,0,0,1-.93,2.15,3,3,0,0,1-2.16.86l-16.19-.31Zm.47-15.71a3,3,0,0,1-3.25-2.78V55.3a3.07,3.07,0,0,1,2.77-3.23c5-.4,10.09-.83,15.1-1.15a3.06,3.06,0,0,1,3.24,2.81h0a3,3,0,0,1-.73,2.21A3.08,3.08,0,0,1,119.12,57c-4.85.48-10.18.88-15.05,1.14Zm-2.16-14.59a3.06,3.06,0,0,1-3.78-2.11v0a3,3,0,0,1,.27-2.3,3.1,3.1,0,0,1,1.83-1.46c5.21-1.37,10.4-3.05,15.61-4.45a3,3,0,0,1,2.3.27,3.08,3.08,0,0,1,1.45,1.8v0a3.07,3.07,0,0,1-2.1,3.78l-15.59,4.45Zm-6.3-14.32a3.05,3.05,0,0,1-4.09-1.37,3,3,0,0,1-.17-2.33,3.09,3.09,0,0,1,1.53-1.77l15-7.49a3,3,0,0,1,4.09,1.37,3,3,0,0,1,.16,2.33,3.07,3.07,0,0,1-1.53,1.76l-15,7.5ZM58.75,12c.13-.15.27-.29.4-.42a4.46,4.46,0,0,1,1.48-1.11h0a3.58,3.58,0,0,1,2-.08c2.25.36,4.66,2,7.11,4.51,6,6.19,12.32,18,16.82,30.41s7.2,25.36,5.85,33.77c-.51,3.13-1.57,5.66-3.31,7.33l-.09.07a5.64,5.64,0,0,1-2.47,1,2.43,2.43,0,0,0-.25-.2C80.3,83,75,75.9,70.54,67.55c1.14.22,2.6-.63,4-2.08,7-7.38,3.56-20.28-5.17-23.27C65.81,41,62.27,41,60.87,41.69l-.15.08C57.52,29.42,56.61,18,58.75,12Z"/>
                                        </svg>
                                        Become a sponsor

                                    </button>
                                </div>
                                
                                    


                                <input type="hidden" name="is_organizer" id="is_organizer" value="0" >
                                <input type="hidden" name="user_profile_type" id="user_profile_type" value="" >
                                <input type="hidden" name="rsvp_sponsor_title" id="rsvp_sponsor_title" value="<?php echo $current_ticket_type['title'] ?? ''; ?>">
                                <input type="hidden" name="exh_count" id="exh_count" value="0" >
                                <input type="hidden" name="spk_count" id="spk_count" value="0" >
                                <input type="hidden" name="event_live_state" id="event_live_state" value="0" >
                                <input type="hidden" name="enable_exhibitor_hall" id="enable_exhibitor_hall" value="" >
                                <input type="hidden" name="enable_speaker_hall" id="enable_speaker_hall" value="" >
                                <input type="hidden" name="enable_hall" id="enable_hall" value="" >

                                <input type="hidden" name="event_country_lock" id="event_country_lock" value="" >
                                <input type="hidden" name="event_country_name" id="event_country_name" value="" >


                                <input type="hidden" name="superorganizer_token" id="superorganizer_token" value="<?php echo TAOH_SUPER_ORGANIZER_TOKEN;?>" >
                                <input id="sponsor_type" name="sponsor_type" type="hidden" value=""/>
                                <input id="rsvp_perpage" name="rsvp_perpage" type="hidden" value="1"/>
                                <div style="display:flex; flex-direction:column; width:100%;" class="scroll-top-hide">

                                    <div class="d-flex" style="gap: 8px;width:100%; margin-bottom: 2px;">
                                        <div id="setup_speaker_slot" style="display:none;margin:2px 0;" data-toggle="tooltip" data-placement="top" title="Setup Session Slot">
                                            <button id="setup_speaker_slot_btn" class="btn btn-edit d-flex align-items-center metrics_action" style="width:100%;gap: 8px;" data-metrics="create_session">
                                                <svg width="21" height="24" viewBox="0 0 21 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.77273 6V16C4.77273 19.3125 7.33807 22 10.5 22C13.6619 22 16.2273 19.3125 16.2273 16H11.4545C10.9295 16 10.5 15.55 10.5 15C10.5 14.45 10.9295 14 11.4545 14H16.2273V12H11.4545C10.9295 12 10.5 11.55 10.5 11C10.5 10.45 10.9295 10 11.4545 10H16.2273V8H11.4545C10.9295 8 10.5 7.55 10.5 7C10.5 6.45 10.9295 6 11.4545 6H16.2273C16.2273 2.6875 13.6619 0 10.5 0C7.33807 0 4.77273 2.6875 4.77273 6ZM18.1364 15V16C18.1364 20.4188 14.7179 24 10.5 24C6.2821 24 2.86364 20.4188 2.86364 16V13.5C2.86364 12.6687 2.22528 12 1.43182 12C0.638352 12 0 12.6687 0 13.5V16C0 21.5688 3.94943 26.1687 9.06818 26.9V29H6.20455C5.41108 29 4.77273 29.6688 4.77273 30.5C4.77273 31.3312 5.41108 32 6.20455 32H10.5H14.7955C15.5889 32 16.2273 31.3312 16.2273 30.5C16.2273 29.6688 15.5889 29 14.7955 29H11.9318V26.9C17.0506 26.1687 21 21.5688 21 16V13.5C21 12.6687 20.3616 12 19.5682 12C18.7747 12 18.1364 12.6687 18.1364 13.5V15Z" fill="#333333"/>
                                                </svg>
                                                Add Speaker
                                            </button>
                                        </div>


                                        <div id="setup_exhibitor_slot" style="display:none;margin:2px 0;" data-toggle="tooltip" data-placement="top" title="Setup Exhibitor Slot">
                                            <button id="setup_exhibitor_slot_btn" class="btn btn-edit d-flex align-items-center metrics_action" style="width:100%;gap: 8px;" data-metrics="create_exhibitor">
                                                <svg width="21" height="24" viewBox="0 0 21 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.50438 0.299394C10.1057 -0.099798 10.893 -0.099798 11.4943 0.299394L12.4675 0.939195C12.7955 1.15246 13.1782 1.25636 13.5663 1.23449L14.7308 1.1634C15.4524 1.11965 16.1303 1.51338 16.4529 2.15865L16.9777 3.20311C17.1526 3.55308 17.4369 3.83197 17.7813 4.00696L18.8365 4.53739C19.4816 4.86003 19.8752 5.53811 19.8314 6.25994L19.7604 7.4247C19.7385 7.81296 19.8424 8.20121 20.0556 8.52385L20.7007 9.49722C21.0998 10.0987 21.0998 10.8862 20.7007 11.4877L20.0556 12.4666C19.8424 12.7947 19.7385 13.1774 19.7604 13.5657L19.8314 14.7305C19.8752 15.4523 19.4816 16.1304 18.8365 16.453L17.7923 16.978C17.4424 17.153 17.1636 17.4373 16.9886 17.7818L16.4583 18.8372C16.1358 19.4825 15.4579 19.8762 14.7362 19.8325L13.5718 19.7614C13.1836 19.7395 12.7955 19.8434 12.4729 20.0567L11.4998 20.7019C10.8985 21.1011 10.1112 21.1011 9.50985 20.7019L8.53126 20.0567C8.20325 19.8434 7.82056 19.7395 7.43241 19.7614L6.26795 19.8325C5.54631 19.8762 4.86841 19.4825 4.54586 18.8372L4.02103 17.7928C3.84609 17.4428 3.56181 17.1639 3.21739 16.9889L2.16227 16.4585C1.51717 16.1358 1.12355 15.4578 1.16729 14.7359L1.23836 13.5712C1.26023 13.1829 1.15635 12.7947 0.943142 12.472L0.30351 11.4932C-0.0955773 10.8917 -0.0955773 10.1042 0.30351 9.50269L0.943142 8.52932C1.15635 8.20121 1.26023 7.81843 1.23836 7.43017L1.16729 6.2654C1.12355 5.54358 1.51717 4.8655 2.16227 4.54286L3.20646 4.0179C3.55634 3.83744 3.84062 3.55308 4.01557 3.20311L4.54039 2.15865C4.86294 1.51338 5.54084 1.11965 6.26248 1.1634L7.42694 1.23449C7.81509 1.25636 8.20325 1.15246 8.5258 0.939195L9.50438 0.299394ZM14.8729 10.4979C14.8729 9.33769 14.4121 8.22497 13.5919 7.40455C12.7717 6.58413 11.6593 6.12323 10.4994 6.12323C9.33942 6.12323 8.22699 6.58413 7.40679 7.40455C6.58659 8.22497 6.12581 9.33769 6.12581 10.4979C6.12581 11.6582 6.58659 12.7709 7.40679 13.5913C8.22699 14.4117 9.33942 14.8726 10.4994 14.8726C11.6593 14.8726 12.7717 14.4117 13.5919 13.5913C14.4121 12.7709 14.8729 11.6582 14.8729 10.4979ZM0.073898 24.158L2.43015 18.5529C2.44109 18.5583 2.44655 18.5638 2.45202 18.5747L2.97685 19.6192C3.61648 20.8879 4.94495 21.6589 6.36635 21.5769L7.53081 21.5058C7.54175 21.5058 7.55815 21.5058 7.56908 21.5167L8.5422 22.162C8.82101 22.3425 9.11623 22.4846 9.42237 22.5831L7.3668 27.4663C7.24106 27.7671 6.96225 27.9694 6.6397 27.9968C6.31715 28.0241 6.00553 27.8765 5.83059 27.603L4.07024 24.9071L1.00328 25.361C0.691663 25.4048 0.380047 25.279 0.183237 25.0329C-0.0135731 24.7868 -0.0518417 24.4478 0.0684311 24.158H0.073898ZM13.6319 27.4609L11.5764 22.5831C11.8825 22.4846 12.1777 22.3479 12.4565 22.162L13.4296 21.5167C13.4406 21.5113 13.4515 21.5058 13.4679 21.5058L14.6324 21.5769C16.0538 21.6589 17.3822 20.8879 18.0219 19.6192L18.5467 18.5747C18.5522 18.5638 18.5576 18.5583 18.5686 18.5529L20.9303 24.158C21.0506 24.4478 21.0068 24.7814 20.8155 25.0329C20.6241 25.2845 20.3071 25.4102 19.9954 25.361L16.9285 24.9071L15.1681 27.5976C14.9932 27.871 14.6816 28.0186 14.359 27.9913C14.0365 27.964 13.7577 27.7562 13.6319 27.4609Z" fill="black"/>
                                                </svg>
                                                Add Exhibitor
                                            </button>
                                        </div>
                                    </div>
                                     <div class="d-flex pt-1 resp-left-align" style="gap:8px; margin: 0 auto; align-items: center; width: 100%;">
                                        
                                            <?php if($live_state == 'before') { ?>
                                                <div class="dropdown calendar-dropdown">
                                                    
                                                    
                                                    <button title="Add to Calendar" style="padding:8px;" class="btn dropdown-toggle v1-calendar w-100" type="button" id="addToCalenderButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                                                <i class="fa-solid fa-calendar-check" style="font-size: 24px; margin: auto;"></i>
		                                            </button>
                                                    
                                                    <ul class="dropdown-menu w-100" aria-labelledby="addToCalenderButton">
                                                        <?php
                                                        $event_start_date = $event_arr['local_start_at'];
                                                        $event_end_date = $event_arr['local_end_at'];

                                                        $calendar_start_time = date("H:i", strtotime($event_arr['local_start_at']));
                                                        $calendar_end_time = date("H:i", strtotime($event_arr['local_end_at']));

                                                        $calendarDate = date('Ymd',strtotime($event_start_date)) .'T'. date('Hi',strtotime($calendar_start_time)). '00/' . date('Ymd',strtotime($event_end_date)) .'T'. date('Hi',strtotime($calendar_end_time)) . '00';
                                                        $start_outlook = date('Y-m-d',strtotime($event_start_date)) .'T'. date('H:i:s',strtotime($calendar_start_time)). '' ;
                                                        $end_outlook = date('Y-m-d',strtotime($event_end_date)) .'T'. date('H:i:s',strtotime($calendar_end_time)). '';
                                                        $eventDetails = "To RSVP and see the complete details click here : \r\n".  TAOH_SITE_URL_ROOT . '/events/d/' . taoh_slugify($events_data['title']) . '-' . $event_arr['eventtoken'];
                                                        $eventLocation = $events_data['full_location'] ?? '';
                                                        ?>
                                                        <li class="dropdown-item"><a target="_blank" href="<?php echo 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='.str_replace("#","",$event_title).'&dates='.$calendarDate.'&details='.$eventDetails.'&location='.$eventLocation.'&sf=true&output=xml'; ?>">Google Calendar</a></li>

                                                        <li class="dropdown-item"><a target="_blank" href="<?php
                                                        $eventLink = TAOH_SITE_URL_ROOT . '/events/d/' . taoh_slugify($events_data['title']) . '-' . $event_arr['eventtoken'];
                                                        $outlookBodyHtml = '<p>To RSVP and see the complete details click here:</p><p><a href="' . $eventLink . '">' . $eventLink . '</a></p>';
                                                        echo 'https://outlook.live.com/calendar/0/deeplink/compose?subject='.urlencode(str_replace("#","",$event_title)).'&body='.urlencode($outlookBodyHtml).'&startdt='.$start_outlook.'&enddt='.$end_outlook.'&location='.urlencode($eventLocation).'&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent'; ?>">Outlook Calendar</a></li>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        <div class="edit-rsvp">

                                            <a title="Edit Rsvp" class="btn d-flex align-items-center text-nowrap" style="padding:8px"
                                            href="<?php echo TAOH_SITE_URL_ROOT . '/events/edit_rsvp/' . $event_arr['eventtoken'] . '/' . $current_ticket_type['title']; ?>" >
                                                <i class="fa-solid fa-edit" style="font-size: 24px; margin: auto;"></i>
                                            </a>
                                        </div>
                                        <div id="share_event_slot" style="">
                                            
                                            <a title="Share Event" data-toggle="modal" data-target="#shareModal" href="javascript:void(0);"
                                            class="btn d-flex align-items-center text-nowrap" style="padding:8px" >
                                                <i class="fa-solid fa-share-nodes" style="font-size: 24px; margin: auto;"></i>
                                            </a>
                                        </div>

                                    </div>
                                    

                                   

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="event_start_at" id="event_start_at" value="">
            <input type="hidden" name="event_end_at" id="event_end_at" value="">
            <input type="hidden" name="event_timezone" id="event_timezone" value="">
            <input type="hidden" name="event_locality" id="event_locality" value="">

            <!-- main elements -->
            <div class="main">
            <div class="row">
                <div class="col-xl-12">

                    <!-- hall tabs -->
                    <div class="events-hall container py-5 px-0" style="display:block;border-bottom: 2px solid #d3d3d3;">
                        <?php require_once('events_lobby_hall.php'); ?>
                    </div>
                    <!-- Count-down -->
                    <div class="count-down py-5" hidden>
                        <h5 class="text-center mb-5 count-heading">Your Event starts in</h5>
                        <div class="row count-row mx-0 d-flex justify-content-center" style="gap: 0.5rem;">
                            <div class="col-3 count-col mr-lg-3">
                                <div>
                                    <div class="count cound-days mb-2"></div>
                                    <div class="count-label">Days</div>
                                </div>
                            </div>
                            <div class="col-3 count-col mr-lg-3">
                                <div>
                                    <div class="count cound-hours mb-2"></div>
                                    <div class="count-label">Hours</div>
                                </div>
                            </div>
                            <div class="col-3 count-col mr-lg-3">
                                <div>
                                    <div class="count cound-min mb-2"></div>
                                    <div class="count-label">Minutes</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="event_lobby_content" class="controlled-size">

                    </div>
                </div>

                <div class="col-xl-4">
                    <!-- Sponsor Widget -->
                    <div class="mt-4 pb-3 sticky-top" style="top: 100px;z-index: 0;">
                        <div class="sponsor-card mb-4 mx-auto" id="sponsor_card1" style="display: none;">
                            <div class="bb mx-3 mt-3 pb-3 d-flex justify-content-between" style="gap: 6px;">
                                <h3 class="sponsor-card-title">
                                    <svg width="15" height="20" viewBox="0 0 15 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.78884 0.213853C7.21839 -0.0712843 7.7807 -0.0712843 8.21025 0.213853L8.90533 0.670854C9.13963 0.823188 9.41298 0.897401 9.69023 0.881777L10.522 0.830999C11.0374 0.799752 11.5217 1.08098 11.752 1.54189L12.1269 2.28793C12.2519 2.53792 12.4549 2.73712 12.701 2.86211L13.4546 3.241C13.9154 3.47145 14.1966 3.95579 14.1653 4.47138L14.1145 5.30336C14.0989 5.58068 14.1731 5.85801 14.3254 6.08846L14.7862 6.78373C15.0713 7.21339 15.0713 7.77585 14.7862 8.20551L14.3254 8.90468C14.1731 9.13904 14.0989 9.41246 14.1145 9.68979L14.1653 10.5218C14.1966 11.0374 13.9154 11.5217 13.4546 11.7521L12.7088 12.1271C12.4588 12.2521 12.2597 12.4552 12.1347 12.7013L11.756 13.4552C11.5256 13.9161 11.0413 14.1973 10.5259 14.1661L9.69413 14.1153C9.41688 14.0996 9.13963 14.1739 8.90924 14.3262L8.21415 14.7871C7.78461 15.0722 7.22229 15.0722 6.79275 14.7871L6.09376 14.3262C5.85946 14.1739 5.58611 14.0996 5.30886 14.1153L4.47711 14.1661C3.96165 14.1973 3.47744 13.9161 3.24704 13.4552L2.87217 12.7091C2.74721 12.4591 2.54415 12.2599 2.29814 12.1349L1.54448 11.7561C1.08369 11.5256 0.802537 11.0413 0.833776 10.5257L0.884541 9.69369C0.900161 9.41637 0.825967 9.13904 0.673673 8.90859L0.216793 8.20942C-0.0682695 7.77976 -0.0682695 7.21729 0.216793 6.78764L0.673673 6.09237C0.825967 5.85801 0.900161 5.58459 0.884541 5.30726L0.833776 4.47529C0.802537 3.9597 1.08369 3.47535 1.54448 3.2449L2.29033 2.86993C2.54024 2.74103 2.7433 2.53792 2.86826 2.28793L3.24314 1.54189C3.47353 1.08098 3.95775 0.799752 4.4732 0.830999L5.30496 0.881777C5.58221 0.897401 5.85946 0.823188 6.08985 0.670854L6.78884 0.213853ZM10.6235 7.49853C10.6235 6.66978 10.2944 5.87498 9.70852 5.28896C9.12267 4.70295 8.32807 4.37373 7.49955 4.37373C6.67102 4.37373 5.87642 4.70295 5.29057 5.28896C4.70471 5.87498 4.37558 6.66978 4.37558 7.49853C4.37558 8.32727 4.70471 9.12207 5.29057 9.70809C5.87642 10.2941 6.67102 10.6233 7.49955 10.6233C8.32807 10.6233 9.12267 10.2941 9.70852 9.70809C10.2944 9.12207 10.6235 8.32727 10.6235 7.49853ZM0.0527843 17.2557L1.73582 13.252C1.74363 13.256 1.74754 13.2599 1.75144 13.2677L2.12632 14.0137C2.5832 14.9199 3.5321 15.4707 4.54739 15.4121L5.37915 15.3613C5.38696 15.3613 5.39868 15.3613 5.40649 15.3691L6.10157 15.83C6.30072 15.9589 6.51159 16.0605 6.73027 16.1308L5.262 19.6188C5.17219 19.8336 4.97304 19.9782 4.74264 19.9977C4.51225 20.0172 4.28967 19.9118 4.16471 19.7165L2.90731 17.7908L0.716628 18.115C0.494045 18.1463 0.271462 18.0564 0.130884 17.8806C-0.00969507 17.7049 -0.0370298 17.4627 0.0488793 17.2557H0.0527843ZM9.73709 19.6149L8.26882 16.1308C8.4875 16.0605 8.69837 15.9628 8.89752 15.83L9.5926 15.3691C9.60041 15.3652 9.60822 15.3613 9.61994 15.3613L10.4517 15.4121C11.467 15.4707 12.4159 14.9199 12.8728 14.0137L13.2476 13.2677C13.2516 13.2599 13.2555 13.256 13.2633 13.252L14.9502 17.2557C15.0361 17.4627 15.0049 17.701 14.8682 17.8806C14.7315 18.0603 14.505 18.1502 14.2825 18.115L12.0918 17.7908L10.8344 19.7126C10.7094 19.9079 10.4868 20.0133 10.2564 19.9938C10.0261 19.9743 9.8269 19.8258 9.73709 19.6149Z" fill="#2A4E96"/>
                                    </svg>
                                    <span>Sponsors</span>
                                </h3>
                                <input id="sponsor_type1" name="sponsor_type1" type="hidden" value=""/>
                                <div class="sponsor_edit"></div>
                                <div class="event_sponsor_right_header">
                                <a href="<?php echo  TAOH_SITE_URL_ROOT.'/events/event_sponsor/'.$eventtoken;?>" 
                                class="btn btn-warning sponsor-btn">Become a sponsor</a>

                                </div>
                            </div>

                            <div class="sponsor-content-carousel" id="sponsor_content_carousel"></div>

                            <div class="d-flex justify-content-end p-3">
                                <button type="button" class="btn btn-sm btn-primary get-started" data-toggle="modal" data-target="#sponsorInfo">More Info</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div style="padding-bottom: 10px;">
                <div class="mt-4 mb-5 py-3 py-md-5 px-3 px-md-5 contact-host d-flex align-items-center justify-content-between flex-wrap" style="gap: 1rem;">
                    <div class="d-flex align-items-center" style="gap: 1rem;">
                        <div class="d-flex align-items-center justify-content-center contact-svg mr-lg-2">
                            <svg width="32" height="28" viewBox="0 0 32 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 3C8.81875 3 3 8.81875 3 16V22.5C3 23.3312 2.33125 24 1.5 24C0.66875 24 0 23.3312 0 22.5V16C0 7.1625 7.1625 0 16 0C24.8375 0 32 7.1625 32 16V22.5C32 23.3312 31.3312 24 30.5 24C29.6688 24 29 23.3312 29 22.5V16C29 8.81875 23.1812 3 16 3ZM5 20C5 17.7938 6.79375 16 9 16H10C11.1062 16 12 16.8937 12 18V26C12 27.1063 11.1062 28 10 28H9C6.79375 28 5 26.2062 5 24V20ZM23 16C25.2062 16 27 17.7938 27 20V24C27 26.2062 25.2062 28 23 28H22C20.8937 28 20 27.1063 20 26V18C20 16.8937 20.8937 16 22 16H23Z" fill="#fff"/>
                            </svg>
                        </div>
                        <p class="guide-text">Need Help ! Having Questions ? Please Contact Host</p>
                    </div>
                    <div id="contact_host_btn_blk"></div>
                </div>
            </div>

        </div>

    </div>
    </div>
</div>

<div class="modal fade dark-head-v1-modal" id="addCoAttendessModal" tabindex="-1" role="dialog" aria-labelledby="addCoAttendessModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    
      <div class="modal-header p-4 px-lg-5">
        <div class="modal-title">
            <div class="d-flex align-items-center" style="gap: 21px;">
                <svg class="d-none d-sm-block" width="59" height="59" viewBox="0 0 59 59" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="29.5" cy="29.5" r="28.5" fill="black" stroke="#D3D3D3" stroke-width="2"/>
                    <path d="M18.65 23.25C18.65 21.5924 19.3032 20.0027 20.4659 18.8306C21.6287 17.6585 23.2057 17 24.85 17C26.4943 17 28.0713 17.6585 29.2341 18.8306C30.3968 20.0027 31.05 21.5924 31.05 23.25C31.05 24.9076 30.3968 26.4973 29.2341 27.6694C28.0713 28.8415 26.4943 29.5 24.85 29.5C23.2057 29.5 21.6287 28.8415 20.4659 27.6694C19.3032 26.4973 18.65 24.9076 18.65 23.25ZM14 40.5498C14 35.7402 17.8653 31.8438 22.6364 31.8438H27.0636C31.8347 31.8438 35.7 35.7402 35.7 40.5498C35.7 41.3506 35.0558 42 34.2614 42H15.4386C14.6442 42 14 41.3506 14 40.5498ZM38.4125 32.2344V29.1094H35.3125C34.6683 29.1094 34.15 28.5869 34.15 27.9375C34.15 27.2881 34.6683 26.7656 35.3125 26.7656H38.4125V23.6406C38.4125 22.9912 38.9308 22.4688 39.575 22.4688C40.2192 22.4688 40.7375 22.9912 40.7375 23.6406V26.7656H43.8375C44.4817 26.7656 45 27.2881 45 27.9375C45 28.5869 44.4817 29.1094 43.8375 29.1094H40.7375V32.2344C40.7375 32.8838 40.2192 33.4062 39.575 33.4062C38.9308 33.4062 38.4125 32.8838 38.4125 32.2344Z" fill="white"/>
                </svg>
                <div>
                    <h5>Add Co-attendees</h5>
                    <p>Your sponsorship allows you to bring 3 Co-Attendees</p>
                </div>
            </div>
        </div>
        <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.6449 2.04935C12.1134 1.58082 12.1134 0.819928 11.6449 0.351398C11.1763 -0.117133 10.4154 -0.117133 9.9469 0.351398L6 4.30205L2.04935 0.355146C1.58082 -0.113384 0.819928 -0.113384 0.351398 0.355146C-0.117133 0.823676 -0.117133 1.58457 0.351398 2.0531L4.30205 6L0.355146 9.95065C-0.113384 10.4192 -0.113384 11.1801 0.355146 11.6486C0.823676 12.1171 1.58457 12.1171 2.0531 11.6486L6 7.69795L9.95065 11.6449C10.4192 12.1134 11.1801 12.1134 11.6486 11.6449C12.1171 11.1763 12.1171 10.4154 11.6486 9.9469L7.69795 6L11.6449 2.04935Z" fill="#ffffff"/>
            </svg>
        </button>
      </div>
      
      <div class="modal-body px-4 px-lg-5">
        <!-- Form or content goes here -->
        <form>
            <h6 class="mb-3 pb-1">Co-Attendee 1</h6>
            <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                <div class="form-group col-lg-6">
                    <label for="">First Name*</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Last Name *</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-12">
                    <label for="">Email *</label>
                    <input type="email" class="form-control">
                </div>
            </div>

            <h6 class="mb-3 pb-1">Co-Attendee 2</h6>
            <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                <div class="form-group col-lg-6">
                    <label for="">First Name*</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Last Name *</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-12">
                    <label for="">Email *</label>
                    <input type="email" class="form-control">
                </div>
            </div>

            <h6 class="mb-3 pb-1">Co-Attendee 3</h6>
            <div class="row mx-0 border pt-3 mb-4 px-lg-3">
                <div class="form-group col-lg-6">
                    <label for="">First Name*</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-6">
                    <label for="">Last Name *</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group col-lg-12">
                    <label for="">Email *</label>
                    <input type="email" class="form-control">
                </div>
            </div>


            <button type="button" class="btn v1-dark-btn-lg mb-5">Add Co-attendees</button>
        </form>
      </div>

    </div>
  </div>
</div>


<?php
require_once TAOH_APP_PATH . '/events/event_video_modal.php';

if ($show_rsvp_ticket) {
    ?>
    <!-- RSVP Ticket Modal -->
    <div class="modal fade" id="rsvpTicketModal" tabindex="-1" role="dialog" aria-labelledby="rsvpTicketModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rsvpTicketModalTitle">Event Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer justify-content-center">

                </div>
            </div>
        </div>
    </div>
    <?php
}


if (!$valid_user) {
    ?>
    <!-- Complete Settings -->
    <div class="modal fade" id="completeSettingsModal" tabindex="-1" role="dialog" aria-labelledby="completeSettingsModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeSettingsModalTitle">Complete settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center">You cannot attend the event without complete settings; please use the opportunity now to complete your settings.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button class="btn theme-btn-primary complete_settings_now">Complete Settings Now</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

?>

<div class="modal fade" id="contacthostModal" tabindex="-1" role="dialog" aria-labelledby="contacthostModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="contacthostModalLabel">Contact Host</h5>
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
          X
        </button>
      </div>
      <div class="modal-body h-auto">
        <form name="contacthostForm" id="contacthostForm" enctype="multipart/form-data" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="title" id="title" placeholder="Title" required>
            </div>
            <div class="form-group">
            <textarea class="form-control" name="description" id="description"  rows="4" placeholder="Describe here..." required></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" id="contacthostSubmit" class="btn btn-primary"><i></i>Submit Report</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade sponsorship-option" id="sponsorInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>


<?php
// Session Slot Modal
require_once('events_session_form.php');

// Exhibitor Slot Modal
require_once('events_exhibitor_form_new.php');
?>

<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/event.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script> 
    <script type="application/javascript">
        const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
        const isValidUser = <?= json_encode($valid_user); ?>;
        const profileType = <?= json_encode($profile_type); ?>;
        const is_sponsor_enable = <?= json_encode($is_sponsor_enable); ?>;
        const is_exhibitor_enable = <?= json_encode($is_exhibitor_enable); ?>;
        const is_speaker_enable = <?= json_encode($is_speaker_enable); ?>;
        const is_hall_enable    = <?= json_encode($is_hall_enable); ?>;
        const user_profile_type = '<?= $user_info_obj->type ?? ''; ?>';
        const is_rsvp = 0;
        const dojoeventrules = <?php echo json_encode(DOJO_EVENT_DETAIL_MESSAGE); ?>; 
        const my_pToken = '<?= $ptoken ?? ''; ?>';
        const my_local_timezone = '<?= $my_local_timezone ?? ''; ?>';
        const my_email = '<?= $user_info_obj->email ?? ''; ?>';
        let eventToken = '<?= $eventtoken ?? ''; ?>';

        let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
        let show_rsvp_ticket = <?= json_encode($show_rsvp_ticket); ?>;
        let show_upgrade = <?= json_encode($show_upgrade); ?>;
        let rsvp_ticket_token = '<?= $rsvp_ticket_token ?? ''; ?>';
        let click_view = '<?= $click_view ?? 'view'; ?>';
        let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';
        let rsvp_slug = '<?= $rsvp_slug ?? ''; ?>';
        let rsvp_token = '<?= $rsvp_token ?? ''; ?>';
        let is_event_freeze = '<?= $is_event_freeze ?? ''; ?>';
        let live_state = '<?= $live_state ?? ''; ?>';
        let event_arr = <?= json_encode($event_arr); ?>;
        let ref_slug = '<?= $ref_slug ?? ''; ?>';
        let success_discount_amt = '<?= $success_discount_amt ?? '';?>';
        let trackingtoken = '<?= $trackingtoken ?? '';?>';

        let event_organizer_banners = JSON.parse(`<?= json_encode(($event_organizer_banner ?? [])); ?>`);

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1000000)
        }, 'File size must be less than {0} MB');

        $(document).ready(function () {
            if (isLoggedIn && !profileType && typeof showBasicSettingsModal === 'function') {
                showBasicSettingsModal(true);
            }

            if (isLoggedIn && typeof save_metrics === 'function') {
                save_metrics('events_lobby', click_view, eventToken);
            }

            async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
                if (!pToken_to?.trim()) return null;

                let userInfo = {};

                if (!serverFetch) {
                    // Try to get userInfo from IndexedDB
                    if (!userInfo.ptoken) {
                        const user_info_key = 'user_info_list';
                        const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                        if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                            let userInfoObj = intao_data.values[ops][pToken_to];
                            // Check if data is expired (expires after 2 day)
                            if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                                userInfo = userInfoObj;
                                $("#user_profile_type").val(userInfo.type);
                            }
                        }
                    }
                }

                // Fetch userInfo from server if not found locally
                if (!userInfo.ptoken) {
                    const formData = {
                        taoh_action: 'taoh_user_info',
                        ops: ops,
                        ptoken: pToken_to
                    };

                    try {
                        const srv_userInfoObj = await fetchUserInfoFromServer(formData);
                        srv_userInfoObj.last_fetch_time = Date.now();
                        userInfo = srv_userInfoObj;
                    } catch (e) {
                        console.log('getUserInfo error:', e);
                    }
                }

                // If userInfo not found, set default values
                if (!userInfo.ptoken) {
                    userInfo = {
                        ptoken: pToken_to,
                        chat_name: 'Unknown Name',
                        avatar: 'default',
                        full_location: 'Unknown Location',
                        type: 'Unknown Type',
                        is_unknown: true,
                        last_fetch_time: Date.now()
                    };
                }

                return userInfo;
            }

            function hasVisibleText(html) {
		if (!html) return false;

		// Decode HTML entities
		const decodedHtml = $('<div>').html(html).text();

		// Create an element to parse HTML content
		const $el = $('<div>').html(decodedHtml);

		// Remove common "empty" elements like <br>, <p><br></p>, etc.
		$el.find('br, p:empty, p:has(br)').remove();

		// Get remaining visible text
		const visibleText = $el.text().replace(/\s|&nbsp;/g, '');

		return visibleText.length > 0;
	}

            function generateAdditionalInfoTabsHtml(additionalInfo) {
                const keys = ['1', '2', '3'];
                let hasTabs = false;
                let isFirst = true;
                let tabsHtml = '';
                let contentHtml = '';

                keys.forEach(key => {
                    const titleKey = `ad_info_title_${key}`;
                    const contentKey = `ad_info_content_${key}`;

                    if (additionalInfo[titleKey] && additionalInfo[contentKey]) {
                        hasTabs = true;

                        // Create Tab HTML
                        tabsHtml += `<li class="nav-item col-md">
                            <a class="nav-link ${isFirst ? 'active' : ''}" id="ad_info_${key}_tab"
                                data-toggle="tab" href="#ad_info_${key}" role="tab"
                                aria-controls="ad_info_${key}" aria-selected="${isFirst}">
                                ${taoh_title_desc_decode(additionalInfo[titleKey])}</a>
                        </li>`;

                        // Create Tab Content HTML
                        contentHtml += `<div class="tab-pane fade show ${isFirst ? 'active' : ''}" id="ad_info_${key}" role="tabpanel" aria-labelledby="ad_info_${key}_tab">
                                <p>${taoh_desc_decode(additionalInfo[contentKey])}</p>
                            </div>`;

                        isFirst = false;
                    }
                });

                if (!hasTabs) return '';

                return `<div class="mt-5 pb-4 controlled-size">
                    <ul class="nav nav-tabs row d-flex flex-wrap scroll-container" id="descriptionTabs" role="tablist" style="border-bottom: 1px solid #d3d3d3; gap: 1rem; overflow-x: auto;">
                        ${tabsHtml}
                    </ul>
                    <div class="tab-content mt-4" id="descriptionTabsContent">
                        ${contentHtml}
                    </div>
                </div>`;
            }

            async function processEventBaseInfo(requestData, response) {
                let event_output = response.output;
                let event_owner = event_output.ptoken;
                let conttoken_data = event_output.conttoken;
                const enable_hall = Number(conttoken_data?.enable_hall) === 1;
                var event_country_name = '';

                /* Event Sponsor */
                await getEventSponsor(event_output.eventtoken);
                /* Event Sponsor */
                
                if (conttoken_data.full_location != '' && conttoken_data.full_location != undefined) {
                    event_country = conttoken_data.full_location;
                    evet_country_array = event_country.split(',');
                    event_country_name = evet_country_array[evet_country_array.length - 1].trim();
                }

                if(conttoken_data.country_locked !='' && conttoken_data.country_locked != undefined)
                    var country_locked = conttoken_data.country_locked;
                else
                    var country_locked = 0;

                $('#event_country_lock').val(country_locked);
                $('#event_country_name').val(event_country_name);

                if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) {
                    org_email = $.trim(conttoken_data.org_email);
                    $("#exhibitor_contactus").attr('href', "mailto:" + org_email + "");
                    $("#speaker_contactus").attr('href', "mailto:" + org_email + "");
                    $("#agenda_contactus").attr('href',"mailto:"+org_email+"");
                }

                $("#enable_exhibitor_hall").val(conttoken_data.enable_exhibitor_hall);
                $("#enable_speaker_hall").val(conttoken_data.enable_speaker_hall);
                $("#enable_hall").val(conttoken_data.enable_hall);

                if(enable_hall) {
                    $('#exhibitor_top').show();
                    $('#speaker_top').show();
                } else {
                    $('#exhibitor_top').remove();
                    $('#exhibitor_desc').remove();
                    $('#speaker_top').remove();
                    $('#speaker_desc').remove();
                }

                let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                    .split(',')
                    .map(token => token.trim())
                    .filter(token => token);

                if(event_owner) event_organizer_ptokens.push(event_owner);
                
                let event_instance_owner = conttoken_data.ptoken;
                event_organizer_ptokens.push(event_instance_owner);

                var superorganizer_token = $('#superorganizer_token').val();
                event_organizer_ptokens.push(superorganizer_token);//deeksha

                if (event_organizer_ptokens.includes(my_pToken)) {
                    $('#download_rsvp').show();
                    $('#email_rsvp').show();
                } else {
                    $('#download_rsvp').hide();
                    $('#email_rsvp').hide();
                }

                if (event_organizer_ptokens.includes(my_pToken)) {
                    $("#is_organizer").val(1);
                } else {
                    $("#is_organizer").val(0);
                }

                const event_type = (conttoken_data?.event_type || 'virtual').toLowerCase();
                const is_event_suspended = parseInt(event_output?.status) === 2;
                const is_event_freeze = parseInt(conttoken_data?.freeze_option) === 1;

                $('.event_title').text(conttoken_data?.title);

                /* Event Banner */
                const allEventBannersArray = [conttoken_data.event_video, conttoken_data.event_image].concat((conttoken_data.more_banner || []));
                const eventBannersArray = allEventBannersArray.filter(url => url.trim() !== "" && isValidURL(url)).map(url => ({
                    src: url,
                    type: getMediaType(url)
                }));

                const galleryContainer = document.getElementById("event_banner_container");
                const mainDisplay = document.createElement("div");
                mainDisplay.id = "event_banner_image";
                galleryContainer.before(mainDisplay);

                function formatVideoSrc(videoSrc) {
                    if (videoSrc.includes("youtube.com")) {
                        return `https://www.youtube.com/embed/${videoSrc.split("v=")[1]?.split("&")[0]}`;
                    }
                    if (videoSrc.includes("youtu.be")) {
                        const videoId = videoSrc.split("youtu.be/")[1];
                        return `https://www.youtube.com/embed/${videoId}`;
                    }
                    if (videoSrc.includes("vimeo.com")) {
                        return `https://player.vimeo.com/video/${videoSrc.split("vimeo.com/")[1]}`;
                    }
                    return videoSrc; // For other video formats
                }

                function displayMedia(media) {
                    if (!media) return;

                    mainDisplay.innerHTML = "";
                    let mediaHtml = "";

                    if (media.type === "image") {
                        mediaHtml = `
                            <div class="cover-event-image">
                                <div class="events-bg" style="background-image: url('${media.src}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${media.src}" class="main-image" alt="Event" style="max-width: 173px; min-width: 173px; max-height: 136px; min-height: 136px; border: 1px solid #d3d3d3;">
                            </div>
                            
                        `;
                    } else if (media.type === "video") {
                        let videoSrc = formatVideoSrc(media.src);
                        mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay" style="max-width: 173px; min-width: 173px; max-height: 136px; min-height: 136px; border: 0;"></iframe>`;
                    }

                    mainDisplay.innerHTML = mediaHtml;
                }

                // Generate the gallery items
                eventBannersArray.forEach((media, index) => {
                    let itemHtml = "";

                    if (media.type === "image") {
                        itemHtml = `<div class="item" style="--background-src: url('${media.src}');"><img src="${media.src}" class="thumbnail" data-index="${index}" alt="Gallery Image ${index + 1}"></div>`;
                    } else if (media.type === "video") {
                        let thumbnailSrc = "";
                        if (media.src.includes("youtube.com") || media.src.includes("youtu.be")) {
                            thumbnailSrc = getYouTubeThumbnail(media.src);
                        } else if (media.src.includes("vimeo.com")) {
                            getVimeoThumbnail(media.src, (thumbnail) => {
                                document.querySelector(`[data-index='${index}']`).src = thumbnail;
                            });
                            thumbnailSrc = "https://via.placeholder.com/150/FF0000/FFFFFF?text=Vimeo";
                        } else {
                            thumbnailSrc = "https://via.placeholder.com/150/0000FF/FFFFFF?text=Video";
                        }

                        itemHtml = `<div class="item" style="--background-src: url('${media.src}');"><img src="${thumbnailSrc}" class="thumbnail" data-index="${index}" alt="Gallery Video ${index + 1}"><div>`;
                    }

                    galleryContainer.innerHTML += itemHtml;
                });

                if (eventBannersArray[0]) {
                    displayMedia(eventBannersArray[0]);
                } else {
                    const noImage = _taoh_site_url_root + '/assets/images/event.jpg';
                    const mediaHtml = `
                            <div class="cover-event-image">
                                <div class="events-bg" style="background-image: url('${noImage}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${noImage}" class="main-image" alt="Event" style="max-width: 173px; min-width: 173px; max-height: 136px; min-height: 136px; border: 1px solid #d3d3d3;">
                            </div>
                            `;
                    $('#event_banner_image').html(mediaHtml);
                }


                // Handle clicking on thumbnails
                galleryContainer.addEventListener("click", (event) => {
                    const target = event.target.closest(".thumbnail");
                    if (target) {
                        const mediaIndex = parseInt(target.getAttribute("data-index"));
                        displayMedia(eventBannersArray[mediaIndex]);
                    }
                });

                if (eventBannersArray.length > 1) {
                    galleryContainer.style.display = "flex";
                }

                /* /Event Banner */


                $('#event_like_btn').html(`<img src="${_taoh_site_url_root + '/assets/images/bookmark.svg'}" alt="bookmark" data-event="${event_output.eventtoken}" data-cont="${conttoken_data.conttoken}" class="event_save" title="Save Event" style="width: 18px">`);

                // Event Description
                let eventDescriptionHtml = '';
                if (conttoken_data.description && $.trim(conttoken_data.description) != '') {
                    eventDescriptionHtml += '<h3>About this Event</h3>';
                    eventDescriptionHtml += `<div>${taoh_desc_decode(conttoken_data.description)}</div>`;
                }

                /* Event Lobby Content */
                let eventLobbyContentHtml = '';

                if (is_event_freeze) {
                    eventDescriptionHtml += `<div class="freeze-event mt-4">
                        <div class="freeze-event-inner">
                            <div class="freeze-event-inner-content">
                                <h5 class="org-title">Event is suspended</h5>
                                <div class="mt-4 mb-5">${taoh_desc_decode(conttoken_data.freeze_note)}</div>
                            </div>
                        </div>
                    </div>`;
                } else {
                    if (conttoken_data.message_content && $.trim(conttoken_data.message_content) != '') {
                        // Lobby Content
                        eventDescriptionHtml += `<div class="mt-5">
                            <div class="mt-4">${taoh_desc_decode(conttoken_data.message_content)}</div>
                        </div>`;
                    }

                    // Message from owner
                    if (conttoken_data.msg_from_owner && $.trim(conttoken_data.msg_from_owner) != '' && hasVisibleText(conttoken_data.msg_from_owner)) {
                        // Organizer message

                        let organizerBannerLink = '';
                        if (Array.isArray(event_organizer_banners)) {
                            if (event_organizer_banners.length > 0 && event_organizer_banners[0].organizer_banner_link) {
                                organizerBannerLink = event_organizer_banners[0].organizer_banner_link;
                            }
                        } else if (event_organizer_banners && event_organizer_banners.organizer_banner_link) {
                            organizerBannerLink = event_organizer_banners.organizer_banner_link;
                        }

                        eventDescriptionHtml += `<div class="mt-5 controlled-size org-msg-wrapper">
                            ${isValidUrl(organizerBannerLink) ? `<div class="org-banner mb-4"><div class="cover-event-image">
                                <div class="events-bg" style="background-image: url('${organizerBannerLink}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${organizerBannerLink}" class="detail-main-image" alt="Event Organizer Banner">
                            </div></div>` : ''}

                            <div class="d-flex" style="gap: 12px;">
                                <div class="org-svg">
                                    <svg class="dark-svg" width="28" height="32" viewBox="0 0 28 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 8C6 10.1217 6.84285 12.1566 8.34315 13.6569C9.84344 15.1571 11.8783 16 14 16C16.1217 16 18.1566 15.1571 19.6569 13.6569C21.1571 12.1566 22 10.1217 22 8C22 5.87827 21.1571 3.84344 19.6569 2.34315C18.1566 0.842855 16.1217 0 14 0C11.8783 0 9.84344 0.842855 8.34315 2.34315C6.84285 3.84344 6 5.87827 6 8ZM11.9062 20.5125L13.0688 22.45L10.9875 30.1938L8.7375 21.0125C8.6125 20.5063 8.125 20.175 7.61875 20.3062C3.24375 21.4 0 25.3625 0 30.0812C0 31.1437 0.8625 32 1.91875 32H10.1562C10.1562 32 10.1562 32 10.1625 32H10.5H17.5H17.8438C17.8438 32 17.8438 32 17.85 32H26.0812C27.1437 32 28 31.1375 28 30.0812C28 25.3625 24.7563 21.4 20.3813 20.3062C19.875 20.1812 19.3875 20.5125 19.2625 21.0125L17.0125 30.1938L14.9312 22.45L16.0938 20.5125C16.4937 19.8438 16.0125 19 15.2375 19H14H12.7688C11.9938 19 11.5125 19.85 11.9125 20.5125H11.9062Z" fill="#2557A7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="org-sub-title">The Organizer's Message</p>
                                    <h5 class="org-title">Message From the Host !</h5>
                                </div>
                            </div>
                            <p class="org-desc mt-3">${taoh_desc_decode($.trim(conttoken_data.msg_from_owner))}</p>
                        </div>`;
                    }

                    // Additonal Information
                    eventDescriptionHtml += generateAdditionalInfoTabsHtml(conttoken_data);

                    // About the Host
                    if (conttoken_data.about_you && $.trim(conttoken_data.about_you) != '') {
                        // About the Host
                        eventDescriptionHtml += `<div class="mt-5 controlled-size">
                            <div class="d-flex justify-content-between flex-wrap pt-5" style="gap: 1rem;border-top: 2px solid #d3d3d3;">
                                <div class="d-flex" style="gap: 12px;">
                                    <div class="org-svg">
                                        <svg class="dark-svg" width="28" height="32" viewBox="0 0 28 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 8C6 10.1217 6.84285 12.1566 8.34315 13.6569C9.84344 15.1571 11.8783 16 14 16C16.1217 16 18.1566 15.1571 19.6569 13.6569C21.1571 12.1566 22 10.1217 22 8C22 5.87827 21.1571 3.84344 19.6569 2.34315C18.1566 0.842855 16.1217 0 14 0C11.8783 0 9.84344 0.842855 8.34315 2.34315C6.84285 3.84344 6 5.87827 6 8ZM11.9062 20.5125L13.0688 22.45L10.9875 30.1938L8.7375 21.0125C8.6125 20.5063 8.125 20.175 7.61875 20.3062C3.24375 21.4 0 25.3625 0 30.0812C0 31.1437 0.8625 32 1.91875 32H10.1562C10.1562 32 10.1562 32 10.1625 32H10.5H17.5H17.8438C17.8438 32 17.8438 32 17.85 32H26.0812C27.1437 32 28 31.1375 28 30.0812C28 25.3625 24.7563 21.4 20.3813 20.3062C19.875 20.1812 19.3875 20.5125 19.2625 21.0125L17.0125 30.1938L14.9312 22.45L16.0938 20.5125C16.4937 19.8438 16.0125 19 15.2375 19H14H12.7688C11.9938 19 11.5125 19.85 11.9125 20.5125H11.9062Z" fill="#2557A7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="org-sub-title">The Organizer</p>
                                        <h5 class="org-title">About the Host</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="pb-4">
                                <p class="org-desc mt-3">${taoh_desc_decode(conttoken_data.about_you)}</p>
                            </div>
                        </div>`;
                    }

                    // Event FAQ Section
                    if (conttoken_data?.event_faq && (conttoken_data.event_faq).length) {
                        // FAQ Header Section
                        eventDescriptionHtml += `
                            <div class="d-flex mt-4" style="gap: 12px;">
                                <div class="spon-svg">
                                    <svg class="dark-svg" width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.5 29C18.3456 29 22.0338 27.4723 24.753 24.753C27.4723 22.0338 29 18.3456 29 14.5C29 10.6544 27.4723 6.96623 24.753 4.24695C22.0338 1.52767 18.3456 0 14.5 0C10.6544 0 6.96623 1.52767 4.24695 4.24695C1.52767 6.96623 0 10.6544 0 14.5C0 18.3456 1.52767 22.0338 4.24695 24.753C6.96623 27.4723 10.6544 29 14.5 29ZM14.5 7.25C15.2533 7.25 15.8594 7.85605 15.8594 8.60938V14.9531C15.8594 15.7064 15.2533 16.3125 14.5 16.3125C13.7467 16.3125 13.1406 15.7064 13.1406 14.9531V8.60938C13.1406 7.85605 13.7467 7.25 14.5 7.25ZM12.6875 19.9375C12.6875 19.4568 12.8785 18.9958 13.2184 18.6559C13.5583 18.316 14.0193 18.125 14.5 18.125C14.9807 18.125 15.4417 18.316 15.7816 18.6559C16.1215 18.9958 16.3125 19.4568 16.3125 19.9375C16.3125 20.4182 16.1215 20.8792 15.7816 21.2191C15.4417 21.559 14.9807 21.75 14.5 21.75C14.0193 21.75 13.5583 21.559 13.2184 21.2191C12.8785 20.8792 12.6875 20.4182 12.6875 19.9375Z" fill="#2557A7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="spon-sub-title">FAQ</p>
                                    <h5 class="spon-title">Frequently Asked Questions</h5>
                                </div>
                            </div>
                        `;

                        eventDescriptionHtml += '<div class="faq mt-4" id="accordion">';

                        (conttoken_data.event_faq).forEach(faq => {
                            if (faq.question && faq.answer) {
                                const faqCardId = `faq_${faq.id}`;
                                eventDescriptionHtml += `
                                    <div class="card">
                                        <div class="card-header py-3" id="heading_${faqCardId}" data-toggle="collapse" data-target="#collapse_${faqCardId}" aria-expanded="false" aria-controls="collapse_${faqCardId}">
                                            <h5 class="mb-0">${faq.question}</h5>
                                        </div>
                                        <div id="collapse_${faqCardId}" class="collapse" aria-labelledby="heading_${faqCardId}" data-parent="#accordion">
                                            <div class="card-body">
                                                ${faq.answer}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        });

                        eventDescriptionHtml += '</div>';
                    }
                }

                $('.event_description').html(eventDescriptionHtml);
                $('#event_lobby_content').html(eventLobbyContentHtml);
                /* /Event Lobby Content */


                // Contact Host
                let contactHostButtonHtml = '';
                if (conttoken_data?.org_email?.trim()) {
                    contactHostButtonHtml += `<a class="btn contact-btn metrics_action" data-toggle="modal" data-target="#contacthostModal" data-metrics="contact_host">Contact Host</a>`;
                    $('#contact_host_btn_blk').html(contactHostButtonHtml);
                } else {
                    getUserInfo(conttoken_data.ptoken, 'notify').then((event_userinfo) => {
                        if (event_userinfo?.email?.trim()) {
                            contactHostButtonHtml += `<a data-toggle="modal" data-target="#contacthostModal" class="btn contact-btn metrics_action" data-metrics="contact_host">Contact Host</a>`;
                        } else {
                            contactHostButtonHtml += `<a data-toggle="modal" data-target="#contacthostModal" class="btn contact-btn metrics_action" target="_blank" data-metrics="contact_host">Contact Host</a>`;
                        }
                        $('#contact_host_btn_blk').html(contactHostButtonHtml);
                    }).catch((error) => {
                        console.error('Error getting event user info:', error);
                    });
                }

                $(document).on('click', '#contacthostSubmit', async function(e) {
                    e.preventDefault(); // Prevent form submission
                    getEventBaseInfo({ eventtoken: eventToken }, false)
                        .then(async ({requestData, response}) => {
                        let event_output = response.output;
                        let conttoken_data = event_output.conttoken;
                        let to_email = '';
                        if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) { // email from event form
                            to_email = conttoken_data.org_email;
                        }else { // get event owner email
                            const contact_info = await getUserInfo(conttoken_data.ptoken, 'notify');
                            if (contact_info?.email?.trim()) {
                                to_email = event_userinfo?.email?.trim();
                            }else{
                                to_email = 'info@noworkerleftbehind.org';
                            }
                        }
                    if($("#contacthostForm").valid()){
                        const formData = new FormData($("#contacthostForm")[0]);
                        formData.append('taoh_action', 'taoh_contact_host');
                        formData.append('eventtoken', eventToken);
                        formData.append('to_email', to_email);
                        
                        let submit_btn = $(this);
                        submit_btn.prop('disabled', true);

                        $.ajax({
                            url: '<?php echo taoh_site_ajax_url(); ?>',
                            type: 'post',
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                            success: function (response) {
                                if(response.success){
                                    $("#contacthostModal").modal("hide");
                                    document.getElementById("contacthostForm").reset();
                                    submit_btn.prop('disabled', false);
                                    taoh_set_success_message('Thanks! Mail sent successfully.', false);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.log(xhr.responseText);
                            }
                        });
                    }
                    });
                });

                // Filter the event_links array to remove items without a label
                if (Array.isArray(conttoken_data?.link) && conttoken_data.link.length > 0) {
                    const filteredEventLinks = (conttoken_data.link).filter(link => link.label?.trim());

                    if (filteredEventLinks.length > 0) {
                        let eventLinksHtml = `<ul class="list-group list-group-flush">
                            ${filteredEventLinks.map(link => {
                                const url = link.value?.trim();
                                return `<li class="list-group-item p-0">
                                            ${url && /^https?:\/\/\S+$/.test(url)
                                                ? `<a href="${url}" target="_blank">${link.label}</a>`
                                                : link.label}
                                        </li>`;
                            }).join("")}
                            </ul>`;

                        if (filteredEventLinks.length > 1) {
                            eventLinksHtml += `<button id="toggleBtn" class="toggle-btn">Show More</button>`;
                        }

                        $('#event_links_blk').html(eventLinksHtml)

                        if (filteredEventLinks.length > 1) {
                            $('#toggleBtn').click(function () {
                                const isExpanded = ($(this).text() === "Show Less");
                                $(this).text(isExpanded ? "Show More" : "Show Less");

                                $('#event_links_blk li:not(:first-child)').each(function () {
                                    $(this).toggleClass('show');
                                });
                            });
                        }

                        $('#external_links_blk').show();
                    }
                }


                let user_timezone;
                if (isLoggedIn) {
                    user_timezone = '<?= taoh_user_timezone(); ?>';
                }
                if (!isLoggedIn || !user_timezone?.trim()) {
                    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
                $("#event_live_state").val(event_live_state);

                if (isLoggedIn && !is_event_suspended && !is_event_freeze && event_live_state === 'before') {
                    constructUpgradeModalContent(event_output, my_pToken, rsvp_slug, isLoggedIn);
                    $('.attendee_tagline').css('display', 'flex');
                } else {
                    $('.upgrade_modal_btn_wrapper').hide();
                }

                /* Ticket Types */
                let eventTicketTypesHtml = '';
                if (isLoggedIn) {
                    if (is_user_rsvp_done && event_live_state == 'live') {
                        eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Event Status</h3>';
                    } else if (is_user_rsvp_done) {
                        eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Registration Status</h3>';
                    } else {
                        if (event_live_state == 'before' || event_live_state == 'live') {
                            eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Choose a ticket to Register !</h3>';
                        } else if (event_live_state == 'after') {
                            eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Thank You !</h3>';
                        }
                    }

                    if (!is_user_rsvp_done && (event_live_state === 'before' || event_live_state === 'live')) {
                        eventTicketTypesHtml += '<ul class="ticket-list w-100">';

                        const ticket_types = conttoken_data.ticket_types;
                        let ticket_type_selected = false;
                        ticket_types.forEach(ticket_type => {
                            const ticket_type_slug = ticket_type.slug;
                            const ticket_type_title = ticket_type.title;
                            const ticket_type_cost = ticket_type.cost;

                            eventTicketTypesHtml += '<li>';
                            eventTicketTypesHtml += '<input type="radio" name="ticket" id="' + ticket_type_slug + '" value="' + encodeURIComponent(ticket_type_title) + '" class="d-none"';
                            if (is_user_rsvp_done) {
                                eventTicketTypesHtml += ' disabled';
                                if (rsvp_slug === ticket_type_slug) {
                                    eventTicketTypesHtml += ' checked';
                                    ticket_type_selected = true;
                                }
                            }
                            if (!ticket_type_selected && !is_user_rsvp_done) {
                                eventTicketTypesHtml += ' checked';
                                ticket_type_selected = true;
                            }
                            eventTicketTypesHtml += '>';
                            eventTicketTypesHtml += '<label for="' + ticket_type_slug + '" class="item btn w-100">';
                            eventTicketTypesHtml += '<p class="item-title">' + ticket_type_title + '</p>';
                            eventTicketTypesHtml += '<p class="item-cost">' + (ticket_type.price === 'paid' ? 'Costs you $' + ticket_type_cost : 'Free') + '</p>';
                            eventTicketTypesHtml += '</label>';
                            eventTicketTypesHtml += '</li>';
                        });
                        eventTicketTypesHtml += '</ul>';
                    }

                    $('#myTab').removeClass('event-live-before');
                    $('#myTab').removeClass('event-live-live');
                    $('#myTab').removeClass('event-live-end');

                    if (event_live_state === 'after') {
                        $('#myTab').addClass('event-live-end');
                        eventTicketTypesHtml += `
                        <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>

                        <a href="${TAOH_CURR_APP_URL}" class="mt-4 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>`;
                    } else {
                        if (is_event_freeze || is_event_suspended) {
                            $('#myTab').addClass('event-live-end');
                            eventTicketTypesHtml += `
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>
                              <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>        
                                              
                            <a href="${TAOH_CURR_APP_URL}" 
                            class="mt-4 btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i>Event Suspended</a>`;
                        } else {
                            if (is_user_rsvp_done && event_live_state === 'before') {
                                $('#myTab').addClass('event-live-before');
                                eventTicketTypesHtml += `
                                 <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="1"/>`;

                            } else if (!is_user_rsvp_done && event_live_state === 'before') {
                                $('#myTab').addClass('event-live-before');
                                eventTicketTypesHtml += `
                                 <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="0"/>`;

                            } else if (is_user_rsvp_done && event_live_state === 'live') {
                                $('#myTab').addClass('event-live-on');
                                eventTicketTypesHtml += `
                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                                    <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>        
                                <a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventToken}" class="mt-4 btn btn-success w-100 metrics_action" data-metrics="join_event">
                                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>
                                Event Live, ${!isValidUser ? 'Complete settings to' : 'Click to'} Join</a>`;
                            } else if (is_user_rsvp_done) {
                                $('#myTab').addClass('event-live-on');
                                eventTicketTypesHtml += `
                               <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>
                                   <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>         
                                <a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventToken}" class="mt-4 btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>`;
                            } else {
                                $('#myTab').addClass('event-live-before');
                                eventTicketTypesHtml += `
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>`;
                            }
                        }
                    }

                } else {
                    eventTicketTypesHtml = '<a aria-pressed="true" data-toggle="modal" data-target="#config-modal" data-metrics="rsvp" class="login-button btn btn-primary w-100">Login & Register Now</a>';
                }

                $('#ticket-card').html(eventTicketTypesHtml);

                /* /Ticket Types */

                let event_timestamp_start_data = {
                    utc_datetime: event_output.utc_start_at,
                    local_datetime: event_output.local_start_at,
                    timezone: event_output.local_timezone,
                    locality: event_output.conttoken.locality
                };
                let event_timestamp_end_data = {
                    utc_datetime: event_output.utc_end_at,
                    local_datetime: event_output.local_end_at,
                    timezone: event_output.local_timezone,
                    locality: event_output.conttoken.locality
                };

                let localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
                let localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);

                const event_info_container = $('#event_info_container');
                event_info_container.find('#event_start_end_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));


                $('#event_start_at').val(event_output.local_start_at);
                $('#event_end_at').val(event_output.local_end_at);
                $('#event_timezone').val(event_output?.local_timezone);
                $('#event_locality').val(event_output?.locality ?? '');

                function eventVenueLoc(conttoken_data) {
                    return (conttoken_data.map_link)
                        ? `<a href="${conttoken_data.map_link}" target="_blank" class="cursor-pointer text-underline">${conttoken_data.venue}</a>`
                        : conttoken_data.venue;
                }

                function eventJoinHere(taoh_curr_app_url, eventtoken, is_user_rsvp_done, event_live_state) {
                    return '';
                }

                /* Event Venue Info */
                let eventVenueInfoHtml = '';
                if (event_type === 'in-person') {
                    eventVenueInfoHtml += '' +
                        '<path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>' +
                        '</svg>';

                    eventVenueInfoHtml += `<span class="theme-blue-clr">In-Person, <span>${eventVenueLoc(conttoken_data)}</span></span>`;
                } else if (event_type === 'hybrid') {
                    eventVenueInfoHtml += '' +
                        '<path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#2557A7"/>' +
                        '</svg>';

                    eventVenueInfoHtml += `<span class="theme-blue-clr">Hybrid - <span>${eventVenueLoc(conttoken_data)}</span> or Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span>`;
                } else if (event_type === 'virtual') {
                    eventVenueInfoHtml += '' +
                        '<path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path></svg>';

                    eventVenueInfoHtml += `<span class="theme-blue-clr">Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span>`;
                }
                $('#event_venue_info').html(eventVenueInfoHtml);

                /* /Event Venue Info */


                function checkButtonVisibility(conttoken_data) {
                    let i_am_org = $("#is_organizer").val();
                    let enable_exhibitor_hall = $("#enable_exhibitor_hall").val();
                    let enable_speaker_hall = $("#enable_speaker_hall").val();
                    let rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                    const enable_hall = Number(conttoken_data?.enable_hall) === 1;
                    const event_halls = Array.isArray(conttoken_data?.event_halls)
                        ? conttoken_data.event_halls.filter(h => h?.id && h?.name)
                        : [];
                    let TicketArr = conttoken_data?.ticket_types.find(ticket => taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title)  || {};
                    let event_form_version = conttoken_data?.event_form_version ?? 1;

                    d_is_session_allowed = 0;
                    d_is_exhibitor_setup = 0;

                    // Initial hide all
                    $('#setup_exhibitor_slot').hide();
                    $('#setup_speaker_slot').hide();
                    $('#raffle_slot').hide();

                    if(enable_hall) {
                        let spkhallexist = false;
                        let exhhallexist = false;

                        spkhallexist = event_halls.some(
                            item => parseInt(item.accesslevel) === 1 || parseInt(item.accesslevel) === 3
                        );
                        exhhallexist = event_halls.some(
                            item => parseInt(item.accesslevel) === 2 || parseInt(item.accesslevel) === 3
                        );

                        if (spkhallexist) {
                            if (i_am_org == 1) {
                                $('#setup_speaker_slot').show();
                                d_is_session_allowed = 1;
                            } else if (TicketArr?.session_enable == 1 && TicketArr?.max_sessions_allowed > 0) {
                                const speakerCount = +$('#spk_count').val() || 0;
                                const maxSessionsAllowed = +TicketArr.max_sessions_allowed || 0;

                                if (maxSessionsAllowed && speakerCount < maxSessionsAllowed) {
                                    $('#setup_speaker_slot').show();
                                }

                                d_is_session_allowed = 1;
                            }
                        }

                        if (exhhallexist) {
                            if (i_am_org == 1) {
                                $('#setup_exhibitor_slot').show();
                                d_is_exhibitor_allowed = 1;
                            } else if (TicketArr?.exhibit_enable == 1 && TicketArr?.max_exhibits_allowed > 0) {
                                const exhibitCount = +$('#exh_count').val() || 0;
                                const maxExhibitAllowed  = +TicketArr.max_exhibits_allowed || 0;

                                if (maxExhibitAllowed && exhibitCount < maxExhibitAllowed) {
                                    $('#setup_exhibitor_slot').show();
                                }

                                d_is_exhibitor_allowed = 1;
                            }
                        }
                    }

                    if (i_am_org == 1) {
                        $('#raffle_slot').show();
                    } else {
                        var sponsor_type = $('#sponsor_type').val();
                        if (sponsor_type != "") {
                            if (event_form_version == 2) {
                                if (TicketArr?.exhibitor_raffle == 1) {
                                    $('#raffle_slot').show();
                                }
                            } else {
                                $.each(conttoken_data.event_sponsor_levels, function (sk, sponsors) {
                                    if (sponsor_type == sponsors.title) {
                                        if ($("#exh_count").val() >= sponsors.max_exhibits_allowed || sponsors.max_exhibits_allowed == 0) {
                                            enable_exhibitor_hall = 0;
                                        }
                                        if (sponsors.max_sessions_allowed == 0) {
                                            enable_speaker_hall = 0;
                                        }
                                        if (sponsors.raffle == 1) {
                                            $('#raffle_slot').show();
                                        }
                                    }
                                });
                            }
                        }
                    }
                }

                /*Event speaker, exhibitor button dispay*/
                setTimeout(() => {
                    checkButtonVisibility(conttoken_data);
                }, 5000);
                
                /* Event Sponsor popup*/
                let eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
                let eventTicketType = conttoken_data.ticket_types || {};
                let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(
                    widget => widget.quantity > 0 ? 1 : 0
                );
                var event_form_version = conttoken_data.event_form_version ?? 1;
                let social_share_status = '<?php echo $social_token;?>';
                let trackingtoken = '<?php echo $trackingtoken; ?>';
                let is_social_share_enabled = conttoken_data.event_social_sharing;
                constructSponsorInfoPopup(event_output.eventtoken, eventSponsorWidgetType,user_profile_type, conttoken_data.org_email,social_share_status,eventTicketType,event_form_version,is_social_share_enabled,trackingtoken,isLoggedIn);

                setTimeout(() => {
                    getEventMetaInfo(event_output.eventtoken).then(() => {
                        getEventsHall(event_output.eventtoken);
                    });
                }, 3000);

                setTimeout(() => {
                    eventCheckinList(event_output.eventtoken, '', 1);
                    var event_status = $('#event_status_hidden').val();
                    if (event_status == 2 && eventSponsorWidgetTypeStatusList.includes(1)) {
                        $('.event_sponsor_right_header').show();
                        $('#sponsor_card').show();
                        $('.get-started').show();
                    } else {
                        $('.event_sponsor_right_header').hide();
                        $('.get-started').hide();
                    }

                    var superorganizer_token = $('#superorganizer_token').val();
                    if (event_status == 1 || my_pToken == superorganizer_token) {
                        $('.speaker-banner').hide();
                        $('.exhibitor-banner').hide();
                    } else {
                        $('.speaker-banner').show();
                        $('.exhibitor-banner').show();
                        $('.rsvp_actions').css('display', 'none');

                        $("#rsvp_default_list").show();
                        if($("#is_organizer").val() == 1){
                            $('.rsvp_actions').show();
                        }

                        if(is_user_rsvp_done){
                            $("#register_now").hide();
                        }
                        loader(false, $("#rsvpdir_loaderArea"));

                    }
                    if($("#is_organizer").val() == 1){
                        $('#networking_link').show();
                    } else {
                        $('#networking_link').hide();
                    }

                    if (conttoken_data.table_discussion != '' && conttoken_data.table_discussion != undefined && conttoken_data.table_discussion == 1) {
                        $('#tables_top').show();
                    } 

                    if (conttoken_data.comments != '' && conttoken_data.comments != undefined && conttoken_data.comments == 1) {
                        $('#comments_top').show();
                    } 
                    
                }, 3000);
                /* Event Sponsor popup*/

                if(typeof constructOrganizerVideoModalContent === 'function') {
                    constructOrganizerVideoModalContent(event_output, user_timezone);
                }

                /* Show Event Upgrade  */
                if (isLoggedIn && show_upgrade) {
                    if (is_event_suspended || is_event_freeze) {
                        taoh_set_info_message('Event upgrade is not available for suspended or frozen events.', false, 'toast-middle', [
                            {
                                text: 'OK',
                                action: () => {},
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);
                    } else {
                        if (event_live_state === 'before') {
                            if($('#upgrade_modal_btn').length) $('#upgrade_modal_btn').trigger('click');
                        } else {
                            taoh_set_info_message('Event upgrade is only available before the event goes live.', false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                        }
                    }
                }
                /* /Show Event Upgrade */

                /* Event Ticket Confirmation */
                if (show_rsvp_ticket) {
                    getEventRSVPInfo({rsvptoken: rsvp_ticket_token}, true)
                        .then(async ({requestData, response}) => {
                            const rsvp_output = response.output;

                            if (rsvp_output.success) {
                                const rsvp_ptoken = rsvp_output.rsvp_user_ptoken;
                                const rsvp_slug = rsvp_output.rsvp_slug;
                                const rsvp_amount = parseFloat(rsvp_output.amount) || 0;

                                let rsvpTicketHtml = `<div class="view-ticket d-flex flex-column flex-md-row align-items-center">
                                <div style="width: 100%; max-width: 342px;">`;

                                rsvpTicketHtml += '<button type="button" class="btn btn-success valid-badge mb-3"><i class="fa fa-check-circle mr-1" aria-hidden="true"></i><span>Valid for entry</span></button>';
                                rsvpTicketHtml += `<h3 class="ticket-title pb-3">${conttoken_data.title}</h3>`;

                                if (event_type === 'in-person') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">In-Person, <span>${eventVenueLoc(conttoken_data)}</span></span></p>`;
                                } else if (event_type === 'hybrid') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Hybrid - <span>${eventVenueLoc(conttoken_data)}</span> or Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span></p>`;
                                } else if (event_type === 'virtual') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span></p>`;
                                }

                                rsvpTicketHtml += `<p class="ticket-content py-1">Start DateTime: <span>${beautifyTime(localized_event_start_data.datetime, localized_event_start_data.timezone, '{week}, {month} {day}, {year}, {time} {abbr}')}</span></p>`;

                                const rsvp_userinfo = await getUserInfo(rsvp_ptoken, 'notify');
                                if (rsvp_userinfo?.fname?.trim()) {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Name: <span>${rsvp_userinfo.fname || ''} ${rsvp_userinfo.lname || ''}</span></p>`;
                                }

                                let current_ticket_type = (conttoken_data.ticket_types).find(ticket_type => ticket_type.slug === rsvp_slug);
                                if (current_ticket_type) {
                                    rsvpTicketHtml += `<p class="ticket-content pt-3">Ticket Type: ${current_ticket_type['title'] ?? ''} | ${(rsvp_amount > 0) ? ' Paid $' + rsvp_amount : 'Free'} </p>`;
                                }

                                rsvpTicketHtml += `</div>`;

                                rsvpTicketHtml += `<div class="text-center">`;
                                let eventTicketBanner = eventBannersArray[0] || [];
                                if (eventTicketBanner.type === "image") {
                                    rsvpTicketHtml += `<img class="ticket-main-image" src="${eventTicketBanner.src}" alt="Event">`;
                                } else if (eventTicketBanner.type === "video") {
                                    let thumbnailSrc = "";
                                    if (eventTicketBanner.src.includes("youtube.com") || eventTicketBanner.src.includes("youtu.be")) {
                                        thumbnailSrc = getYouTubeThumbnail(eventTicketBanner.src);
                                    } else if (eventTicketBanner.src.includes("vimeo.com")) {
                                        getVimeoThumbnail(eventTicketBanner.src, (thumbnail) => {
                                            document.querySelector(`[data-index='${index}']`).src = thumbnail;
                                        });
                                        thumbnailSrc = "https://via.placeholder.com/150/FF0000/FFFFFF?text=Vimeo";
                                    } else {
                                        thumbnailSrc = "https://via.placeholder.com/150/0000FF/FFFFFF?text=Video";
                                    }

                                    rsvpTicketHtml += `<img class="ticket-main-image" src="${thumbnailSrc}" alt="Event">`;
                                }

                                rsvpTicketHtml += `<img class="ticket-stamp" src="${_taoh_site_url_root + '/assets/images/valid-for-admission.png'}" alt="valid-for-admission">`;

                                rsvpTicketHtml += `</div>
                            </div>`;

                                $('#rsvpTicketModal .modal-body').html(rsvpTicketHtml);

                                let rsvpTicketFooterHtml = `<button type="button" class="btn theme-btn-primary" data-dismiss="modal" style="width: 150px;">OK</button>`;
                                $('#rsvpTicketModal .modal-footer').html(rsvpTicketFooterHtml);

                                $('#rsvpTicketModal').modal('show');
                            } else {
                                taoh_set_error_message('Unable to find any valid RSVP ticket info. Use a valid link to view your RSVP ticket.', false, 'toast-middle-right', [
                                    {
                                        text: 'OK',
                                        action: () => {},
                                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                                    }
                                ]);
                            }
                        })
                        .catch(error => console.error("Error fetching event rsvp info:", error));

                }
                /* /Event Ticket Confirmation */

                $('.aw').awloader('hide');
            }

            // :rk temp always fetch from server
            getEventBaseInfo({eventtoken: eventToken}, true)
                .then(({requestData, response}) => processEventBaseInfo(requestData, response)
            )
                .catch(error => console.error("Error fetching event info:", error));

            
            /*============== Exhibitor ============== */

            $('#setup_exhibitor_slot_btn').on('click', async function () {
                const form = $('#setup_exhibitor_slot_form');
                if (!form.length) return;

                let setup_exhibitor_slot_btn = $('#setup_exhibitor_slot_btn');
                let setup_exhibitor_slot_btn_icon = setup_exhibitor_slot_btn.find('i');

                // Clear all standard inputs, selects, and textareas in the form
                form[0].reset();
                form.find('input[type="hidden"][data-dynamic="1"]').val('');
                if (form.data('validator')) {
                    form.validate().resetForm();
                    form.find('.is-invalid, .is-valid, .error').removeClass('is-invalid is-valid error');
                }

                setup_exhibitor_slot_btn.prop('disabled', true);
                setup_exhibitor_slot_btn_icon.removeClass('fa-pencil-square-o').addClass('fa-spinner fa-spin');

                var eventHallAccess = [];
                var eventHallAccessKey = `event_hall_access_${eventToken}`;
                const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey); // await 
                if (data?.values) {
                    eventHallAccess = data?.values.output;
                }

                $('#exh_tags').val([]).trigger('change');
                $('#exh_room_status').prop('checked', true);
                $(".exh_streaming_link_wrapper").show();
                $(".lead-raffle").hide();
                updateraffle();
                
                // Additionally, manually clear custom widgets like TomSelect
                if (typeof timeZoneInstance !== 'undefined') {
                    timeZoneInstance.clear(); // Clears selected value
                }
                $('#exhibitorSlotModal').find("#exh_logo_preview, #exh_banner_preview").html('');
                $('#exhibitorSlotModal').find('#taoh_action').val('save_exhibitor_slot');
                $('#exhibitorSlotModal').find('#eventtoken').val(eventToken);
                $('#exhibitorSlotModal').find('#ptoken').val(my_pToken);
                $('#exhibitorSlotModal').find('#exh_contact_email').val(my_email);
                getEventBaseInfo({ eventtoken: eventToken }, false)
                    .then(({requestData, response}) => {
                        let event_output = response.output;
                        let event_owner = event_output.ptoken;
                        let conttoken_data = event_output.conttoken;
                        var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                        var event_form_version = conttoken_data.event_form_version ?? 1;
                        var TicketArr = conttoken_data.ticket_types.find(ticket => taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title)  || {};

                        let event_instance_owner = conttoken_data.ptoken;
                        let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                            .split(',').concat(event_instance_owner)
                            .map(token => token.trim())
                            .filter(token => token);

                        if(event_owner) event_organizer_ptokens.push(event_owner);

                        const exh_allowed = new Set(["2", "3"]);
                        const allowed_exhibitor_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                            .filter(h => Number(h?.id) > 0 && h?.name && exh_allowed.has(h.accesslevel));

                        if (!allowed_exhibitor_halls.length) {
                            taoh_set_error_message('No Exhibitor Hall available for setup');
                            setup_exhibitor_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                            setup_exhibitor_slot_btn.prop('disabled', false);
                            return;
                        }
                        
                        var sponsor_type = $('#sponsor_type').val();
                        var is_organizer = $("#is_organizer").val();
                        var user_profile_type = $("#user_profile_type").val();
                        var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                        var hall_exist = 0;
                        var notallowed = 0;

                        if (is_organizer == 1) {
                            notallowed = 0;
                        } else {
                            const count = Number($("#exh_count").val()) || 0;
                            const exceeds = (cap) => Number(cap) === 0 || count >= Number(cap || 0);
                            if (exceeds(TicketArr?.max_exhibits_allowed)) {
                                notallowed = 1;
                            }
                        }

                        if(notallowed == 1){
                            taoh_set_error_message('The maximum exhibitor count has been exceeded.',false);
                            setup_exhibitor_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                            setup_exhibitor_slot_btn.prop('disabled', false);
                            $('#setup_exhibitor_slot').hide();
                            return;
                        }
                        
                        $('#exh_hall').empty();
                        allowed_exhibitor_halls.forEach(hall => {
                            if(!hall.id || !hall.name) return;

                            const hall_id   = hall.id;
                            const hall_name = hall.name;
                            const hall_token = hall.name;

                            let showHall = false;

                            if (
                                event_form_version == 2 &&
                                !is_organizer &&
                                !(
                                    TicketArr?.exhibitor_halls?.includes?.("All") ||
                                    TicketArr?.exhibitor_halls?.includes?.(hall_name) ||
                                    TicketArr?.exhibitor_halls?.includes?.(hall_id)
                                )
                            ) {
                                return; // skip this hall
                            }

                            if (event_form_version == 2) {
                                if (is_organizer == 1) {
                                    showHall = true;
                                } else if (
                                    TicketArr &&
                                    typeof TicketArr.max_exhibits_allowed !== "undefined" &&
                                    Number(TicketArr.max_exhibits_allowed) > 0 &&
                                    (
                                        TicketArr.exhibitor_halls === "All" ||
                                        TicketArr.exhibitor_halls?.includes?.("All") ||
                                        TicketArr.exhibitor_halls?.includes?.(hall_name) ||
                                        TicketArr.exhibitor_halls?.includes?.(hall_id)
                                    )
                                ) {
                                    showHall = true;
                                }
                            } else {
                                const exhibitorAccess = eventHallAccess?.exhibitor?.[hall_name];

                                if (!exhibitorAccess) {
                                    // no restriction defined for this hall: allowed
                                    showHall = true;
                                } else {
                                    const canAccess = roleKey => (
                                        exhibitorAccess?.[roleKey]?.allowed > 0
                                    );

                                    // organizer always allowed
                                    if (is_organizer == 1) {
                                        showHall = true;
                                    }

                                    // sponsor_type
                                    if (!showHall && sponsor_type && canAccess(sponsor_type)) {
                                        showHall = true;
                                    }

                                    // user_profile_type
                                    if (!showHall && user_profile_type && canAccess(user_profile_type)) {
                                        showHall = true;
                                    }

                                    // rsvp_sponsor_title
                                    if (!showHall && rsvp_sponsor_title && canAccess(rsvp_sponsor_title)) {
                                        showHall = true;
                                    }
                                }
                            }

                            if (showHall) {
                                hall_exist = 1;
                                $('#exh_hall').append(
                                    `<option value="${hall_token}">${hall_name}</option>`
                                );
                            }
                        });


                        if (hall_exist === 0) {
                            taoh_set_error_message('No Exhibitor Hall available for setup');
                            setup_exhibitor_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                            setup_exhibitor_slot_btn.prop('disabled', false);
                            return;
                        }

                        const tagsArr = (conttoken_data.event_tags || '').split(',').map(t => t.trim()).filter(Boolean);
                        $('.tags-field').select2({data: tagsArr, width: '100%'});

                        _getEventMetaInfo({eventtoken: eventToken}, true)
                            .then(({requestData, response}) => {
                                let spon_list = response.output.event_sponsor;
                                if (spon_list?.length) {
                                    spon_list.forEach(ddata =>
                                        Object.values(ddata).forEach(v => {
                                            if (v.ptoken === my_pToken) {
                                                const exhibitorslotmodal_elem = $('#exhibitorSlotModal');

                                                exhibitorslotmodal_elem.find('#taoh_action').val('save_exhibitor_slot');
                                                exhibitorslotmodal_elem.find('#eventtoken').val(eventToken);
                                                exhibitorslotmodal_elem.find('#ptoken').val(v.ptoken);
                                                exhibitorslotmodal_elem.find('#sponsor_id').val(v.ID);
                                                exhibitorslotmodal_elem.find('#exh_session_title').val(taoh_desc_decode(v.title));
                                                exhibitorslotmodal_elem.find('#exh_description').val(taoh_desc_decode(v.description));
                                                exhibitorslotmodal_elem.find('#exh_hero_button_url').val(v.link);
                                                exhibitorslotmodal_elem.find('#exh_logo').val(v.image);
                                                exhibitorslotmodal_elem.find('#exh_hall').val(v.hall);
                                                exhibitorslotmodal_elem.find('#exh_booth').val(v.booth);
                                                exhibitorslotmodal_elem.find('#exh_start_date').val(v.start_date);
                                                exhibitorslotmodal_elem.find('#exh_end_date').val(v.end_date);
                                                
                                                exhibitorslotmodal_elem.find('#exh_raffle_ques').val(v.exh_raffle_ques);
                                                exhibitorslotmodal_elem.find('#exh_raffle_announce_time').val(v.exh_raffle_announce_time);
                                                exhibitorslotmodal_elem.find('#exh_raffle_timezoneSelect').val(v.exh_raffle_timezoneSelect);
                                                exhibitorslotmodal_elem.find('#exh_winner_profile').val(v.exh_winner_profile);
                                                exhibitorslotmodal_elem.find('#exh_state').val(v.exh_state);
                                                exhibitorslotmodal_elem.find('#exh_external_video_room_link').val(v.exh_external_video_room_link);
                                                exhibitorslotmodal_elem.find('#exh_streaming_link').val(v.exh_streaming_link);

                                                exhibitorslotmodal_elem.modal('show');
                                            }
                                        })
                                    );
                                }
                            });

                        updateEventMetaInfo(eventToken, true); // update intao on open exhibitor slot btn

                        $('#exhibitorSlotModal').find('input[name="country_locked"]').val(conttoken_data.country_locked);

                        $('label[for="exh_contact_email"] .text-danger').css('display', ((is_organizer == 1) ? 'none' : 'inline-block'));

                        $('#exhibitorSlotModal').modal('show');
                        setup_exhibitor_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                        setup_exhibitor_slot_btn.prop('disabled', false);
                        $('#exh_sponsor_levels').empty();
                        $('.exh_sponsor_levels_wrapper').hide();
                        setTimeout(() => {
                            $('#exh_sponsor_levels').empty();
                                $('.exh_sponsor_levels_wrapper').hide();
                        }, 5000);
                        
                    })
                    .catch(error => console.error("Error fetching event info:", error));
            });

            /*============== /Exhibitor ============== */

            $('#setup_speaker_slot_btn').on('click', async function () {
                const form = $('#spk_form');
                if (!form.length) return;

                let setup_speaker_slot_btn = $('#setup_speaker_slot_btn');
                let setup_speaker_slot_btn_icon = setup_speaker_slot_btn.find('i');

                // Clear all standard inputs, selects, and textareas in the form
                form[0].reset();
                form.find('input[type="hidden"][data-dynamic="1"]').val('');
                if (form.data('validator')) {
                    form.validate().resetForm();
                    form.find('.is-invalid, .is-valid, .error').removeClass('is-invalid is-valid error');
                }

                const $old_repeatable_speaker = $("#speaker_blk #repeatable_speaker");
                const $new_repeatable_speaker = $('<div id="repeatable_speaker"></div>');
                $old_repeatable_speaker.replaceWith($new_repeatable_speaker);
                initRepeatableSpeaker($new_repeatable_speaker);

                $('#spk_tags').val([]).trigger('change');
                $('#speakerSlotModal #session_enable_tao_networking_yes').prop('checked', true);
                $(".spk_external_video_room").hide();
                $(".spk_streaming_link_wrapper").show();
                $("#spk_logo_image_preview, #spk_image_preview, .spk_profileimg_preview").html('');
                $('#speakerSlotModal #speaker_blk').collapse('hide');
                $('#speakerSlotModal #sessionStateCollapse').collapse('hide');

                $('#speakerSlotModal').find('#taoh_action').val('event_save_speaker');
                $('#speakerSlotModal').find('#eventtoken').val(eventToken);
                $('#speakerSlotModal').find('#ptoken').val(my_pToken);
                $("#local_timezoneSelect_session").val(my_local_timezone);

                updateTimeSlotHelperText(my_local_timezone);

                if($("#is_organizer").val() == 1){
                    $("#spk_video_room_block").show();
                    $("#spk_video_room-yes").prop("disabled",true);
                }else{
                    $("#spk_video_room-no").prop("checked",false);
                }

                var eventHallAccess = [];
                var eventHallAccessKey = `event_hall_access_${eventToken}`;
                const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey); // await 
                if (data?.values) {
                    eventHallAccess = data?.values.output;
                }

                getEventBaseInfo({ eventtoken: eventToken }, false)
                    .then(({requestData, response}) => {
                    let event_output = response.output;
                    let event_owner = event_output.ptoken;
                    let conttoken_data = event_output.conttoken;
                    var event_form_version = conttoken_data.event_form_version ?? 1;
                    var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                    var ticketArr = conttoken_data.ticket_types.find(ticket => taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title)  || {};

                    let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                        .split(',')
                        .map(token => token.trim())
                        .filter(token => token);

                    if(event_owner) event_organizer_ptokens.push(event_owner);

                    let event_instance_owner = conttoken_data.ptoken;
                    event_organizer_ptokens.push(event_instance_owner);

                    const spk_allowed = new Set(["1", "3"]);
                    const allowed_speaker_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                        .filter(h => Number(h?.id) > 0 && h?.name && spk_allowed.has(h.accesslevel));

                    if (!allowed_speaker_halls.length) {
                        taoh_set_error_message('No Speaker Hall available for setup');
                        return;
                    }

                    const tagsArr = (conttoken_data.event_tags || '').split(',').map(t => t.trim()).filter(Boolean);
                    $('.tags-field').select2({data: tagsArr, width: '100%'});
                    
                    var sponsor_type = $('#sponsor_type').val();
                    var is_organizer = $("#is_organizer").val();
                    var user_profile_type = $("#user_profile_type").val();
                    var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                    var hall_exist = 0;
                    var notallowed = 0;

                    if (is_organizer == 1) {
                        notallowed = 0;
                    } else {
                        const count = Number($("#spk_count").val()) || 0;
                        const exceeds = (cap) => Number(cap) === 0 || count >= Number(cap || 0);
                        if (exceeds(ticketArr?.max_sessions_allowed)) {
                            notallowed = 1;
                        }
                    }

                    if(notallowed == 1){
                        taoh_set_error_message('The maximum speaker count has been exceeded.',false);
                        setup_speaker_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                        setup_speaker_slot_btn.prop('disabled', false);
                        $('#setup_speaker_slot').hide();
                        return;
                    }

                    $('#spk_hall').empty();
                    $('#spk_hall').append('<option value="">Select Session Room</option>');

                    allowed_speaker_halls.forEach(hall => {
                        let hall_id = hall.id ?? '';
                        let hall_name = hall.name ?? '';
                        let hall_token = hall.name ?? '';
                        var showhall = 0;
                        if (event_form_version == 2 && (Array.isArray(ticketArr?.speaker_halls) && !ticketArr.speaker_halls.includes("All") && !ticketArr.speaker_halls.includes(hall_name) && !ticketArr.speaker_halls.includes(hall_id))){
                            return true;
                        }
                        if(event_form_version == 2){
                            if (is_organizer == 1) {
                                showhall = 1;
                            }
                            if ( ticketArr && typeof ticketArr.max_sessions_allowed !== 'undefined' && ticketArr.max_sessions_allowed > 0 && ( ticketArr.speaker_halls === 'All' ||   (  Array.isArray(ticketArr.speaker_halls) && ( ticketArr.speaker_halls.includes('All') || ticketArr.speaker_halls.includes(hall_name) || ticketArr.speaker_halls.includes(hall_id))) ||   (  Array.isArray(ticketArr.session_halls) && ( ticketArr.session_halls.includes('All') || ticketArr.session_halls.includes(hall_name) || ticketArr.session_halls.includes(hall_id))) )) {
                                showhall = 1;
                            }
                        }else{
                            if(typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined"){
                                if(is_organizer == 1){
                                    showhall = 1;
                                    if(typeof eventHallAccess['speaker'][hall_name]["organizer"] !== "undefined" && eventHallAccess['speaker'][hall_name]["organizer"]["allowed"] > 0){
                                        showhall = 1;
                                    }
                                }
                                if(sponsor_type != ''){
                                    if(typeof eventHallAccess['speaker'][hall_name][sponsor_type] !== "undefined" && eventHallAccess['speaker'][hall_name][sponsor_type]["allowed"] > 0){
                                        showhall = 1;
                                    }
                                }
                                if(user_profile_type != ''){
                                    if(typeof eventHallAccess['speaker'][hall_name][user_profile_type] !== "undefined" && eventHallAccess['speaker'][hall_name][user_profile_type]["allowed"] > 0){
                                        showhall = 1;
                                    }
                                }
                                if(rsvp_sponsor_title != ''){
                                    if(typeof eventHallAccess['speaker'][hall_name][rsvp_sponsor_title] !== "undefined" && eventHallAccess['speaker'][hall_name][rsvp_sponsor_title]["allowed"] > 0){
                                        showhall = 1;
                                    }
                                }
                            }else{
                                showhall = 1;
                            }
                        }
                        if(showhall == 1 && hall_token != '' && hall_name != ''){
                                hall_exist = 1;
                                let hall_option = `<option value="${hall_token}">${hall_name}</option>`;
                                $('#spk_hall').append(hall_option);
                            }
                    });

                    if (hall_exist === 0) {
                        taoh_set_error_message('No Speaker Hall available for setup',false);
                        setup_speaker_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                        setup_speaker_slot_btn.prop('disabled', false);
                        return;
                    }

                    $('#speakerSlotModal').find('input[name="country_locked"]').val(conttoken_data.country_locked);

                    $('#speakerSlotModal').modal('show');
                }).catch(error => console.error("Error fetching event info:", error));
            });

            $('input[name="spk_timezoneSelect"]').on('change', function(){
                $('#spk_datefrom').val('');
                $('#spk_dateto').val('');
                updateTimeSlotHelperText(my_local_timezone);
            });

            $('input[name="spk_video_room[]"]').on('change', function () {
                let isZoomChecked = $('#spk_video_room-yes').prop('checked'); // Check if Zoom is checked
                let isPhysicalRoomChecked = $('#spk_video_room-physical').prop('checked'); // Check if Physical Room is checked

                if (isZoomChecked) {
                    $('#spk_video_room-link').css('display', 'block');
                }else {
                    $('#spk_video_room-link').css('display', 'none');
                }
                if (isPhysicalRoomChecked) {
                    $('#spk_phycial_location-link').css('display', 'block');
                }else {
                    $('#spk_phycial_location-link').css('display', 'none');
                }
            });

            setTimeout(() => {
                eventCheckIn(eventToken);
            }, 5000);
           
            if(live_state == 'before'){
                setInterval(() => updateEventStatusButton(), 15*60*1000);
            }

            $('#continuePurchase').modal('hide');

            if(ref_slug != '' && success_discount_amt != ''){
                if(trackingtoken != ''){
                    getEventSponsorForShare(eventToken, trackingtoken).then(() => {}).catch(() => {});
                }
                let newUrl = '<?php echo $original_link; ?>';
                history.replaceState(null, "", newUrl);
            }
        });

        function updateTimeSlotHelperText(user_timezone) {
            let spk_timezone = $('input[name="spk_timezoneSelect"]').val();
            let event_start_at = $("#event_start_at").val();
            let event_end_at = $("#event_end_at").val();
            let event_timezone = $("#event_timezone").val();
            let event_locality = $("#event_locality").val();

            let event_timestamp_start_data = {
                utc_datetime: event_start_at,
                local_datetime: event_start_at,
                timezone: event_timezone,
                locality: event_locality
            };

            let event_timestamp_end_data = {
                utc_datetime: event_end_at,
                local_datetime: event_end_at,
                timezone: event_timezone,
                locality: event_locality
            };

            let startdate = format_event_timestamp(event_timestamp_start_data, spk_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);
            let enddate = format_event_timestamp(event_timestamp_end_data, spk_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);

            if (spk_timezone) {
                const formatForInput = dt => dt.replace(/:\d{2}$/, '');

                const formattedStartDateTime = formatForInput(startdate.replace(' ', 'T'));
                const formattedEndDateTime   = formatForInput(enddate.replace(' ', 'T'));

                document.getElementById('spk_datefrom').min = formattedStartDateTime;
                document.getElementById('spk_datefrom').max = formattedEndDateTime;
                document.getElementById('spk_dateto').min = formattedStartDateTime;
                document.getElementById('spk_dateto').max = formattedEndDateTime;

                $('#spk_timeslot_timezone_txt').text(`in ${user_timezone}`);
            }
        }

        /*============== Exhibitor ============== */
        function updateraffle(){
            if($("input[name='exh_raffles']:checked").val() == '1') {
                $('#exh_raffle_options').show();
            } else {
                $('#exh_raffles_timebound_no').prop('checked', true).trigger('change');
                $('#exh_raffle_time_bound_time').hide();
                $('#exh_raffle_options').hide();
            }
        }

        function updateraffletimebound(){
            if($("input[name='exh_raffles_timebound_option']:checked").val() == '1') {
                $('#exh_raffle_time_bound_time').show();
            } else {
                $('#exh_raffle_time_bound_time').hide();
            }
        }
        /*============== /Exhibitor ============== */

        if(!isValidUser) {
            setTimeout(() => {
                $('#completeSettingsModal').modal('show');
            }, 5000); // 5 seconds

            $('.complete_settings_now').on('click', function () {
                $('#completeSettingsModal').modal('hide');

                $('#completeSettingsModal').on('hidden.bs.modal', function () {
                    // Only run once
                    $(this).off('hidden.bs.modal');

                    if (typeof showBasicSettingsModal === 'function') {
                        showBasicSettingsModal();
                    }
                });
            });
        }


        const url = new URL(window.location.href);
        if (url.searchParams.has('confirmation')) {
            if (url.searchParams.get('confirmation') === 'sponsor') {
                if (url.searchParams.has('status') && url.searchParams.get('status') === 'success') {
                    taoh_set_success_message('Thank you for your interest in sponsoring this event.');
                } else {
                    taoh_set_error_message('There was an error processing your request. Please try again later.');
                }
            }

            url.searchParams.delete('confirmation');
            url.searchParams.delete('status');
            url.searchParams.delete('tickettoken');
            window.history.pushState({}, '', url.toString());
        }

        if (url.searchParams.has('from') && url.searchParams.get('from') === 'sponsor') {
            delete_events_into('event_details_sponsor_' + eventToken);
            delete_events_into('event_MetaInfo_' + eventToken);

            url.searchParams.delete('from');
            window.history.pushState({}, '', url.toString());
        }

        if (url.searchParams.has('upgrade') && url.searchParams.get('upgrade') === 'from_email') {
            url.searchParams.delete('upgrade');
            window.history.pushState({}, '', url.toString());
        }

        function delete_events_into(find_key) {
            getIntaoDb(dbName).then((db) => {
                let dataStoreName = EVENTStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const index_key = cursor.primaryKey;
                        if (index_key.includes(find_key)) {
                            objectStore.delete(index_key);
                        }
                        cursor.continue();
                    }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }


        // Function to toggle visibility based on radio selection
        function toggleVisibility(radioName, sectionToShow) {
                const yesOption = document.querySelector(`input[name="${radioName}"][id$="-yes"]`);
                const noOption = document.querySelector(`input[name="${radioName}"][id$="-no"]`);
                const section = document.getElementById(sectionToShow);

                if (yesOption.checked) {
                    section.style.display = "";
                } else if (noOption.checked) {
                    section.style.display = "none";
                }
            }

        // Attach event listeners to radio buttons to trigger the function
        document.addEventListener("DOMContentLoaded", function() {
            // For Speaker Video Room
            document.querySelectorAll('input[name="speaker-video-room"]').forEach(input => {
                input.addEventListener('change', function() {
                    toggleVisibility('speaker-video-room', 'speaker-video-room-link');
                });
            });
        });
        
        $(document).on('change', 'input[name="spk_video_room[]"]', function() {
            if($("#spk_video_room-no").prop("checked")){
                $("#spk_video_room-yes").prop("disabled",true);
            }else{
                $("#spk_video_room-yes").prop("disabled",false);
            }
            if($("#spk_video_room-yes").prop("checked")){
                $("#spk_video_room-no").prop("disabled",true);
            }else{
                $("#spk_video_room-no").prop("disabled",false);
            }
        });

        function formatTimestamp(timestamp) {
            var year = timestamp.slice(0, 4);
            var month = timestamp.slice(4, 6);
            var day = timestamp.slice(6, 8);
            var hour = timestamp.slice(8, 10);
            var minute = timestamp.slice(10, 12);
            var second = timestamp.slice(12, 14);

            // Create the formatted datetime string
            var formattedDateTime = year + '-' + month + '-' + day + 'T' + hour + ':' + minute + ':' + second;
            return formattedDateTime;
        }
        
        function eventCheckIn(eventtoken){
           // alert(eventtoken)
           var data = {
               'taoh_action': 'event_checkin',
               'eventtoken': eventtoken,
               'country_locked': $('#event_country_lock').val(),
               'country': $('#event_country_name').val(),
               'ptoken': '<?php echo $ptoken;?>',
               'ticket_details': '<?php echo json_encode($current_ticket_type);?>',
           };
           $.post(_taoh_site_ajax_url, data, function (response) {
               if (response.success) {
                   taoh_set_success_message('Event checkedin Successfully.');
               } else {
                  
               }
           }).fail(function () {
               console.log("Network issue!");
           })
      }

        function updateEventStatusButton(){
        let user_timezone;
        if (isLoggedIn) {
            user_timezone = '<?= taoh_user_timezone(); ?>';
        }
        if (!isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';
        let event_output = event_arr;
        let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
        let event_live_link = (chat_room_status == 2 && isValidUrl(event_output.conttoken.external_link))
                            ? event_output.conttoken.external_link : '<?php echo $adopter_url;?>';
        if(event_live_state == 'live'){
            taoh_set_success_message('Event live now!!', false);
            setTimeout(function() {
                location.reload();
                // window.open(event_live_link, '_blank');
                // window.location.href = event_live_link;
            }, 2000);
        }
      }
      
        $(document).on('click', '.event_sponsor_right_header', function() {          
           save_metrics('become_sponsor', 'click', eventToken);
        });

        
        $(document).on('click', '.get-started', function() {           
           save_metrics('sponsor_get_started', 'click', eventToken);
        });

        $(document).on('click', '.join_video_link', function() {           
           save_metrics('join_video_link', 'click', eventToken);
        });

        $(document).on('click', '.join_networking', function() {
           save_metrics('join_networking', 'click', eventToken);
        });

        $(document).on('click', '.metrics_action', function() {
            let action = $(this).data('metrics');
            save_metrics(action, 'click', eventToken);
        });

        <?php if(TAOH_DOJO_SUGGESTION_ENABLE) { ?>
             let timelimit = <?php echo (int)TAOH_DOJO_SUGGESTION_TIMELIMIT; ?>;
             let innertimelimit = Math.floor(timelimit / 2);

            // Every 5 mins: refresh all contexts
            setInterval(() => {
                refreshDojoLobbyContexts();
            }, timelimit);

            // Every 1 min: check one scenario
            setInterval(() => {
                checkNextDojoEventScenario();
            }, innertimelimit );

            // Initial trigger
            refreshDojoLobbyContexts();
            checkNextDojoEventScenario();
 

        <?php } ?>

        let currentShareLink = "";
        $(document).on("click", "[data-target='#shareModal']", function () {
            if ($(this).hasClass('sponsor-share-click')) {
                $('.sponsor-share-title').show();
                $('.normal-share-title').hide();
                $('#social_from').val(2);
                $('.email-btn').hide();
                $('.copys-btns').hide();
            }else{
                $('.sponsor-share-title').hide();
                $('.normal-share-title').show();
                $('#social_from').val(0);
                $('.email-btn').show();
                $('.copys-btns').show();
            }
            let shareUrl = $(this).data("url");     
            if(shareUrl != '' && shareUrl != undefined){
                currentShareLink = shareUrl;
            }   
        
        
        });

    $(window).on('scroll', function() {
        var $sticky = $('.sticky-top-fixed');

        if ($sticky.length) {
            var top_sticky_pos = $sticky.offset().top;
            <?php if( $show_rsvp_confirmation && $live_state != 'live') 
                $top = 765;  
                else if($show_rsvp_confirmation) $top = 300;
                else { $top = 126; } 
            ?>
            if(top_sticky_pos > <?php echo $top; ?>) {
                $sticky.addClass('is-sticky');
            } else {
                $sticky.removeClass('is-sticky');
            }
        }

        if ($(this).scrollTop() > 100) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });

    </script>
<?php
require_once('events_popup.php');

taoh_get_footer();
