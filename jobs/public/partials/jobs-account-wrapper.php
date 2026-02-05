<?php
/**
 * Unified Account Wrapper - Comprehensive Frontend Control Panel
 */
$user = wp_get_current_user();
$roles = (array) $user->roles;
$role_id = $roles[0];
$role_names = get_option( 'jobs_role_names' );
$display_role = isset( $role_names[$role_id] ) ? $role_names[$role_id] : ucfirst($role_id);

$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'overview';
?>
<div class="jobs-account-unified">
	<div class="jobs-account-sidebar">
		<div class="user-profile-summary">
			<?php echo get_avatar( $user->ID, 80 ); ?>
			<h3><?php echo esc_html( $user->display_name ); ?></h3>
			<span class="role-label"><?php echo esc_html( $display_role ); ?></span>
		</div>
		<nav class="account-nav">
			<ul>
				<li class="<?php echo ($tab == 'overview') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=overview'); ?>"><i class="dashicons dashicons-dashboard"></i> <?php _e( 'Overview', 'jobs' ); ?></a></li>

				<?php if ( $role_id == 'employer' || $role_id == 'administrator' || $role_id == 'system_administrator' ) : ?>
					<li class="<?php echo ($tab == 'manage-jobs') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=manage-jobs'); ?>"><i class="dashicons dashicons-businessperson"></i> <?php _e( 'Job Management', 'jobs' ); ?></a></li>
				<?php endif; ?>

				<?php if ( $role_id == 'job_seeker' || $role_id == 'administrator' || $role_id == 'system_administrator' ) : ?>
					<li class="<?php echo ($tab == 'my-applications') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=my-applications'); ?>"><i class="dashicons dashicons-clipboard"></i> <?php _e( 'Applications', 'jobs' ); ?></a></li>
					<li class="<?php echo ($tab == 'cv') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=cv'); ?>"><i class="dashicons dashicons-welcome-write-blog"></i> <?php _e( 'CV Manager', 'jobs' ); ?></a></li>
					<li class="<?php echo ($tab == 'saved') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=saved'); ?>"><i class="dashicons dashicons-star-filled"></i> <?php _e( 'Saved Items', 'jobs' ); ?></a></li>
				<?php endif; ?>

				<li class="<?php echo ($tab == 'notifications') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=notifications'); ?>"><i class="dashicons dashicons-bell"></i> <?php _e( 'Notifications', 'jobs' ); ?></a></li>

				<li class="<?php echo ($tab == 'analytics') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=analytics'); ?>"><i class="dashicons dashicons-chart-area"></i> <?php _e( 'Reports & Analytics', 'jobs' ); ?></a></li>

				<?php if ( $role_id == 'employer' || $role_id == 'administrator' || $role_id == 'system_administrator' ) : ?>
					<li class="<?php echo ($tab == 'ads') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=ads'); ?>"><i class="dashicons dashicons-megaphone"></i> <?php _e( 'Ad Tracking', 'jobs' ); ?></a></li>
				<?php endif; ?>

				<li class="<?php echo ($tab == 'settings') ? 'active' : ''; ?>"><a href="<?php echo home_url('/jobs-dashboard?tab=settings'); ?>"><i class="dashicons dashicons-admin-settings"></i> <?php _e( 'Account Settings', 'jobs' ); ?></a></li>

				<li class="logout-link"><a href="<?php echo wp_logout_url( home_url() ); ?>"><i class="dashicons dashicons-exit"></i> <?php _e( 'Logout', 'jobs' ); ?></a></li>
			</ul>
		</nav>
	</div>
	<div class="jobs-account-main">
		<div class="jobs-tab-content">
			<?php
			switch($tab) {
				case 'manage-jobs':
					include plugin_dir_path(__FILE__) . 'jobs-manage-jobs.php';
					break;
				case 'my-applications':
					include plugin_dir_path(__FILE__) . 'jobs-manage-applications.php';
					break;
				case 'cv':
					include plugin_dir_path(__FILE__) . 'jobs-cv-manager.php';
					break;
				case 'saved':
					include plugin_dir_path(__FILE__) . 'jobs-saved-items.php';
					break;
				case 'notifications':
					include plugin_dir_path(__FILE__) . 'jobs-notifications.php';
					break;
				case 'analytics':
					include plugin_dir_path(__FILE__) . 'jobs-frontend-analytics.php';
					break;
				case 'ads':
					include plugin_dir_path(__FILE__) . 'jobs-ad-tracking.php';
					break;
				case 'settings':
					include plugin_dir_path(__FILE__) . 'jobs-settings-panel.php';
					break;
				case 'overview':
				default:
					switch($role_id) {
						case 'job_seeker': include plugin_dir_path(__FILE__) . 'jobs-dashboard-seeker.php'; break;
						case 'employer': include plugin_dir_path(__FILE__) . 'jobs-dashboard-employer.php'; break;
						case 'job_reviewer': include plugin_dir_path(__FILE__) . 'jobs-dashboard-reviewer.php'; break;
						default: include plugin_dir_path(__FILE__) . 'jobs-dashboard-admin.php'; break;
					}
					break;
			}
			?>
		</div>
	</div>
</div>
