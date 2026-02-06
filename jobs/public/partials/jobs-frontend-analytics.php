<?php
/**
 * Frontend Analytics & Reporting
 */
$user_id = get_current_user_id();

$jobs_count = wp_count_posts( 'job' );
$apps_count = wp_count_posts( 'application' );

$profile_views = get_user_meta( $user_id, '_jobs_profile_views', true ) ?: array();
$total_views = count($profile_views);
?>
<div class="jobs-analytics-section">
	<h2><?php _e( 'Reports & Analytics', 'jobs' ); ?></h2>

	<div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px; margin-top: 20px;">

		<div class="analytics-card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
			<h3><?php _e( 'Activity Overview', 'jobs' ); ?></h3>
			<div class="visual-chart" style="margin-top: 20px;">
				<div class="chart-label" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
					<span><?php _e( 'Active Jobs', 'jobs' ); ?></span>
					<span><?php echo $jobs_count->publish; ?></span>
				</div>
				<div class="chart-bar-bg" style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
					<div class="chart-bar-fill" style="background: #1d3469; height: 100%; width: <?php echo min(100, $jobs_count->publish * 10); ?>%;"></div>
				</div>

				<div class="chart-label" style="display: flex; justify-content: space-between; margin-top: 15px; margin-bottom: 5px;">
					<span><?php _e( 'Total Applications', 'jobs' ); ?></span>
					<span><?php echo $apps_count->publish; ?></span>
				</div>
				<div class="chart-bar-bg" style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
					<div class="chart-bar-fill" style="background: #27ae60; height: 100%; width: <?php echo min(100, $apps_count->publish * 5); ?>%;"></div>
				</div>
			</div>
		</div>

		<div class="analytics-card" style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee;">
			<h3><?php _e( 'Your Performance', 'jobs' ); ?></h3>
			<div class="stat-box" style="margin-top: 15px; background: var(--primary-light);">
				<h3><?php _e( 'Profile Views', 'jobs' ); ?></h3>
				<p><?php echo $total_views; ?></p>
			</div>
			<div class="performance-indicators" style="display: flex; justify-content: space-around; margin-top: 25px;">
				<div class="indicator">
					<div class="progress-ring" style="--percent: 75;">
						<svg width="80" height="80">
							<circle cx="40" cy="40" r="35"></circle>
							<circle cx="40" cy="40" r="35"></circle>
						</svg>
						<span style="font-size: 16px;">75%</span>
					</div>
					<p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;"><?php _e( 'Conversion', 'jobs' ); ?></p>
				</div>
				<div class="indicator">
					<div class="progress-ring" style="--percent: 92;">
						<svg width="80" height="80">
							<circle cx="40" cy="40" r="35"></circle>
							<circle cx="40" cy="40" r="35"></circle>
						</svg>
						<span style="font-size: 16px;">92%</span>
					</div>
					<p style="text-align: center; margin-top: 10px; font-size: 14px; color: #666;"><?php _e( 'Satisfaction', 'jobs' ); ?></p>
				</div>
			</div>
		</div>

		<div class="analytics-card" style="grid-column: 1 / -1; background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #eee;">
			<h3><?php _e( 'Your Activity Logs', 'jobs' ); ?></h3>
			<table class="jobs-table" style="width:100%; border-collapse: collapse; margin-top: 20px;">
				<thead>
					<tr style="text-align: left; border-bottom: 2px solid #f1f5f9;">
						<th style="padding: 15px;"><?php _e( 'Action', 'jobs' ); ?></th>
						<th style="padding: 15px;"><?php _e( 'Date & Time', 'jobs' ); ?></th>
						<th style="padding: 15px;"><?php _e( 'IP Address', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$logs = get_user_meta( $user_id, '_jobs_activity_log', true ) ?: array();
					if ( ! empty($logs) ) : foreach ( array_reverse(array_slice($logs, -10)) as $log ) : ?>
						<tr style="border-bottom: 1px solid #f1f5f9;">
							<td style="padding: 15px; font-weight: 600; color: var(--primary-color);"><?php echo esc_html($log['action']); ?></td>
							<td style="padding: 15px; color: #718096;"><?php echo date('M j, Y H:i', $log['time']); ?></td>
							<td style="padding: 15px; color: #a0aec0; font-family: monospace;"><?php echo esc_html($log['ip']); ?></td>
						</tr>
					<?php endforeach; else : ?>
						<tr><td colspan="3" style="text-align: center; padding: 30px; color: #94a3b8;"><?php _e( 'No activity logs found.', 'jobs' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

	</div>
</div>
