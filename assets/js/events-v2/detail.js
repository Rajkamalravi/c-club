/**
 * Events V2 - Detail Page JavaScript
 */

const EV2Detail = {
    eventData: null,

    /**
     * Initialize detail page
     */
    init() {
        this.bindEvents();
        this.initStickyHeader();
        this.initTabs();
    },

    /**
     * Set event data
     * @param {Object} data
     */
    setEventData(data) {
        this.eventData = data;
    },

    /**
     * Bind event handlers
     */
    bindEvents() {
        const { on, onAll } = EV2Helpers;

        // Share buttons
        onAll('.ev2-share-btn', 'click', function(e) {
            e.preventDefault();
            const platform = this.dataset.platform;
            EV2Detail.handleShare(platform);
        });

        // Copy link button
        const copyBtn = EV2Helpers.$('.ev2-share-btn-copy');
        if (copyBtn) {
            on(copyBtn, 'click', (e) => {
                e.preventDefault();
                EV2App.copyToClipboard(window.location.href);
            });
        }

        // Add to calendar buttons
        onAll('.ev2-add-calendar-btn', 'click', function(e) {
            e.preventDefault();
            const type = this.dataset.calendar;
            EV2Detail.handleAddToCalendar(type);
        });

        // RSVP button
        const rsvpBtn = EV2Helpers.$('#ev2-rsvp-btn');
        if (rsvpBtn) {
            on(rsvpBtn, 'click', () => this.handleRsvpClick());
        }
    },

    /**
     * Initialize sticky header behavior
     */
    initStickyHeader() {
        const stickyBar = EV2Helpers.$('.ev2-sticky-cta');
        if (!stickyBar) return;

        const hero = EV2Helpers.$('.ev2-detail-hero');
        if (!hero) return;

        const heroBottom = hero.offsetTop + hero.offsetHeight;

        window.addEventListener('scroll', EV2Helpers.throttle(() => {
            if (window.scrollY > heroBottom - 80) {
                stickyBar.classList.add('visible');
            } else {
                stickyBar.classList.remove('visible');
            }
        }, 100));
    },

    /**
     * Initialize tabs
     */
    initTabs() {
        const tabButtons = EV2Helpers.$$('.ev2-detail-tabs .nav-link');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Remove active from all
                tabButtons.forEach(b => b.classList.remove('active'));
                EV2Helpers.$$('.ev2-detail-tabs .tab-pane').forEach(p => {
                    p.classList.remove('show', 'active');
                });

                // Add active to clicked
                this.classList.add('active');
                const targetId = this.getAttribute('data-bs-target') || this.getAttribute('href');
                const targetPane = EV2Helpers.$(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }

                // Update URL hash
                if (targetId) {
                    history.replaceState(null, null, targetId);
                }
            });
        });

        // Activate tab from URL hash
        if (window.location.hash) {
            const hashTab = EV2Helpers.$(`.nav-link[href="${window.location.hash}"], .nav-link[data-bs-target="${window.location.hash}"]`);
            if (hashTab) {
                hashTab.click();
            }
        }
    },

    /**
     * Handle share button click
     * @param {string} platform
     */
    handleShare(platform) {
        if (!this.eventData) {
            EV2App.shareEvent(platform, {
                title: document.title,
                url: window.location.href,
                description: ''
            });
            return;
        }

        EV2App.shareEvent(platform, {
            title: this.eventData.title,
            url: window.location.href,
            description: this.eventData.description
        });
    },

    /**
     * Handle add to calendar
     * @param {string} type - google, outlook, yahoo
     */
    handleAddToCalendar(type) {
        if (!this.eventData) return;

        const links = EV2App.getCalendarLinks({
            title: this.eventData.title,
            description: this.eventData.description || '',
            location: this.eventData.venue || '',
            startDate: this.eventData.startDate,
            endDate: this.eventData.endDate,
            url: window.location.href
        });

        if (links[type]) {
            window.open(links[type], '_blank');
        }
    },

    /**
     * Handle RSVP button click
     */
    handleRsvpClick() {
        if (!IS_LOGGED_IN) {
            // Redirect to login
            const returnUrl = encodeURIComponent(window.location.href);
            window.location.href = `${LOGIN_URL}?return=${returnUrl}`;
            return;
        }

        // Navigate to RSVP page
        const eventtoken = this.eventData?.eventtoken || this.getEventTokenFromUrl();
        if (eventtoken) {
            window.location.href = `${EVENTS_V2_URL}/rsvp/${eventtoken}`;
        }
    },

    /**
     * Get event token from current URL
     * @returns {string}
     */
    getEventTokenFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const dIndex = pathParts.indexOf('d');
        if (dIndex !== -1 && pathParts[dIndex + 1]) {
            const slug = pathParts[dIndex + 1];
            // Extract token from slug (last part after last dash)
            const parts = slug.split('-');
            return parts[parts.length - 1];
        }
        return '';
    },

    /**
     * Load speakers
     * @param {string} eventtoken
     */
    async loadSpeakers(eventtoken) {
        const container = EV2Helpers.$('#ev2-speakers-container');
        if (!container) return;

        EV2Helpers.showLoading(container);

        try {
            const response = await EV2Api.getEventMeta(eventtoken);
            if (response && response.success && response.output?.event_speaker) {
                this.renderSpeakers(response.output.event_speaker, container);
            } else {
                container.innerHTML = '<p class="text-muted">No speakers announced yet.</p>';
            }
        } catch (error) {
            console.error('Error loading speakers:', error);
            container.innerHTML = '<p class="text-muted">Error loading speakers.</p>';
        }
    },

    /**
     * Render speakers
     * @param {Array} speakers
     * @param {Element} container
     */
    renderSpeakers(speakers, container) {
        if (!speakers.length) {
            container.innerHTML = '<p class="text-muted">No speakers announced yet.</p>';
            return;
        }

        container.innerHTML = `
            <div class="ev2-speakers-grid">
                ${speakers.map(speaker => `
                    <div class="ev2-speaker-card">
                        <img src="${speaker.spk_logo_image || `${SITE_URL}/assets/images/avatar.png`}"
                             alt="${EV2Helpers.escapeHtml(speaker.spk_title)}"
                             class="ev2-speaker-card-avatar">
                        <h4 class="ev2-speaker-card-name">${EV2Helpers.escapeHtml(speaker.spk_title)}</h4>
                        <p class="ev2-speaker-card-role">${EV2Helpers.escapeHtml(speaker.spk_hall || '')}</p>
                    </div>
                `).join('')}
            </div>
        `;
    },

    /**
     * Load agenda/sessions
     * @param {string} eventtoken
     */
    async loadAgenda(eventtoken) {
        const container = EV2Helpers.$('#ev2-agenda-container');
        if (!container) return;

        EV2Helpers.showLoading(container);

        try {
            const response = await EV2Api.getEventMeta(eventtoken);
            if (response && response.success && response.output?.event_speaker) {
                this.renderAgenda(response.output.event_speaker, container);
            } else {
                container.innerHTML = '<p class="text-muted">Agenda coming soon.</p>';
            }
        } catch (error) {
            console.error('Error loading agenda:', error);
            container.innerHTML = '<p class="text-muted">Error loading agenda.</p>';
        }
    },

    /**
     * Render agenda timeline
     * @param {Array} sessions
     * @param {Element} container
     */
    renderAgenda(sessions, container) {
        if (!sessions.length) {
            container.innerHTML = '<p class="text-muted">Agenda coming soon.</p>';
            return;
        }

        // Sort by start time
        sessions.sort((a, b) => {
            return (a.spk_datefrom || '').localeCompare(b.spk_datefrom || '');
        });

        container.innerHTML = `
            <div class="ev2-agenda-timeline">
                ${sessions.map(session => `
                    <div class="ev2-agenda-item">
                        <div class="ev2-agenda-time">
                            ${EV2Helpers.formatEventDate(session.spk_datefrom, 'h:mm a')} -
                            ${EV2Helpers.formatEventDate(session.spk_dateto, 'h:mm a')}
                        </div>
                        <div class="ev2-agenda-card">
                            <h4>${EV2Helpers.escapeHtml(session.spk_title)}</h4>
                            <p>${EV2Helpers.truncate(session.spk_desc || '', 150)}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    EV2Detail.init();
});

// Export for use
window.EV2Detail = EV2Detail;
