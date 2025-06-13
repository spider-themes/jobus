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
        let niceSelect = $('.nice-select');
        if (niceSelect.length > 0) {
            niceSelect.niceSelect();
        }

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
            let dataRtlrelated = relatedJobSlider.data("rtl");
            if (relatedJobSlider.length > 0) {
                relatedJobSlider.slick({
                    rtl: dataRtlrelated,
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
            let dataRtlreview = reviewSlider.data("rtl");
            if (reviewSlider.length > 0) {
                reviewSlider.slick({
                    rtl: dataRtlreview,
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
        salaryRangeSlider(".salary-slider");

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


        //============== Candidate Portfolio Slider ================//
        function candidatePortfolio() {

            let portfolioSlider = $('.candidate-portfolio-slider');
            let dataRtlprofile = portfolioSlider.data("rtl");
            if( portfolioSlider.length ) {
                portfolioSlider.slick({
                    rtl: dataRtlprofile,
                    dots: true,
                    arrows: false,
                    lazyLoad: 'ondemand',
                    centerPadding: '0px',
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 450,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            }

        }
        candidatePortfolio()//End Candidate Portfolio Slider

        //==== Tags Filter ====//
        function tagsFilter() {
            const moreBtn = $('.more-btn');
            const filterInput = $('.jobus-tags-wrapper');

            if (moreBtn.length) {
                moreBtn.on('click', function() {
                    const $this = $(this);
                    const hiddenItems = filterInput.find('li.tag-item.hide');

                    if ($this.hasClass('showing-all')) {
                        // Hide items after initial limit
                        hiddenItems.slideUp(300);
                        $this.removeClass('showing-all')
                            .html('<i class="bi bi-plus"></i> Show More');
                    } else {
                        // Show all hidden items
                        hiddenItems.slideDown(300);
                        $this.addClass('showing-all')
                            .html('<i class="bi bi-dash"></i> Show Less');
                    }
                });
            }
        }
        tagsFilter(); // end tagsFilter



    });


})(jQuery);

