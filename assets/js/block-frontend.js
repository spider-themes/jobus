;(function ($) {
    'use strict'

    $(document).ready(function () {

        // Video Popup with FancyBox
        var fancy = $ (".fancybox");
        if(fancy.length) {
            fancy.fancybox({
                arrows: true,
                buttons: [
                    "zoom",
                    //"share",
                    "slideShow",
                    //"fullScreen",
                    //"download",
                    "thumbs",
                    "close"
                ],
                animationEffect: "zoom-in-out",
                transitionEffect: "zoom-in-out",
            });
        }

        // Start Company Details page testimonials slider
        function companyTestimonialsSlider() {
            let testimonialSlider = $('.company-review-slider');

            if (testimonialSlider.length > 0 && $.fn.slick) {
                testimonialSlider.slick({
                    dots: true,
                    arrows: false,
                    lazyLoad: 'ondemand',
                    centerPadding: '0px',
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            }
        }

        companyTestimonialsSlider(); // End Company Details page testimonials slider

    });
})(jQuery);