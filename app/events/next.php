<?php
// log php error to screen
//error_reporting(E_ALL);ini_set('display_errors', 1);
$conttoken = taoh_parse_url(2);
@$conttoken = array_pop( explode( '-', $conttoken ) );

$offset = taoh_parse_url(3) ?? '';
$offset = is_numeric($offset) ? $offset : '';

//$token_add = "&token=".taoh_get_dummy_token();
$force = '';
if ( isset( $_GET[ 'force' ] ) && $_GET[ 'force' ] ) $force = '&force=1';
//$url = EVENTS_EVENT_GET."?&ops=nextevent&mod=events&conttoken=".$conttoken.$token_add."&token=".taoh_get_dummy_token();
//echo $url;exit();
//$response = json_decode(taoh_url_get_content( $url ), true);
if ( !ctype_alnum( $conttoken ) ) {
  taoh_redirect( TAOH_EVENTS_URL );
  taoh_exit();
}
$cache_name = TAOH_ROOT_PATH_HASH.'_event_next_' . $conttoken.'_'.$offset ;

$taoh_call = "events.event.get";
$taoh_vals = array(
    'ops' => 'nextevent',
    'mod' => 'events',
    'conttoken' => $conttoken,
    'offset' => $offset,
    'token' => taoh_get_dummy_token(1),
    'cache_name' => $cache_name,
    'cache_time' => 3600,
    'cache' => array ( "name" => $cache_name,  "ttl" => 3600),
    //'cfcc1h' => 1 //cfcache newly added
);
//$taoh_vals[ 'cfcache' ] = $cache_name;
ksort($taoh_vals);

$taoh_call_type = "get";
//print_r($taoh_vals );die();
// echo taoh_apicall_get_debug( $taoh_call, $taoh_vals ); die;
$response = json_decode(taoh_apicall_get( $taoh_call, $taoh_vals ), true);
$url = TAOH_EVENTS_URL;
//echo'<pre>';print_r($response);exit();
if ( isset( $response[ 'output' ][ 'conttoken' ][ 'title' ] ) && $response[ 'output' ][ 'conttoken' ][ 'title' ] ){
  $url = $url."/d/".taoh_slugify($response[ 'output' ][ 'conttoken' ][ 'title' ]).'-'.$response[ 'output' ][ 'eventtoken' ];
} else if ( isset( $response[ 'output' ][ 'title' ] ) && $response[ 'output' ][ 'title' ] ){
  $url = $url."/d/".taoh_slugify($response[ 'output' ][ 'title' ]).'-'.$response[ 'output' ][ 'eventtoken' ];
}
//echo $url;exit();
taoh_redirect($url);
taoh_exit();
?>
