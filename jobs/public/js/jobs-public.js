(function($) {
	'use strict';

	$(function() {
		var $searchInput = $('#jobs-search-input');
		var $grid = $('#jobs-grid');
		var searchTimer;

		function performSearch() {
			var keyword = $searchInput.val();
			var category = $('#jobs-category-select').val();
			var country = $('#jobs-country-select').val();
			var state = $('#jobs-state-select').val();

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'jobs_search',
					keyword: keyword,
					category: category,
					country: country,
					state: state,
					nonce: jobs_ajax.nonce
				},
				beforeSend: function() {
					$grid.css('opacity', '0.5');
				},
				success: function(response) {
					if (response.success) {
						$grid.html(response.data.html);
						if (response.data.category_ad) {
							if ($('.jobs-ad-premium').length) {
								$('.jobs-ad-premium').html(response.data.category_ad);
							} else {
								$('.jobs-search-section').after('<div class="jobs-ad-zone jobs-ad-premium">' + response.data.category_ad + '</div>');
							}
						} else {
							$('.jobs-ad-premium').remove();
						}
					}
					$grid.css('opacity', '1');
				}
			});
		}

		$searchInput.on('keyup', function() {
			clearTimeout(searchTimer);
			searchTimer = setTimeout(performSearch, 300);
		});

		$(document).on('change', '#jobs-category-select', function() {
			performSearch();
		});

		$(document).on('change', '#jobs-state-select', function() {
			performSearch();
		});

		// Dynamic Location System
		$(document).on('change', '#jobs-country-select', function() {
			var country = $(this).val();
			var $stateSelect = $('#jobs-state-select');

			if (country) {
				$.ajax({
					url: jobs_ajax.ajax_url,
					type: 'POST',
					data: {
						action: 'get_states',
						country: country,
						nonce: jobs_ajax.nonce
					},
					success: function(response) {
						if (response.success) {
							$stateSelect.html('<option value="">Select State</option>');
							$.each(response.data, function(index, state) {
								$stateSelect.append('<option value="' + state + '">' + state + '</option>');
							});
							$stateSelect.prop('disabled', false);
						} else {
							$stateSelect.html('<option value="">' + response.data + '</option>');
							$stateSelect.prop('disabled', true);
						}
						performSearch();
					}
				});
			} else {
				$stateSelect.html('<option value="">Select Country First</option>');
				$stateSelect.prop('disabled', true);
				performSearch();
			}
		});
	});

})(jQuery);
