<?php
//echo taoh_parse_url(0); die();
require_once('reads_lp_functions.php');  //This file should not dependant on any files
define('TAOH_APP_SLUG', 'learning');
$action = taoh_parse_url_lp(1);
//echo $action;die;
switch ($action) {
    case 'd':
        include_once('reads_lp_detail.php');
		break;
    case 'search' || 'category':
        include_once('reads_lp_search.php');
        break;
	default:
        include_once('reads_lp.php');
		break;
}
die();
?>
