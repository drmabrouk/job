<?php
/**
 * Advanced Multilingual Job Management
 */
$user_id = get_current_user_id();

// Handle Job Submission/Update
if ( isset( $_POST['jobs_save_job'] ) && wp_verify_nonce( $_POST['jobs_job_nonce'], 'jobs_save_job_action' ) ) {
	$lang_choice = sanitize_text_field( $_POST['job_lang_choice'] );

	$title = ($lang_choice == 'both') ? sanitize_text_field($_POST['job_title_en']) : sanitize_text_field($_POST['job_title']);
	$content = ($lang_choice == 'both') ? wp_kses_post($_POST['job_description_en']) : wp_kses_post($_POST['job_description']);

	$job_data = array(
		'post_title'   => $title,
		'post_content' => $content,
		'post_type'    => 'job',
		'post_status'  => get_option( 'jobs_default_status', 'pending' ),
		'post_author'  => $user_id,
	);

	if ( ! empty( $_POST['job_id'] ) ) {
		$job_data['ID'] = intval( $_POST['job_id'] );
		$job_id = wp_update_post( $job_data );
		echo '<div class="jobs-msg success">' . __( 'Job updated successfully.', 'jobs' ) . '</div>';
	} else {
		$job_id = wp_insert_post( $job_data );
		echo '<div class="jobs-msg success">' . __( 'Job posted successfully.', 'jobs' ) . '</div>';
	}

	if ( $job_id ) {
		update_post_meta( $job_id, '_job_lang_choice', $lang_choice );
		update_post_meta( $job_id, '_job_location', sanitize_text_field( $_POST['job_location'] ) );
		update_post_meta( $job_id, '_job_country', sanitize_text_field( $_POST['job_country'] ) );
		update_post_meta( $job_id, '_job_state', sanitize_text_field( $_POST['job_state'] ) );

		if($lang_choice == 'both' || $lang_choice == 'ar') {
			update_post_meta( $job_id, '_job_title_ar', sanitize_text_field($_POST['job_title_ar']) );
			update_post_meta( $job_id, '_job_description_ar', wp_kses_post($_POST['job_description_ar']) );
		}

		wp_set_object_terms( $job_id, intval( $_POST['job_category'] ), 'job_category' );
		wp_set_object_terms( $job_id, intval( $_POST['job_type'] ), 'job_type' );

		$expiration = date( 'Y-m-d H:i:s', strtotime( '+ 50 days' ) );
		update_post_meta( $job_id, '_jobs_expiration_date', $expiration );
	}
}

$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';
?>

<div class="jobs-manage-section">
	<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h3 style="margin: 0; font-size: 24px; color: var(--primary-color);"><?php _e( 'Job Management', 'jobs' ); ?></h3>
		<?php if ($action == 'list') : ?>
			<a href="?tab=manage-jobs&action=add" class="jobs-button"><?php _e( 'Post a New Job', 'jobs' ); ?></a>
		<?php else : ?>
			<a href="?tab=manage-jobs&action=list" class="jobs-button btn-outline"><?php _e( 'Back to List', 'jobs' ); ?></a>
		<?php endif; ?>
	</div>

	<?php if ($action == 'add' || $action == 'edit') :
		$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
		$job = $job_id ? get_post($job_id) : null;
		$current_lang = $job ? get_post_meta($job->ID, '_job_lang_choice', true) : 'en';
	?>
		<form method="post" class="jobs-frontend-form" id="multilingual-job-form">
			<?php wp_nonce_field( 'jobs_save_job_action', 'jobs_job_nonce' ); ?>
			<input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

			<div class="form-section" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #f0f0f0; margin-bottom: 30px;">
				<h4 style="margin-top: 0; margin-bottom: 20px;"><?php _e( 'Language Selection', 'jobs' ); ?></h4>
				<div style="display: flex; gap: 20px;">
					<label><input type="radio" name="job_lang_choice" value="en" <?php checked($current_lang, 'en'); ?>> English Only</label>
					<label><input type="radio" name="job_lang_choice" value="ar" <?php checked($current_lang, 'ar'); ?>> Arabic Only</label>
					<label><input type="radio" name="job_lang_choice" value="both" <?php checked($current_lang, 'both'); ?>> Both (EN & AR)</label>
				</div>
			</div>

			<div class="form-section content-fields" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #f0f0f0; margin-bottom: 30px;">
				<!-- English Fields -->
				<div id="en-fields" style="<?php echo ($current_lang == 'ar') ? 'display:none;' : ''; ?>">
					<h4 style="color: var(--primary-color);">English Content</h4>
					<p>
						<label><?php _e( 'Job Title (EN)', 'jobs' ); ?></label>
						<input type="text" name="job_title_en" value="<?php echo $job ? esc_attr($job->post_title) : ''; ?>" required>
					</p>
					<p>
						<label><?php _e( 'Job Description (EN)', 'jobs' ); ?></label>
						<textarea name="job_description_en" rows="8"><?php echo $job ? esc_textarea($job->post_content) : ''; ?></textarea>
					</p>
				</div>

				<!-- Arabic Fields -->
				<div id="ar-fields" style="<?php echo ($current_lang == 'en') ? 'display:none;' : ''; ?>">
					<h4 style="color: var(--primary-color);">المحتوى العربي</h4>
					<p>
						<label><?php _e( 'Job Title (AR)', 'jobs' ); ?></label>
						<input type="text" name="job_title_ar" value="<?php echo $job ? esc_attr(get_post_meta($job->ID, '_job_title_ar', true)) : ''; ?>" style="direction: rtl;">
					</p>
					<p>
						<label><?php _e( 'Job Description (AR)', 'jobs' ); ?></label>
						<textarea name="job_description_ar" rows="8" style="direction: rtl;"><?php echo $job ? esc_textarea(get_post_meta($job->ID, '_job_description_ar', true)) : ''; ?></textarea>
					</p>
				</div>
			</div>

			<div class="form-section" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #f0f0f0; margin-bottom: 30px;">
				<h4 style="margin-top: 0; margin-bottom: 20px;"><?php _e( 'Classification & Location', 'jobs' ); ?></h4>
				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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
						<label><?php _e( 'State/City', 'jobs' ); ?></label>
						<input type="text" name="job_state" value="<?php echo $job ? esc_attr(get_post_meta($job->ID, '_job_state', true)) : ''; ?>" required>
					</p>
				</div>
			</div>

			<p class="submit">
				<input type="submit" name="jobs_save_job" class="jobs-button" value="<?php _e( 'Publish Job Listing', 'jobs' ); ?>" style="padding: 15px 40px;">
			</p>
		</form>

		<script>
		jQuery(document).ready(function($) {
			$('input[name="job_lang_choice"]').on('change', function() {
				const val = $(this).val();
				if(val === 'en') {
					$('#en-fields').show(); $('#ar-fields').hide();
				} else if(val === 'ar') {
					$('#en-fields').hide(); $('#ar-fields').show();
				} else {
					$('#en-fields').show(); $('#ar-fields').show();
				}
			});
		});
		</script>
	<?php else : ?>
		<?php
		$jobs = new WP_Query( array(
			'post_type' => 'job',
			'author'    => $user_id,
			'posts_per_page' => -1,
			'post_status' => array( 'publish', 'draft', 'pending' ),
		) );
		?>
		<div style="background: #fff; padding: 25px; border-radius: 20px; border: 1px solid #f0f0f0;">
			<table class="jobs-table">
				<thead>
					<tr>
						<th><?php _e( 'Job Title', 'jobs' ); ?></th>
						<th><?php _e( 'Status', 'jobs' ); ?></th>
						<th><?php _e( 'Language', 'jobs' ); ?></th>
						<th><?php _e( 'Actions', 'jobs' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( $jobs->have_posts() ) : while ( $jobs->have_posts() ) : $jobs->the_post();
						$lang = get_post_meta(get_the_ID(), '_job_lang_choice', true) ?: 'en';
					?>
						<tr>
							<td><strong><?php the_title(); ?></strong></td>
							<td><span class="status-badge status-<?php echo get_post_status(); ?>"><?php echo get_post_status(); ?></span></td>
							<td><span class="role-label"><?php echo strtoupper($lang); ?></span></td>
							<td>
								<div style="display: flex; gap: 10px;">
									<a href="?tab=manage-jobs&action=edit&job_id=<?php the_ID(); ?>" class="edit-user-link" title="<?php _e( 'Edit', 'jobs' ); ?>"><i class="dashicons dashicons-edit"></i></a>
									<?php if ( get_post_status() == 'draft' ) : ?>
										<a href="#" class="reactivate-job" data-id="<?php the_ID(); ?>" style="color: #27ae60;"><i class="dashicons dashicons-undo"></i></a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endwhile; wp_reset_postdata(); else : ?>
						<tr><td colspan="4" style="text-align: center; padding: 40px; color: #718096;"><?php _e( 'No jobs found.', 'jobs' ); ?></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>
