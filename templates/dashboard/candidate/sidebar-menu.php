<?php
/**
 * The template for displaying the sidebar menu in the candidate dashboard
 *
 * @package jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get dashboard URL
$dashboard_url = get_permalink();

// Use menu_items passed from Dashboard class
$menu_items = $args['menu_items'] ?? [];

// Determine active endpoint from $args if available, otherwise default to dashboard
$active_endpoint = $args['active_endpoint'] ?? 'dashboard';

// Get candidate profile URL
$candidate = get_posts([
	'post_type' => 'jobus_candidate',
	'author' => get_current_user_id(),
	'numberposts' => 1,
]);

$profile_url = !empty($candidate) ? get_permalink($candidate[0]) : '';
?>
<div class="position-relative">

    <?php include __DIR__ . '/../template-part/sidebar-logo.php'; ?>

    <?php include __DIR__ . '/../template-part/sidebar-user-data.php'; ?>

    <nav class="main-nav">
        <ul class="style-none">
			<?php
			foreach ( $menu_items as $endpoint => $item ) :
				$url = trailingslashit( $dashboard_url ) . $endpoint . '/';
				$is_active = $active_endpoint === $endpoint;
				$align_class = 'd-flex w-100 align-items-center dashboard-navigation-link dashboard-navigation-link--' . $endpoint;
				if ( $is_active ) {
					$align_class .= ' active';
				}
				$aria_current = $is_active ? ' aria-current="page"' : '';
				?>
                <li>
                    <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr($align_class) ?>"<?php echo esc_attr($aria_current); ?>>
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
        <img src="<?php echo esc_url(JOBUS_IMG . '/dashboard/icons/logout.svg') ?>" alt="<?php esc_attr_e('Logout', 'jobus'); ?>" class="lazy-img">
        <span><?php esc_html_e('Logout', 'jobus'); ?></span>
    </a>
</div>