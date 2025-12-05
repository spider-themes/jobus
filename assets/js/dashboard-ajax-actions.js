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

    const JobusDashboardAjaxActions = {
        init: function () {
            this.removeSavedPost(); // unified remove for job/candidate
            this.removeApplication();
            this.updateApplicationStatus();
        },

        /**
         * Handles removing a saved job or candidate from dashboard
         */
        removeSavedPost: function () {
            $(document).on('click', '.jobus-dashboard-remove-saved-post', function (e) {
                e.preventDefault();

                const btn = $(this);
                const postId = btn.data('post_id');
                const postType = btn.data('post_type');
                const nonce = btn.data('nonce');
                // Find the item container
                const item = btn.closest('.job-list-one, .candidate-profile-card');
                const icon = btn.find('i');

                if (!postId || !postType || !nonce || btn.hasClass('disabled')) return;

                // Store original icon class
                const originalIcon = icon.attr('class');

                // Show loading spinner
                icon.attr('class', 'spinner-border spinner-border-sm align-middle');
                btn.addClass('loading disabled');

                // Determine AJAX action and data key
                let ajaxAction = '';
                let dataKey = '';
                if (postType === 'jobus_job') {
                    ajaxAction = 'jobus_candidate_saved_job';
                    dataKey = 'job_id';
                } else if (postType === 'jobus_candidate') {
                    ajaxAction = 'jobus_employer_saved_candidate';
                    dataKey = 'post_id';
                } else {
                    btn.removeClass('loading disabled');
                    icon.attr('class', originalIcon);
                    alert('Unknown post type');
                    return;
                }

                // Prepare AJAX data
                let ajaxData = {
                    action: ajaxAction,
                    nonce: nonce
                };
                ajaxData[dataKey] = postId;

                $.ajax({
                    url: jobus_dashboard_obj.ajax_url,
                    type: 'POST',
                    data: ajaxData,
                    success: function (res) {
                        if (res.success) {
                            // Fade out and remove the item from the list
                            item.fadeOut(300, function() {
                                $(this).remove();
                                // Check if there are any items left
                                if ($('.job-list-one, .candidate-profile-card').length === 0) {
                                    $('.wrapper').html('<div class="no-jobs-found">No saved items found.</div>');
                                }
                            });
                        } else {
                            btn.removeClass('loading disabled');
                            icon.attr('class', originalIcon);
                            alert(res.data && res.data.message ? res.data.message : 'Error removing item');
                        }
                    },
                    error: function () {
                        btn.removeClass('loading disabled');
                        icon.attr('class', originalIcon);
                        alert('Error removing item. Please try again.');
                    }
                });
            });
        },

        /**
         * Handles removing a job application from candidate's dashboard
         */
        removeApplication: function () {
            $(document).on('click', '.remove-application', function (e) {
                e.preventDefault();

                const btn = $(this);
                const jobId = btn.data('job_id');
                const nonce = btn.data('nonce'); // Changed to match the data attribute from template
                const row = btn.closest('tr');
                const icon = btn.find('i');

                if (!jobId || !nonce || btn.hasClass('disabled')) return;

                // Store original icon class
                const originalIcon = icon.attr('class');

                // Show loading spinner
                icon.attr('class', 'spinner-border spinner-border-sm align-middle');
                btn.addClass('loading disabled');

                $.ajax({
                    url: jobus_dashboard_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_remove_job_application',
                        job_id: jobId,
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            row.fadeOut(300, function() {
                                $(this).remove();
                                if ($('.job-alert-table tbody tr').length === 0) {
                                    $('.job-alert-table').empty();
                                }
                            });
                        } else {
                            btn.removeClass('loading disabled');
                            icon.attr('class', originalIcon);
                        }
                    },
                    error: function() {
                        btn.removeClass('loading disabled');
                        icon.attr('class', originalIcon);
                    }
                });
            });
        },

        /**
         * Handles updating application status from employer dashboard
         */
        updateApplicationStatus: function () {
            $(document).on('click', '.jobus-update-status', function (e) {
                e.preventDefault();

                const btn = $(this);
                const applicationId = btn.data('application-id');
                const newStatus = btn.data('status');
                const row = btn.closest('tr');
                const statusBadge = row.find('.status-badge');

                if (!applicationId || !newStatus || btn.hasClass('disabled')) return;

                btn.addClass('disabled');

                $.ajax({
                    url: jobus_dashboard_obj.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'jobus_update_application_status',
                        application_id: applicationId,
                        status: newStatus,
                        nonce: jobus_dashboard_obj.nonce
                    },
                    success: function(response) {
                        btn.removeClass('disabled');
                        if (response.success) {
                            // Update the status badge
                            let statusClass = 'jbs-bg-warning';
                            if (newStatus === 'approved') {
                                statusClass = 'jbs-bg-success';
                            } else if (newStatus === 'rejected') {
                                statusClass = 'jbs-bg-danger';
                            }
                            
                            statusBadge
                                .removeClass('jbs-bg-warning jbs-bg-success jbs-bg-danger')
                                .addClass(statusClass)
                                .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                            // Show success notification if SweetAlert is available
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.data && response.data.message ? response.data.message : 'Error updating status'
                                });
                            } else {
                                alert(response.data && response.data.message ? response.data.message : 'Error updating status');
                            }
                        }
                    },
                    error: function() {
                        btn.removeClass('disabled');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error updating status. Please try again.'
                            });
                        } else {
                            alert('Error updating status. Please try again.');
                        }
                    }
                });
            });
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        JobusDashboardAjaxActions.init();
    });

})(jQuery);