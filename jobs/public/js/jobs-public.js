(function($) {
	'use strict';

	$(function() {
		var $searchInput = $('#jobs-search-input');
		var $grid = $('#jobs-grid');
		var searchTimer;

		function performSearch() {
			var keyword = $searchInput.val();
			var category = $('#jobs-category-select').val();
			var type = $('#jobs-type-select').val();
			var country = $('#jobs-country-select').val();
			var state = $('#jobs-state-select').val();

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'jobs_search',
					keyword: keyword,
					category: category,
					type: type,
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

		$(document).on('change', '#jobs-type-select', function() {
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

		// Job Reactivation
		$(document).on('click', '.reactivate-job', function() {
			var $btn = $(this);
			var jobId = $btn.data('id');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'reactivate_job',
					job_id: jobId,
					nonce: jobs_ajax.nonce
				},
				success: function(response) {
					alert(response.data);
					location.reload();
				}
			});
		});

		// Job Extension
		$(document).on('click', '.extend-job', function() {
			var $btn = $(this);
			var jobId = $btn.data('id');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'extend_job',
					job_id: jobId,
					nonce: jobs_ajax.nonce
				},
				success: function(response) {
					alert(response.data);
					location.reload();
				}
			});
		});

		// Save Job
		$(document).on('click', '.save-job-btn', function() {
			var $btn = $(this);
			var jobId = $btn.data('id');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'save_job',
					job_id: jobId,
					nonce: jobs_ajax.nonce
				},
				success: function(response) {
					if (response.success) {
						$btn.toggleClass('saved');
						$btn.text($btn.hasClass('saved') ? '★' : '☆');
					}
				}
			});
		});

		// Follow Employer
		// Notification Polling
		function checkNotifications() {
			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'check_notifications',
					nonce: jobs_ajax.nonce
				},
				success: function(response) {
					if (response.success && response.data.unread_count > 0) {
						if ($('.jobs-nav-links .notif-badge').length) {
							$('.jobs-nav-links .notif-badge').text(response.data.unread_count);
						} else {
							$('.jobs-nav-links a[href*="jobs-dashboard"]').append(' <span class="notif-badge">' + response.data.unread_count + '</span>');
						}
					}
				}
			});
		}

		if ($('.jobs-top-nav').length) {
			setInterval(checkNotifications, 30000); // Every 30 seconds
			checkNotifications();
		}

		$(document).on('click', '.follow-employer-btn', function() {
			var $btn = $(this);
			var empId = $btn.data('id');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'follow_employer',
					employer_id: empId,
					nonce: jobs_ajax.nonce
				},
				success: function(response) {
					if (response.success) {
						$btn.toggleClass('followed');
						if ($btn.hasClass('followed')) {
							$btn.text('Unfollow Employer');
						} else {
							$btn.text('Follow Employer');
						}
						// If in list, maybe remove or reload
					}
				}
			});
		});
	});

})(jQuery);
