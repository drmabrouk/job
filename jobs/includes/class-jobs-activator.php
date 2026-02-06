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
		$installed_ver = get_option( 'jobs_version' );

		self::create_roles();
		self::register_post_types();
		self::register_taxonomies();
		self::preload_categories();
		self::preload_job_types();
		self::seed_locations();

		if ( ! $installed_ver ) {
			self::seed_sample_jobs();
		}

		self::create_homepage();
		self::setup_cron();

		// Automated Versioning & Cache Management
		update_option( 'jobs_version', '1.0.0' );
		wp_cache_flush();
		flush_rewrite_rules();
	}

	/**
	 * Setup daily cron.
	 *
	 * @since    1.0.0
	 */
	private static function setup_cron() {
		if ( ! wp_next_scheduled( 'jobs_daily_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'jobs_daily_cron' );
		}
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
	public static function register_post_types() {
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

		// Messages CPT
		register_post_type( 'jobs_message', array(
			'labels' => array(
				'name'          => __( 'Messages', 'jobs' ),
				'singular_name' => __( 'Message', 'jobs' ),
			),
			'public'      => false,
			'show_ui'     => true,
			'supports'    => array( 'title', 'editor', 'author', 'custom-fields' ),
			'menu_icon'   => 'dashicons-email-alt',
		) );
	}

	/**
	 * Register Taxonomies.
	 *
	 * @since    1.0.0
	 */
	public static function register_taxonomies() {
		// Job Type Taxonomy
		register_taxonomy( 'job_type', array( 'job' ), array(
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => _x( 'Job Types', 'taxonomy general name', 'jobs' ),
				'singular_name'     => _x( 'Job Type', 'taxonomy singular name', 'jobs' ),
				'menu_name'         => __( 'Job Types', 'jobs' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'job-type' ),
		) );

		// Job Category Taxonomy
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
	 * Preload Job Types.
	 *
	 * @since    1.0.0
	 */
	private static function preload_job_types() {
		$types = array( 'Full-time', 'Part-time', 'Freelance', 'Contract', 'Internship', 'Remote' );
		foreach ( $types as $type ) {
			wp_insert_term( $type, 'job_type' );
		}
	}

	/**
	 * Preload 100 main professions and their subcategories.
	 *
	 * @since    1.0.0
	 */
	private static function preload_categories() {
		$professions = array(
			'Information Technology' => array(
				'Software Development' => array( 'Frontend Developer', 'Backend Developer', 'Full Stack Developer', 'Mobile App Developer', 'Python Developer', 'Java Developer', 'PHP Developer' ),
				'Data Science' => array( 'Data Analyst', 'Data Scientist', 'Machine Learning Engineer', 'AI Researcher', 'Data Engineer' ),
				'Cybersecurity' => array( 'Security Analyst', 'Ethical Hacker', 'Information Security Manager', 'SOC Analyst', 'Penetration Tester' ),
				'Cloud Computing' => array( 'Cloud Architect', 'DevOps Engineer', 'Site Reliability Engineer', 'Cloud Security Specialist' ),
				'IT Support' => array( 'Help Desk Technician', 'Network Administrator', 'Systems Administrator', 'IT Manager' ),
			),
			'Engineering' => array(
				'Civil Engineering' => array( 'Structural Engineering', 'Transportation Engineering', 'Geotechnical Engineering', 'Water Resources Engineer', 'Urban Planner' ),
				'Mechanical Engineering' => array( 'Automotive Engineering', 'Aerospace Engineering', 'Robotics', 'Manufacturing Engineer', 'Thermal Engineer' ),
				'Electrical Engineering' => array( 'Power Systems', 'Electronics', 'Telecommunications', 'Hardware Engineer', 'Embedded Systems' ),
				'Chemical Engineering' => array( 'Process Engineering', 'Petroleum Engineering', 'Biochemical Engineer' ),
				'Biomedical Engineering' => array( 'Medical Device Designer', 'Clinical Engineer' ),
			),
			'Healthcare' => array(
				'Medical Doctors' => array( 'General Practitioner', 'Surgeon', 'Pediatrician', 'Psychiatrist', 'Cardiologist', 'Dermatologist' ),
				'Nursing' => array( 'Registered Nurse', 'Nurse Practitioner', 'Nursing Assistant' ),
				'Pharmacy' => array( 'Pharmacist', 'Pharmacy Technician', 'Clinical Pharmacist' ),
				'Therapy' => array( 'Physical Therapist', 'Occupational Therapist', 'Speech Therapist' ),
				'Diagnostics' => array( 'Radiologist', 'Lab Technician', 'Pathologist' ),
			),
			'Education' => array(
				'Teaching' => array( 'Primary School Teacher', 'Secondary School Teacher', 'University Professor', 'Special Education Teacher', 'Language Instructor' ),
				'Administration' => array( 'Principal', 'School Counselor', 'Academic Dean' ),
				'Support' => array( 'Librarian', 'Tutor', 'Instructional Designer' ),
			),
			'Finance' => array(
				'Accounting' => array( 'Public Accountant', 'Auditor', 'Tax Consultant', 'Bookkeeper', 'Cost Accountant' ),
				'Banking' => array( 'Investment Banker', 'Loan Officer', 'Branch Manager', 'Teller' ),
				'Insurance' => array( 'Actuary', 'Underwriter', 'Insurance Agent' ),
				'Investments' => array( 'Financial Analyst', 'Portfolio Manager', 'Stockbroker' ),
			),
			'Marketing & Media' => array(
				'Digital Marketing' => array( 'SEO Specialist', 'Content Marketer', 'Social Media Manager', 'PPC Specialist' ),
				'Advertising' => array( 'Copywriter', 'Art Director', 'Media Buyer' ),
				'Public Relations' => array( 'PR Specialist', 'Communications Manager', 'Spokesperson' ),
				'Journalism' => array( 'Reporter', 'Editor', 'News Anchor', 'Photojournalist' ),
			),
			'Arts & Design' => array(
				'Graphic Design' => array( 'UI Designer', 'UX Designer', 'Illustrator', 'Motion Designer' ),
				'Media Production' => array( 'Video Editor', 'Photographer', 'Animator', 'Sound Engineer' ),
				'Fashion' => array( 'Fashion Designer', 'Textile Designer', 'Stylist' ),
				'Interior Design' => array( 'Residential Designer', 'Commercial Designer' ),
			),
			'Business & Management' => array(
				'Administration' => array( 'Office Manager', 'Executive Assistant', 'Data Entry Clerk' ),
				'Project Management' => array( 'Agile Coach', 'Scrum Master', 'Project Coordinator' ),
				'Consulting' => array( 'Management Consultant', 'Strategy Consultant', 'Operations Consultant' ),
				'Human Resources' => array( 'Recruiter', 'HR Manager', 'Employee Relations', 'Comp & Benefits' ),
			),
			'Sales & Retail' => array(
				'Retail' => array( 'Sales Associate', 'Store Manager', 'Visual Merchandiser' ),
				'B2B Sales' => array( 'Account Executive', 'Business Development Manager', 'Sales Engineer' ),
				'Customer Service' => array( 'Customer Success Manager', 'Support Agent' ),
			),
			'Logistics & Construction' => array(
				'Supply Chain' => array( 'Logistics Manager', 'Warehouse Supervisor', 'Purchasing Manager', 'Inventory Clerk', 'Supply Chain Analyst' ),
				'Transportation' => array( 'Truck Driver', 'Pilot', 'Shipping Clerk', 'Dispatcher', 'Flight Attendant', 'Marine Captain' ),
				'Construction' => array( 'Project Manager', 'Site Supervisor', 'Estimator', 'Quantity Surveyor', 'Safety Officer' ),
				'Trades' => array( 'Electrician', 'Plumber', 'Carpenter', 'Mason', 'Welder', 'Painter', 'HVAC Technician' ),
			),
			'Legal & Public Safety' => array(
				'Law' => array( 'Lawyer', 'Paralegal', 'Legal Secretary', 'Judge', 'Corporate Counsel' ),
				'Public Safety' => array( 'Police Officer', 'Firefighter', 'Paramedic', 'Security Guard', 'Correctional Officer' ),
			),
			'Hospitality & Tourism' => array(
				'Hotel' => array( 'Hotel Manager', 'Receptionist', 'Housekeeper', 'Concierge' ),
				'Food Service' => array( 'Chef', 'Waiter', 'Bartender', 'Restaurant Manager', 'Barista' ),
				'Tourism' => array( 'Tour Guide', 'Travel Agent', 'Event Planner' ),
			),
			'Agriculture & Environment' => array(
				'Farming' => array( 'Farmer', 'Agricultural Worker', 'Agronomist', 'Rancher' ),
				'Environment' => array( 'Environmental Scientist', 'Geologist', 'Park Ranger', 'Ecologist' ),
			),
			'Science & Research' => array(
				'Life Sciences' => array( 'Biologist', 'Biotechnologist', 'Microbiologist', 'Geneticist', 'Pharmacologist' ),
				'Physical Sciences' => array( 'Physicist', 'Chemist', 'Astronomer', 'Meteorologist', 'Oceanographer' ),
				'Social Sciences' => array( 'Psychologist', 'Sociologist', 'Anthropologist', 'Archaeologist', 'Economist' ),
			),
			'Media & Communication' => array(
				'Broadcasting' => array( 'Radio Host', 'TV Presenter', 'Camera Operator', 'Sound Technician' ),
				'Writing' => array( 'Technical Writer', 'Scriptwriter', 'Blogger', 'Grant Writer' ),
				'Translation' => array( 'Interpreter', 'Translator', 'Localization Specialist' ),
			),
			'Human Services' => array(
				'Social Work' => array( 'Social Worker', 'Case Manager', 'Child Advocate' ),
				'Counseling' => array( 'Career Counselor', 'Marriage Therapist', 'Addiction Counselor' ),
				'Non-Profit' => array( 'Fundraiser', 'Program Coordinator', 'Volunteer Manager' ),
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
	/**
	 * Seed sample job listings.
	 *
	 * @since    1.0.0
	 */
	/**
	 * Seed location data.
	 *
	 * @since    1.0.0
	 */
	private static function seed_locations() {
		$locations = array(
			'Algeria' => array( 'Algiers', 'Oran', 'Constantine', 'Annaba', 'Blida' ),
			'Bahrain' => array( 'Manama', 'Riffa', 'Muharraq', 'Hamad Town' ),
			'Canada' => array( 'Ontario', 'Quebec', 'British Columbia', 'Alberta', 'Manitoba' ),
			'Egypt' => array( 'Cairo', 'Alexandria', 'Giza', 'Luxor', 'Aswan', 'Port Said', 'Suez', 'Mansoura' ),
			'Iraq' => array( 'Baghdad', 'Basra', 'Mosul', 'Erbil', 'Sulaymaniyah' ),
			'Jordan' => array( 'Amman', 'Zarqa', 'Irbid', 'Aqaba', 'Madaba' ),
			'Kuwait' => array( 'Kuwait City', 'Al Ahmadi', 'Hawalli', 'Salmiya' ),
			'Lebanon' => array( 'Beirut', 'Tripoli', 'Sidon', 'Tyre', 'Byblos' ),
			'Libya' => array( 'Tripoli', 'Benghazi', 'Misrata', 'Bayda' ),
			'Morocco' => array( 'Casablanca', 'Rabat', 'Marrakesh', 'Fez', 'Tangier', 'Agadir' ),
			'Oman' => array( 'Muscat', 'Salalah', 'Sohar', 'Nizwa' ),
			'Palestine' => array( 'Jerusalem', 'Gaza City', 'Ramallah', 'Nablus', 'Hebron' ),
			'Qatar' => array( 'Doha', 'Al Wakrah', 'Al Khor', 'Al Rayyan' ),
			'Saudi Arabia' => array( 'Riyadh', 'Jeddah', 'Mecca', 'Medina', 'Dammam', 'Khobar', 'Abha', 'Tabuk' ),
			'Sudan' => array( 'Khartoum', 'Omdurman', 'Port Sudan' ),
			'Syria' => array( 'Damascus', 'Aleppo', 'Homs', 'Latakia' ),
			'Tunisia' => array( 'Tunis', 'Sfax', 'Sousse', 'Kairouan' ),
			'UAE' => array( 'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Fujairah', 'Ras Al Khaimah', 'Al Ain' ),
			'UK' => array( 'London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow', 'Liverpool', 'Edinburgh' ),
			'USA' => array( 'California', 'New York', 'Texas', 'Florida', 'Washington', 'Illinois', 'Georgia', 'Arizona' ),
			'Yemen' => array( 'Sana\'a', 'Aden', 'Taiz', 'Al Hudaydah' ),
		);
		ksort($locations);
		update_option( 'jobs_global_locations', $locations );
	}

	private static function seed_sample_jobs() {
		$jobs = array(
			array( 'title' => 'Senior Frontend Developer', 'cat' => 'Software Development', 'type' => 'Full-time', 'country' => 'USA', 'state' => 'California' ),
			array( 'title' => 'Structural Engineer', 'cat' => 'Civil Engineering', 'type' => 'Full-time', 'country' => 'UK', 'state' => 'London' ),
			array( 'title' => 'Registered Nurse', 'cat' => 'Nursing', 'type' => 'Full-time', 'country' => 'UAE', 'state' => 'Dubai' ),
			array( 'title' => 'Marketing Manager', 'cat' => 'Digital Marketing', 'type' => 'Contract', 'country' => 'USA', 'state' => 'New York' ),
			array( 'title' => 'Graphic Designer', 'cat' => 'Graphic Design', 'type' => 'Freelance', 'country' => 'Egypt', 'state' => 'Cairo' ),
			array( 'title' => 'Data Scientist', 'cat' => 'Data Science', 'type' => 'Full-time', 'country' => 'USA', 'state' => 'Texas' ),
			array( 'title' => 'Project Manager', 'cat' => 'Project Management', 'type' => 'Full-time', 'country' => 'UK', 'state' => 'Manchester' ),
			array( 'title' => 'Sales Associate', 'cat' => 'Retail', 'type' => 'Part-time', 'country' => 'UAE', 'state' => 'Abu Dhabi' ),
			array( 'title' => 'Accountant', 'cat' => 'Accounting', 'type' => 'Full-time', 'country' => 'Saudi Arabia', 'state' => 'Riyadh' ),
			array( 'title' => 'Legal Assistant', 'cat' => 'Support', 'type' => 'Internship', 'country' => 'USA', 'state' => 'Florida' ),
		);

		foreach ( $jobs as $data ) {
			$job_id = wp_insert_post( array(
				'post_title'   => $data['title'],
				'post_content' => 'Sample job description for ' . $data['title'] . '.',
				'post_status'  => 'publish',
				'post_type'    => 'job',
				'post_author'  => 1,
			) );

			if ( $job_id ) {
				$term = get_term_by( 'name', $data['cat'], 'job_category' );
				if ( $term ) wp_set_object_terms( $job_id, $term->term_id, 'job_category' );

				$type_term = get_term_by( 'name', $data['type'], 'job_type' );
				if ( $type_term ) wp_set_object_terms( $job_id, $type_term->term_id, 'job_type' );

				update_post_meta( $job_id, '_job_country', $data['country'] );
				update_post_meta( $job_id, '_job_state', $data['state'] );
				update_post_meta( $job_id, '_jobs_expiration_date', date( 'Y-m-d H:i:s', strtotime( '+ 50 days' ) ) );
			}
		}
	}

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
		} else {
			wp_update_post( array( 'ID' => $page_check->ID, 'post_content' => $page_content ) );
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
		} else {
			wp_update_post( array( 'ID' => $dash_check->ID, 'post_content' => '[jobs_dashboard]' ) );
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
		} else {
			wp_update_post( array( 'ID' => $settings_check->ID, 'post_content' => '[jobs_settings]' ) );
		}

		// Auth Page
		$auth_title = 'Join Us';
		$auth_check = get_page_by_title( $auth_title );
		if ( ! isset( $auth_check->ID ) ) {
			wp_insert_post( array(
				'post_type'    => 'page',
				'post_title'   => $auth_title,
				'post_content' => '[jobs_auth]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'jobs-auth',
			) );
		} else {
			wp_update_post( array( 'ID' => $auth_check->ID, 'post_content' => '[jobs_auth]' ) );
		}
	}

}
