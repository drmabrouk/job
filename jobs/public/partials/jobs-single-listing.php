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
	<header class="job-header">
		<div class="jobs-container-nav">
			<div class="job-header-main">
				<div class="job-logo-large">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium' ); else: ?>
						<i class="fas fa-building"></i>
					<?php endif; ?>
				</div>
				<div class="job-title-meta">
					<div class="job-meta-top">
						<?php
						$types = get_the_terms( $job_id, 'job_type' );
						if($types) foreach($types as $t) echo '<span class="job-tag tag-type">' . $t->name . '</span>';
						?>
					</div>
					<h1><?php the_title(); ?></h1>
					<p class="header-subtitle">
						<i class="fas fa-briefcase"></i> <?php echo $author ? $author->display_name : 'Company'; ?>
						<span class="sep">|</span>
						<i class="fas fa-map-marker-alt"></i> <?php echo esc_html("$location, $state, $country"); ?>
					</p>
				</div>
			</div>
			<div class="job-header-actions" id="job-apply-container">
				<?php if ( $is_quick_apply && is_user_logged_in() ) : ?>
					<form id="quick-apply-form">
						<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
						<input type="hidden" name="quick_apply" value="1">
						<?php wp_nonce_field( 'jobs_apply_nonce', 'nonce' ); ?>
						<button type="submit" class="jobs-button btn-primary-modern"><?php _e( 'Quick Apply', 'jobs' ); ?></button>
					</form>
				<?php elseif ( $apply_url ) : ?>
					<a href="<?php echo esc_url($apply_url); ?>" class="jobs-button btn-primary-modern" target="_blank"><?php _e( 'Apply on Company Site', 'jobs' ); ?></a>
				<?php elseif ( $apply_email ) : ?>
					<a href="mailto:<?php echo esc_attr($apply_email); ?>?subject=Application for <?php echo rawurlencode(get_the_title()); ?>" class="jobs-button btn-primary-modern"><?php _e( 'Apply via Email', 'jobs' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<div class="jobs-container">
		<div class="job-details-grid">
			<div class="job-description-area">
				<div class="account-section content-card">
					<h3 class="section-title"><?php _e( 'Job Description', 'jobs' ); ?></h3>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>

			<div class="job-sidebar-area">
				<div class="account-section info-card">
					<h3 class="section-title"><?php _e( 'Job Overview', 'jobs' ); ?></h3>
					<ul class="overview-list">
						<li>
							<strong><?php _e( 'Posted:', 'jobs' ); ?></strong>
							<span><?php echo get_the_date(); ?></span>
						</li>
						<li>
							<strong><?php _e( 'Category:', 'jobs' ); ?></strong>
							<span><?php echo strip_tags(get_the_term_list($job_id, 'job_category', '', ', ')); ?></span>
						</li>
						<li>
							<strong><?php _e( 'Expiration:', 'jobs' ); ?></strong>
							<span class="expiry-date"><?php echo get_post_meta($job_id, '_jobs_expiration_date', true) ?: 'N/A'; ?></span>
						</li>
					</ul>
				</div>

				<div class="account-section company-card">
					<h3 class="section-title"><?php _e( 'About Company', 'jobs' ); ?></h3>
					<div class="company-info-large">
						<?php echo get_avatar($author_id, 80); ?>
						<h4><?php echo $author ? $author->display_name : 'Company Name'; ?></h4>
						<p><?php _e( 'Verified Employer', 'jobs' ); ?></p>
						<button class="jobs-button btn-outline-modern follow-employer-btn" data-id="<?php echo $author_id; ?>"><?php _e( 'Follow Company', 'jobs' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.jobs-single-listing { background: #f8fafc; min-height: 100vh; padding-bottom: 60px; }
.job-header { background: var(--primary-color); color: #fff; padding: 60px 0; border-radius: 0 0 30px 30px; margin-bottom: 40px; }
.job-header .jobs-container-nav { display: flex; justify-content: space-between; align-items: center; gap: 30px; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.job-header-main { display: flex; align-items: center; gap: 30px; }
.job-logo-large { width: 90px; height: 90px; background: #fff; border-radius: 18px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
.job-logo-large i { font-size: 40px; color: #cbd5e0; }
.job-logo-large img { width: 100%; height: 100%; object-fit: contain; }
.job-title-meta h1 { margin: 0; font-size: 32px; font-weight: 700; color: #fff; }
.header-subtitle { font-size: 16px; opacity: 0.9; margin-top: 10px; display: flex; align-items: center; gap: 10px; }
.header-subtitle .sep { opacity: 0.4; }
.job-details-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.content-card, .info-card, .company-card { background: #fff; border-radius: 20px; padding: 30px; border: 1px solid #e2e8f0; }
.section-title { font-size: 20px; color: var(--primary-color); margin-top: 0; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
.overview-list { list-style: none; padding: 0; margin: 0; }
.overview-list li { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.overview-list li:last-child { border-bottom: none; }
.overview-list li strong { color: #64748b; }
.expiry-date { color: #e53e3e; font-weight: 600; }
.company-info-large { text-align: center; }
.company-info-large img { border-radius: 15px; margin-bottom: 15px; }
.company-info-large h4 { margin: 0; font-size: 18px; }
.company-info-large p { color: #64748b; font-size: 13px; margin: 5px 0 20px; }
.follow-employer-btn { width: 100%; }
@media (max-width: 768px) {
	.job-header .jobs-container-nav { flex-direction: column; text-align: center; }
	.job-header-main { flex-direction: column; }
	.job-details-grid { grid-template-columns: 1fr; }
}
</style>
<?php
get_footer();
