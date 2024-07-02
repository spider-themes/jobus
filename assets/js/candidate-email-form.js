(function($) {
    'use strict';

    $(document).ready(function() {
        $('#candidate_email_from').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data
            console.log("Form Data: ", formData); // Debugging: Log form data

            $.ajax({
                url: jobly_candidate_email_form.ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: formData + '&action=candidate_send_mail_form&security=' + jobly_candidate_email_form.nonce, // Combine form data with additional fields
                success: function(response) {
                    var messageContainer = $('#email-form-message');
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