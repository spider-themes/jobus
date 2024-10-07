;(function($) {
    'use strict';

    $(document).ready(function() {
        $('#candidate_email_from').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            let formData = $(this).serialize(); // Serialize form data
            let candidateId = $('#candidate_id').val(); // Get candidate ID

            // Add class to the message container on submit button click
            let messageContainer = $('#email-form-message');

            $.ajax({
                url: jobus_candidate_email_form.ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: formData + '&action=candidate_send_mail_form&security=' + jobus_candidate_email_form.nonce + '&candidate_id=' + candidateId,
                success: function(response) {
                    messageContainer.removeClass('success error'); // Clear any previous messages

                    if (response.success) {
                        messageContainer.addClass('success').text(response.data);
                        $('#candidate_email_from')[0].reset(); // Clear the form fields

                        // Remove the message after 10 seconds
                        setTimeout(function() {
                            messageContainer.removeClass('success').text('');
                        }, 10000); // 10000 milliseconds = 10 seconds
                    } else {
                        messageContainer.addClass('error').text(response.data);
                    }
                },
                error: function() {
                    messageContainer.addClass('error').text('There was an error with the AJAX request.');
                }
            });
        });
    });
})(jQuery);
