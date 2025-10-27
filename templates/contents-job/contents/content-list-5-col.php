<?php
/**
 * Job List Five Column Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-list-5-col.php.
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
			'layout' => '5-col',
			'show_save_button' => true,
			'show_apply_button' => true,
			'show_date' => false,
			'show_location' => true,
			'show_category' => true,
			'show_salary' => true,
			'show_duration' => true,
			'job_id' => get_the_ID(),
			'extra_classes' => 'border-style',
			'col_classes' => [
				'title' => 'jbs-col-xl-4 jbs-col-lg-4',
				'meta' => 'jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6',
				'actions' => 'jbs-col-lg-2 jbs-col-md-4'
			]
		]);
	endwhile;
	wp_reset_postdata();
	?>
</div>
