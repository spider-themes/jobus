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
$company = get_posts( [
    'post_type'   => 'jobus_company',
    'author'      => get_current_user_id(),
    'numberposts' => 1,
] );

$profile_url = ! empty( $company ) ? get_permalink( $company[0] ) : '';

echo '<pre>';
print_r( $profile_url );
echo '</pre>';

?>
<div class="position-relative">

    <?php include __DIR__ . '/../template-part/sidebar-logo.php'; ?>

    <?php include __DIR__ . '/../template-part/sidebar-user-data.php'; ?>

    <?php include __DIR__ . '/../template-part/sidebar-menu-item.php'; ?>

</div>