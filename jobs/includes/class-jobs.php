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

		$plugin_public = new Jobs_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'body_class', $plugin_public, 'add_rtl_body_class' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'add_seo_meta_tags' );
		$this->loader->add_action( 'wp_ajax_jobs_search', $plugin_public, 'ajax_jobs_search' );
		$this->loader->add_action( 'wp_ajax_nopriv_jobs_search', $plugin_public, 'ajax_jobs_search' );
		$this->loader->add_action( 'wp_ajax_get_states', $plugin_public, 'ajax_get_states' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_states', $plugin_public, 'ajax_get_states' );
		$this->loader->add_action( 'wp_body_open', $plugin_public, 'add_custom_nav_bar' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'handle_dashboard_redirection' );
		$this->loader->add_action( 'admin_post_jobs_register_user', $plugin_public, 'handle_user_registration' );
		$this->loader->add_action( 'admin_post_nopriv_jobs_register_user', $plugin_public, 'handle_user_registration' );

		$this->loader->add_shortcode( 'jobs_login', $plugin_public, 'shortcode_jobs_login' );
		$this->loader->add_shortcode( 'jobs_register', $plugin_public, 'shortcode_jobs_register' );
		$this->loader->add_shortcode( 'jobs_search_engine', $plugin_public, 'shortcode_jobs_search_engine' );
		$this->loader->add_shortcode( 'jobs_language_switcher', $plugin_public, 'shortcode_language_switcher' );
		$this->loader->add_shortcode( 'jobs_dashboard', $plugin_public, 'shortcode_jobs_dashboard' );

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
