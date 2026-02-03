<?php
/**
 * Event Chat/Lobby Page
 * Simplified version - CSS/JS extracted to assets/events/
 */
include_once TAOH_SITE_PATH_ROOT.'/assets/icons/icons.php';

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

// URL utility functions now in functions.php (removePathSegment, addPathSegment)

$user_info_obj = taoh_user_all_info();
$valid_user = (bool) $user_info_obj?->profile_complete ?? false;
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$profile_type = ($taoh_user_is_logged_in && in_array($user_info_obj?->type ?? '', ['professional','employer','provider'], true))
    ? $user_info_obj->type : '';

$my_local_timezone = taoh_user_timezone();

$current_app = taoh_parse_url(3);
$eventtoken = taoh_parse_url(4);

$footer_tracking_link = 'events_lobby_'.$eventtoken;

if(taoh_parse_url(5) ){
    $table_field = taoh_parse_url(5);
   $to_page = taoh_parse_url(6,0);
    if(taoh_parse_url(5) == 'stlo')  {
        $table_field = taoh_parse_url(6);
        $to_page = taoh_parse_url(7,0);
    }

    if($table_field != '' && $table_field!= 'stlo' && $table_field == 'tables'){
        $uurl = TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG .'/tables?eventtoken='.$eventtoken.'&to_page='.$to_page;
        taoh_redirect($uurl);
        taoh_exit();
    }
}

$sharerlink  = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$click_view = !empty($_SERVER['HTTP_REFERER']) ? 'click' : 'view';

const TAO_PAGE_TYPE = 'events';
$app_data = taoh_app_info(TAO_PAGE_TYPE);

$user_timezone = taoh_user_timezone();
if (empty($user_timezone)) $user_timezone = 'America/New_York';

$taoh_vals = [
    'token' => taoh_get_api_token(1, 1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken,
];

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
$event_image = $event_arr['conttoken']['event_image'] ?: TAOH_SITE_URL_ROOT.'/assets/images/event.jpg';

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

$show_rsvp_confirmation = ($_GET['confirmation'] ?? '') === 'rsvp';
$show_rsvp_ticket = ($_GET['confirmation'] ?? '') === 'rsvp_ticket';
$show_upgrade = ($_GET['upgrade'] ?? '') === 'from_email';
$rsvp_ticket_token = $_GET['tickettoken'] ?? '';

$event_detail_url = TAOH_EVENTS_URL.'/d/'.slugify2($event_title).'-'.$eventtoken;
$event_detail_url_param = '';
if(in_array($_GET['confirmation'] ?? '', ['rsvp', 'rsvp_ticket']) && !empty($_GET['tickettoken'])){
    $event_detail_url_param = '?tickettoken=' . $rsvp_ticket_token . '&confirmation=rsvp_ticket';
}

if (!$taoh_user_is_logged_in) {
    $event_detail_url = $event_detail_url . $event_detail_url_param;
    taoh_redirect($event_detail_url);
    exit();
}

$event_organizer_banner = [];

$cache_name = 'event_MetaInfo_' . $eventtoken . '__'; // type & search empty for this call
$taoh_vals = [
    'mod' => 'events',
    'token' => taoh_get_api_token(1, 1),
    'eventtoken' => $eventtoken,
    'cfcc5h' => 1,
    'cache_name' => $cache_name,
];
$get_event_meta_info_arr = taoh_get_array(taoh_apicall_get('events.content.get', $taoh_vals));
if (in_array($get_event_meta_info_arr['success'], [true, 'true']) && !empty($get_event_meta_info_arr['output'])) {
    $event_meta_info = $get_event_meta_info_arr['output'];

    $event_organizer_banner = $event_meta_info['event_organizer_banner'][0] ?? [];
}

define( 'TAO_PAGE_AUTHOR', 'Event Organizer' );
define( 'TAO_PAGE_DESCRIPTION', $event_short );
define( 'TAO_PAGE_IMAGE', $event_image);
define( 'TAO_PAGE_TITLE', $event_title );
define( 'TAO_PAGE_ROBOT', 'index, follow' );
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', TAOH_SITE_NAME_SLUG." Virtual job fair, ".TAOH_SITE_NAME_SLUG." Online career fair, ".TAOH_SITE_NAME_SLUG." Job fair event, ".TAOH_SITE_NAME_SLUG." Virtual networking opportunities, ".TAOH_SITE_NAME_SLUG." Remote job opportunities, ".TAOH_SITE_NAME_SLUG." Connecting talent and employers, ".TAOH_SITE_NAME_SLUG." Career advancement fair, ".TAOH_SITE_NAME_SLUG." Industry-specific job fair, ".TAOH_SITE_NAME_SLUG." Virtual recruitment event,".TAOH_SITE_NAME_SLUG." Professional networking event, ".TAOH_SITE_NAME_SLUG." Talent showcase platform, ".TAOH_SITE_NAME_SLUG." Online hiring event, ".TAOH_SITE_NAME_SLUG." Remote job fair, ".TAOH_SITE_NAME_SLUG." Job fair for job seekers, ".TAOH_SITE_NAME_SLUG." Virtual career fair, ".TAOH_SITE_NAME_SLUG." Job fair networking, ".TAOH_SITE_NAME_SLUG." Online job search event, ".TAOH_SITE_NAME_SLUG." Talent acquisition fair, ".TAOH_SITE_NAME_SLUG." Virtual job fair platform" ); }
$additive = '';
if(!empty($site_info['source']) && TAOH_SITE_URL_ROOT != $site_info['source']){
    $canonical_url = $site_info['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken;
    $additive = '<link rel="canonical" href="'.$canonical_url.'"/> 
	<meta name="original-source" content="'.$canonical_url.'"/>';
}
define ( 'TAO_PAGE_CANONICAL', $additive );

$taoh_vals = [
    'ops' => 'status', 'mod' => 'events',
    'token' => TAOH_API_TOKEN, 'eventtoken' => $eventtoken,
    'cache_required' => 0,
];
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
    $share_link = removePathSegment($sharerlink,6);
}else{
    $share_link = $event_detail_url;
}

if($ref_slug != '' && $ref_slug != 'stlo'){
    $share_link = removePathSegment($share_link,6);
}else{
    $share_link = $event_detail_url;
    $ref_slug = '';
}

$original_link = $share_link;
$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){
    $trackingtoken = hash('sha256',(string)$ptoken);
    $share_link =  addPathSegment($share_link,'stlo',$trackingtoken);
}

$social_token = '';
if(!empty($ref_param) && $ref_param != 'stlo'){
    $hashptoken =  hash('sha256',(string)$ptoken); 
    if($ptoken !== '' && $hashptoken === (string)$ref_param) $social_token = $ref_param;
}

$success_discount_amt = '';
$success_sponsor_title = '';
$success_redirect = '';
$discount_amt = 0;

if(!empty($ref_slug) && $ref_slug != 'stlo'){
    $ticketarr = array_column($ticket_types, 'social_sharing_discount', 'title');
    foreach($sponsor_levels as $value){
        if($value['slug'] == $ref_slug){
            $discount_amt = $ticketarr[$value['award_ticket_type']] ?? 0;
            if( $discount_amt > 0 ){
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

$taoh_vals = [
    'ops' => 'info', 'mod' => 'events',
    'token' => taoh_get_dummy_token(), 'rsvptoken' => $rsvp_token,
];
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

$event_arr['conttoken']['locality'] = $event_arr['conttoken']['locality'] ?: 0;
$live_state = event_live_state($event_arr['utc_start_at'], $event_arr['utc_end_at'], $event_arr['status'], $event_arr['conttoken']['locality']);
$event_type = strtolower($events_data['event_type'] ?? 'virtual');

$live_btn_as_link = ($event_type === 'virtual' || $event_type === 'hybrid');

// Utility functions (find_title_slug, edit_prefill, string_to_id) now in functions.php

$enable_chat_room = $events_data['enable_chat_room'] ?? 1;

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


$event_locality = $event_arr['conttoken']['locality'] ?: 0;
$event_timezone = $event_arr['local_timezone'];

$adopter_url = TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/club/' . taoh_slugify($events_data['title']) . '-' . $event_arr['eventtoken'];

if ($live_state == 'live') {
    $event_live_link = ($chat_room_status == 2 && filter_var($events_data['external_link'] ?? '', FILTER_VALIDATE_URL))
        ? $events_data['external_link']
        : $adopter_url;
}

$is_event_suspended = ($event_arr['status'] ?? null) == 2;
$is_event_freeze = ($events_data['freeze_option'] ?? 0) == 1;
define('TAO_CURRENT_APP_INNER_PAGE', 'events_lobby');
taoh_get_header();
$GLOBALS['show_events_css'] = true;

require_once TAOH_APP_PATH . '/events/event_health_check.php';
?>
<!-- External CSS for chat/lobby page -->
<link rel="stylesheet" href="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/css/chat.css?v=<?= TAOH_CSS_JS_VERSION; ?>">

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
                                    <?= icon('check-circle', '#333333', 62) ?>

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

                                <!-- celebration confetti - loaded from external SVG -->
                                <img src="<?= TAOH_SITE_URL_ROOT ?>/assets/icons/svg/celebration-confetti.svg" alt="" style="position: absolute; right: 0; bottom: 0; width: 200px; height: auto; pointer-events: none;" loading="lazy">

                            </div>
                        </div>
                    </div>

                <?php } else { ?>
                    <div class="pb-3" style="border-bottom : 2px solid #D3D3D3; min-height: 71px; gap: 12px;">
                        <div class="event-status">
                            <div class="event-success px-3 py-5 px-lg-5" style="position: relative;">
                                <div class="d-flex align-items-center py-2 flex-column flex-md-row" style="gap: 2rem;">
                                    <?= icon('check-circle', '#333333', 62) ?>

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
                                        <?= taoh_recent_events_full_display($eventtoken); ?>
                                    </div>
                                    <p class="text-right text-underline mt-2 pr-2"><a href="<?= TAOH_SITE_URL_ROOT.'/events'; ?>">View all Events <i class="la la-arrow-right ml-1"></i></a></p>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php } ?>
            <?php } ?>

            <div id="stickySentinel" style="height:1px;margin:0;padding:0;"></div>
            <div class="sticky-top sticky-top-fixed light-dark" style="z-index: 10;">
                <div class="max-w container pl-0 pr-0 pt-3 pb-2" id="event_info_container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3 mb-3" role="tablist" 
                        style="background:none;line-height: 1.143;">
                        <li class="nav-item" >
                            <a href="<?= TAOH_SITE_URL_ROOT.'/'; ?>">Home</a>
                            <?= icon('chevron-right', '#000000', 19) ?>
                        </li>
                        <li class="nav-item" >
                            <a href="<?= TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG; ?>">Events</a>
                            <?= icon('chevron-right', '#000000', 19) ?>
                        </li>
                        <li class="nav-item event_title">

                        </li>
                    </ul>
                    <div class="event-det-block1 d-flex align-items-lg-center flex-column flex-lg-row pb-3 pt-3" style="gap: 9px;">
                        <div class="d-none d-lg-block" id="event_top_banner" style="position: relative;">
                            <div class="d-none" id="event_banner_container" style="display: none;">

                            </div>

                        </div>


                        <div class="d-flex flex-wrap flex-xl-nowrap align-items-center" style="gap: 8px; flex: 1;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1 event-title mr-2 line-clamp-2 event_title"></h5>
                                <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                                    <?= icon('calendar-check', '#2557A7', 22) ?>
                                    <p class="e-v2-info"><span id="event_start_end_datetime" class="event-day"></span></p>


                                </div>
                                <div class="d-flex align-items-center mb-1 mobile-none" style="gap: 8px;">
                                    <?= icon('globe', '#2557A7', 22) ?>
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
                            <div class="flex-shrink-lg-0 ticket-card-div d-flex flex-row flex-wrap flex-xl-column events-btns-outer-block <?= $class; ?>" style="gap: 6px;">
                                
                            <?php if($live_state != 'live') { ?>
                                <a href="<?= $adopter_url; ?>" id="networking_link" style="display:none;"  class="btn btn-success">Go to Networking Room</a>
                            <?php } ?>
                                
                                <div class="event_status_button d-flex flex-wrap flex-xl-column" style="gap: 6px;">
                                    <input type="hidden" name="chat_room_status" id="chat_room_status" value="<?= $chat_room_status; ?>"/>
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
                                            echo $event_live_link && $chat_room_status ? '<a href="' . $event_live_link . '" class="btn live d-flex align-items-center cursor-pointer px-3 metrics_action " style="gap: 12px;" data-metrics="events_join">' : '<span class="btn live d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">';
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
                                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live"  value="1"/>
                                                <input type="hidden" name="event_live_link_hidden" id="event_live_link_hidden" value="' . $event_live_link . '"/>';
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
                                            <span> Event Not Live!</span>
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
                                <input type="hidden" name="rsvp_sponsor_title" id="rsvp_sponsor_title" value="<?= $current_ticket_type['title'] ?? ''; ?>">
                                <input type="hidden" name="exh_count" id="exh_count" value="0" >
                                <input type="hidden" name="spk_count" id="spk_count" value="0" >
                                <input type="hidden" name="event_live_state" id="event_live_state" value="0" >
                                <input type="hidden" name="enable_exhibitor_hall" id="enable_exhibitor_hall" value="" >
                                <input type="hidden" name="enable_speaker_hall" id="enable_speaker_hall" value="" >
                                <input type="hidden" name="enable_hall" id="enable_hall" value="" >

                                <input type="hidden" name="event_country_lock" id="event_country_lock" value="" >
                                <input type="hidden" name="event_country_name" id="event_country_name" value="" >


                                <input type="hidden" name="superorganizer_token" id="superorganizer_token" value="<?= TAOH_SUPER_ORGANIZER_TOKEN; ?>" >
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
                                                        <li class="dropdown-item"><a target="_blank" href="<?= 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='.str_replace("#","",$event_title).'&dates='.$calendarDate.'&details='.$eventDetails.'&location='.$eventLocation.'&sf=true&output=xml'; ?>">Google Calendar</a></li>

                                                        <?php
                                                        $eventLink = TAOH_SITE_URL_ROOT . '/events/d/' . taoh_slugify($events_data['title']) . '-' . $event_arr['eventtoken'];
                                                        $outlookBodyHtml = '<p>To RSVP and see the complete details click here:</p><p><a href="' . $eventLink . '">' . $eventLink . '</a></p>';
                                                        $outlookUrl = 'https://outlook.live.com/calendar/0/deeplink/compose?subject='.urlencode(str_replace("#","",$event_title)).'&body='.urlencode($outlookBodyHtml).'&startdt='.$start_outlook.'&enddt='.$end_outlook.'&location='.urlencode($eventLocation).'&path=%2Fcalendar%2Faction%2Fcompose&rru=addevent';
                                                        ?>
                                                        <li class="dropdown-item"><a target="_blank" href="<?= $outlookUrl; ?>">Outlook Calendar</a></li>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        <div class="edit-rsvp">

                                            <a title="Edit Rsvp" class="btn d-flex align-items-center text-nowrap" style="padding:8px"
                                            href="<?= TAOH_SITE_URL_ROOT . '/events/edit_rsvp/' . $event_arr['eventtoken'] . '/' . $current_ticket_type['title']; ?>" >
                                                <i class="fa-solid fa-edit" style="font-size: 24px; margin: auto;"></i>
                                            </a>
                                        </div>
                                        <div id="share_event_slot">
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
                                    <?= icon('award', '#2A4E96', 18) ?>
                                    <span>Sponsors</span>
                                </h3>
                                <input id="sponsor_type1" name="sponsor_type1" type="hidden" value=""/>
                                <div class="sponsor_edit"></div>
                                <div class="event_sponsor_right_header">
                                <a href="<?= TAOH_SITE_URL_ROOT.'/events/event_sponsor/'.$eventtoken; ?>"
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
                            <i class="fa-solid fa-headset" style="font-size: 28px; color: #fff;"></i>
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

<script src="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/js/event.js?v=<?= TAOH_CSS_JS_VERSION; ?>"></script>
<script>
    // ============================================
    // Chat Page Configuration (PHP-generated)
    // All PHP variables passed to JavaScript here
    // ============================================
    window.chatConfig = {
        // User state
        isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
        isValidUser: <?= json_encode($valid_user); ?>,
        profileType: <?= json_encode($profile_type); ?>,
        userProfileType: '<?= $user_info_obj->type ?? ''; ?>',
        ptoken: '<?= $ptoken ?? ''; ?>',
        myLocalTimezone: '<?= $my_local_timezone ?? ''; ?>',
        myEmail: '<?= $user_info_obj->email ?? ''; ?>',
        userTimezone: '<?= taoh_user_timezone(); ?>',

        // Event state
        eventToken: '<?= $eventtoken ?? ''; ?>',
        eventArr: <?= json_encode($event_arr); ?>,
        liveState: '<?= $live_state ?? ''; ?>',
        isEventFreeze: '<?= $is_event_freeze ?? ''; ?>',

        // RSVP state
        isUserRsvpDone: <?= json_encode($is_user_rsvp_done); ?>,
        showRsvpTicket: <?= json_encode($show_rsvp_ticket); ?>,
        showUpgrade: <?= json_encode($show_upgrade); ?>,
        rsvpTicketToken: '<?= $rsvp_ticket_token ?? ''; ?>',
        rsvpSlug: '<?= $rsvp_slug ?? ''; ?>',
        rsvpToken: '<?= $rsvp_token ?? ''; ?>',
        currentTicketType: <?= json_encode($current_ticket_type ?? []); ?>,

        // Feature flags
        isSponsorEnable: <?= json_encode($is_sponsor_enable); ?>,
        isExhibitorEnable: <?= json_encode($is_exhibitor_enable); ?>,
        isSpeakerEnable: <?= json_encode($is_speaker_enable); ?>,
        isHallEnable: <?= json_encode($is_hall_enable); ?>,

        // URLs
        appUrl: '<?= TAOH_CURR_APP_URL; ?>',
        ajaxUrl: '<?= taoh_site_ajax_url(); ?>',
        adopterUrl: '<?= $adopter_url ?? ''; ?>',
        originalLink: '<?= $original_link ?? ''; ?>',

        // Misc
        clickView: '<?= $click_view ?? 'view'; ?>',
        refSlug: '<?= $ref_slug ?? ''; ?>',
        successDiscountAmt: '<?= $success_discount_amt ?? ''; ?>',
        trackingtoken: '<?= $trackingtoken ?? ''; ?>',
        socialToken: '<?= $social_token ?? ''; ?>',
        eventOrganizerBanners: <?= json_encode($event_organizer_banner ?? []); ?>,
        dojoEventRules: <?= json_encode(DOJO_EVENT_DETAIL_MESSAGE); ?>,

        // Dojo suggestions
        dojoSuggestionEnable: <?= json_encode(TAOH_DOJO_SUGGESTION_ENABLE); ?>,
        dojoSuggestionTimeLimit: <?= json_encode((int)(TAOH_DOJO_SUGGESTION_TIMELIMIT ?? 300000)); ?>,

        // UI thresholds
        stickyTopThreshold: <?php
            if ($show_rsvp_confirmation && $live_state != 'live') echo 765;
            else if ($show_rsvp_confirmation) echo 300;
            else echo 126;
        ?>
           };

    // Legacy global variables for backward compatibility
    const isLoggedIn = window.chatConfig.isLoggedIn;
    const isValidUser = window.chatConfig.isValidUser;
    const profileType = window.chatConfig.profileType;
    const is_sponsor_enable = window.chatConfig.isSponsorEnable;
    const is_exhibitor_enable = window.chatConfig.isExhibitorEnable;
    const is_speaker_enable = window.chatConfig.isSpeakerEnable;
    const is_hall_enable = window.chatConfig.isHallEnable;
    const user_profile_type = window.chatConfig.userProfileType;
    const is_rsvp = 0;
    const dojoeventrules = window.chatConfig.dojoEventRules;
    const my_pToken = window.chatConfig.ptoken;
    const my_local_timezone = window.chatConfig.myLocalTimezone;
    const my_email = window.chatConfig.myEmail;
    let eventToken = window.chatConfig.eventToken;
    let is_user_rsvp_done = window.chatConfig.isUserRsvpDone;
    let show_rsvp_ticket = window.chatConfig.showRsvpTicket;
    let show_upgrade = window.chatConfig.showUpgrade;
    let rsvp_ticket_token = window.chatConfig.rsvpTicketToken;
    let click_view = window.chatConfig.clickView;
    let TAOH_CURR_APP_URL = window.chatConfig.appUrl;
    let rsvp_slug = window.chatConfig.rsvpSlug;
    let rsvp_token = window.chatConfig.rsvpToken;
    let is_event_freeze = window.chatConfig.isEventFreeze;
    let live_state = window.chatConfig.liveState;
    let event_arr = window.chatConfig.eventArr;
    let ref_slug = window.chatConfig.refSlug;
    let success_discount_amt = window.chatConfig.successDiscountAmt;
    let trackingtoken = window.chatConfig.trackingtoken;
    let event_organizer_banners = window.chatConfig.eventOrganizerBanners;
    </script>
<!-- External chat helper functions -->
<script src="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/js/chat.js?v=<?= TAOH_CSS_JS_VERSION; ?>"></script>
<!-- External event processor -->
<script src="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/js/chat-event-processor.js?v=<?= TAOH_CSS_JS_VERSION; ?>"></script>
<?php
require_once('events_popup.php');

taoh_get_footer();
