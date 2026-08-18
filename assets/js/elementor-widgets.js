(function ($, elementor) {
    "use strict";
    var $window = $(elementor);

    var jobus = {
        onInit: function () {
            var E_FRONT = elementorFrontend;
            var widgetHandlersMap = {
                "jobus_job_tabs.default": jobus.jobTabs,
                "jobus_job_categories.default": jobus.joblistSlider,
            };

            $.each(widgetHandlersMap, function (widgetName, callback) {
                E_FRONT.hooks.addAction(
                    "frontend/element_ready/" + widgetName,
                    callback
                );
            });
        },

        /*======= job listing slider css ========*/
        joblistSlider: function ($scope) {
            let sliderWrapper = $scope.find(".jbs-category-slider-one");
            // ------------------------ Category Slider
            if (sliderWrapper.length) {
                sliderWrapper.slick({
                    dots: false,
                    arrows: true,
                    lazyLoad: "ondemand",
                    prevArrow: $(".jbs-prev_d"),
                    nextArrow: $(".jbs-next_d"),
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

        //===================== Job Listing Tabs =====================//
        jobTabs: function ($scope) {

            let isotopeWrapper = $scope.find("#isotop-gallery-wrapper");
            let isotopeMenuWrapper = $scope.find(".jbs-isotop-menu-wrapper");

            if (isotopeWrapper.length > 0) {
                var $grid = isotopeWrapper.isotope({
                    // options
                    itemSelector: ".jbs-isotop-item",
                    percentPosition: true,
                    masonry: {
                        // use element for option
                        columnWidth: ".jbs-grid-sizer",
                    },
                });

                // filter items on button click
                isotopeMenuWrapper.on("click", "li", function () {
                    var filterValue = $(this).attr("data-filter");
                    $grid.isotope({filter: filterValue});
                });

                // change is-checked class on buttons
                isotopeMenuWrapper.each(function (i, buttonGroup) {
                    var $buttonGroup = $(buttonGroup);
                    $buttonGroup.on("click", "li", function () {
                        $buttonGroup.find(".jbs-is-checked").removeClass("jbs-is-checked");
                        $(this).addClass("jbs-is-checked");
                    });
                });
            }
        },
    };

    $window.on("elementor/frontend/init", jobus.onInit);
})(jQuery, window);
