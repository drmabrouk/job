<?php
/**
 * Individual job card template
 *
 * @link       https://jobedia.com
 * @since      1.0.0
 *
 * @package    Jobs
 * @subpackage Jobs/public/partials
 */
?>

<div class="job-card-refined" data-job-id="<?php the_ID(); ?>">
	<div class="job-card-header">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="job-card-logo">
				<?php the_post_thumbnail( 'thumbnail' ); ?>
			</div>
		<?php else: ?>
			<div class="job-card-logo-placeholder">
				<i class="dashicons dashicons-businessperson"></i>
			</div>
		<?php endif; ?>

		<div class="job-card-title-area">
			<h3 class="job-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p class="job-card-company"><?php echo get_the_author(); ?></p>
		</div>

		<?php
		$user_id = get_current_user_id();
		$saved = get_user_meta( $user_id, '_jobs_saved_jobs', true ) ?: array();
		$is_saved = in_array( get_the_ID(), $saved );
		?>
		<button class="save-job-btn-modern <?php echo $is_saved ? 'saved' : ''; ?>" data-id="<?php the_ID(); ?>" title="<?php _e('Save Job', 'jobs'); ?>">
			<i class="dashicons <?php echo $is_saved ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?>"></i>
		</button>
	</div>

	<div class="job-card-body">
		<div class="job-card-tags">
			<?php
			$job_types = get_the_terms( get_the_ID(), 'job_type' );
			if ( $job_types && ! is_wp_error( $job_types ) ) :
				foreach ( $job_types as $type ) : ?>
					<span class="job-tag tag-type"><?php echo esc_html( $type->name ); ?></span>
				<?php endforeach;
			endif;

			$location = get_post_meta( get_the_ID(), '_job_location', true );
			if ( $location ) : ?>
				<span class="job-tag tag-location"><i class="dashicons dashicons-location"></i> <?php echo esc_html( $location ); ?></span>
			<?php endif; ?>
		</div>

		<div class="job-card-excerpt">
			<?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
		</div>
	</div>

	<div class="job-card-footer">
		<span class="job-card-date"><?php printf( __( 'Posted %s ago', 'jobs' ), human_time_diff( get_the_time('U'), current_time('timestamp') ) ); ?></span>

		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php the_permalink(); ?>" class="btn-modern btn-primary-modern">
				<?php _e( 'Apply Now', 'jobs' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn-modern btn-outline-modern">
				<?php _e( 'Login to Apply', 'jobs' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
