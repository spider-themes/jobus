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
            this.jobSave(); // Initialize candidate job saving feature
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
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        JobusAjaxActions.init();
    });

})(jQuery);