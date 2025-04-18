<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="job-listing-three pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-job/sidebar-topbar-filters'); ?>

            <div class="col-12">
                <div class="job-post-item-wrapper">
                    <div class="upper-filter d-flex justify-content-between align-items-center mb-25 mt-70 lg-mt-40">

	                    <?php include ('loop/result-count.php') ?>

	                    <?php include ('loop/sortby.php'); ?>

                    </div>

                    <?php
                    // Check if the current view is set to 'list' or 'grid' contents
                    if ( $current_view == 'list' ) {
	                    include ( 'contents/content-list-5-col.php' );
                    } elseif ( $current_view == 'grid' ) {
	                    include ( 'contents/content-grid.php' );
                    }

                    // Pagination
                    include ( 'loop/pagination.php' );

                    ?>
                </div>

            </div>

        </div>
    </div>
</section>