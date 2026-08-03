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
        <select name="application_status" style="width:100%;margin-bottom:10px;">
            <?php foreach ( jobus_get_application_statuses() as $status_key => $status_meta ) : ?>
                <option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $application_status, $status_key ); ?>>
                    <?php echo esc_html( $status_meta['label'] ); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="save_application_status" class="button button-primary">
            <?php esc_html_e('Update Status', 'jobus'); ?>
        </button>
    </form>
</div>
