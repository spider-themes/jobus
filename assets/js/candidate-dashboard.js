;(function ($) {

    'use strict';


    $(document).ready(function () {


        /**
         * Handles the preview of the profile picture when a new image is selected.
         * Updates the image preview and sets the action for upload.
         */
        function updateProfilePicturePreview() {
            const fileInput = $('#uploadImg');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');

            // Listen for file input change
            fileInput.on('change', function () {
                const file = this.files[0];

                if (file) {
                    const reader = new FileReader();

                    // When the file is loaded, set the src of the image to the file's data URL
                    reader.onload = function (e) {
                        imgPreview.attr('src', e.target.result);
                        // Mark image as changed
                        profilePictureAction.val('upload');
                    }

                    // Read the image file as a data URL
                    reader.readAsDataURL(file);
                }
            });
        }

        /**
         * Handles the AJAX-based deletion of the candidate's profile picture.
         * Updates the UI instantly and shows feedback messages.
         */
        function handleDeleteProfilePicture() {
            const deleteButton = $('#delete_profile_picture');
            const imgPreview = $('#candidate_avatar');
            const profilePictureAction = $('#profile_picture_action');
            const fileInput = $('#uploadImg');

            // Handle delete button click with AJAX implementation
            deleteButton.on('click', function() {
                // Show loading state
                $(this).text(jobus_dashboard_params.deleting_text || 'Deleting...');

                // AJAX request to delete the profile picture
                $.ajax({
                    url: jobus_dashboard_params.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_candidate_profile_picture',
                        security: jobus_dashboard_params.security,
                    },
                    success: function(response) {
                        if(response.success) {
                            // Update the image with default avatar
                            const defaultAvatar = jobus_dashboard_params.default_avatar || '';
                            imgPreview.attr('src', defaultAvatar);

                            // Set action to empty since we've already processed it
                            profilePictureAction.val('');

                            // Reset the file input
                            fileInput.val('');

                            // Show success message
                            const successElement = $('<div class="alert alert-success" role="alert"></div>').text(response.data.message);
                            $('#candidateProfileForm').before(successElement);

                            // Remove success message after 3 seconds
                            setTimeout(function() {
                                successElement.fadeOut('slow', function() {
                                    $(this).remove();
                            });
                        }, 3000);
                        } else {
                            // Show error message
                            const errorElement = $('<div class="alert alert-danger" role="alert"></div>').text(response.data.message || 'Error deleting image');
                            $('#candidateProfileForm').before(errorElement);

                            // Remove error message after 3 seconds
                            setTimeout(function() {
                                errorElement.fadeOut('slow', function() {
                                    $(this).remove();
                            });
                        }, 3000);
                        }
                    },
                    error: function() {
                        // Show error message
                        const errorElement = $('<div class="alert alert-danger" role="alert"></div>').text('Server error. Please try again.');
                        $('#candidateProfileForm').before(errorElement);

                        // Remove error message after 3 seconds
                        setTimeout(function() {
                            errorElement.fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 3000);
                    },
                    complete: function() {
                        // Reset button text
                        deleteButton.text(jobus_dashboard_params.delete_text || 'Delete');
                    }
                });
            });
        }

        /**
         * Handles the candidate profile form submission.
         * Placeholder for additional form handling logic if needed.
         */
        function handleProfileFormSubmit() {
            // Form submission handling
            $('#candidateProfileForm').on('submit', function(e) {
                // Additional form handling logic can go here if needed
            });
        }

        // Initialize all handlers
        updateProfilePicturePreview();
        handleDeleteProfilePicture();
        handleProfileFormSubmit();

    })


})(jQuery);