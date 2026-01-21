<?php

if(empty($eventtoken)){
    exit('Event token not provided');
}

$user_info_obj = taoh_user_all_info();

$taoh_vals = array(
    'ops' => 'status',
    'status' => 'deleteroompattern',
    'code' => TAOH_OPS_CODE,
    'key' => $user_info_obj->ptoken,
    'token' => taoh_get_api_token(1),
    'eventToken' => $eventtoken,
    'redis_store' => 'taoh_intaodb_NTW',
    'cache' => array('remove' => [])
);

//$taoh_vals['debug'] = 1;
//echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();

$delete_result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
$data = json_decode($delete_result, true);
if (in_array($data['success'], [true, 'true'])) {
    $deleted_roomslugs = is_array($data['output']) ? $data['output'] : [];

    foreach ($deleted_roomslugs as $deleted_roomslug){
        if(!empty($deleted_roomslug)) {
            $eventsNtwAdapter = new NtwAdapterEvents();
            $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                    'roomslug' => $deleted_roomslug,
                    'eventtoken' => $eventtoken]
            );
//        if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
//            $room_info = $create_room_response['output'];
//            $roomslug = $room_info['room']['keyslug'] ?? '';
//        }
        }
    }
}
