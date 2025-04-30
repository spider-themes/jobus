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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$found_posts = $result_count->found_posts;
$formatted_count = number_format_i18n( $found_posts );

// Dynamic strings text based on post-type
$message = match ( $post_type ) {
	'jobus_job' => (
		// translators: %s: number of jobs found
	    sprintf( _n( '%s job found', '%s jobs found', $found_posts, 'jobus' ), $formatted_count )
	),
	'jobus_candidate' => (
		// translators: %s: number of candidates found
	    sprintf( _n( '%s candidate found', '%s candidates found', $found_posts, 'jobus' ), $formatted_count )
	),
	'jobus_company' => (
		// translators: %s: number of companies found
	    sprintf( _n( '%s company found', '%s companies found', $found_posts, 'jobus' ), $formatted_count )
	),
	default => (
		// translators: %s: number of items found
	    sprintf( _n( '%s item found', '%s items found', $found_posts, 'jobus' ), $formatted_count )
	),
};
?>
<div class="total-job-found">
	<?php esc_html_e( 'All', 'jobus' ); ?>
    <span class="fw-500"><?php echo esc_html( $found_posts ); ?></span>
	<?php echo esc_html( $message ); ?>
</div>