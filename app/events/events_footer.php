<script>
    var my_ptoken = "<?php echo (taoh_session_get(TAOH_ROOT_PATH_HASH)['USER_INFO'] ?? null)?->ptoken ?? ''; ?>";
    var sponsorGroup = {};

    if (typeof getEventBaseInfo !== 'function') {
        function getEventBaseInfo(requestData, serverFetch = false) {
            return new Promise((resolve, reject) => {
                if (!requestData.eventtoken) {
                    reject("No event token provided.");
                    return;
                }

                const eventBaseInfoKey = `event_detail_${requestData.eventtoken}`;

                const handleResponse = (response, saveToDB = true) => {
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventBaseInfoKey,
                                values: response,
                                timestamp: Date.now()
                            });
                        }
                        resolve({requestData, response});
                    } else {
                        reject("Failed to fetch event details! Try Again");
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_event_baseinfo',
                            taoh_action: 'get_event_baseinfo',
                            token: _taoh_ajax_token,
                            eventtoken: requestData.eventtoken
                        },
                        dataType: 'json',
                        success: (response) => handleResponse(response, true),
                        error: (xhr) => {
                            window.location.href = '<?php echo TAOH_SITE_URL_ROOT; ?>/404?no=event';
                            reject(`Error: ${xhr.status}`)
                        }
                    });
                } else {
                    IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey)
                        .then((data) => {
                            if (data?.values) {
                                handleResponse(data.values, false);
                            } else {
                                getEventBaseInfo(requestData, true).then(resolve).catch(reject);
                            }
                        })
                        .catch(reject);
                }
            });
        }
    }

    if (typeof getEventRSVPStatus !== 'function') {
        function getEventRSVPStatus(requestData, serverFetch = false) {
            return new Promise((resolve, reject) => {
                if (!requestData.eventtoken) {
                    reject("No event token provided.");
                    return;
                }

                const eventRSVPStatusKey = `event_rsvp_status_${requestData.eventtoken}`;

                const handleResponse = (response, saveToDB = true) => {
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventRSVPStatusKey,
                                values: response,
                                timestamp: Date.now()
                            });
                        }
                        resolve({requestData, response});
                    } else {
                        reject("Failed to fetch event details! Try Again");
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_event_rsvp_status',
                            taoh_action: 'get_event_rsvp_status',
                            token: _taoh_ajax_token,
                            eventtoken: requestData.eventtoken
                        },
                        dataType: 'json',
                        success: (response) => handleResponse(response, true),
                        error: (xhr) => reject(`Error: ${xhr.status}`)
                    });
                } else {
                    IntaoDB.getItem(objStores.event_store.name, eventRSVPStatusKey)
                        .then((data) => {
                            if (data?.values) {
                                handleResponse(data.values, false);
                            } else {
                                getEventRSVPStatus(requestData, true).then(resolve).catch(reject);
                            }
                        })
                        .catch(reject);
                }
            });
        }
    }

    if (typeof getEventRSVPInfo !== 'function') {
        function getEventRSVPInfo(requestData, serverFetch = false) {
            return new Promise((resolve, reject) => {
                if (!requestData.rsvptoken) {
                    reject("No rsvp token provided.");
                    return;
                }

                const eventRSVPInfoKey = `event_rsvp_info_${requestData.rsvptoken}`;

                const handleResponse = (response, saveToDB = true) => {
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventRSVPInfoKey,
                                values: response,
                                timestamp: Date.now()
                            });
                        }
                        resolve({requestData, response});
                    } else {
                        reject("Failed to fetch event rsvp details! Try Again");
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_event_rsvp_info',
                            taoh_action: 'get_event_rsvp_info',
                            token: _taoh_ajax_token,
                            rsvptoken: requestData.rsvptoken
                        },
                        dataType: 'json',
                        success: (response) => handleResponse(response, true),
                        error: (xhr) => reject(`Error: ${xhr.status}`)
                    });
                } else {
                    IntaoDB.getItem(objStores.event_store.name, eventRSVPInfoKey)
                        .then((data) => {
                            if (data?.values) {
                                handleResponse(data.values, false);
                            } else {
                                getEventRSVPInfo(requestData, true).then(resolve).catch(reject);
                            }
                        })
                        .catch(reject);
                }
            });
        }
    }

    if (typeof _getEventMetaInfo !== 'function') {
        function _getEventMetaInfo(requestData, serverFetch = false, saveToDB = true) {
            return new Promise((resolve, reject) => {
                if (!requestData.eventtoken) {
                    reject("No event token provided.");
                    return;
                }

                const type = requestData.type || '';
                const search = requestData.search || '';

                let eventMetaInfoKey = `event_MetaInfo_${requestData.eventtoken}`;
                if (type || search) eventMetaInfoKey += `_${type}_${crc32(search)}`;

                const handleResponse = (response) => {
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventMetaInfoKey,
                                values: response,
                                timestamp: Date.now()
                            });
                        }
                        resolve({requestData, response});
                    } else {
                        reject("Failed to fetch event meta details! Try Again");
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_event_MetaInfo',
                            taoh_action: 'get_event_MetaInfo',
                            token: _taoh_ajax_token,
                            eventtoken: requestData.eventtoken,
                            type: requestData.type || '',
                            search: requestData.search || '',
                            search_speaker_name: requestData.search_speaker_name || '',
                        },
                        dataType: 'json',
                        success: handleResponse,
                        error: (xhr) => reject(`Error: ${xhr.status}`)
                    });
                } else {
                    IntaoDB.getItem(objStores.event_store.name, eventMetaInfoKey)
                        .then((data) => {
                            if (data?.values) {
                                handleResponse(data.values);
                            } else {
                                _getEventMetaInfo(requestData, true, saveToDB).then(resolve).catch(reject);
                            }
                        })
                        .catch(reject);
                }
            });
        }
    }

    if (typeof getEventSavedInfo !== 'function') {
        function getEventSavedInfo(requestData, serverFetch = false, saveToDB = true) {
            return new Promise((resolve, reject) => {
                if (!requestData.eventtoken) {
                    reject("No event token provided.");
                    return;
                }

                const eventSavedInfoKey = `event_Saved_${requestData.eventtoken}`;

                const handleResponse = (response) => {
                    if (response.success) {
                        if (saveToDB) {
                            IntaoDB.setItem(objStores.event_store.name, {
                                taoh_data: eventSavedInfoKey,
                                values: response,
                                timestamp: Date.now()
                            });
                        }
                        resolve({requestData, response});
                    } else {
                        reject("Failed to fetch event meta details! Try Again");
                    }
                };

                if (serverFetch) {
                    $.ajax({
                        url: _taoh_site_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'get_event_saved_list',
                            taoh_action: 'get_event_saved_list',
                            token: _taoh_ajax_token,
                            eventtoken: requestData.eventtoken
                        },
                        dataType: 'json',
                        success: handleResponse,
                        error: (xhr) => reject(`Error: ${xhr.status}`)
                    });
                } else {
                    IntaoDB.getItem(objStores.event_store.name, eventSavedInfoKey)
                        .then((data) => {
                            if (data?.values) {
                                handleResponse(data.values);
                            } else {
                                getEventSavedInfo(requestData, true, saveToDB).then(resolve).catch(reject);
                            }
                        })
                        .catch(reject);
                }
            });
        }
    }

    async function getSponsorBadges(eventtoken) {
        const eventBaseInfoKey = `event_MetaInfo_${eventtoken}`;
        const data = await IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey);

        if (!data?.values) return {full: [], semi: []}; // Return empty structure if no data

        const eventSponsor = data.values.output.event_sponsor || [];
        const profile_ids = {full: [], semi: []};

        /*for (const sponsor of eventSponsor) {
            if (sponsor.badge_profiles) {
                try {
                    const badgesArray = JSON.parse(sponsor.badge_profiles);
                    const badgeType = sponsor.display_type === 'full' ? 'full' : 'semi';

                    for (const badgeUrl of badgesArray) {
                        if (badgeUrl && badgeUrl.trim()) {
                            const urlObj = new URL(badgeUrl);
                            const profileId = urlObj.pathname.split('/').filter(Boolean).pop();
                            if (profileId) profile_ids[badgeType].push(profileId);
                        }
                    }
                } catch (error) {
                    console.error('Error parsing badge_profiles:', error);
                }
            }
        }*/

        return profile_ids;
    }

    async function getEventSponsor(eventtoken) {
        return new Promise((resolve, reject) => {
            const eventBaseInfoKey = `event_MetaInfo_${eventtoken}`;

            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
                //console.log('---eventSponsoreventSponsor-------',data)
                if (data?.values) {
                    processEventSponsor(eventtoken, data.values, resolve);
                } else {
                    updateEventSponsor(eventtoken, true, resolve);
                }
            });
        });
    }

    async function getEventSponsorForShare(eventtoken, trackingtoken = '') {
        return new Promise((resolve, reject) => {
            const eventBaseInfoKey = `event_MetaInfo_${eventtoken}`;

            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
                if (data?.values) {
                    const sponsor_data = data.values.output;
                    const eventExhibitors = sponsor_data.event_exhibitor || [];
                    const eventSponsors = sponsor_data.event_sponsor || [];
                    const eventSponsorsBecomeExhibitor = sponsor_data.event_sponsor_deleted || [];

                    // Create a set of sponsor IDs from exhibitors
                    const exhibitorsSponsorId = new Set(
                        eventExhibitors.filter(exhibitor => exhibitor.sponsor_id)
                            .map(exhibitor => exhibitor.sponsor_id)
                    );

                    // Check if the current user is a sponsor directly or through becoming an exhibitor
                    let currentUserSponsor = eventSponsors.find(sponsor => sponsor.ptoken === my_ptoken) ||
                        eventSponsorsBecomeExhibitor.find(sponsor => sponsor.ptoken === my_ptoken && exhibitorsSponsorId.has(sponsor.ID));

                    // If the current user is a sponsor, update UI elements
                    if (currentUserSponsor) {
                        $('.event_sponsor_right_header').hide();
                        $('.get-started').hide();
                        $('#continuePurchase').modal('hide');
                    } else if (trackingtoken != '') {
                        $('#continuePurchase').modal('show');
                    }
                } else {
                    if (trackingtoken != '') {
                        $('#continuePurchase').modal('show');
                    }
                    updateEventSponsor(eventtoken, true, resolve);
                }
            });
        });
    }

    async function updateEventSponsor(eventtoken, serverFetch = true, resolveFn = () => {
    }) {

        const eventBaseInfoKey = `event_MetaInfo_${eventtoken}`;

        const handleResponse = (response) => {
            if (response.success) {

                IntaoDB.setItem(objStores.event_store.name, {
                    taoh_data: eventBaseInfoKey,
                    values: response,
                    timestamp: Date.now()
                });

                processEventSponsor(eventtoken, response, resolveFn);

            } else {
                $('.rsvp_ticket_Sponsor').addClass('disabled');
                $('.rsvp_ticket_Sponsor').attr('disabled', true);
                resolveFn();
                console.log('No result for this event sponsors! Try Again');
            }
        };

        if (serverFetch) {


            $.ajax({
                url: _taoh_site_ajax_url,
                type: 'POST',  // changed to post to avoid caching issues in flexible domain
                /*data: {
                    action: 'get_event_sponsors',
                    taoh_action: 'get_event_sponsors',
                    token: _taoh_ajax_token,
                    eventtoken: eventtoken
                },*/
                data: {
                    action: 'get_event_MetaInfo',
                    taoh_action: 'get_event_MetaInfo',
                    token: _taoh_ajax_token,
                    eventtoken: eventtoken,
                    type: '',
                    search: '',
                    search_speaker_name: '',
                },
                dataType: 'json',
                success: handleResponse,
                error: (xhr) => {
                    console.error('Error:', xhr.status)
                    resolveFn();
                }
            });
        } else {
            IntaoDB.getItem(objStores.event_store.name, eventBaseInfoKey).then((data) => {
                if (data?.values) {
                    processEventSponsor(eventtoken, data.values, resolveFn);
                } else {
                    updateEventSponsor(eventtoken, true, resolveFn);
                }
            });
        }
    }

    function processEventSponsor(eventtoken, response, resolveFn = () => {
    }) {
        const sponsor_data = response.output;
        const eventExhibitors = sponsor_data.event_exhibitor || [];
        const eventSponsors = sponsor_data.event_sponsor || [];
        const eventSponsorsBecomeExhibitor = sponsor_data.event_sponsor_deleted || [];

        // Create a set of sponsor IDs from exhibitors
        const exhibitorsSponsorId = new Set(
            eventExhibitors.filter(exhibitor => exhibitor.sponsor_id)
                .map(exhibitor => exhibitor.sponsor_id)
        );

        // Check if the current user is a sponsor directly or through becoming an exhibitor
        let currentUserSponsor = eventSponsors.find(sponsor => sponsor.ptoken === my_ptoken) ||
            eventSponsorsBecomeExhibitor.find(sponsor => sponsor.ptoken === my_ptoken && exhibitorsSponsorId.has(sponsor.ID));

        // If the current user is a sponsor, update UI elements
        if (currentUserSponsor) {
            $('#sponsor_type').val(currentUserSponsor.sponsor_type);
            $('#sponsor_type').setSyncedData('sponsorid', currentUserSponsor.ID);
            $('.event_sponsor_right_header').hide();
            $('.get-started').hide();
            $('#continuePurchase').modal('hide');
        }

        setTimeout(() => {
            if (currentUserSponsor) {
                $('#sponsor_type').val(currentUserSponsor.sponsor_type);
                $('#sponsor_type').setSyncedData('sponsorid', currentUserSponsor.ID);
                $('.event_sponsor_right_header').hide();
                $('.get-started').hide();
                $('#continuePurchase').modal('hide');
            }
        }, 5000);

        resolveFn();
    }

    function openSponsorLink(sposnor_id, eventtoken) {
        var link = '<?php echo TAOH_SITE_URL_ROOT; ?>/events/event_sponsor/' + eventtoken + '/' + sposnor_id + '/update';
        window.open(link, '_blank');
    }

    function getEventLobbyDashboard(eventtoken, response, hallColorArray = {}, search = '') {
        let colorArray = ['#708090', '#A7C7E7', '#5F9EA0', '#B3A398', '#A8BBA2', '#C3D6B8', '#EED9C4', '#D1C2E0', '#F5F5F5', '#748CAB', '#D6D1CD', '#E4C9AF', '#B8E0D2', '#E6C0C0', '#C8A2C8'];

        const output = (response && response.output) ? response.output : {};
        let speaker_list = output.event_speaker;
        let exh_list = output.event_exhibitor;

        let is_organizer = $("#is_organizer").val();

        let local_timezone = '';
        let locality = '';

        let user_timezone = '';

        if (typeof isLoggedIn !== 'undefined' && isLoggedIn) {
            user_timezone = '<?= taoh_user_timezone(); ?>';
        }
        if (typeof isLoggedIn === 'undefined' || !isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (typeof isValidTimezone === 'function' && !isValidTimezone(user_timezone)) user_timezone = 'UTC';
        if (!user_timezone) user_timezone = 'UTC';

        getEventBaseInfo({ eventtoken }, false)
            .then(({ requestData, response: baseResp }) => {
                let event_output = (baseResp && baseResp.output) ? baseResp.output : {};
                local_timezone = event_output.local_timezone || '';
                locality = (event_output.conttoken && event_output.conttoken.locality) ? event_output.conttoken.locality : '';

                let event_live_status = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', locality, user_timezone);

                $('#dashboard_list').html('');
                var content = '';
                var is_content = 0;
                var removeSponsor = [];

                if (speaker_list != undefined && speaker_list.length > 0) {
                    speaker_list.sort((a, b) => new Date(a.spk_datefrom) - new Date(b.spk_datefrom));

                    $.each(speaker_list, function (i, v) {
                        let disableJoinBtn = 'disabled';

                        if (likedArr && Array.isArray(likedArr.event_speaker) && likedArr.event_speaker.includes(v.ID)) {
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
                                timezone: local_timezone,
                                locality: locality
                            };

                            let event_timestamp_end_data = {
                                utc_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                                local_datetime: v.spk_dateto.replace(/[T:-]/g, '') + '00',
                                timezone: local_timezone,
                                locality: locality
                            };

                            let startdate = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'dd MMM yyyy', 0);
                            let starttime = format_event_timestamp(event_timestamp_start_data, user_timezone, 'date', 'hh:mm A', 1);

                            let enddate = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'dd MMM yyyy', 0);
                            let endtime = format_event_timestamp(event_timestamp_end_data, user_timezone, 'date', 'hh:mm A', 1);

                            let color = colorArray[i % colorArray.length];
                            let backgroundColor = '';

                            if (hallColorArray && hallColorArray[v.spk_hall] != undefined) backgroundColor = hallColorArray[v.spk_hall];
                            else backgroundColor = color;

                            let spk_img = '';
                            if (v.spk_logo_image != '') {
                                spk_img = v.spk_logo_image;
                            } else {
                                var tt = encodeURIComponent(v.spk_title);
                                spk_img = "<?php echo TAOH_CDN_PREFIX . "/images/igcache/"?>" + tt + "/630_630/blog.jpg";
                            }

                            content += `<div class="new-agenda-list p-3 px-lg-5 mb-3 d-flex flex-column flex-md-row" style="gap: 16px;">

                            <div class="g-overlay-con">
                                <div class="n-hall-list-bg d-md-none" style="background-image: url('${spk_img}');"></div>
                                <!--<div class="glass-overlay d-md-none"></div>-->
                                <img class="n-hall-list-pic"
                                src="${spk_img}" alt="">
                            </div>

                            <div style="flex: 1;">

                            <div class="d-flex flex-wrap-reverse" style="gap: 12px;">
                                <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">`;

                            if (startdate && enddate) {
                                if (startdate == enddate) {
                                    content += `<p class="n-info-badge mr-2" >${startdate}, ${starttime} to ${endtime}</p>`;
                                } else {
                                    content += `<p class="n-info-badge mr-2"  >${startdate} ${starttime} to ${enddate} ${endtime}</p>`;
                                }
                            }

                            content += `  <p class="n-info-badge"  style="background-color:#F9A386;color:#000000">${v.spk_hall}</p>
                                </div>
                                <div class="d-flex ml-auto" style="gap: 12px;">`;

                            if ((v.ptoken == my_ptoken && opt == 'chat') || is_organizer == 1) {
                                content += `<a title="Edit Speaker" style="width:30px;" class="svg-opt-con btn p-0 edit_speaker metrics_action" id="edit_speaker_${v.ID}" data_id="${v.ID}" data-metrics="edit_speaker">
                                   <i class="fa-solid fa-edit"></i>
                                </a>`;
                            }

                            content += `<a title="View Speaker" target="_blank" style="width:30px;" href="${TAOH_CURR_APP_URL}/speaker/${eventtoken}/${v.ID}"
                              class="svg-opt-con btn" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker"><i class="fa-solid fa-eye"></i></a>`;

                            const isLikedSpeakers = likedArr && Array.isArray(likedArr.event_speaker) && likedArr.event_speaker.includes(v.ID);
                            content += `<a title="${isLikedSpeakers ? 'Saved' : 'Save'} Speaker" style="width:30px;" href="javascript:void(0)" class="svg-opt-con btn ${isLikedSpeakers ? 'already-saved' : 'speaker_like speaker_save'}" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="speaker">
                                        <i class="fa ${isLikedSpeakers ? 'fa-bookmark' : 'fa-bookmark-o'}" aria-hidden="true"></i>
                                    </a>`;

                            content += `<a title="View More" style="width:30px;" id="dropdown-myagenda_${i}" data-target="#myagenda-content_${i}" class="svg-opt-con btn dropdown-agenda">
                                    <svg style="min-width: fit-content;" width="14" height="10" viewBox="0 0 14 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6.29399 9.17178C6.68448 9.60983 7.31864 9.60983 7.70914 9.17178L13.7071 2.44337C14.0976 2.00532 14.0976 1.29393 13.7071 0.85588C13.3166 0.417832 12.6825 0.417832 12.292 0.85588L7 6.7923L1.70802 0.859384C1.31753 0.421336 0.683365 0.421336 0.292871 0.859384C-0.0976236 1.29743 -0.0976236 2.00882 0.292871 2.44687L6.29086 9.17529L6.29399 9.17178Z" fill="black"/>
                                    </svg>
                                </a>`;

                            content += `</div>
                            </div>

                            <div class="my-2 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between" style="gap: 12px;">
                                <div>
                                    <h6 class="title line-clamp-1 mb-2" style="max-width: 752px;">${taoh_desc_decode(v.spk_title)}</h6>
                                    <p id="myagenda-content_${i}" class="desc-text mb-2" style="display:none">${$.trim(v.spk_desc) != '' ? taoh_desc_decode(v.spk_desc) : ''}</p>
                                    <div class="d-flex flex-wrap" style="gap: 12px;">`;

                            if (Array.isArray(v.spk_name)) {
                                v.spk_name.map(function (item, r) {
                                    let profileimg = v.spk_profileimg[r];
                                    content += `<div class="d-flex align-items-center" style="gap: 6px;">
                                        <img class="p-img" src="${profileimg}" alt="">
                                        <p class="mb-1 name-role">${item}, <span>${v.spk_desig[r]}, ${v.spk_company[r]}</span></p>
                                    </div>`;
                                });
                            }

                            content += `</div></div>`;

                            if (isLoggedIn && event_live_status == 'live' && rsvp_sponsor_title != '' && rsvp_sponsor_title != undefined && isJoinEnabled(v)) {
                                disableJoinBtn = '';
                            }

                            if (disableJoinBtn == '' && v.spk_state != "live" && v.spk_state != "active") {
                                disableJoinBtn = 'disabled';
                            }

                            content += `<div>`;
                            if (v.enable_tao_networking == 0 && v.spk_external_video_room_link != '') {
                                content += `<a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-primary'} joinus-btn join_video_link ${disableJoinBtn}" href="${v.spk_external_video_room_link}" target="_blank">Join us</a>`;
                            } else {
                                content += `<a class="btn ${disableJoinBtn == 'disabled' ? 'bor-btn' : 'btn-primary'} joinus-btn join_networking ${disableJoinBtn}"
                                href="<?php echo TAOH_SITE_URL_ROOT . '/' . TAOH_CURR_APP_SLUG; ?>/club/${taoh_desc_decode(event_output.conttoken.title)}-${eventtoken}?session_id=${v.ID}&session_name=${encodeURIComponent(v.spk_title)}" target="_blank">Join us</a>`;
                            }

                            if ($.trim(v.spk_room_location) != '') {
                                content += ` <div class="text-nowrap">
                                <svg style="min-width: fit-content;" width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198Z" fill="#2557A7"/>
                                </svg>
                                ${v.spk_room_location}</div> `;
                            }

                            content += `</div>`;
                            content += `</div>`;
                            content += `</div></div>`;
                            content += `</div></div>`;

                            is_content = 1;
                        }
                    });
                }

                if (exh_list != undefined && exh_list.length > 0) {
                    $.each(exh_list, function (i, v) {
                        if (v.sponsor_id != '' && v.sponsor_id != null && v.sponsor_id != undefined) {
                            removeSponsor.push(v.sponsor_id);
                        }

                        let color = colorArray[i % colorArray.length];
                        let backgroundColor = '';

                        if (hallColorArray && hallColorArray[v.exh_hall] != undefined) backgroundColor = hallColorArray[v.exh_hall];
                        else backgroundColor = color;

                        if (likedArr && Array.isArray(likedArr.event_exhibitor) && likedArr.event_exhibitor.includes(v.ID)) {
                            let exhibitorWebsiteUrl = v.exh_hero_button_url || '';
                            content += ` <!-- new exh list -->
                        <div class="new-exh-list mb-3">
                            <!-- <div class="gradient-bg-border"></div> -->

                            <div class="p-3 px-lg-5 d-flex" style="gap: 12px; flex: 1;">
                                <div class="d-flex flex-wrap align-items-start" style="gap: 12px; flex: 1;">

                                    <div class="g-overlay-con mr-3">
                                        <div class="n-hall-list-bg" style="background-image: url(${v.exh_logo})"></div>
                                        <!--<div class="glass-overlay"></div>-->
                                        <img class="n-hall-list-pic" src="${v.exh_logo}" alt="">
                                    </div>

                                    <div style="flex: 1;">
                                        <div class="d-flex align-items-center justify-content-between" style="gap: 12px;">
                                            <div class="d-flex flex-wrap" style="gap: 3px;">
                                                <p class="n-info-badge mr-2" style="background-color:#F9A386;color:#000000">${v.exh_hall ? taoh_desc_decode_new(v.exh_hall) : ""}</p>
                                            </div>

                                            <div class="d-flex align-items-center" style="gap: 12px;">
                                             `;

                            content += `
                                        ${v.ptoken == my_ptoken || is_organizer == 1 ? `
                                                <a title="Edit Exhibitor" class="svg-opt-con btn p-0 edit-exhibitor" id="edit_exh_${v.ID}" data-id="${v.ID}" data-type="exhibitor">
                                                   <i class="fa-solid fa-edit"></i>
                                                </a> ` : ''}`;

                            content += `    <a title="View Exhibitor" target="_blank" href="${_taoh_site_url_root}/events/exhibitors/${v.ID}/${eventtoken}"
                                        class="svg-opt-con btn "> <i class="fa-solid fa-eye"></i></a>`;

                            if (likedArr && Array.isArray(likedArr.event_exhibitor) && likedArr.event_exhibitor.includes(v.ID)) {
                                content += `  <a title="Save Exhibitor" href="#" class="svg-opt-con btn already-saved" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="exhibitor" >
                                                        <svg style="min-width: fit-content;" width="12" height="18" viewBox="0 0 12 18" fill="black" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5.53682 13.4304L0.767828 17.175C0.758399 17.1566 0.75 17.1296 0.75 17.0942V1.68243C0.75 1.08455 1.16471 0.75 1.5 0.75H10.5C10.8353 0.75 11.25 1.08455 11.25 1.68243V17.0942C11.25 17.1296 11.2416 17.1565 11.2322 17.175L6.46318 13.4304L6 13.0667L5.53682 13.4304Z" stroke="black" stroke-width="1.5"/>
                                                        </svg>
                                                    </a>`;
                            } else {
                                content += `  <a title="Save Exhibitor" href="#" class="svg-opt-con btn exhibitor_like exhibitor_save" data-cont="${v.ID}" data-eventtoken="${eventtoken}" data-slug="exhibitor" >
                                                        <svg style="min-width: fit-content;" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5.53682 13.4304L0.767828 17.175C0.758399 17.1566 0.75 17.1296 0.75 17.0942V1.68243C0.75 1.08455 1.16471 0.75 1.5 0.75H10.5C10.8353 0.75 11.25 1.08455 11.25 1.68243V17.0942C11.25 17.1296 11.2416 17.1565 11.2322 17.175L6.46318 13.4304L6 13.0667L5.53682 13.4304Z" stroke="black" stroke-width="1.5"/>
                                                        </svg>
                                                    </a>`;
                            }

                            content += `
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between flex-wrap my-2" style="flex: 1; gap: 12px;">
                                            <div class="d-flex flex-column" style="gap:3px;">
                                                <div class="d-flex align-items-center mb-2" style="gap: 10px;">
                                                    <h6 class="n-exh-name mr-2">${taoh_desc_decode_new(v.exh_session_title)}</h6>`;

                            if (v.exh_raffles == '1') {
                                let raffle_start = new Date(v.exh_raffle_start_time);
                                let raffle_end = new Date(v.exh_raffle_stop_time);
                                if (v.exh_raffles_timebound_option == 0 || (v.exh_raffles_timebound_option == 1 && new Date() >= raffle_start && new Date() <= raffle_end)) {
                                    content += ` <a href="#" class="d-flex align-items-center">
                                                        <svg style="min-width: fit-content;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.44141 2.6875L8.80078 5H8.75H5.9375C5.07422 5 4.375 4.30078 4.375 3.4375C4.375 2.57422 5.07422 1.875 5.9375 1.875H6.02344C6.60547 1.875 7.14844 2.18359 7.44141 2.6875Z" fill="#FFC107"/>
                                                        </svg>
                                                    </a>
                                                `;
                                }
                            }

                            content += ` </div>
                                                <a href="${exhibitorWebsiteUrl != '' ? exhibitorWebsiteUrl : 'javascript:void(0)'}" class="site-link"
                                                target="_blank">${exhibitorWebsiteUrl != '' ? exhibitorWebsiteUrl : 'javascript:void(0)'}</a>

                                            </div>

                                               `;

                            content += `
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>`;

                            is_content = 1;
                        }
                    });
                }

                if (!is_content) {
                    $("#dashboard-tab,#dashboard-slash").hide();
                    $('#my_agenda_default_banner').show();
                    $('#dashboard_list').html('<div class="text-center"></div>');
                } else {
                    $("#dashboard-tab,#dashboard-slash").show();
                }

                $('#dashboard_list').html(content);
                loader(false, $("#dashboard_loaderArea"));
            });
    }


    $(document).on('click', '.dropdown-agenda', function (e) {
        e.preventDefault();

        const sel = $(this).data('target');
        if (!sel) return;
        $(sel).stop(true, true).slideToggle(150);
    });

    let skillShowMore = [];

    $(document).on('click', '.show-more', function () {
        let key = $(this).attr('data-id');
        skillShowMore.push(key);
        $('.more-content-' + key).removeClass('d-none');
        $('.less-content-' + key).addClass('d-none');
    });

    $(document).on('click', '.show-less', function () {
        let key = $(this).attr('data-id');
        skillShowMore = skillShowMore.filter(item => item !== key);
        $('.more-content-' + key).addClass('d-none');
        $('.less-content-' + key).removeClass('d-none');
    });

</script>
