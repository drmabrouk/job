(function($) {
	'use strict';

	$(function() {
		var $searchInput = $('.jobs-search-input-modern, #jobs-search-input');
		var $grid = $('#jobs-grid');
		var $pagination = $('#jobs-pagination');
		var searchTimer;

		function performSearch(page) {
			page = page || 1;
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
					paged: page,
					nonce: jobs_ajax.nonce
				},
				beforeSend: function() {
					$grid.css('opacity', '0.5');
				},
				success: function(response) {
					if (response.success) {
						$grid.html(response.data.html);
						renderPagination(response.data.total_pages, response.data.current_page);
					}
					$grid.css('opacity', '1');
				}
			});
		}

		function renderPagination(totalPages, currentPage) {
			$pagination.empty();
			if (totalPages <= 1) return;

			var html = '<div class="jobs-numeric-pagination">';
			for (var i = 1; i <= totalPages; i++) {
				var activeClass = (i == currentPage) ? 'active' : '';
				html += '<button class="page-numbers ' + activeClass + '" data-page="' + i + '">' + i + '</button>';
			}
			html += '</div>';
			$pagination.html(html);
		}

		$(document).on('click', '.page-numbers', function(e) {
			e.preventDefault();
			var page = $(this).data('page');
			performSearch(page);
			$('html, body').animate({
				scrollTop: $grid.offset().top - 150
			}, 500);
		});

		$searchInput.on('keyup', function() {
			clearTimeout(searchTimer);
			searchTimer = setTimeout(performSearch, 300);
		});

		$(document).on('change', '#jobs-category-select, #jobs-state-select', function() {
			performSearch();
		});

		$(document).on('click', '.job-capsule', function() {
			var slug = $(this).data('slug');
			$('#jobs-category-select').val(slug);
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

		// Save Job Logic
		$(document).on('click', '.save-job-btn-refined, .save-job-btn-modern', function(e) {
			e.preventDefault();
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
						var $icon = $btn.find('i');
						if ($btn.hasClass('saved')) {
							$icon.removeClass('far').addClass('fas');
						} else {
							$icon.removeClass('fas').addClass('far');
						}
						if ($('.jobs-toast').length === 0) {
							$('body').append('<div class="jobs-toast" style="position:fixed; bottom:30px; left:50%; transform:translateX(-50%); background:#2d3748; color:#fff; padding:12px 24px; border-radius:50px; z-index:10000; display:none;"></div>');
						}
						$('.jobs-toast').text(response.data).fadeIn().delay(2000).fadeOut();
					} else {
						alert(response.data);
					}
				}
			});
		});

		// AJAX Application Submission
		$(document).on('submit', '#quick-apply-form, #jobs-standard-apply-form', function(e) {
			e.preventDefault();
			var $form = $(this);
			var $btn = $form.find('button[type="submit"], input[type="submit"]');
			var $container = $('#job-apply-container, #jobs-application-container');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: $form.serialize() + '&action=jobs_submit_application_ajax&nonce=' + $form.find('input[name="nonce"]').val(),
				beforeSend: function() {
					$btn.prop('disabled', true).text('Sending...');
				},
				success: function(response) {
					if (response.success) {
						$container.html('<div class="jobs-msg success" style="background:#f0fff4; color:#2f855a; border:1px solid #c6f6d5; padding:20px; border-radius:12px; text-align:center;">' + response.data + '</div>');
					} else {
						alert(response.data);
						$btn.prop('disabled', false).text('Submit');
					}
				}
			});
		});

		// Sidebar Collapse Toggle
		$('.jobs-sidebar-toggle').on('click', function() {
			$('.jobs-account-sidebar').toggleClass('collapsed');
			localStorage.setItem('jobs_sidebar_collapsed', $('.jobs-account-sidebar').hasClass('collapsed'));
		});

		if (localStorage.getItem('jobs_sidebar_collapsed') === 'true') {
			$('.jobs-account-sidebar').addClass('collapsed');
		}

		// Smart Notification Dropdown
		$(document).on('click', '#notif-drop-btn', function(e) {
			e.stopPropagation();
			$('#notif-panel').toggleClass('show');
		});

		$(document).on('click', function() {
			$('.smart-dropdown-panel').removeClass('show');
		});

		$('.smart-dropdown-panel').on('click', function(e) {
			e.stopPropagation();
		});

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
						if ($('.pulse-badge').length) {
							$('.pulse-badge').text(response.data.unread_count);
						}
					}
				}
			});
		}

		if ($('.jobs-top-nav-refined').length) {
			setInterval(checkNotifications, 30000);
			checkNotifications();
		}

		// Browser Geolocation Integration
		if ("geolocation" in navigator) {
			navigator.geolocation.getCurrentPosition(function(position) {
				const lat = position.coords.latitude;
				const lon = position.coords.longitude;

				$.ajax({
					url: jobs_ajax.ajax_url,
					type: 'POST',
					data: {
						action: 'jobs_geo_search',
						lat: lat,
						lon: lon,
						nonce: jobs_ajax.nonce
					},
					success: function(response) {
						if (response.success && response.data.html) {
							$('#jobs-grid').html(response.data.html);
						}
					}
				});
			});
		}

		// Admin User Management Logic (if present)
		$(document).on('click', '#add-user-btn', function() {
			$('#modal-title').text('Add New User');
			if($('#admin-user-form').length) $('#admin-user-form')[0].reset();
			$('#user-modal').fadeIn();
		});

		$(document).on('click', '.edit-user-link', function(e) {
			e.preventDefault();
			$('#modal-title').text('Edit User');
			$('#user-modal').fadeIn();
		});

		$(document).on('click', '.delete-user-link', function(e) {
			e.preventDefault();
			if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
				$(this).closest('tr').fadeOut();
			}
		});

	});

})(jQuery);

// Handle Transparent Nav Scroll
jQuery(window).on('scroll', function() {
    if (jQuery(window).scrollTop() > 50) {
        jQuery('.jobs-top-nav-refined').addClass('scrolled');
    } else {
        jQuery('.jobs-top-nav-refined').removeClass('scrolled');
    }
});

// Unified Action Panel Toggle
jQuery(document).on('click', '#jobs-unified-action-btn', function(e) {
    e.preventDefault();
    jQuery('#jobs-action-panel').addClass('show');
});

jQuery(document).on('click', '.close-panel-btn, .jobs-action-panel-overlay', function(e) {
    if (e.target === this) {
        jQuery('#jobs-action-panel').removeClass('show');
    }
});

jQuery('.action-panel-content').on('click', function(e) {
    e.stopPropagation();
});

jQuery(document).on('click', '.close-panel-btn', function() {
    jQuery('#jobs-action-panel').removeClass('show');
});

// Unified Apps Launcher Toggle
jQuery(document).on('click', '#jobs-apps-launcher-btn', function(e) {
    e.preventDefault();
    jQuery('#jobs-apps-panel').toggleClass('show');
});

jQuery(document).on('click', '.close-apps-btn, .jobs-apps-panel-overlay', function(e) {
    if (e.target === this || jQuery(this).hasClass('close-apps-btn')) {
        jQuery('#jobs-apps-panel').removeClass('show');
    }
});

jQuery('.apps-panel-card').on('click', function(e) {
    e.stopPropagation();
});

// Toggle User Verification
jQuery(document).on('click', '.verify-user-link', function(e) {
    e.preventDefault();
    const btn = jQuery(this);
    const userId = btn.data('id');

    $.post(jobs_ajax.ajax_url, {
        action: 'jobs_toggle_verification',
        user_id: userId
    }, function(res) {
        if (res.success) {
            location.reload(); // Simple way to refresh UI for now
        }
    });
});
