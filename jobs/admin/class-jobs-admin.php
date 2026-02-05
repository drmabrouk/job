<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Jobs
 * @subpackage Jobs/admin
 * @author     jobedia <info@jobedia.com>
 */
class Jobs_Admin {

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
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jobs-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jobs-admin.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add the admin menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		// 1. Job Management
		add_menu_page(
			__( 'Job Management', 'jobs' ),
			__( 'Job Management', 'jobs' ),
			'manage_jobs_plugin',
			'job-management',
			array( $this, 'display_job_management_page' ),
			'dashicons-businessperson',
			20
		);
		add_submenu_page( 'job-management', __( 'All Jobs', 'jobs' ), __( 'All Jobs', 'jobs' ), 'manage_jobs_plugin', 'edit.php?post_type=job' );
		add_submenu_page( 'job-management', __( 'Add New Job', 'jobs' ), __( 'Add New Job', 'jobs' ), 'manage_jobs_plugin', 'post-new.php?post_type=job' );
		add_submenu_page( 'job-management', __( 'Categories', 'jobs' ), __( 'Categories', 'jobs' ), 'manage_jobs_plugin', 'edit-tags.php?taxonomy=job_category&post_type=job' );
		add_submenu_page( 'job-management', __( 'Job Settings', 'jobs' ), __( 'Job Settings', 'jobs' ), 'manage_jobs_plugin', 'job-settings', array( $this, 'display_job_settings_page' ) );

		// 2. Applications
		add_menu_page(
			__( 'Applications', 'jobs' ),
			__( 'Applications', 'jobs' ),
			'view_applications',
			'job-applications',
			array( $this, 'display_applications_page' ),
			'dashicons-clipboard',
			21
		);
		add_submenu_page( 'job-applications', __( 'All Applications', 'jobs' ), __( 'All Applications', 'jobs' ), 'view_applications', 'edit.php?post_type=application' );
		add_submenu_page( 'job-applications', __( 'Pending Review', 'jobs' ), __( 'Pending Review', 'jobs' ), 'view_applications', 'applications-pending', array( $this, 'display_applications_pending_page' ) );
		add_submenu_page( 'job-applications', __( 'Shortlisted', 'jobs' ), __( 'Shortlisted', 'jobs' ), 'view_applications', 'applications-shortlisted', array( $this, 'display_applications_shortlisted_page' ) );
		add_submenu_page( 'job-applications', __( 'Application Settings', 'jobs' ), __( 'Application Settings', 'jobs' ), 'view_applications', 'application-settings', array( $this, 'display_application_settings_page' ) );

		// 3. Theme & Layout Settings
		add_menu_page(
			__( 'Theme Settings', 'jobs' ),
			__( 'Theme Settings', 'jobs' ),
			'manage_options',
			'job-theme-settings',
			array( $this, 'display_theme_settings_page' ),
			'dashicons-admin-appearance',
			22
		);
		add_submenu_page( 'job-theme-settings', __( 'General Layout', 'jobs' ), __( 'General Layout', 'jobs' ), 'manage_options', 'job-theme-settings' );
		add_submenu_page( 'job-theme-settings', __( 'Colors & Fonts', 'jobs' ), __( 'Colors & Fonts', 'jobs' ), 'manage_options', 'job-colors-fonts', array( $this, 'display_colors_fonts_page' ) );
		add_submenu_page( 'job-theme-settings', __( 'Role Renaming', 'jobs' ), __( 'Role Renaming', 'jobs' ), 'manage_options', 'job-role-renaming', array( $this, 'display_role_renaming_page' ) );
		add_submenu_page( 'job-theme-settings', __( 'AdSense Settings', 'jobs' ), __( 'AdSense Settings', 'jobs' ), 'manage_options', 'job-adsense-settings', array( $this, 'display_adsense_settings_page' ) );

		// 4. Reports & Analytics
		add_menu_page(
			__( 'Reports', 'jobs' ),
			__( 'Reports', 'jobs' ),
			'manage_options',
			'job-reports',
			array( $this, 'display_reports_page' ),
			'dashicons-chart-area',
			23
		);
		add_submenu_page( 'job-reports', __( 'Overview', 'jobs' ), __( 'Overview', 'jobs' ), 'manage_options', 'job-reports' );
		add_submenu_page( 'job-reports', __( 'Job Stats', 'jobs' ), __( 'Job Stats', 'jobs' ), 'manage_options', 'job-stats', array( $this, 'display_job_stats_page' ) );
		add_submenu_page( 'job-reports', __( 'Application Stats', 'jobs' ), __( 'Application Stats', 'jobs' ), 'manage_options', 'job-app-stats', array( $this, 'display_app_stats_page' ) );
		add_submenu_page( 'job-reports', __( 'User Activity', 'jobs' ), __( 'User Activity', 'jobs' ), 'manage_options', 'job-user-activity', array( $this, 'display_user_activity_page' ) );
		add_submenu_page( 'job-reports', __( 'Audit Trail', 'jobs' ), __( 'Audit Trail', 'jobs' ), 'manage_options', 'job-audit-trail', array( $this, 'display_audit_trail_page' ) );
	}

	public function display_job_management_page() { echo '<div class="wrap"><h1>Job Management</h1></div>'; }
	public function display_job_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Job Settings', 'jobs' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options' );
				do_settings_sections( 'job-settings' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Default Job Status', 'jobs' ); ?></th>
						<td>
							<select name="jobs_default_status">
								<option value="publish"><?php _e( 'Published', 'jobs' ); ?></option>
								<option value="pending"><?php _e( 'Pending Review', 'jobs' ); ?></option>
							</select>
						</td>
					</tr>
				</table>

				<hr>
				<h2><?php _e( 'Category-Specific Premium Ads', 'jobs' ); ?></h2>
				<table class="form-table" id="category-ads-table">
					<?php
					$cat_ads = get_option( 'jobs_category_ads', array() );
					$categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false ) );
					foreach ( $categories as $cat ) :
						$val = isset( $cat_ads[$cat->term_id] ) ? $cat_ads[$cat->term_id] : '';
					?>
					<tr valign="top">
						<th scope="row"><?php echo esc_html( $cat->name ); ?></th>
						<td>
							<textarea name="jobs_category_ads[<?php echo $cat->term_id; ?>]" rows="3" cols="50" class="large-text"><?php echo esc_textarea( $val ); ?></textarea>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	public function display_applications_page() { echo '<div class="wrap"><h1>Applications</h1></div>'; }
	public function display_applications_pending_page() { echo '<div class="wrap"><h1>Pending Review</h1></div>'; }
	public function display_applications_shortlisted_page() { echo '<div class="wrap"><h1>Shortlisted</h1></div>'; }
	public function display_application_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Application Settings', 'jobs' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Enable Email Notifications', 'jobs' ); ?></th>
						<td>
							<input type="checkbox" name="jobs_enable_notifications" value="1" <?php checked( 1, get_option( 'jobs_enable_notifications' ), true ); ?> />
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	public function display_theme_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Theme & Layout Settings', 'jobs' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Site Logo', 'jobs' ); ?></th>
						<td>
							<input type="hidden" name="jobs_logo_id" id="jobs_logo_id" value="<?php echo esc_attr( get_option( 'jobs_logo_id' ) ); ?>" />
							<div id="jobs_logo_preview" style="margin-bottom: 10px;">
								<?php if ( $logo_id = get_option( 'jobs_logo_id' ) ) : ?>
									<?php echo wp_get_attachment_image( $logo_id, 'medium' ); ?>
								<?php endif; ?>
							</div>
							<button type="button" class="button" id="jobs_upload_logo_btn"><?php _e( 'Upload/Select Logo', 'jobs' ); ?></button>
							<button type="button" class="button" id="jobs_remove_logo_btn"><?php _e( 'Remove Logo', 'jobs' ); ?></button>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Logo Width (px)', 'jobs' ); ?></th>
						<td>
							<input type="number" name="jobs_logo_width" value="<?php echo esc_attr( get_option( 'jobs_logo_width', '200' ) ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Logo Margin Bottom (px)', 'jobs' ); ?></th>
						<td>
							<input type="number" name="jobs_logo_margin" value="<?php echo esc_attr( get_option( 'jobs_logo_margin', '40' ) ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Astra Compatibility Mode', 'jobs' ); ?></th>
						<td>
							<input type="checkbox" name="jobs_astra_compat" value="1" <?php checked( 1, get_option( 'jobs_astra_compat', 1 ) ); ?> />
							<p class="description"><?php _e( 'Optimize containers and spacing for Astra theme.', 'jobs' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	public function display_colors_fonts_page() { echo '<div class="wrap"><h1>Colors & Fonts</h1></div>'; }
	public function display_role_renaming_page() {
		$role_names = get_option( 'jobs_role_names', array(
			'job_seeker'           => 'Job Seeker',
			'employer'             => 'Employer',
			'job_reviewer'         => 'Job Reviewer',
			'system_administrator' => 'System Administrator',
		) );

		if ( isset( $_POST['jobs_save_roles'] ) && check_admin_referer( 'jobs_role_renaming_action', 'jobs_role_renaming_nonce' ) ) {
			foreach ( $role_names as $id => $name ) {
				if ( isset( $_POST['role_' . $id] ) ) {
					$role_names[$id] = sanitize_text_field( $_POST['role_' . $id] );
					// Update actual WP role name
					global $wp_roles;
					if ( isset( $wp_roles->roles[$id] ) ) {
						$wp_roles->roles[$id]['name'] = $role_names[$id];
						update_option( $wp_roles->role_key, $wp_roles->roles );
					}
				}
			}
			update_option( 'jobs_role_names', $role_names );
			echo '<div class="updated"><p>' . __( 'Role names updated.', 'jobs' ) . '</p></div>';
		}
		?>
		<div class="wrap">
			<h1><?php _e( 'Role Renaming', 'jobs' ); ?></h1>
			<form method="post" action="">
				<?php wp_nonce_field( 'jobs_role_renaming_action', 'jobs_role_renaming_nonce' ); ?>
				<table class="form-table">
					<?php foreach ( $role_names as $id => $name ) : ?>
					<tr valign="top">
						<th scope="row"><?php echo esc_html( ucfirst( str_replace( '_', ' ', $id ) ) ); ?></th>
						<td>
							<input type="text" name="role_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $name ); ?>" class="regular-text" />
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				<p class="submit">
					<input type="submit" name="jobs_save_roles" class="button button-primary" value="<?php _e( 'Save Role Names', 'jobs' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}
	public function display_adsense_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'AdSense Settings', 'jobs' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'jobs_options' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Top Ad Zone', 'jobs' ); ?></th>
						<td>
							<textarea name="jobs_ad_top" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'jobs_ad_top' ) ); ?></textarea>
							<p class="description"><?php _e( 'Displays at the top of the search engine.', 'jobs' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Bottom Ad Zone', 'jobs' ); ?></th>
						<td>
							<textarea name="jobs_ad_bottom" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'jobs_ad_bottom' ) ); ?></textarea>
							<p class="description"><?php _e( 'Displays at the bottom of the job listings.', 'jobs' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Sidebar/Grid Ad Zone', 'jobs' ); ?></th>
						<td>
							<textarea name="jobs_ad_sidebar" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'jobs_ad_sidebar' ) ); ?></textarea>
							<p class="description"><?php _e( 'Strategically placed within the layout for maximum visibility.', 'jobs' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
	public function display_reports_page() {
		$metrics = $this->get_system_metrics();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/jobs-admin-reports.php';
	}

	/**
	 * Get system metrics for reports.
	 *
	 * @since    1.0.0
	 */
	private function get_system_metrics() {
		$metrics = array();

		$jobs_count = wp_count_posts( 'job' );
		$metrics['total_jobs'] = $jobs_count->publish + $jobs_count->pending;
		$metrics['published_jobs'] = $jobs_count->publish;
		$metrics['pending_jobs'] = $jobs_count->pending;

		$apps_count = wp_count_posts( 'application' );
		$metrics['total_applications'] = $apps_count->publish + $apps_count->pending;

		$users_count = count_users();
		$metrics['total_users'] = $users_count['total_users'];

		return $metrics;
	}
	public function display_job_stats_page() { echo '<div class="wrap"><h1>Job Stats</h1></div>'; }
	public function display_app_stats_page() { echo '<div class="wrap"><h1>Application Stats</h1></div>'; }
	public function display_user_activity_page() { echo '<div class="wrap"><h1>User Activity</h1></div>'; }

	/**
	 * Display Audit Trail page.
	 *
	 * @since    1.0.0
	 */
	public function display_audit_trail_page() {
		$users = get_users();
		?>
		<div class="wrap">
			<h1><?php _e( 'Security Audit Trail', 'jobs' ); ?></h1>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e( 'User', 'jobs' ); ?></th>
						<th><?php _e( 'Action', 'jobs' ); ?></th>
						<th><?php _e( 'Time', 'jobs' ); ?></th>
						<th><?php _e( 'IP Address', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $users as $user ) :
						$logs = get_user_meta( $user->ID, '_jobs_activity_log', true ) ?: array();
						foreach ( array_reverse( $logs ) as $log ) :
					?>
						<tr>
							<td><strong><?php echo esc_html( $user->display_name ); ?></strong></td>
							<td><?php echo esc_html( $log['action'] ); ?></td>
							<td><?php echo date( 'Y-m-d H:i:s', $log['time'] ); ?></td>
							<td><?php echo esc_html( $log['ip'] ); ?></td>
						</tr>
					<?php endforeach; endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Register settings for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting( 'jobs_options', 'jobs_role_names' );
		register_setting( 'jobs_options', 'jobs_adsense_code' );
		register_setting( 'jobs_options', 'jobs_ad_top' );
		register_setting( 'jobs_options', 'jobs_ad_bottom' );
		register_setting( 'jobs_options', 'jobs_ad_sidebar' );
		register_setting( 'jobs_options', 'jobs_category_ads' );
		register_setting( 'jobs_options', 'jobs_logo_id' );
		register_setting( 'jobs_options', 'jobs_logo_width' );
		register_setting( 'jobs_options', 'jobs_logo_margin' );
		register_setting( 'jobs_options', 'jobs_astra_compat' );
		register_setting( 'jobs_options', 'jobs_default_status' );
		register_setting( 'jobs_options', 'jobs_enable_notifications' );
	}

}
