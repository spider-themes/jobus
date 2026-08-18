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
        _searchTimer: null,

        init: function () {
            this.removeSavedPost();
            this.removeApplication();
            this.updateApplicationStatus();
            this.statusSelector();
            this.searchSavedCandidates();
        },

        /**
         * Status selector dropdown on the Application Details page.
         *
         * Each option is a `.jbs-update-status` button so the existing AJAX
         * handler picks the click up — we only manage open/close state here,
         * plus sync the trigger label/swatch after a successful save so the
         * picker reflects the new "current" status without a page reload.
         */
        statusSelector: function () {
            const $doc = $(document);
            const triggerSelector = '.jbs-app-status-select-trigger';
            const menuSelector    = '.jbs-app-status-select-menu';
            const rootSelector    = '.jbs-app-status-select';

            const closeAllSelectors = function () {
                $(triggerSelector + '[aria-expanded="true"]').attr('aria-expanded', 'false');
                $(menuSelector + '.is-open').removeClass('jbs-is-open');
            };

            $doc.on('click', triggerSelector, function (e) {
                e.preventDefault();
                e.stopPropagation();
                const $trigger = $(this);
                const wasOpen = $trigger.attr('aria-expanded') === 'true';
                closeAllSelectors();
                if (!wasOpen) {
                    $trigger.attr('aria-expanded', 'true');
                    $trigger.siblings(menuSelector).addClass('jbs-is-open');
                }
            });

            $doc.on('click', function (e) {
                if (!$(e.target).closest(rootSelector).length) closeAllSelectors();
            });

            $doc.on('keydown', function (e) {
                if (e.key === 'Escape') closeAllSelectors();
            });
        },

        /**
         * AJAX search within saved candidates — debounced, filters without page reload.
         * Efficient toggle: input is always present; search icon clears on X click.
         */
        searchSavedCandidates: function () {
            const self = this;
            const $form = $('#jbs-saved-candidate-search-form');
            if (!$form.length) return;

            const $input = $('#jbs-saved-candidate-search-input');
            const $list = $('#jbs-saved-candidates-list');
            const $pagination = $('#jbs-saved-candidates-pagination');
            const $loading = $form.find('.jbs-search-loading');
            const $searchIcon = $form.find('.jbs-search-toggle-btn i');
            const nonce = $form.data('nonce');

            // Cache original content to restore when search is cleared
            const originalListHtml = $list.html();
            const paginationWasVisible = $pagination.length > 0;

            $form.on('submit', function (e) { e.preventDefault(); });

            $input.on('input', function () {
                const term = $(this).val().trim();

                // Toggle icon: X when text present, magnifier when empty
                if (term.length > 0) {
                    $searchIcon.removeClass('bi-search').addClass('bi-x-lg');
                } else {
                    $searchIcon.removeClass('bi-x-lg').addClass('bi-search');
                }

                clearTimeout(self._searchTimer);
                self._searchTimer = setTimeout(function () {
                    if (term.length === 0) {
                        // Restore original state instantly from cache
                        $list.html(originalListHtml);
                        if (paginationWasVisible) $pagination.show();
                        return;
                    }
                    self._doSearchSavedCandidates(term, nonce, $list, $pagination, $loading);
                }, 300);
            });

            // Clicking X icon clears the search
            $form.find('.jbs-search-toggle-btn').on('click', function () {
                if ($searchIcon.hasClass('bi-x-lg')) {
                    $input.val('').trigger('input').trigger('focus');
                }
            });
        },

        _doSearchSavedCandidates: function (term, nonce, $list, $pagination, $loading) {
            $loading.removeClass('jbs-d-none');

            $.ajax({
                url: jobus_dashboard_obj.ajax_url,
                type: 'POST',
                data: {
                    action: 'jobus_search_saved_candidates',
                    nonce: nonce,
                    search: term
                },
                success: function (res) {
                    $loading.addClass('jbs-d-none');
                    if (!res.success) return;

                    $list.html(res.data.html || '<div class="jbs-text-center jbs-p-4 jbs-text-muted"><p>No candidates found.</p></div>');
                    $pagination.hide();
                },
                error: function () {
                    $loading.addClass('jbs-d-none');
                }
            });
        },

        /**
         * Handles removing a saved job or candidate from dashboard
         */
        removeSavedPost: function () {
            $(document).on('click', '.jbs-dashboard-remove-saved-post', function (e) {
                e.preventDefault();

                const btn = $(this);
                const postId = btn.data('post_id');
                const postType = btn.data('post_type');
                const nonce = btn.data('nonce');
                // Find the item container
                const item = btn.closest('.jbs-job-list-one, .jbs-candidate-profile-card');
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
                            item.fadeOut(300, function() {
                                $(this).remove();
                                const $list = $('#jbs-saved-candidates-list');
                                const remaining = $list.find('.jbs-candidate-profile-card').length
                                    + $('.jbs-job-list-one').length;
                                if (remaining === 0) {
                                    const archiveUrl = (typeof jobus_dashboard_obj !== 'undefined' && jobus_dashboard_obj.candidate_archive_url)
                                        ? jobus_dashboard_obj.candidate_archive_url : '#';
                                    $('.jbs-wrapper').html(
                                        '<div id="jbs-saved-candidates-list" class="jbs-empty-state-wrap">' +
                                        '<div class="jbs-empty-state">' +
                                        '<span class="jbs-empty-icon"><i class="bi bi-people"></i></span>' +
                                        '<h4>No saved candidates yet</h4>' +
                                        '<p>Start building your talent pool by browsing candidates and saving the ones that match your needs.</p>' +
                                        '<a href="' + archiveUrl + '" class="jbs-btn jbs-btn-primary jbs-browse-candidates-btn" target="_blank"><i class="bi bi-people"></i> Browse Candidates</a>' +
                                        '</div></div>'
                                    );
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
                                if ($('.jbs-job-alert-table tbody tr').length === 0) {
                                    $('.jbs-job-alert-table').empty();
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
            $(document).on('click', '.jbs-update-status', function (e) {
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
                            // Server is the source of truth for badge class + label.
                            // Fall back to a sane default so older payloads still work.
                            const data = response.data || {};
                            const allClasses = data.all_badge_class || 'jbs-bg-warning jbs-bg-info jbs-bg-success jbs-bg-danger';
                            const newClass = data.badge_class || 'jbs-bg-warning';
                            const newLabel = data.status_label || (newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                            // Update every status badge tied to this application (table row + detail page).
                            $('[data-application-id="' + applicationId + '"]')
                                .closest('tr, .jbs-application-details')
                                .find('.status-badge')
                                .removeClass(allClasses)
                                .addClass(newClass)
                                .text(newLabel);

                            // Sync the Application Details status selector: update trigger
                            // swatch + label, close the menu, and re-hide the now-current option.
                            const $selector = $('.jbs-app-status-select[data-application-id="' + applicationId + '"]');
                            if ($selector.length) {
                                const $trigger = $selector.find('.jbs-app-status-select-trigger');
                                $trigger.find('.jbs-app-status-select-label').text(newLabel);
                                $trigger.find('.jbs-app-status-swatch')
                                    .attr('class', 'jbs-app-status-swatch jbs-app-status-swatch--' + newStatus);
                                $trigger.attr('aria-expanded', 'false');
                                $selector.find('.jbs-app-status-select-menu').removeClass('jbs-is-open');
                                $selector.find('.jbs-app-status-select-option').show()
                                    .filter('[data-status="' + newStatus + '"]').hide();
                            }

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