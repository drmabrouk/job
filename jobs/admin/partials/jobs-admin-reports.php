<?php

/**
 * Provide a admin area view for the reports dashboard
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/admin/partials
 */
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<div id="jobs-reports-dashboard" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">

		<div class="metrics-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			<h3 style="margin-top: 0;"><?php _e( 'Total Jobs', 'jobs' ); ?></h3>
			<p style="font-size: 24px; font-weight: 700; color: #1d3469;"><?php echo esc_html( $metrics['total_jobs'] ); ?></p>
		</div>

		<div class="metrics-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			<h3 style="margin-top: 0;"><?php _e( 'Published Jobs', 'jobs' ); ?></h3>
			<p style="font-size: 24px; font-weight: 700; color: #27ae60;"><?php echo esc_html( $metrics['published_jobs'] ); ?></p>
		</div>

		<div class="metrics-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			<h3 style="margin-top: 0;"><?php _e( 'Pending Jobs', 'jobs' ); ?></h3>
			<p style="font-size: 24px; font-weight: 700; color: #f39c12;"><?php echo esc_html( $metrics['pending_jobs'] ); ?></p>
		</div>

		<div class="metrics-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			<h3 style="margin-top: 0;"><?php _e( 'Total Applications', 'jobs' ); ?></h3>
			<p style="font-size: 24px; font-weight: 700; color: #2980b9;"><?php echo esc_html( $metrics['total_applications'] ); ?></p>
		</div>

		<div class="metrics-card" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
			<h3 style="margin-top: 0;"><?php _e( 'Total Users', 'jobs' ); ?></h3>
			<p style="font-size: 24px; font-weight: 700; color: #8e44ad;"><?php echo esc_html( $metrics['total_users'] ); ?></p>
		</div>

	</div>
</div>
