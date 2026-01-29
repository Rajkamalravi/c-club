<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/raj/assets/icons/icons.php';
define('TAO_CURRENT_APP_INNER_PAGE', 'networking');
taoh_get_header();

$requestUri = $_SERVER['REQUEST_URI'];
if (strpos($requestUri, '/room/') === false) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
    exit();
}

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}

function convertEmbedSrc($url, $opts = []) {
    try {
        $u = parse_url($url);
        if (!$u || !isset($u['host'])) return null;

        $host = strtolower($u['host']);
        $path = isset($u['path']) ? rtrim($u['path'], '/') : '';
        $query = [];
        if (isset($u['query'])) parse_str($u['query'], $query);

        // Helper for query params
        $q = function($key) use ($query) {
            return $query[$key] ?? null;
        };

        /* ------------------ YOUTUBE ------------------ */
        if (preg_match('/youtube\.com$|youtu\.be$/', $host)) {

            // Already embed URL
            if (strpos($path, "/embed/") === 0) return $url;

            // Shorts
            if (strpos($path, "/shorts/") === 0) {
                $parts = explode('/', $path);
                return "https://www.youtube.com/embed/" . $parts[2];
            }

            // youtu.be short form
            if (strpos($host, "youtu.be") !== false) {
                return "https://www.youtube.com/embed/" . ltrim($path, '/');
            }

            // Normal watch URL
            if ($path === "/watch" && $q("v")) {
                return "https://www.youtube.com/embed/" . $q("v");
            }

            // Playlist
            if (($path === "/playlist" || $path === "/watch") && $q("list")) {
                return "https://www.youtube.com/embed/videoseries?list=" . $q("list");
            }
        }

        /* ------------------ VIMEO ------------------ */
        if (preg_match('/vimeo\.com$/', $host)) {
            if (preg_match('#^/(?:video/)?(\d+)#', $path, $m)) {
                return "https://player.vimeo.com/video/" . $m[1];
            }
        }

        /* ------------------ TWITCH ------------------ */
        if (preg_match('/twitch\.tv$/', $host)) {

            $parent = $opts['parent'] ?? ($_SERVER['SERVER_NAME'] ?? "");

            if (strpos($path, "/videos/") === 0) {
                $parts = explode('/', $path);
                return "https://player.twitch.tv/?video=" . $parts[2] . "&parent=" . $parent;
            }

            if (strpos($path, "/clip/") === 0) {
                $parts = explode('/', $path);
                return "https://clips.twitch.tv/embed?clip=" . $parts[2] . "&parent=" . $parent;
            }

            $pieces = explode('/', $path);
            if (isset($pieces[1]) && $pieces[1] !== "") {
                return "https://player.twitch.tv/?channel=" . $pieces[1] . "&parent=" . $parent;
            }
        }

        /* ------------------ DAILYMOTION ------------------ */
        if (preg_match('/dailymotion\.com$|dai\.ly$/', $host)) {
            if (strpos($host, 'dai.ly') !== false) {
                $id = ltrim($path, '/');
            } else {
                preg_match('#^/video/([^_/]+)#', $path, $m);
                $id = $m[1] ?? null;
            }

            if ($id) return "https://www.dailymotion.com/embed/video/" . $id;
        }

        /* ------------------ LOOM ------------------ */
        if (preg_match('/loom\.com$/', $host)) {
            if (preg_match('#/share/([a-z0-9]+)#i', $path, $m)) {
                return "https://www.loom.com/embed/" . $m[1];
            }
        }

        /* ------------------ WISTIA ------------------ */
        if (preg_match('/wistia\.(com|net)$/', $host)) {
            if (preg_match('#/(?:medias|embed)/([a-z0-9]+)#i', $url, $m)) {
                return "https://fast.wistia.com/embed/medias/" . $m[1];
            }
        }
    } catch (Exception $e) {
        return null;
    }

    return null;
}


$pagename = 'networking';
$appname = TAOH_CURR_APP_SLUG ?? 'club';
$enable_convo = 1;
//$watchPartyEnabledChannel = 0;

$taoh_user_is_logged_in = taoh_user_is_logged_in();
if (!$taoh_user_is_logged_in) {
    taoh_set_error_message('Please login to access ' . ($pagename ?? 'this') . ' page');
    taoh_redirect(TAOH_LOGIN_URL);
    taoh_exit();
}


$role = '';
$user_info_obj = taoh_user_all_info();
$ptoken = $user_info_obj->ptoken;
if (isset($user_info_obj->title)) {
    error_reporting(E_ALL);
    foreach ($user_info_obj->title as $key => $value) {
        list ($id, $role) = explode(':>', $value);
    }
}
$skillarray = [];
if (isset($user_info_obj->skill)) {
    error_reporting(E_ALL);
    foreach ($user_info_obj->skill as $key => $value) {
        list ($id, $skilll) = explode(':>', $value);
        $skillarray[] = $skilll;
    }
    $skill = implode(',', $skillarray);
}

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

$contslug = taoh_parse_url(2);

if (empty($contslug)) {
//        taoh_redirect(TAOH_SITE_URL_ROOT);
    showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1002, 'networking');
    taoh_exit();
}

$contslug_arr = explode('-', $contslug);
$keytoken = array_pop($contslug_arr);

if (isset($_GET['chatwith']) && !empty($_GET['chatwith'])) {
    $chatwith = $_GET['chatwith'];
    $chatwith_usr_json = taoh_get_user_info($chatwith, 'public');
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

if (isset($_GET['channel_id']) && !empty($_GET['channel_id'])) {
    $ntw_view = 2;
}

$sidekick_avatar = TAOH_CDN_MAIN_PREFIX.'/images/Group 194.svg';
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


$get_room_info_response = getRoomInfo($keytoken, $user_info_obj->ptoken);
$room_info_arr = json_decode($get_room_info_response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1004, 'networking');
    taoh_exit();
}

if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
    $room_info = $room_info_arr['output'];
    $room_data = $room_info['room'] ?? [];
} else {
    showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1004, 'networking');
    taoh_exit();
}

$create_channel_flag = 0;
$can_delete_all_msg = 0;
$owner_org_ptoken = [];
$who_can_create_video_room = [];

$room_app = $room_data['room_type'] ?? '';
$breadcrumbs = $room_data['breadcrumbs'] ?? [];
$channels = $room_data['channels'] ?? [];
$geo_enabled = (int)(($room_data['geo_enable'] ?? 0) == 1);
$isEventNetworking = $room_app === 'event';
$eventtoken = $room_data['eventtoken'] ?? '';
$keyword = $room_data['keyword'] ?? '';
$org_video_message_link = $room_data['org_video_message_link'] ?? '';
$streaming_link = $room_data['streaming_link'] ?? '';
if (!empty($streaming_link)) {
    $ntw_view = 5;
}

$all_browse_channel_tabs = $room_data['browse_channel_tabs'] ?? [];
$browse_channel_tabs = array_values(array_filter(
    $all_browse_channel_tabs,
    fn($tab) => !empty($tab['slug']) && !empty($tab['name'])
));
$browse_channel_first_tab = $browse_channel_tabs[0] ?? null;
$browse_channel_first_tab_slug = $browse_channel_first_tab['slug'] ?? '';

$organizer_ptokens_arr = array();
if (!empty($room_data['organizer_ptokens'] ?? '')) {
    if(is_array($room_data['organizer_ptokens'])) {
        $organizer_ptokens_arr = $room_data['organizer_ptokens'];
    } else {
        $organizer_ptokens_arr = explode(',', $room_data['organizer_ptokens']);
    }
    $owner_org_ptoken = array_filter($organizer_ptokens_arr);
}

$event_owner_ptoken = $room_data['ptoken'] ?? '';
if (!empty($event_owner_ptoken)) {
    $owner_org_ptoken[] = $event_owner_ptoken;
    $who_can_create_video_room[] = $event_owner_ptoken;
    if ($event_owner_ptoken === $ptoken) {
        $create_channel_flag = 1;
    }
}

if (in_array($ptoken, $owner_org_ptoken)) {
    $can_delete_all_msg = 1;
}

$ticket_types_array = $room_data['ticket_types'] ?? [];
$sponsor_array = $room_data['sponsors'] ?? [];
if (!empty($sponsor_array) && is_array($sponsor_array)) {
    foreach ($sponsor_array as $sponsor) {
        if (!empty($sponsor['ptoken'])) {
            $who_can_create_video_room[] = $sponsor['ptoken'];
            if ($sponsor['ptoken'] === $ptoken) {
                $create_channel_flag = 1;
            }
        }
    }
}

$exhibitor_array = $room_data['exhibitors'] ?? [];
foreach ($exhibitor_array as $exhibitor) {
    if (!empty($exhibitor['ptoken'])) {
        //$who_can_create_video_room[] = $exhibitor['ptoken'];
        if ($exhibitor['ptoken'] === $ptoken) {
            $create_channel_flag = 1;
        }
    }
}

$msg_from_owner = $room_data['msg_from_owner'] ?? '';
$show_video_conv_btn = ($room_data['disable_video_conversation'] ?? '') == 1;
$allow_auto_manage = ($room_data['auto_manage'] ?? '') == 1;

$sub_app = $room_info_arr['output']['sub_app'] ?? '';
$club_info = $room_info_arr['output']['club'] ?? '';

if ($parse_url_1 !== 'room' && $parse_url_1 !== 'forum' && $parse_url_1 !== 'custom-room' && $parse_url_1 !== 'dm' && $parse_url_1 !== 'live') {
    if (!empty($club_info) && isset($club_info['links']['club'])) {
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


$rsvp_slug = '';
$current_ticket_type = [];


if ($isEventNetworking) {
    $taoh_call = 'events.rsvp.get';
    $taoh_vals = array(
        'token' => TAOH_API_TOKEN,
        'ops' => 'rsvp',
        'mod' => 'events',
        'eventtoken' => $eventtoken,
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

        if (empty($current_ticket_types)) {
            taoh_redirect(TAOH_SITE_URL_ROOT );
            exit();
        }

        $current_ticket_type = array_values($current_ticket_types)[0];
    } else {
        if (!TAOH_DEV_SITE) {
            taoh_set_error_message('You Should RSVP to the event to access this page');
            taoh_redirect(TAOH_SITE_URL_ROOT . '/events/d/' . $event_title . '-' . $eventtoken);
            die();
        }
    }
}

$room_title = $room_data['title'];

array_push($who_can_create_video_room, $user_info_obj->ptoken);

//$create_channel_flag = 0;
// get event meta info
//$taoh_call = "events.content.get";
//$search = $type = '';
//$taoh_vals = array(
//    'mod' => 'events',
//    'token' => taoh_get_dummy_token(),
//    'eventtoken' => $eventtoken,
//    //'cfcc1d'=> 1, //cfcache newly added
//);
//$event_meta_data = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
//
//if(isset($event_meta_data['success']) && $event_meta_data['success'] == true){
//    $event_meta_data = $event_meta_data['output'];
//    $exhibitor_array = $event_meta_data['output']['event_exhibitor'] ?? [];
//    if(isset($exhibitor_array) && count($exhibitor_array) > 0){
//        foreach($exhibitor_array as $kkk=>$val){
//            if(isset($val['ptoken']) && $val['ptoken'] !=''){
//                if( $val['ptoken'] == $ptoken){
//                    // echo "<br> Event exhibitor creation";
//                    $create_channel_flag = 1;
//                }
//
//            }
//        }
//    }
//}

$lat = '';
$long = '';
$radius = 150000;
$unit = 'km';
// $ptoken = $user_info_obj->ptoken;
$coordinates = $user_info_obj->coordinates;
if (!empty($coordinates)) {
    $co_array = explode('::', $coordinates);
    $lat = $co_array[0];
    $long = $co_array[1];
}

//$show_video_conv_btn = $isEventNetworking && (($events_data['disable_video_conversation'] ?? '') != '1');
//$allow_auto_manage = $isEventNetworking && (($events_data['auto_manage'] ?? '') == '1');

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

$rules = DOJO_NETWORKING_MESSAGE;

if(defined('TAOH_SPEEDNETWORKING_ENABLE') && TAOH_SPEEDNETWORKING_ENABLE == 1) {
    if ($isEventNetworking && !empty($streaming_link))
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


//$isEventNetworking = !empty($eventtoken);

?>

    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/node-waves/waves.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/glightbox/css/glightbox.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/tributejs/tribute.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/chat4/icons.min.css">
    <link rel="stylesheet" href="<?php echo TAOH_CDN_CSS_PREFIX; ?>/chat4/chat.css?v=<?php echo TAOH_CSS_JS_VERSION;?>">

    <style>
        .select2-container { z-index: 2000 !important; }
        .select2-container .select2-dropdown { z-index: 2000 !important; }

        #browseChannelTab .nav-link.active {
            background: #0d6efd;
            color: #ffffff;
            border: 1px solid #0d6efd;
        }

        .add_members_wrapper {
            border-radius: 6px;
            padding: 15px;
            font-size: 20px;
            width: 200px;
        }

        .membersModalChannelStar {
            border: 1px solid;
            border-radius: 4px;
            padding: 4px 15px;
        }

        .membersModalChannelStar i {
            font-size: smaller;
        }

        .channel_info_view, .readmore-btn {
            cursor: pointer;
        }

        .members_view {
            color: #0d6efd !important;
            font-weight: 400 !important;
        }
        .members_view:hover {
            /*text-decoration: underline;*/
        }

        .channel-item.disabled {
        pointer-events: none;
        opacity: 0.5;
        }

        /* small inline loading text (optional) */
        #channel-loading {
        font-size: 12px;
        color: #666;
        margin-top: 6px;
        }

        ul.dotted-list {
            gap: 30px;
            align-items: center;
            display: flex;
        }

        ul.dotted-list > li { list-style: disc !important; }
        ul.dotted-list > li:first-child { list-style: none !important; }

        #dmChannelList .chat-user-img .avatar-xs,
        #exhibitorChannelList .chat-user-img .avatar-xs,
        #sponsorChannelList .chat-user-img .avatar-xs,
        #organizerChannelList .chat-user-img .avatar-xs {
            height: 2rem !important;
            width: 2rem !important;
            min-height: 2rem;
            max-height: 2rem;
            min-width: 2rem;
            max-width: 2rem;
        }

        #createChannelForm .modal-body {
            max-height: 60vh;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .profile_follow_btn {
            background-color: transparent;
        }

        .profile_follow_btn[data-follow_status="1"] {
            background-color: #2557A7 !important;
            border: 1px solid #2557A7 !important;
            color: #ffffff !important;
        }

        .dropdown-menu a, .openProfileSideBar {
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
        .view-replies  {
            cursor: pointer;
        }

        .collapsible {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease;
        }

        .collapsible.open {
            max-height: 500px; /* adjust based on expected content size */
        }

        .channnel_collapsible, .sn_channel_description  {
           display:none;
            transition: max-height 0.3s ease;
        }

        .channnel_collapsible.open, .sn_channel_description.open {
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

        .emoji_placeholder, .a-link {
            cursor: pointer;
        }

        .cw_channel_title, .cw_channel_profile_img {
            cursor: pointer;
        }

        .extra-skill-toggle {
            background: #7b98e780;
            border: none !important;
        }

        .current_status_sel {
            height: 42px;
            padding: 3px 10px 3px 15px;
            border: 1px solid #d0d0d0;
            border-radius: 5px;
            width: 100%;
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
                                <ul class="col-md-10 nav nav-tabs justify-content-left border-0 mt-3 mb-3 <?=($room_app == "live" ? 'd-none' : '')?>" role="tablist">
                                    <?php
                                    $link_count = count($breadcrumbs);
                                    /*$last_crumb = $breadcrumbs[$link_count - 1];

                                    if (str_contains($last_crumb['link'], 'networking')) {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME);
                                    } else {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . $last_crumb['link']);
                                    }*/
                                    $descc = $club_info['description'] ?? '';
                                    $count = 1;
                                    foreach ($breadcrumbs as $value) {
                                        $link = $value['url']; // TAOH_SITE_URL_ROOT
                                        if($count == count($breadcrumbs)){
                                            define('TAOH_NETWORK_REFERRAL_URL', $link);
                                        }

                                        $isBreadcrumbHasLink = !empty($value['url']) && $value['url'] != '#';
//                                        if(empty($value['url']) || $value['url'] == '#') $link = 'javascript:void(0);';

                                        echo '<li class="nav-item"  ' . $count . '>';
                                        if ($isBreadcrumbHasLink) {
                                            echo '<a href="' . $link . '">';
                                        }
                                        echo $value['label'];
                                        if ($isBreadcrumbHasLink) {
                                            echo '</a>';
                                        }

                                        if($count < $link_count)
                                            echo icon('chevron-right', '#000000', 19);

                                        echo '</li>';

                                        $count++;
                                    }

//                                    if ($parse_url_1 == 'dm') {
//                                       // define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . '/message/dm');
//                                        echo '<li class="nav-item"></span>
//                                            DM</li>';
//                                    }
//                                    else{
//
//                                         echo '<li class="nav-item"></span>
//                                            Networking</li>';
//                                    }


                                    if ($isEventNetworking && !empty($msg_from_owner)) {
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
                                    <?php echo html_entity_decode(taoh_title_desc_decode($descc)); ?>
                                </div>

                                <?php
                                if ($isEventNetworking && !empty($msg_from_owner)) { ?>
                                    <div id="org_msg" class="collapse" data-parent="#breadcrumbs_accordion">
                                        <?php echo html_entity_decode(taoh_title_desc_decode($msg_from_owner)); ?>
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

    <div class="layout-wrapper chat-layout d-lg-flex bg-white <?=(!$eventtoken) ? 'non-event': '' ?>" data-bs-theme="light">

        <!-- Start left sidebar-menu -->
        <div class="side-menu flex-lg-column d-none">
            <!-- Start side-menu nav -->
            <div class="flex-lg-column my-2 sidemenu-navigation">
                <ul class="nav nav-pills side-menu-nav" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active mb-1" id="pills-chat-tab" data-bs-toggle="pill" href="#pills-chat" role="tab">
                            <!-- <i class="ri-home-2-line"></i> -->
                            <i class="fa-solid fa-users"></i>
                            <!-- <span class="badge bg-danger fs-11 rounded-pill sidenav-item-badge">19</span> -->
                        </a>
                        <span class="d-none d-lg-block text-center fs-11">Participants</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-1" data-bs-toggle="pill" href="#" role="tab">
                            #
                        </a>
                        <span class="d-none d-lg-block text-center fs-11">Channels</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-1" data-bs-toggle="pill" href="#" role="tab">
                            <i class="fa-solid fa-bookmark"></i>
                        </a>
                        <span class="d-none d-lg-block text-center fs-11">My Favorites</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-1" data-bs-toggle="pill" href="#" role="tab">
                            <i class="fas fa-comments"></i>
                        </a>
                        <span class="d-none d-lg-block text-center fs-11">Direct Messages</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mb-1" data-bs-toggle="pill" href="#" role="tab">
                            <i class="fa-solid fa-display"></i>
                        </a>
                        <span class="d-none d-lg-block text-center fs-11">Presentations</span>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="pills-contacts-tab" data-bs-toggle="pill" href="#pills-contacts" role="tab">
                            <i class="ri-contacts-book-line"></i>
                        </a>
                    </li>  -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" id="pills-bookmark-tab" data-bs-toggle="pill" href="#pills-bookmark" role="tab">
                            <i class="ri-bookmark-3-line"></i>
                        </a>
                    </li> -->
                    <!-- <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" id="pills-setting-tab" data-bs-toggle="pill" href="#pills-setting" role="tab">
                            <i class="ri-settings-4-line"></i>
                        </a>
                    </li> -->
                    <!-- <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" id="pills-user-tab" data-bs-toggle="pill" href="#pills-user" role="tab">
                            <i class="ri-user-3-line"></i>
                        </a>
                    </li> -->
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
                            <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/profile_cover.jpg" class="profile-img" style="height: 160px;" alt="">
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
                                <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/users/avatar-maxresdefault.jpg" class="rounded-circle avatar-lg img-thumbnail" alt="">
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
                                            <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/small/img-maxresdefault.jpg" alt="media img" class="img-fluid">
                                        </a>
                                    </div>
                                    <div class="media-img-list">
                                        <a href="#">
                                            <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/small/img-2.jpg" alt="media img" class="img-fluid">
                                        </a>
                                    </div>
                                    <div class="media-img-list">
                                        <a href="#">
                                            <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/small/img-4.jpg" alt="media img" class="img-fluid">
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
                                                <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/pdf-file.png" alt="" class="avatar-xs">
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
                                                <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/image-file.png" alt="" class="avatar-xs">
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
                                                <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/image-file.png" alt="" class="avatar-xs">
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
                                                <img src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/chat4/zip-file.png" alt="" class="avatar-xs">
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


                            <button class="btn row ml-3 my-3 wp-en px-1">
                                <i class="fas fa-bars fs-24"></i>
                            </button>

                            <!-- Start Participant -->
                            <div class="w-hide">

                                <div class="left-section d-flex align-items-center px-4 mt-4 mb-2">
                                    <div class="flex-grow-1 mt-2 mb-2 " >
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase"> What's On Your Mind?</h4>
                                    </div>
                                     <!-- right side bar access btn  -->
                                    <div class="flex-shrink-0 d-xl-none">
                                        <div class="dropdown user-chat-nav">
                                            <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item d-flex justify-content-between align-items-center chatlist-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="left-section  status_div">
                                    <p style="position: relative" class="d-flex update-status px-3">
                                        <span id="loadEmoji"><img onclick="openStatusModal();" src="<?php echo TAOH_CDN_MAIN_PREFIX . '/images/emojis/default.svg';?>" alt="Emoji" id="loadEmojiImg" class="update-image" style="left: 26px;"></span>
                                        <input class="form-control pl-5 light-dark-card" type="text" maxlength="140" value="" name="my_status" id="my_status" placeholder="Say something">
                                    </p>
                                </div>
                            </div>

                            <div data-intro="Find all the participants here" data-step="1" class="w-hide">
                                <div class="participants-active participants_refresh_div left-section d-flex align-items-center py-2 px-4 mt-4 mb-2">
                                    <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="Partcipants">
                                        <div class="flex-shrink-0 me-2">
                                            <div class="chat-user-img align-self-center">
                                                <div class="avatar-xs">
                                                    <span class="avatar-title bg-white rounded-circle border text-dark">
                                                        <i class="fa align-middle fa-group" ></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 mt-2 mb-2 participants_refresh" >
                                            <span class="mb-0 channel_name text-capitalize" style="font-size:14px;">Participants</span>

                                        </div>

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


                            <!-- Speed Networking channel list -->
                            <div data-intro="" data-step="5" class="d-none1 w-hide">
                                <div class="left-section chat-message-list mb-3">
                                    <div class="collapse show" id="speedChannelCollapse">
                                        <ul class="speedChannelList list-unstyled chat-list chat-user-list" id="speedChannelList"></ul>
                                    </div>
                                </div>
                            </div>


                            <!-- Start chat-message-list -->
                            <div class="my_channel_div d-none">
                                <div class="w-hide">
                                    <div class="left-section  d-flex align-items-center px-4 mt-4 mb-2">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-0 fs-11 text-muted text-uppercase">
                                                My Favorites
                                                <button class="btn shadow-none border-0 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#myChannelCollapse" aria-expanded="false" aria-controls="myChannelCollapse">
                                                    <i class="fa-solid fa-caret-down"></i>
                                                </button>
                                            </h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="left-section">
                                    <div class="collapse show" id="myChannelCollapse">
                                       <ul class="myChannelList list-unstyled chat-list chat-user-list" id="myChannelList"></ul>
                                       <ul class="myExhibitorChannelList list-unstyled chat-list chat-user-list" id="myExhibitorChannelList"></ul>
                                       <ul class="mySessionChannelList list-unstyled chat-list chat-user-list" id="mySessionChannelList"></ul>
                                    </div>

                                </div>
                            </div>

                            <!-- Start chat-message-list -->
                            <div data-intro="Find Channels and start a discussion in the channel. You can create a new channel if interested." data-step="5">
                                <div class="w-hide">
                                    <div class="left-section  d-flex align-items-center px-4 mt-4 mb-2">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-0 fs-11 text-muted text-uppercase">
                                                Channels
                                                <button class="btn channel-collapse-icon shadow-none border-0 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#channelCollapse" aria-expanded="false" aria-controls="channelCollapse">
                                                    <i class="fa-solid fa-caret-down"></i>
                                                </button>
                                            </h4>
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
                                                    class="create_channel btn btn-success btn-sm">
                                                        <i class="bx bx-plus align-middle"></i>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="left-section chat-message-list mb-3">

                                    <div class="collapse show" id="channelCollapse">
                                        <div class="extra-view-scroll">
                                        <ul class="watchpartyChannel list-unstyled chat-list chat-user-list" id="watch_partyChannel"><!-- data-intro="You can also join the video call on the channel by clicking camera button." data-step="6" -->

                                        </ul>

                                        <ul class="sessionChannelList list-unstyled chat-list chat-user-list" id="sessionChannelList">

                                        </ul>

                                        <ul class="exhibitorChannelList list-unstyled chat-list chat-user-list" id="exhibitorChannelList">

                                        </ul>

                                        <ul class="sponsorChannelList list-unstyled chat-list chat-user-list" id="sponsorChannelList">

                                        </ul>

                                        <ul class="channelList list-unstyled chat-list chat-user-list w-hide" id="channelList">

                                        </ul>
                                        </div>

                                        <ul class="list-unstyled chat-list chat-user-list">
                                            <li id="browseMoreChannels" style="display: <?= !empty($browse_channel_tabs) ? 'block' : 'none'; ?>">
                                                <a class="align-items-center justify-content-between w-hide" href="javascript: void(0);">
                                                    <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden">
                                                        <!-- <i class="bx bx-search mr-1"></i> -->
                                                        <div class="flex-shrink-0 me-2">
                                                            <div class="chat-user-img online align-self-center">
                                                                <div class="avatar-xs">
                                                                    <span class="avatar-title bg-white rounded-circle border text-dark">
                                                                        <i class="bx bx-search"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <h6 class="text-truncate mb-0 channel_name text-capitalize">Browse More Channels</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>

                                    </div>

                                </div>
                            </div>
                            <div class="d-flex align-items-center px-4 mt-4 mb-2" id="dm-block">
                                <div class="flex-grow-1">
                                    <h4 class="mb-0 fs-11 text-muted text-uppercase w-hide">
                                        Direct Messages
                                        <button class="btn dm-collapse-icon shadow-none border-0 py-0" type="button" data-bs-toggle="collapse" data-bs-target="#dmCollapse" aria-expanded="false" aria-controls="dmCollapse">
                                            <i class="fa-solid fa-caret-down"></i>
                                        </button>
                                    </h4>
                                    <h4 class="mb-0 fs-11 text-muted text-uppercase w-show dm-lable">DM'S</h4>
                                </div>
                                <div class="flex-shrink-0 w-hide">
                                    <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Start chat from participants">

                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-success btn-sm" id="createDM"> <!--data-bs-toggle="modal" data-bs-target=".contactModal"-->
                                            <i class="bx bx-plus align-middle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-message-list">
                                <div class="collapse show" id="dmCollapse">
                                    <div class="extra-view-scroll" style="min-height: unset;">
                                    <ul class="dmChannelList list-unstyled chat-list chat-user-list" id="dmChannelList">

                                    </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- End chat-message-list -->
                            <?php if(ORGANIZER_CHANNEL_ENABLE && (
                                $event_owner_ptoken == $my_ptoken || in_array($my_ptoken,$owner_org_ptoken))
                                ) { ?>
                            <div class="px-4 mt-4 mb-2">
                                <div class="d-flex align-items-center mb-2" id="organizer-block d-none">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase w-hide">
                                            Organizer
                                        </h4>
                                        <h4 class="mb-0 fs-11 text-muted text-uppercase w-show">Organizer</h4>
                                    </div>
                                </div>

                                <div class="chat-message-list">
                                    <ul class="organizerChannelList_remove list-unstyled chat-list chat-user-list" id="remove_organizerChannelList"></ul>


                                        <div class="w-hide">
                                            <a target="_blank" href="<?php echo TAOH_MASTER_NETWORKING_URL; ?>" class="btn btn-primary btn-sm mt-2" id="go_to_masternetworking_room">Go to Master Networking Room</a>
                                        </div>


                                </div>
                            </div>
                            <?php } ?>

                            <?php if($event_owner_ptoken != $my_ptoken && !in_array($my_ptoken,$owner_org_ptoken)) { ?>
                            <ul class="list-unstyled chat-list chat-user-list chat_with_organizer_div d-none">
                                <li class="chat_with_organizer" data-channel_name="Organizer">
                                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="Organizer">
                                            <div class="flex-shrink-0 me-2">
                                                <div class="chat-user-img align-self-center">
                                                    <div class="avatar-xs">
                                                        <span class="avatar-title bg-white rounded-circle border text-dark">
                                                            <i class="fa align-middle fa-user" style="color: #0d6efd"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden text-truncate">
                                                <span class="mb-0 channel_name text-capitalize" style="color: #0d6efd">Chat with Organizer</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
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


            <button type="button" class="btn unread-mentions d-none">
                <span class="mr-1">
                    <svg width="20" height="20" data-no9="true" data-qa="arrow-down" aria-hidden="true" viewBox="0 0 20 20"><path fill="currentColor" fill-rule="evenodd" d="M10.75 3.75a.75.75 0 0 0-1.5 0v10.628l-3.957-4.146a.75.75 0 0 0-1.086 1.036l5.25 5.5a.75.75 0 0 0 1.085 0l5.25-5.5a.75.75 0 0 0-1.085-1.036l-3.957 4.146z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                Unread mentions
            </button>

        </div>
        <!-- end chat-leftsidebar -->

        <!-- Start User chat -->
        <div class="user-chat w-100 overflow-hidden mobile-transform" id="user-chat">

            <div class="chat-content d-lg-flex h-100">
                <!-- start chat conversation section -->
                <div class="w-100 overflow-hidden position-relative bo-lf-rt h-100">
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
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none networking-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i></a>
                                        <!-- <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".audiocallModal">Audio <i class="bx bxs-phone-call text-muted"></i></a>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-lg-none" href="#" data-bs-toggle="modal" data-bs-target=".videocallModal">Video <i class="bx bx-video text-muted"></i></a> -->
                                    </div>
                                </div>
                            </div>

                            <h4>PARTICIPANTS</h4>
                            <form data-intro="Search through participants by location, company name, role and skills to connect" data-step="2" id="searchFilter" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1" style="width: 100%; max-width: 663px">
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
                                    if($geo_enabled){
                                        ?>
                                        <div class="mb-3 zooming-section">
                                            <button id="zoom_in" type="button" onclick="zoomin()" class="p-2 zoom-buttons" title="Search people in larger 8-8"><i class="la la-plus"></i></button>
                                            <input type="hidden" name="radius" id="radius" value="<?php echo $radius; ?>" />
                                            <button id="zoom_out" type="button" onclick="zoomout()" class="p-2 zoom-buttons" title="Search people in a smaller radius"><i class="la la-minus"></i></button>
                                        </div>
                                    <?php }  ?>
                                </div>
                            </form>

                            <div id='loaderArea-participant' class="text-center"></div>

                            <div class="list-scroll-onmobile" id="networkArea-participant"></div>
                        </div>
                    </div>

                    <div id="browse_channels_wrapper" class="position-relative" style="display: none; max-width: 1200px;">
                        <div class="px-4 py-3 pb-xl-0">
                            <div class="d-flex justify-content-between d-xl-none">
                                <div class="flex-shrink-0 mr-3 mb-3">
                                    <a href="javascript: void(0);" class="btn-primary user-chat-remove fs-18 p-1">
                                        <i class="bx bx-chevron-left align-middle text-white"></i>
                                    </a>
                                </div>

                                <div class="dropdown user-chat-nav">
                                    <button class="btn nav-btn dropdown-toggle after-none border-0" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item d-flex justify-content-between align-items-center d-xl-none networking-sidebar-show" href="#">More info <i class="bx bx-info-circle text-muted"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 id="browse_channel_heading" class="ch-main-title">All Channels</h4>

                                <div class="d-flex align-items-center gap-1">
                                    <?php if($create_channel_flag == 1) { ?>
                                        <button class="btn btn-primary create_channel" id="createChannelBtn">Create Channel</button>
                                    <?php } ?>
<!--                                    <a class="btn"><i class="fa-solid fa-angles-left"></i> Back</a>-->
                                    <!-- <button class="btn btn-secondary" id="browse_channels_filter_btn">Filter</button>-->
                                </div>
                            </div>


                            <!-- channel listing with filter start -->
                            <div>
                                <form method="post" id="browseChannelFilterForm" name="browseChannelFilterForm" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1 mb-2">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="d-flex flex-wrap align-items-center flex-grow-1">
                                            <div class="form-group flex-grow-1 mb-2">
                                                <input name="browseChannelQuery" class="form-control pr-40px mb-0" type="text" id="browseChannelQuery" placeholder="Search for channels">
                                                <span class="la la-search input-icon" style="left:unset; right: 20px;"></span>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <ul class="nav nav-tabs flex-wrap align-items-center mb-2 border-0" style="gap: 10px;" id="browseChannelTab" role="tablist">
                                    <?php
                                    foreach($browse_channel_tabs as $tab) {
                                        echo '<li class="nav-item">
                                                <a class="nav-link btn bor-btn py-2 ' . ($browse_channel_first_tab_slug === $tab['slug'] ? 'active' : '') . '" id="'.$tab['slug'].'-tab" data-toggle="tab" data-bc_slug="'.$tab['slug'].'" href="#'.$tab['slug'].'" role="tab">
                                                    '.$tab['name'].'
                                                </a>
                                            </li>';
                                    }
                                    ?>
                                </ul>

                                 <div class="all-channels tab-content pt-3 px-0" id="myTabContent">
                                     <?php
                                     foreach($browse_channel_tabs as $tab) {
                                         echo '<div class="tab-pane fade ' . ($browse_channel_first_tab_slug === $tab['slug'] ? 'show active' : '') . '" id="'.$tab['slug'].'" data-bcc_slug="'.$tab['slug'].'" role="tabpanel">
                                                <div class="'.$tab['slug'].'-lists channel-lists list-scroll-onmobile aw aw-logo" id="browse_'.$tab['slug'].'_channels">

                                                </div>
                                            </div>';
                                     }
                                     ?>
                                </div>
                            </div>
                            <!-- channel listing with filter end -->

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
                                                            # <a href="#" class="text-capitalize sn_channel_title text-reset text-truncate"></a>
                                                        </h6>
                                                        <span class="sn_channel_description fs-14 text-muted lh-1 py-1"></span>
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
                                                    <div class="dropdown-menu dropdown-menu-end">
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

                                <div id="contentCarousel" class="carousel slide speed-carousel px-4 px-md-5 py-4">
                                    <!-- Carousel inner content -->
                                    <div class="carousel-inner speed_networking_carousel"></div>
                                    <!-- Controls (arrows) -->
                                    <button class="carousel-control-prev" type="button" data-bs-target="#contentCarousel" data-bs-slide="prev">
                                        <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 11C22 13.9174 20.8411 16.7153 18.7782 18.7782C16.7153 20.8411 13.9174 22 11 22C8.08262 22 5.28473 20.8411 3.22182 18.7782C1.15892 16.7153 0 13.9174 0 11C0 8.08262 1.15892 5.28473 3.22182 3.22183C5.28473 1.15893 8.08262 0 11 0C13.9174 0 16.7153 1.15893 18.7782 3.22183C20.8411 5.28473 22 8.08262 22 11ZM11.6445 16.1992C12.0484 16.6031 12.7016 16.6031 13.1012 16.1992C13.5008 15.7953 13.5051 15.1422 13.1012 14.7426L9.36289 11.0043L13.1012 7.26602C13.5051 6.86211 13.5051 6.20898 13.1012 5.80937C12.6973 5.40977 12.0441 5.40547 11.6445 5.80937L7.17578 10.2695C6.77188 10.6734 6.77188 11.3266 7.17578 11.7262L11.6445 16.1992Z" fill="#555555"/>
                                        </svg>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#contentCarousel" data-bs-slide="next">
                                        <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                        <h1 class="count countDownTimer1">60</h1>
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
                                <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center position-relative">

                                    <button type="button" class="btn btn-danger ab-top-right d-none">X</button>

                                    <h6 id="successMatchDivHeading1" class="sm-text fs-24 text-center">Hurray! <span class="user_title"></span> has accepted your invitation and is currently waiting in the video room.</h6>

                                    <h6 id="successMatchDivHeading2" class="sm-text fs-24 text-center d-none">Hurray! You’ve accepted the invitation. You can now join the video room.</h6>


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

                                    <p class="sm-text text-center mb-5 d-none"><span class="user_title1"></span> has accepted your Networking Request !</p>

                                    <button type="button" class="btn speed-v1-b-btn openchatacc_chat_now_btn">Join Now!</button>

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

			<!-- accept  start -->
                            <div class="notAvailableDiv container pt-4 px-lg-4 d-none">
                                <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center position-relative">
                                    <button type="button" class="btn btn-danger ab-top-right d-none">X</button>
                                    <h6 class="sm-text fs-24 text-center"><span class="user_title"></span> seems to be busy, connect with someone else!</h6>

                                    <p class="sm-text text-center mb-5 d-none"><span class="user_title"></span> has accepted your Networking Request !</p>

                                    <button type="button" class="btn speed-v1-b-btn browse_participants_btn mt-5">Browse Participants</button>

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
                            <div class="speed-nw-card p-3 p-lg-5 d-flex flex-column align-items-center position-relative">
                                <button type="button" class="btn btn-danger ab-top-right d-none">X</button>
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
                                        <div id="user-chat-topbar-info" class="flex-grow-1 overflow-hidden">
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
                                                <div class="dropdown-menu dropdown-menu-end">
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
                            <div class="pin-message-v2-dm" style="display: none">
                                <h6 class="d-flex align-items-center pin-m-header" style="gap: 4px;">
                                    <div class="flex-grow-1">
                                        <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.835171 0.875C0.835171 0.391016 1.20743 0 1.6682 0H8.3324C8.79317 0 9.16542 0.391016 9.16542 0.875C9.16542 1.35898 8.79317 1.75 8.3324 1.75H7.56445L7.86122 5.80234C8.81659 6.34648 9.57152 7.25703 9.93077 8.3918L9.9568 8.47383C10.0427 8.7418 9.99845 9.03438 9.84226 9.26133C9.68606 9.48828 9.43355 9.625 9.16542 9.625H0.835171C0.567041 9.625 0.317134 9.49102 0.158338 9.26133C-0.000457284 9.03164 -0.0421086 8.73906 0.0437972 8.47383L0.0698292 8.3918C0.429071 7.25703 1.184 6.34648 2.13938 5.80234L2.43614 1.75H1.6682C1.20743 1.75 0.835171 1.35898 0.835171 0.875ZM4.16727 10.5H5.83332V13.125C5.83332 13.609 5.46106 14 5.0003 14C4.53953 14 4.16727 13.609 4.16727 13.125V10.5Z" fill="#323232"/>
                                        </svg>
                                        Pinned Messages
                                    </div>
                                    <span class="cursor-pointer" id="arrowContainer_dm">
                                        <svg class="d-flex downArrow_dm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9" />
                                        </svg>
                                        <svg class="d-none upArrow_dm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
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
                        if ($isEventNetworking) {
//                            $watchPartyEnabledChannel = 1;
                        ?>
                        <!-- watch party -->
                        <div class="watch-party watchPartySection flex-grow-1 video-section" style="display:none; border-right: 1px solid #d3d3d3;">
                            <div class="card h-100">
                                <div class="card-header wp-head-h" id="headingOne">
                                    <h5 class="mb-0 watch-party-welcome py-3 d-flex align-items-center justify-content-between">
                                        Welcome to Presentation Room!
                                    </h5>
                                </div>

                               <div class="card-body video-container d-flex align-items-center">
                                   <iframe width="100%" height="315" id="watchpartyiframe" src="<?= $streaming_link; ?>"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    <div id="watchPartyMeetingLinkDiv"
                                        style="width: 100%; height: 315px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                                        <div style="font-size: 30px; font-weight: 600;">Join the Session when LIVE.</div>
                                        <br><br>
                                        <a id="watchPartyMeetingLink"
                                        target="_blank"
                                        href=""
                                        class="btn btn-primary btn-lg"
                                        style="font-size: 24px; padding: 16px 40px; border-radius: 8px;">
                                        <i class="fas fa-video"></i> Join the Session
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                        <!-- conversation group -->
                        <div id="channel-chat" class="position-relative flex-grow-1 watchPartySection" data-channel_id="" data-channel_type="" style="display: none;">
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
                                                    <!--<div class="cw_channel_icon flex-shrink-0 chat-user-img online user-own-img align-self-center me-3">
                                                        <div class="avatar-xs">
                                                            <span class="avatar-title rounded-circle bg-primary text-white">
                                                                <span class="username"></span>
                                                            </span>
                                                        </div>
                                                    </div>-->
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h6 class="d-flex align-items-center mb-0 fs-18" style="gap: 6px;">

                                                            <div class="d-flex align-items-center">
                                                                <img alt="" class="avatar-sm rounded-circle me-3 cw_channel_profile_img">
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <h6 class="text-truncate mb-0 cw_channel_title text-capitalize"></h6>
                                                                    <p class="text-truncate mb-0 cw_channel_sub_title" style="font-size: 12px;"></p>
                                                                    <p class="text-truncate mb-0 cw_channel_sub_title1" style="font-size: 12px;"></p>
                                                                </div>
                                                            </div>
                                                            <button type="button" toggle_text="open" class="channel_toggle btn box-shadow-none p-0 d-none">
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
                                                <li class="list-inline-item d-none me-2 ms-0" id="channel_video_btn">
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

                                                <li class="list-inline-item me-2 ms-0 load_more_dots">
                                                    <div class="dropdown">
                                                        <button class="btn nav-btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="bx bx-dots-vertical-rounded"></i>
                                                        </button>

                                                        <div style="width:200px;" class="dropdown-menu dropdown-menu-end">
                                                            <div class="taoh-loader taoh-spinner" id="loader_transcript" style="width:20px;height:20px;display:none;"></div>

                                                            <button type="button" class="dropdown-item d-flex align-items-center justify-content-between send_transcript_btn" data-channelid="" data-channeltype="" data-channelname="">Send Transcript
                                                                <img data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Click this icon to get the chat conversation via mail"
                                                                    src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/email_fwd.png" class="send_transcript" style="width:30px;height:30px;" />
                                                            </button>

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
                                                        <div class="dropdown-menu dropdown-menu-end">
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

                                <div class="pin-message-v2" style="display: none">
                                    <h6 class="d-flex align-items-center pin-m-header" style="gap: 4px;">
                                        <div class="flex-grow-1">
                                            <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0.835171 0.875C0.835171 0.391016 1.20743 0 1.6682 0H8.3324C8.79317 0 9.16542 0.391016 9.16542 0.875C9.16542 1.35898 8.79317 1.75 8.3324 1.75H7.56445L7.86122 5.80234C8.81659 6.34648 9.57152 7.25703 9.93077 8.3918L9.9568 8.47383C10.0427 8.7418 9.99845 9.03438 9.84226 9.26133C9.68606 9.48828 9.43355 9.625 9.16542 9.625H0.835171C0.567041 9.625 0.317134 9.49102 0.158338 9.26133C-0.000457284 9.03164 -0.0421086 8.73906 0.0437972 8.47383L0.0698292 8.3918C0.429071 7.25703 1.184 6.34648 2.13938 5.80234L2.43614 1.75H1.6682C1.20743 1.75 0.835171 1.35898 0.835171 0.875ZM4.16727 10.5H5.83332V13.125C5.83332 13.609 5.46106 14 5.0003 14C4.53953 14 4.16727 13.609 4.16727 13.125V10.5Z" fill="#323232"/>
                                            </svg>
                                            Pinned Messages
                                        </div>
                                        <span class="cursor-pointer arrowContainer">
                                            <svg class="d-flex downArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9" />
                                            </svg>
                                            <svg class="d-none upArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15" /></svg>
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


                    <div class="d-flex chat-input-bottom">
                        <div class="input-left-empty watchPartySection" style="display: none;"></div>
                        <!-- start chat input section -->
                        <div id="chat-input-container" class="position-relative flex-grow-1" style="display: none;">
                            <div class="chat-input-section p-4 border-top border-left">

                                <!-- <form id="chatForm" enctype="multipart/form-data"> -->
                                    <div class="row g-0 align-items-center">
                                        <div class="file_Upload"></div>
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
                                        <div class="col-auto">
                                            <div class="chat-input-links ms-md-3">
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
                                    </div>
                                <!-- </form> -->
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
                            <img style="width: 100%; max-width: 300px;" src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/images/no-chat.svg" class="no_comments_img" alt="No comments">
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
                        <div id="chat-reply-input-container" class="chat-input-bottom" style="z-index: 999;">
                            <div class="chat-reply-input-section p-4 border-top border-left">
                                <!-- <form id="chatReplyForm" enctype="multipart/form-data"> -->
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
                                                <input autocomplete="off" type="text" name="chat_reply_input" class="form-control bg-light border-0 chat-reply-input mb-0" autofocus id="chat_reply_input" placeholder="Type your message...">
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
                                <!-- </form> -->
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
                                <div class="card">
                                    <div class="card-body py-2 px-2">
                                        <ul class="p-0 mb-0" id="activities-list2">
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
                            <button type="button" class="btn bor-btn" data-toggle="modal" data-target="<?=($room_app == 'live_now') ? '#live_now_modal' : '#agreeModal'?>">Watch Video</button>
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

                        <div class="card border-0 mb-3 d-none" id="activities_block">
                            <div class="card-header bg-white py-3" style="cursor: unset;">
                                    <!-- <p class="fs-12 fw-400 text-black mb-0">Here is what happening !</p> -->
                                    <h5 class="fs-19 fw-400 text-black mb-0">Recent Activity</h5>
                                </div>
                            <div class="card-body">
                                <!-- video rooms card -->


                                <div class="card">
                                    <div class="card-body py-2 px-2">
                                        <ul class="p-0 mb-0" id="activities-list">
                                            <div class="taoh-loader taoh-spinner d-block show" id="pc_loader_activities" style="width:50px;height:50px;display:block;"></div>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card my-3 mx-2 d-none" id="activities_block1-main">
                            <div class="card-header bg-white" style="cursor: unset;">
                                <p class="fs-12 fw-400 text-black mb-0">Here is what happening !</p>
                                <h5 class="fs-19 fw-400 text-black mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                 <!-- video rooms card -->
                             <div class="video-room-activity-main d-flex align-items-start" style="gap: 12px;">
                                <div  class="taoh-loader taoh-spinner d-block show" id="pc_loader_room_activities1" style="width:50px;height:50px;display:block;" ></div>
                            </div>
                                <div class="card">
                                    <div class="card-body py-2 px-2">
                                        <ul class="p-0 mb-0" id="activities-list1"></ul>
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
                        if ($isEventNetworking) {
                            echo taoh_sponsor_slider_widget($eventtoken);
                        }

                        if(false && $isEventNetworking && isset($event_rsvp_data)) {
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

                         <!-- <div class="card card-item p-1 light-dark-card" >
                           <div class="card-body upcoming_events" id="upcoming_events">
                                <h3 class="fs-17">Upcoming Events</h3>
                                <div class="divider"><span></span></div>

                            </div>
                        </div> -->

                        <div class="events-v1-widget mb-4 d-none">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3>Upcoming Events</h3>
                                <a class="btn bor-btn" href="<?php echo TAOH_SITE_URL_ROOT.'/events';?>">View all Events</a>
                            </div>
                            <div class="divider mb-0"><span></span></div>
                            <?php if (function_exists('taoh_recent_multiple_event_widget')) {
                                taoh_recent_multiple_event_widget($eventtoken);
                            } ?>
                        </div>

                        <?php if(isset($org_video_message_link) && !empty($org_video_message_link)) { ?>
                        <div class="card card-item p-1 light-dark-card" >
                            <div class="card-body">
                                <h3 class="fs-17">Organizer Video Message</h3>
                                <div class="divider"><span></span></div>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="<?php echo convertEmbedSrc($org_video_message_link); ?>" allowfullscreen style="border:none;"></iframe>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

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
                    <?php
                    if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget($club_info['title'] ?? '','networking',TAOH_NETWORK_REFERRAL_URL);
                    } ?>
                    <?php
                    // if (function_exists('taoh_get_recent_jobs')) {
                    //     taoh_get_recent_jobs('new');
                    // }
                    ?>

                    </div>
                </div>
                 <!-- END participants sidebar -->

            </div>
            <!-- end user chat content -->
        </div>
        <!-- End User chat -->
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
                                        <span class="user_companyName"></span>
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

        <!--connect request modal start-->
        <div class="modal fade speed-req" id="wakeupModal" tabindex="-1" aria-labelledby="wakeupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-white">
                        <h5 class="modal-title sm-text" id="wakeupModalLabel">Session Paused</h5>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex" style="gap: 21px;">
                            <div class="d-flex align-items-center mt-1" style="gap: 6px;">
                                It looks like you haven’t interacted for a while.<br><br>
                                To continue your session and resume updates, please click the button below.
                            </div>
                        </div>
                        <div class="d-flex" style="gap: 21px;">
                            <div class="d-flex align-items-center mt-5" style="gap: 6px;">
                                <button type="button" class="btn std-btn continue_btn">Continue</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start add group Modal -->
        <div class="modal fade" id="createChannelModal" tabindex="-1" role="dialog" aria-labelledby="createChannelModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content modal-header-colored border-0">
                     <form action="<?= taoh_site_ajax_url(); ?>" method="post" name="createChannelForm" id="createChannelForm">
                        <div class="modal-header">
                            <h5 class="modal-title fs-16" id="createChannelModalLabel">Create New Channel</h5>
                            <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" name="channeltype" class="form-control" id="channelType" value="1">

                            <div class="mb-3">
                                <label for="channelName" class="form-label">Channel Name</label>
                                <input type="text" name="channelname" class="form-control alphanumericInput" id="channelName" placeholder="Enter Channel Name" autocomplete="off" required>
                            </div>

                            <div class="mb-3">
                                <label for="channelDescription" class="form-label">Description</label>
                                <textarea name="channeldescription" class="form-control" id="channelDescription" rows="3" placeholder="Enter Short Description" autocomplete="off" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="channelVisibilityPublic" class="form-label">Visibility</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channelvisibility" id="channelVisibilityPublic" value="public" checked>
                                    <label class="form-check-label" for="channelVisibilityPublic">Public - anyone</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="channelvisibility" id="channelVisibilityPrivate" value="private">
                                    <label class="form-check-label" for="channelVisibilityPrivate">Private - only specific people</label>
                                </div>
                            </div>

                            <p class="d-inline-flex gap-1">
                   <!--class="d-none"--> <a data-bs-toggle="collapse" href="#createChannelMoreOptions" role="button" aria-expanded="false" aria-controls="createChannelMoreOptions">
                                    <i class="bx bx-plus"></i> <span class="toggle-text">Show More Options</span>
                                </a>
                            </p>
                            <div class="collapse" id="createChannelMoreOptions">
                                <div>
                                    <div class="mb-4">
                                        <i class="fa fa-lock text-secondary mr-1" aria-hidden="true"></i> <label for="channelpasscode" class="form-label">Channel Passcode</label>
                                        <input type="text" name="channelpasscode" class="form-control" id="channelpasscode" autocomplete="off" maxlength="10" placeholder="Passcode if needed">
                                    </div>
                                    <?php
                                    if($isEventNetworking && !empty($ticket_types_array)) { ?>
                                        <div class="mb-4 ">
                                            <svg class="mr-1" width="19" height="11" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.11111 0C0.946701 0 0 0.946701 0 2.11111V4.22222C0 4.5125 0.244097 4.7401 0.517882 4.83576C1.13802 5.05017 1.58333 5.64062 1.58333 6.33333C1.58333 7.02604 1.13802 7.61649 0.517882 7.8309C0.244097 7.92656 0 8.15417 0 8.44444V10.5556C0 11.72 0.946701 12.6667 2.11111 12.6667H16.8889C18.0533 12.6667 19 11.72 19 10.5556V8.44444C19 8.15417 18.7559 7.92656 18.4821 7.8309C17.862 7.61649 17.4167 7.02604 17.4167 6.33333C17.4167 5.64062 17.862 5.05017 18.4821 4.83576C18.7559 4.7401 19 4.5125 19 4.22222V2.11111C19 0.946701 18.0533 0 16.8889 0H2.11111ZM4.22222 3.69444V8.97222C4.22222 9.2625 4.45972 9.5 4.75 9.5H14.25C14.5403 9.5 14.7778 9.2625 14.7778 8.97222V3.69444C14.7778 3.40417 14.5403 3.16667 14.25 3.16667H4.75C4.45972 3.16667 4.22222 3.40417 4.22222 3.69444ZM3.16667 3.16667C3.16667 2.58281 3.63837 2.11111 4.22222 2.11111H14.7778C15.3616 2.11111 15.8333 2.58281 15.8333 3.16667V9.5C15.8333 10.0839 15.3616 10.5556 14.7778 10.5556H4.22222C3.63837 10.5556 3.16667 10.0839 3.16667 9.5V3.16667Z" fill="#636161"></path>
                                                </svg> <label for="channel_ticket_type" class="form-label">Channel Ticket Type</label><br>

                                            <select name="channel_ticket_type" class="form-control" id="channel_ticket_type">
                                                <option value="" selected>All</option>
                                                <?php
                                                foreach ($ticket_types_array as $key => $value) {  ?>
                                                    <option value="<?php echo $value['slug'];?>"><?php echo $value['title'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                            <span class="fs-10">(Users who registered with this ticket type can only access the channel)</span>
                                        </div>
                                    <?php } ?>
                                </div>
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
                        </div>
                        <div class="modal-footer border-top-dashed px-4">
                           <button type="submit" id="saveChannelBtn" class="btn btn-primary m-0"><i class="fa fa-play-circle-o mr-2" aria-hidden="true"></i> Create Channel</button>
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
                    <form action="#" method="post" id="channel_password_form">
                        <input type="hidden" name="channel_password_channel_id" id="channel_password_channel_id" value="">
                        <input type="hidden" name="channel_password_channel_type" id="channel_password_channel_type" value="">
                        <input type="hidden" name="channel_encrypted_passcode" id="channel_encrypted_passcode" value="">
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
                                <input type="text" placeholder="Enter the passcode" id="passcode" maxlength="10" class="form-control" autocomplete="off" required />
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

                        <a class="btn agree-btn mx-auto mb-3 text-white">I Agree - Join Networking</a>
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

                        <p class="fs-10 fw-400 ml-auto mt-2 mb-0 d-none" style="color: #787272; width: fit-content;">By creating a room, you agree to our Terms & Conditions</p>
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
                        <input type="hidden" id="delete_channel_type" />
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
                                    if(empty($ch['name'])) continue;
                                    if (strpos($ch['name'], '#') !== false) {
                                        echo ' → #' . ucwords(ltrim($ch['name'], '#')).'<br>';
                                    } else {
                                        echo ' → ' .ucwords($ch['name']).'<br>';
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
                                            <div class="position-relative mood_status_other_div mt-3">
                                                <form style="width: 100%;">
                                                    <button type="button" onclick="showEmoji()" id="selected-emoji" class="emoji-place selected-emoji">
                                                        <img class="emoji-place" src="<?php echo TAOH_CDN_MAIN_PREFIX.'/images/emojis/default.svg'; ?>" />
                                                    </button>
                                                    <input type="text" maxlength="140" placeholder="Say something"  class="current_status" id="current_status" name="current_status"/>
                                                    <input type="hidden" name="choosen_emoji" id="choosen_emoji" value=""/>
                                                    <button type="reset" id="not-empty" class="not-empty" onclick="removeEmoji();"><i class="la la-close"></i></button>
                                                </form>
                                            </div>
                                            <span class="mood_status_error2 text-danger"></span>
                                        </div>
                                        <div class="emoji_section emoji-place" id="emoji_section" style="display:none;">
                                            <div class="fs-11 text-dark emoji-inner-section d-flex flex-wrap" style="scrollbar-width: thin;">
                                                <?php for ($i = 1; $i <= 100; $i++) { ?>
                                                    <button type="button" onclick="chooseEmoji(<?php echo $i; ?>);" class="emoji-icon" style="position: unset;">
                                                        <img src="<?php echo TAOH_CDN_MAIN_PREFIX . '/images/emojis/' . $i . '.svg'; ?>"/>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <span class="fs-14 fw-bold mood_status_other_div">You can choose from below list also :</span>
                                        <div class="mood_status_other_div_span fs-15 mt-1 mb-4 frequent-notes text=-right">
                                            <span onclick="copyToStatus('Exploring career opportunities');"> * Exploring career opportunities</span>
                                            <span onclick="copyToStatus('Career Networking');"> * Career Networking</span>
                                            <span onclick="copyToStatus('Recruiting or hiring talent');"> * Recruiting or hiring talent</span>
                                            <span onclick="copyToStatus('Showcasing a product/service');"> * Showcasing a product/service</span>
                                            <span onclick="copyToStatus('Other');"> * Other</span>
                                        </div>
                                        <div class="fixed-footer-btm text-right">
                                            <button onclick="saveStatus();" type="button" class="btn btn-primary fw-medium">Save</button>
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
        <!-- members modal -->
        <div class="modal fade" id="membersModal" tabindex="-1" aria-labelledby="membersModalLabel" aria-hidden="true" data-channelid="" data-channeltype="">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header pb-0 px-lg-5">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="modal-title d-flex align-items-center gap-3" id="membersModalLabel">
                                    <span class="channel_title text-capitalize"></span>
                                    <span class="membersModalStarChannelWrapper d-none">
                                        <span class="membersModalChannelStar star_channel"><i class="fa fa-star-o" aria-hidden="true"></i></span>
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <ul class="nav nav-tabs" id="membersTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ab-channel-tab" data-bs-toggle="tab" data-bs-target="#about-channel" type="button" role="tab">
                                        About Channel
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button" role="tab">
                                        Members
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-body px-lg-5 pb-4">
                        <div class="tab-content pt-3" id="membersTabContent">
                            <div class="tab-pane fade show active" id="about-channel" role="tabpanel">
                                <div class="channel_description_wrapper">
                                    <h6 class="text-md">Description</h6>
                                    <p class="channel_description text-xs"></p>
                                </div>

                                <hr>

                                <div class="channel_created_by_wrapper">
                                    <h6 class="text-md">Created By</h6>
                                    <p class="channel_created_by text-xs"></p>
                                </div>

                                <div class="mt-4 d-none">
                                    <a href="#" class="btn btn-danger px-4 py-2">Leave Channel</a>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="members" role="tabpanel">
                                <div class="d-flex justify-content-end mb-4">
                                    <div class="d-flex align-items-center add_members_wrapper add_channel_members cursor-pointer btn btn-primary"
                                        data-channelid="" data-channeltype="">
                                        <div class="flex-shrink-0">
                                            <i class="fa fa-user-plus" aria-hidden="true"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            Add Members
                                        </div>
                                    </div>
                                </div>
                                <div class="members-lists" id="membersList"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- members modal end -->

        <!-- Add Members Modal -->
        <div class="modal fade" id="addChannelMembersModal" tabindex="-1" aria-labelledby="addChannelMembersModalLabel" aria-hidden="true" data-channelid="" data-channeltype="">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header px-lg-4">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="modal-title d-flex align-items-center font-weight-normal gap-3" id="addChannelMembersModalLabel">
                                    <span class="channel_title text-capitalize">Add Members to channel</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-body px-lg-4">
                        <form action="#" method="post" name="membersToAddForm" id="membersToAddForm">
                            <input type="hidden" name="channel_id" value="">
                            <input type="hidden" name="channel_type" value="">
                            <div>
                                <select class="form-control form-control-lg" name="members_to_add[]" id="members_to_add" multiple="multiple" placeholder="eg. John" required>

                                </select>
                            </div>

                            <div class="mt-4 d-flex justify-content-end gap-3">
                                <button type="submit" class="btn btn-primary px-4 py-2 add_channel_members_button">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Add Members Modal -->

    </div>
    <!-- end  layout wrapper -->
<?php
$show_finish_setup_link = 0;
$data_encode = json_encode(taoh_user_all_info());
$data = json_decode($data_encode, true);

// if (!isset($data['profile_stage']) || (isset($data['profile_stage']) && $data['profile_stage'] < 2)) {
//     $show_finish_setup_link = 1;
// }

if (isset($data['profile_stage']) && $data['profile_stage'] < 2) {
    $show_finish_setup_link = 1;
}

$chatwithchannelid = $chatwithchanneltype = "";

if (isset($_GET['chatwithchannelid']) && !empty($_GET['chatwithchannelid']) && isset($_GET['chatwithchanneltype']) && !empty($_GET['chatwithchanneltype'])) {
    $chatwithchannelid = $_GET['chatwithchannelid'];
    $chatwithchanneltype = $_GET['chatwithchanneltype'];
}

$is_event_owner = ($event_owner_ptoken == $my_ptoken);
?>


    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/simplebar/simplebar.min.js"></script>
    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/node-waves/waves.min.js"></script>
    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/swiper/swiper-bundle.min.js"></script>

    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/glightbox/js/glightbox.min.js"></script>
    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/fg-emoji-picker/fgEmojiPicker.js"></script>

    <script src="<?php echo TAOH_CDN_MAIN_PREFIX; ?>/libs/chat4/tributejs/tribute.min.js"></script>

    <script type="text/javascript">
        let isLoggedIn = "<?= taoh_user_is_logged_in(); ?>";

        let userInfoChatName = "<?= $user_info_obj->chat_name; ?>";

        let pagename = "<?=$pagename ?>";

        let taohChannelDefault = '<?= defined('TAOH_CHANNEL_DEFAULT') ? TAOH_CHANNEL_DEFAULT : 1; ?>';
        let taohChannelExhibitor = '<?= defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : 2; ?>';
        let taohChannelSponsor = '<?= defined('TAOH_CHANNEL_SPONSOR') ? TAOH_CHANNEL_SPONSOR : 3; ?>';
        let taohChannelDm = '<?= defined('TAOH_CHANNEL_DM') ? TAOH_CHANNEL_DM : 4; ?>';
        let taohChannelOrganizer = '<?= defined('TAOH_CHANNEL_ORG_DM') ? TAOH_CHANNEL_ORG_DM : 5; ?>';
        let taohChannelSpeedNtw = '<?= defined('TAOH_CHANNEL_SPEED_NTW') ? TAOH_CHANNEL_SPEED_NTW : 6; ?>';
        let taohChannelSession = '<?= defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : 7; ?>';
        //let watchPartyEnabledChannel = '<?//= $watchPartyEnabledChannel; ?>//';

        let getChatWith = "<?=(isset($_GET['chatwith'])) ? $_GET['chatwith'] : '' ?>";

        let _ORGANIZER_CHANNEL_ENABLE = <?= (defined('ORGANIZER_CHANNEL_ENABLE') && ORGANIZER_CHANNEL_ENABLE) ? 'true' : 'false'; ?>;

        let my_pToken = '<?= $ptoken ?? ''; ?>';
        let ntw_room_key = '<?= $keytoken ?? ''; ?>';
        let layout = '<?php echo $ntw_view; ?>'; //1-participants,2- (1-1) chat window
        let ntw_keyword = '<?php echo !empty($keyword) ? $keyword : 'club'; ?>';
        let eventToken = '<?php echo !empty($eventtoken) ? $eventtoken : ''; ?>';
        let eventTitle = <?php echo json_encode($event_title ?? ''); ?>;
        let room_title = '<?= $room_title ?? ''; ?>';
        let rsvpSlug = '<?php echo !empty($rsvp_slug) ? $rsvp_slug : ''; ?>';
        let event_organizer_ptokens = JSON.parse(`<?= json_encode(($owner_org_ptoken ?? [])); ?>`);
        let event_owner_ptoken = '<?php echo !empty($event_owner_ptoken) ? $event_owner_ptoken : ''; ?>';
        let _can_delete_all_msg = '<?php echo $can_delete_all_msg; ?>';
        let chatwithchannelprocessed = 0;
        let chatwithchannelid = '<?= $chatwithchannelid ?>';
        let chatwithchanneltype = '<?= $chatwithchanneltype ?>';

        let my_following_ptoken_list = JSON.parse(`<?= json_encode(($my_following_ptoken_list ?? [])); ?>`);

        let ticketInfo = <?php echo json_encode($current_ticket_type ?? []); ?>;

        let browse_channel_tabs = JSON.parse(`<?= json_encode(($browse_channel_tabs ?? [])); ?>`);

        const items = ['role', 'location','skill'];
        var random = items[Math.floor(Math.random() * items.length)];

        let ntwuserInfoList = {};
        let userStatusArray = {};
        var mentionUserArray = {};
        let tempUserChannelArray = [];

        var suggestionUsersOnRoleArray = [];
        var suggestionUsersOnLocationArray = [];
        var suggestionUsersOnSkillArray = [];
        var suggestionUsers = [];
        var videos_act = [];

        let _settings_url = '<?php echo TAOH_SITE_URL_ROOT . '/settings' ?>';

        let liveonly = 0;
        let userLiveStatusInterval = 60000; // 1 minute
        let ntwEntriesETag = null;
        let maxradius = 50000;
        let minradius = 100;
        let opt_search = 0;
        let selectedChat = '';
        let chatWindow = 'participants'; // channel, direct_message, participants

        let chatname = "<?php echo $_GET['with'] ?? ''; ?>";

        let show_finish_setup_link = <?php echo $show_finish_setup_link; ?>;
        let sidekick_ptoken = '<?php echo $sidekick_ptoken ?? ''; ?>';

        let loaderArea = $('#loaderArea-participant');
        let networkArea = $('#networkArea-participant');
        const chatInputContainer = $('#chat-input-container');
        const browseChannelsContainer = $('#browse_channels_wrapper');

        var speedNetworkArea = $('#speed_networking-participant');

        var currentFullPath = window.location.origin + window.location.pathname;
        var root_url = '<?php echo TAOH_SITE_URL_ROOT ?>';
        currentFullPath = currentFullPath.replace(root_url, "");

        var win_height = $(window).height();
        var loaderAreaSp = $('.loaderArea');

        var taoh_secret = '<?php echo TAOH_API_SECRET ?>';

        var channelArrayPollStatus = [];

        let dmNotifyBlock = false;

        var intervalId1;
        var intervalId2;
        var speed_networking_last_timestamp = 0;

        var lastSendMsgChannel = 0;
        // let ntw_entries_cleared = 1;
        let ntw_tour_version = '<?php echo defined("TAOH_NETWORK_TOUR_VERSION") ? TAOH_NETWORK_TOUR_VERSION : 1; ?>';

        let idleTimer = null;
        let pollingInterval = null;
        const idleLimit = 12000000; //120000
        let stopChannelUpdate = false;

        var user_timezone;

        if (isLoggedIn) {
            user_timezone = '<?= taoh_user_timezone(); ?>';
        }
        if (!isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

        var is_event_owner = <?= $is_event_owner ? 'true' : 'false' ?>;

        var _taoh_cdn_main_prefix = '<?php echo TAOH_CDN_MAIN_PREFIX; ?>';

        var _taoh_chat_net_url = '<?php echo TAOH_CHAT_NET_URL; ?>';

        var ticket_types = <?php echo json_encode($ticket_types ?? []); ?>;

        var channelOpenTimer = null;  // Add at top of file


    </script>

    <!-- <script src="<?php echo TAOH_CDN_JS_PREFIX; ?>/chat4/chat-common.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script> -->
    <script src="<?php echo TAOH_CHAT_PREFIX; ?>/js/chat4/chat-common.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            if (layout == 1) {
                loadchatWindow('participants');
            }

            taoh_network_update_online();

            taoh_load_network_entries();

            init_speedneworking();
            taoh_get_activities();

            clearInterval(livenowbuttonInterval);

            $('.cover-workcongress-image').addClass('d-none');

            // Render User Group Channels
            getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDefault, my_pToken})
                .then(({response}) => {
                if (response && response.channels?.length) {
                    renderChannelList(response.channels);
                }
            });


            getNTWUserChannels({
                roomslug,
                keyword: ntw_keyword,
                type: taohChannelSpeedNtw,
                my_pToken
            }).then(({response}) => {
                if (response && response.channels?.length) {
                    renderSpeedChannelList(response.channels);
                }
            });

            getNTWUserChannels({
                roomslug,
                keyword: ntw_keyword,
                type: taohChannelDm,
                my_pToken
            }).then(({response}) => {
                renderDMChannelList(response.channels);
            });

            if(getChatWith != "") {
                loadDmWindow(getChatWith);
            }


            const ajaxPost = (url, data) =>
                new Promise((resolve, reject) => {
                    $.ajax({
                        url, type: 'post', dataType: 'json', data,
                        success: (res) => resolve(res),
                        error: (xhr, status, err) => reject(new Error(err || status))
                    });
                });

            if (!is_event_owner && _ORGANIZER_CHANNEL_ENABLE) {

                async function createOrganizerChannel(to_ptoken, onSuccess, onError) {
                    try {
                        const input = [roomslug, my_pToken, to_ptoken, 'organizer'].sort().join('_');
                        const channelId = await generateSecureSlug(input, 16);

                        const channelData = JSON.stringify({
                            name: `Organizer-${my_pToken}-${to_ptoken}`,
                            description: 'Chat with the organizer of this event.',
                            ptoken: my_pToken
                        });

                        const channelMembers = JSON.stringify([my_pToken, to_ptoken]);
                        const payload = {
                            taoh_action: 'taoh_ntw_create_channel',
                            roomslug,
                            keyword: ntw_keyword,
                            channel_id: channelId,
                            channel_type: taohChannelOrganizer,
                            channel_data: channelData,
                            channel_members: channelMembers,
                            key: my_pToken
                        };

                        const response = await ajaxPost(_taoh_site_ajax_url, payload);
                        if (onSuccess) {
                            $('.chat_with_organizer').attr('data-channel_id', channelId);
                            $('.chat_with_organizer').attr('id', "channel-"+channelId);
                            onSuccess(response);
                        }
                        return response;
                    } catch (err) {
                        if (onError) onError(err);
                        throw err;
                    }
                }

                createOrganizerChannel(
                    event_owner_ptoken,
                    (res) => console.log("", res),
                    (err) => console.error("", err)
                );

            }

            if ((is_event_owner || _can_delete_all_msg) && _ORGANIZER_CHANNEL_ENABLE) {
                getNTWUserChannels({
                    roomslug,
                    global_slug: eventToken,
                    keyword: ntw_keyword,
                    type: taohChannelOrganizer,
                    my_pToken
                }).then(({requestData, response}) => {
                    renderOrganizerChannelList(response.channels);
                });
            }

            // Render Exhibitor, Sponsor and Session Channels
            if (eventToken) {
                getNTWUserChannels({
                    roomslug,
                    global_slug: eventToken,
                    keyword: ntw_keyword,
                    type: taohChannelExhibitor,
                    my_pToken
                }).then(({requestData, response}) => {
                    renderExhibitorChannelList(response.channels);
                });

                getNTWUserChannels({
                    roomslug,
                    global_slug: eventToken,
                    keyword: ntw_keyword,
                    type: taohChannelSession,
                    my_pToken
                }).then(({requestData, response}) => {
                    renderSessionChannelList(response.channels);
                });
            }

            taoh_get_mood_status();

            function loadParticipants() {

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

                loadchatWindow(chatWindow);
                taoh_load_network_entries();

                random = items[Math.floor(Math.random() * items.length)];

                taoh_load_suggestion_users();
                taoh_get_activities();
            }

            setInterval(() => {
                taoh_load_network_entries();
                taoh_get_activities();
            }, 120000);

            $('.participants_refresh').on('click', function () {

                CURRENT_BLOCK_VISITING = 'PARTICIPANT';

                PARTICIPANT_VISITING_COUNT++;

                if(PARTICIPANT_VISITING_COUNT == 1){

                     setTimeout(() => {
                        //alert("hi")
                        restartDojoMessages();
                    }, 3000);
                }

                $('.watchPartySection').hide();
                $('.watchPartySection').removeClass('watchPartyEnabled');
                $('.chat-leftsidebar').removeClass('watchPartyEnabled');
                $('#tourbutton').addClass('d-xl-block');

                $('.participants_refresh_div').addClass('participants-active');

                const currentElem = $('#participants_refresh');
                const currentElem_icon = currentElem.find('i');
                currentElem_icon.removeClass('fa-refresh').addClass('fa-spinner fa-spin');

                loadParticipants();
            });

            $('#createDM').on('click', function () {
                $('.watchPartySection').hide();
                $('.watchPartySection').removeClass('watchPartyEnabled');
                $('.chat-leftsidebar').removeClass('watchPartyEnabled');
                loadParticipants();
            });

            $('.arrowContainer').on('click', function () {
                let channelId = $('#channel-chat').data('channel_id');
                $('.downArrow, .upArrow').toggleClass('d-none d-flex');
                //$('.pin_message_div'+channelId).toggleClass('d-none d-flex');
                $('.comm_pin_message_div').toggleClass('d-none d-flex');
            });

            $(document).on('click', '.channel_toggle', function () {
                const toggleText = $(this).attr('toggle_text');
                if (toggleText == 'open') {
                    $(this).attr('toggle_text','close')
                    $('.channnel_collapsible').addClass('open');
                    $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(180deg)');
                } else {
                    $(this).attr('toggle_text','open')
                    $('.channnel_collapsible').removeClass('open');
                    $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(0deg)');
                }
            });

            $(document).on('click', '.openchatacc_chat_now_btn', function() {
                let video_link = $('.openchatacc_chat_now_btn').attr("video_link");
                // let chat_with = $('.openchatacc_chat_now_btn').attr("chat_with");
                // if(chat_with) {
                //     loadDmWindow(chat_with);
                // }
                if (video_link) {
                    window.open(video_link, '_blank');
                }
                updateSpeedNetworkingCarousel();
            });

            $(document).on('click', '.sn_channel_toggle', function () {
                const toggleText = $(this).attr('toggle_text');
                if (toggleText == 'open') {
                    $(this).attr('toggle_text','close')
                    $('.sn_channel_description').addClass('open');
                    $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(180deg)');
                } else {
                    $(this).attr('toggle_text','open')
                    $('.sn_channel_description').removeClass('open');
                    $(this).find('.channel-drp-dwn-svg').css('transform', 'rotate(0deg)');
                }
            });

            $(document).on('click', '.delete_channel', function (e) {
                e.stopPropagation();

                const currentElem = $(this);
                const channelItem = currentElem.closest('.channel-item');
                let channelId = channelItem.getSyncedData('channelid');
                let channelType = channelItem.getSyncedData('channeltype') || 1;

                $('#delete_message_id').val("");
                $('#delete_message_key').val("");
                $('#delete_confirmation_msg').text("Are you sure to delete the channel?");

                $('#delete_channel_id').val(channelId);
                $('#delete_channel_type').val(channelType);

                $('#deleteModal').modal('show');
            });

            $(document).on('click', '#confirm_delete_btn', function () {

                let channelId = $('#delete_channel_id').val();
                let channelType = $('#delete_channel_type').val();

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: "post",
                    data: {
                        roomslug: ntw_room_key,
                        taoh_action: "taoh_ntw_delete_channel",
                        channel_id: channelId,
                        channel_type: channelType,
                        keyword: ntw_keyword,
                        key: my_pToken
                    },
                    dataType: "json",
                    success: function (res) {
                        //console.log("taoh_ntw_delete_channel", res);
                        if (res.status = "success") {
                            $('#deleteModal').modal('hide');
                            $(`#channel-${channelId}`).remove();

                            if (chatWindow == 'browse_channels') {
                                runBrowseChannels(1);
                            }

                            if($(`#channel-chat[data-channel_id='${channelId}']`).length) {
                                $('#channelData-sidebar').removeClass('d-xl-block');
                                $('#participants_refresh').trigger('click');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Delete failed:", error);
                    }
                });
            });

            $(document).on('click', '.toggle-text-usermood', function () {
                const $this = $(this);
                const $p = $this.closest('.mood_status_div');
                $p.find('.short-text, .full-text').toggle();
                if ($this.text() === 'show more') {
                    $this.text('show less');
                } else {
                    $this.text('show more');
                }
            });

            $(document).on('click', '.star_channel', function () {
                const currentElem = $(this);
                let status  = currentElem.attr('data-status');
                let action = 0;
                if(status == 0) {
                    action = 1;
                }

                const channelId = currentElem.attr('data-channel_id');
                const channelType = currentElem.attr('data-channel_type');

                //$(`#action_loader-${channelId}`).attr('class', 'la la-spinner la-spin');

                $(`#action_loader-${channelId}`).removeClass("la-ellipsis-v").addClass("la-spinner la-spin");

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: "post",
                    data: {
                        roomslug: ntw_room_key,
                        taoh_action: "taoh_ntw_star_channel",
                        channel_id: channelId,
                        channel_type: channelType,
                        keyword: ntw_keyword,
                        star: action,
                        key: my_pToken
                    },
                    dataType: "json",
                    success: function (res) {
                        //console.log("taoh_ntw_delete_channel", res);
                        if (res.success) {
                            //$(`.chat-list[data-frm_message_id='${messageId}']`).remove();
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Delete failed:", error);
                    }
                });
            });

            $('#browseMoreChannels').on('click', function () {
                loadchatWindow('browse_channels');

                currentChannelId = null;

                const activeTabSlug = $('#browseChannelTab .nav-link.active').data('bc_slug');
                const fallbackTabSlug = (browse_channel_tabs && browse_channel_tabs[0] && browse_channel_tabs[0].slug) ||
                    $('#browseChannelTab .nav-link').first().data('bc_slug') || null;

                const tabSlug = activeTabSlug || fallbackTabSlug;
                if (!tabSlug) return;

                // Find the nav-link to activate
                const $targetLink = $(`#browseChannelTab .nav-link[data-bc_slug="${tabSlug}"]`);
                if ($targetLink.length) {
                    const tab = new bootstrap.Tab($targetLink[0]);
                    tab.show();
                }

                $('#browseChannelQuery').val('');
                $(`#browse_${tabSlug}_channels`).awloader('show');
                $('#browse_channels_wrapper').removeClass('d-none');

                $('.watchPartySection').hide();
                $('.watchPartySection').removeClass('watchPartyEnabled');
                $('.chat-leftsidebar').removeClass('watchPartyEnabled');

                addRemoveActive($('#browseMoreChannels'));

                loadChannelsList(tabSlug);
            });

            $(document).on('click', '.room_type', function () {
                var type = $(this).val();
                showHideExternal(type);
            });

            $('#browseChannelTab .nav-link').on('click', function () {
                const currentElem = $(this);
                const tabSlug = currentElem.getSyncedData('bc_slug');

                $('#browseChannelQuery').val('');
                $(`#browse_${tabSlug}_channels`).awloader('show');
                loadChannelsList(tabSlug);
            });

            loadParticipants();
            ///$('#participants_refresh').trigger('click');

            $('.live_now_block').removeClass('d-lg-block');

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

            setTimeout(function () {
                taoh_load_suggestion_users();

                //if(eventToken)
                getOrganizerOnlineStatus();

            }, 2000);

            setInterval(() => {
                //if(eventToken)
                getOrganizerOnlineStatus();
            }, 60000);

            $('#toggleMoreOption').click(function () {
                $('#extraRoomFields').slideToggle();
                $('#extraRoomFields').toggleClass('visible');

                if($('#extraRoomFields').hasClass('visible')) {
                    $(this).html('<i class="bx bx-minus"></i> Show Less');
                } else{
                    $(this).html('<i class="bx bx-plus"></i> Show More Options');
                }
            });

            // Initialize observers for both lists
            observeList('#channelList', '.channel-collapse-icon');
            observeList('#dmChannelList', '.dm-collapse-icon');

            // Initial toggle on page load
            toggleCollapseIcon('#channelList', '.channel-collapse-icon');
            toggleCollapseIcon('#dmChannelList', '.dm-collapse-icon');
            // if (layout == 1) { // hide agree modal on page load
                const modalEl = document.getElementById('v-overlay');
                const modal = new bootstrap.Modal(modalEl);
                const storageKey = 'date_last_agree_' + ntw_room_key;
                const lastAgreeDate = localStorage.getItem(storageKey);
                const today = get_today_date();

                const showModal = () => {
                    modal.show();
                    localStorage.setItem(storageKey, today);
                };

                if (!lastAgreeDate || lastAgreeDate !== today) {
                    showModal();
                } else {
                    // setTimeout(initNetworkingTour, 3000);
                }
            // }

            $(".agree-btn").on('click', function () {
                const modalEl = document.getElementById('v-overlay');
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();

                // setTimeout(initNetworkingTour, 3000);
            });
            $("#searchFilter").on('submit', function (e) {
                e.preventDefault();
                searchFilter();
            });

            $("#query").on('keyup', debounceLeadingTrailing(function () {
                searchFilter();
            }, 300)); // Fires immediately, then 300ms after typing stops

            const $sel = $('#members_to_add').attr('multiple','multiple');

            $sel.select2({
                width: '100%',
                placeholder: 'Select members to add',
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    delay: 150,
                    transport: function (params, success, failure) {
                        const term = (params.data?.term || '').toLowerCase();

                        const addChannelMembersModal = $('#addChannelMembersModal');
                        const channel_id = addChannelMembersModal.find('input[name="channel_id"]').val();
                        const channel_type = addChannelMembersModal.find('input[name="channel_type"]').val();

                        IntaoDB.getItem(objStores.ntw_store.name, 'ntw_entries_' + ntw_room_key)
                            .then(async intao => {
                                const items = intao?.values?.items || {};
                                let channelInfoResponse = await getNTWChannelById({
                                    roomslug,
                                    global_slug: eventToken,
                                    keyword: ntw_keyword,
                                    channel_id,
                                    type: channel_type,
                                    my_pToken
                                });
                                const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channel_id);
                                let existingMembers = channelInfo?.members || [];

                                const results = [];
                                const selectedIds = $sel.val() || [];

                                for (const [ptoken, entry] of Object.entries(items)) {
                                    if (ptoken === my_pToken) continue;

                                    if (selectedIds.includes(ptoken)) continue;

                                    const cell = tryParse(entry?.cell);
                                    const idx = makeIndex(cell);
                                    if (idx.includes(term)) {
                                        const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${cell?.avatar?.trim() || 'default'}.png`;
                                        const userAvatarSrc = await buildAvatarImage(cell.avatar_image, fallbackSrc);
                                        const already = existingMembers.includes(ptoken);
                                        results.push({
                                            id: ptoken,
                                            text: makeLabel(cell),
                                            avatar: userAvatarSrc || '',
                                            disabled: already,
                                            _already: already
                                        });
                                    }
                                }
                                success({results: results.slice(0, 50)});
                            })
                            .catch(() => success({ results: [] }));
                        return { abort(){} };
                    },
                    processResults: data => ({ results: data.results || [] }),
                    cache: false
                },
                templateResult: (data) => {
                    if (data.loading) return data.text;

                    const $row = $('<span style="display:flex;align-items:center;gap:8px;width:100%;"></span>');

                    if (data.avatar) {
                        $row.append(
                            $(`<img src="${data.avatar}" onerror="this.style.display='none'" style="width:22px;height:22px;border-radius:50%;">`)
                        );
                    }

                    $row.append($('<span></span>').text(data.text));

                    if (data._already) {
                        $row.append(
                            $('<span style="margin-left:auto;font-size:12px;">Already member</span>')
                        );
                    }
                    return $row;
                },
                templateSelection: data => data.text
            });

            // helpers
            function tryParse(s){ try{ return s ? JSON.parse(s) : {}; } catch { return {}; } }
            function valuesText(obj){ return obj && typeof obj==='object' ? Object.values(obj).map(v=>String(v).split(':>').pop()) : []; }
            function makeLabel(c){
                const name = c.chat_name || [c.fname,c.lname].filter(Boolean).join(' ') || 'Unknown';
                const t = valuesText(c.title)[0] || '';
                const co = ''; //valuesText(c.company)[0] || '';
                const right = [t, co && `@ ${co}`].filter(Boolean).join(' ');
                return right ? `${name} — ${right}` : name;
            }
            function makeIndex(c){
                return [
                    c.chat_name, c.fname, c.lname, c.email,
                    valuesText(c.title).join(' '),
                    valuesText(c.company).join(' '),
                    valuesText(c.skill).join(' '),
                    c.full_location
                ].filter(Boolean).join(' ').toLowerCase();
            }

            function fetchChannels() {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: "post",
                    data: {
                        roomslug: ntw_room_key,
                        taoh_action: "taoh_ntw_get_channel_by_eventtoken",
                        eventtoken,
                        key: my_pToken
                    },
                    dataType: "json",
                    success: function (res) {
                        //console.log("taoh_ntw_delete_channel", res);
                        if (res.status = "success") {
                            $('#deleteModal').modal('hide');
                            $(`#channel-${channelId}`).remove();

                            if (chatWindow == 'browse_channels') {
                                runBrowseChannels(1);
                            }

                            if($(`#channel-chat[data-channel_id='${channelId}']`).length) {
                                $('#channelData-sidebar').removeClass('d-xl-block');
                                $('#participants_refresh').trigger('click');
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Delete failed:", error);
                    }
                });
            }

            $('#membersToAddForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const selected = $('#members_to_add').val() || [];
                if (selected.length === 0) {
                    taoh_set_warning_message('Please select at least one member to add.');
                    return;
                }

                const channelId = form.find('input[name="channel_id"]').val();
                const channelType = form.find('input[name="channel_type"]').val();

                const addBtn = $('#addMembersBtn');
                addBtn.prop('disabled', true).text('Adding...');

                $.ajax({
                    url: _taoh_site_ajax_url,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        taoh_action: 'taoh_ntw_add_channel_members',
                        roomslug,
                        keyword: ntw_keyword,
                        members: selected,
                        key: my_pToken,
                        channel_id: channelId,
                        channel_type: channelType,
                        global_slug: eventToken
                    },
                    success: function(response) {
                        if (response.success) {
                            // Clear relevant cache
                            const ntwChannelInfoKey = ['channel', 'info', roomslug, channelId, channelType].filter(Boolean).join('_');
                            IntaoDB.removeItem(objStores.ntw_store.name, ntwChannelInfoKey);

                            $('#addChannelMembersModal').modal('hide');
                            $('#membersModal').modal('hide');
                            taoh_set_success_message('Members added successfully.');
                            if (chatWindow === 'browse_channels') {
                                $('#browseMoreChannels').trigger('click');
                            }
                            // location.reload();
                        } else {
                            taoh_set_error_message(response.message || 'Failed to add members. Try again!.');
                            addBtn.prop('disabled', false).text('Add');
                        }
                    },
                    error: function() {
                        taoh_set_error_message('An error occurred while adding members.');
                        addBtn.prop('disabled', false).text('Add');
                    }
                });
            });

            /*if(chatwithchannelid != "" && chatwithchanneltype != "") {

                let getNTWChannelsFormData = {
                    roomslug: ntw_room_key,
                    keyword: ntw_keyword,
                    type: chatwithchanneltype,
                    my_pToken,
                    q: '',
                    browsetabslug: chatwithchannelid,
                };
                if(chatwithchanneltype == taohChannelSession || chatwithchanneltype == taohChannelExhibitor) {
                    getNTWChannelsFormData.eventtoken = eventToken;
                }
                getNTWChannels(getNTWChannelsFormData, true).then(() => {
                    showChannelInfoModalPopup(chatwithchannelid, chatwithchanneltype, 'about', chatwithchannelid);
                });

            }*/

            Waves.init();

        });

        $(window).on('load', async function() {
            console.log("Page Load Completed");
            setTimeout(async function () {
                console.log(ntw_room_key, ntw_keyword);
                try {
                    await syncRoomStamp({ roomslug: ntw_room_key, keyword: ntw_keyword, my_pToken });
                } catch (err) {
                    console.error(err);
                }
            }, 2000);
            startPolling();
            resetIdleTimer();
        });

        async function saveLastSendMsgTimestamp(channel_id, timestamp) {
            const store = objStores.ntw_store.name;
            const key = roomslug+'_lastSendMsgTimestamp';

            // Load existing record from IndexedDB
            const existing = await IntaoDB.getItem(store, key);
            const data = existing?.values || {}; // fallback to empty object

            // Update the channel’s timestamp
            data[channel_id] = timestamp;

            // Save back into IndexedDB
            await IntaoDB.setItem(store, {
                taoh_ntw: key,
                values: data,
                timestamp: Date.now(),
            });
        }

        async function loadLastSendMsgTimestamp() {
            const store = objStores.ntw_store.name;
            const key = roomslug+'_lastSendMsgTimestamp';

            const existing = await IntaoDB.getItem(store, key);
            return existing?.values || {};
        }

        async function syncRoomStamp({roomslug, keyword, my_pToken}) {
            const res = await getRoomStamp({roomslug, keyword, my_pToken}, true, false);
            const requestData = res?.requestData ?? {};
            let roomStamp = res?.response ?? {};

            const ntwRoomStamp = ['room', requestData.keyword, requestData.roomslug, 'stamp']
                .filter(Boolean).join('_');
            const store = objStores.ntw_store.name;

            const existing = await IntaoDB.getItem(store, ntwRoomStamp);

            // Once read existing, always overwrite with fresh
            await IntaoDB.setItem(store, { taoh_ntw: ntwRoomStamp, values: roomStamp, timestamp: Date.now() });

            const oldStamp = parseJSONSafely(existing?.values);

            //console.log('syncRoomStamp', oldStamp, roomStamp);

            const channelChanged = !oldStamp || oldStamp.channel !== roomStamp?.channel;
            const exhibitorChanged = !oldStamp || oldStamp.exhibitor !== roomStamp?.exhibitor;
            // const sponsorChanged = !oldStamp || oldStamp.sponsor !== roomStamp?.sponsor;
            const sessionChanged = !oldStamp || oldStamp.session !== roomStamp?.session;
            const sessionInfoChanged = !oldStamp || oldStamp.session_info !== roomStamp?.session_info;
            const exhibitorInfoChanged = !oldStamp || oldStamp.exhibitor_info !== roomStamp?.exhibitor_info;
            const dmChanged = !oldStamp || oldStamp.dm !== roomStamp?.dm;
            const organizerChanged = !oldStamp || oldStamp.organizer !== roomStamp?.organizer;
            const speedNetworkingChanged = !oldStamp || oldStamp.speed_networking !== roomStamp?.speed_networking;

            const cacheKeyScoreChanged = !oldStamp || oldStamp.cache_key_score !== roomStamp?.cache_key_score;
            if (channelChanged) {
                stopChannelUpdate = false;
                const channelBeforeTimestamp = parseInt(oldStamp?.channel || 0, 10);
                const freshChannelStamp = await getRoomChannelStamp({
                    roomslug,
                    keyword,
                    channel_type: taohChannelDefault,
                    timestamp: channelBeforeTimestamp,
                    my_pToken
                }, true, false);

                //console.log('Fetched fresh room channel stamp:', typeof freshChannelStamp.response, freshChannelStamp);
                for (const channelId in freshChannelStamp.response) {

                    if (stopChannelUpdate) {
                        console.log("CHANNEL UPDATE LOOP STOPPED (USER SWITCHED CHANNEL)");
                        break;
                    }

                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshChannelStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);

                    const channelInfoKey = `channel_info_${roomslug}_${channelId}_${taohChannelDefault}`;
                    const channelInfoExisting = await IntaoDB.getItem(store, channelInfoKey);
                    let channelInfoExistingValues = channelInfoExisting?.values?.channels?.[0]?.members ?? [];
                    console.log("skipped here channelInfoExistingValues", channelInfoExistingValues);
                    if (!channelInfoExistingValues.includes(my_pToken)) {
                        console.log("skipped here ch");
                        continue;
                    }
                    console.log("skipped out here");

                    const payload = {
                        ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                        taoh_ntw: channelKey,
                        last_fetch_stamp: channelLastStamp,
                    };

                    await IntaoDB.setItem(store, payload);

                    const channelElem = $(`#channel-${channelId}`);
                    let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_read_stamp ?? 0);

                    if (channelElem.length && currentChannelId != channelId && hasUpdates) {
                        //console.log("hasUpdates1", hasUpdates);
                        var last_send_msg_timestamp = await loadLastSendMsgTimestamp();

                        console.log("last_send_msg_timestamp =====----====", last_send_msg_timestamp);

                        if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                           // console.log("hasUpdates2", hasUpdates);
                            if(channelExisting?.last_read_stamp) {
                               // console.log("hasUpdates3", hasUpdates);
                                //channelElem.addClass('have_updates');
                            }
                        }
                    }
                    if(hasUpdates) {
                        if(currentChannelId && currentChannelId == channelId) {
                            await saveChannelMessages(channelId, 1, taohChannelDefault);
                        } else {
                            await saveChannelMessages(channelId, 0, taohChannelDefault);
                        }
                    }

                }

                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDefault, my_pToken}, true).then(({requestData, response}) => {
                    renderChannelList(response.channels, tempUserChannelArray);
                });
            }

            if (exhibitorChanged && eventToken) {
                stopChannelUpdate = false;
                const exhibitorChannelBeforeTimestamp = parseInt(oldStamp?.channel || 0, 10);
                const freshExhibitorChannelStamp = await getRoomChannelStamp({
                    roomslug,
                    global_slug: eventToken,
                    keyword,
                    channel_type: taohChannelExhibitor,
                    timestamp: exhibitorChannelBeforeTimestamp,
                    my_pToken
                }, true, false);

                //console.log('Fetched fresh room exhibitor channel stamp:', typeof freshExhibitorChannelStamp.response, freshExhibitorChannelStamp);
                for (const channelId in freshExhibitorChannelStamp.response) {
                    if (stopChannelUpdate) {
                        console.log("CHANNEL UPDATE LOOP STOPPED (USER SWITCHED CHANNEL)");
                        break;
                    }
                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshExhibitorChannelStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);

                    const channelInfoKey = `channel_info_${roomslug}_${channelId}_${taohChannelExhibitor}`;
                    const channelInfoExisting = await IntaoDB.getItem(store, channelInfoKey);
                    let channelInfoExistingValues = channelInfoExisting?.values?.channels?.[0]?.members ?? [];
                    console.log("skipped here channelInfoExistingValues", channelInfoExistingValues);
                    if (!channelInfoExistingValues.includes(my_pToken)) {
                        console.log("skipped here ch");
                        continue;
                    }
                    console.log("skipped out here");

                    const payload = {
                        ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                        taoh_ntw: channelKey,
                        last_fetch_stamp: channelLastStamp,
                    };

                    await IntaoDB.setItem(store, payload);

                    //console.log("EXHI channelExisting", channelExisting);

                    const channelElem = $(`#channel-${channelId}`);
                    let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_update_stamp ?? 0);
                    if (channelElem.length && currentChannelId != channelId && hasUpdates) {
                        var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
                        if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                            //channelElem.addClass('have_updates');
                        }
                    }
                    if(hasUpdates) {
                        if(currentChannelId && currentChannelId == channelId) {
                            await saveChannelMessages(channelId, 1, taohChannelExhibitor);
                        } else {
                            await saveChannelMessages(channelId, 0, taohChannelExhibitor);
                        }
                    }
                    //if(currentChannelId && currentChannelId == channelId) loadChannelMessages(currentChannelId, 0);

                }

                getNTWUserChannels({roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelExhibitor, my_pToken}, true).then(({requestData, response}) => {
                    renderExhibitorChannelList(response.channels);
                });
            }

            if (sessionChanged && eventToken) {
                const sessionChannelBeforeTimestamp = parseInt(oldStamp?.channel || 0, 10);
                const freshSessionChannelStamp = await getRoomChannelStamp({
                    roomslug,
                    global_slug: eventToken,
                    keyword,
                    channel_type: taohChannelSession,
                    timestamp: sessionChannelBeforeTimestamp,
                    my_pToken
                }, true, false);

                for (const channelId in freshSessionChannelStamp.response) {
                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshSessionChannelStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);

                    const channelInfoKey = `channel_info_${roomslug}_${channelId}_${taohChannelSession}`;
                    const channelInfoExisting = await IntaoDB.getItem(store, channelInfoKey);
                    let channelInfoExistingValues = channelInfoExisting?.values?.channels?.[0]?.members ?? [];
                    console.log("skipped here channelInfoExistingValues", channelInfoExistingValues);
                    if (!channelInfoExistingValues.includes(my_pToken)) {
                        console.log("skipped here ch");
                        continue;
                    }
                    console.log("skipped out here");

                    const payload = {
                        ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                        taoh_ntw: channelKey,
                        last_fetch_stamp: channelLastStamp,
                    };

                    await IntaoDB.setItem(store, payload);

                    const channelElem = $(`#channel-${channelId}`);
                    let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_update_stamp ?? 0);
                    if (channelElem.length && currentChannelId != channelId && hasUpdates) {
                        var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
                        if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                            //channelElem.addClass('have_updates');
                        }
                    }
                    if(hasUpdates) {
                        if(currentChannelId && currentChannelId == channelId) {
                            await saveChannelMessages(channelId, 1, taohChannelSession);
                        } else {
                            await saveChannelMessages(channelId, 0, taohChannelSession);
                        }
                    }
                    //if(currentChannelId && currentChannelId == channelId) loadChannelMessages(currentChannelId, 0);

                }

                getNTWUserChannels({roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelSession, my_pToken}, true).then(({requestData, response}) => {
                    renderSessionChannelList(response.channels);
                });
            }

            if (dmChanged) {
                stopChannelUpdate = false;
                const dmBeforeTimestamp = parseInt(oldStamp?.dm || 0, 10);
                const freshDMStamp = await getRoomChannelStamp({
                    roomslug,
                    keyword,
                    channel_type: taohChannelDm,
                    timestamp: dmBeforeTimestamp,
                    my_pToken
                }, true, false);

                //console.log('Fetched fresh room dm stamp:', freshDMStamp);
                for (const channelId in freshDMStamp.response) {
                    if (stopChannelUpdate) {
                        console.log("CHANNEL UPDATE LOOP STOPPED (USER SWITCHED CHANNEL)");
                        break;
                    }
                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshDMStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);
                    const channelInfoKey = `channel_info_${roomslug}_${channelId}_${taohChannelDm}`;
                    const channelInfoExisting = await IntaoDB.getItem(store, channelInfoKey);
                    let channelInfoExistingValues = channelInfoExisting?.values?.channels?.[0]?.members ?? [];
                    console.log("skipped here channelInfoExistingValues", channelInfoExistingValues);
                    if (!channelInfoExistingValues.includes(my_pToken)) {
                        console.log("skipped here");
                        continue;
                    }
                    console.log("skipped out here");

                    const payload = {
                        ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                        taoh_ntw: channelKey,
                        last_fetch_stamp: channelLastStamp,
                    };

                    await IntaoDB.setItem(store, payload);

                    const channelElem = $(`#channel-${channelId}`);
                    let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_read_stamp ?? 0);
                    if (channelElem.length && currentChannelId != channelId && hasUpdates) {
                        var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
                        if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                            //channelElem.addClass('have_updates');
                        }
                    }
                    if(hasUpdates) {

                        if(currentChannelId && currentChannelId == channelId) {
                            await saveChannelMessages(channelId, 1, taohChannelDm);
                        } else {
                            await saveChannelMessages(channelId, 0, taohChannelDm);
                        }
                    }
                    //if(currentChannelId && currentChannelId == channelId) loadChannelMessages(currentChannelId, 0);

                }

                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDm, my_pToken}, true).then(({requestData, response}) => {
                    renderDMChannelList(response.channels);
                });
            }

            if (speedNetworkingChanged) {

                console.log('speedNetworkingChanged speedNetworkingChanged:', speedNetworkingChanged);

                const snBeforeTimestamp = parseInt(oldStamp?.dm || 0, 10);
                const freshsnStamp = await getRoomChannelStamp({
                    roomslug,
                    keyword,
                    channel_type: taohChannelSpeedNtw,
                    timestamp: snBeforeTimestamp,
                    my_pToken
                }, true, false);

                console.log('Fetched fresh room speed networking stamp:', freshsnStamp);
                for (const channelId in freshsnStamp.response) {
                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshsnStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);

                    const payload = {
                        ...(channelExisting && typeof channelExisting === 'object' ? channelExisting : {}),
                        taoh_ntw: channelKey,
                        last_fetch_stamp: channelLastStamp,
                    };

                    await IntaoDB.setItem(store, payload);

                    const channelElem = $(`#channel-${channelId}`);
                    // let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_update_stamp ?? 0);
                    // if (channelElem.length && currentChannelId != channelId && hasUpdates) {

                    // }

                    //if(currentChannelId && currentChannelId == channelId) loadSpeedNetworkingData(currentChannelId, 0);
                    loadSpeedNetworkingData(channelId);
                }

                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelSpeedNtw, my_pToken}, true).then(({requestData, response}) => {
                    console.log("getNTWUserChannels ---", requestData, response);
                    renderSpeedChannelList(response.channels);
                });
            }

            if (organizerChanged) {
                stopChannelUpdate = false;
                const organizerBeforeTimestamp = parseInt(oldStamp?.organizer || 0, 10);
                const freshOrganizerStamp = await getRoomChannelStamp({
                    roomslug,
                    keyword,
                    channel_type: taohChannelOrganizer,
                    timestamp: organizerBeforeTimestamp,
                    my_pToken
                }, true, false);

                for (const channelId in freshOrganizerStamp.response) {
                    if (stopChannelUpdate) {
                        console.log("CHANNEL UPDATE LOOP STOPPED (USER SWITCHED CHANNEL)");
                        break;
                    }
                    // get and update channel last fetch timestamp in intaodb
                    const channelLastStamp = freshOrganizerStamp.response[channelId];
                    const channelKey = ['channel', channelId].filter(Boolean).join('_');
                    const channelExisting = await IntaoDB.getItem(store, channelKey);

                    const channelElem = $(`#channel-${channelId}`);
                    let hasUpdates = Number(channelLastStamp) > Number(channelExisting?.last_read_stamp ?? 0);
                    if(hasUpdates) {
                        if(currentChannelId && currentChannelId == channelId) {
                            await saveChannelMessages(channelId, 1, taohChannelOrganizer);
                        } else {
                            await saveChannelMessages(channelId, 0, taohChannelOrganizer);
                        }
                    }
                }

                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelOrganizer, my_pToken}, true).then(({requestData, response}) => {
                    renderOrganizerChannelList(response.channels);
                });
            }

            if (sessionInfoChanged && eventToken) {
                getNTWUserChannels({ roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelSession, my_pToken }, true).then(({ response }) => renderSessionChannelList(response.channels));
            }

            if (exhibitorInfoChanged && eventToken) {
                getNTWUserChannels({ roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelExhibitor, my_pToken }, true).then(({ response }) => renderExhibitorChannelList(response.channels));
            }

            if(cacheKeyScoreChanged && typeof clearCacheProcess === 'function') {
                // clearCacheProcess();
            }

            return { requestData, ntwRoomStamp, roomStamp };
        }

        async function startPolling() {
            pollingInterval = setInterval(async () => {
                try {
                    await syncRoomStamp({ roomslug: ntw_room_key, keyword: ntw_keyword, my_pToken });
                } catch (err) {
                    console.error(err);
                }
            }, 10000);

            // activeuserInterval = setInterval(() => addActiveUserPtoken(), 240000);
            // userLiveIntervalId = setInterval(() => userLiveStatusUpdateFunction(), 60000);
        }

        function stopPolling() {
            clearInterval(pollingInterval);
            clearInterval(activeuserInterval);
            stopChannelUpdate = true;
            taoh_set_warning_message("It looks like you haven’t interacted for a while.<br><br>To continue your session and resume updates, please click the button below.", false, 'toast-middle', [
                {
                    text: 'Continue',
                    action: () => {
                        startPolling();
                        resetIdleTimer();
                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                }
            ], {hideDismissBtn: true});
        }

        // Reset idle timer whenever user interacts
        function resetIdleTimer() {
            clearTimeout(idleTimer); // stop previous timer
            idleTimer = setTimeout(onUserIdle, idleLimit); // restart 2-min timer
        }

        // Function triggered when user idle
        function onUserIdle() {
            //console.log("User is idle");
            stopPolling();
        }

        // Continue button click
        // $('.continue_btn').on('click', function() {
        //     $('#wakeupModal').modal('hide');
        //     startPolling();
        //     resetIdleTimer(); // restart idle tracking
        // });

        // Listen for user activity
        $(document).on('mousemove keypress click scroll', resetIdleTimer);


        document.addEventListener('DOMContentLoaded', function () {
            const createChannelMoreOptionslink = document.querySelector('a[href="#createChannelMoreOptions"]');
            const createChannelMoreOptionsCollapseEl = document.querySelector('#createChannelMoreOptions');

            if (createChannelMoreOptionslink && createChannelMoreOptionsCollapseEl) {
                const textEl = createChannelMoreOptionslink.querySelector('.toggle-text');
                const icon = createChannelMoreOptionslink.querySelector('i');
                const closedText = 'Show More Options';
                const openText = 'Show Less Options';

                createChannelMoreOptionsCollapseEl.addEventListener('show.bs.collapse', () => {
                    if (textEl) textEl.textContent = openText;
                    if (icon) icon.className = icon.className.replace('plus', 'minus');
                });

                createChannelMoreOptionsCollapseEl.addEventListener('hide.bs.collapse', () => {
                    if (textEl) textEl.textContent = closedText;
                    if (icon) icon.className = icon.className.replace('minus', 'plus');
                });
            }
        });

        const tribute = new Tribute({
            noMatchTemplate: () => null,
            values: async (text, cb) => {
                try {
                    if (chatWindow !== 'channel-chat') return cb([]);

                    const query = text.trim().toLowerCase();
                    const ntwChannelsKey = `ntw_channels_${ntw_room_key}`;
                    const channelData = await IntaoDB.getItem(objStores.ntw_store.name, ntwChannelsKey);
                    const channelList = channelData?.values?.channels || {};

                    const channelId = $('#channel-chat').attr('data-channel_id');
                    const channelType = $('#channel-chat').attr('data-channel_type');

                    //console.log("channelId, channelType ####", channelId, channelType);


                    // const store = objStores.ntw_store.name;
                    // const ntwRoomChannels = ['room', ntw_keyword, roomslug, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
                    // const [channelInfo] = await getIntaoDataById(store, ntwRoomChannels, currentChannelId);

                    let channelInfoResponse = await getNTWChannelById({
                        roomslug,
                        global_slug: eventToken,
                        keyword: ntw_keyword,
                        channel_id: channelId,
                        type: channelType,
                        my_pToken
                    });
                    const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

                    if (!channelId) return cb([]);

                    const memberTokens = channelInfo.members || [];
                    const members = [];

                    for (const token of memberTokens) {
                        // if (token === my_pToken) continue; // Skip self

                        const user = await getUserInfo(token, 'public');
                        if (user) {
                            const fallbackAvatar = `${_taoh_ops_prefix}/avatar/PNG/128/${user.avatar || 'default'}.png`;
                            const avatarSrc = await buildAvatarImage(user.avatar_image, fallbackAvatar);
                            members.push({
                                ptoken: user.ptoken,
                                key: user.chat_name,
                                value: user.chat_name,
                                img: avatarSrc
                            });
                        }
                    }

                    const filtered = query
                        ? members.filter(m => m.key.toLowerCase().includes(query))
                        : members;

                    cb(filtered);
                } catch (error) {
                    console.error('Tribute async values error:', error);
                    cb([]);
                }
            },
            menuItemTemplate: (item) => {
                return `<div class="menu-mention-item">
                            <img src="${item.original.img}" alt="${item.original.key}" class="menu-mention-avatar">
                            <span class="menu-mention-name">${item.original.key}</span>
                        </div>`;
            },
        });

        const editor = document.getElementById('chat_input');
        if (editor) {
            tribute.attach(editor);
            editor.addEventListener('tribute-replaced', e => {
                const selected = e.detail.item.original;
                if (selected?.value && selected?.ptoken) {
                    mentionUserArray[selected.value] = selected.ptoken;
                }
            });
        }

        const replyEditor = document.getElementById('chat_reply_input');
        if (replyEditor) {
            tribute.attach(replyEditor);
            replyEditor.addEventListener('tribute-replaced', e => {
                const selected = e.detail.item.original;
                if (selected?.value && selected?.ptoken) {
                    mentionUserArray[selected.value] = selected.ptoken;
                }
            });
        }

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

        loadEmojiCategories();
        loadEmojis();

        $(document).on('click', '.emoji-btn', function(e) {
            e.stopPropagation();
            emojiElem = $(this);
            const messageId = $(this).data('message-id');
            toggleEmojiPicker(messageId);
        });

        $('#emojiToggle').on('click', function () {
            $('#emojiPicker').toggle();
        });

        $(document).on('click', '.emoji-cat-btn', function () {
            const cat = $(this).data('cat');
            loadEmojis(cat);
        });

        $(document).on('click', '.openProfileSideBar', async function () {
            var chatwith = $(this).attr('data-chatwith');
            if(chatwith == "Organizer") {
                return
            }
            const [userLiveStatus, userInfo] = await Promise.all([
                getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
                getUserInfo(chatwith, 'full').catch((e) => {console.log(e)}),
            ]);
            await updateProfileInfo(userInfo, userLiveStatus);
           // console.log("userInfo", userInfo);
           // console.log("userLiveStatus", userLiveStatus);
            loadRightSidebar('profile');
            $('.user-profile-desc .openchatacc').show();
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

        async function init_speedneworking() {
            let data = {
                'taoh_action': 'taoh_ntw_init_speed_networking',
                'key': my_pToken,
                'keyslug': ntw_room_key,
                'channel_type': taohChannelSpeedNtw,
                'keyword': ntw_keyword
            };

            const response = await new Promise((resolve, reject) => {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',

                    success: async function (res) {
                        console.log("taoh_ntw_init_speed_networking", res);
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

            }
        }

        function loadEmojiCategories() {
            const $catRow = $('#emojiCategories').empty();
            for (const category in emojiData) {
                $catRow.append(`<span class="emoji-cat-btn" data-cat="${category}" style="background:none;border:none;padding:5px;font-size:18px;cursor:pointer;">${emojiCategoryIcons[category]}</span>`);
            }
        }

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

        function loadEmojis(category = 'smileys') {
            const emojis = emojiData[category] || [];
            const $grid = $('#emojiGrid').empty();
            emojis.forEach(emoji => {
                $grid.append(`<span class="emoji-item" style="font-size: 20px; cursor: pointer;">${emoji}</span>`);
            });
        }

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

            //console.log("suggestionUsersArray", suggestionUsersArray.length);

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
                                                <span class="fs-12 text-black fw-500 mx-1 ${(suggestionUsersArray.length == 1) ? 'd-none': ''}">OR</span>
                                                <button nextindex="${nextindex}" id="nextIndex" type="button" class="btn std-sm-bor-btn py-0 ${(suggestionUsersArray.length == 1) ? 'd-none': ''}">Next</button>
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

        $(document).on('click', '#nextIndex', function () {
            var nextindex = $(this).attr('nextindex');
            taoh_load_suggestion_users(nextindex);
        });

        async function speedNetworkingAddUser(ptokens, restricted_ptokens) {

            $('.successMatchDiv').addClass('d-none');
            $('.notAvailableDiv').addClass('d-none');
            $('.zeroday-speed').removeClass('d-none');
            $('#contentCarousel').addClass('d-none');
            $('.speed_networking_carousel').html("");

            var rejected_ptokens = [];
            let store = objStores.ntw_store.name;
            var channelId = $('#speedChannelList li:first').attr('data-channel_id');
            let chkey = "channel_"+channelId;
            let existing = await IntaoDB.getItem(store, chkey);
            if (existing && existing.values) {
                rejected_ptokens = existing.values.restricted_ptokens;
            }

            ptokens = [];
            const snchannelKey = `room_${ntw_keyword}_${roomslug}_${my_pToken}_${taohChannelSpeedNtw}_channels`;
            var snchannelData = await IntaoDB.getItem(objStores.ntw_store.name, snchannelKey);
            let snupdatedResponse = snchannelData?.values || [];
            if (snupdatedResponse) {
                ptokens = snupdatedResponse.channels[0].members;
            }

            console.log("speedNetworkingAddUser ptokens ----", ptokens);

            for (const ptoken of ptokens) {

                console.log("speedNetworkingAddUser ptoken", ptoken);

                if(ptoken != my_pToken) {

                    let input = [roomslug, my_pToken, ptoken].sort().join('_');
                    let tmpChannelId = await generateSecureSlug(input, 16);
                    let channelElem = $(`#channel-${tmpChannelId}`);
                    if (channelElem.length) {
                        $('#carousel_item_'+ptoken).remove();
                        updateSpeedNetworkingCarousel();
                        continue;
                    }
                    if (restricted_ptokens?.includes(ptoken)) {
                        continue;
                    }
                    if (rejected_ptokens?.includes(ptoken)) {
                        continue;
                    }

                    $('.zeroday-speed').addClass('d-none');
                    $('#contentCarousel').removeClass('d-none');

                    if($(`.speed_networking_carousel #carousel_item_${ptoken}`).length == 0) {

                        if(!$('.rejectDiv:visible').length && !$('.successMatchDiv:visible').length) {
                            $('.speed_networking_div').removeClass('d-none');
                        }
                        $('#speed_networking-participant').html("");

                        var userInfo = await getUserInfo(ptoken, 'full');
                        var userLiveStatus = await getUserLiveStatus(ptoken);
                        var chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

                        if(chatwith_liveStatus == 0) {
                            continue;
                        }

                        var companyValue = Object.values(userInfo?.company ?? {})[0]?.value || "";
                        //console.log("userInfo userInfo userInfo", userInfo);

                        if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                            var avatar_image = userInfo.avatar_image;
                        } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                            var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                        } else {
                            var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                        }

                        var titleValue = Object.values(userInfo?.title)[0]?.value;
                        if(titleValue == "") {
                            continue;
                        }
                        var userSkills = Object.values(userInfo?.skill ?? {}).map(skill => skill.value);
                        var visibleSkills = userSkills?.slice(0, 3);
                        var extraSkillCount = userSkills?.length - 3;

                        var skillsHtml = "";
                        userSkills.forEach((skill, index) => {
                            let extraClass = index >= 3 ? " d-none extra-skill" : "";
                            skillsHtml += '<a href="#" class="skill' + extraClass + '">' + skill + '</a>';
                        });
                        if (userSkills.length > 3) {
                            skillsHtml += '<a href="#" class="skill extra-skill-toggle">Show more</a>';
                        }

                        let tagsHtml = '';
                        let hobbies = [];
                        try {
                            hobbies = typeof userInfo.hobbies === "string"
                                ? JSON.parse(userInfo.hobbies)   // parse if it's a string
                                : (Array.isArray(userInfo.hobbies) ? userInfo.hobbies : []);
                        } catch (e) {
                            hobbies = [];
                        }

                        if (Array.isArray(hobbies)) {
                            const maxVisible = 3;
                            const extraCount = hobbies.length - maxVisible;

                            hobbies.forEach((tag, index) => {
                                let extraClass = index >= 3 ? " d-none extra-tag" : "";
                                tag = formatTag(tag);
                                tagsHtml += `<a href="#" class="interest ${extraClass} c-${index + 1}">${tag}</a>\n`;
                            });

                            if (extraCount > 0) {
                                tagsHtml += `<a href="#" class="interest extra-interest-toggle c-4">Show more</a>\n`;
                            }
                        }

                        $('.speed_networking_carousel').append(`<div id="carousel_item_${ptoken}" class="carousel-item">
                        <div class="d-flex flex-column flex-lg-row" style="gap: 13px;">
                            <div>
                                <img class="speed-v1-profile" src="${avatar_image}" alt="">
                            </div>
                            <div class="d-flex flex-column justify-content-between" style="min-height: 260px;">
                                <div>
                                    <h5 class="speed-v1-title mb-2" style="text-transform: capitalize;">${userInfo.chat_name}</h5>
                                    <p class="speed-v1-badge mb-2" style="text-transform: capitalize;">
                                    ${userInfo?.type ? userInfo.type : 'Professional'}
                                    </p>
                                    <div class="d-flex align-items-center flex-wrap mb-2" style="gap: 12px;">
                                        <div class="speed-v1-info">
                                            <svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V14.5C0 15.3281 0.671875 16 1.5 16H4.5V13.5C4.5 12.6719 5.17188 12 6 12C6.82812 12 7.5 12.6719 7.5 13.5V16H10.5C11.3281 16 12 15.3281 12 14.5V1.5C12 0.671875 11.3281 0 10.5 0H1.5ZM2 7.5C2 7.225 2.225 7 2.5 7H3.5C3.775 7 4 7.225 4 7.5V8.5C4 8.775 3.775 9 3.5 9H2.5C2.225 9 2 8.775 2 8.5V7.5ZM5.5 7H6.5C6.775 7 7 7.225 7 7.5V8.5C7 8.775 6.775 9 6.5 9H5.5C5.225 9 5 8.775 5 8.5V7.5C5 7.225 5.225 7 5.5 7ZM8 7.5C8 7.225 8.225 7 8.5 7H9.5C9.775 7 10 7.225 10 7.5V8.5C10 8.775 9.775 9 9.5 9H8.5C8.225 9 8 8.775 8 8.5V7.5ZM2.5 3H3.5C3.775 3 4 3.225 4 3.5V4.5C4 4.775 3.775 5 3.5 5H2.5C2.225 5 2 4.775 2 4.5V3.5C2 3.225 2.225 3 2.5 3ZM5 3.5C5 3.225 5.225 3 5.5 3H6.5C6.775 3 7 3.225 7 3.5V4.5C7 4.775 6.775 5 6.5 5H5.5C5.225 5 5 4.775 5 4.5V3.5ZM8.5 3H9.5C9.775 3 10 3.225 10 3.5V4.5C10 4.775 9.775 5 9.5 5H8.5C8.225 5 8 4.775 8 4.5V3.5C8 3.225 8.225 3 8.5 3Z" fill="#555555"/>
                                            </svg>
                                            <span>${companyValue}.,</span>
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
                                    <button type="button" data-chatwith="${ptoken}" class="btn std-btn connect_btn mr-2">Connect</button>
                                    <button type="button" data-chatwith="${ptoken}" class="btn bor-btn not_interested_btn">Not interested</button>
                                </div>
                            </div>
                        </div>
                        </div>`);
                        //$(`.speedChannelList li`).first().addClass("have_updates");
                    }

                    $('.successMatchDiv').addClass('d-none');
                    $('.notAvailableDiv').addClass('d-none');
                    updateSpeedNetworkingCarousel();
                }
            }
        }

        const runBrowseChannels = (loader = 1) => {
            const activeTabSlug = $("#browseChannelTab .nav-link.active").attr("data-bc_slug");
            if (loader) {
                const browseChannels = $(`#browse_${activeTabSlug}_channels`);
                browseChannels.awloader('show');
            }
            loadChannelsList(activeTabSlug);
        };

        $(document)
            .on('input', '#browseChannelQuery', debounceLeadingTrailing(runBrowseChannels, 250))
            .on('keydown', '#browseChannelQuery', function (e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    runBrowseChannels(1);
                    return false;
                }
            })
            .on('submit', '#browseChannelForm', function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                runBrowseChannels(1);
                return false;
            });

        $(document).on('click', '.extra-skill-toggle', function(e) {
            e.preventDefault();
            var $toggle = $(this);
            var $container = $toggle.closest('.spd-v1-skill-con');
            var $hidden = $container.find('.extra-skill');

            if ($hidden.hasClass('d-none')) {
                $hidden.removeClass('d-none');
                $toggle.text('Show less');
            } else {
                $hidden.addClass('d-none');
                // compute total skills (exclude toggle itself)
                var total = $container.find('.skill').not('.extra-skill-toggle').length;
                $toggle.text('Show more');
            }
        });

        $(document).on('click', '.extra-interest-toggle', function(e) {
            e.preventDefault();
            var $toggle = $(this);
            var $container = $toggle.closest('.spd-v1-interest-con');
            var $hidden = $container.find('.extra-tag');

            if ($hidden.hasClass('d-none')) {
                $hidden.removeClass('d-none');
                $toggle.text('Show less');
            } else {
                $hidden.addClass('d-none');
                // compute total skills (exclude toggle itself)
                var total = $container.find('.interest').not('.extra-interest-toggle').length;
                $toggle.text('Show more');
            }
        });

        $(document).on('click', '.user-chat-nav', function () {
            $('.user-profile-sidebar').removeClass('d-xl-block');
        });

        $(document).on('click', '.goto-message', function () {
            let frmMessageId = $(this).attr('data-frm_message_id');
            $('#msg_text'+frmMessageId)[0].scrollIntoView({ behavior: 'smooth' });
        });

        $(document).on('click', '.goto-message1', function () {
            let frmMessageId = $(this).attr('data-frm_message_id');
            let target = document.getElementById('msg_text' + frmMessageId);

            if (target) {
                // First scroll to the element
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });

                // After a short delay, adjust scroll position to add margin at top
                const topOffset = 100; // margin in px
                setTimeout(() => {
                    window.scrollBy({ top: -topOffset, behavior: 'smooth' });
                }, 400); // delay to allow smooth scroll to complete
            } else {
                console.warn('Message not found:', frmMessageId);
            }
        });
        // My Mood Status
        $(document).on("click", "#my_status", function () {
            let emojisrc = $('#loadEmojiImg').attr('src');
            let my_status_text = $('#my_status').val();
            if (my_status_text) $('#current_status').val(my_status_text.trim());
            if (emojisrc) $('#selected-emoji img').attr('src', emojisrc);
            $('#status-modal').modal('show');
        });

        $(document).on("click", ".close-status-modal", function () {
            $('#status-modal').modal('hide');
        });

        $(document).on("click", ".status-save", function () {
            var customer_status = $('.ql-editor').text();
            $('#my_status').val(customer_status.trim());
            $('#my_status').attr('disabled', 'disabled');
            $('#network_status').hide();
        });

        $(document).on("click", function (a) {
            if (!$(a.target).closest('#emoji_section').length) {
                $("#emoji_section").hide();
            }
            if (a.target.className == 'emoji-place') {
                $("#emoji_section").show();
            }
        });

        function openStatusModal() {
            $('#status-modal').modal('show');
        }

        function copyToStatus(status_val) {
            $('.mood_status_error2').text("");
            $('#current_status').val(status_val.trim());
        }

        function formatTag(tag) {
            return tag
                .split('_')                      // split by underscore
                .map(word =>
                    word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
                )                                // capitalize each word
                .join(' ');                      // join with space
        }

        function saveStatus() {

            var mood_status_message = "";
            $('.mood_status_error2').text("");
            var customer_status = $('#current_status').val();
            customer_status = customer_status.replace(/<[^>]*>/g, '').trim();

            if(customer_status == "") {
                $('.mood_status_error2').text("This field is required");
                return;
            }
            var choosen_emoji = $('#choosen_emoji').val();
            mood_status_message = customer_status + "###" + choosen_emoji;
            if (customer_status?.trim() === '' && choosen_emoji?.trim() === '') {
                mood_status_message = '';
            }
            $('#status-modal').modal('hide');
            taoh_update_mood_status(ntw_room_key, my_pToken, mood_status_message);
        }

        function update_mood_status(mood_status) {
            const default_emoji = 'default';
            let [statusText, emoji] = mood_status ? mood_status.split('###') : ['', default_emoji];
            if (!emoji) emoji = default_emoji;
            $('#loadEmojiImg').attr('src', _taoh_cdn_main_prefix + '/images/emojis/' + emoji + '.svg');
            $('#my_status').val(statusText.trim());
        }

        function taoh_update_mood_status(room_hash, ptoken, mood_status = '') {
            var data = {
                'ops': 'moodstatus',
                'status': 'post',
                'code': _taoh_ops_code,
                'key': ptoken,
                'keyslug': room_hash,
                'mood_status': mood_status
            };

            $.ajax({
                url: _taoh_cache_chat_proc_url,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (response, textStatus, jqXHR) {
                    if (response.success && response.output['mood_status']) {
                        update_mood_status(response.output['mood_status']);
                    } else {
                        update_mood_status('');
                    }
                }
            });
        }

        function taoh_get_mood_status() {
            var data = {
                'ops': 'moodstatus',
                'status': 'get',
                'code': _taoh_ops_code,
                'key': my_pToken,
                'keyslug': ntw_room_key,
                'cfcc90': 1
            };

            $.ajax({
                url: _taoh_cache_chat_proc_url,
                type: 'GET',
                dataType: 'json',
                data: data,
                success: function (response, textStatus, jqXHR) {
                    if (response.success && response.output['mood_status']) {
                        update_mood_status(response.output['mood_status']);
                    } else {
                        update_mood_status('');
                    }
                }
            });
        }

        function showEmoji() {
            $('#emoji_section').show();
        }

        function removeEmoji() {
            $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_cdn_main_prefix + '/images/emojis/default.svg'}" alt="emoji">`);
            $('#choosen_emoji').val('');
        }

        function chooseEmoji(id) {
            $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_cdn_main_prefix + '/images/emojis/' + id + '.svg'}" alt="emoji">`);
            $('#choosen_emoji').val(id);
        }

        // /My Mood Status

        function taoh_network_update_online() {
            var data = {
                'taoh_action': 'taoh_network_update_online',
                'keyslug': ntw_room_key,
                'ptoken': my_pToken,
                'ticketInfo': ticketInfo,
                'geo_enable': <?= $geo_enabled ?? 0 ?>,
            };

            if (data.geo_enable == 1) {
                data.latitude = "<?php echo $lat ?? ''; ?>";
                data.longitude = "<?php echo $long ?? ''; ?>";
            }

            $.post(_taoh_site_ajax_url, data, function (response) {

            }).fail(function () {
                loader(false, loaderArea);
                //console.log("Network update online failed!");
            });
        }

        function taoh_load_network_entries(call_from = '', serverFetch = false) {

            PARTICIPANT_MEMBERS_COUNT = 0;

            let radius = $('#radius').val();
            let q = $("#query").val();

            if (q.trim() != '' && opt_search != 1) {
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
                'geo_enable': <?= $geo_enabled ?? 0 ?>,
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
                                // if (ntw_entries_cleared) {
                                //     ntw_entries_cleared = 0;
                                    if (intao_data?.values) {
                                        process_network_entries(data, intao_data.values);
                                    } else {
                                        process_network_entries(data, updateData);
                                    }
                                // }
                                return;
                            }

                            ntwEntriesETag = jqXHR.getResponseHeader('taoh-etag') ?? null;

                            if (response.success && response.output) {
                                updateData.success = true;
                                updateData.items = response['output']['items'];
                                updateData.totalcount = response['output']['totalcount'];
                                updateData.returncount = response['output']['returncount'];
                                let search_term_opt = response['output']['search_term'];


                                const clientTerm = (q ?? '').trim().toLowerCase();
                                const serverTerm = (search_term_opt ?? '').trim().toLowerCase();
                                const isSearchMode = Number(opt_search) === 1;

                                if (!serverTerm) {
                                    IntaoDB.setItem(objStores.ntw_store.name, {
                                        taoh_ntw: ntw_entries_key,
                                        values: updateData,
                                        timestamp: Date.now()
                                    });
                                }

                                const shouldRender = (isSearchMode && clientTerm && clientTerm === serverTerm) ||
                                    (!isSearchMode && !serverTerm);

                                if (shouldRender) {
                                    process_network_entries(data, updateData);
                                }
                            }
                        },
                        error: function (xhr, status, error) {
                            loader(false, loaderArea);
                           // console.log('Error Fetching Network Entries : ' + error);
                        }
                    });
                }
            });
        }

        async function process_network_entries(data, response) {

            CURRENT_BLOCK_VISITING = 'PARTICIPANT';


            if (response.success && Object.keys(data).length > 0) {
                let networkList = response['items'] ?? {};
                await render_network_member_template(networkList, networkArea);
                loader(false, loaderArea);
                $('#participants_refresh').find('i').removeClass('fa-spinner fa-spin').addClass('fa-refresh');
                console.log("ALL ENTRIES ARE FETCHED SUCCESSFULLY");
            } else {
                if(opt_search == 0) $('#speed_networking_channel_block').hide();
                show_empty_network_entries_screen(networkArea);
                loader(false, loaderArea);
                $('#participants_refresh').find('i').removeClass('fa-spinner fa-spin').addClass('fa-refresh');
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
                        let emojisrc = _taoh_cdn_main_prefix + '/images/emojis/' + emoji + '.svg';
                        $('#loadEmojiImg').attr('src', emojisrc);
                        $('#selected-emoji img').attr('src', emojisrc);
                        $('#choosen_emoji').val(emoji);
                    } else {
                        let emojisrc = _taoh_cdn_main_prefix + '/images/emojis/default.svg';
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

                                    PARTICIPANT_MEMBERS_COUNT++;
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
                                Chat <i class="la la-angle-double-right ml-1"></i></button>`;

                                    const chatButton_blank = `<a href="${chatButton_blank_url}" target="_blank" class="btn btn-sm" title="Open chat in new tab" style="white-space: nowrap;border: 1px solid #c3c3c3;">
                                    <i class="fa fa-external-link" aria-hidden="true"></i></a>`;

                                    htmlcontent += `<div class="participant-n-list position-relative ${ntw_entries_cls} entry_${totalentries}  px-3 py-2 mb-3">
                                    <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 12px;">
                                    <a data-ptoken="${l.ptoken}" data-chatwith="${l.ptoken}" data-pagename="${pagename}" class="d-flex flex-column align-items-center chat-username openProfileSideBar" style="gap: 3px;" href="javascript:void(0);" data-profile_token="${l.ptoken}">
                                        <img class="lazy par-list-pro" data-idx="${kk}" src="${avatarSrc}" alt="${l.chat_name}">
                                        <p class="text-capitalize p-type-badge">${l.type ? l.type : 'Professional'}</p>
                                    </a>

                            <div style="flex: 1; min-width: 230px">
                                <div class="d-flex flex-wrap justify-content-between flex-column flex-lg-row flex-lg-nowrap align-items-lg-center my-1" style="gap: 12px;">
                                    <div>

                                        <div class="d-flex align-items-center mb-1" style="gap: 12px; cursor: pointer;">
                                            <a data-ptoken="${l.ptoken}" class="par-name text-capitalize chat-username openProfileSideBar" data-chatwith="${l.ptoken}" data-profile_token="${l.ptoken}">${l.chat_name}</a>

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
                                        <div class="d-flex align-items-center flex-wrap">
                                            ${ticket_type_name !='' ?`<p class="par-ticket-type mb-1 d-flex align-items-center mr-2">
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
                                        <p class="par-loc mb-1 d-flex align-items-center">${l.full_location ? '<svg class="mr-1" width="11" height="13" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#636161"></path></svg>' + l.full_location : ''}</p>
                                        <p class="par-company mb-1 d-flex align-items-center">${(roles && roles.length) ? '<svg class="mr-1" width="11" height="11" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="#636161"></path></svg>' + generateRoleHTML(roles, 1) + ', &nbsp;' : ''}
                                        ${(companies && companies.length) ? `<svg class="mr-1" width="11" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#636161"></path></svg>` + generateCompanyHTML(companies, 1) : ''}</p>
                                        <!-- dropdown show and hide -->
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
                                    </div>`;


                                    // if ($.trim(userMoodStatus) != '') {
                                    //     htmlcontent += `    <p class="live_status p-list mb-1" title='${userMoodStatus}'>${userMoodStatus}</p>`;
                                    // }

                                    if ($.trim(userMoodStatus) != '') {
                                        const maxLength = 50;
                                        let shortText = userMoodStatus.substring(0, maxLength);
                                        if (userMoodStatus.length > maxLength) {
                                            htmlcontent += `
                                                <span class="mood_status_div mb-1" title="${userMoodStatus}">
                                                    <span class="live_status p-list short-text">${shortText}...</span>
                                                    <span class="full-text live_status p-list" style="display:none;">${userMoodStatus}</span>
                                                    <a href="javascript:void(0);" class="toggle-text-usermood text-primary fs-12">show more</a>
                                                </span>
                                            `;
                                        } else {
                                            htmlcontent += `
                                                <p class="live_status p-list mb-1" title="${userMoodStatus}">
                                                    ${userMoodStatus}
                                                </p>
                                            `;
                                        }
                                    }

                                    htmlcontent += `
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
                        <img class="no-network-place" src="${_taoh_cdn_main_prefix + '/images/empty_network.png'}" width="300" alt="no-network">
                    </div>
                     <div class="col-lg-12" style="text-align: center;">
                        You are the first here, wait or check into other channels,
                        while people are on their way,
                        drop in an introduction on channels, checkout channels or go back and check oppurtunities at other places,
                        keeping this page open."
                        <br>
                        - #NetworkingTeam
                     </div>
                </div>
            </div>`);
        }

        function searchFilter() {
            ntwEntriesETag = null;
            opt_search = 1;
            let queryString = $("#query").val();

            if (queryString.trim() == '') {
                opt_search = 0;
            }
            networkArea.empty();
            loader(true, loaderArea, 75);
            taoh_load_network_entries('', true);
        }

        function zoomin() {
            var radius = $('#radius').val();
            var newrad = parseInt(radius) + parseInt(100);
            if (newrad <= maxradius) {
                $('#radius').val(newrad);
                loader(true, loaderArea, 75);
                taoh_load_network_entries('', true);
            }

            if (newrad == minradius) {
                $('#zoom_out').attr('disabled', true);
                $('#zoom_in').attr('disabled', false);
            } else if (newrad == maxradius) {
                $('#zoom_in').attr('disabled', true);
                $('#zoom_out').attr('disabled', false);
            } else {
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
                taoh_load_network_entries('', true);
            }

            if (newrad == minradius) {
                $('#zoom_out').attr('disabled', true);
                $('#zoom_in').attr('disabled', false);
            } else if (newrad == maxradius) {
                $('#zoom_in').attr('disabled', true);
                $('#zoom_out').attr('disabled', false);
            } else {
                $('#zoom_in').attr('disabled', false);
                $('#zoom_out').attr('disabled', false);
            }
        }

        // Observe a specific list for changes
        function observeList(listSelector, iconSelector) {
            const targetNode = document.querySelector(listSelector);
            if (!targetNode) return;

            const observer = new MutationObserver(() => {
                toggleCollapseIcon(listSelector, iconSelector);
            });

            observer.observe(targetNode, { childList: true, subtree: true });
        }

        function getOrganizerOnlineStatus() {
            $.ajax({
                url: _taoh_chat_net_url,
                type: 'GET',
                dataType: 'json',
                data: {
                    ops: 'organizerPilotStatus',
                    action: 'get',
                    keyslug: '',
                    key: '',
                    eventtoken: eventToken,
                    code: _taoh_ops_code,
                },
                success: function (response) {
                    const $organizerBlock = $('#organizer-block');
                    const $organizerChannelList = $('#organizerChannelList');

                    const isOnline = response.success && response.status === 1;

                    // Handle organizer visibility
                    $organizerChannelList.toggle(isOnline);

                    //if (isOnline || _can_delete_all_msg == 1) {
                    if (isOnline) {
                        $organizerBlock.removeClass('d-none');
                        $('.chat_with_organizer_div').removeClass('d-none');
                    } else {
                        $organizerBlock.addClass('d-none');
                        $('.chat_with_organizer_div').addClass('d-none');
                    }

                    organizerOnline = isOnline ? 1 : 0;
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching online status:', error);
                }
            });
        }

        function debounceLeadingTrailing(func, delay) {
            let timeoutId;
            let leadingCalled = false;

            return function() {
                const context = this;
                const args = arguments;

                // Leading edge
                if (!leadingCalled) {
                    func.apply(context, args);
                    leadingCalled = true;
                }

                // Clear existing timer
                clearTimeout(timeoutId);

                // Trailing edge
                timeoutId = setTimeout(() => {
                    func.apply(context, args);
                    leadingCalled = false; // reset for next typing session
                }, delay);
            };
        }
        $(document).on('click', '.send_transcript_btn', function () {
            const currentElem = $(this);
            const channelId = currentElem.getSyncedData('channelid');
            const channelType = currentElem.getSyncedData('channeltype') || 1;
            const channelName = currentElem.getSyncedData('channelname') || '';

            //console.log("Channel ID:", channelId, channelType, channelName);

            if (channelId) {
                $('.send_transcript').hide();
                $('#loader_transcript').addClass('show');

                $.post(_taoh_site_ajax_url, {
                    taoh_action: 'taoh_forward_channel_transcript',
                    keyslug: ntw_room_key,
                    event_token: eventToken,
                    title: eventTitle,
                    channel_id: channelId,
                    channel_type: channelType,
                    keyword: ntw_keyword,
                    channel_name: channelName,
                    ptoken: my_pToken,
                }, function (response) {
                    $('.send_transcript').show();
                    $('#loader_transcript').removeClass('show');

                    jq_confirm_alert('Success', 'We will send the transcript to your email shortly!', 'green', 'Ok');

                }).fail(function () {
                    $('.send_transcript').show();
                    $('.loader_transcript').removeClass('show');
                    //console.log("Forward transcript failed");
                });
            }
        });
    </script>
<script>
    var _speed_networking_enable = 0;

    const roomslug = "<?php echo $keytoken ?>";
    //let my_pToken = '<?php echo $ptoken ?? ''; ?>';

    async function saveChannelToDB(channel_id, messages, metadata, update_timestamp = 0) {
        const channelKey = `channel_${channel_id}`;
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);

        let updatedResponse = channelData?.values || {};
        // Ensure existing messages array
        updatedResponse.messages = updatedResponse.messages || [];

        // Index existing messages by message_id for quick lookup
        const existingMap = new Map(updatedResponse.messages.map(m => [m.message_id, m]));

        if (!Array.isArray(messages)) {
            messages = [messages];
        }

        var lastTimestamp = 0;
        for (const msg of messages) {

            // if ('reply_count' in msg) {
            //     delete msg.reply_count;
            // }

            const existing = existingMap.get(msg.message_id);

            if (!existing) {
                // Message not in cache → push it
                updatedResponse.messages.push(msg);
            } else {
                // Compare updated_timestamp (or fallback to timestamp if missing)
                const existingUpdated = existing.updated_timestamp || existing.timestamp;
                const incomingUpdated = msg.updated_timestamp || msg.timestamp;

                if (incomingUpdated > existingUpdated) {
                    // Replace with newer version
                    const index = updatedResponse.messages.findIndex(m => m.message_id === msg.message_id);
                    if (index !== -1) {
                        updatedResponse.messages[index] = msg;
                    }
                }
            }
            lastTimestamp = msg.timestamp;
        }

        const last_fetch_timestamp = channelData?.last_fetch_stamp || 0;
        const last_read_timestamp = channelData?.last_read_stamp || 0;
        const last_update_stamp = update_timestamp == 1 ? lastTimestamp : (channelData?.last_update_stamp || 0);

        await IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: channelKey,
            values: updatedResponse,
            timestamp: Date.now(),
            last_fetch_stamp: last_fetch_timestamp,
            last_update_stamp,
            last_read_stamp: last_read_timestamp
        });

    }

    async function setHaveUpdateStatus(channel_id, have_update) {
        const channelKey = `channel_${channel_id}`;
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);

        let updatedResponse = channelData?.values || {};
        const last_fetch_timestamp = channelData?.last_fetch_stamp || 0;
        const last_read_timestamp = channelData?.last_read_stamp || 0;
        const last_update_stamp = channelData?.last_update_stamp || 0;

        await IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: channelKey,
            values: updatedResponse,
            timestamp: Date.now(),
            last_fetch_stamp: last_fetch_timestamp,
            last_update_stamp,
            last_read_stamp: last_read_timestamp,
            have_update
        });
    }

    async function getHaveUpdateStatus(channel_id) {
        const channelKey = `channel_${channel_id}`;
        const channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);

        // Return have_update or default to false if not found
        return channelData?.have_update ?? 0;
    }

    $(document).on("click", ".view-replies, .btnReply", function () {
        let parentId = $(this).data("id");
        window.replyToMessageId = parentId;
        $('.channel-reply-message-block .conversation-name').text($(this).attr('data-chat_name'));
        $('.channel-reply-message-block .conversation-text').html($(this).attr('data-msg'));

        loadReplies(parentId);
    });

    $(document).on('click', '.emoji_btn', function() {
        $('.reaction-popup').hide();
        $(this).closest('.user-chat-content').siblings('.reaction-popup').show();
        //$(this).siblings('.reaction-popup').show();
    });

    $(document).on("click", "#closeReplyPanel", function () {
        $("#replyPanel").removeClass("open");
        $("#replyMessages").html("");
    });

    async function loadReplies(parentId) {
        //$("#replyMessages").html("Loading...");

        $("#chat-reply-conversation-list").html("");
        $('#channel-reply-message-block').attr('data-parent-message-id', parentId);

        // Get channel_id from global (or pass it)
        let channel_id = currentChannelId;
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, `channel_${channel_id}`);

        let replies = [];
        if (channelData?.values?.messages) {
            replies = channelData.values.messages.filter(m => m.parent_message_id == parentId);
        }

        window.replyToMessageId = parentId;

        $('.participants-sidebar').removeClass('d-xl-block');
        loadRightSidebar('reply');

        render_messages(replies, "", 0, "reply");

    }


    async function loadChannelFromDB(channel_id, beforeMessageId = null, limit = 20) {
        const channelKey = `channel_${channel_id}`;
        const channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
        const messages = channelData?.values?.messages || [];

        if(messages.length === 0) {
            await saveChannelMessages(channel_id, 1);
            return false;
        }

        console.log("messages loadChannelFromDB", messages);

        return messages

        // if (!beforeMessageId) return messages;

        // const index = messages.findIndex(msg => msg.message_id == beforeMessageId);

        // if (index === -1) return []; // message not found

        // const start = Math.max(0, index - limit);

        // return messages.slice(start, index).reverse();
    }


    async function saveChannelTimestamp(channel_id, timestamp) {
        const channelKey = `channel_${channel_id}`;
        // Fetch existing record
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
        let updatedResponse = channelData?.values || {};
        let last_fetch_timestamp = channelData?.last_fetch_stamp || 0;
        let last_update_timestamp = channelData?.last_update_stamp || 0;
        // Update just the timestamp
        updatedResponse.timestamp = timestamp;
        // Save back
        await IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: channelKey,
            values: updatedResponse,
            timestamp: Date.now(),
            last_fetch_stamp: last_fetch_timestamp,
            last_update_stamp: last_update_timestamp,
        });
    }

    async function getChannelTimestamp(channel_id) {
        const channelKey = `channel_${channel_id}`;
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
        return channelData?.last_update_stamp || 0;
    }


    // =======================================
    // Adaptive Polling
    // =======================================
    let channelTimestamps = {};

    // Open modal on button click
    $(".create_channel").on("click", function() {
        //$('#channelType').val(taohChannelDefault);
        $('#channelType').val(taohChannelDefault);
        const modal = new bootstrap.Modal(document.getElementById('createChannelModal'));
        modal.show();
    });

     $(".create_speed_channel").on("click", function() {
        //$('#channelType').val(taohChannelDefault);
        $('#channelType').val(taohChannelSpeedNtw);
        const modal = new bootstrap.Modal(document.getElementById('createChannelModal'));
        modal.show();
    });

    $(document).on("click", ".btnDeleteMsg", function () {
        const messageId = $(this).data("id");
        const channelId = currentChannelId;

        if (!confirm("Are you sure you want to delete this message?")) return;

        $.ajax({
            url: _taoh_site_ajax_url,
            type: "post",
            data: {
                roomslug: roomslug,
                taoh_action: "taoh_ntw_delete_message",
                channel_id: channelId,
                message_id: messageId,
                keyword: ntw_keyword,
                key: my_pToken
            },
            dataType: "json",
            success: async function (res) {
                console.log("taoh_ntw_delete_message", res.message);
                if (res.success) {
                    await saveChannelToDB(channelId, res.message, "", 1);
                    $(`.chat-list[data-frm_message_id='${messageId}']`).remove();
                    $('#chat-reply-sidebar').hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("Delete failed:", error);
            }
        });
    });


    // Save channel when user clicks Create
    $("#createChannelForm").on("submit", async function (e) {
        e.preventDefault();
        const channelName = $("#channelName").val()?.trim();
        const channelDescription = $("#channelDescription").val()?.trim() || "";
        const channelType = $('#channelType').val();
        const channelPasscode = $('#channelpasscode').val()?.trim() || "";
        const channelVisibility = $('input[name="channelvisibility"]:checked').val()?.trim() || 'public';
        const channelTicketType = $('#channel_ticket_type').val()?.trim() || "";

        if (!channelName) {
            taoh_set_error_message('Please enter a channel name.');
            return;
        }

        const safeName = (channelName);
        const input = [roomslug, safeName].map(String).sort().join('_');
        const channelId = await generateSecureSlug(input, 16);
        const channelData = JSON.stringify({
            name: channelName,
            description: channelDescription,
            ptoken: my_pToken
        });
        const channelMembers = JSON.stringify([my_pToken]); // Default to creator only

        const submit_btn = $(this).find('button[type="submit"]');
        const submit_btn_icon = submit_btn.find('i');
        submit_btn.prop('disabled', true);
        submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            data: {
                taoh_action: 'taoh_ntw_create_channel',
                roomslug,
                keyword: ntw_keyword,
                channel_id: channelId,
                channel_type: channelType,
                channel_data: channelData,
                channel_members: channelMembers,
                channel_passcode: channelPasscode,
                channel_visibility: channelVisibility,
                channel_ticket_type: channelTicketType,
                key: my_pToken
            },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    if (chatWindow == 'browse_channels') {
                        runBrowseChannels(1);
                    }

                    // reload user channel list
                    getNTWUserChannels({
                        roomslug,
                        keyword: ntw_keyword,
                        type: taohChannelDefault,
                        my_pToken
                    }, true).then(({requestData, response}) => {
                        return renderChannelList(response.channels, tempUserChannelArray);
                    }).then(() => {
                        const $el = $(`#channel-${channelId}`);
                        if ($el.length) $el.trigger('click');   // open the newly created channel
                    });

                    resetFormIfExists("#createChannelForm");

                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);

                    const modalEl = document.getElementById('createChannelModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.hide();

                    showAddChannelMembersModal(channelId, channelType);

                } else {
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);
                    taoh_set_error_message("Error creating channel: " + (res.error || "Unknown error"), false, 'toast-middle', [
                        {
                            text: 'OK',
                            action: () => {},
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        }
                    ]);
                }
            },
            error: function (xhr, status, error) {
                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                submit_btn.prop('disabled', false);
                taoh_set_error_message("Failed to create channel.", false, 'toast-middle', [
                    {
                        text: 'OK',
                        action: () => {},
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    }
                ]);
            }
        });
    });

    $(document).on('click', '.message-item-dot', function() {
        var frm_message_id = $(this).attr('data-frm_message_id');
        var channel_id = $(this).attr('data-channel_id');
        $('.pin_msg[data-channel_id="'+channel_id+'"]').removeClass('d-flex').addClass('d-none');
        $('.pin_msg[data-frm_message_id="'+frm_message_id+'"]').removeClass('d-none').addClass('d-flex');
        $(this).addClass('active').siblings('.message-item-dot').removeClass('active');
    });

    async function renderChannelList(channelArray = [], tempUserChannelArray = []) {
        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        //console.log("channelArray channelArray", channelArray);

        // Merge and de-dupe by id (tempUserChannelArray overrides)
        const byId = new Map();
        [...channelArray, ...tempUserChannelArray].forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        let watch_party = '';
        let mychannel_html = '';
        $('#myChannelList').html("");
        $('#channelList').html("");
        $('#watch_partyChannel').html("");
        const store = objStores.ntw_store.name;
        for (const item of list) {
            const channelId = item.id;
            const channelType = item.type;
            const channelName = item.data?.name || '(unnamed)';
            const channelDescription = taoh_desc_decode_new(item.data?.description) || 'description';

            const channelStarred = item.data?.starred ?? 0;
            const channelDefault = item.data?.isDefault ?? 0;
            const channelCreatedBy = item.data?.ptoken;

           // console.log(channelName, channelDefault);

            const channelKey = ['channel', channelId].filter(Boolean).join('_');
            const channelExisting = await IntaoDB.getItem(store, channelKey);

            const { last_fetch_stamp = 0, last_update_stamp = 0, last_read_stamp = 0 } = channelExisting || {};

            let hasUpdates = await getHaveUpdateStatus(channelId);

            let channelLastStamp = channelExisting?.last_update_stamp;
            var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
            console.log("channelExisting?.last_update_stamp == "+channelId+"~"+channelName, last_send_msg_timestamp[channelId], channelLastStamp, last_update_stamp, last_read_stamp);
            if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                if(last_update_stamp > last_read_stamp) {
                    hasUpdates = 1;
                } else {
                    hasUpdates = 0;
                }
            } else {
                hasUpdates = 0;
            }

            let isTempChannel = item.is_temp || false;
            let isActive = (currentChannelId && currentChannelId === channelId);

            if(channelDescription == 'Presentation Room' || channelDescription == 'watch-party') {
                watch_party = `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${(hasUpdates ==  1) ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-channel_name="${channelName}">
                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${channelName}">
                            <div class="flex-grow-1 overflow-hidden">
                                <span class="text-truncate mb-0 channel_name text-capitalize">
                                    <i class="la ${item.visibility === 'private' ? 'la-lock' : 'la-hashtag'} mr-1"></i>${channelName}
                                </span>
                            </div>
                        </div>
                    </a>
                </li>`;
            }

            html = `
            <li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${hasUpdates ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''}"
                data-channel_passcode=""
                data-speed_networking="0"
                data-channel_ticket_slug=""
                data-name="channel"
                data-channel_id="${channelId}"
                data-channel_type="${channelType}"
                data-channel_name="${channelName}"
                data-channelid="${channelId}"
                data-channeltype="${channelType}"
                data-channelname="${channelName}">

                <a class="d-flex align-items-center justify-content-between" href="javascript:void(0);">
                    <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${channelName}">
                        <div class="flex-shrink-0 me-2">
                            <div class="chat-user-img align-self-center">
                                <div class="avatar-xs">
                                    <span class="avatar-title bg-white rounded-circle border text-dark">
                                        <i class="la ${item.visibility === 'private' ? 'la-lock' : 'la-hashtag'}"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <span class="text-truncate mb-0 channel_name text-capitalize">
                                ${channelName}
                            </span>
                        </div>
                    </div>
                    <div class="dropdown ml-2">
                        <span href="javascript:void(0);" class="text-muted" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i id="action_loader-${channelId}" class="la la-ellipsis-v"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-right">
                            <span class="dropdown-item add_channel_member" href="javascript:void(0);" data-channel_type="${channelType}" data-channel_id="${channelId}">Add Members</span>
                            <span class="dropdown-item star_channel" data-status="${channelStarred}" href="javascript:void(0);" data-channel_id="${channelId}"  data-channel_type="${channelType}">${(channelStarred == 1) ? 'Unfavorite' : 'Favorite'}</span>
                            <span class="dropdown-item channel_info_view" href="javascript:void(0);" data-channel_id="${channelId}">View</span>
                            <span class="dropdown-item delete_channel ${(channelDefault == 0 && channelCreatedBy == my_pToken) ? '' : 'd-none'}" href="javascript:void(0);" data-channel_id="${channelId}">Delete</span>
                        </div>
                    </div>
                </a>
            </li>`;

            if (html != '') {
                if (channelStarred == 1) {
                    $('#myChannelList').append(html);
                } else {
                    $('#channelList').append(html);
                }

                if (!chatwithchannelprocessed && chatwithchannelid && chatwithchanneltype == taohChannelDefault) {
                    initChannelChat(chatwithchannelid);
                }
            }

            if(watch_party != ''){
                $('#watch_partyChannel').append(watch_party);
            }
        }

        if($('.myChannelList li').length > 0) {
            $('.my_channel_div').removeClass('d-none');
        } else {
            $('.my_channel_div').addClass('d-none');
        }

    }

    async function renderSpeedChannelList(channelArray = [], tempUserChannelArray = []) {
        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        // Merge and de-dupe by id (tempUserChannelArray overrides)
        const byId = new Map();
        [...channelArray, ...tempUserChannelArray].forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        const store = objStores.ntw_store.name;
        for (const item of list) {
            const channelId = item.id;
            const channelType = item.type;
            const channelName = item.data?.name || '(unnamed)';

            const channelKey = ['channel', channelId].filter(Boolean).join('_');
            const channelExisting = await IntaoDB.getItem(store, channelKey);

            const { last_fetch_stamp = 0, last_update_stamp = 0 } = channelExisting || {};
            let hasUpdates = (currentChannelId !== channelId) && (Number(last_fetch_stamp) > Number(last_update_stamp));

            let isTempChannel = item.is_temp || false;
            let isActive = false;
            //let isActive = (currentChannelId && currentChannelId === channelId);
            if ($('#speed_networking').is(':visible')) {
                $('#channelList .channel-item').each(function() {
                    $(this).removeClass('active');
                });
                isActive = true;
            }

            html += `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${isTempChannel ? 'temp' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-channel_name="${channelName}">
                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${channelName}">
                            <div class="flex-shrink-0 me-2">
                                <div class="chat-user-img align-self-center">
                                    <div class="avatar-xs">
                                        <span class="avatar-title bg-white rounded-circle border text-dark">
                                            <img src="https://cdn.tao.ai/assets/images/speed_networking_icon.svg">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden text-truncate">
                                <span class="mb-0 channel_name text-capitalize">${channelName}</span>
                            </div>
                            <div class="flex-shrink-0">
                                <div data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Refresh">
                                    <button type="button" class="btn btn-success btn-sm" id="speednetworking_refresh_btn" data-channel_id="${channelId}">
                                        <i class="fa align-middle fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>`;
        }

        $('#speedChannelList').html(html);
    }

    async function renderDMChannelList(channelArray = []) {
        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        if(channelArray.length == 0) {
            $('.dm-lable').addClass('d-none');
        } else {
            $('.dm-lable').removeClass('d-none');
        }

        // Merge and de-dupe by id
        const byId = new Map();
        channelArray.forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        const store = objStores.ntw_store.name;
        for (const item of list) {
            let channelMembers = item.members || [];
            let to_ptoken = getPeerPToken(channelMembers, my_pToken);
            if (!to_ptoken) continue;
            let toUserInfo = await getUserInfo(to_ptoken, 'public');

            let channelId = item.id;
            let channelType = item.type;
            let channelName = toUserInfo.chat_name || '(unnamed)';

            let channelKey = ['channel', channelId].filter(Boolean).join('_');
            let channelExisting = await IntaoDB.getItem(store, channelKey);

            let fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${toUserInfo?.avatar?.trim() || 'default'}.png`;
            let userAvatarSrc = await buildAvatarImage(toUserInfo.avatar_image, fallbackSrc);

            let { last_fetch_stamp = 0, last_update_stamp = 0, last_read_stamp = 0 } = channelExisting || {};

            let hasUpdates = await getHaveUpdateStatus(channelId);
            let channelLastStamp = channelExisting?.last_update_stamp;
            var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
            console.log("channelExisting?.last_update_stamp == "+channelId+"~"+channelName, last_send_msg_timestamp[channelId], channelLastStamp, last_update_stamp, last_read_stamp);
            if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                if(last_update_stamp > last_read_stamp) {
                    hasUpdates = 1;
                } else {
                    hasUpdates = 0;
                }
            } else {
                hasUpdates = 0;
            }

            let isTempChannel = item.is_temp || false;
            let isActive = (currentChannelId && currentChannelId === channelId);

            html += `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${(hasUpdates ==  1) ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-avatar_src="${userAvatarSrc}" data-channel_name="${channelName}">
                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${channelName}">
                            <div class="flex-shrink-0 me-2">
                                <div class="chat-user-img align-self-center ms-0">
                                    <img src="${userAvatarSrc}" class="rounded-circle avatar-xs" alt="user-avatar">
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden w-hide">
                                <span class="text-truncate mb-0 channel_name text-capitalize">${channelName}</span>
                            </div>
                        </div>
                    </a>
                </li>`;
        }

        $('#dmChannelList').html(html);

        if(!chatwithchannelprocessed && chatwithchannelid && chatwithchanneltype == taohChannelDm) {
            initChannelChat(chatwithchannelid);
        }
    }

    async function renderExhibitorChannelList(channelArray = []) {
        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        // Merge and de-dupe by id
        const byId = new Map();
        channelArray.forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        $('#myExhibitorChannelList').html("");
        $('#exhibitorChannelList').html("");
        const store = objStores.ntw_store.name;
        for (const item of list) {
            var channelId = item.id;
            var channelType = item.type;
            var channelName = item.data?.name || '(unnamed)';
            var exhibitorLogoSrc = item.data?.exhibitor_logo || '';
            var exhibitorRaffles = item.data?.exhibitor_raffles || 0;
            var channelStarred = item.data?.starred ?? 0;

            var channelKey = ['channel', channelId].filter(Boolean).join('_');
            var channelExisting = await IntaoDB.getItem(store, channelKey);

            var { last_fetch_stamp = 0, last_update_stamp = 0 , last_read_stamp = 0 } = channelExisting || {};

            let hasUpdates = await getHaveUpdateStatus(channelId);
            let channelLastStamp = channelExisting?.last_update_stamp;
            var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
            console.log("channelExisting?.last_update_stamp == "+channelId+"~"+channelName, last_send_msg_timestamp[channelId], channelLastStamp, last_update_stamp, last_read_stamp);
            if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                if(last_update_stamp > last_read_stamp) {
                    hasUpdates = 1;
                } else {
                    hasUpdates = 0;
                }
            } else {
                hasUpdates = 0;
            }

            let isTempChannel = item.is_temp || false;
            let isActive = (currentChannelId && currentChannelId === channelId);

            let channelInfoResponse = await getNTWChannelById({
                roomslug,
                global_slug: eventToken,
                keyword: ntw_keyword,
                channel_id: channelId,
                type: channelType,
                my_pToken
            });
            const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);
            console.log("channelInfo render channel", channelInfo);

            var visibility = 0;
            if(channelInfo?.data?.exh_state == 'active' || channelInfo?.data?.exh_state == 'live') {
                visibility = 1;
            }

            html = `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${(hasUpdates ==  1) ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''} ${(visibility == 0) ? 'd-none' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-channel_name="${channelName}">
                        <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                            <div class="channel_btn flex-grow-1 d-flex align-items-center justify-content-center overflow-hidden" title="${channelName}">
                                <div class="flex-shrink-0 me-2">
                                    <div class="chat-user-img online align-self-center">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-white rounded-circle border text-white">
                                                <img src="${exhibitorLogoSrc}" class="rounded-circle avatar-xs" alt="user-avatar">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-grow-1 align-items-center overflow-hidden w-hide" style="gap: 8px;">
                                    <span class="text-truncate mb-0 channel_name text-capitalize">${channelName}</span>
                                    <!-- raffle svg -->
                                    <svg class="w-hide" style="min-width: fit-content;display: ${parseInt(exhibitorRaffles, 10) === 1 ? 'block' : 'none'};" width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.83691 1.74688L5.72051 3.25H5.6875H3.85938C3.29824 3.25 2.84375 2.79551 2.84375 2.23438C2.84375 1.67324 3.29824 1.21875 3.85938 1.21875H3.91523C4.29355 1.21875 4.64648 1.41934 4.83691 1.74688ZM1.625 2.23438C1.625 2.6 1.71387 2.94531 1.86875 3.25H0.8125C0.363086 3.25 0 3.61309 0 4.0625V5.6875C0 6.13691 0.363086 6.5 0.8125 6.5H12.1875C12.6369 6.5 13 6.13691 13 5.6875V4.0625C13 3.61309 12.6369 3.25 12.1875 3.25H11.1312C11.2861 2.94531 11.375 2.6 11.375 2.23438C11.375 1.00039 10.3746 0 9.14062 0H9.08477C8.2748 0 7.52324 0.429102 7.11191 1.12734L6.5 2.1709L5.88809 1.12988C5.47676 0.429102 4.7252 0 3.91523 0H3.85938C2.62539 0 1.625 1.00039 1.625 2.23438ZM10.1562 2.23438C10.1562 2.79551 9.70176 3.25 9.14062 3.25H7.3125H7.27949L8.16309 1.74688C8.35605 1.41934 8.70644 1.21875 9.08477 1.21875H9.14062C9.70176 1.21875 10.1562 1.67324 10.1562 2.23438ZM0.8125 7.3125V11.7812C0.8125 12.4541 1.3584 13 2.03125 13H5.6875V7.3125H0.8125ZM7.3125 13H10.9688C11.6416 13 12.1875 12.4541 12.1875 11.7812V7.3125H7.3125V13Z" fill="url(#paint0_linear_10005_1644)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_10005_1644" x1="6.5" y1="0" x2="6.5" y2="13" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#FF6600"/>
                                        <stop offset="1" stop-color="#FFD200"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                </div>

                                 <div class=" d-none w-hide">
                                    <div class="flex-shrink-0 ms-2">
                                        <svg width="14" height="14" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="5.5" cy="5.5" r="5.5" fill="#B2F2BB"/>
                                            <circle cx="5.5" cy="5.5" r="3.5" fill="#009944" fill-opacity="0.64"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2 lh-1 d-none w-hide">
                                    <span class="delete_channel_btn" title="Delete">
                                        <i class="bx bx-trash text-danger fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="dropdown ml-2  d-none">
                                <span href="javascript:void(0);" class="text-muted" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i id="action_loader-${channelId}" class="la la-ellipsis-v"></i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <span class="dropdown-item add_channel_member" href="javascript:void(0);" data-channel_type="${channelType}" data-channel_id="${channelId}">Add Members</span>
                                    <span class="dropdown-item star_channel" data-status="${channelStarred}" href="javascript:void(0);" data-channel_id="${channelId}" data-channel_type="${channelType}">${(channelStarred == 1) ? 'Unfavorite' : 'Favorite'}</span>
                                    <span class="dropdown-item channel_info_view" href="javascript:void(0);" data-channel_id="${channelId}">View</span>
                                </div>
                            </div>
                        </a>
                </li>`;

            if(html != '') {
                if(channelStarred == 1) {
                    $('#myExhibitorChannelList').append(html);
                } else{
                    $('#exhibitorChannelList').append(html);
                }

                if(!chatwithchannelprocessed && chatwithchannelid && chatwithchanneltype == taohChannelExhibitor) {
                    initChannelChat(chatwithchannelid);
                }
            }
        }

        //$('#exhibitorChannelList').html(html);
    }

    async function renderSessionChannelList(channelArray = []) {
        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        // Merge and de-dupe by id
        const byId = new Map();
        channelArray.forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        const store = objStores.ntw_store.name;
        for (const item of list) {
            var channelId = item.id;
            var channelType = item.type;
            var channelName = item.data?.name || '(unnamed)';
            var sessionLogoSrc = item.data?.speaker_logo || '';
            var channelStarred = item.data?.starred ?? 0;

            var channelKey = ['channel', channelId].filter(Boolean).join('_');
            var channelExisting = await IntaoDB.getItem(store, channelKey);

            var { last_fetch_stamp = 0, last_update_stamp = 0, last_read_stamp = 0 } = channelExisting || {};

            let hasUpdates = await getHaveUpdateStatus(channelId);
            let channelLastStamp = channelExisting?.last_update_stamp;
            var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
            console.log("channelExisting?.last_update_stamp == "+channelId+"~"+channelName, last_send_msg_timestamp[channelId], channelLastStamp, last_update_stamp, last_read_stamp);
            if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                if(last_update_stamp > last_read_stamp) {
                    hasUpdates = 1;
                } else {
                    hasUpdates = 0;
                }
            } else {
                hasUpdates = 0;
            }

            let isTempChannel = item.is_temp || false;
            let isActive = (currentChannelId && currentChannelId === channelId);

            let channelInfoResponse = await getNTWChannelById({
                roomslug,
                global_slug: eventToken,
                keyword: ntw_keyword,
                channel_id: channelId,
                type: channelType,
                my_pToken
            });
            const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);
            console.log("channelInfo render channel", channelInfo);

            var visibility = 0;
            if(channelInfo?.data?.spk_state == 'active' || channelInfo?.data?.spk_state == 'live') {
                visibility = 1;
            }

            //let live_status = isJoinEnabled(channelInfo.data);
            let live_status = false;

            html += `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${(hasUpdates ==  1) ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''} ${(visibility == 0) ? 'd-none' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-channel_name="${channelName}">
                        <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                            <div class="channel_btn flex-grow-1 d-flex align-items-center justify-content-center overflow-hidden" title="${channelName}">
                                <div class="flex-shrink-0 me-2">
                                    <div class="chat-user-img online align-self-center">
                                        <div class="avatar-xs">
                                            <span class="avatar-title bg-white rounded-circle border text-white">
                                                <img src="${sessionLogoSrc}" class="rounded-circle avatar-xs" alt="user-avatar">
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-grow-1 align-items-center overflow-hidden w-hide" style="gap: 8px;">
                                    <span class="text-truncate mb-0 channel_name text-capitalize">${channelName}</span>
                                </div>

                                <div class=" d-none w-hide">
                                    <div class="flex-shrink-0 ms-2">
                                        <svg width="14" height="14" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="5.5" cy="5.5" r="5.5" fill="#B2F2BB"/>
                                            <circle cx="5.5" cy="5.5" r="3.5" fill="#009944" fill-opacity="0.64"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ms-2 lh-1 d-none w-hide">
                                    <span class="delete_channel_btn" title="Delete">
                                        <i class="bx bx-trash text-danger fs-18"></i>
                                    </span>
                                </div>
                            </div>
                            ${live_status ? `<i class="fa fa-circle" style="color: #22bb74;font-size: 10px;"></i>` : ''}
                        </a>
                </li>`;
        }

        $('#sessionChannelList').html(html);

        console.log("Test1",chatwithchannelid, chatwithchanneltype);


        if(!chatwithchannelprocessed && chatwithchannelid && chatwithchanneltype == taohChannelSession) {
            console.log("Test2", chatwithchannelid, chatwithchanneltype);
            initChannelChat(chatwithchannelid);
        }
    }

    async function renderOrganizerChannelList(channelArray = []) {

        const parse = v => {
            if (!v || typeof v === 'object') return v || {};
            try {
                return JSON.parse(v);
            } catch {
                return {};
            }
        };

        // Merge and de-dupe by id
        const byId = new Map();
        channelArray.forEach(it => {
            if (!it || typeof it !== 'object') return;
            byId.set(it.id, {...it, data: parse(it.data)});
        });

        // Sort by data.name (case-insensitive), fallback by id
        const list = [...byId.values()].sort((a, b) => {
            const an = (a.data?.name || '').toLowerCase();
            const bn = (b.data?.name || '').toLowerCase();
            const cmp = an.localeCompare(bn);
            return cmp !== 0 ? cmp : String(a.id).localeCompare(String(b.id));
        });

        let html = '';
        const store = objStores.ntw_store.name;
        for (const item of list) {
            var channelMembers = item.members || [];
            //var to_ptoken = getPeerPToken(channelMembers, my_pToken);

            var channelNameArray = item?.data?.name?.split('-');
            var to_ptoken = channelNameArray[2] ?? '';
            var toUserInfo = await getUserInfo(to_ptoken, 'public');
            if (!to_ptoken) continue;

            var channelId = item.id;
            var channelType = item.type;
            var channelName = toUserInfo.chat_name || '(unnamed)';

            var channelKey = ['channel', channelId].filter(Boolean).join('_');
            var channelExisting = await IntaoDB.getItem(store, channelKey);

            var fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${toUserInfo?.avatar?.trim() || 'default'}.png`;
            var userAvatarSrc = await buildAvatarImage(toUserInfo.avatar_image, fallbackSrc);

            var { last_fetch_stamp = 0, last_update_stamp = 0, last_read_stamp = 0 } = channelExisting || {};

            let hasUpdates = await getHaveUpdateStatus(channelId);
            let channelLastStamp = channelExisting?.last_update_stamp;
            var last_send_msg_timestamp = await loadLastSendMsgTimestamp();
            console.log("channelExisting?.last_update_stamp == "+channelId+"~"+channelName, last_send_msg_timestamp[channelId], channelLastStamp, last_update_stamp, last_read_stamp);
            if(last_send_msg_timestamp[channelId] != channelLastStamp) {
                if(last_update_stamp > last_read_stamp) {
                    hasUpdates = 1;
                } else {
                    hasUpdates = 0;
                }
            } else {
                hasUpdates = 0;
            }

            let isTempChannel = item.is_temp || false;
            let isActive = (currentChannelId && currentChannelId === channelId);

            html += `<li id="channel-${channelId}" class="channel-item ${isActive ? 'active' : ''} ${(hasUpdates ==  1) ? 'have_updates' : ''} ${isTempChannel ? 'temp' : ''}" data-channel_passcode="" data-speed_networking="0" data-channel_ticket_slug="" data-name="channel" data-channel_id="${channelId}" data-channel_type="${channelType}" data-channel_name="${channelName}">
                    <a class="d-flex align-items-center justify-content-between" href="javascript: void(0);">
                        <div class="channel_btn flex-grow-1 d-flex align-items-center overflow-hidden" title="${channelName}">
                            <div class="flex-shrink-0 me-2">
                                <div class="chat-user-img align-self-center ms-0">
                                    <img src="${userAvatarSrc}" class="rounded-circle avatar-xs" alt="user-avatar">
                                </div>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <span class="text-truncate mb-0 channel_name text-capitalize">${channelName}</span>
                            </div>
                        </div>
                    </a>
                </li>`;
        }

        $('#organizerChannelList').html(html);

        if(!chatwithchannelprocessed && chatwithchannelid && chatwithchanneltype == taohChannelOrganizer) {
            initChannelChat(chatwithchannelid);
        }
    }


    function initChannelChat(channelId) {
        if (!channelId) return;
        //setTimeout(() => {
            const channelElem = $(`#channel-${channelId}`);
            if (channelElem.length) {
                const firstChannelButton = channelElem.find('.channel_btn').first();
                if (firstChannelButton.length) {

                        firstChannelButton.trigger('click');
                        chatwithchannelprocessed = 1;

                } else {
                    console.warn('No .channel_btn found inside channel element.');
                }
            } else {
                console.warn('Channel element not found for ID:', channelId);
            }
        //}, 1000);
    }


    function send_message(channel_id, channel_type, message_text, is_background_send = 0) {

        message_text = convertMentionsToLinks(message_text);
        var taohAction = "taoh_ntw_send_message";

        var channel_name = $("#channel-chat").attr("data-channel_name");

        NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN = 2;

        return new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'roomslug': roomslug,
                    'keyword': ntw_keyword,
                    'taoh_action': taohAction,
                    'channel_id': channel_id,
                    'channel_type': channel_type,
                    'message': message_text,
                    'key': my_pToken,
                    'taoh_secret': taoh_secret,
                    'room_title': room_title,
                    'source': roomslug,
                    'channel_name': channel_name
                },
                dataType: 'json',
                success: function (data) {

                    saveLastSendMsgTimestamp(channel_id, data.message.timestamp);
                    lastSendMsgChannel = channel_id;
                    saveChannelToDB(currentChannelId, [data.message], {}, 0);

                    if(is_background_send == 0) {
                        $('#chat-send-btn i').removeClass('bx-spin').removeClass('bx-loader-alt').addClass('bxs-send');
                        $("#chat-send-btn").prop("disabled", false);
                        $("#chat_input").prop("disabled", false);
                        render_messages(data.message, "", 1);
                        $('#chat_input').focus();
                    }

                    resolve({
                        status: 200,
                        success: true,
                        response: data
                    });
                },
                error: function (xhr, status, error) {
                    resolve({
                        status: 201,
                        success: false,
                        error: error
                    });
                },
                complete: function () {
                    // setTimeout(fetchTimestamps, pollingInterval);
                }
            });
        });
    }

    function send_reply_message(channel_id, channel_type, message_text) {

        var taohAction = "taoh_ntw_add_reply_message";
        var channel_name = $("#channel-chat").attr("data-channel_name");

        return new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'roomslug': roomslug,
                    'taoh_action': taohAction,
                    'keyword': ntw_keyword,
                    'channel_id': channel_id,
                    'channel_type': channel_type,
                    'message': message_text,
                    'parent_message_id': window.replyToMessageId || null,
                    'key': my_pToken,
                    'room_title': room_title,
                    'source': roomslug,
                    'channel_name': channel_name
                },
                dataType: 'json',
                success: function (data) {

                    saveLastSendMsgTimestamp(channel_id, data.message.timestamp);
                    render_messages(data.message, "", 1, "reply");

                    let parentId = data.message.parent_message_id;
                    $(`.conversation-reply-count[data-id="${parentId}"]`).removeClass('d-none').attr('data-count', data.reply_count).text(`${data.reply_count} ${data.reply_count > 1 ? "replies" : "reply"}`);

                    saveChannelToDB(currentChannelId, [data.message], {}, 0);

                    $('#chat-reply-send-btn i').removeClass('bx-spin').removeClass('bx-loader-alt').addClass('bxs-send');
                    $("#chat-reply-send-btn").prop("disabled", false);
                    $("#chat_reply_input").prop("disabled", false);
                    $('#chat_reply_input').focus();

                    resolve({
                        status: 200,
                        success: true,
                        response: data
                    });
                },
                error: function (xhr, status, error) {
                    resolve({
                        status: 201,
                        success: false,
                        error: error
                    });
                },
                complete: function () {
                    // setTimeout(fetchTimestamps, pollingInterval);
                }
            });
        });
    }

    function highlightChannel(channel) {
        $(`.channel[data-id="${channel}"]`).css("background", "#444");
    }

    async function load_channel_messages(channel_id) {
        var all_messages = await loadChannelFromDB(channel_id, false);

        console.log("all_messages all_messages", all_messages);


        let channelKey = `channel_${channel_id}`;
        let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
        let updatedResponse = channelData?.values || {};

        let last_fetch_timestamp = channelData?.last_fetch_stamp || 0;
        let last_read_timestamp = Date.now();
        let last_update_stamp = channelData?.last_update_stamp || 0;

        await IntaoDB.setItem(objStores.ntw_store.name, {
            taoh_ntw: channelKey,
            values: updatedResponse,
            timestamp: Date.now(),
            last_fetch_stamp: last_fetch_timestamp,
            last_update_stamp,
            last_read_stamp: last_read_timestamp
        });

        if (Array.isArray(all_messages)) {
            //const lastPaginateMessages = all_messages.slice(-20);
            render_messages(all_messages, "", 1);
            //console.log("lastPaginateMessages", lastPaginateMessages);
        } else {
            console.warn("Expected an array but got:", all_messages);
        }
    }

    async function saveChannelInfo(channel_id, channel_type, render = 0) {

        const store = objStores.ntw_store.name;

        return new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'roomslug': roomslug,
                    'taoh_action': 'taoh_ntw_get_channel_info',
                    'channel_id': channel_id,
                    'keyword': ntw_keyword,
                    'channel_type': channel_type,
                    'key': my_pToken
                },
                dataType: 'json',
                success: async function (res) {
                    try {
                        console.log("taoh_ntw_get_channel_info", res.output);
                        //const channelInfoKey = `room_${ntw_keyword}_${roomslug}_${my_pToken}_${channel_type}_channels`;
                        const channelInfoKey = `channel_info_${roomslug}_${channel_id}_${channel_type}`;
                        console.log("taoh_ntw_get_channel_info", channelInfoKey);
                        if(res.output) {
                            const channelExisting = await IntaoDB.getItem(store, channelInfoKey);
                            if (!channelExisting) {
                                console.warn(`⚠️ No record found for channelId: ${channelInfoKey}`);
                                return;
                            }
                            const updatedChannel = {
                                ...channelExisting,
                                values: {
                                    ...channelExisting.values,
                                    channels: (channelExisting.values.channels || []).map(ch => {
                                        if (ch.id === channel_id) {
                                            return {
                                                ...ch,
                                                data: { ...res.output },
                                            };
                                        }
                                        return ch;
                                    }),
                                },
                            };
                            await IntaoDB.setItem(store, updatedChannel);
                            console.warn(`✅ Channel info updated for channelId: ${channelInfoKey}`);
                        }

                        const channelUpdateKey = ['channel', 'info', channel_id].filter(Boolean).join('_');
                        const channelUpdateExisting = await IntaoDB.getItem(store, channelUpdateKey);
                        const payload = {
                            ...(channelUpdateExisting && typeof channelUpdateExisting === 'object' ? channelUpdateExisting : {}),
                            taoh_ntw: channelUpdateKey,
                            last_update_stamp: Date.now(),
                        };
                        await IntaoDB.setItem(store, payload);

                        resolve({
                            status: 200,
                            success: true,
                            response: res
                        });
                    } catch (err) {
                        console.error("Error getting messages:", err);
                        reject(err);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching messages:", error);
                    reject({
                        status: 201,
                        success: false,
                        error: error
                    });
                }
            });
        });
    }

    async function saveChannelMessages(channel_id, render = 0, channel_type = '') {

        lastTimestamp = await getChannelTimestamp(channel_id);

        if(channel_type == '') {
            channel_type = $("#channel-chat").attr("data-channel_type");
        }

        //console.log("TEST 12345678");

        return new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'roomslug': roomslug,
                    'taoh_action': 'taoh_ntw_get_messages',
                    'channel_id': channel_id,
                    'keyword': ntw_keyword,
                    'channel_type': channel_type,
                    'last_message_id': 0,
                    'last_timestamp': lastTimestamp,
                    'key': my_pToken
                },
                dataType: 'json',
                success: async function (res) {
                    try {
                        if(Array.isArray(res) && res.length > 0) {

                            let channelKey = `channel_${channel_id}`;
                            let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
                            let messages = channelData?.values?.messages || [];

                            if (res.length == 1 && messages.length == 0 && res[0]?.ptoken != my_pToken && channel_type == taohChannelDm) {

                                if (!dmNotifyBlock) {
                                    taoh_set_success_message("You've got a new direct message!");
                                    dmNotifyBlock = true;
                                    setTimeout(() => {
                                        dmNotifyBlock = false;
                                    }, 20000);
                                }

                            }


                            await saveChannelToDB(channel_id, res, "", 1);
                            if(render == 1) {
                                await load_channel_messages(channel_id);
                            } else {
                                //### $('#channel-'+channel_id).addClass('have_updates');
                                await setHaveUpdateStatus(channel_id, 1);
                                if(channel_type == taohChannelDm) {
                                    //$('.unread-mentions').removeClass('d-none');
                                }
                            }
                        }
                        resolve({
                            status: 200,
                            success: true,
                            response: res
                        });
                    } catch (err) {
                        console.error("Error getting messages:", err);
                        reject(err);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching messages:", error);
                    reject({
                        status: 201,
                        success: false,
                        error: error
                    });
                }
            });
        });
    }

    async function loadSpeedNetworkingData(channel_id) {

        var lastTimestamp = await getChannelTimestamp(channel_id);
        var channel_type = $("#channel-chat").attr("data-channel_type");

       // console.log("getChannelTimestamp ===========", lastTimestamp);

        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'roomslug': roomslug,
                    'taoh_action': 'taoh_ntw_speed_networking_get_data',
                    'channel_id': channel_id,
                    'keyword': ntw_keyword,
                    'channel_type': taohChannelSpeedNtw,
                    'last_message_id': 0,
                    'last_timestamp': lastTimestamp,
                    'key': my_pToken
                },
                dataType: 'json',
                success: async function (res) {
                   // console.log("GET speed channel data ==", res);
                    try {
                        resolve({
                            status: 200,
                            success: true,
                            result: res
                        });
                    } catch (err) {
                        console.error("Error getting messages:", err);
                        reject(err);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching messages:", error);
                    reject({
                        status: 201,
                        success: false,
                        error: error
                    });
                }
            });
        });

        if (response.status == 200) {

            const channelKey = `channel_${channel_id}`;
            let channelData = await IntaoDB.getItem(objStores.ntw_store.name, channelKey);
            let updatedResponse = channelData?.values || {};
            let last_fetch_timestamp = channelData?.last_fetch_stamp || 0;

            await IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: channelKey,
                values: updatedResponse,
                timestamp: Date.now(),
                last_fetch_stamp: last_fetch_timestamp,
                //last_update_stamp: lastTimestamp,
                last_update_stamp: Date.now(),
            });

            var res = response.result;
            var res_timestamp = res.timestamp;
            var diffInSeconds = Math.abs(res_timestamp - Date.now()) / 1000;

            console.log("res_timestamp, lastTimestamp, diffInSeconds1 ==>>", res.all_ptoken);

            const snchannelKey = `room_${ntw_keyword}_${roomslug}_${my_pToken}_${taohChannelSpeedNtw}_channels`;
            var snchannelData = await IntaoDB.getItem(objStores.ntw_store.name, snchannelKey);
            let snupdatedResponse = snchannelData?.values || [];

            console.log("res_timestamp, lastTimestamp, diffInSeconds2 ==>>", snchannelKey);
            console.log("res_timestamp, lastTimestamp, diffInSeconds3 ==>>", snupdatedResponse);

            if (snupdatedResponse) {

                if (!Array.isArray(snupdatedResponse.channels[0].members)) {
                    snupdatedResponse[0].members = [];
                }

                snupdatedResponse.channels[0].members = Array.isArray(res.all_ptoken) ? [...res.all_ptoken] : snupdatedResponse.channels[0].members;

                // if (Array.isArray(res.all_ptoken) && res.all_ptoken.length > 0) {
                //     res.all_ptoken.forEach((ptoken) => {
                //         snupdatedResponse[0].members.push(ptoken);
                //     });
                // }
            }
            await IntaoDB.setItem(objStores.ntw_store.name, {
                taoh_ntw: snchannelKey,
                values: snupdatedResponse,
                timestamp: Date.now(),
            });

            // Comment FOR Prod Move 04122025
            // const store = objStores.ntw_store.name;
            // const [channelInfo] = await getIntaoDataById(store, snchannelKey, channel_id);
            // speedNetworkingAddUser(channelInfo.members, channelInfo.restricted_ptokens);

            if (Array.isArray(res.restricted_ptoken) && res.restricted_ptoken.length > 0) {
                res.restricted_ptoken.forEach((ptoken) => {
                    for (const restricted_ptoken of res.restricted_ptoken) {
                        //$(`.speed_networking_div #carousel_item_${restricted_ptoken}`).addClass('d-none').removeClass('active');
                    }
                    restrictedPtokenUpdate(ptoken);
                    updateSpeedNetworkingCarousel();
                });
            } else {
                $(`.speed_networking_div .carousel-item`).each(function() {
                    $(this).removeClass('d-none');
                });
            }

            if (Array.isArray(res.accept_ptoken) && res.accept_ptoken.length > 0) {

                $('#speed_networking').show();
                $('#channel-chat').hide();
                $('#users-chat').hide();
                $('#browse_channels_wrapper').hide();
                $('#participants').hide();

                $('#channel-chat').removeClass('d-block');
                $('#users-chat').removeClass('d-block');
                $('#browse_channels_wrapper').removeClass('d-block');
                $('#participants').removeClass('d-block');
                $('.chat-input-bottom').removeClass('d-flex').addClass('d-none');
                $('#chat-input-container').hide();

                for (const item of res.accept_ptoken) {

                    const [accept_ptoken, video_link] = Object.entries(item)[0];

                    if(accept_ptoken != my_pToken) {
                        var input = [ntw_room_key, my_pToken, accept_ptoken].sort().join('_');
                        var channelId = await generateSecureSlug(input, 16);
                        $(`.speed_networking_carousel #carousel_item_${accept_ptoken}`).remove();
                        clearInterval(intervalId2);
                        intervalId2 = undefined;
                        $('.countDownDiv').addClass('d-none');
                        const channelElem = $(`#channel-${channelId}`);
                        $('.speed_networking_div').addClass('d-none');
                        $('.zeroday-speed').addClass('d-none');

                        var accept_ptoken_userinfo = await getUserInfo(accept_ptoken, 'public');

                        $('#successMatchDivHeading1').removeClass('d-none');
                        $('#successMatchDivHeading2').addClass('d-none');

                        $('li').each(function() {
                            $(this).removeClass('active');
                        });
                        $('#speedChannelList li:first').addClass('active');

                        $('.successMatchDiv').removeClass('d-none');
                        $('.openchatacc_chat_now_btn').html("Join Now!");
                        $('.openchatacc_chat_now_btn').attr("video_link", video_link);
                        $('.openchatacc_chat_now_btn').attr("chat_with", accept_ptoken);
                        $('.successMatchDiv .openchatacc').attr('data-chatwith', accept_ptoken);
                        $('.successMatchDiv .user_title').text(accept_ptoken_userinfo.chat_name);
                        // if (video_link) {
                        //     window.open(video_link, '_blank');
                        // }
                    }
                }
            }

            if (Array.isArray(res.request_ptoken) && res.request_ptoken.length > 0) {
                //if(speed_networking_last_timestamp != 0) {
                    var req_ptoken = res.request_ptoken[0];

                    let pinned_by = "";
                    let avatar_image = "";
                    const userInfo = await getUserInfo(req_ptoken, 'full');
                    if (userInfo && !$('#connectModal').hasClass('show')) {
                        pinned_by = userInfo.chat_name;
                        pinned_by = pinned_by.charAt(0).toUpperCase() + pinned_by.slice(1);
                        if (userInfo.avatar_image != '' && userInfo.avatar_image != undefined) {
                            avatar_image = userInfo.avatar_image;
                        } else if (userInfo.avatar != undefined && userInfo.avatar != 'default') {
                            avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + userInfo.avatar + '.png';
                        } else {
                            avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                        }
                        var companyValue = Object.values(userInfo?.company ?? {})[0]?.value || "";
                        $('#connectModal .user_profile_img').attr('src', avatar_image);
                        $('#connectModal .user_title').text(pinned_by);
                        $('#connectModal .user_profile_link').attr("href", _taoh_site_url_root + '/profile/' + req_ptoken);
                        $('#connectModal .user_fullLocation').text(userInfo.full_location);
                        $('#connectModal .user_companyName').text(companyValue+"., ");

                        $('#connectModal .accept_btn').attr('data-chatwith', myptoken).attr('data-chatfrom', req_ptoken).attr('data-action', 1);
                        $('#connectModal .reject_btn').attr('data-chatwith', myptoken).attr('data-chatfrom', req_ptoken).attr('data-action', -1);

                        $('#connectModal').modal('show');
                        if(intervalId1) {
                            clearInterval(intervalId1);
                            intervalId1 = undefined;
                        }
                        intervalId1 = runTimer("countDownTimer");
                    }
                //}
            }

            if (Array.isArray(res.reject_ptoken) && res.reject_ptoken.length > 0) {

                $('#speed_networking').show();
                $('#channel-chat').hide();
                $('#users-chat').hide();
                $('#browse_channels_wrapper').hide();
                $('#participants').hide();

                $('#channel-chat').removeClass('d-block');
                $('#users-chat').removeClass('d-block');
                $('#browse_channels_wrapper').removeClass('d-block');
                $('#participants').removeClass('d-block');
                $('.chat-input-bottom').removeClass('d-flex').addClass('d-none');
                $('#chat-input-container').hide();

                $('.watchPartySection').hide();
                $('.watchPartySection').removeClass('watchPartyEnabled');
                $('.chat-leftsidebar').removeClass('watchPartyEnabled');

                //console.log(intervalId2);

                $('#tourbutton').removeClass('d-xl-block');

                $('.participants_refresh_div').removeClass('participants-active');

                const currentElem = $('#speedChannelList li:first');

                $('#channelList .channel-item').each(function() {
                    $(this).removeClass('active');
                });
                $('#myChannelList .channel-item').each(function() {
                    $(this).removeClass('active');
                });

                $('#browse_channels_wrapper').addClass('d-none');

                currentElem.removeClass('have_updates');

                $('.speedChannelList li').each(function() {
                    $(this).removeClass('active');
                });

                $(`.speed_networking_div .carousel-item`).each(function() {
                    $(this).removeClass('d-none');
                });

                currentElem.addClass('active');

                $('#participants-sidebar').show();

                loadchatWindow("speed_networking");


                // for (const rej_ptoken of res.reject_ptoken) {
                //     restrictedPtokenUpdate(rej_ptoken);
                //     $('#carousel_item_'+rej_ptoken).remove();
                //     updateSpeedNetworkingCarousel();
                // }
                if ($('.countDownDiv:visible').length) {
                    for (const rej_ptoken of res.reject_ptoken) {

                        if(rej_ptoken == $('.countDownDiv').data('chatwith')) {
                            $('.countDownDiv').addClass('d-none');

                            clearInterval(intervalId1);
                            clearInterval(intervalId2);
                            intervalId1 = undefined;
                            intervalId2 = undefined;

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
                            $('.zeroday-speed').addClass('d-none');

                            $('.speed_networking_carousel .carousel-item').each(function() {
                                $(this).find('.connect_btn').prop('disabled', false).text('Connect');
                                $(this).find('.not_interested_btn').prop('disabled', false).text('Not interested');
                            });

                            //updateSpeedNetworkingCarousel();
                        }
                        restrictedPtokenUpdate(rej_ptoken);
                    }
                }
                //updateSpeedNetworkingCarousel();
            }

            let ref_btn_elem = $('#speednetworking_refresh_btn');
            let ref_btn_elem_icon = ref_btn_elem.find('i');
            ref_btn_elem_icon.removeClass('fa-spinner fa-spin').addClass('fa-refresh');

        }

    }

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

    async function updateRecentActivity(channel_id) {
        var all_messages = await loadChannelFromDB(channel_id) || [];
        //var all_messages = allmessages?.messages || [];

        videos_act = [];

        // const store = objStores.ntw_store.name;
        // const ntwRoomChannels = ['room', ntw_keyword, roomslug, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
        // const [channelInfo] = await getIntaoDataById(store, ntwRoomChannels, channel_id);

        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: channel_id,
            type: channelType,
            my_pToken
        });
        const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channel_id);

        if (channelInfo && channelInfo.data && channelInfo.data.name) {
            var channelName = channelInfo.data.name;
            channelName = channelName.charAt(0).toUpperCase() + channelName.slice(1);
        } else {
            var channelName = "";
        }

        for (const msg of all_messages) {
            var msg_text = msg.text;
            let decoded = decodeURIComponent(msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));
            decoded = decoded.replace(/\+/g, '');
            const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
            const aTag = match ? match[0] : "";
            if(aTag != "") {
                let videoName = $(msg_text).find("a.join-v-link").text().trim();
                let videoLink = $(msg_text).find("a.join-v-link").attr('link').trim();

                const exists = videos_act.some(v => v.video_link === videoLink);
                if (!exists && videoName && videoLink && msg.ptoken != my_pToken) {
                    videos_act.push({
                        action: "create_video",
                        video_name: videoName,
                        video_link: videoLink,
                        channel_name: channelName,
                        ptoken: msg.ptoken
                    });
                }
                var msgHTML = "Join "+aTag+" - Video Room";
            }
        }
        videos_act = videos_act.filter(v => v.video_name && v.video_link).reverse();
        if (videos_act.length > 0) {
            $('.recent_activity').removeClass('d-none');
            renderVideoActivities(videos_act);
        } else {
            $('.recent_activity').addClass('d-none');
        }
    }

    function convertLinks(text) {
        // Step 1: Protect email addresses temporarily
        const EMAIL_TOKEN = "__EMAIL_PROTECT__";
        const emails = [];
        text = text.replace(/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/g, (email) => {
            emails.push(email);
            return EMAIL_TOKEN + (emails.length - 1) + "__";
        });

        // Step 2: Convert URLs to links
        const urlRegex = /\b((https?:\/\/[^\s]+)|(www\.[^\s]+)|([a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(\/[^\s]*)?))\b/g;
        text = text.replace(urlRegex, function(url) {
            let href = url;
            if (!url.startsWith("http://") && !url.startsWith("https://")) {
                href = "http://" + url;
            }
            return `<a href="${href}" target="_blank">${url}</a>`;
        });

        // Step 3: Restore emails
        text = text.replace(new RegExp(EMAIL_TOKEN + "(\\d+)__", "g"), (_, index) => emails[index]);

        return text;
    }

    async function render_messages_paginate(messages, metadata, append = 1, layout = "main", prepend = 0) {
        // load cached messages (unchanged)
        var allMessagesData = await loadChannelFromDB(currentChannelId);
        var allMessages = allMessagesData?.messages || [];
        $('.pin-message-v2').hide();
        var pinnedFound = false;

        for (const allmsg of allMessages) {
            if (allmsg.pinned == 1) {
                pinnedFound = true;
                continue;
            }
        }

        //console.log("renderMessages !! messages ===", allMessages);

        var loadLayout = "channel-conversation-list";
        if (layout == "reply") {
            loadLayout = "chat-reply-conversation-list";
        }

        // If append==0 you wanted to clear before rendering (keeps existing behavior)
        if (append == 0) {
            $("#" + loadLayout).html("");
        }

        // Normalize messages to array
        if (!Array.isArray(messages)) {
            messages = [messages];
        }

        // If prepending, ensure messages are in chronological order oldest->newest
        if (prepend == 1 && messages.length > 1) {
            try {
                // assume msg.timestamp is comparable; if newest-first then reverse
                const firstTs = Number(messages[0].timestamp || 0);
                const lastTs = Number(messages[messages.length - 1].timestamp || 0);
                if (firstTs > lastTs) {
                    messages = messages.slice().reverse();
                }
            } catch (e) {
                // ignore on parse issues
            }
        }

        const channelElem = $(`#channel-${currentChannelId}`);
        const channelType = channelElem.getSyncedData('channel_type') || taohChannelDefault;

        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: currentChannelId,
            type: channelType,
            my_pToken
        });
        const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === currentChannelId);

        let channelName = "";
        if (channelInfo && channelInfo.data && channelInfo.data.name) {
            channelName = channelInfo.data.name;
            channelName = channelName.charAt(0).toUpperCase() + channelName.slice(1);
        }

        // helper to build date separator HTML
        function buildDateSeparatorHTML(label) {
            return `
            <span class="chat-date-separator my-2" data-label="${label}">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1 border-top"></div>
                    <span class="mx-2 px-3 py-1 rounded bg-light text-dark">
                        ${label}
                    </span>
                    <div class="flex-grow-1 border-top"></div>
                </div>
            </span>`;
        }

        // We'll collect message HTMLs grouped by date label
        const groups = new Map(); // label => { messages: [html,...], sampleMsgTimestamp: <for ordering if needed> }
        const labelsOrder = []; // keep the chronological order encountered (oldest->newest)

        // Helper to push message HTML into groups
        function pushToGroup(label, html, ts) {
            if (!groups.has(label)) {
                groups.set(label, { messages: [], ts });
                labelsOrder.push(label);
            }
            groups.get(label).messages.push(html);
        }

        // Build HTML for each message (but DO NOT insert to DOM yet)
        for (const msg of messages) {
            // preserve existing logic for timestamp, user info, avatar, names, message parsing, pinned handling etc.
            if (!msg.timestamp) continue; // skip if no timestamp

            let date = new Date(msg.timestamp);
            let timeString = date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });

            var chatInfo = await getUserInfo(msg.ptoken, 'public');
            let avatar_image;
            if (chatInfo.avatar_image != '' && chatInfo.avatar_image != undefined) {
                avatar_image = chatInfo.avatar_image;
            } else if (chatInfo.avatar != undefined && chatInfo.avatar != 'default') {
                avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + chatInfo.avatar + '.png';
            } else {
                avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
            }

            var chat_name = (chatInfo.chat_name || "").toString();
            chat_name = chat_name.charAt(0).toUpperCase() + chat_name.slice(1);

            if (channelType == taohChannelOrganizer && msg?.ptoken === event_owner_ptoken) {
                chat_name += ' (Organizer)';
            }

            // ensure pin-message container exists
            let $parent = $('.pin-message-v2');
            if ($parent.find('.pin_message_div' + currentChannelId).length === 0 && currentChannelId) {
                $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div pin_message_div${currentChannelId}" style="gap: 12px;">
                    <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                    <div class="flex-grow-1 pin_message_msg_div"> </div>
                </div>`);
            }

            var pin_msg_count = $(`.pin_message_div${currentChannelId} .pin_message_msg_div pin_msg`).length;
            var activeClass = "";
            if (pin_msg_count > 0) {
                activeClass = "active";
            }

            var msg_text = msg.text || "";
            let decoded = decodeURIComponent(msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));
            decoded = decoded.replace(/\+/g, '');
            const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
            const aTag = match ? match[0] : "";
            let msgHTML = "";
            if (aTag != "") {
                let videoName = $(msg_text).find("a.join-v-link").text().trim();
                let videoLink = $(msg_text).find("a.join-v-link").attr('link')?.trim();

                const exists = videos_act.some(v => v.video_link === videoLink);
                if (!exists && videoName && videoLink && msg.ptoken != my_pToken) {
                    videos_act.push({
                        action: "create_video",
                        video_name: videoName,
                        video_link: videoLink,
                        channel_name: channelName,
                        ptoken: msg.ptoken
                    });
                }

                msgHTML = "Join " + aTag + " - Video Room";
            } else {
                let msgDecoded = decodeURIComponent(
                    msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' ')
                );
                if (msgDecoded.length > 100) {
                    var visibleText = msgDecoded.slice(0, 100);
                    var hiddenText = msgDecoded.slice(100);
                    msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                } else {
                    msgHTML = msgDecoded;
                }
                msg_text = convertLinks(msg_text);
            }

            // Handle pin UI (same logic as before)
            if (msg.pinned == 1) {
                pinnedFound = true;
                $('.pin-message-v2').show();
                if ($(`.pin_message_div${currentChannelId} .pin_message_msg_div [data-frm_message_id="${msg.message_id}"]`).length === 0 && currentChannelId) {
                    $(`.pin_message_div${currentChannelId} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}"></div>`);
                    $(`.pin_message_div${currentChannelId} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center" style="gap: 12px;">
                                <img style="width: 28px; height: 28px; border-radius: 100%;" src="${avatar_image}" alt="">
                                <div class="p-message">
                                    ${msgHTML}
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 dropdown mb-auto">
                            <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i>
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_id="${msg.message_id}">Go to Message</a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between btnPinMsg" data-type="channel" data-id="${msg.message_id}" data-status="1" data-frm_message_key="${msg.message_key}" >Unpin</a>
                                <a data-chatwith="${msg.ptoken}" data-profile_token="${msg.ptoken}" data-pagename="${pagename}" class="dropdown-item ${msg.pinned_by === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between openProfileModal">View Profile</a>
                            </div>
                        </div>
                    </div>`);
                    let dotActiveCount = $(`.pin_message_div${currentChannelId} .message-item-dot.active`).length;
                    let msgActiveCount = $(`.pin_message_div${currentChannelId} .pin_msg.d-flex`).length;
                    if (dotActiveCount == 0 || msgActiveCount == 0) {
                        $(`.pin_message_div${currentChannelId} .message-item-dot`).first().addClass("active");
                        $(`.pin_message_div${currentChannelId} .pin_msg`).removeClass('d-flex').addClass('d-none');
                        $(`.pin_message_div${currentChannelId} .pin_msg`).first().removeClass('d-none').addClass("d-flex");
                    }
                }
            }
            if (msg.pinned == 0) {
                $(`.message-item-dot[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
                $(`.pin_msg[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
            }

            // If message exists already, handle updates (deleted/pinned/reactions) immediately as original logic
            if ($(`.chat-list[data-frm_message_key="${msg.message_key}"]`).length > 0) {
                if (msg.deleted && $(`.chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                    $(`.chat-list[data-frm_message_key="${msg.message_key}"]`).remove();
                    updateRecentActivity(currentChannelId);
                }
                if (msg.pinned == 1 && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                    $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).html(`Unpin <i class="bx bx-pin text-muted ms-2"></i>`);
                    $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).attr("data-status", msg.pinned);
                }
                if (msg.pinned == 0 && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                    $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).html(`Pin <i class="bx bx-pin text-muted ms-2"></i>`);
                    $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).attr("data-status", msg.pinned);
                }
                if (msg.deleted && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                    $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).remove();
                }
                // update reactions if present
                if (msg.reactions) {
                    try {
                        let reactions = JSON.parse(msg.reactions);
                        if (Object.keys(reactions).length > 0) {
                            $(`#${loadLayout} .emoji_btn[data-frm_message_id="${msg.message_id}"] .emoji_placeholder`).addClass('d-none');
                            $(`#${loadLayout} .emoji_btn[data-frm_message_id="${msg.message_id}"] .message-reactions`).html(formatReactions(msg.reactions));
                        }
                    } catch (e) {
                        // ignore
                    }
                }

                // skip pushing to insert list; we handled update above
                continue;
            }

            // Build the <li> HTML (same structure as original) and push to group
            let escapedMsg = escapeHtml(msg.text);
            let replyCount = messages.filter(m => Number(m.parent_message_id) === Number(msg.message_id) && m.deleted === false).length;

            const messageHTML = `
                <li class="chat-list ${msg.ptoken === my_pToken ? 'right' : 'left'} msg_${msg.timestamp}"
                    data-frm_message_id="${msg.message_id}" data-frm_message_key="${msg.message_key}" id="msg_${msg.message_id}">
                    <div class="conversation-list">
                        <div class="chat-avatar openProfileSideBar"
                            data-chatwith="${msg.ptoken}"
                            data-profile_token="${msg.ptoken}">
                            <img src="${avatar_image}" alt="profile">
                        </div>
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
                        <div class="user-chat-content">
                            <div class="ctext-wrap">
                                <div class="ctext-wrap-content">
                                    <h6 class="mb-1 ctext-name">
                                        <span class="openProfileSideBar"
                                            data-chatwith="${msg.ptoken}"
                                            data-profile_token="${msg.ptoken}">
                                            ${chat_name}
                                        </span>
                                    </h6>
                                    <p class="mb-0 ctext-content">${msg_text}</p>
                                </div>
                                <div class="align-self-start message-box-drop d-flex">
                                    <div class="dropdown">
                                        <a class="conversation-reply channel-reply btnReply  ${layout == 'reply' ? 'd-none' : ''}"
                                        data-id="${msg.message_id}" data-msg="${escapedMsg}" data-chat_name="${chat_name}" href="#">
                                            <i class="bx bx-share mt-1 fs-20"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Reply to thread"></i>
                                        </a>
                                    </div>
                                    <div class="dropdown">
                                        <a href="#" role="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill fs-20"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="More actions"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item d-flex align-items-center copy-message" href="#">Copy
                                                <i class="bx bx-copy text-muted ms-2"></i>
                                            </a>
                                            <a class="dropdown-item d-flex align-items-center btnPinMsg ${layout == 'reply' ? 'd-none' : ''}"
                                            data-id="${msg.message_id}" data-status="${msg.pinned ?? 0}" href="#">
                                            ${msg.pinned ? "Unpin" : "Pin"} <i class="bx bx-pin text-muted ms-2"></i>
                                            </a>
                                            <a class="dropdown-item ${msg.ptoken === my_pToken ? 'd-flex' : 'd-none'} align-items-center frm-delete-item btnDeleteMsg"
                                            data-id="${msg.message_id}" href="#">
                                            Delete <i class="bx bx-trash text-muted ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="conversation-name">
                                <small class="text-muted time">${timeString}</small>

                                <span class="emoji_btn ${layout == 'reply' ? 'd-none' : ''}" data-frm_message_id="${msg.message_id}" data-frm_message_key="${msg.message_key}">
                                    <i class="far fa-smile text-muted emoji_placeholder ${(msg.reactions === undefined) ? '' : 'd-none'}"></i>
                                    <div class="message-reactions">${formatReactions(msg.reactions)}</div>
                                </span>

                                <small class="conversation-reply-count view-replies text-primary ${replyCount > 0 ? "" : "d-none"}"
                                    data-msg="${escapedMsg}" data-chat_name="${chat_name}"
                                    data-id="${msg.message_id}"
                                    data-count="${replyCount}">
                                    ${replyCount} ${replyCount > 1 ? "replies 22" : "reply 22"}
                                </small>
                            </div>
                        </div>
                    </div>
                </li>
            `;

            // compute date label
            let currentLabel = getDateLabel(msg.timestamp);

            // push into grouped map
            pushToGroup(currentLabel, messageHTML, msg.timestamp);
        } // end for messages

        // Now we have groups (labelsOrder in the order we encountered them from messages array, which should be oldest->newest if prepend==1)
        // Insert groups into DOM carefully depending on prepend/append.

        const $container = $("#" + loadLayout);

        // For scroll preservation we need the real scroll element (SimpleBar)
        // Try to get wrapper (simplebar content wrapper)
        let $wrapper = $container.closest('.simplebar-content-wrapper');
        if ($wrapper.length === 0) {
            // fallback: the container itself might be the scroll element
            $wrapper = $container;
        }

        if (prepend == 1) {
            // Prepend case (older messages)
            const labels = labelsOrder.slice(); // oldest -> newest
            if (labels.length === 0) {
                // nothing to insert
            } else {
                const firstExistingSep = $container.find('.chat-date-separator').first();
                const existingTopLabel = firstExistingSep.length ? firstExistingSep.attr('data-label') : null;

                // Save scroll state
                const oldScrollHeight = $wrapper[0].scrollHeight;
                const oldScrollTop = $wrapper.scrollTop();

                if (existingTopLabel && labels.includes(existingTopLabel)) {
                    // We will replace the existing separator for that label and insert all groups before the first existing message
                    // find the anchor (first existing message after the separator)
                    const $anchorMsg = firstExistingSep.nextAll('.chat-list').first();

                    // Remove the old separator (we'll re-create a single correct one)
                    firstExistingSep.remove();

                    // find index k
                    const k = labels.indexOf(existingTopLabel);

                    // insert groups from newest (k) down to 0 before the anchor
                    for (let i = k; i >= 0; i--) {
                        const label = labels[i];
                        const grp = groups.get(label);
                        if (!grp) continue;

                        const separatorHtml = buildDateSeparatorHTML(label);
                        const groupHtml = separatorHtml + grp.messages.join('');

                        // Insert before anchor
                        if ($anchorMsg.length) {
                            $anchorMsg.before(groupHtml);
                        } else {
                            // no anchor (unexpected), just prepend the entire group
                            $container.prepend(groupHtml);
                        }
                    }

                    // Any remaining groups after k (shouldn't normally happen since existingTopLabel is newest of batch),
                    // but if there are, prepend them in reverse order to place oldest at top:
                    for (let j = labels.length - 1; j > k; j--) {
                        const label = labels[j];
                        const grp = groups.get(label);
                        if (!grp) continue;
                        const separatorHtml = buildDateSeparatorHTML(label);
                        $container.prepend(separatorHtml + grp.messages.join(''));
                    }
                } else {
                    // No existing top label present in DOM: insert all groups at top.
                    // Insert from newest -> oldest using prepend so final top->down is oldest->newest
                    for (let i = labels.length - 1; i >= 0; i--) {
                        const label = labels[i];
                        const grp = groups.get(label);
                        if (!grp) continue;
                        const separatorHtml = buildDateSeparatorHTML(label);
                        $container.prepend(separatorHtml + grp.messages.join(''));
                    }
                }

                // Restore scroll position so visual view doesn't jump
                const newScrollHeight = $wrapper[0].scrollHeight;
                $wrapper.scrollTop(oldScrollTop + (newScrollHeight - oldScrollHeight));
            }
        } else {
            // Append case (new messages at bottom)
            // Determine existing bottom label to avoid duplicate separators
            let lastDateLabel = null;
            const $lastSep = $container.find('.chat-date-separator').last();
            if ($lastSep.length) {
                lastDateLabel = $lastSep.attr('data-label');
            } else {
                // try deduce from last message element
                const $lastMsg = $container.find('.chat-list').last();
                if ($lastMsg.length) {
                    const cls = $lastMsg.attr('class') || '';
                    const match = cls.match(/msg_(\d+)/);
                    if (match) {
                        lastDateLabel = getDateLabel(Number(match[1]));
                    }
                }
            }

            // Append groups in chronological order (oldest -> newest)
            for (const label of labelsOrder) {
                const grp = groups.get(label);
                if (!grp) continue;

                if (label !== lastDateLabel) {
                    // only add separator if not exists for this label
                    if ($container.find(`.chat-date-separator[data-label="${label}"]`).length === 0) {
                        $container.append(buildDateSeparatorHTML(label));
                    }
                    lastDateLabel = label;
                }
                $container.append(grp.messages.join(''));
            }
        }

       // console.log("pinnedFound==pinnedFound", pinnedFound);

        if (pinnedFound) {
            $('.pin-message-v2').show();
        } else {
            $('.pin-message-v2').hide();
        }
       // console.log("videos_act videos_act", videos_act);
        videos_act = videos_act.filter(v => v.video_name && v.video_link).reverse();
        if (videos_act.length > 0) {
            $('.recent_activity').removeClass('d-none');
            renderVideoActivities(videos_act);
        } else {
            $('.recent_activity').addClass('d-none');
        }

        if(prepend == 0) {
            if (layout == "reply") {
                simpleBarScrollToBottom('#chat-reply-conversation');
            } else {
                simpleBarScrollToBottom('#chat-conversation-channel');
            }

            if (loadLayout == "chat-reply-conversation-list") {
                simpleBarScrollToBottom('#chat-reply-conversation');
            }
        }

    }

    async function render_messages(messages, metadata, append = 1, layout = "main") {

        var allMessages = await loadChannelFromDB(currentChannelId) || [];
        let lastDateLabel = null;
        $('.pin-message-v2').hide();
        VIDEO_POSTED_RECENTLY = 0;

        var pinnedFound = false;

        console.log("allMessages allMessages", allMessages);

        for (const allmsg of allMessages) {
            if(allmsg.pinned == 1) {
                pinnedFound = true;
                continue;
            }
        }

        //console.log("renderMessages !! messages ===", allMessages);

        var loadLayout = "channel-conversation-list";
        if(layout == "reply") {
            loadLayout = "chat-reply-conversation-list";
        }

        if(append == 0) {
            $("#"+loadLayout).html("");
        }
        if (!Array.isArray(messages)) {
            messages = [messages];
        }

        const channelElem = $(`#channel-${currentChannelId}`);
        const channelType = channelElem.getSyncedData('channel_type') || taohChannelDefault;

        const store = objStores.ntw_store.name;
        const ntwRoomChannels = ['room', ntw_keyword, roomslug, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
        const [channelInfo] = await getIntaoDataById(store, ntwRoomChannels, currentChannelId);

        if (channelInfo && channelInfo.data && channelInfo.data.name) {
            var channelName = channelInfo.data.name;
            channelName = channelName.charAt(0).toUpperCase() + channelName.slice(1);
        } else {
            var channelName = "";
        }

        var membersData = {};
        if(channelInfo?.members_data) {
            membersData = channelInfo.members_data;
        }

        const uniquePtokens = [...new Set(messages.map(m => m.ptoken).filter(Boolean))];
        const userInfoCache = new Map();
        await Promise.all(
            uniquePtokens.map(async (ptoken) => {
                try {

                    if(ptoken == "Organizer" && channelType == taohChannelOrganizer) {
                        ptoken = event_owner_ptoken;
                    }

                    let memberJson = membersData[ptoken];
                    var info = null;
                    if(memberJson) {
                        let parsedMember = JSON.parse(memberJson.trim());
                        info = parsedMember?.output?.user;
                    }
                    if(!info || info == null) {
                        info = await getUserInfo(ptoken, 'public', false, true);
                    }
                    userInfoCache.set(ptoken, info);
                } catch (e) {
                    console.error(`Failed to fetch user ${ptoken}:`, e);
                    userInfoCache.set(ptoken, {
                        chat_name: ptoken,
                        avatar_image: '',
                        avatar: 'default'
                    });
                }
            })
        );

        for (const msg of messages) {

            if(msg.parent_message_id) {
                loadLayout = "chat-reply-conversation-list";
            } else {
                loadLayout = "channel-conversation-list";
            }

            let timestamp = msg?.timestamp;
            if (timestamp) {

                let date = new Date(msg.timestamp);
                let timeString = date.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                if(msg.ptoken == "Organizer" && channelType == taohChannelOrganizer) {
                    msg.ptoken = event_owner_ptoken;
                }

                console.log("msg ptoken ----", msg.ptoken);

                var chatInfo = userInfoCache.get(msg.ptoken);
                if (chatInfo.avatar_image != '' && chatInfo.avatar_image != undefined) {
                    var avatar_image = chatInfo.avatar_image;
                } else if (chatInfo.avatar != undefined && chatInfo.avatar != 'default') {
                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + chatInfo.avatar + '.png';
                } else {
                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                }

                var chat_name = chatInfo.chat_name;
                if(!chat_name || chat_name == undefined) {
                    chat_name = msg.ptoken;
                }
                chat_name = chat_name.charAt(0).toUpperCase() + chat_name.slice(1);

                if(channelType == taohChannelOrganizer && msg?.ptoken === event_owner_ptoken){
                    chat_name += ' (Organizer)';
                }

                //let replyCount = messages.filter(m => m.parent_message_id == msg.message_id).length;
                //let replyCount = 0;
                //replyCount = messages.filter(m => m.parent_message_id == msg.message_id).length;

                let $parent = $('.pin-message-v2');
                if ($parent.find('.pin_message_div'+currentChannelId).length === 0 && currentChannelId) {
                    $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div pin_message_div${currentChannelId}" style="gap: 12px;">
                        <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                        <div class="flex-grow-1 pin_message_msg_div"> </div>
                    </div>`);
                }

                var pin_msg_count = $(`.pin_message_div${currentChannelId} .pin_message_msg_div pin_msg`).length;
                var activeClass = "";
                if(pin_msg_count > 0) {
                    activeClass = "active";
                }
                var msg_text = msg.text;
                let decoded = decodeURIComponent(msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));
                decoded = decoded.replace(/\+/g, '');
                const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
                const aTag = match ? match[0] : "";
                if(aTag != "") {
                    let videoName = $(msg_text).find("a.join-v-link").text().trim();
                    let videoLink = $(msg_text).find("a.join-v-link").attr('link').trim();

                    const exists = videos_act.some(v => v.video_link === videoLink);
                    if (!exists && videoName && videoLink && msg.ptoken != my_pToken) {
                        videos_act.push({
                            action: "create_video",
                            video_name: videoName,
                            video_link: videoLink,
                            channel_name: channelName,
                            ptoken: msg.ptoken
                        });
                    }

                    let currentTime = Date.now();
                    let diffMs = currentTime - msg.timestamp;
                    let diffMinutes = diffMs / (1000 * 60);
                    //alert(diffMinutes)
                    if (diffMinutes < 10 && msg.ptoken != my_pToken) {
                        VIDEO_POSTED_RECENTLY = 1;
                         setTimeout(() => {
                            restartDojoMessages();
                        }, 1000);
                    }
                    var msgHTML = "Join "+aTag+" - Video Room";
                } else {
                    let msg = decodeURIComponent(
                        msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' ')
                    );
                    if (msg.length > 100) {
                        var visibleText = msg.slice(0, 100);
                        var hiddenText = msg.slice(100);
                        var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                    } else {
                        var msgHTML = msg;
                    }
                    msg_text = convertLinks(msg_text);
                }

                if(msg.pinned == 1) {
                    //console.log("=== msg msg <<< ===>>", msg.pinned);
                    pinnedFound = true;
                    $('.pin-message-v2').show();
                    if ($(`.pin_message_div${currentChannelId} .pin_message_msg_div [data-frm_message_id="${msg.message_id}"]`).length === 0 && currentChannelId) {
                        $(`.pin_message_div${currentChannelId} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}"></div>`);
                        $(`.pin_message_div${currentChannelId} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <img style="width: 28px; height: 28px; border-radius: 100%;" src="${avatar_image}" alt="">
                                    <div class="p-message">
                                        ${msgHTML}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 dropdown mb-auto">
                                <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_id="${msg.message_id}">Go to Message</a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between btnPinMsg" data-type="channel" data-id="${msg.message_id}" data-status="1" data-frm_message_key="${msg.message_key}" >Unpin</a>
                                    <a data-chatwith="${msg.ptoken}" data-profile_token="${msg.ptoken}" data-pagename="${pagename}" class="dropdown-item ${msg.pinned_by === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between openProfileModal">View Profile</a>
                                </div>
                            </div>
                        </div>`);
                        let dotActiveCount = $(`.pin_message_div${currentChannelId} .message-item-dot.active`).length;
                        let msgActiveCount = $(`.pin_message_div${currentChannelId} .pin_msg.d-flex`).length;
                        if (dotActiveCount == 0 || msgActiveCount == 0) {
                            $(`.pin_message_div${currentChannelId} .message-item-dot`).first().addClass("active");
                            $(`.pin_message_div${currentChannelId} .pin_msg`).removeClass('d-flex').addClass('d-none');
                            $(`.pin_message_div${currentChannelId} .pin_msg`).first().removeClass('d-none').addClass("d-flex");
                        }
                    }
                }
                if(msg.pinned == 0) {
                    $(`.message-item-dot[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
                    $(`.pin_msg[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
                }


                if ($(`.chat-list[data-frm_message_key="${msg.message_key}"]`).length === 0) {

                    if (msg.deleted) {

                        var reply_Count = allMessages.filter(m =>
                            Number(m.parent_message_id) === Number(msg.parent_message_id) && m.deleted === false
                        ).length;
                        console.log("reply_Count", reply_Count);
                        console.log("reply_Count", allMessages);
                        if(reply_Count) {
                            if ($(`#channel-conversation-list .chat-list[data-frm_message_id="${msg.parent_message_id}"]`).length) {
                                //console.log("reply count found");
                                var countText = "";
                                if(reply_Count > 1) {
                                    countText = reply_Count+" replies";
                                } else {
                                    countText = reply_Count+" reply";
                                }
                                $(`#channel-conversation-list .conversation-reply-count[data-id="${msg.parent_message_id}"]`).removeClass('d-none').attr('data-count', reply_Count).text(countText);
                            }
                        }
                        if (msg.deleted && $(`.chat-list[data-frm_message_id="${msg.message_id}"]`).length) {
                            $(`.chat-list[data-frm_message_id="${msg.message_id}"]`).remove();
                        }
                        updateRecentActivity(currentChannelId);

                    } else {

                        if(msg.parent_message_id && msg.reply_count && layout == "main") {
                            if ($(`#channel-conversation-list .chat-list[data-frm_message_id="${msg.parent_message_id}"]`).length) {
                                var countText = "";
                                var replyCount = msg.reply_count;
                                if(replyCount > 1) {
                                    countText = replyCount+" replies";
                                } else {
                                    countText = replyCount+" reply";
                                }
                                $(`#channel-conversation-list .conversation-reply-count[data-id="${msg.parent_message_id}"]`).removeClass('d-none').attr('data-count', replyCount).text(countText);
                            }
                        }

                        let currentLabel = getDateLabel(msg.timestamp);
                        if (currentLabel !== lastDateLabel) {
                            if ($("#"+loadLayout+" .chat-date-separator[data-label='"+currentLabel+"']").length === 0) {
                                $("#"+loadLayout).append(`
                                    <span class="chat-date-separator my-2" data-label="${currentLabel}">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-grow-1 border-top"></div>
                                            <span class="mx-2 px-3 py-1 rounded bg-light text-dark">
                                                ${currentLabel}
                                            </span>
                                            <div class="flex-grow-1 border-top"></div>
                                        </div>
                                    </span>
                                `);
                            }
                            lastDateLabel = currentLabel;
                        }

                        if(msg.parent_message_id && layout == "main") {
                            if(messages.length == 1) {
                                var reply_Count = allMessages.filter(m =>
                                    Number(m.parent_message_id) === Number(msg.parent_message_id) && m.deleted === false
                                ).length;
                                if(reply_Count) {
                                    if ($(`#${loadLayout} .chat-list[data-frm_message_id="${msg.parent_message_id}"]`).length) {
                                        //console.log("reply count found");
                                        var countText = "";
                                        if(reply_Count > 1) {
                                            countText = reply_Count+" replies";
                                        } else {
                                            countText = reply_Count+" reply";
                                        }
                                        $(`#${loadLayout} .conversation-reply-count[data-id="${msg.parent_message_id}"]`).removeClass('d-none').attr('data-count', reply_Count).text(countText);
                                    }
                                }
                                loadLayout = "chat-reply-conversation-list";

                            }  else {
                                //continue;
                            }
                        }

                        var replyCount = messages.filter(m =>
                            Number(m.parent_message_id) === Number(msg.message_id) && m.deleted === false
                        ).length;

                        let escapedMsg = escapeHtml(msg.text);

                        let parent_message_id = $('#channel-reply-message-block').attr('data-parent-message-id');
                        if(parent_message_id && msg.parent_message_id) {
                            if(parent_message_id != msg.parent_message_id) {
                                continue;
                            }
                        }

                        console.log("layout ===>>>", layout);

                        if(msg.parent_message_id) {
                            layout = "reply";
                        } else {
                            layout = "main";
                        }


                        //if(!msg.parent_message_id || layout == "reply") {
                            $("#"+loadLayout).append(`
                                <li class="chat-list ${msg.ptoken === my_pToken ? 'right' : 'left'} msg_${msg.timestamp}" data-frm_message_id="${msg.message_id}" data-frm_message_key="${msg.message_key}" id="msg_${msg.message_id}">
                                    <div class="conversation-list">
                                        <div class="chat-avatar openProfileSideBar"
                                            data-chatwith="${msg.ptoken}"
                                            data-profile_token="${msg.ptoken}">
                                            <img src="${avatar_image}" alt="profile">
                                        </div>

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

                                        <div class="user-chat-content">
                                            <div class="ctext-wrap">
                                                <div class="ctext-wrap-content">
                                                    <h6 class="mb-1 ctext-name">
                                                        <span class="openProfileSideBar"
                                                            data-chatwith="${msg.ptoken}"
                                                            data-profile_token="${msg.ptoken}">
                                                            ${chat_name}
                                                        </span>
                                                    </h6>
                                                    <p class="mb-0 ctext-content" id="msg_text${msg.message_id}">${msg_text}</p>
                                                </div>
                                                <div class="align-self-start message-box-drop d-flex">
                                                    <div class="dropdown">
                                                        <a class="conversation-reply channel-reply btnReply  ${layout == 'reply' ? 'd-none' : ''}"
                                                        data-id="${msg.message_id}" data-msg="${escapedMsg}" data-chat_name="${chat_name}" href="#">
                                                            <i class="bx bx-share mt-1 fs-20"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Reply to thread"></i>
                                                        </a>
                                                    </div>
                                                    <div class="dropdown">
                                                        <a href="#" role="button" data-bs-toggle="dropdown">
                                                            <i class="ri-more-2-fill fs-20"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="More actions"></i>
                                                        </a>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item d-flex align-items-center copy-message" href="#">Copy
                                                                <i class="bx bx-copy text-muted ms-2"></i>
                                                            </a>
                                                            <a class="dropdown-item d-flex align-items-center btnPinMsg ${layout == 'reply' ? 'd-none' : ''}"
                                                            data-id="${msg.message_id}" data-status="${msg.pinned ?? 0}" href="#">
                                                            ${msg.pinned ? "Unpin" : "Pin"} <i class="bx bx-pin text-muted ms-2"></i>
                                                            </a>
                                                            <a class="dropdown-item ${msg.ptoken === my_pToken ? 'd-flex' : 'd-none'} align-items-center frm-delete-item btnDeleteMsg"
                                                            data-id="${msg.message_id}" href="#">
                                                            Delete <i class="bx bx-trash text-muted ms-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="conversation-name">
                                                <small class="text-muted time">${timeString}</small>

                                                <span class="emoji_btn ${layout == 'reply' ? 'd-none' : ''}" data-frm_message_id="${msg.message_id}" data-frm_message_key="${msg.message_key}">
                                                    <i class="far fa-smile text-muted emoji_placeholder ${(msg.reactions === undefined) ? '' : 'd-none'}"></i>
                                                    <div class="message-reactions">${formatReactions(msg.reactions)}</div>
                                                </span>

                                                <small class="conversation-reply-count view-replies text-primary ${replyCount > 0 ? "" : "d-none"}"
                                                    data-msg="${escapedMsg}" data-chat_name="${chat_name}"
                                                    data-id="${msg.message_id}"
                                                    data-count="${replyCount}">
                                                    ${replyCount} ${replyCount > 1 ? "replies" : "reply"}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            `);
                        //}
                    }
                } else {

                    //console.log("EXISTING", msg.message_id);

                    if (msg.deleted && $(`.chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                        $(`.chat-list[data-frm_message_key="${msg.message_key}"]`).remove();
                        updateRecentActivity(currentChannelId);
                    }
                    if (msg.pinned == 1 && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                        $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).html(`Unpin <i class="bx bx-pin text-muted ms-2"></i>`);
                        $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).attr("data-status", msg.pinned);
                    }
                    if (msg.pinned == 0 && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                        $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).html(`Pin <i class="bx bx-pin text-muted ms-2"></i>`);
                        $(`#${loadLayout} .btnPinMsg[data-id="${msg.message_id}"]`).attr("data-status", msg.pinned);
                    }
                    if (msg.deleted && $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).length) {
                        $(`#${loadLayout} .chat-list[data-frm_message_key="${msg.message_key}"]`).remove();
                    }
                    //console.log("reply count found before 1234", msg);
                    if(msg.parent_message_id && msg.reply_count) {
                        if ($(`#${loadLayout} .chat-list[data-frm_message_id="${msg.parent_message_id}"]`).length) {
                            //console.log("reply count found");
                            var countText = "";
                            var replyCount = msg.reply_count;
                            //console.log("reply count found before 1234", replyCount);
                            if(replyCount > 1) {
                                countText = replyCount+" replies";
                            } else {
                                countText = replyCount+" reply";
                            }
                            $(`#${loadLayout} .conversation-reply-count[data-id="${msg.parent_message_id}"]`).removeClass('d-none').attr('data-count', replyCount).text(countText);
                        }
                    }
                    if (msg.reactions) {
                        try {
                            let reactions = JSON.parse(msg.reactions);
                            if (Object.keys(reactions).length > 0) {
                                $(`#${loadLayout} .emoji_btn[data-frm_message_id="${msg.message_id}"] .emoji_placeholder`).addClass('d-none');
                                $(`#${loadLayout} .emoji_btn[data-frm_message_id="${msg.message_id}"] .message-reactions`).html(formatReactions(msg.reactions));
                            }
                        } catch (e) {
                            //console.log("Invalid reactions JSON");
                        }
                    }
                }
            }
        }

        console.log("pinnedFound==pinnedFound", pinnedFound);

        if(pinnedFound) {
            $('.pin-message-v2').show();
        } else {
            $('.pin-message-v2').hide();
        }
        console.log("videos_act videos_act", videos_act);
        videos_act = videos_act.filter(v => v.video_name && v.video_link).reverse();
        if (videos_act.length > 0) {
            $('.recent_activity').removeClass('d-none');
            renderVideoActivities(videos_act);
        } else {
            $('.recent_activity').addClass('d-none');
        }

        //console.log("videos_act videos_act videos_act", videos_act);

        if(layout == "reply") {
            simpleBarScrollToBottom('#chat-reply-conversation');
        } else {
            simpleBarScrollToBottom('#chat-conversation-channel');
        }

        if(loadLayout == "chat-reply-conversation-list") {
            simpleBarScrollToBottom('#chat-reply-conversation');
        }

        // $(".channelList li").removeClass("disabled"); // optional: style disabled
        // $(".myChannelList li").removeClass("disabled");
        // $(".dmChannelList li").removeClass("disabled");
        // $(".organizerChannelList li").removeClass("disabled");
        // $(".exhibitorChannelList li").removeClass("disabled");
        // $(".sessionChannelList li").removeClass("disabled");

    }


    function getDateLabel(ts) {
        const msgDate = new Date(ts);
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        const msgDay = msgDate.toDateString();
        if (msgDay === today.toDateString()) {
            return "Today";
        } else if (msgDay === yesterday.toDateString()) {
            return "Yesterday";
        } else {
            return msgDate.toLocaleDateString('en-US', {
                weekday: 'long', month: 'short', day: 'numeric', year: 'numeric'
            });
        }
    }

    async function renderVideoActivities(videoactivities) {
        var activities_video_html = '';

        activities_video_html += `
            <div>
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div class="count-container">
                        <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="27" cy="27" r="27" fill="black"/>
                            <path d="M14.8 13C12.6489 13 10.9 14.7297 10.9 16.8571V32.2857H14.8V16.8571H38.2V32.2857H42.1V16.8571C42.1 14.7297 40.3511 13 38.2 13H14.8ZM8.17 34.2143C7.52406 34.2143 7 34.7326 7 35.3714C7 37.9268 9.09625 40 11.68 40H41.32C43.9038 40 46 37.9268 46 35.3714C46 34.7326 45.4759 34.2143 44.83 34.2143H8.17Z" fill="white"/>
                            <rect x="15" y="17" width="23" height="15" fill="white"/>
                            <path d="M23.15 20C23.6141 20 24.0592 20.1975 24.3874 20.5492C24.7156 20.9008 24.9 21.3777 24.9 21.875C24.9 22.3723 24.7156 22.8492 24.3874 23.2008C24.0592 23.5525 23.6141 23.75 23.15 23.75C22.6859 23.75 22.2408 23.5525 21.9126 23.2008C21.5844 22.8492 21.4 22.3723 21.4 21.875C21.4 21.3777 21.5844 20.9008 21.9126 20.5492C22.2408 20.1975 22.6859 20 23.15 20ZM31.2 20C31.6641 20 32.1092 20.1975 32.4374 20.5492C32.7656 20.9008 32.95 21.3777 32.95 21.875C32.95 22.3723 32.7656 22.8492 32.4374 23.2008C32.1092 23.5525 31.6641 23.75 31.2 23.75C30.7359 23.75 30.2908 23.5525 29.9626 23.2008C29.6344 22.8492 29.45 22.3723 29.45 21.875C29.45 21.3777 29.6344 20.9008 29.9626 20.5492C30.2908 20.1975 30.7359 20 31.2 20ZM20 27.0008C20 25.6203 21.0456 24.5 22.3341 24.5H23.2681C23.6159 24.5 23.9462 24.582 24.2438 24.7273C24.2153 24.8961 24.2022 25.0719 24.2022 25.25C24.2022 26.1453 24.5697 26.9492 25.1494 27.5C25.145 27.5 25.1406 27.5 25.1341 27.5H20.4659C20.21 27.5 20 27.275 20 27.0008ZM28.8659 27.5C28.8616 27.5 28.8572 27.5 28.8506 27.5C29.4325 26.9492 29.7978 26.1453 29.7978 25.25C29.7978 25.0719 29.7825 24.8984 29.7562 24.7273C30.0538 24.5797 30.3841 24.5 30.7319 24.5H31.6659C32.9544 24.5 34 25.6203 34 27.0008C34 27.2773 33.79 27.5 33.5341 27.5H28.8659ZM24.9 25.25C24.9 24.6533 25.1212 24.081 25.5151 23.659C25.9089 23.2371 26.443 23 27 23C27.557 23 28.0911 23.2371 28.4849 23.659C28.8788 24.081 29.1 24.6533 29.1 25.25C29.1 25.8467 28.8788 26.419 28.4849 26.841C28.0911 27.2629 27.557 27.5 27 27.5C26.443 27.5 25.9089 27.2629 25.5151 26.841C25.1212 26.419 24.9 25.8467 24.9 25.25ZM22.8 31.3742C22.8 29.6492 24.1059 28.25 25.7159 28.25H28.2841C29.8941 28.25 31.2 29.6492 31.2 31.3742C31.2 31.7188 30.9397 32 30.6159 32H23.3841C23.0625 32 22.8 31.7211 22.8 31.3742Z" fill="black"/>
                        </svg>
                        <div class="vdo-act-count"></div>
                    </div>`;
        activities_video_html += `
            <div class="top_portion"></div>
            </div>

            <ul class="mt-2 vdo-room-lists collapsible" id="">`;
        var usernameArray = [];
        if (videoactivities.length > 0) {
            var video_count = 0;
            for (let activity of videoactivities) {
                const userInfo = await getUserInfo(activity.ptoken, 'public');
                const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
                const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
                const userChatName = userInfo.chat_name;

                if (activity.action == 'create_video') {
                    video_count++;
                    if (video_count < 3) {
                        usernameArray.push(userChatName);
                    }
                    activities_video_html += `
                        <li class="d-flex align-items-center" style="gap: 6px;">
                        <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                        <div>
                            <span class="mr-1">
                            <span class="fw-500 text-capitalize">${userChatName} </span>
                            created a video room  <b>${activity.video_name}</b> on ${activity.channel_name}
                        </span>
                            <a href="${activity.video_link}" target="_blank" class="text-underline">Check Video Room</a>
                            </div>
                        </li>
                    `;
                }

            }
            activities_video_html += `</ul></div>`;

            var top_data = '';

            top_data += `
                <p class="fw-500 text-capitalize text-black lh-16 mb-2 sentence" >
                </p>
                <button
                type="button"
                class="toggle-btn btn bor-btn toggle-vdo-lists">
                    <span class="toggleText">More Details</span>
                    <svg class="drp-dwn-svg" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0ZM2.63672 4.70703C2.45312 4.52344 2.45312 4.22656 2.63672 4.04492C2.82031 3.86328 3.11719 3.86133 3.29883 4.04492L4.99805 5.74414L6.69727 4.04492C6.88086 3.86133 7.17773 3.86133 7.35938 4.04492C7.54102 4.22852 7.54297 4.52539 7.35938 4.70703L5.33203 6.73828C5.14844 6.92188 4.85156 6.92188 4.66992 6.73828L2.63672 4.70703Z" fill="black"/>
                    </svg>
                </button>
            `;


            $('.video-room-activity').html(activities_video_html);
            $('.video-room-activity').removeClass('d-none');
            $('.top_portion').html(top_data);
            $('.vdo-act-count').html(video_count);
            const uniqueMemmbers = [...new Set(usernameArray)];
            const usernameData = uniqueMemmbers.join(", ");
            const totalMembers = uniqueMemmbers.length;
            console.log(usernameData)

            var sent = `${usernameData} `;
            if (totalMembers > 2) {
                var remaining = totalMembers - 2;
                sent += `and ${remaining} Others `;

            }
            sent += `Created Video Room(s) !`;

            $('.sentence').html(sent);
        } else {
            //$('.video-room-activity').hide();
            $('.video-room-activity').addClass('d-none');
        }
    }
    function simpleBarScrollToBottom(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        let simplebarInstance = SimpleBar.instances.get(el);
        if (!simplebarInstance) {
            simplebarInstance = new SimpleBar(el);
        }

        console.log("simpleBarScrollToBottom simpleBarScrollToBottom");


        simplebarInstance.recalculate();

        const scrollContent = simplebarInstance.getScrollElement();
        let ignoreScroll = true;

        // Scroll to bottom
        scrollContent.scrollTo({
            top: scrollContent.scrollHeight
        });

        // Stop ignoring after short delay
        setTimeout(() => ignoreScroll = false, 400);

        // Attach scroll listener
        // $(scrollContent).off('scroll.simplebar').on('scroll.simplebar', function () {
        //     if (ignoreScroll) return;

        //     const top = $(this).scrollTop();
        //     if (top <= 5) {
        //         console.log("Reached top");
        //         loadPreviousMessages();
        //     }
        // });
    }

    async function loadPreviousMessages(){
        let message_id = $('#channel-conversation-list li:first').attr('data-frm_message_id');
        const cachedMessages = await loadChannelFromDB($('#channel-chat').attr('data-channel_id'), message_id);
        if(cachedMessages.length) {
            render_messages(cachedMessages, "", append = 1, layout = "main", prepend = 1);
        }
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function unescapeHtml(text) {
        return text
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&quot;/g, "\"")
            .replace(/&#039;/g, "'")
            .replace(/&amp;/g, "&");
    }

    $('#createVideoForm').validate({
        rules: {
            video_name: {
                required: true,
            },
            /*video_desc: {
                    required: true,
                    maxlength: 350
                },*/
            ext_link : {
                required: function () {
                    return $("input[name=room-choice]:checked").val() == "1" ? true : false;
                }
            }
        },
        messages: {
            video_name: {
                required: "Video name is required"
            },
            /* video_desc: {
                required: "Video description is required"
            },*/
            ext_link : {
                required: "External room is required"
            }
        },
        submitHandler: async function (form, event) {

            event.preventDefault();

            var room_choice = $("input[name=room-choice]:checked").val();
            var video_desc = $("#video_desc").val();
            var video_name = $("input[name=video_name]").val();
            var channel_of_type = $("input[name=channel_of_type]").val();
            let createVideoForm = $('#createVideoForm');

            let formData = new FormData(form);
            formData.append('taoh_action', 'taoh_create_video');
            formData.append('key', my_pToken);
            formData.append('ptoken', my_pToken);
            formData.append('room_id', ntw_room_key);

            let submit_btn = createVideoForm.find('button[type="submit"]');
            submit_btn.prop('disabled', true);

            let submit_btn_icon = submit_btn.find('i');
            submit_btn_icon.removeClass('fa-play-circle-o').addClass('fa-spinner fa-spin');
            // alert(room_choice)

            if(channel_of_type == 'channel'){
                let channel_id_a = $('#channel-chat').attr('data-channel_id');
                let channel_name = $('.cw_channel_title').text() || 'Channel';

                var track_data = {
                    'action': 'create_video',
                    'channel_id': channel_id_a,
                    'channel_name': channel_name,
                    'video_name': video_name,
                    'video_link': '',
                    'ptoken': my_pToken
                };
            } else {
                var track_data = {
                    'action': 'create_video',
                    'video_name': video_name,
                    'video_link': '',
                    'ptoken': my_pToken
                };
            }

            console.log("track_data", track_data);

            if(room_choice == 1) {
                var ext_link_org =  $("input[name=ext_link]").val();

                var ext_link = normalizeUrl(ext_link_org);

                track_data.video_link = ext_link;
                var videoChatLinkData = `<div class="ctext-wrap mb-0">
                        <div class="">
                            <p class="mb-0 ctext-content fs-12 fw-400">
                                Join
                                <a href="${ext_link}" video_name="${video_name}" link="${ext_link}" channel_of_type="${channel_of_type}"
                                target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                ${video_name}
                                </a>
                                - Video Room
                            </p>
                            <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>
                        </div>
                    </div>`;
                $('#chat_input').val(videoChatLinkData);
                $('#chat-send-btn').trigger('click');
                //$('#createVideoForm').reset();
                taoh_track_activities(track_data);

                document.getElementById("createVideoForm").reset();
                submit_btn.prop('disabled', false);
                submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                $('#v-channel-room').modal('hide');
                window.open(ext_link);
            }
            else{

                var gmeet_data = await createGoogleMeet(video_name);
                var link = gmeet_data?.data?.meet_link || "";
                console.log("gmeet_data gmeet_data", link);

                 if(!link) {
                    // Fallback to AJAX only if no GMeet link
                    try {
                        const response = await $.ajax({
                            url: createVideoForm.attr('action'),
                            type: 'post',
                            data: formData,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            cache: false,
                        });
                        if(response.my_link) {
                            link = response.my_link;
                            console.log("jitsi link:", link);
                        }
                    } catch (err) {
                        console.error("Error creating Jitsi link:", err);
                    }
                }

                if(link) {
                    track_data.video_link = link;
                    taoh_track_activities(track_data);
                    var videoChatLinkData = `<div class="ctext-wrap mb-0">
                            <div class="">
                                <p class="mb-0 ctext-content fs-12 fw-400">
                                    Join
                                    <a href="${link}" video_name="${video_name}" link="${link}" channel_of_type="${channel_of_type}"
                                    target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;">
                                    ${video_name}
                                    </a>
                                    - Video Room
                                </p>
                                <p class="mb-0 ctext-content fs-12 fw-400 text-black">${video_desc}</p>
                            </div>
                        </div>`;
                    $('#chat_input').val(videoChatLinkData);
                    $('#chat-send-btn').trigger('click');
                    document.getElementById("createVideoForm").reset();
                    submit_btn.prop('disabled', false);
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    $('#v-channel-room').modal('hide');
                    window.open(link);
                } else {
                    alert("Error creating video link. Please try again.");
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-play-circle-o');
                    submit_btn.prop('disabled', false);
                }
            }
        }
    });

    function sendToActivityChannel(videoChatLinkData) {
        var data = {
            'taoh_action': 'taoh_ntw_add_activity_channel',
            'keyslug': roomslug,
            'keyword': ntw_keyword,
            'video_link_data': videoChatLinkData,
            'key': my_pToken
        };

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (response, textStatus, jqXHR) {
                console.log("sendToActivityChannel", response);
            }
        });
    }

    function normalizeUrl(url) {
        // Remove leading/trailing spaces
        url = $.trim(url);

        // Check if it already has http or https
        if (!/^https?:\/\//i.test(url)) {
            // If it starts with "www.", prepend "http://"
            if (/^www\./i.test(url)) {
                url = "http://" + url;
            } else {
                // Otherwise, add "http://www." by default
                url = "http://www." + url;
            }
        }

        return url;
    }

    function taoh_track_activities(track_data) {
        var data = {
            taoh_action: 'taoh_track_activities',
            room_id: ntw_room_key,
            ptoken: my_pToken,
            track_data: track_data
        };

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'POST',
            data: data,
            dataType: 'json',
            complete: function () {
                loader(false, loaderArea);
                taoh_get_activities();
            }
        });
    }

    function taoh_get_activities() {
        let data = {
            ops: 'activity',
            action: 'getActivity',
            code: _taoh_ops_code,
            key: my_pToken,
            room_id: ntw_room_key,
            cfcc60: 1
        };
        $.ajax({
            url: _taoh_cache_chat_url,
            type: 'GET',
            dataType: 'json',
            //headers: {'If-None-Match': ntwChannelListETag},
            data: data,
            success: function (response, textStatus, jqXHR) {
                loader(false, loaderArea);
                if (jqXHR.status === 304) return;
                if (response.success) {
                    renderActivities(response);
                    renderVideoActivitiesMain(response.videoactivities);
                } else {

                }
            },
            error: function (xhr, status, err) {
                console.error('Error Fetching activity list : ' + err);
            }
        });
    }

    async function renderActivities(response) {
        var activities_html = '';
        var activities = response.activities;
        var speed_networking_user_count = response.speed_networking_count;

        if (activities.length > 0) {
            for (let activity of activities) {
                const userInfo = await getUserInfo(activity.ptoken, 'public');
                const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
                const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
                const userChatName = userInfo.chat_name;

                if (activity.action == 'click_1_1') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                                <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                <div>
                                                    <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                    <span class="fw-500 text-capitalize">
                                                    ${userChatName}
                                                    </span> Started a One on One Chat !</p>
                                                    <!--<div style="line-height: 1;">
                                                        <a href="javascript:void(0);" class="participants_refresh a-link">Check Participants !</a>
                                                    </div>-->
                                                </div>
                                            </li>`;
                } else if (activity.action == 'speed_networking_connect') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                                <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                <div>
                                                    <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                    <span class="fw-500 text-capitalize">
                                                    ${userChatName}
                                                    </span> Connected through Speed networking</p>
                                                    <!--<div style="line-height: 1;">
                                                        <a href="javascript:void(0);" class="participants_refresh a-link">Check Participants !</a>
                                                    </div>-->
                                                </div>
                                            </li>`;
                } else if (activity.action == 'joined_video') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                                <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                <div>
                                                    <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                    <span class="fw-500 text-capitalize">
                                                    ${userChatName}</span> Joined <span class="fw-500 text-underline">${activity.video_name}</span></p>
                                                    <div style="line-height: 1;">
                                                        <a target="_blank" href="${activity.video_link}"
                                                        class="load_video a-link">Check Video Room</a>
                                                    </div>
                                                </div>
                                            </li>`;
                } else if (activity.action == 'joined_from') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                                                <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                <div>
                                                    <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                                                    <span class="fw-500 text-capitalize">${userChatName}</span>
                                                    Joined from <span class="fw-500">${activity.location}</span></p>
                                                    <div class="d-flex align-items-center" style="gap: 6px;">
                                                        <span data-profile_token="${activity.ptoken}" data-pagename="${pagename}" class="a-link openProfileModal">View Profile</span>
                                                        <a href="#" class="openchatacc a-link  capitalize-first"
                                                        data-chatwith="${activity.ptoken}"
                                                        data-chatname="${userChatName}"
                                                        >Chat</a>

                                                    </div>
                                                </div>
                                            </li>`;
                } else if (activity.action == 'click_channel') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                        <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                        <div>
                            <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;"> <span class="fw-500 text-capitalize">${userChatName}</span> joined <span class="fw-500 text-underline"> ${activity.channel_name} Channel !</span></p>
                            <div style="line-height: 1;">
                                <a href="javascript:void(0);"
                                click_channel_id="${activity.channel_id}"
                                class="click_channel a-link">Check What's Happening !</a>

                            </div>
                        </div>
                    </li>`;
                } else if (activity.action == 'mention') {
                    activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                        <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                        <div>
                            <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                            <span class="fw-500 text-capitalize">${userChatName}</span> mention
                            <span class="fw-500 text-underline">
                                @${activity.mention_name}</a>
                            </span></p>
                            <div class="d-flex align-items-center" style="gap: 6px;">
                                <span data-profile_token="${activity.ptoken}" data-pagename="${pagename}" class="a-link openProfileModal">View Profile</span>
                                <a href="#" class="openchatacc a-link  capitalize-first"
                                data-chatwith="${activity.ptoken}"
                                data-chatname="${userChatName}">Chat</a>
                            </div>

                        </div>
                    </li>`;
                }
            }
        }
        if (speed_networking_user_count > 0) {
            const userText = speed_networking_user_count > 1 ? "Users" : "User";
            activities_html += `<li class="d-flex align-items-center activity-list" style="gap: 12px;">
                <div>
                    <p class="fs-12 fw-400 mb-0" style="line-height: 1.149;">
                        <span class="fw-500 text-capitalize">
                            ${speed_networking_user_count}
                        </span> ${userText} entered into the Speed networking Channel
                    </p>
                </div>
            </li>`;
        }

        if(activities_html != '') {
            $('#activities_block1-main').removeClass('d-none');
            $('#activities-list').html(activities_html);
            $('#activities-list1').html(activities_html);
            $('#activities-list1').closest('.card').removeClass('d-none');
        } else {
            $('#activities_block1-main').addClass('d-none');
            activities_html = '';
            $("#activities_block").hide();
            $("#activities_block1").hide();
        }

    }

    async function renderVideoActivitiesMain(videoactivities) {
        var activities_video_html = '';

        activities_video_html += `<div>
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <div class="count-container">
                                            <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="27" cy="27" r="27" fill="black"/>
                                                <path d="M14.8 13C12.6489 13 10.9 14.7297 10.9 16.8571V32.2857H14.8V16.8571H38.2V32.2857H42.1V16.8571C42.1 14.7297 40.3511 13 38.2 13H14.8ZM8.17 34.2143C7.52406 34.2143 7 34.7326 7 35.3714C7 37.9268 9.09625 40 11.68 40H41.32C43.9038 40 46 37.9268 46 35.3714C46 34.7326 45.4759 34.2143 44.83 34.2143H8.17Z" fill="white"/>
                                                <rect x="15" y="17" width="23" height="15" fill="white"/>
                                                <path d="M23.15 20C23.6141 20 24.0592 20.1975 24.3874 20.5492C24.7156 20.9008 24.9 21.3777 24.9 21.875C24.9 22.3723 24.7156 22.8492 24.3874 23.2008C24.0592 23.5525 23.6141 23.75 23.15 23.75C22.6859 23.75 22.2408 23.5525 21.9126 23.2008C21.5844 22.8492 21.4 22.3723 21.4 21.875C21.4 21.3777 21.5844 20.9008 21.9126 20.5492C22.2408 20.1975 22.6859 20 23.15 20ZM31.2 20C31.6641 20 32.1092 20.1975 32.4374 20.5492C32.7656 20.9008 32.95 21.3777 32.95 21.875C32.95 22.3723 32.7656 22.8492 32.4374 23.2008C32.1092 23.5525 31.6641 23.75 31.2 23.75C30.7359 23.75 30.2908 23.5525 29.9626 23.2008C29.6344 22.8492 29.45 22.3723 29.45 21.875C29.45 21.3777 29.6344 20.9008 29.9626 20.5492C30.2908 20.1975 30.7359 20 31.2 20ZM20 27.0008C20 25.6203 21.0456 24.5 22.3341 24.5H23.2681C23.6159 24.5 23.9462 24.582 24.2438 24.7273C24.2153 24.8961 24.2022 25.0719 24.2022 25.25C24.2022 26.1453 24.5697 26.9492 25.1494 27.5C25.145 27.5 25.1406 27.5 25.1341 27.5H20.4659C20.21 27.5 20 27.275 20 27.0008ZM28.8659 27.5C28.8616 27.5 28.8572 27.5 28.8506 27.5C29.4325 26.9492 29.7978 26.1453 29.7978 25.25C29.7978 25.0719 29.7825 24.8984 29.7562 24.7273C30.0538 24.5797 30.3841 24.5 30.7319 24.5H31.6659C32.9544 24.5 34 25.6203 34 27.0008C34 27.2773 33.79 27.5 33.5341 27.5H28.8659ZM24.9 25.25C24.9 24.6533 25.1212 24.081 25.5151 23.659C25.9089 23.2371 26.443 23 27 23C27.557 23 28.0911 23.2371 28.4849 23.659C28.8788 24.081 29.1 24.6533 29.1 25.25C29.1 25.8467 28.8788 26.419 28.4849 26.841C28.0911 27.2629 27.557 27.5 27 27.5C26.443 27.5 25.9089 27.2629 25.5151 26.841C25.1212 26.419 24.9 25.8467 24.9 25.25ZM22.8 31.3742C22.8 29.6492 24.1059 28.25 25.7159 28.25H28.2841C29.8941 28.25 31.2 29.6492 31.2 31.3742C31.2 31.7188 30.9397 32 30.6159 32H23.3841C23.0625 32 22.8 31.7211 22.8 31.3742Z" fill="black"/>
                                            </svg>

                                            <div class="vdo-act-count"></div>
                                        </div>`;
        activities_video_html += `

                                                <div class="top_portion"></div>
                                                </div>

                                                <ul class="mt-2 vdo-room-lists collapsible" id="">`;
        var usernameArray = [];
        if (videoactivities.length > 0) {
            var video_count = 0;
            for (let activity of videoactivities) {
                const userInfo = await getUserInfo(activity.ptoken, 'public');
                const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;
                const userAvatarSrc = await buildAvatarImage(userInfo.avatar_image, fallbackSrc);
                const userChatName = userInfo.chat_name;

                if (activity.action == 'create_video') {
                    video_count++;
                    if (video_count < 3) {
                        usernameArray.push(userChatName);
                    }
                    activities_video_html += `


                                                                    <li class="d-flex align-items-center" style="gap: 6px;">
                                                                    <img class="round-profile-24" src="${userAvatarSrc}" alt="">
                                                                    <div>
                                                                        <span class="mr-1">
                                                                        <span class="fw-500 text-capitalize">${userChatName} </span>
                                                                        created a video room  <b>${activity.video_name}</b> on
                                                                        <a href="javascript:void(0);"
                                                                        click_channel_id="${activity.channel_id}"
                                                                        class="click_channel a-link">
                                                                        ${activity.channel_name}</a>
                                                                    </span>
                                                                        <a href="${activity.video_link}" target="_blank" class="text-underline">Check Video Room</a>
                                                                        </div>
                                                                    </li>

                                                                `;
                }

            }
            activities_video_html += `</ul>

                                                </div>`;


            var top_data = '';

            top_data += `
                                                        <p class="fw-500 text-capitalize text-black lh-16 mb-2 sentence" >
                                                        </p>
                                                        <button
                                                        type="button"
                                                        class="toggle-btn btn bor-btn toggle-vdo-lists">
                                                            <span class="toggleText">More Details</span>
                                                            <svg class="drp-dwn-svg" width="14" height="14" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 0C3.67392 0 2.40215 0.526784 1.46447 1.46447C0.526784 2.40215 0 3.67392 0 5C0 6.32608 0.526784 7.59785 1.46447 8.53553C2.40215 9.47322 3.67392 10 5 10C6.32608 10 7.59785 9.47322 8.53553 8.53553C9.47322 7.59785 10 6.32608 10 5C10 3.67392 9.47322 2.40215 8.53553 1.46447C7.59785 0.526784 6.32608 0 5 0ZM2.63672 4.70703C2.45312 4.52344 2.45312 4.22656 2.63672 4.04492C2.82031 3.86328 3.11719 3.86133 3.29883 4.04492L4.99805 5.74414L6.69727 4.04492C6.88086 3.86133 7.17773 3.86133 7.35938 4.04492C7.54102 4.22852 7.54297 4.52539 7.35938 4.70703L5.33203 6.73828C5.14844 6.92188 4.85156 6.92188 4.66992 6.73828L2.63672 4.70703Z" fill="black"/>
                                                            </svg>
                                                        </button>
                                                    `;

            if(activities_video_html != '') {
                $('#activities_block1-main').removeClass('d-none');
                if($('#activities-list1 li').length > 0) {
                    $('#activities-list1').closest('.card').removeClass('d-none');
                } else {
                    $('#activities-list1').closest('.card').addClass('d-none');
                }
            }

            $('.video-room-activity-main').html(activities_video_html);
            $('.video-room-activity-main').removeClass('d-none');
            $('.top_portion').html(top_data);
            $('.vdo-act-count').html(video_count);
            const uniqueMemmbers = [...new Set(usernameArray)];
            const usernameData = uniqueMemmbers.join(", ");
            const totalMembers = uniqueMemmbers.length;
            console.log(usernameData)

            var sent = `${usernameData} `;
            if (totalMembers > 2) {
                var remaining = totalMembers - 2;
                sent += `and ${remaining} Others `;

            }
            sent += `Created Video Room(s) !`;

            $('.sentence').html(sent);
        } else {
            //$('.video-room-activity-main').hide();
            $('.video-room-activity-main').addClass('d-none');
        }
    }

    function formatReactions(reactions) {

        if (!reactions || reactions === undefined || reactions === null) {
            return "";
        }

        if (typeof reactions === 'string') {
            try {
                reactions = JSON.parse(reactions);
            } catch (e) {
                reactions = {};
            }
        }
        var reactionHtml = "";
        for (const [ptoken, emoji] of Object.entries(reactions)) {
            reactionHtml +=  `<span class="reaction ${ptoken}">${emoji}</span>`;
        }

        return reactionHtml;

    }

    // =======================================
    // Channel Join/Leave/Pin/Unpin
    // =======================================

    function leave_channel(channel) {
        fetch('leave_channel.php', { method: 'POST', body: new URLSearchParams({ channel }) });
    }
    function pin_channel(channel, pin) {
        fetch('pin_channel.php', { method: 'POST', body: new URLSearchParams({ channel, pin }) });
    }

    // =======================================
    // UI + Events
    // =======================================
    let currentChannelId = null;

    // Cancel reply
    $(document).on("click", "#cancelReply", function () {
        window.replyToMessageId = null;
        $("#replyingTo").remove();
    });

    $(document).on('click', '.quick-reaction', function(e) {
        e.stopPropagation();

        _emojiElem = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn');

        const emoji = $(this).attr('data-emoji');
        messageId = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn').attr('data-frm_message_id');
        messageKey = $(this).closest('.reaction-popup').siblings('.user-chat-content').find('.emoji_btn').attr('data-frm_message_key');

        let chatfrom = $(this).closest('.reaction-popup').attr('data-chatfrom');
        let chatwith = $(this).closest('.reaction-popup').attr('data-chatwith');
        let data_type = $(this).closest('.reaction-popup').attr('data-type');

        let ch_channelId = $('#channel-chat').attr('data-channel_id');
        let user_ChannelId = $('#users-chat').attr('data-channel_id');
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

    function addReactionToMessage(data_type, chatfrom, chatwith, elem, messageId, messageKey, channelId, emoji, postToRedis = 1, emojiCount = "") {
        const $message = $(`[data-frm_message_id="${messageId}"]`);
        let $reactionsContainer = $message.find('.message-reactions');

        $message.find('.emoji_placeholder').hide();

        if (!$reactionsContainer.length) {
            $reactionsContainer = $('<div>').addClass('message-reactions');
            $message.append($reactionsContainer);
        }

        // ✅ Check if span with this ptoken exists
        const $existingTokenSpan = $reactionsContainer.find(`.${my_pToken}`);

        if ($existingTokenSpan.length) {
            // ✅ Replace old span with new one
            const $newReaction = $('<span>')
                .addClass('reaction')
                .addClass(my_pToken)
                .text(emojiCount ? `${emoji} ${emojiCount}` : `${emoji}`);
            $existingTokenSpan.replaceWith($newReaction);
        } else {
            // ✅ If no old span for this ptoken, append as usual
            const $reaction = $('<span>')
                .addClass('reaction')
                .addClass(my_pToken)
                .text(emojiCount ? `${emoji} ${emojiCount}` : `${emoji}`);
            $reactionsContainer.append($reaction);
        }

        if (postToRedis == 1) {
            emojiPost(data_type, chatfrom, chatwith, elem, channelId, messageId, messageKey, emoji);
        }

        console.log(`Added reaction ${emoji} for ptoken ${my_pToken} in message ${messageId}`);
    }

    async function emojiPost(data_type, chatfrom, chatwith, elem, channel_id, frmMessageId, frmMessageKey, emoji) {

        let sent_time = new Date().getTime();

        let data = {
            'taoh_action': 'taoh_ntw_react_message',
            'message_id': frmMessageId,
            'message_key': frmMessageKey,
            'ptoken': my_pToken,
            'other_ptoken': '',
            'channel_id': channel_id,
            'room_slug': ntw_room_key,
            'sent_time': sent_time,
            'emoji': emoji,
            'emoji_from': data_type,
            'keyword': ntw_keyword,
        };

        let loaderElem = elem.find('.conversation-loader-icon');
        loaderElem.show();

        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (res) {
                    console.log("taoh_ntw_react_message REACT RESPONSE", res);
                    if (res.success) {

                        var chat_messages_key = `channel_${channel_id}`;
                        IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                            if (!intao_data?.values?.messages) {
                                console.log("No messages found");
                                return;
                            }
                            let messages = intao_data.values.messages;
                            // 🔍 Find the message by message_id
                            let msgIndex = messages.findIndex(msg => msg.message_id == frmMessageId);

                            console.log("REACT RESPONSE ##==##", frmMessageId, msgIndex, messages);

                            let newReactions = {};
                            newReactions[my_pToken] = emoji;

                            if (msgIndex !== -1) {
                                // ✅ Update reactions here
                                messages[msgIndex].reactions = JSON.stringify(newReactions);
                                // Save back to DB in same structure
                                IntaoDB.setItem(objStores.ntw_store.name, {
                                    taoh_ntw: chat_messages_key,
                                    values: {
                                        ...intao_data.values,
                                        messages: messages
                                    },
                                    timestamp: Date.now()
                                });
                                console.log("reactions updated", frmMessageId);
                                resolve({
                                    status: 200,
                                    success: true
                                });

                            } else {
                                console.log("message_id not found:", frmMessageId);
                            }
                        });

                    } else {
                        elem.attr('disabled', false);
                        loaderElem.hide();
                        console.log('Error :', res);
                    }
                },
                error: function (xhr, status, error) {
                    elem.attr('disabled', false);
                    loaderElem.hide();
                    console.log('Error :', xhr.status);
                }
            });
        });
    }

    $(document).on("click", ".add_channel_member", function(e) {
        e.stopPropagation();
        const currentElem = $(this);
        const channelId = currentElem.attr('data-channel_id');
        const channelType = currentElem.attr('data-channel_type');

        showChannelInfoModal(channelId, channelType, 'members');
    });

    $(document).on("click", ".btnPinMsg", function(e) {
        e.preventDefault();
        let msgId = $(this).attr("data-id");
        let pinStatus = $(this).attr("data-status");
        let newPinStatus = 1;
        if(pinStatus == 1) {
            newPinStatus = 0;
        }

        $.ajax({
            url: _taoh_site_ajax_url,
            type: "post",
            data: {
                taoh_action: "taoh_pin_message",
                keyword: ntw_keyword,
                roomslug: roomslug,
                channel_id: currentChannelId,
                message_id: msgId,
                pin_status: newPinStatus,
                key: my_pToken
            },
            dataType: "json",
            success: async function(res) {
                if (res.success) {
                    let msgEl = $(`.btnPinMsg[data-id="${msgId}"]`);
                    if (res.message.pinned) {
                        msgEl.attr("data-status", 1).html(`Unpin <i class="bx bx-pin text-muted ms-2"></i>`);
                    } else {
                        msgEl.attr("data-status", 0).html(`Pin <i class="bx bx-pin text-muted ms-2"></i>`);
                    }
                    saveChannelToDB(currentChannelId, [res.message], {}, 1);
                    var msg = res.message;
                    var chatInfo = await getUserInfo(msg.ptoken, 'public');
                    if (chatInfo.avatar_image != '' && chatInfo.avatar_image != undefined) {
                        var avatar_image = chatInfo.avatar_image;
                    } else if (chatInfo.avatar != undefined && chatInfo.avatar != 'default') {
                        var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + chatInfo.avatar + '.png';
                    } else {
                        var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                    }

                    var chat_name = chatInfo.chat_name;
                    chat_name = chat_name.charAt(0).toUpperCase() + chat_name.slice(1);

                    let $parent = $('.pin-message-v2');
                    if ($parent.find('.pin_message_div'+currentChannelId).length === 0 && currentChannelId) {
                        $parent.append(`<div class="pb-2 d-flex align-items-center comm_pin_message_div pin_message_div${currentChannelId}" style="gap: 12px;">
                        <div class="nav-vertical-dots flex-shrink-0 pin_message_dot_div"> </div>
                        <div class="flex-grow-1 pin_message_msg_div"> </div>
                        </div>`);
                    }

                    var pin_msg_count = $(`.pin_message_div${currentChannelId} .pin_message_msg_div pin_msg`).length;
                    var activeClass = "";
                    if(pin_msg_count > 0) {
                        activeClass = "active";
                    }
                    var msg_text = msg.text;
                    let decoded = decodeURIComponent(msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));
                    decoded = decoded.replace(/\+/g, '');
                    const match = decoded.match(/<a [^>]*>.*?<\/a>/i);
                    const aTag = match ? match[0] : "";
                    if(aTag != "") {
                        var msgHTML = "Join "+aTag+" - Video Room";
                    } else {
                    let msg = decodeURIComponent(msg_text.replace(/%(?![0-9A-Fa-f]{2})/g, '%25').replace(/\+/g, ' '));
                        if (msg.length > 100) {
                            var visibleText = msg.slice(0, 100);
                            var hiddenText = msg.slice(100);
                            var msgHTML = `${visibleText}<span class="d-none">${hiddenText}</span> <button type="button" class="btn btn-link p-0 shadow-none show_more_btn">Show More</button>`;
                        } else {
                            var msgHTML = msg;
                        }
                    }

                    if(msg.pinned == 1) {
                        pinnedFound = true;
                        if ($(`.pin_message_div${currentChannelId} .pin_message_msg_div [data-frm_message_id="${msg.message_id}"]`).length === 0 && currentChannelId) {
                            $(`.pin_message_div${currentChannelId} .pin_message_dot_div`).append(`<div class="message-item-dot ${activeClass}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}"></div>`);
                            $(`.pin_message_div${currentChannelId} .pin_message_msg_div`).append(`<div class="pin_msg flex-grow-1 ${(activeClass == "active") ? 'd-flex' : 'd-none'}" data-channel_id="${currentChannelId}" data-frm_message_id="${msg.message_id}">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <img style="width: 28px; height: 28px; border-radius: 100%;" src="${avatar_image}" alt="">
                                    <div class="p-message">
                                        ${msgHTML}
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 dropdown mb-auto">
                                <a class="" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="ri-more-2-fill fs-20" data-bs-toggle="tooltip" data-bs-placement="top" title="More actions"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item d-flex align-items-center justify-content-between goto-message" data-frm_message_id="${msg.message_id}">Go to Message</a>
                                    <a class="dropdown-item d-flex align-items-center justify-content-between btnPinMsg" data-type="channel" data-id="${msg.message_id}" data-status="1" data-frm_message_key="${msg.message_key}" >Unpin</a>
                                    <a data-chatwith="${msg.ptoken}" data-profile_token="${msg.ptoken}" data-pagename="${pagename}" class="dropdown-item ${msg.pinned_by === my_pToken ? 'd-none' : 'd-flex'} align-items-center justify-content-between openProfileModal">View Profile</a>
                                </div>
                            </div>
                            </div>`);
                            let dotActiveCount = $(`.pin_message_div${currentChannelId} .message-item-dot.active`).length;
                            let msgActiveCount = $(`.pin_message_div${currentChannelId} .pin_msg.d-flex`).length;
                            if (dotActiveCount == 0 || msgActiveCount == 0) {
                                $(`.pin_message_div${currentChannelId} .message-item-dot`).first().addClass("active");
                                $(`.pin_message_div${currentChannelId} .pin_msg`).removeClass('d-flex').addClass('d-none');
                                $(`.pin_message_div${currentChannelId} .pin_msg`).first().removeClass('d-none').addClass("d-flex");
                            }
                        }
                    }
                    if(msg.pinned == 0) {
                        $(`.message-item-dot[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
                        $(`.pin_msg[data-channel_id="${currentChannelId}"][data-frm_message_id="${msg.message_id}"]`).remove();
                    }


                    var $dots = $(`.pin_message_div${currentChannelId} .pin_message_dot_div .message-item-dot`);
                    console.log("dots test", $dots.length);
                    if($dots.length > 0) {
                        $('.pin-message-v2').show();
                    } else {
                        $('.pin-message-v2').hide();
                    }
                    if ($dots.length && !$dots.hasClass('active')) {
                        $('[class*="pin_message_div"]').removeClass('d-flex').addClass('d-none');
                        $(`.pin_message_div${currentChannelId}`).removeClass('d-none').addClass('d-flex');

                        $(`.pin_message_div${currentChannelId} .pin_message_msg_div .pin_msg`).removeClass('d-flex').addClass('d-none');
                        $(`.pin_message_div${currentChannelId} .pin_message_msg_div .pin_msg`).first().removeClass('d-none').addClass('d-flex');

                        $(`.pin_message_div${currentChannelId} .pin_message_dot_div .message-item-dot`).removeClass('active');
                        $(`.pin_message_div${currentChannelId} .pin_message_dot_div .message-item-dot`).first().addClass('active');
                    }
                }
            }
        });
    });

    $(document).on('click', '.speed_networking_update_btn, .browse_participants_btn', function () {
        clearInterval(intervalId1);
        clearInterval(intervalId2);
        intervalId1 = undefined;
        intervalId2 = undefined;
        $("#speedChannelList li").first().click();
    });

    $("#chat_input").keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $("#chat-send-btn").click();
        }
    });

    $("#chat_reply_input").keypress(function (e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $("#chat-reply-send-btn").click();
        }
    });

    $(document).on('click', '.connect_btn', async function () {

        const $btn = $(this);
        $btn.prop('disabled', true).text('Loading...');

        var chatwith = $(this).data('chatwith');

        let data = {
            'taoh_action': 'taoh_ntw_speed_networking_connect_user',
            'key': my_pToken,
            'ptoken': my_pToken,
            'chatwith': chatwith,
            'keyword': ntw_keyword,
            'channel_type': taohChannelSpeedNtw,
            'keyslug': ntw_room_key,
            'channel_id': currentChannelId
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

        if (response.status === 200) {

            const chatWith_userInfo = await getUserInfo(chatwith, 'public');
            var chatwith_user = chatWith_userInfo.chat_name;
            var avatar_image;
            chatwith_user = chatwith_user.charAt(0).toUpperCase() + chatwith_user.slice(1);
            if (chatWith_userInfo.avatar_image != '' && chatWith_userInfo.avatar_image != undefined) {
                avatar_image = chatWith_userInfo.avatar_image;
            } else if (chatWith_userInfo.avatar != undefined && chatWith_userInfo.avatar != 'default') {
                avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + chatWith_userInfo.avatar + '.png';
            } else {
                avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
            }
            $('.countDownDiv .user_profile_img').attr('src', avatar_image);
            $('.countDownDiv .user_title').text(chatwith_user);
            $('.notAvailableDiv .user_title').text(chatwith_user);

            $('.countDownDiv .countDownTimer1').text('60');
            $('.countDownDiv').removeClass('d-none');
            $('.countDownDiv').attr('data-chatwith', chatwith);

            //$('body').append('<div id="clickBlocker" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:transparent;"></div>');

            $('.speed_networking_div').addClass('d-none');
            if(intervalId2) {
                clearInterval(intervalId2);
                intervalId2 = undefined;
            }
            intervalId2 = runTimer("countDownTimer1");

        } else {
            console.warn("Connection request failed or no user available");
        }

    });

    $(document).on('click', '.accept_btn, .reject_btn', async function () {

        $('#channel-chat').removeClass('d-block');
        $('#chat-input-container').hide();
        $('#speed_networking').addClass('d-block');

        var channelId = $('#speedChannelList li:first').attr('data-channel_id');
        var chatWith = $(this).attr('data-chatwith');
        var chatFrom = $(this).attr('data-chatfrom');
        var action = $(this).attr('data-action');

        $('#carousel_item_'+chatFrom).remove();
        updateSpeedNetworkingCarousel();

        var videolink  = "";
        var video_name  = "";
        if(action == 1) {
            let chatwithname = $('#connectModal .user_title').text();
            var sortedNames = [userInfoChatName, chatwithname].sort();
            video_name = sortedNames.join(" ");
            let gmeet_data = await createGoogleMeet(video_name);
            videolink = gmeet_data?.data?.meet_link || "";
            console.log("gmeet_data gmeet_data", videolink);
            let createVideoForm = $('#createVideoForm');

            if(!videolink) {
                const formData = {
                    taoh_action: 'taoh_create_video',
                    key: my_pToken,
                    ptoken: my_pToken,
                    room_id: ntw_room_key,
                };
                try {
                    const response = await $.ajax({
                        url: createVideoForm.attr('action'),
                        type: 'post',
                        data: formData,
                        dataType: 'json',
                        cache: false,
                    });
                    if(response.my_link) {
                        videolink = response.my_link;
                        console.log("jitsi link:", videolink);
                    }
                } catch (err) {
                    console.error("Error creating Jitsi link:", err);
                }
            }

        }

        if(videolink == "" && action == 1) {
            alert("Unable to create video room. Please try again.");
            return;
        }

        updateConnectionRequestStatus(chatWith, chatFrom, action, channelId, videolink, video_name);

    });

    $(document).on('click', '.not_interested_btn', async function () {

        const $btn = $(this);
        const $current = $(this).closest('.carousel-item');

        $btn.prop('disabled', true).text('Loading...');

        var chatwith = $(this).data('chatwith');

        let data = {
            'taoh_action': 'taoh_ntw_speed_networking_block_user',
            'key': my_pToken,
            'ptoken': my_pToken,
            'chatwith': chatwith,
            'keyslug': ntw_room_key,
            'keyword': ntw_keyword
        };
        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: async function (res) {
                    $btn.closest('.carousel-item').remove();
                    restrictedPtokenUpdate(chatwith);
                    updateSpeedNetworkingCarousel();
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status);
                }
            });
        });
    });

    async function restrictedPtokenUpdate(chatwith) {
        let store = objStores.ntw_store.name;

        var channelId = $('#speedChannelList li:first').attr('data-channel_id');

        let chkey = "channel_"+channelId;

      // console.log("restrictedPtokenUpdate restrictedPtokenUpdate", chkey);

        let existing = await IntaoDB.getItem(store, chkey);
        let last_fetch_timestamp = existing?.last_fetch_stamp || 0;
        let last_update_timestamp = existing?.last_update_stamp || 0;

       // console.log("restrictedPtokenUpdate restrictedPtokenUpdate", existing.values);

        if (existing && existing.values) {
            let record = existing.values;
            if (!record.restricted_ptokens) {
                record.restricted_ptokens = [];
            }
            if (!record.restricted_ptokens.includes(chatwith)) {
                record.restricted_ptokens.push(chatwith);
            }
            record.restricted_ptokens_count = record.restricted_ptokens.length;
        }

       // console.log("restrictedPtokenUpdate restrictedPtokenUpdate", existing.values);

        await IntaoDB.setItem(store, {
            taoh_ntw: chkey,
            values: existing.values,
            timestamp: Date.now(),
            last_fetch_stamp: last_fetch_timestamp,
            last_update_stamp: last_update_timestamp,
        });
    }

    async function loadConversation(currentElem){

        $('#chat-send-btn i').removeClass('bx-spin').removeClass('bx-loader-alt').addClass('bxs-send');
        $("#chat-send-btn").prop("disabled", false);

        $('.chat-input-bottom').addClass('d-block');

        currentChannelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        // $(".channelList li").addClass("disabled");
        // $(".myChannelList li").addClass("disabled");
        // $(".dmChannelList li").addClass("disabled");
        // $(".organizerChannelList li").addClass("disabled");
        // $(".exhibitorChannelList li").addClass("disabled");
        // $(".sessionChannelList li").addClass("disabled");

        videos_act = [];

        $('#channel_of_type').val('dm');

        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: currentChannelId,
            type: channelType,
            my_pToken
        });
        const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === currentChannelId);

        $("#channel-conversation-list").html("");
        loadchatWindow("channel-chat");

        $('.pin-message-v2').hide();
        $('.comm_pin_message_div').remove();

        addRemoveActive(currentElem);

        var channel_name = currentElem.attr('data-channel_name');
        $("#channel-chat").attr("data-channel_id", currentChannelId);
        $("#channel-chat").attr("data-channel_type", channelType);

        const sendTranscriptBtn = $('.send_transcript_btn');
        if(sendTranscriptBtn.length) {
            sendTranscriptBtn.setSyncedData('channelid', currentChannelId);
            sendTranscriptBtn.setSyncedData('channeltype', channelType);
            sendTranscriptBtn.setSyncedData('channelname', String(channel_name ?? ''));
        }

        currentElem.removeClass('have_updates');
        // :rk here update last view timestamp in intao db

        $('.comm_pin_message_div').removeClass('d-flex').addClass('d-none');
        $('.pin_message_div'+currentChannelId).removeClass('d-none').addClass('d-flex');

        $('#participants').removeClass('d-block');
        $('#channel-chat').addClass('d-block');
        $('#chat-input-container').show();
        $('.chat-input-bottom').removeClass('d-none');

        if(channelType == taohChannelDm) {
            $('.cw_channel_profile_img').show();
            var avatar_src = currentElem.attr('data-avatar_src');
            $('.cw_channel_profile_img').attr('src', avatar_src);
        } else {
            $('.cw_channel_profile_img').hide();
        }

        if(channelType == taohChannelDefault){
            $('.cw_channel_title').html(`<i class="la ${channelInfo.visibility === 'private' ? 'la-lock' : 'la-hashtag'} mr-1"></i>${channel_name}`);
        } else {
            $('.cw_channel_title').html(`${channel_name}`);
        }

        //loadChannelMessages(currentChannelId, 0);
        await load_channel_messages(currentChannelId);

        var member_arr = channelInfo.members;
        let chatwith = member_arr.filter(item => item !== my_pToken)[0];

        $("#channel-chat").attr("data-chatwith", chatwith);

        const [userLiveStatus, userInfo] = await Promise.all([
            getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
            getUserInfo(chatwith, 'full').catch((e) => {console.log(e)}),
        ]);
        await updateProfileInfo(userInfo, userLiveStatus);

       // console.log("userInfo", userInfo);
        //console.log("userLiveStatus2", userLiveStatus.output);

        //loadRightSidebar('profile');

        $('.user-profile-desc .openchatacc').hide();

        $('#activities_block1').remove();
        $('#members_list').html("");

        if ($('#members_block #members_list').length === 0) {
            $('#members_block').html(`
            <div class="p-3 border-bottom">
                <div class="pb-4 border-bottom border-bottom-dashed mb-4">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="fs-16 text-muted text-uppercase">Members</h5>
                        </div>
                    </div>
                    <ul id="members_list" class="list-unstyled chat-list mx-n4">
                    </ul>
                </div>
            </div>`);
        }

        if (channelInfo.members != undefined && channelInfo.members.length > 0) {
            $.each(channelInfo.members, async function (mkey, memtoken) {
                var d_data = await getUserInfo(memtoken, 'public');
                if (d_data.avatar_image != '' && d_data.avatar_image != undefined) {
                    var avatar_image = d_data.avatar_image;
                } else if (d_data.avatar != undefined && d_data.avatar != 'default') {
                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + d_data.avatar + '.png';
                } else {
                    var avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                }
                if ($('#members_list').find(`#member_${memtoken}`).length === 0) {
                    var members = `
                        <li id="member_${memtoken}" data-chatwith="${memtoken}" class="${(memtoken != "Organizer") ? 'openchatacc' : ''}">
                            <a href="javascript: void(0);">
                                <div class="d-flex align-items-center">
                                    <img src="${avatar_image}" alt="" class="avatar-sm rounded-circle me-3">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="text-truncate mb-0">${d_data.chat_name}</h6>
                                    </div>
                                    ${memtoken != my_pToken ?
                        `<div>
                                    <div  class="${memtoken}_loader taoh-loader taoh-spinner" id="pc_loader"
                                    style="width:20px;height:20px;display:none;"
                                    ></div>
                                    <button type="button" id="${memtoken}" class="btn btn-sm mr-2 ${(memtoken == "Organizer") ? 'd-none' : ''}" data-chatwith="${memtoken}" data-chatname="${d_data.chat_name}" data-live="" style="white-space: nowrap;font-size: small;">
                                            Chat <i class="la la-angle-double-right"></i></button></div>` : ''}
                                </div>
                            </a>
                        </li>`;
                    $('#members_list').append(members);
                }
            });
            $('#members_block').show();
        } else {
            $('#members_list').append(`<li><a href="javascript: void(0);">
                <div class="d-flex align-items-center"><div class="flex-grow-1 overflow-hidden">
                <h6 class="text-truncate mb-0" style="text-aign:center">No Members</h6></div></div></a></li>`);

            $('#members_block').hide();
        }
        $('#chat_input').focus();

        if($('.have_updates').length == 0) {
            //$('.unread-mentions').addClass('d-none');
        } else {
            //$('.unread-mentions').removeClass('d-none');
        }

        // $(".channelList li").removeClass("disabled"); // optional: style disabled
        // $(".myChannelList li").removeClass("disabled");
        // $(".dmChannelList li").removeClass("disabled");
        // $(".organizerChannelList li").removeClass("disabled");
        // $(".exhibitorChannelList li").removeClass("disabled");
        // $(".sessionChannelList li").removeClass("disabled");
    }

    $(document).on("click", ".dmChannelList li", async function() {
        CURRENT_BLOCK_VISITING = 'dm';

        $('.cw_channel_sub_title').html('');
        $('.channel_toggle').addClass('d-none');
        stopChannelUpdate = true;
        $('.watchPartySection').hide();
        $('.watchPartySection').removeClass('watchPartyEnabled');
        $('.chat-leftsidebar').removeClass('watchPartyEnabled');
        const currentElem = $(this);
        $('#chat_input').val("");
        $('.pin-message-v2-dm').hide();
        await loadConversation(currentElem);
    });

    $(document).on("click", ".organizerChannelList li", async function() {
        $('.channel_toggle').addClass('d-none');
        $('.cw_channel_profile_img').hide();
        $('.watchPartySection').hide();
        $('.watchPartySection').removeClass('watchPartyEnabled');
        $('.chat-leftsidebar').removeClass('watchPartyEnabled');
        const currentElem = $(this);
        await loadConversation(currentElem);
    });

    function readmore(i) {
        var dots = document.getElementById("dots_" + i);
        var moreText = document.getElementById("more_" + i);
        var btnText = document.getElementById("morebtn_" + i);

        if (dots.style.display === "none") {
            dots.style.display = "inline";
            btnText.innerHTML = "read more";
            moreText.style.display = "none";
        } else {
            dots.style.display = "none";
            btnText.innerHTML = "read less";
            moreText.style.display = "inline";
        }
    }

    async function updateProfileInfo(userInfo, userLiveStatus, fromParticipants = 0) {
        const curUserData = await getUserInfo(my_pToken, 'public').catch((e) => {
            console.log(e)
        });

        chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

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
            <span class="readmore-btn" style="color: #007bff;" onclick="readmore('es')" id="morebtn_es">read more</span>`;
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
            userProfileData += `<p class="message-restriction mx-3">To see <span class="text-capitalize">${userInfo.chat_name}</span> profile, complete your settings <a href="${_settings_url}" target="_blank">now</a></p>`;
        } else {
            userProfileData += `<!-- Start profile user -->
                <div class="">
                    <div class="user-profile-img">
                        <div class="p-v1-header">
                            <div class="user-chat-nav p-2" style="position: absolute; top: 0; left: 0;">
                                <div class="d-flex w-100">
                                    <div class="flex-grow-1">
                                        <button type="button" class="btn nav-btn user-profile-show bg-white rounded-circle" style="border: 1px solid #d3d3d3;">
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
                            <p class="fs-14 text-truncate user-profile-status mt-1 mb-0">
                                <i class="bx bxs-circle fs-10 ${chatwith_liveStatus ? 'text-success' : 'text-warning'} me-1 ms-0"></i>
                                ${live_status}
                            </p>

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
            <a class="btn bor-btn openProfileModal view_more" data-pagename="${pagename}" data-profile_token="${userInfo.ptoken}">View More</a>

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
                       // console.log('Invalid JSON in hobbies field:', error);
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

            userProfileData += `<div class="d-flex justify-content-center flex-wrap mt-3" style="gap: 12px;">
                                <!--<a href="javascript:void(0)" class="openchatacc btn message-n-btn">Message</a>-->
                                <a style="display:none;" target="_blank" href="${_taoh_site_url_root + '/profile/' + userInfo.ptoken}"
                                class="btn view-n-btn">View Full Profile</a>
                                <div>
                                </div>
                            </div>
                        </div></div></div></div>
                        <div class="simplebar-placeholder" style="width: auto; height: 710px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 36px; display: block; transform: translate3d(0px, 0px, 0px);"></div></div></div>
            `;
        }

        if(fromParticipants == 1) {
            $('.participants_profile_info').html(userProfileData);
        } else {

            $('#user-profile-sidebar').html(userProfileData);
            //loadRightSidebar('profile');
        }
    }

    async function openChannel(channelId, channelType) {

       CURRENT_BLOCK_VISITING = 'CHANNEL';
       CURRENT_CHANNEL_ID = channelId;

        NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN = 0;
        VIDEO_POSTED_RECENTLY = 0;

        $('.pin-message-v2').hide();
        $('.comm_pin_message_div').remove();

        // Stop any existing dojo messages immediately
        stopDojoMessages();

        $('#channel_of_type').val('channel');

         // Clear previous channel's pending timer
        if (channelOpenTimer) {
            clearTimeout(channelOpenTimer);
        }
        channelOpenTimer = setTimeout(() => {
            console.log('-----restart at 1--------');
            if(VIDEO_POSTED_RECENTLY != 1)
                restartDojoMessages();
        }, 20000);

        setTimeout(function () {
           // alert(NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN)
            if(NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN != 2) {
                NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN = 1;
                stopDojoMessages();
                setTimeout(() => {
					console.log('-----restart at 2--------');
                    restartDojoMessages();

					NO_MESSAGE_POSTED_IN_CHANNEL_FOR_5MIN = 0;
                }, 5000);
            }
            //alert("I am here")

        }, 300000);


        $('#chat-send-btn i').removeClass('bx-spin').removeClass('bx-loader-alt').addClass('bxs-send');
        $("#chat-send-btn").prop("disabled", false);

        $('.cw_channel_profile_img').hide();
        const currentElem = $(`#channel-${channelId}`);

        currentChannelId = channelId;

        $('.participants_refresh_div').removeClass('participants-active');

        const store = objStores.ntw_store.name;

        let channelRoomSlug;
        // let ntwRoomChannels;
        if (channelType == taohChannelExhibitor || channelType == taohChannelSponsor || channelType == taohChannelSession) {
            channelRoomSlug = eventToken;
            // ntwRoomChannels = ['room', ntw_keyword, eventToken, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
        } else {
            channelRoomSlug = roomslug;
            // ntwRoomChannels = ['room', ntw_keyword, roomslug, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
        }
        // var [channelInfo] = await getIntaoDataById(store, ntwRoomChannels, channelId);

        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: channelId,
            type: channelType,
            my_pToken
        });
        const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

        if (channelInfo?.show_default) {
            const isIAMMember = (channelInfo.members).includes(my_pToken);
            if (!isIAMMember) {
                joinChannel(channelRoomSlug, channelId, channelType, my_pToken).then(() => {
                    getNTWChannelById({
                        roomslug,
                        global_slug: eventToken,
                        keyword: ntw_keyword,
                        channel_id: channelId,
                        type: channelType,
                        my_pToken
                    }, true);
                });
            }
        }

        //[channelInfo] = await getIntaoDataById(store, ntwRoomChannels, channelId);

        console.log("channelInfo ======== channelInfo", channelInfoResponse);

        videos_act = [];
        $('.recent_activity').addClass('d-none');

        $("#channel-conversation-list").html("");
        loadchatWindow("channel-chat");
        //$('.pin-message-v2').hide();

        if(Array.isArray(tempUserChannelArray)){
            tempUserChannelArray.forEach(function(item) {
                const tempChannelId = item.id;
                if(tempChannelId != currentChannelId) {
                    tempUserChannelArray = [];
                    $(`#channel-${tempChannelId}.temp`).remove();
                }
            });
        }

        addRemoveActive(currentElem);

        let channel_name = currentElem.attr('data-channel_name');
        $("#channel-chat").attr("data-channel_id", channelId);
        $("#channel-chat").attr("data-channel_type", channelType);
        $("#channel-chat").attr("data-channel_name", channel_name);

        const sendTranscriptBtn = $('.send_transcript_btn');
        if(sendTranscriptBtn.length) {
            sendTranscriptBtn.setSyncedData('channelid', currentChannelId);
            sendTranscriptBtn.setSyncedData('channeltype', channelType);
            sendTranscriptBtn.setSyncedData('channelname', String(channel_name ?? ''));
        }

        if (currentElem.hasClass('have_updates')) {
            currentElem.removeClass('have_updates');
        }
        await setHaveUpdateStatus(channelId, 0);

        $('.chat-input-bottom').addClass('d-block');

        $('.comm_pin_message_div').removeClass('d-flex').addClass('d-none');
        $('.pin_message_div' + currentChannelId).removeClass('d-none').addClass('d-flex');

        $('#participants').removeClass('d-block');
        $('#channel-chat').addClass('d-block');
        $('#chat-input-container').show();
        $('.chat-input-bottom').removeClass('d-none');
        if(channelType == taohChannelDefault){
            $('.cw_channel_title').html(`<i class="la ${channelInfo.visibility === 'private' ? 'la-lock' : 'la-hashtag'} mr-1"></i>${channel_name}`);
        } else {
            $('.cw_channel_title').html(`${channel_name}`);
        }
        $('.channnel_collapsible').html(convertLinks(taoh_desc_decode_new(channelInfo?.data?.description)) || channelInfo?.data?.name);

        await load_channel_messages(currentChannelId);

        console.log("channelInfo for streaming link ========", channelInfo);
        let channel_streaming_link = null;
        let channel_other_link = null;
        if(channelInfo?.data?.streaming_link) {
            channel_streaming_link = getEmbedSrc(channelInfo.data.streaming_link);
            channel_other_link = (channelInfo.data.streaming_link);
        }

        $('.view_details_link').remove();
        if(channelInfo?.data?.eventtoken && channelInfo?.data?.exhibitor_id) {
            $('.cw_channel_title').append(`<a class="view_details_link" target="_blank" href="${_taoh_site_url_root}/events/exhibitors/${channelInfo.data.exhibitor_id}/${channelInfo.data.eventtoken}"><i class="ml-2 text-primary fa fa-external-link"></i></a>`);
        }
        if(channelInfo?.data?.eventtoken && channelInfo?.data?.speaker_id) {
            $('.cw_channel_title').append(`<a class="view_details_link" target="_blank" href="${_taoh_site_url_root}/events/speaker/${channelInfo.data.eventtoken}/${channelInfo.data.speaker_id}"><i class="ml-2 text-primary fa fa-external-link"></i></a>`);
        }

        var join_status = false;

        if(channelInfo?.data?.spk_datefrom && channelInfo?.data?.spk_dateto && channelInfo?.data?.spk_timezoneSelect) {
            let event_timestamp_start_data = {
                utc_datetime: channelInfo.data.spk_datefrom.replace(/[T:-]/g,'')+'00',
                local_datetime: channelInfo.data.spk_datefrom.replace(/[T:-]/g,'')+'00',
                timezone: channelInfo.data.spk_timezoneSelect,
                locality: ""
            };
            let event_timestamp_end_data = {
                utc_datetime: channelInfo.data.spk_dateto.replace(/[T:-]/g,'')+'00',
                local_datetime: channelInfo.data.spk_dateto.replace(/[T:-]/g,'')+'00',
                timezone: channelInfo.data.spk_timezoneSelect,
                locality: ""
            };

            let start_at = channelInfo.data.spk_datefrom.replace(/[T:-]/g,'')+'00';
            let end_at = channelInfo.data.spk_dateto.replace(/[T:-]/g,'')+'00';

            join_status = isJoinEnabled(channelInfo.data);

            console.log("event_live_status ===>>", join_status);


            let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy',0);
            let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A',1);

            let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy',0);
            let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A',1);

            if(startdate == enddate){
                $('.cw_channel_sub_title').html(`<p class="n-info-badge mr-2" >${startdate}, ${starttime} - ${endtime}</p>`);
            }else{
                $('.cw_channel_sub_title').html(`<p class="n-info-badge mr-2" >${startdate} ${starttime} - ${enddate} ${endtime}</p>`);
            }
        } else {
            join_status = true;
            $('.cw_channel_sub_title').html('');
        }

        if(channel_streaming_link) {
            $('.animated-menu').removeClass('d-sm-block').addClass('d-none');
            $('#watchpartyiframe').attr('src', channel_streaming_link);
            $('#watchpartyiframe').show();
            $('#watchPartyMeetingLinkDiv').hide();
            $('.watchPartySection').show();
            $('.watchPartySection').addClass('watchPartyEnabled');
            $('.chat-leftsidebar').addClass('watchPartyEnabled');
        } else if(channel_other_link) {
            $('.animated-menu').removeClass('d-sm-block').addClass('d-none');
            $('#watchPartyMeetingLink').attr('href', channel_other_link);
            if(join_status === false) {
                $('#watchPartyMeetingLink').addClass('disabled');
                $('#watchPartyMeetingLink').removeClass('btn-primary').addClass('btn-secondary');
            } else {
                $('#watchPartyMeetingLink').removeClass('disabled');
                $('#watchPartyMeetingLink').removeClass('btn-secondary').addClass('btn-primary');
            }
            $('#watchpartyiframe').hide();
            $('#watchPartyMeetingLinkDiv').show();
            $('.watchPartySection').show();
            $('.watchPartySection').addClass('watchPartyEnabled');
            $('.chat-leftsidebar').addClass('watchPartyEnabled');
        } else {
            $('.animated-menu').removeClass('d-none').addClass('d-sm-block');
            $('.watchPartySection').hide();
            $('.watchPartySection').removeClass('watchPartyEnabled');
            $('.chat-leftsidebar').removeClass('watchPartyEnabled');
            loadRightSidebar('members');
        }

        $('#activities_block1').remove();
        $('#members_list').html("");


        if ($('#members_block #members_list').length === 0) {
            $('#members_block').html(`

            <!-- new template activity-v2 redesign-->
            <div class="p-3 d-none">
                <div class="card activity-v2-card">
                    <div class="activity-v2-header">
                        <p>Here is what happening !</p>
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="activity-v2-body">
                        <div class="activity-v2-list">
                            <div class="act-count-v2">
                                <svg width="19" height="25" viewBox="0 0 19 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.6727 24.4382C13.2109 21.2953 19 13.678 19 9.39932C19 4.21011 14.7448 0 9.5 0C4.25521 0 0 4.21011 0 9.39932C0 13.678 5.78906 21.2953 8.32734 24.4382C8.93594 25.1873 10.0641 25.1873 10.6727 24.4382ZM9.5 6.26622C10.3399 6.26622 11.1453 6.59631 11.7392 7.18388C12.333 7.77145 12.6667 8.56837 12.6667 9.39932C12.6667 10.2303 12.333 11.0272 11.7392 11.6148C11.1453 12.2023 10.3399 12.5324 9.5 12.5324C8.66015 12.5324 7.85469 12.2023 7.26083 11.6148C6.66696 11.0272 6.33333 10.2303 6.33333 9.39932C6.33333 8.56837 6.66696 7.77145 7.26083 7.18388C7.85469 6.59631 8.66015 6.26622 9.5 6.26622Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p><b>05</b> People Joined the event from Newyork</p>
                                <div class="act-v2-link-con">
                                    <a href="#" class="btn act-v2-link border-0 shadow-none">View People</a>
                                    <a href="#" class="btn act-v2-link border-0 shadow-none">Message</a>
                                </div>
                            </div>
                        </div>
                        <div class="activity-v2-list">
                            <div class="act-count-v2">
                                <svg width="20" height="13" viewBox="0 0 20 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p><b>06</b> People Joined Marketing Video Room</p>
                                <div class="act-v2-link-con">
                                    <a href="#" class="btn act-v2-link border-0 shadow-none">Check Video Room</a>
                                </div>
                            </div>
                        </div>
                        <div class="activity-v2-list">
                            <div class="act-count-v2">
                                <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.15062 12.375C11.1004 12.375 14.3009 9.60469 14.3009 6.1875C14.3009 2.77031 11.1004 0 7.15062 0C3.2008 0 0.000376228 2.77031 0.000376228 6.1875C0.000376228 7.54453 0.505706 8.79961 1.36167 9.82266C1.24136 10.1531 1.0626 10.4449 0.873531 10.691C0.708525 10.909 0.540082 11.0777 0.416328 11.1937C0.354451 11.25 0.302886 11.2957 0.26851 11.3238C0.251322 11.3379 0.237572 11.3484 0.230697 11.352L0.223821 11.359C0.0347524 11.5031 -0.0477504 11.7562 0.0278772 11.9848C0.103505 12.2133 0.313199 12.375 0.550395 12.375C1.2998 12.375 2.05607 12.1781 2.68515 11.9355C3.00142 11.8125 3.29705 11.6754 3.55487 11.5348C4.61022 12.0691 5.83745 12.375 7.15062 12.375ZM15.4009 6.1875C15.4009 10.1355 11.9942 13.1098 7.95846 13.4648C8.7938 16.0805 11.5645 18 14.8509 18C16.164 18 17.3913 17.6941 18.4501 17.1598C18.7079 17.3004 19.0001 17.4375 19.3163 17.5605C19.9454 17.8031 20.7017 18 21.4511 18C21.6883 18 21.9014 17.8418 21.9736 17.6098C22.0458 17.3777 21.9667 17.1246 21.7742 16.9805L21.7674 16.9734C21.7605 16.9664 21.7467 16.9594 21.7295 16.9453C21.6952 16.9172 21.6436 16.875 21.5817 16.8152C21.458 16.6992 21.2895 16.5305 21.1245 16.3125C20.9355 16.0664 20.7567 15.7711 20.6364 15.4441C21.4924 14.4246 21.9977 13.1695 21.9977 11.809C21.9977 8.54648 19.0791 5.87109 15.3768 5.63906C15.3906 5.81836 15.3975 6.00117 15.3975 6.18398L15.4009 6.1875Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p><b>12</b> People Joined Marketing Tips and Tricks Channel</p>
                                <div class="act-v2-link-con">
                                    <a href="#" class="btn act-v2-link border-0 shadow-none">Check What’s Happening !</a>
                                </div>
                            </div>
                        </div>
                        <div class="activity-v2-list">
                            <div class="act-count-v2">
                                <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.75 0C1.2332 0 0 1.23326 0 2.75012V15.1257C0 16.6425 1.2332 17.8758 2.75 17.8758H6.875V21.3134C6.875 21.5755 7.02109 21.8119 7.25313 21.9279C7.48516 22.0439 7.76445 22.0181 7.975 21.8634L13.2902 17.8758H19.25C20.7668 17.8758 22 16.6425 22 15.1257V2.75012C22 1.23326 20.7668 0 19.25 0H2.75Z" fill="white"/>
                                </svg>
                            </div>
                            <div>
                                <p><b>999+</b> People Joined Marketing Tips and Tricks Channel</p>
                                <div class="act-v2-link-con">
                                    <a href="#" class="btn act-v2-link border-0 shadow-none">Check What’s Happening !</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 border-bottom">
                <div class="pb-4 border-bottom border-bottom-dashed mb-4 recent_activity d-none">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="fs-16 text-muted text-uppercase">Recent Activity</h5>
                        </div>
                    </div>
                    <div class="video-room-activity"></div>
                </div>
                <div class="pb-2 mb-0">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h5 class="fs-16 text-muted text-uppercase">Members</h5>
                        </div>
                    </div>
                    <ul id="members_list" class="list-unstyled chat-list mx-n4">
                    </ul>
                </div>
            </div>`);
        }

        var channelMembers = [];
        if (Array.isArray(channelInfo.members)) {
            channelMembers = channelInfo.members;
        }
        if (!channelMembers.includes(my_pToken)) {
            channelMembers.push(my_pToken);
        }

        var membersData = {};
        if(channelInfo?.members_data) {
            membersData = channelInfo.members_data;
        }

        CHANNEL_MEMBERS_COUNT = channelMembers.length;
        //alert(CHANNEL_MEMBERS_COUNT)

        if (channelMembers != undefined && channelMembers.length > 0) {
            const initialCount = 10;
            let displayedCount = 0;

            const userInfoCache = new Map();
            await Promise.all(
                channelMembers.map(async (ptoken) => {
                    try {
                        let memberJson = membersData[ptoken];
                        var info = null;
                        if(memberJson) {
                            let parsedMember = JSON.parse(memberJson.trim());
                            info = parsedMember?.output?.user;
                        }

                        if(!info || info == null) {
                            info = await getUserInfo(ptoken, 'public', false, true);
                        }
                        userInfoCache.set(ptoken, info);
                    } catch (e) {
                        console.error(`Failed to fetch user ${ptoken}:`, e);
                        userInfoCache.set(ptoken, {
                            chat_name: ptoken,
                            avatar_image: '',
                            avatar: 'default'
                        });
                    }
                })
            );

            async function renderMembers(limit) {
                let sliceMembers = channelMembers.slice(displayedCount, displayedCount + limit);

                for (const memtoken of sliceMembers) {
                    var d_data = userInfoCache.get(memtoken);

                    var avatar_image = '';
                    if (d_data.avatar_image && d_data.avatar_image != '') {
                        avatar_image = d_data.avatar_image;
                    } else if (d_data.avatar && d_data.avatar != 'default') {
                        avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/' + d_data.avatar + '.png';
                    } else {
                        avatar_image = _taoh_ops_prefix + '/avatar/PNG/128/avatar_def.png';
                    }

                    if ($('#members_list').find(`#member_${memtoken}`).length === 0) {
                        var members = `
                            <li id="member_${memtoken}" data-chatwith="${memtoken}" class="${(memtoken != "Organizer") ? 'openchatacc' : ''}">
                                <a href="javascript:void(0);">
                                    <div class="d-flex align-items-center">
                                        <img src="${avatar_image}" alt="" class="avatar-sm rounded-circle me-3">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="text-truncate mb-0">${d_data.chat_name}</h6>
                                        </div>
                                        ${memtoken != my_pToken ? `
                                        <div>
                                            <div class="${memtoken}_loader taoh-loader taoh-spinner" id="pc_loader"
                                                style="width:20px;height:20px;display:none;"></div>
                                            <button type="button" id="${memtoken}"
                                                class="btn btn-sm mr-2 ${(memtoken == "Organizer") ? 'd-none' : ''}"
                                                data-chatwith="${memtoken}"
                                                data-chatname="${d_data.chat_name}"
                                                style="white-space: nowrap;font-size: small;">
                                                Chat <i class="la la-angle-double-right"></i>
                                            </button>
                                        </div>` : ''}
                                    </div>
                                </a>
                            </li>`;
                        $('#members_list').append(members);
                    }
                }

                displayedCount += limit;

                // Handle "Show more" button visibility
                if (displayedCount >= channelMembers.length) {
                    $('#show_more_members').hide();
                } else {
                    $('#show_more_members').show();
                }

                $('#members_block').show();
            }

            // Initial render (first 20)
            renderMembers(initialCount);

            // Add "Show More" button if not already present
            if ($('#show_more_members').length === 0) {
                $('#members_block').append(`
                    <div class="text-center mt-2">
                        <button id="show_more_members" ${(displayedCount >= channelMembers.length) ? 'style="display:none"' : ''} class="btn btn-link">Show more</button>
                    </div>
                `);
            }

            // Click handler for "Show more"
            $(document).off('click', '#show_more_members').on('click', '#show_more_members', function () {
                renderMembers(initialCount); // load next 20
            });
        }

        $('#chat_input').focus();
        $('#chat_input').val("");
    }

    $(document).on("click", ".cw_channel_title, .cw_channel_profile_img", function() {
        let channel_type = $('#channel-chat').attr('data-channel_type');
        if(channel_type == 4) {
            loadRightSidebar('profile');
        }
    });

    $(document).on("click", ".unread-mentions", function() {
        var offset = 100;
        if($('#channel-chat:visible').length) {
            $(".chat-leftsidebar").animate({
                scrollTop: $("#dmCollapse").offset().top - $(window).height() + $("#dmCollapse").outerHeight() + offset
            }, 400);
        } else {
            $("html, body").animate({
                scrollTop: $("#dmCollapse").offset().top - $(window).height() + $("#dmCollapse").outerHeight() + offset
            }, 400);
        }
        //$('.unread-mentions').addClass('d-none');
    });

    $(document).on("click", ".speedChannelList li", async function() {

        CURRENT_BLOCK_VISITING ='speed networking';


        let refBtnElem = $('#speednetworking_refresh_btn');
        let refBtnElemIcon = refBtnElem.find('i');
        refBtnElemIcon.removeClass('fa-refresh').addClass('fa-spinner fa-spin');

        $('.cw_channel_profile_img').hide();
        $('.watchPartySection').hide();
        $('.watchPartySection').removeClass('watchPartyEnabled');
        $('.chat-leftsidebar').removeClass('watchPartyEnabled');

        $('#tourbutton').removeClass('d-xl-block');

        $('.participants_refresh_div').removeClass('participants-active');

        const currentElem = $(this);
        const channelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        $('#channelList .channel-item').each(function() {
            $(this).removeClass('active');
        });
        $('#myChannelList .channel-item').each(function() {
            $(this).removeClass('active');
        });

        currentChannelId = channelId;

        $('#browse_channels_wrapper').addClass('d-none');

        $(this).removeClass('have_updates');

        $('.speedChannelList li').each(function() {
            $(this).removeClass('active');
        });

        $(`.speed_networking_div .carousel-item`).each(function() {
            $(this).removeClass('d-none');
        });

        currentElem.addClass('active');

        $('#participants-sidebar').show();

        const store = objStores.ntw_store.name;
        const ntwRoomChannels = ['room', ntw_keyword, roomslug, my_pToken, channelType, 'channels'].filter(Boolean).join('_');
        const [channelInfo] = await getIntaoDataById(store, ntwRoomChannels, currentChannelId);

        console.log('channelId channelId: '+channelInfo);

        // let channelInfoResponse = await getNTWChannelById({
        //     roomslug,
        //     global_slug: eventToken,
        //     keyword: ntw_keyword,
        //     channel_id: channelId,
        //     type: channelType,
        //     my_pToken
        // });
        // const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

        //console.log('channelInfo channelInfo: SN'+ channelInfoResponse?.response?.channels);

        $('.sn_channel_title').text(channelInfo?.data?.name);
        $('.sn_channel_description').text(channelInfo?.data?.description || channelInfo?.data?.name);

        loadchatWindow("speed_networking");

        if(intervalId2) {
           // console.log("there intervalId2", intervalId2);
            return;
        }

        console.log("channelInfo.members", channelInfo.members);

        speedNetworkingAddUser(channelInfo.members, channelInfo.restricted_ptokens);

        var channelMembers = channelInfo.members;

        //if (!channelMembers.includes(my_pToken)) {

            let data = {
                'taoh_action': 'taoh_ntw_speed_networking_add_user',
                'key': my_pToken,
                'ptoken': my_pToken,
                'keyslug': ntw_room_key,
                'keyword': ntw_keyword,
                'channel_type': channelType,
                'channel_id': currentChannelId,
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
                        console.log("sn user add res", res);
                        loader(false, loaderAreaSp);
                        let resultArr = res;
                        if(res != null && res.success) {
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

        //}

        if(!intervalId2) {
            $('.successMatchDiv').addClass('d-none');
            $('.notAvailableDiv').addClass('d-none');
            $('.rejectDiv').addClass('d-none');
            $('.speed_networking_carousel .carousel-item').each(function() {
                $(this).find('.connect_btn').prop('disabled', false).text('Connect');
                $(this).find('.not_interested_btn').prop('disabled', false).text('Not interested');
            });
            updateSpeedNetworkingCarousel();
        }

        $('.speed_networking_hints').show();

        var track_data = {
            'action': 'speed_networking_count',
            'channel_id': currentChannelId,
            'ptoken': my_pToken
        };
        taoh_track_activities(track_data);

        loadSpeedNetworkingData(currentChannelId);

    });

    let isRendering = false;

    $(document).on("click", ".channelList .channel_btn, .myChannelList .channel_btn ", async function (e) {

        stopChannelUpdate = true;

        $('.channel_toggle').removeClass('d-none');

        if (isRendering) {
            //console.log("Render in progress — click ignored");
            return;
        }

        $('#speedChannelList .channel-item').each(function() {
            $(this).removeClass('active');
        });
        $('#channelList .channel-item').each(function() {
            $(this).removeClass('active');
        });
        $('#myChannelList .channel-item').each(function() {
            $(this).removeClass('active');
        });

        $('.chat-input-bottom').removeClass('d-none').addClass('d-flex');

        const currentElem = $(this).closest('li');
        const channelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        //console.log("channelId channelId", channelId);


        let channelInfoResponse = await getNTWChannelById({
            roomslug,
            global_slug: eventToken,
            keyword: ntw_keyword,
            channel_id: channelId,
            type: channelType,
            my_pToken
        });

        const channelInfo = channelInfoResponse?.response?.channels?.find(c => String(c?.id) === channelId);

        console.log(" .channelList channelInfo ===", channelInfo);

        if (eventToken && channelInfo?.channel_ticket_type && rsvpSlug && channelInfo?.channel_ticket_type !== rsvpSlug && my_pToken !== channelInfo?.data?.ptoken) {
            taoh_set_warning_message('You can not access this channel.');
            return;
        }

        // Lock UI while rendering
        isRendering = true;
        // $(".channelList li").addClass("disabled"); // optional: style disabled
        // $(".myChannelList li").addClass("disabled");
        // $(".dmChannelList li").addClass("disabled");
        // $(".organizerChannelList li").addClass("disabled");
        // $(".exhibitorChannelList li").addClass("disabled");
        // $(".sessionChannelList li").addClass("disabled");

        try {
            // Channel passcode verification
            const channel_passcode_done = `passcode_verified_${channelId}_${channelType}`;
            if (channelInfo?.encrypted_passcode) {
                if (localStorage.getItem(channel_passcode_done) == 1 || my_pToken == channelInfo?.data?.ptoken) {
                    await openChannel(channelId, channelType);
                } else {
                    // Show modal to enter passcode
                    $('#channel_password_channel_id').val(channelId);
                    $('#channel_password_channel_type').val(channelType);
                    $('#channel_encrypted_passcode').val(channelInfo.encrypted_passcode);
                    $('#passcode').val('');
                    $('#passcode_error').hide().text('');

                    const modalEl = document.getElementById('channel_password_modal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            } else {
                // No passcode required, proceed
                await openChannel(channelId, channelType); // ⬅️ await ensures lock stays until render done
            }
        } finally {
            // Always unlock
            isRendering = false;
            // $(".channelList li").removeClass("disabled");
            // $(".myChannelList li").removeClass("disabled");
            // $(".dmChannelList li").removeClass("disabled");
            // $(".organizerChannelList li").removeClass("disabled");
            // $(".exhibitorChannelList li").removeClass("disabled");
            // $(".sessionChannelList li").removeClass("disabled");
        }
    });

    $(document).on("click", ".exhibitorChannelList .channel_btn", async function() {

        $('.channel_toggle').addClass('d-none');

        stopChannelUpdate = true;
        const currentElem = $(this).closest('li');
        const channelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        $('.chat-input-bottom').addClass('d-flex');

        openChannel(channelId, channelType);
    });

    $(document).on("click", ".chat_with_organizer", async function() {

        $('.channel_toggle').addClass('d-none');
        stopChannelUpdate = true;
        const currentElem = $(this);
        const channelId = currentElem.attr("data-channel_id");
        const channelType = taohChannelOrganizer;

        openChannel(channelId, channelType);
    });

    $(document).on("click", ".sponsorChannelList .channel_btn", async function() {
        $('.channel_toggle').addClass('d-none');
        stopChannelUpdate = true;
        const currentElem = $(this).closest('li');
        const channelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        openChannel(channelId, channelType);
    });

    $(document).on("click", ".sessionChannelList .channel_btn", async function() {
        $('.channel_toggle').addClass('d-none');
        stopChannelUpdate = true;
        const currentElem = $(this).closest('li');
        const channelId = currentElem.attr("data-channel_id");
        const channelType = currentElem.attr("data-channel_type");

        $('.chat-input-bottom').addClass('d-flex');

        openChannel(channelId, channelType);
    });

    // Handle submit button click inside modal
    $('#channel_password_form').on('submit', async function(e) {
        e.preventDefault();
        const modalEl = $('#channel_password_modal');
        const passcode_error = $('#passcode_error');
        const submit_btn = $('#channel_password_submit');

        const channelId = $('#channel_password_channel_id').val();
        const channelType = $('#channel_password_channel_type').val();
        const channel_encrypted_passcode = $('#channel_encrypted_passcode').val();
        const enteredPasscode = modalEl.find('#passcode').val()?.trim() || "";

        if (!enteredPasscode) {
            passcode_error.show().text("Please enter the channel passcode.");
            return;
        }

        try {
            passcode_error.hide();
            submit_btn.prop('disabled', true).text('Verifying...');
            const response = await $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    taoh_action: 'checkChannelPasscode',
                    channel_id: channelId,
                    channel_type: channelType,
                    passcode: enteredPasscode,
                    encrypted_passcode: channel_encrypted_passcode
                }
            });

            if (response.success) {
                submit_btn.prop('disabled', false).text('Submit');
                modalEl.modal('hide');

                const channel_passcode_done = `passcode_verified_${channelId}_${channelType}`;
                localStorage.setItem(channel_passcode_done, 1);

                taoh_set_success_message("Passcode verified successfully!");

                // Proceed to open the channel
                openChannel(channelId, channelType);
            } else {
                submit_btn.prop('disabled', false).text('Submit');
                passcode_error.show().text("Incorrect passcode. Please try again.");
            }
        } catch (err) {
            console.error("Error checking passcode:", err);
            submit_btn.prop('disabled', false).text('Submit');
            passcode_error.show().text("Something went wrong. Please try again.");
        }
    });

    $(document).on('click', '.toggle-btn', function () {
        const toggleText = $(this).find('.toggleText');
        if (toggleText.text() === 'More Details') {
            toggleText.text('Less Details');
            $('.collapsible').addClass('open');
            $(this).find('.drp-dwn-svg').css('transform', 'rotate(180deg)');
        } else {
            toggleText.text('More Details');
            $('.collapsible').removeClass('open');
            $(this).find('.drp-dwn-svg').css('transform', 'rotate(0deg)');
        }
    });

    async function createGoogleMeet(summary) {

        let now = new Date();
        let oneHourLater = new Date(now.getTime() + 60 * 60 * 1000);
        let start_datetime = now.toISOString().split('.')[0];
        let end_datetime = oneHourLater.toISOString().split('.')[0];
        let timezone = "America/New_York";

        let data = {
            'taoh_action': 'taoh_create_google_meet_link',
            summary,
            start_datetime,
            end_datetime,
            timezone
        };
        const response = await new Promise((resolve, reject) => {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (res) {
                    console.log("taoh_create_google_meet_link", res);
                    resolve(res);
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status);
                    resolve(status);
                }
            });
        });
        return response;
    }

    async function loadDmWindow(to_ptoken, openVideoRoom = 0, videolink = "", videoname = "", open_dm = true) {

        $('#carousel_item_'+to_ptoken).remove();
        $('#chat-input-container').show();
        $('.chat-input-bottom').removeClass('d-none');

        var video_msg = "";
        if(openVideoRoom == 1) {
            video_msg = `<p class="mb-0 ctext-content fs-12 fw-400"> Join <a href="${videolink}" video_name="${videoname}" link="${videolink}" channel_of_type="channel" target="_blank" class="d-inline-flex align-items-center join-v-link" style="gap: 4px;"> ${videoname} </a> - Video Room </p>`;
        }

        const input = [roomslug, my_pToken, to_ptoken].sort().join('_');
        const channelId = await generateSecureSlug(input, 16);
        currentChannelId = channelId;
        if (!channelId) {
            console.error('Channel ID not found.');
            return;
        }
        const channelElem = $(`#channel-${channelId}`);
        if (channelElem.length) {
            if(open_dm) {
                channelElem.trigger('click');
            }
            if(openVideoRoom == 1) {
                setTimeout(() => {
                    //$('#chat_input').val(video_msg);
                    //$('#chat-send-btn').trigger('click');
                    send_message(channelId, taohChannelDm, video_msg, 1);
                }, 2000);
            }
            $('#chat-input-container').show();
            $('.chat-input-bottom').removeClass('d-none');
        } else {
            // Create DM channel
            const channelData = JSON.stringify({
                name: 'DM-' + my_pToken + '-' + to_ptoken,
                description: 'Private chat between two users',
                ptoken: my_pToken
            });

            const channelMembers = JSON.stringify([my_pToken, to_ptoken]);

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    taoh_action: 'taoh_ntw_create_channel',
                    roomslug,
                    keyword: ntw_keyword,
                    channel_id: channelId,
                    channel_type: taohChannelDm || 4,
                    channel_data: channelData,
                    channel_members: channelMembers,
                    key: my_pToken
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        // reload user dm channel list in left section
                        getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDm, my_pToken}, true).then(({requestData, response}) => {
                            return renderDMChannelList(response.channels);
                        }).then(() => {
                            if(open_dm) {
                                $(`#channel-${channelId}`).trigger('click');
                            }
                            if(openVideoRoom == 1) {
                                setTimeout(() => {
                                    //$('#chat_input').val(video_msg);
                                    //$('#chat-send-btn').trigger('click');
                                    send_message(channelId, taohChannelDm, video_msg, 1);
                                }, 2000);
                            }
                        });
                    } else {
                        if(res.error == "Channel already exists") {
                            if($(`#channel-${channelId}`).length) {
                                if(open_dm) {
                                    $(`#channel-${channelId}`).trigger('click');
                                }
                                if(openVideoRoom == 1) {
                                    setTimeout(() => {
                                        //$('#chat_input').val(video_msg);
                                        //$('#chat-send-btn').trigger('click');
                                        send_message(channelId, taohChannelDm, video_msg, 1);
                                    }, 2000);
                                }
                            } else {
                                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDm, my_pToken}, true).then(({requestData, response}) => {
                                    //console.log("response.channels", response);
                                    return renderDMChannelList(response.channels);
                                }).then(() => {
                                    if(isFromMembersModal) {
                                        hideMembersModal();
                                    }
                                    if(open_dm) {
                                        $(`#channel-${channelId}`).trigger('click');
                                    }
                                    if(openVideoRoom == 1) {
                                        setTimeout(() => {
                                            //$('#chat_input').val(video_msg);
                                            //$('#chat-send-btn').trigger('click');
                                            send_message(channelId, taohChannelDm, video_msg, 1);
                                        }, 2000);
                                    }
                                });
                            }
                        } else {
                            taoh_set_error_message("Error creating dm channel: " + (res.error || "Unknown error"), false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                        }

                    }
                },
                error: function(xhr, status, error) {
                    taoh_set_error_message("Create dm channel failed: " + error, false, 'toast-middle', [
                        {
                            text: 'OK',
                            action: () => {},
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        }
                    ]);
                }
            });

            var track_data = {
                'action': 'click_1_1',
                'channel_id': channelId,
                'channel_name': to_ptoken,
                'ptoken': my_pToken
            };
            taoh_track_activities(track_data);
        }
    }

    $(document).on('click', '.openchatacc', async function () {
        const currentElem = $(this);

        const to_ptoken = currentElem.getSyncedData('chatwith').toString();

        const input = [roomslug, my_pToken, to_ptoken].sort().join('_');
        const channelId = await generateSecureSlug(input, 16);

        currentChannelId = channelId;

        if (!channelId) {
            console.error('Channel ID not found.');
            return;
        }

        let isFromMembersModal = currentElem.hasClass('members_chat');

        const channelElem = $(`#channel-${channelId}`);
        if (channelElem.length) {
            if(isFromMembersModal) {
                hideMembersModal();
            }
            channelElem.trigger('click');
            $('#chat-input-container').show();
            $('.chat-input-bottom').removeClass('d-none');
        } else {

            const chatButtonIconElem = currentElem.find('i');
            chatButtonIconElem.removeClass('la-angle-double-right').addClass('la-spinner la-spin');
            chatButtonIconElem.prop('disabled', true);

            // Create DM channel
            const channelData = JSON.stringify({
                name: 'DM-' + my_pToken + '-' + to_ptoken,
                description: 'Private chat between two users',
                ptoken: my_pToken
            });

            const channelMembers = JSON.stringify([my_pToken, to_ptoken]);

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    taoh_action: 'taoh_ntw_create_channel',
                    roomslug,
                    keyword: ntw_keyword,
                    channel_id: channelId,
                    channel_type: taohChannelDm || 4,
                    channel_data: channelData,
                    channel_members: channelMembers,
                    key: my_pToken
                },
                dataType: 'json',
                success: async function(res) {
                    //console.log("taoh_ntw_create_channel res", res);
                    if (res.success) {
                        chatButtonIconElem.removeClass('la-spinner la-spin').addClass('la-angle-double-right');
                        chatButtonIconElem.prop('disabled', false);

                        // reload user dm channel list in left section
                        getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDm, my_pToken}, true).then(({requestData, response}) => {
                            //console.log("response.channels", response);
                            return renderDMChannelList(response.channels);
                        }).then(async () => {
                            if(isFromMembersModal) {
                                hideMembersModal();
                            }
                            $('.openchatacc_chat_now_btn').html("Join now!");
                            $(`#channel-${channelId}`).trigger('click');
                            $('#chat-input-container').show();
                            $('.chat-input-bottom').removeClass('d-none');
                        });
                    } else {
                        $('.openchatacc_chat_now_btn').html("Join now!");
                        chatButtonIconElem.removeClass('la-spinner la-spin').addClass('la-angle-double-right');
                        chatButtonIconElem.prop('disabled', false);
                        if(res.error == "Channel already exists") {
                            if($(`#channel-${channelId}`).length) {
                                $(`#channel-${channelId}`).trigger('click');
                            } else {
                                getNTWUserChannels({roomslug, keyword: ntw_keyword, type: taohChannelDm, my_pToken}, true).then(({requestData, response}) => {
                                    //console.log("response.channels", response);
                                    return renderDMChannelList(response.channels);
                                }).then(async () => {
                                    if(isFromMembersModal) {
                                        hideMembersModal();
                                    }
                                    $(`#channel-${channelId}`).trigger('click');
                                });
                            }
                        } else {
                            taoh_set_error_message("Error creating dm channel: " + (res.error || "Unknown error"), false, 'toast-middle', [
                                {
                                    text: 'OK',
                                    action: () => {},
                                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                                }
                            ]);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    chatButtonIconElem.removeClass('la-spinner la-spin').addClass('la-angle-double-right');
                    chatButtonIconElem.prop('disabled', false);
                    taoh_set_error_message("Create dm channel failed: " + error, false, 'toast-middle', [
                        {
                            text: 'OK',
                            action: () => {},
                            class: 'dojo-v1-btn float-right mt-3 mb-3'
                        }
                    ]);
                }
            });

            var track_data = {
                'action': 'click_1_1',
                'channel_id': channelId,
                'channel_name': to_ptoken,
                'ptoken': my_pToken
            };
            taoh_track_activities(track_data);

        }
    });

    $('#channel_refresh').on('click', function () {
        const currentElem = $(this);
        const currentElem_icon = currentElem.find('i');
        currentElem_icon.removeClass('fa-refresh').addClass('fa-spinner fa-spin');

        const renderTasks = [];

        // Channels
        renderTasks.push(
            Promise.resolve(
                getNTWUserChannels({ roomslug, keyword: ntw_keyword, type: taohChannelDefault, my_pToken }, true)
            ).then(({ response }) => renderChannelList(response.channels, tempUserChannelArray))
        )

        // Exhibitor + Session Channels (event scope)
        if (eventToken) {
            renderTasks.push(
                Promise.resolve(
                    getNTWUserChannels({ roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelExhibitor, my_pToken }, true)
                ).then(({ response }) => renderExhibitorChannelList(response.channels))
            )

            renderTasks.push(
                Promise.resolve(
                    getNTWUserChannels({ roomslug, global_slug: eventToken, keyword: ntw_keyword, type: taohChannelSession, my_pToken }, true)
                ).then(({ response }) => renderSessionChannelList(response.channels))
            )
        }

        // wait for all renderTasks, then do the final UI update once
        Promise.allSettled(renderTasks)
            .finally(() => {
                $(`#channel-${currentChannelId}`).addClass('active');
                currentElem_icon.removeClass('fa-spinner fa-spin').addClass('fa-refresh');
            });
    });

    $("#chat-send-btn").on("click", async function() {

        let message = $("#chat_input").val().trim();
        let channelId = $("#channel-chat").attr("data-channel_id");
        let channelType = $("#channel-chat").attr("data-channel_type");

        if (!message || !channelId) return;

        let chatwith = "";

        if(channelType == 4) {
            chatwith = $("#channel-chat").attr("data-chatwith");
            const [userLiveStatus_cell] = await Promise.all([
                getUserLiveStatus(chatwith).catch((e) => {
                    console.log(e)
                }),
            ]);
            let chatwith_liveStatus = Boolean(userLiveStatus_cell.output) ? 1 : 0;
            console.log(chatwith, chatwith_liveStatus);

            if (!chatwith_liveStatus) {
                let locationPath = currentFullPath + '?chatwith=' + my_pToken;
                taoh_set_warning_message('It appears the user is currently offline. Would you like to send a copy of this message via email?', false, 'toast-middle', [
                    {
                        text: 'Yes',
                        action: () => {
                            $.post(_taoh_site_ajax_url, {
                                'taoh_action': 'taoh_post_message',
                                'message': message,
                                "ptoken": chatwith,
                                "location_path": locationPath
                            }, function () {
                                //proceedWithSending();
                            });
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3'
                    },
                    {
                        text: 'No',
                        action: () => {
                            //proceedWithSending();
                        },
                        class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                    }
                ]);
            }
        }

        $('#chat-send-btn i').removeClass('bxs-send').addClass('bx-spin').addClass('bx-loader-alt');
        $("#chat-send-btn").prop("disabled", true);
        $("#chat_input").prop("disabled", true);
        $("#chat_input").val("");
        send_message(channelId, channelType, message);
    });

    $("#chat-reply-send-btn").on("click", function() {
        let text = $("#chat_reply_input").val().trim();
        let channelId = $("#channel-chat").attr("data-channel_id");
        let channelType = $("#channel-chat").attr("data-channel_type");
        if (!text || !channelId) return;

        $('#chat-reply-send-btn i').removeClass('bxs-send').addClass('bx-spin').addClass('bx-loader-alt');
        $("#chat-reply-send-btn").prop("disabled", true);
        $("#chat_reply_input").prop("disabled", true);
        $("#chat_reply_input").val("");
        send_reply_message(channelId, channelType, text);
    });


    $(document).on("click", ".pin-btn", function(e) {
        e.stopPropagation();
        let btn = $(this);
        let pinned = btn.data("pinned") === 1 ? 0 : 1;
        btn.data("pinned", pinned);
        pin_channel(btn.data("channel"), pinned);
    });

    // =======================================
    // Init
    // =======================================

    async function getUserInfo(pToken_to, ops = 'public', serverFetch = false, stopServerFetch = false) {

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
            try {
                const formData = {
                    taoh_action: 'taoh_user_info',
                    ops: ops,
                    ptoken: pToken_to
                };
                const srv_userInfoObj = await fetchUserInfoFromServer(formData);
                srv_userInfoObj.last_fetch_time = Date.now();
                userInfo = srv_userInfoObj;
            } catch (e) {
                console.log('getUserInfo error:', e);
            }
        }

        // If userInfo not found, set default values
        if (!userInfo.ptoken) {
            if(pToken_to == "Organizer") {
                userInfo = {
                    ptoken: pToken_to,
                    chat_name: 'Organizer' ,
                    avatar: 'default',
                    full_location: 'Organizer Location',
                    type: 'Organizer',
                    is_unknown: false,
                    last_fetch_time: Date.now()
                };
            } else {
                userInfo = {
                    ptoken: pToken_to,
                    chat_name: 'Unknown Name' ,
                    avatar: 'default',
                    full_location: 'Unknown Location',
                    type: 'Unknown Type',
                    is_unknown: true,
                    last_fetch_time: Date.now()
                };
            }
        }

        return userInfo;
    }

    async function fetchUserInfoFromServer(formData, maxRetries = 3) {
        if (!formData.ptoken) return Promise.reject('Missing ptoken in formData');

        const user_info_key = 'user_info_list';
        const ops = formData.ops;
        const delay = 2000;

        return new Promise((resolve, reject) => {
            let retries = 0;

            function sendUserInfoRequest() {
                retries++;

                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',
                    headers: {"cfcache": 900},
                    data: formData,
                    success: function (response) {
                        try {
                            let srv_userInfoObj = typeof response === 'string' ? JSON.parse(response) : response;

                            if (srv_userInfoObj.success && srv_userInfoObj.user_data) {
                                let userInfoObj = srv_userInfoObj.user_data;
                                userInfoObj.last_fetch_time = Date.now();

                                userInfoList[ops] = userInfoList[ops] || {};
                                userInfoList[ops][userInfoObj.ptoken] = userInfoObj;

                                IntaoDB.getItem(objStores.common_store.name, user_info_key).then((intao_data) => {
                                    let updatedResponse = intao_data?.values || {};
                                    updatedResponse[ops] = updatedResponse[ops] || {};
                                    updatedResponse[ops][userInfoObj.ptoken] = userInfoObj;

                                    IntaoDB.setItem(objStores.common_store.name, { taoh_common: user_info_key, values: updatedResponse, timestamp: Date.now() });
                                });

                                resolve(userInfoObj);
                            } else {
                                reject('Invalid user info response from server!');
                            }
                        } catch (e) {
                            if (retries < maxRetries) {
                                setTimeout(sendUserInfoRequest, delay);
                            } else {
                                reject('Error parsing response from server after max retries!');
                            }
                        }
                    },
                    error: function () {
                        if (retries < maxRetries) {
                            setTimeout(sendUserInfoRequest, delay);
                        } else {
                            reject('Network error after max retries!');
                        }
                    }
                });
            }

            sendUserInfoRequest();
        });
    }

    function convertMentionsToLinks(message) {
        const mentionPattern = new RegExp('@(\\w+)', 'g');
        return message.replace(mentionPattern, (match, username) => {
            const mention_ptoken = mentionUserArray[username] || '';
            if(mention_ptoken !== '') {
                return `<span class="conversation-mention" data-ptoken="${mention_ptoken}">@${username}</span>`;
            } else {
                return match;
            }
        });
    }


    function runTimer(targetElement) {
        let countdown = 30;
        if(targetElement == "countDownTimer1") {
            countdown = 60;
        }
        var appendStr = "";
        var method_popup = 2;
        if(targetElement == "countDownTimer") {
            appendStr = " secs";
            method_popup = 1
        }
        const timerInterval = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                clearInterval(timerInterval);
                $("."+targetElement).text("0"+appendStr);
                onTimerEnd(method_popup);
            } else {
                $("."+targetElement).text(countdown+appendStr);
            }
        }, 1000);

        return timerInterval;
    }


    function onTimerEnd(method_popup) {
        intervalId1 = undefined;
        intervalId2 = undefined;
        if(method_popup == 1) {
            $('#connectModal').modal('hide');
            $('.countDownTimer').text('30 secs');
        } else {
            $('.countDownTimer1').text('60');
            $('.countDownDiv').addClass('d-none');
            $('.successMatchDiv').addClass('d-none');
            $('.notAvailableDiv').addClass('d-none');
            $('.connect_btn').prop('disabled', false).text('Connect');

            showNotAvailablePopup();

            //updateSpeedNetworkingCarousel();
        }
    }

    function hideMembersModal() {
        $('#membersModal').modal('hide');
    }

    function showNotAvailablePopup() {
        $('.notAvailableDiv').removeClass('d-none');
        $('.zeroday-speed').addClass('d-none');
    }

    function updateSpeedNetworkingCarousel() {

        console.log("updateSpeedNetworkingCarousel called1");
        $('.carousel-inner').each(function () {
            let seen = {};
            $(this).find('.carousel-item').each(function () {
                let id = $(this).attr('id');
                if (id) {
                    if (seen[id]) {
                        console.warn("Duplicate carousel-item removed:", id);
                        $(this).remove();
                    } else {
                        seen[id] = true;
                    }
                }
            });
        });
        console.log("updateSpeedNetworkingCarousel called2");

        $('li').each(function() {
            $(this).removeClass('active');
        });
        $('#speedChannelList li:first').addClass('active');

        $('.countDownDiv').addClass('d-none');
        $('.successMatchDiv').addClass('d-none');
        $('.notAvailableDiv').addClass('d-none');
        $('.rejectDiv').addClass('d-none');
        if ($("#speed_networking").is(":visible")) {
            $('.chat-input-bottom').removeClass('d-flex').addClass('d-none');
        }
        $('#browse_channels_wrapper').hide();
        $('#participants').hide();
        $('.watchPartySection').hide();
        $('.watchPartySection').removeClass('watchPartyEnabled');

        if($('.speed_networking_carousel .carousel-item').length == 0) {
            $('#contentCarousel').addClass('d-none');
            $('.zeroday-speed').removeClass('d-none');
            $('.speed_networking_div').addClass('d-none');
        } else {
            if($('.speed_networking_carousel .carousel-item').length == 1) {
                $('.carousel-control-prev').hide();
                $('.carousel-control-next').hide();
            } else {
                $('.carousel-control-prev').show();
                $('.carousel-control-next').show();
            }
            $('.speed_networking_div').removeClass('d-none');
            $('#contentCarousel').removeClass('d-none');
            $('.zeroday-speed').addClass('d-none');
            if(!$('.speed_networking_carousel .carousel-item.active').length) {
                $('.speed_networking_carousel .carousel-item:first').addClass('active');
            }
        }
    }

    const url = new URL(window.location.href);
    if (url.searchParams.has('chatwithchannelid') || url.searchParams.has('chatwithchanneltype')) {
        url.searchParams.delete('chatwithchannelid');
        url.searchParams.delete('chatwithchanneltype');
        window.history.pushState({}, '', url.toString());
    }
</script>
<?php
taoh_get_footer();