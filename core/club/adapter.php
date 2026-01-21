<?php
/**
 * Adapter for Club - to handle club networking related operations
 *
 */
require_once TAOH_CORE_PATH . '/club/NtwAdapterClub.php';


if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
    taoh_exit();
}

$user_info_obj = taoh_user_all_info();
$club_header_ptoken = $user_info_obj ? ($user_info_obj->ptoken ?? '') : '';

// Creating Room Info for Networking
$roomslug = '';

$clubNtwAdapter = new NtwAdapterClub();
$generate_room_slug_response = $clubNtwAdapter->generateRoomSlug([
    'country_code' => $user_info_obj->country_code,
    'country_name' => $user_info_obj->country_name,
    'local_timezone' => $user_info_obj->local_timezone,
    'country_locked' => 1,
]);
if (in_array($generate_room_slug_response['success'], [true, 'true'])) {
    $generated_roomslug = $generate_room_slug_response['roomslug'] ?? '';
    if (!empty($generated_roomslug)) {
//        $get_room_info_response = getRoomInfo($generated_roomslug, $club_header_ptoken);
//        $room_info_arr = json_decode($get_room_info_response, true);
//        if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
//            $room_info = $room_info_arr['output'];
//            $roomslug = $room_info['room']['keyslug'] ?? '';
//        } else {
            // Room does not exist, create new room
            $create_room_response = $clubNtwAdapter->constructAndCreateRoomInfo($user_info_obj, ['roomslug' => $generated_roomslug]);
            if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                $room_info = $create_room_response['output'];
                if(isset($room_info['room'])) {
                    $room_info['room']['keyword'] = "club";
                }
                $clubNtwAdapter->createBulkRoomInfoChannels($room_info, $user_info_obj->ptoken);

                $roomslug = $room_info['room']['keyslug'] ?? '';
            }
//        }
    }
} else {
    // generate room slug failed
}

//------------/Creating Room Info for Networking-------------------------


if(!empty($roomslug)){
    $room_title = $room_info['room']['title'] ?? 'Networking Room';
    $networking_room_url = TAOH_SITE_URL_ROOT . '/club/room/' . taoh_slugify($room_title) . '-' . $roomslug;
    taoh_redirect($networking_room_url);
} else{
    taoh_redirect(TAOH_SITE_URL_ROOT);
}
taoh_exit();