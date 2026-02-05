<?php
/**
 * Unified Account Wrapper
 */
$user = wp_get_current_user();
$roles = (array) $user->roles;
$role_id = $roles[0];
$role_names = get_option( 'jobs_role_names' );
$display_role = isset( $role_names[$role_id] ) ? $role_names[$role_id] : ucfirst($role_id);
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
				<li><a href="<?php echo home_url('/jobs-dashboard'); ?>"><?php _e( 'Dashboard', 'jobs' ); ?></a></li>
				<?php if ( $role_id == 'job_seeker' ) : ?>
					<li><a href="<?php echo home_url('/jobs-dashboard?tab=cv'); ?>"><?php _e( 'CV Manager', 'jobs' ); ?></a></li>
					<li><a href="<?php echo home_url('/jobs-dashboard?tab=saved'); ?>"><?php _e( 'Saved Jobs', 'jobs' ); ?></a></li>
				<?php endif; ?>
				<li><a href="<?php echo home_url('/jobs-settings'); ?>"><?php _e( 'Settings', 'jobs' ); ?></a></li>
				<li><a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e( 'Logout', 'jobs' ); ?></a></li>
			</ul>
		</nav>
	</div>
	<div class="jobs-account-main">
		<?php
		$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'main';
		if ($tab == 'cv' && $role_id == 'job_seeker') {
			include plugin_dir_path(__FILE__) . 'jobs-cv-manager.php';
		} elseif ($tab == 'saved' && $role_id == 'job_seeker') {
			include plugin_dir_path(__FILE__) . 'jobs-saved-items.php';
		} else {
			// Include the original dashboard content based on role
			switch($role_id) {
				case 'job_seeker': include plugin_dir_path(__FILE__) . 'jobs-dashboard-seeker.php'; break;
				case 'employer': include plugin_dir_path(__FILE__) . 'jobs-dashboard-employer.php'; break;
				case 'job_reviewer': include plugin_dir_path(__FILE__) . 'jobs-dashboard-reviewer.php'; break;
				default: include plugin_dir_path(__FILE__) . 'jobs-dashboard-admin.php'; break;
			}
		}
		?>
	</div>
</div>
