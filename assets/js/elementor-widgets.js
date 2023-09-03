(function ($, elementor) {
  "use strict";
  var $window = $(elementor);

  var jobly = {
    onInit: function () {
      var E_FRONT = elementorFrontend;
      var widgetHandlersMap = {
        "jobly_job_tabs.default": jobly.jobTabs,
        "jobly_job_listing.default": jobly.joblistSlider,
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
      let sliderWrapper = $scope.find(".category-slider-one");
      // ------------------------ Category Slider
      if (sliderWrapper.length) {
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

    //===================== Job Listing Tabs =====================//
    jobTabs: function ($scope) {
      let isotopeWrapper = $scope.find(".isotop-gallery-wrapper");
      let isotopeMenuWrapper = $scope.find(".isotop-menu-wrapper");

      if (isotopeWrapper.length > 0) {
        var $grid = isotopeWrapper.isotope({
          // options
          itemSelector: ".isotop-item",
          percentPosition: true,
          masonry: {
            // use element for option
            columnWidth: ".grid-sizer",
          },
        });

        // filter items on button click
        isotopeMenuWrapper.on("click", "li", function () {
          var filterValue = $(this).attr("data-filter");
          $grid.isotope({ filter: filterValue });
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

  $window.on("elementor/frontend/init", jobly.onInit);
})(jQuery, window);
