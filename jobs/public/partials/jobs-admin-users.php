<?php
/**
 * Frontend User Management for Administrators
 */
$user_id = get_current_user_id();

// Search and filter logic
$search = isset($_GET['user_search']) ? sanitize_text_field($_GET['user_search']) : '';
$role_filter = isset($_GET['role_filter']) ? sanitize_text_field($_GET['role_filter']) : '';

$args = array(
	'number' => 20,
);

if ( ! empty($search) ) {
	$args['search'] = '*' . $search . '*';
}

if ( ! empty($role_filter) ) {
	$args['role'] = $role_filter;
}

$users = get_users($args);
$roles = get_editable_roles();
?>

<div class="jobs-admin-users-manager">
	<div class="manager-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
		<h3><?php _e( 'User Management', 'jobs' ); ?></h3>
		<button class="jobs-button" id="add-user-btn"><?php _e( 'Add New User', 'jobs' ); ?></button>
	</div>

	<div class="filters-bar" style="display: flex; gap: 15px; margin-bottom: 25px;">
		<form method="get" style="display: flex; gap: 10px; flex: 1;">
			<input type="hidden" name="tab" value="overview">
			<input type="hidden" name="admin_tab" value="users">
			<input type="text" name="user_search" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e( 'Search by name or email...', 'jobs' ); ?>" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
			<select name="role_filter" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
				<option value=""><?php _e( 'All Roles', 'jobs' ); ?></option>
				<?php foreach ( $roles as $role_key => $role_data ) : ?>
					<option value="<?php echo esc_attr($role_key); ?>" <?php selected($role_filter, $role_key); ?>><?php echo esc_html($role_data['name']); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="jobs-button btn-sm"><?php _e( 'Filter', 'jobs' ); ?></button>
		</form>
	</div>

	<div class="users-table-wrapper" style="background: #fff; padding: 25px; border-radius: 16px; border: 1px solid #f0f0f0;">
		<table class="jobs-table">
			<thead>
				<tr>
					<th><?php _e( 'User', 'jobs' ); ?></th>
					<th><?php _e( 'Email', 'jobs' ); ?></th>
					<th><?php _e( 'Role', 'jobs' ); ?></th>
					<th><?php _e( 'Registered', 'jobs' ); ?></th>
					<th><?php _e( 'Actions', 'jobs' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty($users) ) : foreach ( $users as $u ) :
					$u_roles = $u->roles;
					$display_role = !empty($u_roles) ? translate_user_role($roles[$u_roles[0]]['name']) : 'N/A';
				?>
					<tr>
						<td>
							<div style="display: flex; align-items: center; gap: 10px;">
								<?php echo get_avatar($u->ID, 48, '', '', array('class' => 'circular-avatar-large')); ?>
								<div style="display:flex; flex-direction:column;">
									<strong>
										<?php echo esc_html($u->display_name); ?>
										<?php if ( get_user_meta($u->ID, '_jobs_verified', true) === 'yes' ) : ?>
											<span class="verified-badge" title="<?php _e('Verified Account', 'jobs'); ?>">✔</span>
										<?php endif; ?>
									</strong>
									<small style="color:#718096;"><?php echo esc_html($u->user_login); ?></small>
								</div>
							</div>
						</td>
						<td><?php echo esc_html($u->user_email); ?></td>
						<td><span class="role-label"><?php echo esc_html($display_role); ?></span></td>
						<td><?php echo date('M j, Y', strtotime($u->user_registered)); ?></td>
						<td>
							<div style="display: flex; gap: 10px;">
								<a href="#" class="verify-user-link" data-id="<?php echo $u->ID; ?>" title="<?php _e( 'Toggle Verification', 'jobs' ); ?>"><i class="dashicons dashicons-shield"></i></a>
								<a href="#" class="edit-user-link" data-id="<?php echo $u->ID; ?>" title="<?php _e( 'Edit', 'jobs' ); ?>"><i class="dashicons dashicons-edit"></i></a>
								<a href="#" class="delete-user-link" data-id="<?php echo $u->ID; ?>" style="color: #e53e3e;" title="<?php _e( 'Delete', 'jobs' ); ?>"><i class="dashicons dashicons-trash"></i></a>
							</div>
						</td>
					</tr>
				<?php endforeach; else : ?>
					<tr><td colspan="5" style="text-align: center; padding: 40px; color: #718096;"><?php _e( 'No users found matching your criteria.', 'jobs' ); ?></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<!-- Modal Placeholders for Add/Edit -->
<div id="user-modal" class="jobs-modal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
	<div style="background: #fff; margin: 5% auto; padding: 40px; width: 500px; border-radius: 24px;">
		<h4 id="modal-title"><?php _e( 'Add New User', 'jobs' ); ?></h4>
		<form id="admin-user-form" style="margin-top: 25px;">
			<div class="form-row" style="display: flex; gap: 15px; margin-bottom: 15px;">
				<div class="form-group" style="flex: 1;">
					<label><?php _e( 'First Name', 'jobs' ); ?></label>
					<input type="text" name="first_name" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
				</div>
				<div class="form-group" style="flex: 1;">
					<label><?php _e( 'Last Name', 'jobs' ); ?></label>
					<input type="text" name="last_name" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
				</div>
			</div>
			<div class="form-group" style="margin-bottom: 15px;">
				<label><?php _e( 'Email', 'jobs' ); ?></label>
				<input type="email" name="user_email" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
			</div>
			<div class="form-group" style="margin-bottom: 25px;">
				<label><?php _e( 'Role', 'jobs' ); ?></label>
				<select name="role" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
					<?php foreach ( $roles as $role_key => $role_data ) : ?>
						<option value="<?php echo esc_attr($role_key); ?>"><?php echo esc_html($role_data['name']); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div style="text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
				<button type="button" class="jobs-button btn-outline" onclick="document.getElementById('user-modal').style.display='none'"><?php _e( 'Cancel', 'jobs' ); ?></button>
				<button type="submit" class="jobs-button"><?php _e( 'Save User', 'jobs' ); ?></button>
			</div>
		</form>
	</div>
</div>
