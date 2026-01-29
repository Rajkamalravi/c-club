<?php

if( !taoh_user_is_logged_in() ) {

    taoh_redirect(TAOH_LOGIN_URL);
    taoh_exit();
}

$app_temp = taoh_parse_url(2);
$contslug = taoh_parse_url(3);


function asks_skill_room($app_temp,$contslug){
    // *** CLUB Room Info - Start ***
    $asks_data = explode( '-', urldecode($contslug ) );
    $title = ucwords($asks_data[0]);
    $id = $asks_data[1];
    //echo "==============".$title;die();

    $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
    $full_loc_expl = explode(', ', $sess_user_info->full_location);
    $country = array_pop($full_loc_expl);

    $geo_enable = 0;


    if($geo_enable)
        $keyslug = hash( 'crc32',$title.$id.$app_temp.$country );
    else
        $keyslug = hash( 'crc32', $title.$id.$app_temp );

    $room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'ask']);
    $room_status_arr = json_decode( $room_status, true );
    if ( isset( $room_status_arr[ 'output' ] ) && $room_status_arr[ 'output' ] ){
        $return = $room_status_arr[ 'output' ];
    } else {
        $return = array(
            'keyslug' => $keyslug,
            'app' => 'ask',
            'club' => array(
                'title' => $title,
                'description' => 'Welcome to the Asks - '.$app_temp.' -'.$title,
                'short' => 'Connect with all professionals  to discuss about '.$title,
                'image' => TAOH_SITE_LOGO,
                'square_image' => TAOH_SITE_FAVICON,
                'links' => array(
                    'club' => '/club/room/'.taoh_slugify($title).'-'.$keyslug,
                    'networking' => '/networking/club/local',
                    'detail' => '/networking/club/local',
                ),
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
                'sponsors' => array(
                    array(
                        'title' => 'NoWorkerLeftBehind',
                        'sub_title' => 'Let us all work together and help each other succeed.',
                        'image' => 'https://noworkerleftbehind.org/wp-content/uploads/2022/09/cropped-cropped-nwlb_sq-270x270.png',
                        'link' => 'https://noworkerleftbehind.org/',
                    ),
                    array(
                        'title' => 'TAO.ai',
                        'sub_title' => 'TAO: Through technology, make professional connectios and career growth universally accessible.',
                        'image' => 'https://tao.ai/tao/innovative/img/TAO_AI_Logo_icon_orng.png',
                        'link' => 'https://tao.ai',
                    ),
                ),
                'breadcrumbs' => array(
                    array(
                        'title' => 'Home',
                        'link' => '',
                    ),
                    array(
                        'title' => ucfirst(TAOH_SITE_CURRENT_APP_SLUG),
                        'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG,
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
                'faq' => array(
                    array(
                        'title' => 'What is asks ?',
                        'description' => 'Discuss with all professionals on various topic',
                    ),
                ),
            ),
        );
        create_room_info($return, $sess_user_info->ptoken);
    }
    // *** CLUB Room Info - End ***

    // *** CLUB Cell Info - Start ***
    $userslug = $keyslug;
    $value = taoh_networking_getcell( $keyslug );
    $value_arr = json_decode( $value, true );
    $temp_arr = $return;
    unset( $return );
    $return[ 'club_info' ] = $temp_arr;
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
    // *** CLUB Cell Info - End ***

    return $return;
}


$room_var = asks_skill_room($app_temp,$contslug);
//echo'<pre>';print_r($room_var);echo'</pre>';die();
$forward_url = $room_var[ 'club_info' ][ 'club' ][ 'links' ][ 'club' ];
//echo $forward_url;exit();
taoh_redirect (TAOH_SITE_URL_ROOT.$forward_url);

?>
