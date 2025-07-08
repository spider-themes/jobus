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
            this.updateProfilePicturePreview();
            this.handleDeleteProfilePicture();
        },

        /**
         * Handles the AJAX-based deletion of the candidate's profile picture.
         * Updates the UI instantly and shows feedback messages.
         */
        handleDeleteProfilePicture: function () {
            const deleteButton = $('#delete_profile_picture');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const hiddenId = $('#candidate_profile_picture_id');
            const defaultAvatar = jobus_dashboard_params.default_avatar || '';

            deleteButton.on('click', function() {
                imgPreview.attr('src', defaultAvatar);
                hiddenId.val('');
                profilePictureAction.val('delete');
            });
        },


        /**
         * Handles the preview of the profile picture when a new image is selected.
         * Updates the image preview and sets the action for upload.
         */
        updateProfilePicturePreview: function () {
            // Use media library, not file input
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const mediaBtn = $('#open_media_library');
            const hiddenId = $('#candidate_profile_picture_id');

            if (!mediaBtn.length) return;

            mediaBtn.on('click', function(e) {
                e.preventDefault();
                if (!window.wp || !window.wp.media) return;
                let mediaUploader = wp.media({
                    title: 'Select Profile Picture',
                    button: { text: 'Use this image' },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    imgPreview.attr('src', attachment.url);
                    hiddenId.val(attachment.id);
                    profilePictureAction.val('upload');
                });
                mediaUploader.open();
            });
        },

    };

    // Initialize when DOM is ready
    $(document).ready(function () {
        JobusAjaxActions.init();
    });

})(jQuery);