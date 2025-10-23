<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jbs jobus-company-classic company-profiles jbs-pt-110 lg-pt-80jbs-pb-150 xl-pb-150 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">

            <?php jobus_get_template_part('contents-company/sidebar-classic-filters'); ?>

            <div class="jbs-col-xl-9 jbs-col-lg-8">
                <div class="jbs-ms-xl-3">

                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-mb-30">
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