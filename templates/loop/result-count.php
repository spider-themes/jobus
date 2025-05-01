<?php
/**
 * Result Count - Show items result count
 *
 * This template can be overridden by copying it to yourtheme/jobus/loop/result-count.php
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
switch ( $post_type ) {
    case 'jobus_job':
        $message = _n( 'job found', 'jobs found', $found_posts, 'jobus' );
        break;
    case 'jobus_candidate':
        $message = _n( 'candidate found', 'candidates found', $found_posts, 'jobus' );
        break;
    case 'jobus_company':
        $message = _n( 'company found', 'companies found', $found_posts, 'jobus' );
        break;
    default:
        $message = _n( 'item found', 'items found', $found_posts, 'jobus' );
        break;
}
?>
<div class="total-job-found">
	<?php esc_html_e( 'All', 'jobus' ); ?>
    <span class="fw-500"><?php echo esc_html( $found_posts ); ?></span>
	<?php echo esc_html( $message ); ?>
</div>