<?php
/**
 * Job List Four Column Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-list-4-col.php.
 *
 * This template is used to display job listings in a list format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="accordion-box list-style">
	<?php
	while ( $job_query->have_posts() ) : $job_query->the_post();
		// Use reusable job list item template per Constitution VI: Code Deduplication & Reusability
		jobus_get_template_part('loop/job-list-item', [
			'layout' => '4-col',
			'show_save_button' => true,
			'show_apply_button' => true,
			'show_date' => false,
			'show_location' => true,
			'show_category' => false,
			'show_salary' => true,
			'show_duration' => true,
			'job_id' => get_the_ID(),
			'extra_classes' => 'border-style',
			'col_classes' => [
				'title' => 'jbs-col-md-5',
				'meta' => 'jbs-col-md-4 jbs-col-sm-6 jbs-mt-sm-30',
				'actions' => 'jbs-col-md-3 jbs-col-sm-6'
			]
		]);
    endwhile;
    wp_reset_postdata();
    ?>
</div>
