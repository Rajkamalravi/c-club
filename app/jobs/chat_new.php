<?php
$taoh_id = taoh_parse_url(2); //conttoken
$current_app = taoh_parse_url(0);
$slug = taoh_parse_url(0);
//$title = taoh_parse_url(6);
//$title = ( isset( $title ) && $title ) ? ucwords( urldecode( $title ) ):ucwords( urldecode( $slug ) );

$taoh_id = taoh_sanitizeInput($taoh_id);


// https://preapi.tao.ai/jobs.chat.get?mod=jobs&token=DCkmtVE1&key=8926&type=skillchat
// *****************************
// Event Key Check
// *****************************
if ( taoh_user_is_logged_in() ){
$clubkey = "";
$tried = 1;
//while( ! $clubkey && $tried >= 1 ){
    $ops = 'info';
    $mod = 'jobs';
    $conttoken = $taoh_id;
    if ( ! ctype_alnum( $conttoken ) ) { taoh_redirect( TAOH_SITE_URL_ROOT.'/'.TAOH_SITE_CURRENT_APP_SLUG );taoh_exit(); }
    
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
        
    );
   // $taoh_vals[ 'cfcache' ] = $cache_name;
    ksort($taoh_vals);
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
    $response = json_decode(taoh_apicall_get( $taoh_call,  $taoh_vals ), true);
    $response = $response[ 'output' ];
    //echo "<pre>----";print_r($response);die();

   /* $tried++;
    $taoh_call = 'jobs.chat.get';
    $taoh_call_type = 'get' ;
    $taoh_vals = array(
        "mod" => 'jobs',
        'token'=>taoh_get_dummy_token(),
        "key" => $taoh_id,
        "type" => $current_app,
        "cache" => array ( "name" => taoh_p2us('jobs.chat').'_'.$taoh_id.'_'.$current_app, "ttl" => 3600),
    );
    //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals); die();
    echo taoh_apicall_get_debug($taoh_call, $taoh_vals);exit();
    $return = taoh_apicall_get($taoh_call, $taoh_vals);
    
    //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);
    $return = json_decode($return, true);
    if ( isset( $return[ 'success' ] ) && isset( $return[ 'status' ] ) && $return[ 'success' ] && $return[ 'output' ] &&  $return[ 'status' ] == 'redirect' ) {
        $clubkey = $return[ 'output' ];
    }
}
if ( ! $clubkey ){
    //$forward_url =TAOH_SITE_URL_ROOT."/".TAOH_CURR_APP_SLUG."/d/".$taoh_id;
	taoh_redirect( $forward_url );
    taoh_exit();
}*/
/*if($response['success']){
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_title"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_title"] = $response['title'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_short"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_short"] = $response['description'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_global"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_global"] = $response['publish_global'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_logo"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_logo"] = $response[''];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_app"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_app"] = $response['type_slug'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_type"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_type"] = $response['type'];
    if(isset($_COOKIE[ "taoh_networking_".$taoh_id."_ownerptoken"]))
    $_COOKIE[ "taoh_networking_".$taoh_id."_ownerptoken"] = $response['ptoken'];
}*/

//echo "<pre>----";print_r($_COOKIE);die();

//$forward_url = TAOH_SITE_URL_ROOT."/fwd/club/$clubkey";
//echo $forward_url; echo taoh_get_dummy_token(); print_r($return);die();
$forward_url =TAOH_SITE_URL_ROOT."/".TAOH_NETWORKPAGE_NAME."/d/".$taoh_id;
taoh_redirect( $forward_url );taoh_exit();

}else{
    $forward_url = TAOH_SITE_URL_ROOT."/$current_app";
    taoh_redirect( $forward_url );taoh_exit();
}