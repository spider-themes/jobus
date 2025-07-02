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
            this.removeSavedJob();
        },

        /**
         * Handles removing a saved job from candidate's dashboard
         */
        removeSavedJob: function () {
            $(document).on('click', '.jobus-candidate-remove-saved-job', function (e) {
                e.preventDefault();

                const btn = $(this);
                const jobId = btn.data('job_id');
                const nonce = btn.data('nonce');
                const jobItem = btn.closest('.job-list-one');
                const icon = btn.find('i'); // Get the icon element

                if (!jobId || !nonce || btn.hasClass('disabled')) return;

                // Store original icon class
                const originalIcon = icon.attr('class');

                // Show loading spinner
                icon.attr('class', 'spinner-border spinner-border-sm align-middle');
                btn.addClass('loading disabled');

                $.ajax({
                    url: jobus_candidate_dashboard_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_candidate_saved_job',
                        job_id: jobId,
                        nonce: nonce
                    },
                    success: function (res) {
                        if (res.success) {
                            // Fade out and remove the job item from the list
                            jobItem.fadeOut(300, function() {
                                $(this).remove();

                                // Check if there are any jobs left
                                if ($('.job-list-one').length === 0) {
                                    $('.wrapper').html('<div class="no-jobs-found">No saved jobs found.</div>');
                                }
                            });
                        } else {
                            // Restore original icon and state if there's an error
                            btn.removeClass('loading disabled');
                            icon.attr('class', originalIcon);
                            alert(res.data.message || 'Error removing job');
                        }
                    },
                    error: function () {
                        // Restore original icon and state on error
                        btn.removeClass('loading disabled');
                        icon.attr('class', originalIcon);
                        alert('Error removing job. Please try again.');
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