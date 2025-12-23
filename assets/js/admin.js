;(function($) {
    'use strict';

    $(document).ready(function() {

        let wrapperDataFieldId = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"] .csf-cloneable-item, .csf-cloneable-wrapper[data-field-id="[company_specifications]"] .csf-cloneable-item, .csf-cloneable-wrapper[data-field-id="[candidate_specifications]"] .csf-cloneable-item');

        // Disabled already exist key field [ Job, company, candidate Specifications ]
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
        
        // Meta-key automatically inserts [ Job, company, candidate Specifications ]
        var $containers = $('.csf-cloneable-wrapper[data-field-id="[job_specifications]"], .csf-cloneable-wrapper[data-field-id="[company_specifications]"], .csf-cloneable-wrapper[data-field-id="[candidate_specifications]"]');

        // Delegate keyup on meta_name to always work for newly added items
        $containers.on('keyup', '.csf-cloneable-item input[data-depend-id="meta_name"]', function () {
            var $item = $(this).closest('.csf-cloneable-item');
            var key = $(this).val().replace(/\s+/g, '-').toLowerCase();
            $item.find('input[data-depend-id="meta_key"]').val(key);
        });

        // When a meta_key input receives a value, make it readonly to prevent accidental edits
        $containers.on('change', '.csf-cloneable-item input[data-depend-id="meta_key"]', function () {
            var $this = $(this);
            if ($this.val()) {
                $this.prop('readonly', true);
            }
        });

        // After clicking add, re-index items so cloned-item-id is correct and ensure readonly is applied
        $('.csf-cloneable-add').on('click', function () {
            // We wait a tick for the clone operation (CSF may append synchronously but reindexing is safe)
            setTimeout(function () {
                $containers.each(function () {
                    var $container = $(this);
                    $container.find('.csf-cloneable-item').each(function (i) {
                        var $it = $(this);
                        var idx = i + 1;
                        $it.attr('cloned-item-id', idx);

                        // If meta_key already has a value, lock it readonly
                        var $metaKey = $it.find('input[data-depend-id="meta_key"]');
                        if ($metaKey.length && $metaKey.val()) {
                            $metaKey.prop('readonly', true);
                        }
                    });
                });
            }, 50);
        });


        // Disable last input (only in free)
        if (!$('body').hasClass('jobus-premium')) {
            $('.jobus-pro-locked ul li:last-child label input').prop('disabled', true);
        }

        // jobus pro notice
        function jobus_pro_notice() {
            $('body:not(.jobus-premium) .jobus-pro-locked').on('click', function (e) {
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