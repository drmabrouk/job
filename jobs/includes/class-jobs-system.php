<?php

/**
 * Centralized System Map for the Jobs plugin.
 */
class Jobs_System {

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

	public static function get_applications() {
		return array(
			'post-job' => array(
				'label'     => __( 'Post a Job', 'jobs' ),
				'icon'      => 'fas fa-plus',
				'bg'        => '#e0f2fe',
				'color'     => '#0369a1',
				'panel'     => 'post-job',
				'capability' => 'employer',
			),
			'job-history' => array(
				'label'     => __( 'Job History', 'jobs' ),
				'icon'      => 'fas fa-history',
				'bg'        => '#f0fdf4',
				'color'     => '#166534',
				'panel'     => 'job-history',
				'capability' => 'employer',
			),
			'manage-apps' => array(
				'label'     => __( 'Job Requests', 'jobs' ),
				'icon'      => 'fas fa-inbox',
				'bg'        => '#fff7ed',
				'color'     => '#ea580c',
				'panel'     => 'manage-apps',
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
				'capability' => 'job_seeker',
			),
			'cv-resume' => array(
				'label'     => __( 'CV / Resume', 'jobs' ),
				'icon'      => 'fas fa-file-invoice',
				'bg'        => '#fff1f2',
				'color'     => '#e11d48',
				'panel'     => 'cv-resume',
				'capability' => 'job_seeker',
			),
			'company-profile' => array(
				'label'     => __( 'Company Profile', 'jobs' ),
				'icon'      => 'fas fa-building',
				'bg'        => '#fef3c7',
				'color'     => '#92400e',
				'panel'     => 'company-profile',
				'capability' => 'employer',
			),
			'favorites' => array(
				'label'     => __( 'Favorites', 'jobs' ),
				'icon'      => 'fas fa-heart',
				'bg'        => '#fdf2f8',
				'color'     => '#db2777',
				'panel'     => 'favorites',
				'capability' => 'read',
			),
			'drafts' => array(
				'label'     => __( 'Draft Manager', 'jobs' ),
				'icon'      => 'fas fa-edit',
				'bg'        => '#f3f4f6',
				'color'     => '#374151',
				'panel'     => 'drafts',
				'capability' => 'read',
			),
			'support' => array(
				'label'     => __( 'Support', 'jobs' ),
				'icon'      => 'fas fa-headset',
				'bg'        => '#f0f9ff',
				'color'     => '#0284c7',
				'panel'     => 'support',
				'capability' => 'read',
			),
			'admin-advanced' => array(
				'label'     => __( 'Advanced', 'jobs' ),
				'icon'      => 'fas fa-user-shield',
				'bg'        => '#1e293b',
				'color'     => '#fff',
				'panel'     => 'admin-advanced',
				'capability' => 'manage_options',
			),
			'settings' => array(
				'label'     => __( 'Settings', 'jobs' ),
				'icon'      => 'fas fa-user-cog',
				'panel'     => 'settings',
				'capability' => 'read',
				'hidden'    => true,
			),
			'activity-logs' => array(
				'label'     => __( 'Activity Logs', 'jobs' ),
				'icon'      => 'fas fa-history',
				'panel'     => 'activity-logs',
				'capability' => 'read',
				'hidden'    => true,
			),
			'indexing' => array(
				'label'     => __( 'Indexing & SEO', 'jobs' ),
				'icon'      => 'fas fa-search-plus',
				'bg'        => '#f8fafc',
				'color'     => '#475569',
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
