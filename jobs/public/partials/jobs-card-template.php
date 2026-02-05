<?php
/**
 * Individual job card template
 */
?>

<div class="job-card-refined" data-job-id="<?php the_ID(); ?>">
	<div class="job-card-header" style="display: flex; align-items: center; gap: 20px;">
		<div class="job-card-logo-area">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="job-card-logo">
					<?php the_post_thumbnail( 'thumbnail', array('style' => 'width: 60px; height: 60px; border-radius: 12px;') ); ?>
				</div>
			<?php else: ?>
				<div class="job-card-logo-placeholder" style="width: 60px; height: 60px; background: #f7fafc; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
					<i class="dashicons dashicons-businessperson" style="font-size: 32px; color: #cbd5e0;"></i>
				</div>
			<?php endif; ?>
		</div>

		<div class="job-card-title-area" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
			<h3 class="job-card-title" style="margin: 0; line-height: 1.2;"><a href="<?php the_permalink(); ?>" style="text-decoration: none !important;"><?php the_title(); ?></a></h3>
			<p class="job-card-company" style="margin: 5px 0 0; color: #718096; font-size: 14px; font-weight: 500;"><?php echo get_the_author(); ?></p>
		</div>

		<?php
		$user_id = get_current_user_id();
		$saved = get_user_meta( $user_id, '_jobs_saved_jobs', true ) ?: array();
		$is_saved = in_array( get_the_ID(), $saved );
		?>
		<button class="save-job-btn-refined <?php echo $is_saved ? 'saved' : ''; ?>" data-id="<?php the_ID(); ?>" title="<?php _e('Save for later', 'jobs'); ?>">
			<i class="dashicons dashicons-archive"></i>
		</button>
	</div>

	<div class="job-card-body" style="margin-top: 20px;">
		<div class="job-card-tags" style="margin-bottom: 15px;">
			<?php
			$job_types = get_the_terms( get_the_ID(), 'job_type' );
			if ( $job_types && ! is_wp_error( $job_types ) ) :
				foreach ( $job_types as $type ) : ?>
					<span class="job-tag tag-type"><?php echo esc_html( $type->name ); ?></span>
				<?php endforeach;
			endif;

			$cats = get_the_terms( get_the_ID(), 'job_category' );
			if($cats) : foreach($cats as $c) : ?>
				<span class="job-tag tag-category"><?php echo esc_html($c->name); ?></span>
			<?php endforeach; endif; ?>

			<?php $country = get_post_meta(get_the_ID(), '_job_country', true); if($country): ?>
				<span class="job-tag tag-country"><?php echo esc_html($country); ?></span>
			<?php endif; ?>
		</div>

		<div class="job-card-excerpt-refined">
			<?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
		</div>
	</div>

	<div class="job-card-footer" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f7fafc; padding-top: 15px;">
		<span class="job-card-date" style="color: #a0aec0; font-size: 12px;"><?php printf( __( 'Posted %s ago', 'jobs' ), human_time_diff( get_the_time('U'), current_time('timestamp') ) ); ?></span>

		<div class="job-card-actions" style="display: flex; gap: 10px;">
			<a href="<?php the_permalink(); ?>" class="btn-modern btn-outline-modern" style="padding: 8px 16px; font-size: 13px;">
				<?php _e( 'View Details', 'jobs' ); ?>
			</a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php the_permalink(); ?>?apply=1" class="btn-modern btn-primary-modern" style="padding: 8px 16px; font-size: 13px;">
					<?php _e( 'Apply', 'jobs' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo home_url('/jobs-auth'); ?>" class="btn-modern btn-primary-modern" style="padding: 8px 16px; font-size: 13px;">
					<?php _e( 'Login to Apply', 'jobs' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
