;(function ($) {

    'use strict'

    $(document).ready(function () {

        const ANIM_DURATION = 300;

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

        $(document).on("click", '[data-jbs-toggle="tab"]', function (e) {
            e.preventDefault();

            const $this = $(this);
            const target = $this.data("jbs-target");
            const $targetPane = $(target);

            if ($this.hasClass("active")) return;

            
            function switchTab($trigger, $targetPane, fade) {
                const $nav = $trigger.closest(".jbs-nav");
                const $tabContainer = $targetPane.closest(".jbs-tab-content");
                const $allPanes = $tabContainer.find(".jbs-tab-pane");
                const $activePanes = $allPanes.filter(".active");

                $nav.find(".jbs-nav-link").removeClass("active");
                $trigger.addClass("active");

                if (fade && $targetPane.hasClass("jbs-fade")) {
                    $activePanes.removeClass("jbs-show");
                    setTimeout(function () {
                        $activePanes.removeClass("active");
                        $targetPane.addClass("active");
                        setTimeout(() => $targetPane.addClass("jbs-show"), 10);
                    }, ANIM_DURATION);
                } else {
                    $allPanes.removeClass("jbs-show active");
                    $targetPane.addClass("jbs-show active");
                }
            };

            
            switchTab($this, $targetPane, true);
        });

    });


})(jQuery);