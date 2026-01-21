<?php 
$taoh_user_is_logged_in = taoh_user_is_logged_in() ?? false;
$user_info_obj = taoh_user_all_info();
$valid_user = isset($user_info_obj->profile_complete) ? (bool) $user_info_obj->profile_complete : false;

$urlArr = taoh_parse_url_parse();
$eventtoken = taoh_parse_url(2);
// $eventhall = taoh_title_desc_decode($urlArr[3]);
$eventhall = taoh_parse_url(3);
$parts = explode('-', $eventhall);
$hall_id = array_pop($parts);
$eventhall = implode('-', $parts);
$eventhall = taoh_title_desc_decode($eventhall);
if($hall_id != '' && $hall_id != crc32($eventhall)){
    taoh_set_error_message('Hall ID is invalid or has changed. Please select a valid hall.');
    echo "<script>window.close();</script>";
}

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
// echo "Current Ticket Type : <pre>"; print_r($current_ticket_type); echo "</pre>";

// echo 'eventtoken : '.$eventtoken."===#".urldecode($eventhall).'#==='.$hall_id.'==='.crc32($eventhall);
taoh_get_header(); 
?>
<style>
    #search_halls {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,<svg fill='white' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/><path d='M0 0h24v24H0z' fill='none'/></svg>");
        background-repeat: no-repeat;
        background-position: right .3rem center;
        background-size: 1.5rem auto;
        padding-right: 2rem;
    }
</style>

    <div class="detail-hall light-dark d-noe">
        <!-- new template start-->
            <div class="pt-4 sticky-top light-dark shadow-sm border-bottom" style="z-index: 99;">
                <div class="container">
                    <ul class="nav nav-tabs justify-content-left border-0 mt-3" id="hall_breadcrumb" role="tablist" style="line-height: 1.143;">
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
                                <p class="e-v2-info"><span id="event_start_datetime"></span> to <span id="event_end_datetime"></span></p>
                            </div>
                        </div>

                        <span class="flex-shrink-lg-0" id="liveLink"></span>
                        <!-- <button type="button" class="e-d-v2-btn btn btn-success lh-1 py-2">
                            LIVE NOW! Click to Join
                        </button> -->
                        
                    </div>

                </div>
            </div>

            <!-- carousel start -->
            <div class="container exhibitor-carousel-con pt-4" id="exhibitor_banner_container">
                <div id="hall_banner_image" class="carousel slide exhibitor-carousel" data-ride="carousel">
                    
                </div>
            </div>
            <!-- carousel end -->

            <div class="container">
                <div class="exh-d-v2-left flex-grow-1 py-5 px-md-3">
                  
                    <div class="flex-grow-1 d-flex align-items-center" style="gap: 12px;">
                        <!-- logo  -->
                        <img class="c-v2-img d-none" src="<?php echo TAOH_SITE_URL_ROOT;?>/assets/images/exh-v2-3.png" alt="speaker logo">

                        <div class="flex-grow-1 d-flex align-items-start flex-column flex-lg-row py-3" style="gap: 9px;"> 
                            <div class="flex-grow-1">
                                <h6 class="c-v2-name" id="hall_title"></h6>
                            </div>
                            <div class="hall-tabs-con d-flex align-items-center">
                                <select class="m-0" name="search_halls" id="search_halls">
                                    <option disabled>-- Select Hall --</select>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-0 mb-4" style="max-width: 385px; border-top: 2px solid #d3d3d3;">

                    <div class="exh-v2-desc" id="hall_description"></div>

                    <br>

                    <div class="hall-list-container mx-auto tab-pane " id="hall_details_list" style="max-height: unset;">
                        
                    </div>

                   
                </div>
            </div>

        <!-- new template end-->
    </div>
    <input type="hidden" name="is_organizer" id="is_organizer" value="0" >
    <input type="hidden" name="user_profile_type" id="user_profile_type" value="" >
    <input type="hidden" name="rsvp_sponsor_title" id="rsvp_sponsor_title" value="<?php echo $current_ticket_type['title'] ?? ''; ?>">


    <script type="text/javascript">
        const isLoggedIn = <?= json_encode($taoh_user_is_logged_in); ?>;
        const isValidUser = <?= json_encode($valid_user); ?>;
        let is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;
        // const my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
        let eventtoken = '<?= $eventtoken ?? ''; ?>';
        let eventhall = '<?= $eventhall ?? ''; ?>';
        let TAOH_CURR_APP_URL = '<?= TAOH_CURR_APP_URL; ?>';
        let event_output;
        let user_timezone;
        let local_timezone;
        const user_profile_type = '<?= $user_info_obj->type ?? ''; ?>';

        $("#user_profile_type").val(user_profile_type);
        /* var colorArray = ['#BA3B3B','#A1EAA3','#067CFF','#FDCC6E','#877CFF','#FEBC8F', '#FF6600','#BBBBFF','#F38400','#F6B1B1','#33CD45','#A7D8DE'];  */
        var colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];
        var shuffledColorArray = shuffleArray(colorArray);
        var hall_color_array = [];

        function shuffleArray(array) {
            // Create a new array with the length of the given array in the parameters
            const newArray = array.map(() => null);

            // Create a new array where each index contain the index value
            const arrayReference = array.map((item, index) => index);

            // Iterate on the array given in the parameters
            array.forEach(randomize);

            return newArray;

            function randomize(item) {
                const randomIndex = getRandomIndex();

                // Replace the value in the new array
                newArray[arrayReference[randomIndex]] = item;

                // Remove in the array reference the index used
                arrayReference.splice(randomIndex, 1);
            }

            // Return a number between 0 and current array reference length
            function getRandomIndex() {
                const min = 0;
                const max = arrayReference.length;
                return Math.floor(Math.random() * (max - min)) + min;
            }
        }

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
                    const event_halls = Array.isArray(conttoken_data?.event_halls)
                        ? conttoken_data.event_halls.filter(h => h?.id && h?.name)
                        : [];

                    var hall_details = $.grep(event_halls, function (e) {
                        return e.name?.toLowerCase() === eventhall.toLowerCase();
                    });
                    hall_details = hall_details[0];
                    $("#hall_title").html(taoh_desc_decode(hall_details.name));
                    $("#hall_description").html(taoh_desc_decode(hall_details.shortdesc));

                    let hallImage = _taoh_site_url_root + '/assets/images/sponsor_banner.png';
                    if (hall_details.hall_image != undefined && hall_details.hall_image != '') {
                        hallImage = hall_details.hall_image;
                    }
                    console.log('hallImage', hallImage);
                    const mediaHtml = `
                <div class="item">
                    <div class="cover-event-image">
                        <div class="hall-bg" style="background-image: url('${hallImage}');"></div>
                        <div class="glass-overlay"></div>
                        <img src="${hallImage}" class="carou-main-img" alt="hall banner">
                    </div>
                </div>`;
                    $('#hall_banner_image').html(mediaHtml);

                    local_timezone = event_output.local_timezone;
                    let event_timestamp_start_data = {
                        utc_datetime: event_output.utc_start_at,
                        local_datetime: event_output.local_start_at,
                        timezone: event_output.local_timezone,
                        locality: event_output?.locality ?? ''
                    };
                    let event_timestamp_end_data = {
                        utc_datetime: event_output.utc_end_at,
                        local_datetime: event_output.local_end_at,
                        timezone: event_output.local_timezone,
                        locality: event_output?.locality ?? ''
                    };
                    let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                    let event_end_at = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                    let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
                    var chat_room_status = parseInt(event_output.conttoken.chat_room_status || 1, 10);
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
                                <a href="${TAOH_CURR_APP_URL}" class="btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>
                                </div>`;
                    } else if (event_output.conttoken.freeze_option == 1) {
                        liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="${TAOH_CURR_APP_URL}" class="btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i> Event Suspended</a>
                                </div>`;
                    } else if (isLoggedIn && is_user_rsvp_done && (event_live_state == 'live')) {
                        if (chat_room_status) {
                            liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a target="_blank" href="${event_live_link}" class="btn btn-success w-100 metrics_action" data-metrics="event_join"><i class="fa fa-rss" aria-hidden="true"></i> ${chatroom_text}</a>
                                </div>`;
                        } else {
                            liveLink = ` <div class="d-flex align-items-center" style="gap: 6px;">
                                <a href="javascript:void(0)" class="btn btn-success w-100 metrics_action" data-metrics="event_join"><i class="fa fa-rss" aria-hidden="true"></i> ${chatroom_text}</a>
                                </div>`;
                        }
                    }
                    // console.log('liveLink : '+liveLink);
                    $("#liveLink").html(liveLink);

                    let hallBreadcrumbHTML = `<li class="nav-item"><a href="${_taoh_site_url_root}/events">Events</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
            <li class="nav-item"><a href="${_taoh_site_url_root}/events/chat/id/events/${eventtoken}">${event_title}</a><svg xmlns="http://www.w3.org/2000/svg" height="19px" viewBox="0 0 24 24" width="19px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"></path><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"></path></svg></li>
        `;
                    // ${event_exhibitor_info.exh_session_title ? `<li class="nav-item">${taoh_desc_decode(event_exhibitor_info.exh_session_title)}</li>` : ''}
                    $('#hall_breadcrumb').append(hallBreadcrumbHTML);
                    $("#event_title").text(event_title);
                    $("#event_start_datetime").text(event_start_at);
                    $("#event_end_datetime").text(event_end_at);
                });

            getHallDetails(eventtoken);
            getEventsHall(eventtoken);
        });

        $(document).on('click', '.show-more', function () {
            let key = $(this).attr('data-id');
            $('#more-content-' + key).hide();
            $('#less-content-' + key).show();
        });

        $(document).on('click', '.show-less', function () {
            let key = $(this).attr('data-id');
            $('#more-content-' + key).show();
            $('#less-content-' + key).hide();
        });

        $(document).on("change", "#search_halls", function () {
            const selectedUrl = $(this).val();
            if (selectedUrl) {
                window.open(selectedUrl, '_blank'); // Open in new tab
            }
            // $(this).val('');
        });

        async function updateEventMetaInfo(eventtoken, serverFetch = true) {
            var eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}`;
            console.log('updateEventMetaInfo events_hall_listing_page', eventMetaInfoBaseInfoKey, serverFetch);

            if (serverFetch) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'POST',  // changed to post to avoid caching issues in flexible domain
                    data: {
                        action: 'get_event_MetaInfo',
                        taoh_action: 'get_event_MetaInfo',
                        token: _taoh_ajax_token,
                        eventtoken: eventtoken,
                        type: '',
                        search: '',
                        search_speaker_name: ''
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {

                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventMetaInfoBaseInfoKey,
                                values: response,
                                timestamp: Date.now()
                            });
                            getHallDetails(eventtoken);
                        }
                    },
                    error: (xhr) => console.error('Error:', xhr.status)
                });
            }
        }

        function getHallDetails(eventtoken) {
            var content = '';
            var eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}`;
            var rsvp_sponsor_title = '<?php echo $current_ticket_type['title'] ?? ''; ?>';
            var is_user_rsvp_done = <?= json_encode($is_user_rsvp_done); ?>;

            IntaoDB.getItem(objStores.event_store.name, eventMetaInfoBaseInfoKey).then((data) => {
                if (data?.values) {
                    // Get matching speakers
                    const filteredSpeakers = data.values.output.event_speaker?.filter(spk =>
                        spk.spk_hall?.toLowerCase() === eventhall.toLowerCase()
                    ) || [];

                    if (filteredSpeakers != undefined && filteredSpeakers.length > 0) { // added enable_speaker_hall to display speaker list
                        filteredSpeakers.sort((a, b) => new Date(a.spk_datefrom) - new Date(b.spk_datefrom));

                        content = `<div id="speaker_main_title" class="divider-container d-flex align-items-center mt-1 mb-2" style="gap: 6px;">
                                <div class="left-line" style="max-width:40%"></div>
                            <p class="divider-text text-center" style="width:300px;">Speakers</p>
                            <div class="right-line" style="width:50%"></div>
                            </div>`;

                        $.each(filteredSpeakers, function (i, v) {
                            var disableJoinBtn = 'disabled';
                            var displayBtn = 0;
                            var spk_desc = '';
                            if ($.trim(v.spk_desc) != '') {
                                var vdesc = taoh_desc_decode(v.spk_desc);
                                if (vdesc.length > 110) {
                                    var limitedText = vdesc.substring(0, 110);
                                    spk_desc = limitedText + `<span id="dots_${i}">...</span><span id="more_${i}" style="display:none;">${vdesc.substring(110)} </span>
                                        <button class="readmore-btn" onclick="readmore(${i})" id="morebtn_${i}">Read more</button>`;
                                } else {
                                    spk_desc = vdesc;
                                }
                            }

                            let event_timestamp_start_data = {
                                utc_datetime: v.spk_datefrom.replace(/[T:-]/g, '') + '00',
                                local_datetime: v.spk_datefrom.replace(/[T:-]/g, '') + '00',
                                timezone: v.spk_timezoneSelect,
                                locality: event_output?.locality ?? ''
                            };

                            let event_timestamp_end_data = {
                                utc_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                                local_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                                timezone: v.spk_timezoneSelect,
                                locality: event_output?.locality ?? ''
                            };
                            let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy', 0); // EEEE, dd MMM yyyy
                            let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);

                            let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy', 0);
                            let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

                            content += `<div class="new-agenda-list p-3 px-lg-5 mb-3 d-flex flex-column flex-md-row" style="gap: 16px;">`

                            if (v.spk_logo_image != '') {
                                spk_img = v.spk_logo_image;
                            } else {
                                spk_img = "<?php echo TAOH_CDN_PREFIX . "/images/ig/"?>${encodeURIComponent(v.spk_title)}/uncategorized/1.png";
                            }
                            content += `<div class="g-overlay-con">
                                        <div class="n-hall-list-bg d-md-none" style="background-image: url('${spk_img}');"></div>
                                        <div class="glass-overlay d-md-none"></div>
                                        <img class="n-hall-list-pic" 
                                        src="${spk_img}" alt="">
                                    </div>`;

                            content += ` <div style="flex:1;">
                                <div  class="d-flex flex-wrap-reverse" style="gap: 12px;">
                                    <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">`;

                            if (startdate == enddate) {
                                content += `<p class="n-info-badge mr-2" >${startdate}, ${starttime} to ${endtime}</p>`;
                            } else {
                                content += `<p class="n-info-badge mr-2" >${startdate} ${starttime} to ${enddate} ${endtime}</p>`;
                            }

                            content += `  <p class="n-info-badge" >${v.spk_hall}</p>
                                    </div>
                                    <div class="d-flex ml-auto" style="gap: 12px;">`;

                            content += `
                                    <a style="width:30px;" id="dropdown-agenda_${i}" data-target="#agenda-content_${i}" class="svg-opt-con btn dropdown-agenda">
                                        <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"/>
                                        </svg>
                                    </a>`;

                            content += `
                            </div>
                                </div>

                                <div class="my-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between pr-lg-5" style="gap: 12px;">
                                    <div>
                                        <h6 class="title line-clamp-1 mb-2" style="max-width: 752px;">${taoh_desc_decode(v.spk_title)}</h6>
                                        <p id="agenda-content_${i}" class="desc-text mb-2" style="display:none">${$.trim(v.spk_desc) != '' ? taoh_desc_decode(v.spk_desc) : ''}</p>
                                        <div class="d-flex flex-wrap" style="gap: 12px;">`;

                            if (Array.isArray(v.spk_name)) {
                                v.spk_name.map(function (item, r) {
                                    let profileimg = v.spk_profileimg[r];
                                    content += `  <div class="d-flex align-items-center" style="gap: 6px;">
                                        <img class="p-img" src="${profileimg}" alt="">
                                        <p class="mb-1 name-role">${item}, <span>${v.spk_desig[r]}, ${v.spk_company[r]}</span></p>
                                    </div>`;
                                });
                            }
                            content += `</div></div>`;
                            // const now = new Date(new Date().toLocaleString("en-US", { timeZone: user_timezone }));
                            if (isLoggedIn && rsvp_sponsor_title != '' && rsvp_sponsor_title != undefined && isJoinEnabled(v)) { // speaker date time conditions
                                disableJoinBtn = '';
                            }

                            //if (v.spk_streaming_link != '' && v.spk_external_video_room_link != '') {
                            //    displayBtn = 1;
                            //
                            //    content += `<div class="dropdown">
                            //            <button class="btn bor-btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ${disableJoinBtn}>
                            //                Join
                            //            </button>
                            //            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">`;
                            //
                            //
                            //    if (v.spk_streaming_link != '') {
                            //        content += ` <a href="<?php //echo TAOH_SITE_URL_ROOT; ?>///<?php //echo TAOH_CURR_APP_SLUG; ?>///club/${taoh_desc_decode(event_output.conttoken.title)}-${eventtoken}?session_id=${v.ID}&session_name=${encodeURIComponent(v.spk_title)}"
                            //            class="dropdown-item join_networking" target="_blank">Presentation link</a>`;
                            //    }
                            //    if (v.spk_external_video_room_link != '') {
                            //        content += ` <a href="${v.spk_external_video_room_link}"
                            //        class="dropdown-item join_video_link" target="_blank">Video link</a>`;
                            //    }
                            //
                            //    if ($.trim(v.spk_room_location) != '') {
                            //        content += ` <span class="dropdown-item">
                            //        <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            //        <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                            //        </svg>
                            //                ${v.spk_room_location}</span> `;
                            //    }
                            //
                            //    content += ` </div>
                            //                </div>`;
                            //}

                            // if(displayBtn == 0){
                            content += `<div>`;
                            if (v.enable_tao_networking == 0 && v.spk_external_video_room_link != '') {
                                content += `<a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-primary'} joinus-btn join_video_link ${disableJoinBtn}" href="${v.spk_external_video_room_link}" target="_blank">Join us</a>`;
                            } else {
                                content += `<a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-primary'} joinus-btn join_networking ${disableJoinBtn}"
                            href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG; ?>/club/${taoh_desc_decode(event_output.conttoken.title)}-${eventtoken}?session_id=${v.ID}&session_name=${encodeURIComponent(v.spk_title)}" target="_blank">Join us</a>`;
                            }
                            if ($.trim(v.spk_room_location) != '') {
                                content += ` <div class="">
                        <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/>
                        </svg>
                                ${v.spk_room_location}</div> `;
                            }
                            content += `</div>`;
                            // }

                            content += ` </div>`;

                            content += `  </div></div>`;
                        });
                        is_content = 1;
                    }

                    // Get matching exhibitors
                    /* const filteredExhibitors = data.values.output.event_exhibitor?.filter(exh =>
                        exh.exh_hall?.toLowerCase() === eventhall.toLowerCase()
                    ) || []; */
                    const filteredExhibitors = data.values.output.event_exhibitor?.filter(exh => {
                        // Check if exh_hall is an array or string
                        if (Array.isArray(exh.exh_hall)) {
                            return exh.exh_hall.some(hall => hall.toLowerCase() === eventhall.toLowerCase());
                        } else if (typeof exh.exh_hall === 'string') {
                            return exh.exh_hall.toLowerCase() === eventhall.toLowerCase();
                        }
                        return false;
                    }) || [];


                    sortedExhList = filteredExhibitors.sort(function (a, b) {
                        if (a.ptoken === my_ptoken) {
                            return -1;
                        }
                        if (b.ptoken === my_ptoken) {
                            return 1;
                        }
                        const aNoSponsor = a.sponsor_id === '';
                        const bNoSponsor = b.sponsor_id === '';
                        if (aNoSponsor && !bNoSponsor) return -1;
                        if (!aNoSponsor && bNoSponsor) return 1;
                        return parseInt(b.final_price || 0) - parseInt(a.final_price || 0);
                    });

                    if (sortedExhList != undefined && sortedExhList.length > 0) {

                        content += `<div id="exhibitor_main_title" class="divider-container d-flex align-items-center mt-1 mb-2" style="gap: 6px;">
                               <div class="left-line" style="max-width:40%"></div>
                                <p class="divider-text text-center" style="width:300px;">Exhibitors</p>
                                <div class="right-line" style="width:50%"></div>
                            </div>`;

                        $.each(sortedExhList, function (i, v) {

                            let exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                            content += ` <!-- new exh list -->
                        <div class="new-exh-list mb-3 ${v.ptoken == my_ptoken ? 'sponsor-highlight' : ''}">
                            <!-- <div class="gradient-bg-border"></div> -->

                            <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1;">
                                <div class="d-flex flex-column flex-md-row" style="gap: 16px; flex: 1;">
                                    

                                    <div class="g-overlay-con">
                                            <div class="n-hall-list-bg d-md-none" style="background-image: url(${v.exh_logo})"></div>
                                            <div class="glass-overlay d-md-none"></div>
                                            <img class="n-hall-list-pic" src="${v.exh_logo}" alt="">
                                        </div>


                                    <div style="flex: 1;">
                                        <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                            <div class="d-flex flex-wrap" style="gap: 3px;">
                                                <p class="n-info-badge mr-2">${Array.isArray(v.exh_hall)
                                ? v.exh_hall.map(taoh_desc_decode_new).join(', ')
                                : taoh_desc_decode_new(v.exh_hall)}</p>
                                            </div>

                                                        <!-- dropdown -->
                                            <!-- <a href="#" class="svg-opt-con btn">
                                                <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"></path>
                                                </svg>
                                            </a> -->
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                            <div class="d-flex flex-column" style="gap:3px;">    
                                                <div class="d-flex align-items-center mb-1" style="gap: 10px;">
                                                    <h6 class="n-exh-name mr-2">${taoh_desc_decode_new(v.exh_session_title)}</h6>`;

                            if (v.exh_raffles == '1') {
                                raffle_start = new Date(v.exh_raffle_start_time);
                                raffle_end = new Date(v.exh_raffle_stop_time);
                                if (v.exh_raffles_timebound_option == 0 || (v.exh_raffles_timebound_option == 1 && new Date() >= raffle_start && new Date() <= raffle_end)) { // raffle date conditions
                                    content += ` <a href="#" class="d-flex align-items-center">
                                                        <svg style="min-width: fit-content;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.44141 2.6875L8.80078 5H8.75H5.9375C5.07422 5 4.375 4.30078 4.375 3.4375C4.375 2.57422 5.07422 1.875 5.9375 1.875H6.02344C6.60547 1.875 7.14844 2.18359 7.44141 2.6875ZM2.5 3.4375C2.5 4 2.63672 4.53125 2.875 5H1.25C0.558594 5 0 5.55859 0 6.25V8.75C0 9.44141 0.558594 10 1.25 10H18.75C19.4414 10 20 9.44141 20 8.75V6.25C20 5.55859 19.4414 5 18.75 5H17.125C17.3633 4.53125 17.5 4 17.5 3.4375C17.5 1.53906 15.9609 0 14.0625 0H13.9766C12.7305 0 11.5742 0.660156 10.9414 1.73438L10 3.33984L9.05859 1.73828C8.42578 0.660156 7.26953 0 6.02344 0H5.9375C4.03906 0 2.5 1.53906 2.5 3.4375ZM15.625 3.4375C15.625 4.30078 14.9258 5 14.0625 5H11.25H11.1992L12.5586 2.6875C12.8555 2.18359 13.3945 1.875 13.9766 1.875H14.0625C14.9258 1.875 15.625 2.57422 15.625 3.4375ZM1.25 11.25V18.125C1.25 19.1602 2.08984 20 3.125 20H8.75V11.25H1.25ZM11.25 20H16.875C17.9102 20 18.75 19.1602 18.75 18.125V11.25H11.25V20Z" fill="#FFC107"/>
                                                            <defs>
                                                            <linearGradient id="paint0_linear_7222_848" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                            <stop stop-color="#FFC107"/>
                                                            <stop offset="1" stop-color="#FF5C00"/>
                                                            </linearGradient>
                                                            </defs>
                                                        </svg>
                                                    </a>
                                                `;
                                }
                            }

                            content += ` </div>
                                                <!-- <a href="#" class="site-link">${exhibitorWebsiteUrl != '' ? exhibitorWebsiteUrl : 'javascript:void(0)'}</a> -->
                                            </div>
                                        
                                            <div class="mr-lg-5 d-flex align-items-center" style="gap: 6px;">
                                                <a target="_blank" data-metrics="view_exhibitor" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}" class="btn bor-btn metrics_action">More Info</a>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- new exh list end --> `;
                        });
                    }
                } else {
                    updateEventMetaInfo(eventtoken, true);
                }
                $('#hall_details_list').html(content);
            });
        }

        function getEventsHall(eventtoken) {
            const eventdetailBaseInfoKey = `event_detail_<?php echo $eventtoken;?>`;
            IntaoDB.getItem(objStores.event_store.name, eventdetailBaseInfoKey).then((data) => {
                if (data?.values) {
                    getEventsHallList(data.values);
                }
            });
        }

        async function getEventsHallList(response) {
            let event_output = response.output;
            let conttoken_data = event_output.conttoken;
            const enable_hall = Number(conttoken_data?.enable_hall) === 1;
            let event_halls = enable_hall && Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [];
            let event_form_version = conttoken_data.event_form_version;
            var eventMetaInfoBaseInfoKey = `event_MetaInfo_${eventtoken}`;

            const data = await IntaoDB.getItem(objStores.event_store.name, eventMetaInfoBaseInfoKey);
            var spk_list = data?.values?.output?.event_speaker ?? [];
            var exh_list = data?.values?.output?.event_exhibitor ?? [];
            var speaker_hall_list = spk_list.map(item => item.spk_hall);
            var exhibitor_hall_list = exh_list.map(item => item.exh_hall);

            const exh_allowed = new Set(["2", "3"]);
            const allowed_exhibitor_halls = event_halls.filter(h => Number(h?.id) > 0 && h?.name && exh_allowed.has(h.accesslevel));

            const spk_allowed = new Set(["1", "3"]);
            const allowed_speaker_halls = event_halls.filter(h => Number(h?.id) > 0 && h?.name && spk_allowed.has(h.accesslevel));

            $('#exh_hall_list').empty();
            var u = 0;
            var sponsor_type = $('#sponsor_type').val();
            var is_organizer = $("#is_organizer").val();
            var user_profile_type = $("#user_profile_type").val();
            var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
            var ticketArr = conttoken_data.ticket_types.find(function (ticket) {
                return ticket.title === rsvp_sponsor_title;
            });
            let hall_option_exh = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_exhibitor','','All');">All</a>`;
            $('#exh_hall_list').append(hall_option_exh);

            var exh_allowed_list = {};
            var spk_allowed_list = {};

            allowed_exhibitor_halls.forEach(hall => {
                let hall_name = hall.name;
                let hall_id = hall.id;
                let hall_token = btoa(hall.name);
                var showhall = 0;

                color = shuffledColorArray[u % shuffledColorArray.length];
                hall_color_array[hall_name] = color;
                u++;
                exh_allowed_list[hall_name] = {};
                $.each(hall.profiletype, function (pfkey, pftype) {
                    exh_allowed_list[hall_name][pftype] = {};
                    // console.log(hall_name+'==='+pftype+'==='+pfkey+'===='+hall.hallcount[pfkey]);
                    exh_allowed_list[hall_name][pftype]['max'] = hall.hallcount[pfkey];
                    exh_allowed_list[hall_name][pftype]['allowed'] = hall.hallcount[pfkey];
                });

                if (event_form_version == 2) {
                    if (ticketArr && (ticketArr.exhibitor_halls == 'All' || ticketArr?.exhibitor_halls?.includes(hall_name) || ticketArr?.exhibitor_halls?.includes(hall_id))) { // typeof ticketArr.max_exhibits_allowed !== 'undefined' && ticketArr.max_exhibits_allowed > 0 &&
                        showhall = 1;
                    }
                    showhall = 1; // allow all halls in filter
                } else {
                    if (typeof hall.viewaccess !== "undefined" && hall.viewaccess !== "") {
                        if (hall.viewaccess.includes("all")) {
                            showhall = 1;
                        }
                        if (is_organizer == 1) {
                            showhall = 1;
                            if (hall.viewaccess.includes("organizer")) {
                                showhall = 1;
                            }
                        }
                        if (sponsor_type != '' && sponsor_type != 'undefined') {
                            if (hall.viewaccess.includes(sponsor_type)) {
                                showhall = 1;
                            }
                        }
                        if (user_profile_type != '') {
                            if (hall.viewaccess.includes(user_profile_type)) {
                                showhall = 1;
                            }
                        }
                        if (rsvp_sponsor_title != '') {
                            if (hall.viewaccess.includes(rsvp_sponsor_title)) {
                                showhall = 1;
                            }
                        }
                    }
                }
                if (showhall == 1 && typeof hall_name !== 'undefined' && hall_name !== "undefined" && $.inArray((hall_name), exhibitor_hall_list.flat()) !== -1) {
                    let hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_exhibitor','${hall_name}','${hall_name}');">${hall_name}</a>  `;
                    $('#exh_hall_list').append(hall_option);
                }
            });

            $('.spk_hall_list').empty();

            // var u = 0;
            var sponsor_type = $('#sponsor_type').val();
            var is_organizer = $("#is_organizer").val();
            var user_profile_type = $("#user_profile_type").val();
            var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
            let hall_option_speaker = `
            <a class="dropdown-item" onclick="LoadMetaWithHall('event_speaker','','All');">All</a>  `;
            $('.spk_hall_list').append(hall_option_speaker);

            allowed_speaker_halls.forEach(hall => {
                let hall_name = hall.name;
                let hall_id = hall.id;
                let hall_token = hall.name; // btoa
                var showhall = 0;

                spk_allowed_list[hall_name] = {};
                $.each(hall.profiletype, function (pfkey, pftype) {
                    spk_allowed_list[hall_name][pftype] = {};
                    spk_allowed_list[hall_name][pftype]['max'] = hall.hallcount[pfkey];
                    spk_allowed_list[hall_name][pftype]['allowed'] = hall.hallcount[pfkey];
                });

                color = shuffledColorArray[u % shuffledColorArray.length];
                hall_color_array[hall_name] = color;
                u++;

                if (event_form_version == 2) {
                    if (ticketArr && (ticketArr?.speaker_halls == 'All' || (Array.isArray(ticketArr.speaker_halls) && (ticketArr?.speaker_halls?.includes(hall_name) || ticketArr?.speaker_halls?.includes(hall_id))))) {
                        showhall = 1;
                    }
                    showhall = 1; // allow all halls in filter
                } else {
                    console.log('====>' + hall.viewaccess)
                    if (typeof hall.viewaccess !== "undefined" && hall.viewaccess !== "") {
                        if (hall.viewaccess.includes("all")) {
                            showhall = 1;
                        }
                        if (is_organizer == 1) {
                            showhall = 1;
                            if (hall.viewaccess.includes("organizer")) {
                                showhall = 1;
                            }
                        }
                        if (sponsor_type != '' && sponsor_type != 'undefined') {
                            if (hall.viewaccess.includes(sponsor_type)) {
                                showhall = 1;
                            }
                        }
                        if (user_profile_type != '') {
                            if (hall.viewaccess.includes(user_profile_type)) {
                                showhall = 1;
                            }
                        }
                        if (rsvp_sponsor_title != '') {
                            if (hall.viewaccess.includes(rsvp_sponsor_title)) {
                                showhall = 1;
                            }
                        }
                    }
                }
                if (showhall == 1 && $.inArray((hall_name), speaker_hall_list) !== -1) {
                    let hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_speaker','${hall_token}','${hall_name}');">${hall_name}</a>`;
                    $('.spk_hall_list').append(hall_option);
                }
            });

            // if(Object.keys(exh_allowed_list).length > 0){
            var eventHallAccessKey = `event_hall_access_${eventtoken}`;
            response = {'success': true, output: {'exhibitor': exh_allowed_list, 'speaker': spk_allowed_list}};
            IntaoDB.setItem(objStores.event_store.name, {
                taoh_data: eventHallAccessKey,
                values: response,
                timestamp: Date.now()
            });
            // }

            $('.agenda_hall_list').empty();
            $('#search_halls').empty();
            $('#search_halls').append(
                $('<option>', {
                    value: _taoh_site_url_root + '/events/chat/id/events/' + eventtoken,
                    text: 'All'
                })
            );
            var searchhallcount = 0;

            if (event_halls.length > 0) {
                $('#search_halls').show();
            } else {
                $('#search_halls').hide();
            }
            // const hallColorMap = {};
            let hall_option_agenda = `<a class="dropdown-item" onclick="LoadMetaWithHall('event_speaker','','All','agenda');">All</a>`;
            $('.agenda_hall_list').append(hall_option_agenda);
            event_halls.forEach(hall => {
                let hall_name = hall.name;
                let hall_id = hall?.id ?? '';
                let hall_token = (hall.name); // btoa
                // hallColorMap[hall.name] = getRandomColor();
                let showhall = 0;

                if (event_form_version == 2) {
                    if ((ticketArr && (ticketArr?.exhibitor_halls == 'All' || ticketArr?.exhibitor_halls?.includes(hall_name) || ticketArr?.exhibitor_halls?.includes(hall_id))) || (ticketArr && (ticketArr?.speaker_halls == 'All' || ticketArr?.speaker_halls?.includes(hall_name) || ticketArr?.speaker_halls?.includes(hall_id)))) {
                        showhall = 1;
                    }
                    showhall = 1; // allow all halls in filter
                } else {

                    if (typeof hall.viewaccess !== "undefined" && hall.viewaccess !== "") {
                        if (hall.viewaccess.includes("all")) {
                            showhall = 1;
                        }
                        if (is_organizer == 1) {
                            showhall = 1;
                            if (hall.viewaccess.includes("organizer")) {
                                showhall = 1;
                            }
                        }
                        if (sponsor_type != '' && sponsor_type != 'undefined') {
                            if (hall.viewaccess.includes(sponsor_type)) {
                                showhall = 1;
                            }
                        }
                        if (user_profile_type != '') {
                            if (hall.viewaccess.includes(user_profile_type)) {
                                showhall = 1;
                            }
                        }
                        if (rsvp_sponsor_title != '') {
                            if (hall.viewaccess.includes(rsvp_sponsor_title)) {
                                showhall = 1;
                            }
                        }
                    }
                }

                if (showhall == 1 && typeof hall_name !== 'undefined' && hall_name !== "undefined" && ($.inArray((hall_name), speaker_hall_list) !== -1 || $.inArray((hall_name), exhibitor_hall_list.flat()) !== -1)) {
                    let hall_option = `<a class="dropdown-item" onclick="LoadMetaWithHall('','${hall_token}','${hall_name}','agenda');">${hall_name}</a>`;
                    $('.agenda_hall_list').append(hall_option);

                    const hallLink = `${TAOH_CURR_APP_URL}/hall/${eventtoken}/${encodeURIComponent(hall_name)}-${hall_id}`;
                    const isSelected = taoh_desc_decode(hall_name).toLowerCase() === eventhall;
                    $('#search_halls').append(
                        $('<option>', {
                            value: hallLink,
                            text: taoh_desc_decode(hall_name),
                            selected: isSelected
                        })
                    );
                    searchhallcount++;
                }
            });
            if (searchhallcount == 0) {
                $('#search_halls').hide();
            }
        }

    </script>

<?php taoh_get_footer(); ?>