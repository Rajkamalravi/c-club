<?php
/**
 * SSO Client Logout Handler
 *
 * Portable logout handler that:
 * 1. Clears local cookies
 * 2. Notifies login.tao.ai SSO server
 * 3. Redirects to logout confirmation
 *
 * PORTABLE: Can be drag-dropped to any domain
 * Configure via config.php
 */

require_once __DIR__ . '/config.php';

error_log('[logout.php] ========== LOGOUT INITIATED ==========');

// Get site configuration
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Website';
$site_url = defined('SITE_URL') ? SITE_URL : '/';
$cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
$sso_base_url = defined('SSO_BASE_URL') ? SSO_BASE_URL : 'https://login.tao.ai';

// Get redirect URL (where to go after logout)
$redirect_url = isset($_GET['redirect_url']) ? $_GET['redirect_url'] : $site_url;

// Clear all local cookies
$cookie_options = [
    'expires' => time() - 3600, // Expire in the past
    'path' => '/',
    'domain' => $cookie_domain,
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
];

// Clear legacy cookies
setcookie('tao_email', '', $cookie_options);
setcookie('tao_user_key', '', $cookie_options);
setcookie('tao_logged_in', '', $cookie_options);
setcookie('tao_profile_info', '', $cookie_options);

// Clear aligned cookies
setcookie('email', '', $cookie_options);
setcookie('user_key', '', $cookie_options);
setcookie('login_vars', '', $cookie_options);

// Also clear cookies without domain (in case they were set with empty domain before)
$cookie_options_no_domain = $cookie_options;
$cookie_options_no_domain['domain'] = '';
setcookie('tao_email', '', $cookie_options_no_domain);
setcookie('tao_user_key', '', $cookie_options_no_domain);
setcookie('tao_logged_in', '', $cookie_options_no_domain);
setcookie('tao_profile_info', '', $cookie_options_no_domain);
setcookie('email', '', $cookie_options_no_domain);
setcookie('user_key', '', $cookie_options_no_domain);
setcookie('login_vars', '', $cookie_options_no_domain);

// Unset from $_COOKIE array
unset($_COOKIE['tao_email']);
unset($_COOKIE['tao_user_key']);
unset($_COOKIE['tao_logged_in']);
unset($_COOKIE['tao_profile_info']);
unset($_COOKIE['email']);
unset($_COOKIE['user_key']);
unset($_COOKIE['login_vars']);

error_log('[logout.php] Local cookies cleared');

// Notify SSO server about logout (async - don't wait for response)
$logout_notify_url = $sso_base_url . '/logout.php';
$notify_params = [
    'client_site' => $site_url,
    'timestamp' => time()
];

// Send async notification (fire and forget)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $logout_notify_url . '?' . http_build_query($notify_params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); // 500ms timeout
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_exec($ch);
curl_close($ch);

error_log('[logout.php] SSO server notified');

// Check if this is an AJAX request (return JSON)
$is_ajax = (
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
) || (isset($_GET['format']) && $_GET['format'] === 'json');

if ($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully',
        'redirect_url' => $redirect_url
    ]);
    exit;
}

// Display logout confirmation page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - <?php echo htmlspecialchars($site_name); ?></title>
    <meta http-equiv="refresh" content="3;url=<?php echo htmlspecialchars($redirect_url); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .logout-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .success-icon {
            font-size: 5rem;
            color: #10B981;
            margin-bottom: 1.5rem;
            animation: checkmark 0.8s ease-in-out;
        }
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        .countdown {
            font-size: 1.5rem;
            font-weight: bold;
            color: #10B981;
            margin: 1rem 0;
        }
        .btn-primary {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <h2 class="mb-3">Successfully Logged Out</h2>

        <p class="text-muted mb-4">
            You have been logged out of <?php echo htmlspecialchars($site_name); ?> and the Tao.ai SSO system.
        </p>

        <div class="countdown">
            <i class="bi bi-clock-history me-2"></i>
            <span id="countdown">3</span>
        </div>

        <p class="text-muted">Redirecting automatically...</p>

        <div class="mt-4">
            <a href="<?php echo htmlspecialchars($redirect_url); ?>" class="btn btn-primary">
                <i class="bi bi-arrow-right me-2"></i>Continue Now
            </a>
        </div>

        <div class="mt-4">
            <small class="text-muted">
                <a href="<?php echo htmlspecialchars($site_url . '/login'); ?>" class="text-decoration-none">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Sign in again
                </a>
            </small>
        </div>
    </div>

    <script>
        let seconds = 3;
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
            }
        }, 1000);
    </script>
</body>
</html>
