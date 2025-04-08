<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$job_archive_layout = $jobus_job_archive_layout ?? jobus_opt( 'job_archive_layout' );

// Get the base URL for the archive page
if ( $job_archive_layout ) {
    $archive_url = get_post_type_archive_link( 'jobus_job' );
} else {
    $archive_url = get_the_permalink(); //Created a Page link
}

// Build the URL for list and grid views
$list_view_url = esc_url( add_query_arg( 'view', 'list', $archive_url ) );
$grid_view_url = esc_url( add_query_arg( 'view', 'grid', $archive_url ) );

// Check if the view parameter is set in the URL
$current_view = ! empty( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'list';
?>
<section class="job-listing-three pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part( 'contents-job/sidebar-classic-filters' ); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e( 'All', 'jobus' ); ?>
                            <span class="text-dark"><?php echo esc_html( jobus_posts_count( 'jobus_job' ) ); ?></span>
                            <?php
                            /* translators: 1: job found, 2: jobs found */
                            echo esc_html( sprintf( _n( 'job found', 'jobs found', jobus_posts_count( 'jobus_job' ), 'jobus' ), jobus_posts_count( 'jobus_job' ) ) );
                            ?>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="short-filter d-flex align-items-center">
                                <div class="text-dark fw-500 me-2"><?php esc_html_e( 'Short By:', 'jobus' ); ?></div>
                                <?php
                                $order = ! empty( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';
                                $order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
                                $default = ! empty( $order_by ) ? 'selected' : '';

                                $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                                $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                                $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                                $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                                ?>
                                <form action="" method="get">
                                    <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                        <option <?php echo esc_attr( $default ); ?>><?php esc_html_e( 'Default', 'jobus' ); ?></option>
                                        <option value="orderby=date&order=desc" <?php echo esc_attr( $selected_new_to_old ); ?>><?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?></option>
                                        <option value="orderby=date&order=asc" <?php echo esc_attr( $selected_old_to_new ); ?>><?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?></option>
                                        <option value="orderby=title&order=asc" <?php echo esc_attr( $selected_title_asc ); ?>><?php esc_html_e( 'Title Ascending ', 'jobus' ); ?></option>
                                        <option value="orderby=title&order=desc" <?php echo esc_attr( $selected_title_desc ); ?>><?php esc_html_e( 'Title Descending', 'jobus' ); ?></option>
                                    </select>
                                </form>
                            </div>

                            <a href="<?php echo esc_url( $list_view_url ); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn<?php echo esc_attr( $current_view == 'grid' ) ? ' active' : ''; ?>" title="<?php esc_attr_e( 'Active List', 'jobus' ); ?>">
                                <i class="bi bi-list"></i>
                            </a>

                            <a href="<?php echo esc_url( $grid_view_url ); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn<?php echo esc_attr( $current_view == 'list' ) ? ' active' : ''; ?>" title="<?php esc_attr_e( 'Active Grid', 'jobus' ); ?>">
                                <i class="bi bi-grid"></i>
                            </a>
                        </div>
                    </div>

                    <?php
                    if ( $current_view == 'list' ) :
                        ?>
                        <div class="accordion-box list-style">
                            <?php
                            while ( $job_post->have_posts() ) :
                                $job_post->the_post();
                                ?>
                                <div class="job-list-one style-two position-relative border-style mb-20">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-md-5">
                                            <div class="job-title d-flex align-items-center">
                                                <?php 
                                                if ( has_post_thumbnail() ) : 
                                                    ?>
                                                    <a href="<?php the_permalink(); ?>" class="logo">
                                                        <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img m-auto' ] ); ?>
                                                    </a>
                                                    <?php 
                                                endif; 
                                                ?>
                                                <div class="split-box1">
                                                    <?php 
                                                    if ( jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_1' ) ) : 
                                                        ?>
                                                        <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                            <?php echo jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_1' ); ?>
                                                        </a>
                                                        <?php 
                                                    endif; 
                                                    ?>
                                                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                                                        <?php the_title(); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <!--job archive 1 location-->
                                            <?php
                                            $locations = get_the_terms( get_the_ID(), 'jobus_job_location' );
                                            if ( ! empty( $locations ) ) { ?>
                                                <div class="job-location">
                                                    <?php
                                                    foreach ( $locations as $location ) {
                                                        echo '<a href="'.esc_url( get_the_permalink() ).'">'. esc_html( $location->name ) .'</a>';
                                                    }
                                                    ?>
                                                </div>
                                                <?php
	                                        }
	                                        ?>
                                            <div class="job-salary">
                                                <?php 
                                                if ( jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_2' ) ) : 
                                                    ?>
                                                    <span class="fw-500 text-dark">
                                                        <?php echo jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_2' ); ?>
                                                    </span>
                                                    <?php 
                                                endif;

                                                if ( jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_3' ) ) : 
                                                    ?>
                                                    <span class="expertise">. <?php echo jobus_get_meta_attributes( 'jobus_meta_options','job_archive_meta_3' ); ?> 
                                                    </span>
                                                    <?php 
                                                endif; 
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e( 'APPLY', 'jobus' ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    elseif ( $current_view == 'grid' ) :
                        ?>
                        <div class="accordion-box grid-style">
                            <div class="row">
                                <?php
                                while ( $job_post->have_posts() ) :
                                    $job_post->the_post();
                                    ?>
                                    <div class="col-sm-6 mb-30">
                                        <div class="job-list-two style-two position-relative">
                                            <?php 
                                            if ( has_post_thumbnail() ) : 
                                                ?>
                                                <a href="<?php the_permalink(); ?>" class="logo">
                                                    <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img m-auto' ] ); ?>
                                                </a>
                                                <?php 
                                            endif;
                                            
                                            if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) : 
                                                ?>
                                                <div>
                                                    <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                        <?php echo jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ); ?>
                                                    </a>
                                                </div>
                                                <?php 
                                            endif; 
                                            ?>

                                            <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                                                <?php the_title( '<h3>', '</h3>' ); ?>
                                            </a>

                                            <?php 
                                            if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) : 
                                                ?>
                                                <div class="job-salary">
                                                    <span class="fw-500 text-dark">
                                                        <?php echo jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ); ?>
                                                    </span>
                                                </div>
                                                <?php 
                                            endif; 
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <?php
                                                $locations = get_the_terms( get_the_ID(), 'jobus_job_location' );

                                                if ( ! empty( $locations ) ) :
                                                    ?>
                                                    <div class="job-location">
		                                                <?php
		                                                foreach ( $locations as $location ) :
                                                            ?>
                                                            <a href="<?php the_permalink(); ?>"><?php echo esc_html( $location->name ); ?></a>
			                                                <?php
		                                                endforeach;
		                                                ?>
                                                    </div>
	                                                <?php
	                                            endif;
	                                            ?>
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e( 'APPLY', 'jobus' ); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php
                    endif;
                    ?>

                    <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">
                        <?php 
                        jobus_showing_post_result_count( $job_post );
                        jobus_pagination( $job_post ); 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>