<?php
//  ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

if (file_exists('widgets/likes/ajax.php')) {
    include_once('widgets/likes/ajax.php');
}

include_once('club/ajax.php');
include_once('learning/flashcards/ajax.php');
include_once('learning/blog/ajax.php');
include_once('learning/newsletter/ajax.php');
include_once('widgets/follow/ajax.php');
//include_once('widgets/share/ajax.php');

//ajax for custom apps
foreach (taoh_available_apps() as $file) {
    $url = TAOH_PLUGIN_PATH . "/app/" . $file . "/ajax.php";
    if (file_exists($url)) {
        require $url;
    }
}

if (isset($_REQUEST['taoh_action'])) {
    if (function_exists($_REQUEST['taoh_action'])) {
        if (taoh_is_wp() == 1) {
            add_action('wp_ajax_nopriv_' . $_REQUEST['taoh_action'], $_REQUEST['taoh_action']);
            add_action('wp_ajax_' . $_REQUEST['taoh_action'], $_REQUEST['taoh_action']);
        } else {
            return $_REQUEST['taoh_action']();
            taoh_exit();
        }
    } else {
        header("HTTP/1.0 404 NotFound");
        echo "No method defined";
    }
}
function check_captcha() {
  // Build the URL for captcha verification
 // $captchaVerifyUrl = strtolower(TAOH_SITE_CAPTCHA_VERIFY . "/" . ($_POST['we_code'] - strlen(TAOH_SITE_TITLE)) . "/" . $_POST['we_word']);
  /*$captchaVerifyUrl = strtolower(TAOH_SITE_CAPTCHA_VERIFY . "/34/cycle");
  // Verify captcha
  $checkCaptcha = taoh_url_get_content($captchaVerifyUrl);
  $response = (int)$checkCaptcha;*/

  $response = 1;
  if ($response === 1) {
      // Set email cookie
      setcookie(TAOH_ROOT_PATH_HASH . '_tao_api_email', $_POST['email'], strtotime('+30 days'), '/');

      // Prepare data for API call
      $taoh_vals = [
          'secret' => TAOH_API_SECRET,
          'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
          'cmd' => 'verify',
          'q' => urlencode($_POST['app'] . ":::" . $_POST['email']),
          'time' => time(),
          'cache_required'=>0,
         // 'debug' => 1,
      ];

      // Include referral if present
      if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_taoh_referral'])) {
          $taoh_vals['referral'] = $_COOKIE[TAOH_ROOT_PATH_HASH . '_taoh_referral'];
      }

      // Make API call
      //echo taoh_apicall_get_debug('account.temps', $taoh_vals);die();
      $response = taoh_apicall_get('account.temps', $taoh_vals);

      // Do not need to wait for email response
      //$response = 1;
  }

  // Return response
  echo $response;
  taoh_exit();
}


function update_lock_status() {
  // Prepare data to update lock status
  $scoutaray = ['user_locked' => 1];
  $taoh_call = 'users.tao.add';
  $taoh_vals = [
      'token' => $_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'],
      'mod' => 'tao_tao',
      'toenter' => $scoutaray,
      // 'debug'=>1,
  ];

  //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
  // Make the API call to update the lock status
  $result = taoh_apicall_post($taoh_call, $taoh_vals);
  //print_r($result);die();
  $decodedResult = json_decode($result, true);
  //print_r($decodedResult);die;
  // Set the necessary cookies
  setcookie(TAOH_ROOT_PATH_HASH . '_locked', '0', strtotime('+1 days'), '/');
  setcookie(TAOH_ROOT_PATH_HASH . '_enable_lock_screen', '1', strtotime('-1 days'), '/');

  // Update session information
  taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => $decodedResult]);

  // Set response header and return response
  header('Content-Type: application/json; charset=utf-8');

  // Return success or error based on the API response
  if ($decodedResult) {
      echo json_encode(['status' => 1]);
  } else {
      echo json_encode(['status' => 0, 'message' => 'Failed to update lock status']);
  }

  taoh_exit();
}


function check_access_code() {
  // Determine if lock code is required
  $lock_code_required = TAOH_LOGIC_LOCK_CODE != 0 && TAOH_LOGIC_LOCK_CODE != 'no' ? 1 : 0;
  // Prepare API call parameters
  $taoh_vals = [
      'secret' => TAOH_API_SECRET,
      'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
      'cmd' => 'create',
      'q' => urlencode($_POST['app'] . ":::" . $_POST['id']),
      'site_locked' => $lock_code_required,
      'cache_required' => 0,

  ];

  //echo taoh_apicall_get_debug('account.temps', $taoh_vals);die();
  // Make API call
  //echo taoh_apicall_get_debug('account.temps', $taoh_vals);
  $res = json_decode(taoh_apicall_get('account.temps', $taoh_vals), true);

  //echo '<pre>';print_r($res);die();
  // Default response
  $response = $res['success'] && $res['success'] !='' && $res['success'] != false ? 1 : 0;

  //echo "=========".$response;die();
  if ($response) {
      $token = $res['output'];

      if(TAOH_SIMPLE_LOGIN && isset($res['is_new_user']) && $res['is_new_user']){
        setcookie( TAOH_ROOT_PATH_HASH.'_anonymous', 1, strtotime( '+1 days' ),'/');
      }
  
      setcookie( TAOH_ROOT_PATH_HASH.'_taoh_api_token', $token, strtotime( '+30 days' ),'/');
      setcookie( TAOH_ROOT_PATH_HASH.'_temp_api_token', $token, strtotime( '+1 days' ),'/');

        if(
  
          (
            isset($res['is_new_user']) && $res['is_new_user'] == 1 
          ) || 
          (
            isset($res['site_locked']) && $res['site_locked'] == 1 && 
            isset($res['is_new_user']) && $res['is_new_user'] == 0 && 
            isset($res['user_locked']) && $res['user_locked'] == 0
          )
  
        ){
          //TAOH_LOGIC_LOCK_CODE
          if($lock_code_required){
              setcookie( TAOH_ROOT_PATH_HASH.'_enable_lock_screen', '1', strtotime( '+1 days' ),'/');
              setcookie( TAOH_ROOT_PATH_HASH.'_locked', '1', strtotime( '+1 days' ),'/');
              $response = 2; //show lock screen
              //$_SESSION[TAOH_ROOT_PATH_HASH]['enable_lock_screen'] = 1;
             // setcookie( TAOH_ROOT_PATH_HASH.'_temp_api_token', $token, strtotime( '+1 days' ),'/');
          }
        }
      /*
      // Set cookies for new users
      if (TAOH_SIMPLE_LOGIN && !empty($res['is_new_user'])) {
          setcookie(TAOH_ROOT_PATH_HASH . '_anonymous', 1, strtotime('+1 days'), '/');
      }

      // Set API token cookie
      setcookie(TAOH_ROOT_PATH_HASH . '_temp_api_token', $token, strtotime('+1 days'), '/');

      // Check if lock screen should be shown
      if (TAOH_LOGIC_LOCK_CODE) {
          $showLockScreen = (
              !empty($res['is_new_user']) ||
              (!empty($res['site_locked']) && empty($res['user_locked']) && empty($res['is_new_user']))
          );

          if ($showLockScreen) {
              setcookie(TAOH_ROOT_PATH_HASH . '_enable_lock_screen', '1', strtotime('+1 days'), '/');
              setcookie(TAOH_ROOT_PATH_HASH . '_locked', '1', strtotime('+1 days'), '/');
              $response = 2; // Show lock screen
          }
      }*/
  }

  // Output response and exit
  echo $response;
  taoh_exit();
}
function check_sso_login() {
	//echo '=============';
     setcookie( TAOH_ROOT_PATH_HASH.'_'."tao_api_email", $_POST[ 'email' ], strtotime( '+30 days' ), '/'  );
     $taoh_call = 'account.temps';
     $taoh_call_type = 'get';
     $taoh_vals = array(
       'secret' => TAOH_API_SECRET,
       'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
       'cmd' => 'ssocreate',
       'cache_required'=>0,
       //'q' => urlencode( $_POST['app'].":::".$_POST[ 'email' ].":::".$_POST[ 'first_name' ].":::".$_POST[ 'last_name' ].":::".$_POST[ 'social_id' ].":::".$_POST[ 'from' ]),
	   'q' => urlencode( $_POST['app'].":::".$_POST[ 'email' ].":::".$_POST[ 'first_name' ].":::".$_POST[ 'last_name' ].":::".$_POST[ 'from' ]),
     );
	  //echo "<pre>";print_r($taoh_vals);
     if ( isset( $_COOKIE[ 'taoh_referral' ] ) ) $taoh_vals[ 'referral' ] = $_COOKIE[ 'taoh_referral' ];
     //$res = taoh_apicall_get( $taoh_call,  $taoh_vals );
     //dont need to wait for the email response.
     $res = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );
	 //echo "<pre>";print_r($res);
    $response = $res['success'];
      /* if($response == 1) {
        $token = $res['output'];
        setcookie( TAOH_ROOT_PATH_HASH.'_'."taoh_api_token", $token, strtotime( '+30 days' ), '/'  );
        $user_data = taoh_user_all_info_settings($token);
        if ( isset( $user_data->fname ) && isset( $user_data->lname ) && isset( $user_data->type ) && (isset( $user_data->fname ) && $user_data->tao_tao_status == 20) ) {
          $response = 2;
        }
      } else {
        setcookie( TAOH_ROOT_PATH_HASH.'_'."tao_login_try", $_POST['retry'], strtotime( '+1 hour' ), '/'  );
      } */
      if($response == 1) {
        $token = $res['output'];

        // Set API token cookie
        setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $token, strtotime('+30 days'), '/');
        setcookie(TAOH_ROOT_PATH_HASH . '_temp_api_token', $token, strtotime('+1 days'), '/');

        // Set email cookie if provided
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            setcookie(TAOH_ROOT_PATH_HASH . '_tao_api_email', $_POST['email'], strtotime('+30 days'), '/');
        }

        // Save token to session for immediate availability
        taoh_session_save(TAOH_ROOT_PATH_HASH, ['TAOH_API_TOKEN' => $token]);

        $response = 1;
      }  else {
          $response = 0;
      }     


      echo $response;
   taoh_exit();
 }

function check_social_login() {
  
     setcookie( TAOH_ROOT_PATH_HASH.'_'."tao_api_email", $_POST[ 'email' ], strtotime( '+30 days' ), '/'  );
     $taoh_call = 'account.temps';
     $taoh_call_type = 'get';
     $taoh_vals = array(
       'secret' => TAOH_API_SECRET,
       'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
       'cmd' => 'social',
       'cache_required'=>0,
       'q' => urlencode( $_POST['app'].":::".$_POST[ 'email' ].":::".$_POST[ 'first_name' ].":::".$_POST[ 'last_name' ].":::".$_POST[ 'social_id' ].":::".$_POST[ 'from' ]),
     );
     if ( isset( $_COOKIE[ 'taoh_referral' ] ) ) $taoh_vals[ 'referral' ] = $_COOKIE[ 'taoh_referral' ];
     //$res = taoh_apicall_get( $taoh_call,  $taoh_vals );
     //dont need to wait for the email response.
     $res = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );
    $response = $res['success'];
      if($response == 1) {
        $token = $res['output'];
        setcookie( TAOH_ROOT_PATH_HASH.'_'."taoh_api_token", $token, strtotime( '+30 days' ), '/'  );
        $user_data = taoh_user_all_info_settings($token);
        if ( isset( $user_data->fname ) && isset( $user_data->lname ) && isset( $user_data->type ) && (isset( $user_data->fname ) && $user_data->tao_tao_status == 20) ) {
          $response = 2;
        }
      } else {
        setcookie( TAOH_ROOT_PATH_HASH.'_'."tao_login_try", $_POST['retry'], strtotime( '+1 hour' ), '/'  );
      }
      echo $response;
   taoh_exit();
 }

function check_settings_filled() {
	//$user_api = TAOH_API_PREFIX."/users.tao.get?cacheo=0&mod=tao_tao&token=".TAOH_API_TOKEN;
	//$user_data = json_decode(taoh_url_get_content($user_api));
  $taoh_call = "users.tao.get";
  $taoh_vals = array(
    'mod'=>'tao_tao',
    'token'=>TAOH_API_TOKEN,
    
  );
  $taoh_call_type = "get";
  $user_data = json_decode(taoh_apicall_get( $taoh_call, $taoh_vals ));
  print_r($user_data);
	if(isset($user_data->profile_status)) {
		if($user_data->profile_status->status == 'done') {
			echo 'yes';
		} else {
			echo 'no';
		}
	} else {
		echo 'no';
	}
  taoh_exit();
}

function verify_email() {
  $taoh_call = 'account.temps';
  $taoh_vals = [
      'secret' => TAOH_API_SECRET,
      'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
      'ops' => 'hash',
      'q' => $_POST['email_code'],
      'email' => $_POST['email'],
      'cache_required'=>0,
  ];

  $res = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);

  if (isset($res['success']) && $res['success'] == 1) {
      $token = $res['output'];
      setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $token, strtotime('+30 days'), '/');
      $response = 1;
  } else {
      $response = 0;
  }

  echo json_encode(['status' => $response]);
  taoh_exit();
}

function taoh_get_timezone_by_location_selected() {
	$cur_lat = $_POST['lat'];
	$cur_long = $_POST['lon'];
	$country_code = strtoupper(custom_trim($_POST['countryCode']));
  $output = TAOH_DEFAULT_TIMEZONE;
  $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
                                  : DateTimeZone::listIdentifiers();

  if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {
      $time_zone = '';
      $tz_distance = 0;
      //only one identifier?
      if (count($timezone_ids) == 1) {
          $time_zone = $timezone_ids[0];
      } else {
          foreach($timezone_ids as $timezone_id) {
              $timezone = new DateTimeZone($timezone_id);
              $location = $timezone->getLocation();
              $tz_lat   = $location['latitude'];
              $tz_long  = $location['longitude'];

              $theta    = $cur_long - $tz_long;
              $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
              + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
              $distance = acos($distance);
              $distance = abs(rad2deg($distance));
              // echo '<br />'.$timezone_id.' '.$distance;

              if (!$time_zone || $tz_distance > $distance) {
                  $time_zone   = $timezone_id;
                  $tz_distance = $distance;
              }
          }
      }
      $output = $time_zone;
  }
  echo $output;
	taoh_exit();
}

function metrics_put() {
  $values = json_encode(array($_POST['conttoken'],'asks',$_POST['ptoken'],$_POST['met_click'],time(),TAOH_API_SECRET));
  print_r($values);die;
  $data = taoh_cacheops( 'metricspush', $values );
  print_r($data);die;
  echo $data;
  die();
}

function taoh_resend_code() {
  //$api = TAOH_API_PREFIX."/account.temps?secret=".TAOH_API_SECRET."&mod=".$_POST['app'] ."&cmd=verify&q=".urlencode( $_POST['app'] .":::".$_COOKIE[ 'tao_api_email' ]);
  //$response = taoh_url_get($api);
  $taoh_call = 'account.temps';
  $taoh_call_type = 'get';
  $taoh_vals = array(
    'secret' => TAOH_API_SECRET,
    'mod' => ( isset($_POST['slug']) && $_POST['slug'] ) ? $_POST['slug']:TAOH_WERTUAL_SLUG,
    'cmd' => 'verify',
    'cache_required'=>0,
    'q' => urlencode( $_POST['app'] .":::".$_COOKIE[TAOH_ROOT_PATH_HASH. '_tao_api_email' ]),
  );
  //if ( isset( $_COOKIE[ 'taoh_referral' ] ) ) $taoh_vals[ 'referral' ] = $_COOKIE[ 'taoh_referral' ];
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );

 if($response == 1) {
   echo "1";
 } else {
   echo "0";
 }
 taoh_exit(); 
}

function taoh_get_ops_token() {
  //https://cdn.tao.ai/health/opstoken/y2Ds3ugv
   $api = TAOH_SITE_OPSTOKEN;
   $response = json_decode(taoh_url_get_content($api));
   echo $response;
   taoh_exit();
}

function taoh_get_notifications() {
  //?q = unread/all
  //?status = all
  //old - https://opslogy.com:8443/notify/?&func=get&q=unread&status=all&proj=all&opstoken=l1f15p8j2hws&perpage=5
  //https://cdn.tao.ai/func/ops/notify?&func=get&q=unread&status=all&proj=all&opstoken=l1f15p8j2hws&perpage=5
  $api = TAOH_SITE_NOTIFICATION."?func=get&q=".$_POST['status']."&status=all&proj=all&opstoken=".$_POST['opsToken']."&perpage=".$_POST['limit'];
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_delete_notifications() {
  //?q = id
  //old - https://opslogy.com:8443/notify/?&func=put&q=4&status=delete&proj=all&opstoken=l1f15p8j2hws&perpage=5
  //https://cdn.tao.ai/func/ops/notify?func=put&q=4&status=delete&proj=all&opstoken=l1f15p8j2hws&perpage=5
  $api = TAOH_SITE_NOTIFICATION."?func=put&q=".$_POST['id']."&status=delete&proj=all&opstoken=".$_POST['opsToken'];
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_delete_all_notifications() {
  //old - https://opslogy.com:8443//notify/?func=put&q=all&status=delete&proj=all&opstoken=l1f15p8j2hws
  // https://cdn.tao.ai/func/ops/notify/?func=put&q=all&status=delete&proj=all&opstoken=l1f15p8j2hws
  $api = TAOH_SITE_NOTIFICATION."?func=put&q=all&status=delete&proj=all&opstoken=".$_POST['opsToken'];
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_read_all_notifications() {
  //?q = all/id
  //old - https://opslogy.com:8443/notify/?func=put&q=all&status=read&proj=all&opstoken=l1f15p8j2hws
  // https://cdn.tao.ai/func/ops/notify/?func=put&q=all&status=read&proj=all&opstoken=l1f15p8j2hws
  $api = TAOH_SITE_NOTIFICATION."?func=put&q=all&status=read&proj=all&opstoken=".$_POST['opsToken'];
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_ping_alive() {
  if ( ! defined( 'TAOH_API_TOKEN' ) ) taoh_exit();
  //https://cdn.tao.ai/func/ops/ka/?func=get&ops=replace&opstoken=l1f15p8j2hws
  $api = TAOH_SITE_PING."?func=get&ops=replace&opstoken=".$_POST['opsToken'];
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_get_location()
{
    $taoh_call = 'mapn';

    $taoh_vals['op'] = 'city';
    $taoh_vals['city'] = $_POST['query'] ?? '';
    //$taoh_vals['cfcc1d'] = 1; ////cfcache newly added
    if($_POST['country_code']) {
        $taoh_vals['country_code'] = $_POST['country_code'];
    }
    $taoh_vals['offset'] = max(0, (int)($_POST['offset'] ?? 0));

    //$prefix = 'https://cdn.tao.ai';
    $prefix = TAOH_OPS_PREFIX;
    //echo taoh_apicall_get_debug_log($taoh_call, $taoh_vals, $prefix);exit();
    //$taoh_vals['debug'] = 2;echo taoh_apicall_get($taoh_call, $taoh_vals, $prefix);exit();
    $response = taoh_apicall_get($taoh_call, $taoh_vals, $prefix);
    header('Content-Type: application/json; charset=utf-8');
    echo $response;
    taoh_exit();

    //$api = TAOH_OPS_PREFIX.'/mapn/?op=city&city='.$_POST['query'];
    //$response = taoh_url_get( $api );
}

function taoh_get_geohash() {
  //https://cdn.tao.ai/mapn/?op=geohash&long=-87.9090&lat=41.9803
  $taoh_call = 'mapn';
  $taoh_call_type = 'get';
  $taoh_vals = array( 'op' => 'geohash', 'long' => $_POST['lon'], 'lat' => $_POST['lat'] );
  //$prefix = 'https://cdn.tao.ai';
  $prefix = TAOH_OPS_PREFIX;
  $response = taoh_apicall_get( $taoh_call,$taoh_vals, $prefix );
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_get_roles() {
  //https://cdn.tao.ai/api/find.php?mod=tao_je&code=NLi9kS3X&type=title&ctype=token&term=ta
  //$api = TAOH_SITE_SCRIPT_FIND."?mod=tao_je&code=".TAOH_API_TOKEN."&type=title&ctype=token&term=".$_POST['query'];
  //echo $api;taoh_exit();
  //$response = taoh_url_get_content($api);
  $taoh_call = 'scripts/find.php';
  $taoh_call_type = 'get';
    $taoh_vals = array(
        'mod' => 'tao_je',
        'code' => taoh_get_dummy_token(true),
        'type' => 'title',
        'ctype' => 'token',
        'term' => $_POST['query'],
    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  /*header('Content-Type: application/json; charset=utf-8');
  echo $response;*/
  $result = json_decode($response, true);
  $res=[];
  foreach ($result as $key => $value) {
    //if(!str_contains(',', $value['label'] )){
    if (strpos($value['label'], ',') == false) {
      $newarr = array(
        'label' => $value['label'],
        'value' => $value['value'],
        'id' => $value['id'],
      );
      array_push($res,$newarr);
    }
    //print_r($res);die();
  }
    
  header('Content-Type: application/json; charset=utf-8');
  //print_r($res);die();
  //print_r($response);die();
  echo json_encode($res);
  taoh_exit();
}

function taoh_get_companies() {
  //https://cdn.tao.ai/api/find.php?mod=tao_je&code=NLi9kS3X&type=company&ctype=token&term=ta
  //$api = TAOH_SITE_SCRIPT_FIND."?mod=tao_je&code=".TAOH_API_TOKEN."&type=company&ctype=token&term=".$_POST['query'];
  //$response = taoh_url_get_content($api);
  $taoh_call = 'scripts/find.php';
  $taoh_call_type = 'get';
  $taoh_vals = array(
     'mod' => 'tao_je', 
     'code' => taoh_get_dummy_token(true), 
     'type' => 'company', 
     'ctype' => 'token', 
     'term' => $_POST['query'],

    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  /*header('Content-Type: application/json; charset=utf-8');
  echo $response;*/
  $result = json_decode($response, true);
  $res=[];
  foreach ($result as $key => $value) {
    //if(!str_contains(',', $value['label'] )){
    if (strpos($value['label'], ',') == false) {
      $newarr = array(
        'label' => $value['label'],
        'value' => $value['value'],
        'id' => $value['id'],
      );
      array_push($res,$newarr);
    }
    //print_r($res);die();
  }
    
  header('Content-Type: application/json; charset=utf-8');
  //print_r($res);die();
  //print_r($response);die();
  echo json_encode($res);
  taoh_exit();
}

function taoh_get_skills() {
  //https://cdn.tao.ai/api/find.php?mod=tao_je&code=NLi9kS3X&type=skill&ctype=token&term=ta
  //$api = TAOH_SITE_SCRIPT_FIND ."?mod=tao_je&code=".TAOH_API_TOKEN."&type=skill&ctype=token&term=".$_POST['query'];
  //$response = taoh_url_get_content($api);
  $taoh_call = 'scripts/find.php';
  $taoh_call_type = 'get';
  $taoh_vals = array(
     'mod' => 'tao_je', 
     'code' => taoh_get_dummy_token(true), 
     'type' => 'skill', 
     'ctype' => 'token', 
     'term' => $_POST['query'], 
    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  $result = json_decode($response, true);

  $res=[];
  foreach ($result as $key => $value) {
    //if(!str_contains(',', $value['label'] )){
    if (strpos($value['label'], ',') == false) {
      $newarr = array(
        'label' => $value['label'],
        'value' => $value['value'],
        'id' => $value['id'],
      );
      array_push($res,$newarr);
    }
    //print_r($res);die();
  }
    
  header('Content-Type: application/json; charset=utf-8');
  //print_r($res);die();
  //print_r($response);die();
  echo json_encode($res);
  taoh_exit();
}

//Save new skills on // db
function taoh_add_skills() {
  //https://api.tao.ai/users.add.title?mod=hires&token=hT93oaWC&title=NoWorkerLeftBehind&type=company
  $mod = @$_POST['mod'];
  $skill =  @$_POST['skill'];
  $postdata = array(
		'mod' => $mod,
	  'token' => taoh_get_dummy_token(true),
	  'title' => $skill,
		'type'=>'skill',
		'cache' => array("remove"=>array('skillchat_*')),
		//'debug'=>1,
	 );
	$cmd = "users.add.title";
  //echo taoh_apicall_post_debug($cmd,  $postdata);die();
  $data = taoh_apicall_post( $cmd,  $postdata);
  
  echo $data;
  die();
}

//Save new company on // db
function taoh_add_company() {
  //https://api.tao.ai/users.add.title?mod=hires&token=hT93oaWC&title=NoWorkerLeftBehind&type=company
  $mod = @$_POST['mod'];
  $company =  @$_POST['company'];
  $postdata = array(
		'mod' => $mod,
	  'token' => taoh_get_dummy_token(true),
	  'title' => $company,
		'type'=>'company',
		'cache' => array("remove"=>array('orgchat_*')),
		//'debug'=>1,
	 );
	$cmd = "users.add.title";
  //echo taoh_apicall_post_debug($cmd,  $postdata);die();
  $data = taoh_apicall_post( $cmd,  $postdata);

  echo $data;
  die();
}

//Save new role on // db
function taoh_add_role() {
  //https://api.tao.ai/users.add.title?mod=hires&token=hT93oaWC&title=NoWorkerLeftBehind&type=role
  $mod = @$_POST['mod'];
  $role =  @$_POST['role'];
  $postdata = array(
		'mod' => $mod,
	  'token' => taoh_get_dummy_token(true),
	  'title' => $role,
		'type'=>'title',
		'cache' => array("remove"=>array('rolechat_*')),
		//'debug'=>1,
	 );
	$cmd = "users.add.title";
  //echo taoh_apicall_post_debug($cmd,  $postdata);die();
  $data = taoh_apicall_post( $cmd,  $postdata);

  echo $data;
  die();
}

function taoh_delete_account() {
  //https://ppapi.tao.ai/infofetch.user.delete?mod=hires&token=f3vpzoKr
  //$api = TAOH_SITE_USER_DELETE."?mod=hires&token=".TAOH_API_TOKEN;
  //$response = taoh_url_get_content($api);
  $taoh_call = 'infofetch.user.delete';
  $taoh_call_type = 'get';
  $taoh_vals = array(
    // 'mod' => 'hires',  //kalpana deleted this in order to remove all 'hires' text
     'token'=>TAOH_API_TOKEN, 
  );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_get_qoute() {
  //https://obviousbaba.com/api/worklessons.php
  $api = TAOH_OBVIOUS_API;
  $response = taoh_url_get_content($api);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}


function contains($str, array $arr){
    foreach($arr as $a) {
        if (stripos($str,$a) !== false) return true;
    }
    return false;
}

function taoh_ntw_post_message()
{  

    $taoh_vals = array(
        "ops" => 'dm_message',
        'action' => 'send_dm_message',
        'key' => $_POST['key'],
        'roomSlug' => $_POST['roomslug'],
        'code' => TAOH_OPS_CODE,
        'token' => taoh_get_api_token(1),
        'chatwith' => $_POST['chatwith'],
        'channel_type' => TAOH_CHANNEL_DM,
        'keyword' => 'dm',
        'message' => $_POST['message'],
        //'debug' => 1,
    );

    $result = taoh_post(TAOH_CHAT_NET_URL, $taoh_vals);
    $return = json_decode($result, true);
    $return['sent_time'] = $_POST['sent_time'];

    echo json_encode($return);
}

function taoh_post_message()
{
    $taoh_vals = array(
        "ops" => 'message',
        'token' => taoh_get_api_token(),
        "message" => nl2br($_POST['message']),
        "guestptoken" => $_POST["ptoken"],
    );
    if (isset($_POST["location_path"]) && !empty($_POST["location_path"])) {
        $taoh_vals['respond_pre_link'] = TAOH_SITE_URL_ROOT;
        $taoh_vals['respond_post_link'] = $_POST["location_path"];
    }

    $taoh_call = 'notify.put';

    echo taoh_apicall_post($taoh_call, $taoh_vals);
}

function get_tips() {
  //https://api.tao.ai/core.tips.get?mod=tips&&token=".$var_arr[ 'token' ]."&search=&cat=".$var_arr[ 'cat' ]
  //$api = TAOH_SITE_TIPS."?cacheo=0&ops=".$_POST['ops']."&mod=tips&token=". taoh_get_dummy_token()."&search=".$_POST['search']."&cat=".$_POST['cat'];
  //$response = (taoh_url_get_content($api));
  $taoh_call = 'core.tips.get';
  $taoh_call_type = 'get';
    $taoh_vals = array(
        'cacheo' => '0',
        'ops' => $_POST['ops'],
        'mod' => 'tips',
        'token' => taoh_get_dummy_token(),
        'search' => $_POST['search'],
        'cat' => $_POST['cat'],
        //'cfcc1d' => 1, //cfcache newly added
        'cache' => array("name" => taoh_p2us($taoh_call) . '_' . TAOH_ROOT_PATH_HASH . '_' . $_POST['cat'] . '_' . '_tips'),
    );
  //echo taoh_apicall_get_debug($taoh_call,  $taoh_vals); die();
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function delete_tips() {
  //https://api.tao.ai/core.tips.get?mod=tips&token=y2Ds3ugv&conttoken=koo3xfycmtoz&ops=delete
  //$api = TAOH_SITE_TIPS."?cacheo=0&ops=delete&mod=tips&token=". taoh_get_api_token()."&conttoken=".$_POST['conttoken'];
  //$response = (taoh_url_get_content($api));
    $taoh_call = 'core.tips.get';
    $taoh_call_type = 'get';
    $taoh_vals = array(
        'ops' => 'delete',
        'mod' => 'tips',
        'token' => taoh_get_dummy_token(),
        'conttoken' => $_POST['conttoken'],

    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );

  echo $response;
  taoh_exit();
}

function upvote_tips() {
  //https://api.tao.ai/core.content.vote?mod=tips&token=y2Ds3ugv&conttoken=yqwyrm6w8cog&ops=vote&vote=1
  //$api = TAOH_SITE_VOTE."?cacheo=0&ops=vote&vote=1&mod=tips&token=". taoh_get_api_token()."&conttoken=".$_POST['conttoken'];
  //$response = (taoh_url_get_content($api));
  $taoh_call = 'core.content.vote';
  $taoh_call_type = 'get';
    $taoh_vals = array(
        'ops' => 'vote',
        'vote' => '1',
        'mod' => (isset($_POST['mod']) && $_POST['mod']) ? $_POST['mod'] : TAOH_WERTUAL_SLUG,
        'token' => taoh_get_dummy_token(),
        'conttoken' => $_POST['conttoken'],
    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_get_timezones() {
  $list = timezone_identifiers_list();
  $array =  array("success"=> true, "response"=>$list);
  $response = json_encode($array);
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function taoh_get_blog_categories()
{
    header('Content-Type: application/json; charset=utf-8');
    $api = TAOH_CDN_PREFIX . '/assets/category.php';
    $content = file_get_contents($api);
    if (!empty($content)) {
        $response = json_decode($content);
        $res_keys = array_keys($response);
        $last_key = end($res_keys);
        $response[$last_key] = array(
            "title" => "Uncategorized", "category" => "Uncategorized", "slug" => "uncategorized", "bucket" => "jobs", "color" => "#336699", "text" => "#002040", "bucketcolor" => "#434343",
        );
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }
    taoh_exit();
}

function taoh_chat_baba() {
  //https://preapi.tao.ai/asqs.get?mod=asks&token=gEzNvHsB&ops=askob&q=what+is+your+name
  //$api = TAOH_API_PREFIX."/asqs.get?cacheo=0&mod=asks&token=". taoh_get_api_token()."&ops=askob&q=".$_POST['msg'];
  //$response = taoh_url_get_content($api);
  $taoh_call = 'asqs.get';
  $taoh_call_type = 'get';
    $taoh_vals = array(
        'mod' => 'asks',
        'token' => taoh_get_dummy_token(),
        'ops' => 'askob',
        'q' => $_POST['msg'],

    );
  $response = taoh_apicall_get( $taoh_call, $taoh_vals );
  header('Content-Type: application/json; charset=utf-8');
  echo $response;
  taoh_exit();
}

function chat_msg_get() {
  //https://preapi.tao.ai/asqs.get?mod=asks&token=gEzNvHsB&ops=askob&q=what+is+your+name

  if ( empty( $_POST[ 'ask' ] ) ){
      $messages = array(
          array(
              'message' => 'Friend, Welcome! Life is full of surprises, and as a Life Guru, <br />I am here to help you navigate them all. Remember, <br />like knowledge, the more you share, the more you will gain!',
              'sender' => 'obviousbaba',
          ),
          array(
            'message' => 'Ask away, and I will try to shed some light wherever I see <br />darkness.',
            'sender' => 'obviousbaba',
          ),
      );
  } else {
      //$messages = json_decode($_POST[ 'messages' ], true);
      $ask = $_POST[ 'ask' ];
      $messages[] = array(
          'message' => $ask,
          'sender' => 'user',
      );
      $taoh_call_type = 'get';
      $taoh_call = 'asqs.get';
      $taoh_vals = array(
          'mod' => 'asks',
          'token' => taoh_get_dummy_token(),
          'ops' => 'askob',
          'cacheo' => 0,
          'q' => urlencode($ask),
      );
      //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die();
      $output = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );
      if ( $output[ 'success' ]  ){
          $messages[] = array(
              'message' => $output[ 'output' ],
              'sender' => 'obviousbaba',
          );
      }
      unset($_POST[ 'ask' ]);
  }
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($messages);
  taoh_exit();
}

function chat_coach_get(){
  // https://preapi.tao.ai/asqs.get?mod=asks&token=XpxK1I9c&ops=askcc&q=what+is+the+meaning+of+life
  // https://preapi.tao.ai/asqs.get?mod=asks&token=XpxK1I9c&ops=askcc&q=what+is+the+meaning+of+life
  if ( empty( $_POST[ 'ask' ] ) ){
    $messages = array(
        array(
            'message' => 'Friend, I could help answer your career related question.  <br />To get the best possible answers, please be as descriptive  <br />and detailed as possible in your questions.',
            'sender' => '#JusAskTheCoach',
        ),
        array(
          'message' => 'Ask me anything that is stopping you  <br />from achieving your career goals.  <br />I will try to help you out. Ask away!',
          'sender' => '#JusAskTheCoach',
      ),
  );
} else {

      $ask = $_POST[ 'ask' ];
      $messages[] = array(
          'message' => $ask,
          'sender' => 'user',
      );
      $taoh_call_type = 'get';
      $taoh_call = 'asqs.get';
      $taoh_vals = array(
          'mod' => 'asks',
          'token' => taoh_get_dummy_token(),
          'ops' => 'askcc',
          'cacheo' => 0,
          'q' => urlencode($ask),
      );
      //echo taoh_apicall_get_debug( $taoh_call,  $taoh_vals );die();
      $output = json_decode( taoh_apicall_get( $taoh_call,  $taoh_vals ), true );
      if ( $output[ 'success' ]  ){
          $messages[] = array(
              'message' => $output[ 'output' ],
              'sender' => '#JusAskTheCoach',
          );
      }
      unset($_POST[ 'ask' ]);
}
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($messages);
  taoh_exit();
}

function taoh_all_chatbot_get(){
  if ( isset( $_POST[ 'ask' ] ) ){
    //$messages = json_decode($_POST[ 'messages' ], true);
    $ask = $_POST[ 'ask' ];
    $messages[] = array(
      'message' => $ask,
      'bot' => 'user',
    );

    $taoh_call_type = 'post';
   /*  $taoh_call = 'asqs.post';
    $taoh_vals = array(
        'mod' => 'asks',
        'token'=>taoh_get_dummy_token(),
        'ops' => $_POST['ops'],
        'message' => urlencode($ask),
        'bot' => $_POST['bot'],
        'bot_description' => $_POST['bot_desc'],
        'send_to_support' => $_POST['send_to_support'],
        
        'secret' => TAOH_API_SECRET,
        'cache_required'=>0,
        'current_page'=> $_POST['current_page'],
        'timedtamp'=> time(),
       // 'debug' => 1,
    ); */

    $taoh_call = 'asqs.chatbot.message';
    $taoh_vals = array(
       "token"=>taoh_get_dummy_token(),
       "site_secret" => TAOH_API_SECRET,
       "botname"=> $_POST['ops'],
       "botlocn"=> $_POST['current_page'],
       "botmessage" => urlencode($ask),
       'cache_required'=>0,
       'timedtamp'=> time()
    );

    // echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );die();
    $output = json_decode( taoh_apicall_post( $taoh_call, $taoh_vals ),true);

    //
    // echo'<pre>------------';print_r($output);
    //open_ticket_flag


    if ($output[ 'success' ]){
        $messages[] = $output;
    }
    unset($_POST[ 'ask' ]);
  }
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($messages);
  taoh_exit();
}

function get_readable_time($message_time){
  //return taoh_readable_stamp( $message_time );
  $messages = array('message_time'=>$message_time);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($messages);
  taoh_exit();

}


function taoh_get_notification_counter(){

  //echo"==========".$_COOKIE["taoh_last_notification_seen"] ;

  if($_POST['call_at'] == 0){ //call from header bell in order to make counter 0
    setcookie(TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen",time(), strtotime( '+2 days' ),'/');
    $_COOKIE[TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen"] = time();
    $response = array();
    $response['status'] = 0;
    header('Content-Type: application/json; charset=utf-8');
    $data = json_encode($response);
    echo $data;
    taoh_exit();

  }
  else{
    $value = 0;
    (!isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen"]))?(setcookie(TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen", $value)):($_COOKIE[TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen"]);
    $taoh_call = 'core.content.get';
    $taoh_call_type = 'get';
    $taoh_vals = array(
      'mod' => ( isset($_POST['mod']) && $_POST['mod'] ) ? $_POST['mod']:TAOH_WERTUAL_SLUG, 
      'ops' => $_POST['ops'],
      'token' => $_POST['token'],
      'type' => $_POST['type']
    
    );
    if(isset($_POST['ptoken']) && $_POST['ptoken']!=''){
      $taoh_vals['ptoken'] = $_POST['ptoken'];
    }
    if($_COOKIE["taoh_last_notification_seen"] != 0){
      $taoh_vals['timestamp'] = $_COOKIE["taoh_last_notification_seen"];
  }

   //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die();
    $result = taoh_apicall_get( $taoh_call, $taoh_vals );
    $response_decode = json_decode($result);
    
    setcookie(TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen",time(), strtotime( '+2 days' ), '/');
    $_COOKIE[TAOH_ROOT_PATH_HASH.'_'."taoh_last_notification_seen"] = time();

    $response = array();
    if(isset($response_decode->success)){
      $response['status'] = 1;
      
      $response['total_num'] = $response_decode->total;
    }
    else{
      $response['status'] = 1;
      
      $response['total_num'] = 0;
      
    }
  }
  //print_r($result);die();

    header('Content-Type: application/json; charset=utf-8');
    $data = json_encode($response);
    echo $data;
    taoh_exit();
}

function taoh_get_notification(){
  //https://preapi.tao.ai/core.content.get?mod=core&ops=get&type=notify&token=y2Ds3ugv
  //https://papi .tao.ai/core.content.get?mod=core&ops=get&token=XQ7iOGl4&type=notify&ptoken=6ofz578vo9be&perpage=10&offset=0
  $value = 0;
  //if($_POST['call_from'] == 1)
  //$_COOKIE["LASTPULLEDTIMESTAMP"] = 0;

  //(!isset($_COOKIE["LASTPULLEDTIMESTAMP"]))?(setcookie("LASTPULLEDTIMESTAMP", $value)):($_COOKIE["LASTPULLEDTIMESTAMP"]);
  
  /*$taoh_call = 'core.content.get';*/
  $taoh_call = 'notify.get';
  $taoh_call_type = 'get';
  $taoh_vals = array(
    'mod' => ( isset($_POST['mod']) && $_POST['mod'] ) ? $_POST['mod']:TAOH_WERTUAL_SLUG, 
    'ops' => $_POST['ops'],
    'token' => $_POST['token'],
    /*'type' => $_POST['type']
     'perpage'=>10,
    'offset'=>0 */  
  );
  if(isset($_POST['ptoken']) && $_POST['ptoken']!=''){
    $taoh_vals['ptoken'] = $_POST['ptoken'];
  }
  if(isset($_POST['title']) && $_POST['title']!=''){
    //$taoh_vals['title'] = $_POST['title'];
  }
  if(isset($_POST['message']) && $_POST['message']!=''){
    //$taoh_vals['message'] = $_POST['message'];
  }//echo $_COOKIE["LASTPULLEDTIMESTAMP"];
  if(isset($_COOKIE["LASTPULLEDTIMESTAMP"] )){
    if($_POST['call_from'] != 1)
        $taoh_vals['timestamp'] = $_COOKIE["LASTPULLEDTIMESTAMP"];
  }

  if($_POST['call_from'] == 1){
    $taoh_vals['perpage'] = 10;
    $taoh_vals['offset'] = 0;
  }
  //https://papi s.tao.ai/notify.get?mod=notify&ops=webnotify&token=XQ7iOGl4&ptoken=6ofz578vo9be&perpage=10&offset=0
  //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals);
  //taoh_apicall_get_debug_log($taoh_call,  $taoh_vals);
  
 // echo taoh_apicall_get_debug( $taoh_call, $taoh_vals ); 
  $result = taoh_apicall_get( $taoh_call, $taoh_vals );
  //print_r($result);
  $response_decode = json_decode($result);
  
  $response = array();
  if($response_decode->success){
    
    $response['status'] = 1;
    foreach($response_decode->output as $key=>$val){
       $time = taoh_readable_stamp_short($val->timestamp);
       $val->timestamp =  $time ;

    }
    setcookie(TAOH_ROOT_PATH_HASH.'_'."LASTPULLEDTIMESTAMP",time());
    $_COOKIE[TAOH_ROOT_PATH_HASH.'_'."LASTPULLEDTIMESTAMP"] = time();

    //taoh_readable_stamp_short

    $response['output'] = $response_decode->output;
    $response['total_num'] = $response_decode->total;
      
  }
  else{
    $response['status'] = 0;
    $response['output'] = [];
    $response['total_num'] = 0;
    
  }
  //print_r($result);die();

    header('Content-Type: application/json; charset=utf-8');
    $data = json_encode($response);
    echo $data;
    taoh_exit();
 
}

function taoh_get_notification_list(){
  //https://preapi.tao.ai/core.content.get?mod=core&ops=get&type=notify&token=y2Ds3ugv
  //$offset = ($_POST['offset'] == 0)?($_POST['offset']):(($_POST['offset']-1)*10);
  $offset_default = 0;
  $limit_default = 9;
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? ($_POST['offset'] * $limit_default):($offset_default * $limit_default);
  
  /*$taoh_call = 'core.content.get';*/
  $taoh_call = 'notify.get';
  $taoh_call_type = 'get';
  $taoh_vals = array(
    'mod' => ( isset($_POST['mod']) && $_POST['mod'] ) ? $_POST['mod']:TAOH_WERTUAL_SLUG, 
    'ops' => $_POST['ops'], 
    //'token' => 'L2Fix175',//taoh_get_dummy_token(1),
    'token' => $_POST['token'],
    'type' => $_POST['type'],
    'perpage'=>$_POST['perpage'],
    'limit'=>$limit,
    'offset'=>$offset,
    //'cfcc15m' =>1  ////cfcache newly added
  );
  if(isset($_POST['ptoken']) && $_POST['ptoken']!=''){
    $taoh_vals['ptoken'] = $_POST['ptoken'];
  }
  if(isset($_POST['title']) && $_POST['title']!=''){
    //$taoh_vals['title'] = $_POST['title'];
  }
  if(isset($_POST['message']) && $_POST['message']!=''){
    //$taoh_vals['message'] = $_POST['message'];
  }
  //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals);
  //echo taoh_apicall_get_debug($taoh_call,  $taoh_vals);die();
  $result = taoh_apicall_get( $taoh_call, $taoh_vals );
  $response_decode = json_decode($result);
  $response = array();
  //echo'<pre>';print_r($result);echo'<pre>';
  //echo"==========".$response_decode->success;
  if($response_decode->success){
    
    $response['status'] = 1;
    foreach($response_decode->output as $key=>$val){
       $time = taoh_readable_stamp_short($val->timestamp);
       $val->timestamp =  $time ;

    }

    //taoh_readable_stamp_short

    $response['output'] = $response_decode->output;
    $response['total_num'] = $response_decode->total;
      
  }
  else{
    $response['status'] = 0;
    $response['output'] = [];
    $response['total_num'] = 0;;
    
  }
  //print_r($result);die();

    header('Content-Type: application/json; charset=utf-8');
    $data = json_encode($response);
    echo $data;
    taoh_exit();
 
}

function taoh_post_notification(){
  //https://preapi.tao.ai/core.content.get?mod=core&ops=get&type=notify&token=y2Ds3ugv

  $taoh_call = 'core.content.post';
  $taoh_call_type = 'post';
  $taoh_vals = array(
    'mod' => 'core', 
    'ops' => 'post', 
    'token' => taoh_get_dummy_token(1),
    'type' => $_POST['type'],
    
  );
  if(isset($_POST['ptoken']) && $_POST['ptoken']!=''){
    $taoh_vals['ptoken'] = $_POST['ptoken'];
  }
  if(isset($_POST['payload']) && $_POST['payload']!=''){
    $taoh_vals['payload'] = $_POST['payload'];
  }
  if(isset($_POST['channel']) && $_POST['channel']!=''){
    $taoh_vals['channel'] =  url_encode($_POST['channel']);
  }
  if(isset($_POST['title']) && $_POST['title']!=''){
    $taoh_vals['title'] =  url_encode($_POST['title']);
  }
  if(isset($_POST['message']) && $_POST['message']!=''){
    $taoh_vals['message'] =  url_encode($_POST['message']);
  }
/*
  channel = {CHANNEL} // // [e]mail, [s]ms, [p]ush, [s]lack, qwe[c]hat, [li]ne, [t]elegram,
                   [f]acebook, [t]witter, [w]hatsapp, we[b]site, [a]ll  || default to [b]
  type = {TYPE} // // [i]nfo, [w]arning, [e]rror, [s]uccess, [a]lert, [n]otification,
           [m]essage, [r]eminder, [u]pdate, new[s], e[v]ent, [p]romotion, mar[k]etin || default 'i'
  status = {STATUS} // 2: [n]ew, 1: [r]ead, 0:[d]eleted || default = 2
*/

  //echo TAOH_API_PREFIX . '/' .$taoh_call.'?'.http_build_query($taoh_vals); taoh_exit();
  echo taoh_apicall_post_debug($taoh_call,  $taoh_vals);
  $result = taoh_apicall_post( $taoh_call, $taoh_vals );
  $response_decode = json_decode($result);
  $response = array();
  

    header('Content-Type: application/json; charset=utf-8');
    $data = json_encode($result);
    echo $data;
    taoh_exit();
 
}

function taoh_central_get(){
  $offset_default = 0;
  $limit_default = 10;

  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'list';
  $search = ( isset( $_POST['search'] ) && $_POST['search'] )? $_POST['search']:'';
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? $_POST['offset']:$offset_default;

  $taoh_call = 'core.content.get';
  $taoh_vals = array(
    "mod" => 'core',
    "conttype"=> "blog", 
    "type"=> "reads", 
    "ops"=> $ops,
    'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
    'token'=>taoh_get_dummy_token(1),
    'local'=>TAOH_READS_GET_LOCAL,
    "q"=> $search,
    "category"=> '',
    "page"=> $offset,
    "perpage" => $limit,
    "sort" => 'rand',
    'cache_time'=>600,
    //'debug'=>1,
    //'cfcc5h' => 1 //cfcache newly added
  );
 

  $cache_name = 'reads_blog_reads_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  $taoh_vals[ 'cache_name' ] = $cache_name;
  //$taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
  $req = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  echo $req;
  die();
}

function taoh_central_newsletter_get(){
  $offset_default = 0;
  $limit_default = 10;

  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'list';
  $search = ( isset( $_POST['search'] ) && $_POST['search'] )? $_POST['search']:'';
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? $_POST['offset']:$offset_default;

  $taoh_call = 'core.content.get';
  $taoh_vals = array(
    "mod" => 'core',
    "conttype"=> "blog", 
    "type"=> "newsletter", 
    "ops"=> $ops,
    'key' => defined('TAOH_EVENTS_GET_LOCAL') && TAOH_EVENTS_GET_LOCAL ? TAOH_API_SECRET : TAOH_API_DUMMY_SECRET,
    'token'=>taoh_get_dummy_token(1),
    'local'=>TAOH_READS_GET_LOCAL,
    "q"=> $search,
    "category"=> '',
    "page"=> $offset,
    "perpage" => $limit,
    "sort" => 'rand',
    'cache_time'=>600,
  );

  $cache_name = $taoh_call.'_blog_reads_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  //$taoh_vals[ 'cache_name' ] = $cache_name;
  //$taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  // echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
  $req = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  echo $req;
  die();
}

function taoh_central_tag_get(){
  $offset_default = 0;
  $limit_default = 10;

  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'list';
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? $_POST['offset']:$offset_default;
  $category = ( isset( $_POST['category'] ) && $_POST['category'] )? $_POST['category']:'uncategorized';
  $search = ( isset( $_POST['search'] ) && $_POST['search'] )? $_POST['search']:'';
  $tags = (isset($_POST['tags']) && $_POST['tags'] != '')? $_POST['tags']:'';

  $taoh_call = 'core.content.get';
  $taoh_vals = array(
    "mod" => 'core',
    "type"=> "reads", 
    "ops"=> $ops,
    'token'=>TAOH_API_TOKEN_DUMMY,
    'local'=>TAOH_READS_GET_LOCAL,
    "category"=> $category,
    "page"=> $offset,
    "perpage" => $limit,
    "sort" => 'rand',
    'sub_secret'=>TAOH_LP_SITE_URL_FULL_SECRET,
    'cache_time'=>600,
    'cache_name'=> 'tags_recent_'.TAOH_LP_SITE_URL_FULL_SECRET.'_'.hash('crc32',$search.$offset.$limit.$category),
    //'cfcc5h' => 1 //cfcache newly added
  );
  if(isset($tags) && $tags != ''){
    $taoh_vals['tags'] = $tags;
    $taoh_vals['cache_name'] = 'tags_search_'.TAOH_LP_SITE_URL_FULL_SECRET.'_'.hash('crc32',$search.$tags.$offset.$limit.$category);
  }
  if(isset($search) && $search != ''){
    $taoh_vals['q'] = $search;
    $taoh_vals['cache_name'] = 'tags_search_'.TAOH_LP_SITE_URL_FULL_SECRET.'_'.hash('crc32',$search.$tags.$offset.$limit.$category);
  }
  //$taoh_vals[ 'cfcache' ] = hash('sha256', $taoh_call . serialize($taoh_vals));
  //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
  $req = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  echo $req;
  die();
}

function get_support() {
  $offset_default = 0;
  $limit_default = 10;
  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'get';  
  $app = ( isset( $_POST['app'] ) && $_POST['app'] )? $_POST['app']:'';  
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? ($_POST['offset'] * $limit_default):($offset_default * $limit_default);
  $url = ( isset( $_POST['url'] ) && $_POST['url'] )? $_POST['url']:'';  
  $contoken = ( isset( $_POST['contoken'] ) && $_POST['contoken'] )? $_POST['contoken']:'';  
  if ( stristr( $contoken, '?' ) ){
    list( $contoken, $temp ) = explode('?', $contoken);
  }
  if ( stristr( $contoken, ' ' ) ){
    $pieces = explode(' ', $contoken);
    $contoken = array_pop($pieces);
  }
  if ( stristr( $contoken, '-' ) ){
    $pieces = explode('-', $contoken);
    $contoken = array_pop($pieces);
  }

    $taoh_call = "core.get";
    $taoh_vals = array(
        'mod' => 'tao',
        'token' => taoh_get_dummy_token(),
        'ops' => $ops,
        'q' => $contoken,
        'app' => $app,
        'type' => 'support',
        'limit' => $limit,
        'offset' => $offset,
        //'cfcc1h' => 1 //cfcache newly added
    );
  //print_r($taoh_vals);die;
  
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );
  //echo TAOH_API_PREFIX."/$taoh_call?".http_build_query($taoh_vals);
  $data = taoh_apicall_get( $taoh_call, $taoh_vals );
  echo $data;
  die();
}

function taoh_update_status(){
    $process = $_POST['process'] ;
    $ptoken = $_POST['ptoken'] ;
    if($process == 1){
      $taoh_vals = array(
        "ops" => 'lpushu',
        'key'=> $ptoken.'_live_status',
        'value'=> $_POST['my_status'],
        'url' => TAOH_CACHE_CHAT_PREFIX,
        'debug'=>1
      );
    }
    else{
      $taoh_vals = array(
        "ops" => 'delete',
        'key'=> $ptoken.'_live_status',
        'url' => TAOH_CACHE_CHAT_PREFIX,
        'debug'=>1
      );
    }
		taoh_remote_cache( $taoh_vals );

}

function taoh_metrics_get(){
  $app = $_POST['app'];
  $conttoken = $_POST['conttoken'];
  
  $vars = array(
    'ops' => 'metricsget',
    'code' => TAOH_OPS_CODE,
    'key' => array(
        'app' => $app,
        'conttoken' => array(
          $conttoken
        ),
    ),
  );
  //print_r($vars);die;
  //echo taoh_apicall_get_debug( TAOH_CACHEOPS_PREFIX, $vars );die;
  $data = taoh_post( TAOH_CACHEOPS_PREFIX, $vars );
  echo $data;die;
}

function toah_metrics_push()
{
    /*$contoken = isset($_POST['conttoken']) ? $_POST['conttoken'] : '';
    //$token = isset($_POST['ptoken'])? $_POST['ptoken'] : '';
    $token = (taoh_user_is_logged_in()) ? TAOH_API_TOKEN : TAOH_API_TOKEN_DUMMY;
    $values = json_encode(array($contoken, $_POST['met_type'], $token, $_POST['met_action'], time(), TAOH_API_SECRET));*/
    //echo '<pre>';print_r($_POST['metrics_data']);die;
    $data = taoh_cacheops('metricspush', json_encode($_POST['metrics_data']));
    echo $data;
    die();
}

function articles_get() {
  $ops = ( isset( $_POST['ops'] ) && $_POST['ops'] )? $_POST['ops']:'front';
  $type = ( isset( $_POST['type'] ) && $_POST['type'] )? $_POST['type']:'reads';
  $category = ( isset( $_POST['category'] ) && $_POST['category'] )? $_POST['category']:'uncategorized';
  $taoh_call = "core.content.get";
  $taoh_vals = array(
    'mod'=>'core',
    'type'=>$type,
    'token'=>TAOH_API_TOKEN_DUMMY,
    'ops'=>$ops,
    'category'=>$category,
    'cache_time'=>600,
    //'cfcc5h' => 1 //cfcache newly added
  );
  if($ops == 'list'){
    $taoh_vals['tag'] = 1;
    $taoh_vals['sub_secret'] = TAOH_LP_SITE_URL_FULL_SECRET;
    //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals);die;
  }
  $cache_name = $taoh_call.'_' . $ops .'_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  //$taoh_vals[ 'cache_name' ] = $cache_name;
  //$taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  
  //echo TAOH_API_PREFIX."/$taoh_call?".http_build_query($taoh_vals);die();
  $taoh_call_type = "get";
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals);die;
  $data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  if($ops == 'list'){
    $data = json_decode($data,true);
    $data = $data['output']['list'];
    $newArray = array_map(fn($key) => $data[$key], array_rand($data, 4)); // get first 4 items
    $newArraysend['success'] = true; 
    $newArraysend['output'] = $newArray; 
    $data = json_encode($newArraysend);
  }
  echo $data;
  die();
}

function activity_get(){
  $offset_default = 0;
  $limit_default = 10;
  $limit = ( isset( $_POST['limit'] ) && $_POST['limit'] )? $_POST['limit']:$limit_default;
  $offset =  ( isset( $_POST['offset'] ) && $_POST['offset'] )? $_POST['offset']:$offset_default;
  $taoh_call = "core.content.get";
    $type = 'activity';
    $taoh_vals = array(
        'mod' => 'core',
        'type' => $type,
        'token' => taoh_get_dummy_token(1),
        'limit' => $limit,
        'page' => $offset,
        //'cfcc5h' => 1 //cfcache newly added

    );
    $cache_name = $taoh_call.'_' . $type . '_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //  $taoh_vals[ 'cfcache' ] = $cache_name;
    ////$taoh_vals[ 'cache_name' ] = $cache_name;
   // $taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
    ksort($taoh_vals);
        
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  $data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  echo $data;
  die();
}

function add_edit_employee()
{
//  $years = range(1900, strftime("%Y", time()));
//  $currently_selected = date('Y');
    // Year to start available options at
    $earliest_year = 1900;
    // Set your latest year you want in the range, in this case we use PHP to just set it to the current year.
    $latest_year = date('Y');
    if (isset($_POST['add_or_edit']) && $_POST['add_or_edit'] == "Edit") {
        $decode_employee = json_decode($_POST['post_data'], true);
        $post_id = $_POST['id'];
        $emp_edit_del_id = $_POST['emp_edit_del_id'];
        $values = $decode_employee[$post_id] ?? [];
    } else {
        $post_id = $_POST['id'];
        $emp_edit_del_id = '';
        $values = array();
    }

    $checked = (isset($values['current_role']) && ($values['current_role'] == "on")) ? 'checked' : '';
    $industry_arr = array(
        "agri" => "Agriculture & Forestry",
        "arts" => "Arts & Entertainment",
        "auto" => "Automotive",
        "aero" => "Aviation & Aerospace",
        "bank" => "Banking & Finance",
        "bio" => "Biotechnology",
        "che" => "Chemicals",
        "cons" => "Construction",
        "good" => "Consumer Goods & Services",
        "space" => "Defense & Space",
        "edu" => "Education",
        "ene" => "Energy & Utilities",
        "engi" => "Engineering",
        "envi" => "Environmental Services",
        "fash" => "Fashion & Apparel",
        "food" => "Food & Beverages",
        "govt" => "Government & Public Sector",
        "heal" => "Healthcare & Pharmaceuticals",
        "tour" => "Hospitality & Tourism",
        "tech" => "Information Technology",
        "ins" => "Insurance",
        "legal" => "Legal Services",
        "manu" => "Manufacturing",
        "mari" => "Maritime",
        "mark" => "Marketing & Advertising",
        "media" => "Media & Communications",
        "mini" => "Mining & Metals",
        "non" => "Non-Profit & NGO",
        "prof" => "Professional Services",
        "real" => "Real Estate",
        "ret" => "Retail",
        "sports" => "Sports & Recreation",
        "tele" => "Telecommunications",
        "logi" => "Transport & Logistics",
        "other" => "Others (for industries not listed above)"
    );
    $roletypes = array(
        "remo" => "Remote Work",
        "full" => "Full Time",
        "part" => "Part Time",
        "temp" => "Temporary",
        "free" => "Freelance",
        "cont" => "Contract",
        "pdin" => "Paid Internship",
        "unin" => "Unpaid Internship",
        "voln" => "Volunteer",
    );
    $roletype_arr = array(
        "ons" => "Onsite",
        "rem" => "Remote",
        "hyb" => "Hybrid",
    );

    $data = '<div class="row mt-3">';
    if (isset($post_id) && $post_id != "") {
        $data .= '<input type="hidden" class="emp_add" value="' . $post_id . '" name="emp_add"/>';
        $post_id = $post_id;
    }
    if (isset($emp_edit_del_id) && $emp_edit_del_id != "") {
        $data .= '<input type="hidden" class="emp_edit" value="' . $emp_edit_del_id . '" name="emp_edit"/>';
        $data .= '<input type="hidden" class="emp_delete" value="' . $emp_edit_del_id . '" name="emp_delete"/>';
        $post_id = $emp_edit_del_id;
    }
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label  class="fs-13 text-black lh-20 fw-medium">Current or Last Job Role <span style="color:red"> * </span></label>';
    $data .= '<select id="emp_roleSelect_' . $post_id . '"  name="emp_title_' . $post_id . '[]" placeholder="Type to select" required>';
    if (!empty($values['title'])) {
        foreach ($values['title'] as $key => $value) {
            if (!is_array($value)) {
                list ($pre, $post) = explode(':>', $value);
            } else {
                foreach ($value as $key => $tvalue) {
                    list ($pre, $post) = explode(':>', $tvalue);
                }
            }
            $data .= "<option value='$key' selected='selected'>$post</option>";
        }
    }
    $data .= '</select><script>emp_roleSelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '<div class="input-box mt-2">';
    $data .= '<div class="custom-control custom-checkbox">';
    $data .= '<input ' . $checked . ' onclick="check_still_working(' . $post_id . ')" id="still_working_' . $post_id . '" type="checkbox" name="is_current_role_check_' . $post_id . '"  class="custom-control-input current_role_checkbox"/>';
    $data .= '<label for="still_working_' . $post_id . '" style="user-select: none; cursor: pointer; pointer-events: all;"  class="custom-control-label fs-13 text-black lh-20 fw-medium ">I am currently working this role</label>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Current or Last Company <span style="color:red"> * </span></label>';
    $data .= '<select id="emp_companySelect_' . $post_id . '" name="emp_company_' . $post_id . '[]" placeholder="Type to select" required>';
    if (!empty($values['company'])) {
        foreach ($values['company'] as $key => $value) {
            if (!is_array($value)) {
                list ($pre, $post) = explode(':>', $value);
            } else {
                foreach ($value as $key => $tvalue) {
                    list ($pre, $post) = explode(':>', $tvalue);
                }
            }
            $data .= "<option value='$key' selected='selected'>$post</option>";
        }
    }
    $data .= '</select><script>emp_companySelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '</div>';

    $data .= '<div class="row">';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Start Date <span style="color:red"> * </span></label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" name="emp_start_month_' . $post_id . '">';
    $data .= '<option value="">Month</option>';
    for ($month = 1; $month <= 12; $month++) {
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $emp_start_month = isset($values['emp_start_month']) && ($values['emp_start_month'] == $month) ? 'selected' : '';
        $data .= '<option ' . $emp_start_month . ' value="' . $month . '">' . $monthName . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">&nbsp;</label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" id="emp_year_starts" name="emp_year_start_' . $post_id . '">';
    $data .= '<option value="">Year</option>';
    foreach (range($latest_year, $earliest_year) as $i) {
        $emp_year_start = isset($values['emp_year_start']) && ($values['emp_year_start'] == $i) ? 'selected' : '';
        $data .= '<option ' . $emp_year_start . ' value="' . $i . '">' . $i . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';

    $disp = ($checked) ? "style='display:none'" : "";
    $end_required = ($checked) ? "" : "required";
    $data .= '<div class="col-lg-3 emp_end_month_block_' . $post_id . '" ' . $disp . ' >';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">End Date <span style="color:red"> * </span></label>';
    $data .= '<div class="form-group">';
    $data .= '<select ' . $end_required . ' class="form-control" name="emp_end_month_' . $post_id . '" id="emp_end_month_' . $post_id . '">';
    $data .= '<option value="">Month</option>';
    for ($month = 1; $month <= 12; $month++) {
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $emp_end_month = isset($values['emp_end_month']) && ($values['emp_end_month'] == $month) ? 'selected' : '';
        $data .= '<option ' . $emp_end_month . ' value="' . $month . '">' . $monthName . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-3 emp_end_month_block_' . $post_id . '" ' . $disp . '>';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">&nbsp;</label>';
    $data .= '<div class="form-group">';
    $data .= '<select ' . $end_required . ' class="form-control" id="emp_year_ends" name="emp_year_end_' . $post_id . '">';
    $data .= '<option value="">Year</option>';
    foreach (range($latest_year, $earliest_year) as $i) {
        $emp_year_end = isset($values['emp_year_end']) && ($values['emp_year_end'] == $i) ? 'selected' : '';
        $data .= '<option ' . $emp_year_end . ' value="' . $i . '">' . $i . '</option>';
    }
    $data .= '</select>';
    $data .= '<div id="emp_hidden_end" style="display: none"></div>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '</div>';

    $data .= '<div class="row">';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Industry <span style="color:red"> * </span></label>';
    $data .= '<select class="form-control emp_industry" name="emp_industry_' . $post_id . '" required>';
    $data .= '<option value="">Choose</option>';
    $emp_industry = isset($values['emp_industry'])
        ? (is_array($values['emp_industry']) ? $values['emp_industry'] : explode(' ', (string)$values['emp_industry']))
        : [];

    foreach ($industry_arr as $key => $value) {
        if (in_array($key, $emp_industry)) {
            $data .= '<option selected value="' . $key . '">' . $value . '</option>';
        } else {
            $data .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label  class="fs-13 text-black lh-20 fw-medium">Location </label>';
    $data .= '<select id="emp_locationSelect_' . $post_id . '" placeholder="Location search" autocomplete="off" class="emp_locationSelect_' . $post_id . '" name="emp_coordinates_' . $post_id . '">';
    if (!empty($values['emp_coordinates']) && !empty($values['emp_full_location'])) {
        $data .= '<option value="' . $values['emp_coordinates'] . '">' . $values['emp_full_location'] . '</option>';
    }
    $data .= '</select>';
    $data .= '<input id="emp_coordinateLocation' . $post_id . '" type="hidden" name="emp_full_location_' . $post_id . '" value="' . ($values['emp_full_location'] ?? '') . '">';
    $data .= '<input id="emp_geohash' . $post_id . '" type="hidden" name="emp_geohash_' . $post_id . '" value="' . ($values['emp_geohash'] ?? '') . '">';
    $data .= '<script>emp_locationSelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label  class="fs-13 text-black lh-20 fw-medium">Employment Type </label>';
    $data .= '<div class="form-group">';
    $data .= '<select id="emp_roleTypeSelect_' . $post_id . '" class="form-control" name="emp_roletype_' . $post_id . '[]" autocomplete="off">';
    $data .= '<option value="">Choose</option>';
    $emp_role_type = isset($values['emp_roletype'])
        ? (is_array($values['emp_roletype']) ? $values['emp_roletype'] : explode(' ', (string)$values['emp_roletype']))
        : [];
    foreach ($roletypes as $key => $value) {
        if (isset($emp_role_type) && in_array($key, $emp_role_type)) {
            $data .= '<option selected value="' . $key . '">' . $value . '</option>';
        } else {
            $data .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label  class="fs-13 text-black lh-20 fw-medium">Location Type </label>';
    $data .= '<select ' . ($required ?? '') . ' class="form-control placeType" name="emp_placeType_' . $post_id . '">';
    $data .= '<option value="">Choose</option>';
    $emp_placetype = isset($values['emp_placeType'])
        ? (is_array($values['emp_placeType']) ? $values['emp_placeType'] : explode(' ', (string)$values['emp_placeType']))
        : [];
    foreach ($roletype_arr as $key => $value) {
        if (!empty($emp_placetype) && in_array($key, $emp_placetype)) {
            $data .= '<option selected value="' . $key . '">' . $value . '</option>';
        } else {
            $data .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-12">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Responsibilities</label>';
    $data .= '<div class="form-group">';
    $data .= '<textarea class="form-control" rows="4" maxlength="500" name="emp_responsibilities_' . $post_id . '">' . taoh_title_desc_decode($values['emp_responsibilities'] ?? '') . ' </textarea>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Profile Headline </label>';
    $data .= '<div class="form-group">';
    $data .= '<input class="form-control" value="' . taoh_title_desc_decode($values['emp_profile_headline'] ?? '') . '" type="text" name="emp_profile_headline_' . $post_id . '">';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium skills"> Skills (Choose from the suggested skills for better results)</label>';
    $data .= '<select id="emp_skillSelect_' . $post_id . '" multiple name="emp_skill_' . $post_id . '[]" placeholder="Type to select">';
    if (!empty($values['skill'])) {
        foreach ($values['skill'] as $key => $value) {
            if (!is_array($value)) {
                list ($pre, $post) = explode(':>', $value);
            } else {
                foreach ($value as $key => $tvalue) {
                    list ($pre, $post) = explode(':>', $tvalue);
                }
            }
            $data .= "<option value='$key' selected='selected'>$post</option>";
        }
    }
    $data .= '</select><script>emp_skillSelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '</div><!-- end row -->';

    $data_btn = '';
    if (isset($_POST['add_or_edit']) && $_POST['add_or_edit'] == "Edit") {
        $data_btn = '<button type="submit" class="btn btn-danger float-left" name="emp_btnDelete"><i class="fa fa-trash mr-1"></i> Delete Experience</button>';
    }

    echo json_encode($data . '~' . $data_btn);
    exit();
}

function add_edit_education()
{

//  $years = range(1900, strftime("%Y", time()));
//  $currently_selected = date('Y');
    // Year to start available options at
    $earliest_year = 1900;
    // Set your latest year you want in the range, in this case we use PHP to just set it to the current year.
    $latest_year = date('Y');

    if (isset($_POST['add_or_edit']) && $_POST['add_or_edit'] == "Edit") {
        $decode_education = json_decode($_POST['post_data'], true);
        $post_id = $_POST['id'];
        $edu_edit_del_id = $_POST['edu_edit_del_id'];
        $values = $decode_education[$post_id] ?? [];
    } else {
        $post_id = $_POST['id'];
        $edu_edit_del_id = '';
        $values = array();
    }

    $degeree_arr = array(
        "highschool" => "High School Diploma or GED",
        "vocational" => "Vocational/Technical Diploma",
        "associate" => "Associate Degree",
        "bachelor" => "Bachelor's Degree",
        "master" => "Master's Degree",
        "doctorate" => "Doctorate or Professional Degree",
        "other" => "Other (for degeree not listed above)"
    );

    $data = '<div class="row mt-3">';
    if (isset($post_id) && $post_id != "") {
        $data .= '<input type="hidden" class="edu_add" value="' . $post_id . '" name="edu_add"/>';
    }
    if (isset($edu_edit_del_id) && $edu_edit_del_id != "") {
        $data .= '<input type="hidden" class="edu_edit" value="' . $edu_edit_del_id . '" name="edu_edit"/>';
        $data .= '<input type="hidden" class="edu_delete" value="' . $edu_edit_del_id . '" name="edu_delete"/>';
        $post_id = $edu_edit_del_id;
    }
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">School Name <span style="color:red"> * </span></label>';
    $data .= '<select id="edu_companySelect_' . $post_id . '" name="edu_name_' . $post_id . '[]" placeholder="Type to select" required>';
    if (!empty($values['company'])) {
        foreach ($values['company'] as $key => $value) {
            if (!is_array($value)) {
                list ($pre, $post) = explode(':>', $value);
            } else {
                foreach ($value as $key => $cvalue) {
                    list ($pre, $post) = explode(':>', $cvalue);
                }
            }
            $data .= "<option value='$key' selected='selected'>$post</option>";
        }
    }
    $data .= '</select><script>edu_companySelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Degree <span style="color:red"> * </span></label>';
    $edu_degree = [];
    if (!empty($values['edu_degree'])) {
        if (is_array($values['edu_degree'])) {
            $edu_degree = $values['edu_degree'];
        } else {
            $edu_degree = explode(" ", $values['edu_degree']);
        }
    }

    $data .= '<select id="edu_degree_' . $post_id . '" required class="form-control edu_degree" name="edu_degree_' . $post_id . '[]">';
    $data .= '<option value="">Choose an option below</option>';
    foreach ($degeree_arr as $key => $value) {
        if (in_array($key, $edu_degree)) {
            $data .= '<option selected value="' . $key . '">' . $value . '</option>';
        } else {
            $data .= '<option value="' . $key . '">' . $value . '</option>';
        }
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Specialization <span style="color:red"> * </span></label>';
    $data .= '<div class="form-group">';
    $data .= '<input required id="edu_specalize_' . $post_id . '" class="form-control" value="' . taoh_title_desc_decode($values['edu_specalize'] ?? '') . '" type="text" name="edu_specalize_' . $post_id . '">';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Grade </label>';
    $data .= '<div class="form-group">';
    $data .= '<input class="form-control" value="' . ($values['edu_grade'] ?? '') . '" type="text" name="edu_grade_' . $post_id . '">';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Start Date <span style="color:red"> * </span></label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" id="edu_start_month" name="edu_start_month_' . $post_id . '">';
    $data .= '<option value="">Month</option>';
    $values_edu_start_month = $values['edu_start_month'] ?? null;
    for ($month = 1; $month <= 12; $month++) {
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $edu_start_month = ($values_edu_start_month == $month) ? 'selected' : '';
        $data .= '<option ' . $edu_start_month . ' value="' . $month . '">' . $monthName . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">&nbsp;</label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" id="edu_year_starts" name="edu_start_year_' . $post_id . '">';
    $data .= '<option value="">Year</option>';
    $values_edu_start_year = $values['edu_start_year'] ?? null;
    foreach (range($latest_year, $earliest_year) as $i) {
        $edu_start_year = ($values_edu_start_year == $i) ? 'selected' : '';
        $data .= '<option ' . $edu_start_year . ' value="' . $i . '">' . $i . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">End Date <span style="color:red"> * </span></label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" id="edu_end_month" name="edu_end_month_' . $post_id . '">';
    $data .= '<option value="">Month</option>';
    $values_edu_end_month = $values['edu_end_month'] ?? null;
    for ($month = 1; $month <= 12; $month++) {
        $monthName = date("F", mktime(0, 0, 0, $month, 1));
        $edu_end_month = ($values_edu_end_month == $month) ? 'selected' : '';
        $data .= '<option ' . $edu_end_month . ' value="' . $month . '">' . $monthName . '</option>';
    }
    $data .= '</select>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-3">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">&nbsp;</label>';
    $data .= '<div class="form-group">';
    $data .= '<select required class="form-control" id="edu_year_ends" name="edu_complete_year_' . $post_id . '">';
    $data .= '<option value="">Year</option>';
    $values_edu_complete_year = $values['edu_complete_year'] ?? null;
    foreach (range($latest_year, $earliest_year) as $i) {
        $data .= "<option value='$i'" . ($i == $values_edu_complete_year ? ' selected' : '') . ">$i</option>";
    }
    $data .= '</select>';
    $data .= '<div id="edu_hidden_end" style="display: none"></div>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-3 -->';
    $data .= '<div class="col-lg-12">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Activities</label>';
    $data .= '<div class="form-group">';
    $data .= '<textarea class="form-control" rows="4" maxlength="500" name="edu_activities_' . $post_id . '">' . taoh_title_desc_decode($values['edu_activities'] ?? '') . '</textarea>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-12 -->';
    $data .= '<div class="col-lg-12">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium">Description</label>';
    $data .= '<div class="form-group">';
    $data .= '<textarea class="form-control" rows="4" maxlength="500" name="edu_description_' . $post_id . '">' . taoh_title_desc_decode($values['edu_description'] ?? '') . '</textarea>';
    $data .= '</div>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-12 -->';
    $data .= '<div class="col-lg-6">';
    $data .= '<div class="input-box">';
    $data .= '<label class="fs-13 text-black lh-20 fw-medium skills"> Skills (Choose from the suggested skills for better results)</label>';
    $data .= '<select id="edu_skillSelect_' . $post_id . '" multiple name="edu_skill_' . $post_id . '[]" placeholder="Type to select">';
    if (!empty($values['skill'])) {
        foreach ($values['skill'] as $key => $value) {
            if (!is_array($value)) {
                list ($pre, $post) = explode(':>', $value);
            } else {
                foreach ($value as $key => $tvalue) {
                    list ($pre, $post) = explode(':>', $tvalue);
                }
            }
            $data .= "<option value='$key' selected='selected'>$post</option>";
        }
    }
    $data .= '</select><script>edu_skillSelect(' . $post_id . ');</script>';
    $data .= '</div>';
    $data .= '</div><!-- end col-lg-6 -->';
    $data .= '</div><!-- end row -->';

    $data_btn = '';
    if (isset($_POST['add_or_edit']) && $_POST['add_or_edit'] == "Edit") {
        $data_btn = '<button type="submit" class="btn btn-danger float-left" name="edu_btnDelete"><i class="fa fa-trash mr-1"></i> Delete Education</button>';
    }

    echo json_encode($data . '~' . $data_btn);
    exit();
}


function taoh_invite_friend(){
  // echo 1;
  // exit;
  //https://ppapi.tao.ai/sandbox/posttest/core.refer.put?ops=invite&token=f3vpzoKr
  //&referral_type=scout&toenter[refer]=%7B%22first_name%22:%22sugu%22,%22last_name%22:%22sowmiya%22,%22email%22:%22meenu.rithan@gmail.com%22%7D,%7B%22first_name%22:%22meenu%22,%22last_name%22:%22sowmiya%22,%22email%22:%22meenu.ved03@gmail.com%22%7D&comment=invite%20you%20to%20the%20tao%20scout&link=https://dev.unmeta.net/hires
  $toenter = array();
  $taoh_call = "core.refer.put";
  $toenter['refer'] = json_encode(array(
                array(
                          'first_name' => $_POST['first_name'],
                          'last_name' => $_POST['last_name'],
                          'for_email' => $_POST['email'],
                          'comment' => $_POST['comment'],

                        )
                )
  );
    $actions_var = array(
        'action_url' => $_POST['referral_link'],
        'action_page_blurb' => 'In next step, Go to this page ' . $_POST['referral_link'] . '  and click Open Networking button',
        'action_email_vars' => array(
            'subject' => 'Join the Networking - ' . $_POST['network_title'],
            'supertitle' => 'You have been invited to join the ' . $_POST['network_title'] . ' Networking',
            'title' => 'Join the Networking - ' . $_POST['network_title'],
            'subtitle' => 'After creating and completing your profile, visit ' . $_POST['referral_link'] . '  to join the networking',
        ),
        'extra_info' => array(
            'title' => $_POST['network_title'],
            'app_name' => $_POST['app_name'],
            'action' => 'Join',
            'link' => $_POST['referral_link'],
            'action_link' => $_POST['referral_link'],
            'site_name' => TAOH_SITE_NAME_SLUG,
        ),
    );
    $toenter['refer_data'] = json_encode(array(
            'requested_by_ptoken' => taoh_get_dummy_token(),
            'from_link' => $_POST['from_link'],
            'to_link' => $_POST['referral_link'],
            'for_email' => $_POST['email'],
            'action_flag' => 1,
            'actions_var' => $actions_var,
            'referral_type' => $_POST['app_name'],
        )
    );
    $taoh_vals = array(
        'ops' => 'invite',
        'token' => taoh_get_dummy_token(),
        'toenter' => $toenter,
    );
  

// echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );

$result = json_decode(taoh_apicall_post($taoh_call, $taoh_vals));
//print_r($result);  
if($result->success==1){
 // $referral_id = $result->referral_id[0];
  //echo "---".$referral_id."---";
  echo 1;
}else{
  echo 0;
}
}


function taoh_indb_session(){
  $_POST['hires_type'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->hires_type;
  $_POST['site_locked'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->site_locked;
  $_POST['key'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->key;
  $_POST['referral'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->referral;
  $_POST['ptoken'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken;
  $_POST['user_locked'] = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->user_locked;

  taoh_session_save(TAOH_ROOT_PATH_HASH, ['USER_INFO' => (object) $_POST]);
  // echo "<pre>";
  // print_r(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);die;  
  // print_r($_POST);  
  // echo "</pre>";
  exit;
}

function recipe_get(){
  $category = ( isset( $_POST['category'] ) && $_POST['category'] )? $_POST['category']:'';
  $count = (isset($_POST['count']) && $_POST['count']) ? $_POST['count'] : '';
    $id = (isset($_POST['id']) && $_POST['id']) ? $_POST['id'] : '';
    $taoh_call = "core.content.get";
    $taoh_vals = array(
        "mod" => 'core',
        "ops" => 'related',
        "type" => 'blog',
        "recipe_id" => $id,
        'token' => taoh_get_dummy_token(1),
        "category" => $category,
        "count" => $count,
        "debug" => 1,
        //'cfcc5h' => 1 ////cfcache newly added
    );
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  $cache_name = $taoh_call.'_blog_related_' . hash('sha256', $taoh_call . serialize($taoh_vals));
  //$taoh_vals[ 'cfcache' ] = $cache_name;
  //$taoh_vals[ 'cache_name' ] = $cache_name;
  //$taoh_vals[ 'cache' ] = array ( "name" => $cache_name );
  ksort($taoh_vals);
  
  $data = taoh_apicall_get($taoh_call, $taoh_vals, '', 1);
  echo $data;
  die();
}

function taoh_check_referral_status(){
  //echo 1;die();
  $status = '0';
    if(isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'refer_token']!=''
      && isset($_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'] != '')
    {
      if(!taoh_user_is_logged_in() ) {
        $status = 0;
      }
      else if(defined( 'TAOH_API_TOKEN' ) && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN ){
          $user_data = taoh_user_all_info();
          if (  ! isset( $user_data->type )  && ! isset( $user_data->chat_name )) {
            $status = 2;
          }
          else{
               $url = $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'referral_back_url'];
               $status =3;
          }
      }
      
     
    }


    if(!taoh_user_is_logged_in() ) {
      $status = 0;
    }
    echo $status;
    die();

}

function taoh_blog_recipe_get_ajax() {
  $category = ( isset( $_POST['category'] ) && $_POST['category'] )? $_POST['category']:'';
  $count =  ( isset( $_POST['count'] ) && $_POST['count'] )? $_POST['count']:'';
  $id =  ( isset( $_POST['id'] ) && $_POST['id'] )? $_POST['id']:'';
  $taoh_call = "core.content.get";
  $taoh_vals = array(
    "mod" => 'core',
    "ops"=> 'related',
    "type"=> 'blog',
    "recipe_id"=> $id,
    'token'=> TAOH_API_TOKEN_DUMMY,
    "category"=> $category,
    "count" => $count,
    'sub_secret'=>TAOH_LP_SITE_URL_FULL_SECRET,
    'cache_name' => 'recipe_'.TAOH_ROOT_PATH_HASH.'_'.$id,
    'cache_time' => 30,
     //'cfcc5h' => 1 ////cfcache newly added
   // 'debug'=>1
  );
 //echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die;
 $content = taoh_apicall_get($taoh_call, $taoh_vals,'',1);
  if($content != "") {
    $response = $content;
    echo $response;die;
  } else {
    echo json_encode(['success' => false, 'message' => 'No data found']);
  }
}

function remove_cache_files()
{
    $fileNamesToRemove = json_decode($_POST['fileNames'], true);
    $mods = '';

    if (!is_array($fileNamesToRemove) || empty($fileNamesToRemove)) {
        foreach($fileNamesToRemove as $file_name){
            if(strpos($file_name, 'event') || strpos($file_name, 'rsvp')) {
                taoh_delete_local_cache('events',$file_name);
            } 
            else if(strpos($file_name, 'job')) {
                taoh_delete_local_cache('jobs',$file_name);
            }
            else if(strpos($file_name, 'ask')) {
                taoh_delete_local_cache('asks',$file_name);
            } 
        }
    }

    
    //taoh_delete_local_cache($mods,$fileNamesToRemove);

    echo json_encode(['success' => true]);
    exit();
}

function taoh_read_keyword_opt($full_filepath)
{
    $response = ['success' => false];

    if (file_exists($full_filepath)) {
        $fileContent = file_get_contents($full_filepath);
        $savedOption = json_decode($fileContent, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $response['success'] = true;
            $response['data'] = $savedOption;
        } else {
            $response['error'] = 'invalid_json_format';
            $response['message'] = 'Invalid JSON format';
        }
    } else {
        $response['error'] = 'file_not_found';
        $response['message'] = 'File not found';
    }

    return $response;
}

function taoh_get_keyword_opt()
{
    $response = ['success' => false];

    // Sanitize the input to prevent XSS or injection attacks
    $key = htmlspecialchars($_POST['key'], ENT_QUOTES, 'UTF-8');

    if (!empty($key)) {
        $filename = $key . '_keywords.txt';
        $filepath = TAOH_PLUGIN_PATH . '/keywords/';

        // Fetch keywords from a predefined constant, decoded from JSON
        $taoh_user_keywords = defined('TAOH_USER_KEYWORDS') ? getJsonDecodedData(TAOH_USER_KEYWORDS) : [];

        $keywords_str = $taoh_user_keywords[$key]['values'] ?? '';
        $final_keywords = array_filter(explode(',', $keywords_str));

        // Read existing keywords
        $existing_keywords = taoh_read_keyword_opt($filepath . $filename);
        if ($existing_keywords['success']) {
            $final_keywords = array_merge($final_keywords, $existing_keywords['data']);
        }

        $keywords = [];
        foreach ($final_keywords as $k => $keyword) {
            $keywords[] = [
                'id' => $k,
                'value' => trim(htmlspecialchars_decode($keyword)),
            ];
        }

        $response['success'] = true;
        $response['data'] = $keywords;
    }

    echo json_encode($response);
}

function taoh_add_keyword_opt()
{
    $response = ['success' => false];

    // Sanitize the inputs
    $key = htmlspecialchars($_POST['key'], ENT_QUOTES, 'UTF-8');
    $keyword = htmlspecialchars($_POST['keyword'], ENT_QUOTES, 'UTF-8');

    if (!empty($key) && !empty($keyword)) {
        $filename = $key . '_keywords.txt';
        $filepath = TAOH_PLUGIN_PATH . '/keywords/';

        // Read existing keywords
        $existing_keywords = taoh_read_keyword_opt($filepath . $filename);
        $final_keywords = [];

        if ($existing_keywords['success']) {
            $final_keywords = $existing_keywords['data'];
        }

        // Append the new keyword
        $final_keywords[] = $keyword;
        $final_keywords_str = json_encode(array_unique($final_keywords));

        // Write updated keywords back to the file
        if (file_put_contents($filepath . $filename, $final_keywords_str) !== false) {
            $response['success'] = true;
            $response['data'] = $keyword;
        } else {
            $response['message'] = 'Failed to add keyword';
        }
    } else {
        $response['message'] = 'Key and keyword are required';
    }

    echo json_encode($response);
}
 
function update_unsub()
{

      $remove = array("profile_detail_" . $_POST['taoh_ptoken'],
          "profile_short_" . taoh_get_api_token(),
          "profile_info_" . $_POST['taoh_ptoken'],
          "profile_cell_" . $_POST['taoh_ptoken'],
          "profile_full_" . $_POST['taoh_ptoken'],
          "profile_public_" . $_POST['taoh_ptoken'],
          "*_networking_cell_" . $_POST['taoh_ptoken'],
          "users_*",
      );

  $taoh_call = 'users.unsubscribe.update';
  $taoh_call_type = 'POST';
  $taoh_vals = array(
    'token' => taoh_get_dummy_token(),
    'mod' => 'tao_tao',
    'toenter' => $_POST,
    'redis_action' => 'profile_update',
    'redis_store' => 'taoh_intaodb_common',
    'ptoken' => $_POST['taoh_ptoken'],
    'cache' => array('remove' => $remove),
  );
  //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die; // Unsub API Call
  //$result = taoh_apicall_post($taoh_call, $taoh_vals);
  $result = taoh_apicall_post($taoh_call, $taoh_vals);
  
  unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
  echo $result;exit();

}

function taoh_report_bug(){
 
  $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
  $userAgentArr =getBrowserAndOS($userAgent);
  
  /* $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
    if (!empty($_FILES['screenshot']['name'])) {
        $screenshot_res = taoh_remote_file_upload($_FILES['screenshot'], $file_upload_url);
        if ($screenshot_res['success']) {
            $_POST['screenshot'] = $screenshot_res['output'];
        } else {
            $upload_status = false;
            $upload_error = 'screenshot_error';
        }
    } */
    $_POST['description'] = taoh_title_desc_encode($_POST['description']);
    $_POST['type']      = 'report_bug';
    // $_POST['steps']       = taoh_title_desc_encode($_POST['steps']);
    $_POST['metadata']['browser'] = $userAgentArr['browser'];
    $_POST['metadata']['version'] = $userAgentArr['version'];
    $_POST['metadata']['OS']      = $userAgentArr['OS'];
    
    $user_is_logged_in = taoh_user_is_logged_in() ?? false;
    $_POST['email'] = (!$user_is_logged_in) ? $_POST['bugemail'] : '';
    $taoh_call = 'core.send.bugreport';
    $taoh_vals = [
        'mod' => 'report_bug',
        'token' => (!$user_is_logged_in) ? taoh_get_api_token(1) :  taoh_get_api_token(),
        'toenter' => $_POST
    ];
    // $result = taoh_apicall_post_debug($taoh_call, $taoh_vals); exit;
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo $result;
}

function taoh_report_js_error(){
  $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
  $userAgentArr =getBrowserAndOS($userAgent);
  
  /* $file_upload_url = TAOH_CDN_PREFIX . '/cache/upload/now';
    if (!empty($_FILES['screenshot']['name'])) {
        $screenshot_res = taoh_remote_file_upload($_FILES['screenshot'], $file_upload_url);
        if ($screenshot_res['success']) {
            $_POST['screenshot'] = $screenshot_res['output'];
        } else {
            $upload_status = false;
            $upload_error = 'screenshot_error';
        }
    } */
    $_POST['description'] = taoh_title_desc_encode($_POST['type']).' '.$_POST['src'];
    $_POST['type']        = 'error_log';
    // $_POST['steps']       = taoh_title_desc_encode($_POST['steps']);
    $_POST['metadata']['browser'] = $userAgentArr['browser'];
    $_POST['metadata']['version'] = $userAgentArr['version'];
    $_POST['metadata']['OS']      = $userAgentArr['OS'];
    
    $user_is_logged_in = taoh_user_is_logged_in() ?? false;
    
    $taoh_call = 'core.send.bugreport';
    $taoh_vals = [
        'mod' => 'report_bug',
        'token' => (!$user_is_logged_in) ? taoh_get_api_token(1) :  taoh_get_api_token(),
        'toenter' => $_POST
    ];
    // $result = taoh_apicall_post_debug($taoh_call, $taoh_vals); exit;
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo $result;
}


function taoh_contact_host(){
  $toenter = array();
  
  $toenter['description'] = taoh_title_desc_encode($_POST['description']);
  $toenter['title'] = taoh_title_desc_encode($_POST['title']);
    
    $taoh_call = 'events.contact.host';
    $taoh_vals = [
        'mod' => 'events',
        'token' => taoh_get_api_token(),
        'eventtoken' => $_POST['eventtoken'],
        'to_email' => $_POST['to_email'],
        'toenter' => $toenter
    ];
    // $result = taoh_apicall_post_debug($taoh_call, $taoh_vals); exit;
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo $result;
}


function taoh_contact_us(){
  $toenter = array();
  
  $toenter['description'] = taoh_title_desc_encode($_POST['description']);
  $toenter['title'] = taoh_title_desc_encode($_POST['title']);
  $toenter['addtitle'] = taoh_title_desc_encode($_POST['contact_addtitle']);
    
    $taoh_call = 'events.contact.host';
    $taoh_vals = [
        'mod' => 'events',
        'token' => taoh_get_api_token(),
        'eventtoken' => $_POST['eventtoken'],
        'to_email' => $_POST['to_email'],
        'toenter' => $toenter
    ];
    // $result = taoh_apicall_post_debug($taoh_call, $taoh_vals); exit;
    $result = taoh_apicall_post($taoh_call, $taoh_vals);
    echo $result;
}


function update_dojo_tracker_status() {
  // Prepare data to update lock status
  $_POST['data']['values'] = json_encode( $_POST['data']['values']);
  $toenter['dojo_goal'] = $_POST['data'];


  $taoh_call = 'users.tao.add';
  $taoh_vals = [
      'token' => $_COOKIE[TAOH_ROOT_PATH_HASH . '_temp_api_token'],
      'mod' => 'tao_tao',
      'toenter' => $toenter,
      // 'debug'=>1,
  ];
  //echo "<pre>";print_r($taoh_vals);

  //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die();
  // Make the API call to update the lock status
  $result = taoh_apicall_post($taoh_call, $taoh_vals);
  //print_r($result);die();
  $decodedResult = json_decode($result, true);
  //print_r($decodedResult);die;
  
  // Set response header and return response
  header('Content-Type: application/json; charset=utf-8');

  // Return success or error based on the API response
  if ($decodedResult) {
      echo json_encode(['status' => 1]);
  } else {
      echo json_encode(['status' => 0, 'message' => 'Failed to update']);
  }

  taoh_exit();
}

function check_dojo_goal() {
	//$user_api = TAOH_API_PREFIX."/users.tao.get?cacheo=0&mod=tao_tao&token=".TAOH_API_TOKEN;
	//$user_data = json_decode(taoh_url_get_content($user_api));
  $taoh_call = "users.tao.get";
  $taoh_vals = array(
    'mod'=>'tao_tao',
    'token'=>TAOH_API_TOKEN,
    'ops' => 'full'
  );
  $taoh_call_type = "get";
  //echo taoh_apicall_get_debug( $taoh_call, $taoh_vals );die;
  $user_data = taoh_apicall_get( $taoh_call, $taoh_vals );
  //print_r($user_data);
  if($user_data != ''){
    $userresult = json_decode($user_data,1);
    if(isset($userresult['dojo_goal']) && $userresult['dojo_goal'] != 'undefined'){
      $userresult['dojo_goal']['values'] = json_decode($userresult['dojo_goal']['values']);

    }
    $user_data = json_encode($userresult);

  }
	echo  $user_data;
  taoh_exit();
}

function get_profile_data()
{
    $ptoken = $_GET['profile_token'];
    $pagename = $_GET['pagename'] ?? "";

    header('Content-Type: application/html; charset=utf-8');
    include_once('profile_modal_content.php');
}

function follow_profile()
{
    $response['success'] = false;

    $taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
    if ($taoh_user_is_logged_in) {
        $user_info_obj = taoh_user_all_info();

        $from_ptoken = $user_info_obj?->ptoken ?? '';
        $to_ptoken = $_POST['to_ptoken'] ?? '';

        if ($from_ptoken && $to_ptoken && $from_ptoken !== $to_ptoken) {
            $type = $_POST['type'] ?? '';
            $action_type = $_POST['action_type'] ?? '';

            // Cache keys to remove
            $remove = [];
            foreach ([$from_ptoken, $to_ptoken] as $ptoken) {
                $remove = array_merge($remove, [
                    "profile_detail_$ptoken",
                    "profile_info_$ptoken",
                    "profile_cell_$ptoken",
                    "profile_full_$ptoken",
                    "profile_public_$ptoken",
                    "followup_following_list_" . $ptoken . "_*",
                    "followup_followers_list_" . $ptoken . "_*"
                ]);
            }

            $taoh_vals = [
                'mod' => 'core',
                'token' => taoh_get_api_token(),
                'from_ptoken' => $from_ptoken,
                'to_ptoken' => $to_ptoken,
                'type' => $type,
                'action_type' => $action_type,
                'to_enter' => [
                    'follow_page' => 'profile'
                ]
            ];

            $taoh_vals['cache'] = [
                "action" => 'profile_update',
                "intao_store" => 'taoh_intaodb_common',
                "remove" => $remove,
            ];

//             $taoh_vals['debug'] = 1;
//             $taoh_vals['debug_api'] = 1;

            $result = taoh_apicall_post('core.followup.post', $taoh_vals);
            echo $result;
            exit();
        } else {
            $response['output'] = 'invalid_request';
        }
    } else {
        $response['output'] = 'not_logged_in';
    }

    echo json_encode($response);
    exit();
}

function taoh_followup_users_list()
{
    $ptoken = $_POST['ptoken'] ?? '';
    $follow_type = $_POST['follow_type'] ?? '';

    $taoh_vals = [
        'mod' => 'core',
        'token' => taoh_get_api_token(),
        'ptoken' => $ptoken,
        'follow_type' => $follow_type,
    ];

    if (!empty($_POST['search'])) {
        $taoh_vals['search'] = $_POST['search'];
    }

    if (isset($_POST['offset'])) {
        $taoh_vals['offset'] = (int)$_POST['offset'];
    }

    if (isset($_POST['limit'])) {
        $taoh_vals['limit'] = (int)$_POST['limit'];
    }

    $taoh_vals['cache_name'] = 'followup_' . $taoh_vals['follow_type'] . '_list_' . $taoh_vals['ptoken'] . '_' . hash('crc32', http_build_query($taoh_vals));

    $taoh_vals['cache_required'] = 0;
//     $taoh_vals['debug_api'] = 1;
//     echo taoh_apicall_get('core.followup.get.list', $taoh_vals);exit();

    echo taoh_apicall_get('core.followup.get.list', $taoh_vals);
    exit();
}
function social_login_success() {
  
  /* if (isset($_POST['success']) && $_POST['success'] == 1) {
      $token = $_POST['output'];
      setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $token, strtotime('+30 days'), '/');
      $response = 1;
  } else {
      $response = 0;
  } */

  if (isset($_POST['success']) && $_POST['success'] == 1) {
        $token = $_POST['output'];

        // Set API token cookie
        setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $token, strtotime('+30 days'), '/');
        setcookie(TAOH_ROOT_PATH_HASH . '_temp_api_token', $token, strtotime('+1 days'), '/');

        // Set email cookie if provided
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            setcookie(TAOH_ROOT_PATH_HASH . '_tao_api_email', $_POST['email'], strtotime('+30 days'), '/');
        }

        // Save token to session for immediate availability
        taoh_session_save(TAOH_ROOT_PATH_HASH, ['TAOH_API_TOKEN' => $token]);

        $response = 1;
    }  else {
        $response = 0;
    }     

  echo json_encode(['status' => $response]);
  taoh_exit();
}