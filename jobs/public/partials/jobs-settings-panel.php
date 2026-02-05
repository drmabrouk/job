<?php
/**
 * Account Settings Panel
 */
$user = wp_get_current_user();
$notif_pref = get_user_meta( $user->ID, '_jobs_notif_pref', true ) ?: 'all';
$privacy_pref = get_user_meta( $user->ID, '_jobs_privacy_pref', true ) ?: 'public';
$profile_public = get_user_meta( $user->ID, '_jobs_profile_public', true ) ?: 'no';
$profile_indexed = get_user_meta( $user->ID, '_jobs_profile_indexed', true ) ?: 'no';
$dashboard_layout = get_user_meta( $user->ID, '_jobs_dash_layout', true ) ?: 'grid';
$settings_view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'general';

// Handle Password Change
if ( isset( $_POST['jobs_change_password'] ) && wp_verify_nonce( $_POST['jobs_security_nonce'], 'jobs_save_security' ) ) {
	if ( ! empty( $_POST['new_pass'] ) && $_POST['new_pass'] === $_POST['confirm_pass'] ) {
		wp_set_password( $_POST['new_pass'], $user->ID );
		echo '<div class="jobs-msg">' . __( 'Password changed successfully.', 'jobs' ) . '</div>';
	} else {
		echo '<div class="jobs-msg" style="background:#f8d7da; color:#721c24;">' . __( 'Passwords do not match.', 'jobs' ) . '</div>';
	}
}

// Handle Logout from other devices
if ( isset( $_POST['jobs_logout_others'] ) && wp_verify_nonce( $_POST['jobs_security_nonce'], 'jobs_save_security' ) ) {
	wp_destroy_other_sessions();
	echo '<div class="jobs-msg">' . __( 'Logged out from all other devices.', 'jobs' ) . '</div>';
}

if ( isset( $_POST['jobs_save_account_settings'] ) && wp_verify_nonce( $_POST['jobs_settings_nonce'], 'jobs_save_settings' ) ) {
	update_user_meta( $user->ID, '_jobs_notif_pref', sanitize_text_field( $_POST['notif_pref'] ) );
	update_user_meta( $user->ID, '_jobs_privacy_pref', sanitize_text_field( $_POST['privacy_pref'] ) );
	update_user_meta( $user->ID, '_jobs_dash_layout', sanitize_text_field( $_POST['dash_layout'] ) );
	update_user_meta( $user->ID, '_jobs_locale', sanitize_text_field( $_POST['user_lang'] ) );

	if ( in_array( 'job_seeker', (array) $user->roles ) ) {
		update_user_meta( $user->ID, '_jobs_profile_public', isset($_POST['profile_public']) ? 'yes' : 'no' );
		update_user_meta( $user->ID, '_jobs_profile_indexed', isset($_POST['profile_indexed']) ? 'yes' : 'no' );
	}

	echo '<div class="jobs-msg">' . __( 'Settings saved successfully.', 'jobs' ) . '</div>';
}
?>
<div class="jobs-settings-panel">
	<div class="settings-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h2><?php _e( 'Account Settings', 'jobs' ); ?></h2>
		<div class="settings-nav">
			<a href="?tab=settings&view=general" class="button <?php echo $settings_view == 'general' ? 'button-primary' : ''; ?>"><?php _e( 'General', 'jobs' ); ?></a>
			<a href="?tab=settings&view=security" class="button <?php echo $settings_view == 'security' ? 'button-primary' : ''; ?>"><?php _e( 'Security', 'jobs' ); ?></a>
		</div>
	</div>

	<?php if ( $settings_view == 'general' ) : ?>
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
			<h3><?php _e( 'Dashboard Layout', 'jobs' ); ?></h3>
			<p>
				<label>
					<input type="radio" name="dash_layout" value="grid" <?php checked( $dashboard_layout, 'grid' ); ?>> <?php _e( 'Grid View', 'jobs' ); ?>
				</label><br>
				<label>
					<input type="radio" name="dash_layout" value="list" <?php checked( $dashboard_layout, 'list' ); ?>> <?php _e( 'List View', 'jobs' ); ?>
				</label>
			</p>
		</div>

		<div class="settings-group">
			<h3><?php _e( 'Interface Language', 'jobs' ); ?></h3>
			<p>
				<select name="user_lang">
					<option value="en_US" <?php selected( get_user_meta($user->ID, '_jobs_locale', true), 'en_US' ); ?>>English</option>
					<option value="ar" <?php selected( get_user_meta($user->ID, '_jobs_locale', true), 'ar' ); ?>>العربية</option>
				</select>
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
	<?php else : ?>
		<div class="security-settings">
			<div class="settings-group">
				<h3><?php _e( 'Change Password', 'jobs' ); ?></h3>
				<form method="post" class="jobs-frontend-form">
					<?php wp_nonce_field( 'jobs_save_security', 'jobs_security_nonce' ); ?>
					<p>
						<label><?php _e( 'New Password', 'jobs' ); ?></label>
						<input type="password" name="new_pass" required>
					</p>
					<p>
						<label><?php _e( 'Confirm New Password', 'jobs' ); ?></label>
						<input type="password" name="confirm_pass" required>
					</p>
					<input type="submit" name="jobs_change_password" class="button button-primary" value="<?php _e( 'Update Password', 'jobs' ); ?>">
				</form>
			</div>

			<div class="settings-group">
				<h3><?php _e( 'Active Sessions', 'jobs' ); ?></h3>
				<p><?php _e( 'You are currently logged into this account on these devices:', 'jobs' ); ?></p>
				<table class="jobs-table">
					<thead>
						<tr>
							<th><?php _e( 'IP Address', 'jobs' ); ?></th>
							<th><?php _e( 'Last Activity', 'jobs' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sessions = wp_get_all_sessions();
						foreach ( $sessions as $session ) : ?>
							<tr>
								<td><?php echo esc_html($session['ip']); ?></td>
								<td><?php echo date('M j, H:i', $session['login']); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<form method="post" style="margin-top: 20px;">
					<?php wp_nonce_field( 'jobs_save_security', 'jobs_security_nonce' ); ?>
					<input type="submit" name="jobs_logout_others" class="button" value="<?php _e( 'Logout from all other devices', 'jobs' ); ?>">
				</form>
			</div>
		</div>
	<?php endif; ?>
</div>
