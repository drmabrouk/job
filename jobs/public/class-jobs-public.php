<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Jobs
 * @subpackage Jobs/public
 * @author     jobedia <info@jobedia.com>
 */
class Jobs_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'rubik-font', 'https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap', array(), null );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jobs-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jobs-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'jobs_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'jobs_search_nonce' ),
		) );

	}

	/**
	 * Add RTL class to body if needed.
	 *
	 * @since    1.0.0
	 */
	public function add_rtl_body_class( $classes ) {
		if ( is_rtl() ) {
			$classes[] = 'rtl';
		}
		return $classes;
	}

	/**
	 * Add SEO meta tags to the head.
	 *
	 * @since    1.0.0
	 */
	public function add_seo_meta_tags() {
		if ( is_singular( 'job' ) ) {
			global $post;
			$description = wp_trim_words( $post->post_excerpt, 25 );
			if ( empty( $description ) ) {
				$description = wp_trim_words( $post->post_content, 25 );
			}
			echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
			echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n";
			echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
			echo '<meta property="og:type" content="article" />' . "\n";
			echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />' . "\n";

			// Schema.org JobPosting JSON-LD
			$schema = array(
				'@context' => 'https://schema.org/',
				'@type'    => 'JobPosting',
				'title'    => get_the_title(),
				'description' => wp_kses_post( $post->post_content ),
				'datePosted'  => get_the_date( 'c' ),
				'validThrough' => get_post_meta( get_the_ID(), '_jobs_expiration_date', true ),
				'hiringOrganization' => array(
					'@type' => 'Organization',
					'name'  => get_the_author_meta( 'display_name' ),
					'sameAs' => home_url(),
				),
				'jobLocation' => array(
					'@type' => 'Place',
					'address' => array(
						'@type' => 'PostalAddress',
						'addressLocality' => get_post_meta( get_the_ID(), '_job_state', true ),
						'addressCountry' => get_post_meta( get_the_ID(), '_job_country', true ),
					),
				),
			);
			echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
		}
	}

	/**
	 * Add ads to single job content.
	 *
	 * @since    1.0.0
	 */
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

	/**
	 * Add follow employer button to single job content.
	 *
	 * @since    1.0.0
	 */
	public function add_follow_employer_button( $content ) {
		if ( is_singular( 'job' ) && is_main_query() ) {
			$employer_id = get_post_field( 'post_author', get_the_ID() );
			if ( is_user_logged_in() ) {
				$following = get_user_meta( get_current_user_id(), '_jobs_followed_employers', true ) ?: array();
				$is_following = in_array( $employer_id, $following );
				$text = $is_following ? __( 'Unfollow Employer', 'jobs' ) : __( 'Follow Employer', 'jobs' );
				$class = $is_following ? 'followed' : '';

				$btn = '<div class="follow-employer-section"><button class="button follow-employer-btn ' . $class . '" data-id="' . $employer_id . '">' . $text . '</button></div>';
				$content = $btn . $content;
			}
		}
		return $content;
	}

	/**
	 * AJAX handler to get states by country.
	 *
	 * @since    1.0.0
	 */
	public function ajax_get_states() {
		$country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';

		$locations = array(
			'USA' => array( 'California', 'New York', 'Texas', 'Florida' ),
			'UK' => array( 'London', 'Manchester', 'Birmingham', 'Leeds' ),
			'UAE' => array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman' ),
			'Egypt' => array( 'Cairo', 'Alexandria', 'Giza', 'Luxor' ),
			'Saudi Arabia' => array( 'Riyadh', 'Jeddah', 'Mecca', 'Medina' ),
		);

		if ( isset( $locations[$country] ) ) {
			wp_send_json_success( $locations[$country] );
		} else {
			wp_send_json_error( __( 'No states found for this country.', 'jobs' ) );
		}
		wp_die();
	}

	/**
	 * Daily cron task to expire jobs.
	 *
	 * @since    1.0.0
	 */
	public function check_job_expirations() {
		$args = array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$job_id = get_the_ID();
				$expiration = get_post_meta( $job_id, '_jobs_expiration_date', true );

				if ( ! $expiration ) {
					// Set default if not exists: 50 days from publish
					$publish_date = get_the_date( 'Y-m-d H:i:s', $job_id );
					$expiration = date( 'Y-m-d H:i:s', strtotime( $publish_date . ' + 50 days' ) );
					update_post_meta( $job_id, '_jobs_expiration_date', $expiration );
				}

				if ( strtotime( $expiration ) < time() ) {
					// Expire job: move to draft/archive
					wp_update_post( array(
						'ID'          => $job_id,
						'post_status' => 'draft', // Personal archive
					) );

					// Notify employer
					$author_id = get_post_field( 'post_author', $job_id );
					$notifs = get_user_meta( $author_id, '_jobs_notifications', true ) ?: array();
					$notifs[] = array(
						'message' => sprintf( __( 'Your job listing "%s" has expired and moved to your archive.', 'jobs' ), get_the_title($job_id) ),
						'time'    => time(),
					);
					update_user_meta( $author_id, '_jobs_notifications', $notifs );
				}
			}
			wp_reset_postdata();
		}
	}

	/**
	 * AJAX handler to reactivate a job.
	 *
	 * @since    1.0.0
	 */
	public function ajax_reactivate_job() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;

		if ( $job_id && get_post_field( 'post_author', $job_id ) == get_current_user_id() ) {
			wp_update_post( array(
				'ID'          => $job_id,
				'post_status' => 'publish',
			) );
			// Reset expiration to 50 days from now
			$new_expiration = date( 'Y-m-d H:i:s', strtotime( '+ 50 days' ) );
			update_post_meta( $job_id, '_jobs_expiration_date', $new_expiration );
			wp_send_json_success( __( 'Job reactivated successfully.', 'jobs' ) );
		}
		wp_send_json_error( __( 'Failed to reactivate job.', 'jobs' ) );
	}

	/**
	 * AJAX handler to extend a job.
	 *
	 * @since    1.0.0
	 */
	public function ajax_extend_job() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;

		if ( $job_id && get_post_field( 'post_author', $job_id ) == get_current_user_id() ) {
			$current_expiration = get_post_meta( $job_id, '_jobs_expiration_date', true );
			$max_expiration = date( 'Y-m-d H:i:s', strtotime( get_the_date( 'Y-m-d H:i:s', $job_id ) . ' + 1 year' ) );

			$new_expiration = date( 'Y-m-d H:i:s', strtotime( $current_expiration . ' + 30 days' ) );

			if ( strtotime( $new_expiration ) > strtotime( $max_expiration ) ) {
				$new_expiration = $max_expiration;
			}

			update_post_meta( $job_id, '_jobs_expiration_date', $new_expiration );
			wp_send_json_success( sprintf( __( 'Job extended until %s.', 'jobs' ), $new_expiration ) );
		}
		wp_send_json_error( __( 'Failed to extend job.', 'jobs' ) );
	}

	/**
	 * AJAX handler to save a job.
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_job() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error( __( 'Login required.', 'jobs' ) );

		$user_id = get_current_user_id();
		$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
		$saved = get_user_meta( $user_id, '_jobs_saved_jobs', true ) ?: array();

		if ( ! in_array( $job_id, $saved ) ) {
			$saved[] = $job_id;
			update_user_meta( $user_id, '_jobs_saved_jobs', $saved );
			$this->log_activity( $user_id, 'Saved job: ' . get_the_title($job_id) );
			wp_send_json_success( __( 'Job saved.', 'jobs' ) );
		} else {
			$saved = array_diff( $saved, array($job_id) );
			update_user_meta( $user_id, '_jobs_saved_jobs', $saved );
			wp_send_json_success( __( 'Job removed from saved.', 'jobs' ) );
		}
		wp_die();
	}

	/**
	 * AJAX handler to follow an employer.
	 *
	 * @since    1.0.0
	 */
	/**
	 * AJAX handler to check for new notifications.
	 *
	 * @since    1.0.0
	 */
	public function ajax_check_notifications() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error();

		$user_id = get_current_user_id();
		$notifications = get_user_meta( $user_id, '_jobs_notifications', true ) ?: array();

		$unread_count = 0;
		foreach ( $notifications as $notif ) {
			if ( ! isset( $notif['read'] ) || ! $notif['read'] ) {
				$unread_count++;
			}
		}

		wp_send_json_success( array( 'unread_count' => $unread_count ) );
		wp_die();
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
			$emp = get_userdata($employer_id);
			$this->log_activity( $user_id, 'Followed employer: ' . ($emp ? $emp->display_name : 'ID '.$employer_id) );
			wp_send_json_success( __( 'Employer followed.', 'jobs' ) );
		} else {
			$following = array_diff( $following, array($employer_id) );
			update_user_meta( $user_id, '_jobs_followed_employers', $following );
			wp_send_json_success( __( 'Employer unfollowed.', 'jobs' ) );
		}
		wp_die();
	}

	/**
	 * Notify followers when a new job is posted.
	 *
	 * @since    1.0.0
	 */
	public function notify_followers_new_job( $post_id, $post, $update ) {
		if ( $update || $post->post_type !== 'job' || $post->post_status !== 'publish' ) return;

		$employer_id = $post->post_author;
		$users = get_users(); // Simple way for this task, usually should be optimized
		foreach ( $users as $user ) {
			$following = get_user_meta( $user->ID, '_jobs_followed_employers', true ) ?: array();
			if ( in_array( $employer_id, $following ) ) {
				$notifs = get_user_meta( $user->ID, '_jobs_notifications', true ) ?: array();
				$notifs[] = array(
					'message' => sprintf( __( 'New job posted by followed employer: %s', 'jobs' ), $post->post_title ),
					'time'    => time(),
				);
				update_user_meta( $user->ID, '_jobs_notifications', $notifs );
			}
		}
	}

	/**
	 * AJAX handler for job search.
	 *
	 * @since    1.0.0
	 */
	public function ajax_jobs_search() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );

		$keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '';
		$category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
		$country  = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
		$state    = isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : '';

		// Track user history for recommendations
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$history = get_user_meta( $user_id, '_jobs_search_history', true ) ?: array();
			if ( ! empty( $category ) ) {
				$history[] = array( 'type' => 'category', 'value' => $category, 'time' => time() );
			}
			if ( ! empty( $keyword ) ) {
				$history[] = array( 'type' => 'keyword', 'value' => $keyword, 'time' => time() );
			}
			// Keep only last 20
			$history = array_slice( $history, -20 );
			update_user_meta( $user_id, '_jobs_search_history', $history );
		}

		$args = array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			's'              => $keyword,
			'meta_query'     => array(),
			'tax_query'      => array(),
		);

		if ( ! empty( $category ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_category',
				'field'    => 'slug',
				'terms'    => $category,
			);
		}

		if ( ! empty( $type ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_type',
				'field'    => 'slug',
				'terms'    => $type,
			);
		}

		if ( ! empty( $country ) ) {
			$args['meta_query'][] = array(
				'key'   => '_job_country',
				'value' => $country,
			);
		}

		if ( ! empty( $state ) ) {
			$args['meta_query'][] = array(
				'key'   => '_job_state',
				'value' => $state,
			);
		}

		$query = new WP_Query( $args );

		ob_start();

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				include plugin_dir_path( __FILE__ ) . 'partials/jobs-card-template.php';
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>' . __( 'No jobs found.', 'jobs' ) . '</p>';
		endif;

		$output = ob_get_clean();

		// Get category specific ad if available
		$category_ad = '';
		if ( ! empty( $category ) ) {
			$term = get_term_by( 'slug', $category, 'job_category' );
			if ( $term ) {
				$cat_ads = get_option( 'jobs_category_ads', array() );
				if ( isset( $cat_ads[$term->term_id] ) && ! empty( $cat_ads[$term->term_id] ) ) {
					$category_ad = $cat_ads[$term->term_id];
				}
			}
		}

		wp_send_json_success( array(
			'html' => $output,
			'category_ad' => $category_ad
		) );
		wp_die();
	}

	/**
	 * Add custom transparent navigation bar for logged-in users.
	 *
	 * @since    1.0.0
	 */
	public function add_custom_nav_bar() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();
		$role_names = get_option( 'jobs_role_names' );
		$roles = ( array ) $user->roles;
		$role_id = $roles[0];
		$display_role = isset( $role_names[$role_id] ) ? $role_names[$role_id] : ucfirst( str_replace( '_', ' ', $role_id ) );

		?>
		<nav class="jobs-top-nav">
			<div class="jobs-container">
				<div class="jobs-nav-content">
					<div class="jobs-user-welcome">
						<?php printf( __( 'Welcome, %s (%s)', 'jobs' ), '<strong>' . $user->display_name . '</strong>', $display_role ); ?>
					</div>
					<div class="jobs-nav-links">
						<a href="<?php echo home_url( '/jobs-dashboard' ); ?>"><?php _e( 'Dashboard', 'jobs' ); ?></a>
						<a href="<?php echo home_url( '/jobs-settings' ); ?>"><?php _e( 'Settings', 'jobs' ); ?></a>
						<a href="<?php echo home_url( '/jobs' ); ?>"><?php _e( 'Browse Jobs', 'jobs' ); ?></a>
						<a href="<?php echo wp_logout_url( home_url() ); ?>"><?php _e( 'Logout', 'jobs' ); ?></a>
					</div>
				</div>
			</div>
		</nav>
		<?php
	}

	/**
	 * Handle dashboard redirection based on role.
	 *
	 * @since    1.0.0
	 */
	public function handle_dashboard_redirection() {
		if ( is_page( 'jobs-dashboard' ) && ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( home_url( '/jobs-dashboard' ) ) );
			exit;
		}

		if ( get_query_var( 'job_seeker_profile' ) ) {
			include plugin_dir_path( __FILE__ ) . 'partials/jobs-public-profile.php';
			exit;
		}
	}

	/**
	 * Add custom rewrite rules.
	 *
	 * @since    1.0.0
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule( '^job-seeker/([^/]+)/?', 'index.php?job_seeker_profile=$matches[1]', 'top' );
		add_filter( 'query_vars', function( $vars ) {
			$vars[] = 'job_seeker_profile';
			return $vars;
		} );
	}

	/**
	 * Login shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_jobs_login( $atts ) {
		if ( is_user_logged_in() ) {
			return '<p class="jobs-msg">' . __( 'You are already logged in.', 'jobs' ) . '</p>';
		}

		$redirect = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		ob_start();
		?>
		<div class="jobs-auth-form jobs-login-form">
			<h2><?php _e( 'Login to Your Account', 'jobs' ); ?></h2>
			<?php
			wp_login_form( array(
				'redirect' => $redirect,
				'label_username' => __( 'Username or Email Address', 'jobs' ),
				'label_password' => __( 'Password', 'jobs' ),
				'label_remember' => __( 'Remember Me', 'jobs' ),
				'label_log_in'   => __( 'Log In', 'jobs' ),
				'remember'       => true,
				'value_remember' => true,
			) );
			?>
			<p class="jobs-form-footer">
				<?php _e( "Don't have an account?", 'jobs' ); ?> <a href="#"><?php _e( 'Register here', 'jobs' ); ?></a>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Dashboard shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_jobs_dashboard() {
		if ( ! is_user_logged_in() ) {
			return $this->shortcode_jobs_login( array() );
		}

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/jobs-account-wrapper.php';
		return ob_get_clean();
	}

	/**
	 * Settings shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_jobs_settings() {
		if ( ! is_user_logged_in() ) {
			return $this->shortcode_jobs_login( array() );
		}

		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/jobs-settings-panel.php';
		return ob_get_clean();
	}

	/**
	 * Registration shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_jobs_register( $atts ) {
		if ( is_user_logged_in() ) {
			return '<p class="jobs-msg">' . __( 'You are already registered and logged in.', 'jobs' ) . '</p>';
		}

		ob_start();
		?>
		<div class="jobs-auth-form jobs-register-form">
			<h2><?php _e( 'Create an Account', 'jobs' ); ?></h2>
			<form id="jobs-registration-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
				<input type="hidden" name="action" value="jobs_register_user">
				<input type="hidden" name="redirect_to" value="<?php echo esc_url( ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); ?>">
				<?php wp_nonce_field( 'jobs_register_nonce', 'jobs_nonce' ); ?>
				<p>
					<label for="user_login"><?php _e( 'Username', 'jobs' ); ?></label>
					<input type="text" name="user_login" id="user_login" class="input" required>
				</p>
				<p>
					<label for="user_email"><?php _e( 'Email Address', 'jobs' ); ?></label>
					<input type="email" name="user_email" id="user_email" class="input" required>
				</p>
				<p>
					<label for="user_pass"><?php _e( 'Password', 'jobs' ); ?></label>
					<input type="password" name="user_pass" id="user_pass" class="input" required>
				</p>
				<p class="jobs-submit">
					<input type="submit" value="<?php _e( 'Register Now', 'jobs' ); ?>" class="button button-primary button-large">
				</p>
			</form>
			<p class="jobs-form-footer">
				<?php _e( 'Already have an account?', 'jobs' ); ?> <a href="#"><?php _e( 'Log in here', 'jobs' ); ?></a>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Search engine shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_jobs_search_engine( $atts ) {
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/jobs-public-display.php';
		return ob_get_clean();
	}

	/**
	 * Log login activity.
	 *
	 * @since    1.0.0
	 */
	public function log_login_activity( $user_login, $user ) {
		$this->log_activity( $user->ID, 'User logged in' );
	}

	/**
	 * Log user activity.
	 *
	 * @since    1.0.0
	 */
	public function log_activity( $user_id, $action ) {
		$logs = get_user_meta( $user_id, '_jobs_activity_log', true ) ?: array();
		$logs[] = array(
			'action' => $action,
			'time'   => time(),
			'ip'     => $_SERVER['REMOTE_ADDR']
		);
		update_user_meta( $user_id, '_jobs_activity_log', array_slice( $logs, -50 ) );
	}

	/**
	 * Language switcher shortcode.
	 *
	 * @since    1.0.0
	 */
	public function shortcode_language_switcher() {
		$current_lang = get_locale();
		ob_start();
		?>
		<div class="jobs-lang-switcher">
			<a href="?lang=en" class="<?php echo ($current_lang == 'en_US') ? 'active' : ''; ?>">English</a>
			<span class="sep">|</span>
			<a href="?lang=ar" class="<?php echo ($current_lang == 'ar') ? 'active' : ''; ?>">العربية</a>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Register user from shortcode.
	 *
	 * @since    1.0.0
	 */
	public function handle_user_registration() {
		if ( ! isset( $_POST['jobs_nonce'] ) || ! wp_verify_nonce( $_POST['jobs_nonce'], 'jobs_register_nonce' ) ) {
			wp_die( __( 'Security check failed.', 'jobs' ) );
		}

		$username = sanitize_user( $_POST['user_login'] );
		$email    = sanitize_email( $_POST['user_email'] );
		$password = $_POST['user_pass'];

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			wp_die( $user_id->get_error_message() );
		}

		// Log activity
		$this->log_activity( $user_id, 'Account created' );

		// Set role
		$user = new WP_User( $user_id );
		$user->set_role( 'job_seeker' );

		// Login and redirect
		$redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( $_POST['redirect_to'] ) : home_url();
		wp_set_auth_cookie( $user_id );
		wp_redirect( $redirect_to );
		exit;
	}

}
