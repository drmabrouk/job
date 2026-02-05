<?php
/**
 * Smart Candidate Matching & Recommendations
 */
$user_id = get_current_user_id();

// Get employer's job categories
$my_jobs = get_posts( array( 'post_type' => 'job', 'author' => $user_id, 'fields' => 'ids' ) );
$my_cats = array();
if ( ! empty( $my_jobs ) ) {
	foreach ( $my_jobs as $job_id ) {
		$terms = wp_get_post_terms( $job_id, 'job_category', array( 'fields' => 'ids' ) );
		$my_cats = array_merge( $my_cats, $terms );
	}
	$my_cats = array_unique( $my_cats );
}

// Find seekers with matching interests (simulated by their search history or profile meta)
$users = get_users( array( 'role' => 'job_seeker', 'number' => 10 ) );
$recommended_seekers = array();

foreach ( $users as $u ) {
	// For this simulation, we'll check if they've looked at these categories
	$history = get_user_meta( $u->ID, '_jobs_search_history', true ) ?: array();
	$match = false;
	foreach ( $history as $h ) {
		if ( $h['type'] == 'category' ) {
			$term = get_term_by('slug', $h['value'], 'job_category');
			if ( $term && in_array( $term->term_id, $my_cats ) ) {
				$match = true;
				break;
			}
		}
	}
	if ( $match ) {
		$recommended_seekers[] = $u;
	}
}
?>

<div class="jobs-candidate-matching">
	<h3><?php _e( 'Smart Candidate Recommendations', 'jobs' ); ?></h3>
	<p class="description"><?php _e( 'Based on your job postings, these candidates might be a great fit for your team.', 'jobs' ); ?></p>

	<div class="candidates-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
		<?php if ( ! empty( $recommended_seekers ) ) : foreach ( $recommended_seekers as $seeker ) : ?>
			<div class="candidate-card" style="background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #eee; text-align: center;">
				<?php echo get_avatar( $seeker->ID, 64 ); ?>
				<h4 style="margin: 15px 0 5px;"><?php echo esc_html( $seeker->display_name ); ?></h4>
				<p style="font-size: 13px; color: #777; margin-bottom: 15px;"><?php _e( 'Matching Specialist', 'jobs' ); ?></p>
				<a href="<?php echo home_url('/job-seeker/' . $seeker->user_nicename); ?>" class="button button-small"><?php _e( 'View Profile', 'jobs' ); ?></a>
				<a href="?tab=messages&view=single&action=new&to=<?php echo $seeker->ID; ?>" class="button button-small button-primary"><?php _e( 'Message', 'jobs' ); ?></a>
			</div>
		<?php endforeach; else : ?>
			<p><?php _e( 'No matching candidates found at the moment.', 'jobs' ); ?></p>
		<?php endif; ?>
	</div>
</div>
