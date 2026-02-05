<?php
/**
 * Professional Single Job Listing Template
 */
get_header();
$job_id = get_the_ID();
$author_id = get_post_field( 'post_author', $job_id );
$author = get_userdata( $author_id );
$location = get_post_meta( $job_id, '_job_location', true );
$country = get_post_meta( $job_id, '_job_country', true );
$state = get_post_meta( $job_id, '_job_state', true );
$apply_url = get_post_meta( $job_id, '_jobs_external_url', true );
$apply_email = get_post_meta( $job_id, '_jobs_external_email', true );
$is_quick_apply = get_post_meta( $job_id, '_jobs_quick_apply', true ) === 'yes';

?>
<div class="jobs-single-listing">
	<header class="job-header" style="background: var(--primary-color); color: #fff; padding: 60px 0; margin-bottom: 50px; border-radius: 0 0 40px 40px;">
		<div class="jobs-container" style="display: flex; justify-content: space-between; align-items: flex-end;">
			<div class="job-header-main">
				<div class="job-meta-top" style="margin-bottom: 20px; display: flex; gap: 15px;">
					<?php
					$types = get_the_terms( $job_id, 'job_type' );
					if($types) foreach($types as $t) echo '<span class="job-tag" style="background: rgba(255,255,255,0.2); color: #fff;">' . $t->name . '</span>';
					?>
				</div>
				<h1 style="margin: 0; font-size: 42px; font-weight: 700; color: #fff; text-decoration: none !important;"><?php the_title(); ?></h1>
				<p style="font-size: 18px; opacity: 0.9; margin-top: 10px;">
					<?php if($apply_url || $apply_email): ?>
						<i class="dashicons dashicons-admin-site"></i> <?php _e( 'Sourced via Jobedia', 'jobs' ); ?>
					<?php else: ?>
						<i class="dashicons dashicons-businessperson"></i> <?php echo $author ? $author->display_name : 'Company'; ?>
					<?php endif; ?>
					<span style="margin: 0 15px; opacity: 0.5;">|</span>
					<i class="dashicons dashicons-location"></i> <?php echo esc_html("$location, $state, $country"); ?>
				</p>
			</div>
			<div class="job-header-actions">
				<?php if ( $is_quick_apply && is_user_logged_in() ) : ?>
					<form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
						<input type="hidden" name="action" value="jobs_submit_application">
						<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
						<input type="hidden" name="quick_apply" value="1">
						<?php wp_nonce_field( 'jobs_apply_nonce', 'jobs_nonce' ); ?>
						<button type="submit" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 15px 40px; font-size: 18px;"><?php _e( 'Quick Apply', 'jobs' ); ?></button>
					</form>
				<?php elseif ( $apply_url ) : ?>
					<a href="<?php echo esc_url($apply_url); ?>" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 15px 40px; font-size: 18px;" target="_blank"><?php _e( 'Apply on Company Site', 'jobs' ); ?></a>
				<?php elseif ( $apply_email ) : ?>
					<a href="mailto:<?php echo esc_attr($apply_email); ?>?subject=Application for <?php echo rawurlencode(get_the_title()); ?>" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 15px 40px; font-size: 18px;"><?php _e( 'Apply via Email', 'jobs' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<div class="jobs-container">
		<div class="job-details-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 50px;">
			<div class="job-description-area">
				<div class="account-section">
					<h3><?php _e( 'Job Description', 'jobs' ); ?></h3>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>

			<div class="job-sidebar-area">
				<div class="account-section">
					<h3><?php _e( 'Job Overview', 'jobs' ); ?></h3>
					<ul style="list-style: none; padding: 0; margin: 0;">
						<li style="padding: 10px 0; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between;">
							<strong><?php _e( 'Posted:', 'jobs' ); ?></strong>
							<span><?php echo get_the_date(); ?></span>
						</li>
						<li style="padding: 10px 0; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between;">
							<strong><?php _e( 'Category:', 'jobs' ); ?></strong>
							<span><?php echo strip_tags(get_the_term_list($job_id, 'job_category', '', ', ')); ?></span>
						</li>
						<li style="padding: 10px 0; display: flex; justify-content: space-between;">
							<strong><?php _e( 'Expiration:', 'jobs' ); ?></strong>
							<span><?php echo get_post_meta($job_id, '_jobs_expiration_date', true) ?: 'N/A'; ?></span>
						</li>
					</ul>
				</div>

				<div class="account-section" style="text-align: center;">
					<h3><?php _e( 'About Company', 'jobs' ); ?></h3>
					<?php echo get_avatar($author_id, 80); ?>
					<h4 style="margin-top: 15px;"><?php echo $author ? $author->display_name : 'Company Name'; ?></h4>
					<a href="#" class="jobs-button btn-outline" style="width: 100%; margin-top: 15px;"><?php _e( 'Follow Company', 'jobs' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.jobs-single-listing h1, .jobs-single-listing h3 { text-decoration: none !important; }
</style>
<?php
get_footer();
