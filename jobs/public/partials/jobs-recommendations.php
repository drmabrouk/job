<?php
/**
 * Job Recommendations
 */
$user_id = get_current_user_id();
$history = get_user_meta( $user_id, '_jobs_search_history', true ) ?: array();

if ( ! empty( $history ) ) {
	$last_cat = '';
	foreach ( array_reverse( $history ) as $item ) {
		if ( $item['type'] == 'category' ) {
			$last_cat = $item['value'];
			break;
		}
	}

	$args = array(
		'post_type'      => 'job',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
	);

	if ( $last_cat ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'job_category',
				'field'    => 'slug',
				'terms'    => $last_cat,
			),
		);
	}

	$recommended = new WP_Query( $args );

	if ( $recommended->have_posts() ) :
		?>
		<div class="jobs-recommendations">
			<h2><?php _e( 'Recommended for You', 'jobs' ); ?></h2>
			<div class="jobs-grid">
				<?php while ( $recommended->have_posts() ) : $recommended->the_post(); ?>
					<?php include plugin_dir_path( __FILE__ ) . 'jobs-card-template.php'; ?>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
		<?php
	endif;
}
?>
