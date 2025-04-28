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

?>
<div class="total-job-found">
    <?php esc_html_e('All', 'jobus'); ?>
    <span class="fw-500"><?php echo esc_html( $result_count->found_posts ); ?></span>
    <?php
    /* translators: 1: job found, 2: jobs found */
    echo esc_html(sprintf(_n('company found', 'companies found', $result_count->found_posts, 'jobus'), $result_count->found_posts ));
    ?>
</div>
