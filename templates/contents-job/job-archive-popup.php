<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="jobus-job-popup job-listing-three bg-color pt-90 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="job-post-item-wrapper">

                    <div class="upper-filter d-flex justify-content-between align-items-start align-items-sm-center mb-30">
                        <div class="d-sm-flex align-items-center">

                            <button type="button" class="filter-btn fw-500 tran3s me-3" data-bs-toggle="modal" data-bs-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
								<?php esc_html_e( 'Filter', 'jobus' ); ?>
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
					// Check if the current view is set to 'list' or 'grid' contents
					if ( $current_view == 'list' ) {
						include( 'contents/content-grid-details.php' );
					} elseif ( $current_view == 'grid' ) {
						include( 'contents/content-grid.php' );
					}

					// Pagination
					include (__DIR__ . '/../loop/pagination.php');
					?>
                </div>
            </div>
        </div>
    </div>
</section>