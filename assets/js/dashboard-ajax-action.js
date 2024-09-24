;(function ($) {

    'use strict';


    $(document).ready(function () {


        //Delete Job Action
        const deleteJob = $('.recent-job-tab .job-action .delete-job');
        deleteJob.on('click', function (e){
            e.preventDefault();

            let jobId = $(this).attr('data-job-id');

            $.ajax({
                url: jobly_local.ajax_url,
                method: 'POST',
                data: {
                    action: 'delete_job_application',
                    job_id: jobId,
                    security: jobly_local.nonce
                },

                success: function (response) {

                    console.log(response); // This will help in troubleshooting the server response

                    if (response.success) {
                        $('#job-' + jobId).fadeOut(); // Remove the job from the UI
                    } else {
                        alert('hello');
                    }
                },

                error: function(xhr, status, error) {
                    console.error('Delete failed: ', error);
                }

            })


        });


    })


})(jQuery);