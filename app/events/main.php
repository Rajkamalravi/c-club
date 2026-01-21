<?php

//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

//Events
if ((!TAOH_EVENTS_ENABLE)) {
    taoh_redirect(TAOH_SITE_URL_ROOT);
}


defined('TAOH_APP_SLUG') || define('TAOH_APP_SLUG', 'events');

define('TAOH_CURR_APP_SLUG', 'events');
define('TAOH_CURR_APP_URL', TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG);
define('EVENTS_EVENT_GET', TAOH_API_PREFIX . "/events.event.get");
define('EVENTS_RSVP_GET', TAOH_API_PREFIX . "/events.rsvp.get");
define('TAOH_EVENTS_URL', TAOH_SITE_URL_ROOT . "/" . TAOH_CURR_APP_SLUG);
define('TAOH_CURR_APP_IMAGE_SQUARE', TAOH_CDN_PREFIX . '/app/' . TAOH_CURR_APP_SLUG . '/images/' . TAOH_CURR_APP_SLUG . '_sq.png');
define('TAOH_CURR_APP_IMAGE', TAOH_CDN_PREFIX . '/app/' . TAOH_CURR_APP_SLUG . '/images/' . TAOH_CURR_APP_SLUG . '.png');


defined('TAOH_EVENTS_EVENT_UNPUBLISHED') || define('TAOH_EVENTS_EVENT_UNPUBLISHED', 1);
defined('TAOH_EVENTS_EVENT_SUSPENDED') || define('TAOH_EVENTS_EVENT_SUSPENDED', 2);
defined('TAOH_EVENTS_EVENT_EXPIRED') || define('TAOH_EVENTS_EVENT_EXPIRED', 3);
defined('TAOH_EVENTS_EVENT_PUBLISHED') || define('TAOH_EVENTS_EVENT_PUBLISHED', 4);
defined('TAOH_EVENTS_EVENT_ACTIVE') || define('TAOH_EVENTS_EVENT_ACTIVE', 5);
defined('TAOH_EVENTS_EVENT_LIVEABLE') || define('TAOH_EVENTS_EVENT_LIVEABLE', 6);
defined('TAOH_EVENTS_EVENT_EARLY_START') || define('TAOH_EVENTS_EVENT_EARLY_START', 7);
defined('TAOH_EVENTS_EVENT_START') || define('TAOH_EVENTS_EVENT_START', 8);
defined('TAOH_EVENTS_EVENT_STOP') || define('TAOH_EVENTS_EVENT_STOP', 9);

defined('TAOH_EVENTS_RSVP_SUSPENDED') || define('TAOH_EVENTS_RSVP_SUSPENDED', 1);
defined('TAOH_EVENTS_RSVP_NEW') || define('TAOH_EVENTS_RSVP_NEW', 2);
defined('TAOH_EVENTS_RSVP_NOTMATCHED') || define('TAOH_EVENTS_RSVP_NOTMATCHED', 3);
defined('TAOH_EVENTS_RSVP_NOMATCH') || define('TAOH_EVENTS_RSVP_NOMATCH', 4);
defined('TAOH_EVENTS_RSVP_MATCH') || define('TAOH_EVENTS_RSVP_MATCH', 5);
defined('TAOH_EVENTS_RSVP_NOMATCH_LIVE') || define('TAOH_EVENTS_RSVP_NOMATCH_LIVE', 6);
defined('TAOH_EVENTS_RSVP_MATCH_LIVE') || define('TAOH_EVENTS_RSVP_MATCH_LIVE', 7);

if(EVENT_DEMO_SITE)
$current_app ='events';
else
$current_app =  taoh_parse_url(0);
$action = taoh_parse_url(1);
$goto = taoh_parse_url(2);
if(taoh_parse_url(2) && taoh_parse_url(3)){
$goto = taoh_parse_url(2).'/'.taoh_parse_url(3);
}
//echo taoh_parse_url(4);
if(taoh_parse_url(4)){
	$id = '/'.taoh_parse_url(4);
		
}

$param = '';
if(taoh_parse_url(5)){
	$param .= '/'.taoh_parse_url(5);
		
}
if(taoh_parse_url(6)){
	$param .= '/'.taoh_parse_url(6);
		
}

/* if(isset($_GET['social_share']) && $_GET['social_share'] != ''){
    $param = "?social_share=".$_GET['social_share'];
    $param = "/socialshare/1";
} */


if($goto == 'stlo'){
	$goto = '';

}
//echo "=============>".$goto."=======$id====".$id;
//echo "current_app= $current_app; action = $action";taoh_exit();

// if ( defined( 'TAOH_API_TOKEN' ) ){
// 	$locn = TAOH_API_PREFIX."/chat.chat.myinfo?mod=".WE_ACTIVE_SLUG."&token=".TAOH_API_TOKEN;
// 	$taoh_user_vars[ 'chat' ] = json_decode( taoh_file_get_contents( $locn ) );
// 	$we_chat_vars = $taoh_user_vars[ 'chat' ];
// }



//echo $action; die;

include "functions.php";
require_once(TAOH_PLUGIN_PATH . '/core/form_fields.php');
switch ($action) {
    case 'rsvp':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/rsvp.php");
        break;
    case 'add_rsvp':
    case 'edit_rsvp':
    case 'upgrade_rsvp':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        $eventtoken = taoh_parse_url(2);
        $eventtitle = taoh_parse_url(3);
        $encodeCurrentUrl = taoh_parse_url(4);

        $rsvp_type = $action == 'upgrade_rsvp' ? 'rsvp-upgrade' : 'rsvp';

        if (!empty($eventtoken) && !empty($eventtitle)) {
            taoh_redirect(TAOH_SITE_URL_ROOT . '/fwd/ss/' . TAOH_SITE_ROOT_HASH . '/log/1/u/loc/events/' . $rsvp_type . '/' . $eventtoken . '/' . $eventtitle . '/' . $encodeCurrentUrl);
        } else {
            taoh_redirect(TAOH_SITE_URL_ROOT . '/events');
        }
        break;
    case 'confirmation':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/rsvp_confirmation.php");
        break;
    case 'beam':
        if (!taoh_user_is_logged_in()) {
            taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/orgchat.php");
        break;
    case 'eventsticketview':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_ticket_view.php");
        break;
    case 'eventshtmlnewtemplate':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_html_new_template.php");
        break;
    case 'eventshall':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_hall.php");
        break;
    case 'eventshallexhibitors':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_hall_exhibitors.php");
        break;
    case 'eventshallrsvp':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_hall_rsvp.php");
        break;
    case 'newdescpage':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/new_desc_page.php");
        break;
    case 'exhibitors':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_exhibitor_detail_new.php");

        break;
    case 'sponsor':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_sponsor_detail.php");

        break;
    case 'speakers':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_speaker_detail.php");
        break;
    case 'speaker_detail':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_speakder_details.php");
        break;
    case 'speaker':
    case 'speaker_detail_page':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_speaker_page.php");
        break;
    case 'hall':
        // hall_listing_page
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_hall_listing_page.php");
        break;
    case 'swag_wall':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/swag_wall.php");
        break;
    case 'events.tao.ai':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_tao_ai.php");
        break;
    case 'events.tao.ai.new':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_tao_ai_new.php");
        break;
    case 'allevents':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/all_events.php");
        break;
    case 'tao.connect.html':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/tao_connect_html.php");
        break;
    case 'd':
        if (isset($_GET['q']) && $_GET['q'] == 'main') {
            if (taoh_user_is_logged_in() || 1) {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events.php");
            } else {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/visitor.php");
            }
        } else
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_detail.php");
        break;
    case 'dd':
        if (isset($_GET['q']) && $_GET['q'] == 'main') {
            if (taoh_user_is_logged_in() || 1) {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events.php");
            } else {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/visitor.php");
            }
        } else
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_detail_opt.php");
        break;
    case 'about':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/visitor.php");
        break;
    case 'mobile':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_mobile.php");
        break;
    case 'next':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/next.php");
        break;
    case 'status':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/event_status.php");
        break;
    case 'event_tables':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_tables.php");
        break;
    case 'tables':
        $iframe=0;
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/tables.php");
        break;
    case 'tables-iframe':
        $iframe=1;
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/tables.php");
        break;
    case 'dash':
        $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/1/u/loc/" . $current_app;
        //echo $url ;die();
        taoh_redirect($url);
        exit();
        break;
    case 'event_sponsor':    
               
       // $param = "?social_share=".taoh_parse_url(6) ?? '';
    
        $url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/event_sponsor/".$goto.$id.$param;
        //echo $url;die();
        taoh_redirect($url);
        exit();
        break;
    case 'master':           
        $url = TAOH_SITE_URL_ROOT."/fwd/ss/".TAOH_SITE_ROOT_HASH."/log/1/u/loc/".$current_app."/master-room/".$goto.$id;
        //echo $url;die();
        taoh_redirect($url);
        exit();
        break;
    case 'post':
        $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/1/u/loc/" . $current_app . "/post";
        //echo $url ;die();
        taoh_redirect($url);
        exit();
        break;
    case 'club':
//        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/club.php");
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/adapter.php");
        break;
    case 'chat':
        //commented due to view ticket issue. now redirecting to detail page
        /* if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        } */
        if(taoh_parse_url(2) == 'session' || taoh_parse_url(2) == 'exhibitor'){
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/chat_".taoh_parse_url(2).".php");
            break;
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/chat.php");
        break;
    case 'chat-opt':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        if(taoh_parse_url(2) == 'session' || taoh_parse_url(2) == 'exhibitor'){
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/chat_".taoh_parse_url(2).".php");
            break;
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/chat-opt.php");
        break;
    case 'chat1':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/chat_new.php");
        break;
    case 'session_slot':
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_session_form.php");
        break;
    case 'export_rsvp':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/export_rsvp.php");
        break;
    case 'export_raffle_entries':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/export_raffle_entries.php");
        break;
    case 'export_raffle_feedback':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/export_raffle_feedback.php");
        break; 
   
    default:
        if(EVENT_DEMO_SITE){
            if($action !='')
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/all_events.php");
        else
            include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events_tao_ai_new.php");
            break;
        }else{
            if (taoh_user_is_logged_in() || 1) {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/events.php"); // events_landing.php
            } else {
                include_once(TAOH_PLUGIN_PATH . "/app/" . $current_app . "/visitor.php");
            }
            break;
        }
}
die();
?>
