<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://jobedia.com
 * @since             1.0.0
 * @package           Jobs
 *
 * @wordpress-plugin
 * Plugin Name:       Jobs
 * Plugin URI:        https://jobedia.com
 * Description:       A comprehensive, modern, and professional job management system.
 * Version:           1.0.0
 * Author:            jobedia
 * Author URI:        https://jobedia.com
 * License:           GPL-2.0+
 * Text Domain:       jobs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - http://semver.org/
 */
define( 'JOBS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jobs-activator.php
 */
function activate_jobs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-activator.php';
	Jobs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jobs-deactivator.php
 */
function deactivate_jobs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-deactivator.php';
	Jobs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jobs' );
register_deactivation_hook( __FILE__, 'deactivate_jobs' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jobs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the
 * plugin from this point in the file will register all of the appropriate hooks
 * with WordPress.
 *
 * @since    1.0.0
 */
function run_jobs() {

	$plugin = new Jobs();
	$plugin->run();

}
run_jobs();
