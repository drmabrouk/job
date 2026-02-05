<?php
/**
 * Frontend Job Management
 */
$user_id = get_current_user_id();

// Handle Job Submission/Update
if ( isset( $_POST['jobs_save_job'] ) && wp_verify_nonce( $_POST['jobs_job_nonce'], 'jobs_save_job_action' ) ) {
	$job_data = array(
		'post_title'   => sanitize_text_field( $_POST['job_title'] ),
		'post_content' => wp_kses_post( $_POST['job_description'] ),
		'post_type'    => 'job',
		'post_status'  => get_option( 'jobs_default_status', 'pending' ),
		'post_author'  => $user_id,
	);

	if ( ! empty( $_POST['job_id'] ) ) {
		$job_data['ID'] = intval( $_POST['job_id'] );
		$job_id = wp_update_post( $job_data );
		echo '<div class="jobs-msg">' . __( 'Job updated successfully.', 'jobs' ) . '</div>';
	} else {
		$job_id = wp_insert_post( $job_data );
		echo '<div class="jobs-msg">' . __( 'Job posted successfully.', 'jobs' ) . '</div>';
	}

	if ( $job_id ) {
		update_post_meta( $job_id, '_job_location', sanitize_text_field( $_POST['job_location'] ) );
		update_post_meta( $job_id, '_job_country', sanitize_text_field( $_POST['job_country'] ) );
		update_post_meta( $job_id, '_job_state', sanitize_text_field( $_POST['job_state'] ) );
		wp_set_object_terms( $job_id, intval( $_POST['job_category'] ), 'job_category' );
		wp_set_object_terms( $job_id, intval( $_POST['job_type'] ), 'job_type' );

		// Set expiration
		$expiration = date( 'Y-m-d H:i:s', strtotime( '+ 50 days' ) );
		update_post_meta( $job_id, '_jobs_expiration_date', $expiration );
	}
}

$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
?>

<div class="jobs-manage-section">
	<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
		<h2><?php _e( 'Job Management', 'jobs' ); ?></h2>
		<?php if ($action == 'list') : ?>
			<a href="?tab=manage-jobs&action=add" class="button button-primary"><?php _e( 'Add New Job', 'jobs' ); ?></a>
		<?php else : ?>
			<a href="?tab=manage-jobs&action=list" class="button"><?php _e( 'Back to List', 'jobs' ); ?></a>
		<?php endif; ?>
	</div>

	<?php if ($action == 'add' || $action == 'edit') :
		$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
		$job = $job_id ? get_post($job_id) : null;
	?>
		<form method="post" class="jobs-frontend-form">
			<?php wp_nonce_field( 'jobs_save_job_action', 'jobs_job_nonce' ); ?>
			<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

			<p>
				<label><?php _e( 'Job Title', 'jobs' ); ?></label>
				<input type="text" name="job_title" value="<?php echo $job ? esc_attr($job->post_title) : ''; ?>" required>
			</p>

			<p>
				<label><?php _e( 'Category', 'jobs' ); ?></label>
				<?php wp_dropdown_categories( array( 'taxonomy' => 'job_category', 'name' => 'job_category', 'hide_empty' => 0, 'selected' => $job ? wp_get_object_terms($job->ID, 'job_category', array('fields' => 'ids'))[0] : 0 ) ); ?>
			</p>

			<p>
				<label><?php _e( 'Job Type', 'jobs' ); ?></label>
				<?php wp_dropdown_categories( array( 'taxonomy' => 'job_type', 'name' => 'job_type', 'hide_empty' => 0, 'selected' => $job ? wp_get_object_terms($job->ID, 'job_type', array('fields' => 'ids'))[0] : 0 ) ); ?>
			</p>

			<p>
				<label><?php _e( 'Country', 'jobs' ); ?></label>
				<select name="job_country" id="jobs-country-select" class="jobs-filter-select" required>
					<option value=""><?php _e( 'Select Country', 'jobs' ); ?></option>
					<?php
					$locs = get_option( 'jobs_global_locations', array() );
					$current_country = $job ? get_post_meta($job->ID, '_job_country', true) : '';
					foreach ( array_keys($locs) as $country ) :
					?>
						<option value="<?php echo esc_attr($country); ?>" <?php selected($current_country, $country); ?>><?php echo esc_html($country); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label><?php _e( 'State/Province', 'jobs' ); ?></label>
				<input type="text" name="job_state" value="<?php echo $job ? esc_attr(get_post_meta($job->ID, '_job_state', true)) : ''; ?>" required>
			</p>

			<p>
				<label><?php _e( 'Description', 'jobs' ); ?></label>
				<textarea name="job_description" rows="10" required><?php echo $job ? esc_textarea($job->post_content) : ''; ?></textarea>
			</p>

			<p class="submit">
				<input type="submit" name="jobs_save_job" class="button button-primary" value="<?php _e( 'Save Job Listing', 'jobs' ); ?>">
			</p>
		</form>
	<?php else : ?>
		<?php
		$jobs = new WP_Query( array(
			'post_type' => 'job',
			'author'    => $user_id,
			'posts_per_page' => -1,
			'post_status' => array( 'publish', 'draft', 'pending' ),
		) );
		?>
		<table class="jobs-table">
			<thead>
				<tr>
					<th><?php _e( 'Job Title', 'jobs' ); ?></th>
					<th><?php _e( 'Status', 'jobs' ); ?></th>
					<th><?php _e( 'Expires', 'jobs' ); ?></th>
					<th><?php _e( 'Actions', 'jobs' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $jobs->have_posts() ) : while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
					<tr>
						<td><strong><?php the_title(); ?></strong></td>
						<td><span class="status-badge status-<?php echo get_post_status(); ?>"><?php echo get_post_status(); ?></span></td>
						<td><?php echo get_post_meta( get_the_ID(), '_jobs_expiration_date', true ) ?: 'N/A'; ?></td>
						<td>
							<a href="?tab=manage-jobs&action=edit&job_id=<?php the_ID(); ?>"><?php _e( 'Edit', 'jobs' ); ?></a> |
							<?php if ( get_post_status() == 'draft' ) : ?>
								<a href="#" class="reactivate-job" data-id="<?php the_ID(); ?>"><?php _e( 'Reactivate', 'jobs' ); ?></a>
							<?php else : ?>
								<a href="#" class="extend-job" data-id="<?php the_ID(); ?>"><?php _e( 'Extend', 'jobs' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endwhile; wp_reset_postdata(); else : ?>
					<tr><td colspan="4"><?php _e( 'No jobs found.', 'jobs' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
