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
<div class="jobs-single-listing" style="background: #fcfcfc; min-height: 100vh;">
	<header class="job-header" style="background: var(--primary-color); color: #fff; padding: 80px 0; margin-bottom: 50px; border-radius: 0 0 40px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
		<div class="jobs-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; gap: 40px;">
			<div class="job-header-main" style="flex: 1; display: flex; align-items: center; gap: 30px;">
				<div class="job-logo-large" style="width: 100px; height: 100px; background: #fff; border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium' ); else: ?>
						<i class="dashicons dashicons-businessperson" style="font-size: 60px; color: #cbd5e0; width: 60px; height: 60px;"></i>
					<?php endif; ?>
				</div>
				<div class="job-title-meta">
					<div class="job-meta-top" style="margin-bottom: 15px; display: flex; gap: 10px;">
						<?php
						$types = get_the_terms( $job_id, 'job_type' );
						if($types) foreach($types as $t) echo '<span class="job-tag" style="background: rgba(255,255,255,0.2); color: #fff; padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">' . $t->name . '</span>';
						?>
					</div>
					<h1 style="margin: 0; font-size: 36px; font-weight: 700; color: #fff; line-height: 1.2;"><?php the_title(); ?></h1>
					<p style="font-size: 18px; opacity: 0.9; margin-top: 10px; display: flex; align-items: center; gap: 10px;">
						<?php if($apply_url || $apply_email): ?>
							<i class="dashicons dashicons-admin-site"></i> <?php _e( 'Sourced via Jobedia', 'jobs' ); ?>
						<?php else: ?>
							<i class="dashicons dashicons-businessperson"></i> <?php echo $author ? $author->display_name : 'Company'; ?>
						<?php endif; ?>
						<span style="opacity: 0.5;">|</span>
						<i class="dashicons dashicons-location"></i> <?php echo esc_html("$location, $state, $country"); ?>
					</p>
				</div>
			</div>
			<div class="job-header-actions" id="job-apply-container">
				<?php if ( $is_quick_apply && is_user_logged_in() ) : ?>
					<form id="quick-apply-form">
						<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
						<input type="hidden" name="quick_apply" value="1">
						<?php wp_nonce_field( 'jobs_apply_nonce', 'nonce' ); ?>
						<button type="submit" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 18px 45px; font-size: 18px; font-weight: 700; border-radius: 12px; border: none; cursor: pointer; transition: all 0.3s;"><?php _e( 'Quick Apply', 'jobs' ); ?></button>
					</form>
				<?php elseif ( $apply_url ) : ?>
					<a href="<?php echo esc_url($apply_url); ?>" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 18px 45px; font-size: 18px; font-weight: 700; border-radius: 12px; text-decoration: none;" target="_blank"><?php _e( 'Apply on Company Site', 'jobs' ); ?></a>
				<?php elseif ( $apply_email ) : ?>
					<a href="mailto:<?php echo esc_attr($apply_email); ?>?subject=Application for <?php echo rawurlencode(get_the_title()); ?>" class="jobs-button" style="background: #fff; color: var(--primary-color); padding: 18px 45px; font-size: 18px; font-weight: 700; border-radius: 12px; text-decoration: none;"><?php _e( 'Apply via Email', 'jobs' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<div class="jobs-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
		<div class="job-details-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 50px;">
			<div class="job-description-area">
				<div class="account-section" style="background:#fff; padding:40px; border-radius:20px; border:1px solid #f0f0f0;">
					<h3 style="margin-top:0; margin-bottom:25px; font-size:22px; color:var(--primary-color);"><?php _e( 'Job Description', 'jobs' ); ?></h3>
					<div class="entry-content" style="line-height:1.8; color:#4a5568;">
						<?php the_content(); ?>
					</div>
				</div>
			</div>

			<div class="job-sidebar-area">
				<div class="account-section" style="background:#fff; padding:30px; border-radius:20px; border:1px solid #f0f0f0; margin-bottom:30px;">
					<h3 style="margin-top:0; margin-bottom:20px; font-size:18px; color:var(--primary-color);"><?php _e( 'Job Overview', 'jobs' ); ?></h3>
					<ul style="list-style: none; padding: 0; margin: 0;">
						<li style="padding: 12px 0; border-bottom: 1px solid #f7fafc; display: flex; justify-content: space-between; font-size: 14px;">
							<strong style="color:#718096;"><?php _e( 'Posted:', 'jobs' ); ?></strong>
							<span style="font-weight:600;"><?php echo get_the_date(); ?></span>
						</li>
						<li style="padding: 12px 0; border-bottom: 1px solid #f7fafc; display: flex; justify-content: space-between; font-size: 14px;">
							<strong style="color:#718096;"><?php _e( 'Category:', 'jobs' ); ?></strong>
							<span style="font-weight:600;"><?php echo strip_tags(get_the_term_list($job_id, 'job_category', '', ', ')); ?></span>
						</li>
						<li style="padding: 12px 0; display: flex; justify-content: space-between; font-size: 14px;">
							<strong style="color:#718096;"><?php _e( 'Expiration:', 'jobs' ); ?></strong>
							<span style="font-weight:600; color:#e53e3e;"><?php echo get_post_meta($job_id, '_jobs_expiration_date', true) ?: 'N/A'; ?></span>
						</li>
					</ul>
				</div>

				<div class="account-section" style="background:#fff; padding:30px; border-radius:20px; border:1px solid #f0f0f0; text-align: center;">
					<h3 style="margin-top:0; margin-bottom:20px; font-size:18px; color:var(--primary-color);"><?php _e( 'About Company', 'jobs' ); ?></h3>
					<?php echo get_avatar($author_id, 80, '', '', array('style' => 'border-radius:20px; margin-bottom:15px;')); ?>
					<h4 style="margin: 0; font-size: 18px; font-weight: 700;"><?php echo $author ? $author->display_name : 'Company Name'; ?></h4>
					<p style="color:#718096; font-size: 13px; margin-top:5px;"><?php _e( 'Verified Employer', 'jobs' ); ?></p>
					<button class="jobs-button btn-outline follow-employer-btn" data-id="<?php echo $author_id; ?>" style="width: 100%; margin-top: 20px; padding: 10px;"><?php _e( 'Follow Company', 'jobs' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.jobs-single-listing h1, .jobs-single-listing h3, .jobs-single-listing a { text-decoration: none !important; }
.jobs-single-listing .jobs-button:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
</style>
<?php
get_footer();
