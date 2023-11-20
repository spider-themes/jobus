;(function($) {
    'use strict';

    $(document).ready(function() {

        // Disabled already exist key field [ Job Specifications ]
        $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"] .csf-cloneable-item').each(function() {
            var metaKey = $(this).find('input[data-depend-id="meta_key"]').val();
           
            if (metaKey) {
                $(this).find('input[data-depend-id="meta_key"]').attr('readonly', true);
            }

        });
        // ended
    });

})(jQuery);