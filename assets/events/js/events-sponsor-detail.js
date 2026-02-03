    var _esd = window._esd_cfg || {};

    const isLoggedIn = _esd.isLoggedIn;
    let eventtoken = _esd.eventtoken;
    let eventToken = _esd.eventtoken;
    let sponsor_id = _esd.sponsorId;
    const my_pToken = _esd.myPtoken;
    const my_username = _esd.myUsername;
    let TAOH_CURR_APP_URL = _esd.currAppUrl;
    let is_user_rsvp_done = _esd.isUserRsvpDone;
    let rsvp_slug = _esd.rsvpSlug;
    const isValidUser = _esd.isValidUser;

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
                    user_timezone = _esd.userTimezone;
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
                                            ? event_output.conttoken.external_link : _taoh_site_url_root + '/' + _esd.currAppSlug + '/club/' + (event_output.conttoken.title) + '-' + event_output.eventtoken;
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
