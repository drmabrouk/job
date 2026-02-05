<?php
/**
 * Ad Tracking & Management
 */
?>
<div class="jobs-ads-section">
	<h2><?php _e( 'Advertisement Tracking', 'jobs' ); ?></h2>
	<p class="description"><?php _e( 'Monitor your ad campaign performance and manage placements.', 'jobs' ); ?></p>

	<div class="ad-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 25px;">
		<div class="stat-box">
			<h3><?php _e( 'Active Campaigns', 'jobs' ); ?></h3>
			<p>3</p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Total Impressions', 'jobs' ); ?></h3>
			<p>12,450</p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Click-Through Rate', 'jobs' ); ?></h3>
			<p>2.4%</p>
		</div>
	</div>

	<h3 style="margin-top: 40px;"><?php _e( 'Live Ad Zones', 'jobs' ); ?></h3>
	<table class="jobs-table">
		<thead>
			<tr>
				<th><?php _e( 'Zone Name', 'jobs' ); ?></th>
				<th><?php _e( 'Status', 'jobs' ); ?></th>
				<th><?php _e( 'Performance', 'jobs' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong><?php _e( 'Search Top Banner', 'jobs' ); ?></strong></td>
				<td><span class="status-badge status-publish"><?php _e( 'Active', 'jobs' ); ?></span></td>
				<td><?php _e( 'High Visibility', 'jobs' ); ?></td>
			</tr>
			<tr>
				<td><strong><?php _e( 'Category Premium', 'jobs' ); ?></strong></td>
				<td><span class="status-badge status-publish"><?php _e( 'Active', 'jobs' ); ?></span></td>
				<td><?php _e( 'Targeted', 'jobs' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
