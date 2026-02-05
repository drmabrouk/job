<?php
/**
 * Job Reviewer Dashboard
 */
$pending_jobs = new WP_Query( array(
	'post_type'   => 'job',
	'post_status' => 'pending',
	'posts_per_page' => -1,
) );
?>
<div class="jobs-dashboard reviewer-dashboard">
	<h1><?php _e( 'Job Reviewer Dashboard', 'jobs' ); ?></h1>
	<div class="dashboard-stats">
		<div class="stat-box">
			<h3><?php _e( 'Jobs Pending Review', 'jobs' ); ?></h3>
			<p><?php echo $pending_jobs->found_posts; ?></p>
		</div>
	</div>
	<div class="dashboard-content">
		<h2><?php _e( 'Review Queue', 'jobs' ); ?></h2>
		<?php if ( $pending_jobs->have_posts() ) : ?>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Job Title', 'jobs' ); ?></th>
						<th><?php _e( 'Submitted Date', 'jobs' ); ?></th>
						<th><?php _e( 'Action', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ( $pending_jobs->have_posts() ) : $pending_jobs->the_post(); ?>
						<tr>
							<td><?php echo get_the_title(); ?></td>
							<td><?php echo get_the_date(); ?></td>
							<td><a href="<?php echo get_edit_post_link(); ?>"><?php _e( 'Review', 'jobs' ); ?></a></td>
						</tr>
					<?php endwhile; wp_reset_postdata(); ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php _e( 'No jobs currently pending review.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>
</div>
