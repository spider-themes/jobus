<?php
/**
 * Job Grid Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-grid.php.
 *
 * This template is used to display job listings in a grid format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>
<div class="accordion-box grid-style">
    <div class="jbs-row">
        <?php
        while ( $job_query->have_posts() ) : $job_query->the_post();
            $save_job_status = jobus_get_save_status();
            ?>
            <div class="jbs-col-sm-6 jbs-mb-30">
                <div class="job-list-two style-two jbs-position-relative">
                    <div class="jbs-logo-area">
                    <?php
                    if ( has_post_thumbnail() ) { ?>
                        <a href="<?php the_permalink(); ?>" class="logo">
                            <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-m-auto' ] ); ?>
                        </a>
                        <?php
                    } else {
                        // Show dummy logo when no company logo is available
                        $dummy_logo_url = jobus_get_default_company_logo();
                        ?>
                        <a href="<?php the_permalink(); ?>" class="logo">
                            <img src="<?php echo esc_url( $dummy_logo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="lazy-img jbs-m-auto">
                        </a>
                        <?php
                    }
                    if ( is_array( $save_job_status ) && isset( $save_job_status['post_id'] ) ) {
                        jobus_render_post_save_button( [
                            'post_id'      => $save_job_status['post_id'],
                            'post_type'    => 'jobus_job',
                            'meta_key'     => 'jobus_saved_jobs',
                            'is_saved'     => $save_job_status['is_saved'],
                            'button_title' => ! empty( $save_job_status['is_saved'] ) ? esc_html__( 'Saved Job', 'jobus' ) : esc_html__( 'Save Job', 'jobus' ),
                            'class'        => 'save-btn jbs-text-center jbs-rounded-circle tran3s jobus-saved-post'
                        ] );
                    }
                    ?>
                    </div>
                    <?php
                    if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) { ?>
                        <div>
                            <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500 ">
                                <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) ?>
                            </a>
                        </div>
                        <?php
                    }else{  
                        ?>
                        <div>
                            <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500">Full Time</a>
                        </div>
                        <?php
                    }
                    ?>
                   <div class="jbs-job-title">
                     <a href="<?php the_permalink(); ?>" class="title jbs-fw-500 tran3s">
                        <?php the_title( '<h3>', '</h3>' ) ?>
                    </a>
                    <?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) { ?>
                        <div class="job-salary">
                            <span class="jbs-fw-500 jbs-text-dark">
                                <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) ?>
                            </span>
                        </div>
                    <?php } else{ ?>
                        <div class="job-salary">
                            <span class="jbs-fw-500 jbs-text-dark">
                                <?php echo esc_html( 'Negotiable' ) ?>
                            </span>
                        </div>
                    <?php } ?>
                   </div>
                    <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-between jbs-mt-auto">
                        <div class="job-location">
                            <a href="<?php echo esc_url( jobus_get_first_taxonomy_link( 'jobus_job_location' ) ) ?>">
                                <?php echo esc_html( jobus_get_first_taxonomy_name( 'jobus_job_location' ) ); ?>
                            </a>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s">
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