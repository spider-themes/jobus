<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="jbs jobus-job-popup job-listing-three jbs-pt-90 lg-pt-80jbs-pb-150 xl-pb-150 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="job-post-item-wrapper">

                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-sm-centerjbs-mb-30">
                        <div class="jbs-d-sm-flex jbs-align-items-center">

                            <button type="button" class="jbs-filter-btn jbs-w-84 jbs-fw-500 tran3s jbs-me-3 jbs-open-modal" data-target="#filterPopUp">
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