<?php
// Session Slot Modal
require_once('events_session_form.php');
?>
<div class="modal speaker-detail-modal fade" id="speakerDetailModal" tabindex="-1" role="dialog" 
        aria-labelledby="mySpeakerModalLabel" aria-hidden="true">
    <div class="modal-dialog bg-white" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white align-items-center py-3" style="border: none; border-bottom: 1.8px solid #d3d3d3;">
                <h4 class="text-heading d-flex align-items-center" style="gap: 12px;">
                    <svg width="40" height="40" viewBox="0 0 79 79" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="39.5" cy="39.5" r="39.5" fill="#457BE7"/>
                        <path d="M32.1364 27.3125V39.5C32.1364 43.5371 35.4347 46.8125 39.5 46.8125C43.5653 46.8125 46.8636 43.5371 46.8636 39.5H40.7273C40.0523 39.5 39.5 38.9516 39.5 38.2812C39.5 37.6109 40.0523 37.0625 40.7273 37.0625H46.8636V34.625H40.7273C40.0523 34.625 39.5 34.0766 39.5 33.4062C39.5 32.7359 40.0523 32.1875 40.7273 32.1875H46.8636V29.75H40.7273C40.0523 29.75 39.5 29.2016 39.5 28.5312C39.5 27.8609 40.0523 27.3125 40.7273 27.3125H46.8636C46.8636 23.2754 43.5653 20 39.5 20C35.4347 20 32.1364 23.2754 32.1364 27.3125ZM49.3182 38.2812V39.5C49.3182 44.8854 44.923 49.25 39.5 49.25C34.077 49.25 29.6818 44.8854 29.6818 39.5V36.4531C29.6818 35.44 28.8611 34.625 27.8409 34.625C26.8207 34.625 26 35.44 26 36.4531V39.5C26 46.2869 31.0778 51.8932 37.6591 52.7844V55.3438H33.9773C32.9571 55.3438 32.1364 56.1588 32.1364 57.1719C32.1364 58.185 32.9571 59 33.9773 59H39.5H45.0227C46.0429 59 46.8636 58.185 46.8636 57.1719C46.8636 56.1588 46.0429 55.3438 45.0227 55.3438H41.3409V52.7844C47.9222 51.8932 53 46.2869 53 39.5V36.4531C53 35.44 52.1793 34.625 51.1591 34.625C50.1389 34.625 49.3182 35.44 49.3182 36.4531V38.2812Z" fill="white"/>
                    </svg>
                    <span>Speaker details</span>
                </h4>
                
                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.6153 2.22013C13.1228 1.71256 13.1228 0.888255 12.6153 0.380681C12.1077 -0.126894 11.2834 -0.126894 10.7758 0.380681L6.5 4.66055L2.22013 0.384741C1.71256 -0.122833 0.888255 -0.122833 0.380681 0.384741C-0.126894 0.892316 -0.126894 1.71662 0.380681 2.22419L4.66055 6.5L0.384742 10.7799C-0.122833 11.2874 -0.122833 12.1117 0.384742 12.6193C0.892316 13.1269 1.71662 13.1269 2.22419 12.6193L6.5 8.33945L10.7799 12.6153C11.2874 13.1228 12.1117 13.1228 12.6193 12.6153C13.1269 12.1077 13.1269 11.2834 12.6193 10.7758L8.33945 6.5L12.6153 2.22013Z" fill="#D3D3D3"></path>
                    </svg>
                </button>
                
            </div>
            <div id="speakerdet_loaderArea" style="text-align: center;"></div>
            <div class="modal-body p-3 px-lg-5 pb-lg-5 pt-lg-4" id="speaker_info">
                
            </div>
        </div>
    </div>
</div>
<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var eve = "<?php echo $event_token ?? ''; ?>";

    async function getEventSpeakers(eventtoken, response, hallColorArray, search = '', tab_name = '') {
        var colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];

        var speak_list = response.output.event_speaker;

        const isEmpty = v =>
            !v || (Array.isArray(v) ? v.length === 0 : Object.keys(v).length === 0);

        if (search == '' && isEmpty(speak_list)) {
            $('#speakers-tab').hide();
        }

        var is_content = 0;
        var eventHallAccess = [];
        var eventHallAccessKey = `event_hall_access_${eventtoken}`;
        const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey);
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

            /* speak_list.sort(function(a, b) {
                return  (a['spk_title'] || '').trim().localeCompare((b['spk_title'] || '').trim());
            }); */
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
            /* const distinctHalls = new Set(speak_list.map(speaker => speaker.spk_hall));
            const hallColorMap = {};
            distinctHalls.forEach(hall => {
                hallColorMap[hall] = getRandomColor();
            }); */
            // hallColorMap = JSON.parse($("#hallcolor").val());

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
                            // console.log('Length : '+vdesc.length);
                            if (spkbio.length > 110) {
                                var limitedText = spkbio.substring(0, 110);
                                spk_bio = limitedText + `<span id="dots_bio${i}">...</span><span id="more_bio${i}" style="display:none;">${spkbio.substring(110)} </span>
                    <button class="readmore-btn" onclick="readmore('bio${i}')" id="morebtn_bio${i}">Read more</button>`;
                            } else {
                                spk_bio = spkbio;
                            }
                        }

                        content += ` <div class="new-spk-con mb-3">
                                <div class="new-spk-list flex-wrap p-3 pt-4">
                                    <img src="${v.spk_profileimg[r]}" alt="" class="open_speaker_detail" data-metrics="view_speaker" id="view_speaker_${v.ID}" data_id="${v.ID}" data_name="${v.spk_name[r]}">
                                    <div class="d-flex align-items-center justify-content-between" style="gap: 12px; flex: 1;">
                                        <div style="min-width: 130px;">
                                            <h6 class="sp-name open_speaker_detail mb-1" data-metrics="view_speaker" id="view_speaker_${v.ID}" data_id="${v.ID}" data_name="${v.spk_name[r]}">${item}</h6>
                                            <p class="sp-info">${v.spk_desig[r]}, ${v.spk_company[r]}</p>
                                        </div>

                                        <div class="mr-lg-5 d-flex align-items-center" style="gap: 6px;">
                                            
                                            ${(v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1 ? `
                                            <a title="Edit Speaker" style="min-width: unset;" class="svg-opt-con btn edit_speaker metrics_action" id="edit_speaker_${v.ID}" data_id="${v.ID}" data-metrics="edit_speaker">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>` : ''}
                                            <a title="View Speaker" class="svg-opt-con btn open_speaker_detail metrics_action" data-metrics="view_speaker" id="view_speaker_${v.ID}" data_id="${v.ID}" data_name="${v.spk_name[r]}">
                                             <i class="fa-solid fa-eye"></i></a>

                                             ${is_organizer == 1 ? `
                                            <a title="Delete Speaker" class="svg-opt-con btn p-0 delete-speaker metrics_action" id="delte_spk_${v.ID}" data-id="${v.ID}" data-type="speaker" data-metrics="delete_speaker">
                                            <svg width="15" height="15" viewBox="0 0 42 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5.25 0C2.3543 0 0 2.3543 0 5.25V31.5C0 34.3957 2.3543 36.75 5.25 36.75H36.75C39.6457 36.75 42 34.3957 42 31.5V5.25C42 2.3543 39.6457 0 36.75 0H5.25ZM14.3555 11.7305C15.1266 10.9594 16.3734 10.9594 17.1363 11.7305L20.9918 15.5859L24.8473 11.7305C25.6184 10.9594 26.8652 10.9594 27.6281 11.7305C28.391 12.5016 28.3992 13.7484 27.6281 14.5113L23.7727 18.3668L27.6281 22.2223C28.3992 22.9934 28.3992 24.2402 27.6281 25.0031C26.857 25.766 25.6102 25.7742 24.8473 25.0031L20.9918 21.1477L17.1363 25.0031C16.3652 25.7742 15.1184 25.7742 14.3555 25.0031C13.5926 24.232 13.5844 22.9852 14.3555 22.2223L18.2109 18.3668L14.3555 14.5113C13.5844 13.7402 13.5844 12.4934 14.3555 11.7305Z" fill="#FF0000"></path>
                                            </svg>
                                            </a>` : ''}
                                        </div> 
                                    </div>
                                </div>
                             </div>`;

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
            if ((event_status == 2 || typeof event_status === "undefined") && sponsor_type == '') { // not live and not rsvped as sponsor
                $('#speaker_list .new-spk-con').each(function (index) {
                    if ((index + 1) % 2 === 0 || $('#speaker_list .new-spk-con').length == 1) { // show 0 day banner for every 2nd block
                        $(this).after(`
                        <div class="v2-banner flex-column flex-md-row p-3 px-lg-5">
                            <div class="v2-svg-con">
                                <svg width="59" height="62" viewBox="0 0 59 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M49 5.5C49 2.46773 46.3079 0 43 0C39.6921 0 37 2.46773 37 5.5C37 8.53227 39.6921 11 43 11C46.3079 11 49 8.53227 49 5.5Z" fill="#2557A7"/>
                                    <path d="M20 5.5C20 2.46773 17.5323 0 14.5 0C11.4677 0 9 2.46773 9 5.5C9 8.53227 11.4677 11 14.5 11C17.5323 11 20 8.53227 20 5.5Z" fill="#2557A7"/>
                                    <path d="M21.9888 33H21.9611H0V40H59V33H21.9888Z" fill="#2557A7"/>
                                    <path d="M31 16.7506V30.9924H35.5208V19.9691C35.5208 19.5797 35.8194 19.2666 36.1907 19.2666C36.562 19.2666 36.8606 19.5797 36.8606 19.9691V29.7475C36.8759 29.8078 36.8874 29.868 36.8874 29.9322V31H50.1394V19.9736C50.1394 19.5842 50.438 19.2711 50.8093 19.2711C51.1806 19.2711 51.4792 19.5842 51.4792 19.9736V31H56V16.7582C56 16.3809 55.889 16.0236 55.6823 15.7266C55.4181 15.3452 55.0162 15.0923 54.576 15.0241C54.4803 15.008 54.3999 15 54.3195 15H32.684C31.7577 15 31.0036 15.7908 31.0036 16.7622L31 16.7506Z" fill="#2557A7"/>
                                    <path d="M1 16.7506V30.9924H5.70167V19.9691C5.70167 19.5797 6.0122 19.2666 6.39836 19.2666C6.78453 19.2666 7.09504 19.5797 7.09504 19.9691V29.7475C7.11097 29.8078 7.12291 29.868 7.12291 29.9322V31H20.905V19.9736C20.905 19.5842 21.2155 19.2711 21.6016 19.2711C21.9878 19.2711 22.2983 19.5842 22.2983 19.9736V31H27V16.7582C27 16.3809 26.8845 16.0236 26.6696 15.7266C26.3949 15.3452 25.9769 15.0923 25.519 15.0241C25.4195 15.008 25.3359 15 25.2523 15H2.75138C1.78796 15 1.00374 15.7908 1.00374 16.7622L1 16.7506Z" fill="#2557A7"/>
                                    <path d="M4 41H56V62H4V41Z" fill="#2557A7"/>
                                    <circle cx="16.5" cy="51.5" r="6.5" fill="#5DC1F1"/>
                                    <rect x="27" y="46" width="23" height="4" rx="2" fill="#5DC1F1"/>
                                    <rect x="27" y="51" width="23" height="5" rx="2.5" fill="#5DC1F1"/>
                                </svg>
                            </div>
                            <div>
                                <h6 class="mb-2">Unlock your Speaking Slot</h6>
                                <p class="mb-2">Deliver Keynotes ! Inspire People !</p>
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
                                    <button type="button" class="btn banner-v2-btn mt-0 mr-1 event_sponsor_right_header" id='sponsor_contactus' data-toggle="modal" data-target="#sponsorInfo">Become a Speaker !</button>

                                    <button type="button" class="btn banner-v2-btn mt-0 mr-1 get-started event_sponsor_right_header" data-toggle="modal" data-target="#sponsorInfo">More Info !</button>
                                    <!-- <button type="button" class="btn banner-v2-btn mt-0 mr-1" id='exhibitor_contactus'>Contact us !</button> -->
                                    <a class="btn banner-v2-btn mt-0 mr-1" id='speaker_contactus' data-toggle="modal" data-target="#contactusModal">Contact us !</a>
                                </div>
                            </div>
                        </div>
                        `);
                    }
                });

                if (!isLoggedIn) {
                    $(".get-started, #sponsor_contactus").hide();
                }

                getEventBaseInfo({eventtoken: eventToken}, false)
                    .then(({requestData, response}) => {
                        let event_output = response.output;
                        let conttoken_data = event_output.conttoken;

                        /* Event Sponsor popup*/
                        let eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
                        let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(
                            widget => widget.quantity > 0 ? 1 : 0
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
            'ptoken': '<?php echo $ptoken ?? ''; ?>',
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
        const spk_datefrom = speaker_data.spk_datefrom;
        const spk_dateto = speaker_data.spk_dateto;
        const spk_timezone = speaker_data?.spk_timezoneSelect;

        // let speaker_timestamp_start_data = {
        //     utc_datetime: spk_datefrom,
        //     local_datetime: spk_datefrom,
        //     timezone: spk_timezone,
        //     locality: 0
        // };
        //
        // let speaker_timestamp_end_data = {
        //     utc_datetime: spk_dateto,
        //     local_datetime: spk_dateto,
        //     timezone: spk_timezone,
        //     locality: 0
        // };
        //
        // let startdate = format_event_timestamp(speaker_timestamp_start_data, user_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);
        // let enddate = format_event_timestamp(speaker_timestamp_end_data, user_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);
        //
        // console.log('updateSpeakerTimeSlot:', spk_timezone, user_timezone, spk_datefrom, spk_dateto, startdate, enddate);

        $("#local_timezoneSelect_session").val(spk_timezone);
        $('#spk_timeslot_timezone_txt').text(`in ${spk_timezone}`);

        $("#spk_datefrom").val(spk_datefrom);
        $("#spk_dateto").val(spk_dateto);


        // // let helperText = '';
        // if (spk_timezone) {
        //     const formatForInput = dt => dt.replace(/:\d{2}$/, '');
        //
        //     const formattedStartDateTime = formatForInput(startdate.replace(' ', 'T'));
        //     const formattedEndDateTime   = formatForInput(enddate.replace(' ', 'T'));
        //     console.log('formattedStartDateTime', formattedStartDateTime, formattedEndDateTime, spk_timezone, user_timezone);
        //
        //     document.getElementById('spk_datefrom').min = formattedStartDateTime;
        //     document.getElementById('spk_datefrom').max = formattedEndDateTime;
        //     document.getElementById('spk_dateto').min = formattedStartDateTime;
        //     document.getElementById('spk_dateto').max = formattedEndDateTime;
        //
        //     $('#spk_timeslot_timezone_txt').text(`in ${user_timezone}`);
        //
        //     $("#spk_datefrom").val(speaker_data.spk_datefrom);
        //     $("#spk_dateto").val(speaker_data.spk_dateto);
        //
        //     // helperText = 'choose between ' + startdate + ' and ' + enddate;
        // }
        //
        // // $('#timeslotHelp').text(helperText);
    }
   
    $(document).on('click', '.edit_speaker', async function () {
        let spk_id = $(this).attr('data_id');
        loader(true, $("#speakerdet_loaderArea"));
        if (parseInt(spk_id)) {
            var eventHallAccess = [];
            var eventHallAccessKey = `event_hall_access_${eventToken}`;
            const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey); // await 
            if (data?.values) {
                eventHallAccess = data?.values.output;
            }
            var sponsor_type = $('#sponsor_type').val();
            var is_organizer = $("#is_organizer").val();
            var user_profile_type = $("#user_profile_type").val();
            var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
            getEventBaseInfo({ eventtoken: eventToken }, false)
                .then(({requestData, response}) => {
                let event_output = response.output;
                let event_owner = event_output.ptoken;
                let conttoken_data = event_output.conttoken;
                let event_form_version = conttoken_data.event_form_version ?? 1;
                var rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
                var ticketArr = conttoken_data.ticket_types.find(ticket => taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title)  || {};

                let event_instance_owner = conttoken_data.ptoken;
                let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
                    .split(',').concat(event_instance_owner)
                    .map(token => token.trim())
                    .filter(token => token);

                if(event_owner) event_organizer_ptokens.push(event_owner);


                const form = $('#spk_form');
                if (!form.length) return;

                // Clear all standard inputs, selects, and textareas in the form
                form[0].reset();
                form.find('input[type="hidden"][data-dynamic="1"]').val('');
                if (form.data('validator')) {
                    form.validate().resetForm();
                    form.find('.is-invalid, .is-valid, .error').removeClass('is-invalid is-valid error');
                }


                const tagsArr = conttoken_data?.event_tags ? conttoken_data.event_tags.split(",") : [];
                $(".tags-field").select2({
                    data: tagsArr,
                    width: '100%'
                });
                
                // let speaker_halls = conttoken_data.speaker_halls;
                // let allowed_speaker_halls = Array.isArray(speaker_halls)
                //     ? speaker_halls.filter(hall => (event_organizer_ptokens.includes(my_pToken)) || hall.status === '2')
                //     : [];
                    // hall.status === '1' &&
                const spk_allowed = new Set(["1", "3"]);
                const allowed_speaker_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                    .filter(h => Number(h?.id) > 0 && h?.name && spk_allowed.has(h.accesslevel));

                if (!allowed_speaker_halls.length) {
                    taoh_set_error_message('No Speaker Hall available for setup');
                    return;
                }

                let user_timezone;
                if (isLoggedIn) {
                    user_timezone = '<?= taoh_user_timezone(); ?>';
                }
                if (!isLoggedIn || !user_timezone?.trim()) {
                    let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
                    user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
                }
                if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

                $('#spk_hall').empty();
                $('#spk_hall').append('<option value="">Select Session Room</option>');
                allowed_speaker_halls.forEach(hall => {
                    let hall_id   = hall.id;
                    let hall_name = hall.name;
                    let hall_token = (hall.name); // btoa
                    var showhall = 0;

                    /* Start: check for count */
                if(event_form_version == 2){
                    if(is_organizer == 1){
                        showhall = 1;
                    }
                    // if(ticketArr && typeof ticketArr.max_sessions_allowed !== 'undefined' && ticketArr.max_sessions_allowed > 0 && (ticketArr?.speaker_halls == 'All' || ticketArr?.speaker_halls.includes(hall_name) || ticketArr?.speaker_halls.includes(hall_id))){
                    if ( ticketArr && typeof ticketArr.max_sessions_allowed !== 'undefined' && ticketArr.max_sessions_allowed > 0 && ( ticketArr.speaker_halls === 'All' ||   (  Array.isArray(ticketArr.speaker_halls) && ( ticketArr.speaker_halls.includes('All') || ticketArr.speaker_halls.includes(hall_name) || ticketArr.speaker_halls.includes(hall_id))) ||   (  Array.isArray(ticketArr.session_halls) && ( ticketArr.session_halls.includes('All') || ticketArr.session_halls.includes(hall_name) || ticketArr.session_halls.includes(hall_id))) )) {
                        showhall = 1;
                    }
                }else{
                    if(typeof eventHallAccess['speaker'] !== "undefined" && typeof eventHallAccess['speaker'][hall_name] !== "undefined"){
                    // console.log('speaker hall');
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
                    /* End: check for count */
                    
                        if(showhall == 1){
                            let hall_option = `<option value="${hall_token}">${hall_name}</option>`;
                            $('#spk_hall').append(hall_option);
                        }
                });

                var data = {
                    'taoh_action': 'get_speaker_detail',
                    'ops': 'detail',
                    'speaker_id': spk_id,
                    'ptoken': '<?php echo $ptoken ?? ''; ?>',
                    'eventtoken': eventtoken,
                    // 'speaker_name' : speaker_name
                };
                jQuery.post(_taoh_site_ajax_url, data, function (response) {
                    console.log('speaker_data', response);
                    if(response.success){
                        let speaker_data = response.output;

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
                            $('#spk_logo_image_preview').html(`<img src="${speaker_data.spk_logo_image}" class="img-fluid" alt="Speaker Logo" width="50" />`);
                            $("#spk_logo_image").val(speaker_data.spk_logo_image);
                        }
                        if(speaker_data.spk_image != ''){
                            $('#spk_image_preview').html(`<img src="${speaker_data.spk_image}" class="img-fluid" alt="Speaker Banner" width="100" />`);
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
                        // if (typeof timeZoneInstance !== 'undefined') {
                        //     timeZoneInstance.addOption({ name: speaker_data.spk_timezoneSelect });
                        //     timeZoneInstance.setValue(speaker_data.spk_timezoneSelect);
                        // }
                        if (speaker_data.enable_tao_networking == 1) {
                            $("#session_enable_tao_networking_yes").prop("checked", true);
                            $(".spk_external_video_room").hide();
                            $(".spk_streaming_link_wrapper").show();
                        } else {
                            $("#session_enable_tao_networking_no").prop("checked", true);
                            $(".spk_streaming_link_wrapper").hide();
                            $(".spk_external_video_room").show();
                        }

                        const tags = [].concat(speaker_data.spk_tags || []).filter(Boolean);
                        if (tags.length) {
                            $('#spk_tags').val(tags).trigger('change');
                        }
                        /*skills = speaker_data["skill:skill"];
                        // alert(skills);
                        var data = {
                            'taoh_action': 'taoh_get_skills',
                            'query': ''
                        };
                        let setData = {};
                        jQuery.post(_taoh_site_ajax_url, data, function(response) {
                            let skillsDetails = response;
                            if(skills != ''){
                                var skillsArr = $.trim(skills).split(',');
                                $.each(skillsArr, function(index, value) {
                                    // console.log(skillsDetails);
                                    skillval = skillsDetails.find(item => item.id === $.trim(value));
                                    if(skillval){
                                        $('#skillSelect').append('<option value="'+value+'" selected>'+skillval.label+'</option>');
                                    }
                                });
                            }
                             skillSelect();
                       
                        });
                         */

                        $("#spk_desc").val(taoh_desc_decode(speaker_data.spk_desc));

                        const $old_repeatable_speaker = $("#speaker_blk #repeatable_speaker");
                        const $new_repeatable_speaker = $('<div id="repeatable_speaker"></div>');
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
                            $('#spk_profileimg_preview_'+i).html(`<img src="${speaker_data.spk_profileimg[i]}" width="50" class="img-fluid" alt="profile image" />`);
                        });
                    }
                    loader(false, $("#addspeaker_loaderArea"));
                }).fail(function () {
                    loader(false, $("#addspeaker_loaderArea"));
                    console.log("Network issue on response!");
                });

                const speakerslotmodal_elem = $('#speakerSlotModal');

                if($("#is_organizer").val() == 1){
                    $("#spk_video_room_block").show();
                    $("#spk_video_room-yes").prop("disabled",true);
                }else{
                    $("#spk_video_room-no").prop("checked",false);
                }

                $('#speakerSlotModal').modal('show');
            }).catch(error => console.error("Error fetching event info:", error));
        }
    });

    $(document).on('click', '.delete-speaker', async function () {
        let meta_id = $(this).data('id');
        let meta_type = $(this).data('type');

        if (meta_id) {
            taoh_set_warning_message('Do you want to delete this speaker?', false, 'toast-middle', [
                {
                    text: 'Yes',
                    action: () => {
                        jQuery.post(_taoh_site_ajax_url, {
                            'taoh_action': 'delete_event_meta',
                            'token': _taoh_ajax_token,
                            'eventtoken': eventToken,
                            'meta_id': meta_id,
                            'meta_type': meta_type
                        }, function (response) {
                            if(response.success){
                                taoh_set_success_message('Speaker slot deleted successfully', false);
                                let event_meta_key = `event_MetaInfo_${eventToken}`;
                                IntaoDB.removeItem(objStores.event_store.name,  event_meta_key);
                                location.reload();
                            }
                        });
                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3'
                },
                {
                    text: 'No',
                    action: () => {

                    },
                    class: 'dojo-v1-btn float-right mt-3 mb-3 mr-2'
                }
            ]);
        }
    });
</script>
