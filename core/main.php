<?php
//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
//echo '<pre>';print_r($_SERVER);echo '</pre>';exit();
/* core/main.php
  This page ment for check the route and according to include the files
  You can use the CONSTANT and functions and global variables here
  Do not define or redifine CONSTANT here.
  Do not Implement the redirection
*/

define('TAOH_APP_SLUG', TAOH_PLUGIN_PATH_NAME);

$app_url = taoh_parse_url(0);
$app_name = taoh_parse_url(1);
$detail_name = taoh_parse_url(2);
if (isset($_GET['temp_api_token']) && $_GET['temp_api_token']) {
    setcookie(TAOH_ROOT_PATH_HASH . '_taoh_api_token', $_GET['temp_api_token'], strtotime('+2 days'), '/');
    //die();
    //call_login_referral_action($_COOKIE['tao_api_email']);
}

if (isset($_SESSION['referral_redirect']) && $_SESSION['referral_redirect'] == 'referral_redirect') {
    unset($_SESSION['referral_redirect']);
    ?>
        <script>
        localStorage.removeItem('email');
        localStorage.removeItem('isCodeSent');
        window.location.reload();
        </script>
    <?php
    die();
}

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    taoh_logout();
    header("Location: " . TAOH_SITE_URL_ROOT . "");
    taoh_exit();
}

if (isset($_GET['clear']) && $_GET['clear'] == 'config') {
    if (taoh_user_is_logged_in())
        taoh_get_user_info(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']->ptoken);
}


//echo"<pre>";print_r($_SESSION);taoh_exit();
//echo $_COOKIE[TAOH_ROOT_PATH_HASH.'_'.'locked'];//die();

if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'locked']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'locked'] == 1
    && $app_url != 'login' && $app_url != 'logout' && $app_url != 'ajax') {
    //echo TAOH_LOGIN_URL;die();
    taoh_redirect(TAOH_LOGIN_URL);
    //taoh_redirect(TAOH_SITE_URL_ROOT.'/login');
    taoh_exit();
}
if ($app_url != 'ajax') {
    // die('--------');
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token'])) {
        taoh_update_user_as_anonymous($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token']);
    }
}
//echo "=======".$app_url;
if ($app_url == 'login-i') {
    force_subsecret_data(TAOH_SITE_ROOT_HASH);
}

if (isset($_GET['login']) && $_GET['login'] == 'social') {
    call_login_referral_action();
}

$user_data = taoh_user_all_info();


if (!TAOH_SIMPLE_LOGIN && $app_url != 'createacc' && $app_url != 'actions' && $app_url != 'ajax') {
    //die('==aaaaaa=====');
    if (!isset($user_data->type) && !isset($user_data->chat_name) && defined('TAOH_API_TOKEN') && defined('TAOH_SETTINGS_URL') && TAOH_API_TOKEN) {
        taoh_redirect(TAOH_SITE_URL_ROOT . '/createacc');
        taoh_exit();
    }
}


//echo "app_url = $app_url; app_name = $app_name; detail_name = $detail_name";taoh_exit();

/*if(TAOH_REFER_ENABLE){
  if( $app_url!='refer' && $app_url!='actions' && $app_url!='ajax' && $app_url!='social' && $app_url!='login' &&
  $app_url!='login_fwd'  && $app_url!='logout' && $app_url!='settings' && $app_url!='fwd' && $app_url!='createacc'){
   //i am die
    //die('----i am dead------');
    if(isset($app_name) && $app_name =='ajax' && $app_url =='networking' ) {

    }
    else{
     //die('I am here coming'.'----------'.$app_url);
      checkReferral();
    }
  }
}*/
if (!defined('TAOH_SITE_FWD_URL_FULL')) {
    define('TAOH_SITE_FWD_URL_FULL', sprintf("%s://%s%s", isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])); // Only enable this if your website is not reachable by search engines
}
$current_url = str_replace('/stlo', '', TAOH_SITE_FWD_URL_FULL);
/*echo "<br>".TAOH_SITE_FWD_URL_FULL;

echo "<br>".$current_url;
echo "<br>".$_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'];
die();*/
//echo TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url';die();
if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] != '') {

    if ($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] == TAOH_SITE_URL_FULL) {
        if (taoh_user_is_logged_in()) {
            delete_refer_token();
        }
    }

    if (taoh_user_is_logged_in() && $current_url == $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) {
        delete_refer_token();
        $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] = '';
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url', ' ', strtotime('-3 days'), '/');
    }

}

//echo "==".$app_url;taoh_exit();
switch ($app_url) {
    case 'sb':
        include_once('sandbox.php');
        break;
    case 'test':
        include_once('test.php');
        break;
    case 'taoh_test':
        include_once('taoh_test.php');
        break;
    case 'sitemap':
        include_once('sitemap.php');
        break;
    case 'cacheo':
        include_once('cacheo.php');
        break;
    case 'ajax':
        include_once('ajax.php');
        break;

    case 'networkingajax':
        include_once('networkingajax.php');
        break;
    case 'networking_redis_ajax':
        include_once('networking_redis_ajax.php');
        break;
    case 'networking_club_ajax':
        include_once('networking_club_ajax.php');
        break;
    case 'delete_cache':
        include_once('delete_cache.php');
        break;
    case 'home2':
        include_once('home2.php');
        break;
    case 'jobs_mobile':
        include_once('jobs_mobile.php');
        break;
    case 'events_new':
        include_once('events_new.php');
        break;
    case 'login-success':
        include_once('login_success.php');
        break;
    case 'sso-login':
        include_once('sso_login.php');
        break;
    case 'login-new':
       include_once(TAOH_PLUGIN_PATH . '/login-new/index.php');
       break;
    case 'login-i':
        $link = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $link = $_SERVER['HTTP_REFERER'];
            //call_login_referral($link);
        }
        //echo $link;die('------------');
        //$url = TAOH_DASH_PREFIX."/login-i?sub_secret_token=".TAOH_SITE_ROOT_HASH;
        $url = TAOH_DASH_PREFIX . "/iframe_login.php?sub_secret_token=" . TAOH_SITE_ROOT_HASH;
        taoh_redirect($url);
        exit();
        break;
    case 'new_login':
        //alert('------------');
        $footer_tracking_link = 'login';
        include_once('new_login.php');
        break;
    case 'login':
        $footer_tracking_link = 'login';
        $link = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $link = $_SERVER['HTTP_REFERER'];

        }

        if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'] != '')
            $link = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'];

        if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_locked']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_locked'] == 1) {
            //do nothing
        } else {
            // echo'<pre>';print_r($_SERVER);echo'</pre>';
            //  echo "=====".$link."====". TAOH_SITE_URL_ROOT;
            // $home = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url'];
            $home = $_COOKIE[TAOH_ROOT_PATH_HASH . '_referral_back_url'] ?? ''; // error log
            $home_url = explode('?', $home ?? '')[0];
            //ECHO "<BR>==========".strstr('TAOH_SITE_URL_ROOT',$link);
            if ($home_url != TAOH_SITE_URL_ROOT && stripos($link, 'dash') == false && $link != 'http://localhost/'
                && str_contains($link, TAOH_SITE_URL_ROOT)) {
                // die('-----call_login_referral--------'.$link);
                call_login_referral($link, 1);
            } else {
                //die('----NO REFERRAL----');
            }

        }

        if (taoh_parse_url(1) == 'remove') {
            $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/0/loc/remove/" . taoh_parse_url(2);
            taoh_redirect($url);
            exit();
        } else if (taoh_parse_url(1) == 'code') {
            $url = TAOH_SITE_URL_ROOT . "/login_fwd/code/" . taoh_parse_url(2);
            taoh_redirect($url);
            exit();
        } else {
            $login = 1;
            include_once('new_login.php');
        }

        break;
    case 'login_fwd':
        include_once('login_fwd.php');
        break;
    case 'social':
        $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/0/loc/login";
        // echo $url;die();
        taoh_redirect($url);
        exit();
        break;
    case 'donate':

        if (taoh_user_is_logged_in())
            $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/1/u/loc/donate";
        else
            $url = TAOH_SITE_URL_ROOT . "/fwd/ss/" . TAOH_SITE_ROOT_HASH . "/log/0/u/loc/donate";
        //echo $url ;die();
        taoh_redirect($url);
        exit();
        break;
    case 'panel':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect(TAOH_LOGIN_URL);
        }
        //Check for direct link Ex: /hires/login/hires/EZBZXB
        include_once('panel.php');
        break;
    case 'logout':
        /*taoh_set_success_message('You have successfully logged out!');
        taoh_logout();
        delete_refer_token();
        return taoh_redirect(TAOH_SITE_URL_ROOT);*/
        include_once('logout.php');
        break;
    case 'reads':
    case 'learning':
        include_once('learning/main.php');
        break;
    case 'home_lp':
        include_once('blog_lp/main.php');
        break;
    case 'ig':
        include_once('ig/index.php');
        break;
    case 'about':
        include_once('networking-visitor.php');
        break;
    case 'referral':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect();
        }
        include_once('referral.php');
        break;
    case 'refer':
        include_once('refer.php');
        break;
    case 'dash':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect();
        }
        include_once('dash.php');
        break;
    case 'backsoon':
        include_once('backsoon.php');
        break;
    case 'xml':
        include_once('xml.php');
        break;
    case 'go':
        include_once('go.php');
        break;
    case 'community':
        include_once('community/main.php');
        break;
    case 'notifications':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect();
        }
        include_once('notifications.php');
        break;
    case 'createsettings':
        require_once('form_fields.php');
        include_once('cresettings.php');
        break;
    case 'health':
        echo '{"success":true,"output":{"working":true}}';
        break;
    case 'contact':
        include_once('contact.php');
        break;
    case 'room':
        include_once('room.php');
        break;
    case 'orgchat':
        if (!taoh_user_is_logged_in()) {
            return taoh_redirect();
        }
        include_once('orgchat.php');
        break;

    case 'networking_old':
        if (!taoh_user_is_logged_in()) {
            include_once('networking-visitor.php');
        } else {
            include_once('networking.php');
        }
        break;
    case 'networking_redis':
        if (!taoh_user_is_logged_in()) {
            include_once('networking-visitor.php');
        } else {
            include_once('networking_redis.php');
        }
        break;
    case 'club':

        if (EVENT_DEMO_SITE)
            include_once(TAOH_PLUGIN_PATH . "/app/events/main.php");
        else
            include_once('club/main.php');
        break;
    case 'message':
        include_once('message/main.php');
        break;
    case 'livenow':
    case 'live':
        include_once(TAOH_CORE_PATH . '/live_now/main.php');
        break;
    case 'dm':
        include_once(TAOH_CORE_PATH . '/direct_message/main.php');
        break;
    case 'live_now':
        include_once("live_now.php");
        break;
    case 'actions':
        if (!taoh_user_is_logged_in() && $app_name != 'feedback') {
            return taoh_redirect();
        }
        include_once('actions/main.php');
        break;

    case 'employers':
        include_once('employers.php');
        break;
    case 'partners':
        include_once('partners.php');
        break;
    case 'professionals':
        include_once('job_seekers.php');
        break;
    case 'dashboard':
        include_once('dashboard.php');
        break;
    case 'dashboard_new':
        include_once('dashboard_new.php');
        break;
    case '404':
        include_once('404.php');
        break;
    case 'unsubscribe':
        include_once('unsubscribe.php');
        break;
    case 'noworkerleftbehind':
        include_once('noworkerleftbehind.php');
        break;
    case 'chat':
        include_once('room/room.php');
        break;
    case 'settings':
        $footer_tracking_link = 'settings';
        require_once('form_fields.php');
        include_once('settings.php');
        break;
    case 'resume-html-tcpdf':
        include_once('resume_html_tcpdf.php');
        break;
    case 'resume-html':
        include_once('resume_html.php');
        break;
    case 'connections':
        include_once TAOH_CORE_PATH . '/club/connections.php';
        break;
    case 'directory':
        $parse_param_1 = taoh_parse_url(1);
        if ($parse_param_1 === 'profile') {
            $type = taoh_parse_url(2);
            include_once TAOH_CORE_PATH . '/club/profile_directory.php';
        } elseif ($parse_param_1 === 'club') {
            include_once TAOH_CORE_PATH . '/club/directory.php';
        } else {
            taoh_redirect(TAOH_SITE_URL_ROOT . '/directory/club');
        }
        break;
    case 'profile':
        $ptoken = taoh_parse_url(1);
        include_once TAOH_CORE_PATH . '/profile_html.php';
        break;
    case 'following':
        $ptoken = taoh_parse_url(1);
        include_once TAOH_CORE_PATH . '/following.php';
        break;
    case 'followers':
        $ptoken = taoh_parse_url(1);
        include_once TAOH_CORE_PATH . '/followers.php';
        break;
    case 'resume-download':
        include_once('resume_download.php');
        break;
    case 'faq':
        include_once('faq.php');
        break;
    case 'support':
        include_once('faq.php');
        die;
    case 'feedback':
        include_once('feedback_survey.php');
        die;
    case 'bugreport':
        include_once('bug_reporting.php');
        die;
    case 'profile-iframe':
        include_once('profile-iframe.php');
        break;
    case 'fwdv':
        include_once('fwd_vk.php');
        break;
    case 'fwd':
        include_once('fwd.php');
        break;

    /*case 'fwwd':
      include_once('fwwd.php');
      break;
    case 'fwd1':
      include_once('fwd1.php');
      break;*/
    case 'createacc':
        require_once('login_form_fields.php');
        include_once('create.php');
        break;
    case 'tools':
        include_once('tools/main.php');
        break;
    case 'apps':
        include_once('apps.php');
        break;
    case 'bulk':
        include_once('bulk_form.php');
        break;
    case 'forward':
        include_once('forward.php');
        break;
    case 'site':
        include_once('site_details.php');
        break;

    case 'newhome':
        include_once('new_home.php');
        break;
    case 'rx':
        include_once('new_payment_reciept.php');
        break;
    case 'all':
        include(TAOH_PLUGIN_PATH . "/app/events/main.php");
        break;
    case 'roomupdate':
        $room_type = taoh_parse_url(1);
        if($room_type == 'event'){
            $eventtoken = taoh_parse_url(2);
            include(TAOH_PLUGIN_PATH . "/app/events/room_update.php");
        } else {
//            $roomtoken = taoh_parse_url(2);
//            include(TAOH_PLUGIN_PATH . "/core/club/room_update.php");
        }
        break;
    case taoh_parse_url(0): //App routing
        if (in_array(taoh_parse_url(0), taoh_available_apps())) {
            include(TAOH_PLUGIN_PATH . '/app/' . taoh_parse_url(0) . '/main.php');
            break;
        }
    default:
        //print_r ($_SERVER);taoh_exit();
        //if ( strlen( array_pop( explode( TAOH_PLUGIN_PATH_NAME, $_SERVER[ 'REQUEST_URI' ] ) ) ) <= 2  || ! $app_name ){
        $exp = explode(TAOH_PLUGIN_PATH_NAME, $_SERVER['REQUEST_URI']);
        if (strlen(array_pop($exp)) <= 2 || !$app_name) {
            if (defined('TAOH_API_TOKEN') && TAOH_API_TOKEN) {
                //include_once('dashboard_new.php');
                //include_once('panel.php');
                if (EVENT_DEMO_SITE)
                    include(TAOH_PLUGIN_PATH . "/app/events/main.php");
                else
                    include(TAOH_PLUGIN_PATH . '/core/club/main.php');
                break;
            } else {
                //include_once('dashboard_new.php');
                //include_once('panel.php');
                if (EVENT_DEMO_SITE)
                    include(TAOH_PLUGIN_PATH . "/app/events/main.php");
                else
                    include(TAOH_PLUGIN_PATH . '/core/club/main.php');
                break;
            }
        } else {
            return taoh_redirect(TAOH_SITE_URL_ROOT . '/404');
        }
        break;
}


?>