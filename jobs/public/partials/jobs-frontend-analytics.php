<?php
/**
 * Frontend Analytics & Reporting
 */
$user_id = get_current_user_id();

$jobs_count = wp_count_posts( 'job' );
$apps_count = wp_count_posts( 'application' );
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
			<h3><?php _e( 'System Performance', 'jobs' ); ?></h3>
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

	</div>
</div>
