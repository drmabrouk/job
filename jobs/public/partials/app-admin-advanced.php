<div class="inline-admin-advanced">
	<div class="inline-header">
		<h4><?php _e('Advanced System Settings', 'jobs'); ?></h4>
	</div>

	<div class="admin-tab-nav" style="display:flex; gap:10px; margin-bottom:20px; overflow-x:auto; padding-bottom:10px;">
		<button class="admin-nav-btn active" data-target="admin-users"><i class="fas fa-users"></i> <?php _e('Users', 'jobs'); ?></button>
		<button class="admin-nav-btn" data-target="admin-taxonomy"><i class="fas fa-tags"></i> <?php _e('Taxonomy', 'jobs'); ?></button>
		<button class="admin-nav-btn" data-target="admin-customization"><i class="fas fa-paint-brush"></i> <?php _e('UI', 'jobs'); ?></button>
		<button class="admin-nav-btn" data-target="admin-analytics"><i class="fas fa-chart-line"></i> <?php _e('Analytics', 'jobs'); ?></button>
	</div>

	<div class="admin-tab-content-wrapper">
		<div class="admin-tab-content active" id="admin-users">
			<?php include plugin_dir_path(__FILE__) . 'jobs-admin-users.php'; ?>
		</div>

		<div class="admin-tab-content" id="admin-taxonomy" style="display:none;">
			<div class="taxonomy-manager">
				<div class="tax-block">
					<h5 style="margin-bottom:15px; font-weight:700; color:var(--primary-color);"><?php _e('Locations', 'jobs'); ?></h5>
					<?php include plugin_dir_path(__FILE__) . 'jobs-admin-locations.php'; ?>
				</div>
				<div class="tax-block" style="margin-top:30px;">
					<h5 style="margin-bottom:15px; font-weight:700; color:var(--primary-color);"><?php _e('Professions', 'jobs'); ?></h5>
					<?php include plugin_dir_path(__FILE__) . 'jobs-admin-professions.php'; ?>
				</div>
			</div>
		</div>

		<div class="admin-tab-content" id="admin-customization" style="display:none;">
			<form id="admin-ui-customization-form" style="background:#f8fafc; padding:25px; border-radius:16px;">
				<div class="form-group" style="margin-bottom:20px;">
					<label style="display:block; font-weight:700; margin-bottom:10px;"><?php _e('Site Primary Color', 'jobs'); ?></label>
					<input type="color" name="primary_color" value="<?php echo esc_attr(get_option('jobs_primary_color', '#1d3469')); ?>" style="width:60px; height:40px; border:none; padding:0; cursor:pointer;">
				</div>
				<div class="form-group" style="margin-bottom:25px;">
					<label style="display:block; font-weight:700; margin-bottom:10px;"><?php _e('Font Family', 'jobs'); ?></label>
					<select name="font_family" style="width:100%; padding:10px; border-radius:8px; border:1px solid #e2e8f0;">
						<option value="Rubik" selected>Rubik</option>
						<option value="Arial">Arial</option>
					</select>
				</div>
				<button type="submit" class="jobs-button btn-lg"><?php _e('Save UI Settings', 'jobs'); ?></button>
			</form>
		</div>

		<div class="admin-tab-content" id="admin-analytics" style="display:none;">
			<?php include plugin_dir_path(__FILE__) . 'jobs-frontend-analytics.php'; ?>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('.admin-nav-btn').on('click', function() {
		$('.admin-nav-btn').removeClass('active');
		$(this).addClass('active');
		const target = $(this).data('target');
		$('.admin-tab-content').hide();
		$('#' + target).fadeIn();
	});
});
</script>

<style>
.admin-nav-btn {
	background: #f8fafc;
	border: 1px solid #e2e8f0;
	padding: 8px 15px;
	border-radius: 8px;
	font-size: 12px;
	font-weight: 700;
	cursor: pointer;
	white-space: nowrap;
	transition: all 0.2s;
}
.admin-nav-btn.active {
	background: var(--primary-color);
	color: #fff;
	border-color: var(--primary-color);
}
.admin-tab-content {
	width: 100%;
}
</style>
