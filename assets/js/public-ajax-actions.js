/**
 * Jobus - Frontend AJAX Controller
 *
 * Handles all public-facing AJAX actions used across the Jobus plugin.
 * This script manages the following user interactions via AJAX:
 *
 * It ensures secure data transmission using nonce verification and utilizes
 * WordPress's built-in admin-ajax.php endpoint.
 *
 * @summary   Modular JS controller for handling public AJAX actions
 * @author    spider-themes
 * @since     1.0.0
 * @package   Jobus
 * @license   GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://developer.wordpress.org/plugins/javascript/ajax/
 */

;(function ($) {

    'use strict';

    const JobusAjaxActions = {
        init: function () {
            this.jobSave(); // Initialize candidate job saving feature by clicking the save button
            this.jobApplicationForm(); // Initialize job application form submission by candidates
            this.emailFormToCandidate(); // Initialize email form to candidate
        },

        /**
         * Handles saving or unsaving a job by a candidate.
         */
        jobSave: function () {
            $(document).on('click', '.jobus-candidate-saved-job', function (e) {
                e.preventDefault();

                const btn = $(this);
                const jobId = btn.data('job_id');
                const job_nonce = jobus_public_obj.save_job_nonce;
                const icon = btn.find('i');
                if (!jobId || !job_nonce || btn.hasClass('disabled')) return;

                const originalIcon = icon.attr('class');

                // Show loading spinner
                icon.attr('class', 'spinner-border spinner-border-sm align-middle');
                btn.addClass('loading disabled');

                $.ajax({
                    url: jobus_public_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_candidate_saved_job',
                        job_id: jobId,
                        nonce: job_nonce
                    },
                    success: function (res) {
                        btn.removeClass('loading disabled');

                        if (res.success) {
                            const saved = !btn.hasClass('saved');
                            btn.toggleClass('saved');
                            icon.attr('class', saved ? 'bi bi-bookmark-check-fill text-primary' : 'bi bi-bookmark-dash');
                            // Update title attribute
                            btn.attr('title', saved ? 'Saved' : 'Save Job');
                        } else {
                            icon.attr('class', originalIcon);
                        }
                    },
                    error: function () {
                        btn.removeClass('loading disabled');
                        icon.attr('class', originalIcon);
                    }
                });
            });
        },


        /**
         * Handles job application form submission.
         */
        jobApplicationForm: function () {

            const jobApplication = $('#jobApplicationForm');

            jobApplication.on('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'jobus_job_application');

                $.ajax({
                    url: jobus_public_obj.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#applicationSuccessMessage').fadeIn().delay(3000).fadeOut();
                            jobApplication[0].reset();
                        } else {
                            alert(response.data && response.data.message ? response.data.message : 'Submission failed.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        alert('Error submitting application. Please try again.');
                    }
                });
            });
        },


        /**
         * Handles email form submission to candidate.
         */
        emailFormToCandidate: function () {

            $('#candidate_email_from').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                let formData = $(this).serialize(); // Serialize form data
                let candidateId = $('#candidate_id').val(); // Get candidate ID

                // Add class to the message container on submit button click
                let messageContainer = $('#email-form-message');

                $.ajax({
                    url: jobus_public_obj.ajax_url, // WordPress AJAX URL
                    type: 'POST',
                    data: formData + '&action=candidate_send_mail_form&security=' + jobus_public_obj.nonce + '&candidate_id=' + candidateId,
                    success: function(response) {
                        messageContainer.removeClass('success error'); // Clear any previous messages

                        if (response.success) {
                            messageContainer.addClass('success').text(response.data);
                            $('#candidate_email_from')[0].reset(); // Clear the form fields

                            // Remove the message after 10 seconds
                            setTimeout(function() {
                                messageContainer.removeClass('success').text('');
                            }, 10000); // 10000 milliseconds = 10 seconds
                        } else {
                            messageContainer.addClass('error').text(response.data);
                        }
                    },
                    error: function() {
                        messageContainer.addClass('error').text('There was an error with the AJAX request.');
                    }
                });
            });
        }

    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        JobusAjaxActions.init();
    });

})(jQuery);