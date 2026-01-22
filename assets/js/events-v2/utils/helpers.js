/**
 * Events V2 - DOM and Utility Helpers
 */

const EV2Helpers = {
    /**
     * Select single element
     * @param {string} selector
     * @param {Element} parent
     * @returns {Element|null}
     */
    $(selector, parent = document) {
        return parent.querySelector(selector);
    },

    /**
     * Select multiple elements
     * @param {string} selector
     * @param {Element} parent
     * @returns {NodeList}
     */
    $$(selector, parent = document) {
        return parent.querySelectorAll(selector);
    },

    /**
     * Add event listener
     * @param {Element|string} target
     * @param {string} event
     * @param {Function} handler
     * @param {Object} options
     */
    on(target, event, handler, options = {}) {
        const element = typeof target === 'string' ? this.$(target) : target;
        if (element) {
            element.addEventListener(event, handler, options);
        }
    },

    /**
     * Add event listener to multiple elements
     * @param {string} selector
     * @param {string} event
     * @param {Function} handler
     */
    onAll(selector, event, handler) {
        this.$$(selector).forEach(el => el.addEventListener(event, handler));
    },

    /**
     * Delegate event listener
     * @param {Element|string} parent
     * @param {string} event
     * @param {string} selector
     * @param {Function} handler
     */
    delegate(parent, event, selector, handler) {
        const element = typeof parent === 'string' ? this.$(parent) : parent;
        if (element) {
            element.addEventListener(event, (e) => {
                const target = e.target.closest(selector);
                if (target && element.contains(target)) {
                    handler.call(target, e, target);
                }
            });
        }
    },

    /**
     * Create element from HTML string
     * @param {string} html
     * @returns {Element}
     */
    createElement(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstChild;
    },

    /**
     * Show element
     * @param {Element|string} element
     */
    show(element) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) el.style.display = '';
    },

    /**
     * Hide element
     * @param {Element|string} element
     */
    hide(element) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) el.style.display = 'none';
    },

    /**
     * Toggle element visibility
     * @param {Element|string} element
     */
    toggle(element) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) {
            el.style.display = el.style.display === 'none' ? '' : 'none';
        }
    },

    /**
     * Add class
     * @param {Element|string} element
     * @param {string} className
     */
    addClass(element, className) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) el.classList.add(className);
    },

    /**
     * Remove class
     * @param {Element|string} element
     * @param {string} className
     */
    removeClass(element, className) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) el.classList.remove(className);
    },

    /**
     * Toggle class
     * @param {Element|string} element
     * @param {string} className
     */
    toggleClass(element, className) {
        const el = typeof element === 'string' ? this.$(element) : element;
        if (el) el.classList.toggle(className);
    },

    /**
     * Check if element has class
     * @param {Element|string} element
     * @param {string} className
     * @returns {boolean}
     */
    hasClass(element, className) {
        const el = typeof element === 'string' ? this.$(element) : element;
        return el ? el.classList.contains(className) : false;
    },

    /**
     * Debounce function
     * @param {Function} func
     * @param {number} wait
     * @returns {Function}
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle function
     * @param {Function} func
     * @param {number} limit
     * @returns {Function}
     */
    throttle(func, limit = 300) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Format date using Luxon
     * @param {string} dateString
     * @param {string} format
     * @returns {string}
     */
    formatDate(dateString, format = 'DDD') {
        if (typeof luxon !== 'undefined') {
            const dt = luxon.DateTime.fromISO(dateString);
            return dt.isValid ? dt.toFormat(format) : dateString;
        }
        return dateString;
    },

    /**
     * Format date from YmdHis format
     * @param {string} dateString - Date in YmdHis format (e.g., "20240115143000")
     * @param {string} format
     * @returns {string}
     */
    formatEventDate(dateString, format = 'ccc, LLL d, yyyy') {
        if (typeof luxon !== 'undefined' && dateString) {
            const dt = luxon.DateTime.fromFormat(dateString, 'yyyyMMddHHmmss', { zone: USER_TIMEZONE });
            return dt.isValid ? dt.toFormat(format) : dateString;
        }
        return dateString;
    },

    /**
     * Format event time
     * @param {string} dateString
     * @returns {string}
     */
    formatEventTime(dateString) {
        return this.formatEventDate(dateString, 'h:mm a');
    },

    /**
     * Get relative time
     * @param {string} dateString
     * @returns {string}
     */
    getRelativeTime(dateString) {
        if (typeof luxon !== 'undefined') {
            const dt = luxon.DateTime.fromISO(dateString);
            return dt.isValid ? dt.toRelative() : dateString;
        }
        return dateString;
    },

    /**
     * Truncate text
     * @param {string} text
     * @param {number} length
     * @returns {string}
     */
    truncate(text, length = 150) {
        if (!text) return '';
        text = text.replace(/<[^>]*>/g, '').trim();
        return text.length > length ? text.substring(0, length) + '...' : text;
    },

    /**
     * Escape HTML
     * @param {string} str
     * @returns {string}
     */
    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Parse URL parameters
     * @returns {Object}
     */
    getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        const result = {};
        for (const [key, value] of params) {
            result[key] = value;
        }
        return result;
    },

    /**
     * Update URL parameter without reload
     * @param {string} key
     * @param {string} value
     */
    setUrlParam(key, value) {
        const url = new URL(window.location);
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.history.pushState({}, '', url);
    },

    /**
     * Show loading spinner
     * @param {Element|string} container
     */
    showLoading(container) {
        const el = typeof container === 'string' ? this.$(container) : container;
        if (el) {
            el.innerHTML = `
                <div class="ev2-loading">
                    <div class="ev2-spinner"></div>
                </div>
            `;
        }
    },

    /**
     * Show empty state
     * @param {Element|string} container
     * @param {string} message
     * @param {string} icon
     */
    showEmptyState(container, message = 'No events found', icon = 'bi-calendar-x') {
        const el = typeof container === 'string' ? this.$(container) : container;
        if (el) {
            el.innerHTML = `
                <div class="ev2-empty-state">
                    <i class="bi ${icon} ev2-empty-state-icon"></i>
                    <h3>${this.escapeHtml(message)}</h3>
                    <p>Try adjusting your filters or search terms</p>
                </div>
            `;
        }
    },

    /**
     * Show toast notification
     * @param {string} message
     * @param {string} type - success, warning, danger
     */
    showToast(message, type = 'success') {
        const icons = {
            success: 'bi-check-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            danger: 'bi-x-circle-fill'
        };

        const toast = this.createElement(`
            <div class="ev2-toast ev2-alert ev2-alert-${type}" role="alert">
                <i class="bi ${icons[type]} ev2-alert-icon"></i>
                <div class="ev2-alert-content">
                    <p>${this.escapeHtml(message)}</p>
                </div>
            </div>
        `);

        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
};

// Export for use
window.EV2Helpers = EV2Helpers;
