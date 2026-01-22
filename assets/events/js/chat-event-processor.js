/**
 * Event Chat/Lobby Page - Event Processor
 * Extracted from app/events/chat.php
 *
 * This file handles the main event processing logic for the chat/lobby page.
 * All PHP variables are accessed via window.chatConfig
 */

(function($) {
    'use strict';

    var cfg = window.chatConfig || {};

    // ============================================
    // User Info Functions
    // ============================================

    async function getUserInfo(pToken_to, ops = 'public', serverFetch = false) {
        if (!pToken_to?.trim()) return null;

        let userInfo = {};

        if (!serverFetch) {
            if (!userInfo.ptoken && typeof IntaoDB !== 'undefined' && typeof objStores !== 'undefined') {
                const user_info_key = 'user_info_list';
                const intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                    let userInfoObj = intao_data.values[ops][pToken_to];
                    if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                        userInfo = userInfoObj;
                        $("#user_profile_type").val(userInfo.type);
                    }
                }
            }
        }

        if (!userInfo.ptoken && typeof fetchUserInfoFromServer === 'function') {
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

    // ============================================
    // Helper Functions
    // ============================================

    function hasVisibleText(html) {
        if (!html) return false;
        const decodedHtml = $('<div>').html(html).text();
        const $el = $('<div>').html(decodedHtml);
        $el.find('br, p:empty, p:has(br)').remove();
        const visibleText = $el.text().replace(/\s|&nbsp;/g, '');
        return visibleText.length > 0;
    }

    function generateAdditionalInfoTabsHtml(additionalInfo) {
        const keys = ['1', '2', '3'];
        let hasTabs = false;
        let isFirst = true;
        let tabsHtml = '';
        let contentHtml = '';

        keys.forEach(key => {
            const titleKey = `ad_info_title_${key}`;
            const contentKey = `ad_info_content_${key}`;

            if (additionalInfo[titleKey] && additionalInfo[contentKey]) {
                hasTabs = true;
                tabsHtml += `<li class="nav-item col-md">
                    <a class="nav-link ${isFirst ? 'active' : ''}" id="ad_info_${key}_tab"
                        data-toggle="tab" href="#ad_info_${key}" role="tab"
                        aria-controls="ad_info_${key}" aria-selected="${isFirst}">
                        ${typeof taoh_title_desc_decode === 'function' ? taoh_title_desc_decode(additionalInfo[titleKey]) : additionalInfo[titleKey]}</a>
                </li>`;

                contentHtml += `<div class="tab-pane fade show ${isFirst ? 'active' : ''}" id="ad_info_${key}" role="tabpanel" aria-labelledby="ad_info_${key}_tab">
                    <p>${typeof taoh_desc_decode === 'function' ? taoh_desc_decode(additionalInfo[contentKey]) : additionalInfo[contentKey]}</p>
                </div>`;

                isFirst = false;
            }
        });

        if (!hasTabs) return '';

        return `<div class="mt-5 pb-4 controlled-size">
            <ul class="nav nav-tabs row d-flex flex-wrap scroll-container" id="descriptionTabs" role="tablist" style="border-bottom: 1px solid #d3d3d3; gap: 1rem; overflow-x: auto;">
                ${tabsHtml}
            </ul>
            <div class="tab-content mt-4" id="descriptionTabsContent">
                ${contentHtml}
            </div>
        </div>`;
    }

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
        return videoSrc;
    }

    function eventVenueLoc(conttoken_data) {
        return (conttoken_data.map_link)
            ? `<a href="${conttoken_data.map_link}" target="_blank" class="cursor-pointer text-underline">${conttoken_data.venue}</a>`
            : conttoken_data.venue;
    }

    function checkButtonVisibility(conttoken_data) {
        let i_am_org = $("#is_organizer").val();
        let rsvp_sponsor_title = $("#rsvp_sponsor_title").val();
        const enable_hall = Number(conttoken_data?.enable_hall) === 1;
        const event_halls = Array.isArray(conttoken_data?.event_halls)
            ? conttoken_data.event_halls.filter(h => h?.id && h?.name)
            : [];
        let TicketArr = conttoken_data?.ticket_types?.find(ticket =>
            typeof taoh_title_desc_decode === 'function'
                ? taoh_title_desc_decode(ticket.title) === rsvp_sponsor_title
                : ticket.title === rsvp_sponsor_title
        ) || {};
        let event_form_version = conttoken_data?.event_form_version ?? 1;

        window.d_is_session_allowed = 0;
        window.d_is_exhibitor_setup = 0;

        $('#setup_exhibitor_slot').hide();
        $('#setup_speaker_slot').hide();
        $('#raffle_slot').hide();

        if (enable_hall) {
            let spkhallexist = event_halls.some(item => parseInt(item.accesslevel) === 1 || parseInt(item.accesslevel) === 3);
            let exhhallexist = event_halls.some(item => parseInt(item.accesslevel) === 2 || parseInt(item.accesslevel) === 3);

            if (spkhallexist) {
                if (i_am_org == 1) {
                    $('#setup_speaker_slot').show();
                    window.d_is_session_allowed = 1;
                } else if (TicketArr?.session_enable == 1 && TicketArr?.max_sessions_allowed > 0) {
                    const speakerCount = +$('#spk_count').val() || 0;
                    const maxSessionsAllowed = +TicketArr.max_sessions_allowed || 0;
                    if (maxSessionsAllowed && speakerCount < maxSessionsAllowed) {
                        $('#setup_speaker_slot').show();
                    }
                    window.d_is_session_allowed = 1;
                }
            }

            if (exhhallexist) {
                if (i_am_org == 1) {
                    $('#setup_exhibitor_slot').show();
                    window.d_is_exhibitor_allowed = 1;
                } else if (TicketArr?.exhibit_enable == 1 && TicketArr?.max_exhibits_allowed > 0) {
                    const exhibitCount = +$('#exh_count').val() || 0;
                    const maxExhibitAllowed = +TicketArr.max_exhibits_allowed || 0;
                    if (maxExhibitAllowed && exhibitCount < maxExhibitAllowed) {
                        $('#setup_exhibitor_slot').show();
                    }
                    window.d_is_exhibitor_allowed = 1;
                }
            }
        }

        if (i_am_org == 1) {
            $('#raffle_slot').show();
        } else {
            var sponsor_type = $('#sponsor_type').val();
            if (sponsor_type != "") {
                if (event_form_version == 2) {
                    if (TicketArr?.exhibitor_raffle == 1) {
                        $('#raffle_slot').show();
                    }
                } else {
                    $.each(conttoken_data.event_sponsor_levels, function (sk, sponsors) {
                        if (sponsor_type == sponsors.title) {
                            if (sponsors.raffle == 1) {
                                $('#raffle_slot').show();
                            }
                        }
                    });
                }
            }
        }
    }

    // ============================================
    // Main Event Processing Function
    // ============================================

    async function processEventBaseInfo(requestData, response) {
        let event_output = response.output;
        let event_owner = event_output.ptoken;
        let conttoken_data = event_output.conttoken;
        const enable_hall = Number(conttoken_data?.enable_hall) === 1;
        var event_country_name = '';

        // Event Sponsor
        if (typeof getEventSponsor === 'function') {
            await getEventSponsor(event_output.eventtoken);
        }

        if (conttoken_data.full_location != '' && conttoken_data.full_location != undefined) {
            let event_country = conttoken_data.full_location;
            let evet_country_array = event_country.split(',');
            event_country_name = evet_country_array[evet_country_array.length - 1].trim();
        }

        let country_locked = (conttoken_data.country_locked != '' && conttoken_data.country_locked != undefined)
            ? conttoken_data.country_locked : 0;

        $('#event_country_lock').val(country_locked);
        $('#event_country_name').val(event_country_name);

        if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) {
            let org_email = $.trim(conttoken_data.org_email);
            $("#exhibitor_contactus").attr('href', "mailto:" + org_email);
            $("#speaker_contactus").attr('href', "mailto:" + org_email);
            $("#agenda_contactus").attr('href', "mailto:" + org_email);
        }

        $("#enable_exhibitor_hall").val(conttoken_data.enable_exhibitor_hall);
        $("#enable_speaker_hall").val(conttoken_data.enable_speaker_hall);
        $("#enable_hall").val(conttoken_data.enable_hall);

        if (enable_hall) {
            $('#exhibitor_top').show();
            $('#speaker_top').show();
        } else {
            $('#exhibitor_top').remove();
            $('#exhibitor_desc').remove();
            $('#speaker_top').remove();
            $('#speaker_desc').remove();
        }

        let event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
            .split(',')
            .map(token => token.trim())
            .filter(token => token);

        if (event_owner) event_organizer_ptokens.push(event_owner);
        let event_instance_owner = conttoken_data.ptoken;
        event_organizer_ptokens.push(event_instance_owner);

        var superorganizer_token = $('#superorganizer_token').val();
        event_organizer_ptokens.push(superorganizer_token);

        if (event_organizer_ptokens.includes(cfg.ptoken)) {
            $('#download_rsvp').show();
            $('#email_rsvp').show();
        } else {
            $('#download_rsvp').hide();
            $('#email_rsvp').hide();
        }

        if (event_organizer_ptokens.includes(cfg.ptoken)) {
            $("#is_organizer").val(1);
        } else {
            $("#is_organizer").val(0);
        }

        const event_type = (conttoken_data?.event_type || 'virtual').toLowerCase();
        const is_event_suspended = parseInt(event_output?.status) === 2;
        const is_event_freeze = parseInt(conttoken_data?.freeze_option) === 1;

        $('.event_title').text(conttoken_data?.title);

        // Event Banner
        const allEventBannersArray = [conttoken_data.event_video, conttoken_data.event_image].concat((conttoken_data.more_banner || []));
        const eventBannersArray = allEventBannersArray.filter(url => url && url.trim() !== "" && typeof isValidURL === 'function' && isValidURL(url)).map(url => ({
            src: url,
            type: typeof getMediaType === 'function' ? getMediaType(url) : 'image'
        }));

        const galleryContainer = document.getElementById("event_banner_container");
        if (galleryContainer) {
            const mainDisplay = document.createElement("div");
            mainDisplay.id = "event_banner_image";
            galleryContainer.before(mainDisplay);

            function displayMedia(media) {
                if (!media) return;
                mainDisplay.innerHTML = "";
                let mediaHtml = "";

                if (media.type === "image") {
                    mediaHtml = `
                        <div class="cover-event-image">
                            <div class="events-bg" style="background-image: url('${media.src}');"></div>
                            <div class="glass-overlay"></div>
                            <img src="${media.src}" class="main-image" alt="Event" style="max-width: 173px; min-width: 173px; max-height: 136px; min-height: 136px; border: 1px solid #d3d3d3;">
                        </div>
                    `;
                } else if (media.type === "video") {
                    let videoSrc = formatVideoSrc(media.src);
                    mediaHtml = `<iframe src="${videoSrc}" class="main-media" allowfullscreen allow="autoplay" style="max-width: 173px; min-width: 173px; max-height: 136px; min-height: 136px; border: 0;"></iframe>`;
                }

                mainDisplay.innerHTML = mediaHtml;
            }

            // Generate gallery items
            eventBannersArray.forEach((media, index) => {
                let itemHtml = "";
                if (media.type === "image") {
                    itemHtml = `<div class="item" style="--background-src: url('${media.src}');"><img src="${media.src}" class="thumbnail" data-index="${index}" alt="Gallery Image ${index + 1}"></div>`;
                } else if (media.type === "video") {
                    let thumbnailSrc = "";
                    if (media.src.includes("youtube.com") || media.src.includes("youtu.be")) {
                        thumbnailSrc = typeof getYouTubeThumbnail === 'function' ? getYouTubeThumbnail(media.src) : "";
                    }
                    itemHtml = `<div class="item" style="--background-src: url('${thumbnailSrc}');"><img src="${thumbnailSrc}" class="thumbnail video-thumbnail" data-index="${index}" data-src="${media.src}" alt="Video Thumbnail ${index + 1}"></div>`;
                }
                galleryContainer.innerHTML += itemHtml;
            });

            // Show first media
            if (eventBannersArray.length > 0) {
                displayMedia(eventBannersArray[0]);
            }

            // Click handler for gallery
            $(galleryContainer).on("click", ".thumbnail", function () {
                const index = $(this).data("index");
                displayMedia(eventBannersArray[index]);
            });
        }

        // Event Description
        let eventDescription = conttoken_data?.description || '';
        if (hasVisibleText(eventDescription)) {
            $('#event_desc_section').show();
            $('#event_description').html(typeof taoh_desc_decode === 'function' ? taoh_desc_decode(eventDescription) : eventDescription);
        }

        // Additional Info Tabs
        let additionalInfoHtml = generateAdditionalInfoTabsHtml(conttoken_data);
        if (additionalInfoHtml) {
            $('#additional_info_section').html(additionalInfoHtml);
        }

        // User timezone handling
        let user_timezone;
        if (cfg.isLoggedIn) {
            user_timezone = cfg.userTimezone;
        }
        if (!cfg.isLoggedIn || !user_timezone?.trim()) {
            let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = typeof convertDeprecatedTimeZone === 'function'
                ? convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone)
                : (clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (typeof isValidTimezone === 'function' && !isValidTimezone(user_timezone)) user_timezone = 'UTC';

        let event_live_state = typeof eventLiveState === 'function'
            ? eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone)
            : 'before';
        $("#event_live_state").val(event_live_state);

        // Upgrade modal
        if (cfg.isLoggedIn && !is_event_suspended && !is_event_freeze && event_live_state === 'before') {
            if (typeof constructUpgradeModalContent === 'function') {
                constructUpgradeModalContent(event_output, cfg.ptoken, cfg.rsvpSlug, cfg.isLoggedIn);
            }
            $('.attendee_tagline').css('display', 'flex');
        } else {
            $('.upgrade_modal_btn_wrapper').hide();
        }

        // Ticket Types
        let eventTicketTypesHtml = '';
        let is_user_rsvp_done = cfg.isUserRsvpDone;

        if (cfg.isLoggedIn) {
            if (is_user_rsvp_done && event_live_state == 'live') {
                eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Event Status</h3>';
            } else if (is_user_rsvp_done) {
                eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Registration Status</h3>';
            } else {
                if (event_live_state == 'before' || event_live_state == 'live') {
                    eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Choose a ticket to Register !</h3>';
                } else if (event_live_state == 'after') {
                    eventTicketTypesHtml += '<h3 class="mb-4 ticket-card-title">Thank You !</h3>';
                }
            }

            if (!is_user_rsvp_done && (event_live_state === 'before' || event_live_state === 'live')) {
                eventTicketTypesHtml += '<ul class="ticket-list w-100">';
                const ticket_types = conttoken_data.ticket_types;
                let ticket_type_selected = false;
                ticket_types.forEach(ticket_type => {
                    const ticket_type_slug = ticket_type.slug;
                    const ticket_type_title = ticket_type.title;
                    const ticket_type_cost = ticket_type.cost;

                    eventTicketTypesHtml += '<li>';
                    eventTicketTypesHtml += '<input type="radio" name="ticket" id="' + ticket_type_slug + '" value="' + encodeURIComponent(ticket_type_title) + '" class="d-none"';
                    if (is_user_rsvp_done) {
                        eventTicketTypesHtml += ' disabled';
                        if (cfg.rsvpSlug === ticket_type_slug) {
                            eventTicketTypesHtml += ' checked';
                            ticket_type_selected = true;
                        }
                    }
                    if (!ticket_type_selected && !is_user_rsvp_done) {
                        eventTicketTypesHtml += ' checked';
                        ticket_type_selected = true;
                    }
                    eventTicketTypesHtml += '>';
                    eventTicketTypesHtml += '<label for="' + ticket_type_slug + '" class="item btn w-100">';
                    eventTicketTypesHtml += '<p class="item-title">' + ticket_type_title + '</p>';
                    eventTicketTypesHtml += '<p class="item-cost">' + (ticket_type.price === 'paid' ? 'Costs you $' + ticket_type_cost : 'Free') + '</p>';
                    eventTicketTypesHtml += '</label>';
                    eventTicketTypesHtml += '</li>';
                });
                eventTicketTypesHtml += '</ul>';
            }

            $('#myTab').removeClass('event-live-before event-live-live event-live-end');

            if (event_live_state === 'after') {
                $('#myTab').addClass('event-live-end');
                eventTicketTypesHtml += `
                    <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>
                    <a href="${cfg.appUrl}" class="mt-4 btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>`;
            } else {
                if (is_event_freeze || is_event_suspended) {
                    $('#myTab').addClass('event-live-end');
                    eventTicketTypesHtml += `
                        <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>
                        <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                        <a href="${cfg.appUrl}" class="mt-4 btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i>Event Suspended</a>`;
                } else {
                    if (is_user_rsvp_done && event_live_state === 'before') {
                        $('#myTab').addClass('event-live-before');
                        eventTicketTypesHtml += `
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="1"/>`;
                    } else if (!is_user_rsvp_done && event_live_state === 'before') {
                        $('#myTab').addClass('event-live-before');
                        eventTicketTypesHtml += `
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>
                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="0"/>`;
                    } else if (is_user_rsvp_done && event_live_state === 'live') {
                        $('#myTab').addClass('event-live-on');
                        eventTicketTypesHtml += `
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                            <a href="${cfg.appUrl}/chat/id/events/${cfg.eventToken}" class="mt-4 btn btn-success w-100 metrics_action" data-metrics="join_event">
                                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>
                                Event Live, ${!cfg.isValidUser ? 'Complete settings to' : 'Click to'} Join</a>`;
                    } else if (is_user_rsvp_done) {
                        $('#myTab').addClass('event-live-on');
                        eventTicketTypesHtml += `
                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                            <a href="${cfg.appUrl}/chat/id/events/${cfg.eventToken}" class="mt-4 btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>`;
                    } else {
                        $('#myTab').addClass('event-live-before');
                        eventTicketTypesHtml += `
                            <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>
                            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>`;
                    }
                }
            }
        } else {
            eventTicketTypesHtml = '<a aria-pressed="true" data-toggle="modal" data-target="#config-modal" data-metrics="rsvp" class="login-button btn btn-primary w-100">Login & Register Now</a>';
        }

        $('#ticket-card').html(eventTicketTypesHtml);

        // Event date/time display
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

        let localized_event_start_data = typeof get_localized_event_data === 'function'
            ? get_localized_event_data(event_timestamp_start_data, user_timezone) : {};
        let localized_event_ends_data = typeof get_localized_event_data === 'function'
            ? get_localized_event_data(event_timestamp_end_data, user_timezone) : {};

        const event_info_container = $('#event_info_container');
        if (typeof formatEventDateTime === 'function') {
            event_info_container.find('#event_start_end_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));
        }

        $('#event_start_at').val(event_output.local_start_at);
        $('#event_end_at').val(event_output.local_end_at);
        $('#event_timezone').val(event_output?.local_timezone);
        $('#event_locality').val(event_output?.locality ?? '');

        // Event Venue Info
        let eventVenueInfoHtml = '';
        if (event_type === 'in-person') {
            eventVenueInfoHtml += `<span class="theme-blue-clr">In-Person, <span>${eventVenueLoc(conttoken_data)}</span></span>`;
        } else if (event_type === 'hybrid') {
            eventVenueInfoHtml += `<span class="theme-blue-clr">Hybrid - <span>${eventVenueLoc(conttoken_data)}</span> or Virtual</span>`;
        } else if (event_type === 'virtual') {
            eventVenueInfoHtml += `<span class="theme-blue-clr">Virtual</span>`;
        }
        $('#event_venue_info').html(eventVenueInfoHtml);

        // Button visibility
        setTimeout(() => {
            checkButtonVisibility(conttoken_data);
        }, 5000);

        // Event Sponsor popup
        let eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
        let eventTicketType = conttoken_data.ticket_types || {};
        let eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(widget => widget.quantity > 0 ? 1 : 0);
        var event_form_version = conttoken_data.event_form_version ?? 1;
        let is_social_share_enabled = conttoken_data.event_social_sharing;

        if (typeof constructSponsorInfoPopup === 'function') {
            constructSponsorInfoPopup(
                event_output.eventtoken,
                eventSponsorWidgetType,
                cfg.userProfileType,
                conttoken_data.org_email,
                cfg.socialToken,
                eventTicketType,
                event_form_version,
                is_social_share_enabled,
                cfg.trackingtoken,
                cfg.isLoggedIn
            );
        }

        setTimeout(() => {
            if (typeof getEventMetaInfo === 'function') {
                getEventMetaInfo(event_output.eventtoken).then(() => {
                    if (typeof getEventsHall === 'function') {
                        getEventsHall(event_output.eventtoken);
                    }
                });
            }
        }, 3000);

        setTimeout(() => {
            if (typeof eventCheckinList === 'function') {
                eventCheckinList(event_output.eventtoken, '', 1);
            }

            var event_status = $('#event_status_hidden').val();
            if (event_status == 2 && eventSponsorWidgetTypeStatusList.includes(1)) {
                $('.event_sponsor_right_header').show();
                $('#sponsor_card').show();
                $('.get-started').show();
            } else {
                $('.event_sponsor_right_header').hide();
                $('.get-started').hide();
            }

            var superorganizer_token = $('#superorganizer_token').val();
            if (event_status == 1 || cfg.ptoken == superorganizer_token) {
                $('.speaker-banner').hide();
                $('.exhibitor-banner').hide();
            } else {
                $('.speaker-banner').show();
                $('.exhibitor-banner').show();
                $('.rsvp_actions').css('display', 'none');
                $("#rsvp_default_list").show();

                if ($("#is_organizer").val() == 1) {
                    $('.rsvp_actions').show();
                }

                if (is_user_rsvp_done) {
                    $("#register_now").hide();
                }

                if (typeof loader === 'function') {
                    loader(false, $("#rsvpdir_loaderArea"));
                }
            }

            if ($("#is_organizer").val() == 1) {
                $('#networking_link').show();
            } else {
                $('#networking_link').hide();
            }

            if (conttoken_data.table_discussion != '' && conttoken_data.table_discussion != undefined && conttoken_data.table_discussion == 1) {
                $('#tables_top').show();
            }

            if (conttoken_data.comments != '' && conttoken_data.comments != undefined && conttoken_data.comments == 1) {
                $('#comments_top').show();
            }
        }, 3000);

        // Organizer video modal
        if (typeof constructOrganizerVideoModalContent === 'function') {
            constructOrganizerVideoModalContent(event_output, user_timezone);
        }

        // Show Event Upgrade
        if (cfg.isLoggedIn && cfg.showUpgrade) {
            if (is_event_suspended || is_event_freeze) {
                if (typeof taoh_set_info_message === 'function') {
                    taoh_set_info_message('Event upgrade is not available for suspended or frozen events.', false, 'toast-middle', [
                        { text: 'OK', action: () => {}, class: 'dojo-v1-btn float-right mt-3 mb-3' }
                    ]);
                }
            } else {
                if (event_live_state === 'before') {
                    if ($('#upgrade_modal_btn').length) $('#upgrade_modal_btn').trigger('click');
                } else {
                    if (typeof taoh_set_info_message === 'function') {
                        taoh_set_info_message('Event upgrade is only available before the event goes live.', false, 'toast-middle', [
                            { text: 'OK', action: () => {}, class: 'dojo-v1-btn float-right mt-3 mb-3' }
                        ]);
                    }
                }
            }
        }

        // Hide loader
        $('.aw').awloader('hide');
    }

    // ============================================
    // Initialize on Document Ready
    // ============================================

    $(document).ready(function() {
        // Fetch and process event info
        if (typeof getEventBaseInfo === 'function') {
            getEventBaseInfo({ eventtoken: cfg.eventToken }, true)
                .then(({ requestData, response }) => processEventBaseInfo(requestData, response))
                .catch(error => console.error("Error fetching event info:", error));
        }

        // Contact host form submission
        $(document).on('click', '#sendMail', async function (e) {
            e.preventDefault();
            let contact_info = event_arr?.conttoken || {};
            let event_userinfo = await getUserInfo(contact_info.ptoken, 'notify');
            let to_email = '';

            if (event_userinfo?.email?.trim()) {
                to_email = event_userinfo.email.trim();
            } else {
                if (contact_info?.email?.trim()) {
                    to_email = contact_info.email.trim();
                } else {
                    to_email = 'info@noworkerleftbehind.org';
                }
            }

            if ($("#contacthostForm").valid()) {
                const formData = new FormData($("#contacthostForm")[0]);
                formData.append('taoh_action', 'taoh_contact_host');
                formData.append('eventtoken', cfg.eventToken);
                formData.append('to_email', to_email);

                let submit_btn = $(this);
                submit_btn.prop('disabled', true);

                $.ajax({
                    url: cfg.ajaxUrl,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (response) {
                        if (response.success) {
                            $("#contacthostModal").modal("hide");
                            document.getElementById("contacthostForm").reset();
                            submit_btn.prop('disabled', false);
                            if (typeof taoh_set_success_message === 'function') {
                                taoh_set_success_message('Thanks! Mail sent successfully.', false);
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

        // External links display
        let conttoken_data = cfg.eventArr?.conttoken || {};
        if (Array.isArray(conttoken_data?.link) && conttoken_data.link.length > 0) {
            const filteredEventLinks = conttoken_data.link.filter(link => link.label?.trim());

            if (filteredEventLinks.length > 0) {
                let eventLinksHtml = `<ul class="list-group list-group-flush">
                    ${filteredEventLinks.map(link => {
                        const url = link.value?.trim();
                        return `<li class="list-group-item p-0">
                            ${url && /^https?:\/\/\S+$/.test(url)
                                ? `<a href="${url}" target="_blank">${link.label}</a>`
                                : link.label}
                        </li>`;
                    }).join("")}
                </ul>`;

                if (filteredEventLinks.length > 1) {
                    eventLinksHtml += `<button id="toggleBtn" class="toggle-btn">Show More</button>`;
                }

                $('#event_links_blk').html(eventLinksHtml);

                if (filteredEventLinks.length > 1) {
                    $('#toggleBtn').click(function () {
                        const isExpanded = $(this).text() === "Show Less";
                        $(this).text(isExpanded ? "Show More" : "Show Less");
                        $('#event_links_blk li:not(:first-child)').each(function () {
                            $(this).toggleClass('show');
                        });
                    });
                }

                $('#external_links_blk').show();
            }
        }
    });

    // Export functions for external use
    window.chatEventProcessor = {
        getUserInfo: getUserInfo,
        processEventBaseInfo: processEventBaseInfo,
        checkButtonVisibility: checkButtonVisibility,
        hasVisibleText: hasVisibleText,
        generateAdditionalInfoTabsHtml: generateAdditionalInfoTabsHtml
    };

})(jQuery);
