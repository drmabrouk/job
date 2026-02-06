<div class="inline-drafts">
	<div class="inline-header">
		<h4><?php _e('Draft Manager', 'jobs'); ?></h4>
	</div>
	<div class="drafts-list">
		<?php
		$q = new WP_Query(array('post_type' => 'job', 'author' => get_current_user_id(), 'post_status' => 'draft'));
		if ($q->have_posts()): while($q->have_posts()): $q->the_post(); ?>
			<div class="draft-item-pastel" style="padding:15px; background:#f5f3ff; border-radius:12px; margin-bottom:10px;">
				<strong style="color:#5b21b6;"><?php the_title(); ?></strong>
				<p style="margin:5px 0 0; font-size:11px; color:#7c3aed;"><?php _e('Last edited:', 'jobs'); ?> <?php echo get_the_modified_date(); ?></p>
			</div>
		<?php endwhile; wp_reset_postdata(); else: ?>
			<p class="empty-msg"><?php _e('No drafts found.', 'jobs'); ?></p>
		<?php endif; ?>
	</div>
</div>
