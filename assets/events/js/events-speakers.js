async function getEventSpeakers(eventtoken, response, hallColorArray, search, tab_name) {
    search = search || '';
    tab_name = tab_name || '';

    var colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];

    var speak_list = response.output.event_speaker;

    var isEmpty = function(v) {
        return !v || (Array.isArray(v) ? v.length === 0 : Object.keys(v).length === 0);
    };

    if (search == '' && isEmpty(speak_list)) {
        $('#speakers-tab').hide();
    }

    var is_content = 0;
    var eventHallAccess = [];
    var eventHallAccessKey = 'event_hall_access_' + eventtoken;
    var data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey);
    if (data?.values) {
        eventHallAccess = data?.values.output;
    }

    $('#speaker_list').html('');
    var content = '';
    var u = 0;
    if (speak_list != undefined && speak_list.length > 0) {
        var sponsor_type = $('#sponsor_type').val();
        var is_organizer = $("#is_organizer").val();
        var user_profile_type = $("#user_profile_type").val();
        var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
        var spk_count = 0;
        var spknum = 0;

        speak_list.sort(function (a, b) {
            var nameA = a.spk_name[0].toLowerCase();
            var nameB = b.spk_name[0].toLowerCase();

            if (nameA < nameB) {
                return -1;
            }
            if (nameA > nameB) {
                return 1;
            }
            return 0;
        });

        $.each(speak_list, function (i, v) {
            if (v.ptoken == my_ptoken) {
                spk_count++;
                hall_name = v.spk_hall;
                if (typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined") {
                    if (is_organizer == 1) {
                        if (typeof eventHallAccess['speaker'][hall_name]["organizer"] !== "undefined") {
                            eventHallAccess['speaker'][hall_name]['organizer']['allowed'] = eventHallAccess['speaker'][hall_name]['organizer']['allowed'] - 1;
                        }
                    }
                    if (sponsor_type != '' && sponsor_type != undefined) {
                        if (typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined" && typeof eventHallAccess['speaker'][hall_name][sponsor_type] !== "undefined") {
                            eventHallAccess['speaker'][hall_name][sponsor_type]['allowed'] = eventHallAccess['speaker'][hall_name][sponsor_type]['allowed'] - 1;
                        }
                    }
                    if (user_profile_type != '') {
                        if (typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined" && typeof eventHallAccess['speaker'][hall_name][user_profile_type] !== "undefined") {
                            eventHallAccess['speaker'][hall_name][user_profile_type]['allowed'] = eventHallAccess['speaker'][hall_name][user_profile_type]['allowed'] - 1;
                        }
                    }
                    if (rsvp_sponsor_title != '') {
                        if (typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined" && typeof eventHallAccess['speaker'][hall_name][rsvp_sponsor_title] !== "undefined") {
                            eventHallAccess['speaker'][hall_name][rsvp_sponsor_title]['allowed'] = eventHallAccess['speaker'][hall_name][rsvp_sponsor_title]['allowed'] - 1;
                        }
                    }
                }
            }

            if (Array.isArray(v.spk_name)) {
                v.spk_name.map(function (item, r) {
                    if (hallColorArray[v.spk_hall] != undefined)
                        backgroundColor = hallColorArray[v.spk_hall];
                    else {
                        color = colorArray[u % colorArray.length];
                        backgroundColor = color;
                    }
                    u++;

                    var spk_bio = '';
                    if ($.trim(v.spk_bio[r]) != '') {
                        var spkbio = taoh_desc_decode(v.spk_bio[r]);
                        if (spkbio.length > 110) {
                            var limitedText = spkbio.substring(0, 110);
                            spk_bio = limitedText + '<span id="dots_bio' + i + '">...</span><span id="more_bio' + i + '" style="display:none;">' + spkbio.substring(110) + ' </span>' +
                    '<button class="readmore-btn" onclick="readmore(\'bio' + i + '\')" id="morebtn_bio' + i + '">Read more</button>';
                        } else {
                            spk_bio = spkbio;
                        }
                    }

                    content += ' <div class="new-spk-con mb-3">' +
                        '<div class="new-spk-list flex-wrap p-3 pt-4">' +
                            '<img src="' + v.spk_profileimg[r] + '" alt="" class="open_speaker_detail" data-metrics="view_speaker" id="view_speaker_' + v.ID + '" data_id="' + v.ID + '" data_name="' + v.spk_name[r] + '">' +
                            '<div class="d-flex align-items-center justify-content-between" style="gap: 12px; flex: 1;">' +
                                '<div style="min-width: 130px;">' +
                                    '<h6 class="sp-name open_speaker_detail mb-1" data-metrics="view_speaker" id="view_speaker_' + v.ID + '" data_id="' + v.ID + '" data_name="' + v.spk_name[r] + '">' + item + '</h6>' +
                                    '<p class="sp-info">' + v.spk_desig[r] + ', ' + v.spk_company[r] + '</p>' +
                                '</div>' +
                                '<div class="mr-lg-5 d-flex align-items-center" style="gap: 6px;">' +
                                    ((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1 ?
                                    '<a title="Edit Speaker" style="min-width: unset;" class="svg-opt-con btn edit_speaker metrics_action" id="edit_speaker_' + v.ID + '" data_id="' + v.ID + '" data-metrics="edit_speaker">' +
                                        '<i class="fa-solid fa-edit"></i>' +
                                    '</a>' : '') +
                                    '<a title="View Speaker" class="svg-opt-con btn open_speaker_detail metrics_action" data-metrics="view_speaker" id="view_speaker_' + v.ID + '" data_id="' + v.ID + '" data_name="' + v.spk_name[r] + '">' +
                                     '<i class="fa-solid fa-eye"></i></a>' +
                                     (is_organizer == 1 ?
                                    '<a title="Delete Speaker" class="svg-opt-con btn p-0 delete-speaker metrics_action" id="delte_spk_' + v.ID + '" data-id="' + v.ID + '" data-type="speaker" data-metrics="delete_speaker">' +
                                    '<svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                        '<path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>' +
                                    '</svg>' +
                                    '</a>' : '') +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                     '</div>';

                    spknum++;
                });
            }

        });

        if (eventHallAccess > 0) {
            response = {'success': true, output: eventHallAccess};
            IntaoDB.setItem(objStores.event_store.name, {
                taoh_data: eventHallAccessKey,
                values: response,
                timestamp: Date.now()
            });
        }
        $("#spk_count").val(spk_count);
        is_content = 1;
    }

    var event_status = $('#event_status_hidden').val();

    if (!is_content) {
        if (search != '') {
            $('#speaker_list').html('<p class="text-center">No speakers found.</p>');
        } else {
            $(".speaker_filter").addClass("align-self-start lblur");
            if (event_status == 1 || event_status == 0) {
                $('#speaker_desc,#speaker_top').remove();
            } else {
                $("#speaker_default_list,#speaker_default_banner").show();
            }
        }
    } else {
        $(".speaker_filter").removeClass("align-self-start lblur");
        $("#speaker_default_list,#speaker_default_banner").hide();
        $('#speaker_list').html(content);
        $("#dropdownMenuLink_speaker,#speaker_search_block").show();

        var sponsor_type = $('#sponsor_type').val();
        if ((event_status == 2 || typeof event_status === "undefined") && sponsor_type == '') {
            $('#speaker_list .new-spk-con').each(function (index) {
                if ((index + 1) % 2 === 0 || $('#speaker_list .new-spk-con').length == 1) {
                    $(this).after(
                        '<div class="v2-banner flex-column flex-md-row p-3 px-lg-5">' +
                            '<div class="v2-svg-con">' +
                                '<svg width="59" height="62" viewBox="0 0 59 62" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                    '<path d="M49 5.5C49 2.46773 46.3079 0 43 0C39.6921 0 37 2.46773 37 5.5C37 8.53227 39.6921 11 43 11C46.3079 11 49 8.53227 49 5.5Z" fill="#2557A7"/>' +
                                    '<path d="M20 5.5C20 2.46773 17.5323 0 14.5 0C11.4677 0 9 2.46773 9 5.5C9 8.53227 11.4677 11 14.5 11C17.5323 11 20 8.53227 20 5.5Z" fill="#2557A7"/>' +
                                    '<path d="M21.9888 33H21.9611H0V40H59V33H21.9888Z" fill="#2557A7"/>' +
                                    '<path d="M31 16.7506V30.9924H35.5208V19.9691C35.5208 19.5797 35.8194 19.2666 36.1907 19.2666C36.562 19.2666 36.8606 19.5797 36.8606 19.9691V29.7475C36.8759 29.8078 36.8874 29.868 36.8874 29.9322V31H50.1394V19.9736C50.1394 19.5842 50.438 19.2711 50.8093 19.2711C51.1806 19.2711 51.4792 19.5842 51.4792 19.9736V31H56V16.7582C56 16.3809 55.889 16.0236 55.6823 15.7266C55.4181 15.3452 55.0162 15.0923 54.576 15.0241C54.4803 15.008 54.3999 15 54.3195 15H32.684C31.7577 15 31.0036 15.7908 31.0036 16.7622L31 16.7506Z" fill="#2557A7"/>' +
                                    '<path d="M1 16.7506V30.9924H5.70167V19.9691C5.70167 19.5797 6.0122 19.2666 6.39836 19.2666C6.78453 19.2666 7.09504 19.5797 7.09504 19.9691V29.7475C7.11097 29.8078 7.12291 29.868 7.12291 29.9322V31H20.905V19.9736C20.905 19.5842 21.2155 19.2711 21.6016 19.2711C21.9878 19.2711 22.2983 19.5842 22.2983 19.9736V31H27V16.7582C27 16.3809 26.8845 16.0236 26.6696 15.7266C26.3949 15.3452 25.9769 15.0923 25.519 15.0241C25.4195 15.008 25.3359 15 25.2523 15H2.75138C1.78796 15 1.00374 15.7908 1.00374 16.7622L1 16.7506Z" fill="#2557A7"/>' +
                                    '<path d="M4 41H56V62H4V41Z" fill="#2557A7"/>' +
                                    '<circle cx="16.5" cy="51.5" r="6.5" fill="#5DC1F1"/>' +
                                    '<rect x="27" y="46" width="23" height="4" rx="2" fill="#5DC1F1"/>' +
                                    '<rect x="27" y="51" width="23" height="5" rx="2.5" fill="#5DC1F1"/>' +
                                '</svg>' +
                            '</div>' +
                            '<div>' +
                                '<h6 class="mb-2">Unlock your Speaking Slot</h6>' +
                                '<p class="mb-2">Deliver Keynotes ! Inspire People !</p>' +
                                '<div class="d-flex align-items-center flex-wrap" style="gap: 6px;">' +
                                    '<button type="button" class="btn banner-v2-btn mt-0 mr-1 event_sponsor_right_header" id="sponsor_contactus" data-toggle="modal" data-target="#sponsorInfo">Become a Speaker !</button>' +
                                    '<button type="button" class="btn banner-v2-btn mt-0 mr-1 get-started event_sponsor_right_header" data-toggle="modal" data-target="#sponsorInfo">More Info !</button>' +
                                    '<a class="btn banner-v2-btn mt-0 mr-1" id="speaker_contactus" data-toggle="modal" data-target="#contactusModal">Contact us !</a>' +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                }
            });

            if (!isLoggedIn) {
                $(".get-started, #sponsor_contactus").hide();
            }

            getEventBaseInfo({eventtoken: eventToken}, false)
                .then(function(result) {
                    var event_output = result.response.output;
                    var conttoken_data = event_output.conttoken;

                    var eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
                    var eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(
                        function(widget) { return widget.quantity > 0 ? 1 : 0; }
                    );

                    if (!eventSponsorWidgetTypeStatusList.includes(1)) {
                        $('.event_sponsor_right_header, .get-started').hide();
                    }
                });
        }
    }

    loader(false, $("#speaker_loaderArea"));
}

$(document).on('click', '.open_speaker_detail', function () {
    var speaker_id = $(this).attr('data_id');
    var speaker_name = $(this).attr('data_name');
    loader(true, $("#speakerdet_loaderArea"),30);
    $('#speaker_info').html('');
    var data = {
        'taoh_action': 'speaker_get_detail',
        'ops': 'detail',
        'speaker_id': speaker_id,
        'ptoken': window._spk_ptoken || '',
        'eventtoken': eventtoken,
        'speaker_name' : speaker_name
    };
    jQuery.post(_taoh_site_ajax_url, data, function (response) {
        $('#speaker_info').html(response);
        loader(false, $("#speakerdet_loaderArea"));
    }).fail(function () {
        console.log("Network issue on response!");
    });
    $('#speakerDetailModal').modal('show');
});

function updateSpeakerTimeSlot(speaker_data, user_timezone) {
    var spk_datefrom = speaker_data.spk_datefrom;
    var spk_dateto = speaker_data.spk_dateto;
    var spk_timezone = speaker_data?.spk_timezoneSelect;

    $("#local_timezoneSelect_session").val(spk_timezone);
    $('#spk_timeslot_timezone_txt').text('in ' + spk_timezone);

    $("#spk_datefrom").val(spk_datefrom);
    $("#spk_dateto").val(spk_dateto);
}

$(document).on('click', '.edit_speaker', async function () {
    var spk_id = $(this).attr('data_id');
    loader(true, $("#speakerdet_loaderArea"));
    if (parseInt(spk_id)) {
        var eventHallAccess = [];
        var eventHallAccessKey = 'event_hall_access_' + eventToken;
        var data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey);
        if (data?.values) {
            eventHallAccess = data?.values.output;
        }
        var sponsor_type = $('#sponsor_type').val();
        var is_organizer = $("#is_organizer").val();
        var user_profile_type = $("#user_profile_type").val();
        var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
        getEventBaseInfo({ eventtoken: eventToken }, false)
            .then(function(result) {
            var requestData = result.requestData;
            var response = result.response;
            var event_output = response.output;
            var event_owner = event_output.ptoken;
            var conttoken_data = event_output.conttoken;
            var event_form_version = conttoken_data.event_form_version ?? 1;
            var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
            var ticketArr = conttoken_data.ticket_types.find(function(ticket) { return taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title; }) || {};

            var event_instance_owner = conttoken_data.ptoken;
            var event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                .split(',').concat(event_instance_owner)
                .map(function(token) { return token.trim(); })
                .filter(function(token) { return token; });

            if(event_owner) event_organizer_ptokens.push(event_owner);

            var form = $('#spk_form');
            if (!form.length) return;

            form[0].reset();
            form.find('input[type="hidden"][data-dynamic="1"]').val('');
            if (form.data('validator')) {
                form.validate().resetForm();
                form.find('.is-invalid, .is-valid, .error').removeClass('is-invalid is-valid error');
            }

            var tagsArr = conttoken_data?.event_tags ? conttoken_data.event_tags.split(",") : [];
            $(".tags-field").select2({
                data: tagsArr,
                width: '100%'
            });

            var spk_allowed = new Set(["1", "3"]);
            var allowed_speaker_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                .filter(function(h) { return Number(h?.id) > 0 && h?.name && spk_allowed.has(h.accesslevel); });

            if (!allowed_speaker_halls.length) {
                taoh_set_error_message('No Speaker Hall available for setup');
                return;
            }

            var user_timezone = window._spk_user_timezone || '';
            if (!user_timezone?.trim()) {
                var clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
            }
            if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

            $('#spk_hall').empty();
            $('#spk_hall').append('<option value="">Select Session Room</option>');
            allowed_speaker_halls.forEach(function(hall) {
                var hall_id   = hall.id;
                var hall_name = hall.name;
                var hall_token = (hall.name);
                var showhall = 0;

                if(event_form_version == 2){
                    if(is_organizer == 1){
                        showhall = 1;
                    }
                    if ( ticketArr && typeof ticketArr.max_sessions_allowed !== 'undefined' && ticketArr.max_sessions_allowed > 0 && ( ticketArr.speaker_halls === 'All' ||   (  Array.isArray(ticketArr.speaker_halls) && ( ticketArr.speaker_halls.includes('All') || ticketArr.speaker_halls.includes(hall_name) || ticketArr.speaker_halls.includes(hall_id))) ||   (  Array.isArray(ticketArr.session_halls) && ( ticketArr.session_halls.includes('All') || ticketArr.session_halls.includes(hall_name) || ticketArr.session_halls.includes(hall_id))) )) {
                        showhall = 1;
                    }
                }else{
                    if(typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined"){
                        if(is_organizer == 1){
                            showhall = 1;
                            if(typeof eventHallAccess['speaker'][hall_name]["organizer"] !== "undefined" && eventHallAccess['speaker'][hall_name]["organizer"]["allowed"] > 0){
                                showhall = 1;
                            }
                        }
                        if(sponsor_type != ''){
                            if(typeof eventHallAccess['speaker'][hall_name][sponsor_type] !== "undefined" && eventHallAccess['speaker'][hall_name][sponsor_type]["allowed"] > 0){
                                showhall = 1;
                            }
                        }
                        if(user_profile_type != ''){
                            if(typeof eventHallAccess['speaker'][hall_name][user_profile_type] !== "undefined" && eventHallAccess['speaker'][hall_name][user_profile_type]["allowed"] > 0){
                                showhall = 1;
                            }
                        }
                        if(rsvp_sponsor_title != ''){
                            if(typeof eventHallAccess['speaker'][hall_name][rsvp_sponsor_title] !== "undefined" && eventHallAccess['speaker'][hall_name][rsvp_sponsor_title]["allowed"] > 0){
                                showhall = 1;
                            }
                        }
                    }else{
                        showhall = 1;
                    }
                }

                    if(showhall == 1){
                        var hall_option = '<option value="' + hall_token + '">' + hall_name + '</option>';
                        $('#spk_hall').append(hall_option);
                    }
            });

            var data = {
                'taoh_action': 'get_speaker_detail',
                'ops': 'detail',
                'speaker_id': spk_id,
                'ptoken': window._spk_ptoken || '',
                'eventtoken': eventtoken,
            };
            jQuery.post(_taoh_site_ajax_url, data, function (response) {
                console.log('speaker_data', response);
                if(response.success){
                    var speaker_data = response.output;

                    $("#speaker_id").val(spk_id);
                    $("#spk_title").val(taoh_desc_decode(speaker_data.spk_title));
                    $("#spk_sdesc").val(taoh_desc_decode(speaker_data.spk_sdesc));
                    $("#spk_hall").val(speaker_data.spk_hall);
                    $("#spk_hero_button_text").val(speaker_data.spk_hero_button_text);
                    $("#spk_hero_button_url").val(speaker_data.spk_hero_button_url);
                    if(speaker_data.looking_for_session_memeber){
                        $("#looking_for_session_memeber").attr("checked",true);
                    }

                    updateSpeakerTimeSlot(speaker_data, user_timezone);

                    if(speaker_data.spk_logo_image != ''){
                        $('#spk_logo_image_preview').html('<img src="' + speaker_data.spk_logo_image + '" class="img-fluid" alt="Speaker Logo" width="50" />');
                        $("#spk_logo_image").val(speaker_data.spk_logo_image);
                    }
                    if(speaker_data.spk_image != ''){
                        $('#spk_image_preview').html('<img src="' + speaker_data.spk_image + '" class="img-fluid" alt="Speaker Banner" width="100" />');
                        $("#spk_image").val(speaker_data.spk_image);
                    }
                    if(speaker_data.spk_video_room !=undefined && speaker_data.spk_video_room.includes("tao_room")){
                        $("#spk_video_room-no").attr("checked",true);
                    }
                    if(speaker_data.spk_video_room !=undefined && speaker_data.spk_video_room.includes("zoom")){
                        $("#spk_video_room-yes").attr("checked",true);
                        $('input[name="spk_video_room[]"]').change();
                    }
                    if(speaker_data.spk_video_room !=undefined && speaker_data.spk_video_room.includes("physicalroom")){
                        $("#spk_video_room-physical").attr("checked",true);
                        $('input[name="spk_video_room[]"]').change();
                    }
                    $("#spk_zoom_url").val(speaker_data.spk_zoom_url);
                    $("#spk_phycial_location").val(speaker_data.spk_phycial_location);
                    $("#spk_streaming_link").val(speaker_data.spk_streaming_link);
                    $("#spk_external_video_room_link").val(speaker_data.spk_external_video_room_link);
                    $("#spk_room_location").val(speaker_data.spk_room_location);
                    $("#spk_state").val(speaker_data.spk_state);
                    $("#spk_template").val(speaker_data.spk_template);

                    if (speaker_data.enable_tao_networking == 1) {
                        $("#session_enable_tao_networking_yes").prop("checked", true);
                        $(".spk_external_video_room").hide();
                        $(".spk_streaming_link_wrapper").show();
                    } else {
                        $("#session_enable_tao_networking_no").prop("checked", true);
                        $(".spk_streaming_link_wrapper").hide();
                        $(".spk_external_video_room").show();
                    }

                    var tags = [].concat(speaker_data.spk_tags || []).filter(Boolean);
                    if (tags.length) {
                        $('#spk_tags').val(tags).trigger('change');
                    }

                    $("#spk_desc").val(taoh_desc_decode(speaker_data.spk_desc));

                    var $old_repeatable_speaker = $("#speaker_blk #repeatable_speaker");
                    var $new_repeatable_speaker = $('<div id="repeatable_speaker"></div>');
                    $old_repeatable_speaker.replaceWith($new_repeatable_speaker);
                    initRepeatableSpeaker($new_repeatable_speaker);

                    $.each(speaker_data.spk_name, function (i, data) {
                        if (i > 0) {
                            $("#speaker_blk .speaker_add").trigger("click");
                        }
                        $("#spk_name_"+i).val(data);
                        $("#spk_company_"+i).val(speaker_data.spk_company[i]);
                        $("#spk_desig_"+i).val(speaker_data.spk_desig[i]);
                        $("#spk_linkedin_"+i).val(speaker_data.spk_linkedin[i]);
                        $("#spk_bio_"+i).val(taoh_desc_decode(speaker_data.spk_bio[i]));
                        $("#spk_profileimg_"+i).val(speaker_data.spk_profileimg[i]);
                        $('#spk_profileimg_preview_'+i).html('<img src="' + speaker_data.spk_profileimg[i] + '" width="50" class="img-fluid" alt="profile image" />');
                    });
                }
                loader(false, $("#addspeaker_loaderArea"));
            }).fail(function () {
                loader(false, $("#addspeaker_loaderArea"));
                console.log("Network issue on response!");
            });

            var speakerslotmodal_elem = $('#speakerSlotModal');

            if($("#is_organizer").val() == 1){
                $("#spk_video_room_block").show();
                $("#spk_video_room-yes").prop("disabled",true);
            }else{
                $("#spk_video_room-no").prop("checked",false);
            }

            $('#speakerSlotModal').modal('show');
        }).catch(function(error) { console.error("Error fetching event info:", error); });
    }
});

$(document).on('click', '.delete-speaker', async function () {
    var meta_id = $(this).data('id');
    var meta_type = $(this).data('type');

    if (meta_id) {
        taoh_set_warning_message('Do you want to delete this speaker?', false, 'toast-middle', [
            {
                text: 'Yes',
                action: function() {
                    jQuery.post(_taoh_site_ajax_url, {
                        'taoh_action': 'delete_event_meta',
                        'token': _taoh_ajax_token,
                        'eventtoken': eventToken,
                        'meta_id': meta_id,
                        'meta_type': meta_type
                    }, function (response) {
                        if(response.success){
                            taoh_set_success_message('Speaker slot deleted successfully', false);
                            var event_meta_key = 'event_MetaInfo_' + eventToken;
                            IntaoDB.removeItem(objStores.event_store.name, event_meta_key);
                            location.reload();
                        }
                    });
                },
                class: 'dojo-v1-btn float-right mt-3 mb-3'
            },
            {
                text: 'No',
                action: function() {},
                class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
            }
        ]);
    }
});
