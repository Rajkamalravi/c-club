<?php
$current_app = taoh_parse_url(0);
$action = taoh_parse_url(1);

//Asks
if((!TAOH_ASKS_ENABLE)){
	taoh_redirect(TAOH_SITE_URL_ROOT);
}

//define('TAOH_APP_SLUG', 'asks');
if ( ! defined( 'TAOH_APP_SLUG' ) ) {
	define( 'TAOH_APP_SLUG', 'asks' );
}
define('TAOH_CURR_APP_SLUG', 'asks');
define('TAOH_CURR_APP_URL', TAOH_SITE_URL_ROOT.'/'.TAOH_CURR_APP_SLUG);
define('TAOH_ASKS_ASK_GET', TAOH_API_PREFIX."/asks.ask.get");
define('TAOH_ASKS_URL', TAOH_SITE_URL_ROOT."/".TAOH_CURR_APP_SLUG);
define('TAOH_CURR_APP_IMAGE', TAOH_CDN_PREFIX.'/app/'.TAOH_CURR_APP_SLUG.'/images/'.TAOH_CURR_APP_SLUG.'.png');
define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_CDN_PREFIX.'/app/'.TAOH_CURR_APP_SLUG.'/images/'.TAOH_CURR_APP_SLUG.'_sq.png');


include "functions.php";
require_once(TAOH_PLUGIN_PATH.'/core/form_fields.php');

// if ( defined( 'TAOH_API_TOKEN' ) ){
// 	$locn = TAOH_API_PREFIX."/chat.chat.myinfo?mod=".$current_app."&token=".TAOH_API_TOKEN;
// 	$taoh_user_vars[ 'chat' ] = json_decode( taoh_file_get_contents( $locn ) );
// 	$we_chat_vars = $taoh_user_vars[ 'chat' ];
// }

//echo "=========".$action;die();
switch ($action) {
	case 'd':
		if(isset($_GET['q']) && $_GET['q'] =='main'){
			if( taoh_user_is_logged_in() || 1 ) {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks.php");
			} else {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
			}
		}
		else
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks_detail.php");
		break;
	case 'club':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/club.php");
		break;
	case 'chat':
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/chat.php");
			break;
	case 'mobile':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks_mobile.php");
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
	case 'about':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
		break;
	case 'asks_new':
		include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks_new_design.php");
		break;
	default:
		/*if(taoh_parse_url(1) == "chat") {
			if(!taoh_user_is_logged_in()) {
				return  taoh_redirect(TAOH_LOGIN_URL.'/asks');taoh_exit();
			}
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/chat_old.php");
			include_once $url;taoh_exit();
		}else if(taoh_parse_url(1) == "chat1") {
			if(!taoh_user_is_logged_in()) {
				return  taoh_redirect(TAOH_LOGIN_URL.'/asks');taoh_exit();
			}
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/chat_new.php");
			include_once $url;taoh_exit();
		}else {
			if( taoh_user_is_logged_in() ) {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks.php");
			} else {
				include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
			}

		}*/
		if( taoh_user_is_logged_in() || 1 ) {
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/asks.php");
		} else {
			include_once(TAOH_PLUGIN_PATH."/app/".$current_app."/visitor.php");
		}
		break;
}
die();

?>
