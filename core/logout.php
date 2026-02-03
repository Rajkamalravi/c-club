<?php

 setcookie('tao_login_try', 1, strtotime('+1 days'), '/');
    setcookie('tao_login_stamp', time(), strtotime('-1 days'), '/');


    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'temp_api_token', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'locked', 1, strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'enable_lock_screen', 1, strtotime('-2 days'), '/');


    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_api_token']);
    }
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'taoh_page_url']);
    }
    if (isset($_COOKIE['tao_api_email']) && isset($_COOKIE['tao_api_email'])) {
        setcookie('tao_api_email', '', strtotime('-1 days'), '/');
        unset($_COOKIE['tao_api_email']);
    }
    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email']) && isset($_COOKIE['tao_api_email'])) {
        setcookie(TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email', '', strtotime('-1 days'), '/');
        unset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'tao_api_email']);
    }
    unset(taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO']);
    unset($_SESSION[TAOH_ROOT_PATH_HASH]['USER_INFO']);

    // Clear SSO shared cookies on .unmeta.net domain
    $sso_cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '.unmeta.net';
    $sso_clear_opts = ['expires' => time() - 3600, 'path' => '/', 'domain' => $sso_cookie_domain, 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
    setcookie('tao_email', '', $sso_clear_opts);
    setcookie('tao_user_key', '', $sso_clear_opts);
    setcookie('tao_logged_in', '', $sso_clear_opts);
    setcookie('tao_profile_info', '', $sso_clear_opts);
    setcookie('email', '', $sso_clear_opts);
    setcookie('user_key', '', $sso_clear_opts);
    setcookie('login_vars', '', $sso_clear_opts);

    taoh_set_success_message('You have successfully logged out!');

    //delete_refer_token();

    if (isset($_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token']) && $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'] != '') {
        $refer_token = $_COOKIE[TAOH_ROOT_PATH_HASH . '_' . 'refer_token'];
        $taoh_call = "core.refer.update";
        $taoh_vals = array(
            "mod" => "invite",
            'refer_id' => $refer_token,
            'refer_token' => $refer_token,
            'secret' => TAOH_API_SECRET,
            'token' => taoh_get_dummy_token(),
            'delete' => 1,

        );
        //echo taoh_apicall_post_debug( $taoh_call, $taoh_vals );exit();
        $res = taoh_apicall_post($taoh_call, $taoh_vals);
    }
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'refer_token', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_back_url', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'referral_data', '', strtotime('-2 days'), '/');
    setcookie(TAOH_ROOT_PATH_HASH . '_' . 'from_referral', '', strtotime('-2 days'), '/');

    // Redirect browser to werify.ai to clear its cookies, then it redirects back here
    $sso_base_url = defined('TAOH_SSO_BASE_URL') ? TAOH_SSO_BASE_URL : 'https://login.tao.ai';
    $return_url = TAOH_SITE_URL_ROOT;
    taoh_redirect($sso_base_url . '/logout.php?confirm=yes&redirect=' . urlencode($return_url));
    taoh_exit();
?>