;(function ($, elementor) {
    "use strict";

    const $window = $(elementor);

    const jobus = {

        onInit: function () {
            const E_FRONT = elementorFrontend;
            const widgetHandlersMap = {
                "jobus_job_tabs.default": jobus.jobTabs,
                "jobus_job_categories.default": jobus.jobCategory,
            };

            $.each(widgetHandlersMap, function (widgetName, callback) {
                E_FRONT.hooks.addAction(
                    "frontend/element_ready/" + widgetName,
                    callback
                );
            });
        },

        /*======= Job Category ========*/
        jobCategory: function ($scope) {

            let sliderWrapper = $scope.find(".category-slider-one");
            if ( sliderWrapper.length > 0 ) {
                sliderWrapper.slick({
                    dots: false,
                    arrows: true,
                    lazyLoad: "ondemand",
                    prevArrow: $(".prev_d"),
                    nextArrow: $(".next_d"),
                    centerPadding: "0px",
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 3,
                            },
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 2,
                            },
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: 1,
                            },
                        },
                    ],
                });
            }
        },

        //===================== Job Tabs =====================//
        jobTabs: function ($scope) {

            let isotopeWrapper = $scope.find("#isotop-gallery-wrapper");
            let isotopeMenuWrapper = $scope.find(".isotop-menu-wrapper");

            if ( isotopeWrapper.length > 0 ) {
                let $grid = isotopeWrapper.isotope({
                    itemSelector: ".isotop-item",
                    percentPosition: true,
                    masonry: {
                        columnWidth: ".grid-sizer",
                    },
                });

                // filter items on button click
                isotopeMenuWrapper.on("click", "li", function () {
                    let filterValue = $(this).attr("data-filter");
                    $grid.isotope({filter: filterValue});
                });

                // change is-checked class on buttons
                isotopeMenuWrapper.each(function (i, buttonGroup) {
                    var $buttonGroup = $(buttonGroup);
                    $buttonGroup.on("click", "li", function () {
                        $buttonGroup.find(".is-checked").removeClass("is-checked");
                        $(this).addClass("is-checked");
                    });
                });
            }
        },
    };

    $window.on("elementor/frontend/init", jobus.onInit);
})(jQuery, window);
