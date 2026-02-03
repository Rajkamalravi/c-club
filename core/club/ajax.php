<?php

function taoh_dir_users_list()
{
    $taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
    $user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

    $invalid_data = false;
    $invalid_data_msg = 'invalid_data';

    $type = $_POST['type'] ?? '';

    if ($type === 'flag') {
        $directory_flags_to_show = defined('TAOH_DIRECTORY_FLAGS_TO_SHOW') ? TAOH_DIRECTORY_FLAGS_TO_SHOW : [];
        $my_profile_tag_category = $user_info_obj->tags ?? [];

        $flags_to_show = [];
        if (!empty($_POST['view']) && in_array($_POST['view'], ['club', 'profile'])) {
            if ($_POST['view'] === 'profile') {
                $selected_flag = $_POST['flag'] ?? '';

                if (!empty($selected_flag) && !empty($my_profile_tag_category) && !empty($directory_flags_to_show)) {
                    $my_profile_linked_tag_category = [];
                    foreach ($my_profile_tag_category as $tag) {
                        $kebab_selected_flag = str_replace(' ', '-', strtolower($tag));
                        if (array_key_exists($kebab_selected_flag, $directory_flags_to_show)) {
                            $my_profile_linked_tag_category = array_merge($my_profile_linked_tag_category, $directory_flags_to_show[$kebab_selected_flag]);
                        }
                    }

                    if (!empty($my_profile_linked_tag_category)) {
                        $my_profile_linked_tag_category = array_values(array_unique($my_profile_linked_tag_category));
                    }

                    if (in_array($selected_flag, $my_profile_linked_tag_category)) {
                        $flags_to_show = [$selected_flag];
                    } else {
                        $invalid_data = true;
                        $invalid_data_msg = 'not_exist_in_my_profile_flags';
                    }
                } else {
                    $invalid_data = true;
                    if (empty($selected_flag)) {
                        $invalid_data_msg = 'no_flag_selected';
                    } else if (empty($my_profile_tag_category)) {
                        $invalid_data_msg = 'no_flags_in_my_profile';
                    } else {
                        $invalid_data_msg = 'no_flags_in_directory';
                    }
                }
            }
        } else {
            $invalid_data = true;
            $invalid_data_msg = 'invalid_view_type';
        }
    }

    if (!$invalid_data) {
        $taoh_call = 'users.directory.list';
        $taoh_vals = array(
            "mod" => 'users',
            "token" => taoh_get_api_token(1),
            "local" => true,
            "view" => $_POST['view'] ?? 'club',
            "type" => $type,
        );

        if($taoh_vals['type'] == 'flag') {
            if(!empty($flags_to_show)) {
                $flags_to_show = array_values(array_unique($flags_to_show));
            }
            $taoh_vals['flag'] = $flags_to_show;
        }

        if ($taoh_vals['type'] == 'skill') {
            $skills = $_POST['skill'] ?? '';
            if (!empty($skills)) {
                $taoh_vals['flag'] = [$skills];
            }
        }

        if ($taoh_vals['type'] == 'hobbies') {
            $hobby = $_POST['hobby'] ?? '';
            if (!empty($hobby)) {
                $taoh_vals['flag'] = [$hobby];
            }
        }

        if ($taoh_vals['type'] == 'company') {
            $company = $_POST['company'] ?? '';
            if (!empty($company)) {
                $taoh_vals['flag'] = [$company];
            }
        }

        if ($taoh_vals['type'] == 'role') {
            $taoh_vals['type'] = 'title';

            $role = $_POST['role'] ?? '';
            if (!empty($role)) {
                $taoh_vals['flag'] = [$role];
            }
        }

        if (isset($_POST['search']) && !empty($_POST['search'])) {
            $taoh_vals['search'] = $_POST['search'];
        }

        if (isset($_POST['offset'])) {
            $taoh_vals['offset'] = (int)$_POST['offset'];
        }

        if (isset($_POST['limit'])) {
            $taoh_vals['limit'] = (int)$_POST['limit'];
        }
        // $cache_name = $taoh_call . '_users_' . hash('sha256', $taoh_call . serialize($taoh_vals));
        // $taoh_vals['cfcache'] = $cache_name;
        // $taoh_vals['cache_name'] = $cache_name;
        // $taoh_vals['cache'] = array("name" => $cache_name);
        ksort($taoh_vals);

        $taoh_vals['cache_required'] = 0; // :rk temp added due to cache issues
        //$taoh_vals['debug_api'] = 1;

        echo taoh_apicall_get($taoh_call, $taoh_vals);
    } else {
        $response = array(
            'success' => false,
            'error' => $invalid_data_msg,
        );
        echo json_encode($response);
    }
}

function get_keywords_room()
{
    $response = array('success' => false);

    if (taoh_user_is_logged_in()) {
        $room_type = $_POST['room_type'];

        $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

        if ($room_type === 'keyword') {
            $keyword_key = $_POST['keyword_key'];
            $keyword_value = $_POST['keyword_value'];

            $geo_enable = 1;
            if ($geo_enable) {
                $full_loc_expl = explode(', ', $sess_user_info->full_location);
                $country = array_pop($full_loc_expl);

                $keyslug = hash('crc32', TAOH_SITE_ROOT_HASH . $country . $keyword_key . $keyword_value);
            } else {
                $keyslug = hash('crc32', TAOH_SITE_ROOT_HASH . $keyword_key . $keyword_value);
            }

            $room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'global']);
            $room_status_arr = json_decode($room_status, true);
            if (isset($room_status_arr['output']) && $room_status_arr['output']) {
                $response['success'] = true;
                $response['room_info'] = $room_status_arr['output'];
            } else {
                require_once 'includes/club_room_data.php';

                $taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];
                $user_keywords_data = $taoh_user_keywords[$keyword_key] ?? [];

                if(!empty($user_keywords_data)){
                    $room_input_data = array(
                        'keyslug' => $keyslug,
                        'title' => $user_keywords_data['title'],
                        'description' => $user_keywords_data['description'],
                        'geo_enable' => $geo_enable,
                        'keyword_value' => $keyword_value,
                        'current_app_slug' => 'club',
                    );
                    $room_data_arr = get_networking_keyword_room_data($room_input_data);
                    $room_status_arr = create_room_info($room_data_arr, $sess_user_info->ptoken);
                    if ($room_status_arr['success']) {
                        $response['success'] = true;
                        $response['room_info'] = $room_status_arr['output'];
                    }
                } else {
                    $response['error'] = 'keyword_not_found';
                    $response['message'] = 'Keyword not found';
                }
            }

        }

    } else {
        $response['error'] = 'user_not_logged_in';
        $response['message'] = 'User not logged in';
    }

    echo json_encode($response);
}

/* Networking ajax Methods */

function taoh_user_info()
{
    $return_data['success'] = false;

    $ops = $_POST['ops'] ?? 'public';  // ops = full, public, cell(minimal), notify
    $ptoken = $_POST['ptoken'];

    if (!empty($ptoken)) {
        $userinfo_json = taoh_get_user_info($ptoken, $ops);
        $userinfo_array = json_decode($userinfo_json, true);

        $user_data = $userinfo_array['output']['user'] ?? null;
        if (
            is_array($userinfo_array) &&
            in_array($userinfo_array['success'] ?? null, [true, 1, 'true', '1'], true) &&
            !empty($user_data)
        ) {
            $return_data['success'] = true;
            $return_data['user_data'] = $user_data;
        }
    }

    echo json_encode($return_data);
}

function taoh_get_user_live_status()
{
    $taoh_vals = array(
        "ops" => 'live',
        'status' => 'get',
        'key' => $_POST['key'],
        'ptoken' => $_POST['ptoken'],
        'code' => TAOH_OPS_CODE,
    );
    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    echo $result;
}

function taoh_create_google_meet_link()
{
    $taoh_vals = array(
        "summary" => $_POST['summary'],
        "start_datetime" => $_POST['start_datetime'],
        "end_datetime" => $_POST['end_datetime'],
        "timezone" => $_POST['timezone'],
    );   
    $headers = [
        'x-api-key' => TAOH_CREATE_GOOGLE_MEET_API_KEY,
        'Content-Type' => 'application/json'
    ];
    $result = taoh_post(TAOH_CREATE_GOOGLE_MEET_URL, $taoh_vals, $headers, 0, "json");
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_load_network_entries()
{
    $taoh_vals = array(
        "ops" => 'status',
        'status' => 'getlist',
        'key' => $_POST['ptoken'],
        'code' => TAOH_OPS_CODE,
        'search' => $_POST['search'],
        //'latitude'    => $_POST['latitude'],
        //'longitude'   => $_POST['longitude'],
        'radius' => $_POST['radius'],
        'unit' => $_POST['unit'],
        'offset' => $_POST['offset'],
        'limit' => $_POST['limit'],
        //'country'           => trim($country),
    );

    if (isset($_POST['geo_enable']) && $_POST['geo_enable'] == 1) {
        $taoh_vals['latitude'] = $_POST['latitude'];
        $taoh_vals['longitude'] = $_POST['longitude'];
    }

//    if(isset($_POST['local'])){
//        $taoh_vals['secret'] = TAOH_API_SECRET;
//        $taoh_vals['local'] = $_POST['local'];
//    }
//
//    if(isset($_POST['global'])){
//        $taoh_vals['global'] = $_POST['global'];
//    }
    if (isset($_POST['key'])) {
        $taoh_vals['keyslug'] = $_POST['key'];
    }

    if (isset($_POST['live'])) {
        $taoh_vals['live'] = 1;
    }

    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $return = json_decode($result, true);

    $totalCount = $count = 0;
    $response = array();
    $networkArray = array();
//    $itemArray = $itemLiveArray = $itemNonLiveArray= array();
    if ($return['success']) {
        $totalCount = $return['output']['totalcount'];
        $count = $return['output']['returncount'];
        $networkArray = $return['output']['items'];

//        foreach($return['output']['items'] as $key=>$value){
//            $itemArray[$key] = $value['cell'];
//            if($value['live'] == 1)
//                $itemLiveArray['LIVE_'.$key] = $value;
//            else
//                $itemNonLiveArray['NOLIVE_'.$key] = $value;
//        }
    }
//    $networkArray = array_merge($itemLiveArray,$itemNonLiveArray);


    $response['success'] = true;
    $response['items'] = $networkArray;
    $response['totalcount'] = $totalCount;
    $response['returncount'] = $count;
//    $response['itemList'] = $itemArray;

    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($response);
    taoh_exit();
}

function taoh_room_send_message()
{
    $taoh_vals = array(
        "ops" => 'group_message',
        'status' => 'post',
        'key' => $_POST['ptoken'],
        'with' => $_POST['other_ptoken'],
        'message' => $_POST['message'],
        'user_type' => $_POST['user_type'] ?? 'user',
        'type' => TAOH_CHAT_NETWORK ?? 0,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_dummy_token(),
    );

    if (isset($_POST['key'])) {
        $taoh_vals['keyslug'] = $_POST['key'];
    }

    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $return = json_decode($result, true);
    $return['sent_time'] = $_POST['sent_time'];

    echo json_encode($return);
}

function taoh_forum_send_message()
{
    $taoh_vals = array(
        "ops" => 'group_message',
        'status' => 'post',
        'key' => $_POST['ptoken'],
        'with' => $_POST['other_ptoken'],
        'message' => $_POST['message'],
        'user_type' => $_POST['user_type'] ?? 'user',
        'parent_id' => $_POST['parent_id'] ?? 0,
        'type' => TAOH_CHAT_FORUM ?? 3,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_dummy_token(),
    );

    if (isset($_POST['key'])) {
        $taoh_vals['keyslug'] = $_POST['key'];
    }

    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $return = json_decode($result, true);
    $return['sent_time'] = $_POST['sent_time'];

    echo json_encode($return);
}


function taoh_hash()
{
    $response['success'] = false;

    $token = $_POST['token'];
    $algo = $_POST['algo'] ?? 'crc32';

    if (!empty($token)) {
        if (is_array($token)) {
            sort($token);
            $token = implode(',', $token);
        }

        $response['success'] = true;
        $response['hash'] = hash($algo, $token);
    }

    echo json_encode($response);
}

function taoh_add_video_chat()
{
    // Check which code should go first lexicographically
    $my_token = $_POST['my_token'];
    $guest_token = $_POST['guest_token'];
    if ($my_token > $guest_token) {
        $combined = $my_token . '_' . $guest_token;
    } else {
        $combined = $guest_token . '_' . $my_token;
    }

    if(isset($_POST['random']) && $_POST['random'] == 1){
        $combined = $combined.''.time();
    }

    // Calculate the CRC32 hash of the combined string
    $crc = crc32($combined);

    $my_link = TAOH_VIDEO_CONF_LINK . urlencode($_POST['network_title'] ). '-Room' . $crc . '';
    $other_link = TAOH_VIDEO_CONF_LINK . urlencode($_POST['network_title']) . '-Room' . $crc . '';


    //$keyslug = hash('crc32', $_POST['network_title'].'_room'.$_POST['parent_keyslug']);
    $keyslug = hash('crc32', $_POST['network_title'].$_POST['parent_keyslug']);
    $room_data = array();
    if(isset($_POST['network_title'])){
        $room_data = array( 
            'keyslug'=>$keyslug,
            'ptoken'=>$_POST['my_pToken'],
            'title'=>$_POST['network_title']
        );
    }
    $taoh_vals = array(
        'ops'               => 'status',
        'status'            => 'updatemeets',
        'code'              => TAOH_OPS_CODE,
        'key'               => $keyslug,
        'keyslug'           => $_POST['parent_keyslug'],
        'type'              => 'custom_room',
        'app'                => 'networking',   
        'ptoken'            => $_POST['my_pToken'],
        'value'             => addslashes(json_encode($room_data)),

        //'debug'             => 1
    );

    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);

    $result = json_encode(array(
        'my_link' => $my_link,
        'other_link' => $other_link
    ));
    header('Content-Type: application/json; charset=utf-8');

    echo $result;
    taoh_exit();
}

function taoh_network_update_online()
{
    $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

    $taoh_vals = array(
        "ops" => 'status',
        "status" => 'post',
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['ptoken'],
        'keyslug' => $_POST['keyslug'],
        'is_mood_status_update' => (bool) ($_POST['is_mood_status_update'] ?? false),
        'cell' => json_encode($sess_user_info),
        'ticketInfo' => $_POST['ticketInfo'] ?? '',
    );
    if ($taoh_vals['is_mood_status_update']) {
        $taoh_vals['status_message'] = $_POST['mood_status'] ?? '';
    }
    if (isset($_POST['geo_enable']) && $_POST['geo_enable'] == 1) {
        $taoh_vals['latitude'] = $_POST['latitude'];
        $taoh_vals['longitude'] = $_POST['longitude'];
    }
    /*if (isset($_POST['global'])) {
        $taoh_vals['global'] = $_POST['global'];
    }
    if (isset($_POST['local'])) {
        $taoh_vals['secret'] = TAOH_API_SECRET;
        $taoh_vals['local'] = $_POST['local'];
    }*/
    //$taoh_vals['debug'] = 1;

    echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
}

function toah_network_form_post() {
    if (isset($_POST['short_image'], $_POST['sq_image']) && $_POST['description'] != '<p><br></p>') {
        parse_str($_POST['taoh_form_data'], $form);

        $title = $form['room_title'];
        $keyword = str_replace(' ', '_', $form['room_keyword']);
        $geo_enable = ($form['geo_based'] == 'on') ? 1 : 0;
        $lock_req = ($form['lock_req'] == 'on');
        $lock_code = $lock_req ? $form['lock_code'] : '';
        $room_visiblity = ($form['room_visiblity'] == 'on');
        $chat_room_status = $form['chat_room_status'];

        // Date-Time Lock Processing
        $date_time_lock_req = ($form['datetime_lock_req'] == 'on');
        if ($date_time_lock_req) {
            $start_date_time = strtotime($form['start_datetime_lock']);
            $end_date_time = strtotime($form['end_datetime_lock']);
            $fromzone = 'America/New_york';
            $tozone = 'UTC';
            $format = 'c';

            $gmt_start = (new DateTime($form['start_datetime_lock'], new DateTimeZone($fromzone)))
                ->setTimezone(new DateTimeZone($tozone))->format($format);
            $gmt_end = (new DateTime($form['end_datetime_lock'], new DateTimeZone($fromzone)))
                ->setTimezone(new DateTimeZone($tozone))->format($format);
        } else {
            $start_date_time = $end_date_time = $gmt_start = $gmt_end = '';
        }

        // Other room settings
        $external_link = $form['external_link'] ?? '';
        $live_cast_link = $form['live_cast_link'] ?? '';
        $make_cmp_split = ($form['room_make_cmp_split'] == 'on');
        $make_cmp_specific = ($form['room_make_cmp_specific'] == 'on');
        $company_name = $make_cmp_specific ? $form['company:company'] : '';
        $make_title_split = ($form['room_make_title_split'] == 'on');
        $make_title_specific = ($form['room_make_title_specific'] == 'on');
        $title_name = $make_title_specific ? $form['title:title'] : '';
        $make_country_split = ($form['room_make_country_split'] == 'on');
        $make_country_specific = ($form['room_make_country_specific'] == 'on');
        if($make_country_specific) {
            $full_loc_expl = explode(', ', $form['full_location']);
            $country_name = array_pop($full_loc_expl);
            $geohash = $form['geohash'];
        } else {
            $country_name = '';
            $geohash = '';
        }
        $make_skill_split = ($form['room_make_skill_split'] == 'on');
        $make_skill_specific = ($form['room_make_skill_specific'] == 'on');
        $skill_name = $make_skill_specific ? $form['skill:skill'] : '';
        $room_publish = ($form['room_publish'] == 'on');
        $room_private = ($form['room_private'] == 'on');


        $short_img = $_POST['short_image'];
        $sq_image = $_POST['sq_image'];
        $description = htmlentities($_POST['description']);
        $more_info = htmlentities($_POST['html_description']);
        $msg_from_owner = htmlentities($_POST['msg_from_owner']);

        // User Info
        $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
        $coordinates = explode('::', $sess_user_info->coordinates ?? '');
        $lat = $coordinates[0] ?? '';
        $long = $coordinates[1] ?? '';

        $owner_info = [
            'user' => [
                'ptoken' => $sess_user_info->ptoken,
                'chat_name' => $sess_user_info->chat_name,
                'avatar' => $sess_user_info->avatar,
                'full_location' => $sess_user_info->full_location,
                'coordinates' => $sess_user_info->coordinates,
                'geohash' => $sess_user_info->geohash,
                'local_timezone' => $sess_user_info->local_timezone,
                'profile_type' => $sess_user_info->type,
                'site' => ['source' => '/', 'name' => TAOH_SITE_NAME_SLUG],
                'longitude' => $long, 'latitude' => $lat
            ]
        ];

        $return = [
            'keyslug' => hash('crc32', $keyword),
            'app' => 'custom',
            'club' => [
                'title' => $title,
                'description' => $description,
                'more_info' => $more_info,
                'msg_from_owner' => $msg_from_owner,
                'keyword' => $keyword,
                'image' => $short_img,
                'square_image' => $sq_image,
                'geo_enable' => $geo_enable,
                'lock_required' => $lock_req,
                'lock_code' => $lock_code,
                'date_time_lock_required' => $date_time_lock_req,
                'start_date_time' => $start_date_time,
                'end_date_time' => $end_date_time,
                'utc_start' => $gmt_start,
                'utc_end' => $gmt_end,
                'chat_room_status' => $chat_room_status,
                'external_link' => $external_link,
                'live_cast_link' => $live_cast_link,
                'room_visiblity' => $room_visiblity,
                'make_cmp_split' => $make_cmp_split,
                'make_cmp_specific' => $make_cmp_specific,
                'company_name' => $company_name,
                'make_title_split' => $make_title_split,
                'make_title_specific' => $make_title_specific,
                'title_name' => $title_name,
                'make_country_split' => $make_country_split,
                'make_country_specific' => $make_country_specific,
                'country_name' => $country_name,
                'geohash' => $geohash,
                'make_skill_split' => $make_skill_split,
                'make_skill_specific' => $make_skill_specific,
                'skill_name' => $skill_name,
                'room_publish' => $room_publish,
                'room_private' => $room_private,
                'owner_enable' => false,
                'owner' => $owner_info,
                'sub_secret_token' => TAOH_ROOT_PATH_HASH,
                'custom_room' => true,
            ]
        ];

        // Check room existence and create room
        $existing_room = json_decode(get_room_info($return['keyslug'], $sess_user_info->ptoken, ['app' => 'custom']), true);
        if ($existing_room['success'] && empty($existing_room['output'])) {
            $result = json_encode(create_room_info($return, $sess_user_info->ptoken));
        } else {
            $result = json_encode(['success' => false, 'message' => 'Room already exists']);
        }
    } else {
        $result = json_encode(['success' => false, 'message' => 'Please fill all the fields']);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo $result;
    die();
}

function taoh_rooms_get()
{
    $taoh_call = 'core.network.get';
    $limit_default = 10;
    $offset = isset($_POST['offset']) && is_numeric($_POST['offset']) ? $_POST['offset'] * $limit_default : 0;
    $limit = isset($_POST['limit']) && is_numeric($_POST['limit']) ? $_POST['limit'] : 10;
    $search = $_POST['search'] ?? '';
    $taoh_vals = array(
        'mod' => 'rooms',
        'token' => taoh_get_dummy_token(),
        'local' => true,
        'type' => 'list',
        'offset' => $offset,
        'limit' => $limit,
        'search' => $search
    );
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
    $get_result = taoh_apicall_get($taoh_call, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');
    echo $get_result;
    taoh_exit();
}

function taoh_last_job_posted_date()
{
    $ptoken = (isset($_POST['ptoken']) && $_POST['ptoken']) ? $_POST['ptoken'] : '';
    $taoh_call = "jobs.last.postdate";
    $taoh_vals = array(
        "mod" => 'jobs',
        "ptoken" => $ptoken,
        'token' => taoh_get_dummy_token(1),
        //'debug'=> 1
    );

    $get_result = taoh_apicall_get($taoh_call, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');
    echo $get_result;
    taoh_exit();
}

function taoh_room_delete()
{
    $room_keyslug = $_POST['keyslug'];
    $taoh_vals = array(
        "ops" => 'room',
        'status' => 'delete',
        'key' => $room_keyslug,
        'keyslug' => $room_keyslug,
        'code' => TAOH_OPS_CODE,
        'debug'=>1
    );
    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');
    echo trim($result);
    die;
}

function taoh_get_all_my_rooms()
{
    header('Content-Type: application/json; charset=utf-8');

    $taoh_vals = array(
        "ops" => 'message',
        'key' => $_POST['ptoken'],
        'status' => 'rooms',
        'code' => TAOH_OPS_CODE,
        'timestamp' => $_POST['lastAllMessageTime'] ?? 0,
        //'country'   => trim($country),
    );

    if (isset($_POST['live'])) {
        $taoh_vals['live'] = 1;
    }

    if (isset($_POST['call_from']) && $_POST['call_from'] == 'footer') {
        //dont pass any local or secret or global
        //$taoh_vals['keyslug'] = '1e4fa9bf';
    } else {
        if (isset($_POST['local'])) {
            $taoh_vals['secret'] = TAOH_API_SECRET;
            $taoh_vals['local'] = $_POST['local'];
        }

        if (isset($_POST['global'])) {
            $taoh_vals['global'] = $_POST['global'];
        }
        if (isset($_POST['key'])) {
            $taoh_vals['keyslug'] = $_POST['key'];
        }
    }


    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $return = json_decode($result, true);
    $roomArray = array();
    if ($return['success']) {
        //$roomArray = $return['output'];
        foreach ($return['output'] as $key => $value) {
            //echo"<br>===========".$key;
            foreach ($value as $kk => $val) {
                //$data = json_decode($val);
                //echo"<br>=====kk======".$kk;

                if ($kk == 'timestamp')
                    $val = taoh_readable_stamp_short($val / 1000000);

                $roomArray[$key][$kk] = $val;
            }
        }
    }
    //echo'<pre>';print_r($roomArray);echo'</pre>';

    $response['success'] = true;
    $response['roomList'] = $roomArray;
    $now = (int)round(microtime(true) * 1000000);
    $response['last_update_time'] = $now;

    $data = json_encode($response);
    echo $data;
    taoh_exit();
}

function taoh_add_user_to_room()
{


    $taoh_vals = array(

        "ops" => 'status',
        'key' => $_POST['ptoken'],
        'status' => 'postroom',
        'value' => $_POST['addptoken'],
        'code' => TAOH_OPS_CODE,
    );
    if (isset($_POST['local'])) {
        $taoh_vals['secret'] = TAOH_API_SECRET;
        $taoh_vals['local'] = $_POST['local'];
    }

    if (isset($_POST['global'])) {
        $taoh_vals['global'] = $_POST['global'];
    }
    if (isset($_POST['key'])) {
        $taoh_vals['keyslug'] = $_POST['key'];
    }
    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);

    header('Content-Type: application/json; charset=utf-8');

    echo $result;
    taoh_exit();
}

function taoh_room_get_message_list()
{
    $taoh_vals = array(
        "ops" => 'message',
        'status' => 'chats',
        'key' => $_POST['ptoken'],
        'with' => $_POST['toptoken'],
        //'offset'    => $_POST['offset'],
        //'limit'     => $_POST['limit'],
        'limit' => 1000,
        'code' => TAOH_OPS_CODE,
        'timestamp' => $_POST['last_update_time'],
    );
    if (isset($_POST['local'])) {
        $taoh_vals['secret'] = TAOH_API_SECRET;
        $taoh_vals['local'] = $_POST['local'];
    }

    if (isset($_POST['global'])) {
        $taoh_vals['global'] = $_POST['global'];
    }
    if (isset($_POST['key'])) {
        $taoh_vals['keyslug'] = $_POST['key'];
    }

    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    $return = json_decode($result, true);
    $chatArray = array();
    if ($return['success']) {
        if ($return['output'] && count($return['output']) > 0) {
            $chatArray = $return['output'];
//      foreach($return['output'] as $key=>$val){
//          foreach($val as $k=>$v){
////            if($k == 'time')
////              $v = taoh_readable_stamp_short($v/1000000);
//
//            $chatArray[$key][$k] = $v;
//          }
//      }
        }
    }

    $response['success'] = true;
    $response['chat'] = $chatArray;
    $response['last_update_time'] = (int)round(microtime(true) * 1000000);

    $response['total_num'] = count($chatArray);

    header('Content-Type: application/json; charset=utf-8');

    $data = json_encode($response);
    echo $data;
    taoh_exit();
}

/* /Networking ajax Methods */

function taoh_announcement_save(){
    $make_array = [];
    parse_str($_POST['form_data'], $form);
    $make_array['feed_title'] = taoh_title_desc_encode($form['feed_title']);
    $make_array['feed_desc'] = taoh_title_desc_encode($form['feed_desc']);
    $make_array['post_name'] = taoh_user_full_name();
    $make_array['post_token'] = taoh_get_dummy_token();
    $make_array['post_avatar'] = $form['post_avatar'];
    $make_array['source'] = $form['source'];
    $make_array['sub_secret_token'] = $form['sub_secret_token'];
    $make_array['is_pin'] = $form['is_pin'];
    $conttoken = isset($_POST['conttoken'])?$_POST['conttoken']:'';
    $taoh_call = "core.content.post";
    $taoh_vals = array(
        'mod' => 'tao_tao',
        'type' => $_POST['type'],
        'app_name' => $_POST['type'],
        'conttoken' => $conttoken,
        'token' => taoh_get_dummy_token(),
        'toenter' => $make_array,
        'cache' => array("remove"=>array('feeds_*')),
        //'debug' => 1
    );
    if(isset($_POST['images'])){
        $images_get = [];
        foreach($_POST['images'] as $im_keys => $im_vals){
            if($im_vals != 'File upload failed.'){
                $images_get[$im_keys] = $im_vals;
            }
        }
        $taoh_vals['toenter']['images'] = $images_get;
    }else{
        $taoh_vals['toenter']['images'] = 'deleted';
    }
    if(isset($_POST['files'])){
        $files_get = [];
        foreach($_POST['files'] as $fi_keys => $fi_vals){
            if($fi_vals != 'File upload failed.'){
                $files_get[$fi_keys] = $fi_vals;
            }
        }
        $taoh_vals['toenter']['files'] = $files_get;
    }else{
        $taoh_vals['toenter']['files'] = 'deleted';
    }
    
    $result = taoh_apicall_post( $taoh_call,  $taoh_vals);
    echo $result;die;
}

function taoh_get_feed_list(){
    $offset_default = 0;
    $limit_default = 10;
    $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'all';
    $type = ( isset( $_POST['type'] ) && $_POST['type'] )? $_POST['type']:'';
    $search = ( isset( $_POST['search'] ) && $_POST['search'] )? $_POST['search']:'';
    $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
    $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? ($_POST['offset']):0;
    if($offset != 0){
        $offset = $offset - 1;
    }

    $key = defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET;

    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod'=>'core',
        'token'=>taoh_get_dummy_token(1),
        //'secret' => TAOH_API_SECRET,
        'ops'=>$ops,
        'type'=>$type,
        'key' => $key,
        'local' => true,
        'search'=>$search,
        'limit'=>$limit,
        'offset'=>$offset,
       // 'cache_required'=>0,
        'time'=>time(),
        'cache_time'=>600,
        'cache_name'=> 'feeds_'.hash('crc32',$search.$offset.$limit.$ops.$type.$key),
       // 'debug'=>1,
    );
   // $taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    echo $data;die();
}

function taoh_feed_delete(){
    $conttoken = isset($_POST['conttoken'])?$_POST['conttoken']:'';
    $taoh_call = "content.delete.data";
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(),
        'conttoken' => $conttoken,
        'cache' => array("remove"=>array('feeds_*','feed_'.$conttoken)),
    );
    //print_r($taoh_vals);die;
    //echo taoh_apicall_post_debug($taoh_call,  $taoh_vals);die();
    $result = taoh_apicall_post( $taoh_call,  $taoh_vals);
    echo $result;die;
}

function taoh_get_feed_detail(){
    $conttoken = isset($_POST['conttoken'])?$_POST['conttoken']:'';
    $type = ( isset( $_POST['type'] ) && $_POST['type'] )? $_POST['type']:'';
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        'mod'=>'core',
        'token'=>taoh_get_dummy_token(1),
        'ops'=>'detail',
        'type'=>$type,
        'conttoken' => $conttoken,
        'cache_time'=>600,
        'cache_name'=> 'feed_'.$conttoken,
        //'cache' => array("remove"=>array('skillchat_*')),
       // 'cache_required'=>0,
    );
    //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    echo $data;die();
}

function feed_like_put(){
    //https://ppapi.tao.ai/asks.save?mod=asks&token=C3kONdHX&conttoken=hcr9759qtuir
    $taoh_call = "content.save";
    $taoh_vals = array(
      'slug' => $_POST['slug'],
      'token' => taoh_get_dummy_token(),
      'conttoken' => $_POST['conttoken'],
    );

    $data = taoh_apicall_post($taoh_call, $taoh_vals);
    //taoh_delete_local_cache($remove);
    echo $data;
    die();
  }

  function feed_like_get(){
    
    $conttoken = $_POST['conttoken'];
    $taoh_call = "core.metrics";
    $taoh_vals = array(
      'mod' => 'metricsget',
      'app' => $_POST['app'],
      'token' => taoh_get_dummy_token(),
      'conttoken' => $conttoken,
      'cache_required' => 0,
      'time' => time(),
    );
    //print_r($taoh_vals);die;
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    echo $data;
    die();
  }

  function post_commentsform(){
    $data = $_POST['data'];
    header('Content-Type: text/html');
    taoh_feeds_comments_widget($data);
  }

  function taoh_create_update_custom_room(){
    //https://cachet4.tao.ai/taohnetworking.php?ops=status&status=updatemeets&
    //code=tc2asi3iida2&key=bbbbbbb&keyslug=111111111&type=custom_room&debug=1&app=networking&ptoken=02d7cgo61ll9
   
    $keyslug = hash('crc32', $_POST['room_name'].$_POST['parent_keyslug']);
    //$keyslug = hash('crc32', $_POST['network_title'].'_room'.$_POST['parent_keyslug']);
    $room_data = array();
    if(isset($_POST['room_name'])){
        $room_data = array( 'keyslug'=>$keyslug,
        'ptoken'=>$_POST['my_pToken'],
        'title'=>$_POST['room_name']);
    }
    $taoh_vals = array(
        'ops'               => 'status',
        'status'            => 'updatemeets',
        'code'              => TAOH_OPS_CODE,
        'key'               => $keyslug,
        'keyslug'           => $_POST['parent_keyslug'],
        'type'              => 'custom_room',
        'app'                => 'networking',   
        'ptoken'            => $_POST['my_pToken'],
        'value'             => addslashes(json_encode($room_data)),
        //'debug'             => 1
    );

    if(isset($_POST['delete'])){
        $taoh_vals['delete'] = 1;
        //$taoh_vals['delete'] = 1;
    }

    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);

    if(isset($_POST['join_room'])){
        $room_name = $_POST['room_name'].'_'.$_POST['parent_keyslug'];
        echo taoh_add_video_chat_for_room($room_name);
        die();
    }

    
    echo $room_data_json;
    die();
  }

  function taoh_get_custom_rooms(){

    //https://cachet4.tao.ai/taohnetworking.php?ops=status&status=getmeets&code=tc2asi3iida2
    //&key=aaaaaaaaa&keyslug=111111111&type=custom_room&debug=1&app=networking
    
    $taoh_vals = array(
        'ops'               => 'status',
        'status'            => 'getmeets',
        'code'              => TAOH_OPS_CODE,
        'key'               => $_POST['my_pToken'],
        'ptoken'            => $_POST['my_pToken'],
        'keyslug'           => $_POST['parent_keyslug'],
        'type'              => 'custom_room',
        'app'                => 'networking',       
        //'debug'             => 1
    );

    if(isset( $_POST['room_query'])){
        $taoh_vals['search'] = $_POST['room_query'];
    }
    $room_data_json = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');

    $room_data = json_decode($room_data_json, true);
    //$result = json_decode($room_data, true);
    //echo'<pre>';print_r($room_data);echo'</pre>';die();
    echo $room_data_json;
    die();
  }

  function taoh_create_video(){
    $room_name = $_POST['video_name'];
    $room_id = $_POST['room_id'];
    
     $combined = $_POST['video_name'].''.time();
    

    // Calculate the CRC32 hash of the combined string
    $crc = crc32($combined);

    $my_link = TAOH_VIDEO_CONF_LINK . urlencode($room_name). '-room-' . $room_id . '-'.$crc;

    $result = json_encode(array(
        'my_link' => $my_link,
        
    ));
    header('Content-Type: application/json; charset=utf-8');

    echo $result;
    taoh_exit();
  }

  function taoh_add_video_chat_for_room($room_name)
{
    
    // Calculate the CRC32 hash of the combined string
    $crc = '';
    

    $my_link = TAOH_VIDEO_CONF_LINK . urlencode($room_name). '_room' . $crc . '';
    $other_link = TAOH_VIDEO_CONF_LINK . urlencode($room_name) . '_room' . $crc . '';

    $result = json_encode(array(
        'my_link' => $my_link,
        'other_link' => $other_link
    ));
    header('Content-Type: application/json; charset=utf-8');

    echo $result;
    taoh_exit();
}


/*============================================= New Networking ================================================================*/

function taoh_create_channel()
{
    $channel_name = trim(($_POST['channelname'] ?? ''));
    $channel_description = $_POST['channeldescription'];
    $channel_id = generateSecureSlug($channel_name, 16);
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1; // 1 = discussion channel, 2 = 1-1 channel, 3 = room channel

    if (!empty($channel_id) && !empty($channel_type)) {
        $taoh_vals = array(
            "ops" => 'channel',
            'action' => 'create',
            'code' => TAOH_OPS_CODE,
            'key' => $_POST['key'],
            'ptoken' => $_POST['ptoken'],
            'room_id' => $_POST['room_id'],
            'channel_id' => $channel_id,
            'channel_type' => $channel_type,
            'channel_name' => $channel_name,
            'channel_description' => $channel_description,
            'channel_passcode' => $_POST['channelpasscode'] ?? '',
            'channel_ticket_type' => $_POST['channel_ticket_type'] ?? '', 
            'channel_created_by' => 'user',    
            'channel_data' => [],
            'type' => TAOH_CHAT_FORUM ?? 3,
            'token' => taoh_get_api_token(1)
        );
        if(isset($_POST['channel_video_url']) && $_POST['channel_video_url'] != ''){
            $taoh_vals['channel_video_url'] = $_POST['channel_video_url'];
        }
         if(isset($_POST['channel_video']) && $_POST['channel_video'] != ''){
            $taoh_vals['channel_video'] = $_POST['channel_video'];
        }
        $taoh_vals['redis_action'] = 'channel_add';
        $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
        $taoh_vals['cache'] = ['remove' => [
            'room' => $_POST['room_id'],
            'channel_id' => $taoh_vals['channel_id'],
            'channel_type' => $taoh_vals['channel_type']
        ]];

      //      $taoh_vals['debug'] = 1;
        $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
        $return = json_decode($result, true);

        echo json_encode($return);
    }
}

function taoh_channel_send_message()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;    

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'add_message',
        'key' => $_POST['ptoken'],
        'keyslug' => $_POST['key'],
        'with' => $_POST['other_ptoken'],
        'message' => urlencode($_POST['message']),
        'user_type' => $_POST['user_type'] ?? 'user',
        'parent_id' => $_POST['parent_id'] ?? 0,
        'channel_id' => $_POST['channel_id'] ?? '',
        'channel_type' => $channel_type,
        'event_token' => $_POST['event_token'] ?? '', 
        'type' => TAOH_CHAT_FORUM ?? 3,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_api_token(1),
        'country' => $_POST['country'],
        'my_link' => $_POST['my_link'],
        'secret' => TAOH_API_SECRET,
    );

    if (!empty($taoh_vals['parent_id'])) {
        $taoh_vals['redis_action'] = 'channel_message_added';
        $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
        $taoh_vals['cache'] = ['remove' => [
            'room' => $taoh_vals['keyslug'],
            'channel_id' => $taoh_vals['channel_id'],
            'channel_type' => $taoh_vals['type'] ?? 3,
            'parent_id' => $taoh_vals['parent_id']
        ]];
    }


    //$taoh_vals['debug'] = 1;

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);

    echo json_encode($return);
}

function taoh_direct_send_message()
{
    $channel_type = TAOH_CHANNEL_DIRECT_MESSAGE ?? 2;

    $other_ptoken  = $_POST['other_ptoken'];
      

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'add_message',
        'key' => $_POST['ptoken'],
        'keyslug' => $_POST['key'],
        'with' => $other_ptoken,
        'message' => urlencode($_POST['message']),
        'user_type' => $_POST['user_type'] ?? 'user',       
        'channel_id' => $_POST['channel_id'] ?? '',
        'channel_type' => $channel_type,
        'type' => TAOH_CHAT_NETWORK ?? 0,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_api_token(1),
    );

    /*$taoh_vals['redis_action'] = 'direct_message_added';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['cache'] = ['remove' => [
        'room' => $taoh_vals['keyslug'],
        'channel_id' => $taoh_vals['channel_id'],
        'channel_type' => $taoh_vals['type']
    ]];*/

    //$taoh_vals['debug'] = 1;
    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);

    echo json_encode($return);
}

function taoh_channel_like_message()
{
    //
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'like_message',
        'key' => $_POST['ptoken'],
        'keyslug' => $_POST['key'],
        'with' => $_POST['other_ptoken'],
        'message_id' => $_POST['message_id'],
        'message_key' => $_POST['message_key'],
        'user_type' => $_POST['user_type'] ?? 'user',
        'channel_id' => $_POST['channel_id'] ?? '',
        'channel_type' => $channel_type,
        'type' => TAOH_CHAT_FORUM ?? 3,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_api_token(1),
        'emoji' => $_POST['emoji'],
        'emoji_from' => $_POST['emoji_from'],
        //'debug' => 1
    );

    $taoh_vals['redis_action'] = 'like_message';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['cache'] = ['remove' => [
        'room' => $taoh_vals['keyslug'],
        'channel_id' => $taoh_vals['channel_id'],
        'message_key' => $taoh_vals['message_key'],
        'message_id' => $taoh_vals['message_id'],
        'channel_type' => $taoh_vals['channel_type'],
    ]];   

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_channel_pin_message()
{
    //
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'pin_message',
        'key' => $_POST['ptoken'],
        'keyslug' => $_POST['key'],
        'with' => $_POST['other_ptoken'],
        'message_id' => $_POST['message_id'],
        'message_key' => $_POST['message_key'],
        'user_type' => $_POST['user_type'] ?? 'user',
        'channel_id' => $_POST['channel_id'] ?? '',
        'channel_type' => $channel_type,
        'type' => TAOH_CHAT_FORUM ?? 3,
        'code' => TAOH_OPS_CODE,
        'sent_time' => $_POST['sent_time'],
        'token' => taoh_get_api_token(1),
        'pin' => $_POST['pin'],
        'pin_from' => $_POST['pinFrom'], 
        'chat_with' => $_POST['chatWith'],
        'unpin_old' => $_POST['unpinOld'],
        //'debug' => 1
    );

    $taoh_vals['redis_action'] = 'pin_message';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['cache'] = ['remove' => [
        'room' => $taoh_vals['keyslug'],
        'channel_id' => $taoh_vals['channel_id'],
        'message_key' => $taoh_vals['message_key'],
        'message_id' => $taoh_vals['message_id'],
        'channel_type' => $taoh_vals['channel_type'],
        'pin_from' => $taoh_vals['pin_from'], 
        'chat_from' => $taoh_vals['key'], 
        'chat_with' => $taoh_vals['chat_with'], 
    ]];   

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_get_data()
{
    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_get_data',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE,
        'token' => taoh_get_api_token(1),
        'timestamp' => $_POST['timestamp'],
        //'debug' => 1,
    );

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_add_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_add_user',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1
    ); 

    $taoh_vals['redis_action'] = 'speed_networking_add_user';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['speed_networking'] = ['data' => [
        'room' => $taoh_vals['keyslug'],
        'ptoken' => $taoh_vals['key'],
    ]];

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_block_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_block_user',
        'key' => $_POST['key'],
        'chatWith' => $_POST['chatwith'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE
    );
    
    $taoh_vals['redis_action'] = 'speed_networking_block_user';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['speed_networking'] = ['data' => [
        'room' => $taoh_vals['keyslug'],
        'chat_from' => $taoh_vals['key'],
        'chat_with' => $taoh_vals['chatWith'],
    ]];

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_connect_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_connect_user',
        'key' => $_POST['key'],
        'chatWith' => $_POST['chatwith'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1
    ); 
    $taoh_vals['redis_action'] = 'speed_networking_connect';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['speed_networking'] = ['data' => [
        'room' => $taoh_vals['keyslug'],
        'chat_from' => $taoh_vals['key'],
        'chat_with' => $taoh_vals['chatWith'],
    ]];    

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_connect_user_update()
{
    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_connect_user_update',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'chatWith' => $_POST['chatwith'],
        'code' => TAOH_OPS_CODE,
        'status' => $_POST['status'],
        'channel_id' => $_POST['channel_id'],
        'channel_type' => $_POST['channel_type'],
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_get_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_get_user',
        'key' => $_POST['key'],
        'direction' => $_POST['direction'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE
    ); 

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_speed_networking_connect_user_get()
{
    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_connect_user_get',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE
    ); 
    
    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_create_channel_from_ticket()
{    

    if($_POST['streaming_link'] != '') {
            $channel_name = 'Presentation Room';
            $channel_description = 'Presentation Room';
            
            $channel_type =  1; // 1 = discussion channel, 2 = 1-1 channel, 3 = ticket channel
            $channel_id = generateSecureSlug($_POST['room_id'].'watch-party'.$channel_name, 16);
            
            $taoh_vals = array(
                "ops" => 'channel',
                'action' => 'create',
                'code' => TAOH_OPS_CODE,
                'key' => $_POST['key'],
                'ptoken' => $_POST['ptoken'],
                'room_id' => $_POST['room_id'],
                'channel_created_by' => 'system',
                'channel_id' => $channel_id,
                'channel_name' => $channel_name,
                'channel_type' => $channel_type,
                'channel_description' => 'watch-party',
                'channel_data' => [],
                'type' => TAOH_CHAT_FORUM ?? 3,
                'token' => taoh_get_api_token(1),
               //'debug' =>1,
            );
                
            $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            $return = json_decode($result, true);
             
    }
    //die();
    if ($_POST['livenow_channels'] != '') {

        $livenow_channels = $_POST['livenow_channels'];
        $livenow_channels_array = json_decode($livenow_channels, true);
        
        foreach($livenow_channels_array as $key => $value) {

            $channel_name = $value;
            $channel_description = $value;
            $channel_type =  1; // 1 = discussion channel, 2 = 1-1 channel, 3 = ticket channel
            $channel_id = generateSecureSlug($_POST['room_id'].$channel_name, 16);
            
            $taoh_vals = array(
                "ops" => 'channel',
                'action' => 'create',
                'code' => TAOH_OPS_CODE,
                'key' => $_POST['key'],
                'ptoken' => $_POST['ptoken'],
                'room_id' => $_POST['room_id'],
                'channel_created_by' => 'system',
                'channel_id' => $channel_id,
                'channel_name' => $channel_name,
                'channel_type' => $channel_type,
                'channel_description' => $channel_description,
                'channel_data' => [],
                'type' => TAOH_CHAT_FORUM ?? 3,
                'token' => taoh_get_api_token(1),
            // 'debug' =>1,
            );
                
            $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            $return = json_decode($result, true);
            
        }

    } else {

            if ($_POST['ticket_types'] != '') {
            $ticket_types = $_POST['ticket_types'];
            $tickets = json_decode($ticket_types, true);
            //echo'<pre>';print_r($tickets);echo'</pre>';die();
            if(!empty($tickets)){
            foreach ($tickets as $key => $value) {
                $channel_name = $value['title'];
                $channel_description = $value['description'];
                $ticket_id = $value['slug'];
                $channel_type = TAOH_CHANNEL_TICKET_TYPE ?? 3; // 1 = discussion channel, 2 = 1-1 channel, 3 = ticket channel
                $channel_id = generateSecureSlug($_POST['room_id'].$ticket_id . $channel_name, 16);

                //echo "<br>========".$channel_name;
                //echo "<br>========".$channel_id;
                $taoh_vals = array(
                    "ops" => 'channel',
                    'action' => 'create',
                    'code' => TAOH_OPS_CODE,
                    'key' => $_POST['key'],
                    'ptoken' => $_POST['ptoken'],
                    'room_id' => $_POST['room_id'],
                    'channel_id' => $channel_id,
                    'channel_created_by' => 'system',
                    'channel_name' => $channel_name,
                    'channel_type' => $channel_type,
                    'channel_passcode' => '',
                    'channel_ticket_type'=>'',
                    'channel_description' => $channel_description,
                    'channel_data' => [],
                    'type' => TAOH_CHAT_FORUM ?? 3,
                    'token' => taoh_get_api_token(1)
                );

                $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
                // $return = json_decode($result, true);
            }
            }

        }

        $is_event_channel = 0;
        if ($_POST['event_channels'] != '') {
            $event_channels = $_POST['event_channels'];
            $event_channels_data = json_decode($event_channels, true);
            if(!empty($event_channels_data)){
            foreach ($event_channels_data as $key => $value) {
                $is_event_channel = 1;
                $channel_name = $value['channel_name'];
                $channel_description = $value['channel_desc'];
            
                $channel_type = TAOH_CHANNEL_TICKET_TYPE ?? 3; // 1 = discussion channel, 2 = 1-1 channel, 3 = ticket channel
                $channel_id = generateSecureSlug($_POST['room_id'] . $channel_name, 16);
                $channel_video = $value['video_channel'];
                //echo "<br>========".$channel_name;
                //echo "<br>========".$channel_id;
                $taoh_vals = array(
                    "ops" => 'channel',
                    'action' => 'create',
                    'code' => TAOH_OPS_CODE,
                    'key' => $_POST['key'],
                    'ptoken' => $_POST['ptoken'],
                    'room_id' => $_POST['room_id'],
                    'channel_created_by' => 'system',
                    'channel_id' => $channel_id,
                    'channel_name' => $channel_name,
                    'channel_type' => $channel_type,
                    'channel_video' => $channel_video,
                    'channel_passcode' => '',
                    'channel_ticket_type'=>'',
                    'channel_description' => $channel_description,
                    'channel_data' => [],
                    'type' => TAOH_CHAT_FORUM ?? 3,
                    'token' => taoh_get_api_token(1)
                );

                $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            // $return = json_decode($result, true);
            }
            }
        }
        if ($is_event_channel == 0) {
            
            $basic_channel_array = array(
                
                array('title'=>'#general', 'description'=>'📢 Announcements, updates, and all-community chatter.'),
                array('title'=>'#intros', 'description'=>'🎤 Name, role, and what you\'re exploring.'),
                array('title'=>'#coffee-chats', 'description'=>'💬 Spark a convo. Ask, share, or riff.'),
                array('title'=>'#help-wanted', 'description'=>'🙋 Need a job, collab, or support? Post it here.'),
                array('title'=>'#industry-room-tech', 'description'=>'💻 Tech talks, trends, and connections.'),
                

            );
            //error_reporting(E_ALL);
            foreach($basic_channel_array as $key => $value){
                $channel_name = $value['title'];
                $channel_description = $value['description'];
                $channel_type =  1; // 1 = discussion channel, 2 = 1-1 channel, 3 = ticket channel
                $channel_id = generateSecureSlug($_POST['room_id'].$channel_name, 16);
                
                $taoh_vals = array(
                    "ops" => 'channel',
                    'action' => 'create',
                    'code' => TAOH_OPS_CODE,
                    'key' => $_POST['key'],
                    'ptoken' => $_POST['ptoken'],
                    'room_id' => $_POST['room_id'],
                    'channel_id' => $channel_id,
                    'channel_created_by' => 'system',
                    'channel_name' => $channel_name,
                    'channel_type' => $channel_type,
                    'channel_description' => $channel_description,
                    'channel_data' => [],
                    'type' => TAOH_CHAT_FORUM ?? 3,
                    'token' => taoh_get_api_token(1),
                // 'debug' =>1,
                );

                $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
                $return = json_decode($result, true);                
            }
        }       

        //default channel
       /* if(ORGANIZER_CHANNEL_ENABLE){
            $taoh_vals = array(
                "ops" => 'channel',
                'action' => 'create',
                'code' => TAOH_OPS_CODE,
                'key' => $_POST['key'],
                'ptoken' => $_POST['ptoken'],
                'room_id' => $_POST['room_id'],
                'channel_created_by' => 'system',
                'channel_id' => $_POST['ptoken'].'-organizer',
                'channel_name' => $_POST['ptoken'].' Organizer',
                'channel_type' => TAOH_CHANNEL_DIRECT_MESSAGE ?? 2,
                'channel_description' => 'Chat with the organizer of this event.',
                'channel_data' => [$_POST['ptoken'], 'organizer'],
                'type' => TAOH_CHAT_NETWORK ?? 0,
                'token' => taoh_get_api_token(1),
            // 'debug' =>1,
            );
                
            $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            $return = json_decode($result, true);
        }*/

    }    

    echo json_encode($return);
}

function taoh_create_channel_with_organizer(){
    $sender = $_POST['ptoken'];
    $target = $_POST['chatwith'];
    $room_id = $_POST['room_id'];

    
    if (!empty($sender) && !empty($target)) {
       
        $channel_name = $_POST['ptoken'] . ' - organizer';
        $channel_description = 'Chat with the organizer of this event.';
        $channel_slug_data = [$room_id, $sender, $target];
        asort($channel_slug_data);
        //$channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_id = $_POST['channel_id'] ?? generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = 1; // 1 = discussion channel, 2 = 1-1 channel, 3 = room channel
        $channel_data = [$sender, $target];

        if (!empty($channel_id) && !empty($channel_type)) {
            $taoh_vals = array(
                "ops" => 'channel',
                'action' => 'create',
                'code' => TAOH_OPS_CODE,
                'key' => $_POST['key'],
                'ptoken' => $_POST['ptoken'],
                'room_id' => $_POST['room_id'],
                'channel_id' => $channel_id,
                'channel_type' => $channel_type,
                'channel_name' => $channel_name,
                'channel_created_by' => 'system',
                'channel_description' => $channel_description,
                'channel_passcode' => '',
                'channel_ticket_type' =>'',
                'channel_data' => $channel_data,
                'type' => TAOH_CHAT_FORUM ?? 3,
                'token' => taoh_get_api_token(1)
            );

        //    $taoh_vals['debug'] = 1;
            $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            // $return = json_decode($result, true);

            $data = array(
                'success' => true,
                'channel_id' => $channel_id,
                'channel_name' => $channel_name,
                'channel_data' => $channel_data,
            );
            echo json_encode($data);
        }
    }

}

function taoh_create_channel_for_1_1()
{
    $sender = $_POST['ptoken'];
    $target = $_POST['chatwith'];
    $room_id = $_POST['room_id'];

    if (!empty($sender) && !empty($target)) {
        $sender_name = trim(($_POST['loggedin_chatname'] ?? ''));
        $target_name = trim(($_POST['chatwith_chatname'] ?? ''));

        $channel_name = $sender_name . ' - ' . $target_name;
        $channel_description = $room_id . ' - ' . $sender_name . ' - ' . $target_name;
        $channel_slug_data = [$room_id, $sender, $target];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = TAOH_CHANNEL_DIRECT_MESSAGE ?? 2; // 1 = discussion channel, 2 = 1-1 channel, 3 = room channel
        $channel_data = [$sender, $target];

        if (!empty($channel_id) && !empty($channel_type)) {
            $taoh_vals = array(
                "ops" => 'channel',
                'action' => 'create',
                'code' => TAOH_OPS_CODE,
                'key' => $_POST['key'],
                'ptoken' => $_POST['ptoken'],
                'room_id' => $_POST['room_id'],
                'channel_id' => $channel_id,
                'channel_type' => $channel_type,
                'channel_name' => $channel_name,
                'channel_created_by' => 'system',
                'channel_description' => $channel_description,
                'channel_passcode' => '',
                'channel_ticket_type' =>'',
                'channel_data' => $channel_data,
                'type' => TAOH_CHAT_NETWORK ?? 0,
                'token' => taoh_get_api_token(1)
            );

    //        $taoh_vals['debug'] = 1;
            $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
            // $return = json_decode($result, true);

            $data = array(
                'success' => true,
                'channel_id' => $channel_id,
                'channel_name' => $channel_name,
                'channel_data' => $channel_data,
            );
            echo json_encode($data);
        }
    }
}

function taoh_track_activities(){
    $taoh_vals = array(
        "ops" => 'activity',
        "action" => 'saveActivity',
        'room_id' => $_POST['room_id'],
        'key' => $_POST['ptoken'],
        'track_data' => json_encode($_POST['track_data']), 
        'code' => TAOH_OPS_CODE,
        //'debug' => 1,
    );
    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
}

function taoh_get_activities(){
    $taoh_vals = array(
        "ops" => 'activity',
        "action" => 'getActivity',
        'room_id' => $_POST['room_id'],
        'key' => $_POST['ptoken'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1,
    );
    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');

    echo $result;
    taoh_exit();
}

/*============================================= /New Networking ================================================================*/

/*===================== Networking 3.0 =====================*/

/*function taoh_ntw_get_timestamps()
{
    $taoh_vals = array(
        "ops" => 'other_action',
        'action' => 'get_timestamps',
        'roomSlug' => $_POST['roomslug'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1,
        'key' => $_POST['key'],
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}*/

function taoh_ntw_get_room_stamp()
{
    $taoh_vals = array(
        "ops" => 'room',
        'action' => 'get_room_stamp',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'code' => TAOH_OPS_CODE,
        'key' => '1', // $_POST['key']
        'cfcc5' => 1,
    );

//    $taoh_vals['debug'] = 1;
//    echo taoh_get(TAOH_CHAT_NET_URL, $taoh_vals);exit();

//    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $result = taoh_get(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_get_room_channel_stamp()
{
    $taoh_vals = array(
        "ops" => 'room',
        'action' => 'get_room_channel_stamp',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'channelType' => $_POST['channel_type'] ?? 1,
        'timestamp' => $_POST['timestamp'] ?? 0,
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
    );

    if (isset($_POST['global_slug']) && !empty($_POST['global_slug'])) {
        $taoh_vals['globalSlug'] = $_POST['global_slug'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_create_channel() {
    $roomslug = $_POST['roomslug'] ?? '';
    $keyword = $_POST['keyword'] ?? 'club';
    $key = $_POST['key'];
    $channel_id = $_POST['channel_id'] ?? 0;
    $channel_type = $_POST['channel_type'] ?? 1;
    $channel_data = $_POST['channel_data'] ?? '';
    $channel_members = json_decode($_POST['channel_members'] ?? '[]', true);
    $channel_passcode = $_POST['channel_passcode'] ?? '';
    $channel_ticket_type = $_POST['channel_ticket_type'] ?? '';
    $channel_visibility = $_POST['channel_visibility'] ?? 'public';

    if (!$roomslug || !$keyword || !$channel_id || !$channel_data) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        return;
    }

    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'create_channel',
        'roomSlug' => $roomslug,
        'keyword' => $keyword,
        'channelId' => $channel_id,
        'channelType' => $channel_type,
        'channelData' => $channel_data,
        'visibility' => $channel_visibility,
        'code' => TAOH_OPS_CODE,
        'key' => $key
    );

    if (!empty($channel_members)) {
        $taoh_vals['channelMembers'] = $channel_members;
    }

    if (!empty($channel_passcode)) {
        $taoh_vals['channelEncryptPasscode'] = openEncrypt($channel_passcode);
    }

    if (!empty($channel_ticket_type)) {
        $taoh_vals['channelTicketType'] = $channel_ticket_type;
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_get_channels()
{
    $user_info_obj = taoh_user_all_info();
    $ptoken = $user_info_obj?->ptoken ?? '';

    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'get_channel',
        'fetchType' => 'all',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'ptoken' => $ptoken,
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'q' => $_POST['q'] ?? '',
        'limit' => 20,
        'offset' => 0,
    );

    if (isset($_POST['channel_id']) && !empty($_POST['channel_id'])) {
        $taoh_vals['channel_id'] = $_POST['channel_id'];
    }

    if (isset($_POST['type']) && !empty($_POST['type'])) {
        $taoh_vals['type'] = $_POST['type'];
    }

    if (isset($_POST['channel_ticket_type']) && !empty($_POST['channel_ticket_type'])) {
        $taoh_vals['channel_ticket_type'] = $_POST['channel_ticket_type'];
    }

    if (isset($_POST['global_slug']) && !empty($_POST['global_slug'])) {
        $taoh_vals['globalSlug'] = $_POST['global_slug'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_get_user_channels()
{
    $user_info_obj = taoh_user_all_info();
    $ptoken = $user_info_obj?->ptoken ?? '';

    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'get_channel',
        'fetchType' => 'user',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'ptoken' => $ptoken,
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'q' => $_POST['q'] ?? '',
    );

    if (isset($_POST['type']) && !empty($_POST['type'])) {
        $taoh_vals['type'] = $_POST['type'];
    }

    if (isset($_POST['channel_ticket_type']) && !empty($_POST['channel_ticket_type'])) {
        $taoh_vals['channel_ticket_type'] = $_POST['channel_ticket_type'];
    }

    if (isset($_POST['global_slug']) && !empty($_POST['global_slug'])) {
        $taoh_vals['globalSlug'] = $_POST['global_slug'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_join_channel()
{
    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'join_channel',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
    );

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_add_channel_members()
{
    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'add_channel_members',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'members' => $_POST['members'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
    );

    if (isset($_POST['global_slug']) && !empty($_POST['global_slug'])) {
        $taoh_vals['globalSlug'] = $_POST['global_slug'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_remove_channel_members()
{
    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'remove_channel_members',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'members' => $_POST['members'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
    );

    if (isset($_POST['global_slug']) && !empty($_POST['global_slug'])) {
        $taoh_vals['globalSlug'] = $_POST['global_slug'];
    }

//    $taoh_vals['debug'] = 1;
//    echo taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);exit();

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    echo $result;
}

function taoh_ntw_delete_channel() {

    $room       = $_POST['roomslug'] ?? '';
    $channel_id = $_POST['channel_id'] ?? 0;
    $channel_type = $_POST['channel_type'] ?? 1;
    $key        = $_POST['key'] ?? '';
    $keyword = $_POST['keyword'];

    $taoh_vals = array(
        "ops"       => 'channel',
        "action"    => 'delete_channel',
        "roomSlug"  => $room,
        "channelId" => $channel_id,
        'channelType' => $channel_type,
        "code"      => TAOH_OPS_CODE,
        "key"       => $key,
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_add_broadcast_message() {

    $room    = $_POST['roomslug'] ?? '';
    $message = trim($_POST['message'] ?? '');
    $key     = $_POST['key'] ?? '';
    $keyword = $_POST['keyword'] ?? 'club';

    // Basic validation
    if ($room === '' || $message === '') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Room or message cannot be empty'
        ]);
        return;
    }

    $taoh_vals = array(
        "ops"      => "message",
        "action"   => "add_org_message",
        "roomSlug" => $room,
        "message"  => $message,
        "code"     => TAOH_OPS_CODE,
        "key"      => $key,
        "keyword"  => $keyword,
        // "debug" => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);

    echo json_encode($return);
}

function taoh_ntw_get_broadcast_message() {

    $room    = $_POST['roomslug'] ?? '';
    $timestamp = $_POST['timestamp'] ?? '';
    $key     = $_POST['key'] ?? '';
    $keyword = $_POST['keyword'] ?? 'club';

    // Basic validation
    if ($room === '' || $timestamp === '') {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Room or timestamp cannot be empty'
        ]);
        return;
    }

    $taoh_vals = array(
        "ops"      => "message",
        "action"   => "get_org_message",
        "roomSlug" => $room,
        "timestamp"  => $timestamp,
        "code"     => TAOH_OPS_CODE,
        "key"      => $key,
        "keyword"  => $keyword,
        // "debug" => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);

    echo json_encode($return);
}


function taoh_ntw_star_channel() {

    $room       = $_POST['roomslug'] ?? '';
    $channel_id = $_POST['channel_id'] ?? 0;
    $channel_type = $_POST['channel_type'] ?? 1;
    $key        = $_POST['key'] ?? '';
    $keyword = $_POST['keyword'];
    $star = $_POST['star'];

    $taoh_vals = array(
        "ops"       => 'channel',
        "action"    => 'star_channel',
        "roomSlug"  => $room,
        "channelId" => $channel_id,
        'channelType' => $channel_type,
        "code"      => TAOH_OPS_CODE,
        "star" => $star,
        "key"       => $key,
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function checkChannelPasscode() {
    $passcode = $_POST['passcode'] ?? '';
    $encryptedPasscode = $_POST['encrypted_passcode'] ?? '';

    $decryptedPasscode = openDecrypt($encryptedPasscode);
    $response = ['success' => $passcode === $decryptedPasscode];
    echo json_encode($response);
}

function taoh_ntw_send_message()
{
    $message_data = array(
        "text" => isset($_POST['message']) ? $_POST['message'] : "",
        "ptoken" => $_POST['key'],
        "timestamp" => time()
    );
    /*
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
    */
    $taoh_vals = array(
        "ops" => 'message',
        'action' => 'add_message',
        'roomSlug' => $_POST['roomslug'],
        'globalSlug' => $_POST['keyword'] ?? 'club',
        'keyword' => $_POST['keyword'] ?? 'club',
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
        'messageData' => json_encode($message_data),
        'taoh_secret' => $_POST['taoh_secret'],
        'room_title' => $_POST['room_title'],
        'source' => $_POST['source'],
        'channel_name' => $_POST['channel_name'] ?? '',
        'chatwith' => $_POST['chatwith'] ?? '',
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_get_channel_type()
{
    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'get_channel_type',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_get_channel_info()
{
    $taoh_vals = array(
        "ops" => 'channel',
        'action' => 'get_channel_info',
        'roomSlug' => $_POST['roomslug'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channel_id' => $_POST['channel_id'],
        'eventtoken' => $_POST['keyword'] ?? 'club',
        'type' => $_POST['channel_type'],
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_add_reply_message()
{
    $message_data = array(
        "text" => isset($_POST['message']) ? $_POST['message'] : "",
        "ptoken" => $_POST['key'],        
        "parent_message_id" => isset($_POST['parent_message_id']) ?  $_POST['parent_message_id'] : "",
        "timestamp" => time()
    );
    $taoh_vals = array(
        "ops" => 'message',
        'action' => 'add_reply_message',
        'parentMessageId' => $_POST['parent_message_id'],
        'roomSlug' => $_POST['roomslug'],
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
        'messageData' => json_encode($message_data),
        'keyword' => $_POST['keyword'] ?? 'club',
        'room_title' => $_POST['room_title'],
        'source' => $_POST['source'],
        'channel_name' => $_POST['channel_name'] ?? '',
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_pin_message()
{
    $taoh_vals = array(
        "ops" => 'message',
        'action' => 'pin_message',
        'messageId' => $_POST['message_id'],
        'roomSlug' => $_POST['roomslug'],
        'pToken' => $_POST['key'],
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'pinStatus' => $_POST['pin_status'],
        'channelId' => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_get_messages()
{
    $taoh_vals = array(
        "ops" => 'message',
        'action' => 'get_message',
        'roomSlug' => $_POST['roomslug'],
        'pToken' => $_POST['key'],
        'channelType' => $_POST['channel_type'] ?? 1,
        'code' => TAOH_OPS_CODE,
        'key' => $_POST['key'],
        'channelId' => $_POST['channel_id'],
        'lastTimestamp' => $_POST['last_timestamp'],
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);

    //https://cachet4.tao.ai/taoh_net.php?ops=message&action=get_message&roomSlug=bbc48bdb&channelId=111&offset=0&limit=10&code=tc2asi3iida2&debug=1&key=z3mbltb5mrf1
}

function taoh_ntw_delete_message() {
    $room       = $_POST['roomslug'] ?? '';
    $channel_id = $_POST['channel_id'] ?? 0;
    $channel_type = $_POST['channel_type'] ?? 1;
    $message_id = $_POST['message_id'] ?? 0;
    $key        = $_POST['key'] ?? '';

    if (!$room || !$channel_id || !$message_id) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        return;
    }

    $taoh_vals = array(
        "ops"       => 'message',
        "action"    => 'delete_message',
        "roomSlug"  => $room,
        "channelId" => $channel_id,
        'channelType' => $channel_type,
        "messageId" => $message_id,
        "code"      => TAOH_OPS_CODE,
        "key"       => $key,
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}


function taoh_ntw_speed_networking_get_data()
{
    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_get_data',
        'key' => $_POST['key'],
        'keyslug' => $_POST['roomslug'],
        'code' => TAOH_OPS_CODE,
        'token' => taoh_get_api_token(1),
        'timestamp' => $_POST['last_timestamp'],
        'channel_id' => $_POST['channel_id'],
        'channel_type' => $_POST['channel_type'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'listing' => $_POST['listing'] ?? 0,
        //'debug' => 1,
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_add_user_old()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'speed_networking_add_user',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE,
        'channel_id' => $_POST['channel_id'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'channel_type' => $_POST['channel_type'],
        'debug' => 1
    ); 

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_add_user()
{    
    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_add_user',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'channel_id' => $_POST['channel_id'],
        'keyword' => $_POST['keyword'] ?? 'club',
        'channel_type' => $_POST['channel_type'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1
    ); 

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_add_activity_channel()
{    
    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'add_activity_message',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'keyword' => $_POST['keyword'],
        'video_link_data' => urlencode($_POST['video_link_data']),
        'type' => TAOH_CHAT_NETWORK ?? 0,
        'code' => TAOH_OPS_CODE,
        'token' => taoh_get_api_token(1),
        //'debug' => 1,
    );

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_get_activity_channel()
{    
    $taoh_vals = array(
        "ops" => 'channel_message',
        'action' => 'get_activity_message',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'keyword' => $_POST['keyword'],
        'type' => TAOH_CHAT_NETWORK ?? 0,
        'code' => TAOH_OPS_CODE,
        'token' => taoh_get_api_token(1),
        //'debug' => 1,
    );

    $result = taoh_post(TAOH_CACHE_CHAT_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_block_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_block_user',
        'key' => $_POST['key'],
        'chatWith' => $_POST['chatwith'],
        'keyslug' => $_POST['keyslug'],
        'keyword' => $_POST['keyword'],
        'code' => TAOH_OPS_CODE
    );
    
    $taoh_vals['redis_action'] = 'speed_networking_block_user';
    $taoh_vals['redis_store'] = 'taoh_intaodb_NTW';
    $taoh_vals['speed_networking'] = ['data' => [
        'room' => $taoh_vals['keyslug'],
        'chat_from' => $taoh_vals['key'],
        'chat_with' => $taoh_vals['chatWith'],
    ]];

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_connect_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_connect_user',
        'key' => $_POST['key'],
        'keyword' => $_POST['keyword'],
        'channel_type' => $_POST['channel_type'],
        'channel_id' => $_POST['channel_id'],
        'chatWith' => $_POST['chatwith'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE,
        //'debug' => 1
    ); 

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_connect_user_update()
{
    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_connect_user_update',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'chatWith' => $_POST['chatwith'],
        'code' => TAOH_OPS_CODE,
        'status' => $_POST['status'],
        'channel_id' => $_POST['channel_id'],
        'channel_type' => $_POST['channel_type'],
        'keyword' => $_POST['keyword'] ?? 'club',        
        'videolink' => $_POST['videolink'],
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_connect_user_revoke()
{
    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_revoke',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'chatWith' => $_POST['chatwith'],
        'code' => TAOH_OPS_CODE,
        'status' => $_POST['status'],
        'channel_id' => $_POST['channel_id'],
        'channel_type' => $_POST['channel_type'],
        'keyword' => $_POST['keyword'] ?? 'club',        
        'videolink' => $_POST['videolink'],
        //'debug' => 1
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_get_user()
{
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_get_user',
        'key' => $_POST['key'],
        'direction' => $_POST['direction'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE
    ); 

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_speed_networking_connect_user_get()
{
    $taoh_vals = array(
        "ops" => 'speed_networking',
        'action' => 'speed_networking_connect_user_get',
        'key' => $_POST['key'],
        'keyslug' => $_POST['keyslug'],
        'code' => TAOH_OPS_CODE
    ); 
    
    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_react_message()
{
    //
    $channel_type = TAOH_CHANNEL_DISSCUSSION ?? 1;

    $taoh_vals = array(
        "ops"       => 'message',
        "action"    => 'react_message',
        "roomSlug"  => $_POST['room_slug'],
        "channelId" => $_POST['channel_id'],
        'channelType' => $_POST['channel_type'] ?? 1,
        "messageId" => $_POST['message_id'],
        "code"      => TAOH_OPS_CODE,
        "key"       => $_POST['ptoken'],
        'emoji' => $_POST['emoji'],
        'emoji_from' => $_POST['emoji_from'],
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_init_speed_networking() {
    $taoh_vals = array(
        "ops"       => 'speed_networking',
        "action"    => 'init_speed_networking',
        "roomSlug"  => $_POST['keyslug'],
        "key"       => $_POST['key'],        
        'channelType' => $_POST['channel_type'] ?? 6,
        "code"      => TAOH_OPS_CODE,
        'keyword' => $_POST['keyword'] ?? 'club',
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_ntw_get_connections() {
    $taoh_vals = array(
        "ops"       => 'other_action',
        "action"    => 'get_connections',
        "key"       => $_POST['key'],
        "code"      => TAOH_OPS_CODE,
        "search"    => $_POST['search'],
        //'debug' => 1
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    echo json_encode($return);
}

function taoh_forward_channel_transcript()
{
    //https://ppapi.tao.ai/sandbox/posttest/core.message.channel?mod=core&token=m20F2ftt&channel_id=61ae70e13288ced7
    $taoh_call = "core.message.channel";
    $taoh_vals = array(
        'mod' => 'core',
        'token' => taoh_get_api_token(1),
        'channel_id' => $_POST['channel_id'],
        'channel_type' => $_POST['channel_type'],
        'keyword' => $_POST['keyword'],
        'keyslug' => $_POST['keyslug'],
        'channel_name' => $_POST['channel_name'],
        'title' => $_POST['title'],
        //'cfcc90'=> 1,
        // 'debug'=>1,
    );

    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
    $get_result = taoh_apicall_post($taoh_call, $taoh_vals);
    header('Content-Type: application/json; charset=utf-8');
    echo $get_result;
    taoh_exit();
}