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
			var maxVisible = 5;
			var start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
			var end = Math.min(totalPages, start + maxVisible - 1);

			if (end - start + 1 < maxVisible) {
				start = Math.max(1, end - maxVisible + 1);
			}

			if (start > 1) {
				html += '<button class="page-numbers" data-page="1">1</button>';
				if (start > 2) html += '<span class="dots">...</span>';
			}

			for (var i = start; i <= end; i++) {
				var activeClass = (i == currentPage) ? 'active' : '';
				html += '<button class="page-numbers ' + activeClass + '" data-page="' + i + '">' + i + '</button>';
			}

			if (end < totalPages) {
				if (end < totalPages - 1) html += '<span class="dots">...</span>';
				html += '<button class="page-numbers" data-page="' + totalPages + '">' + totalPages + '</button>';
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

		// Unified Apps Launcher Toggle
		$(document).on('click', '#jobs-apps-launcher-btn', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('#jobs-apps-panel').addClass('show');
		});

		$(document).on('click', '.close-apps-btn, .jobs-apps-panel-overlay', function(e) {
			if (e.target === this || $(this).hasClass('close-apps-btn')) {
				$('#jobs-apps-panel').removeClass('show');
			}
		});

		$('.apps-panel-card').on('click', function(e) {
			e.stopPropagation();
		});

		// Apps Launcher Sub-panels
		$(document).on('click', '#app-post-job-trigger', function() {
			$('#inline-job-post-panel').css('display', 'flex');
		});

		$(document).on('click', '.back-to-apps', function() {
			$('#inline-job-post-panel').hide();
		});

		// Inline Job Post Submission
		$(document).on('submit', '#jobs-inline-post-form', function(e) {
			e.preventDefault();
			var $form = $(this);
			var $btn = $form.find('button[type="submit"]');
			var $result = $('#inline-post-result');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: $form.serialize() + '&action=jobs_post_job_ajax',
				beforeSend: function() {
					$btn.prop('disabled', true).text('Posting...');
				},
				success: function(res) {
					if (res.success) {
						$result.html('<div class="jobs-msg success">' + res.data + '</div>');
						$form.slideUp();
						setTimeout(function() {
							$('#inline-job-post-panel').hide();
							$form.show();
							$form[0].reset();
							$result.empty();
							$btn.prop('disabled', false).text('Post Job Now');
						}, 2000);
					} else {
						$result.html('<div class="jobs-msg error">' + res.data + '</div>');
						$btn.prop('disabled', false).text('Post Job Now');
					}
				}
			});
		});

		// Global Top Nav Refined Interactions
		$(window).on('scroll', function() {
			if ($(window).scrollTop() > 30) {
				$('.jobs-global-top-nav-refined').addClass('scrolled');
			} else {
				$('.jobs-global-top-nav-refined').removeClass('scrolled');
			}
		});

		// Profile Dropdown
		$(document).on('click', '#jobs-profile-pic-btn', function(e) {
			e.stopPropagation();
			$('#jobs-profile-dropdown').toggle();
			$('#jobs-notif-panel, #jobs-msg-panel').hide();
		});

		// Notifications Panel
		$(document).on('click', '#jobs-notif-trigger', function(e) {
			e.stopPropagation();
			$('#jobs-notif-panel').toggle();
			$('#jobs-profile-dropdown, #jobs-msg-panel').hide();
		});

		// Messages Panel
		$(document).on('click', '#jobs-msg-trigger', function(e) {
			e.stopPropagation();
			$('#jobs-msg-panel').toggle();
			$('#jobs-profile-dropdown, #jobs-notif-panel').hide();
		});

		$(document).on('click', function() {
			$('#jobs-profile-dropdown, #jobs-notif-panel, #jobs-msg-panel').hide();
		});

		// Toggle User Verification
		$(document).on('click', '.verify-user-link', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var userId = $btn.data('id');

			$.post(jobs_ajax.ajax_url, {
				action: 'jobs_toggle_verification',
				user_id: userId
			}, function(res) {
				if (res.success) {
					location.reload();
				}
			});
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
						if ($('.notif-dot').length) {
							$('.notif-dot').show();
						}
					} else {
						$('.notif-dot').hide();
					}
				}
			});
		}

		if ($('.jobs-global-top-nav-refined').length) {
			setInterval(checkNotifications, 30000);
			checkNotifications();
		}

		// Browser Geolocation
		if ("geolocation" in navigator) {
			navigator.geolocation.getCurrentPosition(function(position) {
				$.ajax({
					url: jobs_ajax.ajax_url,
					type: 'POST',
					data: {
						action: 'jobs_geo_search',
						lat: position.coords.latitude,
						lon: position.coords.longitude,
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
	});

})(jQuery);

// Unified Apps Sub-panel Logic
jQuery(document).ready(function($) {
    $('.sub-trigger, .profile-sub-trigger').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const panelId = $(this).data('panel');
        $('#jobs-apps-panel').addClass('show'); // Ensure main panel is open
        $('.apps-sub-panel').hide();
        $(`#panel-${panelId}`).css('display', 'flex');
        $('#jobs-profile-dropdown').hide();
    });

    $('.back-btn').on('click', function() {
        $(this).closest('.apps-sub-panel').hide();
    });
});

// Company Profile and Support AJAX
jQuery(document).ready(function($) {
    $(document).on('submit', '#jobs-company-profile-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        $.post(jobs_ajax.ajax_url, $form.serialize() + '&action=jobs_save_company_profile', function(res) {
            alert(res.data);
        });
    });

    $(document).on('submit', '#jobs-support-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        $.post(jobs_ajax.ajax_url, $form.serialize() + '&action=jobs_send_support_message', function(res) {
            alert(res.data);
            $form[0].reset();
            $('.apps-sub-panel').hide();
        });
    });
});

// Global Modal Logic
jQuery(document).ready(function($) {
    function openGlobalModal(title, contentHtml) {
        $('#jobs-modal-title').text(title);
        $('#jobs-modal-body').html(contentHtml);
        $('#jobs-global-modal').css('display', 'flex').hide().fadeIn(300);
        $('body').addClass('jobs-modal-open');
    }

    function closeGlobalModal() {
        $('#jobs-global-modal').fadeOut(300, function() {
            $(this).hide();
            $('#jobs-modal-body').empty();
            $('body').removeClass('jobs-modal-open');
        });
    }

    $(document).on('click', '.jobs-modal-close-btn, .jobs-modal-overlay', function(e) {
        if (e.target === this || $(this).hasClass('jobs-modal-close-btn')) {
            closeGlobalModal();
        }
    });

    $('.jobs-modal-container').on('click', function(e) {
        e.stopPropagation();
    });

    // Update Apps Launcher to use Global Modal
    $('.sub-trigger, .profile-sub-trigger').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const panelId = $(this).data('panel');
        const $sourcePanel = $(`#panel-${panelId}`);

        if ($sourcePanel.length) {
            const title = $sourcePanel.find('h4').text();
            const content = $sourcePanel.find('.sub-panel-body').html();
            openGlobalModal(title, content);
            $('#jobs-apps-panel').removeClass('show');
            $('#jobs-profile-dropdown').hide();
        }
    });
});
