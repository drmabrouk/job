<?php
/**
 * Frontend Translation Verification Module for Administrators
 */
$locales = array('en_US', 'ar');
$strings = array(
	'Jobs Platform' => array('en_US' => 'Jobs Platform', 'ar' => 'منصة الوظائف'),
	'Find Jobs' => array('en_US' => 'Find Jobs', 'ar' => 'بحث عن وظائف'),
	'Apply Now' => array('en_US' => 'Apply Now', 'ar' => 'تقدم الآن'),
	'Quick Apply' => array('en_US' => 'Quick Apply', 'ar' => 'تقدم سريع'),
);

?>
<div class="account-section jobs-translation-verify">
	<h3><?php _e( 'Translation Confirmation System', 'jobs' ); ?></h3>
	<p><?php _e( 'Verify and confirm the accuracy of localized strings across the platform.', 'jobs' ); ?></p>

	<table class="jobs-table" style="margin-top: 20px;">
		<thead>
			<tr>
				<th><?php _e( 'Source String (English)', 'jobs' ); ?></th>
				<th><?php _e( 'Arabic Translation', 'jobs' ); ?></th>
				<th><?php _e( 'Status', 'jobs' ); ?></th>
				<th><?php _e( 'Actions', 'jobs' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $strings as $source => $translations ) : ?>
				<tr>
					<td><strong><?php echo esc_html($source); ?></strong></td>
					<td>
						<input type="text" value="<?php echo esc_attr($translations['ar']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
					</td>
					<td><span class="status-badge status-publish"><?php _e( 'Verified', 'jobs' ); ?></span></td>
					<td>
						<button class="jobs-button" style="padding: 5px 10px; font-size: 12px;"><?php _e( 'Update', 'jobs' ); ?></button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div style="margin-top: 30px; padding: 20px; background: #fffdf0; border: 1px solid #f0e68c; border-radius: 8px;">
		<p style="margin: 0; font-size: 13px;">
			<i class="dashicons dashicons-warning" style="color: #856404;"></i>
			<?php _e( 'All changes here are applied immediately to the frontend. Ensure RTL compatibility before saving.', 'jobs' ); ?>
		</p>
	</div>
</div>
