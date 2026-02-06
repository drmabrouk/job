<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Jobs
 * @subpackage Jobs/includes
 * @author     jobedia <info@jobedia.com>
 */
class Jobs {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Jobs_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, set the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'JOBS_VERSION' ) ) {
			$this->version = JOBS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'jobs';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Jobs_Loader. Orchestrates the hooks of the plugin.
	 * - Jobs_i18n. Defines internationalization functionality.
	 * - Jobs_Admin. Defines all hooks for the admin area.
	 * - Jobs_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for the centralized system map.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-system.php';

		/**
		 * The class responsible for defining all code necessary to run during activation.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-activator.php';

		/**
		 * The class responsible for orchestrating the hooks with the WordPress
		 * plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-i18n.php';

		/**
		 * The class responsible for defining all hooks that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jobs-admin.php';

		/**
		 * The class responsible for defining all hooks that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jobs-public.php';

		$this->loader = new Jobs_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Jobs_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Jobs_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		$this->loader->add_filter( 'locale', $plugin_i18n, 'set_locale' );
		$this->loader->add_filter( 'gettext', $plugin_i18n, 'manual_arabic_translation', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Jobs_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', 'Jobs_Activator', 'register_taxonomies' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		// Register CPTs and Taxonomies on every init
		require_once plugin_dir_path( __FILE__ ) . 'class-jobs-activator.php';
		$this->loader->add_action( 'init', 'Jobs_Activator', 'register_post_types' );
		$this->loader->add_action( 'init', 'Jobs_Activator', 'register_taxonomies' );

		$plugin_public = new Jobs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'add_rewrite_rules' );
		$this->loader->add_action( 'after_setup_theme', $plugin_public, 'hide_wp_for_non_admins' );
		$this->loader->add_action( 'admin_init', $plugin_public, 'restrict_wp_admin_access' );
		$this->loader->add_action( 'jobs_daily_cron', $plugin_public, 'check_job_expirations' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'body_class', $plugin_public, 'add_rtl_body_class' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'add_job_single_ads' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'add_follow_employer_button' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'add_application_form' );
		$this->loader->add_action( 'wp_ajax_jobs_search', $plugin_public, 'ajax_jobs_search' );
		$this->loader->add_action( 'wp_ajax_nopriv_jobs_search', $plugin_public, 'ajax_jobs_search' );
		$this->loader->add_action( 'wp_ajax_jobs_geo_search', $plugin_public, 'ajax_jobs_geo_search' );
		$this->loader->add_action( 'wp_ajax_nopriv_jobs_geo_search', $plugin_public, 'ajax_jobs_geo_search' );
		$this->loader->add_action( 'wp_ajax_get_states', $plugin_public, 'ajax_get_states' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_states', $plugin_public, 'ajax_get_states' );
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'add_custom_nav_bar' );
		$this->loader->add_filter( 'template_include', $plugin_public, 'job_single_template', 99 );
		$this->loader->add_action( 'wp_login', $plugin_public, 'log_login_activity', 10, 2 );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'handle_dashboard_redirection' );
		$this->loader->add_action( 'admin_post_jobs_register_user', $plugin_public, 'handle_user_registration' );
		$this->loader->add_action( 'admin_post_nopriv_jobs_register_user', $plugin_public, 'handle_user_registration' );
		$this->loader->add_action( 'admin_post_jobs_submit_application', $plugin_public, 'handle_application_submission' );
		$this->loader->add_action( 'wp_ajax_reactivate_job', $plugin_public, 'ajax_reactivate_job' );
		$this->loader->add_action( 'wp_ajax_extend_job', $plugin_public, 'ajax_extend_job' );
		$this->loader->add_action( 'wp_ajax_save_job', $plugin_public, 'ajax_save_job' );
		$this->loader->add_action( 'wp_ajax_follow_employer', $plugin_public, 'ajax_follow_employer' );
		$this->loader->add_action( 'wp_ajax_check_notifications', $plugin_public, 'ajax_check_notifications' );
		$this->loader->add_action( 'wp_ajax_jobs_ajax_login', $plugin_public, 'ajax_login' );
		$this->loader->add_action( 'wp_ajax_nopriv_jobs_ajax_login', $plugin_public, 'ajax_login' );
		$this->loader->add_action( 'wp_ajax_jobs_ajax_register', $plugin_public, 'ajax_register' );
		$this->loader->add_action( 'wp_ajax_nopriv_jobs_ajax_register', $plugin_public, 'ajax_register' );
		$this->loader->add_action( 'wp_ajax_jobs_save_onboarding', $plugin_public, 'ajax_save_onboarding' );
		$this->loader->add_action( 'wp_ajax_jobs_toggle_verification', $plugin_public, 'ajax_toggle_verification' );
		$this->loader->add_action( 'wp_ajax_jobs_post_job_ajax', $plugin_public, 'ajax_post_job' );
		$this->loader->add_action( 'wp_ajax_jobs_save_company_profile', $plugin_public, 'ajax_save_company_profile' );
		$this->loader->add_action( 'wp_ajax_jobs_send_support_message', $plugin_public, 'ajax_send_support_message' );
		$this->loader->add_action( 'wp_ajax_jobs_submit_application_ajax', $plugin_public, 'ajax_submit_application' );
		$this->loader->add_action( 'wp_insert_post', $plugin_public, 'notify_followers_new_job', 10, 3 );

		$this->loader->add_shortcode( 'jobs_auth', $plugin_public, 'shortcode_jobs_auth' );
		$this->loader->add_shortcode( 'jobs_login', $plugin_public, 'shortcode_jobs_login' );
		$this->loader->add_shortcode( 'jobs_register', $plugin_public, 'shortcode_jobs_register' );
		$this->loader->add_shortcode( 'jobs_search_engine', $plugin_public, 'shortcode_jobs_search_engine' );
		$this->loader->add_shortcode( 'jobs_language_switcher', $plugin_public, 'shortcode_language_switcher' );
		$this->loader->add_shortcode( 'jobs_dashboard', $plugin_public, 'shortcode_jobs_dashboard' );
		$this->loader->add_shortcode( 'jobs_settings', $plugin_public, 'shortcode_jobs_settings' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Jobs_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
