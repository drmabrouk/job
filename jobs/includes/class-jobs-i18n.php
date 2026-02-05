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
			if ( $lang == 'ar' || $lang == 'en' ) {
				setcookie( 'jobs_lang', $lang, time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
				if ( is_user_logged_in() ) {
					update_user_meta( get_current_user_id(), '_jobs_locale', ( $lang == 'ar' ? 'ar' : 'en_US' ) );
				}
				return ( $lang == 'ar' ? 'ar' : 'en_US' );
			}
		}

		if ( is_user_logged_in() ) {
			$user_locale = get_user_meta( get_current_user_id(), '_jobs_locale', true );
			if ( $user_locale ) {
				return $user_locale;
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

	/**
	 * Manual Arabic Translation Fallback
	 */
	public function manual_arabic_translation( $translated, $text, $domain ) {
		if ( $domain !== 'jobs' || get_locale() !== 'ar' ) {
			return $translated;
		}

		$translations = array(
			'Find Jobs' => 'بحث عن وظائف',
			'Employers' => 'أصحاب العمل',
			'Notifications' => 'التنبيهات',
			'Messages' => 'الرسائل',
			'Dashboard' => 'لوحة التحكم',
			'Account Settings' => 'إعدادات الحساب',
			'Logout' => 'تسجيل الخروج',
			'Login to Your Account' => 'تسجيل الدخول إلى حسابك',
			'Create an Account' => 'إنشاء حساب جديد',
			'Sign In' => 'تسجيل الدخول',
			'Join 100,000+ professionals today.' => 'انضم إلى أكثر من 100,000 محترف اليوم.',
			'View Details' => 'عرض التفاصيل',
			'Apply Now' => 'تقدم الآن',
			'Quick Apply' => 'تقدم سريع',
			'Save for later' => 'حفظ لوقت لاحق',
			'Trusted by 100,000+ professionals' => 'موثوق من قبل أكثر من 100,000 محترف',
		);

		return isset( $translations[$text] ) ? $translations[$text] : $translated;
	}

}
