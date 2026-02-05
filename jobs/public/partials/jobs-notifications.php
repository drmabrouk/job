<?php
/**
 * User Notifications UI
 */
$user_id = get_current_user_id();
$notifications = get_user_meta( $user_id, '_jobs_notifications', true ) ?: array();

if ( isset( $_GET['mark_read'] ) ) {
	update_user_meta( $user_id, '_jobs_notifications', array() );
	$notifications = array();
}
?>
<div class="jobs-notifications-section">
	<h3><?php _e( 'Your Notifications', 'jobs' ); ?></h3>
	<?php if ( ! empty( $notifications ) ) : ?>
		<ul class="notification-list">
			<?php foreach ( array_reverse( $notifications ) as $notif ) : ?>
				<li class="notification-item">
					<span class="notif-time"><?php echo date( 'M j, H:i', $notif['time'] ); ?></span>
					<span class="notif-msg"><?php echo esc_html( $notif['message'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<a href="?mark_read=1" class="button"><?php _e( 'Mark All as Read', 'jobs' ); ?></a>
	<?php else : ?>
		<p><?php _e( 'No new notifications.', 'jobs' ); ?></p>
	<?php endif; ?>
</div>
