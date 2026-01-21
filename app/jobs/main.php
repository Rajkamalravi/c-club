<?php

//Jobs
if((!TAOH_JOBS_ENABLE)){
	taoh_redirect(TAOH_SITE_URL_ROOT);
}

if ( ! defined( 'TAOH_APP_SLUG' ) ) {
	define( 'TAOH_APP_SLUG', 'jobs' );
}
define('TAOH_CURR_APP_SLUG', 'jobs');
define('TAOH_CURR_APP_URL', TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG);
define('TAOH_CURR_APP_IMAGE', TAOH_CDN_PREFIX.'/app/'.TAOH_CURR_APP_SLUG.'/images/'.TAOH_CURR_APP_SLUG.'.png');
define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_CDN_PREFIX.'/app/'.TAOH_CURR_APP_SLUG.'/images/'.TAOH_CURR_APP_SLUG.'_sq.png');


//define('TAOH_JOBS_JOB_GET', TAOH_API_PREFIX."/jobs.job.get");
define('TAOH_JOBS_URL', TAOH_SITE_URL_ROOT."/".TAOH_CURR_APP_SLUG);



$current_app = taoh_parse_url(0);
$action = taoh_parse_url(1);
$goto = taoh_parse_url(2);
if(taoh_parse_url(2) && taoh_parse_url(3)){
$goto = taoh_parse_url(2).'/'.taoh_parse_url(3);
	
}
if(taoh_parse_url(4)){
	$id = '/'.taoh_parse_url(4);
		
}
if($goto == 'stlo'){
	$goto = '';

}
//echo $action;echo "=====".$goto;die();
include "functions.php";

include "apply_form_fields.php";
//require_once(TAOH_PLUGIN_PATH.'/core/form_fields.php');
//https://dev.unmeta.net/hires/jobs/scouts-signup-employer
switch ($action) {
	case 'd':
		if(isset($_GET['q']) && $_GET['q'] =='main'){
			if( taoh_user_is_logged_in() || 1 ) {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/jobs.php");
			} else {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
			}
		}
		else
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/jobs_detail.php");
		break;
	case 'mobile':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/jobs_mobile.php");
		break;
	case 'scout':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/scout.php");
		break;
	case 'club':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/club.php");
		break;
	case 'about':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
		break;
	case 'dash':
		$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app;
		//echo $url ;die();
      	taoh_redirect($url); exit();
		break;
	case 'post':
		$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/post";
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'professional-dashboard':
		$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/professional-dashboard/".$goto.$id;
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'scouts':
		$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/dashboard";
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'scout-dashboard':
		if(taoh_user_is_logged_in())
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/scout-dashboard/".$goto.$id;
		else
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/0/u/loc/".$current_app."/scouts-signup";
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'employer-dashboard':
		if(taoh_user_is_logged_in())
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/employer-dashboard/".$goto.$id;
		else
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/0/u/loc/".$current_app."/scouts-signup-employer";
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'scouts-signup-employer':
		
		if(taoh_user_is_logged_in()){
		
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/scouts-signup-employer";
		}else{
			
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/0/u/loc/".$current_app."/scouts-signup-employer";
		}
		//echo $url ;die();	
		taoh_redirect($url); exit();
		break;
	case 'scouts-signup-professional':

	
		if(taoh_user_is_logged_in())
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/scouts-signup-professional";
		else
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/0/u/loc/".$current_app."/scouts-signup-professional";

			taoh_redirect($url); exit();
		break;
	case 'scouts-signup':
		if(taoh_user_is_logged_in())
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/scouts-signup";
		else
			$url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/0/u/loc/".$current_app."/scouts-signup";
		//echo $url ;die();
			taoh_redirect($url); exit();
		break;
	case 'new_design':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/_design.php");
		break;
	default:
		if(taoh_parse_url(1) == "chat") {
			if( ! taoh_user_is_logged_in() ) {
				return  taoh_redirect(TAOH_LOGIN_URL.'/jobs');
			}
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/chat.php");
		}else if(taoh_parse_url(1) == "chat1") {
			if( ! taoh_user_is_logged_in() ) {
				return  taoh_redirect(TAOH_LOGIN_URL.'/jobs');
			}
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/chat_new.php");
		} else {
			if( taoh_user_is_logged_in() || 1 ) {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/jobs.php");
			} else {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
			}
		}
		break;
}
die();

?>
