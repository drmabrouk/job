<?php
/**
 * Saved Jobs and Followed Employers
 */
$user_id = get_current_user_id();
$saved_jobs = get_user_meta( $user_id, '_jobs_saved_jobs', true ) ?: array();
$followed_employers = get_user_meta( $user_id, '_jobs_followed_employers', true ) ?: array();
?>
<div class="jobs-saved-items">
	<h2><?php _e( 'Your Saved Items', 'jobs' ); ?></h2>

	<div class="saved-section">
		<h3><?php _e( 'Saved Jobs', 'jobs' ); ?></h3>
		<?php if ( ! empty( $saved_jobs ) ) : ?>
			<div class="jobs-grid">
				<?php
				$query = new WP_Query( array(
					'post_type' => 'job',
					'post__in'  => $saved_jobs,
				) );
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) : $query->the_post();
						include plugin_dir_path( __FILE__ ) . 'jobs-card-template.php';
					endwhile;
					wp_reset_postdata();
				endif;
				?>
			</div>
		<?php else : ?>
			<p><?php _e( 'You haven\'t saved any jobs yet.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>

	<hr>

	<div class="saved-section">
		<h3><?php _e( 'Followed Employers', 'jobs' ); ?></h3>
		<?php if ( ! empty( $followed_employers ) ) : ?>
			<ul class="employer-list">
				<?php foreach ( $followed_employers as $emp_id ) :
					$emp = get_userdata( $emp_id );
					if ( $emp ) :
				?>
					<li>
						<strong><?php echo esc_html( $emp->display_name ); ?></strong>
						<button class="button follow-employer-btn followed" data-id="<?php echo $emp_id; ?>"><?php _e( 'Unfollow', 'jobs' ); ?></button>
					</li>
				<?php endif; endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php _e( 'You aren\'t following any employers yet.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>
</div>
