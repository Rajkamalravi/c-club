<?php
if ( strpos( $_SERVER['REQUEST_URI'], 'ajax' ) === false ){
    taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
}
define('TAOH_CURR_APP_SLUG', TAOH_MESSAGEPAGE_NAME);

$slug = taoh_parse_url(1);
$keyslug = taoh_parse_url(2);

switch ($slug) {
    case 'ajax':
    case 'message_ajax':
        include_once('message_ajax.php');  break;
    case 'dm':
        if($keyslug !='')
        include_once(TAOH_PLUGIN_PATH.'/core/club/networking1.php');
        else
        include_once('club.php');
    break;
    default:
        include_once('message.php'); break;
}
?>