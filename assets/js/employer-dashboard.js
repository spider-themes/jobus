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

    const JobusCandidateDashboard = {

        init: function () {
            

        },



    };

    $(document).ready(function () {
        JobusEmployerDashboard.init();
    });

})(jQuery);


document.addEventListener('DOMContentLoaded', function() {

    /**
     * Handles job delete action from employer dashboard
     */
    function jobusDeleteJobHandler() {
        document.querySelectorAll('.jobus-delete-job').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const deleteUrl = btn.getAttribute('data-delete-url');
                if (confirm('Are you sure you want to delete this job?')) {
                    window.location.href = deleteUrl;
                }
            });
        });
    }
    // Initialize handlers
    jobusDeleteJobHandler();
});
