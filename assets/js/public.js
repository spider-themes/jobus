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
                    
                    const loginModal = document.getElementById('jobusLoginModal');
                    
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
            $(document).on('click', '#jobusLoginModal .jbs-btn-close, #jobusLoginModal [data-jbs-dismiss="modal"]', function (e) {
                e.preventDefault();
                closeLoginModal();
            });

            // Close modal when clicking outside the modal content
            $(document).on('click', '#jobusLoginModal', function (e) {
                if (e.target === this) {
                    closeLoginModal();
                }
            });
            
            // Close with ESC key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    const loginModal = document.getElementById('jobusLoginModal');
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
            const loginModal = document.getElementById('jobusLoginModal');
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

        //============== Radius Distance Slider & Geolocation ================//
        function initRadiusGeolocation() {
            const radiusWrapper = document.querySelector('.radius-search-wrapper');
            if(!radiusWrapper) return;

            const slider = radiusWrapper.querySelector('#radius_distance');
            const valText = radiusWrapper.querySelector('#radius_val_text');
            const valUnit = radiusWrapper.querySelector('#radius_val_unit');
            const textExact = radiusWrapper.getAttribute('data-text-exact');

            function updateSliderVisuals(sliderEl) {
                const val = parseInt(sliderEl.value);
                if(valText) valText.innerText = (val === 0) ? textExact : val;
                if(valUnit) valUnit.style.display = (val === 0) ? 'none' : 'inline';
                
                const max = parseInt(sliderEl.max) || 250;
                const percentage = (val / max) * 100;
                sliderEl.style.background = 'linear-gradient(to right, var(--jbs-brand_color_1) ' + percentage + '%, #e2e2e2 ' + percentage + '%)';
            }

            if(slider) {
                // Initialize visuals gracefully on load (taking percentage if rendered server-side)
                if(slider.getAttribute('data-percentage')) {
                   const pct = slider.getAttribute('data-percentage');
                   slider.style.background = 'linear-gradient(to right, var(--jbs-brand_color_1) ' + pct + '%, #e2e2e2 ' + pct + '%)';
                } else {
                   updateSliderVisuals(slider);
                }

                // Update visuals instantly on drag
                slider.addEventListener('input', function() {
                    updateSliderVisuals(this);
                });
            }

            const locationBtn = radiusWrapper.querySelector('#jbs_get_my_location');
            if(locationBtn) {
                locationBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const icon = locationBtn.querySelector('i');
                    icon.className = 'bi bi-arrow-repeat jbs-spin';
                    
                    if ("geolocation" in navigator) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const latInput = radiusWrapper.querySelector('#radius_lat');
                            const lngInput = radiusWrapper.querySelector('#radius_lng');
                            const locInput = radiusWrapper.querySelector('#radius_location');

                            if(latInput) latInput.value = position.coords.latitude;
                            if(lngInput) lngInput.value = position.coords.longitude;
                            if(locInput) locInput.value = radiusWrapper.getAttribute('data-text-my-loc');
                            
                            // If they clicked target but slider was Exact (0), snap it magically to default radius
                            if(slider && parseInt(slider.value) === 0) {
                                slider.value = radiusWrapper.getAttribute('data-default-radius');
                                updateSliderVisuals(slider);
                                
                                // Auto-trigger AJAX filter since we forcefully moved the value programmatically
                                slider.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            
                            icon.className = 'bi bi-check-circle';
                            icon.style.color = '#28a745';
                            
                        }, function(error) {
                            alert(radiusWrapper.getAttribute('data-text-err-loc'));
                            icon.className = 'bi bi-crosshair';
                        });
                    } else {
                        alert(radiusWrapper.getAttribute('data-text-err-sup'));
                        icon.className = 'bi bi-crosshair';
                    }
                });
            }

            // Remove coordinates immediately if they tamper with "My Location" string
            const locInput = radiusWrapper.querySelector('#radius_location');
            if(locInput) {
                locInput.addEventListener('input', function() {
                    if (this.value !== radiusWrapper.getAttribute('data-text-my-loc')) {
                        const latInput = radiusWrapper.querySelector('#radius_lat');
                        const lngInput = radiusWrapper.querySelector('#radius_lng');
                        if(latInput) latInput.value = '';
                        if(lngInput) lngInput.value = '';
                        
                        const icon = radiusWrapper.querySelector('#jbs_get_my_location i');
                        if(icon && icon.className.includes('bi-check-circle')) {
                            icon.className = 'bi bi-crosshair';
                            icon.style.color = '';
                        }
                    }
                });
            }
        }
        initRadiusGeolocation();

        //============== Modern AJAX Filter Engine (PJAX) ================//
        function initAjaxFilters() {
            const filterForm = document.querySelector('#filteroffcanvas form');
            const resultWrapper = document.querySelector('[data-jbs-filter-results="true"]');
            
            // Validate that we are on an archive page with proper wrappers
            if (!filterForm || !resultWrapper) return;

            /**
             * Hot-swaps the DOM efficiently with skeleton loading state
             */
            async function triggerAjaxSearch(targetUrl) {
                // Determine the correct URL for the server fetch
                const url = new URL(targetUrl || filterForm.action);
                
                // The beautiful URL specifically for the browser URL bar
                const cleanUrl = new URL(url.href);

                if (!targetUrl) {
                    
                    // SMART OPTIMIZATION: Filter out un-touched Default Range sliders
                    const rangeSliders = filterForm.querySelectorAll('.salary-slider');
                    rangeSliders.forEach(slider => {
                        const minInput = slider.querySelector('.input-min');
                        const maxInput = slider.querySelector('.input-max');
                        const rangeMin = slider.querySelector('.range-min');
                        const rangeMax = slider.querySelector('.range-max');
                        
                        if (minInput && maxInput && rangeMin && rangeMax) {
                            // If they exactly match their outer limit, they haven't been touched by the user. Disable them temporarily so FormData skips them.
                            if (minInput.value == rangeMin.min && maxInput.value == rangeMax.max) {
                                minInput.disabled = true;
                                maxInput.disabled = true;
                            }
                        }
                    });

                    const formData = new FormData(filterForm);
                    
                    // RESTORE Range Sliders immediately so the UI doesn't break
                    rangeSliders.forEach(slider => {
                        const minInput = slider.querySelector('.input-min');
                        const maxInput = slider.querySelector('.input-max');
                        if (minInput) minInput.disabled = false;
                        if (maxInput) maxInput.disabled = false;
                    });
                    
                    const searchParams = new URLSearchParams();
                    const cleanParams = new URLSearchParams(); 
                    
                    // Group multiple values for the same key to construct clean URLs
                    const cleanGroups = {};

                    for (const [key, value] of formData.entries()) {
                        if (value) { 
                            searchParams.append(key, value);
                            
                            // Hide mechanical backend variables from the beautiful UI URL
                            if (key !== 'jobus_nonce' && key !== '_wp_http_referer' && key !== 'post_type') {
                                // Remove array brackets for clean view e.g., 'salary[]' -> 'salary'
                                const cleanKey = key.replace(/\[\]$/, '');
                                if (!cleanGroups[cleanKey]) {
                                    cleanGroups[cleanKey] = [];
                                }
                                cleanGroups[cleanKey].push(value);
                            }
                        }
                    }
                    url.search = searchParams.toString();
                    
                    for (const key in cleanGroups) {
                        cleanParams.append(key, cleanGroups[key].join(','));
                    }
                    cleanUrl.search = cleanParams.toString();
                    
                } else {
                    // For pagination links that might contain nonces
                    const tempParams = new URLSearchParams(cleanUrl.search);
                    tempParams.delete('jobus_nonce');
                    tempParams.delete('_wp_http_referer');
                    tempParams.delete('post_type');
                    // Ensure brackets are stripped in paginations too
                    const paginationCleaned = new URLSearchParams();
                    for (const [key, value] of tempParams.entries()) {
                        paginationCleaned.append(key.replace(/\[\]$/, ''), value);
                    }
                    cleanUrl.search = paginationCleaned.toString();
                }

                // Push history state using the flawlessly cleaned URL object
                window.history.pushState({ path: url.href }, '', cleanUrl.href);

                // Toggle "Clear All" button visibility instantly
                const clearAllBtn = document.getElementById('jbs-clear-all-filters');
                if (clearAllBtn) {
                    let hasActiveFilters = false;
                    const paramsToCheck = cleanUrl.searchParams;
                    for (const [key, value] of paramsToCheck.entries()) {
                        if (value && key !== 'post_type' && key !== 'jobus_nonce' && key !== '_wp_http_referer') {
                            hasActiveFilters = true;
                            break;
                        }
                    }
                    
                    if (hasActiveFilters) {
                        clearAllBtn.classList.remove('jbs-d-none');
                    } else {
                        clearAllBtn.classList.add('jbs-d-none');
                    }
                }

                // Build World-Class Loading Overlay (LinkedIn/Indeed Style)
                resultWrapper.style.position = 'relative';
                resultWrapper.style.pointerEvents = 'none';
                
                let loaderOverlay = document.getElementById('jbs-premium-ajax-loader');
                if (!loaderOverlay) {
                    loaderOverlay = document.createElement('div');
                    loaderOverlay.id = 'jbs-premium-ajax-loader';
                    
                    // Use the native framework spinner matching the Save Post AJAX logic
                    loaderOverlay.innerHTML = '<div class="jbs-spinner-border jbs-text-primary" style="width: 3.5rem; height: 3.5rem; border-width: 0.25em;" role="status"><span class="jbs-visually-hidden"></span></div>';
                    
                    Object.assign(loaderOverlay.style, {
                        position: 'absolute',
                        top: '0',
                        left: '0',
                        width: '100%',
                        height: '100%',
                        backgroundColor: 'rgba(255, 255, 255, 0.65)',
                        backdropFilter: 'blur(3px)',
                        WebkitBackdropFilter: 'blur(3px)',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'flex-start',
                        paddingTop: '150px',
                        zIndex: '99',
                        borderRadius: '10px'
                    });
                    
                    resultWrapper.appendChild(loaderOverlay);
                } else {
                    loaderOverlay.style.display = 'flex';
                }

                try {
                    const response = await fetch(url.href);
                    const htmlText = await response.text();
                    
                    // Parse the new HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(htmlText, 'text/html');
                    
                    const newResultWrapper = doc.querySelector('[data-jbs-filter-results="true"]');
                    if (newResultWrapper) {
                        // Hot swap the DOM securely
                        resultWrapper.innerHTML = newResultWrapper.innerHTML;
                        
                        // Re-initialize frontend behaviors inside the new DOM
                        if (typeof $.fn.niceSelect === 'function') {
                            $(resultWrapper).find('.jbs-nice-select').niceSelect();
                        }
                    }
                } catch (err) {
                    console.error("Jobus AJAX Filter Error:", err);
                } finally {
                    // Restore styling smoothly
                    resultWrapper.style.opacity = '1';
                    resultWrapper.style.pointerEvents = 'auto';
                    
                    // Auto-scroll to top of results on mobile
                    if(window.innerWidth < 992) {
                        resultWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }

            // 2. Intercept Checkboxes and Toggles
            $(filterForm).on('change', 'input[type="checkbox"], input[type="radio"], select', function() {
                triggerAjaxSearch();
            });

            // 3. Intercept Sliders (Range changes)
            $(filterForm).on('change', 'input[type="range"]', function() {
                triggerAjaxSearch();
            });

            // 4. Intercept Text Inputs (Keywords & Location) with a Debounce (Like Google)
            let typingTimer;
            $(filterForm).on('input', 'input[type="text"]', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    triggerAjaxSearch();
                }, 550); // Wait 550ms after user stops typing before fetching
            });

            // 5. Intercept Click on Magnifying Glass Buttons (If they don't want to type and just click)
            $(filterForm).on('click', '.search-form-widget button', function(e) {
                // If the button clicked is the geolocation crosshair, don't trigger form submit
                if(this.id === 'jbs_get_my_location') return; 
                e.preventDefault();
                triggerAjaxSearch();
            });

            // 6. Intercept Form submit (if user hits "Enter" aggressively)
            $(filterForm).on('submit', function(e) {
                e.preventDefault();
                triggerAjaxSearch();
            });

            // 4. Intercept Pagination links inside the wrapper
            $(document).on('click', '[data-jbs-filter-results="true"] .jbs-pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                if (url) {
                    triggerAjaxSearch(url);
                    // Scroll to top of section for pagination
                    window.scrollTo({ top: resultWrapper.offsetTop - 100, behavior: 'smooth' });
                }
            });

            // 5. Handle standard Browser Back/Forward buttons smoothly
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.path) {
                    triggerAjaxSearch(e.state.path);
                } else {
                    window.location.reload();
                }
            });
        }

        initAjaxFilters(); // Start the engine

    });

})(jQuery);