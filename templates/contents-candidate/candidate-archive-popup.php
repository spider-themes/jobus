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
<section class="jbs jobus-candidate-popup candidates-profile bg-color pt-90 lg-pt-70 pb-160 xl-pb-150 lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-12">
                <div class="jbs-position-relative">

                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-start jbs-align-items-md-center mb-20">
                        <div class="jbs-d-md-flex jbs-justify-content-between jbs-align-items-center">
                            <button type="button" class="filter-btn fw-500 tran3s jbs-me-3 jbs-open-modal" data-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
								<?php esc_html_e( 'Filter', 'jobus' ) ?>
                            </button>

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