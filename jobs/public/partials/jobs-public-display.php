<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/public/partials
 */
?>

<div class="jobs-container">
	<div class="jobs-search-section">
		<div class="jobs-main-search">
			<input type="text" id="jobs-search-input" class="jobs-search-input" placeholder="<?php _e( 'Search jobs by title or keyword...', 'jobs' ); ?>" />
		</div>
		<div class="jobs-location-filters" style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
			<?php
			$categories = get_terms( array(
				'taxonomy' => 'job_category',
				'hide_empty' => false,
				'parent' => 0,
			) );
			?>
			<select id="jobs-category-select" class="jobs-filter-select">
				<option value=""><?php _e( 'All Categories', 'jobs' ); ?></option>
				<?php foreach ( $categories as $cat ) : ?>
					<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
				<?php endforeach; ?>
			</select>
			<select id="jobs-country-select" class="jobs-filter-select">
				<option value=""><?php _e( 'Select Country', 'jobs' ); ?></option>
				<option value="USA">USA</option>
				<option value="UK">UK</option>
				<option value="UAE">UAE</option>
				<option value="Egypt">Egypt</option>
				<option value="Saudi Arabia">Saudi Arabia</option>
			</select>
			<select id="jobs-state-select" class="jobs-filter-select" disabled>
				<option value=""><?php _e( 'Select Country First', 'jobs' ); ?></option>
			</select>
		</div>
	</div>

	<?php if ( get_option( 'jobs_adsense_code' ) ) : ?>
	<div class="jobs-adsense-top" style="margin-bottom: 30px; text-align: center;">
		<?php echo get_option( 'jobs_adsense_code' ); ?>
	</div>
	<?php endif; ?>

	<div id="jobs-grid" class="jobs-grid">
		<!-- Job cards will be loaded here via AJAX or initial load -->
		<?php
		$args = array(
			'post_type'      => 'job',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
		);
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) :
			while ( $query->have_posts() ) : $query->the_post();
				include plugin_dir_path( __FILE__ ) . 'jobs-card-template.php';
			endwhile;
			wp_reset_postdata();
		else :
			echo '<p>' . __( 'No jobs found.', 'jobs' ) . '</p>';
		endif;
		?>
	</div>
</div>
