<?php
include_once TAOH_SITE_PATH_ROOT.'/assets/icons/icons.php';
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;
$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$profile_type = ($taoh_user_is_logged_in && in_array($user_info_obj?->type ?? '', ['professional','employer','provider'], true))
    ? $user_info_obj->type : '';

$encodeCurrentUrl = encrypt_url_safe(getCurrentUrl());
$parse_url_2 = taoh_parse_url(2);
$eventtoken_expl = explode('-', $parse_url_2);
$eventtoken = array_pop($eventtoken_expl);
$to_page = '';
$footer_tracking_link = 'events_detail_'.$eventtoken;

if ( ! ctype_alnum( $eventtoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/invalid-eventtoken' );taoh_exit(); }
if(taoh_parse_url(3) ){
    $table_field = taoh_parse_url(3);
    $to_page = taoh_parse_url(4,0);
    if(taoh_parse_url(3) == 'stlo')  {
        $table_field = taoh_parse_url(4);
        $to_page = taoh_parse_url(5,0);
    }
    if($table_field != '' && $table_field!= 'stlo' && $table_field == 'tables'){

        taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG .'/tables?eventtoken='.$eventtoken.'&to_page='.$to_page);taoh_exit();
    }
}

$show_rsvp_ticket = ($_GET['confirmation'] ?? '') === 'rsvp_ticket';
$rsvp_ticket_token = $_GET['tickettoken'] ?? '';
$is_event_freeze = ($events_data['freeze_option'] ?? 0) == 1;

$sharerlink = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$click_view = !empty($_SERVER['HTTP_REFERER']) ? 'click' : 'view';
$ref_param =  taoh_parse_url(3);
$ref_slug = taoh_parse_url(4);

if($ref_param != '' && $ref_param != 'stlo'){
    if(isset($_GET['fbclid']) && $_GET['fbclid'] != '' && !taoh_user_is_logged_in() ){
        setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',getCurrentUrl(), strtotime( '+2 days' ), '/');
        header("Location: " . TAOH_SITE_URL_ROOT . "/login");
        taoh_exit();
    }
    $share_link = removePathSegment($sharerlink,4);
}else{
    $share_link = $sharerlink;
}
if($ref_slug != '' && $ref_slug != 'stlo'){
    $share_link = removePathSegment($share_link,4);
}else{
    $ref_slug = '';
}
$original_link = $share_link;
const TAO_PAGE_TYPE = 'events';
$app_data = taoh_app_info(TAO_PAGE_TYPE);
/*==========================================*/


if(($_GET['confirmation'] ?? '') == 'sponsor' || ($_GET['action_events'] ?? '') == 'events'){
    taoh_delete_local_cache('events', ['event_details_sponsor_'.$eventtoken, 'event_details_'.$eventtoken]);
}

$taoh_vals = [
    'token' => taoh_get_api_token(1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken
];
//$taoh_vals['debug'] = 2;echo taoh_apicall_get('events.event.get', $taoh_vals);die;
$result = taoh_apicall_get('events.event.get', $taoh_vals);
$response = taoh_get_array($result, true);
if (!$response['success']) {
    
    taoh_redirect(TAOH_EVENTS_URL);
    exit();
}
///assets/images/event.jpg


$event_arr = $response['output'];
$events_data = $event_arr['conttoken'] ?? [];
$ticket_types = $events_data['ticket_types'] ?? [];
$event_links = $events_data['link'] ?? [];
$event_faqs = $events_data['event_faq'] ?? [];
$source = $events_data['source'] ?? '';
$event_title = displayTaohFormatted($events_data['title']);
$is_exhibitor_enable = $events_data['enable_exhibitor_hall'] ?? 0 ;
$is_speaker_enable = $events_data['enable_speaker_hall'] ?? 0 ;
$is_sponsor_enable = isset($events_data['event_sponsor_levels']) ?  1 : 0;
$sponsor_levels = $events_data['event_sponsor_levels'] ?? [];
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


$event_organizer_banner = [];

$cache_name = 'event_MetaInfo_' . $eventtoken . '__'; // type & search empty for this call
$taoh_vals = [
    'mod' => 'events',
    'token' => taoh_get_api_token(1, 1),
    'eventtoken' => $eventtoken,
    'cfcc5h' => 1,
    'cache_name' => $cache_name,
];
$get_event_meta_info_response = taoh_apicall_get('events.content.get', $taoh_vals);
$get_event_meta_info_arr = taoh_get_array($get_event_meta_info_response);
if (in_array($get_event_meta_info_arr['success'], [true, 'true']) && !empty($get_event_meta_info_arr['output'])) {
    $event_meta_info = $get_event_meta_info_arr['output'];

    $event_organizer_banner = $event_meta_info['event_organizer_banner'][0] ?? [];
}


// TAO_PAGE_AUTHOR
define( 'TAO_PAGE_AUTHOR', !empty($events_data['source']) ? $events_data['source'] : 'Event Organizer' );
// TAO_PAGE_DESCRIPTION
define( 'TAO_PAGE_DESCRIPTION', $event_short) ;

// TAO_PAGE_IMAGE
define( 'TAO_PAGE_IMAGE', $event_image);
// TAO_PAGE_TITLE
define( 'TAO_PAGE_TITLE', $event_title );
// TAO_PAGE_TWITTER_SITE
// TAO_PAGE_ROBOT
define( 'TAO_PAGE_ROBOT', 'index, follow' );
if ( ! defined ( 'TAO_PAGE_KEYWORDS' ) ) { define ( 'TAO_PAGE_KEYWORDS', TAOH_SITE_NAME_SLUG." Virtual job fair, ".TAOH_SITE_NAME_SLUG." Online career fair, ".TAOH_SITE_NAME_SLUG." Job fair event, ".TAOH_SITE_NAME_SLUG." Virtual networking opportunities, ".TAOH_SITE_NAME_SLUG." Remote job opportunities, ".TAOH_SITE_NAME_SLUG." Connecting talent and employers, ".TAOH_SITE_NAME_SLUG." Career advancement fair, ".TAOH_SITE_NAME_SLUG." Industry-specific job fair, ".TAOH_SITE_NAME_SLUG." Virtual recruitment event,".TAOH_SITE_NAME_SLUG." Professional networking event, ".TAOH_SITE_NAME_SLUG." Talent showcase platform, ".TAOH_SITE_NAME_SLUG." Online hiring event, ".TAOH_SITE_NAME_SLUG." Remote job fair, ".TAOH_SITE_NAME_SLUG." Job fair for job seekers, ".TAOH_SITE_NAME_SLUG." Virtual career fair, ".TAOH_SITE_NAME_SLUG." Job fair networking, ".TAOH_SITE_NAME_SLUG." Online job search event, ".TAOH_SITE_NAME_SLUG." Talent acquisition fair, ".TAOH_SITE_NAME_SLUG." Virtual job fair platform" ); }
$additive = '';
if(!empty($site_info['source']) && TAOH_SITE_URL_ROOT != $site_info['source']){
    $canonical_url = $site_info['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken;
    $additive = '<link rel="canonical" href="'.$canonical_url.'"/> 
	<meta name="original-source" content="'.$canonical_url.'"/>';
	
   
}


$adopter_url = TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/club/' . taoh_slugify($event_title) . '-' . $eventtoken;


define ( 'TAO_PAGE_CANONICAL', $additive );
$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){
    $trackingtoken = hash('sha256',(string)$ptoken);
    
    $share_link =  addPathSegment($share_link,'stlo',$trackingtoken);
}

$social_token = '';
if(!empty($ref_param) && $ref_param != 'stlo'){
    $hashptoken =  hash('sha256',(string)$ptoken); 
    if ( $ptoken !== '' && $hashptoken === (string)$ref_param) {
        $social_token = $ref_param;
    }
}
$discount_info = ['amt' => '', 'title' => '', 'redirect' => ''];
if(!empty($ref_slug) && $ref_slug != 'stlo'){
    $ticketarr = array_column($ticket_types, 'social_sharing_discount', 'title');
    foreach($sponsor_levels as $value){
        if($value['slug'] == $ref_slug){
            $discount_amt = $ticketarr[$value['award_ticket_type']] ?? 0;
            if( $discount_amt > 0 ){
                $discount_info['amt'] = $discount_amt.'%';
                $discount_info['title'] = $value['title'];
                $discount_info['redirect'] = TAOH_SITE_URL_ROOT.'/events/event_sponsor/'.$eventtoken.'/'.$ref_slug.'/socialshare/'.$trackingtoken;
            }
        }
    }
}
$success_discount_amt = $discount_info['amt'];
$success_sponsor_title = $discount_info['title'];
$success_redirect = $discount_info['redirect'];
$GLOBALS['success_discount_amt'] = $success_discount_amt;
$GLOBALS['success_sponsor_title'] = $success_sponsor_title;
$GLOBALS['success_redirect'] = $success_redirect;

$taoh_vals = [
    'ops' => 'status', 'mod' => 'events',
    'token' => taoh_get_dummy_token(), 'eventtoken' => $eventtoken,
    'cache_required' => 0, 'time' => time(),
];
$response = taoh_get_array(taoh_apicall_get('events.rsvp.get', $taoh_vals));
$is_user_rsvp_done = is_array($response) ? ($response['success'] ?? false) : false;

if($is_user_rsvp_done){
    $share_chat_url = '';
    if(!empty($ref_param) && $ref_param != 'stlo') $share_chat_url = '/'.$ref_param;
    if(!empty($ref_slug) && $ref_slug != 'stlo') $share_chat_url .= '/'.$ref_slug;
    taoh_redirect(TAOH_EVENTS_URL.'/chat/id/events/'.$eventtoken.$share_chat_url.'/stlo');
    exit();
}
$is_user_paid = false;

$rsvp_slug = '';

$rsvp_data = [];
if ($is_user_rsvp_done) {
    $rsvp_slug = $response['output']['rsvp_slug'];
    $rsvp_token = $response['output']['rsvptoken'];

    $taoh_vals = [
        'ops' => 'info', 'mod' => 'events',
        'token' => taoh_get_dummy_token(), 'rsvptoken' => $rsvp_token,
        'cache_required' => 0, 'time' => time(),
    ];
    $response = taoh_get_array(taoh_apicall_get('events.rsvp.get', $taoh_vals));
    $rsvp_data = (array) ($response['output'] ?? []);

    $is_user_paid = !!($rsvp_data['success'] ?? false) && ($rsvp_data['payment_status'] ?? '0') == '1';
}

$taoh_vals = [
    'mod' => 'system', 'token' => taoh_get_dummy_token(),
    'conttoken' => $eventtoken, 'slug' => TAO_PAGE_TYPE,
    'cache_name' => 'events_save_' . taoh_get_dummy_token() . '_' . $eventtoken,
];
$get_liked = taoh_get_array(taoh_apicall_get('system.users.metrics', $taoh_vals));
$userliked_already = ($get_liked['success'] ?? false) === true ? ($get_liked['output']['userliked'] ?? '0') : '';
define('TAO_CURRENT_APP_INNER_PAGE', 'events_details');

// JSON-LD Event structured data for SEO/AEO
$jsonld_event = array(
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event_title,
    'description' => $event_description_clean,
    'image' => $event_image,
    'url' => $original_link,
);
if (!empty($event_arr['utc_start_at'])) {
    $jsonld_event['startDate'] = date('Y-m-d\TH:i:sP', strtotime($event_arr['utc_start_at']));
}
if (!empty($event_arr['utc_end_at'])) {
    $jsonld_event['endDate'] = date('Y-m-d\TH:i:sP', strtotime($event_arr['utc_end_at']));
}
$event_type_val = strtolower($events_data['event_type'] ?? 'virtual');
if ($event_type_val === 'virtual') {
    $jsonld_event['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
    $jsonld_event['location'] = array(
        '@type' => 'VirtualLocation',
        'url' => $original_link,
    );
} elseif ($event_type_val === 'in-person') {
    $jsonld_event['eventAttendanceMode'] = 'https://schema.org/OfflineEventAttendanceMode';
    if (!empty($events_data['venue'])) {
        $jsonld_event['location'] = array(
            '@type' => 'Place',
            'name' => $events_data['venue'],
        );
    }
} elseif ($event_type_val === 'hybrid') {
    $jsonld_event['eventAttendanceMode'] = 'https://schema.org/MixedEventAttendanceMode';
    $locations = array();
    $locations[] = array('@type' => 'VirtualLocation', 'url' => $original_link);
    if (!empty($events_data['venue'])) {
        $locations[] = array('@type' => 'Place', 'name' => $events_data['venue']);
    }
    $jsonld_event['location'] = $locations;
}
if (!empty($events_data['source'])) {
    $jsonld_event['organizer'] = array(
        '@type' => 'Organization',
        'name' => $events_data['source'],
        'url' => $events_data['source'],
    );
}
$additive .= '<script type="application/ld+json">' . json_encode($jsonld_event, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';

taoh_get_header($additive);

require_once TAOH_APP_PATH . '/events/event_health_check.php';
?>
<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-detail.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
    
    <input type="hidden" id="share_link" value="<?= $share_link ?>">
    <div class="new-event-detail event-new-flow py-3 py-lg-5 aw aw-logo aw-loader">


    <!-- new design start -->

    <div class="max-w container">
        
           <div id="event_gallery_carousel" class="carousel slide" data-ride="carousel" data-bs-ride="carousel" >
                <div id="event_banner_container" class="carousel-inner p-0">


                </div>
                <!-- Left arrow -->
                <a class="carousel-control-prev" href="#event_gallery_carousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>

                <!-- Right arrow -->
                <a class="carousel-control-next" href="#event_gallery_carousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>


                <div class="action-btns d-flex align-items-center mb-2" style="gap: 8px;">
                    <button class="light-dark-card btn  save-btn events_like" id="event_like_btn">
                        
                    </button>
                    <button class="light-dark-card btn share-btn" data-toggle="modal" data-target="#shareModal">
                        <?= icon('share', '#6C727C', 22) ?>
                    </button>
                </div>

             </div>
    </div>
    
    <div id="stickySentinel" style="height:1px;margin:0;padding:0;"></div>
    <div class="sticky-top sticky-top-fixed py-3 light-dark px-lg-3 border-bottom" style="z-index: 10;">
        <div class="max-w container" id="event_info_container">

            <ul class="nav nav-tabs justify-content-left border-0 mt-3 mb-3" role="tablist" style="line-height: 1.143;">
                <li class="nav-item" >
                    <a href="<?= TAOH_SITE_URL_ROOT.'/'; ?>">Home</a>
                    <?= icon('chevron-right', '', 19) ?>
                </li>
                <li class="nav-item" >
                    <a href="<?= TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG; ?>">Events</a>
                    <?= icon('chevron-right', '', 19) ?>
                </li>
                <li class="nav-item event_title">
                    
                </li>                                
            </ul>
            
            <div class="d-flex align-items-start flex-column flex-lg-row" style="gap: 9px;">
                <div class="flex-grow-1">
                    <h5 class="e-v2-title mb-1 event_title"></h5>
                    <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                        <?= icon('calendar-check', '#2557A7', 22) ?>
<!--                        <p class="e-v2-info">Event DATE & TIME : <span id="event_start_datetime" class="event-day"></span> to <span id="event_end_datetime" class="event-day"></span></p>-->
                        <p class="e-v2-info"><span id="event_start_end_datetime" class="event-day"></span></p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <?= icon('globe', '#2557A7', 22) ?>
                        <p class="e-v2-info" id="event_venue_info"></p>
                    </div>
                </div>
                
                
                <div class="flex-shrink-lg-0 ticket-card-div d-flex flex-row flex-wrap flex-lg-column" style="gap: 9px;">   

                </div>
                
            
            </div>
        </div>
    </div>
    
    <!-- new design end  -->

        <div class="max-w container pb-2">
            <div class="light-dark"> <!-- sticky-xl-top -->
                <div class="row px-3 py-2 flex-column flex-md-row " style="gap: 6px; ">
                    <div class="" id="event_top_banner">
                        <div class="d-none" id="event_banner_container1" style="display: none;">

                        </div>
                    </div>
                    
                    <div class="row flex-lg-nowrap justify-content-between align-items-center px-3" style="flex: 1; gap: 6px;">
                        <h3 class="event-title" id="event_title1"></h3>
                        <div class="d-flex align-items-center mb-2" id="event_header_icons" style="gap: 8px;">
                            
                            
                        </div>
                    </div>
                    
                </div>


                <div class="mb-3">
                    <div class="row align-items-center">
                        <div class="col-12">
                        <input type="hidden" name="is_organizer" id="is_organizer" value="0" >
                        <input type="hidden" name="event_live_state" id="event_live_state" value="0" >
                        <input type="hidden" name="enable_exhibitor_hall" id="enable_exhibitor_hall" value="" >
                        <input type="hidden" name="enable_speaker_hall" id="enable_speaker_hall" value="" >

                        <input type="hidden" name="event_country_lock" id="event_country_lock" value="" >
                        <input type="hidden" name="event_country_name" id="event_country_name" value="" >
                        <input type="hidden" name="superorganizer_token" id="superorganizer_token" value="<?= TAOH_SUPER_ORGANIZER_TOKEN; ?>" >
                        <input id="sponsor_type" name="sponsor_type" data-sponsorid="" type="hidden" value=""/>
                        <input id="rsvp_perpage" name="rsvp_perpage" type="hidden" value="1"/>   
                        
                        
                        <input type="hidden" name="event_start_at" id="event_start_at" value=""> 
                        <input type="hidden" name="event_end_at" id="event_end_at" value="">
                        <input type="hidden" name="event_timezone" id="event_timezone" value="">
                        <input type="hidden" name="event_locality" id="event_locality" value="">
                            <div class="info-container p-3" id="event_info_container1" style="display:none;">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 16px;">
                                    <div class="info-card happens py-3 py-lg-4">
                                        <?= icon('calendar-check', '#2A4E96', 30) ?>
                                        <div>
                                            <p class="mb-2 info-title">Event Date and Time</p>
                                            <p id="event_start_datetime1"></p>
                                        </div>
                                    </div>
                                    <!-- <div class="info-card happens py-3">
                                        <?= icon('calendar-check', '#2A4E96', 30) ?>
                                        <div>
                                            <p class="mb-2 info-title">End Datetime</p>
                                            <p id="event_end_datetime"></p>
                                        </div>
                                    </div> -->
                                    <div class="info-card venue py-3 py-lg-4" id="event_venue_info1"></div>
                                    <div class="info-card py-3">
                                        <div class="ticket-cards p-0 border-0 shadow-none mx-auto" style="min-height: unset;">
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li class="nav-item flex-grow-1">
                                                    <a class="nav-link active d-flex align-items-center justify-content-center" id="buy-tickets-tab" data-toggle="tab" href="#buy_tickets" role="tab"
                                                        aria-controls="buy_tickets" aria-selected="true" style="gap: 6px;">
                                                        <?= icon('ticket', '#2557A7', 17) ?>
                                                        <span style="color: #2557A7;">Buy Tickets</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item flex-grow-1 event_sponsor_right_header" style="display: none;">
                                                    <a class="nav-link d-flex align-items-center justify-content-center sponsor-btn" id="become-sponsor-tab" data-toggle="tab" href="#become_sponsor" role="tab"
                                                        aria-controls="become_sponsor" aria-selected="false" style="gap: 6px;">
                                                        <?= icon('star-solid', '#2557A7', 16) ?>
                                                        <span style="color: #2557A7;">Become a Sponsor</span>
                                                    </a>
                                                    
                                                </li>
                                            </ul>
                                            <div class="ticket-card-div">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="event-description1" id="event_description1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">

                    <div class="events-hall container pt-3 pb-5 px-0" style="display:block;">
                        <?php 
                        $version = TAOH_CSS_JS_VERSION;
                        require_once('events_lobby_hall.php'); ?>
                    </div>

                </div>
                
            </div>
            <div class="row" style="display:none;">
                <div class="col-lg-4">
                    <?= taoh_sponsor_slider_widget($eventtoken); ?>
                </div>
            </div>
        </div>


        <div class="d-none">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#unlockDiscount">
                unlock discount
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upgradeExplorerPlus">
                Upgrade to Explorer Plus !
            </button>
        </div>

        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#continuePurchase">
            Thank you ! Continue your purchase
        </button> -->

    </div>


<div class="modal upgrade-v1-modal" id="unlockDiscount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header position-relative bg-white px-lg-4">
            <h5 class="modal-title" id="exampleModalLabel">Unlock <span>20% Discount</span></h5>
            <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
                <?= icon('close', '#555555', 12) ?>
            </button>
        </div>
        <div class="modal-body px-lg-4">
            <p>You chose for an Explorer Ticket. Get 20 % Discount Just by sharing the event and instantly unlock extra perks:</p>

            <ul class="svg-styled">
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Create your own discussion channels</span>
                </li>
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Priority access to raffles and swags</span>
                </li>
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Special Display Badge on Networking Strips and Profile</span>
                </li>
            </ul>

            <div class="d-flex align-items-center flex-wrap mt-4" style="gap: 12px;">
                <button class="btn std-btn" data-toggle="modal" data-target="#share" data-dismiss="modal" aria-label="Close">Share for 20% Off</button>
                <button class="btn bor-btn" data-dismiss="modal" aria-label="Close">Not Now</button>
            </div>
        </div>
        
        </div>
    </div>
</div>

<div class="modal upgrade-v1-modal" id="upgradeExplorerPlus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header position-relative bg-white px-lg-4">
            <h5 class="modal-title" id="exampleModalLabel">Upgrade to <span>Explorer Plus !</span></h5>
            <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
                <?= icon('close', '#555555', 12) ?>
            </button>
        </div>
        <div class="modal-body px-lg-4">
            <p>You chose for an Explorer Ticket. Upgrade to Explorer Plus for free — just share this event and instantly unlock extra perks:</p>

            <ul class="svg-styled">
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Create your own discussion channels</span>
                </li>
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Priority access to raffles and swags</span>
                </li>
                <li>
                    <?= icon('check', '#3DB057', 13) ?>
                    <span>Special Display Badge on Networking Strips and Profile</span>
                </li>
            </ul>

            <div class="d-flex align-items-center flex-wrap mt-4" style="gap: 12px;">
                <button class="btn std-btn" data-toggle="modal" data-target="#share" data-dismiss="modal" aria-label="Close">Share to Upgrade</button>
                <button class="btn bor-btn" data-dismiss="modal" aria-label="Close">Not Now</button>
            </div>
        </div>
        
        </div>
    </div>
</div>

<div class="modal upgrade-v1-modal" id="share" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header position-relative bg-white px-lg-4">
            <h5 class="modal-title" id="exampleModalLabel">Upgrade to <span>Explorer Plus !</span></h5>
            <button type="button" class="btn v1-modal-close-btn" data-dismiss="modal" aria-label="Close">
                <?= icon('close', '#555555', 12) ?>
            </button>
        </div>
        <div class="modal-body px-lg-4">
            <div class="my-38 d-flex align-items-center justify-content-center" style="gap: 16px;">
                <a href="">
                    <svg class="fb" width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="34" cy="34" r="34" fill="#365899"/>
                        <path d="M29.125 36.7062V50H36.375V36.7062H41.7812L42.9062 30.5938H36.375V28.4312C36.375 25.2 37.6438 23.9625 40.9188 23.9625C41.9375 23.9625 42.7563 23.9875 43.2313 24.0375V18.4938C42.3375 18.25 40.15 18 38.8875 18C32.2062 18 29.125 21.1562 29.125 27.9625V30.5938H25V36.7062H29.125Z" fill="white"/>
                    </svg>
                </a>

                <a href="">
                    <svg width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="34" cy="34" r="34" fill="black"/>
                        <path d="M42.65 22H47.0625L37.425 33.0125L48.7625 48H39.8875L32.9312 38.9125L24.9812 48H20.5625L30.8687 36.2188L20 22H29.1L35.3812 30.3062L42.65 22ZM41.1 45.3625H43.5438L27.7688 24.5H25.1438L41.1 45.3625Z" fill="white"/>
                    </svg>
                </a>

                <a href="">
                    <svg width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="34" cy="34" r="34" fill="#0A66C2"/>
                        <path d="M26.2675 47.9994H20.4625V29.3056H26.2675V47.9994ZM23.3619 26.7556C21.5056 26.7556 20 25.2181 20 23.3619C20 22.4703 20.3542 21.6151 20.9847 20.9847C21.6151 20.3542 22.4703 20 23.3619 20C24.2535 20 25.1086 20.3542 25.7391 20.9847C26.3696 21.6151 26.7238 22.4703 26.7238 23.3619C26.7238 25.2181 25.2175 26.7556 23.3619 26.7556ZM47.9937 47.9994H42.2013V38.8994C42.2013 36.7306 42.1575 33.9494 39.1831 33.9494C36.165 33.9494 35.7025 36.3056 35.7025 38.7431V47.9994H29.9038V29.3056H35.4712V31.8556H35.5525C36.3275 30.3869 38.2206 28.8369 41.045 28.8369C46.92 28.8369 48 32.7056 48 37.7306V47.9994H47.9937Z" fill="white"/>
                    </svg>
                </a>
            </div>

            <p class="sh-text">Share the post on social media, then click the link on the shared post and return here to claim your benefit.</p>
        </div>
        
        </div>
    </div>
</div>


<?php
require_once('events_popup.php');

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

    <div class="modal fade sponsorship-option" id="sponsorInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


    </div>

    <script src="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/js/event.js?v=<?= TAOH_CSS_JS_VERSION; ?>"></script>
    <script type="text/javascript">
        window.eventDetailConfig = {
            // User state
            isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
            isValidUser: <?= json_encode($valid_user); ?>,
            profileType: <?= json_encode($profile_type); ?>,
            userProfileType: '<?= $user_info_obj->type ?? ''; ?>',
            ptoken: '<?= $ptoken ?? ''; ?>',
            ptokenOrDummy: '<?= $ptoken ?? TAOH_API_TOKEN_DUMMY; ?>',
            userTimezone: '<?= taoh_user_timezone(); ?>',

            // Event state
            eventToken: '<?= $eventtoken ?? ''; ?>',
            encodeCurrentUrl: '<?= $encodeCurrentUrl ?? ''; ?>',

            // RSVP state
            isUserRsvpDone: <?= json_encode($is_user_rsvp_done); ?>,
            showRsvpTicket: <?= json_encode($show_rsvp_ticket); ?>,
            rsvpTicketToken: '<?= $rsvp_ticket_token ?? ''; ?>',
            rsvpSlug: '<?= $rsvp_slug ?? ''; ?>',

            // Feature flags
            isSponsorEnable: <?= json_encode($is_sponsor_enable); ?>,
            isExhibitorEnable: <?= json_encode($is_exhibitor_enable); ?>,
            isSpeakerEnable: <?= json_encode($is_speaker_enable); ?>,

            // URLs
            appUrl: '<?= TAOH_CURR_APP_URL; ?>',
            adopterUrl: '<?= $adopter_url ?? ''; ?>',
            originalLink: '<?= $original_link ?? ''; ?>',
            bookmarkFillSrc: '<?= TAOH_SITE_URL_ROOT; ?>/assets/images/bookmark-fill.svg',

            // Misc
            clickView: '<?= $click_view ?? 'view'; ?>',
            refSlug: '<?= $ref_slug ?? ''; ?>',
            successDiscountAmt: '<?= $success_discount_amt ?? ''; ?>',
            trackingtoken: '<?= $trackingtoken ?? ''; ?>',
            socialShareStatus: '<?= $social_token ?? ''; ?>',
            userlikedAlready: '<?= $userliked_already ?? '0'; ?>',
            dojoEventRules: <?= json_encode(DOJO_EVENT_DETAIL_MESSAGE); ?>,

            // Dojo suggestions
            dojoSuggestionEnable: <?= json_encode(TAOH_DOJO_SUGGESTION_ENABLE); ?>,
            dojoSuggestionTimeLimit: <?= json_encode((int)(TAOH_DOJO_SUGGESTION_TIMELIMIT ?? 300000)); ?>
                };

        // Legacy aliases for external scripts (event.js)
        const isLoggedIn = window.eventDetailConfig.isLoggedIn;
        const isValidUser = window.eventDetailConfig.isValidUser;
        const profileType = window.eventDetailConfig.profileType;
        const is_sponsor_enable = window.eventDetailConfig.isSponsorEnable;
        const is_exhibitor_enable = window.eventDetailConfig.isExhibitorEnable;
        const is_speaker_enable = window.eventDetailConfig.isSpeakerEnable;
        const user_profile_type = window.eventDetailConfig.userProfileType;
        const dojoeventrules = window.eventDetailConfig.dojoEventRules;
        let eventToken = window.eventDetailConfig.eventToken;
        let encodeCurrentUrl = window.eventDetailConfig.encodeCurrentUrl;
        let is_user_rsvp_done = window.eventDetailConfig.isUserRsvpDone;
        const is_rsvp = is_user_rsvp_done;
        let show_rsvp_ticket = window.eventDetailConfig.showRsvpTicket;
        let rsvp_ticket_token = window.eventDetailConfig.rsvpTicketToken;
        let click_view = window.eventDetailConfig.clickView;
        let TAOH_CURR_APP_URL = window.eventDetailConfig.appUrl;
        let rsvp_slug = window.eventDetailConfig.rsvpSlug;
        const my_pToken = window.eventDetailConfig.ptoken;
        let ref_slug = window.eventDetailConfig.refSlug;
        let success_discount_amt = window.eventDetailConfig.successDiscountAmt;
        let social_share_status = window.eventDetailConfig.socialShareStatus;
        let trackingtoken = window.eventDetailConfig.trackingtoken;
        let userliked_already = window.eventDetailConfig.userlikedAlready;
    </script>
    <script src="<?= TAOH_SITE_URL_ROOT; ?>/assets/events/js/event-detail-processor.js?v=<?= TAOH_CSS_JS_VERSION; ?>"></script>


<?php
taoh_get_footer();