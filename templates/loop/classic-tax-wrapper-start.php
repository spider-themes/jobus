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

if ( isset( $taxonomy ) && ! empty( $taxonomy ) ) {
	$job_taxonomy = ! empty( $_GET[ $taxonomy ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET[ $taxonomy ] ) ) : [];
}

// Initialize variables with default values
$is_collapsed_show = 'collapse';
$area_expanded     = 'false';
$is_collapsed      = ' collapsed';

if ( ! empty( $job_taxonomy ) ) {
	$is_collapsed_show = 'collapse show';
	$area_expanded     = 'true';
	$is_collapsed      = '';
}

// Determine the dynamic text based on the taxonomy
$taxonomy_text = '';
if ( $taxonomy === 'jobus_job_cat' ) {
	$taxonomy_text = esc_html__( 'Categories', 'jobus' );
}
if ( $taxonomy === 'jobus_job_location' ) {
	$taxonomy_text = esc_html__( 'Locations', 'jobus' );
}
if ( $taxonomy === 'jobus_job_tag' ) {
	$taxonomy_text = esc_html__( 'Tags', 'jobus' );
}
?>

<div class="filter-block bottom-line pb-25">
    <a class="filter-title fw-500 text-dark<?php echo esc_attr( $is_collapsed ); ?>" data-bs-toggle="collapse"
       href="#collapse-<?php echo esc_attr( $taxonomy ) ?>" role="button" aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
		<?php echo esc_html( $taxonomy_text ); ?>
    </a>
    <div class="<?php echo esc_attr( $is_collapsed_show ); ?>" id="collapse-<?php echo esc_attr( $taxonomy ); ?>">
        <div class="main-body">