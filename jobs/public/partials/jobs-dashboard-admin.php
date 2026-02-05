<?php
/**
 * System Administrator Dashboard
 */
$total_jobs = wp_count_posts( 'job' );
$total_users = count_users();
$total_apps = wp_count_posts( 'application' );
?>
<div class="jobs-dashboard admin-dashboard">
	<h1><?php _e( 'System Administrator Dashboard', 'jobs' ); ?></h1>
	<div class="dashboard-stats">
		<div class="stat-box">
			<h3><?php _e( 'Total Jobs', 'jobs' ); ?></h3>
			<p><?php echo $total_jobs->publish; ?></p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Total Users', 'jobs' ); ?></h3>
			<p><?php echo $total_users['total_users']; ?></p>
		</div>
		<div class="stat-box">
			<h3><?php _e( 'Total Applications', 'jobs' ); ?></h3>
			<p><?php echo $total_apps->publish; ?></p>
		</div>
	</div>
	<div class="dashboard-content">
		<h2><?php _e( 'System Overview', 'jobs' ); ?></h2>
		<p><?php _e( 'Manage the entire job board system from here.', 'jobs' ); ?></p>
		<ul class="admin-quick-links">
			<li><a href="<?php echo admin_url('edit.php?post_type=job'); ?>"><?php _e( 'Manage All Jobs', 'jobs' ); ?></a></li>
			<li><a href="<?php echo admin_url('edit.php?post_type=application'); ?>"><?php _e( 'Manage All Applications', 'jobs' ); ?></a></li>
			<li><a href="<?php echo admin_url('users.php'); ?>"><?php _e( 'Manage Users', 'jobs' ); ?></a></li>
		</ul>
	</div>
</div>
