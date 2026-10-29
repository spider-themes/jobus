/**
 * Free vs Pro comparison page scripts.
 *
 * Category filters, live search, FAQ accordion, and Freemius checkout cleanup.
 *
 * @package Jobus
 */

(function ($) {
	'use strict';

	function hideFreemiusLeakage() {
		var $scope = $('.jbs-free-vs-pro-wrap, body.jobus_job_page_jobus-free-vs-pro');

		$scope.find('.fs-secure-notice, .fs-terms, .fs-checkout-taxes, p.fs-subtitle').remove();

		$scope.find('p, small, span').each(function () {
			var text = ($(this).text() || '').toLowerCase();
			if (text.indexOf('vat') !== -1 && text.indexOf('sales tax') !== -1) {
				$(this).hide();
			}
		});
	}

	function applyCategory(category) {
		if (category === 'all') {
			$('.jbs-matrix-category-group').show();
			$('.jbs-matrix-table tbody tr').show();
			return;
		}

		$('.jbs-matrix-category-group').each(function () {
			var $group = $(this);
			if ($group.data('category') === category) {
				$group.show();
				$group.find('tr').show();
			} else {
				$group.hide();
			}
		});
	}

	function runSearch(query) {
		var matchCount = 0;

		if (!query) {
			var activeCat = $('.jbs-matrix-filter-btn.is-active').data('category') || 'all';
			applyCategory(activeCat);
			$('.jbs-matrix-no-results').hide();
			return;
		}

		$('.jbs-matrix-category-group').each(function () {
			var $group = $(this);
			var groupMatches = 0;

			$group.find('tr.jbs-feature-row').each(function () {
				var rowText = $(this).text().toLowerCase();
				if (rowText.indexOf(query) !== -1) {
					$(this).show();
					groupMatches += 1;
					matchCount += 1;
				} else {
					$(this).hide();
				}
			});

			if (groupMatches > 0) {
				$group.show();
				$group.find('tr.jbs-matrix-category-header').show();
			} else {
				$group.hide();
			}
		});

		$('.jbs-matrix-no-results').toggle(matchCount === 0);
	}

	$(document).ready(function () {
		hideFreemiusLeakage();
		setTimeout(hideFreemiusLeakage, 400);
		setTimeout(hideFreemiusLeakage, 1200);

		$('.jbs-matrix-filter-btn').on('click', function (e) {
			e.preventDefault();
			var category = $(this).data('category');

			$('.jbs-matrix-filter-btn').removeClass('is-active');
			$(this).addClass('is-active');

			$('#jbs-matrix-search').val('');
			$('.jbs-matrix-no-results').hide();
			applyCategory(category);
		});

		$('#jbs-matrix-search').on('input', function () {
			runSearch($(this).val().toLowerCase().trim());
		});

		$('.jbs-faq-question').on('click', function () {
			var $item = $(this).closest('.jbs-faq-item');
			var $answer = $item.find('.jbs-faq-answer');
			var isOpen = $item.hasClass('is-open');

			$('.jbs-faq-item').removeClass('is-open');
			$('.jbs-faq-answer').slideUp(180);

			if (!isOpen) {
				$item.addClass('is-open');
				$answer.slideDown(180);
			}
		});

		$('.jbs-faq-item.is-open .jbs-faq-answer').show();

		$('.jbs-free-vs-pro-wrap a[href^="#"]').on('click', function (e) {
			var target = $($(this).attr('href'));
			if (target.length) {
				e.preventDefault();
				$('html, body').stop().animate({
					scrollTop: target.offset().top - 48
				}, 380);
			}
		});
	});
})(jQuery);
