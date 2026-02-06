<?php

/**
 * Indexing Module
 *
 * Handles SEO, Meta tags, and Sitemap generation.
 */
class Jobs_Module_Indexing extends Jobs_Module {

	public function __construct() {
		$this->id = 'indexing';
		$this->name = __( 'Indexing & SEO', 'jobs' );
	}

	public function init() {
		add_action( 'wp_head', array( $this, 'generate_meta_tags' ), 1 );
		add_action( 'init', array( $this, 'add_sitemap_rewrite_rule' ) );
		add_filter( 'query_vars', array( $this, 'add_sitemap_query_var' ) );
		add_action( 'template_redirect', array( $this, 'render_sitemap' ) );
	}

	/**
	 * Generate SEO Meta Tags and JSON-LD Schema.
	 */
	public function generate_meta_tags() {
		if ( is_singular( 'job' ) ) {
			$this->render_job_meta();
		} elseif ( get_query_var( 'job_seeker_profile' ) ) {
			$this->render_profile_meta();
		} elseif ( is_tax( array( 'job_category', 'job_type' ) ) ) {
			$this->render_taxonomy_meta();
		}
	}

	private function render_job_meta() {
		global $post;
		$description = wp_trim_words( $post->post_excerpt ?: $post->post_content, 25 );

		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
		echo '<meta property="og:type" content="article" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />' . "\n";
		echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '" />' . "\n";

		// Enhanced Schema.org JobPosting
		$schema = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'JobPosting',
			'title'    => get_the_title(),
			'description' => wp_kses_post( $post->post_content ),
			'datePosted'  => get_the_date( 'c' ),
			'validThrough' => get_post_meta( get_the_ID(), '_jobs_expiration_date', true ),
			'hiringOrganization' => array(
				'@type' => 'Organization',
				'name'  => get_the_author_meta( 'display_name' ),
				'logo'  => get_option( 'jobs_logo_id' ) ? wp_get_attachment_url( get_option( 'jobs_logo_id' ) ) : '',
			),
			'jobLocation' => array(
				'@type' => 'Place',
				'address' => array(
					'@type' => 'PostalAddress',
					'addressLocality' => get_post_meta( get_the_ID(), '_job_state', true ),
					'addressCountry' => get_post_meta( get_the_ID(), '_job_country', true ),
				),
			),
			'employmentType' => $this->get_employment_type_for_schema(),
		);
		echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
	}

	private function render_profile_meta() {
		$username = get_query_var( 'job_seeker_profile' );
		$user = get_user_by( 'slug', $username );

		if ( ! $user ) return;

		$is_indexed = get_user_meta( $user->ID, '_jobs_profile_indexed', true ) === 'yes';

		if ( ! $is_indexed ) {
			echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
			return;
		}

		$title = sprintf( __( '%s Profile | Jobedia', 'jobs' ), $user->display_name );
		$description = wp_trim_words( $user->description, 25 ) ?: sprintf( __( 'Professional profile of %s on Jobedia.', 'jobs' ), $user->display_name );

		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( home_url( '/job-seeker/' . $username ) ) . '" />' . "\n";

		// Person Schema
		$schema = array(
			'@context' => 'https://schema.org/',
			'@type'    => 'Person',
			'name'     => $user->display_name,
			'description' => $user->description,
			'jobTitle' => get_user_meta( $user->ID, '_job_title', true ),
			'url'      => home_url( '/job-seeker/' . $username ),
		);
		echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
	}

	private function render_taxonomy_meta() {
		$term = get_queried_object();
		if ( ! $term ) return;

		$title = sprintf( __( '%s Jobs | Jobedia', 'jobs' ), $term->name );
		$description = $term->description ?: sprintf( __( 'Browse latest %s job openings on Jobedia.', 'jobs' ), $term->name );

		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
		echo '<link rel="canonical" href="' . esc_url( get_term_link( $term ) ) . '" />' . "\n";
	}

	private function get_employment_type_for_schema() {
		$types = wp_get_post_terms( get_the_ID(), 'job_type', array( 'fields' => 'names' ) );
		return ! empty( $types ) ? $types[0] : 'FULL_TIME';
	}

	/**
	 * Sitemap process.
	 */
	public function add_sitemap_rewrite_rule() {
		add_rewrite_rule( '^jobs-sitemap\.xml$', 'index.php?jobs_sitemap=1', 'top' );
	}

	public function add_sitemap_query_var( $vars ) {
		$vars[] = 'jobs_sitemap';
		return $vars;
	}

	public function render_sitemap() {
		if ( get_query_var( 'jobs_sitemap' ) ) {
			header( 'Content-Type: application/xml; charset=utf-8' );
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			// Jobs
			$jobs = new WP_Query( array( 'post_type' => 'job', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
			while ( $jobs->have_posts() ) {
				$jobs->the_post();
				echo '<url><loc>' . get_permalink() . '</loc><lastmod>' . get_the_modified_date( 'c' ) . '</lastmod><changefreq>daily</changefreq></url>';
			}
			wp_reset_postdata();

			// Indexed Profiles
			$users = get_users( array( 'meta_key' => '_jobs_profile_indexed', 'meta_value' => 'yes' ) );
			foreach ( $users as $user ) {
				echo '<url><loc>' . home_url( '/job-seeker/' . $user->user_nicename ) . '</loc><changefreq>weekly</changefreq></url>';
			}

			// Categories
			$cats = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => true ) );
			foreach ( $cats as $cat ) {
				echo '<url><loc>' . get_term_link( $cat ) . '</loc><changefreq>weekly</changefreq></url>';
			}

			echo '</urlset>';
			exit;
		}
	}
}
