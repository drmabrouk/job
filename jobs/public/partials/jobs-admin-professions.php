<?php
/**
 * Frontend Profession & Specialty Management for Administrators
 */
$categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false, 'parent' => 0 ) );

?>
<div class="account-section jobs-admin-professions">
	<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h3><?php _e( 'Profession & Specialty Hierarchy', 'jobs' ); ?></h3>
		<button class="jobs-button"><?php _e( 'Add Main Profession', 'jobs' ); ?></button>
	</div>

	<div class="professions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
		<?php foreach ( $categories as $cat ) :
			$children = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false, 'parent' => $cat->term_id ) );
		?>
			<div class="profession-card" style="border: 1px solid #eee; border-radius: 12px; padding: 20px; background: #fff;">
				<div class="card-title" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
					<h4 style="margin: 0; color: var(--primary-color);"><?php echo esc_html($cat->name); ?></h4>
					<div class="actions">
						<i class="dashicons dashicons-plus-alt" style="color: #27ae60; cursor: pointer;"></i>
						<i class="dashicons dashicons-trash" style="color: #e74c3c; cursor: pointer; margin-left: 5px;"></i>
					</div>
				</div>
				<div class="specialties-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
					<?php if($children) : foreach($children as $child) : ?>
						<span class="job-tag tag-type" style="font-size: 11px;">
							<?php echo esc_html($child->name); ?>
							<i class="dashicons dashicons-no-alt" style="font-size: 12px; height: 12px; width: 12px; cursor: pointer; margin-left: 4px;"></i>
						</span>
					<?php endforeach; else : ?>
						<small style="color: #999;"><?php _e( 'No specialties defined', 'jobs' ); ?></small>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
