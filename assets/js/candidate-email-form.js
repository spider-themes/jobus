;(function($) {
    'use strict';

    $(document).ready(function() {

        let mailForm = $('#candidate_email_from')

        mailForm.on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            let formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: jobly_candidate_email_form.ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'candidate_send_mail_form',
                    data_type: formData,
                    security: jobly_candidate_email_form.nonce
                },

                success: function(response) {
                    let messageContainer = $('#email-form-message');
                    messageContainer.removeClass('success error'); // Clear any previous messages

                    if (response.success) {
                        messageContainer.addClass('success').text(response.data);
                    } else {
                        messageContainer.addClass('error').text(response.data);
                    }
                },

                error: function() {
                    $('#email-form-message').addClass('error').text('There was an error with the AJAX request.');
                }

            });
        });

    });
})(jQuery);