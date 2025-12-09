<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<section class="jobus-company-classic company-profiles jbs-pt-90 jbs-lg-pt-70 jbs-pb-150 jbs-xl-pb-150 jbs-lg-pb-80 bg-color ">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="wrapper">

                    <?php
                    // Check if there are any filter widgets (meta or taxonomy) configured
                    $has_filter_widgets = false;
                    
                    // Check meta widgets
                    $filter_widgets = jobus_opt( 'company_sidebar_widgets' );
                    if ( isset( $filter_widgets ) && is_array( $filter_widgets ) && ! empty( $filter_widgets ) ) {
                        $has_filter_widgets = true;
                    }
                    
                    // Check taxonomy widgets independently
                    $taxonomy_widgets = jobus_opt( 'company_taxonomy_widgets' );
                    if ( ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
                        foreach ( $taxonomy_widgets as $is_enabled ) {
                            if ( $is_enabled ) {
                                $has_filter_widgets = true;
                                break;
                            }
                        }
                    }
                    ?>
                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-md-center jbs-mb-25">

                        <div class="jbs-d-md-flex jbs-align-items-center">

                            <?php if ( $has_filter_widgets ) : ?>
                            <button type="button" class="jbs-filter-btn jbs-fw-500 tran3s jbs-me-3 jbs-open-modal jbs_filter-transparent jbs_filter_btn-padding jbs-pointer" data-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
                                <?php esc_html_e('Filter', 'jobus'); ?>
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