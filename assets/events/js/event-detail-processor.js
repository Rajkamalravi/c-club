/**
 * Event Detail Page - Processor
 * Extracted from app/events/events_detail.php
 *
 * All PHP variables are accessed via window.eventDetailConfig (cfg).
 * Legacy global aliases (isLoggedIn, eventToken, etc.) are set in the
 * inline <script> block that loads before this file.
 */

(function($) {
    'use strict';

    var cfg = window.eventDetailConfig || {};

    // ============================================
    // User Info Function
    // ============================================

    async function getUserInfo(pToken_to, ops, serverFetch) {
        ops = ops || 'public';
        serverFetch = serverFetch || false;

        if (!pToken_to?.trim()) return null;

        var userInfo = {};

        if (!serverFetch) {
            // Try to get userInfo from IndexedDB
            if (!userInfo.ptoken) {
                var user_info_key = 'user_info_list';
                var intao_data = await IntaoDB.getItem(objStores.common_store.name, user_info_key);
                if (intao_data?.values && intao_data.values[ops] && intao_data.values[ops][pToken_to]) {
                    var userInfoObj = intao_data.values[ops][pToken_to];
                    // Check if data is expired (expires after 2 days)
                    if (userInfoObj.last_fetch_time && (Date.now() - userInfoObj.last_fetch_time) <= 172800000) {
                        userInfo = userInfoObj;
                    }
                }
            }
        }

        // Fetch userInfo from server if not found locally
        if (!userInfo.ptoken) {
            var formData = {
                taoh_action: 'taoh_user_info',
                ops: ops,
                ptoken: pToken_to
            };

            try {
                var srv_userInfoObj = await fetchUserInfoFromServer(formData);
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

    // ============================================
    // Helper Functions
    // ============================================

    function formatVideoSrc(videoSrc) {
        if (videoSrc.includes("youtube.com")) {
            return 'https://www.youtube.com/embed/' + (videoSrc.split("v=")[1]?.split("&")[0]);
        }
        if (videoSrc.includes("youtu.be")) {
            var videoId = videoSrc.split("youtu.be/")[1];
            return 'https://www.youtube.com/embed/' + videoId;
        }
        if (videoSrc.includes("vimeo.com")) {
            return 'https://player.vimeo.com/video/' + videoSrc.split("vimeo.com/")[1];
        }
        return videoSrc;
    }

    function eventVenueLoc(conttoken_data) {
        return (conttoken_data.map_link)
            ? '<a href="' + conttoken_data.map_link + '" target="_blank" class="cursor-pointer text-underline">' + conttoken_data.venue + '</a>'
            : conttoken_data.venue;
    }

    function eventJoinHere() {
        return '';
    }

    function delete_events_into(find_key) {
        getIntaoDb(dbName).then(function(db) {
            var dataStoreName = EVENTStore;
            var transaction = db.transaction(dataStoreName, 'readwrite');
            var objectStore = transaction.objectStore(dataStoreName);
            var request = objectStore.openCursor();
            request.onsuccess = function(event) {
                var cursor = event.target.result;
                if (cursor) {
                    var index_key = cursor.primaryKey;
                    if (index_key.includes(find_key)) {
                        objectStore.delete(index_key);
                    }
                    cursor.continue();
                }
            };
        }).catch(function() {
            console.log('Error in deleting data store');
        });
    }

    // Make delete_events_into available globally (used by processEventBaseInfo)
    window.delete_events_into = delete_events_into;

    function updateraffle() {
        if ($("input[name='exh_raffles']:checked").val() == '1') {
            $('#exh_raffle_options').show();
        } else {
            $('#exh_raffles_timebound_no').prop('checked', true).trigger('change');
            $('#exh_raffle_time_bound_time').hide();
            $('#exh_raffle_options').hide();
        }
    }
    window.updateraffle = updateraffle;

    function updateraffletimebound() {
        if ($("input[name='exh_raffles_timebound_option']:checked").val() == '1') {
            $('#exh_raffle_time_bound_time').show();
        } else {
            $('#exh_raffle_time_bound_time').hide();
        }
    }
    window.updateraffletimebound = updateraffletimebound;

    // ============================================
    // Render: Event Banner Gallery
    // ============================================

    function renderEventBanner(conttoken_data, event_output) {
        if (conttoken_data.event_video == undefined || conttoken_data.event_video == null) {
            conttoken_data.event_video = '';
        }

        var allEventBannersArray = [conttoken_data.event_video, conttoken_data.event_image].concat((conttoken_data.more_banner || []));
        var eventBannersArray = allEventBannersArray.filter(function(url) {
            return url.trim() !== "" && isValidURL(url);
        }).map(function(url) {
            return { src: url, type: getMediaType(url) };
        });

        var galleryContainer = document.getElementById("event_banner_container");
        var noImage = _taoh_site_url_root + '/assets/images/event.jpg';

        if (eventBannersArray[0]) {
            eventBannersArray.forEach(function(media, index) {
                var itemHtml = "";
                if (media.type === "image") {
                    itemHtml = '<div class="carousel-item ' + (index === 0 ? 'active' : '') + '">' +
                        '<div class="cover-event-image">' +
                        '<div class="events-bg" style="background-image: url(\'' + media.src + '\');"></div>' +
                        '<div class="glass-overlay"></div>' +
                        '<img src="' + media.src + '" class="detail-main-image" alt="Event">' +
                        '</div></div>';
                } else if (media.type === "video") {
                    var videoSrc = formatVideoSrc(media.src);
                    itemHtml = '<div class="carousel-item ' + (index === 0 ? 'active' : '') + '">' +
                        '<iframe src="' + videoSrc + '" class="main-media" allowfullscreen allow="autoplay"></iframe>' +
                        '</div>';
                }
                galleryContainer.innerHTML += itemHtml;
            });
        } else {
            var itemHtml = '<div class="carousel-item active">' +
                '<div class="cover-event-image">' +
                '<div class="events-bg" style="background-image: url(\'' + noImage + '\');"></div>' +
                '<div class="glass-overlay"></div>' +
                '<img src="' + noImage + '" class="detail-main-image" alt="Event">' +
                '</div></div>';
            galleryContainer.innerHTML += itemHtml;
        }

        return eventBannersArray;
    }

    // ============================================
    // Render: Like/Save Button
    // ============================================

    function renderLikeButton(event_output, conttoken_data) {
        var fillColor = (cfg.userlikedAlready == 1) ? '#000000' : '';
        var cssClass  = (cfg.userlikedAlready == 1) ? 'event_saved' : 'event_save';

        $('#event_like_btn').html(
            '<svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" ' +
            'data-event="' + event_output.eventtoken + '" data-cont="' + conttoken_data.conttoken + '" ' +
            'class="' + cssClass + '" title="Save Event">' +
            '<path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" ' +
            'fill="' + fillColor + '" stroke="white"/></svg>'
        );
    }

    // ============================================
    // Render: Event Description
    // ============================================

    function renderEventDescription(conttoken_data) {
        var html = '';
        if (conttoken_data.description && $.trim(conttoken_data.description) != '') {
            html += '<h3>About this Event</h3>';
            html += '<div>' + taoh_desc_decode(conttoken_data.description) + '</div>';
        }
        if (conttoken_data.about_you && $.trim(conttoken_data.about_you) != '') {
            html += '<h3>About the Host</h3>';
            html += '<div>' + taoh_desc_decode(conttoken_data.about_you) + '</div>';
        }
        $('.event_description').html(html);
    }

    // ============================================
    // Render: Ticket Types
    // ============================================

    function renderTicketTypes(conttoken_data, event_output, event_type, is_event_suspended, is_event_freeze, event_live_state) {
        var html = '';
        var redirect_to = !cfg.isValidUser
            ? _taoh_site_url_root + '/settings'
            : cfg.appUrl + '/chat/id/events/' + cfg.eventToken;

        if (cfg.isLoggedIn) {
            html += '<a href="' + cfg.adopterUrl + '" id="networking_link" style="display:none;" class="btn btn-success">Go to Networking Room</a>';
            if (!cfg.isUserRsvpDone && !is_event_suspended && !is_event_freeze && (event_live_state === 'before' || event_live_state === 'live')) {
                html += '<div class="dropdown w-100">';
                html += '<button class="btn ' + (event_live_state === 'live' ? 'btn-success' : 'btn-primary') + ' dropdown-toggle w-100" type="button" id="choose_ticket" data-ticket_selected="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display:none">' + (event_live_state === 'live' ? 'LIVE NOW!' : '') + ' Choose a ticket to Register</button>';
                html += '<ul class="ticket-list w-100 dropdown-menu px-3 light-dark" id="ticket_list" aria-labelledby="choose_ticket" style="z-index: 9999;">';

                var ticket_types = conttoken_data.ticket_types;
                var ticket_type_selected = false;

                ticket_types.forEach(function(ticket_type) {
                    if (ticket_type.visibility === 'hidden') return;

                    var applicable = ticket_type.applicable_to || [];
                    var hasAll = applicable.includes('all');
                    if (!hasAll && !applicable.includes(cfg.userProfileType)) return;

                    var ticket_type_slug = ticket_type.slug;
                    var ticket_type_title = ticket_type.title;
                    var ticket_type_cost = ticket_type.cost;
                    var classes = 'rsvp_ticket_' + ticket_type_title + 'rsvp_tickets ticket-item';

                    html += '<li class="' + classes + '">';
                    html += '<input type="radio" name="ticket" id="' + ticket_type_slug + '" value="' + encodeURIComponent(ticket_type_title) + '" class="rsvp_ticket_' + ticket_type_title + ' rsvp_tickets d-none"';

                    if (cfg.isUserRsvpDone) {
                        html += ' disabled ';
                        if (cfg.rsvpSlug === ticket_type_slug) {
                            html += ' checked';
                            ticket_type_selected = true;
                        }
                    }

                    html += '>';
                    html += '<label for="' + ticket_type_slug + '" class="item btn w-100">';
                    html += '<p class="item-title">' + ticket_type_title + '</p>';
                    html += '<p class="item-cost">' + (ticket_type.price === 'paid' ? 'Costs you $' + ticket_type_cost : 'Free') + '</p>';
                    html += '</label>';
                    html += '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }

            if (is_event_freeze || is_event_suspended) {
                html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>' +
                    '<a href="' + cfg.appUrl + '" class="btn btn-secondary w-100"><i class="fa fa-calendar-times mr-2" aria-hidden="true"></i>Event Suspended</a>';
            } else {
                if (event_live_state === 'after') {
                    html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>' +
                        '<a href="' + cfg.appUrl + '" class="btn btn-secondary w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended</a>';
                } else {
                    if (cfg.isUserRsvpDone && event_live_state === 'before') {
                        html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>' +
                            '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="1"/>' +
                            '<a href="' + cfg.appUrl + '/chat/id/events/' + cfg.eventToken + '" class="btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>';
                    } else if (!cfg.isUserRsvpDone && event_live_state === 'before') {
                        html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="before" value="2"/>' +
                            '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="0"/>';
                    } else if (cfg.isUserRsvpDone && event_live_state === 'live') {
                        html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>' +
                            '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>' +
                            '<a href="' + redirect_to + '" class="btn live w-100">' +
                            '<i class="fa fa-ticket mr-2" aria-hidden="true"></i>Event Live, ' + (!cfg.isValidUser ? 'Complete settings to' : 'Click to') + ' Join</a>';
                    } else if (!cfg.isUserRsvpDone && event_live_state === 'live') {
                        html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>' +
                            '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>';
                    } else if (cfg.isUserRsvpDone) {
                        html += '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>' +
                            '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>' +
                            '<a href="' + cfg.appUrl + '/chat/id/events/' + cfg.eventToken + '" class="btn btn-warning w-100"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered</a>';
                    } else {
                        html += '<input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>' +
                            '<input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="0"/>';
                    }
                }
            }
        } else {
            html = '<button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" data-location="' + location.href + '" data-title="' + btoa(unescape(encodeURIComponent(conttoken_data.title))) + '" data-toggle="modal" data-target="#config-modal"><i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now</button>';
        }

        html += '<button id="sponsor_card1" style="display: none;border:1.2px solid rgba(255, 193, 7, 0.8);border-radius:6px;max-width: none;" type="button" class="btn w-100 event_sponsor_right_header sponsor-card mx-auto sponsor-btn" data-toggle="modal" data-target="#sponsorInfo">' +
            '<svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 113.8" style="width: 29px;margin-right: 5px;fill: black;"><defs><style>.cls-1{fill-rule:evenodd;}</style></defs><title>loudspeake</title><path class="cls-1" d="M0,67.6c-.06-5.83,2.16-11,7.37-12.69l.13-.22a2.91,2.91,0,0,1,.6-.8,3.1,3.1,0,0,1,1.05-.5l.29-.11C34.7,44,46.09,30.07,52.64,20.61c0,9.29,2.33,21.4,6.43,33.12,4.21,12,10.29,23.71,17.71,31.76h0c-7.73-2.2-17.43-5-33.21-2.85a15.17,15.17,0,0,0-1.12,5.06,3.9,3.9,0,0,0,1,3l.24.22c1,.91,1.49,1.36,1.71,2.17a9.17,9.17,0,0,1-.13,3.22l-.09.67c-.37,2.93,1.05,3.53,2.46,4.13s2.51,1.08,3.08,2.69a.68.68,0,0,1,0,.4c-.58,3-2.5,4.64-4.43,6.29-.47.41-.95.82-1.36,1.21l-.06,0c-3.92,3.14-6.57,2.3-8.58-.22-1.58-2-2.68-5-3.7-7.81l-.44-1.22a122.88,122.88,0,0,1-4-15.09l-.34-1.5c-.87.24-1.78.51-2.73.79h0c-1.2.35-2.4.73-3.59,1.11l-.38.14a1.21,1.21,0,0,1-1.33,0C13.47,90,8.2,87,4.68,82a25,25,0,0,1-3.41-6.9A25.74,25.74,0,0,1,0,67.6ZM87.81,16.16a3,3,0,0,1-4.23-.6l-.07-.09a3.05,3.05,0,0,1,.63-4.19C89,7.69,93.84,4.07,98.7.56A3.09,3.09,0,0,1,100.92,0a3.06,3.06,0,0,1,2,1.21l0,.05a3,3,0,0,1,.52,2.23,3,3,0,0,1-1.21,2L87.81,16.16Zm15.79,57.7h0a3,3,0,0,1-2.11-.93,3,3,0,0,1-.85-2.14v-.07a3,3,0,0,1,3.07-3c5.37,0,10.83.2,16.2.3a3.07,3.07,0,0,1,3,3.1,3.09,3.09,0,0,1-.93,2.15,3,3,0,0,1-2.16.86l-16.19-.31Zm.47-15.71a3,3,0,0,1-3.25-2.78V55.3a3.07,3.07,0,0,1,2.77-3.23c5-.4,10.09-.83,15.1-1.15a3.06,3.06,0,0,1,3.24,2.81h0a3,3,0,0,1-.73,2.21A3.08,3.08,0,0,1,119.12,57c-4.85.48-10.18.88-15.05,1.14Zm-2.16-14.59a3.06,3.06,0,0,1-3.78-2.11v0a3,3,0,0,1,.27-2.3,3.1,3.1,0,0,1,1.83-1.46c5.21-1.37,10.4-3.05,15.61-4.45a3,3,0,0,1,2.3.27,3.08,3.08,0,0,1,1.45,1.8v0a3.07,3.07,0,0,1-2.1,3.78l-15.59,4.45Zm-6.3-14.32a3.05,3.05,0,0,1-4.09-1.37,3,3,0,0,1-.17-2.33,3.09,3.09,0,0,1,1.53-1.77l15-7.49a3,3,0,0,1,4.09,1.37,3,3,0,0,1,.16,2.33,3.07,3.07,0,0,1-1.53,1.76l-15,7.5ZM58.75,12c.13-.15.27-.29.4-.42a4.46,4.46,0,0,1,1.48-1.11h0a3.58,3.58,0,0,1,2-.08c2.25.36,4.66,2,7.11,4.51,6,6.19,12.32,18,16.82,30.41s7.2,25.36,5.85,33.77c-.51,3.13-1.57,5.66-3.31,7.33l-.09.07a5.64,5.64,0,0,1-2.47,1,2.43,2.43,0,0,0-.25-.2C80.3,83,75,75.9,70.54,67.55c1.14.22,2.6-.63,4-2.08,7-7.38,3.56-20.28-5.17-23.27C65.81,41,62.27,41,60.87,41.69l-.15.08C57.52,29.42,56.61,18,58.75,12Z"/></svg>' +
            '<svg id="Layer_2" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 113.8" style="width: 29px;margin-right: 5px;fill: white;"><defs><style>.cls-1{fill-rule:evenodd;}</style></defs><title>loudspeake</title><path class="cls-1" d="M0,67.6c-.06-5.83,2.16-11,7.37-12.69l.13-.22a2.91,2.91,0,0,1,.6-.8,3.1,3.1,0,0,1,1.05-.5l.29-.11C34.7,44,46.09,30.07,52.64,20.61c0,9.29,2.33,21.4,6.43,33.12,4.21,12,10.29,23.71,17.71,31.76h0c-7.73-2.2-17.43-5-33.21-2.85a15.17,15.17,0,0,0-1.12,5.06,3.9,3.9,0,0,0,1,3l.24.22c1,.91,1.49,1.36,1.71,2.17a9.17,9.17,0,0,1-.13,3.22l-.09.67c-.37,2.93,1.05,3.53,2.46,4.13s2.51,1.08,3.08,2.69a.68.68,0,0,1,0,.4c-.58,3-2.5,4.64-4.43,6.29-.47.41-.95.82-1.36,1.21l-.06,0c-3.92,3.14-6.57,2.3-8.58-.22-1.58-2-2.68-5-3.7-7.81l-.44-1.22a122.88,122.88,0,0,1-4-15.09l-.34-1.5c-.87.24-1.78.51-2.73.79h0c-1.2.35-2.4.73-3.59,1.11l-.38.14a1.21,1.21,0,0,1-1.33,0C13.47,90,8.2,87,4.68,82a25,25,0,0,1-3.41-6.9A25.74,25.74,0,0,1,0,67.6ZM87.81,16.16a3,3,0,0,1-4.23-.6l-.07-.09a3.05,3.05,0,0,1,.63-4.19C89,7.69,93.84,4.07,98.7.56A3.09,3.09,0,0,1,100.92,0a3.06,3.06,0,0,1,2,1.21l0,.05a3,3,0,0,1,.52,2.23,3,3,0,0,1-1.21,2L87.81,16.16Zm15.79,57.7h0a3,3,0,0,1-2.11-.93,3,3,0,0,1-.85-2.14v-.07a3,3,0,0,1,3.07-3c5.37,0,10.83.2,16.2.3a3.07,3.07,0,0,1,3,3.1,3.09,3.09,0,0,1-.93,2.15,3,3,0,0,1-2.16.86l-16.19-.31Zm.47-15.71a3,3,0,0,1-3.25-2.78V55.3a3.07,3.07,0,0,1,2.77-3.23c5-.4,10.09-.83,15.1-1.15a3.06,3.06,0,0,1,3.24,2.81h0a3,3,0,0,1-.73,2.21A3.08,3.08,0,0,1,119.12,57c-4.85.48-10.18.88-15.05,1.14Zm-2.16-14.59a3.06,3.06,0,0,1-3.78-2.11v0a3,3,0,0,1,.27-2.3,3.1,3.1,0,0,1,1.83-1.46c5.21-1.37,10.4-3.05,15.61-4.45a3,3,0,0,1,2.3.27,3.08,3.08,0,0,1,1.45,1.8v0a3.07,3.07,0,0,1-2.1,3.78l-15.59,4.45Zm-6.3-14.32a3.05,3.05,0,0,1-4.09-1.37,3,3,0,0,1-.17-2.33,3.09,3.09,0,0,1,1.53-1.77l15-7.49a3,3,0,0,1,4.09,1.37,3,3,0,0,1,.16,2.33,3.07,3.07,0,0,1-1.53,1.76l-15,7.5ZM58.75,12c.13-.15.27-.29.4-.42a4.46,4.46,0,0,1,1.48-1.11h0a3.58,3.58,0,0,1,2-.08c2.25.36,4.66,2,7.11,4.51,6,6.19,12.32,18,16.82,30.41s7.2,25.36,5.85,33.77c-.51,3.13-1.57,5.66-3.31,7.33l-.09.07a5.64,5.64,0,0,1-2.47,1,2.43,2.43,0,0,0-.25-.2C80.3,83,75,75.9,70.54,67.55c1.14.22,2.6-.63,4-2.08,7-7.38,3.56-20.28-5.17-23.27C65.81,41,62.27,41,60.87,41.69l-.15.08C57.52,29.42,56.61,18,58.75,12Z"/></svg>' +
            ' Become a sponsor</button>';

        $('.ticket-card-div').html(html);
    }

    // ============================================
    // Render: Event Venue Info
    // ============================================

    function renderEventVenueInfo(conttoken_data, event_output, event_type, event_live_state) {
        var html = '';
        if (event_type === 'in-person') {
            html += '<path d="M14.8079 3.56198C15.2836 3.56198 15.7399 3.37434 16.0762 3.04034C16.4126 2.70634 16.6016 2.25334 16.6016 1.78099C16.6016 1.30864 16.4126 0.85564 16.0762 0.52164C15.7399 0.187639 15.2836 0 14.8079 0C14.3322 0 13.876 0.187639 13.5396 0.52164C13.2033 0.85564 13.0143 1.30864 13.0143 1.78099C13.0143 2.25334 13.2033 2.70634 13.5396 3.04034C13.876 3.37434 14.3322 3.56198 14.8079 3.56198ZM11.6579 7.43934C11.6952 7.4245 11.7289 7.40966 11.7662 7.39482L11.1347 9.75091C10.9255 10.5338 11.131 11.3686 11.684 11.966L14.3259 14.8267L15.148 18.0956C15.3087 18.7301 15.9589 19.1197 16.5978 18.9601C17.2368 18.8006 17.6292 18.155 17.4685 17.5205L16.6091 14.1032C16.5381 13.8138 16.3923 13.5504 16.1905 13.3314L14.3408 11.3278L15.062 8.89752L15.4208 9.75091C15.5852 10.1442 15.8879 10.467 16.2727 10.66L17.2705 11.1534C17.8609 11.4466 18.5783 11.2091 18.8735 10.6229C19.1687 10.0366 18.9296 9.32422 18.3392 9.0311L17.5358 8.63409L16.964 7.26866C16.3213 5.74369 14.8192 4.7493 13.1526 4.7493C12.3006 4.7493 11.4598 4.9274 10.6826 5.26876L10.3836 5.39862C9.15423 5.94034 8.21257 6.97183 7.79032 8.23707L7.69316 8.52649C7.48391 9.14983 7.82395 9.82141 8.44799 10.0292C9.07202 10.237 9.75211 9.89933 9.96137 9.2797L10.0585 8.99028C10.2715 8.35581 10.7424 7.84377 11.3552 7.57291L11.6541 7.44305L11.6579 7.43934ZM10.5368 12.4521L9.60264 14.7674L7.38301 16.9713C6.91592 17.4351 6.91592 18.1884 7.38301 18.6522C7.85011 19.1159 8.60867 19.1159 9.07576 18.6522L11.3813 16.3628C11.5532 16.1922 11.6878 15.9881 11.7774 15.7655L12.3193 14.4223L10.7984 12.7749C10.705 12.6747 10.619 12.5671 10.5368 12.4558V12.4521ZM8.23126 10.1702C7.94353 10.0069 7.58106 10.1034 7.41291 10.3891L6.21715 12.4447L4.18435 11.2796C3.61263 10.9531 2.88023 11.146 2.55139 11.7137L0.159872 15.8285C-0.168962 16.3962 0.025349 17.1235 0.597072 17.45L2.66723 18.6373C3.23896 18.9638 3.97136 18.7709 4.30019 18.2032L6.69171 14.0884C6.74777 13.9919 6.78887 13.8954 6.81503 13.7915L8.45172 10.9828C8.61614 10.6971 8.51899 10.3372 8.23126 10.1702Z" fill="#2557A7"/></svg>';
            html += '<span class="theme-blue-clr">In-Person, <span>' + eventVenueLoc(conttoken_data) + '</span></span>';
        } else if (event_type === 'hybrid') {
            html += '<path d="M6.48083 0.251601C6.84598 -0.0838669 7.40707 -0.0838669 7.76926 0.251601L13.9442 5.95158C14.1431 6.13564 14.25 6.39095 14.25 6.64923H9.97503C9.408 6.64923 8.89738 6.89861 8.55004 7.29345V6.17424C8.55004 5.91299 8.33629 5.69924 8.07504 5.69924H6.17505C5.9138 5.69924 5.70005 5.91299 5.70005 6.17424V8.07423C5.70005 8.33548 5.9138 8.54923 6.17505 8.54923H8.07504V12.3492H3.32506C2.53834 12.3492 1.90006 11.7109 1.90006 10.9242V7.59923H0.950065C0.558192 7.59923 0.20788 7.35876 0.0653809 6.99658C-0.0771186 6.63439 0.0178811 6.21877 0.305849 5.95158L6.48083 0.251601ZM10.45 9.02422V13.2992H16.15V9.02422H10.45ZM9.02504 8.54923C9.02504 8.02376 9.44957 7.59923 9.97503 7.59923H16.625C17.1505 7.59923 17.575 8.02376 17.575 8.54923V13.2992H18.525C18.7863 13.2992 19 13.513 19 13.7742C19 14.5609 18.3617 15.1992 17.575 15.1992H16.15H10.45H9.02504C8.23832 15.1992 7.60004 14.5609 7.60004 13.7742C7.60004 13.513 7.81379 13.2992 8.07504 13.2992H9.02504V8.54923Z" fill="#2557A7"/></svg>';
            html += '<span class="theme-blue-clr">Hybrid - <span>' + eventVenueLoc(conttoken_data) + '</span> or Virtual ' + eventJoinHere() + '</span>';
        } else if (event_type === 'virtual') {
            html += '<path d="M0 2.16667C0 0.971615 0.996528 0 2.22222 0H11.1111C12.3368 0 13.3333 0.971615 13.3333 2.16667V10.8333C13.3333 12.0284 12.3368 13 11.1111 13H2.22222C0.996528 13 0 12.0284 0 10.8333V2.16667ZM19.4132 1.21198C19.7743 1.40156 20 1.76719 20 2.16667V10.8333C20 11.2328 19.7743 11.5984 19.4132 11.788C19.0521 11.9776 18.6146 11.9573 18.2708 11.7339L14.9375 9.56719L14.4444 9.24557V8.66667V4.33333V3.75443L14.9375 3.43281L18.2708 1.26615C18.6111 1.04609 19.0486 1.0224 19.4132 1.21198Z" fill="#2557A7"></path></svg>';
            html += '<span class="theme-blue-clr">Virtual ' + eventJoinHere() + '</span>';
        }
        $('#event_venue_info').html(html);
    }

    // ============================================
    // Render: RSVP Ticket Confirmation Modal
    // ============================================

    function renderRsvpTicketModal(conttoken_data, event_output, event_type, event_live_state, eventBannersArray, localized_event_start_data) {
        if (!cfg.showRsvpTicket) return;

        getEventRSVPInfo({rsvptoken: cfg.rsvpTicketToken}, true)
            .then(async function(result) {
                var requestData = result.requestData;
                var response = result.response;
                var rsvp_output = response.output;

                if (rsvp_output.success) {
                    var rsvp_ptoken = rsvp_output.rsvp_user_ptoken;
                    var rsvp_slug_val = rsvp_output.rsvp_slug;
                    var rsvp_amount = parseFloat(rsvp_output.amount) || 0;

                    var html = '<div class="view-ticket d-flex flex-column flex-md-row align-items-center">' +
                        '<div style="width: 100%; max-width: 342px;">';

                    html += '<button type="button" class="btn btn-success valid-badge mb-3"><i class="fa fa-check-circle mr-1" aria-hidden="true"></i><span>Valid for entry</span></button>';
                    html += '<h3 class="ticket-title pb-3">' + conttoken_data.title + '</h3>';

                    if (event_type === 'in-person') {
                        html += '<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">In-Person, <span>' + eventVenueLoc(conttoken_data) + '</span></span></p>';
                    } else if (event_type === 'hybrid') {
                        html += '<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Hybrid - <span>' + eventVenueLoc(conttoken_data) + '</span> or Virtual ' + eventJoinHere() + '</span></p>';
                    } else if (event_type === 'virtual') {
                        html += '<p class="ticket-content py-1">Venue: <span class="theme-blue-clr">Virtual ' + eventJoinHere() + '</span></p>';
                    }

                    html += '<p class="ticket-content py-1">Start DateTime: <span>' + (typeof event_start_at !== 'undefined' ? event_start_at : beautifyTime(localized_event_start_data.datetime, localized_event_start_data.timezone)) + '</span></p>';

                    var rsvp_userinfo = await getUserInfo(rsvp_ptoken, 'notify');
                    if (rsvp_userinfo?.fname?.trim()) {
                        html += '<p class="ticket-content py-1">Name: <span>' + (rsvp_userinfo.fname || '') + ' ' + (rsvp_userinfo.lname || '') + '</span></p>';
                    }

                    var current_ticket_type = (conttoken_data.ticket_types).find(function(t) { return t.slug === rsvp_slug_val; });
                    if (current_ticket_type) {
                        html += '<p class="ticket-content pt-3">Ticket Type: ' + (current_ticket_type.title || '') + ' | ' + (rsvp_amount > 0 ? ' Paid $' + rsvp_amount : 'Free') + ' </p>';
                    }

                    html += '</div>';
                    html += '<div class="text-center">';

                    var eventTicketBanner = eventBannersArray[0] || [];
                    if (eventTicketBanner.type === "image") {
                        html += '<img class="ticket-main-image" src="' + eventTicketBanner.src + '" alt="Event">';
                    } else if (eventTicketBanner.type === "video") {
                        var thumbnailSrc = "";
                        if (eventTicketBanner.src.includes("youtube.com") || eventTicketBanner.src.includes("youtu.be")) {
                            thumbnailSrc = getYouTubeThumbnail(eventTicketBanner.src);
                        } else if (eventTicketBanner.src.includes("vimeo.com")) {
                            getVimeoThumbnail(eventTicketBanner.src, function(thumbnail) {
                                document.querySelector("[data-index='0']").src = thumbnail;
                            });
                            thumbnailSrc = "https://via.placeholder.com/150/FF0000/FFFFFF?text=Vimeo";
                        } else {
                            thumbnailSrc = "https://via.placeholder.com/150/0000FF/FFFFFF?text=Video";
                        }
                        html += '<img class="ticket-main-image" src="' + thumbnailSrc + '" alt="Event">';
                    }

                    html += '<img class="ticket-stamp" src="' + _taoh_site_url_root + '/assets/images/valid-for-admission.png" alt="valid-for-admission">';
                    html += '</div></div>';

                    $('#rsvpTicketModal .modal-body').html(html);
                    $('#rsvpTicketModal .modal-footer').html('<button type="button" class="btn theme-btn-primary" data-dismiss="modal" style="width: 150px;">OK</button>');
                    $('#rsvpTicketModal').modal('show');
                } else {
                    taoh_set_error_message('Unable to find any valid RSVP ticket info. Use a valid link to view your RSVP ticket.', false, 'toast-middle-right', [
                        { text: 'OK', action: function() {}, class: 'dojo-v1-btn float-right mt-3 mb-3' }
                    ]);
                }
            })
            .catch(function(error) { console.error("Error fetching event rsvp info:", error); });
    }

    // ============================================
    // Post-render: Halls & Sponsor Visibility
    // ============================================

    function setupHallsVisibility(event_output, conttoken_data, event_form_version, eventSponsorWidgetTypeStatusList) {
        setTimeout(function() {
            getEventMetaInfo(event_output.eventtoken).then(function() {
                getEventsHall(event_output.eventtoken);

                var isV2HallEnabled   = (event_form_version == 2 && conttoken_data.enable_hall === "1");
                var hasSpeakerHall    = (conttoken_data.enable_speaker_hall === "1");
                var hasExhibitorHall  = (conttoken_data.enable_exhibitor_hall === "1");

                var $speakerTop   = $('#speaker_top');
                var $speakerDesc  = $('#speaker_desc');
                var $exhibitorTop = $('#exhibitor_top');
                var $exhibitorDesc= $('#exhibitor_desc');
                var $agendaDesc   = $('#agenda_desc');
                var $sponsorHeader= $('.event_sponsor_right_header');
                var $chooseTicket = $('#choose_ticket');

                if ((isV2HallEnabled) || (hasSpeakerHall && hasExhibitorHall)) {
                    if (isV2HallEnabled || hasSpeakerHall) $speakerTop.show();
                    if (isV2HallEnabled || hasExhibitorHall) $exhibitorTop.show();
                } else if (hasSpeakerHall && !hasExhibitorHall) {
                    $exhibitorDesc.remove();
                    $exhibitorTop.remove();
                    $speakerTop.show();
                } else if (!hasSpeakerHall && hasExhibitorHall) {
                    $speakerDesc.remove();
                    $speakerTop.remove();
                    $exhibitorTop.show();
                } else {
                    $sponsorHeader.hide();
                    $chooseTicket.show();
                    $exhibitorDesc.remove();
                    $exhibitorTop.remove();
                    $speakerDesc.remove();
                    $speakerTop.remove();
                    $agendaDesc.addClass('show active');
                }
            });
        }, 3000);
    }

    function setupSponsorVisibility(conttoken_data, eventSponsorWidgetTypeStatusList) {
        setTimeout(function() {
            var event_status = $('#event_status_hidden').val();
            if ((!cfg.isLoggedIn || event_status == 2) && eventSponsorWidgetTypeStatusList.includes(1)) {
                $('.event_sponsor_right_header').show();
                $('#sponsor_card').show();
                $('.get-started').show();
                $("#choose_ticket").show();
            } else {
                $('.event_sponsor_right_header').hide();
                $('.get-started').hide();
                $("#choose_ticket").show();
                $('#continuePurchase').modal('hide');
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
                if (cfg.isUserRsvpDone) {
                    $("#register_now").hide();
                }
                loader(false, $("#rsvpdir_loaderArea"));
            }

            if ($("#is_organizer").val() == 1)
                $('#networking_link').show();
            else
                $('#networking_link').hide();

            if (conttoken_data.table_discussion != '' && conttoken_data.table_discussion != undefined && conttoken_data.table_discussion == 1) {
                $('#tables_top').show();
            }

            if (conttoken_data.comments != '' && conttoken_data.comments != undefined && conttoken_data.comments == 1) {
                $('#comments_top').show();
            }

        }, 4000);
    }

    // ============================================
    // Main: processEventBaseInfo
    // ============================================

    function processEventBaseInfo(requestData, response) {
        console.log('processEventBaseInfo', response);
        var event_output = response.output;
        var event_owner = event_output.ptoken;
        var conttoken_data = event_output.conttoken;

        if (!conttoken_data) return;

        var event_country_name = '';
        if (conttoken_data.full_location != '' && conttoken_data.full_location != undefined) {
            var event_country = conttoken_data.full_location;
            var evet_country_array = event_country.split(',');
            event_country_name = evet_country_array[evet_country_array.length - 1].trim();
        }

        var country_locked = 0;
        if (conttoken_data.country_locked != '' && conttoken_data.country_locked != undefined) {
            country_locked = conttoken_data.country_locked;
        }

        $('#event_country_lock').val(country_locked);
        $('#event_country_name').val(event_country_name);

        if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) {
            var org_email = $.trim(conttoken_data.org_email);
            $("#exhibitor_contactus").attr('data-email', org_email + "");
            $("#speaker_contactus").attr('data-email', org_email + "");
            $("#agenda_contactus").attr('data-email', org_email + "");
        }

        var event_organizer_ptokens = (conttoken_data.event_organizer_ptokens || "")
            .split(',')
            .map(function(token) { return token.trim(); })
            .filter(function(token) { return token; });

        if (event_owner) event_organizer_ptokens.push(event_owner);

        var event_instance_owner = conttoken_data.ptoken;
        event_organizer_ptokens.push(event_instance_owner);

        var superorganizer_token = $('#superorganizer_token').val();
        event_organizer_ptokens.push(superorganizer_token);

        if (event_organizer_ptokens.includes(cfg.ptoken) || cfg.ptoken == superorganizer_token) {
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

        var event_type = (conttoken_data?.event_type || 'virtual').toLowerCase();
        var is_event_suspended = parseInt(event_output?.status) === 2;
        var is_event_freeze = parseInt(conttoken_data?.freeze_option) === 1;
        $('.event_title').text(conttoken_data?.title);

        // Render sections
        var eventBannersArray = renderEventBanner(conttoken_data, event_output);
        renderLikeButton(event_output, conttoken_data);
        renderEventDescription(conttoken_data);

        // Timezone
        var user_timezone;
        if (cfg.isLoggedIn) {
            user_timezone = cfg.userTimezone;
        }
        if (!cfg.isLoggedIn || !user_timezone?.trim()) {
            var clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
            user_timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        }
        if (!isValidTimezone(user_timezone)) user_timezone = 'UTC';

        var event_live_state = eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken.locality, user_timezone);
        if (cfg.isLoggedIn && !is_event_suspended && !is_event_freeze && event_live_state === 'before') {
            constructUpgradeModalContent(event_output, cfg.ptoken, cfg.rsvpSlug, cfg.isLoggedIn);
            $('.attendee_tagline').css('display', 'flex');
        } else {
            $('.upgrade_modal_btn_wrapper').hide();
        }

        // Ticket types
        renderTicketTypes(conttoken_data, event_output, event_type, is_event_suspended, is_event_freeze, event_live_state);

        // Date/time
        var event_timestamp_start_data = {
            utc_datetime: event_output.utc_start_at,
            local_datetime: event_output.local_start_at,
            timezone: event_output.local_timezone,
            locality: event_output.conttoken.locality
        };
        var event_timestamp_end_data = {
            utc_datetime: event_output.utc_end_at,
            local_datetime: event_output.local_end_at,
            timezone: event_output.local_timezone,
            locality: event_output.conttoken.locality
        };

        var localized_event_start_data = get_localized_event_data(event_timestamp_start_data, user_timezone);
        var localized_event_ends_data = get_localized_event_data(event_timestamp_end_data, user_timezone);

        var event_info_container = $('#event_info_container');
        event_info_container.find('#event_start_end_datetime').text(formatEventDateTime(localized_event_start_data, localized_event_ends_data));

        $('#event_start_at').val(event_output.local_start_at);
        $('#event_end_at').val(event_output.local_end_at);
        $('#event_timezone').val(event_output?.local_timezone);
        $('#event_locality').val(event_output?.conttoken.locality ?? '');

        // Venue info
        renderEventVenueInfo(conttoken_data, event_output, event_type, event_live_state);

        // Sponsor
        getEventSponsor(event_output.eventtoken);

        var eventSponsorWidgetType = conttoken_data.event_sponsor_levels || {};
        var eventTicketType = conttoken_data.ticket_types || {};
        var eventSponsorWidgetTypeStatusList = Object.values(eventSponsorWidgetType).map(function(widget) {
            return widget.quantity > 0 ? 1 : 0;
        });

        var event_form_version = conttoken_data.event_form_version ?? 1;
        var is_social_share_enabled = conttoken_data.event_social_sharing;
        constructSponsorInfoPopup(event_output.eventtoken, eventSponsorWidgetType, cfg.userProfileType, conttoken_data.org_email, cfg.socialShareStatus, eventTicketType, event_form_version, is_social_share_enabled, cfg.trackingtoken, cfg.isLoggedIn);
        eventCheckinList(event_output.eventtoken, '', 1);

        $("#enable_exhibitor_hall").val(conttoken_data.enable_exhibitor_hall);
        $("#enable_speaker_hall").val(conttoken_data.enable_speaker_hall);

        // RSVP toggle
        var rsvp_status_hidden = $('#rsvp_status_hidden').val();
        if (rsvp_status_hidden == 0) {
            $('.more-info').hide();
        }

        // Halls & sponsor visibility (delayed)
        setupHallsVisibility(event_output, conttoken_data, event_form_version, eventSponsorWidgetTypeStatusList);
        setupSponsorVisibility(conttoken_data, eventSponsorWidgetTypeStatusList);

        // RSVP ticket modal
        renderRsvpTicketModal(conttoken_data, event_output, event_type, event_live_state, eventBannersArray, localized_event_start_data);

        // Finalize
        $('.aw').awloader('hide');

        // Continue purchase
        $('#continuePurchase').modal('hide');
        if (cfg.refSlug != '' && cfg.successDiscountAmt != '') {
            if (cfg.trackingtoken != '') {
                getEventSponsorForShare(cfg.eventToken, cfg.trackingtoken);
            }
            var newUrl = cfg.originalLink;
            history.replaceState(null, "", newUrl);
        }
    }

    // ============================================
    // URL Confirmation Handler
    // ============================================

    function handleUrlConfirmation() {
        var url = new URL(window.location.href);
        if (url.searchParams.has('confirmation')) {
            if (url.searchParams.get('action_events') === 'events') {
                delete_events_into('event_details_sponsor_' + cfg.eventToken);
                delete_events_into('event_MetaInfo_' + cfg.eventToken);
            }
            if (url.searchParams.get('confirmation') === 'sponsor') {
                delete_events_into('event_details_sponsor_' + cfg.eventToken);
                delete_events_into('event_MetaInfo_' + cfg.eventToken);

                if (url.searchParams.has('status') && url.searchParams.get('status') === 'success') {
                    taoh_set_success_message('Thank you for sponsoring this event.');
                } else if (url.searchParams.has('delete') && url.searchParams.get('delete') === 'success') {
                    taoh_set_success_message('Sponsor deleted successfully!');
                } else if (url.searchParams.has('status') && url.searchParams.get('status') === 'limitexceed') {
                    taoh_set_error_message('You exceed the limit! You are allowed to add one sponsor only.');
                } else if (url.searchParams.has('status') && url.searchParams.get('status') === 'nosponsortype') {
                    taoh_set_error_message('Please select the sponsor type to get started with adding new sponsor.');
                } else {
                    taoh_set_error_message('There was an error processing your request. Please try again later.');
                }
            }

            url.searchParams.delete('confirmation');
            url.searchParams.delete('status');
            url.searchParams.delete('tickettoken');
            url.searchParams.delete('delete');
            window.history.pushState({}, '', url.toString());
        }
    }

    // ============================================
    // Document Ready - Main Init
    // ============================================

    $(document).ready(function() {
        if (cfg.isLoggedIn && !cfg.profileType && typeof showBasicSettingsModal === 'function') {
            showBasicSettingsModal(true);
        }

        if (!cfg.isUserRsvpDone) {
            $("#desc-tab").addClass('active');
            $("#desc_desc").addClass('show active');
            $("#desc-tab").show();
            $("#agenda_desc").removeClass('show active');
            $("#agenda_desc").addClass('fade');
            $("#agenda-tab").removeClass('active');
        }

        getEventBaseInfo({ eventtoken: cfg.eventToken }, true)
            .then(function(result) { processEventBaseInfo(result.requestData, result.response); })
            .catch(function(error) { console.error("Error fetching event info:", error); });

        if (cfg.isLoggedIn) {
            save_metrics('events', cfg.clickView, cfg.eventToken);
        }

        handleUrlConfirmation();
    });

    // ============================================
    // Event Handlers
    // ============================================

    $(document).on("click", "#ticket_list li.ticket-item", function() {
        $('.hall_tabs .nav-item').each(function() {
            $(this).css('pointer-events', 'none');
        });
        var current_elem = $(this);
        var current_input_elem = current_elem.find('input[type="radio"]');
        var selected_ticket_title = current_elem.find('label .item-title').text();
        if (!current_input_elem.prop('disabled')) {
            if (cfg.isLoggedIn) {
                var selected_ticket = current_input_elem.val();
                if (selected_ticket) {
                    $('#choose_ticket').removeClass('dropdown-toggle').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                    window.location.href = _taoh_site_url_root + '/events/add_rsvp/' + cfg.eventToken + '/' + selected_ticket + '/' + cfg.encodeCurrentUrl;
                } else {
                    alert('Please select a ticket');
                }
            }
        } else {
            taoh_set_error_message(selected_ticket_title + ' ticket is not available for selection');
        }
    });

    $(document).on("click", ".event_save", function(event) {
        event.stopPropagation();
        if (!cfg.isLoggedIn) {
            taoh_set_error_message('Login to perform the action.');
            return false;
        }
        var savetoken = $(this).attr('data-event');
        var contttoken = $(this).attr('data-cont');
        $('.events_like').find("[data-cont='" + contttoken + "']").attr('src', cfg.bookmarkFillSrc);
        $('.events_like').find("[data-cont='" + contttoken + "']").removeClass('event_save').addClass("already-saved").removeAttr("style");
        $('.events_like').find("[data-cont='" + contttoken + "']").parent().addClass("already-saved").removeAttr("style");
        localStorage.setItem('events_' + savetoken + '_' + contttoken + '_liked', 1);
        delete_events_into('event_detail_' + savetoken);
        var data = {
            'taoh_action': 'event_like_put',
            'eventtoken': savetoken,
            'contttoken': contttoken,
            'ptoken': cfg.ptokenOrDummy
        };
        $.post(_taoh_site_ajax_url, data, function(response) {
            if (response.success) {
                taoh_set_success_message('Event Saved Successfully.');
            } else {
                taoh_set_error_message('Event Save Failed.');
            }
        }).fail(function() {
            console.log("Network issue!");
        });
    });

    // Carousel
    $('#event_gallery_carousel').carousel({ auto: false });

    setTimeout(function() {
        var $carousel = $('#event_gallery_carousel');
        var totalItems = $carousel.find('.carousel-item').length;
        if (totalItems <= 1) {
            $carousel.find('.carousel-control-prev, .carousel-control-next').hide();
        }
    }, 3000);

    // Dojo suggestions
    if (cfg.dojoSuggestionEnable) {
        var timelimit = cfg.dojoSuggestionTimeLimit;
        var innertimelimit = Math.floor(timelimit / 2);
        setInterval(function() { refreshDojoLobbyContexts(); }, timelimit);
        setInterval(function() { checkNextDojoEventScenario(); }, innertimelimit);
        refreshDojoLobbyContexts();
        checkNextDojoEventScenario();
    }

    // Share modal
    var currentShareLink = "";
    $(document).on("click", "[data-target='#shareModal']", function() {
        if ($(this).hasClass('sponsor-share-click')) {
            $('.sponsor-share-title').show();
            $('.normal-share-title').hide();
            $('#social_from').val(2);
            $('.email-btn').hide();
            $('.copys-btns').hide();
        } else {
            $('.sponsor-share-title').hide();
            $('.normal-share-title').show();
            $('#social_from').val(0);
            $('.email-btn').show();
            $('.copys-btns').show();
        }

        var shareUrl = $(this).data("url");
        if (shareUrl != '' && shareUrl != undefined) {
            currentShareLink = shareUrl;
        }
    });

    // Create referral
    $(document).on("click", ".create_referral", function() {
        var event_title = $(this).data("title");
        var link = $(this).data("location");
        var data = {
            'taoh_action': 'taoh_invite_rsvp_type',
            'from_link': link,
            'detail_link': window.location.href,
            'event_title': event_title
        };
        $.post(_taoh_site_ajax_url, data, function(response) {});
    });

    // Sticky header: use position:fixed when scrolled past sentinel
    (function() {
        var $stickyEl = $('.sticky-top-fixed');
        var sentinel = document.getElementById('stickySentinel');
        if (!$stickyEl.length || !sentinel) return;

        var normalHeight = $stickyEl.outerHeight(true);

        var placeholder = document.createElement('div');
        placeholder.id = 'stickyPlaceholder';
        placeholder.style.cssText = 'height:0;margin:0;padding:0;';
        $stickyEl[0].parentNode.insertBefore(placeholder, $stickyEl[0].nextSibling);

        var isStuck = false;
        var sentinelOffset = sentinel.getBoundingClientRect().top + window.scrollY;

        $(window).on('scroll.stickyHeader', function() {
            var scrollTop = window.scrollY;
            var $hallTabsNav = $('.hall_tabs > ul.nav-tabs');

            if (scrollTop >= sentinelOffset && !isStuck) {
                isStuck = true;
                placeholder.style.height = normalHeight + 'px';
                $stickyEl.addClass('is-sticky');
                var collapsedHeight = $stickyEl.outerHeight();
                if ($hallTabsNav.length) {
                    $hallTabsNav.addClass('is-tabs-sticky');
                    $hallTabsNav.css('top', collapsedHeight + 'px');
                }
            } else if (scrollTop < sentinelOffset && isStuck) {
                isStuck = false;
                placeholder.style.height = '0';
                $stickyEl.removeClass('is-sticky');
                if ($hallTabsNav.length) {
                    $hallTabsNav.removeClass('is-tabs-sticky');
                    $hallTabsNav.css('top', '');
                }
            }
        });
    })();

    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 100) {
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });

    // Register now
    $(document).on('click', '.register_now', function() {
        var dropdown = new bootstrap.Dropdown(document.getElementById('choose_ticket'));
        dropdown.show();
        $('#choose_ticket')[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        $('#choose_ticket').focus();
        $('#choose_ticket').trigger('click');
        var $menu = $('#ticket_list');
        $menu.toggleClass('show');
    });

})(jQuery);
