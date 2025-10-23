<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jbs jobus-job-topbar job-listing-three jbs-pt-110 lg-pt-80jbs-pb-150 xl-pb-150 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">

            <?php jobus_get_template_part('contents-job/sidebar-topbar-filters'); ?>

            <div class="jbs-col-12">
                <div class="job-post-item-wrapper">
                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-mb-25 jbs-mt-70 jbs-lg-mt-40">

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
	                    include ( 'contents/content-list-5-col.php' );
                    } elseif ( $current_view == 'grid' ) {
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