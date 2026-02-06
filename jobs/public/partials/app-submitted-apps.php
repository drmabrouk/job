<div class="inline-submitted">
	<div class="inline-header">
		<h4><?php _e('Submitted Applications', 'jobs'); ?></h4>
	</div>
	<div class="submitted-list">
		<?php
		$q = new WP_Query(array('post_type' => 'application', 'author' => get_current_user_id()));
		if ($q->have_posts()): while($q->have_posts()): $q->the_post();
			$job_id = get_post_meta(get_the_ID(), '_job_id', true);
		?>
			<div class="submitted-item-mini" style="padding:12px; border-bottom:1px solid #f1f5f9;">
				<strong><?php echo get_the_title($job_id); ?></strong>
				<div style="display:flex; justify-content:space-between; font-size:11px; color:#94a3b8; margin-top:5px;">
					<span><?php echo get_post_status(); ?></span>
					<span><?php echo get_the_date(); ?></span>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); else: ?>
			<p class="empty-msg"><?php _e('No applications submitted yet.', 'jobs'); ?></p>
		<?php endif; ?>
	</div>
</div>
