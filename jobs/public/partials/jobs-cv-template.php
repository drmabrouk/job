<?php
/**
 * Professional CV Template for PDF Generation
 */
$user_id = get_current_user_id();
$user = get_userdata( $user_id );
$meta = get_user_meta( $user_id );
?>

<div id="cv-content-to-export" class="cv-document-modern">
	<div class="cv-sidebar-accent"></div>
	<div class="cv-main-layout">
		<div class="cv-left-col">
			<div class="cv-profile-img">
				<?php echo get_avatar($user_id, 150); ?>
			</div>

			<div class="cv-section-left">
				<h4>Contact</h4>
				<ul>
					<li><i class="fas fa-envelope"></i> <?php echo $user->user_email; ?></li>
					<li><i class="fas fa-phone"></i> <?php echo $meta['_job_phone'][0] ?? 'N/A'; ?></li>
					<li><i class="fas fa-map-marker-alt"></i> <?php echo $meta['_job_location'][0] ?? 'N/A'; ?></li>
				</ul>
			</div>

			<div class="cv-section-left">
				<h4>Skills</h4>
				<div class="cv-skills-grid">
					<?php
					$skills = explode(',', $meta['_job_skills'][0] ?? '');
					foreach($skills as $skill) if(trim($skill)) echo '<span>'.trim($skill).'</span>';
					?>
				</div>
			</div>
		</div>

		<div class="cv-right-col">
			<div class="cv-header">
				<h1><?php echo $user->display_name; ?></h1>
				<h3><?php echo $meta['_job_title'][0] ?? 'Professional'; ?></h3>
			</div>

			<div class="cv-section-right">
				<h4 class="cv-title-border">About Me</h4>
				<p><?php echo $user->description ?: 'No bio provided.'; ?></p>
			</div>

			<div class="cv-section-right">
				<h4 class="cv-title-border">Experience</h4>
				<div class="cv-item">
					<h5>Professional Experience</h5>
					<p><?php echo nl2br($meta['_job_experience'][0] ?? 'Details not provided.'); ?></p>
				</div>
			</div>

			<div class="cv-section-right">
				<h4 class="cv-title-border">Education</h4>
				<div class="cv-item">
					<p><?php echo nl2br($meta['_job_education'][0] ?? 'Details not provided.'); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="cv-export-controls">
	<button id="download-cv-pdf" class="jobs-button btn-primary-modern">
		<i class="fas fa-file-pdf"></i> Download Official PDF CV
	</button>
</div>

<style>
.cv-document-modern {
	background: #fff;
	width: 210mm;
	min-height: 297mm;
	margin: 20px auto;
	box-shadow: 0 0 20px rgba(0,0,0,0.1);
	position: relative;
	display: flex;
	overflow: hidden;
	font-family: 'Rubik', sans-serif;
}
.cv-sidebar-accent {
	position: absolute;
	left: 0;
	top: 0;
	bottom: 0;
	width: 10px;
	background: var(--primary-color);
}
.cv-main-layout {
	display: flex;
	width: 100%;
}
.cv-left-col {
	width: 35%;
	background: #f8fafc;
	padding: 40px 30px;
}
.cv-right-col {
	width: 65%;
	padding: 40px 40px;
}
.cv-profile-img { text-align: center; margin-bottom: 30px; }
.cv-profile-img img { border-radius: 50%; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.cv-section-left h4 { color: var(--primary-color); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
.cv-section-left ul { list-style: none; padding: 0; }
.cv-section-left li { margin-bottom: 10px; font-size: 13px; color: #4a5568; display: flex; align-items: center; gap: 8px; }
.cv-skills-grid { display: flex; flex-wrap: wrap; gap: 5px; }
.cv-skills-grid span { background: #fff; padding: 4px 10px; border-radius: 4px; font-size: 11px; border: 1px solid #e2e8f0; }
.cv-header h1 { margin: 0; font-size: 32px; color: #1a202c; }
.cv-header h3 { margin: 5px 0 30px; font-size: 18px; color: var(--primary-color); font-weight: 500; }
.cv-title-border { color: var(--primary-color); text-transform: uppercase; font-size: 16px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 20px; }
.cv-section-right p { font-size: 14px; line-height: 1.6; color: #4a5568; }
.cv-item h5 { margin: 0 0 10px; font-size: 15px; color: #2d3748; }
.cv-export-controls { text-align: center; margin: 30px 0; }
</style>

<script>
document.getElementById('download-cv-pdf').addEventListener('click', function() {
	const element = document.getElementById('cv-content-to-export');
	const opt = {
		margin: 0,
		filename: '<?php echo $user->user_login; ?>-cv.pdf',
		image: { type: 'jpeg', quality: 0.98 },
		html2canvas: { scale: 2, useCORS: true },
		jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
	};
	html2pdf().set(opt).from(element).save();
});
</script>
