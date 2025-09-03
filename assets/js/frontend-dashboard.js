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
            // Unified logic for both employer and candidate profile image upload
            if ($('#employer-profile-form').length) {
                this.initProfilePictureUpload({
                    avatarSelector: '#employer-avatar-preview',
                    uploadBtnSelector: '#employer-profile-picture-upload',
                    hiddenIdSelector: '#employer-profile-picture',
                    deleteBtnSelector: '#employer-delete-profile-picture',
                    actionSelector: '#employer-profile-picture-temp',
                    formSelector: '#employer-profile-form'
                });
            }
            if ($('#candidate-profile-form').length) {
                this.initProfilePictureUpload({
                    avatarSelector: '#candidate_avatar',
                    uploadBtnSelector: '#candidate_profile_picture_upload',
                    hiddenIdSelector: '#candidate_profile_picture_id',
                    deleteBtnSelector: '#delete_profile_picture',
                    actionSelector: '#profile_picture_action',
                    formSelector: '#candidate-profile-form'
                });
            }
        },

        initProfilePictureUpload: function (config) {
            const $avatarPreview = $(config.avatarSelector);
            const $profilePictureAction = $(config.actionSelector);
            const $mediaBtn = $(config.uploadBtnSelector);
            const $hiddenId = $(config.hiddenIdSelector);
            const $deleteBtn = $(config.deleteBtnSelector);
            const $formWrap = $(config.formSelector);
            const originalAvatarUrl = $avatarPreview.attr('src');
            let tempImageUrl = null;
            const defaultAvatarUrl = window.jobus_frontend_dashboard_params && window.jobus_frontend_dashboard_params.default_avatar_url ?
                window.jobus_frontend_dashboard_params.default_avatar_url :
                'https://secure.gravatar.com/avatar/?s=96&d=mm&r=g';

            // Ensure the media library is functional
            if (!$mediaBtn.length || !window.wp || !window.wp.media) return;

            // Open media library on button click
            $mediaBtn.on('click', function(e) {
                e.preventDefault();
                const mediaUploader = wp.media({
                    title: 'Select Profile Picture',
                    button: { text: 'Use this image' },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    tempImageUrl = attachment.url;  // Store temporary URL
                    $avatarPreview.attr('src', tempImageUrl);  // Update preview
                    $hiddenId.val(attachment.id);  // Store the new image ID
                    $profilePictureAction.val('upload');  // Mark the action as 'upload'
                });

                mediaUploader.open();
            });

            // Delete the avatar (revert to default avatar)
            $deleteBtn.on('click', function(e) {
                e.preventDefault();
                $avatarPreview.attr('src', defaultAvatarUrl);
                $hiddenId.val('');
                $profilePictureAction.val('delete');
                tempImageUrl = null;
            });

            // Handle form reset/cancel - revert to original image
            $formWrap.on('reset', function() {
                $avatarPreview.attr('src', originalAvatarUrl);
                $hiddenId.val('');
                $profilePictureAction.val('');
                tempImageUrl = null;
            });
        }
    }

    $(document).ready(function () {

        JobusCandidateDashboard.init();
    });

})(jQuery);
