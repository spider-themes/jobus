;(function($) {
    'use strict';

    $(document).ready(function() {

        let wrapperDataFieldId = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"] .csf-cloneable-item, .csf-cloneable-wrapper[data-field-id="[company_specifications]"] .csf-cloneable-item, .csf-cloneable-wrapper[data-field-id="[candidate_specifications]"] .csf-cloneable-item');

        // Disabled already exist key field [ Job Specifications ]
        $(wrapperDataFieldId).each(function() {
            var metaKey     = $(this).find('input[data-depend-id="meta_key"]').val();
            var $container  = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"], .csf-cloneable-wrapper[data-field-id="[company_specifications]"], .csf-cloneable-wrapper[data-field-id="[candidate_specifications]"]');

            // add attr each item 
            var newIndex    = $(this).index() + 1; 
            $(this).attr('cloned-item-id', newIndex);
           
            if (metaKey) {
                $(this).find('input[data-depend-id="meta_key"]').attr('readonly', true);
            } else {
                $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_name"]').keyup(function(){
                    $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_key"]').val($(this).val().replace(/\s+/g, '-').toLowerCase());
                });
            }

        });
        
        // Meta-key automatically inserts [ Job Specifications ]
        $('.csf-cloneable-add').on('click', function () {
            var $container  = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"], .csf-cloneable-wrapper[data-field-id="[company_specifications]"], .csf-cloneable-wrapper[data-field-id="[candidate_specifications]"]');
            var $lastItem   = $container.find('.csf-cloneable-item').last();
            
            // Index is zero-based, so we add 1 for the next index
            var newIndex    = $lastItem.index() + 1; 

            // Index is used as attribute
            $lastItem.attr('cloned-item-id', newIndex);
            
            $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_name"]').keyup(function(){
                $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_key"]').val($(this).val().replace(/\s+/g, '-').toLowerCase());
            });
            
        });


        // Disable last input (only in free)
        if (!$('body').hasClass('jobus-premium')) {
            $('.jobus-pro-notice ul li:last-child label input').prop('disabled', true);
        }

        // jobus pro notice
        function jobus_pro_notice() {
            $('body:not(.jobus-premium) .jobus-pro-notice').on('click', function (e) {
                if ($(this).hasClass('active-theme-jobi')) {
                    return; // skip alert if unlocked
                }
                e.preventDefault();
                Swal.fire({
                    title: 'Opps...',
                    html: 'This is a PRO feature. You need to <a href="admin.php?page=jobus-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
                    icon: "warning",
                    buttons: [false, "Close"],
                    dangerMode: true
                })
            });
        }
        jobus_pro_notice();
        
    });

})(jQuery);