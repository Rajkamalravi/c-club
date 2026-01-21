<?php

error_reporting(E_ALL);
if(taoh_parse_url(3)){
    $eventtoken = taoh_parse_url(3);
}
if(taoh_parse_url(4)){
    $session_id = (int)taoh_parse_url(4);
}



if ( ! taoh_user_is_logged_in() ){
    taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$eventtoken.'?redirect=1' ); taoh_exit();
}
    $taoh_call = "events.content.detail";
    $cache_name = 'event_MetaInfo_'. $eventtoken.'_speaker_'.$session_id; 
    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'eventtoken' => $eventtoken,
        'meta_id' => $session_id,
        'cache_name' => $cache_name,
    );
    // $response= json_decode(taoh_apicall_get_debug($taoh_call, $taoh_vals), true);
    $response= json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
    


    date_default_timezone_set($sess_user_info->local_timezone);

    
    if ( ( ! isset( $response[ 'success' ] ) || ( isset( $response[ 'success' ] ) && ! $response[ 'success' ] ) ) ){
        taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$eventtoken ); taoh_exit();
    }
    else{
     $speaker_data = $response['output'];


     //   echo "<pre>"; print_r($speaker_data); echo "</pre>";

    $session_title = $speaker_data['spk_title'];
    
    

    $sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

    $cache_name = 'event_detail_' . $eventtoken;
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'ops' => 'baseinfo',
        'mod' => 'events',
        'eventtoken' => $eventtoken ?? '',
        'cache_name' => $cache_name,
        'cache_time' => 2 * 60 * 60,
        'cache' => array ( "name" => $cache_name,  "ttl" => 2 * 60 * 60),
        
    );
    $taoh_call = 'events.event.get';
    //$taoh_vals[ 'cfcache' ] = $cache_name;
    ksort($taoh_vals);
    $response_json = taoh_apicall_get( $taoh_call, $taoh_vals );
    $response = taoh_get_array( $response_json, true );
    if ( ( ! isset( $response[ 'success' ] ) || ( isset( $response[ 'success' ] ) && ! $response[ 'success' ] ) ) ){
        taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug ); taoh_exit();
    }
    $events_arr = $response[ 'output' ];

    //print_r ($events_arr);exit();

    @$country = array_pop( explode( ', ',$sess_user_info->full_location ) );
    $timezone = $sess_user_info->local_timezone;
    $date = new DateTime('now', new DateTimeZone($timezone));
    $abbreviation = $date->format('T');

    $geo_enable = 0;
    /* if( isset($events_arr[ 'conttoken' ][ 'country_locked' ])  && $events_arr[ 'conttoken' ][ 'country_locked' ] !='' ){
        if ( $events_arr[ 'conttoken' ][ 'country_locked' ] ){
            $key_key = $session_title.$eventtoken."_".$country.'_'.$abbreviation;
            $geo_enable = 1;
        } else {
            $key_key = $session_title.$eventtoken;
            $geo_enable = 0;
        }
    } else {
       
        $key_key = $session_title.$eventtoken;
        $geo_enable = 0;
    } */

    $key_key = $session_title.$eventtoken;
    $geo_enable = 0;

    //echo "============".$geo_enable;die();
    //echo '<pre>';print_r($speaker_data);exit();
    $title = $title_desc = '';
       
    $title = $session_title . ' by ' . $speaker_data['spk_name']['0'];
    $title_desc = '<span class="super_title">'.$session_title . ' by ' . $speaker_data['spk_name']['0'].'</span><br>'.$speaker_data['spk_desc'];

    $event_path = '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$events_arr[ 'conttoken' ][ 'title' ].'-'.$eventtoken;


     $keyslug = hash('crc32', $key_key);

     $room_info_arr = array(
        'keyslug' => $keyslug,
        'app' => 'event',
        'sub_app' => 'session',
        'eventtoken' => $eventtoken,
        'country' => $country,
        'timezone' => $abbreviation,
        'club' => array(
            'title' => $title,
            'description' => urldecode( $title_desc),
            'short' => urldecode( $speaker_data['spk_sdesc']),
            'streaming_link' => taoh_either_or( $speaker_data['spk_streaming_link'], '' ),
            'image' => taoh_either_or( $speaker_data['spk_profileimg'], TAOH_CURR_APP_IMAGE ),
            'square_image' => taoh_either_or( $speaker_data['spk_profileimg'], TAOH_CURR_APP_IMAGE_SQUARE ),
            'links' => array(
                'club' => '/club/room/'.taoh_slugify($session_title).'-'.$keyslug,
                'networking' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/club/'.taoh_slugify($events_arr[ 'conttoken' ][ 'title' ]).'-'.$eventtoken,
                'detail' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.taoh_slugify($events_arr[ 'conttoken' ][ 'title' ]).'-'.$eventtoken,
            ),
            'speaker_data' => json_encode($speaker_data),
            'profile_types' => $events_arr[ 'conttoken' ][ 'ticket_types' ],
            'skill' => taoh_either_or( $events_arr[ 'conttoken' ][ 'skill' ], '' ),
            'company' => taoh_either_or( $events_arr[ 'conttoken' ][ 'company' ], '' ),
            'roles' => taoh_either_or( $events_arr[ 'conttoken' ][ 'roles' ], '' ),
           
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '/',
                ),
                array(
                    'title' => ucfirst(TAOH_SITE_CURRENT_APP_SLUG),
                    'link' => $event_path,
                ),
                array(
                    'title' => $title,
                    'link' => $event_path,
                 ),
                
            ),
            'live' => '',
            'geo_enable' => $geo_enable,
            'owner_enable' => false,
            'owner' => taoh_either_or( $owner_arr, '' ),
            'full_location' => taoh_either_or( $events_arr[ 'meta' ][ 'full_location' ], '' ),
            'coordinates' => taoh_either_or( $events_arr[ 'meta' ][ 'coordinates' ], '' ),
            'geohash' => taoh_either_or( $events_arr[ 'meta' ][ 'geohash' ], '' ),
            'longitude' => '',
            'latitude' => '',
            
        ),
    );
   // echo '<pre>';print_r($room_info_arr);exit();
    create_room_info($room_info_arr, $sess_user_info->ptoken);
    $room_status_arr = array( 'output' => $room_info_arr, 'success' => true );

}


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

$url = TAOH_SITE_URL_ROOT . taoh_either_or($room_status_arr['output']['club']['links']['club'], $contslug);
//echo $url;die();
taoh_redirect($url);
taoh_exit();


?>