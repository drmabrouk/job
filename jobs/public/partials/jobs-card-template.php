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

<div class="job-card" data-job-id="<?php the_ID(); ?>">
	<div class="job-content">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="job-thumbnail" style="margin-bottom: 15px;">
				<?php the_post_thumbnail( 'medium' ); ?>
			</div>
		<?php endif; ?>

		<h3><?php the_title(); ?></h3>

		<div class="job-meta">
			<span class="job-date"><?php echo get_the_date(); ?></span>
			<?php
			$location = get_post_meta( get_the_ID(), '_job_location', true );
			if ( $location ) : ?>
				<span class="job-location"> | <?php echo esc_html( $location ); ?></span>
			<?php endif; ?>
		</div>

		<div class="job-excerpt">
			<?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
		</div>
	</div>

	<?php if ( is_user_logged_in() ) : ?>
		<a href="<?php the_permalink(); ?>" class="btn-apply">
			<?php _e( 'Apply Now', 'jobs' ); ?>
		</a>
	<?php else : ?>
		<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn-apply">
			<?php _e( 'Login to Apply', 'jobs' ); ?>
		</a>
	<?php endif; ?>
</div>
