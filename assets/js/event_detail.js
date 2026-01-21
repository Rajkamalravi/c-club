/**
 * Events Detail Page JavaScript
 * Handles event display, ticket selection, RSVP functionality, and interactive features
 */

class EventsDetailPage {
    constructor(config) {
        this.config = {
            isLoggedIn: false,
            isValidUser: false,
            eventToken: '',
            userPToken: '',
            trackingToken: '',
            successDiscountAmt: '',
            userLikedAlready: '0',
            ...config
        };
        
        this.state = {
            eventData: null,
            isUserRSVPDone: false,
            showRSVPTicket: false,
            eventLiveState: 'before'
        };
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeCarousel();
        this.loadEventData();
        
        if (this.config.isLoggedIn) {
            this.saveMetrics();
        }
        
        this.handleURLParameters();
    }

    bindEvents() {
        // Ticket selection
        $(document).on('click', '#ticket_list li.ticket-item', this.handleTicketSelection.bind(this));
        
        // Event save/like functionality
        $(document).on('click', '.event_save', this.handleEventSave.bind(this));
        
        // Share modal events
        $(document).on('click', "[data-target='#shareModal']", this.handleShareModal.bind(this));
        
        // Modal events
        this.bindModalEvents();
    }

    bindModalEvents() {
        // Upgrade modal events
        $('#unlockDiscount, #upgradeExplorerPlus').on('show.bs.modal', () => {
            this.trackModalView('upgrade');
        });
        
        // Share modal events
        $('#share').on('show.bs.modal', () => {
            this.trackModalView('share');
        });
    }

    initializeCarousel() {
        $('#event_gallery_carousel').carousel({
            interval: false,
            keyboard: true,
            pause: 'hover'
        });

        // Hide carousel controls if only one item
        setTimeout(() => {
            const totalItems = $('#event_gallery_carousel .carousel-item').length;
            if (totalItems <= 1) {
                $('#event_gallery_carousel .carousel-control-prev, .carousel-control-next').hide();
            }
        }, 1000);
    }

    async loadEventData() {
        try {
            $('.aw').awloader('show');
            
            const response = await this.getEventBaseInfo({
                eventtoken: this.config.eventToken
            });
            
            if (response.success) {
                this.processEventBaseInfo(response);
            } else {
                this.handleEventLoadError();
            }
        } catch (error) {
            console.error('Error loading event data:', error);
            this.handleEventLoadError();
        } finally {
            $('.aw').awloader('hide');
        }
    }

    async getEventBaseInfo(requestData) {
        const taohVals = {
            token: this.getDummyToken(),
            ops: 'baseinfo',
            mod: 'events',
            eventtoken: requestData.eventtoken,
            cache_name: 'event_detail_' + requestData.eventtoken
        };

        return await this.apiCall('events.event.get', taohVals);
    }

    processEventBaseInfo(response) {
        const eventOutput = response.output;
        const conttokenData = eventOutput.conttoken;
        
        if (!conttokenData) return;

        this.state.eventData = conttokenData;
        
        // Update page elements
        this.updateEventTitle(conttokenData.title);
        this.updateEventImages(conttokenData);
        this.updateEventDateTime(eventOutput);
        this.updateEventVenue(conttokenData);
        this.updateEventDescription(conttokenData);
        this.updateTicketTypes(conttokenData);
        this.updateEventLikeButton(conttokenData);
        
        // Load additional data
        this.loadEventSponsor(eventOutput.eventtoken);
        this.loadEventMetaInfo(eventOutput.eventtoken);
        this.loadEventsHall(eventOutput.eventtoken);
        
        // Handle special features
        this.handleOrganizerFeatures(conttokenData);
        this.handleSponsorFeatures(conttokenData);
        
        setTimeout(() => {
            this.handleEventStatusUpdates();
        }, 2000);
    }

    updateEventTitle(title) {
        $('.event_title').text(this.displayFormatted(title));
        document.title = title;
    }

    updateEventImages(conttokenData) {
        const allBanners = [
            conttokenData.event_video,
            conttokenData.event_image,
            ...(conttokenData.more_banner || [])
        ];
        
        const validBanners = allBanners
            .filter(url => url && url.trim() !== "" && this.isValidURL(url))
            .map(url => ({
                src: url,
                type: this.getMediaType(url)
            }));

        this.renderEventGallery(validBanners);
    }

    renderEventGallery(banners) {
        const galleryContainer = document.getElementById("event_banner_container");
        
        if (banners.length === 0) {
            // Default image
            const defaultImage = `${this.config.siteUrl}/assets/images/event.jpg`;
            galleryContainer.innerHTML = this.createImageCarouselItem(defaultImage, 0, true);
            return;
        }

        banners.forEach((media, index) => {
            const isActive = index === 0;
            let itemHtml = '';
            
            if (media.type === "image") {
                itemHtml = this.createImageCarouselItem(media.src, index, isActive);
            } else if (media.type === "video") {
                itemHtml = this.createVideoCarouselItem(media.src, index, isActive);
            }
            
            galleryContainer.innerHTML += itemHtml;
        });
    }

    createImageCarouselItem(src, index, isActive) {
        return `
            <div class="carousel-item ${isActive ? 'active' : ''}">
                <div class="cover-event-image">
                    <div class="events-bg" style="background-image: url('${src}');"></div>
                    <div class="glass-overlay"></div>
                    <img src="${src}" class="detail-main-image" alt="Event">                                
                </div>
            </div>
        `;
    }

    createVideoCarouselItem(src, index, isActive) {
        const formattedSrc = this.formatVideoSrc(src);
        return `
            <div class="carousel-item ${isActive ? 'active' : ''}">
                <iframe src="${formattedSrc}" class="main-media" allowfullscreen allow="autoplay"></iframe>
            </div>
        `;
    }

    formatVideoSrc(videoSrc) {
        if (videoSrc.includes("youtube.com")) {
            const videoId = videoSrc.split("v=")[1]?.split("&")[0];
            return `https://www.youtube.com/embed/${videoId}`;
        }
        if (videoSrc.includes("youtu.be")) {
            const videoId = videoSrc.split("youtu.be/")[1];
            return `https://www.youtube.com/embed/${videoId}`;
        }
        if (videoSrc.includes("vimeo.com")) {
            const videoId = videoSrc.split("vimeo.com/")[1];
            return `https://player.vimeo.com/video/${videoId}`;
        }
        return videoSrc;
    }

    updateEventDateTime(eventOutput) {
        const userTimezone = this.getUserTimezone();
        
        const startData = {
            utc_datetime: eventOutput.utc_start_at,
            local_datetime: eventOutput.local_start_at,
            timezone: eventOutput.local_timezone,
            locality: eventOutput.locality
        };
        
        const endData = {
            utc_datetime: eventOutput.utc_end_at,
            local_datetime: eventOutput.local_end_at,
            timezone: eventOutput.local_timezone,
            locality: eventOutput.locality
        };

        const localizedStart = this.getLocalizedEventData(startData, userTimezone);
        const localizedEnd = this.getLocalizedEventData(endData, userTimezone);
        
        const formattedDateTime = this.formatEventDateTime(localizedStart, localizedEnd);
        $('#event_start_end_datetime').text(formattedDateTime);
        
        // Update event live state
        this.state.eventLiveState = this.getEventLiveState(
            eventOutput.utc_start_at,
            eventOutput.utc_end_at,
            eventOutput.locality,
            userTimezone
        );
    }

    updateEventVenue(conttokenData) {
        const eventType = (conttokenData?.event_type || 'virtual').toLowerCase();
        let venueHtml = '';
        
        switch (eventType) {
            case 'in-person':
                venueHtml = this.createInPersonVenueHtml(conttokenData);
                break;
            case 'hybrid':
                venueHtml = this.createHybridVenueHtml(conttokenData);
                break;
            default:
                venueHtml = this.createVirtualVenueHtml();
        }
        
        $('#event_venue_info').html(venueHtml);
    }

    createInPersonVenueHtml(conttokenData) {
        const venueLink = conttokenData.map_link 
            ? `<a href="${conttokenData.map_link}" target="_blank" class="cursor-pointer text-underline">${conttokenData.venue}</a>`
            : conttokenData.venue;
            
        return `<span class="theme-blue-clr">In-Person, <span>${venueLink}</span></span>`;
    }

    createHybridVenueHtml(conttokenData) {
        const venueLink = conttokenData.map_link 
            ? `<a href="${conttokenData.map_link}" target="_blank" class="cursor-pointer text-underline">${conttokenData.venue}</a>`
            : conttokenData.venue;
            
        return `<span class="theme-blue-clr">Hybrid - <span>${venueLink}</span> or Virtual</span>`;
    }

    createVirtualVenueHtml() {
        return `<span class="theme-blue-clr">Virtual</span>`;
    }

    updateEventDescription(conttokenData) {
        let descriptionHtml = '';
        
        if (conttokenData.description && conttokenData.description.trim()) {
            descriptionHtml += '<h3>About this Event</h3>';
            descriptionHtml += `<div>${this.decodeDescription(conttokenData.description)}</div>`;
        }
        
        if (conttokenData.about_you && conttokenData.about_you.trim()) {
            descriptionHtml += '<h3>About the Host</h3>';
            descriptionHtml += `<div>${this.decodeDescription(conttokenData.about_you)}</div>`;
        }
        
        $('.event_description').html(descriptionHtml);
    }

    updateTicketTypes(conttokenData) {
        if (!this.config.isLoggedIn) {
            this.renderLoginPrompt(conttokenData.title);
            return;
        }

        const isEventFreeze = conttokenData.freeze_option === 1;
        const ticketTypes = conttokenData.ticket_types || [];
        
        if (isEventFreeze) {
            this.renderEventSuspended();
            return;
        }

        if (this.state.eventLiveState === 'after') {
            this.renderEventEnded();
            return;
        }

        if (!this.state.isUserRSVPDone && 
            (this.state.eventLiveState === 'before' || this.state.eventLiveState === 'live')) {
            this.renderTicketDropdown(ticketTypes);
        } else {
            this.renderEventStatus();
        }
    }

    renderTicketDropdown(ticketTypes) {
        let html = '<div class="dropdown w-100">';
        html += `<button class="btn ${this.state.eventLiveState === 'live' ? 'btn-success' : 'btn-primary'} dropdown-toggle w-100" type="button" id="choose_ticket" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">`;
        html += `${this.state.eventLiveState === 'live' ? 'LIVE NOW! ' : ''}Choose a ticket to Register</button>`;
        html += '<ul class="ticket-list w-100 dropdown-menu px-3 light-dark" id="ticket_list" aria-labelledby="choose_ticket">';

        ticketTypes.forEach(ticketType => {
            if (ticketType.visibility === 'hidden') return;
            
            html += this.createTicketListItem(ticketType);
        });

        html += '</ul></div>';
        
        $('.ticket-card-div').html(html);
    }

    createTicketListItem(ticketType) {
        const classes = `rsvp_ticket_${ticketType.title} rsvp_tickets ticket-item`;
        const costText = ticketType.price === 'paid' 
            ? `Costs you $${ticketType.cost}` 
            : 'Free';

        return `
            <li class="${classes}">
                <input type="radio" name="ticket" id="${ticketType.slug}" value="${encodeURIComponent(ticketType.title)}" class="rsvp_ticket_${ticketType.title} rsvp_tickets d-none">
                <label for="${ticketType.slug}" class="item btn w-100">
                    <p class="item-title">${ticketType.title}</p>
                    <p class="item-cost">${costText}</p>
                </label>
            </li>
        `;
    }

    renderLoginPrompt(eventTitle) {
        const encodedTitle = btoa(unescape(encodeURIComponent(eventTitle)));
        const html = `
            <button type="button" class="mt-3 mb-2 btn btn-primary w-100 create_referral" 
                    data-location="${location.href}" 
                    data-title="${encodedTitle}" 
                    data-toggle="modal" 
                    data-target="#config-modal">
                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Login & Register Now
            </button>
        `;
        $('.ticket-card-div').html(html);
    }

    renderEventSuspended() {
        const html = `
            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="suspended" value="3"/>
            <a href="${this.config.currAppUrl}" class="btn btn-secondary w-100">
                <i class="fa fa-calendar-times mr-2" aria-hidden="true"></i>Event Suspended
            </a>
        `;
        $('.ticket-card-div').html(html);
    }

    renderEventEnded() {
        const html = `
            <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="ended" value="0"/>
            <a href="${this.config.currAppUrl}" class="btn btn-secondary w-100">
                <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Ended
            </a>
        `;
        $('.ticket-card-div').html(html);
    }

    renderEventStatus() {
        let html = '';
        const redirectTo = !this.config.isValidUser 
            ? `${this.config.siteUrl}/settings` 
            : `${this.config.currAppUrl}/chat/id/events/${this.config.eventToken}`;

        if (this.state.isUserRSVPDone && this.state.eventLiveState === 'live') {
            html = `
                <input type="hidden" name="event_status_hidden" id="event_status_hidden" live="live" value="1"/>
                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="live" value="1"/>
                <a href="${redirectTo}" class="btn live w-100">
                    <i class="fa fa-ticket mr-2" aria-hidden="true"></i>Event Live, ${!this.config.isValidUser ? 'Complete settings to' : 'Click to'} Join
                </a>
            `;
        } else if (this.state.isUserRSVPDone) {
            html = `
                <input type="hidden" name="rsvp_status_hidden" id="rsvp_status_hidden" live="before" value="1"/>
                <a href="${this.config.currAppUrl}/chat/id/events/${this.config.eventToken}" class="btn btn-warning w-100">
                    <i class="fa fa-ticket mr-2" aria-hidden="true"></i>You have registered
                </a>
            `;
        }

        $('.ticket-card-div').html(html);
    }

    updateEventLikeButton(conttokenData) {
        const isLiked = this.config.userLikedAlready === '1';
        const iconHtml = isLiked 
            ? this.createLikedButtonHtml(conttokenData.conttoken)
            : this.createUnlikedButtonHtml(conttokenData.conttoken);
            
        $('#event_like_btn').html(iconHtml);
    }

    createLikedButtonHtml(conttoken) {
        return `
            <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" 
                 data-event="${this.config.eventToken}" data-cont="${conttoken}" class="event_saved" title="Save Event">
                <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" 
                      fill="#000000" stroke="white"/>
            </svg>
        `;
    }

    createUnlikedButtonHtml(conttoken) {
        return `
            <svg width="20" height="20" viewBox="0 0 20 27" fill="none" xmlns="http://www.w3.org/2000/svg" 
                 data-event="${this.config.eventToken}" data-cont="${conttoken}" class="event_save" title="Save Event">
                <path d="M2.5 0.5H17.5C18.6041 0.5 19.5 1.39593 19.5 2.5V25.4014C19.4998 25.823 19.156 26.167 18.7344 26.167C18.5737 26.167 18.4201 26.1185 18.2939 26.0293L18.292 26.0283L10.2871 20.4238L10 20.2227L9.71289 20.4238L1.70801 26.0283L1.70605 26.0293C1.57991 26.1185 1.4263 26.167 1.26562 26.167C0.843959 26.167 0.500177 25.823 0.5 25.4014V2.5C0.5 1.39593 1.39593 0.5 2.5 0.5Z" 
                      fill="" stroke="white"/>
            </svg>
        `;
    }

    handleTicketSelection(event) {
        event.preventDefault();
        
        $('.hall_tabs .nav-item').css('pointer-events', 'none');
        
        const currentElem = $(event.currentTarget);
        const inputElem = currentElem.find('input[type="radio"]');
        const ticketTitle = currentElem.find('label .item-title').text();
        
        if (inputElem.prop('disabled')) {
            this.showErrorMessage(`${ticketTitle} ticket is not available for selection`);
            return;
        }
        
        if (!this.config.isLoggedIn) {
            this.showErrorMessage('Please log in to select a ticket');
            return;
        }
        
        const selectedTicket = inputElem.val();
        if (!selectedTicket) {
            this.showErrorMessage('Please select a ticket');
            return;
        }
        
        // Show loading state
        $('#choose_ticket')
            .removeClass('dropdown-toggle')
            .html('<i class="fa fa-spinner fa-spin"></i> Loading...');
        
        // Redirect to RSVP page
        const rsvpUrl = `${this.config.siteUrl}/events/add_rsvp/${this.config.eventToken}/${selectedTicket}/${this.config.encodeCurrentUrl}`;
        window.location.href = rsvpUrl;
    }

    handleEventSave(event) {
        event.stopPropagation();
        
        if (!this.config.isLoggedIn) {
            this.showErrorMessage('Login to perform the action.');
            return;
        }
        
        const saveToken = $(event.currentTarget).attr('data-event');
        const contToken = $(event.currentTarget).attr('data-cont');
        
        // Update UI immediately
        this.updateSaveButtonState(contToken, true);
        
        // Save to localStorage
        localStorage.setItem(`events_${saveToken}_${contToken}_liked`, '1');
        
        // Delete cache
        this.deleteEventsCache(`event_detail_${saveToken}`);
        
        // Send API request
        this.saveEventToServer(saveToken, contToken);
    }

    updateSaveButtonState(contToken, isSaved) {
        const selector = `[data-cont='${contToken}']`;
        const button = $(selector);
        
        if (isSaved) {
            button.removeClass('event_save').addClass('event_saved already-saved');
            button.parent().addClass('already-saved');
        } else {
            button.removeClass('event_saved already-saved').addClass('event_save');
            button.parent().removeClass('already-saved');
        }
    }

    async saveEventToServer(saveToken, contToken) {
        try {
            const data = {
                taoh_action: 'event_like_put',
                eventtoken: saveToken,
                contttoken: contToken,
                ptoken: this.config.userPToken
            };
            
            const response = await $.post(this.config.ajaxUrl, data);
            
            if (response.success) {
                this.showSuccessMessage('Event Saved Successfully.');
            } else {
                this.showErrorMessage('Event Save Failed.');
                // Revert UI state on failure
                this.updateSaveButtonState(contToken, false);
            }
        } catch (error) {
            console.error('Error saving event:', error);
            this.showErrorMessage('Network error. Please try again.');
            this.updateSaveButtonState(contToken, false);
        }
    }

    handleShareModal(event) {
        const $this = $(event.currentTarget);
        
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
        
        const shareUrl = $this.data('url');
        if (shareUrl) {
            this.currentShareLink = shareUrl;
        }
    }

    handleOrganizerFeatures(conttokenData) {
        const eventOrganizerPtokens = (conttokenData.event_organizer_ptokens || "")
            .split(',')
            .map(token => token.trim())
            .filter(token => token);
            
        eventOrganizerPtokens.push(conttokenData.ptoken);
        eventOrganizerPtokens.push(this.config.superOrganizerToken);
        
        const isOrganizer = eventOrganizerPtokens.includes(this.config.userPToken);
        
        $('#is_organizer').val(isOrganizer ? 1 : 0);
        
        if (isOrganizer) {
            $('#download_rsvp, #email_rsvp').show();
            
            if (conttokenData.enable_exhibitor_hall === "1") {
                $('#setup_exhibitor_slot').show();
            }
            
            if (conttokenData.enable_speaker_hall === "1") {
                $('#setup_speaker_slot').show();
            }
        } else {
            $('#download_rsvp, #email_rsvp').hide();
        }
    }

    handleSponsorFeatures(conttokenData) {
        const eventSponsorLevels = conttokenData.event_sponsor_levels || {};
        const eventTicketTypes = conttokenData.ticket_types || {};
        const eventFormVersion = conttokenData.event_form_version ?? 1;
        const isSocialShareEnabled = conttokenData.event_social_sharing;
        
        // Construct sponsor popup
        this.constructSponsorInfoPopup(
            this.config.eventToken,
            eventSponsorLevels,
            this.config.userProfileType,
            conttokenData.org_email,
            this.config.socialToken,
            eventTicketTypes,
            eventFormVersion,
            isSocialShareEnabled,
            this.config.trackingToken,
            this.config.isLoggedIn
        );
    }

    handleEventStatusUpdates() {
        const eventStatus = $('#event_status_hidden').val();
        const eventSponsorStatusList = this.getEventSponsorStatusList();
        
        if ((!this.config.isLoggedIn || eventStatus == 2) && eventSponsorStatusList.includes(1)) {
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
        
        this.handleEventAccess(eventStatus);
    }

    handleEventAccess(eventStatus) {
        if (eventStatus == 1 || this.config.userPToken == this.config.superOrganizerToken) {
            $('.speaker-banner, .exhibitor-banner').hide();
        } else {
            $('.speaker-banner, .exhibitor-banner').show();
            $('.rsvp_actions').css('display', 'none');
            
            $("#rsvp_default_list").show();
            
            if ($("#is_organizer").val() == 1) {
                $('.rsvp_actions').show();
            }
            
            if (this.state.isUserRSVPDone) {
                $("#register_now").hide();
            }
        }
    }

    handleURLParameters() {
        const url = new URL(window.location.href);
        
        if (url.searchParams.has('confirmation')) {
            this.handleConfirmationParams(url);
        }
        
        // Handle special sharing parameters
        if (this.config.refSlug && this.config.successDiscountAmt && this.config.trackingToken) {
            this.handleSharingDiscount();
        }
    }

    handleConfirmationParams(url) {
        const confirmation = url.searchParams.get('confirmation');
        const status = url.searchParams.get('status');
        
        if (confirmation === 'sponsor') {
            this.clearSponsorCache();
            
            if (status === 'success') {
                this.showSuccessMessage('Thank you for sponsoring this event.');
            } else if (url.searchParams.get('delete') === 'success') {
                this.showSuccessMessage('Sponsor deleted successfully!');
            } else if (status === 'limitexceed') {
                this.showErrorMessage('You exceed the limit! You are allowed to add one sponsor only.');
            } else if (status === 'nosponsortype') {
                this.showErrorMessage('Please select the sponsor type to get started with adding new sponsor.');
            } else {
                this.showErrorMessage('There was an error processing your request. Please try again later.');
            }
        }
        
        // Clean URL
        this.cleanURLParameters(url);
    }

    cleanURLParameters(url) {
        url.searchParams.delete('confirmation');
        url.searchParams.delete('status');
        url.searchParams.delete('tickettoken');
        url.searchParams.delete('delete');
        window.history.pushState({}, '', url.toString());
    }

    handleSharingDiscount() {
        if (this.config.trackingToken) {
            this.getEventSponsorForShare(this.config.eventToken, this.config.trackingToken)
                .then(() => {
                    // Handle successful sponsor share
                })
                .catch(() => {
                    // Handle error
                });
        }
        
        // Update URL to remove tracking parameters
        const newUrl = this.config.originalLink;
        history.replaceState(null, "", newUrl);
    }

    // Utility methods
    saveMetrics() {
        if (typeof save_metrics === 'function') {
            save_metrics('events', this.config.clickView, this.config.eventToken);
        }
    }

    getUserTimezone() {
        if (this.config.isLoggedIn && this.config.userTimezone) {
            return this.config.userTimezone;
        }
        
        const clientTimezone = this.getCookie('client_time_zone') || 
                              Intl.DateTimeFormat().resolvedOptions().timeZone;
        
        return this.convertDeprecatedTimeZone(clientTimezone) || 'UTC';
    }

    isValidURL(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    getMediaType(url) {
        const videoPatterns = [
            /youtube\.com/,
            /youtu\.be/,
            /vimeo\.com/,
            /\.mp4$/i,
            /\.mov$/i,
            /\.avi$/i
        ];
        
        return videoPatterns.some(pattern => pattern.test(url)) ? 'video' : 'image';
    }

    displayFormatted(text) {
        // Implement text formatting logic
        return text;
    }

    decodeDescription(description) {
        // Implement description decoding logic
        return description;
    }

    getLocalizedEventData(eventData, timezone) {
        // Implement timezone conversion logic
        return eventData;
    }

    formatEventDateTime(startData, endData) {
        // Implement datetime formatting logic
        return `${startData.datetime} - ${endData.datetime}`;
    }

    getEventLiveState(startTime, endTime, locality, timezone) {
        // Implement live state calculation
        const now = new Date();
        const start = new Date(startTime);
        const end = new Date(endTime);
        
        if (now < start) return 'before';
        if (now > end) return 'after';
        return 'live';
    }

    async apiCall(endpoint, data) {
        // Implement API call logic
        return new Promise((resolve, reject) => {
            // Placeholder for actual API implementation
            resolve({ success: true, output: {} });
        });
    }

    getDummyToken() {
        return this.config.dummyToken || 'dummy-token';
    }

    getCookie(name) {
        // Implement cookie retrieval logic
        return null;
    }

    convertDeprecatedTimeZone(timezone) {
        // Implement timezone conversion logic
        return timezone;
    }

    deleteEventsCache(cacheKey) {
        // Implement cache deletion logic
        if (typeof delete_events_into === 'function') {
            delete_events_into(cacheKey);
        }
    }

    clearSponsorCache() {
        this.deleteEventsCache(`event_details_sponsor_${this.config.eventToken}`);
        this.deleteEventsCache(`event_MetaInfo_${this.config.eventToken}`);
    }

    getEventSponsorStatusList() {
        // Implement sponsor status list logic
        return [0, 1]; // Placeholder
    }

    trackModalView(modalType) {
        // Implement modal tracking logic
        console.log(`Modal viewed: ${modalType}`);
    }

    showSuccessMessage(message) {
        if (typeof taoh_set_success_message === 'function') {
            taoh_set_success_message(message);
        } else {
            alert(message);
        }
    }

    showErrorMessage(message) {
        if (typeof taoh_set_error_message === 'function') {
            taoh_set_error_message(message);
        } else {
            alert(message);
        }
    }

    // Async methods for external API calls
    async loadEventSponsor(eventToken) {
        if (typeof getEventSponsor === 'function') {
            return getEventSponsor(eventToken);
        }
    }

    async loadEventMetaInfo(eventToken) {
        if (typeof getEventMetaInfo === 'function') {
            setTimeout(() => getEventMetaInfo(eventToken), 3000);
        }
    }

    async loadEventsHall(eventToken) {
        if (typeof getEventsHall === 'function') {
            setTimeout(() => getEventsHall(eventToken), 2000);
        }
    }

    async constructSponsorInfoPopup(...args) {
        if (typeof constructSponsorInfoPopup === 'function') {
            return constructSponsorInfoPopup(...args);
        }
    }

    async getEventSponsorForShare(eventToken, trackingToken) {
        if (typeof getEventSponsorForShare === 'function') {
            return getEventSponsorForShare(eventToken, trackingToken);
        }
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    // This will be populated by the PHP page with actual configuration
    const eventPageConfig = window.eventPageConfig || {};
    
    // Initialize the events detail page
    window.eventsDetailPage = new EventsDetailPage(eventPageConfig);
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EventsDetailPage;
}