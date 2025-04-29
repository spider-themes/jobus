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

// Dynamic strings text based on post-type
switch ( $post_type ) {
	case 'jobus_job':
		$singular = esc_html__( 'job found', 'jobus' );
		$plural   = esc_html__( 'jobs found', 'jobus' );
		break;
        case 'jobus_candidate':
		$singular = esc_html__( 'candidate found', 'jobus' );
		$plural   = esc_html__( 'candidates found', 'jobus' );
		break;
	case 'jobus_company':
		$singular = esc_html__( 'company found', 'jobus' );
		$plural   = esc_html__( 'companies found', 'jobus' );
		break;
	default:
		$singular = esc_html__( 'item found', 'jobus' );
		$plural   = esc_html__( 'items found', 'jobus' );
		break;
}
?>
<div class="total-job-found">
	<?php esc_html_e( 'All', 'jobus' ); ?>
    <span class="fw-500"><?php echo esc_html( $found_posts ); ?></span>
	<?php
	/* translators: %s: number of items found */
	echo esc_html( sprintf( _n( $singular, $plural, $found_posts, 'jobus' ), $found_posts ) );
	?>
</div>