<?php
/*
locality [1 means global, uses local user timestamp for live/notlive]
visibility  [0 = public, 1 = private, 2 = protected]
*/

$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$contslug = taoh_parse_url(2);

if ( ! taoh_user_is_logged_in() ){
    taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug ); taoh_exit();
}
$event_path = '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug;
@$eventtoken = array_pop( explode( '-', $contslug ) );

$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
$attendee_count = 0;
if ( isset( $_GET[ 'key' ] ) && $_GET[ 'key' ] ){
    $keyslug = $_GET[ 'key' ];
} else {
    $cache_name = 'event_detail_' . $eventtoken;
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'ops' => 'baseinfo',
        'mod' => 'events',
        'eventtoken' => $eventtoken ?? '',
        'cache_name' => $cache_name,
        'cache_time' => 2 * 60 * 60,
        'cache' => array ( "name" => $cache_name,  "ttl" => 2 * 60 * 60),
        //'cfcc2h' => 1 //cfcache newly added

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

    //echo "======".$abbreviation;die();

    date_default_timezone_set($sess_user_info->local_timezone);

    /* get attendees count based on country */
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'mod' => 'events',
        'eventtoken' => $eventtoken ?? '',
        'country_name' => $sess_user_info->country_name,
        'cache_required' => 0
    );
    $taoh_call = 'events.rsvp.users.count';
    $response_count = taoh_apicall_get( $taoh_call, $taoh_vals );
    $att_response_arr = taoh_get_array( $response_count, true );
    if($att_response_arr['success'] && $att_response_arr['output'] != ''){
        $attendee_count = $att_response_arr['output'];
    }
    /* #get attendees count based on country */
    $geo_enable = 0;
    if( isset($events_arr[ 'conttoken' ][ 'country_locked' ])  && $events_arr[ 'conttoken' ][ 'country_locked' ] !='' ){
        if ( $events_arr[ 'conttoken' ][ 'country_locked' ] ){
            if($attendee_count <= 500){ // 250
                $key_key = $eventtoken."_".$country;
            }else{
                $key_key = $eventtoken."_".$country.'_'.$abbreviation;
            }
            $geo_enable = 1;
        } else {
            $key_key = $eventtoken;
            $geo_enable = 0;
        }
    } else {
        /*$key_key = $contslug."_".$country.'_'.$abbreviation;
        $geo_enable = 1;*/
        $key_key = $eventtoken;
        $geo_enable = 0;
    }

    //echo "============".$geo_enable;die();
    $title = $title_desc = '';

    if(isset($_GET['title']) && $_GET['title'] !='' ){
        $key_key = $key_key."-".$_GET['title'].'-'.$_GET['speaker'];
        $title = ' / '.$_GET['title'] . ' by ' . $_GET['speaker'];
        $title_desc = '<span class="super_title">'.$_GET['title'] . ' by ' . $_GET['speaker'].'</span><br>';
    }
    $keyslug = hash('crc32', $key_key);
    //print_r($events_arr);exit();
    $room_info_arr = array(
        'keyslug' => $keyslug,
        'app' => 'event',
        'sub_app' => '',
        'eventtoken' => $eventtoken,
        'country' => $country,
        'timezone' => $abbreviation,
        'club' => array(
            'title' => $events_arr[ 'conttoken' ][ 'title' ].$title,
            'description' => $title_desc.urldecode( $events_arr[ 'conttoken' ][ 'description' ] ),
            'short' => substr(strip_tags( urldecode( $events_arr[ 'conttoken' ][ 'description' ] ) ), 0, 300),
            'streaming_link' => taoh_either_or( $events_arr[ 'conttoken' ][ 'live_link' ], '' ),
            'image' => taoh_either_or( $events_arr[ 'conttoken' ][ 'event_image' ], TAOH_CURR_APP_IMAGE ),
            'square_image' => taoh_either_or( $events_arr[ 'conttoken' ][ 'chat_image' ], TAOH_CURR_APP_IMAGE_SQUARE ),
            'links' => array(
                'club' => '/club/room/'.taoh_slugify($events_arr[ 'conttoken' ][ 'title' ]).'-'.$keyslug,
                'networking' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/club/'.$contslug,
                'detail' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug,
            ),
            'profile_types' => $events_arr[ 'conttoken' ][ 'ticket_types' ],
            'skill' => $events_arr['conttoken']['skill'] ?? [],
            'company' => $events_arr['conttoken']['company'] ?? [],
            'roles' => $events_arr['conttoken']['roles'] ?? [],
            /*'sponsors' => array(
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
            ),*/
            'breadcrumbs' => array(
                array(
                    'title' => 'Home',
                    'link' => '/',
                ),
                array(
                    'title' => ucfirst(TAOH_SITE_CURRENT_APP_SLUG),
                    'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG,
                ),
                array(
                    'title' => $events_arr[ 'conttoken' ][ 'title' ].$title,
                   // 'title' => $events_arr[ 'conttoken' ][ 'title' ],
                    'link' => $event_path,
                 ),
                // array(
                //     'title' => 'Networking',
                //     'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/'.taoh_slugify($events_arr[ 'conttoken' ][ 'title' ]).'-'.$keyslug,
                // ),
            ),
            'live' => '',
            'geo_enable' => $geo_enable,
            'owner_enable' => false,
            'owner' => $owner_arr ?? '',
            'full_location' => $events_arr['meta']['full_location'] ?? '',
            'coordinates' => $events_arr['meta']['coordinates'] ?? '',
            'geohash' => $events_arr['meta']['geohash'] ?? '',
            'longitude' => '',
            'latitude' => '',
            /*'faq' => array(
                array(
                    'title' => 'What is this networking page for?',
                    'description' => 'This networking page is for job seekers who are interested in connecting with recruiters and learning more about open jobs.',
                ),
                array(
                    'title' => 'Who can use this networking page?',
                    'description' => 'Anyone who is looking for a job can use this networking page.',
                ),

                array(
                    'title' => 'How do I use this networking page?',
                    'description' => 'To use this networking page, simply create an account and browse the job postings. When you find a job that you are interested in, you can click the "Chat with Recruiter" button to start a conversation.',
                ),
                array(
                    'title' => 'What are the rules of this networking page?',
                    'description' => 'The rules of this networking page are simple: be respectful of others and share helpful information.',
                ),
                array(
                    'title' => 'What are the benefits of using this networking page?',
                    'description' => 'The benefits of using this networking page include:<br /><br />

                    The ability to connect with recruiters from all over the world<br />
                    The ability to learn more about open jobs and the companies that are hiring<br />
                    The ability to get your resume noticed by recruiters',
                ),
                array(
                    'title' => 'How do I start a chat with a recruiter?',
                    'description' => 'To start a chat with a recruiter, simply click the "Chat with Recruiter" button on the job posting page.',
                ),
                array(
                    'title' => 'What should I say in my first message to a recruiter?',
                    'description' => 'In your first message to a recruiter, introduce yourself and explain why you are interested in the job. You may also want to include a link to your resume.',
                ),

                array(
                    'title' => 'What are some tips for chatting with recruiters?',
                    'description' => 'Here are some tips for chatting with recruiters:<br /><br />

                        Be professional and polite<br />
                        Be clear and concise in your communication<br />
                        Ask questions to learn more about the job and the company<br />
                        Be prepared to answer questions about your skills and experience',
                ),

                array(
                    'title' => 'What should I do if a recruiter does not respond to my message?',
                    'description' => 'If a recruiter does not respond to your message, do not take it personally. Recruiters are often very busy and may not have time to respond to every message. You can try sending a follow-up message after a few days, but do not be too pushy.',
                ),
                array(
                    'title' => 'What should I do if a recruiter asks me to share personal information?',
                    'description' => 'If a recruiter asks you to share personal information, such as your Social Security number or bank account number, be very cautious. Legitimate recruiters will never ask you for this type of information.',
                ),
                array(
                    'title' => 'What if I have a question about a job posting?',
                    'description' => 'If you have a question about a job posting, you can contact the recruiter directly or you can post your question on the networking page.',
                ),
                array(
                    'title' => 'What if I want to learn more about the company?',
                    'description' => 'If you want to learn more about the company, you can visit the company\'s website or read reviews from current and former employees.',
                ),
                array(
                    'title' => 'What if I need help with my job search?',
                    'description' => 'If you need help with your job search, you can contact a career counselor or search for resources online.',
                ),
            ),*/
        ),
    );
    //echo '<pre>';print_r($room_info_arr);exit();
    create_room_info($room_info_arr, $sess_user_info->ptoken);
    $room_status_arr = array( 'output' => $room_info_arr, 'success' => true );
}

//echo "$keyslug, $country";
$taoh_call = "events.rsvp.get";
$taoh_vals = array(
    'token'=>TAOH_API_TOKEN,
    'ops' => 'rsvp',
    'mod' => 'events',
    'eventtoken' => $eventtoken,
    'cache_required' => 0,
    //'cache' => array ( "name" => taoh_p2us('events.rsvp').'_'.$eventtoken.'_rsvp', "ttl" => 3600),
);
//echo TAOH_API_PREFIX.'/'.$taoh_call."?".http_build_query($taoh_vals);taoh_exit();
$response_json = taoh_apicall_get( $taoh_call, $taoh_vals );
$rsvp_arr = taoh_get_array( $response_json, true );
if ( ( ! isset( $rsvp_arr[ 'success' ] ) || ( isset( $rsvp_arr[ 'success' ] ) && ! $rsvp_arr[ 'success' ] ) ) ){
    taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug ); taoh_exit();
}
// *** CLUB Room Info - End ***


// *** CLUB Cell Info - Start ***
foreach ( $events_arr[ 'conttoken' ][ 'ticket_types' ] as $key => $value ){
    if ( $value[ 'slug' ] == $rsvp_arr[ 'output' ][ 'rsvp_slug' ] ){
        $profile[ 'slug' ] = $value[ 'slug' ];
        $profile[ 'title' ] = $value[ 'title' ];
    }
}

$userslug = $keyslug;
$value = taoh_networking_getcell( $keyslug );
$value_arr = taoh_get_array( $value, true );
$temp_arr = $return ?? [];
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
if(isset($_GET['chatwith']) && $_GET['chatwith'] !='' ){
    $url = $url.'?chatwith='.$_GET['chatwith'];

}
//echo $url;die();
taoh_redirect($url);
taoh_exit();


/*
    $taoh_call = "jobs.job.get";
    $taoh_vals = array(
        'token'=> taoh_get_dummy_token(),
        'mod' => 'jobs',
        'ops' => 'info',
        'conttoken' => $conttoken,
        'cache' => array ( "name" => taoh_p2us('jobs.job').'_'.$conttoken.'_info', "ttl" => 3600),
    );
    $events_arr = taoh_get_array(taoh_apicall_get( $taoh_call, $taoh_vals ), true);
    if ( ( ! isset( $events_arr[ 'success' ] ) || ( isset( $events_arr[ 'success' ] ) && ! $events_arr[ 'success' ] ) ) ){
        taoh_redirect( TAOH_EVENTS_URL.'/d/'.$contslug );taoh_exit();
    }
    $events_arr = $events_arr[ 'output' ];
    @$country = array_pop( explode( ', ',$events_arr[ 'meta' ][ 'full_location' ] ) );
    $keyslug = hash('crc32', $events_arr[ 'conttoken' ][ 'title' ].$country );

*/

$room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'event']);
$room_status_arr = taoh_get_array( $room_status, true );
if ( isset( $room_status_arr[ 'output' ] ) && $room_status_arr[ 'output' ] ){
    $return = $room_status_arr[ 'output' ];
} else {
    $user_ptoken = $sess_user_info->ptoken;
    //@$country = array_pop( explode( ', ', $sess_user_info->full_location ) );
    //$owner_ptoken = $events_arr[ 'ptoken' ];
    $owner_json = taoh_get_user_info( $events_arr[ 'ptoken' ], 'cell' );
    $owner_arrar = taoh_get_array( $owner_json, true );
    $owner_arr = $owner_arrar['output']['user'];
    $return['app'] = 'event';
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
