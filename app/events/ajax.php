<?php
//  ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
//Getting a list of rooms
function events_get_rooms()
{
    $title = "events";
    if ($_POST['type'] == "rolechat") {
        $title = "title";
    } else if ($_POST['type'] == "orgchat") {
        $title = "company";
    }
    /*$query = ($_POST['term'] != "" ? "&term=" .$_POST['term'] : "");
    $api = TAOH_CDN_FIND."?mod=".$_POST['mod']."&type=".$title."&ctype=token&code=".TAOH_API_TOKEN."&misc=".$_POST['misc']."&maxr=".$_POST['maxr'].$query;
    $data = taoh_url_get_content($api);*/

    $query = ($_POST['term'] != "" ? $_POST['term'] : "");
    $taoh_vals = array(
        'mod' => $_POST['mod'],
        'type' => $title,
        'ctype' => 'token',
//    'code'=>TAOH_API_TOKEN,
        'code' => taoh_get_dummy_token(1),
        'misc' => $_POST['misc'],
        'maxr' => $_POST['maxr'],
        'term' => $query,
    );
    $taoh_call = "/api/find.php";
    $taoh_vals['cfcache'] = hash('sha256', $taoh_call . serialize($taoh_vals));
    $taoh_call_type = "get";
    ksort($taoh_vals);
    $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_CDN_PREFIX);

    echo $data;
    die();
}

if (!function_exists('events_get')) {
    function events_get()
    {
        $offset_default = 0;
        $limit_default = 12;
        $limit = $_GET['limit'] ?? $limit_default;
        $offset = ($_GET['offset'] ?? $offset_default) * $limit;
        $filter_type = !empty($_GET['filter_type']) ? $_GET['filter_type'] : '';
        $call_from = !empty($_GET['call_from']) ? $_GET['call_from'] : '';

        $taoh_call = "events.get";
        $taoh_vals = [
            'mod' => 'events',
            'geohash' => $_GET['geohash'] ?? '',
            'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
            'token' => taoh_get_dummy_token(1),
            'local' => TAOH_EVENTS_GET_LOCAL, // TAOH_EVENTS_GET_LOCAL
            'ops' => $_GET['ops'] ?? 'list',
            'search' => $_GET['search'] ?? '',
            'limit' => $limit,
            'offset' => $offset,
            'postDate' => $_GET['postDate'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'filter_type' => $filter_type,
            'call_from' => $call_from,
            'filters' => $_GET['filters'] ?? [],
            'cache_time' => 120,
            'demo' => EVENT_DEMO_SITE,
            'cfcc5h'=> 1,
            'debug' => 0,
        ];

        // $taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));

        if (in_array($filter_type, ['saved', 'rsvp_list'])) {
            $taoh_vals['cache_required'] = 0;
            unset($taoh_vals['cache_time']);
            $taoh_vals['token'] = taoh_get_dummy_token();
        }


        //echo taoh_apicall_get_debug($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);die();

        $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
        echo $data;
        exit();
    }
}

function events_get_tao()
{
        $offset_default = 0;
         $limit_default = 12;
        $limit = $_GET['limit'] ?? $limit_default;
        $offset = ($_GET['offset'] ?? $offset_default) * $limit;
        $filter_type = $_GET['filter_type']!='' ? $_GET['filter_type'] : '';
        $call_from = $_GET['call_from'] !='' ?  $_GET['call_from']  : '';
       // echo'<pre>';print_r($_GET);
       // echo "============".$_GET['offset'];
       $event_type = $_GET['event_type'] ?? '';
        $taoh_call = "events.get";
        $taoh_vals = [
            'mod' => 'events',
            'geohash' => $_GET['geohash'] ?? '',
            'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
            'token' => $_GET['token'] ?? taoh_get_api_token(1),
            'local' => TAOH_EVENTS_GET_LOCAL, // TAOH_EVENTS_GET_LOCAL
            'ops' => $_GET['ops'] ?? 'list',
            'search' => $_GET['search'] ?? '',
            'limit' => $limit,
            'offset' => $offset,
            'postDate' => $_GET['postDate'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'filter_type' => $filter_type,
            'call_from' => $call_from,
            'filters' => $_GET['filters'] ?? [],
            'cache_time' => 120,
            'demo' => 1,
            'event_type' => $event_type,
            //'cfcc5h'=> 1  ////cfcache newly added
            // 'list_all' => 1
            //'debug'=>1,
        ];
        //   $taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
       if (isset($_GET['event_type']) && !empty(trim($_GET['event_type']))) {
        $taoh_vals['list_all'] = '1';
       }
      // echo taoh_apicall_get_debug($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);die();
                      //echo '<pre>';
        //print_r($taoh_vals);die();
        $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);
        // $response = json_decode($datafetch, true);
        // $data =  $response['output']['list']['near'];
        //print_r($data);die();
        echo $data ;
        exit();
    }

    function events_get_all_tao()
    {
        $offset_default = 0;
        $limit_default = 12;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : $limit_default;
        $offset = (isset($_GET['offset']) ? $_GET['offset'] : $offset_default) * $limit;
        $filter_type = $_GET['filter_type']!='' ? $_GET['filter_type'] : '';
        $call_from = $_GET['call_from'] !='' ?  $_GET['call_from']  : '';

       // echo'<pre>';print_r($_GET);
       // echo "============".$_GET['offset'];

        $taoh_call = "events.get";
        $taoh_vals = [
            'mod' => 'events',
            'geohash' => $_GET['geohash'] ?? '',
            'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
            'token' => $_GET['token'] ?? taoh_get_api_token(1),
            'local' => TAOH_EVENTS_GET_LOCAL, // TAOH_EVENTS_GET_LOCAL
            'ops' => $_GET['ops'] ?? 'list',
            'search' => $_GET['search'] ?? '',
            'limit' => $limit,
            'offset' => $offset,
            'postDate' => $_GET['postDate'] ?? '',
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'filter_type' => $filter_type,
            'call_from' => $call_from,
            'filters' => $_GET['filters'] ?? [],
            'cache_time' => 120,
            'demo' => 1,
            'list_all' => 1,
            'cfcc5h'=> 1
            //'debug'=>1,
            //'event_type' => 'near'
        ];
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);die();
       // $taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));

        if (in_array($filter_type, ['saved', 'rsvp_list'])) {
            $taoh_vals['cache_required'] = 0;
            unset($taoh_vals['cache_time']);
        }
        //echo '<pre>';
        //print_r($taoh_vals);die();


        $data = taoh_apicall_get($taoh_call, $taoh_vals, TAOH_API_PREFIX, 1);

        // $response = json_decode($datafetch, true);
        // $data =  $response['output']['list']['near'];
        //print_r($data);die();
        echo $data ;
        exit();
    }

function get_my_rsvp()
{
    //https://api2.tao.ai/events.get?string=so&geohash=dr5x1n7c&mod=events&token=y2Ds3ugv&ops=rsvplist or rsvpactive
    $limit = @$_POST['limit'];
    $offset = @$_POST['offset'];
    $mod = @$_POST['mod'];

    $taoh_call = "events.get";
    $taoh_vals = array(
        'mod' => $mod,
        'key' => TAOH_API_SECRET,
        'token' => taoh_get_api_token(),
        'ops' => 'rsvpactive',
        'limit' => $limit,
        'offset' => $offset,
        //'cfcc15m'=> 1 //cfcache newly added
        
    );
    if ($offset && $limit) {
        $taoh_vals['cache'] = array("name" => taoh_p2us($taoh_call) . TAOH_API_SECRET . '_rsvpactive', 'ttl' => 3600);
        // $taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
    }
    //echo TAOH_API_PREFIX."/$taoh_call?".http_build_query($taoh_vals);die();
    $taoh_call_type = "get";
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals);die;
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    //$api = TAOH_API_PREFIX."/events.get?geohash=&ops=rsvpactive&mod=".$mod."&token=".taoh_get_api_token()."&limit=$limit&offset=$offset";
    //$data = file_get_contents($api);
    echo $data;
    die();
}


function taoh_invite_rsvp_type()
{
    $event_title = base64_decode($_POST['event_title']);

    $actions_var = [
        'action_url' => '',
        'action_page_blurb' => '',
        'action_email_vars' => [
            'subject' => 'RSVP for the event ' . $event_title,
            'supertitle' => 'You have been invited to RSVP for the event ' . $event_title,
            'title' => 'RSVP for the event ' . $event_title,
            'subtitle' => 'After creating and completing your profile, visit RSVP page for the event ' . $event_title . ' to complete RSVP',
        ],
        'extra_info' => [
            'title' => $event_title,
            'app_name' => 'Event',
            'action' => 'RSVP',
            'link' => $_POST['detail_link'],
            'action_link' => $_POST['from_link'],
            'site_name' => TAOH_SITE_NAME_SLUG,
        ]
    ];

    $toenter = array();
    $taoh_call = "core.refer.put";
    $toenter['refer'] = json_encode([]);
    $toenter['refer_data'] = json_encode([
        'requested_by_ptoken' => taoh_get_dummy_token(),
        'from_link' => $_POST['from_link'],
        'to_link' => $_POST['from_link'],
        'for_email' => '',
        'action_flag' => 1,
        'actions_var' => $actions_var,
        'referral_type' => 'RSVP',
    ]);

    $taoh_vals = [
        'ops' => 'invite',
        'token' => taoh_get_dummy_token(),
        'toenter' => $toenter,
    ];

    $res = taoh_apicall_post($taoh_call, $taoh_vals);
    $result = json_decode($res);

    $res = ['success' => 1, 'refer_token' => $result->refer_token[0]];

    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'refer_token', $result->refer_token[0], strtotime('+1 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url', $_POST['from_link'], strtotime('+1 days'), '/');

    if ($result->success == 1) {
        echo json_encode($res);
        die();
    } else {
        echo 0;
    }
}


function event_like_put()
{
    $taoh_call = "content.save";
    $remove = array("events_*", "event_detail_" . $_POST['eventtoken']);
    $taoh_vals = array(
        'slug' => 'events',
        'token' => taoh_get_dummy_token(),
        'conttoken' => $_POST['contttoken'],
        'eventtoken' => $_POST['eventtoken'],
        'redis_store' => 'taoh_intaodb_events',
        'cache' => array('remove' => $remove),
    );
    //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_post($taoh_call, $taoh_vals);
    taoh_delete_local_cache('events', $remove);
    echo $data;
    die();
}


function event_rsvp_put()
{
    $values = json_encode(array($_POST['conttoken'], 'events', $_POST['ptoken'], $_POST['met_click'], time(), TAOH_API_SECRET));
    //print_r($values);die;
    $data = taoh_cacheops( 'metricspush', $values );
    //print_r($data);die;
    echo $data;
    die();
}

function rsvp_status_check()
{
    $ticket_types = json_decode($_POST['ticket_types'], true);
    $return_array = [];
    $taoh_call = "events.rsvp.get";
    $taoh_vals = array(
        'ops' => 'status',
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'eventtoken' => $_POST['event_token'],
        'cache_required' => 0,
        'time' => time(),
    );
    $resp = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
    $rsvp_done_already = $resp['success'];
    if ($rsvp_done_already) {
        $rsvp_token = $resp['output']['rsvptoken'];
        $taoh_call = "events.rsvp.get";
        $taoh_vals = array(
            'ops' => 'info',
            'mod' => 'events',
            'token' => taoh_get_dummy_token(),
            'rsvptoken' => $rsvp_token,
            'cache_required' => 0,
            'time' => time(),
        );
        $respo = json_decode(taoh_apicall_get($taoh_call, $taoh_vals));
        $rsvp_data = $respo->output;
        $tokentyp = $rsvp_data->rsvp_slug;
        foreach ($ticket_types as $k => $v) {
            if (strtolower(trim($v['slug'])) === strtolower(trim($tokentyp))) {
                $tokenkey = $v['title'];
            }
        }
        $return_array['success'] = true;
        $return_array['rsvp_status'] = $resp['output'];
        $return_array['rsvp_info'] = $rsvp_data;
        $return_array['ticket_type'] = $tokenkey;

        $data = json_encode($return_array);
    } else {
        $return_array['success'] = false;
        $data = json_encode($return_array);
    }
    echo $data;
    die;
}


function events_get_detail()
{
    $eventtoken = $_POST['eventtoken'];
    $ptoken = $_POST['ptoken'];
    $from = 'listing';
    header('Content-Type: application/html; charset=utf-8');
    include_once('event_detail_content.php');
}

function get_event_rsvp($eventtoken)
{
    $taoh_call = "events.rsvp.get";
    $taoh_vals = array(
        'token' => TAOH_API_TOKEN,
        'ops' => 'rsvp',
        'mod' => 'events',
        'eventtoken' => $eventtoken,
        'cache_required' => 0,
        'time' => time(),
    );

    echo taoh_apicall_get($taoh_call, $taoh_vals);
}

if (!function_exists('get_event_baseinfo')) {
    function get_event_baseinfo()
    {
        $eventtoken = $_POST["eventtoken"];

        $taoh_vals = array(
            'token' => taoh_get_api_token(1),
            'ops' => 'baseinfo',
            'mod' => 'events',
            'eventtoken' => $eventtoken ?? '',
            'cache_name' => 'event_detail_' . $eventtoken,
            'cfcc5m'=> 1,
        );
//        $taoh_vals['debug_api'] = 1;echo taoh_apicall_get('events.event.get', $taoh_vals);die;
        $data = taoh_apicall_get('events.event.get', $taoh_vals);
        echo $data;
    }
}

if (!function_exists('get_event_rsvp_status')) {
    function get_event_rsvp_status()
    {
        $eventtoken = $_POST["eventtoken"];

        $taoh_vals = array(
            'ops' => 'status',
            'mod' => 'events',
            'token' => $_POST['token'] ?? taoh_get_api_token(),
            'eventtoken' => $eventtoken ?? '',
            'cache_required' => 0,
        );
        $data = taoh_apicall_get('events.rsvp.get', $taoh_vals);

        echo $data;
    }
}

if (!function_exists('get_event_rsvp_info')) {
    function get_event_rsvp_info()
    {
        $rsvptoken = $_POST["rsvptoken"];

        $taoh_vals = array(
            'ops' => 'info',
            'mod' => 'events',
            'token' => $_POST['token'] ?? taoh_get_api_token(),
            'rsvptoken' => $rsvptoken,
            'cache_required' => 0,
        );
//        $taoh_vals['debug'] = 1;
        $data = taoh_apicall_get('events.rsvp.get', $taoh_vals);

        echo $data;
    }
}

if (!function_exists('get_event_sponsors')) {
    function get_event_sponsors()
    {
        $eventtoken = $_REQUEST["eventtoken"];

        //$cache_name = 'event_details_sponsor_3' . $eventtoken . '_' . taoh_scope_key_encode($eventtoken, 'global');
        $cache_name = 'event_details_sponsor_' . $eventtoken;
        $taoh_vals = array(
            'ops' => 'list',
            'mod' => 'events',
            'token' => $_REQUEST['token'] ?? taoh_get_api_token(1),
            'eventtoken' => $eventtoken ?? '',
            'cache_name' => $cache_name,
            'cache_time' => 7200,
            //'cfcc2h' =>1 //cfcache newly added
            //'cache' => ["name" => $cache_name, "ttl" => 7200],
        );
        $taoh_call = 'events.sponsor.get';
        //$taoh_vals['cfcache'] = $cache_name;
        ksort($taoh_vals);
        //echo taoh_apicall_get_debug($taoh_call, $taoh_vals, '', 1);die();
        $data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);

        echo $data;
    }
}

if (!function_exists('get_event_rsvped_users')) {
    function get_event_rsvped_users()
    {
        //https://ppapi.tao.ai/events.rsvp.users.list?mod=events&token=hN3PCXgw&eventtoken=h72cb237mum5wiz&time=dfasdf
        $eventtoken = $_REQUEST["eventtoken"];

        $cache_name = 'event_rsvp_users_' . $eventtoken;
        if ($_REQUEST['search'] != '') {
            $cache_name = 'event_rsvp_users_' . $eventtoken . '_' . $_REQUEST['search'];
        }

        $taoh_vals = array(
            'mod' => 'events',
            'token' => $_REQUEST['token'] ?? taoh_get_api_token(1),
            'eventtoken' => $eventtoken ?? '',
            'cache_name' => $cache_name,
            'search' => $_REQUEST['search'] ?? '',
            'ttl' => 7200,
            // 'debug' =>1,

        );
        $taoh_call = 'events.rsvp.users.list';
        ksort($taoh_vals);
     // echo taoh_apicall_get_debug($taoh_call, $taoh_vals, '', 1);die();
        $data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);

        echo $data;
    }
}


function event_email_send()
{
    //echo '<pre>';print_r($_REQUEST);die();
    $taoh_vals = array(
        "ops" => 'email',
        "mod" => 'events',
        "token" => taoh_get_dummy_token(),
        "ptoken" => $_POST['ptoken'],
        "email_type" => $_POST['email_type'],
        "event_token" => $_POST['event_token'],
        "ticket_type_slug" => $_POST['ticket_type_slug'],
        "email_title" => taoh_title_desc_encode($_POST['email_title']),
        "email_description" => taoh_title_desc_encode($_POST['email_description'])
    );

    //echo taoh_apicall_post_debug('events.rsvp.manageemail', $taoh_vals);die();
    $data = taoh_apicall_post('events.rsvp.manageemail', $taoh_vals);

    echo trim($data);
    die();
}

function get_event_MetaInfo()
{
    $eventtoken = $_POST['eventtoken'];

    $search = $type = $search_speaker_name = '';
    if (isset($_POST['type']) && $_POST['type'] != '') {
        $type = $_POST['type'];
    }
    if (isset($_POST['search']) && $_POST['search'] != '' && $_POST['search'] != 'Select Speaker Hall' && $_POST['search'] != 'All') {
        $search = $_POST['search'];
    }
    if (isset($_POST['search_speaker_name']) && $_POST['search_speaker_name'] != '') {
        $search_speaker_name = $_POST['search_speaker_name'];
    }

    $cache_name = 'event_MetaInfo_' . $eventtoken;
    if (!empty($type) || !empty($search) || !empty($search_speaker_name)) {
        $cache_name .= '_' . $type . '_' . md5($search.$search_speaker_name);
    }

    $taoh_call = "events.content.get";
    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(1),
        'eventtoken' => $eventtoken,
        'cfcc5h' => 1,
        'cache_name' => $cache_name,
    );

    if (!empty($type)) {
        $taoh_vals['type'] = $type;
    }
    if (!empty($search)) {
        $taoh_vals['search'] = $search;
    }
    if (!empty($search_speaker_name)) {
        $taoh_vals['search_speaker_name'] = $search_speaker_name;
    }

//    echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die();
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    echo $data;
    die();
}


function save_exhibitor_slot()
{
    $eventtoken = $_POST['eventtoken'];
    $is_organizer = ($_POST['is_organizer'] ?? 0) == 1 ? 1 : 0;
    $country_locked = (int)($_POST['country_locked'] ?? 0);
    unset($_POST['taoh_action'], $_POST['eventtoken'], $_POST['is_organizer'], $_POST['country_locked']);

    $rsvp_token = '';

    if (!$is_organizer) {
        // Get RSVP status
        $rsvp_status_taoh_vals = array(
            'ops' => 'status',
            'mod' => 'events',
            'token' => taoh_get_api_token(),
            'eventtoken' => $eventtoken,
            'cache_required' => 0,
        );
        $rsvp_status_result = taoh_apicall_get('events.rsvp.get', $rsvp_status_taoh_vals);
        $rsvp_status_response = taoh_get_array($rsvp_status_result);
        if (in_array($rsvp_status_response['success'], [true, 'true']) && !empty($rsvp_status_response['output'])){
            $rsvp_token = $rsvp_status_response['output']['rsvptoken'];
        }
    }

    if ($is_organizer || !empty($rsvp_token)) {
        $upload_status = true;
        $upload_error = 'upload_error';
        $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
        if (!empty($_FILES['exh_logo_upload']['name'])) {
            $exh_l_remote_file_upload_res = taoh_remote_file_upload($_FILES['exh_logo_upload'], $file_upload_url);
            if ($exh_l_remote_file_upload_res['success']) {
                $_POST['exh_logo'] = $exh_l_remote_file_upload_res['output'];
            } else {
                $upload_status = false;
                $upload_error = 'exh_logo_upload_error';
            }
        }
        if ($upload_status && !empty($_FILES['exh_banner_upload']['name'])) {
            $exh_b_remote_file_upload_res = taoh_remote_file_upload($_FILES['exh_banner_upload'], $file_upload_url);
            if ($exh_b_remote_file_upload_res['success']) {
                $_POST['exh_banner'] = $exh_b_remote_file_upload_res['output'];
            } else {
                $upload_status = false;
                $upload_error = 'exh_banner_upload_error';
            }
        }

        if ($upload_status && !empty($_POST['exh_logo'])) { //  && !empty($_POST['exh_banner'])
            $_POST['exh_room_status'] = ($_POST['exh_room_status'] ?? '') == '1' ? 1 : 0;
            $_POST['exh_session_title'] = taoh_title_desc_encode($_POST['exh_session_title']);
            $_POST['exh_subtitle'] = taoh_title_desc_encode($_POST['exh_subtitle']);
            $_POST['exh_description'] = taoh_title_desc_encode($_POST['exh_description']);
            $_POST['exh_raffle_title'] = taoh_title_desc_encode($_POST['exh_raffle_title']);
            $_POST['exh_raffle_description'] = taoh_title_desc_encode($_POST['exh_raffle_description']);

            $exhibitor_id = $_POST['exhibitor_id'];
            unset($_POST['exhibitor_id']);
            unset($_POST['exh_logo_upload']);
            unset($_POST['exh_banner_upload']);
            $taoh_call = 'events.content.post';
            $remove_array = array('event_MetaInfo_' . $eventtoken.'*' );
            $taoh_vals = [
                'mod' => 'events',
                'token' => taoh_get_api_token(1),
                'eventtoken' => $eventtoken,
                'rsvptoken' => $rsvp_token,
                'is_organizer' => $is_organizer,
                'meta_id' => $exhibitor_id,
                'toenter' => [
                    'meta_key' => 'event_exhibitor',
                    'meta_value' => $_POST,
                ],
                'cache' => ['remove' => $remove_array],
            ];

            if (!empty($_POST['sponsor_id'])) {
                $taoh_vals['sponsor_id'] = $_POST['sponsor_id'];
            }

//            $taoh_vals['debug'] = 2;echo taoh_apicall_post($taoh_call, $taoh_vals);exit();

            $res = taoh_apicall_post($taoh_call, $taoh_vals);
            $result_arr = json_decode($res, true);
            if (isset($result_arr['success']) && in_array($result_arr['success'], [true, 'true'])) {
                // rk: use upd room info logic to upd room slug
                require_once (TAOH_APP_PATH . '/events/NtwAdapterEvents.php');

                $user_info_obj = taoh_user_all_info();

                $eventsNtwAdapter = new NtwAdapterEvents();

                if($exhibitor_id){
                    // Update existing exhibitor, update room channel
                    $taoh_vals = array(
                        'ops' => 'status',
                        'status' => 'deleteroompattern',
                        'code' => TAOH_OPS_CODE,
                        'key' => $user_info_obj->ptoken,
                        'token' => taoh_get_api_token(1),
                        'eventToken' => $eventtoken
                    );

//            $taoh_vals['debug'] = 1;
//            echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();

                    $delete_result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
                    $delete_result_arr = json_decode($delete_result, true);

                    $result_arr['delete_room_status'] = $delete_result_arr;

                    if (in_array($delete_result_arr['success'], [true, 'true'])) {
                        $deleted_roomslugs = is_array($delete_result_arr['output']) ? $delete_result_arr['output'] : [];

                        foreach ($deleted_roomslugs as $deleted_roomslug) {
                            if (!empty($deleted_roomslug)) {
                                $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                                    'roomslug' => $deleted_roomslug,
                                    'eventtoken' => $eventtoken
                                ]);
                                if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                                    $room_info = $create_room_response['output'];
                                    $roomslug = $room_info['room']['keyslug'] ?? '';

                                    // Update channel
                                    $taoh_vals = array(
                                        'mod' => 'events',
                                        'token' => taoh_get_api_token(1),
                                        'eventtoken' => $eventtoken,
                                        'meta_id' => $exhibitor_id,
                                        'cache_name' => 'event_MetaInfo_' . $eventtoken . '_exhibitor_' . $exhibitor_id,
                                        'cfcc5h' => 1,
                                    );
                                    $exhibitor_result = taoh_apicall_get('events.content.detail', $taoh_vals);
                                    $exhibitor_result_arr = json_decode($exhibitor_result, true);
                                    if (in_array($exhibitor_result_arr['success'], [true, 'true']) && !empty($exhibitor_result_arr['output'])) {
                                        $exhibitor_result_arr['output']['ID'] = $exhibitor_id;

                                        $exhibitor_channels_info = $eventsNtwAdapter->constructEventExhibitorChannelInfo(
                                            $eventtoken,
                                            [$exhibitor_result_arr['output']]
                                        );

                                        $exhibitor_channel_info = $exhibitor_channels_info[0] ?? [];
                                        $keyword = $exhibitor_channel_info['global_slug'] ?? '';
                                        $eventsNtwAdapter->updateChannelInfo($roomslug, $keyword, $user_info_obj->ptoken, $exhibitor_channel_info);
                                    }
                                }
                            }
                        }
                    }

                } else {
                    // New exhibitor added, create room channel
                    $generate_room_slug_response = $eventsNtwAdapter->generateRoomSlug([
                        'country_code' => $user_info_obj->country_code,
                        'country_name' => $user_info_obj->country_name,
                        'local_timezone' => $user_info_obj->local_timezone,
                        'eventtoken' => $eventtoken,
                        'country_locked' => $country_locked ?? 0,
                    ]);
                    if (isset($generate_room_slug_response['success']) && in_array($generate_room_slug_response['success'], [true, 'true'])) {
                        $generated_roomslug = $generate_room_slug_response['roomslug'] ?? '';
                        if(!empty($generated_roomslug)) {
                            // :rk update need to use here
                            $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                                'roomslug' => $generated_roomslug,
                                'eventtoken' => $eventtoken
                            ]);

                            if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                                $room_info = $create_room_response['output'];
                                $eventsNtwAdapter->createBulkRoomInfoChannels($room_info, $user_info_obj->ptoken);
                            }
                        }
                    }
                }
            }
            echo json_encode($result_arr);
        } else {
            if($upload_status){
                $upload_error = empty($_POST['exh_logo']) ? 'exh_logo_mandatory' : 'exh_banner_mandatory';
            }
            echo json_encode(['success' => false, 'output' => $upload_error]);
        }
    } else {
        echo json_encode(['success' => false, 'output' => 'invalid_rsvp']);
    }
}

function get_event_exhibitor()
{
    $event_token = $_GET['eventtoken'] ?? '';
    $exhibitor_id = (int) $_GET['exhibitor_id'];
    $cache_name = 'event_MetaInfo_'. $event_token.'_exhibitor_'.$exhibitor_id;
    if (!empty($event_token) && !empty($exhibitor_id)) {
        $taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_api_token(1),
            'eventtoken' => $event_token,
            'meta_id' => $exhibitor_id,
            'cache_name' => $cache_name,
            'cfcc5h' => 1,
        ];
        // echo taoh_apicall_get_debug('events.content.detail', $taoh_vals);
        echo taoh_apicall_get('events.content.detail', $taoh_vals);
    } else {
        echo json_encode(['success' => false, 'output' => 'invalid_request']);
    }
}

if (!function_exists('event_save_speaker')) {
    function event_save_speaker()
    {
        $eventtoken = $_POST['eventtoken'];
        $is_organizer = ($_POST['is_organizer'] ?? 0) == 1 ? 1 : 0;
        $country_locked = (int)($_POST['country_locked'] ?? 0);
        unset($_POST['eventtoken'], $_POST['is_organizer'], $_POST['country_locked']);

        $rsvp_token = '';

        if (!$is_organizer) {
            // Get RSVP status
            $rsvp_status_taoh_vals = array(
                'ops' => 'status',
                'mod' => 'events',
                'token' => taoh_get_api_token(),
                'eventtoken' => $eventtoken,
                'cache_required' => 0,
            );
            $rsvp_status_result = taoh_apicall_get('events.rsvp.get', $rsvp_status_taoh_vals);
            $rsvp_status_response = taoh_get_array($rsvp_status_result);
            if (in_array($rsvp_status_response['success'], [true, 'true']) && !empty($rsvp_status_response['output'])) {
                $rsvp_token = $rsvp_status_response['output']['rsvptoken'];
            }
        }

        if ($is_organizer || !empty($rsvp_token)) {
            $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
            if (isset($_FILES['spk_logo_upload']['name']) && $_FILES['spk_logo_upload']['name'] != '') {
                $spk_file_upload_response = taoh_remote_file_upload($_FILES['spk_logo_upload'], $file_upload_url);
                $_POST['spk_logo_image'] = ($spk_file_upload_response['success'] == 1) ? $spk_file_upload_response['output'] : '';
                if (!$spk_file_upload_response['success']) {
                    echo json_encode(['success' => false, 'output' => 'error_uploading_file']);
                    die;
                }
            }
            if (isset($_FILES['spk_image_upload']['name']) && $_FILES['spk_image_upload']['name'] != '') {
                $spk_file_upload_response = taoh_remote_file_upload($_FILES['spk_image_upload'], $file_upload_url);
                $_POST['spk_image'] = ($spk_file_upload_response['success'] == 1) ? $spk_file_upload_response['output'] : '';
                if (!$spk_file_upload_response['success']) {
                    echo json_encode(['success' => false, 'output' => 'error_uploading_file']);
                    die;
                }
            }
            foreach ($_FILES['spk_profileimg_upload']['name'] as $imgkey => $spk_image) {
                if (isset($_FILES['spk_profileimg_upload']['name'][$imgkey]) && $_FILES['spk_profileimg_upload']['name'][$imgkey] != '') {
                    $fileArr['error'] = $_FILES['spk_profileimg_upload']['error'][$imgkey];
                    $fileArr['name'] = $_FILES['spk_profileimg_upload']['name'][$imgkey];
                    $fileArr['tmp_name'] = $_FILES['spk_profileimg_upload']['tmp_name'][$imgkey];
                    $spk_profile_file_upload_response = taoh_remote_file_upload($fileArr, $file_upload_url);
                    $_POST['spk_profileimg'][$imgkey] = ($spk_profile_file_upload_response['success'] == 1) ? $spk_profile_file_upload_response['output'] : '';

                    if (!$spk_profile_file_upload_response['success']) {
                        echo json_encode(['success' => false, 'output' => 'error_uploading_file']);
                        die;
                    }
                }
            }

            // Reindex arrays to prevent issues with missing indexes
            array_map(fn($k) => $_POST[$k] = array_values($_POST[$k] ?? []),
                ['spk_name', 'spk_desig', 'spk_company', 'spk_bio', 'spk_linkedin', 'spk_profileimg_upload', 'spk_profileimg']);

            $speaker_id = $_POST['speaker_id'];
            unset($_POST['speaker_id']);
            unset($_POST['spk_logo_upload']);
            unset($_POST['spk_image_upload']);
            unset($_POST['spk_profileimg_upload']);
            $_POST['spk_title'] = taoh_title_desc_encode($_POST['spk_title']);
            $_POST['spk_sdesc'] = taoh_title_desc_encode($_POST['spk_sdesc']);
            $_POST['spk_desc'] = taoh_title_desc_encode($_POST['spk_desc']);
            foreach ($_POST['spk_bio'] as $k => $bio) {
                $_POST['spk_bio'][$k] = taoh_title_desc_encode($bio);
            }

            $taoh_call = 'events.content.post';
            $remove_array = array('event_MetaInfo_' . $eventtoken . '*');
            $taoh_vals = [
                'mod' => 'events',
                'meta_key' => 'event_speaker',
                'token' => taoh_get_dummy_token(),
                'eventtoken' => $eventtoken,
                'rsvptoken' => $rsvp_token,
                'is_organizer' => $is_organizer,
                'meta_id' => $speaker_id,
                'toenter' => [
                    'meta_key' => 'event_speaker',
                    'meta_value' => $_POST,
                ],
                'cache' => ['remove' => $remove_array],
            ];

//            $taoh_vals['debug'] = 2;echo taoh_apicall_post($taoh_call, $taoh_vals);exit();

            $result = taoh_apicall_post($taoh_call, $taoh_vals);
            $result_arr = json_decode($result, true);
            if (isset($result_arr['success']) && in_array($result_arr['success'], [true, 'true'])) {
                // rk: use upd room info logic to upd room slug
                require_once(TAOH_APP_PATH . '/events/NtwAdapterEvents.php');

                $user_info_obj = taoh_user_all_info();

                $eventsNtwAdapter = new NtwAdapterEvents();

                if (!empty($speaker_id)) {
                    // Update existing speaker, update room channel
                    $taoh_vals = array(
                        'ops' => 'status',
                        'status' => 'deleteroompattern',
                        'code' => TAOH_OPS_CODE,
                        'key' => $user_info_obj->ptoken,
                        'token' => taoh_get_api_token(1),
                        'eventToken' => $eventtoken
                    );

//            $taoh_vals['debug'] = 1;
//            echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();

                    $delete_result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
                    $delete_result_arr = json_decode($delete_result, true);

                    $result_arr['delete_room_status'] = $delete_result_arr;

                    if (in_array($delete_result_arr['success'], [true, 'true'])) {
                        $deleted_roomslugs = is_array($delete_result_arr['output']) ? $delete_result_arr['output'] : [];

                        foreach ($deleted_roomslugs as $deleted_roomslug) {
                            if (!empty($deleted_roomslug)) {
                                $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                                    'roomslug' => $deleted_roomslug,
                                    'eventtoken' => $eventtoken
                                ]);
                                if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                                    $room_info = $create_room_response['output'];
                                    $roomslug = $room_info['room']['keyslug'] ?? '';

                                    // Update channel
                                    $taoh_vals = array(
                                        'mod' => 'events',
                                        'token' => taoh_get_api_token(1),
                                        'eventtoken' => $eventtoken,
                                        'meta_id' => $speaker_id,
                                        'cache_name' => 'event_MetaInfo_' . $eventtoken . '_speaker_' . $speaker_id,
                                        'cfcc5h' => 1,
                                    );
                                    $speaker_result = taoh_apicall_get('events.content.detail', $taoh_vals);
                                    $speaker_result_arr = json_decode($speaker_result, true);

                                    //echo "<pre>"; print_r($speaker_result_arr); die;

                                    if (in_array($speaker_result_arr['success'], [true, 'true']) && !empty($speaker_result_arr['output'])) {
                                        $speaker_result_arr['output']['ID'] = $speaker_id;

                                        $speaker_channels_info = $eventsNtwAdapter->constructEventSessionChannelInfo(
                                            $eventtoken,
                                            [$speaker_result_arr['output']]
                                        );


                                        $speaker_channel_info = $speaker_channels_info[0] ?? [];
                                        $keyword = $speaker_channel_info['global_slug'] ?? '';

                                        //echo "<pre>"; print_r($speaker_channel_info); die;

                                        $eventsNtwAdapter->updateChannelInfo($roomslug, $keyword, $user_info_obj->ptoken, $speaker_channel_info);
                                    }
                                }
                            }
                        }
                    }

                } else {
                    // New speaker added, create room channel
                    $generate_room_slug_response = $eventsNtwAdapter->generateRoomSlug([
                        'country_code' => $user_info_obj->country_code,
                        'country_name' => $user_info_obj->country_name,
                        'local_timezone' => $user_info_obj->local_timezone,
                        'eventtoken' => $eventtoken,
                        'country_locked' => $country_locked ?? 0,
                    ]);
                    if (isset($generate_room_slug_response['success']) && in_array($generate_room_slug_response['success'], [true, 'true'])) {
                        $generated_roomslug = $generate_room_slug_response['roomslug'] ?? '';
                        if (!empty($generated_roomslug)) {
                            // :rk update need to use here
                            $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                                'roomslug' => $generated_roomslug,
                                'eventtoken' => $eventtoken
                            ]);

                            if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                                $room_info = $create_room_response['output'];
                                $eventsNtwAdapter->createBulkRoomInfoChannels($room_info, $user_info_obj->ptoken);
                            }
                        }
                    }
                }
            }
            echo json_encode($result_arr);
            die();
        } else {
            echo json_encode(['success' => false, 'error' => 'invalid_rsvp']);
            die();
        }
    }
}


function speaker_get_detail(){

    $eventtoken = $_POST['eventtoken'];
    $speaker_id = $_POST['speaker_id'];


    header('Content-Type: application/html; charset=utf-8');
    include_once('events_speaker_detail.php');

}

function get_speaker_detail(){
    $eventtoken = $_POST['eventtoken'];
    $speaker_id = $_POST['speaker_id'];

    $taoh_call = "events.content.detail";
    $cache_name = 'event_MetaInfo_'. $_POST['eventtoken'].'_speaker_'.$_POST['speaker_id'];
    $taoh_vals = array(
        'mod'        => 'events',
        'token'      => taoh_get_dummy_token(1),
        'eventtoken' => $_POST['eventtoken'],
        'meta_id'    => $_POST['speaker_id'],
        'cache_name' => $cache_name,
        'cfcc5h'=> 1,
    );
    // $result = taoh_apicall_get_debug($taoh_call, $taoh_vals); die;
    $result = taoh_apicall_get($taoh_call, $taoh_vals);
    echo trim($result);
    die();
}

function update_event_exhibitor_raffle(){

    $taoh_vals = array(
        "ops" => 'event_exhibitor',
        'status' => 'post',
        'code' => TAOH_OPS_CODE,
        'token' => $_POST['token'],
        'key' => $_POST['ptoken'],
        'eventtoken' => $_POST['eventtoken'],
        'exhibitor_id' => $_POST['exhibitor_id'],
        'answer' => $_POST['answer'],
        "user_details" => [
                            'ptoken' => $_POST['ptoken'],
                            'name' => $_POST['username'],
                            'answer' => $_POST['answer']
                            ]
    );
    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);

    echo trim($result);
    die();
}

function get_event_exhibitor_raffle(){
    $taoh_vals = array(
        "ops" => 'event_exhibitor',
        'status' => 'get',
        'code' => TAOH_OPS_CODE,
        'token' => $_POST['token'],
        'key' => $_POST['ptoken'],
        'eventtoken' => $_POST['eventtoken'],
        'exhibitor_id' => $_POST['exhibitor_id'],
    );
    $result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);

    echo trim($result);
    die();
}

function update_exhibitor_rating(){
    $taoh_call = 'events.exhibitor.content.post';
    $remove_array = array('event_MetaInfo_'. $_POST['eventtoken'].'_exhibitor_'.$_POST['exhibitor_id']);
	$taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_dummy_token(),
            'type' => 'rating',
            'exhibitor_id' => $_POST['exhibitor_id'],
            'rating' => $_POST['rating'],
            'cache' => ['remove' => $remove_array],
        ];

    // $result = taoh_apicall_post_debug($taoh_call, $taoh_vals);
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo trim($result);
    die();
}

function add_exhibitor_comments(){
    unset($_POST['taoh_action']);
    $taoh_call = 'events.exhibitor.content.post';
	$taoh_vals = [
            'mod' => 'events',
            'type' => 'comment',
            'token' => taoh_get_dummy_token(),
            'exhibitor_id' => $_POST['exhibitor_id'],
            'eventtoken' => $_POST['eventtoken'],
            // 'comment' => $_POST['exh_review']
            'toenter' => [
                'meta_value' => $_POST,
            ],
        ];

    // echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo trim($result);
    die();
}

function speaker_exhibitor_save_put(){
    $taoh_call = "content.save";
    $remove = array("events_*", "event_detail_" . $_POST['eventtoken'],"event_Saved_*");

    $taoh_vals = array(
        'slug' => $_POST['slug'],
        'token' => taoh_get_dummy_token(),
        'eventtoken' => $_POST['eventtoken'],
        'redis_store' => 'taoh_intaodb_events',
        'cache' => array('remove' => $remove),
    );

    if(isset($_POST['speaker_id'])){
        $taoh_vals['speaker_id'] = $_POST['speaker_id'];
    }
    if(isset($_POST['exhibitor_id'])){
        $taoh_vals['exhibitor_id'] = $_POST['exhibitor_id'];
    }
    // echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die;
    $data = taoh_apicall_post($taoh_call, $taoh_vals);
    taoh_delete_local_cache('events', $remove);
    echo $data;
    die();
}


function get_event_saved_list(){

    //https://ppapi.tao.ai/events.content.get?mod=events&token=m20F2ftt&eventtoken=iozn99q383m1r3l

    $eventtoken = $_POST['eventtoken'];
    $cache_name = 'event_Saved_' . $eventtoken ;
    $taoh_call = "events.content.save.list";
    $search = $type = '';
    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_dummy_token(),
        'eventtoken' => $eventtoken,
        //'cfcc5m' => 1  ////cfcache newly added
    );
    $cache_name = 'event_Saved_'. $eventtoken.'_'.$type;

    $taoh_vals['cache_name'] = $cache_name;
   //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die();
    $data = taoh_apicall_get($taoh_call, $taoh_vals);
    echo $data;
    die();
  }

function event_checkin(){
$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'];
 $user_details =  array(
                'ptoken' => $sess_user_info->ptoken,
                'chat_name' => $sess_user_info->chat_name,
                'avatar' => $sess_user_info->avatar,
                'avatar_image' => $sess_user_info->avatar_image,
                'full_location' => $sess_user_info->full_location,
                'coordinates' => $sess_user_info->coordinates,
                'geohash' => $sess_user_info->geohash,
                'local_timezone' => $sess_user_info->local_timezone,
                'profile_type' => $sess_user_info->type,
                'skill' => $sess_user_info->skill,
                'title' => $sess_user_info->title,
                'company' => $sess_user_info->company,
                'site' => array(
                    'source' => '/',
                    'name' => TAOH_SITE_NAME_SLUG,
                ),
                'ticket_details' => $_POST['ticket_details'] && $_POST['ticket_details'] !='' ? json_decode($_POST['ticket_details']) : '',
                );
//echo'<pre>';print_r($sess_user_info);die();
$country = '';
if($_POST['country_locked'] == 1)
    $country = $_POST['country'];

$taoh_vals = array(
    "ops" => 'push_reg_list',
    'status' => 'post',
    'code' => TAOH_OPS_CODE,
   // 'key' => 'taoh_events_EVENTTOKEN_[COUNTRY (OPTIONAL)]_reg_list'
    'key' => 'taoh_events_'.$_POST['eventtoken'].'_'.$country.'_reg_list',
    'value' => json_encode($user_details),
    'eventtoken' => $_POST['eventtoken'],
    'ptoken' => $_POST['ptoken'],
    //'debug' => 1,


);
$result = taoh_post(TAOH_CONNECT_URL, $taoh_vals);

echo trim($result);
die();
}

function event_checkin_list()
{
    $limit_default = 20;
    $limit = $_POST['limit'] ?? $limit_default;
    $offset = 0;

    if (isset($_POST['page'])) {
        $page = (int)$_POST['page'];
        $offset = ($page - 1) * $limit;
    }

    $taoh_vals = array(
        "ops" => 'get_reg_list',
        'status' => 'get',
        'code' => TAOH_OPS_CODE,
        'key' => 'taoh_events_' . $_POST['eventtoken'] . '__reg_list',
        'offset' => $offset,
        'limit' => $limit,
        'sort' => 'DESC',
        'q' => $_POST['q'] ?? '',
        'country_locked' => $_POST['country_locked'] ?? 0,
        'eventtoken' => $_POST['eventtoken'],
        'ptoken' => $_POST['ptoken'],
        'token' => taoh_get_dummy_token(1),
        'cfcc15m' => 1,
        'user_country' => $_POST['my_country'] ?? '',
//        'debug' => 1,
    );
    $result = taoh_post(TAOH_CONNECT_URL, $taoh_vals);
    echo $result;
    die();
}

function rsvp_download(){
$event_token = $_POST['eventtoken'];

//https://ppapi.tao.ai/events.rsvp.download?mod=events&token=hT93oaWC&eventtoken=6cmogkxhxoizuzq
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_dummy_token(), // 1
    'eventtoken' => $event_token,
    'cfcc15m'=> 1,
    //'cache_required' =>0
);

//echo taoh_apicall_get_debug('events.rsvp.list', $taoh_vals);die();
// $response =  taoh_apicall_get('events.rsvp.list', $taoh_vals, '', 1);
// echo taoh_apicall_post_debug('events.rsvp.download', $taoh_vals, '', 1);die();
$response =  taoh_apicall_post('events.rsvp.download', $taoh_vals);
$result = json_decode( $response,1);
echo $response;die();
}

function delete_event_meta()
{
    $eventtoken = $_POST['eventtoken'];
    $meta_id = $_POST['meta_id'];
    $meta_type = $_POST['meta_type'] ?? '';

    $result_arr = [
        'success' => false,
        'error' => 'invalid_request'
    ];

    if (!empty($meta_id) && in_array($meta_type, ['exhibitor', 'sponsor', 'speaker'])) {
        $remove_array = [
            'event_detail_' . $eventtoken,
            'event_MetaInfo_' . $eventtoken . '*',
            'event_Saved_' . $eventtoken
        ];
        $taoh_vals = array(
            'mod' => 'events',
            'token' => taoh_get_api_token(1),
            'eventtoken' => $eventtoken,
            'meta_id' => $meta_id,
            'cache' => ['remove' => $remove_array]
        );

//    $taoh_vals['debug'] = 1;
//    echo taoh_apicall_post('events.content.delete', $taoh_vals);

        $response = taoh_apicall_post('events.content.delete', $taoh_vals);
        $result_arr = json_decode($response, true);
        if (isset($result_arr['success']) && in_array($result_arr['success'], [true, 'true'])) {
            // rk: use upd room info logic to upd room slug
            require_once(TAOH_APP_PATH . '/events/NtwAdapterEvents.php');

            $user_info_obj = taoh_user_all_info();

            $eventsNtwAdapter = new NtwAdapterEvents();

            // Remove existing, update room channel
            $taoh_vals = array(
                'ops' => 'status',
                'status' => 'deleteroompattern',
                'code' => TAOH_OPS_CODE,
                'key' => $user_info_obj->ptoken,
                'token' => taoh_get_api_token(1),
                'eventToken' => $eventtoken
            );

//        $taoh_vals['debug'] = 1;
//        echo taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);exit();

            $delete_result = taoh_post(TAOH_CACHE_CHAT_PROC_URL, $taoh_vals);
            $delete_result_arr = json_decode($delete_result, true);

            $result_arr['delete_room_status'] = $delete_result_arr;

            if (in_array($delete_result_arr['success'], [true, 'true'])) {
                $deleted_roomslugs = is_array($delete_result_arr['output']) ? $delete_result_arr['output'] : [];

                foreach ($deleted_roomslugs as $deleted_roomslug) {
                    if (!empty($deleted_roomslug)) {
                        $create_room_response = $eventsNtwAdapter->constructAndCreateRoomInfo($user_info_obj, [
                            'roomslug' => $deleted_roomslug,
                            'eventtoken' => $eventtoken
                        ]);
                        if (in_array($create_room_response['success'], [true, 'true']) && !empty($create_room_response['output'])) {
                            $room_info = $create_room_response['output'];
                            $roomslug = $room_info['room']['keyslug'] ?? '';

                            $keyword = $eventtoken;

                            if ($meta_type == 'exhibitor' || $meta_type == 'sponsor') {
                                $channel_slug_data = [$eventtoken, 'exhibitor', $meta_id];
                                $channel_id = $eventsNtwAdapter->generateChannelId($channel_slug_data);
                                $channel_type = defined('TAOH_CHANNEL_EXHIBITOR') ? TAOH_CHANNEL_EXHIBITOR : 2;

                                $exhibitor_channel_info = [
                                    'channel_id' => $channel_id,
                                    'channel_type' => $channel_type,
                                    'global_slug' => $eventtoken
                                ];
                                $eventsNtwAdapter->deleteChannel($roomslug, $keyword, $user_info_obj->ptoken, $exhibitor_channel_info);
                            }

                            if ($meta_type == 'speaker') {
                                $channel_slug_data = [$eventtoken, 'session', $meta_id];
                                $channel_id = $eventsNtwAdapter->generateChannelId($channel_slug_data);
                                $channel_type = defined('TAOH_CHANNEL_SESSION') ? TAOH_CHANNEL_SESSION : 7;

                                $speaker_channel_info = [
                                    'channel_id' => $channel_id,
                                    'channel_type' => $channel_type,
                                    'global_slug' => $eventtoken
                                ];
                                $eventsNtwAdapter->deleteChannel($roomslug, $keyword, $user_info_obj->ptoken, $speaker_channel_info);
                            }
                        }
                    }
                }
            }
        }
    }

    echo json_encode($result_arr);
}

function get_event_tables(){

    $eventtoken = $_POST['eventtoken'];
    //https://cachet4.tao.ai/mos_red_apps.php?code=tc2asi3iida2&app=demo_demo_tables
    // &ops=list&key=&key_timestamp=0&owner_id=596b0db1b957b4bb6427e178f7d4ba58&data={%22content%22:%22upcoming%22,%22status%22:%22ended%22,%22location%22:%22%22,%22limit%22:15,%22offset%22:0,%22start%22:0,%22owner_id%22:%22596b0db1b957b4bb6427e178f7d4ba58%22}
        $taoh_vals = array(
        'ops' => 'list',
        'app' => TAOH_TABLE_VERSION.'_'.$eventtoken.'_tables',                
        'code' => TAOH_OPS_CODE,
        'key' => '',
        'data' => '{"limit":15,"offset":0}',
        // 'debug' => 1
    );

               
                

    $room_data_json = taoh_post(TAOH_TABLE_REDIS_URL, $taoh_vals);
    echo $room_data_json;die();
    //$result = json_decode( $room_data_json,1);
   // echo $result;die();
}

function add_event_organizer_banner() {
    $eventtoken = $_POST['eventtoken'];
    $banner_metaid = $_POST['banner_metaid'] ?? '';
    unset($_POST['taoh_action'], $_POST['banner_metaid']);

    $upload_status = true;
    $upload_error = 'upload_error';
    $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
    if (!empty($_FILES['event_organizer_banner']['name'])) {
        $org_b_remote_file_upload_res = taoh_remote_file_upload($_FILES['event_organizer_banner'], $file_upload_url);
        if ($org_b_remote_file_upload_res['success']) {
            $_POST['organizer_banner_link'] = $org_b_remote_file_upload_res['output'];
        } else {
            $upload_status = false;
            $upload_error = 'organizer_banner_upload_error';
        }
    }

    if ($upload_status) {
        $taoh_call = 'events.content.post';
        $remove_array = array('event_MetaInfo_' . $eventtoken.'*' );
        $taoh_vals = [
            'mod' => 'events',
            'token' => taoh_get_api_token(1),
            'eventtoken' => $eventtoken,
            'is_organizer' => 1,
            'meta_id' => $banner_metaid,
            'toenter' => [
                'meta_key' => 'event_organizer_banner',
                'meta_value' => $_POST,
            ],
            'cache' => ['remove' => $remove_array],
        ];

//        $taoh_vals['debug'] = 1;
//        echo taoh_apicall_post($taoh_call, $taoh_vals);exit();

        $res = taoh_apicall_post($taoh_call, $taoh_vals);
        $result_arr = json_decode($res, true);
        if (isset($result_arr['success']) && in_array($result_arr['success'], [true, 'true'])) {
            echo json_encode(['status' => true, 'message' => 'Organizer banner added successfully.', 'result' => $result_arr]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Failed to add organizer banner.', 'result' => $result_arr]);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to upload organizer banner.']);
    }

}

function remove_event_organizer_banner()
{
    $eventtoken = $_POST['eventtoken'];
    $meta_id = $_POST['metaid'];

    $remove_array = array('event_MetaInfo_' . $eventtoken . '*');
    $taoh_vals = array(
        'mod' => 'events',
        'token' => taoh_get_api_token(1),
        'eventtoken' => $eventtoken,
        'meta_id' => $meta_id,
        'cache' => ['remove' => $remove_array]
    );

//    $taoh_vals['debug'] = 1;
//    echo taoh_apicall_post('events.content.delete', $taoh_vals);

    $response = taoh_apicall_post('events.content.delete', $taoh_vals);
    $result_arr = json_decode($response, true);
    if (isset($result_arr['success']) && in_array($result_arr['success'], [true, 'true'])) {
        echo json_encode(['status' => true, 'message' => 'Organizer banner removed successfully.']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to remove organizer banner.']);
    }
}