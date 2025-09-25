<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$application_status = get_post_meta( $post->ID, 'application_status', true ) ?: '';
?>
<div class="application-status-section">
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field('job_application_status_action', 'job_application_status_nonce'); ?>
        <input type="hidden" name="action" value="jobus_update_application_status" />
        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post->ID ); ?>" />
            <option value="pending" <?php selected($application_status, 'pending'); ?>><?php esc_html_e('Pending', 'jobus'); ?></option>
            <option value="approved" <?php selected($application_status, 'approved'); ?>><?php esc_html_e('Approve', 'jobus'); ?></option>
            <option value="rejected" <?php selected($application_status, 'rejected'); ?>><?php esc_html_e('Rejected', 'jobus'); ?></option>
        </select>
        <p>
            <button type="submit" name="save_application_status" class="button button-primary" style="margin-top: 10px; width: 100%;">
                <?php esc_html_e('Update Status', 'jobus'); ?>
            <button type="submit" class="button button-primary" style="margin-top: 10px; width: 100%;">
        </p>
    </form>
</div>
