<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var _taoh_user_timezone = "<?= taoh_user_timezone(); ?>";
    var _taoh_cdn_prefix = "<?php echo TAOH_CDN_PREFIX; ?>";
    var _taoh_curr_app_slug = "<?php echo TAOH_CURR_APP_SLUG; ?>";
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-footer.js?v=<?php echo time(); ?>"></script>
