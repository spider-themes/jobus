/**
 * Free vs Pro Comparison Page Scripts
 *
 * Handles category filtering, live feature search, FAQ accordion,
 * and smooth navigation on the Jobus Free vs Pro admin page.
 *
 * @package Jobus
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Category Tabs Filter
		$('.jbs-matrix-filter-btn').on('click', function(e) {
			e.preventDefault();
			var category = $(this).data('category');

			$('.jbs-matrix-filter-btn').removeClass('is-active');
			$(this).addClass('is-active');

			if (category === 'all') {
				$('.jbs-matrix-category-group').show();
				$('.jbs-matrix-table tbody tr').show();
			} else {
				$('.jbs-matrix-category-group').each(function() {
					if ($(this).data('category') === category) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}

			// Clear search input on category switch
			$('#jbs-matrix-search').val('');
			$('.jbs-matrix-no-results').hide();
		});

		// Live Feature Search
		$('#jbs-matrix-search').on('input', function() {
			var query = $(this).val().toLowerCase().trim();
			var matchCount = 0;

			if (query.length === 0) {
				// Restore active category filter
				var activeCat = $('.jbs-matrix-filter-btn.is-active').data('category') || 'all';
				if (activeCat === 'all') {
					$('.jbs-matrix-category-group').show();
					$('.jbs-matrix-table tbody tr').show();
				} else {
					$('.jbs-matrix-category-group').each(function() {
						if ($(this).data('category') === activeCat) {
							$(this).show();
						} else {
							$(this).hide();
						}
					});
				}
				$('.jbs-matrix-no-results').hide();
				return;
			}

			// In search mode, search across all categories
			$('.jbs-matrix-category-group').each(function() {
				var $group = $(this);
				var groupMatches = 0;

				$group.find('tbody tr').each(function() {
					var rowText = $(this).text().toLowerCase();
					if (rowText.indexOf(query) !== -1) {
						$(this).show();
						groupMatches++;
						matchCount++;
					} else {
						$(this).hide();
					}
				});

				if (groupMatches > 0) {
					$group.show();
				} else {
					$group.hide();
				}
			});

			if (matchCount === 0) {
				$('.jbs-matrix-no-results').show();
			} else {
				$('.jbs-matrix-no-results').hide();
			}
		});

		// FAQ Accordion
		$('.jbs-faq-question').on('click', function() {
			var $item = $(this).closest('.jbs-faq-item');
			var $answer = $item.find('.jbs-faq-answer');
			var isOpen = $item.hasClass('is-open');

			// Close all others
			$('.jbs-faq-item').removeClass('is-open');
			$('.jbs-faq-answer').slideUp(200);

			if (!isOpen) {
				$item.addClass('is-open');
				$answer.slideDown(200);
			}
		});

		// Smooth Anchor Scroll
		$('a[href^="#"]').on('click', function(e) {
			var target = $(this.getAttribute('href'));
			if (target.length) {
				e.preventDefault();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - 40
				}, 400);
			}
		});
	});
})(jQuery);
