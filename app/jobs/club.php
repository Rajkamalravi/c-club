<?php
/*
locality [1 means global, uses local user timestamp for live/notlive]
visibility  [0 = public, 1 = private, 2 = protected]
*/
$share_link = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$contslug = taoh_parse_url(2);
//exit();
if ( ! taoh_user_is_logged_in() ){
    taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug );taoh_exit();
}
$contslug_expl = explode('-', $contslug);
$conttoken = taoh_sanitizeInput(array_pop($contslug_expl));

$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];

if ( isset( $_GET[ 'key' ] ) && $_GET[ 'key' ] ){
    $keyslug = $_GET[ 'key' ];
} else {
    if ( ! ctype_alnum( $conttoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG );taoh_exit(); }
    
    $ops = 'info';
    $mod = 'jobs';
    $taoh_call = 'jobs.job.get';
   // $cache_name = $mod.'_'.$ops.'_' . $conttoken . '_' . taoh_scope_key_encode( $conttoken, 'global' );
    $cache_name = $mod.'_'.$ops.'_' . $conttoken ;
    $taoh_vals = array(
        'token' => taoh_get_dummy_token(1),
        'ops' => $ops,
        'mod' => $mod,
        'cache_name' => $cache_name,
        'cache_time' => 7200,
       // 'cache' => array ( "name" => $cache_name,  "ttl" => 7200),
        'conttoken' => $conttoken,
        //'cfcc5h'=> 1, //cfcache newly added
    );
    //$taoh_vals[ 'cfcache' ] = $cache_name;
    ksort($taoh_vals);
    $response_json = taoh_apicall_get( $taoh_call, $taoh_vals );
    $job_arr = json_decode( $response_json, true );
    
    if ( ( ! isset( $job_arr[ 'success' ] ) || ( isset( $job_arr[ 'success' ] ) && ! $job_arr[ 'success' ] ) ) ){
        taoh_redirect( TAOH_EVENTS_URL.'/d/'.$contslug );taoh_exit();
    }
    $job_arr = $job_arr[ 'output' ];
    @$country = array_pop( explode( ', ',$job_arr[ 'meta' ][ 'full_location' ] ) );
    $key_key = $contslug.$country;
    //$key_key = $contslug;
    $keyslug = hash('crc32', $key_key );
}


$room_status = get_room_info($keyslug, $sess_user_info->ptoken, ['app' => 'job']);
$room_status_arr = json_decode( $room_status, true );
if ( isset( $room_status_arr[ 'output' ] ) && $room_status_arr[ 'output' ] ){
    $return = $room_status_arr[ 'output' ];
} else {
    $user_ptoken = $sess_user_info->ptoken;
    //@$country = array_pop( explode( ', ', $sess_user_info->full_location ) );
    //$owner_ptoken = $job_arr[ 'ptoken' ];

    $owner = taoh_networking_getcell( $keyslug, $job_arr[ 'ptoken' ] );
   // echo $owner;exit();
    $owner_arr = json_decode( $owner, true );
    //echo'<pre>----1111-------';print_r($owner_arr);echo'</pre>';exit();
    if ( isset( $owner_arr[ 'output' ] ) && $owner_arr[ 'output' ] ){    
        $return[ 'cell_info' ] = $owner_arr[ 'output' ];
    } else {
        $owner_json = taoh_get_user_info( $job_arr[ 'ptoken' ], 'public' );
        $owner_arrar = json_decode( $owner_json, true );

        $owner_arr = $owner_arrar['output']['user'];

        //echo $job_arr[ 'ptoken' ];//exit();
        //print_r($owner_arr);exit();
        $owner_arr_cell[ 'cell_info' ] = array(
            'keyslug' => $keyslug,
            'user' => array(
                'ptoken' => $owner_arr[ 'ptoken' ],
                'chat_name' => $owner_arr['chat_name'],
                'avatar' => $owner_arr['avatar'],
                'full_location' => $owner_arr['full_location'],
                'coordinates' => $owner_arr['coordinates'],
                'geohash' => $owner_arr['geohash'],
                'local_timezone' => $owner_arr['local_timezone'],
                'profile_type' => $owner_arr['profile_type'],
                'site' => $owner_arr['site'],
            ),
    
        );
        $coordinates =  $owner_arr_cell[ 'cell_info' ]['user' ]['coordinates'];
        if($coordinates !=''){
            $co_array = explode('::',$coordinates);
            $lat = $co_array[0];
            $long = $co_array[1];
        }
        $owner_arr_cell[ 'cell_info' ][ 'user' ][ 'longitude' ] = $long;
        $owner_arr_cell[ 'cell_info' ][ 'user' ][ 'latitude' ] = $lat;
        //echo'<pre>-----------';print_r($owner_arr_cell);echo'</pre>';exit();
        taoh_networking_postcell( $owner_arr_cell[ 'cell_info' ], $owner_arr[ 'ptoken' ]);//KALPANA COMMENTED
    }
    //$owner_arr = json_decode( $owner_json, true );
    
    $return = array(
        'keyslug' => $keyslug,
        'app' => 'job',
        'club' => array(
            'title' => $job_arr[ 'title' ],
            'description' => urldecode($job_arr[ 'description' ]),
            'short' => substr(strip_tags( urldecode($job_arr[ 'description' ]) ), 0, 300),
            'image' => taoh_either_or( $job_arr[ 'meta' ][ 'image' ], TAOH_CURR_APP_IMAGE ),
            'square_image' => taoh_either_or( $job_arr[ 'meta' ][ 'image_sq' ], TAOH_CURR_APP_IMAGE_SQUARE ),
            'links' => array(
                'club' => '/club/room/'.taoh_slugify($job_arr[ 'title' ]).'-'.$keyslug,
                'networking' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/club/'.$contslug,
                'detail' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug,
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
            'skill' => taoh_either_or( $job_arr[ 'meta' ][ 'skill' ], '' ),
            'company' => taoh_either_or( $job_arr[ 'meta' ][ 'company' ], '' ),
            'roles' => taoh_either_or( $job_arr[ 'meta' ][ 'title' ], '' ),
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
                    'link' => '/',
                ),
                array(
                    'title' => ucfirst(TAOH_SITE_CURRENT_APP_SLUG),
                    'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG,
                ),
                array(
                    'title' => $job_arr[ 'title' ],
                    'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/d/'.$contslug,
                ),
                // array(
                //     'title' => 'Networking',
                //     'link' => '/'.TAOH_SITE_CURRENT_APP_SLUG.'/'.taoh_slugify($job_arr[ 'title' ]).'-'.$keyslug,
                // ),
            ),
            'live' => '',
            'geo_enable' => false,
            'owner_enable' => true,
            'owner' => taoh_either_or( $owner_arr, '' ),
            'full_location' => taoh_either_or( $job_arr[ 'meta' ][ 'full_location' ], '' ),
            'coordinates' => taoh_either_or( $job_arr[ 'meta' ][ 'coordinates' ], '' ),
            'geohash' => taoh_either_or( $job_arr[ 'meta' ][ 'geohash' ], '' ),
            'longitude' => '',
            'latitude' => '',
            'faq' => array(
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
            ),
        ),
    );
    create_room_info($return, $sess_user_info->ptoken);
    $room_status_arr = array( 'output' => $return, 'success' => true );
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

if (isset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken) && isset($return['club_info']['club']['owner']['ptoken']) && taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken == $return['club_info']['club']['owner']['ptoken'])
    $url = TAOH_SITE_URL_ROOT . $room_status_arr['output']['club']['links']['club'];
else {
    if (isset($return['club_info']['club']['owner']['ptoken']) && isset($return['club_info']['club']['owner']['chat_name'])) {
        $url = TAOH_SITE_URL_ROOT . $room_status_arr['output']['club']['links']['club'] . '/chatwith/' . $return['club_info']['club']['owner']['ptoken'] . '?from=owner&with=' . $return['club_info']['club']['owner']['chat_name'];
    } else {
        $url = TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/d/' . $contslug;
    }
}

taoh_redirect($url);
taoh_exit();

?>