;(function ($) {
    'use strict';

    $(document).ready(function () {


        /**
         * Copy URL to clipboard
         * @param text
         */
        function copyToClipboard(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }


        // Nice Select for search form
        function niceSelect() {
            let niceSelect = $('.nice-select');
            if (niceSelect.length > 0) {
                niceSelect.niceSelect();
            }
        }

        niceSelect();// end Nice Select

        /**
         * Search Keywords
         */
        $('.keywords_search_form ul li a').on('click', function (event) {
            event.preventDefault();
            var content = $(this).text();

            console.log(content);

            $('#searchInput').val(content).focus();
            fetchResults();
        });


        // Related Job Post slider
        function relatedPost() {

            let relatedJobSlider = $('.related-job-slider');
            if (relatedJobSlider.length > 0) {
                relatedJobSlider.slick({
                    dots: false,
                    arrows: true,
                    lazyLoad: 'ondemand',
                    prevArrow: $('.prev_e'),
                    nextArrow: $('.next_e'),
                    centerPadding: '0px',
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [{
                        breakpoint: 992, settings: {
                            slidesToShow: 2
                        }
                    }, {
                        breakpoint: 768, settings: {
                            slidesToShow: 1
                        }
                    }]
                });
            }

        }

        relatedPost(); // end Related Job Post slider


        // Testimonial slider
        function testimonialSlider() {

            let reviewSlider = $('.company-review-slider');
            if (reviewSlider.length > 0) {
                reviewSlider.slick({
                    dots: true,
                    arrows: false,
                    lazyLoad: 'ondemand',
                    centerPadding: '0px',
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [{
                        breakpoint: 768, settings: {
                            slidesToShow: 1
                        }
                    }]
                });
            }
        }

        testimonialSlider(); // end Testimonial slider


        // Copy URL to clipboard
        function copyButton() {
            let copyBtn = document.querySelectorAll('.copy-url');
            if (copyBtn.length > 0) {
                copyBtn.addEventListener('click', function (event) {
                    event.preventDefault();

                    // Get the current page's URL
                    const currentPageURL = window.location.href;

                    // Copy the URL to the clipboard
                    copyToClipboard(currentPageURL);

                });
            }
        }

        copyButton(); // end copyButton click event

    });

})(jQuery);