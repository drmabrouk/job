<div class="inline-history">
	<div class="inline-header">
		<h4><?php _e('Job History', 'jobs'); ?></h4>
	</div>
	<div class="history-list">
		<?php
		$q = new WP_Query(array('post_type' => 'job', 'author' => get_current_user_id(), 'post_status' => array('publish', 'draft', 'pending', 'expired')));
		if ($q->have_posts()): while($q->have_posts()): $q->the_post(); ?>
			<div class="history-item-mini" style="padding:12px; border-bottom:1px solid #f1f5f9;">
				<strong><?php the_title(); ?></strong>
				<div style="display:flex; justify-content:space-between; font-size:11px; color:#94a3b8; margin-top:5px;">
					<span><?php echo get_post_status(); ?></span>
					<span><?php echo get_the_date(); ?></span>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); else: ?>
			<p class="empty-msg"><?php _e('No jobs posted yet.', 'jobs'); ?></p>
		<?php endif; ?>
	</div>
</div>
