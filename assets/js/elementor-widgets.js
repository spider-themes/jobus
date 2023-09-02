;(function ($, elementor) {
    "use strict";
    var $window = $(elementor);

    var jobly = {
        onInit: function () {
            var E_FRONT = elementorFrontend;
            var widgetHandlersMap = {
                "jobly_job_tabs.default": jobly.jobTabs,
            };

            $.each(widgetHandlersMap, function (widgetName, callback) {
                E_FRONT.hooks.addAction(
                    "frontend/element_ready/" + widgetName,
                    callback
                );
            });
        },

        //===================== Job Listing Tabs =====================//
        jobTabs: function ($scope) {

            let isotopeWrapper = $scope.find('.isotop-gallery-wrapper');
            let isotopeMenuWrapper = $scope.find('.isotop-menu-wrapper');

            if (isotopeWrapper.length > 0) {
                var $grid = isotopeWrapper.isotope({
                    // options
                    itemSelector: '.isotop-item',
                    percentPosition: true,
                    masonry: {
                        // use element for option
                        columnWidth: '.grid-sizer'
                    }

                });

                // filter items on button click
                isotopeMenuWrapper.on( 'click', 'li', function() {
                    var filterValue = $(this).attr('data-filter');
                    $grid.isotope({ filter: filterValue });
                });

                // change is-checked class on buttons
                isotopeMenuWrapper.each( function( i, buttonGroup ) {
                    var $buttonGroup = $( buttonGroup );
                    $buttonGroup.on( 'click', 'li', function() {
                        $buttonGroup.find('.is-checked').removeClass('is-checked');
                        $( this ).addClass('is-checked');
                    });
                });
            }


        },
        
        
        
        
    };

    $window.on("elementor/frontend/init", jobly.onInit);
})(jQuery, window);