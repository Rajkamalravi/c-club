<?php
/*
locality [1 means global, uses local user timestamp for live/notlive]
visibility  [0 = public, 1 = private, 2 = protected]
*/
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$contslug = taoh_parse_url(2);
if ( ! taoh_user_is_logged_in() ){
    taoh_redirect( TAOH_SITE_URL_ROOT.'/profile'); taoh_exit();
}

$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
//$key_key = $sess_user_info->ptoken.'-dm';
$key_key = 'dm-direct-message';
$keyslug = hash('crc32', $key_key);

$room_info_arr = create_dm_room($keyslug,$sess_user_info->ptoken);
$room_status_arr = array( 'output' => $room_info_arr, 'success' => true );


//echo '<pre>';print_r($room_status_arr);exit();

$userslug = $keyslug;
$value = taoh_networking_getcell( $keyslug );
$value_arr = taoh_get_array( $value, true );
$temp_arr = $return;
unset( $return );
$return[ 'club_info' ] = $room_status_arr[ 'output' ];
unset( $temp_arr );
if ( isset( $value_arr[ 'output' ] ) && $value_arr[ 'output' ] ){    
    $return[ 'cell_info' ] = $value_arr[ 'output' ];
} else {
    $return[ 'cell_info' ] = array(
        'keyslug' => $keyslug,
        'user' => array(
            'ptoken' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken,
            'chat_name' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->chat_name,
            'avatar' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->avatar,
            'full_location' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->full_location,
            'coordinates' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->coordinates,
            'geohash' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->geohash,
            'local_timezone' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->local_timezone,
            'profile_type' => $profile,
            'site' => array(
                'source' => TAOH_SITE_URL_ROOT,
                'name' => TAOH_SITE_NAME_SLUG,
            )
        ),

    );
    $coordinates =  $return[ 'cell_info' ]['user' ]['coordinates'];
    if($coordinates !=''){
        $co_array = explode('::',$coordinates);
        $lat = $co_array[0];
        $long = $co_array[1];
    }
    $return[ 'cell_info' ][ 'user' ][ 'longitude' ] = $long;
    $return[ 'cell_info' ][ 'user' ][ 'latitude' ] = $lat;
    taoh_networking_postcell( $return[ 'cell_info' ] );
}

//echo '<pre>';print_r($room_status_arr);exit();

$url = TAOH_SITE_URL_ROOT . taoh_either_or($room_status_arr['output']['club']['links']['club'], $contslug);
if(isset($_GET['chatwith']) && $_GET['chatwith'] !='' ){
    $url = $url.'?chatwith='.$_GET['chatwith'];

}
//echo $url;die();
taoh_redirect($url);
taoh_exit();


$room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'message']);
$room_status_arr = taoh_get_array( $room_status, true );
if ( isset( $room_status_arr[ 'output' ] ) && $room_status_arr[ 'output' ] ){
    $return = $room_status_arr[ 'output' ];
} else {
    $user_ptoken = $sess_user_info->ptoken;
   
    $owner_json = taoh_get_user_info( $user_ptoken, 'cell' );
    $owner_arrar = taoh_get_array( $owner_json, true );
    $owner_arr = $owner_arrar['output']['user'];
    $return['app'] = 'message';
    create_room_info($return, $sess_user_info->ptoken);
}

// *** CLUB Room Info - End ***


// *** CLUB Cell Info - Start ***
$userslug = $keyslug;
$value = taoh_networking_getcell( $keyslug );
$value_arr = taoh_get_array( $value, true );
$temp_arr = $return;
unset( $return );
$return[ 'club_info' ] = $temp_arr;
unset( $temp_arr );
if ( isset( $value_arr[ 'output' ] ) && $value_arr[ 'output' ] ){    
    //$return[ 'cell_info' ] = $value_arr[ 'output' ];
    $return[ 'cell_info' ] = $room_status_arr['output'];
} else {
    $return[ 'cell_info' ] = array(
        'keyslug' => $keyslug,
        'user' => array(
            'ptoken' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken,
            'chat_name' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->chat_name,
            'avatar' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->avatar,
            'full_location' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->full_location,
            'coordinates' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->coordinates,
            'geohash' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->geohash,
            'local_timezone' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->local_timezone,
            'profile_type' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->type,
            'site' => array(
                'source' => TAOH_SITE_URL_ROOT,
                'name' => TAOH_SITE_NAME_SLUG,
            )
        ),

    );
    $coordinates =  $return[ 'cell_info' ]['user' ]['coordinates'];
    if($coordinates !=''){
        $co_array = explode('::',$coordinates);
        $lat = $co_array[0];
        $long = $co_array[1];
    }
    $return[ 'cell_info' ][ 'user' ][ 'longitude' ] = $long;
    $return[ 'cell_info' ][ 'user' ][ 'latitude' ] = $lat;
    taoh_networking_postcell( $return[ 'cell_info' ] );
}

$url = TAOH_SITE_URL_ROOT . taoh_either_or($room_status_arr['output']['club']['links']['club'], $contslug);
taoh_redirect($url);
taoh_exit();

?>
