<?php
/**
 * Frontend Application Management
 */
$user_id = get_current_user_id();
$user = wp_get_current_user();
$role = (array) $user->roles;
$role_id = $role[0];

?>
<div class="jobs-applications-section">
	<h2><?php _e( 'Applications', 'jobs' ); ?></h2>

	<?php if ( $role_id == 'employer' ) : ?>
		<h3><?php _e( 'Applications Received', 'jobs' ); ?></h3>
		<?php
		$my_jobs = get_posts( array( 'post_type' => 'job', 'author' => $user_id, 'fields' => 'ids' ) );
		if ( ! empty( $my_jobs ) ) {
			$apps = new WP_Query( array(
				'post_type' => 'application',
				'meta_query' => array(
					array(
						'key' => '_job_id',
						'value' => $my_jobs,
						'compare' => 'IN'
					)
				)
			) );
		} else {
			$apps = null;
		}
		?>
		<table class="jobs-table">
			<thead>
				<tr>
					<th><?php _e( 'Applicant', 'jobs' ); ?></th>
					<th><?php _e( 'Job Title', 'jobs' ); ?></th>
					<th><?php _e( 'Date', 'jobs' ); ?></th>
					<th><?php _e( 'Action', 'jobs' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $apps && $apps->have_posts() ) : while ( $apps->have_posts() ) : $apps->the_post();
					$applicant_id = get_post_field( 'post_author', get_the_ID() );
					$applicant = get_userdata( $applicant_id );
					$job_id = get_post_meta( get_the_ID(), '_job_id', true );
				?>
					<tr>
						<td><?php echo $applicant ? esc_html($applicant->display_name) : 'Unknown'; ?></td>
						<td><?php echo get_the_title($job_id); ?></td>
						<td><?php echo get_the_date(); ?></td>
						<td><a href="<?php echo home_url('/job-seeker/' . $applicant->user_nicename); ?>" target="_blank"><?php _e( 'View Profile', 'jobs' ); ?></a></td>
					</tr>
				<?php endwhile; wp_reset_postdata(); else : ?>
					<tr><td colspan="4"><?php _e( 'No applications received.', 'jobs' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>

	<?php else : ?>
		<h3><?php _e( 'My Applications', 'jobs' ); ?></h3>
		<?php
		$apps = new WP_Query( array(
			'post_type' => 'application',
			'author' => $user_id,
		) );
		?>
		<table class="jobs-table">
			<thead>
				<tr>
					<th><?php _e( 'Job Title', 'jobs' ); ?></th>
					<th><?php _e( 'Date Applied', 'jobs' ); ?></th>
					<th><?php _e( 'Status', 'jobs' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $apps->have_posts() ) : while ( $apps->have_posts() ) : $apps->the_post();
					$job_id = get_post_meta( get_the_ID(), '_job_id', true );
				?>
					<tr>
						<td><?php echo get_the_title($job_id); ?></td>
						<td><?php echo get_the_date(); ?></td>
						<td><span class="status-badge status-publish"><?php _e( 'Submitted', 'jobs' ); ?></span></td>
					</tr>
				<?php endwhile; wp_reset_postdata(); else : ?>
					<tr><td colspan="3"><?php _e( 'You haven\'t applied to any jobs yet.', 'jobs' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
