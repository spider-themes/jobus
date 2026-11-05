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

if ( ! empty( $taxonomy ) ) {
	// Use shared helper so clean URLs (string) and form arrays both work.
	$taxonomy_arr = jobus_search_terms( $taxonomy );
	// Display labels use slugs as submitted; strip legacy @space@ encoding for UI state.
	$taxonomy_arr = array_map(
		static function ( $val ) {
			return str_replace( '@space@', ' ', (string) $val );
		},
		$taxonomy_arr
	);
}


// Initialize variables with default values
$is_collapsed_show = 'jbs-collapse';
$area_expanded     = 'false';
$is_collapsed      = ' jbs-collapsed';

if ( ! empty( $taxonomy_arr  ) ) {
	$is_collapsed_show = 'jbs-collapse show';
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
if ( $taxonomy == 'jobus_candidate_skill' ) {
	$taxonomy_text = esc_html__( 'Skills', 'jobus' );
}
?>

<div class="jbs-filter-block jbs-bottom-line">
    <a class="jbs-filter-title jbs-fw-500 jbs-text-dark jbs-pointer <?php echo esc_attr( $is_collapsed ); ?>" data-jbs-toggle="collapse"
       data-jbs-target="#collapse-<?php echo esc_attr( $taxonomy ) ?>" role="button" aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
		<?php echo esc_html( $taxonomy_text ); ?>
    </a>
    <div class="<?php echo esc_attr( $is_collapsed_show ); ?>" id="collapse-<?php echo esc_attr( $taxonomy ); ?>">
        <div class="jbs-main-body">