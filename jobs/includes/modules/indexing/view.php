<div class="indexing-module-view">
	<div class="inline-header">
		<h4><?php _e('Indexing & SEO Status', 'jobs'); ?></h4>
		<p><?php _e('Manage how your content is indexed by search engines.', 'jobs'); ?></p>
	</div>
	<div class="status-summary" style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-top: 20px;">
		<ul style="list-style: none; padding: 0;">
			<li style="margin-bottom: 10px;">
				<strong><?php _e('Dynamic Sitemap:', 'jobs'); ?></strong>
				<a href="<?php echo home_url('/jobs-sitemap.xml'); ?>" target="_blank"><?php echo home_url('/jobs-sitemap.xml'); ?></a>
			</li>
			<li style="margin-bottom: 10px;">
				<strong><?php _e('Job Listings Schema:', 'jobs'); ?></strong>
				<span style="color: #27ae60;"><?php _e('Active (JSON-LD)', 'jobs'); ?></span>
			</li>
			<li>
				<strong><?php _e('Public Profiles SEO:', 'jobs'); ?></strong>
				<span style="color: #27ae60;"><?php _e('Configurable via user settings', 'jobs'); ?></span>
			</li>
		</ul>
	</div>
</div>
