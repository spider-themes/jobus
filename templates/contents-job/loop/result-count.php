<?php
/**
 * Result Count - Show job result count
 *
 * This template can be overridden by copying it to yourtheme/jobus/loop/result-count.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus\Templates
 */

?>
<div class="total-job-found">
	<?php esc_html_e('All', 'jobus'); ?>
	<span class="fw-500"> <?php echo esc_html( $job_post->found_posts ); ?> </span>
	<?php
	/* translators: 1: job found, 2: jobs found */
	echo esc_html(sprintf(_n('job found', 'jobs found', $job_post->found_posts, 'jobus'), $job_post->found_posts ));
	?>
</div>
