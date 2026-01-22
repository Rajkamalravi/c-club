/**
 * Events V2 - RSVP Flow JavaScript
 */

const EV2Rsvp = {
    selectedTicket: null,
    eventtoken: null,

    /**
     * Initialize RSVP page
     */
    init() {
        this.eventtoken = this.getEventTokenFromUrl();
        this.bindEvents();
        this.restoreState();
    },

    /**
     * Get event token from URL
     * @returns {string}
     */
    getEventTokenFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const rsvpIndex = pathParts.indexOf('rsvp');
        if (rsvpIndex !== -1 && pathParts[rsvpIndex + 1]) {
            return pathParts[rsvpIndex + 1];
        }
        return '';
    },

    /**
     * Bind event handlers
     */
    bindEvents() {
        const { on, onAll, delegate } = EV2Helpers;

        // Ticket selection
        delegate(document, 'change', 'input[name="ticket_type"]', (e, target) => {
            this.handleTicketSelect(target);
        });

        // Also handle click on ticket cards
        onAll('.ev2-ticket-card', 'click', function() {
            const radio = this.parentElement.querySelector('input[type="radio"]');
            if (radio && !radio.disabled) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        // Continue button
        const continueBtn = EV2Helpers.$('#ev2-continue-btn');
        if (continueBtn) {
            on(continueBtn, 'click', () => this.handleContinue());
        }

        // Back button
        const backBtn = EV2Helpers.$('#ev2-back-btn');
        if (backBtn) {
            on(backBtn, 'click', () => this.handleBack());
        }

        // Form submission
        const rsvpForm = EV2Helpers.$('#ev2-rsvp-form');
        if (rsvpForm) {
            on(rsvpForm, 'submit', (e) => this.handleFormSubmit(e));
        }

        // Form validation on blur
        onAll('#ev2-rsvp-form input[required], #ev2-rsvp-form select[required]', 'blur', function() {
            EV2Rsvp.validateField(this);
        });

        // Terms checkbox
        const termsCheckbox = EV2Helpers.$('#ev2-terms-agree');
        if (termsCheckbox) {
            on(termsCheckbox, 'change', () => this.updateSubmitButton());
        }
    },

    /**
     * Restore state from session storage
     */
    restoreState() {
        const savedTicket = sessionStorage.getItem('ev2_selected_ticket');
        if (savedTicket) {
            const radio = EV2Helpers.$(`input[name="ticket_type"][value="${savedTicket}"]`);
            if (radio) {
                radio.checked = true;
                this.selectedTicket = savedTicket;
                this.updateContinueButton();
            }
        }
    },

    /**
     * Handle ticket selection
     * @param {Element} radio
     */
    handleTicketSelect(radio) {
        this.selectedTicket = radio.value;

        // Update visual state
        EV2Helpers.$$('.ev2-ticket-card').forEach(card => {
            card.classList.remove('selected');
        });

        const selectedCard = radio.closest('.ev2-ticket-option')?.querySelector('.ev2-ticket-card');
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }

        // Save to session
        sessionStorage.setItem('ev2_selected_ticket', this.selectedTicket);

        this.updateContinueButton();
    },

    /**
     * Update continue button state
     */
    updateContinueButton() {
        const continueBtn = EV2Helpers.$('#ev2-continue-btn');
        if (continueBtn) {
            continueBtn.disabled = !this.selectedTicket;
        }
    },

    /**
     * Handle continue to form
     */
    handleContinue() {
        if (!this.selectedTicket) {
            EV2Helpers.showToast('Please select a ticket type', 'warning');
            return;
        }

        // Navigate to form page
        window.location.href = `${EVENTS_V2_URL}/rsvp-form/${this.eventtoken}?ticket=${this.selectedTicket}`;
    },

    /**
     * Handle back button
     */
    handleBack() {
        window.history.back();
    },

    /**
     * Validate single field
     * @param {Element} field
     * @returns {boolean}
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';

        // Remove existing error
        field.classList.remove('is-invalid');
        const errorEl = field.parentElement.querySelector('.invalid-feedback');
        if (errorEl) errorEl.remove();

        // Required check
        if (field.required && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }

        // Phone validation
        if (field.type === 'tel' && value) {
            const phoneRegex = /^[\d\s\-+()]{10,}$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid phone number';
            }
        }

        if (!isValid) {
            field.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = errorMessage;
            field.parentElement.appendChild(feedback);
        }

        return isValid;
    },

    /**
     * Validate entire form
     * @param {HTMLFormElement} form
     * @returns {boolean}
     */
    validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input[required], select[required]');

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Check terms checkbox
        const termsCheckbox = EV2Helpers.$('#ev2-terms-agree');
        if (termsCheckbox && !termsCheckbox.checked) {
            isValid = false;
            EV2Helpers.showToast('Please agree to the terms and conditions', 'warning');
        }

        return isValid;
    },

    /**
     * Update submit button state
     */
    updateSubmitButton() {
        const termsCheckbox = EV2Helpers.$('#ev2-terms-agree');
        const submitBtn = EV2Helpers.$('#ev2-submit-btn');

        if (submitBtn && termsCheckbox) {
            submitBtn.disabled = !termsCheckbox.checked;
        }
    },

    /**
     * Handle form submission
     * @param {Event} e
     */
    async handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        if (!this.validateForm(form)) {
            return;
        }

        const submitBtn = EV2Helpers.$('#ev2-submit-btn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

        try {
            const formData = new FormData(form);
            formData.append('eventtoken', this.eventtoken);

            // Get ticket from URL
            const params = EV2Helpers.getUrlParams();
            formData.append('ticket_type', params.ticket || '');

            const response = await EV2Api.submitRsvp(Object.fromEntries(formData));

            if (response && response.success) {
                // Clear session storage
                sessionStorage.removeItem('ev2_selected_ticket');

                // Redirect to confirmation
                window.location.href = `${EVENTS_V2_URL}/confirmation/${this.eventtoken}`;
            } else {
                EV2Helpers.showToast(response?.message || 'Failed to submit RSVP', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('RSVP submission error:', error);
            EV2Helpers.showToast('An error occurred. Please try again.', 'danger');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    },

    /**
     * Initialize confirmation page
     */
    initConfirmation() {
        // Clear any stored state
        sessionStorage.removeItem('ev2_selected_ticket');

        // Bind add to calendar buttons
        EV2Helpers.onAll('.ev2-add-calendar-btn', 'click', function(e) {
            e.preventDefault();
            const type = this.dataset.calendar;
            EV2Detail.handleAddToCalendar(type);
        });
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Check which page we're on
    const path = window.location.pathname;

    if (path.includes('/confirmation/')) {
        EV2Rsvp.initConfirmation();
    } else {
        EV2Rsvp.init();
    }
});

// Export for use
window.EV2Rsvp = EV2Rsvp;
