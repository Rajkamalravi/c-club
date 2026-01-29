<?php
$dash = array();
//$dash[ 'from' ][ 'website' ] = TAOH_SITE_URL_ROOT;
//$dash[ 'from' ][ 'plugin' ] = TAOH_WERTUAL_SLUG;
//$dash[ 'from' ][ 'plugin' ] = TAOH_PLUGIN_PATH_NAME;
//$dash[ 'from' ][ 'page' ] = $_GET[ 'from' ];
$dash[ 'to' ] = $_GET[ 'to' ];
//$dash[ 'to' ] = $_GET[ 'to' ];
$site = TAOH_DASH_PREFIX;

$url = TAOH_SITE_URL_ROOT;
/*if(TAOH_API_TOKEN){
  $url = "$site/u/".$_GET[ 'app' ].'/'.TAOH_ROOT_PATH_HASH.'/'.TAOH_API_TOKEN.'/'.$_GET[ 'to' ];
}

//echo $url;taoh_exit();
header ("Location: $url");taoh_exit();*/

$current_app = TAOH_PLUGIN_PATH_NAME;
$taoh_call = 'users.uuid.add';
$taoh_call_type = 'POST';
$taoh_vals = array(
  'token'=> TAOH_API_TOKEN,
  'sub_secret_token' => TAOH_ROOT_PATH_HASH,
  'to' => $_GET[ 'to' ],
  'app' => $_GET[ 'app' ],
  //'mod' => 'dash',
  //'toenter' => $dash,
  //'available_apps' => taoh_available_apps(),
  //'source' => TAOH_SITE_URL_ROOT,

  //'site' => TAOH_SITE_TITLE,
  //'url' => TAOH_SITE_URL,
  //'path' => TAOH_WERTUAL_SLUG,
  //'path' => TAOH_PLUGIN_PATH_NAME,
  //'logo' => TAOH_SITE_LOGO,
  //'full_name' => taoh_user_full_name(),
);
 $vals = json_encode($taoh_vals);
  $post_data = array(
      'code' => 'tc2asi3iida2',
      'ops' => 'uuid',
      'value' => $vals,
      'status' => 'post',
      //'debug'=> 1,
  );
  $return = taoh_remote_cache( $post_data );
  $result = json_decode($return, true);

//echo "<pre>";print_r($result);//die();*/
$url = TAOH_SITE_URL_ROOT;
if ( $result[ 'success' ] && $result[ 'output' ] ){
  if ( ctype_alnum( str_ireplace( '_', '', $result[ 'output' ] ) ) ){
    $url = "$site/u/".$result[ 'output' ];
  }
}

//echo $url;taoh_exit();
header ("Location: $url");taoh_exit();
?>
