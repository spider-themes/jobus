;(function ($) {
    'use strict';

    $(document).ready(function () {

        // -----------------------------
        // Accordion Functionality
        // -----------------------------
        $(document).on('click', '.jbs-accordion-button', function (e) {
            e.preventDefault();
            
            var $button = $(this);
            var targetSelector = $button.data('jbs-target');
            var $target = $(targetSelector);
            var parentSelector = $button.data('jbs-parent');
            var isExpanded = $button.attr('aria-expanded') === 'true';

            // If there's a parent accordion, close other items
            if (parentSelector) {
                var $parent = $(parentSelector);
                if ($parent.length) {
                    $parent.find('.jbs-accordion-collapse.jbs-show').each(function() {
                        var $otherCollapse = $(this);
                        if ($otherCollapse[0] !== $target[0]) {
                            $otherCollapse.slideUp(300, function() {
                                $otherCollapse.removeClass('jbs-show');
                            });
                            
                            // Update the other button states
                            var otherId = '#' + $otherCollapse.attr('id');
                            var $otherButton = $parent.find('[data-jbs-target="' + otherId + '"]');
                            $otherButton.addClass('jbs-collapsed').attr('aria-expanded', 'false');
                        }
                    });
                }
            }

            // Toggle current item
            if (isExpanded) {
                // Close
                $target.slideUp(300, function() {
                    $target.removeClass('jbs-show');
                });
                $button.addClass('jbs-collapsed').attr('aria-expanded', 'false');
            } else {
                // Open
                $target.slideDown(300, function() {
                    $target.addClass('jbs-show');
                });
                $button.removeClass('jbs-collapsed').attr('aria-expanded', 'true');
            }
        });

        // Open offcanvas
        $(document).on("click", '[data-jbs-toggle="jbs-offcanvas"]', function (e) {
            e.preventDefault();
            var target = $(this).data("jbs-target");
            $(target).addClass("show");
            $(".jbs-offcanvas-backdrop").addClass("show");
        });

        // Close button
        $(document).on("click", ".jbs-offcanvas-close", function () {
            $(this).closest(".jbs-offcanvas").removeClass("show");
            $(".jbs-offcanvas-backdrop").removeClass("show");
        });

        // Click on backdrop
        $(document).on("click", ".jbs-offcanvas-backdrop", function () {
            $(".jbs-offcanvas.show").removeClass("show");
            $(this).removeClass("show");
        });

        // modal close on esc key press
        $(".jbs-open-modal").on("click", function (e) {
            e.preventDefault();
            var target = $(this).data("target");
            $(target).fadeIn(300).addClass("show");
        });

        // -----------------------------
        // Close Modal
        // -----------------------------
        $(".jbs-btn-close").on("click", function () {
            var modal = $(this).closest(".jbs-modal");
            modal.fadeOut(300).removeClass("show"); // fadeOut + remove show
        });

        // -----------------------------
        // Click Outside Modal Content
        // -----------------------------
        $(".jbs-modal").on("click", function (e) {
            if ($(e.target).is(".jbs-modal")) {
                // only if click on backdrop
                $(this).fadeOut(300).removeClass("show");
            }
        });

        $(".filter-header").on("click", function () {
            $(this).toggleClass("jbs-collapsed");
            $(".jbs-collapse").slideToggle(300);
        });

        // -----------------------------
        // Tab Functionality with Smooth Animation
        // -----------------------------
        $('[data-jbs-toggle="tab"]').on("click", function (e) {
            e.preventDefault();

            var $this = $(this);
            var target = $this.data("jbs-target");
            var $targetPane = $(target);

            // Return if already active
            if ($this.hasClass("active")) {
                return;
            }

            // Remove active from all tabs in same nav
            $this.closest(".jbs-nav").find(".jbs-nav-link").removeClass("active");
            
            // Add active to clicked tab
            $this.addClass("active");

            // Find all tab panes in the same tab-content container
            var $tabContentContainer = $targetPane.closest(".jbs-tab-content");
            var $allPanes = $tabContentContainer.find(".jbs-tab-pane");
            
            // Fade out currently active pane
            var $activePanes = $allPanes.filter(".active");
            
            if ($activePanes.length > 0 && $targetPane.hasClass("jbs-fade")) {
                // Smooth fade transition
                $activePanes.removeClass("jbs-show");
                
                // Wait for fade out animation to complete (300ms as per CSS)
                setTimeout(function() {
                    $activePanes.removeClass("active");
                    
                    // Fade in target pane
                    $targetPane.addClass("active");
                    
                    // Small delay to ensure the DOM updates before adding jbs-show
                    setTimeout(function() {
                        $targetPane.addClass("jbs-show");
                    }, 10);
                }, 300);
            } else {
                // No fade effect, instant switch
                $allPanes.removeClass("jbs-show active");
                $targetPane.addClass("jbs-show active");
            }
        });

    });

})(jQuery);
