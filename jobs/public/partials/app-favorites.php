<div class="inline-favorites">
	<div class="inline-header">
		<h4><?php _e('Favorites', 'jobs'); ?></h4>
		<p><?php _e('Your last 5 saved jobs.', 'jobs'); ?></p>
	</div>
	<div class="favorites-list">
		<?php
		$saved = get_user_meta(get_current_user_id(), '_jobs_saved_jobs', true) ?: array();
		if ( !empty($saved) ) :
			$args = array('post_type' => 'job', 'post__in' => array_slice(array_reverse($saved), 0, 5), 'orderby' => 'post__in');
			$q = new WP_Query($args);
			while($q->have_posts()): $q->the_post(); ?>
				<div class="fav-item-mini">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					<span><?php echo get_the_date(); ?></span>
				</div>
			<?php endwhile; wp_reset_postdata();
		else: ?>
			<p class="empty-msg"><?php _e('No saved jobs yet.', 'jobs'); ?></p>
		<?php endif; ?>
	</div>
</div>
