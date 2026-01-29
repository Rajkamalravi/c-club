<?php
// Optimized URL forwarding system
// Error reporting (uncomment for debugging)
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

// Initialize constants and variables

function taoh_decrypt_url_safe($data, $key)
{
    $data = hex2bin($data);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($data, 0, $ivLength);
    $cipherText = substr($data, $ivLength);
    return openssl_decrypt($cipherText, 'aes-256-cbc', "taoh-secret-key", OPENSSL_RAW_DATA, $iv);
}

taoh_define_url_constants();
$url_parts = taoh_parse_redirect_url();
$dash_mode = taoh_is_dash_mode();

//echo'<pre>';print_r($url_parts);die();

taoh_handle_autologin_route($url_parts);

// Main routing logic
if (taoh_handle_uuid_route($url_parts)) exit;
if (taoh_handle_user_route($url_parts)) exit;

// Extract route parameters
$subsecret = taoh_extract_subsecret($url_parts, $dash_mode);

if ( ! defined( 'TAOH_SUBSECRET_HASH' ) ) define( 'TAOH_SUBSECRET_HASH', $subsecret );

$login_flag = taoh_extract_login_flag($url_parts);
$ptoken = taoh_extract_ptoken($url_parts);

// Validate subsecret and handle authentication
taoh_validate_subsecret($subsecret, $dash_mode);
taoh_handle_authentication($login_flag);

// Build final redirect URL
$final_url = taoh_build_final_url($url_parts, $dash_mode);
taoh_cache_subsecret_data();
taoh_redirect_to_final_url($final_url);

// ============== HELPER FUNCTIONS ==============

/**
 * Define URL-related constants
 */
function taoh_define_url_constants() {
    if (!defined('TAOH_SITE_FWD_URL_FULL')) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        define('TAOH_SITE_FWD_URL_FULL', sprintf("%s://%s%s", $protocol, $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']));
    }
    if ( ! defined( 'TAOH_FWD_URL_ROOT' ) ) define( 'TAOH_FWD_URL_ROOT', sprintf( "%s://%s%s", isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] != 'off' ? 'https' : 'http', $_SERVER[ 'HTTP_HOST' ], dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) );


    if ( ! defined( 'TAOH_FWD_URL_ROOT' ) ) define( 'TAOH_FWD_URL_ROOT', sprintf( "%s://%s%s", isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] != 'off' ? 'https' : 'http', $_SERVER[ 'HTTP_HOST' ], dirname( $_SERVER[ 'SCRIPT_NAME' ] ) ) );


    if (!defined('TAOH_SITE_FWD_URL_ROOT')) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        define('TAOH_SITE_FWD_URL_ROOT', sprintf("%s://%s%s", $protocol, $_SERVER['HTTP_HOST'], dirname($_SERVER['SCRIPT_NAME'])));
    }

    if (!defined('TAOH_SITE_FWD_DOC_ROOT')) {
        define('TAOH_SITE_FWD_DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['SCRIPT_NAME']));
    }

    if (!defined('TAOH_SITE_FWD_REDIRECT_URL')) {
        $temp = explode(TAOH_SITE_FWD_URL_ROOT, TAOH_SITE_FWD_URL_FULL);
        define('TAOH_SITE_FWD_REDIRECT_URL', trim($temp[1] ?? '', "/"));
    }
}

/**
 * Parse the redirect URL and return clean path
 */
function taoh_parse_redirect_url() {
    $redirect_url = TAOH_SITE_FWD_REDIRECT_URL;
    return explode('?', $redirect_url, 2)[0];
}

/**
 * Check if running in dashboard mode
 */
function taoh_is_dash_mode() {
    return defined('TAO_DASH_VERSION') && TAO_DASH_VERSION;
}

/**
 * Handle auto login-based routing
 */
function taoh_handle_autologin_route($url_path) {
    if (strpos($url_path, '/auto/') === true){

        $forward_url = explode(TAOH_SITE_URL_ROOT, $url_path)[0];
        $parts = explode("/auto/", $url_path);
        if (!isset($parts[1])) return false;

        $login_token = explode("/", $parts[1])[0];


        $path_url = explode($login_token, $url_path);

        $destination = $path_url[1] ?? '';
        $final_url = TAOH_SITE_URL_ROOT . $destination;

        if(taoh_user_is_logged_in() ){


            header("Location: " . $final_url);
            taoh_exit();
            return true;
        }

        //echo '<pre>';  print_r($parts);die();
        if($login_token == '') return false;

        $app_data = taoh_app_info();
        $taoh_call = "account.temps";
        $taoh_vals = array(
        'secret'=>TAOH_API_SECRET,
        'mod'=>$app_data?->slug ?? '',
        'cmd'=>'autologin',
        'cache_required'=>0,
        'key'=>$login_token,
        // 'debug'=>1
        );
        $taoh_call_type = "get";
        //echo "====in code case=====".taoh_apicall_get_debug($taoh_call, $taoh_vals );die();
        $return = json_decode( taoh_apicall_get( $taoh_call, $taoh_vals ), true );

        //echo "============".$destination;die();

        //echo'<pre>';print_r($return);die();

        if($return['success'] &&  isset( $return['output'] ) ) {
            $api_return_token = $return['output'];
            if(TAOH_SIMPLE_LOGIN && $return['is_new_user']){
                setcookie( TAOH_ROOT_PATH_HASH.'_anonymous', 1, strtotime( '+1 days' ),'/');
            }

            setcookie( TAOH_ROOT_PATH_HASH."_temp_api_token", $return['output'], strtotime( '+1 days' ), '/'  );
            //echo $final_url;die();
            taoh_redirect($final_url);
                    taoh_exit();
        }
        else{
            taoh_redirect_with_error("/404/autologin/invalidtoken");
        }

        //https://localhost/hires-i/fwd/auto/03yz93n4jfxtagc73zoyhxs4/events/d/kalpana-test-on-publish-spsyjuw4g8t8s9e
    }
}

/**
 * Handle UUID-based routing
 */
function taoh_handle_uuid_route($url_path) {
    if (strpos($url_path, '/uu/') === false) return false;

    $parts = explode("/uu/", $url_path);
    if (!isset($parts[1])) return false;

    $uuid = explode("/", $parts[1])[0];
    $uuid_valid = taoh_uuid_fetch($uuid);

    if (!$uuid_valid) {
        taoh_redirect_with_error("/404/uuidnotvalid/uu");
    }

    $redirect_url = (isset($_GET['from']) && $_GET['from'] === 'login')
        ? TAOH_SITE_URL_ROOT . '?login=social'
        : TAOH_SITE_URL_ROOT;

    taoh_redirect($redirect_url);
    taoh_exit();
    return true;
}

/**
 * Handle user creation route (/u/)
 */
function taoh_handle_user_route($url_path) {
    if (strpos($url_path, "/u/") === false) return false;

    $forward_url = explode(TAOH_SITE_URL_ROOT, $url_path)[0];
    $login_token = taoh_user_is_logged_in() ? taoh_get_api_token() : '';

    $uuid_data = [
        'token' => $login_token,
        'source' => TAOH_SITE_URL_ROOT,
        'sub_secret_token' => TAOH_ROOT_PATH_HASH,
        'forward_url' => $forward_url,
    ];

    $uuid = taoh_create_uuid_with_retry($uuid_data);
    if (!$uuid) {
        taoh_redirect_with_error("/404/uuidcreate/fails");
    }

    $url_parse_val = taoh_parse_url_parse();
    $encodedUrl = end($url_parse_val);
    $final_url = TAOH_DASH_PREFIX . "/fwd/uu/" . $uuid."/".$encodedUrl;
    taoh_cache_subsecret_data();
    header("Location: " . $final_url);
    taoh_exit();
    return true;
}

/**
 * Create UUID with retry mechanism
 */
function taoh_create_uuid_with_retry($data, $max_attempts = 3) {
    for ($i = 0; $i < $max_attempts; $i++) {
        $result = taoh_uuid_create($data);
        if ($result !== false) return $result;
    }
    return false;
}

/**
 * Extract subsecret from URL or cookies
 */
function taoh_extract_subsecret($url_path, $dash_mode) {
    // Check URL first
    if (strpos($url_path, '/ss/') !== false) {
        $parts = explode("/ss/", $url_path);
        return isset($parts[1]) ? explode("/", $parts[1])[0] : null;
    }

    // For dashboard mode, check cookies or redirect
    if ($dash_mode) {
        if (isset($_COOKIE['taoh_api_ss'])) {
            return $_COOKIE['taoh_api_ss'];
        }
        taoh_redirect_with_error("/404/ss");
    }

    // Non-dashboard mode uses site hash
    return TAOH_SITE_ROOT_HASH ?? null;
}

/**
 * Extract login flag from URL
 */
function taoh_extract_login_flag($url_path) {
    if (strpos($url_path, '/log/') === false) return 0;

    $parts = explode("/log/", $url_path);
    return isset($parts[1]) ? (int)explode("/", $parts[1])[0] : 0;
}

/**
 * Extract ptoken from URL
 */
function taoh_extract_ptoken($url_path) {
    if (strpos($url_path, '/pt/') === false) return null;

    $parts = explode("/pt/", $url_path);
    return isset($parts[1]) ? explode("/", $parts[1])[0] : null;
}

/**
 * Validate subsecret exists and is valid
 */
function taoh_validate_subsecret($subsecret, $dash_mode) {
    if (!$subsecret) {
        taoh_redirect_with_error("/404/111");
    }

    $subsecret_info = taoh_get_subsecret_info($subsecret, 1);
    if (!$subsecret_info && $dash_mode) {
        taoh_redirect_with_error("/404/4");
    }
}

/**
 * Handle user authentication based on login flag
 */
function taoh_handle_authentication($login_flag) {
    if (!$login_flag) return;

    $token_key = TAOH_ROOT_PATH_HASH . '_taoh_api_token';
    if (empty($_COOKIE[$token_key])) {
        taoh_logout();
        header("Location: " . TAOH_SITE_URL_ROOT . "/login");
        taoh_exit();
    }
}

/**
 * Build the final redirect URL
 */
function taoh_build_final_url($url_path, $dash_mode) {
    $base_url = $dash_mode ? TAOH_SITE_URL_ROOT : TAOH_DASH_PREFIX;
    $cache_url = taoh_build_cache_url();

    return $base_url . "/" . $url_path . "?ssloc=" . urlencode($cache_url);
}

/**
 * Build cache URL for subsecret
 */
function taoh_build_cache_url() {
    $cache_file = "subsecret_" . (TAOH_SUBSECRET_HASH ?? TAOH_SITE_ROOT_HASH) . ".cache";
    return TAOH_FWD_URL_ROOT . "/cache/general/" . $cache_file;
}

/**
 * Cache subsecret data remotely
 */
function taoh_cache_subsecret_data() {
    $cache_data = [
        'value' => json_encode(taoh_subsecret_check(TAOH_ROOT_PATH_HASH)),
        'status' => TAOH_ROOT_PATH_HASH,
        'ops' => 'subsecret',
        'code' => TAOH_OPS_CODE,
    ];
    taoh_remote_cache($cache_data);
}

/**
 * Redirect to final URL and exit
 */
function taoh_redirect_to_final_url($url) {
    header("Location: " . $url);
    taoh_exit();
}

/**
 * Redirect with error message
 */
function taoh_redirect_with_error($error_path) {
    $taohVals = [
        'dateTime' => date('YmdHis'),
        'error_path' => $error_path,
    ];
    taoh_create_log($taohVals);
    $lastUrl = taoh_decrypt_url_safe(end(taoh_parse_url_parse()), "taoh-secret-key");
    header("Location: " . $lastUrl);
    taoh_exit();
}

function taoh_create_log($values) {
    $value_json = json_encode($values);
    $postData = [
        'code' => 'tc2asi3iida2',
        'ops' => 'append',
        'value' => $value_json,
        'status' => 'post',
        'key' => 'errorLog_'.date('Ymd')
    ];

    $result = taoh_remote_cache($postData);
    $return = json_decode($result, true);
    return $return['output'] ?? false;
}

// ============== UTILITY FUNCTIONS ==============

/**
 * Create UUID with validation
 */
function taoh_uuid_create($data) {
    $json_data = json_encode($data);

    while (true) {
        // Create UUID
        $post_data = [
            'code' => 'tc2asi3iida2',
            'ops' => 'uuid',
            'value' => $json_data,
            'status' => 'post',
        ];

        $result = taoh_remote_cache($post_data);
        $response = json_decode($result, true);
        $uuid = $response['output'] ?? null;

        if (!$uuid) return false;

        // Validate UUID
        $validation_data = [
            'code' => 'tc2asi3iida2',
            'ops' => 'uuid',
            'value' => $uuid,
            'status' => 'get',
        ];

        $validation_result = taoh_remote_cache($validation_data);
        $validation_response = json_decode($validation_result, true);

        if ($json_data === ($validation_response['output'] ?? null)) {
            return $uuid;
        }
    }
}

/**
 * Fetch and validate UUID
 */
function taoh_uuid_fetch($uuid) {
    $post_data = [
        'code' => 'tc2asi3iida2',
        'ops' => 'uuid',
        'value' => $uuid,
        'status' => 'get',
    ];

    $result = taoh_remote_cache($post_data);
    $response = json_decode($result, true);
    $data = json_decode($response['output'] ?? '{}', true);

    if (!is_array($data) || empty($data['output'])) return false;

    $redirect_set = false;
    $login_from_social = false;
    $token = '';
    $new_user = false;

    // Process returned data and set cookies
    foreach ($data['output'] as $key => $value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $cookie_key = TAOH_ROOT_PATH_HASH . '_' . $key;
        setcookie($cookie_key, $value, strtotime('+2 days'), '/');

        switch ($key) {
            case 'taoh_api_token':
                $redirect_set = true;
                $token = $value;
                break;
            case 'from':
                $login_from_social = true;
                break;
            case 'new_user':
                $new_user = ($value === '1');
                break;
        }
    }

    // Handle new user setup for simple login
    if ($token && $login_from_social && defined('TAOH_SIMPLE_LOGIN') && TAOH_SIMPLE_LOGIN && $new_user) {
        taoh_setup_anonymous_user($token);
    }

    return $redirect_set;
}

/**
 * Setup anonymous user for simple login
 */
function taoh_setup_anonymous_user($token) {
    // Get random avatar
    $avatar_files = glob(realpath('./assets/images/avatar/PNG/128') . '/anonymous-*.png');
    if (!empty($avatar_files)) {
        $random_file = $avatar_files[array_rand($avatar_files)];
        $avatar_name = pathinfo($random_file, PATHINFO_FILENAME);
    } else {
        $avatar_name = 'anonymous-default';
    }

    $user_data = taoh_user_all_info_settings($token, 1);

    $user_setup = [
        'simple_login' => TAOH_SIMPLE_LOGIN,
        'profile_complete' => 0,
        'created_via' => 'social',
        'avatar' => $avatar_name,
        'type' => 'professional',
        'chat_name' => $user_data->fname ?? 'Anonymous',
        'local_timezone' => $_COOKIE['client_time_zone'] ?? 'UTC',
    ];

    $api_data = [
        'token' => $token,
        'mod' => 'tao_tao',
        'toenter' => $user_setup,
    ];

    taoh_apicall_post('users.tao.add', $api_data);
}

/**
 * Load subsecret configuration from cache or create new
 */
function taoh_subsecret_check($subsecret_identifier) {
    if (strpos($subsecret_identifier, 'http') !== false) {
        return taoh_load_subsecret_from_url($subsecret_identifier);
    }
    return taoh_load_subsecret_from_code($subsecret_identifier);
}

/**
 * Load subsecret from URL
 */
function taoh_load_subsecret_from_url($url) {
    $result = json_decode(file_get_contents($url), true);

    if (!is_array($result) || empty($result)) return false;

    $cache_file = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" . TAOH_SUBSECRET_HASH . ".cache";

    // Update cache if old or missing
    if (!file_exists($cache_file) || (time() - filemtime($cache_file)) >= 86400) {
        if (file_exists($cache_file)) unlink($cache_file);
        file_put_contents($cache_file, json_encode($result));
    }

    // Define constants from result
    foreach ($result as $key => $value) {
        if (!defined($key)) define($key, $value);
    }

    return true;
}

/**
 * Load subsecret from code with caching
 */
function taoh_load_subsecret_from_code($code) {
    $cache_file = TAOH_PLUGIN_PATH . "/cache/general/subsecret_" . $code . ".cache";
    $result = null;

    // Check existing cache
    if (file_exists($cache_file)) {
        if ((time() - filemtime($cache_file)) >= 86400) {
            unlink($cache_file);
        } else {
            $result = json_decode(file_get_contents($cache_file), true);
        }
    }

    // Create new cache if needed
    if (!file_exists($cache_file)) {
        $result = taoh_get_subsecret_info($code, 1);
        if ($result) {
            file_put_contents($cache_file, json_encode($result));
        }
    }

    // Define constants from result
    if (is_array($result)) {
        foreach ($result as $key => $value) {
            if (!defined($key)) define($key, $value);
        }
        return $result;
    }

    return false;
}

exit();