<?php
/**
 * CV Manager
 */
$user_id = get_current_user_id();
$cv_id = get_user_meta( $user_id, '_jobs_cv_attachment_id', true );

if ( isset( $_POST['jobs_upload_cv'] ) && ! empty( $_FILES['cv_file']['name'] ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['cv_file'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

	if ( $movefile && ! isset( $movefile['error'] ) ) {
		$wp_filetype = $movefile['type'];
		$filename = $movefile['file'];
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $wp_filetype,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename );
		update_user_meta( $user_id, '_jobs_cv_attachment_id', $attach_id );
		echo '<div class="jobs-msg">' . __( 'CV uploaded successfully.', 'jobs' ) . '</div>';
	}
}
?>
<div class="jobs-cv-manager">
	<h2><?php _e( 'CV Manager', 'jobs' ); ?></h2>

	<div class="cv-section">
		<h3><?php _e( 'Upload Your CV', 'jobs' ); ?></h3>
		<form method="post" enctype="multipart/form-data">
			<input type="file" name="cv_file" accept=".pdf,.doc,.docx" required>
			<input type="submit" name="jobs_upload_cv" class="button" value="<?php _e( 'Upload', 'jobs' ); ?>">
		</form>
		<?php if ( $cv_id ) : ?>
			<p><a href="<?php echo wp_get_attachment_url($cv_id); ?>" target="_blank"><?php _e( 'Download Current CV', 'jobs' ); ?></a></p>
		<?php endif; ?>
	</div>

	<hr>

	<div class="cv-section">
		<h3><?php _e( 'Generate New Professional CV', 'jobs' ); ?></h3>
		<p><?php _e( 'Automatically generate a professional PDF CV from your profile data.', 'jobs' ); ?></p>
		<a href="?tab=cv&generate_cv=1" class="button button-primary"><?php _e( 'Generate & Download PDF CV', 'jobs' ); ?></a>
	</div>
</div>

<?php
if ( isset( $_GET['generate_cv'] ) ) {
	include plugin_dir_path(__FILE__) . 'jobs-cv-template.php';
}
?>
