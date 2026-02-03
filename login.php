<?php
/**
 * Login New - Standalone SSO Client
 *
 * Combined config and SSO redirect/callback handler
 * Handles both initial SSO redirect and callback from login.tao.ai
 *
 * IMPORTANT: Add the following variables in env.php to configure SSO login:
 *
 *   define('TAOH_SSO_BASE_URL', 'https://login.tao.ai');            // SSO server base URL
 *   define('TAOH_SSO_COOKIE_DOMAIN', '.unmeta.net');                 // Cookie domain for cross-subdomain sharing
 *   define('TAOH_SSO_SITE_EMAIL', 'info@tao.ai');                   // Site contact email
 *   define('TAOH_SSO_APP_EMOJI', '⛵');                              // App emoji identifier
 *   define('TAOH_SSO_POST_LOGIN_PATH', '/sso-login');               // Redirect path after successful login
 *   define('TAOH_SSO_CALLBACK_PATH', '/login.php');             // SSO callback path
 *
 * The following are already expected in env.php / config.php:
 *
 *   TAOH_SITE_NAME_SLUG   -> SITE_NAME
 *   TAOH_SITE_URL_ROOT    -> SITE_URL
 *   TAOH_DEBUG            -> DEBUG_MODE
 *   TAOH_SITE_LOGO        -> APP_LOGO_URL
 *   TAOH_DEFAULT_TIMEZONE -> Timezone
 */

// ========== CONFIGURATION ==========
// Load env.php and config.php (same pattern as the rest of the project)
if (file_exists(__DIR__ . '/env.php')) require_once __DIR__ . '/env.php';
if (file_exists(__DIR__ . '/config.php')) require_once __DIR__ . '/config.php';

// Timezone
date_default_timezone_set(defined('TAOH_DEFAULT_TIMEZONE') ? TAOH_DEFAULT_TIMEZONE : 'UTC');

// Map TAOH constants to login.php constants
defined('SITE_NAME') || define('SITE_NAME', defined('TAOH_SITE_NAME_SLUG') ? TAOH_SITE_NAME_SLUG : 'Unmeta');
defined('SITE_URL') || define('SITE_URL', defined('TAOH_SITE_URL_ROOT') ? TAOH_SITE_URL_ROOT : 'https://unmeta.net/club');
defined('SITE_EMAIL') || define('SITE_EMAIL', defined('TAOH_SSO_SITE_EMAIL') ? TAOH_SSO_SITE_EMAIL : 'info@tao.ai');
defined('BASE_URL') || define('BASE_URL', SITE_URL);
defined('DEBUG_MODE') || define('DEBUG_MODE', defined('TAOH_DEBUG') ? TAOH_DEBUG : false);

// SSO Configuration (TAO.ai Login Integration)
defined('APP_NAME') || define('APP_NAME', SITE_NAME);
defined('APP_EMOJI') || define('APP_EMOJI', defined('TAOH_SSO_APP_EMOJI') ? TAOH_SSO_APP_EMOJI : '⛵');
defined('APP_LOGO_URL') || define('APP_LOGO_URL', defined('TAOH_SITE_LOGO') ? TAOH_SITE_LOGO : 'https://cdn.tao.ai/assets/wertual/images/worker1_sq.png');
defined('SITE_LOGO') || define('SITE_LOGO', APP_LOGO_URL);
defined('POST_LOGIN_URL') || define('POST_LOGIN_URL', SITE_URL . (defined('TAOH_SSO_POST_LOGIN_PATH') ? TAOH_SSO_POST_LOGIN_PATH : '/sso-login'));
defined('SSO_BASE_URL') || define('SSO_BASE_URL', defined('TAOH_SSO_BASE_URL') ? TAOH_SSO_BASE_URL : 'https://login.tao.ai');

// SSO Callback Configuration
defined('SSO_CALLBACK_URL') || define('SSO_CALLBACK_URL', SITE_URL . (defined('TAOH_SSO_CALLBACK_PATH') ? TAOH_SSO_CALLBACK_PATH : '/login.php'));
defined('SSO_CALLBACK_FAIL_URL') || define('SSO_CALLBACK_FAIL_URL', SITE_URL . (defined('TAOH_SSO_CALLBACK_PATH') ? TAOH_SSO_CALLBACK_PATH : '/login.php') . '?error=1');
defined('SSO_VALIDATE_URL') || define('SSO_VALIDATE_URL', SSO_BASE_URL . '/sso.php');

// Cookie Configuration (aligned with SSO server)
defined('COOKIE_DOMAIN') || define('COOKIE_DOMAIN', defined('TAOH_SSO_COOKIE_DOMAIN') ? TAOH_SSO_COOKIE_DOMAIN : '.unmeta.net');
defined('SSO_USER_KEY_COOKIE') || define('SSO_USER_KEY_COOKIE', 'user_key');
defined('SSO_EMAIL_COOKIE') || define('SSO_EMAIL_COOKIE', 'email');
defined('SSO_LOGIN_COOKIE') || define('SSO_LOGIN_COOKIE', 'login_vars');

// ========== END CONFIGURATION ==========


// Check if user is already logged in (check both local AND SSO cookies)
function is_logged_in() {
    // Check local cookies first
    if (!empty($_COOKIE['tao_logged_in']) && $_COOKIE['tao_logged_in'] == '1') {
        return true;
    }

    // Check SSO server cookies (aligned names)
    if (!empty($_COOKIE['user_key']) && !empty($_COOKIE['email'])) {
        return true;
    }

    // Check main SSO login cookie
    if (!empty($_COOKIE['login_vars'])) {
        return true;
    }

    return false;
}

// Validate ops parameter
function validate_ops($ops) {
    $validOps = ['login', 'logout', 'validate'];
    return in_array($ops, $validOps, true);
}

// Helper function to set user cookies (aligned with SSO server)
function set_user_cookies($email, $user_key, $domain, $profile_info) {
    $expire = time() + (60 * 60 * 24 * 30); // 30 days
    // ALWAYS set secure=true in production (HTTPS should always be enforced)
    $secure = true;

    error_log('[login.php] Setting cookies - Email: ' . $email . ', Domain: ' . $domain . ', Secure: YES');

    // Set cookies with proper options (PHP 7.3+ array syntax)
    $cookie_options = [
        'expires' => $expire,
        'path' => '/',
        'domain' => $domain,
        'secure' => $secure,
        'httponly' => true,  // Prevent XSS attacks
        'samesite' => 'Lax'  // Allow cookies across same-site redirects
    ];

    // Set both legacy (tao_*) and new (aligned with server) cookie names
    $result1 = setcookie('tao_email', $email, $cookie_options);
    $result2 = setcookie('tao_user_key', $user_key, $cookie_options);
    $result3 = setcookie('tao_logged_in', '1', $cookie_options);

    // Also set aligned cookie names (matching SSO server)
    $result4 = setcookie('email', $email, $cookie_options);
    $result5 = setcookie('user_key', $user_key, $cookie_options);

    if(count($profile_info) > 0){
        $profile_info_json = json_encode($profile_info);
        $result6 = setcookie('tao_profile_info', $profile_info_json, $cookie_options);
        $_COOKIE['tao_profile_info'] = $profile_info_json;
    }

    // Also set in $_COOKIE for immediate availability
    $_COOKIE['tao_email'] = $email;
    $_COOKIE['tao_user_key'] = $user_key;
    $_COOKIE['tao_logged_in'] = '1';
    $_COOKIE['email'] = $email;
    $_COOKIE['user_key'] = $user_key;

    error_log('[login.php] Cookies set - Results: tao_email=' . ($result1?'OK':'FAIL') . ', tao_key=' . ($result2?'OK':'FAIL') . ', logged_in=' . ($result3?'OK':'FAIL') . ', email=' . ($result4?'OK':'FAIL') . ', user_key=' . ($result5?'OK':'FAIL'));
}

// Helper function to make POST request
function post_request($url, $params = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        error_log('[login.php] cURL error: ' . $err);
        return false;
    }
    $decoded = json_decode($resp, true);
    return $decoded === null ? $resp : $decoded;
}

// ========== HANDLE CALLBACK FROM login.tao.ai ==========
// Check if this is a POST request (callback from login.tao.ai)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate ops parameter if provided
    $ops = isset($_POST['ops']) ? trim($_POST['ops']) : 'login';
    if (!validate_ops($ops)) {
        error_log('[login.php] ERROR: Invalid ops parameter: ' . $ops);
        $fail_url = defined('SSO_CALLBACK_FAIL_URL') ? SSO_CALLBACK_FAIL_URL : '/';
        header('Location: ' . $fail_url . '?error=invalid_ops&error_message=' . urlencode('Invalid operation'));
        exit;
    }
    // echo "<pre>";print_r($_POST);
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $user_key = isset($_POST['user_key']) ? trim($_POST['user_key']) : '';
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
    $social_id = isset($_POST['social_id']) ? trim($_POST['social_id']) : '';
    $profile_info = [];
    if($first_name != ''){
        $profile_info['first_name'] = $first_name;
    }
    if($last_name != ''){
        $profile_info['last_name'] = $last_name;
    }
    if($social_id != ''){
        $profile_info['social_id'] = $social_id;
    }
    //echo "<pre>";print_r($profile_info);die;
    // DEBUG: Log values to error_log (check server logs)
    error_log('[login.php] POST callback - email: ' . $email . ', first_name: ' . $first_name . ', last_name: ' . $last_name);

    $payload = isset($_POST['payload']) ? $_POST['payload'] : null;

    if (!$email || !$user_key) {
        error_log('[login.php] ERROR: Missing email or user_key');
        $fail_url = defined('SSO_CALLBACK_FAIL_URL') ? SSO_CALLBACK_FAIL_URL : '/';
        header('Location: ' . $fail_url . '?error=missing_credentials&error_message=' . urlencode('Missing email or user_key'));
        exit;
    }

    // Validate with login.tao.ai/sso.php
    $sso_validate_url = (defined('SSO_BASE_URL') ? SSO_BASE_URL : 'https://login.tao.ai') . '/sso.php';
    $site_url = defined('SITE_URL') ? SITE_URL : 'https://labs.tao.ai';
    $validate_params = [
        'email' => $email,
        'user_key' => $user_key,
        'callbackurl' => $site_url . '/login.php',
        'ops' => 'validate'
    ];

    error_log('[login.php] Validating with: ' . $sso_validate_url);
    $resp = post_request($sso_validate_url, $validate_params);
    error_log('[login.php] Validation response: ' . print_r($resp, true));

    // If we have email and user_key from callback, proceed to set cookies
    if ($email && $user_key) {
        error_log('[login.php] ========== VALIDATION SUCCESSFUL ==========');

        // Set cookies
        $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '.tao.ai';
        set_user_cookies($email, $user_key, $cookie_domain, $profile_info);

        // Check if this is a popup login (via query param OR cookie set before werify.ai redirect)
        $is_popup_callback = (isset($_GET['popup']) && $_GET['popup'] == '1')
                          || (isset($_COOKIE['sso_popup_login']) && $_COOKIE['sso_popup_login'] == '1');

        if ($is_popup_callback) {
            // Clear the popup cookie
            setcookie('sso_popup_login', '', time() - 3600, '/', '', true, false);

            // Close the popup here itself and tell the parent window to navigate
            // to POST_LOGIN_URL (/sso-login). The parent does a normal full-page
            // load so sso_login.php runs in the parent context with no popup issues.
            $redirect_url = (defined('POST_LOGIN_URL') ? POST_LOGIN_URL : (defined('SITE_URL') ? SITE_URL . '/sso-login' : '/sso-login'));
            ?>
            <!DOCTYPE html>
            <html>
            <head><title>Login Successful</title></head>
            <body>
            <p style="text-align:center;font-family:sans-serif;margin-top:40vh;color:#666;">Login successful...</p>
            <script>
                localStorage.removeItem('email');
                localStorage.removeItem('isCodeSent');
                if (window.opener) {
                    window.opener.location.href = <?php echo json_encode($redirect_url); ?>;
                    setTimeout(function(){ window.close(); }, 500);
                } else {
                    window.location.href = <?php echo json_encode($redirect_url); ?>;
                }
            </script>
            </body>
            </html>
            <?php
            exit;
        }

        // Check if there's a fwd_url parameter (from initial /login?fwd_url=...)
        $fwd_url = isset($_GET['fwd_url']) ? trim($_GET['fwd_url']) : '';
        $fwd_redirect_url = isset($_POST['redirect_url']) ? trim($_POST['redirect_url']) : '';

        // Redirect to forward URL or default
        if ($fwd_url) {
            $redirect_url = $fwd_url;
            error_log('[login.php] Using fwd_url from query parameter: ' . $redirect_url);
        } else if ($fwd_redirect_url) {
            $redirect_url = $fwd_redirect_url;
            error_log('[login.php] Using redirect_url from POST: ' . $redirect_url);
        } else {
            $redirect_url = defined('POST_LOGIN_URL') ? POST_LOGIN_URL : (defined('SITE_URL') ? SITE_URL : '/');
            error_log('[login.php] Using default redirect URL: ' . $redirect_url);
        }

        header('Location: ' . $redirect_url);
        exit;
    } else {
        error_log('[login.php] ERROR: Validation failed');
        http_response_code(401);
        die('Authentication failed');
    }
}

// ========== HANDLE INITIAL LOGIN REQUEST (GET) ==========
// Check for popup mode — set a cookie so it survives the werify.ai round-trip
$is_popup = isset($_GET['popup']) && $_GET['popup'] == '1';
if ($is_popup) {
    setcookie('sso_popup_login', '1', time() + 300, '/', '', true, false);
    $_COOKIE['sso_popup_login'] = '1';
}

// Check for forward URL parameter
$fwd_url          = isset($_GET['fwd_url']) ? trim($_GET['fwd_url']) : '';
$fwd_redirect_url = isset($_GET['fwd_redirect_url']) ? trim($_GET['fwd_redirect_url']) : '';

// If already logged in AND has fwd_url, forward to that URL via SSO
if (is_logged_in() && $fwd_url) {
    error_log('[login.php] User logged in with fwd_url - Forwarding to: ' . $fwd_url);

    // Redirect to login.tao.ai with forward parameters
    $sso_base_url = defined('SSO_BASE_URL') ? SSO_BASE_URL : 'https://login.tao.ai';
    $sso_post_url = $sso_base_url . '/login.php';

    $payload = [
        'site_name' => defined('APP_NAME') ? APP_NAME : 'Labs Login',
        'site_logo' => defined('APP_LOGO_URL') ? APP_LOGO_URL : '',
        'callfwdurl' => $fwd_url,
        'redirecturl' => $fwd_url,
        'ops' => 'login'
    ];

    // Include existing credentials
    if (!empty($_COOKIE['tao_email'])) {
        $payload['email'] = $_COOKIE['tao_email'];
    }
    if (!empty($_COOKIE['tao_user_key'])) {
        $payload['user_key'] = $_COOKIE['tao_user_key'];
    }
    if (!empty($_COOKIE['tao_user_profile'])) {
        $fwd_vars = [];
        $fwd_vars['first_name'] = $_COOKIE['tao_user_profile']['first_name'] ?? '';
        $fwd_vars['last_name'] = $_COOKIE['tao_user_profile']['last_name'] ?? '';
        $fwd_vars['social_id'] = $_COOKIE['tao_user_profile']['social_id'] ?? '';
        $fwd_vars['avatar'] = $_COOKIE['tao_user_profile']['avatar'] ?? '';
        $payload['fwd_vars'] = json_encode($fwd_vars);
    }

    error_log('[login.php] Forwarding with payload: ' . print_r($payload, true));

    // Render auto-submit form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Forwarding...</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            }
            .loader {
                text-align: center;
                color: white;
            }
            .spinner {
                border: 4px solid rgba(255,255,255,0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="loader">
            <div class="spinner"></div>
            <p>Forwarding to destination...</p>
        </div>
        <form id="tao_forward_post" action="<?php echo htmlspecialchars($sso_post_url); ?>" method="POST">
            <?php foreach ($payload as $k => $v): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
            <?php endforeach; ?>
        </form>
        <script>
            document.getElementById('tao_forward_post').submit();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// If already logged in (no fwd_url), redirect to POST_LOGIN_URL
if (is_logged_in()) {
    $redirect_url = defined('POST_LOGIN_URL') ? POST_LOGIN_URL : '/';
    header('Location: ' . $redirect_url);
    exit;
}

// Not logged in - redirect to login.tao.ai with SSO payload
$site_url = defined('SITE_URL') ? SITE_URL : 'https://labs.tao.ai';
$sso_base_url = defined('SSO_BASE_URL') ? SSO_BASE_URL : 'https://login.tao.ai';
$sso_post_url = $sso_base_url . '/login.php';
$app_url = defined('POST_LOGIN_URL') ? POST_LOGIN_URL : $site_url;

// Build the SSO payload
if ($fwd_url) {
    $payload = [
        'site_name' => defined('APP_NAME') ? APP_NAME : 'Labs Login',
        'site_logo' => defined('APP_LOGO_URL') ? APP_LOGO_URL : '',
        'callfwdurl' => $fwd_url,
        'redirecturl' => $fwd_redirect_url ? $fwd_redirect_url : $fwd_url,
        'ops' => 'login'
    ];
    error_log('[login.php] Not logged in with fwd_url - Will redirect to: ' . $fwd_url . ' after login');
} else {
    $callback_url = $site_url . '/login.php';
    if ($is_popup) {
        $callback_url .= '?popup=1';
    }
    $payload = [
        'site_name' => defined('APP_NAME') ? APP_NAME : 'Labs Login',
        'site_logo' => defined('APP_LOGO_URL') ? APP_LOGO_URL : '',
        'callbackurl' => $callback_url,
        'redirecturl' => $app_url,
        'ops' => 'login'
    ];
}

error_log('[login.php] Redirecting to login.tao.ai with payload: ' . print_r($payload, true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to Login...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .loader {
            text-align: center;
            color: white;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
        <p>Redirecting to secure login...</p>
    </div>
    <form id="tao_sso_post" action="<?php echo htmlspecialchars($sso_post_url); ?>" method="POST">
        <?php foreach ($payload as $k => $v): ?>
        <input type="hidden" name="<?php echo htmlspecialchars($k); ?>" value="<?php echo htmlspecialchars($v); ?>">
        <?php endforeach; ?>
    </form>
    <script>
        document.getElementById('tao_sso_post').submit();
    </script>
</body>
</html>
