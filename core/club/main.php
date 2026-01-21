<?php

$slug = taoh_parse_url(1);
$page = taoh_parse_url(2);

if (strpos($_SERVER['REQUEST_URI'], 'ajax') === false) {
    taoh_add_var_to_url('noca', TAOH_MY_NOW_CODE);
}

defined('TAOH_CURR_APP_SLUG') || define('TAOH_CURR_APP_SLUG', 'club');

switch ($slug) {
    case 'rooms':
        if (isset($_GET['admin']) && $_GET['admin'] == 'createroom') {
            require_once(TAOH_PLUGIN_PATH . '/core/form_fields.php');
            include_once('networking_form.php');
        } else {
            include_once('groups.php');
        }
        break;
    case 'groups':
        include_once('groups.php');
        break;

    case 'classifieds':
        include_once('classifieds.php');
        break;
    case 'zerodayemployer':
        include_once('zerodayemployer.php');
        break;
    case 'lobby':
        include_once('lobby.php');
        break;
    case 'announcements':
        $footer_tracking_link = 'club_announcements';
        include_once('announcements.php');
        break;
    case 'news_feed':
        $footer_tracking_link = 'club_news_feed';
        include_once('news_feed.php');
        break;
    case 'd':
        include_once('news_feed_detail.php');
        break;
    case 'alum':
        include_once('alum_landing.php');
        break;
    case 'the_start_club':
        include_once('the_start_club.php');
        break;
    case 'flipper':
        include_once('flipper.php');
        break;
    case 'employer_branding':
        include_once('employer_branding.php');
        break;
    case 'employer_branding_form':
        include_once('employer_branding_form.php');
        break;
    case 'profile':
        include_once('profile.php');
        break;
    case 'live_url':
        include_once('loadLiveNowData.php');
        break;
    case 'networking':
        $footer_tracking_link = 'club_networking';
        include_once(TAOH_CORE_PATH . "/club/adapter.php");
        break;
    case 'room':
    case 'custom-room':
    case 'forum':
    case 'livenow':
        $footer_tracking_link = 'networking';
        if (NETWORKING_3_0) {
            include_once('networking_3_0.php');
        } elseif (NETWORKING_VERSION == 1) {
            include_once('networking1.php');
        } elseif (NETWORKING_VERSION == 4) {
            include_once('networking4.php');
        } elseif (NETWORKING_VERSION == 5) {
            include_once('networking5.php');
        }elseif (NETWORKING_VERSION == 'mini') {
            include_once('networking5_kal.php');
        }  elseif (NETWORKING_VERSION == 'passive') {
            include_once(TAOH_PLUGIN_PATH . '/core/club/networking5_passive.php');
        } else {
            include_once('networking1.php');
        }
        break;
    default:
        if (TAOH_CLUBS_ENABLE)
            include_once('club.php');
        else if (TAOH_EVENTS_ENABLE)
            taoh_redirect(TAOH_SITE_URL_ROOT . '/events');
        else if (TAOH_JOBS_ENABLE)
            taoh_redirect(TAOH_SITE_URL_ROOT . '/jobs');
        else if (TAOH_ASKS_ENABLE)
            taoh_redirect(TAOH_SITE_URL_ROOT . '/asks');
        break;
}