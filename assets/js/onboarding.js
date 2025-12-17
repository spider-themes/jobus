/**
 * Jobus Onboarding Wizard JavaScript
 *
 * Handles step navigation, AJAX form submission, and skip functionality.
 *
 * @package Jobus
 * @since   1.5.0
 */

(function ($) {
  "use strict";

  /**
   * Onboarding Wizard Controller
   */
  var JobusOnboarding = {
    /**
     * Current step number (1-indexed).
     */
    currentStep: 1,

    /**
     * Total number of steps.
     */
    totalSteps: 4,

    /**
     * Cookie name for storing current step.
     */
    cookieName: 'jobus_onboarding_step',

    /**
     * Initialize the wizard.
     */
    init: function () {
      this.bindEvents();
      this.restoreStep();
      this.updateProgress();
    },

    /**
     * Restore step from cookie on page load.
     */
    restoreStep: function () {
      var savedStep = this.getCookie(this.cookieName);
      if (savedStep) {
        var stepNum = parseInt(savedStep, 10);
        if (stepNum >= 1 && stepNum <= this.totalSteps) {
          this.goToStep(stepNum);
        }
      }
    },

    /**
     * Set a cookie.
     *
     * @param {string} name Cookie name.
     * @param {string} value Cookie value.
     * @param {number} days Expiry in days.
     */
    setCookie: function (name, value, days) {
      var expires = '';
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
      }
      document.cookie = name + '=' + (value || '') + expires + '; path=/';
    },

    /**
     * Get a cookie value.
     *
     * @param {string} name Cookie name.
     * @return {string|null} Cookie value or null.
     */
    getCookie: function (name) {
      var nameEQ = name + '=';
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
          c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
          return c.substring(nameEQ.length, c.length);
        }
      }
      return null;
    },

    /**
     * Delete a cookie.
     *
     * @param {string} name Cookie name.
     */
    deleteCookie: function (name) {
      document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    },

    /**
     * Bind all event handlers.
     */
    bindEvents: function () {
      var self = this;

      // Next step button.
      $(document).on("click", ".jobus-next-step", function (e) {
        e.preventDefault();
        self.goToStep(self.currentStep + 1);
      });

      // Previous step button.
      $(document).on("click", ".jobus-prev-step", function (e) {
        e.preventDefault();
        self.goToStep(self.currentStep - 1);
      });

      // Save & Continue button.
      $(document).on("click", ".jobus-save-continue", function (e) {
        e.preventDefault();
        self.saveSettings($(this));
      });

      // Skip setup link.
      $(document).on("click", ".jobus-skip-link", function (e) {
        e.preventDefault();
        self.skipOnboarding($(this));
      });

      // Progress step click (navigate to step).
      $(document).on("click", ".jobus-progress-step", function (e) {
        e.preventDefault();
        var stepNum = parseInt($(this).data("step"), 10);
        if (stepNum && stepNum !== self.currentStep) {
          self.goToStep(stepNum);
        }
      });

      // Finish setup button (just a link, no special handling needed).
    },

    /**
     * Navigate to a specific step.
     *
     * @param {number} stepNumber The step to navigate to.
     */
    goToStep: function (stepNumber) {
      // Validate step number.
      if (stepNumber < 1 || stepNumber > this.totalSteps) {
        return;
      }

      // Hide current step.
      $('.jobus-step[data-step="' + this.currentStep + '"]').removeClass(
        "active"
      );

      // Show new step.
      $('.jobus-step[data-step="' + stepNumber + '"]').addClass("active");

      // Update current step.
      this.currentStep = stepNumber;

      // Save step to cookie (expires in 1 day).
      this.setCookie(this.cookieName, stepNumber, 1);

      // Update progress indicator.
      this.updateProgress();

      // Scroll to top.
      $("html, body").animate({ scrollTop: 0 }, 300);
    },

    /**
     * Update the progress indicator.
     */
    updateProgress: function () {
      var self = this;

      $(".jobus-progress-step").each(function () {
        var stepNum = parseInt($(this).data("step"), 10);

        $(this).removeClass("active completed");

        if (stepNum === self.currentStep) {
          $(this).addClass("active");
        } else if (stepNum < self.currentStep) {
          $(this).addClass("completed");
        }
      });
    },

    /**
     * Save settings via AJAX.
     *
     * @param {jQuery} $button The button that was clicked.
     */
    saveSettings: function ($button) {
      var self = this;

      // Show loading state.
      $button.addClass("is-loading");
      $button.prop("disabled", true);
      $button.find(".btn-arrow").text("⟳");

      // Collect form data from both forms.
      var formData = {
        action: 'jobus_save_onboarding_settings',
        nonce: jobusOnboarding.saveNonce,
        enable_candidate: $('#enable_candidate').is(':checked') ? '1' : '0',
        enable_company: $('#enable_company').is(':checked') ? '1' : '0',
        color_scheme: $('input[name="color_scheme"]:checked').val() || 'scheme_default',
        // Job configuration settings
        job_submission_status: $('#job_submission_status').val() || 'publish',
        allow_guest_application: $('#allow_guest_application').is(':checked') ? '1' : '0',
        is_job_related_posts: $('#is_job_related_posts').is(':checked') ? '1' : '0'
      };

      // Send AJAX request.
      $.ajax({
        url: jobusOnboarding.ajaxUrl,
        type: "POST",
        data: formData,
        success: function (response) {
          if (response.success) {
            // Go to success step (step 4).
            self.goToStep(4);
          } else {
            alert(response.data.message || jobusOnboarding.strings.error);
          }
        },
        error: function () {
          alert(jobusOnboarding.strings.error);
        },
        complete: function () {
          // Reset button state.
          $button.removeClass("is-loading");
          $button.prop("disabled", false);
          $button.find(".btn-arrow").text("→");
        },
      });
    },

    /**
     * Skip onboarding via AJAX.
     *
     * @param {jQuery} $link The link that was clicked.
     */
    skipOnboarding: function ($link) {
      // Confirm skip.
      if (
        !confirm(
          "Are you sure you want to skip the setup wizard? You can always configure these settings later."
        )
      ) {
        return;
      }

      // Show loading state.
      $link.text(jobusOnboarding.strings.saving);

      // Send AJAX request.
      $.ajax({
        url: jobusOnboarding.ajaxUrl,
        type: "POST",
        data: {
          action: "jobus_skip_onboarding",
          nonce: jobusOnboarding.skipNonce,
        },
        success: function (response) {
          if (response.success && response.data.redirectUrl) {
            window.location.href = response.data.redirectUrl;
          } else {
            window.location.href = jobusOnboarding.dashboardUrl;
          }
        },
        error: function () {
          // Redirect anyway.
          window.location.href = jobusOnboarding.dashboardUrl;
        },
      });
    },
  };

  /**
   * Initialize on DOM ready.
   */
  $(document).ready(function () {
    JobusOnboarding.init();
  });
})(jQuery);
