<?php

if((!TAOH_READS_ENABLE)){
	taoh_redirect(TAOH_SITE_URL_ROOT);
}
//echo taoh_parse_url(0); die();
if(taoh_parse_url(0) == 'reads'){
    $uri = explode('/reads/',$_SERVER['REQUEST_URI'])[1];
    $url = TAOH_SITE_URL_ROOT.'/learning/'.$uri;
    //echo $url; die();
    header("Location: ".$url);taoh_exit();
    //taoh_redirect(TAOH_SITE_URL_ROOT); die();
}
$action = taoh_parse_url(1);
//echo TAOH_PLUGIN_PATH.'/learning/blog/functions.php'.$_SERVER['REQUEST_URI'];taoh_exit();
include 'functions.php';
include 'widget_functions.php';
//define('TAOH_CURR_APP_SLUG', 'learning');

switch ($action) {
    case 'blog':
        include_once('blog_detail.php');
		break;
    case 'all':
        if (taoh_user_is_logged_in() && taoh_parse_url(2) == 'post'){
            include_once('blog_all_post.php');
        } else {
            return taoh_redirect();
        }
		break;
    case 'post':
        if (isset( $_GET[ 'post' ] ) && $_GET[ 'post' ] == date('Ymd') && taoh_user_is_logged_in()){
            include_once('blog_post.php');
        } else {
            return taoh_redirect();
        }
        break;
	case 'edit':
        if( ! taoh_user_is_logged_in() ) {
            return taoh_redirect();
        }
        include_once('blog_post.php');
		break;
    case 'job':
    case 'jobs':
    case 'job-search':
    case 'work':
    case 'wellness':
        header("Location: " . TAOH_SITE_URL_ROOT . "/learning/");
        taoh_exit();
        break;
    case 'search':
        include_once('search.php');
        break;
	default:
        include_once('reads.php');
		break;
}
die();
?>
