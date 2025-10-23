<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jobus-company-classic company-profiles pt-90 jbs-lg-pt-70 jbs-pb-150 jbs-xl-pb-150 jbs-lg-pb-80 ">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="wrapper">

                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-md-center jbs-mb-25">

                        <div class="jbs-d-md-flex jbs-align-items-center">

                            <button type="button" class="jbs-filter-btn jbs-w-84 jbs-fw-500 tran3s jbs-me-3 jbs-open-modal" data-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
                                <?php esc_html_e('Filter', 'jobus'); ?>
                            </button>

                            <?php
                            // Display the total number of companies found
                            include __DIR__ . '/../loop/result-count.php';
                            ?>

                        </div>

	                    <?php
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