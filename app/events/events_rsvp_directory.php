<?php
$sess_user_info = taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null;
    $full_loc_expl = explode(', ', $sess_user_info?->full_location ?? '');
    $user_country = array_pop($full_loc_expl);


$erd_my_following_list = [];
$erd_my_following_ptoken_list = [];
if ($sess_user_info) {
    $erd_my_ptoken = $sess_user_info->ptoken ?? '';

    $taoh_vals = [
        'mod' => 'core',
        'token' => taoh_get_api_token(),
        'ptoken' => $erd_my_ptoken,
        'follow_type' => 'following',
    ];
    $taoh_vals['cache_name'] = 'followup_' . $taoh_vals['follow_type'] . '_list_' . $taoh_vals['ptoken'] . '_' . hash('crc32', http_build_query($taoh_vals));

//    $taoh_vals['cache_required'] = 0;
//     $taoh_vals['debug_api'] = 1;
//     echo taoh_apicall_get('core.followup.get.list', $taoh_vals);exit();

    $followup_result = taoh_apicall_get('core.followup.get.list', $taoh_vals);
    $followup_result_array = json_decode($followup_result, true);
    if ($followup_result_array && in_array($followup_result_array['success'], [true, 'true']) && !empty($followup_result_array['output'])) {
        $erd_my_following_list = (array)$followup_result_array['output'];
        $erd_my_following_ptoken_list = array_column($erd_my_following_list, 'ptoken');
    }
}

$eventlivelink = TAOH_SITE_URL_ROOT . '/' . TAOH_SITE_CURRENT_APP_SLUG . '/club/' . slugify2(TAO_PAGE_TITLE) . '-' . $eventtoken;
 
?>

<link rel="stylesheet" href="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/css/events-rsvp-directory.css?v=<?php echo TAOH_CSS_JS_VERSION; ?>">

<script>
    window._erd_cfg = {
        myPtoken: <?= json_encode((taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''); ?>,
        eventLiveLink: <?= json_encode(isset($eventlivelink) ? $eventlivelink : ''); ?>,
        myFollowingPtokenList: <?= json_encode($erd_my_following_ptoken_list ?? []); ?>,
        siteNameSlug: <?= json_encode(TAOH_SITE_NAME_SLUG); ?>,
        eventtoken: <?= json_encode($eventtoken ?? ''); ?>,
        userCountry: <?= json_encode($user_country ?? ''); ?>
        };
</script>
<script src="<?php echo TAOH_SITE_URL_ROOT; ?>/assets/events/js/events-rsvp-directory.js?v=<?php echo TAOH_CSS_JS_VERSION; ?>"></script>
