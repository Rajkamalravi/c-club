/**
 * Events V2 - Listing Page JavaScript
 */

const EV2Listing = {
    // State
    currentPage: 0,
    limit: 12,
    isLoading: false,
    hasMore: true,
    filters: {
        search: '',
        event_types: [], // Array for multiple types
        from_date: '',
        to_date: ''
    },

    /**
     * Initialize listing page
     */
    init() {
        this.parseUrlFilters();
        this.bindEvents();
        this.loadEvents();
    },

    /**
     * Parse filters from URL
     */
    parseUrlFilters() {
        const params = new URLSearchParams(window.location.search);

        this.filters = {
            search: params.get('search') || '',
            event_types: params.getAll('type[]') || [], // Get all type[] values
            from_date: params.get('from') || '',
            to_date: params.get('to') || ''
        };

        // Update form inputs with current filters
        const searchInput = EV2Helpers.$('#ev2-search-input');
        if (searchInput && this.filters.search) {
            searchInput.value = this.filters.search;
        }

        // Update filter checkboxes for multiple types
        if (this.filters.event_types.length > 0) {
            this.filters.event_types.forEach(type => {
                const checkbox = EV2Helpers.$(`input[name="type[]"][value="${type}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }

        // Update date inputs
        const fromDate = EV2Helpers.$('#ev2-from-date');
        const toDate = EV2Helpers.$('#ev2-to-date');
        if (fromDate && this.filters.from_date) fromDate.value = this.filters.from_date;
        if (toDate && this.filters.to_date) toDate.value = this.filters.to_date;
    },

    /**
     * Bind event handlers
     */
    bindEvents() {
        const { $, on, debounce } = EV2Helpers;

        // Search form
        const searchForm = $('#ev2-search-form');
        if (searchForm) {
            on(searchForm, 'submit', (e) => {
                e.preventDefault();
                this.handleSearch();
            });
        }

        // Search input (live search)
        const searchInput = $('#ev2-search-input');
        if (searchInput) {
            on(searchInput, 'input', debounce(() => {
                this.handleSearch();
            }, 500));
        }

        // Event type filters (checkboxes)
        EV2Helpers.onAll('input[name="type[]"]', 'change', () => {
            this.handleFilterChange();
        });

        // Date filters
        const dateInputs = ['#ev2-from-date', '#ev2-to-date'];
        dateInputs.forEach(selector => {
            const input = $(selector);
            if (input) {
                on(input, 'change', () => this.handleFilterChange());
            }
        });

        // Clear filters button
        const clearBtn = $('#ev2-clear-filters');
        if (clearBtn) {
            on(clearBtn, 'click', () => this.clearFilters());
        }

        // Load more button
        const loadMoreBtn = $('#ev2-load-more');
        if (loadMoreBtn) {
            on(loadMoreBtn, 'click', () => this.loadMore());
        }

        // View toggle (grid/list)
        EV2Helpers.onAll('.ev2-view-toggle-btn', 'click', function() {
            EV2Listing.handleViewToggle(this.dataset.view);
        });

        // Infinite scroll (optional)
        if ($('#ev2-infinite-scroll')) {
            this.initInfiniteScroll();
        }
    },

    /**
     * Handle search
     */
    handleSearch() {
        const searchInput = EV2Helpers.$('#ev2-search-input');
        this.filters.search = searchInput ? searchInput.value.trim() : '';
        this.resetAndLoad();
    },

    /**
     * Handle filter change
     */
    handleFilterChange() {
        // Get all checked event types
        const checkedTypes = EV2Helpers.$$('input[name="type[]"]:checked');
        this.filters.event_types = Array.from(checkedTypes).map(input => input.value);

        // Get dates
        const fromDate = EV2Helpers.$('#ev2-from-date');
        const toDate = EV2Helpers.$('#ev2-to-date');
        this.filters.from_date = fromDate ? fromDate.value : '';
        this.filters.to_date = toDate ? toDate.value : '';

        this.updateUrl();
        this.resetAndLoad();
    },

    /**
     * Clear all filters
     */
    clearFilters() {
        this.filters = {
            search: '',
            event_types: [],
            from_date: '',
            to_date: ''
        };

        // Reset form inputs
        const searchInput = EV2Helpers.$('#ev2-search-input');
        if (searchInput) searchInput.value = '';

        EV2Helpers.$$('input[name="type[]"]').forEach(input => {
            input.checked = false;
        });

        const fromDate = EV2Helpers.$('#ev2-from-date');
        const toDate = EV2Helpers.$('#ev2-to-date');
        if (fromDate) fromDate.value = '';
        if (toDate) toDate.value = '';

        this.updateUrl();
        this.resetAndLoad();
        this.updateFilterTags();
    },

    /**
     * Update URL with current filters
     */
    updateUrl() {
        const url = new URL(window.location);

        // Clear existing type[] params
        url.searchParams.delete('type[]');

        // Handle search
        if (this.filters.search) {
            url.searchParams.set('search', this.filters.search);
        } else {
            url.searchParams.delete('search');
        }

        // Handle multiple event types
        if (this.filters.event_types && this.filters.event_types.length > 0) {
            this.filters.event_types.forEach(type => {
                url.searchParams.append('type[]', type);
            });
        }

        // Handle dates
        if (this.filters.from_date) {
            url.searchParams.set('from', this.filters.from_date);
        } else {
            url.searchParams.delete('from');
        }

        if (this.filters.to_date) {
            url.searchParams.set('to', this.filters.to_date);
        } else {
            url.searchParams.delete('to');
        }

        window.history.pushState({}, '', url);
    },

    /**
     * Reset pagination and load
     */
    resetAndLoad() {
        this.currentPage = 0;
        this.hasMore = true;

        const container = EV2Helpers.$('#ev2-events-container');
        if (container) {
            container.innerHTML = '';
            // Ensure grid class is applied
            if (!container.classList.contains('ev2-events-grid') && !container.classList.contains('ev2-events-list')) {
                container.classList.add('ev2-events-grid');
            }
        }

        this.loadEvents();
    },

    /**
     * Load events from API
     */
    async loadEvents() {
        if (this.isLoading || !this.hasMore) return;

        this.isLoading = true;
        const container = EV2Helpers.$('#ev2-events-container');
        const loadMoreBtn = EV2Helpers.$('#ev2-load-more');

        // Ensure container has grid class
        if (container && !container.classList.contains('ev2-events-grid') && !container.classList.contains('ev2-events-list')) {
            container.classList.add('ev2-events-grid');
        }

        // Show loading state
        if (this.currentPage === 0) {
            EV2Helpers.showLoading(container);
        } else if (loadMoreBtn) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
        }

        try {
            const response = await EV2Api.getEvents({
                ...this.filters,
                limit: this.limit,
                offset: this.currentPage
            });

            if (response && response.success) {
                const events = response.output?.list?.near || response.output?.list || [];

                if (this.currentPage === 0) {
                    container.innerHTML = '';
                }

                if (events.length === 0 && this.currentPage === 0) {
                    EV2Helpers.showEmptyState(container, 'No events found');
                    this.hasMore = false;
                } else {
                    this.renderEvents(events, container);
                    this.hasMore = events.length === this.limit;
                    this.currentPage++;
                }

                this.updateResultsCount(response.output?.total || events.length);
            } else {
                if (this.currentPage === 0) {
                    EV2Helpers.showEmptyState(container, 'Error loading events');
                }
            }
        } catch (error) {
            console.error('Error loading events:', error);
            if (this.currentPage === 0) {
                EV2Helpers.showEmptyState(container, 'Error loading events');
            }
        } finally {
            this.isLoading = false;

            if (loadMoreBtn) {
                loadMoreBtn.disabled = false;
                loadMoreBtn.innerHTML = 'Load More Events';

                if (!this.hasMore) {
                    EV2Helpers.hide(loadMoreBtn);
                } else {
                    EV2Helpers.show(loadMoreBtn);
                }
            }
        }
    },

    /**
     * Load more events
     */
    loadMore() {
        this.loadEvents();
    },

    /**
     * Render events to container
     * @param {Array} events
     * @param {Element} container
     */
    renderEvents(events, container) {
        events.forEach(event => {
            const card = this.createEventCard(event);
            container.appendChild(card);
        });

        // Reinitialize lazy loading
        EV2App.initLazyLoading();
    },

    /**
     * Create event card HTML
     * @param {Object} event
     * @returns {Element}
     */
    createEventCard(event) {
        const { escapeHtml, truncate, formatEventDate } = EV2Helpers;

        const eventData = event.conttoken || {};
        const title = escapeHtml(eventData.title || 'Untitled Event');
        const description = truncate(eventData.description || '', 100);
        const image = eventData.event_image || `${SITE_URL}/assets/images/event.jpg`;
        const eventType = (eventData.event_type || 'virtual').toLowerCase();
        const eventUrl = `${EVENTS_V2_URL}/d/${event.slug || event.eventtoken}`;

        // Format dates
        const startDate = formatEventDate(event.utc_start_at, 'ccc, LLL d');
        const startTime = formatEventDate(event.utc_start_at, 'h:mm a');

        // Get badges
        const typeBadge = this.getTypeBadge(eventType);
        const statusBadge = this.getStatusBadge(event);

        const cardHtml = `
            <div class="ev2-event-card">
                <div class="ev2-event-card-image">
                    <img data-src="${escapeHtml(image)}" alt="${title}" loading="lazy">
                    <div class="ev2-event-card-badges">
                        ${statusBadge ? `<span class="badge ${statusBadge.class}">${statusBadge.label}</span>` : ''}
                        <span class="badge ${typeBadge.class}">
                            <i class="bi ${typeBadge.icon} me-1"></i>${typeBadge.label}
                        </span>
                    </div>
                </div>
                <div class="ev2-event-card-body">
                    <h3 class="ev2-event-card-title">
                        <a href="${eventUrl}">${title}</a>
                    </h3>
                    <div class="ev2-event-card-meta">
                        <div class="ev2-event-card-meta-item">
                            <i class="bi bi-calendar"></i>
                            <span>${startDate}</span>
                        </div>
                        <div class="ev2-event-card-meta-item">
                            <i class="bi bi-clock"></i>
                            <span>${startTime}</span>
                        </div>
                    </div>
                    <p class="ev2-event-card-description">${description}</p>
                </div>
                <div class="ev2-event-card-footer">
                    <a href="${eventUrl}" class="btn btn-sm btn-ev2-primary">View Event</a>
                    <button class="btn btn-sm btn-outline-secondary" onclick="EV2Listing.shareEvent('${event.eventtoken}', '${title}')" title="Share">
                        <i class="bi bi-share"></i>
                    </button>
                </div>
            </div>
        `;

        return EV2Helpers.createElement(cardHtml);
    },

    /**
     * Get event type badge config
     * @param {string} type
     * @returns {Object}
     */
    getTypeBadge(type) {
        const badges = {
            'virtual': { class: 'bg-info', icon: 'bi-camera-video', label: 'Virtual' },
            'in-person': { class: 'bg-dark', icon: 'bi-geo-alt', label: 'In-Person' },
            'hybrid': { class: 'bg-purple', icon: 'bi-broadcast', label: 'Hybrid' }
        };
        return badges[type] || badges['virtual'];
    },

    /**
     * Get event status badge config
     * @param {Object} event
     * @returns {Object|null}
     */
    getStatusBadge(event) {
        // Simple status check based on dates
        const now = new Date();
        const startTime = event.utc_start_at;
        const endTime = event.utc_end_at;

        // Parse YmdHis format
        const parseDate = (str) => {
            if (!str || str.length < 14) return null;
            const year = str.substring(0, 4);
            const month = str.substring(4, 6);
            const day = str.substring(6, 8);
            const hour = str.substring(8, 10);
            const min = str.substring(10, 12);
            const sec = str.substring(12, 14);
            return new Date(`${year}-${month}-${day}T${hour}:${min}:${sec}Z`);
        };

        const start = parseDate(startTime);
        const end = parseDate(endTime);

        if (!start || !end) return null;

        if (now >= start && now <= end) {
            return { class: 'bg-success ev2-badge-live', label: 'Live Now' };
        }

        return null;
    },

    /**
     * Share event
     * @param {string} eventtoken
     * @param {string} title
     */
    shareEvent(eventtoken, title) {
        const url = `${EVENTS_V2_URL}/d/${eventtoken}`;

        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(() => {
                EV2App.copyToClipboard(url);
            });
        } else {
            EV2App.copyToClipboard(url);
        }
    },

    /**
     * Update results count
     * @param {number} count
     */
    updateResultsCount(count) {
        const resultsEl = EV2Helpers.$('#ev2-results-count');
        if (resultsEl) {
            resultsEl.innerHTML = `Showing <strong>${count}</strong> events`;
        }
    },

    /**
     * Update filter tags display
     */
    updateFilterTags() {
        const container = EV2Helpers.$('#ev2-filter-tags');
        if (!container) return;

        const tags = [];

        if (this.filters.search) {
            tags.push({ key: 'search', label: `Search: ${this.filters.search}` });
        }

        // Show each selected event type as a tag
        if (this.filters.event_types && this.filters.event_types.length > 0) {
            this.filters.event_types.forEach(type => {
                const typeLabels = {
                    'virtual': 'Virtual',
                    'in-person': 'In-Person',
                    'hybrid': 'Hybrid'
                };
                tags.push({
                    key: `type:${type}`,
                    label: typeLabels[type] || type
                });
            });
        }

        if (this.filters.from_date) {
            tags.push({ key: 'from_date', label: `From: ${this.filters.from_date}` });
        }
        if (this.filters.to_date) {
            tags.push({ key: 'to_date', label: `To: ${this.filters.to_date}` });
        }

        container.innerHTML = tags.map(tag => `
            <span class="ev2-filter-tag">
                ${EV2Helpers.escapeHtml(tag.label)}
                <button class="ev2-filter-tag-remove" onclick="EV2Listing.removeFilter('${tag.key}')">
                    <i class="bi bi-x"></i>
                </button>
            </span>
        `).join('');
    },

    /**
     * Remove single filter
     * @param {string} key
     */
    removeFilter(key) {
        // Handle type removal (format: "type:virtual")
        if (key.startsWith('type:')) {
            const typeToRemove = key.split(':')[1];
            this.filters.event_types = this.filters.event_types.filter(t => t !== typeToRemove);

            // Uncheck the corresponding checkbox
            const checkbox = EV2Helpers.$(`input[name="type[]"][value="${typeToRemove}"]`);
            if (checkbox) checkbox.checked = false;
        } else if (key === 'from_date') {
            this.filters.from_date = '';
            const input = EV2Helpers.$('#ev2-from-date');
            if (input) input.value = '';
        } else if (key === 'to_date') {
            this.filters.to_date = '';
            const input = EV2Helpers.$('#ev2-to-date');
            if (input) input.value = '';
        } else if (key === 'search') {
            this.filters.search = '';
            const input = EV2Helpers.$('#ev2-search-input');
            if (input) input.value = '';
        }

        this.updateUrl();
        this.resetAndLoad();
        this.updateFilterTags();
    },

    /**
     * Handle view toggle (grid/list)
     * @param {string} view
     */
    handleViewToggle(view) {
        const container = EV2Helpers.$('#ev2-events-container');
        const buttons = EV2Helpers.$$('.ev2-view-toggle-btn');

        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        if (container) {
            container.classList.remove('ev2-events-grid', 'ev2-events-list');
            container.classList.add(view === 'list' ? 'ev2-events-list' : 'ev2-events-grid');
        }

        localStorage.setItem('ev2-view', view);
    },

    /**
     * Initialize infinite scroll
     */
    initInfiniteScroll() {
        const sentinel = EV2Helpers.$('#ev2-infinite-scroll');
        if (!sentinel) return;

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !this.isLoading && this.hasMore) {
                this.loadMore();
            }
        }, {
            rootMargin: '100px'
        });

        observer.observe(sentinel);
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    EV2Listing.init();
});

// Export for use
window.EV2Listing = EV2Listing;
