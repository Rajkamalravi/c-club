<?php
/**
 * Adapter for dm - to handle dm networking related operations
 *
 */
require_once TAOH_CORE_PATH . '/NtwAdapterDM.php';

if (!taoh_user_is_logged_in()) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
    taoh_exit();
}

$dm_data = [];
$dm_data['title'] = 'Direct Message';
$dm_data['description'] = 'Direct Message';
$dm_room_slug = hash('crc32', 'dm-direct-message');


$roomslug = '';
if (taoh_user_is_logged_in()) {
    $dm_header_user_info_obj = taoh_user_all_info();
    $dm_header_ptoken = $dm_header_user_info_obj ? ($dm_header_user_info_obj->ptoken ?? '') : '';

    $dmNtwAdapter = new NtwAdapterDM();
    $create_room_response = $dmNtwAdapter->constructAndCreateRoomInfo($dm_header_user_info_obj, [
        'roomslug' => $dm_room_slug,
        'dm_data' => $dm_data,
        'country_locked' => 0,
    ]);
    if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
        $room_info = $create_room_response['output'];                    
        if(isset($room_info['room'])) {
            $room_info['room']['keyword'] = "dm";
        }
        //$dmNtwAdapter->createBulkRoomInfoChannels($room_info, $dm_header_user_info_obj->ptoken);

        $roomslug = $room_info['room']['keyslug'] ?? '';
    }        
}

//echo "test"; die;

//------------/Creating Room Info for Networking-------------------------

if (!empty($dm_data) && !empty($roomslug)) {
    $dm_now_title = $room_info['room']['title'] ?? 'DirectMessage';
    $join_now_url = TAOH_SITE_URL_ROOT . '/club/room/' . taoh_slugify($dm_now_title) . '-' . $roomslug;

    if (!empty($_GET['channel_id'] ?? '')) {
        $join_now_url .= '?chatwithchannelid=' . $_GET['channel_id'];
    }

    taoh_redirect($join_now_url);
} else {
    taoh_redirect(TAOH_SITE_URL_ROOT);
}
taoh_exit();