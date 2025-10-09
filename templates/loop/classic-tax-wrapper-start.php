<?php
/**
 * Classic Tax Wrapper Start
 * This template is used to display the start of the classic tax wrapper for job listings.
 *
 * This template can be overridden by copying it to yourtheme/jobus/loop/classic-tax-wrapper-start.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !empty($taxonomy) ) {
	// Verify nonce for taxonomy filtering
	$nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field(wp_unslash($_GET['jobus_nonce'])) : '';
	if ( !wp_verify_nonce($nonce, 'jobus_search_filter') ) {
		$taxonomy_arr = [];
	} else {
		// Sanitize and process taxonomy data
		$taxonomy_arr = !empty($_GET[$taxonomy]) ? array_map('sanitize_text_field', wp_unslash($_GET[$taxonomy])) : [];
	}
}


// Initialize variables with default values
$is_collapsed_show = 'collapse';
$area_expanded     = 'false';
$is_collapsed      = ' collapsed';

if ( ! empty( $taxonomy_arr  ) ) {
	$is_collapsed_show = 'collapse show';
	$area_expanded     = 'true';
	$is_collapsed      = '';
}

// Determine the dynamic text based on the taxonomy
$taxonomy_text = '';
if ( $taxonomy == 'jobus_job_cat' || $taxonomy == 'jobus_company_cat' || $taxonomy == 'jobus_candidate_cat' ) {
	$taxonomy_text = esc_html__( 'Categories', 'jobus' );
}
if ( $taxonomy == 'jobus_job_location' || $taxonomy == 'jobus_company_location' || $taxonomy == 'jobus_candidate_location' ) {
	$taxonomy_text = esc_html__( 'Locations', 'jobus' );
}
if ( $taxonomy == 'jobus_job_tag' ) {
	$taxonomy_text = esc_html__( 'Tags', 'jobus' );
}
?>

<div class="filter-block bottom-line jbs-pb-25">
    <a class="filter-title jbs-fw-500 jbs-text-dark<?php echo esc_attr( $is_collapsed ); ?>" data-bs-toggle="collapse"
       href="#collapse-<?php echo esc_attr( $taxonomy ) ?>" role="button" aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
		<?php echo esc_html( $taxonomy_text ); ?>
    </a>
    <div class="<?php echo esc_attr( $is_collapsed_show ); ?>" id="collapse-<?php echo esc_attr( $taxonomy ); ?>">
        <div class="main-body">