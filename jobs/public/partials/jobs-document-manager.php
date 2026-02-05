<?php
/**
 * Advanced Document & File Management
 */
$user_id = get_current_user_id();

// Handle File Upload
if ( isset( $_POST['jobs_upload_doc'] ) && ! empty( $_FILES['doc_file']['name'] ) ) {
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$movefile = wp_handle_upload( $_FILES['doc_file'], array( 'test_form' => false ) );

	if ( $movefile && ! isset( $movefile['error'] ) ) {
		$wp_filetype = $movefile['type'];
		$filename = $movefile['file'];
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $wp_filetype,
			'post_title'     => sanitize_text_field( $_POST['doc_title'] ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename );

		$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
		$docs[] = array(
			'id'    => $attach_id,
			'title' => sanitize_text_field( $_POST['doc_title'] ),
			'type'  => sanitize_text_field( $_POST['doc_type'] ),
			'time'  => time()
		);
		update_user_meta( $user_id, '_jobs_user_documents', $docs );
		echo '<div class="jobs-msg">' . __( 'Document uploaded successfully.', 'jobs' ) . '</div>';
	}
}

// Handle Deletion
if ( isset( $_GET['delete_doc'] ) ) {
	$doc_id = intval($_GET['delete_doc']);
	$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
	foreach ( $docs as $key => $doc ) {
		if ( $doc['id'] == $doc_id ) {
			wp_delete_attachment( $doc_id, true );
			unset($docs[$key]);
			break;
		}
	}
	update_user_meta( $user_id, '_jobs_user_documents', array_values($docs) );
}

$docs = get_user_meta( $user_id, '_jobs_user_documents', true ) ?: array();
?>

<div class="jobs-document-manager">
	<div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h2><?php _e( 'Document & File Management', 'jobs' ); ?></h2>
	</div>

	<div class="upload-new-doc" style="background: #fdfdfd; padding: 25px; border-radius: 12px; border: 1px solid #eee; margin-bottom: 40px;">
		<h3><?php _e( 'Upload New Document', 'jobs' ); ?></h3>
		<form method="post" enctype="multipart/form-data" class="jobs-frontend-form" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
			<div>
				<label><?php _e( 'Document Title', 'jobs' ); ?></label>
				<input type="text" name="doc_title" required placeholder="e.g. Master CV 2024">
			</div>
			<div>
				<label><?php _e( 'Type', 'jobs' ); ?></label>
				<select name="doc_type">
					<option value="cv"><?php _e( 'CV / Resume', 'jobs' ); ?></option>
					<option value="cert"><?php _e( 'Certificate', 'jobs' ); ?></option>
					<option value="other"><?php _e( 'Supporting Document', 'jobs' ); ?></option>
				</select>
			</div>
			<div>
				<label><?php _e( 'File (PDF, Doc)', 'jobs' ); ?></label>
				<input type="file" name="doc_file" accept=".pdf,.doc,.docx" required>
			</div>
			<input type="submit" name="jobs_upload_doc" class="button button-primary" value="<?php _e( 'Upload File', 'jobs' ); ?>">
		</form>
	</div>

	<h3><?php _e( 'Your Reusable Documents', 'jobs' ); ?></h3>
	<table class="jobs-table">
		<thead>
			<tr>
				<th><?php _e( 'Title', 'jobs' ); ?></th>
				<th><?php _e( 'Type', 'jobs' ); ?></th>
				<th><?php _e( 'Date', 'jobs' ); ?></th>
				<th><?php _e( 'Actions', 'jobs' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $docs ) ) : foreach ( $docs as $doc ) : ?>
				<tr>
					<td><strong><?php echo esc_html($doc['title']); ?></strong></td>
					<td><span class="status-badge status-publish"><?php echo esc_html($doc['type']); ?></span></td>
					<td><?php echo date('M j, Y', $doc['time']); ?></td>
					<td>
						<a href="<?php echo wp_get_attachment_url($doc['id']); ?>" target="_blank"><?php _e( 'View', 'jobs' ); ?></a> |
						<a href="?tab=cv&delete_doc=<?php echo $doc['id']; ?>" style="color: #e74c3c;" onclick="return confirm('Are you sure?')"><?php _e( 'Delete', 'jobs' ); ?></a>
					</td>
				</tr>
			<?php endforeach; else : ?>
				<tr><td colspan="4"><?php _e( 'No documents uploaded yet.', 'jobs' ); ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>

	<hr style="margin: 50px 0;">

	<div class="cv-generator-section">
		<h3><?php _e( 'Professional CV Generator', 'jobs' ); ?></h3>
		<p><?php _e( 'Instantly generate a high-standard PDF resume using your account data.', 'jobs' ); ?></p>
		<a href="?tab=cv&generate_cv=1" class="button button-primary"><?php _e( 'Generate & Download PDF', 'jobs' ); ?></a>
	</div>
</div>

<?php
if ( isset( $_GET['generate_cv'] ) ) {
	include plugin_dir_path(__FILE__) . 'jobs-cv-template.php';
}
?>
