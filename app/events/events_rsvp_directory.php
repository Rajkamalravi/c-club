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

<style>
    .erd_follow_btn {
        background-color: transparent;
    }

    .erd_follow_btn[data-follow_status="1"] {
        background-color: #2557A7 !important;
        border: 1px solid #2557A7 !important;
        color: #ffffff !important;
    }
    .n-participants-name {
        font-size: 22px;
        font-weight: 600;
        line-height: 22px;
    }
    .events-hall .n-participants-text span.badge, .events-hall .n-participants-text span.badge.text-success {
        color: #b4b4b4 !important;
        font-size: 14px !important;
        font-weight: 500;
    }
    .events-hall .n-participants-text {
        color: #b4b4b4;
    }
    .n-participants-text {
        font-size: 14px;
        font-weight: 500;
    }
    .events-hall .attendees_card .bor-btn {
        line-height: 17px;
        font-size: 17px;
        border: 2px solid #545454;
        min-height: 30px;
        padding: 5px;
        color: #545454;
        border-radius: 10px;
    }
    .tables_div.new-exh-list .btn.bor-btn.metrics_action {
        min-height: auto;
        padding: 5px;
        background: #3b62ab;
        color: #fff !important;
        font-size: 15px;
    }
</style>

<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";

    var eventlivelink = "<?php echo isset($eventlivelink) ? $eventlivelink : ''; ?>";

    let erd_my_following_ptoken_list = JSON.parse(`<?= json_encode(($erd_my_following_ptoken_list ?? [])); ?>`);

    async function getEventRSVPedUsers(eventtoken, search = '', event_organizer_ptokens) {
        const eventStatus = $('#event_status_hidden').val();
        var rsvp_status_hidden = $('#rsvp_status_hidden').val();
        var superorganizer_token = $('#superorganizer_token').val();

        let allowView = false;
        let attendeesMsg = '';

        if (superorganizer_token === my_ptoken) {
            allowView = true;
        } else if (isLoggedIn) {
            if (rsvp_status_hidden == 1 && eventStatus == 1) {
                allowView = true;
            } else {
                attendeesMsg = rsvp_status_hidden != 1
                    ? "Register to view the attendees."
                    : "You can view the attendees when the event is live.";
            }
        } else {
            attendeesMsg = "Login and register to view the attendees.";
        }

        if (allowView) {
            var eventBaseInfoKey = `event_rsvp_users_${eventtoken}`;
            if (search != '' && search != undefined) {
                var eventBaseInfoKey = `event_rsvp_users_${eventtoken}_${search}`;
            }
            //alert(eventBaseInfoKey);
            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
                //console.log('---eventSponsoreventSponsor-------',data)
                if (data?.values) {
                    processEventRSVPedUsers(eventtoken, data.values);
                } else {
                    updateEventRSVPedUsers(eventtoken, true, search);
                }
            });
        } else {
            $('.rsvp_actions').css('display', 'none');

            /* $('#rsvp_users_list').html(`<div class="event-registration-banner d-flex flex-column align-items-center justify-content-center">
                <h4 class="my-4 text-center">${attendeesMsg}</h4>
                <a href="#" style="display:none;" class="btn register-now-btn">Register Now</a></div>`); */
            $("#rsvp_default_list").show();
            if ($("#is_organizer").val() == 1) {
                $('.rsvp_actions').show();
            }
            if (is_user_rsvp_done) {
                $("#register_now").hide();
            }
            loader(false, $("#rsvpdir_loaderArea"));
        }

    }

    async function updateEventRSVPedUsers(eventtoken, serverFetch = true, search = '') {

        var eventBaseInfoKey = `event_rsvp_users_${eventtoken}`;
        if (search != '' && search != undefined) {
            var eventBaseInfoKey = `event_rsvp_users_${eventtoken}_${search}`;
        }


        const handleResponse = (response) => {
            if (response.success) {

                IntaoDB.setItem(objStores.event_store.name, {
                    taoh_data: eventBaseInfoKey,
                    values: response,
                    timestamp: Date.now()
                });

                processEventRSVPedUsers(eventtoken, response);

            } else {
                loader(false, $("#rsvpdir_loaderArea"));
                taoh_set_error_message('Failed to fetch event details! Try Again');
                console.log('Failed to fetch event details! Try Again');
            }
        };

        if (serverFetch) {
            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',  // changed to post to avoid caching issues in flexible domain
                data: {
                    action: 'get_event_rsvped_users',
                    taoh_action: 'get_event_rsvped_users',
                    token: _taoh_ajax_token,
                    search: search,
                    eventtoken: eventtoken
                },
                dataType: 'json',
                success: handleResponse,
                error: (xhr) => console.error('Error:', xhr.status)
            });
        } else {
            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
                if (data?.values) {
                    processEventRSVPedUsers(eventtoken, data.values, search);
                } else {
                    updateEventRSVPedUsers(eventtoken, true);
                }
            });
        }
    }

    function processEventRSVPedUsers(eventtoken, response, search = '') {
        const eventStatus = $('#event_status_hidden').val();
        var rsvp_status_hidden = $('#rsvp_status_hidden').val();
        var superorganizer_token = $('#superorganizer_token').val();

        let allowView = false;
        let attendeesMsg = '';

        if (superorganizer_token === my_ptoken) {
            allowView = true;
        } else if (isLoggedIn) {
            if (rsvp_status_hidden == 1 && eventStatus == 1) {
                allowView = true;
            } else {
                attendeesMsg = rsvp_status_hidden != 1
                    ? "Register to view the attendees."
                    : "You can view the attendees when the event is live.";
            }
        } else {
            attendeesMsg = "Login and register to view the attendees.";
        }

        if (allowView) {
            constructRSVPList(eventtoken, response, search);
        } else {
            $('.rsvp_actions').css('display', 'none');

            /* $('#rsvp_users_list').html(`<div class="event-registration-banner d-flex flex-column align-items-center justify-content-center">
                <h4 class="my-4 text-center">${attendeesMsg}</h4>
                <a href="#" style="display:none;" class="btn register-now-btn">Register Now</a></div>`); */
            $("#rsvp_default_list").show();
            if ($("#is_organizer").val() == 1) {
                $('.rsvp_actions').show();
            }
            if (is_user_rsvp_done) {
                $("#register_now").hide();
            }
            loader(false, $("#rsvpdir_loaderArea"));
        }
    }


    $('#event_email_form').validate({
        rules: {
            email_title: {
                required: true,
            },
            email_description: {
                required: true,
            }
        },
        messages: {
            email_title: {
                required: "Email title is required",
            },
            email_description: {
                required: "Email description is required",
            }
        },
        submitHandler: function (form) {
            let event_email_form = $('#event_email_form');
            let formData = new FormData(form);
            formData.append('taoh_action', 'event_email_send');
            console.log('-----formData------', formData);

            if (formData.get('ptoken') && formData.get('event_token') && (
                (formData.get('email_type') === 'slug' && formData.get('ticket_type_slug')) || formData.get('email_type') === 'all')) {
                $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {

                        if (response.success && response.output) {
                            $('#emailModal').modal('hide');
                            taoh_set_success_message('Email Sending process started successfully.');

                        } else {
                            taoh_set_error_message('Failed to send email! Try Again');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', xhr.status);
                    }
                });
            } else {
                taoh_set_error_message('Invalid data to send email. Try Again!', false);
            }
        }
    });

    setTimeout(() => {
        getTicketTypes();
    }, 5000);


    function getTicketTypes() {
        const eventdetailBaseInfoKey = `event_detail_<?php echo $eventtoken ?? ''; ?>`;
        IntaoDB.getItem(objStores.event_store.name, eventdetailBaseInfoKey).then((data) => {
            if (data?.values) {
                getTicketData(data.values);
            } else {
                //alert('No event details data found------->'+eventdetailBaseInfoKey);
                //updateEventBaseInfo({eventtoken: eve_tok},true);
            }
        });
    }

    function getTicketData(response) {
        let event_output = response.output;
        let conttoken_data = event_output.conttoken;

        const ticket_types = conttoken_data.ticket_types;
        var email_con = '';

        var eve = event_output.eventtoken;
        if (!ticket_types || Object.keys(ticket_types).length === 0) {
            $('#email_rsvp').html(email_con);
        } else {
            email_con += `<button type="button" class="btn btn-primary email_slug_btn m-1"
                        data-eventtoken="${eve}" data-slug="all" > Email - All </button><br>`;
            ticket_types.forEach(function (ticket) {
                if (ticket.title != undefined) {
                    email_con += `<button type="button" class="btn btn-primary email_slug_btn m-1"
                        data-eventtoken="${eve}" data-slug="${ticket.slug}" > Email - ${ticket.title} </button><br>`;
                } else if (ticket.name != undefined) {
                    email_con += `<button type="button" class="btn btn-primary email_slug_btn m-1"
                        data-eventtoken="${eve}" data-slug="${ticket.slug}" > Email - ${ticket.name} </button><br>`;
                }


            });

            /*$.each(ticket_types, function (i,ticket) {

                email_con +=  `<button type="button" class="btn btn-primary email_slug_btn"
                data-eventtoken="${event_tok}" data-slug="${ticket.slug}"> Email - ${ticket.title} </button>`;
            });*/


            $('#email_rsvp').html(email_con);
        }
    }

    async function constructRSVPList(eventtoken, response, search = '', redis = 0, listAppend = 0) {
        $('#load_more_btn').hide();
        if (listAppend == 0) {
            loader(true, $("#rsvpdir_loaderArea"));
        }

        var res = response.output;
        var listed_count = res.length;
        var total_count = response.total_count;

        if (search != '') {
            if (response.total_count == 0) {
                $('#rsvp_users_list').html('<div class="text-center nores">No result found</div>');
            }
            if (listAppend == 0) {
                $('#rsvp_users_list').html('');
            }

        }

        // $('#attendees_total_count').text("Total Attendee(s): "+total_count);
        //$('#rsvp_users_list').html('');
        var content = '';

        for (const user_data of res) {

            var v = user_data;

            var country_locked = $('#event_country_lock').val();
            var event_country_name = $('#event_country_name').val();

            // if( v.ptoken != undefined && v.ptoken != my_ptoken){
            if (typeof v !== 'undefined' && v && v.ptoken !== undefined
                && v.ptoken !== 'undefined' && v.ptoken !== my_ptoken
                && v.fname != '<?php echo TAOH_SITE_NAME_SLUG;?>'

            ) {
                console.log('---v.fnam-' + v.fname + '--country_locked-----' + country_locked)
                const userInfo = await ft_getUserInfo(v.ptoken, 'public');

                if (country_locked != 1) {

                    if (v.full_location != '' && v.full_location != undefined && v.full_location != null) {
                        var user_country_array = v.full_location.split(',');
                        var user_country_name = user_country_array[user_country_array.length - 1].trim();
                    } else {
                        var user_country_array = '';
                        var user_country_name = '';
                    }


                    if (user_country_name != event_country_name) {
                        //return;
                    }

                }
                const fallbackSrc = `${_taoh_ops_prefix}/avatar/PNG/128/${v?.avatar?.trim() || 'default'}.png`;
                const avatarSrc = await buildAvatarImage(v.avatar_image, fallbackSrc);
                var companies = '';
                var skillContent = '';
                var roles = '';

                if (v.company != undefined) companies = formatObject(v.company);
                if (v.title != undefined) roles = formatObject(v.title);

                if (v.skill != undefined) skillContent = buildSkillContent(v.skill, v.ptoken);


                var token = v.ptoken;

                var ticket_badge = '';
                var badge = '';

                if (v.ticket_details != '' && v.ticket_details != undefined && v.ticket_details != null) {
                    ticket_badge = v.ticket_details.title;
                } else {
                    ticket_badge = '';
                }

                const profileTitle = v?.profile_type === 'provider' ? 'Service Provider' : v?.profile_type;

                let isFollowing = false;
                if (Array.isArray(erd_my_following_ptoken_list) && erd_my_following_ptoken_list.includes(userInfo.ptoken)) {
                    isFollowing = true;
                }
                $(".nores").hide();
                if (!$(`#attendees_card${v.ptoken}`).length || listAppend == 0) {
                    content += `
                    <div id="attendees_card${v.ptoken}" class="attendees_card relative-card card card-item d-flex flex-column flex-xl-row justify-content-xl-between py-xl-3 p-3 mb-4 mx-3 light-dark-card svg-fill-light-dark" style="gap: 12px;">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center" style="gap: 12px; flex: 1;">
                                        <div class="left-box d-flex flex-md-column align-items-center pt-md-3 pb-2" style="gap: 6px;">
                                            
                                            <span data-profile_token="${v.ptoken}" class="cursor-pointer openProfileModal">
                                                <img width="40" class="lazy n-participants-img" src="${avatarSrc}" alt="avatar">
                                            </span>
                                            <div class="d-flex flex-column align-items-md-center">
                                                
                                               
                                            
                                                
                                                <div class="icons" style="display:none">
                                                    <a href="#">
                                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.23054 15C9.1482 15 10.9873 14.2098 12.3433 12.8033C13.6993 11.3968 14.4611 9.48912 14.4611 7.5C14.4611 5.51088 13.6993 3.60322 12.3433 2.1967C10.9873 0.790176 9.1482 0 7.23054 0C5.31288 0 3.47377 0.790176 2.11778 2.1967C0.761787 3.60322 0 5.51088 0 7.5C0 9.48912 0.761787 11.3968 2.11778 12.8033C3.47377 14.2098 5.31288 15 7.23054 15ZM6.10077 9.84375H6.77863V7.96875H6.10077C5.72512 7.96875 5.4229 7.65527 5.4229 7.26562C5.4229 6.87598 5.72512 6.5625 6.10077 6.5625H7.45649C7.83214 6.5625 8.13436 6.87598 8.13436 7.26562V9.84375H8.36031C8.73596 9.84375 9.03817 10.1572 9.03817 10.5469C9.03817 10.9365 8.73596 11.25 8.36031 11.25H6.10077C5.72512 11.25 5.4229 10.9365 5.4229 10.5469C5.4229 10.1572 5.72512 9.84375 6.10077 9.84375ZM7.23054 3.75C7.47025 3.75 7.70014 3.84877 7.86963 4.02459C8.03913 4.2004 8.13436 4.43886 8.13436 4.6875C8.13436 4.93614 8.03913 5.1746 7.86963 5.35041C7.70014 5.52623 7.47025 5.625 7.23054 5.625C6.99083 5.625 6.76094 5.52623 6.59144 5.35041C6.42195 5.1746 6.32672 4.93614 6.32672 4.6875C6.32672 4.43886 6.42195 4.2004 6.59144 4.02459C6.76094 3.84877 6.99083 3.75 7.23054 3.75Z" fill="#686767"/>
                                                        </svg>
                                                    </a>
                                                    <a href="#">
                                                        <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2.7 3.63043C2.7 2.66758 3.07928 1.74417 3.75442 1.06333C4.42955 0.382491 5.34522 0 6.3 0C7.25478 0 8.17045 0.382491 8.84558 1.06333C9.52072 1.74417 9.9 2.66758 9.9 3.63043C9.9 4.59329 9.52072 5.5167 8.84558 6.19754C8.17045 6.87838 7.25478 7.26087 6.3 7.26087C5.34522 7.26087 4.42955 6.87838 3.75442 6.19754C3.07928 5.5167 2.7 4.59329 2.7 3.63043ZM0 13.6794C0 10.8856 2.24437 8.62228 5.01469 8.62228H7.58531C10.3556 8.62228 12.6 10.8856 12.6 13.6794C12.6 14.1445 12.2259 14.5217 11.7647 14.5217H0.835312C0.374063 14.5217 0 14.1445 0 13.6794ZM14.175 8.84918V7.03397H12.375C12.0009 7.03397 11.7 6.73049 11.7 6.35326C11.7 5.97604 12.0009 5.67255 12.375 5.67255H14.175V3.85734C14.175 3.48011 14.4759 3.17663 14.85 3.17663C15.2241 3.17663 15.525 3.48011 15.525 3.85734V5.67255H17.325C17.6991 5.67255 18 5.97604 18 6.35326C18 6.73049 17.6991 7.03397 17.325 7.03397H15.525V8.84918C15.525 9.22641 15.2241 9.52989 14.85 9.52989C14.4759 9.52989 14.175 9.22641 14.175 8.84918Z" fill="#686767"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="center-box flex-grow-1 d-flex align-items-center flex-wrap flex-md-nowrap" style="gap: 6px;">
                                            <div class="flex-grow-1">
                                                <p class="n-participants-name mb-2 cursor-pointer openProfileModal" data-profile_token="${v.ptoken}">
                                                    ${v.chat_name}  
                                                    <span class="emp-badge px-2 py-1" style="text-transform: capitalize;">
                                                    ${profileTitle}
                                                    </span>
                                                </p> 
                                                <div class="d-flex algn-items-center flex-wrap" style="gap: 6px;">
                                                    <div class="n-participants-text d-flex align-items-center mb-2" style="gap: 3px;">
                                                        <svg width="21" height="14" viewBox="0 0 19 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2.11111 0C0.946701 0 0 0.946701 0 2.11111V4.22222C0 4.5125 0.244097 4.7401 0.517882 4.83576C1.13802 5.05017 1.58333 5.64062 1.58333 6.33333C1.58333 7.02604 1.13802 7.61649 0.517882 7.8309C0.244097 7.92656 0 8.15417 0 8.44444V10.5556C0 11.72 0.946701 12.6667 2.11111 12.6667H16.8889C18.0533 12.6667 19 11.72 19 10.5556V8.44444C19 8.15417 18.7559 7.92656 18.4821 7.8309C17.862 7.61649 17.4167 7.02604 17.4167 6.33333C17.4167 5.64062 17.862 5.05017 18.4821 4.83576C18.7559 4.7401 19 4.5125 19 4.22222V2.11111C19 0.946701 18.0533 0 16.8889 0H2.11111ZM4.22222 3.69444V8.97222C4.22222 9.2625 4.45972 9.5 4.75 9.5H14.25C14.5403 9.5 14.7778 9.2625 14.7778 8.97222V3.69444C14.7778 3.40417 14.5403 3.16667 14.25 3.16667H4.75C4.45972 3.16667 4.22222 3.40417 4.22222 3.69444ZM3.16667 3.16667C3.16667 2.58281 3.63837 2.11111 4.22222 2.11111H14.7778C15.3616 2.11111 15.8333 2.58281 15.8333 3.16667V9.5C15.8333 10.0839 15.3616 10.5556 14.7778 10.5556H4.22222C3.63837 10.5556 3.16667 10.0839 3.16667 9.5V3.16667Z" fill="#b4b4b4"></path>
                                                        </svg>
                                                        ${ticket_badge} 
                                                    </div>
                                                    <div class="n-participants-text d-flex align-items-center mb-2" style="gap: 3px;">
                                                        <svg width="20" height="17" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2.205 2.94C2.205 2.16026 2.51475 1.41246 3.06611 0.861106C3.61746 0.309749 4.36526 0 5.145 0C5.92474 0 6.67254 0.309749 7.22389 0.861106C7.77525 1.41246 8.085 2.16026 8.085 2.94C8.085 3.71974 7.77525 4.46754 7.22389 5.01889C6.67254 5.57025 5.92474 5.88 5.145 5.88C4.36526 5.88 3.61746 5.57025 3.06611 5.01889C2.51475 4.46754 2.205 3.71974 2.205 2.94ZM0 11.0778C0 8.81541 1.83291 6.9825 4.09533 6.9825H6.19467C8.45709 6.9825 10.29 8.81541 10.29 11.0778C10.29 11.4545 9.98452 11.76 9.60783 11.76H0.682172C0.305484 11.76 0 11.4545 0 11.0778ZM11.5763 7.16625V5.69625H10.1062C9.80077 5.69625 9.555 5.45048 9.555 5.145C9.555 4.83952 9.80077 4.59375 10.1062 4.59375H11.5763V3.12375C11.5763 2.81827 11.822 2.5725 12.1275 2.5725C12.433 2.5725 12.6788 2.81827 12.6788 3.12375V4.59375H14.1488C14.4542 4.59375 14.7 4.83952 14.7 5.145C14.7 5.45048 14.4542 5.69625 14.1488 5.69625H12.6788V7.16625C12.6788 7.47173 12.433 7.7175 12.1275 7.7175C11.822 7.7175 11.5763 7.47173 11.5763 7.16625Z" fill="#b4b4b4"/>
                                                        </svg>

                                                        <span>
                                                            <span class="mr-2 followers-count-view" data-ptoken="${userInfo.ptoken}" data-fscount="${safeParseInt(userInfo.tao_followers_count, 0)}">${safeParseInt(userInfo.tao_followers_count, 0)} Followers</span>
                                                            <span class="mr-2 following-count-view" data-ptoken="${userInfo.ptoken}" data-fgcount="${safeParseInt(userInfo.tao_following_count, 0)}">${safeParseInt(userInfo.tao_following_count, 0)} Following</span>
                                                        </span>
                                                    </div>
                                                </div>

                                               
                                                <div class=" d-flex align-items-center flex-wrap mb-2" style="gap: 6px;">
                                                ${(companies && companies.length) ?
                        `<div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                        <svg width="15" height="18" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 0C0.447917 0 0 0.461914 0 1.03125V9.96875C0 10.5381 0.447917 11 1 11H3V9.28125C3 8.71191 3.44792 8.25 4 8.25C4.55208 8.25 5 8.71191 5 9.28125V11H7C7.55208 11 8 10.5381 8 9.96875V1.03125C8 0.461914 7.55208 0 7 0H1ZM1.33333 5.15625C1.33333 4.96719 1.48333 4.8125 1.66667 4.8125H2.33333C2.51667 4.8125 2.66667 4.96719 2.66667 5.15625V5.84375C2.66667 6.03281 2.51667 6.1875 2.33333 6.1875H1.66667C1.48333 6.1875 1.33333 6.03281 1.33333 5.84375V5.15625ZM3.66667 4.8125H4.33333C4.51667 4.8125 4.66667 4.96719 4.66667 5.15625V5.84375C4.66667 6.03281 4.51667 6.1875 4.33333 6.1875H3.66667C3.48333 6.1875 3.33333 6.03281 3.33333 5.84375V5.15625C3.33333 4.96719 3.48333 4.8125 3.66667 4.8125ZM5.33333 5.15625C5.33333 4.96719 5.48333 4.8125 5.66667 4.8125H6.33333C6.51667 4.8125 6.66667 4.96719 6.66667 5.15625V5.84375C6.66667 6.03281 6.51667 6.1875 6.33333 6.1875H5.66667C5.48333 6.1875 5.33333 6.03281 5.33333 5.84375V5.15625ZM1.66667 2.0625H2.33333C2.51667 2.0625 2.66667 2.21719 2.66667 2.40625V3.09375C2.66667 3.28281 2.51667 3.4375 2.33333 3.4375H1.66667C1.48333 3.4375 1.33333 3.28281 1.33333 3.09375V2.40625C1.33333 2.21719 1.48333 2.0625 1.66667 2.0625ZM3.33333 2.40625C3.33333 2.21719 3.48333 2.0625 3.66667 2.0625H4.33333C4.51667 2.0625 4.66667 2.21719 4.66667 2.40625V3.09375C4.66667 3.28281 4.51667 3.4375 4.33333 3.4375H3.66667C3.48333 3.4375 3.33333 3.28281 3.33333 3.09375V2.40625ZM5.66667 2.0625H6.33333C6.51667 2.0625 6.66667 2.21719 6.66667 2.40625V3.09375C6.66667 3.28281 6.51667 3.4375 6.33333 3.4375H5.66667C5.48333 3.4375 5.33333 3.28281 5.33333 3.09375V2.40625C5.33333 2.21719 5.48333 2.0625 5.66667 2.0625Z" fill="#b4b4b4"/>
                                                        </svg>
                                                        <span>${(companies && companies.length) ? generateCompanyHTML(companies) : ''}</span>
                                                    </div>` : ''}
                                                    ${(roles && roles.length) ?
                        `<div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                        <i class="fa-solid fa-briefcase"></i> 
                                                        <span>${(roles && roles.length) ? generateRoleHTML(roles) : ''}</span>
                                                    </div>` : ''}
                                                     ${v.full_location != '' ?
                        `<div class="n-participants-text d-flex align-items-center" style="gap: 6px;">
                                                    <svg width="15" height="18" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.61719 12.7079C6.95312 11.0736 10 7.11255 10 4.88765C10 2.18926 7.76042 0 5 0C2.23958 0 0 2.18926 0 4.88765C0 7.11255 3.04688 11.0736 4.38281 12.7079C4.70312 13.0974 5.29688 13.0974 5.61719 12.7079ZM5 3.25843C5.44203 3.25843 5.86595 3.43008 6.17851 3.73562C6.49107 4.04116 6.66667 4.45555 6.66667 4.88765C6.66667 5.31974 6.49107 5.73414 6.17851 6.03968C5.86595 6.34522 5.44203 6.51686 5 6.51686C4.55797 6.51686 4.13405 6.34522 3.82149 6.03968C3.50893 5.73414 3.33333 5.31974 3.33333 4.88765C3.33333 4.45555 3.50893 4.04116 3.82149 3.73562C4.13405 3.43008 4.55797 3.25843 5 3.25843Z" fill="#b4b4b4"/>
                                                    </svg>
                                                    <span>${v.full_location}</span>
                                                </div>` : ''}
                                                </div>
                                                ${skillContent != '' ?
                        `<div class=" align-items-center flex-wrap skill-con d-none" style="gap: 3px;">
                                                    <div class="n-participants-text  d-flex align-items-center mr-2" style="gap: 6px;">
                                                        <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M6.77617 0.333008C6.65371 0.126758 6.42812 0 6.1875 0C5.94688 0 5.72129 0.126758 5.59883 0.333008L3.53633 3.77051C3.40957 3.9832 3.40527 4.24746 3.52773 4.4623C3.6502 4.67715 3.87793 4.81035 4.125 4.81035H8.25C8.49707 4.81035 8.72695 4.67715 8.84727 4.4623C8.96758 4.24746 8.96543 3.9832 8.83867 3.77051L6.77617 0.333008ZM6.1875 6.70312V9.79688C6.1875 10.2717 6.57207 10.6562 7.04688 10.6562H10.1406C10.6154 10.6562 11 10.2717 11 9.79688V6.70312C11 6.22832 10.6154 5.84375 10.1406 5.84375H7.04688C6.57207 5.84375 6.1875 6.22832 6.1875 6.70312ZM2.75 11C3.47935 11 4.17882 10.7103 4.69454 10.1945C5.21027 9.67882 5.5 8.97935 5.5 8.25C5.5 7.52065 5.21027 6.82118 4.69454 6.30546C4.17882 5.78973 3.47935 5.5 2.75 5.5C2.02065 5.5 1.32118 5.78973 0.805456 6.30546C0.289731 6.82118 0 7.52065 0 8.25C0 8.97935 0.289731 9.67882 0.805456 10.1945C1.32118 10.7103 2.02065 11 2.75 11Z" fill="#636161"/>
                                                        </svg>
                                                        <span>Skills</span>
                                                    </div>
                                                    
                                                    ${skillContent}
                                                    
                                                </div>` : ''}
                                                <div style="display:none;gap: 6px;" class="n-participants-text align-items-center" >
                                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M10 20C12.6522 20 15.1957 18.9464 17.0711 17.0711C18.9464 15.1957 20 12.6522 20 10C20 7.34784 18.9464 4.8043 17.0711 2.92893C15.1957 1.05357 12.6522 0 10 0C7.34784 0 4.8043 1.05357 2.92893 2.92893C1.05357 4.8043 0 7.34784 0 10C0 12.6522 1.05357 15.1957 2.92893 17.0711C4.8043 18.9464 7.34784 20 10 20ZM6.41016 12.7148C7.10938 13.5234 8.30469 14.375 10 14.375C11.6953 14.375 12.8906 13.5234 13.5898 12.7148C13.8164 12.4531 14.2109 12.4258 14.4727 12.6523C14.7344 12.8789 14.7617 13.2734 14.5352 13.5352C13.6641 14.5352 12.1523 15.625 10 15.625C7.84766 15.625 6.33594 14.5352 5.46484 13.5352C5.23828 13.2734 5.26562 12.8789 5.52734 12.6523C5.78906 12.4258 6.18359 12.4531 6.41016 12.7148ZM5.64062 8.125C5.64062 7.79348 5.77232 7.47554 6.00674 7.24112C6.24116 7.0067 6.5591 6.875 6.89062 6.875C7.22215 6.875 7.54009 7.0067 7.77451 7.24112C8.00893 7.47554 8.14062 7.79348 8.14062 8.125C8.14062 8.45652 8.00893 8.77446 7.77451 9.00888C7.54009 9.2433 7.22215 9.375 6.89062 9.375C6.5591 9.375 6.24116 9.2433 6.00674 9.00888C5.77232 8.77446 5.64062 8.45652 5.64062 8.125ZM13.1406 6.875C13.4721 6.875 13.7901 7.0067 14.0245 7.24112C14.2589 7.47554 14.3906 7.79348 14.3906 8.125C14.3906 8.45652 14.2589 8.77446 14.0245 9.00888C13.7901 9.2433 13.4721 9.375 13.1406 9.375C12.8091 9.375 12.4912 9.2433 12.2567 9.00888C12.0223 8.77446 11.8906 8.45652 11.8906 8.125C11.8906 7.79348 12.0223 7.47554 12.2567 7.24112C12.4912 7.0067 12.8091 6.875 13.1406 6.875Z" fill="url(#paint0_linear_5596_697)"/>
                                                        <defs>
                                                        <linearGradient id="paint0_linear_5596_697" x1="10" y1="0" x2="10" y2="20" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#FFD700"/>
                                                        <stop offset="1" stop-color="#F85556"/>
                                                        </linearGradient>
                                                        </defs>
                                                    </svg>
                                                    <span>Available to connect</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center" style="gap: 8px;" >

                                            `;
                    if ($("#event_live_state").val() == 'live' && $("#chat_room_status").val() == 1) {
                        content += `<a target="_blank" href="${eventlivelink + '?chatwith=' + v.ptoken}" class="bor-btn"> Chat </a>`;
                    } else {
                        let eventStartdate = formatTimestamp($("#event_start_at").val());
                        if (isBeforeThreeDays(eventStartdate)) {
                            // content += `<button type="button" class="btn std-btn mr-2 profile_send_message_btn" data-toptoken="${v.ptoken}" data-respondptoken="${userInfo.ptoken}">Message</button>`;
                            content += `<a target="_blank" href="${_taoh_site_url_root}/profile/${v.ptoken}?from=messaging" class="btn std-btn mr-2">Message</a>`;
                        }
                    }
                    content += `
                                                <button type="button" class="bor-btn erd_follow_btn profile_follow_btn" data-ptoken="${userInfo.ptoken}" data-follow_status="${isFollowing ? 1 : 0}"  data-page="directory" title="${isFollowing ? 'Following' : 'Click to Follow'}">
                                                    <i class="fas fa-user-plus fa-sm follow-user-plus-icon" aria-hidden="true"></i>
                                                </button>
                                                <a href="#" style="padding-top: 5px; margin-left: 10px; margin-right: 10px; color: #040404;"><i class="fa fa-chevron-right" style="font-size: 25px;"></i></a>
                                            </div>
                                        </div>
                                    </div>`;

                    content += `${badge}`;
                    content += `</div>`;
                }
            }

        }

        if (content == '') {
            var cccontent = '';
            if (res.length > 1 || search != '') {
                $('.rsvp_actions').css('display', 'flex');
                $('.rsvp_actions.rsvp_search').css('display', 'flex');
            } else {
                $('.rsvp_actions').css('display', 'none');
                $('.rsvp_actions.rsvp_search').css('display', 'none');
            }

            cccontent += `
                <div class="zeroday-attendees py-5">
                    <svg style="width: 100%; max-width: 896px;" viewBox="0 0 896 202" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M638.022 187.747L468.019 187.729C469.017 97.3485 544.706 18.7944 637.863 19.7826C731.02 20.7708 806.168 97.3828 805.169 187.763L638.022 187.747Z" fill="#4CB7FF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M638.001 189.424L549.428 189.424C549.428 141.505 588.406 97.7374 637.384 97.759C686.287 97.7805 728.003 141.505 728.003 189.425L638.001 189.424Z" fill="#AFE7EF" fill-opacity="0.61"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M229.331 183.495L90 183.495C90.8265 108.681 152.866 43.6504 229.216 44.4603C305.565 45.2702 367.147 108.68 366.32 183.494L229.331 183.495Z" fill="#877CFF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M229.33 183.49L156.738 183.49C156.738 144.217 188.684 108.346 228.825 108.364C268.904 108.381 303.094 144.217 303.094 183.491L229.33 183.49Z" fill="#BBBBFF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M424.175 187.736L235.996 189.674C237.123 87.6699 322.335 1.89415 426.216 2.9961C530.098 4.09806 613.087 87.6602 611.96 189.665L424.175 187.736Z" fill="#CA8787"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M425.577 187.48L326.806 189.072C326.217 135.751 369.943 89.7647 424.556 89.1854C479.086 88.607 523.758 135.747 524.347 189.068L425.577 187.48Z" fill="#FFD0D0"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M146.94 188.268L59 188.398C59.5266 140.729 99.2461 101.418 147.894 101.934C196.542 102.45 235.573 141.533 235.046 189.202L146.94 188.268Z" fill="#FF9750"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M146.937 188.565L100.964 188.396C100.687 163.373 121.101 142.564 146.676 142.293C172.212 142.022 193.496 163.543 193.772 188.565L146.937 188.565Z" fill="#FFD2B3"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.6054 176.589L5.61044 192.213C-3.0062 181.736 -1.49697 166.24 8.99519 157.61C19.4713 148.994 34.9677 150.503 43.5844 160.979L24.6054 176.589Z" fill="#877CFF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M24.5411 176.513L5.54611 192.136C14.1759 202.628 29.6724 204.137 40.1645 195.508C50.6406 186.891 52.1499 171.395 43.52 160.902L24.5411 176.513Z" fill="#BBBBFF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M871.349 174.416L856.612 154.726C867.472 146.598 882.883 148.815 891.023 159.691C899.151 170.551 896.934 185.962 886.074 194.09L871.349 174.416Z" fill="#4CB7FF"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M871.428 174.355L856.69 154.665C845.814 162.805 843.597 178.216 851.737 189.093C859.865 199.952 875.276 202.17 886.152 194.029L871.428 174.355Z" fill="#89D4F5"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M547.348 18.3648L531.753 10.5821C536.045 1.98075 546.51 -1.51656 555.125 2.78238C563.726 7.07475 567.223 17.5397 562.931 26.1411L547.348 18.3648Z" fill="#CA8787"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M547.378 18.3019L531.783 10.5191C527.484 19.1336 530.981 29.5986 539.596 33.8976C548.197 38.1899 558.662 34.6926 562.961 26.0781L547.378 18.3019Z" fill="#FFD0D0"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M288.017 41.0098L279.774 47.7895C276.035 43.2435 276.69 36.5189 281.243 32.774C285.789 29.0348 292.513 29.6898 296.253 34.2358L288.017 41.0098Z" fill="#FF9750"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M287.989 40.9771L279.746 47.7568C283.491 52.3098 290.215 52.9648 294.769 49.2199C299.315 45.4807 299.969 38.7561 296.225 34.2031L287.989 40.9771Z" fill="#FFD2B3"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M725.016 189.46L614 190.615C614.664 129.73 664.933 78.5337 726.217 79.1928C787.502 79.8519 836.462 129.73 835.799 190.614L725.016 189.46Z" fill="#FDE9C4"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M726.579 189.661L668.31 190.614C667.961 158.655 693.757 131.092 725.976 130.745C758.145 130.399 784.5 158.655 784.848 190.614L726.579 189.661Z" fill="#FFBA37" fill-opacity="0.5"/>
                    </svg>
                    <div class="card">
                        <div class="card-body py-4">
                            <svg width="39" height="52" viewBox="0 0 39 52" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.24915 0C1.45197 0 0 1.45234 0 3.25C0 5.04766 1.45197 6.5 3.24915 6.5V7.61719C3.24915 11.9234 4.96511 16.057 8.0112 19.1039L14.8953 26L8.0112 32.8961C4.96511 35.943 3.24915 40.0766 3.24915 44.3828V45.5C1.45197 45.5 0 46.9523 0 48.75C0 50.5477 1.45197 52 3.24915 52H6.49831H32.4915H35.7407C37.5379 52 38.9898 50.5477 38.9898 48.75C38.9898 46.9523 37.5379 45.5 35.7407 45.5V44.3828C35.7407 40.0766 34.0247 35.943 30.9787 32.8961L24.0945 26L30.9888 19.1039C34.0349 16.057 35.7508 11.9234 35.7508 7.61719V6.5C37.548 6.5 39 5.04766 39 3.25C39 1.45234 37.548 0 35.7508 0H32.4915H6.49831H3.24915ZM9.74746 7.61719V6.5H29.2424V7.61719C29.2424 9.54688 28.6738 11.4156 27.6178 13H11.372C10.3262 11.4156 9.74746 9.54688 9.74746 7.61719ZM11.372 39C11.7274 38.4617 12.1437 37.9539 12.6006 37.4867L19.4949 30.6008L26.3892 37.4969C26.8563 37.9641 27.2624 38.4719 27.6178 39.0102H11.372V39Z" fill="#b4b4b4"/>
                            </svg>
                            <p class="fs-20 text-dark fw-400 pt-3 pb-4 px-xl-5">We are getting things ready ! Hang Tight – You'll start seeing others here soon</p>
                            <a href="#" onclick="reloadPage(); return false;" class="refresh-v1">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.15628 6.09409C3.42905 5.31547 3.87185 4.58329 4.49531 3.95826C6.70931 1.72599 10.2978 1.72599 12.5118 3.95826L13.1175 4.57258H11.9025C11.2755 4.57258 10.7689 5.08332 10.7689 5.7155C10.7689 6.34767 11.2755 6.85842 11.9025 6.85842H15.8523H15.8664C16.4934 6.85842 17 6.34767 17 5.7155V1.71527C17 1.0831 16.4934 0.572353 15.8664 0.572353C15.2394 0.572353 14.7329 1.0831 14.7329 1.71527V2.97249L14.1129 2.34388C11.0133 -0.781294 5.99021 -0.781294 2.8906 2.34388C2.02626 3.21536 1.40279 4.24041 1.02021 5.3369C0.811211 5.93337 1.12294 6.5834 1.71098 6.79413C2.29902 7.00486 2.94728 6.69055 3.15628 6.09766V6.09409ZM0.814753 9.19069C0.637633 9.24426 0.467597 9.3407 0.329444 9.48356C0.187747 9.62643 0.0921025 9.79787 0.0425089 9.98359C0.0318817 10.0265 0.0212544 10.0729 0.0141696 10.1193C0.00354239 10.18 0 10.2407 0 10.3015V14.2874C0 14.9196 0.506564 15.4303 1.13357 15.4303C1.76058 15.4303 2.26714 14.9196 2.26714 14.2874V13.0338L2.8906 13.6588C5.99021 16.7804 11.0133 16.7804 14.1094 13.6588C14.9737 12.7873 15.6007 11.7623 15.9833 10.6693C16.1923 10.0729 15.8806 9.42285 15.2926 9.21212C14.7045 9.00139 14.0563 9.3157 13.8473 9.90859C13.5745 10.6872 13.1317 11.4194 12.5082 12.0444C10.2942 14.2767 6.70577 14.2767 4.49177 12.0444L4.48823 12.0408L3.88248 11.4301H5.10106C5.72807 11.4301 6.23463 10.9194 6.23463 10.2872C6.23463 9.655 5.72807 9.14426 5.10106 9.14426H1.14774C1.09106 9.14426 1.03438 9.14783 0.977704 9.15497C0.921025 9.16212 0.867889 9.17283 0.814753 9.19069Z" fill="black"/>
                                </svg>
                                <span>Check Again</span>
                            </a>
                        </div>
                    </div>
                </div>`;

            if ($('#rsvp_users_list').html() == "") {
                $('#load_more_btn').hide();
                $('#rsvp_users_list').html(cccontent);
                $("#rsvp_default_list").hide();
            }
            $(".rsvp_actions").show();
        } else {
            content += ``;
            $('.rsvp_actions').css('display', 'flex');
            $('.rsvp_actions.rsvp_search').css('display', 'flex');
            $("#rsvp_default_list").hide();
            //console.log("listAppend", listAppend);            
            if (listAppend == 1) {
                $('#rsvp_users_list').append(content);
            } else {
                $('#rsvp_users_list').html(content);
            }
        }
        /* console.log('==========' + $('.attendees_card').length)
        console.log('-----total_count----' + total_count) */

        /* if (listed_count >= total_count) {
            $('#load_more_btn').hide();
        } else {
            $('#load_more_btn').show();
        } */

        var cumulative_count = $('.attendees_card').length;
        if (cumulative_count >= total_count) {
            $('#load_more_btn').hide();
        } else {
            $('#load_more_btn').show();
        }

        loader(false, $("#rsvpdir_loaderArea"));
        loader(false, $("#rsvpdir_loaderArea_btm"));
    }

    function loadMoreRsvpedUsers() {
        //loader(true, $("#rsvpdir_loaderArea"),30);
        loader(true, $("#rsvpdir_loaderArea_btm"), 30);
        $('#load_more_btn').hide();

        $('#rsvp_perpage').val(parseInt($('#rsvp_perpage').val(), 10) + 1);
        var search = $('#rsvp_search').val();
        var eventtoken = "<?php echo $eventtoken;?>";
        //$('#rsvp_users_list').html('<div class="text-center">No result found</div>');
        // getEventRSVPedUsers(eventtoken,search);
        eventCheckinList(eventtoken, search);
    }

    function reloadPage(){
        const url = new URL(window.location.href);
            url.searchParams.set('tab', 'rsvp_desc');    // add or update param     

            window.location.href = url.toString(); 
    }

    $(document).ready(function () {
        const params = new URLSearchParams(window.location.search);
        const tabName = params.get('tab');
        //alert(tabName);

        if (tabName) {
            $('.nav-tabs a[href="#' + tabName + '"]').tab('show');
        }
    });

    function performRSVPSearch() {
        loader(true, $("#rsvpdir_loaderArea"), 30);
        var search = $('#rsvp_search').val();
        var eventtoken = "<?php echo $eventtoken;?>";
        ////$('#rsvp_users_list').html('<div class="text-center">No result found</div>');
        // getEventRSVPedUsers(eventtoken,search);
        eventCheckinList(eventtoken, search);
    }


    $(document).on('click', '.event_download_rsvp', function () {
        const currentElem = $(this);
        let eventtoken = "<?php echo $eventtoken;?>";


        /*var url = '<?php echo TAOH_SITE_URL_ROOT . '/events/export_rsvp/?eventtoken=';?>' + eventtoken;
            
            const win = window.open(url);

            if (win) {
            win.onload = function () {
                win.close();
            };
            }*/

        var data = {
            'taoh_action': 'rsvp_download',

            'eventtoken': eventtoken,

        };
        $.post(_taoh_site_ajax_url, data, function (response) {
            if (response.success) {
                taoh_set_success_message('RSVP list will be send to your email. Please check your inbox shortly!');
            } else {
                taoh_set_error_message('Download Failed.');
            }
        }).fail(function () {
            console.log(_taoh_site_ajax_url);
            console.log(data);
            console.log("Network issue RSVP!");
        })


    });


    $(document).on('click', '.email_slug_btn', function () {
        //alert(2);
        const currentElem = $(this);
        let eventtoken = currentElem.data('eventtoken');

        let slug = currentElem.data('slug');

        $('#email_type').val('slug');
        $('#event_token').val(eventtoken);
        $('#ticket_type_slug').val(slug);

        $('#emailModal').modal('show');
    });

    /* $(document).ready(function () {
        $('.profile_send_message_btn').on('click', function () {
            let toPtoken = $(this).data('toptoken');
            let respondPtoken = $(this).data('respondptoken');
    alert(toPtoken+'===='+respondPtoken);
            showOfflineMessageModal(toPtoken, respondPtoken);
        });
    });

    function showOfflineMessageModal(toPtoken, respondPtoken){
        if (toPtoken?.trim() && respondPtoken?.trim()) {
            $('#profileOfflineMessage').val('');
            $('#profileOfflineToPtoken').val(toPtoken);
            $('#profileOfflineLocationPath').val('/profile/' + respondPtoken);
            $('#profileOfflineSuccessMessage').hide();
            $('#profileOfflineMessageBlock').show();
            $('#profileOfflineMessageModal').modal('show');
        }
    } */

    function eventCheckinList(eventtoken, search = '') {
        const eventStatus = $('#event_status_hidden').val();
        const rsvpStatus = $('#rsvp_status_hidden').val();
        const superToken = $('#superorganizer_token').val();
        const page = $('#rsvp_perpage').val();
        const countryLocked = $('#event_country_lock').val();
        const countryName = $('#event_country_name').val();
        const myCountry = '<?= addslashes($user_country); ?>';
        const isOrganizer = $('#is_organizer').val() == 1;

        let allowView = false;
        let attendeesMsg = '';

        // Push event check-in list
        if (typeof eventCheckIn === 'function') {
            eventCheckIn(eventtoken);
        }

        // Permission logic
        if (superToken === my_ptoken) {
            // Super organizer always allowed
            allowView = true;

        } else if (!isLoggedIn) {
            attendeesMsg = "Login and register to view the attendees.";

        } else {
            // Logged in user
            if (rsvpStatus == 1 && eventStatus == 1) {
                // RSVP done, event live
                allowView = true;

            } else if (rsvpStatus == 1 && eventStatus == 2) {
                // RSVP done, event upcoming
                const eventStartdate = formatTimestamp($('#event_start_at').val());
                $('#rsvp_default_list').hide();

                if (isBeforeThreeDays(eventStartdate)) {
                    allowView = true;
                }
            } else {
                attendeesMsg = (rsvpStatus != 1)
                    ? "Register to view the attendees."
                    : "You can view the attendees when the event is live.";
            }
        }

        if (allowView) {
            const data = {
                taoh_action: 'event_checkin_list',
                country_locked: countryLocked,
                country: countryName,
                eventtoken: eventtoken,
                ptoken: my_ptoken,
                page: page,
                my_country: myCountry,
                q: search
            };

            $.post(_taoh_site_ajax_url, data, function (response1) {
                if (response1.success) {
                    const listappend = (page == 1) ? 0 : 1;
                    constructRSVPList(eventtoken, response1, search, 1, listappend);
                } else {
                    taoh_set_error_message('Event check-in failed.');
                }
            }).fail(function () {
                console.error('Network issue RSVP!', _taoh_site_ajax_url, data);
            });

        } else {
            // Not allowed to view list
            $('.rsvp_actions').hide();
            $('#rsvp_default_list').show();
            $('#load_more_btn').hide();

            if (isOrganizer) {
                $('.rsvp_actions').show();
            }
            if (is_user_rsvp_done) {
                $('#register_now').hide();
            }

            loader(false, $('#rsvpdir_loaderArea'));
        }
    }

    function isBeforeThreeDays(dateStr) {
        const eventDate = new Date(dateStr);
        const now = new Date();

        const threeDaysAgo = new Date(dateStr);
        threeDaysAgo.setDate(eventDate.getDate() - 3);
        // console.log(threeDaysAgo,eventDate,now);
        return now >= threeDaysAgo;
    }



</script>
