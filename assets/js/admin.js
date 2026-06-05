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

        function initSocialLogin() {
            const config = window.jobusAdminConfig && window.jobusAdminConfig.socialLogin;
            const root = $('[data-jbs-social-login-admin]');

            if (!config || !root.length) {
                return;
            }

            const selectors = config.selectors || {};
            const activeClass = selectors.isActive || 'is-active';
            const cardSelector = '[data-jbs-social-login-card]';
            const panelSelector = '[data-jbs-social-login-panel]';

            function openPanel(providerId) {
                root.find(cardSelector).removeClass(activeClass);
                root.find(panelSelector).removeClass(activeClass);

                const card = root.find(cardSelector + '[data-provider="' + providerId + '"]');
                const targetPanel = root.find('[data-jbs-social-login-panel="' + providerId + '"]');

                card.addClass(activeClass);
                targetPanel.addClass(activeClass);
            }

            root.on('click', cardSelector, function () {
                openPanel($(this).data('provider'));
            });

            root.on('click', '[data-jbs-social-login-back]', function () {
                root.find(cardSelector).removeClass(activeClass);
                root.find(panelSelector).removeClass(activeClass);
            });

            root.on('click', '.' + (selectors.copy || 'jbs-social-login-admin__copy'), function () {
                const button = $(this);
                const text = button.data('copyText');

                if (!text) {
                    return;
                }

                const onSuccess = function () {
                    button.text(config.copied || 'Copied');
                    window.setTimeout(function () {
                        button.text(config.copy || 'Copy');
                    }, 1800);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(onSuccess);
                    return;
                }

                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    document.execCommand('copy');
                    onSuccess();
                } catch (error) {
                    // Ignore copy fallback failures.
                }

                document.body.removeChild(textarea);
            });

            const defaultProvider = root.data('defaultProvider');
            if (defaultProvider) {
                openPanel(defaultProvider);
            } else {
                const firstCard = root.find(cardSelector).first();
                if (firstCard.length) {
                    openPanel(firstCard.data('provider'));
                }
            }
        }
        initSocialLogin();
        
    });

})(jQuery);
