<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobly
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$args = [
    'post_type' => 'candidate',
    'post_status' => 'publish',
    'posts_per_page' => jobly_opt('candidate_posts_per_page'),
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
];

$candidate_query = new WP_Query($args);

//============= Select Layout ==================//
include 'contents-candidate/candidate-archive-1.php';

get_footer();