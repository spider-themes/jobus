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
            // Unified logic for both company and candidate profile image upload
            if ($('#company-profile-form').length) {
                this.initProfilePictureUpload({
                    avatarSelector: '#company-avatar-preview',
                    uploadBtnSelector: '#company-profile-picture-upload',
                    hiddenIdSelector: '#company-profile-picture',
                    deleteBtnSelector: '#company-delete-profile-picture',
                    actionSelector: '#company-profile-picture-temp',
                    formSelector: '#company-profile-form'
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

            this.SocialIcon();
            this.VideoBgImage();
            this.UserPassword();
            this.checkPasswordRedirect();
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
        },

        /**
         * Manages the dynamic addition and removal of social media links in the candidate dashboard form.
         * Handles UI updates and re-indexing of social link items.
         *
         * @function SocialIcon
         * @returns {void}
         */
        SocialIcon: function () {
            const icons = [
                { value: 'bi bi-facebook', label: 'Facebook' },
                { value: 'bi bi-instagram', label: 'Instagram' },
                { value: 'bi bi-twitter', label: 'Twitter' },
                { value: 'bi bi-linkedin', label: 'LinkedIn' },
                { value: 'bi bi-github', label: 'GitHub' },
                { value: 'bi bi-youtube', label: 'YouTube' },
                { value: 'bi bi-dribbble', label: 'Dribbble' },
                { value: 'bi bi-behance', label: 'Behance' },
                { value: 'bi bi-pinterest', label: 'Pinterest' },
                { value: 'bi bi-tiktok', label: 'TikTok' }
            ];

            const iconOptions = icons.map(icon =>
                `<option value="${icon.value}">${icon.label}</option>`
            ).join('');

            const repeater = $('#social-links-repeater');
            let index = repeater.children('.social-link-item').length;

            $('#add-social-link').on('click', function (e) {
                e.preventDefault();

                const accordionId = `social-link-${index}`;
                const newItem = $(`
                    <div class="accordion-item social-link-item">
                        <div class="accordion-header" id="heading-${index}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#${accordionId}"
                                    aria-expanded="false"
                                    aria-controls="${accordionId}">
                                Social Network #${index + 1}
                            </button>
                        </div>
                        <div id="${accordionId}" class="accordion-collapse collapse"
                             aria-labelledby="heading-${index}"
                             data-bs-parent="#social-links-repeater">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_${index}_icon">Icon</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <select name="social_icons[${index}][icon]" id="social_${index}_icon" class="nice-select">
                                                ${iconOptions}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-10">
                                            <label for="social_${index}_url">URL</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-10">
                                            <input type="text" name="social_icons[${index}][url]" id="social_${index}_url" class="form-control" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-social-link mt-2 mb-2" title="Remove Item">
                                        <i class="bi bi-x"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                repeater.append(newItem);
                index++;
            });

            repeater.on('click', '.remove-social-link', function () {
                $(this).closest('.social-link-item').remove();

                // Re-index all fields
                repeater.children('.social-link-item').each(function (i, el) {
                    const accordionId = `social-link-${i}`;
                    const $el = $(el);

                    $el.find('.accordion-header').attr('id', `heading-${i}`);
                    $el.find('.accordion-button')
                        .attr('data-bs-target', `#${accordionId}`)
                        .attr('aria-controls', accordionId)
                        .text(`Social Network #${i + 1}`);

                    $el.find('.accordion-collapse')
                        .attr('id', accordionId)
                        .attr('aria-labelledby', `heading-${i}`);

                    $el.find('select').attr('name', `social_icons[${i}][icon]`).attr('id', `social_${i}_icon`);
                    $el.find('input[name$="[title]"]').attr('name', `social_icons[${i}][title]`).attr('id', `social_${i}_title`);
                    $el.find('input[name$="[url]"]').attr('name', `social_icons[${i}][url]`).attr('id', `social_${i}_url`);
                });

                index = repeater.children('.social-link-item').length;
            });
        },

        /**
         * Handles background image selection using the WordPress media uploader.
         * Allows users to select an image from the media library and set it as background.
         *
         * @function VideoBgImage
         * @returns {void}
         */
        VideoBgImage: function () {
            const uploadBtn = $('#video-bg-img-upload-btn');
            const preview = $('#bg-img-preview');
            const previewFilename = $('#video-bg-image-uploaded-filename');
            const uploadBtnWrapper = $('#bg-img-upload-btn-wrapper');
            const imgIdInput = $('#video-bg-img');
            const actionInput = $('#video-bg-img-action');
            const removeBtn = $('#remove-uploaded-bg-img');

            if (!window.wp || !window.wp.media) return;

            // Handle remove button click
            removeBtn.on('click', function(e) {
                e.preventDefault();
                actionInput.val('delete');
                imgIdInput.val('');
                previewFilename.text('');
                preview.addClass('hidden');
                uploadBtnWrapper.removeClass('hidden');
            });

            // Handle WordPress media uploader
            uploadBtn.on('click', function(e) {
                e.preventDefault();
                const mediaUploader = wp.media({
                    title: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.bg_upload_title || 'Select Background Image',
                    button: {
                        text: jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.bg_select_text || 'Use this image'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    actionInput.val('upload');
                    imgIdInput.val(attachment.id);

                    // Display the full image URL, fallback to filename if URL is invalid
                    let displayText = attachment.url || attachment.filename || 'Selected Image';

                    // Ensure we have a proper URL format
                    if (attachment.url && attachment.url.indexOf('http') === 0) {
                        displayText = attachment.url;
                    } else if (attachment.filename) {
                        displayText = attachment.filename;
                    } else {
                        displayText = 'Image ID: ' + attachment.id;
                    }

                    previewFilename.text(displayText);
                    preview.removeClass('hidden');
                    uploadBtnWrapper.addClass('hidden');
                });
                mediaUploader.open();
            });
        },


        /**
         * Handles user password management in the dashboard (candidate & employer).
         * Provides functionality for checking password strength, matching new passwords,
         * showing/hiding password fields, and updating the UI accordingly.
         * @function UserPassword
         */
        UserPassword:function () {
            const $form = $('#user-password-form');
            const $currentPassword = $('#current-password');
            const $newPassword = $('#new-password');
            const $confirmPassword = $('#confirm-password');
            const $passwordStrength = $('#password-strength');
            const $passwordMatchStatus = $('#password-match-status');

            // Check password strength
            function checkPasswordStrength() {
                const password = $newPassword.val().trim();
                if (!password) {
                    $passwordStrength.removeClass('text-success text-warning text-danger').empty();
                    return;
                }

                // Simple password strength indicator
                let strength = 0;
                if (password.length >= 8) strength += 1;
                if (/[A-Z]/.test(password)) strength += 1;
                if (/[a-z]/.test(password)) strength += 1;
                if (/[0-9]/.test(password)) strength += 1;
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;

                // Display strength indicator
                if (strength < 3) {
                    $passwordStrength.removeClass('text-success text-warning').addClass('text-danger')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_weak || 'Weak password');
                } else if (strength < 5) {
                    $passwordStrength.removeClass('text-success text-danger').addClass('text-warning')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_medium || 'Medium strength password');
                } else {
                    $passwordStrength.removeClass('text-warning text-danger').addClass('text-success')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.password_strong || 'Strong password');
                }
            }

            // Check if passwords match
            function checkPasswordsMatch() {
                const newPass = $newPassword.val().trim();
                const confirmPass = $confirmPassword.val().trim();

                if (!confirmPass) {
                    $passwordMatchStatus.removeClass('text-success text-danger').empty();
                    return;
                }

                if (newPass === confirmPass) {
                    $passwordMatchStatus.removeClass('text-danger').addClass('text-success')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.passwords_match || 'Passwords match');
                } else {
                    $passwordMatchStatus.removeClass('text-success').addClass('text-danger')
                        .text(jobus_dashboard_params && jobus_dashboard_params.texts && jobus_dashboard_params.texts.passwords_mismatch || 'Passwords do not match');
                }
            }

            // Show/hide password toggle
            $form.find('.passVicon').on('click', function() {
                $(this).toggleClass("eye-slash");
                const $input = $(this).closest('.dash-input-wrapper').find('input');
                const type = $input.attr('type') === 'password' ? 'text' : 'password';
                $input.attr('type', type);
            });

            // Attach input event listeners
            $currentPassword.on('input', checkPasswordsMatch);
            $newPassword.on('input', function() {
                checkPasswordStrength();
                checkPasswordsMatch();
            });
            $confirmPassword.on('input', function() {
                checkPasswordsMatch();
            });
        },


        /**
         * Checks for password change success and triggers redirect if needed
         */
        checkPasswordRedirect: function() {

            let $passwordChange = $('#password-change-success');
            if ( $passwordChange.length ) {
                const $redirectUrl = $passwordChange.data('redirect-url');
                if ($redirectUrl) {
                    setTimeout(function() {
                        window.location.href = $redirectUrl;
                    }, 1000);
                }
            }
        },

    }

    $(document).ready(function () {

        JobusCandidateDashboard.init();
    });

})(jQuery);
