/**
 * Events Detail Page JavaScript
 * Handles dynamic content loading and user interactions for event detail pages
 */

(function() {
    'use strict';

    // Global variables
    let eventData = {};
    let isInitialized = false;

    /**
     * Initialize the events detail page
     */
    function initEventDetail() {
        if (isInitialized) return;

        console.log('Initializing Events Detail Page...');

        // Check if config is available
        if (typeof window.eventPageConfig === 'undefined') {
            console.error('Event page configuration not found');
            return;
        }

        const config = window.eventPageConfig;

        // Load event data and populate the page
        loadEventData(config);

        // Initialize event handlers
        initEventHandlers(config);

        // Handle RSVP ticket modal if needed
        if (config.showRSVPTicket && config.rsvpTicketToken) {
            showRSVPTicketModal();
        }

        isInitialized = true;
        console.log('Events Detail Page initialized successfully');
    }

    /**
     * Load event data and populate page elements
     */
    function loadEventData(config) {
        // Get cached event data or fetch from server
        const eventDetailKey = `event_detail_${config.eventToken}`;

        if (typeof IntaoDB !== 'undefined') {
            IntaoDB.getItem('event_store', eventDetailKey).then((data) => {
                if (data && data.values) {
                    populateEventDetails(data.values);
                } else {
                    fetchEventData(config.eventToken);
                }
            }).catch(() => {
                fetchEventData(config.eventToken);
            });
        } else {
            fetchEventData(config.eventToken);
        }
    }

    /**
     * Fetch event data from server
     */
    function fetchEventData(eventToken) {
        if (typeof $ === 'undefined') {
            console.error('jQuery not loaded');
            return;
        }

        // Use the global AJAX variables that are already defined in the page
        const ajaxUrl = typeof _taoh_site_ajax_url !== 'undefined' ? _taoh_site_ajax_url : window.eventPageConfig.ajaxUrl;
        const ajaxToken = typeof _taoh_ajax_token !== 'undefined' ? _taoh_ajax_token : window.eventPageConfig.dummyToken;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_event_details',
                taoh_action: 'get_event_details',
                token: ajaxToken,
                eventtoken: eventToken,
                ops: 'baseinfo',
                mod: 'events'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateEventDetails(response);

                    // Cache the data if IntaoDB is available
                    if (typeof IntaoDB !== 'undefined') {
                        const eventDetailKey = `event_detail_${eventToken}`;
                        IntaoDB.setItem('event_store', {
                            taoh_data: eventDetailKey,
                            values: response,
                            timestamp: Date.now()
                        });
                    }
                } else {
                    console.error('Failed to fetch event data:', response.message || 'Unknown error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching event data:', error);
            }
        });
    }

    /**
     * Populate event details on the page
     */
    function populateEventDetails(data) {
        if (!data.output || !data.output.conttoken) {
            console.error('Invalid event data structure');
            return;
        }

        const eventInfo = data.output.conttoken;
        eventData = eventInfo;

        // Update page title elements
        updateEventTitle(eventInfo.title);

        // Update event datetime and venue
        updateEventDateTime(eventInfo);
        updateEventVenue(eventInfo);

        // Update event description
        updateEventDescription(eventInfo.description);

        // Update event banner/gallery
        updateEventGallery(eventInfo);

        // Update ticket information
        updateTicketCards(eventInfo.ticket_types || []);

        // Update like button
        updateLikeButton();

        // Note: Initial data loading is now handled by events_lobby_hall.php
        // The existing system will call getEventMetaInfo() automatically on page load
    }

    /**
     * Update event title elements
     */
    function updateEventTitle(title) {
        if (!title) return;

        const formattedTitle = title.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

        // Update all title elements
        $('.event_title, #event_title1, .event-title').text(formattedTitle);

        // Update page title
        document.title = formattedTitle;
    }

    /**
     * Update event date and time information
     */
    function updateEventDateTime(eventInfo) {
        if (!eventInfo.event_start_date || !eventInfo.event_start_time) return;

        const timezone = window.eventPageConfig.userTimezone || 'UTC';
        const startDateTime = new Date(`${eventInfo.event_start_date} ${eventInfo.event_start_time}`);
        const endDateTime = eventInfo.event_end_date && eventInfo.event_end_time ?
            new Date(`${eventInfo.event_end_date} ${eventInfo.event_end_time}`) : null;

        let dateTimeString = formatDateTime(startDateTime, timezone);
        if (endDateTime) {
            dateTimeString += ' - ' + formatDateTime(endDateTime, timezone);
        }

        $('#event_start_end_datetime').text(dateTimeString);
    }

    /**
     * Update venue information
     */
    function updateEventVenue(eventInfo) {
        const venue = eventInfo.event_venue || eventInfo.venue || 'Virtual Event';
        $('#event_venue_info').text(venue);
    }

    /**
     * Update event description
     */
    function updateEventDescription(description) {
        if (!description) return;

        const cleanDescription = description.replace(/&lt;/g, '<').replace(/&gt;/g, '>');
        $('#event_description_all, #event_description1').html(cleanDescription);
    }

    /**
     * Update event gallery/banner
     */
    function updateEventGallery(eventInfo) {
        const images = eventInfo.event_images || [];
        const mainImage = eventInfo.event_image;

        let galleryHtml = '';

        if (images.length > 0) {
            images.forEach((image, index) => {
                const activeClass = index === 0 ? 'active' : '';
                galleryHtml += `
                    <div class="carousel-item ${activeClass}">
                        <img src="${image}" class="d-block w-100" alt="Event Image ${index + 1}">
                    </div>
                `;
            });
        } else if (mainImage) {
            galleryHtml = `
                <div class="carousel-item active">
                    <img src="${mainImage}" class="d-block w-100" alt="Event Image">
                </div>
            `;
        } else {
            galleryHtml = `
                <div class="carousel-item active">
                    <img src="${window.eventPageConfig.siteUrl}/assets/images/event.jpg" class="d-block w-100" alt="Default Event Image">
                </div>
            `;
        }

        $('#event_banner_container').html(galleryHtml);
    }

    /**
     * Update ticket cards
     */
    function updateTicketCards(ticketTypes) {
        if (!Array.isArray(ticketTypes) || ticketTypes.length === 0) return;

        let ticketHtml = '';

        ticketTypes.forEach(ticket => {
            const price = ticket.price && ticket.price > 0 ? `$${ticket.price}` : 'Free';
            const buttonText = window.eventPageConfig.isUserRSVPDone ? 'Registered' : 'Register';
            const buttonClass = window.eventPageConfig.isUserRSVPDone ? 'btn-success' : 'btn-primary';

            ticketHtml += `
                <div class="ticket-card">
                    <div class="ticket-type">${ticket.title || 'General'}</div>
                    <div class="ticket-price">${price}</div>
                    <button class="btn ${buttonClass} ticket-btn" data-ticket-id="${ticket.id || ''}" ${window.eventPageConfig.isUserRSVPDone ? 'disabled' : ''}>
                        ${buttonText}
                    </button>
                </div>
            `;
        });

        $('.ticket-card-div').html(ticketHtml);
    }

    /**
     * Update like button
     */
    function updateLikeButton() {
        const isLiked = window.eventPageConfig.userLikedAlready === '1';
        const likeIcon = isLiked ? 'fas fa-heart' : 'far fa-heart';
        const likeText = isLiked ? 'Liked' : 'Like';

        $('#event_like_btn').html(`
            <i class="${likeIcon}"></i> ${likeText}
        `);
    }

    /**
     * Initialize event handlers
     */
    function initEventHandlers(config) {
        // Like button handler
        $(document).on('click', '.events_like', function() {
            toggleEventLike();
        });

        // Ticket registration handler
        $(document).on('click', '.ticket-btn', function() {
            if (!$(this).prop('disabled')) {
                const ticketId = $(this).data('ticket-id');
                handleTicketRegistration(ticketId);
            }
        });

        // Share modal handlers
        $(document).on('click', '.share-btn', function() {
            updateShareModal();
        });

        // Social sharing handlers
        $(document).on('click', '[data-social-share]', function() {
            const platform = $(this).data('social-share');
            handleSocialShare(platform);
        });
    }

    /**
     * Toggle event like status
     */
    function toggleEventLike() {
        if (!window.eventPageConfig.isLoggedIn) {
            alert('Please login to like this event');
            return;
        }

        // Use the global AJAX variables
        const ajaxUrl = typeof _taoh_site_ajax_url !== 'undefined' ? _taoh_site_ajax_url : window.eventPageConfig.ajaxUrl;
        const ajaxToken = typeof _taoh_ajax_token !== 'undefined' ? _taoh_ajax_token : window.eventPageConfig.dummyToken;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'toggle_event_like',
                taoh_action: 'toggle_event_like',
                token: ajaxToken,
                eventtoken: window.eventPageConfig.eventToken,
                conttoken: window.eventPageConfig.eventToken,
                slug: 'events'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Toggle the like status
                    const currentLiked = window.eventPageConfig.userLikedAlready === '1';
                    window.eventPageConfig.userLikedAlready = currentLiked ? '0' : '1';
                    updateLikeButton();
                }
            },
            error: function() {
                console.error('Error toggling like status');
            }
        });
    }

    /**
     * Handle ticket registration
     */
    function handleTicketRegistration(ticketId) {
        if (!window.eventPageConfig.isLoggedIn) {
            alert('Please login to register for this event');
            return;
        }

        // Redirect to registration/RSVP page
        const rsvpUrl = `${window.eventPageConfig.currAppUrl}/rsvp/${window.eventPageConfig.eventToken}`;
        window.location.href = rsvpUrl;
    }

    /**
     * Update share modal with current event info
     */
    function updateShareModal() {
        const shareLink = window.location.href;
        const eventTitle = eventData.title || 'Event';

        // Update social sharing links
        const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareLink)}`;
        const twitterUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(shareLink)}&text=${encodeURIComponent(eventTitle)}`;
        const linkedinUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareLink)}`;

        $('#share .modal-body a').each(function() {
            const $this = $(this);
            if ($this.find('.fb').length) {
                $this.attr('href', facebookUrl);
            } else if ($this.find('svg circle[fill="black"]').length) {
                $this.attr('href', twitterUrl);
            } else if ($this.find('svg circle[fill="#0A66C2"]').length) {
                $this.attr('href', linkedinUrl);
            }
        });
    }

    /**
     * Handle social sharing
     */
    function handleSocialShare(platform) {
        // Track sharing event
        if (window.eventPageConfig.trackingToken) {
            const ajaxUrl = typeof _taoh_site_ajax_url !== 'undefined' ? _taoh_site_ajax_url : window.eventPageConfig.ajaxUrl;
            const ajaxToken = typeof _taoh_ajax_token !== 'undefined' ? _taoh_ajax_token : window.eventPageConfig.dummyToken;

            $.post(ajaxUrl, {
                action: 'track_social_share',
                taoh_action: 'track_social_share',
                token: ajaxToken,
                eventtoken: window.eventPageConfig.eventToken,
                platform: platform,
                tracking_token: window.eventPageConfig.trackingToken
            });
        }
    }

    /**
     * Show RSVP ticket modal
     */
    function showRSVPTicketModal() {
        // Load ticket information and show modal
        const ajaxUrl = typeof _taoh_site_ajax_url !== 'undefined' ? _taoh_site_ajax_url : window.eventPageConfig.ajaxUrl;
        const ajaxToken = typeof _taoh_ajax_token !== 'undefined' ? _taoh_ajax_token : window.eventPageConfig.dummyToken;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_rsvp_ticket',
                taoh_action: 'get_rsvp_ticket',
                token: ajaxToken,
                tickettoken: window.eventPageConfig.rsvpTicketToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateRSVPTicketModal(response.output);
                    $('#rsvpTicketModal').modal('show');
                }
            }
        });
    }

    /**
     * Populate RSVP ticket modal
     */
    function populateRSVPTicketModal(ticketData) {
        const modalBody = $('#rsvpTicketModal .modal-body');
        const modalFooter = $('#rsvpTicketModal .modal-footer');

        // Generate ticket HTML
        let ticketHtml = `
            <div class="ticket-container">
                <div class="ticket-header">
                    <h4>${ticketData.event_title || 'Event Ticket'}</h4>
                </div>
                <div class="ticket-body">
                    <p><strong>Name:</strong> ${ticketData.attendee_name || ''}</p>
                    <p><strong>Email:</strong> ${ticketData.attendee_email || ''}</p>
                    <p><strong>Ticket Type:</strong> ${ticketData.ticket_type || ''}</p>
                    <p><strong>Date:</strong> ${ticketData.event_date || ''}</p>
                    <div class="qr-code">
                        ${ticketData.qr_code || ''}
                    </div>
                </div>
            </div>
        `;

        modalBody.html(ticketHtml);

        // Add download/print buttons
        modalFooter.html(`
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print Ticket</button>
        `);
    }

    /**
     * Format date/time for display
     */
    function formatDateTime(date, timezone) {
        if (!(date instanceof Date)) return '';

        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: timezone
        };

        return date.toLocaleDateString('en-US', options);
    }

    // Initialize when DOM is ready and all scripts are loaded
    $(document).ready(function() {
        // Small delay to ensure all scripts from events_lobby_hall.php are loaded
        setTimeout(function() {
            console.log('Initializing events detail page...');
            initEventDetail();
        }, 100);
    });

    // Also initialize if called after DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                console.log('Initializing events detail page (DOM loaded)...');
                initEventDetail();
            }, 100);
        });
    } else {
        setTimeout(function() {
            console.log('Initializing events detail page (already loaded)...');
            initEventDetail();
        }, 100);
    }

})();