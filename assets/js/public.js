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


        //============== Post View Switcher ==============//
        function postViewSwitcher() {

            let viewChangerBtn = $('.style-changer-btn');
            if(viewChangerBtn.length > 0 ) {

                $(".list-btn").on("click", function() {
                    $(this).removeClass("active");
                    $(".grid-btn").addClass("active");
                    $(".grid-style").removeClass("show");
                    $(".list-style").addClass("show");
                });

                $(".grid-btn").on("click", function() {
                    $(this).removeClass("active");
                    $(".list-btn").addClass("active");
                    $(".grid-style").addClass("show");
                    $(".list-style").removeClass("show");
                });
            }

        }

        postViewSwitcher(); // end Post View Switcher

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


        // Range Slider for Salary filter
        function salaryRangeSlider(selector) {
            const sliderElements = document.querySelectorAll(selector);
        
            sliderElements.forEach((sliderElement) => {
                const rangeInput = sliderElement.querySelectorAll(".range-input input"),
                    priceInput = sliderElement.querySelectorAll(".price-input input"),
                    range = sliderElement.querySelector(".slider .progress");
                let priceGap = 1;
        
                priceInput.forEach((input) => {
                    input.addEventListener("input", (e) => {
                        let minPrice = parseInt(priceInput[0].value),
                            maxPrice = parseInt(priceInput[1].value);
        
                        if (maxPrice - minPrice >= priceGap && maxPrice <= rangeInput[1].max) {
                            if (e.target.className === "input-min") {
                                rangeInput[0].value = minPrice;
                                range.style.left = (minPrice / rangeInput[0].max) * 100 + "%";
                            } else {
                                rangeInput[1].value = maxPrice;
                                range.style.right = 100 - (maxPrice / rangeInput[1].max) * 100 + "%";
                            }
                        }
                    });
                });
        
                rangeInput.forEach((input) => {
                    input.addEventListener("input", (e) => {
                        let minVal = parseInt(rangeInput[0].value),
                            maxVal = parseInt(rangeInput[1].value);
        
                        if (maxVal - minVal < priceGap) {
                            if (e.target.className === "range-min") {
                                rangeInput[0].value = maxVal - priceGap;
                            } else {
                                rangeInput[1].value = minVal + priceGap;
                            }
                        } else {
                            priceInput[0].value = minVal;
                            priceInput[1].value = maxVal;
                            range.style.left = (minVal / rangeInput[0].max) * 100 + "%";
                            range.style.right = 100 - (maxVal / rangeInput[1].max) * 100 + "%";
                        }
                    });
                });
            });
        }
        
        // Use the function with your specific class or attribute
        salaryRangeSlider(".salary-slider");
         // end Range Slider for Salary filter


        // Job Category Show More Items
        function jobCategoryShowMoreItems() {

            let moreBtn = $(".more-btn");

            if(moreBtn.length > 0) {
                moreBtn.on("click", function() {
                    let showMore = $(this).siblings('ul').toggleClass("show");

                    if (showMore.hasClass('show')) {
                        $(this).html('<i class="bi bi-dash"></i> Show Less');
                    } else {
                        $(this).html('<i class="bi bi-plus"></i> Show More');
                    }
                });
            }
        }

        jobCategoryShowMoreItems(); // end jobCategoryShowMoreItems




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