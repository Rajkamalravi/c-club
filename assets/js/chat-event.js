/**
 * Chat Events JavaScript Module
 * Handles event functionality, exhibitor/speaker setup, and UI interactions
 */

class ChatEvents {
    constructor(options = {}) {
        this.config = {
            isLoggedIn: options.isLoggedIn || false,
            isValidUser: options.isValidUser || false,
            is_sponsor_enable: options.is_sponsor_enable || false,
            is_exhibitor_enable: options.is_exhibitor_enable || false,
            is_speaker_enable: options.is_speaker_enable || false,
            is_hall_enable: options.is_hall_enable || false,
            user_profile_type: options.user_profile_type || '',
            my_pToken: options.my_pToken || '',
            eventToken: options.eventToken || '',
            is_user_rsvp_done: options.is_user_rsvp_done || false,
            show_rsvp_ticket: options.show_rsvp_ticket || false,
            rsvp_ticket_token: options.rsvp_ticket_token || '',
            click_view: options.click_view || 'view',
            TAOH_CURR_APP_URL: options.TAOH_CURR_APP_URL || '',
            rsvp_slug: options.rsvp_slug || '',
            is_event_freeze: options.is_event_freeze || '',
            live_state: options.live_state || '',
            event_arr: options.event_arr || {},
            ref_slug: options.ref_slug || '',
            success_discount_amt: options.success_discount_amt || '',
            trackingtoken: options.trackingtoken || '',
            dojoeventrules: options.dojoeventrules || {}
        };
        
        this.init();
    }

    init() {
        this.setupValidators();
        this.bindEvents();
        this.initializeComponents();
        
        if (this.config.isLoggedIn) {
            this.saveMetrics('events_lobby', this.config.click_view, this.config.eventToken);
        }
    }

    setupValidators() {
        // Custom validator methods
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1000000);
        }, 'File size must be less than {0} MB');

        $.validator.addMethod("greaterThanOrEqual", function (value, element, params) {
            let fromDate = $(params).val();
            if (!fromDate || !value) return false;
            return new Date(value) > new Date(fromDate);
        }, "Start date should be less than end date");

        $.validator.addMethod("imageSize", function (value, element, param) {
            if (element.files.length === 0) return true;

            let file = element.files[0];
            let img = new Image();
            let URL = window.URL || window.webkitURL;
            let deferred = $.Deferred();

            img.src = URL.createObjectURL(file);
            img.onload = function () {
                URL.revokeObjectURL(img.src);
                if (img.width === param[0] && img.height === param[1]) {
                    deferred.resolve();
                } else {
                    deferred.reject();
                }
            };

            return deferred.promise();
        }, "Image must be exactly 1920x1080 pixels.");
    }

    bindEvents() {
        // Exhibitor slot button click
        $(document).on('click', '#setup_exhibitor_slot_btn', (e) => {
            this.handleExhibitorSlotSetup(e);
        });

        // Speaker slot button click
        $(document).on('click', '#setup_speaker_slot_btn', (e) => {
            this.handleSpeakerSlotSetup(e);
        });

        // File upload handlers
        $(document).on('change', '#exh_logo_upload', this.handleExhibitorLogoUpload);
        $(document).on('change', '#exh_banner_upload', this.handleExhibitorBannerUpload);
        $(document).on('change', '#spk_logo_upload', this.handleSpeakerLogoUpload);
        $(document).on('change', '#spk_image_upload', this.handleSpeakerImageUpload);
        $(document).on('change', '.spk_profileimg', this.handleSpeakerProfileImageUpload);

        // Radio button changes
        $(document).on('change', 'input[name="spk_video_room[]"]', this.handleVideoRoomChange);
        $(document).on('change', 'input[name="spk_video_room[]"]', this.handleVideoRoomToggle);

        // Contact host form
        $(document).on('click', '#contacthostSubmit', (e) => {
            this.handleContactHostSubmit(e);
        });

        // Share modal
        $(document).on("click", "[data-target='#shareModal']", this.handleShareModal);

        // Metrics tracking
        $(document).on('click', '.event_sponsor_right_header', () => {
            this.saveMetrics('become_sponsor', 'click', this.config.eventToken);
        });

        $(document).on('click', '.get-started', () => {
            this.saveMetrics('sponsor_get_started', 'click', this.config.eventToken);
        });

        $(document).on('click', '.join_video_link', () => {
            this.saveMetrics('join_video_link', 'click', this.config.eventToken);
        });

        $(document).on('click', '.join_networking', () => {
            this.saveMetrics('join_networking', 'click', this.config.eventToken);
        });

        $(document).on('click', '.metrics_action', (e) => {
            let action = $(e.target).data('metrics');
            this.saveMetrics(action, 'click', this.config.eventToken);
        });

        // Complete settings modal
        $('.complete_settings_now').on('click', () => {
            $('#completeSettingsModal').modal('hide');
            $('#completeSettingsModal').on('hidden.bs.modal', function () {
                $(this).off('hidden.bs.modal');
                if (typeof showBasicSettingsModal === 'function') {
                    showBasicSettingsModal();
                }
            });
        });
    }

    initializeComponents() {
        // Process event base info
        this.getEventBaseInfo({eventtoken: this.config.eventToken}, false)
            .then(({requestData, response}) => {
                this.processEventBaseInfo(requestData, response);
            })
            .catch(error => console.error("Error fetching event info:", error));

        // Initialize other components
        setTimeout(() => {
            this.eventCheckIn(this.config.eventToken);
        }, 5000);

        if (this.config.live_state === 'before') {
            setInterval(() => this.updateEventStatusButton(), 15 * 60 * 1000);
        }

        // Handle modals and popups
        this.handleInitialModals();

        // Setup intervals for DOJO suggestions if enabled
        this.setupDojoSuggestions();
    }

    async handleExhibitorSlotSetup(e) {
        let btn = $('#setup_exhibitor_slot_btn');
        let icon = btn.find('i');

        btn.prop('disabled', true);
        icon.removeClass('fa-pencil-square-o').addClass('fa-spinner fa-spin');

        try {
            const eventHallAccess = await this.getEventHallAccess();
            
            // Reset form
            $("#setup_exhibitor_slot_form").trigger('reset');
            $('#setup_exhibitor_slot_form').find('input[type=hidden]').val('');
            $('#exh_tags').val([]).trigger('change');
            $('#exh_room_status').prop('checked', true);
            $(".lead-raffle").hide();

            // Clear previews
            $('#exhibitorSlotModal').find("#exh_logo_preview, #exh_banner_preview").html('');
            
            // Set form values
            $('#exhibitorSlotModal').find('#taoh_action').val('save_exhibitor_slot');
            $('#exhibitorSlotModal').find('#eventtoken').val(this.config.eventToken);
            $('#exhibitorSlotModal').find('#ptoken').val(this.config.my_pToken);

            const {requestData, response} = await this.getEventBaseInfo({eventtoken: this.config.eventToken}, false);
            
            await this.setupExhibitorForm(response.output, eventHallAccess);
            
            $('#exhibitorSlotModal').modal('show');
        } catch (error) {
            console.error("Error setting up exhibitor slot:", error);
            taoh_set_error_message('Failed to setup exhibitor slot. Please try again.', false);
        } finally {
            icon.removeClass('fa-spinner fa-spin').addClass('fa-pencil-square-o');
            btn.prop('disabled', false);
        }
    }

    async handleSpeakerSlotSetup(e) {
        let btn = $('#setup_speaker_slot_btn');
        let icon = btn.find('i');

        try {
            // Reset form
            $('#spk_form').trigger('reset');
            $('#spk_form').find('input[type=hidden]').val('');
            $('#spk_tags').val([]).trigger('change');
            
            // Clear previews
            $("#spk_logo_image_preview, #spk_image_preview, .spk_profileimg_preview").html('');

            // Set form values
            $('#speakerSlotModal').find('#taoh_action').val('event_save_speaker');
            $('#speakerSlotModal').find('#eventtoken').val(this.config.eventToken);
            $('#speakerSlotModal').find('#ptoken').val(this.config.my_pToken);

            // Set date constraints
            let mindate = this.formatTimestamp($("#event_start_at").val());
            let maxdate = this.formatTimestamp($("#event_end_at").val());
            
            document.getElementById('spk_datefrom').setAttribute('min', mindate);
            document.getElementById('spk_datefrom').setAttribute('max', maxdate);
            document.getElementById('spk_dateto').setAttribute('min', mindate);
            document.getElementById('spk_dateto').setAttribute('max', maxdate);

            const eventHallAccess = await this.getEventHallAccess();
            const {requestData, response} = await this.getEventBaseInfo({eventtoken: this.config.eventToken}, false);
            
            await this.setupSpeakerForm(response.output, eventHallAccess);
            
            $('#speakerSlotModal').modal('show');
        } catch (error) {
            console.error("Error setting up speaker slot:", error);
            taoh_set_error_message('Failed to setup speaker slot. Please try again.', false);
        }
    }

    handleExhibitorLogoUpload(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && file.size > maxSize) {
            taoh_set_error_message("File size exceeds 2MB limit!", false);
            e.target.value = '';
        } else if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#exh_logo_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Logo" />`);
            };
            reader.readAsDataURL(file);
        }
    }

    handleExhibitorBannerUpload(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && file.size > maxSize) {
            taoh_set_error_message("File size exceeds 2MB limit!", false);
            e.target.value = '';
        } else if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#exh_banner_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Exhibitor Banner" />`);
            };
            reader.readAsDataURL(file);
        }
    }

    handleSpeakerLogoUpload(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && file.size > maxSize) {
            taoh_set_error_message("File size exceeds 2MB limit!", false);
            e.target.value = '';
        } else if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#spk_logo_image_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Speaker Logo" width="50" />`);
            };
            reader.readAsDataURL(file);
        }
    }

    handleSpeakerImageUpload(e) {
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && file.size > maxSize) {
            taoh_set_error_message("File size exceeds 2MB limit!", false);
            e.target.value = '';
        } else if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#spk_image_preview').html(`<img src="${e.target.result}" class="img-fluid" alt="Speaker Image" width="100" />`);
            };
            reader.readAsDataURL(file);
        }
    }

    handleSpeakerProfileImageUpload(e) {
        const idArr = $(e.target).attr("id");
        const file = e.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (file && file.size > maxSize) {
            taoh_set_error_message("File size exceeds 2MB limit!", false);
            e.target.value = '';
        } else if (file) {
            const reader = new FileReader();
            const curIdArr = idArr.split("spk_profileimg_upload_");
            const curId = curIdArr[1];
            
            reader.onload = function(e) {
                $(`#spk_profileimg_preview_${curId}`).html(`<img src="${e.target.result}" class="img-fluid" alt="Speaker Profile" width="100" />`);
            };
            reader.readAsDataURL(file);
        }
    }

    handleVideoRoomChange() {
        const isZoomChecked = $('#spk_video_room-yes').prop('checked');
        const isPhysicalRoomChecked = $('#spk_video_room-physical').prop('checked');

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
    }

    handleVideoRoomToggle() {
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
    }

    async handleContactHostSubmit(e) {
        e.preventDefault();
        
        try {
            const {requestData, response} = await this.getEventBaseInfo({eventtoken: this.config.eventToken}, false);
            const conttoken_data = response.output.conttoken;
            
            let to_email = '';
            if (conttoken_data.org_email && $.trim(conttoken_data.org_email)) {
                to_email = conttoken_data.org_email;
            } else {
                const contact_info = await this.getUserInfo(conttoken_data.ptoken, 'notify');
                if (contact_info?.email?.trim()) {
                    to_email = contact_info.email.trim();
                } else {
                    to_email = 'info@noworkerleftbehind.org';
                }
            }

            if ($("#contacthostForm").valid()) {
                const formData = new FormData($("#contacthostForm")[0]);
                formData.append('taoh_action', 'taoh_contact_host');
                formData.append('eventtoken', this.config.eventToken);
                formData.append('to_email', to_email);

                let submit_btn = $(e.target);
                submit_btn.prop('disabled', true);

                const response = await $.ajax({
                    url: _taoh_site_ajax_url,
                    type: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    cache: false
                });

                if (response.success) {
                    $("#contacthostModal").modal("hide");
                    document.getElementById("contacthostForm").reset();
                    submit_btn.prop('disabled', false);
                    taoh_set_success_message('Thanks! Mail sent successfully.', false);
                }
            }
        } catch (error) {
            console.error('Error submitting contact form:', error);
        }
    }

    handleShareModal(e) {
        const $this = $(e.target);
        
        if ($this.hasClass('sponsor-share-click')) {
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

        const shareUrl = $this.data("url");
        if (shareUrl && shareUrl !== undefined) {
            window.currentShareLink = shareUrl;
        }
    }

    // Utility methods
    formatTimestamp(timestamp) {
        const year = timestamp.slice(0, 4);
        const month = timestamp.slice(4, 6);
        const day = timestamp.slice(6, 8);
        const hour = timestamp.slice(8, 10);
        const minute = timestamp.slice(10, 12);
        const second = timestamp.slice(12, 14);

        return `${year}-${month}-${day}T${hour}:${minute}:${second}`;
    }

    async getEventHallAccess() {
        const eventHallAccessKey = `event_hall_access_${this.config.eventToken}`;
        const data = await IntaoDB.getItem(objStores.event_store.name, eventHallAccessKey);
        return data?.values?.output || [];
    }

    async getEventBaseInfo(params, useCache = true) {
        // Implementation would depend on your existing API structure
        // This is a placeholder for the actual implementation
        return new Promise((resolve) => {
            // Your existing getEventBaseInfo implementation
            resolve({requestData: params, response: {}});
        });
    }

    async getUserInfo(ptoken, ops = 'public') {
        // Implementation would depend on your existing API structure
        // This is a placeholder for the actual implementation
        return new Promise((resolve) => {
            // Your existing getUserInfo implementation
            resolve({});
        });
    }

    saveMetrics(action, type, eventToken) {
        if (typeof save_metrics === 'function') {
            save_metrics(action, type, eventToken);
        }
    }

    eventCheckIn(eventtoken) {
        const data = {
            'taoh_action': 'event_checkin',
            'eventtoken': eventtoken,
            'country_locked': $('#event_country_lock').val(),
            'country': $('#event_country_name').val(),
            'ptoken': this.config.my_pToken,
            'ticket_details': JSON.stringify(window.current_ticket_type || {})
        };

        $.post(_taoh_site_ajax_url, data, (response) => {
            if (response.success) {
                taoh_set_success_message('Event checked in successfully.');
            }
        }).fail(() => {
            console.log("Network issue!");
        });
    }

    updateEventStatusButton() {
        let user_timezone = this.getUserTimezone();
        const event_output = this.config.event_arr;
        const event_live_state = this.eventLiveState(
            event_output.utc_start_at || '', 
            event_output.utc_end_at || '', 
            event_output.conttoken.locality, 
            user_timezone
        );

        if (event_live_state === 'live') {
            taoh_set_success_message('Event live now!!', false);
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    }

    getUserTimezone() {
        if (this.config.isLoggedIn) {
            return window.user_timezone || 'UTC';
        }
        
        let clientTimeZone = typeof getCookie === 'function' ? getCookie('client_time_zone') : null;
        let timezone = convertDeprecatedTimeZone(clientTimeZone || Intl.DateTimeFormat().resolvedOptions().timeZone);
        
        return isValidTimezone(timezone) ? timezone : 'UTC';
    }

    eventLiveState(startTime, endTime, locality, timezone) {
        // Implementation would depend on your existing eventLiveState function
        // This is a placeholder
        const now = new Date();
        const start = new Date(startTime);
        const end = new Date(endTime);

        if (now < start) return 'before';
        if (now > end) return 'after';
        return 'live';
    }

    handleInitialModals() {
        // Handle URL parameters and initial modals
        if (!this.config.isValidUser) {
            setTimeout(() => {
                $('#completeSettingsModal').modal('show');
            }, 5000);
        }

        // Handle URL cleanup and redirects
        const url = new URL(window.location.href);
        let shouldUpdateUrl = false;

        if (url.searchParams.has('confirmation')) {
            const confirmation = url.searchParams.get('confirmation');
            if (confirmation === 'sponsor') {
                const status = url.searchParams.get('status');
                if (status === 'success') {
                    taoh_set_success_message('Thank you for your interest in sponsoring this event.');
                } else {
                    taoh_set_error_message('There was an error processing your request. Please try again later.');
                }
            }
            url.searchParams.delete('confirmation');
            url.searchParams.delete('status');
            url.searchParams.delete('tickettoken');
            shouldUpdateUrl = true;
        }

        if (url.searchParams.has('from') && url.searchParams.get('from') === 'sponsor') {
            this.deleteEventsInto('event_details_sponsor_' + this.config.eventToken);
            this.deleteEventsInto('event_MetaInfo_' + this.config.eventToken);
            url.searchParams.delete('from');
            shouldUpdateUrl = true;
        }

        if (shouldUpdateUrl) {
            window.history.pushState({}, '', url.toString());
        }
    }

    setupDojoSuggestions() {
        if (typeof window.TAOH_DOJO_SUGGESTION_ENABLE !== 'undefined' && window.TAOH_DOJO_SUGGESTION_ENABLE) {
            const timelimit = window.TAOH_DOJO_SUGGESTION_TIMELIMIT || 300000; // 5 minutes default
            const innertimelimit = Math.floor(timelimit / 2);

            setInterval(() => {
                if (typeof refreshDojoLobbyContexts === 'function') {
                    refreshDojoLobbyContexts();
                }
            }, timelimit);

            setInterval(() => {
                if (typeof checkNextDojoEventScenario === 'function') {
                    checkNextDojoEventScenario();
                }
            }, innertimelimit);

            // Initial trigger
            if (typeof refreshDojoLobbyContexts === 'function') {
                refreshDojoLobbyContexts();
            }
            if (typeof checkNextDojoEventScenario === 'function') {
                checkNextDojoEventScenario();
            }
        }
    }

    deleteEventsInto(find_key) {
        if (typeof getIntaoDb === 'function' && typeof EVENTStore !== 'undefined') {
            getIntaoDb(dbName).then((db) => {
                const transaction = db.transaction(EVENTStore, 'readwrite');
                const objectStore = transaction.objectStore(EVENTStore);
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
    }

    // Form setup methods (these would contain the complex form setup logic)
    async setupExhibitorForm(eventOutput, eventHallAccess) {
        // Implementation for setting up exhibitor form
        // This would include hall selection, sponsor checks, etc.
    }

    async setupSpeakerForm(eventOutput, eventHallAccess) {
        // Implementation for setting up speaker form
        // This would include hall selection, sponsor checks, etc.
    }

    async processEventBaseInfo(requestData, response) {
        // Implementation for processing event base info
        // This would include banner setup, venue info, ticket types, etc.
    }
}

// Global functions that need to be accessible
window.updateraffle = function() {
    if ($("input[name='exh_raffles']:checked").val() == '1') {
        $('#exh_raffle_options').show();
    } else {
        $('#exh_raffles_timebound_no').prop('checked', true).trigger('change');
        $('#exh_raffle_time_bound_time').hide();
        $('#exh_raffle_options').hide();
    }
};

window.updateraffletimebound = function() {
    if ($("input[name='exh_raffles_timebound_option']:checked").val() == '1') {
        $('#exh_raffle_time_bound_time').show();
    } else {
        $('#exh_raffle_time_bound_time').hide();
    }
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatEvents;
}

// Auto-initialize when DOM is ready
$(document).ready(function() {
    // Configuration object should be populated from PHP
    if (typeof window.chatEventsConfig !== 'undefined') {
        window.chatEventsInstance = new ChatEvents(window.chatEventsConfig);
    }
});