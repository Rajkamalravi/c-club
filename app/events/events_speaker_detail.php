<?php
defined('TAOH_CURR_APP_SLUG') || define('TAOH_CURR_APP_SLUG', 'events');

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

$taoh_call = "events.content.detail";
$cache_name = 'event_MetaInfo_' . $_POST['eventtoken'] . '_speaker_' . $_POST['speaker_id'];
$taoh_vals = array(
    'mod' => 'events',
    'token' => taoh_get_dummy_token(),
    'eventtoken' => $_POST['eventtoken'],
    'meta_id' => $_POST['speaker_id'],
    'cache_name' => $cache_name,
    //'cfcc5h'=> 1, //cfcache newly added
);
//echo taoh_apicall_get_debug($taoh_call, $taoh_vals);die();
$response = json_decode(taoh_apicall_get($taoh_call, $taoh_vals), true);
$speaker_data = $response['output'];

$speaker_key = array_search($_POST['speaker_name'], $speaker_data['spk_name']);

$user_timezone = taoh_user_timezone();
$is_user_rsvp_done = 1;
if (defined('TAOH_API_TOKEN')) {
    // Get RSVP status
    $taoh_vals = array(
        'ops' => 'status',
        'mod' => 'events',
        'token' => TAOH_API_TOKEN,
        'eventtoken' => $_POST['eventtoken'],
        'cache_required' => 0,
    );
    $rsvp_status_result = taoh_apicall_get('events.rsvp.get', $taoh_vals);
    $rsvp_status_response = taoh_get_array($rsvp_status_result, true);

    $is_user_rsvp_done = $rsvp_status_response['success'];
}

?>
<div>

    <div class="d-flex" style="gap: 12px;">
        <div class="d-flex flex-column align-items-center justify-content-center" style="gap: 6px;">
            <img class="spk-detail-pro"
                 src="<?php echo get_headers($speaker_data['spk_profileimg'][$speaker_key], 1)[0] !== false ? $speaker_data['spk_profileimg'][$speaker_key] : TAOH_SITE_URL_ROOT . '/assets/images/profile_room_3.png'; ?>"
                 alt="">
            <?php if ($speaker_data['spk_linkedin'][$speaker_key] != '') { ?>
                <a href="<?php echo $speaker_data['spk_linkedin'][$speaker_key] ?? '#'; ?>" target="_blank">
                    <!-- linkedin  svg -->
                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="21" height="21" rx="6" fill="#2557A7"/>
                        <path d="M7.68607 16H5.19821V7.98821H7.68607V16ZM6.4408 6.89533C5.64527 6.89533 5 6.23639 5 5.44084C5 5.0587 5.1518 4.69222 5.422 4.42201C5.6922 4.1518 6.05868 4 6.4408 4C6.82293 4 7.1894 4.1518 7.45961 4.42201C7.72981 4.69222 7.88161 5.0587 7.88161 5.44084C7.88161 6.23639 7.23607 6.89533 6.4408 6.89533ZM16.9973 16H14.5148V12.0999C14.5148 11.1704 14.4961 9.97844 13.2213 9.97844C11.9279 9.97844 11.7296 10.9883 11.7296 12.0329V16H9.24446V7.98821H11.6305V9.0811H11.6654C11.9975 8.45162 12.8088 7.78732 14.0193 7.78732C16.5371 7.78732 17 9.44539 17 11.599V16H16.9973Z"
                              fill="white"/>
                    </svg>
                </a>
            <?php } ?>
        </div>
        <div class="d-flex flex-column justify-content-center" style="gap: 3px;">
            <h4 class="name"><?php echo $_POST['speaker_name']; ?></h4>
            <h5 class="text-xs designation"><?php echo $speaker_data['spk_desig'][$speaker_key] . ', ' . $speaker_data['spk_company'][$speaker_key]; ?></h5>
        </div>
    </div>

    <div id="speakerdet_loaderArea"></div>

    <div>
        <p class="text-sm grey mt-2"><?php echo taoh_title_desc_decode($speaker_data['spk_title']); // $speaker_data['spk_bio'][$speaker_key]; ?></p>
        <p class="text-xs description my-3"><?php echo taoh_title_desc_decode($speaker_data['spk_bio'][$speaker_key]); // spk_desc ?></p>
        <?php
//        $start = new DateTime($speaker_data['spk_datefrom'],);
//        $today = new DateTime('now', new DateTimeZone($user_timezone));
//        $end = new DateTime($speaker_data['spk_dateto']);
//        $disableJoinBtn = 'disabled';
//
//        if ($taoh_user_is_logged_in && $is_user_rsvp_done == 1 && $today->format('Y-m-d H:i:s') >= $start->format('Y-m-d H:i:s') && $today->format('Y-m-d H:i:s') <= $end->format('Y-m-d H:i:s')) {
//            $disableJoinBtn = '';
//        }
//
//        if ((!isset($speaker_data['enable_tao_networking']) || $speaker_data['enable_tao_networking'] == 0) && (isset($speaker_data['spk_external_video_room_link']) && $speaker_data['spk_external_video_room_link'] != '')) {
//            $joinBtnText = 'Presentation link';
//            if ($speaker_data['spk_streaming_link'] == '') {
//                $joinBtnText = 'Join us';
//            }
//            ?>
<!--            <a target="_blank" class="btn join_video_link join-room-btn px-3 mt-2"-->
<!--               href="--><?php //echo $speaker_data['spk_external_video_room_link']; ?><!--" --><?php //echo $disableJoinBtn; ?><!-- >--><?php //echo $joinBtnText; ?><!--</a>-->
<!--            --><?php
//        } else {
//            ?>
<!--            <a target="_blank" class="btn  join_networking join-room-btn px-3 mt-2"-->
<!--               href="--><?php //echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG . '/club/' . $_POST['eventtoken'] . '-' . $_POST['eventtoken'] . '?session_id=' . $_POST['speaker_id'] . '&session_name=' . rawurlencode($speaker_data['spk_title']); ?><!--" --><?php //echo $disableJoinBtn; ?><!-- >Join us</a>-->
<!--            --><?php
//        }
//
//        if (isset($speaker_data['spk_room_location']) && $speaker_data['spk_room_location'] != '') {
//            ?>
<!--            <span class="text-sm grey"> Physical Room : --><?php //echo $speaker_data['spk_room_location']; ?><!--</span>-->
<!--            --><?php
//        }
        ?>
    </div>
</div>