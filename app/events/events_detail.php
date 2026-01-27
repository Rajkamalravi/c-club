<?php
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

$show_rsvp_ticket = isset($_GET['confirmation']) && $_GET['confirmation'] === 'rsvp_ticket';
$rsvp_ticket_token = !empty($_GET['tickettoken']) ? $_GET['tickettoken'] : '';
$is_event_freeze = isset($events_data['freeze_option']) && $events_data['freeze_option'] == 1;

$sharerlink = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$click_view = (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) ? 'click' : 'view';
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


if(isset( $_GET['confirmation']) && $_GET['confirmation'] == 'sponsor'){

    $remove_array = array('event_details_sponsor_'.$eventtoken,'event_details_'.$eventtoken);
    taoh_delete_local_cache('events',$remove_array);
}

if(isset( $_GET['action_events']) && $_GET['action_events'] == 'events'){
    $remove_array = array('event_details_sponsor_'.$eventtoken,'event_details_'.$eventtoken);
    taoh_delete_local_cache('events',$remove_array);
}

$taoh_vals = array(
    'token' => taoh_get_api_token(1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken
);
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
$get_event_meta_info_arr = taoh_get_array($get_event_meta_info_response);
if (in_array($get_event_meta_info_arr['success'], [true, 'true']) && !empty($get_event_meta_info_arr['output'])) {
    $event_meta_info = $get_event_meta_info_arr['output'];

    $event_organizer_banner = $event_meta_info['event_organizer_banner'][0] ?? [];
}


// TAO_PAGE_AUTHOR
define( 'TAO_PAGE_AUTHOR', 'Event Organizer' );
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
if(isset($site_info['source']) && $site_info['source'] !='' && TAOH_SITE_URL_ROOT != $site_info['source']){
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
if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {
    
    $hashptoken =  hash('sha256',(string)$ptoken); 
    if ( $ptoken !== '' && $hashptoken === (string)$ref_param) {
        
        $social_token = $ref_param;
    }
   
    
}
$discount_info = ['amt' => '', 'title' => '', 'redirect' => ''];
if(isset($ref_slug) && $ref_slug != '' && $ref_slug != 'stlo'){
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

// Check RSVP status
$taoh_vals = [
    'ops' => 'status',
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $eventtoken,
    'cache_required' => 0,
    'time' => time(),
];
$response = taoh_get_array(taoh_apicall_get('events.rsvp.get', $taoh_vals));
$is_user_rsvp_done = is_array($response) ? ($response['success'] ?? false) : false;

if($is_user_rsvp_done){
    $share_chat_url = '';
    if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {
        $share_chat_url = '/'.$ref_param;
    }
    if(isset($ref_slug) && $ref_slug != '' && $ref_slug != 'stlo'){
        $share_chat_url .= '/'.$ref_slug;
    }
    taoh_redirect(TAOH_EVENTS_URL.'/chat/id/events/'.$eventtoken.$share_chat_url.'/stlo');
    exit();
}
$is_user_paid = false;

$rsvp_slug = '';

$rsvp_data = [];
if ($is_user_rsvp_done) {
    $rsvp_slug = $response['output']['rsvp_slug'];
    $rsvp_token = $response['output']['rsvptoken'];

    // Fetch RSVP information
    $taoh_vals = [
        'ops' => 'info',
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'rsvptoken' => $rsvp_token,
        'cache_required' => 0,
        'time' => time(),
    ];
    $response = taoh_get_array(taoh_apicall_get('events.rsvp.get', $taoh_vals));
    $rsvp_data = (array) ($response['output'] ?? []);

    $is_user_paid = !!($rsvp_data['success'] ?? false) && ($rsvp_data['payment_status'] ?? '0') == '1';
}

/* check liked or not */
$taoh_vals = array(
    'mod' => 'system',
    'token' => taoh_get_dummy_token(),
    'conttoken' => $eventtoken,
    'slug' => TAO_PAGE_TYPE,
    'cache_name' => 'events_save_' . taoh_get_dummy_token() . '_' . $eventtoken,
);
$get_liked = taoh_get_array(taoh_apicall_get('system.users.metrics', $taoh_vals));
$userliked_already = '';
if(isset($get_liked['success']) && $get_liked['success'] === true) {
    $userliked_already = $get_liked['output']['userliked'] ?? '0';
}
define('TAO_CURRENT_APP_INNER_PAGE', 'events_details');
taoh_get_header();

require_once TAOH_APP_PATH . '/events/event_health_check.php';
?>
    <style>
        #choose_ticket {
            overflow: hidden;
        }
        
    .sticky-top-fixed{
        top: -50px;
    }
    </style>
    
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
                        <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.0857 9.625H13.0929C11.1643 9.625 9.6 11.1934 9.6 13.127C9.6 14.0852 10.0414 14.6008 10.4229 14.8672C10.7143 15.0691 10.9714 15.3828 10.9714 15.7395C10.9714 16.1605 10.6286 16.5043 10.2086 16.5043H10.1014C9.99857 16.5043 9.89571 16.4871 9.79714 16.4441C9.03429 16.1047 5.48571 14.3258 5.48571 10.3125C5.48571 6.89648 8.25 4.125 11.6571 4.125H15.0857V1.49102C15.0857 0.666016 15.75 0 16.5729 0C16.9414 0 17.2929 0.1375 17.5671 0.382422L23.49 5.72773C23.8157 6.01992 24 6.43672 24 6.875C24 7.31328 23.8157 7.73008 23.49 8.02227L17.5329 13.3977C17.28 13.6254 16.9543 13.75 16.6157 13.75H16.4571C15.6986 13.75 15.0857 13.1355 15.0857 12.375V9.625ZM3.42857 4.125C3.05143 4.125 2.74286 4.43437 2.74286 4.8125V18.5625C2.74286 18.9406 3.05143 19.25 3.42857 19.25H17.1429C17.52 19.25 17.8286 18.9406 17.8286 18.5625V16.5C17.8286 15.7395 18.4414 15.125 19.2 15.125C19.9586 15.125 20.5714 15.7395 20.5714 16.5V18.5625C20.5714 20.4617 19.0371 22 17.1429 22H3.42857C1.53429 22 0 20.4617 0 18.5625V4.8125C0 2.91328 1.53429 1.375 3.42857 1.375H5.48571C6.24429 1.375 6.85714 1.98945 6.85714 2.75C6.85714 3.51055 6.24429 4.125 5.48571 4.125H3.42857Z" fill="#6C727C"/>
                        </svg>
                    </button>
                </div>

             </div>
    </div>
    


    <div class="sticky-top sticky-top-fixed py-3 light-dark px-lg-3 border-bottom" style="z-index: 10;">
        <div class="max-w container" id="event_info_container">

            <ul class="nav nav-tabs justify-content-left border-0 mt-3 mb-3" role="tablist" style="line-height: 1.143;">
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
            
            <div class="d-flex align-items-start flex-column flex-lg-row" style="gap: 9px;">
                <div class="flex-grow-1">
                    <h5 class="e-v2-title mb-1 event_title"></h5>
                    <div class="d-flex align-items-center mb-1" style="gap: 8px;">
                        <svg style="min-width: fit-content;" width="22" height="22" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"/>
                        </svg>
<!--                        <p class="e-v2-info">Event DATE & TIME : <span id="event_start_datetime" class="event-day"></span> to <span id="event_end_datetime" class="event-day"></span></p>-->
                        <p class="e-v2-info"><span id="event_start_end_datetime" class="event-day"></span></p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <svg style="min-width: fit-content;" width="22" height="22" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5 25C15.8152 25 18.9946 23.683 21.3388 21.3388C23.683 18.9946 25 15.8152 25 12.5C25 9.18479 23.683 6.00537 21.3388 3.66117C18.9946 1.31696 15.8152 0 12.5 0C9.18479 0 6.00537 1.31696 3.66117 3.66117C1.31696 6.00537 0 9.18479 0 12.5C0 15.8152 1.31696 18.9946 3.66117 21.3388C6.00537 23.683 9.18479 25 12.5 25ZM10.1855 17.4463L8.28125 16.7871C7.96387 16.6797 7.61719 16.6748 7.2998 16.7725L6.55273 17.0117C5.64941 17.2998 4.67285 16.8945 4.2334 16.0596L4.07227 15.7568C3.55469 14.7754 3.95996 13.5596 4.96094 13.0859L6.68457 12.2656C6.79687 12.2119 6.89941 12.1289 6.97266 12.0312L7.23145 11.6895C7.58301 11.2207 8.13965 10.9424 8.72559 10.9424C9.31152 10.9424 9.86816 11.2207 10.2197 11.6895L10.4443 11.9873C10.542 12.1143 10.6836 12.207 10.8398 12.2363C11.2207 12.3145 11.6064 12.1631 11.8359 11.8506L12.3438 11.1572C12.4414 11.0205 12.6025 10.9424 12.7686 10.9424C12.9834 10.9424 13.1787 11.0742 13.2568 11.2744L13.75 12.5391C13.8867 12.8906 14.0771 13.2227 14.3115 13.5254L15.1855 14.6387C15.4688 15 15.625 15.4492 15.625 15.9082C15.625 16.3672 15.4688 16.8164 15.1855 17.1777L14.5996 17.9297C14.1943 18.4473 13.5742 18.75 12.9199 18.75C12.5098 18.75 12.1094 18.6328 11.7627 18.4082L10.5225 17.6074C10.415 17.5391 10.3027 17.4854 10.1855 17.4414V17.4463ZM13.3691 6.95801L14.4531 8.04199C14.9463 8.53516 14.5947 9.375 13.9014 9.375H12.4414C12.168 9.375 11.8994 9.31641 11.6504 9.20898L9.56055 8.28125C8.8623 7.97363 8.97949 6.94824 9.72656 6.80176L11.6064 6.42578C12.2461 6.29883 12.9102 6.49902 13.3691 6.95801ZM12.1094 21.0938C12.1094 20.6641 12.4609 20.3125 12.8906 20.3125H13.6719C14.1016 20.3125 14.4531 20.6641 14.4531 21.0938C14.4531 21.5234 14.1016 21.875 13.6719 21.875H12.8906C12.4609 21.875 12.1094 21.5234 12.1094 21.0938ZM21.0547 14.5947L21.4453 15.7666C21.582 16.1768 21.3623 16.6162 20.9521 16.7529C20.542 16.8896 20.1025 16.6699 19.9658 16.2598L19.5752 15.0879C19.4385 14.6777 19.6582 14.2383 20.0684 14.1016C20.4785 13.9648 20.918 14.1846 21.0547 14.5947ZM20.083 18.5205L18.5205 20.083C18.2178 20.3857 17.7197 20.3857 17.417 20.083C17.1143 19.7803 17.1143 19.2822 17.417 18.9795L18.9795 17.417C19.2822 17.1143 19.7803 17.1143 20.083 17.417C20.3857 17.7197 20.3857 18.2178 20.083 18.5205Z" fill="#2557A7"/>
                        </svg>
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
                        <input type="hidden" name="superorganizer_token" id="superorganizer_token" value="<?php echo TAOH_SUPER_ORGANIZER_TOKEN;?>" >
                        <input id="sponsor_type" name="sponsor_type" data-sponsorid="" type="hidden" value=""/>
                        <input id="rsvp_perpage" name="rsvp_perpage" type="hidden" value="1"/>   
                        
                        
                        <input type="hidden" name="event_start_at" id="event_start_at" value=""> 
                        <input type="hidden" name="event_end_at" id="event_end_at" value="">
                        <input type="hidden" name="event_timezone" id="event_timezone" value="">
                        <input type="hidden" name="event_locality" id="event_locality" value="">

                        
                    
                            <div class="info-container p-3" id="event_info_container1" style="display:none;">
                                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 16px;">
                                    <div class="info-card happens py-3 py-lg-4">
                                        <svg width="30" height="29" viewBox="0 0 30 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M29 8.77779H1M8.77778 4.11113V1.00002M21.2222 4.11111V1M4.11111 2.55557H3.95556C2.32325 2.55557 1 3.87882 1 5.51113V24.3333C1 26.0516 2.39289 27.4444 4.11111 27.4444H25.8889C27.6072 27.4444 29 26.0516 29 24.3333V5.51113C29 3.87882 27.6767 2.55557 26.0444 2.55557H25.8889M13.4444 2.55557H16.5556M20.0602 15.1725L14.8136 21.7079C14.2339 22.4301 13.1556 22.4889 12.5007 21.834L10.3333 19.6667" stroke="#2A4E96" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <div>
                                            <p class="mb-2 info-title">Event Date and Time</p>
                                            <p id="event_start_datetime1"></p>
                                        </div>
                                    </div>
                                    <!-- <div class="info-card happens py-3">
                                        <svg width="30" height="29" viewBox="0 0 30 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M29 8.77779H1M8.77778 4.11113V1.00002M21.2222 4.11111V1M4.11111 2.55557H3.95556C2.32325 2.55557 1 3.87882 1 5.51113V24.3333C1 26.0516 2.39289 27.4444 4.11111 27.4444H25.8889C27.6072 27.4444 29 26.0516 29 24.3333V5.51113C29 3.87882 27.6767 2.55557 26.0444 2.55557H25.8889M13.4444 2.55557H16.5556M20.0602 15.1725L14.8136 21.7079C14.2339 22.4301 13.1556 22.4889 12.5007 21.834L10.3333 19.6667" stroke="#2A4E96" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
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
                                                        <svg width="17" height="12" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2.0434 0.111328C1.06285 0.111328 0.265625 0.933464 0.265625 1.94466L0.265625 3.77799C0.265625 4.03008 0.471181 4.22773 0.701736 4.31081C1.22396 4.49701 1.59896 5.00977 1.59896 5.61133C1.59896 6.21289 1.22396 6.72565 0.701736 6.91185C0.471181 6.99492 0.265625 7.19258 0.265625 7.44466L0.265625 9.27799C0.265625 10.2892 1.06285 11.1113 2.0434 11.1113H14.4878C15.4684 11.1113 16.2656 10.2892 16.2656 9.27799V7.44466C16.2656 7.19258 16.0601 6.99492 15.8295 6.91185C15.3073 6.72565 14.9323 6.21289 14.9323 5.61133C14.9323 5.00977 15.3073 4.49701 15.8295 4.31081C16.0601 4.22773 16.2656 4.03008 16.2656 3.77799V1.94466C16.2656 0.933464 15.4684 0.111328 14.4878 0.111328L2.0434 0.111328ZM3.82118 3.31966L3.82118 7.90299C3.82118 8.15508 4.02118 8.36133 4.26563 8.36133H12.2656C12.5101 8.36133 12.7101 8.15508 12.7101 7.90299V3.31966C12.7101 3.06758 12.5101 2.86133 12.2656 2.86133L4.26563 2.86133C4.02118 2.86133 3.82118 3.06758 3.82118 3.31966ZM2.93229 2.86133C2.93229 2.3543 3.32951 1.94466 3.82118 1.94466L12.7101 1.94466C13.2017 1.94466 13.599 2.3543 13.599 2.86133V8.36133C13.599 8.86836 13.2017 9.27799 12.7101 9.27799H3.82118C3.32951 9.27799 2.93229 8.86836 2.93229 8.36133L2.93229 2.86133Z" fill="#2557A7"/>
                                                        </svg>
                                                        <span style="color: #2557A7;">Buy Tickets</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item flex-grow-1 event_sponsor_right_header" style="display: none;">
                                                    <a class="nav-link d-flex align-items-center justify-content-center sponsor-btn" id="become-sponsor-tab" data-toggle="tab" href="#become_sponsor" role="tab"
                                                        aria-controls="become_sponsor" aria-selected="false" style="gap: 6px;">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M8.87046 0.562437C8.70987 0.218726 8.37051 0 7.99782 0C7.62514 0 7.28881 0.218726 7.12519 0.562437L5.17691 4.69635L0.825865 5.35878C0.462267 5.41502 0.159269 5.67749 0.04716 6.03682C-0.0649492 6.39616 0.02595 6.79299 0.286528 7.05858L3.44377 10.2801L2.69839 14.8327C2.63779 15.2077 2.78929 15.5889 3.08926 15.8107C3.38923 16.0326 3.78615 16.0607 4.11339 15.8826L8.00085 13.7422L11.8883 15.8826C12.2156 16.0607 12.6125 16.0357 12.9124 15.8107C13.2124 15.5858 13.3639 15.2077 13.3033 14.8327L12.5549 10.2801L15.7121 7.05858C15.9727 6.79299 16.0667 6.39616 15.9515 6.03682C15.8364 5.67749 15.5364 5.41502 15.1728 5.35878L10.8187 4.69635L8.87046 0.562437Z" fill="#2557A7"/>
                                                        </svg>
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
                    <?php echo taoh_sponsor_slider_widget($eventtoken); ?>
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
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.6449 2.04935C12.1134 1.58082 12.1134 0.819928 11.6449 0.351398C11.1763 -0.117133 10.4154 -0.117133 9.9469 0.351398L6 4.30205L2.04935 0.355146C1.58082 -0.113384 0.819928 -0.113384 0.351398 0.355146C-0.117133 0.823676 -0.117133 1.58457 0.351398 2.0531L4.30205 6L0.355146 9.95065C-0.113384 10.4192 -0.113384 11.1801 0.355146 11.6486C0.823676 12.1171 1.58457 12.1171 2.0531 11.6486L6 7.69795L9.95065 11.6449C10.4192 12.1134 11.1801 12.1134 11.6486 11.6449C12.1171 11.1763 12.1171 10.4154 11.6486 9.9469L7.69795 6L11.6449 2.04935Z" fill="#555555"/>
                </svg>
            </button>
        </div>
        <div class="modal-body px-lg-4">
            <p>You chose for an Explorer Ticket. Get 20 % Discount Just by sharing the event and instantly unlock extra perks:</p>

            <ul class="svg-styled">
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
                    <span>Create your own discussion channels</span>
                </li>
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
                    <span>Priority access to raffles and swags</span>
                </li>
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
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
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.6449 2.04935C12.1134 1.58082 12.1134 0.819928 11.6449 0.351398C11.1763 -0.117133 10.4154 -0.117133 9.9469 0.351398L6 4.30205L2.04935 0.355146C1.58082 -0.113384 0.819928 -0.113384 0.351398 0.355146C-0.117133 0.823676 -0.117133 1.58457 0.351398 2.0531L4.30205 6L0.355146 9.95065C-0.113384 10.4192 -0.113384 11.1801 0.355146 11.6486C0.823676 12.1171 1.58457 12.1171 2.0531 11.6486L6 7.69795L9.95065 11.6449C10.4192 12.1134 11.1801 12.1134 11.6486 11.6449C12.1171 11.1763 12.1171 10.4154 11.6486 9.9469L7.69795 6L11.6449 2.04935Z" fill="#555555"/>
                </svg>
            </button>
        </div>
        <div class="modal-body px-lg-4">
            <p>You chose for an Explorer Ticket. Upgrade to Explorer Plus for free — just share this event and instantly unlock extra perks:</p>

            <ul class="svg-styled">
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
                    <span>Create your own discussion channels</span>
                </li>
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
                    <span>Priority access to raffles and swags</span>
                </li>
                <li>
                    <svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.2923 0.0256658C9.82275 0.788638 6.88018 4.67069 4.05964 8.65749C2.86259 6.64193 1.59444 5.96894 0.283202 6.65849C0.121354 6.74348 0.0147307 6.90646 0.00139086 7.08976C-0.0119378 7.27495 0.0712083 7.4538 0.221637 7.56353C1.17242 8.25737 2.15876 9.4872 3.33173 11.4342V11.4336C3.54118 11.7869 3.922 12.0025 4.33201 12H4.35105C4.77376 11.9962 5.16093 11.7653 5.36593 11.3962C6.50712 9.32866 7.76061 7.32576 9.12076 5.39576C10.2353 3.81273 11.4781 2.3229 12.8363 0.942167C13.0191 0.763309 13.0527 0.481724 12.9175 0.264808C12.7912 0.0485417 12.531 -0.0510666 12.2923 0.0256658Z" fill="#3DB057"/>
                    </svg>
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
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.6449 2.04935C12.1134 1.58082 12.1134 0.819928 11.6449 0.351398C11.1763 -0.117133 10.4154 -0.117133 9.9469 0.351398L6 4.30205L2.04935 0.355146C1.58082 -0.113384 0.819928 -0.113384 0.351398 0.355146C-0.117133 0.823676 -0.117133 1.58457 0.351398 2.0531L4.30205 6L0.355146 9.95065C-0.113384 10.4192 -0.113384 11.1801 0.355146 11.6486C0.823676 12.1171 1.58457 12.1171 2.0531 11.6486L6 7.69795L9.95065 11.6449C10.4192 12.1134 11.1801 12.1134 11.6486 11.6449C12.1171 11.1763 12.1171 10.4154 11.6486 9.9469L7.69795 6L11.6449 2.04935Z" fill="#555555"/>
                </svg>
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

    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/event.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script> 
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
    <script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/event-detail-processor.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>


<?php
taoh_get_footer();