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
<section class="jobus-candidate-popup candidates-profile bg-color pt-90 lg-pt-70 pb-160 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="position-relative">

                    <div class="upper-filter d-flex justify-content-between align-items-start align-items-md-center mb-20">
                        <div class="d-md-flex justify-content-between align-items-center">
                            <button type="button" class="filter-btn fw-500 tran3s me-3" data-bs-toggle="modal" data-bs-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
								<?php esc_html_e( 'Filter', 'jobus' ) ?>
                            </button>

							<?php include( 'loop/result-count.php' ) ?>

                        </div>

						<?php include( 'loop/sortby.php' ); ?>

                    </div>

					<?php
					if ( $current_view == 'grid' ) {
						include( 'contents/content-grid.php' );
					} elseif ( $current_view == 'list' ) {
						include( 'contents/content-list-5-col.php' );
					}

					// Pagination
					include (__DIR__ . '/../loop/pagination.php');
					?>
                </div>
            </div>
        </div>
    </div>
</section>