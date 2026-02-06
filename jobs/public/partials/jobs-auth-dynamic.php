<?php
/**
 * Smart Dynamic Authentication Page (Login/Register)
 */
?>

<div class="jobs-auth-container" id="jobs-auth-app">
	<div class="auth-box">
		<div class="auth-header">
			<img src="<?php echo get_option('jobs_logo_id') ? wp_get_attachment_url(get_option('jobs_logo_id')) : ''; ?>" class="auth-logo" alt="Logo">
			<h2 id="auth-title"><?php _e( 'Login to Your Account', 'jobs' ); ?></h2>
			<p id="auth-subtitle"><?php _e( 'Welcome back! Please enter your details.', 'jobs' ); ?></p>
		</div>

		<div id="auth-notices"></div>

		<!-- Login Form -->
		<form id="jobs-login-form" class="auth-form active">
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
			<button type="submit" class="jobs-button btn-block"><?php _e( 'Sign In', 'jobs' ); ?></button>
			<p class="switch-auth">
				<?php _e( "Don't have an account?", 'jobs' ); ?> <a href="#" id="show-register"><?php _e( 'Sign up for free', 'jobs' ); ?></a>
			</p>
		</form>

		<!-- Registration Form (Hidden by default) -->
		<form id="jobs-register-form" class="auth-form">
			<?php wp_nonce_field( 'jobs_auth_nonce', 'auth_nonce' ); ?>
			<div class="form-group">
				<label><?php _e( 'Full Name', 'jobs' ); ?></label>
				<input type="text" name="full_name" required placeholder="John Doe">
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
			<button type="submit" class="jobs-button btn-block"><?php _e( 'Create Account', 'jobs' ); ?></button>
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
		$loginForm.removeClass('active').hide();
		$registerForm.addClass('active').fadeIn();
		$title.text("<?php _e( 'Create an Account', 'jobs' ); ?>");
		$subtitle.text("<?php _e( 'Join 100,000+ professionals today.', 'jobs' ); ?>");
	});

	$('#show-login').on('click', function(e) {
		e.preventDefault();
		$registerForm.removeClass('active').hide();
		$loginForm.addClass('active').fadeIn();
		$title.text("<?php _e( 'Login to Your Account', 'jobs' ); ?>");
		$subtitle.text("<?php _e( 'Welcome back! Please enter your details.', 'jobs' ); ?>");
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
		const data = $(this).serialize() + '&action=jobs_ajax_register';
		$.post(jobs_ajax.ajax_url, data, function(res) {
			if (res.success) {
				$notices.html('<div class="jobs-msg success">'+res.data+'</div>');
				setTimeout(() => window.location.href = '<?php echo home_url('/jobs-dashboard'); ?>', 1500);
			} else {
				$notices.html('<div class="jobs-msg error">'+res.data+'</div>');
			}
		});
	});
});
</script>
