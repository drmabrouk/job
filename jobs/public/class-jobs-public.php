<?php

/**
 * The public-facing functionality of the plugin.
 */
class Jobs_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'rubik-font', 'https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap', array(), null );
		wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jobs-public.css', array(), $this->version, 'all' );

		$primary = get_option('jobs_primary_color', '#1d3469');
		$secondary = get_option('jobs_secondary_color', '#15264d');
		$custom_css = ":root { --primary-color: $primary; --primary-dark: $secondary; }";
		wp_add_inline_style( $this->plugin_name, $custom_css );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', array(), '0.10.1', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jobs-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'jobs_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'jobs_search_nonce' ),
		) );
	}

	public function add_rtl_body_class( $classes ) {
		if ( is_rtl() ) {
			$classes[] = 'rtl';
		}
		return $classes;
	}

	public function add_job_single_ads( $content ) {
		if ( is_singular( 'job' ) && is_main_query() ) {
			$ad_top = get_option( 'jobs_ad_top' );
			$ad_bottom = get_option( 'jobs_ad_bottom' );
			if ( $ad_top ) {
				$content = '<div class="jobs-ad-zone jobs-ad-inline-top">' . $ad_top . '</div>' . $content;
			}
			if ( $ad_bottom ) {
				$content .= '<div class="jobs-ad-zone jobs-ad-inline-bottom">' . $ad_bottom . '</div>';
			}
		}
		return $content;
	}

	public function add_application_form( $content ) {
		if ( is_singular( 'job' ) ) {
			if ( ! is_user_logged_in() ) {
				return '<div class="jobs-msg">' . sprintf( __( 'Please <a href="%s">login</a> to apply for this job.', 'jobs' ), home_url('/jobs-auth?redirect_to=' . urlencode(get_permalink())) ) . '</div>' . $content;
			}
			$user_id = get_current_user_id();
			$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
			ob_start();
			?>
			<div id="jobs-application-container" class="jobs-application-form-wrapper" style="margin: 40px 0; padding: 30px; background: #fdfdfd; border: 1px solid #eee; border-radius: 12px;">
				<h3><?php _e( 'Apply for this Position', 'jobs' ); ?></h3>
				<form id="jobs-standard-apply-form">
					<input type="hidden" name="job_id" value="<?php the_ID(); ?>">
					<?php wp_nonce_field( 'jobs_apply_nonce', 'nonce' ); ?>
					<p>
						<label><?php _e( 'Select Document to Attach', 'jobs' ); ?></label>
						<select name="attachment_id" required style="width:100%; padding:10px; border-radius: 8px; border: 1px solid #ddd;">
							<?php if ( ! empty( $docs ) ) : foreach ( $docs as $doc ) : ?>
								<option value="<?php echo $doc['id']; ?>"><?php echo esc_html($doc['title']); ?> (<?php echo esc_html($doc['type']); ?>)</option>
							<?php endforeach; else : ?>
								<option value=""><?php _e( 'No documents found. Please upload to your Document Manager first.', 'jobs' ); ?></option>
							<?php endif; ?>
						</select>
					</p>
					<p>
						<label><?php _e( 'Cover Letter (Optional)', 'jobs' ); ?></label>
						<textarea name="cover_letter" rows="6" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px;"></textarea>
					</p>
					<input type="submit" value="<?php _e( 'Submit Application', 'jobs' ); ?>" class="jobs-button" <?php echo empty($docs) ? 'disabled' : ''; ?>>
				</form>
			</div>
			<?php
			$form = ob_get_clean();
			$content .= $form;
		}
		return $content;
	}

	public function handle_application_submission() {
		if ( ! isset( $_POST['jobs_nonce'] ) || ! wp_verify_nonce( $_POST['jobs_nonce'], 'jobs_apply_nonce' ) ) {
			wp_die( __( 'Security check failed.', 'jobs' ) );
		}
		$user_id = get_current_user_id();
		$job_id = intval($_POST['job_id']);
		$attach_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
		$cover = isset($_POST['cover_letter']) ? wp_kses_post($_POST['cover_letter']) : '';
		if ( isset($_POST['quick_apply']) ) {
			$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
			if ( ! empty($docs) ) { $attach_id = $docs[0]['id']; }
			$cover = __( 'Fast Application using profile data.', 'jobs' );
		}
		$app_id = wp_insert_post( array(
			'post_title'   => sprintf( __( 'Application: %s - %s', 'jobs' ), get_the_title($job_id), wp_get_current_user()->display_name ),
			'post_content' => $cover,
			'post_type'    => 'application',
			'post_status'  => 'publish',
			'post_author'  => $user_id,
		) );
		if ( $app_id ) {
			update_post_meta( $app_id, '_job_id', $job_id );
			update_post_meta( $app_id, '_attachment_id', $attach_id );
			$employer_id = get_post_field( 'post_author', $job_id );
			$notifs = get_user_meta( $employer_id, '_jobs_notifications', true ) ?: array();
			$notifs[] = array(
				'message' => sprintf( __( 'New application received for job: %s', 'jobs' ), get_the_title($job_id) ),
				'time'    => time(),
			);
			update_user_meta( $employer_id, '_jobs_notifications', $notifs );
			$this->log_activity( $user_id, 'Applied for job: ' . get_the_title($job_id) );
			wp_redirect( get_permalink($job_id) . '?applied=1' );
			exit;
		}
	}

	public function add_follow_employer_button( $content ) {
		if ( is_singular( 'job' ) && is_main_query() ) {
			$employer_id = get_post_field( 'post_author', get_the_ID() );
			if ( is_user_logged_in() ) {
				$following = get_user_meta( get_current_user_id(), '_jobs_followed_employers', true ) ?: array();
				$is_following = in_array( $employer_id, $following );
				$text = $is_following ? __( 'Unfollow Employer', 'jobs' ) : __( 'Follow Employer', 'jobs' );
				$class = $is_following ? 'followed' : '';
				$btn = '<div class="follow-employer-section"><button class="button follow-employer-btn ' . $class . '" data-id="' . $employer_id . '">' . $text . '</button></div>';
				$msg_btn = '<div class="message-employer-section" style="margin-top:10px;"><a href="' . home_url('/jobs-dashboard?tab=messages&view=single&action=new&to=' . $employer_id) . '" class="button">' . __( 'Message Employer', 'jobs' ) . '</a></div>';
				$content = $btn . $msg_btn . $content;
			}
		}
		return $content;
	}

	public function ajax_get_states() {
		$country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
		$locations = get_option( 'jobs_global_locations', array() );
		if ( isset( $locations[$country] ) ) {
			wp_send_json_success( $locations[$country] );
		} else {
			wp_send_json_error( __( 'No states found for this country.', 'jobs' ) );
		}
		wp_die();
	}

	public function check_job_expirations() {
		$args = array( 'post_type' => 'job', 'post_status' => 'publish', 'posts_per_page' => -1 );
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$job_id = get_the_ID();
				$expiration = get_post_meta( $job_id, '_jobs_expiration_date', true );
				if ( strtotime( $expiration ) < time() ) {
					wp_update_post( array( 'ID' => $job_id, 'post_status' => 'draft' ) );
				}
			}
			wp_reset_postdata();
		}
	}

	public function ajax_follow_employer() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error( __( 'Login required.', 'jobs' ) );
		$user_id = get_current_user_id();
		$employer_id = isset( $_POST['employer_id'] ) ? intval( $_POST['employer_id'] ) : 0;
		$following = get_user_meta( $user_id, '_jobs_followed_employers', true ) ?: array();
		if ( ! in_array( $employer_id, $following ) ) {
			$following[] = $employer_id;
			update_user_meta( $user_id, '_jobs_followed_employers', $following );
			wp_send_json_success( __( 'Employer followed.', 'jobs' ) );
		} else {
			$following = array_diff( $following, array($employer_id) );
			update_user_meta( $user_id, '_jobs_followed_employers', $following );
			wp_send_json_success( __( 'Employer unfollowed.', 'jobs' ) );
		}
		wp_die();
	}

	public function ajax_check_notifications() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error();
		$user_id = get_current_user_id();
		$notifications = get_user_meta( $user_id, '_jobs_notifications', true ) ?: array();
		$unread_count = 0;
		foreach ( $notifications as $notif ) {
			if ( ! isset( $notif['read'] ) || ! $notif['read'] ) { $unread_count++; }
		}
		wp_send_json_success( array( 'unread_count' => $unread_count ) );
		wp_die();
	}

	public function notify_followers_new_job( $post_id, $post, $update ) {
		if ( $update || $post->post_type !== 'job' || $post->post_status !== 'publish' ) return;
		$employer_id = $post->post_author;
		$users = get_users();
		foreach ( $users as $user ) {
			$following = get_user_meta( $user->ID, '_jobs_followed_employers', true ) ?: array();
			if ( in_array( $employer_id, $following ) ) {
				$notifs = get_user_meta( $user->ID, '_jobs_notifications', true ) ?: array();
				$notifs[] = array( 'message' => sprintf( __( 'New job posted by followed employer: %s', 'jobs' ), $post->post_title ), 'time' => time() );
				update_user_meta( $user->ID, '_jobs_notifications', $notifs );
			}
		}
	}

	public function ajax_jobs_search() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		$keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$country  = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
		$state    = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';
		$paged    = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
		$args = array( 'post_type' => 'job', 'post_status' => 'publish', 'posts_per_page' => 6, 'paged' => $paged, 's' => $keyword, 'meta_query' => array( 'relation' => 'AND' ), 'tax_query' => array( 'relation' => 'AND' ), 'orderby' => 'date', 'order' => 'DESC' );
		if ( ! empty( $category ) ) { $args['tax_query'][] = array( 'taxonomy' => 'job_category', 'field' => 'slug', 'terms' => $category ); }
		if ( ! empty( $type ) ) { $args['tax_query'][] = array( 'taxonomy' => 'job_type', 'field' => 'slug', 'terms' => $type ); }
		if ( ! empty( $country ) ) { $args['meta_query'][] = array( 'key' => '_job_country', 'value' => $country ); }
		if ( ! empty( $state ) ) { $args['meta_query'][] = array( 'key' => '_job_state', 'value' => $state ); }
		$query = new WP_Query( $args );
		ob_start();
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				include plugin_dir_path( __FILE__ ) . 'partials/jobs-card-template.php';
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>' . __( 'No matching jobs found', 'jobs' ) . '</p>';
		endif;
		$output = ob_get_clean();
		wp_send_json_success( array( 'html' => $output, 'total_pages' => $query->max_num_pages, 'current_page' => $paged ) );
		wp_die();
	}

	public function add_custom_nav_bar() {
		$user = wp_get_current_user();
		$is_logged_in = is_user_logged_in();
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
						<div class="lang-switcher-wrap"><?php echo $this->shortcode_language_switcher(); ?></div>
						<?php if ( $is_logged_in ) : ?>
							<div class="notif-msg-group">
								<div class="nav-icon-wrapper" id="jobs-notif-trigger" title="<?php _e('Notifications', 'jobs'); ?>">
									<i class="fas fa-bell"></i><span class="notif-dot"></span>
									<div class="nav-mini-panel" id="jobs-notif-panel">
										<div class="panel-inner-header"><?php _e('Notifications', 'jobs'); ?></div>
										<div class="panel-scrollable-content"><p class="empty-notif"><?php _e('No new updates', 'jobs'); ?></p></div>
									</div>
								</div>
							</div>
							<div class="user-profile-circle-wrap">
								<div class="profile-pic-circle-btn" id="jobs-profile-pic-btn">
									<?php echo get_avatar( $user->ID, 40, '', '', array('class' => 'circular-avatar') ); ?>
								</div>
								<div class="profile-dropdown-content" id="jobs-profile-dropdown">
									<div class="dropdown-user-header"><strong><?php echo esc_html($user->display_name); ?></strong><small><?php echo esc_html($user->user_email); ?></small></div>
									<div class="panel-divider"></div>
									<a href="#" class="profile-sub-trigger" data-panel="settings"><i class="fas fa-user-cog"></i> <?php _e('Account Settings', 'jobs'); ?></a>
									<a href="#" class="profile-sub-trigger" data-panel="activity-logs"><i class="fas fa-history"></i> <?php _e('Activity Logs', 'jobs'); ?></a>
									<div class="panel-divider"></div>
									<a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link"><i class="fas fa-power-off"></i> <?php _e('Logout', 'jobs'); ?></a>
								</div>
							</div>
						<?php endif; ?>
						<button id="jobs-apps-launcher-btn" class="apps-launcher-modern-trigger" title="<?php _e('Applications', 'jobs'); ?>"><i class="fas fa-th-large"></i></button>
					</div>
				</div>
			</div>
		</nav>
		<div id="jobs-apps-panel" class="jobs-apps-panel-overlay">
			<div class="apps-panel-card">
				<div class="apps-panel-header"><h3><?php _e( 'Applications', 'jobs' ); ?></h3><button class="close-apps-btn">&times;</button></div>
				<div class="apps-grid-content">
					<?php if ( $is_logged_in ) : ?>
						<div class="apps-launcher-grid">
							<?php
							$apps = Jobs_System::get_applications();
							foreach ( $apps as $key => $app ) :
								if ( isset( $app['hidden'] ) && $app['hidden'] ) continue;
								if ( isset( $app['capability'] ) && ! current_user_can( $app['capability'] ) && ! current_user_can( 'manage_options' ) ) continue;
								$classes = 'app-item'; $data_attr = ''; $href = '#';
								if ( isset( $app['panel'] ) ) { $classes .= ' sub-trigger'; $data_attr = 'data-panel="' . esc_attr( $app['panel'] ) . '"'; }
								if ( isset( $app['link'] ) && $app['link'] ) {
									if ( $key === 'public-profile' ) { $href = home_url( '/job-seeker/' . $user->user_nicename ); }
									elseif ( isset( $app['url'] ) ) { $href = home_url( $app['url'] ); }
								}
								?>
								<a href="<?php echo esc_url( $href ); ?>" class="<?php echo esc_attr( $classes ); ?>" <?php echo $data_attr; ?>>
									<div class="app-icon" style="background: <?php echo esc_attr($app['bg']); ?>; color: <?php echo esc_attr($app['color']); ?>;"><i class="<?php echo esc_attr($app['icon']); ?>"></i></div>
									<span><?php echo esc_html( $app['label'] ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
						<div id="apps-sub-panels-wrapper">
							<?php global $jobs_modules; foreach ( $apps as $key => $app ) : if ( ! isset( $app['panel'] ) ) continue; ?>
								<div class="apps-sub-panel" id="panel-<?php echo esc_attr( $app['panel'] ); ?>">
									<div class="sub-panel-header"><button class="back-btn"><i class="fas fa-chevron-left"></i></button><h4><?php echo esc_html( $app['label'] ); ?></h4></div>
									<div class="sub-panel-body">
										<?php if ( isset( $jobs_modules[$key] ) ) { $jobs_modules[$key]->render(); } else { printf( __( 'Module %s not initialized.', 'jobs' ), esc_html( $key ) ); } ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="apps-guest-welcome"><p><?php _e( 'Experience the full potential of Jobedia.', 'jobs' ); ?></p>
							<div class="apps-auth-actions">
								<a href="<?php echo home_url('/jobs-auth?auth_action=login'); ?>" class="btn-app-auth primary"><?php _e( 'Login', 'jobs' ); ?></a>
								<a href="<?php echo home_url('/jobs-auth?auth_action=register'); ?>" class="btn-app-auth outline"><?php _e( 'Register', 'jobs' ); ?></a>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="jobs-global-modal" class="jobs-modal-overlay">
			<div class="jobs-modal-container">
				<div class="jobs-modal-header"><h3 id="jobs-modal-title"><?php _e('Job Portal', 'jobs'); ?></h3><button class="jobs-modal-close-btn">&times;</button></div>
				<div class="jobs-modal-body" id="jobs-modal-body"></div>
			</div>
		</div>
		<?php
	}

	public function job_single_template( $template ) {
		if ( is_singular( 'job' ) ) {
			$new_template = plugin_dir_path( __FILE__ ) . 'partials/jobs-single-listing.php';
			if ( file_exists( $new_template ) ) { return $new_template; }
		}
		return $template;
	}

	public function handle_dashboard_redirection() {
		if ( is_page( 'jobs-dashboard' ) && ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( home_url( '/jobs-dashboard' ) ) ); exit;
		}
		if ( get_query_var( 'job_seeker_profile' ) ) {
			include plugin_dir_path( __FILE__ ) . 'partials/jobs-public-profile.php'; exit;
		}
	}

	public function add_rewrite_rules() {
		add_rewrite_rule( '^job-seeker/([^/]+)/?', 'index.php?job_seeker_profile=$matches[1]', 'top' );
		add_filter( 'query_vars', function( $vars ) { $vars[] = 'job_seeker_profile'; return $vars; } );
	}

	public function shortcode_jobs_login( $atts ) {
		if ( is_user_logged_in() ) { return '<p class="jobs-msg">' . __( 'You are already logged in.', 'jobs' ) . '</p>'; }
		$redirect = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>
		<div class="jobs-auth-form jobs-login-form">
			<h2><?php _e( 'Login to Your Account', 'jobs' ); ?></h2>
			<?php wp_login_form( array( 'redirect' => $redirect, 'label_username' => __( 'Username or Email Address', 'jobs' ), 'label_password' => __( 'Password', 'jobs' ), 'label_remember' => __( 'Remember Me', 'jobs' ), 'label_log_in' => __( 'Log In', 'jobs' ), 'remember' => true, 'value_remember' => true ) ); ?>
			<p class="jobs-form-footer"><?php _e( "Don't have an account?", 'jobs' ); ?> <a href="#"><?php _e( 'Register here', 'jobs' ); ?></a></p>
		</div>
		<?php return ob_get_clean();
	}

	public function shortcode_jobs_dashboard() {
		if ( ! is_user_logged_in() ) { return $this->shortcode_jobs_login( array() ); }
		return '<div class="jobs-msg info">' . __('The traditional dashboard has been replaced by the Apps Launcher in the top navigation bar. Please use the grid icon to access your applications and profile.', 'jobs') . '</div>';
	}

	public function shortcode_jobs_settings() {
		if ( ! is_user_logged_in() ) { return $this->shortcode_jobs_login( array() ); }
		ob_start(); include plugin_dir_path( __FILE__ ) . 'partials/jobs-settings-panel.php'; return ob_get_clean();
	}

	public function shortcode_jobs_register( $atts ) {
		if ( is_user_logged_in() ) { return '<p class="jobs-msg">' . __( 'You are already registered and logged in.', 'jobs' ) . '</p>'; }
		ob_start();
		?>
		<div class="jobs-auth-form jobs-register-form">
			<h2><?php _e( 'Create an Account', 'jobs' ); ?></h2>
			<form id="jobs-registration-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
				<input type="hidden" name="action" value="jobs_register_user">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>">
				<?php wp_nonce_field( 'jobs_register_nonce', 'jobs_nonce' ); ?>
				<p><label for="user_login"><?php _e( 'Username', 'jobs' ); ?></label><input type="text" name="user_login" id="user_login" class="input" required></p>
				<p><label for="user_email"><?php _e( 'Email Address', 'jobs' ); ?></label><input type="email" name="user_email" id="user_email" class="input" required></p>
				<p><label for="user_pass"><?php _e( 'Password', 'jobs' ); ?></label><input type="password" name="user_pass" id="user_pass" class="input" required></p>
				<p class="jobs-submit"><input type="submit" value="<?php _e( 'Register Now', 'jobs' ); ?>" class="button button-primary button-large"></p>
			</form>
			<p class="jobs-form-footer"><?php _e( 'Already have an account?', 'jobs' ); ?> <a href="#"><?php _e( 'Log in here', 'jobs' ); ?></a></p>
		</div>
		<?php return ob_get_clean();
	}

	public function shortcode_jobs_search_engine( $atts ) {
		ob_start(); include plugin_dir_path( __FILE__ ) . 'partials/jobs-public-display.php'; return ob_get_clean();
	}

	public function log_login_activity( $user_login, $user ) { $this->log_activity( $user->ID, 'User logged in' ); }

	public function log_activity( $user_id, $action ) {
		$logs = get_user_meta( $user_id, '_jobs_activity_log', true ) ?: array();
		$logs[] = array( 'action' => $action, 'time' => time(), 'ip' => $_SERVER['REMOTE_ADDR'] );
		update_user_meta( $user_id, '_jobs_activity_log', array_slice( $logs, -50 ) );
	}

	public function shortcode_language_switcher() {
		$current_lang = get_locale();
		ob_start();
		?>
		<div class="jobs-lang-switcher-modern">
			<a href="?lang=en" class="<?php echo ($current_lang == 'en_US') ? 'active' : ''; ?>" title="English"><span class="lang-flag">🇺🇸</span> <span class="lang-label">EN</span></a>
			<a href="?lang=ar" class="<?php echo ($current_lang == 'ar') ? 'active' : ''; ?>" title="العربية"><span class="lang-flag">🇸🇦</span> <span class="lang-label">AR</span></a>
		</div>
		<?php return ob_get_clean();
	}

	public function ajax_login() {
		check_ajax_referer( 'jobs_auth_nonce', 'auth_nonce' );
		$info = array(); $info['user_login'] = sanitize_text_field($_POST['user_login']); $info['user_password'] = $_POST['user_pass']; $info['remember'] = isset($_POST['rememberme']);
		$user_signon = wp_signon( $info, is_ssl() );
		if ( is_wp_error( $user_signon ) ) { wp_send_json_error( $user_signon->get_error_message() ); }
		else { $this->log_activity( $user_signon->ID, 'User logged in via AJAX' ); $redirect = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url('/jobs-dashboard'); wp_send_json_success( array( 'message' => __( 'Login successful! Redirecting...', 'jobs' ), 'redirect' => $redirect ) ); }
	}

	public function ajax_register() {
		check_ajax_referer( 'jobs_auth_nonce', 'auth_nonce' );
		$email = sanitize_email($_POST['user_email']); $first_name = sanitize_text_field($_POST['first_name']); $last_name = sanitize_text_field($_POST['last_name']); $name = $first_name . ' ' . $last_name; $pass = $_POST['user_pass']; $role = sanitize_text_field($_POST['user_role']);
		if ( ! is_email($email) ) { wp_send_json_error( __( 'Invalid email address.', 'jobs' ) ); }
		if ( email_exists($email) ) { wp_send_json_error( __( 'Email already registered.', 'jobs' ) ); }
		if ( strlen($pass) < 8 ) { wp_send_json_error( __( 'Password must be at least 8 characters.', 'jobs' ) ); }
		$username = strstr($email, '@', true) . rand(100,999);
		$user_id = wp_create_user( $username, $pass, $email );
		if ( is_wp_error($user_id) ) { wp_send_json_error( $user_id->get_error_message() ); }
		$user = new WP_User($user_id); $user->set_role($role);
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $name, 'first_name' => $first_name, 'last_name' => $last_name ) );
		wp_set_auth_cookie($user_id); $this->log_activity( $user_id, 'Account created' );
		wp_send_json_success( __( 'Account created successfully!', 'jobs' ) );
	}

	public function ajax_submit_application() {
		check_ajax_referer( 'jobs_apply_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) { wp_send_json_error( __( 'You must be logged in to apply.', 'jobs' ) ); }
		$user_id = get_current_user_id(); $job_id = intval($_POST['job_id']); $attach_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0; $cover = isset($_POST['cover_letter']) ? wp_kses_post($_POST['cover_letter']) : '';
		if ( isset($_POST['quick_apply']) ) {
			$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
			if ( ! empty($docs) ) { $attach_id = $docs[0]['id']; }
			$cover = __( 'Fast Application using profile data.', 'jobs' );
		}
		$app_id = wp_insert_post( array( 'post_title' => sprintf( __( 'Application: %s - %s', 'jobs' ), get_the_title($job_id), wp_get_current_user()->display_name ), 'post_content' => $cover, 'post_type' => 'application', 'post_status' => 'publish', 'post_author' => $user_id ) );
		if ( $app_id ) {
			update_post_meta( $app_id, '_job_id', $job_id ); update_post_meta( $app_id, '_attachment_id', $attach_id );
			$employer_id = get_post_field( 'post_author', $job_id ); $notifs = get_user_meta( $employer_id, '_jobs_notifications', true ) ?: array();
			$notifs[] = array( 'message' => sprintf( __( 'New application received for job: %s', 'jobs' ), get_the_title($job_id) ), 'time' => time() );
			update_user_meta( $employer_id, '_jobs_notifications', $notifs );
			$this->log_activity( $user_id, 'Applied for job: ' . get_the_title($job_id) );
			wp_send_json_success( __( 'Application submitted successfully!', 'jobs' ) );
		}
		wp_send_json_error( __( 'Failed to submit application.', 'jobs' ) );
	}

	public function handle_user_registration() {}
	public function hide_wp_for_non_admins() { if ( ! current_user_can( 'manage_options' ) ) { show_admin_bar( false ); } }
	public function restrict_wp_admin_access() { if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! current_user_can( 'manage_options' ) ) { wp_redirect( home_url() ); exit; } }
}
