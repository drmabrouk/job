<div class="inline-company-profile">
	<div class="inline-header">
		<h4><?php _e('Company Profile', 'jobs'); ?></h4>
		<p><?php _e('Manage your company branding and details.', 'jobs'); ?></p>
	</div>
	<form id="jobs-company-profile-form">
		<?php
		$user_id = get_current_user_id();
		$meta = get_user_meta($user_id);
		?>
		<div class="form-group">
			<label><?php _e('Company Name', 'jobs'); ?></label>
			<input type="text" name="company_name" value="<?php echo esc_attr($meta['_job_company_name'][0] ?? ''); ?>">
		</div>
		<div class="form-row" style="display:flex; gap:15px;">
			<div class="form-group" style="flex:1;">
				<label><?php _e('Employee Count', 'jobs'); ?></label>
				<input type="number" name="employee_count" value="<?php echo esc_attr($meta['_job_employee_count'][0] ?? ''); ?>">
			</div>
			<div class="form-group" style="flex:1;">
				<label><?php _e('Address', 'jobs'); ?></label>
				<input type="text" name="address" value="<?php echo esc_attr($meta['_job_address'][0] ?? ''); ?>">
			</div>
		</div>
		<div class="form-group">
			<label><?php _e('Public Details', 'jobs'); ?></label>
			<textarea name="company_details" rows="5"><?php echo esc_textarea($meta['_job_company_details'][0] ?? ''); ?></textarea>
		</div>
		<button type="submit" class="jobs-button btn-lg"><?php _e('Update Profile', 'jobs'); ?></button>
	</form>
</div>
