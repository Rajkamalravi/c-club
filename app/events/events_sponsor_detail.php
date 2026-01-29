<?php
ob_start();
taoh_get_header();

$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;

/* if (!$taoh_user_is_logged_in) {
    header("Location: " . TAOH_SITE_URL_ROOT . '/login');
    taoh_exit();
} */
$user_info_obj = taoh_user_all_info();
$valid_user = isset($user_info_obj->profile_complete) ? (bool) $user_info_obj->profile_complete : false;

$ptoken = $taoh_user_is_logged_in ? $user_info_obj->ptoken : '';
$puser_name = $taoh_user_is_logged_in ? $user_info_obj->fname : '';

function showErrorPage($base_path, $error_code = 1001, $error_from = '', $error_data = [])
{
    include_once $base_path . '/includes/error.php';
    taoh_get_footer();
    exit();
}

$appname = TAOH_CURR_APP_SLUG ?? 'events';
$sponsor_id = (int) taoh_parse_url(2);
$eventtoken = taoh_parse_url(3);

if (empty($sponsor_id)) {
    showErrorPage(TAOH_APP_PATH . '/' . $appname, 1001, 'event_exhibitor');
    taoh_exit();
}

if(defined('TAOH_API_TOKEN')){
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
    $rsvp_slug = $rsvp_status_response['output']['rsvp_slug'];

    $is_user_rsvp_done = $rsvp_status_response['success'];
}else{
    $is_user_rsvp_done =  false;
}

?>

    <style>
        #external_video_room,
        #exhibitor_website,
        #exhibitor_location {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #exhibitor_website_blk i,
        #exhibitor_location_blk i {
            color: #2557a7;
            font-size: 14px;
        }

        #exhibitor_website_blk a:hover span,
        #exhibitor_location_blk a:hover span {
            text-decoration: underline;
        }
        /*  */
        .exh-d-right, .exh-d-right-rbtn-blk {
            width: 100%;
            max-width: 100%;
        }
        .winner_profile.card {
            width: 100%;
            max-width: 480px;

            padding: 0;
            border: 2px solid #d3d3d3;
            /* background-image: linear-gradient(#ffffff, #ffffff), linear-gradient(90deg, #396AFC 0%, #2948FF 50%);
            background-origin: border-box;
            background-clip: content-box, border-box; */
            border-radius: 6px;
        }
        .exh-d-right button {
            font-size: 14px;
        }
        @media (min-width: 768px) {
            .exh-d-right {
                max-width: 361px;
            }
            .winner_profile.card {
                max-width: 361px;
            }
        }
        @media (min-width: 1024px) {
            .exh-d-right-rbtn-blk {
                max-width: 362px;
            }
        }
        .star-con input {
            display: none;
        }
    </style>
<div class="detail-hall aw aw-logo aw-loader">

    <div class="light-dark">
        <!-- new template start-->
            <div class="pt-4 sticky-top light-dark shadow-sm border-bottom" style="z-index: 99;">
                <div class="container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3" id="exhibitor_breadcrumb" role="tablist" style="line-height: 1.143;">
                        <li class="nav-item">
                            <a href="<?php echo TAOH_SITE_URL_ROOT; ?>">Home</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="#">Events</a>
                            <svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg>
                        </li>
                        <li class="nav-item">
                            First Friday Fair Virtual Job Fair Career Expo Event
                        </li>   -->
                    </ul>

                    <div class="d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;">
                        <div class="flex-grow-1">
                            <h5 class="e-v2-title mb-1" id="event_title"></h5>
                            <div class="d-flex align-items-center mb-1" style="gap: 12px;">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 21 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 0C6.82969 0 7.5 0.698242 7.5 1.5625V3.125H13.5V1.5625C13.5 0.698242 14.1703 0 15 0C15.8297 0 16.5 0.698242 16.5 1.5625V3.125H18.75C19.9922 3.125 21 4.1748 21 5.46875V7.8125H0V5.46875C0 4.1748 1.00781 3.125 2.25 3.125H4.5V1.5625C4.5 0.698242 5.17031 0 6 0ZM0 9.375H21V22.6562C21 23.9502 19.9922 25 18.75 25H2.25C1.00781 25 0 23.9502 0 22.6562V9.375ZM15.4219 14.8926C15.8625 14.4336 15.8625 13.6914 15.4219 13.2373C14.9812 12.7832 14.2688 12.7783 13.8328 13.2373L9.37969 17.876L7.17656 15.5811C6.73594 15.1221 6.02344 15.1221 5.5875 15.5811C5.15156 16.04 5.14687 16.7822 5.5875 17.2363L8.5875 20.3613C9.02812 20.8203 9.74063 20.8203 10.1766 20.3613L15.4219 14.8926Z" fill="#2557A7"></path>
                                </svg>
                                <p class="e-v2-info"><span id="event_start"></span></p> <!-- Friday September 13 04:00 pm IST -->
                            </div>
                        </div>

                        <span id="liveLink"></span>
                        <!-- <button type="button" class="e-d-v2-btn btn btn-success lh-1 py-2">
                            LIVE NOW! Click to Join
                        </button> -->

                    </div>

                </div>
            </div>

            <!-- carousle start -->
            <div class="container exhibitor-carousel-con pt-4" id="exhibitor_banner_container">
                <!-- <a class="exh-owl-prev" role="button" aria-label="Previous">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 16C32 11.7565 30.3143 7.68687 27.3137 4.68629C24.3131 1.68571 20.2435 0 16 0C11.7565 0 7.68687 1.68571 4.68629 4.68629C1.68571 7.68687 0 11.7565 0 16C0 20.2435 1.68571 24.3131 4.68629 27.3137C7.68687 30.3143 11.7565 32 16 32C20.2435 32 24.3131 30.3143 27.3137 27.3137C30.3143 24.3131 32 20.2435 32 16ZM16.9375 8.4375C17.525 7.85 18.475 7.85 19.0562 8.4375C19.6375 9.025 19.6437 9.975 19.0562 10.5562L13.6187 15.9937L19.0562 21.4312C19.6437 22.0187 19.6437 22.9688 19.0562 23.55C18.4688 24.1312 17.5187 24.1375 16.9375 23.55L10.4375 17.0625C9.85 16.475 9.85 15.525 10.4375 14.9438L16.9375 8.4375Z" fill="black"/>
                    </svg>
                </a> -->

                <div class="exhibitor-carousel owl-carousel owl-theme" id="exhibitor_banner_image">
                    <!-- <div class="item">

                        <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 1">
                        </div>


                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 2">
                        </div>
                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 3">
                        </div>

                    </div>
                    <div class="item">
                         <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png');"></div>
                            <div class="glass-overlay"></div>
                            <img src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/emp_branding_hero.png" class="carou-main-img" alt="Image 4">
                        </div>

                    </div> -->
                </div>

                <!-- <a class="exh-owl-next" role="button" aria-label="Next">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 16C0 11.7565 1.68571 7.68687 4.68629 4.68629C7.68687 1.68571 11.7565 0 16 0C20.2435 0 24.3131 1.68571 27.3137 4.68629C30.3143 7.68687 32 11.7565 32 16C32 20.2435 30.3143 24.3131 27.3137 27.3137C24.3131 30.3143 20.2435 32 16 32C11.7565 32 7.68687 30.3143 4.68629 27.3137C1.68571 24.3131 0 20.2435 0 16ZM15.0625 8.4375C14.475 7.85 13.525 7.85 12.9438 8.4375C12.3625 9.025 12.3563 9.975 12.9438 10.5562L18.3813 15.9937L12.9438 21.4312C12.3563 22.0187 12.3563 22.9688 12.9438 23.55C13.5312 24.1312 14.4813 24.1375 15.0625 23.55L21.5625 17.0625C22.15 16.475 22.15 15.525 21.5625 14.9438L15.0625 8.4375Z" fill="black"/>
                    </svg>
                </a> -->
            </div>
            <!-- carousel end -->


            <div class="container d-flex flex-column flex-lg-row">
                <div class="exh-d-v2-left flex-grow-1 py-5 pr-md-3">
                    <div class="d-flex align-items-start" style="gap: 12px;">
                        <img class="c-v2-img" id="exhibitor_logo" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="Exhibitor logo">
                        <div>
                            <h6 class="c-v2-name mb-1" id="exhibitor_title"></h6>
                            <p class="c-v2-tag mb-2" id="exhibitor_subtitle"></p>
                            <input type="hidden" id="raffle_question" name="raffle_question" value="">
                            <a href="#" class="c-v2-site">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.375 9C12.375 9.78047 12.3328 10.5328 12.259 11.25H5.74102C5.66367 10.5328 5.625 9.78047 5.625 9C5.625 8.21953 5.66719 7.46719 5.74102 6.75H12.259C12.3363 7.46719 12.375 8.21953 12.375 9ZM13.3875 6.75H17.7152C17.9016 7.4707 18 8.22305 18 9C18 9.77695 17.9016 10.5293 17.7152 11.25H13.3875C13.4613 10.5258 13.5 9.77344 13.5 9C13.5 8.22656 13.4613 7.47422 13.3875 6.75ZM17.3461 5.625H13.2434C12.8918 3.37852 12.1957 1.49766 11.2992 0.295312C14.052 1.02305 16.2914 3.01992 17.3426 5.625H17.3461ZM12.1043 5.625H5.8957C6.11016 4.34531 6.44062 3.21328 6.84492 2.2957C7.21406 1.46602 7.62539 0.864844 8.02266 0.485156C8.41641 0.1125 8.74336 0 9 0C9.25664 0 9.58359 0.1125 9.97734 0.485156C10.3746 0.864844 10.7859 1.46602 11.1551 2.2957C11.5629 3.20977 11.8898 4.3418 12.1043 5.625ZM4.75664 5.625H0.653906C1.70859 3.01992 3.94453 1.02305 6.70078 0.295312C5.8043 1.49766 5.1082 3.37852 4.75664 5.625ZM0.284766 6.75H4.6125C4.53867 7.47422 4.5 8.22656 4.5 9C4.5 9.77344 4.53867 10.5258 4.6125 11.25H0.284766C0.0984375 10.5293 0 9.77695 0 9C0 8.22305 0.0984375 7.4707 0.284766 6.75ZM6.84492 15.7008C6.43711 14.7867 6.11016 13.6547 5.8957 12.375H12.1043C11.8898 13.6547 11.5594 14.7867 11.1551 15.7008C10.7859 16.5305 10.3746 17.1316 9.97734 17.5113C9.58359 17.8875 9.25664 18 9 18C8.74336 18 8.41641 17.8875 8.02266 17.5148C7.62539 17.1352 7.21406 16.534 6.84492 15.7043V15.7008ZM4.75664 12.375C5.1082 14.6215 5.8043 16.5023 6.70078 17.7047C3.94453 16.977 1.70859 14.9801 0.653906 12.375H4.75664ZM17.3461 12.375C16.2914 14.9801 14.0555 16.977 11.3027 17.7047C12.1992 16.5023 12.8918 14.6215 13.2469 12.375H17.3461Z" fill="#FF6600"/>
                                </svg>
                                <span id="exhibitor_website_blk"></span>
                            </a>
                        </div>
                    </div>

                    <hr style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <div class="exh-v2-desc" id="exh_description"></div>

                    <div class="exh-v2-contact mt-5 configure_now" style="display:none">
                        <img class="c-v2-img" id="exhibitor_logo-configure" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="Exhibitor logo">
                        <p>Your exhibitor slot is not configured yet!</p>
                        <a class="btn v2-room-btn ml-auto edit-exhibitor" id="exhibitor_configure_now" target="_blank" style="max-width: 143px;" data-id="<?php echo $sponsor_id; ?>" data-type="sponsor">Configure Now</a>
                    </div>

                </div>

            </div>

        <!-- new template end-->
    </div>

</div>
<?php
require_once('events_exhibitor_form_new.php');
?>

<script type="text/javascript">
    const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
    let eventtoken = '<?= $eventtoken ?? ''; ?>';
    let eventToken = '<?= $eventtoken ?? ''; ?>';
    let sponsor_id = '<?= $sponsor_id ?? 0; ?>';
    const my_pToken = '<?= $ptoken ?? ''; ?>';
    const my_username = '<?= $puser_name ?? ''; ?>';
    let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';
    let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
    let rsvp_slug = '<?php echo $rsvp_slug; ?>';
    const isValidUser = <?= json_encode($valid_user); ?>;

    $(document).ready(function() {
        function getEventExhibitorInfo(requestData, serverFetch = false, callback = null) {
            // console.log(requestData);
            if (!requestData.eventtoken || !requestData.sponsor_id) return;

            //$cache_name = 'event_MetaInfo_'. $event_token.'_exhibitor_'.$exhibitor_id;
            const event_exhibitor_key = `event_MetaInfo_${requestData.eventtoken}_exhibitor_${requestData.sponsor_id}`;

            const handleResponse = (response, saveToDB = true) => {
                // console.log(response);
                if (response.success) {
                    if (saveToDB) {
                        IntaoDB.setItem(objStores.event_store.name, {
                            taoh_data: event_exhibitor_key,
                            values: response,
                            timestamp: Date.now()
                        });
                    }

                    if (typeof callback === 'function') {
                        callback(requestData, response);
                    }
                } else {
                    console.log('Failed to fetch event exhibitor details! Try Again');
                }
            };

            if (serverFetch) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'GET',
                    data: {
                        taoh_action: 'get_event_exhibitor',
                        token: _taoh_ajax_token,
                        eventtoken: requestData.eventtoken,
                        exhibitor_id: requestData.sponsor_id
                    },
                    dataType: 'json',
                    success: (response) => handleResponse(response, true),
                    error: (xhr) => console.error('Error:', xhr.status)
                });
            } else {

                IntaoDB.getItem(objStores.event_store.name, event_exhibitor_key).then((data) => {
                    if (data?.values) {
                        handleResponse(data.values, false);
                    } else {
                        getEventExhibitorInfo(requestData, true, callback);
                    }
                });
            }
        }

        getEventExhibitorInfo({
            eventtoken,
            sponsor_id
        }, false, (requestData, response) => {
            if (response.success) {
                let event_exhibitor_info = response.output;
                let event_exhibitor_room_status = parseInt(event_exhibitor_info.exh_room_status) === 1;

                let user_timezone;

                if (isLoggedIn) {
                    user_timezone = '<?= taoh_user_timezone(); ?>';
                }
                if (!isLoggedIn || !user_timezone?.trim()) {
                    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';


                /* const exhibitorBannersArray = [event_exhibitor_info.exh_banner].filter(url => typeof url === 'string' &&  url.trim() !== "" && isValidURL(url)).map(url => ({
                    src: url,
                    type: getMediaType(url)
                })); */

                const galleryContainer = document.getElementById("exhibitor_banner_container");
                // const mainDisplay = document.createElement("div");
                const mainDisplay =  document.getElementById("exhibitor_banner_image");
                // mainDisplay.id = "exhibitor_banner_image";
                // galleryContainer.before(mainDisplay);

                function formatVideoSrc(videoSrc) {
                    if (videoSrc.includes("youtube.com")) {
                        return `https://www.youtube.com/embed/${videoSrc.split("v=")[1]?.split("&")[0]}`;
                    }
                    if (videoSrc.includes("youtu.be")) {
                        const videoId = videoSrc.split("youtu.be/")[1];
                        return `https://www.youtube.com/embed/${videoId}`;
                    }
                    if (videoSrc.includes("vimeo.com")) {
                        return `https://player.vimeo.com/video/${videoSrc.split("vimeo.com/")[1]}`;
                    }
                    return videoSrc; // For other video formats
                }


                    const noImage = _taoh_site_url_root + '/assets/images/sponsor_banner.png';
                    const mediaHtml = `
                        <div class="item">
                            <div class="cover-event-image">
                                <div class="exhibitor-bg" style="background-image: url('${noImage}');"></div>
                                <div class="glass-overlay"></div>
                                <img src="${noImage}" class="carou-main-img" alt="Event">
                            </div>
                        </div>`;
                    $('#exhibitor_banner_image').html(mediaHtml);

                if (event_exhibitor_info.exh_hero_button_text && isValidURL(event_exhibitor_info.exh_hero_button_url)) {
                    $('#exhibitor_banner_image').append(`<a href="${event_exhibitor_info.exh_hero_button_url}" target="_blank" class="btn hero-button">${event_exhibitor_info.exh_hero_button_text}</a>`);
                }

                if(event_exhibitor_info.image && isValidURL(event_exhibitor_info.image)){
                    $('#exhibitor_logo,#exhibitor_logo-configure').attr('src', event_exhibitor_info.image);
                }

                $('#exhibitor_title').text(taoh_desc_decode_new(event_exhibitor_info.title));
                // $('#exhibitor_subtitle').text(taoh_desc_decode_new(event_exhibitor_info.exh_subtitle));

                let exhibitorWebsiteUrl = event_exhibitor_info.link || '';
                if (exhibitorWebsiteUrl.trim()) {
                    if (isValidURL(exhibitorWebsiteUrl)) {
                        $('#exhibitor_website_blk').html(`<div class="hall-text-sm-bold mb-2">
                            <a href="${exhibitorWebsiteUrl}" class="btn link" target="_blank" id="exhibitor_website"><span>${exhibitorWebsiteUrl}</span></a></div>`);
                    } else {
                        $('#exhibitor_website_blk').html(`<div class="btn link" id="exhibitor_website">
                            <span title="${exhibitorWebsiteUrl}">${exhibitorWebsiteUrl}</span></div>`);
                    }
                    $('#exhibitor_website_blk').show();
                }

                getEventBaseInfo({ eventtoken }, false)
                    .then(({requestData, response}) => {
                        let event_output = response.output;
                        let event_owner = event_output.ptoken;
                        let conttoken_data = event_output.conttoken;

                        let event_title = conttoken_data?.title;

                        let event_timestamp_start_data = {
                            utc_datetime: event_output.utc_start_at,
                            local_datetime: event_output.local_start_at,
                            timezone: event_output.local_timezone,
                            locality: event_output.conttoken.locality
                        };
                        let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                        let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
                        var chat_room_status = parseInt(event_output.conttoken.chat_room_status || 1, 10);
                        var liveLink = '';
                        if (event_live_state == 'live') {
                            event_live_link = (chat_room_status == 2 && isValidUrl(event_output.conttoken.external_link))
                                            ? event_output.conttoken.external_link : '<?php echo TAOH_SITE_URL_ROOT; ?>' + '/' + '<?php echo TAOH_CURR_APP_SLUG; ?>' + '/club/' + (event_output.conttoken.title) + '-' + event_output.eventtoken;
                            if (chat_room_status) {
                                chatroom_text =  '<span>Event Live, ' + (!isValidUser ? 'Complete settings to' : 'Click to') + ' Join</span>';
                            } else {
                                chatroom_text =  '<span>Event Live</span>';
                            }
                        }

                    if(!isLoggedIn){
                        liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                        <button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="${location.href}" data-title="${btoa(unescape(encodeURIComponent(event_output.conttoken.title)))}" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>
                                    </div>`;
                    }else if (event_live_state == 'before' && is_user_rsvp_done) {
                            liveLink = ` <div class="event-new-flow d-flex align-items-center" style="gap: 6px;">
                                            <span class="btn not-live d-flex align-items-center cursor-pointer px-3" style="width:200px;gap: 12px;">
                                            <svg width="17" height="23" viewBox="0 0 17 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.4163 0C0.632908 0 0 0.642383 0 1.4375C0 2.23262 0.632908 2.875 1.4163 2.875V3.36914C1.4163 5.27383 2.16428 7.10215 3.49206 8.44981L6.49284 11.5L3.49206 14.5502C2.16428 15.8979 1.4163 17.7262 1.4163 19.6309V20.125C0.632908 20.125 0 20.7674 0 21.5625C0 22.3576 0.632908 23 1.4163 23H2.8326H14.163H15.5793C16.3627 23 16.9956 22.3576 16.9956 21.5625C16.9956 20.7674 16.3627 20.125 15.5793 20.125V19.6309C15.5793 17.7262 14.8313 15.8979 13.5035 14.5502L10.5027 11.5L13.5079 8.44981C14.8357 7.10215 15.5837 5.27383 15.5837 3.36914V2.875C16.3671 2.875 17 2.23262 17 1.4375C17 0.642383 16.3671 0 15.5837 0H14.163H2.8326H1.4163ZM4.24889 3.36914V2.875H12.7467V3.36914C12.7467 4.22266 12.4988 5.04922 12.0385 5.75H4.95704C4.50117 5.04922 4.24889 4.22266 4.24889 3.36914ZM4.95704 17.25C5.11195 17.0119 5.29341 16.7873 5.49258 16.5807L8.49779 13.535L11.503 16.5852C11.7066 16.7918 11.8836 17.0164 12.0385 17.2545H4.95704V17.25Z" fill="#000000"/>
                                            </svg>
                                            <span>Event Not Live!</span>
                                        </span>
                                         </div>`;

                    }else if (event_live_state == 'after') {
                        liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>
                                         </div>`;
                    }else if(event_output.conttoken.freeze_option == 1){
                        liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="${TAOH_CURR_APP_URL}" class="mr-lg-5 btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i> Event Suspended</a>
                                         </div>`;
                    }else if (isLoggedIn && is_user_rsvp_done && (event_live_state == 'live')) {
                        if (chat_room_status) {
                            liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a target="_blank" href="${event_live_link}" class="mr-lg-5 btn btn-success w-100">\<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>

                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>
                                                             ${chatroom_text}</a>
                                         </div>`;
                        }else{
                            liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                            <a href="javascript:void(0)" class="mr-lg-5 btn btn-success w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                                                    <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>

                                                                    <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                                                    <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                                            </svg>
                                             ${chatroom_text}</a>
                                         </div>`;
                        }
                    }
                    // console.log('liveLink : '+liveLink);
                    $("#liveLink").html(liveLink);

                        let exhibitorBreadcrumbHTML = `<li class="nav-item"><a href="${_taoh_site_url_root}/events">Events</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
                            <li class="nav-item"><a href="${_taoh_site_url_root}/events/chat/id/events/${eventtoken}">${event_title}</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
                            ${event_exhibitor_info.title ? `<li class="nav-item">${taoh_desc_decode(event_exhibitor_info.title)}</li>` : ''}
                        `;
                        $('#exhibitor_breadcrumb').append(exhibitorBreadcrumbHTML);
                        $("#event_title").text(event_title);
                        $("#event_start").text(event_start_at);

                        let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                            .split(',')
                            .map(token => token.trim())
                            .filter(token => token);

                        if(event_owner) event_organizer_ptokens.push(event_owner);

                        let event_instance_owner = conttoken_data.ptoken;
                        event_organizer_ptokens.push(event_instance_owner);

                        if(event_organizer_ptokens.includes(my_ptoken)){
                            $(".overallrating").hide();
                            $(".review_count").hide();
                        }

                        if (my_pToken == event_exhibitor_info.ptoken || event_organizer_ptokens.includes(my_ptoken)) {
                            $(".configure_now").show();
                        }
                });

                const safeMessage = document.createElement('pre');
                safeMessage.textContent = taoh_desc_decode(event_exhibitor_info.description) || '';
                // let safeMessageHtml = event_exhibitor_info.exh_description || '';
                /* let safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;').replace(/\r\n/g, '<br>'); */
                let safeMessageHtml = safeMessage.innerHTML
                                    .replace(/\r\n/g, '<br>')
                                    .replace(/\n/g, '<br>')
                                    .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

                $('#exh_description').html((safeMessageHtml));
            }


            if (!isLoggedIn){
                $("#avail_now").attr('disabled',true);
                $(".avail_now").addClass('disabled');
            }
            $('.aw').awloader('hide');

        });

        $(document).on('click', '.edit-exhibitor', async function () {
            let exh_id = $(this).data('id');
            let exh_type = $(this).data('type');
            let rsvp_sponsor_title =  '';

            setTimeout(async () => {
                var eventHallAccess = [];
                var eventHallAccessKey = `event_hall_access_${eventToken}`;
                const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey); // await
                if (data?.values) {
                    eventHallAccess = data?.values.output;
                }

            $('#exh_tags').val([]).trigger('change');

            if (parseInt(exh_id)) {
                getEventBaseInfo({ eventtoken: eventToken }, false)
                    .then(({requestData, response}) => {
                    let event_output = response.output;
                    let event_owner = event_output.ptoken;
                    let conttoken_data = event_output.conttoken;
                    var room_keywords_count = 1;
                    let event_form_version = conttoken_data.event_form_version ?? 1;
                    var TicketArr = conttoken_data.ticket_types.find(ticket => ticket.slug === rsvp_slug)  || {};


                    let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                        .split(',').concat(event_owner)
                        .map(token => token.trim())
                        .filter(token => token);

                    let event_instance_owner = conttoken_data.ptoken;
                    event_organizer_ptokens.push(event_instance_owner);

                    let exhibitor_halls = conttoken_data.exhibitor_halls;
                    let allowed_exhibitor_halls = Array.isArray(exhibitor_halls)
                        ? exhibitor_halls.filter(hall => (hall.status === '1' && event_organizer_ptokens.includes(my_pToken)) || hall.status === '2')
                        : [];
                    let event_halls = conttoken_data.event_halls;
                    allowed_exhibitor_halls = Array.isArray(event_halls) ? event_halls.filter(function(hall) {
                        return hall.accesslevel === "2" || hall.accesslevel === "3";
                    }) : allowed_exhibitor_halls;
                    if (allowed_exhibitor_halls.length === 0) {
                        alert('No Exhibitor Hall available for setup');
                        return;
                    }

                    $.each(conttoken_data.ticket_types, function (k, item) {
                        if(item.slug == rsvp_slug){
                            rsvp_sponsor_title = item.title;
                        }
                    });

                    var is_organizer = event_organizer_ptokens.includes(my_pToken) ? 1 : 0;

                    tagsArr = [];
                    if(conttoken_data.event_tags != undefined){
                        tagsArr = conttoken_data.event_tags.split(",");
                    }
                    $( ".tags-field" ).select2( {
                        data : tagsArr,
                        width: '100%'
                    } );


                    $(document).on('click', '.list-group-item', function() {
                        var contentToPrepend  = "<div class='item active last-active'>"+$(this).text()+"</div>";
                        $('.exh_tags_ts_control').prepend(contentToPrepend);
                        // $('#exh_tags_input').val(); // Set input value to selected suggestion
                        $('#exh_tags_dropdown').hide(); // Hide the dropdown after selection
                        // $("#exh_tags").append("<option value='"+$(this).text()+"' selected>"+$(this).text()+"</option>");
                        var tagValue = $(this).text();
                        if ($("#exh_tags option[value='"+tagValue+"']").length === 0) {
                            $("#exh_tags").append("<option value='"+tagValue+"' selected>"+tagValue+"</option>");
                        } else {
                            $("#exh_tags option[value='"+tagValue+"']").prop('selected', true);
                        }
                        $('#exh_tags').select2('destroy');
                        $('#exh_tags').select2({width: '100%'});
                        $("#exh_tags_input").val("");
                    });

                    if(event_organizer_ptokens.includes(my_pToken)){
                        room_keywords_count = 3;
                    }
                    const exhibitorslotmodal_elem = $('#exhibitorSlotModal');

                    //     let exh_title = $('#exhi_title_' + exh_id).html();
                    //     let exh_description = $('#exhi_description_' + exh_id).html();
                    //     let exh_link = $('#exhi_link_' + exh_id).html();
                    //     let exh_logo = $('#exhi_logo_' + exh_id).attr('src');
                    //     let exh_display_type  = $('#exhi_display_type_' + exh_id).html();

                    getEventExhibitorInfo({
                        eventtoken,
                        sponsor_id
                    }, false, async (requestData, response) => {
                        if (response.success) {
                            let event_exhibitor_info = response.output;
                            let exh_title = event_exhibitor_info.title;
                            let exh_description = event_exhibitor_info.description;
                            let exh_link = event_exhibitor_info.link;
                            let exh_logo = event_exhibitor_info.image;
                            // let exh_ptoken = event_exhibitor_info.ptoken;

                            var sponsor_type = event_exhibitor_info.sponsor_type;
                            // let exh_display_type  = event_exhibitor_info.;
                            exhibitorslotmodal_elem.find('#sponsor_id').val(exh_id);
                            // exhibitorslotmodal_elem.find('#display_type').val(exh_display_type);
                            exhibitorslotmodal_elem.find('#exh_session_title').val(taoh_desc_decode(exh_title));
                            exhibitorslotmodal_elem.find('#exh_description').val(taoh_desc_decode(exh_description));
                            exhibitorslotmodal_elem.find('#exh_hero_button_url').val(exh_link);

                            // if (exh_ptoken) {
                            //     const exh_contact_info = await ft_getUserInfo(exh_ptoken, 'full');
                            //     if (exh_contact_info?.email?.trim()) {
                            //         exhibitorslotmodal_elem.find('#exh_contact_email').val(exh_contact_info?.email);
                            //     }
                            // }
                            exhibitorslotmodal_elem.find('#exh_logo').val(exh_logo);
                            exhibitorslotmodal_elem.find('#exh_logo_preview').html(`<img src="${exh_logo}" class="img-fluid" alt="Exhibitor Logo" />`);

                            $('#exh_hall').empty();
                            allowed_exhibitor_halls.forEach(hall => {
                                if(!hall.id || !hall.name) return;

                                let hall_id = hall.id;
                                let hall_name = hall.name;
                                let hall_token = (hall.name); // btoa
                                var showhall = 0;

                                /* Start: check for count */
                                if (event_form_version == 2) {
                                    if (is_organizer == 1) {
                                        showhall = 1;
                                    }
                                    if (TicketArr && typeof TicketArr.max_exhibits_allowed !== 'undefined' && TicketArr.max_exhibits_allowed > 0 && (TicketArr.exhibitor_halls == 'All' || TicketArr.exhibitor_halls.includes(hall_name) || TicketArr.exhibitor_halls.includes(hall_id))) {
                                        showhall = 1;
                                    }
                                } else {
                                    if (typeof eventHallAccess['exhibitor'] !== 'undefined' && typeof eventHallAccess['exhibitor'][hall_name] !== "undefined") {
                                        // console.log(exhibitor_data.exh_hall+'===='+hall_name);
                                        if (is_organizer == 1) {
                                            showhall = 1;
                                            if (typeof eventHallAccess['exhibitor'][hall_name]["organizer"] !== "undefined" && eventHallAccess['exhibitor'][hall_name]["organizer"]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                        if (sponsor_type != '' && sponsor_type != undefined) {
                                            if (typeof eventHallAccess['exhibitor'][hall_name][sponsor_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][sponsor_type]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                        /* if(user_profile_type != ''){
                                            if(typeof eventHallAccess['exhibitor'][hall_name][user_profile_type] !== "undefined" && eventHallAccess['exhibitor'][hall_name][user_profile_type]["allowed"] > 0){
                                                showhall = 1;
                                            }
                                        } */
                                        if (rsvp_sponsor_title != '') {
                                            if (typeof eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title] !== "undefined" && eventHallAccess['exhibitor'][hall_name][rsvp_sponsor_title]["allowed"] > 0) {
                                                showhall = 1;
                                            }
                                        }
                                    }
                                }
                                /* End: check for count */
                                if (showhall == 1) {
                                    let hall_option = `<option value="${hall_token}">${hall_name}</option>`;
                                    $('#exh_hall').append(hall_option);
                                }
                            });
                        }
                    });

                        $('label[for="exh_contact_email"] .text-danger').css('display', ((is_organizer == 1) ? 'none' : 'inline-block'));

                    $('#exhibitorSlotModal').modal('show');
                }).catch(error => console.error("Error fetching event info:", error));
            }
        }, 1000);

        });

        $(document).on('change', '#exh_logo_upload', function(e) {
            let file = e.target.files[0];

            let reader = new FileReader();
            reader.onload = function(e) {
                $('#exh_logo_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" />`);
            }
            reader.readAsDataURL(file);

        });

        $(document).on('change', '#exh_banner_upload', function(e) {
            let file = e.target.files[0];

            let reader = new FileReader();
            reader.onload = function(e) {
                $('#exh_banner_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Banner" />`);
            }
            reader.readAsDataURL(file);
        });
    });

    function updateraffle(){
        if($("input[name='exh_raffles']:checked").val() == '1') {
            $('#exh_raffle_options').show();
        } else {
            $('#exh_raffles_timebound_no').prop('checked', true).trigger('change');
            $('#exh_raffle_time_bound_time').hide();
            $('#exh_raffle_options').hide();
        }
    }

    function updateraffletimebound(){
        if($("input[name='exh_raffles_timebound_option']:checked").val() == '1') {
            $('#exh_raffle_time_bound_time').show();
        } else {
            $('#exh_raffle_time_bound_time').hide();
        }
    }

    function delete_events_meta_into(eventtoken) {
        var eventKey = `event_MetaInfo_${eventtoken}`;
        getIntaoDb(dbName).then((db) => {
            let dataStoreName = EVENTStore;
            const transaction = db.transaction(dataStoreName, 'readwrite');
            const objectStore = transaction.objectStore(dataStoreName);
            const request = objectStore.openCursor();
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const index_key = cursor.primaryKey;
                    if (index_key.includes(eventKey)) {
                        objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
            };
        }).catch((err) => {
            console.log('Error in deleting data store');
        });
    }

    function delete_events_into(find_key) {
            getIntaoDb(dbName).then((db) => {
                let dataStoreName = EVENTStore;
                const transaction = db.transaction(dataStoreName, 'readwrite');
                const objectStore = transaction.objectStore(dataStoreName);
                const request = objectStore.openCursor();
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        const index_key = cursor.primaryKey;
                        if (index_key.includes(find_key)) {
                            objectStore.delete(index_key);
                        }
                        cursor.continue();
                    }
                };
            }).catch((err) => {
                console.log('Error in deleting data store');
            });
        }
</script>


<!-- carousel script -->
 <script>
    $(document).ready(function(){
        $('.exhibitor-carousel').owlCarousel({
            loop: true,
            margin: 10,
            nav: false, // Disable default nav
            dots: false, // Disable dots
            items: 1
        });

        // Custom navigation
        $('.exh-owl-prev').click(function() {
            $('.owl-carousel').trigger('prev.owl.carousel');
        });

        $('.exh-owl-next').click(function() {
            $('.owl-carousel').trigger('next.owl.carousel');
        });
    });
</script>
<!-- carousel script end -->

<?php taoh_get_footer(); ?>