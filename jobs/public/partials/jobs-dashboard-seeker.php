<?php
/**
 * Job Seeker Dashboard
 */
$user_id = get_current_user_id();
$applications = new WP_Query( array(
	'post_type' => 'application',
	'author'    => $user_id,
	'posts_per_page' => 5,
) );
?>
<div class="jobs-dashboard seeker-dashboard">
	<h1><?php _e( 'Job Seeker Dashboard', 'jobs' ); ?></h1>
	<div class="dashboard-stats">
		<div class="stat-box">
			<h3><?php _e( 'Applied Jobs', 'jobs' ); ?></h3>
			<p><?php echo $applications->found_posts; ?></p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Profile Status', 'jobs' ); ?></h3>
			<p><?php _e( 'Active', 'jobs' ); ?></p>
		</div>
	</div>
	<div class="dashboard-content">
		<h2><?php _e( 'Recent Applications', 'jobs' ); ?></h2>
		<?php if ( $applications->have_posts() ) : ?>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Job Title', 'jobs' ); ?></th>
						<th><?php _e( 'Date', 'jobs' ); ?></th>
						<th><?php _e( 'Status', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ( $applications->have_posts() ) : $applications->the_post(); ?>
						<tr>
							<td><?php echo get_the_title(); ?></td>
							<td><?php echo get_the_date(); ?></td>
							<td><?php echo get_post_status(); ?></td>
						</tr>
					<?php endwhile; wp_reset_postdata(); ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php _e( 'You haven\'t applied to any jobs yet.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>
</div>
