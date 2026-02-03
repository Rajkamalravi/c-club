<?php
// Check if already bootstrapped via router
if (!defined('TAOH_SITE_URL')) {
    require_once(dirname(__DIR__) . '/config.php');
    require_once(dirname(__DIR__) . '/function.php');
}

// Load SSO cookie config from login-new
//require_once('login.php');
//echo "<pre>";print_r($_COOKIE);

if (!defined('COOKIE_DOMAIN')) define('COOKIE_DOMAIN', '.unmeta.net');

// Handle popup close step (redirected here after cookies were set)
if (isset($_GET['popup_close']) && $_GET['popup_close'] == '1') {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Login Successful</title></head>
    <body>
    <p style="text-align:center;font-family:sans-serif;margin-top:40vh;color:#666;">Login successful. You can close this window.</p>
    <script>
        localStorage.removeItem('email');
        localStorage.removeItem('isCodeSent');
        if (window.opener) {
            window.opener.postMessage({type: 'login_success'}, '<?php echo TAOH_SITE_URL_ROOT; ?>');
            setTimeout(function(){ window.close(); }, 500);
        } else {
            window.location.href = '<?php echo TAOH_SITE_URL_ROOT; ?>';
        }
    </script>
    </body>
    </html>
    <?php
    exit;
}

// Check for popup login mode (query param or fallback cookie)
$is_popup = (isset($_GET['popup']) && $_GET['popup'] == '1')
         || (isset($_COOKIE['sso_popup_login']) && $_COOKIE['sso_popup_login'] == '1');

// Check for email cookie
if(!isset($_COOKIE['email']) || empty($_COOKIE['email'])) {
    error_log('[sso_login.php] No email cookie found. is_popup=' . ($is_popup ? '1' : '0') . ' cookies=' . print_r(array_keys($_COOKIE), true));
    if ($is_popup) {
        // In popup mode, show error instead of silently exiting
        setcookie('sso_popup_login', '', time() - 3600, '/', '', true, false);
        ?>
        <!DOCTYPE html>
        <html>
        <head><title>Login Error</title></head>
        <body>
        <p style="text-align:center;font-family:sans-serif;margin-top:40vh;color:#c00;">Login session expired. Please try again.</p>
        <script>
            setTimeout(function(){
                if (window.opener) {
                    window.opener.postMessage({type: 'login_failed'}, '<?php echo TAOH_SITE_URL_ROOT; ?>');
                }
                window.close();
            }, 2000);
        </script>
        </body>
        </html>
        <?php
        exit;
    }
    header('Location: ' . TAOH_SITE_URL_ROOT . '/login.php');
    exit;
}

$email = $_COOKIE['email'];
$user_key = isset($_COOKIE['user_key']) ? $_COOKIE['user_key'] : '';
$tao_profile_info = isset($_COOKIE['tao_profile_info']) ? $_COOKIE['tao_profile_info'] : '';
$first_name = $last_name = $social_id = '';

if($tao_profile_info != ''){
    $profile_info = json_decode($tao_profile_info,1);
    $first_name = isset($profile_info['first_name']) ? $profile_info['first_name'] : '';
    $last_name = isset($profile_info['last_name']) ? $profile_info['last_name'] : '';
    $social_id = isset($profile_info['social_id']) ? $profile_info['social_id'] : '';
}
$user_key = isset($_COOKIE['user_key']) ? $_COOKIE['user_key'] : '';

// Clear SSO cookies from browser (they're no longer needed after reading)
$cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
$cookie_clear_options = [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => $cookie_domain,
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
];
setcookie('tao_email', '', $cookie_clear_options);
setcookie('tao_user_key', '', $cookie_clear_options);
setcookie('tao_logged_in', '', $cookie_clear_options);
setcookie('tao_profile_info', '', $cookie_clear_options);
setcookie('email', '', $cookie_clear_options);
setcookie('user_key', '', $cookie_clear_options);

// Also clear without domain (in case set with empty domain)
if ($cookie_domain !== '') {
    $cookie_clear_no_domain = $cookie_clear_options;
    $cookie_clear_no_domain['domain'] = '';
    setcookie('tao_email', '', $cookie_clear_no_domain);
    setcookie('tao_user_key', '', $cookie_clear_no_domain);
    setcookie('tao_logged_in', '', $cookie_clear_no_domain);
    setcookie('tao_profile_info', '', $cookie_clear_no_domain);
    setcookie('email', '', $cookie_clear_no_domain);
    setcookie('user_key', '', $cookie_clear_no_domain);
}

// Unset from PHP array
unset($_COOKIE['tao_email']);
unset($_COOKIE['tao_user_key']);
unset($_COOKIE['tao_logged_in']);
unset($_COOKIE['tao_profile_info']);
unset($_COOKIE['email']);
unset($_COOKIE['user_key']);

$taoh_call = "account.temps";
$taoh_vals = array(
    'secret'=>TAOH_API_SECRET,
    'mod'=>'mod',
    'cmd'=>'ssocreate',
    'cache_required'=>0,
    'q'=> urlencode( "hires:::".$email.":::".$first_name.":::".$last_name.":::1"),
);
$taoh_call_type = "get";

$return = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );
error_log('[sso_login.php] API response: success=' . ($return['success'] ?? 'null') . ' output=' . (isset($return['output']) ? 'SET' : 'NOT SET') . ' is_popup=' . ($is_popup ? '1' : '0'));

if($return['success'] && isset( $return['output'] ) ) {
    $api_return_token = $return['output'];
    if(($first_name != '' || $last_name != '') && (TAOH_SIMPLE_LOGIN && $return['is_new_user'])){
        $userAnonymousData['fname'] = $first_name;
        $userAnonymousData['lname'] = $last_name;    
        if($social_id != ''){
            $userAnonymousData['social_id'] = $social_id;
            $userAnonymousData['created_via'] = 'social';
        }
        $files = glob(realpath('./assets/images/avatar/PNG/128') . '/anonymous-*.png');
        $file = array_rand($files);
        $url = $files[$file];
        $pathinfo = pathinfo($url);
        $image = $pathinfo['filename'];
        $userAnonymousData['avatar'] = $image;
        $userAnonymousData['type'] = 'professional';
        $userAnonymousData['chat_name'] = $first_name;
        $taoh_call = 'users.tao.add';
        $taoh_call_type = 'POST';
        $taoh_vals = array(
            'token' => $api_return_token,
            'mod' => 'tao_tao',
            'toenter' => $userAnonymousData,

        );
        //echo "==========<pre>";print_r( $taoh_vals);
        //echo taoh_apicall_post_debug($taoh_call, $taoh_vals);die;
        $result = taoh_apicall_post($taoh_call, $taoh_vals);
            //echo "<pre>";print_r( $result);die;
            
    }else{
            if(TAOH_SIMPLE_LOGIN && $return['is_new_user']){
            setcookie( TAOH_ROOT_PATH_HASH.'_anonymous', 1, strtotime( '+1 days' ),'/');
        }

    }
    setcookie( TAOH_ROOT_PATH_HASH."_temp_api_token", $return['output'], strtotime( '+1 days' ), '/'  );

    // Note: Popup login is now handled entirely in login.php.
    // The popup closes there and tells the parent to navigate to /sso-login,
    // so this code always runs as a normal page load in the parent window.

    // Clear USER_INFO session so fresh data (with updated fname) is fetched on next page load
    $sessionData = taoh_session_get(TAOH_ROOT_PATH_HASH) ?? [];
    if (isset($sessionData['USER_INFO'])) {
        unset($sessionData['USER_INFO']);
    }
    // Set flag to force cache bypass on next user info fetch
    $sessionData['FORCE_USER_REFRESH'] = 1;
    $_SESSION[TAOH_ROOT_PATH_HASH] = $sessionData;
//echo '========='.TAOH_LOGIC_LOCK_CODE;//die;
    if((isset($return['is_new_user']) && $return['is_new_user'] == 1) || (
            isset($return['site_locked']) && $return['site_locked'] == 1 &&
            isset($return['is_new_user']) && $return['is_new_user'] == 0 &&
            isset($return['user_locked']) && $return['user_locked'] == 0
        )
    ){
        if(TAOH_LOGIC_LOCK_CODE){
            setcookie( TAOH_ROOT_PATH_HASH.'_enable_lock_screen', '1', strtotime( '+1 days' ),'/');
            setcookie( TAOH_ROOT_PATH_HASH.'_locked', '1', strtotime( '+1 days' ),'/');
            taoh_redirect($forward_url);
            taoh_exit();
        }
        else{
            ?>
            <script>
            localStorage.removeItem('email');
            localStorage.removeItem('isCodeSent');
            </script>
            <?php
           
            $_SESSION['referral_redirect'] = 'referral_redirect';
            call_login_referral_action($return['output'],1);
            taoh_user_all_info_settings($api_return_token);
        }
    }
//die;
    ?>
    <script>
    localStorage.removeItem('email');
    localStorage.removeItem('isCodeSent');
    </script>
    <?php
    $_SESSION['referral_redirect'] = 'referral_redirect';
    call_login_referral_action($return['output'],1);

} else {
    taoh_set_error_message('Invalid Code!');
    taoh_redirect(TAOH_SITE_URL_ROOT);
    taoh_exit();
}

taoh_get_header();
?>
<script>
</script>
<?php taoh_get_footer(); ?>
