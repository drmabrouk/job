<?php
/**
 * Unified Account Wrapper - High-End Dashboard Architecture
 */
$user = wp_get_current_user();
$roles = (array) $user->roles;
$role_id = $roles[0];
$role_names = get_option( 'jobs_role_names' );
$display_role = isset( $role_names[$role_id] ) ? $role_names[$role_id] : ucfirst($role_id);

$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'overview';
$page_titles = array(
	'overview'        => __( 'Dashboard Overview', 'jobs' ),
	'manage-jobs'     => __( 'Job Management', 'jobs' ),
	'my-applications' => __( 'Applications', 'jobs' ),
	'messages'        => __( 'Messages', 'jobs' ),
	'notifications'   => __( 'Notifications', 'jobs' ),
	'cv'              => __( 'Document Manager', 'jobs' ),
	'saved'           => __( 'Saved Items', 'jobs' ),
	'analytics'       => __( 'Reports & Insights', 'jobs' ),
	'settings'        => __( 'Account Settings', 'jobs' ),
	'help'            => __( 'Help & Support', 'jobs' ),
	'onboarding'      => __( 'Complete Your Profile', 'jobs' ),
);
$current_title = isset($page_titles[$tab]) ? $page_titles[$tab] : __( 'Dashboard', 'jobs' );
?>

<div class="jobs-dashboard-wrapper" style="display: flex; background: #fcfcfc; min-height: 100vh;">
	<!-- Sidebar -->
	<aside class="jobs-account-sidebar" style="width: 280px; background: #fff; border-right: 1px solid #f0f0f0; display: flex; flex-direction: column; flex-shrink: 0;">
		<div class="sidebar-header" style="padding: 30px; text-align: center;">
			<button class="jobs-sidebar-toggle" style="background:none; border:none; color:#cbd5e0; cursor:pointer; float:right;"><i class="dashicons dashicons-menu"></i></button>
			<div style="clear:both;"></div>
			<?php echo get_avatar( $user->ID, 100, '', '', array('style' => 'border-radius: 24px; margin-bottom: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05);') ); ?>
			<h3 style="margin: 0; font-size: 18px; color: #2d3748; font-weight: 700;"><?php echo esc_html( $user->display_name ); ?></h3>
			<span class="role-label" style="display: inline-block; background: var(--primary-light); color: var(--primary-color); padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 8px; text-transform: uppercase;"><?php echo esc_html( $display_role ); ?></span>
		</div>

		<nav class="account-nav" style="flex: 1; padding: 0 20px;">
			<ul style="list-style: none; padding: 0; margin: 0;">
				<li class="<?php echo ($tab == 'overview') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
					<a href="<?php echo home_url('/jobs-dashboard?tab=overview'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'overview') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'overview') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
						<i class="dashicons dashicons-dashboard"></i> <span><?php _e( 'Overview', 'jobs' ); ?></span>
					</a>
				</li>

				<li class="<?php echo ($tab == 'settings') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
					<a href="<?php echo home_url('/jobs-dashboard?tab=settings'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'settings') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'settings') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
						<i class="dashicons dashicons-admin-users"></i> <span><?php _e( 'Profile & Settings', 'jobs' ); ?></span>
					</a>
				</li>

				<?php if ( $role_id !== 'job_seeker' ) : ?>
					<li class="<?php echo ($tab == 'manage-jobs') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
						<a href="<?php echo home_url('/jobs-dashboard?tab=manage-jobs'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'manage-jobs') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'manage-jobs') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
							<i class="dashicons dashicons-businessperson"></i> <span><?php _e( 'Jobs', 'jobs' ); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<li class="<?php echo ($tab == 'my-applications') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
					<a href="<?php echo home_url('/jobs-dashboard?tab=my-applications'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'my-applications') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'my-applications') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
						<i class="dashicons dashicons-clipboard"></i> <span><?php _e( 'Applications', 'jobs' ); ?></span>
					</a>
				</li>

				<li class="<?php echo ($tab == 'messages') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
					<a href="<?php echo home_url('/jobs-dashboard?tab=messages'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'messages') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'messages') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
						<i class="dashicons dashicons-email-alt"></i> <span><?php _e( 'Messages', 'jobs' ); ?></span>
					</a>
				</li>

				<?php if ( $role_id == 'job_seeker' ) : ?>
					<li class="<?php echo ($tab == 'cv') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
						<a href="<?php echo home_url('/jobs-dashboard?tab=cv'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'cv') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'cv') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
							<i class="dashicons dashicons-welcome-write-blog"></i> <span><?php _e( 'CV & Documents', 'jobs' ); ?></span>
						</a>
					</li>
					<li class="<?php echo ($tab == 'saved') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
						<a href="<?php echo home_url('/jobs-dashboard?tab=saved'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'saved') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'saved') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
							<i class="dashicons dashicons-archive"></i> <span><?php _e( 'Saved Items', 'jobs' ); ?></span>
						</a>
					</li>
				<?php endif; ?>

				<li class="<?php echo ($tab == 'analytics') ? 'active' : ''; ?>" style="margin-bottom: 5px;">
					<a href="<?php echo home_url('/jobs-dashboard?tab=analytics'); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: <?php echo ($tab == 'analytics') ? 'var(--primary-color)' : '#718096'; ?>; background: <?php echo ($tab == 'analytics') ? 'var(--primary-light)' : 'transparent'; ?>; font-weight: 600; font-size: 14px;">
						<i class="dashicons dashicons-chart-area"></i> <span><?php _e( 'Reports & Insights', 'jobs' ); ?></span>
					</a>
				</li>
			</ul>
		</nav>
		<div class="sidebar-footer" style="padding: 20px;">
			<a href="<?php echo wp_logout_url( home_url() ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; border-radius: 12px; text-decoration: none; color: #e53e3e; font-weight: 600; font-size: 14px; background: #fff5f5;">
				<i class="dashicons dashicons-exit"></i> <span><?php _e( 'Logout', 'jobs' ); ?></span>
			</a>
		</div>
	</aside>

	<div class="account-main" style="flex: 1; display: flex; flex-direction: column;">
		<!-- Header -->
		<header class="jobs-dash-header" style="height: 80px; background: #fff; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; padding: 0 40px;">
			<div class="header-breadcrumb" style="display: flex; align-items: center; gap: 10px; color: #718096; font-size: 14px;">
				<span style="font-weight: 600; color: var(--primary-color);"><?php _e( 'Jobs Platform', 'jobs' ); ?></span>
				<i class="dashicons dashicons-arrow-right-alt2" style="font-size: 14px; width: 14px; height: 14px;"></i>
				<span><?php echo esc_html($current_title); ?></span>
			</div>
			<div class="header-actions">
				<?php if ( $role_id == 'employer' ) : ?>
					<a href="?tab=manage-jobs&action=add" class="jobs-button"><i class="dashicons dashicons-plus"></i> <?php _e( 'Post a New Job', 'jobs' ); ?></a>
				<?php elseif ( $role_id == 'job_seeker' ) : ?>
					<a href="<?php echo home_url('/jobs'); ?>" class="jobs-button"><i class="dashicons dashicons-search"></i> <?php _e( 'Browse Jobs', 'jobs' ); ?></a>
				<?php endif; ?>
			</div>
		</header>

		<!-- Content -->
		<main class="account-body" style="flex: 1; padding: 20px 0; overflow-y: auto;">
			<div class="jobs-tab-content" style="max-width: 100%; width: 100%;">
				<?php
				switch($tab) {
					case 'manage-jobs':
						include plugin_dir_path(__FILE__) . 'jobs-manage-jobs.php';
						break;
					case 'my-applications':
						include plugin_dir_path(__FILE__) . 'jobs-manage-applications.php';
						break;
					case 'cv':
						include plugin_dir_path(__FILE__) . 'jobs-document-manager.php';
						break;
					case 'saved':
						include plugin_dir_path(__FILE__) . 'jobs-saved-items.php';
						break;
					case 'notifications':
						include plugin_dir_path(__FILE__) . 'jobs-notifications.php';
						break;
					case 'messages':
						include plugin_dir_path(__FILE__) . 'jobs-messages.php';
						break;
					case 'analytics':
						include plugin_dir_path(__FILE__) . 'jobs-frontend-analytics.php';
						break;
					case 'settings':
						include plugin_dir_path(__FILE__) . 'jobs-settings-panel.php';
						break;
					case 'onboarding':
						include plugin_dir_path(__FILE__) . 'jobs-onboarding.php';
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
		</main>
	</div>
</div>
