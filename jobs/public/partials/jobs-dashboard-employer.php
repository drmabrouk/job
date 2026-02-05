<?php
/**
 * Employer Dashboard
 */
$user_id = get_current_user_id();
$jobs = new WP_Query( array(
	'post_type' => 'job',
	'author'    => $user_id,
	'posts_per_page' => -1,
) );

$total_apps = 0;
if ( $jobs->have_posts() ) {
	foreach ( $jobs->posts as $job ) {
		$apps = new WP_Query( array(
			'post_type' => 'application',
			'meta_query' => array(
				array(
					'key' => '_job_id',
					'value' => $job->ID,
				),
			),
		) );
		$total_apps += $apps->found_posts;
	}
}
?>
<div class="jobs-dashboard employer-dashboard">
	<h1><?php _e( 'Employer Dashboard', 'jobs' ); ?></h1>
	<div class="dashboard-stats">
		<div class="stat-box">
			<h3><?php _e( 'Your Jobs', 'jobs' ); ?></h3>
			<p><?php echo $jobs->found_posts; ?></p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Total Applications', 'jobs' ); ?></h3>
			<p><?php echo $total_apps; ?></p>
		</div>
	</div>
	<div class="dashboard-content">
		<h2><?php _e( 'Manage Your Jobs', 'jobs' ); ?></h2>
		<?php if ( $jobs->have_posts() ) : ?>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Job Title', 'jobs' ); ?></th>
						<th><?php _e( 'Posted Date', 'jobs' ); ?></th>
						<th><?php _e( 'Status', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
						<tr>
							<td><?php echo get_the_title(); ?></td>
							<td><?php echo get_the_date(); ?></td>
							<td><?php echo get_post_status(); ?></td>
						</tr>
					<?php endwhile; wp_reset_postdata(); ?>
				</tbody>
			</table>
		<?php else : ?>
			<p><?php _e( 'Post a new job to start receiving applications.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>
</div>
