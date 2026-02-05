<?php
/**
 * Professional Translation & Localisation Control Center
 */
$categories = array(
	'general' => __( 'General Interface', 'jobs' ),
	'dashboard' => __( 'Dashboard Elements', 'jobs' ),
	'notifications' => __( 'Notifications & Alerts', 'jobs' ),
	'messages' => __( 'Messaging System', 'jobs' ),
	'jobs' => __( 'Job Management', 'jobs' ),
);

$current_cat = isset($_GET['trans_cat']) ? sanitize_text_field($_GET['trans_cat']) : 'general';

$strings = array(
	'general' => array(
		'Find Jobs' => 'بحث عن وظائف',
		'Employers' => 'أصحاب العمل',
		'Trusted by 100,000+ professionals' => 'موثوق من قبل أكثر من 100,000 محترف',
	),
	'dashboard' => array(
		'Overview' => 'نظرة عامة',
		'Profile & Settings' => 'الملف الشخصي والإعدادات',
		'Applications' => 'الطلبات',
	),
	'notifications' => array(
		'New application received' => 'تم استلام طلب جديد',
		'Job expired' => 'انتهت صلاحية الوظيفة',
	),
	'messages' => array(
		'Type your message here...' => 'اكتب رسالتك هنا...',
		'Send Message' => 'إرسال الرسالة',
	),
	'jobs' => array(
		'Job Title' => 'عنوان الوظيفة',
		'Job Description' => 'وصف الوظيفة',
		'Quick Apply' => 'تقدم سريع',
	),
);
?>

<div class="jobs-translation-center">
	<div class="center-header" style="margin-bottom: 40px;">
		<h3 style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"><?php _e( 'Localisation Control Center', 'jobs' ); ?></h3>
		<p style="color: #718096;"><?php _e( 'Manage and verify all system translations to ensure a native experience for all users.', 'jobs' ); ?></p>
	</div>

	<div class="translation-layout" style="display: grid; grid-template-columns: 280px 1fr; gap: 40px;">
		<aside class="translation-sidebar">
			<ul style="list-style: none; padding: 0; margin: 0; background: #fff; border-radius: 16px; border: 1px solid #f0f0f0; overflow: hidden;">
				<?php foreach ( $categories as $key => $label ) : ?>
					<li style="border-bottom: 1px solid #f7fafc;">
						<a href="?tab=overview&admin_tab=translations&trans_cat=<?php echo $key; ?>" style="display: block; padding: 15px 20px; text-decoration: none; color: <?php echo ($current_cat == $key) ? 'var(--primary-color)' : '#4a5568'; ?>; background: <?php echo ($current_cat == $key) ? '#f0f7ff' : 'transparent'; ?>; font-weight: 600;">
							<?php echo esc_html($label); ?>
							<?php if($current_cat == $key) echo '<i class="dashicons dashicons-arrow-right-alt2" style="float: right;"></i>'; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>

		<main class="translation-content">
			<div class="account-section" style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #f0f0f0;">
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
					<h4 style="margin: 0;"><?php echo $categories[$current_cat]; ?></h4>
					<button class="jobs-button"><?php _e( 'Save All Changes', 'jobs' ); ?></button>
				</div>

				<table class="jobs-table">
					<thead>
						<tr>
							<th style="width: 45%;"><?php _e( 'English String', 'jobs' ); ?></th>
							<th><?php _e( 'Arabic Translation', 'jobs' ); ?></th>
							<th style="width: 100px;"><?php _e( 'Verified', 'jobs' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $strings[$current_cat] as $en => $ar ) : ?>
							<tr>
								<td><code style="background: #f7fafc; padding: 4px 8px; border-radius: 4px;"><?php echo esc_html($en); ?></code></td>
								<td>
									<input type="text" value="<?php echo esc_attr($ar); ?>" style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; direction: rtl;">
								</td>
								<td style="text-align: center;">
									<input type="checkbox" checked>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</main>
	</div>
</div>
