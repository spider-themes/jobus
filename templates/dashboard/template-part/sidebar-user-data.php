<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$user = wp_get_current_user();

?>
<div class="user-data">

	<div class="user-avatar online position-relative rounded-circle">
		<?php echo get_avatar( $user->user_email, 75, '', $user->display_name, [ 'class' => 'lazy-img' ] ); ?>
	</div>

	<!-- /.user-avatar -->
	<div class="user-name-data">
		<button class="user-name dropdown-toggle" type="button" id="profile-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside"
		        aria-expanded="false">
			<?php echo esc_html( $user->display_name ); ?>
		</button>

		<ul class="dropdown-menu" aria-labelledby="profile-dropdown">
			<li>
				<a href="<?php echo esc_url($profile_url); ?>" class="dropdown-item d-flex align-items-center">
					<img src="<?php echo esc_url(JOBUS_IMG . '/dashboard/icons/profile.svg') ?>" alt="<?php esc_attr_e( 'Candidate Profile', 'jobus' ); ?>" class="lazy-img">
					<span class="ms-2 ps-1"><?php esc_html_e( 'View Profile', 'jobus' ); ?></span>
				</a>
			</li>
		</ul>
	</div>
</div>
