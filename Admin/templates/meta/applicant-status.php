<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$application_status = get_post_meta( $post->ID, 'application_status', true ) ?: '';
?>
<div class="application-status-section">
    <form method="post">
        <select name="application_status" id="application_status" class="widefat">
            <option value="" <?php selected($application_status, ''); ?>><?php esc_html_e('Default', 'jobus'); ?></option>
            <option value="pending" <?php selected($application_status, 'pending'); ?>><?php esc_html_e('Pending', 'jobus'); ?></option>
            <option value="approved" <?php selected($application_status, 'approved'); ?>><?php esc_html_e('Approve', 'jobus'); ?></option>
            <option value="rejected" <?php selected($application_status, 'rejected'); ?>><?php esc_html_e('Rejected', 'jobus'); ?></option>
        </select>
        <p>
            <button type="submit" name="save_application_status" class="button button-primary" style="margin-top: 10px; width: 100%;">
                <?php esc_html_e('Update Status', 'jobus'); ?>
            </button>
        </p>
    </form>
</div>
