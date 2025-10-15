/**
 * JBS Framework - Dropdown Component
 * 
 * Custom dropdown implementation to replace Bootstrap dropdown functionality.
 * Provides toggle, keyboard navigation, and auto-close functionality.
 * 
 * @package jobus
 * @version 1.0.0
 */

;(function ($) {
    'use strict';

    const JBSDropdown = {
        /**
         * Initialize all dropdown functionality
         */
        init: function () {
            this.bindEvents();
            this.closeOnOutsideClick();
        },

        /**
         * Bind click events for dropdown toggles
         */
        bindEvents: function () {
            // Handle dropdown toggle clicks
            $(document).on('click', '[data-jbs-toggle="jbs-dropdown"]', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const $toggle = $(this);
                const $dropdownMenu = $toggle.siblings('.jbs-dropdown-menu');
                const autoClose = $toggle.data('jbs-auto-close') || 'true';
                const isOpen = $dropdownMenu.hasClass('show');

                // Close all other open dropdowns first
                JBSDropdown.closeAllDropdowns();

                // Toggle current dropdown
                if (!isOpen) {
                    JBSDropdown.openDropdown($toggle, $dropdownMenu);
                    
                    // Store auto-close setting
                    $dropdownMenu.data('auto-close', autoClose);
                } else {
                    JBSDropdown.closeDropdown($toggle, $dropdownMenu);
                }
            });

            // Handle dropdown item clicks
            $(document).on('click', '.jbs-dropdown-menu .jbs-dropdown-item', function (e) {
                const $item = $(this);
                const $dropdownMenu = $item.closest('.jbs-dropdown-menu');
                const $toggle = $dropdownMenu.siblings('[data-jbs-toggle="jbs-dropdown"]');
                const autoClose = $dropdownMenu.data('auto-close') || 'true';

                // Don't close if disabled
                if ($item.hasClass('disabled') || $item.prop('disabled')) {
                    e.preventDefault();
                    return;
                }

                // Handle auto-close behavior
                if (autoClose === 'true' || autoClose === true) {
                    JBSDropdown.closeDropdown($toggle, $dropdownMenu);
                } else if (autoClose === 'jbs-inside') {
                    // Close only if clicked inside
                    JBSDropdown.closeDropdown($toggle, $dropdownMenu);
                }
                // 'jbs-outside' or 'false' means don't auto-close on item click
            });

            // Keyboard navigation
            $(document).on('keydown', '[data-jbs-toggle="jbs-dropdown"], .jbs-dropdown-menu', function (e) {
                const $current = $(this);
                const $dropdownMenu = $current.hasClass('jbs-dropdown-menu') 
                    ? $current 
                    : $current.siblings('.jbs-dropdown-menu');
                const $toggle = $dropdownMenu.siblings('[data-jbs-toggle="jbs-dropdown"]');

                // ESC key - close dropdown
                if (e.key === 'Escape' || e.keyCode === 27) {
                    if ($dropdownMenu.hasClass('show')) {
                        e.preventDefault();
                        JBSDropdown.closeDropdown($toggle, $dropdownMenu);
                        $toggle.focus();
                    }
                }

                // Arrow keys for navigation
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();

                    const $items = $dropdownMenu.find('.jbs-dropdown-item:not(.disabled):not(:disabled)');
                    const currentIndex = $items.index($(document.activeElement));

                    let nextIndex;
                    if (e.key === 'ArrowDown') {
                        nextIndex = currentIndex < $items.length - 1 ? currentIndex + 1 : 0;
                    } else {
                        nextIndex = currentIndex > 0 ? currentIndex - 1 : $items.length - 1;
                    }

                    $items.eq(nextIndex).focus();
                }

                // Enter or Space - open dropdown or activate item
                if (e.key === 'Enter' || e.key === ' ') {
                    if ($current.is('[data-jbs-toggle="jbs-dropdown"]') && !$dropdownMenu.hasClass('show')) {
                        e.preventDefault();
                        $current.click();
                    }
                }
            });
        },

        /**
         * Close dropdown when clicking outside
         */
        closeOnOutsideClick: function () {
            $(document).on('click', function (e) {
                const $target = $(e.target);

                // Don't close if clicking inside a dropdown with auto-close="jbs-outside"
                const $closestDropdown = $target.closest('.jbs-dropdown-menu');
                if ($closestDropdown.length && $closestDropdown.data('auto-close') === 'jbs-outside') {
                    return;
                }

                // Close all dropdowns if clicking outside
                if (!$target.closest('[data-jbs-toggle="jbs-dropdown"]').length && 
                    !$target.closest('.jbs-dropdown-menu').length) {
                    JBSDropdown.closeAllDropdowns();
                }
            });
        },

        /**
         * Open a dropdown
         * 
         * @param {jQuery} $toggle - The toggle button element
         * @param {jQuery} $dropdownMenu - The dropdown menu element
         */
        openDropdown: function ($toggle, $dropdownMenu) {
            $dropdownMenu.addClass('show');
            $toggle.attr('aria-expanded', 'true');

            // Position dropdown if needed
            this.positionDropdown($dropdownMenu);

            // Trigger custom event
            $toggle.trigger('jbs-dropdown:show');
        },

        /**
         * Close a dropdown
         * 
         * @param {jQuery} $toggle - The toggle button element
         * @param {jQuery} $dropdownMenu - The dropdown menu element
         */
        closeDropdown: function ($toggle, $dropdownMenu) {
            $dropdownMenu.removeClass('show');
            $toggle.attr('aria-expanded', 'false');

            // Trigger custom event
            $toggle.trigger('jbs-dropdown:hide');
        },

        /**
         * Close all open dropdowns
         */
        closeAllDropdowns: function () {
            $('.jbs-dropdown-menu.show').each(function () {
                const $dropdownMenu = $(this);
                const $toggle = $dropdownMenu.siblings('[data-jbs-toggle="jbs-dropdown"]');
                JBSDropdown.closeDropdown($toggle, $dropdownMenu);
            });
        },

        /**
         * Position dropdown to prevent overflow
         * 
         * @param {jQuery} $dropdownMenu - The dropdown menu element
         */
        positionDropdown: function ($dropdownMenu) {
            const menuRect = $dropdownMenu[0].getBoundingClientRect();
            const viewportWidth = $(window).width();
            const viewportHeight = $(window).height();

            // Check if dropdown overflows right edge
            if (menuRect.right > viewportWidth) {
                $dropdownMenu.addClass('jbs-dropdown-menu-end');
            }

            // Check if dropdown overflows bottom edge
            if (menuRect.bottom > viewportHeight) {
                $dropdownMenu.css({
                    top: 'auto',
                    bottom: '100%'
                });
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        JBSDropdown.init();
    });

    // Make it globally accessible if needed
    window.JBSDropdown = JBSDropdown;

})(jQuery);
