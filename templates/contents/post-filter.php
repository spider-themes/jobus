<?php
$orderby    = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
?>
<div class="upper-filter d-flex justify-content-between align-items-center mb-20">
    <div class="total-job-found">
        <?php esc_html_e('All', 'jobly'); ?>
        <span class="text-dark"><?php echo esc_html(jobly_job_post_count()) ?></span>
        <?php esc_html_e('jobs found', 'jobly'); ?>
    </div>
    <div class="d-flex align-items-center">

        <div class="short-filter d-flex align-items-center">
            <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
            <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                <option value="" selected><?php esc_html_e( 'Default', 'jobly' ); ?></option>
                <option value="orderby=date&order=desc"><?php esc_html_e('Newest to Oldest', 'jobly'); ?></option>
                <option value="orderby=date&order=asc"><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                <option value="orderby=title&order=asc"><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                <option value="orderby=title&order=desc"><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
            </select>
        </div>

        <button class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn" title="Active List"><i class="bi bi-list"></i></button>
        <button class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn active" title="Active Grid"><i class="bi bi-grid"></i></button>

        <a href="?view=grid" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn <?php //echo esc_attr($listing_view == 'grid' ? 'active' : '') ?>" title="Active List"><i class="bi bi-list"></i></a>
        <a href="?view=list" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn <?php //echo esc_attr($listing_view == 'list' ? 'active' : '') ?>" title="Active Grid"><i class="bi bi-grid"></i></a>

        <?php

        /*if ( $listing_view == 'list' ) {
            //require_once JOBLY_PATH . '/templates/contents/archive-template-2.php';
        }*/

        ?>

    </div>
</div>
