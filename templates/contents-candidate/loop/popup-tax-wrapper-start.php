<?php
/**
 * Popup Tax Wrapper Start
 *
 * This template part is used to display the start of a popup taxonomy wrapper for candidates.
 *
 * @package jobus
 */

if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

// Determine the dynamic text based on the taxonomy
$taxonomy_text = '';
if ( $taxonomy === 'jobus_candidate_cat' ) {
	$taxonomy_text = esc_html__( 'Categories', 'jobus' );
}
if ( $taxonomy === 'jobus_candidate_location' ) {
	$taxonomy_text = esc_html__( 'Locations', 'jobus' );
}
?>
<div class="col-lg-3 col-sm-6">
    <div class="filter-block pb-50 md-pb-20">
        <div class="filter-title fw-500 text-dark"><?php echo esc_html($taxonomy_text) ?></div>
