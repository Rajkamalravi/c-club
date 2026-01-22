<?php
/**
 * Events V2 - Error Page
 *
 * Displayed when event is not found or other errors occur
 */

$error_title = $error_title ?? 'Event Not Found';
$error_message = $error_message ?? 'The event you\'re looking for doesn\'t exist or may have been removed.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/head.php'); ?>
    <title><?php echo htmlspecialchars($error_title); ?> | <?php echo TAOH_SITE_TITLE; ?></title>
    <style>
        .ev2-error-page {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: var(--ev2-spacing-8);
        }
        .ev2-error-content {
            max-width: 500px;
        }
        .ev2-error-icon {
            width: 120px;
            height: 120px;
            background-color: var(--ev2-gray-100);
            border-radius: var(--ev2-radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--ev2-spacing-6);
        }
        .ev2-error-icon i {
            font-size: 48px;
            color: var(--ev2-gray-400);
        }
        .ev2-error-content h1 {
            font-size: var(--ev2-font-size-2xl);
            font-weight: var(--ev2-font-weight-bold);
            margin-bottom: var(--ev2-spacing-3);
            color: var(--ev2-gray-800);
        }
        .ev2-error-content p {
            color: var(--ev2-gray-600);
            margin-bottom: var(--ev2-spacing-6);
        }
        .ev2-error-actions {
            display: flex;
            gap: var(--ev2-spacing-3);
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body class="ev2-body">
    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/header.php'); ?>

    <main class="ev2-error-page">
        <div class="ev2-error-content">
            <div class="ev2-error-icon">
                <i class="bi bi-calendar-x"></i>
            </div>
            <h1><?php echo htmlspecialchars($error_title); ?></h1>
            <p><?php echo htmlspecialchars($error_message); ?></p>
            <div class="ev2-error-actions">
                <a href="<?php echo TAOH_EVENTS_V2_URL; ?>" class="btn btn-primary">
                    <i class="bi bi-grid me-2"></i>Browse Events
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Go Back
                </button>
            </div>
        </div>
    </main>

    <?php include(TAOH_PLUGIN_PATH . '/app/events-v2/includes/footer.php'); ?>
</body>
</html>
