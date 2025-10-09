<?php
/**
 * Job Archive Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/job-archive-classic.php.
 *
 * This template can be used to display job listings in a grid or list format.
 * It includes a sidebar for filters, sorting options, and pagination.
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jobus-job-classic job-listing-three pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">

            <?php jobus_get_template_part('contents-job/sidebar-classic-filters'); ?>

            <div class="jbs-col-xl-9 jbs-col-lg-8">
                <div class="job-post-item-wrapper jbs-ms-xxl-5 ms-xl-3">

                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-mb-20">

                        <?php
                        // Display the total number of companies found
                        include __DIR__ . '/../loop/result-count.php';

                        // Display the sort by dropdown
                        include __DIR__ . '/../loop/sortby.php';
                        ?>

                    </div>

                    <?php
                    // Check if the current view is set to 'list' or 'grid' contents
                    if ( $current_view == 'list' ) {
                        include ( 'contents/content-list-4-col.php' );
                    }
                    elseif ( $current_view == 'grid' ) {
	                    include ( 'contents/content-grid.php' );
                    }

                    // Pagination
                    include (__DIR__ . '/../loop/pagination.php');
                    ?>

                </div>

            </div>

        </div>
    </div>
</section>