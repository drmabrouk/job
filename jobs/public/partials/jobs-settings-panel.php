<?php
/**
 * Account Settings Panel
 */
$user = wp_get_current_user();
$notif_pref = get_user_meta( $user->ID, '_jobs_notif_pref', true ) ?: 'all';
$privacy_pref = get_user_meta( $user->ID, '_jobs_privacy_pref', true ) ?: 'public';
$profile_public = get_user_meta( $user->ID, '_jobs_profile_public', true ) ?: 'no';
$profile_indexed = get_user_meta( $user->ID, '_jobs_profile_indexed', true ) ?: 'no';

if ( isset( $_POST['jobs_save_account_settings'] ) && wp_verify_nonce( $_POST['jobs_settings_nonce'], 'jobs_save_settings' ) ) {
	update_user_meta( $user->ID, '_jobs_notif_pref', sanitize_text_field( $_POST['notif_pref'] ) );
	update_user_meta( $user->ID, '_jobs_privacy_pref', sanitize_text_field( $_POST['privacy_pref'] ) );

	if ( in_array( 'job_seeker', (array) $user->roles ) ) {
		update_user_meta( $user->ID, '_jobs_profile_public', isset($_POST['profile_public']) ? 'yes' : 'no' );
		update_user_meta( $user->ID, '_jobs_profile_indexed', isset($_POST['profile_indexed']) ? 'yes' : 'no' );
	}

	echo '<div class="jobs-msg">' . __( 'Settings saved successfully.', 'jobs' ) . '</div>';
}
?>
<div class="jobs-settings-panel">
	<h2><?php _e( 'Account Settings', 'jobs' ); ?></h2>
	<form method="post" action="">
		<?php wp_nonce_field( 'jobs_save_settings', 'jobs_settings_nonce' ); ?>

		<div class="settings-group">
			<h3><?php _e( 'Notification Preferences', 'jobs' ); ?></h3>
			<p>
				<label>
					<input type="radio" name="notif_pref" value="all" <?php checked( $notif_pref, 'all' ); ?>> <?php _e( 'All Notifications', 'jobs' ); ?>
				</label><br>
				<label>
					<input type="radio" name="notif_pref" value="important" <?php checked( $notif_pref, 'important' ); ?>> <?php _e( 'Only Important Updates', 'jobs' ); ?>
				</label><br>
				<label>
					<input type="radio" name="notif_pref" value="none" <?php checked( $notif_pref, 'none' ); ?>> <?php _e( 'None', 'jobs' ); ?>
				</label>
			</p>
		</div>

		<div class="settings-group">
			<h3><?php _e( 'Privacy Settings', 'jobs' ); ?></h3>
			<p>
				<label>
					<input type="radio" name="privacy_pref" value="public" <?php checked( $privacy_pref, 'public' ); ?>> <?php _e( 'Public (Visible to everyone)', 'jobs' ); ?>
				</label><br>
				<label>
					<input type="radio" name="privacy_pref" value="private" <?php checked( $privacy_pref, 'private' ); ?>> <?php _e( 'Private (Only employers)', 'jobs' ); ?>
				</label>
			</p>
		</div>

		<?php if ( in_array( 'job_seeker', (array) $user->roles ) ) : ?>
		<div class="settings-group">
			<h3><?php _e( 'Public Profile Settings', 'jobs' ); ?></h3>
			<p>
				<label>
					<input type="checkbox" name="profile_public" value="yes" <?php checked( $profile_public, 'yes' ); ?>> <?php _e( 'Enable Public Profile', 'jobs' ); ?>
				</label><br>
				<small><?php printf( __( 'Your shareable link: %s', 'jobs' ), '<code>' . home_url( '/job-seeker/' . $user->user_nicename ) . '</code>' ); ?></small>
			</p>
			<p>
				<label>
					<input type="checkbox" name="profile_indexed" value="yes" <?php checked( $profile_indexed, 'yes' ); ?>> <?php _e( 'Allow Search Engines to Index My Profile', 'jobs' ); ?>
				</label>
			</p>
		</div>
		<?php endif; ?>

		<p class="submit">
			<input type="submit" name="jobs_save_account_settings" class="button button-primary" value="<?php _e( 'Save Settings', 'jobs' ); ?>">
		</p>
	</form>
</div>
