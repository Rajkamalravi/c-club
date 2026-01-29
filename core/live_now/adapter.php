<?php
/**
 * Adapter for Live - to handle live networking related operations
 *
 */
require_once TAOH_CORE_PATH . '/NtwAdapterLive.php';


if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
    taoh_exit();
}

$user_info_obj = taoh_user_all_info();

$live_now_data = [];

$live_now_url = TAOH_LIVE_NOW_URL . '?y=' . gmdate("YmdH");
$live_now_json = @file_get_contents($live_now_url);
$live_now_data_arr = json_decode($live_now_json, true);
if (!empty($live_now_data_arr) && in_array($live_now_data_arr['success'], [true, 'true']) && !empty($live_now_data_arr['output'])) {
    $live_now_data = $live_now_data_arr['output'];
}


// Creating Room Info for Networking
$roomslug = '';
if (taoh_user_is_logged_in() && !empty($live_now_data)) {
    $live_header_user_info_obj = taoh_user_all_info();
    $live_header_ptoken = $live_header_user_info_obj ? ($live_header_user_info_obj->ptoken ?? '') : '';

    $liveNtwAdapter = new NtwAdapterLive();

    $generate_room_slug_response = $liveNtwAdapter->generateRoomSlug([
        'country_code' => $live_header_user_info_obj->country_code,
        'country_name' => $live_header_user_info_obj->country_name,
        'local_timezone' => $live_header_user_info_obj->local_timezone,
        'country_locked' => 0,
        'title' => $live_now_data['title'] ?? 'Live Now',
    ]);
    if (in_array($generate_room_slug_response['success'], [true, 'true'])) {
        $generated_roomslug = $generate_room_slug_response['roomslug'] ?? '';
        if (!empty($generated_roomslug)) {
//            $get_room_info_response = getRoomInfo($generated_roomslug, $live_header_ptoken);
//            $room_info_arr = json_decode($get_room_info_response, true);
//            if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
//                $room_info = $room_info_arr['output'];
//                $roomslug = $room_info['room']['keyslug'] ?? '';
//            } else {
                // Room does not exist, create new room
                $create_room_response = $liveNtwAdapter->constructAndCreateRoomInfo($live_header_user_info_obj, [
                    'roomslug' => $generated_roomslug,
                    'live_now_data' => $live_now_data,
                    'country_locked' => 0,
                ]);
                if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                    $room_info = $create_room_response['output'];
                    if(isset($room_info['room'])) {
                        $room_info['room']['keyword'] = "live";
                    }
                    $liveNtwAdapter->createBulkRoomInfoChannels($room_info, $live_header_user_info_obj->ptoken);

                    $roomslug = $room_info['room']['keyslug'] ?? '';
                }
//            }
        }
    } else {
        // generate room slug failed
    }
}

//------------/Creating Room Info for Networking-------------------------


if (!empty($live_now_data) && !empty($roomslug)) {
    $live_now_title = $room_info['room']['title'] ?? 'Live Now';
    $join_now_url = TAOH_SITE_URL_ROOT . '/club/room/' . taoh_slugify($live_now_title) . '-' . $roomslug;

    taoh_redirect($join_now_url);
} else {
    taoh_redirect(TAOH_SITE_URL_ROOT);
}
taoh_exit();