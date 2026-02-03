<?php

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

if( !taoh_user_is_logged_in() ) {
    taoh_redirect(TAOH_LOGIN_URL);
    taoh_exit();
}

function live_now_room($input_array) {

    if(empty($input_array['output']['title']) || empty($input_array['output']['channels'])) {
        taoh_set_error_message('<b>Live Now is currently quiet.</b><br>Join a scheduled session or check back shortly — your next opportunity to connect is always just around the corner.');
        taoh_redirect (TAOH_SITE_URL_ROOT);
    }

    $title = ucwords($input_array['output']['title']);
    $description = ucwords($input_array['output']['description']);
    $channels = $input_array['output']['channels'];

    //$title = $title."abc";
    
    $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
    $full_loc_expl = explode(', ', $sess_user_info->full_location);
    $country = array_pop($full_loc_expl);

    $geo_enable = 0;   
    $dateHour = gmdate("YmdH");
    
    if($geo_enable)
        $keyslug = hash( 'crc32',$title.$country.$dateHour );
    else
        $keyslug = hash( 'crc32', $title.$dateHour);

    $room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'live_now']);
    $room_status_arr = json_decode( $room_status, true );

    if ( isset( $room_status_arr[ 'output' ] ) && $room_status_arr[ 'output' ]  && isset($room_var[ 'club_info' ][ 'club' ][ 'links' ][ 'live_now' ])  ){
        $return = $room_status_arr[ 'output' ];
    } else {
         $return = array(
            'keyslug' => $keyslug,
            'app' => 'live_now',
            'club' => array(
                'title' => $title,
                'description' => $description,
                'short' => 'Connect with all professionals  to discuss about '.$title,
                'image' => TAOH_SITE_LOGO,
                'square_image' => TAOH_SITE_FAVICON,
                'links' => array(
                    //'club' => '/club/room/'.taoh_slugify($title).'-'.$keyslug,
                    'live_now' => '/club/room/'.taoh_slugify($title).'-'.$keyslug,
                    'networking' => '/networking/club/local',
                    'detail' => '/networking/club/local',
                ),
                'channel_list' => $channels,
                'profile_types' => array(
                                    array(
                                        'slug' => 'employer',
                                        'title' => 'Employer',
                                    ),    
                                    array(
                                        'slug' => 'professional',
                                        'title' => 'Professional',
                                    ),
                                    array(
                                        'slug' => 'provider',
                                        'title' => 'Provider',
                                    ),
                                ),
                'skill' => '',
                'company' => '',
                'roles' => '',
                'sponsors' => '',
                'breadcrumbs' => array(
                    array(
                        'title' => 'Home',
                        'link' => '',
                    ),
                    array(
                        'title' => $title,
                        'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/room/'.taoh_slugify($title).'-'.$keyslug,
                    ),
                ),
                'live' => '',
                'geo_enable' => $geo_enable,
                'owner_enable' => false,
                'owner' => '',
                'full_location' => '',
                'coordinates' => '',
                'geohash' => '',
                'longitude' => '',
                'latitude' => '',
                'faq' => '',
            ),
        );
        create_room_info($return, $sess_user_info->ptoken);
    }

    $userslug = $keyslug;
    $value = taoh_networking_getcell( $keyslug );
    $value_arr = json_decode( $value, true );
    $temp_arr = $return;
    unset( $return );
    $return[ 'club_info' ] = $temp_arr;
    unset( $temp_arr );
    if ( isset( $value_arr[ 'output' ] ) && $value_arr[ 'output' ]) {
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
                'profile_type' => taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->type,
                'site' => array(
                    'source' => '/',
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

    return $return;    
    
}

// $url_data = file_get_contents(TAOH_LIVE_NOW_URL.'?y='.gmdate("Ymd"));
// $data_arr = json_decode($url_data, true);

$url_data = get_live_now_data();
$data_arr = json_decode($url_data, true);

$room_var = live_now_room($data_arr);
$forward_url = $room_var[ 'club_info' ][ 'club' ][ 'links' ][ 'live_now' ];

taoh_redirect (TAOH_SITE_URL_ROOT.$forward_url);

?>
