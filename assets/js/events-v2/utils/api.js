/**
 * Events V2 - API Wrapper
 */

const EV2Api = {
    /**
     * Base fetch wrapper
     * @param {string} url
     * @param {Object} options
     * @returns {Promise}
     */
    async fetch(url, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, mergedOptions);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }

            return await response.text();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * GET request
     * @param {string} url
     * @param {Object} params
     * @returns {Promise}
     */
    async get(url, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;
        return this.fetch(fullUrl);
    },

    /**
     * POST request
     * @param {string} url
     * @param {Object} data
     * @returns {Promise}
     */
    async post(url, data = {}) {
        return this.fetch(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * POST with FormData
     * @param {string} url
     * @param {FormData|Object} data
     * @returns {Promise}
     */
    async postForm(url, data) {
        const formData = data instanceof FormData ? data : this.objectToFormData(data);

        return this.fetch(url, {
            method: 'POST',
            headers: {}, // Let browser set Content-Type for FormData
            body: formData
        });
    },

    /**
     * Convert object to FormData
     * @param {Object} obj
     * @returns {FormData}
     */
    objectToFormData(obj) {
        const formData = new FormData();
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                formData.append(key, obj[key]);
            }
        }
        return formData;
    },

    // ===== Events API Methods =====

    /**
     * Get events list
     * @param {Object} params
     * @returns {Promise}
     */
    async getEvents(params = {}) {
        // Build URL with support for array parameters
        const url = new URL(`${EVENTS_V2_URL}/ajax`, window.location.origin);
        url.searchParams.set('action', 'events');
        url.searchParams.set('limit', params.limit || 12);
        url.searchParams.set('offset', params.offset || 0);

        // Add optional filters
        if (params.search) url.searchParams.set('search', params.search);

        // Handle multiple event types (array)
        if (params.event_types && Array.isArray(params.event_types)) {
            params.event_types.forEach(type => {
                url.searchParams.append('type[]', type);
            });
        } else if (params.type) {
            url.searchParams.set('type', params.type);
        } else if (params.event_type) {
            url.searchParams.set('type', params.event_type);
        }

        if (params.from_date) url.searchParams.set('from_date', params.from_date);
        if (params.to_date) url.searchParams.set('to_date', params.to_date);
        if (params.geohash) url.searchParams.set('geohash', params.geohash);

        return this.fetch(url.toString());
    },

    /**
     * Get single event details
     * @param {string} eventtoken
     * @returns {Promise}
     */
    async getEvent(eventtoken) {
        return this.get(`${EVENTS_V2_URL}/ajax`, {
            action: 'event',
            eventtoken: eventtoken
        });
    },

    /**
     * Get event meta (speakers, exhibitors, sponsors)
     * @param {string} eventtoken
     * @returns {Promise}
     */
    async getEventMeta(eventtoken) {
        return this.get(`${EVENTS_V2_URL}/ajax`, {
            action: 'meta',
            eventtoken: eventtoken
        });
    },

    /**
     * Submit RSVP
     * @param {Object} rsvpData
     * @returns {Promise}
     */
    async submitRsvp(rsvpData) {
        const url = `${EVENTS_V2_URL}/ajax?action=rsvp`;
        return this.post(url, rsvpData);
    },

    /**
     * Get user's RSVP status
     * @param {string} eventtoken
     * @returns {Promise}
     */
    async getRsvpStatus(eventtoken) {
        return this.get(`${EVENTS_V2_URL}/ajax`, {
            action: 'rsvp_status',
            eventtoken: eventtoken
        });
    },

    /**
     * Search events
     * @param {string} query
     * @param {number} limit
     * @returns {Promise}
     */
    async searchEvents(query, limit = 10) {
        return this.get(`${EVENTS_V2_URL}/ajax`, {
            action: 'search',
            q: query,
            limit: limit
        });
    }
};

// Export for use
window.EV2Api = EV2Api;
