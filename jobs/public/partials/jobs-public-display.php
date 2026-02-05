<?php
/**
 * Provide a public-facing view for the plugin
 */
$logo_id = get_option( 'jobs_logo_id' );
$logo_width = get_option( 'jobs_logo_width', '200' );
?>

<div class="jobs-container">
	<div class="jobs-homepage-header" style="text-align: center; padding: 60px 0;">
		<?php if ( $logo_id ) : ?>
			<div class="jobs-site-logo-container" style="margin-bottom: 20px;">
				<a href="<?php echo home_url(); ?>">
					<?php echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'jobs-site-logo', 'style' => 'width:' . $logo_width . 'px;' ) ); ?>
				</a>
				<div class="jobs-trust-badge-subtle" style="font-size: 13px; color: #a0aec0; margin-top: 10px;"><?php _e( 'Trusted by 100,000+ professionals', 'jobs' ); ?></div>
			</div>
		<?php else : ?>
			<div class="jobs-site-logo-container">
				<h1 class="brand-text" style="font-size: 48px; color: var(--primary-color); margin-bottom: 0;">Jobedia</h1>
				<div class="jobs-trust-badge-subtle" style="font-size: 13px; color: #a0aec0; margin-top: 5px;"><?php _e( 'Trusted by 100,000+ professionals', 'jobs' ); ?></div>
			</div>
		<?php endif; ?>

		<div class="jobs-search-section-centered" style="margin-top: 50px; display: flex; justify-content: center;">
			<div class="jobs-smart-search-wrapper" style="width: 100%; max-width: 900px; background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 30px 60px rgba(29, 52, 105, 0.1);">
				<div class="search-main-icon" style="margin-bottom: 25px;">
					<i class="dashicons dashicons-search" style="font-size: 64px; height: 64px; width: 64px; color: var(--primary-color);"></i>
				</div>
				<div class="search-input-group">
					<input type="text" id="jobs-search-input" class="jobs-search-input-modern" placeholder="<?php _e( 'Job title or profession...', 'jobs' ); ?>" style="width: 100%; text-align: center; padding: 20px; font-size: 20px; border: 1px solid #e2e8f0; border-radius: 12px;" />
				</div>

				<div class="search-filters-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 30px;">
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

				<div class="jobs-category-capsules" style="margin-top: 25px; display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;">
					<?php
					$all_cats = get_terms( array( 'taxonomy' => 'job_category', 'number' => 8 ) );
					foreach ( $all_cats as $cat ) :
						$colors = array( '#E3F2FD', '#F1F8E9', '#FFFDE7', '#F3E5F5', '#E8EAF6', '#FBE9E7' );
						$bg = $colors[array_rand($colors)];
					?>
						<span class="job-capsule" style="background-color: <?php echo $bg; ?>; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; cursor: pointer;" data-slug="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html( $cat->name ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<?php if ( $ad_top = get_option( 'jobs_ad_top' ) ?: get_option( 'jobs_adsense_code' ) ) : ?>
	<div class="jobs-ad-zone jobs-ad-top" style="margin: 30px 0; text-align: center;">
		<?php echo $ad_top; ?>
	</div>
	<?php endif; ?>

	<div class="jobs-content-wrapper">
		<div id="jobs-grid" class="jobs-grid">
			<?php
			$args = array(
				'post_type'      => 'job',
				'post_status'    => 'publish',
				'posts_per_page' => 12,
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
	</div>

	<?php if ( $ad_bottom = get_option( 'jobs_ad_bottom' ) ) : ?>
	<div class="jobs-ad-zone jobs-ad-bottom" style="margin: 30px 0; text-align: center;">
		<?php echo $ad_bottom; ?>
	</div>
	<?php endif; ?>
</div>
