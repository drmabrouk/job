<?php
/**
 * Professional Internal Messaging System
 */
$user_id = get_current_user_id();

// Handle Sending Message
if ( isset( $_POST['jobs_send_message'] ) && wp_verify_nonce( $_POST['jobs_message_nonce'], 'jobs_send_msg' ) ) {
	$receiver_id = intval( $_POST['receiver_id'] );
	$subject     = sanitize_text_field( $_POST['subject'] );
	$content     = wp_kses_post( $_POST['message_content'] );
	$thread_id   = ! empty( $_POST['thread_id'] ) ? sanitize_text_field( $_POST['thread_id'] ) : uniqid('thread_');

	$msg_id = wp_insert_post( array(
		'post_title'   => $subject,
		'post_content' => $content,
		'post_type'    => 'jobs_message',
		'post_status'  => 'publish',
		'post_author'  => $user_id,
	) );

	if ( $msg_id ) {
		update_post_meta( $msg_id, '_jobs_receiver_id', $receiver_id );
		update_post_meta( $msg_id, '_jobs_thread_id', $thread_id );
		update_post_meta( $msg_id, '_jobs_read_status', 'unread' );

		// Handle attachment
		if ( ! empty( $_FILES['attachment']['name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$movefile = wp_handle_upload( $_FILES['attachment'], array( 'test_form' => false ) );
			if ( $movefile && ! isset( $movefile['error'] ) ) {
				update_post_meta( $msg_id, '_jobs_attachment', $movefile['url'] );
			}
		}

		// Notify receiver
		$notifs = get_user_meta( $receiver_id, '_jobs_notifications', true ) ?: array();
		$notifs[] = array(
			'message' => sprintf( __( 'You received a new message from %s.', 'jobs' ), wp_get_current_user()->display_name ),
			'time'    => time(),
		);
		update_user_meta( $receiver_id, '_jobs_notifications', $notifs );

		echo '<div class="jobs-msg">' . __( 'Message sent successfully.', 'jobs' ) . '</div>';
	}
}

$view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'inbox';
?>

<div class="jobs-messages-system">
	<div class="messages-header" style="display: flex; justify-content: space-between; margin-bottom: 25px;">
		<h2><?php _e( 'Messages', 'jobs' ); ?></h2>
		<div class="message-nav">
			<a href="?tab=messages&view=inbox" class="button <?php echo $view == 'inbox' ? 'button-primary' : ''; ?>"><?php _e( 'Inbox', 'jobs' ); ?></a>
			<a href="?tab=messages&view=sent" class="button <?php echo $view == 'sent' ? 'button-primary' : ''; ?>"><?php _e( 'Sent', 'jobs' ); ?></a>
		</div>
	</div>

	<?php if ( $view == 'inbox' || $view == 'sent' ) :
		$args = array(
			'post_type' => 'jobs_message',
			'posts_per_page' => -1,
		);
		if ( $view == 'inbox' ) {
			$args['meta_query'] = array(
				array( 'key' => '_jobs_receiver_id', 'value' => $user_id )
			);
		} else {
			$args['author'] = $user_id;
		}
		$messages = new WP_Query( $args );
	?>
		<table class="jobs-table">
			<thead>
				<tr>
					<th><?php echo $view == 'inbox' ? __( 'From', 'jobs' ) : __( 'To', 'jobs' ); ?></th>
					<th><?php _e( 'Subject', 'jobs' ); ?></th>
					<th><?php _e( 'Date', 'jobs' ); ?></th>
					<th><?php _e( 'Status', 'jobs' ); ?></th>
					<th><?php _e( 'Action', 'jobs' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( $messages->have_posts() ) : while ( $messages->have_posts() ) : $messages->the_post();
					$other_user_id = ($view == 'inbox') ? get_the_author_meta('ID') : get_post_meta(get_the_ID(), '_jobs_receiver_id', true);
					$other_user = get_userdata($other_user_id);
					$status = get_post_meta(get_the_ID(), '_jobs_read_status', true);
				?>
					<tr class="<?php echo $status == 'unread' && $view == 'inbox' ? 'unread-msg' : ''; ?>">
						<td><strong><?php echo $other_user ? esc_html($other_user->display_name) : 'System'; ?></strong></td>
						<td><?php the_title(); ?></td>
						<td><?php echo get_the_date('M j, H:i'); ?></td>
						<td><span class="status-badge <?php echo $status == 'read' ? 'status-publish' : 'status-pending'; ?>"><?php echo esc_html($status); ?></span></td>
						<td><a href="?tab=messages&view=single&msg_id=<?php the_ID(); ?>"><?php _e( 'View', 'jobs' ); ?></a></td>
					</tr>
				<?php endwhile; wp_reset_postdata(); else : ?>
					<tr><td colspan="5"><?php _e( 'No messages found.', 'jobs' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>

	<?php elseif ( $view == 'single' ) :
		if ( isset($_GET['action']) && $_GET['action'] == 'new' ) :
			$receiver_id = intval($_GET['to']);
			$receiver = get_userdata($receiver_id);
	?>
		<div class="new-message-view">
			<h3><?php printf( __( 'New Message to %s', 'jobs' ), $receiver ? $receiver->display_name : 'User' ); ?></h3>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'jobs_send_msg', 'jobs_message_nonce' ); ?>
				<input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
				<p>
					<label><?php _e( 'Subject', 'jobs' ); ?></label>
					<input type="text" name="subject" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;">
				</p>
				<p>
					<label><?php _e( 'Message', 'jobs' ); ?></label>
					<textarea name="message_content" rows="10" placeholder="<?php _e( 'Type your message here...', 'jobs' ); ?>" required></textarea>
				</p>
				<p>
					<label><?php _e( 'Attach File (optional)', 'jobs' ); ?></label>
					<input type="file" name="attachment">
				</p>
				<input type="submit" name="jobs_send_message" class="button button-primary" value="<?php _e( 'Send Message', 'jobs' ); ?>">
			</form>
		</div>
	<?php else :
		$msg_id = intval($_GET['msg_id']);
		$msg = get_post($msg_id);
		if ( $msg && ($msg->post_author == $user_id || get_post_meta($msg_id, '_jobs_receiver_id', true) == $user_id) ) :
			if ( get_post_meta($msg_id, '_jobs_receiver_id', true) == $user_id ) {
				update_post_meta( $msg_id, '_jobs_read_status', 'read' );
			}
			$attachment = get_post_meta($msg_id, '_jobs_attachment', true);
			$sender = get_userdata($msg->post_author);
	?>
		<div class="single-message-view">
			<div class="message-meta" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
				<h3><?php echo esc_html($msg->post_title); ?></h3>
				<p><strong><?php _e( 'From:', 'jobs' ); ?></strong> <?php echo esc_html($sender->display_name); ?> | <strong><?php _e( 'Date:', 'jobs' ); ?></strong> <?php echo get_the_date('F j, Y g:i a', $msg_id); ?></p>
			</div>
			<div class="message-body" style="padding: 0 20px; line-height: 1.8;">
				<?php echo wpautop($msg->post_content); ?>
			</div>
			<?php if ( $attachment ) : ?>
				<div class="message-attachment" style="margin-top: 30px; padding: 15px; background: #eef; border-radius: 6px;">
					<i class="dashicons dashicons-paperclip"></i> <a href="<?php echo esc_url($attachment); ?>" target="_blank"><?php _e( 'View Attachment', 'jobs' ); ?></a>
				</div>
			<?php endif; ?>

			<hr style="margin: 40px 0;">

			<div class="reply-section">
				<h4><?php _e( 'Reply', 'jobs' ); ?></h4>
				<form method="post" enctype="multipart/form-data">
					<?php wp_nonce_field( 'jobs_send_msg', 'jobs_message_nonce' ); ?>
					<input type="hidden" name="receiver_id" value="<?php echo $msg->post_author == $user_id ? get_post_meta($msg_id, '_jobs_receiver_id', true) : $msg->post_author; ?>">
					<input type="hidden" name="subject" value="Re: <?php echo esc_attr($msg->post_title); ?>">
					<input type="hidden" name="thread_id" value="<?php echo esc_attr(get_post_meta($msg_id, '_jobs_thread_id', true)); ?>">
					<textarea name="message_content" rows="5" placeholder="<?php _e( 'Type your reply here...', 'jobs' ); ?>" required></textarea>
					<p>
						<label><?php _e( 'Attach File (optional)', 'jobs' ); ?></label>
						<input type="file" name="attachment">
					</p>
					<input type="submit" name="jobs_send_message" class="button button-primary" value="<?php _e( 'Send Reply', 'jobs' ); ?>">
				</form>
			</div>
		</div>
	<?php endif; endif; endif; ?>
</div>

<style>
.unread-msg { background: #fffdf0; font-weight: 500; }
.single-message-view textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; }
</style>
