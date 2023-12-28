<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $job_post;
?>
<div class="upper-filter d-flex justify-content-between align-items-center mb-20">
    <div class="total-job-found">
        <?php esc_html_e('All', 'jobly'); ?>
        <span class="text-dark"><?php echo number_format_i18n($job_post->post_count); ?></span>
        <?php printf(_n('job found', 'jobs found', $job_post->post_count, 'jobly'), number_format_i18n($job_post->post_count)); ?>
    </div>

    <div class="d-flex align-items-center">
        <div class="short-filter d-flex align-items-center">
            <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
            <?php
            $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';
            $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';

            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
            ?>
            <form action="" method="get">
                <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                    <option value=""><?php esc_html_e( 'Default', 'jobly' ); ?></option>
                    <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old) ?>><?php esc_html_e( 'Newest to Oldest', 'jobly' ); ?></option>
                    <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                    <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                    <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
                </select>
            </form>
        </div>

        <button class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn" title="Active List"><i class="bi bi-list"></i></button>
        <button class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn active" title="Active Grid"><i class="bi bi-grid"></i></button>
    </div>
</div>
