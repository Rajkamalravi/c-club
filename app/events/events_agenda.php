<?php
//echo "===ssssssssss";die();
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = $taoh_user_is_logged_in ? taoh_user_all_info() : null;

$full_location = (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->full_location ?? '';
$user_country_array = explode(',', $full_location);
$user_country_name = trim(end($user_country_array));

$valid_user = $taoh_user_is_logged_in && in_array($user_info_obj?->profile_complete ?? null, [1, '1'], true);
$current_app = taoh_parse_url(1);

$cache_name = 'event_Saved_' . $eventtoken;
$taoh_call = "events.content.save.list";
$type = '';
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $eventtoken,
    'cache_required' => 0
);
$data = taoh_apicall_get($taoh_call, $taoh_vals);
$data = json_decode($data, true);

$likedArr = [];
if (!empty($data['output']) && is_array($data['output'])) {
    foreach ($data['output'] as $key => $saveddataArr) {
        if (!is_array($saveddataArr)) continue;

        foreach ($saveddataArr as $dkey => $saveddata) {
            if (is_array($saveddata) && isset($saveddata['ID'])) {
                $likedArr[$key][] = $saveddata['ID'];
            }
        }
    }
}
?>
<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/css/events-agenda.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">
<script>
    window._ega_cfg = {
        myPtoken: <?= json_encode((taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''); ?>,
        eventToken: <?= json_encode($event_token ?? ''); ?>,
        appSlug: <?= json_encode(TAO_PAGE_TYPE); ?>,
        likedArr: <?= json_encode($likedArr ?? []); ?>,
        currentApp: <?= json_encode($current_app); ?>,
        userCountryName: <?= json_encode($user_country_name ?? ''); ?>,
        isLoggedIn: <?= json_encode($taoh_user_is_logged_in); ?>,
        userTimezone: <?= json_encode($taoh_user_is_logged_in ? taoh_user_timezone() : ''); ?>,
        cdnPrefix: <?= json_encode(TAOH_CDN_PREFIX); ?>,
        currAppSlug: <?= json_encode(TAOH_CURR_APP_SLUG); ?>
    };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/js/events-agenda.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
</script>