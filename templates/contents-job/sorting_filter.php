<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$job_archive_layout = $jobus_job_archive_layout ?? jobus_opt('job_archive_layout');

// Check if the view parameter is set in the URL
$current_view = !empty($_GET['view']) ? sanitize_text_field($_GET['view']) : 'list';

// Get the base URL for the archive page
if ( $job_archive_layout ) {
    $archive_url = get_post_type_archive_link('jobus_job');
} else {
    $archive_url = get_the_permalink(); //Created a Page link
}

// Build the URL for list and grid views
$list_view_url = esc_url(add_query_arg('view', 'list', $archive_url));
$grid_view_url = esc_url(add_query_arg('view', 'grid', $archive_url));

$wrap_class = match ($job_archive_layout) {
    '1' => 'mb-20',
    '2' => 'mb-25 mt-70 lg-mt-40',
    default => 'mb-20',
};

?>
<div class="upper-filter d-flex justify-content-between align-items-center <?php echo esc_attr($wrap_class) ?>">
    <div class="total-job-found">
        <?php esc_html_e('All', 'jobus'); ?>
        <span class="text-dark"><?php echo esc_html(jobus_posts_count('jobus_job')) ?></span>
        <?php
        /* translators: 1: job found, 2: jobs found */
        echo esc_html(sprintf(_n('job found', 'jobs found', jobus_posts_count('jobus_job'), 'jobus'), jobus_posts_count('jobus_job') ));
        ?>
    </div>
    <div class="d-flex align-items-center">
        <div class="short-filter d-flex align-items-center">
            <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobus'); ?></div>
            <?php
            $order = !empty($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
            $order_by = !empty($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
            $default = ! empty( $order_by ) ? 'selected' : '';

            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
            ?>
            <form action="" method="get">
                <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                    <option <?php echo esc_attr($default); ?>><?php esc_html_e('Default', 'jobus'); ?></option>
                    <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old) ?>><?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?></option>
                    <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?></option>
                    <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobus' ); ?></option>
                    <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobus' ); ?></option>
                </select>
            </form>
        </div>

        <a href="<?php echo esc_url($list_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn<?php echo esc_attr($current_view == 'grid') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active List', 'jobus'); ?>"><i class="bi bi-list"></i></a>
        <a href="<?php echo esc_url($grid_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn<?php echo esc_attr($current_view == 'list') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active Grid', 'jobus'); ?>"><i class="bi bi-grid"></i></a>
    </div>
</div>
