<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
taoh_get_header();

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}



function check_live_now_token($input_array) {

    if(empty($input_array['output']['title']) || empty($input_array['output']['channels'])) {
        taoh_set_error_message('<b>Live Now is currently quiet.</b><br>Join a scheduled session or check back shortly — your next opportunity to connect is always just around the corner.');
        taoh_redirect (TAOH_SITE_URL_ROOT);
    }

    $title = ucwords($input_array['output']['title']);
    $description = ucwords($input_array['output']['description']);
    $channels = $input_array['output']['channels'];
    
    $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
    $full_loc_expl = explode(', ', $sess_user_info->full_location);
    $country = array_pop($full_loc_expl);

    $geo_enable = 0; 
    $dateHour = gmdate("YmdH"); 
    
    if($geo_enable) {
        $keyslug = hash( 'crc32',$title.$country.$dateHour );
    } else {
        $keyslug = hash( 'crc32', $title.$dateHour);	
    }
    
    return $keyslug;
	
}

$url = TAOH_LIVE_NOW_URL . '?y=' . gmdate("YmdH");
$url_data = @file_get_contents($url);

$data_arr = [];
if ($url_data !== false) {
    $data_arr = json_decode($url_data, true) ?? [];
} else {
    error_log("Failed to fetch data from URL: $url");
}

$pagename = 'networking';
$appname = TAOH_CURR_APP_SLUG ?? 'club';
$enable_convo = 1;

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
if (!$taoh_user_is_logged_in) {
    taoh_set_error_message('Please login to access ' . ($pagename ?? 'this') . ' page');
    taoh_redirect(TAOH_LOGIN_URL);
    taoh_exit();
}


$role = '';
$user_info_obj = taoh_user_all_info();

if(isset($user_info_obj->title)){
     error_reporting(E_ALL)   ;
    foreach ( $user_info_obj->title as $key => $value ){
       // echo "=========".$value;die();
         list ( $id, $role ) = explode( ':>', $value );
    }
   
}

if(isset($user_info_obj->skill)){
     error_reporting(E_ALL) ;
    foreach ( $user_info_obj->skill as $key => $value ){
       // echo "=========".$value;die();
         list ( $id, $skilll ) = explode( ':>', $value );
         $skillarray[] = $skilll;
    }
    $skill = implode(',', $skillarray);
   
}
//echo '<pre>'; print_r($skillarray); echo '</pre>';die();

$location = $user_info_obj->full_location ?? '';
$profile_type = $user_info_obj->type ?? '';

$profile_complete = (isset($user_info_obj->profile_complete) && $user_info_obj->profile_complete) ? $user_info_obj->profile_complete : 0;
if (!$profile_complete) {
    taoh_set_error_message('complete your settings to fully use the platform');
    taoh_redirect(TAOH_SITE_URL_ROOT . '/settings');
    taoh_exit();
}

$ice_break = getRandomIceBreakQuestions();
$parse_url_1 = taoh_parse_url(1);

$ntw_view = 1;
$timezone = $user_info_obj->local_timezone;

if ($parse_url_1 == 'room' || $parse_url_1 == 'forum' || $parse_url_1 == 'live') {
    $contslug = taoh_parse_url(2);    

    if (empty($contslug)) {
//        taoh_redirect(TAOH_SITE_URL_ROOT);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1002, 'networking');
        taoh_exit();
    }

    $contslug_arr = explode('-', $contslug);
    $keytoken = array_pop($contslug_arr);


} else if ($parse_url_1 == 'custom-room') {
    $custom_room_key = $keytoken = taoh_parse_url(3);
    if (empty($custom_room_key)) {
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }
}
else if ($parse_url_1 == 'dm') {
    $custom_room_key = $keytoken = taoh_parse_url(2);
    //echo "==custom_room_key====".$custom_room_key;die();
    if (empty($custom_room_key)) {
        taoh_redirect(TAOH_SITE_URL_ROOT);
        taoh_exit();
    }
    $ntw_view = 3;
} else {
    $contslug = ''; // :rk temp fix
    $geo_enable = 1;
    
    $date = new DateTime('now', new DateTimeZone($timezone));
    $abbreviation = $date->format('T');


    $abbreviation = strtoupper($abbreviation);
    $this_week = date('W');
    //echo "======".$this_week;die();


    if ($geo_enable) {
        $full_loc_expl = explode(', ', $user_info_obj->full_location);
        $country = array_pop($full_loc_expl);

        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH . $country.$this_week);
    } else {
        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH . $this_week);
    }
}

if (isset($_GET['chatwith']) && !empty($_GET['chatwith'])) {
    $chatwith = $_GET['chatwith'];

    //echo "===========".$chatwith;die();

    $chatwith_usr_json = taoh_get_user_info($chatwith, 'public');
    //echo '<pre>-------------';print_r($chatwith_usr_json);echo'</pre>';die();
    $chatwith_usr_arr = json_decode($chatwith_usr_json, true);
    if (!isset($chatwith_usr_arr['success']) || !$chatwith_usr_arr['success']) {
    //        $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    //        taoh_redirect($url);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1003);
        taoh_exit();
    } else {
        $ntw_view = 2;
    }
}



$sidekick_avatar = TAOH_SITE_URL_ROOT.'/assets/images/Group 194.svg';

if($ntw_view != 3){
   // https://ppapi.tao.ai/asqs.chatbot.ptoken?&token=OgeoAbdp&site_secret=wj62hr4i&botname=sidekick
    $taoh_call = 'asqs.chatbot.ptoken';
    $taoh_vals = array(
        "botname" => 'sidekick',
        "token" => taoh_get_api_token(1),
        "site_secret" => TAOH_API_SECRET,
        //'cfcc1h'=> 1, //cfcache newly added
    );

    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    $sidekick_data = json_decode($data, true);
    if(isset($sidekick_data['success'])  && $sidekick_data['success'] == true){
        $sidekick_ptoken = $sidekick_data['bot_ptoken'];
    }
}

$room_info = get_room_info($keytoken, $user_info_obj->ptoken);
$room_info_arr = json_decode($room_info, true);


//echo "<pre>"; print_r($room_info_arr); die;


$livenow_channels = "";
$fetch_live_channels = 0;

if (isset($room_info_arr['output']['club']['channel_list']) && !empty($room_info_arr['output']['club']['channel_list'])) {
    $livenow_channels = json_encode($room_info_arr['output']['club']['channel_list']);
    $fetch_live_channels = 1;
}

if (json_last_error() !== JSON_ERROR_NONE) {
//    taoh_redirect(TAOH_SITE_URL_ROOT);

    showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1004, 'networking');
    taoh_exit();
}

//echo '<pre>'; print_r($room_info_arr); echo '</pre>'; die;
//echo "======".$keytoken;die();
if (!isset($room_info_arr['success']) || !$room_info_arr['success'] || !$room_info_arr['output']) {
    if ($parse_url_1 == 'room') {
        // :rk room not exist alert need to show here
        // taoh_redirect(TAOH_SITE_URL_ROOT);

        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1005, 'networking');
        taoh_exit();
    }

    require_once TAOH_CORE_PATH . '/' . $appname . '/includes/club_room_data.php';

    if ($parse_url_1 == 'forum') {
        if (empty($_GET['t'])) {
            // :rk title not exist alert need to show here
            // taoh_redirect(TAOH_SITE_URL_ROOT);
            showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1006, 'networking');
            taoh_exit();
        }

        $title = base64url_decode($_GET['t']);

        if (empty($title)) {
            // :rk title not exist alert need to show here
            taoh_redirect(TAOH_SITE_URL_ROOT);
            showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1007, 'networking');
            taoh_exit();
        }

        $room_input_data = array(
            'keyslug' => $keytoken,
            'title' => $title,
            'geo_enable' => $geo_enable ?? 1
        );
        $room_data_arr = get_forum_room_data($room_input_data);
    } else {
        $title = TAOH_SITE_NAME_SLUG . ' Community Club Networking Hour';

        $room_input_data = array(
            'keyslug' => $keytoken,
            'title' => $title,
            'geo_enable' => $geo_enable ?? 1
        );
        $room_data_arr = get_networking_local_room_data($room_input_data);
    }
    $room_status_arr = create_room_info($room_data_arr, $user_info_obj->ptoken);
    if ($room_status_arr['success']) {
        $room_info_arr = $room_status_arr;
    } else {
        // taoh_redirect(TAOH_SITE_URL_ROOT);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1008, 'networking');
        taoh_exit();
    }
}

//echo "<pre>"; print_r($room_info_arr); 

$room_app = $room_info_arr['output']['app'] ?? '';
$sub_app = $room_info_arr['output']['sub_app'] ?? '';
$club_info = $room_info_arr['output']['club'] ?? '';


if (isset($room_app) && $room_app === 'event' && isset($club_info['streaming_link']) && !empty($club_info['streaming_link'])) { 
    $ntw_view = 5;
}

if($room_app == 'live_now') {
    $check_keytoken = check_live_now_token($data_arr);
    if($keytoken != $check_keytoken) {
        //taoh_redirect(TAOH_SITE_URL_ROOT);
        //taoh_exit();
    }
}

if ($parse_url_1 !== 'room' && $parse_url_1 !== 'forum' && $parse_url_1 !== 'custom-room' && $parse_url_1 !== 'dm' && $parse_url_1 !== 'live') {
    if (!empty($club_info) && isset($club_info['links']['club'])) {
        //echo "====i am here=====";die();
        if(isset($_GET['chatwith']) && $_GET['chatwith'] !='')
            taoh_redirect(TAOH_SITE_URL_ROOT . $club_info['links']['club'].'?chatwith='.$_GET['chatwith']);
        else
            taoh_redirect(TAOH_SITE_URL_ROOT . $club_info['links']['club']);
        taoh_exit();
    } else {
        // :rk invalid club info alert need to show here

        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1010, 'networking');
        taoh_exit();
    }
}

$who_can_create_video_room = array();$club_token = '';
$event_owner_ptoken = '';
$ptoken = $user_info_obj->ptoken;
$ticket_types_array = [];
$rsvp_slug = $event_channels = '';
$current_ticket_type = [];
$owner_org_ptoken = [];
$can_delete_all_msg = 0;

if (isset($room_app) && $room_app === 'event') {
    $club_info_mod_get = $room_info_arr['output']['club']['links']['detail'] ?? '';
    $explode_get = explode('/', $club_info_mod_get);
    $club_info_mod_filter = array_filter($explode_get);
    $club_token = array_pop($club_info_mod_filter);
    $explode_token = explode('-', $club_token);
    $club_token = array_pop($explode_token);

    $cache_name = 'event_detail_21_' . $club_token;
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'ops' => 'baseinfo',
        'mod' => 'events',
        'eventtoken' => $club_token ?? '',
        'cache_name' => $cache_name,
        'cache_time' => 7200,
        'cache' => array("name" => $cache_name, "ttl" => 7200),
        //'cfcc1d'=> 1, //cfcache newly added
    );
    $taoh_call = 'events.event.get';
    //$taoh_vals['cfcache'] = $cache_name;
    ksort($taoh_vals);
   // echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die();
    $response_det = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
    $event_arr = $response_det['output'];
    $create_channel_flag = 0;

    //$event_arr['conttoken']['live_link'] = "https://www.youtube.com/embed/TO97VtyPcdE";   

    if(isset($event_arr['conttoken']['event_organizer_ptokens']) && !empty($event_arr['conttoken']['event_organizer_ptokens'])) {
        $event_organizer_ptokens_arr = explode(",", $event_arr['conttoken']['event_organizer_ptokens']);
        $owner_org_ptoken = $event_organizer_ptokens_arr;
    }

    $owner_org_ptoken[] = $event_arr['conttoken']['ptoken'];
    //$owner_org_ptoken[] = $ptoken;
    

    if (in_array($ptoken, $owner_org_ptoken)) {
        $can_delete_all_msg = 1;
    }

    $sponsor_array = array();
    if(isset($event_arr['conttoken']['event_sponsor']))
    $sponsor_array = $event_arr['conttoken']['event_sponsor'];
    $event_owner_ptoken = $event_arr['conttoken']['ptoken'];
    $ticket_types_array = $event_arr['conttoken']['ticket_types'] ?? [];
    $ticket_types = json_encode($event_arr['conttoken']['ticket_types'] ?? []);
    $event_channels = json_encode($event_arr['conttoken']['event_channels'] ?? []);
    $event_title = $event_arr['conttoken']['title'] ?? '';
    if($event_owner_ptoken == $ptoken){
        // echo "<br> Event owner creation";
        $create_channel_flag = 1;
    }

    if(isset($sponsor_array) && count($sponsor_array) > 0){
        foreach($sponsor_array as $kkk=>$val){
            if(isset($val['ptoken']) && $val['ptoken'] !=''){
                array_push($who_can_create_video_room,$val['ptoken']);
                if( $val['ptoken'] == $ptoken){
                    // echo "<br> Event Sponsor creation";
                    $create_channel_flag = 1;
                }
            }
        }
    }
    array_push($who_can_create_video_room,$event_arr['conttoken']['ptoken']);


    $events_data = $event_arr['conttoken'] ?? [];
    $msg_from_owner = $events_data['msg_from_owner'] ?? '';
    if (empty($events_data['enable_conversation'])) {
        $enable_convo = 0;
    }
    $taoh_call = 'events.rsvp.get';
    $taoh_vals = array(
        'token' => TAOH_API_TOKEN,
        'ops' => 'rsvp',
        'mod' => 'events',
        'eventtoken' => $club_token,
        'cache_required' => 0,
        'time' => time()
    );
    $event_rsvp_data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);

    if (!empty($event_rsvp_data) && ($event_rsvp_data['success'] ?? null) == 1 && !empty($event_rsvp_data['output'])) {
        $rsvp_slug = $event_rsvp_data['output']['rsvp_slug'] ?? '';

        //$current_ticket_types = array_filter($ticket_types, fn($item) => $item['slug'] === $rsvp_slug);
        $current_ticket_types = [];
        foreach ($ticket_types_array as $item) {
            if ($item['slug'] === $rsvp_slug) {
                $current_ticket_types[] = $item;
            }
        }

        //print_r($ticket_types_array);
        //die();

        if (empty($current_ticket_types)) {
            taoh_redirect(TAOH_SITE_URL_ROOT );
            exit();
        } 

        $current_ticket_type = array_values($current_ticket_types)[0];
    }
    else{
        if(!TAOH_DEV_SITE){
        taoh_set_error_message('You Should RSVP to the event to access this page');
        taoh_redirect (TAOH_SITE_URL_ROOT.'/events/d/'.$event_title.'-'.$club_token);die();
        }
    }
}

$room_title = $room_info_arr['output']['club']['title'];

array_push($who_can_create_video_room, $user_info_obj->ptoken);

$create_channel_flag = 0;
// get event meta info
$eventtoken = $club_token;
$taoh_call = "events.content.get";
$search = $type = '';
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $club_token,
    //'cfcc1d'=> 1, //cfcache newly added
);
$event_meta_data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);

if(isset($event_meta_data['success']) && $event_meta_data['success'] == true){
    $event_meta_data = $event_meta_data['output'];
    $exhibitor_array = $event_meta_data['output']['event_exhibitor'] ?? [];
    if(isset($exhibitor_array) && count($exhibitor_array) > 0){
        foreach($exhibitor_array as $kkk=>$val){
            if(isset($val['ptoken']) && $val['ptoken'] !=''){
                if( $val['ptoken'] == $ptoken){
                    // echo "<br> Event exhibitor creation";
                    $create_channel_flag = 1;
                }

            }
        }
    }
}

$lat = '';
$long = '';
$radius = 2000;
$unit = 'km';
// $ptoken = $user_info_obj->ptoken;
$coordinates = $user_info_obj->coordinates;
if (!empty($coordinates)) {
    $co_array = explode('::', $coordinates);
    $lat = $co_array[0];
    $long = $co_array[1];
}

$show_video_conv_btn = isset($room_app) && $room_app === 'event' && (($events_data['disable_video_conversation'] ?? '') != '1');
$allow_auto_manage = isset($room_app) && $room_app === 'event' && (($events_data['auto_manage'] ?? '') == '1');
//echo "=========";die();
include_once TAOH_CORE_PATH . '/club/includes/ads_data.php';

$create_channel_flag = 1; // enable create channel for all users

$frm_asm_enable = defined('TAOH_ENABLE_FORUM_ASM') && TAOH_ENABLE_FORUM_ASM;
$frm_asm_indexes = [2,4,6];
$frm_asm_index_messages = [
    2 => 'Start a video call to start a group conversation',
    4 => 'Start a video call to start a group conversation',
    6 => 'Start a video call to start a group conference'
];

$ntw_asm_enable = defined('TAOH_ENABLE_NETWORK_ASM') && TAOH_ENABLE_NETWORK_ASM;
$ntw_asm_indexes = [2,4,6];
$ntw_asm_index_messages = [
    2 => 'Start a video call to start a conversation',
    4 => 'Start a video call to start a conversation',
    6 => 'Start a video call to start a conference'
];

const TAOH_CHAT_PREFIX = TAOH_SITE_URL_ROOT . '/assets';
/**Dojo navigate tracker */
$live_users_count = 10;
$last_message_time = strtotime("-20 minutes");
$current_time = time();
$one_on_one_started = false;
$other_profiles_available = 6;
$user_type = "Employer";
$job_posted = true;
$job_posted_date = strtotime("-10 days");
$job_shared = false;
$dojomessage = '';

$rules = DOJO_NETWORKING_MESSAGE;
/* foreach ($rules as $rule) {
    $conditions = $rule['conditions'];
    $triggered = true;

    foreach ($conditions as $key => $value) {
        switch ($key) {
            case 'message_posted':
                $actual = ($current_time - $last_message_time) <= 900;
                if ($actual !== $value) $triggered = false;
                break;

            case 'live_users_gte':
                if ($live_users_count < $value) $triggered = false;
                break;

            case 'one_on_one_started':
                if ($one_on_one_started !== $value) $triggered = false;
                break;

            case 'other_profiles_gt':
                if ($other_profiles_available <= $value) $triggered = false;
                break;

            case 'user_type':
                if ($user_type !== $value) $triggered = false;
                break;

            case 'job_posted':
                if ($job_posted !== $value) $triggered = false;
                break;

            case 'job_posted_within_days':
                $days = ($current_time - $job_posted_date) / (60 * 60 * 24);
                if ($days > $value) $triggered = false;
                break;

            case 'job_shared':
                if ($job_shared !== $value) $triggered = false;
                break;
        }

        if (!$triggered) break;
    }
    //echo "======ooooooo====>".$triggered.$rule['message'];
    if ($triggered) {
        $dojomessage = $rule['message'];
        showPopup($rule['message']);
        break; // Show only one message at a time
    }
} */
if(defined('TAOH_SPEEDNETWORKING_ENABLE') && TAOH_SPEEDNETWORKING_ENABLE == 1) {
    if (isset($room_app) && $room_app === 'event' && isset($club_info['streaming_link']) && !empty($club_info['streaming_link'])) 
        $speednetwork_enable = 0;
    else if(isset($sub_app) && $sub_app !='' && ($sub_app == 'session' || $sub_app == 'exhibitor'))
        $speednetwork_enable = 0;
    else if(defined('TAOH_SPEEDNETWORKING_ENABLE'))
        $speednetwork_enable = TAOH_SPEEDNETWORKING_ENABLE;
    else
        $speednetwork_enable = 0;
}


$my_following_list = [];
$my_following_ptoken_list = [];
if ($taoh_user_is_logged_in) {
    $my_ptoken = $user_info_obj->ptoken ?? '';

    $taoh_vals = [
        'mod' => 'core',
        'token' => taoh_get_api_token(),
        'ptoken' => $my_ptoken,
        'follow_type' => 'following',
    ];
    $taoh_vals['cache_name'] = 'followup_' . $taoh_vals['follow_type'] . '_list_' . $taoh_vals['ptoken'] . '_' . hash('crc32', http_build_query($taoh_vals));

    $taoh_vals['cache_required'] = 0;
//     $taoh_vals['debug_api'] = 1;
//     echo taoh_apicall_get('core.followup.get.list', $taoh_vals);exit();

    $followup_result = taoh_apicall_get('core.followup.get.list', $taoh_vals);
    $followup_result_array = json_decode($followup_result, true);
    if ($followup_result_array && in_array($followup_result_array['success'], [true, 'true']) && !empty($followup_result_array['output'])) {
        $my_following_list = (array)$followup_result_array['output'];
        $my_following_ptoken_list = array_column($my_following_list, 'ptoken');
    }
}
                            
?>

    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/node-waves/waves.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/tributejs/tribute.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/css/icons.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CHAT_PREFIX; ?>/chat/css/chat.css?v=<?php echo TAOH_CSS_JS_VERSION;?>">

    <style>
        .profile_follow_btn {
            background-color: transparent;
        }

        .profile_follow_btn[data-follow_status="1"] {
            background-color: #2557A7 !important;
            border: 1px solid #2557A7 !important;
            color: #ffffff !important;
        }

        .dropdown-menu a {
            cursor: pointer;
        }
        #zoom_out:disabled {
        background-color: #d7cece;
        }
        #zoom_in:disabled {
        background-color: #d7cece;
        }
        .tribute-container {
            margin: 10px 5px;
        }
        .tribute-container ul {
            min-width: 150px;
        }
        .tribute-container li.highlight {
            background: #0d6efd !important;
        }

        .collapsible {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .collapsible.open {
            max-height: 500px; /* adjust based on expected content size */
        }

        .channnel_collapsible {
           display:none;
            transition: max-height 0.3s ease;
        }

        .channnel_collapsible.open {
            display:block;
            max-height: 500px; /* adjust based on expected content size */
        }
         .more-fields {
        display: none;
        margin-top: 10px;
        }

        .messages-container {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            background: #e5ddd5;
        }

        .message {
            margin: 10px 0;
            padding: 10px 15px;
            border-radius: 8px;
            max-width: 70%;
            position: relative;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .message.sent {
            background: #dcf8c6;
            margin-left: auto;
        }

        .message-content {
            margin-bottom: 5px;
        }

        .message-reactions {
            display: flex;
            gap: 5px;
            margin-top: 5px;
            flex-wrap: wrap;
        }

        .reaction {
            background: rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 2px 6px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .emoji-btn {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .emoji-btn:hover {
            background: rgba(0,0,0,0.1);
        }

        .input-container {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-top: 1px solid #ddd;
            background: white;
            gap: 10px;
        }

        #message-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 14px;
        }

        .send-btn {
            background: #075e54;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Emoji Picker Styles */
        .emoji-picker {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 350px;
            height: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            z-index: 9999999;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .emoji-picker-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            color: #333;
        }

        .emoji-categories {
            display: flex;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 8px 0;
            justify-content: space-around;
        }

        .emoji-categories button {
            background: none;
            border: none;
            font-size: 18px;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .emoji-categories button:hover,
        .emoji-categories button.active {
            background: #e3f2fd;
        }

        .emoji-grid {
            padding: 15px;
            height: 280px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 8px;
        }

        .emoji-item {
            font-size: 22px;
            cursor: pointer;
            padding: 8px;
            text-align: center;
            border-radius: 8px;
            transition: background 0.2s;
            user-select: none;
        }

        .emoji-item:hover {
            background: #f0f0f0;
            transform: scale(1.1);
        }

        /* Reaction popup */
        .reaction-popup {
            position: absolute;
            bottom: 100%;
            /* left: 50%; 
            transform: translateX(-50%);*/
            background: white;
            border-radius: 25px;
            padding: 8px 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            display: none;
            z-index: 100;
            margin-bottom: 10px;
        }
        .chat-list.left .reaction-popup {
            left: 50px;
        }
        .chat-list.right .reaction-popup {
            right: 50px;
        }

        .reaction-popup::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: white;
        }

        .quick-reactions {
            display: flex;
            gap: 5px;
        }

        .quick-reaction {
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: transform 0.2s;
        }

        .quick-reaction:hover {
            transform: scale(1.2);
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            display: none;
            z-index: 999;
        }

        /* Scrollbar styling */
        .emoji-grid::-webkit-scrollbar {
            width: 6px;
        }

        .emoji-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .emoji-grid::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .emoji-grid::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .emoji_placeholder {
            cursor: pointer;
        }
        
    </style>

    <div class="bg-white">
        <header class="networking-mobile-sticky sticky-top bg-white border-bottom border-bottom-gray">
            <section class="hero-area bg-white shadow-sm overflow-hidden">
                <span class="stroke-shape stroke-shape-1"></span>
                <span class="stroke-shape stroke-shape-2"></span>
                <span class="stroke-shape stroke-shape-3"></span>
                <span class="stroke-shape stroke-shape-4"></span>
                <span class="stroke-shape stroke-shape-5"></span>
                <span class="stroke-shape stroke-shape-6"></span>
                <div class="container">
                    <?php
                    //include 'includes/club_header.php';
                    ?>
                    <div class="hero-content align-items-center justify-content-between">
                        <div class="col-lg-12">
                            <div class="row align-items-center">                                
                                <ul class="col-md-10 nav nav-tabs justify-content-left border-0 mt-4 mb-4 <?=($room_app == "live" ? 'd-none' : '')?>" role="tablist">
                                    <?php
                                    $link_count = count($club_info['breadcrumbs']);
                                    $last_crumb = $club_info['breadcrumbs'][$link_count - 1];

                                    /*if (str_contains($last_crumb['link'], 'networking')) {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME);
                                    } else {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . $last_crumb['link']);
                                    }*/
                                    $descc = $club_info['description'];
                                    $count = 1;
                                    //echo "==========".$link_count ;
                                    foreach ($club_info['breadcrumbs'] as $value) {

                                        $link = TAOH_SITE_URL_ROOT . $value['link'];
                                        
                                        if($count == count($club_info['breadcrumbs'])){
                                            define('TAOH_NETWORK_REFERRAL_URL', $link);
                                        }
                                        
                                        

                                        
                                        if($value['link'] == '#')
                                            $link = 'javascript:void(0);';
                                        echo '<li class="nav-item"  '.$count.'>
                                                    <a href="' . $link . '">' . $value['title'] . '</a>';
                                                 
                                        if($count <= $link_count)
                                            echo icon('chevron-right', '#000000', 19);
                                        
                                        echo '</li>';

                                        $count++;


                                    }

                                   

                                    if ($parse_url_1 == 'dm') {
                                       // define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . '/message/dm');
                                        echo '<li class="nav-item"></span>
                                            DM</li>';
                                    }
                                    else{
                                       
                                         echo '<li class="nav-item"></span>
                                            Networking</li>';
                                    }
                                    
                                    
                                    if (!empty($msg_from_owner) || !empty($club_info['msg_from_owner'])) {
                                        echo '<li class="nav-item ml-2"><span>| Note from organizer</span>';
                                        echo '<i class="fa fa-info-circle ml-2 cursor-pointer" data-toggle="collapse" data-target="#org_msg" title="View more info" aria-expanded="false" aria-controls="org_msg" style="color: #15a4f7; font-size: 20px;"></i>';
                                        echo '</li>';
                                    }
                                    
                                    if (isset($_GET['from'])) {
                                        echo '<li class="nav-item"><span><?= icon('chevron-right', '#000000', 19) ?></span>
                                            <a href="' . TAOH_SITE_URL_ROOT . '/club/room/' . $contslug . '">Networking</a></li>';
                                    }
                                    ?>
                                </ul>

                                <?php
                                if (false) { // $show_video_conv_btn  // Requirement: Hide it for now
                                    echo '<div class="col-md-2">';
                                    echo '<button class="btn btn-sm theme-btn w-100" id="video_room_btn" data-toggle="modal" data-target="#video_room_join_confirmation"><i class="fa fa-video-camera mr-1" aria-hidden="true"></i> Enter Video Room</button>';
                                    echo '</div>';
                                }
                                
                                ?>
                                <!--<div class="col-md-2">
                                    <button class="btn btn-primary btn-sm theme-btn w-100" data-toggle="modal" data-target="#reportBugModal">
                                        Report an issue</button>
                                    
                                </div>-->
                            </div>
                        </div>
                        


                            
                        <div class="row">
                            <div class="col-lg-8" id="breadcrumbs_accordion">
                                <div id="demo" class="collapse" data-parent="#breadcrumbs_accordion">
                                    <?php echo html_entity_decode($descc); ?>
                                </div>

                                <?php
                                if (!empty($msg_from_owner)) { ?>
                                    <div id="org_msg" class="collapse" data-parent="#breadcrumbs_accordion">
                                        <?php echo html_entity_decode($msg_from_owner); ?>
                                    </div>
                                    <?php
                                }

                                if (!empty($club_info['msg_from_owner'])) { ?>
                                    <div id="org_msg" class="collapse" data-parent="#breadcrumbs_accordion">
                                        <?php echo html_entity_decode($club_info['msg_from_owner']); ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="col-lg-4">

                            </div>

                        </div>
                    </div><!-- end hero-content -->
                </div><!-- end container -->
            </section>
        </header>
    </div>

    <div class="layout-wrapper chat-layout d-lg-flex bg-white" data-bs-theme="light">

        <!-- Start left sidebar-menu -->
        <div class="side-menu flex-lg-column" style="display:none">
            <!-- Start side-menu nav -->
            <div class="flex-lg-column my-2 sidemenu-navigation">
                <ul class="nav nav-pills side-menu-nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-chat-tab" data-bs-toggle="pill" href="#pills-chat" role="tab">
                            <i class="ri-home-2-line"></i>
                            <span class="badge bg-danger fs-11 rounded-pill sidenav-item-badge">9</span>
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="pills-contacts-tab" data-bs-toggle="pill" href="#pills-contacts" role="tab">
                            <i class="ri-contacts-book-line"></i>
                        </a>
                    </li>  -->
                    <li class="nav-item">
                        <a class="nav-link" id="pills-bookmark-tab" data-bs-toggle="pill" href="#pills-bookmark" role="tab">
                            <i class="ri-bookmark-3-line"></i>
                        </a>
                    </li>
                    <!-- <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" id="pills-setting-tab" data-bs-toggle="pill" href="#pills-setting" role="tab">
                            <i class="ri-settings-4-line"></i>
                        </a>
                    </li> -->
                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" id="pills-user-tab" data-bs-toggle="pill" href="#pills-user" role="tab">
                            <i class="ri-user-3-line"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- end side-menu nav -->
        </div>
        <!-- end left sidebar-menu -->
        

         <!-- Emoji Picker -->
        <div id="emoji-picker" class="emoji-picker">
            <div class="emoji-picker-header">
                Choose an emoji
            </div>
            <div class="emoji-categories">
                <button onclick="showCategory('smileys')" class="active" data-category="smileys">😀</button>
                <button onclick="showCategory('people')" data-category="people">👥</button>
                <button onclick="showCategory('nature')" data-category="nature">🌿</button>
                <button onclick="showCategory('food')" data-category="food">🍔</button>
                <button onclick="showCategory('activities')" data-category="activities">⚽</button>
                <button onclick="showCategory('travel')" data-category="travel">✈️</button>
                <button onclick="showCategory('objects')" data-category="objects">💡</button>
                <button onclick="showCategory('symbols')" data-category="symbols">❤️</button>
            </div>
            <div class="emoji-grid" id="emoji-grid">
                <!-- Emojis will be populated here -->
            </div>
        </div>

        <!-- start chat-leftsidebar -->
        <div class="chat-leftsidebar height-unset">

            <div class="tab-content">
                <!-- Start Profile tab-pane -->
                <div class="tab-pane" id="pills-user" role="tabpanel" aria-labelledby="pills-user-tab">
                    <!-- Start profile content -->
                    <div>
                        <div class="user-profile-img">
                            <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/profile_cover.jpg" class="profile-img" style="height: 160px;" alt="">
                            <div class="overlay-content">
                                <div>
                                    <div class="user-chat-nav p-2 ps-3">

                                        <div class="d-flex w-100 align-items-center">
                                            <div class="flex-grow-1">
                                                <h5 class="text-white mb-0">My Profile</h5>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <div class="dropdown">
                                                    <button class="btn nav-btn text-white" type="button"
                                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                           href="#">Info <i class="bx bx-info-circle ms-2 text-muted"></i></a>
                                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                           href="#">Setting <i class="bx bx-cog text-muted ms-2"></i></a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                           href="#">Help <i class="bx bx-help-circle ms-2 text-muted"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center border-bottom border-bottom-dashed pt-2 pb-4 mt-n5 position-relative">
                            <div class="mb-lg-3 mb-2">
                                <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/users/avatar-maxresdefault.jpg" class="rounded-circle avatar-lg img-thumbnail" alt="">
                            </div>

                            <h5 class="fs-17 mb-1 text-truncate">Dushane Daniel</h5>
                            <p class="text-muted fs-14 text-truncate mb-0">Front end Developer</p>
                        </div>
                        <!-- End profile user -->

                        <!-- Start user-profile-desc -->
                        <div class="p-4 profile-desc" data-simplebar>
                            <div class="text-muted">
                                <p class="mb-3">A professional profile is an introductory section on your resume that highlights your
                                    relevant qualifications and skills.</p>
                            </div>

                            <div class="border-bottom border-bottom-dashed mb-4 pb-2">
                                <div class="d-flex py-2 align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="bx bx-user align-middle text-muted fs-19"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">Dushane Daniel</p>
                                    </div>
                                </div>

                                <div class="d-flex py-2 align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-phone-line align-middle text-muted fs-19"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">+(365) 1456 12584</p>
                                    </div>
                                </div>

                                <div class="d-flex py-2 align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-message-2-line align-middle text-muted fs-19"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="fw-medium mb-0">dushanedaniel@gmail.com</p>
                                    </div>
                                </div>

                                <div class="d-flex py-2 align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <i class="ri-map-pin-2-line align-middle text-muted fs-19"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0">California, USA</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-bottom border-bottom-dashed mb-4 pb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-12 text-muted text-uppercase mb-0">Media</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="#" class="fw-medium fs-12 d-block">Show all</a>
                                    </div>
                                </div>
                                <div class="profile-media-img">
                                    <div class="media-img-list">
                                        <a href="#">
                                            <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/small/img-maxresdefault.jpg" alt="media img" class="img-fluid">
                                        </a>
                                    </div>
                                    <div class="media-img-list">
                                        <a href="#">
                                            <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/small/img-2.jpg" alt="media img" class="img-fluid">
                                        </a>
                                    </div>
                                    <div class="media-img-list">
                                        <a href="#">
                                            <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/small/img-4.jpg" alt="media img" class="img-fluid">
                                            <div class="bg-overlay">+ 15</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-12 text-muted text-uppercase mb-0">Attached Files</h5>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="#" class="fw-medium fs-12 d-block">Show all</a>
                                    </div>
                                </div>
                                <div>
                                    <div class="card p-2 border border-dashed mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 ms-1 me-3">
                                                <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/pdf-file.png" alt="" class="avatar-xs">
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-14 text-truncate mb-1">design-phase-1-approved.pdf</h5>
                                                <p class="text-muted fs-13 mb-0">12.5 MB</p>
                                            </div>

                                            <div class="flex-shrink-0 ms-3">
                                                <div class="d-flex gap-2">
                                                    <div>
                                                        <a href="#" class="text-muted px-1">
                                                            <i class="bx bxs-download"></i>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <a class="text-muted px-1" href="#" role="button"
                                                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card p-2 border border-dashed mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 ms-1 me-3">
                                                <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/image-file.png" alt="" class="avatar-xs">
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-14 text-truncate mb-1">Image-maxresdefault.jpg</h5>
                                                <p class="text-muted fs-13 mb-0">4.2 MB</p>
                                            </div>

                                            <div class="flex-shrink-0 ms-3">
                                                <div class="d-flex gap-2">
                                                    <div>
                                                        <a href="#" class="text-muted px-1">
                                                            <i class="bx bxs-download"></i>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <a class="text-muted px-1" href="#" role="button"
                                                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card p-2 border border-dashed mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 ms-1 me-3">
                                                <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/image-file.png" alt="" class="avatar-xs">
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-14 text-truncate mb-1">Image-2.jpg</h5>
                                                <p class="text-muted fs-13 mb-0">3.1 MB</p>
                                            </div>

                                            <div class="flex-shrink-0 ms-3">
                                                <div class="d-flex gap-2">
                                                    <div>
                                                        <a href="#" class="text-muted px-1">
                                                            <i class="bx bxs-download"></i>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <a class="text-muted px-1" href="#" role="button"
                                                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card p-2 border border-dashed mb-0">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 ms-1 me-3">
                                                <img src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/images/zip-file.png" alt="" class="avatar-xs">
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h5 class="fs-14 text-truncate mb-1">Landing-A.zip</h5>
                                                <p class="text-muted fs-13 mb-0">6.7 MB</p>
                                            </div>

                                            <div class="flex-shrink-0 ms-3">
                                                <div class="d-flex gap-2">
                                                    <div>
                                                        <a href="#" class="text-muted px-1">
                                                            <i class="bx bxs-download"></i>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <a class="text-muted px-1" href="#" role="button"
                                                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-horizontal-rounded"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Share <i class="bx bx-share-alt ms-2 text-muted"></i></a>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Bookmark <i class="bx bx-bookmarks text-muted ms-2"></i></a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item d-flex align-items-center justify-content-between"
                                                               href="#">Delete <i class="bx bx-trash ms-2 text-muted"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- end user-profile-desc -->
                    </div>
                    <!-- End profile content -->
                </div>
                <!-- End Profile tab-pane -->

                <!-- Start chats tab-pane -->
                <div class="tab-pane show active" id="pills-chat" role="tabpanel" aria-labelledby="pills-chat-tab">
                    <!-- Start chats content -->
                    <div>
                        <div class="chat-room-list" data-simplebar>

                           
                            <button class="btn row ml-3 my-3 wp-en">
                                <i class="fas fa-bars fs-24"></i>
                            </button>
                            

                            <!-- Start Participant -->
                            <div class="w-hide d-none">

                                <div class="left-section d-none align-items-center px-4 mt-4 mb-2">
                                    <div class="flex-grow-1 mt-2 mb-2 " >
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase"> What's On Your Mind?</h4>
                                    </div>
                                     <!-- right side bar access btn  -->
                                    <div class="flex-shrink-0 d-xl-none">
                                        <div class="dropdown user-chat-nav">
                                            <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" style="">
                                                <a class="dropdown-item d-flex justify-content-between align-items-center chatlist-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i></a>
                                            </div>
                                        </div>   
                                    </div>
                                </div>
                                <div class="left-section  status_div ">                            
                                    <p style="position: relative" class="d-flex update-status px-3">
                                        <span id="loadEmoji"><img onclick="openStatusModal();" src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/emojis/default.svg';?>" alt="Emoji" id="loadEmojiImg" class="update-image" style="left: 26px;"></span>
                                        <input class="form-control pl-5 light-dark-card" type="text" maxlength="140" value="" name="my_status" id="my_status" placeholder="Say something">
                                    </p>
                                </div>
                            </div>

                            <div data-intro="Find all the participants here" data-step="1" class="w-hide d-none">
                                <div class="left-section d-flex align-items-center px-4 mt-4 mb-2">
                                    <div class="flex-grow-1 mt-2 mb-2 participants_refresh" >
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase">Participants</h4>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Refresh">

                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-success btn-sm participants_refresh" id="participants_refresh">
                                                <i class="fa fa-refresh align-middle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="left-section  participant-list">
                                    <ul class="list-unstyled chat-list chat-user-list mb-3" id="participantsList">

                                    </ul>
                                </div>
                            </div>


                            <!-- Start chat-message-list -->
                            <div data-intro="Find Channels and start a discussion in the channel. You can create a new channel if interested." data-step="5">
                                <div class="w-hide  d-none">
                                    <div class="left-section  d-flex align-items-center px-4 mt-4 mb-2">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-0 fs-11 text-muted text-uppercase">Channels</h4>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div>
                                                <!-- Button trigger modal -->
                                                <button data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Refresh" type="button" class="btn btn-success btn-sm" id="channel_refresh">
                                                    <i class="fa fa-refresh align-middle"></i>
                                                </button>

                                                <!-- Button trigger modal -->
                                                <?php if($create_channel_flag == 1){ ?>
                                                    <button data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Create group" type="button" 
                                                    class="create_channel btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#createChannelModal">
                                                        <i class="bx bx-plus align-middle"></i>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="left-section  chat-message-list  d-none">

                                
                                    <ul class="watchpartyChannel list-unstyled chat-list chat-user-list" id="watch_partyChannel"><!-- data-intro="You can also join the video call on the channel by clicking camera button." data-step="6" -->

                                    </ul>
                                    <ul class="channelList list-unstyled chat-list chat-user-list mb-3" id="channelList"><!-- data-intro="You can also join the video call on the channel by clicking camera button." data-step="6" -->

                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex align-items-center px-4 mt-4 mb-2" id="dm-block">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0 fs-11 text-muted text-uppercase w-hide">Direct Messages</h4>
                                    <h4 class="mb-0 fs-11 text-muted text-uppercase w-show">DM'S</h4>
                                </div>
                                <div class="flex-shrink-0">
                                    <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="New Message">

                                        <!-- Button trigger modal -->
                                        <button style="display:none;" type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target=".contactModal">
                                            <i class="bx bx-plus align-middle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-message-list">
                                <ul class="usersList list-unstyled chat-list chat-user-list" id="usersList-block">

                                </ul>
                            </div>
                            <!-- End chat-message-list -->
                            

                            <?php if(ORGANIZER_CHANNEL_ENABLE && $event_owner_ptoken !='') { ?>
                                <div class="d-flex align-items-center px-4 mt-4 mb-2 d-none" id="organizer-block">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase">Organizer</h4>
                                    </div>

                                </div>


                                <div class="chat-message-list ">
                                    <ul class="orgChannelList list-unstyled chat-list chat-user-list" id="organizerChatList">
                                           

                                            
                                    </ul>
                                    <?php if($can_delete_all_msg ) { ?>
                                            <div class="m-3 p-3 w-hide">
                                                <a target="_blank" href="<?php echo TAOH_SITE_URL_ROOT; ?>/events/master/<?php echo $club_token;?>" class="btn btn-primary btn-sm mt-2" id="go_to_masternetworking_room">Go to Master Networking Room</a>
                                            </div>
                                    <?php } ?>
                                </div>


                            <?php } ?>

                            <?php if(SIDEKICK_CHANNEL_ENABLE && !empty($sidekick_ptoken)) { ?>
                                <div class="d-flex align-items-center px-4 mt-4 mb-2 " id="chatbot-block">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase">Chatbot</h4>
                                    </div>

                                </div>


                                <div class="chat-message-list ">
                                    <ul class="list-unstyled chat-list chat-user-list" id="sidekickList">
                                            <li id="dm-sidekick" data-ptoken="<?php echo $sidekick_ptoken; ?>" data-chatwith="<?php echo $sidekick_ptoken; ?>" data-name="sidekick" class="" data-channel_id="sidekick">
                                                <a href="javascript: void(0);">
                                                    <div class="d-flex align-items-center">
                                                        <div class="chat-user-img  align-self-center me-2 ms-0">
                                                            <img src="<?php echo $sidekick_avatar;?>" class="rounded-circle avatar-xs" alt="user-avatar">
                                                        </div>
                                                        <div class="overflow-hidden me-2">
                                                            <p class="text-truncate chat-username text-capitalize mb-0">TeamDojo</p>
                                                            <p class="text-truncate text-muted fs-13 mb-0"></p>
                                                        </div>
                                                        <div class="ms-auto">
                                                            <span class="badge badge-soft-danger rounded p-1 fs-12 unread-count" data-count="0">0</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                    </ul>
                                </div>
                            <?php } ?>
                           

                        </div>

                    </div>
                    <!-- Start chats content -->
                </div>
                <!-- End chats tab-pane -->


            </div>
            <!-- end tab content -->
        </div>
        <!-- end chat-leftsidebar -->

        <!-- Start User chat -->
        <div class="user-chat w-100 overflow-hidden mobile-transform" id="user-chat">

            <div class="chat-content d-lg-flex">
                <!-- start chat conversation section -->
                <div class="w-100 overflow-hidden position-relative bo-lf-rt">
                    <input type="hidden" id="channel_of_type" name="channel_of_type" value="">
                    
                    <!-- participants list margin: auto; -->
                    <div id="participants" class="position-relative" style="display: none; max-width: 1200px;">
                        <div class="px-4 py-3 pb-xl-0">
                            <div class="d-flex justify-content-between d-xl-none">
                                <div class="flex-shrink-0 mr-3 mb-3">
                                    <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                        <i class="bx bx-chevron-left align-middle text-white"></i>
                                    </a>
                                </div>
                                <!-- <button type="button" class="btn nav-btn btn-primary networking-sidebar-show">
                                    <i class="bx bx-menu-alt-right text-white"></i>
                                </button> -->

                                <div class="dropdown user-chat-nav">
                                    <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none networking-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i></a>
                                        <!-- <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a> -->
                                    </div>
                                </div>   


                            </div>
                            <h4>PARTICIPANTS</h4>
                            <form  data-intro="Search through participants by location, company name, role and skills to connect" data-step="2" id="searchFilter" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1" style="width: 100%; max-width: 663px;">
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="d-flex flex-wrap align-items-center flex-grow-1">
                                        <div class="form-group mr-3 flex-grow-1">
                                            <input name='query' class="form-control form--control pl-40px alphanumericInput" type="text" id="query" placeholder="Search for (skill, role or organization)">
                                            <span class="la la-search input-icon"></span>
                                        </div>

                                    </div><!-- end d-flex -->
                                    <div class="mb-3 mr-2 search-btn-box">
                                        <button class="btn search theme-btn" id="search" type="submit">Search<i class="la la-search ml-1"></i></button>
                                    </div><!-- end search-btn-box -->
                                    <?php
                                    if($club_info['geo_enable']){
                                        ?>
                                        <div class="mb-3 zooming-section">
                                            <button id="zoom_in" type="button" onclick="zoomin()" class="p-2 zoom-buttons" title="Search people in larger 8-8"><i class="la la-plus"></i></button>
                                            <input type="hidden" name="radius" id="radius" value="<?php echo $radius;?>" />
                                            <?php
                                            //<?php echo "<span>".$unit."</span>";
                                            ?>
                                            <button id="zoom_out" type="button" onclick="zoomout()" class="p-2 zoom-buttons" title="Search people in a smaller radius"><i class="la la-minus"></i></button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>

                            <div id='loaderArea-participant' class="text-center"></div>

                                <!-- <div class="participants-v2-list px-lg-5 px-3 py-3 shadow-sm d-none">
                                    <img class="p-v2-list-profile" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-4.png" alt="">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between flex-grow-1 mb-2" style="gap: 12px;">
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 9px;">
                                                <span class="prt-v2-name">Abdul - Frontend Dev</span>
                                                
                                                <div class="d-flex align-items-center flex-wrap" style="gap: 9px;">
                                                    <span class="round-con pro-type">P</span>
                                                
                                                    <span class="round-con ticket-type">
                                                        <svg width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.55556 0C0.697569 0 0 0.672656 0 1.5V3C0 3.20625 0.179861 3.36797 0.381597 3.43594C0.838542 3.58828 1.16667 4.00781 1.16667 4.5C1.16667 4.99219 0.838542 5.41172 0.381597 5.56406C0.179861 5.63203 0 5.79375 0 6V7.5C0 8.32734 0.697569 9 1.55556 9H12.4444C13.3024 9 14 8.32734 14 7.5V6C14 5.79375 13.8201 5.63203 13.6184 5.56406C13.1615 5.41172 12.8333 4.99219 12.8333 4.5C12.8333 4.00781 13.1615 3.58828 13.6184 3.43594C13.8201 3.36797 14 3.20625 14 3V1.5C14 0.672656 13.3024 0 12.4444 0H1.55556ZM3.11111 2.625V6.375C3.11111 6.58125 3.28611 6.75 3.5 6.75H10.5C10.7139 6.75 10.8889 6.58125 10.8889 6.375V2.625C10.8889 2.41875 10.7139 2.25 10.5 2.25H3.5C3.28611 2.25 3.11111 2.41875 3.11111 2.625ZM2.33333 2.25C2.33333 1.83516 2.6809 1.5 3.11111 1.5H10.8889C11.3191 1.5 11.6667 1.83516 11.6667 2.25V6.75C11.6667 7.16484 11.3191 7.5 10.8889 7.5H3.11111C2.6809 7.5 2.33333 7.16484 2.33333 6.75V2.25Z" fill="#BC1A53"/>
                                                        </svg>
                                                    </span>
                                                
                                                    <span class="round-con location">
                                                        <svg width="10" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#6F42C1"/>
                                                        </svg>
                                                    </span>

                                                    <span class="round-con company">
                                                        <svg width="9" height="12" viewBox="0 0 9 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.125 0C0.503906 0 0 0.503906 0 1.125V10.875C0 11.4961 0.503906 12 1.125 12H3.375V10.125C3.375 9.50391 3.87891 9 4.5 9C5.12109 9 5.625 9.50391 5.625 10.125V12H7.875C8.49609 12 9 11.4961 9 10.875V1.125C9 0.503906 8.49609 0 7.875 0H1.125ZM1.5 5.625C1.5 5.41875 1.66875 5.25 1.875 5.25H2.625C2.83125 5.25 3 5.41875 3 5.625V6.375C3 6.58125 2.83125 6.75 2.625 6.75H1.875C1.66875 6.75 1.5 6.58125 1.5 6.375V5.625ZM4.125 5.25H4.875C5.08125 5.25 5.25 5.41875 5.25 5.625V6.375C5.25 6.58125 5.08125 6.75 4.875 6.75H4.125C3.91875 6.75 3.75 6.58125 3.75 6.375V5.625C3.75 5.41875 3.91875 5.25 4.125 5.25ZM6 5.625C6 5.41875 6.16875 5.25 6.375 5.25H7.125C7.33125 5.25 7.5 5.41875 7.5 5.625V6.375C7.5 6.58125 7.33125 6.75 7.125 6.75H6.375C6.16875 6.75 6 6.58125 6 6.375V5.625ZM1.875 2.25H2.625C2.83125 2.25 3 2.41875 3 2.625V3.375C3 3.58125 2.83125 3.75 2.625 3.75H1.875C1.66875 3.75 1.5 3.58125 1.5 3.375V2.625C1.5 2.41875 1.66875 2.25 1.875 2.25ZM3.75 2.625C3.75 2.41875 3.91875 2.25 4.125 2.25H4.875C5.08125 2.25 5.25 2.41875 5.25 2.625V3.375C5.25 3.58125 5.08125 3.75 4.875 3.75H4.125C3.91875 3.75 3.75 3.58125 3.75 3.375V2.625ZM6.375 2.25H7.125C7.33125 2.25 7.5 2.41875 7.5 2.625V3.375C7.5 3.58125 7.33125 3.75 7.125 3.75H6.375C6.16875 3.75 6 3.58125 6 3.375V2.625C6 2.41875 6.16875 2.25 6.375 2.25Z" fill="#555555"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                                
                                            </div>
                                            <a href="#" class="chat-btn-v2"> 
                                                <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.90034 6.60022C6.05478 6.60022 7.80047 5.12267 7.80047 3.30011C7.80047 1.47755 6.05478 0 3.90034 0C1.74589 0 0.000205215 1.47755 0.000205215 3.30011C0.000205215 4.02389 0.27584 4.69328 0.74273 5.23893C0.677103 5.41518 0.5796 5.57081 0.476471 5.70207C0.386468 5.81832 0.29459 5.90832 0.227088 5.9702C0.193337 6.0002 0.165211 6.02458 0.14646 6.03958C0.137085 6.04708 0.129585 6.0527 0.125834 6.05458L0.122084 6.05833C0.0189558 6.13521 -0.0260457 6.27021 0.0152057 6.39209C0.0564571 6.51397 0.170836 6.60022 0.300215 6.60022C0.708979 6.60022 1.12149 6.49522 1.46463 6.36584C1.63714 6.30021 1.79839 6.22709 1.93902 6.15208C2.51467 6.43709 3.18406 6.60022 3.90034 6.60022ZM8.40049 3.30011C8.40049 5.40581 6.5423 6.99211 4.34098 7.18149C4.79662 8.57654 6.30792 9.60032 8.10048 9.60032C8.81675 9.60032 9.48615 9.43719 10.0637 9.15218C10.2043 9.22719 10.3637 9.30031 10.5362 9.36594C10.8793 9.49532 11.2918 9.60032 11.7006 9.60032C11.83 9.60032 11.9462 9.51595 11.9856 9.39219C12.025 9.26844 11.9819 9.13343 11.8769 9.05656L11.8731 9.05281C11.8694 9.04906 11.8619 9.04531 11.8525 9.03781C11.8337 9.02281 11.8056 9.0003 11.7719 8.96843C11.7044 8.90655 11.6125 8.81655 11.5225 8.70029C11.4193 8.56904 11.3218 8.41153 11.2562 8.23715C11.7231 7.69339 11.9987 7.02399 11.9987 6.29834C11.9987 4.55828 10.4068 3.13136 8.38736 3.0076C8.39486 3.10323 8.39861 3.20073 8.39861 3.29824L8.40049 3.30011Z" fill="#555555"/>
                                                </svg>
                                                <span>Chat</span>
                                            </a>
                                        </div>

                                        <div class="skills-v2-con">
                                            <span class="skill-v2 rounded-pill">PHP</span>
                                            <span class="skill-v2 rounded-pill">HTML 5</span>
                                            <span class="skill-v2 rounded-pill">JS</span>
                                            <span class="skill-v2 rounded-pill show-skill">+2</span>
                                        </div>
                                    </div>
                                </div> -->

                            <div class="list-scroll-onmobile" id="networkArea-participant"></div> <!-- height: calc(100vh - 293px); overflow-y: auto; scrollbar-width: thin; width: 100%; max-width: 673px; -->                            

                        </div>
                    </div>

                    <div class="px-4 py-3 pb-xl-0" id="welcome-page">
                        <div class="list-scroll-onmobile">
                            <div class="card card-item">
                                <div class="card-body" style="text-align: justify;font-size: 20px;">
                                    <div class="col-lg-12" style="text-align: center;">
                                        <img class="no-network-place" src="<?php echo TAOH_CDN_MAIN_PREFIX.'/images/empty_network.png'; ?>" width="300" alt="no-network">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- speed networking list margin: auto; -->
                    <div id="speed_networking" class="position-relative" style="display: none; max-width: 1200px;">
                        <div>
                            
                            <div class="py-3 user-chat-topbar">
                                <div class="row align-items-center flex-nowrap">
                                    <div class="col">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 d-block d-xl-none me-3">
                                                <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                                    <i class="bx bx-chevron-left align-middle text-white"></i>
                                                </a>
                                            </div>
                                            
                                            <div class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex align-items-center">
                                                   
                                                    <div class="flex-shrink-0 chat-user-img me-3">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-primary text-white">
                                                                <span class="username">SN</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                           
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h6 class="d-flex align-items-center mb-0 fs-18" style="gap: 6px;">
                                                            <span class="text-capitalize text-truncate">Speed Networking</span>
                                                            <button type="button" toggle_text="open" class="channel_toggle btn box-shadow-none p-0 d-none">
                                                                <svg class="channel-drp-dwn-svg" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg" style="transform: rotate(0deg);">
                                                                    <path d="M5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0ZM2.63672 4.70703C2.45312 4.52344 2.45312 4.22656 2.63672 4.04492C2.82031 3.86328 3.11719 3.86133 3.29883 4.04492L4.99805 5.74414L6.69727 4.04492C6.88086 3.86133 7.17773 3.86133 7.35938 4.04492C7.54102 4.22852 7.54297 4.52539 7.35938 4.70703L5.33203 6.73828C5.14844 6.92188 4.85156 6.92188 4.66992 6.73828L2.63672 4.70703Z" fill="black"></path>
                                                                </svg>
                                                            </button>                                                            
                                                        </h6>
                                                        <span class="channnel_collapsible fs-14 text-muted lh-1 py-1 d-none">more info</span>
                                                        <p class="channel_members_count text-truncate text-muted mb-0 d-none">1 Member(s)</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <ul class="list-inline">
                                            <li class="list-inline-item me-2 ms-0">
                                                <div class="dropdown user-chat-nav d-xl-none">
                                                    <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center networking-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                          
                                        </ul>
                                    </div>
                                </div>
                            </div>                                        

                            <!-- new template speed networking start  -->

                            <div class="loaderArea"></div>

                            <div class="speed_networking_div container py-3 px-lg-4 d-none">
                                <h6 class="mb-3 fs-21 fw-400">Browse Participants and connect with someone who shares your spark!</h6>

                                <div id="contentCarousel" class="carousel slide speed-carousel px-3 py-4">
                                    <!-- Carousel inner content -->
                                    <div class="carousel-inner speed_networking_carousel"></div>
                                    <!-- Controls (arrows) -->
                                    <button class="carousel-control-prev" type="button" data-bs-target="#contentCarousel" data-bs-slide="prev">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 11C22 13.9174 20.8411 16.7153 18.7782 18.7782C16.7153 20.8411 13.9174 22 11 22C8.08262 22 5.28473 20.8411 3.22182 18.7782C1.15892 16.7153 0 13.9174 0 11C0 8.08262 1.15892 5.28473 3.22182 3.22183C5.28473 1.15893 8.08262 0 11 0C13.9174 0 16.7153 1.15893 18.7782 3.22183C20.8411 5.28473 22 8.08262 22 11ZM11.6445 16.1992C12.0484 16.6031 12.7016 16.6031 13.1012 16.1992C13.5008 15.7953 13.5051 15.1422 13.1012 14.7426L9.36289 11.0043L13.1012 7.26602C13.5051 6.86211 13.5051 6.20898 13.1012 5.80937C12.6973 5.40977 12.0441 5.40547 11.6445 5.80937L7.17578 10.2695C6.77188 10.6734 6.77188 11.3266 7.17578 11.7262L11.6445 16.1992Z" fill="#555555"/>
                                        </svg>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#contentCarousel" data-bs-slide="next">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 11C0 13.9174 1.15893 16.7153 3.22183 18.7782C5.28473 20.8411 8.08262 22 11 22C13.9174 22 16.7153 20.8411 18.7782 18.7782C20.8411 16.7153 22 13.9174 22 11C22 8.08262 20.8411 5.28473 18.7782 3.22183C16.7153 1.15893 13.9174 0 11 0C8.08262 0 5.28473 1.15893 3.22183 3.22183C1.15893 5.28473 0 8.08262 0 11ZM10.3555 16.1992C9.95156 16.6031 9.29844 16.6031 8.89883 16.1992C8.49922 15.7953 8.49492 15.1422 8.89883 14.7426L12.6371 11.0043L8.89883 7.26602C8.49492 6.86211 8.49492 6.20898 8.89883 5.80937C9.30273 5.40977 9.95586 5.40547 10.3555 5.80937L14.8242 10.2695C15.2281 10.6734 15.2281 11.3266 14.8242 11.7262L10.3555 16.1992Z" fill="#555555"/>
                                        </svg>
                                    </button>
                                </div>

                            </div>


                            <!-- zero day speed -->
                            <div class="zeroday-speed py-5 px-4 d-none">

                                <div class="card">
                                    <div class="card-body py-4">

                                        <!-- svg -->
                                        <svg style="width: 100%; max-width: 896px;" viewBox="0 0 896 202" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M638.022 187.747L468.019 187.729C469.017 97.3485 544.706 18.7944 637.863 19.7826C731.02 20.7708 806.168 97.3828 805.169 187.763L638.022 187.747Z" fill="#4CB7FF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M638.001 189.424L549.428 189.424C549.428 141.505 588.406 97.7374 637.384 97.759C686.287 97.7805 728.003 141.505 728.003 189.425L638.001 189.424Z" fill="#AFE7EF" fill-opacity="0.61"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M229.331 183.495L90 183.495C90.8265 108.681 152.866 43.6504 229.216 44.4603C305.565 45.2702 367.147 108.68 366.32 183.494L229.331 183.495Z" fill="#877CFF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M229.33 183.49L156.738 183.49C156.738 144.217 188.684 108.346 228.825 108.364C268.904 108.381 303.094 144.217 303.094 183.491L229.33 183.49Z" fill="#BBBBFF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M424.175 187.736L235.996 189.674C237.123 87.6699 322.335 1.89415 426.216 2.9961C530.098 4.09806 613.087 87.6602 611.96 189.665L424.175 187.736Z" fill="#CA8787"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M425.577 187.48L326.806 189.072C326.217 135.751 369.943 89.7647 424.556 89.1854C479.086 88.607 523.758 135.747 524.347 189.068L425.577 187.48Z" fill="#FFD0D0"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M146.94 188.268L59 188.398C59.5266 140.729 99.2461 101.418 147.894 101.934C196.542 102.45 235.573 141.533 235.046 189.202L146.94 188.268Z" fill="#FF9750"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M146.937 188.565L100.964 188.396C100.687 163.373 121.101 142.564 146.676 142.293C172.212 142.022 193.496 163.543 193.772 188.565L146.937 188.565Z" fill="#FFD2B3"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M24.6054 176.589L5.61044 192.213C-3.0062 181.736 -1.49697 166.24 8.99519 157.61C19.4713 148.994 34.9677 150.503 43.5844 160.979L24.6054 176.589Z" fill="#877CFF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M24.5411 176.513L5.54611 192.136C14.1759 202.628 29.6724 204.137 40.1645 195.508C50.6406 186.891 52.1499 171.395 43.52 160.902L24.5411 176.513Z" fill="#BBBBFF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M871.349 174.416L856.612 154.726C867.472 146.598 882.883 148.815 891.023 159.691C899.151 170.551 896.934 185.962 886.074 194.09L871.349 174.416Z" fill="#4CB7FF"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M871.428 174.355L856.69 154.665C845.814 162.805 843.597 178.216 851.737 189.093C859.865 199.952 875.276 202.17 886.152 194.029L871.428 174.355Z" fill="#89D4F5"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M547.348 18.3648L531.753 10.5821C536.045 1.98075 546.51 -1.51656 555.125 2.78238C563.726 7.07475 567.223 17.5397 562.931 26.1411L547.348 18.3648Z" fill="#CA8787"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M547.378 18.3019L531.783 10.5191C527.484 19.1336 530.981 29.5986 539.596 33.8976C548.197 38.1899 558.662 34.6926 562.961 26.0781L547.378 18.3019Z" fill="#FFD0D0"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M288.017 41.0098L279.774 47.7895C276.035 43.2435 276.69 36.5189 281.243 32.774C285.789 29.0348 292.513 29.6898 296.253 34.2358L288.017 41.0098Z" fill="#FF9750"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M287.989 40.9771L279.746 47.7568C283.491 52.3098 290.215 52.9648 294.769 49.2199C299.315 45.4807 299.969 38.7561 296.225 34.2031L287.989 40.9771Z" fill="#FFD2B3"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M725.016 189.46L614 190.615C614.664 129.73 664.933 78.5337 726.217 79.1928C787.502 79.8519 836.462 129.73 835.799 190.614L725.016 189.46Z" fill="#FDE9C4"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M726.579 189.661L668.31 190.614C667.961 158.655 693.757 131.092 725.976 130.745C758.145 130.399 784.5 158.655 784.848 190.614L726.579 189.661Z" fill="#FFBA37" fill-opacity="0.5"/>
                                        </svg>
                                        <!-- svg end -->

                                        <div class="d-flex align-items-center justify-content-center pt-3 pb-4 px-xl-5" style="gap: 17px;">
                                            <svg width="23" height="31" viewBox="0 0 23 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.0474 0.193359C0.994874 0.193359 0.144531 1.03633 0.144531 2.07973C0.144531 3.12312 0.994874 3.96609 2.0474 3.96609V4.61453C2.0474 7.11397 3.05235 9.51319 4.83629 11.2817L8.86798 15.2843L4.83629 19.2869C3.05235 21.0554 2.0474 23.4546 2.0474 25.9541V26.6025C0.994874 26.6025 0.144531 27.4455 0.144531 28.4889C0.144531 29.5323 0.994874 30.3752 2.0474 30.3752H3.95026H19.1732H21.0761C22.1286 30.3752 22.9789 29.5323 22.9789 28.4889C22.9789 27.4455 22.1286 26.6025 21.0761 26.6025V25.9541C21.0761 23.4546 20.0711 21.0554 18.2872 19.2869L14.2555 15.2843L18.2931 11.2817C20.0771 9.51319 21.082 7.11397 21.082 4.61453V3.96609C22.1345 3.96609 22.9849 3.12312 22.9849 2.07973C22.9849 1.03633 22.1345 0.193359 21.082 0.193359H19.1732H3.95026H2.0474ZM5.85313 4.61453V3.96609H17.2703V4.61453C17.2703 5.73456 16.9373 6.81922 16.3189 7.73883H6.80456C6.19208 6.81922 5.85313 5.73456 5.85313 4.61453ZM6.80456 22.8298C7.01269 22.5173 7.25649 22.2226 7.52408 21.9514L11.5617 17.9547L15.5994 21.9573C15.8729 22.2285 16.1108 22.5232 16.3189 22.8357H6.80456V22.8298Z" fill="black"/>
                                            </svg>
                                            <p class="fs-20 text-dark fw-400 mb-0">Hang Tight – You'll start seeing others here soon</p>
                                        </div>
                                        <a href="#" class="refresh-v1">
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3.15628 6.09409C3.42905 5.31547 3.87185 4.58329 4.49531 3.95826C6.70931 1.72599 10.2978 1.72599 12.5118 3.95826L13.1175 4.57258H11.9025C11.2755 4.57258 10.7689 5.08332 10.7689 5.7155C10.7689 6.34767 11.2755 6.85842 11.9025 6.85842H15.8523H15.8664C16.4934 6.85842 17 6.34767 17 5.7155V1.71527C17 1.0831 16.4934 0.572353 15.8664 0.572353C15.2394 0.572353 14.7329 1.0831 14.7329 1.71527V2.97249L14.1129 2.34388C11.0133 -0.781294 5.99021 -0.781294 2.8906 2.34388C2.02626 3.21536 1.40279 4.24041 1.02021 5.3369C0.811211 5.93337 1.12294 6.5834 1.71098 6.79413C2.29902 7.00486 2.94728 6.69055 3.15628 6.09766V6.09409ZM0.814753 9.19069C0.637633 9.24426 0.467597 9.3407 0.329444 9.48356C0.187747 9.62643 0.0921025 9.79787 0.0425089 9.98359C0.0318817 10.0265 0.0212544 10.0729 0.0141696 10.1193C0.00354239 10.18 0 10.2407 0 10.3015V14.2874C0 14.9196 0.506564 15.4303 1.13357 15.4303C1.76058 15.4303 2.26714 14.9196 2.26714 14.2874V13.0338L2.8906 13.6588C5.99021 16.7804 11.0133 16.7804 14.1094 13.6588C14.9737 12.7873 15.6007 11.7623 15.9833 10.6693C16.1923 10.0729 15.8806 9.42285 15.2926 9.21212C14.7045 9.00139 14.0563 9.3157 13.8473 9.90859C13.5745 10.6872 13.1317 11.4194 12.5082 12.0444C10.2942 14.2767 6.70577 14.2767 4.49177 12.0444L4.48823 12.0408L3.88248 11.4301H5.10106C5.72807 11.4301 6.23463 10.9194 6.23463 10.2872C6.23463 9.655 5.72807 9.14426 5.10106 9.14426H1.14774C1.09106 9.14426 1.03438 9.14783 0.977704 9.15497C0.921025 9.16212 0.867889 9.17283 0.814753 9.19069Z" fill="black"/>
                                            </svg>
                                            <span class="speed_networking_update_btn">Check Again</span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                            <!-- zero day speed end -->

                            <!--connect request modal end-->
                            <!-- speed networking accept , reject html  -->
                            <!-- countdown -->
                            <div class="countDownDiv container pt-4 px-lg-4 d-none">
                                <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center">
                                    <h6 class="sm-text mb-3 text-center">You are one step closer to connecting with</h6>
                                    <div class="d-flex align-items-center" style="gap: 9px;">
                                        <img class="speed-sm-profile user_profile_img" src="" alt="">
                                        <h5 class="md-text text-center user_title"></h5>
                                    </div>

                                    <div class="center-card">
                                        <h5 class="sm-text text-center"><span class="user_title"></span> has</h5>
                                        <h1 class="count countDownTimer1">30</h1>
                                        <p class="sm-text text-center">Seconds to respond</p>
                                    </div>

                                    <p class="sm-text text-center">We are Just Excited as you are !</p>

                                    <!-- ribbon svgs -->
                                        <svg class="left-ribbon" width="129" height="101" viewBox="0 0 129 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M107.688 23.9038C105.841 26.7318 102.641 29.1363 100.54 31.3369C97.1997 34.8763 94.0108 38.5696 90.9655 42.3937C87.5114 46.7557 84.2692 51.3025 81.247 55.9808C79.7548 58.3392 77.8059 61.479 78.0931 64.4019C77.1639 64.2718 76.2982 63.8678 75.5195 63.1061C73.8917 60.8959 74.28 58.3372 75.3539 55.8608C76.9647 52.1462 79.6328 48.7229 81.9717 45.4569C84.604 41.7664 87.3652 38.1609 90.2776 34.7092C93.6447 30.7049 97.1933 26.8699 100.893 23.1888C102.747 21.3215 104.833 19.075 105.32 16.5859L107.688 23.9038Z" fill="url(#paint0_linear_7358_2)"/>
                                        <path d="M37.206 79.6033C36.5979 80.5343 35.5443 81.3259 34.8527 82.0503C33.7532 83.2154 32.7034 84.4312 31.7009 85.6901C30.5638 87.126 29.4965 88.6228 28.5016 90.1629C28.0104 90.9392 27.3689 91.9728 27.4634 92.935C27.1575 92.8922 26.8725 92.7592 26.6162 92.5085C26.0803 91.7809 26.2082 90.9386 26.5617 90.1234C27.0919 88.9005 27.9702 87.7736 28.7402 86.6985C29.6067 85.4836 30.5157 84.2967 31.4744 83.1604C32.5829 81.8422 33.751 80.5798 34.969 79.368C35.5793 78.7533 36.266 78.0138 36.4262 77.1944L37.206 79.6033Z" fill="url(#paint1_linear_7358_2)"/>
                                        <path d="M75.7767 63.4303C75.7011 63.3534 75.6485 63.269 75.6032 63.2077C75.5654 63.1692 75.558 63.1463 75.5202 63.1078C76.299 63.8695 77.1647 64.2735 78.0938 64.4036C80.1728 64.7191 82.4701 63.5954 84.2728 62.353C88.0847 59.8013 91.5369 56.5297 95.085 53.6326C99.2706 50.1997 107.245 41.6376 113.048 46.8046C114.108 47.7289 115.181 48.8515 116.132 49.9883L118.738 58.0402C118.465 59.5476 117.888 61.0524 117.19 62.4188C114.244 67.9595 107.503 74.1457 109.916 80.7395C111.284 81.2852 112.778 81.3592 114.394 81.4191C116.772 81.4856 119.08 81.8031 121.361 82.5092C123.589 83.2073 125.692 84.2245 127.626 85.4995L128.405 87.9082C126.757 87.1237 125.032 86.491 123.274 86.0716C119.88 85.2947 115.381 86.2696 112.424 84.2613C110.598 83.0025 108.825 80.8139 107.498 79.0638C105.41 76.2929 105.397 72.4955 106.645 69.3801C109.077 63.2707 117.293 56.7085 114.83 49.7253C109.313 47.5574 103.056 54.1444 99.1703 57.3283C95.7221 60.1424 92.3866 63.1481 88.8102 65.801C86.6843 67.3761 83.6482 69.2711 80.8965 68.2864C78.8594 67.5519 77.1048 65.0279 75.7767 63.4303Z" fill="url(#paint2_linear_7358_2)"/>
                                        <path d="M26.6995 92.6159C26.6746 92.5906 26.6573 92.5628 26.6424 92.5426C26.63 92.5299 26.6275 92.5224 26.6151 92.5097C26.8714 92.7605 27.1564 92.8935 27.4623 92.9363C28.1466 93.0402 28.9029 92.6702 29.4963 92.2612C30.7512 91.4212 31.8876 90.3443 33.0556 89.3906C34.4335 88.2605 37.0586 85.4419 38.969 87.1428C39.3178 87.4471 39.671 87.8167 39.984 88.1909L40.8419 90.8415C40.7523 91.3378 40.5622 91.8331 40.3324 92.2829C39.3626 94.1069 37.1435 96.1433 37.9378 98.314C38.3881 98.4936 38.8799 98.518 39.4119 98.5377C40.1948 98.5595 40.9545 98.6641 41.7055 98.8965C42.439 99.1263 43.1313 99.4612 43.7678 99.8809L44.0244 100.674C43.482 100.416 42.914 100.207 42.3353 100.069C41.2181 99.8135 39.7368 100.134 38.7636 99.4733C38.1623 99.0589 37.5787 98.3384 37.1419 97.7623C36.4546 96.8502 36.4504 95.6001 36.861 94.5745C37.6617 92.5634 40.3663 90.4031 39.5555 88.1043C37.7394 87.3907 35.6798 89.559 34.4005 90.6072C33.2654 91.5335 32.1674 92.523 30.99 93.3963C30.2902 93.9148 29.2907 94.5386 28.3849 94.2145C27.7143 93.9727 27.1367 93.1418 26.6995 92.6159Z" fill="url(#paint3_linear_7358_2)"/>
                                        <path d="M27.642 38.7564C27.492 38.8051 27.342 38.8782 27.192 38.9269C27.192 38.9025 27.217 38.9025 27.217 38.9025C27.117 38.9269 27.0169 38.9756 26.9419 39C26.9419 38.9269 26.9669 38.8538 27.0169 38.7564C27.0669 38.6589 27.292 38.5127 27.517 38.3665C27.417 38.3909 27.317 38.3909 27.242 38.4153C27.267 38.3909 27.292 38.3665 27.292 38.3665C30.3922 35.8327 29.6421 31.91 28.492 28.6209C26.9169 24.1135 25.1168 19.6792 23.4417 15.1962C22.0666 11.566 19.9165 7.25352 21.6416 3.37962C22.3666 1.74722 23.6167 0.845745 25.0418 0.212276C26.0169 -0.0313644 26.9919 -0.0557289 27.917 0.0904579C24.9668 3.5258 27.417 9.47065 28.8671 13.3446C30.0671 16.4875 31.2422 19.6305 32.4423 22.7735C33.4424 25.4292 34.8675 28.2798 34.9925 31.1547C35.1675 35.321 32.2673 37.1483 28.6921 38.3909C28.367 38.5127 28.067 38.6102 27.742 38.732C27.717 38.732 27.667 38.7564 27.642 38.7564ZM28.9671 38.196L28.9171 38.2203C28.9421 38.196 28.9671 38.196 28.9671 38.196Z" fill="url(#paint4_linear_7358_2)"/>
                                        <path d="M0.682274 32.7928C-0.709158 32.5107 0.462575 32.8441 0.438163 32.8441C0.779919 32.8185 2.14694 32.8698 2.34223 32.8954C3.78248 32.9724 5.19833 33.3571 6.54094 33.8701C10.8373 35.5372 14.5722 38.8971 19.1371 39.7948C20.6261 40.0769 22.0176 40.0513 23.3846 39.8205H23.409C24.3366 39.6922 25.9478 39.2306 26.2407 39.128C26.2895 39.128 26.2895 39.128 26.2895 39.128C27.6809 38.7176 30 37.6917 30 37.6917C29.6094 37.8968 28.4865 37.9994 27.8518 38.0251C27.8518 38.0251 27.8518 38.0251 27.8762 38.0251C27.6321 38.0507 27.4368 38.0507 27.388 38.0507C25.8013 38.102 24.2146 37.8455 22.7011 37.3326C15.9392 35.0242 10.5199 29.0482 2.9281 31.6386C2.65957 31.7156 2.39105 31.8182 2.12253 31.8951C1.92724 32.2542 1.04844 32.8441 0.682274 32.7928Z" fill="url(#paint5_linear_7358_2)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_7358_2" x1="106.712" y1="26.2182" x2="74.4736" y2="55.4932" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F69CD2"/>
                                        <stop offset="0.980392" stop-color="#BC1558"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_7358_2" x1="36.8844" y1="80.3653" x2="26.2719" y2="90.0023" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#BC1558"/>
                                        <stop offset="0.980392" stop-color="#BC1558"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_7358_2" x1="115.524" y1="58.8016" x2="86.2046" y2="85.4263" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F69CD2"/>
                                        <stop offset="0.0117647" stop-color="#F599D0"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint3_linear_7358_2" x1="39.7841" y1="91.0922" x2="30.1323" y2="99.8568" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F599D0"/>
                                        <stop offset="0.0117647" stop-color="#F599D0"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint4_linear_7358_2" x1="21" y1="19.4997" x2="35.0015" y2="19.4997" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#1897C9"/>
                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                        <stop offset="0.329412" stop-color="#18B7C9"/>
                                        <stop offset="0.819608" stop-color="#19D8C9"/>
                                        <stop offset="1" stop-color="#19D8C9"/>
                                        </linearGradient>
                                        <linearGradient id="paint5_linear_7358_2" x1="22.9449" y1="38.4103" x2="6.89498" y2="33.0766" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#1897C9"/>
                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                        <stop offset="0.490196" stop-color="#18B7C9"/>
                                        <stop offset="0.909804" stop-color="#19D8C9"/>
                                        <stop offset="1" stop-color="#19D8C9"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>

                                    <svg class="right-ribbon" width="129" height="101" viewBox="0 0 129 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.7168 23.9038C22.5639 26.7318 25.7645 29.1363 27.8654 31.3369C31.2055 34.8763 34.3945 38.5696 37.4398 42.3937C40.8939 46.7557 44.136 51.3025 47.1582 55.9808C48.6504 58.3392 50.5993 61.479 50.3122 64.4019C51.2413 64.2718 52.1071 63.8678 52.8858 63.1061C54.5136 60.8959 54.1253 58.3372 53.0514 55.8608C51.4406 52.1462 48.7725 48.7229 46.4336 45.4569C43.8013 41.7664 41.0401 38.1609 38.1277 34.7092C34.7606 30.7049 31.212 26.8699 27.5122 23.1888C25.6583 21.3215 23.5723 19.075 23.0855 16.5859L20.7168 23.9038Z" fill="url(#paint0_linear_7358_2)"/>
                                        <path d="M91.1993 79.6033C91.8074 80.5343 92.861 81.3259 93.5526 82.0503C94.6521 83.2154 95.7019 84.4312 96.7044 85.6901C97.8414 87.126 98.9087 88.6228 99.9036 90.1629C100.395 90.9392 101.036 91.9728 100.942 92.935C101.248 92.8922 101.533 92.7592 101.789 92.5085C102.325 91.7809 102.197 90.9386 101.844 90.1234C101.313 88.9005 100.435 87.7736 99.6651 86.6985C98.7985 85.4836 97.8896 84.2967 96.9308 83.1604C95.8224 81.8422 94.6542 80.5798 93.4363 79.368C92.826 78.7533 92.1393 78.0138 91.979 77.1944L91.1993 79.6033Z" fill="url(#paint1_linear_7358_2)"/>
                                        <path d="M52.6286 63.4303C52.7042 63.3534 52.7568 63.269 52.802 63.2077C52.8398 63.1692 52.8472 63.1463 52.885 63.1078C52.1063 63.8695 51.2406 64.2735 50.3114 64.4036C48.2325 64.7191 45.9352 63.5954 44.1325 62.353C40.3205 59.8013 36.8683 56.5297 33.3203 53.6326C29.1347 50.1997 21.1604 41.6376 15.357 46.8046C14.2975 47.7289 13.2246 48.8515 12.2737 49.9883L9.6675 58.0402C9.9399 59.5476 10.5173 61.0524 11.2154 62.4188C14.1613 67.9595 20.9024 74.1457 18.4894 80.7395C17.1216 81.2852 15.6278 81.3592 14.0118 81.4191C11.6333 81.4856 9.32566 81.8031 7.04428 82.5092C4.81621 83.2073 2.71292 84.2245 0.779637 85.4995L0 87.9082C1.64781 87.1237 3.37319 86.491 5.13094 86.0716C8.52496 85.2947 13.0246 86.2696 15.9809 84.2613C17.8076 83.0025 19.5804 80.8139 20.9072 79.0638C22.9952 76.2929 23.0078 72.4955 21.7607 69.3801C19.3284 63.2707 11.1123 56.7085 13.5753 49.7253C19.0922 47.5574 25.3489 54.1444 29.2349 57.3283C32.6832 60.1424 36.0187 63.1481 39.5951 65.801C41.721 67.3761 44.757 69.2711 47.5087 68.2864C49.5459 67.5519 51.3005 65.0279 52.6286 63.4303Z" fill="url(#paint2_linear_7358_2)"/>
                                        <path d="M101.706 92.6159C101.731 92.5906 101.748 92.5628 101.763 92.5426C101.775 92.5299 101.778 92.5224 101.79 92.5097C101.534 92.7605 101.249 92.8935 100.943 92.9363C100.259 93.0402 99.5024 92.6702 98.9089 92.2612C97.6541 91.4212 96.5176 90.3443 95.3496 89.3906C93.9718 88.2605 91.3467 85.4419 89.4363 87.1428C89.0875 87.4471 88.7343 87.8167 88.4213 88.1909L87.5633 90.8415C87.653 91.3378 87.8431 91.8331 88.0729 92.2829C89.0426 94.1069 91.2618 96.1433 90.4674 98.314C90.0172 98.4936 89.5254 98.518 88.9934 98.5377C88.2105 98.5595 87.4508 98.6641 86.6998 98.8965C85.9663 99.1263 85.2739 99.4612 84.6375 99.8809L84.3809 100.674C84.9233 100.416 85.4913 100.207 86.0699 100.069C87.1872 99.8135 88.6685 100.134 89.6417 99.4733C90.243 99.0589 90.8266 98.3384 91.2633 97.7623C91.9507 96.8502 91.9549 95.6001 91.5443 94.5745C90.7436 92.5634 88.0389 90.4031 88.8497 88.1043C90.6659 87.3907 92.7255 89.559 94.0048 90.6072C95.1399 91.5335 96.2379 92.523 97.4153 93.3963C98.1151 93.9148 99.1145 94.5386 100.02 94.2145C100.691 93.9727 101.269 93.1418 101.706 92.6159Z" fill="url(#paint3_linear_7358_2)"/>
                                        <path d="M100.763 38.7564C100.913 38.8051 101.063 38.8782 101.213 38.9269C101.213 38.9025 101.188 38.9025 101.188 38.9025C101.288 38.9269 101.388 38.9756 101.463 39C101.463 38.9269 101.438 38.8538 101.388 38.7564C101.338 38.6589 101.113 38.5127 100.888 38.3665C100.988 38.3909 101.088 38.3909 101.163 38.4153C101.138 38.3909 101.113 38.3665 101.113 38.3665C98.0131 35.8327 98.7632 31.91 99.9132 28.6209C101.488 24.1135 103.288 19.6792 104.964 15.1962C106.339 11.566 108.489 7.25352 106.764 3.37962C106.039 1.74722 104.789 0.845745 103.363 0.212276C102.388 -0.0313644 101.413 -0.0557289 100.488 0.0904579C103.438 3.5258 100.988 9.47065 99.5382 13.3446C98.3381 16.4875 97.163 19.6305 95.963 22.7735C94.9629 25.4292 93.5378 28.2798 93.4128 31.1547C93.2378 35.321 96.138 37.1483 99.7132 38.3909C100.038 38.5127 100.338 38.6102 100.663 38.732C100.688 38.732 100.738 38.7564 100.763 38.7564ZM99.4382 38.196L99.4882 38.2203C99.4632 38.196 99.4382 38.196 99.4382 38.196Z" fill="url(#paint4_linear_7358_2)"/>
                                        <path d="M127.723 32.7928C129.114 32.5107 127.943 32.8441 127.967 32.8441C127.625 32.8185 126.258 32.8698 126.063 32.8954C124.623 32.9724 123.207 33.3571 121.864 33.8701C117.568 35.5372 113.833 38.8971 109.268 39.7948C107.779 40.0769 106.388 40.0513 105.021 39.8205H104.996C104.069 39.6922 102.458 39.2306 102.165 39.128C102.116 39.128 102.116 39.128 102.116 39.128C100.724 38.7176 98.4053 37.6917 98.4053 37.6917C98.7959 37.8968 99.9188 37.9994 100.553 38.0251C100.553 38.0251 100.553 38.0251 100.529 38.0251C100.773 38.0507 100.968 38.0507 101.017 38.0507C102.604 38.102 104.191 37.8455 105.704 37.3326C112.466 35.0242 117.885 29.0482 125.477 31.6386C125.746 31.7156 126.014 31.8182 126.283 31.8951C126.478 32.2542 127.357 32.8441 127.723 32.7928Z" fill="url(#paint5_linear_7358_2)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_7358_2" x1="21.6936" y1="26.2182" x2="53.9317" y2="55.4932" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F69CD2"/>
                                        <stop offset="0.980392" stop-color="#BC1558"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_7358_2" x1="91.5209" y1="80.3653" x2="102.133" y2="90.0023" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#BC1558"/>
                                        <stop offset="0.980392" stop-color="#BC1558"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_7358_2" x1="12.881" y1="58.8016" x2="42.2007" y2="85.4263" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F69CD2"/>
                                        <stop offset="0.0117647" stop-color="#F599D0"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint3_linear_7358_2" x1="88.6212" y1="91.0922" x2="98.273" y2="99.8568" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#F599D0"/>
                                        <stop offset="0.0117647" stop-color="#F599D0"/>
                                        <stop offset="1" stop-color="#BC1558"/>
                                        </linearGradient>
                                        <linearGradient id="paint4_linear_7358_2" x1="107.405" y1="19.4997" x2="93.4038" y2="19.4997" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#1897C9"/>
                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                        <stop offset="0.329412" stop-color="#18B7C9"/>
                                        <stop offset="0.819608" stop-color="#19D8C9"/>
                                        <stop offset="1" stop-color="#19D8C9"/>
                                        </linearGradient>
                                        <linearGradient id="paint5_linear_7358_2" x1="105.46" y1="38.4103" x2="121.51" y2="33.0766" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#1897C9"/>
                                        <stop offset="0.160784" stop-color="#1897C9"/>
                                        <stop offset="0.490196" stop-color="#18B7C9"/>
                                        <stop offset="0.909804" stop-color="#19D8C9"/>
                                        <stop offset="1" stop-color="#19D8C9"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>


                                </div>
                            </div>

                            <!-- accept  start -->
                            <div class="successMatchDiv container pt-4 px-lg-4 d-none">
                            <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center">
                                <h6 class="sm-text fs-24 text-center">Boom ! It’s a Match</h6>
                                

                                <div class="center-card">
                                    <svg width="129" height="120" viewBox="0 0 129 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M103.012 54.9999C109.645 54.9999 115.023 49.5629 115.023 42.8559C115.023 42.4268 115 42.0028 114.957 41.5849C117.3 42.3211 120.095 42.1946 122.188 41.9001C122.995 41.7862 123.277 40.7904 122.682 40.2268C120.463 38.1218 119.774 33.9963 119.774 30.5115C119.774 24.7193 113.792 25.0022 113.792 25.0022C109.206 25.0022 108.001 29.9868 107.969 31.8039C106.456 31.1087 104.782 30.712 103.012 30.712C96.3777 30.712 91 36.149 91 42.856C91 49.563 96.3777 54.9999 103.012 54.9999ZM97.1914 35.1434C97.9982 37.6218 100.242 41.136 106.532 41.136C106.532 41.136 106.825 46.9696 110.736 48.6101C108.984 51.007 106.179 52.571 103.011 52.571C97.7129 52.571 93.4022 48.2128 93.4022 42.8558C93.4022 39.712 94.8938 36.9205 97.1914 35.1434Z" fill="#2557A7"/>
                                        <path d="M128.772 106.498L115.368 66.52C113.751 61.6974 109.749 58.06 104.798 56.9129L96.329 54.9505C92.736 54.1181 89.6236 51.885 87.6822 48.7465L70.5167 21C70.463 21.6889 70.3121 22.3804 70.0209 23.0475L66 32.2602L79.7638 61.7133C81.8244 66.1227 82.469 71.0562 81.6122 75.8487C80.4262 82.4841 78.9187 92.5212 78.9187 100.674C78.9187 110.199 80.5792 115.929 81.5468 118.46C81.9038 119.393 82.8098 120 83.8079 120H115.579C116.903 120 117.983 118.939 118.009 117.614L118.144 110.655L105.955 80.8027C105.701 80.1806 105.999 79.4698 106.62 79.2159C107.245 78.9639 107.952 79.2605 108.205 79.882L118.264 104.516L118.264 104.493L120.659 109.716C121.675 111.93 124.301 112.887 126.501 111.844C128.501 110.895 129.476 108.599 128.772 106.499L128.772 106.498Z" fill="#2557A7"/>
                                        <path d="M64.5538 29.6556L67.6851 22.43C68.0533 21.5799 68.0849 20.6661 67.8514 19.8307C67.5854 18.8785 66.9721 18.0289 66.0613 17.5071C65.4611 17.1631 64.8088 17 64.1651 17C62.8094 17 61.4932 17.7243 60.7923 19.0088L46.443 45.4795C43.1966 51.4685 37.4706 55.6382 30.8381 57.0081C24.6007 58.2961 17.1058 61.5768 13.925 69.7465C8.23713 84.3544 2.59797 99.6412 0.229857 106.629C-0.5016 108.788 0.567028 111.14 2.66377 111.998C3.18731 112.213 3.7314 112.315 4.26799 112.315C5.7505 112.315 7.17633 111.533 7.95984 110.172L14.3882 99.7731L23.7193 77.6757C23.9788 77.0594 24.6852 76.7725 25.2965 77.0352C25.9077 77.2973 26.1917 78.0095 25.9317 78.6253L17.2499 99.1853V117.575C17.2499 118.914 18.3216 120 19.6497 120H43.0744C44.3856 120 45.4537 118.944 45.4747 117.622C45.5995 109.76 46.1816 97.2171 46.5088 90.4736C46.642 87.7279 47.3485 85.0361 48.6641 82.6283C51.1225 78.1303 52.0539 74.8465 50.5649 70.3419C48.7327 64.8008 50.1009 63.0052 50.1009 63.0052L64.5538 29.6556Z" fill="#2557A7"/>
                                        <path d="M7.57794 47.3989C8.21186 47.3989 8.81115 47.2694 9.37935 47.0738C9.37273 47.1834 9.34677 47.2878 9.34677 47.3989C9.34677 50.4966 11.8442 53.0075 14.9253 53.0075C16.5739 53.0075 18.0399 52.275 19.0612 51.1319C21.2894 53.5082 24.4411 55 27.9457 55C34.6948 55 40.1659 49.4994 40.1659 42.714C40.1659 42.0608 40.1002 41.4245 40.0029 40.7989C42.4932 40.739 44.4969 38.7 44.4969 36.1815C44.4969 35.1745 44.1685 34.2495 43.6257 33.4898C44.473 32.6503 45 31.4857 45 30.1956C45 27.9063 43.3431 26.0178 41.1721 25.6456C41.2892 25.1931 41.3717 24.7262 41.3717 24.2368C41.3717 21.4858 39.3981 19.2078 36.7993 18.7301C36.7998 18.6953 36.8095 18.663 36.8095 18.6282C36.8095 16.0722 34.7484 14 32.2061 14C30.3502 14 28.7605 15.1104 28.0324 16.7008C27.2982 16.3435 26.485 16.1254 25.6143 16.1254C24.6189 16.1254 23.6983 16.409 22.8892 16.8682C22.0506 15.7287 20.715 14.9803 19.1967 14.9803C17.042 14.9803 15.2465 16.4751 14.7456 18.4854C12.2196 19.0209 10.3224 21.2713 10.3224 23.9705C10.3224 24.5628 10.439 25.1229 10.608 25.6589C7.82028 25.9701 5.64412 28.3193 5.64412 31.205C5.64412 31.4466 5.68536 31.6769 5.71489 31.9109C3.9827 32.8625 2.79379 34.689 2.79379 36.8135C2.79379 37.4053 2.90989 37.9648 3.07893 38.5003C2.40785 39.4263 2 40.5566 2 41.7899C1.99949 44.8876 4.49691 47.3989 7.57794 47.3989ZM27.9461 52.5427C25.2398 52.5427 22.7877 51.4303 21.0158 49.6375C23.3142 49.3785 25.1075 47.4408 25.1075 45.061C25.1075 44.6156 25.0251 44.1938 24.9085 43.7863C26.6743 43.1392 27.9426 41.4515 27.9426 39.4519C27.9426 39.4401 27.939 39.4294 27.939 39.4176C30.3214 39.241 32.2053 37.2634 32.2053 34.8231C32.2053 34.4796 32.162 34.1468 32.0912 33.8248C33.3214 34.4074 34.411 35.2372 35.2965 36.2549C36.2349 37.3335 36.9416 38.6165 37.3418 40.0313C37.5842 40.8857 37.7221 41.783 37.7221 42.7138C37.7221 48.1334 33.3367 52.5427 27.9461 52.5427Z" fill="#2557A7"/>
                                        <path d="M64.5 13C65.8807 13 67 11.9917 67 10.7479V2.25206C67 1.00832 65.8807 0 64.5 0C63.1193 0 62 1.00826 62 2.25206V10.7479C62 11.9917 63.1193 13 64.5 13Z" fill="#2557A7"/>
                                        <path d="M71.3568 17C71.9597 17 72.5631 16.7697 73.0232 16.3097L79.3096 10.0232C80.2301 9.10313 80.2301 7.6106 79.3096 6.69043C78.39 5.76986 76.8965 5.76986 75.9768 6.69043L69.6904 12.9769C68.7699 13.897 68.7699 15.3895 69.6904 16.3097C70.1505 16.7697 70.7539 17 71.3568 17Z" fill="#2557A7"/>
                                        <path d="M55.9768 16.3097C56.4364 16.7702 57.0403 17 57.6432 17C58.2461 17 58.8495 16.7697 59.3096 16.3097C60.2301 15.3896 60.2301 13.8971 59.3096 12.9769L53.0232 6.69043C52.1036 5.76986 50.6101 5.76986 49.6904 6.69043C48.7699 7.61051 48.7699 9.10304 49.6904 10.0232L55.9768 16.3097Z" fill="#2557A7"/>
                                    </svg>
                                </div>

                                <p class="sm-text text-center mb-5"><span class="user_title"></span> has accepted your Networking Request !</p>

                                <button type="button" class="btn speed-v1-b-btn load_dm">Start Chat</button>

                                <!-- ribbon svgs -->
                                    <svg class="left-ribbon" width="129" height="101" viewBox="0 0 129 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M107.688 23.9038C105.841 26.7318 102.641 29.1363 100.54 31.3369C97.1997 34.8763 94.0108 38.5696 90.9655 42.3937C87.5114 46.7557 84.2692 51.3025 81.247 55.9808C79.7548 58.3392 77.8059 61.479 78.0931 64.4019C77.1639 64.2718 76.2982 63.8678 75.5195 63.1061C73.8917 60.8959 74.28 58.3372 75.3539 55.8608C76.9647 52.1462 79.6328 48.7229 81.9717 45.4569C84.604 41.7664 87.3652 38.1609 90.2776 34.7092C93.6447 30.7049 97.1933 26.8699 100.893 23.1888C102.747 21.3215 104.833 19.075 105.32 16.5859L107.688 23.9038Z" fill="url(#paint0_linear_7358_2)"/>
                                    <path d="M37.206 79.6033C36.5979 80.5343 35.5443 81.3259 34.8527 82.0503C33.7532 83.2154 32.7034 84.4312 31.7009 85.6901C30.5638 87.126 29.4965 88.6228 28.5016 90.1629C28.0104 90.9392 27.3689 91.9728 27.4634 92.935C27.1575 92.8922 26.8725 92.7592 26.6162 92.5085C26.0803 91.7809 26.2082 90.9386 26.5617 90.1234C27.0919 88.9005 27.9702 87.7736 28.7402 86.6985C29.6067 85.4836 30.5157 84.2967 31.4744 83.1604C32.5829 81.8422 33.751 80.5798 34.969 79.368C35.5793 78.7533 36.266 78.0138 36.4262 77.1944L37.206 79.6033Z" fill="url(#paint1_linear_7358_2)"/>
                                    <path d="M75.7767 63.4303C75.7011 63.3534 75.6485 63.269 75.6032 63.2077C75.5654 63.1692 75.558 63.1463 75.5202 63.1078C76.299 63.8695 77.1647 64.2735 78.0938 64.4036C80.1728 64.7191 82.4701 63.5954 84.2728 62.353C88.0847 59.8013 91.5369 56.5297 95.085 53.6326C99.2706 50.1997 107.245 41.6376 113.048 46.8046C114.108 47.7289 115.181 48.8515 116.132 49.9883L118.738 58.0402C118.465 59.5476 117.888 61.0524 117.19 62.4188C114.244 67.9595 107.503 74.1457 109.916 80.7395C111.284 81.2852 112.778 81.3592 114.394 81.4191C116.772 81.4856 119.08 81.8031 121.361 82.5092C123.589 83.2073 125.692 84.2245 127.626 85.4995L128.405 87.9082C126.757 87.1237 125.032 86.491 123.274 86.0716C119.88 85.2947 115.381 86.2696 112.424 84.2613C110.598 83.0025 108.825 80.8139 107.498 79.0638C105.41 76.2929 105.397 72.4955 106.645 69.3801C109.077 63.2707 117.293 56.7085 114.83 49.7253C109.313 47.5574 103.056 54.1444 99.1703 57.3283C95.7221 60.1424 92.3866 63.1481 88.8102 65.801C86.6843 67.3761 83.6482 69.2711 80.8965 68.2864C78.8594 67.5519 77.1048 65.0279 75.7767 63.4303Z" fill="url(#paint2_linear_7358_2)"/>
                                    <path d="M26.6995 92.6159C26.6746 92.5906 26.6573 92.5628 26.6424 92.5426C26.63 92.5299 26.6275 92.5224 26.6151 92.5097C26.8714 92.7605 27.1564 92.8935 27.4623 92.9363C28.1466 93.0402 28.9029 92.6702 29.4963 92.2612C30.7512 91.4212 31.8876 90.3443 33.0556 89.3906C34.4335 88.2605 37.0586 85.4419 38.969 87.1428C39.3178 87.4471 39.671 87.8167 39.984 88.1909L40.8419 90.8415C40.7523 91.3378 40.5622 91.8331 40.3324 92.2829C39.3626 94.1069 37.1435 96.1433 37.9378 98.314C38.3881 98.4936 38.8799 98.518 39.4119 98.5377C40.1948 98.5595 40.9545 98.6641 41.7055 98.8965C42.439 99.1263 43.1313 99.4612 43.7678 99.8809L44.0244 100.674C43.482 100.416 42.914 100.207 42.3353 100.069C41.2181 99.8135 39.7368 100.134 38.7636 99.4733C38.1623 99.0589 37.5787 98.3384 37.1419 97.7623C36.4546 96.8502 36.4504 95.6001 36.861 94.5745C37.6617 92.5634 40.3663 90.4031 39.5555 88.1043C37.7394 87.3907 35.6798 89.559 34.4005 90.6072C33.2654 91.5335 32.1674 92.523 30.99 93.3963C30.2902 93.9148 29.2907 94.5386 28.3849 94.2145C27.7143 93.9727 27.1367 93.1418 26.6995 92.6159Z" fill="url(#paint3_linear_7358_2)"/>
                                    <path d="M27.642 38.7564C27.492 38.8051 27.342 38.8782 27.192 38.9269C27.192 38.9025 27.217 38.9025 27.217 38.9025C27.117 38.9269 27.0169 38.9756 26.9419 39C26.9419 38.9269 26.9669 38.8538 27.0169 38.7564C27.0669 38.6589 27.292 38.5127 27.517 38.3665C27.417 38.3909 27.317 38.3909 27.242 38.4153C27.267 38.3909 27.292 38.3665 27.292 38.3665C30.3922 35.8327 29.6421 31.91 28.492 28.6209C26.9169 24.1135 25.1168 19.6792 23.4417 15.1962C22.0666 11.566 19.9165 7.25352 21.6416 3.37962C22.3666 1.74722 23.6167 0.845745 25.0418 0.212276C26.0169 -0.0313644 26.9919 -0.0557289 27.917 0.0904579C24.9668 3.5258 27.417 9.47065 28.8671 13.3446C30.0671 16.4875 31.2422 19.6305 32.4423 22.7735C33.4424 25.4292 34.8675 28.2798 34.9925 31.1547C35.1675 35.321 32.2673 37.1483 28.6921 38.3909C28.367 38.5127 28.067 38.6102 27.742 38.732C27.717 38.732 27.667 38.7564 27.642 38.7564ZM28.9671 38.196L28.9171 38.2203C28.9421 38.196 28.9671 38.196 28.9671 38.196Z" fill="url(#paint4_linear_7358_2)"/>
                                    <path d="M0.682274 32.7928C-0.709158 32.5107 0.462575 32.8441 0.438163 32.8441C0.779919 32.8185 2.14694 32.8698 2.34223 32.8954C3.78248 32.9724 5.19833 33.3571 6.54094 33.8701C10.8373 35.5372 14.5722 38.8971 19.1371 39.7948C20.6261 40.0769 22.0176 40.0513 23.3846 39.8205H23.409C24.3366 39.6922 25.9478 39.2306 26.2407 39.128C26.2895 39.128 26.2895 39.128 26.2895 39.128C27.6809 38.7176 30 37.6917 30 37.6917C29.6094 37.8968 28.4865 37.9994 27.8518 38.0251C27.8518 38.0251 27.8518 38.0251 27.8762 38.0251C27.6321 38.0507 27.4368 38.0507 27.388 38.0507C25.8013 38.102 24.2146 37.8455 22.7011 37.3326C15.9392 35.0242 10.5199 29.0482 2.9281 31.6386C2.65957 31.7156 2.39105 31.8182 2.12253 31.8951C1.92724 32.2542 1.04844 32.8441 0.682274 32.7928Z" fill="url(#paint5_linear_7358_2)"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_7358_2" x1="106.712" y1="26.2182" x2="74.4736" y2="55.4932" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F69CD2"/>
                                    <stop offset="0.980392" stop-color="#BC1558"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint1_linear_7358_2" x1="36.8844" y1="80.3653" x2="26.2719" y2="90.0023" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#BC1558"/>
                                    <stop offset="0.980392" stop-color="#BC1558"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint2_linear_7358_2" x1="115.524" y1="58.8016" x2="86.2046" y2="85.4263" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F69CD2"/>
                                    <stop offset="0.0117647" stop-color="#F599D0"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint3_linear_7358_2" x1="39.7841" y1="91.0922" x2="30.1323" y2="99.8568" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F599D0"/>
                                    <stop offset="0.0117647" stop-color="#F599D0"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint4_linear_7358_2" x1="21" y1="19.4997" x2="35.0015" y2="19.4997" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#1897C9"/>
                                    <stop offset="0.160784" stop-color="#1897C9"/>
                                    <stop offset="0.329412" stop-color="#18B7C9"/>
                                    <stop offset="0.819608" stop-color="#19D8C9"/>
                                    <stop offset="1" stop-color="#19D8C9"/>
                                    </linearGradient>
                                    <linearGradient id="paint5_linear_7358_2" x1="22.9449" y1="38.4103" x2="6.89498" y2="33.0766" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#1897C9"/>
                                    <stop offset="0.160784" stop-color="#1897C9"/>
                                    <stop offset="0.490196" stop-color="#18B7C9"/>
                                    <stop offset="0.909804" stop-color="#19D8C9"/>
                                    <stop offset="1" stop-color="#19D8C9"/>
                                    </linearGradient>
                                    </defs>
                                </svg>

                                <svg class="right-ribbon" width="129" height="101" viewBox="0 0 129 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20.7168 23.9038C22.5639 26.7318 25.7645 29.1363 27.8654 31.3369C31.2055 34.8763 34.3945 38.5696 37.4398 42.3937C40.8939 46.7557 44.136 51.3025 47.1582 55.9808C48.6504 58.3392 50.5993 61.479 50.3122 64.4019C51.2413 64.2718 52.1071 63.8678 52.8858 63.1061C54.5136 60.8959 54.1253 58.3372 53.0514 55.8608C51.4406 52.1462 48.7725 48.7229 46.4336 45.4569C43.8013 41.7664 41.0401 38.1609 38.1277 34.7092C34.7606 30.7049 31.212 26.8699 27.5122 23.1888C25.6583 21.3215 23.5723 19.075 23.0855 16.5859L20.7168 23.9038Z" fill="url(#paint0_linear_7358_2)"/>
                                    <path d="M91.1993 79.6033C91.8074 80.5343 92.861 81.3259 93.5526 82.0503C94.6521 83.2154 95.7019 84.4312 96.7044 85.6901C97.8414 87.126 98.9087 88.6228 99.9036 90.1629C100.395 90.9392 101.036 91.9728 100.942 92.935C101.248 92.8922 101.533 92.7592 101.789 92.5085C102.325 91.7809 102.197 90.9386 101.844 90.1234C101.313 88.9005 100.435 87.7736 99.6651 86.6985C98.7985 85.4836 97.8896 84.2967 96.9308 83.1604C95.8224 81.8422 94.6542 80.5798 93.4363 79.368C92.826 78.7533 92.1393 78.0138 91.979 77.1944L91.1993 79.6033Z" fill="url(#paint1_linear_7358_2)"/>
                                    <path d="M52.6286 63.4303C52.7042 63.3534 52.7568 63.269 52.802 63.2077C52.8398 63.1692 52.8472 63.1463 52.885 63.1078C52.1063 63.8695 51.2406 64.2735 50.3114 64.4036C48.2325 64.7191 45.9352 63.5954 44.1325 62.353C40.3205 59.8013 36.8683 56.5297 33.3203 53.6326C29.1347 50.1997 21.1604 41.6376 15.357 46.8046C14.2975 47.7289 13.2246 48.8515 12.2737 49.9883L9.6675 58.0402C9.9399 59.5476 10.5173 61.0524 11.2154 62.4188C14.1613 67.9595 20.9024 74.1457 18.4894 80.7395C17.1216 81.2852 15.6278 81.3592 14.0118 81.4191C11.6333 81.4856 9.32566 81.8031 7.04428 82.5092C4.81621 83.2073 2.71292 84.2245 0.779637 85.4995L0 87.9082C1.64781 87.1237 3.37319 86.491 5.13094 86.0716C8.52496 85.2947 13.0246 86.2696 15.9809 84.2613C17.8076 83.0025 19.5804 80.8139 20.9072 79.0638C22.9952 76.2929 23.0078 72.4955 21.7607 69.3801C19.3284 63.2707 11.1123 56.7085 13.5753 49.7253C19.0922 47.5574 25.3489 54.1444 29.2349 57.3283C32.6832 60.1424 36.0187 63.1481 39.5951 65.801C41.721 67.3761 44.757 69.2711 47.5087 68.2864C49.5459 67.5519 51.3005 65.0279 52.6286 63.4303Z" fill="url(#paint2_linear_7358_2)"/>
                                    <path d="M101.706 92.6159C101.731 92.5906 101.748 92.5628 101.763 92.5426C101.775 92.5299 101.778 92.5224 101.79 92.5097C101.534 92.7605 101.249 92.8935 100.943 92.9363C100.259 93.0402 99.5024 92.6702 98.9089 92.2612C97.6541 91.4212 96.5176 90.3443 95.3496 89.3906C93.9718 88.2605 91.3467 85.4419 89.4363 87.1428C89.0875 87.4471 88.7343 87.8167 88.4213 88.1909L87.5633 90.8415C87.653 91.3378 87.8431 91.8331 88.0729 92.2829C89.0426 94.1069 91.2618 96.1433 90.4674 98.314C90.0172 98.4936 89.5254 98.518 88.9934 98.5377C88.2105 98.5595 87.4508 98.6641 86.6998 98.8965C85.9663 99.1263 85.2739 99.4612 84.6375 99.8809L84.3809 100.674C84.9233 100.416 85.4913 100.207 86.0699 100.069C87.1872 99.8135 88.6685 100.134 89.6417 99.4733C90.243 99.0589 90.8266 98.3384 91.2633 97.7623C91.9507 96.8502 91.9549 95.6001 91.5443 94.5745C90.7436 92.5634 88.0389 90.4031 88.8497 88.1043C90.6659 87.3907 92.7255 89.559 94.0048 90.6072C95.1399 91.5335 96.2379 92.523 97.4153 93.3963C98.1151 93.9148 99.1145 94.5386 100.02 94.2145C100.691 93.9727 101.269 93.1418 101.706 92.6159Z" fill="url(#paint3_linear_7358_2)"/>
                                    <path d="M100.763 38.7564C100.913 38.8051 101.063 38.8782 101.213 38.9269C101.213 38.9025 101.188 38.9025 101.188 38.9025C101.288 38.9269 101.388 38.9756 101.463 39C101.463 38.9269 101.438 38.8538 101.388 38.7564C101.338 38.6589 101.113 38.5127 100.888 38.3665C100.988 38.3909 101.088 38.3909 101.163 38.4153C101.138 38.3909 101.113 38.3665 101.113 38.3665C98.0131 35.8327 98.7632 31.91 99.9132 28.6209C101.488 24.1135 103.288 19.6792 104.964 15.1962C106.339 11.566 108.489 7.25352 106.764 3.37962C106.039 1.74722 104.789 0.845745 103.363 0.212276C102.388 -0.0313644 101.413 -0.0557289 100.488 0.0904579C103.438 3.5258 100.988 9.47065 99.5382 13.3446C98.3381 16.4875 97.163 19.6305 95.963 22.7735C94.9629 25.4292 93.5378 28.2798 93.4128 31.1547C93.2378 35.321 96.138 37.1483 99.7132 38.3909C100.038 38.5127 100.338 38.6102 100.663 38.732C100.688 38.732 100.738 38.7564 100.763 38.7564ZM99.4382 38.196L99.4882 38.2203C99.4632 38.196 99.4382 38.196 99.4382 38.196Z" fill="url(#paint4_linear_7358_2)"/>
                                    <path d="M127.723 32.7928C129.114 32.5107 127.943 32.8441 127.967 32.8441C127.625 32.8185 126.258 32.8698 126.063 32.8954C124.623 32.9724 123.207 33.3571 121.864 33.8701C117.568 35.5372 113.833 38.8971 109.268 39.7948C107.779 40.0769 106.388 40.0513 105.021 39.8205H104.996C104.069 39.6922 102.458 39.2306 102.165 39.128C102.116 39.128 102.116 39.128 102.116 39.128C100.724 38.7176 98.4053 37.6917 98.4053 37.6917C98.7959 37.8968 99.9188 37.9994 100.553 38.0251C100.553 38.0251 100.553 38.0251 100.529 38.0251C100.773 38.0507 100.968 38.0507 101.017 38.0507C102.604 38.102 104.191 37.8455 105.704 37.3326C112.466 35.0242 117.885 29.0482 125.477 31.6386C125.746 31.7156 126.014 31.8182 126.283 31.8951C126.478 32.2542 127.357 32.8441 127.723 32.7928Z" fill="url(#paint5_linear_7358_2)"/>
                                    <defs>
                                    <linearGradient id="paint0_linear_7358_2" x1="21.6936" y1="26.2182" x2="53.9317" y2="55.4932" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F69CD2"/>
                                    <stop offset="0.980392" stop-color="#BC1558"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint1_linear_7358_2" x1="91.5209" y1="80.3653" x2="102.133" y2="90.0023" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#BC1558"/>
                                    <stop offset="0.980392" stop-color="#BC1558"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint2_linear_7358_2" x1="12.881" y1="58.8016" x2="42.2007" y2="85.4263" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F69CD2"/>
                                    <stop offset="0.0117647" stop-color="#F599D0"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint3_linear_7358_2" x1="88.6212" y1="91.0922" x2="98.273" y2="99.8568" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#F599D0"/>
                                    <stop offset="0.0117647" stop-color="#F599D0"/>
                                    <stop offset="1" stop-color="#BC1558"/>
                                    </linearGradient>
                                    <linearGradient id="paint4_linear_7358_2" x1="107.405" y1="19.4997" x2="93.4038" y2="19.4997" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#1897C9"/>
                                    <stop offset="0.160784" stop-color="#1897C9"/>
                                    <stop offset="0.329412" stop-color="#18B7C9"/>
                                    <stop offset="0.819608" stop-color="#19D8C9"/>
                                    <stop offset="1" stop-color="#19D8C9"/>
                                    </linearGradient>
                                    <linearGradient id="paint5_linear_7358_2" x1="105.46" y1="38.4103" x2="121.51" y2="33.0766" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#1897C9"/>
                                    <stop offset="0.160784" stop-color="#1897C9"/>
                                    <stop offset="0.490196" stop-color="#18B7C9"/>
                                    <stop offset="0.909804" stop-color="#19D8C9"/>
                                    <stop offset="1" stop-color="#19D8C9"/>
                                    </linearGradient>
                                    </defs>
                                </svg>


                            </div>
                            </div>
                            <!-- accept  end -->
                                
                            <!--  reject start -->
                            <div class="rejectDiv container pt-4 px-lg-4 d-none">
                            <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center">
                                <h6 class="sm-text text-center user_title"></h6>
                                <div class="center-card">
                                    <svg width="96" height="96" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M47.9 95.8C21.5 95.8 0 74.3 0 47.9C0 21.5 21.5 0 47.9 0C74.3 0 95.8 21.5 95.8 47.9C95.8 74.3 74.3 95.8 47.9 95.8ZM47.9 4C23.7 4 4 23.7 4 47.9C4 72.1 23.7 91.8 47.9 91.8C72.1 91.8 91.8 72.1 91.8 47.9C91.8 23.7 72.1 4 47.9 4Z" fill="#2557A7"/>
                                        <path d="M53.9004 65.7012H41.9004C40.8004 65.7012 39.9004 64.8012 39.9004 63.7012C39.9004 62.6012 40.8004 61.7012 41.9004 61.7012H53.9004C55.0004 61.7012 55.9004 62.6012 55.9004 63.7012C55.9004 64.8012 55.0004 65.7012 53.9004 65.7012Z" fill="#2557A7"/>
                                        <path d="M37.1008 38.9004H20.3008C19.2008 38.9004 18.3008 38.0004 18.3008 36.9004C18.3008 35.8004 19.2008 34.9004 20.3008 34.9004H37.1008C38.2008 34.9004 39.1008 35.8004 39.1008 36.9004C39.1008 38.0004 38.2008 38.9004 37.1008 38.9004Z" fill="#2557A7"/>
                                        <path d="M72.6008 38.5H55.8008C54.7008 38.5 53.8008 37.6 53.8008 36.5C53.8008 35.4 54.7008 34.5 55.8008 34.5H72.6008C73.7008 34.5 74.6008 35.4 74.6008 36.5C74.6008 37.6 73.7008 38.5 72.6008 38.5Z" fill="#2557A7"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_7358_2" x1="47.9" y1="0" x2="47.9" y2="95.8" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#2557A7"/>
                                        <stop offset="1" stop-color="#2557A7" stop-opacity="0.9"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_7358_2" x1="47.9004" y1="61.7012" x2="47.9004" y2="65.7012" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#2557A7"/>
                                        <stop offset="1" stop-color="#2557A7" stop-opacity="0.9"/>
                                        </linearGradient>
                                        <linearGradient id="paint2_linear_7358_2" x1="28.7008" y1="34.9004" x2="28.7008" y2="38.9004" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#2557A7"/>
                                        <stop offset="1" stop-color="#2557A7" stop-opacity="0.9"/>
                                        </linearGradient>
                                        <linearGradient id="paint3_linear_7358_2" x1="64.2008" y1="34.5" x2="64.2008" y2="38.5" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#2557A7"/>
                                        <stop offset="1" stop-color="#2557A7" stop-opacity="0.9"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                </div>

                                <p class="sm-text text-center mb-4">No Worries ! You can always check out other participants !</p>
                                <button type="button" class="btn speed-v1-b-btn browse_participants_btn">Browse Participants</button>
                            </div>
                            </div>
                            <!--  reject end -->
                            <!-- speed networking accept , reject html end -->
                            <!-- new template speed networking end  -->

                            <div id='loaderArea-participant' class="text-center"></div>
                            <div class="list-scroll-onmobile px-4 py-3 pb-xl-0" id="speed_networking-participant"></div>
                        </div>
                    </div>

                    <!--connect request modal start-->
                    <div class="modal fade speed-req" id="connectModal" tabindex="-1" aria-labelledby="connectModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-white">
                                    <h5 class="modal-title sm-text" id="connectModalLabel">Speed Networking Request !</h5>
                                    <div class="respond-time">
                                        <span class="p-1">Time to respond</span>
                                        <span class="seconds p-1 countDownTimer">30 secs</span>
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <div class="d-flex" style="gap: 21px;">
                                        <div class="d-flex flex-column align-items-center flex-shrink-0" style="gap: 9px;">
                                            <img class="spd-req-profile user_profile_img" src="" alt="">
                                            <a href="" target="_blank" class="view-v1-profile user_profile_link">View Profile</a>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="sm-text fw-500 user_title"></h5>
                                            <hr class="my-2" style="width: 40%;">
                                            <div class="d-flex flex-wrap align-items-center" style="gap: 12px;">
                                                <div class="speed-v1-info">
                                                    <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V14.5C0 15.3281 0.671875 16 1.5 16H4.5V13.5C4.5 12.6719 5.17188 12 6 12C6.82812 12 7.5 12.6719 7.5 13.5V16H10.5C11.3281 16 12 15.3281 12 14.5V1.5C12 0.671875 11.3281 0 10.5 0H1.5ZM2 7.5C2 7.225 2.225 7 2.5 7H3.5C3.775 7 4 7.225 4 7.5V8.5C4 8.775 3.775 9 3.5 9H2.5C2.225 9 2 8.775 2 8.5V7.5ZM5.5 7H6.5C6.775 7 7 7.225 7 7.5V8.5C7 8.775 6.775 9 6.5 9H5.5C5.225 9 5 8.775 5 8.5V7.5C5 7.225 5.225 7 5.5 7ZM8 7.5C8 7.225 8.225 7 8.5 7H9.5C9.775 7 10 7.225 10 7.5V8.5C10 8.775 9.775 9 9.5 9H8.5C8.225 9 8 8.775 8 8.5V7.5ZM2.5 3H3.5C3.775 3 4 3.225 4 3.5V4.5C4 4.775 3.775 5 3.5 5H2.5C2.225 5 2 4.775 2 4.5V3.5C2 3.225 2.225 3 2.5 3ZM5 3.5C5 3.225 5.225 3 5.5 3H6.5C6.775 3 7 3.225 7 3.5V4.5C7 4.775 6.775 5 6.5 5H5.5C5.225 5 5 4.775 5 4.5V3.5ZM8.5 3H9.5C9.775 3 10 3.225 10 3.5V4.5C10 4.775 9.775 5 9.5 5H8.5C8.225 5 8 4.775 8 4.5V3.5C8 3.225 8.225 3 8.5 3Z" fill="#555555"/>
                                                    </svg>
                                                    <span>Data Infotech Ltd.,</span>
                                                </div>
                                                <div class="speed-v1-info">
                                                    <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M6.74062 15.6405C8.34375 13.629 12 8.7539 12 6.01557C12 2.69447 9.3125 0 6 0C2.6875 0 0 2.69447 0 6.01557C0 8.7539 3.65625 13.629 5.25938 15.6405C5.64375 16.1198 6.35625 16.1198 6.74062 15.6405ZM6 4.01038C6.53043 4.01038 7.03914 4.22164 7.41421 4.59768C7.78929 4.97373 8 5.48376 8 6.01557C8 6.54738 7.78929 7.0574 7.41421 7.43345C7.03914 7.8095 6.53043 8.02076 6 8.02076C5.46957 8.02076 4.96086 7.8095 4.58579 7.43345C4.21071 7.0574 4 6.54738 4 6.01557C4 5.48376 4.21071 4.97373 4.58579 4.59768C4.96086 4.22164 5.46957 4.01038 6 4.01038Z" fill="#555555"/>
                                                    </svg>
                                                    <span class="user_fullLocation"></span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center mt-4" style="gap: 6px;">
                                                <button type="button" class="btn std-btn accept_btn">Accept</button>
                                                <button type="button" class="btn bor-btn reject_btn" data-bs-dismiss="modal">Ignore</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- conversation user -->
                    <div id="users-chat" class="position-relative" data-channel_id="" data-chatwith="" style="display: none;">
                        <div class="py-3 user-chat-topbar">
                            <div class="row align-items-center flex-nowrap">
                                <div class="col">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-block d-xl-none me-3">
                                            <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                                <i class="bx bx-chevron-left align-middle text-white"></i>
                                            </a>
                                        </div>
                                        <div id="user-chat-topbar-info" class="flex-grow-1 overflow-hidden d-none">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center me-3 ms-0">
                                                    <img src="<?= TAOH_OPS_PREFIX . '/avatar/PNG/128/default.png'; ?>" class="rounded-circle avatar-sm" alt="user-profile-image">
                                                    <span class="user-status"></span>
                                                </div>
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <h6 class="text-truncate mb-0 fs-18">
                                                        <a href="#" id="user_name" class="cw_direct_message_title user-profile-show text-reset"></a>
                                                    </h6>
                                                    <p class="user-status-text text-truncate text-muted mb-0"><small></small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <ul class="list-inline user-chat-nav text-end mb-0">
                                        <!-- video button -->
                                        <!-- <li class="list-inline-item">                                                
                                            <div class="links-list-item" data-bs-container=".chat-input-links" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top"
                                                data-bs-content="<div class='loader-line'><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div></div>">
                                                <button type="button" class="btn nav-btn chat-video" data-bs-toggle="modal" data-bs-target="#v-channel-room">
                                                    <i class="bx bx-video"></i>
                                                </button>
                                            </div>
                                        </li> -->
                                        <!--<li class="list-inline-item">
                                            <div class="dropdown">
                                                <button class="btn nav-btn" type="button" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bx-search"></i>
                                                </button>
                                                <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                                                    <div class="search-box p-2">
                                                        <input type="text" class="form-control" placeholder="Search.."
                                                               id="searchChatMessage">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-inline-item d-none d-lg-inline-block me-2 ms-0">
                                            <button type="button" class="btn nav-btn" data-bs-toggle="modal"
                                                    data-bs-target=".audiocallModal">
                                                <i class="bx bxs-phone-call"></i>
                                            </button>
                                        </li>-->

                                        <li class="list-inline-item d-none  me-2 ms-0">
                                            <button type="button" class="btn nav-btn chat-video" id="user-chat-video" data-type="1"><i class="bx bx-video"></i></button>
                                        </li>
                                        <!-- <button type="button" class="btn nav-btn btn-primary d-xl-none user-profile-sidebar-show">
                                                <i class="bx bx-menu-alt-right text-white"></i>
                                        </button> -->

                                        <li class="list-inline-item d-xl-none">
                                            <div class="dropdown user-chat-nav">
                                                <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="">
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none user-profile-sidebar-show" href="#">View Profile <i class="bx bx-user text-muted"></i></a>
                                                    <!-- <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a> -->
                                                </div>
                                            </div>
                                        </li>

                                        <!--<li class="list-inline-item d-none d-lg-inline-block me-2 ms-0">
                                            <button type="button" class="btn nav-btn" data-bs-toggle="modal" data-bs-target=".pinnedtabModal"><i class="bx bx-bookmark"></i></button>
                                        </li>-->

                                        <li class="list-inline-item d-none d-lg-inline-block me-2 ms-0" >
                                            <button type="button" class="btn nav-btn channelData-show" style="display:none">
                                                
                                                <i class="bx bxs-info-circle"></i>
                                            </button>
                                        </li>
                                        <!-- user-profile-show data-bs-toggle="modal" data-bs-target=".membersData" -->
                                         
                                        <!--<li class="list-inline-item">
                                            <div class="dropdown">
                                                <button class="btn nav-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none user-profile-show" href="#">View Profile <i class="bx bx-user text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Archive <i class="bx bx-archive text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Muted <i class="bx bx-microphone-off text-muted"></i></a>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Delete <i class="bx bx-trash text-muted"></i></a>
                                                </div>
                                            </div>
                                        </li>-->
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- end chat user head -->

                        

                        <!-- start chat conversation -->
                        <div class="chat-conversation p-3 p-lg-4" id="chat-conversation-direct" data-simplebar>
                            <div class="aw aw-spinner" id="ntwChatConversationLoader"></div>
                            <div class="pin_message_dm_div" ></div>
                            <div class="pin-message-v2-dm d-none">
                                <h6 class="d-flex align-items-center pin-m-header" style="gap: 4px;">
                                    <div class="flex-grow-1">
                                        <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.835171 0.875C0.835171 0.391016 1.20743 0 1.6682 0H8.3324C8.79317 0 9.16542 0.391016 9.16542 0.875C9.16542 1.35898 8.79317 1.75 8.3324 1.75H7.56445L7.86122 5.80234C8.81659 6.34648 9.57152 7.25703 9.93077 8.3918L9.9568 8.47383C10.0427 8.7418 9.99845 9.03438 9.84226 9.26133C9.68606 9.48828 9.43355 9.625 9.16542 9.625H0.835171C0.567041 9.625 0.317134 9.49102 0.158338 9.26133C-0.000457284 9.03164 -0.0421086 8.73906 0.0437972 8.47383L0.0698292 8.3918C0.429071 7.25703 1.184 6.34648 2.13938 5.80234L2.43614 1.75H1.6682C1.20743 1.75 0.835171 1.35898 0.835171 0.875ZM4.16727 10.5H5.83332V13.125C5.83332 13.609 5.46106 14 5.0003 14C4.53953 14 4.16727 13.609 4.16727 13.125V10.5Z" fill="#323232"/>
                                        </svg>
                                        Pinned Messages
                                    </div>
                                    <span class="cursor-pointer" id="arrowContainer_dm">
                                        <svg class="d-flex" id="downArrow_dm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9" />
                                        </svg>                                        
                                        <svg class="d-none" id="upArrow_dm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                    </span>
                                </h6>
                            </div>
                            <ul class="list-unstyled chat-conversation-list aw aw-logo" id="users-conversation-list">

                            </ul>
                        </div>

                        <div class="alert alert-warning copyclipboard-alert px-4" id="copyClipBoard">Message copied</div>
                        <!-- end chat conversation end -->
                    </div>


                    <div class="d-flex flex-column flex-md-row">
                        <?php 
                        if (isset($room_app) && $room_app === 'event' && isset($club_info['streaming_link']) && !empty($club_info['streaming_link'])) { ?>
                        <!-- watch party -->
                        <div class="watch-party watchPartySection flex-grow-1" style="display:none; border-right: 1px solid #d3d3d3;">
                            <div class="card h-100">
                                <div class="card-header wp-head-h" id="headingOne">
                                    <h5 class="mb-0 watch-party-welcome py-3 d-flex align-items-center justify-content-between">
                                        Welcome to Presentation Room!
                                    </h5>
                                </div>

                               <div class="card-body video-container d-flex align-items-center">
                                    <iframe 
                                        width="100%" height="315"
                                        src="<?=$club_info['streaming_link'];?>" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- conversation group -->
                        <div id="channel-chat" class="position-relative flex-grow-1" data-channel_id="" style="display: none;">
                            <div class="py-3 user-chat-topbar">
                                <div class="row align-items-center flex-nowrap">
                                    <div class="col">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 d-block d-xl-none me-3">
                                                <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                                    <i class="bx bx-chevron-left align-middle text-white"></i>
                                                </a>
                                            </div>
                                            <div id="channel-chat-topbar-info" class="flex-grow-1 overflow-hidden">
                                                <div class="d-flex align-items-center">
                                                    <div class="cw_channel_icon flex-shrink-0 chat-user-img online user-own-img align-self-center me-3">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-primary text-white">
                                                                <span class="username"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h6 class="d-flex align-items-center mb-0 fs-18" style="gap: 6px;">
                                                            <a href="#" class="text-capitalize cw_channel_title text-reset text-truncate"></a>
                                                            <button
                                                            type="button"
                                                            toggle_text="open"
                                                            class="channel_toggle btn box-shadow-none p-0">
                                                        
                                                            <svg class="channel-drp-dwn-svg" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0ZM2.63672 4.70703C2.45312 4.52344 2.45312 4.22656 2.63672 4.04492C2.82031 3.86328 3.11719 3.86133 3.29883 4.04492L4.99805 5.74414L6.69727 4.04492C6.88086 3.86133 7.17773 3.86133 7.35938 4.04492C7.54102 4.22852 7.54297 4.52539 7.35938 4.70703L5.33203 6.73828C5.14844 6.92188 4.85156 6.92188 4.66992 6.73828L2.63672 4.70703Z" fill="black"/>
                                                            </svg>
                                                        </button>
                                                        </h6>
                                                        <span class="channnel_collapsible fs-14 text-muted lh-1 py-1"></span>
                                                        <p class="channel_members_count text-truncate text-muted mb-0 "><small></small></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <ul class="list-inline user-chat-nav text-end mb-0">
                                                <li class="list-inline-item d-none d-lg-inline-block me-2 ms-0" id="channel_video_btn">
                                                    <button type="button" class="btn nav-btn chat-video" id="channel-chat-video" data-type="1">
                                                        <i class="bx bx-video"></i></button>
                                                </li>
                                                <!-- video button -->
                                                <!-- <li class="list-inline-item">
                                                    <div class="links-list-item" data-bs-container=".chat-input-links" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top"
                                                        data-bs-content="<div class='loader-line'><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div></div>">
                                                        <button type="button" class="btn nav-btn chat-video" data-bs-toggle="modal" data-bs-target="#v-channel-room">
                                                            <i class="bx bx-video"></i>
                                                        </button>
                                                    </div>
                                                </li> -->

                                                <li class="list-inline-item d-none d-lg-inline-block me-2 ms-0 load_more_dots">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn" type="button"
                                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div style="width:200px;" class="dropdown-menu dropdown-menu-end">
                                                            <div class="taoh-loader taoh-spinner" id="loader_transcript"  style="width:20px;height:20px;display:none;" ></div>

                                                            <a onclick="forwardChannelTranscript();" class="dropdown-item d-flex align-items-center justify-content-between"
                                                            href="#">Send Transcript 
                                                            
                                                            <img 
                                                                data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Click this icon to get the chat conversation via mail"
                                                                src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/images/email_fwd.png" class="send_transcript" style="width:30px;height:30px;" /></a>

                                                            <!-- in small screen to view members  -->
                                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none channel-sidebar-show fs-14" href="#">View Members<i class="bx bx-group text-muted fs-18"></i></a>
                                                        
                                                        </div>
                                                    </div>
                                                    
                                                    
                                                </li>

                                                <li class="list-inline-item d-none  me-2 ms-0">
                                                    <button type="button" class="btn nav-btn chat-video" data-bs-toggle="modal" data-bs-target="#v-channel-room"><i class="bx bx-video text-muted"></i></button>
                                                </li>
                                                <!-- <button type="button" class="btn nav-btn btn-primary d-xl-none channel-sidebar-show">
                                                    <i class="bx bx-menu-alt-right text-white"></i>
                                                </button> -->
                                                <li class="list-inline-item d-none">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none channel-sidebar-show fs-14" href="#">View Members<i class="bx bx-group text-muted fs-18"></i></a>
                                                            <!-- <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a> -->
                                                        </div>
                                                    </div> 
                                                </li>

                                            <!--<li class="list-inline-item d-none d-lg-inline-block me-2 ms-0" >
                                                <button type="button" class="btn nav-btn channelData-show" ><i class="bx bxs-info-circle"></i></button>
                                            </li>-->
                                            <!-- user-profile-show data-bs-toggle="modal" data-bs-target=".membersData" -->

                                            <!--<li class="list-inline-item d-lg-none">
                                                <div class="dropdown">
                                                    <button class="btn nav-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none user-profile-show" href="#">View Profile <i class="bx bx-user text-muted"></i></a>
                                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a>
                                                    </div>
                                                </div>
                                            </li>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- end chat user head -->
                            
                            

                            <!-- start chat conversation -->
                            <div class="chat-conversation p-3 p-lg-4" id="chat-conversation-channel" data-simplebar>
                                <div class="aw aw-spinner" id="frmChatConversationLoader"></div>

                                <div class="pin-message-v2">
                                    <h6 class="d-flex align-items-center pin-m-header" style="gap: 4px;">
                                        <div class="flex-grow-1">
                                            <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.835171 0.875C0.835171 0.391016 1.20743 0 1.6682 0H8.3324C8.79317 0 9.16542 0.391016 9.16542 0.875C9.16542 1.35898 8.79317 1.75 8.3324 1.75H7.56445L7.86122 5.80234C8.81659 6.34648 9.57152 7.25703 9.93077 8.3918L9.9568 8.47383C10.0427 8.7418 9.99845 9.03438 9.84226 9.26133C9.68606 9.48828 9.43355 9.625 9.16542 9.625H0.835171C0.567041 9.625 0.317134 9.49102 0.158338 9.26133C-0.000457284 9.03164 -0.0421086 8.73906 0.0437972 8.47383L0.0698292 8.3918C0.429071 7.25703 1.184 6.34648 2.13938 5.80234L2.43614 1.75H1.6682C1.20743 1.75 0.835171 1.35898 0.835171 0.875ZM4.16727 10.5H5.83332V13.125C5.83332 13.609 5.46106 14 5.0003 14C4.53953 14 4.16727 13.609 4.16727 13.125V10.5Z" fill="#323232"/>
                                            </svg>
                                            Pinned Messages
                                        </div>
                                        <span class="cursor-pointer" id="arrowContainer">
                                            <svg class="d-flex" id="downArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9" />
                                            </svg>                                        
                                            <svg class="d-none" id="upArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
                                        </span>
                                    </h6>
                                </div>                    

                                <ul class="list-unstyled chat-conversation-list" id="channel-conversation-list-default">
                                    <li class="chat-list ">
                                        <div class="conversation-list">
                                            <div class="chat-avatar">
                                                <img src="<?= $sidekick_avatar; ?>" alt="profile">
                                            </div>
                                            <div class="user-chat-content">
                                                <div class="ctext-wrap">
                                                    <div class="ctext-wrap-content">
                                                        <p class="mb-0 ctext-content">
                                                            <h6 class="mb-1 ctext-name text-primary teamDojoCall">TeamDojo</h6>
                                                            👋 Welcome to this self-led networking space.<br>
                                                            👉 Drop an intro, start a thread, or reply.<br />
                                                            🎥 Share a video link to connect face-to-face.<br />
                                                            You never know where one message might lead.
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="conversation-name">
                                                    <small class="text-muted time"></small>
                                                    <span class="text-success check-message-icon"><i class="bx"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                
                                
                                <ul class="list-unstyled chat-conversation-list aw aw-logo" id="channel-conversation-list">
                                
                                </ul>
                                <ul class="list-unstyled channel-video-room-link mt-3 d-none" id="">
                                    <li class="chat-list ">
                                        <div class="conversation-list" style="width: 100%; max-width: 600px; border: 1px solid #d3d3d3;">
                                            <div class="chat-avatar pt-3 pl-2 m-0">
                                                <img src="https://opslogy.com/avatar/PNG/128/avatar_045.png" alt="profile">
                                            </div>
                                            <div class="user-chat-content">
                                                <div class="ctext-wrap mb-0">
                                                    <div class="ctext-wrap-content" style="background-color: #ffffff;">
                                                        <h6 class="mb-0 ctext-name fs-13 fw-500">Andrew Take</h6>
                                                        <p class="mb-0 ctext-content fs-12 fw-400">
                                                            Join my video room to discuss about ‘Video Room Title’
                                                        </p>
                                                        <a href="#" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                                            <svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M13.0184 5.77365C14.3272 4.45218 14.3272 2.3121 13.0184 0.990637C11.8601 -0.178803 10.0347 -0.330831 8.70266 0.630449L8.66559 0.656177C8.33201 0.897082 8.25556 1.36486 8.49417 1.69932C8.73277 2.03378 9.19608 2.1133 9.52734 1.8724L9.56441 1.84667C10.308 1.31106 11.325 1.39526 11.969 2.04781C12.6987 2.78456 12.6987 3.97739 11.969 4.71414L9.36982 7.34304C8.64011 8.07979 7.45867 8.07979 6.72896 7.34304C6.08265 6.69049 5.99926 5.66372 6.52974 4.91528L6.55522 4.87786C6.79383 4.54106 6.71507 4.07328 6.3838 3.83472C6.05254 3.59615 5.58691 3.67333 5.35062 4.00779L5.32514 4.04522C4.37073 5.38773 4.5213 7.23077 5.67957 8.40021C6.98842 9.72168 9.10805 9.72168 10.4169 8.40021L13.0184 5.77365ZM0.981633 5.22635C-0.327211 6.54782 -0.327211 8.68789 0.981633 10.0094C2.1399 11.1788 3.96533 11.3308 5.29734 10.3696L5.33441 10.3438C5.66799 10.1029 5.74444 9.63514 5.50583 9.30068C5.26723 8.96622 4.80392 8.8867 4.47266 9.1276L4.43559 9.15333C3.69198 9.68894 2.67502 9.60474 2.03103 8.95219C1.30132 8.2131 1.30132 7.02027 2.03103 6.28352L4.63018 3.65696C5.35989 2.92021 6.54133 2.92021 7.27104 3.65696C7.91735 4.30951 8.00074 5.33628 7.47026 6.08706L7.44478 6.12448C7.20617 6.46128 7.28493 6.92906 7.6162 7.16762C7.94746 7.40619 8.41309 7.329 8.64937 6.99454L8.67486 6.95712C9.62927 5.61227 9.4787 3.76923 8.32043 2.59979C7.01158 1.27832 4.89195 1.27832 3.58311 2.59979L0.981633 5.22635Z" fill="#2557A7"/>
                                                            </svg>
                                                            Join Video Room !
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div class="alert alert-warning copyclipboard-alert px-4" id="copyClipBoardChannel">message copied</div>
                            <!-- end chat conversation end -->
                        </div>
                    </div>


                    <div class="d-none chat-input-bottom">
                        <div class="input-left-empty watchPartySection" style="display: none;"></div>
                        <!-- start chat input section -->
                        <div id="chat-input-container" class="position-relative flex-grow-1" style="display: none;">
                            <div class="chat-input-section p-4 border-top border-left">

                                <form id="chatForm" enctype="multipart/form-data">
                                    <div class="row g-0 align-items-center">
                                        <div class="file_Upload"></div>
                                        <div class="col-auto">
                                            <div class="chat-input-links me-md-2">
                                                <div class="links-list-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="More" hidden>
                                                    <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect" data-bs-toggle="collapse" data-bs-target="#chatinputmorecollapse" aria-expanded="false" aria-controls="chatinputmorecollapse">
                                                        <i class="bx bx-dots-horizontal-rounded align-middle"></i>
                                                    </button>
                                                </div>
                                                <div class="links-list-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Emoji">
                                                    <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect emoji-btn" id="emoji-btn">
                                                        <i class="bx bx-smile align-middle"></i>
                                                    </button>
                                                </div>

                                                <!-- video button -->
                                                <div class="links-list-item" data-bs-container=".chat-input-links" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top"
                                                    data-bs-content="<div class='loader-line'><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div></div>">
                                                    <button type="button" class="btn nav-btn chat-video round-video" data-bs-toggle="modal" data-bs-target="#v-channel-room">
                                                        <i class="bx bx-video"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="position-relative">
                                                <div class="chat-input-feedback">Please Enter a Message</div>
                                                <!-- <input autocomplete="off" type="text" name="chat_input" class="form-control bg-light border-0 chat-input mb-0" autofocus id="chat_input" placeholder="Type your message..."> -->

                                                <div class="emoji-chat-wrapper" style="position: relative; display: inline-block; width: 100%">
                                                    <input autocomplete="off" type="text" name="chat_input" class="form-control bg-light border-0 chat-input mb-0 pr-5" id="chat_input" placeholder="Type your message...">
                                                    <button type="button" id="emojiToggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;"><i class="far fa-smile text-muted emoji_placeholder "></i></button>

                                                    <div id="emojiPicker" style="display: none; position: absolute; bottom: 40px; right: 0; background: white; border: 1px solid #ccc; padding: 5px; width: 310px; max-height: 250px; overflow-y: auto; z-index: 999;">
                                                        <div id="emojiCategories" style="display: flex; flex-wrap: wrap; border-bottom: 1px solid #ccc; margin-bottom: 5px;">
                                                            <!-- Tabs will be injected dynamically -->
                                                        </div>
                                                        <div id="emojiGrid" style="display: flex; flex-wrap: wrap; gap: 5px;"></div>
                                                    </div>
                                                </div>

                                                <!--<div class="chat-input-typing">
                                                    <span class="typing-user d-flex">Joseph A. Mickey is typing
                                                        <span class="typing ms-2">
                                                            <span class="dot"></span>
                                                            <span class="dot"></span>
                                                            <span class="dot"></span>
                                                        </span>
                                                    </span>
                                                </div>-->
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="chat-input-links ms-2 gap-md-1">
                                                <div class="links-list-item d-flex align-items-center">
                                                    <button type="submit" class="btn btn-primary btn-lg chat-send waves-effect waves-light" id="chat-send-btn" data-bs-toggle="collapse" data-bs-target=".chat-input-collapse1.show" title="Send">
                                                        <i class="bx bxs-send align-middle"></i>
                                                    </button>
                                                </div>
                                                <div class="links-list-item d-none " data-bs-container=".chat-input-links" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true" data-bs-placement="top"
                                                    data-bs-content="<div class='loader-line'><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div><div class='line'></div></div>">
                                                    <!--<button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect" onclick="audioPermission()">
                                                                    <i class="bx bx-video align-middle"></i>
                                                                </button>  -->
                                                    <button type="button" class="btn nav-btn chat-video" data-bs-toggle="modal" data-bs-target="#v-channel-room">
                                                        <i class="bx bx-video fs-35 text-muted align-middle"></i></button>
                                            
                                                
                                                    <!--<div class="dropdown">
                                                        
                                                            <button type="button" class="btn btn-link text-decoration-none btn-sm waves-effect" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                            >
                                                                <i class="bx bx-video fs-40 text-muted align-middle"></i>
                                                            </button>
                                                            
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                            
                                                                <a id="user-chat-video-bottom"  data-type="1" class="chat-video dropdown-item d-flex align-items-center justify-content-between"
                                                                href="#">Share Your Meet.TAO.ai link</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a id="user-chat-video-link-bottom" data-type="3" class="chat-video dropdown-item d-flex align-items-center justify-content-between"
                                                                href="#">Share Your Video Link</a>
                                                            </div>
                                                        </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="chat-input-collapse chat-input-collapse1 collapse" id="chatinputmorecollapse">
                                    <div class="card mb-0">
                                        <div class="card-body py-3">
                                            <!-- Swiper -->
                                            <div class="swiper chatinput-links">
                                                <div class="swiper-wrapper">
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2 position-relative">
                                                            <div>
                                                                <input id="attachedfile-input" type="file" class="d-none" accept=".zip,.rar,.7zip,.pdf" multiple>
                                                                <label for="attachedfile-input" class="avatar-sm mx-auto stretched-link">
                                                                    <span class="avatar-title fs-18 bg-primary-subtle  text-primary  text-primary rounded-circle">
                                                                        <i class="bx bx-paperclip"></i>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase mt-3 mb-0 text-body text-truncate">
                                                                Attached</h5>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2">
                                                            <div class="avatar-sm mx-auto">
                                                                <div class="avatar-title fs-18 bg-primary-subtle text-primary  text-primary rounded-circle">
                                                                    <i class="bx bxs-camera"></i>
                                                                </div>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0"><a href="#"
                                                                                                                        class="text-body stretched-link"
                                                                                                                        onclick="cameraPermission()">Camera</a></h5>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2 position-relative">
                                                            <div>
                                                                <input id="galleryfile-input" type="file" class="d-none"
                                                                    accept="image/png, image/gif, image/jpeg" multiple>
                                                                <label for="galleryfile-input"
                                                                    class="avatar-sm mx-auto stretched-link">
                                                                <span class="avatar-title fs-18 bg-primary-subtle text-primary rounded-circle"><i class="bx bx-images"></i></span>
                                                                </label>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0">Gallery
                                                            </h5>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2">
                                                            <div>
                                                                <input id="audiofile-input" type="file" class="d-none"
                                                                    accept="audio/*" multiple>
                                                                <label for="audiofile-input"
                                                                    class="avatar-sm mx-auto stretched-link">
                                                                <span class="avatar-title fs-18 bg-primary-subtle text-primary  text-primary rounded-circle">
                                                                    <i class="bx bx-headphone"></i>
                                                                </span>
                                                                </label>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0">Audio</h5>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2">
                                                            <div class="avatar-sm mx-auto">
                                                                <div  class="avatar-title fs-18 bg-primary-subtle text-primary  text-primary rounded-circle">
                                                                    <i class="bx bx-current-location"></i>
                                                                </div>
                                                            </div>

                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0"><a href="#"
                                                                                                                        class="text-body stretched-link"
                                                                                                                        onclick="getLocation()">Location</a></h5>
                                                        </div>
                                                    </div>
                                                    <div class="swiper-slide">
                                                        <div class="text-center px-2">
                                                            <div class="avatar-sm mx-auto">
                                                                <div class="avatar-title fs-18 bg-primary-subtle text-primary  text-primary rounded-circle">
                                                                    <i class="bx bxs-user-circle"></i>
                                                                </div>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0"><a href="#"
                                                                                                                        class="text-body stretched-link" data-bs-toggle="modal"
                                                                                                                        data-bs-target=".contactModal">Contacts</a></h5>
                                                        </div>
                                                    </div>

                                                    <div class="swiper-slide d-block d-sm-none">
                                                        <div class="text-center px-2">
                                                            <div class="avatar-sm mx-auto">
                                                                <div  class="avatar-title fs-18 bg-primary-subtle text-primary  text-primary rounded-circle">
                                                                    <i class="bx bx-microphone"></i>
                                                                </div>
                                                            </div>
                                                            <h5 class="fs-11 text-uppercase text-truncate mt-3 mb-0"><a href="#"
                                                                                                                        class="text-body stretched-link">Audio</a></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="replyCard">
                                <div class="card mb-0">
                                    <div class="card-body py-3">
                                        <div class="replymessage-block mb-0 d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <h5 class="conversation-name"></h5>
                                                <p class="mb-0"></p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <button type="button" id="close_toggle" class="btn btn-sm btn-link mt-n2 me-n3 fs-18">
                                                    <i class="bx bx-x align-middle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end chat input section -->

                    <div id="no_dm_block" class="position-relative" style="display: none;">
                        <!-- 0 day scenario will come here-->
                        <div class="d-flex flex-column align-items-center py-5" style="gap: 12px;">
                            <span class="fs-18 mb-5">Choose anyone to continue the conversation or go the directory to initiate the chat</span>
                            <img style="width: 100%; max-width: 300px;" src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/images/no-chat.svg" class="no_comments_img" alt="No comments">
                        </div>
                    </div>

                </div>
                <!-- end chat conversation section -->

                <!-- start chat reply sidebar -->
                <div class="chat-reply-sidebar" id="chat-reply-sidebar">
                    <div class="position-relative h-100">
                        <div class="user-chat-nav">
                            <div class="d-flex w-100">
                                <div class="flex-grow-1">
                                    <button type="button" class="btn nav-btn chat-reply-show">
                                        <i class="bx bx-x d-none d-lg-inline"></i>
                                        <i class="bx bx-left-arrow-alt d-inline d-lg-none"></i>
                                    </button>
                                </div>
                                <!--<div class="flex-shrink-0">
                                    <div class="dropdown">
                                        <button class="btn nav-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none user-profile-show" href="#">View Profile <i class="bx bx-user text-muted"></i></a>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Archive <i class="bx bx-archive text-muted"></i></a>
                                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Muted <i class="bx bx-microphone-off text-muted"></i></a>
                                        </div>
                                    </div>
                                </div>-->
                            </div>
                        </div>

                        <div class="chat-reply-conversation pl-3 pr-3" id="chat-reply-conversation" data-simplebar>
                            <div id="channel-reply-message-block" class="channel-reply-message-block mb-0 d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="conversation-name"></h5>
                                    <p class="mb-0 conversation-text"></p>
                                </div>
                            </div>
                            <ul class="list-unstyled chat-reply-conversation-list aw aw-logo" id="chat-reply-conversation-list">

                            </ul>
                        </div>                    

                        <!-- start chat reply input section -->
                        <div id="chat-reply-input-container" class="chat-input-bottom" style="z-index: 999999;">
                            <div class="chat-reply-input-section p-4 border-top border-left">
                                <form id="chatReplyForm" enctype="multipart/form-data">
                                    <input type="hidden" name="reply_comment_id" id="reply_comment_id">
                                    <div class="row g-0 align-items-center">
                                        <div class="file_Upload"></div>
                                        <div class="col-auto">
                                            <div class="chat-input-links me-md-2">
                                                <div class="links-list-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Emoji">
                                                    <button type="button" class="btn btn-link text-decoration-none btn-lg waves-effect emoji-btn" id="reply-emoji-btn">
                                                        <i class="bx bx-smile align-middle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="position-relative">
                                                <div class="chat-reply-input-feedback">Please Enter a Message</div>
                                                <input autocomplete="off" type="text" name="chat_reply_input" class="form-control bg-light border-0 chat-reply-input" autofocus id="chat_reply_input" placeholder="Type your message...">
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="chat-input-links ms-2 gap-md-1">
                                                <div class="links-list-item">
                                                    <button type="submit" class="btn btn-primary btn-lg chat-send waves-effect waves-light" id="chat-reply-send-btn" data-bs-toggle="collapse" data-bs-target=".chat-input-collapse1.show" title="Send">
                                                        <i class="bx bxs-send align-middle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- end chat reply input section -->
                     </div>
                </div>
                <!-- end chat reply sidebar -->

                <!-- start chat like sidebar -->
                <div class="networking-sidebar chat-like-sidebar" id="chat-like-sidebar">
                    <div class="user-chat-nav">
                        <div class="d-flex w-100">
                            <div class="flex-grow-1">
                                <button type="button" class="btn nav-btn chat-like-show">
                                    <i class="bx bx-x d-none d-lg-inline"></i>
                                    <i class="bx bx-left-arrow-alt d-inline d-lg-none"></i>
                                </button>
                            </div>                            
                        </div>
                    </div>

                    <div class="chat-like-conversation pl-3 pr-3" id="chat-like-conversation" data-simplebar>
                        <div id="channel-like-message-block" class="channel-like-message-block mb-0 align-items-start">
                            <div class="flex-grow-1">
                                <h5 class="conversation-name"></h5>
                                <p class="mb-0 conversation-text"></p>
                            </div>
                        </div>
                        <ul class="list-unstyled chat-like-conversation-list aw aw-logo" id="chat-like-conversation-list">

                        </ul>
                    </div>                    
                </div>
                <!-- end chat like sidebar -->

                <!-- start User profile detail sidebar -->
                <div class="user-profile-sidebar" id="user-profile-sidebar">
                    
                </div>
                <!-- end User profile detail sidebar -->

                <!-- start channel members detail sidebar -->
                <div class="networking-sidebar channelData-sidebar" id="channelData-sidebar" >
                        <div class="user-chat-nav p-2 d-xl-none">
                            <div class="d-flex w-100">
                                <div class="flex-grow-1">
                                    <button type="button" class="btn nav-btn channel-sidebar-show bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    
                        <div class="card my-3 mx-2" id="activities_block1">
                            <div class="card-header bg-white" style="cursor: unset;">
                                <p class="fs-12 fw-400 text-black mb-0">Here is what happening !</p>
                                <h5 class="fs-19 fw-400 text-black mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                 <!-- video rooms card -->
                             <div class="video-room-activity d-flex align-items-start" style="gap: 12px;">
                                
                                <div  class="taoh-loader taoh-spinner d-block show" id="pc_loader_room_activities1"
                                                    style="width:50px;height:50px;display:block;"
                                                    ></div>
                            </div> 
                                <div class="card">
                                    <div class="card-body py-2 px-2">
                                        <ul class="p-0 mb-0" id="activities-list1">
                                            <div  class="taoh-loader taoh-spinner d-block show" id="pc_loader_activities1"
                                                        style="width:50px;height:50px;display:block;"
                                                        ></div>
                                        </ul>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="card mb-3 border-0" id="members_block">

                        </div>

                    
                </div>

                
                <!-- End channel members detail sidebar -->

                 <!-- START participants sidebar -->
                <div class="networking-sidebar participants-sidebar ovrflw-y" id="participants-sidebar" style="height: 100%;">
                    <div class="user-chat-nav p-2 d-xl-none">
                        <div class="d-flex w-100 align-items-center px-2">
                            <div class="flex-grow-1 d-sm-none">
                                <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                    <i class="ri-home-2-line align-middle text-white"></i>
                                </a>
                            </div>
                            <div class="flex-shrink-0 ">
                                <button type="button" class="btn nav-btn networking-sidebar-show bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="px-3 py-4">
                    
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#v-overlay">video overlay</button> -->

                     <?php if (function_exists('jobs_networking_widget')) { jobs_networking_widget();  } ?>
                     <button type="button" class="d-xl-none btn unmanaged-event-btn" data-toggle="modal" data-target="#agreeModal">
                        <div class="circle-highlight">
                            <div class="main-circle"></div>
                            <div class="center-circle"></div>
                        </div>
                        <div class="text-left">
                            <p class="md-text mb-0"> What to expect?</p>
                            <p class="sm-text mb-0">Click for Instructions</p>
                        </div>
                    </button>
                    
                    <div class="border-con p-3 mb-3 d-none d-xl-block">
                        <div class="d-flex align-items-center mb-2 pb-2" style="border-bottom: 1px solid #d3d3d3; gap: 6px;">
                            <div class="circle-highlight">
                                <div class="main-circle"></div>
                                <div class="center-circle"></div>
                            </div>
                            <h6 class="mb-0 guide-heading">Don't Know Where to Start</h6>
                        </div>                       

                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                            <button id="tourbutton" type="button" class="btn bor-btn d-none d-xl-block">Start Application Tour</button>
                            <button type="button" class="btn bor-btn" data-toggle="modal" data-target="<?=($room_app == 'live_now') ? '#live_now_modal' : '#agreeModal'?>">Read Instructions</button>
                        </div>
                    </div>

                    <div class="participants_profile_info"></div>

                    <div class="card mb-3 light-dark-card">
                        
                        <div class="card border-0" id="dojo_suggestion_block">
                            <div class="card-header bg-white" style="cursor: unset;">
                                <p class="fs-12 fw-400 text-black mb-0">Hey Here is what</p>
                                <h5 class="fs-19 fw-400 text-black mb-0">Dojo Says !</h5>
                            </div>                           
                            <div class="card-body" id="dojo_participant_div">


                                <p class="fs-12 fw-400 text-black mb-1">Suggested Based on 
                                    <span id="suggestion_on"><span>
                                </p>
                                <!-- Based on Roles -->
                                <div>
                                    
                                    <div class="card" id="suggestion-users">
                                        <div class="taoh-loader taoh-spinner d-block show" id="pc_loader" style="width:50px;height:50px;display:block;"></div>
                                    </div>
                                </div>

                                <!-- Based on Location -->
                                <!-- <div>
                                    <p class="text-end fs-12 fw-400 text-black my-1">Suggested Based on Location</p>
                                    <div class="card">
                                        <div class="card-body d-flex px-2" style="gap: 6px;">
                                            <img src="https://opslogy.com/avatar/PNG/128/avatar_040.png" alt="" class="round-profile-suggestion">
                                            <div class="d-flex flex-wrap align-items-start" style="gap: 1px;">
                                                <div>
                                                    <p class="fs-15 fw-500 mb-0">Andrew Take</p>
                                                    <p class="fs-13 fw-400 mb-0">Lives in California, United States</p>
                                                </div>
                                                <button type="button" class="btn std-sm-bor-btn py-0">Chat</button>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>                      

                        <div class="card border-0 mb-3" id="activities_block">
                            <div class="card-header bg-white py-3" style="cursor: unset;">
                                    <!-- <p class="fs-12 fw-400 text-black mb-0">Here is what happening !</p> -->
                                    <h5 class="fs-19 fw-400 text-black mb-0">Recent Activity</h5>
                                </div>
                            <div class="card-body">
                                <!-- video rooms card -->
                                <div class="video-room-activity">
                                    <div class="taoh-loader taoh-spinner d-block show" id="pc_loader_room_activities" style="width:50px;height:50px;display:block;"></div>
                                </div> 

                                <div class="card">
                                    <div class="card-body py-2 px-2">
                                        <ul class="p-0 mb-0" id="activities-list">
                                            <div class="taoh-loader taoh-spinner d-block show" id="pc_loader_activities" style="width:50px;height:50px;display:block;"></div>
                                        </ul>
                                    </div>
                                </div> 
                            </div>
                        </div>

                    </div><!-- card end -->

                    <div class="listwindow" style="display:none;">
                        <div class="card card-item light-dark-card">
                            <div class="card-body">
                                <h3 class="fs-17">How Networking App Works?</h3> <!-- Job Fair video -->
                                <div class="divider"><span></span></div>
                                <?php if (function_exists('taoh_video_widget')) {
                                    //taoh_video_widget('https://youtu.be/L87udpeMKa0'); 
                                     } ?>
                            </div>
                        </div>
                    </div>

                    <!-- SPONSOR SECTION STARTS -->
                                            
                    <?php
                        if (isset($room_app) && $room_app === 'event') {
                            echo taoh_sponsor_slider_widget($club_token); 
                        }
                    ?>
                    <?php
                    if(false && isset($room_app) && $room_app === 'event' && isset($event_rsvp_data)){
                        echo '<div class="card card-item p-3">';
                        echo '<h3 class="fs-17">Event Ticket Types</h3>';
                        echo '<div class="divider"><span></span></div>';
                        echo '<div class="no-gutters" id="event_info">';
                        echo !empty($event_rsvp_data['output']) ? $event_rsvp_data['output']['question'] : '';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>

                    <div class="speed_networking_hints card card-item p-1 light-dark-card" style="display: none;" >
                        <div class="card-body">
                            <h3 class="fs-17">What to Expect in Speed Networking</h3>
                            <div class="divider"><span></span></div>
                            <ul style="list-style: disc; padding-left: 20px;">
                                <li><strong>Browse Profiles:</strong> Stay in the Speed Networking channel to see others who are actively networking—review their profiles and click <strong>Connect</strong> if interested.</li>
                                <li><strong>Request to Connect:</strong> Once you click <strong>Connect</strong>, the other person has 30 seconds to accept. If accepted, you're moved to a private 1:1 chat room.</li>
                                <li><strong>Chat First, Video Optional:</strong> Start with text chat and switch to video if both are interested—you’re in control.</li>
                                <li><strong>Keep Exploring:</strong> After each chat, return to Speed Networking to meet someone new. If there’s no response, move on easily.</li>
                                <li><strong>You Choose Who to Talk To:</strong> You can accept or ignore connection requests—only connect when you're ready.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="listwindow">
                        <div class="card card-item p-1 light-dark-card" >
                            <div class="card-body">
                                <h3 class="fs-17">Break the Ice: Career Questions</h3>
                                <div class="divider"><span></span></div>
                                <?php echo $ice_break; ?>
                            </div>
                        </div>

                        <?php
                        if($allow_auto_manage){
                            ?>
                            <div class="card card-item p-3" style="display:none">
                                <h3 class="fs-17">How it work</h3>
                                <div class="divider"><span></span></div>
                                <div id="accordion" class="generic-accordion pt-4">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                <span><span class="text-color pr-2 fs-16">1.</span>Important Guidelines</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">

                                                <ul style="list-style-type:disc" class="ml-3 fs-14 lh-22 text-black-50">
                                                    <li>Stay on this page, so others could discover you.</li>
                                                    <li>Clicking Chat now or Open Chat button will take you to chat room</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- end card -->
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseOne">
                                                <span><span class="text-color pr-2 fs-16">2.</span> Find your Contact</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div class="card-body">
                                                <p class="fs-14 lh-22 text-black-50">Search for professionals according to their skills, roles, or organizations. For instance, you could look for recruiters, seekers, Python developers, product managers, or individuals working at Microsoft.<p>
                                            </div>
                                        </div>
                                    </div><!-- end card -->
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">
                                                <span><span class="text-color pr-2 fs-16">3.</span> Ice-breaker Questions</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                            <div class="card-body">
                                                <ul style="list-style-type:disc" class="ml-3 fs-14 lh-22 text-black-50">
                                                    <li>What brought you to this event?</li>
                                                    <li>What do you do?</li>
                                                    <li>What's your favorite thing to do outside of work?</li>
                                                    <li>What's your ideal career?</li>
                                                    <li>What is keeping you up at night?</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- end card -->

                                </div>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="card card-item p-3" style="display:none;">
                                <h3 class="fs-17">How it works</h3>
                                <div class="divider"><span></span></div>
                                <div id="accordion" class="generic-accordion pt-4">
                                    <div class="card">
                                        <div class="card-header" id="headingOne">
                                            <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                                <span><span class="text-color pr-2 fs-16">1.</span>Important Guidelines</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                                            <div class="card-body">

                                                <ul style="list-style-type:disc" class="ml-3 fs-14 lh-22 text-black-50">
                                                    <li>Stay on this page, so others could discover you.</li>
                                                    <li>Clicking Chat now or Open Chat button will take you to chat room</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- end card -->
                                    <div class="card">
                                        <div class="card-header" id="headingTwo">
                                            <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseOne">
                                                <span><span class="text-color pr-2 fs-16">2.</span> Find your Contact</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseTwo" class="collapse " aria-labelledby="headingTwo" data-parent="#accordion">
                                            <div class="card-body">
                                                <p class="fs-14 lh-22 text-black-50">Search for professionals according to their skills, roles, or organizations. For instance, you could look for recruiters, seekers, Python developers, product managers, or individuals working at Microsoft.<p>
                                            </div>
                                        </div>
                                    </div><!-- end card -->
                                    <div class="card">
                                        <div class="card-header" id="headingThree">
                                            <button class="btn btn-link collapsed fs-15" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseTwo">
                                                <span><span class="text-color pr-2 fs-16">3.</span> Ice-breaker Questions</span>
                                                <i class="la la-angle-down collapse-icon"></i>
                                            </button>
                                        </div>
                                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                            <div class="card-body">
                                                <ul style="list-style-type:disc" class="ml-3 fs-14 lh-22 text-black-50">
                                                    <li>What brought you to this event?</li>
                                                    <li>What do you do?</li>
                                                    <li>What's your favorite thing to do outside of work?</li>
                                                    <li>What's your ideal career?</li>
                                                    <li>What is keeping you up at night?</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- end card -->

                                </div>
                            </div>
                            <?php
                        }

                        ?>


                    </div>
                    <?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget($club_info['title'],'networking',TAOH_NETWORK_REFERRAL_URL);  } ?>
                    <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>

                    </div>                                 
                </div>
                 <!-- END participants sidebar -->

            </div>
            <!-- end user chat content -->
        </div>
        <!-- End User chat -->

      

        <!-- Start add group Modal -->
        <div class="modal fade" id="createChannelModal" tabindex="-1" role="dialog" aria-labelledby="createChannelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content modal-header-colored border-0">
                    <form action="<?= taoh_site_ajax_url(); ?>" method="post" name="createChannelForm" id="createChannelForm">
                        <div class="modal-header">
                            <h5 class="modal-title fs-16" id="createChannelModalLabel">Create New Channel</h5>
                            
                               

                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <label for="channelname" class="form-label">Channel Name</label>
                                <input type="text" name="channelname" class="form-control alphanumericInput"
                                 id="channelname" placeholder="Enter Channel Name" required >
                            </div>
                            <div class="mb-3">
                                <label for="channeldescription" class="form-label">Description</label>
                                <textarea name="channeldescription" class="form-control" id="channeldescription" rows="3" placeholder="Enter Short Description" required></textarea>
                            </div>

                            <a href="#" id="toggleMore"><i class="bx bx-plus"></i> Show More Options</a>

                            <div class="more-fields" id="extraFields">
                                <div class="mb-4 " style="">
                                    <label for="channelpasscode" class="form-label">Channel Passcode</label>
                                    <input type="text" name="channelpasscode" class="form-control" id="channelpasscode"
                                    placeholder="Enter passcode if you need channel to be password protected" >
                                </div>
                                <?php if(count($ticket_types_array) > 0){ ?>
                                <div class="mb-4 ">
                                    <label for="channel_ticket_type" class="form-label">Channel Ticket Type</label><br>
                                    
                                    <select  name="channel_ticket_type" class="form-control" id="channel_ticket_type"
                                    >
                                    <option value="">Select</option>
                                    <option value="">All</option>
                                        <?php 
                                        
                                            foreach ($ticket_types_array as $key => $value) {  ?>                                    
                                            <option value="<?php echo $value['slug'];?>"><?php echo $value['title'];?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="fs-10">(Users who registered with this ticket type can only 
                                        access the channel)</span>
                                </div>
                                <?php } ?>
                            </div>

                            

                            <div class="mb-3" style="display:none;">
                                <div class="common-toggle">
                                    <div class="field-label mb-3">Video Channel</div>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary">
                                            <input class="event_channels_video_channel" type="radio" id="video_channel_on" name="channel_video" value="1"> Yes
                                        </label>

                                        <label class="btn btn-outline-primary active">
                                            <input class="event_channels_video_channel" type="radio" id="video_channel_off" name="channel_video" value="0" checked=""> No
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4" id="channel_video_url_block" style="display:none;">
                                <label for="channel_video_url" class="form-label">Channel Video Url</label>
                                <input type="text" name="channel_video_url" class="form-control" id="channel_video_url" placeholder="Enter Channel Video Url" required>
                                <span class="text-danger" id="channel_video_url_error"></span>
                                (or)
                                <button class="btn btn-primary" name="jitsi_for_channel"  id="jitsi_for_channel" >Generate Jitsi Url</button>
                                
                            </div>
                            
                        </div>
                        <div class="modal-footer border-top-dashed">
                           <button type="submit" class="btn btn-primary m-0"><i class="fa fa-play-circle-o mr-2" aria-hidden="true"></i> Create Channel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- End add group Modal -->

        <!-- Channel Password Modal -->
        <div class="modal fade dojo-modal" id="channel_password_modal" tabindex="-1" role="dialog" aria-labelledby="channelPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0">
                    <form action="" method="post" id="channel_password_form">
                        <input type="hidden" name="channel_password_channel_id" id="channel_password_channel_id" value="">
                        <div class="modal-header dojo_info_class">
                            <h5 class="heading" id="channelPasswordModalLabel">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="15.5491" cy="15.1051" rx="9.28352" ry="9.3986" fill="white"/>
                                    <path d="M16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32ZM13.5 21H15V17H13.5C12.6687 17 12 16.3312 12 15.5C12 14.6687 12.6687 14 13.5 14H16.5C17.3312 14 18 14.6687 18 15.5V21H18.5C19.3312 21 20 21.6688 20 22.5C20 23.3312 19.3312 24 18.5 24H13.5C12.6687 24 12 23.3312 12 22.5C12 21.6688 12.6687 21 13.5 21ZM16 8C16.5304 8 17.0391 8.21071 17.4142 8.58579C17.7893 8.96086 18 9.46957 18 10C18 10.5304 17.7893 11.0391 17.4142 11.4142C17.0391 11.7893 16.5304 12 16 12C15.4696 12 14.9609 11.7893 14.5858 11.4142C14.2107 11.0391 14 10.5304 14 10C14 9.46957 14.2107 8.96086 14.5858 8.58579C14.9609 8.21071 15.4696 8 16 8Z" fill="#0058D8"/>
                                </svg>
                                Dojo Says !
                            </h5>

                            <div>
                                <!-- dojo svg -->
                                <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                                    <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                                    <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_21)"/>
                                    <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_21)"/>
                                    <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                                    <defs>
                                        <linearGradient id="paint0_linear_7568_21" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FDCC6E"/>
                                            <stop offset="1" stop-color="#FF6600"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_7568_21" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                                            <stop offset="0.1" stop-color="#FD1D1D"/>
                                            <stop offset="0.9" stop-color="#FF6600"/>
                                        </linearGradient>
                                    </defs>
                                </svg>

                                <button type="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <svg width="13" height="13" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.73364 1.53701C9.08504 1.18562 9.08504 0.614946 8.73364 0.263548C8.38224 -0.0878494 7.81157 -0.0878494 7.46017 0.263548L4.5 3.22653L1.53701 0.266359C1.18562 -0.0850383 0.614946 -0.0850383 0.263548 0.266359C-0.0878494 0.617757 -0.0878494 1.18843 0.263548 1.53982L3.22653 4.5L0.26636 7.46299C-0.0850382 7.81438 -0.0850382 8.38505 0.26636 8.73645C0.617757 9.08785 1.18843 9.08785 1.53982 8.73645L4.5 5.77346L7.46299 8.73364C7.81438 9.08504 8.38505 9.08504 8.73645 8.73364C9.08785 8.38224 9.08785 7.81157 8.73645 7.46017L5.77347 4.5L8.73364 1.53701Z" fill="#ffffff"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="passcode">Password Protected Channel</label>
                                <input type="text" placeholder="Enter the passcode" id="passcode" class="form-control" required />
                                <span class="text-danger" style="display:none;" id="passcode_error">Incorrect passcode</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="channel_password_submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Channel Password Modal -->


        <!-- Share Video Link Modal -->
        <div class="modal fade dojo-modal" id="share_video_modal" tabindex="-1" role="dialog" aria-labelledby="shareVideoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0">
                    <form action="" method="post" id="share_video_form">
                        <input type="hidden" id="share_video_elem_id" value="">
                        <div class="modal-header dojo_info_class">
                            <h5 class="heading" id="shareVideoModalLabel">
                                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <ellipse cx="15.5491" cy="15.1051" rx="9.28352" ry="9.3986" fill="white"/>
                                    <path d="M16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32ZM13.5 21H15V17H13.5C12.6687 17 12 16.3312 12 15.5C12 14.6687 12.6687 14 13.5 14H16.5C17.3312 14 18 14.6687 18 15.5V21H18.5C19.3312 21 20 21.6688 20 22.5C20 23.3312 19.3312 24 18.5 24H13.5C12.6687 24 12 23.3312 12 22.5C12 21.6688 12.6687 21 13.5 21ZM16 8C16.5304 8 17.0391 8.21071 17.4142 8.58579C17.7893 8.96086 18 9.46957 18 10C18 10.5304 17.7893 11.0391 17.4142 11.4142C17.0391 11.7893 16.5304 12 16 12C15.4696 12 14.9609 11.7893 14.5858 11.4142C14.2107 11.0391 14 10.5304 14 10C14 9.46957 14.2107 8.96086 14.5858 8.58579C14.9609 8.21071 15.4696 8 16 8Z" fill="#0058D8"/>
                                </svg>
                                Dojo Says !
                            </h5>

                            <div>
                                <!-- dojo svg -->
                                <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                                    <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                                    <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_4)"/>
                                    <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_4)"/>
                                    <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                                    <defs>
                                        <linearGradient id="paint0_linear_7568_4" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FDCC6E"/>
                                            <stop offset="1" stop-color="#FF6600"/>
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_7568_4" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                                            <stop offset="0.1" stop-color="#FD1D1D"/>
                                            <stop offset="0.9" stop-color="#FF6600"/>
                                        </linearGradient>
                                    </defs>
                                </svg>

                                <button type="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                    <svg width="13" height="13" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.73364 1.53701C9.08504 1.18562 9.08504 0.614946 8.73364 0.263548C8.38224 -0.0878494 7.81157 -0.0878494 7.46017 0.263548L4.5 3.22653L1.53701 0.266359C1.18562 -0.0850383 0.614946 -0.0850383 0.263548 0.266359C-0.0878494 0.617757 -0.0878494 1.18843 0.263548 1.53982L3.22653 4.5L0.26636 7.46299C-0.0850382 7.81438 -0.0850382 8.38505 0.26636 8.73645C0.617757 9.08785 1.18843 9.08785 1.53982 8.73645L4.5 5.77346L7.46299 8.73364C7.81438 9.08504 8.38505 9.08504 8.73645 8.73364C9.08785 8.38224 9.08785 7.81157 8.73645 7.46017L5.77347 4.5L8.73364 1.53701Z" fill="#ffffff"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="share_video_external_link">Share your video link</label>
                                <input type="text" placeholder="Enter the link here" id="share_video_external_link" class="external_link form-control" required />
                                <span class="text-danger" style="display:none;" id="share_video_external_link_error">Incorrect passcode</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="share_video_submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Share Video Link Modal -->


        <!-- video popup -->
        <div class="modal v-overlay fade" id="v-overlay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-white" role="document">
                <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
                    <div class="modal-header flex-column py-0" style="border: none; position: relative;">
                        <div class="d-flex justify-content-end w-100">
                            <button type="button" class="btn" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.5857 2.39091C14.1323 1.84429 14.1323 0.956583 13.5857 0.409964C13.039 -0.136655 12.1513 -0.136655 11.6047 0.409964L7 5.01905L2.39091 0.414337C1.84429 -0.132282 0.956583 -0.132282 0.409964 0.414337C-0.136655 0.960956 -0.136655 1.84866 0.409964 2.39528L5.01905 7L0.414337 11.6091C-0.132282 12.1557 -0.132282 13.0434 0.414337 13.59C0.960956 14.1367 1.84866 14.1367 2.39528 13.59L7 8.98095L11.6091 13.5857C12.1557 14.1323 13.0434 14.1323 13.59 13.5857C14.1367 13.039 14.1367 12.1513 13.59 11.6047L8.98095 7L13.5857 2.39091Z" fill="#CACACA"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body pt-0 mx-auto">
                        <h6 class="mb-1 fs-12 fw-medium">Welcome to <?php echo $room_title;?></h6>
                        <div class="d-flex align-items-center flex-wrap justify-content-between mb-3" style="gap: 12px;">
                            <h4 class="m-0 fs-19" style="font-weight: 400;">How to get started?</h4>
                            <!-- <button type="button" class="btn tour-btn py-1 px-4 d-none" id="tourbutton" data-dismiss="modal" aria-label="Close">Application Tour</button> -->
                        </div>
                        <div class="vdo-container mb-3">
                            <!-- <img src="https://img.youtube.com/vi/L87udpeMKa0/maxresdefault.jpg" alt=""> -->
                            <div class="company-details-panel mb-30px" id="company-videos">
                                <div class="video-box">

                                    <!-- Update Constant TAOH_NETWORK_TOUR_VERSION in config when video change for init tour -->

                                    <!-- image url - https://img.youtube.com/vi/YouTubeID/ImageFormat.jpg -->
                                <img class="w-100 rounded-rounded lazy" src="https://img.youtube.com/vi/L87udpeMKa0/maxresdefault.jpg" data-src="https://img.youtube.com/vi/<?php echo $image_id ?? ''; ?>/maxresdefault.jpg" alt="video image">
                                <div class="video-content">
                                    <!-- <a class="icon-element icon-element-lg hover-y mx-auto" href="<?php //echo $event_arr['conttoken']['event_video'];?>"     data-fancybox="" title="Play Video"> -->
                                        <a class="icon-element icon-element-lg hover-y mx-auto" href="https://www.youtube.com/embed/L87udpeMKa0?rel=0" data-fancybox="" title="Play Video">
                                        <svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve">
                                            <path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205
                                            c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103
                                            c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716
                                            c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243
                                            c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249
                                            C49.663,29.47,49.611,29.561,49.524,29.612z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                            <!-- play svg -->
                            <!-- <svg class="play-svg" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 50C0 36.7392 5.26784 24.0215 14.6447 14.6447C24.0215 5.26784 36.7392 0 50 0C63.2608 0 75.9785 5.26784 85.3553 14.6447C94.7322 24.0215 100 36.7392 100 50C100 63.2608 94.7322 75.9785 85.3553 85.3553C75.9785 94.7322 63.2608 100 50 100C36.7392 100 24.0215 94.7322 14.6447 85.3553C5.26784 75.9785 0 63.2608 0 50ZM36.7773 28.7305C35.293 29.5508 34.375 31.1328 34.375 32.8125V67.1875C34.375 68.8867 35.293 70.4492 36.7773 71.2695C38.2617 72.0898 40.0586 72.0703 41.5234 71.1719L69.6484 53.9844C71.0352 53.125 71.8945 51.6211 71.8945 49.9805C71.8945 48.3398 71.0352 46.8359 69.6484 45.9766L41.5234 28.7891C40.0781 27.9102 38.2617 27.8711 36.7773 28.6914V28.7305Z" fill="#828282"/>
                            </svg> -->
                        </div>
                        <p class="fs-12" style="line-height: 14px;">
                            <b>Code of Conduct:</b> Treat all participants with kindness, respect diverse perspectives, and maintain a professional environment. Stay on-topic, avoid disruptive behavior, and adhere to event guidelines and confidentiality rules. By Joining the event you are entitled to abide by the COC.
                        </p>

                        <a class="btn agree-btn mx-auto mb-3 text-white">I Agree - Join Event</a>
                    </div>
                </div>
            </div>
        </div>


        <!-- v-channel-room -->
        <div class="modal fade create-v-channel-room" id="v-channel-room" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-white">
                <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
                    <!--<div class="modal-header flex-column pb-0 pt-2" style="border: none; position: relative;">
                        
                        <div class="d-flex justify-content-end w-100">
                        <button type="button" class="btn rounded-circle" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: 1px solid #d3d3d3;">
                            <svg width="10" height="10" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.4172 3.41719C20.1984 2.63594 20.1984 1.36719 19.4172 0.585938C18.6359 -0.195312 17.3672 -0.195312 16.5859 0.585938L10.0047 7.17344L3.41719 0.592187C2.63594 -0.189063 1.36719 -0.189063 0.585938 0.592187C-0.195312 1.37344 -0.195312 2.64219 0.585938 3.42344L7.17344 10.0047L0.592188 16.5922C-0.189062 17.3734 -0.189062 18.6422 0.592188 19.4234C1.37344 20.2047 2.64219 20.2047 3.42344 19.4234L10.0047 12.8359L16.5922 19.4172C17.3734 20.1984 18.6422 20.1984 19.4234 19.4172C20.2047 18.6359 20.2047 17.3672 19.4234 16.5859L12.8359 10.0047L19.4172 3.41719Z" fill="#d3d3d3"/>
                            </svg>
                        </button>
                        </div>
                    </div>-->
                    <div class="modal-header">
                            <h5 class="modal-title fs-16" id="createChannelModalLabel">Start a Video Meeting !</h5>
                            
                               

                                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close">
                                </button>
                        </div>

                    <div class="modal-body">
                       
                        
                        <p class="fs-14 fw-400 text-black mb-0">Create or share a video room and invite others to join</p>
                        <hr class="my-2" style="border: none; border-top: 2px solid #d3d3d3;">
                        <form id="createVideoForm" method="post" action="<?= taoh_site_ajax_url(); ?>">
                            <div class="row">
                                <div class="form-group col-lg-8">
                                    <label for="video_name" class="fs-14 fw-400">Name of the Video Room <span class="text-danger">*</span></label>
                                    <input type="text" 
                                    value="<?php echo $user_info_obj->chat_name ?? ''; ?> Room"
                                    id="video_name" name="video_name"  class="form-control alphanumericInput">
                                </div>
                            </div>

                            <a href="#" id="toggleMoreOption"><i class="bx bx-plus"></i> Show More Options</a>

                            <div class="more-fields" id="extraRoomFields">
                                <div class="row">
                                    <div class="form-group col-lg-8">
                                        <label for="video_desc" class="fs-14 fw-400">Description of the Video Room </label>
                                        <textarea name="video_desc" id="video_desc" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 row">
                                        
                                        <label for="" class="fs-14 fw-400 mb-1">Choice of the Room <span class="text-danger">*</span></label>

                                        <div class="col-lg-6 d-inline-flex align-items-center" style="gap: 4px;">
                                            <input class="room_type" type="radio" name="room-choice" value="0" id="tao_rooms" checked="checked">
                                            <label for="tao_rooms" class="fs-14 fw-400 mb-0">Tao Rooms (Hosted on this platform)</label>
                                        </div>
                                        <div class="col-lg-6 d-inline-flex align-items-center" style="gap: 4px;">
                                            <input class="room_type" type="radio" name="room-choice" value="1" id="external_room">
                                            <label for="external_room" class="fs-14 fw-400 mb-0">External Rooms (Zoom Meet etc.,)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="show_external_link_box"  style="display:none;">
                                    <div class="form-group">
                                        <label for="ext_link" class="col-lg-12 fs-14 fw-400 px-0">Enter the link of your external room* (Zoom, Google Meet etc)</label>
                                        <input type="text" name="ext_link" id="ext_link" class="col-lg-8 form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top-dashed" style="gap: 12px;">
                                 <button type="submit" class="btn btn-primary m-0"><i class="fa fa-play-circle-o mr-2" aria-hidden="true"></i> Create & Share Room</button>
                             </div>
                        </form>
                        
                        <p class="fs-10 fw-400 ml-auto mt-2 mb-0" style="color: #787272; width: fit-content;">By creating a room, you agree to our Terms & Conditions</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- v-channel-room end -->

        <!-- Delete Modal -->
        <div class="modal fade dojo-modal" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0">
                    <div class="modal-header dojo_warning_class">
                        <h5 class="heading" id="deleteModalLabel">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.9988 0C16.8864 0 17.7052 0.535714 18.1552 1.41429L31.6562 27.7C32.1125 28.5857 32.1125 29.6786 31.6687 30.5643C31.2249 31.45 30.3936 32 29.4998 32H2.49787C1.60406 32 0.772748 31.45 0.328967 30.5643C-0.114815 29.6786 -0.108565 28.5786 0.341468 27.7L13.8424 1.41429C14.2925 0.535714 15.1113 0 15.9988 0ZM15.9988 9.14286C15.1675 9.14286 14.4987 9.90714 14.4987 10.8571V18.8571C14.4987 19.8071 15.1675 20.5714 15.9988 20.5714C16.8301 20.5714 17.4989 19.8071 17.4989 18.8571V10.8571C17.4989 9.90714 16.8301 9.14286 15.9988 9.14286ZM17.999 25.1429C17.999 24.5366 17.7883 23.9553 17.4132 23.5266C17.0381 23.098 16.5293 22.8571 15.9988 22.8571C15.4684 22.8571 14.9596 23.098 14.5845 23.5266C14.2094 23.9553 13.9987 24.5366 13.9987 25.1429C13.9987 25.7491 14.2094 26.3304 14.5845 26.7591C14.9596 27.1878 15.4684 27.4286 15.9988 27.4286C16.5293 27.4286 17.0381 27.1878 17.4132 26.7591C17.7883 26.3304 17.999 25.7491 17.999 25.1429Z" fill="#FFBA37"/>
                            </svg>
                            Dojo Says !
                        </h5>

                        <div>
                            <!-- dojo svg -->
                            <svg class="dojo-v1-svg" width="69" height="51" viewBox="0 0 69 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M49.5658 17C49.5658 17 44.022 11.0179 45.1519 0L58 2.08076C58 2.08076 57.4602 11.3992 49.5658 17Z" fill="#FF3B38"/>
                                <path d="M52 19.4342C52 19.4342 57.9821 24.978 69 23.8481L66.9192 11C66.9192 11 57.6008 11.5398 52 19.4342Z" fill="#FF3B38"/>
                                <path d="M66 41C66 36.6664 65.1464 32.3752 63.488 28.3714C61.8296 24.3677 59.3989 20.7298 56.3345 17.6655C53.2702 14.6011 49.6323 12.1704 45.6286 10.512C41.6248 8.85357 37.3336 8 33 8C28.6664 8 24.3752 8.85357 20.3714 10.512C16.3677 12.1704 12.7298 14.6011 9.66547 17.6655C6.60114 20.7298 4.17038 24.3677 2.51197 28.3714C0.853569 32.3752 -3.78857e-07 36.6664 0 41L33 41H66Z" fill="url(#paint0_linear_7568_3)"/>
                                <path d="M50 41C50 38.7675 49.5603 36.5569 48.706 34.4944C47.8516 32.4318 46.5994 30.5578 45.0208 28.9792C43.4422 27.4006 41.5682 26.1484 39.5056 25.294C37.4431 24.4397 35.2325 24 33 24C30.7675 24 28.5569 24.4397 26.4944 25.294C24.4318 26.1484 22.5578 27.4006 20.9792 28.9792C19.4006 30.5578 18.1484 32.4318 17.294 34.4944C16.4397 36.5569 16 38.7675 16 41L33 41H50Z" fill="url(#paint1_linear_7568_3)"/>
                                <path d="M32.6953 33.0198C32.7581 32.9963 32.8268 32.9995 32.9012 33.0011C32.9987 33.0044 33.0194 33.0919 33.0293 33.1681C33.045 33.2921 33.0698 33.4258 33.1054 33.5684C33.1153 33.6097 33.1211 33.6519 33.1244 33.6932C33.1351 33.8764 33.1483 34.0182 33.2004 34.177C33.2475 34.3237 33.2484 34.4785 33.2938 34.6179C33.3195 34.6957 33.3567 34.8188 33.3674 34.9226C33.3773 35.0158 33.4005 35.1009 33.436 35.1795C33.4865 35.2897 33.4832 35.4218 33.5294 35.5377C33.5824 35.6706 33.6055 35.8019 33.6568 35.9307C33.6882 36.0126 33.7105 36.0928 33.7345 36.1747C33.746 36.2119 33.7576 36.2443 33.7708 36.2711C33.8204 36.3748 33.8502 36.4818 33.9023 36.5879C33.9883 36.7614 34.0709 36.9753 34.1768 37.169C34.2594 37.3205 34.3685 37.5118 34.4769 37.6552C34.5645 37.7744 34.619 37.8797 34.7125 37.9883C34.8547 38.148 35.0539 38.4032 35.244 38.5953C35.4143 38.7671 35.5962 38.9025 35.8293 39.0913C35.926 39.1691 36.0286 39.2444 36.1368 39.3182C36.3766 39.4795 36.5866 39.6091 36.7626 39.7039C37.1471 39.9122 37.5802 40.1051 38.063 40.2801C38.0887 40.2899 38.1523 40.3101 38.2515 40.3417C38.3375 40.3677 38.4226 40.4041 38.5053 40.4268C38.6144 40.456 38.7012 40.4827 38.7657 40.5087C38.8294 40.5346 38.8996 40.554 38.9774 40.5686C39.1038 40.5913 39.2287 40.6562 39.3659 40.6756C39.4403 40.6853 39.5205 40.7064 39.6098 40.7412C39.7395 40.7907 39.8677 40.7891 40.0041 40.8247C40.0091 40.8255 40.0487 40.8377 40.1231 40.8604C40.2372 40.8936 40.3315 40.9058 40.4555 40.9236C40.5555 40.9382 40.687 40.9965 40.8093 41.016C40.8192 41.0168 40.873 41.0233 40.9722 41.0338C41.0019 41.037 41.0325 41.0419 41.0623 41.046C41.0871 41.0492 41.1152 41.0557 41.1507 41.0662C41.2722 41.1043 41.3963 41.1294 41.5219 41.1424C41.693 41.1594 41.7831 41.1748 41.8989 41.2729C42.0105 41.3669 42.0675 41.567 41.8658 41.6076C41.8162 41.6189 41.693 41.6448 41.4938 41.687C41.4128 41.704 41.3293 41.7162 41.24 41.721C41.0978 41.7299 41.0127 41.7356 40.9854 41.7372C40.9068 41.7437 40.8572 41.7486 40.8366 41.7518C40.6845 41.7688 40.4927 41.8328 40.3877 41.8361C40.2381 41.8401 40.1355 41.8491 40.0818 41.862C39.8768 41.9123 39.7925 41.9398 39.637 41.9568C39.5403 41.9674 39.4775 41.9771 39.4477 41.986C39.3088 42.0282 39.2204 42.0533 39.184 42.0581C39.0071 42.0897 38.8856 42.1181 38.8194 42.1424C38.7359 42.174 38.6698 42.1829 38.5681 42.2129C38.4135 42.2583 38.2317 42.3158 38.0225 42.3823C37.8844 42.4269 37.7604 42.4731 37.6488 42.5217C37.5959 42.5444 37.4736 42.5979 37.2818 42.6805C36.8445 42.8685 36.4113 43.1262 36.0442 43.4423C35.7731 43.6757 35.5151 43.9342 35.2679 44.2154C35.1464 44.3532 35.0191 44.5469 34.9216 44.6709C34.8455 44.7681 34.7777 44.8929 34.6959 45.0072C34.6587 45.0615 34.6289 45.1069 34.6066 45.1425C34.4669 45.3799 34.3842 45.5266 34.3586 45.5817C34.2966 45.7179 34.2189 45.8889 34.123 46.0939C33.9948 46.3686 33.9056 46.5826 33.8568 46.7358C33.827 46.8298 33.8055 46.893 33.7906 46.9238C33.7559 47.0008 33.7336 47.0624 33.7245 47.1102C33.7071 47.2017 33.6435 47.3217 33.6203 47.4351C33.6005 47.53 33.5889 47.5907 33.5476 47.692C33.5187 47.7617 33.5021 47.8112 33.4988 47.8412C33.4831 47.9514 33.4765 48.0259 33.4451 48.1216C33.4087 48.2253 33.3839 48.3355 33.3698 48.4522C33.3508 48.6086 33.293 48.7577 33.2706 48.8939C33.25 49.0227 33.25 49.2026 33.221 49.342C33.1979 49.4482 33.1756 49.5292 33.164 49.6338C33.1284 49.9344 33.1342 50.2197 33.1144 50.543C33.1061 50.6638 33.0598 51.0779 32.8375 50.9871C32.6944 50.9296 32.6647 50.791 32.6597 50.6573C32.6581 50.5925 32.6556 50.4822 32.6531 50.3299C32.6498 50.103 32.5522 49.8882 32.5398 49.6662C32.5365 49.6111 32.5299 49.5325 32.52 49.4296C32.5076 49.3031 32.4737 49.218 32.4415 49.0884C32.4109 48.9652 32.42 48.7764 32.3803 48.6548C32.3472 48.5543 32.3125 48.4441 32.3018 48.308C32.2951 48.2067 32.2629 48.0705 32.2067 47.8987C32.1951 47.8606 32.1827 47.7812 32.1728 47.6597C32.1695 47.6256 32.1505 47.5551 32.1141 47.4506C32.0926 47.3866 32.0761 47.325 32.0645 47.2634C32.0471 47.1661 32.0372 47.0908 32.0075 47.0146C31.9545 46.8785 31.938 46.7326 31.8909 46.5867C31.8347 46.4109 31.7884 46.2674 31.7528 46.1548C31.6991 45.983 31.647 45.8403 31.5982 45.7277C31.4808 45.4538 31.3858 45.2471 31.3163 45.111C31.1932 44.8719 31.0948 44.7058 31.022 44.6126C30.8699 44.4173 30.76 44.2366 30.612 44.0834C30.4268 43.8889 30.2516 43.7195 30.0308 43.5712C29.9101 43.4894 29.801 43.3937 29.6588 43.3087C29.4422 43.1798 29.2562 43.0761 29.1008 42.9967C28.8553 42.8719 28.6379 42.7932 28.3245 42.6757C28.265 42.6539 28.2088 42.6433 28.1542 42.619C28.0856 42.589 28.012 42.5639 27.9335 42.5436C27.8219 42.5145 27.7483 42.4942 27.7095 42.4796C27.5524 42.4245 27.4284 42.4197 27.2755 42.3645C27.1994 42.337 27.0828 42.3135 26.9249 42.294C26.8398 42.2843 26.6902 42.2252 26.5736 42.2065C26.4711 42.1903 26.3297 42.1814 26.2165 42.149C26.1437 42.1279 26.0817 42.1101 26.0329 42.0955C25.9949 42.0841 25.9023 42.076 25.7552 42.0696C25.7014 42.0671 25.6287 42.0534 25.5386 42.0274C25.3641 41.9756 25.2261 41.9658 25.0169 41.9512C24.9582 41.9472 24.9045 41.9383 24.8375 41.9188C24.6722 41.8694 24.5317 41.8556 24.3498 41.8394C24.2721 41.8313 24.2159 41.8216 24.1836 41.8078C24.1134 41.7778 24.0059 41.7033 24.0009 41.6222C23.991 41.4764 24.0605 41.3969 24.2109 41.384C24.3324 41.3743 24.4308 41.3629 24.5044 41.3483C24.6259 41.3256 24.7359 41.3013 24.8359 41.2754C24.8747 41.2657 24.9574 41.2576 25.0864 41.2527C25.1666 41.2495 25.2716 41.2243 25.4014 41.1789C25.5138 41.14 25.6609 41.1498 25.775 41.1238C25.7998 41.1182 25.856 41.1003 25.9453 41.0679C26.009 41.0452 26.0941 41.029 26.1999 41.0209C26.233 41.0193 26.3198 40.9917 26.462 40.9415C26.5389 40.9139 26.6769 40.9107 26.7588 40.8783C26.8133 40.8556 26.8894 40.8224 26.9613 40.807C27.0448 40.7891 27.1167 40.7673 27.1754 40.7438C27.306 40.6919 27.4119 40.6708 27.5358 40.6157C27.583 40.5955 27.6301 40.576 27.678 40.5574C28.1228 40.3848 28.5659 40.1805 28.9718 39.9309C29.0809 39.8637 29.1735 39.8175 29.2636 39.7551C29.4182 39.6489 29.6001 39.5355 29.7621 39.4082C29.9143 39.2883 30.1003 39.1692 30.221 39.0557C30.3739 38.9123 30.5483 38.7421 30.7435 38.5492C30.8972 38.3969 30.9964 38.2867 31.0411 38.2194C31.089 38.144 31.1452 38.0662 31.208 37.9836C31.2858 37.8847 31.3403 37.8053 31.3759 37.7486C31.5305 37.4901 31.6569 37.2494 31.7561 37.0241C31.7735 36.9868 31.7958 36.9325 31.8223 36.8644C31.8727 36.734 31.9157 36.6529 31.9455 36.533C31.9612 36.473 31.981 36.4146 32.005 36.3595C32.0562 36.2445 32.0604 36.1351 32.1042 36.0184C32.1472 35.9025 32.1786 35.7858 32.1959 35.6683C32.2001 35.6432 32.2158 35.5945 32.2431 35.5224C32.3009 35.366 32.2918 35.2088 32.3439 35.0524C32.3555 35.0135 32.3737 34.9576 32.3968 34.883C32.4084 34.8425 32.4183 34.7444 32.4266 34.5888C32.4282 34.5524 32.4514 34.4729 32.4944 34.353C32.5109 34.3076 32.5233 34.2217 32.5308 34.0937C32.5456 33.8603 32.5539 33.7371 32.5572 33.7242C32.5853 33.6285 32.6151 33.5199 32.6267 33.4113C32.6448 33.2574 32.6564 33.1407 32.6655 33.0588C32.668 33.0402 32.6779 33.0271 32.6953 33.0198Z" fill="white"/>
                                <defs>
                                    <linearGradient id="paint0_linear_7568_3" x1="33" y1="8" x2="33" y2="74" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#FDCC6E"/>
                                        <stop offset="1" stop-color="#FF6600"/>
                                    </linearGradient>
                                    <linearGradient id="paint1_linear_7568_3" x1="33" y1="24" x2="33" y2="58" gradientUnits="userSpaceOnUse">
                                        <stop offset="0.1" stop-color="#FD1D1D"/>
                                        <stop offset="0.9" stop-color="#FF6600"/>
                                    </linearGradient>
                                </defs>
                            </svg>

                            <button type="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                <svg width="13" height="13" viewBox="0 0 9 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.73364 1.53701C9.08504 1.18562 9.08504 0.614946 8.73364 0.263548C8.38224 -0.0878494 7.81157 -0.0878494 7.46017 0.263548L4.5 3.22653L1.53701 0.266359C1.18562 -0.0850383 0.614946 -0.0850383 0.263548 0.266359C-0.0878494 0.617757 -0.0878494 1.18843 0.263548 1.53982L3.22653 4.5L0.26636 7.46299C-0.0850382 7.81438 -0.0850382 8.38505 0.26636 8.73645C0.617757 9.08785 1.18843 9.08785 1.53982 8.73645L4.5 5.77346L7.46299 8.73364C7.81438 9.08504 8.38505 9.08504 8.73645 8.73364C9.08785 8.38224 9.08785 7.81157 8.73645 7.46017L5.77347 4.5L8.73364 1.53701Z" fill="#ffffff"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        <span id="delete_confirmation_msg"></span>
                    </div>
                    <div class="modal-footer border-top-dashed">
                        <input type="hidden" id="delete_message_id" />
                        <input type="hidden" id="delete_message_key" />
                        <input type="hidden" id="delete_channel_id" />
                        <button id="confirm_delete_btn" class="btn btn-primary m-0">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->

        <!-- Agree modal -->
        <div class="modal fade unmanaged-event" id="agreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-white" role="document">
                <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
                <div class="modal-header flex-column pb-4" style="border: none; position: relative;">
                    <div class="d-flex justify-content-end w-100">
                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close" style="background: none; border: none;">
                        <svg width="10" height="10" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.4172 3.41719C20.1984 2.63594 20.1984 1.36719 19.4172 0.585938C18.6359 -0.195312 17.3672 -0.195312 16.5859 0.585938L10.0047 7.17344L3.41719 0.592187C2.63594 -0.189063 1.36719 -0.189063 0.585938 0.592187C-0.195312 1.37344 -0.195312 2.64219 0.585938 3.42344L7.17344 10.0047L0.592188 16.5922C-0.189062 17.3734 -0.189062 18.6422 0.592188 19.4234C1.37344 20.2047 2.64219 20.2047 3.42344 19.4234L10.0047 12.8359L16.5922 19.4172C17.3734 20.1984 18.6422 20.1984 19.4234 19.4172C20.2047 18.6359 20.2047 17.3672 19.4234 16.5859L12.8359 10.0047L19.4172 3.41719Z" fill="white"/>
                        </svg>
                    </button>
                    </div>
                    <h5 class="modal-title text-center w-100 heading" id="myModalLabel">Welcome to self moderated event! Here is what you can do today.</h5>
                </div>

                <div class="modal-body">

                    <div class="video-box" style="text-align:center">

                        <img class="w-50 rounded-rounded lazy" src="https://img.youtube.com/vi/L87udpeMKa0/maxresdefault.jpg" data-src="https://img.youtube.com/vi/maxresdefault.jpg" alt="video image">
                        <div class="video-content">
                                <a class="icon-element icon-element-lg hover-y mx-auto" href="https://www.youtube.com/embed/L87udpeMKa0?rel=0" data-fancybox="" title="Play Video">
                                <svg width="24" height="24" version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 58.752 58.752" xml:space="preserve">
                                    <path fill="#0d233e" d="M52.524,23.925L12.507,0.824c-1.907-1.1-4.376-1.097-6.276,0C4.293,1.94,3.088,4.025,3.088,6.264v46.205
                                    c0,2.24,1.204,4.325,3.131,5.435c0.953,0.555,2.042,0.848,3.149,0.848c1.104,0,2.192-0.292,3.141-0.843l40.017-23.103
                                    c1.936-1.119,3.138-3.203,3.138-5.439C55.663,27.134,54.462,25.05,52.524,23.925z M49.524,29.612L9.504,52.716
                                    c-0.082,0.047-0.18,0.052-0.279-0.005c-0.084-0.049-0.137-0.142-0.137-0.242V6.263c0-0.1,0.052-0.192,0.14-0.243
                                    c0.042-0.025,0.09-0.038,0.139-0.038c0.051,0,0.099,0.013,0.142,0.038l40.01,23.098c0.089,0.052,0.145,0.147,0.145,0.249
                                    C49.663,29.47,49.611,29.561,49.524,29.612z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Section 1: Talk with Others -->

                    <div class="agree-card d-flex align-items-center mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 0px 5px;">

                    <div class="agree-card-left d-flex align-items-center py-2">
                        <div class="d-flex align-items-center px-2" style="gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="10" fill="#007BFF"/>
                            <path d="M6 7h8v2H6V7zm0 4h8v2H6v-2z" fill="white"/>
                        </svg>
                        <h4 class="label">Connect with Others</h4>
                        </div>
                    </div>
                    <div class="agree-card-right d-flex align-items-center py-2 pl-3 pr-2">
                        <p class="mb-0">Chat with colleagues, ask smart questions, and share your ideas.</p>
                    </div>
                    </div>

                    <!-- Section 2: Join Video Chats -->
                    <div class="agree-card d-flex align-items-center mb-3" style="border: 1px solid #eee; border-radius: 4px; padding:  0px 5px;">
                    <div class="agree-card-left d-flex align-items-center py-2">
                        <div class="d-flex align-items-center px-2" style="gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="10" fill="#28A745"/>
                            <rect x="5" y="6" width="10" height="8" fill="white"/>
                        </svg>
                        <h4 class="label">Join Video Meetings</h4>
                        </div>
                    </div>
                    <div class="agree-card-right d-flex align-items-center py-2 pl-3 pr-2">
                        <p class="mb-0">Step into small video rooms to talk face-to-face about key topics.</p>
                    </div>
                    </div>

                    <!-- Section 3: Meet People -->
                    <div class="agree-card d-flex align-items-center mb-3" style="border: 1px solid #eee; border-radius: 4px; padding:  0px 5px;">
                    <div class="agree-card-left d-flex align-items-center py-2">
                        <div class="d-flex align-items-center px-2" style="gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="10" fill="#FFC107"/>
                            <path d="M7 6h6v2H7V6zm0 4h6v2H7v-2z" fill="white"/>
                        </svg>
                        <h4 class="label">Meet Professionals</h4>
                        </div>
                    </div>
                    <div class="agree-card-right d-flex align-items-center py-2 pl-3 pr-2">
                        <p class="mb-0">Find new colleagues and connect with people who share your interests.</p>
                    </div>
                    </div>

                    <!-- Section 4: Share & Ask -->
                    <div class="agree-card d-flex align-items-center mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 0px 5px;">
                    <div class="agree-card-left d-flex align-items-center py-2">
                        <div class="d-flex align-items-center px-2" style="gap: 12px;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="10" fill="#17A2B8"/>
                            <path d="M5 8h10v2H5V8zm0 4h7v2H5v-2z" fill="white"/>
                        </svg>
                        <h4 class="label">Share & Ask</h4>
                        </div>
                    </div>
                    <div class="agree-card-right d-flex align-items-center py-2 pl-3 pr-2">
                        <p class="mb-0">Offer your expertise and ask for advice to grow your network.</p>
                    </div>
                    </div>

                    <!-- Code of Conduct -->
                    <p class="coc px-3 px-lg-0 mb-4" style="font-size: 0.9rem;">
                        <strong>Be Respectful:</strong> Treat everyone well, stay focused, and follow our guidelines. <a href="https://tao.ai/events_um.php?back=<?php echo urlencode(TAOH_SITE_URL_FULL); ?>"><strong>more ...</strong></a></p>
                </div>
                </div>
            </div>
        </div>
        <!-- agree modal end -->

        <!-- Live now modal -->
        <div class="modal v-overlay fade" id="live_now_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-white" role="document">
                <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
                    <div class="modal-header flex-column py-0" style="border: none; position: relative;">
                        <div class="d-flex justify-content-end w-100">
                            <button type="button" class="btn" data-dismiss="modal" aria-label="Close" style="background: none; border: none;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.5857 2.39091C14.1323 1.84429 14.1323 0.956583 13.5857 0.409964C13.039 -0.136655 12.1513 -0.136655 11.6047 0.409964L7 5.01905L2.39091 0.414337C1.84429 -0.132282 0.956583 -0.132282 0.409964 0.414337C-0.136655 0.960956 -0.136655 1.84866 0.409964 2.39528L5.01905 7L0.414337 11.6091C-0.132282 12.1557 -0.132282 13.0434 0.414337 13.59C0.960956 14.1367 1.84866 14.1367 2.39528 13.59L7 8.98095L11.6091 13.5857C12.1557 14.1323 13.0434 14.1323 13.59 13.5857C14.1367 13.039 14.1367 12.1513 13.59 11.6047L8.98095 7L13.5857 2.39091Z" fill="#CACACA"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body pt-0 mx-auto">
                        <h4 class="mb-1 fs-18 fw-medium"><?php echo $room_title;?></h4>

                        <i>Peer review your professional online presence while connecting directly with other professionals to expand your LinkedIn network authentically.</i>
                        <br><br><b>💬 Active Channels</b><br>
                        <?php
                            if(!empty($room_info_arr['output']['club']['channel_list'])) {
                                foreach($room_info_arr['output']['club']['channel_list'] as $ch) {
                                    if (strpos($ch, '#') !== false) {
                                        echo ' → #' . ucwords(ltrim($ch, '#')).'<br>';
                                    } else {
                                        echo ' → ' .ucwords($ch).'<br>';
                                    }
                                }                                    
                            }                                
                        ?>

                    </div>
                        <?php if($room_app != "live_now") { ?>
                        <p class="fs-12" style="line-height: 14px;">
                            <b>Code of Conduct:</b> Treat all participants with kindness, respect diverse perspectives, and maintain a professional environment. Stay on-topic, avoid disruptive behavior, and adhere to event guidelines and confidentiality rules. By Joining the event you are entitled to abide by the COC.
                        </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Live now modal end -->



        <!-- What's On Your Mind -->
         <div class="modal fade" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div style="background:none; border:none" class="modal-content">
                    <div class="modal-body p-0 ">
                        <div class="user-panel-main-bar">
                            <div class="user-panel">
                                <div class="delete-account-info card card-item border border-danger">
                                    <div id="deleteAccountBody" class="card-body">
                                        <h3 class="fs-22 fw-bold">What's On Your Mind?</h3>
                                        <button type="button" class="close close-status-modal"  aria-label="Close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                        <div class="fs-15 mt-4 mb-4">
                                            <div class="d-flex position-relative">
                                                <form style="width: 100%;">
                                                    <button type="button" onclick="showEmoji()" id="selected-emoji" class="emoji-place selected-emoji">
                                                        <img class="emoji-place" src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/emojis/default.svg'; ?>" />
                                                    </button>
                                                    <input type="text" required  maxlength="140" placeholder="Say something"  class="current_status" id="current_status" name="current_status"/>
                                                    <input type="hidden" name="choosen_emoji" id="choosen_emoji" value=""/>
                                                    <button type="reset" id="not-empty" class="not-empty" onclick="removeEmoji();"><i class="la la-close"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="emoji_section emoji-place" id="emoji_section" style="display:none;">
                                            <div class="fs-11 text-dark emoji-inner-section d-flex flex-wrap" style="scrollbar-width: thin;">
                                                <?php for ($i = 1; $i <= 100; $i++) { ?>
                                                    <button type="button" onclick="chooseEmoji(<?php echo $i; ?>);" class="emoji-icon" style="position: unset;">
                                                        <img src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/emojis/' . $i . '.svg'; ?>"/>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <span class="fs-14 fw-bold">You can choose from below list also :</span>
                                        <div class="fs-15 mt-1 mb-4 frequent-notes text=-right">
                                            <span onclick="copyToStatus('In a meeting');"> * In a meeting</span>
                                            <span onclick="copyToStatus('Ready to chat');"> * Ready to chat</span>
                                            <span onclick="copyToStatus('Let us talk about jobs');"> * Let us talk about jobs</span>
                                            <span onclick="copyToStatus('Love to learn about you');"> * Love to learn about you</span>
                                            <span onclick="copyToStatus('Be right back');"> * Be right back</span>
                                        </div>
                                        <div class="fixed-footer-btm text-right">
                                            <button onclick="saveStatus();" type="button" class="btn btn-primary fw-medium" data-toggle="modal" data-target="#messageModal" id="message-button">Save</button>
                                            <button type="button" class="btn btn-outline-secondary fw-medium close-status-modal" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- What's On Your Mind -->


    </div>
    <!-- end  layout wrapper -->
<?php
$show_finish_setup_link = 0;
$data_encode = json_encode(taoh_user_all_info());
$data = json_decode($data_encode, true);

//print_r($data); 

// if (!isset($data['profile_stage']) || (isset($data['profile_stage']) && $data['profile_stage'] < 2)) {
//     $show_finish_setup_link = 1;
// }

if (isset($data['profile_stage']) && $data['profile_stage'] < 2) {
    $show_finish_setup_link = 1;
}
?>


    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/simplebar/simplebar.min.js"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/node-waves/waves.min.js"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/swiper/swiper-bundle.min.js"></script>

    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/glightbox/js/glightbox.min.js"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/fg-emoji-picker/fgEmojiPicker.js"></script>

    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/libs/tributejs/tribute.min.js"></script>

    <script type="text/javascript">
        let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
        let _taoh_channel_disscussion = '<?php echo defined('TAOH_CHANNEL_DISSCUSSION') ? TAOH_CHANNEL_DISSCUSSION : 1; ?>';
        let _taoh_channel_direct_message = '<?php echo defined('TAOH_CHANNEL_DIRECT_MESSAGE') ? TAOH_CHANNEL_DIRECT_MESSAGE : 2; ?>';
        let _room_app = '<?php echo $room_app ?>';
        let _can_delete_all_msg = '<?php echo $can_delete_all_msg ?>';
        let _like_enable = '<?php echo defined('TAOH_LIKE_ENABLE') ? TAOH_LIKE_ENABLE : 1; ?>';    
        
        var _speed_networking_enable = '<?php echo $speednetwork_enable;?>';

        let _settings_url = '<?php echo TAOH_SITE_URL_ROOT . '/settings' ?>';

        let _job_search_string = '<?php echo TAOH_SITE_URL_ROOT . '/jobs' ?>';

        var my_liked_messages = [];

        let ntw_tour_version = '<?php echo defined("TAOH_NETWORK_TOUR_VERSION") ? TAOH_NETWORK_TOUR_VERSION : 1; ?>';

        let my_pToken = '<?php echo $ptoken ?? ''; ?>';
        let my_chatName = '<?php echo $user_info_obj->chat_name ?? ''; ?>';
        let fetch_live_channels = '<?php echo $fetch_live_channels ?? ''; ?>';

        let my_link = '<?php echo $user_info_obj->mylink ?? ''; ?>';
        let my_country_name = '<?php echo $user_info_obj->country_name ?? ''; ?>';

        let ntw_room_key = '<?php echo $keytoken ?? ''; ?>';
        let ntw_room_key_hash = '<?php echo hash('crc32', $keytoken); ?>';

        let dojo_data_key = 'dojo_data_'+ntw_room_key;

        let eventtoken = '<?php echo $club_token ?? ''; ?>';
        let eventtitle = <?php echo json_encode($event_title ?? ''); ?>;
        let chatwith = '<?php echo $chatwith ?? ''; ?>';
        let chatname = "<?php echo $_GET['with'] ?? ''; ?>";

        let chatChannelId = '<?php echo $_GET['channel_id'] ?? ''; ?>';
        let ticketInfo = <?php echo json_encode($current_ticket_type ?? []); ?>;
        let orgPtokens = <?php echo json_encode($owner_org_ptoken ?? []); ?>;

        let my_following_ptoken_list = JSON.parse(`<?= json_encode(($my_following_ptoken_list ?? [])); ?>`);

        let frm_asm_enable = <?php echo json_encode($frm_asm_enable); ?>;
        let frm_asm_queue = {};
        let frm_asm_current_index = 0;
        let frm_asm_actual_indexes = <?php echo json_encode($frm_asm_indexes ?? []); ?>;
        let frm_asm_indexes = [...frm_asm_actual_indexes];
        let frm_asm_index_messages = <?php echo json_encode($frm_asm_index_messages ?? []); ?>;

        let ntw_asm_enable = <?php echo json_encode($ntw_asm_enable); ?>;
        let ntw_asm_queue = {};
        let ntw_asm_current_index = 0;
        let ntw_asm_actual_indexes = <?php echo json_encode($ntw_asm_indexes ?? []); ?>;
        let ntw_asm_indexes = [...ntw_asm_actual_indexes];
        let ntw_asm_index_messages = <?php echo json_encode($ntw_asm_index_messages ?? []); ?>;

        let show_finish_setup_link = <?php echo $show_finish_setup_link; ?>;        

        let sidekick_ptoken = '<?php echo $sidekick_ptoken ?? ''; ?>';
        let sidekick_avatar = '<?php echo $sidekick_avatar ?? ''; ?>';

        let layout = '<?php echo $ntw_view; ?>'; //1-participants,2- (1-1) chat window

        let chatWindow = 'channel'; // channel, direct_message, participants
        let ntwChannelListETag = null;

        var liveonly = 0;

        let userLiveIntervalId;
        let userLiveStatusInterval = 60000; // 1 minute
        let chatwith_liveStatus = 0;

        var loaderArea = $('#loaderArea-participant');
        var loaderAreaSp = $('.loaderArea');
        var networkArea = $('#networkArea-participant');
        var speedNetworkArea = $('#speed_networking-participant');
        let maxradius = 50000;
        let minradius = 100;
        let ntwEntriesETag = null;

        var listChannelsArray = [];
        var mentionUserArray = {};

        var totalentries = 0;
        var channelInfoData = {};
        var userStatusArray = {};
        var selectedUser = '';
        var selectedChannel = '';
        var selectedChannelName = '';
        var selectedChat = '';

        var suggestionUsersOnRoleArray = [];
        var suggestionUsersOnLocationArray = [];
        var suggestionUsersOnSkillArray = [];
        var suggestionUsers = [];

        var _taoh_live_user_count = 0;
        var my_profileType = '<?php echo $profile_type ?? ''; ?>';
        var _taoh_last_job_post_date = "";
        var jobUrlData = {};
        var roomSlugData = {};

        var channelLastMsgSentTime = {};
        var DMLastMsgSentTime = "";

        const items = ['role', 'location','skill'];
        var random = items[Math.floor(Math.random() * items.length)];

        const channelList = $('.channelList');
        const usersList = $('.usersList');
        const channelConversationList = $('#channel-conversation-list');
        const chatInputContainer = $('#chat-input-container');
        const channelReplyConversationList = $('#chat-reply-conversation-list');
        const channelLikeConversationList = $('#chat-like-conversation-list');
        const chatReplyInputContainer = $('#chat-reply-input-container');

        const usersConversationList = $('#users-conversation-list');

        var rsvped_ticket = '<?php echo $rsvp_slug; ?>';

        var win_height = $(window).height();
        var organizerOnline = 0;
        var opt_search = 0;

        var watch_party_shown = 0;
        var dojorules = <?php echo json_encode(DOJO_NETWORKING_MESSAGE); ?>;

        var speed_networking_last_timestamp = 0;

        var intervalId1;
        var intervalId2;

        var currentFullPath = window.location.origin + window.location.pathname;
        var root_url = '<?php echo TAOH_SITE_URL_ROOT ?>';
        currentFullPath = currentFullPath.replace(root_url, "");    
                 
    </script>


    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/js/chat.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/js/chat-group.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/js/chat-direct.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/js/chat-common.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/chat/js/chat-dojo.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

    <script type="text/javascript">

        $(window).on('resize', function () {

            //alert(chatWindow);
            var win_height = $(window).height();
            var hh_height = win_height - 163;

            if("participants" === chatWindow){
                $('.chat-leftsidebar').addClass('height-unset');
                // $('.chat-leftsidebar').css('height', 'auto');
            }
            else{
                $('.chat-leftsidebar').removeClass('height-unset');
                // $('.chat-leftsidebar').css('height', hh_height + 'px');
                $('.chat-leftsidebar').css('overflow-y', 'auto');
                $('.chat-leftsidebar').css('overflow-x', 'hidden');
            }
        });
    
    

        $(document).ready(function () {            

            taoh_network_update_online();
                       
            /*
            $('.chat-leftsidebar').css('height', win_height - 163);
            $('.chat-leftsidebar').css('overflow-y', 'auto');
            $('.chat-leftsidebar').css('overflow-x', 'hidden');
            */

            setInterval(() => {

                loadChannelList(1, 1);               
                taoh_load_suggestion_users();
                
            }, 30000);

            setInterval(() => {
                if(_room_app == 'event')
                getOnlineStatus();
            }, 120000);

            setInterval(() => {
                loadChannelList(1);
                taoh_network_update_online();                
            }, 10000);            

            taoh_load_network_entries();
            $('#dm-block').hide();

            setTimeout(() => {
                if (chatChannelId) {
                    const chatElem = $("li[data-channel_id='" + chatChannelId + "']");
                    if (chatElem.length) chatElem.trigger('click');
                }
            }, 3000);

            if(_room_app == "live_now") {
                $('.live_now_btn').parent('li').hide();
                $('.navbar-nav li').each(function() {
                    $(this).removeClass('active');
                });
            } else {
                $('.live_now_btn').parent('li').show();
            }
            
           // alert(layout)

            if (layout == 1) {
                console.log('----chat_window-----------3')
                loadchatWindow('participants');
            } 
            else if(layout == 5){ 
                
                selectedChat = 'watch-party';
                $('.watchPartySection').show();
                $('.watchPartySection').addClass('watchPartyEnabled');
                console.log('----chat_window-----------4')
                loadchatWindow('channel');

                setTimeout(function () {
                   currentElem = $('.watch_party');
                   console.log('-----currentElem--------',currentElem)
                   if(currentElem.length > 0){
                        processChannelClick(currentElem); 
                        watch_party_shown = 1;

                   }                   
                   /*else{

                       
                       console.log('----chat_window-----------33')
                       watch_party_shown = 0;
                       selectedChat = '';
                       loadchatWindow('participants');
                        $('.watchPartySection').hide();
                        $('.watchPartySection').removeClass('watchPartyEnabled');

                        

                   }*/
                }, 1000);
                
                 
              
                
            }
            else if (layout == 3) {
                console.log('----chat_window-----------5')
                loadchatWindow('direct_message');//RK changed from DM
                $('.left-section').hide();
                $('.left-section').removeClass('d-flex');
                $('#dm-block').show();
            } else {
                
            }

            $(document).on('click', '#nextIndex', function () {
                var nextindex = $(this).attr('nextindex');
                taoh_load_suggestion_users(nextindex);
            });

            $(document).on('click', '.browse_participants_btn', function () {
                $('.rejectDiv').addClass('d-none');
                $('.successMatchDiv').addClass('d-none');
                $('.speed_networking_btn').trigger('click');
                if($('.speed_networking_carousel .carousel-item').length > 0 && !$('.speed_networking_carousel .carousel-item.active').length) {
                    $('.speed_networking_carousel .carousel-item:first').addClass('active');
                } else if($('.speed_networking_carousel .carousel-item').length == 0) {
                    $('.zeroday-speed').removeClass('d-none');
                }
            });

            $('#watch_party_view_btn').click(function() {
                const $videoContainer = $('#videoContainer');
                const $toggleBtn = $(this);
                
                if ($videoContainer.hasClass('d-none')) {
                    // Show video
                    $videoContainer.removeClass('d-none');
                    $toggleBtn.text('Hide');
                } else {
                    // Hide video
                    $videoContainer.addClass('d-none');
                    $toggleBtn.text('View');
                }
            });

            $(document).on('click', '.message-item-dot', function() {
                
                var frm_message_id = $(this).data('frm_message_id');
                var channel_id = $(this).data('channel_id');

                $('.pin_msg[data-channel_id="'+channel_id+'"]').removeClass('d-flex').addClass('d-none');
                $('.pin_msg[data-frm_message_id="'+frm_message_id+'"]').removeClass('d-none').addClass('d-flex');

                $(this).addClass('active').siblings('.message-item-dot').removeClass('active');
            });

            $(document).on('click', '#networkArea-participant .chat-username', async function () {
                var chatwith = $(this).data('ptoken');
                const [userLiveStatus, userInfo] = await Promise.all([
                    getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
                    getUserInfo(chatwith, 'public').catch((e) => {console.log(e)}),
                ]);
                console.log("user info comm", userInfo);
                await updateProfileInfo(userInfo, userLiveStatus, 1);
                selectedChannel = '';

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                //loadRightSidebar('profile');
            });


        $(document).on('click', '.speed_networking_update_btn', async function () {
            updateSpeedNetworkingData(my_pToken, true);
        });
            
        $(document).on('click', '.speed_networking_btn', async function () {

            $('.channelList li').each(function() {
                $(this).removeClass('active');
            });

            $('.successMatchDiv').addClass('d-none');
            $('.rejectDiv').addClass('d-none');

            $('.speed_networking_hints').show();

            $('.speed_networking_carousel .carousel-item').each(function() {
                $(this).find('.connect_btn').prop('disabled', false).text('Connect');
                $(this).find('.not_interested_btn').prop('disabled', false).text('Not interested');
            });

            if($('.speed_networking_carousel .carousel-item').length == 1) {
                $('.carousel-control-prev').hide();
                $('.carousel-control-next').hide();
            } else {
                $('.carousel-control-prev').show();
                $('.carousel-control-next').show();
            }
            
            const currentElem = $(this).closest('li');
            currentElem.addClass('active');
            console.log('----chat_window-----------6');
            loadchatWindow("speed_networking");

            let data = {
                'taoh_action': 'taoh_speed_networking_add_user',
                'key': my_pToken,
                'ptoken': my_pToken,
                'keyslug': ntw_room_key,
            };
            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    beforeSend: function() {
                        if($('.speed_networking_carousel').html() == "") {
                            loader(true, loaderAreaSp);
                        }
                    },
                    complete: function(){
                        loader(false, loaderAreaSp);
                    },
                    success: function (res) {
                        loader(false, loaderAreaSp);
                        let resultArr = res;
                        if(res.success) {                                                    
                            const response_data = {
                                status: 200                                
                            };
                            resolve(response_data);
                        } else {
                            const response_data = {
                                status: 201
                            };
                            resolve(response_data);                            
                        }                        
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status);
                    }
                });
            });

            if (response.status === 200) {
                
                if(intervalId1) {
                    clearInterval(intervalId1);
                    $(".countDownTimer").text("30 secs");
                }
                if(intervalId2) {
                    clearInterval(intervalId2);
                    $(".countDownTimer1").text("30");
                }

                $('.countDownDiv').addClass('d-none');
                $('.successMatchDiv').addClass('d-none');                

                $('.connect_btn').prop('disabled', false).text('Connect');
                $('.not_interested_btn').prop('disabled', false).text('Not interested');

                if($('.speed_networking_carousel').html() == "") {
                    $('.speed_networking_div').addClass('d-none');
                    $('.zeroday-speed').removeClass('d-none');
                } else {
                    $('.speed_networking_div').removeClass('d-none');
                    $('.zeroday-speed').addClass('d-none');
                    $('.successMatchDiv').addClass('d-none');
                }
                //updateSpeedNetworkingData(my_pToken, true);                        
            }            
        });

        $(document).on('click', '.connect_btn', async function () {

            const $btn = $(this);
            $btn.prop('disabled', true).text('Loading...');

            var chatwith = $(this).data('chatwith');

            let data = {
                'taoh_action': 'taoh_speed_networking_connect_user',
                'key': my_pToken,
                'ptoken': my_pToken,
                'chatwith': chatwith,
                'keyslug': ntw_room_key,
            };
            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (res) {                        
                        let resultArr = res;

                        chat_messages_key = `sn_${ntw_room_key}`;

                        IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {        
                            let updatedResponse = {};
                            if (intao_data?.values) {
                                updatedResponse = intao_data.values;
                            }
                            if (!Array.isArray(updatedResponse[chatwith])) {
                                updatedResponse[chatwith] = [];
                            }
                            
                            const exists = updatedResponse[chatwith].some(item => item.ptoken === my_pToken);
                            if (!exists) {
                                updatedResponse[chatwith].push({
                                    ptoken: my_pToken,
                                    status: 0
                                });
                            }
                                                        
                            IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key,
                                values: updatedResponse,
                                timestamp: Date.now()
                            });
                        });

                        const response_data = {
                            status: 200,
                            success: true,
                        };
                        resolve(response_data);
                        
                    },
                    error: function (xhr, status, error) {
                        const response_data = {
                            status: 201,
                            success: false,
                            ptoken: resultArr.next_user.ptoken
                        };
                        resolve(response_data);
                    }
                });
            });
        });

        $(document).on('click', '.accept_btn, .reject_btn', async function () {
            var chatWith = $(this).attr('data-chatwith');
            var chatFrom = $(this).attr('data-chatfrom');
            var action = $(this).attr('data-action');            
            updateConnectionRequestStatus(chatWith, chatFrom, action);            
        });

        $(document).on('click', '.not_interested_btn', async function () {

            const $btn = $(this);
            const $current = $(this).closest('.carousel-item');

            $btn.prop('disabled', true).text('Loading...');

            var chatwith = $(this).data('chatwith');

            let data = {
                'taoh_action': 'taoh_speed_networking_block_user',
                'key': my_pToken,
                'ptoken': my_pToken,
                'chatwith': chatwith,
                'keyslug': ntw_room_key,
            };
            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    success: function (res) {
                        $btn.closest('.carousel-item').remove();
                        
                        if($('.speed_networking_carousel .carousel-item').length > 0) {
                            $('.zeroday-speed').addClass('d-none');
                            console.log("speed_networking_carousel ==> ", $('.speed_networking_carousel .carousel-item').length);
                            $('.carousel-item').first().addClass('active');
                            if($('.speed_networking_carousel .carousel-item').length == 1) {
                                $('.carousel-control-prev').hide();
                                $('.carousel-control-next').hide();
                            } else {
                                $('.carousel-control-prev').show();
                                $('.carousel-control-next').show();
                            }
                        } else {
                            $('.speed_networking_div').addClass('d-none');
                            $('.zeroday-speed').removeClass('d-none');
                        }                        
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status);
                    }
                });
            });     
            
              
        });

        $(document).on('click', '.load_dm', async function () {
            var chatwith = $(this).data('chatwith');
            await loadDirectMessage(chatwith, "", "", "1");
        });

        taoh_fetch_connection_request();

        async function taoh_load_suggestion_users(index = 0) {

            if (random == 'role') {
                $('#suggestion_on').html('Roles');
                var suggestionUsersArray = suggestionUsersOnRoleArray;
            } else if (random == 'location') {
                $('#suggestion_on').html('Location');
                var suggestionUsersArray = suggestionUsersOnLocationArray;
            } else if (random == 'skill') {
                $('#suggestion_on').html('Skills');
                var suggestionUsersArray = suggestionUsersOnSkillArray;
            }

            if (suggestionUsersArray.length == 0) {
                var suggestionUsersArray = suggestionUsers;
                $('#suggestion_on').html('Participants');
            }

            if (index >= suggestionUsersArray.length) {
                index = 0;
            }

            if (suggestionUsersArray.length > 0 && index <= suggestionUsersArray.length) {
                $('#suggestion-users').html('');
                let suggestionListFirstElem = true;
                var suggestionList = '';
                $.each(suggestionUsersArray, function (key, l) {


                    if (key == index) {
                        //console.log('------avatarSrc--------', l.avatarSrc);
                        var nextindex = parseInt(index) + 1;
                        const s_companies = formatObject(l.company);
                        const s_roles = formatObject(l.title);
                        //const s_avatarSrc = buildAvatarImage(l.avatar_image, l.avatar);
                        var fallbackSrcs = `${_taoh_ops_prefix}/avatar/PNG/128/${l?.avatar?.trim() || 'default'}.png`;

                        suggestionList += `<div class="card-body d-flex px-2" style="gap: 6px;">
                                        <img class="round-profile-suggestion" src="${l.avatarSrc}" alt="${l.chat_name}">
                                        <div>
                                            <div class="mb-1">
                                                <p class="fs-15 fw-500 mb-0 par-name text-capitalize">${l.chat_name}</p>
                                                <p class="fs-13 fw-400 mb-0 par-role-company">${(s_roles && s_roles.length) ? generateRoleHTML(s_roles, 1) + ', ' : ''}
                                                ${(s_companies && s_companies.length) ? generateCompanyHTML(s_companies, 1) : ''}
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 4px;">
                                                <button class="btn std-sm-bor-btn py-0 openchatacc capitalize-first" data-chatwith="${l.ptoken}" data-chatname="${l.chat_name}"
                                                ${suggestionListFirstElem ? 'data-intro="Click the recommended participant to connect and start a 1 on 1 chat" data-step="6"' : ''}>Chat</button>
                                                <span class="fs-12 text-black fw-500 mx-1">OR</span>
                                                <button nextindex="${nextindex}" id="nextIndex" type="button" class="btn std-sm-bor-btn py-0">Next</button>
                                            </div>
                                        </div>
                                    </div>`;

                        suggestionListFirstElem = false;
                    }
                });
                $('#suggestion-users').html(suggestionList);
                $('#dojo_suggestion_block').show();
            } else {

                $('#dojo_suggestion_block').hide();
                // $('#suggestion-users').html('');
                //  $('#suggestion-users').html('<div class="text-center">No more users to suggest</div>');
            }

        }

            $('.participants_refresh').on('click', function () {
                loader(true, loaderArea);
                $('.chat-like-sidebar').hide();
                $('.speed_networking_hints').hide();
                chatWindow = 'participants';
                chat_activeTabId = chatWindow; // for footer fn
                selectedChannel = '';
                $('#user-chat').removeClass('mobile-transform');
                $("#query").val('');
                opt_search = 0;
                addRemoveActive();
                console.log('----chat_window-----------7')
                
                loadchatWindow(chatWindow);
                taoh_load_network_entries();

                random = items[Math.floor(Math.random() * items.length)];

                taoh_load_suggestion_users();
                taoh_get_activities();
                //loader(false, loaderArea);
            });

            $(document).on('click', '.chat-video', async function () {
                if (!ntw_room_key?.trim()) {
                    return jq_confirm_alert('Error!', 'Something went wrong. Please try again later.', 'red', 'Ok');
                }

                const restoreIcon = () => videoIconElem.removeClass('bx-loader-alt bx-spin').addClass('bx-video');
                const startLoader = () => videoIconElem.removeClass('bx-video').addClass('bx-loader-alt bx-spin');

                const currentElem = $(this);
                const currentElemId = currentElem.attr('id');
                const videoIconElem = currentElem.find('i');
                const type = parseInt(currentElem.data('type'), 10) || 1;

                let isUserChatVideo = currentElemId === 'user-chat-video';

                let data = null;
                let confirmMsg = 'Please confirm that you want to start a video chat?';

                if (currentElemId === 'user-chat-video') {
                    if (!chatwith?.trim()) {
                        return jq_confirm_alert('Error!', 'Please select a user to start a chat with.', 'red', 'Ok');
                    }

                    if (chatname?.trim()) {
                        confirmMsg = `Please confirm that you want to start a video chat with ${chatname}?`;
                    }

                    data = {
                        taoh_action: 'taoh_add_video_chat',
                        my_token: my_pToken,
                        guest_token: chatwith,
                        network_title: '<?php echo $club_info['title'] ?? 'Networking Chat'; ?>'
                    };

                } else if (currentElemId === 'user-chat-video-bottom' || currentElemId === 'user-chat-video-link-bottom') {

                    confirmMsg = `Please confirm that you want to start a video chat ?`;
                    data = {
                        taoh_action: 'taoh_add_video_chat',
                        my_token: my_pToken,
                        guest_token: chatwith,
                        network_title: '<?php echo $club_info['title'] ?? 'Networking Chat'; ?>'
                    };

                } else if (currentElemId === 'channel-chat-video') {
                    let channel_id = $('#channel-chat').attr('data-channel_id');

                    //return jq_confirm_alert('<i style="color:red" class="fa fa-exclamation-triangle" aria-hidden="true"></i> Video Unavailable in Channels', "This video is only available for 1-1 guest chats. To start a conversation, open a 1-1 chat with a guest member and share the video link there. Let's keep it personal and productive!", 'red', 'Ok');

                    if (!channel_id?.trim()) {
                        return jq_confirm_alert('Error!', 'Please select a channel to start a chat with.', 'red', 'Ok');
                    }

                    let channel_name = $('.cw_channel_title').text() || 'Channel';

                    data = {
                        taoh_action: 'taoh_add_video_chat',
                        my_token: ntw_room_key_hash,
                        guest_token: channel_id,
                        network_title: channel_name
                    };
                }


                if (!data) return;

                startLoader();

                const confirmGoogleMeet = () => {
                    taoh_set_warning_message('This will open Google Meet in a new tab. Please copy the generated link and share it in the chat.', false, 'toast-middle', [
                        {
                            text: 'Yes',
                            action: () => {
                                restoreIcon();
                                window.open('https://meet.google.com/new');
                            },
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        },
                        {
                            text: 'cancel',
                            action: () => {
                                restoreIcon();
                            },
                            class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                        }
                    ]);
                    // $.confirm({
                    //     title: 'Confirmation!',
                    //     content: 'This will open Google Meet in a new tab. Please copy the generated link and share it in the chat.',
                    //     type: 'warning',
                    //     buttons: {
                    //         cancel: restoreIcon,
                    //         confirm: {
                    //             text: 'Yes',
                    //             action: () => {
                    //                 restoreIcon();
                    //                 window.open('https://meet.google.com/new');
                    //             }
                    //         }
                    //     }
                    // });
                };


                const confirmExternaLink = () => {
                    $('#share_video_elem_id').val(currentElemId);
                    $('#share_video_modal').modal('show');

                    /*$.confirm({
                        title: 'Share your video link',
                        content: '' +
                            '<form action="" class="formName">' +
                            '<div class="form-group">' +
                            '' +
                            '<input type="text" placeholder="Enter the link here" class="external_link form-control" required />' +
                            '</div>' +
                            '</form>',
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {
                                    var link = this.$content.find('.external_link').val();
                                    if (!link) {
                                        taoh_set_error_message('Please enter the link', false);
                                        return false; // prevent modal from closing
                                    }
                                    var link_url = linkifyWithJQuery(link, 'video');
                                    // alert(link_url)
                                    //<a href="${link}" target="_blank" class="chat-meeting-link">Join video meeting</a>

                                    const videoChatLink = `Want to chat?
                                            <div class="chat-meeting">
                                                <i class="bx bx-video"></i>
                                                <div>
                                                    <div>${`${my_chatName} joined meeting`}</div>
                                                    ${link_url}
                                                </div>
                                            </div>`;
                                    commentInput.val(videoChatLink);


                                    $('#chat-send-btn').trigger('click');
                                    restoreIcon();
                                    window.open(link);

                                }
                            },
                            cancel: function () {
                                // Close the modal
                            }
                        },
                        onContentReady: function () {
                            // bind to events
                            var jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                e.preventDefault();
                                jc.$$formSubmit.trigger('click');
                            }); // trigger submit button click


                        }
                    });*/

                };

                const confirmCustomMeet = () => {
                    taoh_set_warning_message(confirmMsg, false, 'toast-middle', [
                        {
                            text: 'Yes',
                            action: () => {
                                $.post(_taoh_site_ajax_url, data, function (response) {
                                    const videoChatLink1 = `Want to chat?
                                            <div class="chat-meeting">
                                                <i class="bx bx-video"></i>
                                                <div>
                                                    <div>${isUserChatVideo && chatname ? `${chatname}'s meeting` : `${my_chatName} joined meeting`}</div>
                                                    <a href="${response.other_link}" target="_blank" class="chat-meeting-link">Join video meeting</a>
                                                </div>
                                            </div>`;
                                    const videoChatLink = `
                                                    <div class="ctext-wrap mb-0">
                                                        <div class="">
                                                            <h6 class="mb-0 ctext-name fs-13 fw-500">${chatname}</h6>
                                                            <p class="mb-0 ctext-content fs-12 fw-400">
                                                                Join my video room to discuss about 'Video Room Title'
                                                            </p>
                                                            <a href="#" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                                                <svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M13.0184 5.77365C14.3272 4.45218 14.3272 2.3121 13.0184 0.990637C11.8601 -0.178803 10.0347 -0.330831 8.70266 0.630449L8.66559 0.656177C8.33201 0.897082 8.25556 1.36486 8.49417 1.69932C8.73277 2.03378 9.19608 2.1133 9.52734 1.8724L9.56441 1.84667C10.308 1.31106 11.325 1.39526 11.969 2.04781C12.6987 2.78456 12.6987 3.97739 11.969 4.71414L9.36982 7.34304C8.64011 8.07979 7.45867 8.07979 6.72896 7.34304C6.08265 6.69049 5.99926 5.66372 6.52974 4.91528L6.55522 4.87786C6.79383 4.54106 6.71507 4.07328 6.3838 3.83472C6.05254 3.59615 5.58691 3.67333 5.35062 4.00779L5.32514 4.04522C4.37073 5.38773 4.5213 7.23077 5.67957 8.40021C6.98842 9.72168 9.10805 9.72168 10.4169 8.40021L13.0184 5.77365ZM0.981633 5.22635C-0.327211 6.54782 -0.327211 8.68789 0.981633 10.0094C2.1399 11.1788 3.96533 11.3308 5.29734 10.3696L5.33441 10.3438C5.66799 10.1029 5.74444 9.63514 5.50583 9.30068C5.26723 8.96622 4.80392 8.8867 4.47266 9.1276L4.43559 9.15333C3.69198 9.68894 2.67502 9.60474 2.03103 8.95219C1.30132 8.2131 1.30132 7.02027 2.03103 6.28352L4.63018 3.65696C5.35989 2.92021 6.54133 2.92021 7.27104 3.65696C7.91735 4.30951 8.00074 5.33628 7.47026 6.08706L7.44478 6.12448C7.20617 6.46128 7.28493 6.92906 7.6162 7.16762C7.94746 7.40619 8.41309 7.329 8.64937 6.99454L8.67486 6.95712C9.62927 5.61227 9.4787 3.76923 8.32043 2.59979C7.01158 1.27832 4.89195 1.27832 3.58311 2.59979L0.981633 5.22635Z" fill="#2557A7"/>
                                                                </svg>
                                                                Join Video Room !
                                                            </a>
                                                        </div>
                                                    </div>`;
                                    commentInput.val(videoChatLink);
                                    $('#chat-send-btn').trigger('click');
                                    restoreIcon();
                                    if (response.my_link) window.open(response.my_link);
                                }).fail(restoreIcon);
                            },
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        },
                        {
                            text: 'cancel',
                            action: () => {
                                restoreIcon()
                            },
                            class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                        }
                    ]);

                    // $.confirm({
                    //     title: 'Confirmation!',
                    //     content: confirmMsg,
                    //     type: 'warning',
                    //     buttons: {
                    //         cancel: restoreIcon,
                    //         confirm: {
                    //             text: 'Yes',
                    //             action: () => {
                    //                 $.post(_taoh_site_ajax_url, data, function (response) {
                    //                     const videoChatLink1 = `Want to chat?
                    //                         <div class="chat-meeting">
                    //                             <i class="bx bx-video"></i>
                    //                             <div>
                    //                                 <div>${isUserChatVideo && chatname ? `${chatname}'s meeting` : `${my_chatName} joined meeting`}</div>
                    //                                 <a href="${response.other_link}" target="_blank" class="chat-meeting-link">Join video meeting</a>
                    //                             </div>
                    //                         </div>`;
                    //                     const videoChatLink = `
                    //                                 <div class="ctext-wrap mb-0">
                    //                                     <div class="">
                    //                                         <h6 class="mb-0 ctext-name fs-13 fw-500">${chatname}</h6>
                    //                                         <p class="mb-0 ctext-content fs-12 fw-400">
                    //                                             Join my video room to discuss about 'Video Room Title'
                    //                                         </p>
                    //                                         <a href="#" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                    //                                             <svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    //                                                 <path d="M13.0184 5.77365C14.3272 4.45218 14.3272 2.3121 13.0184 0.990637C11.8601 -0.178803 10.0347 -0.330831 8.70266 0.630449L8.66559 0.656177C8.33201 0.897082 8.25556 1.36486 8.49417 1.69932C8.73277 2.03378 9.19608 2.1133 9.52734 1.8724L9.56441 1.84667C10.308 1.31106 11.325 1.39526 11.969 2.04781C12.6987 2.78456 12.6987 3.97739 11.969 4.71414L9.36982 7.34304C8.64011 8.07979 7.45867 8.07979 6.72896 7.34304C6.08265 6.69049 5.99926 5.66372 6.52974 4.91528L6.55522 4.87786C6.79383 4.54106 6.71507 4.07328 6.3838 3.83472C6.05254 3.59615 5.58691 3.67333 5.35062 4.00779L5.32514 4.04522C4.37073 5.38773 4.5213 7.23077 5.67957 8.40021C6.98842 9.72168 9.10805 9.72168 10.4169 8.40021L13.0184 5.77365ZM0.981633 5.22635C-0.327211 6.54782 -0.327211 8.68789 0.981633 10.0094C2.1399 11.1788 3.96533 11.3308 5.29734 10.3696L5.33441 10.3438C5.66799 10.1029 5.74444 9.63514 5.50583 9.30068C5.26723 8.96622 4.80392 8.8867 4.47266 9.1276L4.43559 9.15333C3.69198 9.68894 2.67502 9.60474 2.03103 8.95219C1.30132 8.2131 1.30132 7.02027 2.03103 6.28352L4.63018 3.65696C5.35989 2.92021 6.54133 2.92021 7.27104 3.65696C7.91735 4.30951 8.00074 5.33628 7.47026 6.08706L7.44478 6.12448C7.20617 6.46128 7.28493 6.92906 7.6162 7.16762C7.94746 7.40619 8.41309 7.329 8.64937 6.99454L8.67486 6.95712C9.62927 5.61227 9.4787 3.76923 8.32043 2.59979C7.01158 1.27832 4.89195 1.27832 3.58311 2.59979L0.981633 5.22635Z" fill="#2557A7"/>
                    //                                             </svg>
                    //                                             Join Video Room !
                    //                                         </a>
                    //                                     </div>
                    //                                 </div>`;
                    //                     commentInput.val(videoChatLink);
                    //                     $('#chat-send-btn').trigger('click');
                    //                     restoreIcon();
                    //                     if (response.my_link) window.open(response.my_link);
                    //                 }).fail(restoreIcon);
                    //             }
                    //         }
                    //     }
                    // });
                };
                (type == 3) ? confirmExternaLink() : (type === 2) ? confirmGoogleMeet() : confirmCustomMeet();
            });

            $('#share_video_form').on('submit', function (e) {
                e.preventDefault();

                const currentElemId = $('#share_video_elem_id').val();
                const currentElem = $(`#${currentElemId}`);

                const videoIconElem = currentElem.find('i');

                var link = $('.external_link').val();
                if (!link) {
                    taoh_set_error_message('Please enter the link', false);
                    return false;
                }
                var link_url = linkifyWithJQuery(link, 'video');

                const videoChatLink = `Want to chat?
                                            <div class="chat-meeting">
                                                <i class="bx bx-video"></i>
                                                <div>
                                                    <div>${`${my_chatName} joined meeting`}</div>
                                                    ${link_url}
                                                </div>
                                            </div>`;
                commentInput.val(videoChatLink);


                $('#chat-send-btn').trigger('click');
                $('#share_video_modal').modal('hide');
                videoIconElem.removeClass('bx-loader-alt bx-spin').addClass('bx-video');
                window.open(link);
            });

            $(document).on('click', '#jitsi_for_channel', async function () {
                //alert();
                var channelname = $('#channelname').val();
                var data = {
                    taoh_action: 'taoh_add_video_chat',
                    my_token: my_pToken,
                    guest_token: channelname,
                    random: 1,
                    network_title: '<?php echo $club_info['title'] ?? 'Networking Chat'; ?>'
                };

                $.post(_taoh_site_ajax_url, data, function (response) {
                    if (response.my_link) {
                        $('#channel_video_url').val(response.my_link);
                        $('#channel_video_url_block').show();
                    }
                }).fail(function () {
                    loader(false, loaderArea);
                    console.log("Jitsi url generation failed");
                });
            });
            //$('.event_channels_video_channel').on('change', function () {
            $(document).on('change', '.event_channels_video_channel', async function () {

                let type = $(this).val();
                if (type == 1) {
                    $('#channel_video_url_block').show();
                } else {
                    $('#channel_video_url').val('');
                    $('#channel_video_url_block').hide();
                }
            });


            $(document).on('click', '.room_type', function () {
                var type = $(this).val();
                showHideExternal(type);
            });

            function showHideExternal(type) {
                $(".room_type").removeAttr('checked');
                if (type == 1) {
                    $('#external_room').attr('checked', true);
                    $('#show_external_link_box').show();
                } else {
                    $('#tao_rooms').attr('checked', true);
                    $('#show_external_link_box').hide();
                }
            }

            //loadChannelList();
            //taoh_load_network_entries();

            Waves.init();
        });        


        async function speedNetworkingAddUser(ptokens) {

            var speed_networking_list_key = `sn_list_${ntw_room_key}`;
            var intao_data = await IntaoDB.getItem(objStores.ntw_store.name, speed_networking_list_key);
            let updatedResponse = [];
            if (Array.isArray(intao_data?.values)) {
                updatedResponse = intao_data.values;
            }

            // let needToRemovePtoken = updatedResponse.filter(token => !ptokens.includes(token));
            // needToRemovePtoken.forEach((r_ptoken) => {
            //     $('#carousel_item_'+r_ptoken).remove();
            // });
            // updatedResponse = updatedResponse.filter(token => ptokens.includes(token));
            $('.successMatchDiv').addClass('d-none');
                      
            for (const ptoken of ptokens) {
                
                if (!updatedResponse.includes(ptoken)) {

                    updatedResponse.push(ptoken);

                    if(ptoken != my_pToken) {

                        if(!$('.rejectDiv:visible').length && !$('.successMatchDiv:visible').length) {
                            $('.speed_networking_div').removeClass('d-none');
                        }                        
                        $('#speed_networking-participant').html("");

                        var userInfo = await getUserInfo(ptoken, 'public');
                        if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                            var avatar_image = userInfo.avatar_image;
                        } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                            var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                        } else {
                            var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                        }

                        var titleValue = Object.values(userInfo.title)[0]?.value;
                        var userSkills = Object.values(userInfo?.skill ?? {}).map(skill => skill.value);
                        var visibleSkills = userSkills?.slice(0, 3);
                        var extraSkillCount = userSkills?.length - 3;

                        var skillsHtml = "";
                        visibleSkills.forEach(skill => {
                            skillsHtml += '<a href="#" class="skill">'+skill+'</a>';
                        });
                        if (extraSkillCount > 0) {
                            skillsHtml += '<a href="#" class="skill">+' + extraSkillCount + '</a>';
                        }

                        let tagsHtml = '';
                        if (Array.isArray(userInfo.tags)) {
                            var maxVisible = 3;
                            var extraCount = userInfo.tags.length - maxVisible;
                            userInfo.tags.slice(0, maxVisible).forEach((tag, index) => {
                                tagsHtml += `<a href="#" class="interest c-${index + 1}">${tag}</a>\n`;
                            });
                            if (extraCount > 0) {
                                tagsHtml += `<a href="#" class="interest c-4">+${extraCount}</a>\n`;
                            }
                        }                        

                        if(!$(`.speed_networking_carousel #carousel_item_${ptoken}`).length) {
                            $('.speed_networking_carousel').append(`<div id="carousel_item_${ptoken}" class="carousel-item">
                            <div class="d-flex" style="gap: 13px;">
                                <div>
                                    <img class="speed-v1-profile" src="${avatar_image}" alt="">
                                </div>
                                <div class="d-flex flex-column justify-content-between" style="min-height: 260px;">
                                    <div>
                                        <h5 class="speed-v1-title mb-2" style="text-transform: capitalize;">${userInfo.chat_name}</h5>
                                        <p class="speed-v1-badge mb-2" style="text-transform: capitalize;">
                                        ${userInfo?.type ? userInfo.type : 'Professional'}
                                        </p>
                                        <div class="d-flex align-items-center mb-2" style="gap: 12px;">
                                            <div class="speed-v1-info">
                                                <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V14.5C0 15.3281 0.671875 16 1.5 16H4.5V13.5C4.5 12.6719 5.17188 12 6 12C6.82812 12 7.5 12.6719 7.5 13.5V16H10.5C11.3281 16 12 15.3281 12 14.5V1.5C12 0.671875 11.3281 0 10.5 0H1.5ZM2 7.5C2 7.225 2.225 7 2.5 7H3.5C3.775 7 4 7.225 4 7.5V8.5C4 8.775 3.775 9 3.5 9H2.5C2.225 9 2 8.775 2 8.5V7.5ZM5.5 7H6.5C6.775 7 7 7.225 7 7.5V8.5C7 8.775 6.775 9 6.5 9H5.5C5.225 9 5 8.775 5 8.5V7.5C5 7.225 5.225 7 5.5 7ZM8 7.5C8 7.225 8.225 7 8.5 7H9.5C9.775 7 10 7.225 10 7.5V8.5C10 8.775 9.775 9 9.5 9H8.5C8.225 9 8 8.775 8 8.5V7.5ZM2.5 3H3.5C3.775 3 4 3.225 4 3.5V4.5C4 4.775 3.775 5 3.5 5H2.5C2.225 5 2 4.775 2 4.5V3.5C2 3.225 2.225 3 2.5 3ZM5 3.5C5 3.225 5.225 3 5.5 3H6.5C6.775 3 7 3.225 7 3.5V4.5C7 4.775 6.775 5 6.5 5H5.5C5.225 5 5 4.775 5 4.5V3.5ZM8.5 3H9.5C9.775 3 10 3.225 10 3.5V4.5C10 4.775 9.775 5 9.5 5H8.5C8.225 5 8 4.775 8 4.5V3.5C8 3.225 8.225 3 8.5 3Z" fill="#555555"/>
                                                </svg>
                                                <span>Data Infotech Ltd.,</span>
                                            </div>
                                            <div class="speed-v1-info">
                                                <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.74062 15.6405C8.34375 13.629 12 8.7539 12 6.01557C12 2.69447 9.3125 0 6 0C2.6875 0 0 2.69447 0 6.01557C0 8.7539 3.65625 13.629 5.25938 15.6405C5.64375 16.1198 6.35625 16.1198 6.74062 15.6405ZM6 4.01038C6.53043 4.01038 7.03914 4.22164 7.41421 4.59768C7.78929 4.97373 8 5.48376 8 6.01557C8 6.54738 7.78929 7.0574 7.41421 7.43345C7.03914 7.8095 6.53043 8.02076 6 8.02076C5.46957 8.02076 4.96086 7.8095 4.58579 7.43345C4.21071 7.0574 4 6.54738 4 6.01557C4 5.48376 4.21071 4.97373 4.58579 4.59768C4.96086 4.22164 5.46957 4.01038 6 4.01038Z" fill="#555555"/>
                                                </svg>
                                                <span>${userInfo.full_location}</span>
                                            </div>
                                            <div class="speed-v1-info">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="#555555"/>
                                                <path d="M4 20C4 16.6863 7.13401 14 11 14H13C16.866 14 20 16.6863 20 20V21C20 21.5523 19.5523 22 19 22H5C4.44772 22 4 21.5523 4 21V20Z" fill="#555555"/>
                                                </svg>
                                                <span>${titleValue}</span>
                                            </div>                                        
                                        </div>
                                        <div class="mb-2 ${(skillsHtml == "") ? 'd-none' : ''}">
                                            <h6 class="spd-v1-sub-title">Skills</h6>
                                            <div class="spd-v1-skill-con">${skillsHtml}</div>
                                        </div>
                                        <div class="mb-3 ${(tagsHtml == "") ? 'd-none' : ''}">
                                            <h6 class="spd-v1-sub-title">Shared Interest</h6>
                                            <div class="spd-v1-interest-con">
                                                ${tagsHtml}
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <button type="button" data-chatwith="${ptoken}" class="btn std-btn connect_btn">Connect</button>
                                        <button type="button" data-chatwith="${ptoken}" class="btn bor-btn not_interested_btn">Not interested</button>
                                    </div>
                                </div>
                            </div>
                            </div>`);
                        }

                        if($('.speed_networking_carousel .carousel-item').length > 0 && !$('.speed_networking_carousel .carousel-item.active').length) {
                            $('.speed_networking_carousel .carousel-item:first').addClass('active');
                        } else if($('.speed_networking_carousel .carousel-item').length == 0) {
                            $('.zeroday-speed').removeClass('d-none');
                        }

                        $('.successMatchDiv').addClass('d-none');

                        if($('.speed_networking_carousel .carousel-item').length > 0) {
                            $('.speed_networking_div').removeClass('d-none');
                            $('.successMatchDiv').addClass('d-none');
                        }

                    }
                }
            }

            if($('.speed_networking_carousel .carousel-item').length == 1) {
                $('.carousel-control-prev').hide();
                $('.carousel-control-next').hide();
            } else {
                $('.carousel-control-prev').show();
                $('.carousel-control-next').show();
            }

            await IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: speed_networking_list_key,
                values: updatedResponse,
                timestamp: Date.now()
            });
        }


        function taoh_load_network_entries(tab = '', call_from = '', serverFetch = false) {
            let radius = $('#radius').val();
            let q = $("#query").val();
            //alert(opt_search)
            if(q.trim() !='' && opt_search != 1){
                console.log('-------return here----------')
                return;
            }

            let data = {
                'ops': 'status',
                'status': 'getlist',
                'code': _taoh_ops_code,
                'key': my_pToken,
                'keyslug': ntw_room_key,
                'search': q.trim() ? q.toLowerCase() : '',
                'radius': radius,
                'unit': "<?php echo $unit; ?>",
                'live': liveonly || 0,
                'offset': 0,
                'limit': 1000,
                'geo_enable': <?= (int) ($club_info['geo_enable'] ?? 0) ?>,
                //'cfcc10': 1 //cfcache newly added
            };

            if (data.geo_enable == 1) {
                data.latitude = "<?php echo $lat; ?>";
                data.longitude = "<?php echo $long; ?>";
            }

            let ntw_entries_key = 'ntw_entries_' + ntw_room_key;

            IntaoDB.getItem(objStores.ntw_store.name, ntw_entries_key).then((intao_data) => {
                // Check if data is expired (expires after 10sec (10 * 1000))
                if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 10000) && !serverFetch) {
                    process_network_entries(data, intao_data.values);
                } else {
                    if (!navigator.onLine) return;

                    $.ajax({
                        url: _taoh_cache_chat_url,
                        type: 'GET',
                        dataType: 'json',
                        headers: {'If-None-Match': ntwEntriesETag},
                        data: data,
                        success: function (response, textStatus, jqXHR) {
                            let updateData = {'success': true, 'items': {}, 'totalcount': 0, 'returncount': 0};
                            if (jqXHR.status === 304) {
                                if (ntw_entries_cleared) {
                                    ntw_entries_cleared = 0;
                                    if (intao_data?.values) {
                                        process_network_entries(data, intao_data.values);
                                    } else {
                                        process_network_entries(data, updateData);
                                    }
                                }
                                return;
                            }

                            ntwEntriesETag = jqXHR.getResponseHeader('taoh-etag') ?? null;

                            if (response.success && response.output) {
                                updateData.success = true;
                                updateData.items = response['output']['items'];
                                updateData.totalcount = response['output']['totalcount'];
                                updateData.returncount = response['output']['returncount'];
                                var search_term_opt = response['output']['search_term'];

                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: ntw_entries_key,
                                    values: updateData,
                                    timestamp: Date.now()
                                });

                                if(opt_search == 1 && q == search_term_opt )
                                    process_network_entries(data, updateData);
                                else if(opt_search == 0 && (search_term_opt == '' || search_term_opt == null || search_term_opt == undefined) )
                                    process_network_entries(data, updateData);
                            }
                        },
                        error: function (xhr, status, error) {
                            loader(false, loaderArea);
                            console.log('Error Fetching Network Entries : ' + error);
                        }
                    });
                }
            });
        }

        async function process_network_entries(data, response) {
            if (response.success && Object.keys(data).length > 0) {
                let networkList = response['items'] ?? {};

                await render_network_member_template(networkList, networkArea);

                loader(false, loaderArea);
            } else {
                //alert('--------', opt_search);
                
                if(opt_search == 0 )
                    $('#speed_networking_channel_block').hide();
                                  
                show_empty_network_entries_screen(networkArea);

                loader(false, loaderArea);
            }
            $('#live_switch').show();
        }

        function render_network_member_template(data, slot) {
            return new Promise(async (resolve, reject) => {
                const ptoken = my_pToken ?? '';
                let htmlcontent = '';
                let totalentries = 0;
                _taoh_live_user_count = 0;

                const updateStatusFields = (fields) => {
                    const [status, emoji] = fields;
                    $('#my_status').val(status);
                    $('#current_status').val(status);

                    if (emoji) {
                        let emojisrc = _taoh_site_url_root + '/assets/images/emojis/' + emoji + '.svg';
                        $('#loadEmojiImg').attr('src', emojisrc);
                        $('#selected-emoji img').attr('src', emojisrc);
                        $('#choosen_emoji').val(emoji);
                    } else {
                        let emojisrc = _taoh_site_url_root + '/assets/images/emojis/default.svg';
                        $('#loadEmojiImg').attr('src', emojisrc);
                        $('#selected-emoji img').attr('src', emojisrc);
                        $('#choosen_emoji').val('');
                    }
                };

                if (data !== undefined) {
                    let liveitems = [];
                    let nonliveitems = [];
                    $.each(data, function (key, item) {
                        if (item.live == 1) {
                            liveitems.push(item);
                        } else {
                            nonliveitems.push(item);
                        }
                    });

                    let finalitems = liveitems.concat(nonliveitems);


                    suggestionUsersOnRoleArray = [];
                    suggestionUsersOnLocationArray = [];
                    suggestionUsersOnSkillArray = [];
                    suggestionUsers = [];


                    for (const [kk, ll] of finalitems.entries()) {
                        if (ll.ptoken) {
                            const userInfo = await getUserInfo(ll.ptoken, 'public');


                            if(ll.live == 1) {
                                _taoh_live_user_count++;
                            }

                            if (ll['cell'] !== null && ll['cell'] !== 'null') {
                                const l = JSON.parse(ll['cell']);

                                var ticket_details = ll.ticketInfo ?? '';
                                //console.log('----ticket_details------',ll.ticketInfo)
                                if (ll.status !== undefined) {
                                    Object.assign(userStatusArray, {[l.ptoken]: ll.status});
                                } else {
                                    Object.assign(userStatusArray, {[l.ptoken]: ''});
                                }

                                if (l.ptoken === ptoken && ll.status !== undefined) {
                                    const fields = ll.status.split('###');
                                    if (fields.length === 2) {
                                        updateStatusFields(fields);
                                    }
                                }


                                var user_token = String(l.ptoken);

                                if (l.ptoken !== ptoken && l.chat_name) {


                                    totalentries++;
                                    const ntw_entries_cls = `ntw_${ntw_room_key}_${ll.ptoken}`;

                                    var companies = roles = "";

                                    if(l.company != null && l.company != "" && l.company !== undefined)
                                        companies = formatObject(l.company);

                                    if(l.title != null && l.title != "" && l.title !== undefined)
                                        roles = formatObject(l.title);

                                    const skillContent = buildSkillContent(l.skill, l.ptoken);
                                    const userMoodStatus = buildUserMoodStatus(ll.status);
                                    const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${l?.avatar?.trim() || 'default'}.png`;
                                    const avatarSrc = buildAvatarImageOptimistic(l.avatar_image, fallbackSrc, (updatedSrc) => {
                                        const avatarImgElem = slot.find(`.par-list-pro[data-idx="${kk}"]`);
                                        if (avatarImgElem.length && avatarImgElem.attr('src') !== updatedSrc) {
                                            avatarImgElem.attr('src', updatedSrc);
                                        }
                                    });
                                    l.avatarSrc = avatarSrc;

                                    var myRole = '<?php echo $role;?>';
                                    var myLocation = <?php echo json_encode($location); ?>;
                                    var mySkill = '<?php echo $skill ?? '';?>';

                                    const myskillWords = mySkill.toLowerCase().split(/[\s,]+/);
                                    const myRoleWords = myRole.toLowerCase().split(/\s+/);
                                    const myLocationWords = myLocation.toLowerCase().split(/[\s,]+/);


                                    if (l.skill != undefined && l.skill != null && l.skill != '') {
                                        const skillValue = formatObjectAndReunOnlyValue(l.skill);
                                        const skillString = skillValue.join(",");

                                        const skillWords = skillString.toLowerCase().split(/[\s,]+/);

                                        var skillmatch = skillWords.some(word => myskillWords.includes(word));

                                        if (skillmatch) {
                                            suggestionUsersOnSkillArray.push(l);
                                        }

                                    }
                                    if(l.title !='' && l.title != null && l.title != undefined){
                                        const roleValue = formatObjectAndReunOnlyValue(l.title);
                                        const roleString = roleValue.join(",");

                                        const userRoleWords = roleString.toLowerCase().split(/\s+/);
                                        const rolematch = userRoleWords.some(word => myRoleWords.includes(word));
                                        if (rolematch) {
                                            suggestionUsersOnRoleArray.push(l);
                                        }
                                    }



                                    const locationWords = l.full_location.toLowerCase().split(/\s+/);
                                    const locationmatch = locationWords.some(word => myLocationWords.includes(word));
                                    if (locationmatch) {
                                        suggestionUsersOnLocationArray.push(l);
                                    }


                                    suggestionUsers.push(l);

                                    var ticket_type_name = '';
                                    if(ticket_details !='' && ticket_details != null && ticket_details != undefined) {
                                        ticket_type_name = ticket_details[0].title;
                                        //  console.log('-------ticket_details_data--------',ticket_details_data)
                                    }

                                    let isFollowing = false;
                                    if (Array.isArray(my_following_ptoken_list) && my_following_ptoken_list.includes(userInfo.ptoken)) {
                                        isFollowing = true;
                                    }

                                    let chatButton_blank_url = new URL(window.location.href);
                                    chatButton_blank_url.searchParams.set('chatwith', l.ptoken);

                                    const chatButton = `<button type="button" id="${l.ptoken}"
                                class="btn btn-sm openchatacc mr-2  capitalize-first" data-chatwith="${l.ptoken}"
                                 data-chatname="${l.chat_name}" data-live="${ll.live}" style="white-space: nowrap;font-size: small;">
                                Chat <i class="la la-angle-double-right"></i></button>`;

                                    const chatButton_blank = `<a href="${chatButton_blank_url}" target="_blank" class="btn btn-sm" title="Open chat in new tab" style="white-space: nowrap;border: 1px solid #c3c3c3;">
                            <i class="fa fa-external-link" aria-hidden="true"></i></a>`;

                                    htmlcontent += `<div class="participant-n-list position-relative ${ntw_entries_cls} entry_${totalentries}  px-3 py-2 mb-3 d-flex flex-wrap align-items-center" style="gap: 12px;">
                                    <a data-ptoken="${l.ptoken}" class="d-flex flex-column align-items-center chat-username" style="gap: 3px;"
                                    href="javascript:void(0);">
                                <img class="lazy par-list-pro" data-idx="${kk}" src="${avatarSrc}" alt="${l.chat_name}">

                                
                                <p class="text-capitalize p-type-badge">${l.type ? l.type : 'Professional'}</p>
                                

                            </a>

                            <div style="flex: 1; min-width: 230px">
                                <div class="d-flex flex-wrap justify-content-between flex-column flex-lg-row align-items-lg-center my-1" style="gap: 12px;">
                                    <div>

                                        <div class="d-flex align-items-center mb-1" style="gap: 12px; cursor: pointer;">
                                            <a data-ptoken="${l.ptoken}" class="par-name text-capitalize chat-username" >${l.chat_name}</a>

                                            <?php /*
                                            <span class="text-capitalize" style="font-size:10px;background-color: #3e3e3e;color: white;padding: 4px 8px;text-align: center;border-radius: 5px;">
                                               ${l.type === 'seeker' || l.type === 'Seeker' ? 'professional' : l.type}
                                               </span>

                                            <a style="line-height: 1.149;" target="_blank"  href="${_taoh_site_url_root + '/profile/' + l.ptoken}">
                                                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.23054 15C9.1482 15 10.9873 14.2098 12.3433 12.8033C13.6993 11.3968 14.4611 9.48912 14.4611 7.5C14.4611 5.51088 13.6993 3.60322 12.3433 2.1967C10.9873 0.790176 9.1482 0 7.23054 0C5.31288 0 3.47377 0.790176 2.11778 2.1967C0.761787 3.60322 0 5.51088 0 7.5C0 9.48912 0.761787 11.3968 2.11778 12.8033C3.47377 14.2098 5.31288 15 7.23054 15ZM6.10077 9.84375H6.77863V7.96875H6.10077C5.72512 7.96875 5.4229 7.65527 5.4229 7.26562C5.4229 6.87598 5.72512 6.5625 6.10077 6.5625H7.45649C7.83214 6.5625 8.13436 6.87598 8.13436 7.26562V9.84375H8.36031C8.73596 9.84375 9.03817 10.1572 9.03817 10.5469C9.03817 10.9365 8.73596 11.25 8.36031 11.25H6.10077C5.72512 11.25 5.4229 10.9365 5.4229 10.5469C5.4229 10.1572 5.72512 9.84375 6.10077 9.84375ZM7.23054 3.75C7.47025 3.75 7.70014 3.84877 7.86963 4.02459C8.03913 4.2004 8.13436 4.43886 8.13436 4.6875C8.13436 4.93614 8.03913 5.1746 7.86963 5.35041C7.70014 5.52623 7.47025 5.625 7.23054 5.625C6.99083 5.625 6.76094 5.52623 6.59144 5.35041C6.42195 5.1746 6.32672 4.93614 6.32672 4.6875C6.32672 4.43886 6.42195 4.2004 6.59144 4.02459C6.76094 3.84877 6.99083 3.75 7.23054 3.75Z" fill="#686767"/>
                                                </svg>
                                            </a>*/?>
                                        </div>
                                        <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                                            ${ticket_type_name !='' ?`<p class="par-ticket-type mb-1 d-flex align-items-center">
                                                <svg class="mr-1" width="19" height="11" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.11111 0C0.946701 0 0 0.946701 0 2.11111V4.22222C0 4.5125 0.244097 4.7401 0.517882 4.83576C1.13802 5.05017 1.58333 5.64062 1.58333 6.33333C1.58333 7.02604 1.13802 7.61649 0.517882 7.8309C0.244097 7.92656 0 8.15417 0 8.44444V10.5556C0 11.72 0.946701 12.6667 2.11111 12.6667H16.8889C18.0533 12.6667 19 11.72 19 10.5556V8.44444C19 8.15417 18.7559 7.92656 18.4821 7.8309C17.862 7.61649 17.4167 7.02604 17.4167 6.33333C17.4167 5.64062 17.862 5.05017 18.4821 4.83576C18.7559 4.7401 19 4.5125 19 4.22222V2.11111C19 0.946701 18.0533 0 16.8889 0H2.11111ZM4.22222 3.69444V8.97222C4.22222 9.2625 4.45972 9.5 4.75 9.5H14.25C14.5403 9.5 14.7778 9.2625 14.7778 8.97222V3.69444C14.7778 3.40417 14.5403 3.16667 14.25 3.16667H4.75C4.45972 3.16667 4.22222 3.40417 4.22222 3.69444ZM3.16667 3.16667C3.16667 2.58281 3.63837 2.11111 4.22222 2.11111H14.7778C15.3616 2.11111 15.8333 2.58281 15.8333 3.16667V9.5C15.8333 10.0839 15.3616 10.5556 14.7778 10.5556H4.22222C3.63837 10.5556 3.16667 10.0839 3.16667 9.5V3.16667Z" fill="#636161"/>
                                                </svg>

                                                <span>${ticket_type_name}</span>
                                            </p>` : ''}

                                            <p class="par-followers mb-1 d-flex align-items-center">
                                                <svg class="mr-1" width="15" height="11" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#555555"/>
                                                </svg>

                                                <span>
                                                    <span class="mr-2 followers-count-view" data-ptoken="${l.ptoken}" data-fscount="${safeParseInt(userInfo.tao_followers_count, 0)}">${safeParseInt(userInfo.tao_followers_count, 0)} Followers</span>
                                                    <span class="mr-2 following-count-view" data-ptoken="${l.ptoken}" data-fgcount="${safeParseInt(userInfo.tao_following_count, 0)}">${safeParseInt(userInfo.tao_following_count, 0)} Following</span>
                                                </span>
                                            </p>
                                        </div>
                                        <p class="par-loc mb-1 d-flex align-items-center" >${l.full_location ? '<svg class="mr-1" width="11" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path></svg>' + l.full_location : ''}</p>
                                        <p class="par-company mb-1 d-flex align-items-center" >${(roles && roles.length) ? '<svg class="mr-1" width="11" height="11" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path></svg>' + generateRoleHTML(roles, 1) + ',  ' : ' '}
                                        ${(companies && companies.length) ? '<svg class="mr-1" width="11" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path></svg>' + generateCompanyHTML(companies, 1) : ''}</p>`;


                                    if ($.trim(userMoodStatus) != '') {
                                        htmlcontent += `    <p class="live_status p-list mb-1" title='${userMoodStatus}'>${userMoodStatus}</p>`;
                                    }

                                    htmlcontent += `    <!-- dropdown show and hide -->
                                                    <div class="skill-con">
                                                        ${skillContent}
                                                    </div>
                                                </div>
                                                <div class="d-flex bor-btn-con">

                                                        <div ${kk === 0 ? 'data-intro="You can click chat to start a conversation and also do a video call if interested." data-step="4"' : ''}>${chatButton}</div>
                                                       <?php /* ${chatButton_blank} */ ?>

                                                    <button type="button" class="bor-btn profile_follow_btn" data-ptoken="${l.ptoken}" data-follow_status="${isFollowing ? 1 : 0}"  data-page="directory" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                                        <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                }
                            }
                        }
                    }
                }

                slot.empty();
                slot.append(htmlcontent);


                if (!totalentries) {
                    show_empty_network_entries_screen(slot);
                    //alert(opt_search)
                    
                    if(opt_search == 0 ){
                       $('#speed_networking_channel_block').hide();
                    }
                }
                else{
                    $('#speed_networking_channel_block').show();
                }

                resolve();
            });
        }

        function show_empty_network_entries_screen(element) {
            element.empty();
            element.append(`<div class="card card-item">
                <div class="card-body" style="text-align: justify;font-size: 20px;">
                    <div class="col-lg-12" style="text-align: center;">
                        <img class="no-network-place" src="${_taoh_site_url_root + '/assets/images/empty_network.png'}" width="300" alt="no-network">
                    </div>
                </div>
            </div>`);
        }


        function loadChannelList(serverFetch = 0, isAppend = 0, channelId = '') {
            const current_timestamp = Date.now() * 1000 + Math.floor(performance.now() % 1000);            
            let data = {
                ops: 'channel',
                action: 'list',
                code: _taoh_ops_code,
                key: ntw_room_key,
                room_id: ntw_room_key,
                channel_type: _taoh_channel_direct_message,
                //cfcc45: 1 //cfcache newly added
            };            
            if (isAppend != 0) {
                data.timestamp = current_timestamp;
            }
                        
            let ntw_channels_key = 'ntw_channels_' + ntw_room_key;
            IntaoDB.getItem(objStores.ntw_store.name, ntw_channels_key).then((intao_data) => {
                // Check if data is expired (expires after 10sec (10 * 1000))
                if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 10000) && !serverFetch) {
                    renderChannelList(data, intao_data.values, channelId, isAppend);
                } else {
                    if (!navigator.onLine) return;

                    $.ajax({
                        url: _taoh_cache_chat_url,
                        type: 'GET',
                        dataType: 'json',
                        headers: {'If-None-Match': ntwChannelListETag},
                        data: data,
                        success: function (response, textStatus, jqXHR) {
                            loader(false, loaderArea);
                            if (jqXHR.status === 304) return;
                            ntwChannelListETag = jqXHR.getResponseHeader('taoh-etag') ?? null;

                            if (response.success) {
                                const mergedChannels = (isAppend === 1 && intao_data?.values?.channels)
                                    ? { ...intao_data.values.channels, ...response.channels }
                                    : response.channels;

                                const finalPayload = {
                                    taoh_ntw: ntw_channels_key,
                                    values: {
                                        ...response,
                                        channels: mergedChannels
                                    },
                                    timestamp: Date.now()
                                };

                                IntaoDB.setItem(objStores.ntw_store.name, finalPayload);

                                renderChannelList(data, response, channelId, isAppend);
                            }
                        },
                        error: function (xhr, status, err) {
                            loader(false, loaderArea);
                            console.error('Error Fetching ChannelList : ' + err);
                        }
                    });
                }
            });
        }

        async function updateSpeedNetworkingData(myptoken, showLoader = false) {

            let data = {
                'taoh_action': 'taoh_speed_networking_get_data',
                'key': myptoken,
                'keyslug': ntw_room_key,
                'timestamp': speed_networking_last_timestamp
            };

            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    beforeSend: function() {
                        if(showLoader) {
                            loader(true, loaderAreaSp);
                        }
                    },
                    complete: function(){
                        if(showLoader) {
                            loader(false, loaderAreaSp);
                        }
                    },
                    success: async function (res) {
                        if(showLoader) {
                            loader(false, loaderAreaSp);
                        }
                        if (res.success) {
                            if (Array.isArray(res.all_list) && res.all_list.length > 0) {
                                if (!$('.countDownDiv:visible').length && !$('.successMatchDiv:visible').length) {
                                    $('.speed_networking_div').removeClass('d-none');
                                }                         
                                $('.zeroday-speed').addClass('d-none');     
                                speedNetworkArea.html("");
                                speedNetworkingAddUser(res.all_list);
                            } else {
                                //$('.speed_networking_div').addClass('d-none');
                                //show_empty_network_entries_screen(speedNetworkArea);
                            }

                            if (Array.isArray(res.restricted_ptoken) && res.restricted_ptoken.length > 0) {
                                res.restricted_ptoken.forEach((ptoken) => {
                                    for (const restricted_ptoken of res.restricted_ptoken) {
                                        $(`.speed_networking_div #carousel_item_${restricted_ptoken}`).addClass('d-none').removeClass('active');
                                    }
                                    if($('.speed_networking_carousel .carousel-item').length > 0 && !$('.speed_networking_carousel .carousel-item.active').length) {
                                        var $firstItem = $('.speed_networking_carousel .carousel-item:first');
                                        if ($firstItem.hasClass('d-none')) {
                                            var $nextVisible = $firstItem.nextAll('.carousel-item').not('.d-none').first();
                                            $nextVisible.addClass('active');
                                        } else {
                                            $firstItem.addClass('active');
                                        }
                                    }
                                });
                            } else {
                                $(`.speed_networking_div .carousel-item`).each(function() {
                                    $(this).removeClass('d-none');
                                });
                            }

                            if (Array.isArray(res.accept_ptoken) && res.accept_ptoken.length > 0) {
                                if(speed_networking_last_timestamp != 0) {
                                    const response_data = {
                                        status: 200,
                                        success: true,
                                        accept_ptoken: res.accept_ptoken
                                    };
                                    resolve(response_data);
                                }
                            }

                            if (Array.isArray(res.reject_ptoken) && res.reject_ptoken.length > 0) {

                                $('#clickBlocker').remove();

                                if ($('.countDownDiv:visible').length) {
                                    for (const rej_ptoken of res.reject_ptoken) {

                                        if(rej_ptoken == $('.countDownDiv').data('chatwith')) {
                                            $('.countDownDiv').addClass('d-none');

                                            $('.connect_btn').prop('disabled', false).text('Connect');
                                            $('.not_interested_btn').prop('disabled', false).text('Not interested');

                                            var rej_user_details = await getUserInfo(rej_ptoken, 'public');
                                            if(rej_user_details) {
                                                var rej_user_name = rej_user_details.chat_name;
                                                rej_user_name = rej_user_name.charAt(0).toUpperCase() + rej_user_name.slice(1);
                                                $('.rejectDiv .user_title').text(rej_user_name+" is not available now.");
                                            } else {
                                                $('.rejectDiv .user_title').text("User is not available now.");
                                            }

                                            $('#carousel_item_'+rej_ptoken).remove();

                                            $('.rejectDiv').removeClass('d-none');

                                            $('.speed_networking_carousel .carousel-item').each(function() {
                                                $(this).find('.connect_btn').prop('disabled', false).text('Connect');
                                                $(this).find('.not_interested_btn').prop('disabled', false).text('Not interested');
                                            });

                                            if($('.speed_networking_carousel .carousel-item').length > 0 && !$('.speed_networking_carousel .carousel-item.active').length) {
                                                $('.speed_networking_carousel .carousel-item:first').addClass('active');
                                            }

                                            // if($('.speed_networking_carousel').html() == "") {
                                            //     $('.speed_networking_div').addClass('d-none');
                                            //     show_empty_network_entries_screen(speedNetworkArea);
                                            // }
                                        }
                                    }
                                }
                            }

                            speed_networking_last_timestamp = res.timestamp;

                        } else {
                            console.log('Error :', res);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Error :', xhr.status);
                    }
                });
            });

            if (response.status === 200) {
                $('#clickBlocker').remove();
                for (const accept_ptoken of response.accept_ptoken) {
                    var input = [ntw_room_key, my_pToken, accept_ptoken].sort().join('_');
                    var channelId = await generateSecureSlug(input, 16);
                   
                    $(`.speed_networking_carousel #carousel_item_${accept_ptoken}`).remove();
                    clearInterval(intervalId2);
                    await loadDirectMessage(accept_ptoken, "", "", "0");
                    $('.countDownDiv').addClass('d-none');

                    $('.successMatchDiv').removeClass('d-none');
                    $('.speed_networking_div').addClass('d-none');

                    var accept_ptoken_userinfo = await getUserInfo(accept_ptoken, 'public');

                    $('.successMatchDiv .load_dm').attr('data-chatwith', accept_ptoken);

                    $('.successMatchDiv .user_title').text(accept_ptoken_userinfo.chat_name);
                    
                }
            }
            if($('.speed_networking_carousel .carousel-item').length > 0 && !$('.speed_networking_carousel .carousel-item.active').length) {
                $('.speed_networking_carousel .carousel-item:first').addClass('active');
            }
            if($('.speed_networking_carousel .carousel-item').length == 1) {
                $('.carousel-control-prev').hide();
                $('.carousel-control-next').hide();
            } else {
                $('.carousel-control-prev').show();
                $('.carousel-control-next').show();
            }
        }

        async function renderChannelList(requestData, response, chatchannelId = '', isAppend = 0) {


            let channelListHtml = '';
            //alert(_speed_networking_enable)
            if(_speed_networking_enable == 1) {
                channelListHtml = `
                <li id="speed_networking_channel_block" data-channel_passcode="" data-speed_networking="1" data-channel_ticket_slug="" data-name="channel" >
                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                        <div class="speed_networking_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="📢 Speed Networking">
                            <div class="flex-shrink-0 me-2">
                                <div class="chat-user-img online align-self-center">
                                    <div class="avatar-xs">
                                        <span class="avatar-title rounded-circle bg-primary text-white">
                                            <span class="username">SP</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">                                    
                                <h6 class="text-truncate mb-0 channel_name text-capitalize">#speednetworking</h6>				
                            </div>
                            <div class="flex-shrink-0 ms-2">
                                <button type="button" class="btn btn-success btn-sm speed_networking_update_btn">
                                    <i class="fa fa-refresh align-middle"></i>
                                </button>
                            </div>		                             
                        </div>
                    </a>
                </li>`;
            }

            let channelListHtml_1_1 = '';
            let channelListHtml_1_org = '';

            if(isAppend == 1) {
                channelListHtml = channelList.html();
                channelListHtml_1_1 = $('#usersList-block').html();
                channelListHtml_1_org = $('#organizerChatList').html();
            }

            var dm_count = 0;
            var org_count = 0;
            //$('#dm-block').hide();

            console.log('----------------renderChannelList', response.channels);

            for (const [channelId, channelInfo] of Object.entries(response.channels)) {
                let membersCount = (channelInfo.members).length;
                //channelInfoData.push( { [channelId] : channelInfo } );
                listChannelsArray.push(channelId);
                Object.assign(channelInfoData, {[channelId]: channelInfo});
                if (channelInfo.type == 2 ) { // Direct Message or Group DM


                    /*if(channelId == my_pToken+'-organizer') {
                        //$('#'+my_pToken+'-organizer').show();
                        continue; // Skip organizer and admin channels
                    }*/

                    var userAvatarSrc = `${_taoh_ops_prefix}/avatar/PNG/128/default.png`;
                    var user_chat_name = channelInfo.name;

                    if (channelInfo.data != '' && channelInfo.data != null && channelInfo.data != undefined) {
                        $('#dm-block').show();

                        var channel_data = channelInfo.data;
                        var user_ptoken = my_pToken;
                        if (channel_data[0] != my_pToken)
                            user_ptoken = channel_data[0];
                        else if (channel_data[1] != my_pToken)
                            user_ptoken = channel_data[1];

                        var allow = 0;
                        if (channel_data[0] == my_pToken || channel_data[1] == my_pToken) {
                            allow = 1;
                        }
                        
                        if (allow) {
                            //dm_count++;
                            var active_class = '';
                            if (selectedUser != '' && user_ptoken == selectedUser && !$('.speed_networking_div:visible').length && !$('.countDownDiv:visible').length && !$('.rejectDiv:visible').length && !$('.successMatchDiv:visible').length) {
                                active_class = 'active';


                            }
                            
                            //alert(user_ptoken)
                            //var d_data = await getUserInfo(user_ptoken, 'public');
                            const [userLiveStatus, d_data] = await Promise.all([
                                getUserLiveStatus(user_ptoken).catch((e) => {
                                    console.log(e)
                                }),
                                getUserInfo(user_ptoken, 'public').catch((e) => {
                                    console.log(e)
                                }),
                            ]);
                            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;
                            //alert(chatwith_liveStatus+-----+chatwith)

                            if (chatwith_liveStatus) {
                                var live_status = 'online';
                            } else {

                                var live_status = '';
                            }

                            var current_user_status = userStatusArray[user_ptoken];
                            if (current_user_status != undefined && current_user_status != null && current_user_status != '') {
                                var userMoodStatus = buildUserMoodStatus(current_user_status);
                            } else {
                                var userMoodStatus = '';
                            }

                            userAvatarSrc = d_data?.avatar_image && await checkImageExists(d_data.avatar_image).catch(() => false)
                                ? d_data.avatar_image
                                : `${_taoh_ops_prefix}/avatar/PNG/128/${d_data?.avatar?.trim() || 'default'}.png`;

                            user_chat_name = d_data?.chat_name || getInitials(channelInfo.name);

                            //console.log('-----_can_delete_all_msg----------',_can_delete_all_msg);
                            
                                dm_count++;
                                channelListHtml_1_1 += `<li id="dm-${channelId}" data-ptoken="${d_data.ptoken}" data-chatwith="${d_data.ptoken}" data-name="dm" class="${active_class}" data-channel_id="${channelId}">

                                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                                        <div class="flex-grow-1 d-flex align-items-center overflow-hidden">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="chat-user-img ${live_status} align-self-center me-2 ms-0">
                                                    <img src="${userAvatarSrc}" class="rounded-circle avatar-xs" alt="user-avatar">
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden me-2">
                                                <p class="text-truncate chat-username text-capitalize mb-0">${user_chat_name}</p>
                                                <p class="text-truncate text-muted fs-13 mb-0">${userMoodStatus}</p>
                                            </div>
                                            <div class="ms-auto">
                                                <span class="badge badge-soft-danger rounded p-1 fs-12 unread-count" data-lastview="0" data-count="0">0</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>        
                                `;                            
                        }
                    }
                } else {
                            var channel_active_class = '';
                            if (selectedChannel != '' && channelId == selectedChannel && !$('.speed_networking_div:visible').length && !$('.countDownDiv:visible').length && !$('.rejectDiv:visible').length && !$('.successMatchDiv:visible').length) {
                                channel_active_class = 'active';
                            }
                    

                            if(channelInfo.channel_passcode != undefined && channelInfo.channel_passcode != null && channelInfo.channel_passcode != '') {
                                var channel_passcode = encodeBase64(channelInfo.channel_passcode);
                            } else {
                                var channel_passcode = '';
                            }
                            if (channelInfo.data != '' && channelInfo.data != null && channelInfo.data != undefined) {
                                
                                    var channel_data = channelInfo.data;

                                    
                                    if( _can_delete_all_msg == 1 && channel_data[1] == 'organizer') {
                                        user_ptoken = channel_data[0];
                                    
                                        
                                        const [userLiveStatus, d_data] = await Promise.all([
                                            getUserLiveStatus(user_ptoken).catch((e) => {
                                                console.log(e)
                                            }),
                                            getUserInfo(user_ptoken, 'public').catch((e) => {
                                                console.log(e)
                                            }),
                                        ]);
                                        
                                        
                                        userAvatarSrc = d_data?.avatar_image && await checkImageExists(d_data.avatar_image).catch(() => false)
                                            ? d_data.avatar_image
                                            : `${_taoh_ops_prefix}/avatar/PNG/128/${d_data?.avatar?.trim() || 'default'}.png`;

                                        user_chat_name = d_data?.chat_name || getInitials(channelInfo.name);

                                        /*<div class="chat-user-img  align-self-center me-2 ms-0">*/

                                    }    

                                org_count++;

                                channelListHtml_1_org += `<li id="channel-${channelId}" class="${channel_active_class}"
                                    data-channel_passcode="${channel_passcode}"
                                    data-channel_ticket_slug="${channelInfo.channel_ticket_type}"
                                    data-name="channel" data-channel_id="${channelId}">
                                        <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                                            <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${(channelInfo.description)}">
                                                <div class="flex-shrink-0 me-2 channel_btn">
                                                    <div class="chat-user-img online align-self-center">
                                                        <div class="avatar-xs">
                                                        <img src="${userAvatarSrc}" class="profile-img rounded" alt="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex-grow-1 overflow-hidden channel_btn">                                    
                                                    <h6 class="text-truncate mb-0 channel_name text-capitalize">${user_chat_name}</h6>
                                                </div>
                                                                                
                                            </div>
                                        </a>
                                    </li>`;
                        } 
                        else {
                                var classname = '';
                                //alert(channelInfo.description)
                                if(channelInfo.description == 'Presentation Room' || channelInfo.description == 'watch-party'){
                                    classname = 'watch_party';
                                }

                                channelContruct =  `
                                <li id="channel-${channelId}"  class="${classname} ${channel_active_class}"
                                data-channel_passcode="${channel_passcode}" data-speed_networking="${channelInfo.speed_networking}"
                                data-channel_ticket_slug="${channelInfo.channel_ticket_type}"
                                data-name="channel" data-channel_id="${channelId}">
                                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${(channelInfo.description)}">
                                            <div class="flex-shrink-0 me-2 channel_btn">
                                                <div class="chat-user-img online align-self-center">
                                                    <div class="avatar-xs">
                                                        <span class="avatar-title rounded-circle bg-primary text-white">
                                                            <span class="username">${getInitials(channelInfo.name)}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden channel_btn">                                    
                                                <h6 class="text-truncate mb-0 channel_name text-capitalize">${channelInfo.name}</h6>
                                                ${membersCount > 0 ? `<p class="text-truncate text-muted fs-13 mb-0 members_count" data-count="${membersCount}">${membersCount} Member(s)</p>` : ''}
                                            </div>
                                            <div class="channel_btn">
                                                <div class="flex-shrink-0 ms-2">
                                                    <span class="badge badge-soft-danger rounded p-1 fs-12 unread-count" data-lastview="0" data-count="0">0</span>
                                                </div>
                                            </div>                                
                                            <div class="flex-shrink-0 ms-2 ${((channelInfo.creator == my_pToken || _can_delete_all_msg == 1) && (membersCount < 0 || membersCount == 0) && channelInfo.channel_created_by == 'user' ) ? '' : 'd-none'}">
                                                <span data-channel_id="${channelId}" class="delete_channel_btn" title="Delete">
                                                    <i class="bx bx-trash text-danger fs-18"></i>
                                                </span>
                                            </div>                                
                                        </div>
                                    </a>
                                </li>`;

                                //alert(channelInfo.description)

                                if(channelInfo.description == 'watch-party'){
                                    $('#watch_partyChannel').html(channelContruct);
                                    $('#watch_partyChannel').show();
                                }
                                else{
                                    channelListHtml += channelContruct
                                }

                        }
                }

                // if channel is active and not in reply view then update Mmebers list
                if (selectedChat == 'channel'  && chatWindow === "channel" && !frm_reply_view && selectedChannel && channelId === selectedChannel) {
                    showMembersList(channelId, channelInfo.name);
                }

                
            }

            $('#channelList').html(channelListHtml);

            //console.log('------org_count-----',org_count);
           // alert(_can_delete_all_msg)
            if(org_count > 0 && _can_delete_all_msg == 1) {
               
                //console.log('------org_count-----',channelListHtml_1_org);
                $('#organizerChatList').html(channelListHtml_1_org);
                $('#organizerChatList').removeClass('d-none');
            } else if(_can_delete_all_msg !=1 && organizerOnline) {
                var chatithorghtml = `<li id="channel-organizer"  class=""
                    data-channel_passcode="" 
                    data-channel_ticket_slug=""
                     data-name="channel" data-channel_id="">
                        <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                            <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="Chat with Organizer">
                                <div class="flex-shrink-0 me-2 channel_btn">
                                    <div class="chat-user-img online align-self-center">
                                        <div class="">
                                            <div class="chat-user-img  align-self-center me-2 ms-0">
                                                <img src="${_taoh_ops_prefix}/avatar/PNG/128/default.png" class="rounded-circle avatar-xs" alt="user-avatar">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1 overflow-hidden channel_btn">                                    
                                    <h6 class="text-truncate mb-0 channel_name text-capitalize">#ChatWithOrganizer</h6>
                                 </div>
                                
                            </div>
                        </a>
                     </li>
                    `;

                $('#organizerChatList').html(chatithorghtml);
                $('#organizerChatList').removeClass('d-none');

            }

            //return false;

            if (dm_count == 0 && layout == 3) {
                
            } else {
                //alert(dm_count)

                if (dm_count == 0) {
                    if(isAppend ==1){

                    }
                    else
                    $('#dm-block').addClass('d-none');
                } else {
                    //alert('--------',isAppend)
                    $('#dm-block').removeClass('d-none');
                    

                }

                $('#usersList-block').html(channelListHtml_1_1);
               

            }

            if(channelListHtml_1_1 == "") {
                console.log("channelListHtml_1_1 channelListHtml_1_1", channelListHtml_1_1);
                $('#dm-block').addClass('d-none');
                $('#usersList-block').html('<div class="no_dm_result px-3 py-2 d-flex flex-column align-items-center" style="gap: 12px;"><img src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/images/no_chat.svg" alt="no chats"><span>No Chats Available</span></div>');
            }

            const targetUserLi = $('.usersList').find('#dm-' + chatchannelId);
            if (targetUserLi.length) {
                targetUserLi.trigger('click');
            }

            updateUnreadCountOld('update');

            //alert(layout);
            //alert(watch_party_shown);
            currentElem = $('.watch_party');
            if(layout == 5 &&  watch_party_shown == 0 && currentElem.length > 0) {

                
                    selectedChat = 'watch-party';
                    $('.watchPartySection').show();
                    $('.watchPartySection').addClass('watchPartyEnabled');
                    console.log('----chat_window-----------555555')
                    loadchatWindow('channel');
                    
                    console.log('-----currentElem--------',currentElem)
                    processChannelClick(currentElem); 
                    watch_party_shown = 1;
            }

            if ($('.speed_networking_div:visible').length || $('.countDownDiv:visible').length || $('.rejectDiv:visible').length || $('.successMatchDiv:visible').length) {
                $('#speed_networking_channel_block').addClass('active');
            }

        }

        $(document).on('click', '.delete_channel_btn', function (e) {
            e.stopPropagation();

            $('#delete_message_id').val("");
            $('#delete_message_key').val("");
            $('#delete_confirmation_msg').text("Are you sure to delete the channel?");
            $('#delete_channel_id').val($(this).data('channel_id'));

            $('#deleteModal').modal('show');
        });

        $(document).on('click', '#confirm_delete_btn', async function () {
            
            let channelId = $('#delete_channel_id').val();
            let frmMessageId = $('#delete_message_id').val();
            let frmMessageKey = $('#delete_message_key').val();
            let parentElem = $('#deleteModal').data('parent-elem');

            if (channelId != "" && frmMessageId != "") {
                deleteComment(parentElem, channelId, frmMessageId, frmMessageKey);
                $('#deleteModal').modal('hide');
            } else if (channelId != "") {
                const chat_messages_key = `frm_${ntw_room_key}_${channelId}`;
                IntaoDB.removeItem(objStores.ntw_store.name,  chat_messages_key).catch((err) => console.log('Storage failed', err));

                let data = {
                    ops: 'channel',
                    action: 'delete',
                    code: _taoh_ops_code,
                    channel_id: channelId,
                    key: my_pToken,
                    keyslug: ntw_room_key,
                    //cfcc60: 1 //cfcache newly added
                };

                $.ajax({
                    url: _taoh_cache_chat_url,
                    type: 'GET',
                    dataType: 'json',
                    //headers: {'If-None-Match': ntwChannelListETag},
                    data: data,
                    success: function (response, textStatus, jqXHR) {
                        console.log(response);            
                        if (jqXHR.status === 304) return;
                        if (response.success) {                       
                            $("#channel-"+channelId).remove();
                            $('#deleteModal').modal('hide');
                            if($('#channel-chat').attr('data-channel_id') == channelId) {
                                $('.participants_refresh').trigger('click');
                            }
                        }
                    },
                    error: function (xhr, status, err) {
                        console.error('Error Fetching activity list : ' + err);
                    }
                });   
            } else {
                $('#deleteModal').modal('hide');
                jq_confirm_alert('Warning', 'Message ID not found for deletion. Please try again later.', 'orange', 'Ok');
            }
            
        });

        // Create or update user cell info
        function taoh_network_update_online() {
           // alert();
            var data = {
                'taoh_action': 'taoh_network_update_online',
                'keyslug': ntw_room_key,
                'ptoken': my_pToken,
                'ticketInfo': ticketInfo,
                'geo_enable': <?= (int) ($club_info['geo_enable'] ?? 0) ?>,
            };

            if (data.geo_enable == 1) {
                data.latitude = "<?php echo $lat ?? ''; ?>";
                data.longitude = "<?php echo $long ?? ''; ?>";
            }

            $.post(_taoh_site_ajax_url, data, function (response) {

            }).fail(function () {
                loader(false, loaderArea);
                console.log("Network update online failed!");
            });
        }

        $(document).on('click', '.goto-message', function () {
            let frmMessageKey = $(this).attr('data-frm_message_key');
            $('#msg_'+frmMessageKey)[0].scrollIntoView({ behavior: 'smooth' });
        });

        function createTicketChannel() {
            //let ntw_channels_key = 'ntw_channels_' + ntw_room_key;
            let data = {
                'taoh_action': 'taoh_create_channel_from_ticket',
                'key': my_pToken,
                'ptoken': my_pToken,
                'room_id': ntw_room_key,
                'sent_time': new Date().getTime(),
                'ticket_types': '<?php // echo addslashes($ticket_types) ?? ''; ?>',
                'event_channels': '<?php  echo addslashes($event_channels) ?? ''; ?>',
                'livenow_channels': '<?php  echo addslashes($livenow_channels) ?? ''; ?>',
                'streaming_link': '<?php  echo $club_info['streaming_link'] ?? ''; ?>',


                
            };
            // commented to remove ticket type channels
            //alert(ntw_channels_key);
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (response) {
                    loadChannelList(1);
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status);
                }
            });
        }

        async function taoh_fetch_connection_request() {
            let data = {
                'taoh_action': 'taoh_speed_networking_connect_user_get',
                'key': my_pToken,
                'ptoken': my_pToken,
                'keyslug': ntw_room_key,
            };

            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',

                    success: async function (res) {
                        if (res.status === 'success') {
                            const chat_messages_key = `sn_${ntw_room_key}`;                            
                            await IntaoDB.setItem(objStores.ntw_store.name, {
                                taoh_ntw: chat_messages_key,
                                values: {},
                                timestamp: Date.now()
                            });

                            for (const user of res.data) {
                                console.log(user.ptoken, user.chat_user);

                                let updatedResponse = {};
                                let intao_data = await IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key);
                                if (intao_data?.values) {
                                    updatedResponse = intao_data.values;
                                }
                                if (!Array.isArray(updatedResponse[user.chat_user])) {
                                    updatedResponse[user.chat_user] = [];
                                }                                
                                const exists = updatedResponse[user.chat_user].find(item => item?.ptoken === user.ptoken);
                                if (!exists) {
                                    updatedResponse[user.chat_user].push({
                                        ptoken: user.ptoken,
                                        status: user.status,
                                    });
                                }
                                await IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: chat_messages_key,
                                    values: updatedResponse,
                                    timestamp: Date.now()
                                });
                            }
                        }

                        resolve({
                            status: 200,
                            success: true
                        });
                    },
                    error: function (xhr, status, error) {
                        resolve({
                            status: 201,
                            success: false
                        });
                    }
                });
            });

            if (response.status === 200) {
                console.log("Connection request saved successfully");
            } else {
                console.warn("Connection request failed or no user available");
            }
        }


        function linkUserwithCurrentEvent() {
            var track_data = {
                'action': 'joined_from',
                'location': <?php echo json_encode($location); ?>,
                'ptoken': my_pToken
            };
            taoh_track_activities(track_data);
        }

        async function updateProfileInfo(userInfo, userLiveStatus, fromParticipants = 0) {
            const curUserData = await getUserInfo(my_pToken, 'public').catch((e) => {
                console.log(e)
            });


            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

            if (chatwith_liveStatus == 0) {
                //var d_data = await getUserInfo(user_ptoken, 'public');
                const [userLiveStatus_cell] = await Promise.all([
                    getUserLiveStatus(my_pToken).catch((e) => {
                        console.log(e)
                    }),
                ]);
                chatwith_liveStatus = Boolean(userLiveStatus_cell.output) ? 1 : 0;
            }

            let live_status = chatwith_liveStatus ? 'Online' : 'Away';

            const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

            const skillContent = buildSkillContent(userInfo.skill, userInfo.ptoken);
            var userProfileData = '';
            var about_me = '';
            if (typeof userInfo.aboutme !== undefined && $.trim(userInfo.aboutme) != '' && $.trim(taoh_desc_decode(userInfo.aboutme)).length > 25) {
                var limitedText = $.trim(taoh_desc_decode(userInfo.aboutme)).substring(0, 25);
                about_me = limitedText + `<span id="dots_es">...</span><span id="more_es" style="display:none;">${taoh_desc_decode(userInfo.aboutme).substring(25)} </span>
                <span class="readmore-btn" style="color: #2557A7; text-decoration: underline;" onclick="readmore('es')" id="morebtn_es">Read more</span>`;
            } else if (typeof userInfo.aboutme !== undefined && $.trim(userInfo.aboutme) != '') {
                about_me = userInfo.aboutme;
            }

            let isFollowing = false;
            if (Array.isArray(my_following_ptoken_list) && my_following_ptoken_list.includes(userInfo.ptoken)) {
                isFollowing = true;
            }

            //Message Restriction - Finish Now
            if(show_finish_setup_link == 1) {
                userProfileData += `<!-- Start profile user -->
                    <div class="p-3 border-bottom">
                        <div class="user-profile-img">
                            <img src="${userAvatarSrc}" class="profile-img rounded" alt="">
                            <div class="overlay-content rounded">
                                <div class="user-chat-nav p-2">
                                    <div class="d-flex w-100">
                                        <div class="flex-grow-1">
                                            <button type="button" class="btn nav-btn user-profile-show bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-auto p-3">
                                    <h5 class="user-name mb-0 text-truncate">${userInfo.chat_name}</h5>
                                    <p class="fs-14 text-truncate user-profile-status mt-1 mb-0"><i class="bx bxs-circle fs-10 ${chatwith_liveStatus ? 'text-success' : 'text-warning'} me-1 ms-0"></i>
                                    ${live_status}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- End profile user -->`;
                userProfileData += `<p class="message-restriction">To see <span class="text-capitalize">${userInfo.chat_name}</span> profile, complete your settings <a href="${_settings_url}" target="_blank">now</a></p>`;
            } else {
                userProfileData += `<!-- Start profile user -->
                    <div class="">
                        <div class="user-profile-img">
                            <div class="p-v1-header">
                                <div class="user-chat-nav p-2" style="position: absolute; top: 0; left: 0;">
                                    <div class="d-flex w-100">
                                        <div class="flex-grow-1">
                                            <button type="button" class="btn nav-btn user-profile-show user-profile-close-btn bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <img class="p-v1-img" src="${userAvatarSrc}" alt="profile image">
                                </div>
                                <div class="d-flex align-items-center" style="gap: 8px;">
                                    <p class="name">${userInfo.chat_name}</p>
                                    <!-- <span class="p-type">Recruiter</span> -->
                                </div>
                                <p class="fs-14 text-truncate user-profile-status mt-1 mb-0"></p>

                                ${userInfo.ptoken !== my_pToken ? `
                                <button type="button" class="btn add-to-follow-btn profile_follow_btn" data-ptoken="${userInfo.ptoken}" data-follow_status="${isFollowing ? 1 : 0}"  data-page="networking" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                    <i class="fas fa-user-plus follow-user-plus-icon" aria-hidden="true"></i>
                                </button>` : ''}
                            </div>
                        </div>
                    </div>
                    <!-- End profile user -->

                    <!-- Start user-profile-desc -->
                    <div class="p-4 user-profile-desc" data-simplebar="init"><div class="simplebar-wrapper" style="margin: -24px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 24px;">
                        <div class="d-none text-center border-bottom border-bottom-dashed">
                            <div class="d-flex gap-2 justify-content-center mb-4">
                                <button type="button" class="btn avatar-sm p-0">
                                    <span class="avatar-title rounded bg-info-subtle text-info text-info"><i class="bx bxs-message-alt-detail"></i></span>
                                </button>
                                <button type="button" class="btn avatar-sm p-0 favourite-btn">
                                    <span class="avatar-title rounded bg-danger-subtle text-danger text-body"><i class="bx bx-heart"></i></span>
                                </button>
                                <button type="button" class="btn avatar-sm p-0" data-bs-toggle="modal" data-bs-target=".audiocallModal">
                                    <span class="avatar-title rounded bg-success-subtle text-success"><i class="bx bxs-phone-call"></i></span>
                                </button>
                                <button type="button" class="btn avatar-sm p-0" data-bs-toggle="modal" data-bs-target=".videocallModal">
                                    <span class="avatar-title rounded bg-warning-subtle text-warning text-warning"><i class="bx bx-video"></i></span>
                                </button>
                                <div class="dropdown">
                                    <button class="btn avatar-sm p-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="avatar-title bg-primary-subtle text-primary text-primary rounded"><i class="bx bx-dots-horizontal-rounded"></i></span>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Archive <i class="bx bx-archive text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Muted <i class="bx bx-microphone-off text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">Delete <i class="bx bx-trash text-muted"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>`;

            userProfileData += `<div class="d-flex justify-content-center flex-wrap mb-2" style="gap: 12px;">
                ${(userInfo.ptoken != sidekick_ptoken && userInfo.ptoken != my_pToken && show_finish_setup_link != 1 ) ? `
                    <a id="${userInfo.ptoken}" class="btn std-btn openchatacc mr-2" data-chatwith="${userInfo.ptoken}" data-chatname="${userInfo.chat_name}" data-live="" style="${chatWindow != 'direct_message' ? 'background: #2557A7; color: #fff;' : 'display:none;'}">Chat</a>
                ` : ''}
                <a class="btn bor-btn openProfileModal view_more d-none" data-profile_token="${userInfo.ptoken}">View More</a>

                <!-- view more new -->
                <a class="btn std-btn d-none" href="#viewMore" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="viewMore">
                    View More
                </a>
            </div> `;

            if (skillContent != '') {
                    userProfileData += ` <div class="p-v1-card">
                                    <h5 class="p-v1-title">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.24023 0.454102C9.07324 0.172852 8.76562 0 8.4375 0C8.10938 0 7.80176 0.172852 7.63477 0.454102L4.82227 5.1416C4.64941 5.43164 4.64355 5.79199 4.81055 6.08496C4.97754 6.37793 5.28809 6.55957 5.625 6.55957H11.25C11.5869 6.55957 11.9004 6.37793 12.0645 6.08496C12.2285 5.79199 12.2256 5.43164 12.0527 5.1416L9.24023 0.454102ZM8.4375 9.14062V13.3594C8.4375 14.0068 8.96191 14.5312 9.60938 14.5312H13.8281C14.4756 14.5312 15 14.0068 15 13.3594V9.14062C15 8.49316 14.4756 7.96875 13.8281 7.96875H9.60938C8.96191 7.96875 8.4375 8.49316 8.4375 9.14062ZM3.75 15C4.74456 15 5.69839 14.6049 6.40165 13.9017C7.10491 13.1984 7.5 12.2446 7.5 11.25C7.5 10.2554 7.10491 9.30161 6.40165 8.59835C5.69839 7.89509 4.74456 7.5 3.75 7.5C2.75544 7.5 1.80161 7.89509 1.09835 8.59835C0.395088 9.30161 0 10.2554 0 11.25C0 12.2446 0.395088 13.1984 1.09835 13.9017C1.80161 14.6049 2.75544 15 3.75 15Z" fill="#3D3D3D"></path>
                                        </svg>
                                        <span>Skills</span>
                                    </h5>
                                    <div class="skill-con d-flex flex-wrap align-items-center mt-2 gap-1">
                                        ${skillContent}
                                    </div>
                            </div>`;
                }

            userProfileData += `
                    <div class="p-v1-card">
                        <h5 class="p-v1-title">
                            <svg width="15" height="12" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#555555"/>
                            </svg>
                            <span>Network</span>
                        </h5>
                        <p class="p-v1-content">${safeParseInt(userInfo.tao_followers_count, 0)} Followers and ${safeParseInt(userInfo.tao_following_count, 0)} Following</p>
                    </div>`;

            if (userInfo.full_location != "Unknown Location") {
                userProfileData += `
                            <div class="p-v1-card">
                                    <h5 class="p-v1-title">
                                        <svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.30234 16.618C9.03906 14.4808 13 9.30102 13 6.39154C13 2.86288 10.0885 0 6.5 0C2.91146 0 0 2.86288 0 6.39154C0 9.30102 3.96094 14.4808 5.69766 16.618C6.11406 17.1273 6.88594 17.1273 7.30234 16.618ZM6.5 4.26103C7.07464 4.26103 7.62574 4.48549 8.03207 4.88504C8.43839 5.28459 8.66667 5.82649 8.66667 6.39154C8.66667 6.95659 8.43839 7.49849 8.03207 7.89804C7.62574 8.29759 7.07464 8.52205 6.5 8.52205C5.92536 8.52205 5.37426 8.29759 4.96794 7.89804C4.56161 7.49849 4.33333 6.95659 4.33333 6.39154C4.33333 5.82649 4.56161 5.28459 4.96794 4.88504C5.37426 4.48549 5.92536 4.26103 6.5 4.26103Z" fill="#3D3D3D"></path>
                                        </svg>
                                        <span>Location</span>
                                    </h5>
                                    <p class="p-v1-content">${userInfo.full_location}</p>
                                </div>`;
            }

            if ((about_me !== '') || (userInfo.hobbies != '' && userInfo.hobbies !== undefined && userInfo.hobbies !== null)) {
                userProfileData += ` 
                    <div class="p-v1-card">`;
                if (about_me != '') {
                    userProfileData += `    <h5 class="p-v1-title">
                            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.5 0.349609C9.27765 0.349609 10.9873 1.01176 12.2988 2.19922L12.5557 2.44434C13.8965 3.78522 14.6504 5.6037 14.6504 7.5C14.6504 9.3963 13.8965 11.2148 12.5557 12.5557C11.2148 13.8965 9.3963 14.6504 7.5 14.6504C5.72235 14.6504 4.01274 13.9882 2.70117 12.8008L2.44434 12.5557C1.10345 11.2148 0.349609 9.3963 0.349609 7.5C0.349609 5.72235 1.01176 4.01274 2.19922 2.70117L2.44434 2.44434C3.78522 1.10345 5.6037 0.349609 7.5 0.349609ZM6.5625 9.02539C5.04387 9.02539 3.71435 9.85171 3.00684 11.0811L2.88086 11.3008L3.0498 11.4893C4.14468 12.7086 5.73253 13.4746 7.5 13.4746C9.26771 13.4746 10.8547 12.7058 11.9492 11.4902L12.1191 11.3018L11.9932 11.0811C11.2856 9.85171 9.95613 9.02539 8.4375 9.02539H6.5625ZM7.5 3.40039C6.92927 3.40039 6.37904 3.59845 5.94141 3.95703L5.76074 4.12012C5.29952 4.58134 5.04102 5.20711 5.04102 5.85938C5.04102 6.51164 5.29952 7.13741 5.76074 7.59863C6.22196 8.05986 6.84773 8.31836 7.5 8.31836C8.07073 8.31836 8.62096 8.1203 9.05859 7.76172L9.23926 7.59863C9.70048 7.13741 9.95898 6.51164 9.95898 5.85938C9.95898 5.28864 9.76092 4.73842 9.40234 4.30078L9.23926 4.12012C8.77804 3.6589 8.15227 3.40039 7.5 3.40039Z" fill="#3D3D3D" stroke="#3D3D3D" stroke-width="0.7"></path>
                            </svg>
                            <span>About</span>
                        </h5>
                        <p class="p-v1-content"> ${about_me}</p>

                        <!-- <div class="skill-cat-con d-flex flex-wrap align-items-center" style="gap: 6px;">
                            <span class="fs-12 category category-1" >Volunteer</span>
                            <span class="fs-12 category category-spl" ><span class="spl-p">Bartering</span></span>
                            <span class="fs-12 category category-2" >Career Switch</span>
                            <span class="fs-12 category category-3" >Upskilling</span>
                        </div> -->
                       </div> `;
                }
                // <span style="color: #2557A7; text-decoration: underline;">Read More</span>


                if (userInfo.hobbies != '' && userInfo.hobbies !== undefined) {
                     try {
                        userInfo.hobbies = JSON.parse(userInfo.hobbies);
                    } catch (error) {
                        console.log('Invalid JSON in hobbies field:', error);
                    }
                    let jsonString = JSON.stringify(<?php echo json_encode(PROFESSIONAL_HOBBIES); ?>);
                    let hobbyArr = JSON.parse(jsonString);
                    userProfileData += ` <div class="p-v1-card">
                        <h5 class="p-v1-title">
                            <span>Hobbies</span>
                        </h5>`;
                    userProfileData += `<div class="skill-cat-con d-flex flex-wrap align-items-center mt-2 gap-1">`;
                    $.each(userInfo.hobbies, function (key, item) {
                        if (curUserData.hobbies != undefined && curUserData.hobbies.includes(item)) {
                            userProfileData += ` <span class="fs-11 category category-spl" ><span class="spl-p">` + hobbyArr[item] + `</span></span>`;
                        } else {
                            userProfileData += ` <span class="fs-11 category category-` + (key + 1) + `" >` + hobbyArr[item] + `</span>`;
                        }
                    });
                    userProfileData += ` </div></div>`;
                }
               
            }

                // Company, Designation
                userProfileData += `<!-- <div class="card mt-3">
                            <div class="card-body text-muted">
                                <h5 class="fs-12 text-muted text-uppercase d-flex pb-2" style="gap: 12px; border-bottom: 1px solid #d3d3d3;">
                                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0V15H15V0H0ZM10.5971 10.8884L7.5 13.8583L4.4029 10.8884L6.5625 4.72768L4.4029 1.82812H10.5937L8.4375 4.72768L10.5971 10.8884Z" fill="#3D3D3D"></path>
                                        <path d="M14.5 0.5V14.5H7.55273L7.8457 14.2188L10.9434 11.249L11.1758 11.0264L11.0693 10.7227L8.99707 4.81348L10.9951 2.12695L11.5889 1.32812H3.40723L4.00195 2.12695L6.00195 4.81348L3.93066 10.7227L3.82422 11.0264L4.05664 11.249L7.1543 14.2188L7.44727 14.5H0.5V0.5H14.5Z" stroke="#3D3D3D" stroke-opacity="0.2"></path>
                                    </svg>
                                    <span>Company, Designation</span>
                                </h5>
                                <p class="mb-1">Data Analyst, TamQ Analytics</p>

                            </div>
                        </div> -->`;

                userProfileData += `<!-- <div class="card mt-3">
                                <div class="card-body text-muted">
                                    <h5 class="fs-12 text-muted text-uppercase d-flex pb-2" style="gap: 12px; border-bottom: 1px solid #d3d3d3;">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 0V15H15V0H0ZM10.5971 10.8884L7.5 13.8583L4.4029 10.8884L6.5625 4.72768L4.4029 1.82812H10.5937L8.4375 4.72768L10.5971 10.8884Z" fill="#3D3D3D"></path>
                                            <path d="M14.5 0.5V14.5H7.55273L7.8457 14.2188L10.9434 11.249L11.1758 11.0264L11.0693 10.7227L8.99707 4.81348L10.9951 2.12695L11.5889 1.32812H3.40723L4.00195 2.12695L6.00195 4.81348L3.93066 10.7227L3.82422 11.0264L4.05664 11.249L7.1543 14.2188L7.44727 14.5H0.5V0.5H14.5Z" stroke="#3D3D3D" stroke-opacity="0.2"></path>
                                        </svg>
                                        <span>Experience</span>
                                    </h5>
                                    <p class="mb-1">exp will come here..../p>
                                </div>
                            </div> -->`;

                // complete my profile
                userProfileData += `<!-- <div class="card mt-3 complete-settings">
                            <div class="card-body">
                                <p class="xs-text mb-1">Let People Know More About You !</p>
                                <h6 class="sm-text mb-2">Complete the rest of the settings</h6>
                                <a href="#" class="btn continue-btn py-1">Continue</a>
                           </div>
                        </div>  -->
                `;

                // view more contents
                userProfileData += `
                        <!--<div class="collapse" id="viewMore">
                            <div class="p-v1-card">
                                <div class="p-v1-title d-flex align-items-center justify-content-between">
                                    Experience Details

                                    <button class="btn shadow-none p-0" type="button" data-toggle="collapse" data-target="#viewExperience" aria-expanded="false" aria-controls="viewExperience">
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                </div>
                                <div class="p-v1-content">
                                    &lt;!&ndash; 1st start &ndash;&gt;
                                    <p><b>Data Analyst, TamQ Analytics</b></p>
                                    <p>Present, Since 2010</p>

                                    <div class="collapse" id="viewExperience">
                                        <p>Tokyo, Japan</p>
                                        <p>Remote</p>
                                        <div class="skill-con d-flex flex-wrap align-items-center mt-2" style="gap: 4px;">
                                            <a href="#" class="btn skill-list">Data Analysis</a>
                                            <a href="#" class="btn skill-list">QA / QC</a>
                                            <a href="#" class="btn skill-list">Big Data</a>
                                        </div>
                                        <p class="mt-2">
                                            roles and responsibility Loreum ipsum donor amit, loreum ispum loreum sumlore <span class="v1-read-more">Read more</span>
                                        </p>
                                        &lt;!&ndash; 1st end &ndash;&gt;
                                        <hr class="my-2">
                                        &lt;!&ndash; 2nd start &ndash;&gt;
                                        <p><b>Process Executive, TCS Ltd</b></p>
                                        <p>Full Time, May 2020 to May 2022 . 2 yr</p>
                                        <p>Delhi, India</p>
                                        <p>Onsite</p>
                                        <div class="skill-con d-flex flex-wrap align-items-center mt-2 gap-1">
                                            <a href="#" class="btn skill-list">Data Analysis</a>
                                            <a href="#" class="btn skill-list">QA / QC</a>
                                            <a href="#" class="btn skill-list">Big Data</a>
                                        </div>
                                        <p class="mt-2">
                                            roles and responsibility Loreum ipsum donor amit, loreum ispum loreum sumlore <span class="v1-read-more">Read more</span>
                                        </p>
                                        &lt;!&ndash; 2nd end &ndash;&gt;

                                    </div>
                                </div>
                            </div>

                            <div class="p-v1-card">
                                <div class="p-v1-title d-flex align-items-center justify-content-between">
                                    Educational Qualifications

                                    <button class="btn shadow-none p-0" type="button" data-toggle="collapse" data-target="#viewEduQua" aria-expanded="false" aria-controls="viewEduQua">
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                </div>
                                <div class="p-v1-content">
                                    &lt;!&ndash; 1st start &ndash;&gt;
                                    <p><b>Anna University</b></p>
                                    <p>Master’s Degree in Marketing, class of 2010</p>

                                    <div class="collapse" id="viewEduQua">

                                        <p>Grade: A</p>
                                        <div class="skill-con d-flex flex-wrap align-items-center mt-2" style="gap: 4px;">
                                            <a href="#" class="btn skill-list">Skill 1</a>
                                            <a href="#" class="btn skill-list">Skill 2</a>
                                            <a href="#" class="btn skill-list">Skill 3</a>
                                        </div>

                                        &lt;!&ndash; Activities &ndash;&gt;
                                        <p class="mt-2"><b>Activities</b></p>
                                        <p class="mt-1">
                                            Activities Loreum ipsum donor amit, loreum ispum loreum sumlore  <span class="v1-read-more">Read more</span>
                                        </p>

                                        &lt;!&ndash; Description &ndash;&gt;
                                        <p class="mt-2"><b>Description</b></p>
                                        <p class="mt-1">
                                            Description Loreum ipsum donor amit, loreum ispum loreum sumlore  <span class="v1-read-more">Read more</span>
                                        </p>
                                        &lt;!&ndash; 1st end &ndash;&gt;
                                        <hr class="my-2">
                                        &lt;!&ndash; 2nd start &ndash;&gt;
                                        <p><b>Anna University Chennai</b></p>
                                        <p>Phd Degree in Marketing, class of 2014</p>
                                        <p>Grade: A</p>

                                        <div class="skill-con d-flex flex-wrap align-items-center mt-2" style="gap: 4px;">
                                            <a href="#" class="btn skill-list">Skill 1</a>
                                            <a href="#" class="btn skill-list">Skill 2</a>
                                        </div>

                                        &lt;!&ndash; Activities &ndash;&gt;
                                        <p class="mt-2"><b>Activities</b></p>
                                        <p class="mt-1">
                                            Activities Loreum ipsum donor amit, loreum ispum loreum sumlore  <span class="v1-read-more">Read more</span>
                                        </p>

                                        &lt;!&ndash; Description &ndash;&gt;
                                        <p class="mt-2"><b>Description</b></p>
                                        <p class="mt-1">
                                            Description Loreum ipsum donor amit, loreum ispum loreum sumlore  <span class="v1-read-more">Read more</span>
                                        </p>
                                        &lt;!&ndash; 2nd end &ndash;&gt;

                                    </div>
                                </div>
                            </div>

                            <div class="p-v1-card">
                                <div class="p-v1-title fs-12 text-muted text-uppercase">Hobbies & Interests</div>

                                <div class="hobby-v1-con d-flex flex-wrap align-items-center mt-2" style="gap: 4px;">
                                    <a href="#" class="btn hobby-v1-list">Chess</a>
                                    <a href="#" class="btn hobby-v1-list">Investing</a>
                                    <a href="#" class="btn hobby-v1-list">Puzzles Solving</a>
                                    <a href="#" class="btn hobby-v1-list">+2</a>
                                </div>
                            </div>

                            <div class="p-v1-card">
                                <div class="p-v1-title fs-12 text-muted text-uppercase">Club Information</div>

                                <div class="club-v1-con d-flex flex-wrap align-items-center mt-2" style="gap: 4px;">
                                    <a href="#" class="btn club-v1-list">Career Transition</a>
                                    <a href="#" class="btn club-v1-list">Problem Solving</a>
                                    <a href="#" class="btn club-v1-list">Agriculture & Environment</a>
                                    <a href="#" class="btn club-v1-list">+2</a>
                                </div>
                            </div>

                        </div>

                        <button class="btn btn-v1-link shadow-none mt-3" type="button" data-toggle="collapse" data-target="#viewMore" aria-expanded="false" aria-controls="viewMore">
                            View More
                        </button>-->`;

                userProfileData += `<div class="d-flex justify-content-center flex-wrap mt-3" style="gap: 12px;">
                            <!--<a href="javascript:void(0)" class="openchatacc btn message-n-btn">Message</a>-->
                            <a style="display:none;" target="_blank" href="${_taoh_site_url_root + '/profile/' + userInfo.ptoken}"
                            class="btn view-n-btn">View Full Profile</a>
                            <div>
                            </div>
                        </div>
                    </div></div></div></div>
                    <div class="simplebar-placeholder" style="width: auto; height: 710px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 36px; display: block; transform: translate3d(0px, 0px, 0px);"></div></div></div>
                    <!-- end user-profile-desc -->
                `;
            }            
           
            if(fromParticipants == 1) {
                $('.participants_profile_info').html(userProfileData);
            } else {
                
                $('#user-profile-sidebar').html(userProfileData);
                loadRightSidebar('profile');
            }            
        }

        function taoh_track_activities(track_data) {
            var data = {
                taoh_action: 'taoh_track_activities',
                room_id: ntw_room_key,
                ptoken: my_pToken,
                track_data: track_data
            };

            $.post(_taoh_site_ajax_url, data, function (response) {

            }).fail(function () {
                loader(false, loaderArea);
                console.log("Track failed");
            });
        }

        function zoomin() {
            var radius = $('#radius').val();

           
            
            var newrad = parseInt(radius) + parseInt(100);
            if (newrad <= maxradius) {
                $('#radius').val(newrad);
                loader(true, loaderArea, 75);
                taoh_load_network_entries();
            }

            if(newrad == minradius){
                $('#zoom_out').attr('disabled', true);
                $('#zoom_in').attr('disabled', false);
            }
            else if(newrad == maxradius){
                $('#zoom_in').attr('disabled', true);
                $('#zoom_out').attr('disabled', false);
            }
            else{
                $('#zoom_in').attr('disabled', false);
                $('#zoom_out').attr('disabled', false);
            }
            
        }

        function zoomout() {
            var radius = $('#radius').val();

            
            var newrad = parseInt(radius) - parseInt(100);
            if (newrad >= minradius) {
                $('#radius').val(newrad);
                loader(true, loaderArea, 75);
                taoh_load_network_entries();
            }

            if(newrad == minradius){
                $('#zoom_out').attr('disabled', true);
                $('#zoom_in').attr('disabled', false);
            }
            else if(newrad == maxradius){
                $('#zoom_in').attr('disabled', true);
                $('#zoom_out').attr('disabled', false);
            }
            else{
                $('#zoom_in').attr('disabled', false);
                $('#zoom_out').attr('disabled', false);
            }

        }

        $('.alphanumericInput').on('input', function () {
            //$(this).val($(this).val().replace(/[^a-zA-Z0-9#-_]/g, ''));
            //$(this).val($(this).val().replace(/[^a-zA-Z0-9#_- ]/g, ''));
            $(this).val($(this).val().replace(/[^a-zA-Z0-9#_\- ]/g, ''));
        });

        function forwardChannelTranscript() {

           
            $('.send_transcript').hide();
            $('#loader_transcript').addClass('show');
           
            
            
            var data = {
                'taoh_action': 'taoh_forward_channel_transcript',
                'keyslug': ntw_room_key,
                'event_token': eventtoken,
                'title': eventtitle,
                'channel_id': selectedChannel,
                'channel_name': selectedChannelName,
                'ptoken': my_pToken,
            };

            
            $.post(_taoh_site_ajax_url, data, function (response) {
                    
                    $('.send_transcript').show();
                    $('#loader_transcript').removeClass('show');
                
                jq_confirm_alert('Success', 'We will send the transcript to your email shortly!', 'green', 'Ok');
            
            }).fail(function () {
                   $('.send_transcript').show();
                 $('.loader_transcript').removeClass('show');
                console.log("Forward transcript failed");
            });
        }

        // Base64 Encode
        function encodeBase64(str) {
        return btoa(unescape(encodeURIComponent(str)));
        }

        // Base64 Decode
        function decodeBase64(encodedStr) {
        return decodeURIComponent(escape(atob(encodedStr)));
        }

        
        $(document).on('click', '.openblock', async function () {
            
                     
            $('.open_block').toggle();
               
        });

        $(document).on('click', '.user-profile-close-btn', async function () {
            $('#user-profile-sidebar').removeClass('d-xl-block');
        });

        function getOnlineStatus() {

            //alert();
            
            // Fetch the online status of the organizer
            $.ajax({
                url: _taoh_cache_chat_url,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'If-None-Match': null
                },
                data: {
                    ops: 'organizerOnlineStatus',
                    action: 'get',
                    keyslug:'',
                    key: '',
                    eventtoken: eventtoken,
                    code: _taoh_ops_code,
                },
                success: function (response, textStatus, jqXHR) {
                    if (jqXHR.status === 304) return;
                    
                    if (response.success && response.status !== undefined) {
                        
                        if (response.status == 1) {
                            $('#channel-organizer').show();
                            $('#organizer-block').removeClass('d-none');
                            organizerOnline = 1;
                        } else {
                             $('#organizer-block').addClass('d-none');
                            if(_can_delete_all_msg == 1 ){
                                    $('#organizer-block').show();
                            } 
                            else
                             $('#organizer-block').addClass('d-none');

                            $('#channel-organizer').hide();
                        }
                        
                    } else {
                        
                         $('#organizer-block').addClass('d-none');
                        if(_can_delete_all_msg  == 1){
                            //alert(1);
                            $('#organizer-block').removeClass('d-none');
                        } 
                        else
                         $('#organizer-block').addClass('d-none');
                    
                        $('#channel-organizer').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching online status:', error);
                }
            });
        }

        function taoh_ntw_post_metrics(metrics) {
            if (ntw_room_key?.trim() !== '' && my_pToken?.trim() !== '') {
                save_metrics('networking', metrics, ntw_room_key);
            }
        }

        // Emoji start

        var _emojiElem = messageId = messageKey = channelId = null;
        
        const emojiData = {
            smileys: ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😔', '😪', '🤤', '😴', '😷', '🤒', '🤕', '🤢', '🤮', '🤧', '🥵', '🥶', '🥴', '😵', '🤯', '🤠', '🥳', '😎', '🤓', '🧐'],
            people: ['👶', '🧒', '👦', '👧', '🧑', '👱', '👨', '👩', '🧔', '👴', '👵', '🙋', '🙎', '🙅', '🙆', '💁', '🙇', '🤦', '🤷', '👮', '🕵️', '💂', '👷', '🤴', '👸', '👳', '👲', '🧕', '🤵', '👰', '🤰', '🤱', '👼', '🎅', '🤶', '🦸', '🦹', '🧙', '🧚', '🧛', '🧜', '🧝', '🧞', '🧟', '💆', '💇', '🚶', '🏃', '💃', '🕺'],
            nature: ['🐶', '🐱', '🐭', '🐹', '🐰', '🦊', '🐻', '🐼', '🐨', '🐯', '🦁', '🐮', '🐷', '🐸', '🐵', '🙈', '🙉', '🙊', '🐒', '🐔', '🐧', '🐦', '🐤', '🐣', '🐥', '🦆', '🦅', '🦉', '🦇', '🐺', '🐗', '🐴', '🦄', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦟', '🦗', '🕷️', '🕸️', '🦂', '🐢', '🐍', '🦎', '🐙', '🦑', '🦐'],
            food: ['🍎', '🍊', '🍋', '🍌', '🍉', '🍇', '🍓', '🍈', '🍒', '🍑', '🥭', '🍍', '🥥', '🥝', '🍅', '🍆', '🥑', '🥦', '🥬', '🥒', '🌶️', '🌽', '🥕', '🧄', '🧅', '🥔', '🍠', '🥐', '🥯', '🍞', '🥖', '🥨', '🧀', '🥚', '🍳', '🧈', '🥞', '🧇', '🥓', '🥩', '🍗', '🍖', '🦴', '🌭', '🍔', '🍟', '🍕', '🥪', '🥙'],
            activities: ['⚽', '🏀', '🏈', '⚾', '🥎', '🎾', '🏐', '🏉', '🥏', '🎱', '🪀', '🏓', '🏸', '🏒', '🏑', '🥍', '🏏', '🪃', '🥅', '⛳', '🪁', '🏹', '🎣', '🤿', '🥊', '🥋', '🎽', '🛹', '🛷', '⛸️', '🥌', '🎿', '⛷️', '🏂', '🪂', '🏋️', '🤼', '🤸', '⛹️', '🤺', '🤾', '🏌️', '🧘', '🏄', '🏊', '🤽', '🚣', '🧗', '🚵', '🚴'],
            travel: ['✈️', '🛫', '🛬', '🪂', '💺', '🚁', '🚟', '🚠', '🚡', '🛰️', '🚀', '🛸', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗', '🚘', '🚙', '🛻', '🚚', '🚛', '🚜', '🏎️', '🏍️', '🛵', '🦽', '🦼', '🛺', '🚲', '🛴', '🛹'],
            objects: ['💡', '🔦', '🕯️', '🪔', '🧯', '🛢️', '💸', '💰', '💳', '💎', '⚖️', '🪜', '🧰', '🔧', '🔨', '⚒️', '🛠️', '⛏️', '🪓', '🪚', '🔩', '⚙️', '🪤', '🧱', '⛓️', '🧲', '🔫', '💣', '🧨', '🪃', '🏹', '🛡️', '🚬', '⚰️', '🪦', '⚱️', '🏺', '🔮', '📿', '🧿', '💈', '⚗️', '🔭', '🔬', '🕳️', '🩹', '🩺', '💊', '💉'],
            symbols: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⛎', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '🆔', '⚛️', '🉑', '☢️', '☣️', '📴', '📳']
        };

        const emojiCategoryIcons = {
            smileys: '😀',
            people: '👥',
            nature: '🌿',
            food: '🍔',
            activities: '⚽',
            travel: '✈️',
            objects: '💡',
            symbols: '❤️'
        };

        function loadEmojiCategories() {
            const $catRow = $('#emojiCategories').empty();
            for (const category in emojiData) {
                $catRow.append(`<span class="emoji-cat-btn" data-cat="${category}" style="background:none;border:none;padding:5px;font-size:18px;cursor:pointer;">${emojiCategoryIcons[category]}</span>`);
            }
        }

        function loadEmojis(category = 'smileys') {
            const emojis = emojiData[category] || [];
            const $grid = $('#emojiGrid').empty();
            emojis.forEach(emoji => {
                $grid.append(`<span class="emoji-item" style="font-size: 20px; cursor: pointer;">${emoji}</span>`);
            });
        }

        loadEmojiCategories();
        loadEmojis();

        $('#emojiToggle').on('click', function () {
            $('#emojiPicker').toggle();
        });

        $(document).on('click', '.emoji-cat-btn', function () {
            const cat = $(this).data('cat');
            loadEmojis(cat);
        });

        $(document).on('click', '#emojiGrid .emoji-item', function () {
            const emoji = $(this).text();
            const input = $('#chat_input');
            const cursorPos = input.prop('selectionStart');
            const value = input.val();
            const newValue = value.substring(0, cursorPos) + emoji + value.substring(cursorPos);
            input.val(newValue).focus();
            $('#emojiPicker').hide();
        });

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.emoji-chat-wrapper').length) {
                $('#emojiPicker').hide();
            }
        });

        function showCategory(category) {
            // Update active category button
            $('.emoji-categories button').removeClass('active');
            $(`[data-category="${category}"]`).addClass('active');

            const $grid = $('#emoji-grid');
            $grid.empty();

            emojiData[category].forEach(emoji => {
                const $span = $('<span>')
                    .addClass('emoji-item')
                    .text(emoji)
                    .on('click', () => insertEmoji(emoji));
                $grid.append($span);
            });
        }

        function insertEmoji(emoji) {
            const $messageInput = $('#message-input');

            var chatfrom = $('.emoji-picker').data('chatfrom');
            var chatwith = $('.emoji-picker').data('chatwith');
            var data_type = $('.emoji-picker').data('type');

            if (emojiElem && currentMessageId) {
                addReactionToMessage(data_type, chatfrom, chatwith, _emojiElem, messageId, messageKey, channelId, emoji);
                closeEmojiPicker();
            } else {
                $messageInput.val($messageInput.val() + emoji).focus();
                closeEmojiPicker();
            }
        }

        function toggleEmojiPicker(messageId = null, chatfrom, chatwith, data_type) {
            const $picker = $('#emoji-picker');
            const $overlay = $('#overlay');

            $picker.attr('data-chatfrom', chatfrom);
            $picker.attr('data-chatwith', chatwith);
            $picker.attr('data-type', data_type);

            if ($picker.is(':visible')) {
                closeEmojiPicker();
            } else {
                currentMessageId = messageId;
                $picker.show();
                $overlay.show();
                showCategory('smileys');

                if (messageId && emojiElem) {
                    const rect = $(emojiElem[0]).offset();
                    const bottomOffset = $(window).height() - rect.top + 10;

                    $picker.css({
                        position: 'fixed',
                        bottom: bottomOffset + 'px',
                        right: '20px'
                    });
                }
            }
        }

        function closeEmojiPicker() {
            $('#emoji-picker').hide();
            $('#overlay').hide();
            emojiElem = null;
            currentMessageId = null;
        }

        function addReactionToMessage(data_type, chatfrom, chatwith, elem, messageId, messageKey, channelId, emoji, postToRedis = 1, emojiCount = "") {
            const $message = $(`[data-frm_message_id="${messageId}"]`);
            let $reactionsContainer = $message.find('.message-reactions');

            $message.find('.emoji_placeholder').hide();

            if (!$reactionsContainer.length) {
                $reactionsContainer = $('<div>').addClass('message-reactions');
                $message.append($reactionsContainer);
            }

            const $existingReaction = $reactionsContainer.children().filter(function () {
                return $(this).text().includes(emoji);
            });

            if(emojiCount != "") {
                if ($existingReaction.length) {
                    $existingReaction.text(`${emoji} ${emojiCount}`);             
                } else {
                    const $reaction = $('<div>').addClass('reaction').text(`${emoji} ${emojiCount}`);
                    $reactionsContainer.append($reaction);
                }
            } else {
                if ($existingReaction.length) {
                    const currentText = $existingReaction.text();
                    const match = currentText.match(/(\d+)$/);
                    const currentCount = match ? parseInt(match[1]) : 1;
                    if(postToRedis == 1) {
                        $existingReaction.text(`${emoji} ${currentCount + 1}`);
                    } else {
                        $existingReaction.text(`${emoji} ${currentCount}`);
                    }                
                } else {
                    const $reaction = $('<div>').addClass('reaction').text(`${emoji} 1`);
                    $reactionsContainer.append($reaction);
                }
            }            

            if(postToRedis == 1) {
                emojiPost(data_type, chatfrom, chatwith, elem, channelId, messageId, messageKey, emoji);
            }            

            console.log(`Added reaction ${emoji} to message ${messageId}`);
        }

        function sendMessage() {
            const $input = $('#message-input');
            const message = $input.val().trim();

            if (message) {
                const $messagesContainer = $('#messages-container');
                const messageId = Date.now();

                const $messageDiv = $('<div>')
                    .addClass('message sent')
                    .attr('data-message-id', messageId)
                    .html(`
                        <div class="message-content">${message}</div>
                        <button class="emoji-btn" data-message-id="${messageId}">😊</button>
                        <div class="reaction-popup">
                            <div class="quick-reactions">
                                <span class="quick-reaction" data-emoji="👍">👍</span>
                                <span class="quick-reaction" data-emoji="❤️">❤️</span>
                                <span class="quick-reaction" data-emoji="😂">😂</span>
                                <span class="quick-reaction" data-emoji="😮">😮</span>
                                <span class="quick-reaction" data-emoji="😢">😢</span>
                                <span class="quick-reaction" data-emoji="😡">😡</span>
                                <span class="quick-reaction" data-emoji="➕">➕</span>
                            </div>
                        </div>
                    `);

                $messagesContainer.append($messageDiv);
                $messagesContainer.scrollTop($messagesContainer[0].scrollHeight);
                $input.val('');
            }
        }        

        // Event listeners
        $(document).ready(function() {
            // Message emoji button click
            $(document).on('click', '.emoji-btn', function(e) {
                e.stopPropagation();
                emojiElem = $(this);
                const messageId = $(this).data('message-id');
                toggleEmojiPicker(messageId);
            });

            $('body').on('click', function(e) {
                if (!$(e.target).closest('.emoji-picker').length) {
                    if ($('.emoji-picker').is(':visible')) {
                        $('.emoji-picker').css('display', 'none');
                    }
                }
                if (!$(e.target).closest('.reaction-popup').length) {
                    if ($('.reaction-popup').is(':visible')) {
                        $('.reaction-popup').css('display', 'none');
                    }
                }
            });

            $('#arrowContainer_dm').on('click', function () {
                let channelId = $('#users-chat').data('channel_id');
                $('#downArrow_dm, #upArrow_dm').toggleClass('d-none d-flex');
                $('.pin_message_div-dm'+channelId).toggleClass('d-none d-flex');
            });

            $('#arrowContainer').on('click', function () {
                let channelId = $('#channel-chat').data('channel_id');
                $('#downArrow, #upArrow').toggleClass('d-none d-flex');
                $('.pin_message_div'+channelId).toggleClass('d-none d-flex');
            });

            // Quick reaction clicks
            $(document).on('click', '.quick-reaction', function(e) {
                e.stopPropagation();

                _emojiElem = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn');

                const emoji = $(this).data('emoji');
                messageId = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn').data('frm_message_id');
                messageKey = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn').data('frm_message_key');

                let chatfrom = $(this).closest('.reaction-popup').data('chatfrom');
                let chatwith = $(this).closest('.reaction-popup').data('chatwith');
                let data_type = $(this).closest('.reaction-popup').data('type');
                
                let ch_channelId = $('#channel-chat').data('channel_id');
                let user_ChannelId = $('#users-chat').data('channel_id');
                var channelId;
                var emoji_from;
                if($('#channel-chat:visible').length) {
                    channelId = ch_channelId;
                    emoji_from = "channel";
                }
                if($('#users-chat:visible').length) {
                    channelId = user_ChannelId;
                    emoji_from = "dm";
                }

                $(this).closest('.reaction-popup').hide();
                
                if (emoji === '➕') {
                    emojiElem = $(this).closest('.message').find('.emoji-btn');
                    toggleEmojiPicker(messageId, chatfrom, chatwith, data_type);
                } else {                    
                    addReactionToMessage(data_type, chatfrom, chatwith, _emojiElem, messageId, messageKey, channelId, emoji);                                       
                }
            });

            // Show/hide reaction popup on hover
            $(document).on('click', '.emoji_btn', function() {
                $('.reaction-popup').hide();
                $(this).closest('.user-chat-content').siblings('.reaction-popup').show();
                //$(this).siblings('.reaction-popup').show();
            });

            $(document).on('mouseleave', '.message', function() {
                $(this).find('.reaction-popup').hide();
            });

            // Close emoji picker when clicking overlay
            $(document).on('click', '#overlay', function() {
                closeEmojiPicker();
            });

            let skillShowMore = [];
            $(document).on('click', '.show-more', function () {
                let key = $(this).attr('data-id');
                skillShowMore.push(key);
                $('.more-content-' + key).removeClass('d-none');
                $('.less-content-' + key).addClass('d-none');
            });
            $(document).on('click', '.show-less', function () {
                let key = $(this).attr('data-id');
                skillShowMore = skillShowMore.filter(item => item !== key);
                $('.more-content-' + key).addClass('d-none');
                $('.less-content-' + key).removeClass('d-none');
            });

            $(document).on('click', '.show_more_btn', function() {
                var $span = $(this).siblings('span');
                var isHidden = $span.hasClass('d-none');
                if (isHidden) {
                    $span.removeClass('d-none');
                    $(this).text('Show Less');
                } else {
                    $span.addClass('d-none');
                    $(this).text('Show More');
                }
            });

            // Handle Enter key in input
            $('#message-input').on('keypress', function(e) {
                if (e.which === 13) {
                    sendMessage();
                }
            });
        });

        // Initialize with smileys category
        showCategory('smileys');        

    // Emoji end

    //Channel Loading Manually
    $(window).on('load', function () {
        
        if(!$('.channelList li').length) {
            console.log("channel loads manually");                
            loadChannelList(1);
        }
        
        //alert(my_profileType+" * "+_taoh_live_user_count+" * "+DMLastMsgSentTime+" * "+_taoh_last_job_post_date);
        //console.log("^^^^^^channelLastMsgSentTime", channelLastMsgSentTime);

        updateRoomData(ntw_room_key, _taoh_last_job_post_date, DMLastMsgSentTime, channelLastMsgSentTime, my_profileType, _taoh_live_user_count);
        <?php if(TAOH_DOJO_SUGGESTION_ENABLE) { ?>
             let timelimit = <?php echo (int)TAOH_DOJO_SUGGESTION_TIMELIMIT; ?>;
             let innertimelimit = Math.floor(timelimit / 2);
            //console.log(timelimit+'------timelimit----------'+innertimelimit);
           /*  const dojoCheckNetworkingStatus = () => {
                if (!document.hidden) taoh_load_dojo_suggestion(ntw_room_key);
            };
        
            setInterval(dojoCheckNetworkingStatus, 30000); // 900000 every 15 minutes
            */
            // Every 5 mins: refresh all contexts
            setInterval(() => {
                //refreshDojoContexts(ntw_room_key);
            }, timelimit);

            // Every 1 min: check one scenario
            setInterval(() => {
                //checkNextDojoScenario();
            }, innertimelimit );

            // Initial trigger
            //refreshDojoContexts(ntw_room_key);
            //checkNextDojoScenario();
        <?php } ?>
    });

    function updateRoomData(room_key, job_post_date, dm_sent_time, channel_sent_time, profile_type, live_user_count) {
        roomSlugData[room_key] = {
            jobPostDate: job_post_date,
            dmSentTime: dm_sent_time,
            channelSentTime: channel_sent_time,
            profileType: profile_type,
            liveUserCount: live_user_count,
        };
        console.log("============", roomSlugData);        
    }
    
    
    </script>

<?php
taoh_get_footer();