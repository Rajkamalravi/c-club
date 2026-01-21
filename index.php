<?php
/*
  For Static Website
  Index.php
  Here you can include all the dependancies for the hires.php
  Do not create CONSTANT, functions, Global variable here.
  Do not implement the redirect here.
  For Constant use config.php
  For Taoh function use function.php
  For Global variable use hires.php
  For helper static function use helper_functions.php
*/

require_once('config.php'); //This file only for main config should not dependent on any files

//Index
if (defined( 'TAOH_DEBUG' ) && TAOH_DEBUG ){
  ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
} else {
  error_reporting(-1); ini_set('display_errors',0);
}
//print_r($_SERVER);
// echo "TAOH_SITE_URL_ROOT: ".TAOH_SITE_URL_ROOT."<br />";exit();
if ( ! defined( 'TAOH_API_SECRET' ) && ! TAOH_API_SECRET ){
  if (  ! stristr( $_SERVER[ 'REQUEST_URI' ], 'amisetupright' ) ){
    $options = array("ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));
    $arry_key = stream_context_create($options);
    $temp = file_get_contents(TAOH_SITE_URL_ROOT.'/amisetupright', false, $arry_key);
    if ($temp === false || $temp != 1) {
      $temp = $_SERVER[ 'SERVER_SOFTWARE' ];
      if ( stristr( $temp, 'apache' ) ){
        echo "<h3>Your .htaccess is not configured correctly</h3>
        <p>
        1. Go to folder: ".TAOH_PLUGIN_PATH."<br />
        2. Open/Create .htaccess using your favorite editor and add following lines:<br />
        RewriteEngine on<br />
        RewriteCond %{REQUEST_FILENAME} !-d<br />
        RewriteCond %{REQUEST_FILENAME} !-f<br />
        RewriteRule . ".TAOH_SITE_URL_ROOT."/index.php [L]<br />
        3. Save the file and restart apache server, refresh this page to confirm.
        </p>
        ";
        taoh_exit();
      } else if ( stristr( $temp, 'nginx' ) ){
        echo "<h3>Your nginx site config is not configured correctly</h3>
        <p>
        1. Go to folder: your nginx config file<br />
        2. Make sure to edit / add following line:<br />
        location ".TAOH_SITE_URL_ROOT."/ {<br />
          root ".TAOH_PLUGIN_PATH.";<br />
          try_files \$uri \$uri/ $temp/index.php?$args;<br />
        }<br />
        3. Save the file and restart nginx server, refresh this page to confirm.
        </p>";
        taoh_exit();
      }
    }
  } else {
    echo "1";taoh_exit();
  }
}

require_once('helper_functions.php');  //This file should not dependant on any files
require_once('function.php'); //this file is depends on config and helper functions
taoh_health_sync();
if ( file_exists( TAOH_PLUGIN_PATH.'/debug_function.php' ) ){
  require_once('debug_function.php');
} 

if( taoh_is_wp() ) {
  header("Location: ".TAOH_SITE_URL_ROOT); taoh_exit();
}

taoh_cache_cleaner();
if (file_exists(TAOH_SITE_PATH_ROOT . '/hires.php')) {
    require_once(TAOH_SITE_PATH_ROOT . '/hires.php');
}
exit();