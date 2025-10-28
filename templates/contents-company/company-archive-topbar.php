<?php
/**
 * Company Archive Topbar - Template for displaying company archive with topbar filters
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-company/company-archive-topbar.php.
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
<section class="jbs jobus-company-topbar jobus-company-classic company-profiles jbs-pt-110 jbs-lg-pt-80 jbs-pb-150 jbs-xl-pb-150 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">

            <?php jobus_get_template_part('contents-company/sidebar-topbar-filters'); ?>

            <div class="jbs-col-12">
                <div class="company-profile-item-wrapper">
                    <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-mb-25 jbs-mt-70 jbs-lg-mt-40">

	                    <?php
	                    // Display the total number of companies found
	                    include __DIR__ . '/../loop/result-count.php';

	                    // Display the sort by dropdown
	                    include __DIR__ . '/../loop/sortby.php';
	                    ?>

                    </div>

                    <?php
                    // Check if the current view is set to 'list' or 'grid' contents
                    if ( $current_view == 'list' ) {
	                    include ( 'contents/content-list.php' );
                    } elseif ( $current_view == 'grid' ) {
	                    include ( 'contents/content-grid.php' );
                    }

                    // Pagination
                    include (__DIR__ . '/../loop/pagination.php');
                    ?>
                </div>

            </div>

        </div>
    </div>
</section>
