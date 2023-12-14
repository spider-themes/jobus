;(function($) {
    'use strict';

    $(document).ready(function() {

        let wrapperDataFieldId = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"] .csf-cloneable-item, .csf-cloneable-wrapper[data-field-id="[company_specifications]"] .csf-cloneable-item');

        // Disabled already exist key field [ Job Specifications ]
        $(wrapperDataFieldId).each(function() {
            var metaKey     = $(this).find('input[data-depend-id="meta_key"]').val();
            var $container  = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"], .csf-cloneable-wrapper[data-field-id="[company_specifications]"]');

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
            var $container  = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"], .csf-cloneable-wrapper[data-field-id="[company_specifications]"]');
            var $lastItem   = $container.find('.csf-cloneable-item').last();
            
            // Index is zero-based, so we add 1 for the next index
            var newIndex    = $lastItem.index() + 1; 

            // Index is used as attribute
            $lastItem.attr('cloned-item-id', newIndex);
            
            $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_name"]').keyup(function(){
                $container.find('.csf-cloneable-item[cloned-item-id='+newIndex+'] input[data-depend-id="meta_key"]').val($(this).val().replace(/\s+/g, '-').toLowerCase());
            });
            
        });
        
    });

})(jQuery);