/**
 * Events V2 - Main JavaScript Entry Point
 */

const EV2App = {
    /**
     * Initialize the application
     */
    init() {
        this.initBootstrapComponents();
        this.initMobileMenu();
        this.initSmoothScroll();
        this.initTooltips();
        this.initLazyLoading();

        console.log('Events V2 initialized');
    },

    /**
     * Initialize Bootstrap components
     */
    initBootstrapComponents() {
        // Initialize all dropdowns
        const dropdownTriggerList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownTriggerList.forEach(el => new bootstrap.Dropdown(el));

        // Initialize all collapses
        const collapseTriggerList = document.querySelectorAll('[data-bs-toggle="collapse"]');
        collapseTriggerList.forEach(el => new bootstrap.Collapse(el, { toggle: false }));

        // Initialize all tabs
        const tabTriggerList = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabTriggerList.forEach(el => new bootstrap.Tab(el));
    },

    /**
     * Initialize mobile navigation
     */
    initMobileMenu() {
        const { $, on } = EV2Helpers;

        // Mobile filter sidebar toggle
        const filterToggle = $('.ev2-mobile-filter-btn');
        const filterSidebar = $('.ev2-filter-sidebar-wrapper');
        const filterOverlay = $('.ev2-filter-overlay');
        const filterClose = $('.ev2-filter-close button');

        if (filterToggle && filterSidebar) {
            on(filterToggle, 'click', () => {
                filterSidebar.classList.add('open');
                if (filterOverlay) filterOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            });

            const closeFilter = () => {
                filterSidebar.classList.remove('open');
                if (filterOverlay) filterOverlay.classList.remove('open');
                document.body.style.overflow = '';
            };

            if (filterOverlay) on(filterOverlay, 'click', closeFilter);
            if (filterClose) on(filterClose, 'click', closeFilter);
        }
    },

    /**
     * Initialize smooth scrolling
     */
    initSmoothScroll() {
        EV2Helpers.onAll('a[href^="#"]', 'click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    },

    /**
     * Initialize Bootstrap tooltips
     */
    initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
    },

    /**
     * Initialize lazy loading for images
     */
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }
                        img.classList.add('loaded');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            document.querySelectorAll('img[data-src]').forEach(img => {
                img.src = img.dataset.src;
            });
        }
    },

    /**
     * Copy text to clipboard
     * @param {string} text
     */
    copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                EV2Helpers.showToast('Link copied to clipboard!', 'success');
            }).catch(() => {
                this.fallbackCopyToClipboard(text);
            });
        } else {
            this.fallbackCopyToClipboard(text);
        }
    },

    /**
     * Fallback copy method
     * @param {string} text
     */
    fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.cssText = 'position:fixed;left:-9999px';
        document.body.appendChild(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            EV2Helpers.showToast('Link copied to clipboard!', 'success');
        } catch (err) {
            EV2Helpers.showToast('Failed to copy link', 'danger');
        }

        document.body.removeChild(textArea);
    },

    /**
     * Share event on social media
     * @param {string} platform
     * @param {Object} eventData
     */
    shareEvent(platform, eventData) {
        const { title, url, description } = eventData;
        const encodedTitle = encodeURIComponent(title);
        const encodedUrl = encodeURIComponent(url);
        const encodedDesc = encodeURIComponent(description || '');

        const shareUrls = {
            twitter: `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`,
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
            email: `mailto:?subject=${encodedTitle}&body=${encodedDesc}%0A%0A${encodedUrl}`
        };

        if (shareUrls[platform]) {
            if (platform === 'email') {
                window.location.href = shareUrls[platform];
            } else {
                window.open(shareUrls[platform], '_blank', 'width=600,height=400');
            }
        }
    },

    /**
     * Generate add to calendar links
     * @param {Object} eventData
     * @returns {Object}
     */
    getCalendarLinks(eventData) {
        const { title, description, location, startDate, endDate, url } = eventData;

        // Format dates for calendar
        const formatCalDate = (date) => {
            return date.replace(/[-:]/g, '').replace('.000Z', 'Z');
        };

        const start = formatCalDate(startDate);
        const end = formatCalDate(endDate);

        return {
            google: `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(title)}&dates=${start}/${end}&details=${encodeURIComponent(description + '\n\n' + url)}&location=${encodeURIComponent(location || '')}`,
            outlook: `https://outlook.live.com/calendar/0/deeplink/compose?subject=${encodeURIComponent(title)}&startdt=${startDate}&enddt=${endDate}&body=${encodeURIComponent(description + '\n\n' + url)}&location=${encodeURIComponent(location || '')}`,
            yahoo: `https://calendar.yahoo.com/?v=60&title=${encodeURIComponent(title)}&st=${start}&et=${end}&desc=${encodeURIComponent(description + '\n\n' + url)}&in_loc=${encodeURIComponent(location || '')}`
        };
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    EV2App.init();
});

// Export for use
window.EV2App = EV2App;
