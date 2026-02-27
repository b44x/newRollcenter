/**
 * Dynamic Product Enhancements
 * Adds progressive disclosure, visual feedback, and enhanced interactivity
 * to the Dynamic Product configurator without overriding existing functionality.
*/
(function() {
    'use strict';

    // DEBUG: Check if file is loaded
    console.log('🔧 DYNAMIC PRODUCT ENHANCEMENTS LOADED');
    console.log('📍 Current location:', window.location.href);
    console.log('🎯 Module version check:', typeof window.dp !== 'undefined' ? 'DP found' : 'DP not found');


    // Configuration
    const CONFIG = {
        selectors: {
            container: '.dp_input_div',
            step: '.dp_group',
            stepNav: '.dp-step-nav',
            stepNext: '.dp_step_next',
            stepPrev: '.dp_step_prev',
            progress: '.dp-progress',
            field: '.dp-field',
            input: 'input, select, textarea',
            numberInput: 'input[type="number"]',
            fabricOption: '.dp-fabric__grid',
            blindType: '.dp-thumbnail',
            controlOption: '.dp-thumbnail',
            dimensionInput: '.dp_field_container',
            summary: '.dp-summary',
            modal: '.dp-modal',
            modalClose: '.dp-modal-close',
            lightbox: '.fabric-lightbox'
        },
        classes: {
            active: 'active',
            selected: 'selected',
            error: 'error',
            hidden: 'hidden',
            sticky: 'sticky',
            hover: 'hover',
            validating: 'validating',
            complete: 'complete'
        },
        animations: {
            duration: 300,
            easing: 'ease-in-out'
        }
    };

    // State management
    const state = {
        currentStep: 1,
        totalSteps: 0,
        isValidating: false,
        modalOpen: false
    };

    // Utility functions
    const utils = {
        /**
         * Debounce function calls
         */
        debounce: function(func, wait) {
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
         * Check if element is visible in viewport
         */
        isInViewport: function(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        },

        /**
         * Smooth scroll to element
         */
        scrollToElement: function(element, offset = 0) {
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        },

        /**
         * Animate element
         */
        animate: function(element, properties, duration = CONFIG.animations.duration) {
            element.style.transition = `all ${duration}ms ${CONFIG.animations.easing}`;
            Object.assign(element.style, properties);

            return new Promise(resolve => {
                setTimeout(resolve, duration);
            });
        }
    };

    // Progressive Disclosure
    const progressiveDisclosure = {
        /**
         * Initialize step navigation
         */
        init: function() {
            this.findSteps();
            this.bindEvents();
            this.updateUI();
        },

        /**
         * Find all steps and set total count
         */
        findSteps: function() {
            const steps = document.querySelectorAll(CONFIG.selectors.step);
            state.totalSteps = steps.length;
        },

        /**
         * Bind step navigation events
         */
        bindEvents: function() {
            document.addEventListener('click', (e) => {
                if (e.target.matches(CONFIG.selectors.stepNext)) {
                    e.preventDefault();
                    this.nextStep();
                } else if (e.target.matches(CONFIG.selectors.stepPrev)) {
                    e.preventDefault();
                    this.previousStep();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.matches(CONFIG.selectors.stepNav)) {
                    e.preventDefault();
                    this.nextStep();
                }
            });
        },

        /**
         * Navigate to next step with validation
         */
        nextStep: function() {
            if (this.validateCurrentStep()) {
                if (state.currentStep < state.totalSteps) {
                    this.showStep(state.currentStep + 1);
                }
            }
        },

        /**
         * Navigate to previous step
         */
        previousStep: function() {
            if (state.currentStep > 1) {
                this.showStep(state.currentStep - 1);
            }
        },

        /**
         * Show specific step
         */
        showStep: function(stepNumber) {
            // Hide all steps with fade out
            document.querySelectorAll(CONFIG.selectors.step).forEach(step => {
                if (!step.classList.contains(CONFIG.classes.hidden)) {
                    step.style.opacity = '0';
                    setTimeout(() => {
                        step.classList.add(CONFIG.classes.hidden);
                        step.setAttribute('aria-hidden', 'true');
                        step.style.opacity = '';
                    }, 200);
                }
            });

            // Show target step with fade in
            setTimeout(() => {
                const targetStep = document.querySelector(`${CONFIG.selectors.step}[data-step="${stepNumber}"]`);
                if (targetStep) {
                    targetStep.classList.remove(CONFIG.classes.hidden);
                    targetStep.setAttribute('aria-hidden', 'false');
                    targetStep.style.opacity = '0';
                    state.currentStep = stepNumber;

                    // Animate in
                    setTimeout(() => {
                        targetStep.style.transition = 'opacity 0.3s ease-in';
                        targetStep.style.opacity = '1';
                    }, 50);

                    // Focus management
                    const firstInput = targetStep.querySelector(CONFIG.selectors.input);
                    if (firstInput) {
                        firstInput.focus();
                    }

                    // Scroll to step
                    utils.scrollToElement(targetStep, 100);

                    this.updateUI();
                }
            }, 250);
        },

        /**
         * Validate current step
         */
        validateCurrentStep: function() {
            const currentStepEl = document.querySelector(`${CONFIG.selectors.step}[data-step="${state.currentStep}"]`);
            if (!currentStepEl) return true;

            const requiredFields = currentStepEl.querySelectorAll(`${CONFIG.selectors.field}[required]`);
            let isValid = true;

            requiredFields.forEach(field => {
                const input = field.querySelector(CONFIG.selectors.input);
                if (input && !input.value.trim()) {
                    this.showFieldError(field, 'This field is required');
                    isValid = false;
                } else {
                    this.clearFieldError(field);
                }
            });

            return isValid;
        },

        /**
         * Update progress indicator and navigation
         */
        updateUI: function() {
            // Update sidebar progress bar
            const progressFill = document.querySelector('.dp-progress-fill');
            const progressText = document.querySelector('.dp-progress-text');
            if (progressFill && progressText) {
                const progress = (state.currentStep / state.totalSteps) * 100;
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `${Math.round(progress)}% Complete`;
            }

            // Update sidebar steps
            const sidebarSteps = document.querySelectorAll('.dp-sidebar-step');
            sidebarSteps.forEach((step, index) => {
                const stepNum = index + 1;
                if (stepNum < state.currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (stepNum === state.currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            });

            // Update step tabs
            const stepTabs = document.querySelectorAll('.dp-step-tab');
            stepTabs.forEach((tab, index) => {
                const stepNum = index + 1;
                if (stepNum === state.currentStep) {
                    tab.classList.add('active');
                } else {
                    tab.classList.remove('active');
                }
            });

            // Update step cards visibility
            const stepCards = document.querySelectorAll('.dp-step-card');
            stepCards.forEach((card, index) => {
                const stepNum = index + 1;
                if (stepNum === state.currentStep) {
                    card.classList.remove('dp-hidden');
                } else {
                    card.classList.add('dp-hidden');
                }
            });

            // Update navigation buttons
            const nextBtn = document.querySelector('.dp-next-btn');
            const prevBtn = document.querySelector('.dp-prev-btn');

            if (prevBtn) {
                prevBtn.disabled = state.currentStep === 1;
                prevBtn.setAttribute('aria-disabled', state.currentStep === 1);
            }

            if (nextBtn) {
                nextBtn.disabled = state.currentStep === state.totalSteps;
                nextBtn.setAttribute('aria-disabled', state.currentStep === state.totalSteps);
                nextBtn.textContent = state.currentStep === state.totalSteps ? 'Complete' : 'Next Step';
            }

            // Update ARIA labels
            document.querySelectorAll('.dp-step-card').forEach((card, index) => {
                const stepNum = index + 1;
                card.setAttribute('aria-label', `Step ${stepNum} of ${state.totalSteps}`);
                card.setAttribute('aria-hidden', stepNum !== state.currentStep);
            });
        }
    };

    // Visual Feedback
    const visualFeedback = {
        /**
         * Initialize visual feedback
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind selection events
         */
        bindEvents: function() {
            document.addEventListener('click', (e) => {
                // Selection highlighting
                if (e.target.matches(`${CONFIG.selectors.blindType}, ${CONFIG.selectors.fabricOption}, ${CONFIG.selectors.controlOption}`)) {
                    this.handleSelection(e.target);
                }
            });

            // Hover effects
            document.addEventListener('mouseenter', (e) => {
                if (e.target.matches(`${CONFIG.selectors.fabricOption}, ${CONFIG.selectors.blindType}`)) {
                    e.target.classList.add(CONFIG.classes.hover);
                }
            });

            document.addEventListener('mouseleave', (e) => {
                if (e.target.matches(`${CONFIG.selectors.fabricOption}, ${CONFIG.selectors.blindType}`)) {
                    e.target.classList.remove(CONFIG.classes.hover);
                }
            });

            // Real-time validation
            document.addEventListener('input', utils.debounce((e) => {
                if (e.target.matches(CONFIG.selectors.dimensionInput)) {
                    this.validateDimension(e.target);
                }
            }, 300));
        },

        /**
         * Handle option selection
         */
        handleSelection: function(element) {
            // Remove selected class from siblings
alert(1);
            const siblings = element.parentNode.querySelectorAll(`.${CONFIG.classes.selected}`);
            siblings.forEach(sibling => sibling.classList.remove(CONFIG.classes.selected));

            // Add selected class to clicked element
            element.classList.add(CONFIG.classes.selected);

            // Update ARIA
            element.setAttribute('aria-selected', 'true');
            siblings.forEach(sibling => sibling.setAttribute('aria-selected', 'false'));
        },

        /**
         * Validate dimension input
         */
        validateDimension: function(input) {
            const value = parseFloat(input.value);
            const min = parseFloat(input.getAttribute('min')) || 0;
            const max = parseFloat(input.getAttribute('max')) || Infinity;

            const field = input.closest(CONFIG.selectors.field);

            if (value < min || value > max || isNaN(value)) {
                this.showFieldError(field, `Value must be between ${min} and ${max}`);
            } else {
                this.clearFieldError(field);
            }
        },

        /**
         * Show field error
         */
        showFieldError: function(field, message) {
            field.classList.add(CONFIG.classes.error);
            field.classList.remove('field-valid');

            let errorEl = field.querySelector('.field-error');
            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'field-error';
                errorEl.setAttribute('role', 'alert');
                field.appendChild(errorEl);
            }
            errorEl.textContent = message;

            // Shake animation for error
            field.style.animation = 'none';
            setTimeout(() => {
                field.style.animation = 'dp-shake 0.5s ease-in-out';
            }, 10);
        },

        /**
         * Clear field error
         */
        clearFieldError: function(field) {
            field.classList.remove(CONFIG.classes.error);
            const errorEl = field.querySelector('.field-error');
            if (errorEl) {
                errorEl.style.opacity = '0';
                setTimeout(() => errorEl.remove(), 200);
            }

            // Add success state if field has value
            const input = field.querySelector(CONFIG.selectors.input);
            if (input && input.value.trim()) {
                field.classList.add('field-valid');
            }
        }
    };

    // Enhanced Interactivity
    const enhancedInteractivity = {
        /**
         * Initialize enhanced features
         */
        init: function() {
            this.addNumberButtons();
            this.initFabricLightbox();
            this.initStickySummary();
            this.bindEvents();
        },

        /**
         * Add +/- buttons to number inputs
         */
        addNumberButtons: function() {
            const numberInputs = document.querySelectorAll(CONFIG.selectors.numberInput);
            numberInputs.forEach(input => {
                if (input.nextElementSibling?.classList.contains('number-buttons')) return;

                const buttonContainer = document.createElement('div');
                buttonContainer.className = 'number-buttons';

                const minusBtn = document.createElement('button');
                minusBtn.type = 'button';
                minusBtn.className = 'number-btn number-minus';
                minusBtn.textContent = '-';
                minusBtn.setAttribute('aria-label', 'Decrease value');

                const plusBtn = document.createElement('button');
                plusBtn.type = 'button';
                plusBtn.className = 'number-btn number-plus';
                plusBtn.textContent = '+';
                plusBtn.setAttribute('aria-label', 'Increase value');

                buttonContainer.appendChild(minusBtn);
                buttonContainer.appendChild(plusBtn);
                input.parentNode.insertBefore(buttonContainer, input.nextSibling);
            });
        },

        /**
         * Initialize fabric lightbox
         */
        initFabricLightbox: function() {
            // Create modal if it doesn't exist
            if (!document.querySelector(CONFIG.selectors.lightbox)) {
                const modal = document.createElement('div');
                modal.className = `dp-modal ${CONFIG.selectors.lightbox.substring(1)}`;
                modal.setAttribute('role', 'dialog');
                modal.setAttribute('aria-modal', 'true');
                modal.innerHTML = `
                    <div class="modal-overlay"></div>
                    <div class="modal-content">
                        <button class="modal-close" aria-label="Close lightbox">&times;</button>
                        <div class="modal-body"></div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
        },

        /**
         * Initialize sticky summary
         */
        initStickySummary: function() {
            const summary = document.querySelector(CONFIG.selectors.summary);
            if (!summary) return;

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) {
                        summary.classList.add(CONFIG.classes.sticky);
                    } else {
                        summary.classList.remove(CONFIG.classes.sticky);
                    }
                });
            });

            observer.observe(summary);
        },

        /**
         * Bind enhanced interaction events
         */
        bindEvents: function() {
            // Number buttons
            document.addEventListener('click', (e) => {
                if (e.target.matches('.number-btn')) {
                    e.preventDefault();
                    this.handleNumberButton(e.target);
                }
            });

            // Fabric lightbox
            document.addEventListener('click', (e) => {
                if (e.target.matches(CONFIG.selectors.fabricOption + '[data-lightbox]')) {
                    e.preventDefault();
                    this.openFabricLightbox(e.target);
                }
            });

            // Modal close
            document.addEventListener('click', (e) => {
                if (e.target.matches('.modal-overlay, .modal-close')) {
                    this.closeModal();
                }
            });

            // Sidebar navigation
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-sidebar-step')) {
                    e.preventDefault();
                    const step = parseInt(e.target.dataset.step) || parseInt(e.target.closest('.dp-sidebar-step').dataset.step);
                    progressiveDisclosure.showStep(step);
                }
            });

            // Step tabs (mobile)
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-step-tab')) {
                    e.preventDefault();
                    const step = parseInt(e.target.dataset.step);
                    progressiveDisclosure.showStep(step);
                }
            });

            // Footer navigation buttons
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-prev-btn')) {
                    e.preventDefault();
                    progressiveDisclosure.previousStep();
                } else if (e.target.matches('.dp-next-btn')) {
                    e.preventDefault();
                    progressiveDisclosure.nextStep();
                }
            });

            // Header action buttons
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-save-btn')) {
                    e.preventDefault();
                    this.handleSaveProgress();
                } else if (e.target.matches('.dp-reset-btn')) {
                    e.preventDefault();
                    this.handleResetConfiguration();
                }
            });

            // Keyboard for modal
            document.addEventListener('keydown', (e) => {
                if (state.modalOpen && e.key === 'Escape') {
                    this.closeModal();
                }
            });

            // Responsive behavior
            window.addEventListener('resize', utils.debounce(() => {
                this.handleResponsive();
            }, 250));
        },

        /**
         * Handle number button clicks
         */
        handleNumberButton: function(button) {
            const input = button.parentNode.previousElementSibling;
            if (!input || input.type !== 'number') return;

            const step = parseFloat(input.step) || 1;
            const currentValue = parseFloat(input.value) || 0;
            const min = parseFloat(input.min) || -Infinity;
            const max = parseFloat(input.max) || Infinity;

            let newValue;
            if (button.classList.contains('number-plus')) {
                newValue = Math.min(currentValue + step, max);
            } else {
                newValue = Math.max(currentValue - step, min);
            }

            input.value = newValue;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },

        /**
         * Open fabric lightbox
         */
        openFabricLightbox: function(trigger) {
            const modal = document.querySelector(CONFIG.selectors.lightbox);
            const modalBody = modal.querySelector('.modal-body');

            // Load fabric details (assuming data attributes)
            const fabricId = trigger.dataset.fabricId;
            const fabricImage = trigger.dataset.image;
            const fabricName = trigger.dataset.name;

            modalBody.innerHTML = `
                <img src="${fabricImage}" alt="${fabricName}" class="fabric-large-image">
                <h3>${fabricName}</h3>
                <p>Fabric details and specifications...</p>
            `;

            modal.classList.add(CONFIG.classes.active);
            state.modalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        /**
         * Close modal
         */
        closeModal: function() {
            const modal = document.querySelector(CONFIG.selectors.modal + '.' + CONFIG.classes.active);
            if (modal) {
                modal.classList.remove(CONFIG.classes.active);
                state.modalOpen = false;
                document.body.style.overflow = '';
            }
        },

        /**
         * Handle responsive behavior for new layout
         */
        handleResponsive: function() {
            const isMobile = window.innerWidth < 768;
            const isTablet = window.innerWidth < 1024 && window.innerWidth >= 768;
            const layoutWrapper = document.querySelector('.dp-layout-wrapper');
            const sidebar = document.querySelector('.dp-sidebar-nav');
            const mainContent = document.querySelector('.dp-main-content');
            const stepTabs = document.querySelector('.dp-step-tabs');

            if (isMobile) {
                // Mobile: Stack sidebar below main content, show step tabs
                if (layoutWrapper) {
                    layoutWrapper.classList.remove('dp-layout-sidebar-main');
                    layoutWrapper.classList.add('dp-layout-mobile-stack');
                }
                if (sidebar) {
                    sidebar.classList.add('dp-sidebar-collapsed');
                }
                if (stepTabs) {
                    stepTabs.classList.remove('dp-hidden');
                    stepTabs.classList.add('dp-layout-flex');
                }
                // Hide footer actions on mobile, rely on step tabs
                const footerActions = document.querySelector('.dp-content-footer .dp-footer-actions');
                if (footerActions) {
                    footerActions.classList.add('dp-hidden');
                }
            } else if (isTablet) {
                // Tablet: Show condensed sidebar, hide step tabs
                if (layoutWrapper) {
                    layoutWrapper.classList.add('dp-layout-sidebar-main');
                    layoutWrapper.classList.remove('dp-layout-mobile-stack');
                }
                if (sidebar) {
                    sidebar.classList.add('dp-sidebar-condensed');
                    sidebar.classList.remove('dp-sidebar-collapsed');
                }
                if (stepTabs) {
                    stepTabs.classList.add('dp-hidden');
                }
                const footerActions = document.querySelector('.dp-content-footer .dp-footer-actions');
                if (footerActions) {
                    footerActions.classList.remove('dp-hidden');
                }
            } else {
                // Desktop: Full sidebar, hide step tabs
                if (layoutWrapper) {
                    layoutWrapper.classList.add('dp-layout-sidebar-main');
                    layoutWrapper.classList.remove('dp-layout-mobile-stack');
                }
                if (sidebar) {
                    sidebar.classList.remove('dp-sidebar-condensed', 'dp-sidebar-collapsed');
                }
                if (stepTabs) {
                    stepTabs.classList.add('dp-hidden');
                }
                const footerActions = document.querySelector('.dp-content-footer .dp-footer-actions');
                if (footerActions) {
                    footerActions.classList.remove('dp-hidden');
                }
            }

            // Adjust step cards layout
            const stepCards = document.querySelectorAll('.dp-step-card');
            stepCards.forEach(card => {
                if (isMobile) {
                    card.classList.add('dp-card-mobile');
                    card.classList.remove('dp-card-tablet', 'dp-card-desktop');
                } else if (isTablet) {
                    card.classList.add('dp-card-tablet');
                    card.classList.remove('dp-card-mobile', 'dp-card-desktop');
                } else {
                    card.classList.add('dp-card-desktop');
                    card.classList.remove('dp-card-mobile', 'dp-card-tablet');
                }
            });

            // Adjust header layout
            const headerPanel = document.querySelector('.dp-content-header');
            if (headerPanel) {
                if (isMobile) {
                    headerPanel.classList.add('dp-header-mobile');
                } else {
                    headerPanel.classList.remove('dp-header-mobile');
                }
            }

            // Adjust summary positioning
            const summary = document.querySelector(CONFIG.selectors.summary);
            if (summary) {
                if (isMobile) {
                    summary.classList.remove(CONFIG.classes.sticky);
                    summary.classList.add('dp-summary-modal');
                } else {
                    summary.classList.add(CONFIG.classes.sticky);
                    summary.classList.remove('dp-summary-modal');
                }
            }
        },

        /**
         * Handle save progress action
         */
        handleSaveProgress: function() {
            // Collect current form data
            const formData = {};
            document.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.name) {
                    formData[input.name] = input.value;
                }
            });

            // Save to localStorage
            localStorage.setItem('dp_configuration_progress', JSON.stringify({
                step: state.currentStep,
                data: formData,
                timestamp: Date.now()
            }));

            // Show success notification
            componentInjector.showNotification('Progress saved successfully!', 'success');
        },

        /**
         * Handle reset configuration action
         */
        handleResetConfiguration: function() {
            if (confirm('Are you sure you want to reset the entire configuration? This action cannot be undone.')) {
                // Clear all form inputs
                document.querySelectorAll('input, select, textarea').forEach(input => {
                    if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });

                // Reset to first step
                progressiveDisclosure.showStep(1);

                // Clear saved progress
                localStorage.removeItem('dp_configuration_progress');

                // Show notification
                componentInjector.showNotification('Configuration reset successfully!', 'info');
            }
        }
    };

    // Accessibility Improvements
    const accessibility = {
        /**
         * Initialize accessibility features
         */
        init: function() {
            this.updateAriaAttributes();
            this.bindKeyboardEvents();
        },

        /**
         * Update ARIA attributes
         */
        updateAriaAttributes: function() {
            // Progress indicator
            const progress = document.querySelector(CONFIG.selectors.progress);
            if (progress) {
                progress.setAttribute('role', 'progressbar');
                progress.setAttribute('aria-valuemin', '0');
                progress.setAttribute('aria-valuemax', '100');
            }

            // Form fields
            document.querySelectorAll(CONFIG.selectors.field).forEach(field => {
                const label = field.querySelector('label');
                const input = field.querySelector(CONFIG.selectors.input);

                if (label && input) {
                    const labelId = `label-${Math.random().toString(36).substr(2, 9)}`;
                    label.id = labelId;
                    input.setAttribute('aria-labelledby', labelId);
                }
            });
        },

        /**
         * Bind keyboard navigation events
         */
        bindKeyboardEvents: function() {
            document.addEventListener('keydown', (e) => {
                // Tab navigation within steps
                if (e.key === 'Tab') {
                    this.handleTabNavigation(e);
                }
            });
        },

        /**
         * Handle tab navigation
         */
        handleTabNavigation: function(e) {
            const activeElement = document.activeElement;
            const currentStep = activeElement.closest(CONFIG.selectors.step);

            if (!currentStep) return;

            const focusableElements = currentStep.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                if (activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }
    };

    // Initialize all modules when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we're on a dynamic product page
        if (document.querySelector(CONFIG.selectors.container)) {
            progressiveDisclosure.init();
            visualFeedback.init();
            enhancedInteractivity.init();
            accessibility.init();
        }
    });

    // Custom Overlay System
    const customOverlay = {
        /**
         * Initialize overlay system
         */
        init: function() {
            this.createOverlayContainer();
            this.bindEvents();
        },

        /**
         * Create overlay container
         */
        createOverlayContainer: function() {
            if (document.querySelector('.dp-overlay')) return;

            const overlay = document.createElement('div');
            overlay.className = 'dp-overlay';
            overlay.innerHTML = `
                <div class="dp-overlay-content">
                    <button class="dp-overlay-close" aria-label="Close overlay">&times;</button>
                    <div class="dp-overlay-body"></div>
                </div>
            `;
            document.body.appendChild(overlay);
        },

        /**
         * Bind overlay events
         */
        bindEvents: function() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-overlay-close') || e.target.matches('.dp-overlay')) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && document.querySelector('.dp-overlay.active')) {
                    this.close();
                }
            });
        },

        /**
         * Show overlay with content
         */
        show: function(content, options = {}) {
            const overlay = document.querySelector('.dp-overlay');
            const overlayBody = overlay.querySelector('.dp-overlay-body');

            overlayBody.innerHTML = content;
            overlay.classList.add('active');

            // Focus management
            const focusableElement = overlayBody.querySelector('button, [href], input, select, textarea');
            if (focusableElement) {
                focusableElement.focus();
            }

            // Callbacks
            if (options.onShow) options.onShow();

            return overlay;
        },

        /**
         * Close overlay
         */
        close: function() {
            const overlay = document.querySelector('.dp-overlay');
            overlay.classList.remove('active');

            // Return focus to trigger element
            if (state.lastFocusedElement) {
                state.lastFocusedElement.focus();
            }
        }
    };

    // Advanced DOM Manipulation
    const domManipulator = {
        /**
         * Initialize DOM manipulation features
         */
        init: function() {
            this.injectCustomComponents();
            this.enhanceExistingElements();
            this.addDynamicStyling();
        },

        /**
         * Inject custom components into the DOM
         */
        injectCustomComponents: function() {
            this.injectProgressIndicator();
            this.injectActionButtons();
            this.injectStatusIndicators();
            this.injectHelpTooltips();
        },

        /**
         * Inject enhanced progress indicator as sidebar navigation
         */
        injectProgressIndicator: function() {
            const container = document.querySelector(CONFIG.selectors.container);
            if (!container || document.querySelector('.dp-sidebar-nav')) return;

            // Create main layout wrapper
            const layoutWrapper = document.createElement('div');
            layoutWrapper.className = 'dp-layout-wrapper dp-layout-grid dp-layout-sidebar-main';

            // Create sidebar
            const sidebarHTML = `
                <aside class="dp-sidebar-nav dp-glass-effect dp-layout-column dp-layout-gap-medium">
                    <div class="dp-sidebar-header">
                        <h2 class="dp-sidebar-title">Configuration Steps</h2>
                    </div>
                    <nav class="dp-sidebar-steps dp-layout-column dp-layout-gap-small">
                        <div class="dp-sidebar-step active" data-step="1">
                            <div class="dp-step__circle">
                                <span class="dp-step__number">1</span>
                            </div>
                            <div class="dp-step__content">
                                <span class="dp-step__label">Dimensions</span>
                                <span class="dp-step__desc">Set width and height</span>
                            </div>
                        </div>
                        <div class="dp-sidebar-step" data-step="2">
                            <div class="dp-step__circle">
                                <span class="dp-step__number">2</span>
                            </div>
                            <div class="dp-step__content">
                                <span class="dp-step__label">Fabric</span>
                                <span class="dp-step__desc">Choose material</span>
                            </div>
                        </div>
                        <div class="dp-sidebar-step" data-step="3">
                            <div class="dp-step__circle">
                                <span class="dp-step__number">3</span>
                            </div>
                            <div class="dp-step__content">
                                <span class="dp-step__label">Controls</span>
                                <span class="dp-step__desc">Select operation type</span>
                            </div>
                        </div>
                        <div class="dp-sidebar-step" data-step="4">
                            <div class="dp-step__circle">
                                <span class="dp-step__number">4</span>
                            </div>
                            <div class="dp-step__content">
                                <span class="dp-step__label">Summary</span>
                                <span class="dp-step__desc">Review and confirm</span>
                            </div>
                        </div>
                    </nav>
                    <div class="dp-sidebar-footer">
                        <div class="dp-progress-indicator">
                            <div class="dp-progress-bar">
                                <div class="dp-progress-fill"></div>
                            </div>
                            <span class="dp-progress-text">25% Complete</span>
                        </div>
                    </div>
                </aside>
            `;

            // Create main content area
            const mainContent = document.createElement('main');
            mainContent.className = 'dp-main-content dp-layout-column dp-layout-gap-large';

            // Move existing content to main area
            while (container.firstChild) {
                mainContent.appendChild(container.firstChild);
            }

            // Assemble layout
            layoutWrapper.innerHTML = sidebarHTML;
            layoutWrapper.appendChild(mainContent);
            container.appendChild(layoutWrapper);
        },

        /**
         * Inject action buttons
         */
        injectActionButtons: function() {
            const groups = document.querySelectorAll(CONFIG.selectors.step);
            groups.forEach((group, index) => {
                if (group.querySelector('.dp-custom-actions')) return;

                const actionsHTML = `
                    <div class="dp-custom-actions dp-layout-flex dp-layout-gap-medium dp-layout-justify-center">
                        <button class="dp-custom-button dp-action-help dp-layout-flex-item" data-step="${index + 1}">
                            <span>?</span>
                            Help
                        </button>
                        <button class="dp-custom-button dp-action-preview dp-layout-flex-item" data-step="${index + 1}">
                            <span>👁</span>
                            Preview
                        </button>
                    </div>
                `;

                group.appendChild(this.createElementFromHTML(actionsHTML));
            });
        },

        /**
         * Inject status indicators
         */
        injectStatusIndicators: function() {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.nextElementSibling?.classList.contains('dp-status-indicator')) return;

                const indicator = document.createElement('div');
                indicator.className = 'dp-status-indicator';
                indicator.innerHTML = '<span class="dp-status-icon"></span>';

                input.parentNode.insertBefore(indicator, input.nextSibling);
            });
        },

        /**
         * Inject help tooltips
         */
        injectHelpTooltips: function() {
            const helpData = {
                width: 'Enter the width in centimeters',
                height: 'Enter the height in centimeters',
                fabric: 'Choose your preferred fabric material',
                control: 'Select the control type for your blind'
            };

            Object.keys(helpData).forEach(key => {
                const elements = document.querySelectorAll(`[data-help="${key}"]`);
                elements.forEach(element => {
                    if (element.querySelector('.dp-tooltip')) return;

                    const tooltip = document.createElement('div');
                    tooltip.className = 'dp-tooltip svelte-tooltip';
                    tooltip.textContent = helpData[key];
                    element.appendChild(tooltip);

                    element.addEventListener('mouseenter', () => {
                        tooltip.classList.add('show');
                    });

                    element.addEventListener('mouseleave', () => {
                        tooltip.classList.remove('show');
                    });
                });
            });
        },

        /**
         * Enhance existing elements with custom features
         */
        enhanceExistingElements: function() {
            this.addFloatingLabels();
            this.enhanceButtons();
            this.addInputIcons();
            this.addLoadingStates();
        },

        /**
         * Add floating labels to inputs
         */
        addFloatingLabels: function() {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.previousElementSibling?.tagName === 'LABEL' && !input.parentNode.classList.contains('dp-floating-label')) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'dp-floating-label dp-layout-relative dp-layout-flex dp-layout-column';

                    const label = input.previousElementSibling;
                    input.parentNode.insertBefore(wrapper, label);
                    wrapper.appendChild(label);
                    wrapper.appendChild(input);
                }
            });
        },

        /**
         * Enhance buttons with custom styling
         */
        enhanceButtons: function() {
            const buttons = document.querySelectorAll('button:not(.dp-custom-button)');
            buttons.forEach(button => {
                button.classList.add('dp-custom-button');
            });
        },

        /**
         * Add icons to inputs
         */
        addInputIcons: function() {
            const iconMap = {
                number: '📏',
                text: '✏️',
                email: '📧',
                select: '▼'
            };

            const inputs = document.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.parentNode.classList.contains('dp-custom-input')) return;

                const wrapper = document.createElement('div');
                wrapper.className = 'dp-custom-input dp-layout-relative dp-layout-flex dp-layout-align-center';

                const icon = iconMap[input.type] || iconMap[input.tagName.toLowerCase()] || '📝';
                wrapper.innerHTML = `<span class="input-icon dp-layout-absolute">${icon}</span>`;

                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
            });
        },

        /**
         * Add loading states to interactive elements
         */
        addLoadingStates: function() {
            const interactiveElements = document.querySelectorAll('button, .dp-thumbnail, .dp-fabric__item');
            interactiveElements.forEach(element => {
                element.addEventListener('click', () => {
                    element.classList.add('dp-loading');
                    setTimeout(() => {
                        element.classList.remove('dp-loading');
                    }, 1000);
                });
            });
        },

        /**
         * Add dynamic styling based on user interactions
         */
        addDynamicStyling: function() {
            this.addHoverEffects();
            this.addFocusAnimations();
            this.addScrollEffects();
        },

        /**
         * Add enhanced hover effects
         */
        addHoverEffects: function() {
            const hoverElements = document.querySelectorAll('.dp-thumbnail, .dp-fabric__item, .dp-control__option');
            hoverElements.forEach(element => {
                element.addEventListener('mouseenter', () => {
                    element.classList.add('dp-bounce-in');
                });

                element.addEventListener('mouseleave', () => {
                    element.classList.remove('dp-bounce-in');
                });
            });
        },

        /**
         * Add focus animations
         */
        addFocusAnimations: function() {
            const focusElements = document.querySelectorAll('input, select, button');
            focusElements.forEach(element => {
                element.addEventListener('focus', () => {
                    element.classList.add('dp-pulse');
                });

                element.addEventListener('blur', () => {
                    element.classList.remove('dp-pulse');
                });
            });
        },

        /**
         * Add scroll-based effects
         */
        addScrollEffects: function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('dp-shimmer');
                        setTimeout(() => {
                            entry.target.classList.remove('dp-shimmer');
                        }, 1500);
                    }
                });
            });

            document.querySelectorAll(CONFIG.selectors.step).forEach(step => {
                observer.observe(step);
            });
        },

        /**
         * Create element from HTML string
         */
        createElementFromHTML: function(htmlString) {
            const div = document.createElement('div');
            div.innerHTML = htmlString.trim();
            return div.firstChild;
        }
    };

    // Advanced Animation System
    const animationSystem = {
        /**
         * Initialize animation system
         */
        init: function() {
            this.bindAnimationEvents();
            this.addScrollAnimations();
        },

        /**
         * Bind animation events
         */
        bindAnimationEvents: function() {
            // Animate elements on interaction
            document.addEventListener('click', (e) => {
                if (e.target.matches('.dp-thumbnail, .dp-fabric__item')) {
                    this.animateSelection(e.target);
                }
            });

            // Animate form validation
            document.addEventListener('input', utils.debounce((e) => {
                if (e.target.matches('input, select')) {
                    this.animateValidation(e.target);
                }
            }, 300));
        },

        /**
         * Animate element selection
         */
        animateSelection: function(element) {
            element.style.animation = 'none';
            element.offsetHeight; // Trigger reflow
            element.style.animation = 'dp-bounce-in 0.6s ease-out';
        },

        /**
         * Animate validation feedback
         */
        animateValidation: function(element) {
            const isValid = this.validateField(element);
            const statusIndicator = element.nextElementSibling;

            if (statusIndicator && statusIndicator.classList.contains('dp-status-indicator')) {
                const icon = statusIndicator.querySelector('.dp-status-icon');

                if (isValid) {
                    icon.textContent = '✓';
                    icon.style.color = 'var(--dp-primary-color)';
                    statusIndicator.style.animation = 'dp-bounce-in 0.4s ease-out';
                } else {
                    icon.textContent = '⚠';
                    icon.style.color = '#ff6b6b';
                    statusIndicator.style.animation = 'dp-pulse 0.6s ease-in-out';
                }
            }
        },

        /**
         * Validate field
         */
        validateField: function(field) {
            if (field.hasAttribute('required') && !field.value.trim()) {
                return false;
            }

            if (field.type === 'number') {
                const value = parseFloat(field.value);
                const min = parseFloat(field.min) || 0;
                const max = parseFloat(field.max) || Infinity;
                return value >= min && value <= max && !isNaN(value);
            }

            return true;
        },

        /**
         * Add scroll-triggered animations
         */
        addScrollAnimations: function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationDelay = '0.2s';
                        entry.target.style.animationFillMode = 'both';
                        entry.target.classList.add('dp-slide-in');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll(CONFIG.selectors.step).forEach(step => {
                observer.observe(step);
            });
        }
    };

    // Component Injection System
    const componentInjector = {
        /**
         * Initialize component injection
         */
        init: function() {
            this.injectCustomCards();
            this.injectNotificationSystem();
            this.injectQuickActions();
        },

        /**
         * Inject custom cards around content sections with new layout
         */
        injectCustomCards: function() {
            const mainContent = document.querySelector('.dp-main-content');
            if (!mainContent) return;

            // Create header panel for the main content
            const headerPanel = document.createElement('header');
            headerPanel.className = 'dp-content-header dp-glass-effect dp-layout-flex dp-layout-justify-between dp-layout-align-center';
            headerPanel.innerHTML = `
                <div class="dp-header-info">
                    <h1 class="dp-product-title">Custom Blind Configuration</h1>
                    <p class="dp-product-subtitle">Design your perfect window treatment</p>
                </div>
                <div class="dp-header-actions dp-layout-flex dp-layout-gap-small">
                    <button class="dp-header-btn dp-save-btn">💾 Save</button>
                    <button class="dp-header-btn dp-reset-btn">🔄 Reset</button>
                </div>
            `;

            // Create steps container with enhanced layout
            const stepsContainer = document.createElement('div');
            stepsContainer.className = 'dp-steps-container dp-layout-column dp-layout-gap-large';

            // Create step navigation tabs for mobile/small screens
            const stepTabs = document.createElement('nav');
            stepTabs.className = 'dp-step-tabs dp-layout-flex dp-layout-gap-small dp-layout-responsive';
            stepTabs.innerHTML = `
                <button class="dp-step-tab active" data-step="1">Dimensions</button>
                <button class="dp-step-tab" data-step="2">Fabric</button>
                <button class="dp-step-tab" data-step="3">Controls</button>
                <button class="dp-step-tab" data-step="4">Summary</button>
            `;

            // Create steps wrapper
            const stepsWrapper = document.createElement('div');
            stepsWrapper.className = 'dp-steps-wrapper dp-layout-column dp-layout-gap-medium';

            const sections = mainContent.querySelectorAll(CONFIG.selectors.step);
            sections.forEach((section, index) => {
                if (section.classList.contains('dp-custom-card')) return;

                // Wrap each step in a card with enhanced structure
                const cardWrapper = document.createElement('section');
                cardWrapper.className = 'dp-step-card dp-custom-card dp-glass-effect dp-layout-column dp-layout-gap-medium';
                cardWrapper.setAttribute('data-step', index + 1);

                const cardHeader = document.createElement('header');
                cardHeader.className = 'dp-card-header dp-layout-flex dp-layout-justify-between dp-layout-align-center';
                cardHeader.innerHTML = `
                    <h3 class="dp-card-title">Step ${index + 1}: ${['Dimensions', 'Fabric', 'Controls', 'Summary'][index]}</h3>
                    <div class="dp-card-status">
                        <span class="dp-status-indicator"></span>
                    </div>
                `;

                const cardContent = document.createElement('div');
                cardContent.className = 'dp-card-content dp-layout-column dp-layout-gap-medium';

                // Move existing content
                while (section.firstChild) {
                    cardContent.appendChild(section.firstChild);
                }

                cardWrapper.appendChild(cardHeader);
                cardWrapper.appendChild(cardContent);
                stepsWrapper.appendChild(cardWrapper);

                // Remove original section
                section.remove();
            });

            // Create footer panel
            const footerPanel = document.createElement('footer');
            footerPanel.className = 'dp-content-footer dp-glass-effect dp-layout-flex dp-layout-justify-between dp-layout-align-center';
            footerPanel.innerHTML = `
                <div class="dp-footer-info">
                    <span class="dp-help-link">Need help? <a href="#help">View guide</a></span>
                </div>
                <div class="dp-footer-actions dp-layout-flex dp-layout-gap-small">
                    <button class="dp-footer-btn dp-prev-btn" disabled>Previous</button>
                    <button class="dp-footer-btn dp-next-btn">Next Step</button>
                </div>
            `;

            // Assemble the main content
            stepsContainer.appendChild(stepTabs);
            stepsContainer.appendChild(stepsWrapper);
            mainContent.innerHTML = ''; // Clear existing content
            mainContent.appendChild(headerPanel);
            mainContent.appendChild(stepsContainer);
            mainContent.appendChild(footerPanel);
        },

        /**
         * Inject notification system
         */
        injectNotificationSystem: function() {
            if (document.querySelector('.dp-notifications')) return;

            const notificationHTML = `
                <div class="dp-notifications" aria-live="polite"></div>
            `;

            document.body.insertAdjacentHTML('beforeend', notificationHTML);
        },

        /**
         * Show notification
         */
        showNotification: function(message, type = 'info', duration = 3000) {
            const container = document.querySelector('.dp-notifications');
            if (!container) return;

            const notification = document.createElement('div');
            notification.className = `dp-notification dp-notification--${type}`;
            notification.innerHTML = `
                <span class="dp-notification__message">${message}</span>
                <button class="dp-notification__close" aria-label="Close notification">&times;</button>
            `;

            container.appendChild(notification);

            // Animate in
            setTimeout(() => notification.classList.add('active'), 10);

            // Auto remove
            setTimeout(() => {
                notification.classList.remove('active');
                setTimeout(() => notification.remove(), 300);
            }, duration);

            // Manual close
            notification.querySelector('.dp-notification__close').addEventListener('click', () => {
                notification.classList.remove('active');
                setTimeout(() => notification.remove(), 300);
            });
        },

        /**
         * Inject quick actions panel
         */
        injectQuickActions: function() {
            if (document.querySelector('.dp-quick-actions')) return;

            const actionsHTML = `
                <div class="dp-quick-actions dp-layout-fixed dp-layout-column dp-layout-gap-small">
                    <button class="dp-quick-action dp-layout-flex-item" data-action="save">
                        <span>💾</span>
                        Save Progress
                    </button>
                    <button class="dp-quick-action dp-layout-flex-item" data-action="reset">
                        <span>🔄</span>
                        Reset
                    </button>
                    <button class="dp-quick-action dp-layout-flex-item" data-action="help">
                        <span>❓</span>
                        Help
                    </button>
                </div>
            `;

            document.querySelector(CONFIG.selectors.container).appendChild(
                this.createElementFromHTML(actionsHTML)
            );
        },

        /**
         * Create element from HTML string
         */
        createElementFromHTML: function(htmlString) {
            const div = document.createElement('div');
            div.innerHTML = htmlString.trim();
            return div.firstChild;
        }
    };

    // Enhanced Accessibility Features
    const enhancedAccessibility = {
        /**
         * Initialize enhanced accessibility
         */
        init: function() {
            this.addAriaLabels();
            this.improveKeyboardNavigation();
            this.addScreenReaderSupport();
            this.handleReducedMotion();
        },

        /**
         * Add comprehensive ARIA labels
         */
        addAriaLabels: function() {
            // Progress indicator
            const progress = document.querySelector('.dp-custom-progress');
            if (progress) {
                progress.setAttribute('role', 'progressbar');
                progress.setAttribute('aria-label', 'Configuration progress');
            }

            // Form sections
            document.querySelectorAll(CONFIG.selectors.step).forEach((step, index) => {
                step.setAttribute('aria-label', `Configuration step ${index + 1}`);
                step.setAttribute('role', 'region');
            });

            // Interactive elements
            document.querySelectorAll('.dp-thumbnail, .dp-fabric__item').forEach(item => {
                item.setAttribute('role', 'button');
                item.setAttribute('tabindex', '0');
            });
        },

        /**
         * Improve keyboard navigation
         */
        improveKeyboardNavigation: function() {
            // Make thumbnails keyboard accessible
            document.addEventListener('keydown', (e) => {
                if (e.target.matches('.dp-thumbnail, .dp-fabric__item')) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        e.target.click();
                    }
                }
            });

            // Add skip links
            this.addSkipLinks();
        },

        /**
         * Add skip links for better navigation
         */
        addSkipLinks: function() {
            const skipLinks = document.createElement('div');
            skipLinks.className = 'dp-skip-links';
            skipLinks.innerHTML = `
                <a href="#dp_product" class="dp-skip-link">Skip to main content</a>
                <a href="#dp-summary" class="dp-skip-link">Skip to summary</a>
            `;

            document.body.insertAdjacentElement('afterbegin', skipLinks);
        },

        /**
         * Add screen reader support
         */
        addScreenReaderSupport: function() {
            // Announce dynamic content changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        const announcement = document.createElement('div');
                        announcement.setAttribute('aria-live', 'polite');
                        announcement.setAttribute('aria-atomic', 'true');
                        announcement.className = 'sr-only';
                        announcement.textContent = 'Content updated';
                        document.body.appendChild(announcement);

                        setTimeout(() => announcement.remove(), 1000);
                    }
                });
            });

            observer.observe(document.querySelector(CONFIG.selectors.container), {
                childList: true,
                subtree: true
            });
        },

        /**
         * Handle reduced motion preferences
         */
        handleReducedMotion: function() {
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            if (prefersReducedMotion) {
                document.documentElement.style.setProperty('--dp-transition', 'none');
                document.documentElement.style.setProperty('--dp-animation-duration', '0s');
            }
        }
    };

    // Initialize all enhanced modules when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🏁 DOM Content Loaded - checking for DP container');

        // Check if we're on a dynamic product page
        if (document.querySelector(CONFIG.selectors.container)) {
            console.log('✅ DP container found - applying enhancements');

            // Apply enhancements immediately without waiting for Svelte
            progressiveDisclosure.init();
            visualFeedback.init();
            enhancedInteractivity.init();
            accessibility.init();

            // Initialize new enhanced modules
            customOverlay.init();
            domManipulator.init();
            animationSystem.init();
            componentInjector.init();
            enhancedAccessibility.init();

            console.log('✨ All enhancements applied successfully');
        } else {
            console.log('❌ DP container not found');
        }
    });

    // Expose enhanced API for debugging and external use
    window.dynamicProductEnhancements = {
        progressiveDisclosure,
        visualFeedback,
        enhancedInteractivity,
        accessibility,
        customOverlay,
        domManipulator,
        animationSystem,
        componentInjector,
        enhancedAccessibility,
        state,
        utils,
        showNotification: componentInjector.showNotification.bind(componentInjector),
        showOverlay: customOverlay.show.bind(customOverlay),
        closeOverlay: customOverlay.close.bind(customOverlay)
    };

})();
