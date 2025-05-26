<?php
/**
 * The template for displaying the sidebar menu in the candidate dashboard
 *
 * @package jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$logo = jobus_opt( 'dashboard_logo' );
?>
<aside class="dash-aside-navbar">
    <div class="position-relative">

		<?php if ( ! empty( $logo['url'] ) ) : ?>
            <div class="logo text-md-center d-md-block d-flex align-items-center justify-content-between">
                <a href="<?php esc_url( home_url( '/' ) ); ?>">
                    <img src="<?php echo esc_url( $logo['url'] ) ?>" alt="<?php get_bloginfo( 'name' ) ?>">
                </a>
                <button class="close-btn d-block d-md-none"><i class="bi bi-x-lg"></i></button>
            </div>
		<?php endif; ?>

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
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-profile.html">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_23.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Profile</span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-settings.html">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_24.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Account Settings</span>
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_25.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Notification</span>
                        </a>
                    </li>

                </ul>

            </div>
        </div>

		<?php
		if ( has_nav_menu( 'jobus_candidate_menu' ) ) {
			wp_nav_menu( [
				'menu'            => 'jobus_candidate_menu',
				'theme_location'  => 'jobus_candidate_menu',
				'container'       => 'nav',
				'container_class' => 'dasboard-main-nav',
				'menu_class'      => 'style-none',
				'fallback_cb'     => false,
				'depth'           => 1,
				'walker'          => new \jobus\includes\Classes\Nav_Walker(),
			]);
		}
		?>

        <!-- /.dasboard-main-nav -->
        <div class="profile-complete-status">
            <div class="progress-value fw-500">87%</div>
            <div class="progress-line position-relative">
                <div class="inner-line" style="width:80%;"></div>
            </div>
            <p> <?php esc_html_e( 'Profile Complete', 'jobus' ); ?> </p>
        </div>
        <!-- /.profile-complete-status -->

        <a href="#" class="d-flex w-100 align-items-center logout-btn">
            <img src="../images/lazy.svg" data-src="images/icon/icon_9.svg" alt="" class="lazy-img">
            <span>Logout</span>
        </a>
    </div>
</aside>
