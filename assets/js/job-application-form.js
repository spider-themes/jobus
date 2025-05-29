;(function ($) {
    'use strict';

    $(document).ready(function () {
        function jobApplicationForm() {
            const jobApplication = $('#jobApplicationForm');

            jobApplication.on('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);
                formData.append('action', 'jobus_job_application');

                $.ajax({
                    url: jobus_job_application_obj.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#applicationSuccessMessage').fadeIn().delay(3000).fadeOut();
                            jobApplication[0].reset();
                        } else {
                            alert(response.data && response.data.message ? response.data.message : 'Submission failed.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                        alert('Error submitting application. Please try again.');
                    }
                });
            });
        }

        jobApplicationForm();
    });
})(jQuery);