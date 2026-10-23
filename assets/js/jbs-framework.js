/**
 * JBS Framework - Core UI Components
 * Collapse | Offcanvas | Modal | Tabs
 * 
 * @package jobus
 * @version 1.1.0
 */

;(function ($) {
    'use strict';

    $(document).ready(function () {

        const ANIM_DURATION = 300;

        let lastActiveTrigger = null;

        $(document).on('click', '.jbs-passVicon', function() {
            $(this).toggleClass("jbs-eye-slash");
            const $input = $(this).closest('.jbs-input-group-meta').find('input');
            const type = $input.attr('type') === 'password' ? 'text' : 'password';
            $input.attr('type', type);
        });

        // 1-Click Demo Sandbox Credentials Auto-Fill
        $(document).on('click', '.jbs-btn-fill-demo', function(e) {
            e.preventDefault();
            const user = $(this).data('user');
            const pass = $(this).data('pass');
            const $wrapper = $(this).closest('.jbs-user-data-form, .jbs-login-form-wrapper, .jbs-modal-content');
            const $form = $wrapper.find('form').length ? $wrapper.find('form') : $wrapper.parent().find('form');

            const $userInput = $form.find('input[name="log"], input#user_login, input#user_input').first();
            const $passInput = $form.find('input[name="pwd"], input#user_pass, input#password').first();

            if ($userInput.length) {
                $userInput.val(user).trigger('change');
            }
            if ($passInput.length) {
                $passInput.val(pass).trigger('change');
            }

            // Give natural focus to input for accessibility and native focus styling
            if ($userInput.length) {
                $userInput.trigger('focus');
            }
        });

        // ============================
        // Helper Functions
        // ============================
        const Helpers = {
            toggleCollapse($trigger, $target, open) {
                const action = open ? 'slideDown' : 'slideUp';
                $target[action](ANIM_DURATION, function () {
                    $target.toggleClass('jbs-show jbs-show', open);
                });
                $trigger
                    .toggleClass('jbs-collapsed jbs-collapsed', !open)
                    .attr('aria-expanded', open);
            },

            closeOffcanvas() {
                const $active = $(".jbs-offcanvas.jbs-show, .jbs-offcanvas.show");
                $active.removeClass("jbs-show show");
                $(".jbs-offcanvas-backdrop").removeClass("jbs-show show");
                if (lastActiveTrigger && lastActiveTrigger.length) {
                    lastActiveTrigger.focus();
                    lastActiveTrigger = null;
                }
            },

            closeModal($modal) {
                if ($modal && $modal.length) {
                    $modal.fadeOut(ANIM_DURATION).removeClass("jbs-show show");
                } else {
                    $(".jbs-modal.jbs-show, .jbs-modal.show").fadeOut(ANIM_DURATION).removeClass("jbs-show show");
                }
                if (lastActiveTrigger && lastActiveTrigger.length) {
                    lastActiveTrigger.focus();
                    lastActiveTrigger = null;
                }
            },

            switchTab($trigger, $targetPane, fade) {
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
            }
        };

        // ============================
        // Collapse / Accordion
        // ============================
        $(document).on("click", '[data-jbs-toggle="collapse"]', function (e) {
            e.preventDefault();

            const $this = $(this);
            const targetSelector = $this.attr("href") || $this.data("jbs-target");
            const $target = $(targetSelector);
            const parentSelector = $this.data("jbs-parent");
            const isExpanded = $this.attr("aria-expanded") === "true";

            // Accordion behavior
            if (parentSelector) {
                const $parent = $(parentSelector);
                if ($parent.length) {
                    $parent.find('.jbs-collapse.jbs-show, .jbs-accordion-collapse.jbs-show, .collapse.show, .accordion-collapse.show')
                        .each(function () {
                            const $other = $(this);
                            if ($other[0] !== $target[0]) {
                                $other.slideUp(ANIM_DURATION, function () {
                                    $other.removeClass('jbs-show jbs-show');
                                });
                                const otherId = '#' + $other.attr('id');
                                $parent.find(`[data-jbs-target="${otherId}"], [href="${otherId}"]`)
                                    .addClass('jbs-collapsed jbs-collapsed')
                                    .attr('aria-expanded', 'false');
                            }
                        });
                }
            }

            // Toggle current item
            Helpers.toggleCollapse($this, $target, !isExpanded);
        });

        // ============================
        // Offcanvas
        // ============================
        $(document).on("click", '[data-jbs-toggle="jbs-offcanvas"]', function (e) {
            e.preventDefault();
            lastActiveTrigger = $(this);
            const target = $(this).data("jbs-target");
            const $target = $(target);
            $target.addClass("jbs-show");
            $(".jbs-offcanvas-backdrop").addClass("jbs-show");

            // Move focus into the offcanvas
            setTimeout(function () {
                const $focusable = $target.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible');
                if ($focusable.length) {
                    $focusable.first().focus();
                }
            }, 60);
        });

        $(document).on("click", ".jbs-offcanvas-close, .jbs-offcanvas-backdrop, [data-jbs-dismiss=\"offcanvas\"]", function () {
            Helpers.closeOffcanvas();
        });

        // ============================
        // Modal
        // ============================
        $(document).on("click", ".jbs-open-modal, [data-jbs-toggle=\"modal\"]", function (e) {
            const target = $(this).data("target") || $(this).data("jbs-target");
            const $target = $(target);
            if ($target.length) {
                e.preventDefault();
                lastActiveTrigger = $(this);
                $target.fadeIn(ANIM_DURATION).addClass("jbs-show");

                // Move focus into the modal
                setTimeout(function () {
                    const $focusable = $target.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible');
                    if ($focusable.length) {
                        $focusable.first().focus();
                    }
                }, 60);
            }
        });

        $(document).on("click", ".jbs-btn-close, [data-jbs-dismiss=\"modal\"]", function () {
            Helpers.closeModal($(this).closest(".jbs-modal"));
        });

        $(document).on("click", ".jbs-modal", function (e) {
            if ($(e.target).is(".jbs-modal")) {
                Helpers.closeModal($(this));
            }
        });

        // Close active offcanvas or modal on Escape key
        $(document).on("keydown", function (e) {
            if (e.key === "Escape" || e.keyCode === 27) {
                if ($(".jbs-offcanvas.jbs-show, .jbs-offcanvas.show").length) {
                    Helpers.closeOffcanvas();
                } else if ($(".jbs-modal.jbs-show:visible, .jbs-modal.show:visible").length) {
                    Helpers.closeModal($(".jbs-modal.jbs-show:visible, .jbs-modal.show:visible").first());
                }
            }
        });

        // Keyboard Focus Trap within active Offcanvas or Modal
        $(document).on("keydown", function (e) {
            if (e.key !== "Tab" && e.keyCode !== 9) {
                return;
            }

            const $activeContainer = $(".jbs-offcanvas.jbs-show:visible, .jbs-modal.jbs-show:visible").first();
            if (!$activeContainer.length) {
                return;
            }

            const $focusable = $activeContainer.find(
                'a[href], button:not([disabled]), textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
            ).filter(':visible');

            if (!$focusable.length) {
                return;
            }

            const firstElement = $focusable.first()[0];
            const lastElement = $focusable.last()[0];

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });

        // ============================
        // Tabs
        // ============================
        $(document).on("click", '[data-jbs-toggle="tab"]', function (e) {
            e.preventDefault();

            const $this = $(this);
            const target = $this.data("jbs-target");
            const $targetPane = $(target);

            if ($this.hasClass("active")) return;

            Helpers.switchTab($this, $targetPane, true);    
        });

    });

    // ============================
    // Dropdown Component
    // ============================
    const JBSDropdown = {
        /**
         * Initialize dropdown functionality
         */
        init() {
            this.bindEvents();
            this.closeOnOutsideClick();
        },

        /**
         * Helper: Get toggle and menu from any dropdown element
         */
        getDropdownElements($element) {
            const $dropdownMenu = $element.hasClass('jbs-dropdown-menu')
                ? $element
                : $element.siblings('.jbs-dropdown-menu');
            const $toggle = $dropdownMenu.siblings('[data-jbs-toggle="jbs-dropdown"]');
            return { $toggle, $dropdownMenu };
        },

        /**
         * Helper: Set aria-expanded attribute
         */
        setAriaExpanded($toggle, state) {
            $toggle.attr('aria-expanded', state ? 'true' : 'false');
        },

        /**
         * Helper: Handle horizontal & vertical overflow
         */
        handleOverflow($dropdownMenu) {
            const menuRect = $dropdownMenu[0].getBoundingClientRect();
            const viewportWidth = $(window).width();
            const viewportHeight = $(window).height();

            // Horizontal overflow
            if (menuRect.right > viewportWidth) {
                $dropdownMenu.addClass('jbs-dropdown-menu-end');
            }

            // Vertical overflow
            if (menuRect.bottom > viewportHeight) {
                $dropdownMenu.css({ top: 'auto', bottom: '100%' });
            }
        },

        /**
         * Open dropdown
         */
        openDropdown($toggle, $dropdownMenu) {
            $dropdownMenu.addClass('jbs-show');
            this.setAriaExpanded($toggle, true);
            this.handleOverflow($dropdownMenu);
            $toggle.trigger('jbs-dropdown:show');
        },

        /**
         * Close dropdown
         */
        closeDropdown($toggle, $dropdownMenu) {
            $dropdownMenu.removeClass('jbs-show');
            this.setAriaExpanded($toggle, false);
            $toggle.trigger('jbs-dropdown:hide');
        },

        /**
         * Close all dropdowns
         */
        closeAllDropdowns() {
            $('.jbs-dropdown-menu.show').each(function () {
                const { $toggle, $dropdownMenu } = JBSDropdown.getDropdownElements($(this));
                JBSDropdown.closeDropdown($toggle, $dropdownMenu);
            });
        },

        /**
         * Event bindings
         */
        bindEvents() {
            const self = this;

            // Toggle click
            $(document).on('click', '[data-jbs-toggle="jbs-dropdown"]', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $toggle = $(this);
                const { $dropdownMenu } = self.getDropdownElements($toggle);
                const autoClose = $toggle.data('jbs-auto-close') || 'true';
                const isOpen = $dropdownMenu.hasClass('jbs-show');

                self.closeAllDropdowns();

                if (!isOpen) {
                    self.openDropdown($toggle, $dropdownMenu);
                    $dropdownMenu.data('auto-close', autoClose);
                } else {
                    self.closeDropdown($toggle, $dropdownMenu);
                }
            });

            // Dropdown item click
            $(document).on('click', '.jbs-dropdown-menu .jbs-dropdown-item', function (e) {
                const $item = $(this);
                const { $toggle, $dropdownMenu } = self.getDropdownElements($item);
                const autoClose = $dropdownMenu.data('auto-close') || 'true';

                if ($item.hasClass('disabled') || $item.prop('disabled')) {
                    e.preventDefault();
                    return;
                }

                if (autoClose === 'true' || autoClose === true || autoClose === 'jbs-inside') {
                    self.closeAllDropdowns();
                }
            });

            // Keyboard navigation
            $(document).on('keydown', '[data-jbs-toggle="jbs-dropdown"], .jbs-dropdown-menu', function (e) {
                const $current = $(this);
                const { $toggle, $dropdownMenu } = self.getDropdownElements($current);

                switch (e.key) {
                    case 'Escape':
                        if ($dropdownMenu.hasClass('jbs-show')) {
                            e.preventDefault();
                            self.closeDropdown($toggle, $dropdownMenu);
                            $toggle.focus();
                        }
                        break;

                    case 'ArrowDown':
                    case 'ArrowUp':
                        e.preventDefault();
                        const $items = $dropdownMenu.find('.jbs-dropdown-item:not(.disabled):not(:disabled)');
                        const currentIndex = $items.index($(document.activeElement));
                        const nextIndex =
                            e.key === 'ArrowDown'
                                ? (currentIndex + 1) % $items.length
                                : (currentIndex - 1 + $items.length) % $items.length;
                        $items.eq(nextIndex).focus();
                        break;

                    case 'Enter':
                    case ' ':
                        if ($current.is('[data-jbs-toggle="jbs-dropdown"]') && !$dropdownMenu.hasClass('jbs-show')) {
                            e.preventDefault();
                            $current.click();
                        }
                        break;
                }
            });
        },

        /**
         * Close dropdown when clicking outside
         */
        closeOnOutsideClick() {
            const self = this;
            $(document).on('click', function (e) {
                const $target = $(e.target);
                const $closestDropdown = $target.closest('.jbs-dropdown-menu');

                if ($closestDropdown.length && $closestDropdown.data('auto-close') === 'jbs-outside') {
                    return;
                }

                if (
                    !$target.closest('[data-jbs-toggle="jbs-dropdown"]').length &&
                    !$target.closest('.jbs-dropdown-menu').length
                ) {
                    self.closeAllDropdowns();
                }
            });
        }
    };

    $(document).ready(() => JBSDropdown.init());
    window.JBSDropdown = JBSDropdown;

})(jQuery);
