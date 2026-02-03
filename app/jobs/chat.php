<?php

$request_type = taoh_parse_url(2);
$current_app = taoh_parse_url(3);
$taoh_id = taoh_parse_url(4);
$slug = taoh_parse_url(5);
$title = taoh_parse_url(6);
$title = ( isset( $title ) && $title ) ? ucwords( urldecode( $title ) ):ucwords( urldecode( $slug ) );

// https://preapi.tao.ai/jobs.chat.get?mod=jobs&token=DCkmtVE1&key=8926&type=skillchat
// *****************************
// Event Key Check
// *****************************

$clubkey = "";
$tried = 1;
while( ! $clubkey && $tried >= 1 ){
    $tried++;
    $taoh_call = 'jobs.chat.get';
    $taoh_call_type = 'get' ;
    $taoh_vals = array(
        "mod" => 'jobs',
        'token' => taoh_get_dummy_token(),
        "key" => $taoh_id,
        "type" => $current_app,
        //"cache" => array("name" => taoh_p2us('jobs.chat') . '_' . $taoh_id . '_' . $current_app, "ttl" => 3600),
    );
    //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals); die();
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
    $return = taoh_apicall_get($taoh_call, $taoh_vals);
    
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);
    $return = json_decode($return, true);
    if ( isset( $return[ 'success' ] ) && isset( $return[ 'status' ] ) && $return[ 'success' ] && $return[ 'output' ] &&  $return[ 'status' ] == 'redirect' ) {
        $clubkey = $return[ 'output' ];
    }
}


if ( ! $clubkey ){
    $forward_url =TAOH_SITE_URL_ROOT."/".TAOH_CURR_APP_SLUG."/d/".$taoh_id;
	taoh_redirect( $forward_url );
    taoh_exit();
}

$forward_url = TAOH_SITE_URL_ROOT."/fwd/club/$clubkey";
//echo $forward_url; echo taoh_get_dummy_token(); print_r($return);die();
taoh_redirect( $forward_url );taoh_exit();
