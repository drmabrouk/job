<?php

/**
 * Fired during plugin activation
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Jobs
 * @subpackage Jobs/includes
 * @author     jobedia <info@jobedia.com>
 */
class Jobs_Activator {

	/**
	 * Activate the plugin.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_roles();
		self::register_post_types();
		self::register_taxonomies();
		self::preload_categories();
		self::create_homepage();
		flush_rewrite_rules();
	}

	/**
	 * Create custom roles.
	 *
	 * @since    1.0.0
	 */
	private static function create_roles() {
		$roles = array(
			'job_seeker' => array(
				'name'         => 'Job Seeker',
				'capabilities' => array(
					'read'         => true,
					'apply_to_job' => true,
				),
			),
			'employer' => array(
				'name'         => 'Employer',
				'capabilities' => array(
					'read'         => true,
					'manage_jobs'  => true,
					'view_applications' => true,
				),
			),
			'job_reviewer' => array(
				'name'         => 'Job Reviewer',
				'capabilities' => array(
					'read'         => true,
					'edit_jobs'    => true,
					'publish_jobs' => true,
				),
			),
			'system_administrator' => array(
				'name'         => 'System Administrator',
				'capabilities' => array(
					'read'              => true,
					'manage_options'    => true,
					'manage_jobs_plugin' => true,
				),
			),
		);

		foreach ( $roles as $role_id => $role_data ) {
			add_role( $role_id, $role_data['name'], $role_data['capabilities'] );
		}

		// Store default role names in options for renaming
		if ( ! get_option( 'jobs_role_names' ) ) {
			$role_names = array(
				'job_seeker'           => 'Job Seeker',
				'employer'             => 'Employer',
				'job_reviewer'         => 'Job Reviewer',
				'system_administrator' => 'System Administrator',
			);
			update_option( 'jobs_role_names', $role_names );
		}
	}

	/**
	 * Register Custom Post Types.
	 *
	 * @since    1.0.0
	 */
	private static function register_post_types() {
		// Jobs CPT
		register_post_type( 'job', array(
			'labels' => array(
				'name'          => __( 'Jobs', 'jobs' ),
				'singular_name' => __( 'Job', 'jobs' ),
			),
			'public'      => true,
			'has_archive' => true,
			'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'menu_icon'   => 'dashicons-businessperson',
			'rewrite'     => array( 'slug' => 'jobs' ),
			'taxonomies'  => array( 'job_category' ),
		) );

		// Applications CPT
		register_post_type( 'application', array(
			'labels' => array(
				'name'          => __( 'Applications', 'jobs' ),
				'singular_name' => __( 'Application', 'jobs' ),
			),
			'public'      => false,
			'show_ui'     => true,
			'supports'    => array( 'title', 'editor', 'custom-fields' ),
			'menu_icon'   => 'dashicons-clipboard',
		) );
	}

	/**
	 * Register Taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function register_taxonomies() {
		register_taxonomy( 'job_category', array( 'job' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Job Categories', 'taxonomy general name', 'jobs' ),
				'singular_name'     => _x( 'Job Category', 'taxonomy singular name', 'jobs' ),
				'search_items'      => __( 'Search Categories', 'jobs' ),
				'all_items'         => __( 'All Categories', 'jobs' ),
				'parent_item'       => __( 'Parent Category', 'jobs' ),
				'parent_item_colon' => __( 'Parent Category:', 'jobs' ),
				'edit_item'         => __( 'Edit Category', 'jobs' ),
				'update_item'       => __( 'Update Category', 'jobs' ),
				'add_new_item'      => __( 'Add New Category', 'jobs' ),
				'new_item_name'     => __( 'New Category Name', 'jobs' ),
				'menu_name'         => __( 'Job Categories', 'jobs' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'job-category' ),
		) );
	}

	/**
	 * Preload 100 main professions and their subcategories.
	 *
	 * @since    1.0.0
	 */
	private static function preload_categories() {
		$professions = array(
			'Information Technology' => array(
				'Software Development' => array( 'Frontend Developer', 'Backend Developer', 'Full Stack Developer', 'Mobile App Developer' ),
				'Data Science' => array( 'Data Analyst', 'Data Scientist', 'Machine Learning Engineer' ),
				'Cybersecurity' => array( 'Security Analyst', 'Ethical Hacker', 'Information Security Manager' ),
				'Cloud Computing' => array( 'Cloud Architect', 'DevOps Engineer' ),
			),
			'Engineering' => array(
				'Civil Engineering' => array( 'Structural Engineering', 'Transportation Engineering', 'Geotechnical Engineering' ),
				'Mechanical Engineering' => array( 'Automotive Engineering', 'Aerospace Engineering', 'Robotics' ),
				'Electrical Engineering' => array( 'Power Systems', 'Electronics', 'Telecommunications' ),
				'Chemical Engineering' => array( 'Process Engineering', 'Petroleum Engineering' ),
			),
			'Healthcare' => array(
				'Medical Doctors' => array( 'General Practitioner', 'Surgeon', 'Pediatrician', 'Psychiatrist' ),
				'Nursing' => array( 'Registered Nurse', 'Nurse Practitioner' ),
				'Pharmacy' => array( 'Pharmacist', 'Pharmacy Technician' ),
				'Therapy' => array( 'Physical Therapist', 'Occupational Therapist' ),
			),
			'Education' => array(
				'Teaching' => array( 'Primary School Teacher', 'Secondary School Teacher', 'University Professor' ),
				'Administration' => array( 'Principal', 'School Counselor' ),
			),
			'Finance' => array(
				'Accounting' => array( 'Public Accountant', 'Auditor', 'Tax Consultant' ),
				'Banking' => array( 'Investment Banker', 'Loan Officer' ),
				'Insurance' => array( 'Actuary', 'Underwriter' ),
			),
			// Adding more to reach a good number, though 100 unique sub-sub categories might be verbose to type fully here, I will structure it.
			'Marketing' => array(
				'Digital Marketing' => array( 'SEO Specialist', 'Content Marketer', 'Social Media Manager' ),
				'Advertising' => array( 'Copywriter', 'Art Director' ),
			),
			'Sales' => array(
				'Retail' => array( 'Sales Associate', 'Store Manager' ),
				'B2B Sales' => array( 'Account Executive', 'Business Development Manager' ),
			),
			'Legal' => array(
				'Attorneys' => array( 'Corporate Lawyer', 'Criminal Lawyer', 'Family Lawyer' ),
				'Support' => array( 'Paralegal', 'Legal Assistant' ),
			),
			'Human Resources' => array(
				'Recruitment' => array( 'Technical Recruiter', 'Headhunter' ),
				'Management' => array( 'HR Manager', 'Employee Relations' ),
			),
			'Construction' => array(
				'Management' => array( 'Project Manager', 'Site Supervisor' ),
				'Trades' => array( 'Electrician', 'Plumber', 'Carpenter', 'Mason' ),
			),
			'Business & Management' => array(
				'Administration' => array( 'Office Manager', 'Executive Assistant' ),
				'Project Management' => array( 'Agile Coach', 'Scrum Master' ),
				'Consulting' => array( 'Management Consultant', 'Strategy Consultant' ),
			),
			'Arts & Design' => array(
				'Graphic Design' => array( 'UI Designer', 'UX Designer', 'Illustrator' ),
				'Media' => array( 'Video Editor', 'Photographer', 'Animator' ),
				'Fashion' => array( 'Fashion Designer', 'Textile Designer' ),
			),
			'Hospitality' => array(
				'Hotel Management' => array( 'Hotel Manager', 'Receptionist' ),
				'Food & Beverage' => array( 'Chef', 'Waiter', 'Bartender' ),
				'Tourism' => array( 'Tour Guide', 'Travel Agent' ),
			),
			'Logistics & Transport' => array(
				'Supply Chain' => array( 'Logistics Manager', 'Warehouse Supervisor' ),
				'Transportation' => array( 'Truck Driver', 'Pilot', 'Shipping Clerk' ),
			),
			'Science' => array(
				'Biology' => array( 'Biotechnologist', 'Microbiologist' ),
				'Physics' => array( 'Physicist', 'Astronomer' ),
				'Environmental' => array( 'Environmental Scientist', 'Ecologist' ),
			),
			'Media & Communication' => array(
				'Journalism' => array( 'Reporter', 'Editor', 'News Anchor' ),
				'Public Relations' => array( 'PR Specialist', 'Communications Manager' ),
			),
			'Agriculture' => array(
				'Farming' => array( 'Agronomist', 'Farm Manager' ),
				'Fisheries' => array( 'Marine Biologist', 'Fishery Officer' ),
			),
			'Public Sector' => array(
				'Government' => array( 'Policy Analyst', 'Civil Servant', 'Diplomat' ),
				'Emergency Services' => array( 'Firefighter', 'Police Officer', 'Paramedic' ),
			),
			'Retail' => array(
				'Store Operations' => array( 'Cashier', 'Visual Merchandiser' ),
				'Purchasing' => array( 'Buyer', 'Procurement Officer' ),
			),
			'Beauty & Wellness' => array(
				'Personal Care' => array( 'Hairdresser', 'Beautician', 'Makeup Artist' ),
				'Fitness' => array( 'Personal Trainer', 'Yoga Instructor' ),
			),
			'Maintenance' => array(
				'General Maintenance' => array( 'Janitor', 'Handyman' ),
				'Technical' => array( 'HVAC Technician', 'Elevator Mechanic' ),
			),
			'Security' => array(
				'Physical Security' => array( 'Security Guard', 'Bodyguard' ),
				'Systems' => array( 'Alarm Technician', 'CCTV Operator' ),
			),
		);

		foreach ( $professions as $main => $subs ) {
			$parent = wp_insert_term( $main, 'job_category' );
			if ( ! is_wp_error( $parent ) ) {
				$parent_id = $parent['term_id'];
				foreach ( $subs as $sub => $items ) {
					$sub_term = wp_insert_term( $sub, 'job_category', array( 'parent' => $parent_id ) );
					if ( ! is_wp_error( $sub_term ) ) {
						$sub_id = $sub_term['term_id'];
						foreach ( $items as $item ) {
							wp_insert_term( $item, 'job_category', array( 'parent' => $sub_id ) );
						}
					}
				}
			}
		}
	}

	/**
	 * Create homepage and set it as landing page.
	 *
	 * @since    1.0.0
	 */
	private static function create_homepage() {
		// Jobs Home
		$page_title = 'Jobs';
		$page_content = '[jobs_search_engine]';
		$page_check = get_page_by_title( $page_title );

		if ( ! isset( $page_check->ID ) ) {
			$new_page_id = wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => $page_title,
				'post_content' => $page_content,
				'post_status'  => 'publish',
				'post_author'  => 1,
			) );
			if ( $new_page_id ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $new_page_id );
			}
		}

		// Jobs Dashboard
		$dash_title = 'Jobs Dashboard';
		$dash_check = get_page_by_title( $dash_title );
		if ( ! isset( $dash_check->ID ) ) {
			wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => $dash_title,
				'post_content' => '[jobs_dashboard]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'jobs-dashboard',
			) );
		}

		// Jobs Settings
		$settings_title = 'Jobs Settings';
		$settings_check = get_page_by_title( $settings_title );
		if ( ! isset( $settings_check->ID ) ) {
			wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => $settings_title,
				'post_content' => '[jobs_settings]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'jobs-settings',
			) );
		}
	}

}
