	/**
	 * Add custom transparent navigation bar with Unified Applications Menu.
	 * Global Website Navigation Bar - Refined Final
	 */
	public function add_custom_nav_bar() {
		$user = wp_get_current_user();
		$is_logged_in = is_user_logged_in();
		$is_rtl = is_rtl();
		$is_home = is_front_page() || is_home();

		?>
		<nav class="jobs-global-top-nav-refined">
			<div class="nav-content-container">
				<div class="nav-side-start">
					<?php if ( ! $is_home ) : ?>
						<a href="<?php echo home_url(); ?>" class="global-logo-refined">
							<?php if ( $logo_id = get_option('jobs_logo_id') ) : ?>
								<img src="<?php echo wp_get_attachment_url($logo_id); ?>" alt="Logo">
							<?php else : ?>
								<span>Jobedia</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>

				<div class="nav-side-end">
					<div class="top-nav-actions-group">
						<div class="lang-switcher-wrap">
							<?php echo $this->shortcode_language_switcher(); ?>
						</div>

						<?php if ( $is_logged_in ) : ?>
							<div class="notif-msg-group">
								<div class="nav-icon-wrapper" id="jobs-notif-trigger" title="<?php _e('Notifications', 'jobs'); ?>">
									<i class="fas fa-bell"></i>
									<span class="notif-dot"></span>
									<div class="nav-mini-panel" id="jobs-notif-panel">
										<div class="panel-inner-header"><?php _e('Notifications', 'jobs'); ?></div>
										<div class="panel-scrollable-content">
											<p class="empty-notif"><?php _e('No new updates', 'jobs'); ?></p>
										</div>
									</div>
								</div>
								<div class="nav-icon-wrapper" id="jobs-msg-trigger" title="<?php _e('Messages', 'jobs'); ?>">
									<i class="fas fa-comment-dots"></i>
									<div class="nav-mini-panel" id="jobs-msg-panel">
										<div class="panel-inner-header"><?php _e('Messages', 'jobs'); ?></div>
										<div class="panel-scrollable-content">
											<p class="empty-notif"><?php _e('No new messages', 'jobs'); ?></p>
										</div>
										<a href="<?php echo home_url('/jobs-dashboard?tab=messages'); ?>" class="view-all-link"><?php _e('View All', 'jobs'); ?></a>
									</div>
								</div>
							</div>

							<div class="user-profile-circle-wrap">
								<div class="profile-pic-circle-btn" id="jobs-profile-pic-btn">
									<?php echo get_avatar( $user->ID, 40, '', '', array('class' => 'circular-avatar') ); ?>
								</div>
								<div class="profile-dropdown-content" id="jobs-profile-dropdown">
									<a href="<?php echo home_url('/jobs-dashboard?tab=settings'); ?>"><i class="fas fa-user-circle"></i> <?php _e('Account Settings', 'jobs'); ?></a>
									<a href="<?php echo home_url('/jobs-dashboard?tab=analytics'); ?>"><i class="fas fa-history"></i> <?php _e('Activity Logs', 'jobs'); ?></a>
									<div class="panel-divider"></div>
									<a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link"><i class="fas fa-power-off"></i> <?php _e('Logout', 'jobs'); ?></a>
								</div>
							</div>
						<?php endif; ?>

						<button id="jobs-apps-launcher-btn" class="apps-launcher-modern-trigger" title="<?php _e('Applications', 'jobs'); ?>">
							<i class="fas fa-th-large"></i>
						</button>
					</div>
				</div>
			</div>
		</nav>

		<!-- Apps Launcher Panel (Global Account Controls) -->
		<div id="jobs-apps-panel" class="jobs-apps-panel-overlay">
			<div class="apps-panel-card">
				<div class="apps-panel-header">
					<h3><?php _e( 'Applications', 'jobs' ); ?></h3>
					<button class="close-apps-btn">&times;</button>
				</div>

				<div class="apps-grid-content">
					<?php if ( $is_logged_in ) :
						$role_names = get_option( 'jobs_role_names' );
						$roles = ( array ) $user->roles;
						$role_id = $roles[0];
						$is_admin = current_user_can('manage_options');
						$is_employer = ($role_id === 'employer' || $role_id === 'job_reviewer' || $is_admin);
						$is_seeker = ($role_id === 'job_seeker' || $is_admin);
					?>
						<div class="apps-launcher-grid">
							<?php if ( $is_employer ) : ?>
								<div class="app-item" id="app-post-job-trigger">
									<div class="app-icon" style="background: #e0f2fe; color: #0369a1;"><i class="fas fa-plus"></i></div>
									<span><?php _e( 'Post a Job', 'jobs' ); ?></span>
								</div>
								<a href="<?php echo home_url('/jobs-dashboard?tab=manage-jobs'); ?>" class="app-item">
									<div class="app-icon" style="background: #f0fdf4; color: #166534;"><i class="fas fa-briefcase"></i></div>
									<span><?php _e( 'Job History', 'jobs' ); ?></span>
								</a>
								<a href="<?php echo home_url('/jobs-dashboard?tab=settings'); ?>" class="app-item">
									<div class="app-icon" style="background: #fef3c7; color: #92400e;"><i class="fas fa-sliders-h"></i></div>
									<span><?php _e( 'Posting Settings', 'jobs' ); ?></span>
								</a>
								<a href="<?php echo home_url('/jobs-dashboard?tab=manage-jobs&view=drafts'); ?>" class="app-item">
									<div class="app-icon" style="background: #f3f4f6; color: #374151;"><i class="fas fa-file-signature"></i></div>
									<span><?php _e( 'Draft Manager', 'jobs' ); ?></span>
								</a>
								<a href="<?php echo home_url('/jobs-dashboard?tab=my-applications'); ?>" class="app-item">
									<div class="app-icon" style="background: #fee2e2; color: #991b1b;"><i class="fas fa-users"></i></div>
									<span><?php _e( 'Application Records', 'jobs' ); ?></span>
								</a>
							<?php endif; ?>

							<?php if ( $is_seeker ) : ?>
								<a href="<?php echo home_url('/jobs-dashboard?tab=my-applications'); ?>" class="app-item">
									<div class="app-icon" style="background: #f5f3ff; color: #5b21b6;"><i class="fas fa-paper-plane"></i></div>
									<span><?php _e( 'Submitted Apps', 'jobs' ); ?></span>
								</a>
								<a href="<?php echo home_url('/job-seeker/' . $user->user_nicename); ?>" class="app-item">
									<div class="app-icon" style="background: #ecfeff; color: #155e75;"><i class="fas fa-user-circle"></i></div>
									<span><?php _e( 'Public Profile', 'jobs' ); ?></span>
								</a>
							<?php endif; ?>

							<a href="<?php echo home_url('/jobs-dashboard?tab=settings'); ?>" class="app-item">
								<div class="app-icon" style="background: #f8fafc; color: #475569;"><i class="fas fa-cog"></i></div>
								<span><?php _e( 'Account Settings', 'jobs' ); ?></span>
							</a>
						</div>

						<!-- Inline Job Posting Dropdown Panel -->
						<div id="inline-job-post-panel" class="inline-apps-sub-panel">
							<div class="sub-panel-header">
								<button class="back-to-apps"><i class="fas fa-arrow-left"></i></button>
								<h4><?php _e('Post a Job', 'jobs'); ?></h4>
							</div>
							<div class="sub-panel-body">
								<?php include plugin_dir_path(__FILE__) . 'jobs-post-inline.php'; ?>
							</div>
						</div>

					<?php else : ?>
						<div class="apps-guest-welcome">
							<p><?php _e( 'Experience the full potential of Jobedia.', 'jobs' ); ?></p>
							<div class="apps-auth-actions">
								<a href="<?php echo home_url('/jobs-auth?auth_action=login'); ?>" class="btn-app-auth primary"><?php _e( 'Login', 'jobs' ); ?></a>
								<a href="<?php echo home_url('/jobs-auth?auth_action=register'); ?>" class="btn-app-auth outline"><?php _e( 'Register', 'jobs' ); ?></a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
