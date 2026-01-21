<?php
/* config.php
This file only for main config
This should not dependent on any other files or functions but PHP core functions.
Do not write functions or redirection, or include files or functions here.
*/
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
/*===============SYSTEM VARS START================*/

if (file_exists('env.php')) require_once 'env.php';
if (file_exists(__DIR__ . '/config_data.php')) {
    require_once __DIR__ . '/config_data.php';
}

defined('TAOH_SITE_URL_ROOT_CORE') || define('TAOH_SITE_URL_ROOT_CORE', sprintf("%s%s", $_SERVER['HTTP_HOST'], dirname($_SERVER['SCRIPT_NAME'])));
defined('TAOH_SITE_URL_ROOT') || define('TAOH_SITE_URL_ROOT', sprintf("%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', TAOH_SITE_URL_ROOT_CORE));
defined('TAOH_SITE_URL_FULL') || define('TAOH_SITE_URL_FULL', sprintf("%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']));

defined('TAOH_SITE_DOC_ROOT') || define('TAOH_SITE_DOC_ROOT', sprintf("%s%s", $_SERVER['DOCUMENT_ROOT'], dirname($_SERVER['SCRIPT_NAME'])));
defined('TAOH_SITE_PATH_ROOT') || define('TAOH_SITE_PATH_ROOT', TAOH_SITE_DOC_ROOT);

defined('TAOH_PLUGIN_PATH_NAME') || define('TAOH_PLUGIN_PATH_NAME', dirname($_SERVER['SCRIPT_NAME']));

$taoh_tmp_arr = explode('/' . TAOH_PLUGIN_PATH_NAME, $_SERVER['REQUEST_URI']);
defined('TAOH_WERTUAL_ROOT_SUBDIRECTORY') || define('TAOH_WERTUAL_ROOT_SUBDIRECTORY', (stristr($_SERVER['REQUEST_URI'], '/' . TAOH_PLUGIN_PATH_NAME)) ? array_shift($taoh_tmp_arr) : '');
defined('TAOH_SITE_URL') || define('TAOH_SITE_URL', sprintf("%s://%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['HTTP_HOST'] . TAOH_WERTUAL_ROOT_SUBDIRECTORY));

if (!defined('TAOH_REDIRECT_URL')) {
$temp = explode(TAOH_SITE_URL_ROOT, TAOH_SITE_URL_FULL);
define('TAOH_REDIRECT_URL', (count($temp) > 1 ? trim($temp[1], '/') : '/'));
}
defined('TAOH_SITE_ROOT_HASH') || define('TAOH_SITE_ROOT_HASH', hash('crc32', TAOH_SITE_URL_ROOT_CORE));

defined('TAOH_ROOT_PATH_HASH') || define('TAOH_ROOT_PATH_HASH', TAOH_SITE_ROOT_HASH);
defined('TAOH_PLUGIN_PATH') || define('TAOH_PLUGIN_PATH', dirname(__FILE__));

defined('TAOH_MULTISITE') || define('TAOH_MULTISITE', false);
defined('TAOH_MOSAIC_PREFIX') || define('TAOH_MOSAIC_PREFIX', 'https://mosaic.tao.ai');
defined('TAOH_API_PREFIX') || define('TAOH_API_PREFIX', 'https://api.tao.ai');
defined('TAOH_CACHE_PREFIX') || define('TAOH_CACHE_PREFIX', TAOH_MOSAIC_PREFIX . '/hive/api');
defined('TAOH_API_TOKEN_DUMMY') || define('TAOH_API_TOKEN_DUMMY', 'hT93oaWC');
defined('TAOH_SUPER_ORGANIZER_TOKEN') || define('TAOH_SUPER_ORGANIZER_TOKEN', 'y4cfmcw03h04');//gu0vgkx69t1k , z3mbltb5mrf1

defined('TAOH_CREATE_GOOGLE_MEET_URL') || define('TAOH_CREATE_GOOGLE_MEET_URL', TAOH_MOSAIC_PREFIX . '/google_meet/api/create-meet.php');
defined('TAOH_CREATE_GOOGLE_MEET_API_KEY') || define('TAOH_CREATE_GOOGLE_MEET_API_KEY', 'sk_live_abc123xyz456def789ghi012jkl345mno678');

function requestFile( $filePath ) {

  // This function should contain the logic to generate or download the file
  // For this example, it does nothing but can be implemented as needed

  $api_config_generation = TAOH_API_PREFIX."/scripts/secret_env.php?site=" . $_SERVER['HTTP_HOST']."&host=".trim(dirname($_SERVER['SCRIPT_NAME']),'/')."&time=".time();


  $val_json = file_get_contents($api_config_generation);
  
  //echo  $api_config_generation;
  
  if ( ! $val_json ) return 0;


  $uuid_data = json_decode($val_json, true);
  //echo"===config_array======";print_r($uuid_data);die();
  //$config_array= $uuid_data['output'];

  $uuid  = $uuid_data['output'];


  $cachet_url = TAOH_CACHE_PREFIX.'/cacheops.php?code=tc2asi3iida2&ops=uuid&status=get&value='.$uuid;
  //echo "<br>===========".$cachet_url;die();
  $result = file_get_contents($cachet_url);


  $config_data = json_decode($result, true);
 //echo"===config_array======";print_r($config_data);die();


  $config_array_data = $config_data['output'];
  $config_array = json_decode($config_array_data, true);
  
 
  //echo'<pre>';print_r($config_array_data);die();
  

  $output = "defined('TAOH_SITE_URL_ROOT') || define('TAOH_SITE_URL_ROOT', '" . TAOH_SITE_URL_ROOT . "');\n";
  if ( is_array( $config_array ) || is_object( $config_array ) ){
      foreach ($config_array as $key => $value) {
          defined(strtoupper($key)) || define(strtoupper($key), $value);
          $value = $value === false ? 0 : $value;
          $output .= "defined('" . strtoupper($key) . "') || define('" . strtoupper($key) . "', " . (is_string($value) ? "'$value'" : $value) . ");\n";
      }
  
      file_put_contents($filePath, "<?php\n\n" . $output . "\n\n?>");    
  }
  return 1;
}

//https://dd.localhost/hires-i/login/code/LJSYJL
if ( defined( 'TAOH_MULTISITE' ) && TAOH_MULTISITE===true ){
  $filePath = TAOH_PLUGIN_PATH . '/cache/configs/env_' . TAOH_SITE_ROOT_HASH . '.cache';
  if(isset($_GET['clear']) && $_GET['clear'] == 'config'){

    if (file_exists($filePath)) 
        unlink($filePath);

    $purge_url = ' https://api.tao.ai/scripts/cfpurge.php?platform='.$_SERVER['HTTP_HOST'].'&y='.rand(10,100);
    //echo $purge_url;
    file_get_contents($purge_url);
    //die();
    
  }
  
  
  if ( file_exists($filePath) && TAOH_MULTISITE ) {
    $fileAgeHours = (time() - filemtime($filePath)) / 3600;
  
      // Check if the file is 12 hours old or fresher
      if ($fileAgeHours <= 12) {
          require_once $filePath;
      } else {
          unlink($filePath);
          requestFile($filePath);
      }
  } else {
      requestFile($filePath);
  }
  
}

if (file_exists('env_post.php')) require_once 'env_post.php';

//requestFile( $filePath );


// Example function to simulate file request

//echo "============".TAOH_API_SECRET;

defined('TAOH_SITE_ENVIRONMENT') || define('TAOH_SITE_ENVIRONMENT', 'production'); // development, production
if (0){
    if ( defined('TAOH_SITE_ENVIRONMENT') && TAOH_SITE_ENVIRONMENT === 'production' && ! file_get_contents('https://tao.ai/health/', false, stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]))) {
        header('Location: ' . TAOH_SITE_URL_ROOT . '/busy.php');
        exit();
    }    
}
defined('TAOH_INDEXEDDB_VERSION') || define('TAOH_INDEXEDDB_VERSION', 54);
defined('TAOH_CSS_JS_VERSION') || define('TAOH_CSS_JS_VERSION', '6.5.8');
defined('TAOH_PERFORMANCE_TEST') || define('TAOH_PERFORMANCE_TEST', TRUE);
defined('TAOH_CORE_PATH') || define('TAOH_CORE_PATH', dirname(__FILE__) . '/core');
defined('TAOH_APP_PATH') || define('TAOH_APP_PATH', dirname(__FILE__) . '/app');
defined('TAOH_HOME_URL') || define('TAOH_HOME_URL', TAOH_SITE_URL_ROOT);
defined('TAOH_SITE_POLL') || define('TAOH_SITE_POLL', false);
if (!defined('TAOH_SITE_CURRENT_APP_SLUG')) {
$app_slug = 'club';
if (TAOH_REDIRECT_URL != '') {
$temp = explode('/', trim(TAOH_REDIRECT_URL, "/"));
$app_slug = $temp[0];
if (!file_exists(TAOH_SITE_DOC_ROOT . '/app/' . $app_slug) && !file_exists(TAOH_SITE_DOC_ROOT . '/core/' . $app_slug)) {
$app_slug = 'club';
}
}
define('TAOH_SITE_CURRENT_APP_SLUG', $app_slug);
}
defined('TAOH_API_LOG_ENABLE') || define('TAOH_API_LOG_ENABLE', 0); // 1 = Log only request, 2 = Log request and response
defined('TAOH_CACHE_SCOPE') || define('TAOH_CACHE_SCOPE', 'global');
defined('TAOH_HEALTH_TIMEOUT') || define('TAOH_HEALTH_TIMEOUT', 15);
defined('TAOH_REMOTE_CACHE') || define('TAOH_REMOTE_CACHE', 1);
defined('TAOH_OPS_HEALTH') || define('TAOH_OPS_HEALTH', 'prod');

defined('TAOH_API_USERID') || define('TAOH_API_USERID', '');
defined('TAOH_SITE_ROBOT') || define('TAOH_SITE_ROBOT', '/robots.txt');
defined('TAOH_HEALTH_CHECK') || define('TAOH_HEALTH_CHECK', 0);
defined('TAOH_DEFAULT_TITLE') || define('TAOH_DEFAULT_TITLE', 'Developer');
defined('TAOH_DEFAULT_SKILL') || define('TAOH_DEFAULT_SKILL', 'PHP');
defined('TAOH_DEFAULT_COMPANY') || define('TAOH_DEFAULT_COMPANY', 'Tao.ai');
defined('TAOH_DEFAULT_COUNTRY') || define('TAOH_DEFAULT_COUNTRY', 'US');
defined('TAOH_DEBUG') || define('TAOH_DEBUG', false);

defined('TAOH_INTAODB_ENABLE') || define('TAOH_INTAODB_ENABLE', true);
defined('TAOH_LOCAL_CACHE_ENABLE') || define('TAOH_LOCAL_CACHE_ENABLE', true);
defined('TAOH_SIMPLE_LOGIN') || define('TAOH_SIMPLE_LOGIN', true);
defined('TAOH_PROFILE_PICTURE_UPLOAD') || define('TAOH_PROFILE_PICTURE_UPLOAD', true);
defined('TAOH_DEFAULT_TIMEZONE') || define('TAOH_DEFAULT_TIMEZONE', 'UTC');
defined('TAOH_TIMEZONE_FORMAT') || define('TAOH_TIMEZONE_FORMAT', 'YmdHis');

if (!defined('TAOH_API_TOKEN')) {
if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token'])) {
//define( 'TAOH_API_TOKEN', @$_COOKIE['taoh_api_token'] );
define('TAOH_API_TOKEN', $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']);
} else if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token'])) {
define('TAOH_API_TOKEN', $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token']);
}
}
defined('TAOH_MY_NOW_CODE') || define('TAOH_MY_NOW_CODE', 'stlo' . hash('crc32', TAOH_SITE_URL_ROOT . (defined('TAOH_API_TOKEN') && TAOH_API_TOKEN ? TAOH_API_TOKEN : '') . time()));
if (defined('TAOH_API_SECRET')) {
  setcookie("taoh_api_key", TAOH_API_SECRET, strtotime('+30 days'), '/');
}
defined('TAOH_WERTUAL_NAME_SLUG') || define('TAOH_WERTUAL_NAME_SLUG', 'club');
defined('TAOH_WERTUAL_SLUG') || define('TAOH_WERTUAL_SLUG', 'club');
defined('TAOH_ENCRYPTION_KEY') || define('TAOH_ENCRYPTION_KEY', hash('crc32', 'TAOH'));
/*===============SYSTEM VARS STOP================*/

/*===============PARTNER VARS START================*/
defined('TAOH_OPS_CODE') || define('TAOH_OPS_CODE', 'tc2asi3iida2');
if (!defined('TAOH_VIDEO_CONF_LINK')) define('TAOH_VIDEO_CONF_LINK','https://meet.tao.ai/');
defined('TAOH_CDN_PREFIX') || define('TAOH_CDN_PREFIX', 'https://cdn.tao.ai');
defined('TAOH_CDN_JS_PREFIX') || define('TAOH_CDN_JS_PREFIX', TAOH_CDN_PREFIX.'/assets/wertual/js');
defined('TAOH_CDN_CSS_PREFIX') || define('TAOH_CDN_CSS_PREFIX', TAOH_CDN_PREFIX.'/assets/wertual/css');
defined('TAOH_CDN_MAIN_PREFIX') || define('TAOH_CDN_MAIN_PREFIX', TAOH_CDN_PREFIX.'/assets/wertual');
defined('TAOH_PCDN_PREFIX') || define('TAOH_PCDN_PREFIX', 'https://pcdn.tao.ai');
defined('TAOH_PARK_PREFIX') || define('TAOH_PARK_PREFIX', 'https://park.tao.ai');
defined('TAOH_OPS_PREFIX') || define('TAOH_OPS_PREFIX', 'https://opslogy.com');
defined('TAOH_DASH_PREFIX') || define('TAOH_DASH_PREFIX', 'https://dash.tao.ai');
defined('TAOH_DASH_CODE') || define('TAOH_DASH_CODE', TAOH_DASH_PREFIX);

defined('TAOH_WEMET_PREFIX') || define('TAOH_WEMET_PREFIX', 'https://wemet.tao.ai');
defined('TAOH_OBVIOUS_PREFIX') || define('TAOH_OBVIOUS_PREFIX', 'https://obviousbaba.com');
defined('TAOH_MENU_FOOTER_LINE_3') || define('TAOH_MENU_FOOTER_LINE_3', json_encode(array(
'Privacy Policy' => 'https://tao.ai/privacy.php',
'Terms &amp; Conditions' => 'https://tao.ai/terms.php',
'Code of Conduct' => 'https://tao.ai/conduct.php',
'Cookie' => 'https://tao.ai/cookie.php',
)));
/*===============PARTNER VARS STOP================*/

/*===============POD MACRO VARS START================*/
defined('TAOH_SITE_DONATE_ENABLE') || define('TAOH_SITE_DONATE_ENABLE', 1);
defined('TAOH_REFER_ENABLE') || define('TAOH_REFER_ENABLE', 1);
defined('TAOH_LOGIC_LOCK_TEXT') || define('TAOH_LOGIC_LOCK_TEXT', 'This site requires a code to access, ask the admin or anyone who shared this link to get you the code to complete the sigup.');
defined('TAOH_LOGIC_LOCK_CODE') || define('TAOH_LOGIC_LOCK_CODE', FALSE);
defined('TAOH_SITE_GLOBAL_POST') || define('TAOH_SITE_GLOBAL_POST',true);
defined('TAOH_SITE_GLOBAL_READ') || define('TAOH_SITE_GLOBAL_READ',true);
defined('TAOH_API_SECRET') || define('TAOH_API_SECRET', 'lmTJXUWD');
defined('TAOH_API_DUMMY_SECRET') || define('TAOH_API_DUMMY_SECRET', 'lmTJXUWD');
defined('TAOH_WERTUAL_DESCRIPTION') || define('TAOH_WERTUAL_DESCRIPTION', 'Use the apps below to help find your next success.');
defined('TAOH_CLUB_TARGET') || define('TAOH_CLUB_TARGET', 'Professional');
defined('TAOH_CLUB_HASH') || define('TAOH_CLUB_HASH', TAOH_CLUB_TARGET);
defined('TAOH_CLUB_HASH_2') || define('TAOH_CLUB_HASH_2', 'Employer');
defined('TAOH_SITE_SOURCE') || define('TAOH_SITE_SOURCE', 'https://worker1.com');
defined('TAOH_SITE_NAME_SLUG') || define('TAOH_SITE_NAME_SLUG', 'Worker1');
defined('TAOH_SITE_TITLE') || define('TAOH_SITE_TITLE', TAOH_SITE_NAME_SLUG . ' | Career Growth Home connecting ' . TAOH_CLUB_TARGET . ' with opportunities, networks and resources to bring career home faster');
defined('TAOH_SITE_DESCRIPTION') || define('TAOH_SITE_DESCRIPTION', TAOH_SITE_NAME_SLUG . ' / ' . TAOH_PLUGIN_PATH_NAME . ' is designed to create a robust and trusted home for job seekers, talent seekers and service providers by connecting ' . TAOH_CLUB_TARGET . ' with job opportunies with Jobs, professional networks with Networking, career centered events with Events and lots of resources to assist with jobs, works, and wellness related topics.');
defined('TAOH_SITE_KEYWORDS') || define('TAOH_SITE_KEYWORDS', TAOH_CLUB_HASH . ', ' . TAOH_CLUB_HASH_2 . ', Career Development, Job Search, Professional Networking, Career Events, Career Resources, Professional Growth, Connections, Wellness, Jobs, Work, Networking, Career Home, Career Growth Home');
defined('TAOH_SITE_SLUG') || define('TAOH_SITE_SLUG', strtolower(TAOH_SITE_NAME_SLUG));
defined('TAOH_SITE_LOGO') || define('TAOH_SITE_LOGO', TAOH_CDN_MAIN_PREFIX . '/images/worker1_sq.png');
defined('TAOH_SITE_FAVICON') || define('TAOH_SITE_FAVICON', TAOH_CDN_MAIN_PREFIX . '/images/worker1_sq.png');
/* Google Analytics */
defined('TAOH_SITE_GA_ENABLE') || define('TAOH_SITE_GA_ENABLE', TRUE);
defined('TAOH_SITE_GA') || define('TAOH_SITE_GA', 'G-8H9Y38L6J1');

defined('TAO_CLUB_COMPANY') || define('TAO_CLUB_COMPANY', TAOH_SITE_NAME_SLUG);

defined('BLOG_MAXMIUM_STATUS') || define('BLOG_MAXMIUM_STATUS', 15);
defined('TAOH_NOTIFICATION_ENABLED') || define('TAOH_NOTIFICATION_ENABLED', 0);
defined('TAOH_NOTIFICATION_STATUS') || define('TAOH_NOTIFICATION_STATUS', 0);
defined('TAOH_NOTIFICATION_LOOP_TIME_INTERVAL') || define('TAOH_NOTIFICATION_LOOP_TIME_INTERVAL', 120000);
defined('TAOH_SITE_FAVICON') || define('TAOH_SITE_FAVICON', 'https://worker1.com/images/worker1_sq.png');


/* THEME STYLES CONFIG
* AVAILABLE OPTIONS : THEMEBGCOLOR, THEMEFONTFAMILY, THEMEFONTSIZE
*
* */
defined('THEMEBGCOLOR') || define('THEMEBGCOLOR', '#f4f4f4');
defined('THEMEFONTSIZE') || define('THEMEFONTSIZE', '16px');

defined('TAOH_SITE_PRE_TITLE') || define('TAOH_SITE_PRE_TITLE',TAOH_SITE_NAME_SLUG.' Community');
defined('TAOH_SITE_MAIN_TITLE') || define('TAOH_SITE_MAIN_TITLE','Elevate Your Career with '.TAOH_SITE_NAME_SLUG.'!');
defined('TAOH_SITE_MAIN_DESCRIPTION') || define('TAOH_SITE_MAIN_DESCRIPTION',"
Are you ready to advance your career trajectory? You're in the right<br />
place! Join as a member of one of the fastest-growing ".strtolower(TAOH_CLUB_TARGET)."<br />
communities online. At ".TAOH_SITE_NAME_SLUG.",<br />
we're not just about job listings - we're about fostering a vibrant,<br />inclusive network where career growth thrives.
");
defined('TAOH_SITE_MAIN_OPTION_1') || define('TAOH_SITE_MAIN_OPTION_1','<strong>Join local / global '.TAOH_SITE_NAME_SLUG.' events</strong>');
defined('TAOH_SITE_MAIN_OPTION_2') || define('TAOH_SITE_MAIN_OPTION_2','<strong>Connect with '. TAOH_SITE_NAME_SLUG .' peers</strong>');
defined('TAOH_SITE_MAIN_OPTION_3') || define('TAOH_SITE_MAIN_OPTION_3','<strong>Engage and Grow through '. TAOH_SITE_NAME_SLUG .' opportunities</strong>');

defined('TAOH_SITE_MAIN_HERO_COLOR') || define('TAOH_SITE_MAIN_HERO_COLOR','rgba(37,67,187, 0.7)');// #2557A7
defined('TAOH_SITE_MAIN_HERO_IMAGE') || define('TAOH_SITE_MAIN_HERO_IMAGE','https://cdn.tao.ai/upload/files/tao_splash.jpg');
defined('TAOH_BANNER_VIDEO') || define('TAOH_BANNER_VIDEO','https://www.youtube.com/embed/o9qbgV0Aotk?rel=0');



defined('TAOH_MENU_HEADER_1') || define('TAOH_MENU_HEADER_1', json_encode(array(
'#Club' => '/'.TAOH_WERTUAL_SLUG.'/',
'TheWorkTimes' => 'https://TheWorkTimes.com',
'NoWorkerLeftBehind' => 'https://noworkerleftbehind.com/',
'Re.Imagine.Work' => 'https://re.imagine.work/',
'TAO.ai' => 'https://tao.ai/',
)));

defined('TAOH_MENU_HEADER_2') || define('TAOH_MENU_HEADER_2', json_encode(array(
'Interview' => '?q=interview',
'Self Care' => '?q=self-care',
'Organization' => '?q=organization',
'Negotiation' => '?q=negotiation',
'Jobs of Future' => '?q=jobs-of-future',
'Resume' => '?q=resume',
'Job Search' => '?q=job-search',
)));

defined('TAOH_MENU_FOOTER_LINE_1') || define('TAOH_MENU_FOOTER_LINE_1', json_encode(array(
'#Club' => '/'.TAOH_WERTUAL_SLUG.'/',
'TheWorkTimes' => 'https://TheWorkTimes.com',
'NoWorkerLeftBehind' => 'https://noworkerleftbehind.com/',
'Re.Imagine.Work' => 'https://re.imagine.work/',
'TAO.ai' => 'https://tao.ai/',
)));

defined('TAOH_MENU_FOOTER_LINE_2') || define('TAOH_MENU_FOOTER_LINE_2', json_encode(array(
'Employers' => 'employers',
'Partners' => 'partners',
'Professionals' => 'professionals',
)));

defined('TAOH_ADS_ENABLE') || define('TAOH_ADS_ENABLE', 0);


defined('TAOH_ADS_BANNERS') || define('TAOH_ADS_BANNERS', json_encode(array(
array(
'TITLE' => 'FirstFridayFair Virtual Job Fair Career Expo Event',
'IMAGEURL' => 'https://grants.club/wp-content/uploads/2024/01/job-fair.jpg',
'ALT_TEXT' => 'FirstFridayFair Virtual Job Fair Career Expo Event',
'OUTSIDEURL' => '/club/events/next/6j382kbpcnvd',
),
array(
'TITLE' => 'Career Professional ThirdThursdayNetworking',
'IMAGEURL' => 'https://grants.club/wp-content/uploads/2024/01/job-fair-maxresdefault.jpg',
'ALT_TEXT' => 'Career Professional ThirdThursdayNetworking',
'OUTSIDEURL' => '/club/events/next/bg6oa1okn648tnm',
),
)));

defined('TAOH_WIDGET_ROOMS') || define('TAOH_WIDGET_ROOMS', json_encode(array(
array(
'room_title' => 'Job Search CoLab - Online Collaborative Job Search Support and Networking',
'room_keyword' => 'Job Search, Networking, Career, Job Seekers, Collaboration, Workshop, Online Event',
'room_url' => '/networking/lobby/',
),
array(
'room_title' => 'Resume CoLab - Online Collaborative Resume Improvement and Networking',
'room_keyword' => 'Resume, Networking, Career, Job Seekers, Collaboration, Workshop, Online Event',
'room_url' => '/networking/lobby/',
),
array(
'room_title' => 'Interview CoLab - Online Collaborative Interview Preparation and Networking',
'room_keyword' => 'Interview, Networking, Career, Job Seekers, Collaboration, Workshop, Online Event',
'room_url' => '/networking/lobby/',
),
)));

defined('TAOH_CHANNEL_DISSCUSSION') || define('TAOH_CHANNEL_DISSCUSSION', 1);
defined('TAOH_CHANNEL_DIRECT_MESSAGE') || define('TAOH_CHANNEL_DIRECT_MESSAGE', 2);
defined('TAOH_CHANNEL_TICKET_TYPE') || define('TAOH_CHANNEL_TICKET_TYPE', 3);
defined('TAOH_CHANNEL_FROM_ROOM_TAG') || define('TAOH_CHANNEL_FROM_ROOM_TAG', 4);

defined('TAOH_CHAT_NETWORK') || define('TAOH_CHAT_NETWORK', 0);
defined('TAOH_CHAT_SCOUT') || define('TAOH_CHAT_SCOUT', 1);
defined('TAOH_CHAT_SYSTEM') || define('TAOH_CHAT_SYSTEM', 2);
defined('TAOH_CHAT_FORUM') || define('TAOH_CHAT_FORUM', 3);

defined('TAOH_ENABLE_FORUM_ASM') || define('TAOH_ENABLE_FORUM_ASM', false);
defined('TAOH_ENABLE_NETWORK_ASM') || define('TAOH_ENABLE_NETWORK_ASM', false);

defined('TAOH_NETWORK_TOUR_VERSION') || define('TAOH_NETWORK_TOUR_VERSION', 1);

defined('TAOH_CHANNEL_DEFAULT') || define('TAOH_CHANNEL_DEFAULT', 1);
defined('TAOH_CHANNEL_EXHIBITOR') || define('TAOH_CHANNEL_EXHIBITOR', 2);
defined('TAOH_CHANNEL_SPONSOR') || define('TAOH_CHANNEL_SPONSOR', 3);
defined('TAOH_CHANNEL_DM') || define('TAOH_CHANNEL_DM', 4);
defined('TAOH_CHANNEL_ORG_DM') || define('TAOH_CHANNEL_ORG_DM', 5);
defined('TAOH_CHANNEL_SPEED_NTW') || define('TAOH_CHANNEL_SPEED_NTW', 6);
defined('TAOH_CHANNEL_SESSION') || define('TAOH_CHANNEL_SESSION', 7);

if (!defined('TAOH_USER_KEYWORDS')) {
include 'keywords.php';
define('TAOH_USER_KEYWORDS', ((function_exists('get_keywords_data')) ? json_encode(get_keywords_data()) : '{}'));
}

defined('TAOH_ADMIN_TOKENS') || define('TAOH_ADMIN_TOKENS',"02d7cgo61ll9,8NvDW91c,3AiC4QQi,CDXDDExd,3hPXa97l,z3mbltb5mrf1");

/*===============POD MACRO VARS STOP================*/

/*===============POD MICRO VARS START================*/

// FOR JUSASK CHATBOTS
defined('TAOH_JUSASK_ENABLE') || define('TAOH_JUSASK_ENABLE', true);
defined('TAOH_JUSASK_ICON') || define('TAOH_JUSASK_ICON', TAOH_CDN_PREFIX . '/app/jusask/images/jusask_chat_icon.png');
defined('TAOH_JUSASK_BOT_1') || define('TAOH_JUSASK_BOT_1', true);
defined('TAOH_JUSASK_BOT_1_NAME') || define('TAOH_JUSASK_BOT_1_NAME', 'CareerCoach Chatbot');
defined('TAOH_JUSASK_BOT_1_ASK') || define('TAOH_JUSASK_BOT_1_ASK', 'SideKick'); // askSideKick // TeamDojo
defined('TAOH_JUSASK_BOT_1_TITLE') || define('TAOH_JUSASK_BOT_1_TITLE', 'Career Advice with #SideKick'); // TeamDojo
defined('TAOH_JUSASK_BOT_1_MSG1') || define('TAOH_JUSASK_BOT_1_MSG1', 'I could help answer your career related question. To get the best possible answers, please be as descriptive and detailed as possible in your questions.');
defined('TAOH_JUSASK_BOT_1_MSG2') || define('TAOH_JUSASK_BOT_1_MSG2', 'Ask me anything that is stopping you  <br />from achieving your career goals.  <br />I will try to help you out. Ask away!');
defined('TAOH_JUSASK_BOT_1_DESCRIPTION') || define('TAOH_JUSASK_BOT_1_DESCRIPTION', TAOH_JUSASK_BOT_1_NAME . ' is a brilliant career coach, that provides advice on topics useful for career path, job search, resume writing, interview preparation, and more. Advice is unique, practical, personalized and oftern not traditional. The tone is friendly and supportive. ' . TAOH_JUSASK_BOT_1_NAME . ' is a great listener, direct, succint, to the point, brief and provides practical advice.');
defined('TAOH_JUSASK_BOT_1_IMG') || define('TAOH_JUSASK_BOT_1_IMG', TAOH_CDN_PREFIX . '/app/jusask/images/sidekick_icon.png');

defined('TAOH_JUSASK_BOT_2') || define('TAOH_JUSASK_BOT_2', true);
defined('TAOH_JUSASK_BOT_2_NAME') || define('TAOH_JUSASK_BOT_2_NAME', 'LifeCoach Chatbot');
defined('TAOH_JUSASK_BOT_2_ASK') || define('TAOH_JUSASK_BOT_2_ASK', 'ObviousBaba'); // askObviousBaba
defined('TAOH_JUSASK_BOT_2_TITLE') || define('TAOH_JUSASK_BOT_2_TITLE', 'Life Advice with #ObviousBaba');
defined('TAOH_JUSASK_BOT_2_MSG1') || define('TAOH_JUSASK_BOT_2_MSG1', 'I could help answer your career related question. To get the best possible answers, please be as descriptive and detailed as possible in your questions.');
defined('TAOH_JUSASK_BOT_2_DESCRIPTION') || define('TAOH_JUSASK_BOT_2_DESCRIPTION', TAOH_JUSASK_BOT_2_NAME . ' is a brilliant life coach, that provides advice on life coach related topics useful for career path, job search, resume writing, interview preparation, and more. Advice is unique, practical, personalized and oftern not traditional. The tone is friendly and supportive. ' . TAOH_JUSASK_BOT_2_NAME . ' is a great listener, direct, succint, to the point, brief and provides practical advice.');
defined('TAOH_JUSASK_BOT_2_IMG') || define('TAOH_JUSASK_BOT_2_IMG', TAOH_OBVIOUS_PREFIX . '/images/obviousbaba.png');


defined('TAOH_JUSASK_SUPPORT_BOT') || define('TAOH_JUSASK_SUPPORT_BOT', true);
defined('TAOH_JUSASK_SUPPORT_BOT_NAME') || define('TAOH_JUSASK_SUPPORT_BOT_NAME', 'Asq Support Agent');
defined('TAOH_JUSASK_SUPPORT_BOT_ASK') || define('TAOH_JUSASK_SUPPORT_BOT_ASK', 'Dojo'); // askAsq // Asq
defined('TAOH_JUSASK_SUPPORT_BOT_TITLE') || define('TAOH_JUSASK_SUPPORT_BOT_TITLE', 'Get Support with #Dojo'); // JustAsq
defined('TAOH_JUSASK_SUPPORT_BOT_MSG1') || define('TAOH_JUSASK_SUPPORT_BOT_MSG1', 'I am here to answer your support questions. So, please provide as much detail as possible, so I can provide you the best answer.');
defined('TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION') || define('TAOH_JUSASK_SUPPORT_BOT_DESCRIPTION', TAOH_JUSASK_SUPPORT_BOT_NAME . ' is a empathetic technical support coach, that provides advice on issues faced on the platform ' . TAOH_SITE_TITLE . '.');
defined('TAOH_JUSASK_SUPPORT_BOT_IMG') || define('TAOH_JUSASK_SUPPORT_BOT_IMG', TAOH_CDN_PREFIX . '/app/jusask/images/jusask_sq_64.png');

defined('TAOH_HIRES_APP_DETAILS') || define('TAOH_HIRES_APP_DETAILS', 'wertual');
defined('TAOH_JOBS_ENABLE') || define('TAOH_JOBS_ENABLE', true);
defined('TAOH_ASKS_ENABLE') || define('TAOH_ASKS_ENABLE', false);
defined('TAOH_EVENTS_ENABLE') || define('TAOH_EVENTS_ENABLE', true);
defined('TAOH_READS_ENABLE') || define('TAOH_READS_ENABLE', true);
defined('TAOH_MESSAGE_ENABLE') || define('TAOH_MESSAGE_ENABLE', true);
defined('TAOH_NEWS_ENABLE') || define('TAOH_NEWS_ENABLE',false);
defined('TAOH_ANNOUNCEMENT_ENABLE') || define('TAOH_ANNOUNCEMENT_ENABLE',0);
defined('TAOH_SCOUT_ENABLE') || define('TAOH_SCOUT_ENABLE',false);
defined('TAOH_PAID_JOB_ENABLE') || define('TAOH_PAID_JOB_ENABLE',true);
defined('TAOH_NETWORKPAGE_NAME') || define('TAOH_NETWORKPAGE_NAME', 'networking');
defined('TAOH_COMMUNITY_NAME') || define('TAOH_COMMUNITY_NAME', 'community');
defined('TAOH_MESSAGEPAGE_NAME') || define('TAOH_MESSAGEPAGE_NAME', 'message');
defined('TAOH_LEARNING_ENABLE') || define('TAOH_LEARNING_ENABLE', TRUE);
defined('TAOH_LEARNING_WIDGET_ENABLE') || define('TAOH_LEARNING_WIDGET_ENABLE', 0);
defined('TAOH_CLUBS_ENABLE') || define('TAOH_CLUBS_ENABLE', TRUE);


defined('TAOH_NETWORK_GLOBAL') || define('TAOH_NETWORK_GLOBAL', '1');
defined('TAOH_USE_GLOBAL_ROOMS') || define('TAOH_USE_GLOBAL_ROOMS', 1);
defined('TAOH_USE_SOCIAL_LOGIN') || define('TAOH_USE_SOCIAL_LOGIN', 1);
defined('TAOH_LOGGING_ENABLE') || define('TAOH_LOGGING_ENABLE', 1);
defined('TAOH_METRICS_COUNT_SHOW') || define('TAOH_METRICS_COUNT_SHOW', 0);
defined('TAOH_METRICS_EYE_SHOW') || define('TAOH_METRICS_EYE_SHOW', 0);

defined('TAOH_SOCIAL_LIKES_THRESHOLD') || define('TAOH_SOCIAL_LIKES_THRESHOLD', 0);
defined('TAOH_SOCIAL_SHARES_THRESHOLD') || define('TAOH_SOCIAL_SHARES_THRESHOLD', 0);
defined('TAOH_SOCIAL_COMMENTS_THRESHOLD') || define('TAOH_SOCIAL_COMMENTS_THRESHOLD', 0);
defined('TAOH_SOCIAL_VIEWS_THRESHOLD') || define('TAOH_SOCIAL_VIEWS_THRESHOLD', 0);
defined('TAOH_SOCIAL_CLICKS_THRESHOLD') || define('TAOH_SOCIAL_CLICKS_THRESHOLD', 10);
defined('TAOH_SOCIAL_RATING_THRESHOLD') || define('TAOH_SOCIAL_RATING_THRESHOLD', 4);
defined('TAOH_JOBS_POST_LOCAL') || define('TAOH_JOBS_POST_LOCAL',!TAOH_SITE_GLOBAL_POST);
defined('TAOH_ASKS_POST_LOCAL') || define('TAOH_ASKS_POST_LOCAL', !TAOH_SITE_GLOBAL_POST);
defined('TAOH_EVENTS_POST_LOCAL') || define('TAOH_EVENTS_POST_LOCAL', !TAOH_SITE_GLOBAL_POST);
defined('TAOH_READS_POST_LOCAL') || define('TAOH_READS_POST_LOCAL', !TAOH_SITE_GLOBAL_POST);

defined('TAOH_JOBS_GET_LOCAL') || define('TAOH_JOBS_GET_LOCAL', !TAOH_SITE_GLOBAL_READ);
defined('TAOH_ASKS_GET_LOCAL') || define('TAOH_ASKS_GET_LOCAL', !TAOH_SITE_GLOBAL_READ);
defined('TAOH_EVENTS_GET_LOCAL') || define('TAOH_EVENTS_GET_LOCAL',!TAOH_SITE_GLOBAL_READ);
defined('TAOH_READS_GET_LOCAL') || define('TAOH_READS_GET_LOCAL',!TAOH_SITE_GLOBAL_READ);

/*===============POD MICRO VARS STOP================*/

/*===============APP VARS START================*/

/*===============APP VARS STOP================*/

/*===============COMPILED CONFIG VARS START================*/
defined('TAOH_CACHE_CHAT_PREFIX') || define('TAOH_CACHE_CHAT_PREFIX', TAOH_MOSAIC_PREFIX . '/hive/networking');
defined('TAOH_CACHE_CONNECT_PREFIX') || define('TAOH_CACHE_CONNECT_PREFIX', TAOH_MOSAIC_PREFIX . '/hive/networking');
defined('TAOH_CACHEAPI_PREFIX') || define('TAOH_CACHEAPI_PREFIX', TAOH_CACHE_PREFIX . '/taohapi.php');
defined('TAOH_OBVIOUS_API') || define('TAOH_OBVIOUS_API', TAOH_OBVIOUS_PREFIX . '/api/worklessons');
defined('TAOH_AVATAR_URL') || define('TAOH_AVATAR_URL', TAOH_SITE_URL_ROOT . '/assets/images/');
defined('TAOH_CACHE_CHAT_PROC_URL') || define('TAOH_CACHE_CHAT_PROC_URL', TAOH_CACHE_CHAT_PREFIX . '/taohnetworking.php');
defined('TAOH_CACHE_CHAT_URL') || define('TAOH_CACHE_CHAT_URL', TAOH_CACHE_CHAT_PREFIX . '/taohnetwork.php');
defined('TAOH_LIVE_NOW_URL') || define('TAOH_LIVE_NOW_URL', TAOH_CDN_PREFIX . '/assets/livenow.php');
defined('TAOH_CONNECT_URL') || define('TAOH_CONNECT_URL', TAOH_CACHE_CONNECT_PREFIX . '/taohconnect.php');

defined('TAOH_CACHE_STORE') || define('TAOH_CACHE_STORE', TAOH_CACHE_PREFIX . '/taoh_store.php');
defined('TAOH_CACHEOPS_PREFIX') || define('TAOH_CACHEOPS_PREFIX', TAOH_CACHE_PREFIX . '/cacheops.php');
defined('TAOH_SERVER_NAME') || define('TAOH_SERVER_NAME', ($_SERVER['SERVER_NAME']));
defined('TAOH_CDN_ASSETS') || define('TAOH_CDN_ASSETS', TAOH_CDN_PREFIX . '/assets');
//defined('TAOH_SITE_CONFIG') || define('TAOH_SITE_CONFIG', TAOH_CDN_PREFIX . "/app/club/config");

defined('TAOH_SITE_CAPTCHA') || define('TAOH_SITE_CAPTCHA', TAOH_CDN_PREFIX . "/captcha/code");
defined('TAOH_SITE_CAPTCHA_VERIFY') || define('TAOH_SITE_CAPTCHA_VERIFY', TAOH_CDN_PREFIX . "/captcha/verify");
//defined('TAOH_SITE_CAPTCHA_VERIFY') || define('TAOH_SITE_CAPTCHA_VERIFY', TAOH_CDN_PREFIX . "/func/ops/notify");
defined('TAOH_SITE_PING') || define('TAOH_SITE_PING', TAOH_CDN_PREFIX . "/func/ops/ka");
defined('TAOH_SITE_MAPN') || define('TAOH_SITE_MAPN', TAOH_CDN_PREFIX . "/mapn");
defined('TAOH_CONTENT_CATEGORY') || define('TAOH_CONTENT_CATEGORY', TAOH_CDN_PREFIX . "/assets/category.php");
defined('TAOH_SITE_STATS') || define('TAOH_SITE_STATS', TAOH_CDN_PREFIX . "/app/club/stats");
defined('TAOH_SITE_CDN_TAO_GET') || define('TAOH_SITE_CDN_TAO_GET', TAOH_CDN_PREFIX . "/app/tao/tao.php");
defined('TAOH_SITE_ADS') || define('TAOH_SITE_ADS', TAOH_CDN_PREFIX . "/app/club/ads/");
defined('TAOH_CDN_FIND') || define('TAOH_CDN_FIND', TAOH_CDN_PREFIX . "/api/find.php");
defined('TAOH_CDN_ADS') || define('TAOH_CDN_ADS', TAOH_CDN_PREFIX . "/app/ad.php");
defined('TAOH_SITE_XML_GET') || define('TAOH_SITE_XML_GET', TAOH_API_PREFIX . "/scripts/getclubxml.php");
defined('TAOH_SITE_WEMET_GET') || define('TAOH_SITE_WEMET_GET', TAOH_WEMET_PREFIX . "/api");
defined('TAOH_SITE_SCRIPT_FIND') || define('TAOH_SITE_SCRIPT_FIND', TAOH_API_PREFIX . "/scripts/find.php");
defined('TAOH_SITE_USER_DELETE') || define('TAOH_SITE_USER_DELETE', TAOH_API_PREFIX . "/infofetch.user.delete");
defined('TAOH_INFOFETCH_GET') || define('TAOH_INFOFETCH_GET', TAOH_API_PREFIX . "/infofetch.get");
defined('TAOH_USER_ACCOUNT_TEMP') || define('TAOH_USER_ACCOUNT_TEMP', TAOH_API_PREFIX . "/account.temps");

defined('TAOH_SITE_CHAT') || define('TAOH_SITE_CHAT', TAOH_API_PREFIX . "/chat.get");
defined('TAOH_SITE_CHATS') || define('TAOH_SITE_CHATS', TAOH_API_PREFIX . "/chat.chats.get");
defined('TAOH_SITE_CHATS_POST_API') || define('TAOH_SITE_CHATS_POST_API', "chat.chats.post");
defined('TAOH_SITE_CHATS_POST') || define('TAOH_SITE_CHATS_POST', TAOH_API_PREFIX . "/" . TAOH_SITE_CHATS_POST_API);
defined('TAOH_SITE_TIPS') || define('TAOH_SITE_TIPS', TAOH_API_PREFIX . "/core.tips.get");
defined('TAOH_SITE_CONTENT_GET') || define('TAOH_SITE_CONTENT_GET', TAOH_API_PREFIX . "/core.content.get");
defined('TAOH_SITE_VOTE') || define('TAOH_SITE_VOTE', TAOH_API_PREFIX . "/core.content.vote");
defined('TAOH_SITE_READS') || define('TAOH_SITE_READS', TAOH_API_PREFIX . "/reads.get.reads");

defined('TAOH_XML_URL') || define('TAOH_XML_URL', TAOH_SITE_URL_ROOT . "/xml");
defined('TAOH_GO_URL') || define('TAOH_GO_URL', TAOH_SITE_URL_ROOT . "/go");
defined('TAOH_SANDBOX_URL') || define('TAOH_SANDBOX_URL', TAOH_SITE_URL_ROOT . "/sb");
defined('TAOH_NOTIFICATION_URL') || define('TAOH_NOTIFICATION_URL', TAOH_SITE_URL_ROOT . "/notifications");
defined('TAOH_ACTION_URL') || define('TAOH_ACTION_URL', TAOH_SITE_URL_ROOT . "/actions");
defined('TAOH_REFERRAL_URL') || define('TAOH_REFERRAL_URL', TAOH_SITE_URL_ROOT . "/referral");
defined('TAOH_SHARE_REFERRAL_URL') || define('TAOH_SHARE_REFERRAL_URL', TAOH_SITE_URL_ROOT . "/go/ref");
defined('TAOH_DASHBOARD_URL') || define('TAOH_DASHBOARD_URL', TAOH_SITE_URL_ROOT . "/dash");
defined('TAOH_LOGIN_URL') || define('TAOH_LOGIN_URL', TAOH_SITE_URL_ROOT . "/login");
defined('TAOH_LOGOUT_URL') || define('TAOH_LOGOUT_URL', TAOH_SITE_URL_ROOT . "/logout");
defined('TAOH_FAQ_URL') || define('TAOH_FAQ_URL', TAOH_SITE_URL_ROOT . "/faq");

defined('TAOH_ABOUT_URL') || define('TAOH_ABOUT_URL', TAOH_SITE_URL_ROOT . "/about");
defined('TAOH_LOBBY_URL') || define('TAOH_LOBBY_URL', TAOH_SITE_URL_ROOT . "/lobby");
defined('TAOH_ROOM_URL') || define('TAOH_ROOM_URL', TAOH_SITE_URL_ROOT . "/room");
defined('TAOH_READS_URL') || define('TAOH_READS_URL', TAOH_SITE_URL_ROOT . "/learning");
defined('TAOH_NEWSLETTER_URL') || define('TAOH_NEWSLETTER_URL', TAOH_SITE_URL_ROOT . "/learning/newsletter");
defined('TAOH_CANDY_URL') || define('TAOH_CANDY_URL', TAOH_SITE_URL_ROOT . "/learning/candy");
defined('TAOH_FLASHCARD_URL') || define('TAOH_FLASHCARD_URL', TAOH_SITE_URL_ROOT . "/learning/flashcard");
defined('TAOH_OBVIOUS_URL') || define('TAOH_OBVIOUS_URL', TAOH_SITE_URL_ROOT . "/learning/obviousbaba");
defined('TAOH_TIPS_URL') || define('TAOH_TIPS_URL', TAOH_SITE_URL_ROOT . "/learning/tips");
defined('TAOH_SETTINGS_URL') || define('TAOH_SETTINGS_URL', TAOH_SITE_URL_ROOT . "/settings/" . TAOH_MY_NOW_CODE);
defined('TAOH_DASH_URL') || define('TAOH_DASH_URL', TAOH_SITE_URL_ROOT . "/dash");
// FOR LEARNING PLATFORM
defined('TAOH_READS_LP_URL') || define('TAOH_READS_LP_URL', TAOH_SITE_URL_ROOT . '/home_lp');
if (!defined('TAOH_LP_SITE_URL_FULL_SECRET')) {
    $url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    define('TAOH_LP_SITE_URL_FULL_SECRET', hash('crc32', $url . '/'));
}

if (defined('TAOH_API_TOKEN')) {
    define('TAOH_SITE_OPSTOKEN', TAOH_CDN_PREFIX . "/func/opstoken/" . TAOH_API_TOKEN);
    define('TAOH_SITE_TAO_GET', TAOH_API_PREFIX . "/tao.get?mod=tao&token=" . TAOH_API_TOKEN);
    define('TAOH_USER_CONFIG', TAOH_API_PREFIX . "/users.tao.get?mod=tao_tao&token=" . TAOH_API_TOKEN);
}

defined('TAOH_JOBS_SCOUT_ENABLE') || define('TAOH_JOBS_SCOUT_ENABLE', FALSE);
defined('TAOH_OB_BOT_ENABLE') || define('TAOH_OB_BOT_ENABLE', TRUE);
defined('TAOH_SIDEKICK_BOT_ENABLE') || define('TAOH_SIDEKICK_BOT_ENABLE', TRUE);
defined('TAOH_MESSAGING_ENABLE') || define('TAOH_MESSAGING_ENABLE', TRUE);
defined('TAOH_NEWSLETTER_ENABLE') || define('TAOH_NEWSLETTER_ENABLE', TRUE);
defined('TAOH_AUTO_NEWSLETTER_ENABLE') || define('TAOH_AUTO_NEWSLETTER_ENABLE', TRUE);


defined('TAOH_ENABLE_SEPARATE_EMPLOYER') || define('TAOH_ENABLE_SEPARATE_EMPLOYER', TRUE);

defined('TAOH_ENABLE_OBVIOUSBABA') || define('TAOH_ENABLE_OBVIOUSBABA', false);
defined('TAOH_ENABLE_SIDEKICK') || define('TAOH_ENABLE_SIDEKICK', false);
defined('TAOH_ENABLE_JUSASK') || define('TAOH_ENABLE_JUSASK', false);

defined('TAOH_FOOTER_MENU_ARRAY') || define('TAOH_FOOTER_MENU_ARRAY', '');


defined('TAOH_EVENTS_APP_DETAILS') || define('TAOH_EVENTS_APP_DETAILS', [
    "name" => "Events",
    "name_slug" => "Events",
    "slug" => "events",
    "short" => "Connect, Engage And Participate in Global Career Events",
    "desc" => "Platform for globally connected events to empower workers to build a successful network through learning and growth events.",

]);
defined('TAOH_ASKS_APP_DETAILS') || define('TAOH_ASKS_APP_DETAILS', [
        "name" => "Asks",
        "slug" => "asks",
        "name_slug" => "Asks",
        "short" => "Get Your Asks Answered And Chat About Skills, Roles, & Organizations",
        "desc" => "Get answers to the most pressing questions and join the askers' community to build success, network, reputation, and growth.",
    ]
);

defined('TAOH_JOBS_APP_DETAILS') || define('TAOH_JOBS_APP_DETAILS', [
        "name" => "Jobs",
        "slug" => "jobs",
        "name_slug" => "Jobs",
        "short" => "Job Posting With Direct Chat With Recruiters So Best Hiring Happens",
        "desc" => "Platform with the ability to engage seekers/recruiters with direct chat to increase the quality and success of hiring.",
    ]
);

defined('TAOH_club_APP_DETAILS') || define('TAOH_club_APP_DETAILS', [
        "name" => "club [by TAO.ai]",
        "name_slug" => "club",
        "slug" => "club",
        "short" => "Connected Global Working Community, So We Grow Together",
        "desc" => "World's largest career development platform with jobs, career events and development resources to empower connected workforce.",]
);

defined('TAOH_CLUB_APP_DETAILS') || define('TAOH_CLUB_APP_DETAILS', [
        "name" => "Clubs",
        "name_slug" => "Clubs",
        "slug" => "club",
        "short" => "Connected Global Working Community, So We Grow Together",
        "desc" => "World's largest career development platform with jobs, career events and development resources to empower connected workforce.",]
);

defined('SUPERADMIN_FNAME') || define('SUPERADMIN_FNAME', 'Super');
defined('SUPERADMIN_LNAME') || define('SUPERADMIN_LNAME', 'Admin');
defined('SUPERADMIN_AVATAR') || define('SUPERADMIN_AVATAR', 'avatar_def');
defined('SUPERADMIN_AVATAR_IMG') || define('SUPERADMIN_AVATAR_IMG', '');
defined('ENABLE_CUSTOM_ROOM') || define('ENABLE_CUSTOM_ROOM', 1);
defined('TAOH_LOADER_GIF') || define('TAOH_LOADER_GIF', TAOH_CDN_PREFIX . '/assets/wertual/images/taoh_loader.gif');


defined('TAOH_AJAX_URL') || define('TAOH_AJAX_URL', TAOH_SITE_URL_ROOT . '/ajax?uslo=2');
defined('TAOH_DASH_AJAX_URL') || define('TAOH_DASH_AJAX_URL', TAOH_DASH_PREFIX . '/ajax?uslo=2');

defined('TAOH_AJAX_SECRET') || define('TAOH_AJAX_SECRET', TAOH_API_SECRET);

defined('EVENT_DEMO_SITE') || define('EVENT_DEMO_SITE', 0);

defined('NETWORKING_VERSION') || define('NETWORKING_VERSION', 'mini');//5



defined('SIDEKICK_CHANNEL_ENABLE') || define('SIDEKICK_CHANNEL_ENABLE', false);

defined('TAOH_FOOTER_BANNER_AD') || define('TAOH_FOOTER_BANNER_AD', true);

/*===============COMPILED CONFIG VARS STOP================*/

defined('ORGANIZER_CHANNEL_ENABLE') || define('ORGANIZER_CHANNEL_ENABLE', true);

/*===============LIVE NOW URL================*/
defined('TAOH_LIVE_NOW_URL') || define('TAOH_LIVE_NOW_URL', TAOH_CDN_PREFIX . '/assets/livenow.php');

/* PREFIX */
//defined('TAOH_AVATAR_URL') || define( 'TAOH_AVATAR_URL', TAOH_OPS_PREFIX.'/' );


/* cachet3.tao.ai for API, Metrics, Logs related issues, SESSION to redis features and cachet4.tao.ai for networking and chat related features. */

/* array( taohcode => TAOH_OPSKEY, token => USERTOKEN ops => 'set' or 'get’ or 'remove', key => KEYNAME, value => VALUE ARRAY tts => any tts )
   get for fetch, post for remove / set
 */

//defined('TAOH_SITE_LOGO_2') || define( 'TAOH_SITE_LOGO_2', file_get_contents(TAOH_OPS_PREFIX.'/images/calendar', false, stream_context_create(array( "ssl"=>array( "verify_peer"=>false, "verify_peer_name"=>false,),))));


/* PREFIX */


/* WEBSITE HEALTH CHECK */


/* TAOH DEFAULTS */


// APPS FLAGS]


// FOR JUSASK CHATBOTS


defined('TAOH_TAG_CATEGORY') || define('TAOH_TAG_CATEGORY', $config_data_tag_category ?? []);
defined('TAOH_TAG_CATEGORY_FORM') || define('TAOH_TAG_CATEGORY_FORM', $config_data_tag_category_form ?? []);
defined('TAOH_DIRECTORY_FLAGS_TO_SHOW') || define('TAOH_DIRECTORY_FLAGS_TO_SHOW', $config_data_directory_flags_to_show ?? []);

defined('TAOH_INDUSTRY_CATEGORIES') || define('TAOH_INDUSTRY_CATEGORIES', $config_data_industry_categories ?? []);
defined('TAOH_ROLE_TYPES') || define('TAOH_ROLE_TYPES', $config_data_role_types ?? []);

defined('TAOH_NTW_DEFAULT_CHANNELS') || define('TAOH_NTW_DEFAULT_CHANNELS', $config_data_ntw_default_channels ?? []);

defined('TAOH_NTW_WELCOME_MESSAGES') || define('TAOH_NTW_WELCOME_MESSAGES', $config_data_ntw_welcome_messages ?? []);

define('PROFESSIONAL_HOBBIES', [
    // Analytical & Strategic
    'chess_board'           => 'Chess or board games',
    'brain_teasers'         => 'Puzzle-solving / Brain teasers',
    'investing'             => 'Investing or trading',
    'data_viz'              => 'Data visualization / personal dashboards',

    // Team-Oriented & Social
    'volunteering'          => 'Volunteering / Community work',
    'mentoring'             => 'Mentoring or coaching',
    'public_speaking'       => 'Public speaking / Toastmasters',
    'hosting_events'        => 'Hosting meetups or webinars',

    // Learning & Growth
    'reading_books'         => 'Reading non-fiction or industry books',
    'learning_languages'    => 'Learning new languages (spoken or programming)',
    'online_courses'        => 'Taking online courses (e.g., Coursera, Udemy)',
    'attending_conferences' => 'Attending conferences or workshops',

    // Creative & Techie
    'blogging'              => 'Blogging or technical writing',
    'ui_ux_design'          => 'UX/UI design side projects',
    'app_dev'               => 'Building apps/tools for fun',
    'photography'           => 'Photography / Videography',

    // Culturally Aware
    'travel_learning'       => 'Traveling with a focus on local learning',
    'world_history'         => 'Studying world history or cultures',
    'cooking'               => 'Learning cooking from different cuisines',

    // Wellness & Balance
    'meditation'            => 'Yoga / Meditation',
    'fitness'               => 'Fitness (running, CrossFit, cycling)',
    'hiking'                => 'Hiking or nature exploration',
    'journaling'            => 'Journaling / Bullet journaling'
]);

defined('TAOH_LIKE_ENABLE') || define('TAOH_LIKE_ENABLE', 0);
defined('TAOH_LINK_CANONICAL_URL_ENABLE') || define('TAOH_LINK_CANONICAL_URL_ENABLE', 0);
defined('TAOH_DEV_SITE') || define('TAOH_DEV_SITE', 0);
defined('TAOH_LINK_CANONICAL_URL_ENABLE') || define('TAOH_LINK_CANONICAL_URL_ENABLE', 0);


defined('TAOH_SPEEDNETWORKING_ENABLE') || define('TAOH_SPEEDNETWORKING_ENABLE', 1); 
define('DOJO_NETWORKING_MESSAGE1',[
        [
        'id' => 'no_message_in_15_min',
        'message' => 'Why not post a message in the channel so others can discover and engage?',
        'conditions' => [
            'message_posted' => true,
            'live_users_gte' => 10
        ]
    ],
    [
        'id' => 'no_one_on_one_started',
        'message' => 'Seeing a bunch of profiles from different groups. Why not start a one-on-one conversation?',
        'conditions' => [
            'one_on_one_started' => true,
            'other_profiles_gt' => 5,
            'live_users_gte' => 10
        ]
    ],
    [
        'id' => 'employer_no_job_posted',
        'message' => 'Maybe a good idea to post a job and share the link.',
        'conditions' => [
            'user_type' => 'Employer',
            'job_posted' => true,
            'live_users_gte' => 10
        ]
    ],
    [
        'id' => 'employer_job_not_shared',
        'message' => 'Why not make the group aware of your posted job so they can visit it?',
        'conditions' => [
            'user_type' => 'Employer',
            'job_posted' => true,
            'job_posted_within_days' => 15,
            'job_shared' => false,
            'live_users_gte' => 10
        ]
    ]
]);

define('DOJO_NETWORKING_MESSAGE',[
    [
        "name" => "Post a message in the channel so others can discover and engage?",
        "expectations" => [
            "message_posted_within_15min" => true,
            //"live_users_gte" => 1 //10
        ]
    ],
    [
        "name" => "Seeing a bunch of profiles from different groups. Why not start a one-on-one conversation?",
        "expectations" => [
            "one_on_one_started" => true,
            //"other_profiles_gt" => 5,
            "live_users_gte" => 1 //10
        ]
    ],
    [
        "name" => "Maybe a good idea to post a job and share the link.",
        "expectations" => [
            "user_type" => "Employer",
            "job_posted" => false,
            "live_users_gte" => 1 //10
        ]
    ],
    [
        "name" => "Why not make the group aware of your posted job so they can visit it?",
        "expectations" => [
            "user_type" => "Employer",
            "job_posted_within_days" => 15,
            "job_shared" => false,
            "live_users_gte" => 1// 10
        ]
    ]
]);
defined('TAOH_DOJO_SUGGESTION_ENABLE') || define('TAOH_DOJO_SUGGESTION_ENABLE', 0);
defined('TAOH_DOJO_SUGGESTION_TIMELIMIT') || define('TAOH_DOJO_SUGGESTION_TIMELIMIT', 30000);
defined('TAOH_DOJO_TRACKER_ENABLE') || define('TAOH_DOJO_TRACKER_ENABLE', 0);
 defined('NETWORKING_DOJO_SUGGESTION') || define('NETWORKING_DOJO_SUGGESTION', false);
 

defined('TAOH_CHAT_NET_URL') || define('TAOH_CHAT_NET_URL', TAOH_CACHE_CHAT_PREFIX . '/taoh_net.php');

defined('NETWORKING_2_0') || define('NETWORKING_2_0', true);
defined('NETWORKING_3_0') || define('NETWORKING_3_0', false);
defined('HIDE_REORT_AN_ISSUE') || define('HIDE_REORT_AN_ISSUE' , 1); 


define( 'TAOH_CACHE_CHAT_PREFIX_MOD', TAOH_CACHE_CHAT_PREFIX);


defined('TAO_TABLES_NAME') || define('TAO_TABLES_NAME' , 'tables.im');
defined('TAO_TABLES_URL') || define('TAO_TABLES_URL' , 'https://tables.im'); 
defined('TAO_TABLES_KEYWORD') || define('TAO_TABLES_KEYWORD' , TAOH_SITE_NAME_SLUG); 
defined('TAOH_TABLE_REDIS_URL') || define('TAOH_TABLE_REDIS_URL' , TAOH_CACHE_CHAT_PREFIX_MOD . '/mos_red_apps.php');
defined('TAOH_TABLE_VERSION') || define('TAOH_TABLE_VERSION' , 'tao');
defined('TAOH_COMMENTS_VERSION') || define('TAOH_COMMENTS_VERSION' , 'prod');

defined('TAOH_TABLES_DISCUSSION_SHOW') || define('TAOH_TABLES_DISCUSSION_SHOW' ,TRUE);
defined('TAOH_COMMENTS_SHOW') || define('TAOH_COMMENTS_SHOW' , TRUE);

defined('TAOH_MASTER_NETWORKING_URL') || define('TAOH_MASTER_NETWORKING_URL' , 'https://labs.tao.ai/org/master-networking');
defined('TAOH_COMMENTS_JS') || define('TAOH_COMMENTS_JS' , 'https://labs.tao.ai/assets/cdn/comments/taoh_function_toolkit.js');
defined('TAOH_COMMENTS_CSS') || define('TAOH_COMMENTS_CSS' , 'https://labs.tao.ai/assets/cdn/comments/taoh_function_toolkit.css');


defined('TAOH_RSVP_ROOMSLUG_SPLIT_COUNT') || define('TAOH_RSVP_ROOMSLUG_SPLIT_COUNT' , '10000');

?>
