var _exd = window._exd_cfg || {};

const isLoggedIn = _exd.isLoggedIn;
let eventtoken = _exd.eventtoken;
let eventToken = _exd.eventtoken;
let exhibitor_id = _exd.exhibitorId;
const my_pToken = _exd.myPtoken;
const my_username = _exd.myUsername;
let TAOH_CURR_APP_URL = _exd.currAppUrl;
let is_user_rsvp_done = _exd.isUserRsvpDone;
const isValidUser = _exd.isValidUser;
let click_view = _exd.clickView;


$(document).ready(function () {
    if (isLoggedIn) {
        save_metrics('exhibitor', click_view, eventToken);
    }

    function getEventExhibitorInfo(requestData, serverFetch = false, callback = null) {
        if (!requestData.eventtoken || !requestData.exhibitor_id) return;

        const event_exhibitor_key = `event_MetaInfo_${requestData.eventtoken}_exhibitor_${requestData.exhibitor_id}`;

        const handleResponse = (response, saveToDB = true) => {
            console.log(response);
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
                    exhibitor_id: requestData.exhibitor_id
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
        exhibitor_id
    }, false, (requestData, response) => {
        if (response.success) {
            let event_exhibitor_info = response.output;
            let event_exhibitor_room_status = parseInt(event_exhibitor_info.exh_room_status) === 1;

            let user_timezone;

            if (isLoggedIn) {
                user_timezone = _exd.userTimezone;
            }
            if (!isLoggedIn || !user_timezone?.trim()) {
                let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
            }
            if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

            const exhibitorBannersArray = [event_exhibitor_info.exh_banner].filter(url => typeof url === 'string' && url.trim() !== "" && isValidURL(url)).map(url => ({
                src: url,
                type: getMediaType(url)
            }));

            const galleryContainer = document.getElementById("exhibitor_banner_container");
            // const mainDisplay = document.createElement("div");
            const mainDisplay = document.getElementById("exhibitor_banner_image");
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

            function displayMedia(media) {
                if (!media) return;

                mainDisplay.innerHTML = "";
                let mediaHtml = "";

                if (media.type === "image") {
                    mediaHtml = `
                    <div class="item">
                        <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('${media.src}');"></div>
                            <div class="glass-overlay"></div>
                            <img class="carou-main-img" src="${media.src}" alt="Image 2">
                        </div>
                    </div>
                `;
                    /* <div class="cover-event-image">
                            <div class="exhibitor-bg" style="background-image: url('${media.src}');"></div>
                            <div class="glass-overlay"></div>
                            <img src="${media.src}" class="main-image" alt="Event">
                        </div> */
                } else if (media.type === "video") {
                    let videoSrc = formatVideoSrc(media.src);
                    mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay" style="width: 100%; height: 480px;"></iframe>`;
                }

                mainDisplay.innerHTML = mediaHtml;
            }

            if (exhibitorBannersArray[0]) {
                displayMedia(exhibitorBannersArray[0]);
            } else {
                const noImage = _taoh_site_url_root + '/assets/images/hall-detail.png';
                const mediaHtml = `
                <div class="item">
                    <div class="cover-event-image">
                        <div class="exhibitor-bg" style="background-image: url('${noImage}');"></div>
                        <div class="glass-overlay"></div>
                        <img src="${noImage}" class="carou-main-img" alt="Event">
                    </div>
                </div>`;
                $('#exhibitor_banner_image').html(mediaHtml);
            }
            if (event_exhibitor_info.exh_hero_button_text && isValidURL(event_exhibitor_info.exh_hero_button_url)) {
                $('#exhibitor_banner_image').append(`<a href="${event_exhibitor_info.exh_hero_button_url}" target="_blank" class="btn hero-button">${event_exhibitor_info.exh_hero_button_text}</a>`);
            }

            if (event_exhibitor_info.exh_logo && isValidURL(event_exhibitor_info.exh_logo)) {
                $('#exhibitor_logo').attr('src', event_exhibitor_info.exh_logo);
            }

            $('#exhibitor_title').text(taoh_desc_decode_new(event_exhibitor_info.exh_session_title));
            $('#exhibitor_subtitle').text(taoh_desc_decode_new(event_exhibitor_info.exh_subtitle));

            let exhibitorWebsiteUrl = event_exhibitor_info.exh_hero_button_url || '';
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

            let exhibitorLocationUrl = event_exhibitor_info.exh_room_location || '';
            if (event_exhibitor_room_status && exhibitorLocationUrl.trim()) {
                if (isValidURL(exhibitorLocationUrl)) {
                    $('#exhibitor_location_blk').html(`<div class="hall-text-sm-bold mb-2"><i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location :
                    <a href="${exhibitorLocationUrl}" class="btn link" id="exhibitor_location"><span>${exhibitorLocationUrl}</span></a></div>`);
                } else {
                    $('#exhibitor_location_blk').html(`<div class="btn link" id="exhibitor_location">
                    <i class="fa fa-map-marker mr-1" aria-hidden="true"></i> Location : <span title="${exhibitorLocationUrl}">${exhibitorLocationUrl}</span></div>`);
                }
                $('#exhibitor_location_blk').show();
                $('#exhibitor_room_btn_blk').show();
            }

            if(!isLoggedIn || !is_user_rsvp_done) {
                $("#exhibitor_contact_us,#write_review_blk,.star-con").addClass('disabled');
            }

            if ($.trim(event_exhibitor_info.exh_raffle_announce_time) != '') {
                exh_raffle_announce_time = new Date(event_exhibitor_info.exh_raffle_announce_time);
                let raffle_time = {
                    utc_datetime: event_exhibitor_info.exh_raffle_announce_time.replace(/[T:-]/g, '') + '00',
                    local_datetime: event_exhibitor_info.exh_raffle_announce_time.replace(/[T:-]/g, '') + '00',
                    timezone: event_exhibitor_info.exh_raffle_timezoneSelect,
                    locality: ''
                };
                let raffle_announce_time = format_event_timestamp(raffle_time, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A', 1);

                $('.time_timezone').html(raffle_announce_time);
            }

            if (event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                $('#perform_download').show();
            }
            $('#raffle_question').val(event_exhibitor_info.exh_raffle_ques);
            $('#raffle_question_title').html(event_exhibitor_info.exh_raffle_ques + '?');
            let exhRaffles = event_exhibitor_info.exh_raffles;
            if (exhRaffles == 1) {
                raffle_start = new Date(event_exhibitor_info.exh_raffle_start_time);
                raffle_end = new Date(event_exhibitor_info.exh_raffle_stop_time);
                const now = new Date(new Date().toLocaleString("en-US", {timeZone: user_timezone}));

                if (event_exhibitor_info.exh_raffles_timebound_option == 0 || (event_exhibitor_info.exh_raffles_timebound_option == 1 && now >= raffle_start && now <= raffle_end)) { // raffle date conditions
                    $("#raffle_title").text(taoh_desc_decode(event_exhibitor_info.exh_raffle_title));
                    $("#raffle_description").text(taoh_desc_decode(event_exhibitor_info.exh_raffle_description));
                    $("#exhibitor_raffle_blk").show();
                } else {
                    $("#exhibitor_raffle_blk").hide();
                }
            } else {
                $("#exhibitor_raffle_blk").hide();
                $("#download_raffle").hide();
            }

            /* Start : Update rating */
            $(".rating-text,.rating-block,#avg_review").hide();
            if (event_exhibitor_info.metrics) {
                if (event_exhibitor_info.metrics.view && event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                    $(".click_view").html(`(${event_exhibitor_info.metrics.view} views)`);
                }
                if (event_exhibitor_info.metrics.user_rating) {
                    $(`input[name="rating"][value="${event_exhibitor_info.metrics.user_rating}"]`).prop('checked', true);
                    for (i = 1; i <= event_exhibitor_info.metrics.user_rating; i++) {
                        $(`label[for="star${i}"]`).addClass('like');
                    }
                }
                if (event_exhibitor_info.metrics.rating_avg > 0 && event_exhibitor_info.ptoken != undefined && my_ptoken == event_exhibitor_info.ptoken) {
                    $(".rating-text,.rating-block,#avg_review").show();
                    // $("#review_count").text('('+event_exhibitor_info.metrics.rating_count+' ratings)');
                    $("#review_count").text(event_exhibitor_info.metrics.rating_count);
                    $("#average_count").text(Number(event_exhibitor_info.metrics.rating_avg.toFixed(1)));
                    /* let average = event_exhibitor_info.metrics.rating_avg;
                    $(".average-stars span").each(function (index) {
                        let starValue = index + 1; // Star position (1 to 5)
                        $(this).removeClass("filled half-filled"); // Reset previous classes

                        if (average >= starValue) {
                            $(this).addClass("filled"); // Full star
                        } else if (average >= starValue-1 && (average - (starValue - 1)) > 0) {
                            fillPercent = (average - (starValue - 1)) * 100;
                            $(this).css({
                                "background": `linear-gradient(to right, gold ${fillPercent}%, #ccc ${100-fillPercent}%)`,
                                "-webkit-background-clip": "text",
                                "-webkit-text-fill-color": "transparent"
                            });
                        }
                    }); */
                }
            }
            /* End : Update rating */
            getEventBaseInfo({eventtoken}, false)
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
                    let event_timestamp_end_data = {
                        utc_datetime: event_output.utc_end_at,
                        local_datetime: event_output.local_end_at,
                        timezone: event_output.local_timezone,
                        locality: event_output.conttoken.locality
                    };
                    let event_start_at = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                    let event_end_at = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'EEEE, dd MMM yyyy, hh:mm A');
                    let event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
                    var chat_room_status = parseInt(event_output.conttoken.chat_room_status || 1, 10);
                    var liveLink = '';
                    if (event_live_state == 'live') {
                        event_live_link = (chat_room_status == 2 && isValidUrl(event_output.conttoken.external_link))
                            ? event_output.conttoken.external_link : _taoh_site_url_root + '/' + _exd.currAppSlug + '/club/' + (event_output.conttoken.title) + '-' + event_output.eventtoken;
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
                                    <a target="_blank" href="${event_live_link}" class="mr-lg-5 btn btn-success w-100 metrics_action" data-metrics="event_join">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 80" style="width: 36px"><circle cx="40" cy="40" r="28" fill="#fff"></circle>

                                            <polygon points="34,28 34,52 54,40" fill="#28A745"></polygon>

                                            <path d="M78 26 C84 35, 84 46, 78 54" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>

                                            <path d="M88 10 C104 28, 104 54, 88 70" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"></path>
                                    </svg>
                                    ${chatroom_text}</a>
                                 </div>`;
                        } else {
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
                    ${event_exhibitor_info.exh_session_title ? `<li class="nav-item">${taoh_desc_decode(event_exhibitor_info.exh_session_title)}</li>` : ''}
                `;
                    $('#exhibitor_breadcrumb').append(exhibitorBreadcrumbHTML);
                    $("#event_title").text(event_title);

                    let localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
                    let localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);


                    $('#event_start_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));

                    //$("#event_start_datetime").text(event_start_at);
                    $("#event_end_datetime").text(event_end_at);

                    let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                        .split(',')
                        .map(token => token.trim())
                        .filter(token => token);

                    if(event_owner) event_organizer_ptokens.push(event_owner);

                    let event_instance_owner = conttoken_data.ptoken;
                    event_organizer_ptokens.push(event_instance_owner);

                    if (event_organizer_ptokens.includes(my_ptoken)) {
                        $(".overallrating").hide();
                        $(".review_count").hide();
                    }


                    let exhibitorExternalRoomUrl = event_exhibitor_info.exh_external_video_room_link || '';
                    let enable_tao_networking = event_exhibitor_info.enable_tao_networking || '';
                    let exhStreamingLinkUrl = event_exhibitor_info.exh_streaming_link || '';

                    let disableJoinBtn = 'disabled';
                    if (isLoggedIn && is_user_rsvp_done && event_live_state == 'live') {
                        disableJoinBtn = '';
                    }

                    if(disableJoinBtn == '' && event_exhibitor_info.exh_state != "live" && event_exhibitor_info.exh_state != "active") {
                        disableJoinBtn = 'disabled';
                    }

                    if (event_exhibitor_room_status && (exhibitorExternalRoomUrl.trim() || (enable_tao_networking == '1' || enable_tao_networking == 'on') || (exhStreamingLinkUrl.trim() && exhStreamingLinkUrl != ''))) {
                        const exhibitor_video_room_links = $('#exhibitor_video_room_links');

                        if ((enable_tao_networking == 0 || enable_tao_networking == 'off') && exhibitorExternalRoomUrl != '') {
                            if (isValidURL(exhibitorExternalRoomUrl)) {
                                exhibitor_video_room_links.html(`<a target="_blank" href="${exhibitorExternalRoomUrl}" class="btn v2-room-btn py-2 join_video_link" id="external_video_room" ${disableJoinBtn}>
                        <span class="px-2">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room</span></a>`); // Video
                            } else {
                                exhibitor_video_room_links.html(`<div class="btn v2-room-btn py-2" id="external_video_room" ${disableJoinBtn}>
                        <span class="px-2" title="${exhibitorExternalRoomUrl}">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room ${exhibitorExternalRoomUrl}</span></div>`); // Video
                            }
                        } else {
                            exhibitor_video_room_links.html(`<a target="_blank" href="${_taoh_site_url_root}/${_exd.currAppSlug}/club/${eventtoken}-${eventtoken}?exhbitor_id=${exhibitor_id}&exhbitor_name=${encodeURIComponent(event_exhibitor_info.exh_session_title)}" class="btn v2-room-btn py-2 join_networking" id="external_video_room" ${disableJoinBtn}>
                    <span class="px-2">Join ${taoh_desc_decode(event_exhibitor_info.exh_session_title)} Room</span></a>`);
                        }
                        exhibitor_video_room_links.show();
                        $('#exhibitor_room_btn_blk').show();
                    }
                });

            const safeMessage = document.createElement('pre');
            safeMessage.textContent = taoh_desc_decode_new(event_exhibitor_info.exh_description) || '';
            // let safeMessageHtml = event_exhibitor_info.exh_description || '';
            /* let safeMessageHtml = safeMessage.innerHTML.replace(/\n/g, '<br>')
                .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;').replace(/\r\n/g, '<br>'); */
            let safeMessageHtml = safeMessage.innerHTML
                .replace(/\r\n/g, '<br>')
                .replace(/\n/g, '<br>')
                .replace(/\t/g, '&nbsp;&nbsp;&nbsp;&nbsp;');

            $('#exh_description').html((safeMessageHtml));

            if (event_exhibitor_info.exh_tags != '') {
                $.each(event_exhibitor_info.exh_tags, function (k, tag) {
                    $("#exh_tags").append("<span>" + tag + "</span>|");
                });
            }


            let exhibitorContactEmail = event_exhibitor_info.exh_contact_email || '';
            if (event_exhibitor_info.enable_leadgen_form != 1 && exhibitorContactEmail.trim()) {
                // $('#exhibitor_contact_us').attr('href', 'mailto:' + exhibitorContactEmail);
                $('#exhibitor_contact_us').attr('data-email', exhibitorContactEmail);
                $('#exhibitor_contact_us').show();
                $('#write_review_blk').hide();
                $("#download_leadgen").hide();
            } else {
                $('#write_review_blk').show();
            }

            if ($.trim(event_exhibitor_info.exh_winner_profile) != '' && event_exhibitor_info.exh_winner_profile != undefined) {
                getUserInfo(event_exhibitor_info.exh_winner_profile).then((event_userinfo) => {
                    if (!event_userinfo.is_unknown) {
                        user_info_type = event_userinfo.type;

                        $(".emp-badge").html(user_info_type);
                        if (event_userinfo.avatar_image != '') {
                            $(".n-participants-img").attr("src", event_userinfo.avatar_image);
                        } else if (event_userinfo.avatar != '') {
                            $(".n-participants-img").attr("src", _exd.opsAvatarPrefix + event_userinfo.avatar + '.png');
                        }
                        $(".n-participants-name").html(event_userinfo.chat_name);
                        $("#n-full-location").html(event_userinfo.full_location);
                        $.each(event_userinfo.title, function (i, title) {
                            $("#n-role").html(title.value);
                            $("#n-role").attr('title', title.slug);
                        });
                        $.each(event_userinfo.skill, function (i, skills) {
                            $("#skill-list").append(`<p class="less-content-z3mbltb5mrf1"><span class="btn btn-sm skill_list skill_directory" style="margin-right:5px;background-color:#797f871a; font-size:12px;" data-skillid="${i}" data-skillslug="${skills.slug}">${skills.value}</span></p>`);
                        });

                        $('.winner_profile').show();
                        $('#exhibitor_raffle_blk').addClass('disabled');
                    } else {
                        $('.winner_profile').hide();
                    }
                });
            } else {
                $('.winner_profile').hide();
            }

            if (event_exhibitor_info.enable_leadgen_form != 1 && exhibitorContactEmail.trim() && exhRaffles != 1) {
                $("#perform_download").hide();
            }
            if (event_exhibitor_info.exh_raffle_status != undefined && event_exhibitor_info.exh_raffle_status == 'closed') {
                $("#avail_now").attr('disabled', true);
                $(".avail_now").addClass('disabled');
            }
        }

        $.ajax({
            url: _taoh_site_ajax_url,
            type: 'POST',
            data: {
                taoh_action: 'get_event_exhibitor_raffle',
                token: _taoh_ajax_token,
                ptoken: my_pToken,
                username: my_username,
                eventtoken: eventtoken,
                exhibitor_id: exhibitor_id
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // console.log(response.output);
                    var result = $.grep(response.output, function (obj) {
                        return obj["ptoken"] === my_pToken;
                    });
                    if (result.length > 0) {
                        $("#avail_now").attr('disabled', true);
                        $(".avail_now").addClass('disabled');
                    }
                }
            },
            error: function (xhr, status, error) {
                console.log('Error:', xhr.status + '   error : ' + error);
            }
        });

        if (!isLoggedIn || !is_user_rsvp_done) {
            $("#avail_now").attr('disabled', true);
            $(".avail_now").addClass('disabled');
        }
        $('.aw').awloader('hide');

        $('input[name="rating"]').on('change', function () {
            save_metrics('exhibit_rating', 'click', eventtoken);
            $('.aw').awloader('show');
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'post',
                data: {
                    'action': 'update_exhibitor_rating',
                    'taoh_action': 'update_exhibitor_rating',
                    'exhibitor_id': exhibitor_id,
                    'eventtoken': eventtoken,
                    'ptoken': my_pToken,
                    'rating': $(this).val()
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        taoh_set_success_message('Rating updated successfully', false);
                        $('.aw').awloader('hide');
                        event_exhibitor_key = `event_MetaInfo_${eventtoken}_exhibitor_${exhibitor_id}`;
                        IntaoDB.removeItem(objStores.event_store.name, event_exhibitor_key);
                        location.reload();
                    } else {
                        taoh_set_error_message('Failed to process your data! Try Again', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('Error:', xhr.status);
                }
            });
        });
    });

    $('#review_form').validate({
        rules: {
            exh_review: {
                required: true,
            }
        },
        messages: {
            exh_review: {
                required: "Comments is required",
            }
        },
        submitHandler: function (form) {

            let review_form = $('#review_form');
            let formData = new FormData(form);

            let submit_btn = review_form.find('button[type="submit"]');
            submit_btn.prop('disabled', true);

            let submit_btn_icon = submit_btn.find('i');
            submit_btn_icon.removeClass('fa-arrow-circle-o-right').addClass('fa-spinner fa-spin');

            $("#review_submit").attr('disabled', true);
            $.ajax({
                url: review_form.attr('action'),
                type: 'post',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function (response) {
                    // console.log(response);
                    if (response.success) {
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                        submit_btn.prop('disabled', false);
                        taoh_set_success_message('Comments Saved Successfully.');
                        $('#review_form')[0].reset();
                        $("#writereviewModal").modal("hide");
                        location.reload();
                    } else {
                        submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                        submit_btn.prop('disabled', false);
                        taoh_set_error_message('Failed to process your data! Try Again', false);
                    }
                },
                error: function (xhr, status, error) {
                    submit_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-arrow-circle-o-right');
                    submit_btn.prop('disabled', false);
                    console.log('Error:', xhr.status);
                }
            });


        }
    });

    <!-- carousel script -->
    $('.exhibitor-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: false, // Disable default nav
        dots: false, // Disable dots
        items: 1
    });

    // Custom navigation
    $('.exh-owl-prev').click(function () {
        $('.owl-carousel').trigger('prev.owl.carousel');
    });

    $('.exh-owl-next').click(function () {
        $('.owl-carousel').trigger('next.owl.carousel');
    });
    <!-- /carousel script -->

    <!-- star hover script -->
    const stars = document.querySelectorAll('.star-con .star');
    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', () => {
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('hovered');
            }
        });

        star.addEventListener('mouseleave', () => {
            stars.forEach(s => s.classList.remove('hovered'));
        });
    });
    <!-- /star hover script -->
});

function submitRaffleQuestion() {
    var my_reply = '';
    var raffle_question = $('#raffle_question').val();
    if (raffle_question != '') {
        my_reply = $('#raffle_answer').val();
        if (my_reply == '') {
            taoh_set_error_message('Please enter your reply!', false);
            return false;
        }
    }


    $.ajax({
        url: _taoh_site_ajax_url,
        type: 'POST',
        data: {
            taoh_action: 'update_event_exhibitor_raffle',
            token: _taoh_ajax_token,
            ptoken: my_pToken,
            username: my_username,
            eventtoken: eventtoken,
            exhibitor_id: exhibitor_id,
            answer: my_reply
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                $("#avail_now").attr('disabled', true);
                $(".avail_now").addClass('disabled');
                $('#raffleAnswerModal').modal('hide');
                $('#rafflesDetailModal').modal('show');
                taoh_set_success_message('You’ve successfully entered the raffle! Good luck!', false);
            }
        },
        error: function (xhr, status, error) {
            console.log('Error:', xhr.status + '   error : ' + error);
        }
    });

}

$(document).on('click', '#avail_now', function () {
    save_metrics('exhibit_avail_raffle', 'click', eventToken);
    var raffle_question = $('#raffle_question').val();
    if (raffle_question != '') {
        $('#raffleAnswerModal').modal('show');
    } else {
        submitRaffleQuestion();
    }

});

$(document).on('click', '#submitRaffleAnswer', function () {
    submitRaffleQuestion();
});

async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
    if (!pToken_to?.trim()) return null;

    let userInfo = {};

    if (!serverFetch) {
        // Try to get userInfo from IndexedDB
        if (!userInfo.ptoken) {
            const user_info_key = 'user_info_list';
            const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
            if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                let userInfoObj = intao_data.values[ops][pToken_to];
                // Check if data is expired (expires after 2 day)
                if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                    userInfo = userInfoObj;
                }
            }
        }
    }

    // Fetch userInfo from server if not found locally
    if (!userInfo.ptoken) {
        const formData = {
            taoh_action: 'taoh_user_info',
            ops: ops,
            ptoken: pToken_to
        };

        try {
            const srv_userInfoObj = await fetchUserInfoFromServer(formData);
            srv_userInfoObj.last_fetch_time = Date.now();
            userInfo = srv_userInfoObj;
        } catch (e) {
            console.log('getUserInfo error:', e);
        }
    }

    // If userInfo not found, set default values
    if (!userInfo.ptoken) {
        userInfo = {
            ptoken: pToken_to,
            chat_name: 'Unknown Name',
            avatar: 'default',
            full_location: 'Unknown Location',
            type: 'Unknown Type',
            is_unknown: true,
            last_fetch_time: Date.now()
        };
    }

    return userInfo;
}

function toggleOtherPurpose() {
    const select = document.getElementById("leadgen_purpose");
    const otherInput = document.getElementById("other_purpose");

    if (select.value === "other") {
        otherInput.style.display = "inline";
        otherInput.setAttribute("required", "required");
    } else {
        otherInput.style.display = "none";
        otherInput.removeAttribute("required");
        otherInput.value = "";
    }
}

$(document).on('click', '#exhibitor_contact_us', function () {
    save_metrics('exhibit_contact_us', 'click', eventToken);
});

$(document).on('click', '#write_review_blk', function () {
    save_metrics('exhibit_write_review', 'click', eventToken);
});

$(document).on('click', '.join_networking', function () {
    save_metrics('exhibit_join_networking', 'click', eventToken);
});

$(document).on('click', '.join_video_link', function () {
    save_metrics('exhibit_join_video_link', 'click', eventToken);
});

$(document).on('click', '.metrics_action', function () {
    let action = $(this).data('metrics');
    save_metrics(action, 'click', eventToken);
});
