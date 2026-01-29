<?php
// Check if already bootstrapped via router
if (!defined('TAOH_SITE_URL')) {
    require_once(dirname(__DIR__) . '/config.php');
    require_once(dirname(__DIR__) . '/function.php');
}

// Load SSO cookie config from login-new
require_once(dirname(__DIR__) . '/login-new/config.php');

// Check for email cookie
if(!isset($_COOKIE['email']) || empty($_COOKIE['email'])) {
    echo "No email cookie found - redirecting to login-new";
    header('Location: ' . TAOH_SITE_URL_ROOT . '/login-new');
    exit;
}

$email = $_COOKIE['email'];
$user_key = isset($_COOKIE['user_key']) ? $_COOKIE['user_key'] : '';
$tao_profile_info = isset($_COOKIE['tao_profile_info']) ? $_COOKIE['tao_profile_info'] : '';
$first_name = $last_name = '';
if($tao_profile_info != ''){
    $profile_info = json_decode($tao_profile_info,1);
    $first_name = isset($profile_info['first_name']) ? $profile_info['first_name'] : '';
    $last_name = isset($profile_info['last_name']) ? $profile_info['last_name'] : '';
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
// 'debug'=>1
);
$taoh_call_type = "get";

//echo "====in code case=====".taoh_apicall_get_debug($taoh_call, $taoh_vals );die();
$return = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );
if($return['success'] &&  isset( $return['output'] ) ) {
                $api_return_token = $return['output'];
                if(TAOH_SIMPLE_LOGIN && $return['is_new_user']){
                    setcookie( TAOH_ROOT_PATH_HASH.'_anonymous', 1, strtotime( '+1 days' ),'/');
                }

                setcookie( TAOH_ROOT_PATH_HASH."_temp_api_token", $return['output'], strtotime( '+1 days' ), '/'  );

                if(

                    (
                      isset($return['is_new_user']) && $return['is_new_user'] == 1
                    ) ||
                    (
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
                      //setcookie( TAOH_ROOT_PATH_HASH."_taoh_api_token", $return['output'], strtotime( '+30 days' ), '/'  );
                        //die('------------');
                        ?>
                        <script>
                        localStorage.removeItem('email');
                        localStorage.removeItem('isCodeSent');
                        </script>
                        <?php
                        $_SESSION['referral_redirect'] = 'referral_redirect';
                        call_login_referral_action($return['output'],1);
                       // taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();
                    }

                  }


                    ?>
                  <script>
                    localStorage.removeItem('email');
                     localStorage.removeItem('isCodeSent');
                </script>
                <?php
                //die('-----aaaaaa---------');
                //echo "===========".$return['output'];die();
                //setcookie( TAOH_ROOT_PATH_HASH."_taoh_api_token", $return['output'], strtotime( '+30 days' ), '/'  );
                $_SESSION['referral_redirect'] = 'referral_redirect';
                call_login_referral_action($return['output'],1);
               //taoh_redirect( TAOH_SITE_URL_ROOT ); taoh_exit();


            } else {
                //unset($_COOKIE);
                taoh_set_error_message('Invalid Code!');
                taoh_redirect(TAOH_SITE_URL_ROOT);
                taoh_exit();
            }
/*
echo "<pre>Email: $email</pre>";
echo "<pre>User Key: $user_key</pre>"; */

// Include header to load jQuery and other scripts
taoh_get_header();
?>
<script>
/* $(document).ready(function(){
    var email = '<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>';
    var first_name = '<?php echo htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8'); ?>';
    var last_name = '<?php echo htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8'); ?>';
    console.log('SSO Login - Email:', email);

    const data = {
        'taoh_action': 'check_sso_login',
        'slug': 'mod',
        'app': 'hires',
        'social': '1',
        'email': email,
        'first_name':first_name,
        'last_name':last_name
    };

    console.log('Sending AJAX request...');
    $.post("<?php echo taoh_site_ajax_url(); ?>", data, function(response) {
        console.log('AJAX Response:', response);
        if(response == 1) {
            console.log('Login successful, redirecting to home...');
            window.location.href = '<?php echo TAOH_SITE_URL_ROOT; ?>';
        } else {
            console.log('SSO login failed, response:', response);
            alert('Login failed. Check console for details.');
        }
    }).fail(function(xhr, status, error) {
        console.log('AJAX Error:', status, error);
        alert('AJAX request failed: ' + error);
    });
}); */
</script>
<?php taoh_get_footer(); ?>
