<?php
/**
 * Adapter for Events - to handle event networking related operations
 *
 */
require_once TAOH_APP_PATH . '/events/NtwAdapterEvents.php';


$contslug = taoh_parse_url(2);

$event_path = '/' . TAOH_SITE_CURRENT_APP_SLUG . '/d/' . $contslug;
if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_SITE_URL_ROOT . $event_path);
    taoh_exit();
}

$contslug_arr = explode('-', $contslug);
$eventtoken = array_pop($contslug_arr);

$user_info_obj = taoh_user_all_info();


$taoh_vals = array(
    'token' => taoh_get_api_token(1, 1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken,
    //'cfcc5h' => 1 ////cfcache newly added
);
//echo taoh_apicall_get_debug('events.event.get', $taoh_vals);die();

$result = taoh_apicall_get('events.event.get', $taoh_vals);
$response = taoh_get_array($result, true);

if (!$response['success']) {
    taoh_redirect(TAOH_SITE_URL_ROOT . $event_path);
    exit();
}

$event_arr = $response['output'];
$events_data = $event_arr['conttoken'] ?? [];
// Creating Room Info for Networking
$roomslug = '';

$eventsNtwAdapter = new NtwAdapterEvents();
$generate_room_slug_response = $eventsNtwAdapter->generateRoomSlug([
    'country_code' => $user_info_obj->country_code,
    'country_name' => $user_info_obj->country_name,
    'local_timezone' => $user_info_obj->local_timezone,
    'eventtoken' => $eventtoken,
    'country_locked' => $events_data['country_locked'] ?? 0,
]);

if (in_array($generate_room_slug_response['success'], [true, 'true'])) {
    $generated_roomslug = $generate_room_slug_response['roomslug'] ?? '';
    if(!empty($generated_roomslug)) {
//        $get_room_info_response = getRoomInfo($generated_roomslug, $ptoken);
//        $room_info_arr = json_decode($get_room_info_response, true);
//        if (in_array($room_info_arr['success'], [true, 'true']) && !empty($room_info_arr['output'])) {
//            $room_info = $room_info_arr['output'];
//            $roomslug = $room_info['room']['keyslug'] ?? '';
//        } else {
        // Room does not exist, create new room
        $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                'roomslug' => $generated_roomslug,
                'eventtoken' => $eventtoken]
        );


        //echo '<pre>';print_r($create_room_response);echo '</pre>';die();
        if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
            $room_info = $create_room_response['output'];
            if(isset($room_info['room'])) {
                $room_info['room']['keyword'] = $eventtoken;
            }
            $eventsNtwAdapter->createBulkRoomInfoChannels($room_info, $user_info_obj->ptoken);

            $roomslug = $room_info['room']['keyslug'] ?? '';
        }
//        }
    }
} else {
    // generate room slug failed
}

//------------/Creating Room Info for Networking-------------------------


if(!empty($roomslug)){
    $networking_room_url = TAOH_SITE_URL_ROOT . '/club/room/' . taoh_slugify($room_info['room']['title']) . '-' . $roomslug;

    if (!empty($_GET['exhbitor_id'] ?? '') && !empty($_GET['exhbitor_name'] ?? '')) {
        $channel_slug_data = [$eventtoken, 'exhibitor', $_GET['exhbitor_id']];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : 2;
        taoh_redirect($networking_room_url . '?chatwithchannelid=' . $channel_id.'&chatwithchanneltype='.$channel_type);
    } elseif (!empty($_GET['session_id'] ?? '') && !empty($_GET['session_name'] ?? '')) {
        $channel_slug_data = [$eventtoken, 'session', $_GET['session_id']];
        asort($channel_slug_data);
        $channel_id = generateSecureSlug(implode('_', $channel_slug_data), 16);
        $channel_type = defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : 7;
        taoh_redirect($networking_room_url . '?chatwithchannelid=' . $channel_id.'&chatwithchanneltype='.$channel_type);
    } else {
        if(isset($_GET['chatwith']) && trim($_GET['chatwith']) !='') {
            $chatwith = trim($_GET['chatwith']);
            taoh_redirect($networking_room_url . '?chatwith=' . $chatwith);
        } else {
            taoh_redirect($networking_room_url);
        }
    }
} else{
    taoh_redirect(TAOH_SITE_URL_ROOT . $event_path);
}
taoh_exit();