<?php

/**
 * Centralized System Map for the Jobs plugin.
 *
 * Defines core pages and application modules to ensure structural integrity.
 *
 * @package    Jobs
 * @subpackage Jobs/includes
 * @author     jobedia <info@jobedia.com>
 */
class Jobs_System {

	/**
	 * Get the map of core plugin pages.
	 *
	 * @return array
	 */
	public static function get_core_pages() {
		return array(
			'jobs' => array(
				'title'     => __( 'Jobs', 'jobs' ),
				'shortcode' => '[jobs_search_engine]',
				'slug'      => 'jobs',
				'required'  => true,
				'is_home'   => true,
			),
			'dashboard' => array(
				'title'     => __( 'Jobs Dashboard', 'jobs' ),
				'shortcode' => '[jobs_dashboard]',
				'slug'      => 'jobs-dashboard',
				'required'  => true,
			),
			'auth' => array(
				'title'     => __( 'Join Us', 'jobs' ),
				'shortcode' => '[jobs_auth]',
				'slug'      => 'jobs-auth',
				'required'  => true,
			),
			'settings' => array(
				'title'     => __( 'Jobs Settings', 'jobs' ),
				'shortcode' => '[jobs_settings]',
				'slug'      => 'jobs-settings',
				'required'  => true,
			),
		);
	}

	/**
	 * Get the map of application modules (Apps Launcher).
	 *
	 * @return array
	 */
	public static function get_applications() {
		return array(
			'post-job' => array(
				'label'     => __( 'Post a Job', 'jobs' ),
				'icon'      => 'fas fa-plus',
				'bg'        => '#e0f2fe',
				'color'     => '#0369a1',
				'panel'     => 'post-job',
				'partial'   => 'jobs-post-inline.php',
				'capability' => 'employer', // Logic handled in display
			),
			'job-history' => array(
				'label'     => __( 'Job History', 'jobs' ),
				'icon'      => 'fas fa-history',
				'bg'        => '#f0fdf4',
				'color'     => '#166534',
				'panel'     => 'job-history',
				'partial'   => 'app-job-history.php',
				'capability' => 'employer',
			),
			'manage-apps' => array(
				'label'     => __( 'Job Requests', 'jobs' ),
				'icon'      => 'fas fa-inbox',
				'bg'        => '#fff7ed',
				'color'     => '#ea580c',
				'panel'     => 'manage-apps',
				'partial'   => 'jobs-manage-applications.php',
				'capability' => 'employer',
			),
			'public-profile' => array(
				'label'     => __( 'Public Profile', 'jobs' ),
				'icon'      => 'fas fa-user-circle',
				'bg'        => '#ecfeff',
				'color'     => '#155e75',
				'link'      => true,
				'capability' => 'read',
			),
			'submitted-apps' => array(
				'label'     => __( 'Applications Submitted', 'jobs' ),
				'icon'      => 'fas fa-paper-plane',
				'bg'        => '#f5f3ff',
				'color'     => '#5b21b6',
				'panel'     => 'submitted-apps',
				'partial'   => 'app-submitted-apps.php',
				'capability' => 'job_seeker',
			),
			'cv-resume' => array(
				'label'     => __( 'CV / Resume', 'jobs' ),
				'icon'      => 'fas fa-file-invoice',
				'bg'        => '#fff1f2',
				'color'     => '#e11d48',
				'panel'     => 'cv-resume',
				'partial'   => 'app-cv-resume.php',
				'capability' => 'job_seeker',
			),
			'company-profile' => array(
				'label'     => __( 'Company Profile', 'jobs' ),
				'icon'      => 'fas fa-building',
				'bg'        => '#fef3c7',
				'color'     => '#92400e',
				'panel'     => 'company-profile',
				'partial'   => 'app-company-profile.php',
				'capability' => 'employer',
			),
			'favorites' => array(
				'label'     => __( 'Favorites', 'jobs' ),
				'icon'      => 'fas fa-heart',
				'bg'        => '#fdf2f8',
				'color'     => '#db2777',
				'panel'     => 'favorites',
				'partial'   => 'app-favorites.php',
				'capability' => 'read',
			),
			'drafts' => array(
				'label'     => __( 'Drafts', 'jobs' ),
				'icon'      => 'fas fa-edit',
				'bg'        => '#f3f4f6',
				'color'     => '#374151',
				'panel'     => 'drafts',
				'partial'   => 'app-drafts.php',
				'capability' => 'read',
			),
			'support' => array(
				'label'     => __( 'Support', 'jobs' ),
				'icon'      => 'fas fa-headset',
				'bg'        => '#f0f9ff',
				'color'     => '#0284c7',
				'panel'     => 'support',
				'partial'   => 'app-support.php',
				'capability' => 'read',
			),
			'admin-advanced' => array(
				'label'     => __( 'Advanced', 'jobs' ),
				'icon'      => 'fas fa-user-shield',
				'bg'        => '#1e293b',
				'color'     => '#fff',
				'panel'     => 'admin-advanced',
				'partial'   => 'app-admin-advanced.php',
				'capability' => 'manage_options',
			),
			'settings-panel' => array(
				'label'     => __( 'Settings', 'jobs' ),
				'icon'      => 'fas fa-user-cog',
				'panel'     => 'settings',
				'partial'   => 'app-settings.php',
				'capability' => 'read',
				'hidden'    => true, // Accessed via profile dropdown
			),
			'activity-logs' => array(
				'label'     => __( 'Activity Logs', 'jobs' ),
				'icon'      => 'fas fa-history',
				'panel'     => 'system',
				'capability' => 'read',
				'hidden'    => true,
			),
			'indexing' => array(
				'label'     => __( 'SEO Indexing', 'jobs' ),
				'icon'      => 'fas fa-search-plus',
				'panel'     => 'indexing',
				'capability' => 'manage_options',
				'hidden'    => true,
			),
			'terms' => array(
				'label'     => __( 'Terms', 'jobs' ),
				'icon'      => 'fas fa-file-contract',
				'bg'        => '#fafafa',
				'color'     => '#718096',
				'link'      => true,
				'url'       => '/terms',
				'capability' => 'read',
			),
			'articles' => array(
				'label'     => __( 'Articles', 'jobs' ),
				'icon'      => 'fas fa-newspaper',
				'bg'        => '#fafafa',
				'color'     => '#718096',
				'link'      => true,
				'url'       => '/blog',
				'capability' => 'read',
			),
		);
	}
}
