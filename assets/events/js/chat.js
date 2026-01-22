/**
 * Event Chat/Lobby Page JavaScript
 * Extracted from app/events/chat.php
 *
 * All PHP variables are passed via window.chatConfig object
 */

(function($) {
    'use strict';

    // Get config from window (set by PHP inline script)
    var cfg = window.chatConfig || {};

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
                        ${taoh_title_desc_decode(additionalInfo[titleKey])}</a>
                </li>`;

                contentHtml += `<div class="tab-pane fade show ${isFirst ? 'active' : ''}" id="ad_info_${key}" role="tabpanel" aria-labelledby="ad_info_${key}_tab">
                        <p>${taoh_desc_decode(additionalInfo[contentKey])}</p>
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

    function eventJoinHere(taoh_curr_app_url, eventtoken, is_user_rsvp_done, event_live_state) {
        return '';
    }

    function toggleVisibility(radioName, sectionToShow) {
        const yesOption = document.querySelector(`input[name="${radioName}"][id$="-yes"]`);
        const noOption = document.querySelector(`input[name="${radioName}"][id$="-no"]`);
        const section = document.getElementById(sectionToShow);

        if (yesOption && yesOption.checked) {
            section.style.display = "";
        } else if (noOption && noOption.checked) {
            section.style.display = "none";
        }
    }

    function formatTimestamp(timestamp) {
        var year = timestamp.slice(0, 4);
        var month = timestamp.slice(4, 6);
        var day = timestamp.slice(6, 8);
        var hour = timestamp.slice(8, 10);
        var minute = timestamp.slice(10, 12);
        var second = timestamp.slice(12, 14);
        return year + '-' + month + '-' + day + 'T' + hour + ':' + minute + ':' + second;
    }

    function delete_events_into(find_key) {
        if (typeof getIntaoDb === 'undefined' || typeof dbName === 'undefined' || typeof EVENTStore === 'undefined') return;

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

    // ============================================
    // Global Functions (available outside module)
    // ============================================

    window.updateraffle = function() {
        if($("input[name='exh_raffles']:checked").val() == '1') {
            $('#exh_raffle_options').show();
        } else {
            $('#exh_raffles_timebound_no').prop('checked', true).trigger('change');
            $('#exh_raffle_time_bound_time').hide();
            $('#exh_raffle_options').hide();
        }
    };

    window.updateraffletimebound = function() {
        if($("input[name='exh_raffles_timebound_option']:checked").val() == '1') {
            $('#exh_raffle_time_bound_time').show();
        } else {
            $('#exh_raffle_time_bound_time').hide();
        }
    };

    window.updateTimeSlotHelperText = function(user_timezone) {
        let spk_timezone = $('input[name="spk_timezoneSelect"]').val();
        let event_start_at = $("#event_start_at").val();
        let event_end_at = $("#event_end_at").val();
        let event_timezone = $("#event_timezone").val();
        let event_locality = $("#event_locality").val();

        let event_timestamp_start_data = {
            utc_datetime: event_start_at,
            local_datetime: event_start_at,
            timezone: event_timezone,
            locality: event_locality
        };

        let event_timestamp_end_data = {
            utc_datetime: event_end_at,
            local_datetime: event_end_at,
            timezone: event_timezone,
            locality: event_locality
        };

        if (typeof format_event_timestamp === 'function') {
            let startdate = format_event_timestamp(event_timestamp_start_data, spk_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);
            let enddate = format_event_timestamp(event_timestamp_end_data, spk_timezone, 'date', 'yyyy-MM-dd HH:mm:ss', 0);

            if (spk_timezone) {
                const formatForInput = dt => dt.replace(/:\d{2}$/, '');

                const formattedStartDateTime = formatForInput(startdate.replace(' ', 'T'));
                const formattedEndDateTime = formatForInput(enddate.replace(' ', 'T'));

                document.getElementById('spk_datefrom').min = formattedStartDateTime;
                document.getElementById('spk_datefrom').max = formattedEndDateTime;
                document.getElementById('spk_dateto').min = formattedStartDateTime;
                document.getElementById('spk_dateto').max = formattedEndDateTime;

                $('#spk_timeslot_timezone_txt').text(`in ${user_timezone}`);
            }
        }
    };

    window.eventCheckIn = function(eventtoken) {
        var data = {
            'taoh_action': 'event_checkin',
            'eventtoken': eventtoken,
            'country_locked': $('#event_country_lock').val(),
            'country': $('#event_country_name').val(),
            'ptoken': cfg.ptoken,
            'ticket_details': JSON.stringify(cfg.currentTicketType)
        };
        $.post(_taoh_site_ajax_url, data, function (response) {
            if (response.success) {
                taoh_set_success_message('Event checkedin Successfully.');
            }
        }).fail(function () {
            console.log("Network issue!");
        });
    };

    window.updateEventStatusButton = function() {
        var user_timezone;
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

        let event_output = cfg.eventArr;
        if (!event_output) return;

        let event_live_state = typeof eventLiveState === 'function'
            ? eventLiveState(event_output.utc_start_at || '', event_output.utc_end_at || '', event_output.conttoken?.locality, user_timezone)
            : 'before';

        let chat_room_status = $('#chat_room_status').val();
        let event_live_link = (chat_room_status == 2 && typeof isValidUrl === 'function' && isValidUrl(event_output.conttoken?.external_link))
            ? event_output.conttoken.external_link
            : cfg.adopterUrl;

        if (event_live_state == 'live') {
            taoh_set_success_message('Event live now!!', false);
            setTimeout(function() {
                location.reload();
            }, 2000);
        }
    };

    // Make helper functions available globally
    window.chatHelpers = {
        hasVisibleText: hasVisibleText,
        generateAdditionalInfoTabsHtml: generateAdditionalInfoTabsHtml,
        formatVideoSrc: formatVideoSrc,
        eventVenueLoc: eventVenueLoc,
        eventJoinHere: eventJoinHere,
        toggleVisibility: toggleVisibility,
        formatTimestamp: formatTimestamp,
        delete_events_into: delete_events_into
    };

    // ============================================
    // Document Ready Handler
    // ============================================

    $(document).ready(function() {
        // File size validator
        if ($.validator) {
            $.validator.addMethod('filesize', function (value, element, param) {
                return this.optional(element) || (element.files[0].size <= param * 1000000);
            }, 'File size must be less than {0} MB');
        }

        // Show basic settings modal if needed
        if (cfg.isLoggedIn && !cfg.profileType && typeof showBasicSettingsModal === 'function') {
            showBasicSettingsModal(true);
        }

        // Save metrics
        if (cfg.isLoggedIn && typeof save_metrics === 'function') {
            save_metrics('events_lobby', cfg.clickView, cfg.eventToken);
        }

        // Speaker slot button handler
        $('#setup_speaker_slot_btn').on('click', async function() {
            let setup_speaker_slot_btn = $(this);
            let setup_speaker_slot_btn_icon = setup_speaker_slot_btn.find('i');

            setup_speaker_slot_btn.prop('disabled', true);
            setup_speaker_slot_btn_icon.removeClass('fa-pencil-square-o').addClass('fa-spinner fa-spin');

            try {
                const response = await getEventBaseInfo({ eventtoken: cfg.eventToken }, true);
                const event_output = response.response.output;
                const conttoken_data = event_output.conttoken;

                // Get speaker halls
                const spk_allowed = new Set(["1", "3"]);
                const allowed_speaker_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                    .filter(h => Number(h?.id) > 0 && h?.name && spk_allowed.has(h.accesslevel));

                // Populate hall dropdown
                $('#spk_hall').empty().append('<option value="">Select Session Room</option>');
                allowed_speaker_halls.forEach(hall => {
                    $('#spk_hall').append(`<option value="${hall.name}">${hall.name}</option>`);
                });

                // Set country locked
                $('#speakerSlotModal').find('input[name="country_locked"]').val(conttoken_data.country_locked);

                // Show modal
                $('#speakerSlotModal').modal('show');
            } catch (error) {
                console.error("Error setting up speaker slot:", error);
            } finally {
                setup_speaker_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                setup_speaker_slot_btn.prop('disabled', false);
            }
        });

        // Exhibitor slot button handler
        $('#setup_exhibitor_slot_btn').on('click', async function() {
            let setup_exhibitor_slot_btn = $(this);
            let setup_exhibitor_slot_btn_icon = setup_exhibitor_slot_btn.find('i');

            setup_exhibitor_slot_btn.prop('disabled', true);
            setup_exhibitor_slot_btn_icon.removeClass('fa-pencil-square-o').addClass('fa-spinner fa-spin');

            try {
                const response = await getEventBaseInfo({ eventtoken: cfg.eventToken }, true);
                const event_output = response.response.output;
                const conttoken_data = event_output.conttoken;

                // Get exhibitor halls
                const exh_allowed = new Set(["2", "3"]);
                const allowed_exhibitor_halls = (Array.isArray(conttoken_data?.event_halls) ? conttoken_data.event_halls : [])
                    .filter(h => Number(h?.id) > 0 && h?.name && exh_allowed.has(h.accesslevel));

                // Populate hall dropdown
                $('#exh_hall').empty().append('<option value="">Select Exhibit Hall</option>');
                allowed_exhibitor_halls.forEach(hall => {
                    $('#exh_hall').append(`<option value="${hall.name}">${hall.name}</option>`);
                });

                // Set country locked
                $('#exhibitorSlotModal').find('input[name="country_locked"]').val(conttoken_data.country_locked);

                // Show modal
                $('#exhibitorSlotModal').modal('show');
            } catch (error) {
                console.error("Error setting up exhibitor slot:", error);
            } finally {
                setup_exhibitor_slot_btn_icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
                setup_exhibitor_slot_btn.prop('disabled', false);
            }
        });

        // Speaker timezone change handler
        $('input[name="spk_timezoneSelect"]').on('change', function() {
            $('#spk_datefrom').val('');
            $('#spk_dateto').val('');
            updateTimeSlotHelperText(cfg.myLocalTimezone);
        });

        // Video room checkbox handlers
        $('input[name="spk_video_room[]"]').on('change', function () {
            let isZoomChecked = $('#spk_video_room-yes').prop('checked');
            let isPhysicalRoomChecked = $('#spk_video_room-physical').prop('checked');

            if (isZoomChecked) {
                $('#spk_video_room-link').css('display', 'block');
            } else {
                $('#spk_video_room-link').css('display', 'none');
            }
            if (isPhysicalRoomChecked) {
                $('#spk_phycial_location-link').css('display', 'block');
            } else {
                $('#spk_phycial_location-link').css('display', 'none');
            }
        });

        // Event check-in after 5 seconds
        setTimeout(() => {
            eventCheckIn(cfg.eventToken);
        }, 5000);

        // Update status button periodically if before live
        if (cfg.liveState == 'before') {
            setInterval(() => updateEventStatusButton(), 15 * 60 * 1000);
        }

        // Continue purchase modal
        $('#continuePurchase').modal('hide');

        // Handle social share discount
        if (cfg.refSlug != '' && cfg.successDiscountAmt != '') {
            if (cfg.trackingtoken != '' && typeof getEventSponsorForShare === 'function') {
                getEventSponsorForShare(cfg.eventToken, cfg.trackingtoken).then(() => {}).catch(() => {});
            }
            let newUrl = cfg.originalLink;
            if (newUrl) history.replaceState(null, "", newUrl);
        }

        // Complete settings modal for invalid users
        if (!cfg.isValidUser) {
            setTimeout(() => {
                $('#completeSettingsModal').modal('show');
            }, 5000);

            $('.complete_settings_now').on('click', function () {
                $('#completeSettingsModal').modal('hide');
                $('#completeSettingsModal').on('hidden.bs.modal', function () {
                    $(this).off('hidden.bs.modal');
                    if (typeof showBasicSettingsModal === 'function') {
                        showBasicSettingsModal();
                    }
                });
            });
        }

        // URL parameter handling
        const url = new URL(window.location.href);
        if (url.searchParams.has('confirmation')) {
            if (url.searchParams.get('confirmation') === 'sponsor') {
                if (url.searchParams.has('status') && url.searchParams.get('status') === 'success') {
                    taoh_set_success_message('Thank you for your interest in sponsoring this event.');
                } else {
                    taoh_set_error_message('There was an error processing your request. Please try again later.');
                }
            }
            url.searchParams.delete('confirmation');
            url.searchParams.delete('status');
            url.searchParams.delete('tickettoken');
            window.history.pushState({}, '', url.toString());
        }

        if (url.searchParams.has('from') && url.searchParams.get('from') === 'sponsor') {
            delete_events_into('event_details_sponsor_' + cfg.eventToken);
            delete_events_into('event_MetaInfo_' + cfg.eventToken);
            url.searchParams.delete('from');
            window.history.pushState({}, '', url.toString());
        }

        if (url.searchParams.has('upgrade') && url.searchParams.get('upgrade') === 'from_email') {
            url.searchParams.delete('upgrade');
            window.history.pushState({}, '', url.toString());
        }

        // Speaker video room toggle
        document.querySelectorAll('input[name="speaker-video-room"]').forEach(input => {
            input.addEventListener('change', function() {
                toggleVisibility('speaker-video-room', 'speaker-video-room-link');
            });
        });

        // Additional video room checkbox handling
        $(document).on('change', 'input[name="spk_video_room[]"]', function() {
            if ($("#spk_video_room-no").prop("checked")) {
                $("#spk_video_room-yes").prop("disabled", true);
            } else {
                $("#spk_video_room-yes").prop("disabled", false);
            }
            if ($("#spk_video_room-yes").prop("checked")) {
                $("#spk_video_room-no").prop("disabled", true);
            } else {
                $("#spk_video_room-no").prop("disabled", false);
            }
        });

        // Metrics tracking
        $(document).on('click', '.event_sponsor_right_header', function() {
            if (typeof save_metrics === 'function') save_metrics('become_sponsor', 'click', cfg.eventToken);
        });

        $(document).on('click', '.get-started', function() {
            if (typeof save_metrics === 'function') save_metrics('sponsor_get_started', 'click', cfg.eventToken);
        });

        $(document).on('click', '.join_video_link', function() {
            if (typeof save_metrics === 'function') save_metrics('join_video_link', 'click', cfg.eventToken);
        });

        $(document).on('click', '.join_networking', function() {
            if (typeof save_metrics === 'function') save_metrics('join_networking', 'click', cfg.eventToken);
        });

        $(document).on('click', '.metrics_action', function() {
            let action = $(this).data('metrics');
            if (typeof save_metrics === 'function') save_metrics(action, 'click', cfg.eventToken);
        });

        // Dojo suggestion feature
        if (cfg.dojoSuggestionEnable) {
            let timelimit = cfg.dojoSuggestionTimeLimit || 300000;
            let innertimelimit = Math.floor(timelimit / 2);

            setInterval(() => {
                if (typeof refreshDojoLobbyContexts === 'function') refreshDojoLobbyContexts();
            }, timelimit);

            setInterval(() => {
                if (typeof checkNextDojoEventScenario === 'function') checkNextDojoEventScenario();
            }, innertimelimit);

            if (typeof refreshDojoLobbyContexts === 'function') refreshDojoLobbyContexts();
            if (typeof checkNextDojoEventScenario === 'function') checkNextDojoEventScenario();
        }

        // Share modal handling
        let currentShareLink = "";
        $(document).on("click", "[data-target='#shareModal']", function () {
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
            let shareUrl = $(this).data("url");
            if (shareUrl != '' && shareUrl != undefined) {
                currentShareLink = shareUrl;
            }
        });

        // Sticky scroll handling
        $(window).on('scroll', function() {
            var $sticky = $('.sticky-top-fixed');
            if ($sticky.length) {
                var top_sticky_pos = $sticky.offset().top;
                var stickyTop = cfg.stickyTopThreshold || 126;

                if (top_sticky_pos > stickyTop) {
                    $sticky.addClass('is-sticky');
                } else {
                    $sticky.removeClass('is-sticky');
                }
            }

            if ($(this).scrollTop() > 100) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
    });

})(jQuery);
