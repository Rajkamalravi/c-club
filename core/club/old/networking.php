<?php
taoh_get_header();

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
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

$user_info_obj = taoh_user_all_info();
$profile_complete = (isset($user_info_obj->profile_complete) && $user_info_obj->profile_complete) ? $user_info_obj->profile_complete : 0;
if (!$profile_complete) {
    taoh_set_error_message('complete your settings to fully use the platform');
    taoh_redirect(TAOH_SITE_URL_ROOT . '/settings');
    taoh_exit();
}

$ice_break = getRandomIceBreakQuestions();

$parse_url_1 = taoh_parse_url(1);

if ($parse_url_1 == 'room' || $parse_url_1 == 'forum') {
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
} else {
    $contslug = ''; // :rk temp fix
    $geo_enable = 1;
    if ($geo_enable) {
        $full_loc_expl = explode(', ', $user_info_obj->full_location);
        $country = array_pop($full_loc_expl);
        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH . $country);
    } else {
        $keytoken = hash('crc32', TAOH_SITE_ROOT_HASH);
    }
}


$ntw_view = 1;
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

$room_info = get_room_info($keytoken, $user_info_obj->ptoken);
$room_info_arr = json_decode($room_info, true);
if (json_last_error() !== JSON_ERROR_NONE) {
//    taoh_redirect(TAOH_SITE_URL_ROOT);

    showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1004, 'networking');
    taoh_exit();
}

if (!isset($room_info_arr['success']) || !$room_info_arr['success'] || !$room_info_arr['output']) {
    if ($parse_url_1 == 'room') {
        // :rk room not exist alert need to show here
//        taoh_redirect(TAOH_SITE_URL_ROOT);

        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1005, 'networking');
        taoh_exit();
    }

    require_once TAOH_CORE_PATH . '/' . $appname . '/includes/club_room_data.php';

    if ($parse_url_1 == 'forum') {
        if (empty($_GET['t'])) {
            // :rk title not exist alert need to show here
//            taoh_redirect(TAOH_SITE_URL_ROOT);
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
//        taoh_redirect(TAOH_SITE_URL_ROOT);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1008, 'networking');
        taoh_exit();
    }
}

$room_app = $room_info_arr['output']['app'] ?? '';
$club_info = $room_info_arr['output']['club'] ?? '';

if ($parse_url_1 !== 'room' && $parse_url_1 !== 'forum' && $parse_url_1 !== 'custom-room') {
    if (!empty($club_info) && isset($club_info['links']['club'])) {
        taoh_redirect(TAOH_SITE_URL_ROOT . $club_info['links']['club']);
        taoh_exit();
    } else {
        // :rk invalid club info alert need to show here
//        taoh_redirect(TAOH_SITE_URL_ROOT);
        showErrorPage(TAOH_CORE_PATH . '/' . $appname, 1010, 'networking');
        taoh_exit();
    }
}

$who_can_create_video_room = array();$club_token = '';
$event_owner_ptoken = $user_info_obj->ptoken;
if (isset($room_app) && $room_app === 'event') {
    $club_info_mod_get = $room_info_arr['output']['club']['links']['detail'] ?? '';
    $explode_get = explode('/', $club_info_mod_get);
    $club_info_mod_filter = array_filter($explode_get);
    $club_token = array_pop($club_info_mod_filter);
    $explode_token = explode('-', $club_token);
    $club_token = array_pop($explode_token);

    $cache_name = 'event_detail_' . $club_token;
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'ops' => 'baseinfo',
        'mod' => 'events',
        'eventtoken' => $club_token ?? '',
        'cache_name' => $cache_name,
        'cache_time' => 7200,
        'cache' => array("name" => $cache_name, "ttl" => 7200),
        //'cfcc1d'=> 1, ////cfcache newly added
    );
    $taoh_call = 'events.event.get';
    //$taoh_vals['cfcache'] = $cache_name;
    ksort($taoh_vals);
    $response_det = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
    $event_arr = $response_det['output'];

    $sponsor_array = $event_arr['conttoken']['event_sponsor'];
    $event_owner_ptoken = $event_arr['conttoken']['ptoken'];
    if(isset($sponsor_array) && count($sponsor_array) > 0){
        foreach($sponsor_array as $kkk=>$val){
            if(isset($val['ptoken']) && $val['ptoken'] !=''){
                
                array_push($who_can_create_video_room,$val['ptoken']);
            }
        }
    }
    array_push($who_can_create_video_room,$event_arr['conttoken']['ptoken']);


    $events_data = $event_arr['conttoken'] ?? [];
    $msg_from_owner = $events_data['msg_from_owner'];
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
}

array_push($who_can_create_video_room, $user_info_obj->ptoken);


$lat = '';
$long = '';
$radius = 2000;
$unit = 'km';
$ptoken = $user_info_obj->ptoken;
$coordinates = $user_info_obj->coordinates;
if (!empty($coordinates)) {
    $co_array = explode('::', $coordinates);
    $lat = $co_array[0];
    $long = $co_array[1];
}

$show_video_conv_btn = isset($room_app) && $room_app === 'event' && (($events_data['disable_video_conversation'] ?? '') != '1');
$allow_auto_manage = isset($room_app) && $room_app === 'event' && (($events_data['auto_manage'] ?? '') == '1');

include_once TAOH_CORE_PATH . '/' . $appname . '/includes/ads_data.php';
?>

    <style>
    
        .scroll-button {
            width: 30px;
            height: 30px;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d3d3d3;
            background: #2557A7;
            color: #fff;
        }
        .scroll-button:hover {
            color: #fff;
        }
        .participants-tabs li .nav-link {
            border: none;
        }
        .participants-tabs li .nav-link.active {
            border-bottom: 3px solid #406CB2;
        }
        .participants-tabs li a {
            font-size: 16px;
            white-space: nowrap;
        }
        .post-profile-img {
            width: 100%;
            height: 100%;
            min-width: 50px;
            min-height: 50px;
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border: 1px solid #d3d3d3;
            border-radius: 100%;
        }
        .start-discussion-submit {
            background: #406CB2;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
        }
        .start-discussion-submit:hover {
            background: #406CB2;
            color: #ffffff;
        }
        .select-container {
            position: relative;
            max-width: fit-content;
        }
        .select-container select{
            appearance: none;
            padding-right: 30px;
            background: #EEEEEE;
            border: none !important;
            outline: none;
            border-radius: 12px;
            margin-bottom: 0;
        }
        .select-container svg{
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .input-container {
            position: relative;
        }
        .input-container .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .btn.add-btn {
            background: #000000;
            color: #ffffff;
        }
        .btn.add-option-btn {
            background: #00A2FF;
            color: #ffffff;
        }

        .close.modal-close-btn , .poll-close-btn {
            min-width: 38px;
            max-width: 38px;
            min-height: 38px;
            max-height: 38px;
            border-radius: 100%;
            background: #3D3D3D;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .close.modal-close-btn svg, .poll-close-btn svg{
            min-width: fit-content;
        }

        label.error {
            color: red;
            font-size: 14px;
            font-weight: 400;
        }

        hr {
            margin-bottom: 1.25rem;
        }

        #frm_comment-error,
        #frm_reply_comment-error {
            display: none !important;
        }

        #frm_reply_comment {
            cursor: pointer;
        }

        .frm_reply_comment_btn {
            text-decoration: underline;
            cursor: pointer;
        }

        #networkingTab {
            border: 1px solid #dee2e6;
        }

        #networkingTab .nav-item:hover {
            background: #eeeeee;
        }

        #networkingTab .nav-link {
            width: 100%;
            margin-bottom: 0;
            font-weight: 400;
            background: transparent;
            border: none !important;
        }

        #networkingTab .nav-link.active {
            color: #ffffff;
            border: none;
            font-weight: 500;
        }

        #networkingTab .nav-item:has(> .nav-link.active) {
            color: #ffffff;
            background: #2557A7;
        }

        #networkingTab .nav-link:focus,
        #networkingTab .nav-link:hover {
            border: none !important;
        }

        #comment_form_blk,
        #reply_comment_form_blk {
            padding: 15px 15px 10px 10px;
            background-color: #fff;
            display: table-cell;
            vertical-align: top;
            border-radius: 0.1875rem;
            box-shadow: 0 1px 3px 0 rgb(195 187 187);
        }
                    
        @media (max-width: 425px) {
            #frm_comments_list::-webkit-scrollbar {
                display: none;
            }
            #frm_comments_list {
            scrollbar-width: none;
        }
        }

        #frm_comments_list {
            min-height: 200px;
            max-height: 500px;
            overflow: auto;
            width: 100%;
            scrollbar-width: thin;
        }

        #comment_form_blk textarea,
        #reply_comment_form_blk textarea {
            resize: none;
            outline: none;
            border: none;
            display: block;
            margin: 0;
            width: 100%;
            padding: 10px;
            -webkit-font-smoothing: antialiased;
            font-family: "PT Sans", "Helvetica Neue", "Helvetica", "Roboto", "Arial", sans-serif;
            font-size: 1rem;
            color: #555f77;
            background-color: #fff !important;
        }

        .post-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            margin-top: 20px;
        }

        .post-card .user-info {
            display: flex;
            /*align-items: center;*/
        }

        .post-card .user-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .post-card .user-info .author {
            line-height: 1.3rem;
        }

        .post-card .user-info .user-name {
            font-weight: bold;
            font-size: 16px;
        }

        .post-card .post-content {
            margin-top: 10px;
        }

        .post-card .post-content p {
            margin: 0;
        }

        .post-card .post-icons {
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .post-card .post-icons .icon {
            display: flex;
            align-items: center;
        }

        .post-card .post-icons .icon i {
            margin-right: 5px;
            color: #ff5e5e;
        }

        .post-card .post-icons .action-icons i {
            margin-right: 20px;
            font-size: 20px;
            cursor: pointer;
        }

        .post-card-header-loader .fa {
            font-size: 25px;
            color: #007bff;
            margin-right: 10px;
        }

        .note-wrap {
            margin-bottom: 1.25rem;
            display: table;
            width: 100%;
            min-height: 5.3125rem;
        }

        .note-wrap .note-actions {
            float: right;
        }

        .newNote {
            background-color: #fff !important;
        }

        .photo {
            background-color: transparent !important;
            padding-top: 0.4rem;
            display: table-cell;
            width: 3.2rem;
        }

        .photo .avatar {
            height: 2.25rem;
            width: 2.25rem;
            border-radius: 50%;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .bottom-notes {
            color: #acb4c2;
            font-size: 0.875rem;
            border-top: 1px solid #d0d0d0;
        }


        #start-discussion .modal-header .close {
            padding: 0;
            margin: 0;
        }

        #start-discussion .modal-dialog {
            width: 600px;
            max-width: 600px;
        }

        #video_room_join_confirmation .modal-dialog {
            max-width: 810px;
        }


        .modal.left.message .modal-dialog {
            width: 600px;
            max-width: 600px;
        }

        @media (max-width: 575.98px) {
            .modal.left.message .modal-dialog {
                width: 90%;
                max-width: 90%;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .modal.left.message .modal-dialog {
                width: 80%;
                max-width: 80%;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .modal.left.message .modal-dialog {
                width: 70%;
                max-width: 70%;
            }
        }

        @media (min-width: 992px) and (max-width: 1199.98px) {
            .modal.left.message .modal-dialog {
                width: 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 1200px) {
            .modal.left.message .modal-dialog {
                width: 600px;
                max-width: 600px;
            }
        }

        .video_room_join_guidelines{
            list-style: inherit !important;
            margin-left: 2em;
        }
    </style>


    <style>
        .page-body {
            background-color: #fff !important;
        }

        .card-item .card-body {
            padding: 1.25rem !important;
        }

        #comments {
            min-height: 150px;
            max-height: 500px;
            overflow: auto;
            width: 100%;
        }

        .comment-body {
            margin-left: 10px;
            padding: 0 15px !important;
        }

        #commentInput:hover {
            background-color: #fff;
        }

        #sendChat {
            cursor: pointer;
            color: #007bff;
        }

        #message-area {
            box-shadow: 0 0 8px #52555a33 !important;
        }

        .gobackwindow {
            /*display: none;*/
            font-weight: bold;
            color: #2479d8;
            cursor: pointer
        }

        .entries-title {
            display: flex;
            justify-content: space-between;
        }

        .user-type {
            background: navy;
            margin-left: 15px;
            font-size: 55%;
            color: #fff;
            vertical-align: middle;
        }

        /* Left Network Entries css */
        .left_network_entries .entriesList .comment-avatar {
            width: 2.5em !important;
            height: 2.5em !important;
        }

        .left_network_entries .entriesList .badge, .left_network_entries .entriesList .live_status,
        .left_network_entries .entriesList .location, .left_network_entries .entriesList .skill {
            display: none !important;
        }

        .left_network_entries .entriesList .left_entries {
            padding-left: 40px !important;
        }

        .left_network_entries .entriesList .network_entries .card-body {
            padding: 5px !important;
        }

        .left_network_entries .entriesList .network_entries .active-status,
        .left_network_entries .entriesList .network_entries .active-status-border {
            right: 0 !important;
            top: 35px !important;
            left: 2em !important;
        }

        .left_network_entries .entriesList .chat-box-mobile {
            margin-top: 0 !important;
        }

        .left_network_entries .entriesList .openchatacc {
            float: right !important;
            font-size: 12px !important;
            margin-right: 10px;
            margin-bottom: 10px;
            /*min-width: 100px !important;*/
            text-align: center !important;
        }

        .open_window.left_network_entries h5 a {
            font-size: 20px;
        }

        .open_window.left_network_entries .chat-box-mobile {
            padding-left: 40px !important;
            margin-inline-start: initial !important;
            margin-right: auto !important;
            text-align: left !important;
            left: 70px;
        }

        .left_network_entries .entriesList .chat_name {
            font-size: 1rem !important;
            width: 100% !important;
        }

        #entriesList {
            max-height: 500px;
            overflow: hidden auto;
            display: block;
        }

        #activeRoomList {
            max-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 2px;
        }

        .invites-item .comment-avatar {
            content: attr(data-letters);
            display: inline-block;
            font-size: 1em;
            width: 2.5em;
            height: 2.5em;
            line-height: 2.5em;
            text-align: center;
            border-radius: 100%;
            vertical-align: middle;
            color: white;
        }

        #windowchatavatar .comment-avatar {
            width: 50px;
            height: 50px;
        }

        #current_chat_with_info .active-status, #current_chat_with_info .active-status-border {
            top: 47px;
            width: 10px;
            height: 10px;
        }

        #comments li.mine .comment-body {
            background: #e6f9ff;
        }

        .comment-body:before,
        .comment-body:after {
            position: absolute;
            top: 10px;
            display: inline-block;
            width: 12px;
            height: 12px;
        }

        #comments li.others .comment-body:before {
            content: '';
            left: -7px;
            border-top: 1px solid #d5d5d5;
            border-right: 1px solid #d5d5d5;
            background: #fff;
            transform: rotate(-135deg);
        }

        #comments li.mine .comment-body:after {
            content: '';
            right: -7px;
            border-top: 1px solid #d5d5d5;
            border-right: 1px solid #d5d5d5;
            background: #e6f9ff;
            transform: rotate(45deg);
        }

        .d-none {
            display: none;
        }

        .show-more, .show-less {
            cursor: pointer;
        }

        #newmessages_btn_grp {
            position: absolute;
            bottom: 10px;
            margin-left: auto;
            margin-right: auto;
            left: 50%;
            text-align: center;
            transform: translate(-50%, -50%);
            overflow: hidden;
            z-index: 999;
        }

        #stickyBadge {
            left: 49.3% !important;
        }

        #frm_stickyBadge {
            left: 49.3% !important;
        }

        header .mobile-app:has(#clubTab) {
            margin-bottom: 8px !important;
        }

        /* poll */
        .ans-poll-btn {
            background: #406CB2;
            color: #ffffff;
            border-radius: 12px;
            max-height: 23px;
            display: flex;
            align-items: center;
            font-size: 12px;
            font-weight: 400;
            gap: 12px;
        }
        .ans-poll-btn:hover {
            color: #ffffff;
        }

        .poll-counts {
            max-height: 23px;
            font-size: 12px;
            font-weight: 400;
            color: #444444;
        }


        .poll .question{
            padding: 20px;
            color: #111;
            font-size: 1.5em;
        }

        .poll .answers{
            padding: 20px;
        }

        .poll .answers .answer{
            color: #000000;
            position: relative;
            width: 100%;
            height: 40px;
            line-height: 40px;
            padding: 0 10px;
            border: 1px solid #d4d4d4;
            cursor: pointer;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .poll .answers .answer.selected{
            border: 2px solid #8f9fe8;
        }

        .poll .answers .answer span.percentage_value
        {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: 40px;
            color: #111;
            font-size: 15px;
        }

        .poll .answers .answer span.percentage_bar{
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: #00FFB23B;
            z-index: 1;
            transition: width 200ms ease-in-out;
        }
        .file-upload-icon {
            cursor: pointer;
            color: #6c757d; 
            display: none;
        }

        /* Optional: You can also change the icon style on hover */
        .file-upload-icon:hover {
            color: #007bff; /* Change color on hover */
        }
        .inside-icon {
            position: relative;
        }
        .inside-icon .file-upload-icon {
            position: absolute;
            top: 3px;
            right: 30px;
        }
       /* File preview container */
        .file-preview {
            margin-top: 15px;
            font-size: 14px;
            color: #555;
            display: flex;
            flex-wrap: wrap;
        }

        /* File preview item */
        .file-preview-item {
            position: relative;
            margin-bottom: 10px;
        }

        /* Close button styles */
        .file-preview-item .close-btn {
            position: absolute;
            top: -10px;
            right: 10px;
            font-size: 14px;
            color: #ff0000;
            cursor: pointer;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 0, 0, 0.1);
        }

        .file-preview-item .close-btn:hover {
            background: rgba(255, 0, 0, 0.2);
        }

        /* Image preview container */
        .file-preview .image-preview {
            margin-right: 10px;
        }

        .file-preview img {
            max-width: 100px;
            height: auto;
            margin-right: 10px;
            border: 1px solid #d3d3d3;
            border-radius: 6px;
            padding: 5px;
        }

        /* File name preview */
        .file-preview .file-name-preview {
            font-weight: 500;
            color: #5E5E5E;
            overflow: hidden;
            width: 100%;
            max-width: 500px;


            display: -webkit-box;
            -webkit-line-clamp: 1; /* Limits the text to 3 lines */
            -webkit-box-orient: vertical;
            text-overflow: ellipsis;
        }

        /* Sponsor */
        .sponsor-card {
            /*border: 1px solid #d3d3d3;*/
            width: 100%;
            max-width: 414px;
            box-shadow: 0 0 8px #52555a33 !important;
            border-radius: 6px;
        }
        .sponsor-card .sponsor-card-title {
            font-size: 17px;
            line-height: 1.2;
            font-weight: 500;
            color: #2A4E96;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sponsor-card .sponsor-btn {
            /*font-size: 10px;*/
            /*line-height: 33px;*/
            font-weight: 500;
            color: #000000;
            border-radius: 6px;
        }
        .carousel-control-next, .carousel-control-prev {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .carousel-control-next {
            right: 10px;
        }
        .carousel-control-prev {
            left: 10px;
        }
        .bb {
            border-bottom: 1px solid #d3d3d3;
        }
        .sponsor-content-carousel .sponsor-img {
            width: 100%;
            height: 100%;
            min-width: 62px;
            max-width: 62px;
            min-height: 62px;
            max-height: 62px;
            border: 1px solid #d3d3d3;
            border-radius: 6px;
            /* display: flex;
            align-items: center;
            justify-content: center; */
            object-fit: contain;
        }
        .sponsor-content-carousel .sponsor-logo-img {
            width: 100%;
            height: 100%;
            max-width: 76px;
            max-height: 66px;
            min-height: 66px;
            border: 1px solid #d3d3d3;
            border-radius: 6px;
            object-fit: contain;
            padding: 3px;
        }
        .sponsor-content-carousel .full-image-container {
            width: 100%;
            height: 120px;
            position: relative;
        }
        .sponsor-content-carousel .full-image {
            width: 100%;
            height: 100%;
            border-radius: 6px;
            object-fit: contain;
            position: relative;
            z-index: 1;
        }
        .sponsor-content-carousel .sponsor-name {
            font-size: 19px;
            font-weight: 500;
            color: #2A4E96;
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .sponsor-content-carousel .event-org-desc {
            font-size: 15px;
            font-weight: 400;
            color: #000000;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.5;
            max-height: 3em;
            width: 100%;
        }
        .sponsor-content-carousel .sponsor-link {
            font-size: 15px;
            font-weight: 300;
            color: #2A4E96;
            text-decoration: underline;
        }

        @media(min-width: 1024px) {
            .ticket-card {
                padding: 30px 45px;
            }
        }

        .sub-menu {
            display: block;
            visibility: hidden;
            position: absolute;
            /*top: 100%;   align to bottom of*/
            left: initial !important;
            right: 0 !important;
            box-shadow: none;
            max-height: 0;
            width: 150px;
            overflow: hidden;
            background-color: whitesmoke;
            /* white-space: nowrap; */
        }
        .save-btn, .share-btn {
            border: 1px solid #d3d3d3;
            min-width: 44px;
        }
        .save-btn.saved svg {
            fill: #6C727C;
        }

        @media (max-width: 354px) {
            .sub-menu.mobile-left {
                left: 0 !important;
                right: unset !important;
            }
        }
        .cover-event-image {
            position: relative;
            overflow: hidden;
        }
        .cover-event-image .events-bg, .sponsor-content-carousel .full-image-container .events-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            border-radius: 12px;
            z-index: 0;
        }
        .sponsor-content-carousel .full-image-container .events-bg {
            border-radius: 6px;
        }

        .cover-event-image .glass-overlay, .sponsor-content-carousel .full-image-container .glass-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1); /* semi-transparent white */
            backdrop-filter: blur(10px); /* frosted glass effect */
            z-index: 1;
            border-radius: 12px;
        }
        .sponsor-content-carousel .full-image-container .glass-overlay {
            border-radius: 6px;
        }
        /* /Sponsor */

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
                                <ul class="nav nav-tabs justify-content-left border-0 mt-4 mb-4" role="tablist">
                                    <?php
                                    $last_crumb = array_pop($club_info['breadcrumbs']);

                                    if (str_contains($last_crumb['link'], 'networking')) {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_NETWORKPAGE_NAME);
                                    } else {
                                        define('TAOH_NETWORK_REFERRAL_URL', TAOH_SITE_URL_ROOT . $last_crumb['link']);
                                    }
                                    $descc = $club_info['description'];

                                    foreach ($club_info['breadcrumbs'] as $value) {
                                        //echo "==========".$value['link'] ;

                                            $link = TAOH_SITE_URL_ROOT . $value['link'];
                                            if($value['link'] == '#')
                                                $link = 'javascript:void(0);';
                                            echo '<li class="nav-item">
                                                <a href="' . $link . '">' . $value['title'] . '</a>
                                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></li>';
                                            
                                    
                                        }

                                    
                                    echo '<li class="nav-item"><a href="' . TAOH_SITE_URL_ROOT . $last_crumb['link'] . '">' . $last_crumb['title'] . '</a>';
                                    if (strlen($descc) > 0) echo '<i class="fa fa-info-circle ml-2 cursor-pointer" data-toggle="collapse" data-target="#demo" title="View more info" aria-expanded="false" aria-controls="org_msg" style="color: #15a4f7; font-size: 20px;"></i>';
                                    echo '</li>';

                                    if (!empty($msg_from_owner) || !empty($club_info['msg_from_owner'])) {
                                        echo '<li class="nav-item ml-2"><span>| Note from organizer</span>';
                                        echo '<i class="fa fa-info-circle ml-2 cursor-pointer" data-toggle="collapse" data-target="#org_msg" title="View more info" aria-expanded="false" aria-controls="org_msg" style="color: #15a4f7; font-size: 20px;"></i>';
                                        echo '</li>';
                                    }

                                    if (isset($_GET['from'])) {
                                        echo '<li class="nav-item"><span><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/></svg></span>
                                        <a href="' . TAOH_SITE_URL_ROOT . '/club/room/' . $contslug . '">Networking</a></li>';
                                    }
                                    ?>
                                </ul>
                                <?php
                                if ($show_video_conv_btn) {
                                    echo '<div class="col-md-2">';
                                    echo '<button class="btn btn-sm theme-btn w-100" id="video_room_btn" data-toggle="modal" data-target="#video_room_join_confirmation"><i class="fa fa-video-camera mr-1" aria-hidden="true"></i> Enter Video Room</button>';
                                    echo '</div>';
                                }
                                ?>
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

        <div>
            <input type="hidden" id="lastchatwith" name="lastchatwith">

            <section class="question-area pt-20px pb-20px">
                <div class="container">
                    <div class="tab-content px-0" id="myTabContent">
                        <div class="tab-pane fade show active" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
                            <div class="row mt-4 previous-chat">
                                <div class="col-lg-3">
                                    <div class="sidebar">

                                        <div> <!--<div class="listwindow"> -->
                                            <div class="card card-item " style="max-height: 500px; overflow-y: auto; overflow-x: hidden;">
                                                <div class="card-body">
                                                    <h3 class="fs-17"
                                                        title="Please indicate your purpose for attending (e.g., Interested in job opportunities, Looking to learn about product management) or tell us a bit about yourself (e.g., Veteran seeking connections in technology).">
                                                        What's On Your Mind?
                                                    </h3>

                                                    <div class="divider mb-4"><span></span></div>
                                                    <p style="position: relative" class="d-flex update-status">
                                                        <span id="loadEmoji"><img onclick="openStatusModal();" src="<?php echo TAOH_SITE_URL_ROOT . '/assets/images/emojis/default.svg';?>" alt="Emoji" id="loadEmojiImg" class="update-image"></span>
                                                        <input type="text" maxlength="140" value="" name="my_status" id="my_status" placeholder="Say something">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if($ntw_view != 2) { ?>

                                            <!-- Invites Received -->
                                            <div class="previous_chat card card-item">
                                                <div class="card-body">
                                                    <h3 class="fs-17">Invites Received <span id="activeListloaderArea"></span></h3>
                                                    <div class="divider mb-2"><span></span></div>
                                                    <div id="activeRoomList">
                                                        <span id="no_previous_chat">No Invites Received!</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- All Participants -->
                                            <div class="open_window left_network_entries card card-item" style="display: none;">
                                                <div class="card-body">
                                                    <h3 class="fs-17 ">All Participants <span id="activeListloaderArea"></span></h3>
                                                    <div class="gobackwindow" style="font-size:12px;">click here to see more people</div>
                                                    <div class="divider mb-2"><span></span></div>
                                                    <div id="entriesList" class="entriesList">

                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>


                                    </div>
                                </div>

                                <div class="col-lg-6 p-0">
                                    <div class="container">
                                        <ul class="nav nav-tabs nav-justified" id="networkingTab" role="tablist">
                                            <?php if($enable_convo == 1){ ?>
                                                <li class="nav-item d-flex justify-content-center" role="presentation">
                                                    <button class="nav-link" id="conversation-tab" data-toggle="tab" data-target="#conversation" type="button" role="tab" aria-controls="conversation" aria-selected="true">Discussion</button>
                                                </li>
                                            <?php } ?>
                                            <li class="nav-item d-flex justify-content-center" role="presentation">
                                                <button class="nav-link active" id="connections-tab" data-toggle="tab" data-target="#connections" type="button" role="tab" aria-controls="connections" aria-selected="false">Participants</button>
                                            </li>
                                            <?php if(ENABLE_CUSTOM_ROOM){ ?>
                                                <li class="nav-item d-flex justify-content-center" role="presentation">
                                                    <button class="nav-link" id="rooms-tab" data-toggle="tab" data-target="#customrooms" type="button" role="tab"
                                                     aria-controls="conversation" aria-selected="true">Rooms</button>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div>
                                        <div class="tab-content" id="networkingTabContent">
                                            <?php if($enable_convo == 1){ ?>
                                                <div class="tab-pane fade" id="conversation" role="tabpanel" aria-labelledby="conversation-tab">
                                                <div class="container">
                                                    <div class="row">
                                                        <div class="col-md-12 mt-4">
                                                            <div class="container d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3" style="border-bottom: 1px solid #D3D3D3;">
                                                                <p style="font-size: 16px; color: #000000;">Start a Discussion ! Post What's on your mind</p>
                                                                <button type="button" class="btn add-btn" data-toggle="modal" data-target="#start-discussion">
                                                                    <svg class="mr-2" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M7.5 15C9.48912 15 11.3968 14.2098 12.8033 12.8033C14.2098 11.3968 15 9.48912 15 7.5C15 5.51088 14.2098 3.60322 12.8033 2.1967C11.3968 0.790176 9.48912 0 7.5 0C5.51088 0 3.60322 0.790176 2.1967 2.1967C0.790176 3.60322 0 5.51088 0 7.5C0 9.48912 0.790176 11.3968 2.1967 12.8033C3.60322 14.2098 5.51088 15 7.5 15ZM6.79688 10.0781V8.20312H4.92188C4.53223 8.20312 4.21875 7.88965 4.21875 7.5C4.21875 7.11035 4.53223 6.79688 4.92188 6.79688H6.79688V4.92188C6.79688 4.53223 7.11035 4.21875 7.5 4.21875C7.88965 4.21875 8.20312 4.53223 8.20312 4.92188V6.79688H10.0781C10.4678 6.79688 10.7812 7.11035 10.7812 7.5C10.7812 7.88965 10.4678 8.20312 10.0781 8.20312H8.20312V10.0781C8.20312 10.4678 7.88965 10.7812 7.5 10.7812C7.11035 10.7812 6.79688 10.4678 6.79688 10.0781Z" fill="white"/>
                                                                    </svg>
                                                                    <span>Add New Topic</span>
                                                                </button>
                                                            </div>

                                                            <div class="taoh-loader taoh-spinner show" id="frm_chat_loader"></div>
                                                            <!-- new script -->
                                                            <!--<form action="">
                                                                <div class="form-group d-flex flex-column flex-sm-row mb-4" style="gap: 12px;">
                                                                    <div class="flex-grow-1 input-container" style="">
                                                                        <input type="text" class="form-control mb-0" style="padding-left: 30px; height: 39px;">
                                                                        <span class="la la-search search-icon"></span>
                                                                    </div>
                                                                   
                                                                    <button type="button" class="btn theme-btn py-1">Search <i class="la la-search ml-1"></i></button>
                                                                    
                                                                </div>
                                                            </form>-->
                                                            <!-- /new script -->

                                                            <div id="frm_stickyBadge" class="badge sticky-badge" style="display: none;"></div>

                                                            <div class="mt-4" id="frm_comments_list">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Forum Replies -->
                                                <div class="modal left message fade show" id="reply" tabindex="-1" role="dialog" aria-labelledby="forumReplyModalLabel" aria-modal="true">
                                                    <div class="modal-dialog profile" role="document">
                                                        <div class="modal-content px-md-2">
                                                            <div class="modal-header align-items-center" style="background: #ffffff; border-bottom: 1px solid #D3D3D3;">
                                                                <h4 style="font-size: clamp(16px, 3vw + 16px, 19px); font-weight: 600;">Leave a reply</h4>
                                                                <button type="button" class="btn reply_close">
                                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M19.4172 3.41719C20.1984 2.63594 20.1984 1.36719 19.4172 0.585938C18.6359 -0.195312 17.3672 -0.195312 16.5859 0.585938L10.0047 7.17344L3.41719 0.592187C2.63594 -0.189063 1.36719 -0.189063 0.585938 0.592187C-0.195312 1.37344 -0.195312 2.64219 0.585938 3.42344L7.17344 10.0047L0.592188 16.5922C-0.189062 17.3734 -0.189062 18.6422 0.592188 19.4234C1.37344 20.2047 2.64219 20.2047 3.42344 19.4234L10.0047 12.8359L16.5922 19.4172C17.3734 20.1984 18.6422 20.1984 19.4234 19.4172C20.2047 18.6359 20.2047 17.3672 19.4234 16.5859L12.8359 10.0047L19.4172 3.41719Z" fill="#D3D3D3"/>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="col-md-12 mt-4">
                                                                    <div class="taoh-loader taoh-spinner show" id="frm_reply_chat_loader"></div>
                                                                    <div id="frm_reply_stickyBadge" class="badge sticky-badge" style="display: none;"></div>

                                                                    <div class="mt-4" id="frm_reply_comments_list">

                                                                    </div>

                                                                    <div class="note-wrap mt-4">
                                                                        <div class="note-block newNote" id="reply_comment_form_blk">
                                                                            <form method="post" action="" id="reply_comment_form">
                                                                                <input type="hidden" name="reply_comment_id" id="reply_comment_id">
                                                                                <div class="inside-icon">
                                                                                    <textarea name="frm_reply_comment" id="frm_reply_comment" cols="30" rows="3" placeholder="Reply here..." required></textarea>
                                                                                    <!-- File upload attachment icon -->
                                                                                    <label for="fileInput_2" class="file-upload-icon">
                                                                                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                                                    </label>

                                                                                    <input type="file" id="fileInput_2" name="file" style="display: none;" multiple>

                                                                                    <!-- Preview area for selected files -->
                                                                                    <div class="file-preview" id="filePreview_2">
                                                                                        <!-- Image or file name previews will be added here dynamically -->
                                                                                    </div>
                                                                                </div>
                                                                                <div class="bottom-notes">
                                                                                    <ul class="note-actions mt-2">
                                                                                        <li class="reply_save"><button type="submit" id="frm_reply_comment_send_btn" class="btn btn-sm theme-btn-primary">Reply</button></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <?php } ?>

                                            <div class="tab-pane fade <?php echo 'show active'; // ($enable_convo == 0)?'show active':''; ?>" id="connections" role="tabpanel" aria-labelledby="connections-tab">
                                                <div class="container">
                                                    <div id="accordion1" class="generic-accordion" style="display:none;">
                                                        <div class="card">
                                                            <div class="card-header" id="collapseinvite" style="background-color: #add8e636 ;">
                                                                <button class="btn btn-link fs-15" data-toggle="collapse" data-target="#collapseOnes" aria-expanded="false" aria-controls="collapseOnes">
                                                                    <span class="pr-2 fs-17"> Networking Requests Received</span>
                                                                    <i class="la la-angle-down collapse-icon"></i>
                                                                </button>
                                                            </div>
                                                            <div class="divider ml-4 mr-3"><span></span></div>
                                                            <div id="collapseOnes" class="collapse show" aria-labelledby="collapseinvite" data-parent="#accordion1" style="background-color: #add8e636;overflow-y: auto;overflow-x: hidden;font-size:15px">
                                                                <div class="card-body">  <!-- add8e64d -->
                                                                    <div id="activeChatList" style="font-size:12px;"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php if ($ntw_view != 2 && !empty($club_info['live']) && is_array($club_info['live']) && count($club_info['live']) >= 3) { ?>

                                                        <h2 class="live-title fs-24 mb-1"><?= $club_info['live']['title']; ?>
                                                            <span class="live" data-toggle="collapse" data-target="#live" aria-expanded="true">
                                                                <span class="accicon"><i class="fas fa-angle-down rotate-icon"></i></span>
                                                            </span>
                                                        </h2>

                                                        <div id="live" class="collapse show card card-item">
                                                            <div class="card-body">
                                                                <iframe id="video-link" height="200" width="500" src="<?= $club_info['live']['live_link']; ?>" frameborder="0" allowfullscreen loading="lazy"></iframe>
                                                            </div>
                                                        </div>

                                                    <?php }	?>
                                                    <!-- Search Bar -->

                                                    <!-- Embeded Video -->
                                                    <?php if(isset($club_info['live_cast_link']) && $club_info['live_cast_link']!='') { ?>
                                                        <div class="card mm-card mb-4">
                                                            <div class="card-body">
                                                                <?php
                                                                $video_url =  $club_info['live_cast_link'];
                                                                ?>
                                                                <div style="text-align: center; margin: 0 auto;">
                                                                    <iframe style="height: 300px;width:100%" src="<?php echo $video_url; ?>" frameborder="0" allowfullscreen></iframe>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>

                                                    <!-- new script -->
                                                    <div class="align-items-center" style="gap: 12px;" hidden> <!--d-flex -->
                                                        <button class="d-none d-md-flex btn scroll-button" id="scroll-left"><i class="la la-angle-left"></i></button>

                                                        <ul class="my-3 pb-2 nav nav-tabs participants-tabs scroll-container d-flex justify-content-center justify-content-md-start flex-md-nowrap" id="myTab" role="tablist" style="overflow-x: auto; border-bottom: 2px solid #d3d3d3;  scroll-behavior: smooth;">
                                                            <li class="nav-item">
                                                                <a class="nav-link active" id="event-directory-tab" data-toggle="tab" href="#eventDirectory" role="tab" aria-controls="eventDirectory" aria-selected="true">Event Directory</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="employer-directory-tab" data-toggle="tab" href="#employerDirectory" role="tab" aria-controls="employerDirectory" aria-selected="false">Employer Directory</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="professional-directory-tab" data-toggle="tab" href="#professionalDirectory" role="tab" aria-controls="professionalDirectory" aria-selected="false">Professional Directory</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="your-people-tab" data-toggle="tab" href="#yourPeople" role="tab" aria-controls="yourPeople" aria-selected="false">Your People</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link" id="jobs-from-this-event" data-toggle="tab" href="#jobsFromThisEvent" role="tab" aria-controls="jobsFromThisEvent" aria-selected="false">Jobs from this event</a>
                                                            </li>
                                                        </ul>
                                                        <button class="d-none d-md-flex btn scroll-button" id="scroll-right"><i class="la la-angle-right"></i></button>
                                                    </div>
                                                    <!-- /new script -->

                                                    <div class="container p-0 open_window" style="display: none;">
                                                        <div class="row" id="ow_header" style="display: none;">
                                                            <div class="col">
                                                                <?php if($ntw_view != 2) echo '<span class="gobackwindow"><i class="las la-chevron-circle-left"></i> Go Back</span>'; ?>
                                                            </div>
                                                            <div class="col">
                                                                <span style="float: right; font-size: xx-large;">
                                                                    <i class="la la-video ntw_video" onclick="taoh_add_video_chat();" style="color: #2479d8;"></i>
                                                                </span>
                                                                <!---<div class="btn-group" style="float: right; font-size: xx-large;">

                                                                    <i style="font-size:26px;color: #2479d8;" class="la la-video ntw_video  dropdown-toggle" 
                                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="sr-only">Toggle Dropdown</span></i>
                                                                    
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <button class="dropdown-item" type="button " onclick="taoh_add_video_chat(1);" >
                                                                            Tao Meet
                                                                        </button>
                                                                        <button class="dropdown-item" type="button" onclick="taoh_add_video_chat(2);" >
                                                                            Google Meet</button>
                                                                        
                                                                    </div>
                                                                </div>-->
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col">
                                                                <div class="card card-item chatBlock mt-2 mb-3">
                                                                    <div class="card-body" style="min-width: 100%">
                                                                        <div id="chatArea">
                                                                            <div>
                                                                                <div id="windowchatavatar"></div>
                                                                                <span class="pb-3 fs-15" style="float:right;display:none;" id="message_count"><span id="chatCount">0</span> Message(s)</span>
                                                                                <h3 class="fs-17 pb-3" id="current_chat_with_info"></h3>
                                                                            </div>

                                                                            <div class="taoh-loader taoh-spinner show" id="ntw_chat_loader"></div>
                                                                            <div>
                                                                                <div id="stickyBadge" class="badge sticky-badge" style="display: none;"></div>

                                                                                <ul class="comments-list pt-3" id="comments">

                                                                                </ul>

                                                                                <div id="newmessages_btn_grp" class="btn-group rounded-pill" role="group" aria-label="New Messages" style="display: none;">
                                                                                    <button type="button" id="newmessages_btn" class="btn btn-sm theme-btn" title="Click to scroll down"><i class="fa fa-arrow-down mr-2"></i><span>new messages</span></button>
                                                                                    <button type="button" id="newmessages_close_btn" class="btn btn-sm theme-btn"><i class="fa fa-times"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div><!-- end card-body -->
                                                                </div>
                                                                <div class="chat-bottom  bg-light" id="reply">
                                                                    <div style="display:none" id="sending_msg">Sending....
                                                                        <img id="loaderEmail" width="20" src="<?php echo TAOH_LOADER_GIF; ?>"/>
                                                                    </div>
                                                                    <div class="row chat-area" id="replyQoute">
                                                                        <div class="col-12 sender_part" style="display:none;">
                                                                            <textarea id="commentInput" placeholder="Introduce yourself and get the discussion started" class="form-control" name="message"></textarea>
                                                                            <button id="sendChat" class="btn theme-btn" type="submit" title="Send"><i class="fa fa-paper-plane-o"></i></button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="message-area" id="message-area" style="display:none;">
                                                                        <div class="col-12 text-center p-4 bg-white">
                                                                            <div class="fs-15 fw-bold text-center pb-2">Since the user is offline, you can't chat now but instead send a message.</div>
                                                                            <button id="networking_offline_send_message_btn" class="btn theme-btn" type="button">Send Message</button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="p-2" id="message_helper" style="display:none">
                                                                        <div class="fs-15 fw-bold text-center pb-2">You can click any one to start your conversation</div>
                                                                        <ul style="list-style-type:disc" class="fs-14 lh-22 text-black-50">
                                                                            <li onclick="copyToMessage('What brought you to this event?');">What brought you to this event?</li>
                                                                            <li onclick="copyToMessage('What do you do?');">What do you do</li>
                                                                            <li onclick="copyToMessage('What\'s your favorite thing to do outside of work?');">What's your favorite thing to do outside of work?</li>
                                                                            <li onclick="copyToMessage('What\'s your ideal career?');">What's your ideal career?</li>
                                                                            <li onclick="copyToMessage('What is keeping you up at night?');">What is keeping you up at night?</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="align-items-center" style="gap: 12px;" hidden> <!--d-flex -->
                                                            <button class="d-none d-md-flex btn scroll-button" id="scroll-left"><i class="la la-angle-left"></i></button>

                                                            <ul class="my-3 pb-2 nav nav-tabs participants-tabs scroll-container d-flex justify-content-center justify-content-md-start flex-md-nowrap" id="myTab" role="tablist" style="overflow-x: auto; border-bottom: 2px solid #d3d3d3;  scroll-behavior: smooth;">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" id="event-directory-tab" data-toggle="tab" href="#eventDirectory" role="tab" aria-controls="eventDirectory" aria-selected="true">Event Directory</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="employer-directory-tab" data-toggle="tab" href="#employerDirectory" role="tab" aria-controls="employerDirectory" aria-selected="false">Employer Directory</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="professional-directory-tab" data-toggle="tab" href="#professionalDirectory" role="tab" aria-controls="professionalDirectory" aria-selected="false">Professional Directory</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="your-people-tab" data-toggle="tab" href="#yourPeople" role="tab" aria-controls="yourPeople" aria-selected="false">Your People</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="jobs-from-this-event" data-toggle="tab" href="#jobsFromThisEvent" role="tab" aria-controls="jobsFromThisEvent" aria-selected="false">Jobs from this event</a>
                                                                </li>
                                                            </ul>
                                                            <button class="d-none d-md-flex btn scroll-button" id="scroll-right"><i class="la la-angle-right"></i></button>
                                                        </div>

                                                        <div class="row" id="ow_footer" style="display: none;">
                                                            <div class="col">
                                                                <?php if($ntw_view != 2) echo '<span class="gobackwindow"><i class="las la-chevron-circle-left"></i> Go Back</span>'; ?>
                                                            </div>
                                                            <div class="col">
                                                            
                                                                <span style="float: right; font-size: xx-large;">
                                                                    <i class="la la-video ntw_video" onclick="taoh_add_video_chat();" style="color: #2479d8;"></i>
                                                                </span>
                                                                <!--<div class="btn-group" style="float: right; font-size: xx-large;">

                                                                    <i style="font-size:26px;color: #2479d8;" class="la la-video ntw_video  dropdown-toggle" 
                                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="sr-only">Toggle Dropdown</span></i>
                                                                    
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <button class="dropdown-item" type="button " onclick="taoh_add_video_chat(1);" >
                                                                            Tao Meet
                                                                        </button>
                                                                        <button class="dropdown-item" type="button" onclick="taoh_add_video_chat(2);" >
                                                                            Google Meet</button>
                                                                        
                                                                    </div>
                                                                </div>-->
                                                            </div>
                                                        </div>
                                                    </div>

                                                   
                                                    

                                                    <div class="question-tabs mt-4 mb-50px listwindow">
                                                        <form id="searchFilter" onsubmit="searchFilter();return false" class="search-form p-0 rounded-0 bg-transparent shadow-none position-relative z-index-1">
                                                            <div class="d-flex flex-wrap align-items-center">
                                                                <div class="d-flex flex-wrap align-items-center flex-grow-1">
                                                                    <div class="form-group mr-3 flex-grow-1">
                                                                        <input name='query' class="form-control form--control pl-40px" type="text" id="query" placeholder="Search for (skill, role or organization)">
                                                                        <span class="la la-search input-icon"></span>
                                                                    </div>

                                                                </div><!-- end d-flex -->
                                                                <div class="mb-3 mr-2 search-btn-box">
                                                                    <button class="btn theme-btn" id="search" type="submit">Search<i class="la la-search ml-1"></i></button>
                                                                </div><!-- end search-btn-box -->
                                                                <?php
                                                                if($club_info['geo_enable']){
                                                                    ?>
                                                                    <div class="mb-3 zooming-section">
                                                                        <button type="button" onclick="zoomin()" class="p-2 zoom-buttons" title="Search people in larger radius"><i class="la la-plus"></i></button>
                                                                        <input type="hidden" name="radius" id="radius" value="<?php echo $radius;?>" />
                                                                        <?php
                                                                        //<?php echo "<span>".$unit."</span>";
                                                                        ?>
                                                                        <button type="button" onclick="zoomout()" class="p-2 zoom-buttons" title="Search people in a smaller radius"><i class="la la-minus"></i></button>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </form>

                                                        <div class="tab-content pt-0 px-0" id="myTabContent">
                                                            <div class="tab-pane fade show active" id="thisnetwork" role="tabpanel" aria-labelledby="thisnetwork-tab">
                                                                <div id='loaderArea-thisnetwork-tab' class="text-center"></div>
                                                                <div id="networkArea-thisnetwork-tab"></div>
                                                                <!-- new network_entries card start-->
                                                                
                                                                <!-- <div class="relative-card card card-item d-flex flex-column flex-xl-row py-5 py-xl-2 px-3 mb-4" style="gap: 12px;">
                                                                    <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 12px; flex: 1; max-width: 480px;">
                                                                        <div class="left-box d-flex flex-md-column align-items-center pt-md-3 pb-2" style="gap: 6px;">
                                                                            <a href="#">
                                                                                <img class="lazy n-participants-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="avatar">
                                                                            </a>
                                                                            <div class="d-flex flex-column align-items-md-center">
                                                                                <p class="pro-badge px-2 py-1">
                                                                                    Professional
                                                                                </p>
                                                                               
                                                                                <div class="icons">
                                                                                    <a href="#">
                                                                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M7.23054 15C9.1482 15 10.9873 14.2098 12.3433 12.8033C13.6993 11.3968 14.4611 9.48912 14.4611 7.5C14.4611 5.51088 13.6993 3.60322 12.3433 2.1967C10.9873 0.790176 9.1482 0 7.23054 0C5.31288 0 3.47377 0.790176 2.11778 2.1967C0.761787 3.60322 0 5.51088 0 7.5C0 9.48912 0.761787 11.3968 2.11778 12.8033C3.47377 14.2098 5.31288 15 7.23054 15ZM6.10077 9.84375H6.77863V7.96875H6.10077C5.72512 7.96875 5.4229 7.65527 5.4229 7.26562C5.4229 6.87598 5.72512 6.5625 6.10077 6.5625H7.45649C7.83214 6.5625 8.13436 6.87598 8.13436 7.26562V9.84375H8.36031C8.73596 9.84375 9.03817 10.1572 9.03817 10.5469C9.03817 10.9365 8.73596 11.25 8.36031 11.25H6.10077C5.72512 11.25 5.4229 10.9365 5.4229 10.5469C5.4229 10.1572 5.72512 9.84375 6.10077 9.84375ZM7.23054 3.75C7.47025 3.75 7.70014 3.84877 7.86963 4.02459C8.03913 4.2004 8.13436 4.43886 8.13436 4.6875C8.13436 4.93614 8.03913 5.1746 7.86963 5.35041C7.70014 5.52623 7.47025 5.625 7.23054 5.625C6.99083 5.625 6.76094 5.52623 6.59144 5.35041C6.42195 5.1746 6.32672 4.93614 6.32672 4.6875C6.32672 4.43886 6.42195 4.2004 6.59144 4.02459C6.76094 3.84877 6.99083 3.75 7.23054 3.75Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                    <a href="#">
                                                                                        <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M2.7 3.63043C2.7 2.66758 3.07928 1.74417 3.75442 1.06333C4.42955 0.382491 5.34522 0 6.3 0C7.25478 0 8.17045 0.382491 8.84558 1.06333C9.52072 1.74417 9.9 2.66758 9.9 3.63043C9.9 4.59329 9.52072 5.5167 8.84558 6.19754C8.17045 6.87838 7.25478 7.26087 6.3 7.26087C5.34522 7.26087 4.42955 6.87838 3.75442 6.19754C3.07928 5.5167 2.7 4.59329 2.7 3.63043ZM0 13.6794C0 10.8856 2.24437 8.62228 5.01469 8.62228H7.58531C10.3556 8.62228 12.6 10.8856 12.6 13.6794C12.6 14.1445 12.2259 14.5217 11.7647 14.5217H0.835312C0.374063 14.5217 0 14.1445 0 13.6794ZM14.175 8.84918V7.03397H12.375C12.0009 7.03397 11.7 6.73049 11.7 6.35326C11.7 5.97604 12.0009 5.67255 12.375 5.67255H14.175V3.85734C14.175 3.48011 14.4759 3.17663 14.85 3.17663C15.2241 3.17663 15.525 3.48011 15.525 3.85734V5.67255H17.325C17.6991 5.67255 18 5.97604 18 6.35326C18 6.73049 17.6991 7.03397 17.325 7.03397H15.525V8.84918C15.525 9.22641 15.2241 9.52989 14.85 9.52989C14.4759 9.52989 14.175 9.22641 14.175 8.84918Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="center-box">
                                                                            <p class="n-participants-name mb-2">Mabel</p>
                                                                            <div class="n-participants-text d-flex align-items-center mb-2" style="gap: 6px;">
                                                                                <div class="svg-circle">
                                                                                    <svg width="7" height="10" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                                                                    </svg>
                                                                                </div>
                                                                                <span>Virigina Beach, Virginia US</span>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 6px;">
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="7" height="8" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>New Incentives</span>
                                                                                </div>
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Admin Manager</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 3px;">
                                                                                <div class="n-participants-text  d-flex align-items-center mr-2" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Skills</span>
                                                                                </div>
                                                                                
                                                                                <a href="#" class="btn skill-b">Budgeting</a>
                                                                                <a href="#" class="btn skill-b">Maintenance</a>
                                                                                <a href="#" class="btn skill-b">Book Keeping</a>
                                                                              
                                                                            </div>
                                                                            <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 7.34784 18.9464 4.8043 17.0711 2.92893C15.1957 1.05357 12.6522 0 10 0C7.34784 0 4.8043 1.05357 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C4.8043 18.9464 7.34784 20 10 20ZM6.41016 12.7148C7.10938 13.5234 8.30469 14.375 10 14.375C11.6953 14.375 12.8906 13.5234 13.5898 12.7148C13.8164 12.4531 14.2109 12.4258 14.4727 12.6523C14.7344 12.8789 14.7617 13.2734 14.5352 13.5352C13.6641 14.5352 12.1523 15.625 10 15.625C7.84766 15.625 6.33594 14.5352 5.46484 13.5352C5.23828 13.2734 5.26562 12.8789 5.52734 12.6523C5.78906 12.4258 6.18359 12.4531 6.41016 12.7148ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM13.1406 6.875C13.4721 6.875 13.7901 7.0067 14.0245 7.24112C14.2589 7.47554 14.3906 7.79348 14.3906 8.125C14.3906 8.45652 14.2589 8.77446 14.0245 9.00888C13.7901 9.2433 13.4721 9.375 13.1406 9.375C12.8091 9.375 12.4912 9.2433 12.2567 9.00888C12.0223 8.77446 11.8906 8.45652 11.8906 8.125C11.8906 7.79348 12.0223 7.47554 12.2567 7.24112C12.4912 7.0067 12.8091 6.875 13.1406 6.875Z" fill="url(#paint0_linear_5596_697)"/>
                                                                                    <defs>
                                                                                    <linearGradient id="paint0_linear_5596_697" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                                                    <stop stop-color="#FFD700"/>
                                                                                    <stop offset="1" stop-color="#F85556"/>
                                                                                    </linearGradient>
                                                                                    </defs>
                                                                                </svg>
                                                                                <span>Available to connect</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-box d-flex align-items-center" style="min-width: 125px;">
                                                                        <a href="#" class="btn main-chat-btn btn-success">Chat Now</a>
                                                                    </div>
                                                                   
                                                                    <img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/silver.svg" alt="avatar" style="margin-top: -10px;">
                                                                    
                                                                    <p class="hiring">
                                                                        Hiring
                                                                    </p>
                                                                </div>
                                                                <div class="relative-card card card-item d-flex flex-column flex-xl-row py-5 py-xl-2 px-3 mb-4" style="gap: 12px;">
                                                                    <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 12px; flex: 1; max-width: 480px;">
                                                                        <div class="left-box d-flex flex-md-column align-items-center pt-md-3 pb-2" style="gap: 6px;">
                                                                            <a href="#">
                                                                                <img class="lazy n-participants-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="avatar">
                                                                            </a>
                                                                            <div class="d-flex flex-column align-items-md-center">
                                                                                <p class="pro-badge px-2 py-1">
                                                                                    Professional
                                                                                </p>
                                                                               
                                                                                <div class="icons">
                                                                                    <a href="#">
                                                                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M7.23054 15C9.1482 15 10.9873 14.2098 12.3433 12.8033C13.6993 11.3968 14.4611 9.48912 14.4611 7.5C14.4611 5.51088 13.6993 3.60322 12.3433 2.1967C10.9873 0.790176 9.1482 0 7.23054 0C5.31288 0 3.47377 0.790176 2.11778 2.1967C0.761787 3.60322 0 5.51088 0 7.5C0 9.48912 0.761787 11.3968 2.11778 12.8033C3.47377 14.2098 5.31288 15 7.23054 15ZM6.10077 9.84375H6.77863V7.96875H6.10077C5.72512 7.96875 5.4229 7.65527 5.4229 7.26562C5.4229 6.87598 5.72512 6.5625 6.10077 6.5625H7.45649C7.83214 6.5625 8.13436 6.87598 8.13436 7.26562V9.84375H8.36031C8.73596 9.84375 9.03817 10.1572 9.03817 10.5469C9.03817 10.9365 8.73596 11.25 8.36031 11.25H6.10077C5.72512 11.25 5.4229 10.9365 5.4229 10.5469C5.4229 10.1572 5.72512 9.84375 6.10077 9.84375ZM7.23054 3.75C7.47025 3.75 7.70014 3.84877 7.86963 4.02459C8.03913 4.2004 8.13436 4.43886 8.13436 4.6875C8.13436 4.93614 8.03913 5.1746 7.86963 5.35041C7.70014 5.52623 7.47025 5.625 7.23054 5.625C6.99083 5.625 6.76094 5.52623 6.59144 5.35041C6.42195 5.1746 6.32672 4.93614 6.32672 4.6875C6.32672 4.43886 6.42195 4.2004 6.59144 4.02459C6.76094 3.84877 6.99083 3.75 7.23054 3.75Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                    <a href="#">
                                                                                        <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M2.7 3.63043C2.7 2.66758 3.07928 1.74417 3.75442 1.06333C4.42955 0.382491 5.34522 0 6.3 0C7.25478 0 8.17045 0.382491 8.84558 1.06333C9.52072 1.74417 9.9 2.66758 9.9 3.63043C9.9 4.59329 9.52072 5.5167 8.84558 6.19754C8.17045 6.87838 7.25478 7.26087 6.3 7.26087C5.34522 7.26087 4.42955 6.87838 3.75442 6.19754C3.07928 5.5167 2.7 4.59329 2.7 3.63043ZM0 13.6794C0 10.8856 2.24437 8.62228 5.01469 8.62228H7.58531C10.3556 8.62228 12.6 10.8856 12.6 13.6794C12.6 14.1445 12.2259 14.5217 11.7647 14.5217H0.835312C0.374063 14.5217 0 14.1445 0 13.6794ZM14.175 8.84918V7.03397H12.375C12.0009 7.03397 11.7 6.73049 11.7 6.35326C11.7 5.97604 12.0009 5.67255 12.375 5.67255H14.175V3.85734C14.175 3.48011 14.4759 3.17663 14.85 3.17663C15.2241 3.17663 15.525 3.48011 15.525 3.85734V5.67255H17.325C17.6991 5.67255 18 5.97604 18 6.35326C18 6.73049 17.6991 7.03397 17.325 7.03397H15.525V8.84918C15.525 9.22641 15.2241 9.52989 14.85 9.52989C14.4759 9.52989 14.175 9.22641 14.175 8.84918Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="center-box">
                                                                            <p class="n-participants-name mb-2">Mabel</p>
                                                                            <div class="n-participants-text d-flex align-items-center mb-2" style="gap: 6px;">
                                                                                <div class="svg-circle">
                                                                                    <svg width="7" height="10" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                                                                    </svg>
                                                                                </div>
                                                                                <span>Virigina Beach, Virginia US</span>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 6px;">
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="7" height="8" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>New Incentives</span>
                                                                                </div>
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Admin Manager</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 3px;">
                                                                                <div class="n-participants-text  d-flex align-items-center mr-2" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Skills</span>
                                                                                </div>
                                                                                
                                                                                <a href="#" class="btn skill-b">Budgeting</a>
                                                                                <a href="#" class="btn skill-b">Maintenance</a>
                                                                                <a href="#" class="btn skill-b">Book Keeping</a>
                                                                              
                                                                            </div>
                                                                            <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 7.34784 18.9464 4.8043 17.0711 2.92893C15.1957 1.05357 12.6522 0 10 0C7.34784 0 4.8043 1.05357 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C4.8043 18.9464 7.34784 20 10 20ZM6.41016 12.7148C7.10938 13.5234 8.30469 14.375 10 14.375C11.6953 14.375 12.8906 13.5234 13.5898 12.7148C13.8164 12.4531 14.2109 12.4258 14.4727 12.6523C14.7344 12.8789 14.7617 13.2734 14.5352 13.5352C13.6641 14.5352 12.1523 15.625 10 15.625C7.84766 15.625 6.33594 14.5352 5.46484 13.5352C5.23828 13.2734 5.26562 12.8789 5.52734 12.6523C5.78906 12.4258 6.18359 12.4531 6.41016 12.7148ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM13.1406 6.875C13.4721 6.875 13.7901 7.0067 14.0245 7.24112C14.2589 7.47554 14.3906 7.79348 14.3906 8.125C14.3906 8.45652 14.2589 8.77446 14.0245 9.00888C13.7901 9.2433 13.4721 9.375 13.1406 9.375C12.8091 9.375 12.4912 9.2433 12.2567 9.00888C12.0223 8.77446 11.8906 8.45652 11.8906 8.125C11.8906 7.79348 12.0223 7.47554 12.2567 7.24112C12.4912 7.0067 12.8091 6.875 13.1406 6.875Z" fill="url(#paint0_linear_5596_697)"/>
                                                                                    <defs>
                                                                                    <linearGradient id="paint0_linear_5596_697" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                                                    <stop stop-color="#FFD700"/>
                                                                                    <stop offset="1" stop-color="#F85556"/>
                                                                                    </linearGradient>
                                                                                    </defs>
                                                                                </svg>
                                                                                <span>Available to connect</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-box d-flex align-items-center" style="min-width: 125px;">
                                                                        <a href="#" class="btn main-chat-btn btn-success">Chat Now</a>
                                                                    </div>
                                                                   
                                                                    <img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/gold.svg" alt="avatar" style="margin-top: -10px;">
                                                                    
                                                                    <p class="hiring">
                                                                        Hiring
                                                                    </p>
                                                                </div>
                                                                <div class="relative-card card card-item d-flex flex-column flex-xl-row py-5 py-xl-2 px-3 mb-4" style="gap: 12px;">
                                                                    <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 12px; flex: 1; max-width: 480px;">
                                                                        <div class="left-box d-flex flex-md-column align-items-center pt-md-3 pb-2" style="gap: 6px;">
                                                                            <a href="#">
                                                                                <img class="lazy n-participants-img" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/nprofileplaceholder.png" alt="avatar">
                                                                            </a>
                                                                            <div class="d-flex flex-column align-items-md-center">
                                                                                <p class="pro-badge px-2 py-1">
                                                                                    Professional
                                                                                </p>
                                                                               
                                                                                <div class="icons">
                                                                                    <a href="#">
                                                                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M7.23054 15C9.1482 15 10.9873 14.2098 12.3433 12.8033C13.6993 11.3968 14.4611 9.48912 14.4611 7.5C14.4611 5.51088 13.6993 3.60322 12.3433 2.1967C10.9873 0.790176 9.1482 0 7.23054 0C5.31288 0 3.47377 0.790176 2.11778 2.1967C0.761787 3.60322 0 5.51088 0 7.5C0 9.48912 0.761787 11.3968 2.11778 12.8033C3.47377 14.2098 5.31288 15 7.23054 15ZM6.10077 9.84375H6.77863V7.96875H6.10077C5.72512 7.96875 5.4229 7.65527 5.4229 7.26562C5.4229 6.87598 5.72512 6.5625 6.10077 6.5625H7.45649C7.83214 6.5625 8.13436 6.87598 8.13436 7.26562V9.84375H8.36031C8.73596 9.84375 9.03817 10.1572 9.03817 10.5469C9.03817 10.9365 8.73596 11.25 8.36031 11.25H6.10077C5.72512 11.25 5.4229 10.9365 5.4229 10.5469C5.4229 10.1572 5.72512 9.84375 6.10077 9.84375ZM7.23054 3.75C7.47025 3.75 7.70014 3.84877 7.86963 4.02459C8.03913 4.2004 8.13436 4.43886 8.13436 4.6875C8.13436 4.93614 8.03913 5.1746 7.86963 5.35041C7.70014 5.52623 7.47025 5.625 7.23054 5.625C6.99083 5.625 6.76094 5.52623 6.59144 5.35041C6.42195 5.1746 6.32672 4.93614 6.32672 4.6875C6.32672 4.43886 6.42195 4.2004 6.59144 4.02459C6.76094 3.84877 6.99083 3.75 7.23054 3.75Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                    <a href="#">
                                                                                        <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M2.7 3.63043C2.7 2.66758 3.07928 1.74417 3.75442 1.06333C4.42955 0.382491 5.34522 0 6.3 0C7.25478 0 8.17045 0.382491 8.84558 1.06333C9.52072 1.74417 9.9 2.66758 9.9 3.63043C9.9 4.59329 9.52072 5.5167 8.84558 6.19754C8.17045 6.87838 7.25478 7.26087 6.3 7.26087C5.34522 7.26087 4.42955 6.87838 3.75442 6.19754C3.07928 5.5167 2.7 4.59329 2.7 3.63043ZM0 13.6794C0 10.8856 2.24437 8.62228 5.01469 8.62228H7.58531C10.3556 8.62228 12.6 10.8856 12.6 13.6794C12.6 14.1445 12.2259 14.5217 11.7647 14.5217H0.835312C0.374063 14.5217 0 14.1445 0 13.6794ZM14.175 8.84918V7.03397H12.375C12.0009 7.03397 11.7 6.73049 11.7 6.35326C11.7 5.97604 12.0009 5.67255 12.375 5.67255H14.175V3.85734C14.175 3.48011 14.4759 3.17663 14.85 3.17663C15.2241 3.17663 15.525 3.48011 15.525 3.85734V5.67255H17.325C17.6991 5.67255 18 5.97604 18 6.35326C18 6.73049 17.6991 7.03397 17.325 7.03397H15.525V8.84918C15.525 9.22641 15.2241 9.52989 14.85 9.52989C14.4759 9.52989 14.175 9.22641 14.175 8.84918Z" fill="#686767"/>
                                                                                        </svg>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="center-box">
                                                                            <p class="n-participants-name mb-2">Mabel</p>
                                                                            <div class="n-participants-text d-flex align-items-center mb-2" style="gap: 6px;">
                                                                                <div class="svg-circle">
                                                                                    <svg width="7" height="10" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="white"/>
                                                                                    </svg>
                                                                                </div>
                                                                                <span>Virigina Beach, Virginia US</span>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 6px;">
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="7" height="8" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>New Incentives</span>
                                                                                </div>
                                                                                <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M0 0V10H10V0H0ZM7.06473 7.25893L5 9.23884L2.93527 7.25893L4.375 3.15179L2.93527 1.21875H7.0625L5.625 3.15179L7.06473 7.25893Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Admin Manager</span>
                                                                                </div>
                                                                            </div>
                                                                            <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 3px;">
                                                                                <div class="n-participants-text  d-flex align-items-center mr-2" style="gap: 6px;">
                                                                                    <div class="svg-circle">
                                                                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="white"/>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <span>Skills</span>
                                                                                </div>
                                                                                
                                                                                <a href="#" class="btn skill-b">Budgeting</a>
                                                                                <a href="#" class="btn skill-b">Maintenance</a>
                                                                                <a href="#" class="btn skill-b">Book Keeping</a>
                                                                              
                                                                            </div>
                                                                            <div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path d="M10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 7.34784 18.9464 4.8043 17.0711 2.92893C15.1957 1.05357 12.6522 0 10 0C7.34784 0 4.8043 1.05357 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C4.8043 18.9464 7.34784 20 10 20ZM6.41016 12.7148C7.10938 13.5234 8.30469 14.375 10 14.375C11.6953 14.375 12.8906 13.5234 13.5898 12.7148C13.8164 12.4531 14.2109 12.4258 14.4727 12.6523C14.7344 12.8789 14.7617 13.2734 14.5352 13.5352C13.6641 14.5352 12.1523 15.625 10 15.625C7.84766 15.625 6.33594 14.5352 5.46484 13.5352C5.23828 13.2734 5.26562 12.8789 5.52734 12.6523C5.78906 12.4258 6.18359 12.4531 6.41016 12.7148ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM13.1406 6.875C13.4721 6.875 13.7901 7.0067 14.0245 7.24112C14.2589 7.47554 14.3906 7.79348 14.3906 8.125C14.3906 8.45652 14.2589 8.77446 14.0245 9.00888C13.7901 9.2433 13.4721 9.375 13.1406 9.375C12.8091 9.375 12.4912 9.2433 12.2567 9.00888C12.0223 8.77446 11.8906 8.45652 11.8906 8.125C11.8906 7.79348 12.0223 7.47554 12.2567 7.24112C12.4912 7.0067 12.8091 6.875 13.1406 6.875Z" fill="url(#paint0_linear_5596_697)"/>
                                                                                    <defs>
                                                                                    <linearGradient id="paint0_linear_5596_697" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                                                    <stop stop-color="#FFD700"/>
                                                                                    <stop offset="1" stop-color="#F85556"/>
                                                                                    </linearGradient>
                                                                                    </defs>
                                                                                </svg>
                                                                                <span>Available to connect</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-box d-flex align-items-center" style="min-width: 125px;">
                                                                        <a href="#" class="btn main-chat-btn btn-success">Chat Now</a>
                                                                    </div>
                                                                   
                                                                    <img class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/bronze.svg" alt="avatar" style="margin-top: -10px;">
                                                                    
                                                                    <p class="hiring">
                                                                        Hiring
                                                                    </p>
                                                                </div> -->
                                                                 <!-- new network_entries card end-->
                                                                <div id="pagination"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Networking Page Offline Message Modal -->
                                                <div class="modal fade" id="networkingOfflineMessageModal" tabindex="-1" role="dialog" aria-labelledby="networkingOfflineMessageModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div style="background:none;border:none" class="modal-content">
                                                            <div class="modal-body p-0">
                                                                <div class="card card-item">
                                                                    <div class="card-body">
                                                                        <div id="networkingOfflineMessageBlock">
                                                                            <h3 class="fs-22 fw-bold">Type your message</h3>
                                                                            <div class="row fs-15 mt-4 mb-4">
                                                                                <div class="col-10">
                                                                                    <div class=" inside-icon">
                                                                                        <textarea name="networkingOfflineMessage" id="networkingOfflineMessage" rows="5" maxlength="500" placeholder="Say something" required></textarea>
                                                                                        <!-- File upload attachment icon -->
                                                                                        <label for="fileInput_3" class="file-upload-icon">
                                                                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                                                        </label>

                                                                                        <input type="file" id="fileInput_3" name="file" style="display: none;" multiple>

                                                                                        <!-- Preview area for selected files -->
                                                                                        <div class="file-preview" id="filePreview_2">
                                                                                            <!-- Image or file name previews will be added here dynamically -->
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <input type="hidden" id="networkingOfflineLocationPath" value="">
                                                                                <input type="hidden" id="networkingOfflineToPtoken" value="">
                                                                            </div>
                                                                            <button type="button" class="btn btn-primary fw-medium" id="networking_message_send_button">Send</button>
                                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                        <div id="networkingOfflineSuccessMessage" class="alert text-success mt-3" style="display: none;">
                                                                            Your message has been sent successfully!
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                            <div class="tab-pane fade" id="customrooms" role="tabpanel" aria-labelledby="rooms-tab">
                                                <div class="container">
                                                    <div class=" mt-2 mb-50px">
                                                        <div class="d-flex align-items-center justify-content-between my-2">
                                                            <?php if(isset($who_can_create_video_room) && in_array($ptoken,$who_can_create_video_room)) { ?>
                                                            <div class="d-flex flex-column flex-sm-row mr-2 mr-md-5" style="flex: 1;">
                                                                <input name='custom_room_name' required class="form-control form--control" type="text" id="custom_room_name" placeholder="Name Your Room">
                                                                            
                                                                <button type="button " onclick="create_new_room();" id="create_new_room"                                                                 
                                                                class="btn btn-primary btn-sm create_new_room mb-2 py-2" style="width:200px;white-space: nowrap;font-size: 14px;border: 1px solid #c3c3c3;">Create a Video Room</button>
                                                            </div>
                                                            <?php } ?>

                                                            <div class="d-flex justify-content-end" onclick="loadCustomRooms();">
                                                                <i class="la la-refresh" style="font-size:24px;color: #2479d8;"></i>
                                                            </div>
                                                        </div>

                                                        <div style="clear:both;" class="tab-content pt-0 mt-0 px-0" id="myTabContent">
                                                            <div class="tab-pane fade show active" id="thisnetwork" role="tabpanel" aria-labelledby="thisnetwork-tab">
                                                                <div id="loaderArea-thisnetwork-tab" class="text-center"></div>
                                                                <div id="custom_room_list">
                                                                
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-3">
                                    <div class="rightsidebar sidebar">
                                        <div class="open_window" style="display: none;">
                                            <div id="owner_profile">
                                                <div class="card card-item">
                                                    <div class="card-body">
                                                        <h3 class="fs-17">Profile</h3>
                                                        <div class="divider"><span></span></div>
                                                        <div class="no-gutters" id="profile_info">

                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <?php if (function_exists('jobs_networking_widget')) { jobs_networking_widget();  } ?>
                                  
                                        <button type="button" class="btn unmanaged-event-btn" data-toggle="modal" data-target="#agreeModal">
                                            <div class="circle-highlight">
                                                <div class="main-circle"></div>
                                                <div class="center-circle"></div>
                                            </div>
                                            <div class="text-left">
                                                <p class="md-text"> What to expect?</p>
                                                <p class="sm-text">Click for Instructions</p>
                                            </div>
                                        </button>

                                        <div class="listwindow">
                                            <div class="card card-item">
                                                <div class="card-body">
                                                    <h3 class="fs-17">How Networking App Works?</h3> <!-- Job Fair video -->
                                                    <div class="divider"><span></span></div>
                                                    <?php if (function_exists('taoh_video_widget')) { taoh_video_widget('https://youtu.be/L87udpeMKa0');  } ?>
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
                                            if (0 && isset($club_info['sponsors']) && count($club_info['sponsors']) > 0) {
                                                ?>
                                                <div class="card card-item">
                                                    <div class="card-body p-3">
                                                        <h3 class="fs-17">Sponsor Details</h3>
                                                        <div class="divider"><span></span></div>
                                                        <?php
                                                        foreach($club_info['sponsors'] as $key=>$val){
                                                            ?>
                                                            <div class="sidebar-questions pt-3 sidebar">
                                                                <div class="media media-card p-3 d-block text-center">
                                                                    <a target="_blank" href="<?php echo $val['link'];?>" class="media-img d-inline-block flex-shrink-0">
                                                                        <img src="<?php echo $val['image'];?>" alt="company logo">
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <a target="_blank" href="<?php echo $val['link'];?>" >
                                                                            <h5 class="fs-16 fw-medium"><?php echo $val['title'];?></h5></a>
                                                                        <small class="meta">
                                                                            <?php echo $val['sub_title'];?><br>

                                                                        </small>
                                                                    </div><!-- end media-body -->
                                                                </div>

                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!-- SPONSOR SECTION ENDS -->

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

                                        <div class="listwindow">
                                            <div class="card card-item p-1" >
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

                                        <!-- invite a friend  -->
                                        <?php if (function_exists('taoh_invite_friends_widget')) { taoh_invite_friends_widget($club_info['title'],'networking',TAOH_NETWORK_REFERRAL_URL);  } ?>
                                        <div class="listwindow">
                                            <!-- ADS SECTION STARTS -->
                                            <?php if (function_exists('taoh_get_recent_jobs')) { taoh_get_recent_jobs('new');  } ?>
                                            <?php
                                            /* if (isset($reads_ads) && count($reads_ads) > 0) {
                                                ?>
                                                <div class="card card-item">
                                                    <div class="card-body p-3">
                                                        <h3 class="fs-17">Career and Job Resources</h3>
                                                        <div class="divider"><span></span></div>
                                                        <?php
                                                        foreach($reads_ads as $key=>$val){
                                                            ?>
                                                            <div class="sidebar-questions pt-3 sidebar">
                                                                <div class="media media-card p-3 d-block text-center">
                                                                    <a target="_blank" href="<?php echo $val['link'];?>" class="media-img d-inline-block flex-shrink-0">
                                                                        <?php echo $val["image"];?>
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <a target="_blank" href="<?php echo $val['link'];?>" >
                                                                            <h5 class="fs-16 fw-medium"><?php echo $val['title'];?></h5></a>
                                                                        <small class="meta">
                                                                            <?php echo $val['sub_title'];?><br>

                                                                        </small>
                                                                    </div><!-- end media-body -->
                                                                </div>

                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            } */

                                            if (0 && isset($ads) && count($ads) > 0) {
                                                shuffle($ads);
                                                $val = $ads[0];
                                                ?>
                                                <div class="card card-item">
                                                    <div class="card-body p-3">
                                                        <div class="sidebar-questions pt-3">
                                                            <div class="media media-card">
                                                                <div class="media-body">
                                                                    <h3 class="fs-17 text-color-8">
                                                                        <?php echo $val['title'];?>
                                                                    </h3>
                                                                    <div class="divider"><span></span></div>
                                                                    <center>
                                                                        <a href="<?php echo TAOH_SITE_URL_ROOT.$val['link'];?>">
                                                                            <img class="pt-3 pb-3" src="<?php echo $val['image'];?>" width="256"></a>
                                                                    </center>
                                                                    <p style="font-size:15px;"><?php echo $val['sub_title'];?></p>
                                                                    <br>
                                                                    <span>
														<a href="<?php echo $val['link'];?>" class="add_to_chat1 btn btn-danger btn-sm">
															<?php echo $val['link_title'];?>
														</a>
													</span>

                                                                </div>
                                                            </div><!-- end media -->
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <!-- ADS SECTION ENDS -->

                                        </div>

                                        <?php //if (function_exists('taoh_stats_widget')) { taoh_stats_widget();  } ?>

                                    </div><!-- end col-lg-4 -->
                                </div>

                            </div><!-- end tab-pane -->
                        </div><!-- end tab-content -->
                    </div>
                </div>
            </section>
        </div>


        <!-- Agree modal -->
        <div class="modal fade unmanaged-event" id="agreeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog bg-white" role="document">
                <div class="modal-content" style="border: 1px solid #ccc; border-radius: 8px;">
                <div class="modal-header flex-column pb-4 mb-3" style="border: none; position: relative;">
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
                    <!-- Section 1: Talk with Others -->
                    <div class="agree-card d-flex mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 10px;">
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
                        <p>Chat with colleagues, ask smart questions, and share your ideas.</p>
                    </div>
                    </div>

                    <!-- Section 2: Join Video Chats -->
                    <div class="agree-card d-flex mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 10px;">
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
                        <p>Step into small video rooms to talk face-to-face about key topics.</p>
                    </div>
                    </div>

                    <!-- Section 3: Meet People -->
                    <div class="agree-card d-flex mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 10px;">
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
                        <p>Find new colleagues and connect with people who share your interests.</p>
                    </div>
                    </div>

                    <!-- Section 4: Share & Ask -->
                    <div class="agree-card d-flex mb-3" style="border: 1px solid #eee; border-radius: 4px; padding: 10px;">
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
                        <p>Offer your expertise and ask for advice to grow your network.</p>
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
                                        <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
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
                                            <div class="fs-11 text-dark emoji-inner-section">
                                                <?php for ($i = 1; $i <= 100; $i++) { ?>
                                                    <button type="button" onclick="chooseEmoji(<?php echo $i; ?>);" class="emoji-icon">
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
                                            <button type="button" class="btn btn-outline-secondary fw-medium" data-dismiss="modal">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- new script -->
        <div class="modal fade" id="start-discussion" tabindex="-1" role="dialog" aria-labelledby="discussionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form method="post" action="" id="comment_form">
                        <div class="modal-header">
                            <h5 class="modal-title" id="discussionModalTitle">Create a post</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <textarea name="frm_comment" id="frm_comment" cols="30" rows="3" placeholder="Start a new discussion..." required></textarea>


                            <!-- Poll -->
                            <div id="poll-options" class="card mt-3 d-none">
                                <div class="card-body">
                                    <div class="d-flex justify-content-end mb-3 pb-3" style="border-bottom: 2px solid #d3d3d3;">
                                        <button type="button" class="btn m-0 poll-close-btn" style="">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M15.5265 2.73247C16.1512 2.10776 16.1512 1.09324 15.5265 0.46853C14.9018 -0.156177 13.8872 -0.156177 13.2625 0.46853L8 5.73606L2.73247 0.473528C2.10776 -0.151179 1.09324 -0.151179 0.46853 0.473528C-0.156177 1.09824 -0.156177 2.11276 0.46853 2.73747L5.73606 8L0.473528 13.2675C-0.151179 13.8922 -0.151179 14.9068 0.473528 15.5315C1.09824 16.1562 2.11276 16.1562 2.73747 15.5315L8 10.2639L13.2675 15.5265C13.8922 16.1512 14.9068 16.1512 15.5315 15.5265C16.1562 14.9018 16.1562 13.8872 15.5315 13.2625L10.2639 8L15.5265 2.73247Z" fill="#D3D3D3"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap: 12px;">
                                        <p style="color: #444444;">Create Poll Options !</p>
                                        <button type="button" class="btn add-option-btn">
                                            <svg class="mr-2" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M7.5 15C9.48912 15 11.3968 14.2098 12.8033 12.8033C14.2098 11.3968 15 9.48912 15 7.5C15 5.51088 14.2098 3.60322 12.8033 2.1967C11.3968 0.790176 9.48912 0 7.5 0C5.51088 0 3.60322 0.790176 2.1967 2.1967C0.790176 3.60322 0 5.51088 0 7.5C0 9.48912 0.790176 11.3968 2.1967 12.8033C3.60322 14.2098 5.51088 15 7.5 15ZM6.79688 10.0781V8.20312H4.92188C4.53223 8.20312 4.21875 7.88965 4.21875 7.5C4.21875 7.11035 4.53223 6.79688 4.92188 6.79688H6.79688V4.92188C6.79688 4.53223 7.11035 4.21875 7.5 4.21875C7.88965 4.21875 8.20312 4.53223 8.20312 4.92188V6.79688H10.0781C10.4678 6.79688 10.7812 7.11035 10.7812 7.5C10.7812 7.88965 10.4678 8.20312 10.0781 8.20312H8.20312V10.0781C8.20312 10.4678 7.88965 10.7812 7.5 10.7812C7.11035 10.7812 6.79688 10.4678 6.79688 10.0781Z" fill="white"/>
                                            </svg>
                                            <span>Add More Option</span>
                                        </button>
                                    </div>
                                    <form action="">
                                        <div class="form-group d-flex align-items-center" style="gap: 12px;">
                                            <input type="text" class="form-control mb-0" placeholder="Option 1">
                                            <button type="button" class="btn">
                                                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.048 17C11.808 16.52 11.52 15.992 11.184 15.416C10.848 14.824 10.48 14.216 10.08 13.592C9.68 12.952 9.264 12.32 8.832 11.696C8.4 11.056 7.984 10.456 7.584 9.896C7.184 10.456 6.768 11.056 6.336 11.696C5.904 12.32 5.488 12.952 5.088 13.592C4.704 14.216 4.336 14.824 3.984 15.416C3.648 15.992 3.36 16.52 3.12 17H0.552C1.272 15.592 2.104 14.16 3.048 12.704C4.008 11.248 5.024 9.752 6.096 8.216L0.768 0.368H3.456L7.56 6.56L11.616 0.368H14.28L9.048 8.12C10.136 9.672 11.16 11.184 12.12 12.656C13.08 14.128 13.928 15.576 14.664 17H12.048Z" fill="black"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="form-group d-flex align-items-center" style="gap: 12px;">
                                            <input type="text" class="form-control mb-0" placeholder="Option 2">
                                            <button type="button" class="btn">
                                                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.048 17C11.808 16.52 11.52 15.992 11.184 15.416C10.848 14.824 10.48 14.216 10.08 13.592C9.68 12.952 9.264 12.32 8.832 11.696C8.4 11.056 7.984 10.456 7.584 9.896C7.184 10.456 6.768 11.056 6.336 11.696C5.904 12.32 5.488 12.952 5.088 13.592C4.704 14.216 4.336 14.824 3.984 15.416C3.648 15.992 3.36 16.52 3.12 17H0.552C1.272 15.592 2.104 14.16 3.048 12.704C4.008 11.248 5.024 9.752 6.096 8.216L0.768 0.368H3.456L7.56 6.56L11.616 0.368H14.28L9.048 8.12C10.136 9.672 11.16 11.184 12.12 12.656C13.08 14.128 13.928 15.576 14.664 17H12.048Z" fill="black"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="form-group d-flex align-items-center" style="gap: 12px;">
                                            <input type="text" class="form-control mb-0" placeholder="Option 3">
                                            <button type="button" class="btn">
                                                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.048 17C11.808 16.52 11.52 15.992 11.184 15.416C10.848 14.824 10.48 14.216 10.08 13.592C9.68 12.952 9.264 12.32 8.832 11.696C8.4 11.056 7.984 10.456 7.584 9.896C7.184 10.456 6.768 11.056 6.336 11.696C5.904 12.32 5.488 12.952 5.088 13.592C4.704 14.216 4.336 14.824 3.984 15.416C3.648 15.992 3.36 16.52 3.12 17H0.552C1.272 15.592 2.104 14.16 3.048 12.704C4.008 11.248 5.024 9.752 6.096 8.216L0.768 0.368H3.456L7.56 6.56L11.616 0.368H14.28L9.048 8.12C10.136 9.672 11.16 11.184 12.12 12.656C13.08 14.128 13.928 15.576 14.664 17H12.048Z" fill="black"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- /Poll -->

                            <p class="text-muted font-italic" id="comment_form_note_blk" style="display: none;"><span id="comment_form_note"></span></p>

                            <div class="d-flex flex-wrap justify-content-between mt-4" style="gap: 1rem;">
                                <div class="d-flex flex-wrap align-items-center align-items-center" style="gap: 1rem;">
                                    <button type="button" class="btn align-items-center add-poll-btn" style="gap: 12px;display: none;"><!--d-flex -->
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M4 0C1.79375 0 0 1.79375 0 4V24C0 26.2062 1.79375 28 4 28H24C26.2062 28 28 26.2062 28 24V4C28 1.79375 26.2062 0 24 0H4ZM8 12C9.10625 12 10 12.8938 10 14V20C10 21.1063 9.10625 22 8 22C6.89375 22 6 21.1063 6 20V14C6 12.8938 6.89375 12 8 12ZM12 8C12 6.89375 12.8938 6 14 6C15.1062 6 16 6.89375 16 8V20C16 21.1063 15.1062 22 14 22C12.8938 22 12 21.1063 12 20V8ZM20 16C21.1063 16 22 16.8937 22 18V20C22 21.1063 21.1063 22 20 22C18.8937 22 18 21.1063 18 20V18C18 16.8937 18.8937 16 20 16Z" fill="#444444"/>
                                        </svg>
                                        <span>Add a Poll to the discussion</span>
                                    </button>
                                    <div class="select-container" style="display: none;">
                                        <select name="" id="" class="form-control">
                                            <option value="" selected>Select Category</option>
                                        </select>
                                        <svg width="12" height="9" viewBox="0 0 12 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.15244 8.56072C5.62099 9.14643 6.38193 9.14643 6.85048 8.56072L11.6485 2.56306C11.9933 2.13198 12.0945 1.49004 11.9071 0.927763C11.7197 0.365482 11.2849 0 10.7976 0L1.20159 0.00468596C0.718038 0.00468596 0.279471 0.370168 0.0920492 0.932449C-0.0953726 1.49473 0.00958361 2.13667 0.350691 2.56775L5.14869 8.5654L5.15244 8.56072Z" fill="black"/>
                                        </svg>
                                    </div>
                                </div>

                                <button type="submit" id="frm_comment_send_btn" class="btn start-discussion-submit px-4" disabled>
                                    <i class="fa fa-comments" aria-hidden="true"></i>
                                    <span>Start Discussion</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="add-auto-discussion-topics" tabindex="-1" role="dialog" aria-labelledby="adddiscussionModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    
                        <div class="modal-header">
                            <h5 class="modal-title" id="discussionModalTitle">Add Topics</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                                Are you sure to add topics automatically?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn btn-danger m-2" data-dismiss="modal">No</button>
                            <button type="button" class="btn theme-btn-primary" id="add_auto_discussions">Yes</button>
                    
                        </div>
                    
                </div>
            </div>
        </div>
        <!-- /new script -->

        <div class="modal fade" id="video_room_join_confirmation" tabindex="-1" role="dialog" aria-labelledby="videoRoomJoinConfirmationModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="videoRoomJoinConfirmationModalTitle">Group Video Networking Guidelines</h5>
                    </div>
                    <div class="modal-body">
                        <p>Welcome to our self-moderated networking room, for productive outcome make sure to:</p>
                        <ul class="video_room_join_guidelines">
                            <li><b>Introduce Yourself:</b> Share your name and purpose for joining.</li>
                            <li><b>Invite Others:</b> Encourage new participants to introduce themselves.</li>
                            <li><b>Conversation Etiquette:</b> Respect everyone&rsquo;s voice; avoid dominating the discussion.</li>
                            <li><b>Participate and Connect:</b> Connect with peers, participate in conversation and establish connections.</li>
                        </ul>
                        <br>
                        <p><b>For 1:1 Networking,</b> Click on participant names in the list below for private chats and video calls.</p>
                        <br>
                        <p>By clicking the 'Join Now' button, you consent to participate in this virtual networking event and agree to adhere to our community guidelines. Please be aware that during the session, your audio and video may be visible to other participants, and your participation will conform to our privacy and event policies.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn theme-btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn theme-btn-primary" id="video_room_join_now_btn"><i class="fa fa-video-camera mr-1" aria-hidden="true"></i> Join Now</button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal" id="ans-poll" tabindex="-1" role="dialog" aria-labelledby="anspollModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content pb-4 poll">
                    <div class="modal-header d-flex align-items-center px-3" style="background: transparent; border-bottom: 2px solid #d3d3d3;">
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <img src="<?php echo TAOH_SITE_URL_ROOT.'/assets/images/placeholder-image.jpg'; ?>" alt="" class="lazy post-profile-img">
                            <div>
                                <p style="font-size: 19px; color: #969696; font-weight: 400;" id="anspollModalLabel">poll</p>
                                <p class="question p-0" style="font-size: 20px; color: #444444; font-weight: 400;"></p>
                            </div>
                        </div>
                        <button type="button" class="close modal-close-btn m-0" data-dismiss="modal" aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.5265 2.73247C16.1512 2.10776 16.1512 1.09324 15.5265 0.46853C14.9018 -0.156177 13.8872 -0.156177 13.2625 0.46853L8 5.73606L2.73247 0.473528C2.10776 -0.151179 1.09324 -0.151179 0.46853 0.473528C-0.156177 1.09824 -0.156177 2.11276 0.46853 2.73747L5.73606 8L0.473528 13.2675C-0.151179 13.8922 -0.151179 14.9068 0.473528 15.5315C1.09824 16.1562 2.11276 16.1562 2.73747 15.5315L8 10.2639L13.2675 15.5265C13.8922 16.1512 14.9068 16.1512 15.5315 15.5265C16.1562 14.9018 16.1562 13.8872 15.5315 13.2625L10.2639 8L15.5265 2.73247Z" fill="#D3D3D3"/>
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!--ans poll -->
                        <div class="answers"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        
        include_once(TAOH_SITE_DOC_ROOT.'/app/events/sponsor_details_modal.php');?>
    </div>

    <script type="text/javascript">
        let isLoggedIn = "<?php echo taoh_user_is_logged_in(); ?>";
        let chatloaderArea = $('#loaderArea');
        loader(true, chatloaderArea);

        let locationSelectInput = $('#locationSelect');
        let geohashInput = $('#geohash');
        let geohash = geohashInput.val();

        let search = "";
        let locationClear = $('#locationClear');
        let searchClear = $('#searchClear');

        let activeListloaderArea = $('#activeListloaderArea');
        let listUpdatedAt = 0;
        let activeChatList = $('#activeChatList');
        let activeRoomList = $('#activeRoomList');
        let entriesList = $('#entriesList');
        

        var current_tab = 'thisnetwork-tab';
        var loaderArea = $('#loaderArea-' + current_tab);
        var networkArea = $('#networkArea-' + current_tab);
        loader(true, loaderArea, 75);

        var my_pToken = '<?php echo $ptoken ?? ''; ?>';
        var event_owner_ptoken = '<?php echo $event_owner_ptoken ?? ''; ?>';


        
        let ntwuserInfoList = {};
        let ntwroomInfoList = {};

        /**********************====================== Networking Constants ===========================**********************/
        const ntw_chatContainer = document.getElementById('comments');
        const stickyBadge = document.getElementById('stickyBadge');

        let sendChat = $('#sendChat');
        let chatCount = $('#chatCount');
        let replyQoute = $('#replyQoute');
        let comments = $('#comments');
        let commentInput = $('#commentInput');
        let newmessages_btn_grp = $('#newmessages_btn_grp');
        let newmessages_btn = $('#newmessages_btn');
        let maxradius = 50000;
        let minradius = 100;
        let ntwEntriesETag = null;

        var liveonly = 0;
        var totalentries = 0;
        var firstLoad = 1;
        var setlock = 0;

        let totalItems = 0;
        let itemsPerPage = 0; // zero for no limit
        let currentPage = 1;
        let ntw_view = '<?php echo $ntw_view ?: 1; ?>';
        let networkTitle = '<?php echo $contslug; ?>';
        let show_video_conv_btn = <?= json_encode($show_video_conv_btn ?? false); ?>;

        let chat_window = 0;
        let ntw_room_key = '<?php echo $keytoken ?? ''; ?>';
        let eventtoken = '<?php echo $club_token ?? ''; ?>'; 
        let chatwith = '<?php echo $chatwith ?? ''; ?>';
        let chatwith_liveStatus = 0;
        let chatname = "<?php echo $_GET['with'] ?? ''; ?>";
        
        let ntwChatFirstCall = 1;
        let ntwChatLastTime = 0;
        let ntw_newMessagesCnt = 0;
        let ntw_entries_cleared = 1;
        let ntw_isProcessing = false;
        let ntw_msgScrollUpEnded = false;
        let ntw_msgUpIndex = 0;
        let ntw_msgDownIndex = 0;
        let ntwChatPageNo = 1;
        const ntwChatItemsPerPage = 10;
        let ntw_chatBadgeUpDate = '';
        let ntw_chatBadgeDownDate = '';
        let ntwChatDataInterval;
        let ntwChatDataFromServerInterval;
        let ntwUserEntriesIntervalId;
        let ntwUserEntriesInterval = 10000; // 10 seconds

        let userInfoTimeout;
        let userLiveIntervalId;
        let userLiveStatusInterval = 60000; // 1 minute
        let updateSenderArea = false;

        var profile_badges = [];
        /**********************====================== Networking Constants ===========================**********************/

        /**********************====================== Forum Constants ===========================**********************/
        const frm_chatContainer = document.getElementById('frm_comments_list');
        // const stickyBadge = document.getElementById('stickyBadge');

        let frm_commentInput = $('#frm_comment');
        let frm_comments_list = $('#frm_comments_list');
        let frm_ReplycommentInput = $('#frm_reply_comment');
        let frm_reply_comments_list = $('#frm_reply_comments_list');

        let frm_room_key = '<?php echo $keytoken ?? ''; ?>';


        let frmChatFirstCall = 1;
        let frmChatLastTime = 0;
        let frm_newMessagesCnt = 0;
        let frm_entries_cleared = 1;
        let frm_isProcessing = false;
        let frm_msgScrollUpEnded = false;
        let frm_msgScrollDownEnded = false;
        let frm_msgUpIndex = 0;
        let frm_msgDownIndex = 0;
        let frmChatPageNo = 1;
        const frmChatItemsPerPage = 10;
        let frm_chatBadgeUpDate = '';
        let frm_chatBadgeDownDate = '';
        let frmChatDataInterval;
        let frmChatDataFromServerInterval;

        let frm_my_recent_message_timestamp = 0;
        let frm_commentDelayTimeInSeconds = 7200; // 2 hours
        let frm_checkCommentDelayInterval = null;

        const frm_reply_chatContainer = document.getElementById('frm_reply_comments_list');

        let frmReplyChatFirstCall = 1;
        let frmReplyChatLastTime = 0;
        let frm_reply_newMessagesCnt = 0;
        let frm_reply_entries_cleared = 1;
        let frm_reply_isProcessing = false;
        let frm_reply_msgScrollUpEnded = false;
        let frm_reply_msgUpIndex = 0;
        let frm_reply_msgDownIndex = 0;
        let frmReplyChatPageNo = 1;
        const frmReplyChatItemsPerPage = 10;
        let frm_reply_chatBadgeUpDate = '';
        let frm_reply_chatBadgeDownDate = '';
        let frmReplyChatDataInterval;
        let frmReplyChatDataFromServerInterval;

        /**********************====================== /Forum Constants ===========================**********************/

        $(document).ready(function () {

            /**********************====================== Networking Scripts ===========================**********************/
            $('#network_status').hide();
            $('#accordion1').hide();
            $('#collapseOnes').removeClass('show');

            if (ntw_view == 2) {
                updateChatWindow(chatwith);
            } else {
                $(".open_window").hide();
                $(".listwindow").show();

                taoh_load_network_entries();

                fetchNTWInvites();
                setInterval(function () {
                    fetchNTWInvites();
                }, 6000);

                changeBrowserBackButtonUrl(window.location.href);
            }

            setTimeout(function () {
                taoh_ntw_post_metrics('view');
                taoh_network_update_online();
                taoh_get_mood_status();
                if (ntw_view == 2) {
                    let requestData = getNTWChatRequestData(my_pToken, chatwith, 'init');
                    fetchNTWChatData(requestData, false);

                    initNTWChatDataInterval();

                    taoh_ntw_post_metrics('openchat');
                }
            }, 1000);

            sendChat.on('click', function () {
                let message = commentInput.val();
                if (message.trim() === '') {
                    alert('Message seems empty! Please enter valid message to send.');
                    return false;
                }
                if (message !== "") {
                    let toToken = $("#lastchatwith").val();

                    $('#message_helper').hide();
                    sendNTWChat(message, my_pToken, toToken, ntw_room_key, 'user');
                }
            });

            $('.ts-control').css('height', '37px');

            getUserInfo(my_pToken, 'public').catch((e) => {console.log(e)});

            $('#networking_message_send_button').on('click', function () {
                let message = $('#networkingOfflineMessage').val();
                let locationPath = $('#networkingOfflineLocationPath').val();
                let toPtoken = $('#networkingOfflineToPtoken').val();
                let networking_message_send_button_elem = $('#networking_message_send_button');

                if(message.trim() === ''){
                    alert('Please enter message');
                    return false;
                }

                if(toPtoken?.trim() !== '') {
                    networking_message_send_button_elem.attr('disabled', 'disabled');
                    networking_message_send_button_elem.html('Sending <i class="fa fa-circle-o-notch fa-spin"></i>');
                    $.post(_taoh_site_ajax_url, {
                        'taoh_action': 'taoh_post_message',
                        'message': message,
                        "ptoken": toPtoken,
                        "location_path": locationPath
                    }, function (response) {
                        $('#networkingOfflineMessage').val('');
                        $('#networkingOfflineMessageBlock').hide();
                        $('#networkingOfflineSuccessMessage').show();
                        setTimeout(function () {
                            networking_message_send_button_elem.removeAttr('disabled');
                            networking_message_send_button_elem.text('Send');
                            $('#networkingOfflineMessageModal').modal('hide');
                        }, 1500);
                    });
                }

                if(ntw_room_key?.trim() !== '' && toPtoken?.trim() !== '') {
                    let sent_time = new Date().getTime();
                    let mc_data = {
                        'taoh_action': 'taoh_room_send_message',
                        'ptoken': my_pToken,
                        'other_ptoken': toPtoken,
                        "message": message,
                        'key': ntw_room_key,
                        'sent_time': sent_time
                    };

                    $.post(_taoh_site_ajax_url, mc_data, function (response) {});
                }
            });

            newmessages_btn.on('click', function () {
                comments.animate({scrollTop: comments.prop("scrollHeight")}, 1000);
                newmessages_btn_grp.hide();
            });

            $('#newmessages_close_btn').on('click', function () {
                newmessages_btn_grp.hide();
            });

            comments.on('scroll', function () {
                if(!isScrolledUp(ntw_chatContainer)){
                    newmessages_btn_grp.hide();
                }
            });

            if (show_video_conv_btn && $('#video_room_btn').length > 0) {
                $('#video_room_join_now_btn').on('click', function () {
                    $('#video_room_join_now_btn i').removeClass('fa-video-camera').addClass('la-spinner la-spin');

                    let data = {
                        'taoh_action': 'taoh_add_video_chat',
                        'my_token': ntw_room_key,
                        'guest_token': ntw_room_key,
                        'parent_keyslug': ntw_room_key,
                        'my_pToken': my_pToken,
                        'network_title': '<?php echo $club_info['title'] ?? 'Video-Chat'; ?>'
                    };

                    $.post(_taoh_site_ajax_url, data, function (response) {
                        $('#video_room_join_now_btn i').removeClass('la-spinner la-spin').addClass('fa-video-camera');
                        $("#video_room_join_confirmation").modal('hide');
                        loadCustomRooms();
                        if (response.my_link) window.open(response.my_link);
                    }).fail(function () {
                        $('#video_room_join_now_btn i').removeClass('la-spinner la-spin').addClass('fa-video-camera');
                    });
                });

                $("#video_room_join_confirmation").on('hide.bs.modal', function(){
                    $('#video_room_join_now_btn i').removeClass('la-spinner la-spin').addClass('fa-video-camera');
                });
            }

            /**********************====================== /Networking Scripts ===========================**********************/


            /**********************====================== Forum Scripts ===========================**********************/
            var auto_discussions_added  = localStorage.getItem('auto_discussions_added_for_'+frm_room_key);
            //my_pToken = 
             console.log(event_owner_ptoken+'-----------'+my_pToken)
           // if(auto_discussions_added != 1 && event_owner_ptoken == my_pToken){
            if(auto_discussions_added != 1 && event_owner_ptoken != my_pToken){
               // $('#add-auto-discussion-topics').modal('show');
            }

            autoDiscussionAdd();
            
            $('#add_auto_discussions').on('click', function () {
                autoDiscussionAdd();
            });

            function autoDiscussionAdd(){
                var message1 = `Need Help? Post Here! 🚀
                                →  Looking for advice, guidance, or connections? Drop your question or request here, 
                                and let the community help you out!`;


                var message2 = `Offer Help? Share How You Can Support Others! 🤝
                                  → Have experience, skills, or resources to share? Let others know how you can assist
                                   and make meaningful connections!`;

                var message3 = `Introduce Yourself & Get Noticed! 🌟
                            → "Tell us a bit about yourself—your interests, skills, or what brought you here—so like-minded people can 
                            connect with you!`;
            var message4 = `Success Stories & Wins! 🎉
                 → Got a win, big or small? Share your achievements or milestones to inspire others!`;
            var message5 = `Collaboration Corner! 🤝
             → Looking for partners, team members, or collaborators? 
            Post your idea and find others who share your vision!`;
            var message6 = `Quick Icebreaker! 👋
                     → What's one thing you're passionate about? Drop it here to find people with similar interests!`;
            var message7 = `Shoutouts & Thank Yous! 💙
                → Did someone help you out? Give them a shoutout and spread the positivity!`;
                sendFRMChat(message1, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message2, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message3, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message4, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message5, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message6, event_owner_ptoken, 0, frm_room_key, 'user', 1);
                sendFRMChat(message7, event_owner_ptoken, 0, frm_room_key, 'user', 1);

                localStorage.setItem('auto_discussions_added_for_'+frm_room_key, '1');
                

                // $('#networkingTab [data-target="#conversation"]').tab('show');
                $('#add-auto-discussion-topics').modal('hide');
            }

            $('#comment_form').validate({
                rules: {
                    frm_comment: {
                        required: true,
                        maxlength: 300
                    },
                    // frm_comment_file: {
                    //     required: true,
                    //     extension: "jpg,jpeg,doc|docx|pdf",
                    //     filesize: 5,
                    // }
                },
                messages: {
                    frm_comment: {
                        required: "Comment required"
                    },
                },
                submitHandler: function (form) {
                    // let comment_form = $('#comment_form');
                    let message = frm_commentInput.val();

                    sendFRMChat(message, my_pToken, 0, frm_room_key, 'user');
                }
            });

            $('#reply_comment_form').validate({
                rules: {
                    frm_reply_comment: {
                        required: true,
                        maxlength: 300
                    },
                    // frm_comment_file: {
                    //     required: true,
                    //     extension: "jpg,jpeg,doc|docx|pdf",
                    //     filesize: 5,
                    // }
                },
                messages: {
                    frm_reply_comment: {
                        required: "Comment required"
                    },
                },
                submitHandler: function (form) {
                    let reply_comment_id = $('#reply_comment_id').val();
                    if(!reply_comment_id){
                        alert('Invalid comment reply id');
                    }

                    // let comment_form = $('#comment_form');
                    let reply_message = frm_ReplycommentInput.val();

                    sendFRMChat(reply_message, my_pToken, reply_comment_id, frm_room_key, 'user');
                }
            });

            if (my_pToken !== '') {
                let chat_networking_misc_key = 'ft_frm_networking_misc_' + frm_room_key;
                IntaoDB.getItem(objStores.ntw_store.name, chat_networking_misc_key).then((intao_data) => {
                    if (intao_data && intao_data.last_update_time) {
                        lastFRMMsgCheckedTimestamp = intao_data.last_update_time;
                    } else {
                        lastFRMMsgCheckedTimestamp = 0;
                    }
                    getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 2);
                }).then(() => {
                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);
                    taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, 0, lastCheckedTimestamp));
                });
            }

            updateForumWindow();

            /**********************====================== /Forum Scripts ===========================**********************/

            initializeRequest();


            // Remove query string from URL after page load
            const url = new URL(window.location.href);
            if (url.searchParams.has('t')) {
                url.searchParams.delete('t');
                window.history.pushState({}, '', url.toString());
            }

        });

        /**********************====================== Networking Functions ===========================**********************/
        $(document).on('click', '#networking_offline_send_message_btn', function () {
            if(ntw_room_key.trim() !== '' && chatwith.trim() !== ''){
                let respondLocationPath = '<?php echo '/' . TAOH_MESSAGEPAGE_NAME; ?>/chatwith/' + ntw_room_key + '-' + my_pToken + '?from=networking';

                $('#networkingOfflineMessage').val('');
                $('#networkingOfflineToPtoken').val(chatwith);
                $('#networkingOfflineLocationPath').val(respondLocationPath);
                $('#networkingOfflineSuccessMessage').hide();
                $('#networkingOfflineMessageBlock').show();
                $('#networkingOfflineMessageModal').modal('show');
            }
        });

        let prev_userLiveStatusInterval = userLiveStatusInterval;
        function userLiveStatusUpdate(interval) {
            userLiveIntervalId = setInterval(function() {
                if (ntw_room_key.trim() !== '' && chatwith.trim() !== '' && typeof getUserLiveStatus === 'function') {
                    getUserLiveStatus(chatwith).then((userLiveStatus) => {
                        if (userLiveStatus.success) {
                            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;

                            const chatArea = $('#chatArea');
                            chatArea.find('.userlivestatus').addClass(chatwith_liveStatus ? 'active-status' : 'active-status-border');
                            chatArea.find('.userlivestatus').removeClass(chatwith_liveStatus ? 'active-status-border' : 'active-status');
                            chatArea.find('.userlivestatus_txt').text(chatwith_liveStatus ? 'Active' : 'Away');

                            if (document.visibilityState === 'visible') userLiveStatusInterval = chatwith_liveStatus ? 60000 : 120000; // 1 minute : 2 minutes
                            if (prev_userLiveStatusInterval !== userLiveStatusInterval) {
                                prev_userLiveStatusInterval = userLiveStatusInterval;
                                clearInterval(userLiveIntervalId);
                                userLiveStatusUpdate(userLiveStatusInterval);
                            }
                        }
                    }).then(() => {
                        if(updateSenderArea) updateNTWChatSendArea(ntw_room_key, chatwith);
                    });
                }
            }, interval);
        }

        function updateNTWUserEntriesInterval() {
            if(ntwUserEntriesIntervalId) clearInterval(ntwUserEntriesIntervalId);
            ntwUserEntriesIntervalId = setInterval(function () {
                if (!document.hidden) {
                    taoh_load_network_entries();
                }
            }, ntwUserEntriesInterval);
        }


        function changeBrowserBackButtonUrl(newUrl) {
            history.pushState({ path: newUrl }, '', newUrl);
        }

        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.path) {
                window.location.href = event.state.path;
            } else {
                history.back();
            }
        });

        $(".p-custom_status_modal__preset_container").hover(
            function () {
                $(this).addClass("p-custom_status_modal__preset_container--active");
                $(this).removeClass("focus");
            },
            function () {
                $(this).removeClass("p-custom_status_modal__preset_container--active");
            }
        );

        function openStatusModal() {
            $('#status-modal').modal('show');
        }

        $(document).on("click", "#my_status", function () {
            let emojisrc = $('#loadEmojiImg').attr('src');
            let my_status_text = $('#my_status').val();
            if(my_status_text) $('#current_status').val(my_status_text.trim());
            if(emojisrc) $('#selected-emoji img').attr('src', emojisrc);
            $('#status-modal').modal('show');
        });

        $(document).on("click", ".texty_single_line_input", function () {
            $(this).addClass("focus");
        });

        function copyToStatus(status_val) {
            $('#current_status').val(status_val.trim());
        }

        function saveStatus() {
            var customer_status = $('#current_status').val();
            var choosen_emoji = $('#choosen_emoji').val();
            var mood_status_message = customer_status + "###" + choosen_emoji;
            if (customer_status?.trim() === '' && choosen_emoji?.trim() === ''){
                mood_status_message = '';
            }
            $('#status-modal').modal('hide');
            taoh_update_mood_status(ntw_room_key, my_pToken, mood_status_message);
        }

        $(document).on("click", ".status-save", function () {
            var customer_status = $('.ql-editor').text();
            $('#my_status').val(customer_status.trim());
            $('#my_status').attr('disabled', 'disabled');
            $('#network_status').hide();
        });

        function zoomin() {
            var radius = $('#radius').val();
            var newrad = parseInt(radius) + parseInt(500);
            if (newrad <= maxradius) {
                $('#radius').val(newrad);
                loader(true, loaderArea, 75);
                taoh_load_network_entries();
            }
        }

        function zoomout() {
            var radius = $('#radius').val();
            var newrad = parseInt(radius) - parseInt(500);
            if (newrad > minradius) {
                $('#radius').val(newrad);
                loader(true, loaderArea, 75);
                taoh_load_network_entries();
            }
        }


        /* Get User Information */
        async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
            if (!pToken_to?.trim()) return null;

            let userInfo = {};

            // Initialize ops object if not exists
            ntwuserInfoList[ops] = ntwuserInfoList[ops] || {};

            if (!serverFetch) {
                // Try to get userInfo from local variable
                userInfo = ntwuserInfoList[ops][pToken_to] || userInfo;

                // Try to get userInfo from global variable
                if (!userInfo.ptoken) {
                    ntwuserInfoList[ops] = ntwuserInfoList[ops] || {};
                    userInfo = ntwuserInfoList[ops][pToken_to] || userInfo;
                }

                // Try to get userInfo from IndexedDB
                if (!userInfo.ptoken) {
                    const user_info_key = 'user_info_list';
                    const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                    if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                        let userInfoObj = intao_data.values[ops][pToken_to];
                        // Check if data is expired (expires after 2 day)
                        if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                            ntwuserInfoList[ops][userInfoObj.ptoken] = userInfoObj;
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
                    ntwuserInfoList[ops][srv_userInfoObj.ptoken] = srv_userInfoObj;
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
                ntwuserInfoList[ops][userInfo.ptoken] = userInfo;
            }

            return userInfo;
        }

        /* /Get User Information */



        /* NTW chat */

        function getNTWChatRequestData(pToken_from, pToken_to, callFromEvent = 'init') {
            return {
                "pToken_from": pToken_from,
                "pToken_to": pToken_to,
                "page": ntwChatPageNo,
                "itemPerPage": ntwChatItemsPerPage,
                "callFromEvent": callFromEvent
            };
        }

        async function updateUserProfileInfo(userInfo) {
            // chatwith, serverFetch = false
            // getUserInfo(chatwith, 'public', serverFetch).then((userInfo) => {
            const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

            let profile_info = `<div class="row pt-3">
                    <div class="col-lg-4">
                        <div class='comment-avatar mr-2' style="background:#52514f;vertical-align:middle;margin-right:1em;">
                            <img width="40" class="lazy" src="${userAvatarSrc}" alt="avatar">
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <p class="fs-16 text-capitalize" id="chat_name"><strong>${userInfo.chat_name}</strong></p>
                        <a id="viewProfileClick1" target="_blank" href="${_taoh_site_url_root + '/profile/' + userInfo.ptoken}" profile_id="${userInfo.ptoken}" style="cursor:pointer;color: #007bff;">View full profile</a>
                    </div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg-12">${(userInfo.full_location != '') ? '<p><i class="la la-map-marker"></i>' + userInfo.full_location + '</p>' : ''}</div>
                </div>`;

            $("#profile_info").html(profile_info);
            if (chat_window) $(".open_window").show();
            // });
        }

        function copyToMessage(msg) {
            commentInput.val(msg);
        }

        function controlTextareaEnter(event, triggerElemId){
            if (event.key === 'Enter' && !event.shiftKey && !event.ctrlKey) {
                event.preventDefault();
                if ($(triggerElemId).length > 0) $(triggerElemId).trigger('click');
            } else if (event.key === 'Enter' && event.ctrlKey) {
                // Insert a new line when Ctrl + Enter is pressed
                const textarea = $(this)[0];
                const cursorPosition = textarea.selectionStart;
                const textBeforeCursor = textarea.value.substring(0, cursorPosition);
                const textAfterCursor = textarea.value.substring(cursorPosition);
                textarea.value = textBeforeCursor + '\n' + textAfterCursor;
                textarea.selectionStart = textarea.selectionEnd = cursorPosition + 1;
                event.preventDefault();
            }
        }

        function sendNTWChat(message, my_pToken, chatwith, ntw_room_key, user_type = 'user') {

            if (ntw_room_key.trim() === '' || chatwith.trim() === '') {
                alert('Please select a user you want to chat.');
                return false;
            }

            if (message.trim() === '') {
                alert('Message seems empty! Please enter valid message to send.');
                return false;
            }

            if (user_type !== 'system') {
                let commentInput_emojiInstance = commentInput.data("emojioneArea");
                if (commentInput_emojiInstance) {
                    commentInput_emojiInstance.setText('');
                } else {
                    commentInput.val('');
                }

                $('#message_helper').hide();
            }

            let totalchatscnt = (parseInt(chatCount.text()) || 0) + 1;
            chatCount.text(totalchatscnt);

            let sent_time = new Date().getTime();

            let data = {
                'taoh_action': 'taoh_room_send_message',
                'message': message,
                'ptoken': my_pToken,
                'other_ptoken': chatwith,
                'user_type': user_type,
                'key': ntw_room_key,
                'sent_time': sent_time
            };

            let chatresponse = {
                "chat": [{
                    "ptoken": data.ptoken,
                    "message": data.message,
                    "to_ptoken": data.other_ptoken,
                    "user_type": data.user_type,
                    "room_hash": data.key,
                    "sent_time": data.sent_time,
                    "time": (data.sent_time * 1000)
                }],
                "isTempMsg": true,
                "overallChatCount": totalchatscnt,
                "recent_rendered_items": {}
            };
            renderNTWMessages(chatresponse);

            mover(replyQoute, 450);

            let chat_temp_messages_key = 'cm_temp_' + ntw_room_key + '_' + data.ptoken;
            let ptokenTo = data.other_ptoken;

            // Store temp messages in Indexed DB then send to server
            IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                let updatedResponse = {};
                if (intao_data?.values) {
                    updatedResponse = intao_data.values;
                }
                if (!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
                Object.assign(updatedResponse[ptokenTo], {[data.sent_time]: chatresponse.chat[0]});
                IntaoDB.setItem(objStores.ntw_store.name, {
                    taoh_ntw: chat_temp_messages_key,
                    values: updatedResponse,
                    timestamp: Date.now()
                });
            }).then(() => {
                if (navigator.onLine) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'post',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            ft_ntw_reFetchRequired = true;
                            update_stored_time_in_temp_messages(chat_temp_messages_key, response, ptokenTo);

                            taoh_ntw_post_metrics('chatpost');
                        },
                        error: function (xhr, status, error) {
                            console.log('Error:', xhr.status);
                            checkOfflineMessage = 1;
                            if (typeof syncOfflineMessages === 'function') {
                                syncOfflineMessages();
                            }
                        }
                    });
                } else {
                    checkOfflineMessage = 1;
                    if (typeof syncOfflineMessages === 'function') {
                        syncOfflineMessages();
                    }
                }
            });
        }

        function update_stored_time_in_temp_messages(chat_temp_messages_key, returnedData, ptokenTo) {
            if (returnedData.success) {
                // Update stored_time in temp messages
                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        if ((ptokenTo in updatedResponse) && (returnedData.sent_time in updatedResponse[ptokenTo])) {
                            updatedResponse[ptokenTo][returnedData.sent_time].stored_time = returnedData.stored_time;
                            IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
                        }
                    }
                });
            }
        }

        function fetchNTWChatData(requestData, serverFetch = false) {
            if (requestData.pToken_from && requestData.pToken_to) {
                if(ntw_isProcessing){
                    setTimeout(() => {
                        fetchNTWChatData(requestData, serverFetch);
                    }, 2000);
                    return;
                }

                ntw_isProcessing = true;

                lastchatwith = $("#lastchatwith").val();
                // if (lastchatwith != requestData.pToken_to) lastNTWMsgCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', 0, 1);

                // let formData = getNetworkingMessagesFormData(requestData.pToken_from, getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1));

                $("#lastchatwith").val(requestData.pToken_to);

                let chat_messages_key = 'cm_' + ntw_room_key + '_' + requestData.pToken_from + '_' + requestData.pToken_to;

                IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                    if (intao_data && intao_data.timestamp) {
                        processNTWChatData(requestData, intao_data.values);
                    } else {
                        // Sending default empty data to render chat window
                        const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                        processNTWChatData(requestData, {"chat": {}, "last_update_time": lastCheckedTimestamp, "success": true});
                    }
                });
            }
        }

        function processNTWChatData(requestData, response) {
            let processedResponse = {};
            let recentItemsObject = {};
            let recentRenderedItemsObject = {};
            let totalchats = 0;

            if (response.success) {
                let chats = response.chat ? response.chat : [];

                let allChatKeys = Object.keys(chats);
                totalchats = allChatKeys.length;
                if (totalchats > 0) {
                    if (requestData.callFromEvent == "init" || ntw_msgDownIndex == 0) {
                        const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                        if (slice_end > 0) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "scrollup") {
                        let currentFirstMsgIndex = allChatKeys.indexOf(ntw_msgUpIndex);
                        if (currentFirstMsgIndex > -1) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "interval") {
                        let currentLastMsgIndex = allChatKeys.indexOf(ntw_msgDownIndex);
                        if (currentLastMsgIndex > -1 && currentLastMsgIndex < (totalchats - 1)) {
                            const recentItems = Object.entries(chats).slice(-(totalchats - (currentLastMsgIndex + 1)), totalchats);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }

                        const recentRenderedItems = Object.entries(chats).slice(-20);
                        recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
                    }
                }
            }

            processedResponse.isTempMsg = false;
            processedResponse.callFromEvent = requestData.callFromEvent;
            processedResponse.chat = recentItemsObject;
            processedResponse.overallChatCount = totalchats;
            processedResponse.recent_rendered_items = recentRenderedItemsObject;
            processedResponse.last_update_time = response.last_update_time;

            renderNTWMessages(processedResponse);
        }

        async function compiledNTWMsgHtml(cd) {
            let compiledMMMsgHtml;
            let safeMessageHtml = '';
            const message_content = cd.message;
            if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
                safeMessageHtml = message_content;
            } else {
                const safeMessage = document.createElement('pre');
                safeMessage.textContent = message_content;
                safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }

            if (cd.userType === 'system') {
                compiledMMMsgHtml = `<li class="badge-system clearfix"><span class="badge badge-secondary">${safeMessageHtml}</span></li>`;
            } else {
                compiledMMMsgHtml = `<li class="chat-item align-items-end border-0 ${cd.ptokenTo === cd.ptokenFrom ? 'mine' : 'others'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''} py-2" id="${'msg_' + (cd.time)}">
                <div class="comment-body pt-0 card">
                    <div class="d-flex justify-content-between py-2">
                        <span class="chatname pr-3">
                            <span style="cursor: pointer" class="comment-user text-primary font-weight-bold">${cd.name}</span>
                        </span>
                        <small class="text-muted">${cd.formatted_time}</small>
                    </div>

                    <p class="comment-text pt-1 pb-2 lh-22">${safeMessageHtml}</p>
                </div>
            </li>`;
            }

            return compiledMMMsgHtml;
        }

        async function getNTWChatMsgHtml(response, tempMsgList) {
            let chats = response.chat || {};
            let isTempMsg = response.isTempMsg || false;

            let messageHtml = '';
            let ptokenTo = chatwith;
            let do_highlight = false;
            let compiledChatKeys = { chats: [], temp_chats: [] };

            let lastChatItemKey = Object.keys(chats).pop();
            for (const [key, v] of Object.entries(chats)) {
                const userInfo = await getUserInfo(v.ptoken);
                let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

                if (v.ptoken != my_pToken) {
                    if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') do_highlight = true;
                    if (response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') ntw_newMessagesCnt++;
                }

                let chatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);
                if (chatDateTime_arr) {
                    if (response.callFromEvent === 'scrollup' && key === lastChatItemKey) {
                        removeSameBadge(chatDateTime_arr[0]);
                    }

                    if (response.callFromEvent === 'scrollup' && chatDateTime_arr[0] !== ntw_chatBadgeUpDate) {
                        ntw_chatBadgeUpDate = chatDateTime_arr[0];
                        messageHtml += '<li class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + ntw_chatBadgeUpDate + '</span></li>';
                    } else if (response.callFromEvent !== 'scrollup' && chatDateTime_arr[0] !== ntw_chatBadgeDownDate) {
                        ntw_chatBadgeDownDate = chatDateTime_arr[0];
                        messageHtml += '<li class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + ntw_chatBadgeDownDate + '</span></li>';
                    }
                }

                let compileData = {
                    ptokenTo: v.ptoken,
                    message: v.message,
                    time: v.time,
                    name: userInfo.chat_name,
                    ptokenFrom: my_pToken,
                    userType: v.user_type,
                    isTempMsg: isTempMsg,
                    stored_time: stored_time,
                    formatted_time: chatDateTime_arr[1] ?? ''
                };
                messageHtml += await compiledNTWMsgHtml(compileData);

                compiledChatKeys.chats.push(isTempMsg ? (v.time).toString() : key);

                const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                if (tempMsgElem.length) tempMsgElem.removeClass('new');
            }

            if (!isTempMsg && typeof tempMsgList[ptokenTo] != 'undefined' && response.callFromEvent !== 'scrollup') {
                let allSentTimes = Object.keys(chats).map(k => chats[k]['sent_time']);
                for (const [key, v] of Object.entries(tempMsgList[ptokenTo])) {
                    const userInfo = await getUserInfo(v.ptoken);
                    let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';
                    let tempChatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);

                    if (!allSentTimes.includes((v.sent_time).toString())) {
                        let compileData = {
                            ptokenTo: v.ptoken,
                            message: v.message,
                            time: v.time,
                            name: userInfo.chat_name,
                            ptokenFrom: my_pToken,
                            userType: v.user_type,
                            isTempMsg: true,
                            stored_time: stored_time,
                            formatted_time: tempChatDateTime_arr[1] ?? ''
                        };
                        messageHtml += await compiledNTWMsgHtml(compileData);

                        compiledChatKeys.temp_chats.push(key);
                    }

                    const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.removeClass('new');
                }
            }

            return { messageHtml, compiledChatKeys, do_highlight };
        }

        function renderNTWMessages(response) {
            if (!response) {
                doAfterNTWMsgRender(response);
                return;
            }

            let chats = response.chat || {};
            let allChatKeys = Object.keys(chats);

            let chatTempMessagesKey = 'cm_temp_' + ntw_room_key + '_' + my_pToken;
            IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
                .then(intao_data => intao_data?.values || {})
                .then(tempMsgList => {
                    return getNTWChatMsgHtml(response, tempMsgList);
                })
                .then(({ messageHtml, compiledChatKeys, do_highlight }) => {
                    if (ntwChatFirstCall === 1 || response.callFromEvent === 'init') {
                        comments.empty();
                    }

                    $("#comments .temp_msg:not(.new)").remove();

                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        $('#no_message').remove();
                        $('#message_helper').hide();

                        if (response.callFromEvent === 'scrollup') {
                            comments.prepend(messageHtml);
                        } else {
                            comments.append(messageHtml);
                            if (isScrolledUp(ntw_chatContainer) && ntwChatFirstCall !== 1) {
                                if (ntw_newMessagesCnt > 0) {
                                    newmessages_btn.find('span').text(ntw_newMessagesCnt + ' new messages');
                                    newmessages_btn_grp.show();
                                }
                            } else {
                                ntw_chatContainer.scrollTop = ntw_chatContainer.scrollHeight;
                                ntw_newMessagesCnt = 0;
                            }
                        }
                    }

                    if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                        if (response.callFromEvent === 'init') {
                            ntw_msgUpIndex = allChatKeys[0];
                            ntw_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrollup') {
                            ntw_msgUpIndex = allChatKeys[0];
                        } else if (response.callFromEvent === 'interval') {
                            ntw_msgDownIndex = allChatKeys.pop();
                        }
                    }

                    ntwChatFirstCall = 0;
                    response.compiledChatKeys = compiledChatKeys;

                    // Clearing left over temp messages if already rendered
                    if ($('#comments .temp_msg').length) {
                        let recentRenderedItems = response.recent_rendered_items;
                        for (const [k, value] of Object.entries(recentRenderedItems)) {
                            let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                            if (tempMsgElem.length) tempMsgElem.remove();
                        }
                    }

                    if (do_highlight) highlightMessages();
                })
                .then(() => {
                    doAfterNTWMsgRender(response);
                });

            if (response.callFromEvent === "scrollup") {
                ntw_msgScrollUpEnded = false;
            }
        }

        function doAfterNTWMsgRender(response) {
            let overallChatCountWithTemp = response.overallChatCount;
            if (typeof response.compiledChatKeys != 'undefined') {
                overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
            }
            updateStickyBadgeTxt('#comments', '#stickyBadge');

            if (overallChatCountWithTemp > 0) {
                chatCount.text(overallChatCountWithTemp);
                $('#message_helper').hide();
                $('#message_count').show();
            } else {
                ntw_msgUpIndex = 0;
                ntw_msgDownIndex = 0;
                $('#message_count').hide();
                if (chatwith_liveStatus == 1) $('#message_helper').show();
                comments.html("<p id='no_message'>No Messages yet!</p>");
            }

            if (response.hasOwnProperty('last_update_time')) {
                ntwChatLastTime = response.last_update_time || 0;
            }

            updateNTWChatSendArea(ntw_room_key, chatwith);

            $("#ow_header").show();
            $("#ow_footer").show();

            ntw_isProcessing = false;
            taoh_Loader($('#ntw_chat_loader'), false);
            loader(false, chatloaderArea);
        }

        function updateNTWChatSendArea(ntw_room_key, chatwith) {
            if (ntw_room_key.trim() !== '' && chatwith.trim() !== '') {
                if (chatwith_liveStatus == 1) {
                    $('#message-area').hide();
                    $('#chat-area').show();
                    $('.ntw_video').show();
                    if (!$('.sender_part').is(":visible")) {
                        $('.sender_part').show();
                    }
                } else {
                    $('#chat-area').hide();
                    $('.sender_part').hide();
                    $('#message_helper').hide();
                    $('.ntw_video').hide();
                    $('#message-area').show();
                }
                updateSenderArea = true;
            }
        }

        function updateStickyBadgeTxt(badgeContainerId, stickyBadgeId) {
            const badgeContainer = document.querySelector(badgeContainerId);
            const stickyBadge = document.querySelector(stickyBadgeId);

            const scrollPosition = badgeContainer.scrollTop;

            const dateBadges = badgeContainer.querySelectorAll('.date-badge');

            dateBadges.forEach((badge) => {
                const badgeTop = badge.getBoundingClientRect().top;
                const containerTop = badgeContainer.getBoundingClientRect().top;

                if (badgeTop <= containerTop) {
                    stickyBadge.textContent = badge.textContent;
                    stickyBadge.style.display = (scrollPosition < 20) ? 'none' : 'block';
                }
            });
        }

        function removeSameBadge(daytxt) {
            const badges = document.querySelectorAll('.date-badge');
            if (badges.length > 0 && daytxt == badges[0].textContent) {
                const scrollPosition = ntw_chatContainer.scrollTop;
                badges[0].remove();

                // Restore the scroll position
                ntw_chatContainer.scrollTop = scrollPosition
            }
        }

        let ntw_prevScrollPos = ntw_chatContainer.scrollTop;
        ntw_chatContainer.addEventListener('scroll', () => {
            const ntw_currentScrollPos = ntw_chatContainer.scrollTop;

            if ((ntw_currentScrollPos < ntw_prevScrollPos) && ntw_currentScrollPos < 10 && !ntw_msgScrollUpEnded) {
                // Trigger only if the user scrolls up to the top of the chat container
                ntwChatPageNo++;

                taohLoader(document.getElementById('ntw_chat_loader'), true);
                let requestData = getNTWChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchNTWChatData(requestData, false), (ntw_isProcessing ? 500 : 100));
            }

            updateStickyBadgeTxt('#comments', '#stickyBadge');

            // Update the previous scroll position to find the direction of scrolling
            ntw_prevScrollPos = ntw_currentScrollPos;
        });

        function initNTWChatDataInterval() {
            if (ntwChatDataInterval) clearInterval(ntwChatDataInterval);
            ntwChatDataInterval = setInterval(function () {
                if (!ntw_isProcessing) {
                    if (my_pToken.trim() !== '' && chatwith.trim() !== '') {
                        let requestData = getNTWChatRequestData(my_pToken, chatwith, 'interval');
                        fetchNTWChatData(requestData, false);
                    }
                }
            }, 3000);
        }

        async function updateChatWindowHeader(chatwith) {
            const [userLiveStatus, userInfo] = await Promise.all([
                getUserLiveStatus(chatwith).catch((e) => {console.log(e)}),
                getUserInfo(chatwith, 'public').catch((e) => {console.log(e)}),
            ]);
            chatwith_liveStatus = Boolean(userLiveStatus.output) ? 1 : 0;
            chatname = userInfo.chat_name;

            let liveStatus = (chatwith_liveStatus ? 'active-status' : 'active-status-border');
            let statusName = (chatwith_liveStatus ? 'Active' : 'Away');

            const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                ? userInfo.avatar_image
                : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

            $("#current_chat_with_info").html(' ' + userInfo.chat_name + '<br><span class="userlivestatus ' + liveStatus + '"></span><small class="userlivestatus_txt ml-3">' + statusName + '</small>');
            $('#windowchatavatar').html(`<div class="comment-avatar chat_entries mr-2" style="background:#52514f;vertical-align:middle;margin-right:1em;">
            <img width="40" class="lazy" src="${userAvatarSrc}" alt="User Avatar"></div>`);

            updateUserProfileInfo(userInfo);
        }

        async function updateChatWindow(chatwith) {
            chatname = '';
            $("#current_chat_with_info").html('');
            $('#windowchatavatar').html('');
            $("#profile_info").html('');
            $("#comments").empty();

            $(".listwindow").hide();
            $("#ow_header").hide();
            $("#ow_footer").hide();
            $('#message_helper').hide();
            $('#message_count').hide();
            $('.ntw_video').hide();
            $('.sender_part').hide();
            $('#message-area').hide();
            $(".open_window").show();

            taoh_Loader($('#ntw_chat_loader'), true);

            if (ntwChatDataInterval) clearInterval(ntwChatDataInterval);

            userLiveStatusInterval = 3000;
            if (userLiveIntervalId) clearInterval(userLiveIntervalId);

            // updateUserProfileInfo(chatwith, false);

            await updateChatWindowHeader(chatwith).then(() => {
                chat_window = 1;
                ntwChatFirstCall = 1;
                ntwChatLastTime = 0;
                ntw_msgNewEntriesCnt = 0;
                ntw_msgScrollUpEnded = false;
                ntwChatPageNo = 1;
                updateSenderArea = false;

                let requestData = getNTWChatRequestData(my_pToken, chatwith, 'init');
                fetchNTWChatData(requestData);

                initNTWChatDataInterval();

                userLiveStatusUpdate(userLiveStatusInterval);
            });

            // Highlight the chatting user in the entries list
            let ntw_entry_active_cls = '.ntw_' + ntw_room_key + '_' + chatwith;
            $('#entriesList').find(".network_entries.active").removeClass('active');
            $('#entriesList').find(ntw_entry_active_cls).addClass('active');

            // Update invite read status
            if ($(this).hasClass('invites-btn') && typeof $(this).data('read') !== 'undefined') {
                if ($(this).data('read') == 0) {
                    let item_key = $(this).data('item') ?? '';
                    $(this).data('read', 1);
                    $(this).closest("div.invites-item").css("background", "inherit");
                    updateInvitesReadStatus(ntw_room_key, my_pToken, chatwith, item_key);
                }
            } else {
                let room_invites_elem = $('.room_' + chatwith + '_' + ntw_room_key);
                if (room_invites_elem.length > 0){
                    room_invites_elem.css("background", "inherit");
                }
                updateInvitesReadStatus(ntw_room_key, my_pToken, chatwith);
            }
        }

        $(document).on('click', '.openchatacc', function () {
            $('#networkingTab [data-target="#connections"]').tab('show');

            chatwith = $(this).data("chatwith").toString();

            updateChatWindow(chatwith);
            changeBrowserBackButtonUrl(window.location.href);
        });

        $('#commentInput').keydown(function (event) {
            controlTextareaEnter(event, '#sendChat');
        });

        $("#query").on('keyup', function (event) {
            searchFilter();
            // event.preventDefault();
            // if ($('#search').length > 0) $("#search").trigger('click');
        });

        function highlightMessages() {
            $('.chatBlock').addClass("highlight_msg");
            setTimeout(() => {
                $('.chatBlock').removeClass('highlight_msg');
            }, 5000);
        }

        /* /NTW chat */


        function searchFilter() {
            ntwEntriesETag = null;

            let queryString = $("#query").val();
            if (queryString.trim() !== '') {
                networkArea.empty();
                loader(true, loaderArea, 75);
                taoh_load_network_entries();
            }
        }

        /*function seeOnlyLive() {
            liveonly = $('#liveSwitch').is(":checked") ? 1 : 0;

            networkArea.empty();
            ntw_entries_cleared = 1;
            loader(true, loaderArea, 75);
            taoh_load_network_entries('', '', true);
        }*/

        function taoh_load_network_entries(tab = '', call_from = '', serverFetch = false) {
            let radius = $('#radius').val();
            let q = $("#query").val();

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
                'offset': ((currentPage - 1) * itemsPerPage),
                'limit': itemsPerPage,
                'geo_enable': "<?php echo $club_info['geo_enable'] ?: 0; ?>",
            };

            if(data.geo_enable == 1){
                data.latitude = "<?php echo $lat; ?>";
                data.longitude = "<?php echo $long; ?>";
            }

            let ntw_entries_key = 'ntw_entries_' + ntw_room_key;

            IntaoDB.getItem(objStores.ntw_store.name, ntw_entries_key).then((intao_data) => {
                // Check if data is expired (expires after 10sec (10 * 1000))
                if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 10000) && !serverFetch) {
                    process_network_entries(data, intao_data.values);
                } else {
                    if(!navigator.onLine) return;

                    $.ajax({
                        url: _taoh_cache_chat_proc_url,
                        type: 'POST',
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

                                IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: ntw_entries_key, values: updateData, timestamp: Date.now()});
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
                show_empty_network_entries_screen(networkArea);

                loader(false, loaderArea);
            }
            $('#live_switch').show();
        }

        /*function show_pagination(holder) {
            return $(holder).pagination({
                items: totalItems,
                itemsOnPage: itemsPerPage,
                currentPage: currentPage,
                displayedPages: 3,
                onInit: function () {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                },
                onPageClick: function (pageNumber, event) {
                    $("#pagination ul").addClass('pagination');
                    $("#pagination ul li.disabled").addClass('page-link text-gray');
                    $("#pagination ul li.active").addClass('page-link bg-primary text-white');
                    currentPage = pageNumber;
                    loader(true, loaderArea, 75);
                    taoh_load_network_entries('', 'pagination');
                }
            });
        }*/

        function render_network_member_template(data, slot) {
            return new Promise(async (resolve, reject) => {
                const ptoken = my_pToken ?? '';
                let htmlcontent = '';
                let left_htmlcontent = '';
                let totalentries = 0;

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

                    if (profile_badges != undefined && profile_badges.length == 0) {
                        profile_badges = await getSponsorBadges(eventtoken);
                    }
                    console.log('=====profile_badges===2=======', profile_badges);

                    for (const ll of finalitems) {
                        const kk = finalitems.indexOf(ll);
                        if (ll['cell'] !== null && ll['cell'] !== 'null') {
                            const l = JSON.parse(ll['cell']);

                            if (l.ptoken === ptoken && ll.status !== undefined) {
                                const fields = ll.status.split('###');
                                if (fields.length === 2) {
                                    updateStatusFields(fields);
                                }
                            }

                            var user_token = String(l.ptoken);
                            //alert([profile_badges.full].contains(user_token));
                            /*if (profile_badges.full.indexOf(user_token) >= 0) {
                                console.log('-----inside-------');
                            }*/

                            if (profile_badges != undefined && profile_badges.full.length > 0 && profile_badges.full.indexOf(user_token) >= 0) {
                                var badge_display = `<img style="margin-top: -30px;" class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/gold.png" alt="avatar" style="margin-top: -10px;">
                                 `;
                            } else if (profile_badges != undefined && profile_badges.semi.length > 0 && profile_badges.semi.indexOf(user_token) >= 0) {
                                var badge_display = `<img style="margin-top: -30px;" class="lazy sponsor-badge mb-3" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/silver.png" alt="avatar" style="margin-top: -10px;">
                                 `;
                            } else {
                                var badge_display = '';
                            }

                            //console.log('----profile_badges.full----------',user_token,'----------',profile_badges.full)
                            if (l.ptoken !== ptoken && l.chat_name) {
                                totalentries++;
                                const ntw_entries_cls = `ntw_${ntw_room_key}_${ll.ptoken}`;

                                const companies = formatObject(l.company);
                                const roles = formatObject(l.title);
                                const skillContent = buildSkillContent(l.skill, l.ptoken);
                                const userMoodStatus = buildUserMoodStatus(ll.status);
                                const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${l.avatar ? l.avatar : 'default'}.png`;
                                const avatarSrc = await buildAvatarImage(l.avatar_image, fallbackSrc);

                                let chatButton_blank_url = new URL(window.location.href);
                                chatButton_blank_url.searchParams.set('chatwith', l.ptoken);

                                const chatButton = `<button type="button" id="${l.ptoken}" class="btn btn-sm openchatacc mr-2" data-chatwith="${l.ptoken}" data-chatname="${l.chat_name}" data-live="${ll.live}" style="white-space: nowrap;font-size: small;border: 1px solid #c3c3c3;">
                            Chat <i class="la la-angle-double-right"></i></button>`;

                                const chatButton_blank = `<a href="${chatButton_blank_url}" target="_blank" class="btn btn-sm" title="Open chat in new tab" style="white-space: nowrap;border: 1px solid #c3c3c3;">
                            <i class="fa fa-external-link" aria-hidden="true"></i></a>`;

                                htmlcontent += `
                        <div class="network_entries ${ntw_entries_cls} entry_${totalentries} card card-item" style="margin-bottom:10px;">
                            <div class="card-body" style="font-size:13px;">
                                <div class="row">
                                    <div class="col-md-2 col-lg-2">
                                        <div class='comment-avatar mr-2' style="background:#52514f;vertical-align:middle;width:6em;height:6em;">
                                            <a target="_blank" href="${_taoh_site_url_root + '/profile/' + l.ptoken}">
                                                <img width="40" class="lazy" src="${avatarSrc}" alt="avatar">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-10 col-lg-10">
                                        <div class="entries-title">
                                            <div>
                                                <h5>
                                                    <a class="text-primary text-capitalize" target="_blank" href="${_taoh_site_url_root + '/profile/' + l.ptoken}">${l.chat_name}</a>
                                                    ${(l.type) ? ` &nbsp; <span class="badge user-type text-capitalize">${l.type ? l.type : 'professional'}</span>` : ''}
                                                </h5>
                                                <div class="location text-muted">${l.full_location ? '<i class="la la-map-marker mr-2"></i>' + l.full_location : ''}</div>
                                                <div class="text-muted">${(companies && companies.length) ? generateCompanyHTML(companies) : ''}</div>
                                                <div class="text-muted">${(roles && roles.length) ? generateRoleHTML(roles) : ''}</div>
                                                <div class="d-none">
                                                    <div class="text-muted d-flex align-items-center mb-2" style="line-height: 1.2;">
                                                        <svg class="mr-2" width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5.77778 0H7.22222C7.6217 0 7.94444 0.335156 7.94444 0.75V2.25C7.94444 2.66484 7.6217 3 7.22222 3H5.77778C5.3783 3 5.05556 2.66484 5.05556 2.25V0.75C5.05556 0.335156 5.3783 0 5.77778 0ZM1.44444 1.5H4.33333V2.625C4.33333 3.24609 4.81858 3.75 5.41667 3.75H7.58333C8.18142 3.75 8.66667 3.24609 8.66667 2.625V1.5H11.5556C12.3523 1.5 13 2.17266 13 3V10.5C13 11.3273 12.3523 12 11.5556 12H1.44444C0.647743 12 0 11.3273 0 10.5V3C0 2.17266 0.647743 1.5 1.44444 1.5ZM3.97222 10.2492C3.97222 10.3875 4.08056 10.5 4.21372 10.5H8.78628C8.91944 10.5 9.02778 10.3875 9.02778 10.2492C9.02778 9.55781 8.48837 9 7.82483 9H5.17517C4.50938 9 3.97222 9.56016 3.97222 10.2492ZM6.5 8.25C6.88309 8.25 7.25049 8.09196 7.52138 7.81066C7.79226 7.52936 7.94444 7.14782 7.94444 6.75C7.94444 6.35218 7.79226 5.97064 7.52138 5.68934C7.25049 5.40804 6.88309 5.25 6.5 5.25C6.11691 5.25 5.74951 5.40804 5.47862 5.68934C5.20774 5.97064 5.05556 6.35218 5.05556 6.75C5.05556 7.14782 5.20774 7.52936 5.47862 7.81066C5.74951 8.09196 6.11691 8.25 6.5 8.25Z" fill="#444444"/>
                                                        </svg>
                                                        Senior software engineer at TamQ Analytics
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="btn-group mr-4" role="group" style="margin-top:40px">
                                                  ${chatButton}
                                                  ${chatButton_blank}
                                                </div>
                                            </div>
                                            ${badge_display}

                                        </div>
                                        <p class="live_status" title='${userMoodStatus}'>${userMoodStatus}</p>
                                        <p class="card-text skill text-capitalize">${skillContent}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                                left_htmlcontent += `
                        <div class="network_entries ${ntw_entries_cls} ${l.ptoken === chatwith ? 'active' : ''} entry_${totalentries} card card-item p-1" style="margin-bottom:10px;">
                            <div class="row">
                                <div class="col-2">
                                    <div class="pre-avatar-img-block">
                                        <div class="text-uppercase comment-avatar mr-2" style="content:attr(data-letters);display:inline-block;font-size:1em;width:2.5em;height:2.5em;line-height:2.5em;text-align:center;border-radius:100%;vertical-align:middle;margin-right:1em;color:white;">
                                            <a target="_blank" href="${_taoh_site_url_root + '/profile/' + l.ptoken}">
                                                <img width="40" class="lazy" src="${avatarSrc}" alt="avatar">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-10">
                                    <span class="chat_name">
                                        <h6><a target="_blank" class="text-primary text-capitalize" href="${_taoh_site_url_root + '/profile/' + l.ptoken}">${l.chat_name}</a></h6>
                                    </span>
                                    <span>${chatButton}</span>
                                    <p class="live_status" title="${userMoodStatus}">${userMoodStatus}</p>
                                </div>
                            </div>
                        </div>`;
                            }
                        }
                    }
                }

                slot.empty();
                slot.append(htmlcontent);

                // Left sidebar builder
                entriesList.empty();
                entriesList.append(left_htmlcontent);

                if (!totalentries) {
                    show_empty_network_entries_screen(slot);
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




        $(document).on('click', '.gobackwindow', function () {
            chatwith = '';
            chat_window = 0;
            ntwChatLastTime = 0;
            $(".open_window").hide();
            $(".listwindow").show();

            if (userInfoTimeout) clearTimeout(userInfoTimeout);
            if (ntwChatDataInterval) clearInterval(ntwChatDataInterval);
            if (ntwChatDataFromServerInterval) clearInterval(ntwChatDataFromServerInterval);
        });

        function taoh_add_video_chat(type=1) {
            if (ntw_room_key.trim() != '' && chatwith.trim() != '') {
                $('.ntw_video').removeClass('la-video').addClass('la-spinner la-spin');

                // getRoomInfo(ntw_room_key, my_pToken).then((room_info) => {
                let data = {
                    'taoh_action': 'taoh_add_video_chat',
                    'my_token': my_pToken,
                    'guest_token': chatwith,
                    'network_title': '<?php echo $club_info['title'] ?? 'Networking Chat'; ?>'
                };

                let confirmMsg;
                
                if(type == 1){
                    if (chatname != '') {
                         confirmMsg = 'Please confirm that you want to start a video chat with ' + chatname + ' ?';
                    } else {
                        confirmMsg = 'Please confirm that you want to start a video chat?';
                    }
                }else{
                  
                    confirmMsg = 'Please open the Google Meet in a new tab, copy and paste the link in the chat box.';
                    
                }
                
                if(type == 2){
                    /*let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + chatname + '\'s meeting</p><a href="https://meet.google.com/new" target="_blank" class="chat-meeting-link"> Join Google meeting</a></div></div>';
                    commentInput.val(video_chat_link);
                    sendChat.trigger('click');*/
                    $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');

                    $.confirm({
                        title: 'Confirmation!',
                        content: confirmMsg,
                        type: 'warning',
                        buttons: {
                            cancel: function () {
                                $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');
                            },
                            confirm: {
                                text: 'Yes',
                                action: function () {
                                    window.open('https://meet.google.com/new');
                                }
                            }
                        }
                        
                    });
                }else{
                    $.confirm({
                        title: 'Confirmation!',
                        content: confirmMsg,
                        type: 'warning',
                        buttons: {
                            cancel: function () {
                                $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');
                            },
                            confirm: {
                                text: 'Yes',
                                action: function () {
                                    $.post(_taoh_site_ajax_url, data, function (response) {
                                        let video_chat_link = 'Want to chat? <div class="chat-meeting"><i class="la la-video"></i><div><p>' + chatname + '\'s meeting</p><a href="' + response.other_link + '" target="_blank" class="chat-meeting-link"> Join video meeting</a></div></div>';
                                        commentInput.val(video_chat_link);
                                        sendChat.trigger('click');
                                        $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');
                                        if (response.my_link) window.open(response.my_link);
                                    }).fail(function () {
                                        $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');
                                    });
                                }
                            }
                        }
                    });
                 }
                // }).catch((e) => {
                //     console.log(e);
                //     $('.ntw_video').removeClass('la-spinner la-spin').addClass('la-video');
                // });

            } else {
                taoh_set_error_message('Please select a user to chat with.', false);
            }
        }

        function update_mood_status(mood_status){
            const default_emoji = 'default';
            let [statusText, emoji] = mood_status ? mood_status.split('###') : ['', default_emoji];
            if (!emoji) emoji = default_emoji;
            $('#loadEmojiImg').attr('src', _taoh_site_url_root + '/assets/images/emojis/' + emoji + '.svg');
            $('#my_status').val(statusText.trim());
        }

        function taoh_network_update_online() {
            var data = {
                'taoh_action': 'taoh_network_update_online',
                'keyslug': ntw_room_key,
                'ptoken': my_pToken,
                'geo_enable': "<?php echo $club_info['geo_enable'] || 0; ?>",
            };

            if(data.geo_enable == 1){
                data.latitude = "<?php echo $lat ?? ''; ?>";
                data.longitude = "<?php echo $long ?? ''; ?>";
            }

            $.post(_taoh_site_ajax_url, data, function (response) {

            }).fail(function () {
                loader(false, loaderArea);
                console.log("Network update online failed!");
            });
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
                'keyslug': ntw_room_key
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

        function fetchNTWInvites() {
            let my_invites_key = 'invites_' + my_pToken;

            IntaoDB.getItem(objStores.ntw_store.name, my_invites_key).then((intao_data) => {
                // Check if data is expired (expires after 2 week ((14 * 24) * 60 * 60 * 1000))
                if (intao_data && intao_data.timestamp && !((Date.now() - intao_data.timestamp) > 1209600000)) {
                    let roomInvitesAll = intao_data.values;
                    renderNTWInvites(roomInvitesAll);
                } else {
                    if (intao_data) IntaoDB.removeItem(objStores.ntw_store.name, my_invites_key);
                }
            });
        }

        async function getNTWInvitesHtml(invites) {
            let inviteHtml = '';
            let inviteCount = 0;
            let inviteUnReadCount = 0;

            for (const [k, invite] of Object.entries(invites)) {
                if (invite.room_hash == ntw_room_key) {
                    const userInfo = await getUserInfo(invite.invite_from);
                    let new_class_side = 'room_' + invite.invite_from + '_' + invite.room_hash;

                    if(!invite.read && invite.invite_from == chatwith) {
                        invite.read = 1;
                        updateInvitesReadStatus(invite.room_hash, my_pToken, chatwith, k);
                    }

                    const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                        ? userInfo.avatar_image
                        : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                    // let chat_now = `<button type="button" class="tag-link tag-link-blue openchatacc" data-item="${k}" data-read="${(!invite.read) ? 0 : 1}" data-chatwith="${invite.invite_from}" data-chatname="${userInfo.chat_name}" style="margin-top:8px; margin-right: 5px;cursor:pointer;">Chat now</button>`;
                    let chat_now = `<button type="button" class="btn btn-sm openchatacc invites-btn" data-item="${k}" data-read="${(!invite.read) ? 0 : 1}" data-chatwith="${invite.invite_from}" data-chatname="${userInfo.chat_name}" style="white-space: nowrap;font-size: small;border: 1px solid #c3c3c3;margin-top:8px;cursor:pointer;">Chat <i class="la la-angle-double-right"></i></button>`;

                    inviteHtml += `<div class="${new_class_side} card card-item invites-item p-1" style="margin-bottom:20px;${(!invite.read) ? 'background: rgba(50, 205, 50, 0.3);' : ''}">
                    <div class="row">
                        <div class="col-2">
                            <div class="pre-avatar-img-block">
                                <div class="text-uppercase comment-avatar ml-1">
                                    <a target="_blank" href="${_taoh_site_url_root + '/profile/' + invite.invite_from}">
                                        <img width="40" class="lazy" src="${userAvatarSrc}" alt="avatar">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-10">
                            <span class="chat_name">
                                <h6><a target="_blank" class="text-primary text-capitalize" href="${_taoh_site_url_root + '/profile/' + invite.invite_from}">${userInfo.chat_name}</a></h6>
                            </span>
                            <span>${chat_now}</span>
                        </div>
                    </div>
                </div>`;

                    if(!invite.read) {
                        inviteUnReadCount++;
                    }

                    inviteCount++;
                }
            }

            return {inviteHtml, inviteCount, inviteUnReadCount};
        }

        async function renderNTWInvites(invites) {
            if (Object.keys(invites).length > 0) {
                let {inviteHtml, inviteCount, inviteUnReadCount} = await getNTWInvitesHtml(invites);
                if (inviteUnReadCount > 0 && document.hidden) {
                    startTitleBlinking(htmlDecode('&#9993; |'));
                }
                if (inviteCount > 0) {
                    $('#no_previous_chat').remove();
                    activeRoomList.html(inviteHtml);
                } else {
                    activeRoomList.html('<span id="no_previous_chat">No Invites Received!</span>');
                }
            } else {
                activeRoomList.html('<span id="no_previous_chat">No Invites Received!</span>');
            }
        }

        function taoh_ntw_post_metrics(metrics) {
            if (ntw_room_key.trim() !== '' && my_pToken.trim() !== '') {
                save_metrics('networking',metrics,ntw_room_key);                
            }
        }

        function clearBtn(type) {
            if (type == "search") {
                search = '';
                $('#searchQuery').val('');
            }
            if (type == "geohash") {
                geohash = '';
                $('.ts-control div.item').remove();
            }
            networkArea.empty();
        }

        function mover(elem, offset = 0) {
            $('html, body').animate({
                scrollTop: parseInt(elem.offset().top - offset)
            }, 1000);
        }

        function loader(status, area, width = 20) {
            if (status === true) {
                $(area).empty().append(`<img src="<?php echo TAOH_LOADER_GIF; ?>" alt="taoh-loader" id="loaderEmail" width="${width}">`);
            } else {
                $(area).empty();
            }
        }

        function showEmoji() {
            $('#emoji_section').show();
        }

        function removeEmoji() {
            $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_site_url_root + '/assets/images/emojis/default.svg'}" alt="emoji">`);
            $('#choosen_emoji').val('');
        }

        function chooseEmoji(id) {
            $('#selected-emoji').html(`<img class="emoji-place" src="${_taoh_site_url_root + '/assets/images/emojis/' + id + '.svg'}" alt="emoji">`);
            $('#choosen_emoji').val(id);
        }

        $(document).on("click", function (a) {
            if (!$(a.target).closest('#emoji_section').length) {
                $("#emoji_section").hide();
            }
            if (a.target.className == 'emoji-place') {
                $("#emoji_section").show();
            }
        });

        function validateEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

        function invitea_friend() {
            $("#invite_formdiv").show();
            $('#invite_form input').val('');
            $('#invite_form .error_msg').html('');
            $("#invite_formdiv input").removeClass("error_class");
        }

        /**********************====================== /Networking Functions ===========================**********************/


        /**********************====================== Forum Functions ===========================**********************/
        function sendFRMChat(message, my_pToken, parent_id, frm_room_key, user_type = 'user', is_default = 0) {
            if (frm_room_key.trim() === '') {
                alert('Invalid room.');
                return false;
            }

            if (message.trim() === '') {
                alert('Message seems empty! Please enter valid message to send.');
                return false;
            }

            let isReplyRequest = Boolean(parseInt(parent_id));

            if (user_type !== 'system') {
                if (isReplyRequest) {
                    let frm_ReplycommentInput_emojiInstance = frm_ReplycommentInput.data("emojioneArea");
                    if (frm_ReplycommentInput_emojiInstance) {
                        frm_ReplycommentInput_emojiInstance.setText('');
                    } else {
                        frm_ReplycommentInput.val('');
                    }
                } else {
                    let frm_commentInput_emojiInstance = frm_commentInput.data("emojioneArea");
                    if (frm_commentInput_emojiInstance) {
                        frm_commentInput_emojiInstance.setText('');
                    } else {
                        frm_commentInput.val('');
                    }
                }
            }

            let sent_time = new Date().getTime();

            let data = {
                'taoh_action': 'taoh_forum_send_message',
                'message': message,
                'ptoken': my_pToken,
                'other_ptoken': '',
                'parent_id': parent_id,
                'user_type': user_type,
                'key': frm_room_key,
                'sent_time': sent_time
            };


            let chatresponse = {
                "chat": [{
                    "ptoken": data.ptoken,
                    "message": data.message,
                    "to_ptoken": data.other_ptoken,
                    'parent_id': data.parent_id,
                    'reply_count': 0,
                    "user_type": data.user_type,
                    "room_hash": data.key,
                    "sent_time": data.sent_time,
                    "time": (data.sent_time * 1000)
                }],
                "isTempMsg": true,
                "overallChatCount": 1,
                "recent_rendered_items": {}
            };

            // Commentted below to skip temp message rendering for forum to avoid duplicate topics
            // isReplyRequest ? renderFRMReplyMessages(chatresponse) : renderFRMMessages(chatresponse);

            let chat_temp_messages_key = Boolean(parseInt(data.parent_id))
                ? 'frm_temp_' + frm_room_key + '_' + data.parent_id : 'frm_temp_' + frm_room_key;
            // let ptokenTo = data.other_ptoken;

            // Store temp messages in Indexed DB then send to server
            IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                let updatedResponse = {};
                if (intao_data?.values) {
                    updatedResponse = intao_data.values;
                }
                // if (!(ptokenTo in updatedResponse)) updatedResponse[ptokenTo] = {};
                Object.assign(updatedResponse, {[data.sent_time]: chatresponse.chat[0]});
                IntaoDB.setItem(objStores.ntw_store.name, {
                    taoh_ntw: chat_temp_messages_key,
                    values: updatedResponse,
                    timestamp: Date.now()
                });
            }).then(() => {
                if (navigator.onLine) {
                    const frm_comment_send_btn = $('#frm_comment_send_btn');
                    if (!isReplyRequest) {
                        frm_comment_send_btn.prop('disabled', true);
                        frm_comment_send_btn.find('i').removeClass('fa-comments').addClass('fa-circle-o-notch fa-spin');
                    }

                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'post',
                        data: data,
                        dataType: 'json',
                        success: function (response) {
                            if(!isReplyRequest && !response.success && response.output === 'topic_already_exist') {
                                // Delete temp message from Indexed DB
                                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key)
                                    .then((intao_data) => {
                                        if (!intao_data?.values || !intao_data.values[data.sent_time]) {
                                            return; // Exit early if no relevant data
                                        }

                                        // Clone existing values and remove the target entry
                                        const updatedResponse = { ...intao_data.values };
                                        delete updatedResponse[data.sent_time];

                                        // Update IndexedDB with the modified data
                                        IntaoDB.setItem(objStores.ntw_store.name, {
                                            taoh_ntw: chat_temp_messages_key,
                                            values: updatedResponse,
                                            timestamp: Date.now()
                                        });
                                    });

                                if (!is_default) alert('It seems to be the same topic already exists. Please try with a different topic.');
                            }

                            if(response.success){
                                ft_frm_reFetchRequired = true;
                                frm_update_stored_time_in_temp_messages(chat_temp_messages_key, response);
                            }

                            if (!isReplyRequest) {
                                frm_comment_send_btn.find('i').removeClass('fa-circle-o-notch fa-spin').addClass('fa-comments');
                                frm_comment_send_btn.prop('disabled', false);
                                $('#start-discussion').modal('hide');
                            }

                            // taoh_frm_post_metrics('chatpost');
                        },
                        error: function (xhr, status, error) {
                            console.log('Error:', xhr.status);
                            if (!isReplyRequest) {
                                frm_comment_send_btn.find('i').removeClass('fa-circle-o-notch fa-spin').addClass('fa-comments');
                                frm_comment_send_btn.prop('disabled', false);
                            }
                            // checkOfflineMessage = 1;
                            // if (typeof syncOfflineMessages === 'function') {
                            //     syncOfflineMessages();
                            // }
                        }
                    });
                } else {
                    if (!isReplyRequest) {
                        frm_comment_send_btn.find('i').removeClass('fa-circle-o-notch fa-spin').addClass('fa-comments');
                        frm_comment_send_btn.prop('disabled', false);
                    }
                    // checkOfflineMessage = 1;
                    // if (typeof syncOfflineMessages === 'function') {
                    //     syncOfflineMessages();
                    // }
                }
            });
        }

        function frm_update_stored_time_in_temp_messages(chat_temp_messages_key, returnedData) {
            if (returnedData.success) {
                // Update stored_time in temp messages
                IntaoDB.getItem(objStores.ntw_store.name, chat_temp_messages_key).then((intao_data) => {
                    if (intao_data?.values) {
                        let updatedResponse = intao_data.values;
                        if (returnedData.sent_time in updatedResponse) {
                            updatedResponse[returnedData.sent_time].stored_time = returnedData.stored_time;
                            IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_temp_messages_key, values: updatedResponse, timestamp: Date.now()});
                        }
                    }
                });
            }
        }

        function getFRMChatRequestData(pToken_from, parent_id, callFromEvent = 'init') {
            const isReplyRequest = Boolean(parseInt(parent_id));

            return {
                pToken_from,
                pToken_to: '',
                parent_id,
                page: isReplyRequest ? frmReplyChatPageNo : frmChatPageNo,
                itemPerPage: isReplyRequest ? frmReplyChatItemsPerPage : frmChatItemsPerPage,
                callFromEvent
            };
        }


        /* Forum Parent methods */

        function fetchFRMChatData(requestData, serverFetch = false) {
            if (!requestData.pToken_from) return;

            const chat_messages_key = `frm_${frm_room_key}`;

            IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
                .then((intao_data) => {
                    if (frm_isProcessing || !intao_data) {
                        setTimeout(() => fetchFRMChatData(requestData, serverFetch), 2000);
                        return;
                    }

                    frm_isProcessing = true;

                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);

                    processFRMChatData(
                        requestData,
                        intao_data.timestamp ? intao_data.values : {
                            "chat": {},
                            "last_update_time": lastCheckedTimestamp,
                            "success": true
                        }
                    );
                })
                .catch((error) => {
                    console.error("Error fetching FRMChatData:", error);
                });
        }

        function processFRMChatData(requestData, response) {
            let processedResponse = {};
            let recentItemsObject = {};
            let recentRenderedItemsObject = {};
            let totalchats = 0;

            if (response.success) {
                let chats = response.chat ? response.chat : [];

                let allChatKeys = Object.keys(chats);
                totalchats = allChatKeys.length;
                if (totalchats > 0) {
                    /* // Scroll Up Event Code

                    if (requestData.callFromEvent == "init" || frm_msgDownIndex == 0) {
                        const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                        if (slice_end > 0) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "scrollup") {
                        let currentFirstMsgIndex = allChatKeys.indexOf(frm_msgUpIndex);
                        if (currentFirstMsgIndex > -1) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "interval") {
                        let currentLastMsgIndex = allChatKeys.indexOf(frm_msgDownIndex);
                        if (currentLastMsgIndex > -1 && currentLastMsgIndex < (totalchats - 1)) {
                            const recentItems = Object.entries(chats).slice(-(totalchats - (currentLastMsgIndex + 1)), totalchats);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }

                        const recentRenderedItems = Object.entries(chats).slice(-20);
                        recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
                    }*/

                    if (requestData.callFromEvent == "init" || frm_msgUpIndex == 0) {
                        const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                        if (slice_end > 0) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "scrolldown") {
                        let currentFirstMsgIndex = allChatKeys.indexOf(frm_msgDownIndex);
                        if (currentFirstMsgIndex > -1) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "interval") {
                        let currentLastMsgIndex = allChatKeys.indexOf(frm_msgUpIndex);
                        if (currentLastMsgIndex > -1 && currentLastMsgIndex < (totalchats - 1)) {
                            const recentItems = Object.entries(chats).slice(-(totalchats - (currentLastMsgIndex + 1)), totalchats);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }

                        const recentRenderedItems = Object.entries(chats).slice(-20);
                        recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
                    }
                }
            }

            processedResponse.isTempMsg = false;
            processedResponse.callFromEvent = requestData.callFromEvent;
            processedResponse.chat = recentItemsObject;
            processedResponse.overallChatCount = totalchats;
            processedResponse.recent_rendered_items = recentRenderedItemsObject;
            processedResponse.last_update_time = response.last_update_time;

            renderFRMMessages(processedResponse);
        }

        async function compiledFRMMsgHtml(cd) {
            let compiledMMMsgHtml;
            let safeMessageHtml = '';
            const message_content = cd.message;
            if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
                safeMessageHtml = message_content;
            } else {
                const safeMessage = document.createElement('pre');
                safeMessage.textContent = message_content;
                safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }

            if (cd.userType === 'system') {
                compiledMMMsgHtml = `<div class="badge-system clearfix"><span class="badge badge-secondary">${safeMessageHtml}</span></div>`;
            } else {
                const chatUrl = new URL(window.location.href);
                const chatUrlParams = new URLSearchParams({chatwith: cd.ptokenTo});
                chatUrl.search = chatUrlParams.toString();

                compiledMMMsgHtml = `<div class="post-card ${cd.ptokenTo === cd.ptokenFrom ? 'mine' : 'others'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-frm_message_id="${cd.message_id}" data-frm_message_key="${cd.time}" id="${'msg_' + (cd.time)}">
                <div class="user-info justify-content-between">
                    <div class="post-card-header-left d-flex">
                        <img src="${cd.avatar}" alt="User Image">
                        <div class="author">
                            <div class="user-name">${cd.name}</div>
                            <div class="text-muted"><small>${cd.formatted_time}</small></div>
                        </div>
                    </div>
                    <div class="post-card-header-right d-flex">
                        <div class="post-card-header-loader">${cd.isTempMsg ? '<i class="fa fa-circle-o-notch fa-spin"></i>' : ''}</div>
                        <div class="dropleft">
                            <div class="pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-h fs-24" aria-hidden="true"></i>
                            </div>
                            <div class="dropdown-menu">
                                ${cd.ptokenTo === cd.ptokenFrom ? '<div class="dropdown-item frm_comment_delete">Delete</div>' : `<a class="dropdown-item" href="${chatUrl.toString()}" target="_blank">Chat</a>`}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-content">
                    <div>${safeMessageHtml}</div>
                </div>
                <div class="d-none">
                    <div class="mt-2 d-flex align-items" style="gap: 6px;">
                        <button type="button" class="btn ans-poll-btn" data-toggle="modal" data-target="#ans-poll">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.28571 1.28571C4.28571 0.575893 4.86161 0 5.57143 0H6.42857C7.13839 0 7.71429 0.575893 7.71429 1.28571V10.7143C7.71429 11.4241 7.13839 12 6.42857 12H5.57143C4.86161 12 4.28571 11.4241 4.28571 10.7143V1.28571ZM0 6.42857C0 5.71875 0.575893 5.14286 1.28571 5.14286H2.14286C2.85268 5.14286 3.42857 5.71875 3.42857 6.42857V10.7143C3.42857 11.4241 2.85268 12 2.14286 12H1.28571C0.575893 12 0 11.4241 0 10.7143V6.42857ZM9.85714 1.71429H10.7143C11.4241 1.71429 12 2.29018 12 3V10.7143C12 11.4241 11.4241 12 10.7143 12H9.85714C9.14732 12 8.57143 11.4241 8.57143 10.7143V3C8.57143 2.29018 9.14732 1.71429 9.85714 1.71429Z" fill="white"/>
                            </svg>
                            <span>Answer the Poll</span>
                        </button>
                        <span class="poll-counts">15 Votes</span>
                    </div>
                </div>

                
                <div class="post-icons mt-2">
                    <div class="icon">
                        <!--<i class="fa fa-heart-o"></i> <span>1k</span>-->
                        <span class="frm_reply_comment_btn">
                            <i class="fa fa-comment ml-3"></i> Reply
                            <span style="display:${cd.reply_count !=0 ? '' : 'none'}" class="badge badge-pill badge-secondary frm_reply_comment_cnt">
                            ${cd.reply_count}</span>
                        </span>
                    </div>
                    <div class="action-icons">
                        <!-- <i class="fa fa-share-alt"></i>-->
                    </div>
                </div>
            </div>`;
            }

            return compiledMMMsgHtml;
        }

        async function getFRMChatMsgHtml(response, tempMsgList) {
            let chats = response.chat || {};
            let isTempMsg = response.isTempMsg || false;

            let messageHtml = '';
            // let ptokenTo = chatwith;
            let do_highlight = false;
            let compiledChatKeys = { chats: [], temp_chats: [] };

            let lastChatItemKey = Object.keys(chats).pop();
            for (const [key, v] of Object.entries(chats)) {
                const userInfo = await getUserInfo(v.ptoken);
                let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

                const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                    ? userInfo.avatar_image
                    : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                if (v.ptoken == my_pToken && (response.callFromEvent !== 'scrolldown' || isTempMsg)) {
                    frm_my_recent_message_timestamp = v.time / 1000;
                }

                if (!isTempMsg) {
                    const messageId = parseInt(v.message_id);
                    const parentId = parseInt(v.parent_id);
                    const replyCount = parseInt(v.reply_count) || 0;
                    if (!parentId && messageId) {
                        frm_replyCountMap.set(messageId, {reply_count: replyCount, key});
                    }
                }

                if (v.ptoken != my_pToken) {
                    if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrolldown') do_highlight = true;
                    if (response.callFromEvent !== 'init' && response.callFromEvent !== 'scrolldown') frm_newMessagesCnt++;
                }

                let chatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);
                if (chatDateTime_arr) {
                    if (response.callFromEvent === 'scrolldown' && key === lastChatItemKey) {
                        removeSameBadge(chatDateTime_arr[0]);
                    }

                    if (response.callFromEvent === 'scrolldown' && chatDateTime_arr[0] !== frm_chatBadgeUpDate) {
                        frm_chatBadgeUpDate = chatDateTime_arr[0];
                        messageHtml += '<div class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + frm_chatBadgeUpDate + '</span></div>';
                    } else if (response.callFromEvent !== 'scrolldown' && chatDateTime_arr[0] !== frm_chatBadgeDownDate) {
                        frm_chatBadgeDownDate = chatDateTime_arr[0];
                        messageHtml += '<div class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + frm_chatBadgeDownDate + '</span></div>';
                    }
                }

                let compileData = {
                    ptokenTo: v.ptoken,
                    message: v.message,
                    message_id: v.message_id,
                    is_parent: v.parent_id == 0,
                    time: v.time,
                    name: userInfo.chat_name,
                    avatar: userAvatarSrc,
                    ptokenFrom: my_pToken,
                    userType: v.user_type,
                    isTempMsg: isTempMsg,
                    stored_time: stored_time,
                    formatted_time: chatDateTime_arr[1] ?? ''
                };
                if(compileData.is_parent){
                    compileData.reply_count = v.reply_count;
                }
                messageHtml += await compiledFRMMsgHtml(compileData);

                compiledChatKeys.chats.push(isTempMsg ? (v.time).toString() : key);

                const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                if (tempMsgElem.length) tempMsgElem.removeClass('new');
            }

            let skipTempMsg = true; // Skip temp messages for forum chat to avoid duplicate topic
            if (!skipTempMsg && !isTempMsg && typeof tempMsgList != 'undefined' && response.callFromEvent !== 'scrolldown') {
                let allSentTimes = Object.keys(chats).map(k => chats[k]['sent_time']);
                for (const [key, v] of Object.entries(tempMsgList)) {
                    const userInfo = await getUserInfo(v.ptoken);
                    let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';
                    let tempChatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);

                    const tempUserAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                        ? userInfo.avatar_image
                        : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                    if (v.ptoken == my_pToken && response.callFromEvent !== 'scrolldown') {
                        frm_my_recent_message_timestamp = v.time / 1000;
                    }

                    if (!allSentTimes.includes((v.sent_time).toString())) {
                        let compileData = {
                            ptokenTo: v.ptoken,
                            message: v.message,
                            message_id: '',
                            is_parent: v.parent_id == 0,
                            time: v.time,
                            name: userInfo.chat_name,
                            avatar: tempUserAvatarSrc,
                            ptokenFrom: my_pToken,
                            userType: v.user_type,
                            isTempMsg: true,
                            stored_time: stored_time,
                            formatted_time: tempChatDateTime_arr[1] ?? ''
                        };
                        if(compileData.is_parent){
                            compileData.reply_count = v.reply_count;
                        }
                        messageHtml += await compiledFRMMsgHtml(compileData);

                        compiledChatKeys.temp_chats.push(key);
                    }

                    const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.removeClass('new');
                }
            }

            return { messageHtml, compiledChatKeys, do_highlight };
        }

        function renderFRMMessages(response) {
            if (!response) {
                doAfterFRMMsgRender(response);
                return;
            }

            // Reverse the keys and assign back to response.chat
            response.chat = Object.fromEntries(
                Object.entries(response.chat).reverse()
            );

            let chats = response.chat || {};
            let allChatKeys = Object.keys(chats);

            let chatTempMessagesKey = 'frm_temp_' + frm_room_key;
            IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
                .then(intao_data => intao_data?.values || {})
                .then(tempMsgList => {
                    return getFRMChatMsgHtml(response, tempMsgList);
                })
                .then(({ messageHtml, compiledChatKeys, do_highlight }) => {
                    if (frmChatFirstCall === 1 || response.callFromEvent === 'init') {
                        frm_comments_list.empty();
                    }

                    $("#frm_comments_list .temp_msg:not(.new)").remove();

                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        $('#frm_no_message').remove();
                        // $('#message_helper').hide();

                        if (response.callFromEvent === 'scrolldown') {
                            frm_comments_list.append(messageHtml);
                        } else {
                            frm_comments_list.prepend(messageHtml);
                            if (isScrolledUp(frm_chatContainer) && frmChatFirstCall !== 1) {
                                if (frm_newMessagesCnt > 0) {
                                    // newmessages_btn.find('span').text(frm_newMessagesCnt + ' new messages');
                                    // newmessages_btn_grp.show();
                                }
                            } else {
                                if (frm_newMessagesCnt > 0) {
                                    frm_chatContainer.scrollTop = frm_chatContainer.scrollHeight;
                                    frm_newMessagesCnt = 0;
                                }
                            }
                            checkCommentFormTime();
                        }
                    }

                    if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                        /* // Scroll Up Event Code
                        if (response.callFromEvent === 'init') {
                            frm_msgUpIndex = allChatKeys[0];
                            frm_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrollup') {
                            frm_msgUpIndex = allChatKeys[0];
                        } else if (response.callFromEvent === 'interval') {
                            frm_msgDownIndex = allChatKeys.pop();
                        }*/

                        if (response.callFromEvent === 'init') {
                            frm_msgUpIndex = allChatKeys[0];
                            frm_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrolldown') {
                            frm_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'interval') {
                            frm_msgUpIndex = allChatKeys[0];
                        }
                    }

                    frmChatFirstCall = 0;
                    response.compiledChatKeys = compiledChatKeys;

                    // Clearing left over temp messages if already rendered
                    if ($('#frm_comments_list .temp_msg').length) {
                        let recentRenderedItems = response.recent_rendered_items;
                        for (const [k, value] of Object.entries(recentRenderedItems)) {
                            let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                            if (tempMsgElem.length) tempMsgElem.remove();
                        }
                    }

                    if (do_highlight) highlightMessages();
                })
                .then(() => {
                    doAfterFRMMsgRender(response);
                });

            /* // Scroll Up Event Code
            if (response.callFromEvent === "scrollup") {
                frm_msgScrollUpEnded = false;
            }*/

            if (response.callFromEvent === "scrolldown") {
                frm_msgScrollDownEnded = false;
            }
        }

        function doAfterFRMMsgRender(response) {
            let overallChatCountWithTemp = response.overallChatCount;
            if (typeof response.compiledChatKeys != 'undefined') {
                overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
            }
            updateStickyBadgeTxt('#frm_comments_list', '#frm_stickyBadge');

            if (overallChatCountWithTemp > 0) {
                // chatCount.text(overallChatCountWithTemp);
                // $('#message_helper').hide();
                // $('#message_count').show();
            } else {
                frm_msgUpIndex = 0;
                frm_msgDownIndex = 0;
                // $('#message_count').hide();
                // if (chatwith_liveStatus == 1) $('#message_helper').show();
                frm_comments_list.html(`<div id="frm_no_message" class="col-lg-12" style="text-align: center;">
                    <img class="no-network-place" src="${_taoh_site_url_root + '/assets/images/empty_network.png'}" width="300" alt="no-network">
                </div>`);
            }

            if (response.hasOwnProperty('last_update_time')) {
                frmChatLastTime = response.last_update_time || 0;
            }

            updateFRMChatSendArea(frm_room_key);

            // $("#ow_header").show();
            // $("#ow_footer").show();

            frm_isProcessing = false;
            taoh_Loader($('#frm_chat_loader'), false);
            loader(false, chatloaderArea);
        }

        function updateFRMChatSendArea(frm_room_key) {
            if (frm_room_key.trim() !== '') {
                // if (chatwith_liveStatus == 1) {
                //     $('#message-area').hide();
                //     $('#chat-area').show();
                //     $('.frm_video').show();
                //     if (!$('.sender_part').is(":visible")) {
                //         $('.sender_part').show();
                //     }
                // } else {
                //     $('#chat-area').hide();
                //     $('.sender_part').hide();
                //     $('#message_helper').hide();
                //     $('.frm_video').hide();
                //     $('#message-area').show();
                // }
                updateSenderArea = true;
            }
        }

        /* // Scroll Up Event Code
        let frm_prevScrollPos = frm_chatContainer.scrollTop;
        frm_chatContainer.addEventListener('scroll', () => {
            const frm_currentScrollPos = frm_chatContainer.scrollTop;

            if ((frm_currentScrollPos < frm_prevScrollPos) && frm_currentScrollPos < 10 && !frm_msgScrollUpEnded) {
                // Trigger only if the user scrolls up to the top of the chat container
                frmChatPageNo++;

                taohLoader(document.getElementById('frm_chat_loader'), true);
                let requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchFRMChatData(requestData, false), (frm_isProcessing ? 500 : 100));
            }

            updateStickyBadgeTxt('#frm_comments_list', '#frm_stickyBadge');

            // Update the previous scroll position to find the direction of scrolling
            frm_prevScrollPos = frm_currentScrollPos;
        });*/

        let frm_prevScrollPos = frm_chatContainer.scrollTop;
        frm_chatContainer.addEventListener('scroll', () => {
            const frm_currentScrollPos = frm_chatContainer.scrollTop;

            if ((frm_currentScrollPos > frm_prevScrollPos) &&
                (frm_chatContainer.scrollHeight - frm_chatContainer.scrollTop - frm_chatContainer.clientHeight < 10) &&
                !frm_msgScrollDownEnded) {
                // Trigger only if the user scrolls down to the bottom of the chat container
                frmChatPageNo++;

                taohLoader(document.getElementById('frm_chat_loader'), true);
                let requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrolldown');
                throttle(fetchFRMChatData(requestData, false), (frm_isProcessing ? 500 : 100));
            }

            updateStickyBadgeTxt('#frm_comments_list', '#frm_stickyBadge');

            // Update the previous scroll position to find the direction of scrolling
            frm_prevScrollPos = frm_currentScrollPos;
        });

        function initFRMChatDataInterval() {
            if (frmChatDataInterval) clearInterval(frmChatDataInterval);
            frmChatDataInterval = setInterval(function () {
                if (!frm_isProcessing) {
                    if (my_pToken.trim() !== '') {
                        let requestData = getFRMChatRequestData(my_pToken, 0, 'interval');
                        fetchFRMChatData(requestData, false);
                    }
                }
            }, 3000);
        }

        async function updateForumWindow() {

            // $('#networkingTab [data-target="#conversation"]').tab('show');
            frm_comments_list.empty();

            taoh_Loader($('#frm_chat_loader'), true);

            if (frmChatDataInterval) clearInterval(frmChatDataInterval);

            // userLiveStatusInterval = 3000;
            // if (userLiveIntervalId) clearInterval(userLiveIntervalId);

            // chat_window = 1;
            frmChatFirstCall = 1;
            frmChatLastTime = 0;
            // frm_msgNewEntriesCnt = 0;
            frm_msgScrollUpEnded = false;
            frm_msgScrollDownEnded = false;
            frmChatPageNo = 1;
            // updateSenderArea = false;

            let requestData = getFRMChatRequestData(my_pToken, 0, 'init');
            fetchFRMChatData(requestData);

            initFRMChatDataInterval();


            // // Highlight the chatting user in the entries list
            // let frm_entry_active_cls = '.frm_' + frm_room_key + '_' + chatwith;
            // $('#entriesList').find(".network_entries.active").removeClass('active');
            // $('#entriesList').find(frm_entry_active_cls).addClass('active');
            //
            // // Update invite read status
            // if ($(this).hasClass('invites-btn') && typeof $(this).data('read') !== 'undefined') {
            //     if ($(this).data('read') == 0) {
            //         let item_key = $(this).data('item') ?? '';
            //         $(this).data('read', 1);
            //         $(this).closest("div.invites-item").css("background", "inherit");
            //         updateInvitesReadStatus(frm_room_key, my_pToken, chatwith, item_key);
            //     }
            // } else {
            //     let room_invites_elem = $('.room_' + chatwith + '_' + frm_room_key);
            //     if (room_invites_elem.length > 0){
            //         room_invites_elem.css("background", "inherit");
            //     }
            //     updateInvitesReadStatus(frm_room_key, my_pToken, chatwith);
            // }
        }

        $('#frm_comment').keydown(function (event) {
            controlTextareaEnter(event, '#frm_comment_send_btn');
        });

        /* /Forum Parent methods */



        /* Forum Reply methods */
        function fetchFRMReplyChatData(requestData, serverFetch = false) {
            if (!(requestData.pToken_from && parseInt(requestData.parent_id))) return;

            const chat_messages_key = `frm_${frm_room_key}_${requestData.parent_id}`;

            IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key)
                .then((intao_data) => {
                    if (frm_reply_isProcessing || !intao_data) {
                        setTimeout(() => fetchFRMReplyChatData(requestData, serverFetch), 2000);
                        return;
                    }

                    frm_reply_isProcessing = true;

                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);

                    processFRMReplyChatData(
                        requestData,
                        intao_data.timestamp ? intao_data.values : {
                            "chat": {},
                            "last_update_time": lastCheckedTimestamp,
                            "success": true
                        }
                    );
                })
                .catch((error) => {
                    console.error("Error fetching FRMReplyChatData:", error);
                });
        }

        function processFRMReplyChatData(requestData, response) {
            let processedResponse = {};
            let recentItemsObject = {};
            let recentRenderedItemsObject = {};
            let totalchats = 0;

            if (response.success) {
                let chats = response.chat ? response.chat : [];

                let allChatKeys = Object.keys(chats);
                totalchats = allChatKeys.length;
                if (totalchats > 0) {
                    if (requestData.callFromEvent == "init" || frm_reply_msgDownIndex == 0) {
                        const slice_end = (totalchats - ((requestData.page - 1) * requestData.itemPerPage));
                        if (slice_end > 0) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), slice_end);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "scrollup") {
                        let currentFirstMsgIndex = allChatKeys.indexOf(frm_reply_msgUpIndex);
                        if (currentFirstMsgIndex > -1) {
                            const recentItems = Object.entries(chats).slice(-(requestData.page * requestData.itemPerPage), currentFirstMsgIndex);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }
                    } else if (requestData.callFromEvent == "interval") {
                        let currentLastMsgIndex = allChatKeys.indexOf(frm_reply_msgDownIndex);
                        if (currentLastMsgIndex > -1 && currentLastMsgIndex < (totalchats - 1)) {
                            const recentItems = Object.entries(chats).slice(-(totalchats - (currentLastMsgIndex + 1)), totalchats);
                            recentItemsObject = Object.fromEntries(recentItems);
                        }

                        const recentRenderedItems = Object.entries(chats).slice(-20);
                        recentRenderedItemsObject = Object.fromEntries(recentRenderedItems);
                    }
                }
            }

            processedResponse.isTempMsg = false;
            processedResponse.callFromEvent = requestData.callFromEvent;
            processedResponse.chat = recentItemsObject;
            processedResponse.overallChatCount = totalchats;
            processedResponse.recent_rendered_items = recentRenderedItemsObject;
            processedResponse.last_update_time = response.last_update_time;

            renderFRMReplyMessages(processedResponse);
        }

        async function compiledFRMReplyMsgHtml(cd) {
            let compiledMMMsgHtml;
            let safeMessageHtml = '';
            const message_content = cd.message;
            if (message_content.includes('chat-meeting-link') || cd.userType === 'system') {
                safeMessageHtml = message_content;
            } else {
                const safeMessage = document.createElement('pre');
                safeMessage.textContent = message_content;
                safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }

            if (cd.userType === 'system') {
                compiledMMMsgHtml = `<div class="badge-system clearfix"><span class="badge badge-secondary">${safeMessageHtml}</span></div>`;
            } else {
                compiledMMMsgHtml = `<div class="post-card ${cd.ptokenTo === cd.ptokenFrom ? 'mine' : 'others'} ${'msg_' + (cd.time)} ${cd.isTempMsg ? 'temp_msg new' : ''}" data-frm_reply_message_id="${cd.message_id}" data-frm_reply_message_key="${cd.time}" id="${'msg_' + (cd.time)}">
                <div class="user-info justify-content-between">
                    <div class="post-card-header-left d-flex">
                        <img src="${cd.avatar}" alt="User Image">
                        <div class="author">
                            <div class="user-name">${cd.name}</div>
                            <div class="text-muted"><small>${cd.formatted_time}</small></div>
                        </div>
                    </div>
                    <div class="post-card-header-right d-flex">
                        <div class="post-card-header-loader"></div>
                        <div class="dropleft">
                            <div class="pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-h fs-24" aria-hidden="true"></i>
                            </div>
                            <div class="dropdown-menu">
                                <div class="dropdown-item frm_reply_comment_delete">Delete</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="post-content">
                    <div>${safeMessageHtml}</div>
                </div>
                <div class="post-icons">
                    <div class="icon">
                        <!--<i class="fa fa-heart-o"></i> <span>1k</span>-->
                    </div>
                    <div class="action-icons">
                        <!-- <i class="fa fa-share-alt"></i>-->
                    </div>
                </div>
            </div>`;
            }

            return compiledMMMsgHtml;
        }

        async function getFRMReplyChatMsgHtml(response, tempMsgList) {
            let chats = response.chat || {};
            let isTempMsg = response.isTempMsg || false;

            let messageHtml = '';
            // let ptokenTo = chatwith;
            let do_highlight = false;
            let compiledChatKeys = { chats: [], temp_chats: [] };

            let lastChatItemKey = Object.keys(chats).pop();
            for (const [key, v] of Object.entries(chats)) {
                const userInfo = await getUserInfo(v.ptoken);
                let stored_time = v.hasOwnProperty('stored_time') ? v.stored_time : '';

                const userAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                    ? userInfo.avatar_image
                    : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                if (v.ptoken != my_pToken) {
                    if (!do_highlight && response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') do_highlight = true;
                    if (response.callFromEvent !== 'init' && response.callFromEvent !== 'scrollup') frm_newMessagesCnt++;
                }

                let chatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);
                if (chatDateTime_arr) {
                    if (response.callFromEvent === 'scrollup' && key === lastChatItemKey) {
                        removeSameBadge(chatDateTime_arr[0]);
                    }

                    if (response.callFromEvent === 'scrollup' && chatDateTime_arr[0] !== frm_reply_chatBadgeUpDate) {
                        frm_reply_chatBadgeUpDate = chatDateTime_arr[0];
                        messageHtml += '<div class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + frm_reply_chatBadgeUpDate + '</span></div>';
                    } else if (response.callFromEvent !== 'scrollup' && chatDateTime_arr[0] !== frm_reply_chatBadgeDownDate) {
                        frm_reply_chatBadgeDownDate = chatDateTime_arr[0];
                        messageHtml += '<div class="date-badge clearfix" data-timestamp="' + v.time + '"><span class="badge text-muted">' + frm_reply_chatBadgeDownDate + '</span></div>';
                    }
                }

                let compileData = {
                    ptokenTo: v.ptoken,
                    message: v.message,
                    message_id: v.message_id,
                    is_parent: v.parent_id == 0,
                    time: v.time,
                    name: userInfo.chat_name,
                    avatar: userAvatarSrc,
                    ptokenFrom: my_pToken,
                    userType: v.user_type,
                    isTempMsg: isTempMsg,
                    stored_time: stored_time,
                    formatted_time: chatDateTime_arr[1] ?? ''
                };
                if(compileData.is_parent){
                    compileData.reply_count = v.reply_count;
                }
                messageHtml += await compiledFRMReplyMsgHtml(compileData);

                compiledChatKeys.chats.push(isTempMsg ? (v.time).toString() : key);

                const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                if (tempMsgElem.length) tempMsgElem.removeClass('new');

                // if (isTempMsg) {
                //     // Increament reply count in parent message based on the temp reply message
                //     const msgElem = $('.post-card[data-frm_message_id="' + v.parent_id + '"]');
                //     const replyCount = parseInt(msgElem.find('.frm_reply_comment_cnt').text()) || 0;
                //     if (msgElem.length) msgElem.find('.frm_reply_comment_cnt').text(replyCount + 1);
                // }
            }

            if (!isTempMsg && typeof tempMsgList != 'undefined') {
                let allSentTimes = Object.keys(chats).map(k => chats[k]['sent_time']);
                for (const [key, v] of Object.entries(tempMsgList)) {
                    const userInfo = await getUserInfo(v.ptoken);
                    let stored_time = v.hasOwnProperty('stored_time') ? (v.stored_time).toString() : '';
                    let tempChatDateTime_arr = formatBadgeDateTime((v.time / 1000), _taoh_user_timezone);

                    const tempUserAvatarSrc = userInfo?.avatar_image && await checkImageExists(userInfo.avatar_image).catch(() => false)
                        ? userInfo.avatar_image
                        : `${_taoh_ops_prefix}/avatar/PNG/128/${userInfo?.avatar?.trim() || 'default'}.png`;

                    if (!allSentTimes.includes((v.sent_time).toString())) {
                        let compileData = {
                            ptokenTo: v.ptoken,
                            message: v.message,
                            message_id: '',
                            is_parent: v.parent_id == 0,
                            time: v.time,
                            name: userInfo.chat_name,
                            avatar: tempUserAvatarSrc,
                            ptokenFrom: my_pToken,
                            userType: v.user_type,
                            isTempMsg: true,
                            stored_time: stored_time,
                            formatted_time: tempChatDateTime_arr[1] ?? ''
                        };
                        if(compileData.is_parent){
                            compileData.reply_count = v.reply_count;
                        }
                        messageHtml += await compiledFRMReplyMsgHtml(compileData);

                        compiledChatKeys.temp_chats.push(key);
                    }

                    const tempMsgElem = $('#msg_' + (v.sent_time * 1000));
                    if (tempMsgElem.length) tempMsgElem.removeClass('new');

                    // // Increament reply count in parent message based on the temp reply message
                    // const msgElem = $('.post-card[data-frm_message_id="' + v.parent_id + '"]');
                    // const replyCount = parseInt(msgElem.find('.frm_reply_comment_cnt').text()) || 0;
                    // if (msgElem.length) msgElem.find('.frm_reply_comment_cnt').text(replyCount + 1);
                }
            }

            return { messageHtml, compiledChatKeys, do_highlight };
        }

        function renderFRMReplyMessages(response) {
            if (!response) {
                doAfterFRMReplyMsgRender(response);
                return;
            }

            let chats = response.chat || {};
            let allChatKeys = Object.keys(chats);

            let chatTempMessagesKey = 'frm_temp_' + frm_room_key + '_' + frm_message_id;
            IntaoDB.getItem(objStores.ntw_store.name, chatTempMessagesKey)
                .then(intao_data => intao_data?.values || {})
                .then(tempMsgList => {
                    return getFRMReplyChatMsgHtml(response, tempMsgList);
                })
                .then(({ messageHtml, compiledChatKeys, do_highlight }) => {
                    if (frmReplyChatFirstCall === 1 || response.callFromEvent === 'init') {
                        frm_reply_comments_list.empty();
                    }

                    $("#frm_reply_comments_list .temp_msg:not(.new)").remove();

                    if (compiledChatKeys.chats.length > 0 || compiledChatKeys.temp_chats.length > 0) {
                        $('#frm_reply_no_message').remove();
                        // $('#message_helper').hide();

                        if (response.callFromEvent === 'scrollup') {
                            frm_reply_comments_list.prepend(messageHtml);
                        } else {
                            frm_reply_comments_list.append(messageHtml);
                            if (isScrolledUp(frm_reply_chatContainer) && frmReplyChatFirstCall !== 1) {
                                if (frm_reply_newMessagesCnt > 0) {
                                    // newmessages_btn.find('span').text(frm_newMessagesCnt + ' new messages');
                                    // newmessages_btn_grp.show();
                                }
                            } else {
                                frm_reply_chatContainer.scrollTop = frm_reply_chatContainer.scrollHeight;
                                frm_reply_newMessagesCnt = 0;
                            }
                        }
                    }

                    if (compiledChatKeys.chats.length > 0 && !response.isTempMsg) {
                        if (response.callFromEvent === 'init') {
                            frm_reply_msgUpIndex = allChatKeys[0];
                            frm_reply_msgDownIndex = allChatKeys.pop();
                        } else if (response.callFromEvent === 'scrollup') {
                            frm_reply_msgUpIndex = allChatKeys[0];
                        } else if (response.callFromEvent === 'interval') {
                            frm_reply_msgDownIndex = allChatKeys.pop();
                        }
                    }

                    frmReplyChatFirstCall = 0;
                    response.compiledChatKeys = compiledChatKeys;

                    // Clearing left over temp messages if already rendered
                    if ($('#frm_reply_comments_list .temp_msg').length) {
                        let recentRenderedItems = response.recent_rendered_items;
                        for (const [k, value] of Object.entries(recentRenderedItems)) {
                            let tempMsgElem = $('.msg_' + (value.sent_time * 1000));
                            if (tempMsgElem.length) tempMsgElem.remove();
                        }
                    }

                    if (do_highlight) highlightMessages();
                })
                .then(() => {
                    doAfterFRMReplyMsgRender(response);
                });

            if (response.callFromEvent === "scrollup") {
                frm_reply_msgScrollUpEnded = false;
            }
        }

        function doAfterFRMReplyMsgRender(response) {
            let overallChatCountWithTemp = response.overallChatCount;
            if (typeof response.compiledChatKeys != 'undefined') {
                overallChatCountWithTemp += response.compiledChatKeys.temp_chats.length;
            }
            updateStickyBadgeTxt('#frm_comments_list', '#frm_stickyBadge');

            if (overallChatCountWithTemp > 0) {
                // chatCount.text(overallChatCountWithTemp);
                // $('#message_helper').hide();
                // $('#message_count').show();
            } else {
                frm_reply_msgUpIndex = 0;
                frm_reply_msgDownIndex = 0;
                // $('#message_count').hide();
                // if (chatwith_liveStatus == 1) $('#message_helper').show();
                frm_reply_comments_list.html(`<div id="frm_reply_no_message" class="col-lg-12" style="text-align: center;">
                    <img class="no-network-place" src="${_taoh_site_url_root + '/assets/images/empty_network.png'}" width="300" alt="no-network">
                </div>`);
            }

            if (response.hasOwnProperty('last_update_time')) {
                frmReplyChatLastTime = response.last_update_time || 0;
            }

            updateFRMReplyChatSendArea(frm_room_key);

            // $("#ow_header").show();
            // $("#ow_footer").show();

            frm_reply_isProcessing = false;
            taoh_Loader($('#frm_reply_chat_loader'), false);
        }

        function updateFRMReplyChatSendArea(frm_room_key) {
            if (frm_room_key.trim() !== '') {
                // if (chatwith_liveStatus == 1) {
                //     $('#message-area').hide();
                //     $('#chat-area').show();
                //     $('.frm_video').show();
                //     if (!$('.sender_part').is(":visible")) {
                //         $('.sender_part').show();
                //     }
                // } else {
                //     $('#chat-area').hide();
                //     $('.sender_part').hide();
                //     $('#message_helper').hide();
                //     $('.frm_video').hide();
                //     $('#message-area').show();
                // }
                updateSenderArea = true;
            }
        }

        let frm_reply_prevScrollPos = frm_reply_chatContainer.scrollTop;
        frm_reply_chatContainer.addEventListener('scroll', () => {
            const frm_currentScrollPos = frm_reply_chatContainer.scrollTop;

            if ((frm_currentScrollPos < frm_reply_prevScrollPos) && frm_currentScrollPos < 10 && !frm_msgScrollUpEnded) {
                // Trigger only if the user scrolls up to the top of the chat container
                frmReplyChatPageNo++;

                taohLoader(document.getElementById('frm_reply_chat_loader'), true);
                let requestData = getFRMChatRequestData(my_pToken, chatwith, 'scrollup');
                throttle(fetchFRMReplyChatData(requestData, false), (frm_reply_isProcessing ? 500 : 100));
            }

            updateStickyBadgeTxt('#frm_reply_comments_list', '#frm_reply_stickyBadge');

            // Update the previous scroll position to find the direction of scrolling
            frm_reply_prevScrollPos = frm_currentScrollPos;
        });

        function initFRMReplyChatDataInterval() {
            if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
            frmReplyChatDataInterval = setInterval(function () {
                if (!frm_reply_isProcessing) {
                    if (my_pToken.trim() !== '') {
                        let requestData = getFRMChatRequestData(my_pToken, frm_message_id, 'interval');
                        fetchFRMReplyChatData(requestData, false);
                    }
                }
            }, 3000);
        }

        $(document).on('click', '.frm_reply_comment_btn', function () {
            let parentElem = $(this).closest('.post-card');
            let frmmessageid = parentElem.data('frm_message_id');

            if (frmmessageid) {
                frm_message_id = frmmessageid
                frm_reply_comments_list.empty();
                taoh_Loader($('#frm_reply_chat_loader'), true);
                frm_reply_view = true;
                if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);

                $('#reply_comment_id').val(frm_message_id);
                $('#reply').modal('show');

                if (my_pToken !== '') {
                    let chat_networking_misc_key = 'ft_frm_networking_misc_' + frm_room_key + '_' + frm_message_id;
                    IntaoDB.getItem(objStores.ntw_store.name, chat_networking_misc_key).then((intao_data) => {
                        if (intao_data && intao_data.last_update_time) {
                            lastFRMReplyMsgCheckedTimestamp = intao_data.last_update_time;
                        } else {
                            lastFRMReplyMsgCheckedTimestamp = 0;
                        }
                        getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 2);
                    }).then(() => {
                        const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
                        taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, frm_message_id, lastCheckedTimestamp));
                    });
                }

                let requestData = getFRMChatRequestData(my_pToken, frm_message_id, 'init');
                fetchFRMReplyChatData(requestData);

                initFRMReplyChatDataInterval();
            }
        });

        $(document).on('hide.bs.modal', '#reply', function(){
            frm_message_id = 0;
            frm_reply_view = false;
            $('#reply_comment_id').val(frm_message_id);
            if (frmReplyChatDataInterval) clearInterval(frmReplyChatDataInterval);
            frm_reply_comments_list.empty();
            frmReplyChatPageNo = 1;

            initializeRequest();
        });

        $(document).on('click', '.reply_close', function () {
            $('#reply').modal('hide');
        });

        $('#frm_reply_comment').keydown(function (event) {
            controlTextareaEnter(event, '#frm_reply_comment_send_btn');
        });


        function deleteComment(elem, frmmessageid, frmmessagekey = '', parent_id = 0) {
            let isReplyRequest = Boolean(parseInt(parent_id));
            let sent_time = new Date().getTime();

            let data = {
                'taoh_action': 'taoh_forum_delete_message',
                'message_id': frmmessageid,
                'message_key': frmmessagekey,
                'ptoken': my_pToken,
                'other_ptoken': '',
                'parent_id': parent_id,
                'user_type': 'user',
                'key': frm_room_key,
                'sent_time': sent_time
            };

            let loaderElem = elem.find('.post-card-header-loader');
            loaderElem.html('<i class="fa fa-circle-o-notch fa-spin"></i>');

            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success && response.output) {
                        let chat_messages_key = isReplyRequest ? 'frm_' + data.key + '_' + data.parent_id : 'frm_' + data.key;
                        IntaoDB.getItem(objStores.ntw_store.name, chat_messages_key).then((intao_data) => {
                            if (intao_data?.values) {
                                let updatedResponse = intao_data.values;
                                if (updatedResponse && Object.keys(updatedResponse.chat).length > 0) {
                                    let is_deleted = false;
                                    let deletedEntryKey = null;
                                    for (const key in updatedResponse.chat) {
                                        if (updatedResponse.chat[key].message_id === data.message_id) {
                                            deletedEntryKey = key;
                                            delete updatedResponse.chat[key];
                                            is_deleted = true;
                                            break;
                                        }
                                    }

                                    if (is_deleted) {
                                        const lastMatchingEntry = Object.values(updatedResponse.chat).reverse().find(entry => entry.ptoken === data.ptoken);
                                        if (lastMatchingEntry) frm_my_recent_message_timestamp = lastMatchingEntry.time / 1000;
                                        if(deletedEntryKey){
                                            const msgElem = $('#msg_' + deletedEntryKey);
                                            if (msgElem.length) msgElem.remove();
                                        }
                                    }

                                    IntaoDB.setItem(objStores.ntw_store.name, {taoh_ntw: chat_messages_key, values: updatedResponse, timestamp: Date.now()});
                                }
                            }
                        }).then(() => {
                            loaderElem.empty();
                            checkCommentFormTime();
                        });
                    } else {
                        console.log('Error deleting comment:', response);
                        loaderElem.empty();
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error deleting comment:', xhr.status);
                    loaderElem.empty();
                }
            });
        }

        $(document).on('click', '.frm_comment_delete', function () {
            let parentElem = $(this).closest('.post-card');
            let frmmessageid = parentElem.data('frm_message_id');
            let frmmessagekey = parentElem.data('frm_message_key');

            if (frmmessageid) {
                deleteComment(parentElem, frmmessageid, frmmessagekey);
            } else {
                alert('Message ID not found for deletion. Please try again later.');
            }
        });

        $(document).on('click', '.frm_reply_comment_delete', function () {
            let parentElem = $(this).closest('.post-card');
            let frmreplymessageid = parentElem.data('frm_reply_message_id');
            let frmreplymessagekey = parentElem.data('frm_reply_message_key');

            if (frmreplymessageid) {
                deleteComment(parentElem, frmreplymessageid, frmreplymessagekey, frm_message_id);
            }
        });


        function checkCommentFormTime() {
            /* temp update */
            document.getElementById('comment_form_note_blk').style.display = 'none';
            document.getElementById('frm_comment_send_btn').disabled = false;
            /* /temp update */

            /*const d = new Date(frm_my_recent_message_timestamp);
            if (isNaN(d.getTime())){
                document.getElementById('frm_comment_send_btn').disabled = false;
                return;
            }

            const currentTime = new Date();
            let timeDifference = getTimeDifferenceInSeconds(currentTime, d);

            if (timeDifference < frm_commentDelayTimeInSeconds) {
                const delayTimeInSeconds = frm_commentDelayTimeInSeconds - timeDifference;

                const hours = Math.floor(delayTimeInSeconds / 3600).toString().padStart(2, '0');
                const minutes = Math.floor((delayTimeInSeconds % 3600) / 60).toString().padStart(2, '0');
                const seconds = (delayTimeInSeconds % 60).toString().padStart(2, '0');

                let timeString = `${hours}:${minutes}:${seconds}`;

                let frm_commentDelayTimeInHours = (frm_commentDelayTimeInSeconds / 3600);
                frm_commentDelayTimeInHours = frm_commentDelayTimeInHours % 1 ? frm_commentDelayTimeInHours.toFixed(2) : frm_commentDelayTimeInHours.toFixed(0);

                document.getElementById('comment_form_note').textContent =
                    `Note: You can post again after ${frm_commentDelayTimeInHours} hours. Since your last comment was less than ${frm_commentDelayTimeInHours} hours ago, you will be able to comment again in ${timeString}.`;
                document.getElementById('comment_form_note_blk').style.display = 'block';
                document.getElementById('frm_comment_send_btn').disabled = true;
                if (!frm_checkCommentDelayInterval) {
                    frm_checkCommentDelayInterval = setInterval(checkCommentFormTime, 1000); // Check every second
                }
            } else {
                document.getElementById('comment_form_note_blk').style.display = 'none';
                document.getElementById('frm_comment_send_btn').disabled = false;
                if (frm_checkCommentDelayInterval) clearInterval(frm_checkCommentDelayInterval);
            }*/
        }

        checkCommentFormTime();

        /* /Forum Reply methods */


        /**********************====================== /Forum Functions ===========================**********************/

        function initializeRequest() {
            let activeTabId = $('#networkingTab button[data-toggle="tab"].active').attr("id");
            if (activeTabId?.trim() && activeTabId !== room_activeTabId) {
                room_activeTabId = activeTabId;
            }

            if (activeTabId === 'conversation-tab') {
                if (!frm_reply_view && !ft_frm_isProcessing) {
                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMMsgCheckedTimestamp', lastFRMMsgCheckedTimestamp, 1);
                    taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, 0, lastCheckedTimestamp));
                } else if (frm_reply_view && !ft_frm_reply_isProcessing) {
                    const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastFRMReplyMsgCheckedTimestamp', lastFRMReplyMsgCheckedTimestamp, 1);
                    taohFRMMessagesFromServer(getForumMessagesFormData(frm_room_key, my_pToken, frm_message_id, lastCheckedTimestamp));
                }

                if(ntwUserEntriesIntervalId) clearInterval(ntwUserEntriesIntervalId);
            } else if (activeTabId === 'connections-tab') {
                const lastCheckedTimestamp = getAndSetLastCheckedTimestamp('lastNTWMsgCheckedTimestamp', lastNTWMsgCheckedTimestamp, 1);
                taohNTWMessagesFromServer(getNetworkingMessagesFormData(my_pToken, lastCheckedTimestamp));

                updateNTWUserEntriesInterval();

            } else if (activeTabId === 'rooms-tab') {

                if(ntwUserEntriesIntervalId) clearInterval(ntwUserEntriesIntervalId);
            }
        }

        $(document).on('click', '#networkingTab button[data-toggle="tab"]', function (e) {
            let activeTabId = $(e.target).attr("id");

            if (chat_window != 1) $(".open_window").hide();

            if (activeTabId === 'conversation-tab') {

            } else if (activeTabId === 'connections-tab') {
                if (chat_window == 1) $(".open_window").show();

            } else if (activeTabId === 'rooms-tab') {

            }

            initializeRequest();
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                if (ntw_view != 2) taoh_load_network_entries();

                initializeRequest();
            }


            // User Live Status Update
            userLiveStatusInterval = document.hidden ? 300000: 1000; // 5 minutes : 1 second
            prev_userLiveStatusInterval = userLiveStatusInterval;
            if(userLiveIntervalId) clearInterval(userLiveIntervalId);
            userLiveStatusUpdate(userLiveStatusInterval);

            stopTitleBlinking();
        });


        /* poll question script */

        /* new script */
       
        // Function to toggle visibility of the poll options card
        const togglePollOptions = () => {
            const pollOptions = document.getElementById("poll-options");
            pollOptions.classList.toggle("d-none");
            // console.log('tog pol opt');
        };

        // Function to close the poll options card when the close button is clicked
        const closePollOptions = () => {
            const pollOptions = document.getElementById("poll-options");
            pollOptions.classList.add("d-none");
            // console.log('col pol opt');
        };

        // Add event listeners to the buttons
        document.querySelector(".add-poll-btn").addEventListener("click", togglePollOptions);
        document.querySelector(".poll-close-btn").addEventListener("click", closePollOptions);


        // scroll
        const scrollContainer = document.querySelector('.scroll-container');
        const scrollLeftButton = document.getElementById('scroll-left');
        const scrollRightButton = document.getElementById('scroll-right');

        // Scroll the container to the far left
        scrollLeftButton.addEventListener('click', function() {
            scrollContainer.scrollLeft = 0; // Move to the leftmost position
        });

        // Scroll the container to the far right
        scrollRightButton.addEventListener('click', function() {
            scrollContainer.scrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth; // Move to the rightmost position
        });


        // commentInput.emojioneArea();
        // frm_commentInput.emojioneArea();
        // frm_ReplycommentInput.emojioneArea();
        // $("#networkingOfflineMessage").emojioneArea({
        //     pickerPosition: "bottom"
        // });



        /* /new script */
    </script>

    <!-- poll vote script  -->
    <script>
        /* new script */
        let poll = {
            question: "Which is your favorite programming language?",
            answers: ["Python", "Java", "R", "Php"],
            pollcount: 100, // dynamic count of user participated in poll vote
            answerweight: [0, 0, 0, 0], //sum = 100
            selectanswer: -1
        };

        let polldom = {
            question: document.querySelector(".poll .question"),
            answers: document.querySelector(".poll .answers")
        };

        polldom.question.innerText = poll.question;
        polldom.answers.innerHTML = poll.answers.map(function (answer, i) {
            return (`<div class="answer" onclick="markanswer('${i}')">${answer}
                <span class="percentage_bar"></span>
                <span class="percentage_value"></span>
            </div>`);
        }).join("");

        /* /new script */

        function markanswer(i) {
            poll.selectanswer = +i;

            try {
                document.querySelector(".poll .answers .answer.selected")
                    .classList.remove(".selected");
            } catch (msg) {
            }

            document.querySelectorAll(".poll .answers .answer")
                [+i].classList.add(".selected");

            showresults();
        }

        function showresults() {
            let answers = document.querySelectorAll(".poll .answers .answer");
            for (let i = 0; i < answers.length; i++) {
                let percentage = 0;

                if (i == poll.selectanswer) {
                    percentage = Math.round
                    (
                        (poll.answerweight[i] + 1) * 100 / (poll.pollcount + 1)
                    );
                } else {
                    percentage = Math.round
                    (
                        (poll.answerweight[i]) * 100 / (poll.pollcount + 1)
                    );
                }

                answers[i].querySelector(".percentage_bar").style.width = percentage + "%";
                answers[i].querySelector(".percentage_value").innerText = percentage + "%";

            }
        }

        <!-- preview files -->
        document.querySelectorAll('.inside-icon').forEach(function (container, index) {
            var fileInput = container.querySelector('input[type="file"]');
            var filePreview = container.querySelector('.file-preview');

            // Handle file input change
            fileInput.addEventListener("change", function (event) {
                var files = event.target.files;


                // Clear the input field value (so the same file can be selected again if needed)
                fileInput.value = "";
            });
        });


        /*CUSTOM ROOM*/
        loadCustomRooms();
        var room_query = '';

        function searchRoomsFilter() {
            room_query = $('#room_query').val();
            loadCustomRooms();
        }

        function create_new_room() {
            var room_name = $('#custom_room_name').val();
            //alert(room_name);
            if (room_name == '') {
                $('#custom_room_name').addClass('error');
                return false;
            }
            let data = {
                'taoh_action': 'taoh_create_update_custom_room',
                'parent_keyslug': ntw_room_key,
                'my_pToken': my_pToken,
                'room_name': room_name,
            };

            $.post(_taoh_site_ajax_url, data, function (response) {
                loadCustomRooms();
                $('#custom_room_name').val('');
            }).fail(function () {
                alert('Issue in creating room');
            });
        }

        function join_room_video_chat(room_name) {
            let data = {
                'taoh_action': 'taoh_create_update_custom_room',
                'parent_keyslug': ntw_room_key,
                'my_pToken': my_pToken,
                'room_name': room_name,
                'join_room': 1,
            };

            $.post(_taoh_site_ajax_url, data, function (response) {
                if (response.my_link) window.open(response.my_link);
                loadCustomRooms();
            }).fail(function () {
                alert('Error in video chat');
            });
        }

        function delete_custom_room(room_name) {

            let data = {
                'taoh_action': 'taoh_create_update_custom_room',
                'parent_keyslug': ntw_room_key,
                'my_pToken': my_pToken,
                'room_name': room_name,
                'delete': 1,
            };

            $.post(_taoh_site_ajax_url, data, function (response) {
                loadCustomRooms();
            }).fail(function () {
                alert('Issue in deleting room');
            });
        }

        function loadCustomRooms() {
            var slot = $('#custom_room_list');
            slot.html('<img id="loaderEmail" width="50" src="<?php echo TAOH_LOADER_GIF; ?>"/>');

            let data = {
                'taoh_action': 'taoh_get_custom_rooms',
                'parent_keyslug': ntw_room_key,
                'my_pToken': my_pToken,
                'room_query': room_query,
            };

            $.post(_taoh_site_ajax_url, data, function (response) {

                //alert(response.output + ' ' + response.success);
                if (response.output === false && response.success === true) {
                    if (show_video_conv_btn) {
                        slot.html(`<div class="">
                        <button class="btn btn-sm theme-btn " id="video_room_btn" data-toggle="modal"
                         data-target="#video_room_join_confirmation"><i class="fa fa-video-camera mr-1"
                          aria-hidden="true"></i> Enter Video Room</button>
                    </div>`);
                    } else {
                        slot.html(`<div class="entry_3 card card-item p-3 no_custom_room">No Rooms. Be first to create new room and explore more..</div>`);
                    }

                } else {


                    var myobj = JSON.parse(response.output);

                    var room_list = '';
                    $.each(myobj.meets, function (i, v) {
                        room_list += `
                        <div class="network_entries room_${i} entry_3 card card-item" style="margin-bottom:10px;">
                            <div class="card-body" style="font-size:13px;">
                                <div class="row">
                                    
                                    <div class="col-md-10 col-lg-12">
                                        <div class="entries-title align-items-center">
                                            
                                                <div>
                                                    <h5><a class="text-primary text-capitalize" target="_blank" >
                                                    ${v.value.title}</a></h5>
                                                    <span>${v.count} Clicked to join the room</span> 
                                                    
                                                   
                                                </div>                                                                                                              
                                                <div>
                                                    <div class="btn-group" role="group">
                                                    ${v.value.ptoken == my_pToken ? `
                                                    <button type="button" id="delete_KEYSLUG_${i}" 
                                                    onclick="delete_custom_room('${v.value.title}')" class="btn-error btn-sm mr-2"  style="white-space: nowrap;font-size: small;border: 1px solid #c3c3c3;">
                                                    Delete</button>` : ''}

                                                    <button type="button" id="KEYSLUG_${i}" 
                                                    onclick="join_room_video_chat('${v.value.title}')" class="btn btn-sm openVideoChat mr-2"  style="white-space: nowrap;font-size: small;border: 1px solid #c3c3c3;">
                                Join <i class="la la-angle-double-right"></i></button>
                                                    
                                                    </div>
                                                </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                `;

                    });
                    slot.empty();
                    slot.html(room_list);
                }

            }).fail(function () {
                alert('Issue in loading rooms');
            });
        }

        function get_today_date() {
            var currentDate = new Date()
            var day = currentDate.getDate();
            var month = currentDate.getMonth() + 1;
            var year = currentDate.getFullYear();
            var my_date = month + "-" + day + "-" + year;
            return my_date;
            //localStorage.setItem('date_last_agree', my_date);
        }


var date_last_agree_model  = localStorage.getItem('date_last_agree_'+frm_room_key);
var current_date = get_today_date();
if(date_last_agree_model == '' || date_last_agree_model == null){
    $('#agreeModal').modal('show');
    localStorage.setItem('date_last_agree_'+frm_room_key, current_date);
}
else if(date_last_agree_model != current_date){
    $('#agreeModal').modal('show');
    localStorage.setItem('date_last_agree_'+frm_room_key, current_date);
}

<?php if (isset($room_app) && $room_app === 'event') { ?>
    setTimeout(async function () {
        getEventData(eventtoken);
        profile_badges = await getSponsorBadges(eventtoken);
        //alert(profile_badges);
    }, 3000);



<?php } ?>
      
function getEventData(eventtoken){
         const eventBaseInfoKey = `event_detail_${eventtoken}`;
       
            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
        //    console.log('-----------',data)
            if (data?.values) {
                processEventBaseInfo(eventtoken, data.values);
            } else {
               //alert('No event data in intoadb - reload to event detail')
            }
        });
}
 
function processEventBaseInfo(requestData, response) {
               

                
                let event_output = response.output;
                let conttoken_data = event_output.conttoken;

                /* Event Sponsor */
                getEventSponsor(event_output.eventtoken);
                /* Event Sponsor */

                
                /* Event Sponsor popup*/
                let eventSponsorWidgetType = conttoken_data.event_sponsor_widget_type || {};
                let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(widget => widget.status);

                

                constructSponsorInfoPopup(event_output.eventtoken,eventSponsorWidgetType);
                
                if (eventSponsorWidgetTypeStatusList.includes(1)) {
                    //$('.event_sponsor_right_header').show();
                     $('#sponsor_card').show();
                     $('.get-started').show();
                }
                else{
                    $('.event_sponsor_right_header').hide();
                    $('.get-started').hide();
                }
                    
                /* Event Sponsor popup*/
             }

    </script>

<?php
taoh_get_footer();
