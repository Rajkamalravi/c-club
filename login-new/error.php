<?php
/**
 * SSO Client Error Page
 *
 * Portable error page for SSO authentication failures.
 * Displays user-friendly error messages from login.tao.ai
 *
 * PORTABLE: Can be drag-dropped to any domain
 * Configure via config.php
 */

require_once __DIR__ . '/config.php';

// Get error details from query parameters
$error_code = isset($_GET['error_code']) ? htmlspecialchars($_GET['error_code']) : 'UNKNOWN_ERROR';
$error_message = isset($_GET['error_message']) ? htmlspecialchars($_GET['error_message']) : 'An authentication error occurred.';
$timestamp = isset($_GET['timestamp']) ? intval($_GET['timestamp']) : time();

// Friendly error messages
$friendly_messages = [
    'INVALID_EMAIL' => 'The email address format is invalid.',
    'INVALID_OPS' => 'Invalid operation requested.',
    'INVALID_CALLBACK_URL' => 'The callback URL is not authorized.',
    'INVALID_USER_KEY' => 'Authentication key is invalid.',
    'SESSION_EXPIRED' => 'Your session has expired. Please log in again.',
    'CSRF_TOKEN' => 'Security verification failed. Please try again.',
    'RATE_LIMIT' => 'Too many login attempts. Please wait a few minutes.',
    'AUTH_FAILED' => 'Authentication failed. Please try again.',
    'MISSING_PARAMS' => 'Required information is missing.',
    'SYSTEM_ERROR' => 'A system error occurred. Please try again later.',
    'missing_credentials' => 'Missing email or authentication key.',
    'invalid_ops' => 'Invalid operation.',
];

$display_message = isset($friendly_messages[$error_code]) ? $friendly_messages[$error_code] : $error_message;

// Get site info from config
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Website';
$site_url = defined('SITE_URL') ? SITE_URL : '/';
$sso_base = defined('SSO_BASE_URL') ? SSO_BASE_URL : 'https://login.tao.ai';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Error - <?php echo htmlspecialchars($site_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .error-code {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 1.5rem 0;
            font-size: 0.9rem;
            color: #6c757d;
            border-left: 4px solid #dc3545;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .timestamp {
            font-size: 0.75rem;
            color: #adb5bd;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <h2 class="mb-3">Authentication Error</h2>

        <p class="text-muted mb-4"><?php echo $display_message; ?></p>

        <div class="error-code">
            <strong>Error Code:</strong> <?php echo $error_code; ?>
        </div>

        <div class="d-grid gap-2 mt-4">
            <a href="<?php echo htmlspecialchars($site_url . '/login'); ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
            </a>
            <a href="<?php echo htmlspecialchars($site_url); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-house-door me-2"></i>Back to Home
            </a>
        </div>

        <div class="timestamp">
            <i class="bi bi-clock me-1"></i>
            <?php echo date('Y-m-d H:i:s', $timestamp); ?>
        </div>

        <div class="mt-4">
            <small class="text-muted">
                If this error persists, please contact support.<br>
                <a href="mailto:<?php echo defined('SITE_EMAIL') ? SITE_EMAIL : 'support@tao.ai'; ?>" class="text-decoration-none">
                    <?php echo defined('SITE_EMAIL') ? SITE_EMAIL : 'support@tao.ai'; ?>
                </a>
            </small>
        </div>

        <div class="mt-3">
            <small class="text-muted">
                Powered by <a href="<?php echo htmlspecialchars($sso_base); ?>" class="text-decoration-none" target="_blank">Tao.ai SSO</a>
            </small>
        </div>
    </div>
</body>
</html>
