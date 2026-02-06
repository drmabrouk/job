<?php
/**
 * Multi-Step Onboarding Flow for User Profile Completion
 */
$user_id = get_current_user_id();
$user_meta = get_user_meta($user_id);
?>

<div class="jobs-onboarding-container">
	<div class="onboarding-steps-indicator">
		<div class="step active" data-step="1"><span>1</span> <?php _e( 'Basic Profile', 'jobs' ); ?></div>
		<div class="step" data-step="2"><span>2</span> <?php _e( 'Experience & Education', 'jobs' ); ?></div>
		<div class="step" data-step="3"><span>3</span> <?php _e( 'Skills & Certs', 'jobs' ); ?></div>
		<div class="step" data-step="4"><span>4</span> <?php _e( 'Documents', 'jobs' ); ?></div>
	</div>

	<div class="onboarding-card">
		<form id="jobs-onboarding-form" enctype="multipart/form-data">
			<?php wp_nonce_field( 'jobs_onboarding_nonce', 'onboarding_nonce' ); ?>

			<!-- Step 1: Basic Profile -->
			<div class="onboarding-step-content active" id="step-1">
				<h3><?php _e( 'Let\'s start with the basics', 'jobs' ); ?></h3>
				<div class="form-group">
					<label><?php _e( 'Professional Title', 'jobs' ); ?></label>
					<input type="text" name="job_title" value="<?php echo esc_attr($user_meta['_job_title'][0] ?? ''); ?>" placeholder="e.g. Senior Product Designer">
				</div>
				<div class="form-group">
					<label><?php _e( 'Brief Bio', 'jobs' ); ?></label>
					<textarea name="description" rows="4"><?php echo esc_textarea(get_userdata($user_id)->description); ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e( 'Phone Number', 'jobs' ); ?></label>
					<input type="text" name="phone" value="<?php echo esc_attr($user_meta['_job_phone'][0] ?? ''); ?>">
				</div>
				<div class="form-group">
					<label><?php _e( 'Location', 'jobs' ); ?></label>
					<input type="text" name="location" value="<?php echo esc_attr($user_meta['_job_location'][0] ?? ''); ?>" placeholder="City, Country">
				</div>
			</div>

			<!-- Step 2: Experience & Education -->
			<div class="onboarding-step-content" id="step-2" style="display:none;">
				<h3><?php _e( 'Your Background', 'jobs' ); ?></h3>
				<div class="form-group">
					<label><?php _e( 'Professional Experience', 'jobs' ); ?></label>
					<textarea name="experience" rows="6" placeholder="<?php _e( 'Describe your work history...', 'jobs' ); ?>"><?php echo esc_textarea($user_meta['_job_experience'][0] ?? ''); ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e( 'Education', 'jobs' ); ?></label>
					<textarea name="education" rows="4" placeholder="<?php _e( 'Your degrees and schools...', 'jobs' ); ?>"><?php echo esc_textarea($user_meta['_job_education'][0] ?? ''); ?></textarea>
				</div>
			</div>

			<!-- Step 3: Skills & Certs -->
			<div class="onboarding-step-content" id="step-3" style="display:none;">
				<h3><?php _e( 'Skills & Certifications', 'jobs' ); ?></h3>
				<div class="form-group">
					<label><?php _e( 'Skills (comma separated)', 'jobs' ); ?></label>
					<input type="text" name="skills" value="<?php echo esc_attr($user_meta['_job_skills'][0] ?? ''); ?>" placeholder="PHP, JavaScript, UX Research...">
				</div>
				<div class="form-group">
					<label><?php _e( 'Training', 'jobs' ); ?></label>
					<textarea name="training" rows="3"><?php echo esc_textarea($user_meta['_job_training'][0] ?? ''); ?></textarea>
				</div>
				<div class="form-group">
					<label><?php _e( 'Certifications', 'jobs' ); ?></label>
					<textarea name="certifications" rows="3"><?php echo esc_textarea($user_meta['_job_certifications'][0] ?? ''); ?></textarea>
				</div>
			</div>

			<!-- Step 4: Documents -->
			<div class="onboarding-step-content" id="step-4" style="display:none;">
				<h3><?php _e( 'Upload Your Resume', 'jobs' ); ?></h3>
				<p><?php _e( 'A professional resume is key to getting hired.', 'jobs' ); ?></p>
				<div class="form-group">
					<div class="file-upload-zone" id="onboarding-resume-upload">
						<i class="fas fa-cloud-upload-alt"></i>
						<span><?php _e( 'Click or drag file to upload (PDF, DOCX)', 'jobs' ); ?></span>
						<input type="file" name="resume_file" accept=".pdf,.doc,.docx" style="display:none;">
					</div>
					<div id="resume-filename" style="margin-top:10px; font-weight:600; color:var(--primary-color);"></div>
				</div>
			</div>

			<div class="onboarding-footer">
				<button type="button" class="jobs-button btn-outline" id="prev-step" style="display:none;"><?php _e( 'Back', 'jobs' ); ?></button>
				<button type="button" class="jobs-button" id="next-step"><?php _e( 'Next Step', 'jobs' ); ?></button>
				<button type="submit" class="jobs-button" id="finish-onboarding" style="display:none;"><?php _e( 'Complete Profile', 'jobs' ); ?></button>
			</div>
		</form>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	let currentStep = 1;
	const totalSteps = 4;

	$('#next-step').on('click', function() {
		if (currentStep < totalSteps) {
			$(`#step-${currentStep}`).hide();
			$(`.step[data-step="${currentStep}"]`).removeClass('active').addClass('completed');
			currentStep++;
			$(`#step-${currentStep}`).fadeIn();
			$(`.step[data-step="${currentStep}"]`).addClass('active');

			updateButtons();
		}
	});

	$('#prev-step').on('click', function() {
		if (currentStep > 1) {
			$(`#step-${currentStep}`).hide();
			$(`.step[data-step="${currentStep}"]`).removeClass('active');
			currentStep--;
			$(`#step-${currentStep}`).fadeIn();
			$(`.step[data-step="${currentStep}"]`).addClass('active').removeClass('completed');

			updateButtons();
		}
	});

	function updateButtons() {
		if (currentStep === 1) {
			$('#prev-step').hide();
		} else {
			$('#prev-step').show();
		}

		if (currentStep === totalSteps) {
			$('#next-step').hide();
			$('#finish-onboarding').show();
		} else {
			$('#next-step').show();
			$('#finish-onboarding').hide();
		}
	}

	$('#onboarding-resume-upload').on('click', function() {
		$(this).find('input').click();
	});

	$('input[name="resume_file"]').on('change', function() {
		if (this.files && this.files[0]) {
			$('#resume-filename').text(this.files[0].name);
		}
	});

	$('#jobs-onboarding-form').on('submit', function(e) {
		e.preventDefault();
		const formData = new FormData(this);
		formData.append('action', 'jobs_save_onboarding');

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(res) {
				if (res.success) {
					window.location.href = '<?php echo home_url('/jobs-dashboard'); ?>';
				} else {
					alert(res.data);
				}
			}
		});
	});
});
</script>

<style>
.jobs-onboarding-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 0;
}

.onboarding-steps-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 40px;
    position: relative;
}

.onboarding-steps-indicator::before {
    content: '';
    position: absolute;
    top: 15px;
    left: 0;
    right: 0;
    height: 2px;
    background: #edf2f7;
    z-index: 1;
}

.onboarding-steps-indicator .step {
    position: relative;
    z-index: 2;
    background: #fcfcfc;
    padding: 0 15px;
    font-size: 13px;
    font-weight: 700;
    color: #a0aec0;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.onboarding-steps-indicator .step span {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #edf2f7;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.onboarding-steps-indicator .step.active { color: var(--primary-color); }
.onboarding-steps-indicator .step.active span { border-color: var(--primary-color); background: var(--primary-color); color: #fff; box-shadow: 0 0 0 4px rgba(29, 52, 105, 0.1); }
.onboarding-steps-indicator .step.completed span { background: #27ae60; border-color: #27ae60; color: #fff; }

.onboarding-card {
    background: #fff;
    padding: 50px;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.03);
    border: 1px solid #f1f5f9;
}

.onboarding-step-content h3 {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 30px;
}

.onboarding-footer {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 1px solid #f1f5f9;
}

.file-upload-zone {
    border: 2px dashed #e2e8f0;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    gap: 10px;
    color: #718096;
}

.file-upload-zone:hover {
    border-color: var(--primary-color);
    background: #f8fafc;
    color: var(--primary-color);
}

.file-upload-zone i { font-size: 32px; }
</style>
