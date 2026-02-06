<?php
/**
 * Professional Single Job Listing Template - Refined
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

$share_url = urlencode(get_permalink());
$share_title = urlencode(get_the_title());
?>
<div class="jobs-single-listing-refined">
	<header class="job-hero-section">
		<div class="jobs-container-nav">
			<div class="job-hero-content">
				<div class="job-hero-logo">
					<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium' ); else: ?>
						<i class="fas fa-building"></i>
					<?php endif; ?>
				</div>
				<div class="job-hero-title-area">
					<div class="job-hero-tags">
						<?php
						$types = get_the_terms( $job_id, 'job_type' );
						if($types) foreach($types as $t) echo '<span class="hero-tag">' . $t->name . '</span>';
						?>
					</div>
					<h1><?php the_title(); ?></h1>
					<p class="hero-subtitle">
						<span><i class="fas fa-briefcase"></i> <?php echo $author ? $author->display_name : 'Company'; ?></span>
						<span class="sep">•</span>
						<span><i class="fas fa-map-marker-alt"></i> <?php echo esc_html("$location, $state, $country"); ?></span>
					</p>
				</div>
			</div>
			<div class="job-hero-actions">
				<?php if ( is_user_logged_in() ) : ?>
					<button id="inline-apply-trigger" class="jobs-button btn-primary-lg"><?php _e( 'Apply for this Job', 'jobs' ); ?></button>
				<?php else : ?>
					<a href="<?php echo home_url('/jobs-auth?redirect_to=' . urlencode(get_permalink())); ?>" class="jobs-button btn-primary-lg"><?php _e( 'Login to Apply', 'jobs' ); ?></a>
				<?php endif; ?>

				<div class="job-share-wrap">
					<span><?php _e('Share:', 'jobs'); ?></span>
					<a href="https://api.whatsapp.com/send?text=<?php echo $share_title . ' ' . $share_url; ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
					<a href="https://twitter.com/intent/tweet?text=<?php echo $share_title; ?>&url=<?php echo $share_url; ?>" target="_blank" title="X"><i class="fab fa-x-twitter"></i></a>
				</div>
			</div>
		</div>
	</header>

	<div class="jobs-container content-grid-layout">
		<div class="job-main-column">
			<div class="job-content-card">
				<h3 class="card-title"><?php _e( 'Job Description', 'jobs' ); ?></h3>
				<div class="job-entry-content">
					<?php the_content(); ?>
				</div>
			</div>
		</div>

		<aside class="job-sidebar-column">
			<div class="job-info-card">
				<h3 class="card-title"><?php _e( 'Overview', 'jobs' ); ?></h3>
				<ul class="job-overview-list">
					<li><strong><?php _e( 'Posted:', 'jobs' ); ?></strong> <span><?php echo get_the_date(); ?></span></li>
					<li><strong><?php _e( 'Category:', 'jobs' ); ?></strong> <span><?php echo strip_tags(get_the_term_list($job_id, 'job_category', '', ', ')); ?></span></li>
					<li><strong><?php _e( 'Type:', 'jobs' ); ?></strong> <span><?php echo strip_tags(get_the_term_list($job_id, 'job_type', '', ', ')); ?></span></li>
					<li><strong><?php _e( 'Expiration:', 'jobs' ); ?></strong> <span class="text-danger"><?php echo get_post_meta($job_id, '_jobs_expiration_date', true) ?: 'N/A'; ?></span></li>
				</ul>
			</div>

			<div class="job-company-card">
				<div class="company-card-header">
					<?php echo get_avatar($author_id, 64, '', '', array('class' => 'circular-avatar')); ?>
					<div class="company-meta">
						<h4><?php echo $author ? $author->display_name : 'Company Name'; ?></h4>
						<p><?php _e( 'Verified Employer', 'jobs' ); ?></p>
					</div>
				</div>
				<button class="jobs-button btn-outline follow-employer-btn" data-id="<?php echo $author_id; ?>"><?php _e( 'Follow Company', 'jobs' ); ?></button>
			</div>
		</aside>
	</div>

	<!-- Application Modal Template (Hidden) -->
	<div style="display:none;">
		<div id="application-form-source">
			<div class="application-form-card-modal">
				<div class="form-card-header">
					<h3><?php _e('Submit Your Application', 'jobs'); ?></h3>
				</div>
				<div class="application-modal-content-inner">
					<?php
					$plugin_public = new Jobs_Public('jobs', '1.0.0');
					echo $plugin_public->add_application_form('');
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	$('#inline-apply-trigger').on('click', function() {
		const title = $('#application-form-source h3').text();
		const content = $('#application-form-source .application-modal-content-inner').html();

		$('#jobs-modal-title').text(title);
		$('#jobs-modal-body').html(content);
		$('#jobs-global-modal').css('display', 'flex').hide().fadeIn(300);
		$('body').addClass('jobs-modal-open');
	});
});
</script>

<style>
.jobs-single-listing-refined { background: #fcfcfc; min-height: 100vh; padding-bottom: 80px; }
.job-hero-section { background: #fff; padding: 60px 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 50px; }
.job-hero-section .jobs-container-nav { display: flex; justify-content: space-between; align-items: center; gap: 40px; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.job-hero-content { display: flex; align-items: center; gap: 30px; }
.job-hero-logo { width: 100px; height: 100px; background: #f8fafc; border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid #edf2f7; }
.job-hero-logo i { font-size: 40px; color: #cbd5e0; }
.job-hero-logo img { width: 100%; height: 100%; object-fit: contain; }
.job-hero-title-area h1 { margin: 10px 0; font-size: 36px; font-weight: 800; color: #1a202c; line-height: 1.2; }
.hero-tag { background: var(--primary-light); color: var(--primary-color); padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
.hero-subtitle { display: flex; align-items: center; gap: 15px; color: #718096; font-size: 16px; font-weight: 500; }
.hero-subtitle .sep { color: #cbd5e0; }
.job-hero-actions { text-align: right; }
.btn-primary-lg { padding: 18px 40px; font-size: 18px; font-weight: 700; border-radius: 14px; }
.job-share-wrap { margin-top: 20px; display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
.job-share-wrap span { font-size: 13px; color: #94a3b8; font-weight: 600; }
.job-share-wrap a { color: #64748b; font-size: 18px; transition: color 0.2s; }
.job-share-wrap a:hover { color: var(--primary-color); }

.content-grid-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.job-content-card, .job-info-card, .job-company-card { background: #fff; border-radius: 24px; padding: 40px; border: 1px solid #f1f5f9; margin-bottom: 30px; }
.card-title { font-size: 20px; font-weight: 700; color: #1a202c; margin-top: 0; margin-bottom: 25px; }
.job-entry-content { line-height: 1.8; color: #4a5568; font-size: 16px; }
.job-overview-list { list-style: none; padding: 0; margin: 0; }
.job-overview-list li { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #f8fafc; font-size: 14px; }
.job-overview-list li:last-child { border-bottom: none; }
.job-overview-list li strong { color: #64748b; }
.job-company-card .company-card-header { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; }
.company-meta h4 { margin: 0; font-size: 18px; }
.company-meta p { margin: 5px 0 0; color: #94a3b8; font-size: 13px; }
.follow-employer-btn { width: 100%; padding: 12px; }

@media (max-width: 991px) {
	.job-hero-section .jobs-container-nav { flex-direction: column; text-align: center; }
	.job-hero-content { flex-direction: column; }
	.job-hero-actions { text-align: center; margin-top: 30px; }
	.job-share-wrap { justify-content: center; }
	.content-grid-layout { grid-template-columns: 1fr; }
}

/* RTL Adjustment */
body.rtl .job-hero-actions, body.rtl .job-share-wrap { text-align: left; justify-content: flex-start; }
</style>
<?php
get_footer();
