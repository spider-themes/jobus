<?php
$per_page = isset($args['posts_per_page']) ? intval($args['posts_per_page']) : 10; // Posts per page
$current_page = max(1, get_query_var('paged')); // Current page number

$total_jobs = wp_count_posts('job');
$total_jobs = $total_jobs->publish; // Total number of job posts is publish status

// Calculate the first and last result numbers
$first_result = ($per_page * ($current_page - 1)) + 1;
$last_result = min(($per_page * $current_page), $total_jobs);
?>
<p class="m0 order-sm-last text-center text-sm-start xs-pb-20">
    <?php printf(
            __('Showing <span class="text-dark fw-500"> %1$d-%2$d </span> of <span class="text-dark fw-500">%3$d</span> results', 'jobly'),
        $first_result, $last_result, $total_jobs
    )
    ?>
</p>