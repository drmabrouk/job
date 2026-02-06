<?php

/**
 * Base class for all independent Jobs modules.
 */
abstract class Jobs_Module {

	protected $id;
	protected $name;

	abstract public function init();

	public function get_id() {
		return $this->id;
	}

	public function get_name() {
		return $this->name;
	}

	public function render( $args = array() ) {
		$view_path = $this->get_view_path();
		if ( file_exists( $view_path ) ) {
			if ( ! empty( $args ) ) {
				extract( $args );
			}
			include $view_path;
		} else {
			printf( __( 'View not found for module: %s', 'jobs' ), $this->id );
		}
	}

	protected function get_view_path() {
		$reflection = new ReflectionClass( $this );
		return dirname( $reflection->getFileName() ) . '/view.php';
	}

	protected function add_ajax( $action, $callback, $nopriv = false ) {
		add_action( 'wp_ajax_jobs_' . $action, array( $this, $callback ) );
		if ( $nopriv ) {
			add_action( 'wp_ajax_nopriv_jobs_' . $action, array( $this, $callback ) );
		}
	}

	protected function log_activity( $user_id, $action ) {
		$logs = get_user_meta( $user_id, '_jobs_activity_log', true ) ?: array();
		$logs[] = array(
			'action' => $action,
			'time'   => time(),
			'ip'     => $_SERVER['REMOTE_ADDR']
		);
		update_user_meta( $user_id, '_jobs_activity_log', array_slice( $logs, -50 ) );
	}
}
