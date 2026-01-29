<?php
$slug = taoh_parse_url(1);
$page = taoh_parse_url(2);

if (strpos($_SERVER['REQUEST_URI'], 'ajax') === false) {
    taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
}

defined('TAOH_CURR_APP_SLUG') || define('TAOH_CURR_APP_SLUG', 'club');

switch ($slug) {
    case 'networking':
        include_once(TAOH_CORE_PATH . "/direct_message/adapter.php");
        break;
    case 'dm':
    default:
        if (NETWORKING_3_0) {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking_3_0.php');
        } elseif (NETWORKING_VERSION == 1) {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking1.php');
        } elseif (NETWORKING_VERSION == 4) {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking4.php');
        } elseif (NETWORKING_VERSION == 5) {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking5.php');
        }
        elseif (NETWORKING_VERSION == 'mini') {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking5_kal.php');
        } elseif (NETWORKING_VERSION == 'passive') {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking5_passive.php');
        } else {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking1.php');
        }
        break;
}