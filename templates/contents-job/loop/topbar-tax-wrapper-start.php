<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly.
}

// Determine the dynamic text based on the taxonomy
$taxonomy_text = '';
if ( $taxonomy === 'jobus_job_cat' ) {
    $taxonomy_text = __('Categories', 'jobus');
}
if ( $taxonomy === 'jobus_job_location' ) {
    $taxonomy_text = __('Locations', 'jobus');
}
if ( $taxonomy === 'jobus_job_tag' ) {
    $taxonomy_text = __('Tags', 'jobus');
}
?>
<div class="col-lg-3 col-sm-6">
    <div class="filter-block pb-50 lg-pb-20">
        <div class="filter-title fw-500 text-dark"><?php echo esc_html($taxonomy_text) ?></div>
