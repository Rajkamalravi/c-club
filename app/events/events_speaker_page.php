<?php
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = taoh_user_all_info();
$valid_user = isset($user_info_obj->profile_complete) ? (bool) $user_info_obj->profile_complete : false;

$eventtoken = taoh_parse_url(2);
$speaker_id = taoh_parse_url(3);

$taoh_vals = array(
    'token' => taoh_get_dummy_token(1),
    'ops' => 'baseinfo',
    'mod' => 'events',
    'eventtoken' => $eventtoken ?? '',
    'cache_name' => 'event_detail_' . $eventtoken,
    //'cfcc5h' => 1 ////cfcache newly added
);
//echo taoh_apicall_get_debug('events.event.get', $taoh_vals);die();

$result = taoh_apicall_get('events.event.get', $taoh_vals);
$response = taoh_get_array($result, true);
$rsvp_slug = '';
if($taoh_user_is_logged_in){
    // Get RSVP status
    $taoh_vals = array(
        'ops' => 'status',
        'mod' => 'events',
        'token' => TAOH_API_TOKEN,
        'eventtoken' => $eventtoken,
        'cache_required' => 0,
    );
    $rsvp_status_result = taoh_apicall_get('events.rsvp.get', $taoh_vals);
    $rsvp_status_response = taoh_get_array($rsvp_status_result, true);

    $is_user_rsvp_done = $rsvp_status_response['success'] ?? '';
    $rsvp_slug = $rsvp_status_response['output']['rsvp_slug'] ?? '';
}

$event_arr = $response['output'];
$events_data = $event_arr['conttoken'] ?? [];
$ticket_types = $events_data['ticket_types'] ?? [];
// echo "<pre>"; print_r($ticket_types); echo "</pre>";
$current_ticket_types = [];
foreach ($ticket_types as $item) {
    if ($item['slug'] === $rsvp_slug) {
        $current_ticket_types[] = $item;
    }
}

$current_ticket_type = array_values($current_ticket_types)[0];
taoh_get_header(); ?>

<style>
    .joinus-btn {
        min-width: 110px;
        min-height: 42px;
        font-size: 18px !important;
        font-weight: 500 !important;
        line-height: 1.02 !important;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center !important;
    }

    .joinus-btn.disabled {
        color: #000000;
        background-color: #ccc !important;
        border-color: transparent !important;
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
</style>

    <div class="detail-hall light-dark d-non">
        <!-- new template start-->
            <div class="pt-4 sticky-top light-dark shadow-sm border-bottom" style="z-index: 99;">
                <div class="container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3" id="speaker_breadcrumb" role="tablist" style="line-height: 1.143;">
                        <li class="nav-item">
                            <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                    </ul>

                    <div class="d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;">
                        <div class="flex-grow-1">
                            <h5 class="e-v2-title mb-1" id="event_title"></h5>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"></path>
                                </svg>
                                <p class="e-v2-info"><span id="event_start_datetime"></span> <span id="event_end_datetime"></span></p>
                            </div>
                        </div>

                        <span id="liveLink"></span>
                        <!-- <button type="button" class="e-d-v2-btn btn btn-success lh-1 py-2">
                            LIVE NOW! Click to Join
                        </button> -->

                    </div>

                </div>
            </div>

            <!-- carousel start -->
            <div class="container session-carousel-con pt-4" id="session_banner_container">
                <!-- <a class="exh-owl-prev" role="button" aria-label="Previous" data-target="#exhibitor_banner_image" data-slide="prev">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16ZM16.9375 8.4375C17.525 7.85 18.475 7.85 19.0562 8.4375C19.6375 9.025 19.6437 9.975 19.0562 10.5562L13.6187 15.9937L19.0562 21.4312C19.6437 22.0187 19.6437 22.9688 19.0562 23.55C18.4688 24.1312 17.5187 24.1375 16.9375 23.55L10.4375 17.0625C9.85 16.475 9.85 15.525 10.4375 14.9438L16.9375 8.4375Z" fill="black"/>
                    </svg>
                </a> -->

                <div id="session_banner_image" class="carousel slide session-carousel" data-ride="carousel">
                    <div class="carousel-inner">
                        <!-- <div class="carousel-item active">
                            <div class="cover-event-image">
                                <div class="speaker-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                                <div class="glass-overlay"></div>
                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 1">
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                                <div class="glass-overlay"></div>
                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 2">
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                                <div class="glass-overlay"></div>
                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 3">
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                                <div class="glass-overlay"></div>
                                <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 4">
                            </div>
                        </div> -->
                    </div>
                </div>

                <!-- <a class="exh-owl-next" role="button" aria-label="Next" data-target="#exhibitor_banner_image" data-slide="next">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 16C0 11.7565 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7565 0 16 0C20.2435 0 24.3131 1.68571 27.3137 4.68629C30.3143 7.68687 32 11.7565 32 16C32 20.2435 30.3143 24.3131 27.3137 27.3137C24.3131 30.3143 20.2435 32 16 32C11.7565 32 7.68687 30.3143 4.68629 27.3137C1.68571 24.3131 0 20.2435 0 16ZM15.0625 8.4375C14.475 7.85 13.525 7.85 12.9438 8.4375C12.3625 9.025 12.3563 9.975 12.9438 10.5562L18.3813 15.9937L12.9438 21.4312C12.3563 22.0187 12.3563 22.9688 12.9438 23.55C13.5312 24.1312 14.4813 24.1375 15.0625 23.55L21.5625 17.0625C22.15 16.475 22.15 15.525 21.5625 14.9438L15.0625 8.4375Z" fill="black"/>
                    </svg>
                </a> -->
            </div>
            <!-- carousel end -->
            <div class="container">
                <div class="exh-d-v2-left flex-grow-1 py-5 pr-md-3">
                    <div class="d-flex align-items-start flex-wrap" style="gap: 12px;">
                        <div class="flex-grow-1 d-flex align-items-start" style="gap: 12px;">

                            <!-- logo  -->
                            <img class="c-v2-img d-none" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="speaker logo">

                            <!-- default svg -->
                            <img class="c-v2-img-svg session_logo" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/spk_icon_svg.svg" alt="speaker icon svg">

                            <div>
                                <h6 class="c-v2-name mb-1 font-weight-bold" id="session_title"></h6>
                                <p class="c-v2-tag mb-2" id="session_time"></p>
                            </div>
                        </div>
                        <div id="join_btn"></div>
                        <!-- <button type="button" class="btn std-btn">Join Discussion Room</button> -->
                    </div>

                    <hr style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <h5 class="font-weight-bold mb-2" id="session_subtitle"></h5>

                    <div class="exh-v2-desc" id="session_desc"></div>

                    <!-- Session Speakers start -->
                    <div>
                        <div class="d-flex align-items-center mt-4" style="gap: 12px;">
                            <!-- default svg -->
                            <div class="c-v2-img-svg-con">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24.8637 20.6394C26.7069 18.4713 27.8075 15.7431 27.8075 12.7775C27.8075 5.71813 21.5825 0 13.9038 0C6.225 0 0 5.71813 0 12.7775C0 19.8369 6.225 25.5544 13.9038 25.5544C15.5943 25.557 17.2731 25.273 18.8687 24.7144C18.9593 24.6834 19.0575 24.6823 19.1488 24.711C19.2401 24.7398 19.3198 24.7971 19.3762 24.8744C20.5375 26.4425 22.3519 27.5456 24.3694 27.9944C24.4411 28.0092 24.5157 27.9949 24.5769 27.9547C24.638 27.9145 24.6807 27.8517 24.6956 27.78C24.704 27.7407 24.7035 27.7001 24.6943 27.6609C24.6851 27.6218 24.6674 27.5852 24.6425 27.5537C23.8714 26.5533 23.4727 25.3156 23.5148 24.0532C23.5569 22.7909 24.0371 21.5825 24.8731 20.6356L24.8637 20.6394ZM20.4987 11.275L16.9175 13.9931L18.2162 18.2969C18.2408 18.3777 18.2396 18.4642 18.2127 18.5443C18.1858 18.6244 18.1347 18.6941 18.0663 18.7437C17.998 18.7934 17.9158 18.8205 17.8314 18.8213C17.7469 18.822 17.6643 18.7965 17.595 18.7481L13.9038 16.1825L10.2125 18.75C10.1432 18.7982 10.0606 18.8236 9.97614 18.8228C9.89172 18.8219 9.80965 18.7948 9.74131 18.7452C9.67297 18.6956 9.62175 18.626 9.59476 18.546C9.56776 18.466 9.56631 18.3796 9.59063 18.2987L10.89 13.995L7.30875 11.2769C7.24145 11.2259 7.1917 11.1552 7.16643 11.0746C7.14116 10.9941 7.14162 10.9077 7.16774 10.8274C7.19386 10.7471 7.24435 10.6769 7.31219 10.6267C7.38002 10.5764 7.46183 10.5485 7.54625 10.5469L12.0388 10.4525L13.5175 6.2075C13.5452 6.12779 13.597 6.05867 13.6658 6.00976C13.7346 5.96085 13.8169 5.93457 13.9013 5.93457C13.9856 5.93457 14.0679 5.96085 14.1367 6.00976C14.2055 6.05867 14.2573 6.12779 14.285 6.2075L15.7638 10.4525L20.2563 10.5469C20.3411 10.5475 20.4236 10.5746 20.4922 10.6245C20.5609 10.6744 20.6121 10.7445 20.6389 10.825C20.6656 10.9056 20.6665 10.9924 20.6414 11.0735C20.6162 11.1545 20.5664 11.2256 20.4987 11.2769V11.275Z" fill="white"/>
                                </svg>
                            </div>

                            <h6 class="c-v2-name mb-1 font-weight-bold" id="exhibitor_title">Session Speakers</h6>
                        </div>

                        <div class="session-spk-v1-con" id="session_speakers">
                            <!-- <div class="session-spk-v1-card">
                                <div class="s-spk-header">
                                    <img class="s-spk-pro" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/profile_room_1.png" alt="">
                                    <div>
                                        <h6 class="name">
                                            Andrew Garfield
                                            <a href="#">
                                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.6429 0H1.3529C0.606473 0 0 0.614955 0 1.36987V17.6301C0 18.385 0.606473 19 1.3529 19H17.6429C18.3893 19 19 18.385 19 17.6301V1.36987C19 0.614955 18.3893 0 17.6429 0ZM5.74241 16.2857H2.92634V7.2183H5.74665V16.2857H5.74241ZM4.33437 5.97991C3.43103 5.97991 2.70156 5.24621 2.70156 4.3471C2.70156 3.44799 3.43103 2.71429 4.33437 2.71429C5.23348 2.71429 5.96719 3.44799 5.96719 4.3471C5.96719 5.25045 5.23772 5.97991 4.33437 5.97991ZM16.2984 16.2857H13.4824V11.875C13.4824 10.8232 13.4612 9.47031 12.0192 9.47031C10.5518 9.47031 10.327 10.6154 10.327 11.7987V16.2857H7.51094V7.2183H10.2125V8.4567H10.2507C10.6281 7.7442 11.5484 6.99353 12.9183 6.99353C15.7683 6.99353 16.2984 8.87232 16.2984 11.3152V16.2857Z" fill="#2557A7"/>
                                                </svg>
                                            </a>
                                        </h6>
                                        <div class="s-spk-com">
                                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="">
                                            <h6>CTO, TamQ Analytics LLC</h6>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2" style="max-width: 292px;">
                                <div class="s-spk-content">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.Aenean ante nunc, ultrices ac lobortis cursus, dictum in nibh. Nullam pretium semper felis, vitae laoreet eros aliquam at.
                                </div>
                            </div> -->
                    </div>
                     <!-- Session Speakers end -->

                    <!-- <div class="exh-v2-contact mt-5" style="max-width: 867px;">

                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.71429 0C2.11406 0 0 2.11406 0 4.71429V28.2857C0 30.8859 2.11406 33 4.71429 33H28.2857C30.8859 33 33 30.8859 33 28.2857V4.71429C33 2.11406 30.8859 0 28.2857 0H4.71429ZM16.058 17.6565L4.72902 10.342C4.86161 9.16339 5.85603 8.25 7.07143 8.25H25.9286C27.144 8.25 28.1384 9.16339 28.271 10.342L16.942 17.6565C16.8094 17.7449 16.6547 17.7891 16.5 17.7891C16.3453 17.7891 16.1906 17.7449 16.058 17.6565ZM18.2237 19.6379L28.2857 13.1411V22.3929C28.2857 23.6967 27.2324 24.75 25.9286 24.75H7.07143C5.76763 24.75 4.71429 23.6967 4.71429 22.3929V13.1411L14.7763 19.6379C15.292 19.9694 15.8886 20.1462 16.5 20.1462C17.1114 20.1462 17.708 19.9694 18.2237 19.6379Z" fill="#FF6600"/>
                            </svg>
                            <p>Feel Free to reach out and Drop your enquiries here</p>
                        </div>
                        <div style="flex: 1;" class="d-flex justify-content-end">
                            <a href="#" class="btn v2-room-btn contact-us" style="max-width: 143px;">Contact Us</a>
                        </div>

                    </div> -->
                </div>
            </div>

        <!-- new template end-->
    </div>


<script type="text/javascript">
const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
const isValidUser = <?= json_encode($valid_user); ?>;
let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
let eventtoken = '<?= $eventtoken ?? ''; ?>';
let speaker_id = '<?= $speaker_id ?? ''; ?>';
let event_output;
let user_timezone;
let local_timezone;

if (isLoggedIn) {
    user_timezone = '<?= taoh_user_timezone(); ?>';
}
if (!isLoggedIn || !user_timezone?.trim()) {
    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
}
if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

$(document).ready(function () {
    getEventBaseInfo({eventtoken}, false)
        .then(({requestData, response}) => {
            let event_output = response.output;
            let conttoken_data = event_output.conttoken;
            let event_title = conttoken_data?.title;

            local_timezone = event_output.local_timezone;
            let event_timestamp_start_data = {
                utc_datetime: event_output.utc_start_at,
                local_datetime: event_output.local_start_at,
                timezone: event_output.local_timezone,
                locality: conttoken_data?.locality ?? ''
            };
            let event_timestamp_end_data = {
                utc_datetime: event_output.utc_end_at,
                local_datetime: event_output.local_end_at,
                timezone: event_output.local_timezone,
                locality: conttoken_data?.locality ?? ''
            };
            //let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
            //let event_end_at = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
            let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', conttoken_data?.locality, user_timezone);
            var chat_room_status = parseInt(event_output.conttoken.chat_room_status || 1, 10);
            const is_event_suspended = parseInt(event_output?.status) === 2;
            const is_event_freeze = parseInt(conttoken_data?.freeze_option) === 1;

            //alert(event_start_at);
            var liveLink = '';
            if (event_live_state == 'live') {
                event_live_link = (chat_room_status == 2 && isValidUrl(event_output.conttoken.external_link))
                    ? event_output.conttoken.external_link : '<?php echo TAOH_SITE_URL_ROOT; ?>' + '/' + '<?php echo TAOH_CURR_APP_SLUG; ?>' + '/club/' + (event_output.conttoken.title) + '-' + event_output.eventtoken;
                if (chat_room_status) {
                    chatroom_text = '<span>Event Live, ' + (!isValidUser ? 'Complete settings to' : 'Click to') + ' Join</span>';
                } else {
                    chatroom_text = '<span>Event Live</span>';
                }
            }

            if (!isLoggedIn) {
                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                            <button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="${location.href}" data-title="${btoa(unescape(encodeURIComponent(event_output.conttoken.title)))}" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>
                        </div>`;
            } else if (event_live_state == 'before' && is_user_rsvp_done) {
                liveLink = ` <div class="event-new-flow d-flex align-items-center" style="gap: 6px;">
                                <span class="btn not-live d-flex align-items-center cursor-pointer px-3" style="width:200px;gap: 12px;">
                                <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                </svg>
                                <span>Event Not Live!</span>
                            </span>
                                </div>`;

            } else if (event_live_state == 'after') {
                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>
                                </div>`;
            } else if (event_output.conttoken.freeze_option == 1) {
                liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i> Event Suspended</a>
                                </div>`;
            } else if (isLoggedIn && is_user_rsvp_done && (event_live_state == 'live')) {
                if (chat_room_status) {
                    liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a target="_blank" href="${event_live_link}" class="mr-lg-5 btn btn-success w-100 metrics_action" data-metrics="event_join"><i class="fa fa-rss" aria-hidden="true"></i> ${chatroom_text}</a>
                                </div>`;
                } else {
                    liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="javascript:void(0)" class="mr-lg-5 btn btn-success w-100 metrics_action" data-metrics="event_join"><i class="fa fa-rss" aria-hidden="true"></i> ${chatroom_text}</a>
                                </div>`;
                }
            }

            $("#liveLink").html(liveLink);

            let hallBreadcrumbHTML = `<li class="nav-item"><a href="${_taoh_site_url_root}/events">Events</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
                <li class="nav-item"><a href="${_taoh_site_url_root}/events/chat/id/events/${eventtoken}">${event_title}</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
            `;

            let localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
            let localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);


            $('#event_start_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));


            $('#speaker_breadcrumb').append(hallBreadcrumbHTML);
            $("#event_title").text(event_title);
            //$("#event_start_datetime").text(event_start_at);
           // $("#event_end_datetime").text(event_end_at);

            _getEventMetaInfo({eventtoken})
                .then(({requestData, response}) => {
                    const event_speaker_data = response?.output?.event_speaker || [];

                    const sessionDetails = event_speaker_data.find(
                        item => String(item.ID) === String(speaker_id)
                    ) || null;

                    if (!sessionDetails) {
                        taoh_set_error_message('Speaker session details not found.', false, 'toast-middle', [
                            {
                                text: 'OK',
                                action: () => {
                                    window.history.back();
                                },
                                class: 'dojo-v1-btn float-right mt-3 mb-3'
                            }
                        ]);
                        return;
                    }

                    let content = '';
                    let rsvp_sponsor_title = <?= json_encode($current_ticket_type['title'] ?? '') ?>;

                    let event_timestamp_start_data = {
                        utc_datetime: sessionDetails.spk_datefrom.replace(/[T:-]/g, '') + '00',
                        local_datetime: sessionDetails.spk_datefrom.replace(/[T:-]/g, '') + '00',
                        timezone: sessionDetails.spk_timezoneSelect,
                        locality: event_output?.locality ?? ''
                    };
                    let event_timestamp_end_data = {
                        utc_datetime: sessionDetails.spk_dateto.replace(/[T:-]/g, '') + '00',
                        local_datetime: sessionDetails.spk_dateto.replace(/[T:-]/g, '') + '00',
                        timezone: sessionDetails.spk_timezoneSelect,
                        locality: event_output?.locality ?? ''
                    };
                    let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy', 0); // EEEE, dd MMM yyyy
                    let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);
                    let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy', 0);
                    let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

                    $("#session_title").html(taoh_desc_decode(sessionDetails.spk_title));
                    $("#session_subtitle").html(taoh_desc_decode(sessionDetails.spk_sdesc));
                    $("#session_desc").html(taoh_desc_decode(sessionDetails.spk_desc));

                    if (startdate == enddate) {
                        $("#session_time").html(`${startdate}, ${starttime} to ${endtime}`);
                    } else {
                        $("#session_time").html(`${startdate} ${starttime} to ${enddate} ${endtime}`);
                    }

                    if (sessionDetails.spk_logo_image != '') {
                        $(".session_logo").attr('src', sessionDetails.spk_logo_image);
                    }

                    let sessionImage = '';
                    if (sessionDetails.spk_image != '') {
                        sessionImage = sessionDetails.spk_image;
                    } else {
                        sessionImage = `<?php echo TAOH_CDN_PREFIX . "/images/igcache/"?>${encodeURIComponent(sessionDetails.spk_title)}/1920_680/blog.jpg`;
                    }

                    const mediaHtml = `
            <div class="item">
                <div class="cover-event-image">
                    <div class="hall-bg" style="background-image: url('${sessionImage}');"></div>
                    <div class="glass-overlay"></div>
                    <img src="${sessionImage}" class="carou-main-img" alt="Event">
                </div>
            </div>`;
                    $("#session_banner_image").html(mediaHtml);

                    if (sessionDetails.spk_hero_button_text && isValidURL(sessionDetails.spk_hero_button_url)) {
                        $('#session_banner_image').append(`<a href="${sessionDetails.spk_hero_button_url}" target="_blank" class="btn hero-button">${sessionDetails.spk_hero_button_text}</a>`);
                    }

                    $.each(sessionDetails.spk_name, function (k, spkname) {
                        content = `<div class="session-spk-v1-card">
                        <div class="s-spk-header">
                            <img class="s-spk-pro" src="${sessionDetails.spk_profileimg[k]}" alt="">
                            <div>
                                <h6 class="name">
                                    ${spkname}
                                    ${sessionDetails.spk_linkedin[k] != '' ?
                            `<a target="_blank" href="${sessionDetails.spk_linkedin[k]}">
                                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.6429 0H1.3529C0.606473 0 0 0.614955 0 1.36987V17.6301C0 18.385 0.606473 19 1.3529 19H17.6429C18.3893 19 19 18.385 19 17.6301V1.36987C19 0.614955 18.3893 0 17.6429 0ZM5.74241 16.2857H2.92634V7.2183H5.74665V16.2857H5.74241ZM4.33437 5.97991C3.43103 5.97991 2.70156 5.24621 2.70156 4.3471C2.70156 3.44799 3.43103 2.71429 4.33437 2.71429C5.23348 2.71429 5.96719 3.44799 5.96719 4.3471C5.96719 5.25045 5.23772 5.97991 4.33437 5.97991ZM16.2984 16.2857H13.4824V11.875C13.4824 10.8232 13.4612 9.47031 12.0192 9.47031C10.5518 9.47031 10.327 10.6154 10.327 11.7987V16.2857H7.51094V7.2183H10.2125V8.4567H10.2507C10.6281 7.7442 11.5484 6.99353 12.9183 6.99353C15.7683 6.99353 16.2984 8.87232 16.2984 11.3152V16.2857Z" fill="#2557A7"/>
                                        </svg>
                                    </a>` : ``}
                                </h6>
                                <div class="s-spk-com">
                                    <!-- <img src="<?php // echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt=""> -->
                                    <h6>${sessionDetails.spk_desig[k]}, ${sessionDetails.spk_company[k]}</h6>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2" style="max-width: 292px;">
                        <div class="s-spk-content">
                            ${sessionDetails.spk_bio[k]}
                        </div>
                    </div>`;

                        $("#session_speakers").append(content);
                    });

                    var disableJoinBtn = 'disabled';
                    if (!is_event_suspended && !is_event_freeze) {
                        if (isLoggedIn && event_live_state == 'live' && rsvp_sponsor_title != '' && rsvp_sponsor_title != undefined && isJoinEnabled(sessionDetails)) { // speaker date time conditions
                            disableJoinBtn = '';
                        }
                        if (disableJoinBtn == '' && sessionDetails.spk_state != "live" && sessionDetails.spk_state != "active") {
                            disableJoinBtn = 'disabled';
                        }
                    } else {
                        disableJoinBtn = 'disabled';
                    }

                    content = '';

                    content += `<div class="d-flex flex-column align-items-center">`;
                    if (sessionDetails.enable_tao_networking == 0 && sessionDetails.spk_external_video_room_link != '') {
                        content += ` <a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'std-btn'} joinus-btn join_video_link ${disableJoinBtn}"
                            href="${sessionDetails.spk_external_video_room_link}" target="_blank">
                            ${disableJoinBtn == 'disabled' ? 'Not Live' :
                            `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width:36px">
                                    <!-- Center circle -->
                                    <circle cx="60" cy="40" r="14" fill="#000" />
                                    <path d="M78 28 C84 35, 84 45, 78 52"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M88 14 C102 30, 102 50, 88 66"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M42 28 C36 35, 36 45, 42 52" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                     <path d="M32 14 C18 30, 18 50, 32 66" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />

                                </svg> <span class="color:#000000">Live</span>`}

                                </a>`; // Video Link-Presentation link
                    } else {
                        content += ` <a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'std-btn'} joinus-btn join_networking ${disableJoinBtn}"
                    href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG; ?>/club/${taoh_desc_decode(event_output.conttoken.title)}-${eventtoken}?session_id=${sessionDetails.ID}&session_name=${encodeURIComponent(sessionDetails.spk_title)}"
                    target="_blank">
                            ${disableJoinBtn == 'disabled' ? 'Not Live' : `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width:36px">
                                    <!-- Center circle -->
                                    <circle cx="60" cy="40" r="14" fill="#000" />
                                    <path d="M78 28 C84 35, 84 45, 78 52"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M88 14 C102 30, 102 50, 88 66"  fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                    <path d="M42 28 C36 35, 36 45, 42 52" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />
                                     <path d="M32 14 C18 30, 18 50, 32 66" fill="none"  stroke="#000" stroke-width="6" stroke-linecap="round" />

                                </svg> <span class="color:#000000">Live</span>`}
                    </a>`;
                    }
                    if ($.trim(sessionDetails.spk_room_location) != '') {
                        content += ` <div class="">
                <svg style="min-width: fit-content;" width="20" height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                </svg>
                ${sessionDetails.spk_room_location}</div> `;
                    }
                    content += `</div>`;

                    $("#join_btn").html(content);

                })
                .catch(err => {
                    console.error('Failed to load event speaker info:', err);
                });
        });
});
</script>

<?php taoh_get_footer(); ?>