<?php
?>
<section class="company-profiles pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobly_get_template_part('contents-company/sidebar-search-filter'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <!----------------- Post Filter ---------------------->
                    <?php jobly_get_template_part('contents-company/post-filter'); ?>

                    <!-- Post-Grid View -->
                    <div class="accordion-box grid-style show">
                        <div class="row">
                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();

                                jobly_get_template_part('contents-company/content-grid');

                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>


                    <!-- Post-List View -->
                    <div class="accordion-box list-style">
                        <?php
                        while ( $company_query->have_posts() ) : $company_query->the_post();

                            jobly_get_template_part('contents-company/content-list');

                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>

                    <div class="pt-50 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobly_showing_post_result_count('company', jobly_opt('company_posts_per_page')) ?>

                        <ul class="jobly_pagination">
                            <?php jobly_pagination($company_query); ?>
                        </ul>

                    </div>

                </div>
            </div>


        </div>
    </div>
</section>
