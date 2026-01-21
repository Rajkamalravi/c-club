# Portable SSO Client Module

**Drag-and-drop SSO integration for any website using login.tao.ai**

This module provides seamless Single Sign-On (SSO) integration with the login.tao.ai authentication system. Simply copy this folder to any domain and update the configuration.

## 🚀 Quick Start

### 1. Copy Files

Copy the entire `/login/` folder to your website root:

```bash
# Your website structure:
/var/www/yoursite.com/
├── login/
│   ├── config.php        # ← Configure this
│   ├── index.php         # Main handler
│   ├── logout.php        # Logout handler
│   ├── error.php         # Error page
│   └── README.md         # This file
```

### 2. Configure

Edit `config.php` and update these 3 lines:

```php
// Site configuration
define('SITE_NAME', 'Your Site Name');          // Your site's name
define('SITE_URL', 'https://yoursite.com');     // Your site's URL
define('SITE_EMAIL', 'support@yoursite.com');   // Support email
```

That's it! Your site now has SSO.

---

## 📖 How It Works

### User Flow

```
1. User visits: https://yoursite.com/login
2. Redirects to: login.tao.ai (email verification)
3. Callback to: https://yoursite.com/login (with credentials)
4. User is logged in with cookies set
```

### Cookie Names

The module sets these cookies (aligned with SSO server):

- `email` - User's email address
- `user_key` - Authentication key
- `tao_email` - Legacy cookie (for backwards compatibility)
- `tao_user_key` - Legacy cookie
- `tao_logged_in` - Login status flag

All cookies:
- Domain: `.tao.ai` (works across subdomains)
- Expiration: 30 days
- Secure: Yes (HTTPS only)
- HttpOnly: Yes (XSS protection)
- SameSite: Lax (CSRF protection)

---

## 🔧 Advanced Configuration

### Custom Callback URL

```php
// Default callback is SITE_URL/login/
define('SSO_CALLBACK_URL', SITE_URL . '/custom-callback');
```

### Custom Error Page

```php
// Default error page is SITE_URL/login/error.php
define('SSO_CALLBACK_FAIL_URL', SITE_URL . '/custom-error-page');
```

### Custom Cookie Domain

```php
// Default is .tao.ai (works for all *.tao.ai subdomains)
define('COOKIE_DOMAIN', '.yourdomain.com');
```

---

## 🔐 3 Core Scenarios

### Scenario 1: Login [self]
**Description:** User logs into your site

```php
// User visits:
https://yoursite.com/login

// Flow:
1. index.php checks if user logged in
2. If not → POST to login.tao.ai
3. User verifies email
4. Callback to index.php with credentials
5. Cookies set → user logged in
```

### Scenario 2: Login [callfwd]
**Description:** Forward user from Site A → login.tao.ai → Site B

```php
// User visits Site A with forward parameter:
https://sitea.com/login?fwd_url=https://siteb.com

// Flow:
1. Site A: User already logged in (has cookies)
2. Site A: POST to login.tao.ai with callfwdurl=siteb.com
3. login.tao.ai: Validates credentials
4. login.tao.ai: POST to siteb.com/login with credentials
5. Site B: Validates with login.tao.ai/sso.php
6. Site B: Sets cookies → user logged in
```

### Scenario 3: Logout [self]
**Description:** User logs out from your site and SSO server

```php
// User clicks logout:
https://yoursite.com/login/logout.php

// Flow:
1. logout.php clears local cookies
2. Sends async notification to login.tao.ai/logout.php
3. login.tao.ai clears SSO cookies
4. Redirects to confirmation page
```

---

## 🛠️ API Reference

### Check if User is Logged In

```php
require_once __DIR__ . '/login/index.php';

if (is_logged_in()) {
    echo "Welcome! Email: " . $_COOKIE['email'];
} else {
    header('Location: /login');
}
```

### Protect Pages

Add this to the top of protected pages:

```php
<?php
require_once __DIR__ . '/login/index.php';

if (!is_logged_in()) {
    header('Location: /login?redirect_url=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Protected content below
?>
```

### Forward to Another Site

```php
// On your page, add a forward button:
<a href="/login?fwd_url=https://othersite.com" class="btn">
    Forward to Other Site
</a>
```

### Manual Logout

```php
// Link to logout:
<a href="/login/logout.php">Logout</a>

// Or with custom redirect:
<a href="/login/logout.php?redirect_url=/goodbye">Logout</a>
```

---

## 🧪 Testing

### Test Login Flow

1. Visit: `https://yoursite.com/login`
2. Enter email address
3. Verify code from email
4. Should redirect back to your site with cookies set

### Test Forward Flow

1. Visit: `https://sitea.com/login?fwd_url=https://siteb.com`
2. If logged in to Site A → automatically forward to Site B
3. Should be logged in to Site B with same credentials

### Test Logout

1. Click logout link
2. Should clear cookies and redirect
3. Visit `/login` again → should require re-authentication

---

## 🔒 Security Features

- ✅ **Rate Limiting:** 10 requests/minute per IP
- ✅ **CSRF Protection:** Token-based validation
- ✅ **XSS Protection:** Output sanitization
- ✅ **Open Redirect Protection:** Domain whitelist
- ✅ **Secure Cookies:** HttpOnly, Secure, SameSite
- ✅ **Input Validation:** Email, URL, ops parameter
- ✅ **HTTPS Enforcement:** All cookies require HTTPS

---

## 📝 Error Handling

Errors are displayed on `error.php` with user-friendly messages:

**Common Error Codes:**
- `INVALID_EMAIL` - Email format is invalid
- `INVALID_USER_KEY` - Authentication key mismatch
- `SESSION_EXPIRED` - Session timed out (re-login required)
- `RATE_LIMIT` - Too many requests (wait 5 minutes)
- `AUTH_FAILED` - Authentication failed

---

## 🎨 Customization

### Modify Error Page

Edit `error.php` to match your site's design:

```php
// Change colors, logo, messaging
$site_name = defined('SITE_NAME') ? SITE_NAME : 'Website';
$site_url = defined('SITE_URL') ? SITE_URL : '/';
```

### Modify Logout Page

Edit `logout.php` to customize the logout experience:

```php
// Change redirect time (default: 3 seconds)
<meta http-equiv="refresh" content="3;url=...">

// Change countdown
let seconds = 3; // in JavaScript
```

---

## 🐛 Troubleshooting

### Cookies Not Setting

**Problem:** Cookies not being set after login

**Solution:**
1. Ensure `COOKIE_DOMAIN` matches your domain
2. Check HTTPS is enabled (cookies require secure connection)
3. Verify `SITE_URL` in config.php is correct

### Infinite Redirect Loop

**Problem:** Keeps redirecting between login and your site

**Solution:**
1. Check `SSO_CALLBACK_URL` in config.php
2. Ensure callback URL is in whitelist on login.tao.ai
3. Check browser console for JavaScript errors

### "Invalid callback URL" Error

**Problem:** Getting callback URL error

**Solution:**
1. Verify your domain is in `ALLOWED_REDIRECT_DOMAINS` on login.tao.ai
2. Contact admin to whitelist your domain

### Rate Limit Error

**Problem:** "Too many requests" error

**Solution:**
1. Wait 5 minutes (rate limit: 10 req/min per IP)
2. Don't refresh login page repeatedly
3. Check for automated scripts hitting the endpoint

---

## 📞 Support

**Questions?** Contact: support@tao.ai

**Documentation:** https://login.tao.ai/

**GitHub Issues:** (if applicable)

---

## 📄 License

This SSO client module is part of the Tao.ai authentication system.

© 2024 Tao.ai - All rights reserved.

---

## 🔄 Changelog

### Version 2.0 (2024-11-19)
- ✅ Complete rewrite with security hardening
- ✅ Cookie alignment with SSO server
- ✅ ops parameter support (login/logout/validate)
- ✅ callfailurl error handling
- ✅ Rate limiting per IP
- ✅ Comprehensive error messages
- ✅ JSON API support
- ✅ Payload preservation
- ✅ Multi-site logout support

### Version 1.0 (2024-10-19)
- Initial release
- Basic SSO integration
- Email verification

---

**🎉 You're all set! Your site now has enterprise-grade SSO authentication.**
