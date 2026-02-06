<?php
/**
 * Provide a public-facing view for the plugin
 */
$logo_id = get_option( 'jobs_logo_id' );
$logo_width = get_option( 'jobs_logo_width', '200' );
?>

<div class="jobs-container">
	<div class="jobs-homepage-header">
		<?php if ( $logo_id ) : ?>
			<div class="jobs-site-logo-container">
				<a href="<?php echo home_url(); ?>" class="logo-link">
					<?php echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'jobs-site-logo', 'style' => 'width:' . $logo_width . 'px; max-width: 100%;' ) ); ?>
				</a>
				<div class="jobs-trust-badge-subtle"><?php _e( 'Trusted by 100,000+ professionals', 'jobs' ); ?></div>
			</div>
		<?php else : ?>
			<div class="jobs-site-logo-container">
				<h1 class="brand-text">Jobedia</h1>
				<div class="jobs-trust-badge-subtle"><?php _e( 'Trusted by 100,000+ professionals', 'jobs' ); ?></div>
			</div>
		<?php endif; ?>

		<div class="jobs-search-section-centered">
			<div class="jobs-smart-search-wrapper">
				<div class="search-input-group">
					<i class="dashicons dashicons-search"></i>
					<input type="text" id="jobs-search-input" class="jobs-search-input-modern" placeholder="<?php _e( 'Job title or profession...', 'jobs' ); ?>" />
				</div>

				<div class="search-filters-row">
					<div class="filter-col">
						<select id="jobs-category-select" class="jobs-filter-select-modern">
							<option value=""><?php _e( 'Select Specialization', 'jobs' ); ?></option>
							<?php
							$categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false, 'parent' => 0 ) );
							foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="filter-col">
						<?php $locs = get_option( 'jobs_global_locations', array() ); ?>
						<select id="jobs-country-select" class="jobs-filter-select-modern">
							<option value=""><?php _e( 'Country', 'jobs' ); ?></option>
							<?php foreach ( array_keys($locs) as $country ) : ?>
								<option value="<?php echo esc_attr($country); ?>"><?php echo esc_html($country); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="filter-col">
						<select id="jobs-state-select" class="jobs-filter-select-modern" disabled>
							<option value=""><?php _e( 'Region', 'jobs' ); ?></option>
						</select>
					</div>
				</div>

				<div class="jobs-category-capsules">
					<?php
					$all_cats = get_terms( array( 'taxonomy' => 'job_category', 'number' => 8 ) );
					foreach ( $all_cats as $cat ) :
					?>
						<span class="job-capsule" data-slug="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html( $cat->name ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ( $ad_top = get_option( 'jobs_ad_top' ) ?: get_option( 'jobs_adsense_code' ) ) : ?>
	<div class="jobs-ad-zone jobs-ad-top">
		<?php echo $ad_top; ?>
	</div>
	<?php endif; ?>

	<div class="jobs-content-wrapper">
		<div class="jobs-main-listing-area">
			<div id="jobs-grid" class="jobs-grid">
				<?php
				$args = array(
					'post_type'      => 'job',
					'post_status'    => 'publish',
					'posts_per_page' => 6,
					'paged'          => 1,
					'orderby'        => 'date',
					'order'          => 'DESC'
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
			<div id="jobs-pagination" class="jobs-pagination-container">
				<?php
				if ( $query->max_num_pages > 1 ) :
					echo '<div class="jobs-numeric-pagination">';
					for ( $i = 1; $i <= $query->max_num_pages; $i++ ) {
						$active = ( $i == 1 ) ? 'active' : '';
						echo '<button class="page-numbers ' . $active . '" data-page="' . $i . '">' . $i . '</button>';
					}
					echo '</div>';
				endif;
				?>
			</div>
		</div>
	</div>

	<?php if ( $ad_bottom = get_option( 'jobs_ad_bottom' ) ) : ?>
	<div class="jobs-ad-zone jobs-ad-bottom">
		<?php echo $ad_bottom; ?>
	</div>
	<?php endif; ?>

	<div class="jobs-legal-footer-line">
		<?php echo esc_html( get_option( 'jobs_footer_text', '© ' . date('Y') . ' Jobedia. All rights reserved.' ) ); ?>
	</div>
</div>
