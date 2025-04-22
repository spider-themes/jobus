<?php
/**
 * Result Count - Show candidate result count
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/loop/result-count.php.
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
    <span class="fw-500"><?php echo esc_html( $candidate_query->found_posts ); ?></span>
	<?php
	/* translators: 1: candidate found, 2: candidates found */
	echo esc_html( sprintf( _n( 'candidate found', 'candidates found', $candidate_query->found_posts, 'jobus' ), $candidate_query->found_posts ) );
	?>
</div>
