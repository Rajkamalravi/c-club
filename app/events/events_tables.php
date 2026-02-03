<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-tables.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
<script>
    window._etb_cfg = {
        myPtoken: <?= json_encode((taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''); ?>,
        tableUrlIndex: <?= json_encode(TAOH_SITE_URL_ROOT . '/events/d/event-' . $eventtoken . '/'); ?>,
        cdnPrefix: <?= json_encode(TAOH_CDN_PREFIX); ?>,
        currentAppPage: <?= json_encode(TAO_CURRENT_APP_INNER_PAGE); ?>,
        tablesImgUrl: <?= json_encode(TAO_TABLES_URL . '/images/tables_im_sq.svg'); ?>,
        isLoggedIn: <?= json_encode(taoh_user_is_logged_in()); ?>,
        isEventsLobby: <?= json_encode(TAO_CURRENT_APP_INNER_PAGE == 'events_lobby'); ?>,
        tablesCreateUrl: <?= json_encode(TAOH_SITE_URL_ROOT . '/events/d/event-' . $eventtoken . '/tables/create'); ?>,
        tablesUrl: <?= json_encode(TAOH_SITE_URL_ROOT . '/events/d/event-' . $eventtoken . '/tables'); ?>,
        userTimezone: <?= json_encode(taoh_user_is_logged_in() ? taoh_user_timezone() : ''); ?>
                };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/events-tables.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
