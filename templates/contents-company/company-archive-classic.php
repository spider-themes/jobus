<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jobus-company-classic company-profiles pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-company/sidebar-classic-filters'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
	                    <?php
	                    // Display the total number of companies found
	                    include __DIR__ . '/../loop/result-count.php';

                        // Display the sort by dropdown
	                    include __DIR__ . '/../loop/sortby.php';
	                    ?>
                    </div>

                    <?php
                    if ( $current_view == 'grid' ) {
                        include 'contents/content-grid.php';
                    } elseif ( $current_view == 'list' ) {
                        include 'contents/content-list.php';
                    }

                    // Pagination
                    include __DIR__ . '/../loop/pagination.php';
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>