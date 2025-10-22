<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();

$user_id       = get_post_field( 'post_author', get_the_ID() );
$employer_mail = get_userdata( $user_id ) ? get_userdata( $user_id )->user_email : '';

$video_bg_img = get_post_meta( get_the_ID(), 'company_video_bg_img', true );
$video_title  = get_post_meta( get_the_ID(), 'company_video_title', true );
$video_url    = get_post_meta( get_the_ID(), 'company_video_url', true );

$meta           = get_post_meta( get_the_ID(), 'jobus_meta_company_options', true );
$website        = $meta['company_website'] ?? [];
$website_url    = $website['url'] ?? '';
$website_text   = $website['title'] ?? '';
$website_target = $website['target'] ?? '_self';
$social_icons   = ! empty( $meta['social_icons'] ) && is_array( $meta['social_icons'] ) ? $meta['social_icons'] : '';

$location = jobus_get_first_taxonomy_name( 'jobus_company_location' );
$cats     = wp_get_post_terms( get_the_ID(), 'jobus_company_cat', array( 'fields' => 'names' ) );
?>

    <section class="company-details pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="jbs-container">
            <div class="jbs-row">

                <div class="jbs-col-xxl-3 jbs-col-xl-4 jbs-order-xl-last">
                    <div class="job-company-info jbs-ms-xl-5 jbs-ms-xxl-0 lg-mb-50">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'full', array( 'class' => 'lazy-img jbs-m-auto logo' ) );
                        }
                        ?>
                        <div class="jbs-text-md jbs-text-dark jbs-text-center jbs-mt-15 jbs-mb-20 lg-mb-10"><?php the_title() ?></div>
                        <?php if ( ! empty( $website_url ) ) : ?>
                            <div class="jbs-text-center">
                                <a href="<?php echo esc_url( $website_url ) ?>" class="website-btn-two tran3s"
                                   target="<?php echo esc_attr( $website_target ) ?>">
                                    <?php echo esc_html( $website_text ) ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="jbs-border-top jbs-mt-35 lg-mt-20 jbs-pt-25">
                            <ul class="job-meta-data jbs-row jbs-style-none">
                                <?php
                                if ( ! empty( $location ) ) { ?>
                                    <li>
                                        <span><?php esc_html_e( 'Location: ', 'jobus' ); ?></span>
                                        <div><?php echo esc_html( $location ) ?></div>
                                    </li>
                                    <?php
                                }

                                if ( $employer_mail ) { ?>
                                    <li>
                                        <span><?php esc_html_e( 'Email: ', 'jobus' ); ?></span>
                                        <div>
                                            <a href="mailto:<?php echo esc_attr( $employer_mail ) ?>">
                                                <?php echo esc_html( $employer_mail ) ?>
                                            </a>
                                        </div>
                                    </li>
                                    <?php
                                }

                                // Retrieve the repeater field configurations from settings options
                                $specifications = jobus_opt( 'company_specifications' );
                                if ( $specifications ) {

                                    foreach ( $specifications as $field ) {

                                        $meta_name = $field['meta_name'] ?? '';
                                        $meta_key  = $field['meta_key'] ?? '';

                                        // Get the stored meta-values
                                        $meta_options = get_post_meta( get_the_ID(), 'jobus_meta_company_options', true );

                                        if ( isset( $meta_options[ $meta_key ] ) ) {
                                            ?>
                                            <li class="jbs-col-12">
                                                <?php
                                                if ( ! empty( $meta_options[ $meta_key ] ) ) {
                                                    echo '<span>' . esc_html( $meta_name ) . ':</span>';
                                                }
                                                if ( ! empty( $meta_options[ $meta_key ] && is_array( $meta_options[ $meta_key ] ) ) ) {
                                                    echo '<div>';
                                                    foreach ( $meta_options[ $meta_key ] as $value ) {
                                                        $trim_value = str_replace( '@space@', ' ', $value );
                                                        echo esc_html( $trim_value );
                                                    }
                                                    echo '</div>';
                                                }
                                                ?>
                                            </li>
                                            <?php
                                        }
                                    }

                                }
                                if ( is_array( $cats ) ) {
                                    ?>
                                    <li>
                                        <span>
                                            <?php
                                            /* translators: 1: Category, 2: Categories */
                                            echo esc_html( sprintf( _n( 'Category:', 'Categories:', '', 'jobus' ), '' ) );
                                            ?>
                                        </span>
                                        <div>
                                            <?php
                                            $terms_list = implode( ', ', $cats );
                                            echo esc_html( $terms_list );
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                                if ( $social_icons ) {
                                    ?>
                                    <li>
                                        <span><?php esc_html_e( 'Social: ', 'jobus' ); ?></span>
                                        <div>
                                            <?php
                                            foreach ( $social_icons as $item ) {
                                                if ( ! empty( $item['url'] ) ) { ?>
                                                    <a href="<?php echo esc_url( $item['url'] ) ?>" class="jbs-me-3">
                                                        <i class="<?php echo esc_attr( $item['icon'] ) ?>"></i>
                                                    </a>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>

                            <a href="<?php echo esc_url( jobus_get_selected_company_count( get_the_ID() ) ); ?>"
                               class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-25">
                                <?php esc_html_e( 'Posted Jobs', 'jobus' ); ?>
                            </a>
                        </div>
                    </div>
                    <!-- /.job-company-info -->
                </div>

                <div class="jbs-col-xxl-9 jbs-col-xl-8 jbs-order-xl-first">
                    <div class="details-post-data jbs-me-xxl-5 jbs-pe-xxl-4">

                        <?php
                        the_content();

                        if ( ! empty( $video_title ) ) { ?>
                            <h3><?php echo esc_html( $video_title ) ?></h3>
                            <?php
                        }
                        if ( $video_url ) {
                            ?>
                            <div class="video-post jbs-d-flex jbs-align-items-center jbs-justify-content-center mb-50"
                                 style="background-image: url(<?php echo esc_url( wp_get_attachment_url( $video_bg_img ) ); ?>)">
                                <a class="fancybox jbs-rounded-circle video-icon tran3s jbs-text-center" data-fancybox href="<?php echo esc_url( $video_url ); ?>">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                            </div>
                            <?php
                        }

                        $testimonials      = get_post_meta( get_the_ID(), 'company_testimonials', true );
                        $testimonial_title = get_post_meta( get_the_ID(), 'company_testimonial_title', true );

                        if ( ! empty( $testimonials ) && ! empty( $testimonial_title ) ) {
                            ?>
                            <div class="jbs-position-relative">
                                <h3><?php echo esc_html( $testimonial_title ); ?></h3>
                                <div class="company-review-slider">
                                    <?php
                                    foreach ( $testimonials as $testimonial ) {
                                        $author_name    = ! empty( $testimonial['author_name'] ) ? $testimonial['author_name'] : '';
                                        $location       = ! empty( $testimonial['location'] ) ? $testimonial['location'] : '';
                                        $review_content = ! empty( $testimonial['review_content'] ) ? $testimonial['review_content'] : '';
                                        $rating         = isset( $testimonial['rating'] ) && is_numeric( $testimonial['rating'] ) ? floatval( $testimonial['rating'] ) : false;
                                        $author_img_id  = ! empty( $testimonial['image'] ) ? $testimonial['image'] : '';
                                        $author_img_url = $author_img_id ? wp_get_attachment_url( $author_img_id ) : get_template_directory_uri() . '/images/assets/img_14.jpg';
                                        ?>
                                        <div class="item">
                                            <div class="feedback-block-four ggggg">
                                                <div class="jbs-d-flex jbs-align-items-center">
                                                    <?php if ( $rating !== false ) : ?>
                                                        <ul class="jbs-style-none jbs-d-flex rating">
                                                            <?php
                                                            $full_stars  = floor( $rating );
                                                            $half_star   = ( $rating - $full_stars ) >= 0.5 ? 1 : 0;
                                                            $empty_stars = 5 - $full_stars - $half_star;
                                                            for ( $i = 0; $i < $full_stars; $i ++ ) {
                                                                echo '<li><a href="#" tabindex="0"><i class="bi bi-star-fill"></i></a></li>';
                                                            }
                                                            if ( $half_star ) {
                                                                echo '<li><a href="#" tabindex="0"><i class="bi bi-star-half"></i></a></li>';
                                                            }
                                                            for ( $i = 0; $i < $empty_stars; $i ++ ) {
                                                                echo '<li><a href="#" tabindex="0"><i class="bi bi-star"></i></a></li>';
                                                            }
                                                            ?>
                                                        </ul>
                                                        <div class="review-score">
                                                            <span class="jbs-fw-500 jbs-text-dark"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
                                                            <?php esc_html_e('out of 5', 'jobus'); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ( $review_content ) : ?>
                                                    <blockquote><?php echo esc_html( $review_content ); ?></blockquote>
                                                <?php endif; ?>
                                                <div class="jbs-d-flex jbs-align-items-center">
                                                    <img src="<?php echo esc_url( $author_img_url ); ?>" alt="<?php echo esc_attr( $author_name ); ?>"
                                                         class="author-img jbs-rounded-circle">
                                                    <div class="jbs-ms-3">
                                                        <?php if ( $author_name ) : ?>
                                                            <div class="name jbs-fw-500 jbs-text-dark"><?php echo esc_html( $author_name ); ?></div>
                                                        <?php endif; ?>
                                                        <?php if ( $location ) : ?>
                                                            <span class="jbs-opacity-50"><?php echo esc_html( $location ); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <nav class="share-option mt-60">
                            <?php jobus_social_share_icons() ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="company-open-position pt-80 lg-pt-60 pb-100 lg-pb-60">
        <div class="jbs-container">

            <div class="jbs-row jbs-justify-content-between jbs-align-items-center">
                <div class="jbs-col-lg-6">
                    <div class="title-two">
                        <h2><?php esc_html_e( 'Open Position', 'jobus' ); ?></h2>
                    </div>
                </div>
                <div class="jbs-col-lg-5">
                    <div class="jbs-d-flex jbs-justify-content-lg-end">
                        <a href="<?php echo esc_url( get_post_type_archive_link( 'jobus_job' ) ) ?>" class="btn-six">
                            <?php esc_html_e( 'Explore More', 'jobus' ); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="jbs-mt-50">
                <?php
                $args = array(
                        'post_type'      => 'jobus_job',
                        'posts_per_page' => - 1,
                        'meta_query'     => array(
                                'relation' => 'AND', // Optional, defaults to "AND
                                array(
                                        'key'     => 'jobus_meta_options',
                                        'value'   => get_the_ID(),
                                        'compare' => 'LIKE',
                                ),
                        ),
                );

                $jobs = new WP_Query( $args );

                while ( $jobs->have_posts() ) : $jobs->the_post();
                    // Get the selected company ID
                    $job_meta   = get_post_meta( get_the_ID(), 'jobus_meta_options', true );
                    $company_id = $job_meta['select_company'] ?? '';
                    ?>
                    <div class="job-list-one style-two jbs-position-relative mb-20">
                        <div class="jbs-row jbs-justify-content-between jbs-align-items-center">
                            <div class="jbs-col-xxl-3 jbs-col-lg-4">
                                <div class="job-title jbs-d-flex jbs-align-items-center">
                                    <a href="<?php the_permalink(); ?>" class="logo">
                                        <?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-m-auto' ] ); ?>
                                    </a>
                                    <a href="<?php the_permalink(); ?>" class="title jbs-fw-500 tran3s">
                                        <?php the_title( '<h3>', '</h3>' ) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6 jbs-ms-auto">
                                <?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'company_open_job_meta_1' ) ) : ?>
                                    <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500">
                                        <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'company_open_job_meta_1' ) ) ?>
                                    </a>
                                <?php endif; ?>
                                <div class="job-date">
                                    <?php echo esc_html( get_the_time( get_option( 'date_format' ) ) ) . esc_html_e( ' by', 'jobus' ) ?>
                                    <a href="<?php echo esc_url( get_permalink( $company_id ) ) ?>">
                                        <?php echo esc_html( get_the_title( $company_id ) ) ?>
                                    </a>
                                </div>
                            </div>
                            <div class="jbs-col-xxl-2 jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6 jbs-ms-auto xs-mt-10">
                                <?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'company_open_job_meta_2' ) ) : ?>
                                    <div class="job-location">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'company_open_job_meta_2' ) ) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="job-category">
                                    <a href="<?php echo esc_url( jobus_get_first_taxonomy_link() ) ?>">
                                        <?php echo esc_html( jobus_get_first_taxonomy_name() ); ?>
                                    </a>
                                </div>
                            </div>
                            <div class="jbs-col-lg-2 jbs-col-md-4">
                                <div class="btn-group jbs-d-flex jbs-align-items-center jbs-justify-content-md-end sm-mt-20">
                                    <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s">
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
        </div>
    </section>
<?php

get_footer();
