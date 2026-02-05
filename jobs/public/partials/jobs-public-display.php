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

$logo_id = get_option( 'jobs_logo_id' );
$logo_width = get_option( 'jobs_logo_width', '200' );
$logo_margin = get_option( 'jobs_logo_margin', '40' );
?>

<div class="jobs-container">
	<div class="jobs-homepage-header">
		<div class="jobs-welcome-badge"><?php _e( 'Trusted by 10,000+ Professionals', 'jobs' ); ?></div>
		<?php if ( $logo_id ) : ?>
			<div class="jobs-site-logo-container" style="margin-bottom: <?php echo esc_attr($logo_margin); ?>px;">
				<a href="<?php echo home_url(); ?>">
					<?php echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'jobs-site-logo', 'style' => 'width:' . $logo_width . 'px;' ) ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="jobs-search-section-centered">
			<div class="jobs-smart-search-wrapper">
				<div class="search-main-icon">
					<i class="dashicons dashicons-search"></i>
				</div>
				<div class="search-input-group">
					<input type="text" id="jobs-search-input" class="jobs-search-input-modern" placeholder="<?php _e( 'Job title or profession...', 'jobs' ); ?>" />
				</div>

				<div class="search-filters-row">
					<div class="filter-col">
						<select id="jobs-category-select" class="jobs-filter-select-modern">
							<option value=""><?php _e( 'Select Category', 'jobs' ); ?></option>
							<?php
							$categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false, 'parent' => 0 ) );
							foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="filter-col">
						<?php $types = get_terms( array( 'taxonomy' => 'job_type', 'hide_empty' => false ) ); ?>
						<select id="jobs-type-select" class="jobs-filter-select-modern">
							<option value=""><?php _e( 'Specialization/Type', 'jobs' ); ?></option>
							<?php foreach ( $types as $type ) : ?>
								<option value="<?php echo esc_attr( $type->slug ); ?>"><?php echo esc_html( $type->name ); ?></option>
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
						$colors = array( '#E3F2FD', '#F1F8E9', '#FFFDE7', '#F3E5F5', '#E8EAF6', '#FBE9E7' );
						$bg = $colors[array_rand($colors)];
					?>
						<span class="job-capsule" style="background-color: <?php echo $bg; ?>;" data-slug="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html( $cat->name ); ?></span>
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
		<div id="jobs-grid" class="jobs-grid">
			<?php
			// Initial random listings
			$args = array(
				'post_type'      => 'job',
				'post_status'    => 'publish',
				'posts_per_page' => 10,
			);

			if ( ! empty( $geo_country ) ) {
				$args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key' => '_job_country',
						'value' => $geo_country,
						'compare' => '=',
					),
					array(
						'key' => '_job_country',
						'compare' => 'EXISTS',
					),
				);
				$args['orderby'] = array(
					'meta_value' => 'DESC',
					'rand' => 'ASC',
				);
			} else {
				$args['orderby'] = 'rand';
			}
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

		<?php if ( $ad_sidebar = get_option( 'jobs_ad_sidebar' ) ) : ?>
		<aside class="jobs-ad-zone jobs-ad-sidebar">
			<?php echo $ad_sidebar; ?>
		</aside>
		<?php endif; ?>
	</div>

	<?php if ( $ad_bottom = get_option( 'jobs_ad_bottom' ) ) : ?>
	<div class="jobs-ad-zone jobs-ad-bottom">
		<?php echo $ad_bottom; ?>
	</div>
	<?php endif; ?>

	<?php if ( is_user_logged_in() ) : ?>
		<hr>
		<?php include plugin_dir_path( __FILE__ ) . 'jobs-recommendations.php'; ?>
	<?php endif; ?>
</div>
