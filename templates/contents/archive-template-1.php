<section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobly_get_template_part('contents/sidebar-search-filter'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">

                    <?php jobly_get_template_part('contents/post-filter'); ?>

                    <div class="accordion-box list-style show">

                        <?php
                        if ( $job_post->have_posts() ) {

                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();

                                jobly_get_template_part('contents/content');

                            }
                            wp_reset_postdata();

                        } else {
                            echo 'No jobs found.';
                        }
                        ?>

                    </div>


                    <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php include "result-count.php" ?>

                        <ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
                            <?php
                            $big = 999999999; // need an unlikely integer
                            echo paginate_links( array(
                                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                'format' => '?paged=%#%',
                                'current' => max( 1, get_query_var('paged') ),
                                'total' =>  $job_post->max_num_pages,
                                'prev_text' => '<i class="fas fa-chevron-left"></i>',
                                'next_text' => '<i class="fas fa-chevron-right"></i>',
                            ));
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- /.job-post-item-wrapper -->
            </div>
            <!-- /.col- -->
        </div>
    </div>
</section>