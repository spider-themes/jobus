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
        let niceSelect = $('.jbs-nice-select');
        if (niceSelect.length > 0) {
            niceSelect.jbsNiceSelect();
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

            let relatedJobSlider = $('.jbs-related-job-slider');
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

            if (moreBtn.length > 0) {
                moreBtn.on("click", function () {
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
            if (portfolioSlider.length) {
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
                moreBtn.on('click', function () {
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

        /**
         * Login Modal Handler for Save Post Button and Apply Now Button
         */
        function initLoginModal() {
            // Open login modal when clicking save button or apply now button if user is not logged in
            $(document).on('click', '.save_post_btn, .jbs-job-apply', function (e) {
                // Check if jobus_public_obj exists
                if (typeof jobus_public_obj === 'undefined') {
                    return;
                }
                
                // Check if user is logged in
                const isLoggedIn = jobus_public_obj.is_user_logged_in === true || 
                                 jobus_public_obj.is_user_logged_in === 1 || 
                                 jobus_public_obj.is_user_logged_in === '1';
                
                // Check if guest applications are allowed (only for job apply button)
                const isApplyButton = $(this).hasClass('jbs-job-apply');
                const allowGuestApplication = jobus_public_obj.allow_guest_application === true || 
                                              jobus_public_obj.allow_guest_application === 1 || 
                                              jobus_public_obj.allow_guest_application === '1';
                
                // If guest applications are allowed and this is the apply button, let the default action proceed
                if (isApplyButton && allowGuestApplication) {
                    return; // Allow the application form popup to show
                }
                
                if (!isLoggedIn) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const loginModal = document.getElementById('loginModal');
                    
                    if (loginModal) {
                        // Show the modal
                        loginModal.style.display = 'block';
                        loginModal.classList.add('show');
                        loginModal.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('modal-open');
                        
                        // Add fade-in effect
                        setTimeout(function() {
                            loginModal.style.opacity = '1';
                        }, 10);
                    }
                    
                    return false;
                }
            });

            // Close modal when clicking close button
            $(document).on('click', '#loginModal .jbs-btn-close, #loginModal [data-jbs-dismiss="modal"]', function (e) {
                e.preventDefault();
                closeLoginModal();
            });

            // Close modal when clicking outside the modal content
            $(document).on('click', '#loginModal', function (e) {
                if (e.target === this) {
                    closeLoginModal();
                }
            });
            
            // Close with ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    const loginModal = document.getElementById('loginModal');
                    if (loginModal && loginModal.classList.contains('show')) {
                        closeLoginModal();
                    }
                }
            });
        }

        /**
         * Close login modal
         */
        function closeLoginModal() {
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.style.opacity = '0';
                setTimeout(function() {
                    loginModal.style.display = 'none';
                    loginModal.classList.remove('show');
                    loginModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('modal-open');
                }, 300);
            }
        }

        // Initialize login modal handlers
        initLoginModal();

    });

})(jQuery);