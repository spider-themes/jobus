<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<section class="jbs jbs-job-popup jbs-job-listing-three jbs-bg-white">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="jbs-job-post-item-wrapper" data-jbs-filter-results="true">

                    <?php
                    // Show Filter button when any sidebar filter surface is available
                    // (meta widgets, taxonomy widgets, or keyword search) — matches classic/topbar.
                    $has_filter_widgets = false;
                    $filter_widgets     = jobus_opt( 'job_sidebar_widgets' );
                    $taxonomy_widgets   = jobus_opt( 'job_taxonomy_widgets' );
                    $show_search_form   = jobus_opt( 'job_show_search_form', true );

                    if ( ! empty( $filter_widgets ) && is_array( $filter_widgets ) ) {
                        $has_filter_widgets = true;
                    }
                    if ( ! $has_filter_widgets && ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
                        foreach ( $taxonomy_widgets as $is_enabled ) {
                            if ( $is_enabled ) {
                                $has_filter_widgets = true;
                                break;
                            }
                        }
                    }
                    if ( ! $has_filter_widgets && $show_search_form ) {
                        $has_filter_widgets = true;
                    }
                    ?>
                    <div class="jbs-upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-sm-center jbs-mb-30">
                        <div class="jbs-d-sm-flex jbs-align-items-center">

                            <?php if ( $has_filter_widgets ) : ?>
                            <button type="button" class="jbs-filter-btn jbs-fw-500 jbs-tran3s jbs-me-3 jbs-open-modal jbs_filter-transparent jbs_filter_btn-padding jbs-pointer" data-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
								<?php esc_html_e( 'Filter', 'jobus' ); ?>
                            </button>
                            <?php endif; ?>

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