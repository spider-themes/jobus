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
            this.savePost(); // save for job and candidate posts
            this.jobApplicationForm(); // Initialize job application form submission by candidates
            this.emailFormToCandidate(); // Initialize email form to candidate
        },

        /**
         * Unified handler for saving/unsaving jobs or candidates.
         */
        savePost: function () {
            $(document).on('click', '.jobus-saved-post[data-post_id][data-post_type][data-meta_key]', function (e) {
                e.preventDefault();
                const btn = $(this);
                const postId = btn.data('post_id');
                const postType = btn.data('post_type');
                const metaKey = btn.data('meta_key');
                const nonce = jobus_public_obj.save_post_nonce;
                const icon = btn.find('i');
                if (!postId || !postType || !metaKey || !nonce || btn.hasClass('disabled')) return;
                const originalIcon = icon.attr('class');
                icon.attr('class', 'jbs-spinner-border jbs-spinner-border-sm jbs-align-middle');
                btn.addClass('loading disabled');
                $.ajax({
                    url: jobus_public_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_saved_post',
                        post_id: postId,
                        post_type: postType,
                        meta_key: metaKey,
                        nonce: nonce
                    },
                    success: function (res) {
                        btn.removeClass('loading disabled');
                        if (res.success && res.data && typeof res.data.status !== 'undefined') {
                            // Debug: log action and button
                            console.log('AJAX success:', res.data.status, 'for post', postId, 'type', postType);
                            // Always update button state
                            if (res.data.status === 'added') {
                                btn.addClass('saved');
                                icon.attr('class', 'bi bi-bookmark-check-fill jbs-text-primary');
                                btn.attr('title', postType === 'jobus_job' ? 'Saved' : postType === 'jobus_candidate' ? 'Saved Candidate' : 'Saved');
                            } else if (res.data.status === 'removed') {
                                btn.removeClass('saved');
                                icon.attr('class', 'bi bi-bookmark-dash');
                                btn.attr('title', postType === 'jobus_job' ? 'Save Job' : postType === 'jobus_candidate' ? 'Save Candidate' : 'Save');
                            }
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

            $('#candidate-email-from').on('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                let formData = $(this).serialize(); // Serialize form data
                let candidateId = $('#candidate-id').val(); // Get candidate ID

                // Add class to the message container on submit button click
                let messageContainer = $('#email-form-message');

                $.ajax({
                    url: jobus_public_obj.ajax_url, // WordPress AJAX URL
                    type: 'POST',
                    data: formData + '&action=jobus_candidate_send_mail_form&security=' + jobus_public_obj.candidate_email_nonce + '&candidate_id=' + candidateId,
                    success: function(response) {
                        messageContainer.removeClass('success error'); // Clear any previous messages

                        if (response.success) {
                            // For success responses, data is a string
                            let successMessage = typeof response.data === 'string' ? response.data : 'Message sent successfully!';
                            messageContainer.addClass('success').text(successMessage);
                            $('#candidate-email-from')[0].reset(); // Clear the form fields

                            // Remove the message after 10 seconds
                            setTimeout(function() {
                                messageContainer.removeClass('success').text('');
                            }, 10000); // 10000 milliseconds = 10 seconds
                        } else {
                            // For error responses, data might be an object with 'message' property
                            let errorMessage = '';
                            if (typeof response.data === 'object' && response.data.message) {
                                errorMessage = response.data.message;
                            } else if (typeof response.data === 'string') {
                                errorMessage = response.data;
                            } else {
                                errorMessage = 'An error occurred. Please try again.';
                            }
                            messageContainer.addClass('error').text(errorMessage);
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