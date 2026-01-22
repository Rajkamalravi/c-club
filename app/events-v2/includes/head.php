<?php
/**
 * Events V2 - Head Section
 *
 * Bootstrap 5.3 and custom CSS/JS includes
 *
 * Usage: Set these variables BEFORE including this file:
 * - $page_title (optional)
 * - $page_description (optional)
 * - $page_image (optional)
 * - $page_url (optional)
 * - $page_css (optional) - page-specific CSS file name without extension (e.g., 'listing', 'detail', 'rsvp')
 */

// Get page-specific variables if set
$page_title = $page_title ?? 'Events';
$page_description = $page_description ?? 'Discover and join amazing events';
$page_image = $page_image ?? TAOH_SITE_URL_ROOT . '/assets/images/event.jpg';
$page_url = $page_url ?? TAOH_EVENTS_V2_URL;
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($page_image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($page_url); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($page_image); ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo TAOH_SITE_FAVICON; ?>">

    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Events V2 CSS -->
    <link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-v2/variables.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
    <link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-v2/main.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
    <link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-v2/components.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">

    <?php if (isset($page_css) && !empty($page_css)): ?>
    <link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-v2/<?php echo htmlspecialchars($page_css); ?>.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
    <?php endif; ?>

    <!-- Luxon for datetime handling -->
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.4.4/build/global/luxon.min.js"></script>

    <!-- Global JS Variables -->
    <script>
        const SITE_URL = '<?php echo TAOH_SITE_URL_ROOT; ?>';
        const EVENTS_V2_URL = '<?php echo TAOH_EVENTS_V2_URL; ?>';
        const API_PREFIX = '<?php echo TAOH_API_PREFIX; ?>';
        const USER_TIMEZONE = '<?php echo taoh_user_timezone(); ?>';
        const IS_LOGGED_IN = <?php echo taoh_user_is_logged_in() ? 'true' : 'false'; ?>;
        const LOGIN_URL = '<?php echo TAOH_LOGIN_URL; ?>';
    </script>
