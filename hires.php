<?php
/**
 * @package Hires
 */
/*
Plugin Name: Hires
Plugin URI: https://tao.ai/
Description: hires .
Version: 1.0.1
Requires at least: 5.0
Requires PHP: 8.2
Author: Tao.ai
Author URI: https://tao.ai/
License: GPLv2 or later
Text Domain: hires
*/

/*hires.php
This file ment to do the initial setup for the app to work
Ex: register site, Set debug, health check, session setup, cookie setup and set global variables
Do not redirect to somewhere
Do not create or redefine the CONSTANT. use config.php
Do not create functions use function.php instead
*/

//if ( stristr( TAOH_PLUGIN_PATH, TAOH_WERTUAL_SLUG ) ){
//  echo time()." 1:<br />";
//if (session_status() === PHP_SESSION_NONE) { session_reset(); }
if (session_status() === PHP_SESSION_NONE) { session_start(); }

//$_SESSION = array();
//echo'<pre>----aaa------';print_r($_SESSION);

//echo time()." 2:<br />";
if ( ! defined ('TAOH_SITE_URL') ) {
  require_once('config.php'); //This file only for main config should not dependent on any files
 // echo'<pre>----bbb------';print_r($_SESSION);
  require_once('helper_functions.php');  //This file should not dependant on any files
  require_once('function.php'); //this file is depends on config and helper functions
  if ( file_exists( TAOH_PLUGIN_PATH.'/debug_function.php' ) ){
    require_once('debug_function.php');
  }
  require_once('core/ajax.php');

  require_once('core/networkingajax.php');
  require_once('core/networkingredis_ajax.php');
}
//echo'<pre>----cccc------';print_r($_SESSION);
if ( ! defined( 'TAOH_API_SECRET' ) || ! TAOH_API_SECRET ){
  echo "
  <h3>Claim your secret key, and add it to your config.php file</h3>
  <p>Email following information to info@tao.ai<br />
  Subject: Requesting Secret Key<br />
  Add following information in your email:<br />
  1. Your First Name<br />
  2. Your Last Name<br />
  3. Your website address<br />
  4. Your primary associated email (This email will get the secret key)<br />
  5. Your Website Title<br />
  6. Your Website Desription<br />
  7. Your Website square logo URL [Typically used in FAVIcon 128x128]<br /><br />

  Once we receive this, we will email your secret key that you need to add to your config.php file.<br />
  </p>";
  taoh_exit();

}

if ( ! defined( 'TAOH_SITE_LOGO' ) || ! defined( 'TAOH_SITE_TITLE' ) || ! TAOH_SITE_LOGO || ! TAOH_SITE_TITLE ){
  echo "<h3>Complete config.php website section</h3>
  <p>Open config.php, and answer all the fields between website section.</p>";
  taoh_exit();
}
//print_r($_SERVER);taoh_exit();
//Storing site information to session so function.php can get the information from session

// Clean up old cache files in general folder in cache directory
if ( defined( 'TAOH_API_TOKEN' ) && TAOH_API_TOKEN ){
  $time_threshold = 21600;
  $directory = TAOH_PLUGIN_PATH."/cache";
  taoh_clean_old_file_cron( $directory, $time_threshold );

  if ( strpos( $_SERVER['REQUEST_URI'], 'ajax' ) === false && strpos( $_SERVER['REQUEST_URI'], 'action' ) === false && strpos( $_SERVER['REQUEST_URI'], 'rsvp' ) === false && strpos( $_SERVER['REQUEST_URI'], 'networking' ) === false && strpos( $_SERVER['REQUEST_URI'], 'ss' ) === false  && strpos( $_SERVER['REQUEST_URI'], 'flashcard' ) === false){
    taoh_check_and_refresh_page_cache();
  }
}

require_once('core/widgets/widget_functions.php');

if ( ! defined ( 'TAOH_URL_PATH' ) ) {
  $temp = '/'.TAOH_PLUGIN_PATH_NAME;
  if ( defined( 'TAOH_WERTUAL_ROOT_SUBDIRECTORY' ) && strlen(TAOH_WERTUAL_ROOT_SUBDIRECTORY) >= 2 ) {$temp = '/'.trim( TAOH_WERTUAL_ROOT_SUBDIRECTORY, '/' ).'/'.TAOH_PLUGIN_PATH_NAME;}
  define( 'TAOH_URL_PATH', $temp );
}
//echo'<pre>';print_r($_SESSION);taoh_exit();
//include core/main.php to render initial page
//taoh_beam();
include_once('core/main.php');
taoh_exit();
