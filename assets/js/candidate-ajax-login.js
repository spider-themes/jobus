;(function ($) {
    "use strict";

    $(document).ready(function() {
        $('#contact-form').submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            var senderName = $('#sender_name').val();
            var senderEmail = $('#sender_email').val();
            var senderSubject = $('#sender_subject').val();
            var message = $('#message').val();

            $.ajax({
                // url: '<?php echo admin_url('admin-ajax.php'); ?>', // Server-side script location
                type: 'POST',
                data: {
                    action: 'jobly_contact_form', // Custom action for your form
                    sender_name: senderName,
                    sender_email: senderEmail,
                    sender_subject: senderSubject,
                    message: message,
                    // Add a nonce here for security (explained below)
                },
                beforeSend: function() {
                    $('#contact-form').addClass('loading'); // Add a loading indicator (optional)
                },
                success: function(response) {
                    $('#contact-form').removeClass('loading'); // Remove loading indicator
                    $('#form-message').html(response.message); // Display success/error message
                    $('#contact-form').trigger('reset'); // Clear the form (optional)
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#contact-form').removeClass('loading'); // Remove loading indicator
                    $('#form-message').html('An error occurred. Please try again later.');
                    console.error('AJAX Error:', textStatus, errorThrown);
                }
            });
        });
    });
})(jQuery);
