<?php
/**
 * Account Settings Panel - Advanced Configuration
 */
$user = wp_get_current_user();
$notif_pref = get_user_meta( $user->ID, '_jobs_notif_pref', true ) ?: 'all';
$privacy_pref = get_user_meta( $user->ID, '_jobs_privacy_pref', true ) ?: 'public';
$profile_public = get_user_meta( $user->ID, '_jobs_profile_public', true ) ?: 'no';
$profile_indexed = get_user_meta( $user->ID, '_jobs_profile_indexed', true ) ?: 'no';
$dashboard_layout = get_user_meta( $user->ID, '_jobs_dash_layout', true ) ?: 'grid';
$settings_view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'general';

?>
<div class="jobs-settings-panel-refined">
	<div class="settings-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
		<h2 style="margin: 0; color: var(--primary-color); font-size: 28px; font-weight: 700;"><?php _e( 'Account Settings', 'jobs' ); ?></h2>
		<div class="settings-nav-pills" style="display: flex; gap: 10px; background: #f1f3f5; padding: 5px; border-radius: 12px;">
			<a href="?tab=settings&view=general" class="nav-pill <?php echo $settings_view == 'general' ? 'active' : ''; ?>" style="padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; color: <?php echo $settings_view == 'general' ? '#fff' : '#4a5568'; ?>; background: <?php echo $settings_view == 'general' ? 'var(--primary-color)' : 'transparent'; ?>;"><?php _e( 'General', 'jobs' ); ?></a>
			<a href="?tab=settings&view=security" class="nav-pill <?php echo $settings_view == 'security' ? 'active' : ''; ?>" style="padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; color: <?php echo $settings_view == 'security' ? '#fff' : '#4a5568'; ?>; background: <?php echo $settings_view == 'security' ? 'var(--primary-color)' : 'transparent'; ?>;"><?php _e( 'Security', 'jobs' ); ?></a>
		</div>
	</div>

	<?php if ( $settings_view == 'general' ) : ?>
	<form method="post" action="" class="account-section" style="background:#fff; padding:40px; border-radius:24px; border:1px solid #f0f0f0;">
		<?php wp_nonce_field( 'jobs_save_settings', 'jobs_settings_nonce' ); ?>

		<div class="settings-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
			<div class="settings-group">
				<h3 style="font-size: 18px; margin-bottom: 20px; color: #2d3748;"><?php _e( 'Notification Preferences', 'jobs' ); ?></h3>
				<div class="form-group" style="margin-bottom: 20px;">
					<label style="display:block; margin-bottom:10px; font-weight:600;"><?php _e( 'Email Alerts Frequency', 'jobs' ); ?></label>
					<select name="notif_freq" class="jobs-filter-select" style="width:100%; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">
						<option value="instant"><?php _e( 'Instant (Real-time)', 'jobs' ); ?></option>
						<option value="daily"><?php _e( 'Daily Digest', 'jobs' ); ?></option>
						<option value="weekly"><?php _e( 'Weekly Summary', 'jobs' ); ?></option>
						<option value="none"><?php _e( 'Disabled', 'jobs' ); ?></option>
					</select>
				</div>
				<div class="checkbox-group" style="display:flex; flex-direction:column; gap:12px;">
					<label><input type="checkbox" name="notif_msg" value="yes" checked> <?php _e( 'New Message Alerts', 'jobs' ); ?></label>
					<label><input type="checkbox" name="notif_app" value="yes" checked> <?php _e( 'Application Status Changes', 'jobs' ); ?></label>
					<label><input type="checkbox" name="notif_matches" value="yes"> <?php _e( 'New Job Matches', 'jobs' ); ?></label>
				</div>
			</div>

			<div class="settings-group">
				<h3 style="font-size: 18px; margin-bottom: 20px; color: #2d3748;"><?php _e( 'Privacy & Visibility', 'jobs' ); ?></h3>
				<div class="form-group" style="margin-bottom: 20px;">
					<label style="display:block; margin-bottom:10px; font-weight:600;"><?php _e( 'Profile Visibility', 'jobs' ); ?></label>
					<select name="privacy_pref" class="jobs-filter-select" style="width:100%; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">
						<option value="public" <?php selected($privacy_pref, 'public'); ?>><?php _e( 'Public - Visible to everyone', 'jobs' ); ?></option>
						<option value="verified" <?php selected($privacy_pref, 'verified'); ?>><?php _e( 'Verified - Logged-in users only', 'jobs' ); ?></option>
						<option value="employers" <?php selected($privacy_pref, 'employers'); ?>><?php _e( 'Employers - Verified employers only', 'jobs' ); ?></option>
						<option value="private" <?php selected($privacy_pref, 'private'); ?>><?php _e( 'Private - Hidden', 'jobs' ); ?></option>
					</select>
				</div>
				<div class="checkbox-group" style="display:flex; flex-direction:column; gap:12px;">
					<label><input type="checkbox" name="show_online" value="yes" checked> <?php _e( 'Show my online status', 'jobs' ); ?></label>
					<label><input type="checkbox" name="profile_indexed" value="yes" <?php checked($profile_indexed, 'yes'); ?>> <?php _e( 'Allow Search Engines indexing', 'jobs' ); ?></label>
				</div>
			</div>
		</div>

		<div class="settings-footer" style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #f0f0f0;">
			<input type="submit" name="jobs_save_account_settings" class="jobs-button" value="<?php _e( 'Save Changes', 'jobs' ); ?>" style="padding: 15px 40px;">
		</div>
	</form>
	<?php else : ?>
		<div class="security-settings account-section" style="background:#fff; padding:40px; border-radius:24px; border:1px solid #f0f0f0;">
			<div class="settings-group" style="margin-bottom: 40px;">
				<h3 style="font-size: 18px; margin-bottom: 25px; color: #2d3748;"><?php _e( 'Update Password', 'jobs' ); ?></h3>
				<form method="post" class="jobs-frontend-form" style="max-width: 500px;">
					<?php wp_nonce_field( 'jobs_save_security', 'jobs_security_nonce' ); ?>
					<div class="form-group" style="margin-bottom: 15px;">
						<label><?php _e( 'New Password', 'jobs' ); ?></label>
						<input type="password" name="new_pass" required style="width:100%; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">
					</div>
					<div class="form-group" style="margin-bottom: 25px;">
						<label><?php _e( 'Confirm New Password', 'jobs' ); ?></label>
						<input type="password" name="confirm_pass" required style="width:100%; padding:12px; border-radius:10px; border:1px solid #e2e8f0;">
					</div>
					<input type="submit" name="jobs_change_password" class="jobs-button" value="<?php _e( 'Update Password', 'jobs' ); ?>">
				</form>
			</div>

			<div class="settings-group">
				<h3 style="font-size: 18px; margin-bottom: 20px; color: #2d3748;"><?php _e( 'Active Sessions', 'jobs' ); ?></h3>
				<p style="color: #718096; margin-bottom: 20px;"><?php _e( 'Manage your logged-in devices and active sessions.', 'jobs' ); ?></p>
				<table class="jobs-table" style="width:100%; border-collapse: collapse;">
					<thead>
						<tr style="text-align: left; border-bottom: 2px solid #f7fafc;">
							<th style="padding: 15px;"><?php _e( 'IP Address', 'jobs' ); ?></th>
							<th style="padding: 15px;"><?php _e( 'Last Activity', 'jobs' ); ?></th>
							<th style="padding: 15px;"><?php _e( 'Action', 'jobs' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sessions = wp_get_all_sessions();
						foreach ( $sessions as $session ) : ?>
							<tr style="border-bottom: 1px solid #f7fafc;">
								<td style="padding: 15px;"><?php echo esc_html($session['ip']); ?></td>
								<td style="padding: 15px;"><?php echo date('M j, H:i', $session['login']); ?></td>
								<td style="padding: 15px;"><span class="status-badge" style="background: #e6f7ef; color: #27ae60; font-size: 10px;"><?php _e( 'Active', 'jobs' ); ?></span></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<form method="post" style="margin-top: 30px;">
					<?php wp_nonce_field( 'jobs_save_security', 'jobs_security_nonce' ); ?>
					<input type="submit" name="jobs_logout_others" class="jobs-button btn-outline" value="<?php _e( 'Log out from all other devices', 'jobs' ); ?>">
				</form>
			</div>
		</div>
	<?php endif; ?>
</div>
