;(function ($) {
    'use strict';

    $(document).ready(function () {
        function jobApplicationForm() {
            let jobApplication = $('#jobApplicationForm');

            jobApplication.submit(function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                formData.append('action', 'handle_job_application');
                formData.append('security', job_application_form.nonce);
                formData.append('job_id', job_application_form.job_id);

                $.ajax({
                    url: job_application_form.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            alert(response.data.message);
                            $('#applyJobModal').modal('hide');
                            jobApplication[0].reset();
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
