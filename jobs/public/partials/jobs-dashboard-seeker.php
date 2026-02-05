<?php
/**
 * Job Seeker Dashboard
 */
$user_id = get_current_user_id();
$layout = get_user_meta( $user_id, '_jobs_dash_layout', true ) ?: 'grid';
$applications = new WP_Query( array(
	'post_type' => 'application',
	'author'    => $user_id,
	'posts_per_page' => 5,
) );
?>
<div class="jobs-dashboard seeker-dashboard layout-<?php echo $layout; ?>">
	<h1><?php _e( 'Job Seeker Dashboard', 'jobs' ); ?></h1>
	<div class="dashboard-stats">
		<div class="stat-box">
			<h3><?php _e( 'Applied Jobs', 'jobs' ); ?></h3>
			<p><?php echo $applications->found_posts; ?></p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Profile Status', 'jobs' ); ?></h3>
			<div class="visual-indicator">
				<div class="progress-ring" style="--percent: 85;">
					<svg width="60" height="60">
						<circle cx="30" cy="30" r="25"></circle>
						<circle cx="30" cy="30" r="25"></circle>
					</svg>
					<span>85%</span>
				</div>
			</div>
		</div>
	</div>
	<div class="dashboard-content">
		<?php if ( get_user_meta( get_current_user_id(), '_jobs_profile_public', true ) === 'yes' ) : ?>
		<div class="profile-link-alert" style="background: var(--primary-light); padding: 15px; border-radius: 8px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
			<p style="margin: 0; font-size: 14px; color: var(--primary-color);">
				<strong><?php _e( 'Your Public Profile is Live!', 'jobs' ); ?></strong><br>
				<?php _e( 'Share this link with employers:', 'jobs' ); ?> <code><?php echo home_url( '/job-seeker/' . wp_get_current_user()->user_nicename ); ?></code>
			</p>
			<a href="<?php echo home_url( '/job-seeker/' . wp_get_current_user()->user_nicename ); ?>" class="button button-small" target="_blank"><?php _e( 'View Profile', 'jobs' ); ?></a>
		</div>
		<?php endif; ?>

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
