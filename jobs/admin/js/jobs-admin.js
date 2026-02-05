(function($) {
	'use strict';

	$(function() {
		var mediaUploader;

		$('#jobs_upload_logo_btn').on('click', function(e) {
			e.preventDefault();

			if (mediaUploader) {
				mediaUploader.open();
				return;
			}

			mediaUploader = wp.media({
				title: 'Select Logo',
				button: {
					text: 'Use this logo'
				},
				multiple: false
			});

			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#jobs_logo_id').val(attachment.id);
				$('#jobs_logo_preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />');
			});

			mediaUploader.open();
		});

		$('#jobs_remove_logo_btn').on('click', function(e) {
			e.preventDefault();
			$('#jobs_logo_id').val('');
			$('#jobs_logo_preview').html('');
		});
	});

})(jQuery);
