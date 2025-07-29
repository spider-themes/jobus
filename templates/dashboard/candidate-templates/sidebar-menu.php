<?php
/**
 * The template for displaying the sidebar menu in the candidate dashboard
 *
 * @package jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp;

// Get current user and base dashboard URL
$user = wp_get_current_user();
$dashboard_url = get_permalink();


// If on base dashboard URL (no endpoint), set to dashboard
if ($dashboard_url === home_url( add_query_arg( [], $wp->request ) . '/' )) {
	$active_endpoint = 'dashboard';
}

// Define endpoints and menu items
$menu_items = [
	[
		'endpoint' => 'dashboard',
		'label' => __( 'Dashboard', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_1_active.svg',
	],
	[
		'endpoint' => 'profile',
		'label' => __( 'My Profile', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_2.svg',
	],
	[
		'endpoint' => 'resume',
		'label' => __( 'Resume', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_3.svg',
	],
	[
		'endpoint' => 'applied-jobs',
		'label' => __( 'Applied Jobs', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_5.svg',
	],
	[
		'endpoint' => 'saved-jobs',
		'label' => __( 'Saved Jobs', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_6.svg',
	],
	[
		'endpoint' => 'change-password',
		'label' => __( 'Account Settings', 'jobus' ),
		'icon' => JOBUS_IMG . '/dashboard/icons/icon_7.svg',
	],
];

// Determine active endpoint from $args if available, otherwise default to dashboard
$active_endpoint = $args['active_endpoint'] ?? 'dashboard';

// Get candidate profile URL
$candidate = get_posts([
	'post_type' => 'jobus_candidate',
	'author' => get_current_user_id(),
	'numberposts' => 1,
]);

$profile_url = !empty($candidate) ? get_permalink($candidate[0]) : '';

$logo = jobus_opt( 'dashboard_logo' );
?>
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
                    <a href="<?php echo esc_url($profile_url); ?>" class="dropdown-item d-flex align-items-center">
                        <img src="<?php echo JOBUS_IMG . '/dashboard/icons/profile.svg' ?>" alt="<?php esc_attr_e( 'Candidate Profile', 'jobus' ); ?>" class="lazy-img">
                        <span class="ms-2 ps-1"><?php esc_html_e( 'View Profile', 'jobus' ); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <nav class="dashboard-main-nav">
        <ul class="style-none">
			<?php
			foreach ( $menu_items as $item ) :
				$url = trailingslashit( $dashboard_url ) . $item['endpoint'] . '/';
				$is_active = $active_endpoint === $item['endpoint'];
				$align_class = 'd-flex w-100 align-items-center dashboard-navigation-link dashboard-navigation-link--' . $item['endpoint'];
				if ( $is_active ) {
					$align_class .= ' active';
				}
				$aria_current = $is_active ? ' aria-current="page"' : '';
				?>
                <li>
                    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr($align_class) ?>"<?php echo $aria_current; ?>>
                        <img src="<?php echo esc_url( $item['icon'] ); ?>" alt="" class="lazy-img">
                        <span><?php echo esc_html( $item['label'] ); ?></span>
                    </a>
                </li>
			<?php
			endforeach;
			?>
        </ul>
    </nav>

    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="d-flex w-100 align-items-center logout-btn mt-25">
        <img src="<?php echo JOBUS_IMG . '/dashboard/icons/logout.svg' ?>" alt="<?php esc_attr_e('Logout', 'jobus'); ?>" class="lazy-img">
        <span><?php esc_html_e('Logout', 'jobus'); ?></span>
    </a>
</div>