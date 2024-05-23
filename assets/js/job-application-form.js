;(function ($) {
    'use strict';

    $(document).ready(function () {
        function jobApplicationForm() {

            let jobApplication = $('#jobApplicationForm');

            jobApplication.submit(function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                formData.append('action', 'jobly_job_application');
                formData.append('security', job_application_form.nonce);

                $.ajax({
                    url: job_application_form.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#jobApplicationForm').trigger('reset');
                            $('#applicationSuccessMessage').fadeIn().delay(3000).fadeOut();
                        } else {
                            alert(response.data.message);
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