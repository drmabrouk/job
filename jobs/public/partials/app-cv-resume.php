<div class="inline-cv-manager">
	<div class="inline-header">
		<h4><?php _e('My CV / Professional Resume', 'jobs'); ?></h4>
		<p><?php _e('Manage your professional identity and generate your resume.', 'jobs'); ?></p>
	</div>

	<div class="cv-onboarding-wrapper">
		<?php include plugin_dir_path(__FILE__) . 'jobs-onboarding.php'; ?>
	</div>

	<div class="cv-actions-footer">
		<a href="#" id="generate-cv-pdf-btn" class="jobs-button"><i class="fas fa-file-pdf"></i> <?php _e('Generate PDF Resume', 'jobs'); ?></a>
	</div>
</div>
