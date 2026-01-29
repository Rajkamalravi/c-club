<?php
/**
 * EventSail Dev - Configuration
 */


// Site configuration
define('SITE_NAME', 'Unmeta');
define('SITE_URL', 'https://unmeta.net/club');
define('SITE_EMAIL', 'info@tao.ai');

// Only define if not already defined by parent config.php
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://unmeta.net/club');
}


// Production mode
define('DEBUG_MODE', false);

// Timezone
date_default_timezone_set('UTC');

// SSO Configuration (TAO.ai Login Integration)
define('APP_NAME', SITE_NAME);
define('APP_EMOJI', '⛵');
define('APP_LOGO_URL', 'https://cdn.tao.ai/assets/wertual/images/worker1_sq.png');
define('SITE_LOGO', APP_LOGO_URL); // Alias
define('POST_LOGIN_URL', SITE_URL.'/sso-login'); // Redirect to sso-login route after login
define('SSO_BASE_URL', 'https://login.tao.ai');

// SSO Callback Configuration
define('SSO_CALLBACK_URL', SITE_URL . '/login-new/');
define('SSO_CALLBACK_FAIL_URL', SITE_URL . '/login-new/error.php');
define('SSO_VALIDATE_URL', SSO_BASE_URL . '/sso.php');
//define('SSO_FORWARD_URL', 'https://moat.page/tools/login.php');
//define('SSO_REDIRECT_URL', SSO_FORWARD_URL);

// Forward Button Configuration
//define('FORWARD_TEXT', 'Forward to Moat.page');

// Cookie Configuration (aligned with SSO server)
// Use '.unmeta.net' to share cookies across subdomains, or leave empty for current host only
define('COOKIE_DOMAIN', '.unmeta.net');
define('SSO_USER_KEY_COOKIE', 'user_key');  // Match server cookie name
define('SSO_EMAIL_COOKIE', 'email');         // Match server cookie name
define('SSO_LOGIN_COOKIE', 'login_vars');    // Main login cookie from server
