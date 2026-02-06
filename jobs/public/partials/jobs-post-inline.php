<div class="inline-post-job-container">
	<div class="inline-header">
		<h4><?php _e('Post a New Job', 'jobs'); ?></h4>
		<p><?php _e('Complete the fields below to list your vacancy.', 'jobs'); ?></p>
	</div>
	<form id="jobs-inline-post-form">
		<?php wp_nonce_field( 'jobs_post_job_nonce', 'nonce' ); ?>
		<div class="form-grid">
			<div class="form-group">
				<label><?php _e('Job Title', 'jobs'); ?> *</label>
				<input type="text" name="job_title" required placeholder="e.g. Senior Software Engineer">
			</div>
			<div class="form-group">
				<label><?php _e('Specialization', 'jobs'); ?></label>
				<select name="job_category" required>
					<?php
					$categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false ) );
					foreach ( $categories as $cat ) echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
					?>
				</select>
			</div>
			<div class="form-group">
				<label><?php _e('Location (City, Country)', 'jobs'); ?></label>
				<input type="text" name="job_location" placeholder="London, UK">
			</div>
			<div class="form-group">
				<label><?php _e('Employment Type', 'jobs'); ?></label>
				<select name="job_type">
					<?php
					$types = get_terms( array( 'taxonomy' => 'job_type', 'hide_empty' => false ) );
					foreach ( $types as $t ) echo '<option value="'.$t->term_id.'">'.$t->name.'</option>';
					?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label><?php _e('Job Description', 'jobs'); ?> *</label>
			<textarea name="job_description" rows="5" required></textarea>
		</div>
		<div class="inline-form-footer">
			<button type="submit" class="jobs-button btn-lg"><?php _e('Post Job Now', 'jobs'); ?></button>
		</div>
	</form>
	<div id="inline-post-result"></div>
</div>
