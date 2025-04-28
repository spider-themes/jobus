<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="company-profiles bg-color pt-90 lg-pt-70 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="wrapper">

                    <div class="upper-filter d-flex justify-content-between align-items-start align-items-md-center mb-25">

                        <div class="d-md-flex align-items-center">

                            <button type="button" class="filter-btn fw-500 tran3s me-3" data-bs-toggle="modal" data-bs-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
                                <?php esc_html_e('Filter', 'jobus'); ?>
                            </button>

                            <?php
                            // Display the total number of companies found
                            $result_count = $company_query;
                            include __DIR__ . '/../loop/result-count.php';
                            ?>

                        </div>

	                    <?php
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
                    $pagination = $company_query;
                    include __DIR__ . '/../loop/pagination.php';
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>