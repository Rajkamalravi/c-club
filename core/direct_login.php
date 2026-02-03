<?php
/*
This page only run if accesscode exist on login link
Direct Login coming from email link

*/
$current_app = taoh_parse_url(1);
$access_code = taoh_parse_url(2);
//echo "current_app = $current_app; access_code = $access_code;"; taoh_exit();
if ( taoh_user_is_logged_in() ){
  if ( strstr( $_SERVER[ 'REQUEST_URI' ], $access_code ) ){
    list($pre, $forward_url) = explode( "$current_app/$access_code", $_SERVER[ 'REQUEST_URI' ] );
  } elseif ( strstr( $_SERVER[ 'REQUEST_URI' ], mb_strtoupper( $access_code ) ) ){
    list($pre, $forward_url) = explode( "$current_app/".mb_strtoupper( $access_code ), $_SERVER[ 'REQUEST_URI' ] );
  }
  { header( "Location: $forward_url" );taoh_exit(); }
}

$app_data = taoh_app_info();
//Check valid accesscode
//$api = TAOH_USER_ACCOUNT_TEMP."?secret=".TAOH_API_SECRET."&mod=".$config['slug']."&cmd=create&q=".urlencode( $current_app.":::".$access_code );
//$return = json_decode(taoh_url_get_content( $api ), true);
$taoh_call = "account.temps";
$taoh_vals = array(    
  'secret'=>TAOH_API_SECRET,
  'mod'=>($app_data?->slug ?? ''),
  'cmd'=>'create',
  'q'=>urlencode( $current_app.":::".$access_code ),
); 
$taoh_call_type = "get";
$return = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );

//echo taoh_url_get( $api );
//print_r($return); echo $api; taoh_exit();
if ( isset( $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'taoh_referral' ] ) ) {
  unset( $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'taoh_referral' ] );
  setcookie(TAOH_ROOT_PATH_HASH.'_'.'taoh_referral', null, -1, '/');
}
//Check token valid
if( isset( $return['token'] ) ) {
  taoh_clean_cookie();
  setcookie( TAOH_ROOT_PATH_HASH.'_'."taoh_api_token", $return['token'], strtotime( '+30 days' ), '/'  );

  $forward_url = '';
  if ( strstr( $_SERVER[ 'REQUEST_URI' ], $access_code ) ){
    list($pre, $forward_url) = explode( "$current_app/$access_code", $_SERVER[ 'REQUEST_URI' ] );

  } elseif ( strstr( $_SERVER[ 'REQUEST_URI' ], mb_strtoupper( $access_code ) ) ){
    list($pre, $forward_url) = explode( "$current_app/".mb_strtoupper( $access_code ), $_SERVER[ 'REQUEST_URI' ] );
  }
  //echo $forward_url;taoh_exit();
  if ( $forward_url ) { header( "Location: $forward_url" );taoh_exit(); }
  else { taoh_redirect(TAOH_SETTINGS_URL); taoh_exit(); }
} else {
  taoh_redirect(TAOH_LOGIN_URL."/$current_app");
}
exit;
?>
