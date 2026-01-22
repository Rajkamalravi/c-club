<?php
//ini_set('display_errors',1);
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;
$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$ptoken = $taoh_user_is_logged_in ? ($user_info_obj?->ptoken ?? '') : '';
$profile_type = ($taoh_user_is_logged_in && in_array($user_info_obj?->type ?? '', ['professional','employer','provider'], true))
    ? $user_info_obj->type : '';

/* 
if(isset($_GET['fbclid']) && $_GET['fbclid'] != '' && !taoh_user_is_logged_in() ){
    setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',getCurrentUrl(), strtotime( '+2 days' ), '/');
    header("Location: " . TAOH_SITE_URL_ROOT . "/login");    
    taoh_exit();
    
} */

$encodeCurrentUrl = encrypt_url_safe(getCurrentUrl());

//echo "=========".$user_location;die();
$parse_url_2 = taoh_parse_url(2);
$eventtoken_expl = explode('-', $parse_url_2);
$eventtoken = array_pop($eventtoken_expl);
$to_page = '';
$footer_tracking_link = 'events_detail_'.$eventtoken;

if ( ! ctype_alnum( $eventtoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/invalid-eventtoken' );taoh_exit(); }
if(taoh_parse_url(3) ){
    $table_field = taoh_parse_url(3);
    $to_page = taoh_parse_url(4,0);
   // echo $table_field;die();
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

/* if(isset($_GET['fbclid']) && $_GET['fbclid'] != '' && !taoh_user_is_logged_in() ){
    setcookie(TAOH_ROOT_PATH_HASH.'_'.'referral_back_url',getCurrentUrl(), strtotime( '+2 days' ), '/');
    header("Location: " . TAOH_SITE_URL_ROOT . "/login");
    taoh_exit();
} */

//echo $ref_param;
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
    $share_link = $share_link;
    $ref_slug = '';
}
$original_link = $share_link;



//echo '======>'.$share_link;die;

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

//echo '<pre>';print_r($response);echo '';die;
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
//echo "=====".$event_short;
//$event_short = htmlspecialchars($event_short);
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
//$taoh_vals['debug'] = 1;
//echo taoh_apicall_get('events.content.get', $taoh_vals);die();
$get_event_meta_info_response = taoh_apicall_get('events.content.get', $taoh_vals);
$get_event_meta_info_arr = json_decode($get_event_meta_info_response, true);
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
//echo "<pre>";print_r($events_data);
$additive = '';
/* if(isset($events_data['canonical_url']) && $events_data['canonical_url'] !=''){
    echo '==========1111111=========>';
	$additive = '<link rel="canonical" href="'.$events_data['canonical_url'].'"/> 
	<meta name="original-source" content="'.$events_data['canonical_url'].'"/>';
	define ( 'TAO_PAGE_CANONICAL', $additive );
}else{
    
		$additive = '<link rel="canonical" href="'.$events_data['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken.'"/>
		<meta name="original-source" content="'.$events_data['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken.'"/>';
		// TAO_PAGE_CANONICAL
	        
        define ( 'TAO_PAGE_CANONICAL', $additive );
}

 */
if(isset($site_info['source']) && $site_info['source'] !='' && TAOH_SITE_URL_ROOT != $site_info['source']){
    $canonical_url = $site_info['source'].'/'.$app_data->slug.'/d/'.slugify2($event_title)."-".$eventtoken;
    $additive = '<link rel="canonical" href="'.$canonical_url.'"/> 
	<meta name="original-source" content="'.$canonical_url.'"/>';
	
   
}


$adopter_url = TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/club/' . taoh_slugify($event_title) . '-' . $eventtoken;


define ( 'TAO_PAGE_CANONICAL', $additive );
//echo '====333=====>'.$additive;die;
$trackingtoken = '';

if($taoh_user_is_logged_in && $ptoken != ''){
    $trackingtoken = hash('sha256',(string)$ptoken);
    
    /* $trackingtoken = bin2hex($ptoken); */
    $share_link =  addPathSegment($share_link,'stlo',$trackingtoken);
    //echo $share_link;
}

$social_token = '';
if (isset($ref_param) && $ref_param != '' && $ref_param != 'stlo') {
    
    $hashptoken =  hash('sha256',(string)$ptoken); 
    if ( $ptoken !== '' && $hashptoken === (string)$ref_param) {
        
        $social_token = $ref_param;
    }
   
    
}
$success_discount_amt = '';
$success_sponsor_title = '';
$success_redirect = '';
$discount_amt = 0;
//echo '-----------'.$ref_slug;
//ho "<pre>";print_r($sponsor_levels);
if(isset($ref_slug) && $ref_slug != '' && $ref_slug != 'stlo'){
    $ticketarr = [];
    foreach($ticket_types as $tkey=>$tvalue){
        $ticketarr[$tvalue['title']]  = $tvalue['social_sharing_discount'];
    }
   //cho "<pre>";print_r($ticketarr);
    foreach($sponsor_levels as $key=>$value){
        //echo $value['slug'].'-----------'.$ref_slug;
        if($value['slug'] == $ref_slug){
            //echo '=============>'.$value['slug'];
            
            //echo '=========='.$value['award_ticket_type'];
            if(array_key_exists($value['award_ticket_type'], $ticketarr)){
                //echo '<=ddddddddd==========>'.$value['award_ticket_type'];
                $discount_amt = $ticketarr[$value['award_ticket_type']];
//echo '<=eeeeeeeee==========>'. $discount_amt;
            }
            if( $discount_amt > 0 ){
                $success_discount_amt = $discount_amt.'%';
                $success_sponsor_title = $value['title'];
                $success_redirect = TAOH_SITE_URL_ROOT.'/events/event_sponsor/'.$eventtoken.'/'.$ref_slug.'/socialshare/'.$trackingtoken; 
                //echo $success_redirect;
            }
        }
    }
}
$GLOBALS['success_discount_amt'] = $success_discount_amt;
$GLOBALS['success_sponsor_title'] = $success_sponsor_title;
$GLOBALS['success_redirect'] = $success_redirect;
//$GLOBALS['success_discount_amt'] = '50';

//echo '========='.$GLOBALS['success_discount_amt'] ;

// Check RSVP status
$taoh_vals = [
    'ops' => 'status',
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $eventtoken,
    'cache_required' => 0,
    'time' => time(),
];
//echo taoh_apicall_get_debug('events.rsvp.get', $taoh_vals);die;
$res = taoh_apicall_get('events.rsvp.get', $taoh_vals);
$response = json_decode($res, true);
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
//    echo taoh_apicall_get_debug('events.rsvp.get', $taoh_vals);die;
    $response = json_decode(taoh_apicall_get('events.rsvp.get', $taoh_vals), true);
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
//echo taoh_apicall_get_debug('system.users.metrics', $taoh_vals);die();
$get_liked = json_decode(taoh_apicall_get('system.users.metrics', $taoh_vals), true);
//echo '<pre>';print_r($get_liked);echo '</pre>';die();
$userliked_already = '';
if(isset($get_liked['success']) && $get_liked['success'] === true) {
    $userliked_already = $get_liked['output']['userliked'] ?? '0';
}

//echo "--------".$userliked_already;die();
/* End check liked or not */

/*$liked_arr = array();
if ($taoh_user_is_logged_in) {
    $taoh_call = "system.users.metrics";
    $taoh_vals = array(
        'mod' => 'system',
        'token' => taoh_get_dummy_token(),
        'slug' => TAO_PAGE_TYPE,
    );
    //echo taoh_apicall_get_debug('system.users.metrics', $taoh_vals);die();
    $get_liked = json_decode(taoh_apicall_get('system.users.metrics', $taoh_vals), true);

    if (isset($get_liked['conttoken_liked'])) {
        $liked_arr = json_encode($get_liked['conttoken_liked']);
    }
}*/
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


                <div class="mb-3" style="">
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
        
        const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
        const isValidUser = <?= json_encode($valid_user); ?>;
        const profileType = <?= json_encode($profile_type); ?>;
        const is_sponsor_enable = <?= json_encode($is_sponsor_enable); ?>;
        const is_exhibitor_enable = <?= json_encode($is_exhibitor_enable); ?>;
        const is_speaker_enable = <?= json_encode($is_speaker_enable); ?>;
        const user_profile_type = '<?= $user_info_obj->type ?? ''; ?>';
        const dojoeventrules = <?php echo json_encode(DOJO_EVENT_DETAIL_MESSAGE); ?>;    
        let eventToken = '<?= $eventtoken ?? ''; ?>';
        let encodeCurrentUrl = '<?= $encodeCurrentUrl ?? ''; ?>';

        let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
        const is_rsvp = is_user_rsvp_done;
        let show_rsvp_ticket = <?= json_encode($show_rsvp_ticket); ?>;
        let rsvp_ticket_token = '<?= $rsvp_ticket_token ?? ''; ?>';
        let click_view = '<?= $click_view ?? 'view'; ?>';
        let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';
        let rsvp_slug = '<?= $rsvp_slug ?? ''; ?>';
        const my_pToken = '<?= $ptoken ?? ''; ?>';
        let ref_slug = '<?= $ref_slug ?? ''; ?>';
        let success_discount_amt = '<?= $success_discount_amt ?? ''; ?>';
        let social_share_status = '<?php echo $social_token ?? ''; ?>';
        let trackingtoken = '<?= $trackingtoken ?? ''; ?>';
        let userliked_already = '<?= $userliked_already ?? '0'; ?>';

        $(document).ready(function () {
            if (isLoggedIn && !profileType && typeof showBasicSettingsModal === 'function') {
                showBasicSettingsModal(true);
            }

            if(!is_user_rsvp_done){
                $("#desc-tab").addClass('active');
                $("#desc_desc").addClass('show active');
                $("#desc-tab").show();
                $("#agenda_desc").removeClass('show active');
                $("#agenda_desc").addClass('fade');
                $("#agenda-tab").removeClass('active');
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

            function processEventBaseInfo(requestData, response) {
                console.log('processEventBaseInfo', response);
                let event_output = response.output;
                let event_owner = event_output.ptoken;
                let conttoken_data = event_output.conttoken;

                if(!conttoken_data) return;

                var event_country_name = '';

                if(conttoken_data.full_location !='' && conttoken_data.full_location != undefined){
                     event_country = conttoken_data.full_location;
                     evet_country_array = event_country.split(',');
                     event_country_name = evet_country_array[evet_country_array.length-1].trim();
                }

                var country_locked = 0;
                if(conttoken_data.country_locked !='' && conttoken_data.country_locked != undefined){
                    country_locked = conttoken_data.country_locked;
                }

                $('#event_country_lock').val(country_locked);
                $('#event_country_name').val(event_country_name);

                if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) {
                    org_email = $.trim(conttoken_data.org_email);
                    $("#exhibitor_contactus").attr('data-email',  org_email + "");
                    $("#speaker_contactus").attr('data-email', org_email + "");
                    $("#agenda_contactus").attr('data-email', org_email+"");
                 }

                 var event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                    .split(',')
                    .map(token => token.trim())
                    .filter(token => token);

                if(event_owner) event_organizer_ptokens.push(event_owner);

                var event_instance_owner = conttoken_data.ptoken;
                event_organizer_ptokens.push(event_instance_owner);

                var superorganizer_token = $('#superorganizer_token').val();
                event_organizer_ptokens.push(superorganizer_token);//deeksha

                if (event_organizer_ptokens.includes(my_pToken) || my_pToken == superorganizer_token) {
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

                if(conttoken_data.event_video == undefined || conttoken_data.event_video == null ){
                    conttoken_data.event_video = '';
                }
                /* Event Banner */
                const allEventBannersArray = [conttoken_data.event_video, conttoken_data.event_image].concat((conttoken_data.more_banner || []));
                const eventBannersArray = allEventBannersArray.filter(url => url.trim() !== "" && isValidURL(url)).map(url => ({
                    src: url,
                    type: getMediaType(url)
                }));

                const galleryContainer = document.getElementById("event_banner_container");
                //const mainDisplay = document.createElement("div");
                //mainDisplay.id = "event_banner_image";
                //galleryContainer.before(mainDisplay);

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

                /*function displayMedia(media) {
                    if(!media) return;


                    mainDisplay.innerHTML = "";
                    let mediaHtml = "";
                    //alert(media.type)
                    if (media.type === "image") {
                        mediaHtml = `
                            <div class="cover-event-image">
                                <div class="events-bg" style="background-image: url('${media.src}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${media.src}" class="detail-main-image" alt="Event">
                            </div>
                        `;
                    } else if (media.type === "video") {
                        let videoSrc = formatVideoSrc(media.src);
                        mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay"></iframe>`;
                    }


                    mainDisplay.innerHTML = mediaHtml;
                }*/

                /*style="--background-src: url('${media.src}');"*/

                const noImage = _taoh_site_url_root + '/assets/images/event.jpg';

                if(eventBannersArray[0]){
                        // Generate the gallery items
                        eventBannersArray.forEach((media, index) => {
                            let itemHtml = "";
                            //alert(media.type+'------------'+index);
                            if (media.type === "image") {
                                itemHtml = `<div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <div class="cover-event-image">
                                        <div class="events-bg" style="background-image: url('${media.src}');"></div>
                                        <div class="glass-overlay"></div>
                                        <img src="${media.src}" class="detail-main-image" alt="Event">
                                    </div>
                                </div>`;
                            } else if (media.type === "video") {
                                /*let thumbnailSrc = "";
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

                                itemHtml = `<div class="carousel-item ${index === 0 ? 'active' : ''}"
                                ><img src="${thumbnailSrc}" class="thumbnail" data-index="${index}" alt="Gallery Video ${index + 1}"></div>`;*/
                                let videoSrc = formatVideoSrc(media.src);

                                itemHtml = `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}" >
                                    <iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay"></iframe>
                                </div>`;

                            }

                            galleryContainer.innerHTML += itemHtml;
                        });
                }
                else{
                    const noImage = _taoh_site_url_root + '/assets/images/event.jpg';
                    itemHtml = `<div class="carousel-item active" >
                                    <div class="cover-event-image">
                                        <div class="events-bg" style="background-image: url('${noImage}');"></div>
                                        <div class="glass-overlay"></div>
                                        <img src="${noImage}" class="detail-main-image" alt="Event">
                                    </div>
                                </div>`;
                    galleryContainer.innerHTML += itemHtml;
                }


                /* /Event Banner */

                if (userliked_already == 1) {
                    $('#event_like_btn').html(`<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-event="${event_output.eventtoken}" data-cont="${conttoken_data.conttoken}" class="event_saved" title="Save Event">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="#000000" stroke="white"/>
				    </svg>`);
                } else {
                    $('#event_like_btn').html(`<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" data-event="${event_output.eventtoken}" data-cont="${conttoken_data.conttoken}" class="event_save" title="Save Event">
					<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" fill="" stroke="white"/>
				    </svg>`);
                }


                let eventDescriptionHtml = '';
                if (conttoken_data.description && $.trim(conttoken_data.description) != '') {
                    eventDescriptionHtml += '<h3>About this Event</h3>';
                    eventDescriptionHtml += `<div>${taoh_desc_decode(conttoken_data.description)}</div>`;
                }

                if (conttoken_data.about_you && $.trim(conttoken_data.about_you) != '') {
                    eventDescriptionHtml += '<h3>About the Host</h3>';
                    eventDescriptionHtml += `<div>${taoh_desc_decode(conttoken_data.about_you)}</div>`;
                }

                $('.event_description').html(eventDescriptionHtml);


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
                if (isLoggedIn && !is_event_suspended && !is_event_freeze && event_live_state === 'before') {
                    constructUpgradeModalContent(event_output, my_pToken, rsvp_slug, isLoggedIn);
                    $('.attendee_tagline').css('display', 'flex');
                } else {
                    $('.upgrade_modal_btn_wrapper').hide();
                }

                /* Ticket Types */
                let eventTicketTypesHtml = '';
                var redirect_to = !isValidUser ? `${_taoh_site_url_root}/settings` : `${TAOH_CURR_APP_URL}/chat/id/events/${eventToken}`;

                if (isLoggedIn) {
                     eventTicketTypesHtml += '<a href="<?php echo $adopter_url;?>" id="networking_link" style="display:none;" class="btn btn-success">Go to Networking Room</a>';
                    // eventTicketTypesHtml += '<div class="tab-content" id="myTabContent">';
                    // eventTicketTypesHtml += '<div class="tab-pane fade show active p-3" id="buy_tickets" role="tabpanel" aria-labelledby="buy-tickets-tab">';
                    if (!is_user_rsvp_done && !is_event_suspended && !is_event_freeze && (event_live_state === 'before' || event_live_state === 'live')) {
                        eventTicketTypesHtml += '<div class="dropdown w-100">';
                        eventTicketTypesHtml += `<button class="btn ${event_live_state === 'live' ? 'btn-success' : 'btn-primary'} dropdown-toggle w-100" type="button" id="choose_ticket" data-ticket_selected="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:none">${event_live_state === 'live' ? 'LIVE NOW!' : ''} Choose a ticket to Register</button>`;
                        eventTicketTypesHtml += '<ul class="ticket-list w-100 dropdown-menu px-3 light-dark" id="ticket_list" aria-labelledby="choose_ticket" style="z-index: 9999;">';

                        const ticket_types = conttoken_data.ticket_types;
                        let ticket_type_selected = false;
                        i_am_the_sponsor = false;

                        ticket_types.forEach(ticket_type => {
                            if(ticket_type.visibility === 'hidden') return;

                            const applicable = ticket_type.applicable_to || [];
                            const hasAll = applicable.includes('all');
                            if(!hasAll && !applicable.includes(user_profile_type)) {
                                return;
                            }

                            const ticket_type_slug = ticket_type.slug;
                            const ticket_type_title = ticket_type.title;
                            const ticket_type_cost = ticket_type.cost;

                            var classes = 'rsvp_ticket_' + ticket_type_title + 'rsvp_tickets ticket-item';

                            /*if(ticket_type_title == 'Sponsor'){
                                var classes = 'rsvp_ticket_' + ticket_type_title + 'rsvp_tickets ticket-item event_sponsor_right_header';
                            }*/

                            eventTicketTypesHtml += '<li class="'+classes+'">';
                            eventTicketTypesHtml += '<input type="radio" name="ticket" id="' + ticket_type_slug + '" value="' + encodeURIComponent(ticket_type_title) + '" class="rsvp_ticket_' + ticket_type_title + ' rsvp_tickets d-none"';

                            if (is_user_rsvp_done) {
                                eventTicketTypesHtml += ' disabled ';
                                if (rsvp_slug === ticket_type_slug) {
                                    eventTicketTypesHtml += ' checked';
                                    ticket_type_selected = true;
                                }
                            }

                            eventTicketTypesHtml += '>';
                            eventTicketTypesHtml += '<label for="' + ticket_type_slug + '" class="item btn w-100">';
                            eventTicketTypesHtml += '<p class="item-title">' + ticket_type_title + '</p>';
                            eventTicketTypesHtml += '<p class="item-cost">' + (ticket_type.price === 'paid' ? 'Costs you $' + ticket_type_cost : 'Free') + '</p>';
                            eventTicketTypesHtml += '</label>';
                            eventTicketTypesHtml += '</li>';
                        });
                        eventTicketTypesHtml += '</ul>';
                        eventTicketTypesHtml += '</div>';
                    }


                   //  var eventStatusButton = '';
                    if (is_event_freeze || is_event_suspended) {
                            eventTicketTypesHtml += `
                             <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>
                            <a href="${TAOH_CURR_APP_URL}" class="btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i>Event Suspended</a>`;

                             /*eventStatusButton += `<span class="btn event-end d-flex align-items-center cursor-pointer px-3" style="min-width:200px;gap: 12px;">
                                        <svg width="24" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.85714 0C7.80536 0 8.57143 0.754102 8.57143 1.6875V3.375H15.4286V1.6875C15.4286 0.754102 16.1946 0 17.1429 0C18.0911 0 18.8571 0.754102 18.8571 1.6875V3.375H21.4286C22.8482 3.375 24 4.50879 24 5.90625V8.4375H0V5.90625C0 4.50879 1.15179 3.375 2.57143 3.375H5.14286V1.6875C5.14286 0.754102 5.90893 0 6.85714 0ZM0 10.125H24V24.4688C24 25.8662 22.8482 27 21.4286 27H2.57143C1.15179 27 0 25.8662 0 24.4688V10.125ZM16.3393 16.084C16.8429 15.5883 16.8429 14.7867 16.3393 14.2963C15.8357 13.8059 15.0214 13.8006 14.5232 14.2963L12.0054 16.7748L9.4875 14.2963C8.98393 13.8006 8.16964 13.8006 7.67143 14.2963C7.17321 14.792 7.16786 15.5936 7.67143 16.084L10.1893 18.5625L7.67143 21.041C7.16786 21.5367 7.16786 22.3383 7.67143 22.8287C8.175 23.3191 8.98929 23.3244 9.4875 22.8287L12.0054 20.3502L14.5232 22.8287C15.0268 23.3244 15.8411 23.3244 16.3393 22.8287C16.8375 22.333 16.8429 21.5314 16.3393 21.041L13.8214 18.5625L16.3393 16.084Z" fill="#444444"/>
                                        </svg>
                                        <span>Event Suspended</span>
                                    </span>`;*/

                    } else {
                        if (event_live_state === 'after') {
                            eventTicketTypesHtml += `
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>
                            <a href="${TAOH_CURR_APP_URL}" class="btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>`;

                            /*eventStatusButton += `<span class="btn event-end d-flex align-items-center cursor-pointer px-3" style="gap: 12px;">
                                            <svg width="24" height="27" viewBox="0 0 24 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6.85714 0C7.80536 0 8.57143 0.754102 8.57143 1.6875V3.375H15.4286V1.6875C15.4286 0.754102 16.1946 0 17.1429 0C18.0911 0 18.8571 0.754102 18.8571 1.6875V3.375H21.4286C22.8482 3.375 24 4.50879 24 5.90625V8.4375H0V5.90625C0 4.50879 1.15179 3.375 2.57143 3.375H5.14286V1.6875C5.14286 0.754102 5.90893 0 6.85714 0ZM0 10.125H24V24.4688C24 25.8662 22.8482 27 21.4286 27H2.57143C1.15179 27 0 25.8662 0 24.4688V10.125ZM16.3393 16.084C16.8429 15.5883 16.8429 14.7867 16.3393 14.2963C15.8357 13.8059 15.0214 13.8006 14.5232 14.2963L12.0054 16.7748L9.4875 14.2963C8.98393 13.8006 8.16964 13.8006 7.67143 14.2963C7.17321 14.792 7.16786 15.5936 7.67143 16.084L10.1893 18.5625L7.67143 21.041C7.16786 21.5367 7.16786 22.3383 7.67143 22.8287C8.175 23.3191 8.98929 23.3244 9.4875 22.8287L12.0054 20.3502L14.5232 22.8287C15.0268 23.3244 15.8411 23.3244 16.3393 22.8287C16.8375 22.333 16.8429 21.5314 16.3393 21.041L13.8214 18.5625L16.3393 16.084Z" fill="#444444"/>
                                            </svg> <span>Ended</span>
                                        </span>`;*/

                        } else {
                            if(is_user_rsvp_done && event_live_state === 'before'){
                                eventTicketTypesHtml +=  `<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="1"/>
                                <a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventToken}" class="btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>`;


                                /*eventStatusButton += `<span class="btn not-live d-flex align-items-center px-3" style="min-width:200px;gap: 12px;">
                                        <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                        </svg>
                                        <span>Event Not Live!</span>
                                    </span>`;*/
                            } else if (!is_user_rsvp_done && event_live_state === 'before') {
                                eventTicketTypesHtml +=  `<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="0"/>
                                `;
                                /*eventStatusButton += `<span class="btn not-live d-flex align-items-center px-3" style="min-width:200px;gap: 12px;">
                                        <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                        </svg>
                                        <span>Event Not Live!</span>
                                    </span>`;*/
                            } else if (is_user_rsvp_done && event_live_state === 'live') {
                               eventTicketTypesHtml += `
                                 <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                                 <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>
                                <a href="${redirect_to}" class="btn live w-100">
                                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Event Live, ${!isValidUser ? 'Complete settings to' : 'Click to'} Join</a>`;

                                /*eventStatusButton += `<a href="${redirect_to}" class="btn btn-success w-100">
                                            <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Event Live, ${!isValidUser ? 'Complete settings to' : 'Click to'} Join</a>
                                        `;*/

                            } else if (!is_user_rsvp_done && event_live_state === 'live') {
                                eventTicketTypesHtml += `
                                 <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                                 <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                               `;

                                /*eventStatusButton += `<span class="btn live d-flex align-items-center cursor-pointer px-3" style="min-width:200px;gap: 12px;">
                                        <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#ffffff"/>
                                        </svg>
                                        <span>Event Live!</span>
                                    </span>
                                        `;*/

                            } else if (is_user_rsvp_done) {
                                eventTicketTypesHtml += `
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>
                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                                <a href="${TAOH_CURR_APP_URL}/chat/id/events/${eventToken}" class="btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>`;
                            } else {
                                eventTicketTypesHtml +=  `
                                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                                `;

                                /*<button type="button" class="mt-2 btn btn-primary w-100" id="register_ticket">
                                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Register Now</button>*/
                            }
                        }
                    }
                    // eventTicketTypesHtml += '</div>'; //tab content end
                } else {
                    eventTicketTypesHtml = `<button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="${location.href}" data-title="${btoa(unescape(encodeURIComponent(conttoken_data.title)))}" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>`;
                }

                eventTicketTypesHtml += `             
                    <button id="sponsor_card1" style="display: none;border:1.2px solid rgba(255, 193, 7, 0.8);border-radius:6px;max-width: none;" type="button" class="btn w-100 event_sponsor_right_header 
                    sponsor-card mx-auto  sponsor-btn " data-toggle="modal" data-target="#sponsorInfo">

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
                    Become a sponsor</button>

                    `;

                //tab end div
                // eventTicketTypesHtml += '</div>';

                $('.ticket-card-div').html(eventTicketTypesHtml);


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
                $('#event_locality').val(event_output?.conttoken.locality ?? '');

                function eventVenueLoc(conttoken_data) {
                    return (conttoken_data.map_link)
                        ? `<a href="${conttoken_data.map_link}" target="_blank" class="cursor-pointer text-underline">${conttoken_data.venue}</a>`
                        : conttoken_data.venue;
                }

                function eventJoinHere(taoh_curr_app_url, eventtoken, is_user_rsvp_done, event_live_state) {
                    return '';
                    /*let lobbyLink = `${taoh_curr_app_url}/chat/id/events/${eventtoken}`;

                    return (is_user_rsvp_done && event_live_state === 'live')
                        ? `, <a href="${lobbyLink}" title="${lobbyLink}" target="_blank" class="cursor-pointer text-underline">Join here</a>`
                        : ', Join here';*/
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

                /* Event Sponsor */
                getEventSponsor(event_output.eventtoken);

                let eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
                let eventTicketType = conttoken_data.ticket_types || {};
                let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(
                    widget => widget.quantity > 0 ? 1 : 0
                );

                let event_form_version = conttoken_data.event_form_version ?? 1;
                let is_social_share_enabled = conttoken_data.event_social_sharing;
                constructSponsorInfoPopup(event_output.eventtoken,eventSponsorWidgetType,user_profile_type, conttoken_data.org_email,social_share_status,eventTicketType,event_form_version,is_social_share_enabled,trackingtoken,isLoggedIn);
                eventCheckinList(event_output.eventtoken,'',1);
                //getEventRSVPedUsers(event_output.eventtoken,'',event_organizer_ptokens);

                $("#enable_exhibitor_hall").val(conttoken_data.enable_exhibitor_hall);
                $("#enable_speaker_hall").val(conttoken_data.enable_speaker_hall);

                // RSVP toggle
                const rsvp_status_hidden = $('#rsvp_status_hidden').val();
                if (rsvp_status_hidden == 0) {
                    $('.more-info').hide();
                }

                setTimeout(() => {
                    getEventMetaInfo(event_output.eventtoken).then(() => {
                        getEventsHall(event_output.eventtoken);

                        const isV2HallEnabled   = (event_form_version == 2 && conttoken_data.enable_hall === "1");
                        const hasSpeakerHall    = (conttoken_data.enable_speaker_hall === "1");
                        const hasExhibitorHall  = (conttoken_data.enable_exhibitor_hall === "1");

                        const $speakerTop   = $('#speaker_top');
                        const $speakerDesc  = $('#speaker_desc');
                        const $exhibitorTop = $('#exhibitor_top');
                        const $exhibitorDesc= $('#exhibitor_desc');
                        const $agendaDesc   = $('#agenda_desc');
                        const $sponsorHeader= $('.event_sponsor_right_header');
                        const $chooseTicket = $('#choose_ticket');

                        // Case 1: v2 + hall enabled OR both speaker & exhibitor enabled
                        if ((isV2HallEnabled) || (hasSpeakerHall && hasExhibitorHall)) {

                            // Speaker tab/top visible if v2 OR speaker halls enabled
                            if (isV2HallEnabled || hasSpeakerHall) {
                                $speakerTop.show();
                            }

                            // Exhibitor tab/top visible if v2 OR exhibitor halls enabled
                            if (isV2HallEnabled || hasExhibitorHall) {
                                $exhibitorTop.show();
                            }

                            // Case 2: only speaker halls enabled
                        } else if (hasSpeakerHall && !hasExhibitorHall) {

                            $exhibitorDesc.remove();
                            $exhibitorTop.remove();
                            $speakerTop.show();

                            // Case 3: only exhibitor halls enabled
                        } else if (!hasSpeakerHall && hasExhibitorHall) {

                            $speakerDesc.remove();
                            $speakerTop.remove();
                            $exhibitorTop.show();

                            // Case 4: neither speaker nor exhibitor enabled
                        } else {

                            $sponsorHeader.hide();
                            $chooseTicket.show();

                            $exhibitorDesc.remove();
                            $exhibitorTop.remove();
                            $speakerDesc.remove();
                            $speakerTop.remove();

                            $agendaDesc.addClass('show active');
                        }
                    });
                }, 3000);


                setTimeout(() => {
                    var event_status = $('#event_status_hidden').val();
                    if ((!isLoggedIn || event_status == 2) && eventSponsorWidgetTypeStatusList.includes(1)) {
                        $('.event_sponsor_right_header').show();
                        $('#sponsor_card').show();
                        $('.get-started').show();
                        $("#choose_ticket").show();
                    } else {
                        $('.event_sponsor_right_header').hide();
                        $('.get-started').hide();
                        $("#choose_ticket").show();
                        $('#continuePurchase').modal('hide');
                    }

                    var superorganizer_token = $('#superorganizer_token').val();
                    if (event_status == 1 || my_pToken == superorganizer_token) {
                        $('.speaker-banner').hide();
                        $('.exhibitor-banner').hide();
                    } else {
                        $('.speaker-banner').show();
                        $('.exhibitor-banner').show();
                        $('.rsvp_actions').css('display', 'none');

                        let allowView = false;
                        let attendeesMsg = '';
                        if (superorganizer_token === my_ptoken) {
                            allowView = true;
                        } else if (isLoggedIn) {
                            if (is_user_rsvp_done == 1 && event_status == 1) {
                                allowView = true;
                            } else {
                                attendeesMsg = is_user_rsvp_done != 1
                                    ? "Register to view the attendees."
                                    : "You can view the attendees when the event is live.";
                            }
                        } else {
                            attendeesMsg = "Login and register to view the attendees.";
                        }

                        /* $('#rsvp_users_list').html(`<div class="event-registration-banner d-flex flex-column align-items-center justify-content-center">
                            <h4 class="my-4 text-center">${attendeesMsg}</h4>
                            <a href="#" style="display:none;" class="btn register-now-btn">Register Now</a></div>`); */
                        $("#rsvp_default_list").show();

                        if($("#is_organizer").val() == 1){
                            $('.rsvp_actions').show();
                        }
                        if(is_user_rsvp_done){
                            $("#register_now").hide();
                        }
                        loader(false, $("#rsvpdir_loaderArea"));
                    }

                     if($("#is_organizer").val() == 1)
                            $('#networking_link').show();
                    else
                         $('#networking_link').hide();

                    if (conttoken_data.table_discussion != '' && conttoken_data.table_discussion != undefined && conttoken_data.table_discussion == 1) {
                        $('#tables_top').show();
                    }

                    if (conttoken_data.comments != '' && conttoken_data.comments != undefined && conttoken_data.comments == 1) {
                        $('#comments_top').show();
                    }

                }, 4000);


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
                                // if (isLoggedIn) {
                                //     rsvpTicketHtml += `${is_user_rsvp_done
                                //         ? '<button type="button" class="btn btn-success valid-badge mb-3"><i class="fa fa-check-circle mr-1" aria-hidden="true"></i><span>Valid for entry</span></button>'
                                //         : '<button type="button" class="btn btn-danger mb-3"><i class="fa fa-times-circle mr-1" aria-hidden="true"></i><span>Not Valid for entry</span></button>'}`;
                                // }

                                rsvpTicketHtml += `<h3 class="ticket-title pb-3">${conttoken_data.title}</h3>`;

                                if (event_type === 'in-person') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">In-Person, <span>${eventVenueLoc(conttoken_data)}</span></span></p>`;
                                } else if (event_type === 'hybrid') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Hybrid - <span>${eventVenueLoc(conttoken_data)}</span> or Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span></p>`;
                                } else if (event_type === 'virtual') {
                                    rsvpTicketHtml += `<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Virtual ${eventJoinHere(TAOH_CURR_APP_URL, event_output.eventtoken, is_user_rsvp_done, event_live_state)}</span></p>`;
                                }

                                rsvpTicketHtml += `<p class="ticket-content py-1">Start DateTime: <span>${typeof event_start_at !== 'undefined' ? event_start_at : beautifyTime(localized_event_start_data.datetime, localized_event_start_data.timezone)}</span></p>`;

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

                                // if (is_user_rsvp_done) {
                                rsvpTicketHtml += `<img class="ticket-stamp" src="${_taoh_site_url_root + '/assets/images/valid-for-admission.png'}" alt="valid-for-admission">`;
                                // }

                                rsvpTicketHtml += `</div>
                            </div>`;

                                $('#rsvpTicketModal .modal-body').html(rsvpTicketHtml);

                                let rsvpTicketFooterHtml = '';
                                // if (isLoggedIn) {
                                rsvpTicketFooterHtml += `<button type="button" class="btn theme-btn-primary" data-dismiss="modal" style="width: 150px;">OK</button>`;
                                // } else {
                                //     rsvpTicketFooterHtml += `<a href="${_taoh_site_url_root + '/login'}" class="btn btn-primary">Login to continue</a>`;
                                // }
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

                //To show continue purchase
                $('#continuePurchase').modal('hide');
                if (ref_slug != '' && success_discount_amt != '') {
                    if (trackingtoken != '') {
                        getEventSponsorForShare(eventToken, trackingtoken);
                    }
                    let newUrl = '<?php echo $original_link; ?>';
                    history.replaceState(null, "", newUrl);
                }
            }

            getEventBaseInfo({ eventtoken: eventToken }, true)
                .then(({requestData, response}) => processEventBaseInfo(requestData, response))
                .catch(error => console.error("Error fetching event info:", error));

            if (isLoggedIn) {
                save_metrics('events', click_view, eventToken);
            }


            const url = new URL(window.location.href);
            if (url.searchParams.has('confirmation')) {
                if (url.searchParams.get('action_events') === 'events') {
                    delete_events_into('event_details_sponsor_' + eventToken);
                    delete_events_into('event_MetaInfo_' + eventToken);
                }
                if (url.searchParams.get('confirmation') === 'sponsor') {
                    delete_events_into('event_details_sponsor_' + eventToken);
                    delete_events_into('event_MetaInfo_' + eventToken);

                    if (url.searchParams.has('status') && url.searchParams.get('status') === 'success') {
                        taoh_set_success_message('Thank you for sponsoring this event.');
                    }
                    else if (url.searchParams.has('delete') && url.searchParams.get('delete') === 'success') {
                        taoh_set_success_message('Sponsor deleted successfully!');
                    }
                    else if (url.searchParams.has('status') && url.searchParams.get('status') === 'limitexceed') {
                        taoh_set_error_message('You exceed the limit! You are allowed to add one sponsor only.');
                    }
                    else if (url.searchParams.has('status') && url.searchParams.get('status') === 'nosponsortype') {
                        taoh_set_error_message('Please select the sponsor type to get started with adding new sponsor.');
                    }
                    else {
                        taoh_set_error_message('There was an error processing your request. Please try again later.');
                    }
                }

                url.searchParams.delete('confirmation');
                url.searchParams.delete('status');
                url.searchParams.delete('tickettoken');
                url.searchParams.delete('delete');
                window.history.pushState({}, '', url.toString());
            }

            
           

        });

        /*$(document).on("click", "#register_ticket", function () {
            if (isLoggedIn) {
                // let selected_ticket = $('select[name="ticket"] option:selected').val();
                let selected_ticket = $('input[name="ticket"]:checked').val();
                if (selected_ticket) {
                    window.location.href = _taoh_site_url_root + '/events/add_rsvp/' + eventToken + '/' + selected_ticket;
                } else {
                    alert('Please select a ticket');
                }
            }
        });*/

        /*function update_choose_ticket(current_elem){
            let selected_ticket = current_elem.find('input[type="radio"]').val();
            let selected_ticket_title = current_elem.find('label .item-title').text();
            let selected_ticket_cost = current_elem.find('label .item-cost').text();
            $('#choose_ticket').text(selected_ticket_title + ' (' + selected_ticket_cost + ')');
        }*/

        $(document).on("click", "#ticket_list li.ticket-item", function () {
            $('.hall_tabs .nav-item').each(function() {
                $(this).css('pointer-events', 'none');
            });
            let current_elem = $(this);
            let current_input_elem = current_elem.find('input[type="radio"]');
            let selected_ticket_title = current_elem.find('label .item-title').text();
            if (!current_input_elem.prop('disabled')) {
                if (isLoggedIn) {
                    let selected_ticket = current_input_elem.val();
                    if (selected_ticket) {
                        $('#choose_ticket').removeClass('dropdown-toggle').html('<i class="fa fa-spinner fa-spin"></i> Loading...');

                        window.location.href = _taoh_site_url_root + '/events/add_rsvp/' + eventToken + '/' + selected_ticket + '/' + encodeCurrentUrl;
                    } else {
                        alert('Please select a ticket');
                    }
                }
                // update_choose_ticket(current_elem);
            } else {
                taoh_set_error_message(selected_ticket_title + ' ticket is not available for selection');
            }
        });

        $(document).on("click", ".event_save", function (event) {
            event.stopPropagation(); // Stop the event from propagating to the parent
            if(!isLoggedIn){
                 taoh_set_error_message('Login to perform the action.');
                return false;
            }
            var savetoken = $(this).attr('data-event');
            var contttoken = $(this).attr('data-cont');
            $('.events_like').find(`[data-cont='${contttoken}']`).attr('src', "<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bookmark-fill.svg");
            $('.events_like').find(`[data-cont='${contttoken}']`).removeClass('event_save').addClass("already-saved").removeAttr("style");
            $('.events_like').find(`[data-cont='${contttoken}']`).parent().addClass("already-saved").removeAttr("style");
            localStorage.setItem('events_' + savetoken + '_' + contttoken + '_liked', 1);
            delete_events_into('event_detail_'+savetoken);
            var data = {
                'taoh_action': 'event_like_put',
                'eventtoken': savetoken,
                'contttoken': contttoken,
                'ptoken': '<?php echo $ptoken ?? TAOH_API_TOKEN_DUMMY; ?>',
            };
            $.post(_taoh_site_ajax_url, data, function (response) {
                if (response.success) {
                    taoh_set_success_message('Event Saved Successfully.');
                } else {
                    taoh_set_error_message('Event Save Failed.');
                }
            }).fail(function () {
                console.log("Network issue!");
            })
        });

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


      
         $('#event_gallery_carousel').carousel({
                //interval: 2000
                auto:false,
                
        });

        setTimeout(() => {
                   var $carousel = $('#event_gallery_carousel'); // replace with your carousel ID
                    var totalItems = $carousel.find('.carousel-item').length;

                    if (totalItems <= 1) {
                        $carousel.find('.carousel-control-prev, .carousel-control-next').hide();
                    }
         }, 3000);

       
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

        <?php if(TAOH_DOJO_SUGGESTION_ENABLE) { ?>
             let timelimit = <?php echo (int)TAOH_DOJO_SUGGESTION_TIMELIMIT; ?>;
             let innertimelimit = Math.floor(timelimit / 2);
            console.log(timelimit+'------timelimit----------'+innertimelimit);
           
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
        //alert(shareUrl)
        if(shareUrl != '' && shareUrl != undefined){
             currentShareLink = shareUrl;
        }   
       
       
    });

    
    $(document).on("click", ".create_referral", function () {
        let event_title = $(this).data("title");
        let link = $(this).data("location");
        let data = {
            'taoh_action': 'taoh_invite_rsvp_type',
            'from_link' : link,
            'detail_link': window.location.href,
            'event_title' : event_title,
        };
        $.post(_taoh_site_ajax_url, data, function(response) {});
    });


    $(window).on('scroll', function() {
        var $sticky = $('.sticky-top-fixed');

        if ($sticky.length) {
            var top_sticky_pos = $sticky.offset().top;
            //console.log('top_sticky_pos', top_sticky_pos);
            if(top_sticky_pos > 126){
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

    $(document).on('click', '.register_now', function() {
        //alert(1)
         var dropdown = new bootstrap.Dropdown(document.getElementById('choose_ticket'));
        dropdown.show();
        $('#choose_ticket')[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        $('#choose_ticket').focus();
        $('#choose_ticket').trigger('click');
        const $btn = $('#choose_ticket');
        const $menu = $('#ticket_list');
        $menu.toggleClass('show');
    });
    

    $(".register_now").on('click', function () {
        alert(2)
        var dropdown = new bootstrap.Dropdown(document.getElementById('choose_ticket'));
        dropdown.show();
        $('#choose_ticket')[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        $('#choose_ticket').focus();
        $('#choose_ticket').trigger('click');
        const $btn = $('#choose_ticket');
        const $menu = $('#ticket_list');
        $menu.toggleClass('show');
    });

    </script>

<?php
taoh_get_footer();