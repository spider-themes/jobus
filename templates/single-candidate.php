<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
$experience = !empty($meta['experience']) ? $meta['experience'] : '';
$educations = !empty($meta['education']) ? $meta['education'] : '';
$cv_attachment = !empty($meta['cv_attachment']) ? $meta['cv_attachment'] : '';

$portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
$portfolio_ids = explode(',', $portfolio);

$skills = get_terms( array(
        'taxonomy' => 'candidate_skill'
    )
);

include 'single-candidate/candidate-single-1.php';


?>



<?php

get_footer();