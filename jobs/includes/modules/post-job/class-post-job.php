<?php
class Jobs_Module_Post_Job extends Jobs_Module {
	public function __construct() {
		$this->id = 'post-job';
		$this->name = __( 'Post a Job', 'jobs' );
	}
	public function init() {
		$this->add_ajax( 'post_job_ajax', 'ajax_post_job' );
	}
	public function ajax_post_job() {
		check_ajax_referer( 'jobs_post_job_nonce', 'nonce' );
		if ( ! is_user_logged_in() || ! (current_user_can('employer') || current_user_can('manage_options')) ) {
			wp_send_json_error( __('Unauthorized to post jobs.', 'jobs') );
		}
		$title = sanitize_text_field( $_POST['job_title'] );
		$cat   = intval( $_POST['job_category'] );
		$loc   = sanitize_text_field( $_POST['job_location'] );
		$type  = intval( $_POST['job_type'] );
		$desc  = wp_kses_post( $_POST['job_description'] );
		$job_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $desc,
			'post_status'  => 'pending',
			'post_type'    => 'job',
			'post_author'  => get_current_user_id(),
		) );
		if ( $job_id ) {
			wp_set_object_terms( $job_id, $cat, 'job_category' );
			wp_set_object_terms( $job_id, $type, 'job_type' );
			update_post_meta( $job_id, '_job_location', $loc );
			$days = get_option( 'jobs_expiration_days', '50' );
			$expiration = date( 'Y-m-d H:i:s', strtotime( '+ ' . $days . ' days' ) );
			update_post_meta( $job_id, '_jobs_expiration_date', $expiration );
			$this->log_activity( get_current_user_id(), 'Posted new job: ' . $title );
			wp_send_json_success( __('Job posted successfully and is pending review!', 'jobs') );
		}
		wp_send_json_error( __('Failed to post job. Please try again.', 'jobs') );
	}
}
