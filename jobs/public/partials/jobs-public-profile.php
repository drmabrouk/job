<?php
/**
 * Public Profile Template for Job Seekers
 */
$username = get_query_var( 'job_seeker_profile' );
$user = get_user_by( 'slug', $username );

if ( ! $user || ! in_array( 'job_seeker', (array) $user->roles ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	get_template_part( 404 );
	exit;
}

$is_public = get_user_meta( $user->ID, '_jobs_profile_public', true ) === 'yes';
$is_indexed = get_user_meta( $user->ID, '_jobs_profile_indexed', true ) === 'yes';

if ( ! $is_public && get_current_user_id() !== $user->ID ) {
	wp_die( __( 'This profile is private.', 'jobs' ) );
}

// Track profile view
if ( get_current_user_id() !== $user->ID ) {
	$views = get_user_meta( $user->ID, '_jobs_profile_views', true ) ?: array();
	$views[] = time();
	update_user_meta( $user->ID, '_jobs_profile_views', array_slice($views, -100) );
}

get_header();
?>
<div class="jobs-container">
	<div class="jobs-public-profile">

		<div class="profile-header" style="text-align: center; margin-bottom: 40px;">
			<?php echo get_avatar( $user->ID, 128, '', '', array( 'class' => 'profile-avatar' ) ); ?>
			<h1 style="color: #1d3469; margin-top: 20px;"><?php echo esc_html( $user->display_name ); ?></h1>
			<p class="role-badge" style="display: inline-block; background: #1d3469; color: #fff; padding: 5px 15px; border-radius: 20px; font-size: 14px;">
				<?php _e( 'Job Seeker', 'jobs' ); ?>
			</p>
		</div>

		<div class="profile-content" style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
			<h3><?php _e( 'About Me', 'jobs' ); ?></h3>
			<p><?php echo wpautop( esc_html( get_user_meta( $user->ID, 'description', true ) ?: __( 'No description provided.', 'jobs' ) ) ); ?></p>

			<hr style="margin: 30px 0;">

			<h3><?php _e( 'Contact Information', 'jobs' ); ?></h3>
			<?php if ( is_user_logged_in() ) : ?>
				<p><strong><?php _e( 'Email:', 'jobs' ); ?></strong> <?php echo esc_html( $user->user_email ); ?></p>
			<?php else : ?>
				<p><em><?php _e( 'Please login to view contact details.', 'jobs' ); ?></em></p>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
?>
