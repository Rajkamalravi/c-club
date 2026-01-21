<?php

$action = taoh_parse_url(2);
//echo TAOH_PLUGIN_PATH.'/learning/blog/functions.php'.$_SERVER['REQUEST_URI'];taoh_exit();
include 'functions.php';
include 'widget_functions.php';

switch ($action) {
    case 'd':
        include_once('newsletter_detail.php');
		break;
    case 'post':
        if ( isset( $_GET[ 'post' ] ) && $_GET[ 'post' ] == date('Ymd') ){
            include_once('newsletter_post.php');
        } else {
            return taoh_redirect();
        }
		break;
	case 'edit':
        if( ! taoh_user_is_logged_in() ) {
            return taoh_redirect();
        }
        include_once('newsletter_post.php');
		break;
    case 'search':
        include_once('search.php');
        break;
	default:
        include_once('newsletter.php');
		break;
}
die();
?>
