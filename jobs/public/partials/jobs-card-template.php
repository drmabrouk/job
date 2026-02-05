<?php
/**
 * Individual job card template
 */
$job_id = get_the_ID();
$user_id = get_current_user_id();
$saved = get_user_meta( $user_id, '_jobs_saved_jobs', true ) ?: array();
$is_saved = in_array( $job_id, $saved );
?>

<div class="job-card-refined" data-job-id="<?php echo $job_id; ?>">
	<div class="job-card-header">
		<div class="job-card-logo-area">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="job-card-logo">
					<?php the_post_thumbnail( 'thumbnail' ); ?>
				</div>
			<?php else: ?>
				<div class="job-card-logo-placeholder">
					<i class="fas fa-building"></i>
				</div>
			<?php endif; ?>
		</div>

		<div class="job-card-title-area">
			<h3 class="job-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p class="job-card-company"><?php echo get_the_author(); ?></p>
		</div>

		<button class="save-job-btn-refined <?php echo $is_saved ? 'saved' : ''; ?>" data-id="<?php echo $job_id; ?>" title="<?php _e('Save for later', 'jobs'); ?>">
			<i class="<?php echo $is_saved ? 'fas' : 'far'; ?> fa-bookmark"></i>
		</button>
	</div>

	<div class="job-card-body">
		<div class="job-card-tags">
			<?php
			$job_types = get_the_terms( $job_id, 'job_type' );
			if ( $job_types && ! is_wp_error( $job_types ) ) :
				foreach ( $job_types as $type ) : ?>
					<span class="job-tag tag-type"><?php echo esc_html( $type->name ); ?></span>
				<?php endforeach;
			endif;

			$cats = get_the_terms( $job_id, 'job_category' );
			if($cats && ! is_wp_error($cats)) : foreach(array_slice($cats, 0, 1) as $c) : ?>
				<span class="job-tag tag-category"><?php echo esc_html($c->name); ?></span>
			<?php endforeach; endif; ?>

			<?php $country = get_post_meta($job_id, '_job_country', true); if($country): ?>
				<span class="job-tag tag-country"><i class="fas fa-map-marker-alt"></i> <?php echo esc_html($country); ?></span>
			<?php endif; ?>
		</div>

		<div class="job-card-excerpt-refined">
			<?php echo wp_trim_words( get_the_excerpt(), 18 ); ?>
		</div>
	</div>

	<div class="job-card-footer">
		<span class="job-card-date"><i class="far fa-clock"></i> <?php printf( __( '%s ago', 'jobs' ), human_time_diff( get_the_time('U'), current_time('timestamp') ) ); ?></span>

		<div class="job-card-actions">
			<a href="<?php the_permalink(); ?>" class="btn-card-action btn-outline">
				<?php _e( 'Details', 'jobs' ); ?>
			</a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php the_permalink(); ?>?apply=1" class="btn-card-action btn-primary">
					<?php _e( 'Apply', 'jobs' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo home_url('/jobs-auth'); ?>" class="btn-card-action btn-primary">
					<?php _e( 'Join to Apply', 'jobs' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</div>
