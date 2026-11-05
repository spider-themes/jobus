<?php
/**
 * Candidate Archive - Template for displaying candidate archive
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/candidate-archive-2.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="jbs jbs-candidate-popup jbs-bg-color jbs-candidates-profile">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="jbs-position-relative" data-jbs-filter-results="true">

                    <?php
                    // Show Filter button when any sidebar filter surface is available
                    // (meta widgets, taxonomy widgets, or keyword search) — matches classic/topbar.
                    $has_filter_widgets = false;
                    $filter_widgets     = jobus_opt( 'candidate_sidebar_widgets' );
                    $taxonomy_widgets   = jobus_opt( 'candidate_taxonomy_widgets' );
                    $show_search_form   = jobus_opt( 'candidate_show_search_form', true );

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
                    <div class="jbs-upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-md-center jbs-mb-20">
                        <div class="jbs-d-md-flex jbs-justify-content-between jbs-align-items-center">
                            <?php if ( $has_filter_widgets ) : ?>
                            <button type="button" class="jbs-filter-btn jbs-fw-500 jbs-tran3s jbs-me-3 jbs-open-modal jbs_filter-transparent jbs_filter_btn-padding jbs-pointer" data-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
								<?php esc_html_e( 'Filter', 'jobus' ) ?>
                            </button>
                            <?php endif; ?>

							<?php
                            // Display the total number of candidates found
							include (__DIR__ . '/../loop/result-count.php');
                            ?>

                        </div>

						<?php
						// Display the sort by dropdown
						include __DIR__ . '/../loop/sortby.php';
                        ?>

                    </div>

					<?php
					if ( $current_view == 'grid' ) {
						include( 'contents/content-grid.php' );
					} elseif ( $current_view == 'list' ) {
						include( 'contents/content-list.php' );
					}

					// Pagination
					include (__DIR__ . '/../loop/pagination.php');
					?>
                </div>
            </div>
        </div>
    </div>
</section>