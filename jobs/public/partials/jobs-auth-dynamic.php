<?php
/**
 * Smart Dynamic Authentication Page (Login/Register)
 * Separated Forms, First/Last Name, Transparent, Centered
 */
$action = isset($_GET['auth_action']) ? sanitize_text_field($_GET['auth_action']) : 'login';
?>

<div class="jobs-auth-container-fullscreen" id="jobs-auth-app">
	<div class="auth-box-transparent">
		<div class="auth-header">
			<?php if ( $logo_id = get_option('jobs_logo_id') ) : ?>
				<img src="<?php echo wp_get_attachment_url($logo_id); ?>" class="auth-logo" alt="Logo">
			<?php else : ?>
				<h1 class="brand-text">Jobedia</h1>
			<?php endif; ?>
			<h2 id="auth-title"><?php echo ($action === 'register') ? __( 'Create an Account', 'jobs' ) : __( 'Login to Your Account', 'jobs' ); ?></h2>
			<p id="auth-subtitle"><?php echo ($action === 'register') ? __( 'Join 100,000+ professionals today.', 'jobs' ) : __( 'Welcome back! Please enter your details.', 'jobs' ); ?></p>
		</div>

		<div id="auth-notices"></div>

		<!-- Login Form -->
		<form id="jobs-login-form" class="auth-form <?php echo ($action === 'login') ? 'active' : ''; ?>" style="<?php echo ($action === 'login') ? '' : 'display:none;'; ?>">
			<?php wp_nonce_field( 'jobs_auth_nonce', 'auth_nonce' ); ?>
			<input type="hidden" name="redirect_to" value="<?php echo esc_url( isset($_GET['redirect_to']) ? $_GET['redirect_to'] : wp_get_referer() ); ?>">
			<div class="form-group">
				<label><?php _e( 'Username or Email', 'jobs' ); ?></label>
				<input type="text" name="user_login" required placeholder="name@company.com">
			</div>
			<div class="form-group">
				<label><?php _e( 'Password', 'jobs' ); ?></label>
				<input type="password" name="user_pass" required placeholder="••••••••">
			</div>
			<div class="form-footer-actions">
				<label class="remember-me">
					<input type="checkbox" name="rememberme"> <?php _e( 'Remember me', 'jobs' ); ?>
				</label>
				<a href="<?php echo wp_lostpassword_url(); ?>" class="forgot-password"><?php _e( 'Forgot password?', 'jobs' ); ?></a>
			</div>
			<button type="submit" class="jobs-button btn-block btn-lg"><?php _e( 'Sign In', 'jobs' ); ?></button>
			<p class="switch-auth">
				<?php _e( "Don't have an account?", 'jobs' ); ?> <a href="#" id="show-register"><?php _e( 'Sign up for free', 'jobs' ); ?></a>
			</p>
		</form>

		<!-- Registration Form -->
		<form id="jobs-register-form" class="auth-form <?php echo ($action === 'register') ? 'active' : ''; ?>" style="<?php echo ($action === 'register') ? '' : 'display:none;'; ?>">
			<?php wp_nonce_field( 'jobs_auth_nonce', 'auth_nonce' ); ?>
			<div class="form-row" style="display: flex; gap: 15px;">
				<div class="form-group" style="flex: 1;">
					<label><?php _e( 'First Name', 'jobs' ); ?></label>
					<input type="text" name="first_name" required placeholder="John">
				</div>
				<div class="form-group" style="flex: 1;">
					<label><?php _e( 'Last Name', 'jobs' ); ?></label>
					<input type="text" name="last_name" required placeholder="Doe">
				</div>
			</div>
			<div class="form-group" id="prof-title-group">
				<label><?php _e( 'Professional Title', 'jobs' ); ?></label>
				<input type="text" name="professional_title" placeholder="<?php _e('e.g. Senior Software Engineer', 'jobs'); ?>">
			</div>
			<div class="form-group">
				<label><?php _e( 'Email Address', 'jobs' ); ?></label>
				<input type="email" name="user_email" required placeholder="john@example.com">
			</div>
			<div class="form-group">
				<label><?php _e( 'Password', 'jobs' ); ?></label>
				<input type="password" name="user_pass" required placeholder="At least 8 characters">
			</div>
			<div class="form-group">
				<label><?php _e( 'I am a...', 'jobs' ); ?></label>
				<select name="user_role" class="jobs-filter-select">
					<option value="job_seeker"><?php _e( 'Job Seeker', 'jobs' ); ?></option>
					<option value="employer"><?php _e( 'Employer', 'jobs' ); ?></option>
				</select>
			</div>
			<button type="submit" class="jobs-button btn-block btn-lg"><?php _e( 'Create Account', 'jobs' ); ?></button>
			<p class="switch-auth">
				<?php _e( 'Already have an account?', 'jobs' ); ?> <a href="#" id="show-login"><?php _e( 'Log in', 'jobs' ); ?></a>
			</p>
		</form>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	const $loginForm = $('#jobs-login-form');
	const $registerForm = $('#jobs-register-form');
	const $title = $('#auth-title');
	const $subtitle = $('#auth-subtitle');
	const $notices = $('#auth-notices');

	$('#show-register').on('click', function(e) {
		e.preventDefault();
		$loginForm.hide().removeClass('active');
		$registerForm.fadeIn().addClass('active');
		$title.text("<?php echo esc_js(__( 'Create an Account', 'jobs' )); ?>");
		$subtitle.text("<?php echo esc_js(__( 'Join 100,000+ professionals today.', 'jobs' )); ?>");
	});

	$('#show-login').on('click', function(e) {
		e.preventDefault();
		$registerForm.hide().removeClass('active');
		$loginForm.fadeIn().addClass('active');
		$title.text("<?php echo esc_js(__( 'Login to Your Account', 'jobs' )); ?>");
		$subtitle.text("<?php echo esc_js(__( 'Welcome back! Please enter your details.', 'jobs' )); ?>");
	});

	$('select[name="user_role"]').on('change', function() {
		if ($(this).val() === 'employer') {
			$('#prof-title-group').hide();
		} else {
			$('#prof-title-group').show();
		}
	});

	// Handle AJAX Login
	$loginForm.on('submit', function(e) {
		e.preventDefault();
		$notices.empty();
		const data = $(this).serialize() + '&action=jobs_ajax_login';
		$.post(jobs_ajax.ajax_url, data, function(res) {
			if (res.success) {
				$notices.html('<div class="jobs-msg success">'+res.data.message+'</div>');
				setTimeout(() => window.location.href = res.data.redirect || '<?php echo home_url('/jobs-dashboard'); ?>', 1000);
			} else {
				$notices.html('<div class="jobs-msg error">'+res.data+'</div>');
			}
		});
	});

	// Handle AJAX Registration
	$registerForm.on('submit', function(e) {
		e.preventDefault();
		$notices.empty();
		const data = $(this).serialize() + '&action=jobs_ajax_register';
		$.post(jobs_ajax.ajax_url, data, function(res) {
			if (res.success) {
				$notices.html('<div class="jobs-msg success">'+res.data+'</div>');
				// Redirect to onboarding instead of dashboard
				setTimeout(() => window.location.href = '<?php echo home_url('/jobs-dashboard?tab=onboarding'); ?>', 1500);
			} else {
				$notices.html('<div class="jobs-msg error">'+res.data+'</div>');
			}
		});
	});
});
</script>

<style>
.jobs-auth-container-fullscreen {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80vh; /* Professional centering */
    padding: 60px 20px;
    background: transparent !important;
}

.auth-box-transparent {
    width: 100%;
    max-width: 500px;
    background: transparent !important;
    padding: 0;
}

.auth-header {
    text-align: center;
    margin-bottom: 40px;
}

.auth-logo {
    max-height: 80px;
    margin-bottom: 30px;
}

.auth-header h2 {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.auth-header p {
    color: #718096;
    font-size: 16px;
}

.auth-form {
    background: transparent !important;
}

.auth-form label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #4a5568;
    font-size: 14px;
}

.auth-form input[type="text"],
.auth-form input[type="email"],
.auth-form input[type="password"],
.auth-form select {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #edf2f7;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    font-size: 15px;
}

.auth-form input:focus {
    border-color: var(--primary-color);
    background: #fff;
    outline: none;
    box-shadow: 0 0 0 4px rgba(29, 52, 105, 0.05);
}

.btn-block {
    width: 100%;
    padding: 16px;
    font-size: 16px;
    font-weight: 700;
    border-radius: 12px;
}

.switch-auth {
    text-align: center;
    margin-top: 25px;
    font-size: 15px;
    color: #718096;
}

.switch-auth a {
    color: var(--primary-color);
    font-weight: 700;
    text-decoration: none;
}

.form-footer-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    font-size: 14px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    margin: 0 !important;
}

.forgot-password {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}
</style>
