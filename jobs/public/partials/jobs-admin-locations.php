<?php
/**
 * Frontend Location Management for Administrators
 */
$locs = get_option( 'jobs_global_locations', array() );

// Handle Add/Remove Logic
if ( isset( $_POST['jobs_admin_action'] ) && $_POST['jobs_admin_action'] == 'save_locations' ) {
	// Logic to update locations from form
	// This would typically involve sanitizing a large array
}

?>
<div class="account-section jobs-admin-locations">
	<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h3><?php _e( 'Location Management', 'jobs' ); ?></h3>
		<button class="jobs-button" onclick="document.getElementById('add-country-modal').style.display='block'"><?php _e( 'Add New Country', 'jobs' ); ?></button>
	</div>

	<div class="admin-location-controls" style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
		<!-- Sidebar List -->
		<div class="location-list-sidebar" style="border: 1px solid #eee; border-radius: 12px; overflow: hidden;">
			<div class="list-header" style="background: #f9f9f9; padding: 15px; font-weight: 700; border-bottom: 1px solid #eee;">
				<?php _e( 'Active Countries', 'jobs' ); ?>
			</div>
			<div class="list-body" style="max-height: 500px; overflow-y: auto;">
				<?php foreach ( array_keys($locs) as $country ) : ?>
					<div class="location-item" style="padding: 12px 15px; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onmouseover="this.style.background='#f0f2f5'" onmouseout="this.style.background='white'">
						<span><?php echo esc_html($country); ?></span>
						<div class="item-actions">
							<i class="dashicons dashicons-edit" style="font-size: 16px; color: #aaa; margin-right: 5px;"></i>
							<i class="dashicons dashicons-trash" style="font-size: 16px; color: #e74c3c;"></i>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Visual Map & Details -->
		<div class="location-visual-area">
			<div class="visual-map-container" style="background: #f0f4f8; padding: 20px; border-radius: 12px; margin-bottom: 30px; border: 1px solid #eee; text-align: center;">
				<h5 style="margin-top: 0; color: #666;"><?php _e( 'Interactive Regional Management', 'jobs' ); ?></h5>
				<svg viewBox="0 0 1000 400" style="width: 100%; height: auto; max-height: 250px;">
					<!-- Simple Stylized Continents/Regions -->
					<path d="M100,100 Q150,50 200,100 T300,100" fill="none" stroke="#cbd5e0" stroke-width="2" />
					<circle cx="200" cy="150" r="40" fill="var(--primary-color)" opacity="0.1" />
					<circle cx="500" cy="200" r="60" fill="var(--primary-color)" opacity="0.2" />
					<circle cx="800" cy="180" r="50" fill="var(--primary-color)" opacity="0.1" />
					<!-- Labels -->
					<text x="200" y="155" text-anchor="middle" font-size="12" fill="var(--primary-color)" font-weight="700">North America</text>
					<text x="500" y="205" text-anchor="middle" font-size="12" fill="var(--primary-color)" font-weight="700">Middle East & Africa</text>
					<text x="800" y="185" text-anchor="middle" font-size="12" fill="var(--primary-color)" font-weight="700">Europe & Asia</text>
				</svg>
				<p style="font-size: 11px; color: #999; margin-top: 10px;"><?php _e( 'Visual representation of job distribution and location nodes.', 'jobs' ); ?></p>
			</div>

			<div class="location-details-card" style="background: #fff; border: 1px solid #eee; padding: 25px; border-radius: 12px;">
				<h4 id="selected-country-name"><?php _e( 'Country Details', 'jobs' ); ?></h4>
				<hr>
				<div class="hierarchical-list">
					<p><strong><?php _e( 'States / Governorates:', 'jobs' ); ?></strong></p>
					<div id="state-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
						<span class="job-tag tag-location"><?php _e( 'Select a country to view states', 'jobs' ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="add-country-modal" style="display:none; position: fixed; z-index: 20000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
	<div style="background: #fff; margin: 10% auto; padding: 30px; width: 400px; border-radius: 12px;">
		<h4><?php _e( 'Add New Country', 'jobs' ); ?></h4>
		<input type="text" placeholder="Country Name" style="width: 100%; padding: 10px; margin: 20px 0; border: 1px solid #ddd; border-radius: 6px;">
		<div style="text-align: right;">
			<button class="jobs-button btn-outline" onclick="document.getElementById('add-country-modal').style.display='none'"><?php _e( 'Cancel', 'jobs' ); ?></button>
			<button class="jobs-button"><?php _e( 'Add', 'jobs' ); ?></button>
		</div>
	</div>
</div>
