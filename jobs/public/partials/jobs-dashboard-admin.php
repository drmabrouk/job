<?php
/**
 * Advanced System Administrator Dashboard
 */
$total_jobs = wp_count_posts( 'job' );
$total_users = count_users();
$total_apps = wp_count_posts( 'application' );

$admin_tab = isset($_GET['admin_tab']) ? sanitize_text_field($_GET['admin_tab']) : 'overview';
?>
<div class="jobs-dashboard admin-dashboard-advanced">
	<div class="admin-dash-header">
		<h1><?php _e( 'System Administration', 'jobs' ); ?></h1>
		<div class="admin-dash-nav">
			<a href="?tab=overview&admin_tab=overview" class="<?php echo $admin_tab == 'overview' ? 'active' : ''; ?>"><?php _e( 'Overview', 'jobs' ); ?></a>
			<a href="?tab=overview&admin_tab=jobs" class="<?php echo $admin_tab == 'jobs' ? 'active' : ''; ?>"><?php _e( 'Job Moderation', 'jobs' ); ?></a>
			<a href="?tab=overview&admin_tab=users" class="<?php echo $admin_tab == 'users' ? 'active' : ''; ?>"><?php _e( 'User Management', 'jobs' ); ?></a>
			<a href="?tab=overview&admin_tab=system" class="<?php echo $admin_tab == 'system' ? 'active' : ''; ?>"><?php _e( 'System Logs', 'jobs' ); ?></a>
		</div>
	</div>

	<?php if ( $admin_tab == 'overview' ) : ?>
		<div class="dashboard-stats">
			<div class="stat-box">
				<i class="dashicons dashicons-businessperson"></i>
				<h3><?php _e( 'Total Jobs', 'jobs' ); ?></h3>
				<p><?php echo $total_jobs->publish + $total_jobs->pending; ?></p>
			</div>
			<div class="stat-box">
				<i class="dashicons dashicons-admin-users"></i>
				<h3><?php _e( 'Total Users', 'jobs' ); ?></h3>
				<p><?php echo $total_users['total_users']; ?></p>
			</div>
			<div class="stat-box">
				<i class="dashicons dashicons-clipboard"></i>
				<h3><?php _e( 'Applications', 'jobs' ); ?></h3>
				<p><?php echo $total_apps->publish; ?></p>
			</div>
		</div>

		<div class="dashboard-content-split">
			<div class="content-block">
				<h3><?php _e( 'Recent Activity', 'jobs' ); ?></h3>
				<ul class="admin-activity-list">
					<?php
					$all_users = get_users( array( 'number' => 5 ) );
					foreach ( $all_users as $u ) :
						$logs = get_user_meta( $u->ID, '_jobs_activity_log', true ) ?: array();
						foreach ( array_slice(array_reverse($logs), 0, 1) as $log ) :
					?>
						<li>
							<strong><?php echo esc_html($u->display_name); ?></strong>: <?php echo esc_html($log['action']); ?>
							<small><?php echo human_time_diff($log['time'], time()); ?> ago</small>
						</li>
					<?php endforeach; endforeach; ?>
				</ul>
			</div>
			<div class="content-block">
				<h3><?php _e( 'Quick Configuration', 'jobs' ); ?></h3>
				<div class="admin-quick-actions">
					<a href="<?php echo home_url('/jobs-settings'); ?>" class="btn-modern btn-outline-modern"><?php _e( 'Plugin Settings', 'jobs' ); ?></a>
					<a href="<?php echo admin_url('options-general.php'); ?>" class="btn-modern btn-outline-modern"><?php _e( 'WP Settings', 'jobs' ); ?></a>
				</div>
			</div>
		</div>

	<?php elseif ( $admin_tab == 'jobs' ) : ?>
		<div class="admin-table-wrapper">
			<h3><?php _e( 'Pending Job Moderation', 'jobs' ); ?></h3>
			<?php
			$pending = new WP_Query( array( 'post_type' => 'job', 'post_status' => 'pending', 'posts_per_page' => 10 ) );
			?>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Title', 'jobs' ); ?></th>
						<th><?php _e( 'Employer', 'jobs' ); ?></th>
						<th><?php _e( 'Date', 'jobs' ); ?></th>
						<th><?php _e( 'Actions', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( $pending->have_posts() ) : while ( $pending->have_posts() ) : $pending->the_post(); ?>
						<tr>
							<td><?php the_title(); ?></td>
							<td><?php the_author(); ?></td>
							<td><?php echo get_the_date(); ?></td>
							<td>
								<a href="<?php echo get_edit_post_link(); ?>" class="button-link"><?php _e( 'Review', 'jobs' ); ?></a>
							</td>
						</tr>
					<?php endwhile; wp_reset_postdata(); else : ?>
						<tr><td colspan="4"><?php _e( 'No pending jobs.', 'jobs' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

	<?php elseif ( $admin_tab == 'users' ) : ?>
		<div class="admin-table-wrapper">
			<h3><?php _e( 'Platform Users', 'jobs' ); ?></h3>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Name', 'jobs' ); ?></th>
						<th><?php _e( 'Email', 'jobs' ); ?></th>
						<th><?php _e( 'Role', 'jobs' ); ?></th>
						<th><?php _e( 'Actions', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( get_users( array( 'number' => 20 ) ) as $u ) : ?>
						<tr>
							<td><?php echo esc_html($u->display_name); ?></td>
							<td><?php echo esc_html($u->user_email); ?></td>
							<td><?php echo implode(', ', $u->roles); ?></td>
							<td><a href="<?php echo get_edit_user_link($u->ID); ?>"><?php _e( 'Edit', 'jobs' ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	<?php elseif ( $admin_tab == 'system' ) : ?>
		<div class="admin-logs-wrapper">
			<h3><?php _e( 'Security Audit Trail', 'jobs' ); ?></h3>
			<?php
			$all_logs = array();
			foreach ( get_users() as $u ) {
				$logs = get_user_meta( $u->ID, '_jobs_activity_log', true ) ?: array();
				foreach($logs as $l) {
					$l['user'] = $u->display_name;
					$all_logs[] = $l;
				}
			}
			usort($all_logs, function($a, $b) { return $b['time'] - $a['time']; });
			?>
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Time', 'jobs' ); ?></th>
						<th><?php _e( 'User', 'jobs' ); ?></th>
						<th><?php _e( 'Action', 'jobs' ); ?></th>
						<th><?php _e( 'IP', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( array_slice($all_logs, 0, 30) as $log ) : ?>
						<tr>
							<td><?php echo date('Y-m-d H:i', $log['time']); ?></td>
							<td><?php echo esc_html($log['user']); ?></td>
							<td><?php echo esc_html($log['action']); ?></td>
							<td><?php echo esc_html($log['ip']); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

<style>
.admin-dash-header {
	margin-bottom: 30px;
}
.admin-dash-nav {
	display: flex;
	gap: 20px;
	border-bottom: 1px solid #eee;
	padding-bottom: 10px;
}
.admin-dash-nav a {
	text-decoration: none;
	color: #666;
	font-weight: 600;
	font-size: 14px;
}
.admin-dash-nav a.active {
	color: var(--primary-color);
	border-bottom: 2px solid var(--primary-color);
}
.dashboard-content-split {
	display: grid;
	grid-template-columns: 2fr 1fr;
	gap: 30px;
	margin-top: 30px;
}
.content-block {
	background: #fdfdfd;
	padding: 20px;
	border-radius: 12px;
	border: 1px solid #eee;
}
.admin-activity-list {
	list-style: none;
	padding: 0;
}
.admin-activity-list li {
	padding: 10px 0;
	border-bottom: 1px solid #f5f5f5;
	font-size: 13px;
}
.admin-quick-actions {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
</style>
