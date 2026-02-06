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
			'Job Requests' => 'طلبات التوظيف',
			'Applications Submitted' => 'الطلبات المقدمة',
			'Login to Your Account' => 'تسجيل الدخول إلى حسابك',
			'Create an Account' => 'إنشاء حساب جديد',
			'Sign In' => 'تسجيل الدخول',
			'Join 100,000+ professionals today.' => 'انضم إلى أكثر من 100,000 محترف اليوم.',
			'View Details' => 'عرض التفاصيل',
			'Apply Now' => 'تقدم الآن',
			'Quick Apply' => 'تقدم سريع',
			'Save for later' => 'حفظ لوقت لاحق',
			'Trusted by 100,000+ professionals' => 'موثوق من قبل أكثر من 100,000 محترف',
			'Applications' => 'التطبيقات',
			'Activity Logs' => 'سجلات النشاط',
			'Job History' => 'سجل الوظائف',
			'Post a Job' => 'نشر وظيفة',
			'Draft Manager' => 'إدارة المسودات',
			'Application Records' => 'سجلات الطلبات',
			'Submitted Apps' => 'الطلبات المقدمة',
			'Public Profile' => 'الملف الشخصي العام',
			'Apply for this Job' => 'التقدم لهذه الوظيفة',
			'Share:' => 'مشاركة:',
			'Submit Your Application' => 'إرسال طلبك',
			'Overview' => 'نظرة عامة',
			'Details' => 'التفاصيل',
			'Join to Apply' => 'انضم للتقدم',
			'Post Job Now' => 'انشر الوظيفة الآن',
			'Job Title' => 'عنوان الوظيفة',
			'Specialization' => 'التخصص',
			'Location (City, Country)' => 'الموقع (المدينة، الدولة)',
			'Employment Type' => 'نوع التوظيف',
			'Job Description' => 'وصف الوظيفة',
			'First Name' => 'الاسم الأول',
			'Last Name' => 'اسم العائلة',
			'CV / Resume' => 'السيرة الذاتية',
			'Company Profile' => 'ملف الشركة',
			'Favorites' => 'المفضلة',
			'Drafts' => 'المسودات',
			'Support' => 'الدعم الفني',
			'Advanced' => 'إعدادات متقدمة',
			'Terms' => 'الشروط والأحكام',
			'Articles' => 'المقالات',
			'Account Management' => 'إدارة الحساب',
			'Sign Out' => 'تسجيل الخروج',
			'Welcome to Jobedia' => 'مرحباً بك في جوبيديا',
			'Experience the full potential of Jobedia.' => 'استمتع بكافة مميزات جوبيديا.',
			'View Public Profile' => 'عرض الملف الشخصي العام',
			'Submitted Applications' => 'الطلبات المقدمة',
			'Quick Tools' => 'أدوات سريعة',
			'Advanced Settings' => 'إعدادات متقدمة',
			'Users' => 'المستخدمين',
			'Taxonomy' => 'التصنيفات',
			'UI' => 'الواجهة',
			'Analytics' => 'التحليلات',
			'Site Primary Color' => 'لون الموقع الأساسي',
			'Font Family' => 'نوع الخط',
			'Save UI Settings' => 'حفظ إعدادات الواجهة',
		);

		return isset( $translations[$text] ) ? $translations[$text] : $translated;
	}

}
