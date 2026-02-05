<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jobs
 * @subpackage Jobs/includes
 * @author     jobedia <info@jobedia.com>
 */
class Jobs_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jobs',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

	/**
	 * Set the locale based on the user's choice.
	 *
	 * @since    1.0.0
	 */
	public function set_locale( $locale ) {
		if ( isset( $_GET['lang'] ) ) {
			$lang = sanitize_text_field( $_GET['lang'] );
			if ( $lang == 'ar' ) {
				setcookie( 'jobs_lang', 'ar', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
				return 'ar';
			} elseif ( $lang == 'en' ) {
				setcookie( 'jobs_lang', 'en', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
				return 'en_US';
			}
		}

		if ( isset( $_COOKIE['jobs_lang'] ) ) {
			if ( $_COOKIE['jobs_lang'] == 'ar' ) {
				return 'ar';
			} elseif ( $_COOKIE['jobs_lang'] == 'en' ) {
				return 'en_US';
			}
		}

		return $locale;
	}

}
