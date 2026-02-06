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
		wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', array(), '6.0.0' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jobs-public.css', array(), $this->version, 'all' );
		wp_enqueue_script( 'html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', array(), '0.10.1', true );

		// Custom Colors
		$primary = get_option('jobs_primary_color', '#1d3469');
		$secondary = get_option('jobs_secondary_color', '#15264d');
		$custom_css = ":root { --primary-color: $primary; --primary-dark: $secondary; }";
		wp_add_inline_style( $this->plugin_name, $custom_css );
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
	 * Add application form to single job content.
	 *
	 * @since    1.0.0
	 */
	public function add_application_form( $content ) {
		if ( is_singular( 'job' ) && is_main_query() ) {
			if ( ! is_user_logged_in() ) {
				return '<div class="jobs-msg">' . sprintf( __( 'Please <a href="%s">login</a> to apply for this job.', 'jobs' ), home_url('/jobs-auth') ) . '</div>' . $content;
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

	/**
	 * Handle Application Submission.
	 *
	 * @since    1.0.0
	 */
	public function handle_application_submission() {
		if ( ! isset( $_POST['jobs_nonce'] ) || ! wp_verify_nonce( $_POST['jobs_nonce'], 'jobs_apply_nonce' ) ) {
			wp_die( __( 'Security check failed.', 'jobs' ) );
		}

		$user_id = get_current_user_id();
		$job_id = intval($_POST['job_id']);
		$attach_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
		$cover = isset($_POST['cover_letter']) ? wp_kses_post($_POST['cover_letter']) : '';

		// Fast Application logic
		if ( isset($_POST['quick_apply']) ) {
			$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
			if ( ! empty($docs) ) {
				$attach_id = $docs[0]['id']; // Take first document as default
			}
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

			// Notify Employer
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
				$msg_btn = '<div class="message-employer-section" style="margin-top:10px;"><a href="' . home_url('/jobs-dashboard?tab=messages&view=single&action=new&to=' . $employer_id) . '" class="button">' . __( 'Message Employer', 'jobs' ) . '</a></div>';
				$content = $btn . $msg_btn . $content;
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

		$locations = get_option( 'jobs_global_locations', array() );

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
					// Set default if not exists
					$days = get_option( 'jobs_expiration_days', '50' );
					$publish_date = get_the_date( 'Y-m-d H:i:s', $job_id );
					$expiration = date( 'Y-m-d H:i:s', strtotime( $publish_date . ' + ' . $days . ' days' ) );
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
			// Reset expiration
			$days = get_option( 'jobs_expiration_days', '50' );
			$new_expiration = date( 'Y-m-d H:i:s', strtotime( '+ ' . $days . ' days' ) );
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

		// If no country selected, try geolocation for prioritization
		$geo_country = '';
		if ( empty( $country ) ) {
			$geo_country = $this->get_user_country_by_ip();
		}

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
			'meta_query'     => array( 'relation' => 'AND' ),
			'tax_query'      => array( 'relation' => 'AND' ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Smart prioritization: if geo_country is found and no specific country is selected
		if ( ! empty( $geo_country ) && empty( $country ) ) {
			$args['meta_query'][] = array(
				'relation' => 'OR',
				array(
					'key' => '_job_country',
					'value' => $geo_country,
					'compare' => '=',
				),
				array(
					'key' => '_job_country',
					'compare' => 'NOT EXISTS',
				),
			);
		}

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
			?>
			<div class="jobs-no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border-radius: 24px; border: 1px solid #f0f0f0;">
				<div style="font-size: 48px; margin-bottom: 20px;">🔍</div>
				<h3 style="color: var(--primary-color); margin-bottom: 10px;"><?php _e( 'No matching jobs found', 'jobs' ); ?></h3>
				<p style="color: #718096; max-width: 400px; margin: 0 auto;"><?php _e( 'We couldn\'t find any positions matching your current filters. Try adjusting your keywords or exploring different categories.', 'jobs' ); ?></p>
				<button class="jobs-button btn-outline" style="margin-top: 25px;" onclick="location.reload()"><?php _e( 'Clear all filters', 'jobs' ); ?></button>
			</div>
			<?php
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
		$user = wp_get_current_user();
		$is_logged_in = is_user_logged_in();

		?>
		<nav class="jobs-top-nav-refined">
			<div class="nav-inner-container">
				<!-- Brand Section (Far Left) -->
				<div class="nav-brand-section">
					<div class="main-nav-links">
						<a href="<?php echo home_url(); ?>" class="nav-item"><?php _e( 'Home', 'jobs' ); ?></a>
						<a href="<?php echo home_url( '/jobs' ); ?>" class="nav-item"><?php _e( 'Find Jobs', 'jobs' ); ?></a>
						<a href="#" class="nav-item"><?php _e( 'Employers', 'jobs' ); ?></a>
					</div>
				</div>

				<!-- Action Section (Far Right) -->
				<div class="nav-actions-section">
					<div class="lang-toggle-wrap">
						<?php echo $this->shortcode_language_switcher(); ?>
					</div>

					<?php if ( $is_logged_in ) :
						$role_names = get_option( 'jobs_role_names' );
						$roles = ( array ) $user->roles;
						$role_id = $roles[0];
						$display_role = isset( $role_names[$role_id] ) ? $role_names[$role_id] : ucfirst($role_id);

						// Notifications Polling Context
						$notifs = get_user_meta( $user->ID, '_jobs_notifications', true ) ?: array();
						$unread_notifs = count($notifs);
					?>
						<div class="user-meta-controls">
							<!-- Messages Shortcut -->
							<a href="<?php echo home_url('/jobs-dashboard?tab=messages'); ?>" class="icon-control-btn" title="<?php _e('Messages', 'jobs'); ?>">
								<i class="dashicons dashicons-email-alt"></i>
							</a>

							<!-- Notifications Dropdown -->
							<div class="nav-dropdown-wrapper notification-trigger">
								<button class="icon-control-btn" id="notif-drop-btn">
									<i class="dashicons dashicons-bell"></i>
									<?php if($unread_notifs > 0): ?><span class="pulse-badge"><?php echo $unread_notifs; ?></span><?php endif; ?>
								</button>
								<div class="smart-dropdown-panel" id="notif-panel">
									<div class="panel-header">
										<span><?php _e( 'Notifications', 'jobs' ); ?></span>
										<a href="#"><?php _e( 'Mark all read', 'jobs' ); ?></a>
									</div>
									<div class="panel-body scrollable">
										<?php if( !empty($notifs) ) : foreach( array_reverse($notifs) as $n ) : ?>
											<div class="notif-row unread">
												<div class="notif-icon"><i class="dashicons dashicons-info"></i></div>
												<div class="notif-content">
													<p><?php echo esc_html($n['message']); ?></p>
													<small><?php echo human_time_diff($n['time'], time()); ?> ago</small>
												</div>
											</div>
										<?php endforeach; else : ?>
											<p class="empty-msg"><?php _e( 'No new notifications', 'jobs' ); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Profile Dropdown (Far Right) -->
							<div class="nav-dropdown-wrapper profile-trigger">
								<div class="user-pill">
									<?php echo get_avatar( $user->ID, 40 ); ?>
									<div class="user-name-role">
										<span class="u-name"><?php echo esc_html( $user->display_name ); ?></span>
										<span class="u-role"><?php echo esc_html( $display_role ); ?></span>
									</div>
									<i class="dashicons dashicons-arrow-down-alt2"></i>
								</div>
								<div class="smart-dropdown-panel profile-panel">
									<a href="<?php echo home_url( '/jobs-dashboard' ); ?>"><i class="dashicons dashicons-dashboard"></i> <?php _e( 'Dashboard', 'jobs' ); ?></a>
									<a href="<?php echo home_url( '/jobs-dashboard?tab=settings' ); ?>"><i class="dashicons dashicons-admin-settings"></i> <?php _e( 'Account Settings', 'jobs' ); ?></a>
									<div class="divider"></div>
									<a href="<?php echo wp_logout_url( home_url() ); ?>" class="logout-link"><i class="dashicons dashicons-exit"></i> <?php _e( 'Logout', 'jobs' ); ?></a>
								</div>
							</div>
						</div>
					<?php else : ?>
						<div class="guest-auth-actions">
							<a href="<?php echo home_url('/jobs-auth'); ?>" class="btn-login-text"><?php _e( 'Sign In', 'jobs' ); ?></a>
							<a href="<?php echo home_url('/jobs-auth'); ?>" class="jobs-button"><?php _e( 'Post a Job', 'jobs' ); ?></a>
						</div>
					<?php endif; ?>
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
	/**
	 * Override Single Job Template
	 */
	public function job_single_template( $template ) {
		if ( is_singular( 'job' ) || get_post_type() === 'job' ) {
			$new_template = plugin_dir_path( __FILE__ ) . 'partials/jobs-single-listing.php';
			if ( file_exists( $new_template ) ) {
				return $new_template;
			}
		}
		return $template;
	}

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
		$geo_country = $this->get_user_country_by_ip();
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
		<div class="jobs-lang-switcher-modern">
			<a href="?lang=en" class="<?php echo ($current_lang == 'en_US') ? 'active' : ''; ?>" title="English">
				<span class="lang-flag">🇺🇸</span> <span class="lang-label">EN</span>
			</a>
			<a href="?lang=ar" class="<?php echo ($current_lang == 'ar') ? 'active' : ''; ?>" title="العربية">
				<span class="lang-flag">🇸🇦</span> <span class="lang-label">AR</span>
			</a>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Register user from shortcode.
	 *
	 * @since    1.0.0
	 */
	/**
	 * Get user country by IP.
	 *
	 * @since    1.0.0
	 */
	private function get_user_country_by_ip() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if ( $ip == '127.0.0.1' || $ip == '::1' ) return 'USA'; // Mock for local

		$response = wp_remote_get( "http://ip-api.com/json/{$ip}" );
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ) );
			if ( isset( $body->country ) ) {
				return $body->country;
			}
		}
		return '';
	}

	/**
	 * AJAX Login handler.
	 */
	public function ajax_login() {
		check_ajax_referer( 'jobs_auth_nonce', 'auth_nonce' );

		$info = array();
		$info['user_login'] = sanitize_user($_POST['user_login']);
		$info['user_password'] = $_POST['user_pass'];
		$info['remember'] = isset($_POST['rememberme']);

		$user_signon = wp_signon( $info, is_ssl() );

		if ( is_wp_error( $user_signon ) ) {
			wp_send_json_error( $user_signon->get_error_message() );
		} else {
			$this->log_activity( $user_signon->ID, 'User logged in via AJAX' );
			$redirect = isset($_POST['redirect_to']) ? esc_url_raw($_POST['redirect_to']) : home_url('/jobs-dashboard');
			wp_send_json_success( array(
				'message' => __( 'Login successful! Redirecting...', 'jobs' ),
				'redirect' => $redirect
			) );
		}
	}

	/**
	 * AJAX Register handler.
	 */
	public function ajax_register() {
		check_ajax_referer( 'jobs_auth_nonce', 'auth_nonce' );

		$email = sanitize_email($_POST['user_email']);
		$name = sanitize_text_field($_POST['full_name']);
		$prof_title = isset($_POST['professional_title']) ? sanitize_text_field($_POST['professional_title']) : '';
		$pass = $_POST['user_pass'];
		$role = sanitize_text_field($_POST['user_role']);

		if ( ! is_email($email) ) {
			wp_send_json_error( __( 'Invalid email address.', 'jobs' ) );
		}

		if ( email_exists($email) ) {
			wp_send_json_error( __( 'Email already registered.', 'jobs' ) );
		}

		if ( strlen($pass) < 8 ) {
			wp_send_json_error( __( 'Password must be at least 8 characters.', 'jobs' ) );
		}

		$username = strstr($email, '@', true) . rand(100,999);
		$user_id = wp_create_user( $username, $pass, $email );

		if ( is_wp_error($user_id) ) {
			wp_send_json_error( $user_id->get_error_message() );
		}

		$user = new WP_User($user_id);
		$user->set_role($role);
		wp_update_user( array( 'ID' => $user_id, 'display_name' => $name ) );

		if ( ! empty($prof_title) ) {
			update_user_meta( $user_id, '_job_title', $prof_title );
		}

		// Auto login after registration
		wp_set_auth_cookie($user_id);
		$this->log_activity( $user_id, 'Account created' );

		wp_send_json_success( __( 'Account created successfully!', 'jobs' ) );
	}

	/**
	 * AJAX Application Submission.
	 */
	public function ajax_submit_application() {
		check_ajax_referer( 'jobs_apply_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( __( 'You must be logged in to apply.', 'jobs' ) );
		}

		$user_id = get_current_user_id();
		$job_id = intval($_POST['job_id']);
		$attach_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
		$cover = isset($_POST['cover_letter']) ? wp_kses_post($_POST['cover_letter']) : '';

		if ( isset($_POST['quick_apply']) ) {
			$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
			if ( ! empty($docs) ) {
				$attach_id = $docs[0]['id'];
			}
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

			wp_send_json_success( __( 'Application submitted successfully!', 'jobs' ) );
		}

		wp_send_json_error( __( 'Failed to submit application.', 'jobs' ) );
	}

	/**
	 * Dynamic Auth Shortcode.
	 */
	public function shortcode_jobs_auth() {
		if ( is_user_logged_in() ) {
			return '<p class="jobs-msg success">' . __( 'You are already logged in.', 'jobs' ) . '</p>';
		}
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/jobs-auth-dynamic.php';
		return ob_get_clean();
	}

	public function handle_user_registration() {
		// Handled via AJAX now
	}

	/**
	 * AJAX handler for Geolocation-based Search
	 */
	public function ajax_jobs_geo_search() {
		check_ajax_referer( 'jobs_search_nonce', 'nonce' );

		// In a real implementation, we would use lat/lon to query nearby jobs.
		// For this simulation, we'll return a localized greeting and prioritized results.
		$args = array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'orderby'        => 'rand'
		);
		$query = new WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				include plugin_dir_path( __FILE__ ) . 'partials/jobs-card-template.php';
			endwhile;
			wp_reset_postdata();
		endif;
		$html = ob_get_clean();

		wp_send_json_success( array( 'html' => $html ) );
	}

	/**
	 * Absolute WordPress Abstraction
	 */
	public function hide_wp_for_non_admins() {
		if ( ! current_user_can( 'manage_options' ) ) {
			show_admin_bar( false );
		}
	}

	public function restrict_wp_admin_access() {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! current_user_can( 'manage_options' ) ) {
			wp_redirect( home_url( '/jobs-dashboard' ) );
			exit;
		}
	}

}
